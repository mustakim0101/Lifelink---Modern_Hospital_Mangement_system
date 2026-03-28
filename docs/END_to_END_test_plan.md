> [!NOTE]
> This file contains the full manual end-to-end validation path for the current LifeLink prototype.
> The original step-by-step plan is preserved below exactly as working reference content.

# End-to-End Test Guide

This document is the GitHub-friendly reading layer for the existing test plan below.
Nothing from the original plan has been deleted.

## What This Test Covers

- Public registration
- Applicant approval
- Staff setup by admin
- Doctor-driven admission creation
- IT worker ward and bed operations
- Nurse monitoring and vital logging
- Discharge and bed release flow

## Test Actors

| Actor | Example account | Main purpose in the flow |
| --- | --- | --- |
| Admin | bootstrap admin | Approves applicants and completes staff setup |
| Patient A | `patienta@gmail.com` | Admission, bed assignment, discharge |
| Patient B | `patientb@gmail.com` | Second patient for parallel flow testing |
| Doctor 1 | `doctorone@gmail.com` | Creates first admission |
| Doctor 2 | `doctortwo@gmail.com` | Creates second admission |
| Nurse 1 | `nurseone@gmail.com` | Logs vitals after admission |
| IT Worker 1 | `itone@gmail.com` | Creates ward capacity and manages beds |

## High-Level Story

1. Start the app and create the first admin.
2. Create two patient accounts.
3. Create two doctor applicants.
4. Admin approves both doctors and completes doctor setup.
5. Create and approve nurse and IT worker accounts.
6. Doctors create admissions for two patients.
7. IT worker creates care units and beds.
8. IT worker assigns beds to admitted patients.
9. Nurse loads department patients and records vitals.
10. IT worker discharges a patient and releases the bed.

## Quick Progress Checklist

- [ ] Docker stack is running
- [ ] Admin account works
- [ ] Patient A account works
- [ ] Patient B account works
- [ ] Doctor 1 approved and configured
- [ ] Doctor 2 approved and configured
- [ ] Nurse approved and configured
- [ ] IT worker approved and configured
- [ ] Admission created for Patient A
- [ ] Admission created for Patient B
- [ ] Bed assigned to Patient A
- [ ] Bed assigned to Patient B
- [ ] Vitals logged by nurse
- [ ] Patient A discharged
- [ ] Released bed returned to `Available`

## DB Outcomes To Expect

| Flow stage | Main tables affected |
| --- | --- |
| Registration | `users`, `patients`, `user_roles` |
| Job application | `job_applications`, `user_roles` |
| Doctor setup | `doctors` |
| Nurse setup | `nurses` |
| IT setup | `department_admins` |
| Ward setup | `care_units`, `beds` |
| Admission | `admissions` |
| Bed assignment | `bed_assignments`, `beds` |
| Nurse monitoring | `nurse_vital_sign_logs` |
| Discharge | `admissions`, `bed_assignments`, `beds` |

## Reading Tips

- Use the original numbered plan below when executing the test.
- Use the checklist above when reporting progress in GitHub.
- Use SQL verification after each major milestone if you want strong proof of state changes.

---


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

---

## Revision 3 - Blood Bank Department End-to-End Test Plan

This section is the new blood-module-specific end-to-end plan based on the locked workflow decision:
- `Blood Bank` becomes a department
- Blood Bank nurses perform donor health checks
- Blood Bank IT workers perform blood matching, donor notification, donation logging, and request fulfillment
- donor keeps only donor-owned actions

The earlier test plans above are preserved as working history. This section is the current detailed blood workflow test story.

## What This Blood Test Covers

- Blood Bank department availability
- donor registration and availability
- patient blood requests
- Blood Bank staff setup
- donor notification and acceptance
- nurse-entered eligibility screening
- casual donation flow
- request-linked donation flow
- request fulfillment and inventory movement

## Blood Test Actors

| Actor | Example account | Purpose |
| --- | --- | --- |
| Admin | bootstrap admin | Approves staff and assigns Blood Bank department |
| Blood Bank Nurse | `nurseblood@demo.com` | Performs donor health checks |
| Blood Bank IT Worker | `itblood@demo.com` | Runs matching, notifications, donation recording, request fulfillment |
| Patient O+ | `patient.rahim@demo.com` | Creates O+ blood request |
| Patient AB- | `patient.sadia@demo.com` | Creates AB- blood request |
| Donor O+ A | `donor.oplus.a@demo.com` | Main donor for common compatibility testing |
| Donor O- Universal | `donor.oneg@demo.com` | Universal red-cell donor scenario |
| Donor A+ | `donor.aplus@demo.com` | Secondary compatible donor |
| Donor AB- | `donor.abneg@demo.com` | Rare-group donor for AB- patient |
| Casual Walk-in Donor B+ | `donor.bplus.walkin@demo.com` | Tests donation not linked to request |

## High-Level Blood Story

1. Start the app and confirm `Blood Bank` exists as a department.
2. Create admin, two patients, several donors, one Blood Bank nurse, and one Blood Bank IT worker.
3. Admin approves the nurse and IT worker and assigns them to `Blood Bank`.
4. Donors set weekly availability.
5. Patient O+ creates a request for O+ blood.
6. Patient AB- creates a request for AB- blood.
7. Blood Bank IT worker loads requests and notifies matching donors.
8. Donors log in and accept or decline.
9. Donors physically come in.
10. Blood Bank nurse performs health checks.
11. System marks donors eligible or ineligible.
12. Blood Bank IT worker records one request-linked donation and one casual stock donation.
13. Requests are fulfilled through the chosen donation/inventory path.

## Quick Blood Checklist

- [ ] Docker stack is running
- [ ] `Blood Bank` department exists
- [ ] Admin account works
- [ ] Blood Bank nurse approved and assigned
- [ ] Blood Bank IT worker approved and assigned
- [ ] Patient O+ account works
- [ ] Patient AB- account works
- [ ] Donor O+ A account works
- [ ] Donor O- account works
- [ ] Donor A+ account works
- [ ] Donor AB- account works
- [ ] Casual donor B+ account works
- [ ] Weekly availability saved for donors
- [ ] O+ request created
- [ ] AB- request created
- [ ] Notifications sent to compatible donors
- [ ] At least one donor accepted O+ request
- [ ] At least one donor accepted AB- request
- [ ] Blood Bank nurse logged donor health checks
- [ ] Eligibility updated correctly
- [ ] Request-linked donation recorded
- [ ] Casual donation recorded
- [ ] Request status updated correctly
- [ ] Inventory updated correctly

## Current Design Fit Check

This Revision 3 plan matches the current implementation, but it should now be executed with these page responsibilities in mind:

- `/ui/blood-matching` is the main Blood Bank operations page
- `/ui/blood-bank-schema` is the setup/debug and inspection page
- `/ui/nurse-dashboard` is the Blood Bank nurse screening page when the nurse department is `Blood Bank`
- `/ui/it-bed-allocation` is only the Blood Bank entry page for IT workers; the actual blood operations happen in `/ui/blood-matching`
- `/ui/donor-dashboard` remains donor-owned only
- `/ui/patient-portal` remains the patient blood-request page

Use `/ui/blood-bank-schema` only when you need:
- a blood bank row created
- inventory baseline inserted or inspected
- donor profile baseline inspected

Do not treat `/ui/blood-bank-schema` as the normal daily blood workflow page.

## Recommendation Before Full-Scale Checking

Yes, you should start a full-scale website check for the blood donation workflow.

Recommended approach:
1. run the whole blood story from UI
2. pause after each major milestone
3. verify visible result plus DB result before moving forward
4. use `/ui/blood-bank-schema` only if setup data is missing

Recommendation status:
- `Go`, but staged
- not `Go` as one giant all-or-nothing uninterrupted run

## Shared Sample Data Pack For This Test Run

Use this exact dataset for the whole run so any failure can be traced in DB later with the same emails and IDs.

### Shared password

- all non-admin test accounts: `12345678`

### Staff accounts

| Role | Full name | Email | Department |
| --- | --- | --- | --- |
| Blood Bank nurse | Nurse Blood | `nurseblood@demo.com` | Blood Bank |
| Blood Bank IT worker | IT Blood | `itblood@demo.com` | Blood Bank |
| Normal comparison nurse | Main Nurse | `mnurse@demo.com` | non-Blood-Bank |

### Patient accounts

| Full name | Email | Blood group | Planned request |
| --- | --- | --- | --- |
| Rahim Patient | `patient.rahim@demo.com` | O+ | O+ WholeBlood, 2 units, Urgent |
| Sadia Patient | `patient.sadia@demo.com` | AB- | AB- WholeBlood, 1 unit, High |

### Donor accounts

| Donor | Email | Blood group | Purpose |
| --- | --- | --- | --- |
| Donor O Plus A | `donor.oplus.a@demo.com` | O+ | main O+ request donor |
| Donor O Negative | `donor.oneg@demo.com` | O- | compatibility comparison donor |
| Donor A Plus | `donor.aplus@demo.com` | A+ | extra donor visibility test |
| Donor AB Negative | `donor.abneg@demo.com` | AB- | main AB- request donor |
| Donor B Plus Walkin | `donor.bplus.walkin@demo.com` | B+ | casual donation inventory donor |

### Suggested donor availability pattern

| Donor | Availability suggestion |
| --- | --- |
| `donor.oplus.a@demo.com` | Mon, Wed, Sat available |
| `donor.oneg@demo.com` | Tue, Thu available |
| `donor.aplus@demo.com` | Fri available |
| `donor.abneg@demo.com` | Mon, Thu available |
| `donor.bplus.walkin@demo.com` | Sun available |

### Suggested notification messages

| Request | Suggested message |
| --- | --- |
| O+ request | `Blood Bank request for O+ WholeBlood. Please come within the next 3 days if you can donate.` |
| AB- request | `Urgent AB- donor request. Please respond today if available to come for screening.` |

### Suggested Blood Bank nurse health-check values

| Donor | Weight kg | Temp C | Hemoglobin | Expected eligibility |
| --- | --- | --- | --- | --- |
| `donor.oplus.a@demo.com` | 61 | 36.7 | 13.8 | Eligible |
| `donor.oneg@demo.com` | 43 | 36.8 | 13.2 | Not eligible |
| `donor.abneg@demo.com` | 58 | 36.9 | 12.9 | Eligible |
| `donor.bplus.walkin@demo.com` | 64 | 36.6 | 14.1 | Eligible |

### Suggested donation logging records

| Donor | Type | Group | Component | Units | Linked request |
| --- | --- | --- | --- | --- | --- |
| `donor.oplus.a@demo.com` | request-linked | O+ | WholeBlood | 1 | Rahim O+ request |
| `donor.abneg@demo.com` | request-linked | AB- | WholeBlood | 1 | Sadia AB- request |
| `donor.bplus.walkin@demo.com` | casual | B+ | WholeBlood | 1 | null |

### IDs to note down during the run

As soon as they exist, note these values and keep them in your test notes:

- `users.id` for `nurseblood@demo.com`
- `users.id` for `itblood@demo.com`
- `users.id` for both patients
- `users.id` for all five donors
- `blood_banks.id` for the bank used in donation logging
- `blood_requests.id` for Rahim O+ request
- `blood_requests.id` for Sadia AB- request

## DB Outcomes To Expect For Blood Flow

| Flow stage | Main tables affected |
| --- | --- |
| Donor registration | `users`, `patients`, `user_roles`, `donor_profiles` |
| Staff application and approval | `users`, `job_applications`, `user_roles`, `nurses`, `department_admins` |
| Weekly availability | `donor_availabilities` |
| Blood request | `blood_requests` |
| Matching and notify | `blood_request_matches`, `donor_notifications` |
| Donor response | `blood_request_matches`, `donor_notifications` |
| Donor health screening | `donor_health_checks`, `donor_profiles` |
| Request-linked donation | `blood_donations`, `blood_inventory`, `blood_requests` |
| Casual stock donation | `blood_donations`, `blood_inventory` |
| Request fulfillment | `blood_requests`, `blood_request_matches`, `blood_inventory` |

## Part 1: Environment and Department Baseline

1. Start the project
- run Docker
- confirm app opens at `http://localhost:8000`

Expected website result:
- public landing and login pages load

Expected DB result:
- database is reachable
- core seed data is present

2. Confirm `Blood Bank` department exists
- check admin-side department dropdowns or reference department list if exposed
- later verify in DB if needed

Expected website result:
- `Blood Bank` appears as a selectable department for staff setup

Expected DB result:
- one row in `departments` with `dept_name = Blood Bank`

## Part 2: Create Admin

3. Create bootstrap admin
- open `/ui/login`
- use bootstrap admin helper
- log in as admin

Expected website result:
- redirect goes to `/ui/admin-users`

Expected DB result:
- row in `users`
- `Admin` role in `user_roles`

## Part 3: Create Two Patient Accounts

4. Register Patient O+
- open `/ui/register/patient`
- use:
  - name: `Rahim Patient`
  - email: `patient.rahim@demo.com`
  - password: `12345678`
  - blood group: `O+`
- submit and log in

Expected website result:
- redirect to `/ui/patient-portal`

Expected DB result:
- row in `users`
- row in `patients`
- `Patient` role assigned

5. Register Patient AB-
- open `/ui/register/patient`
- use:
  - name: `Sadia Patient`
  - email: `patient.sadia@demo.com`
  - password: `12345678`
  - blood group: `AB-`
- submit and log in

Expected website result:
- redirect to `/ui/patient-portal`

Expected DB result:
- second patient row in `users`
- second patient row in `patients`
- `Patient` role assigned

## Part 4: Register Blood Bank Staff As Applicants

6. Register Blood Bank nurse applicant
- open `/ui/register/applicant`
- use:
  - name: `Nurse Blood`
  - email: `nurseblood@demo.com`
  - password: `12345678`
  - role: `Nurse`
- submit and log in once

Expected website result:
- landing goes to applicant workspace, not nurse dashboard

Expected DB result:
- row in `users`
- `Applicant` role assigned
- one `job_applications` row with `Pending`

7. Register Blood Bank IT applicant
- open `/ui/register/applicant`
- use:
  - name: `IT Blood`
  - email: `itblood@demo.com`
  - password: `12345678`
  - role: `ITWorker`
- submit and log in once

Expected website result:
- landing goes to applicant workspace, not IT dashboard

Expected DB result:
- second applicant row in `users`
- second `job_applications` row with `Pending`

## Part 5: Admin Approves and Assigns Blood Bank Staff

8. Approve Blood Bank nurse
- log in as admin
- open `/ui/admin-users`
- approve `nurseblood@demo.com`
- complete nurse setup using department `Blood Bank`

Expected website result:
- nurse applicant card moves out of pending state
- nurse setup saves successfully

Expected DB result:
- application status becomes `Approved`
- `Nurse` role added
- applicant role removed if your flow removes it
- row created in `nurses`
- `nurses.department_id` points to `Blood Bank`

9. Approve Blood Bank IT worker
- stay on `/ui/admin-users`
- approve `itblood@demo.com`
- assign department `Blood Bank`

Expected website result:
- IT applicant moves out of pending queue
- department assignment succeeds

Expected DB result:
- application status becomes `Approved`
- `ITWorker` role added
- row created in `department_admins`
- assignment points to `Blood Bank`

## Part 6: Register Multiple Donors

10. Register Donor O+ A
- open `/ui/register/donor`
- use:
  - name: `Donor O Plus A`
  - email: `donor.oplus.a@demo.com`
  - password: `12345678`
  - blood group: `O+`

11. Register Donor O-
- use:
  - name: `Donor O Negative`
  - email: `donor.oneg@demo.com`
  - password: `12345678`
  - blood group: `O-`

12. Register Donor A+
- use:
  - name: `Donor A Plus`
  - email: `donor.aplus@demo.com`
  - password: `12345678`
  - blood group: `A+`

13. Register Donor AB-
- use:
  - name: `Donor AB Negative`
  - email: `donor.abneg@demo.com`
  - password: `12345678`
  - blood group: `AB-`

14. Register Casual Walk-in Donor B+
- use:
  - name: `Donor B Plus Walkin`
  - email: `donor.bplus.walkin@demo.com`
  - password: `12345678`
  - blood group: `B+`

Expected website result for all donors:
- registration succeeds
- login redirects to `/ui/donor-dashboard`

Expected DB result for each donor:
- row in `users`
- `Donor` role assigned in `user_roles`
- row in `donor_profiles`
- donor profile uses the same `users.id`

## Part 7: Donors Set Weekly Availability

15. Log in as each donor and set availability
- open `/ui/donor-dashboard`
- save weekly availability
- set available = true
- set possible bags or max units as allowed by UI

Expected website result:
- availability success message appears
- donor dashboard shows saved or refreshed availability

Expected DB result:
- rows created in `donor_availabilities`

Important note:
- donors should no longer self-enter staff-owned health checks or actual donations after the planned cleanup
- donor dashboard should remain focused on availability, notifications, accept/decline, and donation history display

## Part 8: Patients Create Blood Requests

16. Patient O+ creates a request
- log in as `patient.rahim@demo.com`
- open `/ui/patient-portal`
- create request:
  - blood group needed = `O+`
  - units required = `2`
  - urgency = `Urgent`
  - component type = as supported by page, for example `WholeBlood`

Expected website result:
- request appears in patient request history
- status should start as pending or equivalent initial state

Expected DB result:
- row in `blood_requests`
- linked to patient and requesting user
- `status = Pending` or current default pending-like status

17. Patient AB- creates a request
- log in as `patient.sadia@demo.com`
- create request:
  - blood group needed = `AB-`
  - units required = `1`
  - urgency = `High`

Expected website result:
- second request appears in patient history

Expected DB result:
- second row in `blood_requests`

## Part 9: Blood Bank IT Worker Matches and Notifies Donors

18. Log in as Blood Bank IT worker
- use `itblood@demo.com`
- open `/ui/blood-matching`

Expected website result:
- blood matching page loads successfully
- page should behave as blood operations center for Blood Bank IT work
- if the stored token is fresh, the page should auto-detect it without forcing manual token paste

19. Load requests
- refresh board or load requests

Expected website result:
- O+ and AB- requests appear in the request list
- if the page shows `Unauthenticated`, re-login from `/ui/auth` as `itblood@demo.com` and reopen `/ui/blood-matching`

Expected DB result:
- no new rows yet, just existing request visibility

20. Select O+ request and inspect suggestions

Expected website result:
- compatible donor suggestions should prefer:
  - `donor.oplus.a@demo.com`
  - `donor.oneg@demo.com`
- other incompatible groups should not be treated as best matches

21. Notify donors for O+ request
- notify `donor.oplus.a@demo.com`
- notify `donor.oneg@demo.com`
- optionally also notify `donor.aplus@demo.com` only if your compatibility rules allow that specific case in your implementation

Expected website result:
- notifications sent successfully
- request shows matching activity

Expected DB result:
- rows in `blood_request_matches`
- rows in `donor_notifications`
- request may move to `Matched`

22. Select AB- request and inspect suggestions

Expected website result:
- `donor.abneg@demo.com` should be a suitable donor
- compatible rare-group suggestions should be visible if supported
- if `donor.oneg@demo.com` also appears because of compatibility logic, record that as observed behavior and continue

23. Notify donor for AB- request
- notify `donor.abneg@demo.com`

Expected DB result:
- additional rows in `blood_request_matches`
- additional rows in `donor_notifications`

## Part 10: Donors Respond To Notifications

24. Log in as `donor.oplus.a@demo.com`
- open notifications
- accept O+ request

Expected website result:
- request notification shows accepted response state

Expected DB result:
- matching row becomes `Accepted`
- donor notification response becomes `Accepted`

25. Log in as `donor.oneg@demo.com`
- optionally decline or leave pending for comparison

Expected website result:
- decline or pending state is shown correctly

Expected DB result:
- matching row and notification reflect decline or remain pending

26. Log in as `donor.abneg@demo.com`
- accept AB- request

Expected DB result:
- AB- matching row becomes accepted

## Part 11: Blood Bank Nurse Performs Health Checks

27. Log in as `nurseblood@demo.com`
- open `/ui/nurse-dashboard`
- load profile

Expected website result:
- profile shows department = `Blood Bank`
- blood-bank-only donor screening tools should be visible for this nurse

28. Search or load donor card for `donor.oplus.a@demo.com`
- confirm donor id is visible in the card or selection UI

Expected website result:
- nurse can identify donor without guessing ids

29. Record health check for O+ donor
- enter realistic values such as:
  - weight = `68`
  - temperature = `36.8`
  - hemoglobin = `13.5`
  - note = `Fit for donation`

Expected website result:
- health check saves
- donor appears eligible

Expected DB result:
- row in `donor_health_checks`
- `checked_by_user_id` points to Blood Bank nurse user id
- `donor_profiles.is_eligible = 1`

30. Record health check for AB- donor
- enter acceptable values and save

Expected DB result:
- second row in `donor_health_checks`
- donor remains eligible

31. Optional negative-path test
- load `donor.oneg@demo.com`
- enter failing values such as high temperature or low hemoglobin if supported by the criteria

Expected website result:
- donor should be marked ineligible if rules fail

Expected DB result:
- health check row still exists
- `donor_profiles.is_eligible = 0`

## Part 12: Blood Bank IT Worker Records Request-Linked Donation

32. Log back in as `itblood@demo.com`
- open `/ui/blood-matching`
- select the accepted O+ request

Expected website result:
- accepted donor timeline is visible
- approved donor can be selected for next action

33. Approve accepted donor if your page requires this step
- choose `donor.oplus.a@demo.com`
- attach blood bank if needed

Expected DB result:
- request moves to approved or selected state
- selected match stores selection metadata

34. Record actual donation for O+ donor
- use the request-linked donation flow
- choose donor `donor.oplus.a@demo.com`
- choose Blood Bank
- set blood group `O+`
- set units donated `2` or the supported bag count
- link to O+ request
- link to latest successful health check if supported by UI

Expected website result:
- donation saved
- request can proceed toward fulfillment

Expected DB result:
- row in `blood_donations`
- `recorded_by_user_id` points to Blood Bank IT worker
- `linked_request_id` points to O+ request
- `donor_health_check_id` points to O+ donor health check when supported

35. Fulfill O+ request
- complete the request using the recorded donor path

Expected website result:
- request status becomes fulfilled

Expected DB result:
- `blood_requests.status = Fulfilled`
- selected match may move to `Completed`

## Part 13: Record Casual Walk-In Donation

36. Stay logged in as Blood Bank IT worker
- use donation logging for `donor.bplus.walkin@demo.com`
- this donor is not tied to a patient request

37. If health screening is required first, ask Blood Bank nurse to record a passing health check for the casual donor

Expected DB result:
- health check row exists
- donor becomes eligible

38. Record casual donation
- choose donor `donor.bplus.walkin@demo.com`
- choose Blood Bank
- blood group `B+`
- set units donated `1`
- do not link a blood request

Expected website result:
- donation history reflects a successful casual donation

Expected DB result:
- row in `blood_donations`
- `linked_request_id` is null
- inventory should increase for the selected blood bank and matching blood group/component

## Part 14: Fulfill AB- Request

39. Return to the AB- request
- select accepted donor `donor.abneg@demo.com`
- verify health check is successful

40. Record request-linked donation for AB- donor
- log donation against AB- request

Expected DB result:
- second request-linked donation row

41. Fulfill AB- request
- complete request

Expected website result:
- patient AB- request history shows fulfilled status

Expected DB result:
- `blood_requests.status = Fulfilled`

## Part 15: Verify Website Outcomes Role By Role

42. Check donor dashboards
- accepted donors should see notification history and donation history
- declined or ineligible donors should see correct status behavior

43. Check patient dashboards
- `patient.rahim@demo.com` should see O+ request status progress through matched/approved/fulfilled as supported
- `patient.sadia@demo.com` should see AB- request fulfilled

44. Check Blood Bank nurse dashboard
- Blood Bank nurse should see donor-screening tools
- normal non-Blood-Bank nurse should not get those blood-bank-only tools after implementation

45. Check Blood Bank IT worker dashboard/page access
- blood matching and donation tools should be available to Blood Bank IT worker
- ordinary department IT staff should not automatically receive Blood Bank operations if not assigned there

## Expected Final Story

By the end of this blood plan, the system should prove this complete workflow:

1. `Blood Bank` exists as a department
2. admin assigns a nurse and an IT worker to Blood Bank operations
3. multiple donors with different blood groups exist in the system
4. donors mark availability
5. two patients create different blood requests
6. Blood Bank IT worker matches and notifies compatible donors
7. donors accept or decline
8. Blood Bank nurse performs real health screening
9. system updates donor eligibility from staff-entered data
10. Blood Bank IT worker records actual donations
11. one request-linked donation is completed
12. one casual stock donation is completed
13. inventory and request status reflect the real story

## Important Clarifications For This Blood Plan

- all users still come from the shared `users` table
- donor id is the same underlying `users.id`
- nurse id is the same underlying `users.id`
- staff do not get a new person identity when assigned to Blood Bank
- `Blood Bank` is a department assignment decision, not a brand new role family
- scheduling can stay in notification text for now
- SQL verification queries can be written later against the final tested website outcomes
- if baseline setup data is missing, create or inspect it from `/ui/blood-bank-schema`, then continue the real workflow from `/ui/blood-matching`

