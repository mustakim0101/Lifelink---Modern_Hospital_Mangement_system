IF OBJECT_ID(N'dbo.blood_donations', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.blood_donations (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        donor_id BIGINT NOT NULL,
        blood_bank_id BIGINT NOT NULL,
        donation_datetime DATETIME2 NOT NULL,
        blood_group NVARCHAR(5) NOT NULL,
        component_type NVARCHAR(30) NOT NULL CONSTRAINT DF_blood_donations_component_type DEFAULT N'WholeBlood',
        units_donated INT NOT NULL,
        recorded_by_user_id BIGINT NULL,
        linked_request_id BIGINT NULL,
        donor_health_check_id BIGINT NULL,
        notes NVARCHAR(MAX) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_blood_donations_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_blood_donations_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_blood_donations_donor FOREIGN KEY (donor_id) REFERENCES dbo.donor_profiles(donor_id) ON DELETE CASCADE,
        CONSTRAINT FK_blood_donations_bank FOREIGN KEY (blood_bank_id) REFERENCES dbo.blood_banks(id),
        CONSTRAINT FK_blood_donations_recorded_by FOREIGN KEY (recorded_by_user_id) REFERENCES dbo.users(id),
        CONSTRAINT FK_blood_donations_linked_request FOREIGN KEY (linked_request_id) REFERENCES dbo.blood_requests(id) ON DELETE SET NULL,
        CONSTRAINT FK_blood_donations_health_check FOREIGN KEY (donor_health_check_id) REFERENCES dbo.donor_health_checks(id)
    );

    CREATE INDEX IX_blood_donations_donor_datetime ON dbo.blood_donations(donor_id, donation_datetime);
    CREATE INDEX IX_blood_donations_bank_group_component ON dbo.blood_donations(blood_bank_id, blood_group, component_type);
END
GO
