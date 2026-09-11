<!-- ========================================================================= -->
<!-- NOT FOUND STATE                                                           -->
<!-- ========================================================================= -->
<div class="bg-slate-900/90 border border-rose-500/30 rounded-3xl p-8 sm:p-10 shadow-2xl text-center space-y-4 max-w-md mx-auto my-12">
    <div class="w-16 h-16 rounded-3xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-3xl mx-auto border border-rose-500/30 shadow-lg shadow-rose-500/10">
        🔍
    </div>
    <div class="space-y-1.5">
        <h2 class="text-xl font-black text-white">Data Aset Tidak Ditemukan</h2>
        <p class="text-xs text-slate-400 leading-relaxed">
            Barcode / Nomor Register <span class="font-mono text-amber-400 font-bold">{{ $nibar }}</span> tidak terdaftar dalam database inventaris resmi SIMAT-RK RSUD Dr. H. Koesnandi.
        </p>
    </div>
    <div class="pt-2">
        <a href="/" class="inline-flex items-center space-x-1.5 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold transition-all border border-slate-700">
            <span>Kembali ke Beranda</span>
        </a>
    </div>
</div>
