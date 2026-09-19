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
<div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>HIERARKI PENGADAAN SIPD (SISTEM INFORMASI PEMERINTAH DAERAH)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Jenis & Hierarki Pengadaan SIPD</h1>
            <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                Struktur 3 tingkatan penganggaran pengadaan barang & jasa (Program Pengadaan, Kegiatan Pengadaan, dan Sub Kegiatan Pengadaan) sesuai standar DPA SIPD.
            </p>
        </div>
        
        <button type="button" @click="openAdd()"
            class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            <span>Tambah Pengadaan SIPD</span>
        </button>
    </div>

    <!-- Mini Summary KPI Cards Strip -->
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
            <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📑</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Program</span>
                <span class="text-sm sm:text-base font-extrabold text-emerald-400">{{ count($uniquePrograms) }} Program</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
            <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">📁</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Kegiatan</span>
                <span class="text-sm sm:text-base font-extrabold text-amber-300">{{ count($uniqueKegiatan) }} Kegiatan</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
            <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">📄</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Sub Kegiatan</span>
                <span class="text-sm sm:text-base font-extrabold text-purple-300">{{ $totalCount }} Sub Kegiatan</span>
            </div>
        </div>

        <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
            <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">🔗</div>
            <div>
                <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Sinkronisasi</span>
                <span class="text-sm sm:text-base font-extrabold text-cyan-300">DPA SIPD RSUD</span>
            </div>
        </div>
    </div>
</div>
