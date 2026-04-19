IF OBJECT_ID(N'dbo.doctors', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.doctors (
        doctor_id BIGINT NOT NULL PRIMARY KEY,
        department_id BIGINT NOT NULL,
        specialization NVARCHAR(150) NULL,
        license_number NVARCHAR(100) NULL,
        is_active BIT NOT NULL CONSTRAINT DF_doctors_is_active DEFAULT 1,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_doctors_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_doctors_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_doctors_user FOREIGN KEY (doctor_id) REFERENCES dbo.users(id) ON DELETE CASCADE,
        CONSTRAINT FK_doctors_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id)
    );

    CREATE INDEX IX_doctors_department_id ON dbo.doctors(department_id);
    CREATE INDEX IX_doctors_license_number ON dbo.doctors(license_number);
END
GO
