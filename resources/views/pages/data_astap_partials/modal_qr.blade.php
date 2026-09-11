        <!-- FRONTEND MODAL: PRATINJAU & DOWNLOAD QR CODE -->
        <div x-show="showQrModal" x-cloak @click.self="showQrModal = false" class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999;">
            <div class="border border-emerald-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl text-center space-y-5 max-h-[90vh] overflow-y-auto my-auto" style="background-color: #0f172a;">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">📱</span>
                        <h3 class="text-base font-extrabold text-white">Label QR Code Aset ASTAP</h3>
                    </div>
                    <button type="button" @click.stop="showQrModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <template x-if="selectedQrItem">
                    <div class="space-y-4">
                        <!-- Gambar QR Code yang Berisi URL Publik (Bisa Di-scan HP Tanpa Login) -->
                        <div class="p-4 bg-white rounded-2xl inline-block shadow-lg border-2 border-emerald-500/40">
                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=280x280&margin=10&data=' + encodeURIComponent(getQrPayloadUrl(selectedQrItem))"
                                 :alt="selectedQrItem.nama_barang"
                                 class="w-52 h-52 mx-auto object-contain" />
                        </div>

                        <!-- Kartu Informasi Detail Barang Sesuai QR Code -->
                        <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 text-left space-y-2 text-xs">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">📦 Nama Barang:</span>
                                <span class="text-white font-bold text-right max-w-[200px] truncate" x-text="selectedQrItem.nama_barang"></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">🏷️ NIBAR / Kode:</span>
                                <span class="text-emerald-400 font-bold font-mono text-right text-[11px]" x-text="selectedQrItem.kode_barang"></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">📅 Tahun Perolehan:</span>
                                <span class="text-slate-200 font-bold font-mono" x-text="selectedQrItem.tahun_perolehan"></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">📍 Penempatan Ruangan:</span>
                                <span class="text-teal-300 font-bold text-right max-w-[190px] truncate" x-text="selectedQrItem.ruang_pemegang"></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">⚙️ Kondisi Aset:</span>
                                <span class="text-emerald-300 font-extrabold" x-text="selectedQrItem.kondisi"></span>
                            </div>
                            <div class="flex items-start justify-between pt-0.5">
                                <span class="text-slate-400 font-semibold text-[10.5px] shrink-0 mr-2">🛠️ Riwayat Perbaikan:</span>
                                <span class="text-amber-300 font-medium text-right text-[10.5px]" x-text="selectedQrItem.riwayat_servis"></span>
                            </div>
                        </div>

                        <!-- Box URL Publik Scan -->
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-left space-y-1.5">
                            <span class="text-[9.5px] font-bold text-slate-500 uppercase tracking-wider block">🔗 URL Publik Terenkripsi QR Code (Tanpa Login):</span>
                            <a :href="getQrPayloadUrl(selectedQrItem)" target="_blank"
                               class="font-mono text-[10.5px] text-cyan-400 hover:underline block truncate" x-text="getQrPayloadUrl(selectedQrItem)"></a>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 pt-1">
                            <button type="button" @click="downloadQrImage()"
                                class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center space-x-2 active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh QR</span>
                            </button>
                            <a :href="getQrPayloadUrl(selectedQrItem)" target="_blank"
                               class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 font-bold text-xs transition-all flex items-center justify-center space-x-1.5">
                                <span>🌐 Buka Halaman Scan</span>
                            </a>
                            <button type="button" @click="showQrModal = false"
                                class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- FRONTEND MODAL: UBAH KONDISI UNIT BARANG (KHUSUS KONDISI) -->
        <div x-show="showEditKondisiModal" x-cloak @click.self="showEditKondisiModal = false" class="fixed inset-0 flex items-center justify-center p-4" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999;">
            <div class="border border-amber-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" style="background-color: #0f172a;">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">⚙️</span>
                        <h3 class="text-base font-extrabold text-white">Ubah Kondisi Unit Barang</h3>
                    </div>
                    <button type="button" @click.stop="showEditKondisiModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <template x-if="editingRegisterItem">
                    <div class="space-y-4 text-xs">
                        <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Target Unit NIBAR:</span>
                            <p class="text-emerald-400 font-mono font-bold text-sm" x-text="editingRegisterItem.nibar || editingRegisterItem.no_register"></p>
                            <p class="text-slate-300 font-semibold text-[11px]" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : ''"></p>
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

        <!-- FRONTEND MODAL: CEK RIWAYAT MUTASI REGISTER NIBAR -->
        <div x-show="showRiwayatModal" x-cloak @click.self="showRiwayatModal = false" class="fixed inset-0 flex items-center justify-center p-4" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999;">
            <div class="border border-purple-500/40 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[88vh] overflow-y-auto" style="background-color: #0f172a;">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Riwayat Mutasi Unit Barang</h3>
                            <p class="text-[11px] text-slate-400">Histori pergerakan, tanggal, kondisi saat mutasi, dan alasan mutasi</p>
                        </div>
                    </div>
                    <button type="button" @click.stop="showRiwayatModal = false" class="text-slate-500 hover:text-white text-xl font-bold p-1 cursor-pointer">&times;</button>
                </div>

                <template x-if="selectedRiwayatRegister">
                    <div class="space-y-4 text-xs">
                        <!-- Info Register Card -->
                        <div class="p-3.5 rounded-2xl border border-slate-800 flex items-center justify-between gap-3" style="background-color: #020617;">
                            <div class="space-y-0.5 min-w-0 flex-1">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block truncate" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : 'Unit Barang'"></span>
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

                        <!-- Loading State -->
                        <template x-if="isLoadingRiwayat">
                            <div class="py-8 text-center space-y-2">
                                <div class="inline-block w-6 h-6 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-xs text-slate-400">Memuat riwayat mutasi barang...</p>
                            </div>
                        </template>

                        <!-- Empty State: Belum Pernah Mutasi -->
                        <template x-if="!isLoadingRiwayat && (!selectedRiwayatMutasis || selectedRiwayatMutasis.length === 0)">
                            <div class="p-6 text-center rounded-2xl border border-dashed border-slate-800 space-y-2" style="background-color: #020617;">
                                <span class="text-3xl block">📦</span>
                                <h4 class="text-sm font-bold text-slate-200">Belum Ada Riwayat Mutasi</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                                    Unit register ini belum pernah dimutasi ke ruangan atau unit lain. Unit saat ini berada di lokasi penempatan: <strong class="text-slate-300" x-text="selectedRiwayatRegister.ruang_pemegang || 'Gudang Aset'"></strong> dengan kondisi <strong class="text-emerald-400" x-text="selectedRiwayatRegister.kondisi"></strong>.
                                </p>
                            </div>
                        </template>

                        <!-- List Riwayat Mutasi Timeline -->
                        <template x-if="!isLoadingRiwayat && selectedRiwayatMutasis && selectedRiwayatMutasis.length > 0">
                            <div class="space-y-3 relative pl-4 border-l-2 border-purple-500/30 my-2">
                                <template x-for="(m, idx) in selectedRiwayatMutasis" :key="m.id || idx">
                                    <div class="relative group">
                                        <!-- Timeline dot -->
                                        <div class="absolute -left-[21px] top-2 w-2.5 h-2.5 rounded-full bg-purple-500 ring-4 ring-slate-900"></div>
                                        
                                        <div class="p-4 rounded-2xl border border-slate-800 space-y-2.5 hover:border-slate-700 transition-colors" style="background-color: #020617;">
                                            <!-- Row 1: Tanggal Mutasi, Jenis Mutasi, & Kondisi Saat Itu -->
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs font-mono font-extrabold text-white flex items-center space-x-1">
                                                        <span>📅</span>
                                                        <span x-text="m.tanggal_mutasi"></span>
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30" x-text="m.jenis_mutasi || 'Mutasi'"></span>
                                                    <span class="text-[10px] font-mono text-slate-400" x-text="'(' + (m.nomor_bamb || 'BAMB') + ')'"></span>
                                                </div>

                                                <!-- KONDISI SAAT ITU (HIGHLIGHTED) -->
                                                <div class="flex items-center space-x-1.5">
                                                    <span class="text-[10px] text-slate-400 font-bold uppercase">Kondisi saat mutasi:</span>
                                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black border"
                                                          :class="{
                                                              'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': m.kondisi === 'Baik',
                                                              'bg-amber-500/20 text-amber-300 border-amber-500/30': m.kondisi === 'Kurang Baik',
                                                              'bg-rose-500/20 text-rose-300 border-rose-500/30': m.kondisi === 'Rusak Berat'
                                                          }" x-text="m.kondisi || 'Baik'"></span>
                                                </div>
                                            </div>

                                            <!-- Row 2: Alur Perpindahan Ruangan -->
                                            <div class="p-2.5 bg-slate-900/80 rounded-xl border border-slate-800/80 flex items-center justify-between text-xs">
                                                <div class="flex items-center space-x-2 min-w-0">
                                                    <div class="text-slate-300">
                                                        <span class="text-[9.5px] uppercase font-bold text-slate-500 block">Dari Ruangan:</span>
                                                        <span class="font-semibold text-slate-200" x-text="m.ruangan_asal"></span>
                                                    </div>
                                                    <span class="text-purple-400 font-extrabold text-sm px-1">➔</span>
                                                    <div class="text-cyan-300">
                                                        <span class="text-[9.5px] uppercase font-bold text-slate-500 block">Ke Ruangan:</span>
                                                        <span class="font-bold text-cyan-300" x-text="m.ruangan_tujuan"></span>
                                                    </div>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border"
                                                      :class="m.status && m.status.includes('Disetujui') ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : (m.status && m.status.includes('Ditolak') ? 'bg-rose-500/10 text-rose-300 border-rose-500/30' : 'bg-cyan-500/10 text-cyan-300 border-cyan-500/30')"
                                                      x-text="m.status || 'Tercatat'"></span>
                                            </div>

                                            <!-- Row 3: ALASAN MUTASI (CLEAR & PROMINENT) -->
                                            <div class="p-3 bg-slate-900/95 rounded-xl border border-amber-500/20 space-y-1">
                                                <div class="flex items-center space-x-1.5 text-amber-400">
                                                    <span class="text-xs">📝</span>
                                                    <span class="text-[10px] font-extrabold uppercase tracking-wider">Alasan Mutasi:</span>
                                                </div>
                                                <p class="text-xs text-slate-200 leading-relaxed italic pl-1" x-text="m.alasan_mutasi || 'Tidak ada alasan khusus dicatat'"></p>
                                            </div>

                                            <!-- Row 4: Info Penanggung Jawab -->
                                            <div class="flex flex-wrap items-center justify-between text-[10px] text-slate-400 pt-0.5 px-1 border-t border-slate-800/60">
                                                <span>Pengirim: <strong class="text-slate-300 font-semibold" x-text="m.penanggung_jawab_asal || '-'"></strong></span>
                                                <span>Penerima: <strong class="text-slate-300 font-semibold" x-text="m.penanggung_jawab_tujuan || '-'"></strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="pt-3 border-t border-slate-800 flex justify-end">
                            <button type="button" @click.stop="showRiwayatModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs cursor-pointer">
                                Tutup Riwayat
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
