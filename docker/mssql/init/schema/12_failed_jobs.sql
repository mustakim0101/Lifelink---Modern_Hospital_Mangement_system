IF OBJECT_ID(N'dbo.failed_jobs', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.failed_jobs (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        uuid NVARCHAR(255) NOT NULL,
        connection NVARCHAR(MAX) NOT NULL,
        queue NVARCHAR(MAX) NOT NULL,
        payload NVARCHAR(MAX) NOT NULL,
        exception NVARCHAR(MAX) NOT NULL,
        failed_at DATETIME2 NOT NULL CONSTRAINT DF_failed_jobs_failed_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_failed_jobs_uuid UNIQUE (uuid)
    );
END
GO
