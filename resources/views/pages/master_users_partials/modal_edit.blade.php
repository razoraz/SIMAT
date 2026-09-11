        <!-- ========================================================================= -->
        <!-- MODAL UBAH USER (UPDATE)                                                  -->
        <!-- ========================================================================= -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

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
