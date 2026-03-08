IF OBJECT_ID(N'dbo.donor_notifications', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.donor_notifications (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        donor_id BIGINT NOT NULL,
        request_id BIGINT NOT NULL,
        match_id BIGINT NULL,
        notification_title NVARCHAR(180) NOT NULL,
        notification_message NVARCHAR(MAX) NOT NULL,
        status NVARCHAR(20) NOT NULL CONSTRAINT DF_donor_notifications_status DEFAULT N'Sent',
        response_status NVARCHAR(20) NULL,
        sent_at DATETIME2 NOT NULL CONSTRAINT DF_donor_notifications_sent_at DEFAULT SYSDATETIME(),
        read_at DATETIME2 NULL,
        responded_at DATETIME2 NULL,
        created_by_user_id BIGINT NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_donor_notifications_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_donor_notifications_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_donor_notifications_donor FOREIGN KEY (donor_id) REFERENCES dbo.donor_profiles(donor_id),
        CONSTRAINT FK_donor_notifications_request FOREIGN KEY (request_id) REFERENCES dbo.blood_requests(id),
        CONSTRAINT FK_donor_notifications_match FOREIGN KEY (match_id) REFERENCES dbo.blood_request_matches(id),
        CONSTRAINT FK_donor_notifications_created_by FOREIGN KEY (created_by_user_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_donor_notifications_donor_status ON dbo.donor_notifications(donor_id, status);
    CREATE INDEX IX_donor_notifications_request_status ON dbo.donor_notifications(request_id, status);
    CREATE INDEX IX_donor_notifications_sent_at ON dbo.donor_notifications(sent_at);
END
GO
