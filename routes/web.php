<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('majors', \App\Http\Controllers\MajorController::class);
    Route::resource('classrooms', \App\Http\Controllers\ClassroomController::class);
    Route::resource('students', \App\Http\Controllers\StudentController::class);
    Route::resource('payment-categories', \App\Http\Controllers\PaymentCategoryController::class);
    Route::resource('expense-categories', \App\Http\Controllers\ExpenseCategoryController::class);
    Route::resource('expenses', \App\Http\Controllers\ExpenseController::class);
    
    Route::get('/updates', [\App\Http\Controllers\UpdateController::class, 'index'])->name('updates');
    Route::get('/backup', [\App\Http\Controllers\BackupController::class, 'index'])->name('backup.index');
    Route::get('/backup/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('backup.download');
});