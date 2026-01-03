<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('type')->default('string'); // string, integer, boolean, json, array
            $table->string('group')->default('general'); // general, attendance, payroll, dosen, etc
            $table->string('label')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('key');
            $table->index('group');
        });

        // Insert default settings
        DB::table('settings')->insert([
            // Attendance Settings
            [
                'key' => 'work_days',
                'value' => json_encode([1, 2, 3, 4, 5, 6]), // Senin-Sabtu (0=Minggu, 6=Sabtu)
                'type' => 'array',
                'group' => 'attendance',
                'label' => 'Hari Kerja dalam Seminggu',
                'description' => 'Pilih hari kerja (0=Minggu, 1=Senin, dst). Hari yang dipilih akan dihitung sebagai hari kerja.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'work_hours_per_day',
                'value' => '8',
                'type' => 'integer',
                'group' => 'attendance',
                'label' => 'Jam Kerja per Hari',
                'description' => 'Jumlah jam kerja standar per hari untuk karyawan',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Payroll Settings
            [
                'key' => 'pulang_awal_deduction',
                'value' => '10000',
                'type' => 'integer',
                'group' => 'payroll',
                'label' => 'Potongan Pulang Awal',
                'description' => 'Jumlah potongan transport untuk karyawan yang pulang awal (Rp)',
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // Dosen Settings (BARU)
            [
                'key' => 'dosen_rate_per_sks',
                'value' => '7500',
                'type' => 'integer',
                'group' => 'dosen',
                'label' => 'Upah per SKS per Pertemuan',
                'description' => 'Honorarium dosen per SKS per pertemuan (Rp). Contoh: 7500 × SKS × Jumlah Pertemuan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
