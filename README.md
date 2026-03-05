# 🏥 LifeLink - Hospital Management System

## 📋 Project Overview
LifeLink is a comprehensive hospital management system built with **Laravel Blade**, **MSSQL**, and **Docker**. It provides role-based access control for hospital staff, patients, blood donors, and applicants with features including bed management, blood bank operations, clinical workflows, and job application processing.

---

## 👥 Contributors

| Name | Email | GitHub |
|------|-------|--------|
| Mustakim Musa | mustakim.official.0101@gmail.com | [mustakim0101](https://github.com/mustakim0101) |
| Ahbab Hasan | hasan100_official@gmail.com | [tigertech119](https://github.com/tigertech119) |
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

## 🚀 Getting Started

### Prerequisites
- Docker & Docker Compose
- Git
- PHP 8.2+
- Composer
- Node.js & NPM
---

