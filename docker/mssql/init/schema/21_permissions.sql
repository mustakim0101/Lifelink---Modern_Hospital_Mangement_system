IF OBJECT_ID(N'dbo.permissions', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.permissions (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        permission_code NVARCHAR(120) NOT NULL,
        description NVARCHAR(255) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_permissions_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_permissions_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_permissions_permission_code UNIQUE (permission_code)
    );
END
GO
