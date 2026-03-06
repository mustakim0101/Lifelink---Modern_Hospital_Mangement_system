<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'fullName' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $name = $validated['name'] ?? $validated['fullName'] ?? null;

        if (! $name) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => [
                    'name' => ['The name or fullName field is required.'],
                ],
            ], 422);
        }

        $user = User::query()->create([
            'name' => $name,
            'full_name' => $name,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $this->assignRole($user, 'Patient');

        $token = auth('api')->login($user);

        return $this->tokenResponse($token, $user, 201, 'Registered');
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = auth('api')->user();

        if ($user->isFrozen()) {
            auth('api')->logout();

            return response()->json([
                'message' => 'Account is frozen. Contact admin.',
            ], 403);
        }

        return $this->tokenResponse($token, $user, 200, 'Logged in');
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'user' => auth('api')->user(),
        ]);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout();

        return response()->json([
            'message' => 'Logged out',
        ]);
    }

    public function refresh(): JsonResponse
    {
        $token = auth('api')->refresh();

        return $this->tokenResponse($token, auth('api')->user(), 200, 'Token refreshed');
    }

    public function createAdmin(Request $request): JsonResponse
    {
        if (! app()->environment(['local', 'development'])) {
            return response()->json([
                'message' => 'Not allowed in this environment',
            ], 403);
        }

        $validated = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'fullName' => ['required', 'string', 'max:255'],
        ]);

        $user = User::query()->create([
            'name' => $validated['fullName'],
            'full_name' => $validated['fullName'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $this->assignRole($user, 'Admin', $user->id);

        $token = auth('api')->login($user);

        return $this->tokenResponse($token, $user, 201, 'Admin bootstrap user created');
    }

    private function tokenResponse(string $token, User $user, int $statusCode, string $message): JsonResponse
    {
        return response()->json([
            'message' => $message,
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
            'user' => [
                'id' => $user->id,
                'email' => $user->email,
                'fullName' => $user->full_name ?? $user->name,
                'account_status' => $user->account_status,
                'roles' => $user->roles()->pluck('role_name')->values(),
            ],
            'latestApplication' => $this->latestApplicationPayload($user->id),
        ], $statusCode);
    }

    private function assignRole(User $user, string $roleName, ?int $assignedByUserId = null): void
    {
        $role = Role::query()->firstOrCreate(
            ['role_name' => $roleName],
            ['description' => $roleName.' role']
        );

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'assigned_at' => now(),
                'assigned_by_user_id' => $assignedByUserId,
            ],
        ]);
    }

    private function latestApplicationPayload(int $userId): ?array
    {
        $latest = JobApplication::query()
            ->with(['appliedRole', 'department'])
            ->where('user_id', $userId)
            ->orderByDesc('applied_at')
            ->first();

        if (! $latest) {
            return null;
        }

        return [
            'id' => $latest->id,
            'status' => $latest->status,
            'applied_at' => optional($latest->applied_at)->toISOString(),
            'applied_role' => $latest->appliedRole?->role_name,
            'applied_department' => $latest->department?->dept_name,
        ];
    }
}
