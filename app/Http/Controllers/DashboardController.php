<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Payroll;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Data untuk summary cards
        $totalKaryawanAktif = Employee::where('status', 'aktif')->count();
        $totalUser = User::count(); // <-- Data baru: Total User
        $totalGajiBulanIni = Payroll::where('periode_bulan', Carbon::now()->month)
                                    ->where('periode_tahun', Carbon::now()->year)
                                    ->sum('gaji_bersih');

        // notif untuk absensi
        $showUrgentMessage = false;
        $today = Carbon::now();
        if ($today->dayOfWeek !== Carbon::SUNDAY) {
            $activeEmployeeIds = Employee::where('status', 'aktif')->pluck('id');
            $submittedEmployeeIds = Attendance::whereDate('date', $today->toDateString())->pluck('employee_id');
            $unsubmittedEmployees = $activeEmployeeIds->diff($submittedEmployeeIds);

            if ($unsubmittedEmployees->isNotEmpty()) {
                $showUrgentMessage = true;
            }
        }

        // 2. DATA GRAFIK KEHADIRAN (Bulan Ini)
        $kehadiranBulanIni = Attendance::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status'); // Hasilnya: ['hadir' => 100, 'sakit' => 5, ...]

        // 3. DATA GRAFIK PENGELUARAN (6 Bulan Terakhir)
        $pengeluaran6Bulan = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bulan = $date->isoFormat('MMM');
            $totalGaji = Payroll::where('periode_bulan', $date->month)
                                ->where('periode_tahun', $date->year)
                                ->sum('gaji_bersih');
            
            $pengeluaran6Bulan['labels'][] = $bulan;
            $pengeluaran6Bulan['data'][] = $totalGaji;
        }

        // 4. DATA GRAFIK KOMPONEN GAJI (Bulan Ini)
        $komponenGajiBulanIni = Payroll::where('periode_bulan', Carbon::now()->month)
            ->where('periode_tahun', Carbon::now()->year)
            ->select(
                DB::raw('SUM(gaji_pokok) as pokok'),
                DB::raw('SUM(total_tunjangan_transport) as transport'),
                DB::raw('SUM(total_upah_lembur) as lembur'),
                DB::raw('SUM(total_insentif) as insentif')
            )->first();

        $employees = Employee::where('status', 'aktif')->orderBy('nama')->get();
        // Kirim semua data yang sudah diolah ke view
        return view('dashboard', [
            'totalKaryawanAktif' => $totalKaryawanAktif,
            'totalUser' => $totalUser,
            'totalGajiBulanIni' => $totalGajiBulanIni,
            'kehadiranBulanIni' => $kehadiranBulanIni,
            'pengeluaran6Bulan' => $pengeluaran6Bulan,
            'komponenGajiBulanIni' => $komponenGajiBulanIni,
            'showUrgentMessage' => $showUrgentMessage,
            'employees' => $employees,
        ]);
    }

    // app/Http/Controllers/DashboardController.php

    public function getEmployeeCalendarData(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        // --- BAGIAN YANG DIPERBAIKI ---
        // Kita gunakan Carbon untuk memastikan kita membandingkan rentang tanggal penuh
        $startDate = Carbon::parse($validated['start'])->startOfDay();
        $endDate = Carbon::parse($validated['end'])->endOfDay();

        $attendances = Attendance::where('employee_id', $validated['employee_id'])
            // Menggunakan where() yang lebih eksplisit daripada whereBetween
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->get(['date', 'status']);
        // --- AKHIR PERBAIKAN ---
        
        // Logika untuk mengubah data ke format FullCalendar (tetap sama)
        $calendarEvents = $attendances->map(function ($item) {
            $colors = [
                'hadir' => '#22c55e',
                'sakit' => '#f97316',
                'izin' => '#eab308',
                'telat' => '#3b82f6',
                'pulang_awal' => '#ef4444'
            ];
            return [
                'title' => str_replace('_', ' ', $item->status),
                'start' => Carbon::parse($item->date)->toDateString(),
                'backgroundColor' => $colors[$item->status] ?? '#d1d5db',
                'borderColor' => $colors[$item->status] ?? '#d1d5db',
            ];
        });

        return response()->json($calendarEvents);
    }
}