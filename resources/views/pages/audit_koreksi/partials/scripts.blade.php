{{-- SCRIPTS & ALPINE DASHBOARD LOGIC AUDIT KOREKSI NILAI BMD --}}
<script>
    function auditKoreksiDashboard() {
        return {
            activeTab: '{{ in_array($selectedSubKoreksi, ['biasa', 'lkd', 'manset']) ? $selectedSubKoreksi : 'semua' }}',
            detailModalOpen: false,
            loadingDetail: false,
            detailData: {
                id: null,
                astap_id: null,
                nibar: '-',
                nama_barang: '',
                kelompok_kib: '',
                kode_108: '',
                rekening_belanja: '',
                sub_koreksi: 'biasa',
                sub_koreksi_label: '',
                tipe_koreksi: 'kurang',
                nilai_reklas: 0,
                nilai_semula: 0,
                nilai_setelah_koreksi: 0,
                tanggal_reklas: '-',
                triwulan: 1,
                tahun: {{ $selectedYear }},
                nomor_ba_reklas: '-',
                alasan_reklas: '-',
                keterangan: '-',
                dampak_rmb: null,
                user_nama: 'Administrator',
                created_at: '-',
            },

            init() {
                // Keyboard shortcut: Escape untuk menutup modal
                window.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && this.detailModalOpen) {
                        this.closeDetailModal();
                    }
                });
            },

            async openDetailModal(id) {
                this.loadingDetail = true;
                this.detailModalOpen = true;

                try {
                    const response = await fetch(`{{ url('/audit-pemulihan/koreksi') }}/${id}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('Gagal mengambil rincian data audit.');
                    }

                    const result = await response.json();
                    if (result.success && result.data) {
                        this.detailData = result.data;
                    }
                } catch (err) {
                    console.error('Error fetching detail koreksi:', err);
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal Memuat Detail',
                            text: 'Terjadi kendala saat mengambil data audit koreksi.',
                            background: '#0f172a',
                            color: '#f8fafc',
                            confirmButtonColor: '#10b981'
                        });
                    }
                } finally {
                    this.loadingDetail = false;
                }
            },

            closeDetailModal() {
                this.detailModalOpen = false;
            },

            formatRupiah(amount) {
                const val = parseFloat(amount) || 0;
                return 'Rp ' + val.toLocaleString('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        };
    }
</script>
