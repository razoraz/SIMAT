        <!-- ========================================================================= -->
        <!-- KONTEN TAB 3: BAST MUTASI ASET (PEMINDAHAN ANTAR RUANGAN)                  -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'mutasi'" class="space-y-6" x-cloak>
            
            <!-- Toolbar & Filter Status Mutasi -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-80">
                            <input type="text" x-model="mutasiSearch" placeholder="Cari nomor BAST mutasi / barang / ruangan..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                            <svg class="w-4 h-4 text-rose-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Filter Status TTD -->
                        <select x-model="mutasiStatusFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-rose-300 font-bold focus:outline-none focus:border-rose-500">
                            <option value="all">🔍 Semua Status TTD Mutasi</option>
                            <option value="signed">✍️ Telah Ditandatangani BSrE</option>
                            <option value="unsigned">⏳ Belum Ditandatangani</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar BAST Mutasi Aset -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
                <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 340px; overflow-y: auto; overflow-x: auto;">
                    <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                        <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5 text-left">Nomor BAST Mutasi</th>
                            <th class="px-4 py-3.5 text-left">Nama Barang Dimutasi</th>
                            <th class="px-4 py-3.5 text-center">Ruangan Asal</th>
                            <th class="px-4 py-3.5 text-center">Ruangan Tujuan</th>
                            <th class="px-4 py-3.5 text-center">Status TTD BSrE</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi BAST</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredMutasiList" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                                <td class="px-4 py-4">
                                    <div class="font-mono font-bold text-rose-400" x-text="item.nomor_bast"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.tgl_bast"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-white" x-text="item.nama"></div>
                                    <div class="text-[10px] text-slate-400 font-mono" x-text="item.kode_barang + ' • Vol: ' + item.qty + ' ' + item.satuan"></div>
                                </td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-300" x-text="item.asal"></td>
                                <td class="px-4 py-4 text-center font-semibold text-rose-300" x-text="item.tujuan"></td>
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
                                    <button type="button" @click="toggleSignMutasi(item)"
                                        :class="item.signed ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-sm'"
                                        class="px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all inline-flex items-center space-x-1 active:scale-95"
                                        :title="item.signed ? 'Batalkan Tanda Tangan Digital BSrE' : 'Tanda Tangan Digital BSrE'">
                                        <span x-text="item.signed ? '↩️ Batal TTD' : '✍️ TTD BSrE'"></span>
                                    </button>

                                    <!-- 2. Tombol Rincian Modal -->
                                    <button type="button" @click="openDetailMutasi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>👁️ Rincian</span>
                                    </button>

                                    <!-- 3. Tombol Cetak (dengan Live Edit Panel) -->
                                    <button type="button" @click="openPrintMutasi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>🖨️ Cetak / Edit</span>
                                    </button>

                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        </div>
