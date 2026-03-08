<?php

namespace App\Services\Sql;

use Illuminate\Support\Facades\DB;

class ApplicationReviewSqlService
{
    public function listApplications(?string $status = null): array
    {
        $params = [];
        $statusClause = '';

        if ($status && in_array($status, ['Pending', 'Approved', 'Rejected'], true)) {
            $statusClause = 'WHERE ja.status = ?';
            $params[] = $status;
        }

        return DB::select(
            "SELECT TOP 200
                    ja.id,
                    ja.status,
                    ja.applied_at,
                    r.role_name AS applied_role,
                    ja.applied_department_id,
                    d.dept_name AS applied_department,
                    ja.reviewed_by_user_id,
                    reviewer.full_name AS reviewed_by_full_name,
                    reviewer.name AS reviewed_by_name,
                    ja.reviewed_at,
                    ja.review_notes,
                    u.id AS user_id,
                    u.email AS user_email,
                    u.full_name AS user_full_name,
                    u.name AS user_name
             FROM job_applications ja
             INNER JOIN users u ON u.id = ja.user_id
             INNER JOIN roles r ON r.id = ja.applied_role_id
             LEFT JOIN departments d ON d.id = ja.applied_department_id
             LEFT JOIN users reviewer ON reviewer.id = ja.reviewed_by_user_id
             {$statusClause}
             ORDER BY ja.applied_at DESC, ja.id DESC;",
            $params
        );
    }

    public function getApplicationById(int $applicationId): ?object
    {
        return DB::selectOne(
            'SELECT TOP 1
                    ja.id,
                    ja.user_id,
                    ja.applied_role_id,
                    ja.status,
                    ja.applied_at,
                    r.role_name AS applied_role,
                    ja.applied_department_id,
                    d.dept_name AS applied_department,
                    ja.reviewed_by_user_id,
                    reviewer.full_name AS reviewed_by_full_name,
                    reviewer.name AS reviewed_by_name,
                    ja.reviewed_at,
                    ja.review_notes,
                    u.email AS user_email,
                    u.full_name AS user_full_name,
                    u.name AS user_name
             FROM job_applications ja
             INNER JOIN users u ON u.id = ja.user_id
             INNER JOIN roles r ON r.id = ja.applied_role_id
             LEFT JOIN departments d ON d.id = ja.applied_department_id
             LEFT JOIN users reviewer ON reviewer.id = ja.reviewed_by_user_id
             WHERE ja.id = ?;',
            [$applicationId]
        );
    }

    public function approve(int $applicationId, int $reviewerId, ?string $reviewNotes): object
    {
        return DB::transaction(function () use ($applicationId, $reviewerId, $reviewNotes) {
            $application = $this->getApplicationById($applicationId);
            if (! $application) {
                abort(404, 'Application not found.');
            }

            if ($application->status !== 'Pending') {
                abort(409, 'Only pending applications can be approved.');
            }

            DB::update(
                'UPDATE job_applications
                 SET status = ?, reviewed_by_user_id = ?, reviewed_at = SYSDATETIME(), review_notes = ?, updated_at = SYSDATETIME()
                 WHERE id = ?;',
                ['Approved', $reviewerId, $reviewNotes, $applicationId]
            );

            $exists = DB::selectOne(
                'SELECT COUNT(*) AS total FROM user_roles WHERE user_id = ? AND role_id = ?;',
                [(int) $application->user_id, (int) $application->applied_role_id]
            );

            if (((int) ($exists->total ?? 0)) === 0) {
                DB::insert(
                    'INSERT INTO user_roles (user_id, role_id, assigned_at, assigned_by_user_id)
                     VALUES (?, ?, SYSDATETIME(), ?);',
                    [(int) $application->user_id, (int) $application->applied_role_id, $reviewerId]
                );
            }

            $applicantRole = DB::selectOne('SELECT id FROM roles WHERE role_name = ?;', ['Applicant']);
            if ($applicantRole) {
                DB::delete(
                    'DELETE FROM user_roles WHERE user_id = ? AND role_id = ?;',
                    [(int) $application->user_id, (int) $applicantRole->id]
                );
            }

            $updated = $this->getApplicationById($applicationId);
            if (! $updated) {
                abort(500, 'Unable to load approved application.');
            }

            return $updated;
        });
    }

    public function reject(int $applicationId, int $reviewerId, ?string $reviewNotes): object
    {
        return DB::transaction(function () use ($applicationId, $reviewerId, $reviewNotes) {
            $application = $this->getApplicationById($applicationId);
            if (! $application) {
                abort(404, 'Application not found.');
            }

            if ($application->status !== 'Pending') {
                abort(409, 'Only pending applications can be rejected.');
            }

            DB::update(
                'UPDATE job_applications
                 SET status = ?, reviewed_by_user_id = ?, reviewed_at = SYSDATETIME(), review_notes = ?, updated_at = SYSDATETIME()
                 WHERE id = ?;',
                ['Rejected', $reviewerId, $reviewNotes, $applicationId]
            );

            $updated = $this->getApplicationById($applicationId);
            if (! $updated) {
                abort(500, 'Unable to load rejected application.');
            }

            return $updated;
        });
    }
}
