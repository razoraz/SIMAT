<!-- 1. HEADER & IDENTITAS UNIT TERDAFTAR -->
<div class="bg-gradient-to-r from-emerald-600/15 via-teal-950/40 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-8 relative overflow-hidden">
    <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-0 right-1/4 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
        <!-- Info Sub Admin & Unit -->
        <div class="space-y-3 max-w-3xl">
            <div class="flex flex-wrap items-center gap-2">
                <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold tracking-wide">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>SUB ADMIN · PENANGGUNG JAWAB RUANGAN</span>
                </span>
                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-700 text-xs font-mono font-semibold">
                    <span>{{ $unitKode }}</span>
                </span>
            </div>

            <div>
                <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight flex flex-wrap items-center gap-3">
                    <span>🏥 {{ $unitNama }}</span>
                </h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                    Penanggung Jawab: <span class="font-bold text-white">{{ $unitKepala }}</span> 
                    <span class="text-slate-400">(NIP. {{ $unitNip }})</span> · 
                    <span class="text-emerald-400 font-semibold">{{ $unitTipe }}</span>
                </p>
            </div>

            <p class="text-xs text-slate-400 leading-relaxed">
                Pantau inventaris fisik ruangan Anda, visualisasi grafik nilai dan kondisi aset, telusuri katalog barang ASTAP, serta cetak Lembar Kartu Inventaris Ruangan (KIR) resmi.
            </p>
        </div>

        <!-- Action Buttons Khusus Sub Admin -->
        <div class="flex flex-wrap sm:flex-nowrap gap-2.5 shrink-0 w-full sm:w-auto">
            <a href="{{ route('astap.index') }}" 
                class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition-all flex items-center justify-center space-x-2">
                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
                <span>Katalog ASTAP</span>
            </a>

            <a href="{{ route('kir.index') }}" 
                class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition-all flex items-center justify-center space-x-2 shadow-lg shadow-emerald-500/20">
                <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Lembar KIR Ruangan</span>
            </a>
        </div>
    </div>
</div>
