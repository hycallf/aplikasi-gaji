<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CompanyProfile;

class CompanyProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        CompanyProfile::create([
        'nama_perusahaan' => 'STMIK Mercusuar',
        'alamat' => 'Jl. Raya Jatiwaringin No.144, RT.001/RW.008, Jatiwaringin, Kec. Pd. Gede, Kota Bks, Jawa Barat 17411',
        'email_kontak' => 'informasi@mercusuar.ac.id',
        'no_telepon' => '+628111341579',

        ]);
    }
}
