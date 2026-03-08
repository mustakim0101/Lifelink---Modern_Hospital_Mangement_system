IF OBJECT_ID(N'dbo.blood_banks', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.blood_banks (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        bank_name NVARCHAR(150) NOT NULL,
        location NVARCHAR(255) NULL,
        is_active BIT NOT NULL CONSTRAINT DF_blood_banks_is_active DEFAULT 1,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_blood_banks_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_blood_banks_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_blood_banks_bank_name UNIQUE (bank_name)
    );
END
GO
