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
        Schema::table('employees', function (Blueprint $table) {
        // 1. Buat user_id bisa kosong (NULL)
        $table->foreignId('user_id')->nullable()->change();

        // 2. Tambahkan kolom untuk tipe karyawan setelah jabatan
        $table->enum('tipe_karyawan', ['karyawan', 'dosen'])->after('jabatan');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            //
        });
    }
};
