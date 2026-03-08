<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\BloodRequest;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\MedicalRecord;
use App\Models\Patient;
use App\Services\Sql\BloodMatchingSqlService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PatientPortalController extends Controller
{
    private const APPOINTMENT_STATUS = ['Booked', 'Cancelled', 'Completed', 'NoShow'];
    private const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    private const BLOOD_COMPONENTS = ['WholeBlood', 'Plasma', 'Platelets', 'RBC'];
    private const BLOOD_URGENCY = ['Normal', 'Urgent', 'Emergency'];
    private const BLOOD_STATUS = ['Pending', 'Matched', 'Approved', 'Fulfilled', 'Rejected', 'Cancelled'];

    public function __construct(private readonly BloodMatchingSqlService $matchingService)
    {
    }

    public function portal(): JsonResponse
    {
        $patient = $this->resolvePatientProfile();

        $recordsCount = MedicalRecord::query()
            ->where('patient_id', $patient->patient_id)
            ->count();

        $upcomingAppointments = Appointment::query()
            ->where('patient_id', $patient->patient_id)
            ->where('status', 'Booked')
            ->where('appointment_datetime', '>=', now())
            ->count();

        $bloodRequestsCount = BloodRequest::query()
            ->where('patient_id', $patient->patient_id)
            ->count();

        return response()->json([
            'patient' => $this->patientPayload($patient),
            'stats' => [
                'medical_records' => $recordsCount,
                'upcoming_appointments' => $upcomingAppointments,
                'blood_requests' => $bloodRequestsCount,
            ],
        ]);
    }

    public function profile(): JsonResponse
    {
        return response()->json([
            'patient' => $this->patientPayload($this->resolvePatientProfile()),
        ]);
    }

    public function medicalRecords(Request $request): JsonResponse
    {
        $patient = $this->resolvePatientProfile();

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = $validated['limit'] ?? 30;

        $records = MedicalRecord::query()
            ->where('patient_id', $patient->patient_id)
            ->with(['createdBy:id,full_name,name,email', 'admission:id,department_id,status,admit_date,discharge_date'])
            ->orderByDesc('record_datetime')
            ->limit($limit)
            ->get()
            ->map(fn (MedicalRecord $record) => [
                'id' => $record->id,
                'admission_id' => $record->admission_id,
                'record_datetime' => optional($record->record_datetime)->toISOString(),
                'diagnosis' => $record->diagnosis,
                'treatment_plan' => $record->treatment_plan,
                'notes' => $record->notes,
                'created_by_user_id' => $record->created_by_user_id,
                'created_by' => $record->createdBy?->full_name ?? $record->createdBy?->name,
                'created_by_email' => $record->createdBy?->email,
            ]);

        return response()->json([
            'medical_records' => $records,
        ]);
    }

    public function appointments(Request $request): JsonResponse
    {
        $patient = $this->resolvePatientProfile();

        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(self::APPOINTMENT_STATUS)],
        ]);

        $query = Appointment::query()
            ->with(['department:id,dept_name', 'doctor:id,full_name,name,email'])
            ->where('patient_id', $patient->patient_id)
            ->orderByDesc('appointment_datetime');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        return response()->json([
            'appointments' => $query->get()->map(fn (Appointment $appointment) => $this->appointmentPayload($appointment)),
        ]);
    }

    public function bookingOptions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $departmentQuery = Department::query()
            ->where('is_active', true)
            ->orderBy('dept_name');

        if (! empty($validated['departmentId'])) {
            $departmentQuery->where('id', $validated['departmentId']);
        }

        $doctorQuery = Doctor::query()
            ->with(['user:id,full_name,name,email', 'department:id,dept_name'])
            ->where('is_active', true)
            ->orderBy('doctor_id');

        if (! empty($validated['departmentId'])) {
            $doctorQuery->where('department_id', $validated['departmentId']);
        }

        return response()->json([
            'departments' => $departmentQuery->get(['id', 'dept_name']),
            'doctors' => $doctorQuery->get()->map(fn (Doctor $doctor) => [
                'user_id' => $doctor->doctor_id,
                'full_name' => $doctor->user?->full_name ?? $doctor->user?->name,
                'email' => $doctor->user?->email,
                'department_id' => $doctor->department_id,
                'department' => $doctor->department?->dept_name,
                'specialization' => $doctor->specialization,
            ]),
        ]);
    }

    public function bookAppointment(Request $request): JsonResponse
    {
        $patient = $this->resolvePatientProfile();

        $validated = $request->validate([
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
            'doctorUserId' => ['nullable', 'integer', 'exists:doctors,doctor_id'],
            'appointmentDateTime' => ['required', 'date', 'after:now'],
        ]);

        $doctorProfile = null;
        if (! empty($validated['doctorUserId'])) {
            $doctorProfile = Doctor::query()->find($validated['doctorUserId']);

            if (! $doctorProfile || ! $doctorProfile->is_active) {
                return response()->json([
                    'message' => 'Selected doctor profile is not active.',
                ], 422);
            }

            if ((int) $doctorProfile->department_id !== (int) $validated['departmentId']) {
                return response()->json([
                    'message' => 'Selected doctor does not belong to this department.',
                ], 422);
            }
        }

        $appointment = Appointment::query()->create([
            'patient_id' => $patient->patient_id,
            'department_id' => $validated['departmentId'],
            'doctor_user_id' => $doctorProfile?->doctor_id,
            'appointment_datetime' => $validated['appointmentDateTime'],
            'status' => 'Booked',
        ]);

        $appointment->load(['department:id,dept_name', 'doctor:id,full_name,name,email']);

        return response()->json([
            'message' => 'Appointment booked',
            'appointment' => $this->appointmentPayload($appointment),
        ], 201);
    }

    public function cancelAppointment(Request $request, Appointment $appointment): JsonResponse
    {
        $patient = $this->resolvePatientProfile();

        if ((int) $appointment->patient_id !== (int) $patient->patient_id) {
            return response()->json([
                'message' => 'This appointment does not belong to the current patient.',
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
            'cancel_reason' => $validated['cancelReason'] ?? 'Cancelled by patient',
        ]);

        $appointment->refresh();
        $appointment->load(['department:id,dept_name', 'doctor:id,full_name,name,email']);

        return response()->json([
            'message' => 'Appointment cancelled',
            'appointment' => $this->appointmentPayload($appointment),
        ]);
    }

    public function requestBlood(Request $request): JsonResponse
    {
        $patient = $this->resolvePatientProfile();

        $validated = $request->validate([
            'bloodGroup' => ['required', 'string', Rule::in(self::BLOOD_GROUPS)],
            'unitsRequested' => ['required', 'integer', 'min:1', 'max:20'],
            'admissionId' => ['nullable', 'integer', 'exists:admissions,id'],
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'componentType' => ['nullable', 'string', Rule::in(self::BLOOD_COMPONENTS)],
            'urgency' => ['nullable', 'string', Rule::in(self::BLOOD_URGENCY)],
            'notes' => ['nullable', 'string'],
        ]);

        $admission = null;
        $departmentId = $validated['departmentId'] ?? null;

        if (! empty($validated['admissionId'])) {
            $admission = Admission::query()->findOrFail($validated['admissionId']);
            if ((int) $admission->patient_user_id !== (int) $patient->patient_id) {
                return response()->json([
                    'message' => 'Admission does not belong to current patient.',
                ], 403);
            }
            $departmentId = $admission->department_id;
        }

        if (! $departmentId) {
            $departmentId = Admission::query()
                ->where('patient_user_id', $patient->patient_id)
                ->latest('id')
                ->value('department_id');
        }

        if (! $departmentId) {
            $departmentId = Department::query()->orderBy('id')->value('id');
        }

        if (! $departmentId) {
            return response()->json([
                'message' => 'No department available to map blood request.',
            ], 422);
        }

        $bloodRequest = BloodRequest::query()->create([
            'patient_id' => $patient->patient_id,
            'admission_id' => $admission?->id,
            'department_id' => $departmentId,
            'requested_by_user_id' => auth('api')->id(),
            'blood_group_needed' => $validated['bloodGroup'],
            'component_type' => $validated['componentType'] ?? 'WholeBlood',
            'units_required' => $validated['unitsRequested'],
            'urgency' => $validated['urgency'] ?? 'Urgent',
            'status' => 'Pending',
            'request_date' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        $bloodRequest->load(['department:id,dept_name']);

        return response()->json([
            'message' => 'Blood request submitted',
            'blood_request' => $this->bloodRequestPayload($bloodRequest),
        ], 201);
    }

    public function myBloodRequests(Request $request): JsonResponse
    {
        $patient = $this->resolvePatientProfile();

        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(self::BLOOD_STATUS)],
        ]);

        $query = BloodRequest::query()
            ->with(['department:id,dept_name'])
            ->where('patient_id', $patient->patient_id)
            ->orderByDesc('request_date')
            ->orderByDesc('id');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        $requests = $query->get();
        $acceptedDonorsByRequest = $this->matchingService->acceptedDonorsByRequestIds(
            $requests->pluck('id')->all()
        );

        return response()->json([
            'blood_requests' => $requests->map(
                fn (BloodRequest $request) => $this->bloodRequestPayload(
                    $request,
                    $acceptedDonorsByRequest[(int) $request->id] ?? []
                )
            ),
        ]);
    }

    private function resolvePatientProfile(): Patient
    {
        $user = auth('api')->user();

        $patient = Patient::query()->firstOrCreate(
            ['patient_id' => $user->id],
            ['is_active' => true]
        );

        if (! $patient->is_active) {
            abort(403, 'Patient profile is inactive.');
        }

        $patient->load('user:id,full_name,name,email', 'user.roles:id,role_name');

        return $patient;
    }

    private function patientPayload(Patient $patient): array
    {
        return [
            'patient_id' => $patient->patient_id,
            'full_name' => $patient->user?->full_name ?? $patient->user?->name,
            'email' => $patient->user?->email,
            'blood_group' => $patient->blood_group,
            'emergency_contact_name' => $patient->emergency_contact_name,
            'emergency_contact_phone' => $patient->emergency_contact_phone,
            'is_active' => (bool) $patient->is_active,
            'roles' => $patient->user?->roles?->pluck('role_name')->values(),
        ];
    }

    private function appointmentPayload(Appointment $appointment): array
    {
        return [
            'id' => $appointment->id,
            'patient_id' => $appointment->patient_id,
            'department_id' => $appointment->department_id,
            'department' => $appointment->department?->dept_name,
            'doctor_user_id' => $appointment->doctor_user_id,
            'doctor_name' => $appointment->doctor?->full_name ?? $appointment->doctor?->name,
            'doctor_email' => $appointment->doctor?->email,
            'appointment_datetime' => optional($appointment->appointment_datetime)->toISOString(),
            'status' => $appointment->status,
            'cancel_reason' => $appointment->cancel_reason,
            'cancelled_by_user_id' => $appointment->cancelled_by_user_id,
        ];
    }

    private function bloodRequestPayload(BloodRequest $request, array $acceptedDonors = []): array
    {
        return [
            'id' => $request->id,
            'patient_id' => $request->patient_id,
            'admission_id' => $request->admission_id,
            'department_id' => $request->department_id,
            'department' => $request->department?->dept_name,
            'requested_by_user_id' => $request->requested_by_user_id,
            'blood_group_needed' => $request->blood_group_needed,
            'component_type' => $request->component_type,
            'units_required' => $request->units_required,
            'urgency' => $request->urgency,
            'status' => $request->status,
            'request_date' => optional($request->request_date)->toISOString(),
            'notes' => $request->notes,
            'accepted_donors' => array_map(fn (array $donor) => [
                'donor_id' => $donor['donor_id'],
                'donor_name' => $donor['donor_name'],
                'donor_email' => $donor['donor_email'],
                'donor_blood_group' => $donor['donor_blood_group'],
                'match_status' => $donor['match_status'],
                'responded_at' => $donor['responded_at'],
            ], $acceptedDonors),
        ];
    }
}
