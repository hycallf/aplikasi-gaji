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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan')->default('Nama Perusahaan Anda');
            $table->string('nama_perwakilan')->nullable();
            $table->text('alamat')->nullable();
            $table->string('email_kontak')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('logo')->nullable(); // Path ke file logo
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
