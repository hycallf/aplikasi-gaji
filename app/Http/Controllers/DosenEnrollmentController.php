<?php

namespace App\Http\Controllers;

use App\Models\DosenMatkulEnrollment;
use App\Models\Employee;
use App\Models\Matkul;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\DB;

class DosenEnrollmentController extends Controller
{
    /**
     * Menampilkan daftar enrollment
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DosenMatkulEnrollment::with(['employee', 'matkul', 'academicYear']);

            // Filter berdasarkan tahun ajaran jika dipilih
            if ($request->filled('academic_year_id')) {
                $query->where('academic_year_id', $request->academic_year_id);
            } else {
                // Default: tampilkan tahun ajaran aktif
                $query->whereHas('academicYear', fn($q) => $q->where('is_active', true));
            }

            $data = $query->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('dosen_nama', fn($row) => $row->employee->nama_lengkap ?? 'N/A')
                ->addColumn('matkul_info', function($row) {
                    $nama = $row->matkul->nama_matkul ?? 'N/A';
                    $sks = $row->matkul->sks ?? 0;
                    $kelas = $row->kelas ? " (Kelas {$row->kelas})" : '';
                    return "{$nama} ({$sks} SKS){$kelas}";
                })
                ->addColumn('periode', fn($row) => $row->academicYear->nama_lengkap ?? 'N/A')
                ->addColumn('action', function($row){
                    $editButton = view('components.action-button', ['type' => 'edit', 'href' => route('enrollments.edit', $row->id)])->render();
                    $deleteButton = view('components.action-button', ['type' => 'delete', 'route' => route('enrollments.destroy', $row->id)])->render();
                    return '<div class="flex items-center justify-center">' . $editButton . $deleteButton . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        $academicYears = AcademicYear::orderBy('tanggal_mulai', 'desc')->get();
        return view('enrollments.index', compact('academicYears'));
    }

    /**
     * Menampilkan form untuk enrollment baru
     */
    public function create()
    {
        $dosens = Employee::where('tipe_karyawan', 'dosen')
                         ->where('status', 'aktif')
                         ->orderBy('nama')
                         ->get();
        $matkuls = Matkul::orderBy('nama_matkul')->get();
        $academicYear = AcademicYear::where('is_active', true)->first();

        if (!$academicYear) {
            return redirect()->route('enrollments.index')
                           ->with('error', 'Tidak ada tahun ajaran aktif. Silakan aktifkan tahun ajaran terlebih dahulu.');
        }

        return view('enrollments.create', compact('dosens', 'matkuls', 'academicYear'));
    }

    /**
     * Menyimpan enrollment baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'matkul_id' => 'required|exists:matkuls,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'kelas' => 'nullable|string|max:50',
            'jumlah_mahasiswa' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
        ]);

        // Validasi: Pastikan employee adalah dosen
        $employee = Employee::find($request->employee_id);
        if ($employee->tipe_karyawan !== 'dosen') {
            return back()->with('error', 'Employee yang dipilih bukan dosen.')
                        ->withInput();
        }

        try {
            DosenMatkulEnrollment::create($request->all());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan enrollment: ' . $e->getMessage())
                        ->withInput();
        }

        return redirect()->route('enrollments.index')
                        ->with('success', 'Enrollment berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit enrollment
     */
    public function edit(DosenMatkulEnrollment $enrollment)
    {
        $enrollment->load(['employee', 'matkul', 'academicYear']);
        $dosens = Employee::where('tipe_karyawan', 'dosen')
                         ->where('status', 'aktif')
                         ->orderBy('nama')
                         ->get();
        $matkuls = Matkul::orderBy('nama_matkul')->get();
        $academicYears = AcademicYear::orderBy('tanggal_mulai', 'desc')->get();

        return view('enrollments.edit', compact('enrollment', 'dosens', 'matkuls', 'academicYears'));
    }

    /**
     * Mengupdate enrollment
     */
    public function update(Request $request, DosenMatkulEnrollment $enrollment)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'matkul_id' => 'required|exists:matkuls,id',
            'academic_year_id' => 'required|exists:academic_years,id',
            'kelas' => 'nullable|string|max:50',
            'jumlah_mahasiswa' => 'nullable|integer|min:0',
            'catatan' => 'nullable|string',
        ]);

        try {
            $enrollment->update($request->all());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengupdate enrollment: ' . $e->getMessage())
                        ->withInput();
        }

        return redirect()->route('enrollments.index')
                        ->with('success', 'Enrollment berhasil diperbarui.');
    }

    /**
     * Menghapus enrollment
     */
    public function destroy(DosenMatkulEnrollment $enrollment)
    {
        // Proteksi: Cek apakah ada attendance terkait
        if ($enrollment->attendances()->exists()) {
            return redirect()->route('enrollments.index')
                           ->with('error', 'Tidak dapat menghapus enrollment yang sudah memiliki data absensi.');
        }

        $enrollment->delete();
        return redirect()->route('enrollments.index')
                        ->with('success', 'Enrollment berhasil dihapus.');
    }

    /**
     * AJAX: Mendapatkan daftar matkul yang sudah dienroll oleh dosen
     * untuk tahun ajaran tertentu (untuk mencegah duplikasi)
     */
    public function getEnrolledMatkuls(Request $request)
    {
        $employeeId = $request->input('employee_id');
        $academicYearId = $request->input('academic_year_id');

        $enrolledMatkuls = DosenMatkulEnrollment::where('employee_id', $employeeId)
                                                ->where('academic_year_id', $academicYearId)
                                                ->pluck('matkul_id');

        return response()->json($enrolledMatkuls);
    }
}
