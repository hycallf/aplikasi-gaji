<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Deduction;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    // app/Http/Controllers/AttendanceController.php

    // Menampilkan halaman utama dengan semua data yang dibutuhkan
    public function index(Request $request)
    {
        $selectedDate = Carbon::parse($request->input('date', Carbon::today()));
        $searchName = $request->input('search_name');

        // Data untuk Kalender Navigasi
        $totalActiveEmployees = Employee::where('status', 'aktif')->count();
        $attendanceSummary = Attendance::whereYear('date', $selectedDate->year)->whereMonth('date', $selectedDate->month)
            ->select('date', DB::raw('count(*) as total_submitted'))->groupBy('date')
            ->get()->keyBy(fn($item) => Carbon::parse($item->date)->day);
        
        $completedDates = [];
        foreach($attendanceSummary as $day => $summary) {
            if ($summary->total_submitted >= $totalActiveEmployees) {
                $completedDates[] = $day;
            }
        }

        // Data untuk Form Input
        $employees = $this->getFilteredEmployees($request);
        $attendances = Attendance::whereDate('date', $selectedDate->toDateString())->pluck('status', 'employee_id')->all();
        $descriptions = Attendance::whereDate('date', $selectedDate->toDateString())->pluck('description', 'employee_id')->all();

        return view('attendances.index', compact('employees', 'selectedDate', 'attendances', 'descriptions', 'completedDates', 'searchName'));
    }

    // Method baru untuk melayani request pencarian dari HTMX
    public function search(Request $request)
    {
        $selectedDate = Carbon::parse($request->input('date', Carbon::today()));
        $employees = $this->getFilteredEmployees($request);
        $attendances = Attendance::whereDate('date', $selectedDate->toDateString())->pluck('status', 'employee_id')->all();
        $descriptions = Attendance::whereDate('date', $selectedDate->toDateString())->pluck('description', 'employee_id')->all();
        
        // Kembalikan HANYA view partial baris tabelnya
        return view('attendances._employee_rows', compact('employees', 'attendances', 'descriptions'));
    }
    
    // Method private untuk mengambil data karyawan yang difilter
    private function getFilteredEmployees(Request $request)
    {
        $query = Employee::where('status', 'aktif')->orderBy('nama');
        if ($request->filled('search_name')) {
            $query->where('nama', 'like', '%' . $request->search_name . '%');
        }
        return $query->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|before_or_equal:today',
            'attendances' => 'required|array',
        ], [
            // Tambahkan pesan error kustom agar lebih jelas
            'date.before_or_equal' => 'Anda tidak bisa mengisi absensi untuk tanggal di masa depan.'
        ]);

        $date = $request->input('date');

        foreach ($request->attendances as $employeeId => $status) {
            $description = $request->descriptions[$employeeId] ?? null;

            // Gunakan updateOrCreate untuk membuat record baru atau mengupdate jika sudah ada
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

            if ($status == 'pulang_awal') {
                // Buat atau update potongan untuk hari itu jika statusnya pulang awal
                Deduction::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'tanggal_potongan' => $date,
                        'sumber' => 'absensi', // Tandai sebagai potongan dari absensi
                    ],
                    [
                        'jenis_potongan' => 'Transport',
                        'jumlah_potongan' => 10000, // Nominal potongan 10k
                        'keterangan' => 'Potongan transport karena pulang awal',
                    ]
                );
            } else {
                // Jika statusnya BUKAN pulang awal, hapus potongan otomatis yang mungkin ada
                Deduction::where('employee_id', $employeeId)
                    ->where('tanggal_potongan', $date)
                    ->where('sumber', 'absensi')
                    ->delete();
            }
        }

        return redirect()->route('attendances.index', ['date' => $date])->with('success', 'Absensi untuk tanggal ' . $date . ' berhasil disimpan!');
    }
}
