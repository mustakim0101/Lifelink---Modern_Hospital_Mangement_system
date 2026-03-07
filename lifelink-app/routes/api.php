<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\ApplicationReviewController;
use App\Http\Controllers\Api\Admin\AccountControlController;
use App\Http\Controllers\Api\BloodBankSchemaController;
use App\Http\Controllers\Api\DoctorClinicalController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\NurseCareController;
use App\Http\Controllers\Api\PatientPortalController;
use App\Http\Controllers\Api\ItBedAllocationController;
use App\Http\Controllers\Api\WardCatalogController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware(['auth:api', 'active.user'])->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/refresh', [AuthController::class, 'refresh']);
    });
});

Route::prefix('dev')->group(function () {
    Route::post('/create-admin', [AuthController::class, 'createAdmin']);
});

Route::prefix('admin')->middleware(['auth:api', 'active.user', 'role:Admin'])->group(function () {
    Route::post('/users/{user}/freeze', [AccountControlController::class, 'freeze']);
    Route::post('/users/{user}/unfreeze', [AccountControlController::class, 'unfreeze']);
    Route::get('/users/{user}/status', [AccountControlController::class, 'status']);
    Route::post('/doctors/profile', [DoctorClinicalController::class, 'upsertDoctorProfile']);
    Route::post('/nurses/profile', [NurseCareController::class, 'upsertNurseProfile']);
});

Route::prefix('admin')->middleware(['auth:api', 'active.user', 'role:Admin,ITWorker'])->group(function () {
    Route::get('/applications', [ApplicationReviewController::class, 'index']);
    Route::post('/applications/{application}/approve', [ApplicationReviewController::class, 'approve']);
    Route::post('/applications/{application}/reject', [ApplicationReviewController::class, 'reject']);
});

Route::prefix('applications')->middleware(['auth:api', 'active.user'])->group(function () {
    Route::post('/', [JobApplicationController::class, 'submit']);
    Route::get('/my', [JobApplicationController::class, 'myApplications']);
    Route::get('/my/latest', [JobApplicationController::class, 'myLatest']);
});

Route::prefix('ward')->middleware(['auth:api', 'active.user'])->group(function () {
    Route::get('/departments', [WardCatalogController::class, 'departments']);
    Route::get('/care-units', [WardCatalogController::class, 'careUnits']);
    Route::get('/beds', [WardCatalogController::class, 'beds']);
    Route::get('/beds/summary', [WardCatalogController::class, 'bedSummary']);
});

Route::prefix('ward')->middleware(['auth:api', 'active.user', 'role:Admin,ITWorker'])->group(function () {
    Route::post('/care-units', [WardCatalogController::class, 'storeCareUnit']);
    Route::post('/beds', [WardCatalogController::class, 'storeBed']);
});

Route::prefix('ward/it')->middleware(['auth:api', 'active.user', 'role:Admin,ITWorker'])->group(function () {
    Route::get('/departments', [ItBedAllocationController::class, 'myDepartments']);
    Route::get('/admissions', [ItBedAllocationController::class, 'admissions']);
    Route::get('/available-beds', [ItBedAllocationController::class, 'availableBeds']);
    Route::post('/admissions', [ItBedAllocationController::class, 'createAdmission']);
    Route::post('/admissions/{admission}/discharge', [ItBedAllocationController::class, 'dischargeAdmission']);
    Route::post('/assign-bed', [ItBedAllocationController::class, 'assignBed']);
});

Route::post('/ward/it/department-admins', [ItBedAllocationController::class, 'assignDepartmentToItWorker'])
    ->middleware(['auth:api', 'active.user', 'role:Admin']);

Route::prefix('doctor')->middleware(['auth:api', 'active.user', 'role:Doctor'])->group(function () {
    Route::get('/profile', [DoctorClinicalController::class, 'profile']);
    Route::get('/patients', [DoctorClinicalController::class, 'patients']);
    Route::get('/appointments', [DoctorClinicalController::class, 'appointments']);
    Route::post('/appointments/{appointment}/cancel', [DoctorClinicalController::class, 'cancelAppointment']);
    Route::post('/bed-requests', [DoctorClinicalController::class, 'createBedRequest']);
    Route::get('/bed-requests', [DoctorClinicalController::class, 'myBedRequests']);
});

Route::prefix('nurse')->middleware(['auth:api', 'active.user', 'role:Nurse'])->group(function () {
    Route::get('/profile', [NurseCareController::class, 'profile']);
    Route::get('/patients', [NurseCareController::class, 'patients']);
    Route::get('/admissions/{admission}', [NurseCareController::class, 'admissionDetail']);
    Route::get('/admissions/{admission}/vitals', [NurseCareController::class, 'vitalSigns']);
    Route::post('/admissions/{admission}/vitals', [NurseCareController::class, 'logVitalSigns']);
});

Route::prefix('patient')->middleware(['auth:api', 'active.user', 'role:Patient'])->group(function () {
    Route::get('/portal', [PatientPortalController::class, 'portal']);
    Route::get('/profile', [PatientPortalController::class, 'profile']);
    Route::get('/medical-records', [PatientPortalController::class, 'medicalRecords']);
    Route::get('/appointments', [PatientPortalController::class, 'appointments']);
    Route::get('/booking-options', [PatientPortalController::class, 'bookingOptions']);
    Route::post('/appointments', [PatientPortalController::class, 'bookAppointment']);
    Route::post('/appointments/{appointment}/cancel', [PatientPortalController::class, 'cancelAppointment']);
    Route::post('/blood-requests', [PatientPortalController::class, 'requestBlood']);
    Route::get('/blood-requests', [PatientPortalController::class, 'myBloodRequests']);
});

Route::prefix('blood/schema')->middleware(['auth:api', 'active.user', 'role:Admin,ITWorker'])->group(function () {
    Route::get('/overview', [BloodBankSchemaController::class, 'overview']);
    Route::get('/banks', [BloodBankSchemaController::class, 'banks']);
    Route::post('/banks', [BloodBankSchemaController::class, 'createBank']);
    Route::get('/donor-profiles', [BloodBankSchemaController::class, 'donorProfiles']);
    Route::post('/donor-profiles', [BloodBankSchemaController::class, 'upsertDonorProfile']);
    Route::get('/inventory', [BloodBankSchemaController::class, 'inventory']);
    Route::post('/inventory', [BloodBankSchemaController::class, 'upsertInventory']);
    Route::get('/requests', [BloodBankSchemaController::class, 'requests']);
});
