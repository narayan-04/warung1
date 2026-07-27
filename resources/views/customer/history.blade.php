@extends('layouts.customer')

@section('title', 'Riwayat Pesanan - Warung Seafood')

@section('content')
<!-- PREMIUM HERO BANNER WITH BACKGROUND TEXTURE -->
<div class="relative bg-cover bg-center py-6 px-4 overflow-hidden border-b border-slate-800" style="background-image: url('{{ asset('images/hero_bg.png') }}');">
    <div class="absolute inset-0 bg-gradient-to-r from-[#0B132B]/95 via-[#0B132B]/90 to-[#0B132B]/95 backdrop-blur-[1px] z-0"></div>
    <div class="max-w-4xl mx-auto text-center relative z-10">
        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-white">Riwayat Pesanan</h1>
        <p class="text-slate-400 text-xs mt-1">Daftar hidangan lezat yang pernah Anda nikmati di Warung Seafood sebelumnya.</p>
    </div>
</div>

<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 font-['Plus_Jakarta_Sans']">
    <div class="space-y-4">
        @forelse($riwayatPesanan as $pesanan)
            <!-- Sleek Professional History Card -->
            <div class="bg-white rounded-3xl border border-slate-200 shadow-[0_8px_30px_rgb(0,0,0,0.03)] p-6 hover:shadow-[0_15px_35px_rgb(0,0,0,0.08)] transition-all duration-300 flex flex-col md:flex-row md:items-center justify-between gap-6 group">
                
                <!-- Info Kiri: ID, Tanggal & Detail Singkat -->
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-100 text-emerald-600 flex items-center justify-center shrink-0 shadow-inner">
                        <span class="material-symbols-outlined text-2xl font-bold">check_circle</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <h3 class="text-lg font-black text-[#0B132B] group-hover:text-orange-500 transition-colors">#ORD-{{ $pesanan->id_pesanan }}</h3>
                            <span class="bg-slate-100 text-slate-600 text-[9px] font-black px-2.5 py-0.5 rounded-md uppercase tracking-wider">
                                {{ $pesanan->no_meja }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium">
                            {{ $pesanan->detailPesanan->count() }} Hidangan • {{ $pesanan->metode_pembayaran }} • {{ $pesanan->created_at ? $pesanan->created_at->format('d M Y, H:i') : '-' }}
                        </p>
                    </div>
                </div>
                
                <!-- Info Kanan: Harga & Tombol Detail -->
                <div class="flex items-center justify-between md:justify-end gap-6 border-t md:border-t-0 pt-4 md:pt-0 border-slate-100">
                    <div class="flex flex-col md:items-end">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Bayar</span>
                        <span class="text-xl font-black text-slate-800">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex items-center gap-2 shrink-0">
                        <button type="button" 
                            onclick="openDetailModal({{ json_encode([
                                'id' => $pesanan->id_pesanan,
                                'meja' => $pesanan->no_meja,
                                'tgl' => $pesanan->created_at ? $pesanan->created_at->format('d M Y, H:i') : '-',
                                'bayar' => $pesanan->metode_pembayaran,
                                'total' => number_format($pesanan->total_harga, 0, ',', '.'),
                                'catatan' => $pesanan->catatan,
                                'items' => $pesanan->detailPesanan->map(function($d) {
                                    return [
                                        'nama' => $d->menu->nama_menu ?? 'Menu Dihapus',
                                        'harga' => number_format($d->menu->harga ?? 0, 0, ',', '.'),
                                        'qty' => $d->jumlah,
                                        'sub' => number_format($d->subtotal, 0, ',', '.'),
                                        'foto' => $d->menu->foto_url ?? null
                                    ];
                                })
                            ]) }})"
                            class="bg-[#0F172A] hover:bg-orange-500 text-white hover:text-white font-extrabold text-xs px-5 py-3 rounded-2xl transition-all duration-300 flex items-center gap-1.5 shadow-md shadow-slate-900/10 hover:shadow-orange-500/20">
                            <span class="material-symbols-outlined text-[16px]">visibility</span> Detail
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-20 bg-white rounded-3xl border border-slate-200 shadow-sm">
                <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-6 shadow-inner">
                    <span class="material-symbols-outlined text-5xl text-slate-300 block">history</span>
                </div>
                <p class="text-slate-500 font-extrabold text-lg">Belum Ada Riwayat Pesanan</p>
                <p class="text-slate-400 text-sm mt-1 mb-6">Nikmati pesanan seafood pertama Anda hari ini!</p>
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-orange-500 text-white px-6 py-3.5 rounded-2xl font-bold text-xs shadow-md hover:bg-orange-600 transition-colors">
                    <span class="material-symbols-outlined text-[18px]">restaurant_menu</span> Katalog Menu
                </a>
            </div>
        @endforelse
    </div>
</div>

<!-- ============================================== -->
<!-- POP UP DETAIL RIWAYAT PESANAN (MODAL HTML) -->
<!-- ============================================== -->
<div id="detailModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4">
    <!-- Backdrop Blur -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeDetailModal()"></div>
    
    <!-- Modal Box -->
    <div class="bg-white rounded-[2rem] max-w-lg w-full p-6 sm:p-8 relative z-10 shadow-2xl border border-slate-100 transform scale-95 opacity-0 transition-all duration-300 ease-out flex flex-col max-h-[90vh]" id="detailModalBox">
        
        <!-- Header Modal -->
        <div class="flex justify-between items-start border-b border-slate-100 pb-4 mb-5">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Detail Pesanan</span>
                    <span id="modalMeja" class="bg-orange-100 text-orange-700 text-[10px] font-black px-2.5 py-0.5 rounded-md uppercase tracking-wider">
                        Meja
                    </span>
                </div>
                <h3 class="text-2xl font-black text-[#0B132B] tracking-tight" id="modalOrderId">#ORD-X</h3>
            </div>
            <button onclick="closeDetailModal()" class="w-8 h-8 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600 transition-colors">
                <span class="material-symbols-outlined text-[20px]">close</span>
            </button>
        </div>

        <!-- Body Scrollable -->
        <div class="flex-grow overflow-y-auto space-y-6 pr-2">
            <!-- Pembayaran -->
            <div class="bg-slate-50 p-4 rounded-2xl border border-slate-100 text-xs flex justify-between items-center">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Metode Pembayaran</span>
                <span class="font-extrabold text-slate-700" id="modalPembayaran">-</span>
            </div>

            <!-- List Menu Hidangan -->
            <div class="space-y-3">
                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Daftar Hidangan</span>
                <ul id="modalItemList" class="divide-y divide-slate-100">
                    <!-- Dynamic injection -->
                </ul>
            </div>

            <!-- Catatan khusus -->
            <div id="modalCatatanContainer" class="hidden bg-orange-500/5 border border-orange-500/10 rounded-xl p-3 flex gap-2">
                <span class="material-symbols-outlined text-orange-500 text-[18px] shrink-0">edit_note</span>
                <p class="text-[11px] text-slate-600 italic leading-normal">
                    <span class="font-bold not-italic text-slate-800">Catatan Koki:</span> "<span id="modalCatatan">-</span>"
                </p>
            </div>
        </div>

        <!-- Footer Total -->
        <div class="border-t border-slate-100 pt-5 mt-5 space-y-4">
            <div class="flex justify-between items-center">
                <span class="text-sm font-extrabold text-slate-500">Total Pembayaran</span>
                <span class="text-2xl font-black text-orange-600" id="modalTotal">Rp 0</span>
            </div>
            
            <button type="button" onclick="closeDetailModal()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold py-3.5 rounded-xl text-center text-sm transition-colors shadow-sm">
                Tutup Rincian
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<style>
    /* Custom scrollbar untuk list rincian menu dalam modal */
    #detailModalBox div::-webkit-scrollbar {
        width: 4px;
    }
    #detailModalBox div::-webkit-scrollbar-track {
        background: transparent;
    }
    #detailModalBox div::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }
</style>
<script>
    const detailModal = document.getElementById('detailModal');
    const detailModalBox = document.getElementById('detailModalBox');

    function openDetailModal(data) {
        // Set Data Utama
        document.getElementById('modalOrderId').innerText = '#ORD-' + data.id;
        document.getElementById('modalMeja').innerText = data.meja;
        document.getElementById('modalPembayaran').innerText = data.bayar;
        document.getElementById('modalTotal').innerText = 'Rp ' + data.total;

        // Set Catatan
        const catatanContainer = document.getElementById('modalCatatanContainer');
        if (data.catatan) {
            catatanContainer.classList.remove('hidden');
            document.getElementById('modalCatatan').innerText = data.catatan;
        } else {
            catatanContainer.classList.add('hidden');
        }

        // Render Menu Items
        const listContainer = document.getElementById('modalItemList');
        listContainer.innerHTML = ''; // Reset

        data.items.forEach(item => {
            const li = document.createElement('li');
            li.className = 'py-3.5 flex items-center justify-between gap-3 text-sm';
            
            const imgHtml = item.foto 
                ? `<img src="${item.foto}" alt="" class="w-10 h-10 rounded-xl object-cover">`
                : `<div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 shadow-inner">
                    <span class="material-symbols-outlined text-[16px]">restaurant</span>
                   </div>`;

            li.innerHTML = `
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl overflow-hidden shrink-0 bg-slate-100 border border-slate-200 shadow-sm">
                        ${imgHtml}
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 leading-tight line-clamp-1">${item.nama}</h4>
                        <p class="text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">Rp ${item.harga} × ${item.qty} Porsi</p>
                    </div>
                </div>
                <span class="font-extrabold text-slate-700 shrink-0">Rp ${item.sub}</span>
            `;
            listContainer.appendChild(li);
        });

        // Tampilkan Modal
        detailModal.classList.remove('hidden');
        detailModal.classList.add('flex');
        setTimeout(() => {
            detailModalBox.classList.remove('scale-95', 'opacity-0');
            detailModalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeDetailModal() {
        detailModalBox.classList.remove('scale-100', 'opacity-100');
        detailModalBox.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            detailModal.classList.add('hidden');
            detailModal.classList.remove('flex');
        }, 300);
    }
</script>
@endpush