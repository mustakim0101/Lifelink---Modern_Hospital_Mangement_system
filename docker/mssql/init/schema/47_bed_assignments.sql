IF OBJECT_ID(N'dbo.bed_assignments', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.bed_assignments (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        admission_id BIGINT NOT NULL,
        bed_id BIGINT NOT NULL,
        assigned_by_user_id BIGINT NOT NULL,
        assigned_at DATETIME2 NOT NULL CONSTRAINT DF_bed_assignments_assigned_at DEFAULT SYSDATETIME(),
        released_at DATETIME2 NULL,
        released_by_user_id BIGINT NULL,
        release_reason NVARCHAR(40) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_bed_assignments_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_bed_assignments_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_bed_assignments_admission FOREIGN KEY (admission_id) REFERENCES dbo.admissions(id) ON DELETE CASCADE,
        CONSTRAINT FK_bed_assignments_bed FOREIGN KEY (bed_id) REFERENCES dbo.beds(id),
        CONSTRAINT FK_bed_assignments_assigned_by FOREIGN KEY (assigned_by_user_id) REFERENCES dbo.users(id),
        CONSTRAINT FK_bed_assignments_released_by FOREIGN KEY (released_by_user_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_bed_assignments_admission_released ON dbo.bed_assignments(admission_id, released_at);
    CREATE INDEX IX_bed_assignments_bed_released ON dbo.bed_assignments(bed_id, released_at);
END
GO
