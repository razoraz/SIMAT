<!-- Header Banner & Mini KPI Strip Mutasi Eksternal -->
<div class="bg-gradient-to-r from-indigo-900/30 via-slate-900 to-slate-900 border border-indigo-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-bold mb-3">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                <span>PELIMPAHAN ASET SKPD & MUTASI EKSTERNAL</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Pelimpahan Aset (Mutasi Eksternal)</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Pencatatan resmi Berita Acara Serah Terima (BAST / BAMB) atas pelimpahan Barang Milik Daerah (BMD) dari SKPD / Dinas di lingkungan Pemerintah Kabupaten Bondowoso ke RSUD dr. H. Koesnadi.
            </p>
        </div>
        
        <a href="{{ route('astap.create_mutasi_eksternal', ['from' => 'eksternal']) }}"
            class="px-5 py-3 rounded-2xl bg-gradient-to-r from-indigo-500 to-purple-600 hover:from-indigo-400 hover:to-purple-500 text-white font-extrabold text-xs shadow-lg shadow-indigo-500/25 transition-all flex items-center space-x-2 shrink-0 active:scale-95 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Pelimpahan Aset Baru</span>
        </a>
    </div>

    <!-- Mini Summary KPI Cards Strip (4 Kartu Elegan & Fokus Pelimpahan OPD) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-800/80">
        <!-- 1. Total BAST Pelimpahan -->
        <div class="bg-slate-950/70 border border-slate-800/90 rounded-2xl p-4 flex items-center space-x-3.5 shadow-sm">
            <div class="p-3 rounded-xl bg-indigo-500/15 text-indigo-400 text-xl border border-indigo-500/20">🏛️</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">Total BAST Pelimpahan</span>
                <span class="text-base sm:text-lg font-black text-white" x-text="countAll + ' Dokumen'"></span>
            </div>
        </div>

        <!-- 2. Total Nilai Perolehan BMD -->
        <div class="bg-slate-950/70 border border-slate-800/90 rounded-2xl p-4 flex items-center space-x-3.5 shadow-sm">
            <div class="p-3 rounded-xl bg-emerald-500/15 text-emerald-400 text-xl border border-emerald-500/20">💰</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">Total Nilai Perolehan</span>
                <span class="text-base sm:text-lg font-black text-emerald-300 font-mono" x-text="formatRupiah(totalNominal)"></span>
            </div>
        </div>

        <!-- 3. Total Fisik Unit Aset -->
        <div class="bg-slate-950/70 border border-slate-800/90 rounded-2xl p-4 flex items-center space-x-3.5 shadow-sm">
            <div class="p-3 rounded-xl bg-cyan-500/15 text-cyan-400 text-xl border border-cyan-500/20">📦</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">Total Unit Fisik Aset</span>
                <span class="text-base sm:text-lg font-black text-cyan-300" x-text="totalUnits + ' Unit'"></span>
            </div>
        </div>

        <!-- 4. Jumlah SKPD Pengirim -->
        <div class="bg-slate-950/70 border border-slate-800/90 rounded-2xl p-4 flex items-center space-x-3.5 shadow-sm">
            <div class="p-3 rounded-xl bg-purple-500/15 text-purple-400 text-xl border border-purple-500/20">🏢</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block">SKPD / Dinas Pengirim</span>
                <span class="text-base sm:text-lg font-black text-purple-300" x-text="countSkpd + ' OPD'"></span>
            </div>
        </div>
    </div>
</div>
