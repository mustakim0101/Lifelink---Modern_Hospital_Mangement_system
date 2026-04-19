<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\DoctorAppointmentRule;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Support\Collection;

class AppointmentCapacityService
{
    public const CAPACITY_STATUSES = ['PendingApproval', 'Approved', 'Booked'];
    private const WEEKDAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function normalizeDate(string|CarbonInterface $date): Carbon
    {
        if ($date instanceof CarbonInterface) {
            return Carbon::instance($date)->startOfDay();
        }

        return Carbon::parse($date)->startOfDay();
    }

    public function activeRulesForDate(int $doctorUserId, int $departmentId, string|CarbonInterface $date): Collection
    {
        $normalizedDate = $this->normalizeDate($date);
        $dayOfWeek = (int) $normalizedDate->dayOfWeek;

        return DoctorAppointmentRule::query()
            ->where('doctor_user_id', $doctorUserId)
            ->where('department_id', $departmentId)
            ->where('is_active', true)
            ->where('day_of_week', $dayOfWeek)
            ->orderBy('start_time')
            ->orderBy('end_time')
            ->get();
    }

    public function dailyCapacityForDate(int $doctorUserId, int $departmentId, string|CarbonInterface $date): int
    {
        return (int) $this->activeRulesForDate($doctorUserId, $departmentId, $date)->sum('daily_capacity');
    }

    public function consultationWindowForDate(int $doctorUserId, int $departmentId, string|CarbonInterface $date): ?array
    {
        $rules = $this->activeRulesForDate($doctorUserId, $departmentId, $date);
        if ($rules->isEmpty()) {
            return null;
        }

        $startTime = $rules->min('start_time');
        $endTime = $rules->max('end_time');

        return [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'label' => sprintf('%s - %s', $startTime, $endTime),
        ];
    }

    public function usedCapacityForDate(
        int $doctorUserId,
        string|CarbonInterface $date,
        ?int $excludeAppointmentId = null
    ): int {
        $normalizedDate = $this->normalizeDate($date)->toDateString();

        $query = Appointment::query()
            ->where('doctor_user_id', $doctorUserId)
            ->whereDate('appointment_date', $normalizedDate)
            ->whereIn('status', self::CAPACITY_STATUSES);

        if ($excludeAppointmentId !== null) {
            $query->where('id', '!=', $excludeAppointmentId);
        }

        return (int) $query->count();
    }

    public function remainingCapacityForDate(int $doctorUserId, int $departmentId, string|CarbonInterface $date): int
    {
        $capacity = $this->dailyCapacityForDate($doctorUserId, $departmentId, $date);
        $used = $this->usedCapacityForDate($doctorUserId, $date);

        return max(0, $capacity - $used);
    }

    public function canCreateOrApprove(
        int $doctorUserId,
        int $departmentId,
        string|CarbonInterface $date,
        ?int $excludeAppointmentId = null
    ): array {
        $normalizedDate = $this->normalizeDate($date);
        $rules = $this->activeRulesForDate($doctorUserId, $departmentId, $normalizedDate);

        if ($rules->isEmpty()) {
            return [
                'allowed' => false,
                'message' => 'Doctor has no active consultation routine for the selected date.',
                'capacity' => 0,
                'used' => 0,
                'remaining' => 0,
            ];
        }

        $capacity = (int) $rules->sum('daily_capacity');
        $used = $this->usedCapacityForDate($doctorUserId, $normalizedDate, $excludeAppointmentId);
        $remaining = max(0, $capacity - $used);

        if ($used >= $capacity) {
            return [
                'allowed' => false,
                'message' => 'Doctor daily capacity is full for the selected date.',
                'capacity' => $capacity,
                'used' => $used,
                'remaining' => $remaining,
            ];
        }

        return [
            'allowed' => true,
            'message' => null,
            'capacity' => $capacity,
            'used' => $used,
            'remaining' => $remaining,
        ];
    }

    public function doctorWeeklySummary(int $doctorUserId, ?int $departmentId = null): array
    {
        $query = DoctorAppointmentRule::query()
            ->where('doctor_user_id', $doctorUserId)
            ->where('is_active', true)
            ->orderBy('day_of_week')
            ->orderBy('start_time');

        if ($departmentId !== null) {
            $query->where('department_id', $departmentId);
        }

        $rules = $query->get();
        if ($rules->isEmpty()) {
            return [
                'active_weekdays' => [],
                'consultation_window' => null,
                'daily_capacity_default' => 0,
                'daily_capacity_by_weekday' => [],
                'rules' => [],
            ];
        }

        $activeWeekdays = $rules->pluck('day_of_week')
            ->unique()
            ->sort()
            ->values()
            ->map(fn (int $day) => ['day_of_week' => $day, 'weekday' => self::WEEKDAY_NAMES[$day]])
            ->all();

        $consultationWindow = [
            'start_time' => $rules->min('start_time'),
            'end_time' => $rules->max('end_time'),
        ];
        $consultationWindow['label'] = sprintf('%s - %s', $consultationWindow['start_time'], $consultationWindow['end_time']);

        $capacityByWeekday = $rules->groupBy('day_of_week')
            ->map(function (Collection $items, int $day) {
                return [
                    'day_of_week' => (int) $day,
                    'weekday' => self::WEEKDAY_NAMES[(int) $day],
                    'daily_capacity' => (int) $items->sum('daily_capacity'),
                ];
            })
            ->sortBy('day_of_week')
            ->values()
            ->all();

        $defaultCapacity = (int) round(
            $rules->groupBy('day_of_week')->map(fn (Collection $items) => (int) $items->sum('daily_capacity'))->avg()
        );

        return [
            'active_weekdays' => $activeWeekdays,
            'consultation_window' => $consultationWindow,
            'daily_capacity_default' => $defaultCapacity,
            'daily_capacity_by_weekday' => $capacityByWeekday,
            'rules' => $rules->map(fn (DoctorAppointmentRule $rule) => [
                'id' => $rule->id,
                'doctor_user_id' => $rule->doctor_user_id,
                'department_id' => $rule->department_id,
                'day_of_week' => (int) $rule->day_of_week,
                'weekday' => self::WEEKDAY_NAMES[(int) $rule->day_of_week],
                'start_time' => $rule->start_time,
                'end_time' => $rule->end_time,
                'daily_capacity' => (int) $rule->daily_capacity,
                'is_active' => (bool) $rule->is_active,
            ])->all(),
        ];
    }
}

