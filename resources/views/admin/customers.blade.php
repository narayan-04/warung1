@extends('layouts.admin')

@section('title', 'Daftar Pelanggan - Warung Seafood')

@section('content')
<div class="max-w-container-max mx-auto font-['Plus_Jakarta_Sans']">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-[#0B132B] tracking-tight mb-1">Daftar Pelanggan</h1>
            <p class="text-slate-500 text-sm">Pantau daftar pelanggan terdaftar dan riwayat jumlah pesanan mereka.</p>
        </div>
        <button onclick="window.print()" class="flex items-center gap-2 bg-[#0F172A] hover:bg-slate-800 text-white px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-md shadow-slate-900/10">
            <span class="material-symbols-outlined text-[18px]">print</span>
            Cetak Data
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl mb-6 flex items-center shadow-sm font-semibold text-sm">
            <span class="material-symbols-outlined mr-2 text-emerald-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Statistik Singkat Pelanggan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-8">
        <div class="bg-blue-50/50 border border-blue-100 p-5 rounded-2xl flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-blue-500 text-white flex items-center justify-center shadow-md shadow-blue-500/15">
                <span class="material-symbols-outlined">group</span>
            </div>
            <div>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Total Terdaftar</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $pelanggan->count() }} Orang</h3>
            </div>
        </div>
        
        <div class="bg-emerald-50/50 border border-emerald-100 p-5 rounded-2xl flex items-center gap-4 shadow-sm">
            <div class="w-12 h-12 rounded-xl bg-emerald-500 text-white flex items-center justify-center shadow-md shadow-emerald-500/15">
                <span class="material-symbols-outlined">loyalty</span>
            </div>
            <div>
                <p class="text-xs font-black text-slate-400 uppercase tracking-widest">Pelanggan Aktif Memesan</p>
                <h3 class="text-2xl font-black text-slate-800 mt-1">{{ $pelanggan->where('pesanan_count', '>', 0)->count() }} Orang</h3>
            </div>
        </div>
    </div>

    <!-- Tabel Pelanggan Card -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4 w-20 text-center">No</th>
                        <th class="px-6 py-4">Informasi Pelanggan</th>
                        <th class="px-6 py-4">Username Akun</th>
                        <th class="px-6 py-4 text-center">Total Pesanan</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($pelanggan as $index => $p)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Nomor -->
                            <td class="px-6 py-4 text-center font-bold text-sm text-slate-400">
                                {{ $index + 1 }}
                            </td>
                            <!-- Informasi -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-orange-100 text-orange-700 flex items-center justify-center font-black text-base shadow-inner shrink-0">
                                        {{ strtoupper(substr($p->nama_pelanggan ?? ($p->akun->username ?? 'T'), 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-extrabold text-sm text-[#0B132B]">{{ $p->nama_pelanggan ?? 'Tamu Tanpa Profil' }}</div>
                                        <div class="text-[10px] text-slate-400 font-bold uppercase mt-1 tracking-wider">ID: PLG-{{ str_pad($p->id_pelanggan, 4, '0', STR_PAD_LEFT) }}</div>
                                    </div>
                                </div>
                            </td>
                            <!-- Username -->
                            <td class="px-6 py-4">
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                                    @ {{ $p->akun->username ?? 'Tidak ada data akun' }}
                                </span>
                            </td>
                            <!-- Total Pesanan -->
                            <td class="px-6 py-4 text-center font-black text-sm text-slate-700">
                                {{ $p->pesanan_count }}
                            </td>
                            <!-- Status Badge -->
                            <td class="px-6 py-4 text-center">
                                @if($p->pesanan_count >= 5)
                                    <span class="inline-flex items-center gap-1 bg-amber-50 text-amber-700 border border-amber-200/50 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm">
                                        <span class="material-symbols-outlined text-[14px]">star</span> VIP
                                    </span>
                                @elseif($p->pesanan_count > 0)
                                    <span class="inline-flex items-center gap-1 bg-emerald-50 text-emerald-700 border border-emerald-200/50 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 bg-slate-50 text-slate-500 border border-slate-200 px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-wider">
                                        Baru
                                    </span>
                                @endif
                            </td>
                            <!-- Aksi (Hapus Pelanggan) -->
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.customers.destroy', $p->id_pelanggan) }}" method="POST" id="delete-form-{{ $p->id_pelanggan }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDeleteCustomer('{{ $p->id_pelanggan }}', '{{ addslashes($p->nama_pelanggan ?? $p->akun->username) }}')" class="p-2.5 text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 rounded-xl transition-all" title="Hapus Akun Pelanggan">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-5xl mb-3 opacity-50 block">group_off</span>
                                Belum ada pelanggan yang terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert2 Script -->
<script>
    function confirmDeleteCustomer(id, name) {
        Swal.fire({
            title: 'Hapus Pelanggan?',
            text: `Apakah Anda yakin ingin menghapus akun dan profil "${name}"? Tindakan ini juga akan menghapus kredensial login mereka secara permanen.`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-2xl font-[\'Plus_Jakarta_Sans\']',
                confirmButton: 'rounded-xl px-5 py-2.5 text-sm font-bold',
                cancelButton: 'rounded-xl px-5 py-2.5 text-sm font-bold bg-slate-200 text-slate-700 hover:bg-slate-300'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        });
    }
</script>
@endsection