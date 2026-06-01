<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\Doctor\DashboardController as DoctorDashboardController;
use App\Http\Controllers\Patient\DashboardController as PatientDashboardController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'home'])->name('home');

Route::get('/login/{role}', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login/{role}', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::post('/doctors/{doctor}/approve', [AdminDashboardController::class, 'approveDoctor'])->name('doctors.approve');
    Route::post('/doctors/{doctor}/reject', [AdminDashboardController::class, 'rejectDoctor'])->name('doctors.reject');
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
});

Route::middleware(['auth', 'role:doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments', [AppointmentController::class, 'doctorIndex'])->name('appointments.index');
    Route::post('/appointments/{appointment}/discharge', [BillingController::class, 'discharge'])->name('appointments.discharge');
});

Route::middleware(['auth', 'role:patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/appointments/create', [AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/invoices', [BillingController::class, 'patientInvoices'])->name('invoices.index');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
});
