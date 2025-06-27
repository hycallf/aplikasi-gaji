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

    // Route::get('/report/payroll', [ReportController::class, 'downloadPayrollReport'])->name('report.payroll');
    // Route::get('/payslip/{payroll}', [ReportController::class, 'downloadPayslip'])->name('payslip.download');


    Route::get('/debug-payroll-ajax', function(Illuminate\Http\Request $request) {
        // Kita akan berpura-pura ini adalah request AJAX
        // Ini akan memaksa logika `if ($request->ajax())` di controller Anda untuk berjalan.
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');

        // Ambil bulan & tahun saat ini untuk simulasi
        $request->merge([
            'month' => date('m'),
            'year' => date('Y'),
        ]);

        // Panggil method index dari PayrollController secara manual
        try {
            $controller = new App\Http\Controllers\PayrollController();
            return $controller->index($request);
        } catch (Throwable $e) {
            // Jika ada error apapun, tampilkan dengan Ignition
            throw $e;
        }
    });
    // Tambahkan route-route khusus operator lainnya di sini
});

require __DIR__.'/auth.php';
