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
        Schema::create('inactivity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('tipe'); // Menyimpan alasan: 'Cuti', 'Sakit', 'Melahirkan', dll.
            $table->text('keterangan')->nullable(); // Detail tambahan dari alasan
            $table->date('start_date'); // Tanggal mulai nonaktif
            $table->date('end_date')->nullable(); // Estimasi tanggal kembali aktif (bisa kosong)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inactivity_logs');
    }
};
