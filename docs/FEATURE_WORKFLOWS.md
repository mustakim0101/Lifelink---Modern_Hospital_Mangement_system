# Feature Workflows





  --------------------------------------------------------------------------------------
  ---------------------------------------------------------------------------------------
                                  #WORKflows

**What `app/Services` Is Doing**
In your project, [lifelink-app/app/Services](/s:/Lifelink---Modern_Hospital_Mangement_system/lifelink-app/app/Services) contains backend business logic that is too detailed or SQL-heavy to keep directly inside controllers.

Right now it contains:
- [ApplicationReviewSqlService.php](/s:/Lifelink---Modern_Hospital_Mangement_system/lifelink-app/app/Services/Sql/ApplicationReviewSqlService.php)
- [BloodMatchingSqlService.php](/s:/Lifelink---Modern_Hospital_Mangement_system/lifelink-app/app/Services/Sql/BloodMatchingSqlService.php)
- [JobApplicationSqlService.php](/s:/Lifelink---Modern_Hospital_Mangement_system/lifelink-app/app/Services/Sql/JobApplicationSqlService.php)

In plain words:
- controllers receive the request
- services contain the real decision-making and database operations
- this folder is where “how the business process works” is being implemented

So `app/Services` is the place where your backend is handling the hospital’s logic, especially the parts that depend on raw SQL and more complex workflow rules.

**Blood Donation Workflow In Plain English**
A person first becomes a donor. The system marks that user as someone who is allowed to participate in donation activities. Then the donor fills in or confirms basic donor information such as blood group and notes.

After that, the donor sets weekly availability. This means the system knows whether that donor is available this week and how many bags they may be able to donate. Before a donation is accepted, the donor’s health condition is checked. The system records things like weight and temperature and uses those values to decide if the donor is currently eligible.

When a hospital needs blood, the request enters the system. The matching process then looks for donors whose blood group is compatible, whose health condition is acceptable, and who have marked themselves available. Suitable donors receive notifications asking whether they are willing to help.

If a donor accepts, that response is saved. When the actual donation happens, the system records where the donation was made, what type of blood component was donated, how many units were given, and whether the donation was linked to a specific blood request. At the same time, the blood bank inventory is increased so the hospital can see that more blood is now available.

So in story form: a person joins as a donor, shows they are available, passes a health check, receives a request if needed, agrees to donate, gives blood, and the hospital stock is updated immediately.

**Bed Management Workflow In Plain English**
A patient first reaches the point where admission is needed. Sometimes that starts with a doctor deciding the patient needs a bed or a higher level of care. The doctor records the diagnosis and requests the kind of bed needed, such as a normal ward bed or ICU-level care.

That request then becomes visible to the staff responsible for bed coordination. The IT worker or department admin looks at the patient’s department, reviews the available beds in that department, and chooses a suitable bed that is currently free.

Once the bed is assigned, the patient is officially linked to that bed, and the bed is no longer shown as available. The system also remembers who assigned the bed and when it happened. Nurses and other staff can then view the patient’s placement and continue care around that assignment.

Later, when the patient is discharged, the system closes the admission and releases the bed. That means the bed becomes available again for the next patient. So the whole idea is: doctor requests care, staff finds a suitable bed, patient is placed, care continues, and when the patient leaves, the bed returns to the pool for reuse.

About app/Services: not exactly all of blood donation and bed management yet.

Accurate version:

->Job application system is using services
->Blood matching and donor notification system is using services
->Bed management and much of donor donation flow are still mostly handled in controllers/models/database operations, not dedicated service classes yet


-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=

### Role of app/Services
`lifelink-app/app/Services` is used for backend business logic that is too SQL-heavy or workflow-specific to keep directly inside controllers.

Current services:
- `app/Services/Sql/JobApplicationSqlService.php`
- `app/Services/Sql/ApplicationReviewSqlService.php`
- `app/Services/Sql/BloodMatchingSqlService.php`

Important note:
- Job application flow is already using service classes.
- Blood matching and donor notification logic is already using a service class.
- Bed management and donor donation flows are currently implemented mostly through controllers, models, and database operations, not yet through separate service classes.

So `app/Services` is not the whole backend. It is the place where the project currently keeps the heavier raw-SQL workflow logic.

### Plain-English workflow: blood donation
A user first becomes a donor. The system then stores that donor's blood profile and lets the donor mark weekly availability. Before donating, the donor records health information such as weight and temperature. The system checks whether the donor is currently eligible. When a blood request appears, matching logic looks for compatible and available donors. Those donors receive notifications. If a donor accepts and later donates blood, the system records the donation and immediately increases the blood inventory for the selected blood bank.

### Plain-English workflow: bed management
A patient reaches a stage where admission is needed. A doctor records the diagnosis and requests the level of care, such as Ward or ICU. That request becomes visible to the responsible department staff. An Admin or IT Worker then checks available beds in the correct department and assigns one. Once assigned, that bed is no longer available to others. Staff can continue monitoring the patient while the admission remains active. When the patient is discharged, the admission is closed and the bed is released back into the available pool.


  --------------------------------------------------------------------------------------
  ---------------------------------------------------------------------------------------
                                  #MUSA finding

Yes, based on your current code/schema:

- `Department` -> has many `CareUnit` (ward/ICU/NICU/CCU)
- `CareUnit` -> has many `Bed`

So:
- Neurology can have 50 beds
- Cardiology can have another separate 50 beds
- They are separate because each bed belongs to a care unit, and each care unit belongs to one department.

For your second question:
- Ward and bed are not the same.
- In your model, ward/ICU/NICU are `care_units` (containers/units).
- Beds are inside a care unit.
- So one ward usually has many beds.

If you switch from migrations-first to raw-SQL-first:
- You’d need to stop relying on Laravel migrations as the primary schema history.
- Add ordered SQL files (create tables, alter tables, seeds) and a deployment process to run them safely.
- Replace `php artisan migrate` workflow with SQL execution workflow in CI/deploy.
- Handle rollback/version tracking yourself (or add a custom migration tracking table/tool).

About “others won’t notice”:
- They will notice in workflow.
- If you keep migrations as source of truth and add SQL dumps only as reference, team workflow stays the same (`artisan migrate`), so minimal disruption.
- SQL dumps then help documentation, DB review, and external sharing, but don’t control schema evolution.


  --------------------------------------------------------------------------------------
  ---------------------------------------------------------------------------------------
                                  #how is databse working


Based on your current setup,(after issue 18 and making ui) on a new PC the database is created by `Docker + MSSQL init scripts`, not by Laravel migrations automatically.

**Actual Flow**
When someone clones the repo and runs:

```powershell
docker compose up -d --build
```

this is the order:

1. Docker reads [docker-compose.yml](/s:/Lifelink---Modern_Hospital_Mangement_system/docker-compose.yml).
2. Root env values are loaded from [`.env`](/s:/Lifelink---Modern_Hospital_Mangement_system/.env) for Compose variables like:
   - `APP_PORT`
   - `MSSQL_PORT`
   - `MSSQL_SA_PASSWORD`
3. Docker starts the `mssql` container first.
4. SQL Server boots with an empty persistent volume `mssql_data` if this is the first run on that machine.
5. After `mssql` becomes healthy, Docker starts `mssql-init`.
6. `mssql-init` runs [init-db.sh](/s:/Lifelink---Modern_Hospital_Mangement_system/docker/mssql/init/init-db.sh).
7. That script waits until SQL Server accepts connections.
8. Then it runs [01-init.sql](/s:/Lifelink---Modern_Hospital_Mangement_system/docker/mssql/init/01-init.sql) against `master`.
   - this creates the `lifelink` database if it does not already exist
9. Then the same script runs every `.sql` file in [docker/mssql/init/schema](/s:/Lifelink---Modern_Hospital_Mangement_system/docker/mssql/init/schema) in filename order against the `lifelink` database.
   - users
   - roles
   - departments
   - care units
   - beds
   - patients
   - doctors
   - nurses
   - admissions
   - blood bank tables
   - and the rest
10. After schema files, it runs every `.sql` file in [docker/mssql/init/seed](/s:/Lifelink---Modern_Hospital_Mangement_system/docker/mssql/init/seed) in filename order.
   - reference roles
   - departments
   - blood banks
11. Separately, the `app` container starts after `mssql` is healthy, but it does **not** auto-run migrations.
12. Laravel reads DB connection settings from [lifelink-app/.env](/s:/Lifelink---Modern_Hospital_Mangement_system/lifelink-app/.env):
   - `DB_CONNECTION=sqlsrv`
   - `DB_HOST=mssql`
   - `DB_DATABASE=lifelink`
   - `DB_USERNAME=sa`
   - `DB_PASSWORD=YourStrong!Passw0rd`
13. The `web` container serves Laravel through Nginx.

**Important Conclusion**
So the current DB creation flow is:

`docker-compose.yml` -> root [`.env`](/s:/Lifelink---Modern_Hospital_Mangement_system/.env) -> `mssql` container -> [init-db.sh](/s:/Lifelink---Modern_Hospital_Mangement_system/docker/mssql/init/init-db.sh) -> [01-init.sql](/s:/Lifelink---Modern_Hospital_Mangement_system/docker/mssql/init/01-init.sql) -> `schema/*.sql` -> `seed/*.sql` -> Laravel connects using [lifelink-app/.env](/s:/Lifelink---Modern_Hospital_Mangement_system/lifelink-app/.env)

**What `.env.docker` is doing**
[`.env.docker`](/s:/Lifelink---Modern_Hospital_Mangement_system/.env.docker) is basically a sample/template for the root Compose env. It is not the main runtime file unless someone copies/uses it as `.env`.

**Are migrations part of first-run flow?**
Not automatically.

Your [docker/Dockerfile](/s:/Lifelink---Modern_Hospital_Mangement_system/docker/Dockerfile) only starts `php-fpm`. It does not run:
- `php artisan migrate`
- `php artisan db:seed`

So unless a person manually runs migrations later, the database is being built from raw SQL only.

**First Run vs Later Runs**
On first run on a new PC:
- DB gets created and initialized

On later runs:
- because of Docker volume `mssql_data`, the database stays there
- `01-init.sql` is safe because it says “create DB only if missing”
- but the init container still attempts the schema/seed script sequence each startup, so whether that remains harmless depends on how idempotent your SQL files are

**Your Project Classification From This**
On a fresh machine, your project currently behaves as:
- `database-first in execution`
- `hybrid in codebase`
- `not code-first in startup flow`

If you want, next I can give you the same explanation in a very simple “story format” for your report, like:
“when the project starts on a new computer, first Docker does this, then SQL Server does this...”


## Overview
[Add high-level workflow summary here]

## Major Features
- [Add feature]
- [Add feature]
- [Add feature]

## Workflow Template
### Feature Name
1. [Describe first step]
2. [Describe next step]
3. [Describe final step]

Expected result:
[Add expected outcome here]

## User Flows
### Public User Flow
1. [Add step]
2. [Add step]
3. [Add step]

### Admin Flow
1. [Add step]
2. [Add step]
3. [Add step]

### IT Worker Flow
1. [Add step]
2. [Add step]
3. [Add step]

### Doctor Flow
1. [Add step]
2. [Add step]
3. [Add step]

### Nurse Flow
1. [Add step]
2. [Add step]
3. [Add step]

### Patient Flow
1. [Add step]
2. [Add step]
3. [Add step]

### Donor Flow
1. [Add step]
2. [Add step]
3. [Add step]

## Edge Cases
- [Add edge case]
- [Add edge case]
- [Add edge case]

## Workflow Notes
[Add extra details here]

---

## Revision 2 - Consolidated Feature Workflow Notes

This section is the cleaner working version of the system workflows. The earlier content above is preserved as note history. This section should be used as the main readable reference.

### What `app/Services` Is Doing
In this project, `lifelink-app/app/Services` contains backend business logic that is too SQL-heavy or workflow-specific to keep directly inside controllers.

Current service classes:
- `app/Services/Sql/JobApplicationSqlService.php`
- `app/Services/Sql/ApplicationReviewSqlService.php`
- `app/Services/Sql/BloodMatchingSqlService.php`

What that means in plain words:
- controllers receive the request
- services handle the deeper workflow logic and heavier SQL work
- the service layer is where some of the system's more complex business behavior is organized

Important clarification:
- job application flow is already using services
- blood matching and donor notification flow is already using services
- bed management and much of donor donation handling are still mostly implemented in controllers, models, and direct database operations

So `app/Services` is not the entire backend. It is the place where the project currently keeps the heavier raw-SQL workflow logic.

### System Workflow Summary
The website is built around several role-based journeys. Each person uses the same system, but what they see and do depends on who they are. The core workflows already designed in the backend revolve around:
- authentication and role recognition
- job application review
- patient admission and bed assignment
- clinical care monitoring
- blood request, donor matching, and donation tracking

### Public and Entry Flow
A new visitor first arrives at the website and sees the public-facing information. From there, the person can either understand the hospital system, choose to log in, register as a normal user, or later enter a more specific role journey such as applicant or donor.

Once the person logs in, the system identifies that user's role. Based on that role, the website should send the user to the correct area. This is how the same project becomes different experiences for patients, doctors, nurses, donors, IT workers, and admins.

### Authentication and Role Flow
The system begins by creating or recognizing a user account. A new person may register as a general user and receive a basic role such as Patient. Later, an admin or approval process can allow that user to become something else, such as IT Worker, Doctor, or Donor.

When someone logs in:
1. the system checks the email and password
2. it verifies whether the account is active
3. it reads the user's role information
4. it returns access for the correct protected areas

If a user is frozen by an admin, the user cannot continue into the protected parts of the system.

### Job Application Workflow
This workflow is for people who want to join the hospital staff through the system.

Story version:
A user submits a job application through the website. The application includes the role they want and, when needed, the department they are interested in. Once submitted, the application remains in a pending state. Admins or IT-related reviewers can open the list of submitted applications and decide what to do next. If they approve it, the system adds the new role to the user. If they reject it, the status changes but the record remains visible in the system for tracking.

Step-by-step:
1. user submits a job application
2. system saves it as pending
3. admin or IT reviewer checks the application
4. reviewer approves or rejects it
5. if approved, the user's role set is updated

Expected result:
- applicants can track that something has been submitted
- admins and reviewers can manage staff onboarding through the system

### Bed and Admission Workflow
This workflow manages how patients enter hospital care and receive beds.

Story version:
A patient reaches a point where hospital admission is needed. In many cases, a doctor creates the request by recording the diagnosis and saying what level of care the patient needs, such as Ward or ICU. That admission request then becomes visible to the staff responsible for managing hospital space. The Admin or IT Worker checks the patient's department, reviews which beds are available in that department, and assigns one suitable bed.

Once the bed is assigned, the patient becomes actively linked to that bed. The bed is no longer available to anyone else. Nurses and other staff can then continue care while knowing exactly where the patient is placed. Later, when the patient is discharged, the bed is released and becomes available again.

Step-by-step:
1. a doctor or authorized staff member creates an admission
2. the system marks the patient as admitted
3. Admin or IT Worker checks available beds in the correct department
4. one available bed is assigned to the patient
5. the bed becomes occupied
6. when the patient is discharged, the assignment is released
7. the bed becomes available again

Expected result:
- admissions are tracked
- departments keep control of their own bed resources
- beds are automatically freed after discharge

### Doctor Workflow
The doctor's role is centered on patients, appointments, and admission requests.

Story version:
After logging in, the doctor sees the patients connected to that doctor's work. The doctor can review appointments, cancel an appointment when necessary, and request a bed or higher level of care for a patient. That bed request becomes part of the admission flow and is later acted on by the bed-management side of the system.

Step-by-step:
1. doctor logs in
2. doctor views profile and assigned patient-related information
3. doctor reviews appointments
4. doctor may cancel booked appointments if needed
5. doctor creates a bed or care-level request for a patient
6. the request enters the admission/bed pipeline

### Nurse Workflow
The nurse's role is focused on monitoring and follow-up.

Story version:
A nurse logs into the system and sees the patients and admissions relevant to the nurse's department. The nurse can open admission details, understand the patient placement, and record vital signs over time. This allows nursing care to be tracked in a structured way instead of being scattered.

Step-by-step:
1. nurse logs in
2. nurse sees relevant patients and admissions
3. nurse opens admission details
4. nurse checks previous vital records
5. nurse logs new vital signs

Expected result:
- nursing follow-up becomes organized and visible

### Patient Workflow
The patient's side is the self-service part of the system.

Story version:
A patient logs in and opens the patient portal. From there, the patient can view medical records, check appointments, book new appointments, cancel when allowed, and create blood requests if blood is needed. The patient can also later see the status of those blood requests and understand whether help has been arranged.

Step-by-step:
1. patient logs in
2. patient opens personal portal
3. patient views records and appointment list
4. patient books or cancels appointments
5. patient creates blood request if needed
6. patient tracks request status

### Donor Workflow
This workflow handles the donor journey from enrollment to donation.

Story version:
A user first becomes a donor in the system. The website stores that donor's blood-related profile and allows the donor to mark weekly availability. Before or during donation activity, the donor's health condition is recorded using details such as weight and temperature. If the donor is currently eligible, that donor can participate in matching and donation. When a real donation happens, the system records it and updates the blood inventory.

Step-by-step:
1. user enrolls as donor
2. system creates or updates donor profile
3. donor marks weekly availability
4. donor health data is recorded
5. system checks current eligibility
6. donation is logged when donation happens
7. blood inventory is increased

Expected result:
- donor history is stored
- eligibility is tracked
- real stock changes are reflected in the blood bank inventory

### Blood Request and Matching Workflow
This is one of the most important multi-role workflows in the project.

Story version:
When a patient needs blood, a blood request is created in the system. The request includes details such as blood group, component type, urgency, and required units. Once the request is available, the Admin or IT Worker can review it and ask the matching system to suggest compatible donors. The system checks blood compatibility, donor eligibility, and current availability. After that, suitable donors are notified.

Each donor receives a notification and can respond by accepting or declining. If donors accept, the request becomes easier to fulfill. Later, once donation actually takes place, the donation record updates the inventory and the request can move toward completion.

Step-by-step:
1. patient or staff creates blood request
2. request enters the system with pending status
3. Admin or IT Worker reviews the request
4. matching logic searches for compatible available donors
5. selected donors are notified
6. donors accept or decline
7. accepted donors become visible in the request history
8. when donation is recorded, inventory is updated

Expected result:
- urgent blood needs are turned into a trackable workflow
- staff can see who was notified and who responded
- accepted donor activity supports fulfillment of the request

### Admin Workflow
Admins have the broadest visibility and control.

Story version:
An admin can manage users, freeze or unfreeze accounts, review applications, and in some cases also perform actions that support staff setup. Admins are also the most suitable role for advanced diagnostics and technical verification areas because they need system-wide awareness.

Step-by-step:
1. admin logs in
2. admin monitors users and system status
3. admin freezes or unfreezes accounts if necessary
4. admin reviews applications
5. admin manages staff-related setup tasks
6. admin may access advanced diagnostic views

### IT Worker Workflow
The IT Worker acts like an operations coordinator for department infrastructure and blood-matching support.

Story version:
After logging in, the IT Worker works on department-linked operational tasks. That person may manage ward setup structures, review admissions, assign beds inside allowed departments, and coordinate blood matching by checking requests and notifying donors. The IT Worker does not just view information; they help move the hospital workflow forward.

Step-by-step:
1. IT Worker logs in
2. system limits the worker to assigned departments when needed
3. worker manages care units and beds
4. worker checks admissions and assigns available beds
5. worker reviews blood requests
6. worker sends donor notifications through matching tools

### Important Structural Finding
Based on the current design:
- one Department can have many CareUnits
- one CareUnit can have many Beds

This means beds are separated department by department through their care units. A ward or ICU is not itself a single bed. It is the container or unit that holds many beds.

### Edge Cases and Process Notes
- a frozen user should not proceed into protected features
- a patient should not receive multiple active admissions at the same time
- a bed cannot be assigned if it is already occupied
- a bed must belong to the same department as the admission
- a donor should not be treated as eligible if the health data says otherwise
- a closed blood request should not continue sending donor notifications

### Workflow Direction for Future UI
The UI should present these workflows as human stories, guided actions, and structured dashboards rather than raw endpoint testing pages. The goal is for someone unfamiliar with the code to understand what the hospital system is doing just by following the screens.
