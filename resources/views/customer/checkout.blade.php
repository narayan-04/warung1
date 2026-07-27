@extends('layouts.customer')

@section('title', 'Konfirmasi Pesanan - Warung Seafood')

@section('content')
<!-- PREMIUM HERO BANNER -->
<div class="relative bg-[#0B132B] py-10 px-4 overflow-hidden border-b border-slate-800">
    <div class="absolute inset-0 bg-gradient-to-r from-[#0B132B]/95 via-[#0B132B]/90 to-[#0B132B]/95 backdrop-blur-[1px] z-0"></div>
    <div class="max-w-6xl mx-auto text-center relative z-10">
        <span class="inline-flex items-center gap-1.5 bg-orange-500/20 text-[#F97316] border border-orange-500/30 text-xs px-4 py-1.5 rounded-full font-bold uppercase tracking-widest mb-3">
            <span class="material-symbols-outlined text-[14px]">checklist_rtl</span> Langkah Terakhir
        </span>
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-white">Konfirmasi Pesanan</h1>
        <p class="text-slate-400 text-sm max-w-xl mx-auto mt-2">Periksa kembali daftar hidanganmu dan lengkapi informasi meja Anda.</p>
    </div>
</div>

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10 font-['Plus_Jakarta_Sans']">
    
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-2xl mb-8 flex items-center gap-3 shadow-sm max-w-4xl mx-auto">
            <span class="material-symbols-outlined text-red-500">error</span>
            <span class="font-bold text-sm">{{ session('error') }}</span>
        </div>
    @endif

    <form id="checkoutForm" action="{{ route('checkout.store') }}" method="POST">
        @csrf

        <!-- SIDE-BY-SIDE GRID LAYOUT (Untuk Menghindari White Space) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- KOLOM KIRI: Rincian Menu Pilihan (col-span-7) -->
            <div class="lg:col-span-7 bg-white rounded-3xl border border-slate-200 shadow-sm overflow-hidden p-6 sm:p-8">
                <h3 class="text-lg font-extrabold text-[#0B132B] mb-6 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <span class="material-symbols-outlined text-orange-500 text-2xl">receipt_long</span> Rincian Menu Pilihan
                </h3>

                <div class="divide-y divide-slate-100">
                    @foreach($cartItems as $item)
                        <div class="py-4 flex items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                @if(is_array($item['menu']) ? ($item['menu']['foto_url'] ?? null) : $item['menu']->foto_url)
                                    <img src="{{ is_array($item['menu']) ? $item['menu']['foto_url'] : $item['menu']->foto_url }}" alt="" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 shadow-sm shrink-0">
                                @else
                                    <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 shrink-0 shadow-inner">
                                        <span class="material-symbols-outlined text-2xl">restaurant</span>
                                    </div>
                                @endif
                                <div>
                                    <h4 class="font-extrabold text-slate-800 text-base leading-snug">{{ is_array($item['menu']) ? $item['menu']['nama_menu'] : $item['menu']->nama_menu }}</h4>
                                    <p class="text-xs text-slate-500 mt-1 font-semibold">Rp {{ number_format(is_array($item['menu']) ? $item['menu']['harga'] : $item['menu']->harga, 0, ',', '.') }} × {{ $item['qty'] }} Porsi</p>
                                </div>
                            </div>
                            <div class="text-right font-black text-slate-800 text-base shrink-0">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 pt-6 border-t-2 border-dashed border-slate-200 flex justify-between items-center">
                    <span class="font-extrabold text-slate-600 text-base">Total Pembayaran</span>
                    <span class="font-black text-2xl text-orange-600">Rp {{ number_format($totalHarga, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- KOLOM KANAN: Informasi Meja & Pembayaran (col-span-5) -->
            <div class="lg:col-span-5 bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
                <h3 class="text-lg font-extrabold text-[#0B132B] mb-2 flex items-center gap-2 border-b border-slate-100 pb-4">
                    <span class="material-symbols-outlined text-orange-500 text-2xl">desk</span> Informasi Meja & Pembayaran
                </h3>

                <!-- Pilihan Meja -->
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Nomor Meja</label>
                    <div class="relative">
                        <select name="no_meja" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all appearance-none">
                            <option value="" disabled selected>-- Pilih Nomor Meja --</option>
                            <option value="Meja 1">Meja 1 (Kapasitas 4 Orang)</option>
                            <option value="Meja 2">Meja 2 (Kapasitas 4 Orang)</option>
                            <option value="Meja 3">Meja 3 (Kapasitas 4 Orang)</option>
                            <option value="Meja 4">Meja 4 (Kapasitas 4 Orang)</option>
                            <option value="Meja 5">Meja 5 (Kapasitas 6 Orang)</option>
                            <option value="Meja 6">Meja 6 (Kapasitas 6 Orang)</option>
                            <option value="Meja 7">Meja 7 (Kapasitas 6 Orang)</option>
                            <option value="Meja 8">Meja 8 (Kapasitas 6 Orang)</option>
                            <option value="Meja 9">Meja 9 (Kapasitas 8 Orang)</option>
                            <option value="Meja 10">Meja 10 (Kapasitas 8 Orang)</option>
                            <option value="Meja 11">Meja 11 (VIP Keluarga - 10 Orang)</option>
                            <option value="Meja 12">Meja 12 (VIP Keluarga - 10 Orang)</option>
                            <option value="Meja 13">Meja 13 (VIP Outdoor - 6 Orang)</option>
                            <option value="Meja 14">Meja 14 (VIP Outdoor - 6 Orang)</option>
                            <option value="Meja 15">Meja 15 (Saung Lesehan A - 6 Orang)</option>
                            <option value="Meja 16">Meja 16 (Saung Lesehan B - 6 Orang)</option>
                            <option value="Meja 17">Meja 17 (Saung Lesehan C - 6 Orang)</option>
                            <option value="Meja 18">Meja 18 (Saung Lesehan D - 8 Orang)</option>
                            <option value="Meja 19">Meja 19 (Saung Lesehan E - 8 Orang)</option>
                            <option value="Meja 20">Meja 20 (Meja Bar / Solo - 2 Orang)</option>
                            <option value="Takeaway">Bungkus / Takeaway</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>

                <!-- Metode Pembayaran -->
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Metode Pembayaran</label>
                    <div class="relative">
                        <select id="paymentMethodSelect" name="metode_pembayaran" required class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all appearance-none">
                            <option value="Cash">💵 Bayar Tunai (Cash di Kasir)</option>
                            <option value="QRIS">📱 QRIS (Simulasi Scan QR)</option>
                        </select>
                        <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">expand_more</span>
                    </div>
                </div>

                <!-- Catatan Dapur -->
                <div>
                    <label class="block text-xs font-black text-slate-500 uppercase tracking-widest mb-2">Catatan Koki (Opsional)</label>
                    <textarea name="catatan" rows="3" placeholder="Contoh: Sangat pedas, kuah dipisah, dll" class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-4 py-3.5 text-sm font-semibold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:bg-white transition-all resize-none"></textarea>
                </div>

                <!-- Tombol Navigasi -->
                <div class="pt-4 flex items-center gap-4">
                    <a href="{{ route('home') }}" class="w-1/3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-extrabold py-4 rounded-2xl text-center text-sm transition-colors shadow-sm">
                        Kembali
                    </a>
                    <button type="submit" class="w-2/3 bg-[#F97316] hover:bg-orange-600 text-white font-extrabold py-4 rounded-2xl shadow-lg shadow-orange-500/30 transition-all text-sm flex items-center justify-center gap-2">
                        Kirim Pesanan <span class="material-symbols-outlined text-[18px]">send</span>
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- ============================================== -->
<!-- 1. POP UP KONFIRMASI ULANG PESANAN (MODAL HTML) -->
<!-- ============================================== -->
<div id="confirmModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <!-- Backdrop Blur -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" onclick="closeConfirmModal()"></div>
    
    <!-- Modal Box -->
    <div class="bg-white rounded-[2rem] max-w-md w-full p-6 sm:p-8 relative z-10 shadow-2xl border border-slate-100 transform scale-95 opacity-0 transition-all duration-300 ease-out" id="confirmModalBox">
        <div class="text-center">
            <!-- Warning Icon -->
            <div class="w-16 h-16 bg-amber-50 text-amber-500 rounded-full flex items-center justify-center mx-auto mb-5 shadow-inner">
                <span class="material-symbols-outlined text-4xl">warning</span>
            </div>
            
            <h3 class="text-xl font-black text-[#0B132B] mb-2">Konfirmasi Kirim Pesanan</h3>
            <p class="text-sm text-slate-500 leading-relaxed mb-6 font-medium">Apakah Anda yakin seluruh hidangan dan nomor meja sudah sesuai?</p>
            
            <!-- Warning Box -->
            <div class="bg-red-50 border border-red-100 text-red-800 p-4 rounded-2xl mb-6 text-left flex items-start gap-3">
                <span class="material-symbols-outlined text-red-500 shrink-0 text-[20px]">info</span>
                <p class="text-[11px] sm:text-xs leading-relaxed font-bold">
                    PENTING: Pesanan yang sudah dikirim tidak dapat dibatalkan atau diubah dengan alasan apa pun karena langsung diproses di dapur.
                </p>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center gap-3">
                <button type="button" onclick="closeConfirmModal()" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-extrabold py-3.5 rounded-xl text-sm transition-colors">
                    Periksa Kembali
                </button>
                <button type="button" onclick="proceedOrderSubmission()" class="w-1/2 bg-orange-500 hover:bg-orange-600 text-white font-extrabold py-3.5 rounded-xl text-sm transition-colors shadow-lg shadow-orange-500/25">
                    Ya, Pesan Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ============================================== -->
<!-- 2. POP UP SCAN QRIS (MODAL HTML) -->
<!-- ============================================== -->
<div id="qrisModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <!-- Backdrop Blur -->
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
    
    <!-- Modal Box -->
    <div class="bg-white rounded-[2.5rem] max-w-sm w-full p-6 sm:p-8 relative z-10 shadow-2xl border border-slate-100 transform scale-95 opacity-0 transition-all duration-300 ease-out" id="qrisModalBox">
        <div class="text-center">
            <h3 class="text-xl font-black text-[#0B132B]">Pembayaran QRIS</h3>
            <p class="text-xs text-slate-400 font-semibold mt-1">Warung Seafood Merchant Resmi</p>
            
            <!-- QRIS QR Code Image Frame -->
            <div class="my-6 p-4 bg-slate-50 rounded-3xl border border-slate-200 max-w-[240px] mx-auto shadow-inner relative group cursor-pointer" onclick="submitFinalForm()">
                <img src="{{ asset('images/qris_mockup.png') }}" alt="QRIS Code" class="w-full h-auto rounded-2xl group-hover:scale-95 transition-transform duration-300">
                
                <!-- Hover Click Indicator -->
                <div class="absolute inset-4 bg-[#0F172A]/85 rounded-2xl flex flex-col items-center justify-center text-white opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                    <span class="material-symbols-outlined text-3xl mb-1 animate-bounce">touch_app</span>
                    <span class="text-[10px] font-bold uppercase tracking-widest px-2 text-center">Klik untuk Selesaikan Pembayaran</span>
                </div>
            </div>

            <!-- Amount Info Box -->
            <div class="bg-orange-50/50 border border-orange-100 p-3.5 rounded-2xl mb-5">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Nominal</span>
                <h4 class="text-xl font-black text-orange-600 mt-0.5">Rp {{ number_format($totalHarga, 0, ',', '.') }}</h4>
            </div>
            
            <p class="text-xs text-slate-500 leading-relaxed mb-6 font-medium">
                Pindai QRIS di atas dengan e-wallet/M-Banking Anda.<br>
                <span class="font-bold text-orange-500">Ketuk pada gambar QR di atas</span> untuk mensimulasikan pembayaran berhasil & kirim pesanan.
            </p>
            
            <button type="button" onclick="closeQrisModal()" class="w-full bg-slate-100 hover:bg-slate-200 text-slate-600 font-extrabold py-3.5 rounded-xl text-sm transition-colors">
                Batal
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const confirmModal = document.getElementById('confirmModal');
    const confirmModalBox = document.getElementById('confirmModalBox');
    
    const qrisModal = document.getElementById('qrisModal');
    const qrisModalBox = document.getElementById('qrisModalBox');
    
    const checkoutForm = document.getElementById('checkoutForm');
    const paymentMethodSelect = document.getElementById('paymentMethodSelect');

    // Intersepsi submit form untuk menampilkan konfirmasi
    checkoutForm.addEventListener('submit', function(e) {
        e.preventDefault();
        openConfirmModal();
    });

    // FUNGSI MODAL KONFIRMASI
    function openConfirmModal() {
        confirmModal.classList.remove('hidden');
        confirmModal.classList.add('flex');
        setTimeout(() => {
            confirmModalBox.classList.remove('scale-95', 'opacity-0');
            confirmModalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeConfirmModal() {
        confirmModalBox.classList.remove('scale-100', 'opacity-100');
        confirmModalBox.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            confirmModal.classList.add('hidden');
            confirmModal.classList.remove('flex');
        }, 300);
    }

    // Saat pengguna menyetujui konfirmasi
    function proceedOrderSubmission() {
        closeConfirmModal();
        
        // Cek Metode Pembayaran
        if (paymentMethodSelect.value === 'QRIS') {
            // Tampilkan Modal QRIS
            setTimeout(() => {
                openQrisModal();
            }, 350);
        } else {
            // Langsung submit jika Tunai / Cash
            submitFinalForm();
        }
    }

    // FUNGSI MODAL QRIS
    function openQrisModal() {
        qrisModal.classList.remove('hidden');
        qrisModal.classList.add('flex');
        setTimeout(() => {
            qrisModalBox.classList.remove('scale-95', 'opacity-0');
            qrisModalBox.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeQrisModal() {
        qrisModalBox.classList.remove('scale-100', 'opacity-100');
        qrisModalBox.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            qrisModal.classList.add('hidden');
            qrisModal.classList.remove('flex');
        }, 300);
    }

    // SUBMIT FORM AKHIR
    function submitFinalForm() {
        checkoutForm.submit();
    }
</script>
@endpush