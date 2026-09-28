<!-- TAB 4: UNIT & PAVILIUN TABLE -->
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
                                        <template x-if="currentList.length === 0">
                                            <div>
                                                <p class="text-sm font-bold text-slate-200">Tong Sampah Unit & Paviliun Kosong</p>
                                                <p class="text-xs text-slate-500 max-w-sm mt-1">Tidak ada data unit atau ruangan yang berstatus terhapus.</p>
                                            </div>
                                        </template>
                                        <template x-if="currentList.length > 0">
                                            <div class="space-y-2">
                                                <p class="text-sm font-bold text-slate-200">Tidak Ada Data yang Cocok</p>
                                                <p class="text-xs text-slate-500 max-w-sm">Tidak ditemukan unit/ruangan dengan kata kunci atau filter waktu yang dipilih.</p>
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
