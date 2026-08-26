<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Dashboard' }} - SIMAT-RK RSUD Dr. H. Koesnandi</title>

    <!-- Favicon Logo RSUD -->
    <link rel="icon" type="image/png" href="{{ asset('img/Logo-rsud/logo-rsud.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/Logo-rsud/logo-rsud.png') }}">

    <!-- Tailwind CSS (Vite) & Alpine.js -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; }

        /* Global Elegant Dark Scrollbar */
        * {
            scrollbar-width: thin;
            scrollbar-color: #334155 #0b1120;
        }
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #0b1120;
            border-radius: 8px;
        }
        ::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 8px;
            border: 1px solid #1e293b;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #0d9488;
        }


        @media print {
            .no-print, aside, header, footer, #topbar, nav, button, .no-print * {
                display: none !important;
            }
            body, main, html, div[x-data] {
                background: white !important;
                color: black !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                overflow: visible !important;
            }
            .fixed.inset-0 {
                position: static !important;
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .bg-slate-900, .bg-slate-950 {
                background: white !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            #print-area-bast, #print-area-distribusi, #print-area-triwulan, #print-area-kir {
                display: block !important;
                width: 100% !important;
                max-height: none !important;
                overflow: visible !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            @page {
                size: A4 portrait;
                margin: 1.2cm 1.2cm 1.2cm 1.2cm;
            }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased selection:bg-emerald-500 selection:text-slate-950">
    <div x-data="{ 
            sidebarOpen: window.innerWidth >= 1024,
            isMobile: window.innerWidth < 1024
         }" 
         @resize.window="
            isMobile = window.innerWidth < 1024;
            if (!isMobile) { sidebarOpen = true; }
         " 
         class="min-h-screen flex bg-slate-950 relative">
        
        <!-- Mobile Dark Backdrop Overlay -->
        <div x-show="sidebarOpen && isMobile" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-950/80 backdrop-blur-sm lg:hidden" x-cloak>
        </div>

        <!-- Panggil Sidebar -->
        @include('partials.sidebar')

        <!-- Area Konten Utama -->
        <div class="flex-1 flex flex-col min-w-0 min-h-screen w-full">
            <!-- Panggil Topbar -->
            @include('partials.topbar')

            <!-- Slot Konten Utama -->
            <main class="flex-1 p-3 sm:p-6 lg:p-8 max-w-7xl mx-auto w-full">
                {{ $slot }}
            </main>

            <!-- Panggil Footer -->
            @include('partials.footer')
        </div>

    </div>
</body>
</html>
