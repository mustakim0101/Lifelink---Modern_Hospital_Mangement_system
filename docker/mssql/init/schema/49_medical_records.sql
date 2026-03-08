IF OBJECT_ID(N'dbo.medical_records', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.medical_records (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        patient_id BIGINT NOT NULL,
        admission_id BIGINT NULL,
        created_by_user_id BIGINT NOT NULL,
        record_datetime DATETIME2 NOT NULL CONSTRAINT DF_medical_records_record_datetime DEFAULT SYSDATETIME(),
        diagnosis NVARCHAR(255) NOT NULL,
        treatment_plan NVARCHAR(MAX) NOT NULL,
        notes NVARCHAR(MAX) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_medical_records_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_medical_records_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_medical_records_patient FOREIGN KEY (patient_id) REFERENCES dbo.patients(patient_id) ON DELETE CASCADE,
        CONSTRAINT FK_medical_records_admission FOREIGN KEY (admission_id) REFERENCES dbo.admissions(id),
        CONSTRAINT FK_medical_records_created_by FOREIGN KEY (created_by_user_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_medical_records_patient_datetime ON dbo.medical_records(patient_id, record_datetime);
    CREATE INDEX IX_medical_records_admission_id ON dbo.medical_records(admission_id);
END
GO
