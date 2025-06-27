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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->enum('status', ['hadir', 'sakit', 'izin', 'pulang_awal'])->default('hadir');
            $table->text('description')->nullable(); // Keterangan untuk sakit/izin
            $table->timestamps();

            // Membuat kombinasi employee_id dan date menjadi unik
            // agar satu karyawan hanya punya satu record absensi per hari.
            $table->unique(['employee_id', 'date']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
