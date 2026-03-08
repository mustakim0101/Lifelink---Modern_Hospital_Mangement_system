IF OBJECT_ID(N'dbo.admissions', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.admissions (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        patient_user_id BIGINT NOT NULL,
        department_id BIGINT NOT NULL,
        admitted_by_doctor_id BIGINT NULL,
        diagnosis NVARCHAR(255) NOT NULL,
        care_level_requested NVARCHAR(20) NOT NULL,
        care_level_assigned NVARCHAR(20) NULL,
        status NVARCHAR(20) NOT NULL CONSTRAINT DF_admissions_status DEFAULT N'Admitted',
        admit_date DATETIME2 NOT NULL CONSTRAINT DF_admissions_admit_date DEFAULT SYSDATETIME(),
        discharge_date DATETIME2 NULL,
        notes NVARCHAR(MAX) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_admissions_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_admissions_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_admissions_patient FOREIGN KEY (patient_user_id) REFERENCES dbo.users(id) ON DELETE CASCADE,
        CONSTRAINT FK_admissions_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id),
        CONSTRAINT FK_admissions_admitted_by_doctor FOREIGN KEY (admitted_by_doctor_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_admissions_department_status ON dbo.admissions(department_id, status);
    CREATE INDEX IX_admissions_patient_user_id ON dbo.admissions(patient_user_id);
    CREATE INDEX IX_admissions_admitted_by_doctor_id ON dbo.admissions(admitted_by_doctor_id);
END
GO
