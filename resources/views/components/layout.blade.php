<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $title ?? 'Dashboard' }} - SIMAT-RK RSUD Dr. H. Koesnandi</title>

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
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased overflow-x-hidden selection:bg-emerald-500 selection:text-slate-950">
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
        <div class="flex-1 flex flex-col min-w-0 min-h-screen overflow-x-hidden w-full">
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
