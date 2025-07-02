<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('dosen_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('matkul_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('periode_bulan');
            $table->unsignedSmallInteger('periode_tahun');
            $table->unsignedTinyInteger('jumlah_pertemuan'); // Jumlah kehadiran dosen di matkul ini pada periode tsb
            $table->timestamps();

            // Kunci unik untuk mencegah duplikasi data
            $table->unique(
                ['employee_id', 'matkul_id', 'periode_bulan', 'periode_tahun'],
                'dosen_attendance_unique' // <-- Nama custom yang singkat
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_attendances');
    }
};
