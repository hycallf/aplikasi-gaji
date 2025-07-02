<?php

namespace App\Http\Controllers;

use App\Models\DosenAttendance;
use App\Models\Employee;
use App\Models\Matkul;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DosenAttendanceController extends Controller
{
    /**
     * Menampilkan halaman form absensi dosen.
     */
    public function index(Request $request)
    {
        // Ambil semua dosen untuk dropdown
        $dosens = Employee::where('tipe_karyawan', 'dosen')->where('status', 'aktif')->orderBy('nama')->get();
        $selectedDosen = null;
        $matkuls = collect();
        $existingAttendances = collect();

        // Tentukan periode
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        // Jika ada dosen yang dipilih, ambil data matkul & absensinya
        if ($request->filled('employee_id')) {
            $selectedDosen = Employee::with('matkuls')->find($request->employee_id);
            if ($selectedDosen) {
                $matkuls = $selectedDosen->matkuls;

                $existingAttendances = DosenAttendance::where('employee_id', $selectedDosen->id)
                    ->where('periode_bulan', $selectedMonth)
                    ->where('periode_tahun', $selectedYear)
                    ->pluck('jumlah_pertemuan', 'matkul_id');
            }
        }

        return view('dosen_attendances.index', compact('dosens', 'selectedDosen', 'matkuls', 'selectedMonth', 'selectedYear', 'existingAttendances'));
    }

    /**
     * Menyimpan data rekap absensi dosen.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'pertemuan' => 'required|array',
            'pertemuan.*' => 'required|integer|min:0',
        ]);

        $employeeId = $request->employee_id;
        $month = $request->month;
        $year = $request->year;

        DB::beginTransaction();
        try {
            foreach ($request->pertemuan as $matkulId => $jumlahPertemuan) {
                DosenAttendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'matkul_id' => $matkulId,
                        'periode_bulan' => $month,
                        'periode_tahun' => $year,
                    ],
                    [
                        'jumlah_pertemuan' => $jumlahPertemuan,
                    ]
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data absensi: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('dosen.attendances.index', $request->only(['employee_id', 'month', 'year']))
                         ->with('success', 'Absensi dosen berhasil disimpan!');
    }
}
