@extends('layouts.customer')

@section('title', 'Daftar Akun - Warung Seafood')

@section('content')
<div class="min-h-[85vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-lg w-full bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden p-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-[#0F172A] text-white rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-md">
                <span class="material-symbols-outlined text-3xl">person_add</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-800">Buat Akun Pelanggan</h2>
            <p class="text-sm text-slate-500 mt-1">Nikmati kemudahan memesan menu lezat di Warung Seafood.</p>
        </div>

        <!-- Notifikasi Error -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Form Register -->
        <form action="{{ route('register.submit') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                <input type="text" name="nama_pelanggan" value="{{ old('nama_pelanggan') }}" required
                       class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all"
                       placeholder="Contoh: Budi Santoso">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Nomor Telepon</label>
                    <input type="text" name="no_telp" value="{{ old('no_telp') }}" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all"
                           placeholder="08123456789">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all"
                           placeholder="budi_user">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                <textarea name="alamat" rows="2" required
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all resize-none"
                          placeholder="Jalan Mawar No. 123">{{ old('alamat') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all"
                           placeholder="••••••••">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Konfirmasi Sandi</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all"
                           placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="w-full mt-2 bg-[#F97316] hover:bg-orange-600 text-white font-extrabold py-3.5 rounded-xl shadow-lg shadow-orange-500/30 transition-all text-sm flex items-center justify-center gap-2">
                Daftar Akun Baru <span class="material-symbols-outlined text-[18px]">how_to_reg</span>
            </button>
        </form>

        <!-- Footer Link -->
        <div class="text-center mt-6 pt-6 border-t border-slate-100">
            <p class="text-sm text-slate-500">
                Sudah punya akun? 
                <a href="{{ route('login') }}" class="text-orange-600 font-bold hover:underline">Masuk di sini</a>
            </p>
        </div>

    </div>
</div>
@endsection