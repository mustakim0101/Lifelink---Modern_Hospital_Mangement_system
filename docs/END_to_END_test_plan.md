
**Full End-to-End Test Plan**

This is the updated, cleaner test plan based on your current codebase and current workflow.

We will use:
- `Admin`: bootstrap admin
- `Patient A`: `patienta@gmail.com`
- `Patient B`: `patientb@gmail.com`
- `Doctor 1`: `doctorone@gmail.com`
- `Doctor 2`: `doctortwo@gmail.com`
- `Nurse 1`: `nurseone@gmail.com`
- `IT Worker 1`: `itone@gmail.com`

We will test:
- registration
- applicant approval
- doctor setup
- nurse setup
- IT setup
- doctor creating admissions
- IT creating/assigning beds
- nurse logging vitals
- discharge flow

---

**Part 1: Initial Setup**

1. Start the project
- run Docker
- confirm app opens at `http://localhost:8000`

2. Create first admin
- go to `/ui/login`
- use the bootstrap admin section
- create admin account
- log in as admin
- confirm redirect goes to `/ui/admin-users`

Expected DB result:
- row in `users`
- `Admin` role in `user_roles`

---

**Part 2: Create Two Patient Accounts**

3. Register Patient A
- open `/ui/register/patient`
- enter:
  - full name
  - `patienta@gmail.com`
  - password
  - blood group if wanted
- submit
- log in as Patient A

Expected:
- redirect to `/ui/patient-portal`

Expected DB result:
- row in `users`
- row in `patients`
- `Patient` role assigned

4. Register Patient B
- repeat same process with `patientb@gmail.com`

Expected DB result:
- second patient user row
- second patient profile row
- `Patient` role assigned

At this point you should have:
- 2 patient accounts

---

**Part 3: Create Two Doctor Applicants**

5. Register Doctor 1 as applicant
- open `/ui/register/applicant`
- fill:
  - name
  - `doctorone@gmail.com`
  - password
  - role = `Doctor`
  - department = for example `Cardiology`
- submit
- log in as Doctor 1 applicant

Expected:
- redirect to `/ui/applications`
- not doctor dashboard yet

Expected DB result:
- row in `users`
- `Patient` role exists because base registration gives it
- `Applicant` role exists
- one row in `job_applications`
- status = `Pending`
- applied role = `Doctor`
- applied department = `Cardiology`

6. Register Doctor 2 as applicant
- same process
- use `doctortwo@gmail.com`
- choose a different department, for example `Orthopedics`

Expected:
- second pending doctor application

At this point:
- 2 doctor applicants are pending

---

**Part 4: Admin Approves Both Doctors**

7. Login as admin
- go to `/ui/admin-users`

8. Approve Doctor 1
- find Doctor 1 in pending applicant cards
- optionally write review note
- click approve
- then in doctor setup section:
  - put Doctor 1 user id
  - choose `Cardiology`
  - save doctor setup

Expected DB result:
- `job_applications.status = Approved`
- `Doctor` role added in `user_roles`
- `Applicant` role removed
- row created in `doctors`
- `doctors.doctor_id = users.id`
- `doctors.department_id = Cardiology id`

Important:
- no new separate doctor number is invented
- doctor id is the same as user id

9. Approve Doctor 2
- same process
- setup under `Orthopedics`

Expected:
- second row in `doctors`

At this point:
- 2 approved and configured doctors exist

---

**Part 5: Verify Doctor Login**

10. Login as Doctor 1
- use `/ui/login`
- log in with `doctorone@gmail.com`

Expected:
- redirect to `/ui/doctor-dashboard`

11. Login as Doctor 2
- same with `doctortwo@gmail.com`

Expected:
- redirect to `/ui/doctor-dashboard`

If redirect fails:
- check admin actually completed doctor setup
- check `Doctor` role exists
- check doctor row exists in `doctors`

---

**Part 6: Create Nurse and IT Worker**

12. Register Nurse 1 as applicant
- `/ui/register/applicant`
- role = `Nurse`
- no public department selection now
- submit
- log in once
- confirm landing at `/ui/applications`

13. Admin approves Nurse 1
- login as admin
- go to `/ui/admin-users`
- approve nurse application
- use nurse setup section:
  - nurse user id
  - assign department, for example `Cardiology`
  - optional note
  - save

Expected DB result:
- `Nurse` role added
- row created in `nurses`
- `department_id = Cardiology`

14. Register IT Worker 1 as applicant
- `/ui/register/applicant`
- role = `ITWorker`
- submit
- confirm applicant flow works

15. Admin approves IT Worker 1
- go to `/ui/admin-users`
- approve IT worker
- in IT setup section:
  - IT worker user id
  - assign department
- for this test, assign:
  - `Cardiology`
  - optionally later also `Orthopedics`

Expected DB result:
- `ITWorker` role added
- row in `department_admins`
- one row per assigned department

---

**Part 7: Doctor Creates Admissions for Two Patients**

16. Doctor 1 creates admission for Patient A
- login as Doctor 1
- go to `/ui/doctor-dashboard`
- use patient id for Patient A
- diagnosis = something like `Chest pain`
- care level = `Ward`
- submit bed request

Expected DB result:
- row created in `admissions`
- `patient_user_id = Patient A user id`
- `department_id = Cardiology`
- `admitted_by_doctor_id = Doctor 1 user id`
- `status = Admitted`
- `care_level_requested = Ward`
- no bed assignment yet

17. Doctor 2 creates admission for Patient B
- login as Doctor 2
- use Patient B
- diagnosis = something like `Fracture`
- care level = `ICU` or `Ward`
- submit

Expected DB result:
- second admission row
- department = `Orthopedics`
- admitted by Doctor 2

Now you have:
- 2 admissions
- 0 bed assignments yet

---

**Part 8: IT Worker Prepares Ward Structure**

18. Login as IT Worker 1
- go to `/ui/it-bed-allocation`

19. Load departments
- click `Load my departments`
- confirm assigned department(s) appear

20. Create care units if needed
For Cardiology:
- choose department `Cardiology`
- unit type `Ward`
- maybe name `Cardio Ward A`
- create care unit

For Orthopedics:
- if IT has Orthopedics access too, create:
  - department `Orthopedics`
  - unit type `Ward` or `ICU`
  - create care unit

Expected DB result:
- rows in `care_units`

21. Create beds
For Cardiology care unit:
- create at least 1 bed, e.g. `CARD-W-01`

For Orthopedics care unit:
- create at least 1 bed, e.g. `ORTHO-W-01`

Expected DB result:
- rows in `beds`
- initial status = `Available`

---

**Part 9: IT Worker Loads Doctors and Patients**

22. Test doctor search on IT page
- use doctor search panel
- filter by Cardiology
- search `doctor`
- load doctors

Expected:
- Doctor 1 appears
- maybe Doctor 2 too if department filter is open correctly

Purpose:
- IT can pick doctor id into admission form without memorizing it

23. Test patient search on IT page
- use patient search panel
- search `patient`
- load patients

Expected:
- Patient A and Patient B appear as cards
- IT can click one and auto-fill patient id

Purpose:
- IT can find patient ids without memorizing them

---

**Part 10: IT Worker Loads Admissions and Assigns Beds**

24. Load admissions
- filter department = `Cardiology`
- click `Load admissions`

Expected:
- Patient A admission appears as a card

25. Load available beds
- same department = `Cardiology`
- click `Load available beds`

Expected:
- `CARD-W-01` appears

26. Assign a bed to Patient A
- pick Patient A admission id
- pick Cardiology bed id
- click `Assign bed`

Expected DB result:
- row created in `bed_assignments`
- `admission_id = Patient A admission`
- `bed_id = CARD-W-01`
- `assigned_by_user_id = IT worker`
- bed status changes to `Occupied`
- admission `care_level_assigned` updated

27. Repeat for Patient B
- load Orthopedics admissions
- load Orthopedics available beds
- assign one bed to Patient B

Expected:
- second `bed_assignments` row
- second bed becomes `Occupied`

Now:
- 2 patients admitted
- 2 patients assigned beds

---

**Part 11: Nurse Monitors One Patient**

28. Login as Nurse 1
- go to `/ui/nurse-dashboard`

29. Load nurse profile
- click `Load profile`

Expected:
- nurse profile loads
- confirms assigned department = `Cardiology`

30. Load department patients
- click `Refresh patients`

Expected:
- Patient A appears if Patient A is admitted in Cardiology
- Patient B should not appear if Nurse 1 belongs only to Cardiology

31. Open Patient A admission
- click Patient A card

Expected:
- admission details load in Admission Monitor
- admission id and patient id auto-fill

32. Log vitals
- enter:
  - temperature
  - pulse
  - systolic
  - diastolic
  - respiration
  - SpO2
  - note
- click `Save vital signs`

Expected DB result:
- row inserted into `nurse_vital_sign_logs`
- linked to:
  - admission id
  - patient id
  - nurse id

This is where your temp/pulse/BP values are stored.

---

**Part 12: Verify Patient-Side Outcomes**

33. Login as Patient A
- go to `/ui/patient-portal`

What to check:
- patient account still works
- any related appointment/record/blood flow still opens
- if admission-related downstream UI exists, confirm no obvious break

34. Login as Patient B
- same check

This confirms:
- patient auth still works even after admission/bed assignment

---

**Part 13: Discharge Flow**

35. Login as IT Worker
- open `/ui/it-bed-allocation`

36. Discharge Patient A
- use Patient A admission id
- optionally enter release reason
- click `Discharge and release bed`

Expected DB result:
- admission status becomes `Discharged`
- `discharge_date` set
- matching active bed assignment gets:
  - `released_at`
  - `released_by_user_id`
  - `release_reason`
- bed status changes from `Occupied` back to `Available`

37. Verify after discharge
- load admissions again
- Patient A should now show `Discharged`
- load available beds again
- Patient A’s former bed should be available again

---

**Part 14: What to Verify Manually in Database**

After the full test, verify these tables:

- `users`
- `patients`
- `job_applications`
- `user_roles`
- `doctors`
- `nurses`
- `department_admins`
- `care_units`
- `beds`
- `admissions`
- `bed_assignments`
- `nurse_vital_sign_logs`

---

**Expected Final Story**
By the end of this plan, your system should prove this complete workflow:

1. two patients exist
2. two doctors apply and are approved
3. one nurse applies and is approved
4. one IT worker applies and is approved
5. admin completes staff department setup
6. doctors create admissions for patients
7. IT worker creates ward capacity and assigns beds
8. nurse logs vitals for admitted patient
9. IT worker discharges patient and frees bed

---

**Important Clarifications**
- `admission id` is auto-generated by the DB
- `bed assignment id` is auto-generated by the DB
- `doctor_id` in `doctors` is the same as that user’s `users.id`
- patient is not permanently tied to a department before admission
- department belongs to the admission, doctor profile, nurse profile, and IT scope
- bed must belong to a care unit, and care unit must belong to the same department as the admission





[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[
\\\\\\\       \\\\\\\\\\\\      \\\\\\\\\\\\\\\       \\\\\\\\\\\\\
]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]
If you want, next I can turn this into a **checklist table** and add it into [dev_log/README.md](/s:/Lifelink---Modern_Hospital_Mangement_system/dev_log/README.md) in a report-ready format.




[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[
\\\\\\\       \\\\\\\\\\\\      \\\\\\\\\\\\\\\       \\\\\\\\\\\\\
]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]

Use these directly in MSSQL Studio against the `lifelink` database.

Replace the sample emails if needed:
- `patienta@gmail.com`
- `patientb@gmail.com`
- `doctorone@gmail.com`
- `doctortwo@gmail.com`
- `nurseone@gmail.com`
- `itone@gmail.com`



[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[
\\\\\\\       \\\\\\\\\\\\      \\\\\\\\\\\\\\\       \\\\\\\\\\\\\
]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]

so the end to end flow is working fine in every way 

but i havent testes the sql codes in sql studio
can you give me some codes to check
let me tell you i dischared patient a


[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[[
\\\\\\\       \\\\\\\\\\\\      \\\\\\\\\\\\\\\       \\\\\\\\\\\\\
]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]]

Yes. Since you already **discharged Patient A**, the best SQL checks now are the ones that prove the full admission -> bed assignment -> discharge flow actually happened in the database.

Run these in SSMS against `lifelink`.

First:

```sql
USE lifelink;
GO
```

If your Patient A email is `patienta@gmail.com`, these will verify the flow.

**1. Check Patient A basic user row**
```sql
SELECT
    id,
    full_name,
    email,
    account_status,
    created_at
FROM users
WHERE email = 'patienta@gmail.com';
GO
```

**2. Check Patient A patient profile**
```sql
SELECT
    p.patient_id,
    u.full_name,
    u.email,
    p.blood_group,
    p.emergency_contact_name,
    p.emergency_contact_phone,
    p.is_active
FROM patients p
JOIN users u ON u.id = p.patient_id
WHERE u.email = 'patienta@gmail.com';
GO
```

**3. Check all admissions of Patient A**
This shows whether the patient was admitted and then discharged.

```sql
SELECT
    a.id AS admission_id,
    u.email AS patient_email,
    d.dept_name AS department,
    a.admitted_by_doctor_id,
    doc.email AS admitted_by_doctor_email,
    a.diagnosis,
    a.care_level_requested,
    a.care_level_assigned,
    a.status,
    a.admit_date,
    a.discharge_date
FROM admissions a
JOIN users u ON u.id = a.patient_user_id
JOIN departments d ON d.id = a.department_id
LEFT JOIN users doc ON doc.id = a.admitted_by_doctor_id
WHERE u.email = 'patienta@gmail.com'
ORDER BY a.id DESC;
GO
```

Expected:
- latest admission should have `status = Discharged`
- `discharge_date` should be filled

**4. Check bed assignment history for Patient A**
This proves a bed was assigned and later released.

```sql
SELECT
    ba.id AS bed_assignment_id,
    ba.admission_id,
    b.id AS bed_id,
    b.bed_code,
    ba.assigned_by_user_id,
    assigner.email AS assigned_by_email,
    ba.assigned_at,
    ba.released_at,
    ba.released_by_user_id,
    releaser.email AS released_by_email,
    ba.release_reason
FROM bed_assignments ba
JOIN admissions a ON a.id = ba.admission_id
JOIN users p ON p.id = a.patient_user_id
JOIN beds b ON b.id = ba.bed_id
LEFT JOIN users assigner ON assigner.id = ba.assigned_by_user_id
LEFT JOIN users releaser ON releaser.id = ba.released_by_user_id
WHERE p.email = 'patienta@gmail.com'
ORDER BY ba.id DESC;
GO
```

Expected:
- `released_at` should be filled for the discharged admission
- `release_reason` should usually show your discharge reason

**5. Check whether that bed became available again**
```sql
SELECT
    b.id,
    b.bed_code,
    b.status,
    cu.unit_name,
    cu.unit_type,
    d.dept_name
FROM beds b
JOIN care_units cu ON cu.id = b.care_unit_id
JOIN departments d ON d.id = cu.department_id
WHERE b.id IN (
    SELECT TOP 1 ba.bed_id
    FROM bed_assignments ba
    JOIN admissions a ON a.id = ba.admission_id
    JOIN users u ON u.id = a.patient_user_id
    WHERE u.email = 'patienta@gmail.com'
    ORDER BY ba.id DESC
);
GO
```

Expected:
- after discharge, that bed should be `Available`

**6. Check whether Patient B is still admitted or assigned**
Useful comparison if Patient B is still active.

```sql
SELECT
    a.id AS admission_id,
    u.email,
    a.status,
    a.admit_date,
    a.discharge_date,
    b.bed_code,
    ba.assigned_at,
    ba.released_at
FROM admissions a
JOIN users u ON u.id = a.patient_user_id
LEFT JOIN bed_assignments ba ON ba.admission_id = a.id AND ba.released_at IS NULL
LEFT JOIN beds b ON b.id = ba.bed_id
WHERE u.email IN ('patienta@gmail.com', 'patientb@gmail.com')
ORDER BY u.email, a.id DESC;
GO
```

This helps confirm:
- Patient A = discharged
- Patient B = maybe still admitted / active bed assigned

**7. One compact “story” query for Patient A**
This is the most useful single query.

```sql
SELECT
    u.full_name,
    u.email,
    a.id AS admission_id,
    d.dept_name AS department,
    a.diagnosis,
    a.status AS admission_status,
    a.admit_date,
    a.discharge_date,
    b.bed_code,
    ba.assigned_at,
    ba.released_at,
    ba.release_reason
FROM users u
LEFT JOIN admissions a ON a.patient_user_id = u.id
LEFT JOIN departments d ON d.id = a.department_id
LEFT JOIN bed_assignments ba ON ba.admission_id = a.id
LEFT JOIN beds b ON b.id = ba.bed_id
WHERE u.email = 'patienta@gmail.com'
ORDER BY a.id DESC, ba.id DESC;
GO
```

**Expected final result for Patient A**
You want to see:

- user exists
- patient profile exists
- admission exists
- admission status = `Discharged`
- discharge date is not null
- bed assignment exists
- released_at is not null
- bed status became `Available`

