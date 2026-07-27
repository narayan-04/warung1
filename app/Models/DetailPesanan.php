<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPesanan extends Model
{
    protected $table = 'detail_pesanan';
    protected $primaryKey = 'id_detail';
    public $timestamps = false;

    protected $fillable = [
        'id_pesanan',
        'id_menu',
        'harga_satuan', // Pastikan ini ada
        'jumlah',       // Pastikan ini ada
        'subtotal',
    ];

    // Relasi ke Pesanan
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }

    // Relasi ke Menu (Agar kita bisa menampilkan nama menu & harga di keranjang/struk)
    public function menu()
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }
}