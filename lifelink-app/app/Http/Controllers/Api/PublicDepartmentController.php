<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use App\Services\DepartmentDirectoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicDepartmentController extends Controller
{
    public function __construct(private readonly DepartmentDirectoryService $directoryService)
    {
    }

    public function catalog(): JsonResponse
    {
        return response()->json([
            'departments' => $this->directoryService->departmentCatalog(),
        ]);
    }

    public function legacyList(): JsonResponse
    {
        return response()->json([
            'departments' => Department::query()
                ->where('is_active', true)
                ->orderBy('dept_name')
                ->get(['id', 'dept_name']),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $detail = $this->directoryService->departmentDetail($slug);
        if (! $detail) {
            return response()->json([
                'message' => 'Department not found.',
            ], 404);
        }

        return response()->json($detail);
    }

    public function availability(Request $request, string $slug): JsonResponse
    {
        $validated = $request->validate([
            'doctorId' => ['nullable', 'integer', 'exists:doctors,doctor_id'],
            'startDate' => ['nullable', 'date_format:Y-m-d'],
            'days' => ['nullable', 'integer', 'min:1', 'max:14'],
        ]);

        $department = $this->directoryService->findPublicDepartmentBySlug($slug);
        if (! $department) {
            return response()->json([
                'message' => 'Department not found.',
            ], 404);
        }

        $doctorId = isset($validated['doctorId']) ? (int) $validated['doctorId'] : null;
        if ($doctorId !== null) {
            $isDoctorInDepartment = Doctor::query()
                ->where('doctor_id', $doctorId)
                ->where('department_id', $department->id)
                ->where('is_active', true)
                ->exists();

            if (! $isDoctorInDepartment) {
                return response()->json([
                    'message' => 'Selected doctor is not active in this department.',
                ], 422);
            }
        }

        return response()->json(
            $this->directoryService->departmentAvailability(
                $department,
                $doctorId,
                $validated['startDate'] ?? null,
                (int) ($validated['days'] ?? 7)
            )
        );
    }
}
