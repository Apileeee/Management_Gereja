<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeLayanan extends Model
{
    use HasFactory;

    protected $table = 'periode_layanan';
    protected $primaryKey = 'id_periode';
    protected $fillable = ['nama_periode', 'deskripsi', 'id_user'];
}
