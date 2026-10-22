        <!-- ========================================================================= -->
        <!-- MODAL TAMBAH USER (CREATE)                                                -->
        <!-- ========================================================================= -->
<template x-teleport="body">
    <div x-show="showAddModal" 
         class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-4 overflow-y-auto"
         style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); z-index: 99999;"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto my-auto"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95">
            <!-- Tombol Close Corner -->
            <button type="button" @click="showAddModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>

            <!-- Header Center -->
            <div class="text-center pb-3 border-b border-slate-800 mb-4">
                <h3 class="text-base font-extrabold text-white" x-text="currentUserRole === 'admin' ? 'Tambah Akun Sub Admin (Unit/Paviliun)' : 'Tambah Pengguna Baru'"></h3>
                <p class="text-[11px] text-slate-400 mt-0.5" x-text="currentUserRole === 'admin' ? 'Pendaftaran Akun Kepala Ruangan & Penanggung Jawab Inventaris Unit' : 'Registrasi Akun Pegawai & Pengaturan Role Otorisasi'"></p>
            </div>

            <!-- Info Notice untuk Admin Operasional -->
            <template x-if="currentUserRole === 'admin'">
                <div class="p-3 bg-cyan-500/10 border border-cyan-500/30 rounded-2xl mb-3 flex items-start space-x-2.5 text-xs text-cyan-200">
                    <span class="text-base">ℹ️</span>
                    <div>
                        <span class="font-bold block">Wewenang Admin:</span>
                        <span>Anda berwenang mendaftarkan akun <strong>Sub Admin (Kepala Ruangan / Paviliun)</strong>. Penambahan akun Admin & Master Admin hanya dapat dilakukan oleh Master Admin.</span>
                    </div>
                </div>
            </template>

            <form @submit.prevent="saveNew()" class="space-y-3.5 text-xs">
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Nama Lengkap & Gelar <span class="text-rose-400">*</span></label>
                    <input type="text" x-model="newFormData.name" required placeholder="Nama Pegawai RSUD..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white focus:border-amber-500 focus:outline-none">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Email Kredensial (Login) <span class="text-rose-400">*</span></label>
                        <input type="email" x-model="newFormData.email" required placeholder="pegawai@gmail.com" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-cyan-400 font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Password (Default: rsud123)</label>
                        <input type="text" x-model="newFormData.password" placeholder="Kosongkan untuk pakai 'rsud123'" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white font-mono focus:border-amber-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Role Otorisasi</label>
                    
                    <!-- Jika login Admin: Dropdown terkunci ke sub_admin -->
                    <template x-if="currentUserRole === 'admin'">
                        <input type="text" value="🏥 Sub Admin (User Unit / Ruangan)" readonly class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-3.5 py-2.5 text-emerald-400 font-bold cursor-not-allowed">
                    </template>

                    <!-- Jika login Master Admin: Bebas pilih role -->
                    <template x-if="currentUserRole === 'master_admin'">
                        <select x-model="newFormData.role" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-amber-300 font-bold focus:border-amber-500 focus:outline-none">
                            <option value="sub_admin">🏥 Sub Admin (User Unit / Ruangan)</option>
                            <option value="admin">🛡️ Admin Operasional</option>
                            <option value="master_admin">👑 Master Admin System</option>
                        </select>
                    </template>
                </div>

                <!-- Dropdown Pemilihan Unit / Ruangan Penugasan (Khusus Sub Admin) -->
                <template x-if="newFormData.role === 'sub_admin'">
                    <div class="p-3 bg-slate-950/80 border border-emerald-500/30 rounded-2xl space-y-1.5 shadow-inner">
                        <label class="block text-emerald-300 font-extrabold text-xs flex items-center space-x-2">
                            <span>🏥</span>
                            <span>Unit / Ruangan Penugasan</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <select x-model="newFormData.unit_id" @change="const sel = unitsList.find(u => u.id == newFormData.unit_id); newFormData.unit = sel ? sel.nama : ''; newFormData.nip = sel ? (sel.nip || '') : ''; if (sel && !newFormData.penugasan) { newFormData.penugasan = 'Penanggung Jawab Inventaris ' + sel.nama; }" required class="w-full bg-slate-900 border border-slate-700 hover:border-emerald-500 rounded-xl px-3.5 py-2.5 text-emerald-300 font-semibold text-xs focus:border-emerald-500 focus:outline-none transition-all cursor-pointer">
                            <option value="" class="bg-slate-950 text-slate-400">-- Pilih Ruangan / Paviliun Penugasan --</option>
                            <template x-for="u in unitsList" :key="u.id">
                                <option :value="u.id" class="bg-slate-950 text-white" x-text="u.nama + (u.tipe ? ' (' + u.tipe + ')' : '')"></option>
                            </template>
                        </select>
                        <p class="text-[10px] text-slate-400">Akun Sub Admin akan otomatis mengelola inventaris, KIR, dan verifikasi distribusi untuk unit yang dipilih.</p>
                    </div>
                </template>

                <!-- Bagian Hak Akses Modul (Hanya Tampil Jika Role: Admin Operasional & Login: Master Admin) -->
                <template x-if="newFormData.role === 'admin' && currentUserRole === 'master_admin'">
                    <div class="p-4 bg-slate-950/90 border border-cyan-500/30 rounded-2xl space-y-3 shadow-inner">
                        <!-- Header Kontrol Permissions -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                            <div>
                                <label class="block text-cyan-300 font-extrabold text-xs flex items-center space-x-2">
                                    <span>🛡️</span>
                                    <span>Hak Akses Modul & Sub-Fitur Admin</span>
                                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">Hierarchical Tree</span>
                                </label>
                                <p class="text-[10px] text-slate-400 mt-0.5">Centang modul induk untuk otomatis aktifkan sub-aksi, atau sesuaikan opsi detailnya:</p>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <button type="button" @click="toggleAllPermissions('new', true)" class="px-2 py-1 rounded-lg text-[10px] bg-slate-800/80 hover:bg-cyan-500/20 text-cyan-300 border border-slate-700 hover:border-cyan-500/40 font-bold transition-all">
                                    Pilih Semua
                                </button>
                                <button type="button" @click="toggleAllPermissions('new', false)" class="px-2 py-1 rounded-lg text-[10px] bg-slate-800/80 hover:bg-rose-500/20 text-rose-300 border border-slate-700 hover:border-rose-500/40 font-bold transition-all">
                                    Kosongkan
                                </button>
                            </div>
                        </div>


                        <!-- Tree Daftar Modul & Sub-Aksi -->
                        <div class="space-y-2 max-h-[380px] overflow-y-auto pr-1 custom-scrollbar">
                            <template x-for="(modDef, modKey) in availablePermissions" :key="modKey">
                                <div class="rounded-xl border transition-all overflow-hidden"
                                    :class="isModuleEnabled('new', modKey) ? 'bg-slate-900/80 border-slate-700 shadow-md' : 'bg-slate-950/60 border-slate-800/70 opacity-80'">
                                    
                                    <!-- Baris Header Modul Induk (Parent) -->
                                    <div class="p-2.5 flex items-center justify-between gap-2.5 cursor-pointer select-none"
                                        @click="toggleExpandModule('new', modKey)">
                                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                                            <!-- Tri-State Checkbox Modul Induk -->
                                            <input type="checkbox"
                                                :checked="isModuleFullyChecked('new', modKey)"
                                                x-effect="$el.indeterminate = isModuleIndeterminate('new', modKey); $el.checked = isModuleFullyChecked('new', modKey) || (!isModuleIndeterminate('new', modKey) && isModuleEnabled('new', modKey))"
                                                @click.stop="toggleParentModule('new', modKey)"
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
                                                    'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': getModuleActionSummary('new', modKey).color === 'emerald',
                                                    'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': getModuleActionSummary('new', modKey).color === 'cyan',
                                                    'bg-slate-800 text-slate-500 border-slate-700': getModuleActionSummary('new', modKey).color === 'slate'
                                                }"
                                                x-text="getModuleActionSummary('new', modKey).text"></span>

                                            <!-- Chevron Expand/Collapse -->
                                            <button type="button" @click.stop="toggleExpandModule('new', modKey)"
                                                class="w-6 h-6 rounded-lg bg-slate-800/60 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-transform"
                                                :class="isModuleExpanded('new', modKey) ? 'rotate-180 text-cyan-400' : ''">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Drawer Sub-Aksi (Collapsible Children) -->
                                    <div x-show="isModuleExpanded('new', modKey)" x-collapse
                                        class="border-t border-slate-800/80 bg-slate-950/80 p-3 space-y-2">
                                        
                                        <!-- Sub-Header Quick Action -->
                                        <div class="flex items-center justify-between text-[10px] pb-1.5 border-b border-slate-800/60 text-slate-400">
                                            <span>Rincian Wewenang Sub-Aksi:</span>
                                            <div class="flex items-center space-x-2">
                                                <button type="button" @click="toggleAllActionsInModule('new', modKey, true)"
                                                    class="text-cyan-400 hover:text-cyan-300 font-semibold hover:underline">Semua Aksi</button>
                                                <span>•</span>
                                                <button type="button" @click="toggleAllActionsInModule('new', modKey, false)"
                                                    class="text-rose-400 hover:text-rose-300 font-semibold hover:underline">Hapus Semua</button>
                                            </div>
                                        </div>

                                        <!-- Grid Sub-Aksi -->
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                            <template x-for="(actDef, actKey) in modDef.actions" :key="actKey">
                                                <label class="flex items-start space-x-2.5 p-2 rounded-xl border transition-all cursor-pointer select-none"
                                                    :class="isActionChecked('new', modKey, actKey) ? 'bg-cyan-500/10 border-cyan-500/40 text-white' : 'bg-slate-900/50 border-slate-800/80 text-slate-400 hover:border-slate-700'">
                                                    
                                                    <input type="checkbox"
                                                        :checked="isActionChecked('new', modKey, actKey)"
                                                        @change="toggleSubAction('new', modKey, actKey)"
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
                    </div>
                </template>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Deskripsi Tugas / Catatan Jabatan</label>
                    <input type="text" x-model="newFormData.penugasan" placeholder="Contoh: Admin Operasional Sarpras / Sub Admin..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white focus:border-amber-500 focus:outline-none">
                </div>

                <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                    <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Batal</span>
                    </button>
                    <button type="submit" :disabled="isSaving" class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-1.5 active:scale-95 disabled:opacity-50">
                        <svg x-show="!isSaving" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="isSaving ? 'Menyimpan...' : 'Register Pengguna'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
