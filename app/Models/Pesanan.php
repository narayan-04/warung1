<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pesanan extends Model
{
    protected $table = 'pesanan';
    protected $primaryKey = 'id_pesanan';
    public $timestamps = false;

    protected $fillable = [
        'id_pelanggan',
        'no_meja',
        'total_harga',
        'status', // 'Pesanan Masuk', 'Diproses', 'Selesai', 'Dibatalkan'
        'status_pembayaran', // 'Belum Bayar', 'Lunas'
        'metode_pembayaran', // 'Cash', 'QRIS'
        'catatan',
    ];

    // Relasi ke Pelanggan
    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan', 'id_pelanggan');
    }

    // Relasi ke Detail Pesanan (Rincian hidangan apa saja yang dipesan)
    public function detailPesanan()
    {
        return $this->hasMany(DetailPesanan::class, 'id_pesanan', 'id_pesanan');
    }
}