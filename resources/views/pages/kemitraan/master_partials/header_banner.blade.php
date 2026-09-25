<!-- ========================================================================= -->
<!-- HEADER BANNER & 4 STATISTIK KPI MASTER KEMITRAAN (AKUN 1.5.2)             -->
<!-- ========================================================================= -->
<div class="space-y-4">
    <!-- Top Header Banner -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl relative overflow-hidden">
        <div class="flex items-center space-x-3.5 sm:space-x-4">
            <div class="w-12 h-12 rounded-2xl bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-2xl shrink-0 shadow-lg shadow-cyan-500/10">
                🤝
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight">
                        Kelola Aset Kemitraan Pihak Ketiga
                    </h1>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                        Akun 1.5.2 · PMDN 108
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                    Pusat monitoring aset kerja sama operasional (KSO), sewa, dan Bangun Guna Serah (BGS) dengan rekanan swasta sebelum direklasifikasi definitif ke Aset Tetap.
                </p>
            </div>
        </div>

        <!-- Tombol Aksi Cepat -->
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('master.reklasifikasi') }}"
                class="px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-cyan-500/40 text-xs font-bold text-cyan-400 hover:text-cyan-300 transition-all flex items-center gap-1.5 shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                <span>Matriks Reklasifikasi</span>
            </a>

            <a href="{{ route('astap.create_kemitraan') }}"
                class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-xs font-extrabold shadow-lg shadow-cyan-500/20 transition-all flex items-center gap-1.5 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Catat Aset Kemitraan</span>
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
            <p class="text-[10px] text-slate-500 mt-1">Masa Konsesi Masih Berlaku</p>
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
