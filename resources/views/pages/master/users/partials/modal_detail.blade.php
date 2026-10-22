        <!-- ========================================================================= -->
        <!-- MODAL DETAIL USER                                                         -->
        <!-- ========================================================================= -->
        <template x-teleport="body">
            <div x-show="showDetailModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
                 style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>
                <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 relative my-auto max-h-[92vh] overflow-y-auto custom-scrollbar">
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

                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1">Role Otorisasi</span>
                            <p class="font-bold text-amber-300" x-text="selectedUser.role ? selectedUser.role.toUpperCase().replace('_', ' ') : ''"></p>
                        </div>

                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Deskripsi Tugas & Wewenang:</span>
                            <p class="text-slate-300 leading-relaxed" x-text="selectedUser.penugasan || selectedUser.deskripsi || '-'"></p>
                        </div>

                        <!-- Hak Akses Modul Operasional -->
                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-2">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">Wewenang Modul Sistem:</span>
                                <span class="text-[10px] font-mono text-cyan-400" x-text="selectedUser.role === 'master_admin' ? 'Akses Penuh' : (selectedUser.role === 'admin' ? (getUserPermissionsSummary(selectedUser)?.text || 'Modul Aktif') : 'Ruangan Unit')"></span>
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
                                <div class="space-y-2 pt-0.5 max-h-[250px] overflow-y-auto custom-scrollbar pr-1">
                                    <template x-for="(perm, key) in availablePermissions" :key="key">
                                        <div class="p-2.5 rounded-xl border transition-all"
                                            :class="isDetailModEnabled(key) ? 'bg-cyan-950/20 border-cyan-500/30 text-white' : 'bg-slate-900/40 border-slate-800/80 text-slate-500 opacity-60'">
                                            <div class="flex items-center justify-between text-xs">
                                                <div class="flex items-center space-x-2 min-w-0 flex-1">
                                                    <span class="text-sm" x-text="perm.icon"></span>
                                                    <span class="font-bold text-[11px] text-white truncate" x-text="perm.label"></span>
                                                </div>
                                                <div class="flex items-center space-x-1.5 shrink-0">
                                                    <template x-if="isDetailModEnabled(key)">
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40"
                                                            x-text="getDetailModActions(key).length + ' Aksi Aktif'"></span>
                                                    </template>
                                                    <template x-if="!isDetailModEnabled(key)">
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-slate-800 text-slate-500 border border-slate-700">Nonaktif</span>
                                                    </template>
                                                </div>
                                            </div>

                                            <!-- Sub-Aksi Aktif Badges -->
                                            <template x-if="isDetailModEnabled(key) && getDetailModActions(key).length > 0">
                                                <div class="flex flex-wrap gap-1 mt-2 pt-2 border-t border-cyan-500/10">
                                                    <template x-for="actKey in getDetailModActions(key)" :key="actKey">
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded-md text-[9px] font-medium"
                                                              :class="perm.actions[actKey]?.critical ? 'bg-rose-500/10 text-rose-300 border border-rose-500/30' : 'bg-cyan-500/10 text-cyan-300 border border-cyan-500/20'"
                                                              :title="perm.actions[actKey]?.desc || ''">
                                                            <span class="mr-1" x-text="perm.actions[actKey]?.critical ? '⚠️' : '✓'"></span>
                                                            <span x-text="perm.actions[actKey]?.label || actKey"></span>
                                                        </span>
                                                    </template>
                                                </div>
                                            </template>
                                        </div>
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

                        <!-- ========================================================= -->
                        <!-- RIWAYAT LOG AUDIT WEWENANG & ESKALASI AKSES              -->
                        <!-- ========================================================= -->
                        <div class="p-3.5 bg-slate-950 rounded-2xl border border-slate-800 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-xs">📜</span>
                                    <span class="text-slate-300 text-[11px] uppercase font-bold tracking-wider">Log Audit Eskalasi & Wewenang</span>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold bg-slate-800 text-slate-400 border border-slate-700"
                                      x-text="(selectedUser.access_logs?.length || 0) + ' Catatan Terbaru'"></span>
                            </div>

                            <!-- List Timeline Log -->
                            <template x-if="selectedUser.access_logs && selectedUser.access_logs.length > 0">
                                <div class="space-y-2 max-h-[180px] overflow-y-auto custom-scrollbar pr-1 pt-1">
                                    <template x-for="log in selectedUser.access_logs" :key="log.id">
                                        <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800/80 hover:border-slate-700 transition-all text-[11px] space-y-1">
                                            <div class="flex items-center justify-between gap-2">
                                                <div class="flex items-center space-x-1.5 min-w-0">
                                                    <!-- Action Badge -->
                                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-bold shrink-0"
                                                          :class="getAuditBadge(log.action_type).class"
                                                          x-text="getAuditBadge(log.action_type).label"></span>
                                                    <span class="text-[10px] text-slate-400 truncate"
                                                          x-text="'Oleh: ' + (log.actor?.name || 'Sistem')"></span>
                                                </div>
                                                <span class="text-[9px] font-mono text-slate-400 shrink-0"
                                                      x-text="formatAuditDate(log.created_at)"></span>
                                            </div>
                                            <p class="text-slate-300 text-[11px] leading-relaxed" x-text="log.summary"></p>
                                            <div class="flex items-center justify-between text-[9px] text-slate-400 pt-0.5 border-t border-slate-800/50">
                                                <span class="font-mono" x-text="'IP: ' + (log.ip_address || '127.0.0.1')"></span>
                                                <template x-if="log.old_role && log.new_role && log.old_role !== log.new_role">
                                                    <span class="text-amber-400/90 font-mono" x-text="log.old_role + ' → ' + log.new_role"></span>
                                                </template>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- State Kosong (Belum ada catatan log) -->
                            <template x-if="!selectedUser.access_logs || selectedUser.access_logs.length === 0">
                                <div class="py-3 text-center rounded-xl bg-slate-900/40 border border-dashed border-slate-800/80 text-slate-400 text-[11px]">
                                    <span>🛡️ Belum ada riwayat eskalasi hak akses atau mutasi peran untuk akun ini.</span>
                                </div>
                            </template>
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
        </template>
