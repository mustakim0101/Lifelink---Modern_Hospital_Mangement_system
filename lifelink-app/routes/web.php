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
Route::view('/ui/auth', 'ui.auth');
Route::view('/ui/applications', 'ui.applications');
Route::view('/ui/admin-users', 'ui.admin-users');
Route::view('/ui/application-reviews', 'ui.application-reviews');
Route::view('/ui/ward-setup', 'ui.ward-setup');
Route::view('/ui/it-bed-allocation', 'ui.it-bed-allocation');
Route::view('/ui/doctor-dashboard', 'ui.doctor-dashboard');
