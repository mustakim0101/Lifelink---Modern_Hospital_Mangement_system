<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
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

    private function resolveNurseProfile(): Nurse
    {
        $nurse = Nurse::query()
            ->with(['department:id,dept_name', 'user:id,full_name,name,email'])
            ->find(auth('api')->id());

        abort_unless($nurse && $nurse->is_active, 404, 'Nurse profile not configured or inactive.');

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
}
