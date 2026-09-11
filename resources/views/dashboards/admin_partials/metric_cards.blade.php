<!-- Metric Summary Stats Cards (Posisi 1) -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
        <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-xs font-bold uppercase tracking-wider">Total Aset (ASTAP)</span>
            <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-black text-white">{{ number_format($totalAsetVolumeCount ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-emerald-400">Unit</span></p>
        <p class="text-[11px] text-slate-400 mt-1">Valuasi: <span class="text-emerald-400 font-bold">{{ $hargaAsetFormatted ?? 'Rp 0' }} {{ $hargaAsetUnit ?? '' }}</span> ({{ number_format($totalAstapMasterCount ?? 0, 0, ',', '.') }} Master)</p>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
        <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-xs font-bold uppercase tracking-wider">Total Unit RSUD</span>
            <div class="p-2 rounded-xl bg-cyan-500/10 text-cyan-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 4h4" />
                </svg>
            </div>
        </div>
        <p class="text-2xl font-black text-white">{{ number_format($totalUnitRsudCount ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-cyan-400">Unit</span></p>
        <p class="text-[11px] text-slate-400 mt-1">Master Unit &amp; Ruang Kerja RSUD</p>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
        <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-xs font-bold uppercase tracking-wider">Distribusi Barang</span>
            <div class="p-2 rounded-xl bg-teal-500/10 text-teal-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-black text-white">{{ number_format($totalTerdistribusiUnit ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-teal-400">Terdistribusi</span></p>
        <p class="text-[11px] text-slate-400 mt-1">Ke {{ $totalUnitRsudCount ?? 0 }} Unit RSUD ({{ $totalTransaksiDistribusi ?? 0 }} Transaksi)</p>
    </div>

    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
        <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-xs font-bold uppercase tracking-wider">Kondisi Aset</span>
            <div class="p-2 rounded-xl bg-amber-500/10 text-amber-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
        </div>
        <div class="flex items-baseline justify-between">
            <p class="text-2xl font-black text-white">{{ number_format($kondisiBaik ?? 0, 0, ',', '.') }} <span class="text-xs font-bold text-emerald-400">Baik</span></p>
            <span class="text-xs font-extrabold text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-md border border-rose-500/20">{{ number_format($totalRusak ?? 0, 0, ',', '.') }} Rusak</span>
        </div>
        <p class="text-[11px] text-slate-400 mt-1">{{ number_format($kondisiRusakRingan ?? 0, 0, ',', '.') }} Rusak Ringan · {{ number_format($kondisiRusakBerat ?? 0, 0, ',', '.') }} Rusak Berat</p>
    </div>
</div>
