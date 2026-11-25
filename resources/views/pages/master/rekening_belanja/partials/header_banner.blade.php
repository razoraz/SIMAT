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
<div class="bg-gradient-to-r from-blue-600/15 via-slate-900 to-slate-900 border border-blue-500/30 rounded-3xl p-4 sm:p-6 lg:p-8 shadow-2xl mb-6 relative overflow-hidden">
    <style>
        /* Mode Layar Medium ke Bawah (< 1024px): Rata Tengah Simetris */
        @media (max-width: 1023px) {
            .rekening-banner-wrap {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                gap: 1.25rem !important;
            }
            .rekening-text-col {
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                width: 100% !important;
            }
            .rekening-badge,
            .rekening-desc {
                margin-left: auto !important;
                margin-right: auto !important;
                text-align: center !important;
            }
            .rekening-btn-wrap {
                display: flex !important;
                justify-content: center !important;
                width: 100% !important;
            }
        }
        /* Mode Desktop Luas (>= 1024px): Rata Kiri Standar */
        @media (min-width: 1024px) {
            .rekening-banner-wrap {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
            }
            .rekening-text-col {
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                text-align: left !important;
            }
            .rekening-badge,
            .rekening-desc {
                margin-left: 0 !important;
                margin-right: 0 !important;
                text-align: left !important;
            }
            .rekening-btn-wrap {
                display: flex !important;
                justify-content: flex-end !important;
                width: auto !important;
            }
        }
    </style>

    <div class="rekening-banner-wrap flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
        <div class="rekening-text-col min-w-0 flex-1">
            <div class="rekening-badge inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/30 text-blue-300 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider mb-3 shadow-sm backdrop-blur-md max-w-full">
                <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse shadow-sm shadow-blue-400 shrink-0"></span>
                <span class="truncate sm:whitespace-normal">AKUN REKENING BELANJA PENGADAAN SIPD RSUD</span>
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight break-words">Rekening Belanja Pengadaan SIPD</h1>
            <p class="rekening-desc text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed break-words">
                Daftar akun rekening belanja modal (Akun 5.2) untuk perolehan aset tetap rumah sakit sesuai kodefikasi DPA SIPD Kabupaten Bondowoso.
            </p>
        </div>
        
        <div class="rekening-btn-wrap shrink-0">
            <button type="button" @click="openAdd()"
                class="px-4 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-bold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2 shrink-0 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Rekening Belanja</span>
            </button>
        </div>
    </div>

    <!-- Mini Summary KPI Cards Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-slate-800/80">
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
            <div class="p-2 sm:p-2.5 rounded-xl bg-blue-500/10 text-blue-400 text-base sm:text-lg shrink-0">💳</div>
            <div class="min-w-0 flex-1">
                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Total Rekening</span>
                <span class="text-xs sm:text-sm lg:text-base font-extrabold text-blue-400 truncate block">{{ $totalCount }} Akun Belanja</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
            <div class="p-2 sm:p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-base sm:text-lg shrink-0">🔬</div>
            <div class="min-w-0 flex-1">
                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Modal Alat &amp; Mesin</span>
                <span class="text-xs sm:text-sm lg:text-base font-extrabold text-cyan-300 truncate block">Akun 5.2.02</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
            <div class="p-2 sm:p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-base sm:text-lg shrink-0">🏢</div>
            <div class="min-w-0 flex-1">
                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Modal Bangunan</span>
                <span class="text-xs sm:text-sm lg:text-base font-extrabold text-purple-300 truncate block">Akun 5.2.03</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
            <div class="p-2 sm:p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-base sm:text-lg shrink-0">🔗</div>
            <div class="min-w-0 flex-1">
                <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Sinkronisasi</span>
                <span class="text-xs sm:text-sm lg:text-base font-extrabold text-emerald-300 truncate block">SIPD RSUD</span>
            </div>
        </div>
    </div>
</div>
