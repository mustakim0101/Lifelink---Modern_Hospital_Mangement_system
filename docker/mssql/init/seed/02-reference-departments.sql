SET QUOTED_IDENTIFIER ON;
GO

DECLARE @now DATETIME2 = SYSDATETIME();

IF EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Cardiology')
   AND NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Cardiology & Vascular Medicine')
BEGIN
    UPDATE dbo.departments
    SET dept_name = N'Cardiology & Vascular Medicine',
        updated_at = @now
    WHERE dept_name = N'Cardiology';
END

IF EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Neurology')
   AND NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Neurology & Neurosurgery')
BEGIN
    UPDATE dbo.departments
    SET dept_name = N'Neurology & Neurosurgery',
        updated_at = @now
    WHERE dept_name = N'Neurology';
END

IF EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Orthopedics')
   AND NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Orthopedics & Musculoskeletal Care')
BEGIN
    UPDATE dbo.departments
    SET dept_name = N'Orthopedics & Musculoskeletal Care',
        updated_at = @now
    WHERE dept_name = N'Orthopedics';
END

IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Neurology & Neurosurgery')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Neurology & Neurosurgery', 1, @now, @now);
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Cardiology & Vascular Medicine')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Cardiology & Vascular Medicine', 1, @now, @now);
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Pulmonology')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Pulmonology', 1, @now, @now);
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Gastroenterology & Hepatology')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Gastroenterology & Hepatology', 1, @now, @now);
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Nephrology & Urology')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Nephrology & Urology', 1, @now, @now);
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Orthopedics & Musculoskeletal Care')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Orthopedics & Musculoskeletal Care', 1, @now, @now);
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Pediatrics')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Pediatrics', 1, @now, @now);
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'General Medicine')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'General Medicine', 1, @now, @now);
IF NOT EXISTS (SELECT 1 FROM dbo.departments WHERE dept_name = N'Blood Bank')
    INSERT INTO dbo.departments (dept_name, is_active, created_at, updated_at) VALUES (N'Blood Bank', 1, @now, @now);

IF COL_LENGTH('dbo.departments', 'slug') IS NOT NULL
BEGIN
    UPDATE dbo.departments
    SET slug = N'neurology-neurosurgery',
        short_description = N'Diagnosis and treatment for conditions involving the brain, skull, and peripheral nerves.',
        banner_title = N'Neurology & Neurosurgery',
        banner_description = N'Comprehensive evaluation and treatment for headaches, stroke risk, nerve disorders, seizure care, and neurosurgical follow-up.',
        organ_coverage_json = N'["Brain","Skull","Nerve"]',
        services_json = N'["Neurology consultation","Stroke and seizure assessment","Nerve conduction support","Neurosurgery referral planning","Chronic headache management"]',
        sort_order = 1,
        icon_key = N'brain',
        updated_at = @now
    WHERE dept_name = N'Neurology & Neurosurgery';

    UPDATE dbo.departments
    SET slug = N'cardiology-vascular-medicine',
        short_description = N'Heart and circulation-focused care for acute and long-term cardiovascular health.',
        banner_title = N'Cardiology & Vascular Medicine',
        banner_description = N'Focused cardiac and vascular assessment including hypertension support, rhythm evaluation, and circulation monitoring.',
        organ_coverage_json = N'["Heart","Blood vessels"]',
        services_json = N'["Cardiology consultation","Blood pressure management","Cardiac risk assessment","Vascular circulation review","Chest pain triage support"]',
        sort_order = 2,
        icon_key = N'heart',
        updated_at = @now
    WHERE dept_name = N'Cardiology & Vascular Medicine';

    UPDATE dbo.departments
    SET slug = N'pulmonology',
        short_description = N'Respiratory care for lung-related symptoms, acute breathing issues, and chronic pulmonary conditions.',
        banner_title = N'Pulmonology',
        banner_description = N'Clinical pathways for cough, breathlessness, infection follow-up, and long-term pulmonary disease management.',
        organ_coverage_json = N'["Lung"]',
        services_json = N'["Pulmonary consultation","Respiratory symptom triage","Chronic lung follow-up","Oxygenation monitoring","Breathing function support"]',
        sort_order = 3,
        icon_key = N'lungs',
        updated_at = @now
    WHERE dept_name = N'Pulmonology';

    UPDATE dbo.departments
    SET slug = N'gastroenterology-hepatology',
        short_description = N'Digestive and liver care for upper and lower gastrointestinal symptoms and hepatology follow-up.',
        banner_title = N'Gastroenterology & Hepatology',
        banner_description = N'Evaluation of digestive complaints, bowel issues, liver-related symptoms, and coordinated long-term digestive care.',
        organ_coverage_json = N'["Oesophagus","Stomach","Small intestine","Large intestine","Liver"]',
        services_json = N'["Digestive consultation","Liver disease follow-up","Abdominal symptom triage","Bowel pattern evaluation","Nutrition-linked GI guidance"]',
        sort_order = 4,
        icon_key = N'digestive',
        updated_at = @now
    WHERE dept_name = N'Gastroenterology & Hepatology';

    UPDATE dbo.departments
    SET slug = N'nephrology-urology',
        short_description = N'Kidney and urinary-system care for fluid balance, renal symptoms, and bladder health management.',
        banner_title = N'Nephrology & Urology',
        banner_description = N'Integrated nephrology and urology support for kidney function review, urinary concerns, and chronic renal monitoring.',
        organ_coverage_json = N'["Kidneys","Bladder"]',
        services_json = N'["Kidney consultation","Urinary symptom assessment","Renal function follow-up","Fluid and electrolyte review","Bladder health pathways"]',
        sort_order = 5,
        icon_key = N'kidney',
        updated_at = @now
    WHERE dept_name = N'Nephrology & Urology';

    UPDATE dbo.departments
    SET slug = N'orthopedics-musculoskeletal-care',
        short_description = N'Musculoskeletal care for injury, mobility, and pain across bones, joints, and muscle systems.',
        banner_title = N'Orthopedics & Musculoskeletal Care',
        banner_description = N'Coordinated orthopedic care for fractures, joint pain, muscle strain, and rehabilitation-oriented treatment plans.',
        organ_coverage_json = N'["Bone","Joint","Muscle"]',
        services_json = N'["Orthopedic consultation","Fracture and trauma follow-up","Joint mobility evaluation","Muscle strain management","Rehabilitation planning"]',
        sort_order = 6,
        icon_key = N'bone',
        updated_at = @now
    WHERE dept_name = N'Orthopedics & Musculoskeletal Care';

    UPDATE dbo.departments
    SET is_active = 0,
        updated_at = @now
    WHERE dept_name = N'Hematology & Lymphatic Care'
       OR slug = N'hematology-lymphatic-care';

    UPDATE dbo.departments
    SET slug = N'pediatrics',
        short_description = COALESCE(short_description, N'Child-focused clinical care for preventive, acute, and developmental health needs.'),
        banner_title = COALESCE(banner_title, N'Pediatrics'),
        banner_description = COALESCE(banner_description, N'Pediatric pathways for child wellness, fever care, growth monitoring, and age-specific treatment support.'),
        organ_coverage_json = COALESCE(organ_coverage_json, N'["Child care"]'),
        services_json = COALESCE(services_json, N'["Pediatric consultation","Child wellness checks","Vaccination guidance","Growth and development monitoring","Fever and acute symptom triage"]'),
        sort_order = COALESCE(sort_order, 8),
        icon_key = COALESCE(icon_key, N'child'),
        updated_at = @now
    WHERE dept_name = N'Pediatrics';

    UPDATE dbo.departments
    SET slug = N'blood-bank',
        short_description = COALESCE(short_description, N'Blood request coordination, donor management, and transfusion support services.'),
        banner_title = COALESCE(banner_title, N'Blood Bank'),
        banner_description = COALESCE(banner_description, N'Operational center for blood availability, donor support, and urgent transfusion-linked workflows.'),
        organ_coverage_json = COALESCE(organ_coverage_json, N'["Blood","Donation"]'),
        services_json = COALESCE(services_json, N'["Blood request triage","Donor coordination","Inventory tracking","Donation logging","Fulfillment and notification support"]'),
        sort_order = COALESCE(sort_order, 9),
        icon_key = COALESCE(icon_key, N'blood-bank'),
        updated_at = @now
    WHERE dept_name = N'Blood Bank';
END
GO
