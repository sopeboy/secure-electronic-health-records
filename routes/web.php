<?php

use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ClinicalEntryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\StaffController;
use Illuminate\Support\Facades\Route;

// Guests only
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->name('login.store');
});

// Signed-in and active staff only
Route::middleware(['auth', 'active'])->group(function () {
    Route::redirect('/', '/dashboard');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Staff management — administrators only
    Route::middleware('role:admin')->prefix('staff')->name('staff.')->group(function () {
        Route::get('/', [StaffController::class, 'index'])->name('index');
        Route::get('/create', [StaffController::class, 'create'])->name('create');
        Route::post('/', [StaffController::class, 'store'])->name('store');
        Route::get('/{user}/edit', [StaffController::class, 'edit'])->name('edit');
        Route::put('/{user}', [StaffController::class, 'update'])->name('update');
        Route::patch('/{user}/status', [StaffController::class, 'toggleStatus'])->name('toggle-status');
    });

    // Patients — every action is checked by PatientPolicy inside the controller
    Route::prefix('patients')->name('patients.')->group(function () {
        Route::get('/', [PatientController::class, 'index'])->name('index');
        Route::get('/create', [PatientController::class, 'create'])->name('create');
        Route::post('/', [PatientController::class, 'store'])->name('store');
        Route::get('/{patient}', [PatientController::class, 'show'])->name('show');
        Route::get('/{patient}/edit', [PatientController::class, 'edit'])->name('edit');
        Route::put('/{patient}', [PatientController::class, 'update'])->name('update');
        Route::post('/{patient}/assignments', [PatientController::class, 'assign'])->name('assign');
        Route::delete('/{patient}/assignments/{user}', [PatientController::class, 'unassign'])->name('unassign');

        // Clinical entries (add only — records are never edited or deleted)
        Route::post('/{patient}/records', [ClinicalEntryController::class, 'storeRecord'])->name('records.store');
        Route::post('/{patient}/vitals', [ClinicalEntryController::class, 'storeVitals'])->name('vitals.store');
        Route::post('/{patient}/prescriptions', [ClinicalEntryController::class, 'storePrescription'])->name('prescriptions.store');
        Route::post('/{patient}/lab-results', [ClinicalEntryController::class, 'storeLabResult'])->name('lab-results.store');
    });

    // Audit logs — administrators only, read-only
    Route::get('/audit-logs', [AuditLogController::class, 'index'])
        ->middleware('can:view-audit-logs')->name('audit-logs.index');

    // Reports — administrators and doctors
    Route::get('/reports', [ReportController::class, 'index'])
        ->middleware('can:view-reports')->name('reports.index');
});
