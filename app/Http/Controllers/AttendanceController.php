<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Deduction;
use Illuminate\Http\Request;
use Carbon\Carbon;


class AttendanceController extends Controller
{
    // app/Http/Controllers/AttendanceController.php

    public function index(Request $request)
    {
        // Tetap kirimkan data untuk tampilan awal
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        return view('attendances.index', ['selectedDate' => $selectedDate]);
    }

    // METHOD BARU UNTUK HANDLE PENCARIAN ASYNCHRONOUS
    public function search(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        $searchName = $request->input('search_name');

        $employeesQuery = Employee::where('status', 'aktif');
        if ($searchName) {
            $employeesQuery->where('nama', 'like', '%' . $searchName . '%');
        }
        $employees = $employeesQuery->get();

        $attendances = Attendance::where('date', $selectedDate)
                                ->whereIn('employee_id', $employees->pluck('id'))
                                ->pluck('status', 'employee_id')
                                ->all();
        
        $descriptions = Attendance::where('date', $selectedDate)
                                ->whereIn('employee_id', $employees->pluck('id'))
                                ->pluck('description', 'employee_id')
                                ->all();

        // Kembalikan HANYA view partial baris tabelnya, bukan layout lengkap
        return view('attendances._employee_rows', compact('employees', 'attendances', 'descriptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'attendances' => 'required|array',
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
