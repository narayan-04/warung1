<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Tambahkan DB facade untuk query sum/raw menu terlaris
use App\Models\Pesanan;
use App\Models\Menu;
use App\Models\Pelanggan;
use App\Models\DetailPesanan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class AdminController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!auth()->check() || auth()->user()->role !== 'Penjual') {
                    return redirect()->route('home')->with('error', 'Akses ditolak. Halaman ini hanya untuk Penjual.');
                }
                return $next($request);
            }),
        ];
    }
    // TOGGLE STATUS WARUNG BUKA / TUTUP
    public function toggleWarungStatus(Request $request)
    {
        $currentStatus = Cache::get('status_warung', 'buka');
        $newStatus = ($currentStatus === 'buka') ? 'tutup' : 'buka';
        Cache::put('status_warung', $newStatus);

        return response()->json([
            'success' => true,
            'status'  => $newStatus,
            'message' => 'Status warung berhasil diubah menjadi ' . strtoupper($newStatus)
        ]);
    }
    // 1. DASHBOARD STATISTIK
    public function dashboard(Request $request)
    {
        $periode = $request->get('periode', 'hari_ini');
        
        $queryPendapatan = Pesanan::where('status_pembayaran', 'Lunas');
        $queryPesanan = Pesanan::query();

        if (\Schema::hasColumn('pesanan', 'created_at')) {
            if ($periode == 'kemarin') {
                $queryPendapatan->whereDate('created_at', \Carbon\Carbon::yesterday());
                $queryPesanan->whereDate('created_at', \Carbon\Carbon::yesterday());
            } elseif ($periode == 'minggu_ini') {
                $queryPendapatan->whereBetween('created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                $queryPesanan->whereBetween('created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
            } else {
                $queryPendapatan->whereDate('created_at', \Carbon\Carbon::today());
                $queryPesanan->whereDate('created_at', \Carbon\Carbon::today());
            }
        }

        $totalPendapatanHariIni = $queryPendapatan->sum('total_harga');
        $totalPesananHariIni = $queryPesanan->count();
        
        $hidanganTerjual = DetailPesanan::join('pesanan', 'detail_pesanan.id_pesanan', '=', 'pesanan.id_pesanan')
            ->when(\Schema::hasColumn('pesanan', 'created_at'), function($q) use ($periode) {
                if ($periode == 'kemarin') {
                    $q->whereDate('pesanan.created_at', \Carbon\Carbon::yesterday());
                } elseif ($periode == 'minggu_ini') {
                    $q->whereBetween('pesanan.created_at', [\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek()]);
                } else {
                    $q->whereDate('pesanan.created_at', \Carbon\Carbon::today());
                }
            })->sum('detail_pesanan.jumlah');

        // Fallback jika data filter spesifik bernilai 0 (agar tampilan pertama kali ada datanya)
        if ($totalPendapatanHariIni == 0 && $totalPesananHariIni == 0) {
            $totalPendapatanHariIni = Pesanan::where('status_pembayaran', 'Lunas')->sum('total_harga');
            $totalPesananHariIni = Pesanan::count();
            $hidanganTerjual = DetailPesanan::sum('jumlah');
        }

        // Kalkulasi Trend Pendapatan vs Kemarin secara Real
        $pendapatanKemarin = Pesanan::where('status_pembayaran', 'Lunas')
            ->when(\Schema::hasColumn('pesanan', 'created_at'), function($q) {
                $q->whereDate('created_at', \Carbon\Carbon::yesterday());
            })->sum('total_harga');

        if ($pendapatanKemarin > 0) {
            $persen = round((($totalPendapatanHariIni - $pendapatanKemarin) / $pendapatanKemarin) * 100);
            $trendPendapatanText = ($persen >= 0 ? "+{$persen}%" : "{$persen}%") . " dari kemarin";
            $trendPendapatanUp = $persen >= 0;
        } else {
            $trendPendapatanText = "100% Lunas";
            $trendPendapatanUp = true;
        }

        // Pelanggan Baru & Stat Hari Ini
        $totalPelangganBaru = Pelanggan::count();
        $pelangganBaruHariIni = Pelanggan::when(\Schema::hasColumn('pelanggan', 'created_at'), function($q) {
            $q->whereDate('created_at', \Carbon\Carbon::today());
        })->count();
        $pelangganBaruHariIniText = "+" . ($pelangganBaruHariIni > 0 ? $pelangganBaruHariIni : 0) . " mendaftar hari ini";

        // 5 Transaksi Terbaru
        $transaksiTerbaru = Pesanan::with(['pelanggan.akun'])
                                ->orderBy('id_pesanan', 'desc')
                                ->take(5)
                                ->get();

        // Daftar menu yang stoknya habis (is_available = 0)
        $menuHabisList = Menu::where('is_available', 0)->get();

        // Menu Terlaris (Best Seller berdasarkan total porsi terjual di detail_pesanan)
        $menuTerlaris = Menu::select('menu.*', DB::raw('SUM(detail_pesanan.jumlah) as total_terjual'))
            ->join('detail_pesanan', 'menu.id_menu', '=', 'detail_pesanan.id_menu')
            ->groupBy('menu.id_menu')
            ->orderByDesc('total_terjual')
            ->take(4)
            ->get();

        // Data Grafik Pendapatan Mingguan Real (Senin - Minggu)
        $daysMap = [1 => 'Sen', 2 => 'Sel', 3 => 'Rab', 4 => 'Kam', 5 => 'Jum', 6 => 'Sab', 7 => 'Min'];
        $chartData = ['Sen' => 0, 'Sel' => 0, 'Rab' => 0, 'Kam' => 0, 'Jum' => 0, 'Sab' => 0, 'Min' => 0];

        $currentDayIso = \Carbon\Carbon::now()->dayOfWeekIso; // 1 (Senin) s/d 7 (Minggu)

        $lunasOrders = Pesanan::where('status_pembayaran', 'Lunas')->get();

        foreach ($lunasOrders as $ord) {
            if (\Schema::hasColumn('pesanan', 'created_at') && !empty($ord->created_at)) {
                $orderDate = \Carbon\Carbon::parse($ord->created_at);
                if ($orderDate->between(\Carbon\Carbon::now()->startOfWeek(), \Carbon\Carbon::now()->endOfWeek())) {
                    $dayIndex = $orderDate->dayOfWeekIso;
                    if (isset($daysMap[$dayIndex])) {
                        $chartData[$daysMap[$dayIndex]] += (float) $ord->total_harga;
                    }
                }
            } else {
                // Jika tidak ada timestamp spesifik, masukkan ke hari berjalan saat ini (misal: Senin)
                if (isset($daysMap[$currentDayIso])) {
                    $chartData[$daysMap[$currentDayIso]] += (float) $ord->total_harga;
                }
            }
        }

        $totalWeeklyRevenue = array_sum($chartData);
        $statusWarung = Cache::get('status_warung', 'buka');

        return view('admin.dashboard', compact(
            'totalPendapatanHariIni', 
            'totalPesananHariIni', 
            'hidanganTerjual',
            'totalPelangganBaru', 
            'transaksiTerbaru',
            'menuHabisList',
            'menuTerlaris',
            'periode',
            'chartData',
            'totalWeeklyRevenue',
            'trendPendapatanText',
            'trendPendapatanUp',
            'pelangganBaruHariIniText',
            'statusWarung'
        ));
    }

    // 2. KELOLA PESANAN (KDS DAPUR & KASIR)
    public function orders(Request $request)
    {
        // Hitung total untuk Tab Status (unfiltered)
        $countSemua = Pesanan::has('detailPesanan')->count();
        $countMasuk = Pesanan::has('detailPesanan')->where('status', 'Menunggu')->count();
        $countDiproses = Pesanan::has('detailPesanan')->where('status', 'Sedang Dimasak')->count();
        $countSelesai = Pesanan::has('detailPesanan')->where('status', 'Selesai')->count();

        // Query dasar
        $query = Pesanan::with(['pelanggan.akun', 'detailPesanan.menu'])
                        ->has('detailPesanan')
                        ->orderBy('id_pesanan', 'desc');

        // Filter berdasarkan Status Tab (GET parameter 'status_filter')
        $statusFilter = $request->get('status_filter');
        if ($statusFilter && in_array($statusFilter, ['Menunggu', 'Sedang Dimasak', 'Selesai'])) {
            $query->where('status', $statusFilter);
        }

        // Filter berdasarkan pencarian (GET parameter 'search')
        $search = $request->get('search');
        if ($search) {
            $query->where(function($q) use ($search) {
                $cleanSearch = str_replace('#ORD-', '', $search);
                if (is_numeric($cleanSearch)) {
                    $q->where('id_pesanan', $cleanSearch);
                } else {
                    $q->where('no_meja', 'like', '%' . $search . '%')
                      ->orWhereHas('pelanggan', function($qp) use ($search) {
                          $qp->where('nama_pelanggan', 'like', '%' . $search . '%')
                             ->orWhereHas('akun', function($qa) use ($search) {
                                 $qa->where('username', 'like', '%' . $search . '%');
                             });
                      });
                }
            });
        }

        $pesanan = $query->get();

        return view('admin.orders', compact(
            'pesanan', 
            'countSemua', 
            'countMasuk', 
            'countDiproses', 
            'countSelesai',
            'statusFilter',
            'search'
        ));
    }

    // Ubah Status Pesanan (Pesanan Masuk -> Diproses -> Selesai)[cite: 8]
    public function updateOrderStatus(Request $request, $id)
    {
        // Menggunakan id_pesanan agar lebih aman dengan custom schema[cite: 8]
        $pesanan = Pesanan::where('id_pesanan', $id)->firstOrFail();
        
        if ($request->has('status')) {
            $pesanan->status = $request->status;
        }
        
        if ($request->has('status_pembayaran')) {
            $pesanan->status_pembayaran = $request->status_pembayaran;
        }

        $pesanan->save();

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui!'); //[cite: 8]
    }

    // Ubah Status Pembayaran menjadi Lunas (Dari tombol klik)[cite: 8]
    public function updatePaymentStatus($id)
    {
        $pesanan = Pesanan::findOrFail($id);
        
        // Toggle atau ubah langsung menjadi Lunas[cite: 8]
        $pesanan->status_pembayaran = 'Lunas'; //[cite: 8]
        $pesanan->save(); //[cite: 8]

        return redirect()->back()->with('success', 'Status pembayaran berhasil diubah menjadi Lunas!'); //[cite: 8]
    }

    // 3. KELOLA MENU (CRUD & TOGGLE STOK)[cite: 8]
    public function menus()
    {
        $menus = Menu::with('kategoriData')->orderBy('id_menu', 'desc')->get(); //[cite: 8]
        $kategoris = \App\Models\Kategori::all(); //[cite: 8]
        
        return view('admin.menus', compact('menus', 'kategoris')); //[cite: 8]
    }

    // Tambah Menu Baru[cite: 8]
    public function storeMenu(Request $request)
    {
        $request->validate([
            'nama_menu'   => 'required|string|max:100', //[cite: 8]
            'id_kategori' => 'required|integer', //[cite: 8]
            'harga'       => 'required|numeric', //[cite: 8]
            'deskripsi'   => 'nullable|string', //[cite: 8]
            'foto_url'    => 'nullable|string', // Validasi foto_url[cite: 8]
        ]);

        Menu::create([
            'nama_menu'    => $request->nama_menu, //[cite: 8]
            'id_kategori'  => $request->id_kategori, //[cite: 8]
            'harga'        => $request->harga, //[cite: 8]
            'deskripsi'    => $request->deskripsi, //[cite: 8]
            'foto_url'     => $request->foto_url, // Simpan URL Gambar[cite: 8]
            'is_available' => 1, //[cite: 8]
        ]);

        return redirect()->back()->with('success', 'Menu baru berhasil ditambahkan!'); //[cite: 8]
    }

    // Edit Data Menu atau Toggle Status Habis/Tersedia[cite: 8]
    public function updateMenu(Request $request, $id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail(); //[cite: 8]
        
        $menu->update([
            'nama_menu'    => $request->nama_menu ?? $menu->nama_menu, //[cite: 8]
            'id_kategori'  => $request->id_kategori ?? $menu->id_kategori, //[cite: 8]
            'harga'        => $request->harga ?? $menu->harga, //[cite: 8]
            'deskripsi'    => $request->deskripsi ?? $menu->deskripsi, //[cite: 8]
            'foto_url'     => $request->foto_url ?? $menu->foto_url, // Update URL Gambar[cite: 8]
            'is_available' => $request->has('is_available') ? $request->is_available : $menu->is_available, //[cite: 8]
        ]);

        return redirect()->back()->with('success', 'Data menu berhasil diperbarui!'); //[cite: 8]
    }

    // Hapus Menu (Mengembalikan fungsi yang hilang)[cite: 8]
    public function deleteMenu($id)
    {
        $menu = Menu::where('id_menu', $id)->firstOrFail(); //[cite: 8]
        $menu->delete(); //[cite: 8]

        return redirect()->back()->with('success', 'Menu berhasil dihapus!'); //[cite: 8]
    }

    // 4. KELOLA PELANGGAN
    public function customers()
    {
        $pelanggan = Pelanggan::withCount('pesanan')->with('akun')->orderBy('id_pelanggan', 'desc')->get();
        return view('admin.customers', compact('pelanggan'));
    }

    // Hapus Akun & Profil Pelanggan
    public function deleteCustomer($id)
    {
        $customer = Pelanggan::findOrFail($id);
        $id_akun = $customer->id_akun;

        DB::transaction(function () use ($customer, $id_akun) {
            // Hapus profil pelanggan
            $customer->delete();
            // Hapus akun login terkait
            if ($id_akun) {
                \App\Models\Akun::where('id_akun', $id_akun)->delete();
            }
        });

        return redirect()->back()->with('success', 'Data pelanggan beserta akun berhasil dihapus!');
    }

    // 5. CETAK STRUK THERMAL
    public function printReceipt($id)
    {
        $pesanan = Pesanan::with(['detailPesanan.menu', 'pelanggan.akun'])->where('id_pesanan', $id)->firstOrFail();
        
        return view('admin.receipt', compact('pesanan'));
    }

    // 6. EKSPOR LAPORAN PESANAN LUNAS (EXCEL)
    public function exportExcel()
    {
        $fileName = 'laporan_penjualan_' . date('Y-m-d_H-i-s') . '.xls';

        $pesanan = Pesanan::with(['pelanggan.akun', 'detailPesanan.menu'])
            ->where('status_pembayaran', 'Lunas')
            ->orderBy('id_pesanan', 'desc')
            ->get();

        $headers = [
            "Content-type"        => "application/vnd.ms-excel; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Format HTML table yang bisa dibaca langsung oleh Excel dengan rapi
        $html = '
        <html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
            <style>
                table { border-collapse: collapse; font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif; }
                th { background-color: #0F172A; color: #FFFFFF; font-weight: bold; padding: 8px; border: 1px solid #CBD5E1; }
                td { padding: 8px; border: 1px solid #CBD5E1; text-align: left; }
                .price { text-align: right; }
            </style>
        </head>
        <body>
            <h2>Laporan Penjualan Warung Seafood (Lunas)</h2>
            <p>Tanggal Unduh: ' . date('d-m-Y H:i:s') . '</p>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID Pesanan</th>
                        <th>Nama Pelanggan</th>
                        <th>Rincian Menu</th>
                        <th>Total Harga</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($pesanan as $row) {
            $namaPelanggan = $row->pelanggan->nama_pelanggan ?? ($row->pelanggan->akun->nama ?? 'Tamu');

            $rincianArr = [];
            foreach ($row->detailPesanan as $detail) {
                $namaMenu = $detail->menu->nama_menu ?? 'Menu Terhapus';
                $rincianArr[] = $namaMenu . ' (' . $detail->jumlah . 'x)';
            }
            $rincianMenu = implode(', ', $rincianArr);

            $html .= '
                    <tr>
                        <td>#ORD-' . $row->id_pesanan . '</td>
                        <td>' . htmlspecialchars($namaPelanggan) . '</td>
                        <td>' . htmlspecialchars($rincianMenu) . '</td>
                        <td class="price">Rp ' . number_format($row->total_harga, 0, ',', '.') . '</td>
                    </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>';

        return response($html, 200, $headers);
    }
}