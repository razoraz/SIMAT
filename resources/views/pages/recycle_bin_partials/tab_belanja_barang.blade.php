{{-- ========================================================================= --}}
{{-- TAB 7: BELANJA BARANG (AKUN 5.1.02 / PERBEKALAN RUANGAN) & NIBAR          --}}
{{-- ========================================================================= --}}
<template x-if="activeModule === 'belanja_barang'">
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        
        <!-- LEVEL 1: PRIMARY SUB-TAB SWITCHER (Faktur Belanja Barang vs Unit Fisik NIBAR) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-4 mb-4 border-b border-slate-800/80 gap-3">
            <div class="flex items-center space-x-1.5 p-1 bg-slate-950/80 rounded-2xl border border-slate-800 shrink-0">
                <!-- Tab 1: Faktur Belanja Barang -->
                <button type="button" @click="changeBelanjaBarangMainTab('invoice')"
                    :style="belanjaBarangMainTab === 'invoice' ? 'border-color: #14b8a6; box-shadow: 0 0 14px rgba(20, 184, 166, 0.35);' : ''"
                    :class="belanjaBarangMainTab === 'invoice' 
                        ? 'bg-teal-500/15 border border-teal-500 text-teal-300 shadow-lg' 
                        : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer whitespace-nowrap shrink-0">
                    <span>🛒 Faktur Belanja Barang</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black transition-colors shrink-0"
                        :class="belanjaBarangs.length > 0 ? 'bg-teal-500 text-slate-950 shadow-sm' : 'bg-slate-800 text-slate-400'"
                        x-text="belanjaBarangs.length">0</span>
                </button>

                <!-- Tab 2: Unit Fisik NIBAR -->
                <button type="button" @click="changeBelanjaBarangMainTab('nibar')"
                    :style="belanjaBarangMainTab === 'nibar' ? 'border-color: #10b981; box-shadow: 0 0 14px rgba(16, 185, 129, 0.35);' : ''"
                    :class="belanjaBarangMainTab === 'nibar' 
                        ? 'bg-emerald-500/15 border border-emerald-500 text-emerald-300 shadow-lg' 
                        : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer whitespace-nowrap shrink-0">
                    <span>🏷️ Unit Fisik NIBAR</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black transition-colors shrink-0"
                        :class="belanjaBarangNibars.length > 0 ? 'bg-emerald-500 text-slate-950 shadow-sm' : 'bg-slate-800 text-slate-400'"
                        x-text="belanjaBarangNibars.length">0</span>
                </button>
            </div>

            <div class="text-xs text-slate-400 flex items-center gap-1.5 min-w-0">
                <template x-if="belanjaBarangMainTab === 'invoice'">
                    <span>🛒 Berkas faktur belanja barang perbekalan ruangan (Akun 5.1.02) terhapus.</span>
                </template>
                <template x-if="belanjaBarangMainTab === 'nibar'">
                    <span>🏷️ Register unit fisik barang (NIBAR) yang dihapus satuan dari belanja barang aktif.</span>
                </template>
            </div>
        </div>

        <!-- SUB-KONTEN 1: TABLE FAKTUR BELANJA BARANG -->
        <template x-if="belanjaBarangMainTab === 'invoice'">
            <div class="rounded-2xl border border-slate-800/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3.5 w-10 text-center">
                                    <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                        class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-3.5">Nomor &amp; Tanggal Faktur</th>
                                <th class="px-4 py-3.5">Toko / Rekanan Penyedia</th>
                                <th class="px-4 py-3.5">Nama Barang &amp; Kode 108</th>
                                <th class="px-4 py-3.5">Total Pembelian</th>
                                <th class="px-4 py-3.5">Ruangan Pemegang</th>
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
                                        <span class="font-mono font-bold text-teal-300 block" x-text="item.nomor_faktur"></span>
                                        <span class="text-[10px] text-slate-400" x-text="'Tanggal: ' + item.tanggal_faktur"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-white block" x-text="item.toko_penyedia"></span>
                                        <span class="text-[10px] text-slate-400" x-text="'Tahun: ' + item.tahun + ' (' + item.triwulan + ')'"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-slate-200 block" x-text="item.nama_barang"></span>
                                        <span class="text-[10px] font-mono text-cyan-400/80" x-text="'Kode 108: ' + item.kode_barang"></span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="font-mono font-bold text-emerald-400 block" x-text="item.total_pembelian_rp"></span>
                                        <span class="text-[10px] text-teal-300 font-mono font-semibold" x-text="item.volume"></span>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <span class="text-indigo-300 font-semibold block" x-text="item.ruangan"></span>
                                        <span class="text-[10px] text-slate-500 block">KIR Ruangan RSUD</span>
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
                                            <button type="button" @click="openDetail(item)" title="Lihat Detail Belanja Barang"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <span>👁️ Detail</span>
                                            </button>
                                            <button type="button" @click="restoreSingle('belanja_barang', item)" title="Pulihkan Belanja Barang ke Daftar Aktif"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Pulihkan</span>
                                            </button>
                                            <button type="button" @click="forceDeleteSingle('belanja_barang', item)" title="Hapus Permanen dari Database"
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
                                        <template x-if="currentList.length === 0">
                                            <div class="flex flex-col items-center justify-center space-y-2">
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">🛒</div>
                                                <p class="text-sm font-bold text-slate-200">Tong Sampah Belanja Barang Kosong</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tidak ada transaksi belanja barang atau perbekalan ruangan yang berstatus terhapus.</p>
                                            </div>
                                        </template>
                                        <template x-if="currentList.length > 0">
                                            <div class="flex flex-col items-center justify-center space-y-3">
                                                <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-amber-400 shadow-inner">🔍</div>
                                                <div>
                                                    <p class="text-sm font-bold text-slate-200">Tidak Ada Belanja Barang yang Cocok</p>
                                                    <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan faktur atau barang terhapus yang sesuai dengan pencarian atau filter waktu.</p>
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

        <!-- SUB-KONTEN 2: TABLE REGISTER NIBAR BELANJA BARANG -->
        <template x-if="belanjaBarangMainTab === 'nibar'">
            <div class="rounded-2xl border border-slate-800/80 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead class="bg-slate-950/80 text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800">
                            <tr>
                                <th class="px-4 py-3.5 w-10 text-center">
                                    <input type="checkbox" @change="toggleSelectAll($event)" :checked="isAllSelected"
                                        class="rounded border-slate-700 bg-slate-900 text-red-600 focus:ring-red-500 cursor-pointer">
                                </th>
                                <th class="px-4 py-3.5">NIBAR &amp; Register</th>
                                <th class="px-4 py-3.5">Nama Barang / Aset</th>
                                <th class="px-4 py-3.5">Faktur &amp; Penyedia</th>
                                <th class="px-4 py-3.5">Ruang &amp; Tahun</th>
                                <th class="px-4 py-3.5">Kondisi &amp; Status</th>
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
                                        <span class="font-mono font-bold text-teal-300 block" x-text="item.nomor_faktur"></span>
                                        <span class="text-[10px] text-slate-400 block truncate max-w-[170px]" :title="item.toko_penyedia" x-text="item.toko_penyedia"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-semibold text-slate-200 block truncate max-w-[170px]" :title="item.ruang" x-text="item.ruang"></span>
                                        <span class="text-[10px] text-slate-400" x-text="'Tahun: ' + item.tahun"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border inline-block"
                                            :class="item.kondisi === 'Baik' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : (item.kondisi === 'Rusak Berat' ? 'bg-rose-500/15 text-rose-300 border-rose-500/30' : 'bg-amber-500/15 text-amber-300 border-amber-500/30')"
                                            x-text="item.kondisi"></span>
                                        <span class="text-[10px] text-slate-400 block mt-1" x-text="'Status: ' + item.status"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-red-300 block text-xs" x-text="item.deleted_by"></span>
                                        <span class="text-[10px] text-slate-500 font-mono">Label: 1 (Soft Delete)</span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-mono text-slate-200 block text-[11px]" x-text="item.deleted_at"></span>
                                        <span class="text-[10px] text-slate-400" x-text="item.deleted_at_relative"></span>
                                    </td>
                                    <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[220px] w-[220px]"
                                        style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openDetail(item)" title="Lihat Detail NIBAR"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <span>👁️ Detail</span>
                                            </button>
                                            <button type="button" @click="restoreSingle('belanja_barang_nibar', item)" title="Pulihkan NIBAR ke Belanja Barang Aktif"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Pulihkan</span>
                                            </button>
                                            <button type="button" @click="forceDeleteSingle('belanja_barang_nibar', item)" title="Hapus Permanen NIBAR dari Database"
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
                                            <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">🏷️</div>
                                            <template x-if="currentList.length === 0">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-200">Tong Sampah Register NIBAR Belanja Barang Kosong</p>
                                                    <p class="text-xs text-slate-500 max-w-sm mt-1">Tidak ada register NIBAR belanja barang individual yang berstatus terhapus.</p>
                                                </div>
                                            </template>
                                            <template x-if="currentList.length > 0">
                                                <div class="space-y-2">
                                                    <p class="text-sm font-bold text-slate-200">Tidak Ada Data yang Cocok</p>
                                                    <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan register NIBAR belanja barang dengan kata kunci atau filter waktu yang dipilih.</p>
                                                    <button type="button" @click="searchQuery = ''; setTimeFilter('all');"
                                                        class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-slate-300 font-bold border border-slate-700 transition-all inline-flex items-center gap-1 cursor-pointer">
                                                        <span>🔄 Reset Filter &amp; Pencarian</span>
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

    </div>
</template>
