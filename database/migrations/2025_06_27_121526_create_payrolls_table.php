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
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->integer('periode_bulan');
            $table->integer('periode_tahun');
            
            // Komponen Pendapatan
            $table->decimal('gaji_pokok', 15, 2)->default(0);
            $table->decimal('total_tunjangan_transport', 15, 2)->default(0);
            $table->decimal('total_upah_lembur', 15, 2)->default(0);
            $table->decimal('total_insentif', 15, 2)->default(0); // Untuk event
            
            // Komponen Potongan
            $table->decimal('total_potongan', 15, 2)->default(0);
            
            // Hasil Akhir
            $table->decimal('gaji_kotor', 15, 2)->default(0);
            $table->decimal('gaji_bersih', 15, 2)->default(0);
            
            $table->timestamps();
            $table->unique(['employee_id', 'periode_bulan', 'periode_tahun']);
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payrolls');
    }
};
