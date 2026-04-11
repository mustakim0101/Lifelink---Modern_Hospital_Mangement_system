<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\User;
use App\Services\AppointmentCapacityService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class DoctorClinicalController extends Controller
{
    private const CARE_LEVELS = ['Ward', 'ICU', 'NICU', 'CCU'];
    private const APPOINTMENT_STATUS = ['PendingApproval', 'Approved', 'Rejected', 'Cancelled', 'Completed', 'NoShow', 'Booked'];
    private const WEEKDAY_NAMES = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

    public function __construct(private readonly AppointmentCapacityService $capacityService)
    {
    }

    public function upsertDoctorProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'userId' => ['required', 'integer', 'exists:users,id'],
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
            'specialization' => ['nullable', 'string', 'max:150'],
            'licenseNumber' => ['nullable', 'string', 'max:100'],
            'isActive' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->findOrFail($validated['userId']);
        if (! $user->hasRole('Doctor')) {
            return response()->json([
                'message' => 'Target user must have Doctor role first.',
            ], 422);
        }

        $profile = Doctor::query()->updateOrCreate(
            ['doctor_id' => $validated['userId']],
            [
                'department_id' => $validated['departmentId'],
                'specialization' => $validated['specialization'] ?? null,
                'license_number' => $validated['licenseNumber'] ?? null,
                'is_active' => $validated['isActive'] ?? true,
            ]
        );

        $profile->load(['user:id,full_name,name,email', 'department:id,dept_name']);

        return response()->json([
            'message' => 'Doctor profile upserted',
            'doctor' => $this->doctorProfilePayload($profile),
        ]);
    }

    public function profile(): JsonResponse
    {
        $user = auth('api')->user();

        $doctor = Doctor::query()
            ->with(['department:id,dept_name', 'user:id,full_name,name,email'])
            ->find($user->id);

        if (! $doctor || ! $doctor->is_active) {
            return response()->json([
                'message' => 'Doctor profile not configured or inactive.',
            ], 404);
        }

        return response()->json([
            'doctor' => $this->doctorProfilePayload($doctor),
        ]);
    }

    public function patients(): JsonResponse
    {
        $doctor = $this->resolveDoctorProfile();
        $doctorId = auth('api')->id();

        $appointmentPatientIds = Appointment::query()
            ->where('doctor_user_id', $doctorId)
            ->pluck('patient_id')
            ->all();

        $admissionPatientIds = Admission::query()
            ->where('admitted_by_doctor_id', $doctorId)
            ->pluck('patient_user_id')
            ->all();

        $patientIds = array_values(array_unique(array_merge($appointmentPatientIds, $admissionPatientIds)));

        if (empty($patientIds)) {
            return response()->json([
                'patients' => [],
            ]);
        }

        $patients = User::query()
            ->with('patientProfile')
            ->whereIn('id', $patientIds)
            ->orderBy('id')
            ->get()
            ->map(function (User $user) use ($doctorId) {
                $activeAdmission = Admission::query()
                    ->where('patient_user_id', $user->id)
                    ->where('admitted_by_doctor_id', $doctorId)
                    ->where('status', 'Admitted')
                    ->latest('id')
                    ->first();

                return [
                    'patient_user_id' => $user->id,
                    'full_name' => $user->full_name ?? $user->name,
                    'email' => $user->email,
                    'blood_group' => $user->patientProfile?->blood_group,
                    'active_admission_id' => $activeAdmission?->id,
                    'active_admission_status' => $activeAdmission?->status,
                ];
            });

        return response()->json([
            'department_id' => $doctor->department_id,
            'department' => $doctor->department?->dept_name,
            'patients' => $patients,
        ]);
    }

    public function appointments(Request $request): JsonResponse
    {
        $this->resolveDoctorProfile();

        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(self::APPOINTMENT_STATUS)],
            'appointmentDate' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $query = Appointment::query()
            ->with(['patient.user:id,full_name,name,email', 'department:id,dept_name'])
            ->where('doctor_user_id', auth('api')->id())
            ->orderByDesc('appointment_date')
            ->orderByDesc('appointment_datetime');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['appointmentDate'])) {
            $query->whereDate('appointment_date', $validated['appointmentDate']);
        }

        return response()->json([
            'appointments' => $query->get()->map(fn (Appointment $a) => [
                'id' => $a->id,
                'patient_id' => $a->patient_id,
                'patient_name' => $a->patient?->user?->full_name ?? $a->patient?->user?->name,
                'patient_email' => $a->patient?->user?->email,
                'department_id' => $a->department_id,
                'department' => $a->department?->dept_name,
                'appointment_date' => optional($a->appointment_date)->format('Y-m-d'),
                'appointment_datetime' => optional($a->appointment_datetime)->toISOString(),
                'status' => $a->status,
                'approved_by_user_id' => $a->approved_by_user_id,
                'approved_at' => optional($a->approved_at)->toISOString(),
                'rejection_reason' => $a->rejection_reason,
                'cancel_reason' => $a->cancel_reason,
            ]),
        ]);
    }

    public function appointmentSummary(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctorProfile();
        $doctorUserId = (int) auth('api')->id();

        $validated = $request->validate([
            'dateFrom' => ['nullable', 'date_format:Y-m-d'],
            'dateTo' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:dateFrom'],
        ]);

        $fromDate = isset($validated['dateFrom'])
            ? Carbon::parse($validated['dateFrom'])->startOfDay()
            : now()->startOfDay();
        $toDate = isset($validated['dateTo'])
            ? Carbon::parse($validated['dateTo'])->startOfDay()
            : now()->addDays(14)->startOfDay();

        $appointments = Appointment::query()
            ->with(['patient.user:id,full_name,name,email'])
            ->where('doctor_user_id', $doctorUserId)
            ->whereBetween('appointment_date', [$fromDate->toDateString(), $toDate->toDateString()])
            ->orderBy('appointment_date')
            ->get();

        $dateRows = [];
        $cursor = $fromDate->copy();
        while ($cursor->lte($toDate)) {
            $dateKey = $cursor->toDateString();
            $dayAppointments = $appointments->filter(
                fn (Appointment $a) => optional($a->appointment_date)->format('Y-m-d') === $dateKey
            )->values();

            $pendingPatients = $dayAppointments
                ->filter(fn (Appointment $a) => $a->status === 'PendingApproval')
                ->map(fn (Appointment $a) => [
                    'appointment_id' => $a->id,
                    'patient_id' => $a->patient_id,
                    'patient_name' => $a->patient?->user?->full_name ?? $a->patient?->user?->name,
                    'status' => $a->status,
                ])->values();

            $approvedPatients = $dayAppointments
                ->filter(fn (Appointment $a) => in_array($a->status, ['Approved', 'Booked'], true))
                ->map(fn (Appointment $a) => [
                    'appointment_id' => $a->id,
                    'patient_id' => $a->patient_id,
                    'patient_name' => $a->patient?->user?->full_name ?? $a->patient?->user?->name,
                    'status' => $a->status,
                ])->values();

            $capacity = $this->capacityService->dailyCapacityForDate(
                $doctorUserId,
                (int) $doctor->department_id,
                $dateKey
            );
            $usedCount = (int) $pendingPatients->count() + (int) $approvedPatients->count();

            $dateRows[] = [
                'date' => $dateKey,
                'day_of_week' => (int) $cursor->dayOfWeek,
                'weekday' => self::WEEKDAY_NAMES[(int) $cursor->dayOfWeek] ?? 'Unknown',
                'consultation_window' => $this->capacityService->consultationWindowForDate(
                    $doctorUserId,
                    (int) $doctor->department_id,
                    $dateKey
                ),
                'daily_capacity' => $capacity,
                'pending_count' => (int) $pendingPatients->count(),
                'approved_count' => (int) $approvedPatients->count(),
                'total_count' => $usedCount,
                'remaining_capacity' => max(0, $capacity - $usedCount),
                'pending_patients' => $pendingPatients,
                'approved_patients' => $approvedPatients,
            ];

            $cursor->addDay();
        }

        return response()->json([
            'doctor_user_id' => $doctorUserId,
            'department_id' => $doctor->department_id,
            'department' => $doctor->department?->dept_name,
            'date_from' => $fromDate->toDateString(),
            'date_to' => $toDate->toDateString(),
            'by_date' => $dateRows,
        ]);
    }

    public function cancelAppointment(Request $request, Appointment $appointment): JsonResponse
    {
        $this->resolveDoctorProfile();

        if ((int) $appointment->doctor_user_id !== (int) auth('api')->id()) {
            return response()->json([
                'message' => 'Appointment is not assigned to this doctor.',
            ], 403);
        }

        if (! in_array($appointment->status, ['PendingApproval', 'Approved', 'Booked'], true)) {
            return response()->json([
                'message' => 'Only pending or approved appointments can be cancelled.',
            ], 409);
        }

        $validated = $request->validate([
            'cancelReason' => ['nullable', 'string', 'max:255'],
        ]);

        $appointment->update([
            'status' => 'Cancelled',
            'cancelled_by_user_id' => auth('api')->id(),
            'cancel_reason' => $validated['cancelReason'] ?? 'Cancelled by doctor',
        ]);

        return response()->json([
            'message' => 'Appointment cancelled',
            'appointment' => [
                'id' => $appointment->id,
                'status' => $appointment->status,
                'cancel_reason' => $appointment->cancel_reason,
            ],
        ]);
    }

    public function createBedRequest(Request $request): JsonResponse
    {
        $doctor = $this->resolveDoctorProfile();
        $doctorId = auth('api')->id();

        $validated = $request->validate([
            'patientUserId' => ['required', 'integer', 'exists:patients,patient_id'],
            'diagnosis' => ['required', 'string', 'max:255'],
            'careLevelRequested' => ['required', 'string', Rule::in(self::CARE_LEVELS)],
            'notes' => ['nullable', 'string'],
        ]);

        $existing = Admission::query()
            ->where('patient_user_id', $validated['patientUserId'])
            ->where('status', 'Admitted')
            ->exists();

        if ($existing) {
            return response()->json([
                'message' => 'Patient already has an active admission.',
            ], 409);
        }

        $admission = Admission::query()->create([
            'patient_user_id' => $validated['patientUserId'],
            'department_id' => $doctor->department_id,
            'admitted_by_doctor_id' => $doctorId,
            'diagnosis' => $validated['diagnosis'],
            'care_level_requested' => $validated['careLevelRequested'],
            'status' => 'Admitted',
            'admit_date' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        $admission->load(['patient:id,full_name,name,email', 'department:id,dept_name', 'admittedByDoctor:id,full_name,name,email']);

        return response()->json([
            'message' => 'Bed request submitted',
            'admission' => $this->doctorAdmissionPayload($admission),
        ], 201);
    }

    public function myBedRequests(Request $request): JsonResponse
    {
        $this->resolveDoctorProfile();

        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(['Admitted', 'Discharged', 'Transferred', 'Cancelled'])],
        ]);

        $query = Admission::query()
            ->with([
                'patient:id,full_name,name,email',
                'department:id,dept_name',
                'bedAssignments' => fn ($q) => $q->whereNull('released_at')->with(['bed:id,care_unit_id,bed_code,status', 'bed.careUnit:id,department_id,unit_type,unit_name']),
            ])
            ->where('admitted_by_doctor_id', auth('api')->id())
            ->orderByDesc('id');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return response()->json([
            'bed_requests' => $query->get()->map(fn (Admission $admission) => $this->doctorAdmissionPayload($admission)),
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

    private function doctorProfilePayload(Doctor $doctor): array
    {
        return [
            'doctor_id' => $doctor->doctor_id,
            'full_name' => $doctor->user?->full_name ?? $doctor->user?->name,
            'email' => $doctor->user?->email,
            'department_id' => $doctor->department_id,
            'department' => $doctor->department?->dept_name,
            'specialization' => $doctor->specialization,
            'license_number' => $doctor->license_number,
            'is_active' => (bool) $doctor->is_active,
        ];
    }

    private function doctorAdmissionPayload(Admission $admission): array
    {
        $activeAssignment = $admission->bedAssignments->first();
        $bed = $activeAssignment?->bed;

        return [
            'id' => $admission->id,
            'patient_user_id' => $admission->patient_user_id,
            'patient_name' => $admission->patient?->full_name ?? $admission->patient?->name,
            'patient_email' => $admission->patient?->email,
            'department_id' => $admission->department_id,
            'department' => $admission->department?->dept_name,
            'diagnosis' => $admission->diagnosis,
            'care_level_requested' => $admission->care_level_requested,
            'care_level_assigned' => $admission->care_level_assigned,
            'status' => $admission->status,
            'admit_date' => optional($admission->admit_date)->toISOString(),
            'active_bed_assignment' => $activeAssignment ? [
                'assignment_id' => $activeAssignment->id,
                'bed_id' => $bed?->id,
                'bed_code' => $bed?->bed_code,
                'unit_type' => $bed?->careUnit?->unit_type,
                'assigned_at' => optional($activeAssignment->assigned_at)->toISOString(),
            ] : null,
        ];
    }
}
