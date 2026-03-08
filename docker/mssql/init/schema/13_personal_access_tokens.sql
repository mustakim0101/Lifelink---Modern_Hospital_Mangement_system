IF OBJECT_ID(N'dbo.personal_access_tokens', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.personal_access_tokens (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        tokenable_type NVARCHAR(255) NOT NULL,
        tokenable_id BIGINT NOT NULL,
        name NVARCHAR(255) NOT NULL,
        token NVARCHAR(64) NOT NULL,
        abilities NVARCHAR(MAX) NULL,
        last_used_at DATETIME2 NULL,
        expires_at DATETIME2 NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_pat_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_pat_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_personal_access_tokens_token UNIQUE (token)
    );

    CREATE INDEX IX_personal_access_tokens_tokenable ON dbo.personal_access_tokens(tokenable_type, tokenable_id);
END
GO
