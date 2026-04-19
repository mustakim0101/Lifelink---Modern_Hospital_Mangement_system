IF OBJECT_ID(N'dbo.patients', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.patients (
        patient_id BIGINT NOT NULL PRIMARY KEY,
        blood_group NVARCHAR(5) NULL,
        emergency_contact_name NVARCHAR(150) NULL,
        emergency_contact_phone NVARCHAR(30) NULL,
        is_active BIT NOT NULL CONSTRAINT DF_patients_is_active DEFAULT 1,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_patients_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_patients_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_patients_user FOREIGN KEY (patient_id) REFERENCES dbo.users(id) ON DELETE CASCADE
    );
END
GO
