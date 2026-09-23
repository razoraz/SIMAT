<!-- MODAL DETAIL BAST MUTASI EKSTERNAL (ANTAR-OPD) -->
<div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 50;" @click.self="showDetailModal = false" x-cloak>
    <div class="border border-slate-800 rounded-3xl max-w-3xl w-full p-6 sm:p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto my-auto" style="background-color: #0f172a;">
        
        <!-- Header Modal -->
        <div class="flex items-center justify-between pb-4 border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 flex items-center justify-center text-xl font-black">
                    🏛️
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima (BAST) Antar-OPD</h3>
                    <p class="text-xs text-slate-400">Pengalihan Barang Milik Daerah (BMD) Pemerintah Kabupaten Bondowoso</p>
                </div>
            </div>
            <button type="button" @click.stop="showDetailModal = false" class="text-slate-500 hover:text-white text-2xl font-bold cursor-pointer transition-colors">&times;</button>
        </div>
        
        <template x-if="selectedMutasi">
            <div class="space-y-5 text-xs">
                
                <!-- Info Nomor & Status Strip -->
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <span class="text-slate-400 text-[10.5px] uppercase font-bold tracking-wider block">Nomor BAST Resmi:</span>
                        <p class="font-mono font-black text-indigo-300 text-sm mt-0.5" x-text="selectedMutasi.kode"></p>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[10.5px] uppercase font-bold tracking-wider block">Nilai Perolehan:</span>
                        <p class="font-mono font-black text-emerald-400 text-sm mt-0.5" x-text="selectedMutasi.nilai_perolehan_formatted || selectedMutasi.jumlah_realisasi || ('Rp ' + Number(selectedMutasi.nilai_perolehan || 0).toLocaleString('id-ID'))"></p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 rounded-xl text-xs font-extrabold border shadow-sm"
                            :class="{
                                'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': (selectedMutasi.status || '').includes('Selesai') || (selectedMutasi.status || '').includes('Disahkan'),
                                'bg-cyan-500/20 text-cyan-300 border-cyan-500/40':         (selectedMutasi.status || '').includes('Peminjaman'),
                                'bg-amber-500/20 text-amber-300 border-amber-500/40':     (selectedMutasi.status || '').includes('Menunggu')
                            }"
                            x-text="selectedMutasi.status"></span>
                        <span class="px-2.5 py-1 rounded-xl bg-slate-900 text-slate-300 border border-slate-800 text-[11px] font-mono" x-text="selectedMutasi.tgl"></span>
                    </div>
                </div>

                <!-- 2 Pihak yang Terlibat (Pengirim & Penerima) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Pihak Pertama (Pengirim: SKPD Pengirim) -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                        <div class="flex items-center space-x-2 border-b border-slate-800 pb-2">
                            <span class="text-xs">📤</span>
                            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">PIHAK PERTAMA (SKPD PENGIRIM)</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">Instansi Asal Pelimpahan:</span>
                            <p class="font-bold text-white text-xs" x-text="selectedMutasi.opd_asal"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">Pejabat / Pihak yang Menyerahkan:</span>
                            <p class="text-emerald-400 font-bold" x-text="selectedMutasi.pj_asal_nama"></p>
                            <p class="text-[10px] text-slate-400 font-mono" x-text="'NIP: ' + selectedMutasi.pj_asal_nip"></p>
                            <p class="text-[10px] text-slate-500" x-text="selectedMutasi.pj_asal_jabatan"></p>
                        </div>
                    </div>

                    <!-- Pihak Kedua (Penerima: RSUD Dr. H. Koesnadi) -->
                    <div class="p-4 rounded-2xl bg-indigo-950/20 border border-indigo-500/30 space-y-2">
                        <div class="flex items-center space-x-2 border-b border-indigo-500/30 pb-2">
                            <span class="text-xs">📥</span>
                            <span class="text-xs font-bold text-indigo-300 uppercase tracking-wider">PIHAK KEDUA (PENERIMA RSUD)</span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">Instansi Penerima & Ruangan Baru:</span>
                            <p class="font-bold text-indigo-200 text-xs" x-text="selectedMutasi.opd_tujuan"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">Pejabat Penerima di RSUD:</span>
                            <p class="text-white font-bold" x-text="selectedMutasi.pejabat_opd_tujuan"></p>
                            <p class="text-[10px] text-slate-400 font-mono" x-text="'NIP: ' + selectedMutasi.nip_pejabat_opd_tujuan"></p>
                            <p class="text-[10px] text-indigo-400/90 font-semibold" x-text="selectedMutasi.jabatan_opd_tujuan"></p>
                        </div>
                    </div>
                </div>

                <!-- Dasar Hukum / Dokumen Mutasi BMD -->
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 space-y-1">
                    <span class="text-slate-400 text-[10.5px] uppercase font-bold tracking-wider block">Dasar Hukum & Dokumen Mutasi:</span>
                    <p class="text-white font-semibold text-xs leading-relaxed" x-text="selectedMutasi.nomor_sk_dasar || '-'"></p>
                    <template x-if="selectedMutasi.tgl_estimasi_kembali">
                        <p class="text-amber-400 font-medium text-[11px] pt-1">
                            ⏱️ Jangka Waktu Pinjam Pakai: Sampai dengan tanggal <strong class="font-mono text-white" x-text="selectedMutasi.tgl_estimasi_kembali"></strong>
                        </p>
                    </template>
                </div>

                <!-- Daftar Rincian Barang yang Dimutasi (Table) -->
                <div class="space-y-2">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-300 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                            <span>📦 Daftar Barang Milik Daerah (BMD)</span>
                            <span class="text-indigo-400 font-extrabold" x-text="'(' + selectedMutasi.items.length + ' Unit Aset)'"></span>
                        </span>
                    </div>
                    <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
                                <tr>
                                    <th class="px-3 py-2.5 text-center w-8">No</th>
                                    <th class="px-3 py-2.5">Nama Barang / Aset</th>
                                    <th class="px-3 py-2.5 font-mono">NIBAR</th>
                                    <th class="px-3 py-2.5 font-mono text-center">Kode 108</th>
                                    <th class="px-3 py-2.5 text-center whitespace-nowrap">Kondisi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                <template x-for="(it, idx) in selectedMutasi.items" :key="idx">
                                    <tr class="hover:bg-slate-900/40">
                                        <td class="px-3 py-2.5 text-center font-bold text-slate-500" x-text="idx + 1"></td>
                                        <td class="px-3 py-2.5 font-bold text-white" x-text="it.nama_barang"></td>
                                        <td class="px-3 py-2.5 font-mono text-indigo-300 text-[11px]" x-text="it.nibar"></td>
                                        <td class="px-3 py-2.5 font-mono text-slate-400 text-center text-[10.5px]" x-text="it.kode_108"></td>
                                        <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                            <span class="px-2 py-0.5 rounded text-[9.5px] font-black border whitespace-nowrap inline-block"
                                                :class="{
                                                    'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': it.kondisi === 'Baik',
                                                    'bg-rose-500/20 text-rose-300 border-rose-500/30':         it.kondisi === 'Rusak Berat',
                                                    'bg-amber-500/20 text-amber-300 border-amber-500/30':     it.kondisi.includes('Kurang') || it.kondisi.includes('Ringan')
                                                }" x-text="it.kondisi"></span>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Alasan & Maksud Penyerahan -->
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                    <span class="text-slate-400 text-[10.5px] uppercase font-bold tracking-wider block mb-1">Maksud / Alasan Mutasi Antar-OPD:</span>
                    <p class="text-slate-300 leading-relaxed text-xs" x-text="selectedMutasi.alasan_mutasi || '-'"></p>
                </div>
            </div>
        </template>

        <!-- Footer Modal Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-slate-800">
            <button type="button" @click="showDetailModal = false"
                class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                Tutup
            </button>
            <div class="flex items-center space-x-2">
                <button type="button" @click="alert('Fitur Cetak Dokumen BAST Resmi akan menyusul!')"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-400 hover:to-violet-500 text-white font-extrabold text-xs shadow-lg shadow-indigo-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    <span>Cetak BAST Resmi (PDF)</span>
                </button>
            </div>
        </div>
    </div>
</div>
