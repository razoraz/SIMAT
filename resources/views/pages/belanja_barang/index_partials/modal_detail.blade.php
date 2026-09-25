<!-- ========================================================================= -->
<!-- MODAL DETAIL BELANJA BARANG & REGISTER NIBAR                               -->
<!-- ========================================================================= -->
<div x-show="detailModalOpen" x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title" role="dialog" aria-modal="true">
    
    <!-- Backdrop Blur Overlay -->
    <div x-show="detailModalOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity"></div>

    <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
        <div x-show="detailModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            @click.away="closeDetailModal()"
            class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
            
            <!-- Modal Header -->
            <div class="p-6 border-b border-slate-800 flex items-center justify-between bg-slate-950/50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-lg">
                        📦
                    </div>
                    <div>
                        <h3 class="text-base font-extrabold text-white" id="modal-title">
                            Detail Perolehan Belanja Barang
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">
                            Pengadaan Mandiri Gudang · Pengawasan Fisik Ruangan (KIR)
                        </p>
                    </div>
                </div>

                <button type="button" @click="closeDetailModal()"
                    class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto custom-scrollbar" x-if="selectedItem">
                <!-- Box 1: Info Faktur & Toko -->
                <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold text-indigo-400 uppercase tracking-wider">DOKUMEN FAKTUR PEMBELIAN</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/10 text-indigo-300 border border-indigo-500/20" x-text="selectedItem ? selectedItem.triwulan + ' · ' + selectedItem.tahun : ''"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-slate-500 block text-[11px]">Toko / Rekanan Penyedia:</span>
                            <span class="text-white font-bold text-sm" x-text="selectedItem ? selectedItem.toko_penyedia : '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[11px]">Nomor Faktur / Nota:</span>
                            <span class="text-white font-mono font-semibold" x-text="selectedItem ? selectedItem.nomor_faktur : '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[11px]">Tanggal Faktur:</span>
                            <span class="text-white font-medium" x-text="selectedItem ? selectedItem.tanggal_faktur : '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[11px]">Ruangan Penempatan:</span>
                            <span class="text-indigo-300 font-bold" x-text="selectedItem ? selectedItem.ruang_pemegang : '-'"></span>
                        </div>
                    </div>
                </div>

                <!-- Box 2: Rincian Barang & Nominal -->
                <div class="space-y-3">
                    <h4 class="text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                        Rincian Barang &amp; Spesifikasi
                    </h4>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Nama Barang</span>
                            <span class="text-white font-bold text-sm mt-0.5 block" x-text="selectedItem ? selectedItem.nama_barang : '-'"></span>
                            <span class="text-indigo-400 font-mono text-[11px] block mt-1" x-text="selectedItem ? selectedItem.kode_108 : '-'"></span>
                        </div>

                        <div class="p-3 rounded-xl bg-slate-950 border border-slate-800">
                            <span class="text-slate-500 text-[10px] uppercase font-bold block">Total Realisasi Pembelian</span>
                            <div class="text-white font-mono font-black text-lg mt-0.5">
                                Rp <span x-text="selectedItem ? formatRupiah(selectedItem.total_pembelian) : '0'"></span>
                            </div>
                            <span class="text-slate-400 text-[10px] block mt-1">
                                Vol: <strong class="text-white" x-text="selectedItem ? selectedItem.jumlah_volume + ' ' + selectedItem.satuan : '-'"></strong> (@ Rp <span x-text="selectedItem ? formatRupiah(selectedItem.harga_satuan) : '0'"></span>)
                            </span>
                        </div>
                    </div>

                    <!-- Keterangan -->
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-800 text-xs" x-show="selectedItem && selectedItem.keterangan && selectedItem.keterangan !== '-'">
                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Catatan / Keterangan Pembelian</span>
                        <p class="text-slate-300 mt-1 whitespace-pre-line" x-text="selectedItem ? selectedItem.keterangan : ''"></p>
                    </div>
                </div>

                <!-- Box 3: Daftar Unit Fisik & NIBAR -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                            Daftar Unit Fisik &amp; Register NIBAR (KIR)
                        </h4>
                        <span class="text-[10px] text-slate-500 font-mono" x-text="selectedItem && selectedItem.registers ? selectedItem.registers.length + ' Unit' : ''"></span>
                    </div>

                    <div class="rounded-xl border border-slate-800 overflow-hidden">
                        <div class="max-h-48 overflow-y-auto custom-scrollbar">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-950 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
                                    <tr>
                                        <th class="py-2 px-3 w-8">#</th>
                                        <th class="py-2 px-3">NIBAR</th>
                                        <th class="py-2 px-3">Ruangan</th>
                                        <th class="py-2 px-3 text-center">Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/60 bg-slate-900/60">
                                    <template x-for="(reg, i) in (selectedItem ? selectedItem.registers : [])" :key="i">
                                        <tr class="hover:bg-slate-800/30">
                                            <td class="py-2 px-3 text-slate-500 font-mono text-[11px]" x-text="i + 1"></td>
                                            <td class="py-2 px-3 font-mono font-bold text-indigo-300 text-[11px]" x-text="reg.nibar"></td>
                                            <td class="py-2 px-3 text-slate-300 text-[11px]" x-text="reg.ruang"></td>
                                            <td class="py-2 px-3 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" x-text="reg.kondisi"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-end gap-2">
                <button type="button" @click="closeDetailModal()"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
