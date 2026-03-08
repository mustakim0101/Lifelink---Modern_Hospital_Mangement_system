IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Cardiology')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Cardiology', 1, SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Neurology')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Neurology', 1, SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Orthopedics')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Orthopedics', 1, SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Pediatrics')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Pediatrics', 1, SYSDATETIME(), SYSDATETIME());
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'General Medicine')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'General Medicine', 1, SYSDATETIME(), SYSDATETIME());
GO
