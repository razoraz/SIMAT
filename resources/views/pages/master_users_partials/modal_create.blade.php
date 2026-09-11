        <!-- ========================================================================= -->
        <!-- MODAL TAMBAH USER (CREATE)                                                -->
        <!-- ========================================================================= -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative max-h-[90vh] overflow-y-auto">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showAddModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

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
