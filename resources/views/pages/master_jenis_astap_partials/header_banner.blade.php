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
<div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
        <div>
            <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-extrabold uppercase tracking-wider mb-3 shadow-sm backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shadow-sm shadow-emerald-400"></span>
                <span>KODE 108 PERMENDAGRI (BARANG MILIK DAERAH)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Master Jenis ASTAP (Kode 108 BMD)</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Struktur 3 tingkatan klasifikasi aset tetap resmi pemerintah daerah (Jenis Utama, Sub Rincian Objek, dan Sub-Sub Rincian Objek).
            </p>
        </div>
        
        <div class="flex items-center space-x-2 shrink-0">
            <button type="button" @click="showImportModal = true"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-emerald-400 font-bold text-xs border border-emerald-500/30 shadow-lg transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import CSV/Excel</span>
            </button>
            <button type="button" @click="showAddModal = true"
                class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                <span>Tambah Jenis ASTAP</span>
            </button>
        </div>
    </div>

    <!-- Mini Summary KPI Cards Strip -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6 pt-6 border-t border-slate-800/80">
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-4 flex items-center space-x-3">
            <div class="p-3 rounded-xl bg-emerald-500/10 text-emerald-400 text-xl">🏛️</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Objek Aset</span>
                <span class="text-base sm:text-lg font-extrabold text-emerald-400">{{ number_format($totalCount ?? 0) }} Data Kode 108</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-4 flex items-center space-x-3">
            <div class="p-3 rounded-xl bg-cyan-500/10 text-cyan-400 text-xl">📋</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Klasifikasi Aset</span>
                <span class="text-base sm:text-lg font-extrabold text-cyan-300">8 Kategori Aset Tetap</span>
            </div>
        </div>
    </div>
</div>
