<!-- HEADER BANNER & KPI STATISTIK HIBAH ASET -->
<div class="space-y-4">
    <style>
        /* Mode Layar Medium ke Bawah (< 1024px): Rata Tengah Simetris */
        @media (max-width: 1023px) {
            .hibah-banner-wrap {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                gap: 1.25rem !important;
            }
            .hibah-identity-wrap {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                width: 100% !important;
            }
            .hibah-heading-row {
                justify-content: center !important;
                text-align: center !important;
            }
            .hibah-desc {
                margin-left: auto !important;
                margin-right: auto !important;
                text-align: center !important;
            }
            .hibah-btn-wrap {
                justify-content: center !important;
                width: 100% !important;
                flex-wrap: wrap !important;
            }
        }
        /* Mode Desktop Luas (>= 1024px): Rata Kiri Standar */
        @media (min-width: 1024px) {
            .hibah-banner-wrap {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
                text-align: left !important;
            }
            .hibah-identity-wrap {
                flex-direction: row !important;
                align-items: center !important;
                text-align: left !important;
            }
            .hibah-heading-row {
                justify-content: flex-start !important;
                text-align: left !important;
            }
            .hibah-desc {
                margin-left: 0 !important;
                margin-right: 0 !important;
                text-align: left !important;
            }
            .hibah-btn-wrap {
                justify-content: flex-end !important;
                width: auto !important;
            }
        }
        /* KPI Grid Hibah: 1 col (<640), 2x2 (640-1279), 4x1 (>=1280) */
        .kpi-hibah-grid {
            display: grid;
            grid-template-columns: repeat(1, minmax(0, 1fr));
            gap: 0.75rem;
        }
        .kpi-hibah-grid > div {
            min-width: 0;
        }
        @media (min-width: 640px) {
            .kpi-hibah-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 0.875rem;
            }
        }
        @media (min-width: 1280px) {
            .kpi-hibah-grid {
                grid-template-columns: repeat(4, minmax(0, 1fr));
                gap: 0.875rem;
            }
        }
    </style>

    <!-- Top Header Banner Card with Subtle Ambient Glow -->
    <div class="hibah-banner-wrap flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 p-4 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl relative overflow-hidden backdrop-blur-md">
        <!-- Ambient Background Glow -->
        <div class="absolute -top-20 -left-20 w-56 h-56 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -right-20 w-56 h-56 bg-rose-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Left: Icon & Text Information -->
        <div class="hibah-identity-wrap flex items-start gap-4 relative z-10 min-w-0">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 via-amber-500 to-amber-600 flex items-center justify-center text-2xl shrink-0 shadow-lg shadow-amber-500/25 ring-1 ring-amber-400/30 mt-0.5">
                🎁
            </div>
            <div class="space-y-1 min-w-0">
                <div class="hibah-heading-row flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight">
                        Kelola Data Hibah Aset
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-amber-400/15 text-amber-300 border border-amber-400/30 font-mono shadow-sm">
                        Masuk &amp; Pengurangan Hibah
                    </span>
                </div>
                <p class="hibah-desc text-xs text-slate-400 max-w-2xl leading-relaxed">
                    Pencatatan mutasi bertambah (hibah masuk) &amp; pengurangan barang RSUD yang dihibahkan ke pihak lain beserta dokumen BAST resmi.
                </p>
            </div>
        </div>

        <!-- Right: Action Buttons Group (Symmetrical & Aligned) -->
        <div class="hibah-btn-wrap flex flex-wrap items-center gap-2.5 relative z-10 shrink-0 lg:self-center">
            <!-- Ekspor Excel Sipenerbang -->
            <button type="button" @click="openExportModal()"
                class="group px-4 py-2.5 rounded-xl bg-slate-950/80 hover:bg-emerald-950/30 border border-slate-800 hover:border-emerald-500/50 text-slate-300 hover:text-emerald-300 font-bold text-xs transition-all shadow-md flex items-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Sheet Hibah (Excel)</span>
            </button>

            <!-- Tambah Hibah Masuk (Primary Solid Amber) -->
            <a href="{{ route('astap.create_hibah') }}"
                class="group px-4 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-black text-xs transition-all duration-200 shadow-lg shadow-amber-500/20 hover:shadow-xl hover:shadow-amber-400/35 flex items-center gap-2 cursor-pointer active:scale-95 transform hover:-translate-y-0.5">
                <span class="text-sm inline-block group-hover:scale-110 transition-transform">🎁</span>
                <span>+ Tambah Hibah Masuk</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="kpi-hibah-grid">
        <!-- Card 1: Hibah Masuk Unit & Nominal -->
        <div class="p-4 rounded-3xl bg-slate-900/80 border border-slate-800 relative overflow-hidden shadow-xl group hover:border-amber-400/40 transition-all before:absolute before:top-0 before:left-0 before:right-0 before:h-0.5 before:bg-gradient-to-r before:from-transparent before:via-amber-400/80 before:to-transparent">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">🎁 Hibah Masuk</span>
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-amber-400/15 text-amber-300 border border-amber-400/20 shrink-0">
                    Aset Bertambah
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-white group-hover:text-amber-300 transition-colors">
                    {{ number_format($totalMasukUnit, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Unit</span>
                </div>
                <div class="text-xs font-extrabold text-amber-400 font-mono mt-0.5 truncate">
                    Rp {{ number_format($totalMasukNominal, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between gap-2 border-t border-slate-800/80 pt-2">
                <span class="truncate">Penerimaan Hibah Masuk</span>
                <span class="text-amber-400/80 font-bold shrink-0">KIB A - F / ATB</span>
            </div>
        </div>

        <!-- Card 2: Hibah Keluar Unit & Nominal -->
        <div class="p-4 rounded-3xl bg-slate-900/80 border border-slate-800 relative overflow-hidden shadow-xl group hover:border-rose-500/40 transition-all before:absolute before:top-0 before:left-0 before:right-0 before:h-0.5 before:bg-gradient-to-r before:from-transparent before:via-rose-500/80 before:to-transparent">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">📤 Hibah Keluar</span>
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-rose-500/15 text-rose-300 border border-rose-500/20 shrink-0">
                    Pengurangan AT
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-white group-hover:text-rose-300 transition-colors">
                    {{ number_format($totalKeluarUnit, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Unit</span>
                </div>
                <div class="text-xs font-extrabold text-rose-400 font-mono mt-0.5 truncate">
                    Rp {{ number_format($totalKeluarNominal, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between gap-2 border-t border-slate-800/80 pt-2">
                <span class="truncate">Dihibahkan ke luar RSUD</span>
                <span class="text-rose-400/80 font-bold shrink-0">Sheet 2 AT</span>
            </div>
        </div>

        <!-- Card 3: Instansi & Mitra Hibah (Universal) -->
        <div class="p-4 rounded-3xl bg-slate-900/80 border border-slate-800 relative overflow-hidden shadow-xl group hover:border-purple-500/40 transition-all before:absolute before:top-0 before:left-0 before:right-0 before:h-0.5 before:bg-gradient-to-r before:from-transparent before:via-purple-400/80 before:to-transparent">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">🏢 Instansi &amp; Mitra</span>
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-purple-500/15 text-purple-300 border border-purple-500/20 shrink-0">
                    Kerjasama
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-white group-hover:text-purple-300 transition-colors">
                    {{ number_format($totalInstansiUnik ?? 0, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Instansi</span>
                </div>
                <div class="text-xs font-extrabold text-purple-400 font-mono mt-0.5 truncate">
                    {{ $pemberiUnik ?? 0 }} Donatur · {{ $penerimaUnik ?? 0 }} Penerima
                </div>
            </div>
            <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between gap-2 border-t border-slate-800/80 pt-2">
                <span class="truncate">Pihak Hibah Terverifikasi</span>
                <span class="text-purple-400/80 font-bold shrink-0">Mitra Terdaftar</span>
            </div>
        </div>

        <!-- Card 4: Kondisi Fisik Aset Hibah (Universal) -->
        <div class="p-4 rounded-3xl bg-slate-900/80 border border-slate-800 relative overflow-hidden shadow-xl group hover:border-emerald-500/40 transition-all before:absolute before:top-0 before:left-0 before:right-0 before:h-0.5 before:bg-gradient-to-r before:from-transparent before:via-emerald-400/80 before:to-transparent">
            <div class="flex items-center justify-between gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider truncate">🩺 Kondisi Fisik Barang</span>
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-emerald-500/15 text-emerald-300 border border-emerald-500/20 shrink-0">
                    Kelaikan
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-white group-hover:text-emerald-300 transition-colors">
                    {{ number_format($totalBaik ?? 0, 0, ',', '.') }} <span class="text-xs font-semibold text-emerald-400">Unit Baik</span>
                </div>
                <div class="text-xs font-extrabold text-emerald-400 font-mono mt-0.5 truncate">
                    {{ $persenBaik ?? 100 }}% Laik Operasional
                </div>
            </div>
            <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between gap-2 border-t border-slate-800/80 pt-2">
                <span class="truncate">Kondisi Aset Serah Terima</span>
                <span class="text-emerald-400/80 font-bold shrink-0">
                    @if(($totalRusak ?? 0) > 0)
                        <span class="text-amber-400">{{ $totalRusak }} Perlu Cek</span>
                    @else
                        Siap Pakai
                    @endif
                </span>
            </div>
        </div>
    </div>
</div>