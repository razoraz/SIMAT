        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-blue-600/15 via-slate-900 to-slate-900 border border-blue-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/30 text-blue-300 text-xs font-extrabold uppercase tracking-wider mb-3 shadow-sm backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse shadow-sm shadow-blue-400"></span>
                        <span>HIERARKI UNIT KERJA, PAVILIUN & INSTALASI RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Unit Kerja</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Memuat informasi lokasi penempatan aset tetap, penanggung jawab ruangan, kuantitas aset tercatat, serta akumulasi nilai aset di setiap unit kerja.
                    </p>
                </div>
                
                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                <a href="{{ route('unit.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-bold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Unit Baru</span>
                </a>
                @endif
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Unit</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="units.length + ' Lokasi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Nilai Terdistribusi</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(units.reduce((acc, u) => acc + (parseInt(String(u.total_nilai).replace(/[^0-9]/g, '')) || 0), 0))"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Aset Aktif</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300" x-text="units.reduce((acc, u) => acc + (u.assets ? u.assets.length : (u.total_aset || 0)), 0) + ' Item'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">👨‍⚕️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Penanggung Jawab</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300" x-text="units.filter(u => u.kepala && u.kepala !== '-').length + ' Kepala Unit'"></span>
                    </div>
                </div>
            </div>
        </div>
