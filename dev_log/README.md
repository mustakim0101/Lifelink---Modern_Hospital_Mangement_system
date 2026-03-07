# Dev Notes (Q&A)

## 🚀 Phase 1: Infrastructure (2 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To -->>DONE  |
|:---:|-------------|----------------|:---:|:---:|
| **1** | **Setup Docker environment with MSSQL** | `chore: dockerize laravel with mssql 2022` | `main` | musa|
| **2** | **Configure MSSQL database connection** | `fix: establish mssql connection and test migrations` | `main` | musa |

## 🔐 Phase 2: Identity & RBAC (3 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **3** | **Install JWT & implement authentication** | `feat(auth): install jwt-auth with login/register` | `dev` | musa|
| **4** | **Create RBAC database schema** | `feat(rbac): migrations for users, roles, permissions` | `dev` | musa |
| **5** | **Build role middleware & account controls** | `feat(rbac): role middleware with freeze/unfreeze` | `dev` | musa |

## 📝 Phase 3: Hiring Flow (3 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **6** | **Department & application tables** | `feat(hiring): migrations for departments and applications` | `dev` | musa |
| **7** | **Job application submission feature** | `feat(hiring): applicant submission with status tracking` | `dev` | musa |
| **8** | **Admin approval workflow** | `feat(hiring): admin/it approval with auto-role assignment` | `dev` | musa |

## 🏥 Phase 4: Bed Management (3 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **9** | **Bed/ICU/Ward schema** | `feat(beds): migrations for care_units and beds` | `dev` | Database Architect |
| **10** | **IT worker bed assignment** | `feat(beds): it-worker dashboard for bed allocation` | `dev` | Frontend Developer |
| **11** | **Discharge & bed release** | `feat(beds): auto-release bed on patient discharge` | `dev` | Backend Developer |

## 👨‍⚕️ Phase 5: Clinical Operations (4 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **12** | **Clinical data schema** | `feat(clinical): migrations for patients, appointments, records` | `dev` | Database Architect |
| **13** | **Doctor dashboard & actions** | `feat(clinical): doctor management of patients and bed requests` | `dev` | Frontend Developer |
| **14** | **Nurse care dashboard** | `feat(clinical): nurse view for dept-wise patient monitoring` | `dev` | Frontend Developer |
| **15** | **Patient portal** | `feat(clinical): patient portal for records and blood requests` | `dev` | Full Stack Developer |

## 🩸 Phase 6: Blood Bank (3 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **16** | **Blood bank schema** | `feat(blood): migrations for donors, inventory, requests` | `dev` | Database Architect |
| **17** | **Donor dashboard & tracking** | `feat(blood): donor availability, weight, temp, bag logging` | `dev` | Full Stack Developer |
| **18** | **Blood matching system** | `feat(blood): it-worker matching with donor notifications` | `dev` | Backend Developer |

## ✅ Phase 7: Final Polish (3 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **19** | **Comprehensive testing** | `test: feature tests for all role workflows` | `dev` | QA Engineer |
| **20** | **API documentation** | `docs: swagger/openapi documentation for all endpoints` | `dev` | Technical Writer |
| **21** | **Deployment preparation** | `chore: deployment config and environment setup` | `dev` | DevOps Engineer |






## Phase 1 - Issue 1: Setup Docker environment with MSSQL

This repository now includes Docker scaffolding for Laravel + MSSQL.

### Added files
- `docker-compose.yml`
- `docker/Dockerfile`
- `docker/nginx/default.conf`
- `docker/mssql/init/01-init.sql`
- `.env.docker`
- `scripts/setup-laravel.ps1`

### Quick start
1. Copy Docker environment variables:
   - PowerShell: `Copy-Item .env.docker .env`
2. Bootstrap Laravel app (Laravel 10) into `lifelink-app/`:
   - PowerShell: `./scripts/setup-laravel.ps1`
3. Start containers:
   - `docker compose up -d --build`
4. Install PHP dependencies inside app container (if needed):
   - `docker compose exec app composer install`
5. Copy Laravel env file and set DB values:
   - `docker compose exec app cp .env.example .env`
   - Set in `lifelink-app/.env`:
     - `DB_CONNECTION=sqlsrv`
     - `DB_HOST=mssql`
     - `DB_PORT=1433`
     - `DB_DATABASE=lifelink`
     - `DB_USERNAME=sa`
     - `DB_PASSWORD=<same as MSSQL_SA_PASSWORD>`
6. Generate app key and run migrations:
   - `docker compose exec app php artisan key:generate`
   - `docker compose exec app php artisan migrate`

### Service endpoints
- Laravel (nginx): `http://localhost:8000`
- MSSQL: `localhost:1433`

### Notes
- MSSQL database `lifelink` is initialized by `mssql-init` service.
- Change `MSSQL_SA_PASSWORD` in `.env` before first run.

## Phase 1 - Issue 2: Configure MSSQL connection and test migrations

### Connection updates
- Laravel app uses `sqlsrv` in `lifelink-app/.env`.
- SQL Server TLS options are enabled in `lifelink-app/config/database.php`:
  - `encrypt`
  - `trust_server_certificate`
- App image includes Microsoft ODBC Driver 18.

### Verification commands
1. `docker compose up -d --build`
2. `docker compose exec app php artisan key:generate --force`
3. `docker compose exec app php artisan migrate --force`
4. `docker compose exec app php artisan migrate:status`

### Expected migrated tables (default Laravel)
- `migrations`
- `users`
- `password_reset_tokens`
- `failed_jobs`
- `personal_access_tokens`


-->>now opens the laravel open home pg at http://localhost:8000


## 1) Laravel home page load flow (`http://localhost:8000`)

In this project, Laravel lives inside `lifelink-app/`.

Current home route chain is:

Browser request (`GET /` on localhost:8000)
-> `lifelink-app/public/index.php` (Laravel entry point)
-> `lifelink-app/bootstrap/app.php` (build app container + bind HTTP Kernel)
-> `App\Http\Kernel` (`lifelink-app/app/Http/Kernel.php`) runs global + `web` middleware
-> `App\Providers\RouteServiceProvider` (`lifelink-app/app/Providers/RouteServiceProvider.php`) loads `routes/web.php`
-> `lifelink-app/routes/web.php` matches `Route::get('/', fn () => view('welcome'))`
-> Blade view file `lifelink-app/resources/views/welcome.blade.php`
-> HTML response returned to browser

### Which folder is responsible for home page load?
- Main app folder: `lifelink-app/`
- Request entry starts in: `lifelink-app/public/`
- Route definition is in: `lifelink-app/routes/`
- UI blade file is in: `lifelink-app/resources/views/`

So practically: `lifelink-app` is the responsible project folder, and `public/index.php` is the first executed file.

---

## 2) What is `scripts/setup-laravel.ps1`?

File: `S:\Lifelink---Modern_Hospital_Mangement_system\scripts\setup-laravel.ps1`

Purpose:
- Bootstraps a fresh Laravel project in `lifelink-app` using Docker Composer image.
- Runs only when `lifelink-app` is empty.
- Command inside script: creates project with `composer create-project laravel/laravel lifelink-app ^10.0`.

### If you delete it, what happens?
- Existing app (`lifelink-app`) will keep working.
- You only lose this convenience automation script for recreating/bootstraping Laravel.
- Any docs/commands that reference this script will fail until updated.

### If you already committed it, then delete and commit again?
- Yes, Git/GitHub fully allow this.
- New commit will simply record file deletion.
- History still keeps old versions in previous commits.
- Typical flow:
  1. `git rm scripts/setup-laravel.ps1`
  2. `git commit -m "chore: remove setup-laravel bootstrap script"`
  3. `git push`

---

## Important for next issues (Phase 2 onward)

- I do **not** have persistent memory from previous chat sessions unless details are present in this repo/thread.
- If your feature list, table design, Postman collection/results, and demo folder structure are not already in project files here, please share them again before coding Issue #3.

---

## Logging format to use after each issue (we will keep updating this file)

For each completed issue, add:
1. Issue title + commit message + branch
2. Files created/updated/deleted
3. Functional flowchart (request-to-response chain)
4. Test evidence (Postman/screenshots/commands)

---

## Phase 2 - Issue 3 (Implemented)

Issue: Install JWT & implement authentication
Commit message target: `feat(auth): install jwt-auth with login/register`
Branch target: `dev`

### Files created/updated
- Updated: `lifelink-app/composer.json` (added `tymon/jwt-auth`)
- Updated: `lifelink-app/config/auth.php` (added `api` guard with `jwt` driver)
- Updated: `lifelink-app/app/Models/User.php` (implements `JWTSubject`)
- Created: `lifelink-app/app/Http/Controllers/Api/AuthController.php`
- Updated: `lifelink-app/routes/api.php`

### Auth flowchart (JWT API login)
User input (`POST /api/auth/login` with email/password)
-> Route (`lifelink-app/routes/api.php`)
-> Controller (`App\Http\Controllers\Api\AuthController@login`)
-> Validation (request validation in controller)
-> Auth guard (`auth('api')->attempt(...)` using JWT driver)
-> User model lookup (`App\Models\User`)
-> MSSQL database query (via configured DB connection)
-> Password verification (Laravel Hash check inside auth attempt)
-> JWT token generated (if valid)
-> JSON response (`token`, `expires_in`, `user`) returned

### Notes
- UI Blade login page is not part of this issue; this issue is API JWT auth.
- For this to run, install dependencies and set JWT secret before testing.
- Added compatibility with `fullName` request field and `token` response field for Postman alignment.
- Added local-only bootstrap endpoint: `POST /api/dev/create-admin`.
- Updated by install step: `lifelink-app/composer.lock`
- Created by package publish: `lifelink-app/config/jwt.php`
- Generated env key: `lifelink-app/.env` now includes `JWT_SECRET`.

---

## Run + Verify Now (Phase 2 Issue 3)

Use this from project root:
`S:\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start containers
1. `docker compose up -d --build`
2. `docker compose ps`

Expected:
- `lifelink_app`, `lifelink_web`, `lifelink_mssql` should be `Up`.
- Web app should open at `http://localhost:8000`.

### 2) Ensure Laravel DB tables exist
1. `docker compose exec app php artisan migrate --force`
2. `docker compose exec app php artisan migrate:status`

Expected:
- `users`, `password_reset_tokens`, `failed_jobs`, `personal_access_tokens`, `migrations` are migrated.

### 3) Confirm API routes for Issue 3
Run:
`docker compose exec app php artisan route:list --path=api`

Expected routes:
- `POST api/auth/register`
- `POST api/auth/login`
- `GET api/auth/me`
- `POST api/auth/logout`
- `POST api/auth/refresh`
- `POST api/dev/create-admin`

### 4) Postman test sequence (Base URL)
Use base URL:
`http://localhost:8000/api`

#### A) Create bootstrap admin user
- `POST /dev/create-admin`
- Body:
```json
{
  "email": "admin@demo.com",
  "password": "admin12345",
  "fullName": "Admin Demo"
}
```

Expected:
- First time: `201` with `token` + `user`.
- If already exists: validation error (`email already taken`).

#### B) Login test
- `POST /auth/login`
- Body:
```json
{
  "email": "admin@demo.com",
  "password": "admin12345"
}
```

Expected:
- `200` with `token`, `expires_in`, and user info.

#### C) Test protected endpoint (`/me`)
- `GET /auth/me`
- Header: `Authorization: Bearer <token>`

Expected:
- `200` with current user object.
- Without token: `401 Unauthorized`.

#### D) Token refresh
- `POST /auth/refresh` with Bearer token

Expected:
- `200` with a new `token`.

#### E) Logout
- `POST /auth/logout` with Bearer token

Expected:
- `200` with message `Logged out`.

### 5) What to expect on browser
- `http://localhost:8000` shows Laravel welcome page.
- no Blade login UI .

### 6) Quick troubleshooting
- If login always fails: confirm user exists via `/dev/create-admin` and use exact password.
- If API gives 500: run `docker compose logs app --tail 100`.
- If DB error: verify `lifelink-app/.env` DB settings (`DB_HOST=mssql`, `DB_PORT=1433`, `DB_DATABASE=lifelink`, `DB_USERNAME=sa`).
- If token issues: ensure `JWT_SECRET` exists in `lifelink-app/.env`.

---

## Phase 2 Re-run Fix (Clone on another drive: migration timeout)

### Reported problem
- `php artisan migrate` failed with:
  - `SQLSTATE[HYT00] ... Login timeout expired`
- In re-runs, setup was unstable after `docker compose down -v` and fresh `up -d`.
- Risk of env mismatch in fresh clone (`.env.example` defaulted to MySQL settings).

### Root causes found
1. SQL Server readiness race:
   - Compose only ensured container start order, not DB readiness.
   - Existing `mssql-init` used a fixed `sleep 25`, which can be insufficient on slower startups.
2. Fresh-clone env mismatch risk:
   - `lifelink-app/.env.example` had MySQL defaults (`mysql`, port `3306`), which conflicts with project MSSQL setup.

### Files changed to fix
1. `docker-compose.yml`
   - Added MSSQL healthcheck (`tcp 1433`).
   - Changed `app` dependency to wait for MSSQL health.
   - Changed `mssql-init` dependency to wait for MSSQL health.
2. `docker/mssql/init/init-db.sh` (new)
   - Added robust DB init script with:
     - sqlcmd binary auto-detection (`/opt/mssql-tools18/bin/sqlcmd` fallback `/opt/mssql-tools/bin/sqlcmd`)
     - readiness retries (`SELECT 1`, up to 120 seconds)
     - database creation script execution only after SQL Server is ready
3. `lifelink-app/.env.example`
   - Switched defaults from MySQL to MSSQL:
     - `DB_CONNECTION=sqlsrv`
     - `DB_HOST=mssql`
     - `DB_PORT=1433`
     - `DB_DATABASE=lifelink`
     - `DB_USERNAME=sa`
     - `DB_PASSWORD=YourStrong!Passw0rd`
     - `DB_ENCRYPT=yes`
     - `DB_TRUST_SERVER_CERTIFICATE=true`

### Why this solves it
- Migrations no longer race against SQL Server boot time.
- Init script no longer depends on a fragile single sleep duration.
- New clones start with SQL Server-compatible app defaults instead of MySQL defaults.

### Re-run commands (clean test)
1. `docker compose down -v`
2. `Copy-Item .env.docker .env -Force`
3. `docker compose up -d --build`
4. `docker compose exec app cp -n .env.example .env`
5. `docker compose exec app php artisan key:generate --force`
6. `docker compose exec app php artisan config:clear`
7. `docker compose exec app php artisan migrate --force`

---

## Phase 2 - Issue 4 (Implemented)

Issue: Create RBAC database schema  
Commit message target: `feat(rbac): migrations for users, roles, permissions`  
Branch target: `dev`

### Files created/updated
- Created: `lifelink-app/database/migrations/2026_03_06_000100_add_rbac_fields_to_users_table.php`
- Created: `lifelink-app/database/migrations/2026_03_06_000110_create_roles_table.php`
- Created: `lifelink-app/database/migrations/2026_03_06_000120_create_permissions_table.php`
- Created: `lifelink-app/database/migrations/2026_03_06_000130_create_user_roles_table.php`
- Created: `lifelink-app/database/migrations/2026_03_06_000140_create_role_permissions_table.php`

### RBAC schema flow
`users` (base identity)  
-> `roles` (role catalog)  
-> `permissions` (permission catalog)  
-> `user_roles` (many-to-many user-role assignment with `assigned_at`, `assigned_by_user_id`)  
-> `role_permissions` (many-to-many role-permission mapping with `granted_at`)

### Users table extensions added
- `full_name` (nullable)
- `phone` (nullable)
- `date_of_birth` (nullable)
- `gender` (nullable)
- `account_status` (default: `Active`, indexed)
- `frozen_at` (nullable)
- `frozen_by_user_id` (nullable FK to users)

### MSSQL compatibility note
- During validation, SQL Server rejected `ON DELETE SET NULL` self/cross-user metadata FKs due to multiple cascade path rules.
- Fix applied: metadata FKs (`frozen_by_user_id`, `assigned_by_user_id`) use default `NO ACTION` behavior instead of cascading delete actions.

### Verification commands
1. `docker compose exec app php artisan migrate:fresh --force`
2. `docker compose exec app php artisan migrate:status`

### Verification result
- All default Laravel migrations + all Issue 4 RBAC migrations ran successfully on MSSQL.
- New RBAC tables now exist:
  - `roles`
  - `permissions`
  - `user_roles`
  - `role_permissions`

---

## Phase 2 - Issue 5 (Implemented)

Issue: Build role middleware & account controls  
Commit message target: `feat(rbac): role middleware with freeze/unfreeze`  
Branch target: `dev`

### Files created/updated
- Created: `lifelink-app/app/Http/Middleware/RoleMiddleware.php`
- Created: `lifelink-app/app/Http/Middleware/EnsureUserIsActive.php`
- Created: `lifelink-app/app/Http/Controllers/Api/Admin/AccountControlController.php`
- Created: `lifelink-app/app/Models/Role.php`
- Created: `lifelink-app/app/Models/Permission.php`
- Updated: `lifelink-app/app/Http/Kernel.php`
- Updated: `lifelink-app/app/Models/User.php`
- Updated: `lifelink-app/app/Http/Controllers/Api/AuthController.php`
- Updated: `lifelink-app/routes/api.php`

### Implemented behavior
1. Role middleware:
   - New route middleware alias: `role`
   - Checks `user_roles` -> `roles.role_name` for authorization
2. Active-account middleware:
   - New route middleware alias: `active.user`
   - Blocks frozen users from protected API access
3. Account control endpoints (Admin only):
   - `POST /api/admin/users/{user}/freeze`
   - `POST /api/admin/users/{user}/unfreeze`
   - `GET /api/admin/users/{user}/status`
4. Auth flow updates:
   - Register assigns `Patient` role automatically
   - Dev create-admin assigns `Admin` role automatically
   - Login denies frozen users (`403`)
   - Token response now includes `roles` and `account_status`

### Access control flow
JWT auth (`auth:api`)
-> active user check (`active.user`)
-> role check (`role:Admin`)
-> admin controller action (freeze/unfreeze/status)

### Validation evidence
- `php artisan route:list --path=api` shows admin freeze/unfreeze/status routes.
- Live API check:
  - Created admin (`/api/dev/create-admin`) -> role assigned `Admin`
  - Registered patient (`/api/auth/register`) -> role assigned `Patient`
  - Admin froze patient (`/api/admin/users/{id}/freeze`) -> `account_status=Frozen`
  - Patient login after freeze -> blocked with `403` and message `Account is frozen. Contact admin.`

## Run + Verify Now (Phase 2 Issue 5)

Use this from project root:
`S:\Lifelink---Modern_Hospital_Mangement_system`

### 1) Ensure app is running and migrations are applied
1. `docker compose ps`
2. `docker compose exec app php artisan migrate --force`
3. `docker compose exec app php artisan route:list --path=api`

Expected extra routes for Issue 5:
- `POST api/admin/users/{user}/freeze`
- `POST api/admin/users/{user}/unfreeze`
- `GET api/admin/users/{user}/status`

### 2) Postman verification flow (API-only)
Base URL:
`http://localhost:8000/api`

#### A) Create admin (bootstrap)
- `POST /dev/create-admin`
- Body:
```json
{
  "email": "admin@demo.com",
  "password": "admin12345",
  "fullName": "Admin Demo"
}
```
- Save returned `token` as `ADMIN_TOKEN`.

#### B) Register normal user (patient role auto-assign)
- `POST /auth/register`
- Body:
```json
{
  "email": "patient1@demo.com",
  "password": "patient12345",
  "fullName": "Patient One"
}
```
- Save returned `user.id` as `PATIENT_ID`.

#### C) Freeze user as admin
- `POST /admin/users/{{PATIENT_ID}}/freeze`
- Header: `Authorization: Bearer {{ADMIN_TOKEN}}`

Expected:
- `200` with `user.account_status = Frozen`.

#### D) Confirm frozen user cannot login
- `POST /auth/login`
- Body:
```json
{
  "email": "patient1@demo.com",
  "password": "patient12345"
}
```

Expected:
- `403` with message:
`Account is frozen. Contact admin.`

#### E) Unfreeze user as admin
- `POST /admin/users/{{PATIENT_ID}}/unfreeze`
- Header: `Authorization: Bearer {{ADMIN_TOKEN}}`

Expected:
- `200` with `user.account_status = Active`.

#### F) Check status endpoint
- `GET /admin/users/{{PATIENT_ID}}/status`
- Header: `Authorization: Bearer {{ADMIN_TOKEN}}`

Expected:
- `200` with account status + roles.

### 3) UI status for Issue 5
- No frontend UI/dashboard screen is implemented yet for freeze/unfreeze.
- Issue 5 is currently implemented and verified through API endpoints (Postman/cURL).

### 4) If Postman returns Laravel HTML page
- Cause: request is going to web root (`/`) or wrong method/path.
- Fix checklist:
  1. Method must be `POST` (not GET).
  2. Full URL must be exactly: `http://localhost:8000/api/dev/create-admin`
  3. Body type: `raw` -> `JSON`
  4. Header: `Content-Type: application/json`
  5. Header: `Accept: application/json`
  6. Restart request tab and send again.

Quick terminal check:
`curl -X POST http://localhost:8000/api/dev/create-admin -H "Content-Type: application/json" -H "Accept: application/json" -d "{\"email\":\"admin@demo.com\",\"password\":\"admin12345\",\"fullName\":\"Admin Demo\"}"`

---

## Phase 3 - Issue 6 (Implemented)

Issue: Department & application tables  
Commit message target: `feat(hiring): migrations for departments and applications`  
Branch target: `dev`

### Files created/updated
- Created: `lifelink-app/database/migrations/2026_03_06_000200_create_departments_table.php`
- Created: `lifelink-app/database/migrations/2026_03_06_000210_create_job_applications_table.php`

### Tables added
1. `departments`
   - `id` (PK)
   - `dept_name` (unique)
   - `is_active` (default true)
   - `timestamps`

2. `job_applications`
   - `id` (PK)
   - `user_id` (FK -> `users.id`)
   - `applied_role_id` (FK -> `roles.id`)
   - `applied_department_id` (nullable FK -> `departments.id`)
   - `status` (default `Pending`, indexed)
   - `applied_at`
   - `reviewed_by_user_id` (nullable FK -> `users.id`)
   - `reviewed_at` (nullable)
   - `review_notes` (nullable)
   - `timestamps`
   - index: (`user_id`, `status`)

### Hiring flow mapping (Issue 6 scope)
Applicant user (`users`)
-> chooses target role (`roles`)
-> optionally chooses department (`departments`)
-> submits record in `job_applications` with `Pending` status.

### Verification commands
1. `docker compose exec app php artisan migrate --force`
2. `docker compose exec app php artisan migrate:status`

### Verification result
- Issue 6 migrations ran successfully on MSSQL:
  - `2026_03_06_000200_create_departments_table`
  - `2026_03_06_000210_create_job_applications_table`

---
## Phase 3 - Issue 7 (Implemented)


Issue: Job application submission feature  
Commit message target: `feat(hiring): applicant submission with status tracking`  
Branch target: `dev`

### Files created/updated
- Created: `lifelink-app/app/Models/Department.php`
- Created: `lifelink-app/app/Models/JobApplication.php`
- Created: `lifelink-app/app/Http/Controllers/Api/JobApplicationController.php`
- Updated: `lifelink-app/routes/api.php`
- Updated: `lifelink-app/app/Http/Controllers/Api/AuthController.php`
- Updated: `lifelink-app/routes/web.php`
- Created: `lifelink-app/resources/views/ui/index.blade.php`
- Created: `lifelink-app/resources/views/ui/auth.blade.php`
- Created: `lifelink-app/resources/views/ui/applications.blade.php`
- Created: `lifelink-app/resources/views/ui/admin-users.blade.php`

### Pages created in this issue
1. `/ui`
   - UI landing page for completed backend features.
2. `/ui/auth`
   - Create admin, register patient/user, login.
   - Shows/stores IDs and tokens in a context panel.
   - Stores test keys in localStorage:
     - `ADMIN_TOKEN`, `ADMIN_USER_ID`, `ADMIN_EMAIL`
     - `USER_TOKEN`
     - `PATIENT_ID`, `PATIENT_EMAIL`, `PATIENT_PASSWORD`
3. `/ui/applications`
   - Submit job application.
   - View `my` and `my/latest`.
   - Saves and shows last application snapshot.
4. `/ui/admin-users`
   - Admin freeze/unfreeze/status.
   - Auto-loads stored `PATIENT_ID`.
   - Has built-in "Test Patient Login (frozen check)" button.

### Program flow (page to backend)
#### A) UI route and page load flow
Browser `GET /ui/*`
-> `lifelink-app/routes/web.php`
-> Blade view file in `lifelink-app/resources/views/ui/*.blade.php`
-> HTML + JS rendered in browser.

#### B) Auth page flow (`/ui/auth`)
UI button click (Create Admin/Register/Login)
-> `fetch('/api/...')` from `resources/views/ui/auth.blade.php`
-> `routes/api.php`
-> `AuthController` method
-> DB (`users`, `roles`, `user_roles`)
-> JSON response shown in page `<pre>` output.
-> important IDs/tokens saved to localStorage.

#### C) Applications page flow (`/ui/applications`)
UI button click (Submit / My / My Latest)
-> `fetch('/api/applications...')` from `resources/views/ui/applications.blade.php`
-> `routes/api.php`
-> `JobApplicationController` (`submit`, `myApplications`, `myLatest`)
-> DB (`job_applications`, `roles`, `departments`, `user_roles`)
-> JSON response shown in page.

#### D) Admin account control flow (`/ui/admin-users`)
UI button click (Freeze / Unfreeze / Status)
-> `fetch('/api/admin/users/{id}/...')` from `resources/views/ui/admin-users.blade.php`
-> `routes/api.php` + middleware (`auth:api`, `active.user`, `role:Admin`)
-> `Api\Admin\AccountControlController`
-> DB update/read on `users`
-> JSON response shown in page.

#### E) Frozen login verification flow from UI
UI click "Test Patient Login (frozen check)" on `/ui/admin-users`
-> reads `PATIENT_EMAIL` + `PATIENT_PASSWORD` from localStorage
-> `POST /api/auth/login`
-> if frozen, backend returns `403` with message:
`Account is frozen. Contact admin.`

### Backend behavior implemented (Issue 7 core)
- `POST /api/applications`
  - Creates `job_applications` row with status `Pending`
  - Assigns `Applicant` role automatically if missing
  - Accepts `appliedRole`/`applied_role_id` and optional department id
- `GET /api/applications/my`
  - Returns authenticated user's application history (latest first)
- `GET /api/applications/my/latest`
  - Returns latest application status
- Duplicate pending protection:
  - Re-submit while pending -> `409` with message:
    - `You already have a pending application.`
- Auth response update:
  - `latestApplication` included in auth token response.

### Verification commands
1. `docker compose exec app php artisan route:list --path=api`
2. `docker compose exec app php artisan route:list --path=ui`

### Run + Verify Now (Issue 7 with UI + API)
Use from project root:
`S:\Lifelink---Modern_Hospital_Mangement_system`

#### 1) Start/verify app
1. `docker compose up -d --build`
2. `docker compose ps`
3. `docker compose exec app php artisan migrate --force`
4. `docker compose exec app php artisan route:list --path=api`
5. `docker compose exec app php artisan route:list --path=ui`

#### 2) What you will see in browser
1. Open `http://localhost:8000/ui`
   - A simple menu page with links to Auth, Applications, Admin Account Control.
2. Open `http://localhost:8000/ui/auth`
   - Three cards: Create Admin, Register Patient/User, Login.
   - `Stored Test Context` block shows ids/tokens available for test flow.
3. Open `http://localhost:8000/ui/applications`
   - Submit role + optional department id.
   - Buttons for `Get My Latest` and `Get My Applications`.
   - `Latest Application Snapshot` block updates after submit/status calls.
4. Open `http://localhost:8000/ui/admin-users`
   - Freeze/unfreeze/status by user id (auto-filled from stored `PATIENT_ID`).
   - Button `Test Patient Login (frozen check)` verifies if freeze is enforced.

#### 3) Full UI test scenario (no manual ID hunting)
A) Go to `/ui/auth` -> Create admin  
- Use new email  
- Expect token response + admin context stored.

B) Same page `/ui/auth` -> Register patient/user  
- Use new email  
- Expect response includes `user.id`  
- `PATIENT_ID`, `PATIENT_EMAIL`, `PATIENT_PASSWORD`, `USER_TOKEN` stored automatically.

C) Go to `/ui/applications` -> Submit application  
- `appliedRole`: e.g., `Doctor`  
- optional `departmentId`: e.g., `1`  
- Expect: `Application submitted`, status `Pending`.

D) Still on `/ui/applications` -> click `Get My Latest` and `Get My Applications`  
- Expect latest/history to show pending application.

E) Go to `/ui/admin-users` -> click `Use Stored PATIENT_ID` -> click `Freeze`  
- Expect account status changes to `Frozen`.

F) On `/ui/admin-users` -> click `Test Patient Login (frozen check)`  
- Expect `403`, `Account is frozen. Contact admin.`

G) On `/ui/admin-users` -> click `Unfreeze` -> then `Test Patient Login` again  
- Expect login success with token response.

#### 4) Postman steps (optional parallel verification)
Base URL: `http://localhost:8000/api`
1. `POST /dev/create-admin`
2. `POST /auth/register`
3. `POST /applications` with bearer user token
4. `GET /applications/my/latest`
5. `POST /admin/users/{id}/freeze` with bearer admin token
6. `POST /auth/login` for patient (expect `403`)
7. `POST /admin/users/{id}/unfreeze`

### Verification result
- New routes visible:
  - `POST api/applications`
  - `GET api/applications/my`
  - `GET api/applications/my/latest`
- New UI routes visible:
  - `GET /ui`
  - `GET /ui/auth`
  - `GET /ui/applications`
  - `GET /ui/admin-users`
- Live verification passed:
  - UI pages load with HTTP `200`
  - UI now exposes/stores patient/admin IDs and tokens for complete manual test flow
  - API submit message: `Application submitted`
  - status tracking works (`Pending`)
  - duplicate pending returns `409` conflict

---

## Phase 3 - Issue 8 (Implemented)

Issue: Admin approval workflow  
Commit message target: `feat(hiring): admin/it approval with auto-role assignment`  
Branch target: `dev`

### New files created (Issue 8)
- `lifelink-app/app/Http/Controllers/Api/Admin/ApplicationReviewController.php`
- `lifelink-app/resources/views/ui/application-reviews.blade.php`

### Existing files updated (Issue 8)
- `lifelink-app/routes/api.php`
- `lifelink-app/routes/web.php`
- `lifelink-app/resources/views/ui/index.blade.php`
- `dev_log/README.md`

### API endpoints added
- `GET /api/admin/applications` (Admin/ITWorker)
- `POST /api/admin/applications/{application}/approve` (Admin/ITWorker)
- `POST /api/admin/applications/{application}/reject` (Admin/ITWorker)

### Issue 8 behavior implemented
1. Admin/IT can list applications with optional status filter:
   - `?status=Pending|Approved|Rejected`
2. Approve workflow:
   - Allowed only when current status is `Pending`
   - Sets `status=Approved`, `reviewed_by_user_id`, `reviewed_at`, optional `review_notes`
   - Auto-assigns the applied role to the applicant in `user_roles` (if not already assigned)
   - Removes `Applicant` role after approval
3. Reject workflow:
   - Allowed only when current status is `Pending`
   - Sets `status=Rejected`, review metadata, optional notes
4. Non-pending review protection:
   - Approve/reject on non-pending returns `409`

### UI added for Issue 8 testing
- New page: `/ui/application-reviews`
  - Uses `ADMIN_TOKEN` from localStorage
  - Load applications by status
  - Approve/reject by application id with optional review notes

### Flowchart (Issue 8)
Admin/IT login (JWT)
-> `GET /api/admin/applications` to fetch pending items
-> Select application id
-> `POST /api/admin/applications/{id}/approve` or `/reject`
-> On approve: update `job_applications` + assign role in `user_roles` + remove `Applicant`
-> Applicant next login shows updated roles

### Live verification evidence (Issue 8)
- Route verification:
  - `api/admin/applications`
  - `api/admin/applications/{application}/approve`
  - `api/admin/applications/{application}/reject`
- End-to-end API run result:
  - submitted application id: `4`
  - review status after approve: `Approved`
  - applied role: `Doctor`
  - applicant login roles after approval: `Patient,Doctor`

## Run + Verify Now (Up to Issue 8)

Use this from project root:
`S:\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start stack
1. `Copy-Item .env.docker .env -Force`
2. `docker compose up -d --build`
3. `docker compose ps`

Expected:
- `lifelink_app`, `lifelink_web`, `lifelink_mssql` are `Up`
- App responds at `http://localhost:8000`

### 2) Verify migrations/routes
1. `docker compose exec app php artisan migrate --force`
2. `docker compose exec app php artisan route:list --path=api/admin/applications`
3. `docker compose exec app php artisan route:list --path=api/applications`

### 3) Browser verify pages
1. `http://localhost:8000/ui`
2. `http://localhost:8000/ui/auth`
3. `http://localhost:8000/ui/applications`
4. `http://localhost:8000/ui/admin-users`
5. `http://localhost:8000/ui/application-reviews`

### 4) End-to-end approval test
1. On `/ui/auth`: create admin, register/login applicant.
2. On `/ui/applications`: submit application (`appliedRole=Doctor` for quick test).
3. On `/ui/application-reviews`: load `Pending`, take application id, click `Approve`.
4. On `/ui/auth` (login applicant again): check roles now include approved role.

Expected:
- Application status transitions `Pending -> Approved`
- Applicant role assignment updates automatically in `user_roles`
- `Applicant` role is removed after approval

---

## Phase 4 - Issue 9 (Implemented)

Issue: Bed/ICU/Ward schema  
Commit message target: `feat(beds): migrations for care_units and beds`  
Branch target: `dev`

### New files created (Issue 9)
- `lifelink-app/database/migrations/2026_03_07_000300_create_care_units_table.php`
- `lifelink-app/database/migrations/2026_03_07_000310_create_beds_table.php`
- `lifelink-app/app/Models/CareUnit.php`
- `lifelink-app/app/Models/Bed.php`
- `lifelink-app/app/Http/Controllers/Api/WardCatalogController.php`
- `lifelink-app/resources/views/ui/ward-setup.blade.php`

### Existing files updated (Issue 9)
- `lifelink-app/app/Models/Department.php`
- `lifelink-app/routes/api.php`
- `lifelink-app/routes/web.php`
- `lifelink-app/resources/views/ui/index.blade.php`
- `dev_log/postman_codes_testing_apipoints_with_tables.txt` (localhost 5000 -> 8000 updates done before Issue 9 work)
- `dev_log/README.md`

### Schema implemented
1. `care_units`
   - `id` (PK)
   - `department_id` (FK -> `departments.id`)
   - `unit_type` (`Ward|ICU|NICU|CCU`)
   - `unit_name` (nullable)
   - `floor` (nullable)
   - `is_active` (default true)
   - `timestamps`

2. `beds`
   - `id` (PK)
   - `care_unit_id` (FK -> `care_units.id`)
   - `bed_code`
   - `status` (`Available|Occupied|Maintenance|Reserved`, default `Available`)
   - `is_active` (default true)
   - `timestamps`
   - unique: (`care_unit_id`, `bed_code`)

### Backend APIs added (Issue 9)
Protected with `auth:api` + `active.user`:
- `GET /api/ward/departments`
- `GET /api/ward/care-units`
- `GET /api/ward/beds`
- `GET /api/ward/beds/summary`

Create APIs protected with `role:Admin,ITWorker`:
- `POST /api/ward/care-units`
- `POST /api/ward/beds`

### UI added (Issue 9)
- New page: `GET /ui/ward-setup`
  - Create care unit
  - Create bed
  - List departments/care units/beds
  - View bed summary
  - Reads token from localStorage (`ADMIN_TOKEN`/`USER_TOKEN`)

### Flowchart (Issue 9)
`/ui/ward-setup` button click
-> fetch `/api/ward/...`
-> API route in `routes/api.php`
-> `WardCatalogController`
-> `CareUnit` / `Bed` Eloquent + MSSQL tables
-> JSON response in UI panel

### Verification evidence run
1. `docker compose exec app php artisan migrate --force`
   - migrated:
     - `2026_03_07_000300_create_care_units_table`
     - `2026_03_07_000310_create_beds_table`
2. `docker compose exec app php artisan route:list --path=api/ward`
   - shows 6 ward routes (list/create/summary)
3. `docker compose exec app php artisan route:list --path=ui/ward-setup`
   - shows UI route exists

## Run + Verify Now (Up to Issue 9)

Use from project root:  
`F:\31 projects\db project\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start stack
1. `Copy-Item .env.docker .env -Force`
2. `docker compose up -d --build`
3. `docker compose ps`

### 2) Migrate and confirm routes
1. `docker compose exec app php artisan migrate --force`
2. `docker compose exec app php artisan route:list --path=api/ward`
3. `docker compose exec app php artisan route:list --path=ui/ward-setup`
4. `docker compose exec app php artisan jwt:secret --force`
5. `docker compose exec app php artisan config:clear`

Expected ward APIs:
- `GET api/ward/departments`
- `GET api/ward/care-units`
- `POST api/ward/care-units`
- `GET api/ward/beds`
- `POST api/ward/beds`
- `GET api/ward/beds/summary`

If you see `Secret is not set.` (JWTException), run step 4 and 5 above, then login again.

### 3) Browser verify
1. `http://localhost:8000/ui`
2. `http://localhost:8000/ui/auth`
3. `http://localhost:8000/ui/ward-setup`

### 4) Quick test data flow
1. On `/ui/auth`:
   - Create admin and keep `ADMIN_TOKEN` in localStorage.
2. On `/ui/ward-setup`:
   - Click `Use ADMIN_TOKEN`.
   - Create one care unit (example: `departmentId=1`, `unitType=ICU`, `unitName=Main ICU`, `floor=2`).
   - Create one bed (example: `careUnitId=<created>`, `bedCode=ICU-01`, `status=Available`).
   - Click `GET /ward/beds` and `GET /ward/beds/summary`.

Expected:
- Care unit create returns `201` and `care_unit.id`
- Bed create returns `201` and `bed.id`
- Bed list shows unit + department info
- Summary returns grouped totals by `department`, `unit_type`, `status`
