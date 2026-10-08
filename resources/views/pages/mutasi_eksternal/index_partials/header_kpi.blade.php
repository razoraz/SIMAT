<!-- ========================================================================= -->
<!-- HEADER BANNER & 4 STATISTIK KPI KATALOG MUTASI EKSTERNAL (MASUK & KELUAR) -->
<!-- ========================================================================= -->
<div class="space-y-4">
    <!-- Top Header Banner -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl relative overflow-hidden">
        <!-- Ambient glow decoration -->
        <div class="absolute -top-12 -left-12 w-48 h-48 rounded-full blur-3xl pointer-events-none transition-all duration-500"
            :class="activeDirection === 'keluar' ? 'bg-cyan-500/10' : 'bg-indigo-500/10'"></div>

        <div class="flex items-center space-x-3.5 sm:space-x-4 relative z-10">
            <div class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 shadow-lg transition-all duration-300"
                :class="activeDirection === 'keluar' ? 'bg-cyan-500/15 border border-cyan-500/30 text-cyan-400 shadow-cyan-500/10' : 'bg-indigo-500/15 border border-indigo-500/30 text-indigo-400 shadow-indigo-500/10'">
                <template x-if="activeDirection === 'masuk'">
                    <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </template>
                <template x-if="activeDirection === 'keluar'">
                    <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                    </svg>
                </template>
            </div>
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight">
                        <span x-show="activeDirection === 'masuk'">Katalog Pelimpahan Aset (Mutasi Masuk)</span>
                        <span x-show="activeDirection === 'keluar'">Katalog Transfer Aset Keluar (Mutasi Keluar)</span>
                    </h1>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider border transition-all"
                        :class="activeDirection === 'keluar' ? 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30' : 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30'">
                        <span class="w-1.5 h-1.5 rounded-full animate-pulse" :class="activeDirection === 'keluar' ? 'bg-cyan-400' : 'bg-indigo-400'"></span>
                        <span x-text="activeDirection === 'keluar' ? 'Transfer Aset RSUD · Pemindahtanganan OPD' : 'Pelimpahan Antar-SKPD · BMD Masuk'"></span>
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-1 max-w-2xl leading-relaxed">
                    <span x-show="activeDirection === 'masuk'">
                        Pencatatan resmi Berita Acara Serah Terima (BAST / BAMB) atas pelimpahan Barang Milik Daerah (BMD) dari SKPD / Dinas di lingkungan Pemerintah Kabupaten Bondowoso ke RSUD dr. H. Koesnadi.
                    </span>
                    <span x-show="activeDirection === 'keluar'">
                        Pencatatan resmi Berita Acara Serah Terima (BAST) pemindahtanganan, hibah, atau transfer pinjam-pakai aset dari RSUD dr. H. Koesnadi ke SKPD / Instansi luar di lingkungan Pemkab Bondowoso.
                    </span>
                </p>
            </div>
        </div>

        <!-- Tombol Aksi Cepat (Bespoke Action Group) -->
        <div class="flex flex-wrap items-center gap-2.5 shrink-0 relative z-10">
            <!-- Action 1: Input Pelimpahan Masuk Baru -->
            <a href="{{ route('astap.create_mutasi_eksternal', ['from' => 'eksternal']) }}"
                class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 transition-all duration-200 flex items-center gap-2 active:scale-95 cursor-pointer shrink-0 border border-indigo-500/30">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                <span>+ Pelimpahan Masuk Baru</span>
            </a>
        </div>
    </div>

    <!-- 4 Kartu Statistik KPI Ringkas Mutasi Eksternal -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3.5">
        <!-- KPI 1: Total BAST -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group transition-all"
            :class="activeDirection === 'keluar' ? 'hover:border-cyan-500/50' : 'hover:border-indigo-500/50'">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    x-text="activeDirection === 'keluar' ? 'TOTAL BAST TRANSFER KELUAR' : 'TOTAL BAST PELIMPAHAN MASUK'"></span>
                <span class="p-1.5 rounded-lg text-xs"
                    :class="activeDirection === 'keluar' ? 'bg-cyan-500/10 text-cyan-400' : 'bg-indigo-500/10 text-indigo-400'"
                    x-text="activeDirection === 'keluar' ? '📤' : '🏛️'"></span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-white font-mono" x-text="countAll"></span>
                <span class="text-xs font-semibold text-slate-400">Dokumen Sah</span>
            </div>
            <p class="text-[10px] mt-1 font-medium"
                :class="activeDirection === 'keluar' ? 'text-cyan-400/80' : 'text-indigo-400/80'"
                x-text="activeDirection === 'keluar' ? 'Penyerahan RSUD ke OPD' : 'Registrasi SKPD ke RSUD'"></p>
        </div>

        <!-- KPI 2: Total Nilai Perolehan / Realisasi -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-emerald-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    x-text="activeDirection === 'keluar' ? 'TOTAL NILAI ASET KELUAR' : 'TOTAL NILAI PEROLEHAN BMD'"></span>
                <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-400 text-xs">💰</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1">
                <span class="text-lg sm:text-xl font-black text-emerald-400 font-mono truncate" x-text="formatRupiah(totalNominal)"></span>
            </div>
            <p class="text-[10px] text-emerald-400/80 mt-1"
                x-text="activeDirection === 'keluar' ? 'Akumulasi Nilai Aset Diserahkan' : 'Akumulasi Nilai Aset Masuk'"></p>
        </div>

        <!-- KPI 3: Total Fisik Unit Aset -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group transition-all"
            :class="activeDirection === 'keluar' ? 'hover:border-teal-500/50' : 'hover:border-cyan-500/50'">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    x-text="activeDirection === 'keluar' ? 'TOTAL FISIK ASET KELUAR' : 'TOTAL UNIT FISIK BARANG'"></span>
                <span class="p-1.5 rounded-lg text-xs"
                    :class="activeDirection === 'keluar' ? 'bg-teal-500/10 text-teal-400' : 'bg-cyan-500/10 text-cyan-400'">📦</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black font-mono"
                    :class="activeDirection === 'keluar' ? 'text-teal-400' : 'text-cyan-400'"
                    x-text="totalUnits"></span>
                <span class="text-xs font-semibold text-slate-400">Unit / Fisik Barang</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1"
                x-text="activeDirection === 'keluar' ? 'Diserahkan ke Instansi Penerima' : 'Tercatat di Inventaris Ruangan'"></p>
        </div>

        <!-- KPI 4: SKPD / OPD Terkait -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-lg relative overflow-hidden group hover:border-purple-500/50 transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider"
                    x-text="activeDirection === 'keluar' ? 'SKPD / OPD PENERIMA' : 'SKPD / DINAS PENGIRIM'"></span>
                <span class="p-1.5 rounded-lg bg-purple-500/10 text-purple-400 text-xs">🏢</span>
            </div>
            <div class="mt-2 flex items-baseline gap-1.5">
                <span class="text-lg sm:text-xl font-black text-purple-400 font-mono" x-text="countSkpd"></span>
                <span class="text-xs font-semibold text-slate-400">Instansi OPD</span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1"
                x-text="activeDirection === 'keluar' ? 'Instansi Pemkab / Luar Daerah' : 'Pemkab Bondowoso & Dinas'"></p>
        </div>
    </div>
</div>
