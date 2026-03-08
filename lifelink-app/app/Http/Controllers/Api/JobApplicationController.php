<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Sql\JobApplicationSqlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class JobApplicationController extends Controller
{
    public function __construct(private readonly JobApplicationSqlService $sqlService)
    {
    }

    public function submit(Request $request): JsonResponse
    {
        $user = auth('api')->user();

        $validated = $request->validate([
            'appliedRole' => ['nullable', 'string', 'max:100'],
            'applied_role_id' => ['nullable', 'integer', 'exists:roles,id'],
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'applied_department_id' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $roleId = $this->sqlService->resolveRoleId($validated);
        $departmentId = $this->sqlService->resolveDepartmentId($validated);
        $existingPending = $this->sqlService->hasPendingApplication((int) $user->id);

        if ($existingPending) {
            return response()->json([
                'message' => 'You already have a pending application.',
            ], 409);
        }

        $applicationId = $this->sqlService->createApplication((int) $user->id, $roleId, $departmentId);
        $this->sqlService->assignApplicantRole((int) $user->id);
        $application = $this->sqlService->getLatestApplication((int) $user->id);

        return response()->json([
            'message' => 'Application submitted',
            'application' => $this->applicationPayload($applicationId, $application),
        ], 201);
    }

    public function myApplications(): JsonResponse
    {
        $user = auth('api')->user();

        $applications = collect($this->sqlService->getMyApplications((int) $user->id))
            ->map(fn ($application) => $this->applicationPayload((int) $application->id, $application));

        return response()->json([
            'applications' => $applications,
        ]);
    }

    public function myLatest(): JsonResponse
    {
        $user = auth('api')->user();

        $application = $this->sqlService->getLatestApplication((int) $user->id);

        return response()->json([
            'latestApplication' => $application ? $this->applicationPayload((int) $application->id, $application) : null,
        ]);
    }

    private function applicationPayload(int $applicationId, object $application): array
    {
        return [
            'id' => $applicationId,
            'status' => $application->status,
            'applied_at' => $this->toIso($application->applied_at ?? null),
            'applied_role' => $application->applied_role,
            'applied_department_id' => $application->applied_department_id,
            'applied_department' => $application->applied_department,
            'reviewed_by_user_id' => $application->reviewed_by_user_id,
            'reviewed_at' => $this->toIso($application->reviewed_at ?? null),
            'review_notes' => $application->review_notes,
        ];
    }

    private function toIso(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        return Carbon::parse((string) $value)->toISOString();
    }
}
