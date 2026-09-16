    <script>
        function unitPaviliunCatalog() {
            return {
                searchQuery: '',
                viewMode: 'grid', // 'grid' or 'table'
                showAddModal: false,
                showEditModal: false,
                showDetailModal: false,
                showPrintKIRModal: false,
                showEditKIRForm: false,
                selectedUnit: null,

                // Data Dokumen Cetak KIR
                kirDoc: {
                    nomor_surat: '028/KIR-RSUD/2026',
                    tanggal_pengesahan: '10 Januari 2026',
                    pj_nama: '',
                    pj_nip: '19880512 201201 2 004',
                    pj_jabatan: 'Kepala / Penanggung Jawab Ruangan',
                    pengurus_nama: 'BUDI HARTONO, S.Sos',
                    pengurus_nip: '19760229 200801 1 010',
                    pengurus_jabatan: 'Pengurus Barang Pengelola Aset',
                    direktur_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                    direktur_nip: '19771002 200604 1 006'
                },

                // Filter di dalam Modal Detail Aset Ruangan
                detailSearchQuery: '',
                detailKondisiFilter: 'all',
                detailCategoryFilter: 'all',

                // Role & Unit ID Pengguna Login
                currentUserRole: {{ Js::from(Auth::user()->role ?? '') }},
                currentUserUnitId: {{ Js::from(Auth::user()->unit_id ?? null) }},

                // Data Unit & Paviliun RSUD diambil dinamis langsung dari Database Tabel Users (Role Sub Admin)
                units: {{ Js::from($units ?? []) }},

                // Helper: Cek apakah tombol KIR boleh ditampilkan untuk unit tertentu
                canAccessKir(item) {
                    if (!item) return false;
                    // Master Admin dan Admin dapat melihat seluruh KIR unit
                    if (this.currentUserRole === 'master_admin' || this.currentUserRole === 'admin') {
                        return true;
                    }
                    // Sub Admin hanya boleh melihat KIR pada ruangannya sendiri
                    if (this.currentUserRole === 'sub_admin') {
                        return this.currentUserUnitId && Number(item.id) === Number(this.currentUserUnitId);
                    }
                    return false;
                },

                get filteredUnits() {
                    const query = (this.searchQuery || '').toLowerCase();
                    return this.units.filter(item => {
                        return (item.nama || '').toLowerCase().includes(query) || 
                               (item.kode || '').toLowerCase().includes(query) ||
                               (item.kepala || '').toLowerCase().includes(query);
                    });
                },

                // Filter aset di dalam modal detail ruangan
                get filteredDetailAssets() {
                    if (!this.selectedUnit || !this.selectedUnit.assets) return [];
                    const query = (this.detailSearchQuery || '').toLowerCase();
                    return this.selectedUnit.assets.filter(ast => {
                        const matchSearch = (ast.nama || '').toLowerCase().includes(query) ||
                                            (ast.kode || '').toLowerCase().includes(query) ||
                                            (ast.merk || '').toLowerCase().includes(query) ||
                                            (ast.no_seri || '').toLowerCase().includes(query);
                        const matchKondisi = this.detailKondisiFilter === 'all' || ast.kondisi === this.detailKondisiFilter;
                        const matchCategory = this.detailCategoryFilter === 'all' || ast.category === this.detailCategoryFilter;
                        return matchSearch && matchKondisi && matchCategory;
                    });
                },

                formatRupiah(number) {
                    if (number === null || number === undefined) return '0';
                    const num = Number(number);
                    if (isNaN(num)) return '0';
                    return new Intl.NumberFormat('id-ID').format(num);
                },

                resetFilters() {
                    this.searchQuery = '';
                },

                openDetail(item) {
                    this.selectedUnit = item;
                    this.detailSearchQuery = '';
                    this.detailKondisiFilter = 'all';
                    this.detailCategoryFilter = 'all';
                    this.showDetailModal = true;
                },

                openPrintKIR(item) {
                    this.selectedUnit = item ? { ...item } : (this.selectedUnit || this.units[0]);
                    this.kirDoc.pj_nama = this.selectedUnit.pj_aset || this.selectedUnit.kepala;
                    this.showPrintKIRModal = true;
                },

                printCurrentKIR() {
                    window.print();
                },

                openEdit(item) {
                    this.selectedUnit = { ...item };
                    this.showEditModal = true;
                },

                showConfirmModal: false,
                confirmData: {
                    title: 'Konfirmasi Tindakan',
                    message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                    itemName: '',
                    type: 'danger',
                    btnText: 'Ya, Lanjutkan',
                    isBlocked: false,
                    actionUrl: null,
                    actionText: '🔄 Ajukan Mutasi Barang Terlebih Dahulu',
                    assetWarning: null,
                    onConfirm: null
                },

                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                askConfirmation({ title, message, itemName, type = 'danger', btnText, assetWarning = null, isBlocked = false, actionUrl = null, actionText = null, onConfirm }) {
                    this.confirmData = {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        type: type,
                        btnText: isBlocked ? null : (btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan'))),
                        isBlocked: Boolean(isBlocked),
                        actionUrl: actionUrl,
                        actionText: actionText || '🔄 Ajukan Mutasi Barang Terlebih Dahulu',
                        assetWarning: assetWarning,
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

                deleteUnit(item) {
                    if (!item) return;

                    const totalAset = Number(item.total_aset || (item.assets ? item.assets.length : 0));
                    const hasAssets = totalAset > 0;
                    const nilaiFmt = item.total_nilai || 'Rp 0';

                    // JIKA UNIT/RUANGAN MEMILIKI ASET: BLOKIR PENGHAPUSAN DAN TAMPILKAN LINK AJUKAN MUTASI
                    if (hasAssets) {
                        this.askConfirmation({
                            title: '🚫 Unit Tidak Dapat Dihapus!',
                            message: `Ruangan "${item.nama}" saat ini TIDAK DAPAT DIHAPUS karena masih tercatat menampung ${totalAset} barang inventaris/aset (${nilaiFmt}) di database RSUD.`,
                            itemName: `${item.nama} (${item.kode || 'UNIT'}) — ⚠️ Masih Memiliki ${totalAset} Aset Aktif`,
                            type: 'danger',
                            isBlocked: true,
                            actionUrl: '/mutasi-aset',
                            actionText: '🔄 Ajukan Mutasi Barang Terlebih Dahulu',
                            assetWarning: `Sistem mendeteksi bahwa unit/ruangan ini masih memegang ${totalAset} aset aktif bernilai ${nilaiFmt}. Demi akuntabilitas dan pencegahan kehilangan aset RSUD Koesnadi, unit yang masih memiliki aset tidak diperkenankan untuk dihapus. Silakan ajukan proses mutasi seluruh aset ke ruangan lain terlebih dahulu sampai ruangan ini kosong.`,
                            btnText: null,
                            onConfirm: null
                        });
                        return;
                    }

                    this.askConfirmation({
                        title: '⚠️ Konfirmasi Pindahkan Unit ke Tong Sampah',
                        message: 'Apakah Anda yakin ingin menghapus data unit / ruangan ini dari master data RSUD? Data akan dipindahkan ke Pusat Data Terhapus dan akun Sub-Admin terkait dinonaktifkan.',
                        itemName: (item.nama || 'Unit') + ' (' + (item.kode || 'UNIT') + ') — Tidak ada aset',
                        type: 'danger',
                        isBlocked: false,
                        btnText: '🗑️ Ya, Pindahkan ke Tong Sampah',
                        assetWarning: null,
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                const res = await fetch('/unit-paviliun/' + item.id, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                                });
                                const d = await res.json();
                                if (d.success) {
                                    this.showSimatToast(d.message || 'Unit berhasil dipindahkan ke tong sampah.', 'success');
                                    setTimeout(() => window.location.reload(), 600);
                                } else {
                                    this.showSimatToast(d.message || 'Gagal menghapus unit.', 'error');
                                }
                            } catch(err) {
                                window.location.reload();
                            }
                        }
                    });
                }
            };
        }
    </script>
