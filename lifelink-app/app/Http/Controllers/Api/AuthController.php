<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\JobApplication;
use App\Models\Patient;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    private const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    private const AUTH_SERVICE_UNAVAILABLE_MESSAGE = 'Authentication service is temporarily unavailable. Please contact admin.';

    public function register(Request $request): JsonResponse
    {
        $timer = microtime(true);
        $marks = [];
        $result = 'success';
        $user = null;

        try {
            $validated = $request->validate([
                'name' => ['nullable', 'string', 'max:255'],
                'fullName' => ['nullable', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
                'bloodGroup' => ['nullable', 'string', Rule::in(self::BLOOD_GROUPS)],
                'emergencyContactName' => ['nullable', 'string', 'max:150'],
                'emergencyContactPhone' => ['nullable', 'string', 'max:30'],
            ]);
            $this->markDuration($marks, 'validated', $timer);

            $name = $validated['name'] ?? $validated['fullName'] ?? null;

            if (! $name) {
                $result = 'missing_name';

                return response()->json([
                    'message' => 'Validation failed',
                    'errors' => [
                        'name' => ['The name or fullName field is required.'],
                    ],
                ], 422);
            }

            $token = DB::transaction(function () use ($name, $validated, &$user, &$marks, $timer) {
                $user = User::query()->create([
                    'name' => $name,
                    'full_name' => $name,
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);
                $this->markDuration($marks, 'created_user', $timer);

                $this->assignRole($user, 'Patient');
                $this->markDuration($marks, 'assigned_role', $timer);

                $user->loadMissing('roles:id,role_name');
                $this->ensurePatientProfile($user, $validated, true);
                $this->markDuration($marks, 'ensured_patient_profile', $timer);

                $token = $this->issueTokenForUser($user);
                $this->markDuration($marks, 'issued_token', $timer);

                return $token;
            });

            return $this->tokenResponse($token, $user, 201, 'Registered');
        } catch (JWTException $e) {
            $result = 'auth_service_unavailable';

            return $this->authServiceUnavailableResponse('register', $e, [
                'email' => $request->input('email'),
            ]);
        } catch (Throwable $e) {
            $result = 'error';
            Log::error('auth.register.failed', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Registration failed. Please try again.',
            ], 500);
        } finally {
            $this->logTiming('auth.register', $timer, $marks, [
                'result' => $result,
                'user_id' => $user?->id,
            ]);
        }
    }

    public function login(Request $request): JsonResponse
    {
        $timer = microtime(true);
        $marks = [];
        $result = 'success';
        $user = null;

        try {
            $credentials = $request->validate([
                'email' => ['required', 'string', 'email'],
                'password' => ['required', 'string'],
            ]);
            $this->markDuration($marks, 'validated', $timer);

            $token = auth('api')->attempt($credentials);
            if (! $token) {
                $result = 'invalid_credentials';

                return response()->json([
                    'message' => 'Invalid credentials',
                ], 401);
            }
            $this->markDuration($marks, 'attempted_auth', $timer);

            $user = auth('api')->user();
            $user->loadMissing('roles:id,role_name');
            $this->markDuration($marks, 'loaded_user_roles', $timer);

            if ($user->isFrozen()) {
                auth('api')->logout();
                $result = 'frozen';

                return response()->json([
                    'message' => 'Account is frozen. Contact admin.',
                ], 403);
            }

            $isPatient = in_array('Patient', $user->roleNames(), true);
            if ($isPatient) {
                $this->ensurePatientProfile($user, [], true);
            }
            $this->markDuration($marks, 'ensured_patient_profile', $timer);

            return $this->tokenResponse($token, $user, 200, 'Logged in');
        } catch (JWTException $e) {
            $result = 'auth_service_unavailable';

            return $this->authServiceUnavailableResponse('login', $e, [
                'email' => $request->input('email'),
            ]);
        } catch (Throwable $e) {
            $result = 'error';
            Log::error('auth.login.failed', [
                'email' => $request->input('email'),
                'error' => $e->getMessage(),
                'exception' => $e,
            ]);

            return response()->json([
                'message' => 'Login failed. Please try again.',
            ], 500);
        } finally {
            $this->logTiming('auth.login', $timer, $marks, [
                'result' => $result,
                'user_id' => $user?->id,
            ]);
        }
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

        try {
            $user = null;
            $token = DB::transaction(function () use (&$user, $validated) {
                $user = User::query()->create([
                    'name' => $validated['fullName'],
                    'full_name' => $validated['fullName'],
                    'email' => $validated['email'],
                    'password' => Hash::make($validated['password']),
                ]);

                $this->assignRole($user, 'Admin', $user->id);
                $user->loadMissing('roles:id,role_name');

                return $this->issueTokenForUser($user);
            });
        } catch (JWTException $e) {
            return $this->authServiceUnavailableResponse('create_admin', $e, [
                'email' => $request->input('email'),
            ]);
        }

        return $this->tokenResponse($token, $user, 201, 'Admin bootstrap user created');
    }

    private function tokenResponse(string $token, User $user, int $statusCode, string $message): JsonResponse
    {
        $roleNames = $user->roleNames();
        $includeLatestApplication = in_array('Applicant', $roleNames, true);

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
                'roles' => $roleNames,
            ],
            'latestApplication' => $includeLatestApplication ? $this->latestApplicationPayload($user->id) : null,
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
            ->select(['id', 'status', 'applied_at', 'applied_role_id', 'applied_department_id'])
            ->with(['appliedRole:id,role_name', 'department:id,dept_name'])
            ->where('user_id', $userId)
            ->orderByDesc('applied_at')
            ->orderByDesc('id')
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

    private function issueTokenForUser(User $user): string
    {
        $token = auth('api')->login($user);
        if (! is_string($token) || $token === '') {
            throw new JWTException('Token issuance failed.');
        }

        return $token;
    }

    private function authServiceUnavailableResponse(string $action, Throwable $exception, array $context = []): JsonResponse
    {
        Log::error('auth.jwt_unavailable', array_merge($context, [
            'action' => $action,
            'error' => $exception->getMessage(),
            'exception' => $exception,
        ]));

        return response()->json([
            'message' => self::AUTH_SERVICE_UNAVAILABLE_MESSAGE,
        ], 503);
    }

    private function ensurePatientProfile(User $user, array $context, ?bool $isPatient = null): void
    {
        $patientRole = $isPatient ?? $user->hasRole('Patient');
        if (! $patientRole) {
            return;
        }

        $patient = Patient::query()->firstOrCreate(
            ['patient_id' => $user->id],
            ['is_active' => true]
        );

        $profileData = [];

        if (array_key_exists('bloodGroup', $context)) {
            $profileData['blood_group'] = $context['bloodGroup'];
        }

        if (array_key_exists('emergencyContactName', $context)) {
            $profileData['emergency_contact_name'] = $context['emergencyContactName'];
        }

        if (array_key_exists('emergencyContactPhone', $context)) {
            $profileData['emergency_contact_phone'] = $context['emergencyContactPhone'];
        }

        if (empty($profileData)) {
            return;
        }

        $patient->fill($profileData)->save();
    }

    private function markDuration(array &$marks, string $step, float $startedAt): void
    {
        if (! config('app.debug')) {
            return;
        }

        $marks[$step] = (int) round((microtime(true) - $startedAt) * 1000);
    }

    private function logTiming(string $label, float $startedAt, array $marks, array $context = []): void
    {
        if (! config('app.debug')) {
            return;
        }

        Log::info('perf.'.$label, array_merge($context, [
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'steps_ms' => $marks,
        ]));
    }
}
