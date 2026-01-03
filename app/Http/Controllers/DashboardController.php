<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\DosenAttendance;
use App\Models\DosenMatkulEnrollment;
use App\Models\AcademicYear;
use App\Models\Payroll;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index()
    {
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        // === SUMMARY CARDS ===
        $totalKaryawanAktif = Employee::where('status', 'aktif')
                                     ->where('tipe_karyawan', 'karyawan')
                                     ->count();

        $totalDosenAktif = Employee::where('status', 'aktif')
                                  ->where('tipe_karyawan', 'dosen')
                                  ->count();

        $totalUser = User::count();

        $totalGajiBulanIni = Payroll::where('periode_bulan', Carbon::now()->month)
                                    ->where('periode_tahun', Carbon::now()->year)
                                    ->sum('gaji_bersih');

        // === NOTIFIKASI ABSENSI KARYAWAN ===
        $showUrgentMessage = false;
        $workDays = Setting::get('work_days', [1, 2, 3, 4, 5, 6]);
        $today = Carbon::now();

        if (in_array($today->dayOfWeek, $workDays)) {
            $activeKaryawanIds = Employee::where('status', 'aktif')
                                        ->where('tipe_karyawan', 'karyawan')
                                        ->pluck('id');
            $submittedKaryawanIds = Attendance::whereDate('date', $today->toDateString())
                                              ->pluck('employee_id');
            $unsubmittedKaryawan = $activeKaryawanIds->diff($submittedKaryawanIds);

            if ($unsubmittedKaryawan->isNotEmpty()) {
                $showUrgentMessage = true;
            }
        }

        // === NOTIFIKASI ABSENSI DOSEN ===
        $showDosenReminder = false;
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();
        $dosenEnrollments = collect(); // Inisialisasi koleksi kosong

        if ($activeAcademicYear) {
            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            // FIX: Gunakan where biasa karena kolom periode_bulan adalah integer, bukan date
            $totalEnrollments = DosenMatkulEnrollment::where('academic_year_id', $activeAcademicYear->id)->count();
            $submittedDosenAttendances = DosenAttendance::where('periode_bulan', $currentMonth)
                                                       ->where('periode_tahun', $currentYear)
                                                       ->where('academic_year_id', $activeAcademicYear->id)
                                                       ->count();

            if ($submittedDosenAttendances < $totalEnrollments) {
                $showDosenReminder = true;
            }

            // BARU: Ambil data enrollment detail per dosen untuk list dashboard
            $dosenEnrollments = Employee::where('tipe_karyawan', 'dosen')
                ->where('status', 'aktif')
                ->whereHas('enrollments', function($q) use ($activeAcademicYear) {
                    $q->where('academic_year_id', $activeAcademicYear->id);
                })
                ->with(['enrollments' => function($q) use ($activeAcademicYear) {
                    $q->where('academic_year_id', $activeAcademicYear->id)
                      ->with('matkul');
                }])
                ->get()
                ->map(function($dosen) {
                    $dosen->total_sks = $dosen->enrollments->sum(function($enrol) {
                        return $enrol->matkul->sks ?? 0;
                    });
                    return $dosen;
                });
        }

        // === DATA GRAFIK KEHADIRAN KARYAWAN (Bulan Ini) ===
        $kehadiranBulanIni = Attendance::whereMonth('date', Carbon::now()->month)
            ->whereYear('date', Carbon::now()->year)
            ->whereHas('employee', function($q) {
                $q->where('tipe_karyawan', 'karyawan');
            })
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        // === DATA PERTEMUAN DOSEN (Bulan Ini) ===
        $pertemuanDosenBulanIni = DosenAttendance::where('periode_bulan', Carbon::now()->month)
            ->where('periode_tahun', Carbon::now()->year)
            ->sum('jumlah_pertemuan');

        // === DATA GRAFIK PENGELUARAN (6 Bulan Terakhir) ===
        $pengeluaran6Bulan = ['labels' => [], 'data' => []];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $bulan = $date->isoFormat('MMM');
            $totalGaji = Payroll::where('periode_bulan', $date->month)
                                ->where('periode_tahun', $date->year)
                                ->sum('gaji_bersih');

            $pengeluaran6Bulan['labels'][] = $bulan;
            $pengeluaran6Bulan['data'][] = $totalGaji;
        }

        // === DATA GRAFIK KOMPONEN GAJI (Bulan Ini) ===
        $komponenGajiBulanIni = Payroll::where('periode_bulan', Carbon::now()->month)
            ->where('periode_tahun', Carbon::now()->year)
            ->select(
                DB::raw('SUM(gaji_pokok) as pokok'),
                DB::raw('SUM(total_tunjangan_transport) as transport'),
                DB::raw('SUM(total_upah_lembur) as lembur'),
                DB::raw('SUM(total_insentif) as insentif')
            )->first();

        // === DATA BREAKDOWN GAJI DOSEN VS KARYAWAN ===
        $gajiKaryawanBulanIni = Payroll::where('periode_bulan', Carbon::now()->month)
            ->where('periode_tahun', Carbon::now()->year)
            ->whereHas('employee', fn($q) => $q->where('tipe_karyawan', 'karyawan'))
            ->sum('gaji_bersih');

        $gajiDosenBulanIni = Payroll::where('periode_bulan', Carbon::now()->month)
            ->where('periode_tahun', Carbon::now()->year)
            ->whereHas('employee', fn($q) => $q->where('tipe_karyawan', 'dosen'))
            ->sum('gaji_bersih');

        // === ENROLLMENT STATS ===
        $enrollmentStats = null;
        if ($activeAcademicYear) {
            $enrollmentStats = [
                'total' => DosenMatkulEnrollment::where('academic_year_id', $activeAcademicYear->id)->count(),
                'by_dosen' => DosenMatkulEnrollment::where('academic_year_id', $activeAcademicYear->id)
                    ->select('employee_id', DB::raw('count(*) as total'))
                    ->groupBy('employee_id')
                    ->count(),
                'academic_year' => $activeAcademicYear,
            ];
        }

        // === TOP 5 DOSEN (Pertemuan Terbanyak Bulan Ini) ===
        $topDosen = DosenAttendance::with('employee')
            ->where('periode_bulan', Carbon::now()->month)
            ->where('periode_tahun', Carbon::now()->year)
            ->select('employee_id', DB::raw('SUM(jumlah_pertemuan) as total_pertemuan'))
            ->groupBy('employee_id')
            ->orderByDesc('total_pertemuan')
            ->limit(5)
            ->get();

        // === DATA UNTUK KALENDER ===
        $employees = Employee::where('status', 'aktif')
                            ->where('tipe_karyawan', 'karyawan')
                            ->orderBy('nama')
                            ->get();


        return view('dashboard', compact(
            'totalKaryawanAktif',
            'totalDosenAktif',
            'totalUser',
            'totalGajiBulanIni',
            'gajiKaryawanBulanIni',
            'gajiDosenBulanIni',
            'kehadiranBulanIni',
            'pertemuanDosenBulanIni',
            'pengeluaran6Bulan',
            'komponenGajiBulanIni',
            'showUrgentMessage',
            'showDosenReminder',
            'dosenEnrollments', // Variable baru
            'activeAcademicYear',
            'topDosen',
            'employees'
        ));
    }

    public function getEmployeeCalendarData(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $startDate = Carbon::parse($validated['start'])->startOfDay();
        $endDate = Carbon::parse($validated['end'])->endOfDay();

        $attendances = Attendance::where('employee_id', $validated['employee_id'])
            ->where('date', '>=', $startDate)
            ->where('date', '<=', $endDate)
            ->get(['date', 'status']);

        $calendarEvents = $attendances->map(function ($item) {
            $colors = [
                'hadir' => '#22c55e',
                'sakit' => '#f97316',
                'izin' => '#eab308',
                'telat' => '#3b82f6',
                'pulang_awal' => '#ef4444'
            ];
            return [
                'title' => str_replace('_', ' ', ucfirst($item->status)),
                'start' => Carbon::parse($item->date)->toDateString(),
                'backgroundColor' => $colors[$item->status] ?? '#d1d5db',
                'borderColor' => $colors[$item->status] ?? '#d1d5db',
            ];
        });

        return response()->json($calendarEvents);
    }
}
