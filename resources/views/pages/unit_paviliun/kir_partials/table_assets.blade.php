        <!-- 3. TABEL DAFTAR ASET RUANGAN & FILTER (NO-PRINT) -->
        <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
            
            <!-- Filter & Search Controls Header -->
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                <!-- Filter Status Pills -->
                <div class="flex flex-wrap items-center gap-1.5 text-xs">
                    <button type="button" @click="statusFilter = 'all'"
                        :class="statusFilter === 'all' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua (<span x-text="assets.length"></span>)
                    </button>
                    <button type="button" @click="statusFilter = 'Baik'"
                        :class="statusFilter === 'Baik' ? 'bg-emerald-500/20 text-emerald-300 font-extrabold border border-emerald-500/40 shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1">
                        <span>Baik</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px]" x-text="countBaik"></span>
                    </button>
                    <button type="button" @click="statusFilter = 'Kurang Baik'"
                        :class="statusFilter === 'Kurang Baik' ? 'bg-amber-500/20 text-amber-300 font-extrabold border border-amber-500/40 shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1">
                        <span>Kurang Baik</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-amber-500/20 text-amber-300 text-[10px]" x-text="countKurangBaik"></span>
                    </button>
                    <button type="button" @click="statusFilter = 'Rusak Berat'"
                        :class="statusFilter === 'Rusak Berat' ? 'bg-rose-500/20 text-rose-300 font-extrabold border border-rose-500/40 shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1">
                        <span>Rusak Berat</span>
                        <span class="px-1.5 py-0.2 rounded-full bg-rose-500/20 text-rose-300 text-[10px]" x-text="countRusakBerat"></span>
                    </button>
                </div>

                <!-- Search Input Box -->
                <div class="relative min-w-[260px]">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" x-model="searchQuery" placeholder="Cari nama, NIBAR, kode 108..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-xl pl-10 pr-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                </div>
            </div>

            <!-- Tabel Data Aset Fisik Ruangan (Scrollable 5-6 rows: max-h-[380px]) -->
            <div class="overflow-x-auto overflow-y-auto rounded-2xl border border-slate-800/80 bg-slate-950/50 relative shadow-inner"
                 style="max-height: 380px !important; overflow-y: auto !important; overflow-x: auto !important;">
                <table class="w-full text-left text-xs text-slate-300 border-separate border-spacing-0">
                    <thead class="sticky top-0 z-20 bg-slate-950 text-slate-400 font-bold uppercase tracking-wider shadow-sm">
                        <tr>
                            <th class="px-3 py-3 text-center whitespace-nowrap w-10 bg-slate-950 border-b border-slate-800">No</th>
                            <th class="px-3.5 py-3 text-left min-w-[160px] bg-slate-950 border-b border-slate-800">Nomor Register NIBAR</th>
                            <th class="px-3.5 py-3 text-left min-w-[220px] bg-slate-950 border-b border-slate-800">Nama Barang / ASTAP</th>
                            <th class="px-3.5 py-3 text-left min-w-[140px] bg-slate-950 border-b border-slate-800">Merk / Tipe</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap bg-slate-950 border-b border-slate-800">No. Seri/Pabrik</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap bg-slate-950 border-b border-slate-800">Tahun</th>
                            <th class="px-3.5 py-3 text-right whitespace-nowrap bg-slate-950 border-b border-slate-800">Nilai Buku</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap min-w-[120px] bg-slate-950 border-b border-slate-800">Kondisi Barang</th>
                            <th class="px-3.5 py-3 text-center whitespace-nowrap border-l border-b border-slate-800 shrink-0 min-w-[170px] w-[170px]" 
                                style="position: sticky; right: 0; top: 0; z-index: 30; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredAssets" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-3 py-3 text-center font-bold text-slate-500" x-text="index + 1"></td>
                                <td class="px-3.5 py-3">
                                    <div class="flex items-center space-x-1.5">
                                        <span class="font-mono font-semibold text-emerald-400 text-xs block truncate max-w-[200px]" 
                                              :title="item.nibar" x-text="item.nibar"></span>
                                    </div>
                                    <span class="font-mono text-[10px] text-slate-500 block" x-text="'108: ' + item.kode_108"></span>
                                </td>
                                <td class="px-3.5 py-3">
                                    <p class="font-bold text-white text-xs leading-snug" x-text="item.nama"></p>
                                    <p class="text-[10px] text-slate-400 truncate max-w-xs mt-0.5" x-text="item.kategori"></p>
                                </td>
                                <td class="px-3.5 py-3 text-slate-300" x-text="item.merk || '-'"></td>
                                <td class="px-3.5 py-3 text-center font-mono text-[11px] text-slate-400" x-text="item.no_seri || '-'"></td>
                                <td class="px-3.5 py-3 text-center font-mono text-slate-300 font-semibold" x-text="item.tahun || '-'"></td>
                                <td class="px-3.5 py-3 text-right font-mono font-semibold text-white whitespace-nowrap" x-text="item.harga_fmt"></td>
                                
                                <!-- Kolom Kondisi dengan Tombol Cepat Ubah -->
                                <td class="px-3.5 py-3 text-center whitespace-nowrap">
                                    <button type="button" @click="openEditKondisi(item)"
                                        class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg text-[10px] font-bold cursor-pointer hover:ring-2 hover:ring-amber-400/50 transition-all group"
                                        :class="item.kondisi === 'Baik' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 
                                               (item.kondisi === 'Kurang Baik' ? 'bg-amber-500/15 text-amber-300 border border-amber-500/30' : 
                                               (item.kondisi === 'Rusak Ringan' ? 'bg-orange-500/15 text-orange-300 border border-orange-500/30' : 'bg-rose-500/15 text-rose-300 border border-rose-500/30'))"
                                        title="Klik untuk ubah kondisi barang">
                                        <span x-text="item.kondisi"></span>
                                        <svg class="w-3 h-3 opacity-60 group-hover:opacity-100 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                    </button>
                                </td>

                                <!-- Kolom Aksi — FREEZE STICKY RIGHT -->
                                <td class="px-3.5 py-3 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[170px] w-[170px]" 
                                    style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- 1. Tombol Detail (Kaya Data ASTAP) -->
                                        <button type="button" @click="openDetail(item)"
                                            title="Lihat Detail ASTAP"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Detail</span>
                                        </button>

                                        <!-- 2. Tombol Ubah Kondisi -->
                                        <button type="button" @click="openEditKondisi(item)"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none"
                                            title="Ubah Kondisi Fisik Barang">
                                            <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Ubah</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <tr x-show="filteredAssets.length === 0">
                            <td colspan="9" class="p-8 text-center text-slate-400">
                                <span class="text-2xl block mb-2">🔍</span>
                                <p class="text-sm font-semibold">Tidak ada aset yang sesuai dengan pencarian atau filter Anda.</p>
                                <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci atau bersihkan filter kondisi.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Footer Info Table -->
            <div class="pt-2 flex flex-col sm:flex-row sm:items-center justify-between text-xs text-slate-400 gap-2">
                <div>
                    Menampilkan <strong class="text-white" x-text="filteredAssets.length"></strong> dari <strong class="text-white" x-text="assets.length"></strong> unit inventaris ruangan
                </div>
                <div class="flex items-center space-x-2">
                    <button type="button" @click="showPrintModal = true"
                        class="text-emerald-400 hover:underline font-semibold flex items-center space-x-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Pratinjau Lembar Cetak KIR &rarr;</span>
                    </button>
                </div>
            </div>
        </div>
