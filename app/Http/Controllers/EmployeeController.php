<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\EmployeeDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use App\Models\Matkul;
use DataTables;
use Carbon\Carbon;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Employee::query();

            // Filter berdasarkan tipe jika ada
            if ($request->filled('tipe')) {
                $query->where('tipe_karyawan', $request->tipe);
            }

            $data = $query->latest()->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_lengkap', function($row) {
                    return $row->nama_lengkap; // Menggunakan accessor dari model
                })
                ->editColumn('status', function($row){
                    if ($row->status == 'aktif') {
                        return '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Aktif</span>';
                    } else {
                        $badge = '<span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 mb-1">Nonaktif</span>';
                        if($row->latestInactivityLog) {
                            $badge .= '<div class="text-xs text-gray-500 mt-1">('.$row->latestInactivityLog->tipe.')</div>';
                        }
                        return $badge;
                    }
                })
                ->addColumn('action', function($row){
                    $showUrl = route('employees.show', $row->id);
                    $showButton = '<button
                        type="button"
                        @click.prevent="$dispatch(\'open-employee-detail\', { employeeData: await (await fetch(\''.$showUrl.'\')).json() })"
                        class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 px-2.5 py-1.5 text-sm font-semibold text-green-700 shadow-sm hover:bg-green-200 transition-colors">
                            <i class="fa-solid fa-eye fa-fw"></i>
                      </button>';

                    $editButton = view('components.action-button', [
                        'type' => 'edit',
                        'href' => route('employees.edit', $row->id)
                    ])->render();

                    $deleteButton = view('components.action-button', [
                        'type' => 'delete',
                        'route' => route('employees.destroy', $row->id)
                    ])->render();

                    return '<div class="flex items-center justify-center space-x-2">' . $showButton . $editButton . $deleteButton . '</div>';
                })
                ->rawColumns(['action', 'status'])
                ->make(true);
        }

        return view('employees.index');
    }

    public function create(Request $request)
    {
        $tipe = $request->get('tipe', 'karyawan');
        return view('employees.create', compact('tipe'));
    }

    public function store(Request $request)
    {
        $rules = [
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'tipe_karyawan' => 'required|in:karyawan,dosen',
            'gaji_pokok' => 'required|numeric',
            'transport' => 'required|numeric',
            'tunjangan' => 'required|numeric',
            'departemen' => 'required|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pendidikan_terakhir' => 'nullable|string',
            'jurusan' => 'nullable|string|max:255',
            'domisili' => 'nullable|string|max:255',
        ];

        // Tambahan validasi khusus dosen
        if ($request->tipe_karyawan === 'dosen') {
            $rules['nidn'] = 'nullable|string|max:50|unique:employees,nidn';
            $rules['gelar_depan'] = 'nullable|string|max:50';
            $rules['gelar_belakang'] = 'nullable|string|max:100';
            $rules['status_dosen'] = 'nullable|in:tetap,honorer,luar_biasa';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $employeeData = $request->only([
                'nama', 'jabatan', 'tipe_karyawan', 'gaji_pokok',
                'transport', 'tunjangan', 'departemen'
            ]);

            // Tambahkan data khusus dosen jika ada
            if ($request->tipe_karyawan === 'dosen') {
                $employeeData['nidn'] = $request->nidn;
                $employeeData['gelar_depan'] = $request->gelar_depan;
                $employeeData['gelar_belakang'] = $request->gelar_belakang;
                $employeeData['status_dosen'] = $request->status_dosen;
            }

            $employee = Employee::create($employeeData);

            $pathFoto = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $namaFile = Str::slug($request->nama) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $pathFoto = $file->storeAs('fotos', $namaFile, 'public');
            }

            EmployeeDetail::create([
                'foto' => $pathFoto,
                'employee_id' => $employee->id,
                'tanggal_masuk' => $request->tanggal_masuk,
                'alamat' => $request->alamat,
                'domisili' => $request->domisili,
                'no_hp' => $request->no_hp,
                'status_pernikahan' => $request->status_pernikahan,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'jurusan' => $request->jurusan,
                'jumlah_anak' => $request->jumlah_anak,
            ]);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('employees.index')->with('success', ($request->tipe_karyawan === 'dosen' ? 'Dosen' : 'Karyawan') . ' baru berhasil ditambahkan!');
    }

    public function show(Employee $employee)
    {
        $employee->load('detail', 'user');
        return response()->json($employee);
    }

    public function edit(Employee $employee)
    {
        $employee->load('detail');

        // Jika dosen, ambil enrollments aktif (bukan matkuls lagi)
        $enrollments = collect();
        if ($employee->isDosen()) {
            $enrollments = $employee->enrollments()
                ->with(['matkul', 'academicYear'])
                ->whereHas('academicYear', fn($q) => $q->where('is_active', true))
                ->get();
        }

        return view('employees.edit', compact('employee', 'enrollments'));
    }

    public function update(Request $request, Employee $employee)
    {
        $rules = [
            'nama' => ['required', 'string', 'max:255'],
            'jabatan' => ['required', 'string', 'max:255'],
            'tipe_karyawan' => ['required', 'in:karyawan,dosen'],
            'tanggal_masuk' => ['nullable', 'date'],
            'departemen' => ['required','string','max:255'],
            'gaji_pokok' => ['required', 'numeric'],
            'transport' => ['required', 'numeric'],
            'tunjangan' => ['required', 'numeric'],
            'alamat' => ['nullable', 'string'],
            'domisili' => ['nullable', 'string'],
            'no_hp' => ['nullable', 'string'],
            'status_pernikahan' => ['nullable', 'in:Lajang,Menikah,Cerai'],
            'jumlah_anak' => ['nullable', 'integer'],
            'riwayat_pendidikan' => ['nullable', 'string'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ];

        // Validasi khusus dosen
        if ($request->tipe_karyawan === 'dosen') {
            $rules['nidn'] = 'nullable|string|max:50|unique:employees,nidn,' . $employee->id;
            $rules['gelar_depan'] = 'nullable|string|max:50';
            $rules['gelar_belakang'] = 'nullable|string|max:100';
            $rules['status_dosen'] = 'nullable|in:tetap,honorer,luar_biasa';
        }

        $request->validate($rules);

        try {
            DB::beginTransaction();

            $employeeData = $request->only([
                'nama', 'jabatan', 'departemen', 'tipe_karyawan',
                'gaji_pokok', 'transport', 'tunjangan', 'status'
            ]);

            // Data khusus dosen
            if ($request->tipe_karyawan === 'dosen') {
                $employeeData['nidn'] = $request->nidn;
                $employeeData['gelar_depan'] = $request->gelar_depan;
                $employeeData['gelar_belakang'] = $request->gelar_belakang;
                $employeeData['status_dosen'] = $request->status_dosen;
            } else {
                // Reset data dosen jika diubah jadi karyawan
                $employeeData['nidn'] = null;
                $employeeData['gelar_depan'] = null;
                $employeeData['gelar_belakang'] = null;
                $employeeData['status_dosen'] = null;
            }

            $detailData = $request->only([
                'tanggal_masuk', 'alamat', 'domisili', 'no_hp',
                'status_pernikahan', 'jumlah_anak', 'riwayat_pendidikan'
            ]);

            if ($request->hasFile('foto')) {
                if ($employee->detail && $employee->detail->foto) {
                    Storage::disk('public')->delete($employee->detail->foto);
                }

                $file = $request->file('foto');
                $namaFile = Str::slug($request->nama) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $pathFoto = $file->storeAs('fotos', $namaFile, 'public');
                $detailData['foto'] = $pathFoto;
            }

            $employee->update($employeeData);
            $employee->detail()->updateOrCreate(
                ['employee_id' => $employee->id],
                $detailData
            );

            // Update user terkait
            if ($employee->user) {
                $employee->user->update([
                    'name' => $request->nama,
                    'role' => $request->tipe_karyawan
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('employees.index')->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy(Employee $employee)
    {
        try {
            DB::beginTransaction();

            if ($employee->user) {
                $employee->user->delete();
            }

            $employee->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }

        return redirect()->route('employees.index')->with('success', 'Data berhasil dihapus.');
    }
}
