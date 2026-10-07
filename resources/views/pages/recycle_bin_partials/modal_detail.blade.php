{{-- ========================================================================= --}}
{{-- MODAL DETAIL PREVIEW (DYNAMIC FOR ALL 8 MODULES)                         --}}
{{-- ========================================================================= --}}
<div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto modal-backdrop-full"
    style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(32px); -webkit-backdrop-filter: blur(32px);"
    @click.self="showDetailModal = false" x-cloak>
    <div class="border border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto my-auto"
        style="background-color: #0f172a;">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2">
                <span class="text-red-400 font-bold text-lg">🗑️</span>
                <h3 class="text-base font-extrabold text-white" x-text="'Detail Data Terhapus: ' + activeModuleName"></h3>
            </div>
            <button type="button" @click="showDetailModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <template x-if="selectedItem">
            <div class="space-y-4 text-xs">
                {{-- Banner Info Penghapusan --}}
                <div class="p-4 bg-red-950/60 border border-red-500/50 rounded-2xl flex items-start space-x-3 text-red-200 shadow-xl">
                    <div class="p-2 rounded-xl bg-red-500/20 text-red-400 text-lg shrink-0 flex items-center justify-center">
                        ⚠️
                    </div>
                    <div class="flex-1 text-xs space-y-1">
                        <p class="font-extrabold text-red-300 text-sm">Status Data: Terhapus Sementara</p>
                        <p class="text-slate-300">Dihapus oleh: <strong class="text-white" x-text="selectedItem.deleted_by"></strong></p>
                        <p class="text-slate-400 text-[11px]">Waktu Penghapusan: <span class="font-mono text-slate-200" x-text="selectedItem.deleted_at"></span></p>
                    </div>
                </div>

                <!-- 1. DETAIL KHUSUS MUTASI (INTERNAL & EKSTERNAL) -->
                <template x-if="activeModule === 'mutasi'">
                    <div class="space-y-3">
                        <!-- Mode 1: Eksternal -->
                        <template x-if="selectedItem && (selectedItem.is_eksternal || mutasiSubTab === 'eksternal')">
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                                    <div>
                                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Nomor BAST Eksternal:</span>
                                        <span class="font-mono font-bold text-cyan-300 text-sm" x-text="selectedItem.kode"></span>
                                        <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-cyan-950/70 border border-cyan-800/50 text-cyan-300 inline-block mt-1" x-text="selectedItem.jenis"></span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Nilai Perolehan:</span>
                                        <span class="font-mono font-bold text-emerald-300 text-sm" x-text="selectedItem.nilai_perolehan_formatted || '-'"></span>
                                        <span class="text-[10.5px] text-slate-400 block" x-text="(selectedItem.item_count || 1) + ' Unit Barang'"></span>
                                    </div>
                                    <div class="mt-2">
                                        <span class="text-slate-500 text-[10px] block">Instansi / OPD Asal:</span>
                                        <span class="font-semibold text-slate-200" x-text="selectedItem.asal"></span>
                                        <span class="text-slate-500 text-[10px] block" x-text="'PJ: ' + (selectedItem.pemohon || '-')"></span>
                                    </div>
                                    <div class="mt-2 text-right">
                                        <span class="text-slate-500 text-[10px] block">Ruangan Tujuan di RSUD:</span>
                                        <span class="font-semibold text-cyan-300" x-text="selectedItem.tujuan"></span>
                                        <span class="text-slate-500 text-[10px] block" x-text="'PJ: ' + (selectedItem.penerima_pj || '-')"></span>
                                    </div>
                                    <div class="col-span-2 pt-2 border-t border-slate-800/80">
                                        <span class="text-slate-500 text-[10px] block">Alasan / Catatan Pelimpahan:</span>
                                        <span class="text-xs text-slate-300 italic" x-text="selectedItem.alasan_mutasi || '-'"></span>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <!-- Mode 2: Internal -->
                        <template x-if="selectedItem && !selectedItem.is_eksternal && mutasiSubTab !== 'eksternal'">
                            <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                                <div>
                                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Nomor BAMB:</span>
                                    <span class="font-mono font-bold text-amber-300 text-sm" x-text="selectedItem.kode"></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Jenis Mutasi:</span>
                                    <span class="font-bold text-white" x-text="selectedItem.jenis"></span>
                                </div>
                                <div class="mt-2">
                                    <span class="text-slate-500 text-[10px] block">Ruangan Asal:</span>
                                    <span class="font-semibold text-slate-200" x-text="selectedItem.asal"></span>
                                    <span class="text-slate-500 text-[10px] block" x-text="'PJ: ' + (selectedItem.pemohon || '-')"></span>
                                </div>
                                <div class="mt-2 text-right">
                                    <span class="text-slate-500 text-[10px] block">Ruangan Tujuan:</span>
                                    <span class="font-semibold text-indigo-300" x-text="selectedItem.tujuan"></span>
                                    <span class="text-slate-500 text-[10px] block" x-text="'PJ: ' + (selectedItem.penerima_pj || '-')"></span>
                                </div>
                            </div>
                        </template>

                        <!-- Tabel Rincian Barang -->
                        <div class="space-y-1.5">
                            <span class="text-slate-400 text-[10.5px] font-bold uppercase tracking-wider block">Rincian Barang Terkait:</span>
                            <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-44 overflow-y-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-center w-8">No</th>
                                            <th class="px-3 py-2">Nama Barang / Aset</th>
                                            <th class="px-3 py-2 font-mono">NIBAR</th>
                                            <th class="px-3 py-2 text-center">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60">
                                        <template x-for="(it, idx) in selectedItem.items" :key="idx">
                                            <tr class="hover:bg-slate-900/40">
                                                <td class="px-3 py-2 text-center text-slate-500 font-bold" x-text="idx + 1"></td>
                                                <td class="px-3 py-2 font-bold text-white" x-text="it.nama_barang"></td>
                                                <td class="px-3 py-2 font-mono text-cyan-400 text-[11px]" x-text="it.nibar"></td>
                                                <td class="px-3 py-2 text-center" x-text="it.kondisi || 'Baik'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- 2. DETAIL KHUSUS MASTER ASTAP (PAKET PENGADAAN) -->
                <template x-if="activeModule === 'astap' && astapSubTab === 'packet'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Nama Aset:</span>
                                <span class="font-bold text-white text-sm" x-text="selectedItem.nama"></span>
                                <span class="text-xs text-cyan-400 font-mono" x-text="'Kode 108: ' + selectedItem.kode"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Total Realisasi:</span>
                                <span class="font-mono font-bold text-amber-300 text-sm" x-text="selectedItem.total_realisasi"></span>
                                <span class="text-xs text-slate-400 block" x-text="'Volume: ' + selectedItem.volume"></span>
                            </div>
                            <div class="mt-2">
                                <span class="text-slate-500 text-[10px] block">Penyedia:</span>
                                <span class="font-semibold text-slate-200" x-text="selectedItem.penyedia"></span>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-slate-500 text-[10px] block">Nomor Dokumen SPK:</span>
                                <span class="font-mono font-semibold text-slate-300" x-text="selectedItem.spk_nomor"></span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <span class="text-slate-400 text-[10.5px] font-bold uppercase tracking-wider block">Daftar Register NIBAR Terkait:</span>
                            <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-44 overflow-y-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-center w-8">No</th>
                                            <th class="px-3 py-2 font-mono">NIBAR</th>
                                            <th class="px-3 py-2">Ruangan Pemegang</th>
                                            <th class="px-3 py-2 text-center">Kondisi</th>
                                            <th class="px-3 py-2 text-center">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60">
                                        <template x-for="(r, idx) in selectedItem.items" :key="idx">
                                            <tr class="hover:bg-slate-900/40">
                                                <td class="px-3 py-2 text-center text-slate-500 font-bold" x-text="idx + 1"></td>
                                                <td class="px-3 py-2 font-mono font-bold text-cyan-300" x-text="r.nibar"></td>
                                                <td class="px-3 py-2 text-slate-300" x-text="r.ruang"></td>
                                                <td class="px-3 py-2 text-center" x-text="r.kondisi"></td>
                                                <td class="px-3 py-2 text-center text-[10px] text-slate-400" x-text="r.status"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- 2B. DETAIL KHUSUS NIBAR INDIVIDUAL -->
                <template x-if="activeModule === 'astap' && astapSubTab === 'nibar'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Nomor Induk Barang (NIBAR):</span>
                                <span class="font-mono font-bold text-cyan-300 text-sm tracking-wide" x-text="selectedItem.nibar"></span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="'No Register: ' + selectedItem.no_register"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Kondisi & Status:</span>
                                <span class="px-2 py-0.5 rounded text-xs font-bold inline-block"
                                    :class="selectedItem.kondisi === 'Baik' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : (selectedItem.kondisi === 'Rusak Berat' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40')"
                                    x-text="selectedItem.kondisi"></span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="'Status: ' + selectedItem.status"></span>
                            </div>
                            <div class="mt-2">
                                <span class="text-slate-500 text-[10px] block">Aset Induk (Pengadaan):</span>
                                <span class="font-semibold text-slate-200" x-text="selectedItem.nama_barang"></span>
                                <span class="text-[10px] text-cyan-400 font-mono block" x-text="'Kode 108: ' + selectedItem.kode_108 + ' (' + selectedItem.kategori + ')'"></span>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-slate-500 text-[10px] block">Ruangan Pemegang:</span>
                                <span class="font-semibold text-white text-xs" x-text="selectedItem.ruang"></span>
                                <span class="text-[10px] text-slate-400 block" x-text="'Tahun: ' + selectedItem.tahun + ' | SPK: ' + selectedItem.spk_nomor"></span>
                            </div>
                        </div>
                        <div class="p-3 bg-blue-500/10 border border-blue-500/20 rounded-2xl text-blue-300 text-xs flex items-center space-x-2">
                            <span class="text-base">💡</span>
                            <span>Memulihkan NIBAR ini akan mengembalikan data unit ke paket pengadaan aset induk (<span class="font-bold text-white" x-text="selectedItem.nama_barang"></span>) dan menambah volume aktif sebesar +1 unit.</span>
                        </div>
                    </div>
                </template>

                <!-- 3. DETAIL KHUSUS DISTRIBUSI -->
                <template x-if="activeModule === 'distribusi'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Kode Distribusi:</span>
                                <span class="font-mono font-bold text-cyan-300 text-sm" x-text="selectedItem.kode"></span>
                                <span class="text-[10px] text-slate-400 block" x-text="'BAST: ' + selectedItem.bast_nomor"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Unit / Ruangan Tujuan:</span>
                                <span class="font-bold text-white" x-text="selectedItem.tujuan"></span>
                                <span class="text-xs text-slate-400 block" x-text="'Tgl Pengajuan: ' + (selectedItem.tanggal || '-')"></span>
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <span class="text-slate-400 text-[10.5px] font-bold uppercase tracking-wider block">Item Barang Distribusi:</span>
                            <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-44 overflow-y-auto">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0">
                                        <tr>
                                            <th class="px-3 py-2 text-center w-8">No</th>
                                            <th class="px-3 py-2">Nama Barang</th>
                                            <th class="px-3 py-2">Volume</th>
                                            <th class="px-3 py-2">NIBAR Terkait</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60">
                                        <template x-for="(it, idx) in selectedItem.items" :key="idx">
                                            <tr class="hover:bg-slate-900/40">
                                                <td class="px-3 py-2 text-center text-slate-500 font-bold" x-text="idx + 1"></td>
                                                <td class="px-3 py-2 font-bold text-white" x-text="it.nama_barang"></td>
                                                <td class="px-3 py-2 text-teal-300 font-bold" x-text="it.qty"></td>
                                                <td class="px-3 py-2 font-mono text-cyan-400 text-[11px]" x-text="it.nibar_list"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- 4. DETAIL KHUSUS UNIT -->
                <template x-if="activeModule === 'unit'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Kode Unit:</span>
                                <span class="font-mono font-bold text-indigo-300 text-sm" x-text="selectedItem.kode"></span>
                                <span class="text-slate-200 font-bold text-base block mt-1" x-text="selectedItem.nama"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Tipe Ruangan:</span>
                                <span class="font-bold text-white" x-text="selectedItem.tipe"></span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="'Total Aset: ' + selectedItem.total_aset + ' Barang'"></span>
                            </div>
                            <div class="mt-2">
                                <span class="text-slate-500 text-[10px] block">Kepala Ruangan:</span>
                                <span class="font-semibold text-slate-200" x-text="selectedItem.kepala"></span>
                                <span class="text-[10px] text-slate-500 block" x-text="'NIP: ' + selectedItem.nip"></span>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-slate-500 text-[10px] block">Akun Sub Admin:</span>
                                <span class="font-mono text-cyan-400 text-xs" x-text="selectedItem.email"></span>
                            </div>
                        </div>

                        <!-- PERINGATAN JIKA UNIT MEMILIKI ASET -->
                        <template x-if="selectedItem.total_aset > 0">
                            <div class="p-3 bg-amber-500/15 border border-amber-500/30 rounded-2xl flex items-center space-x-2.5 text-amber-300 text-xs font-semibold">
                                <span class="text-base">⚠️</span>
                                <span>Perhatian: Unit ini masih tercatat menampung <strong class="text-white" x-text="selectedItem.total_aset"></strong> aset inventaris aktif. Pemulihan unit ini akan menyambungkan kembali data lokasi aset terkait.</span>
                            </div>
                        </template>

                        <!-- PERINGATAN JIKA UNIT MEMILIKI ARSIP BAST -->
                        <template x-if="selectedItem.total_bast > 0">
                            <div class="p-3 bg-blue-500/15 border border-blue-500/30 rounded-2xl flex items-center space-x-2.5 text-blue-300 text-xs font-semibold">
                                <span class="text-base">📜</span>
                                <span>Proteksi Audit: Unit ini memiliki <strong class="text-white" x-text="selectedItem.total_bast"></strong> arsip dokumen BAST Distribusi resmi yang dilindungi undang-undang untuk audit BPK & Inspektorat sehingga unit ini tidak dapat dihapus permanen.</span>
                            </div>
                        </template>
                    </div>
                </template>

                <!-- 5. DETAIL KHUSUS HIBAH ASET -->
                <template x-if="activeModule === 'hibah'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Nomor BAST:</span>
                                <span class="font-mono font-bold text-cyan-300 text-sm tracking-wide" x-text="selectedItem.nomor_bast"></span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="'Tanggal BAST: ' + selectedItem.tanggal_bast"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Jenis Hibah:</span>
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold inline-block"
                                    :class="selectedItem.tipe_hibah === 'masuk' ? 'bg-amber-400/20 text-amber-300 border border-amber-400/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'"
                                    x-text="selectedItem.tipe_hibah === 'masuk' ? '🎁 Hibah Masuk' : '📤 Hibah Keluar'"></span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="selectedItem.triwulan + ' ' + selectedItem.tahun"></span>
                            </div>
                            <div class="mt-2">
                                <span class="text-slate-500 text-[10px] block" x-text="selectedItem.tipe_hibah === 'masuk' ? 'Pemberi Hibah:' : 'Penerima Hibah:'"></span>
                                <span class="font-semibold text-slate-200" x-text="selectedItem.pihak_hibah"></span>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-slate-500 text-[10px] block">Nilai Aset:</span>
                                <span class="font-mono font-bold text-amber-300" x-text="selectedItem.nilai_aset_rp"></span>
                                <span class="text-xs text-slate-400 block" x-text="'Volume: ' + selectedItem.volume"></span>
                            </div>
                        </div>
                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-[10px] text-slate-500 block uppercase font-bold">Barang Yang Dihibahkan:</span>
                            <p class="font-bold text-white text-xs" x-text="selectedItem.nama_barang"></p>
                            <p class="font-mono text-[10px] text-cyan-400" x-text="'Kode 108: ' + selectedItem.kode_barang"></p>
                        </div>
                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-[10px] text-slate-500 block uppercase font-bold">Alasan Penghapusan:</span>
                            <p class="text-xs text-rose-300 font-semibold" x-text="selectedItem.alasan_hapus"></p>
                            <p class="text-[11px] text-slate-400 mt-1" x-show="selectedItem.keterangan && selectedItem.keterangan !== '-'" x-text="'Catatan BAST: ' + selectedItem.keterangan"></p>
                        </div>
                    </div>
                </template>

                <!-- 6. DETAIL KHUSUS KEMITRAAN ASET -->
                <template x-if="activeModule === 'kemitraan'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Nomor Dokumen PKS:</span>
                                <span class="font-mono font-bold text-cyan-300 text-sm tracking-wide" x-text="selectedItem.nomor_pks"></span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="'Tanggal PKS: ' + selectedItem.tanggal_pks"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Skema Kerja Sama:</span>
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-cyan-950/80 border border-cyan-800/60 text-cyan-300 inline-block"
                                    x-text="'🤝 ' + selectedItem.skema_kemitraan"></span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="'Status: ' + selectedItem.status_konsesi"></span>
                            </div>
                            <div class="mt-2">
                                <span class="text-slate-500 text-[10px] block">Rekanan Mitra Kerja Sama:</span>
                                <span class="font-bold text-white text-sm" x-text="selectedItem.mitra_nama"></span>
                                <span class="text-[10px] text-slate-400 block" x-text="'Penempatan: ' + selectedItem.ruangan"></span>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-slate-500 text-[10px] block">Nilai Wajar Aset (Akun 1.5.2):</span>
                                <span class="font-mono font-bold text-emerald-400" x-text="selectedItem.nilai_aset_rp"></span>
                                <span class="text-xs text-teal-300 font-mono block" x-text="'Volume: ' + selectedItem.volume"></span>
                            </div>
                            <div class="col-span-2 pt-2 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                                <span>Periode Konsesi: <strong class="text-white" x-text="selectedItem.tanggal_mulai"></strong> s/d <strong class="text-white" x-text="selectedItem.tanggal_selesai"></strong></span>
                                <span x-text="selectedItem.triwulan + ' ' + selectedItem.tahun"></span>
                            </div>
                        </div>

                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-[10px] text-slate-500 block uppercase font-bold">Aset Yang Dikerjasamakan:</span>
                            <p class="font-bold text-white text-xs" x-text="selectedItem.nama_barang"></p>
                            <p class="font-mono text-[10px] text-cyan-400" x-text="'Kode 108: ' + selectedItem.kode_barang"></p>
                        </div>

                        <!-- TABEL REGISTER UNIT BARANG KEMITRAAN -->
                        <template x-if="selectedItem.registers && selectedItem.registers.length > 0">
                            <div class="space-y-1.5">
                                <span class="text-slate-400 text-[10.5px] font-bold uppercase tracking-wider block">Rincian Register NIBAR Terkait:</span>
                                <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-40 overflow-y-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0">
                                            <tr>
                                                <th class="px-3 py-2 text-center w-8">No</th>
                                                <th class="px-3 py-2">NIBAR</th>
                                                <th class="px-3 py-2">Ruangan</th>
                                                <th class="px-3 py-2 text-center">Kondisi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-800/60">
                                            <template x-for="(reg, rIdx) in selectedItem.registers" :key="rIdx">
                                                <tr class="hover:bg-slate-900/40">
                                                    <td class="px-3 py-2 text-center text-slate-500 font-bold" x-text="reg.no"></td>
                                                    <td class="px-3 py-2 font-mono font-bold text-cyan-300" x-text="reg.nibar"></td>
                                                    <td class="px-3 py-2 text-slate-200" x-text="reg.ruangan"></td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30" x-text="reg.kondisi"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-[10px] text-slate-500 block uppercase font-bold">Alasan Penghapusan:</span>
                            <p class="text-xs text-rose-300 font-semibold" x-text="selectedItem.alasan_hapus"></p>
                            <p class="text-[11px] text-slate-400 mt-1" x-show="selectedItem.keterangan && selectedItem.keterangan !== '-'" x-text="'Catatan PKS: ' + selectedItem.keterangan"></p>
                        </div>
                    </div>
                </template>

                <!-- 7. DETAIL KHUSUS BELANJA BARANG -->
                <template x-if="activeModule === 'belanja_barang'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Nomor Faktur Pembelian:</span>
                                <span class="font-mono font-bold text-teal-300 text-sm tracking-wide" x-text="selectedItem.nomor_faktur"></span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="'Tanggal Faktur: ' + selectedItem.tanggal_faktur"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Sumber Belanja:</span>
                                <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-950/80 border border-emerald-800/60 text-emerald-300 inline-block">
                                    🛒 Akun 5.1.02
                                </span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="'Tahun: ' + selectedItem.tahun + ' (' + selectedItem.triwulan + ')'"></span>
                            </div>
                            <div class="mt-2">
                                <span class="text-slate-500 text-[10px] block">Toko / Rekanan Penyedia:</span>
                                <span class="font-bold text-white text-sm" x-text="selectedItem.toko_penyedia"></span>
                                <span class="text-[10px] text-indigo-300 block" x-text="'Lokasi Penempatan: ' + selectedItem.ruangan"></span>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-slate-500 text-[10px] block">Total Pembelian:</span>
                                <span class="font-mono font-bold text-emerald-400" x-text="selectedItem.total_pembelian_rp"></span>
                                <span class="text-xs text-teal-300 font-mono block" x-text="'Volume: ' + selectedItem.volume"></span>
                            </div>
                        </div>

                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-[10px] text-slate-500 block uppercase font-bold">Barang Belanja / Perbekalan:</span>
                            <p class="font-bold text-white text-xs" x-text="selectedItem.nama_barang"></p>
                            <p class="font-mono text-[10px] text-cyan-400" x-text="'Kode 108: ' + selectedItem.kode_barang"></p>
                        </div>

                        <!-- TABEL REGISTER UNIT BARANG BELANJA -->
                        <template x-if="selectedItem.registers && selectedItem.registers.length > 0">
                            <div class="space-y-1.5">
                                <span class="text-slate-400 text-[10.5px] font-bold uppercase tracking-wider block">Rincian Register NIBAR Terkait:</span>
                                <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-40 overflow-y-auto">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-900 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0">
                                            <tr>
                                                <th class="px-3 py-2 text-center w-8">No</th>
                                                <th class="px-3 py-2">NIBAR</th>
                                                <th class="px-3 py-2">Ruangan</th>
                                                <th class="px-3 py-2 text-center">Kondisi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-800/60">
                                            <template x-for="(reg, rIdx) in selectedItem.registers" :key="rIdx">
                                                <tr class="hover:bg-slate-900/40">
                                                    <td class="px-3 py-2 text-center text-slate-500 font-bold" x-text="reg.no"></td>
                                                    <td class="px-3 py-2 font-mono font-bold text-teal-300" x-text="reg.nibar"></td>
                                                    <td class="px-3 py-2 text-slate-200" x-text="reg.ruangan"></td>
                                                    <td class="px-3 py-2 text-center">
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30" x-text="reg.kondisi"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </template>

                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-[10px] text-slate-500 block uppercase font-bold">Alasan Penghapusan:</span>
                            <p class="text-xs text-rose-300 font-semibold" x-text="selectedItem.alasan_hapus"></p>
                            <p class="text-[11px] text-slate-400 mt-1" x-show="selectedItem.keterangan && selectedItem.keterangan !== '-'" x-text="'Catatan Belanja: ' + selectedItem.keterangan"></p>
                        </div>
                    </div>
                </template>

                <!-- 8. DETAIL PENGGUNA -->
                <template x-if="activeModule === 'users'">
                    <div class="space-y-3">
                        <div class="p-4 bg-slate-950/80 border border-slate-800 rounded-2xl grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Nama Lengkap & NIP:</span>
                                <span class="text-white font-bold text-base block mt-1" x-text="selectedItem.name"></span>
                                <span class="text-[10px] font-mono text-slate-400" x-text="'NIP: ' + selectedItem.nip"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-500 text-[10px] uppercase font-bold block">Role Otorisasi:</span>
                                <span class="font-bold text-amber-300" x-text="selectedItem.role_label || selectedItem.role"></span>
                                <span class="text-xs text-slate-400 block mt-1" x-text="'Status: ' + selectedItem.status"></span>
                            </div>
                            <div class="mt-2">
                                <span class="text-slate-500 text-[10px] block">Unit Penugasan:</span>
                                <span class="font-semibold text-slate-200" x-text="selectedItem.unit || '-'"></span>
                                <span class="text-[10px] text-slate-400 block" x-text="selectedItem.penugasan || '-'"></span>
                            </div>
                            <div class="mt-2 text-right">
                                <span class="text-slate-500 text-[10px] block">Email Kredensial:</span>
                                <span class="font-mono text-cyan-400 text-xs" x-text="selectedItem.email"></span>
                            </div>
                        </div>
                    </div>
                </template>

            </div>
        </template>

        <div class="flex items-center justify-between pt-4 border-t border-slate-800">
            <div class="flex items-center space-x-2">
                <template x-if="selectedItem">
                    <button type="button" @click="restoreSingle(currentTargetModule, selectedItem); showDetailModal = false;"
                        class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500/30 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                        <span>♻️ Pulihkan Data Ini</span>
                    </button>
                </template>
            </div>
            <button type="button" @click="showDetailModal = false" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">Tutup</button>
        </div>
    </div>
</div>
