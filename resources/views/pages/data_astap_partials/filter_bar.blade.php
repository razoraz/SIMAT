        <!-- ========================================================================= -->
        <!-- AREA PEMFILTERAN & PENCARIAN (FULL WIDTH, RAPID & BERSIH)                 -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Live Search Bar + Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama barang / lokasi penempatan / tahun / merk..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                        <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-emerald-400 font-bold" x-text="filteredAstaps.length"></span> dari <span class="text-white font-bold" x-text="astaps.length"></span> Data
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>

                <!-- Advanced Filter Collapsible Bar (4 Kolom: KIB, Tahun, Triwulan, Kondisi) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-3 border-t border-slate-800/60">
                    
                    <!-- Filter KIB -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Klasifikasi KIB</label>
                        <div class="relative">
                            <select x-model="categoryFilter"
                                style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-semibold text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 cursor-pointer hover:bg-slate-900/80 transition-all">
                                <option value="all" class="bg-slate-900 text-slate-200 py-2 font-medium">Semua</option>
                                <option value="KIB A" class="bg-slate-900 text-amber-300 py-2 font-medium">KIB A - Tanah</option>
                                <option value="KIB B" class="bg-slate-900 text-cyan-300 py-2 font-medium">KIB B - Peralatan &amp; Mesin</option>
                                <option value="KIB C" class="bg-slate-900 text-purple-300 py-2 font-medium">KIB C - Gedung &amp; Bangunan</option>
                                <option value="KIB D" class="bg-slate-900 text-teal-300 py-2 font-medium">KIB D - Jalan &amp; Jaringan</option>
                                <option value="KIB E" class="bg-slate-900 text-orange-300 py-2 font-medium">KIB E - Aset Tetap Lainnya</option>
                                <option value="KIB F" class="bg-slate-900 text-rose-300 py-2 font-medium">KIB F - Konstruksi KDP</option>
                                <option value="ATB" class="bg-slate-900 text-indigo-300 py-2 font-medium">ATB - Aset Tidak Berwujud</option>
                                <option value="EXTRACOM" class="bg-slate-900 text-amber-400 py-2 font-medium">Extracom</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tahun -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Perolehan</label>
                        <div class="relative">
                            <select x-model="tahunFilter"
                                style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-semibold text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 cursor-pointer hover:bg-slate-900/80 transition-all">
                                <option value="all" class="bg-slate-900 text-slate-200 py-2 font-medium">Semua Tahun</option>
                                <template x-for="yr in availableYears" :key="yr">
                                    <option :value="yr" class="bg-slate-900 text-cyan-300 py-2 font-medium" x-text="yr"></option>
                                </template>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Triwulan Pengadaan -->
                    <div>
                        <label class="block text-[10px] font-bold text-cyan-400 uppercase tracking-wider mb-1">Triwulan Pengadaan</label>
                        <div class="relative">
                            <select x-model="triwulanFilter"
                                style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-semibold text-cyan-300 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/30 cursor-pointer hover:bg-slate-900/80 transition-all">
                                <option value="all" class="bg-slate-900 text-slate-200 py-2 font-medium">Semua Triwulan</option>
                                <option value="TW I" class="bg-slate-900 text-cyan-300 py-2 font-medium">Triwulan I (TW I)</option>
                                <option value="TW II" class="bg-slate-900 text-cyan-300 py-2 font-medium">Triwulan II (TW II)</option>
                                <option value="TW III" class="bg-slate-900 text-cyan-300 py-2 font-medium">Triwulan III (TW III)</option>
                                <option value="TW IV" class="bg-slate-900 text-cyan-300 py-2 font-medium">Triwulan IV (TW IV)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Kondisi Barang -->
                    <div>
                        <label class="block text-[10px] font-bold text-emerald-400 uppercase tracking-wider mb-1">Kondisi Barang</label>
                        <div class="relative">
                            <select x-model="kondisiFilter"
                                style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-semibold text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 cursor-pointer hover:bg-slate-900/80 transition-all">
                                <option value="all" class="bg-slate-900 text-slate-200 py-2 font-medium">Semua Kondisi</option>
                                <option value="Baik" class="bg-slate-900 text-emerald-400 py-2 font-medium">🟢 Baik</option>
                                <option value="Kurang Baik" class="bg-slate-900 text-amber-400 py-2 font-medium">🟡 Kurang Baik</option>
                                <option value="Rusak Berat" class="bg-slate-900 text-rose-400 py-2 font-medium">🔴 Rusak Berat</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
