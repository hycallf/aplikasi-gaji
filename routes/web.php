<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\OvertimeController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\IncentiveController;
use App\Http\Controllers\DeductionController;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserDashboardController;
use Illuminate\Support\Facades\Route;



Route::get('/', function () {
    // Cek apakah user sudah login
    if (Auth::check()) {
        $user = Auth::user();
        // Jika rolenya operator, arahkan ke dashboard admin
        if ($user->role === 'operator') {
            return redirect()->route('dashboard');
        }
        // Jika bukan, arahkan ke dashboard user biasa
        else {
            return redirect()->route('user.dashboard');
        }
    }

    // Jika tidak ada yang login, tampilkan halaman welcome
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', [\App\Http\Controllers\Auth\EmailVerificationPromptController::class, '__invoke'])
                ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', [\App\Http\Controllers\Auth\VerifyEmailController::class, '__invoke'])
                ->middleware(['signed', 'throttle:6,1'])
                ->name('verification.verify');

    Route::post('email/verification-notification', [\App\Http\Controllers\Auth\EmailVerificationNotificationController::class, 'store'])
                ->middleware('throttle:6,1')
                ->name('verification.send');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/payslip/{payroll}', [ReportController::class, 'downloadPayslip'])->name('payslip.download');
});

// routes karyawan

Route::middleware(['auth', 'verified', 'is_not_operator'])->group(function () {
    Route::get('/my-dashboard', [UserDashboardController::class, 'index'])->name('user.dashboard');

     Route::get('/my-history/payroll', [UserDashboardController::class, 'payrollHistory'])->name('user.payroll.history');
     
    // Jika ada halaman lain khusus karyawan/dosen, letakkan di sini juga.
});

// Hanya bisa diakses oleh user dengan role 'operator'

Route::middleware(['auth', 'is_operator'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('is_operator');
    Route::resource('users', UserController::class);
    Route::resource('employees', EmployeeController::class);
    Route::resource('events', EventController::class);
    Route::resource('deductions', DeductionController::class);
    
    Route::post('/events/{event}/incentives', [IncentiveController::class, 'store'])->name('events.incentives.store');
    
    // DITAMBAHKAN: Route untuk mengupdate insentif spesifik
    Route::put('/incentives/{incentive}', [IncentiveController::class, 'update'])->name('incentives.update');
    
    Route::delete('/incentives/{incentive}', [IncentiveController::class, 'destroy'])->name('incentives.destroy');

    Route::get('/attendances', [AttendanceController::class, 'index'])->name('attendances.index');
    Route::post('/attendances', [AttendanceController::class, 'store'])->name('attendances.store');
    Route::get('/attendances/search', [AttendanceController::class, 'search'])->name('attendances.search');

    Route::get('/overtimes', [OvertimeController::class, 'index'])->name('overtimes.index');
    Route::post('/overtimes', [OvertimeController::class, 'store'])->name('overtimes.store');
    Route::get('/overtimes/search', [OvertimeController::class, 'search'])->name('overtimes.search');

    Route::get('/payroll', [PayrollController::class, 'index'])->name('payroll.index');
    Route::post('/payroll', [PayrollController::class, 'process'])->name('payroll.process');
    Route::get('/payroll/{payroll}/details/{type}', [PayrollController::class, 'getDetails'])->name('payroll.details');

    Route::get('/report/payroll', [ReportController::class, 'downloadPayrollReport'])->name('report.payroll');

    Route::get('/dashboard/employee-calendar', [DashboardController::class, 'getEmployeeCalendarData'])->name('dashboard.employee_calendar');


    // Tambahkan route-route khusus operator lainnya di sini
});

require __DIR__.'/auth.php';
