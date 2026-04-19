IF OBJECT_ID(N'dbo.roles', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.roles (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        role_name NVARCHAR(100) NOT NULL,
        description NVARCHAR(255) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_roles_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_roles_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_roles_role_name UNIQUE (role_name)
    );
END
GO
