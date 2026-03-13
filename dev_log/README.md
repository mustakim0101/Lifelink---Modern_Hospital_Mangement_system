
# Dev Notes (Q&A)

## 🚀 Phase 1: Infrastructure (2 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To -->>DONE |
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
| **9** | **Bed/ICU/Ward schema** | `feat(beds): migrations for care_units and beds` | `dev` | Ahbab |
| **10** | **IT worker bed assignment** | `feat(beds): it-worker dashboard for bed allocation` | `dev` | Ahbab |
| **11** | **Discharge & bed release** | `feat(beds): auto-release bed on patient discharge` | `dev` | Ahbab |

## 👨‍⚕️ Phase 5: Clinical Operations (4 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **12** | **Clinical data schema** | `feat(clinical): migrations for patients, appointments, records` | `dev` | Ahbab |
| **13** | **Doctor dashboard & actions** | `feat(clinical): doctor management of patients and bed requests` | `dev` | Ahbab |
| **14** | **Nurse care dashboard** | `feat(clinical): nurse view for dept-wise patient monitoring` | `dev` | Shadman |
| **15** | **Patient portal** | `feat(clinical): patient portal for records and blood requests` | `dev` | Full Shadman |

## 🩸 Phase 6: Blood Bank (3 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **16** | **Blood bank schema** | `feat(blood): migrations for donors, inventory, requests` | `dev` | Shadman |
| **17** | **Donor dashboard & tracking** | `feat(blood): donor availability, weight, temp, bag logging` | `dev` | Shadman |
| **18** | **Blood matching system** | `feat(blood): it-worker matching with donor notifications` | `dev` | Shadman |

## ✅ Phase 7: Final Polish (3 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **19** | **Comprehensive testing** | `test: feature tests for all role workflows` | `dev` | QA Engineer |
| **20** | **API documentation** | `docs: swagger/openapi documentation for all endpoints` | `dev` | Technical Writer |
| **21** | **Deployment preparation** | `chore: deployment config and environment setup` | `dev` | DevOps Engineer |


##
##
##

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

---

## Phase 4 - Issue 10 (Implemented)

Issue: IT worker bed assignment
Commit message target: `feat(beds): it-worker dashboard for bed allocation`
Branch target: `dev`

### New files created (Issue 10)
- `lifelink-app/database/migrations/2026_03_07_000320_create_department_admins_table.php`
- `lifelink-app/database/migrations/2026_03_07_000330_create_admissions_table.php`
- `lifelink-app/database/migrations/2026_03_07_000340_create_bed_assignments_table.php`
- `lifelink-app/app/Models/DepartmentAdmin.php`
- `lifelink-app/app/Models/Admission.php`
- `lifelink-app/app/Models/BedAssignment.php`
- `lifelink-app/app/Http/Controllers/Api/ItBedAllocationController.php`
- `lifelink-app/resources/views/ui/it-bed-allocation.blade.php`

### Existing files updated (Issue 10)
- `lifelink-app/app/Models/User.php`
- `lifelink-app/app/Models/Department.php`
- `lifelink-app/app/Models/Bed.php`
- `lifelink-app/routes/api.php`
- `lifelink-app/routes/web.php`
- `lifelink-app/resources/views/ui/index.blade.php`
- `dev_log/README.md`

### Schema implemented
1. `department_admins`
- maps IT workers to departments (`user_id`, `department_id`)
2. `admissions`
- stores admitted patient records for bed allocation flow
3. `bed_assignments`
- stores bed assignment history with active assignment tracked by `released_at = null`

### Backend APIs added (Issue 10)
Admin/ITWorker:
- `GET /api/ward/it/departments`
- `GET /api/ward/it/admissions`
- `POST /api/ward/it/admissions`
- `GET /api/ward/it/available-beds?departmentId=...&unitType=...`
- `POST /api/ward/it/assign-bed`

Admin only:
- `POST /api/ward/it/department-admins`

### UI added
- `GET /ui/it-bed-allocation`
- assign IT worker to department (admin flow)
- create admission
- list admissions
- list available beds
- assign bed to admission

### Allocation flow (Issue 10)
IT/Admin token
-> create admission (`/ward/it/admissions`)
-> fetch available beds in department (`/ward/it/available-beds`)
-> assign bed (`/ward/it/assign-bed`)
-> bed status changes `Available -> Occupied`
-> admission gets active assignment payload

### Live verification evidence
Verified by direct API run:
- created admin
- registered patient
- created available NICU bed
- created admission
- assigned bed
- received successful assignment payload with `bed_code` and `unit_type`

---

## Run + Verify Now (Up to Issue 10)

Use from project root:
`F:\31 projects\db project\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start and prepare
1. `Copy-Item .env.docker .env -Force`
2. `docker compose up -d --build`
3. `docker compose exec app php artisan jwt:secret --force`
4. `docker compose exec app php artisan config:clear`
5. `docker compose exec app php artisan migrate --force`

### 2) Confirm new routes
1. `docker compose exec app php artisan route:list --path=api/ward/it`
2. `docker compose exec app php artisan route:list --path=ui/it-bed-allocation`

Expected API routes:
- `GET api/ward/it/departments`
- `GET api/ward/it/admissions`
- `POST api/ward/it/admissions`
- `GET api/ward/it/available-beds`
- `POST api/ward/it/assign-bed`
- `POST api/ward/it/department-admins`

### 3) Browser flow (recommended)
1. Open `http://localhost:8000/ui/auth` and login/create admin + create patient user.
2. Open `http://localhost:8000/ui/ward-setup` and ensure at least one care unit + one available bed exists.
3. Open `http://localhost:8000/ui/it-bed-allocation`:
- use `ADMIN_TOKEN`
- create admission (`patientUserId`, `departmentId`, `careLevelRequested`, `diagnosis`)
- load available beds for the department
- assign bed using `admissionId` + `bedId`

### 4) Expected success results
- Admission create -> `201` with `admission.id`
- Assign bed -> `200` with message `Bed assigned`
- Response includes:
- `admission.active_bed_assignment.bed_code`
- `admission.active_bed_assignment.unit_type`
- `GET /api/ward/beds?status=Available` count decreases for that unit/department.

---

## Phase 4 - Issue 11 (Implemented)

Issue: Discharge & bed release
Commit message target: `feat(beds): auto-release bed on patient discharge`
Branch target: `dev`

### New files created (Issue 11)
- No new files required.

### Existing files updated (Issue 11)
- `lifelink-app/app/Http/Controllers/Api/ItBedAllocationController.php`
- `lifelink-app/routes/api.php`
- `lifelink-app/resources/views/ui/it-bed-allocation.blade.php`
- `dev_log/README.md`

### Backend behavior implemented
New API endpoint:
- `POST /api/ward/it/admissions/{admission}/discharge`

When discharge is called:
1. Validates department access (Admin or scoped ITWorker)
2. Checks admission is currently `Admitted`
3. Finds active bed assignment (`released_at IS NULL`)
4. Auto-releases assignment:
- sets `released_at`
- sets `released_by_user_id`
- sets `release_reason` (default `Discharge`)
5. Sets bed status back to `Available`
6. Sets admission status to `Discharged` with `discharge_date`

### UI behavior implemented
Updated `/ui/it-bed-allocation`:
- Added discharge action form:
- admission id
- optional release reason
- `Discharge + Auto Release Bed` button

### Live verification evidence
Verified by API:
- `POST /api/ward/it/admissions/3/discharge` returned:
- `message: Admission discharged and bed released`
- `admission.status: Discharged`
- `admission.active_bed_assignment: null`
- `GET /api/ward/beds/summary` after discharge showed:
- Pediatrics NICU moved from `Occupied` to `Available`

---

## Run + Verify Now (Up to Issue 11)

Use from project root:
`F:\31 projects\db project\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start and prepare
1. `Copy-Item .env.docker .env -Force`
2. `docker compose up -d --build`
3. `docker compose exec app php artisan jwt:secret --force`
4. `docker compose exec app php artisan config:clear`
5. `docker compose exec app php artisan migrate --force`

### 2) Confirm routes
1. `docker compose exec app php artisan route:list --path=api/ward/it`

Expected additional route for Issue 11:
- `POST api/ward/it/admissions/{admission}/discharge`

### 3) Browser flow
1. Open `http://localhost:8000/ui/it-bed-allocation`
2. Use admin/it token
3. Ensure you already have:
- one admitted admission with active bed assignment
4. In discharge section:
- set `admission id`
- optional reason
- click `Discharge + Auto Release Bed`

Expected response:
- `message = Admission discharged and bed released`
- `admission.status = Discharged`
- `admission.active_bed_assignment = null`

### 4) Verify bed released
1. Open `http://localhost:8000/ui/ward-setup`
2. Click `GET /ward/beds/summary`

Expected:
- previously assigned bed is now counted under `status = Available`
- occupied count decreases accordingly in that department/unit

---

## Phase 5 - Issue 12 (Implemented)

Issue: Clinical data schema
Commit message target: `feat(clinical): migrations for patients, appointments, records`
Branch target: `dev`

### New files created (Issue 12)
- `lifelink-app/database/migrations/2026_03_07_000350_create_patients_table.php`
- `lifelink-app/database/migrations/2026_03_07_000360_create_appointments_table.php`
- `lifelink-app/database/migrations/2026_03_07_000370_create_medical_records_table.php`
- `lifelink-app/app/Models/Patient.php`
- `lifelink-app/app/Models/Appointment.php`
- `lifelink-app/app/Models/MedicalRecord.php`

### Existing files updated (Issue 12)
- `lifelink-app/app/Models/User.php`
- `lifelink-app/app/Models/Department.php`
- `lifelink-app/app/Models/Admission.php`
- `dev_log/README.md`

### Schema implemented
1. `patients`
- `patient_id` (PK + FK -> `users.id`)
- `blood_group`
- `emergency_contact_name`
- `emergency_contact_phone`
- `is_active`
- `timestamps`

2. `appointments`
- `id` (PK)
- `patient_id` (FK -> `patients.patient_id`)
- `department_id` (FK -> `departments.id`)
- `doctor_user_id` (nullable FK -> `users.id`)
- `appointment_datetime`
- `status` (default `Booked`)
- `cancelled_by_user_id` (nullable FK -> `users.id`)
- `cancel_reason`
- `timestamps`

3. `medical_records`
- `id` (PK)
- `patient_id` (FK -> `patients.patient_id`)
- `admission_id` (nullable FK -> `admissions.id`)
- `created_by_user_id` (FK -> `users.id`)
- `record_datetime`
- `diagnosis`
- `treatment_plan`
- `notes`
- `timestamps`

### Model wiring added
- `Patient` model with relations:
- `user`, `appointments`, `medicalRecords`
- `Appointment` model with relations:
- `patient`, `department`, `doctor`, `cancelledBy`
- `MedicalRecord` model with relations:
- `patient`, `admission`, `createdBy`
- `User` model relations:
- `patientProfile`, `doctorAppointments`, `cancelledAppointments`, `createdMedicalRecords`
- `Department` model relation:
- `appointments`
- `Admission` model relation:
- `medicalRecords`

### Verification evidence
Commands run:
1. `docker compose exec app php artisan migrate --force`
2. `docker compose exec app php artisan migrate:status`
3. `docker compose exec app php artisan tinker --execute "echo config('database.default');"`

Results:
- active DB connection: `sqlsrv`
- new migrations are applied in batch 5:
- `2026_03_07_000350_create_patients_table`
- `2026_03_07_000360_create_appointments_table`
- `2026_03_07_000370_create_medical_records_table`

---

## Run + Verify Now (Up to Issue 12)

Use from project root:
`F:\31 projects\db project\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start and prepare
1. `Copy-Item .env.docker .env -Force`
2. `docker compose up -d --build`
3. `docker compose exec app php artisan jwt:secret --force`
4. `docker compose exec app php artisan config:clear`
5. `docker compose exec app php artisan migrate --force`

### 2) Confirm clinical migrations are applied
1. `docker compose exec app php artisan migrate:status`

Expected new rows marked `Ran`:
- `2026_03_07_000350_create_patients_table`
- `2026_03_07_000360_create_appointments_table`
- `2026_03_07_000370_create_medical_records_table`

### 3) Optional DB checks through Tinker
1. `docker compose exec app php artisan tinker --execute "echo config('database.default');"`
2. `docker compose exec app php artisan tinker --execute "print_r(Schema::hasTable('patients')); print_r(Schema::hasTable('appointments')); print_r(Schema::hasTable('medical_records'));"`

Expected:
- database default: `sqlsrv`
- all three table checks return `1` (true)

---

## Phase 5 - Issue 13 (Implemented)

Issue: Doctor dashboard & actions
Commit message target: `feat(clinical): doctor management of patients and bed requests`
Branch target: `dev`

### New files created (Issue 13)
- `lifelink-app/database/migrations/2026_03_07_000380_create_doctors_table.php`
- `lifelink-app/database/migrations/2026_03_07_000390_add_admitted_by_doctor_to_admissions_table.php`
- `lifelink-app/app/Models/Doctor.php`
- `lifelink-app/app/Http/Controllers/Api/DoctorClinicalController.php`
- `lifelink-app/resources/views/ui/doctor-dashboard.blade.php`

### Existing files updated (Issue 13)
- `lifelink-app/app/Models/User.php`
- `lifelink-app/app/Models/Department.php`
- `lifelink-app/app/Models/Admission.php`
- `lifelink-app/routes/api.php`
- `lifelink-app/routes/web.php`
- `lifelink-app/resources/views/ui/index.blade.php`
- `dev_log/README.md`

### Schema updates
1. `doctors`
- `doctor_id` (PK + FK -> `users.id`)
- `department_id` (FK -> `departments.id`)
- `specialization`, `license_number`, `is_active`, timestamps
2. `admissions` alteration
- added nullable `admitted_by_doctor_id` FK -> `users.id`

### API endpoints added
Admin:
- `POST /api/admin/doctors/profile` (upsert doctor profile for a Doctor-role user)

Doctor role (`role:Doctor`):
- `GET /api/doctor/profile`
- `GET /api/doctor/patients`
- `GET /api/doctor/appointments`
- `POST /api/doctor/appointments/{appointment}/cancel`
- `POST /api/doctor/bed-requests`
- `GET /api/doctor/bed-requests`

### Doctor actions implemented
1. Doctor profile view by logged-in doctor
2. Doctor patients list based on doctor-linked admissions/appointments
3. Appointment list and cancel action (for doctor-owned appointments)
4. Bed request creation by doctor (creates admission with doctor linkage)
5. Bed requests list for current doctor

### UI added
- `GET /ui/doctor-dashboard`
- admin doctor-profile setup panel
- doctor profile/patients/appointments actions
- doctor bed request create + list actions

### Live verification evidence
Smoke test completed:
1. registered a user
2. submitted Doctor application
3. admin approved application (Doctor role granted)
4. admin upserted doctor profile (`department=Cardiology`)
5. doctor login succeeded
6. doctor bed request submitted (`message: Bed request submitted`)
7. doctor bed request list returned records
8. doctor patients endpoint returned patient count > 0

---

## Run + Verify Now (Up to Issue 13)

Use from project root:
`F:\31 projects\db project\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start and migrate
1. `Copy-Item .env.docker .env -Force`
2. `docker compose up -d --build`
3. `docker compose exec app php artisan jwt:secret --force`
4. `docker compose exec app php artisan config:clear`
5. `docker compose exec app php artisan migrate --force`

### 2) Confirm routes
1. `docker compose exec app php artisan route:list --path=api/doctor`
2. `docker compose exec app php artisan route:list --path=api/admin/doctors/profile`
3. `docker compose exec app php artisan route:list --path=ui/doctor-dashboard`

### 3) Browser verification flow
1. Open `/ui/auth`
- create/login admin
- register doctor-candidate user
2. Use existing Issue 8 flow to approve candidate as `Doctor`
- `/ui/applications` submit appliedRole=`Doctor`
- `/ui/application-reviews` approve
3. Open `/ui/doctor-dashboard`
- use `ADMIN_TOKEN`
- upsert doctor profile (doctor user id + department id)
4. Login as doctor from `/ui/auth` (sets `USER_TOKEN`)
5. Back to `/ui/doctor-dashboard` use `USER_TOKEN`
- `GET /doctor/profile`
- `GET /doctor/patients`
- create bed request
- `GET /doctor/bed-requests`

Expected:
- doctor profile returns configured department/specialization
- doctor bed request returns `201` with admission payload
- doctor bed request list includes created request
- doctor patients list includes linked patient(s)

---

## Phase 5 - Issue 14 (Implemented)

Issue: Nurse care dashboard
Commit message target: `feat(clinical): nurse view for dept-wise patient monitoring`
Branch target: `dev`

### New files created (Issue 14)
- `lifelink-app/database/migrations/2026_03_07_000400_create_nurses_table.php`
- `lifelink-app/database/migrations/2026_03_07_000410_create_nurse_vital_sign_logs_table.php`
- `lifelink-app/app/Models/Nurse.php`
- `lifelink-app/app/Models/NurseVitalSignLog.php`
- `lifelink-app/app/Http/Controllers/Api/NurseCareController.php`
- `lifelink-app/resources/views/ui/nurse-dashboard.blade.php`

### Existing files updated (Issue 14)
- `lifelink-app/app/Models/User.php`
- `lifelink-app/app/Models/Department.php`
- `lifelink-app/app/Models/Patient.php`
- `lifelink-app/app/Models/Admission.php`
- `lifelink-app/routes/api.php`
- `lifelink-app/routes/web.php`
- `lifelink-app/resources/views/ui/index.blade.php`
- `dev_log/README.md`

### Schema updates
1. `nurses`
- `nurse_id` (PK + FK -> `users.id`)
- `department_id` (FK -> `departments.id`)
- `ward_assignment_note`, `is_active`, timestamps
2. `nurse_vital_sign_logs`
- `admission_id` (FK -> `admissions.id`, cascade delete)
- `patient_id` (FK -> `patients.patient_id`)
- `nurse_id` (FK -> `nurses.nurse_id`)
- `measured_at`, `temperature_c`, `pulse_bpm`, `systolic_bp`, `diastolic_bp`, `respiration_rate`, `spo2_percent`, `note`
- MSSQL-safe FK design used to avoid multiple cascade path error.

### API endpoints added
Admin (`role:Admin`):
- `POST /api/admin/nurses/profile` (upsert nurse profile for a Nurse-role user)

Nurse (`role:Nurse`):
- `GET /api/nurse/profile`
- `GET /api/nurse/patients`
- `GET /api/nurse/admissions/{admission}`
- `GET /api/nurse/admissions/{admission}/vitals`
- `POST /api/nurse/admissions/{admission}/vitals`

### Nurse actions implemented
1. Nurse profile view by logged-in nurse.
2. Department-wise admission/patient monitoring with search and status filters.
3. Admission detail panel with bed assignment + linked medical record history.
4. Vital signs logging with validations (including BP consistency and metric presence).
5. Vital signs history retrieval per admission.

### UI added / modernized
- `GET /ui/nurse-dashboard`
- modern interactive dashboard layout (responsive cards + animated gradient surface)
- token context + admin nurse profile setup panel
- department patient monitor list with quick status badges
- selected admission detail summary
- quick vital-sign entry form and live refresh actions
- recent vitals + medical records tables

Also updated:
- `/ui` index page now includes Nurse Dashboard entry and marks progress up to Issue 14.

### Verification evidence
Executed in Docker app container:
1. `docker compose exec app php artisan route:list --path=api/admin/nurses/profile`
2. `docker compose exec app php artisan route:list --path=api/nurse`
3. `docker compose exec app php artisan route:list --path=ui/nurse-dashboard`
4. `docker compose exec app php artisan migrate --force`
5. `docker compose exec app php artisan migrate:status`
6. `docker compose exec app php artisan test`

Observed results:
- new nurse routes listed correctly
- nurse migrations applied (`2026_03_07_000400`, `2026_03_07_000410`)
- tests passed (`2 passed`)

---

## Run + Verify Now (Up to Issue 14)

Use from project root:
`J:\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start and migrate
1. `Copy-Item .env.docker .env -Force`
2. `docker compose up -d --build`
3. `docker compose exec app php artisan jwt:secret --force`
4. `docker compose exec app php artisan config:clear`
5. `docker compose exec app php artisan migrate --force`

### 2) Confirm Issue 14 routes
1. `docker compose exec app php artisan route:list --path=api/admin/nurses/profile`
2. `docker compose exec app php artisan route:list --path=api/nurse`
3. `docker compose exec app php artisan route:list --path=ui/nurse-dashboard`

### 3) Browser verification flow (Nurse)
1. Open `/ui/auth`
- register/login admin user
- register nurse-candidate user
2. Use existing Issue 8 flow to approve candidate as `Nurse`
- `/ui/applications` submit `appliedRole=Nurse`
- `/ui/application-reviews` approve
3. Open `/ui/nurse-dashboard`
- use `ADMIN_TOKEN`
- upsert nurse profile (nurse user id + department id)
4. Login as nurse from `/ui/auth` (sets `USER_TOKEN`)
5. Back to `/ui/nurse-dashboard` use `USER_TOKEN`
- `GET /nurse/profile`
- `GET /nurse/patients`
- select one admission from patient list
- submit vital sign log
- refresh vitals table

Expected:
- nurse profile returns configured department and active state
- patients endpoint returns only nurse department admissions
- admission details show bed assignment + medical records
- vital log creation returns `201` and appears in recent vitals list

---

## Phase 5 - Issue 15 (Implemented)

Issue: Patient portal
Commit message target: `feat(clinical): patient portal for records and blood requests`
Branch target: `dev`

### New files created (Issue 15)
- `lifelink-app/database/migrations/2026_03_07_000500_create_blood_requests_table.php`
- `lifelink-app/app/Models/BloodRequest.php`
- `lifelink-app/app/Http/Controllers/Api/PatientPortalController.php`
- `lifelink-app/resources/views/ui/patient-portal.blade.php`

### Existing files updated (Issue 15)
- `lifelink-app/app/Http/Controllers/Api/AuthController.php`
- `lifelink-app/app/Models/Patient.php`
- `lifelink-app/app/Models/Department.php`
- `lifelink-app/app/Models/Admission.php`
- `lifelink-app/app/Models/User.php`
- `lifelink-app/routes/api.php`
- `lifelink-app/routes/web.php`
- `lifelink-app/resources/views/ui/index.blade.php`
- `dev_log/README.md`

### Schema updates
1. `blood_requests`
- `patient_id` (FK -> `patients.patient_id`)
- `admission_id` (nullable FK -> `admissions.id`)
- `department_id` (FK -> `departments.id`)
- `requested_by_user_id` (FK -> `users.id`)
- `blood_group_needed`, `component_type`, `units_required`, `urgency`, `status`, `request_date`, `notes`
2. Added model relationships for blood requests in:
- `Patient`
- `Admission`
- `Department`
- `User`

### API endpoints added
Patient role (`role:Patient`):
- `GET /api/patient/portal`
- `GET /api/patient/profile`
- `GET /api/patient/medical-records`
- `GET /api/patient/appointments`
- `GET /api/patient/booking-options`
- `POST /api/patient/appointments`
- `POST /api/patient/appointments/{appointment}/cancel`
- `POST /api/patient/blood-requests`
- `GET /api/patient/blood-requests`

### Patient portal actions implemented
1. Patient dashboard summary with records/appointments/blood-request counts.
2. Patient profile fetch with role + emergency info.
3. Medical records list endpoint for current patient.
4. Appointment booking + listing + cancellation by patient.
5. Blood request submission + history list by patient.
6. Automatic patient profile bootstrap during register/login to avoid missing `patients` profile rows.
7. Booking options endpoint returns active departments + active doctors for patient-side dropdown UX.

### UI added / modernized
- `GET /ui/patient-portal`
- modern responsive portal UI
- token context + one-click refresh
- patient snapshot and summary stats
- appointment booking with department-linked doctor dropdown and live list/cancel actions
- blood request submission and request history
- medical records table with search filter
- API response log for debugging

Also updated:
- `/ui` index page now includes `Patient Portal` entry and progress note up to Issue 15.

### Refinement pass (2026-03-07)
Files updated in this pass:
- `lifelink-app/app/Http/Controllers/Api/PatientPortalController.php`
- `lifelink-app/app/Http/Controllers/Api/AuthController.php`
- `lifelink-app/routes/api.php`
- `lifelink-app/resources/views/ui/patient-portal.blade.php`
- `dev_log/README.md`

Refinements applied:
- Added `GET /api/patient/booking-options` for dynamic department/doctor selectors in patient UI.
- Appointment booking now validates:
- datetime must be after now
- selected doctor must be active
- selected doctor must belong to selected department
- Patient profile bootstrap was hardened to avoid force-resetting `patients.is_active` on every login.
- Patient portal UI redesigned for clearer flow:
- guided doctor dropdown (no manual doctor ID typing)
- appointment/blood request status chips
- record search
- lightweight toast feedback

### Verification evidence
Executed in Docker app container:
1. `docker compose exec app php artisan migrate --force`
2. `docker compose exec app php artisan route:list --path=api/patient`
3. `docker compose exec app php artisan route:list --path=ui/patient-portal`
4. `docker compose exec app php artisan migrate:status | Select-String -Pattern "000500_create_blood_requests_table"`
5. `docker compose exec app php artisan test`

Observed results:
- new patient routes listed correctly (`9 routes`)
- patient portal web route listed correctly
- `2026_03_07_000500_create_blood_requests_table` marked `Ran`
- tests passed (`2 passed`)

---

## Run + Verify Now (Up to Issue 15)

Use from project root:
`J:\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start and migrate
1. `Copy-Item .env.docker .env -Force`
2. `docker compose up -d --build`
3. `docker compose exec app php artisan jwt:secret --force`
4. `docker compose exec app php artisan config:clear`
5. `docker compose exec app php artisan migrate --force`

### 2) Confirm Issue 15 routes
1. `docker compose exec app php artisan route:list --path=api/patient`
2. `docker compose exec app php artisan route:list --path=ui/patient-portal`

### 3) Browser verification flow (Patient)
1. Open `/ui/auth`
- register/login patient user
2. Open `/ui/patient-portal`
- use `USER_TOKEN`
- click `Refresh All`
3. Book appointment
- choose department
- set datetime
- submit and verify in appointments list
4. Submit blood request
- set blood group + units + urgency
- submit and verify in blood request list
5. Load medical records
- verify records table loads without authorization errors

Expected:
- portal profile and stats load with `200`
- appointment booking returns `201`, then appears in list
- blood request returns `201`, then appears in request history
- patient-only routes reject non-patient tokens with `403`

---

## Phase 6 - Issue 16 (Implemented)

Issue: Blood bank schema
Commit message target: `feat(blood): migrations for donors, inventory, requests`
Branch target: `dev`

### New files created (Issue 16)
- `lifelink-app/database/migrations/2026_03_08_000600_create_blood_banks_table.php`
- `lifelink-app/database/migrations/2026_03_08_000610_create_donor_profiles_table.php`
- `lifelink-app/database/migrations/2026_03_08_000620_create_blood_inventory_table.php`
- `lifelink-app/database/migrations/2026_03_08_000630_add_blood_bank_id_to_blood_requests_table.php`
- `lifelink-app/app/Models/BloodBank.php`
- `lifelink-app/app/Models/BloodInventory.php`
- `lifelink-app/app/Models/DonorProfile.php`
- `lifelink-app/app/Http/Controllers/Api/BloodBankSchemaController.php`
- `lifelink-app/resources/views/ui/blood-bank-schema.blade.php`

### Existing files updated (Issue 16)
- `lifelink-app/app/Models/User.php`
- `lifelink-app/app/Models/BloodRequest.php`
- `lifelink-app/routes/api.php`
- `lifelink-app/routes/web.php`
- `lifelink-app/resources/views/ui/index.blade.php`
- `dev_log/README.md`

### Schema updates
1. `blood_banks`
- `bank_name` (unique), `location`, `is_active`, timestamps
2. `donor_profiles`
- `donor_id` (PK + FK -> `users.id`)
- `blood_group`, `last_donation_date`, `is_eligible`, `notes`, timestamps
3. `blood_inventory`
- `blood_bank_id` (FK -> `blood_banks.id`)
- `blood_group`, `component_type`, `units_available`, `last_updated_at`, timestamps
- unique key on (`blood_bank_id`, `blood_group`, `component_type`)
4. `blood_requests` extended
- added nullable `blood_bank_id` (FK -> `blood_banks.id`) with null-on-delete

### API endpoints added
Admin/IT role (`role:Admin,ITWorker`):
- `GET /api/blood/schema/overview`
- `GET /api/blood/schema/banks`
- `POST /api/blood/schema/banks`
- `GET /api/blood/schema/donor-profiles`
- `POST /api/blood/schema/donor-profiles`
- `GET /api/blood/schema/inventory`
- `POST /api/blood/schema/inventory`
- `GET /api/blood/schema/requests`

### Blood schema actions implemented
1. Blood bank creation and listing.
2. Donor profile upsert/list with donor-role auto-attach.
3. Inventory upsert/list per bank + blood group + component.
4. Blood request listing with optional bank/status filters.
5. Overview endpoint with schema-level counts and request status summary.

### UI added / modernized
- `GET /ui/blood-bank-schema`
- modern responsive schema dashboard for admin/IT
- token context (`ADMIN_TOKEN` / `USER_TOKEN`) and one-click refresh
- quick forms for bank creation, donor profile upsert, inventory upsert
- live tables for banks, donors, inventory, and blood requests
- request status chips and API response log

Also updated:
- `/ui` index page now includes `Blood Bank Schema` entry and progress note up to Issue 16.

### Verification evidence
Executed in Docker app container:
1. `docker compose exec app php artisan migrate --force`
2. `docker compose exec app php artisan route:list --path=api/blood/schema`
3. `docker compose exec app php artisan route:list --path=ui/blood-bank-schema`
4. `docker compose exec app php artisan migrate:status`
5. `docker compose exec app php artisan test`

Observed results:
- blood schema routes listed correctly
- blood schema migrations marked `Ran`
- tests passed

---

## Run + Verify Now (Up to Issue 16)

Use from project root:
`J:\Lifelink---Modern_Hospital_Mangement_system`

### 1) Start and migrate
1. `Copy-Item .env.docker .env -Force`
2. `docker compose up -d --build`
3. `docker compose exec app php artisan jwt:secret --force`
4. `docker compose exec app php artisan config:clear`
5. `docker compose exec app php artisan migrate --force`

### 2) Confirm Issue 16 routes
1. `docker compose exec app php artisan route:list --path=api/blood/schema`
2. `docker compose exec app php artisan route:list --path=ui/blood-bank-schema`

### 3) Browser verification flow (Blood schema)
1. Open `/ui/auth`
- login admin user (or IT worker)
2. Open `/ui/blood-bank-schema`
- use `ADMIN_TOKEN` or `USER_TOKEN`
- click `Refresh All`
3. Create one blood bank and verify it appears in banks table
4. Upsert one donor profile (existing user id + blood group) and verify donor table
5. Upsert inventory row (bank + blood group + component + units) and verify inventory table
6. Verify requests table loads existing patient blood requests with `200`

Expected:
- all schema endpoints respond without authorization errors for Admin/IT tokens
- create/upsert actions return success and refresh tables
- schema snapshot counters update after successful writes

### 4) Common Postman pitfalls (Issue 16)
1. Use `POST /api/blood/schema/banks` to create a bank.
`GET /api/blood/schema/banks` only lists banks.
2. If `POST /api/blood/schema/inventory` says `The selected bank id is invalid`, create/list banks first and use a real `id` from `GET /api/blood/schema/banks`.
3. For GET endpoints (`overview`, `banks`, `donor-profiles`, `inventory`, `requests`), keep request body empty and pass filters in query params.

---

## Phase 6 - Issue 17 (Implemented)

Issue: Donor dashboard & tracking
Commit message target: `feat(blood): donor availability, weight, temp, bag logging`
Branch target: `dev`

### New files created (Issue 17)
- `lifelink-app/database/migrations/2026_03_08_000700_create_donor_availabilities_table.php`
- `lifelink-app/database/migrations/2026_03_08_000710_create_donor_health_checks_table.php`
- `lifelink-app/database/migrations/2026_03_08_000720_create_blood_donations_table.php`
- `lifelink-app/app/Models/DonorAvailability.php`
- `lifelink-app/app/Models/DonorHealthCheck.php`
- `lifelink-app/app/Models/BloodDonation.php`
- `lifelink-app/app/Http/Controllers/Api/DonorDashboardController.php`
- `lifelink-app/resources/views/ui/donor-dashboard.blade.php`

### Existing files updated (Issue 17)
- `lifelink-app/app/Models/DonorProfile.php`
- `lifelink-app/app/Models/BloodBank.php`
- `lifelink-app/app/Models/BloodRequest.php`
- `lifelink-app/app/Models/User.php`
- `lifelink-app/routes/api.php`
- `lifelink-app/routes/web.php`
- `lifelink-app/resources/views/ui/index.blade.php`
- `dev_log/README.md`

### Schema updates
1. `donor_availabilities`
- `donor_id` FK -> `donor_profiles.donor_id`
- `week_start_date`, `is_available`, `max_bags_possible`, `notes`, timestamps
- unique key on (`donor_id`, `week_start_date`)
2. `donor_health_checks`
- `donor_id` FK -> `donor_profiles.donor_id`
- `check_datetime`, `weight_kg`, `temperature_c`, `hemoglobin`, `notes`
- `checked_by_user_id` FK -> `users.id` (nullable)
3. `blood_donations`
- `donor_id` FK -> `donor_profiles.donor_id`
- `blood_bank_id` FK -> `blood_banks.id`
- `donation_datetime`, `blood_group`, `component_type`, `units_donated`
- optional links to `blood_requests` and `donor_health_checks`
- tracks `recorded_by_user_id`

### API endpoints added
Donor role (`role:Donor`):
- `GET /api/donor/dashboard`
- `GET /api/donor/profile`
- `GET /api/donor/banks`
- `GET /api/donor/availability`
- `POST /api/donor/availability`
- `GET /api/donor/health-checks`
- `POST /api/donor/health-checks`
- `GET /api/donor/donations`
- `POST /api/donor/donations`

Authenticated user bootstrap:
- `POST /api/donor/enroll` (enables Donor role for current user + creates donor profile)

### Donor tracking actions implemented
1. Donor weekly availability upsert/list with max bags.
2. Donor health check logging (weight, temperature, optional hemoglobin).
3. Donor eligibility flag update from health check vitals.
4. Donation bag logging per blood bank/component/group.
5. Inventory auto-increment on successful donation log.
6. Donor dashboard summary (stats + latest records + request pressure by blood group).
7. Self-enroll endpoint for authenticated users to enable Donor role and donor profile.

### UI added / modernized
- `GET /ui/donor-dashboard`
- modern responsive donor dashboard
- availability form, health check form, donation logging form
- live stats + history tables
- API response panel and token shortcuts

Also updated:
- `/ui` index page now includes `Donor Dashboard` entry and progress note up to Issue 17.

### Verification flow (Issue 17)
1. Ensure a user has `Donor` role (use Issue 16 donor profile upsert if needed).
2. If role is missing, call `POST /api/donor/enroll` using that user's token.
3. Login as donor and copy token.
4. `GET /api/donor/dashboard` returns donor summary.
5. `POST /api/donor/availability` logs weekly availability.
6. `POST /api/donor/health-checks` logs weight/temp.
7. `POST /api/donor/donations` logs bag units and updates inventory.
8. `GET /api/donor/donations` shows donation history with latest record.

---

## Pre-Issue 18 Verification + Fixes (2026-03-08)

### Scope reviewed
- Re-checked implemented work for Issues **9-17**:
- bed/ward schema and allocation/discharge
- doctor/nurse/patient clinical APIs
- blood bank schema and donor tracking APIs

### Runtime verification executed
1. `docker compose up -d --build`
2. `docker compose exec app php artisan migrate --force`
3. `docker compose exec app php artisan route:list --path=api`
4. `docker compose exec app php artisan test`

Result:
- API routes loaded successfully (68 API routes found, including ward/doctor/nurse/patient/donor/blood schema groups).
- Migrations succeeded for all Issue 9-17 tables.
- Current test suite passed (`2 passed`).

### Bug found and fixed
- **Problem:** `mssql-init` failed at startup with:
- `/init/init-db.sh: line 2: set: pipefail: invalid option name`
- **Root cause:** `docker/mssql/init/init-db.sh` had Windows line endings (CRLF), which broke `set -euo pipefail` in Linux container.
- **Fix applied:** normalized script content/line endings in:
- `docker/mssql/init/init-db.sh`
- **Verification after fix:**
- Recreated init service container.
- `lifelink_mssql_init` now exits cleanly with exit code `0`.

- **Problem:** intermittent `502 Bad Gateway` from Nginx after app container recreation.
- **Root cause:** Nginx upstream name was not configured for Docker DNS re-resolution.
- **Fix applied:** updated:
- `docker/nginx/default.conf`
- added Docker DNS resolver (`127.0.0.11`)
- changed `fastcgi_pass` to variable-based upstream (`$php_upstream`)
- **Verification after fix:**
- restarted `web` container
- `GET /` and `POST /api/auth/login` both return `200`

---

## SQL-First Transition (Pre-Issue 18)

### Objective
- Make raw SQL files the primary table-creation path (instead of `php artisan migrate`).
- Start converting Eloquent-heavy data-manipulation paths to raw SQL service style.

### New SQL-first structure
- `docker/mssql/init/01-init.sql` (database bootstrap)
- `docker/mssql/init/schema/*.sql` (separate table schema files)
- `docker/mssql/init/seed/*.sql` (reference/dummy data)

### SQL init runner update
- Updated `docker/mssql/init/init-db.sh` to:
1. wait for SQL Server readiness
2. run `01-init.sql` on `master`
3. run all `schema/*.sql` on `lifelink` in filename order
4. run all `seed/*.sql` on `lifelink` in filename order

### Raw SQL service layer added
- New folder: `lifelink-app/app/Services/Sql/`
- Added:
- `JobApplicationSqlService.php`
- `ApplicationReviewSqlService.php`

### Controller integration (raw SQL path)
- Updated:
- `lifelink-app/app/Http/Controllers/Api/JobApplicationController.php`
- `lifelink-app/app/Http/Controllers/Api/Admin/ApplicationReviewController.php`
- These now use SQL service classes for core fetch/manipulate flows.

### Verification (clean start, no migrate command)
1. `docker compose down -v`
2. `docker compose up -d --build`
3. SQL init logs show schema + seed execution complete.
4. DB table count check:
- `SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE='BASE TABLE'`
- Result: `28`
5. API smoke checks passed:
- `POST /api/dev/create-admin`
- `POST /api/applications`
- `GET /api/admin/applications?status=Pending`
- `POST /api/admin/applications/{id}/approve`
6. `php artisan test` passed (`2 passed`).

---

## Phase 6 - Issue 18 (Implemented)

Issue: Blood matching system
Commit message target: `feat(blood): it-worker matching with donor notifications`
Branch target: `dev`

### SQL-first schema files created (no PHP migration)
- `docker/mssql/init/schema/67_blood_request_matches.sql`
- `docker/mssql/init/schema/68_donor_notifications.sql`

### New backend files created
- `lifelink-app/app/Services/Sql/BloodMatchingSqlService.php`
- `lifelink-app/app/Http/Controllers/Api/BloodMatchingController.php`
- `lifelink-app/app/Http/Controllers/Api/DonorNotificationController.php`

### Existing backend files updated
- `lifelink-app/routes/api.php`
- `lifelink-app/app/Http/Controllers/Api/PatientPortalController.php`

### New UI file created
- `lifelink-app/resources/views/ui/blood-matching.blade.php`

### Existing UI/routes/docs files updated
- `lifelink-app/routes/web.php`
- `lifelink-app/resources/views/ui/index.blade.php`
- `dev_log/README.md`

### Feature summary (Issue 18)
1. Added `blood_request_matches` table to store donor matching score, compatibility label, selection actor, notification/response timestamps, and match status lifecycle (`Suggested/Notified/Accepted/Declined/Completed`).
2. Added `donor_notifications` table to store donor-facing notifications per blood request/match with read and response tracking.
3. Added SQL service (`BloodMatchingSqlService`) for raw-SQL-only matching operations:
- IT/Admin blood request list with inventory + match counters
- compatibility-based donor suggestion query (blood group + eligibility + weekly availability)
- donor notification dispatch and match upsert
- match timeline retrieval
- donor inbox, read, and accept/decline response handling
- accepted donor lookup map for patient request view
4. Added IT/Admin matching APIs:
- `GET /api/blood/matching/requests`
- `GET /api/blood/matching/requests/{bloodRequest}/suggestions`
- `POST /api/blood/matching/requests/{bloodRequest}/notify`
- `GET /api/blood/matching/requests/{bloodRequest}/matches`
5. Added donor notification APIs:
- `GET /api/donor/notifications`
- `POST /api/donor/notifications/{notification}/read`
- `POST /api/donor/notifications/{notification}/respond`
6. Updated patient blood request response (`GET /api/patient/blood-requests`) to include `accepted_donors` so patient can see confirmed donor info after donor acceptance.
7. Added modern interactive IT-worker UI page:
- `GET /ui/blood-matching`
- request filtering, donor suggestion cards, selective/auto notify actions, match timeline table, live API response panel.

### Verification performed
- Syntax checks passed:
- `php -l lifelink-app/app/Services/Sql/BloodMatchingSqlService.php`
- `php -l lifelink-app/app/Http/Controllers/Api/BloodMatchingController.php`
- `php -l lifelink-app/app/Http/Controllers/Api/DonorNotificationController.php`
- `php -l lifelink-app/app/Http/Controllers/Api/PatientPortalController.php`
- `php -l lifelink-app/routes/api.php`
- `php -l lifelink-app/routes/web.php`
- `php artisan route:list` could not be executed in this local shell because local PHP is `8.2.12` while project dependencies require `>=8.3.0` (run inside project Docker/container PHP to verify route registration).



##
##
##
---

## UI Update Log - Public Mode Landing Page

### Scope completed
- Implemented the first part of the UI redesign plan: `Public Mode`
- Replaced the default Laravel welcome screen with a real public landing page for non-logged-in visitors

### Files changed for this UI step
- Updated: `lifelink-app/resources/views/welcome.blade.php`

### What this file now holds
#### `lifelink-app/resources/views/welcome.blade.php`
This file is now the main public landing page of the project. It is the page shown when someone visits `/` without going directly into a role-specific UI page.

It now contains:
- top navigation bar
- hero section
- short product summary
- major services overview
- departments snapshot
- blood donation call-to-action
- footer with public links into the prototype UI

### Current UI file responsibility map
- `lifelink-app/resources/views/welcome.blade.php`
- public home / landing page for non-logged-in visitors
- `lifelink-app/resources/views/ui/index.blade.php`
- internal UI directory / prototype page hub
- `lifelink-app/resources/views/ui/auth.blade.php`
- authentication prototype page
- `lifelink-app/resources/views/ui/applications.blade.php`
- job application prototype page
- `lifelink-app/resources/views/ui/admin-users.blade.php`
- admin account control prototype page
- `lifelink-app/resources/views/ui/application-reviews.blade.php`
- admin and IT application review prototype page
- `lifelink-app/resources/views/ui/ward-setup.blade.php`
- ward and care-unit setup prototype page
- `lifelink-app/resources/views/ui/it-bed-allocation.blade.php`
- bed allocation prototype page for IT/admin operations
- `lifelink-app/resources/views/ui/doctor-dashboard.blade.php`
- doctor workflow prototype page
- `lifelink-app/resources/views/ui/nurse-dashboard.blade.php`
- nurse monitoring prototype page
- `lifelink-app/resources/views/ui/patient-portal.blade.php`
- patient portal prototype page
- `lifelink-app/resources/views/ui/blood-bank-schema.blade.php`
- blood bank schema and inventory prototype page
- `lifelink-app/resources/views/ui/donor-dashboard.blade.php`
- donor workflow prototype page
- `lifelink-app/resources/views/ui/blood-matching.blade.php`
- blood matching and donor notification prototype page

### Notes for next UI step
- next major target should be the auth page redesign
- after that, add a shared authenticated layout so role dashboards stop feeling like separate isolated pages



---

## UI Update Log - Authenticated Mode Foundation

### Scope completed
- Started the `Authenticated Mode` UI work
- Redesigned the authentication page into a cleaner public-to-auth entry screen
- Added a role-aware authenticated dashboard page
- Added a separate advanced tools page for Admin and IT Worker diagnostics

### Files changed for this UI step
- Updated: `lifelink-app/resources/views/ui/auth.blade.php`
- Created: `lifelink-app/resources/views/ui/dashboard.blade.php`
- Created: `lifelink-app/resources/views/ui/dev-tools.blade.php`
- Updated: `lifelink-app/routes/web.php`

### What each file now holds
#### `lifelink-app/resources/views/ui/auth.blade.php`
- cleaned login/register experience
- session summary card
- hidden advanced bootstrap area for creating the first admin
- redirects into authenticated dashboard after successful login/register

#### `lifelink-app/resources/views/ui/dashboard.blade.php`
- main authenticated landing page after login
- reads current user roles from stored session data
- shows role-aware primary destination
- shows role-specific navigation cards
- exposes advanced tools entry only for Admin and IT Worker sessions

#### `lifelink-app/resources/views/ui/dev-tools.blade.php`
- controlled technical verification page
- intended for Admin and IT Worker use
- shows stored session context
- supports `GET /api/auth/me` verification
- keeps raw technical inspection separate from the normal user flow

#### `lifelink-app/routes/web.php`
- now includes:
  - `/ui/dashboard`
  - `/ui/dev-tools`

### Updated UI file responsibility map
- `lifelink-app/resources/views/welcome.blade.php`
  - public landing page for non-logged-in visitors
- `lifelink-app/resources/views/ui/auth.blade.php`
  - authentication entry page
- `lifelink-app/resources/views/ui/dashboard.blade.php`
  - authenticated home/dashboard page
- `lifelink-app/resources/views/ui/dev-tools.blade.php`
  - advanced admin/IT diagnostic page
- `lifelink-app/resources/views/ui/index.blade.php`
  - internal prototype page directory
- `lifelink-app/resources/views/ui/applications.blade.php`
  - job application page
- `lifelink-app/resources/views/ui/admin-users.blade.php`
  - admin account control page
- `lifelink-app/resources/views/ui/application-reviews.blade.php`
  - application review page
- `lifelink-app/resources/views/ui/ward-setup.blade.php`
  - ward and care-unit setup page
- `lifelink-app/resources/views/ui/it-bed-allocation.blade.php`
  - IT/admin bed allocation page
- `lifelink-app/resources/views/ui/doctor-dashboard.blade.php`
  - doctor workflow page
- `lifelink-app/resources/views/ui/nurse-dashboard.blade.php`
  - nurse workflow page
- `lifelink-app/resources/views/ui/patient-portal.blade.php`
  - patient workflow page
- `lifelink-app/resources/views/ui/blood-bank-schema.blade.php`
  - blood bank schema and inventory page
- `lifelink-app/resources/views/ui/donor-dashboard.blade.php`
  - donor workflow page
- `lifelink-app/resources/views/ui/blood-matching.blade.php`
  - blood request matching and donor notification page

### Notes for next UI step
- next likely target is a shared authenticated layout pattern across existing role pages
- after that, existing dashboards/pages should be refit into the authenticated navigation system
---

## UI Correction Log - Auth Flow and Session Behavior

### Problems reported
1. Public registration effectively behaved like patient-only registration in the visible UI.
2. Registration immediately redirected users into the dashboard because the returned token was being stored and reused as an active login session.
3. Logged-in users could return to the public landing page and still see the same public login/register prompts, which made the flow confusing for session-aware use.
4. Sensitive values, especially passwords, were being stored in browser local storage in the prototype auth flow.

### How the problems were solved

#### Problem 1 - registration choices were too narrow
Updated the auth experience so it now has:
- one shared login card for all users
- separate registration cards for:
  - patient
  - blood donor
  - job applicant

Implementation behavior:
- patient registration -> creates normal user account
- donor registration -> creates account, then initializes donor profile through donor enroll API
- applicant registration -> creates account, then submits initial job application through application API

So login remains unified, but registration is now split by purpose.

#### Problem 2 - registration auto-logged the user in
Changed the registration flow so registration no longer becomes an active session by default.

New behavior:
- user account is created
- donor/applicant extra setup is completed if relevant
- temporary auth state is cleared
- last used email is remembered
- user is asked to log in explicitly
- only successful login sends user to `/ui/dashboard`

This makes the flow cleaner and more realistic:
register first -> login next -> dashboard after successful login

#### Problem 3 - landing page ignored active session state
Updated the public landing page so it now checks whether a local authenticated session exists.

New behavior:
- if not logged in:
  - landing page shows public login/register entry options
- if logged in:
  - landing page switches to a session-aware state
  - login/register prompts are replaced with:
    - go to dashboard
    - logout

This prevents the confusing situation where a logged-in user sees a public registration-first experience as if no session exists.

#### Problem 4 - password stored in local storage
This was the most important security-related prototype issue.

Old behavior:
- password values such as patient/admin password were being saved in browser local storage for convenience

Why it was bad:
- browser local storage is visible through inspect tools
- any saved password there is exposed to the local browser environment
- for a cleaner and safer UI flow, passwords should never be intentionally persisted this way

New behavior:
- passwords are no longer stored in local storage
- only non-sensitive convenience data such as last used email may be remembered
- browser storage now keeps:
  - token
  - user id
  - user email
  - roles
- password keys were removed from the new auth flow logic

#### Old code snippet that caused the issue
This is the old auth logic from `lifelink-app/resources/views/ui/auth.blade.php` that persisted passwords into browser local storage:

```js
function persistLoginContext(responseData, submittedEmail, submittedPassword) {
    const user = responseData?.user || {};
    const roles = Array.isArray(user.roles) ? user.roles : [];

    localStorage.setItem('USER_TOKEN', responseData.token || '');
    localStorage.setItem('CURRENT_USER_ID', String(user.id || ''));
    localStorage.setItem('CURRENT_USER_EMAIL', user.email || submittedEmail || '');
    localStorage.setItem('CURRENT_USER_ROLES', JSON.stringify(roles));

    if (roles.includes('Admin')) {
        localStorage.setItem('ADMIN_TOKEN', responseData.token || '');
        localStorage.setItem('ADMIN_USER_ID', String(user.id || ''));
        localStorage.setItem('ADMIN_EMAIL', user.email || submittedEmail || '');
        localStorage.setItem('ADMIN_PASSWORD', submittedPassword || '');
    }
}

async function registerUser() {
    const payload = {
        email: document.getElementById('regEmail').value.trim(),
        password: document.getElementById('regPassword').value.trim(),
        fullName: document.getElementById('regName').value.trim()
    };
    const r = await call('/auth/register', 'POST', payload);
    if (r.data && r.data.token) {
        localStorage.setItem('USER_TOKEN', r.data.token);
        localStorage.setItem('PATIENT_ID', String(r.data.user?.id || ''));
        localStorage.setItem('PATIENT_EMAIL', payload.email);
        localStorage.setItem('PATIENT_PASSWORD', payload.password);
        localStorage.setItem('CURRENT_USER_ID', String(r.data.user?.id || ''));
        localStorage.setItem('CURRENT_USER_EMAIL', payload.email);
        localStorage.setItem('CURRENT_USER_ROLES', JSON.stringify(r.data.user?.roles || []));
    }
}

function clearStorage() {
    [
        'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL', 'ADMIN_PASSWORD',
        'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL', 'PATIENT_PASSWORD',
        'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES'
    ].forEach(k => localStorage.removeItem(k));
}
```

Why this was the problem:
- `ADMIN_PASSWORD` was being saved after admin creation/login
- `PATIENT_PASSWORD` was being saved after patient registration
- `submittedPassword` was flowing directly into browser storage
- anyone with local browser access could inspect those values

#### New code snippet after the fix
This is the new auth logic that removed password persistence while still keeping minimal session metadata:

```js
function persistLoginContext(responseData, submittedEmail) {
    const user = responseData?.user || {};
    const roles = Array.isArray(user.roles) ? user.roles : [];

    localStorage.setItem('USER_TOKEN', responseData.token || '');
    localStorage.setItem('CURRENT_USER_ID', String(user.id || ''));
    localStorage.setItem('CURRENT_USER_EMAIL', user.email || submittedEmail || '');
    localStorage.setItem('CURRENT_USER_ROLES', JSON.stringify(roles));

    if (roles.includes('Admin')) {
        localStorage.setItem('ADMIN_TOKEN', responseData.token || '');
        localStorage.setItem('ADMIN_USER_ID', String(user.id || ''));
        localStorage.setItem('ADMIN_EMAIL', user.email || submittedEmail || '');
    }
}

function rememberLastEmail(email) {
    if (email) {
        localStorage.setItem('LAST_USED_EMAIL', email);
    }
}

async function registerPatient() {
    clearMessage();
    const name = document.getElementById('patientName').value.trim();
    const email = document.getElementById('patientEmail').value.trim();
    const password = document.getElementById('patientPassword').value.trim();

    try {
        await registerBase(name, email, password);
        clearTransientSession();
        fillLoginEmail(email);
        showMessage('success', 'Patient account created. Please log in with your new account to continue.');
    } catch (error) {
        showMessage('error', error.message);
    }
}

function clearStorage() {
    [
        'ADMIN_TOKEN', 'ADMIN_USER_ID', 'ADMIN_EMAIL',
        'USER_TOKEN', 'PATIENT_ID', 'PATIENT_EMAIL',
        'CURRENT_USER_ID', 'CURRENT_USER_EMAIL', 'CURRENT_USER_ROLES'
    ].forEach(key => localStorage.removeItem(key));
}
```

What changed in the new version:
- the `submittedPassword` parameter was removed from `persistLoginContext()`
- `ADMIN_PASSWORD` is no longer written
- `PATIENT_PASSWORD` is no longer written
- the login helper only remembers `LAST_USED_EMAIL`
- registration now clears transient auth state and asks the user to log in properly

### Files changed for these corrections
- Updated: `lifelink-app/resources/views/ui/auth.blade.php`
- Updated: `lifelink-app/resources/views/welcome.blade.php`
- Updated: `lifelink-app/resources/views/ui/dashboard.blade.php`
- Updated: `lifelink-app/resources/views/ui/dev-tools.blade.php`

### Resulting flow after correction
1. visitor opens public landing page
2. visitor chooses one of:
   - login
   - register as patient
   - register as blood donor
   - register as job applicant
3. after registration, system completes setup but does not keep the user logged in
4. user logs in through the single login card
5. system stores session token and role info
6. user is redirected to role-aware dashboard
7. if user returns to landing page while logged in, page now shows dashboard/logout state instead of public registration-first state
