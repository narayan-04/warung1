@extends('layouts.customer')

@section('title', 'Katalog Hidangan Laut - Warung Seafood')

@section('content')

<!-- AESTHETIC COMPACT HERO BANNER WITH BACKGROUND IMAGE -->
<div class="relative bg-cover bg-center py-10 sm:py-12 px-4 overflow-hidden border-b border-slate-800" style="background-image: url('{{ asset('images/hero_bg.png') }}');">
    <!-- Dark Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-r from-[#0B132B]/95 via-[#0B132B]/90 to-[#0B132B]/95 backdrop-blur-[1px] z-0"></div>
    
    <!-- Decorative Ambient Glows -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 pointer-events-none">
        <div class="absolute -top-[20%] -left-[10%] w-[40vw] h-[40vw] rounded-full bg-blue-600/20 blur-[100px]"></div>
        <div class="absolute bottom-[0%] right-[0%] w-[30vw] h-[30vw] rounded-full bg-orange-500/15 blur-[80px]"></div>
    </div>
    
    <div class="max-w-[90rem] mx-auto text-center relative z-10 flex flex-col items-center">
        <div class="inline-flex items-center gap-2 bg-white/10 backdrop-blur-md border border-white/20 text-orange-400 text-xs px-4 py-1.5 rounded-full font-bold uppercase tracking-[0.15em] mb-3 shadow-lg">
            <span class="material-symbols-outlined text-[15px]">restaurant_menu</span> Spesial Tangkapan Harian
        </div>
        <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight mb-3 text-white drop-shadow-lg leading-tight">
            Sajian Laut <span class="text-transparent bg-clip-text bg-gradient-to-r from-orange-400 to-yellow-300">Premium</span>
        </h1>
        <p class="text-slate-300 text-xs sm:text-sm md:text-base max-w-xl mx-auto leading-relaxed font-medium">
            Nikmati kesegaran hidangan laut kualitas terbaik. Diolah dengan bumbu rahasia Nusantara oleh koki berpengalaman kami.
        </p>
    </div>
</div>

<!-- KONTEN KATALOG UTAMA -->
<div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 py-8 pb-32 font-['Plus_Jakarta_Sans'] relative z-20">
    
    <!-- PEMBERITAHUAN WARUNG TUTUP (Floating style) -->
    @if(isset($isWarungBuka) && !$isWarungBuka)
        <div class="relative -mt-10 sm:-mt-12 mb-8">
            <div class="bg-gradient-to-r from-red-600/95 to-red-700/95 backdrop-blur-xl text-white p-5 sm:p-6 rounded-3xl flex flex-col sm:flex-row sm:items-center justify-between gap-5 shadow-[0_20px_40px_rgba(220,38,38,0.2)] border border-red-400/20 max-w-4xl mx-auto ring-4 ring-white/10">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center shrink-0 shadow-inner">
                        <span class="material-symbols-outlined text-white text-2xl animate-pulse">storefront</span>
                    </div>
                    <div>
                        <h3 class="font-black text-lg sm:text-xl tracking-tight">Mohon Maaf, Warung Seafood Sedang TUTUP</h3>
                        <p class="text-sm text-red-100 mt-1 font-medium">Pemesanan sementara waktu tidak dapat dilakukan. Silakan kembali nanti.</p>
                    </div>
                </div>
                <span class="bg-white text-red-600 font-extrabold text-xs px-6 py-3 rounded-full uppercase tracking-wider shrink-0 text-center shadow-lg">
                    Tutup Sementara
                </span>
            </div>
        </div>
    @endif

    <!-- ALERTS -->
    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-2xl mb-8 flex items-center gap-3 shadow-sm max-w-3xl mx-auto">
            <span class="material-symbols-outlined text-emerald-500">check_circle</span>
            <span class="font-bold text-sm">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 p-4 rounded-2xl mb-8 flex items-center gap-3 shadow-sm max-w-3xl mx-auto">
            <span class="material-symbols-outlined text-red-500">error</span>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <!-- TAB FILTER KATEGORI (Premium Pills) -->
    <div class="flex flex-nowrap overflow-x-auto items-center sm:justify-center gap-3 mb-12 pb-4 scrollbar-hide pt-4">
        <a href="{{ url('/') }}" class="shrink-0 px-6 py-3 rounded-full text-sm font-extrabold transition-all duration-300 {{ !request('kategori') ? 'bg-[#0B132B] text-white shadow-lg shadow-slate-900/20 scale-105' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:shadow-md' }}">
            Semua Menu
        </a>
        @foreach($kategoris ?? [] as $kat)
            <a href="{{ url('/?kategori=' . $kat->id_kategori) }}" class="shrink-0 px-6 py-3 rounded-full text-sm font-extrabold transition-all duration-300 {{ request('kategori') == $kat->id_kategori ? 'bg-[#0B132B] text-white shadow-lg shadow-slate-900/20 scale-105' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:shadow-md' }}">
                {{ $kat->nama_kategori }}
            </a>
        @endforeach
    </div>

    <!-- FORM DRAFT PESANAN -->
    <form action="{{ route('checkout.preview') }}" method="POST" id="cartForm">
        @csrf
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6 sm:gap-8">
            @forelse($menus as $menu)
                <div class="group bg-white rounded-[2rem] border-0 overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_20px_40px_rgb(0,0,0,0.12)] hover:-translate-y-2 transition-all duration-500 flex flex-col">
                    <!-- Foto Menu (Aspect Ratio) -->
                    <div class="relative aspect-[4/3] w-full bg-slate-100 overflow-hidden">
                        @if($menu->foto_url)
                            <img src="{{ $menu->foto_url }}" alt="{{ $menu->nama_menu }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-in-out">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <span class="material-symbols-outlined text-5xl text-slate-400/50">restaurant</span>
                            </div>
                        @endif
                        
                        <!-- Overlay Gradient On Hover -->
                        <div class="absolute inset-0 bg-gradient-to-t from-[#0B132B]/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 pointer-events-none"></div>
                        
                        <!-- Floating Badge Kategori -->
                        <span class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm text-slate-900 text-[10px] font-black px-4 py-2 rounded-xl uppercase tracking-widest shadow-sm">
                            {{ $menu->kategoriData->nama_kategori ?? 'Umum' }}
                        </span>
                    </div>

                    <!-- Info Menu -->
                    <div class="p-6 flex-grow flex flex-col bg-white">
                        <div class="flex-grow">
                            <h3 class="font-extrabold text-xl text-slate-800 leading-tight mb-2 group-hover:text-orange-500 transition-colors line-clamp-1" title="{{ $menu->nama_menu }}">{{ $menu->nama_menu }}</h3>
                            <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed mb-4">{{ $menu->deskripsi ?? 'Hidangan laut spesial dengan olahan bumbu khas warung yang menggugah selera.' }}</p>
                        </div>
                        
                        <div class="flex items-end justify-between mt-auto pt-2">
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Harga Spesial</p>
                                <div class="font-black text-orange-600 text-2xl">
                                    Rp {{ number_format($menu->harga, 0, ',', '.') }}
                                </div>
                            </div>
                        </div>

                        <!-- Area Interaktif (Tombol Tambah) -->
                        <div class="mt-6 pt-5 border-t border-slate-100">
                            @if(isset($isWarungBuka) && !$isWarungBuka)
                                <button type="button" disabled class="w-full bg-slate-100 text-slate-400 font-bold py-3.5 rounded-[1.25rem] text-sm cursor-not-allowed flex items-center justify-center gap-2">
                                    <span class="material-symbols-outlined text-[18px]">block</span> Warung Tutup
                                </button>
                            @else
                                @auth
                                    @if(Auth::user()->role === 'Pelanggan')
                                        <!-- Tombol Tambah Awal -->
                                        <div id="btn-tambah-{{ $menu->id_menu }}">
                                            <button type="button" onclick="activateItem({{ $menu->id_menu }})" class="w-full bg-[#0F172A] hover:bg-orange-500 text-white font-bold py-3.5 rounded-[1.25rem] text-sm transition-all duration-300 flex items-center justify-center gap-2 shadow-lg shadow-slate-900/10 hover:shadow-orange-500/30">
                                                <span class="material-symbols-outlined text-[18px]">add_shopping_cart</span> Tambah Pesanan
                                            </button>
                                        </div>

                                        <!-- Kontrol Plus Minus (Premium Style) -->
                                        <div id="qty-control-{{ $menu->id_menu }}" class="hidden bg-slate-50 p-1.5 rounded-[1.25rem] border border-slate-200 flex items-center justify-between shadow-inner">
                                            <button type="button" onclick="updateQty({{ $menu->id_menu }}, -1)" class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center hover:bg-slate-200 text-slate-700 shadow-sm transition-colors shrink-0">
                                                <span class="material-symbols-outlined text-[20px]">remove</span>
                                            </button>
                                            
                                            <div class="flex flex-col items-center">
                                                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Jumlah</span>
                                                <input type="number" id="input-{{ $menu->id_menu }}" name="items[{{ $menu->id_menu }}]" min="0" value="0" class="w-12 bg-transparent font-black text-center text-lg focus:outline-none text-slate-800 leading-none" readonly>
                                            </div>
                                            
                                            <button type="button" onclick="updateQty({{ $menu->id_menu }}, 1)" class="w-10 h-10 rounded-xl bg-orange-500 text-white flex items-center justify-center hover:bg-orange-600 shadow-sm shadow-orange-500/30 transition-colors shrink-0">
                                                <span class="material-symbols-outlined text-[20px]">add</span>
                                            </button>
                                        </div>
                                    @endif
                                @else
                                    <a href="{{ route('login') }}" class="flex items-center justify-center gap-2 w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold py-3.5 rounded-[1.25rem] text-sm transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">lock</span> Login untuk Memesan
                                    </a>
                                @endauth
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-28 bg-white rounded-[2rem] border border-slate-200 shadow-sm">
                    <div class="w-24 h-24 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6">
                        <span class="material-symbols-outlined text-6xl text-slate-300 block">inventory_2</span>
                    </div>
                    <h3 class="text-2xl font-extrabold text-slate-800">Katalog Sedang Kosong</h3>
                    <p class="text-slate-500 text-base mt-2 max-w-md mx-auto">Koki kami sedang menyiapkan menu-menu lezat. Silakan periksa kembali beberapa saat lagi.</p>
                </div>
            @endforelse
        </div>

        <!-- STICKY BOTTOM BAR UNTUK CHECKOUT (Premium Floating Bar) -->
        @auth
            @if(Auth::user()->role === 'Pelanggan' && ($isWarungBuka ?? true))
                <div class="fixed bottom-6 left-1/2 -translate-x-1/2 w-[95%] max-w-3xl bg-[#0F172A]/95 backdrop-blur-xl border border-white/10 rounded-[2rem] p-3 sm:p-4 shadow-[0_20px_40px_rgba(0,0,0,0.3)] z-50 transform transition-all duration-500 translate-y-[150%] opacity-0" id="stickyCheckoutBar">
                    <div class="flex items-center justify-between pl-4 sm:pl-6">
                        <div>
                            <p class="text-[10px] sm:text-xs text-slate-400 font-bold uppercase tracking-widest mb-1">Total Pesanan Anda</p>
                            <p class="text-xl sm:text-2xl font-black text-white drop-shadow-sm" id="totalPriceDisplay">Rp 0</p>
                        </div>
                        <button type="submit" class="bg-gradient-to-r from-orange-500 to-orange-400 hover:from-orange-400 hover:to-orange-500 text-white font-extrabold py-3.5 px-6 sm:px-8 rounded-[1.5rem] shadow-lg shadow-orange-500/30 transition-all duration-300 flex items-center gap-2 text-sm sm:text-base hover:scale-105">
                            Selesaikan <span class="material-symbols-outlined text-[20px]">arrow_forward</span>
                        </button>
                    </div>
                </div>
            @endif
        @endauth
    </form>
</div>
@endsection

@push('scripts')
<style>
    /* Sembunyikan scrollbar untuk filter kategori */
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>
<script>
    // Data Harga dari Server ke Javascript
    const menuPrices = {
        @foreach($menus as $menu)
            {{ $menu->id_menu }}: {{ $menu->harga }},
        @endforeach
    };

    // Saat tombol Tambah diklik pertama kali
    function activateItem(id) {
        document.getElementById('btn-tambah-' + id).classList.add('hidden');
        document.getElementById('qty-control-' + id).classList.remove('hidden');
        updateQty(id, 1);
    }

    // Fungsi Plus Minus
    function updateQty(id, change) {
        const input = document.getElementById('input-' + id);
        let currentVal = parseInt(input.value);
        let newVal = currentVal + change;
        
        if (newVal < 0) newVal = 0;
        input.value = newVal;

        if (newVal === 0) {
            document.getElementById('btn-tambah-' + id).classList.remove('hidden');
            document.getElementById('qty-control-' + id).classList.add('hidden');
        }

        calculateTotal();
    }

    // Kalkulasi Total dan Munculkan Sticky Bar
    function calculateTotal() {
        let total = 0;
        let itemsCount = 0;
        
        for (const id in menuPrices) {
            const input = document.getElementById('input-' + id);
            if (input) {
                const qty = parseInt(input.value);
                if (qty > 0) {
                    total += qty * menuPrices[id];
                    itemsCount += qty;
                }
            }
        }

        const formattedTotal = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(total);
        document.getElementById('totalPriceDisplay').innerText = formattedTotal;

        const stickyBar = document.getElementById('stickyCheckoutBar');
        if (itemsCount > 0) {
            // Animasi muncul ala Apple
            stickyBar.classList.remove('translate-y-[150%]', 'opacity-0');
        } else {
            stickyBar.classList.add('translate-y-[150%]', 'opacity-0');
        }
    }
</script>
@endpush