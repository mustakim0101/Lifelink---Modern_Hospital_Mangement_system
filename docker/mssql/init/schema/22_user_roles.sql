IF OBJECT_ID(N'dbo.user_roles', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.user_roles (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        user_id BIGINT NOT NULL,
        role_id BIGINT NOT NULL,
        assigned_at DATETIME2 NOT NULL CONSTRAINT DF_user_roles_assigned_at DEFAULT SYSDATETIME(),
        assigned_by_user_id BIGINT NULL,
        CONSTRAINT UQ_user_roles_user_role UNIQUE (user_id, role_id),
        CONSTRAINT FK_user_roles_user FOREIGN KEY (user_id) REFERENCES dbo.users(id) ON DELETE CASCADE,
        CONSTRAINT FK_user_roles_role FOREIGN KEY (role_id) REFERENCES dbo.roles(id) ON DELETE CASCADE,
        CONSTRAINT FK_user_roles_assigned_by FOREIGN KEY (assigned_by_user_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_user_roles_role_id ON dbo.user_roles(role_id);
END
GO
