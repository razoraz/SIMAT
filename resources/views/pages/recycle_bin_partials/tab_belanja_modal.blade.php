<!-- TAB: BELANJA MODAL (AKUN 5.2 KASDA / BLUD APBD & REGISTER NIBAR) -->
<template x-if="activeModule === 'belanja_modal'">
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        <!-- SUB-TAB SWITCHER (Paket Pengadaan vs Unit Fisik NIBAR) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-5 mb-5 border-b border-slate-800/80 gap-3">
            <div class="flex items-center space-x-1.5 p-1 bg-slate-950/80 rounded-2xl border border-slate-800 shrink-0">
                <button type="button" @click="changeBelanjaModalSubTab('packet')"
                    :style="belanjaModalSubTab === 'packet' ? 'border-color: #10b981; box-shadow: 0 0 14px rgba(16, 185, 129, 0.35);' : ''"
                    :class="belanjaModalSubTab === 'packet' 
                        ? 'bg-emerald-500/15 border border-emerald-500 text-emerald-300 shadow-lg' 
                        : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer whitespace-nowrap shrink-0">
                    <span>🏛️ Paket Pengadaan (Akun 5.2)</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black transition-colors shrink-0"
                        :class="belanjaModalSubTab === 'packet' ? 'bg-emerald-500 text-slate-950 shadow-sm' : 'bg-slate-800 text-slate-400'"
                        x-text="belanjaModals.length">0</span>
                </button>
                <button type="button" @click="changeBelanjaModalSubTab('nibar')"
                    :style="belanjaModalSubTab === 'nibar' ? 'border-color: #06b6d4; box-shadow: 0 0 14px rgba(6, 182, 212, 0.35);' : ''"
                    :class="belanjaModalSubTab === 'nibar' 
                        ? 'bg-cyan-500/15 border border-cyan-500 text-cyan-300 shadow-lg' 
                        : 'border border-transparent text-slate-400 hover:text-slate-200 hover:bg-slate-900/60'"
                    class="px-4 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-2 cursor-pointer whitespace-nowrap shrink-0">
                    <span>🏷️ Unit Fisik NIBAR</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-mono font-black transition-colors shrink-0"
                        :class="belanjaModalSubTab === 'nibar' ? 'bg-cyan-500 text-slate-950 shadow-sm' : 'bg-slate-800 text-slate-400'"
                        x-text="belanjaModalNibars.length">0</span>
                </button>
            </div>
            <div class="text-xs text-slate-400 flex items-center gap-1.5 min-w-0">
                <template x-if="belanjaModalSubTab === 'packet'">
                    <span>🏛️ Paket Belanja Modal APBD/BLUD (Akun 5.2) terhapus beserta rincian kontrak & NIBAR.</span>
                </template>
                <template x-if="belanjaModalSubTab === 'nibar'">
                    <span>🏷️ Register unit fisik barang (NIBAR) yang dihapus satuan dari paket Belanja Modal aktif.</span>
                </template>
            </div>
        </div>

        <!-- SUB-TAB 1: PAKET PENGADAAN BELANJA MODAL -->
        <template x-if="belanjaModalSubTab === 'packet'">
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
                                <th class="px-4 py-3.5">Kode 108 & Akun 5.2</th>
                                <th class="px-4 py-3.5">Volume & Satuan</th>
                                <th class="px-4 py-3.5">Realisasi & SP2D</th>
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
                                        <div class="flex items-center gap-1.5 mb-0.5">
                                            <span class="font-bold text-white block text-sm" x-text="item.nama"></span>
                                        </div>
                                        <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                            <span class="text-[10px] text-emerald-400 font-mono font-bold flex items-center gap-1">
                                                <span>🏷️</span>
                                                <span x-text="item.item_count + ' Register NIBAR'"></span>
                                            </span>
                                            <span class="px-1.5 py-0.5 rounded text-[9.5px] font-bold bg-slate-800/80 text-slate-300 border border-slate-700/60" x-text="item.category"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-mono text-cyan-300 font-bold block text-xs" x-text="item.kode"></span>
                                        <div class="mt-1">
                                            <span class="px-2 py-0.5 rounded text-[10px] font-mono font-semibold bg-emerald-950/60 text-emerald-300 border border-emerald-800/40 block truncate max-w-[200px]"
                                                  :title="item.rekening"
                                                  x-text="item.rekening_kode !== '-' ? item.rekening_kode : 'Akun 5.2 Kasda'"></span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="font-bold text-slate-200 block" x-text="item.volume"></span>
                                        <span class="text-[10px] text-slate-400 font-mono block mt-0.5" x-text="'Harga: ' + item.harga_satuan"></span>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-amber-300 font-bold block font-mono text-sm" x-text="item.total_realisasi"></span>
                                        <div class="text-[10px] text-slate-400 mt-0.5 space-y-0.5">
                                            <span class="block" x-text="'Tahun: ' + item.tahun"></span>
                                            <template x-if="item.sp2d_nomor && item.sp2d_nomor !== '-'">
                                                <span class="font-mono text-emerald-400/90 block truncate max-w-[170px]" :title="'SP2D: ' + item.sp2d_nomor" x-text="'SP2D: ' + item.sp2d_nomor"></span>
                                            </template>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <span class="text-slate-200 font-semibold block text-xs truncate max-w-[180px]" :title="item.penyedia" x-text="item.penyedia"></span>
                                        <div class="text-[10px] font-mono text-slate-400 mt-0.5 space-y-0.5">
                                            <span class="block truncate max-w-[180px]" :title="'SPK: ' + item.spk_nomor" x-text="'SPK: ' + item.spk_nomor"></span>
                                            <template x-if="item.faktur_nomor && item.faktur_nomor !== '-'">
                                                <span class="block text-slate-500 truncate max-w-[180px]" :title="'Faktur: ' + item.faktur_nomor" x-text="'Fkt: ' + item.faktur_nomor"></span>
                                            </template>
                                        </div>
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
                                            <button type="button" @click="openDetail(item)" title="Lihat Dokumen Kontrak & Register NIBAR"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 border border-slate-700 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <span>👁️ Detail</span>
                                            </button>
                                            <button type="button" @click="restoreSingle('belanja_modal', item)" title="Pulihkan Paket Pengadaan Belanja Modal ke Katalog Aktif"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Pulihkan</span>
                                            </button>
                                            <button type="button" @click="forceDeleteSingle('belanja_modal', item)" title="Hapus Permanen Paket dari Database"
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
                                            <div class="w-14 h-14 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-center text-2xl text-emerald-400 shadow-inner">🏛️</div>
                                            <template x-if="currentList.length === 0">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-200">Tong Sampah Paket Belanja Modal Kosong</p>
                                                    <p class="text-xs text-slate-500 max-w-sm mt-1">Tidak ada paket pengadaan belanja modal APBD/BLUD yang berstatus terhapus.</p>
                                                </div>
                                            </template>
                                            <template x-if="currentList.length > 0">
                                                <div class="space-y-2">
                                                    <p class="text-sm font-bold text-slate-200">Tidak Ada Data yang Cocok</p>
                                                    <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan paket Belanja Modal dengan kata kunci atau filter waktu yang dipilih.</p>
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

        <!-- SUB-TAB 2: UNIT FISIK NIBAR BELANJA MODAL -->
        <template x-if="belanjaModalSubTab === 'nibar'">
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
                                <th class="px-4 py-3.5">SPK Belanja Modal</th>
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
                                            <button type="button" @click="restoreSingle('belanja_modal_nibar', item)" title="Pulihkan NIBAR ke Paket Belanja Modal Aktif"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Pulihkan</span>
                                            </button>
                                            <button type="button" @click="forceDeleteSingle('belanja_modal_nibar', item)" title="Hapus Permanen NIBAR dari Database"
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
                                            <template x-if="currentList.length === 0">
                                                <div>
                                                    <p class="text-sm font-bold text-slate-200">Tong Sampah Register NIBAR Belanja Modal Kosong</p>
                                                    <p class="text-xs text-slate-500 max-w-sm mt-1">Tidak ada register NIBAR belanja modal individual yang berstatus terhapus.</p>
                                                </div>
                                            </template>
                                            <template x-if="currentList.length > 0">
                                                <div class="space-y-2">
                                                    <p class="text-sm font-bold text-slate-200">Tidak Ada Data yang Cocok</p>
                                                    <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan register NIBAR dengan kata kunci atau filter waktu yang dipilih.</p>
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
    </div>
</template>
