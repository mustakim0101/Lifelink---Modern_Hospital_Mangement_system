<?php

namespace App\Services\Sql;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BloodMatchingSqlService
{
    private const REQUEST_STATUSES = ['Pending', 'Matched', 'Approved', 'Fulfilled', 'Rejected', 'Cancelled'];
    private const MATCH_STATUSES = ['Suggested', 'Notified', 'Accepted', 'Declined', 'Completed'];
    private const NOTIFICATION_STATUSES = ['Sent', 'Read', 'Acknowledged'];

    public function listRequests(int $actorUserId, bool $isAdmin, array $filters = []): array
    {
        $limit = max(1, min(150, (int) ($filters['limit'] ?? 50)));
        $params = [];
        $where = ['1=1'];

        if (! empty($filters['status']) && in_array($filters['status'], self::REQUEST_STATUSES, true)) {
            $where[] = 'br.status = ?';
            $params[] = $filters['status'];
        }

        if (! empty($filters['departmentId'])) {
            $where[] = 'br.department_id = ?';
            $params[] = (int) $filters['departmentId'];
        }

        if (! empty($filters['bloodGroup'])) {
            $where[] = 'br.blood_group_needed = ?';
            $params[] = $filters['bloodGroup'];
        }

        if (! $isAdmin) {
            $deptIds = $this->departmentIdsForItWorker($actorUserId);
            if (empty($deptIds)) {
                return [];
            }

            $placeholders = implode(',', array_fill(0, count($deptIds), '?'));
            $where[] = "br.department_id IN ({$placeholders})";
            $params = array_merge($params, $deptIds);
        }

        $whereSql = implode(' AND ', $where);
        $sql = "
            SELECT TOP {$limit}
                br.id,
                br.patient_id,
                br.department_id,
                d.dept_name AS department_name,
                br.blood_bank_id,
                bb.bank_name,
                br.blood_group_needed,
                br.component_type,
                br.units_required,
                br.urgency,
                br.status,
                br.request_date,
                br.notes,
                COALESCE(NULLIF(pu.full_name, N''), pu.name) AS patient_name,
                pu.email AS patient_email,
                ISNULL(inv.total_units, 0) AS available_units,
                ISNULL(mt.notified_count, 0) AS notified_count,
                ISNULL(mt.accepted_count, 0) AS accepted_count
            FROM blood_requests br
            INNER JOIN patients p ON p.patient_id = br.patient_id
            INNER JOIN users pu ON pu.id = p.patient_id
            LEFT JOIN departments d ON d.id = br.department_id
            LEFT JOIN blood_banks bb ON bb.id = br.blood_bank_id
            OUTER APPLY (
                SELECT SUM(bi.units_available) AS total_units
                FROM blood_inventory bi
                WHERE bi.blood_group = br.blood_group_needed
                    AND bi.component_type = br.component_type
                    AND (br.blood_bank_id IS NULL OR bi.blood_bank_id = br.blood_bank_id)
            ) inv
            OUTER APPLY (
                SELECT
                    SUM(CASE WHEN m.status IN (N'Notified', N'Accepted', N'Completed') THEN 1 ELSE 0 END) AS notified_count,
                    SUM(CASE WHEN m.status IN (N'Accepted', N'Completed') THEN 1 ELSE 0 END) AS accepted_count
                FROM blood_request_matches m
                WHERE m.request_id = br.id
            ) mt
            WHERE {$whereSql}
            ORDER BY
                CASE br.urgency
                    WHEN N'Emergency' THEN 0
                    WHEN N'Urgent' THEN 1
                    ELSE 2
                END,
                br.request_date DESC,
                br.id DESC;
        ";

        return DB::select($sql, $params);
    }

    public function getRequest(int $requestId, int $actorUserId, bool $isAdmin): object
    {
        $request = DB::selectOne(
            'SELECT TOP 1
                br.id,
                br.patient_id,
                br.department_id,
                br.blood_bank_id,
                br.blood_group_needed,
                br.component_type,
                br.units_required,
                br.urgency,
                br.status,
                br.request_date,
                d.dept_name AS department_name
            FROM blood_requests br
            LEFT JOIN departments d ON d.id = br.department_id
            WHERE br.id = ?;',
            [$requestId]
        );

        if (! $request) {
            abort(404, 'Blood request not found.');
        }

        if (! $isAdmin) {
            $allowed = DB::selectOne(
                'SELECT COUNT(*) AS total
                 FROM department_admins
                 WHERE user_id = ? AND department_id = ?;',
                [$actorUserId, (int) $request->department_id]
            );

            if (((int) ($allowed->total ?? 0)) === 0) {
                abort(403, 'Forbidden: department access not allowed for this blood request.');
            }
        }

        return $request;
    }

    public function donorSuggestions(int $requestId, int $actorUserId, bool $isAdmin, int $limit = 25): array
    {
        $request = $this->getRequest($requestId, $actorUserId, $isAdmin);
        $limit = max(1, min(200, $limit));

        $compatibleGroups = $this->compatibleDonorGroups((string) $request->blood_group_needed);
        $groupPlaceholders = implode(',', array_fill(0, count($compatibleGroups), '?'));
        $weekStart = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();

        $params = [
            $request->blood_group_needed,
            $request->blood_group_needed,
            $request->blood_group_needed,
            $request->blood_group_needed,
            $weekStart,
            $requestId,
            ...$compatibleGroups,
            $request->blood_group_needed,
        ];

        $sql = "
            SELECT TOP {$limit}
                dp.donor_id,
                COALESCE(NULLIF(u.full_name, N''), u.name) AS donor_name,
                u.email AS donor_email,
                dp.blood_group AS donor_blood_group,
                CAST(dp.is_eligible AS BIT) AS is_eligible,
                dp.last_donation_date,
                CAST(ISNULL(da.is_available, 0) AS BIT) AS is_available,
                ISNULL(da.max_bags_possible, 0) AS max_bags_possible,
                da.week_start_date,
                da.notes AS availability_notes,
                hc.check_datetime AS last_check_datetime,
                hc.weight_kg AS last_weight_kg,
                hc.temperature_c AS last_temperature_c,
                m.id AS existing_match_id,
                m.status AS existing_match_status,
                m.match_score AS existing_match_score,
                CASE
                    WHEN dp.blood_group = ? THEN N'Exact'
                    WHEN dp.blood_group = N'O-' AND ? <> N'O-' THEN N'Universal'
                    ELSE N'Compatible'
                END AS compatibility_label,
                (
                    CASE
                        WHEN dp.blood_group = ? THEN 100
                        WHEN dp.blood_group = N'O-' AND ? <> N'O-' THEN 90
                        ELSE 80
                    END
                    + (ISNULL(da.max_bags_possible, 0) * 2)
                    + CASE
                        WHEN dp.last_donation_date IS NULL THEN 6
                        WHEN DATEDIFF(DAY, dp.last_donation_date, SYSDATETIME()) >= 56 THEN 4
                        ELSE -12
                    END
                ) AS match_score
            FROM donor_profiles dp
            INNER JOIN users u ON u.id = dp.donor_id
            LEFT JOIN donor_availabilities da
                ON da.donor_id = dp.donor_id
                AND da.week_start_date = ?
            OUTER APPLY (
                SELECT TOP 1
                    h.check_datetime,
                    h.weight_kg,
                    h.temperature_c
                FROM donor_health_checks h
                WHERE h.donor_id = dp.donor_id
                ORDER BY h.check_datetime DESC, h.id DESC
            ) hc
            LEFT JOIN blood_request_matches m
                ON m.request_id = ?
                AND m.donor_id = dp.donor_id
            WHERE dp.is_eligible = 1
                AND dp.blood_group IN ({$groupPlaceholders})
                AND ISNULL(da.is_available, 0) = 1
                AND ISNULL(da.max_bags_possible, 0) > 0
            ORDER BY
                CASE
                    WHEN dp.blood_group = ? THEN 0
                    WHEN dp.blood_group = N'O-' THEN 1
                    ELSE 2
                END,
                match_score DESC,
                da.max_bags_possible DESC,
                dp.donor_id ASC;
        ";

        return DB::select($sql, $params);
    }

    public function notifyDonors(int $requestId, int $actorUserId, bool $isAdmin, array $payload): array
    {
        $request = $this->getRequest($requestId, $actorUserId, $isAdmin);
        if (in_array($request->status, ['Fulfilled', 'Rejected', 'Cancelled'], true)) {
            abort(409, 'Cannot notify donors for a closed blood request.');
        }

        $requestedDonorIds = array_values(array_filter(
            array_unique(array_map('intval', $payload['donorIds'] ?? [])),
            fn (int $id): bool => $id > 0
        ));
        $forceResend = (bool) ($payload['forceResend'] ?? false);
        $requestedLimit = (int) ($payload['suggestedLimit'] ?? max(((int) $request->units_required) * 3, 4));
        $requestedLimit = max(1, min(30, $requestedLimit));

        $suggestions = $this->donorSuggestions($requestId, $actorUserId, $isAdmin, max($requestedLimit, count($requestedDonorIds), 40));
        $suggestionByDonorId = [];
        foreach ($suggestions as $row) {
            $suggestionByDonorId[(int) $row->donor_id] = $row;
        }

        if (empty($requestedDonorIds)) {
            $requestedDonorIds = array_values(array_slice(array_keys($suggestionByDonorId), 0, $requestedLimit));
        }

        if (empty($requestedDonorIds)) {
            abort(422, 'No available compatible donors were found.');
        }

        $missing = array_values(array_filter(
            $requestedDonorIds,
            fn (int $donorId): bool => ! array_key_exists($donorId, $suggestionByDonorId)
        ));

        if (! empty($missing)) {
            abort(422, 'Some donorIds are not currently compatible/available: '.implode(', ', $missing));
        }

        $title = trim((string) ($payload['title'] ?? ''));
        if ($title === '') {
            $title = 'Blood request #'.$requestId.' needs donor confirmation';
        }

        $customMessage = trim((string) ($payload['message'] ?? ''));
        $result = DB::transaction(function () use (
            $requestId,
            $actorUserId,
            $request,
            $requestedDonorIds,
            $suggestionByDonorId,
            $title,
            $customMessage,
            $forceResend
        ): array {
            $sent = [];
            $skipped = [];

            foreach ($requestedDonorIds as $donorId) {
                $suggestion = $suggestionByDonorId[$donorId];
                $existing = DB::selectOne(
                    'SELECT TOP 1 id, status
                     FROM blood_request_matches
                     WHERE request_id = ? AND donor_id = ?;',
                    [$requestId, $donorId]
                );

                if ($existing && in_array($existing->status, ['Accepted', 'Completed'], true) && ! $forceResend) {
                    $skipped[] = [
                        'donor_id' => $donorId,
                        'reason' => 'match already accepted/completed',
                    ];
                    continue;
                }

                $compatibility = (string) ($suggestion->compatibility_label ?? 'Compatible');
                $score = isset($suggestion->match_score) ? (float) $suggestion->match_score : null;
                $message = $customMessage !== ''
                    ? $customMessage
                    : $this->defaultNotificationMessage($request, $suggestion);

                if ($existing) {
                    DB::update(
                        'UPDATE blood_request_matches
                         SET status = ?, match_score = ?, compatibility_label = ?, notified_at = SYSDATETIME(),
                             selected_by_user_id = ?, updated_at = SYSDATETIME()
                         WHERE id = ?;',
                        ['Notified', $score, $compatibility, $actorUserId, (int) $existing->id]
                    );
                    $matchId = (int) $existing->id;
                } else {
                    DB::insert(
                        'INSERT INTO blood_request_matches
                            (request_id, donor_id, match_score, compatibility_label, status, notified_at, selected_by_user_id, created_at, updated_at)
                         VALUES (?, ?, ?, ?, ?, SYSDATETIME(), ?, SYSDATETIME(), SYSDATETIME());',
                        [$requestId, $donorId, $score, $compatibility, 'Notified', $actorUserId]
                    );

                    $created = DB::selectOne(
                        'SELECT TOP 1 id
                         FROM blood_request_matches
                         WHERE request_id = ? AND donor_id = ?
                         ORDER BY id DESC;',
                        [$requestId, $donorId]
                    );

                    $matchId = (int) ($created->id ?? 0);
                }

                DB::insert(
                    'INSERT INTO donor_notifications
                        (donor_id, request_id, match_id, notification_title, notification_message, status, sent_at, created_by_user_id, created_at, updated_at)
                     VALUES (?, ?, ?, ?, ?, ?, SYSDATETIME(), ?, SYSDATETIME(), SYSDATETIME());',
                    [$donorId, $requestId, $matchId ?: null, $title, $message, 'Sent', $actorUserId]
                );

                $sent[] = [
                    'donor_id' => $donorId,
                    'match_id' => $matchId ?: null,
                    'compatibility' => $compatibility,
                    'match_score' => $score,
                ];
            }

            if (! empty($sent)) {
                DB::update(
                    'UPDATE blood_requests
                     SET status = ?, updated_at = SYSDATETIME()
                     WHERE id = ? AND status IN (?, ?, ?);',
                    ['Matched', $requestId, 'Pending', 'Matched', 'Approved']
                );
            }

            return [
                'sent' => $sent,
                'skipped' => $skipped,
            ];
        });

        return [
            'request' => $this->getRequest($requestId, $actorUserId, $isAdmin),
            'sent' => $result['sent'],
            'skipped' => $result['skipped'],
        ];
    }

    public function requestMatches(int $requestId, int $actorUserId, bool $isAdmin): array
    {
        $this->getRequest($requestId, $actorUserId, $isAdmin);

        return DB::select(
            'SELECT
                m.id,
                m.request_id,
                m.donor_id,
                COALESCE(NULLIF(u.full_name, N\'\'), u.name) AS donor_name,
                u.email AS donor_email,
                dp.blood_group AS donor_blood_group,
                m.match_score,
                m.compatibility_label,
                m.status,
                m.notified_at,
                m.responded_at,
                m.selected_by_user_id,
                m.notes,
                n.id AS latest_notification_id,
                n.status AS latest_notification_status,
                n.response_status AS latest_notification_response,
                n.sent_at AS latest_notification_sent_at,
                n.responded_at AS latest_notification_responded_at
             FROM blood_request_matches m
             INNER JOIN donor_profiles dp ON dp.donor_id = m.donor_id
             INNER JOIN users u ON u.id = dp.donor_id
             OUTER APPLY (
                SELECT TOP 1
                    dn.id,
                    dn.status,
                    dn.response_status,
                    dn.sent_at,
                    dn.responded_at
                FROM donor_notifications dn
                WHERE dn.match_id = m.id
                ORDER BY dn.id DESC
             ) n
             WHERE m.request_id = ?
             ORDER BY
                CASE m.status
                    WHEN N\'Accepted\' THEN 0
                    WHEN N\'Notified\' THEN 1
                    WHEN N\'Suggested\' THEN 2
                    WHEN N\'Declined\' THEN 3
                    WHEN N\'Completed\' THEN 4
                    ELSE 5
                END,
                m.match_score DESC,
                m.id DESC;',
            [$requestId]
        );
    }

    public function donorNotifications(int $donorUserId, array $filters = []): array
    {
        $limit = max(1, min(120, (int) ($filters['limit'] ?? 30)));
        $params = [$donorUserId];
        $where = ['dn.donor_id = ?'];

        if (! empty($filters['status']) && in_array($filters['status'], self::NOTIFICATION_STATUSES, true)) {
            $where[] = 'dn.status = ?';
            $params[] = $filters['status'];
        }

        if (! empty($filters['requestId'])) {
            $where[] = 'dn.request_id = ?';
            $params[] = (int) $filters['requestId'];
        }

        $whereSql = implode(' AND ', $where);

        return DB::select(
            "SELECT TOP {$limit}
                dn.id,
                dn.donor_id,
                dn.request_id,
                dn.match_id,
                dn.notification_title,
                dn.notification_message,
                dn.status,
                dn.response_status,
                dn.sent_at,
                dn.read_at,
                dn.responded_at,
                br.blood_group_needed,
                br.component_type,
                br.units_required,
                br.urgency,
                br.status AS request_status,
                d.dept_name AS department_name,
                m.status AS match_status,
                m.match_score,
                m.compatibility_label
             FROM donor_notifications dn
             INNER JOIN blood_requests br ON br.id = dn.request_id
             LEFT JOIN departments d ON d.id = br.department_id
             LEFT JOIN blood_request_matches m ON m.id = dn.match_id
             WHERE {$whereSql}
             ORDER BY
                CASE dn.status
                    WHEN N'Sent' THEN 0
                    WHEN N'Read' THEN 1
                    ELSE 2
                END,
                dn.sent_at DESC,
                dn.id DESC;",
            $params
        );
    }

    public function markNotificationRead(int $donorUserId, int $notificationId): ?object
    {
        $notification = DB::selectOne(
            'SELECT TOP 1 id, status
             FROM donor_notifications
             WHERE id = ? AND donor_id = ?;',
            [$notificationId, $donorUserId]
        );

        if (! $notification) {
            abort(404, 'Notification not found for current donor.');
        }

        if (! empty($notification->response_status)) {
            abort(409, 'This notification already has a donor response.');
        }

        if ($notification->status === 'Sent') {
            DB::update(
                'UPDATE donor_notifications
                 SET status = ?, read_at = SYSDATETIME(), updated_at = SYSDATETIME()
                 WHERE id = ?;',
                ['Read', $notificationId]
            );
        }

        return DB::selectOne(
            'SELECT TOP 1
                id,
                donor_id,
                request_id,
                match_id,
                notification_title,
                notification_message,
                status,
                response_status,
                sent_at,
                read_at,
                responded_at
             FROM donor_notifications
             WHERE id = ?;',
            [$notificationId]
        );
    }

    public function respondToNotification(int $donorUserId, int $notificationId, string $response, ?string $responseNote = null): array
    {
        if (! in_array($response, ['Accepted', 'Declined'], true)) {
            abort(422, 'Invalid response. Allowed values: Accepted, Declined.');
        }

        $notification = DB::selectOne(
            'SELECT TOP 1
                id,
                donor_id,
                request_id,
                match_id,
                status,
                response_status
             FROM donor_notifications
             WHERE id = ? AND donor_id = ?;',
            [$notificationId, $donorUserId]
        );

        if (! $notification) {
            abort(404, 'Notification not found for current donor.');
        }

        $updated = DB::transaction(function () use ($notification, $response, $responseNote): array {
            DB::update(
                'UPDATE donor_notifications
                 SET status = ?, response_status = ?, read_at = COALESCE(read_at, SYSDATETIME()),
                     responded_at = SYSDATETIME(), updated_at = SYSDATETIME()
                 WHERE id = ?;',
                ['Acknowledged', $response, (int) $notification->id]
            );

            $matchId = (int) ($notification->match_id ?? 0);
            if ($matchId > 0) {
                $nextMatchStatus = $response === 'Accepted' ? 'Accepted' : 'Declined';
                $nextNote = $this->appendResponseNote($response, $responseNote);

                DB::update(
                    'UPDATE blood_request_matches
                     SET status = ?, responded_at = SYSDATETIME(), notes = ?,
                         updated_at = SYSDATETIME()
                     WHERE id = ?;',
                    [$nextMatchStatus, $nextNote, $matchId]
                );
            }

            $requestId = (int) $notification->request_id;
            $statusCount = DB::selectOne(
                'SELECT
                    SUM(CASE WHEN status IN (N\'Accepted\', N\'Completed\') THEN 1 ELSE 0 END) AS accepted_count,
                    SUM(CASE WHEN status = N\'Notified\' THEN 1 ELSE 0 END) AS notified_count
                 FROM blood_request_matches
                 WHERE request_id = ?;',
                [$requestId]
            );

            $acceptedCount = (int) ($statusCount->accepted_count ?? 0);
            $notifiedCount = (int) ($statusCount->notified_count ?? 0);

            if ($acceptedCount > 0 || $notifiedCount > 0) {
                DB::update(
                    'UPDATE blood_requests
                     SET status = ?, updated_at = SYSDATETIME()
                     WHERE id = ? AND status IN (?, ?, ?);',
                    ['Matched', $requestId, 'Pending', 'Matched', 'Approved']
                );
            } else {
                DB::update(
                    'UPDATE blood_requests
                     SET status = ?, updated_at = SYSDATETIME()
                     WHERE id = ? AND status IN (?, ?, ?);',
                    ['Pending', $requestId, 'Pending', 'Matched', 'Approved']
                );
            }

            $updatedNotification = DB::selectOne(
                'SELECT TOP 1
                    id,
                    donor_id,
                    request_id,
                    match_id,
                    notification_title,
                    notification_message,
                    status,
                    response_status,
                    sent_at,
                    read_at,
                    responded_at
                 FROM donor_notifications
                 WHERE id = ?;',
                [(int) $notification->id]
            );

            $requestRow = DB::selectOne(
                'SELECT TOP 1 id, status FROM blood_requests WHERE id = ?;',
                [$requestId]
            );

            $matchRow = $matchId > 0
                ? DB::selectOne(
                    'SELECT TOP 1 id, request_id, donor_id, status, responded_at
                     FROM blood_request_matches
                     WHERE id = ?;',
                    [$matchId]
                )
                : null;

            return [
                'notification' => $updatedNotification,
                'request' => $requestRow,
                'match' => $matchRow,
            ];
        });

        return $updated;
    }

    public function acceptedDonorsByRequestIds(array $requestIds): array
    {
        $ids = array_values(array_unique(array_map('intval', $requestIds)));
        if (empty($ids)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $rows = DB::select(
            "SELECT
                m.request_id,
                m.donor_id,
                COALESCE(NULLIF(u.full_name, N''), u.name) AS donor_name,
                u.email AS donor_email,
                dp.blood_group AS donor_blood_group,
                m.status,
                m.responded_at
             FROM blood_request_matches m
             INNER JOIN donor_profiles dp ON dp.donor_id = m.donor_id
             INNER JOIN users u ON u.id = dp.donor_id
             WHERE m.request_id IN ({$placeholders})
                AND m.status IN (N'Accepted', N'Completed')
             ORDER BY m.request_id, m.responded_at DESC, m.id DESC;",
            $ids
        );

        $grouped = [];
        foreach ($rows as $row) {
            $requestId = (int) $row->request_id;
            if (! array_key_exists($requestId, $grouped)) {
                $grouped[$requestId] = [];
            }

            $grouped[$requestId][] = [
                'donor_id' => (int) $row->donor_id,
                'donor_name' => $row->donor_name,
                'donor_email' => $row->donor_email,
                'donor_blood_group' => $row->donor_blood_group,
                'match_status' => $row->status,
                'responded_at' => $this->toIso($row->responded_at),
            ];
        }

        return $grouped;
    }

    private function departmentIdsForItWorker(int $userId): array
    {
        return array_map(
            fn (object $row): int => (int) $row->department_id,
            DB::select(
                'SELECT department_id FROM department_admins WHERE user_id = ?;',
                [$userId]
            )
        );
    }

    private function defaultNotificationMessage(object $request, object $suggestion): string
    {
        return 'Patient blood request #'.$request->id
            .' needs '.$request->units_required
            .' unit(s) of '.$request->blood_group_needed.' '.$request->component_type
            .'. Compatibility: '.$suggestion->compatibility_label
            .'. Please accept or decline from your donor dashboard.';
    }

    private function appendResponseNote(string $response, ?string $responseNote): string
    {
        $prefix = '['.$response.' via donor notification @ '.Carbon::now()->toDateTimeString().']';
        $note = trim((string) $responseNote);

        if ($note === '') {
            return $prefix;
        }

        return $prefix.' '.$note;
    }

    private function compatibleDonorGroups(string $recipientGroup): array
    {
        $map = [
            'O-' => ['O-'],
            'O+' => ['O+', 'O-'],
            'A-' => ['A-', 'O-'],
            'A+' => ['A+', 'A-', 'O+', 'O-'],
            'B-' => ['B-', 'O-'],
            'B+' => ['B+', 'B-', 'O+', 'O-'],
            'AB-' => ['AB-', 'A-', 'B-', 'O-'],
            'AB+' => ['AB+', 'AB-', 'A+', 'A-', 'B+', 'B-', 'O+', 'O-'],
        ];

        return $map[$recipientGroup] ?? [$recipientGroup];
    }

    private function toIso(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format(DATE_ATOM);
        }

        try {
            return Carbon::parse((string) $value)->toISOString();
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
