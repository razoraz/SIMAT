        <!-- ========================================================================= -->
        <!-- KONTEN TAB 3: BAST MUTASI ASET (INTERNAL ANTAR-RUANG & EKSTERNAL OPD)       -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'mutasi'" class="space-y-6" x-cloak>
            
            <!-- Sub-nav Switcher Scope Mutasi: Internal vs Eksternal -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-3 shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center space-x-2 bg-slate-950/80 p-1.5 rounded-2xl border border-slate-800/80 w-full sm:w-auto">
                    <!-- Scope 1: Internal -->
                    <button type="button" @click="mutasiScope = 'internal'"
                        class="flex-1 sm:flex-initial py-2.5 px-4 sm:px-6 rounded-xl text-xs font-bold transition-all flex items-center justify-center space-x-2 cursor-pointer active:scale-95 whitespace-nowrap shrink-0"
                        :class="mutasiScope === 'internal' ? 'bg-indigo-500 text-slate-950 shadow-md shadow-indigo-500/30' : 'text-slate-400 hover:text-white'">
                        <span class="whitespace-nowrap">Mutasi Internal</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold shrink-0"
                            :class="mutasiScope === 'internal' ? 'bg-slate-950 text-indigo-300' : 'bg-slate-800 text-slate-300'"
                            x-text="mutasiList.length"></span>
                    </button>

                    <!-- Scope 2: Eksternal -->
                    <button type="button" @click="mutasiScope = 'eksternal'"
                        class="flex-1 sm:flex-initial py-2.5 px-4 sm:px-6 rounded-xl text-xs font-bold transition-all flex items-center justify-center space-x-2 cursor-pointer active:scale-95 whitespace-nowrap shrink-0"
                        :class="mutasiScope === 'eksternal' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/30' : 'text-slate-400 hover:text-white'">
                        <span class="whitespace-nowrap">Mutasi Eksternal</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold shrink-0"
                            :class="mutasiScope === 'eksternal' ? 'bg-slate-950 text-cyan-300' : 'bg-slate-800 text-slate-300'"
                            x-text="mutasiEksternalList.length"></span>
                    </button>
                </div>

                <div class="text-xs text-slate-400 flex items-center space-x-2 w-full sm:w-auto justify-end">
                    <span class="inline-block w-2 h-2 rounded-full" :class="mutasiScope === 'internal' ? 'bg-indigo-400 animate-pulse' : 'bg-cyan-400 animate-pulse'"></span>
                    <span class="text-[11px] font-medium" x-text="mutasiScope === 'internal' ? 'Ruang Lingkup: Internal RSUD dr. H. Koesnandi' : 'Ruang Lingkup: Antar-OPD Pemkab Bondowoso / SKPD Luar'"></span>
                </div>
            </div>

            <!-- Toolbar & Filter Mutasi Internal -->
            <div x-show="mutasiScope === 'internal'" class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-80">
                            <input type="text" x-model="mutasiSearch" placeholder="Cari nomor BAST mutasi / barang / ruangan..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                            <svg class="w-4 h-4 text-indigo-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Filter Status TTD Mutasi Internal -->
                        <select x-model="mutasiStatusFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-indigo-300 font-bold focus:outline-none focus:border-indigo-500">
                            <option value="all">🔍 Semua Status TTD Mutasi</option>
                            <option value="signed">✍️ Telah Ditandatangani BSrE</option>
                            <option value="unsigned">⏳ Belum Ditandatangani</option>
                        </select>
                    </div>

                    <div class="text-[11px] text-slate-400 font-mono">
                        Menampilkan <span class="text-indigo-400 font-bold" x-text="filteredMutasiList.length"></span> dari <span x-text="mutasiList.length"></span> BAST Internal
                    </div>
                </div>
            </div>

            <!-- Toolbar & Filter Mutasi Eksternal -->
            <div x-show="mutasiScope === 'eksternal'" class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-80">
                            <input type="text" x-model="mutasiEksternalSearch" placeholder="Cari nomor BAST / barang / OPD asal atau tujuan..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                            <svg class="w-4 h-4 text-cyan-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Filter Tipe Masuk / Keluar -->
                        <select x-model="mutasiEksternalTipeFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-cyan-300 font-bold focus:outline-none focus:border-cyan-500">
                            <option value="all">🌐 Semua Tipe Transfer</option>
                            <option value="masuk">📥 Pelimpahan Masuk (Dari OPD Luar)</option>
                            <option value="keluar">📤 Mutasi Keluar (Ke OPD Luar)</option>
                        </select>

                        <!-- Filter Status TTD Mutasi Eksternal -->
                        <select x-model="mutasiEksternalStatusFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-cyan-300 font-bold focus:outline-none focus:border-cyan-500">
                            <option value="all">🔍 Semua Status TTD BSrE</option>
                            <option value="signed">✍️ Telah Ditandatangani BSrE</option>
                            <option value="unsigned">⏳ Belum Ditandatangani</option>
                        </select>
                    </div>

                    <div class="text-[11px] text-slate-400 font-mono">
                        Menampilkan <span class="text-cyan-400 font-bold" x-text="filteredMutasiEksternalList.length"></span> dari <span x-text="mutasiEksternalList.length"></span> BAST Eksternal
                    </div>
                </div>
            </div>

            <!-- ===================================================================== -->
            <!-- 1. TABEL DAFTAR BAST MUTASI INTERNAL (ANTAR RUANGAN RSUD)             -->
            <!-- ===================================================================== -->
            <div x-show="mutasiScope === 'internal'" class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
                <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 380px; overflow-y: auto; overflow-x: auto;">
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
                                        <div class="font-mono font-bold text-indigo-400" x-text="item.nomor_bast"></div>
                                        <div class="text-[10px] text-slate-400" x-text="item.tgl_bast"></div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-white" x-text="item.nama"></div>
                                        <div class="text-[10px] text-slate-400 font-mono" x-text="item.kode_barang + ' • Vol: ' + item.qty + ' ' + item.satuan"></div>
                                    </td>
                                    <td class="px-4 py-4 text-center font-semibold text-slate-300" x-text="item.asal"></td>
                                    <td class="px-4 py-4 text-center font-semibold text-indigo-300" x-text="item.tujuan"></td>
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
                                        <!-- Toggle TTD -->
                                        <button type="button" @click="toggleSignMutasi(item)"
                                            :class="item.signed ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-sm'"
                                            class="px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all inline-flex items-center space-x-1 active:scale-95 cursor-pointer"
                                            :title="item.signed ? 'Batalkan Tanda Tangan Digital BSrE' : 'Tanda Tangan Digital BSrE'">
                                            <span x-text="item.signed ? '↩️ Batal TTD' : '✍️ TTD BSrE'"></span>
                                        </button>

                                        <!-- Rincian -->
                                        <button type="button" @click="openDetailMutasi(item)"
                                            class="px-2.5 py-1.5 rounded-xl bg-indigo-500/15 hover:bg-indigo-500/25 text-indigo-300 border border-indigo-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <span>👁️ Rincian</span>
                                        </button>

                                        <!-- Cetak -->
                                        <button type="button" @click="openPrintMutasi(item)"
                                            class="px-2.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <span>🖨️ Cetak / Edit</span>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredMutasiList.length === 0">
                                <td colspan="7" class="py-8 text-center text-slate-500">
                                    Tidak ada data Berita Acara Mutasi Internal yang cocok dengan filter pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ===================================================================== -->
            <!-- 2. TABEL DAFTAR BAST MUTASI EKSTERNAL (TRANSFER ANTAR-OPD / SKPD)     -->
            <!-- ===================================================================== -->
            <div x-show="mutasiScope === 'eksternal'" class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
                <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 380px; overflow-y: auto; overflow-x: auto;">
                    <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                        <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                            <tr>
                                <th class="px-4 py-3.5 text-center w-12">No</th>
                                <th class="px-4 py-3.5 text-left">Nomor BAST Pelimpahan</th>
                                <th class="px-4 py-3.5 text-left">Nama Barang & Nilai Perolehan</th>
                                <th class="px-4 py-3.5 text-left">Pihak 1 (OPD Pengirim)</th>
                                <th class="px-4 py-3.5 text-left">Pihak 2 (Penerima RSUD)</th>
                                <th class="px-4 py-3.5 text-center">Tipe</th>
                                <th class="px-4 py-3.5 text-center">Status TTE</th>
                                <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi BAST</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            <template x-for="(item, index) in filteredMutasiEksternalList" :key="item.id">
                                <tr class="hover:bg-slate-800/30 transition-colors">
                                    <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                                    
                                    <!-- Nomor BAST & Tanggal -->
                                    <td class="px-4 py-4">
                                        <div class="font-mono font-bold text-cyan-400" x-text="item.nomor_bast"></div>
                                        <div class="text-[10px] text-slate-400" x-text="item.tgl_bast"></div>
                                    </td>

                                    <!-- Nama Barang, Kode 108 & Nilai Perolehan -->
                                    <td class="px-4 py-4">
                                        <div class="font-bold text-white" x-text="item.nama"></div>
                                        <div class="text-[10px] text-slate-400 font-mono" x-text="item.kode_108 + ' • Vol: ' + item.vol + ' ' + item.satuan"></div>
                                        <div class="text-[10px] text-emerald-400 font-mono font-semibold" x-text="item.nilai_perolehan_format"></div>
                                    </td>

                                    <!-- Pihak 1: OPD Asal -->
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-slate-200" x-text="item.opd_asal"></div>
                                        <div class="text-[10px] text-slate-400" x-text="'PJ: ' + item.pj_asal_nama"></div>
                                    </td>

                                    <!-- Pihak 2: RSUD Koesnandi -->
                                    <td class="px-4 py-4">
                                        <div class="font-semibold text-cyan-300" x-text="item.ruangan_tujuan ? ('RSUD (' + item.ruangan_tujuan + ')') : 'RSUD Koesnandi'"></div>
                                        <div class="text-[10px] text-slate-400" x-text="'PJ: ' + item.pj_tujuan_nama"></div>
                                    </td>

                                    <!-- Tipe Mutasi (Masuk vs Keluar) -->
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold border"
                                              :class="item.tipe === 'keluar' ? 'bg-amber-500/15 text-amber-300 border-amber-500/30' : 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30'"
                                              x-text="item.tipe === 'keluar' ? '📤 Keluar' : '📥 Masuk'">
                                        </span>
                                    </td>

                                    <!-- Status TTE BSrE -->
                                    <td class="px-4 py-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap border shadow-sm"
                                              :class="item.signed ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/15 text-amber-300 border-amber-500/30'">
                                            <template x-if="item.signed">
                                                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </template>
                                            <template x-if="!item.signed">
                                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </template>
                                            <span x-text="item.signed ? 'Sudah TTE BSrE' : 'Belum TTD'"></span>
                                        </span>
                                    </td>

                                    <!-- Aksi BAST Eksternal -->
                                    <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                        <!-- Toggle TTD -->
                                        <button type="button" @click="toggleSignMutasiEksternal(item)"
                                            :class="item.signed ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-sm'"
                                            class="px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all inline-flex items-center space-x-1 active:scale-95 cursor-pointer"
                                            :title="item.signed ? 'Batalkan Tanda Tangan Digital BSrE' : 'Tanda Tangan Digital BSrE'">
                                            <span x-text="item.signed ? '↩️ Batal TTD' : '✍️ TTD BSrE'"></span>
                                        </button>

                                        <!-- Rincian -->
                                        <button type="button" @click="openDetailMutasiEksternal(item)"
                                            class="px-2.5 py-1.5 rounded-xl bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <span>👁️ Rincian</span>
                                        </button>

                                        <!-- Cetak BAST via Jalur Fitur Mutasi Eksternal -->
                                        <a :href="'/mutasi-eksternal/' + (item.mutasi_id || item.id) + '/cetak?returnTo=' + encodeURIComponent('/berita-acara?tab=mutasi&scope=eksternal')"
                                            target="_blank"
                                            class="px-2.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer"
                                            title="Cetak Berita Acara Serah Terima (BAST) Pelimpahan BMD">
                                            <span>🖨️ Cetak</span>
                                        </a>
                                    </td>
                                </tr>
                            </template>
                            <tr x-show="filteredMutasiEksternalList.length === 0">
                                <td colspan="8" class="py-8 text-center text-slate-500">
                                    Tidak ada data Berita Acara Mutasi Eksternal (Pelimpahan BMD) yang cocok dengan filter pencarian.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
