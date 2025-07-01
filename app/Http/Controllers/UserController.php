<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use App\Notifications\UserInvitationNotification;

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
        $request->validate([
            'employee_id' => 'required|exists:employees,id,user_id,NULL',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'employee_id.exists' => 'Karyawan yang dipilih tidak valid atau sudah memiliki akun.']);

        try {
            DB::beginTransaction();
            
            $employee = Employee::find($request->employee_id);
            
            $user = User::create([
                'name' => $employee->nama,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => $employee->tipe_karyawan,
            ]);

            $user->notify(new UserInvitationNotification());

            $employee->user_id = $user->id;
            $employee->save();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat akun user: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('users.index')->with('success', 'Undangan untuk mengatur akun telah dikirim ke karyawan.');
    }

    public function resendInvitation(User $user)
    {
        // Proteksi: hanya kirim ulang jika email belum diverifikasi dan bukan operator
        if ($user->hasVerifiedEmail() || $user->role === 'operator') {
            return back()->with('error', 'Tidak dapat mengirim ulang undangan untuk user ini.');
        }

        try {
            $user->notify(new UserInvitationNotification());
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengirim email undangan: ' . $e->getMessage());
        }
        
        return back()->with('success', 'Email undangan berhasil dikirim ulang ke ' . $user->email);
    }
    /**
     * DILENGKAPI: Menampilkan form untuk mengedit user.
     */
    public function edit(User $user)
    {
        // Proteksi: jangan izinkan edit user dengan role 'operator'
        if ($user->role === 'operator') {
            return redirect()->route('users.index')->with('error', 'Akun Operator tidak bisa diedit.');
        }
        return view('users.edit', compact('user'));
    }

    /**
     * DILENGKAPI: Mengupdate data user.
     */
    public function update(Request $request, User $user)
    {
        // Proteksi: jangan izinkan update user dengan role 'operator'
        if ($user->role === 'operator') {
            return redirect()->route('users.index')->with('error', 'Akun Operator tidak bisa diupdate.');
        }

        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);
        
        // Update nama dan email
        $user->name = $request->name;
        $user->email = $request->email;

        // Hanya update password jika diisi
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Akun user berhasil diperbarui.');
    }

    /**
     * DILENGKAPI: Menghapus akun user.
     */
    public function destroy(User $user)
    {
        // Proteksi: jangan izinkan hapus user dengan role 'operator'
        if ($user->role === 'operator') {
            return redirect()->route('users.index')->with('error', 'Akun Operator tidak bisa dihapus.');
        }

        try {
            DB::beginTransaction();

            // Cari employee yang terhubung dengan user ini
            $employee = Employee::where('user_id', $user->id)->first();

            // Jika ada, putuskan hubungannya dengan mengosongkan user_id
            if ($employee) {
                $employee->user_id = null;
                $employee->save();
            }

            // Hapus user
            $user->delete();
            
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus akun user: ' . $e->getMessage());
        }

        return redirect()->route('users.index')->with('success', 'Akun user berhasil dihapus.');
    }
}