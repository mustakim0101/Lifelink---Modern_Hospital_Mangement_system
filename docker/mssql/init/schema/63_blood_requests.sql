IF OBJECT_ID(N'dbo.blood_requests', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.blood_requests (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        patient_id BIGINT NOT NULL,
        admission_id BIGINT NULL,
        department_id BIGINT NOT NULL,
        requested_by_user_id BIGINT NOT NULL,
        blood_group_needed NVARCHAR(5) NOT NULL,
        component_type NVARCHAR(30) NOT NULL CONSTRAINT DF_blood_requests_component_type DEFAULT N'WholeBlood',
        units_required INT NOT NULL,
        urgency NVARCHAR(20) NOT NULL CONSTRAINT DF_blood_requests_urgency DEFAULT N'Urgent',
        status NVARCHAR(20) NOT NULL CONSTRAINT DF_blood_requests_status DEFAULT N'Pending',
        request_date DATETIME2 NOT NULL CONSTRAINT DF_blood_requests_request_date DEFAULT SYSDATETIME(),
        notes NVARCHAR(MAX) NULL,
        blood_bank_id BIGINT NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_blood_requests_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_blood_requests_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_blood_requests_patient FOREIGN KEY (patient_id) REFERENCES dbo.patients(patient_id),
        CONSTRAINT FK_blood_requests_admission FOREIGN KEY (admission_id) REFERENCES dbo.admissions(id),
        CONSTRAINT FK_blood_requests_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id),
        CONSTRAINT FK_blood_requests_requested_by FOREIGN KEY (requested_by_user_id) REFERENCES dbo.users(id),
        CONSTRAINT FK_blood_requests_blood_bank FOREIGN KEY (blood_bank_id) REFERENCES dbo.blood_banks(id) ON DELETE SET NULL
    );

    CREATE INDEX IX_blood_requests_patient_status ON dbo.blood_requests(patient_id, status);
    CREATE INDEX IX_blood_requests_department_status ON dbo.blood_requests(department_id, status);
    CREATE INDEX IX_blood_requests_request_date ON dbo.blood_requests(request_date);
    CREATE INDEX IX_blood_requests_bank_status ON dbo.blood_requests(blood_bank_id, status);
END
GO
