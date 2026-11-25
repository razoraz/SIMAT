<!-- PANEL KHUSUS KAPITALISASI KDP SELESAI -> DEFINITIF (KIB F -> KIB C / KIB D) -->
<div x-show="formData.jenis_reklas === 'KDP_TO_DEFINITIF'"
     x-transition.duration.250ms
     class="space-y-4">

    <!-- 1. Pilihan KIB Tujuan Definitif (Hanya KIB C atau KIB D) -->
    <div class="p-4 rounded-2xl bg-slate-950/70 border border-rose-500/30 shadow-lg shadow-rose-950/20 space-y-2.5">
        <div class="flex items-center justify-between gap-2">
            <label class="text-rose-300 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5">
                <span>🏗️</span>
                <span>Alihkan KDP Selesai ke KIB Definitif:</span>
            </label>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/40 shrink-0">
                KIB F &rarr; Definitif
            </span>
        </div>
        <select x-model="formData.tujuan_kib"
                @change="onTujuanKibChange()"
                class="w-full bg-slate-900 border border-rose-500/40 rounded-xl px-3.5 py-2.5 text-xs font-bold text-white focus:outline-none focus:border-rose-400 focus:ring-1 focus:ring-rose-400/50 transition-all cursor-pointer">
            <option value="KIB C">🏛️ KIB C - Gedung &amp; Bangunan (Definitif)</option>
            <option value="KIB D">🛣️ KIB D - Jalan, Jaringan &amp; Irigasi (Definitif)</option>
        </select>
        <p class="text-[11px] text-slate-400 leading-relaxed">
            Akumulasi belanja modal KDP akan dikapitalisasi dan dibukukan sebagai aset tetap definitif pada kelompok KIB di atas.
        </p>
    </div>

    <!-- 2. Panel Transformasi Progres Fisik Konstruksi (Otomatis 100% Selesai) -->
    <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-br from-emerald-950/40 via-slate-950/70 to-slate-900 border border-emerald-500/40 shadow-xl shadow-emerald-950/30 space-y-3.5">
        <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
                <span class="p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs shrink-0">
                    ✅
                </span>
                <div>
                    <h4 class="text-xs font-black text-white tracking-wide">Transformasi Progres Fisik KDP</h4>
                    <p class="text-[10.5px] text-emerald-300/80">Syarat mutlak kapitalisasi KDP ke aset definitif</p>
                </div>
            </div>
            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm animate-pulse shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                <span>DIKUNCI 100%</span>
            </span>
        </div>

        <!-- Komparasi Progres Semula vs Progres Baru -->
        <div class="flex flex-col sm:flex-row gap-3 items-stretch bg-slate-900/80 p-3.5 rounded-xl border border-slate-800">
            <!-- Progres Awal -->
            <div class="flex-1 bg-slate-950/80 p-3.5 rounded-xl border border-slate-800/90 flex flex-col justify-between gap-2.5">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">
                        Progres Semula (KIB F)
                    </span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-md bg-amber-500/10 text-amber-300 border border-amber-500/25 font-semibold whitespace-nowrap shrink-0">
                        Dalam Pengerjaan
                    </span>
                </div>
                <div class="text-sm sm:text-base font-black text-amber-300 font-mono whitespace-nowrap"
                     x-text="(reklasKdpProgresAwal || 0) + '% Fisik'"></div>
            </div>

            <!-- Indikator Panah -->
            <div class="flex items-center justify-center shrink-0">
                <span class="w-8 h-8 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-black text-sm flex items-center justify-center shrink-0 shadow-sm">
                    <span class="sm:hidden">&darr;</span>
                    <span class="hidden sm:inline">&rarr;</span>
                </span>
            </div>

            <!-- Progres Baru (100%) -->
            <div class="flex-1 bg-emerald-950/40 p-3.5 rounded-xl border border-emerald-500/40 flex flex-col justify-between gap-2.5 shadow-inner">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-wider whitespace-nowrap">
                        Progres Baru (Definitif)
                    </span>
                    <span class="text-[10px] px-2.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-bold whitespace-nowrap shrink-0">
                        Terbit BAST 100%
                    </span>
                </div>
                <div class="text-sm sm:text-base font-black text-emerald-200 font-mono whitespace-nowrap">
                    100% Selesai Penuh
                </div>
            </div>
        </div>

        <!-- Progress Bar Visual 100% -->
        <div class="space-y-1.5">
            <div class="flex justify-between text-[10.5px] font-medium text-slate-400">
                <span>Tingkat Kesiapan Aset:</span>
                <span class="font-mono font-bold text-emerald-300">100% (Pekerjaan Fisik Selesai)</span>
            </div>
            <div class="w-full bg-slate-950 rounded-full h-2.5 p-0.5 border border-slate-800 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400 h-1.5 rounded-full w-full transition-all duration-500 shadow-sm shadow-emerald-500/50"></div>
            </div>
        </div>

        <!-- Catatan Akuntansi & Neraca RSUD -->
        <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 text-[11px] text-slate-300 leading-relaxed flex items-start gap-2">
            <span class="text-emerald-400 text-xs shrink-0 mt-0.5">ℹ️</span>
            <div>
                <strong class="text-white">Dampak Buku &amp; Neraca:</strong> Saldo <span class="text-rose-300 font-mono font-bold">KIB F (Konstruksi)</span> akan dinihilkan / berkurang (-) di neraca, dan berpindah menjadi <span class="text-emerald-300 font-mono font-bold" x-text="formData.tujuan_kib || 'KIB C'"></span> aktif (+) yang siap dioperasikan serta dihitung penyusutannya.
            </div>
        </div>
    </div>
</div>
