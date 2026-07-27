<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    protected $table = 'pelanggan';
    protected $primaryKey = 'id_pelanggan';
    public $timestamps = false;

    protected $fillable = [
        'id_akun',
        'nama_pelanggan',
        'no_telp',
    ];

    // Relasi ke Akun
    public function akun()
    {
        return $this->belongsTo(Akun::class, 'id_akun', 'id_akun');
    }

    // Relasi ke Pesanan (Satu pelanggan bisa memesan berkali-kali)
    public function pesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_pelanggan', 'id_pelanggan');
    }
}