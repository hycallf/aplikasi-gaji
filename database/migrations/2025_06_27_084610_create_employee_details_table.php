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
        Schema::create('employee_details', function (Blueprint $table) {
            $table->id();

            // Kunci utama & relasi
            $table->foreignId('employee_id')->unique()->constrained()->onDelete('cascade');

            // Data personal
            $table->string('foto')->nullable();
            $table->text('alamat')->nullable();
            $table->text('domisili')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('no_ktp')->nullable();
            $table->enum('status_pernikahan', ['Lajang', 'Menikah'])->nullable();
            $table->integer('jumlah_anak')->default(0);
            $table->string('riwayat_pendidikan')->nullable();

            // Data pekerjaan yang lebih detail (seperti yang Anda sebutkan)
            $table->date('tanggal_masuk')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_details');
    }
};
