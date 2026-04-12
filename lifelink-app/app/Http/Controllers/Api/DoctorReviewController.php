<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\DoctorReview;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorReviewController extends Controller
{
    private const REVIEW_ELIGIBLE_APPOINTMENT_STATUSES = ['Approved', 'Booked', 'Completed', 'NoShow', 'Cancelled'];

    public function index(Request $request, Doctor $doctor): JsonResponse
    {
        if (! $doctor->is_active) {
            return response()->json([
                'message' => 'Doctor profile is not active.',
            ], 404);
        }

        $validated = $request->validate([
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'perPage' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $departmentId = isset($validated['departmentId']) ? (int) $validated['departmentId'] : null;
        $perPage = (int) ($validated['perPage'] ?? 10);

        $summaryQuery = DoctorReview::query()
            ->where('doctor_user_id', $doctor->doctor_id)
            ->where('is_visible', true);

        if ($departmentId !== null) {
            $summaryQuery->where('department_id', $departmentId);
        }

        $reviewCount = (int) (clone $summaryQuery)->count();
        $averageRating = $reviewCount > 0
            ? round((float) ((clone $summaryQuery)->avg('rating') ?? 0), 2)
            : null;

        $reviews = DoctorReview::query()
            ->with([
                'patient.user:id,full_name,name',
                'department:id,dept_name',
            ])
            ->where('doctor_user_id', $doctor->doctor_id)
            ->where('is_visible', true)
            ->when($departmentId !== null, fn ($query) => $query->where('department_id', $departmentId))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        return response()->json([
            'doctor_user_id' => (int) $doctor->doctor_id,
            'average_rating' => $averageRating,
            'review_count' => $reviewCount,
            'reviews' => $reviews->getCollection()->map(fn (DoctorReview $review) => [
                'id' => (int) $review->id,
                'doctor_user_id' => (int) $review->doctor_user_id,
                'department_id' => (int) $review->department_id,
                'department' => $review->department?->dept_name,
                'appointment_id' => $review->appointment_id !== null ? (int) $review->appointment_id : null,
                'rating' => (int) $review->rating,
                'review_text' => $review->review_text,
                'patient_display_name' => $this->maskPatientName($review->patient?->user?->full_name ?? $review->patient?->user?->name),
                'created_at' => optional($review->created_at)->toISOString(),
            ])->values(),
            'pagination' => [
                'current_page' => $reviews->currentPage(),
                'per_page' => $reviews->perPage(),
                'last_page' => $reviews->lastPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    public function store(Request $request, Doctor $doctor): JsonResponse
    {
        if (! $doctor->is_active) {
            return response()->json([
                'message' => 'Doctor profile is not active.',
            ], 422);
        }

        $patient = $this->resolvePatientProfile();

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'reviewText' => ['nullable', 'string', 'max:1000'],
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
            'appointmentId' => ['nullable', 'integer', 'exists:appointments,id'],
        ]);

        if ((int) $doctor->department_id !== (int) $validated['departmentId']) {
            return response()->json([
                'message' => 'Doctor is not active in the selected department.',
            ], 422);
        }

        $eligibleAppointmentQuery = Appointment::query()
            ->where('patient_id', $patient->patient_id)
            ->where('doctor_user_id', $doctor->doctor_id)
            ->where('department_id', (int) $validated['departmentId'])
            ->whereIn('status', self::REVIEW_ELIGIBLE_APPOINTMENT_STATUSES);

        if (! (clone $eligibleAppointmentQuery)->exists()) {
            return response()->json([
                'message' => 'You can only review doctors after an approved or completed appointment relationship.',
            ], 403);
        }

        $appointmentId = null;
        if (! empty($validated['appointmentId'])) {
            $appointment = (clone $eligibleAppointmentQuery)
                ->where('id', (int) $validated['appointmentId'])
                ->first();

            if (! $appointment) {
                return response()->json([
                    'message' => 'Appointment is not eligible for this review submission.',
                ], 422);
            }

            $alreadyReviewedAppointment = DoctorReview::query()
                ->where('doctor_user_id', $doctor->doctor_id)
                ->where('patient_id', $patient->patient_id)
                ->where('appointment_id', $appointment->id)
                ->exists();

            if ($alreadyReviewedAppointment) {
                return response()->json([
                    'message' => 'This appointment has already been reviewed.',
                ], 409);
            }

            $appointmentId = (int) $appointment->id;
        } else {
            $hasRecentReview = DoctorReview::query()
                ->where('doctor_user_id', $doctor->doctor_id)
                ->where('patient_id', $patient->patient_id)
                ->where('department_id', (int) $validated['departmentId'])
                ->where('created_at', '>=', now()->subDays(7))
                ->exists();

            if ($hasRecentReview) {
                return response()->json([
                    'message' => 'Please wait before posting another review for the same doctor.',
                ], 409);
            }
        }

        $review = DoctorReview::query()->create([
            'doctor_user_id' => (int) $doctor->doctor_id,
            'patient_id' => (int) $patient->patient_id,
            'department_id' => (int) $validated['departmentId'],
            'appointment_id' => $appointmentId,
            'rating' => (int) $validated['rating'],
            'review_text' => isset($validated['reviewText']) && trim((string) $validated['reviewText']) !== ''
                ? trim((string) $validated['reviewText'])
                : null,
            'is_visible' => true,
        ]);

        $summaryQuery = DoctorReview::query()
            ->where('doctor_user_id', $doctor->doctor_id)
            ->where('is_visible', true);
        $reviewCount = (int) (clone $summaryQuery)->count();
        $averageRating = $reviewCount > 0
            ? round((float) ((clone $summaryQuery)->avg('rating') ?? 0), 2)
            : null;

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => [
                'id' => (int) $review->id,
                'doctor_user_id' => (int) $review->doctor_user_id,
                'patient_id' => (int) $review->patient_id,
                'department_id' => (int) $review->department_id,
                'appointment_id' => $review->appointment_id !== null ? (int) $review->appointment_id : null,
                'rating' => (int) $review->rating,
                'review_text' => $review->review_text,
                'is_visible' => (bool) $review->is_visible,
                'created_at' => optional($review->created_at)->toISOString(),
            ],
            'doctor_review_summary' => [
                'average_rating' => $averageRating,
                'review_count' => $reviewCount,
            ],
        ], 201);
    }

    private function resolvePatientProfile(): Patient
    {
        $user = auth('api')->user();

        $patient = Patient::query()->firstOrCreate(
            ['patient_id' => $user->id],
            ['is_active' => true]
        );

        abort_unless($patient->is_active, 403, 'Patient profile is inactive.');

        return $patient;
    }

    private function maskPatientName(?string $name): string
    {
        $name = trim((string) $name);
        if ($name === '') {
            return 'Patient';
        }

        $chunks = preg_split('/\s+/', $name) ?: [];
        $chunks = array_values(array_filter($chunks, fn ($chunk) => $chunk !== ''));
        if (empty($chunks)) {
            return 'Patient';
        }

        $masked = array_map(function (string $chunk) {
            $first = mb_substr($chunk, 0, 1);
            $length = mb_strlen($chunk);

            return $first.str_repeat('*', max(2, $length - 1));
        }, array_slice($chunks, 0, 2));

        return implode(' ', $masked);
    }
}
