<?php

namespace App\View\Composers;

use App\Models\CompanyProfile;
use Illuminate\View\View;

class CompanyProfileComposer
{
    /**
     * Mengikat data ke view.
     */
    public function compose(View $view): void
    {
        // Ambil data profil perusahaan pertama yang ada di database.
        // Gunakan cache nanti jika perlu untuk performa.
        $companyProfile = CompanyProfile::first();

        // Kirim variabel $companyProfile ke semua view yang terdaftar.
        $view->with('companyProfile', $companyProfile);
    }
}