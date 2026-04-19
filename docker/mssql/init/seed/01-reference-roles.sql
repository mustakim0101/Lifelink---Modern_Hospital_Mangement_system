-- Roles
IF NOT EXISTS (SELECT 1 FROM dbo.roles WHERE role_name = N'Admin')
    INSERT INTO dbo.roles (role_name, description, created_at, updated_at) VALUES (N'Admin', N'Full access admin', SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.roles WHERE role_name = N'ITWorker')
    INSERT INTO dbo.roles (role_name, description, created_at, updated_at) VALUES (N'ITWorker', N'Department IT worker', SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.roles WHERE role_name = N'Doctor')
    INSERT INTO dbo.roles (role_name, description, created_at, updated_at) VALUES (N'Doctor', N'Doctor role', SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.roles WHERE role_name = N'Nurse')
    INSERT INTO dbo.roles (role_name, description, created_at, updated_at) VALUES (N'Nurse', N'Nurse role', SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.roles WHERE role_name = N'Patient')
    INSERT INTO dbo.roles (role_name, description, created_at, updated_at) VALUES (N'Patient', N'Patient role', SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.roles WHERE role_name = N'Applicant')
    INSERT INTO dbo.roles (role_name, description, created_at, updated_at) VALUES (N'Applicant', N'Applicant role', SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.roles WHERE role_name = N'Donor')
    INSERT INTO dbo.roles (role_name, description, created_at, updated_at) VALUES (N'Donor', N'Blood donor role', SYSDATETIME(), SYSDATETIME());
GO
