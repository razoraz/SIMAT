        <!-- ========================================================================= -->
        <!-- MODAL RINCIAN 1: DETAIL POPUP BAST DISTRIBUSI BARANG ASET                  -->
        <!-- ========================================================================= -->
        <div x-show="showDetailDistribusiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 overflow-y-auto" x-cloak>
            <div @click.away="showDetailDistribusiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 my-auto relative">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xl font-bold">
                            🚚
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-teal-400 uppercase tracking-wider block">Rincian BAST Distribusi</span>
                            <h3 class="text-lg font-extrabold text-white" x-text="selectedDetailDistribusi ? selectedDetailDistribusi.nomor_bast : ''"></h3>
                        </div>
                    </div>
                    
                    <button type="button" @click="showDetailDistribusiModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white font-bold">&times;</button>
                </div>

                <template x-if="selectedDetailDistribusi">
                    <div class="space-y-4 text-xs">
                        
                        <!-- Status Bar TTD BSrE & Toggle Button -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Status Tanda Tangan Digital BSrE:</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-extrabold"
                                          :class="selectedDetailDistribusi.signed ? 'text-emerald-400' : 'text-amber-400'"
                                          x-text="selectedDetailDistribusi.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum Ditandatangani'"></span>
                                    <template x-if="selectedDetailDistribusi.signed && selectedDetailDistribusi.tgl_signed && selectedDetailDistribusi.tgl_signed !== '-'">
                                        <span class="text-[11px] font-mono text-emerald-300 font-semibold px-2 py-0.5 rounded-md bg-emerald-500/10 border border-emerald-500/20" x-text="selectedDetailDistribusi.tgl_signed"></span>
                                    </template>
                                </div>
                            </div>

                            <button type="button" @click="toggleSignDistribusi(selectedDetailDistribusi)"
                                :class="selectedDetailDistribusi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-md'"
                                class="px-4 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95">
                                <span x-text="selectedDetailDistribusi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ Tandatangani Digital BSrE'"></span>
                            </button>
                        </div>

                        <!-- Data Informasi Unit & PJ -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-teal-400 font-bold uppercase block">Unit / Paviliun Penerima</span>
                                <div class="font-extrabold text-white text-sm" x-text="selectedDetailDistribusi.unit_nama"></div>
                                <div class="text-[11px] text-slate-400" x-text="selectedDetailDistribusi.unit_tipe"></div>
                                <div class="text-[11px] text-slate-300 pt-1" x-text="'Catatan: ' + selectedDetailDistribusi.keterangan_lokasi"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-emerald-400 font-bold uppercase block">Penanggung Jawab (Sub-Admin)</span>
                                <div class="font-extrabold text-emerald-300 text-sm" x-text="selectedDetailDistribusi.pj_nama"></div>
                                <div class="text-[11px] text-slate-400 font-mono" x-text="'NIP. ' + selectedDetailDistribusi.pj_nip"></div>
                                <div class="text-[11px] text-slate-300 pt-1" x-text="selectedDetailDistribusi.pj_jabatan_ttd"></div>
                            </div>
                        </div>

                        <!-- Tabel Item Barang -->
                        <div>
                            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-2">📦 Rincian Barang Yang Penyerahannya Diberikan:</span>
                            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-950 text-slate-400 font-bold border-b border-slate-800">
                                        <tr>
                                            <th class="px-3 py-2 text-center w-8">No</th>
                                            <th class="px-3 py-2">Nama Barang</th>
                                            <th class="px-3 py-2">Merk / Spesifikasi</th>
                                            <th class="px-3 py-2 text-center">Qty</th>
                                            <th class="px-3 py-2 text-center">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800 bg-slate-900/60">
                                        <template x-for="(sub, idx) in selectedDetailDistribusi.items" :key="idx">
                                            <tr>
                                                <td class="px-3 py-2 text-center text-slate-400 font-mono" x-text="idx + 1"></td>
                                                <td class="px-3 py-2 font-bold text-white" x-text="sub.nama_barang"></td>
                                                <td class="px-3 py-2 text-slate-400" x-text="sub.spesifikasi || sub.merk_type || '-'"></td>
                                                <td class="px-3 py-2 text-center font-bold text-teal-300 font-mono" x-text="(sub.qty_acc !== null && sub.qty_acc !== undefined ? sub.qty_acc : sub.qty) + ' ' + (sub.satuan || 'Unit')"></td>
                                                <td class="px-3 py-2 text-center font-bold text-emerald-400" x-text="sub.kondisi || 'Baik'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center">
                            <button type="button" @click="showDetailDistribusiModal = false; openPrintDistribusi(selectedDetailDistribusi)"
                                class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all flex items-center space-x-1.5">
                                <span>🖨️ Cetak & Edit Surat BAST</span>
                            </button>
                            <button type="button" @click="showDetailDistribusiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                        </div>

                    </div>
                </template>

            </div>
        </div>
