<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // Attendance Settings
            [
                'key' => 'work_days',
                'value' => json_encode([1, 2, 3, 4, 5, 6]),
                'type' => 'array',
                'group' => 'attendance',
                'label' => 'Hari Kerja dalam Seminggu',
                'description' => 'Pilih hari kerja (0=Minggu, 1=Senin, dst). Hari yang dipilih akan dihitung sebagai hari kerja.',
            ],
            [
                'key' => 'work_hours_per_day',
                'value' => '8',
                'type' => 'integer',
                'group' => 'attendance',
                'label' => 'Jam Kerja per Hari',
                'description' => 'Jumlah jam kerja standar per hari untuk karyawan',
            ],

            // Payroll Settings
            [
                'key' => 'pulang_awal_deduction',
                'value' => '10000',
                'type' => 'integer',
                'group' => 'payroll',
                'label' => 'Potongan Pulang Awal',
                'description' => 'Jumlah potongan transport untuk karyawan yang pulang awal (Rp)',
            ],

            // Dosen Settings
            [
                'key' => 'dosen_rate_per_sks',
                'value' => '7500',
                'type' => 'integer',
                'group' => 'dosen',
                'label' => 'Upah per SKS per Pertemuan',
                'description' => 'Honorarium dosen per SKS per pertemuan (Rp). Contoh: 7500 × SKS × Jumlah Pertemuan',
            ],

            // CONTOH TAMBAHAN: Anda bisa tambah di sini nanti
            /*
            [
                'key' => 'site_name',
                'value' => 'Aplikasi Absensi',
                'type' => 'string',
                'group' => 'general',
                'label' => 'Nama Situs',
                'description' => 'Nama aplikasi yang muncul di header',
            ],
            */
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->updateOrInsert(
                ['key' => $setting['key']], // Cek berdasarkan key unik
                array_merge($setting, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        $this->command->info('Settings table seeded/updated successfully!');
    }
}
