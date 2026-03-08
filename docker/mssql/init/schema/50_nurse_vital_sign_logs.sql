IF OBJECT_ID(N'dbo.nurse_vital_sign_logs', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.nurse_vital_sign_logs (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        admission_id BIGINT NOT NULL,
        patient_id BIGINT NOT NULL,
        nurse_id BIGINT NOT NULL,
        measured_at DATETIME2 NOT NULL CONSTRAINT DF_nurse_vital_measured_at DEFAULT SYSDATETIME(),
        temperature_c DECIMAL(4,1) NULL,
        pulse_bpm SMALLINT NULL,
        systolic_bp SMALLINT NULL,
        diastolic_bp SMALLINT NULL,
        respiration_rate SMALLINT NULL,
        spo2_percent TINYINT NULL,
        note NVARCHAR(MAX) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_nurse_vital_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_nurse_vital_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_nurse_vital_admission FOREIGN KEY (admission_id) REFERENCES dbo.admissions(id) ON DELETE CASCADE,
        CONSTRAINT FK_nurse_vital_patient FOREIGN KEY (patient_id) REFERENCES dbo.patients(patient_id),
        CONSTRAINT FK_nurse_vital_nurse FOREIGN KEY (nurse_id) REFERENCES dbo.nurses(nurse_id)
    );

    CREATE INDEX IX_nurse_vital_admission_measured ON dbo.nurse_vital_sign_logs(admission_id, measured_at);
    CREATE INDEX IX_nurse_vital_patient_measured ON dbo.nurse_vital_sign_logs(patient_id, measured_at);
    CREATE INDEX IX_nurse_vital_nurse_measured ON dbo.nurse_vital_sign_logs(nurse_id, measured_at);
END
GO
