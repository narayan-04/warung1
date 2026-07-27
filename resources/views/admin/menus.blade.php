@extends('layouts.admin')

@section('title', 'Kelola Menu - Warung Seafood')

@section('content')
<div class="max-w-container-max mx-auto font-['Plus_Jakarta_Sans']">
    <!-- Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-[#0B132B] tracking-tight mb-1">Katalog Menu</h1>
            <p class="text-slate-500 text-sm">Kelola daftar hidangan laut, harga, dan ketersediaan stok hari ini.</p>
        </div>
        <button onclick="openModal('modalTambahMenu')" class="flex items-center gap-2 bg-[#0F172A] hover:bg-orange-600 text-white px-5 py-3 rounded-xl font-bold text-sm transition-all shadow-md shadow-slate-900/10 hover:shadow-orange-500/20">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Menu Baru
        </button>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-5 py-4 rounded-xl mb-6 flex items-center shadow-sm font-semibold text-sm">
            <span class="material-symbols-outlined mr-2 text-emerald-600">check_circle</span>
            {{ session('success') }}
        </div>
    @endif

    <!-- Daftar Menu Card Table -->
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-bold text-xs uppercase tracking-wider border-b border-slate-200">
                        <th class="px-6 py-4">Info Menu</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Harga</th>
                        <th class="px-6 py-4 text-center">Status Stok</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($menus as $menu)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <!-- Info Menu -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if($menu->foto_url)
                                        <img src="{{ $menu->foto_url }}" alt="Foto" class="w-12 h-12 rounded-xl object-cover border border-slate-200 shadow-sm shrink-0">
                                    @else
                                        <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0 shadow-inner">
                                            <span class="material-symbols-outlined text-[20px]">image_not_supported</span>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="font-extrabold text-sm text-[#0B132B]">{{ $menu->nama_menu }}</div>
                                        <div class="text-xs text-slate-400 font-medium mt-0.5 truncate max-w-[240px]" title="{{ $menu->deskripsi }}">
                                            {{ $menu->deskripsi ?? 'Tidak ada deskripsi' }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <!-- Kategori -->
                            <td class="px-6 py-4">
                                <span class="bg-slate-100 text-slate-600 text-[10px] font-black px-2.5 py-1 rounded-md uppercase tracking-wider">
                                    {{ $menu->kategoriData->nama_kategori ?? 'Tanpa Kategori' }}
                                </span>
                            </td>
                            <!-- Harga -->
                            <td class="px-6 py-4 font-black text-sm text-[#0B132B]">
                                Rp {{ number_format($menu->harga, 0, ',', '.') }}
                            </td>
                            <!-- Status Stok (Toggle) -->
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.menus.update', $menu->id_menu) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="is_available" value="{{ $menu->is_available ? 0 : 1 }}">
                                    <button type="submit" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-black border transition-all duration-300 {{ $menu->is_available ? 'bg-emerald-50 text-emerald-700 border-emerald-200 hover:bg-red-50 hover:text-red-700 hover:border-red-200' : 'bg-red-50 text-red-700 border-red-200 hover:bg-emerald-50 hover:text-emerald-700 hover:border-emerald-200' }}" title="Klik untuk mengubah ketersediaan">
                                        @if($menu->is_available)
                                            <span class="w-1.5 h-1.5 bg-emerald-600 rounded-full animate-ping"></span> Tersedia
                                        @else
                                            <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span> Habis
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <!-- Aksi (Edit & Hapus) -->
                            <td class="px-6 py-4 text-right flex items-center justify-end gap-2">
                                <!-- Tombol Edit -->
                                <button onclick="openModal('modalEditMenu-{{ $menu->id_menu }}')" class="p-2.5 text-blue-600 hover:bg-blue-50 border border-transparent hover:border-blue-100 rounded-xl transition-all" title="Edit Menu">
                                    <span class="material-symbols-outlined text-[18px]">edit</span>
                                </button>

                                <!-- Tombol Hapus -->
                                <form action="{{ route('admin.menus.destroy', $menu->id_menu) }}" method="POST" id="delete-form-{{ $menu->id_menu }}" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDelete('{{ $menu->id_menu }}', '{{ addslashes($menu->nama_menu) }}')" class="p-2.5 text-red-600 hover:bg-red-50 border border-transparent hover:border-red-100 rounded-xl transition-all" title="Hapus Menu">
                                        <span class="material-symbols-outlined text-[18px]">delete</span>
                                    </button>
                                </form>
                            </td>
                        </tr>

                        <!-- Modal Edit Menu -->
                        <div id="modalEditMenu-{{ $menu->id_menu }}" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4 text-left">
                            <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl border border-slate-200 overflow-hidden transform scale-95 opacity-0 transition-all duration-200 modal-content">
                                <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                                    <h2 class="text-lg font-black text-slate-800">Edit Menu</h2>
                                    <button onclick="closeModal('modalEditMenu-{{ $menu->id_menu }}')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                                        <span class="material-symbols-outlined text-[18px]">close</span>
                                    </button>
                                </div>
                                
                                <form action="{{ route('admin.menus.update', $menu->id_menu) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="p-6 space-y-4">
                                        <div>
                                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Nama Menu</label>
                                            <input type="text" name="nama_menu" value="{{ $menu->nama_menu }}" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all">
                                        </div>
                                        
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Kategori</label>
                                                <select name="id_kategori" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all">
                                                    @foreach($kategoris as $kategori)
                                                        <option value="{{ $kategori->id_kategori }}" {{ $menu->id_kategori == $kategori->id_kategori ? 'selected' : '' }}>
                                                            {{ $kategori->nama_kategori }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Harga (Rp)</label>
                                                <input type="number" name="harga" value="{{ $menu->harga }}" required min="0" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">URL Gambar (Opsional)</label>
                                            <input type="text" name="foto_url" value="{{ $menu->foto_url }}" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all" placeholder="https://contoh.com/gambar.jpg">
                                        </div>

                                        <div>
                                            <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Deskripsi Singkat</label>
                                            <textarea name="deskripsi" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all resize-none">{{ $menu->deskripsi }}</textarea>
                                        </div>
                                    </div>
                                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                                        <button type="button" onclick="closeModal('modalEditMenu-{{ $menu->id_menu }}')" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-600 font-bold rounded-xl text-sm transition-colors">Batal</button>
                                        <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl text-sm transition-colors shadow-sm shadow-blue-500/20">Update Menu</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center text-slate-400">
                                <span class="material-symbols-outlined text-5xl mb-3 opacity-50 block">restaurant</span>
                                Belum ada menu yang didaftarkan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Tambah Menu -->
<div id="modalTambahMenu" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
    <div class="bg-white w-full max-w-md rounded-[2rem] shadow-2xl border border-slate-200 overflow-hidden transform scale-95 opacity-0 transition-all duration-200 modal-content">
        <div class="px-6 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h2 class="text-lg font-black text-slate-800">Tambah Menu Baru</h2>
            <button onclick="closeModal('modalTambahMenu')" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 flex items-center justify-center text-slate-400 hover:text-red-500 transition-colors">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        
        <form action="{{ route('admin.menus.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Nama Menu</label>
                    <input type="text" name="nama_menu" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all" placeholder="Cth: Ikan Bakar Gurame">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Kategori</label>
                        <select name="id_kategori" required class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all">
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id_kategori }}">{{ $kategori->nama_kategori }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Harga (Rp)</label>
                        <input type="number" name="harga" required min="0" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all" placeholder="50000">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">URL Gambar (Opsional)</label>
                    <input type="text" name="foto_url" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all" placeholder="Cth: https://contoh.com/gambar.jpg">
                </div>

                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Deskripsi Singkat</label>
                    <textarea name="deskripsi" rows="3" class="w-full px-4 py-3 border border-slate-200 rounded-2xl text-sm font-semibold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 bg-slate-50 focus:bg-white transition-all resize-none" placeholder="Cth: Dibakar dengan kecap manis dan rempah..."></textarea>
                </div>
            </div>
            
            <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3">
                <button type="button" onclick="closeModal('modalTambahMenu')" class="px-4 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-600 font-bold rounded-xl text-sm transition-colors">Batal</button>
                <button type="submit" class="px-5 py-2.5 bg-[#0F172A] hover:bg-orange-600 text-white font-bold rounded-xl text-sm transition-colors shadow-sm shadow-slate-900/20">Simpan Menu</button>
            </div>
        </form>
    </div>
</div>

<!-- Script untuk Modal & SweetAlert2 -->
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

    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Hapus Menu?',
            text: `Apakah Anda yakin ingin menghapus "${name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                popup: 'rounded-3xl border border-slate-100 shadow-2xl',
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