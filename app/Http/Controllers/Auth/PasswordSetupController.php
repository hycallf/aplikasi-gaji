<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class PasswordSetupController extends Controller
{
    // Method untuk menampilkan form
    public function showSetupForm(Request $request, User $user)
    {
        // Pastikan URL yang diakses valid dan ditandatangani oleh Laravel
        if (! $request->hasValidSignature()) {
            abort(401);
        }
        return view('auth.setup-password', ['request' => $request, 'user' => $user]);
    }

    // Method untuk menyimpan password baru
    public function submitSetupForm(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
        
        $user = User::where('email', $request->email)->firstOrFail();
        
        $user->password = Hash::make($request->password);
        $user->email_verified_at = now(); // Tandai email sebagai terverifikasi
        $user->save();

        return redirect()->route('login')->with('status', 'Password Anda berhasil diatur! Silakan login.');
    }
}