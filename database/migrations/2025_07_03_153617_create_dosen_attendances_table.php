<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Migration ini akan DROP tabel lama dan buat ulang dengan struktur baru
     * yang sudah support enrollment system.
     *
     * PERINGATAN: Semua data di tabel dosen_attendances akan HILANG!
     * Hanya gunakan di fase DEVELOPMENT.
     */
    public function up(): void
    {
        // Drop tabel lama jika ada (termasuk semua constraint-nya)
        Schema::dropIfExists('dosen_attendances');

        // Buat tabel baru dengan struktur yang benar
        Schema::create('dosen_attendances', function (Blueprint $table) {
            $table->id();

            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('matkul_id')->constrained('matkuls')->onDelete('cascade');

            // --- TAMBAHKAN INI ---
            // Karena kamu mau meng-index kolom ini di bawah, kolomnya harus ada dulu.
            $table->foreignId('academic_year_id')
                ->constrained('academic_years')
                ->onDelete('cascade')->nullable();
            // ---------------------

            $table->foreignId('enrollment_id')
                ->nullable()
                ->constrained('dosen_matkul_enrollments')
                ->onDelete('cascade');

            $table->string('kelas', 50)->nullable();
            $table->unsignedTinyInteger('periode_bulan');
            $table->unsignedSmallInteger('periode_tahun');
            $table->unsignedTinyInteger('jumlah_pertemuan')->default(0);

            $table->timestamps();

            $table->unique(['enrollment_id', 'periode_bulan', 'periode_tahun'], 'unique_enrollment_attendance');

            $table->index(['employee_id', 'periode_bulan', 'periode_tahun'], 'idx_employee_period');

            // Sekarang baris ini aman dijalankan karena kolomnya sudah ada
            $table->index('academic_year_id', 'idx_academic_year');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop tabel baru
        Schema::dropIfExists('dosen_attendances');

        // Buat ulang tabel dengan struktur LAMA (tanpa enrollment)
        Schema::create('dosen_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('matkul_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('periode_bulan');
            $table->unsignedSmallInteger('periode_tahun');
            $table->unsignedTinyInteger('jumlah_pertemuan');
            $table->timestamps();

            // Constraint lama
            $table->unique(
                ['employee_id', 'matkul_id', 'periode_bulan', 'periode_tahun'],
                'dosen_attendance_unique'
            );
        });
    }
};
