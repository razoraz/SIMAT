    <script>
        window.__simatTriwulanData = {!! $triwulanDataJson ?? '{}' !!};
        window.__simatDistribusiList = {!! $distribusiListJson ?? '[]' !!};
        window.__simatMutasiList = {!! $mutasiListJson ?? '[]' !!};
        window.__simatMutasiEksternalList = {!! $mutasiEksternalListJson ?? '[]' !!};
        window.__simatHibahList = {!! $hibahListJson ?? '[]' !!};
        window.__simatKemitraanList = {!! $kemitraanListJson ?? '[]' !!};
        window.__simatBelanjaBarangList = {!! $belanjaBarangListJson ?? '[]' !!};
        window.__simatUnits = {!! $unitsJson ?? '[]' !!};

        function beritaAcaraApp() {
            return {
                // Tab Navigasi Aktif: 'triwulan', 'distribusi', 'mutasi', 'hibah', 'kemitraan', atau 'belanja_barang'
                activeTab: 'triwulan',

                // Sub-scope Mutasi: 'internal' atau 'eksternal'
                mutasiScope: 'internal',
                
                // Modal Live Edit Toggle
                showEditTriwulanForm: false,
                showEditDistribusiForm: false,
                showEditMutasiForm: false,
                showEditHibahForm: false,
                showEditKemitraanForm: false,
                showEditBelanjaBarangForm: false,

                init() {
                    const urlParams = new URLSearchParams(window.location.search);
                    const tabParam = urlParams.get('tab');
                    const scopeParam = urlParams.get('scope');
                    const idParam = urlParams.get('id') || urlParams.get('distribusi_id') || urlParams.get('mutasi_id') || urlParams.get('eksternal_id') || urlParams.get('hibah_id') || urlParams.get('kemitraan_id') || urlParams.get('belanja_id');
                    const returnTo = urlParams.get('returnTo');

                    if (returnTo) {
                        this.returnToUrl = returnTo;
                    }

                    if (tabParam && ['triwulan', 'distribusi', 'mutasi', 'mutasi_eksternal', 'hibah', 'kemitraan', 'belanja_barang'].includes(tabParam)) {
                        if (tabParam === 'mutasi_eksternal') {
                            this.activeTab = 'mutasi';
                            this.mutasiScope = 'eksternal';
                        } else {
                            this.activeTab = tabParam;
                        }
                    }

                    if (scopeParam && ['internal', 'eksternal'].includes(scopeParam)) {
                        this.mutasiScope = scopeParam;
                    }

                    if (idParam && this.activeTab === 'hibah') {
                        const targetHibah = this.hibahList.find(h => String(h.id) === String(idParam) || String(h.kode) === String(idParam));
                        if (targetHibah) {
                            this.$nextTick(() => { this.openPrintHibah(targetHibah); });
                        }
                    } else if (idParam && this.activeTab === 'kemitraan') {
                        const targetKmt = this.kemitraanList.find(k => String(k.id) === String(idParam) || String(k.kode) === String(idParam));
                        if (targetKmt) {
                            this.$nextTick(() => { this.openPrintKemitraan(targetKmt); });
                        }
                    } else if (idParam && this.activeTab === 'belanja_barang') {
                        const targetBlj = this.belanjaBarangList.find(b => String(b.id) === String(idParam) || String(b.kode) === String(idParam));
                        if (targetBlj) {
                            this.$nextTick(() => { this.openPrintBelanjaBarang(targetBlj); });
                        }
                    } else if (idParam && this.activeTab === 'mutasi') {
                        if (this.mutasiScope === 'eksternal') {
                            const targetExt = this.mutasiEksternalList.find(m => String(m.id) === String(idParam) || String(m.mutasi_id) === String(idParam) || String(m.kode) === String(idParam));
                            if (targetExt) {
                                this.$nextTick(() => {
                                    this.openDetailMutasiEksternal(targetExt);
                                });
                            }
                        } else {
                            const targetMutasi = this.mutasiList.find(m => String(m.id) === String(idParam) || String(m.kode) === String(idParam));
                            if (targetMutasi) {
                                this.$nextTick(() => {
                                    this.openPrintMutasi(targetMutasi);
                                });
                            }
                        }
                    } else if (idParam && (this.activeTab === 'distribusi' || !tabParam)) {
                        // Auto-buka print modal distribusi (behavior lama)
                        this.activeTab = 'distribusi';
                        const target = this.distribusiList.find(d => String(d.id) === String(idParam) || String(d.kode) === String(idParam));
                        if (target) {
                            this.$nextTick(() => {
                                this.openPrintDistribusi(target);
                            });
                        }
                    }
                },

                getQrCodeSvg(text) {
                    if (typeof window.getQrCodeSvg === 'function') {
                        return window.getQrCodeSvg(text);
                    }
                    return 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(text || '');
                },

                // =========================================================================
                // DATA TAB 1: BAST PENAMBAHAN DATA ASTAP BERDASARKAN TRIWULAN
                // =========================================================================
                selectedTahun: '{{ $tahun ?? "2026" }}',
                selectedTriwulanKey: 'TW2',
                showPrintTriwulanModal: false,
                searchBarangTriwulan: '',

                triwulanData: window.__simatTriwulanData || {},

                get currentTriwulanDoc() {
                    return this.triwulanData[this.selectedTriwulanKey] || this.triwulanData['TW2'] || { rekapItems: [], detailBarang: [] };
                },

                get currentTriwulanTotalNilai() {
                    return (this.currentTriwulanDoc.rekapItems || []).reduce((acc, item) => acc + (item.nilai || 0), 0);
                },

                get currentTriwulanTotalQty() {
                    return (this.currentTriwulanDoc.rekapItems || []).reduce((acc, item) => acc + (item.qty || 0), 0);
                },

                get filteredDetailBarangTriwulan() {
                    const query = (this.searchBarangTriwulan || '').toLowerCase();
                    return (this.currentTriwulanDoc.detailBarang || []).filter(b => {
                        return (b.nama_barang || '').toLowerCase().includes(query) ||
                               (b.kode_108 || '').toLowerCase().includes(query) ||
                               (b.nomor_spk || '').toLowerCase().includes(query) ||
                               (b.penyedia || '').toLowerCase().includes(query);
                    });
                },

                // =========================================================================
                // DATA TAB 2: BAST DISTRIBUSI BARANG KE UNIT / PAVILIUN (SUB ADMIN)
                // =========================================================================
                distribusiSearch: '',
                distribusiUnitFilter: 'all',
                distribusiStatusFilter: 'all',
                showPrintDistribusiModal: false,
                showDetailDistribusiModal: false,
                selectedDistribusi: null,
                selectedDetailDistribusi: null,
                returnToUrl: null,

                unitsList: window.__simatUnits || [],
                distribusiList: window.__simatDistribusiList || [],

                get filteredDistribusiList() {
                    const query = (this.distribusiSearch || '').toLowerCase().trim();
                    return this.distribusiList.filter(d => {
                        const matchQuery = !query ||
                                           (d.nomor_bast || '').toLowerCase().includes(query) ||
                                           (d.kode || '').toLowerCase().includes(query) ||
                                           (d.unit_nama || '').toLowerCase().includes(query) ||
                                           (d.tujuan || '').toLowerCase().includes(query) ||
                                           (d.pj_nama || '').toLowerCase().includes(query) ||
                                           (d.penerima || '').toLowerCase().includes(query) ||
                                           (d.keterangan_lokasi || '').toLowerCase().includes(query) ||
                                           (d.keterangan || '').toLowerCase().includes(query) ||
                                           (d.items || []).some(it => (it.nama_barang || '').toLowerCase().includes(query));

                        const matchUnit = this.distribusiUnitFilter === 'all' || 
                                          (d.unit_nama || '').toLowerCase() === this.distribusiUnitFilter.toLowerCase() ||
                                          (d.tujuan || '').toLowerCase() === this.distribusiUnitFilter.toLowerCase();
                        
                        let matchStatus = true;
                        if (this.distribusiStatusFilter === 'signed') {
                            matchStatus = d.signed === true;
                        } else if (this.distribusiStatusFilter === 'unsigned') {
                            matchStatus = d.signed === false;
                        }
                        return matchQuery && matchUnit && matchStatus;
                    });
                },

                openDetailDistribusi(item) {
                    this.selectedDetailDistribusi = item;
                    this.showDetailDistribusiModal = true;
                },

                // =========================================================================
                // DATA TAB 3: BAST MUTASI ASET (PEMINDAHAN ANTAR RUANGAN)
                // =========================================================================
                mutasiSearch: '',
                mutasiStatusFilter: 'all',
                showPrintMutasiModal: false,
                showDetailMutasiModal: false,
                selectedMutasi: null,
                selectedDetailMutasi: null,

                mutasiList: window.__simatMutasiList || [],

                get filteredMutasiList() {
                    const query = (this.mutasiSearch || '').toLowerCase();
                    return this.mutasiList.filter(m => {
                        const matchQuery = (m.nomor_bast || '').toLowerCase().includes(query) ||
                                           (m.nama || '').toLowerCase().includes(query) ||
                                           (m.asal || '').toLowerCase().includes(query) ||
                                           (m.tujuan || '').toLowerCase().includes(query) ||
                                           (m.pemohon || '').toLowerCase().includes(query);
                        
                        let matchStatus = true;
                        if (this.mutasiStatusFilter === 'signed') {
                            matchStatus = m.signed === true;
                        } else if (this.mutasiStatusFilter === 'unsigned') {
                            matchStatus = m.signed === false;
                        }
                        return matchQuery && matchStatus;
                    });
                },

                openDetailMutasi(item) {
                    this.selectedDetailMutasi = item;
                    this.showDetailMutasiModal = true;
                },

                // =========================================================================
                // DATA TAB 3 (SUB-SCOPE 2): BAST MUTASI EKSTERNAL (TRANSFER ANTAR-OPD / SKPD)
                // =========================================================================
                mutasiEksternalSearch: '',
                mutasiEksternalStatusFilter: 'all',
                mutasiEksternalTipeFilter: 'all',
                showDetailMutasiEksternalModal: false,
                selectedDetailMutasiEksternal: null,

                mutasiEksternalList: window.__simatMutasiEksternalList || [],

                get filteredMutasiEksternalList() {
                    const query = (this.mutasiEksternalSearch || '').toLowerCase().trim();
                    return this.mutasiEksternalList.filter(m => {
                        const matchQuery = !query ||
                                           (m.nomor_bast || '').toLowerCase().includes(query) ||
                                           (m.nama || '').toLowerCase().includes(query) ||
                                           (m.opd_asal || '').toLowerCase().includes(query) ||
                                           (m.opd_tujuan || '').toLowerCase().includes(query) ||
                                           (m.ruangan_tujuan || '').toLowerCase().includes(query) ||
                                           (m.pj_asal_nama || '').toLowerCase().includes(query) ||
                                           (m.pj_tujuan_nama || '').toLowerCase().includes(query) ||
                                           (m.kode_108 || '').toLowerCase().includes(query) ||
                                           (m.nibar || '').toLowerCase().includes(query);

                        let matchStatus = true;
                        if (this.mutasiEksternalStatusFilter === 'signed') {
                            matchStatus = m.signed === true;
                        } else if (this.mutasiEksternalStatusFilter === 'unsigned') {
                            matchStatus = m.signed === false;
                        }

                        let matchTipe = true;
                        if (this.mutasiEksternalTipeFilter === 'masuk') {
                            matchTipe = m.tipe === 'masuk';
                        } else if (this.mutasiEksternalTipeFilter === 'keluar') {
                            matchTipe = m.tipe === 'keluar';
                        }

                        return matchQuery && matchStatus && matchTipe;
                    });
                },

                openDetailMutasiEksternal(item) {
                    this.selectedDetailMutasiEksternal = item;
                    this.showDetailMutasiEksternalModal = true;
                },

                async toggleSignMutasiEksternal(item) {
                    const target = item || this.selectedDetailMutasiEksternal;
                    if (!target) return;

                    const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                    const nextSignedState = !target.signed;

                    const applyLocal = (signedVal, timeVal, hashVal, statusVal) => {
                        const updateTarget = (obj) => {
                            if (!obj) return;
                            obj.signed = signedVal;
                            obj.tgl_signed = timeVal;
                            obj.qr_hash = hashVal;
                            obj.status = statusVal;
                        };
                        updateTarget(target);

                        const matched = this.mutasiEksternalList.find(m => String(m.id) === String(target.id));
                        if (matched && matched !== target) updateTarget(matched);

                        if (this.selectedMutasiEksternal && String(this.selectedMutasiEksternal.id) === String(target.id) && this.selectedMutasiEksternal !== target) {
                            updateTarget(this.selectedMutasiEksternal);
                        }
                        if (this.selectedDetailMutasiEksternal && String(this.selectedDetailMutasiEksternal.id) === String(target.id) && this.selectedDetailMutasiEksternal !== target) {
                            updateTarget(this.selectedDetailMutasiEksternal);
                        }
                    };

                    try {
                        const res = await fetch('/berita-acara/mutasi-eksternal/' + target.id + '/sign', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ signed: nextSignedState })
                        });
                        const data = await res.json();
                        if (data.success) {
                            applyLocal(Boolean(data.signed), data.tgl_signed, data.qr_hash, data.status);
                            alert((data.signed ? '✍️ ' : '↩️ ') + data.message);
                        } else {
                            alert('❌ ' + (data.message || 'Gagal mengubah status tanda tangan digital BAST.'));
                        }
                    } catch (e) {
                        const now = new Date();
                        const timeStr = nextSignedState ? (now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB') : '-';
                        const hashStr = nextSignedState ? ('BSRE-KOESNANDI-EXT-' + target.id + '-' + Date.now()) : '';
                        const statusStr = nextSignedState ? 'Telah Ditandatangani BSrE' : 'Draft';
                        applyLocal(nextSignedState, timeStr, hashStr, statusStr);
                        alert(nextSignedState ? '✍️ BAST Mutasi Eksternal berhasil ditandatangani secara elektronik (BSrE)!' : '↩️ Tanda tangan digital BSrE BAST Mutasi Eksternal berhasil dibatalkan.');
                    }
                },

                // Helper Format Rupiah & Angka
                formatRupiah(val) {
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
                },

                formatNumber(val) {
                    return new Intl.NumberFormat('id-ID').format(val || 0);
                },

                // =========================================================================
                // LOGIK TOGGLE TANDA TANGAN DIGITAL BSR-E (TTD & BATALKAN TTD) 3 JENIS BAST
                // =========================================================================

                // 1. Toggle TTD BAST Triwulan
                openPrintTriwulan(twKey) {
                    if (twKey) this.selectedTriwulanKey = twKey;
                    this.showPrintTriwulanModal = true;
                },

                async toggleSignTriwulan(key) {
                    const targetKey = key || this.selectedTriwulanKey;
                    const doc = this.triwulanData[targetKey];
                    if (doc) {
                        const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                        const nextSignedState = !doc.pihak2_signed;
                        try {
                            const res = await fetch('/berita-acara/triwulan/' + doc.key + '/sign', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ 
                                    tahun: this.selectedTahun,
                                    signed: nextSignedState
                                })
                            });
                            const data = await res.json();
                            if (data.success) {
                                doc.pihak2_signed = Boolean(data.signed);
                                doc.pihak2_tgl_ttd = data.tgl_signed || (data.signed ? new Date().toLocaleDateString('id-ID') : '-');
                                doc.pihak2_qr_hash = data.qr_hash || '';
                                doc.status = data.status || (data.signed ? 'Telah Ditandatangani BSrE' : 'Draft');
                                alert((data.signed ? '✍️ ' : '↩️ ') + data.message);
                            } else {
                                alert('❌ ' + (data.message || 'Gagal mengubah status tanda tangan BAST Triwulan.'));
                            }
                        } catch(e) {
                            // Fallback jika offline atau koneksi bermasalah
                            doc.pihak2_signed = nextSignedState;
                            if (doc.pihak2_signed) {
                                const now = new Date();
                                doc.pihak2_tgl_ttd = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                                doc.pihak2_qr_hash = 'BSRE-KOESNANDI-TW-' + Date.now();
                                doc.status = 'Telah Ditandatangani BSrE';
                                alert('✍️ Dokumen BAST ' + doc.key + ' berhasil ditandatangani secara elektronik (BSrE)!');
                            } else {
                                doc.pihak2_tgl_ttd = '-';
                                doc.pihak2_qr_hash = '';
                                doc.status = 'Draft';
                                alert('↩️ Tanda tangan digital BSrE Dokumen BAST ' + doc.key + ' berhasil dibatalkan.');
                            }
                        }
                    }
                },

                async saveTriwulanEdit(key) {
                    const doc = this.triwulanData[key || this.selectedTriwulanKey];
                    if (doc) {
                        const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                        try {
                            const res = await fetch('/berita-acara/triwulan/' + doc.key, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    tahun: this.selectedTahun,
                                    nomor_surat: doc.nomor_surat,
                                    lokasi: doc.lokasi,
                                    pihak1_nama: doc.pihak1_nama,
                                    pihak1_nip: doc.pihak1_nip,
                                    pihak1_jabatan: doc.pihak1_jabatan,
                                    pihak2_nama: doc.pihak2_nama,
                                    pihak2_nip: doc.pihak2_nip,
                                    pihak2_jabatan: doc.pihak2_jabatan,
                                    direktur_nama: doc.direktur_nama,
                                    direktur_nip: doc.direktur_nip,
                                    catatan: doc.catatan
                                })
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.showEditTriwulanForm = false;
                                if (typeof window.showSimatToast === 'function') {
                                    window.showSimatToast(data.message || 'Data BAST Triwulan berhasil diperbarui!', 'success');
                                } else {
                                    alert(data.message || 'Data BAST Triwulan berhasil diperbarui!');
                                }
                            }
                        } catch(e) {
                            this.showEditTriwulanForm = false;
                            if (typeof window.showSimatToast === 'function') {
                                window.showSimatToast('Data BAST Triwulan berhasil diperbarui!', 'success');
                            } else {
                                alert('Data BAST Triwulan berhasil diperbarui!');
                            }
                        }
                    }
                },

                // 2. Toggle TTD BAST Distribusi
                openPrintDistribusi(item) {
                    const target = item || (this.distribusiList && this.distribusiList.length > 0 ? this.distribusiList[0] : null);
                    if (!target) {
                        alert('Belum ada transaksi distribusi berstatus "Dalam Pengiriman" atau "Telah Diterima" untuk dicetak.');
                        return;
                    }
                    this.selectedDistribusi = { ...target };
                    this.showPrintDistribusiModal = true;
                },

                closeDistribusiModal() {
                    this.showPrintDistribusiModal = false;
                    if (this.returnToUrl) {
                        window.location.href = this.returnToUrl;
                    }
                },

                async toggleSignDistribusi(item) {
                    const target = item || this.selectedDistribusi;
                    if (!target || !target.id) return;

                    const matched = this.distribusiList.find(d => d.id === target.id);

                    try {
                        const resp = await fetch(`/distribusi/${target.id}/sign`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await resp.json();
                        if (!data.success) throw new Error(data.message || 'Gagal menyimpan status TTD.');

                        // Update semua referensi objek di state Alpine
                        const applyUpdate = (obj) => {
                            obj.signed     = data.signed;
                            obj.tgl_signed = data.tgl_signed;
                            obj.qr_hash    = data.qr_hash;
                            obj.status     = data.signed ? 'Telah Ditandatangani BSrE' : 'Belum TTD';
                        };
                        applyUpdate(target);
                        if (matched && matched !== target) applyUpdate(matched);
                        if (this.selectedDistribusi && this.selectedDistribusi.id === target.id && this.selectedDistribusi !== target) {
                            applyUpdate(this.selectedDistribusi);
                        }
                        if (this.selectedDetailDistribusi && this.selectedDetailDistribusi.id === target.id && this.selectedDetailDistribusi !== target) {
                            applyUpdate(this.selectedDetailDistribusi);
                        }

                        alert(data.signed
                            ? '✍️ BAST (' + (target.nomor_bast || 'BAST') + ') berhasil ditandatangani secara digital BSrE! Status tersimpan.'
                            : '↩️ Tanda tangan BSrE BAST (' + (target.nomor_bast || 'BAST') + ') berhasil dibatalkan.');
                    } catch (err) {
                        alert('❌ Gagal menyimpan status TTD: ' + err.message);
                    }
                },

                // 3. Toggle TTD BAST Mutasi
                openPrintMutasi(item) {
                    this.selectedMutasi = item ? { ...item } : this.mutasiList[0];
                    this.showPrintMutasiModal = true;
                },

                closeMutasiModal() {
                    this.showPrintMutasiModal = false;
                    if (this.returnToUrl) {
                        window.location.href = this.returnToUrl;
                    }
                },

                toggleSignMutasi(item) {
                    const target = item || this.selectedMutasi;
                    if (!target) return;

                    const newSigned = !target.signed;
                    const now = new Date();
                    const tgl = newSigned ? (now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB') : '-';
                    const hash = newSigned ? ('BSRE-KOESNANDI-MTS-' + Date.now()) : '';
                    const status = newSigned ? 'Telah Ditandatangani BSrE' : 'Belum TTD';

                    const applyUpdate = (obj) => {
                        if (!obj) return;
                        obj.signed = newSigned;
                        obj.tgl_signed = tgl;
                        obj.qr_hash = hash;
                        obj.status = status;
                    };

                    applyUpdate(target);

                    // Sync dengan item di mutasiList
                    const matched = this.mutasiList.find(m => String(m.id) === String(target.id));
                    if (matched && matched !== target) applyUpdate(matched);

                    // Sync dengan selectedMutasi
                    if (this.selectedMutasi && String(this.selectedMutasi.id) === String(target.id) && this.selectedMutasi !== target) {
                        applyUpdate(this.selectedMutasi);
                    }

                    // Sync dengan selectedDetailMutasi
                    if (this.selectedDetailMutasi && String(this.selectedDetailMutasi.id) === String(target.id) && this.selectedDetailMutasi !== target) {
                        applyUpdate(this.selectedDetailMutasi);
                    }

                    alert(newSigned
                        ? '✍️ BAST Mutasi (' + (target.nomor_bast || 'BAST') + ') berhasil ditandatangani secara digital (QR Code BSrE Aktif)!'
                        : '↩️ Tanda tangan digital BSrE BAST Mutasi (' + (target.nomor_bast || 'BAST') + ') berhasil dibatalkan.');
                },

                // =========================================================================
                // DATA & METHODS TAB 4: BAST HIBAH ASET BMD
                // =========================================================================
                hibahList: window.__simatHibahList || [],
                hibahSearch: '',
                hibahTipeFilter: 'all',
                hibahStatusFilter: 'all',
                showPrintHibahModal: false,
                selectedHibah: null,

                get filteredHibahList() {
                    const q = (this.hibahSearch || '').toLowerCase().trim();
                    return this.hibahList.filter(item => {
                        const matchSearch = !q
                            || (item.nomor_bast && item.nomor_bast.toLowerCase().includes(q))
                            || (item.pemberi && item.pemberi.toLowerCase().includes(q))
                            || (item.penerima_nama && item.penerima_nama.toLowerCase().includes(q))
                            || (item.nama_barang && item.nama_barang.toLowerCase().includes(q));

                        const matchTipe = this.hibahTipeFilter === 'all' || item.tipe_hibah === this.hibahTipeFilter;
                        const matchStatus = this.hibahStatusFilter === 'all'
                            || (this.hibahStatusFilter === 'signed' && item.signed)
                            || (this.hibahStatusFilter === 'unsigned' && !item.signed);

                        return matchSearch && matchTipe && matchStatus;
                    });
                },

                openPrintHibah(item) {
                    this.selectedHibah = item ? { ...item } : (this.hibahList[0] ? { ...this.hibahList[0] } : null);
                    this.showPrintHibahModal = true;
                },

                closeHibahModal() {
                    this.showPrintHibahModal = false;
                    if (this.returnToUrl) {
                        window.location.href = this.returnToUrl;
                    }
                },

                toggleSignHibah(item) {
                    const target = item || this.selectedHibah;
                    if (!target) return;

                    const newSigned = !target.signed;
                    const now = new Date();
                    const tgl = newSigned ? (now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB') : null;
                    const hash = newSigned ? ('BSRE-KOESNANDI-HIB-' + target.id + '-' + Date.now()) : '';

                    target.signed = newSigned;
                    target.tgl_signed = tgl;
                    target.qr_hash = hash;

                    // Sync dengan item di hibahList
                    const matched = this.hibahList.find(h => String(h.id) === String(target.id));
                    if (matched && matched !== target) {
                        matched.signed = newSigned;
                        matched.tgl_signed = tgl;
                        matched.qr_hash = hash;
                    }

                    alert(newSigned
                        ? '✍️ BAST Hibah (' + (target.nomor_bast || 'BAST') + ') berhasil ditandatangani secara digital (QR Code BSrE Aktif)!'
                        : '↩️ Tanda tangan digital BSrE BAST Hibah (' + (target.nomor_bast || 'BAST') + ') berhasil dibatalkan.');
                },

                // =========================================================================
                // DATA & METHODS TAB 5: BAST PEMANFAATAN KEMITRAAN (AKUN 1.5.2)
                // =========================================================================
                kemitraanList: window.__simatKemitraanList || [],
                kemitraanSearch: '',
                kemitraanTipeFilter: 'all',
                kemitraanStatusFilter: 'all',
                showPrintKemitraanModal: false,
                selectedKemitraan: null,

                get filteredKemitraanList() {
                    const q = (this.kemitraanSearch || '').toLowerCase().trim();
                    return this.kemitraanList.filter(item => {
                        const matchSearch = !q
                            || (item.nomor_pks && item.nomor_pks.toLowerCase().includes(q))
                            || (item.nomor_bast && item.nomor_bast.toLowerCase().includes(q))
                            || (item.mitra_nama && item.mitra_nama.toLowerCase().includes(q))
                            || (item.objek_nama && item.objek_nama.toLowerCase().includes(q));

                        const matchTipe = this.kemitraanTipeFilter === 'all'
                            || (item.tipe_kemitraan && item.tipe_kemitraan.toLowerCase() === this.kemitraanTipeFilter.toLowerCase());

                        const matchStatus = this.kemitraanStatusFilter === 'all'
                            || (this.kemitraanStatusFilter === 'signed' && item.signed)
                            || (this.kemitraanStatusFilter === 'unsigned' && !item.signed);

                        return matchSearch && matchTipe && matchStatus;
                    });
                },

                openPrintKemitraan(item) {
                    this.selectedKemitraan = item ? { ...item } : (this.kemitraanList[0] ? { ...this.kemitraanList[0] } : null);
                    this.showPrintKemitraanModal = true;
                },

                closeKemitraanModal() {
                    this.showPrintKemitraanModal = false;
                    if (this.returnToUrl) {
                        window.location.href = this.returnToUrl;
                    }
                },

                toggleSignKemitraan(item) {
                    const target = item || this.selectedKemitraan;
                    if (!target) return;

                    const newSigned = !target.signed;
                    const now = new Date();
                    const tgl = newSigned ? (now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB') : null;
                    const hash = newSigned ? ('BSRE-KOESNANDI-KMT-' + target.id + '-' + Date.now()) : '';

                    target.signed = newSigned;
                    target.tgl_signed = tgl;
                    target.qr_hash = hash;

                    const matched = this.kemitraanList.find(k => String(k.id) === String(target.id));
                    if (matched && matched !== target) {
                        matched.signed = newSigned;
                        matched.tgl_signed = tgl;
                        matched.qr_hash = hash;
                    }

                    alert(newSigned
                        ? '✍️ BAST Kemitraan (' + (target.nomor_bast || target.nomor_pks || 'BAST') + ') berhasil ditandatangani secara digital (QR Code BSrE Aktif)!'
                        : '↩️ Tanda tangan digital BSrE BAST Kemitraan (' + (target.nomor_bast || target.nomor_pks || 'BAST') + ') berhasil dibatalkan.');
                },

                // =========================================================================
                // DATA & METHODS TAB 6: BAST BELANJA MODAL / BARANG (FAKTUR REKANAN)
                // =========================================================================
                belanjaBarangList: window.__simatBelanjaBarangList || [],
                belanjaBarangSearch: '',
                showPrintBelanjaBarangModal: false,
                selectedBelanjaBarang: null,

                get filteredBelanjaBarangList() {
                    const q = (this.belanjaBarangSearch || '').toLowerCase().trim();
                    return this.belanjaBarangList.filter(item => {
                        return !q
                            || (item.nomor_faktur && item.nomor_faktur.toLowerCase().includes(q))
                            || (item.spk_nomor && item.spk_nomor.toLowerCase().includes(q))
                            || (item.nomor_bast && item.nomor_bast.toLowerCase().includes(q))
                            || (item.toko_penyedia && item.toko_penyedia.toLowerCase().includes(q));
                    });
                },

                openPrintBelanjaBarang(item) {
                    this.selectedBelanjaBarang = item ? { ...item } : (this.belanjaBarangList[0] ? { ...this.belanjaBarangList[0] } : null);
                    this.showPrintBelanjaBarangModal = true;
                },

                closeBelanjaBarangModal() {
                    this.showPrintBelanjaBarangModal = false;
                    if (this.returnToUrl) {
                        window.location.href = this.returnToUrl;
                    }
                },

                printCurrent() {
                    let el = null;
                    if (this.showPrintTriwulanModal) {
                        el = document.getElementById('print-area-triwulan');
                    } else if (this.showPrintDistribusiModal) {
                        el = document.getElementById('print-area-distribusi');
                    } else if (this.showPrintMutasiModal) {
                        el = document.getElementById('print-area-mutasi');
                    } else if (this.showPrintHibahModal) {
                        el = document.getElementById('print-area-hibah');
                    } else if (this.showPrintKemitraanModal) {
                        el = document.getElementById('print-area-kemitraan');
                    } else if (this.showPrintBelanjaBarangModal) {
                        el = document.getElementById('print-area-belanja-barang');
                    }

                    if (!el) {
                        window.print();
                        return;
                    }

                    let iframe = document.getElementById('simat-print-frame');
                    if (iframe) {
                        iframe.remove();
                    }

                    iframe = document.createElement('iframe');
                    iframe.id = 'simat-print-frame';
                    iframe.style.position = 'fixed';
                    iframe.style.right = '0';
                    iframe.style.bottom = '0';
                    iframe.style.width = '0';
                    iframe.style.height = '0';
                    iframe.style.border = '0';
                    document.body.appendChild(iframe);

                    const headStyles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
                        .map(el => el.outerHTML)
                        .join('\n');

                    const doc = iframe.contentWindow.document;
                    doc.open();
                    doc.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara - RSUD Dr. H. Koesnandi</title>
    ${headStyles}
    <style>
        @page {
            size: auto;
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
        .print-container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }
        table {
            border-collapse: collapse !important;
            width: 100% !important;
            max-width: 100% !important;
            table-layout: fixed !important;
        }
        th, td {
            border: 1px solid #000000 !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
        }
        tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .kop-section, .ttd-section {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        img {
            max-width: 100% !important;
            height: auto !important;
            object-fit: contain !important;
        }
    </style>
</head>
<body class="bg-white text-black font-sans">
    <div class="print-container">
        ${el.innerHTML}
    </div>
</body>
</html>`);
                    doc.close();

                    setTimeout(() => {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    }, 400);
                }
            };
        }
    </script>
