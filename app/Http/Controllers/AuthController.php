<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Akun;
use App\Models\Pelanggan;

class AuthController extends Controller
{
    // Tampilkan Halaman Login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses Login & Pemisah Jalur (Penjual vs Pelanggan)
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Cek Role: Jika Penjual langsung ke Dashboard Admin, jika Pelanggan ke Katalog
            if (Auth::user()->role === 'Penjual') {
                return redirect()->intended(route('admin.dashboard'));
            }
            return redirect()->intended(route('home'));
        }

        return back()->withErrors([
            'username' => 'Username atau password yang Anda masukkan salah.',
        ])->onlyInput('username');
    }

    // Tampilkan Halaman Register (Khusus Pelanggan Baru)
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses Pendaftaran Akun Pelanggan Baru
    public function register(Request $request)
    {
        $request->validate([
            'nama_pelanggan' => 'required|string|max:100',
            'no_telp'        => 'required|string|max:20',
            'username'       => 'required|string|max:50|unique:akun,username',
            'password'       => 'required|string|min:6|confirmed',
        ]);

        // 1. Buat Akun Login (Default role: 'Pelanggan')
        $akun = Akun::create([
            'username'      => $request->username,
            'password_hash' => Hash::make($request->password), // <-- Ganti kata 'password' jadi 'password_hash'
            'role'          => 'Pelanggan',
        ]);

        // 2. Buat Profil Pelanggan yang terhubung ke Akun tadi
        Pelanggan::create([
            'id_akun'        => $akun->id_akun,
            'nama_pelanggan' => $request->nama_pelanggan,
            'no_telp'        => $request->no_telp,
        ]);

        // 3. Alihkan ke halaman login agar user melakukan login manual terlebih dahulu
        return redirect()->route('login')->with('success', 'Pendaftaran akun berhasil! Silakan masuk menggunakan akun baru Anda.');
    }

    // Keluar / Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}