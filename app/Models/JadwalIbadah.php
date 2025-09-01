<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JadwalIbadah extends Model
{
    protected $table = 'jadwal_ibadah';
    protected $fillable = [
        'id',
        'nama_periode',
        'nama_ibadah',
        'waktu_ibadah',
        'nama_pemain',
        'nama_alat',
    ];

    public function ibadah()
    {
        return $this->belongsTo(Ibadah::class, 'ibadah_id');
    }

    public function periode_layanan()
    {
        return $this->belongsTo(PeriodeLayanan::class, 'periode', 'nama_periode');
    }
    
    // Accessor untuk format waktu
    public function getFormattedWaktuAttribute()
    {
        return $this->waktu_ibadah ? $this->waktu_ibadah->format('d-m-Y H:i') : null;
    }
    
    // Accessor untuk hitung jumlah personil
    public function getJumlahPersonilAttribute()
    {
        if (empty($this->personil)) return 0;
        return count(explode(', ', $this->personil));
    }
}

