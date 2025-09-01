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
        Schema::create('ibadah', function (Blueprint $table) {
            $table->id(); // id otomatis
            $table->string('nama_periode'); // Nama Ibadah
            $table->string('nama_ibadah'); // Nama Ibadah
            $table->text('deskripsi')->nullable(); // Deskripsi tambahan
            $table->datetime('waktu_ibadah')->nullable(); // Waktu ibadah (H:i)
            $table->timestamps(); // created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ibadah');
    }
};