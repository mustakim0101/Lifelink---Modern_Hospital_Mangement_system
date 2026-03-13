# 🏥 LifeLink - Hospital Management System

## 📋 Project Overview
LifeLink is a comprehensive hospital management system built with **Laravel Blade**, **MSSQL**, and **Docker**. It provides role-based access control for hospital staff, patients, blood donors, and applicants with features including bed management, blood bank operations, clinical workflows, and job application processing.

---

## 👥 Contributors

| Name | Email | GitHub |
|------|-------|--------|
| Mustakim Musa | mustakim.official.0101@gmail.com | [mustakim0101](https://github.com/mustakim0101) |
| Ahbab Hasan | hasan100.official@gmail.com | [tigertech119](https://github.com/tigertech119) |
| Shadman Muhtasim | nksoag2006@gmail.com | [ShadmanMuhtasim](https://github.com/ShadmanMuhtasim) |

---

## 🎯 Features by Role

### 👑 Admin
- Full system access
- Approve/reject job applications
- Freeze/unfreeze user accounts
- View all users and system data
- System-wide configuration

### 🛠️ IT Workers (Department Admins)
- Approve job applications
- Department-wise bed/ward/ICU management
- Search admitted patients within their department
- Blood donor matching based on blood type
- View blood inventory levels
- Send notifications to blood donors

### 👨‍⚕️ Doctors
- View assigned patients
- Write prescriptions
- Give diagnoses
- Manage appointments (cancel/reject)
- Request bed/ICU admission for patients

### 👩‍⚕️ Nurses
- View department-wise patient records
- Monitor bed assignments
- Track patient vital signs (optional)

### 🧑‍🤝‍🧑 Patients
- View personal medical records
- Book appointments
- View prescriptions
- Request blood
- Track blood request status

### 📝 Applicants
- Submit job applications
- Track application status
- View "Admin will contact you" message

### 💉 Blood Donors
- Register as donor
- Set weekly availability
- Track donation history
- Receive blood request notifications
- Log health metrics (weight, temperature)

---

## 🏥 Departments
- 🫀 Cardiology
- 🧠 Neurology
- 🦴 Orthopedics
- 👶 Pediatrics
- 🩺 General Medicine
- 👁 Ophthalmology
- 🦷 Dentistry
-etc

---
## 🚀 Getting Started

### Prerequisites
- Docker & Docker Compose
- Git
- PHP 8.2+
- Composer
- Node.js & NPM
---

## 📅 Project Milestones/Devepolment Pathway

### ✅ Phase 1: Environment & Setup 
- [ ] Initial Laravel installation
- [ ] Docker configuration with MSSQL
- [ ] Database connectivity testing
- [ ] Git repository setup

### ✅ Phase 2: Core Identity & RBAC 
- [ ] JWT authentication setup
- [ ] User registration/login
- [ ] Roles and permissions tables
- [ ] Role middleware implementation

### ✅ Phase 3: Application Flow 
- [ ] Job applications schema
- [ ] Applicant submission flow
- [ ] Admin approval/rejection logic
- [ ] Role transition (Applicant → Staff)

### ✅ Phase 4: Department & Bed Management 
- [ ] Departments and CareUnits schema
- [ ] Bed assignment logic for IT workers
- [ ] Nurse bed viewing dashboard
- [ ] Auto-release bed on discharge

### ✅ Phase 5: Clinical Operations 
- [ ] Patients, Appointments schema
- [ ] Doctor patient management
- [ ] Prescription and diagnosis features
- [ ] Patient portal for records

### ✅ Phase 6: Blood Bank Module 
- [ ] Blood donors and inventory schema
- [ ] Donor availability tracking
- [ ] Blood request matching
- [ ] IT worker notification system

### ✅ Phase 7: Testing & Deployment 
- [ ] Feature testing
- [ ] Performance optimization
- [ ] Documentation
- [ ] Deployment

---


## 🚀 Phase 1: Infrastructure (2 Issues/Commits)


| # | Issue Title | Commit Message | Branch |
|:---:|-------------|----------------|:---:|
| **1** | **Setup Docker environment with MSSQL** | `chore: dockerize laravel  with mssql 2022` | `main` |
| **2** | **Configure MSSQL database connection** | `fix: establish mssql connection and test migrations` | `main` |

## 🔐 Phase 2: Identity & RBAC (3 Issues/Commits)


| # | Issue Title | Commit Message | Branch |
|:---:|-------------|----------------|:---:|
| **3** | **Install JWT & implement authentication** | `feat(auth): install jwt-auth with login/register` | `dev` |
| **4** | **Create RBAC database schema** | `feat(rbac): migrations for users, roles, permissions` | `dev` |
| **5** | **Build role middleware & account controls** | `feat(rbac): role middleware with freeze/unfreeze` | `dev` |

## 📝 Phase 3: Hiring Flow (3 Issues/Commits)


| # | Issue Title | Commit Message | Branch |
|:---:|-------------|----------------|:---:|
| **6** | **Department & application tables** | `feat(hiring): migrations for departments and applications` | `dev` |
| **7** | **Job application submission feature** | `feat(hiring): applicant submission with status tracking` | `dev` |
| **8** | **Admin approval workflow** | `feat(hiring): admin/it approval with auto-role assignment` | `dev` |

## 🏥 Phase 4: Bed Management (3 Issues/Commits)


| # | Issue Title | Commit Message | Branch |
|:---:|-------------|----------------|:---:|
| **9** | **Bed/ICU/Ward schema** | `feat(beds): migrations for care_units and beds` | `dev` |
| **10** | **IT worker bed assignment** | `feat(beds): it-worker dashboard for bed allocation` | `dev` |
| **11** | **Discharge & bed release** | `feat(beds): auto-release bed on patient discharge` | `dev` |

## 👨‍⚕️ Phase 5: Clinical Operations (4 Issues/Commits)


| # | Issue Title | Commit Message | Branch |
|:---:|-------------|----------------|:---:|
| **12** | **Clinical data schema** | `feat(clinical): migrations for patients, appointments, records` | `dev` |
| **13** | **Doctor dashboard & actions** | `feat(clinical): doctor management of patients and bed requests` | `dev` |
| **14** | **Nurse care dashboard** | `feat(clinical): nurse view for dept-wise patient monitoring` | `dev` |
| **15** | **Patient portal** | `feat(clinical): patient portal for records and blood requests` | `dev` |

## 🩸 Phase 6: Blood Bank (3 Issues/Commits)


| # | Issue Title | Commit Message | Branch |
|:---:|-------------|----------------|:---:|
| **16** | **Blood bank schema** | `feat(blood): migrations for donors, inventory, requests` | `dev` |
| **17** | **Donor dashboard & tracking** | `feat(blood): donor availability, weight, temp, bag logging` | `dev` |
| **18** | **Blood matching system** | `feat(blood): it-worker matching with donor notifications` | `dev` |

## ✅ Phase 7: Final Polish (3 Issues/Commits)


| # | Issue Title | Commit Message | Branch |
|:---:|-------------|----------------|:---:|
| **19** | **Comprehensive testing** | `test: feature tests for all role workflows` | `dev` |
| **20** | **API documentation** | `docs: swagger/openapi documentation for all endpoints` | `dev` |
| **21** | **Deployment preparation** | `chore: deployment config and environment setup` | `dev` |

---

## ⚠️⚠️⚠️
###this project is made with RAW SQL Schemas, you may find migration but current step uses :
- Schema source of truth: raw SQL in docker/mssql/init/schema
- Startup source of truth: docker compose up
- Models: active Laravel application layer, keep them
- Migrations: retained for reference/history, not primary setup path