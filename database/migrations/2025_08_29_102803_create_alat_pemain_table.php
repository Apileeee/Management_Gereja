<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat_pemain', function (Blueprint $table) {
        $table->id();

        // Relasi ke tabel alat_musik
        $table->unsignedBigInteger('alat_id');
        $table->foreign('alat_id')->references('id_alat')->on('alat_musik')->onDelete('cascade');

        // Relasi ke tabel pemain_musik
        $table->unsignedBigInteger('pemain_id');
        $table->foreign('pemain_id')->references('id_pemain')->on('pemain_musik')->onDelete('cascade');

        $table->timestamps();
    });

    }

    public function down(): void
    {
        Schema::dropIfExists('alat_pemain');
    }
};
