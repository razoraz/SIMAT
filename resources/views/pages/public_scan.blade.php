<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identitas Resmi Aset - SIMAT-RK RSUD Dr. H. Koesnandi</title>
    <!-- Favicon Logo RSUD -->
    <link rel="icon" type="image/png" href="{{ asset('img/Logo-rsud/logo-rsud.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/Logo-rsud/logo-rsud.png') }}">

    <!-- Tailwind CSS & JS (Vite Bundle Lokal) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Google Fonts (Fallback Online) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-slate-950 antialiased">

    @include('pages.public_scan_partials.header_nav')

    <!-- MAIN CONTENT CONTAINER -->
    <main class="max-w-3xl mx-auto px-4 py-6 sm:py-8 w-full flex-1 space-y-5">
        @if($found)
            @php
                // Parsing spesifikasi JSON jika berupa string
                $spec = $astap->spesifikasi_json ?? [];
                if (is_string($spec)) {
                    try {
                        $spec = json_decode($spec, true) ?? [];
                    } catch (\Throwable $e) {
                        $spec = [];
                    }
                }

                // Kategori / KIB
                $category = $astap->category ?? '';
                $kodeBarang = $astap->kode_barang ?? ($register->nibar ?? '');
                if (empty($category) && !empty($kodeBarang)) {
                    if (str_starts_with($kodeBarang, '1.3.1')) $category = 'KIB A • Tanah';
                    elseif (str_starts_with($kodeBarang, '1.3.2')) $category = 'KIB B • Peralatan dan Mesin';
                    elseif (str_starts_with($kodeBarang, '1.3.3')) $category = 'KIB C • Gedung dan Bangunan';
                    elseif (str_starts_with($kodeBarang, '1.3.4')) $category = 'KIB D • Jalan, Jaringan & Irigasi';
                    elseif (str_starts_with($kodeBarang, '1.3.5')) $category = 'KIB E • Aset Tetap Lainnya';
                    elseif (str_starts_with($kodeBarang, '1.3.6')) $category = 'KIB F • Konstruksi Dalam Pengerjaan';
                    elseif (str_starts_with($kodeBarang, '1.5.3')) $category = 'Aset Tidak Berwujud (ATB)';
                    else $category = 'Aset Tetap RSUD';
                }

                // Kondisi Fisik
                $rawKondisi = strtoupper(trim((string)($register->kondisi ?? ($astap->kondisi ?? 'Baik'))));
                $kondisi = ($rawKondisi === 'KB' || $rawKondisi === 'KURANG BAIK') ? 'Kurang Baik' : (($rawKondisi === 'RB' || $rawKondisi === 'RUSAK BERAT' || $rawKondisi === 'RUSAK') ? 'Rusak Berat' : (($rawKondisi === 'RR' || $rawKondisi === 'RUSAK RINGAN') ? 'Rusak Ringan' : 'Baik'));

                // Ruang / Lokasi Penempatan
                $ruang = $register->ruang_pemegang ?? ($astap->ruang_pemegang ?? ($spec['ruang_pemegang'] ?? ($astap->letak_lokasi ?? '')));

                // Ciri Khusus / Spesifikasi Fisik
                $merk = $spec['mesin_merk'] ?? ($spec['merk'] ?? ($astap->merk ?? ''));
                $tipe = $spec['mesin_tipe'] ?? ($spec['tipe'] ?? ($astap->type ?? ''));
                $ukuran = $spec['mesin_ukuran_cc'] ?? ($spec['ukuran_cc'] ?? ($spec['ukuran'] ?? ($spec['luas_m2'] ?? '')));
                $bahan = $spec['mesin_bahan'] ?? ($spec['bahan'] ?? ($spec['beton'] ?? ''));
                $noPabrik = $spec['mesin_no_pabrik'] ?? ($spec['no_pabrik'] ?? ($astap->no_pabrik ?? ''));
                $noRangka = $spec['mesin_no_rangka'] ?? ($spec['no_rangka'] ?? '');
                $noMesin = $spec['mesin_no_mesin'] ?? ($spec['no_mesin'] ?? '');
                $noPolisi = $spec['mesin_no_polisi'] ?? ($spec['no_polisi'] ?? '');

                // Atribut Khusus ATB / Buku / Lahan
                $judul = $spec['atb_judul'] ?? ($spec['lainnya_buku_judul'] ?? ($spec['lainnya_hewan_judul'] ?? ($spec['judul'] ?? '')));
                $pencipta = $spec['atb_pencipta'] ?? ($spec['lainnya_buku_pencipta'] ?? ($spec['lainnya_kesenian_asal'] ?? ($spec['pencipta'] ?? '')));
                $spesifikasiKhusus = $spec['atb_spesifikasi'] ?? ($spec['lainnya_buku_spesifikasi'] ?? ($spec['spesifikasi'] ?? ''));

                // Icon Aset
                $iconAset = '📦';
                if (str_contains(strtolower($category), 'tanah')) $iconAset = '🌱';
                elseif (str_contains(strtolower($category), 'mesin') || str_contains(strtolower($category), 'peralatan')) $iconAset = '⚙️';
                elseif (str_contains(strtolower($category), 'gedung')) $iconAset = '🏢';
                elseif (str_contains(strtolower($category), 'jaringan') || str_contains(strtolower($category), 'jalan')) $iconAset = '🛣️';
                elseif (str_contains(strtolower($category), 'lainnya')) $iconAset = '📚';
                elseif (str_contains(strtolower($category), 'kdp')) $iconAset = '🏗️';
                elseif (str_contains(strtolower($category), 'tidak berwujud') || str_contains(strtolower($category), 'atb')) $iconAset = '💻';
            @endphp

            @include('pages.public_scan_partials.hero_card')
            @include('pages.public_scan_partials.card_lokasi')
            @include('pages.public_scan_partials.card_spesifikasi')

            <!-- Tombol Aksi Praktis (Bagikan Info) -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                <button type="button" onclick="shareAssetInfo()"
                        class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 active:scale-95 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-600/20 transition-all cursor-pointer">
                    <span>📤</span>
                    <span>Bagikan Identitas Barang</span>
                </button>
                <a href="javascript:window.location.reload()"
                   class="w-full sm:w-auto inline-flex items-center justify-center space-x-2 px-5 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                    <span>🔄</span>
                    <span>Muat Ulang Status</span>
                </a>
            </div>
        @else
            @include('pages.public_scan_partials.not_found')
        @endif
    </main>

    @include('pages.public_scan_partials.footer')
    @include('pages.public_scan_partials.scripts')

</body>
</html>
