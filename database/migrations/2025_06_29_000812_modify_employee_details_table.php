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
        Schema::table('employee_details', function (Blueprint $table) {
            
            $table->renameColumn('riwayat_pendidikan', 'pendidikan_terakhir');

            // 2. Tambahkan kolom baru setelah 'pendidikan_terakhir'
            $table->string('jurusan')->nullable()->after('pendidikan_terakhir');

            // 3. Hapus kolom 'no_ktp'
            if (Schema::hasColumn('employee_details', 'no_ktp')) {
                $table->dropColumn('no_ktp');
            }

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_details', function (Blueprint $table) {
            $table->renameColumn('pendidikan_terakhir', 'riwayat_pendidikan');
            $table->dropColumn(['jurusan', 'domisili']);
            $table->string('no_ktp')->nullable();
        });
    }
};
