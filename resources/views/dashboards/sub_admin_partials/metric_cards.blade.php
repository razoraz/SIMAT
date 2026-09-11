<!-- 2. RINGKASAN METRIK KHUSUS RUANGAN (4 STATS CARDS) -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
    <!-- 1. Total Aset Ruangan -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-emerald-500/40 transition-all">
        <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-xs font-bold uppercase tracking-wider">Aset di Ruangan Ini</span>
            <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01" />
                </svg>
            </div>
        </div>
        <div class="flex items-baseline space-x-2">
            <p class="text-2xl sm:text-3xl font-black text-white">{{ $unitTotalAset }}</p>
            <span class="text-xs font-bold text-emerald-400">Unit Barang</span>
        </div>
        <p class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
            <span>Tercatat Resmi Dokumen KIR</span>
        </p>
    </div>

    <!-- 2. Valuasi Nilai Aset Ruangan -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-cyan-500/40 transition-all">
        <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-xs font-bold uppercase tracking-wider">Nilai Aset Ruangan</span>
            <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="flex items-baseline space-x-1">
            <p class="text-xl sm:text-2xl font-black text-white">{{ $unitTotalNilai }}</p>
        </div>
        <p class="text-[11px] text-slate-400 mt-2">
            Estimasi Nilai Buku Inventaris Unit
        </p>
    </div>

    <!-- 3. Kondisi Aset Siap Pakai (Baik) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-teal-500/40 transition-all">
        <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-xs font-bold uppercase tracking-wider">Kondisi Siap Pakai</span>
            <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
        </div>
        <div class="flex items-baseline space-x-2">
            <p class="text-2xl sm:text-3xl font-black text-white">{{ $kondisiBaik ?? 0 }}</p>
            <span class="text-xs font-bold text-emerald-400">Unit Baik</span>
        </div>
        <p class="text-[11px] text-slate-400 mt-2">
            {{ $unitTotalAset > 0 ? round((($kondisiBaik ?? 0) / $unitTotalAset) * 100, 1) : 0 }}% dari total aset ruangan
        </p>
    </div>

    <!-- 4. Aset Perlu Perhatian / Servis -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-amber-500/40 transition-all">
        <div class="flex items-center justify-between text-slate-400 mb-2">
            <span class="text-xs font-bold uppercase tracking-wider">Perlu Perhatian</span>
            <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        <div class="flex items-baseline space-x-2">
            <p class="text-2xl sm:text-3xl font-black text-white">{{ $totalRusak ?? 0 }}</p>
            <span class="text-xs font-bold text-amber-400">Unit Servis/Rusak</span>
        </div>
        <p class="text-[11px] text-slate-400 mt-2">
            {{ $kondisiRusakRingan ?? 0 }} Rusak Ringan · {{ $kondisiRusakBerat ?? 0 }} Rusak Berat
        </p>
    </div>
</div>
