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
        Schema::create('dosen_matkul_enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('matkul_id')->constrained('matkuls')->onDelete('cascade');
            $table->foreignId('academic_year_id')->constrained('academic_years')->onDelete('cascade');
            $table->string('kelas')->nullable(); // "A", "B", "Reguler", "Malam"
            $table->integer('jumlah_mahasiswa')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            // Constraint: Satu dosen tidak bisa mengajar matkul yang sama di kelas yang sama dalam satu tahun ajaran
            $table->unique(['employee_id', 'matkul_id', 'academic_year_id', 'kelas'], 'unique_enrollment');

            // Index untuk performa query
            $table->index(['employee_id', 'academic_year_id']);
            $table->index('academic_year_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dosen_matkul_enrollments');
    }
};
