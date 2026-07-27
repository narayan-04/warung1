@extends('layouts.admin')

@section('title', 'Dashboard Ringkasan - Warung Seafood')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 font-['Plus_Jakarta_Sans']">
    
    <!-- HEADER DASHBOARD RINGKASAN & TOP CONTROLS -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight">Dashboard Ringkasan</h1>
            <p class="text-slate-500 text-sm mt-1">Selamat datang kembali, {{ Auth::user()->username ?? 'Narayan' }}! Berikut adalah performa Warung Seafood hari ini.</p>
        </div>

        <div class="flex items-center gap-3">
            <!-- TOGGLE WARUNG BUKA / TUTUP -->
            <button type="button" id="btnStatusWarung" onclick="toggleStatusWarung()" class="inline-flex items-center gap-2 {{ ($statusWarung ?? 'buka') == 'buka' ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-emerald-100' : 'bg-red-50 text-red-700 border-red-200 hover:bg-red-100' }} border px-4 py-2 rounded-full text-xs font-bold transition-all shadow-sm">
                <span id="dotStatusWarung" class="w-2.5 h-2.5 rounded-full {{ ($statusWarung ?? 'buka') == 'buka' ? 'bg-emerald-500 animate-pulse' : 'bg-red-500' }}"></span>
                <span id="textStatusWarung">{{ ($statusWarung ?? 'buka') == 'buka' ? 'Warung Buka' : 'Warung Tutup' }}</span>
            </button>

            <!-- FILTER PERIODE WAKTU -->
            <form action="{{ route('admin.dashboard') }}" method="GET" id="filterForm">
                <select name="periode" onchange="document.getElementById('filterForm').submit()" class="bg-white border border-slate-200 text-slate-700 text-xs font-bold rounded-xl px-4 py-2.5 shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500 cursor-pointer">
                    <option value="hari_ini" {{ request('periode') == 'hari_ini' ? 'selected' : '' }}>Hari ini</option>
                    <option value="kemarin" {{ request('periode') == 'kemarin' ? 'selected' : '' }}>Kemarin</option>
                    <option value="minggu_ini" {{ request('periode') == 'minggu_ini' ? 'selected' : '' }}>Minggu ini</option>
                </select>
            </form>
        </div>
    </div>

    <!-- KPI CARDS GRID (4 KOLOM) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1: Total Pendapatan (Dynamic Trend) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex justify-between items-start hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Total Pendapatan</p>
                <h3 class="text-2xl font-black text-slate-800">Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}</h3>
                <span class="inline-flex items-center gap-1 text-xs font-bold {{ ($trendPendapatanUp ?? true) ? 'text-emerald-600' : 'text-red-600' }} mt-2">
                    <span class="material-symbols-outlined text-[14px]">{{ ($trendPendapatanUp ?? true) ? 'trending_up' : 'trending_down' }}</span> {{ $trendPendapatanText ?? '100% Lunas' }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-orange-50 text-orange-500 flex items-center justify-center shrink-0 border border-orange-100">
                <span class="material-symbols-outlined text-2xl">account_balance_wallet</span>
            </div>
        </div>

        <!-- Card 2: Pesanan Masuk -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex justify-between items-start hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pesanan Masuk</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalPesananHariIni }} Order</h3>
                <span class="text-xs font-semibold text-slate-400 mt-2 block">100% Lunas / Selesai</span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-500 flex items-center justify-center shrink-0 border border-indigo-100">
                <span class="material-symbols-outlined text-2xl">shopping_bag</span>
            </div>
        </div>

        <!-- Card 3: Hidangan Terjual -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex justify-between items-start hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Hidangan Terjual</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $hidanganTerjual }} Porsi</h3>
                <span class="text-xs font-semibold text-slate-400 mt-2 block truncate max-w-[140px]">
                    {{ $menuTerlaris->first()->nama_menu ?? 'Seafood' }} terlaris
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0 border border-amber-100">
                <span class="material-symbols-outlined text-2xl">restaurant</span>
            </div>
        </div>

        <!-- Card 4: Pelanggan Baru (Dynamic Count) -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex justify-between items-start hover:shadow-md transition-shadow">
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Pelanggan Baru</p>
                <h3 class="text-2xl font-black text-slate-800">{{ $totalPelangganBaru }} Orang</h3>
                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-600 mt-2">
                    <span class="material-symbols-outlined text-[14px]">arrow_upward</span> {{ $pelangganBaruHariIniText ?? '+0 mendaftar hari ini' }}
                </span>
            </div>
            <div class="w-12 h-12 rounded-2xl bg-purple-50 text-purple-500 flex items-center justify-center shrink-0 border border-purple-100">
                <span class="material-symbols-outlined text-2xl">person_add</span>
            </div>
        </div>
    </div>

    <!-- MIDDLE SECTION: STATISTIK PENDAPATAN & SIDEBAR KANAN -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- 1. STATISTIK PENDAPATAN MINGGU INI (2 KOLOM) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 p-6 sm:p-8 shadow-sm flex flex-col justify-between">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">Statistik Pendapatan Minggu Ini</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Grafik performa omzet harian Senin hingga Minggu.</p>
                </div>
                <span class="text-xs font-bold bg-slate-100 text-slate-600 px-3 py-1.5 rounded-full">
                    Total Rp {{ number_format($totalWeeklyRevenue, 0, ',', '.') }}
                </span>
            </div>

            <!-- Visual Bar Chart Pendapatan -->
            <div class="pt-6 border-t border-slate-100">
                <div class="h-64 flex items-end justify-between gap-3 sm:gap-6 px-2">
                    @php
                        $maxVal = max($chartData ?? [1]);
                        if ($maxVal == 0) $maxVal = 1;
                    @endphp
                    
                    @foreach(($chartData ?? []) as $day => $amount)
                        @php
                            $hasRevenue = ($amount > 0);
                            $heightPercent = $hasRevenue ? min(100, max(15, round(($amount / $maxVal) * 100))) : 8;
                            $isPeak = ($hasRevenue && $amount == $maxVal);
                        @endphp
                        <div class="flex-1 flex flex-col items-center h-full justify-end group relative">
                            <!-- Tooltip Nominal -->
                            <div class="opacity-0 group-hover:opacity-100 transition-opacity absolute -top-10 bg-slate-900 text-white text-[10px] font-bold px-2.5 py-1 rounded-md whitespace-nowrap z-10 shadow-lg pointer-events-none">
                                Rp {{ number_format($amount, 0, ',', '.') }}
                            </div>
                            
                            <!-- Batang Grafik -->
                            <div style="height: {{ $heightPercent }}%;" class="w-full rounded-t-xl transition-all duration-500 relative overflow-hidden {{ $isPeak ? 'bg-gradient-to-t from-orange-600 to-orange-400 shadow-md shadow-orange-500/30' : ($hasRevenue ? 'bg-orange-300' : 'bg-slate-100') }}">
                            </div>
                            
                            <!-- Label Hari -->
                            <span class="text-xs font-bold text-slate-500 mt-3">{{ $day }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- SIDEBAR KANAN: MENU TERLARIS & PERINGATAN STOK -->
        <div class="space-y-6">
            <!-- Kartu Menu Terlaris -->
            <div class="bg-white rounded-3xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                    <h2 class="text-base font-bold text-slate-800 flex items-center gap-2">
                        <span class="material-symbols-outlined text-orange-500 text-[20px]">local_fire_department</span> Menu Terlaris
                    </h2>
                </div>

                <div class="space-y-4">
                    @forelse(($menuTerlaris ?? []) as $index => $item)
                        @php
                            $topQty = $menuTerlaris->first()->total_terjual ?? 1;
                            $barWidth = round(($item->total_terjual / max(1, $topQty)) * 100);
                        @endphp
                        <div>
                            <div class="flex justify-between items-center text-xs font-bold mb-1.5">
                                <span class="text-slate-800 truncate max-w-[180px]">{{ $item->nama_menu }}</span>
                                <span class="text-slate-500">{{ $item->total_terjual }} Porsi</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-gradient-to-r from-orange-500 to-amber-500 h-full rounded-full transition-all duration-500" style="width: {{ $barWidth }}%;"></div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-6 text-slate-400 text-xs">Belum ada data penjualan menu.</div>
                    @endforelse
                </div>
            </div>

            <!-- Kartu Peringatan Stok / Habis (Dynamic Check) -->
            <div class="bg-red-50/60 rounded-3xl border border-red-100 p-6 shadow-sm">
                <div class="flex items-center gap-2 text-red-700 font-bold text-sm mb-3">
                    <span class="material-symbols-outlined text-[20px]">warning</span> Peringatan Stok / Habis
                </div>
                
                @if(isset($menuHabisList) && count($menuHabisList) > 0)
                    <ul class="space-y-2 mb-4">
                        @foreach($menuHabisList as $habis)
                            <li class="flex justify-between items-center text-xs">
                                <span class="font-bold text-slate-700">{{ $habis->nama_menu }}</span>
                                <span class="bg-red-200 text-red-800 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase">HABIS</span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="flex items-center gap-2 text-xs text-emerald-700 font-bold mb-4 bg-emerald-50 border border-emerald-200 p-3 rounded-xl">
                        <span class="material-symbols-outlined text-[18px] text-emerald-600">check_circle</span>
                        Semua menu tersedia untuk dipesan hari ini.
                    </div>
                @endif

                <a href="{{ route('admin.menus') }}" class="block w-full bg-white hover:bg-red-50 text-red-600 border border-red-200 text-xs font-bold py-2.5 rounded-xl text-center transition-colors shadow-sm">
                    Perbarui Stok
                </a>
            </div>
        </div>
    </div>

    <!-- 5 TRANSAKSI TERAKHIR (DIPINDAH KE BAWAH) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden p-6 sm:p-8">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-lg font-bold text-slate-800">5 Transaksi Terakhir</h2>
                <p class="text-xs text-slate-400 mt-0.5">Daftar transaksi pesanan paling baru masuk dari pelanggan.</p>
            </div>
            <a href="{{ route('admin.orders') }}" class="text-xs font-bold text-[#F97316] hover:text-orange-700 flex items-center gap-1">
                Lihat Semua <span class="material-symbols-outlined text-[16px]">arrow_forward</span>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 text-slate-400 text-xs font-bold uppercase tracking-wider">
                        <th class="py-3 px-4">ID Pesanan</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Total</th>
                        <th class="py-3 px-4 text-right">Status Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transaksiTerbaru as $pesanan)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-4 font-bold text-slate-800 text-sm">#ORD-{{ $pesanan->id_pesanan }}</td>
                            <td class="py-4 px-4 text-slate-600 text-sm font-semibold">
                                {{ $pesanan->pelanggan->nama_lengkap ?? $pesanan->pelanggan->nama_pelanggan ?? $pesanan->pelanggan->akun->nama ?? 'Tamu' }}
                            </td>
                            <td class="py-4 px-4 font-extrabold text-slate-800 text-sm">
                                Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-4 text-right">
                                @if($pesanan->status_pembayaran == 'Lunas')
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">
                                        Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-red-50 text-red-600 border border-red-200">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-slate-400 text-sm">
                                Belum ada transaksi pesanan terbaru.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- SCRIPT UNTUK TOGGLE STATUS WARUNG BUKA / TUTUP VIA SERVER -->
<script>
    function toggleStatusWarung() {
        const btn = document.getElementById('btnStatusWarung');
        const dot = document.getElementById('dotStatusWarung');
        const text = document.getElementById('textStatusWarung');

        fetch("{{ route('admin.toggle.warung') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.status === 'tutup') {
                    btn.className = 'inline-flex items-center gap-2 bg-red-50 text-red-700 border border-red-200 hover:bg-red-100 px-4 py-2 rounded-full text-xs font-bold transition-all shadow-sm';
                    dot.className = 'w-2.5 h-2.5 rounded-full bg-red-500';
                    text.innerText = 'Warung Tutup';
                } else {
                    btn.className = 'inline-flex items-center gap-2 bg-emerald-50 text-emerald-700 border border-emerald-200 hover:bg-emerald-100 px-4 py-2 rounded-full text-xs font-bold transition-all shadow-sm';
                    dot.className = 'w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse';
                    text.innerText = 'Warung Buka';
                }

                if (typeof Swal !== 'undefined') {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'success',
                        title: data.message
                    });
                }
            }
        })
        .catch(error => {
            console.error('Error toggling status:', error);
        });
    }
</script>
@endsection