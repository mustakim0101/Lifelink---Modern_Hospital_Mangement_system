<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodBank;
use App\Models\BloodDonation;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\DonorAvailability;
use App\Models\DonorHealthCheck;
use App\Models\DonorProfile;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DonorDashboardController extends Controller
{
    private const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    private const BLOOD_COMPONENTS = ['WholeBlood', 'Plasma', 'Platelets', 'RBC'];

    public function dashboard(): JsonResponse
    {
        $profile = $this->resolveDonorProfile();
        $weekStart = now()->startOfWeek(Carbon::MONDAY)->toDateString();

        $availability = DonorAvailability::query()
            ->where('donor_id', $profile->donor_id)
            ->whereDate('week_start_date', $weekStart)
            ->first();

        $latestHealthCheck = DonorHealthCheck::query()
            ->where('donor_id', $profile->donor_id)
            ->orderByDesc('check_datetime')
            ->first();

        $recentDonations = BloodDonation::query()
            ->with('bloodBank:id,bank_name')
            ->where('donor_id', $profile->donor_id)
            ->orderByDesc('donation_datetime')
            ->limit(5)
            ->get();

        $totalDonations = BloodDonation::query()
            ->where('donor_id', $profile->donor_id)
            ->count();

        $totalUnitsDonated = (int) BloodDonation::query()
            ->where('donor_id', $profile->donor_id)
            ->sum('units_donated');

        $pendingGroupRequests = BloodRequest::query()
            ->where('status', 'Pending')
            ->where('blood_group_needed', $profile->blood_group)
            ->count();

        return response()->json([
            'donor' => $this->donorPayload($profile),
            'current_week_availability' => $availability ? $this->availabilityPayload($availability) : null,
            'latest_health_check' => $latestHealthCheck ? $this->healthCheckPayload($latestHealthCheck) : null,
            'stats' => [
                'total_donations' => $totalDonations,
                'total_units_donated' => $totalUnitsDonated,
                'pending_group_requests' => $pendingGroupRequests,
            ],
            'recent_donations' => $recentDonations->map(fn (BloodDonation $donation) => $this->donationPayload($donation)),
        ]);
    }

    public function profile(): JsonResponse
    {
        return response()->json([
            'donor' => $this->donorPayload($this->resolveDonorProfile()),
        ]);
    }

    public function enroll(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bloodGroup' => ['nullable', 'string', Rule::in(self::BLOOD_GROUPS)],
            'notes' => ['nullable', 'string'],
        ]);

        $user = auth('api')->user();
        $this->assignDonorRole($user->id);

        $bloodGroup = $validated['bloodGroup']
            ?? $user->patientProfile?->blood_group
            ?? 'O+';

        $profile = DonorProfile::query()->firstOrCreate(
            ['donor_id' => $user->id],
            [
                'blood_group' => $bloodGroup,
                'is_eligible' => true,
            ]
        );

        $profile->update([
            'blood_group' => $validated['bloodGroup'] ?? $profile->blood_group ?? $bloodGroup,
            'notes' => $validated['notes'] ?? $profile->notes,
        ]);

        $profile->load('donor:id,full_name,name,email', 'donor.roles:id,role_name');

        return response()->json([
            'message' => 'Donor role enabled for current user',
            'donor' => $this->donorPayload($profile),
        ]);
    }

    public function banks(): JsonResponse
    {
        return response()->json([
            'banks' => BloodBank::query()
                ->where('is_active', true)
                ->orderBy('bank_name')
                ->get(['id', 'bank_name', 'location']),
        ]);
    }

    public function availabilities(Request $request): JsonResponse
    {
        $profile = $this->resolveDonorProfile();

        $validated = validator($request->query(), [
            'fromWeek' => ['nullable', 'date'],
            'toWeek' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:52'],
        ])->validate();

        $query = DonorAvailability::query()
            ->where('donor_id', $profile->donor_id)
            ->orderByDesc('week_start_date');

        if (! empty($validated['fromWeek'])) {
            $query->whereDate('week_start_date', '>=', Carbon::parse($validated['fromWeek'])->startOfWeek(Carbon::MONDAY)->toDateString());
        }

        if (! empty($validated['toWeek'])) {
            $query->whereDate('week_start_date', '<=', Carbon::parse($validated['toWeek'])->startOfWeek(Carbon::MONDAY)->toDateString());
        }

        $limit = $validated['limit'] ?? 20;

        return response()->json([
            'availabilities' => $query->limit($limit)->get()->map(fn (DonorAvailability $availability) => $this->availabilityPayload($availability)),
        ]);
    }

    public function upsertAvailability(Request $request): JsonResponse
    {
        $profile = $this->resolveDonorProfile();

        $validated = $request->validate([
            'weekStartDate' => ['nullable', 'date'],
            'isAvailable' => ['required', 'boolean'],
            'maxBagsPossible' => ['nullable', 'integer', 'min:0', 'max:10'],
            'notes' => ['nullable', 'string'],
        ]);

        if ($validated['isAvailable'] && ! array_key_exists('maxBagsPossible', $validated)) {
            return response()->json([
                'message' => 'maxBagsPossible is required when donor is available.',
            ], 422);
        }

        $weekStart = Carbon::parse($validated['weekStartDate'] ?? now())
            ->startOfWeek(Carbon::MONDAY)
            ->toDateString();

        $availability = DonorAvailability::query()->updateOrCreate(
            [
                'donor_id' => $profile->donor_id,
                'week_start_date' => $weekStart,
            ],
            [
                'is_available' => $validated['isAvailable'],
                'max_bags_possible' => $validated['isAvailable']
                    ? ($validated['maxBagsPossible'] ?? 0)
                    : 0,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Donor availability upserted',
            'availability' => $this->availabilityPayload($availability),
        ]);
    }

    public function healthChecks(Request $request): JsonResponse
    {
        $profile = $this->resolveDonorProfile();

        $validated = validator($request->query(), [
            'fromDate' => ['nullable', 'date'],
            'toDate' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ])->validate();

        $query = DonorHealthCheck::query()
            ->where('donor_id', $profile->donor_id)
            ->orderByDesc('check_datetime');

        if (! empty($validated['fromDate'])) {
            $query->where('check_datetime', '>=', Carbon::parse($validated['fromDate'])->startOfDay());
        }

        if (! empty($validated['toDate'])) {
            $query->where('check_datetime', '<=', Carbon::parse($validated['toDate'])->endOfDay());
        }

        $limit = $validated['limit'] ?? 20;

        return response()->json([
            'health_checks' => $query->limit($limit)->get()->map(fn (DonorHealthCheck $check) => $this->healthCheckPayload($check)),
        ]);
    }

    public function logHealthCheck(Request $request): JsonResponse
    {
        $profile = $this->resolveDonorProfile();

        $validated = $request->validate([
            'checkDateTime' => ['nullable', 'date'],
            'weightKg' => ['required', 'numeric', 'min:30', 'max:250'],
            'temperatureC' => ['required', 'numeric', 'min:34', 'max:43'],
            'hemoglobin' => ['nullable', 'numeric', 'min:5', 'max:25'],
            'notes' => ['nullable', 'string'],
        ]);

        $check = DonorHealthCheck::query()->create([
            'donor_id' => $profile->donor_id,
            'check_datetime' => $validated['checkDateTime'] ?? now(),
            'weight_kg' => $validated['weightKg'],
            'temperature_c' => $validated['temperatureC'],
            'hemoglobin' => $validated['hemoglobin'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'checked_by_user_id' => auth('api')->id(),
        ]);

        $isEligible = $this->isEligibleByVitals((float) $validated['weightKg'], (float) $validated['temperatureC']);
        $profile->update(['is_eligible' => $isEligible]);

        return response()->json([
            'message' => 'Donor health check logged',
            'donor_is_eligible' => $isEligible,
            'health_check' => $this->healthCheckPayload($check),
        ], 201);
    }

    public function donations(Request $request): JsonResponse
    {
        $profile = $this->resolveDonorProfile();

        $validated = validator($request->query(), [
            'bankId' => ['nullable', 'integer', 'exists:blood_banks,id'],
            'fromDate' => ['nullable', 'date'],
            'toDate' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ])->validate();

        $query = BloodDonation::query()
            ->with(['bloodBank:id,bank_name', 'linkedRequest:id,status,blood_group_needed,units_required'])
            ->where('donor_id', $profile->donor_id)
            ->orderByDesc('donation_datetime');

        if (! empty($validated['bankId'])) {
            $query->where('blood_bank_id', $validated['bankId']);
        }

        if (! empty($validated['fromDate'])) {
            $query->where('donation_datetime', '>=', Carbon::parse($validated['fromDate'])->startOfDay());
        }

        if (! empty($validated['toDate'])) {
            $query->where('donation_datetime', '<=', Carbon::parse($validated['toDate'])->endOfDay());
        }

        $limit = $validated['limit'] ?? 30;

        return response()->json([
            'donations' => $query->limit($limit)->get()->map(fn (BloodDonation $donation) => $this->donationPayload($donation)),
        ]);
    }

    public function logDonation(Request $request): JsonResponse
    {
        $profile = $this->resolveDonorProfile();

        $validated = $request->validate([
            'bankId' => ['required', 'integer', 'exists:blood_banks,id'],
            'donationDateTime' => ['nullable', 'date'],
            'bloodGroup' => ['nullable', 'string', Rule::in(self::BLOOD_GROUPS)],
            'componentType' => ['nullable', 'string', Rule::in(self::BLOOD_COMPONENTS)],
            'unitsDonated' => ['required', 'integer', 'min:1', 'max:5'],
            'linkedRequestId' => ['nullable', 'integer', 'exists:blood_requests,id'],
            'healthCheckId' => ['nullable', 'integer', 'exists:donor_health_checks,id'],
            'weightKg' => ['nullable', 'numeric', 'min:30', 'max:250', 'required_with:temperatureC'],
            'temperatureC' => ['nullable', 'numeric', 'min:34', 'max:43', 'required_with:weightKg'],
            'hemoglobin' => ['nullable', 'numeric', 'min:5', 'max:25'],
            'notes' => ['nullable', 'string'],
        ]);

        $donationDateTime = $validated['donationDateTime'] ?? now();
        $healthCheckId = null;
        $eligibleStatus = (bool) $profile->is_eligible;

        if (! empty($validated['healthCheckId'])) {
            $healthCheck = DonorHealthCheck::query()->findOrFail($validated['healthCheckId']);

            if ((int) $healthCheck->donor_id !== (int) $profile->donor_id) {
                return response()->json([
                    'message' => 'Selected health check does not belong to current donor.',
                ], 403);
            }

            $healthCheckId = $healthCheck->id;
            $eligibleStatus = $this->isEligibleByVitals((float) $healthCheck->weight_kg, (float) $healthCheck->temperature_c);
        } elseif (array_key_exists('weightKg', $validated) || array_key_exists('temperatureC', $validated)) {
            $weight = (float) ($validated['weightKg'] ?? 0);
            $temp = (float) ($validated['temperatureC'] ?? 0);
            $eligibleStatus = $this->isEligibleByVitals($weight, $temp);

            $healthCheck = DonorHealthCheck::query()->create([
                'donor_id' => $profile->donor_id,
                'check_datetime' => $donationDateTime,
                'weight_kg' => $weight,
                'temperature_c' => $temp,
                'hemoglobin' => $validated['hemoglobin'] ?? null,
                'notes' => $validated['notes'] ?? 'Auto-recorded from donation log',
                'checked_by_user_id' => auth('api')->id(),
            ]);

            $healthCheckId = $healthCheck->id;
        }

        $profile->update(['is_eligible' => $eligibleStatus]);

        if (! $eligibleStatus) {
            return response()->json([
                'message' => 'Donor is currently not eligible based on health check data.',
            ], 409);
        }

        $bloodGroup = $validated['bloodGroup'] ?? $profile->blood_group;
        $componentType = $validated['componentType'] ?? 'WholeBlood';
        $donation = null;
        $inventory = null;

        DB::transaction(function () use (&$donation, &$inventory, $validated, $profile, $bloodGroup, $componentType, $donationDateTime, $healthCheckId): void {
            $donation = BloodDonation::query()->create([
                'donor_id' => $profile->donor_id,
                'blood_bank_id' => $validated['bankId'],
                'donation_datetime' => $donationDateTime,
                'blood_group' => $bloodGroup,
                'component_type' => $componentType,
                'units_donated' => $validated['unitsDonated'],
                'recorded_by_user_id' => auth('api')->id(),
                'linked_request_id' => $validated['linkedRequestId'] ?? null,
                'donor_health_check_id' => $healthCheckId,
                'notes' => $validated['notes'] ?? null,
            ]);

            $inventory = BloodInventory::query()
                ->where('blood_bank_id', $validated['bankId'])
                ->where('blood_group', $bloodGroup)
                ->where('component_type', $componentType)
                ->lockForUpdate()
                ->first();

            if ($inventory) {
                $inventory->update([
                    'units_available' => ((int) $inventory->units_available) + (int) $validated['unitsDonated'],
                    'last_updated_at' => now(),
                ]);
            } else {
                $inventory = BloodInventory::query()->create([
                    'blood_bank_id' => $validated['bankId'],
                    'blood_group' => $bloodGroup,
                    'component_type' => $componentType,
                    'units_available' => $validated['unitsDonated'],
                    'last_updated_at' => now(),
                ]);
            }

            $profile->update(['last_donation_date' => $donationDateTime]);
        });

        $donation->load(['bloodBank:id,bank_name', 'linkedRequest:id,status,blood_group_needed,units_required']);
        $inventory->load('bloodBank:id,bank_name');

        return response()->json([
            'message' => 'Donation logged and inventory updated',
            'donation' => $this->donationPayload($donation),
            'inventory' => [
                'id' => $inventory->id,
                'blood_bank_id' => $inventory->blood_bank_id,
                'bank_name' => $inventory->bloodBank?->bank_name,
                'blood_group' => $inventory->blood_group,
                'component_type' => $inventory->component_type,
                'units_available' => (int) $inventory->units_available,
                'last_updated_at' => optional($inventory->last_updated_at)->toISOString(),
            ],
        ], 201);
    }

    private function resolveDonorProfile(): DonorProfile
    {
        $user = auth('api')->user();

        $bloodGroup = $user->patientProfile?->blood_group ?? 'O+';

        $profile = DonorProfile::query()->firstOrCreate(
            ['donor_id' => $user->id],
            [
                'blood_group' => $bloodGroup,
                'is_eligible' => true,
            ]
        );

        $profile->load('donor:id,full_name,name,email', 'donor.roles:id,role_name');

        return $profile;
    }

    private function assignDonorRole(int $userId): void
    {
        $role = Role::query()->firstOrCreate(
            ['role_name' => 'Donor'],
            ['description' => 'Donor role']
        );

        DB::table('user_roles')->updateOrInsert(
            [
                'user_id' => $userId,
                'role_id' => $role->id,
            ],
            [
                'assigned_at' => now(),
                'assigned_by_user_id' => auth('api')->id(),
            ]
        );
    }

    private function isEligibleByVitals(float $weightKg, float $temperatureC): bool
    {
        return $weightKg >= 45
            && $temperatureC >= 36.0
            && $temperatureC <= 37.8;
    }

    private function donorPayload(DonorProfile $profile): array
    {
        return [
            'donor_id' => $profile->donor_id,
            'full_name' => $profile->donor?->full_name ?? $profile->donor?->name,
            'email' => $profile->donor?->email,
            'blood_group' => $profile->blood_group,
            'last_donation_date' => optional($profile->last_donation_date)->toISOString(),
            'is_eligible' => (bool) $profile->is_eligible,
            'notes' => $profile->notes,
            'roles' => $profile->donor?->roles?->pluck('role_name')->values(),
        ];
    }

    private function availabilityPayload(DonorAvailability $availability): array
    {
        return [
            'id' => $availability->id,
            'donor_id' => $availability->donor_id,
            'week_start_date' => optional($availability->week_start_date)->toDateString(),
            'is_available' => (bool) $availability->is_available,
            'max_bags_possible' => (int) $availability->max_bags_possible,
            'notes' => $availability->notes,
            'updated_at' => optional($availability->updated_at)->toISOString(),
        ];
    }

    private function healthCheckPayload(DonorHealthCheck $check): array
    {
        return [
            'id' => $check->id,
            'donor_id' => $check->donor_id,
            'check_datetime' => optional($check->check_datetime)->toISOString(),
            'weight_kg' => $check->weight_kg,
            'temperature_c' => $check->temperature_c,
            'hemoglobin' => $check->hemoglobin,
            'notes' => $check->notes,
            'checked_by_user_id' => $check->checked_by_user_id,
        ];
    }

    private function donationPayload(BloodDonation $donation): array
    {
        return [
            'id' => $donation->id,
            'donor_id' => $donation->donor_id,
            'blood_bank_id' => $donation->blood_bank_id,
            'bank_name' => $donation->bloodBank?->bank_name,
            'donation_datetime' => optional($donation->donation_datetime)->toISOString(),
            'blood_group' => $donation->blood_group,
            'component_type' => $donation->component_type,
            'units_donated' => (int) $donation->units_donated,
            'recorded_by_user_id' => $donation->recorded_by_user_id,
            'linked_request_id' => $donation->linked_request_id,
            'linked_request_status' => $donation->linkedRequest?->status,
            'notes' => $donation->notes,
        ];
    }
}
