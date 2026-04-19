IF OBJECT_ID(N'dbo.donor_profiles', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.donor_profiles (
        donor_id BIGINT NOT NULL PRIMARY KEY,
        blood_group NVARCHAR(5) NOT NULL,
        last_donation_date DATETIME2 NULL,
        is_eligible BIT NOT NULL CONSTRAINT DF_donor_profiles_is_eligible DEFAULT 1,
        notes NVARCHAR(MAX) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_donor_profiles_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_donor_profiles_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_donor_profiles_user FOREIGN KEY (donor_id) REFERENCES dbo.users(id) ON DELETE CASCADE
    );

    CREATE INDEX IX_donor_profiles_group_eligible ON dbo.donor_profiles(blood_group, is_eligible);
END
GO
