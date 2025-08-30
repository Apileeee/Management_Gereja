<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admin'; // nama tabel

    protected $primaryKey = 'id_user'; // sesuaikan primary key
    public $incrementing = true; // jika auto-increment
    protected $keyType = 'int'; // tipe primary key

    protected $fillable = [
        'nama_user',
        'password',
        'foto', 
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Mutator untuk otomatis hash password
    public function setPasswordAttribute($password)
    {
        if ($password) { // hanya jika diisi
            $this->attributes['password'] = bcrypt($password);
        }
    }
}
