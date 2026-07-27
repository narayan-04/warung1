<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Warung Seafood')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; position: relative; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 1; }
        
        /* Premium Abstract Dot Pattern Background */
        body::before {
            content: "";
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
            opacity: 0.035; /* Opacity minimal 3.5% */
            background-image: radial-gradient(#0f172a 1.5px, transparent 1.5px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="text-slate-800 flex flex-col min-h-screen">

    <!-- NAVBAR ATAS -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 shadow-sm">
        <!-- Menggunakan w-full agar logo mentok kiri dan user mentok kanan -->
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                
                <!-- KIRI: Logo & Brand -->
                <div class="flex items-center gap-3">
                    <div class="bg-[#0F172A] p-2.5 rounded-xl text-white flex items-center justify-center">
                        <span class="material-symbols-outlined">anchor</span>
                    </div>
                    <div>
                        <h1 class="font-extrabold text-xl text-[#0F172A] leading-tight">Warung Seafood</h1>
                        <p class="text-[10px] text-orange-500 font-bold uppercase tracking-widest">Spesial Tangkapan Harian</p>
                    </div>
                </div>

                <!-- TENGAH: Menu Navigasi (Sembunyi di Mobile) -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-900 font-semibold' }} flex items-center gap-2 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">menu_book</span> Katalog Menu
                    </a>
                    <a href="{{ route('customer.status') }}" class="{{ request()->routeIs('customer.status') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-900 font-semibold' }} flex items-center gap-2 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">dining</span> Status Pesanan
                    </a>
                    <a href="{{ route('customer.history') }}" class="{{ request()->routeIs('customer.history') ? 'text-orange-500 font-bold' : 'text-slate-500 hover:text-slate-900 font-semibold' }} flex items-center gap-2 transition-colors">
                        <span class="material-symbols-outlined text-[20px]">history</span> Riwayat
                    </a>
                </div>

                <!-- KANAN: User Info & Logout -->
                <div class="flex items-center gap-5">
                    @auth
                        <div class="flex items-center gap-3 border-r border-slate-200 pr-5">
                            <div class="w-10 h-10 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center font-bold text-lg">
                                {{ substr(Auth::user()->username ?? 'U', 0, 1) }}
                            </div>
                            <div class="hidden sm:block text-right">
                                <p class="text-[11px] text-slate-400 font-semibold uppercase tracking-wider">Halo,</p>
                                <p class="text-sm font-bold text-slate-800">{{ Auth::user()->username }}</p>
                            </div>
                        </div>
                        <form id="logout-form-customer" action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button type="button" onclick="confirmLogoutCustomer()" class="flex items-center gap-2 text-red-500 hover:text-red-700 hover:bg-red-50 px-3 py-2 rounded-lg font-bold transition-colors">
                                <span class="material-symbols-outlined">logout</span>
                                <span class="hidden sm:inline">Keluar</span>
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="bg-orange-500 text-white px-6 py-2.5 rounded-full font-bold hover:bg-orange-600 transition-colors shadow-md shadow-orange-500/20">
                            Masuk / Login
                        </a>
                    @endauth
                </div>

            </div>
        </div>
    </nav>

    <!-- KONTEN UTAMA -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- SweetAlert2 CDN & Logout Script -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogoutCustomer() {
            Swal.fire({
                title: 'Keluar Aplikasi?',
                text: 'Apakah Anda yakin ingin keluar dari akun Anda?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#f97316',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl border border-slate-100 shadow-2xl font-[\'Plus_Jakarta_Sans\']',
                    confirmButton: 'rounded-xl px-5 py-2.5 text-sm font-bold',
                    cancelButton: 'rounded-xl px-5 py-2.5 text-sm font-bold bg-slate-200 text-slate-700 hover:bg-slate-300'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form-customer').submit();
                }
            });
        }
    </script>

    @stack('scripts')
</body>
</html>