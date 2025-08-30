<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalIbadah extends Model
{
    protected $table = 'jadwal_ibadah';
    protected $fillable = ['periode', 'ibadah_id', 'waktu_ibadah', 'personil', 'alat_musik'];

    public function ibadah()
    {
        return $this->belongsTo(Ibadah::class, 'ibadah_id');
    }
}

