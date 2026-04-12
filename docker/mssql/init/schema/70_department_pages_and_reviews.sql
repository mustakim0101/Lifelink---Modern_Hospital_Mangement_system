IF COL_LENGTH('dbo.departments', 'slug') IS NULL
BEGIN
    ALTER TABLE dbo.departments ADD slug NVARCHAR(180) NULL;
END
GO

IF COL_LENGTH('dbo.departments', 'short_description') IS NULL
BEGIN
    ALTER TABLE dbo.departments ADD short_description NVARCHAR(255) NULL;
END
GO

IF COL_LENGTH('dbo.departments', 'banner_title') IS NULL
BEGIN
    ALTER TABLE dbo.departments ADD banner_title NVARCHAR(200) NULL;
END
GO

IF COL_LENGTH('dbo.departments', 'banner_description') IS NULL
BEGIN
    ALTER TABLE dbo.departments ADD banner_description NVARCHAR(MAX) NULL;
END
GO

IF COL_LENGTH('dbo.departments', 'organ_coverage_json') IS NULL
BEGIN
    ALTER TABLE dbo.departments ADD organ_coverage_json NVARCHAR(MAX) NULL;
END
GO

IF COL_LENGTH('dbo.departments', 'services_json') IS NULL
BEGIN
    ALTER TABLE dbo.departments ADD services_json NVARCHAR(MAX) NULL;
END
GO

IF COL_LENGTH('dbo.departments', 'sort_order') IS NULL
BEGIN
    ALTER TABLE dbo.departments ADD sort_order INT NULL;
END
GO

IF COL_LENGTH('dbo.departments', 'icon_key') IS NULL
BEGIN
    ALTER TABLE dbo.departments ADD icon_key NVARCHAR(100) NULL;
END
GO

SET QUOTED_IDENTIFIER ON;
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.departments')
      AND name = N'UX_departments_slug_not_null'
)
BEGIN
    CREATE UNIQUE INDEX UX_departments_slug_not_null
        ON dbo.departments(slug)
        WHERE slug IS NOT NULL;
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.departments')
      AND name = N'IX_departments_sort_order'
)
BEGIN
    CREATE INDEX IX_departments_sort_order
        ON dbo.departments(sort_order)
        WHERE sort_order IS NOT NULL;
END
GO

IF COL_LENGTH('dbo.doctors', 'years_experience') IS NULL
BEGIN
    ALTER TABLE dbo.doctors ADD years_experience INT NULL;
END
GO

IF COL_LENGTH('dbo.doctors', 'consultation_fee') IS NULL
BEGIN
    ALTER TABLE dbo.doctors ADD consultation_fee DECIMAL(10,2) NULL;
END
GO

IF COL_LENGTH('dbo.doctors', 'bio') IS NULL
BEGIN
    ALTER TABLE dbo.doctors ADD bio NVARCHAR(MAX) NULL;
END
GO

IF COL_LENGTH('dbo.doctors', 'profile_image_url') IS NULL
BEGIN
    ALTER TABLE dbo.doctors ADD profile_image_url NVARCHAR(500) NULL;
END
GO

IF OBJECT_ID(N'dbo.doctor_reviews', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.doctor_reviews (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        doctor_user_id BIGINT NOT NULL,
        patient_id BIGINT NOT NULL,
        department_id BIGINT NOT NULL,
        appointment_id BIGINT NULL,
        rating INT NOT NULL,
        review_text NVARCHAR(1000) NULL,
        is_visible BIT NOT NULL CONSTRAINT DF_doctor_reviews_is_visible DEFAULT 1,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_doctor_reviews_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_doctor_reviews_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT CK_doctor_reviews_rating CHECK (rating BETWEEN 1 AND 5),
        CONSTRAINT FK_doctor_reviews_doctor FOREIGN KEY (doctor_user_id) REFERENCES dbo.doctors(doctor_id),
        CONSTRAINT FK_doctor_reviews_patient FOREIGN KEY (patient_id) REFERENCES dbo.patients(patient_id),
        CONSTRAINT FK_doctor_reviews_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id),
        CONSTRAINT FK_doctor_reviews_appointment FOREIGN KEY (appointment_id) REFERENCES dbo.appointments(id)
    );
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.check_constraints
    WHERE name = N'CK_doctor_reviews_rating'
      AND parent_object_id = OBJECT_ID(N'dbo.doctor_reviews')
)
BEGIN
    ALTER TABLE dbo.doctor_reviews
    ADD CONSTRAINT CK_doctor_reviews_rating CHECK (rating BETWEEN 1 AND 5);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.foreign_keys
    WHERE name = N'FK_doctor_reviews_doctor'
      AND parent_object_id = OBJECT_ID(N'dbo.doctor_reviews')
)
BEGIN
    ALTER TABLE dbo.doctor_reviews
    ADD CONSTRAINT FK_doctor_reviews_doctor
        FOREIGN KEY (doctor_user_id) REFERENCES dbo.doctors(doctor_id);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.foreign_keys
    WHERE name = N'FK_doctor_reviews_patient'
      AND parent_object_id = OBJECT_ID(N'dbo.doctor_reviews')
)
BEGIN
    ALTER TABLE dbo.doctor_reviews
    ADD CONSTRAINT FK_doctor_reviews_patient
        FOREIGN KEY (patient_id) REFERENCES dbo.patients(patient_id);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.foreign_keys
    WHERE name = N'FK_doctor_reviews_department'
      AND parent_object_id = OBJECT_ID(N'dbo.doctor_reviews')
)
BEGIN
    ALTER TABLE dbo.doctor_reviews
    ADD CONSTRAINT FK_doctor_reviews_department
        FOREIGN KEY (department_id) REFERENCES dbo.departments(id);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.foreign_keys
    WHERE name = N'FK_doctor_reviews_appointment'
      AND parent_object_id = OBJECT_ID(N'dbo.doctor_reviews')
)
BEGIN
    ALTER TABLE dbo.doctor_reviews
    ADD CONSTRAINT FK_doctor_reviews_appointment
        FOREIGN KEY (appointment_id) REFERENCES dbo.appointments(id);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.doctor_reviews')
      AND name = N'IX_doctor_reviews_doctor'
)
BEGIN
    CREATE INDEX IX_doctor_reviews_doctor
        ON dbo.doctor_reviews(doctor_user_id);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.doctor_reviews')
      AND name = N'IX_doctor_reviews_department'
)
BEGIN
    CREATE INDEX IX_doctor_reviews_department
        ON dbo.doctor_reviews(department_id);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.doctor_reviews')
      AND name = N'IX_doctor_reviews_patient'
)
BEGIN
    CREATE INDEX IX_doctor_reviews_patient
        ON dbo.doctor_reviews(patient_id);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.doctor_reviews')
      AND name = N'IX_doctor_reviews_appointment'
)
BEGIN
    CREATE INDEX IX_doctor_reviews_appointment
        ON dbo.doctor_reviews(appointment_id);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.doctor_reviews')
      AND name = N'IX_doctor_reviews_doctor_visible_created'
)
BEGIN
    CREATE INDEX IX_doctor_reviews_doctor_visible_created
        ON dbo.doctor_reviews(doctor_user_id, is_visible, created_at);
END
GO

IF NOT EXISTS (
    SELECT 1
    FROM sys.indexes
    WHERE object_id = OBJECT_ID(N'dbo.doctor_reviews')
      AND name = N'UX_doctor_reviews_patient_doctor_appointment'
)
BEGIN
    CREATE UNIQUE INDEX UX_doctor_reviews_patient_doctor_appointment
        ON dbo.doctor_reviews(patient_id, doctor_user_id, appointment_id)
        WHERE appointment_id IS NOT NULL;
END
GO
