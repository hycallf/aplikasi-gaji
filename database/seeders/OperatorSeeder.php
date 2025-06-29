<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat user baru dengan data spesifik
        User::create([
            'name' => 'Operator Payroll',
            'email' => 'operator@aplikasi.com',
            'password' => bcrypt('password123'),
            'role' => 'operator',
            // INI KUNCINYA: Langsung isi tanggal verifikasi dengan waktu saat ini
            'email_verified_at' => now(), 
        ]);
    }
}