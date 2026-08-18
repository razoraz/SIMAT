<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIMAT-RK RSUD Dr. H. Koesnandi Bondowoso</title>

    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts - Nunito -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Chart.js untuk grafik dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <!-- JavaScript Kustom Aplikasi -->
    <script src="{{ asset('js/app.js') }}"></script>

</head>

<body>
    <!-- Overlay gelap untuk sidebar mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- ===================================================
     WRAPPER UTAMA - Sidebar + Konten
     =================================================== -->
    <div class="app-wrapper">

        <!-- 1. Panggil Topbar -->
        @include('partials.topbar')

        <div class="main-content" id="mainContent">
            <!-- 2. Panggil Sidebar -->
            @include('partials.sidebar')

            <!-- 3. Area Konten Dinamis -->
            <main>
                @yield('content')
            </main>


            <!-- 4. Panggil Footer -->
            @include('partials.footer')
        </div>
    </div>

</body>

</html>
