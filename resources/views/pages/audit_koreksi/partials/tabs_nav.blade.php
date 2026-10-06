{{-- TAB NAVIGASI KHUSUS 3 SUB-KOREKSI NILAI BMD --}}
<div class="flex items-center justify-between border-b border-slate-800/80 pb-3 gap-4 flex-wrap">
    <div class="flex items-center p-1 rounded-2xl bg-slate-950/80 border border-slate-800/80 backdrop-blur-sm overflow-x-auto max-w-full">
        {{-- Tab 1: Semua Koreksi --}}
        <button type="button" @click="activeTab = 'semua'"
                class="flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition-all whitespace-nowrap"
                :class="activeTab === 'semua' 
                    ? 'bg-slate-800 text-white border border-slate-700 shadow-md' 
                    : 'text-slate-400 hover:text-white'">
            <span>📑</span>
            <span>Semua Koreksi</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-slate-900 border border-slate-700 text-slate-300">
                {{ $semuaKoreksi->count() }}
            </span>
        </button>

        {{-- Tab 2: Koreksi Biasa --}}
        <button type="button" @click="activeTab = 'biasa'"
                class="flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition-all ml-1 whitespace-nowrap"
                :class="activeTab === 'biasa' 
                    ? 'bg-indigo-600/25 text-indigo-300 border border-indigo-500/40 shadow-md shadow-indigo-950/30' 
                    : 'text-slate-400 hover:text-indigo-300'">
            <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
            <span>Koreksi Biasa (Internal)</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-indigo-950/80 border border-indigo-800/80 text-indigo-300">
                {{ $biasaKoreksi->count() }}
            </span>
        </button>

        {{-- Tab 3: Koreksi LKD --}}
        <button type="button" @click="activeTab = 'lkd'"
                class="flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition-all ml-1 whitespace-nowrap"
                :class="activeTab === 'lkd' 
                    ? 'bg-cyan-600/25 text-cyan-300 border border-cyan-500/40 shadow-md shadow-cyan-950/30' 
                    : 'text-slate-400 hover:text-cyan-300'">
            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
            <span>Koreksi LKD (BPK RI)</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyan-950/80 border border-cyan-800/80 text-cyan-300">
                {{ $lkdKoreksi->count() }}
            </span>
        </button>

        {{-- Tab 4: Koreksi Manset --}}
        <button type="button" @click="activeTab = 'manset'"
                class="flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition-all ml-1 whitespace-nowrap"
                :class="activeTab === 'manset' 
                    ? 'bg-emerald-600/25 text-emerald-300 border border-emerald-500/40 shadow-md shadow-emerald-950/30' 
                    : 'text-slate-400 hover:text-emerald-300'">
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            <span>Koreksi Manset (BPKAD)</span>
            <span class="px-1.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-emerald-950/80 border border-emerald-800/80 text-emerald-300">
                {{ $mansetKoreksi->count() }}
            </span>
        </button>
    </div>

    {{-- Keterangan Aktif --}}
    <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
        <span x-show="activeTab === 'semua'">Menampilkan seluruh riwayat audit penyesuaian nilai buku</span>
        <span x-show="activeTab === 'biasa'">Fokus selisih rekonsiliasi kas internal &amp; pembukuan (Kolom 5 &amp; 15)</span>
        <span x-show="activeTab === 'lkd'">Fokus temuan audit LHP BPK RI &amp; neraca LKPD (Kolom 6 &amp; 16)</span>
        <span x-show="activeTab === 'manset'">Fokus sinkronisasi aplikasi E-Manset / SIMDA BMD BPKAD (Kolom 7 &amp; 17)</span>
    </div>
</div>
