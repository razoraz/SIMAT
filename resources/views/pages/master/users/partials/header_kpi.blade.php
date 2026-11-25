        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-amber-600/15 via-slate-900 to-slate-900 border border-amber-500/30 rounded-3xl p-4 sm:p-6 lg:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <style>
                /* Mode Layar Medium ke Bawah (< 1024px): Rata Tengah Simetris */
                @media (max-width: 1023px) {
                    .users-banner-wrap {
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center !important;
                        gap: 1.25rem !important;
                    }
                    .users-text-col {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: center !important;
                        text-align: center !important;
                        width: 100% !important;
                    }
                    .users-badge,
                    .users-desc {
                        margin-left: auto !important;
                        margin-right: auto !important;
                        text-align: center !important;
                    }
                    .users-role-row {
                        justify-content: center !important;
                        width: 100% !important;
                    }
                    .users-btn-wrap {
                        display: flex !important;
                        justify-content: center !important;
                        width: 100% !important;
                    }
                }
                /* Mode Desktop Luas (>= 1024px): Rata Kiri Standar */
                @media (min-width: 1024px) {
                    .users-banner-wrap {
                        flex-direction: row !important;
                        align-items: center !important;
                        justify-content: space-between !important;
                    }
                    .users-text-col {
                        display: flex !important;
                        flex-direction: column !important;
                        align-items: flex-start !important;
                        text-align: left !important;
                    }
                    .users-badge,
                    .users-desc {
                        margin-left: 0 !important;
                        margin-right: 0 !important;
                        text-align: left !important;
                    }
                    .users-role-row {
                        justify-content: flex-start !important;
                        width: auto !important;
                    }
                    .users-btn-wrap {
                        display: flex !important;
                        justify-content: flex-end !important;
                        width: auto !important;
                    }
                }
            </style>

            <div class="users-banner-wrap flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div class="users-text-col min-w-0 flex-1">
                    <div class="users-badge inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[10px] sm:text-xs font-extrabold uppercase tracking-wider mb-3 shadow-sm backdrop-blur-md max-w-full">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse shadow-sm shadow-amber-400 shrink-0"></span>
                        <span class="truncate sm:whitespace-normal">MANAJEMEN AKUN &amp; HAK AKSES ROLE PEGAWAI</span>
                    </div>
                    <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white tracking-tight break-words">Manajemen Pengguna SIMAT-RK</h1>
                    <p class="users-desc text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed break-words">
                        Pengelolaan akun pegawai RSUD Dr. H. Koesnandi, pengaturan tingkatan hak akses otorisasi (3 Role), dan penetapan unit/ruangan penugasan.
                    </p>

                    <!-- Role Status & Otorisasi Badge -->
                    <div class="users-role-row mt-4 flex flex-wrap items-center gap-2 text-xs">
                        <div class="inline-flex items-center space-x-2 px-3 py-1.5 rounded-xl border shrink-0"
                            :class="currentUserRole === 'admin' ? 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30 font-bold' : 'bg-amber-500/15 text-amber-300 border-amber-500/30 font-bold'">
                            <span x-text="currentUserRole === 'admin' ? '🛡️ Sesi Aktif: Admin Operasional' : '👑 Sesi Aktif: Master Admin'"></span>
                        </div>
                        <span class="text-slate-400 text-[11px]" x-show="currentUserRole === 'admin'">
                            • Hak Akses: <strong class="text-emerald-400">Penuh atas Sub Admin</strong> (Tambah/Ubah/Hapus) &amp; <strong class="text-cyan-300">Ubah Profil Sendiri</strong>. Akun Admin lain &amp; Master Admin diproteksi.
                        </span>
                        <span class="text-slate-400 text-[11px]" x-show="currentUserRole === 'master_admin'">
                            • Hak Akses: <strong class="text-amber-400">Superuser Penuh</strong> atas semua tingkatan role akun.
                        </span>
                    </div>
                </div>
                
                <!-- Action Button Tambah Pengguna / Sub Admin -->
                @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('users', 'create'))
                <div class="users-btn-wrap shrink-0">
                    <button type="button" @click="openAddModal()"
                        class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-2 shrink-0 active:scale-95">
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span x-text="currentUserRole === 'admin' ? 'Tambah Sub Admin' : 'Tambah Pengguna'"></span>
                    </button>
                </div>
                @endif
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 sm:gap-3 mt-5 sm:mt-6 pt-5 sm:pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-base sm:text-lg shrink-0">👥</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Total Pengguna</span>
                        <span class="text-xs sm:text-sm lg:text-base font-extrabold text-white truncate block" x-text="users.length + ' Akun'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-amber-500/10 text-amber-300 text-base sm:text-lg shrink-0">👑</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Master Admin</span>
                        <span class="text-xs sm:text-sm lg:text-base font-extrabold text-amber-300 truncate block" x-text="countMasterAdmin + ' Akun'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-base sm:text-lg shrink-0">🛡️</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Admin Operasional</span>
                        <span class="text-xs sm:text-sm lg:text-base font-extrabold text-cyan-300 truncate block" x-text="countAdmin + ' Akun'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-2.5 sm:p-3.5 flex items-center space-x-2.5 sm:space-x-3 min-w-0">
                    <div class="p-2 sm:p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-base sm:text-lg shrink-0">🏥</div>
                    <div class="min-w-0 flex-1">
                        <span class="text-[9.5px] sm:text-[10px] uppercase tracking-wider font-semibold text-slate-400 block truncate">Sub Admin Unit</span>
                        <span class="text-xs sm:text-sm lg:text-base font-extrabold text-emerald-300 truncate block" x-text="countSubAdmin + ' Akun'"></span>
                    </div>
                </div>
            </div>
        </div>
