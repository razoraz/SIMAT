<div class="relative overflow-hidden p-4 sm:p-6 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900/95 to-slate-950 border border-slate-800 shadow-2xl backdrop-blur-xl">
    <style>
        /* Mode Layar Medium ke Bawah (< 1024px): Rata Tengah Simetris */
        @media (max-width: 1023px) {
            .rmb-banner-wrap {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                gap: 1.25rem !important;
            }
            .rmb-text-col {
                width: 100% !important;
                text-align: center !important;
            }
            .rmb-badges-row,
            .rmb-title {
                justify-content: center !important;
                text-align: center !important;
            }
            .rmb-desc {
                margin-left: auto !important;
                margin-right: auto !important;
                text-align: center !important;
            }
            .rmb-filter-form {
                justify-content: center !important;
                width: 100% !important;
            }
        }
        /* Mode Desktop Luas (>= 1024px): Rata Kiri Standar */
        @media (min-width: 1024px) {
            .rmb-banner-wrap {
                flex-direction: row !important;
                align-items: center !important;
                justify-content: space-between !important;
            }
            .rmb-text-col {
                text-align: left !important;
            }
            .rmb-badges-row,
            .rmb-title {
                justify-content: flex-start !important;
                text-align: left !important;
            }
            .rmb-desc {
                margin-left: 0 !important;
                margin-right: 0 !important;
                text-align: left !important;
            }
            .rmb-filter-form {
                justify-content: flex-end !important;
                width: auto !important;
            }
        }
    </style>

    <div class="absolute -right-16 -top-16 w-64 h-64 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute right-32 -bottom-16 w-56 h-56 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="rmb-banner-wrap relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
        {{-- Sisi Kiri: Judul & Keterangan --}}
        <div class="rmb-text-col space-y-2 min-w-0">
            <div class="rmb-badges-row flex items-center space-x-2 sm:space-x-2.5 flex-wrap gap-y-1.5">
                <span class="px-3 py-1 rounded-full text-[10px] font-mono font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 flex items-center gap-1.5 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
                    <span>Audit &amp; Rekonsiliasi BMD</span>
                </span>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-slate-800/80 text-slate-300 border border-slate-700">
                    PMDN No. 108 / 2016
                </span>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-mono font-bold bg-cyan-500/10 text-cyan-300 border border-cyan-500/30">
                    RSUD dr. H. Koesnandi
                </span>
            </div>

            <h1 class="rmb-title text-xl sm:text-2xl font-black text-white tracking-tight flex flex-wrap items-center gap-2.5 break-words">
                <span>📑</span>
                <span>Rekonsiliasi Belanja Modal (RMB)</span>
            </h1>
            <p class="rmb-desc text-xs text-slate-400 max-w-3xl leading-relaxed break-words">
                Kertas kerja resmi rekapitulasi mutasi barang aset tetap (KIB A s/d F) beserta matriks <strong class="text-slate-200">21 Kolom Penambahan &amp; Pengurangan</strong> untuk menguji keseimbangan (<em class="text-emerald-300">balance</em>) antara Saldo Buku Aset Tetap di SIMDA BMD dengan Realisasi Kas Belanja Modal di LRA BPKAD.
            </p>
        </div>

        {{-- Sisi Kanan: Form Filter Periode --}}
        <form method="GET" action="{{ route('rmb.index') }}" class="rmb-filter-form flex items-center gap-2.5 flex-wrap shrink-0 max-w-full">
            {{-- Filter Tahun --}}
            <div class="space-y-1 min-w-0 max-w-full">
                <label class="block text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold text-left">Tahun:</label>
                <select name="tahun" onchange="this.form.submit()"
                        class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs font-mono font-bold text-white focus:outline-none focus:border-emerald-500 shadow-inner max-w-full cursor-pointer">
                    @foreach ($tahunList as $th)
                        <option value="{{ $th }}" {{ $selectedTahun == $th ? 'selected' : '' }}>
                            {{ $th }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Filter Triwulan / Periode --}}
            <div class="space-y-1 min-w-0 max-w-full">
                <label class="block text-[10px] font-mono uppercase tracking-wider text-slate-400 font-bold text-left">Periode:</label>
                <select name="triwulan" onchange="this.form.submit()"
                        class="px-3 py-2 bg-slate-950 border border-slate-800 rounded-xl text-xs font-semibold text-white focus:outline-none focus:border-emerald-500 shadow-inner max-w-full cursor-pointer">
                    <option value="all" {{ $selectedTw === 'all' ? 'selected' : '' }}>Seluruh Tahun</option>
                    <option value="1" {{ $selectedTw === '1' ? 'selected' : '' }}>Triwulan I (Q1)</option>
                    <option value="2" {{ $selectedTw === '2' ? 'selected' : '' }}>Triwulan II (Q2 / Semester 1)</option>
                    <option value="3" {{ $selectedTw === '3' ? 'selected' : '' }}>Triwulan III (Q3)</option>
                    <option value="4" {{ $selectedTw === '4' ? 'selected' : '' }}>Triwulan IV (Q4 / Tutup Buku)</option>
                </select>
            </div>
        </form>
    </div>
</div>
