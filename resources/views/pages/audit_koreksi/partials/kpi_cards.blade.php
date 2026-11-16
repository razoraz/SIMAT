{{-- KPI CARDS RINGKASAN AUDIT KOREKSI NILAI BMD --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
    {{-- Card 1: Total Semua Koreksi --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-slate-900/95 to-slate-950 border border-slate-700/70 p-5 backdrop-blur-md shadow-xl flex flex-col justify-between group hover:border-slate-600 transition-all">
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] uppercase font-bold tracking-wider text-slate-300 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                    Total Audit Koreksi
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-800 text-slate-200 border border-slate-700">
                    {{ $stats['total_count'] }} Transaksi
                </span>
            </div>

            <div class="text-xl sm:text-2xl font-black font-mono tracking-tight mt-1.5">
                @if ($stats['net_koreksi'] < 0)
                    <span class="text-rose-400">-Rp {{ number_format(abs($stats['net_koreksi']), 0, ',', '.') }}</span>
                @elseif ($stats['net_koreksi'] > 0)
                    <span class="text-emerald-400">+Rp {{ number_format($stats['net_koreksi'], 0, ',', '.') }}</span>
                @else
                    <span class="text-white">Rp 0</span>
                @endif
            </div>

            <div class="flex items-center gap-1.5 text-[11px] text-slate-400 font-medium">
                <span>Net Dampak Penyesuaian Neraca</span>
                @if ($stats['net_koreksi'] < 0)
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">Pengurangan</span>
                @elseif ($stats['net_koreksi'] > 0)
                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Penambahan</span>
                @endif
            </div>
        </div>

        <div class="mt-4 pt-3 border-t border-slate-800/90 flex items-center justify-between text-[11px] font-mono">
            <div class="text-emerald-400 font-semibold" title="Total Nilai Penambahan Kapitalisasi">
                <span class="text-emerald-500/80 font-sans text-[9.5px] uppercase font-bold tracking-wider block">TAMBAH (+)</span>
                <div>Rp {{ number_format($stats['total_tambah'], 0, ',', '.') }}</div>
            </div>
            <div class="text-rose-400 font-semibold text-right" title="Total Nilai Pengurangan Aset">
                <span class="text-rose-500/80 font-sans text-[9.5px] uppercase font-bold tracking-wider block">KURANG (-)</span>
                <div>Rp {{ number_format($stats['total_kurang'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Card 2: Koreksi Biasa (Internal RSUD) --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-indigo-950/40 via-slate-900/95 to-slate-950 border border-indigo-500/40 p-5 backdrop-blur-md shadow-xl shadow-indigo-950/20 flex flex-col justify-between group hover:border-indigo-400/60 transition-all">
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] uppercase font-bold tracking-wider text-indigo-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    Koreksi Biasa (Internal)
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-indigo-950/80 text-indigo-300 border border-indigo-800/60">
                    {{ $stats['biasa']['count'] }} Transaksi
                </span>
            </div>

            <div class="text-xl sm:text-2xl font-black font-mono tracking-tight mt-1.5">
                @if ($stats['biasa']['net'] < 0)
                    <span class="text-rose-400">-Rp {{ number_format(abs($stats['biasa']['net']), 0, ',', '.') }}</span>
                @elseif ($stats['biasa']['net'] > 0)
                    <span class="text-emerald-400">+Rp {{ number_format($stats['biasa']['net'], 0, ',', '.') }}</span>
                @else
                    <span class="text-indigo-100">Rp 0</span>
                @endif
            </div>

            <p class="text-[11px] text-indigo-300/80 font-medium">Rekonsiliasi Kas &amp; Pembukuan RSUD</p>
        </div>

        <div class="mt-4 pt-3 border-t border-indigo-900/40 flex items-center justify-between text-[11px] font-mono">
            <div class="text-emerald-400 font-semibold" title="Kolom 5: Koreksi Rekening Bertambah">
                <span class="text-indigo-400/90 font-sans text-[9.5px] uppercase font-bold tracking-wider block">KOLOM 5 (+)</span>
                <div>Rp {{ number_format($stats['biasa']['tambah'], 0, ',', '.') }}</div>
            </div>
            <div class="text-rose-400 font-semibold text-right" title="Kolom 15: Koreksi Berkurang">
                <span class="text-indigo-400/90 font-sans text-[9.5px] uppercase font-bold tracking-wider block">KOLOM 15 (-)</span>
                <div>Rp {{ number_format($stats['biasa']['kurang'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Card 3: Koreksi LKD (BPK RI) --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-cyan-950/40 via-slate-900/95 to-slate-950 border border-cyan-500/40 p-5 backdrop-blur-md shadow-xl shadow-cyan-950/20 flex flex-col justify-between group hover:border-cyan-400/60 transition-all">
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] uppercase font-bold tracking-wider text-cyan-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    Koreksi LKD (BPK RI)
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyan-950/80 text-cyan-300 border border-cyan-800/60">
                    {{ $stats['lkd']['count'] }} Transaksi
                </span>
            </div>

            <div class="text-xl sm:text-2xl font-black font-mono tracking-tight mt-1.5">
                @if ($stats['lkd']['net'] < 0)
                    <span class="text-rose-400">-Rp {{ number_format(abs($stats['lkd']['net']), 0, ',', '.') }}</span>
                @elseif ($stats['lkd']['net'] > 0)
                    <span class="text-emerald-400">+Rp {{ number_format($stats['lkd']['net'], 0, ',', '.') }}</span>
                @else
                    <span class="text-cyan-100">Rp 0</span>
                @endif
            </div>

            <p class="text-[11px] text-cyan-300/80 font-medium">Temuan Audit LHP &amp; Rekon Neraca</p>
        </div>

        <div class="mt-4 pt-3 border-t border-cyan-900/40 flex items-center justify-between text-[11px] font-mono">
            <div class="text-emerald-400 font-semibold" title="Kolom 6: Koreksi LKD Bertambah">
                <span class="text-cyan-400/90 font-sans text-[9.5px] uppercase font-bold tracking-wider block">KOLOM 6 (+)</span>
                <div>Rp {{ number_format($stats['lkd']['tambah'], 0, ',', '.') }}</div>
            </div>
            <div class="text-rose-400 font-semibold text-right" title="Kolom 16: Koreksi LKD Berkurang">
                <span class="text-cyan-400/90 font-sans text-[9.5px] uppercase font-bold tracking-wider block">KOLOM 16 (-)</span>
                <div>Rp {{ number_format($stats['lkd']['kurang'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Card 4: Koreksi Manset (E-Manset BPKAD) --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-b from-emerald-950/40 via-slate-900/95 to-slate-950 border border-emerald-500/40 p-5 backdrop-blur-md shadow-xl shadow-emerald-950/20 flex flex-col justify-between group hover:border-emerald-400/60 transition-all">
        <div class="space-y-1">
            <div class="flex items-center justify-between">
                <span class="text-[10px] uppercase font-bold tracking-wider text-emerald-400 flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    Koreksi Manset (BPKAD)
                </span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-950/80 text-emerald-300 border border-emerald-800/60">
                    {{ $stats['manset']['count'] }} Transaksi
                </span>
            </div>

            <div class="text-xl sm:text-2xl font-black font-mono tracking-tight mt-1.5">
                @if ($stats['manset']['net'] < 0)
                    <span class="text-rose-400">-Rp {{ number_format(abs($stats['manset']['net']), 0, ',', '.') }}</span>
                @elseif ($stats['manset']['net'] > 0)
                    <span class="text-emerald-400">+Rp {{ number_format($stats['manset']['net'], 0, ',', '.') }}</span>
                @else
                    <span class="text-emerald-100">Rp 0</span>
                @endif
            </div>

            <p class="text-[11px] text-emerald-300/80 font-medium">Harmonisasi Aplikasi SIMDA / E-Manset</p>
        </div>

        <div class="mt-4 pt-3 border-t border-emerald-900/40 flex items-center justify-between text-[11px] font-mono">
            <div class="text-emerald-400 font-semibold" title="Kolom 7: Koreksi Manset Bertambah">
                <span class="text-emerald-400/90 font-sans text-[9.5px] uppercase font-bold tracking-wider block">KOLOM 7 (+)</span>
                <div>Rp {{ number_format($stats['manset']['tambah'], 0, ',', '.') }}</div>
            </div>
            <div class="text-rose-400 font-semibold text-right" title="Kolom 17: Koreksi Manset Berkurang">
                <span class="text-emerald-400/90 font-sans text-[9.5px] uppercase font-bold tracking-wider block">KOLOM 17 (-)</span>
                <div>Rp {{ number_format($stats['manset']['kurang'], 0, ',', '.') }}</div>
            </div>
        </div>
    </div>
</div>