<!-- ========================================================================= -->
<!-- KONTEN TAB 6: BAST PENERIMAAN BELANJA MODAL / BARANG (FAKTUR REKANAN)      -->
<!-- ========================================================================= -->
<div x-show="activeTab === 'belanja_barang'" class="space-y-6" x-cloak>
    
    <!-- Toolbar & Filter Belanja Barang -->
    <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
        <div class="ba-toolbar-wrap flex flex-col lg:flex-row items-center justify-between gap-4">
            
            <div class="ba-toolbar-actions flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <div class="relative flex-1 min-w-0 sm:w-80">
                    <input type="text" x-model="belanjaBarangSearch" placeholder="Cari nomor faktur / SPK / rekanan toko..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-sky-500">
                    <svg class="w-4 h-4 text-sky-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="text-xs text-slate-400 font-medium text-center lg:text-right">
                Total Data: <span class="font-bold text-sky-400 font-mono" x-text="filteredBelanjaBarangList.length"></span> Faktur Belanja Pengadaan
            </div>
        </div>
    </div>

    <!-- Tabel Daftar BAST Belanja Barang -->
    <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        <div class="swipe-hint-ba hidden items-center justify-center gap-1.5 py-2 px-4 text-[10px] font-bold uppercase tracking-wider text-sky-300/90 bg-slate-950/60 border border-b-0 border-slate-800/80 rounded-t-2xl">
            <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
            <span>Geser tabel ke samping untuk melihat seluruh kolom</span>
        </div>
        <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 400px; overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-left">Nomor Faktur &amp; SPK</th>
                        <th class="px-4 py-3.5 text-left">Rekanan Toko / Penyedia</th>
                        <th class="px-4 py-3.5 text-center">Tanggal Faktur</th>
                        <th class="px-4 py-3.5 text-left">Rincian Barang</th>
                        <th class="px-4 py-3.5 text-right">Total Pembelian (Rp)</th>
                        <th class="px-4 py-3.5 text-center">Status Verifikasi</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi BAST</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredBelanjaBarangList" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            <td class="px-4 py-4">
                                <div class="font-mono font-bold text-sky-400" x-text="item.nomor_faktur"></div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="'SPK: ' + item.spk_nomor"></div>
                                <div class="text-[10px] text-slate-500 font-mono" x-text="item.nomor_bast"></div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-white" x-text="item.toko_penyedia"></div>
                                <div class="text-[10px] text-slate-400" x-text="item.keterangan"></div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <div class="font-mono text-slate-200 text-xs font-semibold" x-text="item.tanggal_faktur"></div>
                                <div class="text-[10px] text-slate-400" x-text="item.tgl_bast_format"></div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-200" x-text="item.items && item.items.length > 0 ? (item.items[0].nama_barang + (item.items.length > 1 ? ' (+' + (item.items.length - 1) + ' barang lain)' : '')) : 'Barang Pengadaan'"></div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="(item.items ? item.items.reduce((s, i) => s + (parseInt(i.volume, 10) || 1), 0) : 1) + ' Unit Total'"></div>
                            </td>
                            <td class="px-4 py-4 text-right font-mono font-bold text-emerald-400" x-text="item.total_pembelian_format"></td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap border shadow-sm bg-emerald-500/15 text-emerald-300 border-emerald-500/30">
                                    <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span x-text="item.status"></span>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                
                                <!-- Tombol Cetak / Live Edit -->
                                <button type="button" @click="openPrintBelanjaBarang(item)"
                                    class="px-3 py-1.5 rounded-xl bg-sky-500/20 hover:bg-sky-500/30 text-sky-300 border border-sky-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                    <span>🖨️ Cetak BAST</span>
                                </button>

                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <template x-if="filteredBelanjaBarangList.length === 0">
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-500">
                                <div class="flex flex-col items-center justify-center space-y-1.5">
                                    <span class="text-2xl">🏪</span>
                                    <p class="font-semibold text-slate-300 text-xs">Tidak ada data BAST belanja barang yang ditemukan.</p>
                                    <p class="text-[11px] text-slate-500">Faktur belanja barang dapat dikelola melalui menu <span class="text-sky-400 font-semibold">Master Aset &rarr; Kelola Belanja Barang</span>.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>
