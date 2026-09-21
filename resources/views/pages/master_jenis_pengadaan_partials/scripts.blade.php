<script>
    function masterJenisPengadaan() {
        return {
            searchQuery: '{{ request('search', '') }}',
            programFilter: '{{ request('program', 'all') }}',
            showAddModal: false,
            showEditModal: false,
            editActionUrl: '',

            newFormData: {
                program_kode: '',
                program_nama: '',
                kegiatan_kode: '',
                kegiatan_nama: '',
                sub_kegiatan_kode: '',
                sub_kegiatan_nama: ''
            },

            editFormData: {
                id: null,
                program_kode: '',
                program_nama: '',
                kegiatan_kode: '',
                kegiatan_nama: '',
                sub_kegiatan_kode: '',
                sub_kegiatan_nama: ''
            },

            masterProgramList: @json($uniquePrograms ?? []),
            masterKegiatanList: @json($uniqueKegiatan ?? []),

            // --- Program (New) ---
            showProgramDropdownNew: false,
            get allMatchingProgramNew() {
                const q = (this.newFormData.program_nama || '').toLowerCase().trim();
                const list = (this.masterProgramList || []).filter(item => item && item.program_nama && item.program_nama.trim() !== '');
                if (!q) return list;
                return list.filter(item =>
                    (item.program_nama || '').toLowerCase().includes(q) ||
                    (item.program_kode || '').toLowerCase().includes(q)
                );
            },
            get filteredProgramListNew() {
                return this.allMatchingProgramNew.slice(0, 5);
            },
            get totalProgramCountNew() {
                return this.allMatchingProgramNew.length;
            },
            selectProgramNew(item) {
                this.newFormData.program_nama = item.program_nama;
                this.newFormData.program_kode = item.program_kode;
                if (!this.newFormData.kegiatan_kode || !this.newFormData.kegiatan_kode.startsWith(item.program_kode)) {
                    this.newFormData.kegiatan_kode = item.program_kode + '.2.10';
                }
                if (!this.newFormData.sub_kegiatan_kode || !this.newFormData.sub_kegiatan_kode.startsWith(item.program_kode)) {
                    this.newFormData.sub_kegiatan_kode = item.program_kode + '.2.10.0001';
                }
                this.showProgramDropdownNew = false;
            },
            onKodeProgramInputNew() {
                const code = (this.newFormData.program_kode || '').trim();
                const found = (this.masterProgramList || []).find(item => item.program_kode === code);
                if (found) {
                    this.newFormData.program_nama = found.program_nama;
                }
                if (code) {
                    if (!this.newFormData.kegiatan_kode || !this.newFormData.kegiatan_kode.startsWith(code)) {
                        this.newFormData.kegiatan_kode = code + '.2.10';
                    }
                    if (!this.newFormData.sub_kegiatan_kode || !this.newFormData.sub_kegiatan_kode.startsWith(code)) {
                        this.newFormData.sub_kegiatan_kode = code + '.2.10.0001';
                    }
                }
            },

            // --- Kegiatan (New) ---
            showKegiatanDropdownNew: false,
            get allMatchingKegiatanNew() {
                const q = (this.newFormData.kegiatan_nama || '').toLowerCase().trim();
                const curProg = (this.newFormData.program_kode || '').trim();

                let list = (this.masterKegiatanList || []).filter(item => item && item.kegiatan_nama && item.kegiatan_nama.trim() !== '');
                if (curProg) {
                    list = list.filter(item => item.program_kode === curProg || (item.kegiatan_kode && item.kegiatan_kode.startsWith(curProg)));
                }

                if (!q) return list;

                const filtered = list.filter(item =>
                    (item.kegiatan_nama || '').toLowerCase().includes(q) ||
                    (item.kegiatan_kode || '').toLowerCase().includes(q)
                );

                if (filtered.length === 0 && curProg) {
                    return (this.masterKegiatanList || []).filter(item =>
                        item && item.kegiatan_nama && (
                            (item.kegiatan_nama || '').toLowerCase().includes(q) ||
                            (item.kegiatan_kode || '').toLowerCase().includes(q)
                        )
                    );
                }

                return filtered;
            },
            get filteredKegiatanListNew() {
                return this.allMatchingKegiatanNew.slice(0, 5);
            },
            get totalKegiatanCountNew() {
                return this.allMatchingKegiatanNew.length;
            },
            selectKegiatanNew(item) {
                this.newFormData.kegiatan_nama = item.kegiatan_nama;
                this.newFormData.kegiatan_kode = item.kegiatan_kode;
                if (item.program_kode && (!this.newFormData.program_kode || this.newFormData.program_kode !== item.program_kode)) {
                    this.newFormData.program_kode = item.program_kode;
                    const parentProg = (this.masterProgramList || []).find(p => p.program_kode === item.program_kode);
                    if (parentProg) {
                        this.newFormData.program_nama = parentProg.program_nama;
                    }
                }
                if (!this.newFormData.sub_kegiatan_kode || !this.newFormData.sub_kegiatan_kode.startsWith(item.kegiatan_kode)) {
                    this.newFormData.sub_kegiatan_kode = item.kegiatan_kode + '.0001';
                }
                this.showKegiatanDropdownNew = false;
            },
            onKodeKegiatanInputNew() {
                const code = (this.newFormData.kegiatan_kode || '').trim();
                const found = (this.masterKegiatanList || []).find(item => item.kegiatan_kode === code);
                if (found) {
                    this.newFormData.kegiatan_nama = found.kegiatan_nama;
                    if (found.program_kode && !this.newFormData.program_kode) {
                        this.newFormData.program_kode = found.program_kode;
                    }
                }
                if (code && (!this.newFormData.sub_kegiatan_kode || !this.newFormData.sub_kegiatan_kode.startsWith(code))) {
                    this.newFormData.sub_kegiatan_kode = code + '.0001';
                }
            },

            openAdd() {
                this.newFormData = {
                    program_kode: '',
                    program_nama: '',
                    kegiatan_kode: '',
                    kegiatan_nama: '',
                    sub_kegiatan_kode: '',
                    sub_kegiatan_nama: ''
                };
                this.showProgramDropdownNew = false;
                this.showKegiatanDropdownNew = false;
                this.showAddModal = true;
            },

            // --- Program (Edit) ---
            showProgramDropdownEdit: false,
            get allMatchingProgramEdit() {
                const q = (this.editFormData.program_nama || '').toLowerCase().trim();
                const list = (this.masterProgramList || []).filter(item => item && item.program_nama && item.program_nama.trim() !== '');
                if (!q) return list;
                return list.filter(item =>
                    (item.program_nama || '').toLowerCase().includes(q) ||
                    (item.program_kode || '').toLowerCase().includes(q)
                );
            },
            get filteredProgramListEdit() {
                return this.allMatchingProgramEdit.slice(0, 5);
            },
            get totalProgramCountEdit() {
                return this.allMatchingProgramEdit.length;
            },
            selectProgramEdit(item) {
                this.editFormData.program_nama = item.program_nama;
                this.editFormData.program_kode = item.program_kode;
                this.showProgramDropdownEdit = false;
            },
            onKodeProgramInputEdit() {
                const code = (this.editFormData.program_kode || '').trim();
                const found = (this.masterProgramList || []).find(item => item.program_kode === code);
                if (found) {
                    this.editFormData.program_nama = found.program_nama;
                }
            },

            // --- Kegiatan (Edit) ---
            showKegiatanDropdownEdit: false,
            get allMatchingKegiatanEdit() {
                const q = (this.editFormData.kegiatan_nama || '').toLowerCase().trim();
                const curProg = (this.editFormData.program_kode || '').trim();

                let list = (this.masterKegiatanList || []).filter(item => item && item.kegiatan_nama && item.kegiatan_nama.trim() !== '');
                if (curProg) {
                    list = list.filter(item => item.program_kode === curProg || (item.kegiatan_kode && item.kegiatan_kode.startsWith(curProg)));
                }

                if (!q) return list;

                const filtered = list.filter(item =>
                    (item.kegiatan_nama || '').toLowerCase().includes(q) ||
                    (item.kegiatan_kode || '').toLowerCase().includes(q)
                );

                if (filtered.length === 0 && curProg) {
                    return (this.masterKegiatanList || []).filter(item =>
                        item && item.kegiatan_nama && (
                            (item.kegiatan_nama || '').toLowerCase().includes(q) ||
                            (item.kegiatan_kode || '').toLowerCase().includes(q)
                        )
                    );
                }

                return filtered;
            },
            get filteredKegiatanListEdit() {
                return this.allMatchingKegiatanEdit.slice(0, 5);
            },
            get totalKegiatanCountEdit() {
                return this.allMatchingKegiatanEdit.length;
            },
            selectKegiatanEdit(item) {
                this.editFormData.kegiatan_nama = item.kegiatan_nama;
                this.editFormData.kegiatan_kode = item.kegiatan_kode;
                if (item.program_kode && (!this.editFormData.program_kode || this.editFormData.program_kode !== item.program_kode)) {
                    this.editFormData.program_kode = item.program_kode;
                    const parentProg = (this.masterProgramList || []).find(p => p.program_kode === item.program_kode);
                    if (parentProg) {
                        this.editFormData.program_nama = parentProg.program_nama;
                    }
                }
                this.showKegiatanDropdownEdit = false;
            },
            onKodeKegiatanInputEdit() {
                const code = (this.editFormData.kegiatan_kode || '').trim();
                const found = (this.masterKegiatanList || []).find(item => item.kegiatan_kode === code);
                if (found) {
                    this.editFormData.kegiatan_nama = found.kegiatan_nama;
                }
            },

            openEdit(item) {
                this.editFormData = { ...item };
                this.editActionUrl = '/master-data/jenis-pengadaan/' + item.id;
                this.showProgramDropdownEdit = false;
                this.showKegiatanDropdownEdit = false;
                this.showEditModal = true;
            },

            filterByProgram(progKode) {
                this.programFilter = progKode;
                let url = new URL(window.location.href);
                if (progKode === 'all') {
                    url.searchParams.delete('program');
                } else {
                    url.searchParams.set('program', progKode);
                }
                window.location.href = url.toString();
            },

            resetFilters() {
                window.location.href = '{{ route('master.jenis_pengadaan') }}';
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
                const cleanMsg = String(message || '').replace(/^[\s✅✔️☑️✓✔⚠️❌🚫⛔ℹ️🗑️✏️🔑💾]+/, '').trim();
                this.toast = { show: true, message: cleanMsg, type: type };
                setTimeout(() => { this.toast.show = false; }, 4000);
            },

            confirmDeleteForm(event, itemName) {
                event.preventDefault();
                const formElement = event.target;
                this.askConfirmation({
                    title: '🗑️ Konfirmasi Hapus Jenis Pengadaan',
                    message: 'Apakah Anda yakin ingin menghapus data jenis pengadaan SIPD ini dari master data?',
                    itemName: itemName || '',
                    type: 'danger',
                    btnText: '🗑️ Ya, Hapus Pengadaan',
                    onConfirm: () => {
                        formElement.submit();
                    }
                });
            },

            confirmAddForm(event) {
                event.preventDefault();
                const formElement = event.target;
                const itemPreview = (formElement.querySelector('[name="sub_kegiatan_nama"]')?.value || 'Sub Kegiatan Baru') + ' (' + (formElement.querySelector('[name="sub_kegiatan_kode"]')?.value || 'SIPD') + ')';
                this.askConfirmation({
                    title: '➕ Konfirmasi Tambah Pengadaan SIPD',
                    message: 'Apakah Anda yakin ingin mendaftarkan jenis pengadaan SIPD baru ini ke master data?',
                    itemName: itemPreview,
                    type: 'success',
                    btnText: '➕ Ya, Simpan Pengadaan',
                    onConfirm: () => {
                        formElement.submit();
                    }
                });
            },

            confirmEditForm(event) {
                event.preventDefault();
                const formElement = event.target;
                const itemPreview = (this.editFormData.sub_kegiatan_nama || 'Sub Kegiatan') + ' (' + (this.editFormData.sub_kegiatan_kode || 'SIPD') + ')';
                this.askConfirmation({
                    title: '✏️ Konfirmasi Simpan Perubahan Pengadaan',
                    message: 'Apakah Anda yakin ingin menyimpan perubahan data pengadaan SIPD ini?',
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
