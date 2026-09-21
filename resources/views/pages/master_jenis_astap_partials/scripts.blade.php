<script>
    function masterJenisAstap() {
        return {
            showAddModal: false,
            showEditModal: false,
            showImportModal: false,
            selectedKode: null,

            newFormData: {
                jenis: '1.3.1',
                nama_jenis: 'TANAH',
                sub_rincian_objek: '1.3.1.01.01.01',
                uraian_sub_rincian: '',
                sub_sub_rincian_objek: '1.3.1.01.01.01.001',
                uraian_sub_sub_rincian: ''
            },

            editFormData: {
                id: null,
                jenis: '',
                nama_jenis: '',
                sub_rincian_objek: '',
                uraian_sub_rincian: '',
                sub_sub_rincian_objek: '',
                uraian_sub_sub_rincian: ''
            },

            openEdit(item) {
                this.editFormData = { ...item };
                this.showEditModal = true;
            },

            masterJenisList: (function() {
                const defaults = [
                    { kode: '1.3.1', nama: 'TANAH', sub_prefix: '1.3.1.01.01.01', sub_sub_prefix: '1.3.1.01.01.01.001' },
                    { kode: '1.3.2', nama: 'PERALATAN DAN MESIN', sub_prefix: '1.3.2.02.01.01', sub_sub_prefix: '1.3.2.02.01.01.001' },
                    { kode: '1.3.3', nama: 'GEDUNG DAN BANGUNAN', sub_prefix: '1.3.3.01.01.01', sub_sub_prefix: '1.3.3.01.01.01.001' },
                    { kode: '1.3.4', nama: 'JALAN, IRIGASI DAN JARINGAN', sub_prefix: '1.3.4.03.01.01', sub_sub_prefix: '1.3.4.03.01.01.001' },
                    { kode: '1.3.5', nama: 'ASET TETAP LAINNYA', sub_prefix: '1.3.5.01.01.01', sub_sub_prefix: '1.3.5.01.01.01.001' },
                    { kode: '1.3.6', nama: 'KONSTRUKSI DALAM PENGERJAAN', sub_prefix: '1.3.6.01.01.01', sub_sub_prefix: '1.3.6.01.01.01.001' },
                    { kode: '1.5.3', nama: 'ASET TIDAK BERWUJUD', sub_prefix: '1.5.3.01.01.01', sub_sub_prefix: '1.5.3.01.01.01.001' },
                    { kode: '1.3.7', nama: 'ASET TETAP DALAM RENOVASI', sub_prefix: '1.3.7.01.01.01', sub_sub_prefix: '1.3.7.01.01.01.001' }
                ];
                const fromDb = @json($uniqueJenis ?? []);
                const merged = [...defaults];
                if (Array.isArray(fromDb)) {
                    fromDb.forEach(dbItem => {
                        const nama = (dbItem.nama_jenis || '').trim();
                        if (dbItem && dbItem.jenis && nama && !merged.some(m => m.kode === dbItem.jenis || m.nama.toUpperCase() === nama.toUpperCase())) {
                            merged.push({
                                kode: dbItem.jenis,
                                nama: nama.toUpperCase(),
                                sub_prefix: dbItem.jenis + '.01.01.01',
                                sub_sub_prefix: dbItem.jenis + '.01.01.01.001'
                            });
                        }
                    });
                }
                return merged.filter(m => m && m.nama && m.nama.trim() !== '');
            })(),

            masterSubRincianList: @json($uniqueSubRincian ?? []),

            showJenisDropdownNew: false,
            get allMatchingJenisNew() {
                const q = (this.newFormData.nama_jenis || '').toLowerCase().trim();
                const list = (this.masterJenisList || []).filter(item => item && item.nama && item.nama.trim() !== '');
                if (!q) return list;
                return list.filter(item => 
                    item.nama.toLowerCase().includes(q) || 
                    item.kode.toLowerCase().includes(q)
                );
            },
            get filteredJenisListNew() {
                return this.allMatchingJenisNew.slice(0, 5);
            },
            get totalJenisCountNew() {
                return this.allMatchingJenisNew.length;
            },
            selectJenisNew(item) {
                this.newFormData.nama_jenis = item.nama;
                this.newFormData.jenis = item.kode;
                this.newFormData.sub_rincian_objek = item.sub_prefix || (item.kode + '.01.01.01');
                this.newFormData.sub_sub_rincian_objek = item.sub_sub_prefix || (item.kode + '.01.01.01.001');
                this.showJenisDropdownNew = false;
            },
            onKodeJenisInputNew() {
                const found = this.masterJenisList.find(m => m.kode === (this.newFormData.jenis || '').trim());
                if (found) {
                    this.newFormData.nama_jenis = found.nama;
                    if (!this.newFormData.sub_rincian_objek || this.newFormData.sub_rincian_objek.endsWith('.01.01.01')) {
                        this.newFormData.sub_rincian_objek = found.sub_prefix;
                    }
                    if (!this.newFormData.sub_sub_rincian_objek || this.newFormData.sub_sub_rincian_objek.endsWith('.01.01.01.001')) {
                        this.newFormData.sub_sub_rincian_objek = found.sub_sub_prefix;
                    }
                }
            },

            // --- Sub Rincian Objek (New) ---
            showSubRincianDropdownNew: false,
            get allMatchingSubRincianNew() {
                const q = (this.newFormData.uraian_sub_rincian || '').toLowerCase().trim();
                const curJenis = (this.newFormData.jenis || '').trim();

                let list = (this.masterSubRincianList || []).filter(item => item && item.uraian_sub_rincian && item.uraian_sub_rincian.trim() !== '');
                if (curJenis) {
                    list = list.filter(item => item.jenis === curJenis || (item.sub_rincian_objek && item.sub_rincian_objek.startsWith(curJenis)));
                }

                if (!q) return list;

                const filtered = list.filter(item => 
                    (item.uraian_sub_rincian || '').toLowerCase().includes(q) || 
                    (item.sub_rincian_objek || '').toLowerCase().includes(q)
                );

                if (filtered.length === 0 && curJenis) {
                    return (this.masterSubRincianList || []).filter(item => 
                        item && item.uraian_sub_rincian && (
                            (item.uraian_sub_rincian || '').toLowerCase().includes(q) || 
                            (item.sub_rincian_objek || '').toLowerCase().includes(q)
                        )
                    );
                }

                return filtered;
            },
            get filteredSubRincianListNew() {
                return this.allMatchingSubRincianNew.slice(0, 5);
            },
            get totalSubRincianCountNew() {
                return this.allMatchingSubRincianNew.length;
            },
            selectSubRincianNew(item) {
                this.newFormData.uraian_sub_rincian = item.uraian_sub_rincian;
                this.newFormData.sub_rincian_objek = item.sub_rincian_objek;
                if (item.jenis && (!this.newFormData.jenis || this.newFormData.jenis !== item.jenis)) {
                    this.newFormData.jenis = item.jenis;
                    const parentJenis = this.masterJenisList.find(j => j.kode === item.jenis);
                    if (parentJenis) {
                        this.newFormData.nama_jenis = parentJenis.nama;
                    }
                }
                if (!this.newFormData.sub_sub_rincian_objek || !this.newFormData.sub_sub_rincian_objek.startsWith(item.sub_rincian_objek)) {
                    this.newFormData.sub_sub_rincian_objek = item.sub_rincian_objek + '.001';
                }
                this.showSubRincianDropdownNew = false;
            },
            onKodeSubRincianInputNew() {
                const code = (this.newFormData.sub_rincian_objek || '').trim();
                const found = (this.masterSubRincianList || []).find(item => item.sub_rincian_objek === code);
                if (found) {
                    this.newFormData.uraian_sub_rincian = found.uraian_sub_rincian;
                }
                if (code && (!this.newFormData.sub_sub_rincian_objek || !this.newFormData.sub_sub_rincian_objek.startsWith(code))) {
                    this.newFormData.sub_sub_rincian_objek = code + '.001';
                }
            },

            openAdd() {
                this.newFormData = {
                    jenis: '',
                    nama_jenis: '',
                    sub_rincian_objek: '',
                    uraian_sub_rincian: '',
                    sub_sub_rincian_objek: '',
                    uraian_sub_sub_rincian: ''
                };
                this.showJenisDropdownNew = false;
                this.showSubRincianDropdownNew = false;
                this.showAddModal = true;
            },

            // --- Edit Form Methods ---
            showJenisDropdownEdit: false,
            get allMatchingJenisEdit() {
                const q = (this.editFormData.nama_jenis || '').toLowerCase().trim();
                const list = (this.masterJenisList || []).filter(item => item && item.nama && item.nama.trim() !== '');
                if (!q) return list;
                return list.filter(item => 
                    item.nama.toLowerCase().includes(q) || 
                    item.kode.toLowerCase().includes(q)
                );
            },
            get filteredJenisListEdit() {
                return this.allMatchingJenisEdit.slice(0, 5);
            },
            get totalJenisCountEdit() {
                return this.allMatchingJenisEdit.length;
            },
            selectJenisEdit(item) {
                this.editFormData.nama_jenis = item.nama;
                this.editFormData.jenis = item.kode;
                this.showJenisDropdownEdit = false;
            },
            onKodeJenisInputEdit() {
                const found = this.masterJenisList.find(m => m.kode === (this.editFormData.jenis || '').trim());
                if (found) {
                    this.editFormData.nama_jenis = found.nama;
                }
            },

            // --- Sub Rincian Objek (Edit) ---
            showSubRincianDropdownEdit: false,
            get allMatchingSubRincianEdit() {
                const q = (this.editFormData.uraian_sub_rincian || '').toLowerCase().trim();
                const curJenis = (this.editFormData.jenis || '').trim();

                let list = (this.masterSubRincianList || []).filter(item => item && item.uraian_sub_rincian && item.uraian_sub_rincian.trim() !== '');
                if (curJenis) {
                    list = list.filter(item => item.jenis === curJenis || (item.sub_rincian_objek && item.sub_rincian_objek.startsWith(curJenis)));
                }

                if (!q) return list;

                const filtered = list.filter(item => 
                    (item.uraian_sub_rincian || '').toLowerCase().includes(q) || 
                    (item.sub_rincian_objek || '').toLowerCase().includes(q)
                );

                if (filtered.length === 0 && curJenis) {
                    return (this.masterSubRincianList || []).filter(item => 
                        item && item.uraian_sub_rincian && (
                            (item.uraian_sub_rincian || '').toLowerCase().includes(q) || 
                            (item.sub_rincian_objek || '').toLowerCase().includes(q)
                        )
                    );
                }

                return filtered;
            },
            get filteredSubRincianListEdit() {
                return this.allMatchingSubRincianEdit.slice(0, 5);
            },
            get totalSubRincianCountEdit() {
                return this.allMatchingSubRincianEdit.length;
            },
            selectSubRincianEdit(item) {
                this.editFormData.uraian_sub_rincian = item.uraian_sub_rincian;
                this.editFormData.sub_rincian_objek = item.sub_rincian_objek;
                if (item.jenis && (!this.editFormData.jenis || this.editFormData.jenis !== item.jenis)) {
                    this.editFormData.jenis = item.jenis;
                    const parentJenis = this.masterJenisList.find(j => j.kode === item.jenis);
                    if (parentJenis) {
                        this.editFormData.nama_jenis = parentJenis.nama;
                    }
                }
                this.showSubRincianDropdownEdit = false;
            },
            onKodeSubRincianInputEdit() {
                const code = (this.editFormData.sub_rincian_objek || '').trim();
                const found = (this.masterSubRincianList || []).find(item => item.sub_rincian_objek === code);
                if (found) {
                    this.editFormData.uraian_sub_rincian = found.uraian_sub_rincian;
                }
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

            showSimatToast(message, type = 'success') {
                this.toast = { show: true, message: message, type: type };
                setTimeout(() => { this.toast.show = false; }, 4000);
            },

            confirmDeleteForm(event, itemName) {
                event.preventDefault();
                const formElement = event.target;
                this.askConfirmation({
                    title: '🗑️ Konfirmasi Hapus Kode 108',
                    message: 'Apakah Anda yakin ingin menghapus data klasifikasi Kode 108 BMD ini dari master data?',
                    itemName: itemName || '',
                    type: 'danger',
                    btnText: '🗑️ Ya, Hapus Kode 108',
                    onConfirm: () => {
                        formElement.submit();
                    }
                });
            },

            confirmAddForm(event) {
                event.preventDefault();
                const formElement = event.target;
                const itemPreview = (this.newFormData.uraian_sub_sub_rincian || 'Klasifikasi Baru') + ' (' + (this.newFormData.sub_sub_rincian_objek || 'Kode 108') + ')';
                this.askConfirmation({
                    title: '➕ Konfirmasi Tambah Kode 108',
                    message: 'Apakah Anda yakin ingin mendaftarkan data klasifikasi Kode 108 BMD baru ini?',
                    itemName: itemPreview,
                    type: 'success',
                    btnText: '➕ Ya, Simpan Kode 108',
                    onConfirm: () => {
                        formElement.submit();
                    }
                });
            },

            confirmEditForm(event) {
                event.preventDefault();
                const formElement = event.target;
                const itemPreview = (this.editFormData.uraian_sub_sub_rincian || 'Klasifikasi ASTAP') + ' (' + (this.editFormData.sub_sub_rincian_objek || 'Kode 108') + ')';
                this.askConfirmation({
                    title: '✏️ Konfirmasi Simpan Perubahan Kode 108',
                    message: 'Apakah Anda yakin ingin menyimpan perubahan data klasifikasi Kode 108 ini?',
                    itemName: itemPreview,
                    type: 'warning',
                    btnText: '✏️ Ya, Simpan Perubahan',
                    onConfirm: () => {
                        formElement.submit();
                    }
                });
            }
        };
    }
</script>
