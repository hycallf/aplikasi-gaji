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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Cek jika request datang dari datatables
        if ($request->ajax()) {
            $data = Employee::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn() // Menambahkan kolom nomor urut
                ->editColumn('status', function($row){
                    // Mengubah tampilan kolom status menjadi badge
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
                    // Menggunakan helper view() untuk me-render komponen Blade
                    $showUrl = route('employees.show', $row->id);
                    $showButton = '<button
                        type="button"
                        @click.prevent="$dispatch(\'open-employee-detail\', { employeeData: await (await fetch(\''.$showUrl.'\')).json() })"
                        class="inline-flex items-center gap-x-1.5 rounded-md bg-green-100 dark:bg-green-900/50 px-2.5 py-1.5 text-sm font-semibold text-green-700 dark:text-green-300 shadow-sm hover:bg-green-200 dark:hover:bg-green-800 transition-colors">
                            <i class="fa-solid fa-eye fa-fw"></i>
                            <span></span>
                  </button>';

                    $editButton = view('components.action-button', [
                        'type' => 'edit',
                        'href' => route('employees.edit', $row->id)
                    ])->render();

                    $deleteButton = view('components.action-button', [
                        'type' => 'delete',
                        'route' => route('employees.destroy', $row->id)
                    ])->render();

                    // Gabungkan kedua tombol dalam satu div untuk penataan
                    return '<div class="flex items-center justify-center space-x-2">' . $showButton . $editButton . $deleteButton . '</div>';
                })
                ->rawColumns(['action', 'status']) // Kolom yang isinya HTML
                ->make(true);
        }

        return view('employees.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255', // DIUBAH: menjadi 'nama'
            'jabatan' => 'required|string|max:255',
            'tipe_karyawan' => 'required|in:karyawan,dosen',
            'gaji_pokok' => 'required|numeric', // DIUBAH: menjadi 'gaji_pokok'
            'transport' => 'required|numeric',
            'tunjangan' => 'required|numeric',
            'departemen' => 'required|string|max:255',
            'tanggal_masuk' => 'nullable|date',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'pendidikan_terakhir' => 'nullable|string',
            'jurusan' => 'nullable|string|max:255',
            'domisili' => 'nullable|string|max:255',
            // Tambahkan validasi lain sesuai kebutuhan...
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat data utama di tabel 'employees'
            $employee = Employee::create([
                'nama' => $request->nama,
                'jabatan' => $request->jabatan,
                'tipe_karyawan' => $request->tipe_karyawan,
                'gaji_pokok' => $request->gaji_pokok,
                'transport' => $request->transport,
                'tunjangan' => $request->tunjangan,
                'departemen' => $request->departemen,
            ]);

            $pathFoto = null;
            if ($request->hasFile('foto')) {
                // --- BAGIAN YANG DIUBAH ---
                $file = $request->file('foto');
                // Membuat nama file: udin-sedunia-16893452.jpg
                $namaFile = Str::slug($request->nama) . '-' . time() . '.' . $file->getClientOriginalExtension();
                // Simpan file dengan nama baru
                $pathFoto = $file->storeAs('fotos', $namaFile, 'public');
                // --- AKHIR PERUBAHAN ---
            }

            // 2. Buat data detail di tabel 'employee_details'
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

        return redirect()->route('employees.index')->with('success', 'Karyawan baru berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        // Eager load relasi detail dan user
        $employee->load('detail', 'user');
        return response()->json($employee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $employee->load('detail');
        $matkuls = Matkul::orderBy('nama_matkul')->get();
        return view('employees.edit', compact('employee','matkuls'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee) // DIUBAH: Menggunakan Route Model Binding
    {
        $request->validate([
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
        ]);
        // Dengan Route Model Binding, $employee sudah berisi data yang benar
        try {
            DB::beginTransaction();

            $employeeData = $request->only(['nama', 'jabatan','departemen', 'tipe_karyawan', 'gaji_pokok', 'transport','tunjangan', 'status']);
            $detailData = $request->only(['tanggal_masuk', 'alamat', 'domisili', 'no_hp', 'status_pernikahan', 'jumlah_anak', 'riwayat_pendidikan']);

            // 4. Proses upload foto baru jika ada
            if ($request->hasFile('foto')) {
                // Hapus foto lama dari storage jika ada
                if ($employee->detail && $employee->detail->foto) {
                    Storage::disk('public')->delete($employee->detail->foto);
                }

                // Buat nama file baru dan simpan
                $file = $request->file('foto');
                $namaFile = Str::slug($request->nama) . '-' . time() . '.' . $file->getClientOriginalExtension();
                $pathFoto = $file->storeAs('fotos', $namaFile, 'public');

                // Tambahkan path foto baru ke data detail
                $detailData['foto'] = $pathFoto;
            }

            // 5. Update data di tabel 'employees'
            $employee->update($employeeData);

            // 6. Update atau buat data di 'employee_details'
            $employee->detail()->updateOrCreate(
                ['employee_id' => $employee->id], // Cari berdasarkan ini
                $detailData  // Dan update/buat dengan data ini
            );

            if ($request->tipe_karyawan === 'dosen' && $request->has('matkuls')) {
                // sync() akan otomatis mengatur relasi di tabel employee_matkul
                $employee->matkuls()->sync($request->input('matkuls', []));
            } else {
                // Jika bukan dosen, hapus semua relasi matkulnya
                $employee->matkuls()->sync([]);
            }

            // 7. Update data user yang terhubung (jika ada) agar tetap sinkron
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

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        try {
            DB::beginTransaction();

            // Hapus user yang terhubung terlebih dahulu
            // Ini akan gagal jika ada relasi lain yang melindungi user,
            // tapi untuk kasus ini seharusnya aman.
            if ($employee->user) {
                $employee->user->delete();
            }

            // Hapus data employee
            $employee->delete();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }

        return redirect()->route('employees.index')->with('success', 'Data karyawan berhasil dihapus.');
    }
}
