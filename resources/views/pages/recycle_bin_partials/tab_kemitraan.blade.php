{{-- ========================================================================= --}}
{{-- TAB 6: KEMITRAAN ASET (AKUN 1.5.2) - DIMANFAATKAN vs DITAMBAHKAN MITRA   --}}
{{-- ========================================================================= --}}
<template x-if="activeModule === 'kemitraan'">
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        
        <!-- SUB-TAB SWITCHER KEMITRAAN (Dimanfaatkan Mitra vs Ditambahkan Mitra) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 mb-5 border-b border-slate-800/80 gap-3">
            <div class="flex items-center space-x-1.5 p-1 bg-slate-950/80 rounded-2xl border border-slate-800">
                <!-- Sub-Tab 1: Aset BMD Dimanfaatkan Mitra -->
                <button type="button" @click="changeKemitraanSubTab('dimanfaatkan')"
                    :style="kemitraanSubTab === 'dimanfaatkan' ? 'border-color: #06b6d4; box-shadow: 0 0 14px rgba(6, 182, 212, 0.35);' : ''"
                    :class="kemitraanSubTab === 'dimanfaatkan' 
                        ? 'bg-cyan-500/15 border border-cyan-500 text-cyan-300 shadow-lg' 
                        : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer">
                    <span>🏛️ Aset Dimanfaatkan Mitra</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black transition-colors"
                        :class="kemitraanDimanfaatkan.length > 0 ? 'bg-cyan-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                        x-text="kemitraanDimanfaatkan.length">0</span>
                </button>

                <!-- Sub-Tab 2: Aset Ditambahkan Mitra -->
                <button type="button" @click="changeKemitraanSubTab('ditambahkan')"
                    :style="kemitraanSubTab === 'ditambahkan' ? 'border-color: #10b981; box-shadow: 0 0 14px rgba(16, 185, 129, 0.35);' : ''"
                    :class="kemitraanSubTab === 'ditambahkan' 
                        ? 'bg-emerald-500/15 border border-emerald-500 text-emerald-300 shadow-lg' 
                        : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer">
                    <span>📦 Aset Ditambahkan Mitra</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black transition-colors"
                        :class="kemitraanDitambahkan.length > 0 ? 'bg-emerald-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                        x-text="kemitraanDitambahkan.length">0</span>
                </button>
            </div>

            <div class="text-xs text-slate-400 flex items-center space-x-2">
                <span class="inline-block w-2 h-2 rounded-full" :class="kemitraanSubTab === 'dimanfaatkan' ? 'bg-cyan-400 animate-pulse' : 'bg-emerald-400 animate-pulse'"></span>
                <span class="text-[11px] font-medium" x-text="kemitraanSubTab === 'dimanfaatkan' ? 'Ruang Lingkup: Aset BMD RSUD yang Dimanfaatkan / Disewakan ke Rekanan' : 'Ruang Lingkup: Alat Medis, Mesin, atau Fasilitas Baru yang Disediakan Mitra (KSO)'"></span>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- SUB-KONTEN 1: TABEL ASET BMD RSUD DIMANFAATKAN MITRA (CYAN THEME)          -->
        <!-- ========================================================================= -->
        <template x-if="kemitraanSubTab === 'dimanfaatkan'">
            <div class="rounded-2xl border border-slate-800/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3.5 w-10 text-center">
                                    <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                        class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-3.5">Dokumen PKS &amp; Rekanan</th>
                                <th class="px-4 py-3.5">Objek BMD yang Dimanfaatkan</th>
                                <th class="px-4 py-3.5">Identitas 108 Kemitraan</th>
                                <th class="px-4 py-3.5">Skema &amp; Periode Konsesi</th>
                                <th class="px-4 py-3.5">Nilai Pemanfaatan</th>
                                <th class="px-4 py-3.5">Dihapus Oleh &amp; Alasan</th>
                                <th class="px-4 py-3.5">Waktu Penghapusan</th>
                                <th class="px-4 py-3.5 text-center shrink-0 min-w-[220px] w-[220px]" style="position: sticky; right: 0; z-index: 10; background-color: #020617;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-900/40">
                            <template x-for="(item, idx) in filteredItems" :key="item.id">
                                <tr class="hover:bg-cyan-950/20 transition-colors">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" :value="item.id" x-model="selectedIds"
                                            class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-white block truncate max-w-[180px]" x-text="item.mitra_nama"></span>
                                        <span class="font-mono text-cyan-300 text-[11px] block truncate max-w-[180px]" x-text="'No: ' + item.nomor_pks"></span>
                                        <span class="text-[10px] text-slate-500 mt-0.5 block" x-text="'Tgl PKS: ' + item.tanggal_pks"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-slate-200 block" x-text="item.nama_barang"></span>
                                        <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400">
                                            <span class="font-mono text-slate-300" x-text="'Vol: ' + item.volume"></span>
                                            <template x-if="item.luas">
                                                <span class="text-cyan-400 font-mono" x-text="'• ' + item.luas + ' m²'"></span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-mono text-[11px] font-bold text-cyan-400 bg-cyan-950/60 border border-cyan-500/30 px-2 py-0.5 rounded-lg inline-block" x-text="item.kode_barang"></span>
                                        <div class="text-[9.5px] text-slate-400 mt-1 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                            <span>Akun 1.5.2 Kemitraan</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-1.5 mb-1">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 inline-block uppercase" x-text="item.skema_kemitraan"></span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 border border-slate-700 text-slate-300 inline-block" x-text="item.status_konsesi"></span>
                                        </div>
                                        <span class="text-[10px] font-mono text-slate-400 block" x-text="item.tanggal_mulai + ' s/d ' + item.tanggal_selesai"></span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="font-mono font-bold text-cyan-300 block text-xs" x-text="item.nilai_aset_rp"></span>
                                        <span class="text-[10px] text-slate-500 font-mono" x-text="item.triwulan + ' ' + item.tahun"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-red-300 block" x-text="item.deleted_by"></span>
                                        <span class="text-[10px] text-slate-400 block truncate max-w-[150px]" :title="item.alasan_hapus" x-text="item.alasan_hapus"></span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="font-mono text-slate-200 block text-[11px]" x-text="item.deleted_at"></span>
                                        <span class="text-[10px] text-slate-500" x-text="item.deleted_at_relative"></span>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[220px] w-[220px]"
                                        style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openDetail(item)" title="Lihat Detail Transaksi Pemanfaatan"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <span>👁️ Detail</span>
                                            </button>
                                            <button type="button" @click="restoreSingle('kemitraan', item)" title="Pulihkan Objek Pemanfaatan ke Daftar Aktif"
                                                class="px-2.5 py-1.5 rounded-xl bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Pulihkan</span>
                                            </button>
                                            <button type="button" @click="forceDeleteSingle('kemitraan', item)" title="Hapus Permanen dari Database"
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
                                    <td colspan="9" class="py-14 text-center">
                                        <template x-if="kemitraanDimanfaatkan.length === 0">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-cyan-400 shadow-inner">🏛️</div>
                                                <p class="text-sm font-bold text-slate-200">Tidak Ada Aset Dimanfaatkan yang Terhapus</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tong sampah untuk objek pemanfaatan / sewa BMD RSUD saat ini kosong.</p>
                                            </div>
                                        </template>
                                        <template x-if="kemitraanDimanfaatkan.length > 0">
                                            <div class="flex flex-col items-center justify-center space-y-3">
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-amber-400 shadow-inner">🔍</div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-200">Tidak Ada Data yang Cocok</p>
                                                    <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan aset dimanfaatkan terhapus yang sesuai dengan kata kunci pencarian atau filter waktu.</p>
                                                </div>
                                                <button type="button" @click="searchQuery = ''; setTimeFilter('all');"
                                                    class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition-all shadow-sm cursor-pointer">
                                                    Reset Filter &amp; Pencarian
                                                </button>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <!-- ========================================================================= -->
        <!-- SUB-KONTEN 2: TABEL ASET YANG DITAMBAHKAN MITRA (EMERALD THEME)           -->
        <!-- ========================================================================= -->
        <template x-if="kemitraanSubTab === 'ditambahkan'">
            <div class="rounded-2xl border border-slate-800/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3.5 w-10 text-center">
                                    <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                        class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-3.5">Dokumen Kontrak &amp; Mitra</th>
                                <th class="px-4 py-3.5">Nama Barang &amp; Spesifikasi</th>
                                <th class="px-4 py-3.5">Identitas 108 Aset Kemitraan</th>
                                <th class="px-4 py-3.5">Skema &amp; Masa Konsesi</th>
                                <th class="px-4 py-3.5">Taksiran Nilai Investasi</th>
                                <th class="px-4 py-3.5">Dihapus Oleh &amp; Alasan</th>
                                <th class="px-4 py-3.5">Waktu Penghapusan</th>
                                <th class="px-4 py-3.5 text-center shrink-0 min-w-[220px] w-[220px]" style="position: sticky; right: 0; z-index: 10; background-color: #020617;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-900/40">
                            <template x-for="(item, idx) in filteredItems" :key="item.id">
                                <tr class="hover:bg-emerald-950/20 transition-colors">
                                    <td class="px-4 py-4 text-center">
                                        <input type="checkbox" :value="item.id" x-model="selectedIds"
                                            class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-white block truncate max-w-[180px]" x-text="item.mitra_nama"></span>
                                        <span class="font-mono text-emerald-300 text-[11px] block truncate max-w-[180px]" x-text="'No: ' + item.nomor_pks"></span>
                                        <span class="text-[10px] text-slate-500 mt-0.5 block" x-text="'Tgl Kontrak: ' + item.tanggal_pks"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-white block" x-text="item.nama_barang"></span>
                                        <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400 flex-wrap">
                                            <span class="font-mono text-white font-bold bg-slate-800 px-1.5 py-0.5 rounded" x-text="'Vol: ' + item.volume"></span>
                                            <template x-if="item.merk || item.type">
                                                <span class="text-emerald-300 bg-emerald-950/60 border border-emerald-500/30 px-1.5 py-0.5 rounded truncate max-w-[180px]"
                                                    x-text="[item.merk, item.type].filter(Boolean).join(' ')"></span>
                                            </template>
                                        </div>
                                        <template x-if="item.objek_asal_nama">
                                            <div class="mt-1 text-[9.5px] text-slate-400 flex items-center gap-1">
                                                <span>🏛️ Menempati:</span>
                                                <span class="text-cyan-300 font-medium truncate max-w-[160px]" x-text="item.objek_asal_nama"></span>
                                            </div>
                                        </template>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-mono text-[11px] font-bold text-emerald-400 bg-emerald-950/60 border border-emerald-500/30 px-2 py-0.5 rounded-lg inline-block" x-text="item.kode_barang"></span>
                                        <div class="text-[9.5px] text-slate-400 mt-1 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                            <span>Akun 1.5.2 Kemitraan</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-1.5 mb-1">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 inline-block uppercase" x-text="item.skema_kemitraan"></span>
                                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 border border-slate-700 text-slate-300 inline-block" x-text="item.status_konsesi"></span>
                                        </div>
                                        <span class="text-[10px] font-mono text-slate-400 block" x-text="item.tanggal_mulai + ' s/d ' + item.tanggal_selesai"></span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="font-mono font-bold text-emerald-400 block text-xs" x-text="item.nilai_aset_rp"></span>
                                        <span class="text-[10px] text-slate-500 font-mono" x-text="item.triwulan + ' ' + item.tahun"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-red-300 block" x-text="item.deleted_by"></span>
                                        <span class="text-[10px] text-slate-400 block truncate max-w-[150px]" :title="item.alasan_hapus" x-text="item.alasan_hapus"></span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="font-mono text-slate-200 block text-[11px]" x-text="item.deleted_at"></span>
                                        <span class="text-[10px] text-slate-500" x-text="item.deleted_at_relative"></span>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[220px] w-[220px]"
                                        style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openDetail(item)" title="Lihat Detail Transaksi Aset Mitra"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <span>👁️ Detail</span>
                                            </button>
                                            <button type="button" @click="restoreSingle('kemitraan', item)" title="Pulihkan Aset Mitra ke Daftar Aktif"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Pulihkan</span>
                                            </button>
                                            <button type="button" @click="forceDeleteSingle('kemitraan', item)" title="Hapus Permanen dari Database"
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
                                    <td colspan="9" class="py-14 text-center">
                                        <template x-if="kemitraanDitambahkan.length === 0">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">📦</div>
                                                <p class="text-sm font-bold text-slate-200">Tidak Ada Aset Ditambahkan Mitra yang Terhapus</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tong sampah untuk aset / fasilitas yang didatangkan oleh mitra rekanan saat ini kosong.</p>
                                            </div>
                                        </template>
                                        <template x-if="kemitraanDitambahkan.length > 0">
                                            <div class="flex flex-col items-center justify-center space-y-3">
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-amber-400 shadow-inner">🔍</div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-200">Tidak Ada Data yang Cocok</p>
                                                    <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan aset ditambahkan mitra yang sesuai dengan kata kunci pencarian atau filter waktu.</p>
                                                </div>
                                                <button type="button" @click="searchQuery = ''; setTimeFilter('all');"
                                                    class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-bold transition-all shadow-sm cursor-pointer">
                                                    Reset Filter &amp; Pencarian
                                                </button>
                                            </div>
                                        </template>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

    </div>
</template>
