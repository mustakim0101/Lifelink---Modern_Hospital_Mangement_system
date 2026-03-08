IF NOT EXISTS (SELECT 1 FROM dbo.blood_banks WHERE bank_name = N'LifeLink Central Blood Bank')
    INSERT INTO dbo.blood_banks (bank_name, location, is_active, created_at, updated_at)
    VALUES (N'LifeLink Central Blood Bank', N'Dhaka', 1, SYSDATETIME(), SYSDATETIME());
GO
