<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Identitas Resmi Aset - SIMAT-RK RSUD Dr. H. Koesnandi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen flex flex-col justify-between selection:bg-emerald-500 selection:text-slate-950 antialiased">

    <!-- TOP HEADER NAVBAR RESMI RSUD -->
    <header class="bg-slate-900/90 border-b border-slate-800/90 sticky top-0 z-50 backdrop-blur-xl shadow-lg shadow-black/20">
        <div class="max-w-3xl mx-auto px-4 py-3.5 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-white/5 border border-white/10 p-1.5 flex items-center justify-center shadow-md shrink-0">
                    <img src="/img/Logo-rsud/logo-rsud.png" alt="Logo RSUD" class="w-full h-full object-contain" onerror="this.src='/img/logo-bondowoso.png'">
                </div>
                <div>
                    <span class="text-[9.5px] sm:text-[10px] font-extrabold tracking-widest text-emerald-400 uppercase block leading-none mb-0.5">
                        RSUD DR. H. KOESNANDI
                    </span>
                    <h1 class="text-xs sm:text-sm font-extrabold text-white tracking-tight leading-tight">
                        SIMAT-RK • Identitas Fisik Aset
                    </h1>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 text-[10.5px] font-bold">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="hidden xs:inline">QR Terverifikasi</span>
                    <span class="xs:hidden">Resmi</span>
                </span>
            </div>
        </div>
    </header>

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

        <!-- ========================================================================= -->
        <!-- KARTU IDENTITAS RESMI ASET (VERIFIED ASSET HERO CARD)                      -->
        <!-- ========================================================================= -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border border-slate-800 p-5 sm:p-7 shadow-2xl shadow-emerald-950/20">
            <!-- Glow background decor -->
            <div class="absolute -right-16 -top-16 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 space-y-4">
                <!-- Header Badge & Kategori -->
                <div class="flex flex-wrap items-center justify-between gap-2.5">
                    <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 text-[11px] font-bold tracking-wide">
                        <span>🛡️</span>
                        <span>BARANG MILIK RSUD DR. H. KOESNANDI</span>
                    </span>

                    <span class="px-3 py-1 rounded-xl bg-slate-800/80 text-slate-300 border border-slate-700/70 text-[11px] font-bold">
                        {{ $iconAset }} {{ $category }}
                    </span>
                </div>

                <!-- Nama Barang Fisik -->
                <div>
                    <span class="text-[10.5px] uppercase font-extrabold text-slate-400 tracking-wider block mb-1">
                        Nama Barang / Inventaris Fisik:
                    </span>
                    <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-snug">
                        {{ $astap->nama_barang ?? 'Aset Inventaris RSUD' }}
                    </h2>
                    @if(!empty($judul) && strtolower($judul) !== strtolower($astap->nama_barang ?? ''))
                    <p class="text-emerald-400 text-sm font-semibold mt-1">
                        Judul / Seri: {{ $judul }}
                    </p>
                    @endif
                </div>

                <!-- NIBAR & Kode Register Unit -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                            Nomor Induk Barang (NIBAR) &amp; Register:
                        </span>
                        <div class="flex flex-wrap items-baseline gap-2">
                            <span id="nibarText" class="font-mono text-sm sm:text-base font-black text-emerald-400 tracking-wider select-all">
                                {{ $register->nibar ?? ($nibar ?? $astap->kode_barang) }}
                            </span>
                            @if($register && !empty($register->no_register_int))
                            <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-300 font-mono text-xs font-bold border border-emerald-500/20">
                                Unit #{{ str_pad($register->no_register_int, 4, '0', STR_PAD_LEFT) }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <!-- Tombol Salin NIBAR -->
                    <button type="button" onclick="copyNibar()" id="copyBtn"
                            class="inline-flex items-center justify-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 text-xs font-bold transition-all border border-slate-700 cursor-pointer shrink-0">
                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        <span id="copyBtnLabel">Salin NIBAR</span>
                    </button>
                </div>

                <!-- Status Kondisi & Ketersediaan -->
                <div class="grid grid-cols-2 gap-3 pt-1">
                    <!-- Kondisi Fisik -->
                    <div class="p-3 rounded-2xl border
                        @if($kondisi === 'Baik') bg-emerald-500/10 border-emerald-500/30 text-emerald-300
                        @elseif($kondisi === 'Kurang Baik' || $kondisi === 'Rusak Ringan') bg-amber-500/10 border-amber-500/30 text-amber-300
                        @else bg-rose-500/10 border-rose-500/30 text-rose-300 @endif">
                        <span class="text-[9.5px] uppercase font-extrabold opacity-70 tracking-wider block mb-0.5">
                            Kondisi Fisik Terkini
                        </span>
                        <span class="text-xs sm:text-sm font-black flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full
                                @if($kondisi === 'Baik') bg-emerald-400
                                @elseif($kondisi === 'Kurang Baik' || $kondisi === 'Rusak Ringan') bg-amber-400
                                @else bg-rose-400 @endif"></span>
                            <span>{{ $kondisi }}</span>
                        </span>
                    </div>

                    <!-- Status Penggunaan -->
                    <div class="p-3 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-300">
                        <span class="text-[9.5px] uppercase font-extrabold opacity-70 tracking-wider block mb-0.5">
                            Status Penggunaan
                        </span>
                        <span class="text-xs sm:text-sm font-black flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                            <span>{{ $register->status ?? 'Aktif Digunakan' }}</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- LOKASI PENEMPATAN & UNIT PEMEGANG BARANG                                  -->
        <!-- ========================================================================= -->
        <div class="p-4 sm:p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                <span class="text-xs font-extrabold text-white flex items-center space-x-2 tracking-wide uppercase">
                    <span class="text-teal-400 text-sm">📍</span>
                    <span>Lokasi Penempatan &amp; Unit Pemegang</span>
                </span>
                <span class="text-[10.5px] text-teal-400/90 font-bold px-2 py-0.5 rounded-lg bg-teal-500/10 border border-teal-500/20">
                    Fisik Barang
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                <!-- Ruangan / Paviliun -->
                <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                        Ruang / Paviliun / Instalasi:
                    </span>
                    <div class="text-sm font-extrabold text-white">
                        @if(!empty($ruang))
                            <span class="text-teal-300 flex items-center space-x-1.5">
                                <span>🏛️</span>
                                <span>{{ $ruang }}</span>
                            </span>
                        @else
                            <span class="text-amber-400 flex items-center space-x-1.5">
                                <span>⚠️</span>
                                <span>Gudang Aset / Belum Ditempatkan</span>
                            </span>
                        @endif
                    </div>
                    <p class="text-[10px] text-slate-400 leading-normal pt-0.5">
                        Unit kerja yang bertanggung jawab memegang barang fisik ini.
                    </p>
                </div>

                <!-- Gedung / Letak Kompleks -->
                <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800/80 space-y-1">
                    <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                        Alamat / Kompleks Rumah Sakit:
                    </span>
                    <div class="text-sm font-extrabold text-white flex items-center space-x-1.5">
                        <span>🏥</span>
                        <span class="text-slate-200 truncate">
                            {{ $astap->alamat_barang ?? ($astap->letak_lokasi ?? 'Kompleks RSUD Dr. H. Koesnandi') }}
                        </span>
                    </div>
                    <p class="text-[10px] text-slate-400 leading-normal pt-0.5">
                        Jl. Piere Tendean No. 1, Kabupaten Bondowoso, Jawa Timur.
                    </p>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SPESIFIKASI FISIK & CIRI-CIRI BARANG                                     -->
        <!-- ========================================================================= -->
        <div class="p-4 sm:p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                <span class="text-xs font-extrabold text-white flex items-center space-x-2 tracking-wide uppercase">
                    <span class="text-emerald-400 text-sm">🔍</span>
                    <span>Spesifikasi &amp; Ciri Fisik Barang</span>
                </span>
                <span class="text-[10.5px] text-emerald-400/90 font-mono font-bold px-2 py-0.5 rounded-lg bg-emerald-500/10 border border-emerald-500/20">
                    Detail Pengenal
                </span>
            </div>

            <!-- Grid Atribut Barang -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                <!-- Merk / Brand -->
                <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">🏷️ Merk / Brand</span>
                    <span class="text-white font-extrabold text-xs block truncate" title="{{ $merk ?: '-' }}">
                        {{ $merk ?: '-' }}
                    </span>
                </div>

                <!-- Tipe / Model -->
                <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">📐 Tipe / Model</span>
                    <span class="text-cyan-300 font-extrabold text-xs block truncate" title="{{ $tipe ?: '-' }}">
                        {{ $tipe ?: '-' }}
                    </span>
                </div>

                <!-- Ukuran / Dimensi -->
                <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">📏 Ukuran / CC</span>
                    <span class="text-white font-extrabold text-xs block truncate">
                        {{ $ukuran ? $ukuran : '-' }}
                    </span>
                </div>

                <!-- Bahan / Material -->
                <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">🪵 Bahan / Material</span>
                    <span class="text-amber-300 font-extrabold text-xs block truncate">
                        {{ $bahan ?: '-' }}
                    </span>
                </div>

                <!-- Nomor Pabrik / Seri -->
                <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">🔢 No. Pabrik / Seri</span>
                    <span class="text-emerald-300 font-mono font-bold text-xs block truncate" title="{{ $noPabrik ?: '-' }}">
                        {{ $noPabrik ?: '-' }}
                    </span>
                </div>

                <!-- Tahun Masuk / Perolehan -->
                <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">📅 Tahun Pengadaan</span>
                    <span class="text-white font-mono font-black text-xs block">
                        Tahun {{ $astap->tahun_perolehan ?? '-' }}
                    </span>
                </div>

                <!-- Satuan Barang -->
                <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">📦 Satuan Fisik</span>
                    <span class="text-purple-300 font-bold text-xs block">
                        1 {{ $astap->satuan ?? 'Unit' }}
                    </span>
                </div>

                <!-- Klasifikasi 108 -->
                <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800/80">
                    <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block mb-0.5">🏛️ Kode Klasifikasi</span>
                    <span class="text-slate-300 font-mono text-[11px] font-bold block truncate" title="{{ $astap->kode_barang ?? '-' }}">
                        {{ $astap->kode_barang ?? '-' }}
                    </span>
                </div>
            </div>

            <!-- Ciri Khusus Tambahan (Jika Kendaraan / Buku / Lisensi ATB) -->
            @if(!empty($noPolisi) || !empty($noRangka) || !empty($noMesin) || !empty($pencipta) || !empty($spesifikasiKhusus))
            <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800/90 text-xs space-y-2 mt-2">
                <span class="text-[10px] font-extrabold text-emerald-400 uppercase tracking-wider block">
                    Ciri Tambahan &amp; Keterangan Spesifik:
                </span>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-[11px]">
                    @if(!empty($noPolisi))
                    <div class="flex items-center justify-between border-b border-slate-800/60 pb-1">
                        <span class="text-slate-400">Nomor Polisi (Plat):</span>
                        <strong class="text-amber-300 font-mono">{{ $noPolisi }}</strong>
                    </div>
                    @endif
                    @if(!empty($noRangka))
                    <div class="flex items-center justify-between border-b border-slate-800/60 pb-1">
                        <span class="text-slate-400">Nomor Rangka:</span>
                        <strong class="text-slate-200 font-mono">{{ $noRangka }}</strong>
                    </div>
                    @endif
                    @if(!empty($noMesin))
                    <div class="flex items-center justify-between border-b border-slate-800/60 pb-1">
                        <span class="text-slate-400">Nomor Mesin:</span>
                        <strong class="text-slate-200 font-mono">{{ $noMesin }}</strong>
                    </div>
                    @endif
                    @if(!empty($pencipta))
                    <div class="flex items-center justify-between border-b border-slate-800/60 pb-1">
                        <span class="text-slate-400">Pencipta / Vendor:</span>
                        <strong class="text-cyan-300">{{ $pencipta }}</strong>
                    </div>
                    @endif
                    @if(!empty($spesifikasiKhusus))
                    <div class="sm:col-span-2 pt-1">
                        <span class="text-slate-400 block text-[10px] mb-0.5">Spesifikasi Detail:</span>
                        <p class="text-slate-300 text-xs leading-relaxed bg-slate-900/60 p-2.5 rounded-xl border border-slate-800">
                            {{ $spesifikasiKhusus }}
                        </p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

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
        <!-- ========================================================================= -->
        <!-- NOT FOUND STATE                                                           -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-rose-500/30 rounded-3xl p-8 sm:p-10 shadow-2xl text-center space-y-4 max-w-md mx-auto my-12">
            <div class="w-16 h-16 rounded-3xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-3xl mx-auto border border-rose-500/30 shadow-lg shadow-rose-500/10">
                🔍
            </div>
            <div class="space-y-1.5">
                <h2 class="text-xl font-black text-white">Data Aset Tidak Ditemukan</h2>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Barcode / Nomor Register <span class="font-mono text-amber-400 font-bold">{{ $nibar }}</span> tidak terdaftar dalam database inventaris resmi SIMAT-RK RSUD Dr. H. Koesnandi.
                </p>
            </div>
            <div class="pt-2">
                <a href="/" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700">
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
        @endif

    </main>

    <!-- FOOTER RESMI -->
    <footer class="bg-slate-900/80 border-t border-slate-800/80 py-6 text-center text-xs text-slate-400 mt-8">
        <div class="max-w-3xl mx-auto px-4 space-y-1.5">
            <div class="flex items-center justify-center space-x-2 text-slate-300 font-extrabold text-xs">
                <span>RSUD Dr. H. Koesnandi Bondowoso</span>
                <span>•</span>
                <span class="text-emerald-400">SIMAT-RK</span>
            </div>
            <p class="text-[11px] text-slate-400">
                Sistem Informasi Manajemen Aset Tetap &amp; Rumah Tangga Rumah Sakit
            </p>
            <p class="text-[10px] text-slate-400 font-mono pt-1">
                Waktu Pindai QR: {{ date('d M Y, H:i') }} WIB • Terverifikasi Otentik
            </p>
        </div>
    </footer>

    <!-- SCRIPT COPY & SHARE -->
    <script>
        function copyNibar() {
            const nibar = document.getElementById('nibarText')?.innerText?.trim();
            if (!nibar) return;
            navigator.clipboard.writeText(nibar).then(() => {
                const btnLabel = document.getElementById('copyBtnLabel');
                const copyBtn = document.getElementById('copyBtn');
                if (btnLabel && copyBtn) {
                    btnLabel.innerText = 'Tersalin!';
                    copyBtn.classList.add('bg-emerald-600', 'text-slate-950');
                    copyBtn.classList.remove('bg-slate-800', 'text-slate-200');
                    setTimeout(() => {
                        btnLabel.innerText = 'Salin NIBAR';
                        copyBtn.classList.remove('bg-emerald-600', 'text-slate-950');
                        copyBtn.classList.add('bg-slate-800', 'text-slate-200');
                    }, 2000);
                }
            }).catch(err => {
                console.error('Gagal menyalin:', err);
            });
        }

        function shareAssetInfo() {
            const nama = @json($astap->nama_barang ?? 'Aset RSUD');
            const nibar = @json($register->nibar ?? ($nibar ?? ''));
            const ruang = @json($ruang ?? 'RSUD Dr. H. Koesnandi');
            const kondisi = @json($kondisi ?? 'Baik');

            const text = `*VERIFIKASI ASET SIMAT-RK RSUD DR. H. KOESNANDI*\n\n` +
                         `• *Barang:* ${nama}\n` +
                         `• *NIBAR:* ${nibar}\n` +
                         `• *Lokasi/Ruang:* ${ruang}\n` +
                         `• *Kondisi:* ${kondisi}\n` +
                         `• *Link Verifikasi:* ${window.location.href}`;

            if (navigator.share) {
                navigator.share({
                    title: 'Identitas Aset ' + nama,
                    text: text,
                    url: window.location.href
                }).catch(console.warn);
            } else {
                navigator.clipboard.writeText(text).then(() => {
                    alert('📋 Informasi aset telah disalin ke clipboard! Anda dapat menempelkannya ke WhatsApp atau catatan.');
                });
            }
        }
    </script>

</body>
</html>
