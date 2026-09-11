        <!-- Filter, Toggle Grid/Table & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
                <div class="relative flex-1 w-full">
                    <input type="text" x-model="searchQuery" placeholder="Cari unit ruangan / kode lokasi / nama kepala ruangan..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all">
                    <svg class="w-4 h-4 text-blue-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                        Menampilkan <span class="text-blue-400 font-bold" x-text="filteredUnits.length"></span> dari <span class="text-white font-bold" x-text="units.length"></span> Unit
                    </span>
                    
                    <!-- View Toggle -->
                    <div class="flex items-center bg-slate-950 border border-slate-800 rounded-xl p-1">
                        <button type="button" @click="viewMode = 'grid'" 
                                :class="viewMode === 'grid' ? 'bg-blue-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'"
                                class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center space-x-1">
                            <span>Grid</span>
                        </button>
                        <button type="button" @click="viewMode = 'table'" 
                                :class="viewMode === 'table' ? 'bg-blue-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'"
                                class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center space-x-1">
                            <span>Tabel</span>
                        </button>
                    </div>

                    <button type="button" @click="resetFilters()"
                        class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                        🔄 Reset
                    </button>
                </div>
            </div>
        </div>
