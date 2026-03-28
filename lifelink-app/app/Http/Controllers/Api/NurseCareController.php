<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\DonorHealthCheck;
use App\Models\DonorProfile;
use App\Models\MedicalRecord;
use App\Models\Nurse;
use App\Models\NurseVitalSignLog;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NurseCareController extends Controller
{
    private const ADMISSION_STATUS = ['Admitted', 'Discharged', 'Transferred', 'Cancelled'];
    private const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];

    public function upsertNurseProfile(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'userId' => ['required', 'integer', 'exists:users,id'],
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
            'wardAssignmentNote' => ['nullable', 'string', 'max:255'],
            'isActive' => ['nullable', 'boolean'],
        ]);

        $user = User::query()->findOrFail($validated['userId']);
        if (! $user->hasRole('Nurse')) {
            return response()->json([
                'message' => 'Target user must have Nurse role first.',
            ], 422);
        }

        $profile = Nurse::query()->updateOrCreate(
            ['nurse_id' => $validated['userId']],
            [
                'department_id' => $validated['departmentId'],
                'ward_assignment_note' => $validated['wardAssignmentNote'] ?? null,
                'is_active' => $validated['isActive'] ?? true,
            ]
        );

        $profile->load(['user:id,full_name,name,email', 'department:id,dept_name']);

        return response()->json([
            'message' => 'Nurse profile upserted',
            'nurse' => $this->nursePayload($profile),
        ]);
    }

    public function profile(): JsonResponse
    {
        $nurse = $this->resolveNurseProfile();

        return response()->json([
            'nurse' => $this->nursePayload($nurse),
        ]);
    }

    public function patients(Request $request): JsonResponse
    {
        $nurse = $this->resolveNurseProfile();

        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(self::ADMISSION_STATUS)],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $query = Admission::query()
            ->with([
                'patient:id,full_name,name,email',
                'department:id,dept_name',
                'patient.patientProfile:patient_id,blood_group',
                'bedAssignments' => fn ($q) => $q
                    ->whereNull('released_at')
                    ->with(['bed:id,care_unit_id,bed_code,status', 'bed.careUnit:id,department_id,unit_type,unit_name,floor']),
            ])
            ->where('department_id', $nurse->department_id)
            ->orderByDesc('admit_date')
            ->orderByDesc('id');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['q'])) {
            $term = trim($validated['q']);

            $query->where(function ($q) use ($term) {
                $q->where('diagnosis', 'like', '%'.$term.'%')
                    ->orWhereHas('patient', function ($patientQ) use ($term) {
                        $patientQ->where('full_name', 'like', '%'.$term.'%')
                            ->orWhere('name', 'like', '%'.$term.'%')
                            ->orWhere('email', 'like', '%'.$term.'%');
                    })
                    ->orWhereHas('bedAssignments.bed', function ($bedQ) use ($term) {
                        $bedQ->where('bed_code', 'like', '%'.$term.'%');
                    });
            });
        }

        $admissions = $query->get();
        $latestVitals = $this->latestVitalsByAdmission($admissions->pluck('id')->all());

        $patients = $admissions->map(function (Admission $admission) use ($latestVitals) {
            return $this->admissionPayload($admission, $latestVitals[$admission->id] ?? null);
        });

        $hasRecentVitals = collect($latestVitals)
            ->filter(fn (?NurseVitalSignLog $log) => $log && $log->measured_at && $log->measured_at->gte(now()->subDay()))
            ->count();

        return response()->json([
            'nurse' => $this->nursePayload($nurse),
            'stats' => [
                'total_admissions' => $admissions->count(),
                'active_admissions' => $admissions->where('status', 'Admitted')->count(),
                'with_bed_assignment' => $admissions->filter(fn (Admission $a) => $a->bedAssignments->isNotEmpty())->count(),
                'without_bed_assignment' => $admissions->filter(fn (Admission $a) => $a->bedAssignments->isEmpty())->count(),
                'monitored_last_24h' => $hasRecentVitals,
            ],
            'patients' => $patients,
        ]);
    }

    public function admissionDetail(Request $request, Admission $admission): JsonResponse
    {
        $nurse = $this->resolveNurseProfile();
        $this->ensureAdmissionAccessible($nurse, $admission);

        $validated = $request->validate([
            'vitalsLimit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'recordsLimit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $vitalsLimit = $validated['vitalsLimit'] ?? 20;
        $recordsLimit = $validated['recordsLimit'] ?? 20;

        $admission->load([
            'patient:id,full_name,name,email',
            'department:id,dept_name',
            'patient.patientProfile:patient_id,blood_group,emergency_contact_name,emergency_contact_phone',
            'bedAssignments' => fn ($q) => $q
                ->whereNull('released_at')
                ->with(['bed:id,care_unit_id,bed_code,status', 'bed.careUnit:id,department_id,unit_type,unit_name,floor']),
        ]);

        $medicalRecords = MedicalRecord::query()
            ->where('patient_id', $admission->patient_user_id)
            ->where(function ($q) use ($admission) {
                $q->whereNull('admission_id')->orWhere('admission_id', $admission->id);
            })
            ->with('createdBy:id,full_name,name,email')
            ->orderByDesc('record_datetime')
            ->limit($recordsLimit)
            ->get()
            ->map(fn (MedicalRecord $record) => [
                'id' => $record->id,
                'record_datetime' => optional($record->record_datetime)->toISOString(),
                'diagnosis' => $record->diagnosis,
                'treatment_plan' => $record->treatment_plan,
                'notes' => $record->notes,
                'created_by' => $record->createdBy?->full_name ?? $record->createdBy?->name,
                'created_by_email' => $record->createdBy?->email,
            ]);

        $vitals = NurseVitalSignLog::query()
            ->where('admission_id', $admission->id)
            ->with('nurse.user:id,full_name,name,email')
            ->orderByDesc('measured_at')
            ->orderByDesc('id')
            ->limit($vitalsLimit)
            ->get()
            ->map(fn (NurseVitalSignLog $log) => $this->vitalPayload($log));

        return response()->json([
            'admission' => $this->admissionPayload($admission, $vitals->first()),
            'medical_records' => $medicalRecords,
            'vital_sign_logs' => $vitals,
        ]);
    }

    public function vitalSigns(Request $request, Admission $admission): JsonResponse
    {
        $nurse = $this->resolveNurseProfile();
        $this->ensureAdmissionAccessible($nurse, $admission);

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $limit = $validated['limit'] ?? 20;

        $logs = NurseVitalSignLog::query()
            ->where('admission_id', $admission->id)
            ->with('nurse.user:id,full_name,name,email')
            ->orderByDesc('measured_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(fn (NurseVitalSignLog $log) => $this->vitalPayload($log));

        return response()->json([
            'admission_id' => $admission->id,
            'vital_sign_logs' => $logs,
        ]);
    }

    public function logVitalSigns(Request $request, Admission $admission): JsonResponse
    {
        $nurse = $this->resolveNurseProfile();
        $this->ensureAdmissionAccessible($nurse, $admission);

        $validated = $request->validate([
            'patientUserId' => ['required', 'integer', 'exists:patients,patient_id'],
            'measuredAt' => ['nullable', 'date'],
            'temperatureC' => ['nullable', 'numeric', 'between:30,45'],
            'pulseBpm' => ['nullable', 'integer', 'between:20,260'],
            'systolicBp' => ['nullable', 'integer', 'between:40,300'],
            'diastolicBp' => ['nullable', 'integer', 'between:30,200'],
            'respirationRate' => ['nullable', 'integer', 'between:5,80'],
            'spo2Percent' => ['nullable', 'integer', 'between:0,100'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        if ($admission->status !== 'Admitted') {
            return response()->json([
                'message' => 'Vital signs can be logged only for admitted patients.',
            ], 409);
        }

        if ((int) $admission->patient_user_id !== (int) $validated['patientUserId']) {
            return response()->json([
                'message' => 'Patient does not match this admission.',
            ], 422);
        }

        if (
            is_null($validated['temperatureC'] ?? null)
            && is_null($validated['pulseBpm'] ?? null)
            && is_null($validated['systolicBp'] ?? null)
            && is_null($validated['diastolicBp'] ?? null)
            && is_null($validated['respirationRate'] ?? null)
            && is_null($validated['spo2Percent'] ?? null)
        ) {
            return response()->json([
                'message' => 'At least one vital measurement is required.',
            ], 422);
        }

        if (
            isset($validated['systolicBp'], $validated['diastolicBp'])
            && $validated['diastolicBp'] >= $validated['systolicBp']
        ) {
            return response()->json([
                'message' => 'Diastolic BP must be lower than systolic BP.',
            ], 422);
        }

        $log = NurseVitalSignLog::query()->create([
            'admission_id' => $admission->id,
            'patient_id' => $validated['patientUserId'],
            'nurse_id' => $nurse->nurse_id,
            'measured_at' => $validated['measuredAt'] ?? now(),
            'temperature_c' => $validated['temperatureC'] ?? null,
            'pulse_bpm' => $validated['pulseBpm'] ?? null,
            'systolic_bp' => $validated['systolicBp'] ?? null,
            'diastolic_bp' => $validated['diastolicBp'] ?? null,
            'respiration_rate' => $validated['respirationRate'] ?? null,
            'spo2_percent' => $validated['spo2Percent'] ?? null,
            'note' => $validated['note'] ?? null,
        ]);

        $log->load('nurse.user:id,full_name,name,email');

        return response()->json([
            'message' => 'Vital signs logged',
            'vital_sign' => $this->vitalPayload($log),
        ], 201);
    }

    public function bloodBankDonors(Request $request): JsonResponse
    {
        $nurse = $this->resolveBloodBankNurse();

        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'requestId' => ['nullable', 'integer', 'exists:blood_requests,id'],
            'bloodGroup' => ['nullable', 'string', Rule::in(self::BLOOD_GROUPS)],
            'eligible' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:80'],
        ]);

        $query = DonorProfile::query()
            ->with(['donor:id,full_name,name,email', 'healthChecks' => fn ($q) => $q->latest('check_datetime')->latest('id')->limit(1)])
            ->orderBy('donor_id');

        if (array_key_exists('eligible', $validated)) {
            $query->where('is_eligible', (bool) $validated['eligible']);
        }

        if (! empty($validated['bloodGroup'])) {
            $query->where('blood_group', $validated['bloodGroup']);
        }

        if (! empty($validated['requestId'])) {
            $query->whereIn('donor_id', function ($q) use ($validated) {
                $q->select('donor_id')
                    ->from('blood_request_matches')
                    ->where('request_id', $validated['requestId'])
                    ->whereIn('status', ['Accepted', 'Completed']);
            });
        }

        if (! empty($validated['q'])) {
            $term = trim($validated['q']);
            $query->where(function ($q) use ($term) {
                $q->where('donor_id', 'like', '%'.$term.'%')
                    ->orWhere('blood_group', 'like', '%'.$term.'%')
                    ->orWhereHas('donor', function ($userQuery) use ($term) {
                        $userQuery->where('full_name', 'like', '%'.$term.'%')
                            ->orWhere('name', 'like', '%'.$term.'%')
                            ->orWhere('email', 'like', '%'.$term.'%');
                    });
            });
        }

        $donors = $query->limit($validated['limit'] ?? 30)->get();

        return response()->json([
            'nurse' => $this->nursePayload($nurse),
            'donors' => $donors->map(fn (DonorProfile $profile) => $this->donorPayload($profile)),
        ]);
    }

    public function donorHealthChecks(Request $request, int $donor): JsonResponse
    {
        $this->resolveBloodBankNurse();

        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $profile = DonorProfile::query()
            ->with('donor:id,full_name,name,email')
            ->findOrFail($donor);

        $checks = DonorHealthCheck::query()
            ->where('donor_id', $profile->donor_id)
            ->with('checkedBy:id,full_name,name,email')
            ->orderByDesc('check_datetime')
            ->orderByDesc('id')
            ->limit($validated['limit'] ?? 20)
            ->get();

        return response()->json([
            'donor' => $this->donorPayload($profile),
            'health_checks' => $checks->map(fn (DonorHealthCheck $check) => $this->donorHealthPayload($check)),
        ]);
    }

    public function logDonorHealthCheck(Request $request, int $donor): JsonResponse
    {
        $nurse = $this->resolveBloodBankNurse();
        $profile = DonorProfile::query()
            ->with('donor:id,full_name,name,email')
            ->findOrFail($donor);

        $validated = $request->validate([
            'checkDateTime' => ['nullable', 'date'],
            'weightKg' => ['required', 'numeric', 'min:30', 'max:250'],
            'temperatureC' => ['required', 'numeric', 'min:34', 'max:43'],
            'hemoglobin' => ['nullable', 'numeric', 'min:5', 'max:25'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $eligibility = $this->evaluateDonorEligibility(
            (float) $validated['weightKg'],
            (float) $validated['temperatureC'],
            array_key_exists('hemoglobin', $validated) ? (float) $validated['hemoglobin'] : null
        );

        $check = DonorHealthCheck::query()->create([
            'donor_id' => $profile->donor_id,
            'check_datetime' => $validated['checkDateTime'] ?? now(),
            'weight_kg' => $validated['weightKg'],
            'temperature_c' => $validated['temperatureC'],
            'hemoglobin' => $validated['hemoglobin'] ?? null,
            'notes' => $validated['notes'] ?? $eligibility['reason'],
            'checked_by_user_id' => $nurse->nurse_id,
        ]);

        $profile->update([
            'is_eligible' => $eligibility['is_eligible'],
            'notes' => $eligibility['is_eligible'] ? $profile->notes : $eligibility['reason'],
        ]);

        $check->load('checkedBy:id,full_name,name,email');
        $profile->refresh();
        $profile->load('donor:id,full_name,name,email');

        return response()->json([
            'message' => 'Donor health check logged by Blood Bank nurse.',
            'donor' => $this->donorPayload($profile),
            'eligibility' => $eligibility,
            'health_check' => $this->donorHealthPayload($check),
        ], 201);
    }

    private function resolveNurseProfile(): Nurse
    {
        $nurse = Nurse::query()
            ->with(['department:id,dept_name', 'user:id,full_name,name,email'])
            ->find(auth('api')->id());

        abort_unless($nurse && $nurse->is_active, 404, 'Nurse profile not configured or inactive.');

        return $nurse;
    }

    private function resolveBloodBankNurse(): Nurse
    {
        $nurse = $this->resolveNurseProfile();

        if (strcasecmp((string) $nurse->department?->dept_name, 'Blood Bank') !== 0) {
            throw new HttpResponseException(response()->json([
                'message' => 'Blood Bank donor screening is available only to nurses assigned to the Blood Bank department.',
            ], 403));
        }

        return $nurse;
    }

    private function ensureAdmissionAccessible(Nurse $nurse, Admission $admission): void
    {
        if ((int) $admission->department_id !== (int) $nurse->department_id) {
            throw new HttpResponseException(response()->json([
                'message' => 'Forbidden: this admission is outside your department.',
            ], 403));
        }
    }

    private function latestVitalsByAdmission(array $admissionIds): array
    {
        if (empty($admissionIds)) {
            return [];
        }

        return NurseVitalSignLog::query()
            ->whereIn('admission_id', $admissionIds)
            ->with('nurse.user:id,full_name,name,email')
            ->orderByDesc('measured_at')
            ->orderByDesc('id')
            ->get()
            ->groupBy('admission_id')
            ->map(fn ($group) => $group->first())
            ->all();
    }

    private function nursePayload(Nurse $nurse): array
    {
        return [
            'nurse_id' => $nurse->nurse_id,
            'full_name' => $nurse->user?->full_name ?? $nurse->user?->name,
            'email' => $nurse->user?->email,
            'department_id' => $nurse->department_id,
            'department' => $nurse->department?->dept_name,
            'ward_assignment_note' => $nurse->ward_assignment_note,
            'is_active' => (bool) $nurse->is_active,
        ];
    }

    private function admissionPayload(Admission $admission, ?NurseVitalSignLog $latestVital): array
    {
        $activeAssignment = $admission->bedAssignments->first();
        $bed = $activeAssignment?->bed;

        return [
            'id' => $admission->id,
            'patient_user_id' => $admission->patient_user_id,
            'patient_name' => $admission->patient?->full_name ?? $admission->patient?->name,
            'patient_email' => $admission->patient?->email,
            'blood_group' => $admission->patient?->patientProfile?->blood_group,
            'department_id' => $admission->department_id,
            'department' => $admission->department?->dept_name,
            'diagnosis' => $admission->diagnosis,
            'care_level_requested' => $admission->care_level_requested,
            'care_level_assigned' => $admission->care_level_assigned,
            'status' => $admission->status,
            'admit_date' => optional($admission->admit_date)->toISOString(),
            'discharge_date' => optional($admission->discharge_date)->toISOString(),
            'active_bed_assignment' => $activeAssignment ? [
                'assignment_id' => $activeAssignment->id,
                'bed_id' => $bed?->id,
                'bed_code' => $bed?->bed_code,
                'bed_status' => $bed?->status,
                'unit_type' => $bed?->careUnit?->unit_type,
                'unit_name' => $bed?->careUnit?->unit_name,
                'floor' => $bed?->careUnit?->floor,
                'assigned_at' => optional($activeAssignment->assigned_at)->toISOString(),
            ] : null,
            'latest_vital_sign' => $latestVital ? $this->vitalPayload($latestVital) : null,
        ];
    }

    private function vitalPayload(NurseVitalSignLog $log): array
    {
        return [
            'id' => $log->id,
            'admission_id' => $log->admission_id,
            'patient_id' => $log->patient_id,
            'nurse_id' => $log->nurse_id,
            'nurse_name' => $log->nurse?->user?->full_name ?? $log->nurse?->user?->name,
            'measured_at' => optional($log->measured_at)->toISOString(),
            'temperature_c' => $log->temperature_c !== null ? (float) $log->temperature_c : null,
            'pulse_bpm' => $log->pulse_bpm,
            'systolic_bp' => $log->systolic_bp,
            'diastolic_bp' => $log->diastolic_bp,
            'respiration_rate' => $log->respiration_rate,
            'spo2_percent' => $log->spo2_percent,
            'note' => $log->note,
        ];
    }

    private function donorPayload(DonorProfile $profile): array
    {
        $latestCheck = $profile->healthChecks->first();

        return [
            'donor_id' => $profile->donor_id,
            'full_name' => $profile->donor?->full_name ?? $profile->donor?->name,
            'email' => $profile->donor?->email,
            'blood_group' => $profile->blood_group,
            'is_eligible' => (bool) $profile->is_eligible,
            'last_donation_date' => optional($profile->last_donation_date)->toISOString(),
            'latest_health_check' => $latestCheck ? $this->donorHealthPayload($latestCheck) : null,
        ];
    }

    private function donorHealthPayload(DonorHealthCheck $check): array
    {
        return [
            'id' => $check->id,
            'donor_id' => $check->donor_id,
            'check_datetime' => optional($check->check_datetime)->toISOString(),
            'weight_kg' => $check->weight_kg !== null ? (float) $check->weight_kg : null,
            'temperature_c' => $check->temperature_c !== null ? (float) $check->temperature_c : null,
            'hemoglobin' => $check->hemoglobin !== null ? (float) $check->hemoglobin : null,
            'notes' => $check->notes,
            'checked_by_user_id' => $check->checked_by_user_id,
            'checked_by_name' => $check->checkedBy?->full_name ?? $check->checkedBy?->name,
        ];
    }

    private function evaluateDonorEligibility(float $weightKg, float $temperatureC, ?float $hemoglobin = null): array
    {
        if ($weightKg < 45) {
            return ['is_eligible' => false, 'reason' => 'Weight below minimum donation threshold.'];
        }

        if ($temperatureC < 36.0 || $temperatureC > 37.8) {
            return ['is_eligible' => false, 'reason' => 'Temperature is outside donation-safe range.'];
        }

        if ($hemoglobin !== null && $hemoglobin < 12.5) {
            return ['is_eligible' => false, 'reason' => 'Hemoglobin below donation-safe threshold.'];
        }

        return ['is_eligible' => true, 'reason' => 'Eligible based on latest Blood Bank nurse screening.'];
    }
}
