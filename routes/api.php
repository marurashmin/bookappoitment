<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HealthcareProfessionalController;
use App\Http\Controllers\AppointmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);

Route::group(['middleware' => 'auth:sanctum'], function() {
    Route::get('all-healthcare-professional', [HealthcareProfessionalController::class, 'allHealthcareProfessional']);
    Route::post('add-edit-healthcare-professional', [HealthcareProfessionalController::class, 'addEditHealthcareProfessional']);
    Route::post('book-appointment', [AppointmentController::class, 'bookAppointment']);
    Route::get('user-appointment', [AppointmentController::class, 'userAppointment']);
    Route::post('mark-complete-appointment', [AppointmentController::class, 'markCompleteAppointment']);
    Route::post('cancel-appointment', [AppointmentController::class, 'cancelAppointment']);
    Route::post('logout',[AuthController::class,'logout']);
});