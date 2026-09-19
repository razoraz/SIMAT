        <!-- ========================================================================= -->
        <!-- MODAL UBAH USER (UPDATE)                                                  -->
        <!-- ========================================================================= -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white" x-text="isSelf(editFormData) ? '✏️ Ubah Profil Akun Saya' : '✏️ Ubah Data Pengguna'"></h3>
                    <p class="text-[11px] text-slate-400 mt-0.5" x-text="isSelf(editFormData) ? 'Perbarui informasi identitas, email dan NIP akun Anda' : 'Perbarui data penugasan unit, email, dan status aktif pengguna'"></p>
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

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Status Akun</label>
                            <select x-model="editFormData.status" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-emerald-400 font-bold focus:border-amber-500 focus:outline-none">
                                <option value="Aktif">Aktif</option>
                                <option value="Nonaktif">Nonaktif</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Ubah Password (Opsional)</label>
                            <input type="password" x-model="editFormData.password" placeholder="Kosongkan jika tidak diganti" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white font-mono focus:border-amber-500 focus:outline-none">
                        </div>
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
                        <div class="p-3.5 bg-slate-950/90 border border-cyan-500/30 rounded-2xl space-y-2.5">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <div>
                                    <label class="block text-cyan-300 font-bold text-xs flex items-center space-x-1.5">
                                        <span>🛡️</span>
                                        <span>Wewenang Hak Akses Modul Admin</span>
                                    </label>
                                    <p class="text-[10px] text-slate-400" x-text="currentUserRole === 'master_admin' ? 'Tentukan modul yang diizinkan untuk admin ini:' : 'Daftar modul yang saat ini diberikan kepada Anda:'"></p>
                                </div>
                                <template x-if="currentUserRole === 'master_admin'">
                                    <div class="flex items-center space-x-2">
                                        <button type="button" @click="toggleAllEditPermissions(true)" class="text-[10px] text-cyan-400 hover:text-cyan-300 font-bold hover:underline">Pilih Semua</button>
                                        <span class="text-slate-600">•</span>
                                        <button type="button" @click="toggleAllEditPermissions(false)" class="text-[10px] text-rose-400 hover:text-rose-300 font-bold hover:underline">Kosongkan</button>
                                    </div>
                                </template>
                            </div>

                            <!-- Interactive Checkbox untuk Master Admin -->
                            <template x-if="currentUserRole === 'master_admin'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 pt-1">
                                    <template x-for="(perm, key) in availablePermissions" :key="key">
                                        <label class="flex items-start space-x-2.5 p-2.5 rounded-xl border transition-all cursor-pointer select-none"
                                            :class="editFormData.permissions.includes(key) ? 'bg-cyan-500/10 border-cyan-500/40 text-white' : 'bg-slate-900/60 border-slate-800 text-slate-400 hover:border-slate-700'">
                                            <input type="checkbox" :value="key" x-model="editFormData.permissions" class="mt-0.5 rounded border-slate-700 text-cyan-500 focus:ring-0 focus:ring-offset-0 bg-slate-950">
                                            <div class="min-w-0 flex-1">
                                                <div class="flex items-center space-x-1.5">
                                                    <span x-text="perm.icon"></span>
                                                    <span class="font-bold text-[11px] truncate" x-text="perm.label"></span>
                                                </div>
                                                <p class="text-[9px] text-slate-400 line-clamp-1 mt-0.5" x-text="perm.description"></p>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </template>

                            <!-- Read-only Pills untuk Admin Operasional biasa -->
                            <template x-if="currentUserRole !== 'master_admin'">
                                <div class="flex flex-wrap gap-1.5 pt-1">
                                    <template x-for="(perm, key) in availablePermissions" :key="key">
                                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-semibold flex items-center space-x-1 border"
                                            :class="editFormData.permissions.includes(key) ? 'bg-cyan-500/15 text-cyan-300 border-cyan-500/40' : 'bg-slate-900 text-slate-500 border-slate-800 opacity-60 line-through'">
                                            <span x-text="perm.icon"></span>
                                            <span x-text="perm.label"></span>
                                        </span>
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
