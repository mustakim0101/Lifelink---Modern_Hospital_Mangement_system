<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorAppointmentRule;
use App\Services\AppointmentCapacityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorAppointmentRuleController extends Controller
{
    private const WEEKDAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function __construct(private readonly AppointmentCapacityService $capacityService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctorProfile();

        $validated = $request->validate([
            'activeOnly' => ['nullable', 'boolean'],
        ]);

        $query = DoctorAppointmentRule::query()
            ->where('doctor_user_id', $doctor->doctor_id)
            ->orderBy('day_of_week')
            ->orderBy('start_time');

        if (($validated['activeOnly'] ?? true) === true) {
            $query->where('is_active', true);
        }

        $rules = $query->get();

        return response()->json([
            'doctor_user_id' => $doctor->doctor_id,
            'department_id' => $doctor->department_id,
            'department' => $doctor->department?->dept_name,
            'summary' => $this->capacityService->doctorWeeklySummary($doctor->doctor_id, $doctor->department_id),
            'rules' => $rules->map(fn (DoctorAppointmentRule $rule) => $this->rulePayload($rule)),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctorProfile();

        $validated = $request->validate([
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'dayOfWeek' => ['required', 'integer', 'min:0', 'max:6'],
            'startTime' => ['required', 'date_format:H:i'],
            'endTime' => ['required', 'date_format:H:i', 'after:startTime'],
            'dailyCapacity' => ['required', 'integer', 'min:1', 'max:500'],
            'isActive' => ['nullable', 'boolean'],
        ]);

        $departmentId = (int) ($validated['departmentId'] ?? $doctor->department_id);
        if ($departmentId !== (int) $doctor->department_id) {
            return response()->json([
                'message' => 'Doctor can only manage schedules inside the doctor profile department.',
            ], 422);
        }

        $rule = DoctorAppointmentRule::query()->create([
            'doctor_user_id' => $doctor->doctor_id,
            'department_id' => $departmentId,
            'day_of_week' => (int) $validated['dayOfWeek'],
            'start_time' => $this->normalizeTimeValue($validated['startTime']),
            'end_time' => $this->normalizeTimeValue($validated['endTime']),
            'daily_capacity' => (int) $validated['dailyCapacity'],
            'is_active' => $validated['isActive'] ?? true,
        ]);

        return response()->json([
            'message' => 'Consultation routine created.',
            'rule' => $this->rulePayload($rule),
            'summary' => $this->capacityService->doctorWeeklySummary($doctor->doctor_id, $doctor->department_id),
        ], 201);
    }

    public function update(Request $request, DoctorAppointmentRule $rule): JsonResponse
    {
        $doctor = $this->resolveDoctorProfile();
        $this->ensureRuleOwner($doctor, $rule);

        $validated = $request->validate([
            'dayOfWeek' => ['nullable', 'integer', 'min:0', 'max:6'],
            'startTime' => ['nullable', 'date_format:H:i'],
            'endTime' => ['nullable', 'date_format:H:i'],
            'dailyCapacity' => ['nullable', 'integer', 'min:1', 'max:500'],
            'isActive' => ['nullable', 'boolean'],
        ]);

        $newStart = isset($validated['startTime']) ? $this->normalizeTimeValue($validated['startTime']) : $rule->start_time;
        $newEnd = isset($validated['endTime']) ? $this->normalizeTimeValue($validated['endTime']) : $rule->end_time;
        if ($newEnd <= $newStart) {
            return response()->json([
                'message' => 'Consultation end time must be greater than start time.',
            ], 422);
        }

        $rule->fill([
            'day_of_week' => $validated['dayOfWeek'] ?? $rule->day_of_week,
            'start_time' => $newStart,
            'end_time' => $newEnd,
            'daily_capacity' => $validated['dailyCapacity'] ?? $rule->daily_capacity,
            'is_active' => $validated['isActive'] ?? $rule->is_active,
        ]);
        $rule->save();

        return response()->json([
            'message' => 'Consultation routine updated.',
            'rule' => $this->rulePayload($rule),
            'summary' => $this->capacityService->doctorWeeklySummary($doctor->doctor_id, $doctor->department_id),
        ]);
    }

    public function deactivate(DoctorAppointmentRule $rule): JsonResponse
    {
        $doctor = $this->resolveDoctorProfile();
        $this->ensureRuleOwner($doctor, $rule);

        if (! $rule->is_active) {
            return response()->json([
                'message' => 'Consultation routine is already inactive.',
                'rule' => $this->rulePayload($rule),
            ]);
        }

        $rule->update(['is_active' => false]);

        return response()->json([
            'message' => 'Consultation routine deactivated.',
            'rule' => $this->rulePayload($rule),
            'summary' => $this->capacityService->doctorWeeklySummary($doctor->doctor_id, $doctor->department_id),
        ]);
    }

    private function resolveDoctorProfile(): Doctor
    {
        $doctor = Doctor::query()
            ->with('department:id,dept_name')
            ->find(auth('api')->id());

        abort_unless($doctor && $doctor->is_active, 404, 'Doctor profile not configured or inactive.');

        return $doctor;
    }

    private function ensureRuleOwner(Doctor $doctor, DoctorAppointmentRule $rule): void
    {
        abort_unless((int) $rule->doctor_user_id === (int) $doctor->doctor_id, 403, 'Appointment rule is not owned by this doctor.');
    }

    private function normalizeTimeValue(string $value): string
    {
        return substr($value, 0, 5).':00';
    }

    private function rulePayload(DoctorAppointmentRule $rule): array
    {
        return [
            'id' => $rule->id,
            'doctor_user_id' => $rule->doctor_user_id,
            'department_id' => $rule->department_id,
            'day_of_week' => (int) $rule->day_of_week,
            'weekday' => self::WEEKDAY_NAMES[(int) $rule->day_of_week] ?? 'Unknown',
            'start_time' => $rule->start_time,
            'end_time' => $rule->end_time,
            'daily_capacity' => (int) $rule->daily_capacity,
            'is_active' => (bool) $rule->is_active,
            'created_at' => optional($rule->created_at)->toISOString(),
            'updated_at' => optional($rule->updated_at)->toISOString(),
        ];
    }
}
