<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\EventController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// routes/web.php

// Hanya bisa diakses oleh user dengan role 'operator'

// routes/web.php
Route::middleware(['auth', 'is_operator'])->group(function () {
    Route::resource('users', UserController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('events', EventController::class);

    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('/attendances/search', [AttendanceController::class, 'search'])->name('attendances.search');

    Route::get('/overtimes', [OvertimeController::class, 'index'])->name('overtimes.index');
    Route::post('/overtimes', [OvertimeController::class, 'store'])->name('overtimes.store');
    Route::get('/overtimes/search', [OvertimeController::class, 'search'])->name('overtimes.search');
    // Tambahkan route-route khusus operator lainnya di sini
});

require __DIR__.'/auth.php';
