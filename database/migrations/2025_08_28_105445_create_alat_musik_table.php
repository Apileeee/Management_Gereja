<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alat_musik', function (Blueprint $table) {
            $table->id('id_alat');
            $table->string('nama_alat');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alat_musik');
    }
};
