        <!-- ========================================================================= -->
        <!-- MODAL RINCIAN: DETAIL POPUP BAST MUTASI EKSTERNAL (TRANSFER ANTAR-OPD)     -->
        <!-- ========================================================================= -->
        <div x-show="showDetailMutasiEksternalModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 overflow-y-auto" x-cloak>
            <div @click.away="showDetailMutasiEksternalModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 my-auto relative">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xl font-bold">
                            🌐
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider block">Rincian BAST Mutasi Eksternal (Pelimpahan BMD)</span>
                            <h3 class="text-lg font-extrabold text-white" x-text="selectedDetailMutasiEksternal ? (selectedDetailMutasiEksternal.nomor_bast || selectedDetailMutasiEksternal.kode) : ''"></h3>
                        </div>
                    </div>
                    
                    <button type="button" @click="showDetailMutasiEksternalModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white font-bold">&times;</button>
                </div>

                <template x-if="selectedDetailMutasiEksternal">
                    <div class="space-y-4 text-xs">
                        
                        <!-- Status Bar TTD BSrE & Toggle Button -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div class="flex items-center space-x-3">
                                <div>
                                    <span class="text-[10px] text-slate-400 font-semibold block uppercase">Status TTE BSrE (Sertifikat Digital):</span>
                                    <span class="text-sm font-extrabold"
                                          :class="selectedDetailMutasiEksternal.signed ? 'text-emerald-400' : 'text-amber-400'"
                                          x-text="selectedDetailMutasiEksternal.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum Ditandatangani'"></span>
                                </div>
                                <div class="hidden sm:block border-l border-slate-800 h-8"></div>
                                <div class="hidden sm:block">
                                    <span class="text-[10px] text-slate-400 font-semibold block uppercase">Tipe Mutasi:</span>
                                    <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold"
                                          :class="selectedDetailMutasiEksternal.tipe === 'keluar' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30'"
                                          x-text="selectedDetailMutasiEksternal.tipe === 'keluar' ? '📤 Mutasi Keluar' : '📥 Pelimpahan Masuk'"></span>
                                </div>
                            </div>

                            <button type="button" @click="toggleSignMutasiEksternal(selectedDetailMutasiEksternal)"
                                :class="selectedDetailMutasiEksternal.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-md'"
                                class="px-4 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95 cursor-pointer">
                                <span x-text="selectedDetailMutasiEksternal.signed ? '↩️ Batalkan TTD BSrE' : '✍️ Tandatangani Digital BSrE'"></span>
                            </button>
                        </div>

                        <!-- Data Informasi Pihak Pertama & Kedua -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-cyan-400 font-bold uppercase block">Pihak 1 (OPD Pengirim)</span>
                                <div class="font-extrabold text-white text-sm" x-text="selectedDetailMutasiEksternal.opd_asal"></div>
                                <div class="text-[11px] text-cyan-300 font-semibold" x-text="selectedDetailMutasiEksternal.pj_asal_nama"></div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="'NIP: ' + selectedDetailMutasiEksternal.pj_asal_nip"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-indigo-400 font-bold uppercase block">Pihak 2 (RSUD Penerima)</span>
                                <div class="font-extrabold text-indigo-300 text-sm" x-text="'RSUD dr. H. Koesnandi (' + (selectedDetailMutasiEksternal.ruangan_tujuan || 'Gudang/Ruangan') + ')'"></div>
                                <div class="text-[11px] text-emerald-300 font-semibold" x-text="selectedDetailMutasiEksternal.pj_tujuan_nama"></div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="'NIP: ' + selectedDetailMutasiEksternal.pj_tujuan_nip"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-purple-400 font-bold uppercase block">Dasar Hukum & Nilai</span>
                                <div class="font-extrabold text-purple-300 text-sm" x-text="selectedDetailMutasiEksternal.nilai_perolehan_format"></div>
                                <div class="text-[11px] text-slate-300" x-text="'SK: ' + (selectedDetailMutasiEksternal.nomor_sk_dasar || '-')"></div>
                                <div class="text-[10px] text-slate-400" x-text="'Tgl: ' + selectedDetailMutasiEksternal.tgl_bast"></div>
                            </div>
                        </div>

                        <!-- Tabel Rincian Register / Satuan Barang -->
                        <div class="rounded-2xl border border-slate-800 overflow-hidden bg-slate-950">
                            <div class="p-3 border-b border-slate-800 bg-slate-900/50 flex justify-between items-center">
                                <span class="font-bold text-white uppercase text-[11px] tracking-wider">Daftar Barang & NIBAR Terkait:</span>
                                <span class="text-[11px] font-mono text-cyan-400 font-semibold" x-text="'Total: ' + selectedDetailMutasiEksternal.vol + ' ' + selectedDetailMutasiEksternal.satuan"></span>
                            </div>
                            <div class="max-h-48 overflow-y-auto custom-scrollbar">
                                <table class="w-full text-left">
                                    <thead class="bg-slate-900 text-slate-400 font-bold border-b border-slate-800 text-[11px]">
                                        <tr>
                                            <th class="p-2.5 text-center w-10">No</th>
                                            <th class="p-2.5">Nama Barang / Spesifikasi</th>
                                            <th class="p-2.5">NIBAR</th>
                                            <th class="p-2.5 text-center">Kondisi</th>
                                            <th class="p-2.5 text-right">Nilai Aset</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800 text-[11px]">
                                        <template x-for="(it, idx) in selectedDetailMutasiEksternal.items" :key="idx">
                                            <tr class="hover:bg-slate-900/40">
                                                <td class="p-2.5 text-center text-slate-500 font-bold" x-text="idx + 1"></td>
                                                <td class="p-2.5">
                                                    <span class="font-bold text-white block" x-text="it.nama_barang"></span>
                                                    <span class="text-[10px] text-slate-500 font-mono" x-text="it.kode_108"></span>
                                                </td>
                                                <td class="p-2.5 font-mono text-cyan-400" x-text="it.nibar || '-'"></td>
                                                <td class="p-2.5 text-center">
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold"
                                                          :class="it.kondisi === 'Baik' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/20 text-amber-300'"
                                                          x-text="it.kondisi"></span>
                                                </td>
                                                <td class="p-2.5 text-right font-mono text-slate-300" x-text="'Rp ' + formatNumber(it.nilai_aset || it.harga_satuan)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Catatan / Keterangan Alasan Mutasi & Dokumen Lampiran -->
                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Keterangan / Alasan Pelimpahan:</span>
                                <p class="text-slate-300 italic text-[11px] mt-0.5" x-text="selectedDetailMutasiEksternal.alasan_mutasi || 'Tidak ada catatan tambahan.'"></p>
                            </div>

                            <template x-if="selectedDetailMutasiEksternal.dokumen_lampiran_url">
                                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                                    <span class="text-[11px] text-slate-400">Scan Fisik BAST / Dokumen Legal:</span>
                                    <a :href="selectedDetailMutasiEksternal.dokumen_lampiran_url" target="_blank"
                                       class="px-3 py-1 rounded-xl bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-300 border border-indigo-500/40 font-bold text-[11px] transition-all inline-flex items-center space-x-1">
                                        <span>📄</span>
                                        <span>Buka File Scan Asli</span>
                                    </a>
                                </div>
                            </template>
                        </div>

                        <!-- Footer Modal Actions -->
                        <div class="flex items-center justify-end space-x-2 pt-2 border-t border-slate-800">
                            <button type="button" @click="showDetailMutasiEksternalModal = false"
                                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold transition-all">
                                Tutup
                            </button>
                            <a :href="'/mutasi-eksternal/' + (selectedDetailMutasiEksternal.mutasi_id || selectedDetailMutasiEksternal.id) + '/cetak?returnTo=' + encodeURIComponent('/berita-acara?tab=mutasi&scope=eksternal')"
                                target="_blank"
                                class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold transition-all flex items-center space-x-1.5 shadow-lg shadow-purple-500/20 active:scale-95 cursor-pointer">
                                <span>🖨️ Cetak Lembar BAST</span>
                            </a>
                        </div>

                    </div>
                </template>

            </div>
        </div>
