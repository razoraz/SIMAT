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

            onJenisChangeNew() {
                const lookup = {
                    '1.3.1': { nama: 'TANAH', sub_prefix: '1.3.1.01.01.01', sub_sub_prefix: '1.3.1.01.01.01.001' },
                    '1.3.2': { nama: 'PERALATAN DAN MESIN', sub_prefix: '1.3.2.02.01.01', sub_sub_prefix: '1.3.2.02.01.01.001' },
                    '1.3.3': { nama: 'GEDUNG DAN BANGUNAN', sub_prefix: '1.3.3.01.01.01', sub_sub_prefix: '1.3.3.01.01.01.001' },
                    '1.3.4': { nama: 'JALAN, IRIGASI DAN JARINGAN', sub_prefix: '1.3.4.03.01.01', sub_sub_prefix: '1.3.4.03.01.01.001' },
                    '1.3.5': { nama: 'ASET TETAP LAINNYA', sub_prefix: '1.3.5.01.01.01', sub_sub_prefix: '1.3.5.01.01.01.001' },
                    '1.3.6': { nama: 'KONSTRUKSI DALAM PENGERJAAN', sub_prefix: '1.3.6.01.01.01', sub_sub_prefix: '1.3.6.01.01.01.001' },
                    '1.5.3': { nama: 'ASET TIDAK BERWUJUD', sub_prefix: '1.5.3.01.01.01', sub_sub_prefix: '1.5.3.01.01.01.001' },
                    '1.3.7': { nama: 'ASET TETAP DALAM RENOVASI', sub_prefix: '1.3.7.01.01.01', sub_sub_prefix: '1.3.7.01.01.01.001' }
                };
                const item = lookup[this.newFormData.jenis];
                if (item) {
                    this.newFormData.nama_jenis = item.nama;
                    this.newFormData.sub_rincian_objek = item.sub_prefix;
                    this.newFormData.sub_sub_rincian_objek = item.sub_sub_prefix;
                }
            },

            onJenisChangeEdit() {
                const lookup = {
                    '1.3.1': 'TANAH',
                    '1.3.2': 'PERALATAN DAN MESIN',
                    '1.3.3': 'GEDUNG DAN BANGUNAN',
                    '1.3.4': 'JALAN, IRIGASI DAN JARINGAN',
                    '1.3.5': 'ASET TETAP LAINNYA',
                    '1.3.6': 'KONSTRUKSI DALAM PENGERJAAN',
                    '1.5.3': 'ASET TIDAK BERWUJUD',
                    '1.3.7': 'ASET TETAP DALAM RENOVASI'
                };
                if (lookup[this.editFormData.jenis]) {
                    this.editFormData.nama_jenis = lookup[this.editFormData.jenis];
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
