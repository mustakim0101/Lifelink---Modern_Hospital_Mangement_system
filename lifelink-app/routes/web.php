<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::view('/ui', 'ui.index');
Route::redirect('/ui/auth', '/ui/login');
Route::view('/ui/login', 'ui.auth', ['mode' => 'login']);
Route::view('/ui/register/patient', 'ui.auth', ['mode' => 'patient']);
Route::view('/ui/register/donor', 'ui.auth', ['mode' => 'donor']);
Route::view('/ui/register/applicant', 'ui.auth', ['mode' => 'applicant']);
Route::view('/ui/dashboard', 'ui.dashboard');
Route::view('/ui/dev-tools', 'ui.dev-tools');
Route::view('/ui/applications', 'ui.applications');
Route::view('/ui/admin-users', 'ui.admin-users');
Route::view('/ui/application-reviews', 'ui.application-reviews');
Route::view('/ui/ward-setup', 'ui.ward-setup');
Route::view('/ui/it-bed-allocation', 'ui.it-bed-allocation');
Route::view('/ui/doctor-dashboard', 'ui.doctor-dashboard');
Route::view('/ui/nurse-dashboard', 'ui.nurse-dashboard');
Route::view('/ui/patient-portal', 'ui.patient-portal');
Route::view('/ui/blood-bank-schema', 'ui.blood-bank-schema');
Route::view('/ui/donor-dashboard', 'ui.donor-dashboard');
Route::view('/ui/blood-matching', 'ui.blood-matching');
