            <!-- Header Banner & Mini KPI Strip -->
            <div class="bg-gradient-to-r from-teal-600/15 via-slate-900 to-slate-900 border border-teal-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                        <span>{{ $isSubAdmin ? 'PENGAJUAN & PERMOHONAN BARANG ASET RUANGAN' : 'PENYERAHAN & ALOKASI BARANG ASET RSUD' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $isSubAdmin ? 'Katalog Pengajuan Baru' : 'Katalog Distribusi ASTAP' }}</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        {{ $isSubAdmin 
                            ? 'Daftar pengajuan permohonan alokasi barang aset ruangan. Anda dapat mengajukan permintaan kebutuhan barang baru untuk unit/ruangan Anda.' 
                            : 'Pengelolaan alokasi penyerahan barang aset dari inventaris ke paviliun rawat inap, unit RSUD, dan instalasi RSUD. Mendukung distribusi beberapa barang sekaligus dalam satu transaksi.' }}
                    </p>
                </div>
                
                <!-- Action Button Input Baru -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('distribusi.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>{{ $isSubAdmin ? 'Input Pengajuan Baru' : 'Input Distribusi Baru' }}</span>
                    </a>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 gap-3 mt-6 pt-6 border-t border-slate-800/80" style="grid-template-columns: repeat(5, minmax(0, 1fr))">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🚚</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Distribusi</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="countAll + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Telah Diterima</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300" x-text="countSelesai + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">⏳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Menunggu Admin</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300" x-text="countMenungguAdmin + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">⏳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Menunggu Penerima</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300" x-text="countMenungguPenerima + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-400 text-lg">🚫</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Ditolak</span>
                        <span class="text-sm sm:text-base font-extrabold text-rose-300" x-text="countDitolak + ' Transaksi'"></span>
                    </div>
                </div>
            </div>
        </div>
