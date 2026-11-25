@if ($errors->any())
    <div class="mb-5 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-semibold shadow-lg">
        <div class="flex items-center space-x-2.5 mb-1.5">
            <span class="text-base">⚠️</span>
            <span class="font-bold">Terjadi kesalahan validasi:</span>
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-200">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<!-- Header Banner & Mini KPI Strip -->
<div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-4 sm:p-6 lg:p-8 shadow-2xl mb-6 relative overflow-hidden">
    <style>
        /* Mode Layar Medium ke Bawah (< 1024px): Rata Tengah Simetris */
        @media (max-width: 1023px) {
            .pengadaan-banner-wrap {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                gap: 1.25rem !important;
            }
            .pengadaan-text-col {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                width: 100% !important;
            }
            .pengadaan-badge,
            .pengadaan-desc {
                margin-left: auto !important;
                margin-right: auto !important;
                text-align: center !important;
            }
            .pengadaan-btn-wrap {
                display: flex !important;
                justify-content: center !important;
                width: 100% !important;
            }
        }
        /* Mode Desktop Luas (>= 1024px): Rata Kiri Standar */
        @media (min-width: 1024px) {
            .pengadaan-banner-wrap {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
            }
            .pengadaan-text-col {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                text-align: left !important;
            }
            .pengadaan-badge,
            .pengadaan-desc {
                margin-left: 0 !important;
                margin-right: 0 !important;
                text-align: left !important;
            }
            .pengadaan-btn-wrap {
                display: flex !important;
                justify-content: flex-end !important;
                width: auto !important;
            }
        }
    </style>

    <div class="pengadaan-banner-wrap flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
        <div class="pengadaan-text-col min-w-0 flex-1">
            <div class="pengadaan-badge inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] sm:text-xs font-bold mb-3 max-w-full">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                <span class="truncate sm:whitespace-normal">HIERARKI PENGADAAN SIPD (SISTEM INFORMASI PEMERINTAH DAERAH)</span>
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight break-words">Jenis &amp; Hierarki Pengadaan SIPD</h1>
            <p class="pengadaan-desc text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed break-words">
                Struktur 3 tingkatan penganggaran pengadaan barang &amp; jasa (Program Pengadaan, Kegiatan Pengadaan, dan Sub Kegiatan Pengadaan) sesuai standar DPA SIPD.
            </p>
        </div>
        
        <div class="pengadaan-btn-wrap shrink-0">
            <button type="button" @click="openAdd()"
                class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 shrink-0 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Pengadaan SIPD</span>
            </button>
        </div>
    </div>

    <!-- Mini Summary KPI Cards Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-slate-800/80">
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
            <div class="p-2 sm:p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-base sm:text-lg shrink-0">📑</div>
            <div class="min-w-0 flex-1">
                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Total Program</span>
                <span class="text-xs sm:text-sm lg:text-base font-extrabold text-emerald-400 truncate block">{{ count($uniquePrograms) }} Program</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
            <div class="p-2 sm:p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-base sm:text-lg shrink-0">📁</div>
            <div class="min-w-0 flex-1">
                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Total Kegiatan</span>
                <span class="text-xs sm:text-sm lg:text-base font-extrabold text-amber-300 truncate block">{{ count($uniqueKegiatan) }} Kegiatan</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
            <div class="p-2 sm:p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-base sm:text-lg shrink-0">📄</div>
            <div class="min-w-0 flex-1">
                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Total Sub Kegiatan</span>
                <span class="text-xs sm:text-sm lg:text-base font-extrabold text-purple-300 truncate block">{{ $totalCount }} Sub Kegiatan</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
            <div class="p-2 sm:p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-base sm:text-lg shrink-0">🔗</div>
            <div class="min-w-0 flex-1">
                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Sinkronisasi</span>
                <span class="text-xs sm:text-sm lg:text-base font-extrabold text-cyan-300 truncate block">DPA SIPD RSUD</span>
            </div>
        </div>
    </div>
</div>
