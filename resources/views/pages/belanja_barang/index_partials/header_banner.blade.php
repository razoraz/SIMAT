<!-- ========================================================================= -->
<!-- HEADER BANNER & 4 STATISTIK KPI MASTER BELANJA BARANG                     -->
<!-- ========================================================================= -->
<div class="space-y-4">
    <!-- Top Header Banner -->
    <style>
        /* Mode Layar Medium ke Bawah (< 1024px): Rata Tengah Simetris */
        @media (max-width: 1023px) {
            .belanja-banner-wrap {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                gap: 1.25rem !important;
            }
            .belanja-header-content {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                width: 100% !important;
                gap: 0.875rem !important;
            }
            .belanja-header-text {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                width: 100% !important;
            }
            .belanja-title-wrap {
                justify-content: center !important;
                text-align: center !important;
            }
            .belanja-desc {
                text-align: center !important;
                margin-left: auto !important;
                margin-right: auto !important;
            }
            .belanja-btn-wrap {
                justify-content: center !important;
                width: 100% !important;
            }
        }
        /* Mode Desktop Luas (>= 1024px): Rata Kiri Standar */
        @media (min-width: 1024px) {
            .belanja-banner-wrap {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
            }
            .belanja-header-content {
                flex-direction: row !important;
                align-items: center !important;
                text-align: left !important;
            }
            .belanja-header-text {
                align-items: flex-start !important;
                text-align: left !important;
            }
            .belanja-title-wrap {
                justify-content: flex-start !important;
                text-align: left !important;
            }
            .belanja-desc {
                text-align: left !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
            }
            .belanja-btn-wrap {
                justify-content: flex-end !important;
                width: auto !important;
            }
        }
    </style>

    <div class="belanja-banner-wrap flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-4 sm:p-5 lg:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl relative overflow-hidden">
        <div class="belanja-header-content flex items-center gap-3.5 sm:gap-4 min-w-0">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-2xl shrink-0 shadow-lg shadow-indigo-500/10">
                📦
            </div>
            <div class="belanja-header-text min-w-0">
                <div class="belanja-title-wrap flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight break-words">
                        Kelola Belanja Barang (Instalasi Perbekalan / Gudang)
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 max-w-full">
                        Pengadaan Mandiri · Non-Belanja Modal
                    </span>
                </div>
                <p class="belanja-desc text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed break-words">
                    Pusat penatausahaan dan inventarisasi aset yang dibeli langsung oleh Instalasi Perbekalan / Gudang menggunakan anggaran perbekalan sendiri (bukan belanja modal) untuk pengawasan fisik inventaris ruangan (KIR).
                </p>
            </div>
        </div>

        <!-- Tombol Aksi Cepat -->
        <div class="belanja-btn-wrap flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('astap.create_belanja_barang') }}"
                class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-400 hover:to-purple-500 text-white text-xs font-extrabold shadow-lg shadow-indigo-500/20 transition-all flex items-center gap-1.5 active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Catat Belanja Barang</span>
            </a>
        </div>
    </div>

    <!-- 4 Kartu Statistik KPI Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- KPI 1: Total Realisasi Belanja -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-indigo-500/50 transition-all min-w-0">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL REALISASI PEMBELIAN</span>
                <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400 text-xs shrink-0">💰</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1 min-w-0">
                <span class="text-xs font-bold text-slate-500 font-mono shrink-0">Rp</span>
                <span class="text-lg sm:text-xl font-black text-white font-mono truncate" x-text="formatRupiah({{ $totalNilaiBelanja ?? 0 }})"></span>
            </div>
            <p class="text-[10px] text-indigo-400/80 mt-1">Dana Operasional Perbekalan / Gudang</p>
        </div>

        <!-- KPI 2: Total Fisik Unit -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL FISIK BARANG</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs">📦</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-emerald-400 font-mono" x-text="{{ $totalVolumeUnit ?? 0 }}"></span>
                <span class="text-xs font-semibold text-slate-400">Unit / Pcs Terdaftar</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Tercatat di Inventaris Ruangan (KIR)</p>
        </div>

        <!-- KPI 3: Total Faktur -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-blue-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL FAKTUR / KUITANSI</span>
                <span class="p-1.5 rounded-lg bg-blue-500/10 text-blue-400 text-xs">🧾</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-blue-400 font-mono" x-text="{{ $totalFaktur ?? 0 }}"></span>
                <span class="text-xs font-semibold text-slate-400">Dokumen Pembelian</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Nomor Faktur / Bukti Sah Perbekalan</p>
        </div>

        <!-- KPI 4: Toko / Rekanan Penyedia -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-purple-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOKO / REKANAN PENYEDIA</span>
                <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400 text-xs">🏬</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-purple-400 font-mono" x-text="{{ $totalTokoUnik ?? 0 }}"></span>
                <span class="text-xs font-semibold text-slate-400">Penyedia Terdaftar</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Pusat Perbekalan &amp; Rekanan Toko</p>
        </div>
    </div>
</div>
