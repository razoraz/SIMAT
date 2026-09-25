        {{-- ===== TOP HEADER ===== --}}
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('mutasi.index') }}"
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center px-2.5 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ UBAH PENGAJUAN MUTASI' : '🔄 PENGAJUAN MUTASI WIZARD (4 LANGKAH)'"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white">Form Multi-Tahap Mutasi Aset</h1>
                </div>
            </div>
            
            <div class="flex items-center space-x-2 text-xs text-slate-400 font-bold bg-slate-950 px-3.5 py-2 rounded-2xl border border-slate-800">
                <span>Tahap saat ini:</span>
                <span class="text-rose-400 font-mono text-sm font-black" x-text="'Langkah ' + step + ' dari 4'"></span>
            </div>
        </div>

