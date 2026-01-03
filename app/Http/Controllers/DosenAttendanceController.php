<?php

namespace App\Http\Controllers;

use App\Models\DosenAttendance;
use App\Models\Employee;
use App\Models\AcademicYear;
use App\Models\DosenMatkulEnrollment;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DosenAttendanceController extends Controller
{
    /**
     * Menampilkan halaman form absensi dosen (versi baru dengan enrollment).
     */
    public function index(Request $request)
    {
        // Ambil tahun ajaran aktif
        $activeAcademicYear = AcademicYear::where('is_active', true)->first();

        if (!$activeAcademicYear) {
            return view('dosen_attendances.index', [
                'error' => 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.',
                'dosens' => collect(),
                'enrollments' => collect(),
                'existingAttendances' => collect(),
                'activeAcademicYear' => null,
                'selectedDosen' => null,
                'selectedMonth' => Carbon::now()->month,
                'selectedYear' => Carbon::now()->year,
            ]);
        }

        // Ambil semua dosen untuk dropdown
        $dosens = Employee::where('tipe_karyawan', 'dosen')
                         ->where('status', 'aktif')
                         ->orderBy('nama')
                         ->get();

        $selectedDosen = null;
        $enrollments = collect();
        $existingAttendances = collect();

        // Tentukan periode (bulan dan tahun)
        $selectedMonth = $request->input('month', Carbon::now()->month);
        $selectedYear = $request->input('year', Carbon::now()->year);

        // Jika ada dosen yang dipilih
        if ($request->filled('employee_id')) {
            $selectedDosen = Employee::find($request->employee_id);

            if ($selectedDosen) {
                // Ambil enrollment dosen untuk tahun ajaran aktif
                $enrollments = DosenMatkulEnrollment::with(['matkul', 'academicYear'])
                    ->where('employee_id', $selectedDosen->id)
                    ->where('academic_year_id', $activeAcademicYear->id)
                    ->get();

                // Ambil data absensi yang sudah ada
                $existingAttendances = DosenAttendance::where('employee_id', $selectedDosen->id)
                    ->where('periode_bulan', $selectedMonth)
                    ->where('periode_tahun', $selectedYear)
                    ->get()
                    ->keyBy('enrollment_id'); // Key by enrollment_id
            }
        }

        return view('dosen_attendances.index', compact(
            'dosens',
            'selectedDosen',
            'enrollments',
            'activeAcademicYear',
            'selectedMonth',
            'selectedYear',
            'existingAttendances'
        ));
    }

    /**
     * FIXED: Menyimpan data rekap absensi dosen
     */
    public function store(Request $request)
    {
        // Validasi input
        $validated = $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'month' => 'required|integer|between:1,12',
            'year' => 'required|integer|min:2000',
            'pertemuan' => 'required|array',
            'pertemuan.*' => 'required|integer|min:0',
        ]);

        $employeeId = $validated['employee_id'];
        $month = $validated['month'];
        $year = $validated['year'];

        // Validasi: pastikan employee adalah dosen
        $employee = Employee::find($employeeId);
        if (!$employee || $employee->tipe_karyawan !== 'dosen') {
            return back()->with('error', 'Employee yang dipilih bukan dosen.')->withInput();
        }

        DB::beginTransaction();
        try {
            foreach ($validated['pertemuan'] as $enrollmentId => $jumlahPertemuan) {
                // Validasi: Pastikan enrollment exists dan milik dosen ini
                $enrollment = DosenMatkulEnrollment::with('academicYear')
                                                   ->where('id', $enrollmentId)
                                                   ->where('employee_id', $employeeId)
                                                   ->first();

                if (!$enrollment) {
                    throw new \Exception("Enrollment ID {$enrollmentId} tidak valid atau bukan milik dosen ini.");
                }

                // Simpan atau update attendance
                DosenAttendance::updateOrCreate(
                    [
                        'employee_id' => $employeeId,
                        'enrollment_id' => $enrollmentId,
                        'periode_bulan' => $month,
                        'periode_tahun' => $year,
                    ],
                    [
                        'matkul_id' => $enrollment->matkul_id,
                        'academic_year_id' => $enrollment->academic_year_id, // DITAMBAHKAN
                        'jumlah_pertemuan' => $jumlahPertemuan,
                        'kelas' => $enrollment->kelas,
                    ]
                );
            }

            DB::commit();

            return redirect()->route('dosen.attendances.index', [
                'employee_id' => $employeeId,
                'month' => $month,
                'year' => $year
            ])->with('success', 'Kehadiran dosen berhasil disimpan!');

        } catch (\Exception $e) {
            DB::rollBack();

            // Log error untuk debugging
            \Log::error('Error saving dosen attendance: ' . $e->getMessage(), [
                'employee_id' => $employeeId,
                'month' => $month,
                'year' => $year,
                'trace' => $e->getTraceAsString()
            ]);

            return back()
                ->with('error', 'Gagal menyimpan data kehadiran: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Method untuk melihat history absensi dosen per enrollment
     */
    public function history(Request $request, $enrollmentId)
    {
        $enrollment = DosenMatkulEnrollment::with(['employee', 'matkul', 'academicYear'])
                                          ->findOrFail($enrollmentId);

        $attendances = DosenAttendance::where('enrollment_id', $enrollmentId)
                                     ->orderBy('periode_tahun', 'desc')
                                     ->orderBy('periode_bulan', 'desc')
                                     ->get();

        return view('dosen_attendances.history', compact('enrollment', 'attendances'));
    }
}
