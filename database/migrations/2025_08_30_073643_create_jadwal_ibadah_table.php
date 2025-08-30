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
        Schema::create('jadwal_ibadah', function (Blueprint $table) {
            $table->id();
            $table->string('nama_periode');
            $table->string('nama_ibadah');
            $table->dateTime('waktu_ibadah'); // bisa datetime, atau string kalau mau lebih fleksibel
            $table->string('nama_pemain');
            $table->string('nama_alat');
            $table->timestamps(); // otomatis buat created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_ibadah');
    }
};
