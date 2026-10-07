{{-- ========================================================================= --}}
{{-- TAB 9: REKLASIFIKASI ASET (PERUBAHAN KIB / KOREKSI 108) TABLE             --}}
{{-- ========================================================================= --}}
<template x-if="activeModule === 'reklas'">
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        <div class="rounded-2xl border border-slate-800/80 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                        <tr>
                            <th class="px-4 py-3.5 w-10 text-center">
                                <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                    class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                            </th>
                            <th class="px-4 py-3.5">Nomor & Tanggal BA</th>
                            <th class="px-4 py-3.5">Nama Barang & Kode 108</th>
                            <th class="px-4 py-3.5">Jenis & Alur Reklasifikasi</th>
                            <th class="px-4 py-3.5">Nilai Reklasifikasi</th>
                            <th class="px-4 py-3.5">Dihapus Oleh</th>
                            <th class="px-4 py-3.5">Waktu Penghapusan</th>
                            <th class="px-4 py-3.5 text-center shrink-0 min-w-[220px] w-[220px]" style="position: sticky; right: 0; z-index: 10; background-color: #020617;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60 bg-slate-900/40">
                        <template x-for="(item, idx) in filteredItems" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center">
                                    <input type="checkbox" :value="item.id" x-model="selectedIds"
                                        class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-cyan-300 block" x-text="item.nomor_ba"></span>
                                    <span class="text-[10px] text-slate-400" x-text="'Tanggal: ' + item.tanggal_reklas"></span>
                                    <span class="text-[9.5px] text-slate-500 font-mono" x-text="'T.A. ' + item.tahun + ' (TW ' + item.triwulan + ')'"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-bold text-white block" x-text="item.nama_barang"></span>
                                    <span class="text-[10px] font-mono text-cyan-400/80" x-text="'Kode 108: ' + item.kode_barang"></span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-950/70 border border-emerald-800/60 text-emerald-300 inline-block mb-1" x-text="item.jenis_reklas"></span>
                                    <div class="flex items-center gap-1.5 text-[10.5px]">
                                        <span class="px-1.5 py-0.5 rounded bg-slate-800 text-slate-300 font-semibold" x-text="item.asal_kib"></span>
                                        <span class="text-slate-500">➔</span>
                                        <span class="px-1.5 py-0.5 rounded bg-cyan-950 text-cyan-300 font-semibold border border-cyan-800/50" x-text="item.tujuan_kib"></span>
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="font-mono font-bold text-emerald-400 block" x-text="item.nilai_reklas_rp"></span>
                                    <span class="text-[10px] text-slate-400 italic truncate max-w-[150px] block" x-text="item.keterangan"></span>
                                </td>
                                <td class="px-4 py-4">
                                    <span class="font-bold text-red-300 block" x-text="item.deleted_by"></span>
                                    <span class="text-[10px] text-slate-400 block truncate max-w-[140px]" :title="item.alasan_hapus" x-text="item.alasan_hapus"></span>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="font-mono text-slate-200 block text-[11px]" x-text="item.deleted_at"></span>
                                    <span class="text-[10px] text-slate-500" x-text="item.deleted_at_relative"></span>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[220px] w-[220px]"
                                    style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button type="button" @click="openDetail(item)" title="Lihat Detail Transaksi Reklasifikasi"
                                            class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <span>👁️ Detail</span>
                                        </button>
                                        <button type="button" @click="restoreSingle('reklas', item)" title="Pulihkan Transaksi Reklasifikasi ke Daftar Aktif"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                            <span>Pulihkan</span>
                                        </button>
                                        <button type="button" @click="forceDeleteSingle('reklas', item)" title="Hapus Permanen dari Database"
                                            class="px-2.5 py-1.5 rounded-xl bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <template x-if="filteredItems.length === 0">
                            <tr>
                                <td colspan="8" class="py-14 text-center">
                                    <template x-if="currentList.length === 0">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">⚖️</div>
                                            <p class="text-sm font-bold text-slate-200">Tong Sampah Reklasifikasi Aset Kosong</p>
                                            <p class="text-xs text-slate-500 max-w-sm">Tidak ada transaksi reklasifikasi atau pergeseran KIB yang berstatus terhapus.</p>
                                        </div>
                                    </template>
                                    <template x-if="currentList.length > 0">
                                        <div class="flex flex-col items-center justify-center space-y-3">
                                            <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-amber-400 shadow-inner">🔍</div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-200">Tidak Ada Data Reklasifikasi yang Cocok</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan data terhapus yang sesuai dengan kata kunci pencarian atau rentang waktu yang dipilih.</p>
                                            </div>
                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>
