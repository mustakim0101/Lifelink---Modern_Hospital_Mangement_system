IF OBJECT_ID(N'dbo.job_applications', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.job_applications (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        user_id BIGINT NOT NULL,
        applied_role_id BIGINT NOT NULL,
        applied_department_id BIGINT NULL,
        status NVARCHAR(30) NOT NULL CONSTRAINT DF_job_applications_status DEFAULT N'Pending',
        applied_at DATETIME2 NOT NULL CONSTRAINT DF_job_applications_applied_at DEFAULT SYSDATETIME(),
        reviewed_by_user_id BIGINT NULL,
        reviewed_at DATETIME2 NULL,
        review_notes NVARCHAR(MAX) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_job_applications_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_job_applications_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_job_applications_user FOREIGN KEY (user_id) REFERENCES dbo.users(id),
        CONSTRAINT FK_job_applications_role FOREIGN KEY (applied_role_id) REFERENCES dbo.roles(id),
        CONSTRAINT FK_job_applications_dept FOREIGN KEY (applied_department_id) REFERENCES dbo.departments(id),
        CONSTRAINT FK_job_applications_reviewer FOREIGN KEY (reviewed_by_user_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_job_applications_status ON dbo.job_applications(status);
    CREATE INDEX IX_job_applications_user_status ON dbo.job_applications(user_id, status);
END
GO
