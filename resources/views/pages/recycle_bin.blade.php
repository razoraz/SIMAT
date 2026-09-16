<x-layout title="Pusat Pemulihan Data - SIMAT-RK">
    @section('page-title', 'Pusat Pemulihan Data')
    @section('breadcrumb', 'Audit & Pemulihan / Pusat Data Terhapus')

    <div x-data="recycleBinApp()" x-cloak class="space-y-6">

        <!-- HEADER BANNER & KPI CARDS -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-900/90 to-red-950/40 border border-slate-800 p-6 md:p-8 shadow-2xl">
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-red-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute right-40 -bottom-10 w-48 h-48 bg-rose-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-red-500/15 border border-red-500/30 text-red-300 text-xs font-bold mb-3 shadow-sm">
                        <span>♻️ Audit Trail & Central Recycle Bin</span>
                    </div>
                    <h1 class="text-2xl md:text-3xl font-black text-white tracking-tight">Pusat Data Terhapus</h1>
                    <p class="text-slate-400 text-xs md:text-sm mt-1 max-w-2xl leading-relaxed">
                        Arsip terpusat seluruh data transaksi dan aset SIMAT yang berlabel terhapus (<code class="text-rose-300 font-mono font-bold">is_deleted = 1</code>). Anda dapat memulihkan kembali data aktif atau menghapusnya secara permanen.
                    </p>
                </div>

                <!-- KPI STATS CARDS -->
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 shrink-0">
                    <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/90 text-center shadow-inner">
                        <span class="text-[10.5px] uppercase font-bold text-slate-400 block mb-0.5">Total Terhapus</span>
                        <span class="text-2xl font-black text-white font-mono" x-text="mutasis.length"></span>
                        <span class="text-[10px] text-slate-500 block">Semua Modul</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/90 text-center shadow-inner">
                        <span class="text-[10.5px] uppercase font-bold text-amber-400 block mb-0.5">Mutasi Aset</span>
                        <span class="text-2xl font-black text-amber-300 font-mono" x-text="mutasis.length"></span>
                        <span class="text-[10px] text-slate-500 block">Data Transaksi</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/90 text-center shadow-inner col-span-2 sm:col-span-1">
                        <span class="text-[10.5px] uppercase font-bold text-rose-400 block mb-0.5">30 Hari Terakhir</span>
                        <span class="text-2xl font-black text-rose-300 font-mono">{{ $totalThisMonth }}</span>
                        <span class="text-[10px] text-slate-500 block">Aktivitas Hapus</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODULE CATEGORY TABS -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-3 shadow-xl">
            <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs">
                @foreach ($moduleStats as $modKey => $mod)
                    <button type="button" @click="activeModule = '{{ $modKey }}'; selectedIds = []"
                        :class="activeModule === '{{ $modKey }}' 
                            ? 'bg-gradient-to-r from-red-600/30 to-rose-600/30 text-white font-black border-red-500/50 shadow-lg shadow-red-500/10 ring-1 ring-red-500/40' 
                            : 'bg-slate-950/60 text-slate-400 hover:text-white hover:bg-slate-800 border-slate-800/80'"
                        class="px-4 py-2.5 rounded-2xl border text-xs transition-all flex items-center space-x-2 shrink-0 active:scale-95 cursor-pointer">
                        <span class="text-base">{{ $mod['icon'] }}</span>
                        <span>{{ $mod['name'] }}</span>
                        @if ($mod['ready'])
                            <span class="px-2 py-0.5 text-[10.5px] font-mono font-bold rounded-lg"
                                :class="activeModule === '{{ $modKey }}' ? 'bg-red-500 text-white' : 'bg-slate-800 text-slate-300'"
                                x-text="mutasis.length"></span>
                        @else
                            <span class="px-1.5 py-0.2 text-[9.5px] rounded bg-slate-800/80 text-slate-500 font-medium">Ready</span>
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

        <!-- TAB CONTENT: MUTASI ASET -->
        <template x-if="activeModule === 'mutasi'">
            <div class="space-y-4">
                <!-- TOOLBAR: SEARCH & BULK ACTIONS -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nomor BAMB / nama aset / NIBAR / ruangan asal / tujuan / penghapus..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500/50 transition-all">
                        <svg class="w-4 h-4 text-red-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2.5 shrink-0 w-full md:w-auto justify-end flex-wrap gap-y-2">
                        {{-- Tombol Bulk Restore --}}
                        <button type="button" @click="bulkRestore('mutasi')" :disabled="selectedIds.length === 0"
                            :class="selectedIds.length > 0 ? 'bg-indigo-600 hover:bg-indigo-500 text-white cursor-pointer shadow-lg shadow-indigo-600/25' : 'bg-slate-800/50 text-slate-500 cursor-not-allowed border-slate-800'"
                            class="px-4 py-2.5 rounded-2xl border border-indigo-500/40 text-xs font-bold transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                            <span>Pulihkan Terpilih (<span x-text="selectedIds.length"></span>)</span>
                        </button>

                        {{-- Tombol Kosongkan Tong Sampah (Khusus Master Admin) --}}
                        @if (Auth::user()?->isMasterAdmin())
                            <button type="button" @click="emptyTrash('mutasi')" :disabled="mutasis.length === 0"
                                class="px-4 py-2.5 rounded-2xl bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                <span>Kosongkan Tong Sampah</span>
                            </button>
                        @endif
                    </div>
                </div>

                <!-- TABLE MUTASI TERHAPUS -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
                    <div class="overflow-x-auto rounded-2xl border border-slate-800/80 bg-slate-950/40">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shadow-sm shrink-0">
                                <tr>
                                    <th class="px-4 py-3.5 text-center w-10 bg-slate-950">
                                        <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                            class="rounded bg-slate-900 border-slate-700 text-indigo-500 focus:ring-0 cursor-pointer">
                                    </th>
                                    <th class="px-4 py-3.5 text-center w-12 bg-slate-950">No</th>
                                    <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">No. BAMB</th>
                                    <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Jenis</th>
                                    <th class="px-4 py-3.5 text-left min-w-[220px] bg-slate-950">Nama Barang / NIBAR</th>
                                    <th class="px-4 py-3.5 text-center whitespace-nowrap min-w-[180px] bg-slate-950">Asal → Tujuan</th>
                                    <th class="px-4 py-3.5 text-left whitespace-nowrap min-w-[190px] bg-slate-950">Dihapus Oleh</th>
                                    <th class="px-4 py-3.5 text-center whitespace-nowrap min-w-[160px] bg-slate-950">Waktu Dihapus</th>
                                    <th class="px-4 py-3.5 text-center whitespace-nowrap border-l border-slate-800 shrink-0 min-w-[220px] w-[220px]"
                                        style="position: sticky; right: 0; z-index: 20; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                        Aksi Pemulihan
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                <template x-for="(item, index) in filteredMutasis" :key="item.id">
                                    <tr class="group hover:bg-slate-800/40 transition-colors bg-red-950/10">
                                        {{-- Checkbox --}}
                                        <td class="px-4 py-4 text-center">
                                            <input type="checkbox" :value="item.id" x-model="selectedIds"
                                                class="rounded bg-slate-900 border-slate-700 text-indigo-500 focus:ring-0 cursor-pointer">
                                        </td>

                                        {{-- No --}}
                                        <td class="px-4 py-4 text-center font-bold text-slate-500" x-text="index + 1"></td>

                                        {{-- No. BAMB --}}
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <span class="px-2.5 py-1 rounded-lg bg-red-950/60 border border-red-500/30 text-red-300 font-mono font-bold text-[11px] shadow-sm inline-block" x-text="item.kode"></span>
                                        </td>

                                        {{-- Jenis --}}
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                                <span x-text="item.jenis || 'Mutasi'"></span>
                                            </span>
                                        </td>

                                        {{-- Nama Barang / NIBAR --}}
                                        <td class="px-4 py-4 min-w-[220px]">
                                            <p class="font-bold text-white text-xs leading-snug" x-text="item.nama"></p>
                                            <p class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="item.kode_barang"></p>
                                        </td>

                                        {{-- Asal -> Tujuan --}}
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800">
                                                <span class="text-[10.5px] text-slate-300" x-text="item.asal"></span>
                                                <span class="text-slate-500">→</span>
                                                <span class="text-[10.5px] text-indigo-300 font-semibold" x-text="item.tujuan"></span>
                                            </div>
                                        </td>

                                        {{-- Dihapus Oleh --}}
                                        <td class="px-4 py-4 whitespace-nowrap">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-6 h-6 rounded-full bg-red-500/20 text-red-400 flex items-center justify-center text-[10px] font-bold">
                                                    👤
                                                </div>
                                                <div>
                                                    <p class="font-bold text-slate-200 text-[11px]" x-text="item.deleted_by"></p>
                                                    <p class="text-[9.5px] text-slate-500" x-text="'Status lalu: ' + item.status_terakhir"></p>
                                                </div>
                                            </div>
                                        </td>

                                        {{-- Waktu Dihapus --}}
                                        <td class="px-4 py-4 text-center whitespace-nowrap">
                                            <p class="font-mono text-[10.5px] text-slate-300" x-text="item.deleted_at"></p>
                                            <p class="text-[9.5px] text-red-400 font-medium mt-0.5" x-text="item.deleted_at_relative"></p>
                                        </td>

                                        {{-- Aksi Sticky Right --}}
                                        <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[220px] w-[220px]"
                                            style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                            <div class="flex items-center justify-center gap-1.5">
                                                {{-- Detail --}}
                                                <button type="button" @click="openDetail(item)" title="Lihat Detail Transaksi"
                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <span>👁️ Detail</span>
                                                </button>

                                                {{-- Pulihkan --}}
                                                <button type="button" @click="restoreSingle('mutasi', item)" title="Pulihkan Data ke Status Aktif (Label 0)"
                                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                    <span>Pulihkan</span>
                                                </button>

                                                {{-- Hapus Permanen (Khusus Admin / Master Admin) --}}
                                                <button type="button" @click="forceDeleteSingle('mutasi', item)" title="Hapus Permanen dari Database"
                                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                {{-- Empty State --}}
                                <template x-if="filteredMutasis.length === 0">
                                    <tr>
                                        <td colspan="9" class="py-14 text-center">
                                            <div class="flex flex-col items-center justify-center space-y-3">
                                                <div class="w-16 h-16 rounded-full bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">
                                                    ✨
                                                </div>
                                                <p class="text-sm font-bold text-slate-200">Tong Sampah Mutasi Kosong</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tidak ada transaksi mutasi aset yang sedang berstatus terhapus dalam sistem.</p>
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

        <!-- TAB PLACEHOLDER: MODUL LAINNYA -->
        <template x-if="activeModule !== 'mutasi'">
            <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-12 text-center shadow-xl space-y-4">
                <div class="w-20 h-20 mx-auto rounded-3xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-center text-4xl shadow-inner">
                    🚀
                </div>
                <h3 class="text-lg font-black text-white" x-text="'Pusat Pemulihan: ' + activeModuleName"></h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto leading-relaxed">
                    Modul ini telah disiapkan untuk arsitektur terpusat menggunakan <code class="text-indigo-400 font-mono font-bold">TrackableSoftDelete</code>. Saat kolom <code class="text-rose-300 font-mono font-bold">is_deleted</code> ditambahkan ke tabel terkait, data terhapus akan otomatis muncul di tab ini.
                </p>
                <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] text-slate-400 font-mono">
                    <span>STATUS: STANDARISASI MODEL SIAP DIAKTIFKAN</span>
                </div>
            </div>
        </template>

        <!-- MODAL DETAIL TRANSAKSI MUTASI TERHAPUS -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
            style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px);" @click.self="showDetailModal = false" x-cloak>
            <div class="border border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto my-auto" style="background-color: #0f172a;">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="text-red-400 font-bold text-lg">🗑️</span>
                        <h3 class="text-base font-extrabold text-white">Detail Mutasi Aset (Terhapus)</h3>
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
                                <p class="font-extrabold text-red-300 text-sm">Status Data: Terhapus (Label 1)</p>
                                <p class="text-slate-300">Dihapus oleh: <strong class="text-white" x-text="selectedItem.deleted_by"></strong></p>
                                <p class="text-slate-400 text-[11px]">Waktu Penghapusan: <span class="font-mono text-slate-200" x-text="selectedItem.deleted_at"></span></p>
                            </div>
                        </div>

                        {{-- Metadata BAMB --}}
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

                        {{-- Daftar Rincian Barang --}}
                        <div class="space-y-1.5">
                            <span class="text-slate-400 text-[10.5px] font-bold uppercase tracking-wider block">
                                Rincian Barang Terkait (<span x-text="(selectedItem.items ? selectedItem.items.length : 1) + ' Barang'"></span>):
                            </span>
                            <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner max-h-48 overflow-y-auto">
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
                                        <template x-for="(it, idx) in (selectedItem.items && selectedItem.items.length > 0 ? selectedItem.items : [{no: 1, nama_barang: selectedItem.nama, nibar: selectedItem.kode_barang, kondisi: 'Baik'}])" :key="idx">
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

                        {{-- Keterangan / Alasan Mutasi --}}
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-slate-500 block mb-1 font-bold text-[10px]">Alasan Pengajuan Sebelumnya:</span>
                            <p class="text-slate-300" x-text="selectedItem.alasan_mutasi"></p>
                        </div>
                    </div>
                </template>

                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <div class="flex items-center space-x-2">
                        <template x-if="selectedItem">
                            <button type="button" @click="restoreSingle('mutasi', selectedItem); showDetailModal = false;"
                                class="px-4 py-2 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500/30 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                                <span>♻️ Pulihkan Mutasi Ini</span>
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
                selectedIds: [],
                searchQuery: '',
                showDetailModal: false,
                selectedItem: null,

                get activeModuleName() {
                    const map = {
                        mutasi: 'Mutasi Aset',
                        astap: 'Master ASTAP',
                        distribusi: 'Distribusi Aset',
                        bast: 'Berita Acara (BAST)',
                        unit: 'Unit & Paviliun'
                    };
                    return map[this.activeModule] || 'Modul SIMAT';
                },

                get filteredMutasis() {
                    const q = (this.searchQuery || '').toLowerCase().trim();
                    if (!q) return this.mutasis;

                    return this.mutasis.filter(m => {
                        return (m.kode || '').toLowerCase().includes(q) ||
                               (m.nama || '').toLowerCase().includes(q) ||
                               (m.kode_barang || '').toLowerCase().includes(q) ||
                               (m.asal || '').toLowerCase().includes(q) ||
                               (m.tujuan || '').toLowerCase().includes(q) ||
                               (m.pemohon || '').toLowerCase().includes(q) ||
                               (m.deleted_by || '').toLowerCase().includes(q);
                    });
                },

                get isAllSelected() {
                    return this.filteredMutasis.length > 0 && this.selectedIds.length === this.filteredMutasis.length;
                },

                toggleSelectAll(event) {
                    if (event.target.checked) {
                        this.selectedIds = this.filteredMutasis.map(m => m.id);
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
                    const bNomor = item.kode || 'Item';
                    this.askConfirmation({
                        title: '♻️ Konfirmasi Pulihkan Data',
                        message: 'Apakah Anda yakin ingin mengembalikan data ini ke status aktif (label 0)?',
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
                    const bNomor = item.kode || 'Item';
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
                    this.askConfirmation({
                        title: '🚨 KOSONGKAN SELURUH TONG SAMPAH',
                        message: 'PERINGATAN MASTER ADMIN: Seluruh data yang ada di tong sampah modul ini akan dimusnahkan secara permanen dari database. Tindakan ini TIDAK DAPAT DIKEMBALIKAN!',
                        itemName: 'Semua Data Terhapus (' + this.mutasis.length + ' item)',
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
