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

            // State Dokumen Viewer Interaktif
            previewDocModalOpen: false,
            previewDocUrl: '',
            previewDocTitle: '',
            previewDocType: '', // 'pdf' atau 'image'
            showInlineFakturImg: false,
            showInlineKuitansiImg: false,

            // State Cetak BAST Belanja Barang (Akun 5.1.02)
            showModalPrintBast: false,
            showEditBastForm: false,
            printBastDoc: null,

            openDocPreview(url, title) {
                if (!url) return;
                const cleanUrl = String(url).split('?')[0].split('#')[0].toLowerCase();
                this.previewDocUrl = url;
                this.previewDocTitle = title || 'Dokumen Belanja Barang';
                this.previewDocType = cleanUrl.endsWith('.pdf') ? 'pdf' : 'image';
                this.previewDocModalOpen = true;
            },

            closeDocPreview() {
                this.previewDocModalOpen = false;
                setTimeout(() => {
                    this.previewDocUrl = '';
                    this.previewDocTitle = '';
                    this.previewDocType = '';
                }, 200);
            },

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
                        if (!reg.ruang || reg.ruang === 'Gudang Perbekalan' || reg.ruang === 'Belum Ditempatkan') return false;
                    } else if (this.detailPenempatanFilter === 'gudang') {
                        if (reg.ruang && reg.ruang !== 'Gudang Perbekalan' && reg.ruang !== 'Belum Ditempatkan') return false;
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

            deleteRegister(reg) {
                if (!reg || !reg.id) return;
                const nibarStr = reg.nibar || reg.no_register || 'Unit NIBAR';
                const namaBarang = reg.nama_barang || this.selectedItem?.toko_penyedia || 'Aset Belanja Barang';
                const hargaNominal = reg.harga_satuan ? ('Rp ' + Number(reg.harga_satuan).toLocaleString('id-ID')) : 'Rp 0';

                const performDelete = async () => {
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

                                // Force Reaktivitas Alpine.js agar getter filteredRegisters segera ter-render ulang
                                this.selectedItem = JSON.parse(JSON.stringify(this.selectedItem));
                            }

                            // Jika NIBAR terakhir habis dan faktur / master ASTAP otomatis dipindahkan ke Recycle Bin
                            if (data.astap_auto_deleted || (this.selectedItem && this.selectedItem.total_volume <= 0)) {
                                this.closeDetailModal();
                                if (typeof window.showSimatToast === 'function') {
                                    window.showSimatToast(data.message || 'Semua unit NIBAR telah dihapus — faktur belanja barang otomatis dipindahkan ke Recycle Bin.', 'success');
                                }
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1000);
                            } else {
                                if (typeof window.showSimatToast === 'function') {
                                    window.showSimatToast(data.message || `Unit register NIBAR "${nibarStr}" berhasil dipindahkan ke Recycle Bin.`, 'success');
                                }
                            }
                        } else if (data.is_blocked) {
                            if (typeof window.askSimatConfirm === 'function') {
                                window.askSimatConfirm({
                                    title: 'Unit Tidak Dapat Dihapus',
                                    message: data.message,
                                    itemName: 'NIBAR: ' + nibarStr,
                                    type: 'danger',
                                    isBlocked: true,
                                    actionUrl: data.action_url || '/mutasi-aset',
                                    actionText: data.action_text || 'Ajukan Mutasi Aset',
                                    btnText: null,
                                    onConfirm: null
                                });
                            } else {
                                alert(data.message);
                            }
                        } else {
                            if (typeof window.showSimatToast === 'function') {
                                window.showSimatToast(data.message || 'Gagal menghapus unit register.', 'error');
                            } else {
                                alert(data.message || 'Gagal menghapus unit register.');
                            }
                        }
                    } catch (err) {
                        console.error('Delete register error:', err);
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast('Terjadi kesalahan jaringan saat menghapus unit register.', 'error');
                        } else {
                            alert('Terjadi kesalahan jaringan saat menghapus unit register.');
                        }
                    }
                };

                // Buka modal konfirmasi kustom SIMAT-RK Bespoke
                if (typeof window.askSimatConfirm === 'function') {
                    window.askSimatConfirm({
                        title: 'Konfirmasi Pindahkan ke Tong Sampah',
                        message: `Apakah Anda yakin ingin memindahkan unit register NIBAR "${nibarStr}" ke Pusat Data Terhapus (Recycle Bin)?`,
                        itemName: 'NIBAR: ' + nibarStr,
                        itemDetails: {
                            nama: namaBarang,
                            kode: nibarStr,
                            badgeText: '1 UNIT REGISTER',
                            totalAset: 1,
                            nilaiFmt: hargaNominal
                        },
                        type: 'danger',
                        isBlocked: false,
                        btnText: 'Pindahkan ke Tong Sampah',
                        onConfirm: performDelete
                    });
                } else {
                    performDelete();
                }
            },

            downloadQrCodeNibar(reg, parent) {
                if (!reg) return;
                this.selectedQrItem = {
                    kode_barang: reg.nibar || reg.no_register || 'ASET',
                    nama_barang: reg.nama_barang || (parent ? parent.toko_penyedia : 'Belanja Barang'),
                    tahun_perolehan: parent ? parent.tahun : new Date().getFullYear(),
                    ruang_pemegang: reg.ruang || reg.ruang_pemegang || 'Belum Ditempatkan',
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
            },

            // =====================================================================
            // FITUR CETAK BAST BELANJA BARANG RESMI (AKUN 5.1.02)
            // =====================================================================
            openPrintBastModal(item) {
                if (!item) return;

                const hariMap = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const bulanMap = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                let dateObj = new Date();
                if (item.tanggal_faktur_raw) {
                    const parsed = new Date(item.tanggal_faktur_raw);
                    if (!isNaN(parsed.getTime())) dateObj = parsed;
                }

                const hari = hariMap[dateObj.getDay()] || 'Senin';
                const tglAngka = dateObj.getDate();
                const bulan = bulanMap[dateObj.getMonth() + 1] || 'Januari';
                const tahun = dateObj.getFullYear();

                const id = item.id || 1;
                const nomorBast = `000.2.3.2/BAST-BLJ/${String(id).padStart(3, '0')}/430.10.7/${tahun}`;

                let itemsList = [];
                if (item.items && Array.isArray(item.items) && item.items.length > 0) {
                    itemsList = item.items.map((it, idx) => ({
                        id: it.id,
                        nama_barang: it.nama_barang || 'Barang Milik Daerah',
                        kode_108: it.kode_108 || '-',
                        jumlah_volume: it.jumlah_volume || 1,
                        satuan: it.satuan || 'Unit',
                        harga_satuan: it.harga_satuan || 0,
                        subtotal: it.subtotal || (it.jumlah_volume * it.harga_satuan) || 0,
                        ruangan: it.ruangan || 'Belum Ditempatkan',
                        registers: it.registers || []
                    }));
                } else {
                    itemsList = [{
                        id: item.id || 1,
                        nama_barang: item.first_nama || 'Barang Belanja',
                        kode_108: '-',
                        jumlah_volume: item.total_volume || 1,
                        satuan: 'Unit',
                        harga_satuan: item.total_pembelian || 0,
                        subtotal: item.total_pembelian || 0,
                        ruangan: 'Belum Ditempatkan',
                        registers: []
                    }];
                }

                const totalVol = item.total_volume || itemsList.reduce((acc, it) => acc + (parseInt(it.jumlah_volume) || 0), 0);
                const totalBeli = item.total_pembelian || itemsList.reduce((acc, it) => acc + (parseFloat(it.subtotal) || 0), 0);
                const terbilangStr = (terbilangBelanjaJs(totalBeli).trim() || 'Nol') + ' Rupiah';

                this.printBastDoc = {
                    id: item.id,
                    nomor_faktur: item.nomor_faktur || '-',
                    tanggal_faktur: item.tanggal_faktur || `${tglAngka} ${bulan} ${tahun}`,
                    nomor_bast: nomorBast,
                    hari: hari,
                    tgl_angka: tglAngka,
                    bulan: bulan,
                    tahun: tahun,
                    toko_penyedia: item.toko_penyedia || '-',
                    pihak_1_nama: item.toko_penyedia || 'Toko Rekanan Penyedia',
                    pihak_1_jabatan: 'Pimpinan / Rekanan Penyedia',
                    pihak_2_nama: 'BUDI HARTONO, S.Sos',
                    pihak_2_nip: '19760229 200801 1 010',
                    pihak_2_jabatan: 'Pengurus Barang Pengguna',
                    pihak_2_instansi: 'RSUD dr. H. Koesnandi Kabupaten Bondowoso',
                    ppk_nama: 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR.',
                    ppk_nip: '19771002 200604 1 006',
                    total_pembelian: totalBeli,
                    total_volume: totalVol,
                    status_alokasi: item.status_alokasi || 'gudang',
                    terbilang: terbilangStr,
                    keterangan: item.keterangan || '-',
                    signed: true,
                    items: itemsList
                };

                this.showModalPrintBast = true;
                this.showEditBastForm = false;
            },

            toggleSignBast() {
                if (!this.printBastDoc) return;
                this.printBastDoc.signed = !this.printBastDoc.signed;
                if (this.printBastDoc.signed) {
                    if (typeof window.showSimatToast === 'function') {
                        window.showSimatToast('✍️ BAST Belanja Barang berhasil disahkan secara digital (BSrE Aktif)!', 'success');
                    }
                } else {
                    if (typeof window.showSimatToast === 'function') {
                        window.showSimatToast('↩️ Tanda tangan digital BSrE dibatalkan.', 'info');
                    }
                }
            },

            printCurrentBast() {
                const el = document.getElementById('print-area-bast-belanja');
                if (!el) {
                    window.print();
                    return;
                }

                let iframe = document.getElementById('simat-print-frame-belanja');
                if (iframe) {
                    iframe.remove();
                }

                iframe = document.createElement('iframe');
                iframe.id = 'simat-print-frame-belanja';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                document.body.appendChild(iframe);

                const headStyles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
                    .map(elem => elem.outerHTML)
                    .join('\n');

                const doc = iframe.contentWindow.document;
                doc.open();
                doc.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BAST Belanja Barang - ${this.printBastDoc?.nomor_bast || 'RSUD Dr. H. Koesnandi'}</title>
    ${headStyles}
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        *, *::before, *::after {
            box-sizing: border-box !important;
        }
        html, body {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        table {
            border-collapse: collapse !important;
            width: 100% !important;
        }
        tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
    </style>
</head>
<body style="background:#ffffff; color:#000000; padding:0; margin:0;">
    <div style="width:100%; max-width:100%;">
        ${el.innerHTML}
    </div>
</body>
</html>`);
                doc.close();

                setTimeout(() => {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                }, 350);
            }
        };
    }

    function terbilangBelanjaJs(angka) {
        angka = Math.floor(Math.abs(Number(angka) || 0));
        const bil = ['', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas'];
        if (angka < 12) return ' ' + bil[angka];
        if (angka < 20) return terbilangBelanjaJs(angka - 10) + ' Belas';
        if (angka < 100) return terbilangBelanjaJs(Math.floor(angka / 10)) + ' Puluh' + terbilangBelanjaJs(angka % 10);
        if (angka < 200) return ' Seratus' + terbilangBelanjaJs(angka - 100);
        if (angka < 1000) return terbilangBelanjaJs(Math.floor(angka / 100)) + ' Ratus' + terbilangBelanjaJs(angka % 100);
        if (angka < 2000) return ' Seribu' + terbilangBelanjaJs(angka - 1000);
        if (angka < 1000000) return terbilangBelanjaJs(Math.floor(angka / 1000)) + ' Ribu' + terbilangBelanjaJs(angka % 1000);
        if (angka < 1000000000) return terbilangBelanjaJs(Math.floor(angka / 1000000)) + ' Juta' + terbilangBelanjaJs(angka % 1000000);
        if (angka < 1000000000000) return terbilangBelanjaJs(Math.floor(angka / 1000000000)) + ' Miliar' + terbilangBelanjaJs(angka % 1000000000);
        if (angka < 1000000000000000) return terbilangBelanjaJs(Math.floor(angka / 1000000000000)) + ' Triliun' + terbilangBelanjaJs(angka % 1000000000000);
        return ' Banyak';
    }
</script>
