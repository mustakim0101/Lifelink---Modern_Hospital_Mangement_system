IF OBJECT_ID(N'dbo.beds', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.beds (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        care_unit_id BIGINT NOT NULL,
        bed_code NVARCHAR(50) NOT NULL,
        status NVARCHAR(20) NOT NULL CONSTRAINT DF_beds_status DEFAULT N'Available',
        is_active BIT NOT NULL CONSTRAINT DF_beds_is_active DEFAULT 1,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_beds_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_beds_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_beds_care_unit_code UNIQUE (care_unit_id, bed_code),
        CONSTRAINT FK_beds_care_unit FOREIGN KEY (care_unit_id) REFERENCES dbo.care_units(id) ON DELETE CASCADE
    );

    CREATE INDEX IX_beds_status_active ON dbo.beds(status, is_active);
END
GO
