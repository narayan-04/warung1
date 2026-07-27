@extends('layouts.customer')

@section('title', 'Masuk - Warung Seafood')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center px-4 py-12">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl border border-slate-200 overflow-hidden p-8">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-[#0F172A] text-white rounded-2xl mx-auto flex items-center justify-center mb-4 shadow-md">
                <span class="material-symbols-outlined text-3xl">anchor</span>
            </div>
            <h2 class="text-2xl font-extrabold text-slate-800">Selamat Datang Kembali!</h2>
            <p class="text-sm text-slate-500 mt-1">Masuk untuk mulai memesan hidangan laut favoritmu.</p>
        </div>

        <!-- Notifikasi Error / Sukses -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-xl mb-6 text-sm font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <!-- Form Login -->
        <form action="{{ route('login.submit') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Username / Email</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">person</span>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                           class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all"
                           placeholder="Masukkan username anda">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-2">Kata Sandi</label>
                <div class="relative">
                    <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">lock</span>
                    <input type="password" name="password" required
                           class="w-full pl-12 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-xl text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all"
                           placeholder="••••••••">
                </div>
            </div>

            <button type="submit" class="w-full bg-[#F97316] hover:bg-orange-600 text-white font-extrabold py-3.5 rounded-xl shadow-lg shadow-orange-500/30 transition-all text-sm flex items-center justify-center gap-2">
                Masuk Sekarang <span class="material-symbols-outlined text-[18px]">login</span>
            </button>
        </form>

        <!-- Footer Link -->
        <div class="text-center mt-8 pt-6 border-t border-slate-100">
            <p class="text-sm text-slate-500">
                Belum punya akun? 
                <a href="{{ route('register') }}" class="text-orange-600 font-bold hover:underline">Daftar di sini</a>
            </p>
        </div>

    </div>
</div>
@endsection