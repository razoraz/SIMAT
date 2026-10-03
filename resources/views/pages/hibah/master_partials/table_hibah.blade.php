<!-- ========================================================================= -->
<!-- TABEL MASTER TRANSAKSI HIBAH ASET (DARK LUXURY AMBER / GOLD)              -->
<!-- ========================================================================= -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl relative">
    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-xs text-slate-300 min-w-[1100px]">
            <thead class="bg-slate-950 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800 select-none">
                <tr>
                    <th class="px-4 py-3.5 text-center w-12">No</th>
                    <th class="px-4 py-3.5 text-left min-w-[170px]">Tipe &amp; BAST</th>
                    <th class="px-4 py-3.5 text-left min-w-[180px]">Pemberi / Penerima</th>
                    <th class="px-5 py-3.5 text-left min-w-[220px]">Identitas Barang &amp; 108</th>
                    <th class="px-4 py-3.5 text-center min-w-[140px]">Kondisi Fisik</th>
                    <th class="px-4 py-3.5 text-left min-w-[160px]">Ruangan / Penempatan</th>
                    <th class="px-4 py-3.5 text-center w-24">Volume</th>
                    <th class="px-5 py-3.5 text-right min-w-[140px]">Nilai Aset (Rp)</th>
                    <th class="px-4 py-3.5 text-center w-28">Periode</th>
                    <th class="px-4 py-3.5 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[240px] w-[240px]" 
                        style="position: sticky; right: 0; z-index: 2; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80 bg-slate-900/40">
                <template x-for="(item, idx) in filteredHibahList" :key="item.id">
                    <tr class="hover:bg-slate-800/50 transition-colors group">
                        <!-- 1. No -->
                        <td class="px-4 py-4 text-center font-bold text-slate-500 font-mono" x-text="idx + 1"></td>

                        <!-- 2. Tipe Hibah & Dokumen BAST -->
                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="mb-1.5">
                                <template x-if="item.tipe_hibah === 'masuk'">
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-amber-400/20 text-amber-300 border border-amber-400/40 shadow-sm">
                                        <span>🎁</span><span>HIBAH MASUK</span>
                                    </span>
                                </template>
                                <template x-if="item.tipe_hibah === 'keluar'">
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-lg text-[10px] font-black bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-sm">
                                        <span>📤</span><span>HIBAH KELUAR</span>
                                    </span>
                                </template>
                            </div>
                            <div class="font-mono font-bold text-white text-xs truncate max-w-[190px]" :title="item.nomor_bast" x-text="item.nomor_bast || '-'"></div>
                            <div class="text-[10px] text-slate-400 mt-0.5 flex items-center space-x-1">
                                <span>📅</span>
                                <span x-text="formatTanggalIndo(item.tanggal_bast)"></span>
                            </div>
                        </td>

                        <!-- 3. Pemberi / Penerima Hibah -->
                        <td class="px-4 py-4">
                            <div class="font-extrabold text-amber-300 text-xs truncate max-w-[190px]" :title="item.pihak_hibah" x-text="item.pihak_hibah || '-'"></div>
                            <div class="text-[10px] text-slate-400 mt-0.5" x-text="item.tipe_hibah === 'masuk' ? 'Pihak Pemberi Hibah' : 'Pihak Penerima Hibah'"></div>
                        </td>

                        <!-- 4. Identitas Barang & Kode 108 -->
                        <td class="px-5 py-4">
                            <div class="font-extrabold text-white text-xs group-hover:text-amber-300 transition-colors leading-snug truncate max-w-[240px]" 
                                 :title="item.astap ? item.astap.nama_barang : (item.nama_barang || '-')"
                                 x-text="item.astap ? item.astap.nama_barang : (item.nama_barang || '-')"></div>
                            
                            <div class="flex items-center space-x-1.5 mt-1">
                                <!-- Kategori KIB Badge -->
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold uppercase border"
                                      :class="{
                                          'bg-amber-500/20 text-amber-300 border-amber-500/40': getEffectiveKibCategory(item) === 'KIB A',
                                          'bg-cyan-500/20 text-cyan-300 border-cyan-500/40':     getEffectiveKibCategory(item) === 'KIB B',
                                          'bg-purple-500/20 text-purple-300 border-purple-500/40': getEffectiveKibCategory(item) === 'KIB C',
                                          'bg-teal-500/20 text-teal-300 border-teal-500/40':     getEffectiveKibCategory(item) === 'KIB D',
                                          'bg-orange-500/20 text-orange-300 border-orange-500/40': getEffectiveKibCategory(item) === 'KIB E',
                                          'bg-blue-500/20 text-blue-300 border-blue-500/40':     getEffectiveKibCategory(item) === 'KIB F' || getEffectiveKibCategory(item) === 'ATB'
                                      }"
                                      x-text="getEffectiveKibCategory(item)"></span>

                                <span class="font-mono text-[10px] text-cyan-400 bg-cyan-500/10 px-1.5 py-0.5 rounded border border-cyan-500/20 truncate max-w-[160px]"
                                      :title="item.astap?.kode_108 || item.kode_108 || '-'"
                                      x-text="item.astap?.kode_108 || item.kode_108 || '-'"></span>
                            </div>
                        </td>

                        <!-- 5. Kondisi Fisik (Mini Progress Bar 3 Kondisi) -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <template x-if="getKondisiStats(item).is_multi">
                                <div class="min-w-[125px] max-w-[140px] mx-auto">
                                    <div class="flex h-2 rounded-full overflow-hidden bg-slate-800 mb-1 border border-slate-700/50">
                                        <template x-if="getKondisiStats(item).pct_baik > 0">
                                            <div class="bg-emerald-400" :style="'width: ' + getKondisiStats(item).pct_baik + '%'" :title="getKondisiStats(item).pct_baik + '% Baik'"></div>
                                        </template>
                                        <template x-if="getKondisiStats(item).pct_kb > 0">
                                            <div class="bg-amber-400" :style="'width: ' + getKondisiStats(item).pct_kb + '%'" :title="getKondisiStats(item).pct_kb + '% Kurang Baik'"></div>
                                        </template>
                                        <template x-if="getKondisiStats(item).pct_rb > 0">
                                            <div class="bg-rose-400" :style="'width: ' + getKondisiStats(item).pct_rb + '%'" :title="getKondisiStats(item).pct_rb + '% Rusak Berat'"></div>
                                        </template>
                                    </div>
                                    <div class="flex flex-wrap gap-x-1.5 gap-y-0.5 justify-center text-[9px] font-bold">
                                        <span class="text-emerald-400" x-text="getKondisiStats(item).pct_baik + '% Baik'"></span>
                                        <template x-if="getKondisiStats(item).pct_kb > 0">
                                            <span class="text-amber-400" x-text="getKondisiStats(item).pct_kb + '% KB'"></span>
                                        </template>
                                        <template x-if="getKondisiStats(item).pct_rb > 0">
                                            <span class="text-rose-400" x-text="getKondisiStats(item).pct_rb + '% RB'"></span>
                                        </template>
                                    </div>
                                </div>
                            </template>
                            <template x-if="!getKondisiStats(item).is_multi">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                      :class="getKondisiStats(item).badge_class">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="getKondisiStats(item).dot_class"></span>
                                    <span x-text="getKondisiStats(item).text"></span>
                                </span>
                            </template>
                        </td>

                        <!-- 6. Ruangan / Penempatan -->
                        <td class="px-4 py-4">
                            <div class="text-xs font-semibold text-slate-200 truncate max-w-[170px]" 
                                 :title="getRuangLabel(item)" 
                                 x-text="getRuangLabel(item)"></div>
                            <div class="text-[10px] text-slate-400 mt-0.5">Penempatan Unit RSUD</div>
                        </td>

                        <!-- 7. Volume & Satuan -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800 font-mono font-bold text-teal-300 text-xs shadow-inner">
                                <span x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span>
                            </span>
                        </td>

                        <!-- 8. Nilai Aset (Rp) -->
                        <td class="px-5 py-4 text-right whitespace-nowrap">
                            <div class="font-mono font-black text-xs sm:text-sm"
                                :class="item.tipe_hibah === 'masuk' ? 'text-amber-300' : 'text-rose-400'"
                                x-text="'Rp ' + formatRupiah(item.nilai_aset)"></div>
                            <div class="text-[10px] text-slate-500 font-mono"
                                x-text="'@ Rp ' + formatRupiah(item.nilai_aset / Math.max(1, item.jumlah_volume || 1))"></div>
                        </td>

                        <!-- 9. Periode (TW & Tahun) -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <div class="font-bold text-white text-xs" x-text="item.triwulan"></div>
                            <div class="font-mono text-[10px] text-slate-400 font-semibold" x-text="'TA ' + item.tahun"></div>
                        </td>

                        <!-- 10. Aksi (Detail, Cetak, Ubah, Hapus) — FREEZE STICKY RIGHT -->
                        <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[240px] w-[240px]" 
                            style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- 1. Tombol Detail -->
                                <button type="button" @click="openDetail(item)"
                                    title="Lihat Rincian Data Hibah"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 hover:border-emerald-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-emerald-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Detail</span>
                                </button>

                                <!-- 2. Tombol Cetak BAST -->
                                <button type="button" @click="openPrintBast(item)"
                                    title="Cetak Lembar Dokumen BAST Resmi"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-600 text-amber-300 hover:text-white border border-amber-500/30 hover:border-amber-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-amber-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-amber-400 group-hover/btn:text-white transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                    </svg>
                                    <span>BAST</span>
                                </button>

                                <!-- 3. Tombol Ubah (Form Edit ASTAP Hibah — Khusus Hibah Masuk) -->
                                <template x-if="item.tipe_hibah === 'masuk' && item.astap_id">
                                    <a :href="'/astap/' + item.astap_id + '/edit-hibah'"
                                        title="Ubah Data Aset Hibah (Form Lengkap)"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-cyan-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/btn:text-white group-hover/btn:rotate-12 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Ubah</span>
                                    </a>
                                </template>

                                <!-- 4. Tombol Hapus -->
                                <button type="button" @click="confirmDelete(item)"
                                    title="Hapus / Batalkan Transaksi Hibah"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 hover:border-rose-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-rose-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-rose-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- Empty State -->
                <template x-if="filteredHibahList.length === 0">
                    <tr>
                        <td colspan="10" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-16 h-16 rounded-full bg-slate-800/80 flex items-center justify-center text-3xl shadow-inner">
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
            Menampilkan <span class="font-bold text-white font-mono" x-text="filteredHibahList.length"></span> dari <span class="font-bold text-white font-mono" x-text="hibahList.length"></span> total transaksi hibah.
        </div>
        <div class="flex items-center space-x-4">
            <div>
                Total Nilai: <strong class="text-amber-400 font-mono font-bold text-sm" x-text="'Rp ' + formatRupiah(computedFilteredTotal)"></strong>
            </div>
        </div>
    </div>
</div>
