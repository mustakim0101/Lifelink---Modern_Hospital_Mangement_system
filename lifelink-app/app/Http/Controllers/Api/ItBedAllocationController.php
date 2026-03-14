<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Bed;
use App\Models\BedAssignment;
use App\Models\Department;
use App\Models\DepartmentAdmin;
use App\Models\Doctor;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItBedAllocationController extends Controller
{
    private const CARE_LEVELS = ['Ward', 'ICU', 'NICU', 'CCU'];
    private const ADMISSION_STATUS = ['Admitted', 'Discharged', 'Transferred', 'Cancelled'];

    public function myDepartments(): JsonResponse
    {
        $user = auth('api')->user();

        if ($user->hasRole('Admin')) {
            $departments = Department::query()
                ->where('is_active', true)
                ->orderBy('dept_name')
                ->get(['id', 'dept_name']);

            return response()->json([
                'scope' => 'all',
                'departments' => $departments,
            ]);
        }

        $departments = Department::query()
            ->join('department_admins as da', 'da.department_id', '=', 'departments.id')
            ->where('da.user_id', $user->id)
            ->where('departments.is_active', true)
            ->orderBy('departments.dept_name')
            ->get(['departments.id', 'departments.dept_name']);

        return response()->json([
            'scope' => 'assigned',
            'departments' => $departments,
        ]);
    }

    public function assignDepartmentToItWorker(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'userId' => ['required', 'integer', 'exists:users,id'],
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
        ]);

        $user = User::query()->findOrFail($validated['userId']);

        if (! $user->hasRole('ITWorker')) {
            return response()->json([
                'message' => 'Target user must have ITWorker role first.',
            ], 422);
        }

        $record = DepartmentAdmin::query()->firstOrCreate([
            'user_id' => $validated['userId'],
            'department_id' => $validated['departmentId'],
        ], [
            'assigned_at' => now(),
        ]);

        return response()->json([
            'message' => $record->wasRecentlyCreated ? 'Department assigned to IT worker' : 'Assignment already exists',
            'assignment' => [
                'user_id' => $record->user_id,
                'department_id' => $record->department_id,
                'assigned_at' => optional($record->assigned_at)->toISOString(),
            ],
        ]);
    }

    public function createAdmission(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patientUserId' => ['required', 'integer', 'exists:users,id'],
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
            'admittedByDoctorId' => ['nullable', 'integer', 'exists:users,id'],
            'diagnosis' => ['required', 'string', 'max:255'],
            'careLevelRequested' => ['required', 'string', Rule::in(self::CARE_LEVELS)],
            'notes' => ['nullable', 'string'],
        ]);

        $actor = auth('api')->user();
        $this->ensureDepartmentAccessible($actor, $validated['departmentId']);

        $patient = User::query()->findOrFail($validated['patientUserId']);
        if (! $patient->hasRole('Patient')) {
            return response()->json([
                'message' => 'Selected user is not a patient.',
            ], 422);
        }

        $doctorId = null;
        if (! empty($validated['admittedByDoctorId'])) {
            $doctorProfile = Doctor::query()
                ->where('doctor_id', $validated['admittedByDoctorId'])
                ->where('department_id', $validated['departmentId'])
                ->where('is_active', true)
                ->first();

            if (! $doctorProfile) {
                return response()->json([
                    'message' => 'Selected doctor is not active in this department.',
                ], 422);
            }

            $doctorId = (int) $doctorProfile->doctor_id;
        }

        $admission = Admission::query()->create([
            'patient_user_id' => $validated['patientUserId'],
            'department_id' => $validated['departmentId'],
            'admitted_by_doctor_id' => $doctorId,
            'diagnosis' => $validated['diagnosis'],
            'care_level_requested' => $validated['careLevelRequested'],
            'status' => 'Admitted',
            'admit_date' => now(),
            'notes' => $validated['notes'] ?? null,
        ]);

        $admission->load(['patient:id,full_name,name,email', 'department:id,dept_name']);

        return response()->json([
            'message' => 'Admission created',
            'admission' => $this->admissionPayload($admission),
        ], 201);
    }

    public function admissions(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'status' => ['nullable', 'string', Rule::in(self::ADMISSION_STATUS)],
            'careLevelRequested' => ['nullable', 'string', Rule::in(self::CARE_LEVELS)],
        ]);

        $actor = auth('api')->user();
        $query = Admission::query()
            ->with([
                'patient:id,full_name,name,email',
                'department:id,dept_name',
                'bedAssignments' => function ($q) {
                    $q->whereNull('released_at')->with(['bed:id,care_unit_id,bed_code,status', 'bed.careUnit:id,department_id,unit_type,unit_name']);
                },
            ])
            ->orderByDesc('id');

        if ($actor->hasRole('ITWorker') && ! $actor->hasRole('Admin')) {
            $deptIds = $this->accessibleDepartmentIds($actor);
            $query->whereIn('department_id', $deptIds);
        }

        if (isset($validated['departmentId'])) {
            $this->ensureDepartmentAccessible($actor, $validated['departmentId']);
            $query->where('department_id', $validated['departmentId']);
        }

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (isset($validated['careLevelRequested'])) {
            $query->where('care_level_requested', $validated['careLevelRequested']);
        }

        return response()->json([
            'admissions' => $query->get()->map(fn (Admission $admission) => $this->admissionPayload($admission)),
        ]);
    }

    public function doctors(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'q' => ['nullable', 'string', 'max:120'],
        ]);

        $actor = auth('api')->user();
        $query = Doctor::query()
            ->with(['user:id,full_name,name,email', 'department:id,dept_name'])
            ->where('is_active', true)
            ->orderBy('doctor_id');

        if ($actor->hasRole('ITWorker') && ! $actor->hasRole('Admin')) {
            $query->whereIn('department_id', $this->accessibleDepartmentIds($actor));
        }

        if (isset($validated['departmentId'])) {
            $this->ensureDepartmentAccessible($actor, $validated['departmentId']);
            $query->where('department_id', $validated['departmentId']);
        }

        if (! empty($validated['q'])) {
            $term = trim($validated['q']);
            $query->whereHas('user', function ($userQuery) use ($term) {
                $userQuery->where('full_name', 'like', '%'.$term.'%')
                    ->orWhere('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%');
            });
        }

        return response()->json([
            'doctors' => $query->get()->map(fn (Doctor $doctor) => [
                'doctor_id' => $doctor->doctor_id,
                'full_name' => $doctor->user?->full_name ?? $doctor->user?->name,
                'email' => $doctor->user?->email,
                'department_id' => $doctor->department_id,
                'department' => $doctor->department?->dept_name,
            ]),
        ]);
    }

    public function patients(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:120'],
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
        ]);

        $actor = auth('api')->user();
        if (isset($validated['departmentId']) && ! $actor->hasRole('Admin')) {
            $this->ensureDepartmentAccessible($actor, $validated['departmentId']);
        }

        $query = User::query()
            ->with('patientProfile:patient_id,blood_group')
            ->whereHas('roles', fn ($roleQuery) => $roleQuery->where('role_name', 'Patient'))
            ->orderBy('id');

        if (! empty($validated['q'])) {
            $term = trim($validated['q']);
            $query->where(function ($userQuery) use ($term) {
                $userQuery->where('full_name', 'like', '%'.$term.'%')
                    ->orWhere('name', 'like', '%'.$term.'%')
                    ->orWhere('email', 'like', '%'.$term.'%');
            });
        }

        if (isset($validated['departmentId'])) {
            $query->whereHas('admissions', function ($admissionQuery) use ($validated) {
                $admissionQuery->where('department_id', $validated['departmentId']);
            });
        }

        return response()->json([
            'patients' => $query->limit(100)->get()->map(fn (User $user) => [
                'patient_user_id' => $user->id,
                'full_name' => $user->full_name ?? $user->name,
                'email' => $user->email,
                'blood_group' => $user->patientProfile?->blood_group,
            ]),
        ]);
    }

    public function availableBeds(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
            'unitType' => ['nullable', 'string', Rule::in(self::CARE_LEVELS)],
        ]);

        $actor = auth('api')->user();
        $this->ensureDepartmentAccessible($actor, $validated['departmentId']);

        $query = Bed::query()
            ->with(['careUnit:id,department_id,unit_type,unit_name,floor', 'careUnit.department:id,dept_name'])
            ->where('status', 'Available')
            ->where('is_active', true)
            ->whereHas('careUnit', function ($q) use ($validated) {
                $q->where('department_id', $validated['departmentId']);
                if (! empty($validated['unitType'])) {
                    $q->where('unit_type', $validated['unitType']);
                }
            })
            ->orderBy('id');

        return response()->json([
            'beds' => $query->get()->map(fn (Bed $bed) => [
                'id' => $bed->id,
                'bed_code' => $bed->bed_code,
                'status' => $bed->status,
                'department_id' => $bed->careUnit?->department_id,
                'department' => $bed->careUnit?->department?->dept_name,
                'unit_type' => $bed->careUnit?->unit_type,
                'unit_name' => $bed->careUnit?->unit_name,
                'floor' => $bed->careUnit?->floor,
            ]),
        ]);
    }

    public function assignBed(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'admissionId' => ['required', 'integer', 'exists:admissions,id'],
            'bedId' => ['required', 'integer', 'exists:beds,id'],
        ]);

        $actor = auth('api')->user();

        $result = DB::transaction(function () use ($validated, $actor) {
            $admission = Admission::query()
                ->with(['department:id,dept_name', 'bedAssignments' => fn ($q) => $q->whereNull('released_at')])
                ->lockForUpdate()
                ->findOrFail($validated['admissionId']);

            $bed = Bed::query()
                ->with('careUnit:id,department_id,unit_type')
                ->lockForUpdate()
                ->findOrFail($validated['bedId']);

            $this->ensureDepartmentAccessible($actor, $admission->department_id);

            if ($admission->status !== 'Admitted') {
                return response()->json([
                    'message' => 'Bed can be assigned only to admitted patients.',
                ], 409);
            }

            if ($admission->bedAssignments->isNotEmpty()) {
                return response()->json([
                    'message' => 'Admission already has an active bed assignment.',
                ], 409);
            }

            if (! $bed->careUnit || (int) $bed->careUnit->department_id !== (int) $admission->department_id) {
                return response()->json([
                    'message' => 'Selected bed does not belong to admission department.',
                ], 422);
            }

            if ($bed->status !== 'Available') {
                return response()->json([
                    'message' => 'Selected bed is not available.',
                ], 409);
            }

            BedAssignment::query()->create([
                'admission_id' => $admission->id,
                'bed_id' => $bed->id,
                'assigned_by_user_id' => $actor->id,
                'assigned_at' => now(),
            ]);

            $bed->update(['status' => 'Occupied']);
            $admission->update(['care_level_assigned' => $bed->careUnit->unit_type]);
            $admission->refresh();
            $admission->load([
                'patient:id,full_name,name,email',
                'department:id,dept_name',
                'bedAssignments' => fn ($q) => $q->whereNull('released_at')->with(['bed:id,care_unit_id,bed_code,status', 'bed.careUnit:id,department_id,unit_type,unit_name']),
            ]);

            return response()->json([
                'message' => 'Bed assigned',
                'admission' => $this->admissionPayload($admission),
            ]);
        });

        return $result;
    }

    public function dischargeAdmission(Request $request, Admission $admission): JsonResponse
    {
        $validated = $request->validate([
            'releaseReason' => ['nullable', 'string', 'max:40'],
        ]);

        $actor = auth('api')->user();

        $result = DB::transaction(function () use ($admission, $actor, $validated) {
            $admission = Admission::query()
                ->with(['department:id,dept_name', 'patient:id,full_name,name,email'])
                ->lockForUpdate()
                ->findOrFail($admission->id);

            $this->ensureDepartmentAccessible($actor, $admission->department_id);

            if ($admission->status !== 'Admitted') {
                return response()->json([
                    'message' => 'Admission is not in Admitted state.',
                ], 409);
            }

            $activeAssignment = BedAssignment::query()
                ->where('admission_id', $admission->id)
                ->whereNull('released_at')
                ->lockForUpdate()
                ->first();

            if ($activeAssignment) {
                $bed = Bed::query()->lockForUpdate()->find($activeAssignment->bed_id);
                if ($bed) {
                    $bed->update(['status' => 'Available']);
                }

                $activeAssignment->update([
                    'released_at' => now(),
                    'released_by_user_id' => $actor->id,
                    'release_reason' => $validated['releaseReason'] ?? 'Discharge',
                ]);
            }

            $admission->update([
                'status' => 'Discharged',
                'discharge_date' => now(),
            ]);

            $admission->refresh();
            $admission->load([
                'department:id,dept_name',
                'patient:id,full_name,name,email',
                'bedAssignments' => fn ($q) => $q->whereNull('released_at')->with(['bed:id,care_unit_id,bed_code,status', 'bed.careUnit:id,department_id,unit_type,unit_name']),
            ]);

            return response()->json([
                'message' => 'Admission discharged and bed released',
                'admission' => $this->admissionPayload($admission),
                'released_bed' => $activeAssignment ? [
                    'assignment_id' => $activeAssignment->id,
                    'bed_id' => $activeAssignment->bed_id,
                    'released_at' => optional($activeAssignment->released_at)->toISOString(),
                ] : null,
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

    private function admissionPayload(Admission $admission): array
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
            'admitted_by_doctor_id' => $admission->admitted_by_doctor_id,
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
                'unit_type' => $bed?->careUnit?->unit_type,
                'unit_name' => $bed?->careUnit?->unit_name,
                'assigned_by_user_id' => $activeAssignment->assigned_by_user_id,
                'assigned_at' => optional($activeAssignment->assigned_at)->toISOString(),
            ] : null,
        ];
    }
}
