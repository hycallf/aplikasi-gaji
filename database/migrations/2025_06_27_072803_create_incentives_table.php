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
        Schema::create('incentives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_insentif');
            $table->text('deskripsi')->nullable();
            $table->decimal('jumlah_insentif', 15, 2);
            $table->timestamps();

            // Satu karyawan hanya bisa dapat satu insentif per event
            $table->unique(['event_id', 'employee_id', 'tanggal_insentif']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('incentives');
    }
};
