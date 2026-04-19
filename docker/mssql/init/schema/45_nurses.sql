IF OBJECT_ID(N'dbo.nurses', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.nurses (
        nurse_id BIGINT NOT NULL PRIMARY KEY,
        department_id BIGINT NOT NULL,
        ward_assignment_note NVARCHAR(255) NULL,
        is_active BIT NOT NULL CONSTRAINT DF_nurses_is_active DEFAULT 1,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_nurses_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_nurses_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_nurses_user FOREIGN KEY (nurse_id) REFERENCES dbo.users(id) ON DELETE CASCADE,
        CONSTRAINT FK_nurses_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id)
    );

    CREATE INDEX IX_nurses_department_id ON dbo.nurses(department_id);
END
GO
