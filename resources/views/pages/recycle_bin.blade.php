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
                            Arsip terpusat seluruh data transaksi dan inventaris aset SIMAT yang berlabel terhapus (<code class="text-rose-300 font-mono font-bold">is_deleted = 1</code>). Anda dapat meninjau jejak audit penghapus, memulihkan data aktif, atau memusnahkannya secara permanen.
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

                <!-- BARIS BAWAH: KARTU INDIKATOR SELURUH 5 MODUL (MUTASI, ASTAP, DISTRIBUSI, BAST, UNIT) -->
                <div class="pt-5 border-t border-slate-800/80">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-400"></span>
                            Rincian Data Terhapus Per Modul SIMAT:
                        </span>
                        <span class="text-[10.5px] text-slate-500 hidden sm:inline">Klik kartu untuk beralih tampilan modul</span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                        <!-- 1. Mutasi Aset -->
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

                        <!-- 2. Master ASTAP -->
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
                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="getModuleCount('astap') > 0 ? getModuleCount('astap') + ' data terhapus' : 'Tidak ada data'"></span>
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

                        <!-- 4. Berita Acara (BAST) -->
                        <div @click="changeTab('bast')" 
                            class="cursor-pointer p-3.5 rounded-2xl border transition-all hover:scale-[1.02] relative group"
                            :class="activeModule === 'bast' 
                                ? 'bg-gradient-to-br from-purple-500/20 to-purple-950/30 border-purple-500/60 ring-2 ring-purple-500/30 shadow-lg shadow-purple-500/10' 
                                : 'bg-slate-950/60 border-slate-800/90 hover:border-slate-700 hover:bg-slate-900/80'">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-base p-1.5 rounded-xl bg-purple-500/15 border border-purple-500/30 text-purple-300">📜</span>
                                <span class="text-xs font-mono font-black px-2 py-0.5 rounded-lg transition-colors"
                                    :class="getModuleCount('bast') > 0 
                                        ? 'bg-purple-500 text-white font-black shadow-sm' 
                                        : 'bg-slate-800/80 text-slate-400'"
                                    x-text="getModuleCount('bast')">0</span>
                            </div>
                            <span class="text-xs font-bold text-white block truncate group-hover:text-purple-300 transition-colors">Berita Acara (BAST)</span>
                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="getModuleCount('bast') > 0 ? getModuleCount('bast') + ' data terhapus' : 'Tidak ada data'"></span>
                        </div>

                        <!-- 5. Unit & Paviliun -->
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
                    </div>
                </div>
            </div>
        </div>

        <!-- MODULE CATEGORY TABS -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-3 shadow-xl">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                @foreach ($moduleStats as $modKey => $mod)
                    <button type="button" @click="changeTab('{{ $modKey }}')"
                        :class="activeModule === '{{ $modKey }}' 
                            ? 'bg-gradient-to-r from-red-600/30 to-rose-600/30 text-white font-black border-red-500/50 shadow-lg shadow-red-500/10 ring-1 ring-red-500/40' 
                            : 'bg-slate-950/60 text-slate-400 hover:text-white hover:bg-slate-800 border-slate-800/80'"
                        class="px-4 py-2.5 rounded-2xl border text-xs transition-all flex items-center space-x-2 shrink-0 active:scale-95 cursor-pointer">
                        <span class="text-base">{{ $mod['icon'] }}</span>
                        <span>{{ $mod['name'] }}</span>
                        <span class="px-2 py-0.5 text-[10.5px] font-mono font-bold rounded-lg"
                            :class="activeModule === '{{ $modKey }}' ? 'bg-red-500 text-white' : 'bg-slate-800 text-slate-300'"
                            x-text="getModuleCount('{{ $modKey }}')"></span>
                    </button>
                @endforeach
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
                <button type="button" @click="bulkRestore(activeModule)" :disabled="selectedIds.length === 0"
                    :class="selectedIds.length > 0 ? 'bg-indigo-600 hover:bg-indigo-500 text-white cursor-pointer shadow-lg shadow-indigo-600/25' : 'bg-slate-800/50 text-slate-500 cursor-not-allowed border-slate-800'"
                    class="px-4 py-2.5 rounded-2xl border border-indigo-500/40 text-xs font-bold transition-all flex items-center space-x-1.5 active:scale-95">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Pulihkan Terpilih (<span x-text="selectedIds.length"></span>)</span>
                </button>

                {{-- Tombol Kosongkan Tong Sampah (Khusus Master Admin) --}}
                @if (Auth::user()?->isMasterAdmin())
                    <button type="button" @click="emptyTrash(activeModule)" :disabled="currentList.length === 0"
                        class="px-4 py-2.5 rounded-2xl bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        <span>Kosongkan Tong Sampah</span>
                    </button>
                @endif
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
        <!-- TAB 2: MASTER ASTAP TABLE -->
        <!-- ========================================================================= -->
        <template x-if="activeModule === 'astap'">
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
        <!-- TAB 4: BERITA ACARA (BAST) TABLE -->
        <!-- ========================================================================= -->
        <template x-if="activeModule === 'bast'">
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
                                    <th class="px-4 py-3.5">Dokumen Triwulan</th>
                                    <th class="px-4 py-3.5">Nomor Surat BAST</th>
                                    <th class="px-4 py-3.5">Tanggal BAST</th>
                                    <th class="px-4 py-3.5">Pihak 1 (PPK)</th>
                                    <th class="px-4 py-3.5">Pihak 2 (Pengurus)</th>
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
                                            <span class="font-extrabold text-blue-300 block text-sm" x-text="item.triwulan + ' Tahun ' + item.tahun"></span>
                                            <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-400" x-text="item.status"></span>
                                        </td>
                                        <td class="px-4 py-4 font-mono font-bold text-white" x-text="item.kode"></td>
                                        <td class="px-4 py-4 text-slate-300" x-text="item.tanggal_bast"></td>
                                        <td class="px-4 py-4 text-slate-300" x-text="item.pihak1_nama"></td>
                                        <td class="px-4 py-4 text-slate-300" x-text="item.pihak2_nama"></td>
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
                                                <button type="button" @click="openDetail(item)" title="Lihat Detail BAST"
                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <span>👁️ Detail</span>
                                                </button>
                                                <button type="button" @click="restoreSingle('bast', item)" title="Pulihkan Data ke Status Aktif (Label 0)"
                                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span>Pulihkan</span>
                                                </button>
                                                <button type="button" @click="forceDeleteSingle('bast', item)" title="Hapus Permanen dari Database"
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
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">📜</div>
                                                <p class="text-sm font-bold text-slate-200">Tong Sampah BAST Kosong</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tidak ada dokumen BAST triwulan yang berstatus terhapus.</p>
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
                                        <td colspan="9" class="py-14 text-center">
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
                                <p class="font-extrabold text-red-300 text-sm">Status Data: Terhapus (Label is_deleted = 1)</p>
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

                        <!-- 2. DETAIL KHUSUS MASTER ASTAP -->
                        <template x-if="activeModule === 'astap'">
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

                        <!-- 4. DETAIL KHUSUS BAST -->
                        <template x-if="activeModule === 'bast'">
                            <div class="grid grid-cols-2 gap-3 bg-slate-950 p-4 rounded-2xl border border-slate-800">
                                <div>
                                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Dokumen BAST:</span>
                                    <span class="font-bold text-white text-sm" x-text="selectedItem.triwulan + ' Tahun ' + selectedItem.tahun"></span>
                                    <span class="text-xs text-cyan-400 font-mono block mt-1" x-text="'Nomor: ' + selectedItem.kode"></span>
                                </div>
                                <div class="text-right">
                                    <span class="text-slate-500 text-[10px] uppercase font-bold block">Status Tanda Tangan:</span>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold"
                                        :class="selectedItem.signed ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'"
                                        x-text="selectedItem.status"></span>
                                    <span class="text-[11px] text-slate-400 block mt-1" x-text="'Tgl BAST: ' + selectedItem.tanggal_bast"></span>
                                </div>
                                <div class="mt-2">
                                    <span class="text-slate-500 text-[10px] block">Pihak 1 (PPK):</span>
                                    <span class="font-semibold text-slate-200" x-text="selectedItem.pihak1_nama"></span>
                                </div>
                                <div class="mt-2 text-right">
                                    <span class="text-slate-500 text-[10px] block">Pihak 2 (Pengurus Barang):</span>
                                    <span class="font-semibold text-slate-200" x-text="selectedItem.pihak2_nama"></span>
                                </div>
                            </div>
                        </template>

                        <!-- 5. DETAIL KHUSUS UNIT -->
                        <template x-if="activeModule === 'unit'">
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
                        </template>

                    </div>
                </template>

                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <div class="flex items-center space-x-2">
                        <template x-if="selectedItem">
                            <button type="button" @click="restoreSingle(activeModule, selectedItem); showDetailModal = false;"
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
                distribusis: {{ Js::from($deletedDistribusis) }},
                basts: {{ Js::from($deletedBasts) }},
                units: {{ Js::from($deletedUnits) }},

                selectedIds: [],
                searchQuery: '',
                showDetailModal: false,
                selectedItem: null,

                changeTab(tab) {
                    this.activeModule = tab;
                    this.selectedIds = [];
                    this.searchQuery = '';
                },

                get totalCount() {
                    return this.mutasis.length + this.astaps.length + this.distribusis.length + this.basts.length + this.units.length;
                },

                getModuleCount(mod) {
                    if (mod === 'mutasi') return this.mutasis.length;
                    if (mod === 'astap') return this.astaps.length;
                    if (mod === 'distribusi') return this.distribusis.length;
                    if (mod === 'bast') return this.basts.length;
                    if (mod === 'unit') return this.units.length;
                    return 0;
                },

                get activeModuleName() {
                    const map = {
                        mutasi: 'Mutasi Aset',
                        astap: 'Master ASTAP',
                        distribusi: 'Distribusi Aset',
                        bast: 'Berita Acara (BAST)',
                        unit: 'Unit & Paviliun'
                    };
                    return map[this.activeModule] || 'Modul';
                },

                get searchPlaceholder() {
                    const map = {
                        mutasi: 'Cari BAMB / nama aset / NIBAR / ruangan asal / tujuan / penghapus...',
                        astap: 'Cari nama aset / kode 108 / NIBAR / penyedia / SPK / penghapus...',
                        distribusi: 'Cari kode distribusi / BAST / unit tujuan / tanggal / penghapus...',
                        bast: 'Cari triwulan / nomor surat / PPK / pengurus / penghapus...',
                        unit: 'Cari kode unit / nama ruangan / kepala ruangan / NIP / penghapus...'
                    };
                    return map[this.activeModule] || 'Cari data terhapus...';
                },

                get currentList() {
                    if (this.activeModule === 'mutasi') return this.mutasis;
                    if (this.activeModule === 'astap') return this.astaps;
                    if (this.activeModule === 'distribusi') return this.distribusis;
                    if (this.activeModule === 'bast') return this.basts;
                    if (this.activeModule === 'unit') return this.units;
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
                    const bNomor = item.kode || item.nama || 'Data';
                    this.askConfirmation({
                        title: '♻️ Konfirmasi Pulihkan Data',
                        message: `Apakah Anda yakin ingin mengembalikan ${bNomor} ke status aktif (label 0)? Data akan kembali muncul di katalog operasional.`,
                        itemName: bNomor,
                        type: 'info',
                        btnText: '♻️ Ya, Pulihkan Data',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch(`/recycle-bin/${module}/${item.id}/restore`, {
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
                    this.askConfirmation({
                        title: '♻️ Konfirmasi Pulihkan Massal',
                        message: `Apakah Anda yakin ingin memulihkan ${count} data terpilih kembali ke status aktif?`,
                        itemName: `${count} Data Terpilih`,
                        type: 'info',
                        btnText: '♻️ Pulihkan Semua Terpilih',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch(`/recycle-bin/${module}/bulk-restore`, {
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

                forceDeleteSingle(module, item) {
                    if (!item) return;
                    const bNomor = item.kode || item.nama || 'Item';
                    this.askConfirmation({
                        title: '🚨 Konfirmasi HAPUS PERMANEN',
                        message: 'TINDAKAN BERBAHAYA: Data ini akan dihapus secara PERMANEN dari database dan seluruh relasinya akan hilang. Tindakan ini TIDAK DAPAT DIBATALKAN!',
                        itemName: bNomor,
                        type: 'danger',
                        btnText: '💥 Ya, Hapus Permanen Sekarang',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch(`/recycle-bin/${module}/${item.id}/force-delete`, {
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

                emptyTrash(module) {
                    const count = this.currentList.length;
                    this.askConfirmation({
                        title: '🚨 KOSONGKAN SELURUH TONG SAMPAH',
                        message: `PERINGATAN MASTER ADMIN: Seluruh data yang ada di tong sampah modul ${this.activeModuleName} (${count} data) akan dimusnahkan secara permanen dari database. Tindakan ini TIDAK DAPAT DIKEMBALIKAN!`,
                        itemName: `Semua Data Terhapus (${count} item)`,
                        type: 'danger',
                        btnText: '💥 Kosongkan Seluruhnya',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch(`/recycle-bin/${module}/empty-trash`, {
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
                                    this.showSimatToast(d.message || 'Tong sampah berhasil dikosongkan.', 'success');
                                    setTimeout(() => window.location.reload(), 600);
                                } else {
                                    this.showSimatToast(d.message || 'Gagal mengosongkan tong sampah.', 'error');
                                }
                            })
                            .catch(err => {
                                console.error('empty trash error:', err);
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
