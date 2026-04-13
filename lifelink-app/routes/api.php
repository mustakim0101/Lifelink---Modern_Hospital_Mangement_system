<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Admin\ApplicationReviewController;
use App\Http\Controllers\Api\Admin\AccountControlController;
use App\Http\Controllers\Api\BloodBankSchemaController;
use App\Http\Controllers\Api\BloodMatchingController;
use App\Http\Controllers\Api\DonorDashboardController;
use App\Http\Controllers\Api\DonorNotificationController;
use App\Http\Controllers\Api\DoctorReviewController;
use App\Http\Controllers\Api\DoctorAppointmentRuleController;
use App\Http\Controllers\Api\DoctorClinicalController;
use App\Http\Controllers\Api\ItAppointmentQueueController;
use App\Http\Controllers\Api\JobApplicationController;
use App\Http\Controllers\Api\NurseCareController;
use App\Http\Controllers\Api\PatientPortalController;
use App\Http\Controllers\Api\PublicDepartmentController;
use App\Http\Controllers\Api\PublicWelcomeController;
use App\Http\Controllers\Api\ItBedAllocationController;
use App\Http\Controllers\Api\WardCatalogController;
use Illuminate\Support\Facades\Route;

Route::prefix('public')->group(function () {
    Route::get('/welcome/metrics', [PublicWelcomeController::class, 'metrics']);
    Route::get('/departments', [PublicDepartmentController::class, 'legacyList']);
    Route::get('/departments/catalog', [PublicDepartmentController::class, 'catalog']);
    Route::get('/departments/{slug}', [PublicDepartmentController::class, 'show']);
    Route::get('/departments/{slug}/availability', [PublicDepartmentController::class, 'availability']);
    Route::get('/doctors/{doctor}/reviews', [DoctorReviewController::class, 'index']);
});

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
    Route::patch('/applications/{application}/department', [ApplicationReviewController::class, 'updateDepartment']);
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
    Route::get('/doctors', [ItBedAllocationController::class, 'doctors']);
    Route::get('/patients', [ItBedAllocationController::class, 'patients']);
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
    Route::get('/appointments/summary', [DoctorClinicalController::class, 'appointmentSummary']);
    Route::post('/appointments/{appointment}/cancel', [DoctorClinicalController::class, 'cancelAppointment']);
    Route::get('/appointment-rules', [DoctorAppointmentRuleController::class, 'index']);
    Route::post('/appointment-rules', [DoctorAppointmentRuleController::class, 'store']);
    Route::put('/appointment-rules/{rule}', [DoctorAppointmentRuleController::class, 'update']);
    Route::post('/appointment-rules/{rule}/deactivate', [DoctorAppointmentRuleController::class, 'deactivate']);
    Route::post('/bed-requests', [DoctorClinicalController::class, 'createBedRequest']);
    Route::get('/bed-requests', [DoctorClinicalController::class, 'myBedRequests']);
});

Route::prefix('nurse')->middleware(['auth:api', 'active.user', 'role:Nurse'])->group(function () {
    Route::get('/profile', [NurseCareController::class, 'profile']);
    Route::get('/patients', [NurseCareController::class, 'patients']);
    Route::get('/admissions/{admission}', [NurseCareController::class, 'admissionDetail']);
    Route::get('/admissions/{admission}/vitals', [NurseCareController::class, 'vitalSigns']);
    Route::post('/admissions/{admission}/vitals', [NurseCareController::class, 'logVitalSigns']);
    Route::get('/blood-bank/donors', [NurseCareController::class, 'bloodBankDonors']);
    Route::get('/blood-bank/donors/{donor}/health-checks', [NurseCareController::class, 'donorHealthChecks']);
    Route::post('/blood-bank/donors/{donor}/health-checks', [NurseCareController::class, 'logDonorHealthCheck']);
});

Route::prefix('patient')->middleware(['auth:api', 'active.user', 'role:Patient'])->group(function () {
    Route::get('/portal', [PatientPortalController::class, 'portal']);
    Route::get('/profile', [PatientPortalController::class, 'profile']);
    Route::get('/medical-records', [PatientPortalController::class, 'medicalRecords']);
    Route::get('/appointments', [PatientPortalController::class, 'appointments']);
    Route::get('/booking-options', [PatientPortalController::class, 'bookingOptions']);
    Route::post('/appointments', [PatientPortalController::class, 'bookAppointment']);
    Route::post('/appointments/{appointment}/cancel', [PatientPortalController::class, 'cancelAppointment']);
    Route::post('/doctors/{doctor}/reviews', [DoctorReviewController::class, 'store']);
    Route::post('/blood-requests', [PatientPortalController::class, 'requestBlood']);
    Route::get('/blood-requests', [PatientPortalController::class, 'myBloodRequests']);
});

Route::prefix('donor')->middleware(['auth:api', 'active.user'])->group(function () {
    Route::post('/enroll', [DonorDashboardController::class, 'enroll']);
});

Route::prefix('donor')->middleware(['auth:api', 'active.user', 'role:Donor'])->group(function () {
    Route::get('/dashboard', [DonorDashboardController::class, 'dashboard']);
    Route::get('/profile', [DonorDashboardController::class, 'profile']);
    Route::get('/banks', [DonorDashboardController::class, 'banks']);
    Route::get('/availability', [DonorDashboardController::class, 'availabilities']);
    Route::post('/availability', [DonorDashboardController::class, 'upsertAvailability']);
    Route::get('/donations', [DonorDashboardController::class, 'donations']);
    Route::get('/notifications', [DonorNotificationController::class, 'index']);
    Route::post('/notifications/{notification}/read', [DonorNotificationController::class, 'markRead']);
    Route::post('/notifications/{notification}/respond', [DonorNotificationController::class, 'respond']);
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

Route::prefix('blood/matching')->middleware(['auth:api', 'active.user', 'role:Admin,ITWorker'])->group(function () {
    Route::get('/requests', [BloodMatchingController::class, 'requests']);
    Route::get('/requests/{bloodRequest}/suggestions', [BloodMatchingController::class, 'suggestions']);
    Route::post('/requests/{bloodRequest}/notify', [BloodMatchingController::class, 'notify']);
    Route::get('/requests/{bloodRequest}/matches', [BloodMatchingController::class, 'matches']);
    Route::get('/donors', [BloodMatchingController::class, 'donors']);
    Route::get('/donors/{donor}/health-checks', [BloodMatchingController::class, 'donorHealthChecks']);
    Route::post('/donations', [BloodMatchingController::class, 'logDonation']);
    Route::post('/requests/{bloodRequest}/approve', [BloodMatchingController::class, 'approve']);
    Route::post('/requests/{bloodRequest}/fulfill', [BloodMatchingController::class, 'fulfill']);
});

Route::prefix('appointments/it')->middleware(['auth:api', 'active.user', 'role:Admin,ITWorker'])->group(function () {
    Route::get('/queue', [ItAppointmentQueueController::class, 'queue']);
    Route::post('/{appointment}/approve', [ItAppointmentQueueController::class, 'approve']);
    Route::post('/{appointment}/reject', [ItAppointmentQueueController::class, 'reject']);
    Route::post('/{appointment}/cancel', [ItAppointmentQueueController::class, 'cancel']);
});
