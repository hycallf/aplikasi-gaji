<?php

namespace App\Http\Controllers;

use App\Models\Matkul;
use Illuminate\Http\Request;
use DataTables;

class MatkulController extends Controller
{
    /**
     * Menampilkan daftar mata kuliah.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Matkul::latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function($row){
                    $editButton = view('components.action-button', ['type' => 'edit', 'href' => route('matkuls.edit', $row->id)])->render();
                    $deleteButton = view('components.action-button', ['type' => 'delete', 'route' => route('matkuls.destroy', $row->id)])->render();
                    return '<div class="flex items-center justify-center">' . $editButton . $deleteButton . '</div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('matkuls.index');
    }

    /**
     * Menampilkan form untuk membuat mata kuliah baru.
     */
    public function create()
    {
        return view('matkuls.create');
    }

    /**
     * Menyimpan mata kuliah baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_matkul' => 'required|string|max:255|unique:matkuls,nama_matkul',
            'sks' => 'required|integer|min:1',
        ]);

        Matkul::create($request->all());

        return redirect()->route('matkuls.index')->with('success', 'Mata kuliah baru berhasil dibuat.');
    }

    /**
     * Menampilkan form untuk mengedit mata kuliah.
     */
    public function edit(Matkul $matkul)
    {
        return view('matkuls.edit', compact('matkul'));
    }

    /**
     * Mengupdate mata kuliah di database.
     */
    public function update(Request $request, Matkul $matkul)
    {
        $request->validate([
            'nama_matkul' => 'required|string|max:255|unique:matkuls,nama_matkul,' . $matkul->id,
            'sks' => 'required|integer|min:1',
        ]);

        $matkul->update($request->all());

        return redirect()->route('matkuls.index')->with('success', 'Mata kuliah berhasil diperbarui.');
    }

    /**
     * Menghapus mata kuliah dari database.
     */
    public function destroy(Matkul $matkul)
    {
        // Proteksi agar tidak bisa dihapus jika sudah diajar oleh dosen
        if ($matkul->employees()->exists()) {
            return redirect()->route('matkuls.index')->with('error', 'Mata kuliah ini tidak bisa dihapus karena sedang diajar oleh dosen.');
        }

        $matkul->delete();
        return redirect()->route('matkuls.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
