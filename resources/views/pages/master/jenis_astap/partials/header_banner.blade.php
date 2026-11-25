@if ($errors->any())
    <div class="mb-4 p-4 rounded-2xl bg-rose-500/20 border border-rose-500/30 text-rose-300 text-xs font-semibold shadow-lg space-y-1">
        <div class="font-bold flex items-center space-x-1.5">
            <span>⚠️ Terjadi kesalahan:</span>
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
            .jenis-banner-wrap {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                gap: 1.25rem !important;
            }
            .jenis-text-col {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                width: 100% !important;
            }
            .jenis-badge,
            .jenis-desc {
                margin-left: auto !important;
                margin-right: auto !important;
                text-align: center !important;
            }
            .jenis-btn-wrap {
                justify-content: center !important;
                width: 100% !important;
            }
        }
        /* Mode Desktop Luas (>= 1024px): Rata Kiri Standar */
        @media (min-width: 1024px) {
            .jenis-banner-wrap {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
            }
            .jenis-text-col {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                text-align: left !important;
            }
            .jenis-badge,
            .jenis-desc {
                margin-left: 0 !important;
                margin-right: 0 !important;
                text-align: left !important;
            }
            .jenis-btn-wrap {
                justify-content: flex-end !important;
                width: auto !important;
            }
        }
    </style>

    <div class="jenis-banner-wrap flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
        <div class="jenis-text-col min-w-0 flex-1">
            <div class="jenis-badge inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider mb-3 shadow-sm backdrop-blur-md max-w-full">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shadow-sm shadow-emerald-400 shrink-0"></span>
                <span class="truncate sm:whitespace-normal">KODE 108 PERMENDAGRI (BARANG MILIK DAERAH)</span>
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight break-words">Master Jenis ASTAP (Kode 108 BMD)</h1>
            <p class="jenis-desc text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed break-words">
                Struktur 3 tingkatan klasifikasi aset tetap resmi pemerintah daerah (Jenis Utama, Sub Rincian Objek, dan Sub-Sub Rincian Objek).
            </p>
        </div>
        
        <div class="jenis-btn-wrap flex flex-wrap sm:flex-nowrap items-center gap-2 shrink-0">
            <button type="button" @click="showImportModal = true"
                class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 font-bold text-xs border border-emerald-500/30 shadow-lg transition-all flex items-center space-x-2 whitespace-nowrap shrink-0 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import CSV/Excel</span>
            </button>
            <button type="button" @click="openAdd()"
                class="px-3.5 sm:px-4 py-2 sm:py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 whitespace-nowrap shrink-0 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Jenis ASTAP</span>
            </button>
        </div>
    </div>

    <!-- Mini Summary KPI Cards Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4 mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-slate-800/80">
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3 sm:p-4 flex items-center space-x-3 min-w-0">
            <div class="p-2.5 sm:p-3 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg sm:text-xl shrink-0">🏛️</div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Total Objek Aset</span>
                <span class="text-sm sm:text-base lg:text-lg font-extrabold text-emerald-400 truncate block">{{ number_format($totalCount ?? 0) }} Data Kode 108</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3 sm:p-4 flex items-center space-x-3 min-w-0">
            <div class="p-2.5 sm:p-3 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg sm:text-xl shrink-0">📋</div>
            <div class="min-w-0 flex-1">
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Klasifikasi Aset</span>
                <span class="text-sm sm:text-base lg:text-lg font-extrabold text-cyan-300 truncate block">8 Kategori Aset Tetap</span>
            </div>
        </div>
    </div>
</div>
