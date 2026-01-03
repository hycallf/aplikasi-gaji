<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MatkulSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = Carbon::now();

        $matkuls = [
            // --- Semester 1 (Dasar) ---
            ['nama_matkul' => 'Algoritma dan Pemrograman', 'sks' => 4],
            ['nama_matkul' => 'Kalkulus 1', 'sks' => 3],
            ['nama_matkul' => 'Logika Informatika', 'sks' => 3],
            ['nama_matkul' => 'Pengantar Teknologi Informasi', 'sks' => 2],
            ['nama_matkul' => 'Bahasa Inggris 1', 'sks' => 2],
            ['nama_matkul' => 'Pendidikan Agama', 'sks' => 2],

            // --- Semester 2 (Lanjutan Dasar) ---
            ['nama_matkul' => 'Struktur Data', 'sks' => 4],
            ['nama_matkul' => 'Arsitektur dan Organisasi Komputer', 'sks' => 3],
            ['nama_matkul' => 'Kalkulus 2', 'sks' => 3],
            ['nama_matkul' => 'Aljabar Linear dan Matriks', 'sks' => 3],
            ['nama_matkul' => 'Statistika dan Probabilitas', 'sks' => 3],
            ['nama_matkul' => 'Pendidikan Pancasila', 'sks' => 2],

            // --- Semester 3 (Inti) ---
            ['nama_matkul' => 'Basis Data 1', 'sks' => 4],
            ['nama_matkul' => 'Pemrograman Berorientasi Objek', 'sks' => 4],
            ['nama_matkul' => 'Sistem Operasi', 'sks' => 3],
            ['nama_matkul' => 'Matematika Diskrit', 'sks' => 3],
            ['nama_matkul' => 'Jaringan Komputer 1', 'sks' => 3],

            // --- Semester 4 (Inti Lanjutan) ---
            ['nama_matkul' => 'Rekayasa Perangkat Lunak', 'sks' => 3],
            ['nama_matkul' => 'Analisis dan Perancangan Algoritma', 'sks' => 3],
            ['nama_matkul' => 'Pemrograman Web 1', 'sks' => 3],
            ['nama_matkul' => 'Basis Data 2', 'sks' => 3],
            ['nama_matkul' => 'Interaksi Manusia dan Komputer', 'sks' => 3],
            ['nama_matkul' => 'Kewarganegaraan', 'sks' => 2],

            // --- Semester 5 & 6 (Peminatan/Lanjut) ---
            ['nama_matkul' => 'Kecerdasan Buatan', 'sks' => 3],
            ['nama_matkul' => 'Keamanan Jaringan', 'sks' => 3],
            ['nama_matkul' => 'Pemrograman Mobile', 'sks' => 3],
            ['nama_matkul' => 'Data Mining', 'sks' => 3],
            ['nama_matkul' => 'Teori Bahasa dan Otomata', 'sks' => 3],
            ['nama_matkul' => 'Sistem Terdistribusi', 'sks' => 3],
            ['nama_matkul' => 'Metode Penelitian', 'sks' => 2],

            // --- Semester 7 & 8 (Akhir) ---
            ['nama_matkul' => 'Kerja Praktik', 'sks' => 2],
            ['nama_matkul' => 'Etika Profesi', 'sks' => 2],
            ['nama_matkul' => 'Kewirausahaan', 'sks' => 2],
            ['nama_matkul' => 'Tugas Akhir / Skripsi', 'sks' => 6],
        ];

        // Insert batch dengan timestamp
        foreach ($matkuls as $mk) {
            DB::table('matkuls')->insert([
                'nama_matkul' => $mk['nama_matkul'],
                'sks' => $mk['sks'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }
}
