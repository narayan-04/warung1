<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Menu;
use App\Models\Pesanan;
use App\Models\DetailPesanan;
use App\Models\Pelanggan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CustomerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // Blokir Penjual agar tidak bisa membuka katalog utama (/)
            new Middleware(function ($request, $next) {
                if (auth()->check() && auth()->user()->role === 'Penjual') {
                    return redirect()->route('admin.dashboard');
                }
                return $next($request);
            }, only: ['index']),

            // Blokir non-Pelanggan dari fitur checkout, status, dan riwayat
            new Middleware(function ($request, $next) {
                if (!auth()->check()) {
                    return redirect()->route('login');
                }
                if (auth()->user()->role !== 'Pelanggan') {
                    return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak. Halaman tersebut hanya untuk Pelanggan.');
                }
                return $next($request);
            }, only: ['checkoutPreview', 'storeCheckout', 'status', 'history']),
        ];
    }
    // 1. KATALOG MENU (Halaman Utama / Landing Page)
    public function index(Request $request)
    {
        // Ambil semua data kategori dari database untuk Tab Filter
        $kategoris = \App\Models\Kategori::all();

        // Filter kategori berdasarkan ID
        $id_kategori = $request->get('kategori'); 
        
        // Eager loading relasi kategoriData
        $query = Menu::with('kategoriData')->where('is_available', 1); 
        
        if ($id_kategori) {
            $query->where('id_kategori', $id_kategori);
        }

        $menus = $query->orderBy('id_menu', 'asc')->get();
        $isWarungBuka = (Cache::get('status_warung', 'buka') === 'buka');

        return view('customer.catalog', compact('menus', 'kategoris', 'id_kategori', 'isWarungBuka'));
    }

    // 2. PREVIEW CHECKOUT (Menampilkan halaman konfirmasi pesanan)
    public function checkoutPreview(Request $request)
    {
        // Proteksi jika warung sedang tutup
        if (Cache::get('status_warung', 'buka') === 'tutup') {
            return redirect()->route('home')->with('error', 'Mohon maaf, warung saat ini sedang TUTUP. Pemesanan tidak dapat dilakukan.');
        }
        if ($request->isMethod('post')) {
            $items = $request->input('items', []); // Array id_menu => qty
            
            $cartItems = [];
            $totalHarga = 0;

            foreach ($items as $id_menu => $qty) {
                if ($qty > 0) {
                    $menu = Menu::with('kategoriData')->find($id_menu);
                    if ($menu) {
                        $subtotal = $menu->harga * $qty;
                        $totalHarga += $subtotal;
                        $cartItems[] = [
                            'menu' => $menu,
                            'qty' => $qty,
                            'subtotal' => $subtotal
                        ];
                    }
                }
            }

            if (empty($cartItems)) {
                return redirect()->back()->with('error', 'Silakan pilih minimal 1 porsi menu terlebih dahulu.');
            }

            // Simpan sementara di session
            session(['cart_checkout' => $cartItems, 'cart_total' => $totalHarga]);
        } else {
            // Jika request GET (misal di-refresh atau redirect setelah login), ambil dari session
            $cartItems = session('cart_checkout', []);
            $totalHarga = session('cart_total', 0);

            if (empty($cartItems)) {
                return redirect()->route('home')->with('error', 'Silakan pilih menu terlebih dahulu.');
            }
        }

        return view('customer.checkout', compact('cartItems', 'totalHarga'));
    }

    // 3. PROSES FINAL SIMPAN PESANAN KE DATABASE
    public function storeCheckout(Request $request)
    {
        if (Cache::get('status_warung', 'buka') === 'tutup') {
            return redirect()->route('home')->with('error', 'Mohon maaf, warung saat ini sedang TUTUP. Pemesanan tidak dapat dilakukan.');
        }

        $request->validate([
            'no_meja'           => 'required|string',
            'metode_pembayaran' => 'required|in:Cash,QRIS',
        ]);

        $cartItems = session('cart_checkout', []);
        $totalHarga = session('cart_total', 0);

        if (empty($cartItems)) {
            return redirect()->route('home')->with('error', 'Keranjang pesanan kosong atau sudah kadaluarsa.');
        }

        $user = Auth::user();
        $pelanggan = Pelanggan::where('id_akun', $user->id_akun)->first();

        if (!$pelanggan) {
            return redirect()->back()->with('error', 'Data profil pelanggan tidak ditemukan.');
        }

        // Simpan Transaksi Utama ke PostgreSQL
        $pesanan = Pesanan::create([
            'id_pelanggan'      => $pelanggan->id_pelanggan,
            'no_meja'           => $request->no_meja,
            'total_harga'       => $totalHarga,
            'status'            => 'Menunggu', // Selaras dengan KDS Dapur Admin
            'status_pembayaran' => 'Belum Bayar',
            'metode_pembayaran' => $request->metode_pembayaran,
            'catatan'           => $request->catatan ?? null,
        ]);

        // Simpan Rincian Menu yang Dipesan
        foreach ($cartItems as $item) {
            DetailPesanan::create([
                'id_pesanan'   => $pesanan->id_pesanan,
                'id_menu'      => $item['menu']['id_menu'],
                'harga_satuan' => $item['menu']['harga'], // Wajib ada karena tabel membutuhkannya
                'jumlah'       => $item['qty'],           // Sesuai dengan kolom 'jumlah' di tabel
                'subtotal'     => $item['subtotal'],
            ]);
        }

        // Hapus session keranjang belanja sementara
        session()->forget(['cart_checkout', 'cart_total']);

        return redirect()->route('customer.status')->with('success', 'Pesanan berhasil dikirim ke dapur!');
    }

    // 4. LACAK STATUS PESANAN (Real-time tracking KDS)
    public function status()
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::where('id_akun', $user->id_akun)->first();

        // Ambil pesanan aktif berdasarkan status di KDS Dapur
        $pesananAktif = Pesanan::with('detailPesanan.menu')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->whereIn('status', ['Menunggu', 'Sedang Dimasak'])
            ->orderBy('id_pesanan', 'desc')
            ->get();

        return view('customer.status', compact('pesananAktif'));
    }

    // 5. RIWAYAT PESANAN
    public function history()
    {
        $user = Auth::user();
        $pelanggan = Pelanggan::where('id_akun', $user->id_akun)->first();

        // Ambil pesanan yang sudah selesai atau dibatalkan
        $riwayatPesanan = Pesanan::with('detailPesanan.menu')
            ->where('id_pelanggan', $pelanggan->id_pelanggan)
            ->whereIn('status', ['Selesai', 'Dibatalkan'])
            ->orderBy('id_pesanan', 'desc')
            ->get();

        return view('customer.history', compact('riwayatPesanan'));
    }
}