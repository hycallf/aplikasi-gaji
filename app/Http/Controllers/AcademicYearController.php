<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Http\Request;
use DataTables;
use Carbon\Carbon;

class AcademicYearController extends Controller
{
    /**
     * Menampilkan daftar tahun ajaran
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = AcademicYear::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('periode', function($row) {
                    return $row->nama_lengkap;
                })
                ->editColumn('tanggal_mulai', fn($row) => $row->tanggal_mulai->format('d M Y'))
                ->editColumn('tanggal_selesai', fn($row) => $row->tanggal_selesai->format('d M Y'))
                ->addColumn('status', function($row) {
                    if ($row->is_active) {
                        return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>';
                    }
                    return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">Nonaktif</span>';
                })
                ->addColumn('action', function($row){
                    $buttons = '';

                    // Tombol Aktifkan/Nonaktifkan
                    if (!$row->is_active) {
                        $buttons .= '<button onclick="activateYear('.$row->id.')" class="inline-flex items-center gap-x-1.5 rounded-md bg-blue-100 px-2.5 py-1.5 text-sm font-semibold text-blue-700 hover:bg-blue-200 transition-colors mr-1">
                            <i class="fa-solid fa-check fa-fw"></i> Aktifkan
                        </button>';
                    }

                    $editButton = view('components.action-button', ['type' => 'edit', 'href' => route('academic-years.edit', $row->id)])->render();
                    $deleteButton = view('components.action-button', ['type' => 'delete', 'route' => route('academic-years.destroy', $row->id)])->render();

                    return '<div class="flex items-center justify-center">' . $buttons . $editButton . $deleteButton . '</div>';
                })
                ->rawColumns(['status', 'action'])
                ->make(true);
        }
        return view('academic_years.index');
    }

    /**
     * Menampilkan form untuk membuat tahun ajaran baru
     */
    public function create()
    {
        return view('academic_years.create');
    }

    /**
     * Menyimpan tahun ajaran baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:255',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'nullable|boolean',
        ]);

        $academicYear = AcademicYear::create($request->all());

        // Jika diset sebagai aktif, nonaktifkan yang lain
        if ($request->is_active) {
            $academicYear->activate();
        }

        return redirect()->route('academic-years.index')
                        ->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Menampilkan form untuk mengedit tahun ajaran
     */
    public function edit(AcademicYear $academicYear)
    {
        return view('academic_years.edit', compact('academicYear'));
    }

    /**
     * Mengupdate tahun ajaran
     */
    public function update(Request $request, AcademicYear $academicYear)
    {
        $request->validate([
            'nama_tahun_ajaran' => 'required|string|max:255',
            'semester' => 'required|in:ganjil,genap',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_active' => 'nullable|boolean',
        ]);

        $academicYear->update($request->all());

        // Jika diset sebagai aktif, nonaktifkan yang lain
        if ($request->is_active) {
            $academicYear->activate();
        }

        return redirect()->route('academic-years.index')
                        ->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Menghapus tahun ajaran
     */
    public function destroy(AcademicYear $academicYear)
    {
        // Proteksi: tidak bisa hapus tahun ajaran yang sedang aktif
        if ($academicYear->is_active) {
            return redirect()->route('academic-years.index')
                           ->with('error', 'Tidak dapat menghapus tahun ajaran yang sedang aktif.');
        }

        // Proteksi: tidak bisa hapus jika ada enrollment
        if ($academicYear->enrollments()->exists()) {
            return redirect()->route('academic-years.index')
                           ->with('error', 'Tidak dapat menghapus tahun ajaran yang memiliki data enrollment.');
        }

        $academicYear->delete();
        return redirect()->route('academic-years.index')
                        ->with('success', 'Tahun ajaran berhasil dihapus.');
    }

    /**
     * AJAX: Mengaktifkan tahun ajaran tertentu
     */
    public function activate(AcademicYear $academicYear)
    {
        $academicYear->activate();
        return response()->json(['success' => true, 'message' => 'Tahun ajaran berhasil diaktifkan.']);
    }
}
