{{-- TOP HEADER BANNER MODUL AUDIT KOREKSI NILAI BMD --}}
<div class="relative overflow-hidden rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-7 backdrop-blur-xl shadow-2xl">
    {{-- Glow background subtle decoration --}}
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        {{-- Sisi Kiri: Icon Box + Judul & Subjudul Resmi --}}
        <div class="flex items-start sm:items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/10">
                <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>

            <div class="space-y-1.5">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/60 text-[11px] font-semibold text-emerald-300">
                    <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Standar Kertas Kerja RMB 21 Kolom · BPKAD &amp; BPK RI</span>
                </div>
                
                <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-3">
                    <span>Audit &amp; Ledger Koreksi Nilai BMD</span>
                </h1>
                
                <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                    Pusat pengawasan dan rekapitulasi 3 klaster penyesuaian nilai buku aset tetap: 
                    <strong class="text-indigo-300">Koreksi Biasa</strong> (Internal), 
                    <strong class="text-cyan-300">Koreksi LKD</strong> (Audit BPK), dan 
                    <strong class="text-emerald-300">Koreksi Manset</strong> (Harmonisasi BPKAD) pada RSUD dr. H. Koesnandi.
                </p>
            </div>
        </div>

        {{-- Sisi Kanan: Action Buttons (Ekspor CSV & Cetak) --}}
        <div class="flex items-center flex-wrap gap-2.5 shrink-0">
            <a href="{{ route('audit_koreksi.export', ['tahun' => $selectedYear, 'triwulan' => $selectedTriwulan, 'sub_koreksi' => $selectedSubKoreksi, 'tipe_koreksi' => $selectedTipe]) }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 text-emerald-300 hover:text-emerald-200 text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-950/20 hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh Rekap CSV</span>
            </a>

            <button type="button" @click="window.print()"
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-slate-200 hover:text-white text-xs font-bold transition-all flex items-center gap-2 shadow-sm hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Lembar Audit</span>
            </button>
        </div>
    </div>
</div>