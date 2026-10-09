<!-- ========================================================================= -->
<!-- HEADER BANNER & 4 STATISTIK KPI MASTER KEMITRAAN (AKUN 1.5.2)             -->
<!-- ========================================================================= -->
<div class="space-y-4">
    <!-- Top Header Banner -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl relative overflow-hidden backdrop-blur-md">
        <!-- Subtle Ambient Background Glow -->
        <div class="absolute -top-20 -left-20 w-52 h-52 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-20 -right-20 w-52 h-52 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Left: Icon & Text Information -->
        <div class="flex items-start gap-4 relative z-10">
            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-cyan-500/20 via-slate-800 to-cyan-500/5 border border-cyan-500/30 flex items-center justify-center text-2xl shrink-0 shadow-lg shadow-cyan-500/10 ring-1 ring-cyan-500/20 mt-0.5">
                🤝
            </div>
            <div class="space-y-1">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight">
                        Kelola Aset Kemitraan Pihak Ketiga
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-mono shadow-sm">
                        Akun 1.5.2 · PMDN 108
                    </span>
                </div>
                <p class="text-xs text-slate-400 max-w-2xl leading-relaxed">
                    Pusat monitoring aset kerja sama sewa, pemanfaatan (KSP), bangun guna serah (BGS/BSG), dan penyediaan infrastruktur (KSPI) dengan rekanan swasta sebelum direklasifikasi definitif ke Aset Tetap.
                </p>
            </div>
        </div>

        <!-- Right: Action Buttons Group -->
        <div class="flex flex-wrap items-center gap-2.5 relative z-10 shrink-0 lg:self-center">
            <!-- Ekspor Excel Button -->
            <button type="button" @click="openExportModal()"
                class="group px-4 py-2.5 rounded-xl bg-slate-950/90 hover:bg-emerald-950/30 border border-slate-700/80 hover:border-emerald-500/60 text-xs font-bold text-slate-300 hover:text-emerald-300 transition-all duration-200 transform hover:-translate-y-0.5 hover:shadow-lg hover:shadow-emerald-500/15 flex items-center gap-2 cursor-pointer active:scale-95">
                <svg class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition-transform duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Ekspor Excel</span>
            </button>
            <!-- Tombol Catat Aset Ditambahkan Mitra -->
            <a href="{{ route('astap.create_kemitraan', ['tipe' => 'ditambahkan']) }}"
                class="group px-4 py-2.5 rounded-xl bg-emerald-400 hover:bg-emerald-300 text-slate-950 text-xs font-black shadow-lg shadow-emerald-500/20 hover:shadow-xl hover:shadow-emerald-400/35 transition-all duration-200 transform hover:-translate-y-0.5 hover:scale-[1.02] flex items-center gap-2 active:scale-95 cursor-pointer"
                title="Catat Aset / Peralatan Baru yang Ditambahkan Rekanan Mitra ke RSUD (KSO / BGS)">
                <span class="text-base inline-block group-hover:scale-115 group-hover:rotate-6 transition-transform duration-200">📦</span>
                <span>Catat Aset Ditambahkan Mitra</span>
            </a>
        </div>
    </div>

    <!-- 4 Kartu Statistik KPI Ringkas -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- KPI 1: Total Nilai Aset -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-cyan-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL NILAI ASET KEMITRAAN</span>
                <span class="p-1.5 rounded-lg bg-cyan-500/10 text-cyan-400 text-xs">💰</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-xs font-bold text-slate-500 font-mono">Rp</span>
                <span class="text-lg sm:text-xl font-black text-white font-mono truncate" x-text="formatRupiah({{ $totalNilaiKemitraan ?? 0 }})"></span>
            </div>
            <p class="text-[10px] text-cyan-400/80 mt-1">Akun 1.5.2 Kemitraan Pihak Ketiga</p>
        </div>

        <!-- KPI 2: Total Fisik Unit -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL UNIT BARANG</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs">📦</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-emerald-400 font-mono" x-text="{{ $totalVolumeUnit ?? 0 }}"></span>
                <span class="text-xs font-semibold text-slate-400">Unit Terdaftar</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Tercatat di Inventaris Ruangan (KIR)</p>
        </div>

        <!-- KPI 3: Kemitraan Aktif -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-blue-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">KEMITRAAN AKTIF</span>
                <span class="p-1.5 rounded-lg bg-blue-500/10 text-blue-400 text-xs">🟢</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-blue-400 font-mono" x-text="{{ $totalAktif ?? 0 }}"></span>
                <span class="text-xs font-semibold text-slate-400">PKS Berjalan</span>
            </div>
            @if(($totalBerakhir ?? 0) > 0)
                <div class="mt-1 flex items-center justify-between text-[10px]">
                    <span class="text-slate-500">Masa Konsesi Berlaku</span>
                    <span class="font-bold text-rose-400 bg-rose-500/10 px-1.5 py-0.5 rounded border border-rose-500/20" title="{{ $totalBerakhir }} PKS telah melewati tanggal masa konsesi">
                        🛑 {{ $totalBerakhir }} Berakhir
                    </span>
                </div>
            @else
                <p class="text-[10px] text-slate-500 mt-1">Masa Konsesi Masih Berlaku</p>
            @endif
        </div>

        <!-- KPI 4: Mitra Rekanan Bekerjasama -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-purple-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">MITRA REKANAN</span>
                <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400 text-xs">🏢</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-purple-400 font-mono" x-text="{{ $totalMitraUnik ?? 0 }}"></span>
                <span class="text-xs font-semibold text-slate-400">Perusahaan Swasta</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Vendor Alkes &amp; Pengembang</p>
        </div>
    </div>
</div>
