# LifeLink - Modern Hospital Management System

LifeLink is a role-based hospital operations platform built with Laravel and Microsoft SQL Server.  
It combines staff onboarding, clinical operations, admissions/bed flow, patient self-service, and blood-bank workflows in one system.

## Project Overview

LifeLink supports real hospital-style flows for:
- identity, authentication, and role-based access
- applicant approval and staff setup
- doctor/nurse/IT daily operations
- patient portal actions
- donor and blood request lifecycle
- public department browsing and hospital information pages

The project is organized so each user role lands in a dedicated workspace, while core data remains unified in one database.

## Current Build Stack

- Backend: Laravel 10 (PHP 8.1+)
- UI: Blade-based multi-page UI (`resources/views/ui`)
- Auth: JWT (`tymon/jwt-auth`) with role + active-account middleware
- Database: Microsoft SQL Server 2022
- Runtime: Docker Compose
- Web serving: Nginx + PHP-FPM containers
- Data access style:
  - Eloquent/controllers for many flows
  - targeted SQL service layer in `lifelink-app/app/Services/Sql` for heavier workflow logic

## Main Roles

- Admin
- IT Worker
- Doctor
- Nurse
- Patient
- Donor
- Applicant

## Main Modules and Workflows

### 1. Authentication and Role Control
- register/login via API JWT flow
- role-aware workspace routing
- account freeze/unfreeze and status control

### 2. Applicant Review and Staff Setup
- applicant submission and status tracking
- admin/IT review queue (approve/reject)
- post-approval staff setup for doctor/nurse/IT department assignment

### 3. IT Operations (Ward, Admission, Bed)
- department-scoped IT operations
- care unit and bed setup
- admission creation support and bed assignment/discharge flow

### 4. Doctor Workflow
- profile + department-scoped patient access
- date-based appointment monitoring and rule-driven consultation setup
- bed/admission request creation

### 5. Nurse Workflow
- department patient monitoring
- admission detail + vitals logging
- Blood Bank nurse screening tools gated by department assignment

### 6. Patient Portal
- profile and medical-record views
- appointment booking and history
- blood request creation and tracking

### 7. Donor Workflow
- donor enrollment/profile
- availability updates
- notification response and donation history

### 8. Blood Bank Operations
- blood request board and matching
- donor suggestions/notifications
- donation logging and request fulfillment
- inventory and blood-bank setup tooling

### 9. Public Experience
- welcome pages and role entry
- department directory + department detail pages
- public doctor review and availability read paths

## UI and Design State (Current)

- The project uses Blade UI, not a separate SPA frontend.
- The UI has evolved significantly from earlier prototype/debug-first pages.
- A shared shell/layout direction is active (`resources/views/ui/layouts/app.blade.php`) with:
  - public mode and authenticated dashboard mode
  - role-aware sidebar/panel navigation
  - shared profile/editor and section patterns across dashboards
- Some docs/dev notes still include older prototype-era planning text; treat this README + current code as the current state.

## Architecture Notes

- Main Laravel app: `lifelink-app/`
- UI pages: `lifelink-app/resources/views/ui/`
- API routes: `lifelink-app/routes/api.php`
- UI routes: `lifelink-app/routes/web.php`
- Controllers: `lifelink-app/app/Http/Controllers/Api/`
- SQL services: `lifelink-app/app/Services/Sql/`

Current SQL services:
- `JobApplicationSqlService.php`
- `ApplicationReviewSqlService.php`
- `BloodMatchingSqlService.php`

## Database and Startup Truth

This project currently starts in a SQL-first runtime mode on fresh environments:

- Docker starts MSSQL, then `mssql-init`
- `docker/mssql/init/init-db.sh` applies:
  - `docker/mssql/init/01-init.sql`
  - all files in `docker/mssql/init/schema/*.sql`
  - all files in `docker/mssql/init/seed/*.sql`

Operational source of truth for startup schema/data:
- `docker/mssql/init/schema`
- `docker/mssql/init/seed`

Laravel migrations still exist in the repo for history/reference, but they are not the primary first-run setup path in current Docker flow.

## How to Run

For project setup/run instructions, read: `dev_log/steps to run project.txt`

## Documentation Map

- `docs/PROJECT_INFO.md` - project summary, scope, and stack notes
- `docs/FEATURE_WORKFLOWS.md` - role/module workflow narratives and service-layer notes
- `docs/END_to_END_test_plan.md` - manual end-to-end execution plans and validation paths
- `docs/DESIGN_IDEAS.md` - UI direction and design-system planning history
- `docs/EXTRAS.md` - optional enhancements, reporting, and integration ideas
- `docs/FUTURE_NOTES.md` - backlog, reliability, and long-term planning notes
- `dev_log/README.md` - detailed implementation history and chronological change log

## Contributors

| Name | Email | GitHub |
|------|-------|--------|
| Mustakim Musa | mustakim.official.0101@gmail.com | [mustakim0101](https://github.com/mustakim0101) |
| Ahbab Hasan | hasan100.official@gmail.com | [tigertech119](https://github.com/tigertech119) |
| Shadman Muhtasim | nksoag2006@gmail.com | [ShadmanMuhtasim](https://github.com/ShadmanMuhtasim) |
