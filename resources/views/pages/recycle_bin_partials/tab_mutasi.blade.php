<!-- TAB 1: MUTASI ASET TABLE (INTERNAL & EKSTERNAL) -->
<template x-if="activeModule === 'mutasi'">
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        <!-- SUB-TAB SWITCHER MUTASI (Internal Antar-Ruangan vs Eksternal Antar-OPD) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 mb-5 border-b border-slate-800/80 gap-3">
            <div class="flex items-center space-x-1.5 p-1 bg-slate-950/80 rounded-2xl border border-slate-800">
                <button type="button" @click="changeMutasiSubTab('internal')"
                    :style="mutasiSubTab === 'internal' ? 'border-color: #f59e0b; box-shadow: 0 0 14px rgba(245, 158, 11, 0.35);' : ''"
                    :class="mutasiSubTab === 'internal' 
                        ? 'bg-amber-500/15 border border-amber-500 text-amber-300 shadow-lg' 
                        : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer">
                    <span>🏢 Mutasi Internal (Antar-Ruangan)</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black transition-colors"
                        :class="mutasis.length > 0 ? 'bg-amber-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                        x-text="mutasis.length">0</span>
                </button>

                <button type="button" @click="changeMutasiSubTab('eksternal')"
                    :style="mutasiSubTab === 'eksternal' ? 'border-color: #06b6d4; box-shadow: 0 0 14px rgba(6, 182, 212, 0.35);' : ''"
                    :class="mutasiSubTab === 'eksternal' 
                        ? 'bg-cyan-500/15 border border-cyan-500 text-cyan-300 shadow-lg' 
                        : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer">
                    <span>🌐 Mutasi Eksternal (Transfer Antar-OPD)</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black transition-colors"
                        :class="mutasiEksternals.length > 0 ? 'bg-cyan-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                        x-text="mutasiEksternals.length">0</span>
                </button>
            </div>

            <div class="text-xs text-slate-400 flex items-center space-x-2">
                <span class="inline-block w-2 h-2 rounded-full" :class="mutasiSubTab === 'internal' ? 'bg-amber-400 animate-pulse' : 'bg-cyan-400 animate-pulse'"></span>
                <span class="text-[11px] font-medium" x-text="mutasiSubTab === 'internal' ? 'Ruang Lingkup: Arsip Mutasi Antar-Ruangan Internal RSUD' : 'Ruang Lingkup: Arsip Mutasi Aset Antar-OPD Pemkab'"></span>
            </div>
        </div>

        <!-- SUB-KONTEN 1: MUTASI INTERNAL TABLE -->
        <template x-if="mutasiSubTab === 'internal'">
            <div class="rounded-2xl border border-slate-800/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3.5 w-10 text-center">
                                    <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                        class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-3.5">Nomor BAMB</th>
                                <th class="px-4 py-3.5">Nama Aset / Barang</th>
                                <th class="px-4 py-3.5">Ruangan Asal & PJ</th>
                                <th class="px-4 py-3.5">Ruangan Tujuan & PJ</th>
                                <th class="px-4 py-3.5">Status Terakhir</th>
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
                                    <td class="px-4 py-4">
                                        <span class="font-mono font-bold text-amber-300 block" x-text="item.kode"></span>
                                        <span class="text-[10px] text-slate-500" x-text="item.jenis"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-white block" x-text="item.nama"></span>
                                        <span class="text-[10px] font-mono text-slate-400" x-text="'NIBAR: ' + item.kode_barang"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-slate-200 block" x-text="item.asal"></span>
                                        <span class="text-[10px] text-slate-500" x-text="'PJ: ' + (item.pemohon || '-')"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-indigo-300 font-semibold block" x-text="item.tujuan"></span>
                                        <span class="text-[10px] text-slate-500" x-text="'PJ: ' + (item.penerima_pj || '-')"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="item.status_terakhir"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-red-300 block" x-text="item.deleted_by"></span>
                                        <span class="text-[10px] text-slate-500">Label: 1 (Soft Delete)</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-mono text-slate-200 block text-[11px]" x-text="item.deleted_at"></span>
                                        <span class="text-[10px] text-slate-500" x-text="item.deleted_at_relative"></span>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[220px] w-[220px]"
                                        style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openDetail(item)" title="Lihat Detail Transaksi"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <span>👁️ Detail</span>
                                            </button>
                                            <button type="button" @click="restoreSingle('mutasi', item)" title="Pulihkan Data ke Status Aktif (Label 0)"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Pulihkan</span>
                                            </button>
                                            <button type="button" @click="forceDeleteSingle('mutasi', item)" title="Hapus Permanen dari Database"
                                                class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredItems.length === 0">
                                <tr>
                                    <td colspan="9" class="py-14 text-center">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-amber-400 shadow-inner">🏢</div>
                                            <template x-if="currentList.length === 0">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-200">Tong Sampah Mutasi Internal Kosong</p>
                                                    <p class="text-xs text-slate-500 max-w-sm mt-1">Tidak ada transaksi mutasi internal antar-ruangan yang berstatus terhapus.</p>
                                                </div>
                                            </template>
                                            <template x-if="currentList.length > 0">
                                                <div class="space-y-2">
                                                    <p class="text-sm font-bold text-slate-200">Tidak Ada Data yang Cocok</p>
                                                    <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan data mutasi internal dengan kata kunci atau filter waktu yang dipilih.</p>
                                                    <button type="button" @click="searchQuery = ''; setTimeFilter('all');"
                                                        class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 font-bold border border-slate-700 transition-all inline-flex items-center gap-1 cursor-pointer">
                                                        <span>🔄 Reset Filter & Pencarian</span>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </template>

        <!-- SUB-KONTEN 2: MUTASI EKSTERNAL TABLE -->
        <template x-if="mutasiSubTab === 'eksternal'">
            <div class="space-y-4">
                <!-- DIRECTION FILTER PILLS (Semua / Masuk / Keluar) -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-2 bg-slate-950/70 border border-slate-800/80 rounded-2xl shadow-inner">
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <!-- Button 1: Semua Arah -->
                        <button type="button" @click="setMutasiEksternalFilter('all')"
                            :style="mutasiEksternalFilter === 'all' ? 'border-color: #06b6d4; box-shadow: 0 0 12px rgba(6, 182, 212, 0.25);' : ''"
                            :class="mutasiEksternalFilter === 'all'
                                ? 'bg-cyan-500/15 border border-cyan-500 text-cyan-300 font-bold'
                                : 'bg-slate-900/60 border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer active:scale-95">
                            <span>🌐 Semua Arah</span>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black"
                                :class="mutasiEksternalFilter === 'all' ? 'bg-cyan-500 text-slate-950' : 'bg-slate-800 text-slate-400'"
                                x-text="mutasiEksternals.length">0</span>
                        </button>

                        <!-- Button 2: Mutasi Masuk -->
                        <button type="button" @click="setMutasiEksternalFilter('masuk')"
                            :style="mutasiEksternalFilter === 'masuk' ? 'border-color: #14b8a6; box-shadow: 0 0 12px rgba(20, 184, 166, 0.25);' : ''"
                            :class="mutasiEksternalFilter === 'masuk'
                                ? 'bg-teal-500/15 border border-teal-500 text-teal-300 font-bold'
                                : 'bg-slate-900/60 border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer active:scale-95">
                            <span class="text-teal-400">📥</span>
                            <span>Pelimpahan Masuk SKPD</span>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black"
                                :class="mutasiEksternalFilter === 'masuk' ? 'bg-teal-400 text-slate-950' : 'bg-slate-800 text-slate-400'"
                                x-text="countMutasiEksternalMasuk">0</span>
                        </button>

                        <!-- Button 3: Mutasi Keluar -->
                        <button type="button" @click="setMutasiEksternalFilter('keluar')"
                            :style="mutasiEksternalFilter === 'keluar' ? 'border-color: #f59e0b; box-shadow: 0 0 12px rgba(245, 158, 11, 0.25);' : ''"
                            :class="mutasiEksternalFilter === 'keluar'
                                ? 'bg-amber-500/15 border border-amber-500 text-amber-300 font-bold'
                                : 'bg-slate-900/60 border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-800/60'"
                            class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer active:scale-95">
                            <span class="text-amber-400">📤</span>
                            <span>Mutasi Keluar ke OPD</span>
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black"
                                :class="mutasiEksternalFilter === 'keluar' ? 'bg-amber-400 text-slate-950' : 'bg-slate-800 text-slate-400'"
                                x-text="countMutasiEksternalKeluar">0</span>
                        </button>
                    </div>

                    <!-- Keterangan Context Filter -->
                    <div class="text-[11px] text-slate-400 px-3 py-1 flex items-center space-x-1.5 font-medium">
                        <span class="w-1.5 h-1.5 rounded-full"
                            :class="mutasiEksternalFilter === 'masuk' ? 'bg-teal-400' : (mutasiEksternalFilter === 'keluar' ? 'bg-amber-400' : 'bg-cyan-400')"></span>
                        <template x-if="mutasiEksternalFilter === 'all'">
                            <span class="text-slate-400">Menampilkan seluruh arsip transfer antar-OPD</span>
                        </template>
                        <template x-if="mutasiEksternalFilter === 'masuk'">
                            <span class="text-teal-300">Menampilkan perolehan aset masuk dari SKPD/OPD luar</span>
                        </template>
                        <template x-if="mutasiEksternalFilter === 'keluar'">
                            <span class="text-amber-300">Menampilkan aset RSUD yang diserahkan/dimutasi ke OPD luar</span>
                        </template>
                    </div>
                </div>

                <!-- TABLE MUTASI EKSTERNAL -->
                <div class="rounded-2xl border border-slate-800/80 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                                <tr>
                                    <th class="px-4 py-3.5 w-10 text-center">
                                        <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                            class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                    </th>
                                    <th class="px-4 py-3.5">Nomor BAMB / BAST</th>
                                    <th class="px-4 py-3.5">Nama Barang / Aset</th>
                                    <th class="px-4 py-3.5">Nilai Perolehan</th>
                                    <th class="px-4 py-3.5">Asal & Penyerah</th>
                                    <th class="px-4 py-3.5">Tujuan & Penerima</th>
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
                                        <td class="px-4 py-4">
                                            <span class="font-mono font-bold text-cyan-300 block" x-text="item.kode"></span>
                                            <template x-if="item.tipe === 'keluar'">
                                                <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-amber-500/15 border border-amber-500/40 text-amber-300 inline-flex items-center gap-1 mt-1 shadow-sm">
                                                    <span>📤</span> Mutasi Keluar ke OPD
                                                </span>
                                            </template>
                                            <template x-if="item.tipe !== 'keluar'">
                                                <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-cyan-500/15 border border-cyan-500/40 text-cyan-300 inline-flex items-center gap-1 mt-1 shadow-sm">
                                                    <span>📥</span> Pelimpahan Masuk SKPD
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="font-bold text-white block" x-text="item.nama"></span>
                                            <span class="text-[10px] font-mono text-slate-400" x-text="'Kode 108: ' + item.kode_barang"></span>
                                        </td>
                                        <td class="px-4 py-4 font-mono font-bold text-emerald-400" x-text="item.nilai_perolehan_formatted"></td>
                                        <td class="px-4 py-4">
                                            <span class="text-[9.5px] uppercase font-bold tracking-wider block text-slate-400" x-text="item.tipe === 'keluar' ? 'Dari RSUD:' : 'Dari OPD Pengirim:'"></span>
                                            <span class="text-slate-200 font-semibold block mt-0.5" x-text="item.asal"></span>
                                            <span class="text-[10px] text-slate-500 block" x-text="'PJ: ' + (item.pemohon || '-')"></span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="text-[9.5px] uppercase font-bold tracking-wider block text-slate-400" x-text="item.tipe === 'keluar' ? 'Tujuan OPD Luar:' : 'Penempatan di RSUD:'"></span>
                                            <span class="font-semibold block mt-0.5" :class="item.tipe === 'keluar' ? 'text-amber-300' : 'text-cyan-300'" x-text="item.tujuan"></span>
                                            <span class="text-[10px] text-slate-500 block" x-text="'PJ: ' + (item.penerima_pj || '-')"></span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="font-bold text-red-300 block" x-text="item.deleted_by"></span>
                                            <span class="text-[10px] text-slate-500">Label: 1 (Soft Delete)</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="font-mono text-slate-200 block text-[11px]" x-text="item.deleted_at"></span>
                                            <span class="text-[10px] text-slate-500" x-text="item.deleted_at_relative"></span>
                                        </td>
                                        <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[220px] w-[220px]"
                                            style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                            <div class="flex items-center justify-center gap-1.5">
                                                <button type="button" @click="openDetail(item)" title="Lihat Detail Mutasi Eksternal"
                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <span>👁️ Detail</span>
                                                </button>
                                                <button type="button" @click="restoreSingle('mutasi_eksternal', item)" title="Pulihkan Data ke Status Aktif (Label 0)"
                                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span>Pulihkan</span>
                                                </button>
                                                <button type="button" @click="forceDeleteSingle('mutasi_eksternal', item)" title="Hapus Permanen dari Database"
                                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredItems.length === 0">
                                    <tr>
                                        <td colspan="9" class="py-14 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl shadow-inner"
                                                    :class="mutasiEksternalFilter === 'masuk' ? 'text-teal-400' : (mutasiEksternalFilter === 'keluar' ? 'text-amber-400' : 'text-cyan-400')"
                                                    x-text="mutasiEksternalFilter === 'masuk' ? '📥' : (mutasiEksternalFilter === 'keluar' ? '📤' : '🌐')">
                                                </div>
                                                <template x-if="currentList.length === 0">
                                                    <div>
                                                        <p class="text-sm font-bold text-slate-200"
                                                            x-text="mutasiEksternalFilter === 'masuk' ? 'Tong Sampah Mutasi Masuk Kosong' : (mutasiEksternalFilter === 'keluar' ? 'Tong Sampah Mutasi Keluar Kosong' : 'Tong Sampah Mutasi Eksternal Kosong')"></p>
                                                        <p class="text-xs text-slate-500 max-w-sm mt-1"
                                                            x-text="mutasiEksternalFilter === 'masuk' ? 'Tidak ada data perolehan aset masuk dari SKPD luar yang berstatus terhapus.' : (mutasiEksternalFilter === 'keluar' ? 'Tidak ada transaksi transfer mutasi aset keluar ke OPD yang berstatus terhapus.' : 'Tidak ada transaksi mutasi eksternal antar-OPD yang berstatus terhapus.')"></p>
                                                    </div>
                                                </template>
                                                <template x-if="currentList.length > 0">
                                                    <div class="space-y-2">
                                                        <p class="text-sm font-bold text-slate-200">Tidak Ada Data yang Cocok</p>
                                                        <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan data mutasi eksternal dengan kata kunci atau filter waktu yang dipilih.</p>
                                                        <button type="button" @click="searchQuery = ''; setTimeFilter('all');"
                                                            class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 font-bold border border-slate-700 transition-all inline-flex items-center gap-1 cursor-pointer">
                                                            <span>🔄 Reset Filter & Pencarian</span>
                                                        </button>
                                                    </div>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>
