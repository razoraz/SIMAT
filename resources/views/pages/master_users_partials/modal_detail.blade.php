        <!-- ========================================================================= -->
        <!-- MODAL DETAIL USER                                                         -->
        <!-- ========================================================================= -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showDetailModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800">
                    <h3 class="text-base font-extrabold text-white">Detail Akun & Kredensial Pegawai</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Informasi Profil dan Wewenang Role SIMAT-RK</p>
                </div>

                <div class="space-y-3.5 text-xs" x-if="selectedUser">
                    <div class="flex items-center space-x-3.5 p-3.5 rounded-2xl bg-slate-950 border border-slate-800">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center font-extrabold text-base shrink-0"
                             :class="{
                                 'bg-amber-500/20 text-amber-300 border border-amber-500/30': selectedUser.role === 'master_admin',
                                 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': selectedUser.role === 'admin',
                                 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': selectedUser.role === 'sub_admin'
                             }"
                             x-text="(selectedUser.name || 'U').substring(0, 1)"></div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center space-x-2">
                                <p class="font-extrabold text-white text-base truncate" x-text="selectedUser.name"></p>
                                <span x-show="isSelf(selectedUser)" class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shrink-0">Akun Saya</span>
                            </div>
                            <p class="text-slate-400 font-mono text-xs" x-text="'NIP: ' + (selectedUser.nip || '-')"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">Email Kredensial</span>
                            <p class="font-mono font-bold text-cyan-400 truncate" x-text="selectedUser.email"></p>
                        </div>
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">Unit Penugasan</span>
                            <p class="font-semibold text-emerald-400 truncate" x-text="selectedUser.unit || '-'"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">Role Otorisasi</span>
                            <p class="font-bold text-amber-300" x-text="selectedUser.role.toUpperCase().replace('_', ' ')"></p>
                        </div>
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">Status Akun</span>
                            <p class="font-bold text-emerald-400" x-text="selectedUser.status || 'Aktif'"></p>
                        </div>
                    </div>

                    <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block">Deskripsi Tugas & Wewenang:</span>
                        <p class="text-slate-300 leading-relaxed" x-text="selectedUser.penugasan || selectedUser.deskripsi || '-'"></p>
                    </div>

                    <!-- Hak Akses Modul Operasional -->
                    <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Wewenang Modul Sistem:</span>
                            <span class="text-[10px] font-mono text-cyan-400" x-text="selectedUser.role === 'master_admin' ? 'Akses Penuh' : (selectedUser.role === 'admin' ? ((selectedUser.permissions || []).length + ' Modul Aktif') : 'Ruangan Unit')"></span>
                        </div>

                        <!-- Master Admin -->
                        <template x-if="selectedUser.role === 'master_admin'">
                            <div class="p-2.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-300 text-[11px] font-bold flex items-center space-x-2">
                                <span>👑</span>
                                <span>Akses Penuh (Full Control): Berhak mengelola seluruh data, pengguna, dan sistem SIMAT-RK.</span>
                            </div>
                        </template>

                        <!-- Admin Operasional -->
                        <template x-if="selectedUser.role === 'admin'">
                            <div class="flex flex-wrap gap-1.5 pt-0.5">
                                <template x-for="(perm, key) in availablePermissions" :key="key">
                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold flex items-center space-x-1.5 border"
                                        :class="(selectedUser.permissions && selectedUser.permissions.includes(key)) || !selectedUser.permissions ? 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30' : 'bg-slate-900/50 text-slate-600 border-slate-800/80 line-through opacity-50'">
                                        <span x-text="perm.icon"></span>
                                        <span x-text="perm.label"></span>
                                        <span x-text="(selectedUser.permissions && selectedUser.permissions.includes(key)) || !selectedUser.permissions ? '✓' : '✗'" class="text-[9px] font-bold"></span>
                                    </span>
                                </template>
                            </div>
                        </template>

                        <!-- Sub Admin -->
                        <template x-if="selectedUser.role === 'sub_admin'">
                            <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-[11px] font-medium flex items-center space-x-2">
                                <span>🏥</span>
                                <span>Wewenang Ruangan: Akses Katalog ASTAP, Lembar KIR Ruangan, dan Pengajuan Distribusi Unit.</span>
                            </div>
                        </template>
                    </div>

                    <!-- Tombol Reset Password Cepat -->
                    <div class="pt-2 flex items-center justify-between p-3 bg-amber-500/10 border border-amber-500/20 rounded-2xl">
                        <div class="text-[11px] text-amber-200">
                            <span class="font-bold block">🔑 Reset Password Default:</span>
                            <span class="text-slate-400">Kembalikan password akun ini ke <code class="text-amber-300 font-bold">rsud123</code></span>
                        </div>
                        <button type="button" @click="resetPasswordAction(selectedUser)"
                            class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-md transition-all active:scale-95 shrink-0">
                            Reset ke rsud123
                        </button>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-center">
                    <button type="button" @click="showDetailModal = false" class="px-6 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tutup</span>
                    </button>
                </div>
            </div>
        </div>
