<!-- ========================================================================= -->
<!-- KARTU IDENTITAS RESMI ASET (VERIFIED ASSET HERO CARD)                      -->
<!-- ========================================================================= -->
<div class="relative overflow-hidden rounded-3xl bg-gradient-to-b from-slate-900 via-slate-900 to-slate-950 border border-slate-800 p-5 sm:p-7 shadow-2xl shadow-emerald-950/20">
    <!-- Glow background decor -->
    <div class="absolute -right-16 -top-16 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -left-16 -bottom-16 w-48 h-48 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 space-y-4">
        <!-- Header Badge & Kategori -->
        <div class="flex flex-wrap items-center justify-between gap-2.5">
            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 text-[11px] font-bold tracking-wide">
                <span>🛡️</span>
                <span>BARANG MILIK RSUD DR. H. KOESNANDI</span>
            </span>

            <span class="px-3 py-1 rounded-xl bg-slate-800/80 text-slate-300 border border-slate-700/70 text-[11px] font-bold">
                {{ $iconAset }} {{ $category }}
            </span>
        </div>

        <!-- Nama Barang Fisik -->
        <div>
            <span class="text-[10.5px] uppercase font-extrabold text-slate-400 tracking-wider block mb-1">
                Nama Barang / Inventaris Fisik:
            </span>
            <h2 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-snug">
                {{ $astap->nama_barang ?? 'Aset Inventaris RSUD' }}
            </h2>
            @if(!empty($judul) && strtolower($judul) !== strtolower($astap->nama_barang ?? ''))
            <p class="text-emerald-400 text-sm font-semibold mt-1">
                Judul / Seri: {{ $judul }}
            </p>
            @endif
        </div>

        <!-- NIBAR & Kode Register Unit -->
        <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800/90 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="space-y-0.5">
                <span class="text-[10px] uppercase font-bold text-slate-400 tracking-wider block">
                    Nomor Induk Barang (NIBAR) &amp; Register:
                </span>
                <div class="flex flex-wrap items-baseline gap-2">
                    <span id="nibarText" class="font-mono text-sm sm:text-base font-black text-emerald-400 tracking-wider select-all">
                        {{ $register->nibar ?? ($nibar ?? $astap->kode_barang) }}
                    </span>
                    @if($register && !empty($register->no_register_int))
                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-300 font-mono text-xs font-bold border border-emerald-500/20">
                        Unit #{{ str_pad($register->no_register_int, 4, '0', STR_PAD_LEFT) }}
                    </span>
                    @endif
                </div>
            </div>

            <!-- Tombol Salin NIBAR -->
            <button type="button" onclick="copyNibar()" id="copyBtn"
                    class="inline-flex items-center justify-center space-x-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:scale-95 text-slate-200 text-xs font-bold transition-all border border-slate-700 cursor-pointer shrink-0">
                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
                <span id="copyBtnLabel">Salin NIBAR</span>
            </button>
        </div>

        <!-- Status Kondisi & Ketersediaan -->
        <div class="grid grid-cols-2 gap-3 pt-1">
            <!-- Kondisi Fisik -->
            <div class="p-3 rounded-2xl border
                @if($kondisi === 'Baik') bg-emerald-500/10 border-emerald-500/30 text-emerald-300
                @elseif($kondisi === 'Kurang Baik' || $kondisi === 'Rusak Ringan') bg-amber-500/10 border-amber-500/30 text-amber-300
                @else bg-rose-500/10 border-rose-500/30 text-rose-300 @endif">
                <span class="text-[9.5px] uppercase font-extrabold opacity-70 tracking-wider block mb-0.5">
                    Kondisi Fisik Terkini
                </span>
                <span class="text-xs sm:text-sm font-black flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full
                        @if($kondisi === 'Baik') bg-emerald-400
                        @elseif($kondisi === 'Kurang Baik' || $kondisi === 'Rusak Ringan') bg-amber-400
                        @else bg-rose-400 @endif"></span>
                    <span>{{ $kondisi }}</span>
                </span>
            </div>

            <!-- Status Penggunaan -->
            <div class="p-3 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-300">
                <span class="text-[9.5px] uppercase font-extrabold opacity-70 tracking-wider block mb-0.5">
                    Status Penggunaan
                </span>
                <span class="text-xs sm:text-sm font-black flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                    <span>{{ $register->status ?? 'Aktif Digunakan' }}</span>
                </span>
            </div>
        </div>
    </div>
</div>
