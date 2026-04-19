# Project Info

## Project Summary
[Add project summary here]

## Purpose
[Explain why this project exists]

## Goals
- [Add goal 1]
- [Add goal 2]
- [Add goal 3]

## Target Users
- [Add user group 1]
- [Add user group 2]
- [Add user group 3]

## Main Modules
- [Add module name and short description]
- [Add module name and short description]
- [Add module name and short description]

## Project Scope
### In Scope
- [Add in-scope item]
- [Add in-scope item]

### Out of Scope
- [Add out-of-scope item]
- [Add out-of-scope item]

## Tech Stack
- Backend: [Add backend details]
- Frontend: [Add frontend details]
- Database: [Add database details]
- DevOps/Runtime: [Add runtime details]

## Current Status
[Add current project status here]

## Notes
[Add any extra project information here]

---

## Revision 2 - Consolidated Project Reference

This section is the cleaner working version of the project overview. The earlier content above remains as the starter template.

## Project Summary
LifeLink is a hospital management system built with Laravel, Blade, MSSQL, and Docker. The project is designed around role-based hospital workflows such as patient care, bed allocation, staff applications, donor management, blood request handling, and blood matching operations.

## Purpose
The purpose of this project is to bring several hospital-related processes into one organized web platform. Instead of handling admissions, bed assignment, donor records, and application approvals in disconnected ways, the system brings them into one structured environment with role-based access.

## Core Goals
- build a role-based hospital system with separated responsibilities for Admins, IT Workers, Doctors, Nurses, Patients, Applicants, and Donors
- manage hospital operations such as admissions, bed allocation, and blood support in one project
- combine database-driven workflows with a usable web interface
- demonstrate both backend workflow design and frontend usability improvements

## Target Users
- hospital administrators
- department-level IT workers or operations staff
- doctors
- nurses
- patients
- job applicants
- blood donors

## Main Modules
### 1. Authentication and Role Control
Handles login, registration, JWT-based protected access, role assignment, and account freeze/unfreeze features.

### 2. Hiring and Application Review
Handles job application submission, review, approval, rejection, and role transition after approval.

### 3. Department, Ward, and Bed Management
Handles care units, beds, admissions, active bed assignment, and discharge-based bed release.

### 4. Clinical Operations
Handles doctor workflows, nurse monitoring, patient portal features, appointments, and medical records access.

### 5. Blood Bank and Donor Operations
Handles blood banks, inventory, donor profiles, availability, health checks, donation logging, and request matching.

## Scope
### In Scope
- user authentication and RBAC
- application submission and review
- department and bed setup
- doctor, nurse, patient, and donor flows
- blood request and donor matching workflows
- Docker-based MSSQL setup

### Out of Scope for the current phase
- full enterprise-grade production hardening
- advanced billing and payment systems
- deep real-time communication features
- complete feature test coverage for every user journey

## Tech Stack
- Backend: Laravel 10, PHP, JWT authentication
- Frontend: Blade-based UI prototype with room for Alpine.js, Livewire, or Inertia-based upgrade
- Database: Microsoft SQL Server
- Runtime/Environment: Docker, Nginx, PHP-FPM
- Data access style: hybrid, with both Eloquent usage and raw SQL service-based logic

## Current Project State
The project has working backend features implemented through the issue flow up to Issue 18. The application can run through Docker, the SQL initialization path is working, and the role-specific backend routes exist. The current UI is functional as a prototype but is still in the stage where it needs redesign and consolidation before final testing and polishing.

## Project Structure Notes
Important areas of the repository:
- `lifelink-app/` -> main Laravel application
- `lifelink-app/resources/views/ui/` -> current Blade prototype pages
- `lifelink-app/routes/` -> route definitions
- `lifelink-app/app/Http/Controllers/` -> controller layer
- `lifelink-app/app/Services/Sql/` -> heavier raw-SQL business logic
- `docker/` -> container and MSSQL initialization setup
- `docs/` -> project documentation files
- `dev_log/` -> development notes and process history

## Project Approach Note
The project is currently best described as hybrid, but operationally it behaves closer to database-first because the running schema is created through SQL initialization scripts in Docker. At the same time, Laravel migrations also exist in the repository, which means schema design knowledge is present in both SQL files and migration files.

## Immediate Next Phase
Before comprehensive testing, the project should first stabilize the user interface so that the main user flows are presented cleanly and consistently for each role.
