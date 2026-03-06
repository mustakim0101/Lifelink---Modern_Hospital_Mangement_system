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
| **5** | **Build role middleware & account controls** | `feat(rbac): role middleware with freeze/unfreeze` | `dev` | Backend Developer |

## 📝 Phase 3: Hiring Flow (3 Issues/Commits)

| # | Issue Title | Commit Message | Branch | Assigned To |
|:---:|-------------|----------------|:---:|:---:|
| **6** | **Department & application tables** | `feat(hiring): migrations for departments and applications` | `dev` | Database Architect |
| **7** | **Job application submission feature** | `feat(hiring): applicant submission with status tracking` | `dev` | Full Stack Developer |
| **8** | **Admin approval workflow** | `feat(hiring): admin/it approval with auto-role assignment` | `dev` | Backend Developer |

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
