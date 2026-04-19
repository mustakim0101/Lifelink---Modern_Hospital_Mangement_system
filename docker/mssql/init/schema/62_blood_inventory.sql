IF OBJECT_ID(N'dbo.blood_inventory', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.blood_inventory (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        blood_bank_id BIGINT NOT NULL,
        blood_group NVARCHAR(5) NOT NULL,
        component_type NVARCHAR(30) NOT NULL CONSTRAINT DF_blood_inventory_component_type DEFAULT N'WholeBlood',
        units_available INT NOT NULL CONSTRAINT DF_blood_inventory_units DEFAULT 0,
        last_updated_at DATETIME2 NULL,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_blood_inventory_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_blood_inventory_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_blood_inventory_bank_group_component UNIQUE (blood_bank_id, blood_group, component_type),
        CONSTRAINT FK_blood_inventory_bank FOREIGN KEY (blood_bank_id) REFERENCES dbo.blood_banks(id) ON DELETE CASCADE
    );

    CREATE INDEX IX_blood_inventory_group_component ON dbo.blood_inventory(blood_group, component_type);
END
GO
