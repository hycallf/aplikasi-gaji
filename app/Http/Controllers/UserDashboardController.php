<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Payroll;
use App\Models\DosenAttendance;
use App\Models\DosenMatkulEnrollment;
use App\Models\Attendance;
use App\Models\AcademicYear;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UserDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user->employee) {
            return view('user_dashboard', [
                'error' => 'Data kepegawaian Anda tidak ditemukan. Silakan hubungi operator.'
            ]);
        }

        $employee = $user->employee;
        $employeeId = $employee->id;
        $isDosen = $employee->tipe_karyawan === 'dosen';

        // === DATA PAYROLL (UMUM UNTUK SEMUA) ===
        $payrolls = Payroll::where('employee_id', $employeeId)
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->orderBy('periode_tahun', 'asc')
            ->orderBy('periode_bulan', 'asc')
            ->get();

        $latestPayroll = $payrolls->last();

        $gaji6Bulan = [
            'labels' => $payrolls->map(fn($p) => Carbon::create($p->periode_tahun, $p->periode_bulan)->isoFormat('MMM YYYY')),
            'data' => $payrolls->pluck('gaji_bersih'),
        ];

        // === DATA KHUSUS BERDASARKAN TIPE ===
        if ($isDosen) {
            // DATA UNTUK DOSEN
            $activeAcademicYear = AcademicYear::where('is_active', true)->first();

            // Enrollment aktif
            $enrollments = collect();
            if ($activeAcademicYear) {
                $enrollments = DosenMatkulEnrollment::with(['matkul', 'academicYear'])
                    ->where('employee_id', $employeeId)
                    ->where('academic_year_id', $activeAcademicYear->id)
                    ->get();
            }

            // Pertemuan bulan ini
            $pertemuanBulanIni = DosenAttendance::where('employee_id', $employeeId)
                ->whereMonth('periode_bulan', Carbon::now()->month)
                ->whereYear('periode_tahun', Carbon::now()->year)
                ->sum('jumlah_pertemuan');

            // Pertemuan per mata kuliah (bulan ini)
            $pertemuanPerMatkul = DosenAttendance::with(['enrollment.matkul'])
                ->where('employee_id', $employeeId)
                ->whereMonth('periode_bulan', Carbon::now()->month)
                ->whereYear('periode_tahun', Carbon::now()->year)
                ->get();

            // Tren pertemuan 6 bulan terakhir
            $trenPertemuan = ['labels' => [], 'data' => []];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $total = DosenAttendance::where('employee_id', $employeeId)
                    ->where('periode_bulan', $date->month)
                    ->where('periode_tahun', $date->year)
                    ->sum('jumlah_pertemuan');

                $trenPertemuan['labels'][] = $date->isoFormat('MMM');
                $trenPertemuan['data'][] = $total;
            }

            return view('user_dashboard', compact(
                'latestPayroll',
                'gaji6Bulan',
                'employee',
                'isDosen',
                'activeAcademicYear',
                'enrollments',
                'pertemuanBulanIni',
                'pertemuanPerMatkul',
                'trenPertemuan'
            ));

        } else {
            // DATA UNTUK KARYAWAN

            // Kehadiran bulan ini
            $kehadiranBulanIni = Attendance::where('employee_id', $employeeId)
                ->whereMonth('date', Carbon::now()->month)
                ->whereYear('date', Carbon::now()->year)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            // Work days setting
            $workDays = Setting::get('work_days', [1, 2, 3, 4, 5, 6]);
            $totalWorkDays = collect(range(1, Carbon::now()->daysInMonth))
                ->filter(function($day) use ($workDays) {
                    $date = Carbon::now()->setDay($day);
                    return in_array($date->dayOfWeek, $workDays) && !$date->isFuture();
                })
                ->count();

            $totalHadir = $kehadiranBulanIni->get('hadir', 0);
            $persentaseKehadiran = $totalWorkDays > 0 ? ($totalHadir / $totalWorkDays) * 100 : 0;

            // Tren kehadiran 6 bulan terakhir
            $trenKehadiran = ['labels' => [], 'data' => []];
            for ($i = 5; $i >= 0; $i--) {
                $date = Carbon::now()->subMonths($i);
                $hadir = Attendance::where('employee_id', $employeeId)
                    ->whereMonth('date', $date->month)
                    ->whereYear('date', $date->year)
                    ->where('status', 'hadir')
                    ->count();

                $trenKehadiran['labels'][] = $date->isoFormat('MMM');
                $trenKehadiran['data'][] = $hadir;
            }

            return view('user_dashboard', compact(
                'latestPayroll',
                'gaji6Bulan',
                'employee',
                'isDosen',
                'kehadiranBulanIni',
                'totalWorkDays',
                'persentaseKehadiran',
                'trenKehadiran'
            ));
        }
    }

    public function payrollHistory()
    {
        $employeeId = Auth::user()->employee->id;

        $payrolls = Payroll::where('employee_id', $employeeId)
            ->orderBy('periode_tahun', 'desc')
            ->orderBy('periode_bulan', 'desc')
            ->paginate(10);

        return view('user_payroll_history', compact('payrolls'));
    }
}
