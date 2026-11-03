<script>
    function masterBelanjaBarang() {
        return {
            selectedItem: null,
            detailModalOpen: false,
            detailPenempatanFilter: 'all',
            detailKondisiFilter: 'all',
            detailSearchQuery: '',
            confirmDeleteModalOpen: false,
            deleteId: null,
            deleteTitle: '',
            deleteReason: '',
            isDeleting: false,

            // State modal aksi register NIBAR
            showEditKondisiModal: false,
            editingRegisterItem: null,
            newKondisiValue: 'Baik',
            isSavingKondisi: false,

            showRiwayatModal: false,
            selectedRiwayatRegister: null,
            selectedRiwayatMutasis: [],
            selectedRiwayatMutasisInternal: [],
            selectedRiwayatMutasisEksternal: [],
            selectedRiwayatReklas: [],
            selectedRiwayatTimeline: [],
            selectedRiwayatCounts: { internal: 0, eksternal: 0, reklas: 0, total: 0 },
            riwayatActiveTab: 'semua',
            isLoadingRiwayat: false,

            // State modal preview & download QR Code
            showQrModal: false,
            selectedQrItem: null,
            qrDataUrl: '',
            isGeneratingQr: false,

            openDetailModal(item) {
                this.selectedItem = item;
                this.detailPenempatanFilter = 'all';
                this.detailKondisiFilter = 'all';
                this.detailSearchQuery = '';
                this.detailModalOpen = true;
            },

            closeDetailModal() {
                this.detailModalOpen = false;
                setTimeout(() => {
                    this.selectedItem = null;
                    this.detailPenempatanFilter = 'all';
                    this.detailKondisiFilter = 'all';
                    this.detailSearchQuery = '';
                }, 200);
            },

            get filteredRegisters() {
                if (!this.selectedItem || !this.selectedItem.items) return [];
                let list = [];
                this.selectedItem.items.forEach(it => {
                    (it.registers || []).forEach(r => {
                        list.push({
                            ...r,
                            nama_barang: it.nama_barang,
                            kode_108: it.kode_108,
                            satuan: it.satuan || 'Unit',
                            harga_satuan: it.harga_satuan || 0
                        });
                    });
                });
                return list.filter(reg => {
                    if (this.detailPenempatanFilter === 'ruangan') {
                        if (!reg.ruang || reg.ruang === 'Gudang Perbekalan') return false;
                    } else if (this.detailPenempatanFilter === 'gudang') {
                        if (reg.ruang && reg.ruang !== 'Gudang Perbekalan') return false;
                    }

                    if (this.detailKondisiFilter !== 'all') {
                        if ((reg.kondisi || 'Baik').toLowerCase() !== this.detailKondisiFilter.toLowerCase()) return false;
                    }

                    if (this.detailSearchQuery) {
                        const q = this.detailSearchQuery.toLowerCase();
                        const matchNibar = (reg.nibar || '').toLowerCase().includes(q);
                        const matchRuang = (reg.ruang || '').toLowerCase().includes(q);
                        const matchNama = (reg.nama_barang || '').toLowerCase().includes(q);
                        const matchKode = (reg.kode_108 || '').toLowerCase().includes(q);
                        if (!matchNibar && !matchRuang && !matchNama && !matchKode) return false;
                    }

                    return true;
                });
            },

            get totalRegistersCount() {
                if (!this.selectedItem || !this.selectedItem.items) return 0;
                return this.selectedItem.items.reduce((acc, it) => acc + (it.registers ? it.registers.length : 0), 0);
            },

            // =====================================================================
            // AKSI REGISTER NIBAR: EDIT KONDISI, RIWAYAT MUTASI, HAPUS & DOWNLOAD QR
            // =====================================================================
            openEditKondisiModal(reg) {
                if (!reg) return;
                this.editingRegisterItem = reg;
                this.newKondisiValue = reg.kondisi || 'Baik';
                this.showEditKondisiModal = true;
            },

            async saveKondisiChange() {
                if (!this.editingRegisterItem || this.isSavingKondisi) return;
                const reg = this.editingRegisterItem;
                this.isSavingKondisi = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                try {
                    const res = await fetch('/astap-register/' + reg.id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            ruang_pemegang: reg.ruang || reg.ruang_pemegang || '',
                            kondisi: this.newKondisiValue
                        })
                    });

                    const data = await res.json();
                    this.isSavingKondisi = false;

                    if (res.ok && data.success) {
                        reg.kondisi = this.newKondisiValue;
                        if (this.selectedItem && this.selectedItem.items) {
                            this.selectedItem.items.forEach(it => {
                                if (it.registers) {
                                    it.registers.forEach(r => {
                                        if (r.id === reg.id || r.nibar === reg.nibar) {
                                            r.kondisi = this.newKondisiValue;
                                        }
                                    });
                                }
                            });
                        }
                        this.showEditKondisiModal = false;
                    } else {
                        alert(data.message || 'Gagal memperbarui kondisi barang.');
                    }
                } catch (err) {
                    this.isSavingKondisi = false;
                    console.error('Save kondisi error:', err);
                    alert('Terjadi kesalahan jaringan saat menyimpan kondisi barang.');
                }
            },

            async openRiwayatModal(reg) {
                if (!reg) return;
                this.riwayatActiveTab = 'semua';
                this.selectedRiwayatRegister = reg;
                this.selectedRiwayatMutasis = [];
                this.selectedRiwayatMutasisInternal = [];
                this.selectedRiwayatMutasisEksternal = [];
                this.selectedRiwayatReklas = [];
                this.selectedRiwayatTimeline = [];
                this.selectedRiwayatCounts = { internal: 0, eksternal: 0, reklas: 0, total: 0 };
                this.showRiwayatModal = true;

                try {
                    this.isLoadingRiwayat = true;
                    const res = await fetch(`/astap/register-mutasi/${reg.id}`);
                    if (res.ok) {
                        const data = await res.json();
                        if (data.success) {
                            this.selectedRiwayatMutasis = data.mutasis || [];
                            this.selectedRiwayatMutasisInternal = data.mutasis_internal || data.mutasis || [];
                            this.selectedRiwayatMutasisEksternal = data.mutasis_eksternal || [];
                            this.selectedRiwayatReklas = data.reklasifikasis || [];
                            this.selectedRiwayatTimeline = data.timeline || [];
                            this.selectedRiwayatCounts = data.counts || {
                                internal: this.selectedRiwayatMutasisInternal.length,
                                eksternal: this.selectedRiwayatMutasisEksternal.length,
                                reklas: this.selectedRiwayatReklas.length,
                                total: (data.timeline || []).length
                            };
                            if (data.kondisi) reg.kondisi = data.kondisi;
                            if (data.ruang) reg.ruang = data.ruang;
                        }
                    }
                } catch (err) {
                    console.error('Gagal memuat riwayat mutasi:', err);
                } finally {
                    this.isLoadingRiwayat = false;
                }
            },

            async deleteRegister(reg) {
                if (!reg) return;
                const nibarStr = reg.nibar || reg.no_register || 'Unit NIBAR';
                const confirmMsg = `Apakah Anda yakin ingin memindahkan unit register NIBAR "${nibarStr}" ke Pusat Data Terhapus (Recycle Bin)?`;
                if (!confirm(confirmMsg)) return;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                try {
                    const res = await fetch('/astap-register/' + reg.id, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    const data = await res.json();
                    if (res.ok && data.success) {
                        if (this.selectedItem && this.selectedItem.items) {
                            this.selectedItem.items.forEach(it => {
                                if (it.registers) {
                                    it.registers = it.registers.filter(r => r.id !== reg.id);
                                    it.jumlah_volume = it.registers.length;
                                }
                            });
                            this.selectedItem.total_volume = this.selectedItem.items.reduce((acc, it) => acc + (it.registers ? it.registers.length : 0), 0);
                        }
                    } else {
                        alert(data.message || 'Gagal menghapus unit register.');
                    }
                } catch (err) {
                    console.error('Delete register error:', err);
                    alert('Terjadi kesalahan jaringan saat menghapus unit register.');
                }
            },

            downloadQrCodeNibar(reg, parent) {
                if (!reg) return;
                this.selectedQrItem = {
                    kode_barang: reg.nibar || reg.no_register || 'ASET',
                    nama_barang: reg.nama_barang || (parent ? parent.toko_penyedia : 'Belanja Barang'),
                    tahun_perolehan: parent ? parent.tahun : new Date().getFullYear(),
                    ruang_pemegang: reg.ruang || reg.ruang_pemegang || 'Gudang Perbekalan',
                    kondisi: reg.kondisi || 'Baik'
                };
                this.showQrModal = true;
                this.generateQrImage(this.selectedQrItem);
            },

            getQrPayloadUrl(item) {
                if (!item) return '';
                return window.location.origin + '/scan/' + encodeURIComponent(item.kode_barang || '');
            },

            generateQrImage(item) {
                if (!item) return;
                this.isGeneratingQr = true;
                this.qrDataUrl = '';
                const scanUrl = this.getQrPayloadUrl(item);

                const renderQr = () => {
                    if (window.QRCode && typeof window.QRCode.toDataURL === 'function') {
                        window.QRCode.toDataURL(scanUrl, {
                            width: 320,
                            margin: 2,
                            color: { dark: '#000000', light: '#ffffff' },
                            errorCorrectionLevel: 'M'
                        }).then(url => {
                            this.qrDataUrl = url;
                            this.isGeneratingQr = false;
                        }).catch(err => {
                            this.qrDataUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=10&data=' + encodeURIComponent(scanUrl);
                            this.isGeneratingQr = false;
                        });
                    } else {
                        this.qrDataUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=10&data=' + encodeURIComponent(scanUrl);
                        this.isGeneratingQr = false;
                    }
                };

                if (this.$nextTick) {
                    this.$nextTick(() => renderQr());
                } else {
                    setTimeout(renderQr, 50);
                }
            },

            downloadQrImage() {
                if (!this.selectedQrItem || !this.qrDataUrl) return;
                const cleanFilename = 'QR_CODE_' + (this.selectedQrItem.kode_barang || 'ASET').replace(/[\/\.\s]/g, '_') + '.png';

                if (this.qrDataUrl.startsWith('data:image/')) {
                    const a = document.createElement('a');
                    a.style.display = 'none';
                    a.href = this.qrDataUrl;
                    a.download = cleanFilename;
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                    return;
                }

                fetch(this.qrDataUrl)
                    .then(resp => resp.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download = cleanFilename;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                        document.body.removeChild(a);
                    })
                    .catch(() => {
                        window.open(this.qrDataUrl, '_blank');
                    });
            },

            openReklas(astapId, searchKey) {
                if (!astapId && !searchKey) return;
                const query = searchKey ? `search=${encodeURIComponent(searchKey)}&` : '';
                window.location.href = `/astap?${query}open_reklas=${astapId || ''}`;
            },

            openConfirmDelete(id, title) {
                this.deleteId = id;
                this.deleteTitle = title;
                this.deleteReason = '';
                this.confirmDeleteModalOpen = true;
            },

            closeConfirmDelete() {
                this.confirmDeleteModalOpen = false;
                this.deleteId = null;
                this.deleteTitle = '';
                this.deleteReason = '';
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
                        },
                        body: JSON.stringify({})
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
