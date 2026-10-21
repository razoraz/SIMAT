        <!-- ========================================================================= -->
        <!-- MODAL UBAH USER (UPDATE)                                                  -->
        <!-- ========================================================================= -->
        <template x-teleport="body">
            <div x-show="showEditModal" class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
                 style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 x-cloak>
                <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto my-auto">
                    <!-- Tombol Close Corner -->
                    <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>

                    <!-- Header Center -->
                    <div class="text-center pb-3 border-b border-slate-800 mb-4">
                        <h3 class="text-base font-extrabold text-white" x-text="isSelf(editFormData) ? '✏️ Ubah Profil Akun Saya' : '✏️ Ubah Data Pengguna'"></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5" x-text="isSelf(editFormData) ? 'Perbarui informasi identitas, email dan NIP akun Anda' : 'Perbarui data penugasan unit, email, dan wewenang akun pengguna'"></p>
                    </div>

                    <form @submit.prevent="saveEdit()" class="space-y-3.5 text-xs">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Nama Lengkap & Gelar <span class="text-rose-400">*</span></label>
                            <input type="text" x-model="editFormData.name" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white focus:border-amber-500 focus:outline-none">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Email Kredensial <span class="text-rose-400">*</span></label>
                                <input type="email" x-model="editFormData.email" required class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-cyan-400 font-mono focus:border-amber-500 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-300 font-semibold mb-1">Role Otorisasi</label>
                                
                                <!-- Jika login Admin: Role terkunci sesuai target akun -->
                                <template x-if="currentUserRole === 'admin'">
                                    <input type="text" :value="editFormData.role === 'admin' ? '🛡️ Admin Operasional (Role Terkunci)' : '🏥 Sub Admin (Role Terkunci)'" readonly class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-3.5 py-2.5 text-slate-300 font-bold cursor-not-allowed">
                                </template>

                                <!-- Jika login Master Admin: Bebas ubah role -->
                                <template x-if="currentUserRole === 'master_admin'">
                                    <select x-model="editFormData.role" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-amber-300 font-bold focus:border-amber-500 focus:outline-none">
                                        <option value="sub_admin">🏥 Sub Admin (User Unit / Ruangan)</option>
                                        <option value="admin">🛡️ Admin Operasional</option>
                                        <option value="master_admin">👑 Master Admin System</option>
                                    </select>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Ubah Password (Opsional)</label>
                            <input type="password" x-model="editFormData.password" placeholder="Kosongkan jika tidak diganti" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white font-mono focus:border-amber-500 focus:outline-none">
                        </div>

                        <template x-if="editFormData.unit">
                            <div class="p-2.5 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-200 flex items-center justify-between text-[11px]">
                                <span class="flex items-center space-x-1.5">
                                    <span>🏥</span>
                                    <span>Unit: <strong class="text-white" x-text="editFormData.unit"></strong></span>
                                </span>
                                <span class="font-mono text-slate-400" x-text="'NIP: ' + (editFormData.nip || '-')"></span>
                            </div>
                        </template>

                        <!-- Bagian Hak Akses Modul Admin Operasional -->
                        <template x-if="editFormData.role === 'admin'">
                            <div class="p-4 bg-slate-950/90 border border-cyan-500/30 rounded-2xl space-y-3 shadow-inner">
                                <!-- Header Kontrol Permissions -->
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                                    <div>
                                        <label class="block text-cyan-300 font-extrabold text-xs flex items-center space-x-2">
                                            <span>🛡️</span>
                                            <span>Wewenang Hak Akses Modul Admin</span>
                                            <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">Hierarchical Tree</span>
                                        </label>
                                        <p class="text-[10px] text-slate-400 mt-0.5" x-text="currentUserRole === 'master_admin' ? 'Atur modul induk & sesuaikan centang sub-aksi untuk akun admin ini:' : 'Daftar modul & sub-aksi yang saat ini diberikan kepada Anda:'"></p>
                                    </div>
                                    <template x-if="currentUserRole === 'master_admin'">
                                        <div class="flex items-center space-x-2 shrink-0">
                                            <button type="button" @click="toggleAllPermissions('edit', true)" class="px-2 py-1 rounded-lg text-[10px] bg-slate-800/80 hover:bg-cyan-500/20 text-cyan-300 border border-slate-700 hover:border-cyan-500/40 font-bold transition-all">
                                                Pilih Semua
                                            </button>
                                            <button type="button" @click="toggleAllPermissions('edit', false)" class="px-2 py-1 rounded-lg text-[10px] bg-slate-800/80 hover:bg-rose-500/20 text-rose-300 border border-slate-700 hover:border-rose-500/40 font-bold transition-all">
                                                Kosongkan
                                            </button>
                                        </div>
                                    </template>
                                </div>

                                <!-- Template Presets Cepat (Hanya Master Admin) -->
                                <template x-if="currentUserRole === 'master_admin'">
                                    <div class="p-2.5 rounded-xl bg-slate-900/80 border border-slate-800 space-y-1.5">
                                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1.5">
                                            <span>⚡</span>
                                            <span>Pilihan Cepat Template Otorisasi:</span>
                                        </span>
                                        <div class="flex flex-wrap gap-1.5">
                                            <template x-for="(preset, pKey) in rolePresets" :key="pKey">
                                                <button type="button" @click="applyPreset('edit', pKey)"
                                                    class="px-2.5 py-1 rounded-lg text-[10px] font-semibold flex items-center space-x-1.5 transition-all border active:scale-95 bg-slate-950 text-slate-300 border-slate-800 hover:border-cyan-500/40 hover:text-white hover:bg-cyan-500/10"
                                                    :title="preset.description">
                                                    <span x-text="preset.icon"></span>
                                                    <span x-text="preset.title"></span>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Tree Daftar Modul & Sub-Aksi (Interactive untuk Master Admin) -->
                                <template x-if="currentUserRole === 'master_admin'">
                                    <div class="space-y-2 max-h-[380px] overflow-y-auto pr-1 custom-scrollbar">
                                        <template x-for="(modDef, modKey) in availablePermissions" :key="modKey">
                                            <div class="rounded-xl border transition-all overflow-hidden"
                                                :class="isModuleEnabled('edit', modKey) ? 'bg-slate-900/80 border-slate-700 shadow-md' : 'bg-slate-950/60 border-slate-800/70 opacity-80'">
                                                
                                                <!-- Baris Header Modul Induk (Parent) -->
                                                <div class="p-2.5 flex items-center justify-between gap-2.5 cursor-pointer select-none"
                                                    @click="toggleExpandModule('edit', modKey)">
                                                    <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                                                        <!-- Tri-State Checkbox Modul Induk -->
                                                        <input type="checkbox"
                                                            :checked="isModuleFullyChecked('edit', modKey)"
                                                            x-effect="$el.indeterminate = isModuleIndeterminate('edit', modKey); $el.checked = isModuleFullyChecked('edit', modKey) || (!isModuleIndeterminate('edit', modKey) && isModuleEnabled('edit', modKey))"
                                                            @click.stop="toggleParentModule('edit', modKey)"
                                                            class="rounded border-slate-700 text-cyan-500 focus:ring-0 focus:ring-offset-0 bg-slate-950 w-4 h-4 cursor-pointer">
                                                        
                                                        <div class="min-w-0 flex-1">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-sm" x-text="modDef.icon"></span>
                                                                <span class="font-extrabold text-xs text-white truncate" x-text="modDef.label"></span>
                                                            </div>
                                                            <p class="text-[9px] text-slate-400 truncate mt-0.5" x-text="modDef.description"></p>
                                                        </div>
                                                    </div>

                                                    <div class="flex items-center space-x-2 shrink-0">
                                                        <!-- Status Summary Pill -->
                                                        <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border"
                                                            :class="{
                                                                'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': getModuleActionSummary('edit', modKey).color === 'emerald',
                                                                'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': getModuleActionSummary('edit', modKey).color === 'cyan',
                                                                'bg-slate-800 text-slate-500 border-slate-700': getModuleActionSummary('edit', modKey).color === 'slate'
                                                            }"
                                                            x-text="getModuleActionSummary('edit', modKey).text"></span>

                                                        <!-- Chevron Expand/Collapse -->
                                                        <button type="button" @click.stop="toggleExpandModule('edit', modKey)"
                                                            class="w-6 h-6 rounded-lg bg-slate-800/60 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-transform"
                                                            :class="isModuleExpanded('edit', modKey) ? 'rotate-180 text-cyan-400' : ''">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                        </button>
                                                    </div>
                                                </div>

                                                <!-- Drawer Sub-Aksi (Collapsible Children) -->
                                                <div x-show="isModuleExpanded('edit', modKey)" x-collapse
                                                    class="border-t border-slate-800/80 bg-slate-950/80 p-3 space-y-2">
                                                    
                                                    <!-- Sub-Header Quick Action -->
                                                    <div class="flex items-center justify-between text-[10px] pb-1.5 border-b border-slate-800/60 text-slate-400">
                                                        <span>Rincian Wewenang Sub-Aksi:</span>
                                                        <div class="flex items-center space-x-2">
                                                            <button type="button" @click="toggleAllActionsInModule('edit', modKey, true)"
                                                                class="text-cyan-400 hover:text-cyan-300 font-semibold hover:underline">Semua Aksi</button>
                                                            <span>•</span>
                                                            <button type="button" @click="toggleAllActionsInModule('edit', modKey, false)"
                                                                class="text-rose-400 hover:text-rose-300 font-semibold hover:underline">Hapus Semua</button>
                                                        </div>
                                                    </div>

                                                    <!-- Grid Sub-Aksi -->
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                                        <template x-for="(actDef, actKey) in modDef.actions" :key="actKey">
                                                            <label class="flex items-start space-x-2.5 p-2 rounded-xl border transition-all cursor-pointer select-none"
                                                                :class="isActionChecked('edit', modKey, actKey) ? 'bg-cyan-500/10 border-cyan-500/40 text-white' : 'bg-slate-900/50 border-slate-800/80 text-slate-400 hover:border-slate-700'">
                                                                
                                                                <input type="checkbox"
                                                                    :checked="isActionChecked('edit', modKey, actKey)"
                                                                    @change="toggleSubAction('edit', modKey, actKey)"
                                                                    class="mt-0.5 rounded border-slate-700 text-cyan-500 focus:ring-0 focus:ring-offset-0 bg-slate-950 w-3.5 h-3.5 cursor-pointer">
                                                                
                                                                <div class="min-w-0 flex-1">
                                                                    <div class="flex items-center space-x-1.5">
                                                                        <span class="font-bold text-[11px] truncate" x-text="actDef.label"></span>
                                                                        <template x-if="actDef.critical">
                                                                            <span class="px-1.5 py-0.2 rounded text-[8px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/30">Kritis</span>
                                                                        </template>
                                                                    </div>
                                                                    <p class="text-[9px] text-slate-400 line-clamp-1 mt-0.5" x-text="actDef.desc"></p>
                                                                </div>
                                                            </label>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Read-only Preview untuk Admin Biasa -->
                                <template x-if="currentUserRole !== 'master_admin'">
                                    <div class="space-y-2 max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
                                        <template x-for="(modDef, modKey) in availablePermissions" :key="modKey">
                                            <div class="p-2.5 rounded-xl border transition-all"
                                                :class="isModuleEnabled('edit', modKey) ? 'bg-slate-900/80 border-slate-700' : 'bg-slate-950/40 border-slate-850 opacity-60'">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2">
                                                        <span x-text="modDef.icon"></span>
                                                        <span class="font-bold text-xs text-white" x-text="modDef.label"></span>
                                                    </div>
                                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold border"
                                                        :class="isModuleEnabled('edit', modKey) ? 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30' : 'bg-slate-800 text-slate-500 border-slate-700'"
                                                        x-text="getModuleActionSummary('edit', modKey).text"></span>
                                                </div>

                                                <template x-if="isModuleEnabled('edit', modKey)">
                                                    <div class="flex flex-wrap gap-1 mt-2 pt-2 border-t border-slate-800/60">
                                                        <template x-for="(actDef, actKey) in modDef.actions" :key="actKey">
                                                            <span class="px-2 py-0.5 rounded-md text-[9px] font-medium border flex items-center space-x-1"
                                                                :class="isActionChecked('edit', modKey, actKey) ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : 'bg-slate-950 text-slate-600 border-slate-800 line-through opacity-50'">
                                                                <span x-text="isActionChecked('edit', modKey, actKey) ? '✓' : '✗'"></span>
                                                                <span x-text="actDef.label"></span>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Deskripsi Tugas / Penugasan</label>
                            <input type="text" x-model="editFormData.penugasan" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white focus:border-amber-500 focus:outline-none">
                        </div>

                        <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                            <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                <span>Batal</span>
                            </button>
                            <button type="submit" :disabled="isSaving" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-1.5 active:scale-95 disabled:opacity-50">
                                <svg x-show="!isSaving" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                <span x-text="isSaving ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
