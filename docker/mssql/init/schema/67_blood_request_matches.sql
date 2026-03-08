IF OBJECT_ID(N'dbo.blood_request_matches', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.blood_request_matches (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        request_id BIGINT NOT NULL,
        donor_id BIGINT NOT NULL,
        match_score DECIMAL(6,2) NULL,
        compatibility_label NVARCHAR(20) NOT NULL CONSTRAINT DF_blood_request_matches_compatibility DEFAULT N'Exact',
        status NVARCHAR(20) NOT NULL CONSTRAINT DF_blood_request_matches_status DEFAULT N'Suggested',
        notified_at DATETIME2 NULL,
        responded_at DATETIME2 NULL,
        selected_by_user_id BIGINT NULL,
        notes NVARCHAR(MAX) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_blood_request_matches_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_blood_request_matches_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_blood_request_matches_request_donor UNIQUE (request_id, donor_id),
        CONSTRAINT FK_blood_request_matches_request FOREIGN KEY (request_id) REFERENCES dbo.blood_requests(id) ON DELETE CASCADE,
        CONSTRAINT FK_blood_request_matches_donor FOREIGN KEY (donor_id) REFERENCES dbo.donor_profiles(donor_id) ON DELETE CASCADE,
        CONSTRAINT FK_blood_request_matches_selected_by FOREIGN KEY (selected_by_user_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_blood_request_matches_request_status ON dbo.blood_request_matches(request_id, status);
    CREATE INDEX IX_blood_request_matches_donor_status ON dbo.blood_request_matches(donor_id, status);
END
GO
