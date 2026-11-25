{{-- TAB NAVIGASI KHUSUS 3 SUB-KOREKSI NILAI BMD --}}
<style>
    .audit-tabs-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
    }
    .audit-tabs-box {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        width: 100%;
        max-width: 100%;
    }
    .audit-tabs-caption {
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        max-width: 100%;
    }
    @media (min-width: 1024px) {
        .audit-tabs-wrap {
            flex-direction: row;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }
        .audit-tabs-box {
            justify-content: flex-start;
            width: auto;
            gap: 0.25rem;
        }
        .audit-tabs-caption {
            justify-content: flex-end;
            text-align: left;
        }
    }
</style>

<div class="audit-tabs-wrap border-b border-slate-800/80 pb-3">
    <div class="audit-tabs-box p-1.5 rounded-2xl bg-slate-950/90 border border-slate-800/90 backdrop-blur-sm shadow-inner">
        {{-- Tab 1: Semua Koreksi --}}
        <button type="button" @click="activeTab = 'semua'"
                class="flex items-center space-x-2 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer active:scale-95"
                :class="activeTab === 'semua' 
                    ? 'bg-slate-800 text-white border border-slate-700 shadow-md' 
                    : 'text-slate-400 hover:text-white hover:bg-slate-900/60'">
            <svg class="w-4 h-4 text-slate-300 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <span>Semua Koreksi</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold transition-colors"
                  :class="activeTab === 'semua' ? 'bg-slate-950 text-emerald-400 border border-emerald-500/30' : 'bg-slate-900 border border-slate-800 text-slate-400'">
                {{ $semuaKoreksi->count() }}
            </span>
        </button>

        {{-- Tab 2: Koreksi Biasa --}}
        <button type="button" @click="activeTab = 'biasa'"
                class="flex items-center space-x-2 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer active:scale-95"
                :class="activeTab === 'biasa' 
                    ? 'bg-indigo-600/30 text-indigo-200 border border-indigo-500/50 shadow-md shadow-indigo-950/40' 
                    : 'text-slate-400 hover:text-indigo-300 hover:bg-slate-900/60'">
            <svg class="w-4 h-4 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span>Koreksi Biasa (Internal)</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold transition-colors"
                  :class="activeTab === 'biasa' ? 'bg-indigo-950 text-indigo-300 border border-indigo-500/40' : 'bg-slate-900 border border-slate-800 text-slate-400'">
                {{ $biasaKoreksi->count() }}
            </span>
        </button>

        {{-- Tab 3: Koreksi LKD --}}
        <button type="button" @click="activeTab = 'lkd'"
                class="flex items-center space-x-2 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer active:scale-95"
                :class="activeTab === 'lkd' 
                    ? 'bg-cyan-600/30 text-cyan-200 border border-cyan-500/50 shadow-md shadow-cyan-950/40' 
                    : 'text-slate-400 hover:text-cyan-300 hover:bg-slate-900/60'">
            <svg class="w-4 h-4 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
            </svg>
            <span>Koreksi LKD (BPK RI)</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold transition-colors"
                  :class="activeTab === 'lkd' ? 'bg-cyan-950 text-cyan-300 border border-cyan-500/40' : 'bg-slate-900 border border-slate-800 text-slate-400'">
                {{ $lkdKoreksi->count() }}
            </span>
        </button>

        {{-- Tab 4: Koreksi Manset --}}
        <button type="button" @click="activeTab = 'manset'"
                class="flex items-center space-x-2 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl text-xs font-bold transition-all whitespace-nowrap cursor-pointer active:scale-95"
                :class="activeTab === 'manset' 
                    ? 'bg-emerald-600/30 text-emerald-200 border border-emerald-500/50 shadow-md shadow-emerald-950/40' 
                    : 'text-slate-400 hover:text-emerald-300 hover:bg-slate-900/60'">
            <svg class="w-4 h-4 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            <span>Koreksi Manset (BPKAD)</span>
            <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-bold transition-colors"
                  :class="activeTab === 'manset' ? 'bg-emerald-950 text-emerald-300 border border-emerald-500/40' : 'bg-slate-900 border border-slate-800 text-slate-400'">
                {{ $mansetKoreksi->count() }}
            </span>
        </button>
    </div>

    {{-- Keterangan Aktif --}}
    <div class="audit-tabs-caption gap-2 text-[11px] sm:text-xs text-slate-400 px-3 py-1.5 rounded-xl bg-slate-950/60 border border-slate-800/80">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse shrink-0"></span>
        <span x-show="activeTab === 'semua'">Menampilkan seluruh riwayat audit penyesuaian nilai buku</span>
        <span x-show="activeTab === 'biasa'">Fokus selisih rekonsiliasi kas internal &amp; pembukuan (Kolom 5 &amp; 15)</span>
        <span x-show="activeTab === 'lkd'">Fokus temuan audit LHP BPK RI &amp; neraca LKPD (Kolom 6 &amp; 16)</span>
        <span x-show="activeTab === 'manset'">Fokus sinkronisasi aplikasi E-Manset / SIMDA BMD BPKAD (Kolom 7 &amp; 17)</span>
    </div>
</div>