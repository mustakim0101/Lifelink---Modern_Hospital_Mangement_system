<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BloodBank;
use App\Models\BloodInventory;
use App\Models\BloodRequest;
use App\Models\DonorProfile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class BloodBankSchemaController extends Controller
{
    private const BLOOD_GROUPS = ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'];
    private const BLOOD_COMPONENTS = ['WholeBlood', 'Plasma', 'Platelets', 'RBC'];
    private const BLOOD_REQUEST_STATUS = ['Pending', 'Matched', 'Approved', 'Fulfilled', 'Rejected', 'Cancelled'];

    public function overview(): JsonResponse
    {
        $banksCount = BloodBank::query()->count();
        $donorsCount = DonorProfile::query()->count();
        $inventoryRowsCount = BloodInventory::query()->count();
        $requestsCount = BloodRequest::query()->count();
        $totalUnits = (int) BloodInventory::query()->sum('units_available');

        $requestsByStatus = BloodRequest::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->orderBy('status')
            ->get()
            ->map(fn ($row) => [
                'status' => $row->status,
                'total' => (int) $row->total,
            ]);

        $banks = BloodBank::query()
            ->withCount('inventoryRows')
            ->withSum('inventoryRows', 'units_available')
            ->orderBy('id')
            ->get()
            ->map(fn (BloodBank $bank) => [
                'id' => $bank->id,
                'bank_name' => $bank->bank_name,
                'location' => $bank->location,
                'is_active' => (bool) $bank->is_active,
                'inventory_rows' => (int) ($bank->inventory_rows_count ?? 0),
                'units_total' => (int) ($bank->inventory_rows_sum_units_available ?? 0),
            ]);

        return response()->json([
            'stats' => [
                'banks' => $banksCount,
                'donor_profiles' => $donorsCount,
                'inventory_rows' => $inventoryRowsCount,
                'blood_requests' => $requestsCount,
                'total_units_available' => $totalUnits,
            ],
            'requests_by_status' => $requestsByStatus,
            'banks' => $banks,
        ]);
    }

    public function banks(Request $request): JsonResponse
    {
        // Read filters from query string only, so stale JSON bodies in Postman do not break GET.
        $validated = validator($request->query(), [
            'active' => ['nullable', 'boolean'],
        ])->validate();

        $query = BloodBank::query()
            ->withCount('inventoryRows')
            ->withSum('inventoryRows', 'units_available')
            ->orderBy('id');

        if (array_key_exists('active', $validated)) {
            $query->where('is_active', (bool) $validated['active']);
        }

        return response()->json([
            'banks' => $query->get()->map(fn (BloodBank $bank) => [
                'id' => $bank->id,
                'bank_name' => $bank->bank_name,
                'location' => $bank->location,
                'is_active' => (bool) $bank->is_active,
                'inventory_rows' => (int) ($bank->inventory_rows_count ?? 0),
                'units_total' => (int) ($bank->inventory_rows_sum_units_available ?? 0),
            ]),
        ]);
    }

    public function createBank(Request $request): JsonResponse
    {
        $payload = [
            'bankName' => $request->input('bankName', $request->input('bank_name')),
            'location' => $request->input('location'),
            'isActive' => $request->has('isActive')
                ? $request->input('isActive')
                : $request->input('is_active'),
        ];

        $validated = validator($payload, [
            'bankName' => ['required', 'string', 'max:150', 'unique:blood_banks,bank_name'],
            'location' => ['nullable', 'string', 'max:255'],
            'isActive' => ['nullable', 'boolean'],
        ])->validate();

        $bank = BloodBank::query()->create([
            'bank_name' => $validated['bankName'],
            'location' => $validated['location'] ?? null,
            'is_active' => $validated['isActive'] ?? true,
        ]);

        return response()->json([
            'message' => 'Blood bank created',
            'bank' => [
                'id' => $bank->id,
                'bank_name' => $bank->bank_name,
                'location' => $bank->location,
                'is_active' => (bool) $bank->is_active,
            ],
        ], 201);
    }

    public function donorProfiles(Request $request): JsonResponse
    {
        // Read filters from query string only, so stale JSON bodies in Postman do not break GET.
        $validated = validator($request->query(), [
            'bloodGroup' => ['nullable', 'string', Rule::in(self::BLOOD_GROUPS)],
            'eligible' => ['nullable', 'boolean'],
        ])->validate();

        $query = DonorProfile::query()
            ->with('donor:id,full_name,name,email')
            ->orderBy('donor_id');

        if (! empty($validated['bloodGroup'])) {
            $query->where('blood_group', $validated['bloodGroup']);
        }

        if (array_key_exists('eligible', $validated)) {
            $query->where('is_eligible', (bool) $validated['eligible']);
        }

        return response()->json([
            'donor_profiles' => $query->get()->map(fn (DonorProfile $profile) => [
                'donor_id' => $profile->donor_id,
                'full_name' => $profile->donor?->full_name ?? $profile->donor?->name,
                'email' => $profile->donor?->email,
                'blood_group' => $profile->blood_group,
                'last_donation_date' => optional($profile->last_donation_date)->toISOString(),
                'is_eligible' => (bool) $profile->is_eligible,
                'notes' => $profile->notes,
            ]),
        ]);
    }

    public function upsertDonorProfile(Request $request): JsonResponse
    {
        $payload = [
            'donorUserId' => $request->input('donorUserId', $request->input('donor_user_id')),
            'bloodGroup' => $request->input('bloodGroup', $request->input('blood_group')),
            'lastDonationDate' => $request->input('lastDonationDate', $request->input('last_donation_date')),
            'isEligible' => $request->has('isEligible')
                ? $request->input('isEligible')
                : $request->input('is_eligible'),
            'notes' => $request->input('notes'),
        ];

        $validated = validator($payload, [
            'donorUserId' => ['required', 'integer', 'exists:users,id'],
            'bloodGroup' => ['required', 'string', Rule::in(self::BLOOD_GROUPS)],
            'lastDonationDate' => ['nullable', 'date'],
            'isEligible' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
        ])->validate();

        $user = User::query()->findOrFail($validated['donorUserId']);
        $this->assignDonorRole($user);

        $profile = DonorProfile::query()->updateOrCreate(
            ['donor_id' => $validated['donorUserId']],
            [
                'blood_group' => $validated['bloodGroup'],
                'last_donation_date' => $validated['lastDonationDate'] ?? null,
                'is_eligible' => $validated['isEligible'] ?? true,
                'notes' => $validated['notes'] ?? null,
            ]
        );

        $profile->load('donor:id,full_name,name,email');

        return response()->json([
            'message' => 'Donor profile upserted',
            'donor_profile' => [
                'donor_id' => $profile->donor_id,
                'full_name' => $profile->donor?->full_name ?? $profile->donor?->name,
                'email' => $profile->donor?->email,
                'blood_group' => $profile->blood_group,
                'last_donation_date' => optional($profile->last_donation_date)->toISOString(),
                'is_eligible' => (bool) $profile->is_eligible,
                'notes' => $profile->notes,
            ],
        ]);
    }

    public function inventory(Request $request): JsonResponse
    {
        // Read filters from query string only, so stale JSON bodies in Postman do not break GET.
        $validated = validator($request->query(), [
            'bankId' => ['nullable', 'integer', 'exists:blood_banks,id'],
            'bloodGroup' => ['nullable', 'string', Rule::in(self::BLOOD_GROUPS)],
            'componentType' => ['nullable', 'string', Rule::in(self::BLOOD_COMPONENTS)],
        ])->validate();

        $query = BloodInventory::query()
            ->with('bloodBank:id,bank_name')
            ->orderBy('blood_bank_id')
            ->orderBy('blood_group')
            ->orderBy('component_type');

        if (! empty($validated['bankId'])) {
            $query->where('blood_bank_id', $validated['bankId']);
        }

        if (! empty($validated['bloodGroup'])) {
            $query->where('blood_group', $validated['bloodGroup']);
        }

        if (! empty($validated['componentType'])) {
            $query->where('component_type', $validated['componentType']);
        }

        return response()->json([
            'inventory' => $query->get()->map(fn (BloodInventory $row) => [
                'id' => $row->id,
                'blood_bank_id' => $row->blood_bank_id,
                'bank_name' => $row->bloodBank?->bank_name,
                'blood_group' => $row->blood_group,
                'component_type' => $row->component_type,
                'units_available' => (int) $row->units_available,
                'last_updated_at' => optional($row->last_updated_at)->toISOString(),
            ]),
        ]);
    }

    public function upsertInventory(Request $request): JsonResponse
    {
        $rawBody = trim((string) $request->getContent());
        if ($request->isJson() && str_contains($rawBody, '<real_id>')) {
            return response()->json([
                'message' => 'Replace <real_id> with a numeric bank id from GET /api/blood/schema/banks.',
                'example' => [
                    'bankId' => 1,
                    'bloodGroup' => 'O+',
                    'componentType' => 'WholeBlood',
                    'unitsAvailable' => 12,
                ],
            ], 422);
        }

        $payload = [
            'bankId' => $request->input('bankId', $request->input('bank_id')),
            'bloodGroup' => $request->input('bloodGroup', $request->input('blood_group')),
            'componentType' => $request->input('componentType', $request->input('component_type')),
            'unitsAvailable' => $request->input('unitsAvailable', $request->input('units_available')),
        ];

        $validated = validator($payload, [
            'bankId' => ['required', 'integer', 'exists:blood_banks,id'],
            'bloodGroup' => ['required', 'string', Rule::in(self::BLOOD_GROUPS)],
            'componentType' => ['nullable', 'string', Rule::in(self::BLOOD_COMPONENTS)],
            'unitsAvailable' => ['required', 'integer', 'min:0'],
        ])->validate();

        $inventory = BloodInventory::query()->updateOrCreate(
            [
                'blood_bank_id' => $validated['bankId'],
                'blood_group' => $validated['bloodGroup'],
                'component_type' => $validated['componentType'] ?? 'WholeBlood',
            ],
            [
                'units_available' => $validated['unitsAvailable'],
                'last_updated_at' => now(),
            ]
        );

        $inventory->load('bloodBank:id,bank_name');

        return response()->json([
            'message' => 'Inventory row upserted',
            'inventory' => [
                'id' => $inventory->id,
                'blood_bank_id' => $inventory->blood_bank_id,
                'bank_name' => $inventory->bloodBank?->bank_name,
                'blood_group' => $inventory->blood_group,
                'component_type' => $inventory->component_type,
                'units_available' => (int) $inventory->units_available,
                'last_updated_at' => optional($inventory->last_updated_at)->toISOString(),
            ],
        ]);
    }

    public function requests(Request $request): JsonResponse
    {
        // Read filters from query string only, so stale JSON bodies in Postman do not break GET.
        $validated = validator($request->query(), [
            'status' => ['nullable', 'string', Rule::in(self::BLOOD_REQUEST_STATUS)],
            'bankId' => ['nullable', 'integer', 'exists:blood_banks,id'],
        ])->validate();

        $query = BloodRequest::query()
            ->with([
                'patient.user:id,full_name,name,email',
                'department:id,dept_name',
                'bloodBank:id,bank_name',
            ])
            ->orderByDesc('request_date')
            ->orderByDesc('id');

        if (! empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        if (! empty($validated['bankId'])) {
            $query->where('blood_bank_id', $validated['bankId']);
        }

        return response()->json([
            'blood_requests' => $query->get()->map(fn (BloodRequest $request) => [
                'id' => $request->id,
                'patient_id' => $request->patient_id,
                'patient_name' => $request->patient?->user?->full_name ?? $request->patient?->user?->name,
                'patient_email' => $request->patient?->user?->email,
                'department_id' => $request->department_id,
                'department' => $request->department?->dept_name,
                'blood_bank_id' => $request->blood_bank_id,
                'blood_bank' => $request->bloodBank?->bank_name,
                'blood_group_needed' => $request->blood_group_needed,
                'component_type' => $request->component_type,
                'units_required' => $request->units_required,
                'urgency' => $request->urgency,
                'status' => $request->status,
                'request_date' => optional($request->request_date)->toISOString(),
            ]),
        ]);
    }

    private function assignDonorRole(User $user): void
    {
        $role = Role::query()->firstOrCreate(
            ['role_name' => 'Donor'],
            ['description' => 'Donor role']
        );

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'assigned_at' => now(),
                'assigned_by_user_id' => auth('api')->id(),
            ],
        ]);
    }
}
