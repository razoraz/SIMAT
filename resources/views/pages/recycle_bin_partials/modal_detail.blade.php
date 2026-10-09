{{-- ========================================================================= --}}
{{-- MODAL DETAIL PREVIEW (DYNAMIC FOR ALL 8 MODULES)                         --}}
{{-- Bespoke High-End Dark Dashboard Modal dengan Header & Footer Terpinci      --}}
{{-- ========================================================================= --}}
<div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
    style="background-color: rgba(2, 6, 23, 0.88); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);"
    @click.self="showDetailModal = false" x-cloak>
    <div class="border border-slate-800/90 rounded-3xl max-w-3xl sm:max-w-4xl w-full max-h-[90vh] flex flex-col shadow-2xl relative overflow-hidden my-auto"
        style="background-color: #0f172a;">
        
        <!-- Pinned Header -->
        <div class="shrink-0 flex items-center justify-between px-5 sm:px-6 py-4 border-b border-slate-800/80 bg-slate-900/95 backdrop-blur-md">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 rounded-xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-rose-400 shrink-0 shadow-lg shadow-rose-500/10">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-white leading-tight" x-text="'Detail Data Terhapus: ' + activeModuleName"></h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pratinjau arsip data sebelum dipulihkan ke master atau dimusnahkan permanen</p>
                </div>
            </div>
            <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-white text-2xl font-bold cursor-pointer p-1 transition-colors leading-none">&times;</button>
        </div>

        <!-- Scrollable Body Content -->
        <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4 custom-scrollbar">
            <template x-if="selectedItem">
                <div class="space-y-4 text-xs">
                    {{-- Banner Info Penghapusan --}}
                    <div class="p-4 bg-gradient-to-r from-rose-950/70 via-slate-950 to-slate-950 border border-rose-500/40 rounded-2xl flex items-start space-x-3.5 text-rose-200 shadow-xl">
                        <div class="p-2 rounded-xl bg-rose-500/20 text-rose-400 text-lg shrink-0 flex items-center justify-center border border-rose-500/30">
                            ⚠️
                        </div>
                        <div class="flex-1 text-xs space-y-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                <p class="font-extrabold text-rose-300 text-sm tracking-tight">Status Data: Terhapus Sementara</p>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-900/60 border border-rose-700/50 text-rose-200 uppercase tracking-wider">Recycle Bin</span>
                            </div>
                            <p class="text-slate-300">Dihapus oleh: <strong class="text-white font-bold" x-text="selectedItem.deleted_by"></strong></p>
                            <p class="text-slate-400 text-[11px]">Waktu Penghapusan: <span class="font-mono text-slate-200 font-semibold" x-text="selectedItem.deleted_at"></span></p>
                        </div>
                    </div>

                    <!-- 1. DETAIL KHUSUS MUTASI (INTERNAL & EKSTERNAL) -->
                    <template x-if="activeModule === 'mutasi'">
                        <div class="space-y-4">
                            <!-- Mode 1: Eksternal -->
                            <template x-if="selectedItem && (selectedItem.is_eksternal || mutasiSubTab === 'eksternal')">
                                <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-3.5 shadow-inner">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-3 border-b border-slate-800/80">
                                        <div>
                                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nomor BAST Eksternal:</span>
                                            <span class="font-mono font-bold text-cyan-300 text-sm sm:text-base block mt-0.5" x-text="selectedItem.kode"></span>
                                            <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-cyan-950/70 border border-cyan-800/50 text-cyan-300 inline-block mt-1" x-text="selectedItem.jenis"></span>
                                        </div>
                                        <div class="sm:text-right">
                                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nilai Perolehan:</span>
                                            <span class="font-mono font-bold text-emerald-300 text-sm sm:text-base block mt-0.5" x-text="selectedItem.nilai_perolehan_formatted || '-'"></span>
                                            <span class="text-[11px] text-slate-400 block mt-0.5" x-text="(selectedItem.item_count || 1) + ' Unit Barang'"></span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-0.5">
                                        <div>
                                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Instansi / OPD Asal:</span>
                                            <span class="font-bold text-slate-200 text-xs block mt-0.5" x-text="selectedItem.asal"></span>
                                            <span class="text-slate-400 text-[11px] block" x-text="'PJ: ' + (selectedItem.pemohon || '-')"></span>
                                        </div>
                                        <div class="sm:text-right">
                                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Ruangan Tujuan di RSUD:</span>
                                            <span class="font-bold text-cyan-300 text-xs block mt-0.5" x-text="selectedItem.tujuan"></span>
                                            <span class="text-slate-400 text-[11px] block" x-text="'PJ: ' + (selectedItem.penerima_pj || '-')"></span>
                                        </div>
                                        <div class="sm:col-span-2 pt-2 border-t border-slate-800/80">
                                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Alasan / Catatan Pelimpahan:</span>
                                            <span class="text-xs text-slate-300 italic block mt-0.5" x-text="selectedItem.alasan_mutasi || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Mode 2: Internal -->
                            <template x-if="selectedItem && !selectedItem.is_eksternal && mutasiSubTab !== 'eksternal'">
                                <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-3.5 shadow-inner">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-3 border-b border-slate-800/80">
                                        <div>
                                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nomor BAMB:</span>
                                            <span class="font-mono font-bold text-amber-300 text-sm sm:text-base block mt-0.5" x-text="selectedItem.kode"></span>
                                        </div>
                                        <div class="sm:text-right">
                                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Jenis Mutasi:</span>
                                            <span class="font-bold text-white text-xs sm:text-sm block mt-0.5" x-text="selectedItem.jenis"></span>
                                        </div>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-0.5">
                                        <div>
                                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Ruangan Asal:</span>
                                            <span class="font-bold text-slate-200 text-xs block mt-0.5" x-text="selectedItem.asal"></span>
                                            <span class="text-slate-400 text-[11px] block" x-text="'PJ: ' + (selectedItem.pemohon || '-')"></span>
                                        </div>
                                        <div class="sm:text-right">
                                            <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Ruangan Tujuan:</span>
                                            <span class="font-bold text-indigo-300 text-xs block mt-0.5" x-text="selectedItem.tujuan"></span>
                                            <span class="text-slate-400 text-[11px] block" x-text="'PJ: ' + (selectedItem.penerima_pj || '-')"></span>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- Tabel Rincian Barang -->
                            <div class="space-y-2">
                                <span class="text-slate-300 text-xs font-bold uppercase tracking-wider block">Rincian Barang Terkait:</span>
                                <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-56 overflow-y-auto custom-scrollbar">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-900/95 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0 z-10">
                                            <tr>
                                                <th class="px-3.5 py-2.5 text-center w-10">No</th>
                                                <th class="px-3.5 py-2.5">Nama Barang / Aset</th>
                                                <th class="px-3.5 py-2.5 font-mono">NIBAR</th>
                                                <th class="px-3.5 py-2.5 text-center">Kondisi</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-800/60">
                                            <template x-for="(it, idx) in selectedItem.items" :key="idx">
                                                <tr class="hover:bg-slate-900/50">
                                                    <td class="px-3.5 py-2.5 text-center text-slate-500 font-bold" x-text="idx + 1"></td>
                                                    <td class="px-3.5 py-2.5 font-bold text-white" x-text="it.nama_barang"></td>
                                                    <td class="px-3.5 py-2.5 font-mono text-cyan-400 text-xs" x-text="it.nibar"></td>
                                                    <td class="px-3.5 py-2.5 text-center">
                                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border"
                                                              :class="{
                                                                  'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': it.kondisi === 'Baik',
                                                                  'bg-amber-500/15 text-amber-300 border-amber-500/30': it.kondisi === 'Kurang Baik',
                                                                  'bg-rose-500/15 text-rose-300 border-rose-500/30': it.kondisi === 'Rusak Berat'
                                                              }" x-text="it.kondisi || 'Baik'"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- 2. DETAIL KHUSUS MASTER ASTAP (PAKET PENGADAAN - GAMBAR 2) -->
                    <template x-if="activeModule === 'astap' && astapSubTab === 'packet'">
                        <div class="space-y-4">
                            <!-- Card Ringkasan Info Paket Pengadaan -->
                            <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-4 shadow-inner">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pb-3.5 border-b border-slate-800/80">
                                    <!-- Kolom Kiri: Nama Aset & Kode 108 -->
                                    <div class="space-y-2">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nama Aset Induk / Pengadaan:</span>
                                        <h4 class="font-extrabold text-white text-base leading-snug break-words" x-text="selectedItem.nama"></h4>
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-cyan-950/60 border border-cyan-800/40 text-cyan-300 font-mono text-[11px] font-bold">
                                            <span class="text-cyan-400/70 text-[9.5px]">KODE 108:</span>
                                            <span x-text="selectedItem.kode"></span>
                                        </div>
                                    </div>
                                    <!-- Kolom Kanan: Total Realisasi & Volume -->
                                    <div class="sm:text-right space-y-1.5 flex flex-col sm:items-end justify-center">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Total Nilai Realisasi:</span>
                                        <span class="font-mono font-black text-amber-400 text-lg sm:text-xl block tracking-tight" x-text="selectedItem.total_realisasi"></span>
                                        <div class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-md bg-slate-900 border border-slate-800 text-xs text-slate-300 font-bold">
                                            <span class="text-slate-400 font-normal">Volume:</span>
                                            <span class="text-white" x-text="selectedItem.volume + ' Unit'"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Baris Tambahan: Rekanan Penyedia & Dokumen SPK -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs pt-0.5">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Penyedia / Rekanan:</span>
                                        <span class="font-semibold text-slate-200 text-xs block mt-0.5" x-text="selectedItem.penyedia || '-'"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nomor Dokumen SPK:</span>
                                        <span class="font-mono font-semibold text-slate-300 text-xs block mt-0.5" x-text="selectedItem.spk_nomor || '-'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabel Daftar Register NIBAR Terkait -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-slate-300 text-xs font-bold uppercase tracking-wider flex items-center gap-1.5">
                                        <span>📋 Daftar Register NIBAR Terkait:</span>
                                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-cyan-300 font-mono text-[10px] font-bold" x-text="(selectedItem.items ? selectedItem.items.length : 0) + ' Unit'"></span>
                                    </span>
                                </div>
                                <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-56 sm:max-h-64 overflow-y-auto custom-scrollbar">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-900/95 backdrop-blur-sm text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0 z-10">
                                            <tr>
                                                <th class="px-3.5 py-2.5 text-center w-10">No</th>
                                                <th class="px-3.5 py-2.5 font-mono">NIBAR</th>
                                                <th class="px-3.5 py-2.5">Ruangan Pemegang</th>
                                                <th class="px-3.5 py-2.5 text-center">Kondisi</th>
                                                <th class="px-3.5 py-2.5 text-center">Status</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-800/60">
                                            <template x-for="(r, idx) in selectedItem.items" :key="idx">
                                                <tr class="hover:bg-slate-900/50 transition-colors">
                                                    <td class="px-3.5 py-2.5 text-center text-slate-500 font-bold" x-text="idx + 1"></td>
                                                    <td class="px-3.5 py-2.5 font-mono font-bold text-cyan-300 text-xs tracking-wide" x-text="r.nibar"></td>
                                                    <td class="px-3.5 py-2.5 text-slate-200 font-medium" x-text="r.ruang"></td>
                                                    <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold border inline-block"
                                                              :class="{
                                                                  'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': r.kondisi === 'Baik',
                                                                  'bg-amber-500/15 text-amber-300 border-amber-500/30': r.kondisi === 'Kurang Baik',
                                                                  'bg-rose-500/15 text-rose-300 border-rose-500/30': r.kondisi === 'Rusak Berat'
                                                              }" x-text="r.kondisi || 'Baik'"></span>
                                                    </td>
                                                    <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-semibold border"
                                                              :class="r.status === 'Tersedia' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-slate-800/80 text-slate-400 border-slate-700/60'"
                                                              x-text="r.status || '-'"></span>
                                                    </td>
                                                </tr>
                                            </template>
                                            <template x-if="!selectedItem.items || selectedItem.items.length === 0">
                                                <tr>
                                                    <td colspan="5" class="px-4 py-6 text-center text-slate-500 italic text-xs">
                                                        Tidak ada rincian register NIBAR pada paket ini.
                                                    </td>
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
                        <div class="space-y-4">
                            <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-3.5 shadow-inner">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-3 border-b border-slate-800/80">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nomor Induk Barang (NIBAR):</span>
                                        <span class="font-mono font-bold text-cyan-300 text-sm sm:text-base tracking-wide block mt-0.5" x-text="selectedItem.nibar"></span>
                                        <span class="text-xs text-slate-400 block mt-1" x-text="'No Register: ' + selectedItem.no_register"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Kondisi & Status:</span>
                                        <div class="mt-1 flex items-center sm:justify-end gap-2 flex-wrap">
                                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-bold border"
                                                :class="selectedItem.kondisi === 'Baik' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : (selectedItem.kondisi === 'Rusak Berat' ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'bg-amber-500/20 text-amber-300 border-amber-500/40')"
                                                x-text="selectedItem.kondisi"></span>
                                            <span class="px-2.5 py-0.5 rounded-lg text-xs font-semibold bg-slate-800 border border-slate-700 text-slate-300" x-text="selectedItem.status"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-0.5">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Aset Induk (Pengadaan):</span>
                                        <span class="font-bold text-slate-200 text-xs block mt-0.5" x-text="selectedItem.nama_barang"></span>
                                        <span class="text-[10px] text-cyan-400 font-mono block mt-0.5" x-text="'Kode 108: ' + selectedItem.kode_108 + ' (' + selectedItem.kategori + ')'"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Ruangan Pemegang:</span>
                                        <span class="font-semibold text-white text-xs block mt-0.5" x-text="selectedItem.ruang"></span>
                                        <span class="text-[10px] text-slate-400 block mt-0.5" x-text="'Tahun: ' + selectedItem.tahun + ' | SPK: ' + selectedItem.spk_nomor"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3.5 bg-blue-500/10 border border-blue-500/25 rounded-2xl text-blue-300 text-xs flex items-center space-x-2.5">
                                <span class="text-base shrink-0">💡</span>
                                <span>Memulihkan NIBAR ini akan mengembalikan data unit ke paket pengadaan aset induk (<strong class="text-white" x-text="selectedItem.nama_barang"></strong>) dan menambah volume aktif sebesar +1 unit.</span>
                            </div>
                        </div>
                    </template>

                    <!-- 3. DETAIL KHUSUS DISTRIBUSI -->
                    <template x-if="activeModule === 'distribusi'">
                        <div class="space-y-4">
                            <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-3 shadow-inner">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-3 border-b border-slate-800/80">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Kode Distribusi:</span>
                                        <span class="font-mono font-bold text-cyan-300 text-sm sm:text-base block mt-0.5" x-text="selectedItem.kode"></span>
                                        <span class="text-[11px] text-slate-400 block mt-0.5" x-text="'BAST: ' + selectedItem.bast_nomor"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Unit / Ruangan Tujuan:</span>
                                        <span class="font-bold text-white text-xs sm:text-sm block mt-0.5" x-text="selectedItem.tujuan"></span>
                                        <span class="text-[11px] text-slate-400 block mt-0.5" x-text="'Tgl Pengajuan: ' + (selectedItem.tanggal || '-')"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <span class="text-slate-300 text-xs font-bold uppercase tracking-wider block">Item Barang Distribusi:</span>
                                <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-56 overflow-y-auto custom-scrollbar">
                                    <table class="w-full text-left text-xs">
                                        <thead class="bg-slate-900/95 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0 z-10">
                                            <tr>
                                                <th class="px-3.5 py-2.5 text-center w-10">No</th>
                                                <th class="px-3.5 py-2.5">Nama Barang</th>
                                                <th class="px-3.5 py-2.5">Volume</th>
                                                <th class="px-3.5 py-2.5">NIBAR Terkait</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-800/60">
                                            <template x-for="(it, idx) in selectedItem.items" :key="idx">
                                                <tr class="hover:bg-slate-900/50">
                                                    <td class="px-3.5 py-2.5 text-center text-slate-500 font-bold" x-text="idx + 1"></td>
                                                    <td class="px-3.5 py-2.5 font-bold text-white" x-text="it.nama_barang"></td>
                                                    <td class="px-3.5 py-2.5 text-teal-300 font-bold" x-text="it.qty"></td>
                                                    <td class="px-3.5 py-2.5 font-mono text-cyan-400 text-xs" x-text="it.nibar_list"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- 4. DETAIL KHUSUS UNIT / RUANGAN -->
                    <template x-if="activeModule === 'unit'">
                        <div class="space-y-4">
                            <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-3.5 shadow-inner">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-3 border-b border-slate-800/80">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Kode Unit:</span>
                                        <span class="font-mono font-bold text-indigo-300 text-sm sm:text-base block mt-0.5" x-text="selectedItem.kode"></span>
                                        <span class="text-white font-black text-base sm:text-lg block mt-1" x-text="selectedItem.nama"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Tipe Ruangan:</span>
                                        <span class="font-bold text-white text-xs sm:text-sm block mt-0.5" x-text="selectedItem.tipe"></span>
                                        <span class="text-xs text-slate-400 block mt-1" x-text="'Total Aset: ' + selectedItem.total_aset + ' Barang'"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-0.5">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Kepala Ruangan:</span>
                                        <span class="font-semibold text-slate-200 text-xs block mt-0.5" x-text="selectedItem.kepala"></span>
                                        <span class="text-[10px] text-slate-500 block" x-text="'NIP: ' + selectedItem.nip"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Akun Sub Admin:</span>
                                        <span class="font-mono text-cyan-400 text-xs block mt-0.5" x-text="selectedItem.email"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- PERINGATAN JIKA UNIT MEMILIKI ASET -->
                            <template x-if="selectedItem.total_aset > 0">
                                <div class="p-3.5 bg-amber-500/15 border border-amber-500/30 rounded-2xl flex items-center space-x-2.5 text-amber-300 text-xs font-semibold">
                                    <span class="text-base shrink-0">⚠️</span>
                                    <span>Perhatian: Unit ini masih tercatat menampung <strong class="text-white" x-text="selectedItem.total_aset"></strong> aset inventaris aktif. Pemulihan unit ini akan menyambungkan kembali data lokasi aset terkait.</span>
                                </div>
                            </template>

                            <!-- PERINGATAN JIKA UNIT MEMILIKI ARSIP BAST -->
                            <template x-if="selectedItem.total_bast > 0">
                                <div class="p-3.5 bg-blue-500/15 border border-blue-500/30 rounded-2xl flex items-center space-x-2.5 text-blue-300 text-xs font-semibold">
                                    <span class="text-base shrink-0">📜</span>
                                    <span>Proteksi Audit: Unit ini memiliki <strong class="text-white" x-text="selectedItem.total_bast"></strong> arsip dokumen BAST Distribusi resmi yang dilindungi untuk audit BPK & Inspektorat.</span>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- 5. DETAIL KHUSUS HIBAH ASET -->
                    <template x-if="activeModule === 'hibah'">
                        <div class="space-y-4">
                            <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-3.5 shadow-inner">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-3 border-b border-slate-800/80">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nomor BAST:</span>
                                        <span class="font-mono font-bold text-cyan-300 text-sm sm:text-base tracking-wide block mt-0.5" x-text="selectedItem.nomor_bast"></span>
                                        <span class="text-xs text-slate-400 block mt-1" x-text="'Tanggal BAST: ' + selectedItem.tanggal_bast"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Jenis Hibah:</span>
                                        <div class="mt-1">
                                            <span class="px-2.5 py-0.5 rounded text-xs font-bold inline-block"
                                                :class="selectedItem.tipe_hibah === 'masuk' ? 'bg-amber-400/20 text-amber-300 border border-amber-400/40' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40'"
                                                x-text="selectedItem.tipe_hibah === 'masuk' ? '🎁 Hibah Masuk' : '📤 Hibah Keluar'"></span>
                                        </div>
                                        <span class="text-xs text-slate-400 block mt-1" x-text="selectedItem.triwulan + ' ' + selectedItem.tahun"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-0.5">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block" x-text="selectedItem.tipe_hibah === 'masuk' ? 'Pemberi Hibah:' : 'Penerima Hibah:'"></span>
                                        <span class="font-semibold text-slate-200 text-xs block mt-0.5" x-text="selectedItem.pihak_hibah"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nilai Aset:</span>
                                        <span class="font-mono font-bold text-amber-300 text-sm block mt-0.5" x-text="selectedItem.nilai_aset_rp"></span>
                                        <span class="text-xs text-slate-400 block" x-text="'Volume: ' + selectedItem.volume"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Barang Yang Dihibahkan:</span>
                                <p class="font-bold text-white text-xs" x-text="selectedItem.nama_barang"></p>
                                <p class="font-mono text-[10px] text-cyan-400" x-text="'Kode 108: ' + selectedItem.kode_barang"></p>
                            </div>
                            <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Alasan Penghapusan:</span>
                                <p class="text-xs text-rose-300 font-semibold" x-text="selectedItem.alasan_hapus"></p>
                                <p class="text-[11px] text-slate-400 mt-1" x-show="selectedItem.keterangan && selectedItem.keterangan !== '-'" x-text="'Catatan BAST: ' + selectedItem.keterangan"></p>
                            </div>
                        </div>
                    </template>

                    <!-- 6. DETAIL KHUSUS KEMITRAAN ASET -->
                    <template x-if="activeModule === 'kemitraan'">
                        <div class="space-y-4">
                            <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-3.5 shadow-inner">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-3 border-b border-slate-800/80">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1.5 flex-wrap">
                                            <template x-if="selectedItem.is_ditambahkan">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">
                                                    📦 Aset Ditambahkan Mitra
                                                </span>
                                            </template>
                                            <template x-if="!selectedItem.is_ditambahkan">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">
                                                    🏛️ Aset Dimanfaatkan Mitra
                                                </span>
                                            </template>
                                        </div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nomor Dokumen PKS / Kontrak:</span>
                                        <span class="font-mono font-bold text-cyan-300 text-sm sm:text-base tracking-wide block mt-0.5" x-text="selectedItem.nomor_pks"></span>
                                        <span class="text-xs text-slate-400 block mt-1" x-text="'Tanggal PKS: ' + selectedItem.tanggal_pks"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Skema Kerja Sama:</span>
                                        <div class="mt-1">
                                            <span class="px-2.5 py-0.5 rounded text-xs font-bold inline-block"
                                                :class="selectedItem.is_ditambahkan ? 'bg-emerald-950/80 border border-emerald-800/60 text-emerald-300' : 'bg-cyan-950/80 border border-cyan-800/60 text-cyan-300'"
                                                x-text="'🤝 ' + selectedItem.skema_kemitraan"></span>
                                        </div>
                                        <span class="text-xs text-slate-400 block mt-1" x-text="'Status: ' + selectedItem.status_konsesi"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-0.5">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Rekanan Mitra Kerja Sama:</span>
                                        <span class="font-bold text-white text-sm block mt-0.5" x-text="selectedItem.mitra_nama"></span>
                                        <span class="text-[11px] text-slate-400 block" x-text="'Penempatan: ' + selectedItem.ruangan"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">
                                            <span x-text="selectedItem.is_ditambahkan ? 'Taksiran Nilai Investasi Mitra:' : 'Nilai Pemanfaatan / Wajar (Akun 1.5.2):'"></span>
                                        </span>
                                        <span class="font-mono font-bold text-emerald-400 text-sm block mt-0.5" x-text="selectedItem.nilai_aset_rp"></span>
                                        <span class="text-xs text-teal-300 font-mono block" x-text="'Volume: ' + selectedItem.volume"></span>
                                    </div>
                                </div>
                                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400 flex-wrap gap-2">
                                    <span>Periode Konsesi: <strong class="text-white" x-text="selectedItem.tanggal_mulai"></strong> s/d <strong class="text-white" x-text="selectedItem.tanggal_selesai"></strong></span>
                                    <span x-text="selectedItem.triwulan + ' ' + selectedItem.tahun"></span>
                                </div>
                            </div>

                            <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1.5">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider"
                                    x-text="selectedItem.is_ditambahkan ? 'Barang yang Didatangkan Mitra:' : 'Objek BMD yang Dikerjasamakan:'"></span>
                                <p class="font-bold text-white text-xs" x-text="selectedItem.nama_barang"></p>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="font-mono text-[10px] text-cyan-400" x-text="'Kode 108: ' + selectedItem.kode_barang"></span>
                                    <template x-if="selectedItem.merk || selectedItem.type">
                                        <span class="text-[10px] text-emerald-300 font-medium" x-text="'• Merk/Tipe: ' + [selectedItem.merk, selectedItem.type].filter(Boolean).join(' ')"></span>
                                    </template>
                                </div>
                                <template x-if="selectedItem.objek_asal_nama">
                                    <div class="mt-1.5 pt-1.5 border-t border-slate-800/80 text-[11px] text-slate-400 flex items-center gap-1.5">
                                        <span>🏛️ Menempati Objek BMD:</span>
                                        <strong class="text-cyan-300" x-text="selectedItem.objek_asal_nama"></strong>
                                        <template x-if="selectedItem.objek_asal_nibar">
                                            <span class="text-[10px] font-mono text-slate-500" x-text="'(NIBAR: ' + selectedItem.objek_asal_nibar + ')'"></span>
                                        </template>
                                    </div>
                                </template>
                            </div>

                            <!-- TABEL REGISTER UNIT BARANG KEMITRAAN -->
                            <template x-if="selectedItem.registers && selectedItem.registers.length > 0">
                                <div class="space-y-2">
                                    <span class="text-slate-300 text-xs font-bold uppercase tracking-wider block">Rincian Register NIBAR Terkait:</span>
                                    <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-56 overflow-y-auto custom-scrollbar">
                                        <table class="w-full text-left text-xs">
                                            <thead class="bg-slate-900/95 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-3.5 py-2.5 text-center w-10">No</th>
                                                    <th class="px-3.5 py-2.5">NIBAR</th>
                                                    <th class="px-3.5 py-2.5">Ruangan</th>
                                                    <th class="px-3.5 py-2.5 text-center">Kondisi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-800/60">
                                                <template x-for="(reg, rIdx) in selectedItem.registers" :key="rIdx">
                                                    <tr class="hover:bg-slate-900/50">
                                                        <td class="px-3.5 py-2.5 text-center text-slate-500 font-bold" x-text="reg.no"></td>
                                                        <td class="px-3.5 py-2.5 font-mono font-bold text-cyan-300" x-text="reg.nibar"></td>
                                                        <td class="px-3.5 py-2.5 text-slate-200" x-text="reg.ruangan"></td>
                                                        <td class="px-3.5 py-2.5 text-center">
                                                            <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30" x-text="reg.kondisi"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </template>

                            <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Alasan Penghapusan:</span>
                                <p class="text-xs text-rose-300 font-semibold" x-text="selectedItem.alasan_hapus"></p>
                                <p class="text-[11px] text-slate-400 mt-1" x-show="selectedItem.keterangan && selectedItem.keterangan !== '-'" x-text="'Catatan PKS: ' + selectedItem.keterangan"></p>
                            </div>
                        </div>
                    </template>

                    <!-- 7. DETAIL KHUSUS BELANJA BARANG -->
                    <template x-if="activeModule === 'belanja_barang'">
                        <div class="space-y-4">
                            <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-3.5 shadow-inner">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-3 border-b border-slate-800/80">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nomor Faktur Pembelian:</span>
                                        <span class="font-mono font-bold text-teal-300 text-sm sm:text-base tracking-wide block mt-0.5" x-text="selectedItem.nomor_faktur"></span>
                                        <span class="text-xs text-slate-400 block mt-1" x-text="'Tanggal Faktur: ' + selectedItem.tanggal_faktur"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Sumber Belanja:</span>
                                        <div class="mt-1">
                                            <span class="px-2.5 py-0.5 rounded text-xs font-bold bg-emerald-950/80 border border-emerald-800/60 text-emerald-300 inline-block">
                                                🛒 Akun 5.1.02
                                            </span>
                                        </div>
                                        <span class="text-xs text-slate-400 block mt-1" x-text="'Tahun: ' + selectedItem.tahun + ' (' + selectedItem.triwulan + ')'"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-0.5">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Toko / Rekanan Penyedia:</span>
                                        <span class="font-bold text-white text-sm block mt-0.5" x-text="selectedItem.toko_penyedia"></span>
                                        <span class="text-[11px] text-indigo-300 block" x-text="'Lokasi Penempatan: ' + selectedItem.ruangan"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Total Pembelian:</span>
                                        <span class="font-mono font-bold text-emerald-400 text-sm block mt-0.5" x-text="selectedItem.total_pembelian_rp"></span>
                                        <span class="text-xs text-teal-300 font-mono block" x-text="'Volume: ' + selectedItem.volume + ' Unit'"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Barang Belanja / Perbekalan:</span>
                                <p class="font-bold text-white text-xs" x-text="selectedItem.nama_barang"></p>
                                <p class="font-mono text-[10px] text-cyan-400" x-text="'Kode 108: ' + selectedItem.kode_barang"></p>
                            </div>

                            <!-- TABEL REGISTER UNIT BARANG BELANJA -->
                            <template x-if="selectedItem.registers && selectedItem.registers.length > 0">
                                <div class="space-y-2">
                                    <span class="text-slate-300 text-xs font-bold uppercase tracking-wider block">Rincian Register NIBAR Terkait:</span>
                                    <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-56 overflow-y-auto custom-scrollbar">
                                        <table class="w-full text-left text-xs">
                                            <thead class="bg-slate-900/95 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800 sticky top-0 z-10">
                                                <tr>
                                                    <th class="px-3.5 py-2.5 text-center w-10">No</th>
                                                    <th class="px-3.5 py-2.5">NIBAR</th>
                                                    <th class="px-3.5 py-2.5">Ruangan</th>
                                                    <th class="px-3.5 py-2.5 text-center">Kondisi</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-800/60">
                                                <template x-for="(reg, rIdx) in selectedItem.registers" :key="rIdx">
                                                    <tr class="hover:bg-slate-900/50">
                                                        <td class="px-3.5 py-2.5 text-center text-slate-500 font-bold" x-text="reg.no"></td>
                                                        <td class="px-3.5 py-2.5 font-mono font-bold text-teal-300" x-text="reg.nibar"></td>
                                                        <td class="px-3.5 py-2.5 text-slate-200" x-text="reg.ruangan"></td>
                                                        <td class="px-3.5 py-2.5 text-center">
                                                            <span class="px-2.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30" x-text="reg.kondisi"></span>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </template>

                            <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Alasan Penghapusan:</span>
                                <p class="text-xs text-rose-300 font-semibold" x-text="selectedItem.alasan_hapus"></p>
                                <p class="text-[11px] text-slate-400 mt-1" x-show="selectedItem.keterangan && selectedItem.keterangan !== '-'" x-text="'Catatan Belanja: ' + selectedItem.keterangan"></p>
                            </div>
                        </div>
                    </template>

                    <!-- 8. DETAIL REKLASIFIKASI ASET -->
                    <template x-if="activeModule === 'reklas'">
                        <div class="space-y-4">
                            <div class="bg-slate-950/90 p-4 sm:p-5 rounded-2xl border border-slate-800 space-y-3.5 shadow-inner">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5 pb-3 border-b border-slate-800/80">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nomor Berita Acara:</span>
                                        <span class="font-mono font-bold text-cyan-300 text-sm sm:text-base block mt-0.5" x-text="selectedItem.nomor_ba"></span>
                                        <span class="text-[11px] text-slate-400 block mt-0.5" x-text="'Tanggal BA: ' + selectedItem.tanggal_reklas"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nilai Reklasifikasi:</span>
                                        <span class="font-mono font-bold text-emerald-300 text-sm sm:text-base block mt-0.5" x-text="selectedItem.nilai_reklas_rp"></span>
                                        <span class="text-[11px] text-slate-400 block mt-0.5" x-text="'T.A. ' + selectedItem.tahun + ' (Triwulan ' + selectedItem.triwulan + ')'"></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-0.5">
                                    <div>
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Aset Terkait:</span>
                                        <span class="font-bold text-white text-xs block mt-0.5" x-text="selectedItem.nama_barang"></span>
                                        <span class="text-[10px] font-mono text-cyan-400 block mt-0.5" x-text="'Kode 108: ' + selectedItem.kode_barang"></span>
                                    </div>
                                    <div class="sm:text-right">
                                        <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Perubahan Rekening / KIB:</span>
                                        <div class="mt-0.5">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-200 border border-slate-700 inline-block" x-text="selectedItem.asal_kib + ' ➔ ' + selectedItem.tujuan_kib"></span>
                                        </div>
                                        <span class="text-[10px] text-emerald-300 block font-semibold mt-1" x-text="selectedItem.jenis_reklas"></span>
                                    </div>
                                </div>
                            </div>
                            <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 block uppercase font-bold tracking-wider">Alasan Penghapusan:</span>
                                <p class="text-xs text-rose-300 font-semibold" x-text="selectedItem.alasan_hapus"></p>
                                <p class="text-[11px] text-slate-400 mt-1" x-show="selectedItem.keterangan && selectedItem.keterangan !== '-'" x-text="'Catatan Reklas: ' + selectedItem.keterangan"></p>
                            </div>
                        </div>
                    </template>

                    <!-- 9. DETAIL PENGGUNA -->
                    <template x-if="activeModule === 'users'">
                        <div class="space-y-4">
                            <div class="p-4 sm:p-5 bg-slate-950/90 border border-slate-800 rounded-2xl grid grid-cols-1 sm:grid-cols-2 gap-3.5 text-xs shadow-inner">
                                <div>
                                    <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Nama Lengkap & NIP:</span>
                                    <span class="text-white font-black text-base block mt-1" x-text="selectedItem.name"></span>
                                    <span class="text-[11px] font-mono text-slate-400" x-text="'NIP: ' + selectedItem.nip"></span>
                                </div>
                                <div class="sm:text-right">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Role Otorisasi:</span>
                                    <span class="font-bold text-amber-300 text-sm block mt-1" x-text="selectedItem.role_label || selectedItem.role"></span>
                                    <span class="text-xs text-slate-400 block mt-1" x-text="'Status: ' + selectedItem.status"></span>
                                </div>
                                <div class="pt-2 border-t border-slate-800/80">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Unit Penugasan:</span>
                                    <span class="font-semibold text-slate-200 text-xs block mt-0.5" x-text="selectedItem.unit || '-'"></span>
                                    <span class="text-[11px] text-slate-400 block" x-text="selectedItem.penugasan || '-'"></span>
                                </div>
                                <div class="sm:text-right pt-2 border-t border-slate-800/80">
                                    <span class="text-slate-400 text-[10px] uppercase font-bold tracking-wider block">Email Kredensial:</span>
                                    <span class="font-mono text-cyan-400 text-xs block mt-0.5" x-text="selectedItem.email"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                </div>
            </template>
        </div>

        <!-- Fixed Pinned Footer (Selalu Terlihat, Tidak Terpotong) -->
        <div class="shrink-0 flex items-center justify-between px-5 sm:px-6 py-4 border-t border-slate-800/80 bg-slate-950/90 backdrop-blur-md">
            <div class="flex items-center space-x-2">
                <template x-if="selectedItem">
                    <button type="button" @click="restoreSingle(currentTargetModule, selectedItem); showDetailModal = false;"
                        class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs shadow-lg shadow-emerald-600/25 border border-emerald-500/40 transition-all flex items-center space-x-2 cursor-pointer active:scale-95">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <span>Pulihkan Data Ini</span>
                    </button>
                </template>
            </div>
            <button type="button" @click="showDetailModal = false"
                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-xs border border-slate-700 transition-all cursor-pointer active:scale-95">
                Tutup
            </button>
        </div>
    </div>
</div>
