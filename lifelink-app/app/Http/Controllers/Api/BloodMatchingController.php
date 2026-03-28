<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Sql\BloodMatchingSqlService;
use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class BloodMatchingController extends Controller
{
    private const REQUEST_STATUSES = ['Pending', 'Matched', 'Approved', 'Fulfilled', 'Rejected', 'Cancelled'];

    public function __construct(private readonly BloodMatchingSqlService $matchingService)
    {
    }

    public function requests(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(self::REQUEST_STATUSES)],
            'departmentId' => ['nullable', 'integer', 'exists:departments,id'],
            'bloodGroup' => ['nullable', 'string', 'max:5'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:150'],
        ]);

        $actor = auth('api')->user();
        $rows = $this->matchingService->listRequests(
            (int) $actor->id,
            $actor->hasRole('Admin'),
            $validated
        );

        return response()->json([
            'requests' => array_map(fn (object $row): array => [
                'id' => (int) $row->id,
                'patient_id' => (int) $row->patient_id,
                'patient_name' => $row->patient_name,
                'patient_email' => $row->patient_email,
                'department_id' => (int) $row->department_id,
                'department_name' => $row->department_name,
                'blood_bank_id' => $row->blood_bank_id !== null ? (int) $row->blood_bank_id : null,
                'bank_name' => $row->bank_name,
                'blood_group_needed' => $row->blood_group_needed,
                'component_type' => $row->component_type,
                'units_required' => (int) $row->units_required,
                'urgency' => $row->urgency,
                'status' => $row->status,
                'request_date' => $this->asIso($row->request_date),
                'notes' => $row->notes,
                'available_units' => (int) $row->available_units,
                'notified_count' => (int) $row->notified_count,
                'accepted_count' => (int) $row->accepted_count,
            ], $rows),
        ]);
    }

    public function suggestions(Request $request, int $bloodRequest): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:200'],
        ]);

        $actor = auth('api')->user();
        $rows = $this->matchingService->donorSuggestions(
            $bloodRequest,
            (int) $actor->id,
            $actor->hasRole('Admin'),
            (int) ($validated['limit'] ?? 25)
        );

        return response()->json([
            'suggestions' => array_map(fn (object $row): array => [
                'donor_id' => (int) $row->donor_id,
                'donor_name' => $row->donor_name,
                'donor_email' => $row->donor_email,
                'donor_blood_group' => $row->donor_blood_group,
                'is_eligible' => (bool) $row->is_eligible,
                'last_donation_date' => $this->asIso($row->last_donation_date),
                'is_available' => (bool) $row->is_available,
                'max_bags_possible' => (int) $row->max_bags_possible,
                'week_start_date' => $this->asIsoDate($row->week_start_date),
                'availability_notes' => $row->availability_notes,
                'last_check_datetime' => $this->asIso($row->last_check_datetime),
                'last_weight_kg' => $row->last_weight_kg,
                'last_temperature_c' => $row->last_temperature_c,
                'existing_match_id' => $row->existing_match_id !== null ? (int) $row->existing_match_id : null,
                'existing_match_status' => $row->existing_match_status,
                'existing_match_score' => $row->existing_match_score !== null ? (float) $row->existing_match_score : null,
                'compatibility_label' => $row->compatibility_label,
                'match_score' => $row->match_score !== null ? (float) $row->match_score : null,
            ], $rows),
        ]);
    }

    public function notify(Request $request, int $bloodRequest): JsonResponse
    {
        $validated = $request->validate([
            'donorIds' => ['nullable', 'array'],
            'donorIds.*' => ['integer', 'distinct', 'exists:donor_profiles,donor_id'],
            'title' => ['nullable', 'string', 'max:180'],
            'message' => ['nullable', 'string'],
            'forceResend' => ['nullable', 'boolean'],
            'suggestedLimit' => ['nullable', 'integer', 'min:1', 'max:30'],
        ]);

        $actor = auth('api')->user();
        $result = $this->matchingService->notifyDonors(
            $bloodRequest,
            (int) $actor->id,
            $actor->hasRole('Admin'),
            $validated
        );

        return response()->json([
            'message' => 'Donor notifications processed',
            'request' => [
                'id' => (int) $result['request']->id,
                'status' => $result['request']->status,
                'blood_group_needed' => $result['request']->blood_group_needed,
                'component_type' => $result['request']->component_type,
                'units_required' => (int) $result['request']->units_required,
                'department_id' => (int) $result['request']->department_id,
                'department_name' => $result['request']->department_name,
            ],
            'sent_count' => count($result['sent']),
            'skipped_count' => count($result['skipped']),
            'sent' => $result['sent'],
            'skipped' => $result['skipped'],
        ]);
    }

    public function matches(int $bloodRequest): JsonResponse
    {
        $actor = auth('api')->user();
        $rows = $this->matchingService->requestMatches(
            $bloodRequest,
            (int) $actor->id,
            $actor->hasRole('Admin')
        );

        return response()->json([
            'matches' => array_map(fn (object $row): array => [
                'id' => (int) $row->id,
                'request_id' => (int) $row->request_id,
                'donor_id' => (int) $row->donor_id,
                'donor_name' => $row->donor_name,
                'donor_email' => $row->donor_email,
                'donor_blood_group' => $row->donor_blood_group,
                'match_score' => $row->match_score !== null ? (float) $row->match_score : null,
                'compatibility_label' => $row->compatibility_label,
                'status' => $row->status,
                'notified_at' => $this->asIso($row->notified_at),
                'responded_at' => $this->asIso($row->responded_at),
                'selected_by_user_id' => $row->selected_by_user_id !== null ? (int) $row->selected_by_user_id : null,
                'notes' => $row->notes,
                'latest_notification_id' => $row->latest_notification_id !== null ? (int) $row->latest_notification_id : null,
                'latest_notification_status' => $row->latest_notification_status,
                'latest_notification_response' => $row->latest_notification_response,
                'latest_notification_sent_at' => $this->asIso($row->latest_notification_sent_at),
                'latest_notification_responded_at' => $this->asIso($row->latest_notification_responded_at),
            ], $rows),
        ]);
    }

    public function donors(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'requestId' => ['nullable', 'integer', 'exists:blood_requests,id'],
            'q' => ['nullable', 'string', 'max:120'],
            'bloodGroup' => ['nullable', 'string', 'max:5'],
            'eligible' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $actor = auth('api')->user();
        $rows = $this->matchingService->staffDonors(
            (int) $actor->id,
            $actor->hasRole('Admin'),
            $validated
        );

        return response()->json([
            'donors' => array_map(fn (object $row): array => [
                'donor_id' => (int) $row->donor_id,
                'donor_name' => $row->donor_name,
                'donor_email' => $row->donor_email,
                'blood_group' => $row->blood_group,
                'is_eligible' => (bool) $row->is_eligible,
                'last_donation_date' => $this->asIso($row->last_donation_date),
                'latest_health_check_id' => $row->latest_health_check_id !== null ? (int) $row->latest_health_check_id : null,
                'latest_health_check_at' => $this->asIso($row->latest_health_check_at),
                'latest_checked_by_name' => $row->latest_checked_by_name,
                'matched_request_id' => $row->matched_request_id !== null ? (int) $row->matched_request_id : null,
                'matched_request_status' => $row->matched_request_status,
                'matched_request_match_id' => $row->matched_request_match_id !== null ? (int) $row->matched_request_match_id : null,
            ], $rows),
        ]);
    }

    public function donorHealthChecks(Request $request, int $donor): JsonResponse
    {
        $validated = $request->validate([
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $actor = auth('api')->user();
        $rows = $this->matchingService->staffDonorHealthChecks(
            $donor,
            (int) $actor->id,
            $actor->hasRole('Admin'),
            (int) ($validated['limit'] ?? 20)
        );

        return response()->json([
            'health_checks' => array_map(fn (object $row): array => [
                'id' => (int) $row->id,
                'donor_id' => (int) $row->donor_id,
                'check_datetime' => $this->asIso($row->check_datetime),
                'weight_kg' => $row->weight_kg !== null ? (float) $row->weight_kg : null,
                'temperature_c' => $row->temperature_c !== null ? (float) $row->temperature_c : null,
                'hemoglobin' => $row->hemoglobin !== null ? (float) $row->hemoglobin : null,
                'notes' => $row->notes,
                'checked_by_user_id' => $row->checked_by_user_id !== null ? (int) $row->checked_by_user_id : null,
                'checked_by_name' => $row->checked_by_name,
            ], $rows),
        ]);
    }

    public function logDonation(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'donorId' => ['required', 'integer', 'exists:donor_profiles,donor_id'],
            'bloodBankId' => ['required', 'integer', 'exists:blood_banks,id'],
            'donationDateTime' => ['nullable', 'date'],
            'bloodGroup' => ['nullable', 'string', 'max:5'],
            'componentType' => ['nullable', 'string', 'max:30'],
            'unitsDonated' => ['required', 'integer', 'min:1', 'max:5'],
            'linkedRequestId' => ['nullable', 'integer', 'exists:blood_requests,id'],
            'donorHealthCheckId' => ['required', 'integer', 'exists:donor_health_checks,id'],
            'notes' => ['nullable', 'string'],
        ]);

        $actor = auth('api')->user();
        $result = $this->matchingService->recordDonation(
            (int) $actor->id,
            $actor->hasRole('Admin'),
            $validated
        );

        return response()->json([
            'message' => 'Donation logged by Blood Bank staff and inventory updated.',
            'donation' => $result['donation'],
            'inventory' => $result['inventory'],
        ], 201);
    }

    public function approve(Request $request, int $bloodRequest): JsonResponse
    {
        $validated = $request->validate([
            'matchId' => ['required', 'integer'],
            'bloodBankId' => ['nullable', 'integer', 'exists:blood_banks,id'],
            'note' => ['nullable', 'string'],
        ]);

        $actor = auth('api')->user();
        $result = $this->matchingService->approveMatch(
            $bloodRequest,
            (int) $validated['matchId'],
            (int) $actor->id,
            $actor->hasRole('Admin'),
            isset($validated['bloodBankId']) ? (int) $validated['bloodBankId'] : null,
            $validated['note'] ?? null
        );

        return response()->json([
            'message' => 'Accepted donor approved for this blood request.',
            'request' => [
                'id' => (int) $result['request']->id,
                'status' => $result['request']->status,
                'department_id' => (int) $result['request']->department_id,
                'department_name' => $result['request']->department_name,
                'blood_group_needed' => $result['request']->blood_group_needed,
                'component_type' => $result['request']->component_type,
                'units_required' => (int) $result['request']->units_required,
                'blood_bank_id' => $result['request']->blood_bank_id !== null ? (int) $result['request']->blood_bank_id : null,
            ],
            'match' => $result['match'] ? [
                'id' => (int) $result['match']->id,
                'request_id' => (int) $result['match']->request_id,
                'donor_id' => (int) $result['match']->donor_id,
                'status' => $result['match']->status,
                'match_score' => $result['match']->match_score !== null ? (float) $result['match']->match_score : null,
                'compatibility_label' => $result['match']->compatibility_label,
                'selected_by_user_id' => $result['match']->selected_by_user_id !== null ? (int) $result['match']->selected_by_user_id : null,
                'notes' => $result['match']->notes,
            ] : null,
        ]);
    }

    public function fulfill(Request $request, int $bloodRequest): JsonResponse
    {
        $validated = $request->validate([
            'matchId' => ['nullable', 'integer'],
            'bloodBankId' => ['nullable', 'integer', 'exists:blood_banks,id'],
            'consumeInventory' => ['nullable', 'boolean'],
            'note' => ['nullable', 'string'],
        ]);

        $actor = auth('api')->user();
        $result = $this->matchingService->fulfillRequest(
            $bloodRequest,
            (int) $actor->id,
            $actor->hasRole('Admin'),
            isset($validated['matchId']) ? (int) $validated['matchId'] : null,
            isset($validated['bloodBankId']) ? (int) $validated['bloodBankId'] : null,
            (bool) ($validated['consumeInventory'] ?? false),
            $validated['note'] ?? null
        );

        return response()->json([
            'message' => 'Blood request fulfilled.',
            'request' => [
                'id' => (int) $result['request']->id,
                'status' => $result['request']->status,
                'department_id' => (int) $result['request']->department_id,
                'department_name' => $result['request']->department_name,
                'blood_group_needed' => $result['request']->blood_group_needed,
                'component_type' => $result['request']->component_type,
                'units_required' => (int) $result['request']->units_required,
                'blood_bank_id' => $result['request']->blood_bank_id !== null ? (int) $result['request']->blood_bank_id : null,
            ],
            'match' => $result['match'] ? [
                'id' => (int) $result['match']->id,
                'request_id' => (int) $result['match']->request_id,
                'donor_id' => (int) $result['match']->donor_id,
                'status' => $result['match']->status,
                'match_score' => $result['match']->match_score !== null ? (float) $result['match']->match_score : null,
                'compatibility_label' => $result['match']->compatibility_label,
                'selected_by_user_id' => $result['match']->selected_by_user_id !== null ? (int) $result['match']->selected_by_user_id : null,
                'notes' => $result['match']->notes,
            ] : null,
        ]);
    }

    private function asIso(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        try {
            return Carbon::parse((string) $value)->toISOString();
        } catch (\Throwable) {
            return (string) $value;
        }
    }

    private function asIsoDate(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        try {
            return Carbon::parse((string) $value)->toDateString();
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
