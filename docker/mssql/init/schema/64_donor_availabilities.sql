IF OBJECT_ID(N'dbo.donor_availabilities', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.donor_availabilities (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        donor_id BIGINT NOT NULL,
        week_start_date DATE NOT NULL,
        is_available BIT NOT NULL CONSTRAINT DF_donor_avail_is_available DEFAULT 1,
        max_bags_possible INT NOT NULL CONSTRAINT DF_donor_avail_max_bags DEFAULT 0,
        notes NVARCHAR(MAX) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_donor_avail_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_donor_avail_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_donor_avail_donor_week UNIQUE (donor_id, week_start_date),
        CONSTRAINT FK_donor_avail_donor FOREIGN KEY (donor_id) REFERENCES dbo.donor_profiles(donor_id) ON DELETE CASCADE
    );

    CREATE INDEX IX_donor_avail_week_available ON dbo.donor_availabilities(week_start_date, is_available);
END
GO
