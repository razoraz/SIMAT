<script>
    function masterJenisPengadaan() {
        return {
            searchQuery: '{{ request('search', '') }}',
            programFilter: '{{ request('program', 'all') }}',
            showAddModal: false,
            showEditModal: false,
            editActionUrl: '',

            editFormData: {
                id: null,
                program_kode: '',
                program_nama: '',
                kegiatan_kode: '',
                kegiatan_nama: '',
                sub_kegiatan_kode: '',
                sub_kegiatan_nama: ''
            },

            openEdit(item) {
                this.editFormData = { ...item };
                this.editActionUrl = '/master-data/jenis-pengadaan/' + item.id;
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
                this.toast = { show: true, message: message, type: type };
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
