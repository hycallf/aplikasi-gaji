<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Overtime;
use Illuminate\Http\Request;
use Carbon\Carbon;

class OvertimeController extends Controller
{
    // Method index sekarang hanya menampilkan halaman shell utama
    public function index(Request $request)
    {
        $selectedDate = $request->input('date', Carbon::today()->toDateString());
        return view('overtimes.index', ['selectedDate' => $selectedDate]);
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

        $overtimes = Overtime::where('tanggal_lembur', $selectedDate)
                                ->whereIn('employee_id', $employees->pluck('id'))
                                ->get()
                                ->keyBy('employee_id');

        // Kembalikan HANYA view partial baris tabelnya
        return view('overtimes._employee_rows', compact('employees', 'overtimes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'overtimes' => 'sometimes|array', // 'overtimes' tidak wajib ada
        ]);

        $date = $request->input('date');
        $overtimeData = $request->input('overtimes', []);
        $allEmployeeIds = Employee::where('status', 'aktif')->pluck('id');

        foreach ($allEmployeeIds as $employeeId) {
            // Cek apakah karyawan ini ditandai lembur (checkbox dicentang)
            if (isset($overtimeData[$employeeId]['checked'])) {
                // Jika lembur, buat atau update record
                Overtime::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'tanggal_lembur' => $date,
                    ],
                    [
                        'deskripsi_lembur' => $overtimeData[$employeeId]['deskripsi_lembur'] ?? 'Lembur',
                        'upah_lembur' => $overtimeData[$employeeId]['upah_lembur'] ?? 0,
                    ]
                );
            } else {
                // Jika tidak lembur, cari dan hapus record lembur yang mungkin sudah ada
                Overtime::where('employee_id', $employeeId)
                        ->where('tanggal_lembur', $date)
                        ->delete();
            }
        }

        return redirect()->route('overtimes.index', ['date' => $date])->with('success', 'Data lembur untuk tanggal ' . $date . ' berhasil disimpan!');
    }
}
