<x-layout title="Pusat Pemulihan Data - SIMAT-RK">
    @section('page-title', 'Pusat Pemulihan Data')
    @section('breadcrumb', 'Audit & Pemulihan / Pusat Data Terhapus')

    <div x-data="recycleBinApp()" x-cloak class="space-y-6">

        <!-- HEADER BANNER & KPI CARDS -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900/90 to-red-950/40 border border-slate-800 p-6 md:p-8 shadow-2xl">
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-40 -bottom-10 w-48 h-48 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 space-y-6">
                <!-- BARIS ATAS: JUDUL & STATISTIK GLOBAL AUDIT -->
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div>
                        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-red-500/15 border border-red-500/30 text-red-300 text-xs font-bold mb-3 shadow-sm">
                            <span>♻️ Audit Trail & Central Recycle Bin</span>
                        </div>
                        <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">Pusat Data Terhapus</h1>
                        <p class="text-slate-400 text-xs md:text-sm mt-1 max-w-2xl leading-relaxed">
                            Arsip terpusat seluruh data transaksi dan inventaris aset SIMAT yang telah dinonaktifkan atau dihapus sementara. Anda dapat meninjau jejak audit penghapusan, memulihkan data aktif ke sistem, atau memusnahkannya secara permanen.
                        </p>
                    </div>

                    <!-- RINGKASAN AUDIT GLOBAL -->
                    <div class="flex items-center gap-3 shrink-0">
                        <div class="p-3.5 px-5 rounded-2xl bg-slate-950/70 border border-slate-800/90 text-center shadow-inner min-w-[130px]">
                            <span class="text-[10px] uppercase tracking-wider font-bold text-slate-400 block mb-0.5">Total Terhapus</span>
                            <span class="text-2xl font-black text-white font-mono" x-text="totalCount"></span>
                            <span class="text-[10px] text-slate-500 block">Semua Modul</span>
                        </div>
                        <div class="p-3.5 px-5 rounded-2xl bg-slate-950/70 border border-slate-800/90 text-center shadow-inner min-w-[130px]">
                            <span class="text-[10px] uppercase tracking-wider font-bold text-rose-400 block mb-0.5">30 Hari Terakhir</span>
                            <span class="text-2xl font-black text-rose-300 font-mono">{{ $totalThisMonth }}</span>
                            <span class="text-[10px] text-slate-500 block">Aktivitas Hapus</span>
                        </div>
                    </div>
                </div>

                <!-- BARIS BAWAH: KARTU INDIKATOR 4 MODUL (ASTAP, UNIT, DISTRIBUSI, MUTASI) -->
                <div class="pt-5 border-t border-slate-800/80">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                            Rincian Data Terhapus Per Modul SIMAT:
                        </span>
                        <span class="text-[10.5px] text-slate-500 hidden sm:inline">Klik kartu untuk beralih tampilan modul</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <!-- 1. Master ASTAP -->
                        <div @click="changeTab('astap')" 
                            class="cursor-pointer p-3.5 rounded-2xl border transition-all hover:scale-[1.02] relative group"
                            :class="activeModule === 'astap' 
                                ? 'bg-gradient-to-br from-blue-500/20 to-blue-950/30 border-blue-500/60 ring-2 ring-blue-500/30 shadow-lg shadow-blue-500/10' 
                                : 'bg-slate-950/60 border-slate-800/90 hover:border-slate-700 hover:bg-slate-900/80'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-base p-1.5 rounded-xl bg-blue-500/15 border border-blue-500/30 text-blue-300">📦</span>
                                <span class="text-xs font-mono font-black px-2 py-0.5 rounded-lg transition-colors"
                                    :class="getModuleCount('astap') > 0 
                                        ? 'bg-blue-500 text-white font-black shadow-sm' 
                                        : 'bg-slate-800/80 text-slate-400'"
                                    x-text="getModuleCount('astap')">0</span>
                            </div>
                            <span class="text-xs font-bold text-white block truncate group-hover:text-blue-300 transition-colors">Master ASTAP</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="getModuleCount('astap') > 0 ? (astaps.length + ' Paket · ' + nibars.length + ' NIBAR') : 'Tidak ada data'"></span>
                        </div>

                        <!-- 2. Unit & Paviliun -->
                        <div @click="changeTab('unit')" 
                            class="cursor-pointer p-3.5 rounded-2xl border transition-all hover:scale-[1.02] relative group"
                            :class="activeModule === 'unit' 
                                ? 'bg-gradient-to-br from-cyan-500/20 to-cyan-950/30 border-cyan-500/60 ring-2 ring-cyan-500/30 shadow-lg shadow-cyan-500/10' 
                                : 'bg-slate-950/60 border-slate-800/90 hover:border-slate-700 hover:bg-slate-900/80'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-base p-1.5 rounded-xl bg-cyan-500/15 border border-cyan-500/30 text-cyan-300">🏥</span>
                                <span class="text-xs font-mono font-black px-2 py-0.5 rounded-lg transition-colors"
                                    :class="getModuleCount('unit') > 0 
                                        ? 'bg-cyan-500 text-slate-950 font-black shadow-sm' 
                                        : 'bg-slate-800/80 text-slate-400'"
                                    x-text="getModuleCount('unit')">0</span>
                            </div>
                            <span class="text-xs font-bold text-white block truncate group-hover:text-cyan-300 transition-colors">Unit & Paviliun</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="getModuleCount('unit') > 0 ? getModuleCount('unit') + ' data terhapus' : 'Tidak ada data'"></span>
                        </div>

                        <!-- 3. Distribusi Aset -->
                        <div @click="changeTab('distribusi')" 
                            class="cursor-pointer p-3.5 rounded-2xl border transition-all hover:scale-[1.02] relative group"
                            :class="activeModule === 'distribusi' 
                                ? 'bg-gradient-to-br from-emerald-500/20 to-emerald-950/30 border-emerald-500/60 ring-2 ring-emerald-500/30 shadow-lg shadow-emerald-500/10' 
                                : 'bg-slate-950/60 border-slate-800/90 hover:border-slate-700 hover:bg-slate-900/80'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-base p-1.5 rounded-xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-300">🚚</span>
                                <span class="text-xs font-mono font-black px-2 py-0.5 rounded-lg transition-colors"
                                    :class="getModuleCount('distribusi') > 0 
                                        ? 'bg-emerald-500 text-slate-950 font-black shadow-sm' 
                                        : 'bg-slate-800/80 text-slate-400'"
                                    x-text="getModuleCount('distribusi')">0</span>
                            </div>
                            <span class="text-xs font-bold text-white block truncate group-hover:text-emerald-300 transition-colors">Distribusi Aset</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="getModuleCount('distribusi') > 0 ? getModuleCount('distribusi') + ' data terhapus' : 'Tidak ada data'"></span>
                        </div>

                        <!-- 4. Mutasi Aset -->
                        <div @click="changeTab('mutasi')" 
                            class="cursor-pointer p-3.5 rounded-2xl border transition-all hover:scale-[1.02] relative group"
                            :class="activeModule === 'mutasi' 
                                ? 'bg-gradient-to-br from-amber-500/20 to-amber-950/30 border-amber-500/60 ring-2 ring-amber-500/30 shadow-lg shadow-amber-500/10' 
                                : 'bg-slate-950/60 border-slate-800/90 hover:border-slate-700 hover:bg-slate-900/80'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-base p-1.5 rounded-xl bg-amber-500/15 border border-amber-500/30 text-amber-300">🔄</span>
                                <span class="text-xs font-mono font-black px-2 py-0.5 rounded-lg transition-colors"
                                    :class="getModuleCount('mutasi') > 0 
                                        ? 'bg-amber-500 text-slate-950 font-black shadow-sm' 
                                        : 'bg-slate-800/80 text-slate-400'"
                                    x-text="getModuleCount('mutasi')">0</span>
                            </div>
                            <span class="text-xs font-bold text-white block truncate group-hover:text-amber-300 transition-colors">Mutasi Aset</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="getModuleCount('mutasi') > 0 ? getModuleCount('mutasi') + ' data terhapus' : 'Tidak ada data'"></span>
                        </div>

                        <!-- 5. Akun Pengguna -->
                        <div @click="changeTab('users')" 
                            class="cursor-pointer p-3.5 rounded-2xl border transition-all hover:scale-[1.02] relative group"
                            :class="activeModule === 'users' 
                                ? 'bg-gradient-to-br from-rose-500/20 to-rose-950/30 border-rose-500/60 ring-2 ring-rose-500/30 shadow-lg shadow-rose-500/10' 
                                : 'bg-slate-950/60 border-slate-800/90 hover:border-slate-700 hover:bg-slate-900/80'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-base p-1.5 rounded-xl bg-rose-500/15 border border-rose-500/30 text-rose-300">👥</span>
                                <span class="text-xs font-mono font-black px-2 py-0.5 rounded-lg transition-colors"
                                    :class="getModuleCount('users') > 0 
                                        ? 'bg-rose-500 text-white font-black shadow-sm' 
                                        : 'bg-slate-800/80 text-slate-400'"
                                    x-text="getModuleCount('users')">0</span>
                            </div>
                            <span class="text-xs font-bold text-white block truncate group-hover:text-rose-300 transition-colors">Akun Pengguna</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="getModuleCount('users') > 0 ? getModuleCount('users') + ' akun terhapus' : 'Tidak ada data'"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <!-- TOOLBAR: SEARCH & BULK ACTIONS -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="relative flex-1 w-full">
                <input type="text" x-model="searchQuery" :placeholder="searchPlaceholder"
                    class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/50 transition-all">
                <svg class="w-4 h-4 text-red-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
            </div>

            <div class="flex items-center space-x-2.5 shrink-0 w-full md:w-auto justify-end flex-wrap gap-y-2">
                {{-- Tombol Bulk Restore --}}
                <button type="button" @click="bulkRestore(currentTargetModule)" :disabled="selectedIds.length === 0"
                    :class="selectedIds.length > 0 ? 'bg-indigo-600 hover:bg-indigo-500 text-white cursor-pointer shadow-lg shadow-indigo-600/25' : 'bg-slate-800/50 text-slate-500 cursor-not-allowed border-slate-800'"
                    class="px-4 py-2.5 rounded-2xl border border-indigo-500/40 text-xs font-bold transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Pulihkan Terpilih (<span x-text="selectedIds.length"></span>)</span>
                </button>

                {{-- Tombol Bulk Force Delete (Hapus Terpilih) --}}
                <button type="button" @click="bulkForceDelete(currentTargetModule)" :disabled="selectedIds.length === 0"
                    :class="selectedIds.length > 0 ? 'bg-rose-600/25 hover:bg-rose-600/35 text-rose-300 border-rose-500/50 cursor-pointer shadow-lg shadow-rose-600/15' : 'bg-slate-800/30 text-slate-600 cursor-not-allowed border-slate-800/60'"
                    class="px-4 py-2.5 rounded-2xl border text-xs font-bold transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    <span>Hapus Terpilih (<span x-text="selectedIds.length"></span>)</span>
                </button>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TAB 1: MUTASI ASET TABLE -->
        <!-- ========================================================================= -->
        <template x-if="activeModule === 'mutasi'">
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
                                            <span class="font-mono font-bold text-cyan-300 block" x-text="item.kode"></span>
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
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">✨</div>
                                                <p class="text-sm font-bold text-slate-200">Tong Sampah Mutasi Kosong</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tidak ada transaksi mutasi aset yang berstatus terhapus dalam sistem.</p>
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

        <!-- ========================================================================= -->
        <!-- TAB 2: MASTER ASTAP & UNIT NIBAR TABLE -->
        <!-- ========================================================================= -->
        <template x-if="activeModule === 'astap'">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
                <!-- SUB-TAB SWITCHER (Paket Pengadaan vs Unit Fisik NIBAR) -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 mb-5 border-b border-slate-800/80 gap-3">
                    <div class="flex items-center space-x-1.5 p-1 bg-slate-950/80 rounded-2xl border border-slate-800/80">
                        <button type="button" @click="changeAstapSubTab('packet')"
                            :class="astapSubTab === 'packet' 
                                ? 'bg-blue-600 text-white shadow-md shadow-blue-600/30' 
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer">
                            <span>📦 Paket Pengadaan ASTAP</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-black"
                                :class="astapSubTab === 'packet' ? 'bg-blue-800 text-blue-100' : 'bg-slate-800 text-slate-400'"
                                x-text="astaps.length">0</span>
                        </button>
                        <button type="button" @click="changeAstapSubTab('nibar')"
                            :class="astapSubTab === 'nibar' 
                                ? 'bg-cyan-600 text-white shadow-md shadow-cyan-600/30' 
                                : 'text-slate-400 hover:text-slate-200 hover:bg-slate-800/50'"
                            class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer">
                            <span>🏷️ Unit Fisik NIBAR</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-black"
                                :class="astapSubTab === 'nibar' ? 'bg-cyan-800 text-cyan-100' : 'bg-slate-800 text-slate-400'"
                                x-text="nibars.length">0</span>
                        </button>
                    </div>
                    <div class="text-xs text-slate-400 flex items-center gap-1.5">
                        <template x-if="astapSubTab === 'packet'">
                            <span>📦 Paket pengadaan ASTAP yang terhapus utuh bersama seluruh unitnya.</span>
                        </template>
                        <template x-if="astapSubTab === 'nibar'">
                            <span>🏷️ Register unit barang (NIBAR) yang dihapus satuan dari paket pengadaan aktif.</span>
                        </template>
                    </div>
                </div>

                <!-- SUB-TAB 1: PAKET PENGADAAN ASTAP -->
                <template x-if="astapSubTab === 'packet'">
                    <div class="rounded-2xl border border-slate-800/80 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                                    <tr>
                                        <th class="px-4 py-3.5 w-10 text-center">
                                            <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                                class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                        </th>
                                        <th class="px-4 py-3.5">Nama Barang / Aset</th>
                                        <th class="px-4 py-3.5">Kode 108 / Kategori</th>
                                        <th class="px-4 py-3.5">Volume & Satuan</th>
                                        <th class="px-4 py-3.5">Tahun & Nilai Realisasi</th>
                                        <th class="px-4 py-3.5">Penyedia / SPK</th>
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
                                                <span class="font-bold text-white block text-sm" x-text="item.nama"></span>
                                                <span class="text-[10px] text-emerald-400 font-mono" x-text="item.item_count + ' Register NIBAR Terkait'"></span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="font-mono text-cyan-300 font-bold block" x-text="item.kode"></span>
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300" x-text="item.category"></span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="font-bold text-slate-200 block" x-text="item.volume"></span>
                                                <span class="text-[10px] text-slate-500" x-text="'Harga: ' + item.harga_satuan"></span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="text-amber-300 font-bold block font-mono" x-text="item.total_realisasi"></span>
                                                <span class="text-[10px] text-slate-400" x-text="'Tahun: ' + item.tahun"></span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="text-slate-200 block" x-text="item.penyedia"></span>
                                                <span class="text-[10px] font-mono text-slate-500" x-text="'SPK: ' + item.spk_nomor"></span>
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
                                                    <button type="button" @click="openDetail(item)" title="Lihat Detail & Register NIBAR"
                                                        class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                        <span>👁️ Detail</span>
                                                    </button>
                                                    <button type="button" @click="restoreSingle('astap', item)" title="Pulihkan Data ke Status Aktif (Label 0)"
                                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        <span>Pulihkan</span>
                                                    </button>
                                                    <button type="button" @click="forceDeleteSingle('astap', item)" title="Hapus Permanen dari Database"
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
                                                    <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">📦</div>
                                                    <p class="text-sm font-bold text-slate-200">Tong Sampah Master ASTAP Kosong</p>
                                                    <p class="text-xs text-slate-500 max-w-sm">Tidak ada barang aset tetap yang berstatus terhapus.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </template>

                <!-- SUB-TAB 2: UNIT FISIK NIBAR -->
                <template x-if="astapSubTab === 'nibar'">
                    <div class="rounded-2xl border border-slate-800/80 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs border-collapse">
                                <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                                    <tr>
                                        <th class="px-4 py-3.5 w-10 text-center">
                                            <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                                class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                        </th>
                                        <th class="px-4 py-3.5">Nomor Induk Barang (NIBAR)</th>
                                        <th class="px-4 py-3.5">Aset Induk & Kode 108</th>
                                        <th class="px-4 py-3.5">Ruangan Pemegang</th>
                                        <th class="px-4 py-3.5">Kondisi & Status</th>
                                        <th class="px-4 py-3.5">SPK Pengadaan</th>
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
                                                <span class="font-mono font-bold text-cyan-300 block text-xs tracking-wide" x-text="item.nibar"></span>
                                                <span class="text-[10px] font-mono text-slate-500" x-text="'No Reg: ' + item.no_register"></span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="font-bold text-white block text-sm" x-text="item.nama_barang"></span>
                                                <div class="flex items-center space-x-1.5 mt-0.5">
                                                    <span class="font-mono text-cyan-400 text-[11px]" x-text="item.kode_108"></span>
                                                    <span class="px-1.5 py-0.2 rounded text-[9px] font-bold bg-slate-800 text-slate-300" x-text="item.kategori"></span>
                                                </div>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="font-semibold text-slate-200 block" x-text="item.ruang"></span>
                                                <span class="text-[10px] text-slate-500" x-text="'Tahun: ' + item.tahun"></span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border inline-block"
                                                    :class="item.kondisi === 'Baik' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : (item.kondisi === 'Rusak Berat' ? 'bg-rose-500/15 text-rose-300 border-rose-500/30' : 'bg-amber-500/15 text-amber-300 border-amber-500/30')"
                                                    x-text="item.kondisi"></span>
                                                <span class="text-[10px] text-slate-400 block mt-1" x-text="'Status: ' + item.status"></span>
                                            </td>
                                            <td class="px-4 py-4">
                                                <span class="text-slate-300 block font-mono text-[11px]" x-text="item.spk_nomor"></span>
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
                                                    <button type="button" @click="openDetail(item)" title="Lihat Detail NIBAR"
                                                        class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                        <span>👁️ Detail</span>
                                                    </button>
                                                    <button type="button" @click="restoreSingle('nibar', item)" title="Pulihkan NIBAR ke Paket Pengadaan Aktif"
                                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                        <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                        <span>Pulihkan</span>
                                                    </button>
                                                    <button type="button" @click="forceDeleteSingle('nibar', item)" title="Hapus Permanen dari Database"
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
                                                    <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-cyan-400 shadow-inner">🏷️</div>
                                                    <p class="text-sm font-bold text-slate-200">Tong Sampah Register NIBAR Kosong</p>
                                                    <p class="text-xs text-slate-500 max-w-sm">Tidak ada register NIBAR individual yang berstatus terhapus.</p>
                                                </div>
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

        <!-- ========================================================================= -->
        <!-- TAB 3: DISTRIBUSI ASET TABLE -->
        <!-- ========================================================================= -->
        <template x-if="activeModule === 'distribusi'">
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
                                    <th class="px-4 py-3.5">Kode Distribusi</th>
                                    <th class="px-4 py-3.5">Nomor BAST</th>
                                    <th class="px-4 py-3.5">Unit / Ruangan Tujuan</th>
                                    <th class="px-4 py-3.5">Tanggal Distribusi</th>
                                    <th class="px-4 py-3.5">Total Qty & Status</th>
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
                                        <td class="px-4 py-4 font-mono font-bold text-cyan-300" x-text="item.kode"></td>
                                        <td class="px-4 py-4 font-mono text-slate-300" x-text="item.bast_nomor"></td>
                                        <td class="px-4 py-4 font-bold text-white" x-text="item.tujuan"></td>
                                        <td class="px-4 py-4 text-slate-300" x-text="item.tanggal"></td>
                                        <td class="px-4 py-4">
                                            <span class="font-bold text-teal-300 block" x-text="item.total_qty"></span>
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-400" x-text="item.status"></span>
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
                                                <button type="button" @click="openDetail(item)" title="Lihat Rincian Distribusi"
                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <span>👁️ Detail</span>
                                                </button>
                                                <button type="button" @click="restoreSingle('distribusi', item)" title="Pulihkan Data ke Status Aktif (Label 0)"
                                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span>Pulihkan</span>
                                                </button>
                                                <button type="button" @click="forceDeleteSingle('distribusi', item)" title="Hapus Permanen dari Database"
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
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">🚚</div>
                                                <p class="text-sm font-bold text-slate-200">Tong Sampah Distribusi Kosong</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tidak ada transaksi distribusi aset yang berstatus terhapus.</p>
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



        <!-- ========================================================================= -->
        <!-- TAB 5: UNIT & PAVILIUN TABLE -->
        <!-- ========================================================================= -->
        <template x-if="activeModule === 'unit'">
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
                                    <th class="px-4 py-3.5">Kode Unit</th>
                                    <th class="px-4 py-3.5">Nama Unit / Paviliun</th>
                                    <th class="px-4 py-3.5 text-center">Status Aset & BAST</th>
                                    <th class="px-4 py-3.5">Tipe Ruangan</th>
                                    <th class="px-4 py-3.5">Kepala Ruangan & NIP</th>
                                    <th class="px-4 py-3.5">Email Akun Sub Admin</th>
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
                                        <td class="px-4 py-4 font-mono font-bold text-indigo-300" x-text="item.kode"></td>
                                        <td class="px-4 py-4 font-bold text-white text-sm" x-text="item.nama"></td>
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <div class="flex flex-col items-center gap-1">
                                                <template x-if="item.total_aset > 0">
                                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm"
                                                        :title="'Ruangan ini masih menampung ' + item.total_aset + ' aset di database'">
                                                        <span>⚠️</span>
                                                        <span x-text="item.total_aset + ' Aset'"></span>
                                                    </span>
                                                </template>
                                                <template x-if="!item.total_aset || item.total_aset == 0">
                                                    <span class="text-[10.5px] text-slate-500 font-mono">0 Aset</span>
                                                </template>

                                                <!-- Status Arsip BAST Distribusi -->
                                                <template x-if="item.total_bast > 0">
                                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/15 text-blue-300 border border-blue-500/30 shadow-sm"
                                                        :title="'Ruangan ini memiliki ' + item.total_bast + ' dokumen BAST Distribusi resmi yang dilindungi audit'">
                                                        <span>📜</span>
                                                        <span x-text="item.total_bast + ' BAST (Terkunci)'"></span>
                                                    </span>
                                                </template>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 text-slate-300" x-text="item.tipe"></td>
                                        <td class="px-4 py-4">
                                            <span class="font-bold text-slate-200 block" x-text="item.kepala"></span>
                                            <span class="text-[10px] font-mono text-slate-500" x-text="'NIP: ' + item.nip"></span>
                                        </td>
                                        <td class="px-4 py-4 font-mono text-cyan-400 text-[11px]" x-text="item.email"></td>
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
                                                <button type="button" @click="openDetail(item)" title="Lihat Detail Unit"
                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <span>👁️ Detail</span>
                                                </button>
                                                <button type="button" @click="restoreSingle('unit', item)" title="Pulihkan Data ke Status Aktif (Label 0)"
                                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span>Pulihkan</span>
                                                </button>
                                                <button type="button" @click="forceDeleteSingle('unit', item)" title="Hapus Permanen dari Database"
                                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredItems.length === 0">
                                    <tr>
                                        <td colspan="10" class="py-14 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">🏥</div>
                                                <p class="text-sm font-bold text-slate-200">Tong Sampah Unit & Paviliun Kosong</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tidak ada data unit atau ruangan yang berstatus terhapus.</p>
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
 
        <!-- ========================================================================= -->
        <!-- TAB 6: AKUN PENGGUNA (USERS) TABLE -->
        <!-- ========================================================================= -->
        <template x-if="activeModule === 'users'">
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
                                    <th class="px-4 py-3.5">Nama & NIP Pegawai</th>
                                    <th class="px-4 py-3.5">Email Kredensial</th>
                                    <th class="px-4 py-3.5 text-center">Role Otorisasi</th>
                                    <th class="px-4 py-3.5">Unit Penugasan</th>
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
                                            <div class="flex items-center space-x-2.5">
                                                <div class="w-7 h-7 rounded-xl flex items-center justify-center font-bold text-xs shrink-0"
                                                     :class="{
                                                         'bg-amber-500/20 text-amber-300 border border-amber-500/30': item.role === 'master_admin',
                                                         'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': item.role === 'admin',
                                                         'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': item.role === 'sub_admin'
                                                     }"
                                                     x-text="(item.name || 'U').substring(0, 1)"></div>
                                                <div>
                                                    <span class="font-bold text-white block truncate" x-text="item.name"></span>
                                                    <span class="text-[10px] font-mono text-slate-500" x-text="'NIP: ' + item.nip"></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 font-mono text-cyan-400 text-[11px]" x-text="item.email"></td>
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-block whitespace-nowrap"
                                                :class="{
                                                    'bg-amber-500/20 text-amber-300 border-amber-500/30': item.role === 'master_admin',
                                                    'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': item.role === 'admin',
                                                    'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': item.role === 'sub_admin'
                                                }"
                                                x-text="item.role_label || item.role">
                                            </span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <span class="font-bold text-slate-200 block" x-text="item.unit || '-'"></span>
                                            <span class="text-[10px] text-slate-500" x-text="item.penugasan || '-'"></span>
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
                                                <button type="button" @click="openDetail(item)" title="Lihat Detail Akun"
                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <span>👁️ Detail</span>
                                                </button>
                                                <button type="button" @click="restoreSingle('users', item)" title="Pulihkan Akun ke Status Aktif"
                                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span>Pulihkan</span>
                                                </button>
                                                <button type="button" @click="forceDeleteSingle('users', item)" title="Hapus Permanen dari Sistem"
                                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="filteredItems.length === 0">
                                    <tr>
                                        <td colspan="8" class="py-14 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-rose-400 shadow-inner">👥</div>
                                                <p class="text-sm font-bold text-slate-200">Tong Sampah Akun Pengguna Kosong</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tidak ada akun pengguna atau staf yang berstatus terhapus.</p>
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

        <!-- ========================================================================= -->
        <!-- MODAL DETAIL PREVIEW (DYNAMIC FOR ALL 5 MODULES) -->
        <!-- ========================================================================= -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
            style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px);"
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

                        <!-- 1. DETAIL KHUSUS MUTASI -->
                        <template x-if="activeModule === 'mutasi'">
                            <div class="space-y-3">
                                <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                                    <div>
                                        <span class="text-slate-500 text-[10px] uppercase font-bold block">Nomor BAMB:</span>
                                        <span class="font-mono font-bold text-cyan-300 text-sm" x-text="selectedItem.kode"></span>
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
                                        <span class="text-xs text-slate-400 block" x-text="'Tgl: ' + selectedItem.tanggal"></span>
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



                        <!-- 5. DETAIL KHUSUS UNIT -->
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

                        <!-- DETAIL PENGGUNA -->
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

    </div>

    <!-- SCRIPT LOGIC RECYCLE BIN APP -->
    <script>
        function recycleBinApp() {
            return {
                activeModule: '{{ $activeTab }}',
                mutasis: {{ Js::from($deletedMutasis) }},
                astaps: {{ Js::from($deletedAstaps) }},
                nibars: {{ Js::from($deletedNibars) }},
                distribusis: {{ Js::from($deletedDistribusis) }},
                units: {{ Js::from($deletedUnits) }},
                users: {{ Js::from($deletedUsers ?? []) }},
                astapSubTab: 'packet',

                selectedIds: [],
                searchQuery: '',
                showDetailModal: false,
                selectedItem: null,

                changeTab(tab) {
                    this.activeModule = tab;
                    this.selectedIds = [];
                    this.searchQuery = '';
                },

                changeAstapSubTab(subTab) {
                    this.astapSubTab = subTab;
                    this.selectedIds = [];
                    this.searchQuery = '';
                },

                get currentTargetModule() {
                    if (this.activeModule === 'astap' && this.astapSubTab === 'nibar') {
                        return 'nibar';
                    }
                    return this.activeModule;
                },

                get totalCount() {
                    return this.mutasis.length + this.astaps.length + this.nibars.length + this.distribusis.length + this.units.length + this.users.length;
                },

                getModuleCount(mod) {
                    if (mod === 'mutasi') return this.mutasis.length;
                    if (mod === 'astap') return this.astaps.length + this.nibars.length;
                    if (mod === 'distribusi') return this.distribusis.length;
                    if (mod === 'unit') return this.units.length;
                    if (mod === 'users') return this.users.length;
                    return 0;
                },

                get activeModuleName() {
                    if (this.activeModule === 'astap') {
                        return this.astapSubTab === 'nibar' ? 'Register NIBAR' : 'Paket Master ASTAP';
                    }
                    const map = {
                        mutasi: 'Mutasi Aset',
                        distribusi: 'Distribusi Aset',
                        unit: 'Unit & Paviliun',
                        users: 'Akun Pengguna'
                    };
                    return map[this.activeModule] || 'Modul';
                },

                get searchPlaceholder() {
                    if (this.activeModule === 'mutasi') return 'Cari BAMB / nama aset / NIBAR / ruangan asal / tujuan / penghapus...';
                    if (this.activeModule === 'astap') {
                        if (this.astapSubTab === 'nibar') {
                            return 'Cari NIBAR (45-digit) / nama aset / kode 108 / ruangan / kondisi / penghapus...';
                        }
                        return 'Cari nama aset / kode 108 / NIBAR / penyedia / SPK / penghapus...';
                    }
                    if (this.activeModule === 'distribusi') return 'Cari kode distribusi / BAST / unit tujuan / tanggal / penghapus...';
                    if (this.activeModule === 'unit') return 'Cari kode unit / nama ruangan / kepala ruangan / NIP / penghapus...';
                    if (this.activeModule === 'users') return 'Cari nama pengguna / email / NIP / role / unit penugasan / penghapus...';
                    return 'Cari data terhapus...';
                },

                get currentList() {
                    if (this.activeModule === 'mutasi') return this.mutasis;
                    if (this.activeModule === 'astap') {
                        return this.astapSubTab === 'nibar' ? this.nibars : this.astaps;
                    }
                    if (this.activeModule === 'distribusi') return this.distribusis;
                    if (this.activeModule === 'unit') return this.units;
                    if (this.activeModule === 'users') return this.users;
                    return [];
                },

                get filteredItems() {
                    const q = (this.searchQuery || '').toLowerCase().trim();
                    const list = this.currentList;
                    if (!q) return list;

                    return list.filter(item => {
                        const fields = Object.values(item).map(v => typeof v === 'string' ? v.toLowerCase() : '').join(' ');
                        return fields.includes(q);
                    });
                },

                get isAllSelected() {
                    return this.filteredItems.length > 0 && this.selectedIds.length === this.filteredItems.length;
                },

                toggleSelectAll(event) {
                    if (event.target.checked) {
                        this.selectedIds = this.filteredItems.map(m => m.id);
                    } else {
                        this.selectedIds = [];
                    }
                },

                openDetail(item) {
                    this.selectedItem = item;
                    this.showDetailModal = true;
                },

                restoreSingle(module, item) {
                    if (!item) return;
                    const targetMod = module || this.currentTargetModule;
                    const bNomor = item.nibar || item.kode || item.nama || 'Data';
                    const confirmMsg = targetMod === 'nibar'
                        ? `Apakah Anda yakin ingin mengembalikan register NIBAR ${bNomor} ke paket pengadaan aset induk? Volume barang akan bertambah +1 unit.`
                        : `Apakah Anda yakin ingin mengembalikan ${bNomor} ke status aktif? Data akan kembali muncul di katalog operasional.`;

                    this.askConfirmation({
                        title: 'Konfirmasi Pulihkan Data',
                        message: confirmMsg,
                        itemName: bNomor,
                        type: 'info',
                        btnText: 'Pulihkan Data',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch(`/recycle-bin/${targetMod}/${item.id}/restore`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(d => {
                                if (d.success) {
                                    this.showSimatToast(d.message || 'Data berhasil dipulihkan!', 'success');
                                    setTimeout(() => window.location.reload(), 600);
                                } else {
                                    this.showSimatToast(d.message || 'Gagal memulihkan data.', 'error');
                                }
                            })
                            .catch(err => {
                                console.error('restore error:', err);
                                window.location.reload();
                            });
                        }
                    });
                },

                bulkRestore(module) {
                    if (this.selectedIds.length === 0) return;
                    const count = this.selectedIds.length;
                    const targetMod = module || this.currentTargetModule;
                    const entityName = targetMod === 'nibar' ? 'register NIBAR' : 'data';

                    this.askConfirmation({
                        title: 'Konfirmasi Pulihkan Massal',
                        message: `Apakah Anda yakin ingin memulihkan ${count} ${entityName} terpilih kembali ke status aktif?`,
                        itemName: `${count} Data Terpilih`,
                        type: 'info',
                        btnText: 'Pulihkan Semua Terpilih',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch(`/recycle-bin/${targetMod}/bulk-restore`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ ids: this.selectedIds })
                            })
                            .then(res => res.json())
                            .then(d => {
                                if (d.success) {
                                    this.showSimatToast(d.message || 'Data berhasil dipulihkan!', 'success');
                                    setTimeout(() => window.location.reload(), 600);
                                } else {
                                    this.showSimatToast(d.message || 'Gagal memulihkan massal.', 'error');
                                }
                            })
                            .catch(err => {
                                console.error('bulk restore error:', err);
                                window.location.reload();
                            });
                        }
                    });
                },

                bulkForceDelete(module) {
                    if (this.selectedIds.length === 0) return;
                    const count = this.selectedIds.length;
                    const targetMod = module || this.currentTargetModule;

                    // BLOKIR PENGHAPUSAN MASSAL JIKA ADA UNIT YANG MASIH MEMILIKI ASET
                    if (targetMod === 'unit') {
                        const unitsWithAssets = this.units.filter(u => this.selectedIds.includes(u.id) && Number(u.total_aset) > 0);
                        if (unitsWithAssets.length > 0) {
                            const totalAsetCount = unitsWithAssets.reduce((sum, u) => sum + Number(u.total_aset), 0);
                            const unitNames = unitsWithAssets.map(u => u.nama).slice(0, 3).join(', ') + (unitsWithAssets.length > 3 ? '...' : '');
                            this.askConfirmation({
                                title: 'Penghapusan Massal Ditolak',
                                message: `Terdapat ${unitsWithAssets.length} unit terpilih (${unitNames}) yang masih menampung total ${totalAsetCount} aset aktif di database RSUD. Unit yang memiliki aset tidak dapat dihapus.`,
                                itemName: `${unitsWithAssets.length} Unit Terpilih Masih Memiliki Aset (Total ${totalAsetCount} Aset)`,
                                type: 'danger',
                                isBlocked: true,
                                actionUrl: '/mutasi-aset',
                                actionText: 'Ajukan Mutasi Aset',
                                assetWarning: `Demi integritas data aset RSUD Koesnadi, Anda tidak dapat menghapus unit yang masih memegang inventaris barang. Silakan batalkan centang pada unit yang memiliki aset, atau pulihkan unit tersebut dan ajukan mutasi aset ke ruangan lain terlebih dahulu.`,
                                btnText: null,
                                onConfirm: null
                            });
                            return;
                        }

                        // BLOKIR PENGHAPUSAN MASSAL JIKA ADA UNIT YANG MEMILIKI RIWAYAT BAST DISTRIBUSI
                        const unitsWithBasts = this.units.filter(u => this.selectedIds.includes(u.id) && Number(u.total_bast) > 0);
                        if (unitsWithBasts.length > 0) {
                            const totalBastCount = unitsWithBasts.reduce((sum, u) => sum + Number(u.total_bast), 0);
                            const unitNames = unitsWithBasts.map(u => u.nama).slice(0, 3).join(', ') + (unitsWithBasts.length > 3 ? '...' : '');
                            this.askConfirmation({
                                title: 'Proteksi Audit: Penghapusan Massal Ditolak',
                                message: `Terdapat ${unitsWithBasts.length} unit terpilih (${unitNames}) yang memiliki total ${totalBastCount} dokumen riwayat BAST Distribusi resmi.`,
                                itemName: `${unitsWithBasts.length} Unit Terpilih Memiliki Riwayat BAST Resmi (${totalBastCount} Dokumen)`,
                                type: 'danger',
                                isBlocked: true,
                                actionUrl: '/berita-acara',
                                actionText: 'Buka Arsip BAST',
                                assetWarning: `Sesuai standar audit BPK dan Inspektorat, dokumen Berita Acara Serah Terima (BAST) adalah bukti legalitas penyerahan barang yang dilindungi undang-undang dan tidak boleh dihapus dari sistem. Unit-unit ini hanya dapat dinonaktifkan/dipulihkan, tidak boleh dimusnahkan permanen dari database.`,
                                btnText: null,
                                onConfirm: null
                            });
                            return;
                        }
                    }

                    let title = 'Konfirmasi Hapus Permanen Massal';
                    let message = `PERINGATAN: Apakah Anda yakin ingin MENGHAPUS PERMANEN ${count} data terpilih dari database? Tindakan ini TIDAK DAPAT DIBATALKAN!`;
                    let itemName = `${count} Data Terpilih`;

                    this.askConfirmation({
                        title: title,
                        message: message,
                        itemName: itemName,
                        type: 'danger',
                        isBlocked: false,
                        btnText: 'Hapus Permanen Sekarang',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch(`/recycle-bin/${targetMod}/bulk-force-delete`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ ids: this.selectedIds })
                            })
                            .then(res => res.json())
                            .then(d => {
                                if (d.success) {
                                    this.showSimatToast(d.message || 'Data terpilih berhasil dihapus permanen!', 'success');
                                    setTimeout(() => window.location.reload(), 600);
                                } else {
                                    this.showSimatToast(d.message || 'Gagal menghapus data permanen.', 'error');
                                }
                            })
                            .catch(err => {
                                console.error('bulk force delete error:', err);
                                window.location.reload();
                            });
                        }
                    });
                },

                forceDeleteSingle(module, item) {
                    if (!item) return;
                    const targetMod = module || this.currentTargetModule;

                    // JIKA RUANGAN / UNIT MEMILIKI ASET: BLOKIR PENGHAPUSAN DAN TAMPILKAN LINK AJUKAN MUTASI
                    if (targetMod === 'unit' && Number(item.total_aset) > 0) {
                        this.askConfirmation({
                            title: 'Unit Tidak Dapat Dihapus Permanen',
                            message: `Ruangan "${item.nama}" saat ini tidak dapat dihapus permanen karena masih tercatat menampung ${item.total_aset} barang inventaris/aset di database RSUD.`,
                            itemName: `${item.nama} (${item.kode || 'UNIT'}) — Memiliki ${item.total_aset} Aset Aktif`,
                            type: 'danger',
                            isBlocked: true,
                            actionUrl: '/mutasi-aset',
                            actionText: 'Ajukan Mutasi Aset',
                            assetWarning: `Sistem mendeteksi bahwa ruangan ini masih tercatat menampung ${item.total_aset} aset aktif. Demi akuntabilitas inventaris RSUD Koesnadi, unit yang memiliki aset tidak diperkenankan untuk dihapus permanen. Silakan pulihkan unit ini lalu ajukan mutasi aset ke ruangan lain terlebih dahulu sampai ruangan ini kosong (0 aset).`,
                            btnText: null,
                            onConfirm: null
                        });
                        return;
                    }

                    // JIKA RUANGAN / UNIT MEMILIKI RIWAYAT BAST DISTRIBUSI: BLOKIR PENGHAPUSAN DEMI KEPATUHAN AUDIT BPK
                    if (targetMod === 'unit' && Number(item.total_bast) > 0) {
                        this.askConfirmation({
                            title: 'Proteksi Audit: Unit Tidak Dapat Dihapus Permanen',
                            message: `Ruangan "${item.nama}" tidak dapat dihapus permanen karena memiliki ${item.total_bast} riwayat dokumen Berita Acara Serah Terima (BAST) Distribusi resmi.`,
                            itemName: `${item.nama} (${item.kode || 'UNIT'}) — Memiliki ${item.total_bast} Dokumen BAST Resmi`,
                            type: 'danger',
                            isBlocked: true,
                            actionUrl: '/berita-acara',
                            actionText: 'Buka Arsip BAST',
                            assetWarning: `Dokumen Berita Acara Serah Terima (BAST) bernomor resmi dilindungi untuk kepentingan audit berkala BPK & Inspektorat sebagai bukti sah penyerahan barang milik daerah. Unit yang pernah memiliki transaksi BAST tidak boleh dihapus permanen dari database. Silakan pulihkan unit ini ke katalog aktif jika diperlukan.`,
                            btnText: null,
                            onConfirm: null
                        });
                        return;
                    }

                    let bNomor = item.nibar || item.kode || item.nama || 'Item';
                    let title = targetMod === 'nibar' ? 'Konfirmasi Hapus Permanen NIBAR' : 'Konfirmasi Hapus Permanen';
                    let message = targetMod === 'nibar'
                        ? `TINDAKAN BERBAHAYA: Register NIBAR "${bNomor}" akan dimusnahkan secara PERMANEN dari database RSUD. Tindakan ini TIDAK DAPAT DIBATALKAN!`
                        : 'TINDAKAN BERBAHAYA: Data ini akan dihapus secara PERMANEN dari database dan seluruh relasinya akan hilang. Tindakan ini TIDAK DAPAT DIBATALKAN!';
                    let btnText = 'Hapus Permanen Sekarang';

                    this.askConfirmation({
                        title: title,
                        message: message,
                        itemName: bNomor,
                        type: 'danger',
                        isBlocked: false,
                        btnText: btnText,
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch(`/recycle-bin/${targetMod}/${item.id}/force-delete`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(d => {
                                if (d.success) {
                                    this.showSimatToast(d.message || 'Data telah dihapus permanen.', 'success');
                                    setTimeout(() => window.location.reload(), 600);
                                } else {
                                    this.showSimatToast(d.message || 'Gagal menghapus permanen.', 'error');
                                }
                            })
                            .catch(err => {
                                console.error('force delete error:', err);
                                window.location.reload();
                            });
                        }
                    });
                },

                askConfirmation(config) {
                    if (typeof window.askSimatConfirm === 'function') {
                        window.askSimatConfirm(config);
                    } else if (window.dispatchEvent) {
                        window.dispatchEvent(new CustomEvent('ask-confirm', {
                            detail: config
                        }));
                    } else if (confirm(config.message)) {
                        config.onConfirm();
                    }
                },

                showSimatToast(message, type = 'info') {
                    if (typeof window.showSimatToast === 'function') {
                        window.showSimatToast(message, type);
                    } else if (window.dispatchEvent) {
                        window.dispatchEvent(new CustomEvent('show-toast', {
                            detail: { message, type }
                        }));
                    } else {
                        alert(message);
                    }
                }
            };
        }
    </script>
</x-layout>
