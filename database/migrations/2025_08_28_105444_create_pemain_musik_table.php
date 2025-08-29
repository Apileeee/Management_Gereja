<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemain_musik', function (Blueprint $table) {
            $table->id('id_pemain');
            $table->string('nama_pemain');
            $table->enum('gender', ['L', 'P']);
            $table->string('foto')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemain_musik');
    }
};
