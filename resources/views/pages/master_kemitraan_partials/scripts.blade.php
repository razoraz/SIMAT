<!-- ========================================================================= -->
<!-- SCRIPTS: LOGIKA STATE MANAGEMENT MASTER KEMITRAAN ASET (AKUN 1.5.2)        -->
<!-- ========================================================================= -->
<script>
    function masterKemitraan() {
        return {
            // Modal Detail States
            showDetailModal: false,
            activeDetail: {
                kemitraan: null,
                astap: null,
                register: null
            },
            statusForm: {
                id: null,
                status_konsesi: 'Aktif',
                keterangan: ''
            },
            isUpdatingStatus: false,

            // Modal Delete States
            showDeleteModal: false,
            deleteItem: {
                id: null,
                nama: ''
            },
            isDeleting: false,

            // Helpers Formatters
            formatRupiah(value) {
                if (value === null || value === undefined || isNaN(value)) return '0';
                return new Intl.NumberFormat('id-ID').format(value);
            },

            formatTanggal(dateString) {
                if (!dateString) return '-';
                try {
                    const d = new Date(dateString);
                    if (isNaN(d.getTime())) return dateString;
                    return d.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                } catch (e) {
                    return dateString;
                }
            },

            // Buka Modal Detail & Siapkan Form Status
            openDetail(kemitraan, astap, register) {
                this.activeDetail = {
                    kemitraan: kemitraan || {},
                    astap: astap || {},
                    register: register || {}
                };
                this.statusForm = {
                    id: kemitraan ? kemitraan.id : null,
                    status_konsesi: kemitraan?.status_konsesi || 'Aktif',
                    keterangan: ''
                };
                this.showDetailModal = true;
            },

            // Simpan Pembaruan Status Konsesi Kerjasama
            async saveStatusUpdate() {
                if (!this.statusForm.id) return;
                this.isUpdatingStatus = true;

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const url = `{{ url('/master-data/kemitraan') }}/${this.statusForm.id}/status`;

                    const res = await fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status_konsesi: this.statusForm.status_konsesi,
                            keterangan: this.statusForm.keterangan
                        })
                    });

                    const json = await res.json();
                    if (json.success) {
                        alert(json.message || 'Status kerjasama berhasil diperbarui!');
                        window.location.reload();
                    } else {
                        alert(json.message || 'Gagal memperbarui status kerjasama.');
                    }
                } catch (err) {
                    console.error('Update status error:', err);
                    alert('Terjadi kesalahan jaringan atau server saat menyimpan status.');
                } finally {
                    this.isUpdatingStatus = false;
                }
            },

            // Konfirmasi Penghapusan
            confirmDelete(id, nama) {
                this.deleteItem = { id: id, nama: nama || 'Aset Kemitraan' };
                this.showDeleteModal = true;
            },

            // Eksekusi Penghapusan
            async executeDelete() {
                if (!this.deleteItem.id) return;
                this.isDeleting = true;

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const url = `{{ url('/master-data/kemitraan') }}/${this.deleteItem.id}`;

                    const res = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        }
                    });

                    const json = await res.json();
                    if (json.success) {
                        alert(json.message || 'Data Aset Kemitraan berhasil dihapus.');
                        window.location.reload();
                    } else {
                        alert(json.message || 'Gagal menghapus data.');
                    }
                } catch (err) {
                    console.error('Delete error:', err);
                    alert('Terjadi kesalahan server saat menghapus data.');
                } finally {
                    this.isDeleting = false;
                    this.showDeleteModal = false;
                }
            }
        };
    }
</script>
