        <!-- VIEW MODE 1: GRID CARDS -->
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="item in filteredUnits" :key="item.id">
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl hover:border-blue-500/50 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30" x-text="item.kode"></span>
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-800 text-cyan-300 border border-slate-700" x-text="item.total_aset + ' Aset'"></span>
                        </div>
                        <h3 class="text-base font-extrabold text-white group-hover:text-blue-400 transition-colors mb-4" x-text="item.nama"></h3>

                        <div class="space-y-2 py-3 border-y border-slate-800/80 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Kepala Unit:</span>
                                <span class="font-semibold text-white" x-text="item.kepala"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Total Aset:</span>
                                <span class="font-bold text-cyan-400" x-text="item.total_aset + ' Item'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Nilai Aset:</span>
                                <span class="font-bold text-emerald-400 font-mono" x-text="item.total_nilai"></span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-1">
                        <button type="button" @click="openPrintKIR(item)" x-show="canAccessKir(item)"
                            class="px-2.5 py-1.5 rounded-xl bg-blue-500/15 text-blue-300 hover:bg-blue-500/25 border border-blue-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>KIR</span>
                        </button>
                        <button type="button" @click="openDetail(item)"
                            class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Detail</span>
                        </button>
                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                        <a :href="'/unit-paviliun/' + item.id + '/edit'"
                            class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Ubah</span>
                        </a>
                        <button type="button" @click="deleteUnit(item)"
                            class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                        @endif
                    </div>
                </div>
            </template>
        </div>

        <!-- VIEW MODE 2: TABLE VIEW -->
        <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 340px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                    <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 bg-slate-950 whitespace-nowrap">No</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950 whitespace-nowrap">Kode Unit</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950 whitespace-nowrap">Nama Unit / Paviliun</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950 whitespace-nowrap">Kepala Ruangan</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950 whitespace-nowrap">Total Aset</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950 whitespace-nowrap">Total Nilai</th>
                            <th class="px-3 py-3.5 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0 min-w-[310px] w-[310px]" style="position: sticky; right: 0; top: 0; z-index: 25; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredUnits" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                                <td class="px-4 py-4 text-center font-mono font-semibold text-blue-400 whitespace-nowrap" x-text="item.kode"></td>
                                <td class="px-4 py-4 font-bold text-white whitespace-nowrap" x-text="item.nama"></td>
                                <td class="px-4 py-4 text-center whitespace-nowrap text-slate-300" x-text="item.kepala || '-'"></td>
                                <td class="px-4 py-4 text-center font-bold text-cyan-400 font-mono whitespace-nowrap" x-text="item.total_aset + ' Item'"></td>
                                <td class="px-4 py-4 text-center font-bold text-emerald-400 font-mono whitespace-nowrap" x-text="item.total_nilai"></td>
                                
                                <!-- Kolom Aksi — FREEZE STICKY RIGHT -->
                                <td class="px-3 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[310px] w-[310px]" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" @click="openPrintKIR(item)" x-show="canAccessKir(item)"
                                            class="px-2.5 py-1.5 rounded-xl bg-blue-500/15 text-blue-300 hover:bg-blue-500/25 border border-blue-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            <span>KIR</span>
                                        </button>
                                        <button type="button" @click="openDetail(item)"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </button>
                                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                        <a :href="'/unit-paviliun/' + item.id + '/edit'"
                                            class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Ubah</span>
                                        </a>
                                        <button type="button" @click="deleteUnit(item)"
                                            class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm cursor-pointer active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
