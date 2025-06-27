<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserController extends Controller
{
    // Menampilkan halaman daftar semua user
    public function index()
    {
        $users = User::all(); // Cukup ambil semua user, kolom 'role' sudah ikut otomatis.
        return view('users.index', compact('users'));
    }

    // Menampilkan form untuk membuat user baru
    public function create()
    {
        // Ambil HANYA karyawan yang belum punya akun (user_id nya NULL)
        $employees = Employee::whereNull('user_id')->get();
        
        // Kirim data karyawan ke view
        return view('users.create', compact('employees'));
    }

    // Menyimpan user baru ke database
    public function store(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'employee_id' => 'required|exists:employees,id', // Pastikan karyawan yang dipilih ada
            'email' => 'required|string|email|max:255|unique:users',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 2. Cari data karyawan yang dipilih
        $employee = Employee::find($request->employee_id);

        // 3. Buat User baru
        $user = User::create([
            'name' => $employee->nama_lengkap, // Nama user diambil dari data karyawan
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $employee->tipe_karyawan, // Role diambil dari tipe karyawan
        ]);

        // 4. Hubungkan User baru ke data Karyawan
        $employee->user_id = $user->id;
        $employee->save();

        return redirect()->route('users.index')->with('success', 'Akun user berhasil dibuat dan dihubungkan.');
    }
}