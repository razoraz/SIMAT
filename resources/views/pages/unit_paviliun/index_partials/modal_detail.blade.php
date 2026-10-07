        <!-- MODAL DETAIL UNIT & INVENTARIS ASET RUANGAN (LENGKAP DENGAN DAFTAR ASET)   -->
        <!-- ========================================================================= -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-3 sm:p-5 overflow-y-auto" x-cloak>
            <div @click.away="if (!showPrintKIRModal && !showEditModal) showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-5xl w-full p-6 sm:p-7 shadow-2xl space-y-5 my-auto max-h-[90vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-blue-500/20 text-blue-300 text-xl">🏥</div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30" x-text="selectedUnit ? selectedUnit.kode : ''"></span>
                                <h3 class="text-lg sm:text-xl font-extrabold text-white" x-text="selectedUnit ? selectedUnit.nama : ''"></h3>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="selectedUnit ? ('Kepala Ruangan: ' + (selectedUnit.kepala || '-')) : ''"></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="printKIR()" x-show="canAccessKir(selectedUnit)"
                            class="hidden sm:inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/40 text-xs font-bold transition-all active:scale-95">
                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak KIR</span>
                        </button>
                        <button type="button" @click="showDetailModal = false" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-base font-bold transition-all">
                            &times;
                        </button>
                    </div>
                </div>

                <!-- Informasi Ringkas Profil Ruangan -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0" x-if="selectedUnit">
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Kepala Ruangan / PJ</span>
                        <p class="font-bold text-white text-xs mt-0.5 truncate" x-text="selectedUnit.kepala"></p>
                        <p class="text-[10px] text-slate-400 font-mono truncate mt-0.5" x-text="'NIP: ' + (selectedUnit.nip || '-')"></p>
                    </div>
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-blue-400 block tracking-wider">Email Login Sub Admin</span>
                        <p class="font-bold text-blue-300 text-xs mt-0.5 truncate" x-text="selectedUnit.email"></p>
                        <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="'Ruangan ' + selectedUnit.nama"></p>
                    </div>
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-cyan-400 block tracking-wider">Total Aset Terpasang</span>
                        <p class="font-black text-cyan-300 text-xs mt-0.5" x-text="(selectedUnit.assets ? selectedUnit.assets.length : selectedUnit.total_aset) + ' Item Inventaris'"></p>
                        <span class="text-[10px] text-slate-500 block">KIR Ruangan</span>
                    </div>
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 block tracking-wider">Total Nilai Realisasi</span>
                        <p class="font-black text-emerald-400 font-mono text-xs mt-0.5" x-text="selectedUnit.total_nilai"></p>
                        <span class="text-[10px] text-slate-500 block">Akumulasi Aset</span>
                    </div>
                </div>

                <!-- Toolbar Pencarian & Filter Aset Ruangan -->
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto flex-1">
                        <div class="relative flex-1 sm:w-64">
                            <input type="text" x-model="detailSearchQuery" placeholder="Cari nama aset, merk, kode 108, nomor seri..."
                                class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2 pl-9 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                            <svg class="w-3.5 h-3.5 text-blue-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <button type="button" x-show="detailSearchQuery" @click="detailSearchQuery = ''" class="absolute right-2.5 top-2 text-slate-500 hover:text-white text-xs">&times;</button>
                        </div>

                        <select x-model="detailKondisiFilter" class="bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-blue-500">
                            <option value="all">Semua Kondisi</option>
                            <option value="Baik">Kondisi Baik (B)</option>
                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                        </select>
                    </div>

                    <div class="text-[11px] text-slate-400 shrink-0 font-medium">
                        Ditemukan <span class="text-blue-400 font-bold" x-text="filteredDetailAssets.length"></span> dari <span class="text-white font-bold" x-text="(selectedUnit && selectedUnit.assets) ? selectedUnit.assets.length : 0"></span> Aset Terdata
                    </div>
                </div>

                <!-- Tabel Daftar Rincian Aset Terpasang di Ruangan -->
                <div class="flex-1 overflow-y-auto bg-slate-950/80 border border-slate-800 rounded-2xl overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300 min-w-[750px]">
                        <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10px] sticky top-0 z-10 border-b border-slate-800">
                            <tr>
                                <th class="px-3 py-3 text-center w-10">No</th>
                                <th class="px-3 py-3 text-center">Kode 108 / Register</th>
                                <th class="px-4 py-3">Nama Barang & Spesifikasi</th>
                                <th class="px-3 py-3 text-center">Tahun</th>
                                <th class="px-3 py-3 text-center">Kondisi</th>
                                <th class="px-3 py-3 text-right">Nilai Aset</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            <template x-for="(ast, idx) in filteredDetailAssets" :key="idx">
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="px-3 py-3 text-center text-slate-400 font-mono font-bold" x-text="idx + 1"></td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="font-mono text-cyan-400 font-bold text-[11px] block" x-text="ast.kode"></span>
                                        <span class="font-mono text-[9px] text-slate-400" x-text="ast.no_seri"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-white text-xs" x-text="ast.nama"></div>
                                        <div class="text-[10px] text-blue-300 font-medium" x-text="ast.merk"></div>
                                    </td>
                                    <td class="px-3 py-3 text-center font-mono font-semibold text-slate-300" x-text="ast.tahun"></td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="{
                                                'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': ast.kondisi === 'Baik',
                                                'bg-amber-500/20 text-amber-300 border border-amber-500/30': ast.kondisi === 'Kurang Baik' || ast.kondisi === 'Rusak Ringan',
                                                'bg-rose-500/20 text-rose-300 border border-rose-500/30': ast.kondisi === 'Rusak Berat'
                                            }"
                                            x-text="ast.kondisi"></span>
                                    </td>
                                    <td class="px-3 py-3 text-right font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(ast.nilai ?? ast.harga ?? 0)"></td>
                                </tr>
                            </template>
                            <template x-if="filteredDetailAssets.length === 0">
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                        <div class="text-2xl mb-1">🔍</div>
                                        <p class="font-semibold text-white">Tidak ada aset yang cocok dengan filter</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Coba ubah kata kunci pencarian atau reset filter kondisi.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Action Footer -->
                <div class="pt-3 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center space-x-2 text-xs text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Inventaris ruangan terintegrasi dengan Kartu Inventaris Ruangan (KIR) & BAST Distribusi.</span>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                        <a href="{{ route('bast.index') }}"
                            class="px-3.5 py-2 rounded-xl bg-purple-500/15 hover:bg-purple-500/25 text-purple-300 border border-purple-500/30 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>BAST Ruangan</span>
                        </a>
                        @endif

                        <button type="button" @click="openPrintKIR(selectedUnit)" x-show="canAccessKir(selectedUnit)"
                            class="px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs shadow-lg shadow-blue-500/25 border border-blue-400/30 transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak KIR</span>
                        </button>

                        <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>

            </div>
        </div>
