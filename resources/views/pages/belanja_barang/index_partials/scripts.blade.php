<script>
    function masterBelanjaBarang() {
        return {
            selectedItem: null,
            detailModalOpen: false,
            confirmDeleteModalOpen: false,
            deleteId: null,
            deleteTitle: '',
            isDeleting: false,

            openDetailModal(item) {
                this.selectedItem = item;
                this.detailModalOpen = true;
            },

            closeDetailModal() {
                this.detailModalOpen = false;
                setTimeout(() => {
                    this.selectedItem = null;
                }, 200);
            },

            openConfirmDelete(id, title) {
                this.deleteId = id;
                this.deleteTitle = title;
                this.confirmDeleteModalOpen = true;
            },

            closeConfirmDelete() {
                this.confirmDeleteModalOpen = false;
                this.deleteId = null;
                this.deleteTitle = '';
                this.isDeleting = false;
            },

            async executeDelete() {
                if (!this.deleteId) return;
                this.isDeleting = true;

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const res = await fetch(`{{ url('/master-data/belanja-barang') }}/${this.deleteId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        }
                    });

                    const data = await res.json();
                    if (res.ok && data.success) {
                        this.closeConfirmDelete();
                        window.location.reload();
                    } else {
                        alert(data.message || 'Gagal menghapus data belanja barang.');
                        this.isDeleting = false;
                    }
                } catch (e) {
                    console.error('Delete error:', e);
                    alert('Terjadi kesalahan jaringan saat menghapus data.');
                    this.isDeleting = false;
                }
            },

            formatRupiah(val) {
                if (!val || isNaN(val)) return '0';
                return new Intl.NumberFormat('id-ID').format(Math.round(val));
            }
        };
    }
</script>
