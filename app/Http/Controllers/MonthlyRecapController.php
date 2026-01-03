<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Overtime;
use App\Models\Deduction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MonthlyRecapController extends Controller
{
    /**
     * Menampilkan halaman form rekap bulanan.
     */
    public function index(Request $request)
    {
        $employees = Employee::where('tipe_karyawan', 'karyawan')
                         ->where('status', 'aktif')
                         ->orderBy('nama')->get();

        // Ambil data existing untuk ditampilkan di kalender
        $existingData = null;
        if ($request->filled(['employee_id', 'month', 'year'])) {
            $existingData = $this->getExistingData(
                $request->employee_id,
                $request->month,
                $request->year
            );
        }

        // Get work days dan non-working days dari settings
        $workDays = Setting::get('work_days', [1, 2, 3, 4, 5, 6]);
        $selectedMonth = $request->input('month', date('m'));
        $selectedYear = $request->input('year', date('Y'));
        $nonWorkingDays = Setting::getNonWorkingDays($selectedYear, $selectedMonth);

        return view('recap.index', compact('employees', 'existingData', 'workDays', 'nonWorkingDays'));
    }

    /**
     * Ambil data absensi yang sudah ada
     */
    private function getExistingData($employeeId, $month, $year)
    {
        $attendances = Attendance::where('employee_id', $employeeId)
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get()
            ->groupBy('status');

        $overtime = Overtime::where('employee_id', $employeeId)
            ->whereMonth('tanggal_lembur', $month)
            ->whereYear('tanggal_lembur', $year)
            ->sum('upah_lembur');

        // Kelompokkan tanggal per status
        $datesByStatus = [
            'sakit' => [],
            'izin' => [],
            'pulang_awal' => []
        ];

        foreach ($attendances as $status => $records) {
            if (isset($datesByStatus[$status])) {
                $datesByStatus[$status] = $records->map(function($attendance) {
                    return Carbon::parse($attendance->date)->day;
                })->toArray();
            }
        }

        return [
            'employee_id' => $employeeId,
            'month' => $month,
            'year' => $year,
            'dates_by_status' => $datesByStatus,
            'total_overtime' => $overtime,
            'hadir' => $attendances->get('hadir', collect())->count(),
            'sakit' => count($datesByStatus['sakit']),
            'izin' => count($datesByStatus['izin']),
            'pulang_awal' => count($datesByStatus['pulang_awal']),
        ];
    }

    /**
     * Menyimpan data rekap bulanan ke database.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'hadir' => 'required|integer|min:0',
            'sakit' => 'required|integer|min:0',
            'izin' => 'required|integer|min:0',
            'pulang_awal' => 'required|integer|min:0',
            'total_lembur' => 'nullable|numeric|min:0',
        ]);

        $employeeId = $validated['employee_id'];
        $month = $validated['month'];
        $year = $validated['year'];
        $startDate = Carbon::create($year, $month, 1);
        $daysInMonth = $startDate->daysInMonth;

        // AMBIL SETTINGS
        $workDays = Setting::get('work_days', [1, 2, 3, 4, 5, 6]);
        $potonganAmount = Setting::get('pulang_awal_deduction', 10000);

        DB::beginTransaction();
        try {
            // Hapus data lama
            Attendance::where('employee_id', $employeeId)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->delete();

            Overtime::where('employee_id', $employeeId)
                ->whereMonth('tanggal_lembur', $month)
                ->whereYear('tanggal_lembur', $year)
                ->delete();

            Deduction::where('employee_id', $employeeId)
                ->whereMonth('tanggal_potongan', $month)
                ->whereYear('tanggal_potongan', $year)
                ->where('sumber', 'absensi')
                ->delete();

            $statuses = [
                'hadir' => $validated['hadir'],
                'sakit' => $validated['sakit'],
                'izin' => $validated['izin'],
                'pulang_awal' => $validated['pulang_awal'],
            ];

            // Loop sebanyak hari di bulan tersebut
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDate = Carbon::create($year, $month, $day);

                // GUNAKAN SETTING WORK DAYS
                if (!in_array($currentDate->dayOfWeek, $workDays)) {
                    continue; // Skip non-working days
                }

                // Cari status untuk diisi pada hari ini
                $statusToInsert = null;
                foreach($statuses as $status => &$count) {
                    if ($count > 0) {
                        $statusToInsert = $status;
                        $count--;
                        break;
                    }
                }

                // Buat record absensi hanya jika masih ada jatah hari
                if($statusToInsert) {
                    Attendance::create([
                        'employee_id' => $employeeId,
                        'date' => $currentDate->toDateString(),
                        'status' => $statusToInsert,
                    ]);

                    // Jika pulang awal, buat potongan dengan amount dari settings
                    if ($statusToInsert === 'pulang_awal') {
                        Deduction::create([
                            'employee_id' => $employeeId,
                            'tanggal_potongan' => $currentDate->toDateString(),
                            'sumber' => 'absensi',
                            'jenis_potongan' => 'Transport',
                            'jumlah_potongan' => $potonganAmount,
                            'keterangan' => 'Potongan transport karena pulang awal',
                        ]);
                    }
                }
            }

            // Jika ada input total lembur
            if (!empty($validated['total_lembur']) && $validated['total_lembur'] > 0) {
                Overtime::create([
                    'employee_id' => $employeeId,
                    'tanggal_lembur' => $startDate->endOfMonth()->toDateString(),
                    'deskripsi_lembur' => 'Akumulasi lembur periode ' . $startDate->isoFormat('MMMM Y'),
                    'upah_lembur' => $validated['total_lembur'],
                ]);
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data rekap: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('recap.index', [
            'employee_id' => $employeeId,
            'month' => $month,
            'year' => $year
        ])->with('success', 'Data rekap bulanan berhasil disimpan!');
    }
}
