<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Services\Sql\ApplicationReviewSqlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ApplicationReviewController extends Controller
{
    public function __construct(private readonly ApplicationReviewSqlService $sqlService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status');
        $applications = collect($this->sqlService->listApplications($status))
            ->map(
                fn ($application) => $this->applicationPayload($application)
        );

        return response()->json([
            'applications' => $applications,
            'filter_status' => $status ?: null,
        ]);
    }

    public function approve(Request $request, int $application): JsonResponse
    {
        $reviewer = auth('api')->user();
        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $updated = $this->sqlService->approve($application, (int) $reviewer->id, $validated['review_notes'] ?? null);

            return response()->json([
                'message' => 'Application approved and role assigned.',
                'application' => $this->applicationPayload($updated),
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'application' => ($existing = $this->sqlService->getApplicationById($application))
                    ? $this->applicationPayload($existing)
                    : null,
            ], $exception->getStatusCode());
        }
    }

    public function reject(Request $request, int $application): JsonResponse
    {
        $reviewer = auth('api')->user();
        $validated = $request->validate([
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        try {
            $updated = $this->sqlService->reject($application, (int) $reviewer->id, $validated['review_notes'] ?? null);

            return response()->json([
                'message' => 'Application rejected.',
                'application' => $this->applicationPayload($updated),
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
                'application' => ($existing = $this->sqlService->getApplicationById($application))
                    ? $this->applicationPayload($existing)
                    : null,
            ], $exception->getStatusCode());
        }
    }

    private function applicationPayload(object $application): array
    {
        return [
            'id' => $application->id,
            'status' => $application->status,
            'applied_at' => $this->toIso($application->applied_at ?? null),
            'applied_role' => $application->applied_role,
            'applied_department_id' => $application->applied_department_id,
            'applied_department' => $application->applied_department,
            'user' => [
                'id' => $application->user_id ?? null,
                'email' => $application->user_email ?? null,
                'full_name' => $application->user_full_name ?? $application->user_name ?? null,
            ],
            'reviewed_by_user_id' => $application->reviewed_by_user_id,
            'reviewed_by' => $application->reviewed_by_full_name ?? $application->reviewed_by_name ?? null,
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
