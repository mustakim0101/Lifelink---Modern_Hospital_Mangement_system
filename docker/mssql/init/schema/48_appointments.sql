IF OBJECT_ID(N'dbo.appointments', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.appointments (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        patient_id BIGINT NOT NULL,
        department_id BIGINT NOT NULL,
        doctor_user_id BIGINT NULL,
        appointment_datetime DATETIME2 NOT NULL,
        status NVARCHAR(20) NOT NULL CONSTRAINT DF_appointments_status DEFAULT N'Booked',
        cancelled_by_user_id BIGINT NULL,
        cancel_reason NVARCHAR(255) NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_appointments_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_appointments_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_appointments_patient FOREIGN KEY (patient_id) REFERENCES dbo.patients(patient_id) ON DELETE CASCADE,
        CONSTRAINT FK_appointments_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id),
        CONSTRAINT FK_appointments_doctor_user FOREIGN KEY (doctor_user_id) REFERENCES dbo.users(id),
        CONSTRAINT FK_appointments_cancelled_by FOREIGN KEY (cancelled_by_user_id) REFERENCES dbo.users(id)
    );

    CREATE INDEX IX_appointments_department_status ON dbo.appointments(department_id, status);
    CREATE INDEX IX_appointments_patient_id ON dbo.appointments(patient_id);
    CREATE INDEX IX_appointments_datetime ON dbo.appointments(appointment_datetime);
END
GO
