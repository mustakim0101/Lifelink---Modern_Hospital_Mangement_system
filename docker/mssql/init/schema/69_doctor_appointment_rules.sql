IF OBJECT_ID(N'dbo.doctor_appointment_rules', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.doctor_appointment_rules (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        doctor_user_id BIGINT NOT NULL,
        department_id BIGINT NOT NULL,
        day_of_week TINYINT NOT NULL,
        start_time TIME NOT NULL,
        end_time TIME NOT NULL,
        daily_capacity INT NOT NULL,
        is_active BIT NOT NULL CONSTRAINT DF_doc_appt_rules_is_active DEFAULT 1,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_doc_appt_rules_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_doc_appt_rules_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_doc_appt_rules_doctor FOREIGN KEY (doctor_user_id) REFERENCES dbo.doctors(doctor_id) ON DELETE CASCADE,
        CONSTRAINT FK_doc_appt_rules_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id)
    );

    CREATE INDEX IX_doc_appt_rules_doctor_day_active
        ON dbo.doctor_appointment_rules(doctor_user_id, day_of_week, is_active);
    CREATE INDEX IX_doc_appt_rules_department_day_active
        ON dbo.doctor_appointment_rules(department_id, day_of_week, is_active);
END
GO

IF COL_LENGTH('dbo.appointments', 'appointment_date') IS NULL
BEGIN
    ALTER TABLE dbo.appointments ADD appointment_date DATE NULL;
END
GO

IF COL_LENGTH('dbo.appointments', 'approved_by_user_id') IS NULL
BEGIN
    ALTER TABLE dbo.appointments ADD approved_by_user_id BIGINT NULL;
END
GO

IF COL_LENGTH('dbo.appointments', 'approved_at') IS NULL
BEGIN
    ALTER TABLE dbo.appointments ADD approved_at DATETIME2 NULL;
END
GO

IF COL_LENGTH('dbo.appointments', 'rejection_reason') IS NULL
BEGIN
    ALTER TABLE dbo.appointments ADD rejection_reason NVARCHAR(255) NULL;
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.foreign_keys
    WHERE name = N'FK_appointments_approved_by'
)
BEGIN
    ALTER TABLE dbo.appointments
    ADD CONSTRAINT FK_appointments_approved_by
        FOREIGN KEY (approved_by_user_id) REFERENCES dbo.users(id);
END
GO

UPDATE dbo.appointments
SET appointment_date = CONVERT(date, appointment_datetime)
WHERE appointment_date IS NULL
  AND appointment_datetime IS NOT NULL;
GO

IF NOT EXISTS (
    SELECT 1 FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.appointments')
      AND name = N'IX_appointments_doctor_date_status'
)
BEGIN
    CREATE INDEX IX_appointments_doctor_date_status
        ON dbo.appointments(doctor_user_id, appointment_date, status);
END
GO

IF NOT EXISTS (
    SELECT 1 FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.appointments')
      AND name = N'IX_appointments_date'
)
BEGIN
    CREATE INDEX IX_appointments_date
        ON dbo.appointments(appointment_date);
END
GO

