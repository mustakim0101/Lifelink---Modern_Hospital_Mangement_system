IF OBJECT_ID(N'dbo.donor_health_checks', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.donor_health_checks (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        donor_id BIGINT NOT NULL,
        check_datetime DATETIME2 NOT NULL,
        weight_kg DECIMAL(5,2) NOT NULL,
        temperature_c DECIMAL(4,2) NOT NULL,
        hemoglobin DECIMAL(4,2) NULL,
        notes NVARCHAR(MAX) NULL,
        checked_by_user_id BIGINT NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_donor_hc_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_donor_hc_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_donor_hc_donor FOREIGN KEY (donor_id) REFERENCES dbo.donor_profiles(donor_id) ON DELETE CASCADE,
        CONSTRAINT FK_donor_hc_checked_by FOREIGN KEY (checked_by_user_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_donor_hc_donor_check_datetime ON dbo.donor_health_checks(donor_id, check_datetime);
END
GO
