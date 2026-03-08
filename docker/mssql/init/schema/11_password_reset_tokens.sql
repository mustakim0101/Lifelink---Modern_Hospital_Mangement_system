IF OBJECT_ID(N'dbo.password_reset_tokens', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.password_reset_tokens (
        email NVARCHAR(255) NOT NULL PRIMARY KEY,
        token NVARCHAR(255) NOT NULL,
        created_at DATETIME2 NULL
    );
END
GO
