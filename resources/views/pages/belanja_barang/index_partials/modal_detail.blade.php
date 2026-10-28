<!-- ========================================================================= -->
<!-- MODAL DETAIL FAKTUR BELANJA BARANG MULTI-ITEM & BERKAS DOKUMEN            -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="detailModalOpen" x-cloak
        class="fixed inset-0 overflow-y-auto"
        style="z-index: 9000;"
        aria-labelledby="modal-title" role="dialog" aria-modal="true">
        
        <!-- Backdrop Blur Overlay Fullscreen -->
        <div x-show="detailModalOpen"
            x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity"
            style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);"></div>

        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div x-show="detailModalOpen"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                @click.away="closeDetailModal()"
                class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl">
                
                <!-- Modal Header -->
                <div class="p-6 border-b border-slate-800 flex items-center justify-between bg-slate-950/60">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 text-lg">
                            📦
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white" id="modal-title">
                                Detail Faktur Belanja Barang (Perbekalan)
                            </h3>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Inventaris Ekstrakomptabel · Akun 5.1.02 · Pengawasan Fisik Ruangan (KIR)
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
                    
                    <!-- Box 1: Info Faktur & Toko Rekanan -->
                    <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-extrabold text-indigo-400 uppercase tracking-wider">INFORMASI FAKTUR PEMBELIAN</span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/10 text-indigo-300 border border-indigo-500/20"
                                x-text="selectedItem ? selectedItem.triwulan + ' · ' + selectedItem.tahun : ''"></span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 text-xs">
                            <div>
                                <span class="text-slate-500 block text-[11px] mb-0.5">Toko / Rekanan Penyedia:</span>
                                <span class="text-white font-bold text-sm block" x-text="selectedItem ? selectedItem.toko_penyedia : '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[11px] mb-0.5">Nomor Faktur / Nota:</span>
                                <span class="text-indigo-300 font-mono font-bold block" x-text="selectedItem ? selectedItem.nomor_faktur : '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[11px] mb-0.5">Tanggal Faktur:</span>
                                <span class="text-white font-medium block" x-text="selectedItem ? selectedItem.tanggal_faktur : '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[11px] mb-0.5">Total Nilai Faktur:</span>
                                <span class="text-emerald-400 font-mono font-black text-sm block">
                                    Rp <span x-text="selectedItem ? formatRupiah(selectedItem.total_pembelian) : '0'"></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Box 2: Berkas Dokumen Terlampir (Digital Attachments) -->
                    <div class="p-4 rounded-2xl bg-slate-950/60 border border-indigo-500/20 space-y-2.5">
                        <span class="text-[10px] font-extrabold text-cyan-400 uppercase tracking-wider block">BERKAS DOKUMEN DIGITAL TERLAMPIR</span>
                        <div class="flex flex-wrap items-center gap-3">
                            <template x-if="selectedItem && selectedItem.dokumen_faktur_url">
                                <a :href="selectedItem.dokumen_faktur_url" target="_blank"
                                    class="px-4 py-2 rounded-xl bg-indigo-600/20 hover:bg-indigo-600/30 text-indigo-300 border border-indigo-500/40 text-xs font-bold transition-all flex items-center gap-2">
                                    <span>📄</span>
                                    <span>Lihat Berkas Nota / Faktur Asli ↗</span>
                                </a>
                            </template>
                            <template x-if="selectedItem && !selectedItem.dokumen_faktur_url">
                                <span class="text-xs text-slate-500 italic">Tidak ada berkas faktur terlampir</span>
                            </template>

                            <template x-if="selectedItem && selectedItem.dokumen_kuitansi_url">
                                <a :href="selectedItem.dokumen_kuitansi_url" target="_blank"
                                    class="px-4 py-2 rounded-xl bg-cyan-600/20 hover:bg-cyan-600/30 text-cyan-300 border border-cyan-500/40 text-xs font-bold transition-all flex items-center gap-2">
                                    <span>📦</span>
                                    <span>Lihat Bukti Terima / Kuitansi Gudang ↗</span>
                                </a>
                            </template>
                        </div>
                    </div>

                    <!-- Box 3: Tabel Rincian Seluruh Barang dalam Faktur (Multi-Item) -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-extrabold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📋 Rincian Barang Pembelian</span>
                                <span class="text-[10px] text-slate-500" x-text="selectedItem ? '(' + (selectedItem.items ? selectedItem.items.length : 0) + ' Macam Barang)' : ''"></span>
                            </h4>
                            <span class="text-[11px] font-mono text-slate-400">
                                Total Volume: <strong class="text-white" x-text="selectedItem ? selectedItem.total_volume + ' Unit' : '0'"></strong>
                            </span>
                        </div>

                        <div class="rounded-2xl border border-slate-800 bg-slate-950 overflow-hidden">
                            <table class="w-full text-left text-xs text-slate-300">
                                <thead class="bg-slate-900 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                                    <tr>
                                        <th class="py-2.5 px-3 w-10 text-center">No</th>
                                        <th class="py-2.5 px-3">Nama Barang &amp; Kode 108</th>
                                        <th class="py-2.5 px-3 text-center">Volume</th>
                                        <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                                        <th class="py-2.5 px-3">Penempatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/80">
                                    <template x-for="(item, iIdx) in (selectedItem?.items || [])" :key="iIdx">
                                        <tr class="hover:bg-slate-900/50">
                                            <td class="py-3 px-3 text-center font-mono text-slate-500" x-text="iIdx + 1"></td>
                                            <td class="py-3 px-3">
                                                <div class="font-bold text-white" x-text="item.nama_barang"></div>
                                                <div class="text-[10px] font-mono text-indigo-400" x-text="item.kode_108"></div>
                                            </td>
                                            <td class="py-3 px-3 text-center font-mono">
                                                <span class="font-bold text-white" x-text="item.jumlah_volume"></span>
                                                <span class="text-slate-400 text-[10px]" x-text="item.satuan"></span>
                                            </td>
                                            <td class="py-3 px-3 text-right font-mono text-slate-300" x-text="formatRupiah(item.harga_satuan)"></td>
                                            <td class="py-3 px-3 text-right font-mono font-bold text-emerald-400" x-text="formatRupiah(item.subtotal)"></td>
                                            <td class="py-3 px-3">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                                    :class="item.ruangan && item.ruangan !== 'Gudang Perbekalan' ? 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20'"
                                                    x-text="item.ruangan || 'Gudang Perbekalan'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Box 4: Daftar Unit Fisik & Register NIBAR (Collapsible / List) -->
                    <div class="space-y-3">
                        <h4 class="text-xs font-extrabold text-slate-300 uppercase tracking-wider">
                            Daftar Register NIBAR 28-Digit Seluruh Unit Fisik
                        </h4>

                        <div class="space-y-2">
                            <template x-for="(item, iIdx) in (selectedItem?.items || [])" :key="'reg-item-' + iIdx">
                                <div class="rounded-xl border border-slate-800 bg-slate-950 p-3 space-y-2">
                                    <div class="flex items-center justify-between text-xs border-b border-slate-900 pb-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-white" x-text="item.nama_barang"></span>
                                            <span class="text-[10px] text-slate-500" x-text="'(' + item.registers.length + ' Unit)'"></span>
                                        </div>
                                        <span class="text-[10px] text-indigo-400 font-mono" x-text="item.kode_108"></span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        <template x-for="(reg, rIdx) in item.registers" :key="rIdx">
                                            <div class="p-2 rounded-lg bg-slate-900/80 border border-slate-800/80 flex items-center justify-between text-xs">
                                                <div class="min-w-0">
                                                    <span class="font-mono text-[11px] font-bold text-indigo-300 block truncate" x-text="reg.nibar"></span>
                                                    <span class="text-[10px] text-slate-400 block truncate" x-text="reg.ruang"></span>
                                                </div>
                                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded shrink-0 ml-2"
                                                    :class="reg.status === 'Tersedia' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-300'"
                                                    x-text="reg.status"></span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Keterangan Faktur -->
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-xs" x-show="selectedItem && selectedItem.keterangan && selectedItem.keterangan !== '-'">
                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Catatan / Keterangan Pembelian</span>
                        <p class="text-slate-300 mt-1 whitespace-pre-line" x-text="selectedItem ? selectedItem.keterangan : ''"></p>
                    </div>

                </div>

                <!-- Modal Footer -->
                <div class="p-4 border-t border-slate-800 flex items-center justify-end bg-slate-950/60">
                    <button type="button" @click="closeDetailModal()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
</template>
