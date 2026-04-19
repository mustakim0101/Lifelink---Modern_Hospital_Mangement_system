<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountControlController extends Controller
{
    public function searchUsers(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:120'],
            'role' => ['nullable', 'string', 'max:80'],
            'department' => ['nullable', 'string', 'max:120'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $nameFilter = trim((string) ($validated['name'] ?? ''));
        $roleFilter = trim((string) ($validated['role'] ?? ''));
        $departmentFilter = trim((string) ($validated['department'] ?? ''));
        $limit = (int) ($validated['limit'] ?? 20);

        $query = User::query()
            ->select(['users.id', 'users.name', 'users.full_name', 'users.email', 'users.account_status'])
            ->with([
                'roles:id,role_name',
                'doctorProfile:doctor_id,department_id',
                'doctorProfile.department:id,dept_name',
                'nurseProfile:nurse_id,department_id',
                'nurseProfile.department:id,dept_name',
                'departmentAdminScopes:user_id,department_id',
                'departmentAdminScopes.department:id,dept_name',
            ]);

        if ($nameFilter !== '') {
            $query->where(function ($nameQuery) use ($nameFilter) {
                $likeName = '%' . $nameFilter . '%';
                $nameQuery
                    ->where('users.full_name', 'like', $likeName)
                    ->orWhere('users.name', 'like', $likeName)
                    ->orWhere('users.email', 'like', $likeName);
            });
        }

        if ($roleFilter !== '') {
            $query->whereHas('roles', function ($roleQuery) use ($roleFilter) {
                $roleQuery->where('role_name', 'like', '%' . $roleFilter . '%');
            });
        }

        if ($departmentFilter !== '') {
            $query->where(function ($departmentQuery) use ($departmentFilter) {
                $likeDepartment = '%' . $departmentFilter . '%';
                $departmentQuery
                    ->whereHas('doctorProfile.department', fn ($q) => $q->where('dept_name', 'like', $likeDepartment))
                    ->orWhereHas('nurseProfile.department', fn ($q) => $q->where('dept_name', 'like', $likeDepartment))
                    ->orWhereHas('departmentAdminScopes.department', fn ($q) => $q->where('dept_name', 'like', $likeDepartment));
            });
        }

        $users = $query
            ->orderBy('users.id', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'filters' => [
                'name' => $nameFilter,
                'role' => $roleFilter,
                'department' => $departmentFilter,
                'limit' => $limit,
            ],
            'users' => $users->map(function (User $user) {
                $departmentNames = collect([
                    optional($user->doctorProfile?->department)->dept_name,
                    optional($user->nurseProfile?->department)->dept_name,
                    ...$user->departmentAdminScopes->map(fn ($scope) => optional($scope->department)->dept_name)->all(),
                ])
                    ->filter()
                    ->unique()
                    ->values();

                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'full_name' => $user->full_name ?? $user->name,
                    'account_status' => $user->account_status,
                    'roles' => $user->roles->pluck('role_name')->filter()->unique()->values(),
                    'departments' => $departmentNames,
                ];
            })->values(),
        ]);
    }

    public function freeze(Request $request, User $user): JsonResponse
    {
        $admin = auth('api')->user();

        if ($admin->id === $user->id) {
            return response()->json([
                'message' => 'Admin cannot freeze own account.',
            ], 422);
        }

        $user->update([
            'account_status' => 'Frozen',
            'frozen_at' => now(),
            'frozen_by_user_id' => $admin->id,
        ]);

        return response()->json([
            'message' => 'User account frozen',
            'user' => $this->statusPayload($user->fresh()),
        ]);
    }

    public function unfreeze(User $user): JsonResponse
    {
        $user->update([
            'account_status' => 'Active',
            'frozen_at' => null,
            'frozen_by_user_id' => null,
        ]);

        return response()->json([
            'message' => 'User account unfrozen',
            'user' => $this->statusPayload($user->fresh()),
        ]);
    }

    public function status(User $user): JsonResponse
    {
        return response()->json([
            'user' => $this->statusPayload($user),
        ]);
    }

    private function statusPayload(User $user): array
    {
        return [
            'id' => $user->id,
            'email' => $user->email,
            'fullName' => $user->full_name ?? $user->name,
            'account_status' => $user->account_status,
            'frozen_at' => optional($user->frozen_at)->toISOString(),
            'frozen_by_user_id' => $user->frozen_by_user_id,
            'roles' => $user->roles()->pluck('role_name')->values(),
        ];
    }
}
