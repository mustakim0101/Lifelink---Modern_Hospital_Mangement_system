IF OBJECT_ID(N'dbo.departments', N'U') IS NULL
BEGIN
    CREATE TABLE dbo.departments (
        id BIGINT IDENTITY(1,1) NOT NULL PRIMARY KEY,
        dept_name NVARCHAR(150) NOT NULL,
        is_active BIT NOT NULL CONSTRAINT DF_departments_is_active DEFAULT 1,
        created_at DATETIME2 NOT NULL CONSTRAINT DF_departments_created_at DEFAULT SYSDATETIME(),
        updated_at DATETIME2 NOT NULL CONSTRAINT DF_departments_updated_at DEFAULT SYSDATETIME(),
        CONSTRAINT UQ_departments_dept_name UNIQUE (dept_name)
    );
END
GO
