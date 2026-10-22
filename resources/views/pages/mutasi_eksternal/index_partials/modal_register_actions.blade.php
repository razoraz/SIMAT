        <!-- ========================================================================= -->
        <!-- 1. MODAL: UBAH KONDISI UNIT BARANG (NIBAR)                                 -->
        <!-- ========================================================================= -->
        <template x-teleport="body">
            <div x-show="showEditKondisiModal" x-cloak @click.self="showEditKondisiModal = false"
                 class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
                 style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="border border-amber-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 my-auto bg-slate-900"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">⚙️</span>
                            <h3 class="text-base font-extrabold text-white">Ubah Kondisi Unit Barang</h3>
                        </div>
                        <button type="button" @click.stop="showEditKondisiModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer p-1">&times;</button>
                    </div>

                    <template x-if="editingRegisterItem">
                        <div class="space-y-4 text-xs">
                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">TARGET UNIT NIBAR:</span>
                                <p class="text-emerald-400 font-mono font-bold text-sm" x-text="editingRegisterItem.nibar || editingRegisterItem.no_register"></p>
                                <p class="text-slate-300 font-semibold text-[11px]" x-text="(selectedAstapDetail || selectedMutasi) ? ((selectedAstapDetail || selectedMutasi).nama_barang || (selectedAstapDetail || selectedMutasi).nama) : ''"></p>
                            </div>

                            <!-- Pilihan Kondisi -->
                            <div class="space-y-2">
                                <label class="font-bold text-slate-300 text-xs">Pilih Kondisi Terkini Unit:</label>
                                <div class="space-y-2">
                                    <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                        :class="newKondisiValue === 'Baik' ? 'bg-emerald-500/15 border-emerald-500/50 text-emerald-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                        <input type="radio" value="Baik" x-model="newKondisiValue" class="text-emerald-500 focus:ring-0">
                                        <div>
                                            <span class="font-extrabold text-xs block text-emerald-300">🟢 Baik (B)</span>
                                            <span class="text-[10px] text-slate-400 block">Unit berfungsi sempurna dan siap digunakan.</span>
                                        </div>
                                    </label>

                                    <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                        :class="newKondisiValue === 'Kurang Baik' ? 'bg-amber-500/15 border-amber-500/50 text-amber-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                        <input type="radio" value="Kurang Baik" x-model="newKondisiValue" class="text-amber-500 focus:ring-0">
                                        <div>
                                            <span class="font-extrabold text-xs block text-amber-300">🟡 Kurang Baik (KB)</span>
                                            <span class="text-[10px] text-slate-400 block">Ada kendala kecil / penurunan performa namun masih dapat difungsikan.</span>
                                        </div>
                                    </label>

                                    <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                        :class="newKondisiValue === 'Rusak Berat' ? 'bg-rose-500/15 border-rose-500/50 text-rose-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                        <input type="radio" value="Rusak Berat" x-model="newKondisiValue" class="text-rose-500 focus:ring-0">
                                        <div>
                                            <span class="font-extrabold text-xs block text-rose-300">🔴 Rusak Berat (RB)</span>
                                            <span class="text-[10px] text-slate-400 block">Unit rusak parah / tidak dapat dipakai (siap usul hapus).</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2">
                                <button type="button" @click.stop="showEditKondisiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold hover:bg-slate-700 cursor-pointer">Batal</button>
                                <button type="button" @click.stop="saveKondisiChange()" :disabled="isSavingKondisi" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold shadow-lg shadow-amber-500/20 active:scale-95 disabled:opacity-50 cursor-pointer">
                                    <span x-text="isSavingKondisi ? 'Menyimpan...' : 'Simpan Kondisi'"></span>
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <!-- ========================================================================= -->
        <!-- 2. MODAL: RIWAYAT MUTASI LENGKAP UNIT BARANG (NIBAR)                      -->
        <!-- ========================================================================= -->
        <template x-teleport="body">
            <div x-show="showRiwayatModal" x-cloak @click.self="showRiwayatModal = false"
                 class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
                 style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="border border-purple-500/40 rounded-3xl max-w-2xl w-full p-5 sm:p-6 shadow-2xl space-y-4 max-h-[88vh] overflow-y-auto my-auto bg-slate-900"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-500/20 border border-purple-500/40 flex items-center justify-center text-purple-400 shrink-0 shadow-lg shadow-purple-500/10">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-white leading-tight">Riwayat Mutasi &amp; Status Unit Barang</h3>
                                <p class="text-[11px] text-slate-400 mt-0.5">Histori komprehensif: Mutasi Internal Ruangan, Mutasi Antar-OPD, &amp; Reklasifikasi Aset</p>
                            </div>
                        </div>
                        <button type="button" @click.stop="showRiwayatModal = false" class="text-slate-500 hover:text-white text-xl font-bold p-1 cursor-pointer">&times;</button>
                    </div>

                    <template x-if="selectedRiwayatRegister">
                        <div class="space-y-4 text-xs">
                            <!-- Info Register Card -->
                            <div class="p-3.5 rounded-2xl border border-slate-800 flex items-center justify-between gap-3 shadow-inner bg-slate-950">
                                <div class="space-y-0.5 min-w-0 flex-1">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold block truncate" x-text="(selectedAstapDetail || selectedMutasi) ? ((selectedAstapDetail || selectedMutasi).nama_barang || (selectedAstapDetail || selectedMutasi).nama) : 'Unit Barang'"></span>
                                    <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                        <span class="text-purple-300 font-mono font-bold text-xs" x-text="selectedRiwayatRegister.nibar || selectedRiwayatRegister.no_register"></span>
                                        <span class="text-slate-500 text-[11px]">•</span>
                                        <span class="text-cyan-400 text-[11px] font-semibold truncate" x-text="selectedRiwayatRegister.ruang_pemegang || 'Gudang Aset'"></span>
                                    </div>
                                </div>
                                <div class="shrink-0 text-right">
                                    <span class="text-[9px] uppercase font-bold text-slate-500 block mb-0.5">Kondisi Sekarang</span>
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold border inline-block"
                                          :class="{
                                              'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': selectedRiwayatRegister.kondisi === 'Baik',
                                              'bg-amber-500/20 text-amber-300 border-amber-500/30': selectedRiwayatRegister.kondisi === 'Kurang Baik',
                                              'bg-rose-500/20 text-rose-300 border-rose-500/30': selectedRiwayatRegister.kondisi === 'Rusak Berat'
                                          }" x-text="selectedRiwayatRegister.kondisi"></span>
                                </div>
                            </div>

                            <!-- Modern Pills Tab Switcher: Semua, Internal, Eksternal, Reklasifikasi -->
                            <div class="flex items-center gap-1.5 p-1 rounded-2xl bg-slate-950 border border-slate-800/80 overflow-x-auto custom-scrollbar">
                                <button type="button" @click="riwayatActiveTab = 'semua'"
                                    class="flex-1 min-w-[100px] py-1.5 px-3 rounded-xl font-bold text-[11px] transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                                    :class="riwayatActiveTab === 'semua'
                                        ? 'bg-purple-600 text-white shadow-md shadow-purple-600/30 font-extrabold'
                                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'">
                                    <span>🌐 Semua Histori</span>
                                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono"
                                          :class="riwayatActiveTab === 'semua' ? 'bg-purple-900/80 text-white' : 'bg-slate-800 text-slate-400'"
                                          x-text="selectedRiwayatCounts.total || 0"></span>
                                </button>

                                <button type="button" @click="riwayatActiveTab = 'internal'"
                                    class="flex-1 min-w-[110px] py-1.5 px-3 rounded-xl font-bold text-[11px] transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                                    :class="riwayatActiveTab === 'internal'
                                        ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30 font-extrabold'
                                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'">
                                    <span>🏢 Mutasi Internal</span>
                                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono"
                                          :class="riwayatActiveTab === 'internal' ? 'bg-indigo-900/80 text-white' : 'bg-slate-800 text-slate-400'"
                                          x-text="selectedRiwayatCounts.internal || 0"></span>
                                </button>

                                <button type="button" @click="riwayatActiveTab = 'eksternal'"
                                    class="flex-1 min-w-[110px] py-1.5 px-3 rounded-xl font-bold text-[11px] transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                                    :class="riwayatActiveTab === 'eksternal'
                                        ? 'bg-cyan-600 text-white shadow-md shadow-cyan-600/30 font-extrabold'
                                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'">
                                    <span>🏛️ Mutasi Eksternal</span>
                                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono"
                                          :class="riwayatActiveTab === 'eksternal' ? 'bg-cyan-900/80 text-white' : 'bg-slate-800 text-slate-400'"
                                          x-text="selectedRiwayatCounts.eksternal || 0"></span>
                                </button>

                                <button type="button" @click="riwayatActiveTab = 'reklas'"
                                    class="flex-1 min-w-[110px] py-1.5 px-3 rounded-xl font-bold text-[11px] transition-all flex items-center justify-center space-x-1.5 cursor-pointer"
                                    :class="riwayatActiveTab === 'reklas'
                                        ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 font-extrabold'
                                        : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900'">
                                    <span>🔄 Reklasifikasi</span>
                                    <span class="px-1.5 py-0.2 rounded-full text-[9px] font-mono"
                                          :class="riwayatActiveTab === 'reklas' ? 'bg-emerald-900/80 text-white' : 'bg-slate-800 text-slate-400'"
                                          x-text="selectedRiwayatCounts.reklas || 0"></span>
                                </button>
                            </div>

                            <!-- Loading State -->
                            <template x-if="isLoadingRiwayat">
                                <div class="py-12 text-center space-y-3">
                                    <div class="inline-block w-7 h-7 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                                    <p class="text-xs text-slate-400 font-medium">Memuat histori mutasi internal, eksternal &amp; reklasifikasi...</p>
                                </div>
                            </template>

                            <!-- ========================================================= -->
                            <!-- TAB 1: SEMUA HISTORI (TIMELINE TERPADU)                   -->
                            <!-- ========================================================= -->
                            <div x-show="!isLoadingRiwayat && riwayatActiveTab === 'semua'" class="space-y-3">
                                <template x-if="!selectedRiwayatTimeline || selectedRiwayatTimeline.length === 0">
                                    <div class="p-8 text-center rounded-2xl border border-dashed border-slate-800 space-y-2 bg-slate-950">
                                        <span class="text-3xl block">📦</span>
                                        <h4 class="text-sm font-bold text-slate-200">Belum Ada Riwayat Tercatat</h4>
                                        <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                                            Unit register ini belum pernah dimutasi internal antar-ruangan, dilimpahkan ke SKPD luar, maupun direklasifikasi. Unit saat ini berada di lokasi: <strong class="text-slate-300" x-text="selectedRiwayatRegister.ruang_pemegang || 'Gudang Aset'"></strong> dengan kondisi <strong class="text-emerald-400" x-text="selectedRiwayatRegister.kondisi"></strong>.
                                        </p>
                                    </div>
                                </template>

                                <template x-if="selectedRiwayatTimeline && selectedRiwayatTimeline.length > 0">
                                    <div class="space-y-3 relative pl-4 border-l-2 border-purple-500/30 my-2">
                                        <template x-for="(item, idx) in selectedRiwayatTimeline" :key="'timeline-' + idx">
                                            <div class="relative group">
                                                <!-- Timeline dot -->
                                                <div class="absolute -left-[21px] top-2.5 w-2.5 h-2.5 rounded-full ring-4 ring-slate-900"
                                                     :class="{
                                                         'bg-indigo-500 ring-indigo-950': item.kategori === 'Internal',
                                                         'bg-cyan-500 ring-cyan-950': item.kategori === 'Eksternal',
                                                         'bg-emerald-500 ring-emerald-950': item.kategori === 'Reklasifikasi'
                                                     }"></div>

                                                <div class="p-3.5 rounded-2xl border border-slate-800 space-y-2.5 hover:border-slate-700 transition-colors shadow-sm bg-slate-950">
                                                    <!-- Header Timeline Card -->
                                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="text-xs font-mono font-extrabold text-white flex items-center space-x-1">
                                                                <span>📅</span>
                                                                <span x-text="item.tanggal_mutasi || item.tanggal || '-'"></span>
                                                            </span>
                                                            <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold uppercase border"
                                                                  :class="{
                                                                      'bg-indigo-500/20 text-indigo-300 border-indigo-500/40': item.kategori === 'Internal',
                                                                      'bg-cyan-500/20 text-cyan-300 border-cyan-500/40': item.kategori === 'Eksternal',
                                                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': item.kategori === 'Reklasifikasi'
                                                                  }" x-text="item.kategori === 'Internal' ? '🏢 Mutasi Ruangan' : (item.kategori === 'Eksternal' ? '🏛️ Mutasi Antar-OPD' : '🔄 Reklasifikasi')"></span>
                                                            <span class="text-[10px] font-mono text-slate-400" x-text="'(' + (item.nomor_bamb || item.nomor_bast || item.nomor_ba || '-') + ')'"></span>
                                                        </div>

                                                        <!-- Status / Kondisi Badge -->
                                                        <template x-if="item.kategori === 'Internal'">
                                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border"
                                                                  :class="{
                                                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': item.kondisi === 'Baik',
                                                                      'bg-amber-500/20 text-amber-300 border-amber-500/30': item.kondisi === 'Kurang Baik',
                                                                      'bg-rose-500/20 text-rose-300 border-rose-500/30': item.kondisi === 'Rusak Berat'
                                                                  }" x-text="'Kondisi: ' + (item.kondisi || 'Baik')"></span>
                                                        </template>
                                                        <template x-if="item.kategori === 'Eksternal'">
                                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30" x-text="item.status || 'Dilimpahkan'"></span>
                                                        </template>
                                                        <template x-if="item.kategori === 'Reklasifikasi'">
                                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30" x-text="item.sub_koreksi_label || item.jenis_reklas"></span>
                                                        </template>
                                                    </div>

                                                    <!-- Rincian Konten Berdasarkan Kategori -->
                                                    <template x-if="item.kategori === 'Internal'">
                                                        <div class="space-y-2">
                                                            <div class="p-2 bg-slate-900/80 rounded-xl border border-slate-800 flex items-center justify-between text-xs">
                                                                <div>
                                                                    <span class="text-[9.5px] uppercase font-bold text-slate-500 block">Dari Ruangan:</span>
                                                                    <span class="font-semibold text-slate-200" x-text="item.dari_ruang || '-'"></span>
                                                                </div>
                                                                <span class="text-indigo-400 font-extrabold text-sm px-1">➔</span>
                                                                <div>
                                                                    <span class="text-[9.5px] uppercase font-bold text-slate-500 block">Ke Ruangan:</span>
                                                                    <span class="font-bold text-indigo-300" x-text="item.ke_ruang || '-'"></span>
                                                                </div>
                                                            </div>
                                                            <p class="text-[11px] text-slate-300 italic pt-1 border-t border-slate-800/80" x-text="'Catatan: ' + (item.keterangan || '-')"></p>
                                                        </div>
                                                    </template>

                                                    <template x-if="item.kategori === 'Eksternal'">
                                                        <div class="space-y-2">
                                                            <div class="p-2 bg-slate-900/80 rounded-xl border border-slate-800 flex items-center justify-between text-xs">
                                                                <div>
                                                                    <span class="text-[9.5px] uppercase font-bold text-slate-500 block">SKPD Asal:</span>
                                                                    <span class="font-semibold text-slate-200" x-text="item.opd_asal || '-'"></span>
                                                                </div>
                                                                <span class="text-cyan-400 font-extrabold text-sm px-1">➔</span>
                                                                <div>
                                                                    <span class="text-[9.5px] uppercase font-bold text-slate-500 block">SKPD Tujuan:</span>
                                                                    <span class="font-bold text-cyan-300" x-text="item.opd_tujuan || '-'"></span>
                                                                </div>
                                                            </div>
                                                            <p class="text-[11px] text-slate-300 italic pt-1 border-t border-slate-800/80" x-text="'Peruntukan: ' + (item.keterangan || item.alasan_mutasi || '-')"></p>
                                                        </div>
                                                    </template>

                                                    <template x-if="item.kategori === 'Reklasifikasi'">
                                                        <div class="space-y-2">
                                                            <div class="p-2 bg-slate-900/80 rounded-xl border border-slate-800 flex items-center justify-between text-xs">
                                                                <div>
                                                                    <span class="text-[9.5px] uppercase font-bold text-slate-500 block">Akun / KIB Asal:</span>
                                                                    <span class="font-semibold text-slate-200" x-text="item.asal_kib || item.asal_kode || '-'"></span>
                                                                </div>
                                                                <span class="text-emerald-400 font-extrabold text-sm px-1">➔</span>
                                                                <div>
                                                                    <span class="text-[9.5px] uppercase font-bold text-slate-500 block">Akun / KIB Tujuan:</span>
                                                                    <span class="font-bold text-emerald-300" x-text="item.tujuan_kib || item.tujuan_kode || '-'"></span>
                                                                </div>
                                                            </div>
                                                            <p class="text-[11px] text-slate-300 italic pt-1 border-t border-slate-800/80" x-text="'Alasan: ' + (item.keterangan || item.alasan_reklas || '-')"></p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- ========================================================= -->
                            <!-- TAB 2: MUTASI INTERNAL RUANGAN RSUD                       -->
                            <!-- ========================================================= -->
                            <div x-show="!isLoadingRiwayat && riwayatActiveTab === 'internal'" class="space-y-3">
                                <template x-if="!selectedRiwayatMutasisInternal || selectedRiwayatMutasisInternal.length === 0">
                                    <div class="p-8 text-center rounded-2xl border border-dashed border-slate-800 space-y-2 bg-slate-950">
                                        <span class="text-3xl block">🏢</span>
                                        <h4 class="text-sm font-bold text-slate-200">Belum Ada Mutasi Ruangan</h4>
                                        <p class="text-xs text-slate-400 max-w-sm mx-auto">Unit ini belum pernah dipindahkan antar-ruangan di lingkungan RSUD.</p>
                                    </div>
                                </template>
                                <template x-if="selectedRiwayatMutasisInternal && selectedRiwayatMutasisInternal.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="mut in selectedRiwayatMutasisInternal" :key="'mut-' + mut.id">
                                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-2">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="font-mono font-bold text-white" x-text="mut.nomor_bamb || '-'"></span>
                                                    <span class="text-slate-400 font-mono text-[11px]" x-text="mut.tanggal_mutasi || '-'"></span>
                                                </div>
                                                <div class="p-2 bg-slate-900 rounded-xl border border-slate-800/60 flex items-center justify-between text-xs">
                                                    <span class="text-slate-300" x-text="mut.dari_ruang || '-'"></span>
                                                    <span class="text-indigo-400 font-bold">➔</span>
                                                    <span class="text-indigo-300 font-bold" x-text="mut.ke_ruang || '-'"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- ========================================================= -->
                            <!-- TAB 3: MUTASI EKSTERNAL (ANTAR-OPD)                       -->
                            <!-- ========================================================= -->
                            <div x-show="!isLoadingRiwayat && riwayatActiveTab === 'eksternal'" class="space-y-3">
                                <template x-if="!selectedRiwayatMutasisEksternal || selectedRiwayatMutasisEksternal.length === 0">
                                    <div class="p-8 text-center rounded-2xl border border-dashed border-slate-800 space-y-2 bg-slate-950">
                                        <span class="text-3xl block">🏛️</span>
                                        <h4 class="text-sm font-bold text-slate-200">Belum Ada Mutasi Antar-OPD Lain</h4>
                                        <p class="text-xs text-slate-400 max-w-sm mx-auto">Tidak ada mutasi eksternal sekunder untuk unit ini.</p>
                                    </div>
                                </template>
                                <template x-if="selectedRiwayatMutasisEksternal && selectedRiwayatMutasisEksternal.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="ex in selectedRiwayatMutasisEksternal" :key="'ex-' + ex.id">
                                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-2">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="font-mono font-bold text-cyan-300" x-text="ex.nomor_bast || ex.nomor_ba || '-'"></span>
                                                    <span class="text-slate-400 font-mono text-[11px]" x-text="ex.tanggal || '-'"></span>
                                                </div>
                                                <div class="p-2 bg-slate-900 rounded-xl border border-slate-800/60 flex items-center justify-between text-xs">
                                                    <span class="text-slate-300" x-text="ex.opd_asal || '-'"></span>
                                                    <span class="text-cyan-400 font-bold">➔</span>
                                                    <span class="text-cyan-300 font-bold" x-text="ex.opd_tujuan || '-'"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- ========================================================= -->
                            <!-- TAB 4: REKLASIFIKASI KODE 108                             -->
                            <!-- ========================================================= -->
                            <div x-show="!isLoadingRiwayat && riwayatActiveTab === 'reklas'" class="space-y-3">
                                <template x-if="!selectedRiwayatReklas || selectedRiwayatReklas.length === 0">
                                    <div class="p-8 text-center rounded-2xl border border-dashed border-slate-800 space-y-2 bg-slate-950">
                                        <span class="text-3xl block">🔄</span>
                                        <h4 class="text-sm font-bold text-slate-200">Belum Ada Reklasifikasi</h4>
                                        <p class="text-xs text-slate-400 max-w-sm mx-auto">Unit ini belum pernah direklasifikasi antar-kode 108 / KIB.</p>
                                    </div>
                                </template>
                                <template x-if="selectedRiwayatReklas && selectedRiwayatReklas.length > 0">
                                    <div class="space-y-2">
                                        <template x-for="rk in selectedRiwayatReklas" :key="'rk-' + rk.id">
                                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-2">
                                                <div class="flex items-center justify-between text-xs">
                                                    <span class="font-mono font-bold text-emerald-300" x-text="rk.sub_koreksi_label || rk.jenis_reklas"></span>
                                                    <span class="text-slate-400 font-mono text-[11px]" x-text="rk.tanggal || '-'"></span>
                                                </div>
                                                <div class="p-2 bg-slate-900 rounded-xl border border-slate-800/60 flex items-center justify-between text-xs">
                                                    <span class="text-slate-300" x-text="rk.asal_kib || rk.asal_kode || '-'"></span>
                                                    <span class="text-emerald-400 font-bold">➔</span>
                                                    <span class="text-emerald-300 font-bold" x-text="rk.tujuan_kib || rk.tujuan_kode || '-'"></span>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <div class="pt-3 border-t border-slate-800 flex justify-end">
                                <button type="button" @click.stop="showRiwayatModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition-all cursor-pointer">
                                    Tutup Riwayat
                                </button>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </template>
