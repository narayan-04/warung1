@extends('layouts.customer')

@section('title', 'Status Pesanan Saya - Warung Seafood')

@section('content')
<!-- PREMIUM HERO BANNER WITH BACKGROUND TEXTURE -->
<div class="relative bg-cover bg-center py-6 px-4 overflow-hidden border-b border-slate-800" style="background-image: url('{{ asset('images/hero_bg.png') }}');">
    <div class="absolute inset-0 bg-gradient-to-r from-[#0B132B]/95 via-[#0B132B]/90 to-[#0B132B]/95 backdrop-blur-[1px] z-0"></div>
    <div class="max-w-6xl mx-auto text-center relative z-10">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Status Pesanan Saya</h1>
        <p class="text-slate-400 text-xs mt-1">Pantau pesanan Anda yang sedang diproses oleh koki kami secara real-time.</p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 font-['Plus_Jakarta_Sans']">
    
    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 p-4 rounded-2xl flex items-center gap-3 shadow-sm mb-8">
            <span class="material-symbols-outlined text-green-500">check_circle</span>
            <span class="font-semibold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    <div class="space-y-8">
        @forelse($pesananAktif as $pesanan)
            <!-- SINGLE UNIFIED CARD FOR TRACKER & DETAILS -->
            <div class="bg-white rounded-[2rem] border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.03)] overflow-hidden">
                
                <!-- HEADER KOTAK: Info Order ID, Waktu & Pembayaran -->
                <div class="p-6 border-b border-slate-100 bg-slate-50/50 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">No. Pesanan</span>
                            <span class="bg-orange-100 text-orange-700 border border-orange-200 text-[10px] font-black px-3 py-1 rounded-full uppercase tracking-wider">
                                {{ $pesanan->no_meja }}
                            </span>
                        </div>
                        <h2 class="text-2xl font-black text-[#0B132B] tracking-tight">#ORD-{{ $pesanan->id_pesanan }}</h2>
                        <p class="text-xs text-slate-500 mt-1.5 flex items-center gap-1.5 font-medium">
                            <span class="material-symbols-outlined text-[16px] text-slate-400">schedule</span> 
                            Dibuat pada {{ $pesanan->created_at ? $pesanan->created_at->format('H:i') : 'Baru saja' }}
                        </p>
                    </div>
                    
                    <div class="flex items-center gap-2 shrink-0">
                        @if($pesanan->status_pembayaran == 'Lunas')
                            <span class="bg-emerald-50 text-emerald-700 border border-emerald-200/60 px-4 py-2 rounded-2xl text-xs font-extrabold flex items-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-emerald-600">check_circle</span> Lunas ({{ $pesanan->metode_pembayaran }})
                            </span>
                        @else
                            <span class="bg-amber-50 text-amber-700 border border-amber-200/60 px-4 py-2 rounded-2xl text-xs font-extrabold flex items-center gap-1.5 shadow-sm">
                                <span class="material-symbols-outlined text-[16px] text-amber-600">pending</span> Belum Bayar ({{ $pesanan->metode_pembayaran }})
                            </span>
                        @endif
                    </div>
                </div>

                <!-- CONTENT AREA: Berdampingan Kiri-Kanan -->
                <div class="grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-slate-100">
                    
                    <!-- SISI KIRI (Tracker Status) - col-span-7 -->
                    <div class="p-6 sm:p-8 lg:col-span-7 flex flex-col justify-center space-y-8">
                        <div class="text-center sm:text-left">
                            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest mb-2">Perkembangan Pesanan</h3>
                            <p class="text-slate-600 text-sm font-medium">Hidangan Anda sedang diproses oleh kru dapur kami di tempat.</p>
                        </div>

                        <!-- Horizontal Progress Tracker -->
                        <div class="relative px-2 sm:px-6 py-4">
                            <!-- Progress Line Backing -->
                            <div class="absolute top-9 left-12 right-12 h-1.5 bg-slate-100 rounded-full z-0">
                                <div class="absolute top-0 left-0 h-full bg-teal-500 rounded-full transition-all duration-500" 
                                    style="width: {{ $pesanan->status == 'Menunggu' ? '0%' : ($pesanan->status == 'Sedang Dimasak' ? '50%' : '100%') }}"></div>
                            </div>
                            
                            <!-- Steps circles -->
                            <div class="flex justify-between relative z-10">
                                <!-- Step 1: Diterima / Menunggu -->
                                <div class="flex flex-col items-center w-1/3 text-center gap-2.5">
                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl {{ in_array($pesanan->status, ['Menunggu', 'Sedang Dimasak', 'Selesai']) ? 'bg-teal-600 text-white shadow-md shadow-teal-600/20' : 'bg-slate-200 text-slate-400' }} flex items-center justify-center transition-all duration-300">
                                        <span class="material-symbols-outlined text-[20px] font-bold">check</span>
                                    </div>
                                    <div>
                                        <p class="text-xs sm:text-sm font-extrabold text-slate-800 leading-tight">Diterima</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Masuk Dapur</p>
                                    </div>
                                </div>

                                <!-- Step 2: Sedang Dimasak -->
                                <div class="flex flex-col items-center w-1/3 text-center gap-2.5">
                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl {{ in_array($pesanan->status, ['Sedang Dimasak', 'Selesai']) ? 'bg-orange-500 text-white shadow-md shadow-orange-500/20 ring-4 ring-orange-100' : 'bg-slate-200 text-slate-400' }} flex items-center justify-center transition-all duration-300">
                                        <span class="material-symbols-outlined text-[20px] font-bold">skillet</span>
                                    </div>
                                    <div>
                                        <p class="text-xs sm:text-sm font-extrabold {{ in_array($pesanan->status, ['Sedang Dimasak', 'Selesai']) ? 'text-orange-600' : 'text-slate-400' }} leading-tight">Dimasak</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Estimasi 15 Mnt</p>
                                    </div>
                                </div>

                                <!-- Step 3: Siap Disajikan -->
                                <div class="flex flex-col items-center w-1/3 text-center gap-2.5">
                                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl {{ $pesanan->status == 'Selesai' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/20' : 'bg-slate-200 text-slate-400' }} flex items-center justify-center transition-all duration-300">
                                        <span class="material-symbols-outlined text-[20px] font-bold">room_service</span>
                                    </div>
                                    <div>
                                        <p class="text-xs sm:text-sm font-extrabold {{ $pesanan->status == 'Selesai' ? 'text-emerald-700' : 'text-slate-400' }} leading-tight">Siap Disajikan</p>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-0.5">Meja Anda</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SISI KANAN (Rincian Menu yang Dipesan) - col-span-5 -->
                    <div class="p-6 sm:p-8 lg:col-span-5 bg-slate-50/30 flex flex-col justify-between">
                        <div class="space-y-4">
                            <h3 class="text-sm font-black text-slate-400 uppercase tracking-widest flex items-center gap-2 pb-3 border-b border-slate-100">
                                <span class="material-symbols-outlined text-orange-500 text-[18px]">receipt</span> Rincian Menu
                            </h3>

                            <!-- Menu Items List -->
                            <ul class="divide-y divide-slate-100 max-h-[220px] overflow-y-auto pr-2">
                                @foreach($pesanan->detailPesanan as $detail)
                                    <li class="py-3 flex items-center justify-between gap-3 text-sm">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 bg-slate-100 border border-slate-200 shadow-sm">
                                                @if($detail->menu && $detail->menu->foto_url)
                                                    <img src="{{ $detail->menu->foto_url }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center text-slate-400">
                                                        <span class="material-symbols-outlined text-[16px]">restaurant</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-slate-800 leading-tight line-clamp-1" title="{{ $detail->menu->nama_menu ?? 'Menu Dihapus' }}">
                                                    {{ $detail->menu->nama_menu ?? 'Menu Dihapus' }}
                                                </h4>
                                                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">
                                                    {{ $detail->jumlah }} Porsi
                                                </p>
                                            </div>
                                        </div>
                                        <span class="font-extrabold text-slate-700 shrink-0">
                                            Rp {{ number_format($detail->subtotal, 0, ',', '.') }}
                                        </span>
                                    </li>
                                @endforeach
                            </ul>

                            <!-- Catatan Pesanan -->
                            @if($pesanan->catatan)
                                <div class="bg-orange-500/5 border border-orange-500/10 rounded-xl p-3 flex gap-2">
                                    <span class="material-symbols-outlined text-orange-500 text-[18px] shrink-0">edit_note</span>
                                    <p class="text-[11px] text-slate-600 italic leading-normal">
                                        <span class="font-bold not-italic text-slate-800">Catatan:</span> "{{ $pesanan->catatan }}"
                                    </p>
                                </div>
                            @endif
                        </div>

                        <!-- Financials / Subtotal & Total -->
                        <div class="border-t border-slate-100 pt-4 mt-6 space-y-2">
                            <div class="flex justify-between items-center text-xs text-slate-500 font-semibold">
                                <span>Subtotal</span>
                                <span>Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                            </div>
                            <div class="flex justify-between items-center mt-3 pt-3 border-t border-dashed border-slate-200">
                                <span class="text-sm font-black text-slate-800">Total Pembayaran</span>
                                <span class="text-xl font-black text-orange-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- CARD ACTION BUTTONS (Melayang di bawah kartu pesanan) -->
            <section class="flex flex-col sm:flex-row gap-4 justify-end">
                <button onclick="window.location.reload();" class="px-6 py-3.5 rounded-2xl border border-slate-200 bg-white text-[#0B132B] text-xs font-black hover:bg-slate-50 hover:shadow-md transition-all flex items-center justify-center gap-2 shadow-sm">
                    <span class="material-symbols-outlined text-[18px]">refresh</span> Refresh Status
                </button>
                <a href="{{ route('home') }}" class="px-6 py-3.5 rounded-2xl bg-orange-500 text-white text-xs font-black hover:bg-orange-600 hover:shadow-orange-500/30 transition-all shadow-lg shadow-orange-500/15 flex items-center justify-center gap-2">
                    <span class="material-symbols-outlined text-[18px]">add</span> Tambah Pesanan Menu
                </a>
            </section>
        @empty
            <div class="text-center py-24 bg-white rounded-[2rem] border border-slate-200 shadow-sm max-w-4xl mx-auto">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <span class="material-symbols-outlined text-5xl text-slate-300 block">soup_kitchen</span>
                </div>
                <h3 class="text-xl font-extrabold text-slate-800">Belum Ada Pesanan Aktif</h3>
                <p class="text-slate-500 text-sm mt-2 mb-6 max-w-sm mx-auto">Anda tidak memiliki pesanan aktif yang sedang diproses di dapur saat ini.</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-orange-500 text-white px-6 py-3.5 rounded-2xl font-bold text-xs shadow-md hover:bg-orange-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">restaurant_menu</span> Katalog Menu
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Custom scrollbar untuk list rincian menu */
    ul::-webkit-scrollbar {
        width: 4px;
    }
    ul::-webkit-scrollbar-track {
        background: transparent;
    }
    ul::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>
@endpush