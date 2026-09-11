<script>
    function masterRekeningBelanja() {
        return {
            searchQuery: '{{ request('search', '') }}',
            showAddModal: false,
            showEditModal: false,
            editActionUrl: '',

            newFormData: {
                kelompok: '5.2.02',
                nama_kelompok: 'Belanja Modal Peralatan dan Mesin',
                kode_rek: '',
                nama_belanja: ''
            },

            editFormData: {
                id: null,
                kelompok: '',
                nama_kelompok: '',
                kode_rek: '',
                nama_belanja: ''
            },

            openEdit(item) {
                this.editFormData = { ...item };
                this.editActionUrl = '/master-data/rekening-belanja/' + item.id;
                this.showEditModal = true;
            },

            resetFilters() {
                window.location.href = '{{ route('master.rekening_belanja') }}';
            },

            onKelompokChangeNew() {
                const lookup = {
                    '5.2.01': 'Belanja Modal Tanah',
                    '5.2.02': 'Belanja Modal Peralatan dan Mesin',
                    '5.2.03': 'Belanja Modal Gedung dan Bangunan',
                    '5.2.04': 'Belanja Modal Jalan, Jaringan dan Irigasi',
                    '5.2.05': 'Belanja Modal Aset Tetap Lainnya',
                    '5.2.06': 'Belanja Modal Aset Tidak Berwujud'
                };
                if (lookup[this.newFormData.kelompok]) {
                    this.newFormData.nama_kelompok = lookup[this.newFormData.kelompok];
                }
            },

            onKelompokChangeEdit() {
                const lookup = {
                    '5.2.01': 'Belanja Modal Tanah',
                    '5.2.02': 'Belanja Modal Peralatan dan Mesin',
                    '5.2.03': 'Belanja Modal Gedung dan Bangunan',
                    '5.2.04': 'Belanja Modal Jalan, Jaringan dan Irigasi',
                    '5.2.05': 'Belanja Modal Aset Tetap Lainnya',
                    '5.2.06': 'Belanja Modal Aset Tidak Berwujud'
                };
                if (lookup[this.editFormData.kelompok]) {
                    this.editFormData.nama_kelompok = lookup[this.editFormData.kelompok];
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
                    title: '🗑️ Konfirmasi Hapus Rekening Belanja',
                    message: 'Apakah Anda yakin ingin menghapus data rekening belanja ini dari master data?',
                    itemName: itemName || '',
                    type: 'danger',
                    btnText: '🗑️ Ya, Hapus Rekening',
                    onConfirm: () => {
                        formElement.submit();
                    }
                });
            },

            confirmAddForm(event) {
                event.preventDefault();
                const formElement = event.target;
                const itemPreview = (formElement.querySelector('[name="nama_belanja"]')?.value || 'Rekening Baru') + ' (' + (formElement.querySelector('[name="kode_rek"]')?.value || 'Kode Rek') + ')';
                this.askConfirmation({
                    title: '➕ Konfirmasi Tambah Rekening Belanja',
                    message: 'Apakah Anda yakin ingin mendaftarkan data rekening belanja SIPD baru ini ke master data?',
                    itemName: itemPreview,
                    type: 'success',
                    btnText: '➕ Ya, Simpan Rekening',
                    onConfirm: () => {
                        formElement.submit();
                    }
                });
            },

            confirmEditForm(event) {
                event.preventDefault();
                const formElement = event.target;
                const itemPreview = (this.editFormData.nama_belanja || 'Rekening Belanja') + ' (' + (this.editFormData.kode_rek || 'Kode Rek') + ')';
                this.askConfirmation({
                    title: '✏️ Konfirmasi Simpan Perubahan Rekening',
                    message: 'Apakah Anda yakin ingin menyimpan perubahan data rekening belanja SIPD ini?',
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
