<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlatPemain extends Model
{
    protected $table = 'alat_pemain';
    public $timestamps = false;
    protected $fillable = ['alat_id', 'pemain_id'];

    public function pemain()
    {
        // pk pemain_musik = id_pemain
        return $this->belongsTo(PemainMusik::class, 'pemain_id', 'id_pemain');
    }

    public function alat()
    {
        // pk alat_musik = id_alat
        return $this->belongsTo(AlatMusik::class, 'alat_id', 'id_alat');
    }
}
