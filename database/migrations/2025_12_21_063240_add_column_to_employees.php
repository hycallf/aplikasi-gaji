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
            // Kolom khusus untuk Dosen
            $table->string('nidn')->nullable()->after('nama'); // Nomor Induk Dosen Nasional
            $table->string('gelar_depan')->nullable()->after('nidn');
            $table->string('gelar_belakang')->nullable()->after('gelar_depan');
            $table->enum('status_dosen', ['tetap', 'honorer', 'luar_biasa'])->nullable()->after('gelar_belakang');

            // Index untuk pencarian
            $table->index('nidn');
            $table->index(['tipe_karyawan', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropIndex(['nidn']);
            $table->dropIndex(['tipe_karyawan', 'status']);
            $table->dropColumn(['nidn', 'gelar_depan', 'gelar_belakang', 'status_dosen']);
        });
    }
};
