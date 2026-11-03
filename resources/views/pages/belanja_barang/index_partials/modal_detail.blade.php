<!-- ========================================================================= -->
<!-- MODAL DETAIL FAKTUR BELANJA BARANG MULTI-ITEM & BERKAS DOKUMEN            -->
<!-- Dibuat seragam mengikuti standar Bespoke High-End Dashboard Mutasi        -->
<!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="detailModalOpen" x-cloak @click.self="closeDetailModal()"
         class="fixed inset-0 flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 9000;"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-5 my-auto"
             style="background-color: #0f172a;">

            <!-- 1. MODAL HEADER -->
            <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
                <div class="space-y-1.5 min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <!-- Kategori Badge -->
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0 bg-amber-400/20 text-amber-300 border-amber-400/30">
                            📦 EXTRACOM
                        </span>

                        <!-- Sumber Dana Badge -->
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0 bg-indigo-500/20 text-indigo-300 border-indigo-500/30">
                            🏪 BELANJA BARANG (AKUN 5.1.02)
                        </span>

                        <!-- Status Alokasi Badge -->
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                            :class="selectedItem?.status_alokasi === 'ruangan'
                                ? 'bg-indigo-500/20 text-indigo-300 border-indigo-500/30'
                                : (selectedItem?.status_alokasi === 'sebagian'
                                    ? 'bg-amber-500/20 text-amber-300 border-amber-500/30'
                                    : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30')"
                            x-text="selectedItem?.status_alokasi === 'ruangan'
                                ? '🏢 Penempatan Ruangan'
                                : (selectedItem?.status_alokasi === 'sebagian'
                                    ? '🔀 Sebagian Ruang & Gudang'
                                    : '📦 Buffer Stok Gudang')">
                        </span>

                        <!-- Nomor Faktur Badge -->
                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate"
                            x-text="'Faktur: ' + (selectedItem?.nomor_faktur || '-')"></span>

                        <!-- Tanggal Faktur Badge -->
                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0">
                            <span class="text-slate-400">📅 Tanggal:</span>
                            <span class="text-cyan-300 font-bold" x-text="selectedItem?.tanggal_faktur || '-'"></span>
                        </span>
                    </div>

                    <!-- Nama Toko & Nomor Faktur Title -->
                    <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-white leading-snug break-words"
                        x-text="(selectedItem?.toko_penyedia ? selectedItem.toko_penyedia + ' — ' : '') + 'Faktur No. ' + (selectedItem?.nomor_faktur || '')"></h3>
                </div>

                <!-- Tombol Action Header: Close -->
                <div class="flex items-center space-x-2 shrink-0">
                    <button type="button" @click="closeDetailModal()"
                        title="Tutup Modal"
                        class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all shrink-0 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <template x-if="selectedItem">
                <div class="space-y-4 text-xs text-slate-300">

                    <!-- 2. TOP 4 METRIC KPI CARDS -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                        <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0 shadow-sm">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">🏪 Toko Rekanan Penyedia</span>
                            <span class="text-white font-bold text-xs sm:text-sm leading-tight block truncate"
                                  :title="selectedItem?.toko_penyedia"
                                  x-text="selectedItem?.toko_penyedia || '-'"></span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0 shadow-sm">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📅 Periode Pembelian</span>
                            <span class="text-cyan-300 font-extrabold font-mono text-xs sm:text-sm block"
                                  x-text="(selectedItem?.triwulan || 'TW I') + ' · ' + (selectedItem?.tahun || '-')"></span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0 shadow-sm">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📏 Macam &amp; Volume</span>
                            <span class="text-teal-300 font-extrabold font-mono text-xs sm:text-sm block truncate"
                                  x-text="(selectedItem?.total_item_count || 0) + ' Macam / ' + (selectedItem?.total_volume || 0) + ' Unit'"></span>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0 shadow-sm">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">💰 Total Nilai Pembelian</span>
                            <span class="text-emerald-400 font-extrabold font-mono text-xs sm:text-sm block truncate"
                                  x-text="'Rp ' + formatRupiah(selectedItem?.total_pembelian || 0)"></span>
                        </div>
                    </div>

                    <!-- 3. DOKUMEN FAKTUR & BERKAS DIGITAL (INTERACTIVE ATTACHMENTS CARD) -->
                    <div class="p-4 rounded-2xl bg-slate-950/60 border border-indigo-500/20 space-y-2.5 shadow-sm">
                        <div class="flex items-center justify-between border-b border-indigo-500/20 pb-2">
                            <span class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📜 Berkas Dokumen Digital Terlampir</span>
                            </span>
                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded border bg-indigo-950/80 border-indigo-500/40 text-indigo-300"
                                x-text="'No. Faktur: ' + (selectedItem?.nomor_faktur || '-')"></span>
                        </div>
                        <div class="flex flex-wrap items-center gap-3 pt-1">
                            <template x-if="selectedItem?.dokumen_faktur_url">
                                <a :href="selectedItem.dokumen_faktur_url" target="_blank" rel="noopener noreferrer"
                                    class="px-3.5 py-2 rounded-xl bg-indigo-500/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/40 text-xs font-bold transition-all flex items-center space-x-2 shadow-sm">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span>Buka Berkas Nota / Faktur Asli ↗</span>
                                </a>
                            </template>
                            <template x-if="!selectedItem?.dokumen_faktur_url">
                                <span class="text-xs text-slate-500 italic flex items-center gap-1.5">
                                    <span>📎</span> Faktur: Tidak ada berkas nota/faktur terunggah
                                </span>
                            </template>

                            <template x-if="selectedItem?.dokumen_kuitansi_url">
                                <a :href="selectedItem.dokumen_kuitansi_url" target="_blank" rel="noopener noreferrer"
                                    class="px-3.5 py-2 rounded-xl bg-cyan-500/20 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/40 text-xs font-bold transition-all flex items-center space-x-2 shadow-sm">
                                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    <span>Buka Berkas Bukti Terima / Kuitansi Gudang ↗</span>
                                </a>
                            </template>
                            <template x-if="!selectedItem?.dokumen_kuitansi_url">
                                <span class="text-xs text-slate-500 italic flex items-center gap-1.5">
                                    <span>📎</span> Kuitansi: Tidak ada berkas kuitansi terunggah
                                </span>
                            </template>
                        </div>
                    </div>

                    <!-- 4. PIHAK & ALUR PENGADAAN -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-2">
                            <div class="flex items-center space-x-1.5 border-b border-slate-800 pb-1.5 text-slate-300 font-bold uppercase tracking-wider text-[11px]">
                                <span>🏪</span>
                                <span>PIHAK PENYEDIA / TOKO REKANAN</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-400 text-[10px] block">Toko / Rekanan Penjual:</span>
                                <p class="text-emerald-400 font-bold text-xs" x-text="selectedItem?.toko_penyedia || '-'"></p>
                                <p class="text-[10px] text-slate-400 font-mono" x-text="'No. Faktur: ' + (selectedItem?.nomor_faktur || '-')"></p>
                                <p class="text-[10px] text-slate-400" x-text="'Tanggal Faktur: ' + (selectedItem?.tanggal_faktur || '-')"></p>
                            </div>
                        </div>

                        <div class="p-3.5 rounded-2xl bg-indigo-950/20 border border-indigo-500/30 space-y-2">
                            <div class="flex items-center space-x-1.5 border-b border-indigo-500/30 pb-1.5 font-bold uppercase tracking-wider text-[11px] text-indigo-300">
                                <span>🏢</span>
                                <span>PENERIMA &amp; PENYIMPANAN RSUD</span>
                            </div>
                            <div class="space-y-1">
                                <span class="text-slate-400 text-[10px] block">Status Penempatan Aset:</span>
                                <p class="font-bold text-xs text-indigo-200"
                                    x-text="selectedItem?.status_alokasi === 'ruangan' ? 'Langsung Ditempatkan di Ruangan RSUD' : (selectedItem?.status_alokasi === 'sebagian' ? 'Sebagian Gudang & Ruangan' : 'Disimpan di Gudang Buffer Stok Perbekalan')"></p>
                                <p class="text-[10px] text-indigo-400/90 font-semibold" x-text="'Instansi: RSUD Dr. H. Koesnandi Bondowoso'"></p>
                            </div>
                        </div>
                    </div>

                    <!-- 5. TABEL RINCIAN SELURUH BARANG PEMBELIAN (MULTI-ITEM) -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <h4 class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📦 RINCIAN BARANG PEMBELIAN</span>
                                <span class="text-[10px] text-slate-500" x-text="selectedItem ? '(' + (selectedItem.items ? selectedItem.items.length : 0) + ' Macam Barang)' : ''"></span>
                            </h4>
                            <span class="text-[11px] font-mono text-slate-400">
                                Total Volume: <strong class="text-white" x-text="selectedItem ? selectedItem.total_volume + ' Unit' : '0'"></strong>
                            </span>
                        </div>

                        <div class="rounded-xl border border-slate-800/80 overflow-x-auto custom-scrollbar">
                            <table class="w-full text-left text-xs text-slate-300">
                                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                                    <tr>
                                        <th class="py-2.5 px-3 w-10 text-center">No</th>
                                        <th class="py-2.5 px-3 min-w-[200px]">Nama Barang &amp; Kode 108</th>
                                        <th class="py-2.5 px-3 text-center">Volume</th>
                                        <th class="py-2.5 px-3 text-right">Harga Satuan</th>
                                        <th class="py-2.5 px-3 text-right">Subtotal</th>
                                        <th class="py-2.5 px-3 min-w-[150px]">Alokasi Penempatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                    <template x-for="(item, iIdx) in (selectedItem?.items || [])" :key="iIdx">
                                        <tr class="hover:bg-slate-800/60 transition-colors">
                                            <td class="py-3 px-3 text-center font-mono text-slate-500" x-text="iIdx + 1"></td>
                                            <td class="py-3 px-3">
                                                <div class="font-bold text-white" x-text="item.nama_barang"></div>
                                                <div class="text-[10px] font-mono text-cyan-400 mt-0.5" x-text="item.kode_108"></div>
                                            </td>
                                            <td class="py-3 px-3 text-center font-mono">
                                                <span class="font-bold text-white" x-text="item.jumlah_volume"></span>
                                                <span class="text-slate-400 text-[10px]" x-text="item.satuan"></span>
                                            </td>
                                            <td class="py-3 px-3 text-right font-mono text-slate-300" x-text="'Rp ' + formatRupiah(item.harga_satuan)"></td>
                                            <td class="py-3 px-3 text-right font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(item.subtotal)"></td>
                                            <td class="py-3 px-3">
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border"
                                                    :class="item.ruangan && item.ruangan !== 'Gudang Perbekalan' ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30' : 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'"
                                                    x-text="item.ruangan || 'Gudang Perbekalan'"></span>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 6. TABEL RINCIAN REGISTER NIBAR PER-UNIT PERSIS MUTASI / ASTAP -->
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-800 pb-3">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider">🏷️ RINCIAN NIBAR &amp; PENEMPATAN RUANGAN (REGISTER):</span>
                                </div>
                                <p class="text-[10px] text-slate-400 mt-0.5">
                                    Daftar unik kode NIBAR 28-digit per-aset hasil belanja barang beserta penempatannya di RSUD Koesnandi.
                                </p>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <span class="text-[10px] font-extrabold px-3 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-mono shadow-sm"
                                      x-text="filteredRegisters.length + ' / ' + totalRegistersCount + ' Aset'"></span>
                            </div>
                        </div>

                        <!-- Filter Bar Interaktif Rincian Modal -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 bg-slate-900/70 p-3 rounded-xl border border-slate-800">
                            <div>
                                <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Penempatan</label>
                                <select x-model="detailPenempatanFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                    <option value="all">Semua Penempatan</option>
                                    <option value="ruangan">🏢 Sudah di Ruangan</option>
                                    <option value="gudang">📦 Buffer Stok Gudang</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Aset</label>
                                <select x-model="detailKondisiFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                    <option value="all">Semua Kondisi</option>
                                    <option value="Baik">Baik (B)</option>
                                    <option value="Kurang Baik">Kurang Baik (KB)</option>
                                    <option value="Rusak Berat">Rusak Berat (RB)</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari NIBAR / Ruangan / Barang</label>
                                <input type="text" x-model="detailSearchQuery" placeholder="Cari NIBAR / Ruang / Nama..."
                                       class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                            </div>
                        </div>

                        <!-- Responsive Scroll Container -->
                        <div class="rounded-xl border border-slate-800/80 custom-scrollbar" style="max-height: 285px; overflow-y: auto; overflow-x: auto;">
                            <table class="w-full text-left text-[11px] text-slate-300 min-w-[640px]">
                                <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px] border-b border-slate-800 shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                                    <tr>
                                        <th class="px-3.5 py-2.5 text-center">NIBAR RESMI</th>
                                        <th class="px-3.5 py-2.5 text-left">Nama Barang</th>
                                        <th class="px-3.5 py-2.5 text-center">Penempatan Ruangan</th>
                                        <th class="px-3.5 py-2.5 text-center">Kondisi</th>
                                        <th class="px-3.5 py-2.5 text-center">Status</th>
                                        <th class="px-3.5 py-2.5 text-center whitespace-nowrap">QR Code</th>
                                        <th class="px-3.5 py-2.5 text-center whitespace-nowrap">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                    <template x-for="reg in filteredRegisters" :key="reg.nibar || reg.id">
                                        <tr class="hover:bg-slate-800/60 transition-colors">
                                            <td class="px-3.5 py-2.5 font-mono font-bold text-emerald-400 whitespace-nowrap text-center" x-text="reg.nibar"></td>
                                            <td class="px-3.5 py-2.5">
                                                <div class="font-bold text-white truncate max-w-[200px]" :title="reg.nama_barang" x-text="reg.nama_barang"></div>
                                                <div class="text-[9.5px] font-mono text-cyan-400/90" x-text="reg.kode_108"></div>
                                            </td>
                                            <td class="px-3.5 py-2.5 text-center">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold border"
                                                    :class="reg.ruang && reg.ruang !== 'Gudang Perbekalan' ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30' : 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30'"
                                                    x-text="reg.ruang || 'Gudang Perbekalan'"></span>
                                            </td>
                                            <td class="px-3.5 py-2.5 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border"
                                                    :class="(reg.kondisi || 'Baik') === 'Baik' ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : ((reg.kondisi || '') === 'Kurang Baik' ? 'bg-amber-500/10 text-amber-300 border-amber-500/30' : 'bg-rose-500/10 text-rose-300 border-rose-500/30')"
                                                    x-text="reg.kondisi || 'Baik'"></span>
                                            </td>
                                            <td class="px-3.5 py-2.5 text-center">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border"
                                                    :class="reg.status === 'Tersedia' ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : 'bg-slate-800 text-slate-300 border-slate-700'"
                                                    x-text="reg.status || 'Tersedia'"></span>
                                            </td>
                                            <!-- Kolom QR Code -->
                                            <td class="px-3.5 py-2.5 text-center whitespace-nowrap align-middle">
                                                <button type="button" @click.stop="downloadQrCodeNibar(reg, selectedItem)"
                                                    title="Pratinjau & Download QR NIBAR Aset Ini"
                                                    class="inline-flex items-center space-x-1.5 px-3 py-1.5 rounded-full bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 hover:text-emerald-300 border border-emerald-500/40 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                                    <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                    </svg>
                                                    <span>Download QR</span>
                                                </button>
                                            </td>

                                            <!-- Kolom Aksi (Riwayat Mutasi, Ubah Kondisi, Hapus) -->
                                            <td class="px-3.5 py-2.5 text-center whitespace-nowrap align-middle">
                                                <div class="flex items-center justify-center gap-1.5">
                                                    <!-- 1. Tombol Cek Riwayat Aset -->
                                                    <button type="button" @click.stop="openRiwayatModal(reg)" title="Cek Riwayat Mutasi Aset Ini"
                                                            class="p-1.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-400 hover:text-purple-300 transition-all cursor-pointer shadow-sm active:scale-95">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                    </button>

                                                    @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('astap', 'edit'))
                                                    <!-- 2. Tombol Ubah Kondisi Barang (Modal Khusus) -->
                                                    <button type="button" @click.stop="openEditKondisiModal(reg)" title="Ubah Kondisi Aset Ini"
                                                            class="p-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 hover:text-amber-300 transition-all cursor-pointer shadow-sm active:scale-95">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L20.586 7a2 2 0 010 2.828l-8.586 8.586z"/></svg>
                                                    </button>
                                                    @endif

                                                    @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('astap', 'delete'))
                                                    <!-- 3. Tombol Hapus Register -->
                                                    <button type="button" @click.stop="deleteRegister(reg)" title="Hapus Aset Register Ini"
                                                            class="p-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition-all cursor-pointer shadow-sm active:scale-95">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                    </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                    <template x-if="filteredRegisters.length === 0">
                                        <tr>
                                            <td colspan="7" class="px-4 py-8 text-center text-slate-500 italic">
                                                Tidak ada data register NIBAR yang cocok dengan kriteria filter.
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- 7. KETERANGAN / CATATAN FAKTUR -->
                    <template x-if="selectedItem?.keterangan && selectedItem.keterangan !== '-'">
                        <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800 text-xs">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">Catatan / Keterangan Pembelian:</span>
                            <p class="text-slate-300 whitespace-pre-line leading-relaxed" x-text="selectedItem.keterangan"></p>
                        </div>
                    </template>

                </div>
            </template>

            <!-- 8. MODAL FOOTER -->
            <div class="flex items-center justify-end pt-3 border-t border-slate-800">
                <button type="button" @click="closeDetailModal()"
                    class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all cursor-pointer">
                    Tutup Rincian
                </button>
            </div>

        </div>
    </div>
</template>
