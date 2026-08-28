<x-layout title="Manajemen Pengguna - SIMAT-RK">
    @section('page-title', 'Manajemen Pengguna')
    @section('breadcrumb', 'Master Data System / Manajemen Pengguna')

    <script>
        function userManager() {
            return {
                searchQuery: '',
                roleFilter: 'all',
                showAddModal: false,
                showEditModal: false,
                showDetailModal: false,
                selectedUser: null,
                isSaving: false,

                // Sesi Pengguna Aktif
                currentUserRole: '{{ Auth::user()->role ?? "admin" }}',
                currentUserId: {{ Auth::user()->id ?? 2 }},
                currentUserEmail: '{{ Auth::user()->email ?? "admin@asimat.com" }}',
                csrfToken: '{{ csrf_token() }}',

                newFormData: {
                    name: '',
                    nip: '',
                    email: '',
                    role: 'sub_admin',
                    unit: 'Pav. Anggrek',
                    penugasan: '',
                    password: '',
                    status: 'Aktif'
                },

                editFormData: {
                    id: null,
                    name: '',
                    nip: '',
                    email: '',
                    role: '',
                    unit: '',
                    penugasan: '',
                    password: '',
                    status: ''
                },

                // Data Users & Units dari Database Backend
                users: @json($users ?? []),
                unitsList: @json($units ?? []),

                init() {
                    if (this.unitsList && this.unitsList.length > 0) {
                        this.newFormData.unit = this.unitsList[0];
                    }
                },

                // Cek apakah user target adalah akun diri sendiri
                isSelf(targetUser) {
                    if (!targetUser) return false;
                    return targetUser.id === this.currentUserId || targetUser.email === this.currentUserEmail;
                },

                // Cek hak akses untuk Mengubah Data (Edit)
                canEditUser(targetUser) {
                    if (!targetUser) return false;
                    if (this.currentUserRole === 'master_admin') return true;

                    if (this.currentUserRole === 'admin') {
                        if (targetUser.role === 'master_admin') return false;
                        if (targetUser.role === 'admin') {
                            return this.isSelf(targetUser);
                        }
                        if (targetUser.role === 'sub_admin') return true;
                    }

                    return false;
                },

                // Cek hak akses untuk Menghapus Data (Delete)
                canDeleteUser(targetUser) {
                    if (!targetUser) return false;
                    if (this.currentUserRole === 'master_admin') {
                        return !this.isSelf(targetUser);
                    }

                    if (this.currentUserRole === 'admin') {
                        if (targetUser.role === 'master_admin' || targetUser.role === 'admin') return false;
                        return targetUser.role === 'sub_admin';
                    }

                    return false;
                },

                getEditTooltip(targetUser) {
                    if (this.canEditUser(targetUser)) return '';
                    if (targetUser.role === 'master_admin') return '🔒 Akun Master Admin diproteksi khusus (Hanya Master Admin yang dapat mengubah)';
                    if (targetUser.role === 'admin' && !this.isSelf(targetUser)) return '🔒 Admin tidak diizinkan mengubah akun Admin lain';
                    return 'Akses dibatasi';
                },

                getDeleteTooltip(targetUser) {
                    if (this.canDeleteUser(targetUser)) return '';
                    if (targetUser.role === 'master_admin') return '🔒 Akun Master Admin tidak dapat dihapus';
                    if (targetUser.role === 'admin') return '🔒 Admin tidak diizinkan menghapus akun Admin';
                    return 'Akses dibatasi';
                },

                get filteredUsers() {
                    const query = (this.searchQuery || '').toLowerCase();
                    const roleWeight = { 'master_admin': 1, 'admin': 2, 'sub_admin': 3 };

                    return this.users
                        .filter(item => {
                            const matchSearch = (item.name || '').toLowerCase().includes(query) ||
                                                (item.email || '').toLowerCase().includes(query) ||
                                                (item.nip || '').toLowerCase().includes(query) ||
                                                (item.unit || '').toLowerCase().includes(query) ||
                                                (item.penugasan || '').toLowerCase().includes(query);

                            const matchRole = this.roleFilter === 'all' || item.role === this.roleFilter;
                            return matchSearch && matchRole;
                        })
                        .sort((a, b) => {
                            const weightA = roleWeight[a.role] || 99;
                            const weightB = roleWeight[b.role] || 99;
                            if (weightA !== weightB) {
                                return weightA - weightB;
                            }
                            return (a.id || 0) - (b.id || 0);
                        });
                },

                get countMasterAdmin() {
                    return this.users.filter(u => u.role === 'master_admin').length;
                },

                get countAdmin() {
                    return this.users.filter(u => u.role === 'admin').length;
                },

                get countSubAdmin() {
                    return this.users.filter(u => u.role === 'sub_admin').length;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.roleFilter = 'all';
                },

                openDetail(item) {
                    this.selectedUser = item;
                    this.showDetailModal = true;
                },

                openAddModal() {
                    this.newFormData = {
                        name: '',
                        nip: '',
                        email: '',
                        role: 'sub_admin',
                        unit: this.unitsList && this.unitsList.length > 0 ? this.unitsList[0] : 'Pav. Anggrek',
                        penugasan: '',
                        password: '',
                        status: 'Aktif'
                    };
                    this.showAddModal = true;
                },

                openEdit(item) {
                    if (!this.canEditUser(item)) {
                        this.showToast('⛔ Akses Ditolak: ' + this.getEditTooltip(item), 'error');
                        return;
                    }
                    this.editFormData = { 
                        id: item.id,
                        name: item.name || '',
                        nip: item.nip || '',
                        email: item.email || '',
                        role: item.role || 'sub_admin',
                        unit: item.unit || '',
                        penugasan: item.penugasan || '',
                        password: '',
                        status: item.status || 'Aktif'
                    };
                    this.showEditModal = true;
                },

                // 1. Simpan Pengguna Baru ke Backend (CREATE)
                async saveUser() {
                    if (!this.newFormData.name || !this.newFormData.email || !this.newFormData.password) {
                        this.showToast('⚠️ Harap lengkapi Nama Lengkap, Email, dan Password!', 'warning');
                        return;
                    }

                    if (this.currentUserRole === 'admin' && this.newFormData.role !== 'sub_admin') {
                        this.showToast('⛔ Admin Operasional hanya diizinkan menambah akun Sub Admin!', 'error');
                        this.newFormData.role = 'sub_admin';
                        return;
                    }

                    this.askConfirmation({
                        title: '➕ Konfirmasi Tambah Akun Pengguna',
                        message: 'Apakah Anda yakin ingin mendaftarkan akun pengguna baru ini ke dalam sistem?',
                        itemName: this.newFormData.name + ' (' + this.newFormData.email + ')',
                        type: 'success',
                        btnText: '➕ Ya, Daftarkan Akun',
                        onConfirm: async () => {
                            this.isSaving = true;
                            try {
                                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || this.csrfToken;
                                const response = await fetch('{{ route("master.users.store") }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        _token: token,
                                        ...this.newFormData
                                    })
                                });

                                const res = await response.json();
                                if (response.ok && res.success) {
                                    this.users.unshift(res.user);
                                    this.showAddModal = false;
                                    this.showToast('✅ ' + res.message, 'success');
                                } else {
                                    this.showToast('⚠️ ' + (res.message || 'Gagal mendaftarkan akun baru.'), 'error');
                                }
                            } catch (err) {
                                console.error(err);
                                this.showToast('❌ Terjadi kesalahan server saat mendaftarkan akun.', 'error');
                            } finally {
                                this.isSaving = false;
                            }
                        }
                    });
                },

                // 2. Simpan Perubahan ke Backend (UPDATE)
                saveEdit() {
                    if (!this.editFormData.name || !this.editFormData.email) {
                        this.showToast('⚠️ Harap lengkapi Nama Lengkap dan Email pengguna!', 'warning');
                        return;
                    }

                    this.askConfirmation({
                        title: '✏️ Konfirmasi Simpan Perubahan Akun',
                        message: 'Apakah Anda yakin ingin menyimpan perubahan data pengguna ini?',
                        itemName: this.editFormData.name + ' (' + this.editFormData.email + ')',
                        type: 'warning',
                        btnText: '✏️ Ya, Simpan Perubahan',
                        onConfirm: async () => {
                            this.isSaving = true;
                            try {
                                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || this.csrfToken;
                                const response = await fetch('/master-data/users/' + this.editFormData.id, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        _token: token,
                                        ...this.editFormData
                                    })
                                });

                                const res = await response.json();
                                if (response.ok && res.success) {
                                    const index = this.users.findIndex(i => i.id === this.editFormData.id);
                                    if (index !== -1) {
                                        this.users[index] = { ...res.user };
                                    }
                                    if (this.selectedUser && this.selectedUser.id === this.editFormData.id) {
                                        this.selectedUser = { ...res.user };
                                    }
                                    this.showEditModal = false;
                                    this.showToast('✅ ' + res.message, 'success');
                                } else {
                                    this.showToast('⚠️ ' + (res.message || 'Gagal menyimpan perubahan.'), 'error');
                                }
                            } catch (err) {
                                console.error(err);
                                this.showToast('❌ Terjadi kesalahan server saat memperbarui akun.', 'error');
                            } finally {
                                this.isSaving = false;
                            }
                        }
                    });
                },

                // 3. Hapus Pengguna dari Backend (DELETE)
                deleteItem(item) {
                    if (!this.canDeleteUser(item)) {
                        this.showToast('⛔ Akses Ditolak: ' + this.getDeleteTooltip(item), 'error');
                        return;
                    }

                    this.askConfirmation({
                        title: '🗑️ Konfirmasi Hapus Akun Pengguna',
                        message: 'Apakah Anda yakin ingin menghapus akun pengguna ini secara permanen dari sistem SIMAT-RK?',
                        itemName: item.name + ' (' + (item.unit || item.role) + ')',
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Akun',
                        onConfirm: async () => {
                            try {
                                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || this.csrfToken;
                                const response = await fetch('/master-data/users/' + item.id, {
                                    method: 'DELETE',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        _token: token
                                    })
                                });

                                const res = await response.json();
                                if (response.ok && res.success) {
                                    this.users = this.users.filter(i => i.id !== item.id);
                                    if (this.selectedUser && this.selectedUser.id === item.id) {
                                        this.showDetailModal = false;
                                    }
                                    this.showToast('🗑️ ' + res.message, 'success');
                                } else {
                                    this.showToast('⚠️ ' + (res.message || 'Gagal menghapus akun pengguna.'), 'error');
                                }
                            } catch (err) {
                                console.error(err);
                                this.showToast('❌ Terjadi kesalahan server saat menghapus akun.', 'error');
                            }
                        }
                    });
                },

                showConfirmModal: false,
                confirmData: {
                    title: 'Konfirmasi Tindakan',
                    message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                    itemName: '',
                    type: 'danger',
                    btnText: 'Ya, Lanjutkan',
                    onConfirm: null
                },

                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                askConfirmation({ title, message, itemName, type = 'danger', btnText, onConfirm }) {
                    this.confirmData = {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        type: type,
                        btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan')),
                        onConfirm: onConfirm
                    };
                    this.showConfirmModal = true;
                },

                executeConfirmedAction() {
                    if (typeof this.confirmData.onConfirm === 'function') {
                        this.confirmData.onConfirm();
                    }
                    this.showConfirmModal = false;
                },

                showToast(message, type = 'success') {
                    this.toast = { show: true, message: message, type: type };
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },

                // 4. Reset Password ke Default 'rsud123'
                resetPasswordAction(item) {
                    if (!item) return;
                    this.askConfirmation({
                        title: '🔑 Konfirmasi Reset Password',
                        message: 'Apakah Anda yakin ingin mengembalikan password akun ini ke password default "rsud123"?',
                        itemName: item.name + ' (' + item.email + ')',
                        type: 'warning',
                        btnText: '🔑 Ya, Reset Password',
                        onConfirm: async () => {
                            try {
                                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || this.csrfToken;
                                const response = await fetch('/master-data/users/' + item.id + '/reset-password', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'Accept': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'X-Requested-With': 'XMLHttpRequest'
                                    },
                                    body: JSON.stringify({
                                        _token: token
                                    })
                                });

                                const res = await response.json();
                                if (response.ok && res.success) {
                                    this.showToast('🔑 ' + res.message, 'success');
                                } else {
                                    this.showToast('⚠️ ' + (res.message || 'Gagal mereset password.'), 'error');
                                }
                            } catch (err) {
                                console.error(err);
                                this.showToast('❌ Terjadi kesalahan server saat mereset password.', 'error');
                            }
                        }
                    });
                }
            };
        }
    </script>

    <div x-data="userManager()" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-amber-600/15 via-slate-900 to-slate-900 border border-amber-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
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

        <!-- Filter & Search Toolbar -->
        <div class="bg-slate-900/80 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6 space-y-4">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                
                <!-- Filter Kategori Role -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <button type="button" @click="roleFilter = 'all'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                        :class="roleFilter === 'all' ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'">
                        Semua Role (<span x-text="users.length"></span>)
                    </button>
                    <button type="button" @click="roleFilter = 'master_admin'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                        :class="roleFilter === 'master_admin' ? 'bg-amber-500 text-slate-950 shadow-md shadow-amber-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'">
                        👑 Master Admin (<span x-text="countMasterAdmin"></span>)
                    </button>
                    <button type="button" @click="roleFilter = 'admin'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                        :class="roleFilter === 'admin' ? 'bg-cyan-500 text-slate-950 shadow-md shadow-cyan-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'">
                        🛡️ Admin (<span x-text="countAdmin"></span>)
                    </button>
                    <button type="button" @click="roleFilter = 'sub_admin'"
                        class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                        :class="roleFilter === 'sub_admin' ? 'bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'">
                        🏥 Sub Admin (<span x-text="countSubAdmin"></span>)
                    </button>
                </div>

                <!-- Input Pencarian -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama pegawai / email kredensial / NIP / unit penugasan..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-all">
                        <svg class="w-4 h-4 text-amber-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-amber-400 font-bold" x-text="filteredUsers.length"></span> dari <span class="text-white font-bold" x-text="users.length"></span> Pengguna
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Users -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-5 mb-6">
            <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 350px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 border-collapse">
                    <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                        <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-left">Nama & NIP Pegawai</th>
                        <th class="px-4 py-3.5 text-left">Email Kredensial</th>
                        <th class="px-4 py-3.5 text-center">Role Otorisasi</th>
                        <th class="px-4 py-3.5 text-left">Unit Penugasan</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredUsers" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 rounded-xl flex items-center justify-center font-bold text-xs shrink-0"
                                         :class="{
                                             'bg-amber-500/20 text-amber-300 border border-amber-500/30': item.role === 'master_admin',
                                             'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': item.role === 'admin',
                                             'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': item.role === 'sub_admin'
                                         }"
                                         x-text="(item.name || 'U').substring(0, 1)"></div>
                                    <div>
                                        <div class="flex items-center space-x-1.5">
                                            <span class="font-bold text-white text-sm" x-text="item.name"></span>
                                            <span x-show="isSelf(item)" class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">Saya</span>
                                        </div>
                                        <div class="text-[10px] text-slate-400 font-mono" x-text="'NIP: ' + (item.nip || '-')"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 font-mono font-semibold"
                                :class="item.role === 'master_admin' ? 'text-amber-300' : (item.role === 'admin' ? 'text-cyan-300' : 'text-emerald-300')"
                                x-text="item.email"></td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                    :class="{
                                        'bg-amber-500/20 text-amber-300 border-amber-500/30': item.role === 'master_admin',
                                        'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': item.role === 'admin',
                                        'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': item.role === 'sub_admin'
                                    }">
                                    <span class="whitespace-nowrap" x-text="item.role === 'master_admin' ? '👑 Master Admin' : (item.role === 'admin' ? '🛡️ Admin Operasional' : '🏥 Sub Admin Unit')"></span>
                                </span>
                            </td>
                            <td class="px-4 py-4 font-semibold">
                                <template x-if="item.unit">
                                    <span class="text-slate-200 flex items-center space-x-1.5">
                                        <span>🏥</span>
                                        <span x-text="item.unit"></span>
                                    </span>
                                </template>
                                <template x-if="!item.unit">
                                    <span class="text-slate-400 font-mono text-[11px] px-2 py-0.5 rounded-lg bg-slate-950/80 border border-slate-800">
                                        - Non-Unit (Pusat) -
                                    </span>
                                </template>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                                    :class="item.status === 'Aktif' ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-slate-500/15 text-slate-400 border border-slate-500/30'"
                                    x-text="item.status || 'Aktif'"></span>
                            </td>
                            
                            <!-- Aksi dengan Granular Role Permission -->
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                
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
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL DETAIL USER                                                         -->
        <!-- ========================================================================= -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showDetailModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-sm font-bold transition-all">&times;</button>

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

        <!-- GLOBAL CUSTOM CONFIRMATION DIALOG MODAL (Sleek Dark Theme) -->
        <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div @click.away="showConfirmModal = false"
                 x-show="showConfirmModal"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-slate-900 border rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative"
                 :class="{
                     'border-rose-500/40': confirmData.type === 'danger',
                     'border-amber-500/40': confirmData.type === 'warning',
                     'border-emerald-500/40': confirmData.type === 'success',
                     'border-cyan-500/40': confirmData.type === 'info'
                 }">
                
                <!-- Header Icon & Title -->
                <div class="flex items-start space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 font-bold border"
                         :class="{
                             'bg-rose-500/20 text-rose-400 border-rose-500/30': confirmData.type === 'danger',
                             'bg-amber-500/20 text-amber-300 border-amber-500/30': confirmData.type === 'warning',
                             'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': confirmData.type === 'success',
                             'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': confirmData.type === 'info'
                         }">
                        <span x-text="confirmData.type === 'danger' ? '🗑️' : (confirmData.type === 'warning' ? '✏️' : '➕')"></span>
                    </div>
                    <div class="space-y-1 min-w-0 flex-1">
                        <h3 class="text-base font-extrabold text-white leading-snug" x-text="confirmData.title"></h3>
                        <p class="text-slate-300 text-xs leading-relaxed" x-text="confirmData.message"></p>
                    </div>
                </div>

                <!-- Item Target Preview Card -->
                <template x-if="confirmData.itemName">
                    <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Item Target:</span>
                        <p class="text-xs font-bold text-cyan-300 truncate font-mono" x-text="confirmData.itemName"></p>
                    </div>
                </template>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmedAction()"
                        class="px-5 py-2.5 rounded-xl font-extrabold text-xs shadow-lg transition-all active:scale-95 cursor-pointer flex items-center space-x-1.5"
                        :class="{
                            'bg-rose-500 hover:bg-rose-400 text-white shadow-rose-500/20': confirmData.type === 'danger',
                            'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/20': confirmData.type === 'warning',
                            'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/20': confirmData.type === 'success',
                            'bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-cyan-500/20': confirmData.type === 'info'
                        }">
                        <span x-text="confirmData.btnText"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- GLOBAL FLOATING TOAST NOTIFICATION POPUP -->
        <div x-show="toast.show" x-cloak
             x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4 scale-95"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform opacity-100 translate-y-0 scale-100"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="fixed bottom-6 right-6 z-50 max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3"
             :class="{
                 'border-emerald-500/40 text-emerald-300': toast.type === 'success',
                 'border-rose-500/40 text-rose-300': toast.type === 'error',
                 'border-amber-500/40 text-amber-300': toast.type === 'warning',
                 'border-cyan-500/40 text-cyan-300': toast.type === 'info'
             }">
            <div class="flex items-center space-x-2.5 min-w-0">
                <span class="text-base shrink-0" x-text="toast.type === 'success' ? '✅' : (toast.type === 'error' ? '⚠️' : 'ℹ️')"></span>
                <p class="text-xs font-bold leading-snug truncate" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white text-base font-bold shrink-0">&times;</button>
        </div>

    </div>
</x-layout>
