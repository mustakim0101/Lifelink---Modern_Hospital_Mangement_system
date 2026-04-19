<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Department;
use App\Models\DepartmentAdmin;
use App\Models\Doctor;
use App\Models\User;
use App\Services\AppointmentCapacityService;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItAppointmentQueueController extends Controller
{
    private const APPOINTMENT_STATUS = ['PendingApproval', 'Approved', 'Rejected', 'Cancelled', 'Completed', 'NoShow', 'Booked'];

    public function __construct(private readonly AppointmentCapacityService $capacityService)
    {
    }

    public function queue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'doctorUserId' => ['nullable', 'integer', 'exists:doctors,doctor_id'],
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'appointmentDate' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'string', Rule::in(self::APPOINTMENT_STATUS)],
        ]);

        $actor = auth('api')->user();
        $query = Appointment::query()
            ->with([
                'patient.user:id,full_name,name,email',
                'doctor:id,full_name,name,email',
                'department:id,dept_name',
                'approvedBy:id,full_name,name,email',
                'cancelledBy:id,full_name,name,email',
            ])
            ->orderByDesc('appointment_date')
            ->orderByDesc('id');

        if ($actor->hasRole('ITWorker') && ! $actor->hasRole('Admin')) {
            $query->whereIn('department_id', $this->accessibleDepartmentIds($actor));
        }

        if (! empty($validated['departmentId'])) {
            $this->ensureDepartmentAccessible($actor, (int) $validated['departmentId']);
            $query->where('department_id', $validated['departmentId']);
        }

        if (! empty($validated['doctorUserId'])) {
            $query->where('doctor_user_id', $validated['doctorUserId']);
        }

        if (! empty($validated['appointmentDate'])) {
            $query->whereDate('appointment_date', $validated['appointmentDate']);
        }

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return response()->json([
            'appointments' => $query->limit(300)->get()->map(fn (Appointment $appointment) => $this->appointmentPayload($appointment)),
        ]);
    }

    public function approve(Request $request, Appointment $appointment): JsonResponse
    {
        $request->validate([
            'approvalNote' => ['nullable', 'string', 'max:255'],
        ]);

        $actor = auth('api')->user();
        $this->ensureDepartmentAccessible($actor, (int) $appointment->department_id);

        $result = DB::transaction(function () use ($appointment, $actor) {
            $locked = Appointment::query()->lockForUpdate()->findOrFail($appointment->id);

            if ($locked->status !== 'PendingApproval') {
                return response()->json([
                    'message' => 'Only pending appointments can be approved.',
                ], 409);
            }

            if (! $locked->doctor_user_id || ! $locked->department_id || ! $locked->appointment_date) {
                return response()->json([
                    'message' => 'Appointment is missing doctor, department, or appointment date.',
                ], 422);
            }

            $doctor = Doctor::query()
                ->where('doctor_id', $locked->doctor_user_id)
                ->where('department_id', $locked->department_id)
                ->where('is_active', true)
                ->first();

            if (! $doctor) {
                return response()->json([
                    'message' => 'Doctor is not active in this department anymore.',
                ], 422);
            }

            $capacity = $this->capacityService->canCreateOrApprove(
                (int) $locked->doctor_user_id,
                (int) $locked->department_id,
                (string) $locked->appointment_date,
                (int) $locked->id
            );

            if (! $capacity['allowed']) {
                return response()->json([
                    'message' => $capacity['message'],
                    'capacity' => $capacity,
                ], 409);
            }

            $locked->update([
                'status' => 'Approved',
                'approved_by_user_id' => $actor->id,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            $locked->refresh();
            $locked->load(['patient.user:id,full_name,name,email', 'doctor:id,full_name,name,email', 'department:id,dept_name', 'approvedBy:id,full_name,name,email']);

            return response()->json([
                'message' => 'Appointment approved.',
                'appointment' => $this->appointmentPayload($locked),
                'capacity' => [
                    'daily_capacity' => $this->capacityService->dailyCapacityForDate(
                        (int) $locked->doctor_user_id,
                        (int) $locked->department_id,
                        (string) $locked->appointment_date
                    ),
                    'used_count' => $this->capacityService->usedCapacityForDate(
                        (int) $locked->doctor_user_id,
                        (string) $locked->appointment_date
                    ),
                    'remaining_count' => $this->capacityService->remainingCapacityForDate(
                        (int) $locked->doctor_user_id,
                        (int) $locked->department_id,
                        (string) $locked->appointment_date
                    ),
                ],
            ]);
        });

        return $result;
    }

    public function reject(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'rejectionReason' => ['required', 'string', 'max:255'],
        ]);

        $actor = auth('api')->user();
        $this->ensureDepartmentAccessible($actor, (int) $appointment->department_id);

        $result = DB::transaction(function () use ($appointment, $actor, $validated) {
            $locked = Appointment::query()->lockForUpdate()->findOrFail($appointment->id);

            if (! in_array($locked->status, ['PendingApproval', 'Booked'], true)) {
                return response()->json([
                    'message' => 'Only pending appointments can be rejected.',
                ], 409);
            }

            $locked->update([
                'status' => 'Rejected',
                'rejection_reason' => $validated['rejectionReason'],
                'approved_by_user_id' => $actor->id,
                'approved_at' => now(),
            ]);

            $locked->refresh();
            $locked->load(['patient.user:id,full_name,name,email', 'doctor:id,full_name,name,email', 'department:id,dept_name', 'approvedBy:id,full_name,name,email']);

            return response()->json([
                'message' => 'Appointment rejected.',
                'appointment' => $this->appointmentPayload($locked),
            ]);
        });

        return $result;
    }

    public function cancel(Request $request, Appointment $appointment): JsonResponse
    {
        $validated = $request->validate([
            'cancelReason' => ['nullable', 'string', 'max:255'],
        ]);

        $actor = auth('api')->user();
        $this->ensureDepartmentAccessible($actor, (int) $appointment->department_id);

        $result = DB::transaction(function () use ($appointment, $actor, $validated) {
            $locked = Appointment::query()->lockForUpdate()->findOrFail($appointment->id);

            if (in_array($locked->status, ['Cancelled', 'Completed', 'NoShow'], true)) {
                return response()->json([
                    'message' => 'Appointment cannot be cancelled from current state.',
                ], 409);
            }

            $locked->update([
                'status' => 'Cancelled',
                'cancelled_by_user_id' => $actor->id,
                'cancel_reason' => $validated['cancelReason'] ?? 'Cancelled by IT/admin',
            ]);

            $locked->refresh();
            $locked->load(['patient.user:id,full_name,name,email', 'doctor:id,full_name,name,email', 'department:id,dept_name', 'cancelledBy:id,full_name,name,email']);

            return response()->json([
                'message' => 'Appointment cancelled.',
                'appointment' => $this->appointmentPayload($locked),
            ]);
        });

        return $result;
    }

    private function accessibleDepartmentIds(User $user): array
    {
        if ($user->hasRole('Admin')) {
            return Department::query()->pluck('id')->all();
        }

        return DepartmentAdmin::query()
            ->where('user_id', $user->id)
            ->pluck('department_id')
            ->all();
    }

    private function ensureDepartmentAccessible(User $user, int $departmentId): void
    {
        if ($user->hasRole('Admin')) {
            return;
        }

        $allowed = DepartmentAdmin::query()
            ->where('user_id', $user->id)
            ->where('department_id', $departmentId)
            ->exists();

        if (! $allowed) {
            throw new HttpResponseException(response()->json([
                'message' => 'Forbidden: department access not allowed',
            ], 403));
        }
    }

    private function appointmentPayload(Appointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'patient_name' => $appointment->patient?->user?->full_name ?? $appointment->patient?->user?->name,
            'patient_email' => $appointment->patient?->user?->email,
            'department_id' => $appointment->department_id,
            'department' => $appointment->department?->dept_name,
            'doctor_user_id' => $appointment->doctor_user_id,
            'doctor_name' => $appointment->doctor?->full_name ?? $appointment->doctor?->name,
            'doctor_email' => $appointment->doctor?->email,
            'appointment_date' => optional($appointment->appointment_date)->format('Y-m-d'),
            'appointment_datetime' => optional($appointment->appointment_datetime)->toISOString(),
            'status' => $appointment->status,
            'approved_by_user_id' => $appointment->approved_by_user_id,
            'approved_by_name' => $appointment->approvedBy?->full_name ?? $appointment->approvedBy?->name,
            'approved_at' => optional($appointment->approved_at)->toISOString(),
            'rejection_reason' => $appointment->rejection_reason,
            'cancel_reason' => $appointment->cancel_reason,
            'cancelled_by_user_id' => $appointment->cancelled_by_user_id,
            'cancelled_by_name' => $appointment->cancelledBy?->full_name ?? $appointment->cancelledBy?->name,
            'created_at' => optional($appointment->created_at)->toISOString(),
            'updated_at' => optional($appointment->updated_at)->toISOString(),
        ];
    }
}
