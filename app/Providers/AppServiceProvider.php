<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\View\Composers\CompanyProfileComposer;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer([
            'layouts.app',             // Layout utama setelah login
            'layouts.guest',           // Layout untuk halaman login/register
            'reports.payslip',         // View PDF Slip Gaji
            'reports.payroll_report',  // View PDF Laporan
        ], CompanyProfileComposer::class);
    }
}
