<?php

namespace App\Services\Sql;

use Illuminate\Support\Facades\DB;

class JobApplicationSqlService
{
    public function hasPendingApplication(int $userId): bool
    {
        $row = DB::selectOne(
            'SELECT COUNT(*) AS total FROM job_applications WHERE user_id = ? AND status = ?;',
            [$userId, 'Pending']
        );

        return ((int) ($row->total ?? 0)) > 0;
    }

    public function resolveRoleId(array $validated): int
    {
        if (isset($validated['applied_role_id'])) {
            $role = DB::selectOne('SELECT id FROM roles WHERE id = ?;', [(int) $validated['applied_role_id']]);
            if (! $role) {
                abort(422, 'Invalid applied_role_id.');
            }

            return (int) $role->id;
        }

        $roleName = $validated['appliedRole'] ?? 'ITWorker';
        $existing = DB::selectOne('SELECT id FROM roles WHERE role_name = ?;', [$roleName]);
        if ($existing) {
            return (int) $existing->id;
        }

        DB::insert(
            'INSERT INTO roles (role_name, description, created_at, updated_at) VALUES (?, ?, SYSDATETIME(), SYSDATETIME());',
            [$roleName, $roleName.' role']
        );

        $created = DB::selectOne('SELECT id FROM roles WHERE role_name = ?;', [$roleName]);
        if (! $created) {
            abort(500, 'Failed to create role.');
        }

        return (int) $created->id;
    }

    public function resolveDepartmentId(array $validated): ?int
    {
        $departmentId = $validated['departmentId'] ?? $validated['applied_department_id'] ?? null;
        if (! $departmentId) {
            return null;
        }

        $department = DB::selectOne('SELECT id FROM departments WHERE id = ?;', [(int) $departmentId]);
        if (! $department) {
            abort(422, 'Invalid department id.');
        }

        return (int) $department->id;
    }

    public function createApplication(int $userId, int $roleId, ?int $departmentId): int
    {
        DB::insert(
            'INSERT INTO job_applications (user_id, applied_role_id, applied_department_id, status, applied_at, created_at, updated_at)
             VALUES (?, ?, ?, ?, SYSDATETIME(), SYSDATETIME(), SYSDATETIME());',
            [$userId, $roleId, $departmentId, 'Pending']
        );

        $row = DB::selectOne(
            'SELECT TOP 1 id FROM job_applications WHERE user_id = ? ORDER BY id DESC;',
            [$userId]
        );

        return (int) $row->id;
    }

    public function assignApplicantRole(int $userId): void
    {
        $role = DB::selectOne('SELECT id FROM roles WHERE role_name = ?;', ['Applicant']);
        if (! $role) {
            DB::insert(
                'INSERT INTO roles (role_name, description, created_at, updated_at) VALUES (?, ?, SYSDATETIME(), SYSDATETIME());',
                ['Applicant', 'Applicant role']
            );
            $role = DB::selectOne('SELECT id FROM roles WHERE role_name = ?;', ['Applicant']);
        }

        $exists = DB::selectOne(
            'SELECT COUNT(*) AS total FROM user_roles WHERE user_id = ? AND role_id = ?;',
            [$userId, (int) $role->id]
        );

        if (((int) ($exists->total ?? 0)) === 0) {
            DB::insert(
                'INSERT INTO user_roles (user_id, role_id, assigned_at, assigned_by_user_id) VALUES (?, ?, SYSDATETIME(), NULL);',
                [$userId, (int) $role->id]
            );
        }
    }

    public function getMyApplications(int $userId): array
    {
        return DB::select(
            'SELECT ja.id,
                    ja.status,
                    ja.applied_at,
                    r.role_name AS applied_role,
                    ja.applied_department_id,
                    d.dept_name AS applied_department,
                    ja.reviewed_by_user_id,
                    ja.reviewed_at,
                    ja.review_notes
             FROM job_applications ja
             INNER JOIN roles r ON r.id = ja.applied_role_id
             LEFT JOIN departments d ON d.id = ja.applied_department_id
             WHERE ja.user_id = ?
             ORDER BY ja.applied_at DESC, ja.id DESC;',
            [$userId]
        );
    }

    public function getLatestApplication(int $userId): ?object
    {
        return DB::selectOne(
            'SELECT TOP 1 ja.id,
                           ja.status,
                           ja.applied_at,
                           r.role_name AS applied_role,
                           ja.applied_department_id,
                           d.dept_name AS applied_department,
                           ja.reviewed_by_user_id,
                           ja.reviewed_at,
                           ja.review_notes
             FROM job_applications ja
             INNER JOIN roles r ON r.id = ja.applied_role_id
             LEFT JOIN departments d ON d.id = ja.applied_department_id
             WHERE ja.user_id = ?
             ORDER BY ja.applied_at DESC, ja.id DESC;',
            [$userId]
        );
    }
}
