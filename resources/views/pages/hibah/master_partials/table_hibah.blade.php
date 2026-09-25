<!-- TABEL TRANSAKSI HIBAH ASET -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-xs text-slate-300 min-w-[900px]">
            <thead class="bg-slate-950 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                <tr>
                    <th class="px-4 py-3.5 text-center w-12">No</th>
                    <th class="px-4 py-3.5 text-center w-36">Tipe Hibah</th>
                    <th class="px-5 py-3.5">Nama Barang &amp; Kode 108</th>
                    <th class="px-4 py-3.5">Dokumen BAST Resmi</th>
                    <th class="px-4 py-3.5">Pemberi / Penerima</th>
                    <th class="px-4 py-3.5 text-center">Volume</th>
                    <th class="px-5 py-3.5 text-right">Nilai Aset (Rp)</th>
                    <th class="px-4 py-3.5 text-center">Periode</th>
                    <th class="px-4 py-3.5 text-center w-28">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80 bg-slate-900/40">
                <template x-for="(item, idx) in filteredHibahList" :key="item.id">
                    <tr class="hover:bg-slate-800/50 transition-colors">
                        <!-- No -->
                        <td class="px-4 py-4 text-center font-bold text-slate-500" x-text="idx + 1"></td>

                        <!-- Tipe Hibah Badge -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <template x-if="item.tipe_hibah === 'masuk'">
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-amber-400/20 text-amber-300 border border-amber-400/40 shadow-sm">
                                    <span>🎁</span><span>HIBAH MASUK</span>
                                </span>
                            </template>
                            <template x-if="item.tipe_hibah === 'keluar'">
                                <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-sm">
                                    <span>📤</span><span>HIBAH KELUAR</span>
                                </span>
                            </template>
                        </td>

                        <!-- Nama Barang & Kode 108 -->
                        <td class="px-5 py-4">
                            <div class="font-extrabold text-white text-xs" x-text="item.astap ? item.astap.nama_barang : (item.nama_barang || '-')"></div>
                            <div class="flex items-center space-x-2 mt-1">
                                <span class="font-mono text-[10px] text-cyan-400 bg-cyan-500/10 px-1.5 py-0.5 rounded border border-cyan-500/20"
                                    x-text="item.astap?.kode_108 || item.kode_108 || '-'"></span>
                                <span class="text-[10px] text-slate-400 truncate max-w-xs" x-text="item.astap?.jenis_astap?.nama_jenis || item.astap?.category || 'ASET'"></span>
                            </div>
                        </td>

                        <!-- Dokumen BAST -->
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="font-mono font-bold text-slate-200 text-xs" x-text="item.nomor_bast || '-'"></div>
                            <div class="text-[10px] text-slate-400 mt-0.5 flex items-center space-x-1">
                                <span>📅</span>
                                <span x-text="formatTanggalIndo(item.tanggal_bast)"></span>
                            </div>
                        </td>

                        <!-- Pemberi / Penerima Hibah -->
                        <td class="px-4 py-4">
                            <div class="font-bold text-amber-300 text-xs truncate max-w-[200px]" x-text="item.pihak_hibah || '-'"></div>
                            <div class="text-[10px] text-slate-400 mt-0.5" x-text="item.tipe_hibah === 'masuk' ? 'Pemberi Hibah' : 'Penerima Hibah'"></div>
                        </td>

                        <!-- Volume -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800 font-mono font-bold text-teal-300 text-xs">
                                <span x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span>
                            </span>
                        </td>

                        <!-- Nilai Aset (Rp) -->
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <div class="font-mono font-black text-sm"
                                :class="item.tipe_hibah === 'masuk' ? 'text-amber-300' : 'text-rose-400'"
                                x-text="formatRupiah(item.nilai_aset)"></div>
                            <div class="text-[10px] text-slate-500 font-mono"
                                x-text="'@ ' + formatRupiah(item.nilai_aset / Math.max(1, item.jumlah_volume || 1))"></div>
                        </td>

                        <!-- Periode (TW & Tahun) -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <div class="font-bold text-white text-xs" x-text="item.triwulan"></div>
                            <div class="font-mono text-[10px] text-slate-400 font-semibold" x-text="'TA ' + item.tahun"></div>
                        </td>

                        <!-- Aksi Buttons -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <div class="flex items-center justify-center space-x-1.5">
                                <!-- Detail Button -->
                                <button type="button" @click="openDetail(item)"
                                    class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition-all shadow-sm cursor-pointer"
                                    title="Lihat Rincian Data Hibah">
                                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                <!-- Hapus / Batalkan Button -->
                                <button type="button" @click="confirmDelete(item)"
                                    class="p-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 border border-rose-500/20 transition-all shadow-sm cursor-pointer"
                                    title="Hapus / Batalkan Transaksi Ini">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- Empty State -->
                <template x-if="filteredHibahList.length === 0">
                    <tr>
                        <td colspan="9" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-16 h-16 rounded-full bg-slate-800/80 flex items-center justify-center text-3xl">
                                    🎁
                                </div>
                                <div class="text-sm font-bold text-white">Belum Ada Data Transaksi Hibah</div>
                                <p class="text-xs text-slate-400 max-w-md">
                                    Tidak ada catatan hibah yang cocok dengan filter yang dipilih. Silakan klik tombol "Tambah Hibah Masuk" atau "Hibahkan Barang (Keluar)".
                                </p>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Table Footer Summary -->
    <div class="p-4 bg-slate-950 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-2">
        <div>
            Menampilkan <span class="font-bold text-white" x-text="filteredHibahList.length"></span> dari <span class="font-bold text-white" x-text="hibahList.length"></span> total transaksi hibah.
        </div>
        <div class="flex items-center space-x-4">
            <div>
                Total Nilai: <strong class="text-amber-400 font-mono font-bold" x-text="formatRupiah(computedFilteredTotal)"></strong>
            </div>
        </div>
    </div>
</div>
