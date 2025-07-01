<?php

namespace App\Http\Controllers;

use App\Models\CompanyProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyProfileController extends Controller
{
    /**
     * Menampilkan form untuk mengedit profil perusahaan.
     */
    public function edit()
    {
        // Ambil data profil pertama yang ada, atau buat baru jika tidak ada.
        $profile = CompanyProfile::firstOrCreate(['id' => 1]);
        
        return view('company_profile.edit', compact('profile'));
    }

    /**
     * Mengupdate profil perusahaan di database.
     */
    public function update(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_perusahaan' => 'required|string|max:255',
            'nama_perwakilan' => 'nullable|string|max:255',
            'alamat' => 'nullable|string',
            'email_kontak' => 'nullable|email',
            'no_telepon' => 'nullable|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);
        
        // Cari profil perusahaan, atau buat baru jika tidak ada
        $profile = CompanyProfile::firstOrCreate(['id' => 1]);
        $dataToUpdate = $request->except('logo');

        // Proses upload logo baru jika ada
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($profile->logo) {
                Storage::disk('public')->delete($profile->logo);
            }
            // Simpan logo baru dengan nama unik
            $file = $request->file('logo');
            $fileName = 'logo-' . time() . '.' . $file->getClientOriginalExtension();
            $dataToUpdate['logo'] = $file->storeAs('company', $fileName, 'public');
        }

        // Update data di database
        $profile->update($dataToUpdate);

        return redirect()->route('company.profile.edit')->with('success', 'Profil perusahaan berhasil diperbarui.');
    }
}