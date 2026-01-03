<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Deduction;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    /**
     * Menampilkan halaman utama dengan semua data yang dibutuhkan
     */
    public function index(Request $request)
    {
        $selectedDate = Carbon::parse($request->input('date', Carbon::today()));
        $searchName = $request->input('search_name');

        // AMBIL WORK DAYS DARI SETTINGS
        $workDays = Setting::get('work_days', [1, 2, 3, 4, 5, 6]);

        // Data untuk Kalender Navigasi
        $totalActiveEmployees = Employee::where('status', 'aktif')
                                       ->where('tipe_karyawan', 'karyawan')
                                       ->count();

        $attendanceSummary = Attendance::whereYear('date', $selectedDate->year)
            ->whereMonth('date', $selectedDate->month)
            ->select('date', DB::raw('count(*) as total_submitted'))
            ->groupBy('date')
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->date)->day);

        $completedDates = [];
        foreach($attendanceSummary as $day => $summary) {
            if ($summary->total_submitted >= $totalActiveEmployees) {
                $completedDates[] = $day;
            }
        }

        // Data untuk Form Input
        $employees = $this->getFilteredEmployees($request);
        $attendances = Attendance::whereDate('date', $selectedDate->toDateString())
                                ->pluck('status', 'employee_id')
                                ->all();
        $descriptions = Attendance::whereDate('date', $selectedDate->toDateString())
                                  ->pluck('description', 'employee_id')
                                  ->all();

        return view('attendances.index', compact(
            'employees',
            'selectedDate',
            'attendances',
            'descriptions',
            'completedDates',
            'searchName',
            'workDays' // KIRIM KE VIEW
        ));
    }

    /**
     * Method untuk melayani request pencarian dari HTMX
     */
    public function search(Request $request)
    {
        $selectedDate = Carbon::parse($request->input('date', Carbon::today()));
        $employees = $this->getFilteredEmployees($request);
        $attendances = Attendance::whereDate('date', $selectedDate->toDateString())
                                ->pluck('status', 'employee_id')
                                ->all();
        $descriptions = Attendance::whereDate('date', $selectedDate->toDateString())
                                  ->pluck('description', 'employee_id')
                                  ->all();

        return view('attendances._employee_rows', compact('employees', 'attendances', 'descriptions'));
    }

    /**
     * Method private untuk mengambil data karyawan yang difilter
     */
    private function getFilteredEmployees(Request $request)
    {
        $query = Employee::where('status', 'aktif')
                         ->where('tipe_karyawan', 'karyawan')
                         ->orderBy('nama');

        if ($request->filled('search_name')) {
            $query->where('nama', 'like', '%' . $request->search_name . '%');
        }

        return $query->get();
    }

    /**
     * UPDATED: Menyimpan data absensi dengan validasi work days dari settings
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendances' => 'required|array',
        ], [
            'date.before_or_equal' => 'Anda tidak bisa mengisi absensi untuk tanggal di masa depan.'
        ]);

        $date = $request->input('date');
        $dateCarbon = Carbon::parse($date);

        // VALIDASI: Cek apakah tanggal adalah hari kerja
        $workDays = Setting::get('work_days', [1, 2, 3, 4, 5, 6]);
        if (!in_array($dateCarbon->dayOfWeek, $workDays)) {
            return back()->with('error', 'Tanggal yang dipilih bukan hari kerja menurut pengaturan sistem.')
                        ->withInput();
        }

        // AMBIL SETTING POTONGAN
        $potonganAmount = Setting::get('pulang_awal_deduction', 10000);

        DB::beginTransaction();
        try {
            foreach ($request->attendances as $employeeId => $status) {
                $description = $request->descriptions[$employeeId] ?? null;

                // Update atau buat record attendance
                Attendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'date' => $date,
                    ],
                    [
                        'status' => $status,
                        'description' => $description,
                    ]
                );

                // Handle potongan untuk pulang awal
                if ($status == 'pulang_awal') {
                    // Buat atau update potongan (gunakan amount dari settings)
                    Deduction::updateOrCreate(
                        [
                            'employee_id' => $employeeId,
                            'tanggal_potongan' => $date,
                            'sumber' => 'absensi',
                        ],
                        [
                            'jenis_potongan' => 'Transport',
                            'jumlah_potongan' => $potonganAmount,
                            'keterangan' => 'Potongan transport karena pulang awal',
                        ]
                    );
                } else {
                    // Hapus potongan jika status bukan pulang awal
                    Deduction::where('employee_id', $employeeId)
                        ->where('tanggal_potongan', $date)
                        ->where('sumber', 'absensi')
                        ->delete();
                }
            }

            DB::commit();

            return redirect()
                ->route('attendances.index', ['date' => $date])
                ->with('success', 'Absensi untuk tanggal ' . $dateCarbon->isoFormat('D MMMM Y') . ' berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Error saving attendance: ' . $e->getMessage());
            
            return back()
                ->with('error', 'Gagal menyimpan absensi: ' . $e->getMessage())
                ->withInput();
        }
    }
}