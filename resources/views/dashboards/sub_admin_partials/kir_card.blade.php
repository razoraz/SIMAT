<!-- CARD 2: LEMBAR KIR RUANGAN (DITARUH TEPAT DI BAWAH KATALOG) -->
<div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 border border-emerald-500/30 rounded-3xl p-6 shadow-xl relative overflow-hidden">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
        <div class="flex items-center space-x-3">
            <div class="p-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xl">
                📋
            </div>
            <div>
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-mono font-bold text-emerald-400 uppercase tracking-wider">Kartu Inventaris Ruangan</span>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-full">Tersinkronisasi</span>
                </div>
                <h3 class="text-lg font-extrabold text-white">Lembar KIR Ruangan {{ $unitNama }}</h3>
            </div>
        </div>

        <a href="{{ route('kir.index') }}" 
            class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center space-x-2 shrink-0">
            <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            <span>Buka & Cetak Dokumen KIR</span>
        </a>
    </div>

    <p class="text-xs text-slate-300 leading-relaxed mb-4">
        Daftar lengkap inventaris fisik yang ditempatkan resmi di <strong class="text-white">{{ $unitNama }}</strong>. Anda dapat mencetak lembar resmi KIR ber-barcode standar rumah sakit untuk ditempel pada pintu ruangan atau dinding inventaris.
    </p>

    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-4 space-y-2.5 text-xs">
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 text-slate-300">
            <span class="text-slate-500">Unit Ruangan:</span>
            <span class="font-bold text-white">{{ $unitNama }} ({{ $unitKode }})</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 text-slate-300 border-t border-slate-800/80 pt-2">
            <span class="text-slate-500">Total Item Terpasang:</span>
            <span class="font-bold text-emerald-400">{{ $unitTotalAset }} Unit Barang (Valuasi: {{ $unitTotalNilai }})</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-1 text-slate-300 border-t border-slate-800/80 pt-2">
            <span class="text-slate-500">Penanggung Jawab Ruangan:</span>
            <span class="font-semibold text-slate-200">{{ $unitKepala }} <span class="text-slate-400 font-mono">(NIP. {{ $unitNip }})</span></span>
        </div>
    </div>
</div>
