<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlatMusik extends Model
{
    use HasFactory;

    protected $table = 'alat_musik';
    protected $primaryKey = 'id_alat';
    protected $fillable = ['nama_alat'];

public function pemain()
{
    return $this->belongsToMany(
        PemainMusik::class, 'alat_pemain', 'alat_id', 'pemain_id', 'id_alat', 'id_pemain'
    );
}
    
}
