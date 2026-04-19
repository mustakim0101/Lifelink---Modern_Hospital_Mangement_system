<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Sql\BloodMatchingSqlService;
use DateTimeInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class DonorNotificationController extends Controller
{
    private const NOTIFICATION_STATUSES = ['Sent', 'Read', 'Acknowledged'];
    private const DONOR_RESPONSES = ['Accepted', 'Declined'];

    public function __construct(private readonly BloodMatchingSqlService $matchingService)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['nullable', 'string', Rule::in(self::NOTIFICATION_STATUSES)],
            'requestId' => ['nullable', 'integer', 'exists:blood_requests,id'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:120'],
        ]);

        $donorId = (int) auth('api')->id();
        $rows = $this->matchingService->donorNotifications($donorId, $validated);

        return response()->json([
            'notifications' => array_map(fn (object $row): array => [
                'id' => (int) $row->id,
                'donor_id' => (int) $row->donor_id,
                'request_id' => (int) $row->request_id,
                'match_id' => $row->match_id !== null ? (int) $row->match_id : null,
                'title' => $row->notification_title,
                'message' => $row->notification_message,
                'status' => $row->status,
                'response_status' => $row->response_status,
                'sent_at' => $this->asIso($row->sent_at),
                'read_at' => $this->asIso($row->read_at),
                'responded_at' => $this->asIso($row->responded_at),
                'request' => [
                    'blood_group_needed' => $row->blood_group_needed,
                    'component_type' => $row->component_type,
                    'units_required' => (int) $row->units_required,
                    'urgency' => $row->urgency,
                    'status' => $row->request_status,
                    'department_name' => $row->department_name,
                ],
                'match' => [
                    'status' => $row->match_status,
                    'score' => $row->match_score !== null ? (float) $row->match_score : null,
                    'compatibility' => $row->compatibility_label,
                ],
            ], $rows),
        ]);
    }

    public function markRead(int $notification): JsonResponse
    {
        $donorId = (int) auth('api')->id();
        $row = $this->matchingService->markNotificationRead($donorId, $notification);

        return response()->json([
            'message' => 'Notification marked as read',
            'notification' => [
                'id' => (int) $row->id,
                'status' => $row->status,
                'response_status' => $row->response_status,
                'read_at' => $this->asIso($row->read_at),
                'responded_at' => $this->asIso($row->responded_at),
            ],
        ]);
    }

    public function respond(Request $request, int $notification): JsonResponse
    {
        $validated = $request->validate([
            'response' => ['required', 'string', Rule::in(self::DONOR_RESPONSES)],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $donorId = (int) auth('api')->id();
        $result = $this->matchingService->respondToNotification(
            $donorId,
            $notification,
            $validated['response'],
            $validated['note'] ?? null
        );

        return response()->json([
            'message' => 'Notification response recorded',
            'notification' => [
                'id' => (int) $result['notification']->id,
                'status' => $result['notification']->status,
                'response_status' => $result['notification']->response_status,
                'read_at' => $this->asIso($result['notification']->read_at),
                'responded_at' => $this->asIso($result['notification']->responded_at),
            ],
            'match' => $result['match'] ? [
                'id' => (int) $result['match']->id,
                'request_id' => (int) $result['match']->request_id,
                'donor_id' => (int) $result['match']->donor_id,
                'status' => $result['match']->status,
                'responded_at' => $this->asIso($result['match']->responded_at),
            ] : null,
            'request' => [
                'id' => (int) $result['request']->id,
                'status' => $result['request']->status,
            ],
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
}
