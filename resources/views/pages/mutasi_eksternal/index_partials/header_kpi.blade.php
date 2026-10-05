<!-- ========================================================================= -->
<!-- HEADER BANNER & 4 STATISTIK KPI KATALOG PELIMPAHAN ASET (MUTASI EKSTERNAL) -->
<!-- ========================================================================= -->
<div class="space-y-4">
    <!-- Top Header Banner -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl relative overflow-hidden">
        <!-- Ambient glow decoration -->
        <div class="absolute -top-12 -left-12 w-48 h-48 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center space-x-3.5 sm:space-x-4 relative z-10">
            <div class="w-12 h-12 rounded-2xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-2xl shrink-0 shadow-lg shadow-indigo-500/10">
                🏛️
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight">
                        Katalog Pelimpahan Aset (Mutasi Eksternal)
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                        Pelimpahan Antar-SKPD · BMD
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                    Pencatatan resmi Berita Acara Serah Terima (BAST / BAMB) atas pelimpahan Barang Milik Daerah (BMD) dari SKPD / Dinas di lingkungan Pemerintah Kabupaten Bondowoso ke RSUD dr. H. Koesnadi.
                </p>
            </div>
        </div>

        <!-- Tombol Aksi Cepat (Bespoke Action Group) -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0 relative z-10">

            <!-- Primary Action: Input Pelimpahan Baru -->
            <a href="{{ route('astap.create_mutasi_eksternal', ['from' => 'eksternal']) }}"
                class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 active:scale-95 cursor-pointer shrink-0 border border-indigo-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>Pelimpahan Aset Baru</span>
            </a>
        </div>
    </div>

    <!-- 4 Kartu Statistik KPI Ringkas Mutasi Eksternal -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- KPI 1: Total BAST Pelimpahan -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-indigo-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL BAST PELIMPAHAN</span>
                <span class="p-1.5 rounded-lg bg-indigo-500/10 text-indigo-400 text-xs">🏛️</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-white font-mono" x-text="countAll"></span>
                <span class="text-xs font-semibold text-slate-400">Dokumen Sah</span>
            </div>
            <p class="text-[10px] text-indigo-400/80 mt-1">Registrasi SKPD ke RSUD</p>
        </div>

        <!-- KPI 2: Total Nilai Perolehan BMD -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL NILAI PEROLEHAN BMD</span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs">💰</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-lg sm:text-xl font-black text-emerald-400 font-mono truncate" x-text="formatRupiah(totalNominal)"></span>
            </div>
            <p class="text-[10px] text-emerald-400/80 mt-1">Akumulasi Nilai Aset Masuk</p>
        </div>

        <!-- KPI 3: Total Fisik Unit Aset -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-cyan-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">TOTAL UNIT FISIK BARANG</span>
                <span class="p-1.5 rounded-lg bg-cyan-500/10 text-cyan-400 text-xs">📦</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-cyan-400 font-mono" x-text="totalUnits"></span>
                <span class="text-xs font-semibold text-slate-400">Unit / Fisik Barang</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Tercatat di Inventaris Ruangan</p>
        </div>

        <!-- KPI 4: SKPD / Dinas Pengirim -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-purple-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">SKPD / DINAS PENGIRIM</span>
                <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400 text-xs">🏢</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-purple-400 font-mono" x-text="countSkpd"></span>
                <span class="text-xs font-semibold text-slate-400">Instansi OPD</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1">Pemkab Bondowoso &amp; Dinas</p>
        </div>
    </div>
</div>
