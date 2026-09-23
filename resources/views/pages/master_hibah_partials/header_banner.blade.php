<!-- HEADER BANNER & KPI STATISTIK HIBAH ASET -->
<div class="space-y-5">
    <!-- Top Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-amber-600 flex items-center justify-center text-2xl shadow-lg shadow-amber-500/25">
                    🎁
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-white tracking-tight">
                            Kelola Data Hibah Aset
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400/15 text-amber-300 border border-amber-400/30">
                            RMB &amp; PENGURANGAN HIBAH
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pencatatan mutasi bertambah (hibah masuk) &amp; pengurangan barang RSUD yang dihibahkan ke pihak lain beserta dokumen BAST resmi.
                    </p>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Ekspor Excel Sipenerbang -->
            <button type="button" @click="openExportModal()"
                class="px-4 py-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/40 text-emerald-400 hover:text-emerald-300 font-bold text-xs transition-all shadow-lg shadow-black/20 flex items-center space-x-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Sheet RMB Hibah (Excel)</span>
            </button>

            <!-- Catat Hibah Keluar -->
            <button type="button" @click="openModalHibahKeluar()"
                class="px-4 py-2.5 rounded-2xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-300 hover:text-rose-200 font-bold text-xs transition-all shadow-lg shadow-black/20 flex items-center space-x-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <span>📤 Hibahkan Barang (Keluar)</span>
            </button>

            <!-- Tambah Hibah Masuk -->
            <a href="{{ route('astap.create_hibah') }}"
                class="px-5 py-2.5 rounded-2xl bg-gradient-to-r from-amber-400 to-amber-500 hover:from-amber-300 hover:to-amber-400 text-slate-950 font-black text-xs transition-all shadow-xl shadow-amber-400/20 flex items-center space-x-2 cursor-pointer active:scale-95">
                <span>🎁</span>
                <span>+ Tambah Hibah Masuk</span>
            </a>
        </div>
    </div>

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Hibah Masuk Unit & Nominal -->
        <div class="p-4 rounded-3xl bg-slate-900/80 border border-slate-800 relative overflow-hidden shadow-xl group hover:border-amber-400/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">🎁 Hibah Masuk</span>
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-amber-400/15 text-amber-300 border border-amber-400/20">
                    Aset Bertambah
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-white group-hover:text-amber-300 transition-colors">
                    {{ number_format($totalMasukUnit, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Unit</span>
                </div>
                <div class="text-xs font-extrabold text-amber-400 font-mono mt-0.5">
                    Rp {{ number_format($totalMasukNominal, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between border-t border-slate-800/80 pt-2">
                <span>RMB Hibah Pihak Ketiga</span>
                <span class="text-amber-400/80 font-bold">KIB A - F / ATB</span>
            </div>
        </div>

        <!-- Card 2: Hibah Keluar Unit & Nominal -->
        <div class="p-4 rounded-3xl bg-slate-900/80 border border-slate-800 relative overflow-hidden shadow-xl group hover:border-rose-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">📤 Hibah Keluar</span>
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-rose-500/15 text-rose-300 border border-rose-500/20">
                    Pengurangan AT
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-white group-hover:text-rose-300 transition-colors">
                    {{ number_format($totalKeluarUnit, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Unit</span>
                </div>
                <div class="text-xs font-extrabold text-rose-400 font-mono mt-0.5">
                    Rp {{ number_format($totalKeluarNominal, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between border-t border-slate-800/80 pt-2">
                <span>Dihibahkan ke luar RSUD</span>
                <span class="text-rose-400/80 font-bold">Sheet 2 AT</span>
            </div>
        </div>

        <!-- Card 3: Total Transaksi Dokumen BAST -->
        <div class="p-4 rounded-3xl bg-slate-900/80 border border-slate-800 relative overflow-hidden shadow-xl group hover:border-cyan-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">📜 Legalitas BAST</span>
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-cyan-500/15 text-cyan-300 border border-cyan-500/20">
                    Dokumen
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-white group-hover:text-cyan-300 transition-colors">
                    {{ count($hibahRecords) }} <span class="text-xs font-semibold text-slate-400">Berkas</span>
                </div>
                <div class="text-xs font-medium text-slate-400 mt-0.5">
                    Serah Terima Terverifikasi
                </div>
            </div>
            <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between border-t border-slate-800/80 pt-2">
                <span>Bukti Fisik &amp; Administrasi</span>
                <span class="text-cyan-400/80 font-bold">Valid SIPD</span>
            </div>
        </div>

        <!-- Card 4: Netto Saldo Hibah -->
        @php
            $nettoUnit = $totalMasukUnit - $totalKeluarUnit;
            $nettoNominal = $totalMasukNominal - $totalKeluarNominal;
        @endphp
        <div class="p-4 rounded-3xl bg-slate-900/80 border border-slate-800 relative overflow-hidden shadow-xl group hover:border-emerald-500/40 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">⚖️ Mutasi Bersih (Netto)</span>
                <span class="px-2 py-0.5 rounded-lg text-[10px] font-black bg-emerald-500/15 text-emerald-300 border border-emerald-500/20">
                    Posisi Saldo
                </span>
            </div>
            <div class="mt-2.5">
                <div class="text-2xl font-black text-emerald-400">
                    {{ $nettoUnit >= 0 ? '+' : '' }}{{ number_format($nettoUnit, 0, ',', '.') }} <span class="text-xs font-semibold text-slate-400">Unit</span>
                </div>
                <div class="text-xs font-extrabold text-emerald-300 font-mono mt-0.5">
                    {{ $nettoNominal >= 0 ? '+' : '' }}Rp {{ number_format($nettoNominal, 0, ',', '.') }}
                </div>
            </div>
            <div class="text-[10px] text-slate-500 mt-2 flex items-center justify-between border-t border-slate-800/80 pt-2">
                <span>Dampak Neraca Aset</span>
                <span class="text-emerald-400/80 font-bold">Aktif di RSUD</span>
            </div>
        </div>
    </div>
</div>
