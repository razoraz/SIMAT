        <!-- Table Users -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-5 mb-6">
            <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 360px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 border-collapse">
                    <thead class="text-slate-400 font-bold uppercase tracking-wider text-[11px] border-b border-slate-800 sticky top-0 z-10 bg-slate-950 shadow-md" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap">No</th>
                            <th class="px-4 py-3.5 text-left min-w-[220px] whitespace-nowrap">Nama & NIP Pegawai</th>
                            <th class="px-4 py-3.5 text-left min-w-[190px] whitespace-nowrap">Email Kredensial</th>
                            <th class="px-4 py-3.5 text-center min-w-[160px] whitespace-nowrap">Role Otorisasi</th>
                            <th class="px-4 py-3.5 text-left min-w-[180px] whitespace-nowrap">Unit Penugasan</th>
                            <th class="px-4 py-3.5 text-center min-w-[90px] whitespace-nowrap">Status</th>
                            <th class="px-4 py-3.5 text-center min-w-[200px] whitespace-nowrap border-l border-slate-800/60" style="position: sticky; right: 0; z-index: 20; background-color: #020617;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredUsers" :key="item.id">
                            <tr class="group hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0"
                                             :class="{
                                                 'bg-amber-500/20 text-amber-300 border border-amber-500/30': item.role === 'master_admin',
                                                 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': item.role === 'admin',
                                                 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': item.role === 'sub_admin'
                                             }"
                                             x-text="(item.name || 'U').substring(0, 1)"></div>
                                        <div class="min-w-0">
                                            <div class="flex items-center space-x-1.5">
                                                <span class="font-bold text-white text-sm whitespace-nowrap" x-text="item.name"></span>
                                                <span x-show="isSelf(item)" class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shrink-0">Saya</span>
                                            </div>
                                            <div class="text-[10px] text-slate-400 font-mono whitespace-nowrap" x-text="'NIP: ' + (item.nip || '-')"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-4 font-mono font-semibold whitespace-nowrap"
                                    :class="item.role === 'master_admin' ? 'text-amber-300' : (item.role === 'admin' ? 'text-cyan-300' : 'text-emerald-300')"
                                    x-text="item.email"></td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center space-y-1">
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-block whitespace-nowrap"
                                            :class="{
                                                'bg-amber-500/20 text-amber-300 border-amber-500/30': item.role === 'master_admin',
                                                'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': item.role === 'admin',
                                                'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': item.role === 'sub_admin'
                                            }">
                                            <span class="whitespace-nowrap" x-text="item.role === 'master_admin' ? '👑 Master Admin' : (item.role === 'admin' ? '🛡️ Admin Operasional' : '🏥 Sub Admin Unit')"></span>
                                        </span>
                                        <!-- Badge info modul aktif untuk admin operasional -->
                                        <template x-if="item.role === 'admin'">
                                            <div class="flex items-center space-x-1 text-[9px] font-mono text-cyan-400">
                                                <template x-if="!item.permissions || item.permissions.length >= 7">
                                                    <span class="px-1.5 py-0.5 rounded bg-cyan-950/60 border border-cyan-500/30 font-semibold">Semua Modul</span>
                                                </template>
                                                <template x-if="item.permissions && item.permissions.length < 7">
                                                    <span class="px-1.5 py-0.5 rounded bg-slate-950/80 border border-slate-800 text-slate-300 font-semibold" x-text="item.permissions.length + ' Modul Diizinkan'"></span>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </td>
                                <td class="px-4 py-4 font-semibold whitespace-nowrap">
                                    <template x-if="item.unit">
                                        <span class="text-slate-200 inline-flex items-center space-x-1.5 whitespace-nowrap">
                                            <span>🏥</span>
                                            <span x-text="item.unit"></span>
                                        </span>
                                    </template>
                                    <template x-if="!item.unit">
                                        <span class="text-slate-400 font-mono text-[11px] px-2.5 py-1 rounded-lg bg-slate-950/80 border border-slate-800 inline-block whitespace-nowrap">- Non-Unit (Pusat) -</span>
                                    </template>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold whitespace-nowrap"
                                        :class="item.status === 'Aktif' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-slate-500/15 text-slate-400 border border-slate-500/30'"
                                        x-text="item.status || 'Aktif'"></span>
                                </td>
                                
                                <!-- Aksi Frozen/Sticky di Sebelah Kanan -->
                                <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/60 shadow-2xl" style="position: sticky; right: 0; z-index: 10; background-color: #0b1329;">
                                    <div class="inline-flex items-center space-x-1">
                                        <!-- Tombol Detail (Bisa untuk Semua Akun) -->
                                        <button type="button" @click="openDetail(item)"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </button>

                                        <!-- Tombol Ubah (Aktif jika diizinkan) -->
                                        <template x-if="canEditUser(item)">
                                            <button type="button" @click="openEdit(item)"
                                                class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                <span x-text="isSelf(item) ? 'Profil Saya' : 'Ubah'"></span>
                                            </button>
                                        </template>
                                        <template x-if="!canEditUser(item)">
                                            <button type="button" disabled :title="getEditTooltip(item)"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800/40 text-slate-500 border border-slate-800 font-semibold text-xs cursor-not-allowed inline-flex items-center space-x-1 opacity-60">
                                                <span>🔒 Terkunci</span>
                                            </button>
                                        </template>

                                        <!-- Tombol Hapus (Aktif jika Sub Admin / Master Admin) -->
                                        <template x-if="canDeleteUser(item)">
                                            <button type="button" @click="deleteItem(item)"
                                                class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>
                                        </template>
                                        <template x-if="!canDeleteUser(item)">
                                            <button type="button" disabled :title="getDeleteTooltip(item)"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800/40 text-slate-500 border border-slate-800 font-semibold text-xs cursor-not-allowed inline-flex items-center space-x-1 opacity-60">
                                                <span>🔒 Terkunci</span>
                                            </button>
                                        </template>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
