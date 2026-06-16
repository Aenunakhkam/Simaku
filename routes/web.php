<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('majors', \App\Http\Controllers\MajorController::class);
    Route::resource('classrooms', \App\Http\Controllers\ClassroomController::class);
    Route::get('students/template', [\App\Http\Controllers\StudentController::class, 'template'])->name('students.template');
    Route::post('students/import', [\App\Http\Controllers\StudentController::class, 'import'])->name('students.import');
    Route::resource('students', \App\Http\Controllers\StudentController::class);
    
    Route::resource('academic-years', \App\Http\Controllers\AcademicYearController::class);
    Route::post('academic-years/{academic_year}/set-active', [\App\Http\Controllers\AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

    Route::resource('payment-categories', \App\Http\Controllers\PaymentCategoryController::class);
    Route::resource('expense-categories', \App\Http\Controllers\ExpenseCategoryController::class);
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    
    // Billings & Payments
    Route::resource('billings', \App\Http\Controllers\BillingController::class);
    Route::get('/payments', [\App\Http\Controllers\PaymentController::class, 'index'])->name('payments.index');
    Route::get('/payments/process/{student}', [\App\Http\Controllers\PaymentController::class, 'process'])->name('payments.process');
    Route::post('/payments/process/{student}', [\App\Http\Controllers\PaymentController::class, 'store'])->name('payments.store');
    Route::delete('/payments/{payment}', [\App\Http\Controllers\PaymentController::class, 'destroy'])->name('payments.destroy');

    Route::get('/updates', [\App\Http\Controllers\UpdateController::class, 'index'])->name('updates');
    Route::post('/updates/pull', [\App\Http\Controllers\UpdateController::class, 'pull'])->name('updates.pull');
    Route::get('backup', [\App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
    Route::post('backup/create', [\App\Http\Controllers\BackupController::class, 'create'])->name('backup.create');
    Route::get('backup/download/{file}', [\App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
    Route::delete('backup/delete/{file}', [\App\Http\Controllers\BackupController::class, 'delete'])->name('backup.delete');
    Route::post('backup/restore', [\App\Http\Controllers\BackupController::class, 'restore'])->name('backup.restore');

    Route::get('/reports/classrooms', [\App\Http\Controllers\ReportController::class, 'classrooms'])->name('reports.classrooms');
    Route::get('/reports/classrooms/{id}', [\App\Http\Controllers\ReportController::class, 'classroomDetail'])->name('reports.classroom.detail');
});