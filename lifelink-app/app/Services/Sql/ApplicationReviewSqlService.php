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
                    CASE
                        WHEN r.role_name = N'Doctor' THEN doc.department_id
                        WHEN r.role_name = N'Nurse' THEN nur.department_id
                        WHEN r.role_name = N'ITWorker' THEN it_assign.department_id
                        ELSE ja.applied_department_id
                    END AS assigned_department_id,
                    CASE
                        WHEN r.role_name = N'Doctor' THEN doc_department.dept_name
                        WHEN r.role_name = N'Nurse' THEN nur_department.dept_name
                        WHEN r.role_name = N'ITWorker' THEN it_department.dept_name
                        ELSE d.dept_name
                    END AS assigned_department,
                    CASE
                        WHEN r.role_name IN (N'Doctor', N'Nurse', N'ITWorker') THEN CAST(1 AS BIT)
                        ELSE CAST(0 AS BIT)
                    END AS department_required,
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
             LEFT JOIN doctors doc ON doc.doctor_id = ja.user_id
             LEFT JOIN departments doc_department ON doc_department.id = doc.department_id
             LEFT JOIN nurses nur ON nur.nurse_id = ja.user_id
             LEFT JOIN departments nur_department ON nur_department.id = nur.department_id
             OUTER APPLY (
                 SELECT TOP 1 da.department_id
                 FROM department_admins da
                 WHERE da.user_id = ja.user_id
                 ORDER BY da.assigned_at DESC, da.id DESC
             ) it_assign
             LEFT JOIN departments it_department ON it_department.id = it_assign.department_id
             LEFT JOIN users reviewer ON reviewer.id = ja.reviewed_by_user_id
             {$statusClause}
             ORDER BY ja.applied_at DESC, ja.id DESC;",
            $params
        );
    }

    public function getApplicationById(int $applicationId): ?object
    {
        return DB::selectOne(
            "SELECT TOP 1
                    ja.id,
                    ja.user_id,
                    ja.applied_role_id,
                    ja.status,
                    ja.applied_at,
                    r.role_name AS applied_role,
                    ja.applied_department_id,
                    d.dept_name AS applied_department,
                    CASE
                        WHEN r.role_name = N'Doctor' THEN doc.department_id
                        WHEN r.role_name = N'Nurse' THEN nur.department_id
                        WHEN r.role_name = N'ITWorker' THEN it_assign.department_id
                        ELSE ja.applied_department_id
                    END AS assigned_department_id,
                    CASE
                        WHEN r.role_name = N'Doctor' THEN doc_department.dept_name
                        WHEN r.role_name = N'Nurse' THEN nur_department.dept_name
                        WHEN r.role_name = N'ITWorker' THEN it_department.dept_name
                        ELSE d.dept_name
                    END AS assigned_department,
                    CASE
                        WHEN r.role_name IN (N'Doctor', N'Nurse', N'ITWorker') THEN CAST(1 AS BIT)
                        ELSE CAST(0 AS BIT)
                    END AS department_required,
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
             LEFT JOIN doctors doc ON doc.doctor_id = ja.user_id
             LEFT JOIN departments doc_department ON doc_department.id = doc.department_id
             LEFT JOIN nurses nur ON nur.nurse_id = ja.user_id
             LEFT JOIN departments nur_department ON nur_department.id = nur.department_id
             OUTER APPLY (
                 SELECT TOP 1 da.department_id
                 FROM department_admins da
                 WHERE da.user_id = ja.user_id
                 ORDER BY da.assigned_at DESC, da.id DESC
             ) it_assign
             LEFT JOIN departments it_department ON it_department.id = it_assign.department_id
             LEFT JOIN users reviewer ON reviewer.id = ja.reviewed_by_user_id
             WHERE ja.id = ?;",
            [$applicationId]
        );
    }

    public function listActiveDepartments(): array
    {
        return DB::select(
            'SELECT id, dept_name
             FROM departments
             WHERE is_active = 1
             ORDER BY dept_name ASC;'
        );
    }

    public function approve(int $applicationId, int $reviewerId, ?string $reviewNotes, ?int $departmentId = null): object
    {
        return DB::transaction(function () use ($applicationId, $reviewerId, $reviewNotes, $departmentId) {
            $application = $this->getApplicationById($applicationId);
            if (! $application) {
                abort(404, 'Application not found.');
            }

            if ($application->status !== 'Pending') {
                abort(409, 'Only pending applications can be approved.');
            }

            $roleName = (string) ($application->applied_role ?? '');
            $requiresDepartment = $this->roleRequiresDepartment($roleName);
            $resolvedDepartmentId = $this->resolveDepartmentId($application, $departmentId, $requiresDepartment);

            DB::update(
                'UPDATE job_applications
                 SET status = ?, reviewed_by_user_id = ?, reviewed_at = SYSDATETIME(), review_notes = ?, applied_department_id = ?, updated_at = SYSDATETIME()
                 WHERE id = ?;',
                ['Approved', $reviewerId, $reviewNotes, $resolvedDepartmentId, $applicationId]
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

            if ($requiresDepartment && $resolvedDepartmentId !== null) {
                $this->syncRoleDepartmentAssignment((int) $application->user_id, $roleName, $resolvedDepartmentId);
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

    public function updateDepartment(int $applicationId, int $departmentId, ?string $reviewNotes = null): object
    {
        return DB::transaction(function () use ($applicationId, $departmentId, $reviewNotes) {
            $application = $this->getApplicationById($applicationId);
            if (! $application) {
                abort(404, 'Application not found.');
            }

            if (! in_array($application->status, ['Pending', 'Approved'], true)) {
                abort(409, 'Department can be updated only for pending or approved applications.');
            }

            $roleName = (string) ($application->applied_role ?? '');
            if (! $this->roleRequiresDepartment($roleName)) {
                abort(422, 'Department assignment is only required for Doctor, Nurse, and ITWorker applications.');
            }

            $resolvedDepartmentId = $this->requireDepartmentId($departmentId);

            if ($reviewNotes !== null && trim($reviewNotes) !== '') {
                DB::update(
                    'UPDATE job_applications
                     SET applied_department_id = ?, review_notes = ?, updated_at = SYSDATETIME()
                     WHERE id = ?;',
                    [$resolvedDepartmentId, $reviewNotes, $applicationId]
                );
            } else {
                DB::update(
                    'UPDATE job_applications
                     SET applied_department_id = ?, updated_at = SYSDATETIME()
                     WHERE id = ?;',
                    [$resolvedDepartmentId, $applicationId]
                );
            }

            if ($application->status === 'Approved') {
                $this->syncRoleDepartmentAssignment((int) $application->user_id, $roleName, $resolvedDepartmentId);
            }

            $updated = $this->getApplicationById($applicationId);
            if (! $updated) {
                abort(500, 'Unable to load updated application.');
            }

            return $updated;
        });
    }

    private function roleRequiresDepartment(string $roleName): bool
    {
        return in_array($roleName, ['Doctor', 'Nurse', 'ITWorker'], true);
    }

    private function resolveDepartmentId(object $application, ?int $requestedDepartmentId, bool $required): ?int
    {
        $candidate = $requestedDepartmentId;

        if ($candidate === null) {
            $candidate = $application->assigned_department_id ?? $application->applied_department_id ?? null;
        }

        if ($candidate === null) {
            if ($required) {
                abort(422, 'Department assignment is required for Doctor, Nurse, and ITWorker approvals.');
            }

            return null;
        }

        return $this->requireDepartmentId((int) $candidate);
    }

    private function requireDepartmentId(int $departmentId): int
    {
        $department = DB::selectOne(
            'SELECT id FROM departments WHERE id = ? AND is_active = 1;',
            [$departmentId]
        );

        if (! $department) {
            abort(422, 'Selected department is invalid or inactive.');
        }

        return (int) $department->id;
    }

    private function syncRoleDepartmentAssignment(int $userId, string $roleName, int $departmentId): void
    {
        if ($roleName === 'Doctor') {
            $existing = DB::selectOne('SELECT doctor_id FROM doctors WHERE doctor_id = ?;', [$userId]);
            if ($existing) {
                DB::update(
                    'UPDATE doctors
                     SET department_id = ?, is_active = 1, updated_at = SYSDATETIME()
                     WHERE doctor_id = ?;',
                    [$departmentId, $userId]
                );
            } else {
                DB::insert(
                    'INSERT INTO doctors (doctor_id, department_id, is_active, created_at, updated_at)
                     VALUES (?, ?, 1, SYSDATETIME(), SYSDATETIME());',
                    [$userId, $departmentId]
                );
            }

            return;
        }

        if ($roleName === 'Nurse') {
            $existing = DB::selectOne('SELECT nurse_id FROM nurses WHERE nurse_id = ?;', [$userId]);
            if ($existing) {
                DB::update(
                    'UPDATE nurses
                     SET department_id = ?, is_active = 1, updated_at = SYSDATETIME()
                     WHERE nurse_id = ?;',
                    [$departmentId, $userId]
                );
            } else {
                DB::insert(
                    'INSERT INTO nurses (nurse_id, department_id, ward_assignment_note, is_active, created_at, updated_at)
                     VALUES (?, ?, NULL, 1, SYSDATETIME(), SYSDATETIME());',
                    [$userId, $departmentId]
                );
            }

            return;
        }

        if ($roleName === 'ITWorker') {
            DB::delete(
                'DELETE FROM department_admins WHERE user_id = ?;',
                [$userId]
            );

            DB::insert(
                'INSERT INTO department_admins (user_id, department_id, assigned_at)
                 VALUES (?, ?, SYSDATETIME());',
                [$userId, $departmentId]
            );
        }
    }
}
