        <!-- MODAL DETAIL RINCIAN DISTRIBUSI BARANG (MENDUKUNG MULTI-BARANG & NIBAR REGISTER) -->
        <div x-show="showDetailModal" class="no-print fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="if (!showConfirmModal) showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <span class="p-2.5 rounded-2xl bg-teal-500/20 text-teal-300 text-xl border border-teal-500/30">🚚</span>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-white">Detail Alokasi Penyerahan & Register NIBAR</h3>
                            <p class="text-xs text-slate-400 font-mono" x-text="selectedDistribusi ? ('Nomor Registrasi: ' + selectedDistribusi.kode + (selectedDistribusi.status === 'Ditolak' ? ' • BAST: (tidak diterbitkan)' : (selectedDistribusi.status === 'Menunggu Konfirmasi' ? ' • BAST: (Menunggu Konfirmasi)' : (' • BAST: ' + (selectedDistribusi.bast_nomor || selectedDistribusi.nomor_bast || '(Menunggu Konfirmasi)'))))) : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <template x-if="selectedDistribusi">
                    <div class="space-y-5 text-xs">
                        
                        <!-- Informasi Ringkas Transaksi -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 bg-slate-950 p-4 sm:p-5 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Tujuan Unit / Ruangan</span>
                                <span class="font-bold text-teal-300 text-xs block mt-0.5" x-text="selectedDistribusi.tujuan"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Pegawai Penerima (PJ)</span>
                                <span class="font-bold text-white text-xs block mt-0.5" x-text="selectedDistribusi.penerima || selectedDistribusi.pj_nama"></span>
                                <span class="text-[10px] text-slate-400 font-mono" x-text="selectedDistribusi.pj_nip ? ('NIP: ' + selectedDistribusi.pj_nip) : ''"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Tanggal Pengajuan</span>
                                <span class="font-mono text-slate-300 text-xs block mt-0.5" x-text="selectedDistribusi.tgl || selectedDistribusi.tanggal_distribusi"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Status Penyerahan</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold border mt-0.5"
                                      :class="{
                                          'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': selectedDistribusi.status === 'Telah Diterima' || selectedDistribusi.status === 'Diterima',
                                          'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': selectedDistribusi.status === 'Dalam Pengiriman' || selectedDistribusi.status === 'Dikirim',
                                          'bg-amber-500/15 text-amber-300 border-amber-500/30': selectedDistribusi.status === 'Menunggu Konfirmasi' || selectedDistribusi.status === 'Pending',
                                          'bg-rose-500/15 text-rose-300 border-rose-500/30': selectedDistribusi.status === 'Ditolak',
                                          'bg-slate-500/15 text-slate-300 border-slate-500/30': selectedDistribusi.status === 'Draft' || !selectedDistribusi.status
                                      }"
                                      x-text="selectedDistribusi.status"></span>
                            </div>
                            <div class="col-span-2 sm:col-span-4 pt-2 border-t border-slate-900 flex items-center justify-between">
                                <div class="text-slate-400 text-[11px]">
                                    <span class="font-bold text-slate-300">Catatan:</span>
                                    <span class="italic ml-1" x-text="selectedDistribusi.keterangan || '-'"></span>
                                </div>
                                <template x-if="selectedDistribusi.signed">
                                    <span class="inline-flex items-center space-x-1 text-[10px] text-emerald-400 font-mono bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                                        <span>✍️ E-Sign BSrE Sah:</span>
                                        <span x-text="selectedDistribusi.tgl_signed || 'Terverifikasi'"></span>
                                    </span>
                                </template>
                            </div>

                            <!-- Banner Alasan Penolakan jika Status Ditolak -->
                            <template x-if="selectedDistribusi.status === 'Ditolak' && selectedDistribusi.alasan_penolakan">
                                <div class="col-span-2 sm:col-span-4 p-3 bg-rose-950/40 rounded-xl border border-rose-500/30">
                                    <span class="text-[11px] font-bold text-rose-300 block mb-1">🚫 Alasan Penolakan:</span>
                                    <p class="text-xs text-rose-200/90 leading-relaxed" x-text="selectedDistribusi.alasan_penolakan"></p>
                                </div>
                            </template>
                        </div>

                        <!-- Tabel Rincian Semua Barang & Register NIBAR yang Didistribusikan -->
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-slate-200 font-extrabold text-xs flex items-center space-x-2">
                                    <span>📦 Rincian Barang & Nomor NIBAR 45 Karakter</span>
                                    <span class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold"
                                          x-text="(selectedDistribusi.items ? selectedDistribusi.items.length : 0) + ' Jenis Barang'"></span>
                                </span>
                                <span class="text-[11px] text-emerald-400 font-bold bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20"
                                      x-text="'Total Volume: ' + getTotalQty(selectedDistribusi.items) + ' Item'"></span>
                            </div>

                            <div class="overflow-x-auto overflow-y-auto rounded-2xl border border-slate-800 bg-slate-950 shadow-inner" style="max-height: 380px; overflow-y: auto;">
                                <table class="w-full text-left text-xs text-slate-300">
                                    <thead class="bg-slate-900 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800 shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #0f172a;">
                                        <tr>
                                            <th class="px-3.5 py-3 text-center w-8">No</th>
                                            <th class="px-3.5 py-3 text-center min-w-[180px]">Nama Barang & Kode 108</th>
                                            <th class="px-3.5 py-3 text-center min-w-[140px]">Merk / Spesifikasi</th>
                                            <th class="px-3.5 py-3 text-center min-w-[300px]">Nomor Register NIBAR (45 Digit)</th>
                                            <th class="px-3.5 py-3 text-center w-24">Kondisi</th>
                                            <th class="px-3.5 py-3 text-center w-24">Vol. Pengajuan</th>
                                            <th class="px-3.5 py-3 text-center w-24">Vol. ACC</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/80">
                                        <template x-for="(item, idx) in selectedDistribusi.items" :key="idx">
                                            <tr class="hover:bg-slate-900/40 transition-colors">
                                                <td class="px-3.5 py-3 text-center font-bold text-slate-500 align-top" x-text="idx + 1"></td>
                                                
                                                <!-- Nama Barang & Kode 108 -->
                                                <td class="px-3.5 py-3 align-top text-center">
                                                    <p class="font-extrabold text-white text-xs leading-snug" x-text="item.nama_barang"></p>
                                                    <p class="text-[10px] text-teal-400 font-mono mt-0.5" x-text="'Kode 108: ' + (item.kode_barang || '-')"></p>
                                                    <template x-if="item.jenis_nama">
                                                        <span class="text-[9.5px] text-slate-500 block truncate max-w-xs" x-text="item.jenis_nama"></span>
                                                    </template>
                                                </td>

                                                <!-- Merk & Spesifikasi -->
                                                <td class="px-3.5 py-3 text-slate-300 text-[11px] align-top text-center">
                                                    <span class="font-semibold text-slate-200" x-text="item.merk_type || item.merk || '-'"></span>
                                                </td>

                                                <!-- Kolom NIBAR -->
                                                <td class="px-3.5 py-3 align-top text-center">
                                                    <!-- Jika status transaksi Ditolak -->
                                                    <template x-if="selectedDistribusi && selectedDistribusi.status === 'Ditolak'">
                                                        <div class="inline-flex items-center h-[32px] space-x-1.5 px-3 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-300 text-[10.5px] font-mono font-bold">
                                                            <span>❌</span>
                                                            <span>Ditolak</span>
                                                        </div>
                                                    </template>

                                                    <template x-if="!selectedDistribusi || selectedDistribusi.status !== 'Ditolak'">
                                                        <div>
                                                            <!-- Ada nibar_registers -->
                                                            <template x-if="item.nibar_registers && item.nibar_registers.length > 0">
                                                                <div class="flex flex-col gap-3">
                                                                    <template x-for="(reg, nIdx) in item.nibar_registers" :key="reg.nibar || nIdx">
                                                                        <div class="flex items-center h-[32px] bg-slate-800/90 hover:bg-slate-800 border border-slate-700/60 hover:border-teal-500/50 rounded-lg px-3 transition-all group">
                                                                            <span class="text-slate-400 font-mono text-[10px] font-semibold shrink-0 mr-2" x-text="'#' + (nIdx + 1)"></span>
                                                                            <a :href="'/scan/' + reg.nibar" target="_blank"
                                                                               class="font-mono text-[11px] font-bold text-teal-300 group-hover:text-teal-200 group-hover:underline tracking-tight select-all truncate"
                                                                               title="Buka Detail Barang Aset"
                                                                               x-text="reg.nibar">
                                                                            </a>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </template>

                                                            <!-- Fallback: nibar_list -->
                                                            <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && item.nibar_list && item.nibar_list.length > 0">
                                                                <div class="flex flex-col gap-3">
                                                                    <template x-for="(nibar, nIdx) in item.nibar_list" :key="nIdx">
                                                                        <div class="flex items-center h-[32px] bg-slate-800/90 hover:bg-slate-800 border border-slate-700/60 hover:border-teal-500/50 rounded-lg px-3 transition-all group">
                                                                            <span class="text-slate-400 font-mono text-[10px] font-semibold shrink-0 mr-2" x-text="'#' + (nIdx + 1)"></span>
                                                                            <a :href="'/scan/' + nibar" target="_blank"
                                                                               class="font-mono text-[11px] font-bold text-teal-300 group-hover:text-teal-200 group-hover:underline tracking-tight select-all truncate"
                                                                               title="Buka Detail Barang Aset"
                                                                               x-text="nibar">
                                                                            </a>
                                                                        </div>
                                                                    </template>
                                                                </div>
                                                            </template>

                                                            <!-- Belum ada NIBAR -->
                                                            <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && (!item.nibar_list || item.nibar_list.length === 0)">
                                                                <div class="inline-flex items-center h-[32px] space-x-1.5 px-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[10.5px] font-mono">
                                                                    <span>⚠️</span>
                                                                    <span>Belum ditentukan</span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </td>

                                                <!-- Kolom Kondisi -->
                                                <td class="px-2 py-3 align-top">
                                                    <template x-if="item.nibar_registers && item.nibar_registers.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(reg, kIdx) in item.nibar_registers" :key="'k-' + (reg.nibar || kIdx)">
                                                                <div class="flex items-center justify-center h-[32px]">
                                                                    <span class="h-[32px] px-3 flex items-center justify-center rounded-lg text-[10px] font-bold whitespace-nowrap"
                                                                          :class="{
                                                                              'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30': reg.kondisi === 'Baik' || !reg.kondisi,
                                                                              'bg-amber-500/15 text-amber-300 border border-amber-500/30': reg.kondisi === 'Kurang Baik',
                                                                              'bg-orange-500/15 text-orange-300 border border-orange-500/30': reg.kondisi === 'Rusak Ringan',
                                                                              'bg-rose-500/15 text-rose-300 border border-rose-500/30': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'Rusak'
                                                                          }"
                                                                          x-text="reg.kondisi || 'Baik'"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="!item.nibar_registers || item.nibar_registers.length === 0">
                                                        <div class="flex items-center justify-center h-[32px]">
                                                            <span class="text-slate-500 font-mono text-xs font-bold">-</span>
                                                        </div>
                                                    </template>
                                                </td>

                                                <!-- Vol. Pengajuan -->
                                                <td class="px-3.5 py-3 text-center align-top whitespace-nowrap">
                                                    <span class="font-bold text-teal-300 text-xs font-mono" x-text="item.qty"></span>
                                                    <span class="text-slate-400 text-[10px] ml-0.5" x-text="item.satuan"></span>
                                                </td>

                                                <!-- Vol. ACC -->
                                                <td class="px-3.5 py-3 text-center align-top whitespace-nowrap">
                                                    <!-- Jika status transaksi Ditolak -->
                                                    <template x-if="selectedDistribusi && selectedDistribusi.status === 'Ditolak'">
                                                        <div class="flex items-center justify-center h-[32px]">
                                                            <span class="text-[10px] font-bold text-rose-300 bg-rose-500/10 px-2.5 py-1 rounded-lg border border-rose-500/30 whitespace-nowrap inline-flex items-center space-x-1">
                                                                <span>❌</span>
                                                                <span>Ditolak</span>
                                                            </span>
                                                        </div>
                                                    </template>

                                                    <template x-if="!selectedDistribusi || selectedDistribusi.status !== 'Ditolak'">
                                                        <div>
                                                            <template x-if="item.qty_acc !== null && item.qty_acc !== undefined">
                                                                <div>
                                                                    <template x-if="item.qty_acc > 0">
                                                                        <span class="inline-block whitespace-nowrap">
                                                                            <span class="font-bold text-emerald-400 text-xs font-mono" x-text="item.qty_acc"></span>
                                                                            <span class="text-slate-400 text-[10px] ml-0.5" x-text="item.satuan"></span>
                                                                        </span>
                                                                    </template>
                                                                    <template x-if="item.qty_acc <= 0">
                                                                        <span class="text-[10px] font-bold text-slate-400 bg-slate-800/80 px-2 py-0.5 rounded border border-slate-700/80 whitespace-nowrap">✕ Tidak di-ACC</span>
                                                                    </template>
                                                                </div>
                                                            </template>
                                                            <template x-if="item.qty_acc === null || item.qty_acc === undefined">
                                                                <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20 whitespace-nowrap">⏳ Belum ACC</span>
                                                            </template>
                                                        </div>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                    <div class="text-[11px] text-slate-500 font-mono">
                        <span>Format NIBAR: 45 Digit Kode BMD RSUD dr. H. Koesnandi</span>
                    </div>
                    <div class="flex items-center space-x-2">
                        <!-- Tombol Barang Diterima (Hanya muncul ketika status Dalam Pengiriman) -->
                        <template x-if="selectedDistribusi && ['Dalam Pengiriman', 'Dikirim'].includes(selectedDistribusi.status)">
                            <button type="button"
                                    @click="confirmKonfirmasiDiterima(selectedDistribusi)"
                                    class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs transition-all shadow-md active:scale-95 inline-flex items-center space-x-1.5 cursor-pointer">
                                <svg class="w-4 h-4 text-slate-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Barang Diterima</span>
                            </button>
                        </template>

                        <!-- Tombol Tolak (Hanya admin, hilang ketika status Dalam Pengiriman / Telah Diterima / Ditolak atau jika ada minimal 1 NIBAR yang diinput) -->
                        <template x-if="userRole !== 'sub_admin' && selectedDistribusi && !['Dalam Pengiriman', 'Dikirim', 'Telah Diterima', 'Diterima', 'Ditolak'].includes(selectedDistribusi.status) && !hasAccNibar(selectedDistribusi)">
                            <button type="button"
                                    @click="confirmTolakDistribusi(selectedDistribusi)"
                                    class="px-4 py-2.5 rounded-xl bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40 font-extrabold text-xs transition-all shadow-md active:scale-95 inline-flex items-center space-x-1.5 cursor-pointer">
                                <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                                <span>Tolak</span>
                            </button>
                        </template>

                        <!-- Tombol Setuju (Hanya admin, langsung arahkan ke halaman edit distribusi) -->
                        <template x-if="userRole !== 'sub_admin' && selectedDistribusi && !['Dalam Pengiriman', 'Dikirim', 'Telah Diterima', 'Diterima', 'Ditolak'].includes(selectedDistribusi.status)">
                            <a :href="selectedDistribusi ? ('/distribusi/' + selectedDistribusi.id + '/edit') : '#'"
                               class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs transition-all shadow-md active:scale-95 inline-flex items-center space-x-1.5 cursor-pointer">
                                <svg class="w-4 h-4 text-slate-950 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                                </svg>
                                <span>Setuju</span>
                            </a>
                        </template>

                        <a :href="selectedDistribusi ? ('/berita-acara?tab=distribusi&id=' + selectedDistribusi.id + '&returnTo=' + encodeURIComponent('/distribusi?openDetail=' + selectedDistribusi.id)) : '#'"
                           x-show="userRole !== 'sub_admin' && selectedDistribusi && selectedDistribusi.status !== 'Ditolak' && (['Dalam Pengiriman', 'Dikirim', 'Telah Diterima', 'Diterima'].includes(selectedDistribusi.status) || hasAccNibar(selectedDistribusi))"
                           class="px-4 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs transition-all shadow-md active:scale-95 inline-flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>🖨️ Cetak / Edit BAST</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
