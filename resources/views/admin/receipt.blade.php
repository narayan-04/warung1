<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pesanan #{{ $pesanan->id_pesanan }}</title>
    <style>
        /* CSS Native untuk Printer Thermal 58mm */
        @page {
            size: 58mm auto; /* Mengatur ukuran kertas printer kasir 58mm secara otomatis */
            margin: 0;
        }
        html, body {
            width: 58mm;
            margin: 0;
            padding: 0;
            font-family: 'Courier New', Courier, monospace; /* Wajib Monospace agar tabulasi rata */
            font-size: 11px;
            color: #000;
            background: #fff;
            line-height: 1.3;
        }
        body {
            padding: 8px 6px;
            box-sizing: border-box;
        }
        .receipt-container {
            width: 100%;
        }
        .text-center {
            text-align: center;
        }
        .text-right {
            text-align: right;
        }
        .font-bold {
            font-weight: bold;
        }
        .brand {
            font-size: 14px;
            font-weight: bold;
            margin: 0 0 2px 0;
            letter-spacing: 1px;
        }
        .address {
            font-size: 9px;
            margin: 0 0 8px 0;
        }
        .divider {
            border-top: 1px dashed #000;
            margin: 6px 0;
        }
        .double-divider {
            border-top: 1px double #000;
            margin: 6px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table td {
            vertical-align: top;
            padding: 1px 0;
        }
        .item-row td {
            padding-bottom: 2px;
        }
        .item-name {
            display: block;
            font-weight: bold;
            word-wrap: break-word;
        }
        .item-qty-price {
            font-size: 10px;
            color: #333;
        }
        .footer {
            margin-top: 15px;
            font-size: 9px;
        }
    </style>
</head>
<body>
    <div class="receipt-container">
        <!-- Header Struk -->
        <div class="header text-center">
            <div class="brand">WARUNG SEAFOOD</div>
            <div class="address">
                Jl. Contoh Jalan No. 123<br>
                Telp: 0812-3456-7890
            </div>
        </div>
        
        <div class="divider"></div>

        <!-- Info Pesanan -->
        <table>
            <tr>
                <td style="width: 35%;">Order ID</td>
                <td>: #{{ $pesanan->id_pesanan }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td>: {{ \Carbon\Carbon::parse($pesanan->created_at)->format('d/m/y H:i') }}</td>
            </tr>
            <tr>
                <td>Pelanggan</td>
                <td>: {{ $pesanan->pelanggan->nama_pelanggan ?? ($pesanan->pelanggan->akun->nama ?? 'Tamu') }}</td>
            </tr>
            <tr>
                <td>Meja</td>
                <td>: {{ $pesanan->no_meja }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Daftar Menu -->
        <table>
            @foreach($pesanan->detailPesanan as $detail)
            <tr class="item-row">
                <td colspan="2">
                    <span class="item-name">{{ $detail->menu->nama_menu }}</span>
                    <span class="item-qty-price">{{ $detail->jumlah }} x Rp {{ number_format($detail->menu->harga, 0, ',', '.') }}</span>
                </td>
                <td class="text-right" style="vertical-align: bottom; width: 40%;">
                    Rp {{ number_format($detail->jumlah * $detail->menu->harga, 0, ',', '.') }}
                </td>
            </tr>
            @endforeach
        </table>

        <div class="double-divider"></div>

        <!-- Rincian Pembayaran -->
        <table style="width: 100%;">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
            </tr>
            <tr class="font-bold">
                <td>TOTAL</td>
                <td class="text-right">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td colspan="2" class="divider"></td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td class="text-right">{{ $pesanan->metode_pembayaran }}</td>
            </tr>
            <tr class="font-bold">
                <td>Status</td>
                <td class="text-right" style="text-transform: uppercase;">{{ $pesanan->status_pembayaran }}</td>
            </tr>
        </table>

        <div class="divider"></div>

        <!-- Footer Struk -->
        <div class="footer text-center">
            <div>Terima Kasih Atas Kunjungan Anda</div>
            <div style="margin-top: 4px; font-weight: bold;">-- Selamat Menikmati --</div>
        </div>
    </div>

    <!-- Script Cetak Otomatis -->
    <script>
        window.onload = function() {
            window.print();
            // Opsional: Tutup jendela otomatis setelah cetak selesai
            window.onafterprint = function() {
                window.close();
            }
        }
    </script>
</body>
</html>
