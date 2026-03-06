<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\JobApplication;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobApplicationController extends Controller
{
    public function submit(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $validated = $request->validate([
            'appliedRole' => ['nullable', 'string', 'max:100'],
            'applied_role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'applied_department_id' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $role = $this->resolveRole($validated);
        $department = $this->resolveDepartment($validated);

        $existingPending = JobApplication::query()
            ->where('user_id', $user->id)
            ->where('status', 'Pending')
            ->exists();

        if ($existingPending) {
            return response()->json([
                'message' => 'You already have a pending application.',
            ], 409);
        }

        $application = JobApplication::query()->create([
            'user_id' => $user->id,
            'applied_role_id' => $role->id,
            'applied_department_id' => $department?->id,
            'status' => 'Pending',
            'applied_at' => now(),
        ]);

        $this->assignApplicantRole($user->id);

        return response()->json([
            'message' => 'Application submitted',
            'application' => $this->applicationPayload($application->fresh(['appliedRole', 'department'])),
        ], 201);
    }

    public function myApplications(): JsonResponse
    {
        $user = auth('api')->user();

        $applications = JobApplication::query()
            ->with(['appliedRole', 'department'])
            ->where('user_id', $user->id)
            ->orderByDesc('applied_at')
            ->get()
            ->map(fn (JobApplication $application) => $this->applicationPayload($application));

        return response()->json([
            'applications' => $applications,
        ]);
    }

    public function myLatest(): JsonResponse
    {
        $user = auth('api')->user();

        $application = JobApplication::query()
            ->with(['appliedRole', 'department'])
            ->where('user_id', $user->id)
            ->orderByDesc('applied_at')
            ->first();

        return response()->json([
            'latestApplication' => $application ? $this->applicationPayload($application) : null,
        ]);
    }

    private function resolveRole(array $validated): Role
    {
        if (isset($validated['applied_role_id'])) {
            return Role::query()->findOrFail($validated['applied_role_id']);
        }

        if (isset($validated['appliedRole'])) {
            return Role::query()->firstOrCreate(
                ['role_name' => $validated['appliedRole']],
                ['description' => $validated['appliedRole'].' role']
            );
        }

        return Role::query()->firstOrCreate(
            ['role_name' => 'ITWorker'],
            ['description' => 'ITWorker role']
        );
    }

    private function resolveDepartment(array $validated): ?Department
    {
        $departmentId = $validated['departmentId'] ?? $validated['applied_department_id'] ?? null;

        if (! $departmentId) {
            return null;
        }

        return Department::query()->findOrFail($departmentId);
    }

    private function assignApplicantRole(int $userId): void
    {
        $applicantRole = Role::query()->firstOrCreate(
            ['role_name' => 'Applicant'],
            ['description' => 'Applicant role']
        );

        $exists = \DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $applicantRole->id)
            ->exists();

        if ($exists) {
            return;
        }

        \DB::table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => $applicantRole->id,
            'assigned_at' => now(),
            'assigned_by_user_id' => null,
        ]);
    }

    private function applicationPayload(JobApplication $application): array
    {
        return [
            'id' => $application->id,
            'status' => $application->status,
            'applied_at' => optional($application->applied_at)->toISOString(),
            'applied_role' => $application->appliedRole?->role_name,
            'applied_department_id' => $application->applied_department_id,
            'applied_department' => $application->department?->dept_name,
            'reviewed_by_user_id' => $application->reviewed_by_user_id,
            'reviewed_at' => optional($application->reviewed_at)->toISOString(),
            'review_notes' => $application->review_notes,
        ];
    }
}
