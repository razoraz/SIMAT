        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-amber-600/15 via-slate-900 to-slate-900 border border-amber-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-xs font-extrabold uppercase tracking-wider mb-3 shadow-sm backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shadow-sm shadow-amber-400"></span>
                        <span>MANAJEMEN AKUN & HAK AKSES ROLE PEGAWAI</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Manajemen Pengguna SIMAT-RK</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pengelolaan akun pegawai RSUD Dr. H. Koesnandi, pengaturan tingkatan hak akses otorisasi (3 Role), penetapan unit/ruangan penugasan, dan status akun aktif.
                    </p>

                    <!-- Role Status & Otorisasi Badge -->
                    <div class="mt-4 flex flex-wrap items-center gap-2 text-xs">
                        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl border"
                            :class="currentUserRole === 'admin' ? 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30 font-bold' : 'bg-amber-500/15 text-amber-300 border-amber-500/30 font-bold'">
                            <span x-text="currentUserRole === 'admin' ? '🛡️ Sesi Aktif: Admin Operasional' : '👑 Sesi Aktif: Master Admin'"></span>
                        </div>
                        <span class="text-slate-400 text-[11px]" x-show="currentUserRole === 'admin'">
                            • Hak Akses: <strong class="text-emerald-400">Penuh atas Sub Admin</strong> (Tambah/Ubah/Hapus) & <strong class="text-cyan-300">Ubah Profil Sendiri</strong>. Akun Admin lain & Master Admin diproteksi.
                        </span>
                        <span class="text-slate-400 text-[11px]" x-show="currentUserRole === 'master_admin'">
                            • Hak Akses: <strong class="text-amber-400">Superuser Penuh</strong> atas semua tingkatan role akun.
                        </span>
                    </div>
                </div>
                
                <!-- Action Button Tambah Pengguna / Sub Admin -->
                <div class="shrink-0">
                    <button type="button" @click="openAddModal()"
                        class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-2 shrink-0 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span x-text="currentUserRole === 'admin' ? 'Tambah Sub Admin' : 'Tambah Pengguna'"></span>
                    </button>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">👥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Pengguna</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="users.length + ' Akun'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-300 text-lg">👑</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Master Admin</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300" x-text="countMasterAdmin + ' Akun'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">🛡️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Admin Operasional</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300" x-text="countAdmin + ' Akun'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Sub Admin Unit</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300" x-text="countSubAdmin + ' Akun'"></span>
                    </div>
                </div>
            </div>
        </div>
