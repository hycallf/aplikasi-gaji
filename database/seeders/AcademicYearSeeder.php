<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AcademicYear;
use Carbon\Carbon;

class AcademicYearSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $academicYears = [
            [
                'nama_tahun_ajaran' => '2023/2024',
                'semester' => 'ganjil',
                'tanggal_mulai' => Carbon::create(2023, 9, 1),
                'tanggal_selesai' => Carbon::create(2024, 1, 31),
                'is_active' => false,
            ],
            [
                'nama_tahun_ajaran' => '2023/2024',
                'semester' => 'genap',
                'tanggal_mulai' => Carbon::create(2024, 2, 1),
                'tanggal_selesai' => Carbon::create(2024, 7, 31),
                'is_active' => false,
            ],
            [
                'nama_tahun_ajaran' => '2024/2025',
                'semester' => 'ganjil',
                'tanggal_mulai' => Carbon::create(2024, 9, 1),
                'tanggal_selesai' => Carbon::create(2025, 1, 31),
                'is_active' => false, // Tahun ajaran aktif
            ],
            [
                'nama_tahun_ajaran' => '2024/2025',
                'semester' => 'genap',
                'tanggal_mulai' => Carbon::create(2025, 2, 1),
                'tanggal_selesai' => Carbon::create(2025, 7, 31),
                'is_active' => false,
            ],
            [
                'nama_tahun_ajaran' => '2025/2026',
                'semester' => 'ganjil',
                'tanggal_mulai' => Carbon::create(2025, 9, 1),
                'tanggal_selesai' => Carbon::create(2026, 1, 31),
                'is_active' => true, // Tahun ajaran aktif
            ],
            [
                'nama_tahun_ajaran' => '2025/2026',
                'semester' => 'genap',
                'tanggal_mulai' => Carbon::create(2026, 2, 1),
                'tanggal_selesai' => Carbon::create(2026, 7, 31),
                'is_active' => false,
            ],
        ];

        foreach ($academicYears as $year) {
            AcademicYear::create($year);
        }

        $this->command->info('Academic years seeded successfully!');
    }
}
