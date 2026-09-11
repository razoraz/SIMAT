        <!-- ========================================================================= -->
        <!-- KONTEN TAB 2: BAST DISTRIBUSI BARANG KE UNIT & PAVILIUN (SUB ADMIN)       -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'distribusi'" class="space-y-6" x-cloak>
            
            <!-- Toolbar & Filter Distribusi -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-80">
                            <input type="text" x-model="distribusiSearch" placeholder="Cari nomor BAST / unit / PJ..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">
                            <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Filter Status TTD -->
                        <select x-model="distribusiStatusFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-teal-300 font-bold focus:outline-none focus:border-teal-500">
                            <option value="all">🔍 Semua Status TTD</option>
                            <option value="signed">✍️ Telah Ditandatangani BSrE</option>
                            <option value="unsigned">⏳ Belum Ditandatangani</option>
                        </select>

                        <!-- Filter Unit Dropdown -->
                        <select x-model="distribusiUnitFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:border-teal-500">
                            <option value="all">Semua Unit / Paviliun (Sub-Admin)</option>
                            <template x-for="u in unitsList" :key="u.id">
                                <option :value="u.nama" x-text="u.nama"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar BAST Distribusi ke Unit / Ruangan -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
                <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 340px; overflow-y: auto; overflow-x: auto;">
                    <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                        <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5 text-left">Nomor BAST Distribusi</th>
                            <th class="px-4 py-3.5 text-left">Unit / Paviliun (Penerima)</th>
                            <th class="px-4 py-3.5 text-left">Kepala Ruangan / PJ (Sub Admin)</th>
                            <th class="px-4 py-3.5 text-center">Jumlah Diajukan</th>
                            <th class="px-4 py-3.5 text-center">Jumlah Diterima</th>
                            <th class="px-4 py-3.5 text-center">Status TTD BSrE</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi BAST</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredDistribusiList" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                                <td class="px-4 py-4">
                                    <div class="font-mono font-bold text-teal-400" x-text="item.nomor_bast"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.tgl_bast"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-white" x-text="item.unit_nama"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.unit_tipe"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-emerald-400" x-text="item.pj_nama"></div>
                                    <div class="text-[10px] text-slate-400 font-mono" x-text="'NIP. ' + item.pj_nip"></div>
                                </td>
                                <td class="px-4 py-4 text-center font-mono text-slate-400 font-semibold" x-text="(item.items && item.items.length > 0 ? item.items.reduce((s, i) => s + (parseInt(i.qty, 10) || 0), 0) : (item.volume || 0)) + ' Unit'"></td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-lg bg-emerald-500/15 border border-emerald-500/30 text-emerald-300 font-mono font-bold text-xs inline-flex items-center space-x-1">
                                        <span x-text="(item.items && item.items.length > 0 ? item.items.reduce((s, i) => s + ((i.qty_acc !== null && i.qty_acc !== undefined && i.qty_acc !== '') ? (parseInt(i.qty_acc, 10) || 0) : (i.vol_bast !== undefined ? parseInt(i.vol_bast, 10) : (parseInt(i.qty, 10) || 0))), 0) : (item.volume_acc || item.volume || 0)) + ' Unit'"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap border shadow-sm"
                                          :class="item.signed ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/15 text-amber-300 border-amber-500/30'">
                                        <template x-if="item.signed">
                                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </template>
                                        <template x-if="!item.signed">
                                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        </template>
                                        <span x-text="item.signed ? 'Sudah TTD BSrE' : 'Belum TTD'"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                    
                                    <!-- 1. Tombol Toggle TTD / Batalkan TTD -->
                                    <button type="button" @click="toggleSignDistribusi(item)"
                                        :class="item.signed ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-sm'"
                                        class="px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all inline-flex items-center space-x-1 active:scale-95"
                                        :title="item.signed ? 'Batalkan Tanda Tangan Digital BSrE' : 'Tanda Tangan Digital BSrE'">
                                        <span x-text="item.signed ? '↩️ Batal TTD' : '✍️ TTD BSrE'"></span>
                                    </button>

                                    <!-- 2. Tombol Rincian Modal -->
                                    <button type="button" @click="openDetailDistribusi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-teal-500/15 hover:bg-teal-500/25 text-teal-300 border border-teal-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>👁️ Rincian</span>
                                    </button>

                                    <!-- 3. Tombol Cetak (dengan Live Edit Panel) -->
                                    <button type="button" @click="openPrintDistribusi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>🖨️ Cetak / Edit</span>
                                    </button>

                                </td>
                            </tr>
                        </template>

                        <!-- Empty State Jika Belum Ada Data Distribusi Siap Cetak BAST -->
                        <template x-if="filteredDistribusiList.length === 0">
                            <tr>
                                <td colspan="8" class="text-center py-12 text-slate-500">
                                    <div class="flex flex-col items-center justify-center space-y-1.5">
                                        <span class="text-2xl">🚚</span>
                                        <p class="font-semibold text-slate-300 text-xs">Tidak ada data BAST distribusi yang siap dicetak.</p>
                                        <p class="text-[11px] text-slate-500">Data BAST penyerahan hanya muncul untuk transaksi dengan status <span class="text-teal-400 font-semibold">"Dalam Pengiriman"</span> atau <span class="text-emerald-400 font-semibold">"Telah Diterima"</span>.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        </div>
