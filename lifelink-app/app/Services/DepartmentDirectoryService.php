<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorReview;
use Carbon\Carbon;

class DepartmentDirectoryService
{
    private const PATIENTS_SEEN_STATUSES = ['Approved', 'Booked', 'Completed', 'NoShow', 'Cancelled'];

    public function __construct(private readonly AppointmentCapacityService $capacityService)
    {
    }

    public function findPublicDepartmentBySlug(string $slug): ?Department
    {
        return Department::query()
            ->publicCatalog()
            ->where('slug', $slug)
            ->first();
    }

    public function departmentCatalog(): array
    {
        $departments = Department::query()
            ->publicCatalog()
            ->withCount([
                'doctors as doctor_count' => fn ($query) => $query->where('is_active', true),
            ])
            ->get();

        $departmentIds = $departments->pluck('id')->values()->all();
        $ratingByDepartment = $this->departmentRatingSummary($departmentIds);

        return $departments->map(function (Department $department) use ($ratingByDepartment) {
            return [
                'id' => (int) $department->id,
                'name' => $department->dept_name,
                'slug' => $department->slug,
                'short_description' => $department->short_description,
                'banner_title' => $department->banner_title,
                'organ_coverage_summary' => $department->organ_coverage,
                'doctor_count' => (int) ($department->doctor_count ?? 0),
                'average_rating_summary' => $ratingByDepartment[(int) $department->id] ?? [
                    'average_rating' => null,
                    'review_count' => 0,
                ],
            ];
        })->all();
    }

    public function departmentDetail(string $slug): ?array
    {
        $department = $this->findPublicDepartmentBySlug($slug);
        if (! $department) {
            return null;
        }

        $doctors = Doctor::query()
            ->with(['user:id,full_name,name,email'])
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->orderBy('doctor_id')
            ->get();

        $doctorIds = $doctors->pluck('doctor_id')->map(fn ($id) => (int) $id)->all();
        $ratingsByDoctor = $this->doctorRatingSummary($doctorIds);
        $patientsSeenByDoctor = $this->patientsSeenSummary($doctorIds);
        $departmentRating = $this->departmentRatingSummary([(int) $department->id]);

        return [
            'department' => [
                'id' => (int) $department->id,
                'name' => $department->dept_name,
                'slug' => $department->slug,
                'short_description' => $department->short_description,
                'banner_title' => $department->banner_title,
                'banner_description' => $department->banner_description,
                'organ_coverage' => $department->organ_coverage,
                'services' => $department->services,
                'icon_key' => $department->icon_key,
                'sort_order' => $department->sort_order,
                'average_rating_summary' => $departmentRating[(int) $department->id] ?? [
                    'average_rating' => null,
                    'review_count' => 0,
                ],
            ],
            'doctors' => $doctors->map(function (Doctor $doctor) use ($department, $ratingsByDoctor, $patientsSeenByDoctor) {
                $weeklySummary = $this->capacityService->doctorWeeklySummary((int) $doctor->doctor_id, (int) $department->id);
                $availabilityPreview = $this->doctorAvailabilityPreview(
                    (int) $doctor->doctor_id,
                    (int) $department->id,
                    now()->startOfDay(),
                    7
                );
                $nextAvailableDay = collect($availabilityPreview)->firstWhere('is_available', true);
                $ratingSummary = $ratingsByDoctor[(int) $doctor->doctor_id] ?? [
                    'average_rating' => null,
                    'review_count' => 0,
                ];

                return [
                    'doctor_user_id' => (int) $doctor->doctor_id,
                    'full_name' => $doctor->user?->full_name ?? $doctor->user?->name,
                    'email' => $doctor->user?->email,
                    'specialization' => $doctor->specialization,
                    'years_experience' => $doctor->years_experience !== null ? (int) $doctor->years_experience : null,
                    'consultation_fee' => $doctor->consultation_fee !== null ? (float) $doctor->consultation_fee : null,
                    'bio' => $doctor->bio,
                    'profile_image_url' => $doctor->profile_image_url,
                    'average_rating' => $ratingSummary['average_rating'],
                    'review_count' => $ratingSummary['review_count'],
                    'patients_seen_count' => $patientsSeenByDoctor[(int) $doctor->doctor_id] ?? 0,
                    'weekly_schedule_summary' => $weeklySummary,
                    'next_available_day_summary' => $nextAvailableDay ? [
                        'date' => $nextAvailableDay['date'],
                        'weekday' => $nextAvailableDay['weekday'],
                        'consultation_window' => $nextAvailableDay['consultation_window'],
                        'remaining_count' => $nextAvailableDay['remaining_count'],
                    ] : null,
                    'availability_preview' => $availabilityPreview,
                ];
            })->all(),
        ];
    }

    public function departmentAvailability(
        Department $department,
        ?int $doctorId = null,
        ?string $startDate = null,
        int $days = 7
    ): array {
        $days = max(1, min($days, 14));
        $start = $startDate ? Carbon::parse($startDate)->startOfDay() : now()->startOfDay();

        $doctorQuery = Doctor::query()
            ->with('user:id,full_name,name,email')
            ->where('department_id', $department->id)
            ->where('is_active', true)
            ->orderBy('doctor_id');

        if ($doctorId !== null) {
            $doctorQuery->where('doctor_id', $doctorId);
        }

        $doctors = $doctorQuery->get();

        return [
            'department' => [
                'id' => (int) $department->id,
                'name' => $department->dept_name,
                'slug' => $department->slug,
            ],
            'start_date' => $start->toDateString(),
            'days' => $days,
            'doctors' => $doctors->map(function (Doctor $doctor) use ($department, $start, $days) {
                return [
                    'doctor_user_id' => (int) $doctor->doctor_id,
                    'full_name' => $doctor->user?->full_name ?? $doctor->user?->name,
                    'specialization' => $doctor->specialization,
                    'availability' => $this->doctorAvailabilityPreview(
                        (int) $doctor->doctor_id,
                        (int) $department->id,
                        $start,
                        $days
                    ),
                ];
            })->all(),
        ];
    }

    /**
     * @return array<int, array{average_rating: float|null, review_count: int}>
     */
    private function doctorRatingSummary(array $doctorIds): array
    {
        if (empty($doctorIds)) {
            return [];
        }

        $rows = DoctorReview::query()
            ->selectRaw('doctor_user_id, AVG(CAST(rating AS FLOAT)) AS average_rating, COUNT(*) AS review_count')
            ->whereIn('doctor_user_id', $doctorIds)
            ->where('is_visible', true)
            ->groupBy('doctor_user_id')
            ->get();

        $summary = [];
        foreach ($rows as $row) {
            $summary[(int) $row->doctor_user_id] = [
                'average_rating' => $row->average_rating !== null ? round((float) $row->average_rating, 2) : null,
                'review_count' => (int) $row->review_count,
            ];
        }

        return $summary;
    }

    /**
     * @return array<int, array{average_rating: float|null, review_count: int}>
     */
    private function departmentRatingSummary(array $departmentIds): array
    {
        if (empty($departmentIds)) {
            return [];
        }

        $rows = DoctorReview::query()
            ->selectRaw('department_id, AVG(CAST(rating AS FLOAT)) AS average_rating, COUNT(*) AS review_count')
            ->whereIn('department_id', $departmentIds)
            ->where('is_visible', true)
            ->groupBy('department_id')
            ->get();

        $summary = [];
        foreach ($rows as $row) {
            $summary[(int) $row->department_id] = [
                'average_rating' => $row->average_rating !== null ? round((float) $row->average_rating, 2) : null,
                'review_count' => (int) $row->review_count,
            ];
        }

        return $summary;
    }

    /**
     * @return array<int, int>
     */
    private function patientsSeenSummary(array $doctorIds): array
    {
        if (empty($doctorIds)) {
            return [];
        }

        $rows = Appointment::query()
            ->selectRaw('doctor_user_id, COUNT(DISTINCT patient_id) AS patients_seen_count')
            ->whereIn('doctor_user_id', $doctorIds)
            ->whereIn('status', self::PATIENTS_SEEN_STATUSES)
            ->groupBy('doctor_user_id')
            ->get();

        $summary = [];
        foreach ($rows as $row) {
            $summary[(int) $row->doctor_user_id] = (int) $row->patients_seen_count;
        }

        return $summary;
    }

    /**
     * @return array<int, array{
     *     date: string,
     *     weekday: string,
     *     consultation_window: array<string, mixed>|null,
     *     daily_capacity: int,
     *     used_count: int,
     *     remaining_count: int,
     *     is_available: bool,
     *     status_label: string
     * }>
     */
    private function doctorAvailabilityPreview(
        int $doctorUserId,
        int $departmentId,
        Carbon $startDate,
        int $days
    ): array {
        $rows = [];

        for ($offset = 0; $offset < $days; $offset++) {
            $date = $startDate->copy()->addDays($offset);
            $dateString = $date->toDateString();

            $consultationWindow = $this->capacityService->consultationWindowForDate(
                $doctorUserId,
                $departmentId,
                $dateString
            );
            $dailyCapacity = $this->capacityService->dailyCapacityForDate(
                $doctorUserId,
                $departmentId,
                $dateString
            );
            $usedCount = $this->capacityService->usedCapacityForDate(
                $doctorUserId,
                $dateString
            );
            $remainingCount = max(0, $dailyCapacity - $usedCount);
            $isAvailable = $consultationWindow !== null && $dailyCapacity > 0 && $remainingCount > 0;

            $rows[] = [
                'date' => $dateString,
                'weekday' => $date->format('l'),
                'consultation_window' => $consultationWindow,
                'daily_capacity' => (int) $dailyCapacity,
                'used_count' => (int) $usedCount,
                'remaining_count' => (int) $remainingCount,
                'is_available' => $isAvailable,
                'status_label' => $this->availabilityStatusLabel($consultationWindow, $dailyCapacity, $remainingCount),
            ];
        }

        return $rows;
    }

    private function availabilityStatusLabel(?array $consultationWindow, int $dailyCapacity, int $remainingCount): string
    {
        if ($consultationWindow === null) {
            return 'No consultation routine';
        }

        if ($dailyCapacity <= 0) {
            return 'No capacity configured';
        }

        if ($remainingCount > 0) {
            return 'Available';
        }

        return 'Fully booked';
    }
}
