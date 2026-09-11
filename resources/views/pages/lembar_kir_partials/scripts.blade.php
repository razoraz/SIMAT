    <script>
    function kirRuanganData() {
        return {
            searchQuery: '',
            statusFilter: 'all',
            tahunFilter: 'all',
            showPrintModal: false,
            showDetailModal: false,
            showEditModal: false,
            selectedAsset: null,
            editAsset: null,
            isSavingKondisi: false,
            showToast: false,
            toastMessage: '',

            init() {
                const updateBodyScroll = () => {
                    const isOpen = this.showEditModal || this.showDetailModal || this.showPrintModal;
                    document.body.style.overflow = isOpen ? 'hidden' : '';
                };
                this.$watch('showEditModal', updateBodyScroll);
                this.$watch('showDetailModal', updateBodyScroll);
                this.$watch('showPrintModal', updateBodyScroll);
            },

            // Form Ubah Kondisi
            editForm: {
                kondisi: 'Baik'
            },

            // Data Aset Ruangan
            assets: {!! json_encode($assets ?? []) !!},

            // Dokumen Pengesahan KIR
            kirDoc: {
                nomor_surat: '000.2.3.2/KIR/{{ $currentUnit->id ?? 1 }}/430.10.7/2026',
                pj_nama: {!! json_encode($unitKepala) !!},
                pj_nip: {!! json_encode($unitNip) !!},
                pj_jabatan: {!! json_encode('Penanggung Jawab ' . $unitNama) !!},
                pengurus_nama: 'BUDI HARTONO, S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang Pengguna RSUD',
                direktur_nama: 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR',
                direktur_nip: '19771002 200604 1 006',
                direktur_jabatan: 'Direktur RSUD dr. H. Koesnandi Bondowoso',
                kota_tanggal: 'Bondowoso, {{ date('d F Y') }}'
            },

            get filteredAssets() {
                const q = (this.searchQuery || '').toLowerCase();
                return this.assets.filter(a => {
                    const matchSearch = !q || 
                        (a.nama || '').toLowerCase().includes(q) || 
                        (a.kode || '').toLowerCase().includes(q) || 
                        (a.kode_108 || '').toLowerCase().includes(q) || 
                        (a.nibar || '').toLowerCase().includes(q) || 
                        (a.merk || '').toLowerCase().includes(q) ||
                        (a.no_seri || '').toLowerCase().includes(q);

                    const matchStatus = this.statusFilter === 'all' || a.kondisi === this.statusFilter;
                    const matchTahun = this.tahunFilter === 'all' || String(a.tahun) === String(this.tahunFilter);

                    return matchSearch && matchStatus && matchTahun;
                });
            },

            get countBaik() {
                return this.assets.filter(a => a.kondisi === 'Baik').length;
            },
            get countKurangBaik() {
                return this.assets.filter(a => a.kondisi === 'Kurang Baik' || a.kondisi === 'Rusak Ringan').length;
            },
            get countRusakBerat() {
                return this.assets.filter(a => a.kondisi === 'Rusak Berat' || a.kondisi === 'Rusak').length;
            },

            get persentaseBaik() {
                return this.assets.length ? ((this.countBaik / this.assets.length) * 100).toFixed(1) + '% Siap Digunakan' : '0%';
            },

            openDetail(ast) {
                this.selectedAsset = ast;
                this.showDetailModal = true;
            },

            openEditKondisi(ast) {
                this.editAsset = ast;
                this.editForm.kondisi = ast.kondisi || 'Baik';
                this.showEditModal = true;
            },

            async saveKondisi() {
                if (!this.editAsset) return;
                this.isSavingKondisi = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                try {
                    const res = await fetch(`/lembar-kir-ruangan/kondisi/${this.editAsset.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            kondisi: this.editForm.kondisi
                        })
                    });
                    const data = await res.json();
                    this.isSavingKondisi = false;

                    if (data.success) {
                        // Update state di array assets
                        const target = this.assets.find(a => a.id === this.editAsset.id);
                        if (target) {
                            target.kondisi = data.kondisi;
                        }
                        if (this.selectedAsset && this.selectedAsset.id === this.editAsset.id) {
                            this.selectedAsset.kondisi = data.kondisi;
                        }
                        this.showEditModal = false;
                        this.triggerToast(data.message || 'Kondisi barang berhasil diperbarui!');
                    } else {
                        alert('⚠️ ' + (data.message || 'Gagal mengubah kondisi barang.'));
                    }
                } catch(err) {
                    this.isSavingKondisi = false;
                    alert('⚠️ Terjadi kendala saat menyimpan perubahan kondisi.');
                }
            },

            triggerToast(msg) {
                this.toastMessage = msg;
                this.showToast = true;
                setTimeout(() => { this.showToast = false; }, 3500);
            },

            printKir() {
                window.print();
            },

            formatRupiah(num) {
                return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
            }
        };
    }
    </script>
