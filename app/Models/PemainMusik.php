<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PemainMusik extends Model
{
    use HasFactory;

    protected $table = 'pemain_musik';
    protected $primaryKey = 'id_pemain';
    protected $fillable = ['nama_pemain', 'gender', 'foto'];
    
    public function alat()
    {
        return $this->belongsToMany(AlatMusik::class, 'alat_pemain', 'pemain_id', 'alat_id');
    }
}
