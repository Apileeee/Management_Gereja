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
        Schema::create('ibadah_pemain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ibadah_id')->constrained('ibadah','id_ibadah')->onDelete('cascade');
            $table->foreignId('pemain_id')->constrained('pemain_musik','id_pemain')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ibadah_pemain');
    }
};