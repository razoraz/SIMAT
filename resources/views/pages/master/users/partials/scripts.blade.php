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

                // Sesi Pengguna Aktif & Izin Akses
                currentUserRole: '{{ Auth::user()?->role ?? "admin" }}',
                currentUserId: {{ Auth::user()?->id ?? 2 }},
                currentUserEmail: '{{ Auth::user()?->email ?? "admin@asimat.com" }}',
                canCreateUser: {{ (Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('users', 'create')) ? 'true' : 'false' }},
                canEditUserPerm: {{ (Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('users', 'edit')) ? 'true' : 'false' }},
                canDeleteUserPerm: {{ (Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('users', 'delete')) ? 'true' : 'false' }},
                csrfToken: '{{ csrf_token() }}',

                newFormData: {
                    name: '',
                    nip: '',
                    email: '',
                    role: 'admin',
                    unit_id: '',
                    unit: '',
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
                    unit_id: '',
                    unit: '',
                    penugasan: '',
                    permissions: [],
                    password: '',
                    status: ''
                },

                // Daftar Definisi Hak Akses Modul & Sub-Aksi dari Backend (Hierarchical Tree)
                availablePermissions: @json($availablePermissions ?? \App\Models\User::AVAILABLE_PERMISSIONS),
                rolePresets: @json($rolePresets ?? \App\Models\User::ROLE_PRESETS),

                // State Expanded Modul untuk Accordion Drawer UI
                expandedModules: {
                    new: {},
                    edit: {}
                },

                // Helper Inisialisasi Tree Permissions dari Data Backend / Database
                initTreePermissions(rawPermissions, role = 'admin') {
                    const result = {};
                    const isLegacyFlat = Array.isArray(rawPermissions) && (rawPermissions.length === 0 || typeof rawPermissions[0] === 'string');
                    const hasData = rawPermissions && (typeof rawPermissions === 'object');

                    Object.keys(this.availablePermissions).forEach(modKey => {
                        const modDef = this.availablePermissions[modKey];
                        const allActionKeys = Object.keys(modDef.actions || {});
                        const defaultActionKeys = allActionKeys.filter(actKey => modDef.actions[actKey].default);

                        let enabled = false;
                        let actions = [];

                        if (!hasData || rawPermissions === null) {
                            if (role === 'admin') {
                                // Default awal untuk admin baru: Modul ASTAP, Distribusi, Unit aktif penuh
                                enabled = ['astap', 'distribusi', 'unit'].includes(modKey);
                                actions = enabled ? [...allActionKeys] : [];
                            }
                        } else if (isLegacyFlat) {
                            // Kompatibilitas Akun Lama (Flat Array): Berikan akses penuh ke modul aktif tersebut
                            enabled = rawPermissions.includes(modKey);
                            actions = enabled ? [...allActionKeys] : [];
                        } else {
                            // Format Hirarki Baru (Object)
                            const modConfig = rawPermissions[modKey];
                            if (modConfig && modConfig.enabled) {
                                enabled = true;
                                if (Array.isArray(modConfig.actions)) {
                                    if (modConfig.actions.includes('*')) {
                                        actions = [...allActionKeys];
                                    } else {
                                        actions = modConfig.actions.filter(a => allActionKeys.includes(a));
                                    }
                                }
                            }
                        }

                        result[modKey] = {
                            enabled: enabled,
                            actions: actions
                        };
                    });

                    return result;
                },

                // Cek status modul induk apakah aktif
                isModuleEnabled(formType, modKey) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    return !!(form.permissions && form.permissions[modKey] && form.permissions[modKey].enabled);
                },

                // Cek status indeterminate (sebagian aksi dicentang)
                isModuleIndeterminate(formType, modKey) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    if (!this.isModuleEnabled(formType, modKey)) return false;

                    const actions = form.permissions[modKey]?.actions || [];
                    const totalActions = Object.keys(this.availablePermissions[modKey]?.actions || {}).length;

                    return actions.length > 0 && actions.length < totalActions;
                },

                // Cek apakah seluruh aksi dalam modul sudah dicentang
                isModuleFullyChecked(formType, modKey) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    if (!this.isModuleEnabled(formType, modKey)) return false;

                    const actions = form.permissions[modKey]?.actions || [];
                    const totalActions = Object.keys(this.availablePermissions[modKey]?.actions || {}).length;

                    return actions.length >= totalActions && totalActions > 0;
                },

                // Toggle Modul Induk (Klik Checkbox Parent: Langsung Pilih Semua Aksi & Auto-Expand)
                toggleParentModule(formType, modKey) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    if (!form.permissions) form.permissions = {};
                    if (!form.permissions[modKey]) {
                        form.permissions[modKey] = { enabled: false, actions: [] };
                    }

                    const modDef = this.availablePermissions[modKey];
                    const allActionKeys = Object.keys(modDef.actions || {});
                    const isFullyChecked = this.isModuleFullyChecked(formType, modKey);

                    // Jika saat ini SUDAH terpilih semua (100%) -> nonaktifkan modul & tutup drawer
                    if (isFullyChecked) {
                        form.permissions[modKey].enabled = false;
                        form.permissions[modKey].actions = [];
                        this.setModuleExpanded(formType, modKey, false);
                    } else {
                        // Jika saat ini belum terpilih penuh -> LANGSUNG PILIH SEMUA AKSI & Buka Drawer
                        form.permissions[modKey].enabled = true;
                        form.permissions[modKey].actions = [...allActionKeys];
                        this.setModuleExpanded(formType, modKey, true);
                    }
                },

                // Cek apakah sub-aksi spesifik dicentang
                isActionChecked(formType, modKey, actionKey) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    const mod = form.permissions?.[modKey];
                    if (!mod || !mod.enabled) return false;
                    return Array.isArray(mod.actions) && mod.actions.includes(actionKey);
                },

                // Toggle Sub-Aksi (Klik Checkbox Child)
                toggleSubAction(formType, modKey, actionKey) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    if (!form.permissions) form.permissions = {};
                    if (!form.permissions[modKey]) {
                        form.permissions[modKey] = { enabled: true, actions: [] };
                    }

                    const mod = form.permissions[modKey];
                    if (!Array.isArray(mod.actions)) mod.actions = [];

                    const idx = mod.actions.indexOf(actionKey);
                    if (idx > -1) {
                        mod.actions.splice(idx, 1);
                    } else {
                        mod.actions.push(actionKey);
                    }

                    // Sinkronisasi status modul induk
                    if (mod.actions.length > 0) {
                        mod.enabled = true;
                    } else {
                        mod.enabled = false;
                    }
                },

                // Toggle Seluruh Aksi di dalam 1 Modul
                toggleAllActionsInModule(formType, modKey, selectAll) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    if (!form.permissions?.[modKey]) return;

                    const allActions = Object.keys(this.availablePermissions[modKey]?.actions || {});
                    if (selectAll) {
                        form.permissions[modKey].enabled = true;
                        form.permissions[modKey].actions = [...allActions];
                    } else {
                        form.permissions[modKey].enabled = false;
                        form.permissions[modKey].actions = [];
                    }
                },

                // Kontrol Expand / Collapse Accordion Drawer Modul
                isModuleExpanded(formType, modKey) {
                    return !!this.expandedModules[formType]?.[modKey];
                },

                toggleExpandModule(formType, modKey) {
                    if (!this.expandedModules[formType]) this.expandedModules[formType] = {};
                    this.expandedModules[formType][modKey] = !this.expandedModules[formType][modKey];
                },

                setModuleExpanded(formType, modKey, state) {
                    if (!this.expandedModules[formType]) this.expandedModules[formType] = {};
                    this.expandedModules[formType][modKey] = state;
                },

                // Hitung jumlah aksi kritis yang aktif di 1 modul
                getModuleCriticalCount(formType, modKey) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    const actions = form.permissions?.[modKey]?.actions || [];
                    const modDef = this.availablePermissions[modKey];
                    if (!modDef) return 0;
                    return actions.filter(actKey => modDef.actions[actKey]?.critical).length;
                },

                // Ringkasan Global Wewenang
                getGlobalPermissionStats(formType) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    const totalModules = Object.keys(this.availablePermissions).length;
                    let activeModules = 0;
                    let activeActions = 0;
                    let criticalActions = 0;

                    Object.keys(this.availablePermissions).forEach(modKey => {
                        const mod = form.permissions?.[modKey];
                        const modDef = this.availablePermissions[modKey];
                        if (mod && mod.enabled && Array.isArray(mod.actions) && mod.actions.length > 0) {
                            activeModules++;
                            activeActions += mod.actions.length;
                            criticalActions += mod.actions.filter(a => modDef.actions[a]?.critical).length;
                        }
                    });

                    return {
                        totalModules,
                        activeModules,
                        activeActions,
                        criticalActions
                    };
                },

                // Hitung ringkasan aksi aktif (misal: "5/10 Aksi Aktif")
                getModuleActionSummary(formType, modKey) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    const mod = form.permissions?.[modKey];
                    const total = Object.keys(this.availablePermissions[modKey]?.actions || {}).length;

                    if (!mod || !mod.enabled || !mod.actions || mod.actions.length === 0) {
                        return { text: 'Nonaktif', color: 'slate', count: 0, total };
                    }

                    const count = mod.actions.length;
                    if (count >= total) {
                        return { text: 'Semua Aksi Aktif (' + count + '/' + total + ')', color: 'emerald', count, total };
                    }

                    return { text: count + '/' + total + ' Aksi Aktif', color: 'cyan', count, total };
                },

                // Terapkan Template Preset 1-Klik
                applyPreset(formType, presetKey) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    const tree = this.availablePermissions;

                    Object.keys(tree).forEach(modKey => {
                        const modDef = tree[modKey];
                        const allActionKeys = Object.keys(modDef.actions || {});
                        const defaultActions = allActionKeys.filter(a => modDef.actions[a].default);

                        let enabled = false;
                        let actions = [];

                        switch (presetKey) {
                            case 'full_admin':
                                enabled = true;
                                actions = [...allActionKeys];
                                break;

                            case 'inventaris':
                                enabled = ['astap', 'distribusi', 'unit'].includes(modKey);
                                if (enabled) {
                                    actions = defaultActions.filter(a => !['delete', 'reklas', 'hibah', 'kemitraan', 'tte'].includes(a));
                                }
                                break;

                            case 'keuangan_legal':
                                enabled = ['astap', 'bast', 'mutasi'].includes(modKey);
                                if (modKey === 'astap') {
                                    actions = ['view', 'export', 'reklas', 'hibah', 'kemitraan', 'tte'];
                                } else if (modKey === 'bast') {
                                    actions = ['view', 'create', 'tte', 'cetak'];
                                } else if (modKey === 'mutasi') {
                                    actions = ['view', 'internal', 'eksternal', 'approve', 'cetak'];
                                }
                                break;

                            case 'data_entry':
                                enabled = ['astap', 'distribusi'].includes(modKey);
                                if (modKey === 'astap') {
                                    actions = ['view', 'create', 'edit', 'export', 'qr'];
                                } else if (modKey === 'distribusi') {
                                    actions = ['view', 'create', 'konfirmasi', 'cetak'];
                                }
                                break;

                            case 'auditor':
                                enabled = true;
                                actions = allActionKeys.filter(a => a === 'view' || a === 'export');
                                break;

                            default:
                                enabled = false;
                                actions = [];
                                break;
                        }

                        if (!form.permissions) form.permissions = {};
                        form.permissions[modKey] = {
                            enabled: enabled,
                            actions: actions
                        };
                    });

                    const presetTitle = this.rolePresets[presetKey]?.title || 'Preset';
                    this.showToast('✨ Template diterapkan: ' + presetTitle, 'info');
                },

                // Toggle Seluruh Modul (Pilih Semua / Safe-Mode / Kosongkan)
                toggleAllPermissions(formType, selectAll, safeOnly = false) {
                    const form = formType === 'new' ? this.newFormData : this.editFormData;
                    if (!form.permissions) form.permissions = {};

                    if (selectAll) {
                        Object.keys(this.availablePermissions).forEach(modKey => {
                            const modDef = this.availablePermissions[modKey];
                            const allActions = Object.keys(modDef.actions || {});
                            const targetActions = safeOnly 
                                ? allActions.filter(a => !modDef.actions[a]?.critical)
                                : [...allActions];

                            form.permissions[modKey] = {
                                enabled: true,
                                actions: targetActions
                            };
                            this.setModuleExpanded(formType, modKey, safeOnly ? false : true);
                        });
                        if (safeOnly) {
                            this.showToast('🛡️ Seluruh modul diaktifkan dengan Mode Aman (Aksi Hapus & Kritis tetap dinonaktifkan).', 'info');
                        } else {
                            this.showToast('👑 Seluruh modul & seluruh aksi aktif penuh (Full Control).', 'success');
                        }
                    } else {
                        Object.keys(this.availablePermissions).forEach(modKey => {
                            form.permissions[modKey] = { enabled: false, actions: [] };
                            this.setModuleExpanded(formType, modKey, false);
                        });
                        this.showToast('Seluruh hak akses telah dikosongkan.', 'warning');
                    }
                },

                // Helper Tampilan Hak Akses di Modal Detail
                isDetailModEnabled(modKey) {
                    if (!this.selectedUser) return false;
                    if (this.selectedUser.role === 'master_admin') return true;
                    const p = this.selectedUser.permissions;
                    if (!p) return true;
                    if (Array.isArray(p)) return p.includes(modKey);
                    return !!(p[modKey] && p[modKey].enabled);
                },

                getDetailModActions(modKey) {
                    if (!this.selectedUser) return [];
                    const modDef = this.availablePermissions[modKey];
                    if (!modDef) return [];
                    const allActionKeys = Object.keys(modDef.actions || {});
                    if (this.selectedUser.role === 'master_admin') return allActionKeys;

                    const p = this.selectedUser.permissions;
                    if (!p) return allActionKeys;
                    if (Array.isArray(p)) {
                        return p.includes(modKey) ? allActionKeys.filter(a => modDef.actions[a]?.default) : [];
                    }

                    const mod = p[modKey];
                    if (!mod || !mod.enabled) return [];
                    if (Array.isArray(mod.actions)) {
                        return mod.actions.includes('*') ? allActionKeys : mod.actions;
                    }
                    return [];
                },

                // Ringkasan Tampilan Badge Modul untuk Kolom Tabel Peran
                getUserPermissionsSummary(user) {
                    if (!user || user.role !== 'admin') return null;
                    const perms = user.permissions;
                    const totalModules = Object.keys(this.availablePermissions || {}).length || 7;

                    if (!perms) {
                        return { text: 'Semua Modul', isAll: true, tooltip: 'Akses penuh ke seluruh modul sistem' };
                    }

                    if (Array.isArray(perms)) {
                        if (perms.length >= totalModules) {
                            return { text: 'Semua Modul', isAll: true, tooltip: 'Akses ke seluruh modul sistem' };
                        }
                        return {
                            text: `${perms.length} Modul Diizinkan`,
                            isAll: false,
                            tooltip: 'Modul aktif: ' + perms.map(k => this.availablePermissions[k]?.label || k).join(', ')
                        };
                    }

                    if (typeof perms === 'object') {
                        const activeKeys = Object.entries(perms)
                            .filter(([k, v]) => v && (v === true || v.enabled === true))
                            .map(([k]) => k);

                        if (activeKeys.length >= totalModules) {
                            return { text: 'Semua Modul', isAll: true, tooltip: 'Akses ke seluruh modul sistem' };
                        }
                        if (activeKeys.length === 0) {
                            return { text: 'Belum Ada Akses', isAll: false, tooltip: 'Akun ini belum memiliki akses modul aktif' };
                        }
                        return {
                            text: `${activeKeys.length} Modul Diizinkan`,
                            isAll: false,
                            tooltip: 'Modul aktif: ' + activeKeys.map(k => this.availablePermissions[k]?.label || k).join(', ')
                        };
                    }

                    return { text: 'Semua Modul', isAll: true, tooltip: 'Akses penuh operasional' };
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
                        if (targetUser.role === 'sub_admin') {
                            return this.canEditUserPerm;
                        }
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
                        return targetUser.role === 'sub_admin' && this.canDeleteUserPerm;
                    }

                    return false;
                },

                getEditTooltip(targetUser) {
                    if (this.canEditUser(targetUser)) return '';
                    if (targetUser.role === 'master_admin') return '🔒 Akun Master Admin diproteksi khusus (Hanya Master Admin yang dapat mengubah)';
                    if (targetUser.role === 'admin' && !this.isSelf(targetUser)) return '🔒 Admin tidak diizinkan mengubah akun Admin lain';
                    if (targetUser.role === 'sub_admin' && !this.canEditUserPerm) return '🔒 Anda tidak memiliki izin untuk mengubah akun pengguna';
                    return 'Akses dibatasi';
                },

                getDeleteTooltip(targetUser) {
                    if (this.canDeleteUser(targetUser)) return '';
                    if (targetUser.role === 'master_admin') return '🔒 Akun Master Admin tidak dapat dihapus';
                    if (targetUser.role === 'admin') return '🔒 Admin tidak diizinkan menghapus akun Admin';
                    if (targetUser.role === 'sub_admin' && !this.canDeleteUserPerm) return '🔒 Anda tidak memiliki izin untuk menghapus akun pengguna';
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
                    if (!this.canCreateUser) {
                        this.showToast('⛔ Akses Ditolak: Anda tidak memiliki wewenang untuk menambah akun pengguna baru', 'error');
                        return;
                    }

                    this.newFormData = {
                        name: '',
                        nip: '',
                        email: '',
                        role: this.currentUserRole === 'master_admin' ? 'admin' : 'sub_admin',
                        unit_id: '',
                        unit: '',
                        penugasan: '',
                        permissions: this.initTreePermissions(null, 'admin'),
                        password: '',
                        status: 'Aktif'
                    };
                    this.expandedModules.new = {};
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
                        unit_id: item.unit_id || '',
                        unit: item.unit || '',
                        penugasan: item.penugasan || '',
                        permissions: this.initTreePermissions(item.permissions, item.role),
                        password: '',
                        status: item.status || 'Aktif'
                    };
                    this.expandedModules.edit = {};
                    this.showEditModal = true;
                },

                saveNew() {
                    return this.saveUser();
                },

                // 1. Simpan Pengguna Baru ke Backend (CREATE)
                async saveUser() {
                    if (!this.newFormData.name || !this.newFormData.email) {
                        this.showToast('⚠️ Harap lengkapi Nama Lengkap dan Email pengguna!', 'warning');
                        return;
                    }

                    if (this.newFormData.role === 'sub_admin' && !this.newFormData.unit_id) {
                        this.showToast('⚠️ Harap pilih Ruangan / Unit Penugasan untuk akun Sub Admin!', 'warning');
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
                                    this.showToast(res.message, 'success');
                                } else {
                                    this.showToast(res.message || 'Gagal mendaftarkan akun baru.', 'error');
                                }
                            } catch (err) {
                                console.error(err);
                                this.showToast('Terjadi kesalahan server saat mendaftarkan akun.', 'error');
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
                                    this.showToast(res.message, 'success');
                                } else {
                                    this.showToast(res.message || 'Gagal menyimpan perubahan.', 'error');
                                }
                            } catch (err) {
                                console.error(err);
                                this.showToast('Terjadi kesalahan server saat memperbarui akun.', 'error');
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

                // 4. Masuk Sementara Sebagai Pengguna (IMPERSONATE)
                confirmImpersonate(user) {
                    if (this.currentUserRole !== 'master_admin') {
                        this.showToast('⛔ Hanya Master Admin yang dapat menggunakan fitur ini.', 'error');
                        return;
                    }

                    this.askConfirmation({
                        title: '👁️ Masuk Sementara Sebagai Pengguna',
                        message: `Anda akan beralih sesi dan melihat SIMAT-RK persis sebagaimana dilihat oleh ${user.name} (${user.role.toUpperCase()}). Anda dapat kembali ke sesi Master Admin kapan saja melalui tombol di bagian atas layar.`,
                        itemName: user.name + ' (' + user.email + ')',
                        type: 'info',
                        btnText: '👁️ Ya, Masuk Sebagai Akun Ini',
                        onConfirm: async () => {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = '/impersonate/' + user.id;

                            const csrfInput = document.createElement('input');
                            csrfInput.type = 'hidden';
                            csrfInput.name = '_token';
                            csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || this.csrfToken;
                            form.appendChild(csrfInput);

                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                },

                deleteItem(item) {
                    if (!this.canDeleteUser(item)) {
                        this.showToast('⛔ Akses Ditolak: ' + this.getDeleteTooltip(item), 'error');
                        return;
                    }

                    this.askConfirmation({
                        title: 'Konfirmasi Pindahkan ke Tong Sampah',
                        message: 'Apakah Anda yakin ingin memindahkan akun pengguna ini ke Pusat Data Terhapus (Recycle Bin)? Akun tidak akan dapat digunakan untuk login kembali kecuali dipulihkan sewaktu-waktu oleh Administrator.',
                        itemName: `${item.name} (${item.unit || (item.role === 'master_admin' ? 'Master Admin' : (item.role === 'admin' ? 'Admin Operasional' : 'Sub Admin Unit'))})`,
                        type: 'danger',
                        btnText: 'Pindahkan ke Tong Sampah',
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
                                    this.showToast(res.message, 'success');
                                } else {
                                    this.showToast(res.message || 'Gagal menghapus akun pengguna.', 'error');
                                }
                            } catch (err) {
                                console.error(err);
                                this.showToast('Terjadi kesalahan server saat menghapus akun.', 'error');
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
                        btnText: btnText || (type === 'danger' ? 'Pindahkan ke Tong Sampah' : (type === 'warning' ? 'Simpan Perubahan' : 'Tambahkan')),
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
                    const cleanMsg = String(message || '').replace(/^[\s✅✔️☑️✓✔⚠️❌🚫⛔ℹ️🗑️✏️🔑💾]+/, '').trim();
                    this.toast = { show: true, message: cleanMsg, type: type };
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },

                // 4. Reset Password ke Default 'rsud123'
                resetPasswordAction(item) {
                    if (!item) return;
                    this.askConfirmation({
                        title: '🔑 Konfirmasi Reset Password',
                        message: 'Apakah Anda yakin ingin mengembalikan password akun ini ke password default "rsud123"? Tindakan ini akan dicatat ke dalam Log Audit Wewenang.',
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
                                    if (res.user) {
                                        const idx = this.users.findIndex(u => u.id === item.id);
                                        if (idx !== -1) {
                                            this.users[idx] = { ...res.user };
                                        }
                                        if (this.selectedUser && this.selectedUser.id === item.id) {
                                            this.selectedUser = { ...res.user };
                                        }
                                    }
                                    this.showToast(res.message, 'success');
                                } else {
                                    this.showToast(res.message || 'Gagal mereset password.', 'error');
                                }
                            } catch (err) {
                                console.error(err);
                                this.showToast('Terjadi kesalahan server saat mereset password.', 'error');
                            }
                        }
                    });
                },

                // Helper Format Tanggal Audit Trail
                formatAuditDate(dateStr) {
                    if (!dateStr) return '-';
                    try {
                        const d = new Date(dateStr);
                        if (isNaN(d.getTime())) return dateStr;
                        return d.toLocaleString('id-ID', {
                            day: 'numeric',
                            month: 'short',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit'
                        }) + ' WIB';
                    } catch (e) {
                        return dateStr;
                    }
                },

                // Helper Badge Styling Action Type Audit Trail
                getAuditBadge(actionType) {
                    switch (actionType) {
                        case 'ROLE_CHANGE':
                            return { label: '👑 UBAH ROLE', class: 'bg-amber-500/20 text-amber-300 border border-amber-500/40' };
                        case 'PERMISSION_UPDATE':
                            return { label: '⚡ HAK AKSES', class: 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40' };
                        case 'ROLE_AND_PERMISSION':
                            return { label: '👑⚡ ROLE & AKSES', class: 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40' };
                        case 'PASSWORD_CHANGE':
                            return { label: '🔑 UBAH PASSWORD', class: 'bg-purple-500/20 text-purple-300 border border-purple-500/40' };
                        case 'RESET_PASSWORD':
                            return { label: '🔄 RESET PASSWORD', class: 'bg-rose-500/20 text-rose-300 border border-rose-500/40' };
                        case 'UNIT_ASSIGNMENT':
                            return { label: '🏥 RUANGAN UNIT', class: 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' };
                        case 'CREATE_USER':
                            return { label: '✨ BUAT AKUN', class: 'bg-teal-500/20 text-teal-300 border border-teal-500/40' };
                        case 'DELETE_USER':
                            return { label: '🗑️ HAPUS AKUN', class: 'bg-red-500/20 text-red-300 border border-red-500/40' };
                        default:
                            return { label: '📝 PROFIL', class: 'bg-slate-700/60 text-slate-300 border border-slate-600' };
                    }
                }
            };
        }
    </script>
