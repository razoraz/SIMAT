<script>
    function mutasiEksternalCatalog() {
        return {
            userRole: {{ Js::from(Auth::user()?->role ?? 'admin') }},
            userName: {{ Js::from(Auth::user()?->name ?? 'Admin') }},

            searchQuery: '',
            statusFilter: 'all',
            jenisFilter: 'all',
            showDetailModal: false,
            selectedMutasi: null,

            mutasiEksternals: {{ Js::from($mutasiEksternals) }},

            get filteredMutasis() {
                const query = (this.searchQuery || '').toLowerCase().trim();

                return this.mutasiEksternals.filter(item => {
                    // Filter Jenis Transaksi
                    if (this.jenisFilter !== 'all') {
                        if (item.jenis !== this.jenisFilter) return false;
                    }

                    // Filter Status BAST
                    if (this.statusFilter === 'selesai') {
                        if (!item.status.toLowerCase().includes('selesai') && !item.status.toLowerCase().includes('disahkan')) return false;
                    } else if (this.statusFilter === 'pinjam_aktif') {
                        if (!item.status.toLowerCase().includes('peminjaman')) return false;
                    } else if (this.statusFilter === 'menunggu_verifikasi') {
                        if (!item.status.toLowerCase().includes('menunggu')) return false;
                    }

                    // Search Query (No BAST, OPD Tujuan, Nama Aset, NIBAR, Pejabat)
                    if (query) {
                        const match =
                            (item.kode || '').toLowerCase().includes(query) ||
                            (item.opd_tujuan || '').toLowerCase().includes(query) ||
                            (item.pejabat_opd_tujuan || '').toLowerCase().includes(query) ||
                            (item.nama || '').toLowerCase().includes(query) ||
                            (item.kode_barang || '').toLowerCase().includes(query) ||
                            (item.nomor_sk_dasar || '').toLowerCase().includes(query) ||
                            (item.jenis || '').toLowerCase().includes(query) ||
                            (item.ruangan_asal || '').toLowerCase().includes(query);
                        if (!match) return false;
                    }

                    return true;
                });
            },

            get countAll() {
                return this.mutasiEksternals.length;
            },

            get countSelesai() {
                return this.mutasiEksternals.filter(m => m.status.toLowerCase().includes('selesai') || m.status.toLowerCase().includes('disahkan')).length;
            },

            get countPinjamAktif() {
                return this.mutasiEksternals.filter(m => m.status.toLowerCase().includes('peminjaman')).length;
            },

            get countMenunggu() {
                return this.mutasiEksternals.filter(m => m.status.toLowerCase().includes('menunggu')).length;
            },

            get countTransfer() {
                return this.mutasiEksternals.filter(m => m.jenis === 'Transfer Antar-OPD').length;
            },

            get countBpkad() {
                return this.mutasiEksternals.filter(m => m.jenis === 'Penyerahan ke BPKAD').length;
            },

            openDetail(item) {
                this.selectedMutasi = item;
                this.showDetailModal = true;
            },

            resetFilters() {
                this.searchQuery = '';
                this.statusFilter = 'all';
                this.jenisFilter = 'all';
            },

            cetakBast(item) {
                window.print();
            }
        };
    }
</script>
