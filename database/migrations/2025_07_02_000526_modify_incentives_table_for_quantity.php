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
        Schema::table('incentives', function (Blueprint $table) {
            $table->renameColumn('jumlah_insentif', 'total_amount');

            // Tambahkan kolom baru
            $table->unsignedInteger('quantity')->default(1)->after('tanggal_insentif');
            $table->decimal('unit_amount', 15, 2)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('incentives', function (Blueprint $table) {
            //
        });
    }
};
