<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Payroll;
use Carbon\Carbon;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Pastikan user terhubung dengan data employee
        if (!$user->employee) {
            // Mungkin tampilkan pesan bahwa data employee belum ada
            return view('user_dashboard')->with('error', 'Data kepegawaian Anda tidak ditemukan.');
        }

        $employeeId = $user->employee->id;

        // 1. Ambil data payroll 6 bulan terakhir
        $payrolls = Payroll::where('employee_id', $employeeId)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->orderBy('periode_tahun', 'asc')
            ->orderBy('periode_bulan', 'asc')
            ->get();
        
        // 2. Ambil data payroll terakhir untuk summary cards dan pie chart
        $latestPayroll = $payrolls->last();

        // 3. Siapkan data untuk grafik tren gaji
        $gaji6Bulan = [
            'labels' => $payrolls->map(fn($p) => Carbon::create($p->periode_tahun, $p->periode_bulan)->isoFormat('MMM YYYY')),
            'data' => $payrolls->pluck('gaji_bersih'),
        ];
        
        return view('user_dashboard', compact('latestPayroll', 'gaji6Bulan'));
    }
}