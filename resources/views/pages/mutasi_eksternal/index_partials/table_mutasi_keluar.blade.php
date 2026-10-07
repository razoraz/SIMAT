<!-- ========================================================================= -->
<!-- TABEL KATALOG MUTASI EKSTERNAL KELUAR (TRANSFER ASET RSUD KE OPD LUAR)   -->
<!-- ========================================================================= -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
    <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 custom-scrollbar min-h-[520px]" style="max-height: calc(100vh - 200px); overflow-y: auto; overflow-x: auto;">
        <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-h-[480px]">
            <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                <tr>
                    <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                    <th class="px-4 py-3.5 text-left min-w-[210px] bg-slate-950">Dokumen BAMB &amp; Tanggal</th>
                    <th class="px-4 py-3.5 text-left min-w-[240px] bg-slate-950">Instansi / SKPD Penerima</th>
                    <th class="px-4 py-3.5 text-left min-w-[260px] bg-slate-950">Aset RSUD yang Ditransfer</th>
                    <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Volume &amp; Nilai</th>
                    <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Kondisi</th>
                    <th class="px-4 py-3.5 text-left min-w-[200px] bg-slate-950">Sifat &amp; Keterangan</th>
                    <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0 min-w-[220px] w-[220px]" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
                <template x-for="(item, index) in filteredMutasis" :key="item.id">
                    <tr class="group hover:bg-slate-800/40 transition-colors">
                        <!-- 1. Nomor Urut -->
                        <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>

                        <!-- 2. Dokumen BAMB & Tanggal Keluar -->
                        <td class="px-4 py-4">
                            <div class="flex items-center space-x-1.5 font-mono font-bold text-cyan-400 text-xs">
                                <span>📄</span>
                                <span class="truncate" x-text="item.kode || item.nomor_bamb || '-'"></span>
                            </div>
                            <div class="flex items-center space-x-1.5 text-[11px] text-slate-400 mt-1">
                                <span>📅</span>
                                <span class="font-medium" x-text="item.tgl || formatTanggalIndo(item.mutasi_tanggal || item.tgl_raw)"></span>
                            </div>
                            <template x-if="item.nomor_sk_dasar && item.nomor_sk_dasar !== item.kode">
                                <div class="text-[10px] text-slate-500 font-mono mt-1 truncate" title="Nomor SK Dasar Pemindahtanganan">
                                    <span class="text-slate-400">SK:</span> <span class="text-slate-300 font-semibold" x-text="item.nomor_sk_dasar"></span>
                                </div>
                            </template>
                            <!-- Status Penyerahan -->
                            <div class="mt-1.5">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold border"
                                    :class="(item.status || '').toLowerCase().includes('selesai') || (item.status || '').toLowerCase().includes('disahkan')
                                        ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30'
                                        : 'bg-amber-500/10 text-amber-300 border-amber-500/30'">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                        :class="(item.status || '').toLowerCase().includes('selesai') || (item.status || '').toLowerCase().includes('disahkan') ? 'bg-emerald-400' : 'bg-amber-400'"></span>
                                    <span x-text="item.status || 'Disahkan (Selesai)'"></span>
                                </span>
                            </div>
                        </td>

                        <!-- 3. Instansi / SKPD Penerima (Tujuan) -->
                        <td class="px-4 py-4">
                            <div class="flex items-start space-x-2">
                                <span class="text-base text-cyan-400 shrink-0 mt-0.5">🏛️</span>
                                <div class="min-w-0">
                                    <div class="font-bold text-white text-xs leading-snug" x-text="item.opd_tujuan || 'SKPD / Instansi Luar'"></div>
                                    <template x-if="item.alamat_instansi">
                                        <div class="text-[10.5px] text-slate-400 flex items-center space-x-1 mt-0.5">
                                            <span class="text-slate-500">📍</span>
                                            <span class="truncate" x-text="item.alamat_instansi"></span>
                                        </div>
                                    </template>
                                    <!-- Pejabat Penerima Pihak Kedua -->
                                    <div class="mt-2 pt-1.5 border-t border-slate-800/60 text-[11px] space-y-0.5">
                                        <div class="text-slate-400 text-[10px] uppercase font-bold tracking-wider">Penerima (Pihak Kedua):</div>
                                        <div class="font-semibold text-cyan-200" x-text="item.pejabat_opd_tujuan || item.pj_tujuan_nama || 'Pejabat Penerima OPD'"></div>
                                        <template x-if="item.nip_pejabat_opd_tujuan && item.nip_pejabat_opd_tujuan !== '-'">
                                            <div class="text-[10px] font-mono text-slate-400" x-text="'NIP. ' + item.nip_pejabat_opd_tujuan"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- 4. Aset RSUD yang Ditransfer -->
                        <td class="px-4 py-4">
                            <div class="font-bold text-white text-sm" x-text="item.nama_murni || item.nama_barang || item.nama"></div>
                            <div class="text-[11px] font-mono text-cyan-400 font-medium mt-0.5" x-text="'Kode 108: ' + (item.kode_108 || item.kode_barang || '-')"></div>
                            
                            <!-- KIB Badge & Sub-rincian -->
                            <div class="flex items-center space-x-1.5 mt-1.5 flex-wrap gap-y-1">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold whitespace-nowrap leading-none shrink-0 shadow-sm"
                                    :class="{
                                        'bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-amber-500/10': item.category === 'KIB A',
                                        'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-cyan-500/10': item.category === 'KIB B',
                                        'bg-purple-500/20 text-purple-300 border border-purple-500/40 shadow-purple-500/10': item.category === 'KIB C',
                                        'bg-teal-500/20 text-teal-300 border border-teal-500/40 shadow-teal-500/10': item.category === 'KIB D',
                                        'bg-orange-500/20 text-orange-300 border border-orange-500/40 shadow-orange-500/10': item.category === 'KIB E',
                                        'bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-rose-500/10': item.category === 'KIB F',
                                        'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 shadow-indigo-500/10': item.category === 'ATB',
                                        'bg-amber-400/20 text-amber-300 border border-amber-400/40 shadow-amber-400/10': item.category === 'EXTRACOM'
                                    }"
                                    x-text="item.category === 'ATB' ? 'ATB' : (item.category === 'EXTRACOM' ? 'Extracom' : (item.category || 'KIB B'))"></span>

                                <span class="text-[11px] text-slate-400 truncate font-medium" x-text="item.jenis_aset_nama"></span>
                            </div>

                            <!-- Ruangan Asal di RSUD Dr. H. Koesnadi -->
                            <div class="mt-2 pt-1.5 border-t border-slate-800/70 flex items-center space-x-1.5 text-[11px] text-slate-300">
                                <span class="text-rose-400 text-xs">🏥</span>
                                <span class="text-slate-400 text-[10px]">Ruangan Asal RSUD:</span>
                                <span class="font-semibold text-rose-200" x-text="item.ruangan_asal || 'RSUD Dr. H. Koesnadi'"></span>
                            </div>

                            <!-- Chips NIBAR Register jika ada -->
                            <template x-if="item.items && item.items.length > 0">
                                <div class="mt-1.5 flex flex-wrap gap-1">
                                    <template x-for="(sub, sIdx) in item.items.slice(0, 3)" :key="sIdx">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-mono bg-slate-950 border border-slate-800 text-slate-300"
                                            :title="'NIBAR: ' + (sub.nibar || '-')">
                                            <span class="text-cyan-400 mr-0.5">#</span>
                                            <span x-text="sub.nibar && sub.nibar !== '-' ? sub.nibar : ('Unit ' + (sIdx + 1))"></span>
                                        </span>
                                    </template>
                                    <template x-if="item.items.length > 3">
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9.5px] font-mono bg-slate-900 text-slate-400"
                                            x-text="'+' + (item.items.length - 3) + ' lainnya'"></span>
                                    </template>
                                </div>
                            </template>
                        </td>

                        <!-- 5. Volume & Nilai Aset -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-slate-800 text-teal-300 font-semibold font-mono text-xs whitespace-nowrap mb-1.5">
                                <span>📏</span>
                                <span x-text="(item.jumlah_volume || item.item_count || 1) + ' ' + (item.satuan || 'Aset')"></span>
                            </div>
                            <div class="font-mono font-extrabold text-emerald-400 text-xs whitespace-nowrap"
                                x-text="item.nilai_perolehan_formatted || formatRupiah(item.nilai_perolehan)"></div>
                            <div class="text-[10px] text-slate-500 font-medium mt-0.5">Nilai Buku / Perolehan</div>
                        </td>

                        <!-- 6. Kondisi Aset Terkini -->
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <div x-data="{ get st() { return getKondisiStats(item); } }">
                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-[11px] font-bold border shadow-sm select-none"
                                      :class="st.badge_class">
                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="st.dot_class"></span>
                                    <span x-text="st.kondisi_dominan"></span>
                                </span>
                            </div>
                        </td>

                        <!-- 7. Sifat & Keterangan Mutasi -->
                        <td class="px-4 py-4">
                            <!-- Badge Sifat Mutasi: Permanen vs Pinjam Pakai -->
                            <div class="mb-1.5">
                                <template x-if="item.tgl_estimasi_kembali || (item.jenis && item.jenis.toLowerCase().includes('pinjam'))">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                        ⏳ Pinjam Pakai Sementara
                                    </span>
                                </template>
                                <template x-if="!item.tgl_estimasi_kembali && (!item.jenis || !item.jenis.toLowerCase().includes('pinjam'))">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-[10px] font-extrabold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30">
                                        🔄 Pemindahtanganan Permanen
                                    </span>
                                </template>
                            </div>

                            <template x-if="item.tgl_estimasi_kembali">
                                <div class="text-[10.5px] text-amber-300/90 font-mono mb-1">
                                    Estimasi Kembali: <span class="font-bold" x-text="formatTanggalIndo(item.tgl_estimasi_kembali)"></span>
                                </div>
                            </template>

                            <div class="text-[11px] text-slate-400 line-clamp-2 leading-relaxed"
                                :title="item.alasan_mutasi || 'Pemindahtanganan aset RSUD Dr. H. Koesnadi ke SKPD luar.'"
                                x-text="item.alasan_mutasi || 'Pemindahtanganan aset RSUD Dr. H. Koesnadi ke SKPD luar.'"></div>
                        </td>

                        <!-- 8. Aksi (Detail, Cetak BAST, Hapus) — FREEZE STICKY RIGHT -->
                        <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[220px] w-[220px]" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- 1. Tombol Detail -->
                                <button type="button" @click="openDetail(item)"
                                    title="Lihat Detail BAST Serah Terima Keluar"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-cyan-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Detail</span>
                                </button>

                                <!-- 2. Tombol Cetak BAST Serah Terima Keluar -->
                                <button type="button" @click="openPrintModal(item)"
                                    title="Cetak Dokumen BAST Serah Terima Keluar"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-purple-500/10 hover:bg-purple-600 text-purple-300 hover:text-white border border-purple-500/30 hover:border-purple-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-purple-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-purple-400 group-hover/btn:text-white group-hover/btn:rotate-6 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h6z"/>
                                    </svg>
                                    <span>Cetak</span>
                                </button>

                                <!-- 3. Tombol Hapus / Batalkan Transfer -->
                                <button type="button" @click="deleteMutasi(item)"
                                    title="Hapus Data Mutasi Keluar (Pindahkan ke Tong Sampah)"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 hover:border-rose-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-rose-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-rose-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    <span>Hapus</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- 1. Empty State jika BELUM ADA DATA Mutasi Keluar sama sekali -->
                <template x-if="countKeluar === 0">
                    <tr>
                        <td colspan="8" class="text-center py-16 text-slate-400">
                            <div class="flex flex-col items-center justify-center space-y-4 max-w-md mx-auto">
                                <div class="w-16 h-16 rounded-3xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-3xl shadow-inner text-cyan-400">
                                    📤
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-base font-bold text-white">Belum Ada Data Mutasi Eksternal Keluar</h4>
                                    <p class="text-xs text-slate-400 leading-relaxed">
                                        Belum tercatat adanya penyerahan / transfer aset dari <strong class="text-cyan-300 font-semibold">RSUD Dr. H. Koesnadi</strong> ke SKPD atau Instansi luar di lingkungan Pemkab Bondowoso.
                                    </p>
                                </div>
                                <div class="pt-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] text-slate-400">
                                        <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                                        Siap mencatat BAST pemindahtanganan aset keluar
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- 2. Empty State jika ada data tetapi tidak cocok dengan filter / pencarian -->
                <template x-if="countKeluar > 0 && filteredMutasis.length === 0">
                    <tr>
                        <td colspan="8" class="text-center align-middle py-28 text-slate-400">
                            <div class="flex flex-col items-center justify-center space-y-2 py-4">
                                <p class="text-sm font-semibold text-slate-300">Tidak ada data transfer aset keluar yang cocok dengan filter atau pencarian Anda.</p>
                                <p class="text-xs text-slate-500">Coba ubah kata kunci atau reset filter pencarian.</p>
                                <div class="pt-2">
                                    <button type="button" @click="resetFilters()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-cyan-300 font-bold border border-slate-700 transition-all cursor-pointer">
                                        🔄 Reset Filter
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
