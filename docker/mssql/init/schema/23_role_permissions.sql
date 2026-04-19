IF OBJECT_ID(N'dbo.role_permissions', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.role_permissions (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        role_id BIGINT NOT NULL,
        permission_id BIGINT NOT NULL,
        granted_at DATETIME2 NOT NULL CONSTRAINT DF_role_permissions_granted_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_role_permissions_role_permission UNIQUE (role_id, permission_id),
        CONSTRAINT FK_role_permissions_role FOREIGN KEY (role_id) REFERENCES dbo.roles(id) ON DELETE CASCADE,
        CONSTRAINT FK_role_permissions_permission FOREIGN KEY (permission_id) REFERENCES dbo.permissions(id) ON DELETE CASCADE
    );

    CREATE INDEX IX_role_permissions_permission_id ON dbo.role_permissions(permission_id);
END
GO
