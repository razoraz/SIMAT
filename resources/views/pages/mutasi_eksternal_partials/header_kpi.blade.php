<!-- Header Banner & Mini KPI Strip Mutasi Eksternal -->
<div class="bg-gradient-to-r from-indigo-900/30 via-slate-900 to-slate-900 border border-indigo-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xs font-bold mb-3">
                <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                <span>MUTASI ASET EKSTERNAL & ANTAR-OPD PEMKAB</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Mutasi Eksternal (Antar-OPD)</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Pencatatan serah terima, alih status, peminjaman sementara, dan penyerahan Barang Milik Daerah (BMD) antara RSUD dr. H. Koesnadi dengan Organisasi Perangkat Daerah (OPD) lain di lingkungan Pemkab Bondowoso.
            </p>
        </div>
        
        <div class="flex items-center space-x-3 shrink-0">
            <a href="{{ route('mutasi.index') }}"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs border border-slate-700 transition-all flex items-center space-x-2 active:scale-95">
                <span>🏢 Ke Mutasi Internal</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </a>
            <button type="button" @click="alert('Form Pengajuan Mutasi Eksternal Baru akan segera hadir pada tahap selanjutnya!')"
                class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white font-bold text-xs shadow-lg shadow-indigo-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Pengajuan BAST Baru</span>
            </button>
        </div>
    </div>

    <!-- Mini Summary KPI Cards Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 mt-6 pt-6 border-t border-slate-800/80">
        <!-- 1. Total Eksternal -->
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
            <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-400 text-lg">🏛️</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Antar-OPD</span>
                <span class="text-sm sm:text-base font-extrabold text-white" x-text="countAll + ' BAST'"></span>
            </div>
        </div>

        <!-- 2. Telah Disahkan -->
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
            <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">✅</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Telah Disahkan</span>
                <span class="text-sm sm:text-base font-extrabold text-emerald-300" x-text="countSelesai + ' BAST'"></span>
            </div>
        </div>

        <!-- 3. Peminjaman Aktif -->
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
            <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">⏱️</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Peminjaman Aktif</span>
                <span class="text-sm sm:text-base font-extrabold text-cyan-300" x-text="countPinjamAktif + ' Berjalan'"></span>
            </div>
        </div>

        <!-- 4. Menunggu Verifikasi -->
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
            <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">⏳</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Menunggu Verifikasi</span>
                <span class="text-sm sm:text-base font-extrabold text-amber-300" x-text="countMenunggu + ' Berkas'"></span>
            </div>
        </div>

        <!-- 5. Penyerahan BPKAD -->
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
            <div class="p-2.5 rounded-xl bg-violet-500/10 text-violet-400 text-lg">📦</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Serah ke BPKAD</span>
                <span class="text-sm sm:text-base font-extrabold text-violet-300" x-text="countBpkad + ' Aset'"></span>
            </div>
        </div>
    </div>
</div>
