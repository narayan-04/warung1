<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard Admin - Warung Seafood')</title>
    <!-- Tailwind CSS CDN dengan Plugins -->
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Material Symbols Outlined Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet">
    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    "colors": {
                        "primary-container": "#ff7f50",
                        "surface": "#f8f9fa",
                        "surface-bright": "#f8f9fa",
                        "error-container": "#ffdad6",
                        "inverse-surface": "#2e3132",
                        "outline-variant": "#dec0b6",
                        "on-secondary-fixed": "#131b2e",
                        "on-secondary-fixed-variant": "#3f465c",
                        "inverse-primary": "#ffb59c",
                        "on-primary-fixed": "#380c00",
                        "on-error": "#ffffff",
                        "tertiary-fixed": "#89f5e7",
                        "surface-variant": "#e1e3e4",
                        "surface-container-lowest": "#ffffff",
                        "primary-fixed": "#ffdbcf",
                        "error": "#ba1a1a",
                        "surface-container": "#edeeef",
                        "outline": "#8b7169",
                        "surface-tint": "#a43c12",
                        "on-primary": "#ffffff",
                        "on-primary-fixed-variant": "#822800",
                        "inverse-on-surface": "#f0f1f2",
                        "secondary-container": "#dae2fd",
                        "on-tertiary-fixed-variant": "#005049",
                        "surface-container-high": "#e7e8e9",
                        "on-error-container": "#93000a",
                        "on-tertiary-fixed": "#00201d",
                        "secondary": "#565e74",
                        "primary": "#a43c12",
                        "on-tertiary": "#ffffff",
                        "secondary-fixed-dim": "#bec6e0",
                        "on-surface-variant": "#57423b",
                        "background": "#f8f9fa",
                        "on-surface": "#191c1d",
                        "on-primary-container": "#6c2000",
                        "on-background": "#191c1d",
                        "surface-dim": "#d9dadb",
                        "tertiary": "#006a61",
                        "surface-container-highest": "#e1e3e4",
                        "tertiary-container": "#44b5a8",
                        "primary-fixed-dim": "#ffb59c",
                        "tertiary-fixed-dim": "#6bd8cb",
                        "secondary-fixed": "#dae2fd",
                        "surface-container-low": "#f3f4f5",
                        "on-tertiary-container": "#00423c",
                        "on-secondary-container": "#5c647a",
                        "on-secondary": "#ffffff"
                    },
                    "borderRadius": {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                    "spacing": {
                        "gutter": "24px",
                        "stack-sm": "8px",
                        "stack-md": "16px",
                        "margin-mobile": "16px",
                        "container-max": "1280px",
                        "stack-lg": "32px",
                        "margin-desktop": "40px"
                    },
                    "fontFamily": {
                        "body-md": ["Plus Jakarta Sans"],
                        "headline-sm": ["Plus Jakarta Sans"],
                        "label-sm": ["Plus Jakarta Sans"],
                        "headline-md": ["Plus Jakarta Sans"],
                        "body-lg": ["Plus Jakarta Sans"],
                        "label-md": ["Plus Jakarta Sans"],
                        "display-lg-mobile": ["Plus Jakarta Sans"],
                        "display-lg": ["Plus Jakarta Sans"]
                    },
                    "fontSize": {
                        "body-md": ["16px", { "lineHeight": "24px", "fontWeight": "400" }],
                        "headline-sm": ["20px", { "lineHeight": "28px", "fontWeight": "600" }],
                        "label-sm": ["12px", { "lineHeight": "16px", "fontWeight": "500" }],
                        "headline-md": ["24px", { "lineHeight": "32px", "fontWeight": "600" }],
                        "body-lg": ["18px", { "lineHeight": "28px", "fontWeight": "400" }],
                        "label-md": ["14px", { "lineHeight": "20px", "letterSpacing": "0.01em", "fontWeight": "600" }],
                        "display-lg-mobile": ["32px", { "lineHeight": "40px", "letterSpacing": "-0.02em", "fontWeight": "700" }],
                        "display-lg": ["48px", { "lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700" }]
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 1; }
        
        /* Fix SweetAlert2 height:auto breaking admin sidebar layout */
        html.swal2-shown, body.swal2-shown {
            height: 100vh !important;
            overflow: hidden !important;
            margin: 0 !important;
            padding: 0 !important;
        }

        /* Premium Abstract Dot Pattern Background on Admin Dashboard */
        main {
            position: relative;
        }
        main > * {
            position: relative;
            z-index: 1;
        }
        main::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 0;
            pointer-events: none;
            opacity: 0.035; /* Opacity minimal 3.5% */
            background-image: radial-gradient(#0f172a 1.5px, transparent 1.5px);
            background-size: 24px 24px;
        }
    </style>
</head>
<body class="bg-surface flex h-screen overflow-hidden text-on-surface">

    <!-- SIDEBAR KIRI GOOGLE STITCH -->
    <aside class="w-[250px] bg-[#0F172A] flex-shrink-0 flex flex-col h-full hidden md:flex">
        <!-- Logo & Brand -->
        <div class="p-gutter flex items-center gap-3">
            <div class="bg-surface/10 p-2 rounded-lg text-on-secondary">
                <span class="material-symbols-outlined text-2xl">badge</span>
            </div>
            <div class="text-on-secondary font-headline-sm text-headline-sm leading-tight">
                Warung<br>Seafood
            </div>
        </div>

        <!-- Profile Section -->
        <div class="px-gutter pb-6 border-b border-on-secondary/10 flex items-center gap-3">
            <div class="w-12 h-12 rounded-full bg-orange-500/20 text-orange-400 font-bold flex items-center justify-center text-lg">
                {{ substr(Auth::user()->username ?? 'A', 0, 1) }}
            </div>
            <div class="overflow-hidden">
                <div class="font-label-md text-label-md text-on-secondary truncate">{{ Auth::user()->username ?? 'Narayan Seafood' }}</div>
                <div class="mt-1 bg-primary-container/20 text-primary-container text-[10px] uppercase font-bold px-2 py-0.5 rounded-md inline-block">
                    Penjual / Admin
                </div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 py-stack-md px-stack-sm flex flex-col gap-2 overflow-y-auto">
            <div class="px-4 mb-2 text-[12px] font-semibold text-slate-400 uppercase tracking-wider">MANAJEMEN WARUNG</div>
            
            <!-- Dashboard Link -->
            <a class="flex items-center gap-stack-md {{ request()->routeIs('admin.dashboard') ? 'bg-[#F97316] text-white font-bold shadow-lg shadow-[#F97316]/30' : 'text-slate-400 hover:text-white font-normal' }} rounded-xl px-4 py-3 mx-2 transition-all" href="{{ route('admin.dashboard') }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="font-label-md text-label-md">Dashboard</span>
            </a>
            
            <!-- Kelola Pesanan Link -->
            <a class="flex items-center gap-stack-md {{ request()->routeIs('admin.orders*') ? 'bg-[#F97316] text-white font-bold shadow-lg shadow-[#F97316]/30' : 'text-slate-400 hover:text-white font-normal' }} rounded-lg px-4 py-3 mx-2 transition-all" href="{{ route('admin.orders') }}">
                <span class="material-symbols-outlined">receipt_long</span>
                <span class="font-label-md text-label-md">Kelola Pesanan</span>
            </a>
            
            <!-- Kelola Menu Link -->
            <a class="flex items-center gap-stack-md {{ request()->routeIs('admin.menus*') ? 'bg-[#F97316] text-white font-bold shadow-lg shadow-[#F97316]/30' : 'text-slate-400 hover:text-white font-normal' }} rounded-lg px-4 py-3 mx-2 transition-all" href="{{ route('admin.menus') }}">
                <span class="material-symbols-outlined">restaurant</span>
                <span class="font-label-md text-label-md">Kelola Menu</span>
            </a>
            
            <!-- Kelola Pelanggan Link -->
            <a class="flex items-center gap-stack-md {{ request()->routeIs('admin.customers*') ? 'bg-[#F97316] text-white font-bold shadow-lg shadow-[#F97316]/30' : 'text-slate-400 hover:text-white font-normal' }} rounded-lg px-4 py-3 mx-2 transition-all" href="{{ route('admin.customers') }}">
                <span class="material-symbols-outlined">group</span>
                <span class="font-label-md text-label-md">Kelola Pelanggan</span>
            </a>
        </nav>

        <!-- Footer / Tombol Logout -->
        <div class="p-gutter border-t border-on-secondary/10">
            <form id="logout-form-admin" action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="button" onclick="confirmLogoutAdmin()" class="flex items-center gap-stack-md text-slate-400 hover:text-white w-full px-2 py-2 transition-colors">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="font-label-md text-label-md">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- AREA KONTEN UTAMA -->
    <main class="flex-1 h-full overflow-y-auto bg-surface-container-low p-gutter lg:p-margin-desktop">
        @yield('content')
    </main>

    <!-- SweetAlert2 CDN & Automatic Toast Handler -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: @json(session('success'))
                });
            @endif

            @if(session('error'))
                Toast.fire({
                    icon: 'error',
                    title: @json(session('error'))
                });
            @endif
        });

        function confirmLogoutAdmin() {
            Swal.fire({
                title: 'Keluar Aplikasi?',
                text: 'Apakah Anda yakin ingin keluar dari panel admin?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#a43c12',
                cancelButtonColor: '#565e74',
                confirmButtonText: 'Ya, Keluar',
                cancelButtonText: 'Batal',
                customClass: {
                    popup: 'rounded-3xl border border-slate-100 shadow-2xl font-[\'Plus_Jakarta_Sans\']',
                    confirmButton: 'rounded-xl px-5 py-2.5 text-sm font-bold',
                    cancelButton: 'rounded-xl px-5 py-2.5 text-sm font-bold bg-slate-200 text-slate-700 hover:bg-slate-300'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form-admin').submit();
                }
            });
        }
    </script>
</body>
</html>