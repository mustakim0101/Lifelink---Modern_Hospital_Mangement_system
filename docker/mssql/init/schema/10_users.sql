IF OBJECT_ID(N'dbo.users', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.users (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        name NVARCHAR(255) NOT NULL,
        email NVARCHAR(255) NOT NULL,
        email_verified_at DATETIME2 NULL,
        phone NVARCHAR(30) NULL,
        date_of_birth DATE NULL,
        gender NVARCHAR(20) NULL,
        password NVARCHAR(255) NOT NULL,
        account_status NVARCHAR(20) NOT NULL CONSTRAINT DF_users_account_status DEFAULT N'Active',
        remember_token NVARCHAR(100) NULL,
        frozen_at DATETIME2 NULL,
        frozen_by_user_id BIGINT NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_users_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_users_updated_at DEFAULT SYSDATETIME(),
        full_name NVARCHAR(255) NULL,
        CONSTRAINT UQ_users_email UNIQUE (email),
        CONSTRAINT FK_users_frozen_by FOREIGN KEY (frozen_by_user_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_users_account_status ON dbo.users(account_status);
END
GO
