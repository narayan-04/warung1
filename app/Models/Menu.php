<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Menu extends Model
{
    use SoftDeletes;

    protected $table = 'menu';
    protected $primaryKey = 'id_menu';
    public $timestamps = false; 

    protected $fillable = [
        'nama_menu',
        'id_kategori',
        'harga',
        'deskripsi',
        'foto_url', // Pastikan menggunakan foto_url
        'is_available',
    ];

    // Tambahkan fungsi relasi ini
    public function kategoriData()
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }
}