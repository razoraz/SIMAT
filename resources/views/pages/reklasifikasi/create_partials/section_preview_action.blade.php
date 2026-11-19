<!-- BILAH AKSI NAVIGASI LANGKAH 1 -->
<div class="p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 backdrop-blur-xl">
    <a href="{{ route('master.reklasifikasi', ['tahun' => $selectedTahun]) }}"
       class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-950 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 text-xs font-bold transition-all cursor-pointer text-center">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
        </svg>
        <span>Batalkan &amp; Kembali</span>
    </a>

    <div class="flex items-center gap-3 self-end sm:self-auto">
        <button type="button" @click="goToStep(2)"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-7 py-3 rounded-2xl text-xs font-black shadow-xl transition-all cursor-pointer"
                :class="!selectedAstap
                    ? 'bg-slate-800 border border-slate-700 text-slate-500 cursor-not-allowed opacity-60 shadow-none'
                    : 'bg-gradient-to-r from-indigo-600 via-purple-600 to-indigo-600 hover:from-indigo-500 hover:to-purple-500 text-white shadow-indigo-600/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 active:scale-95'">
            <template x-if="!selectedAstap">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Pilih Aset di Tahap 01</span>
                </div>
            </template>
            <template x-if="selectedAstap">
                <div class="flex items-center gap-2">
                    <span>Lanjut ke Verifikasi Reklasifikasi</span>
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                    </svg>
                </div>
            </template>
        </button>
    </div>
</div>
