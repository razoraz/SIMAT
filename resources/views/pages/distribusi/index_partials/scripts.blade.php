    <script>
        function distribusiCatalog() {
            return {
                userRole: {{ Js::from(Auth::user()->role) }},
                userUnit: {{ Js::from(Auth::user()->unitModel?->nama ?? Auth::user()->unit ?? '') }},
                userName: {{ Js::from(Auth::user()->name) }},

                searchQuery: '',
                unitFilter: 'all',
                statusFilter: 'all',
                showDetailModal: false,
                selectedDistribusi: null,

                unitList: {{ Js::from($units ?? []) }},

                distribusis: {{ Js::from($distribusis ?? []) }},

                init() {
                    // Selalu gunakan data riil dari backend database
                    // Bersihkan cache localStorage lama agar tidak membangkitkan data hantu saat tabel di database kosong
                    try {
                        localStorage.removeItem('simat_distribusis');
                    } catch (e) {}

                    // Auto-buka modal detail jika kembali dari BAST
                    const urlParams = new URLSearchParams(window.location.search);
                    const openDetailId = urlParams.get('openDetail');
                    if (openDetailId) {
                        this.$nextTick(() => {
                            const target = this.distribusis.find(d => String(d.id) === String(openDetailId));
                            if (target) {
                                this.openDetail(target);
                            }
                        });
                        // Bersihkan parameter dari URL tanpa reload
                        const cleanUrl = window.location.pathname;
                        window.history.replaceState({}, '', cleanUrl);
                    }
                },

                saveToStorage() {
                    // Data sekarang sepenuhnya tersimpan di backend database MySQL
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

                askConfirmation({ title, message, itemName, type = 'danger', btnText, showReasonInput = false, onConfirm }) {
                    this.confirmData = {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        type: type,
                        btnText: btnText || (type === 'danger' ? 'Pindahkan ke Tong Sampah' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan')),
                        showReasonInput: showReasonInput,
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
                    const cleanMsg = String(message || '').replace(/^[\s✅✔️☑️✓✔⚠️❌🚫⛔ℹ️🗑️✏️🔑💾]+/, '').trim();
                    this.toast = { show: true, message: cleanMsg, type: type };
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },

                deleteDistribusi(item) {
                    if (!item) return;
                    const targetObj = typeof item === 'object' && item !== null ? item : this.distribusis.find(d => d.id === item);
                    const targetId = typeof item === 'object' && item !== null ? item.id : item;

                    this.askConfirmation({
                        title: 'Konfirmasi Pindahkan ke Tong Sampah',
                        message: 'Apakah Anda yakin ingin memindahkan transaksi distribusi aset ini ke Pusat Data Terhapus? Status unit register NIBAR terkait akan dikembalikan ke Gudang Aset Utama.',
                        itemName: targetObj ? ((targetObj.kode || 'DIST') + ' - ' + (targetObj.nama || 'Aset') + ' (' + (targetObj.tujuan || 'Unit') + ')') : ('ID: ' + targetId),
                        type: 'danger',
                        btnText: 'Pindahkan ke Tong Sampah',
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                const res = await fetch('/distribusi/' + targetId, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                                });
                                if (res.ok) {
                                    this.distribusis = this.distribusis.filter(d => String(d.id) !== String(targetId));
                                    this.showDetailModal = false;
                                    this.showSimatToast('Transaksi distribusi berhasil dihapus.', 'success');
                                } else {
                                    const data = await res.json().catch(() => ({}));
                                    this.showSimatToast('Gagal menghapus: ' + (data.message || 'Terjadi kesalahan.'), 'error');
                                }
                            } catch(err) {
                                this.showSimatToast('Gagal menghapus transaksi distribusi.', 'error');
                            }
                        }
                    });
                },

                unitSearchQuery: '',
                isUnitDropdownOpen: false,

                get filteredUnitDropdownList() {
                    if (!this.unitSearchQuery || this.unitSearchQuery.trim().length === 0) {
                        return this.unitList;
                    }
                    const q = this.unitSearchQuery.toLowerCase().trim();
                    return this.unitList.filter(u => 
                        (u.nama || '').toLowerCase().includes(q) ||
                        (u.kode || '').toLowerCase().includes(q) ||
                        (u.tipe || '').toLowerCase().includes(q) ||
                        (u.kepala || '').toLowerCase().includes(q)
                    );
                },

                selectUnitFilter(unit) {
                    if (!unit) {
                        this.unitFilter = 'all';
                        this.unitSearchQuery = '';
                    } else {
                        this.unitFilter = unit.nama;
                        this.unitSearchQuery = unit.nama;
                    }
                    this.isUnitDropdownOpen = false;
                },

                clearUnitFilter() {
                    this.unitFilter = 'all';
                    this.unitSearchQuery = '';
                    this.isUnitDropdownOpen = false;
                },

                get countAll() {
                    return (this.distribusis || []).length;
                },

                get countSelesai() {
                    return (this.distribusis || []).filter(d => d.status === 'Telah Diterima' || d.status === 'Diterima' || d.status === 'Selesai').length;
                },

                get countMenungguAdmin() {
                    return (this.distribusis || []).filter(d => d.status === 'Menunggu Konfirmasi' || d.status === 'Pending' || d.status === 'Menunggu Admin').length;
                },

                get countMenungguPenerima() {
                    return (this.distribusis || []).filter(d => d.status === 'Dalam Pengiriman' || d.status === 'Dikirim' || d.status === 'Menunggu Penerima').length;
                },

                get countDitolak() {
                    return (this.distribusis || []).filter(d => d.status === 'Ditolak').length;
                },

                get filteredDistribusis() {
                    const query = (this.searchQuery || '').toLowerCase().trim();
                    const role = this.userRole;
                    const myUnit = (this.userUnit || '').toLowerCase().trim();
                    const myName = (this.userName || '').toLowerCase().trim();

                    return (this.distribusis || []).filter(item => {
                        // Jika Sub Admin: hanya tampilkan distribusi yang melibatkan unit atau penerima dirinya
                        if (role === 'sub_admin' && myUnit) {
                            const isUnitMatch = (item.tujuan || '').toLowerCase().includes(myUnit) ||
                                                (item.pj_ruangan || '').toLowerCase().includes(myUnit) ||
                                                myUnit.includes((item.tujuan || '').toLowerCase());
                            const isPenerimaMatch = (item.penerima || '').toLowerCase().includes(myName) ||
                                                    (item.pj_nama || '').toLowerCase().includes(myName);
                            if (!isUnitMatch && !isPenerimaMatch) return false;
                        }

                        const matchSearch = !query ||
                                            (item.nama || '').toLowerCase().includes(query) || 
                                            (item.kode || '').toLowerCase().includes(query) || 
                                            (item.tujuan || '').toLowerCase().includes(query) || 
                                            (item.pj_ruangan || '').toLowerCase().includes(query) || 
                                            (item.penerima || '').toLowerCase().includes(query) ||
                                            (item.pj_nama || '').toLowerCase().includes(query) ||
                                            (item.nomor_bast || '').toLowerCase().includes(query) ||
                                            (item.bast_nomor || '').toLowerCase().includes(query) ||
                                            (item.items || []).some(it => 
                                                (it.nama_barang || '').toLowerCase().includes(query) ||
                                                (it.kode_barang || '').toLowerCase().includes(query) ||
                                                ((it.nibar_list || [])).some(nb => String(nb).toLowerCase().includes(query))
                                            );

                        const matchUnit = this.unitFilter === 'all' || 
                                          (item.tujuan || '').toLowerCase().includes(this.unitFilter.toLowerCase()) ||
                                          this.unitFilter.toLowerCase().includes((item.tujuan || '').toLowerCase());

                        let matchStatus = true;
                        if (this.statusFilter !== 'all') {
                            if (this.statusFilter === 'selesai' || this.statusFilter === 'Telah Diterima' || this.statusFilter === 'Diterima') {
                                matchStatus = item.status === 'Telah Diterima' || item.status === 'Diterima' || item.status === 'Selesai';
                            } else if (this.statusFilter === 'menunggu_admin' || this.statusFilter === 'Menunggu Konfirmasi' || this.statusFilter === 'Pending') {
                                matchStatus = item.status === 'Menunggu Konfirmasi' || item.status === 'Pending' || item.status === 'Menunggu Admin';
                            } else if (this.statusFilter === 'menunggu_penerima' || this.statusFilter === 'Dalam Pengiriman' || this.statusFilter === 'Dikirim') {
                                matchStatus = item.status === 'Dalam Pengiriman' || item.status === 'Dikirim' || item.status === 'Menunggu Penerima';
                            } else if (this.statusFilter === 'ditolak' || this.statusFilter === 'Ditolak') {
                                matchStatus = item.status === 'Ditolak';
                            } else {
                                matchStatus = item.status === this.statusFilter;
                            }
                        }

                        return matchSearch && matchUnit && matchStatus;
                    });
                },

                getTotalQty(items) {
                    if (!items || !items.length) return 0;
                    return items.reduce((acc, curr) => acc + (parseInt(curr.qty) || 0), 0);
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.unitFilter = 'all';
                    this.unitSearchQuery = '';
                    this.isUnitDropdownOpen = false;
                    this.statusFilter = 'all';
                },

                openDetail(item) {
                    this.selectedDistribusi = item;
                    this.showDetailModal = true;
                },

                hasAccNibar(d) {
                    if (!d || !d.items) return false;
                    return d.items.some(it => {
                        const regCount = (it.registers && it.registers.length > 0) ? it.registers.length : ((it.nibar_selected && it.nibar_selected.length > 0) ? it.nibar_selected.length : (it.qty_acc || 0));
                        return regCount > 0;
                    });
                },

                openPrintBast(item) {
                    if (!item) return;
                    const targetId = typeof item === 'object' ? item.id : item;
                    window.location.href = '/berita-acara?tab=distribusi&id=' + encodeURIComponent(targetId) + '&returnTo=' + encodeURIComponent('/distribusi?openDetail=' + targetId);
                },

                terimaDistribusi(item) {
                    this.toggleSignDistribusi(item);
                },

                async toggleSignDistribusi(item) {
                    if (!item || !item.id) return;
                    const target = this.distribusis.find(d => d.id === item.id) || item;

                    try {
                        const resp = await fetch(`/distribusi/${item.id}/sign`, {
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
                            obj.status     = data.signed ? 'Telah Diterima' : 'Menunggu Konfirmasi';
                        };
                        applyUpdate(target);
                        if (item !== target) applyUpdate(item);
                        if (this.selectedDistribusi && this.selectedDistribusi.id === item.id) {
                            applyUpdate(this.selectedDistribusi);
                        }
                        this.saveToStorage();

                        alert(data.signed
                            ? '✍️ BAST (' + (target.nomor_bast || target.kode) + ') berhasil ditandatangani secara digital BSrE! Status tersimpan.'
                            : '↩️ Tanda tangan BSrE BAST (' + (target.nomor_bast || target.kode) + ') berhasil dibatalkan.');
                    } catch (err) {
                        alert('❌ Gagal menyimpan status TTD: ' + err.message);
                    }
                },

                alasanTolak: '',

                confirmTolakDistribusi(item) {
                    if (!item) return;
                    this.alasanTolak = '';
                    const kode = item.kode || 'DIST';
                    const tujuan = item.tujuan || item.unit_nama || 'Ruangan';
                    this.askConfirmation({
                        title: '🚫 Konfirmasi Tolak Distribusi',
                        message: 'Distribusi ' + kode + ' ke unit (' + tujuan + ') akan ditolak. Semua barang NIBAR terkait akan dikembalikan ke status Tersedia di Gudang.',
                        itemName: kode + ' ➔ ' + tujuan,
                        type: 'danger',
                        btnText: '🚫 Ya, Tolak Distribusi Ini',
                        showReasonInput: true,
                        onConfirm: async () => {
                            await this.tolakDistribusi(item);
                        }
                    });
                },

                async tolakDistribusi(item) {
                    if (!item || !item.id) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    try {
                        const response = await fetch('/distribusi/' + item.id + '/status', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status: 'Ditolak', alasan_penolakan: this.alasanTolak || null, alasan_tolak: this.alasanTolak || null })
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            const clearRejectedItems = (items) => {
                                return (items || []).map(it => ({
                                    ...it,
                                    qty_acc: 0,
                                    nibar_registers: [],
                                    nibar_list: []
                                }));
                            };

                            // Update referensi objek terpilih
                            if (this.selectedDistribusi && String(this.selectedDistribusi.id) === String(item.id)) {
                                this.selectedDistribusi = {
                                    ...this.selectedDistribusi,
                                    status: 'Ditolak',
                                    alasan_penolakan: this.alasanTolak || null,
                                    bast_nomor: '(tidak diterbitkan)',
                                    nomor_bast: '(tidak diterbitkan)',
                                    signed: false,
                                    tgl_signed: '-',
                                    qr_hash: '',
                                    items: clearRejectedItems(this.selectedDistribusi.items)
                                };
                            }
                            // Update item di dalam array
                            const found = this.distribusis.find(d => String(d.id) === String(item.id));
                            if (found) {
                                found.status           = 'Ditolak';
                                found.alasan_penolakan = this.alasanTolak || null;
                                found.bast_nomor       = '(tidak diterbitkan)';
                                found.nomor_bast       = '(tidak diterbitkan)';
                                found.signed           = false;
                                found.tgl_signed       = '-';
                                found.qr_hash          = '';
                                found.items            = clearRejectedItems(found.items);
                            }
                            item.status           = 'Ditolak';
                            item.alasan_penolakan = this.alasanTolak || null;
                            item.bast_nomor       = '(tidak diterbitkan)';
                            item.nomor_bast = '(tidak diterbitkan)';
                            item.signed     = false;
                            item.tgl_signed = '-';
                            item.qr_hash    = '';
                            item.items      = clearRejectedItems(item.items);

                            // Reaktivitas array Alpine
                            this.distribusis = [...this.distribusis];
                            this.saveToStorage();
                            this.showSimatToast('Distribusi ' + (item.kode || '') + ' berhasil ditolak. Barang NIBAR dikembalikan ke Tersedia.', 'error');
                        } else {
                            this.showSimatToast('Gagal menolak distribusi: ' + (data.message || 'Terjadi kesalahan.'), 'error');
                        }
                    } catch(e) {
                        console.error(e);
                        this.showSimatToast('Terjadi kesalahan koneksi saat menolak distribusi.', 'error');
                    }
                },

                copiedNibar: null,
                copyNibar(nibar) {
                    navigator.clipboard.writeText(nibar).then(() => {
                        this.copiedNibar = nibar;
                        setTimeout(() => {
                            if (this.copiedNibar === nibar) {
                                this.copiedNibar = null;
                            }
                        }, 2000);
                    });
                },

                kondisiSaving: {},   // { reg_id: true/false }
                kondisiSaved: {},    // { reg_id: true } — tampil centang sesaat

                updateKondisiRegister(regId, newKondisi, item) {
                    if (!regId) {
                        alert('⚠️ Register ID tidak ditemukan untuk NIBAR ini. Kondisi tidak dapat disimpan ke database.');
                        return;
                    }
                    this.kondisiSaving[regId] = true;

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/distribusi/register-kondisi/' + regId, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ kondisi: newKondisi })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.kondisiSaving[regId] = false;
                        if (data.success) {
                            this.kondisiSaved[regId] = true;
                            // Update kondisi di nibar_registers item yang bersangkutan
                            if (item && item.nibar_registers) {
                                const reg = item.nibar_registers.find(r => r.reg_id == regId);
                                if (reg) reg.kondisi = newKondisi;
                            }
                            // Update kondisi ringkasan item (ambil dari register pertama)
                            if (item && item.nibar_registers && item.nibar_registers.length > 0) {
                                item.kondisi = item.nibar_registers[0].kondisi;
                            }
                            setTimeout(() => { delete this.kondisiSaved[regId]; }, 2000);
                        } else {
                            alert('⚠️ Gagal menyimpan kondisi: ' + (data.message || 'Terjadi kesalahan.'));
                        }
                    })
                    .catch(() => {
                        this.kondisiSaving[regId] = false;
                        alert('⚠️ Koneksi gagal. Pastikan server berjalan dan coba kembali.');
                    });
                },

                confirmKonfirmasiDiterima(item) {
                    if (!item) return;
                    const kode = item.kode || 'DIST';
                    const tujuan = item.tujuan || item.unit_nama || 'Ruangan';
                    this.askConfirmation({
                        title: '📦 Konfirmasi Barang Diterima',
                        message: 'Apakah barang distribusi ' + kode + ' sudah diterima dengan baik di unit / ruangan (' + tujuan + ')?',
                        itemName: kode + ' ➔ ' + tujuan,
                        type: 'success',
                        btnText: '✅ Ya, Barang Sudah Diterima',
                        onConfirm: async () => {
                            await this.updateStatusDistribusi(item, 'Telah Diterima');
                        }
                    });
                },

                async updateStatusDistribusi(item, newStatus) {
                    if (!item || !item.id) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    try {
                        const response = await fetch('/distribusi/' + item.id + '/status', {
                            method: 'PATCH',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ status: newStatus })
                        });
                        const data = await response.json();
                        if (response.ok && data.success) {
                            item.status = newStatus;
                            if (data.bast_nomor !== undefined) {
                                item.bast_nomor = data.bast_nomor || '-';
                                item.nomor_bast = data.bast_nomor || '-';
                            }
                            const target = this.distribusis.find(d => d.id === item.id);
                            if (target) {
                                target.status = newStatus;
                                if (data.bast_nomor !== undefined) {
                                    target.bast_nomor = data.bast_nomor || '-';
                                    target.nomor_bast = data.bast_nomor || '-';
                                }
                            }
                            if (this.selectedDistribusi && this.selectedDistribusi.id === item.id) {
                                this.selectedDistribusi.status = newStatus;
                                if (data.bast_nomor !== undefined) {
                                    this.selectedDistribusi.bast_nomor = data.bast_nomor || '-';
                                    this.selectedDistribusi.nomor_bast = data.bast_nomor || '-';
                                }
                            }
                            this.saveToStorage();
                            this.showSimatToast('Status distribusi ' + (item.kode || '') + ' berhasil diperbarui menjadi ' + newStatus + '!', 'success');
                        } else {
                            this.showSimatToast('Gagal memperbarui status: ' + (data.message || 'Terjadi kesalahan.'), 'error');
                        }
                    } catch(e) {
                        console.error(e);
                        this.showSimatToast('Terjadi kesalahan koneksi saat memperbarui status.', 'error');
                    }
                }
            }
        }
    </script>
