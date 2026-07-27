@extends('layouts.admin')

@section('title', 'Kelola Pesanan - Warung Seafood')

@section('content')
<div class="max-w-container-max mx-auto font-['Plus_Jakarta_Sans']">
    
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-[#0B132B] tracking-tight mb-1">Kelola Pesanan</h1>
            <p class="text-slate-500 text-sm">Kelola dan perbarui status semua pesanan hidangan laut secara real-time.</p>
        </div>
        <a href="{{ route('admin.reports.excel') }}" class="flex items-center gap-2 bg-[#0F172A] hover:bg-emerald-700 text-white px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-md shadow-slate-900/10 hover:shadow-emerald-700/20">
            <span class="material-symbols-outlined text-[18px]">download</span>
            Ekspor Laporan Excel
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl mb-6 flex items-center shadow-sm font-semibold text-sm">
            <span class="material-symbols-outlined mr-2 text-emerald-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Filters & Search Form -->
    <form action="{{ route('admin.orders') }}" method="GET" class="flex flex-col lg:flex-row gap-4 mb-6">
        <!-- Search Input -->
        <div class="relative flex-1">
            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">search</span>
            <input name="search" value="{{ request('search') }}" class="w-full pl-12 pr-4 py-3 bg-white border border-slate-200 rounded-xl text-slate-800 placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 shadow-sm font-medium text-sm transition-all" placeholder="Cari nomor pesanan, meja, atau nama pelanggan..." type="text">
        </div>
        
        <!-- Dropdown Status Filter -->
        <div class="flex gap-4">
            <select name="status_filter" onchange="this.form.submit()" class="appearance-none bg-white border border-slate-200 rounded-xl px-4 py-3 pr-10 font-bold text-sm text-slate-700 focus:outline-none focus:ring-2 focus:ring-orange-500 shadow-sm bg-[url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%2224%22%20height%3D%2224%22%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2357423b%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C%2Fpolyline%3E%3C%2Fsvg%3E')] bg-no-repeat bg-[position:right_12px_center] bg-[length:16px_16px] transition-all cursor-pointer">
                <option value="" {{ !request('status_filter') ? 'selected' : '' }}>Semua Status</option>
                <option value="Menunggu" {{ request('status_filter') == 'Menunggu' ? 'selected' : '' }}>Menunggu (Masuk)</option>
                <option value="Sedang Dimasak" {{ request('status_filter') == 'Sedang Dimasak' ? 'selected' : '' }}>Diproses (Dimasak)</option>
                <option value="Selesai" {{ request('status_filter') == 'Selesai' ? 'selected' : '' }}>Selesai (Disajikan)</option>
            </select>
        </div>
    </form>

    <!-- Status Tabs (Functional Link Pills) -->
    <div class="flex overflow-x-auto gap-3 mb-6 pb-2 hide-scrollbar">
        <a href="{{ route('admin.orders', ['search' => request('search')]) }}" class="whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-bold transition-all shadow-sm {{ !request('status_filter') ? 'bg-[#0F172A] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Semua ({{ $countSemua }})
        </a>
        <a href="{{ route('admin.orders', ['status_filter' => 'Menunggu', 'search' => request('search')]) }}" class="whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-bold transition-all shadow-sm {{ request('status_filter') == 'Menunggu' ? 'bg-[#0F172A] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Menunggu ({{ $countMasuk }})
        </a>
        <a href="{{ route('admin.orders', ['status_filter' => 'Sedang Dimasak', 'search' => request('search')]) }}" class="whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-bold transition-all shadow-sm {{ request('status_filter') == 'Sedang Dimasak' ? 'bg-[#0F172A] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Diproses ({{ $countDiproses }})
        </a>
        <a href="{{ route('admin.orders', ['status_filter' => 'Selesai', 'search' => request('search')]) }}" class="whitespace-nowrap px-5 py-2.5 rounded-full text-xs font-bold transition-all shadow-sm {{ request('status_filter') == 'Selesai' ? 'bg-[#0F172A] text-white' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50' }}">
            Selesai ({{ $countSelesai }})
        </a>
    </div>

    <!-- Order Table / Cards Area -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <!-- Table Header (Hidden on small screens) -->
        <div class="hidden lg:grid grid-cols-12 gap-4 px-6 py-4 bg-slate-50 border-b border-slate-200 font-bold text-xs text-slate-500 uppercase tracking-wider">
            <div class="col-span-1">ID Pesanan</div>
            <div class="col-span-2">Pelanggan</div>
            <div class="col-span-3">Rincian Menu</div>
            <div class="col-span-2 text-right">Total & Bayar</div>
            <div class="col-span-1 text-center">Status</div>
            <div class="col-span-3 text-right">Aksi</div>
        </div>

        <!-- Order Rows -->
        <div class="divide-y divide-slate-100">
            @forelse($pesanan as $p)
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 px-6 py-5 items-center hover:bg-slate-50/50 transition-colors group {{ $p->status == 'Selesai' ? 'opacity-90' : '' }}">
                    
                    <!-- ID & Meja -->
                    <div class="col-span-1 lg:col-span-1 flex items-center gap-2">
                        <span class="lg:hidden text-xs font-black text-slate-400 uppercase">ID: </span>
                        <div class="flex flex-col">
                            <span class="font-extrabold text-sm text-[#0B132B]">#ORD-{{ $p->id_pesanan }}</span>
                            <span class="bg-orange-100 text-orange-700 text-[9px] font-black px-2 py-0.5 rounded mt-1 w-max uppercase tracking-wider">
                                {{ $p->no_meja }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Pelanggan -->
                    <div class="col-span-1 lg:col-span-2">
                        <div class="font-extrabold text-sm text-[#0B132B]">{{ $p->pelanggan->nama_pelanggan ?? ($p->pelanggan->akun->nama ?? 'Tamu') }}</div>
                        <div class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mt-1">Dine In / Takeaway</div>
                    </div>
                    
                    <!-- Rincian Menu -->
                    <div class="col-span-1 lg:col-span-3">
                        <ul class="text-xs text-slate-700 space-y-1 font-semibold">
                            @foreach($p->detailPesanan as $detail)
                                <li>
                                    <span class="text-slate-400 font-extrabold mr-1">{{ $detail->jumlah }}x</span> 
                                    {{ $detail->menu->nama_menu ?? 'Menu Dihapus' }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    
                    <!-- Total & Pembayaran Badge -->
                    <div class="col-span-1 lg:col-span-2 lg:text-right">
                        <span class="lg:hidden text-xs font-black text-slate-400 uppercase mr-2">Total: </span>
                        <span class="font-black text-sm text-[#0B132B]">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</span>
                        <div class="mt-1">
                            @if($p->status_pembayaran == 'Belum Bayar')
                                <span class="inline-flex text-[9px] bg-red-100 text-red-700 font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                                    Belum Bayar
                                </span>
                            @else
                                <span class="inline-flex text-[9px] bg-emerald-100 text-emerald-700 font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                                    Lunas
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Status Dapur Badge -->
                    <div class="col-span-1 lg:col-span-1 lg:text-center mt-2 lg:mt-0">
                        @if($p->status == 'Menunggu')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200/60 uppercase tracking-wider">
                                Menunggu
                            </span>
                        @elseif($p->status == 'Sedang Dimasak')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black bg-blue-50 text-blue-700 border border-blue-200/60 uppercase tracking-wider">
                                Diproses
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-black bg-emerald-50 text-emerald-700 border border-emerald-200/60 uppercase tracking-wider">
                                Selesai
                            </span>
                        @endif
                    </div>
                    
                    <!-- Aksi (Tombol Proses, Selesai, Konfirmasi Lunas) -->
                    <div class="col-span-1 lg:col-span-3 flex items-center lg:justify-end gap-2 mt-4 lg:mt-0 flex-wrap">
                        
                        <!-- TOMBOL KONFIRMASI LUNAS MANDIRI (Jika Belum Bayar) -->
                        @if($p->status_pembayaran == 'Belum Bayar')
                            <form action="{{ route('admin.orders.pay', $p->id_pesanan) }}" method="POST" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-black px-3.5 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-1 hover:scale-105" title="Konfirmasi Lunas">
                                    <span class="material-symbols-outlined text-[16px]">payments</span> Lunas
                                </button>
                            </form>
                        @endif

                        @if($p->status == 'Menunggu')
                            <form action="{{ route('admin.orders.status', $p->id_pesanan) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="Sedang Dimasak">
                                <button type="submit" class="bg-amber-500 hover:bg-amber-600 text-white text-[11px] font-black px-4 py-2.5 rounded-xl transition-all shadow-sm hover:scale-105">
                                    Proses
                                </button>
                            </form>
                        @elseif($p->status == 'Sedang Dimasak')
                            <form action="{{ route('admin.orders.status', $p->id_pesanan) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="Selesai">
                                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-[11px] font-black px-4 py-2.5 rounded-xl transition-all shadow-sm hover:scale-105">
                                    Selesai
                                </button>
                            </form>
                        @endif

                        <!-- TOMBOL CETAK STRUK -->
                        <a href="{{ route('admin.orders.print', $p->id_pesanan) }}" target="_blank" class="p-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-200 rounded-xl transition-colors border border-slate-200" title="Cetak Struk">
                            <span class="material-symbols-outlined text-[18px]">print</span>
                        </a>

                        <!-- Tombol Mata Detail -->
                        <button type="button" onclick="openModal('modalDetail-{{ $p->id_pesanan }}')" class="p-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-200 rounded-xl transition-colors border border-slate-200" title="Lihat Detail">
                            <span class="material-symbols-outlined text-[18px]">visibility</span>
                        </button>
                    </div>
                </div>

                <!-- Modal Detail Pesanan -->
                <div id="modalDetail-{{ $p->id_pesanan }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
                    <div class="bg-white w-full max-w-lg rounded-[2rem] shadow-2xl border border-slate-200 overflow-hidden transform scale-95 opacity-0 transition-all duration-200 modal-content">
                        <!-- Header Modal -->
                        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                            <h2 class="text-lg font-black text-slate-800">Detail Pesanan #ORD-{{ $p->id_pesanan }}</h2>
                            <button onclick="closeModal('modalDetail-{{ $p->id_pesanan }}')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">close</span>
                            </button>
                        </div>
                        
                        <!-- Konten Modal -->
                        <div class="p-6 space-y-6">
                            <!-- Informasi Pelanggan & Meja -->
                            <div class="grid grid-cols-2 gap-4 bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs">
                                <div>
                                    <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-widest mb-1">Pelanggan</span>
                                    <span class="text-sm font-extrabold text-slate-800">{{ $p->pelanggan->nama_pelanggan ?? ($p->pelanggan->akun->nama ?? 'Tamu') }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-widest mb-1">No. Meja</span>
                                    <span class="text-sm font-extrabold text-slate-800">{{ $p->no_meja }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-widest mb-1">Waktu Transaksi</span>
                                    <span class="text-sm font-extrabold text-slate-600">{{ \Carbon\Carbon::parse($p->created_at)->format('d M Y, H:i') }}</span>
                                </div>
                                <div>
                                    <span class="text-[10px] text-slate-400 block font-bold uppercase tracking-widest mb-1">Metode Bayar</span>
                                    <span class="text-sm font-extrabold text-slate-600">{{ $p->metode_pembayaran }}</span>
                                </div>
                            </div>

                            <!-- Rincian Menu -->
                            <div>
                                <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Item Pesanan</h3>
                                <div class="divide-y divide-slate-100 max-h-[200px] overflow-y-auto pr-2">
                                    @foreach($p->detailPesanan as $detail)
                                        <div class="py-3.5 flex justify-between items-center text-sm">
                                            <div>
                                                <span class="font-extrabold text-slate-800">{{ $detail->menu->nama_menu ?? 'Menu Dihapus' }}</span>
                                                <span class="text-slate-400 font-bold ml-2">x{{ $detail->jumlah }}</span>
                                            </div>
                                            <span class="font-extrabold text-slate-700">Rp {{ number_format($detail->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Catatan Khusus -->
                            @if($p->catatan)
                                <div class="bg-orange-50 border border-orange-100 rounded-xl p-3 text-xs text-orange-800 flex gap-2">
                                    <span class="material-symbols-outlined text-orange-500 text-[18px]">edit_note</span>
                                    <div>
                                        <span class="font-bold block mb-0.5">Catatan Pelanggan:</span>
                                        <p class="italic text-slate-700 font-medium">"{{ $p->catatan }}"</p>
                                    </div>
                                </div>
                            @endif

                            <!-- Ringkasan Total & Pembayaran -->
                            <div class="border-t border-slate-100 pt-4 flex justify-between items-center">
                                <div>
                                    <span class="text-xs text-slate-400 block font-extrabold uppercase mb-0.5">Total Pembayaran</span>
                                    <span class="text-2xl font-black text-[#F97316]">Rp {{ number_format($p->total_harga, 0, ',', '.') }}</span>
                                </div>
                                <div>
                                    @if($p->status_pembayaran == 'Belum Bayar')
                                        <span class="bg-red-50 text-red-600 border border-red-200 px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider">
                                            Belum Bayar
                                        </span>
                                    @else
                                        <span class="bg-emerald-50 text-emerald-600 border border-emerald-200 px-3 py-1.5 rounded-xl text-xs font-black uppercase tracking-wider">
                                            Lunas
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Footer Modal -->
                        <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                            <button onclick="closeModal('modalDetail-{{ $p->id_pesanan }}')" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-600 font-bold rounded-xl text-sm transition-colors">Tutup</button>
                            <a href="{{ route('admin.orders.print', $p->id_pesanan) }}" target="_blank" class="px-5 py-2.5 bg-[#0F172A] text-white font-bold rounded-xl text-sm hover:bg-[#0F172A]/90 transition-colors shadow-md flex items-center gap-1.5">
                                <span class="material-symbols-outlined text-[18px]">print</span> Cetak Struk
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-12 text-center text-slate-400">
                    <span class="material-symbols-outlined text-5xl mb-3 opacity-50 block">inbox</span>
                    Belum ada pesanan yang masuk ke sistem.
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Script untuk Modal Detail -->
<script>
    function openModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('.modal-content');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            content.classList.remove('scale-95', 'opacity-0');
            content.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(id) {
        const modal = document.getElementById(id);
        const content = modal.querySelector('.modal-content');
        content.classList.remove('scale-100', 'opacity-100');
        content.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }, 200);
    }
</script>
@endsection