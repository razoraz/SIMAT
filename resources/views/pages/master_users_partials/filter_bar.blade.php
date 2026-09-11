        <!-- Filter & Search Toolbar -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-4 sm:p-5 shadow-xl mb-6">
            <div class="flex flex-col md:flex-row items-center gap-3">
                
                <!-- 1. Filter Role Dropdown Select -->
                <div class="min-w-[220px] w-full md:w-auto shrink-0">
                    <select x-model="roleFilter"
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 text-xs font-bold text-amber-300 focus:outline-none focus:border-amber-500 transition-all cursor-pointer">
                        <option value="all" class="bg-slate-900 text-white">Semua Role ({{ count($users ?? []) }})</option>
                        <option value="master_admin" class="bg-slate-900 text-white">👑 Master Admin ({{ collect($users ?? [])->where('role', 'master_admin')->count() }})</option>
                        <option value="admin" class="bg-slate-900 text-white">🛡️ Admin ({{ collect($users ?? [])->where('role', 'admin')->count() }})</option>
                        <option value="sub_admin" class="bg-slate-900 text-white">🏥 Sub Admin ({{ collect($users ?? [])->where('role', 'sub_admin')->count() }})</option>
                    </select>
                </div>

                <!-- 2. Input Search -->
                <div class="relative flex-1 w-full">
                    <input type="text" x-model="searchQuery" placeholder="Cari nama pegawai / email kredensial / NIP / unit penugasan..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-all">
                    <svg class="w-4 h-4 text-amber-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                </div>

                <!-- 3. Tombol Reset Filter -->
                <button type="button" @click="resetFilters()"
                    class="px-4 py-3 rounded-2xl bg-slate-950 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-bold border border-slate-800 transition-all flex items-center justify-center space-x-1.5 shrink-0 active:scale-95 cursor-pointer w-full md:w-auto">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                    <span>Reset</span>
                </button>

            </div>
        </div>
