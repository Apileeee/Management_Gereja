<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ibadah extends Model
{
    use HasFactory;

    protected $table = 'ibadah';

    protected $primaryKey = 'id';
    protected $fillable = [
        'nama_periode',
        'nama_ibadah',
        'waktu_ibadah',
        'deskripsi',
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodeLayanan::class, 'nama_periode', 'nama_periode');
    }

}
