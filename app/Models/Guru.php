<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Guru extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "guru";
    protected $primaryKey = "nip";
    public $incrementing = false;

    protected $fillable = [
        'nip',
        'nama_lengkap',
        'mata_pelajaran',
        'no_hp',
        'password',
        'kode_jurusan',
        'kode_sekolah',
        'foto',
        'face_descriptor',
        'face_registered_at',
        'status_location',
        'status_jam_kerja',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'face_registered_at' => 'datetime',
    ];
}
