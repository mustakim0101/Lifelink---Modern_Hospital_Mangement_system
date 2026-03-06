<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $validStatuses = ['Pending', 'Approved', 'Rejected'];

        $query = JobApplication::query()
            ->with(['user:id,full_name,name,email', 'appliedRole:id,role_name', 'department:id,dept_name', 'reviewer:id,full_name,name,email'])
            ->orderByDesc('applied_at');

        if ($status && in_array($status, $validStatuses, true)) {
            $query->where('status', $status);
        }

        $applications = $query->limit(200)->get()->map(
            fn (JobApplication $application) => $this->applicationPayload($application)
        );

        return response()->json([
            'applications' => $applications,
            'filter_status' => $status ?: null,
        ]);
    }

    public function approve(Request $request, JobApplication $application): JsonResponse
    {
        $reviewer = auth('api')->user();
        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($application->status !== 'Pending') {
            return response()->json([
                'message' => 'Only pending applications can be approved.',
                'application' => $this->applicationPayload($application->loadMissing(['user', 'appliedRole', 'department', 'reviewer'])),
            ], 409);
        }

        DB::transaction(function () use ($application, $reviewer, $validated): void {
            $application->update([
                'status' => 'Approved',
                'reviewed_by_user_id' => $reviewer->id,
                'reviewed_at' => now(),
                'review_notes' => $validated['review_notes'] ?? null,
            ]);

            $this->assignApprovedRole($application->user_id, $application->applied_role_id, $reviewer->id);
            $this->removeApplicantRole($application->user_id);
        });

        $application->refresh()->load(['user', 'appliedRole', 'department', 'reviewer']);

        return response()->json([
            'message' => 'Application approved and role assigned.',
            'application' => $this->applicationPayload($application),
        ]);
    }

    public function reject(Request $request, JobApplication $application): JsonResponse
    {
        $reviewer = auth('api')->user();
        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($application->status !== 'Pending') {
            return response()->json([
                'message' => 'Only pending applications can be rejected.',
                'application' => $this->applicationPayload($application->loadMissing(['user', 'appliedRole', 'department', 'reviewer'])),
            ], 409);
        }

        $application->update([
            'status' => 'Rejected',
            'reviewed_by_user_id' => $reviewer->id,
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'] ?? null,
        ]);

        $application->refresh()->load(['user', 'appliedRole', 'department', 'reviewer']);

        return response()->json([
            'message' => 'Application rejected.',
            'application' => $this->applicationPayload($application),
        ]);
    }

    private function assignApprovedRole(int $userId, int $roleId, int $assignedByUserId): void
    {
        $exists = DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $roleId)
            ->exists();

        if ($exists) {
            return;
        }

        DB::table('user_roles')->insert([
            'user_id' => $userId,
            'role_id' => $roleId,
            'assigned_at' => now(),
            'assigned_by_user_id' => $assignedByUserId,
        ]);
    }

    private function removeApplicantRole(int $userId): void
    {
        $applicantRoleId = Role::query()
            ->where('role_name', 'Applicant')
            ->value('id');

        if (! $applicantRoleId) {
            return;
        }

        DB::table('user_roles')
            ->where('user_id', $userId)
            ->where('role_id', $applicantRoleId)
            ->delete();
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
            'user' => [
                'id' => $application->user?->id,
                'email' => $application->user?->email,
                'full_name' => $application->user?->full_name ?? $application->user?->name,
            ],
            'reviewed_by_user_id' => $application->reviewed_by_user_id,
            'reviewed_by' => $application->reviewer?->full_name ?? $application->reviewer?->name,
            'reviewed_at' => optional($application->reviewed_at)->toISOString(),
            'review_notes' => $application->review_notes,
        ];
    }
}

