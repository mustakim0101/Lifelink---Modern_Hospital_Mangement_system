<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Doctor;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DoctorClinicalController extends Controller
{
    private const CARE_LEVELS = ['Ward', 'ICU', 'NICU', 'CCU'];

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
            'status' => ['nullable', 'string', Rule::in(['Booked', 'Cancelled', 'Completed', 'NoShow'])],
        ]);

        $query = Appointment::query()
            ->with(['patient.user:id,full_name,name,email', 'department:id,dept_name'])
            ->where('doctor_user_id', auth('api')->id())
            ->orderByDesc('appointment_datetime');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return response()->json([
            'appointments' => $query->get()->map(fn (Appointment $a) => [
                'id' => $a->id,
                'patient_id' => $a->patient_id,
                'patient_name' => $a->patient?->user?->full_name ?? $a->patient?->user?->name,
                'patient_email' => $a->patient?->user?->email,
                'department_id' => $a->department_id,
                'department' => $a->department?->dept_name,
                'appointment_datetime' => optional($a->appointment_datetime)->toISOString(),
                'status' => $a->status,
                'cancel_reason' => $a->cancel_reason,
            ]),
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

        if ($appointment->status !== 'Booked') {
            return response()->json([
                'message' => 'Only booked appointments can be cancelled.',
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

