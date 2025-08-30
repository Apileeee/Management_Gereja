<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jadwal_ibadah', function (Blueprint $table) {
            $table->id();
            $table->foreignId('periode_layanan_id')->constrained('periode_layanan')->onDelete('cascade');
            $table->foreignId('ibadah_id')->constrained('ibadah')->onDelete('cascade');
            $table->text('personil'); // nama-nama personil gabungan
            $table->json('pemain_ids')->nullable(); // simpan id pemain dalam array
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwal_ibadah');
    }
};
