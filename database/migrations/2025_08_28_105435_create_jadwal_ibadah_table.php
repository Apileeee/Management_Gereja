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
            $table->id('id_jadwal');
            $table->date('tanggal_ibadah');
            $table->foreignId('periode_id')->constrained('periode_layanan','id_periode')->onDelete('cascade');
            $table->foreignId('ibadah_id')->constrained('ibadah','id_ibadah')->onDelete('cascade')->unique(); // 1-1 relasi
            $table->foreignId('id_user')->constrained('admin','id_user')->onDelete('cascade');
            $table->timestamps();
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