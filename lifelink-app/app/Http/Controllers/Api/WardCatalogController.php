<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\CareUnit;
use App\Models\Department;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class WardCatalogController extends Controller
{
    private const UNIT_TYPES = ['Ward', 'ICU', 'NICU', 'CCU'];
    private const BED_STATUSES = ['Available', 'Occupied', 'Maintenance', 'Reserved'];

    public function departments(): JsonResponse
    {
        $departments = Department::query()
            ->where('is_active', true)
            ->orderBy('dept_name')
            ->get(['id', 'dept_name', 'is_active']);

        return response()->json([
            'departments' => $departments,
        ]);
    }

    public function careUnits(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'unitType' => ['nullable', 'string', Rule::in(self::UNIT_TYPES)],
            'isActive' => ['nullable', 'boolean'],
        ]);

        $query = CareUnit::query()
            ->with('department:id,dept_name')
            ->orderByDesc('id');

        if (isset($validated['departmentId'])) {
            $query->where('department_id', $validated['departmentId']);
        }

        if (isset($validated['unitType'])) {
            $query->where('unit_type', $validated['unitType']);
        }

        if (array_key_exists('isActive', $validated)) {
            $query->where('is_active', $validated['isActive']);
        }

        return response()->json([
            'care_units' => $query->get()->map(fn (CareUnit $unit) => $this->careUnitPayload($unit)),
        ]);
    }

    public function storeCareUnit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departmentId' => ['required', 'integer', 'exists:departments,id'],
            'unitType' => ['required', 'string', Rule::in(self::UNIT_TYPES)],
            'unitName' => ['nullable', 'string', 'max:120'],
            'floor' => ['nullable', 'integer', 'min:0', 'max:999'],
            'isActive' => ['nullable', 'boolean'],
        ]);

        $careUnit = CareUnit::query()->create([
            'department_id' => $validated['departmentId'],
            'unit_type' => $validated['unitType'],
            'unit_name' => $validated['unitName'] ?? null,
            'floor' => $validated['floor'] ?? null,
            'is_active' => $validated['isActive'] ?? true,
        ])->load('department:id,dept_name');

        return response()->json([
            'message' => 'Care unit created',
            'care_unit' => $this->careUnitPayload($careUnit),
        ], 201);
    }

    public function beds(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'careUnitId' => ['nullable', 'integer', 'exists:care_units,id'],
            'unitType' => ['nullable', 'string', Rule::in(self::UNIT_TYPES)],
            'status' => ['nullable', 'string', Rule::in(self::BED_STATUSES)],
            'isActive' => ['nullable', 'boolean'],
        ]);

        $query = Bed::query()
            ->with(['careUnit:id,department_id,unit_type,unit_name,floor,is_active', 'careUnit.department:id,dept_name'])
            ->orderByDesc('id');

        if (isset($validated['departmentId'])) {
            $query->whereHas('careUnit', function ($q) use ($validated) {
                $q->where('department_id', $validated['departmentId']);
            });
        }

        if (isset($validated['careUnitId'])) {
            $query->where('care_unit_id', $validated['careUnitId']);
        }

        if (isset($validated['unitType'])) {
            $query->whereHas('careUnit', function ($q) use ($validated) {
                $q->where('unit_type', $validated['unitType']);
            });
        }

        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (array_key_exists('isActive', $validated)) {
            $query->where('is_active', $validated['isActive']);
        }

        return response()->json([
            'beds' => $query->get()->map(fn (Bed $bed) => $this->bedPayload($bed)),
        ]);
    }

    public function storeBed(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'careUnitId' => ['required', 'integer', 'exists:care_units,id'],
            'bedCode' => ['required', 'string', 'max:50'],
            'status' => ['nullable', 'string', Rule::in(self::BED_STATUSES)],
            'isActive' => ['nullable', 'boolean'],
        ]);

        $exists = Bed::query()
            ->where('care_unit_id', $validated['careUnitId'])
            ->whereRaw('LOWER(bed_code) = ?', [mb_strtolower($validated['bedCode'])])
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => 'Bed code already exists in this care unit.',
            ], 409);
        }

        $bed = Bed::query()->create([
            'care_unit_id' => $validated['careUnitId'],
            'bed_code' => $validated['bedCode'],
            'status' => $validated['status'] ?? 'Available',
            'is_active' => $validated['isActive'] ?? true,
        ])->load(['careUnit:id,department_id,unit_type,unit_name,floor,is_active', 'careUnit.department:id,dept_name']);

        return response()->json([
            'message' => 'Bed created',
            'bed' => $this->bedPayload($bed),
        ], 201);
    }

    public function bedSummary(): JsonResponse
    {
        $summary = DB::table('beds as b')
            ->join('care_units as cu', 'cu.id', '=', 'b.care_unit_id')
            ->join('departments as d', 'd.id', '=', 'cu.department_id')
            ->select([
                'd.id as department_id',
                'd.dept_name as department',
                'cu.unit_type',
                'b.status',
                DB::raw('COUNT(*) as total'),
            ])
            ->groupBy('d.id', 'd.dept_name', 'cu.unit_type', 'b.status')
            ->orderBy('d.dept_name')
            ->orderBy('cu.unit_type')
            ->orderBy('b.status')
            ->get();

        return response()->json([
            'summary' => $summary,
        ]);
    }

    private function careUnitPayload(CareUnit $unit): array
    {
        return [
            'id' => $unit->id,
            'department_id' => $unit->department_id,
            'department' => $unit->department?->dept_name,
            'unit_type' => $unit->unit_type,
            'unit_name' => $unit->unit_name,
            'floor' => $unit->floor,
            'is_active' => (bool) $unit->is_active,
            'created_at' => optional($unit->created_at)->toISOString(),
            'updated_at' => optional($unit->updated_at)->toISOString(),
        ];
    }

    private function bedPayload(Bed $bed): array
    {
        return [
            'id' => $bed->id,
            'care_unit_id' => $bed->care_unit_id,
            'bed_code' => $bed->bed_code,
            'status' => $bed->status,
            'is_active' => (bool) $bed->is_active,
            'department_id' => $bed->careUnit?->department_id,
            'department' => $bed->careUnit?->department?->dept_name,
            'unit_type' => $bed->careUnit?->unit_type,
            'unit_name' => $bed->careUnit?->unit_name,
            'floor' => $bed->careUnit?->floor,
            'created_at' => optional($bed->created_at)->toISOString(),
            'updated_at' => optional($bed->updated_at)->toISOString(),
        ];
    }
}

