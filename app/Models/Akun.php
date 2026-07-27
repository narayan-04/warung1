<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Akun extends Authenticatable
{
    use Notifiable;

    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $timestamps = false; // Diberi false agar aman jika tidak ada kolom updated_at

    protected $fillable = [
        'username',
        'password_hash', // <-- Sesuaikan dengan nama kolom di PostgreSQL
        'role',
    ];

    protected $hidden = [
        'password_hash',
    ];

    /*
    |--------------------------------------------------------------------------
    | 🌟 KUNCI RAHASIA: Override Kolom Password Bawaan Laravel
    |--------------------------------------------------------------------------
    | Dua fungsi di bawah ini bertugas memberi tahu fitur Auth Laravel
    | bahwa sandi kita disimpan di dalam kolom 'password_hash'.
    */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    public function getAuthPasswordName()
    {
        return 'password_hash';
    }

    // Relasi ke data pelanggan
    public function pelanggan()
    {
        return $this->hasOne(Pelanggan::class, 'id_akun', 'id_akun');
    }
}