<!-- CARD 1: KATALOG DATA ASTAP -->
<div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 border border-teal-500/30 rounded-3xl p-6 shadow-xl relative overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div class="flex items-center space-x-3">
            <div class="p-3 rounded-2xl bg-teal-500/10 border border-teal-500/20 text-teal-400 text-xl">
                📦
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-mono font-bold text-teal-400 uppercase tracking-wider">Katalog Inventaris Induk</span>
                </div>
                <h3 class="text-lg font-extrabold text-white">Katalog Data ASTAP</h3>
            </div>
        </div>

        <a href="{{ route('astap.index') }}" 
            class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center justify-center space-x-2 shrink-0">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <span>Buka Katalog ASTAP &rarr;</span>
        </a>
    </div>

    <p class="text-xs text-slate-300 leading-relaxed mb-4">
        Telusuri seluruh katalog master inventaris aset tetap RSUD Dr. H. Koesnandi. Cari spesifikasi barang, nomor inventaris 108, kode register NIBAR, riwayat pengadaan, dan status ketersediaan barang inventaris.
    </p>

    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
        <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3 flex items-center space-x-2.5">
            <span class="text-emerald-400 font-bold text-sm">🔍</span>
            <div>
                <span class="text-slate-400 text-[11px] block">Pencarian Cepat</span>
                <span class="text-white font-semibold text-xs">Nama & Kode Barang</span>
            </div>
        </div>
        <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3 flex items-center space-x-2.5">
            <span class="text-teal-400 font-bold text-sm">🏷️</span>
            <div>
                <span class="text-slate-400 text-[11px] block">Standar Kode 108</span>
                <span class="text-white font-semibold text-xs">Permendagri No. 108</span>
            </div>
        </div>
        <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3 flex items-center space-x-2.5">
            <span class="text-cyan-400 font-bold text-sm">📱</span>
            <div>
                <span class="text-slate-400 text-[11px] block">Label Barcode QR</span>
                <span class="text-white font-semibold text-xs">Scan Fisik Terintegrasi</span>
            </div>
        </div>
    </div>
</div>
