IF OBJECT_ID(N'dbo.care_units', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.care_units (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        department_id BIGINT NOT NULL,
        unit_type NVARCHAR(20) NOT NULL,
        unit_name NVARCHAR(120) NULL,
        floor SMALLINT NULL,
        is_active BIT NOT NULL CONSTRAINT DF_care_units_is_active DEFAULT 1,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_care_units_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_care_units_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT FK_care_units_department FOREIGN KEY (department_id) REFERENCES dbo.departments(id) ON DELETE CASCADE
    );

    CREATE INDEX IX_care_units_department_unit_type ON dbo.care_units(department_id, unit_type);
    CREATE INDEX IX_care_units_is_active ON dbo.care_units(is_active);
END
GO
