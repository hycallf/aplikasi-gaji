<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Deduction;
use App\Models\Employee;
use DataTables;
// ...

class DeductionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Deduction::with('employee')->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('nama_karyawan', function($row){
                    return $row->employee->nama;
                })
                ->editColumn('jumlah_potongan', function($row){
                    return 'Rp ' . number_format($row->jumlah_potongan, 0, ',', '.');
                })
                ->addColumn('action', function($row){
                    // Tombol Aksi HANYA muncul jika sumbernya 'manual'
                    if ($row->sumber == 'manual') {
                        $editButton = view('components.action-button', ['type' => 'edit', 'href' => route('deductions.edit', $row->id)])->render();
                        $deleteButton = view('components.action-button', ['type' => 'delete', 'route' => route('deductions.destroy', $row->id)])->render();
                        return '<div class="flex items-center justify-center">' . $editButton . $deleteButton . '</div>';
                    }
                    return '<span class="text-xs text-gray-500 italic">Otomatis</span>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('deductions.index');
    }

    public function create()
    {
        $employees = Employee::where('status', 'aktif')->get();
        return view('deductions.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal_potongan' => 'required|date',
            'jenis_potongan' => 'required|string|max:255',
            'jumlah_potongan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);
        
        Deduction::create($request->all()); // 'sumber' otomatis 'manual' (default)

        return redirect()->route('deductions.index')->with('success', 'Potongan baru berhasil ditambahkan.');
    }

    public function edit(Deduction $deduction)
    {
        // Tolak edit jika potongan berasal dari absensi
        if ($deduction->sumber !== 'manual') {
            return redirect()->route('deductions.index')->with('error', 'Potongan otomatis dari absensi tidak bisa diedit.');
        }

        $employees = Employee::where('status', 'aktif')->get();
        return view('deductions.edit', compact('deduction', 'employees'));
    }

    public function update(Request $request, Deduction $deduction)
    {
        // Tolak update jika potongan berasal dari absensi
        if ($deduction->sumber !== 'manual') {
            return redirect()->route('deductions.index')->with('error', 'Potongan otomatis dari absensi tidak bisa diupdate.');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'tanggal_potongan' => 'required|date',
            'jenis_potongan' => 'required|string|max:255',
            'jumlah_potongan' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);
        
        $deduction->update($request->all());

        return redirect()->route('deductions.index')->with('success', 'Data potongan berhasil diperbarui.');
    }

    public function destroy(Deduction $deduction)
    {
        // Tolak hapus jika potongan berasal dari absensi
        if ($deduction->sumber !== 'manual') {
             return redirect()->route('deductions.index')->with('error', 'Potongan otomatis dari absensi tidak bisa dihapus.');
        }
        
        $deduction->delete();
        return redirect()->route('deductions.index')->with('success', 'Data potongan berhasil dihapus.');
    }
    // Buat juga method edit, update, dan destroy untuk CRUD lengkap...
}