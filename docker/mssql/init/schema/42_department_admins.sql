IF OBJECT_ID(N'dbo.department_admins', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.department_admins (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        user_id BIGINT NOT NULL,
        department_id BIGINT NOT NULL,
        assigned_at DATETIME2 NOT NULL CONSTRAINT DF_department_admins_assigned_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_department_admins_user_department UNIQUE (user_id, department_id),
        CONSTRAINT FK_department_admins_user FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE,
        CONSTRAINT FK_department_admins_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id) ON DELETE CASCADE
    );

    CREATE INDEX IX_department_admins_department_id ON dbo.department_admins(department_id);
END
GO
