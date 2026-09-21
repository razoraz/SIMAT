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
                    role: 'admin',
                    unit: 'Pav. Anggrek',
                    penugasan: '',
                    permissions: ['astap', 'distribusi', 'bast', 'mutasi', 'unit', 'master_data'],
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
                    permissions: [],
                    password: '',
                    status: ''
                },

                // Daftar Definisi Hak Akses Modul dari Backend
                availablePermissions: @json($availablePermissions ?? \App\Models\User::AVAILABLE_PERMISSIONS),

                // Helper Checklist Permissions
                toggleAllNewPermissions(select) {
                    if (select) {
                        this.newFormData.permissions = Object.keys(this.availablePermissions);
                    } else {
                        this.newFormData.permissions = [];
                    }
                },

                toggleAllEditPermissions(select) {
                    if (select) {
                        this.editFormData.permissions = Object.keys(this.availablePermissions);
                    } else {
                        this.editFormData.permissions = [];
                    }
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
                        role: this.currentUserRole === 'master_admin' ? 'admin' : 'sub_admin',
                        unit: this.unitsList && this.unitsList.length > 0 ? this.unitsList[0] : 'Pav. Anggrek',
                        penugasan: '',
                        permissions: ['astap', 'distribusi', 'bast', 'mutasi', 'unit', 'master_data'],
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

                    // Inisialisasi permissions: jika null/array kosong pada role admin, defaultkan seluruh modul
                    let currentPermissions = [];
                    if (Array.isArray(item.permissions)) {
                        currentPermissions = [...item.permissions];
                    } else if (item.role === 'admin') {
                        currentPermissions = Object.keys(this.availablePermissions);
                    }

                    this.editFormData = { 
                        id: item.id,
                        name: item.name || '',
                        nip: item.nip || '',
                        email: item.email || '',
                        role: item.role || 'sub_admin',
                        unit: item.unit || '',
                        penugasan: item.penugasan || '',
                        permissions: currentPermissions,
                        password: '',
                        status: item.status || 'Aktif'
                    };
                    this.showEditModal = true;
                },

                saveNew() {
                    return this.saveUser();
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
                confirmDelete(item) {
                    this.deleteItem(item);
                },

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
