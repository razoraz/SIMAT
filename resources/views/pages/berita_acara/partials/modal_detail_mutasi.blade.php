        <!-- ========================================================================= -->
        <!-- MODAL RINCIAN 2: DETAIL POPUP BAST MUTASI ASET                            -->
        <!-- ========================================================================= -->
        <div x-show="showDetailMutasiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 overflow-y-auto" x-cloak>
            <div @click.away="showDetailMutasiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 my-auto relative">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xl font-bold">
                            🔄
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider block">Rincian BAST Mutasi Aset</span>
                            <h3 class="text-lg font-extrabold text-white" x-text="selectedDetailMutasi ? selectedDetailMutasi.nomor_bast : ''"></h3>
                        </div>
                    </div>
                    
                    <button type="button" @click="showDetailMutasiModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white font-bold">&times;</button>
                </div>

                <template x-if="selectedDetailMutasi">
                    <div class="space-y-4 text-xs">
                        
                        <!-- Status Bar TTD BSrE & Toggle Button -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Status Tanda Tangan Digital BSrE:</span>
                                <span class="text-sm font-extrabold"
                                      :class="selectedDetailMutasi.signed ? 'text-emerald-400' : 'text-amber-400'"
                                      x-text="selectedDetailMutasi.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum Ditandatangani'"></span>
                            </div>

                            <button type="button" @click="toggleSignMutasi(selectedDetailMutasi)"
                                :class="selectedDetailMutasi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-md'"
                                class="px-4 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95">
                                <span x-text="selectedDetailMutasi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ Tandatangani Digital BSrE'"></span>
                            </button>
                        </div>

                        <!-- Data Informasi Mutasi Ruangan -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Ruangan Asal</span>
                                <div class="font-extrabold text-white text-sm" x-text="selectedDetailMutasi.asal"></div>
                                <div class="text-[11px] text-purple-300" x-text="'PJ: ' + selectedDetailMutasi.pj_asal_nama"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-rose-400 font-bold uppercase block">Ruangan Tujuan</span>
                                <div class="font-extrabold text-rose-300 text-sm" x-text="selectedDetailMutasi.tujuan"></div>
                                <div class="text-[11px] text-emerald-300" x-text="'PJ: ' + selectedDetailMutasi.pj_tujuan_nama"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-cyan-400 font-bold uppercase block">Jenis Mutasi</span>
                                <div class="font-extrabold text-cyan-300 text-sm" x-text="selectedDetailMutasi.jenis_mutasi || selectedDetailMutasi.jenis || 'Mutasi'"></div>
                                <div class="text-[11px] text-slate-400" x-text="'Alasan: ' + (selectedDetailMutasi.keterangan || '-')"></div>
                            </div>
                        </div>

                        <!-- Info Barang Dimutasi -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                            <span class="text-[10px] text-cyan-400 font-bold uppercase block">Barang Aset Yang Dimutasi</span>
                            <template x-if="selectedDetailMutasi.items && selectedDetailMutasi.items.length > 0">
                                <div class="space-y-2">
                                    <template x-for="(sub, idx) in selectedDetailMutasi.items" :key="idx">
                                        <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between text-xs">
                                            <div>
                                                <div class="font-bold text-white" x-text="(idx + 1) + '. ' + sub.nama_barang"></div>
                                                <div class="text-[10px] text-slate-400 font-mono" x-text="'NIBAR: ' + (sub.nibar || '-') + ' • Kode 108: ' + sub.kode_barang"></div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-semibold text-[10px]" x-text="'Kondisi: ' + sub.kondisi"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!selectedDetailMutasi.items || selectedDetailMutasi.items.length === 0">
                                <div>
                                    <div class="font-extrabold text-white text-base" x-text="selectedDetailMutasi.nama"></div>
                                    <div class="flex flex-wrap gap-3 font-mono text-[11px] text-slate-400">
                                        <span>Kode 108: <strong class="text-cyan-300" x-text="selectedDetailMutasi.kode_barang"></strong></span>
                                        <span>Volume: <strong class="text-white" x-text="selectedDetailMutasi.qty + ' ' + selectedDetailMutasi.satuan"></strong></span>
                                    </div>
                                </div>
                            </template>
                            <div class="text-slate-300 text-[11px] pt-1" x-text="'Alasan Pemindahan: ' + selectedDetailMutasi.keterangan"></div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center">
                            <button type="button" @click="showDetailMutasiModal = false; openPrintMutasi(selectedDetailMutasi)"
                                class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all flex items-center space-x-1.5">
                                <span>🖨️ Cetak & Edit Surat BAST</span>
                            </button>
                            <button type="button" @click="showDetailMutasiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                        </div>

                    </div>
                </template>

            </div>
        </div>
