    <script>
        function mutasiCatalog() {
            return {
                userRole: {{ Js::from(Auth::user()?->role ?? 'admin') }},
                userUnit: {{ Js::from(Auth::user()?->unitModel?->nama ?? (Auth::user()?->unit ?? '')) }},
                userName: {{ Js::from(Auth::user()?->name ?? 'Admin') }},

                searchQuery: '',
                statusFilter: 'all',
                showDetailModal: false,

                selectedMutasi: null,

                mutasis: {{ Js::from($mutasis) }},

                get filteredMutasis() {
                    const query = (this.searchQuery || '').toLowerCase().trim();
                    const role = this.userRole;
                    const myUnit = (this.userUnit || '').toLowerCase().trim();
                    const myName = (this.userName || '').toLowerCase().trim();

                    return this.mutasis.filter(item => {
                        // Jika Sub Admin: hanya tampilkan mutasi yang melibatkan unit dia
                        if (role === 'sub_admin' && myUnit) {
                            const isAsal = (item.asal || '').toLowerCase().includes(myUnit) || 
                                           (item.pemohon || '').toLowerCase().includes(myName) ||
                                           myUnit.includes((item.asal || '').toLowerCase());
                            const isTujuan = (item.tujuan || '').toLowerCase().includes(myUnit) || 
                                             (item.penerima_pj || '').toLowerCase().includes(myName) ||
                                             myUnit.includes((item.tujuan || '').toLowerCase());
                            if (!isAsal && !isTujuan) return false;
                        }

                        // Search query
                        const matchSearch = !query ||
                            (item.nama || '').toLowerCase().includes(query) ||
                            (item.kode || '').toLowerCase().includes(query) ||
                            (item.kode_barang || '').toLowerCase().includes(query) ||
                            (item.tujuan || '').toLowerCase().includes(query) ||
                            (item.asal || '').toLowerCase().includes(query) ||
                            (item.pemohon || '').toLowerCase().includes(query) ||
                            (item.penerima_pj || '').toLowerCase().includes(query) ||
                            (item.jenis || '').toLowerCase().includes(query);

                        // Status filter tab
                        let matchStatus = true;
                        if (this.statusFilter === 'selesai') {
                            matchStatus = item.status === 'Disetujui Admin (Selesai)' || item.persetujuan_admin;
                        } else if (this.statusFilter === 'menunggu_admin') {
                            matchStatus = item.status === 'Disetujui 2 Pihak (Menunggu Admin)' || (item.persetujuan_penerima && !item.persetujuan_admin && item.status !== 'Ditolak');
                        } else if (this.statusFilter === 'menunggu_penerima') {
                            matchStatus = item.status === 'Menunggu Persetujuan Penerima' || (!item.persetujuan_penerima && item.status !== 'Ditolak');
                        } else if (this.statusFilter === 'ditolak') {
                            matchStatus = item.status === 'Ditolak';
                        }

                        return matchSearch && matchStatus;
                    });
                },

                get countAll() {
                    return (this.mutasis || []).length;
                },

                get countSelesai() {
                    return (this.mutasis || []).filter(m => m.persetujuan_admin || m.status === 'Disetujui Admin (Selesai)').length;
                },

                get countMenungguAdmin() {
                    return (this.mutasis || []).filter(m => m.status === 'Disetujui 2 Pihak (Menunggu Admin)' || (m.persetujuan_penerima && !m.persetujuan_admin && m.status !== 'Ditolak')).length;
                },

                get countMenungguPenerima() {
                    return (this.mutasis || []).filter(m => m.status === 'Menunggu Persetujuan Penerima' || (!m.persetujuan_penerima && m.status !== 'Ditolak')).length;
                },

                get countMenunggu() {
                    return this.countMenungguAdmin + this.countMenungguPenerima;
                },

                get countDitolak() {
                    return (this.mutasis || []).filter(m => m.status === 'Ditolak').length;
                },

                get countUnits() {
                    const set = new Set();
                    this.mutasis.forEach(m => {
                        if (m.asal) set.add(m.asal);
                        if (m.tujuan) set.add(m.tujuan);
                    });
                    return set.size;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.statusFilter = 'all';
                },

                init() {
                    // Auto-buka modal detail jika kembali dari halaman Berita Acara (BAMB)
                    const urlParams = new URLSearchParams(window.location.search);
                    const openDetailId = urlParams.get('openDetail');
                    if (openDetailId) {
                        this.$nextTick(() => {
                            const target = this.mutasis.find(m => String(m.id) === String(openDetailId));
                            if (target) {
                                this.openDetail(target);
                            }
                        });
                        // Bersihkan parameter dari URL tanpa reload
                        const cleanUrl = window.location.pathname;
                        window.history.replaceState({}, '', cleanUrl);
                    }
                },

                canPrint(item) {
                    if (!item) return false;
                    // Tombol Cetak / Edit BAMB hanya untuk akun Admin & Master Admin (Sub Admin dibatasi)
                    if (this.userRole === 'sub_admin') return false;
                    return (item.persetujuan_pengirim && item.persetujuan_penerima && item.persetujuan_admin) || 
                           item.status === 'Disetujui Admin (Selesai)';
                },

                isMutasiSelesai(item) {
                    if (!item) return false;
                    return item.status === 'Disetujui Admin (Selesai)' || 
                           (item.persetujuan_pengirim && item.persetujuan_penerima && item.persetujuan_admin);
                },

                askConfirmation({ title, message, itemName, type = 'danger', btnText, onConfirm }) {
                    this.$dispatch('ask-confirm', {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        type: type,
                        btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan')),
                        onConfirm: onConfirm
                    });
                },

                showSimatToast(message, type = 'success') {
                    this.$dispatch('show-toast', { message, type });
                },

                canApprovePengirim(item) {
                    if (!item || item.persetujuan_pengirim || item.status === 'Ditolak') return false;
                    if (this.userRole !== 'sub_admin') return false;
                    if (!this.userUnit) return true;
                    const myUnit = (this.userUnit || '').toLowerCase().trim();
                    const asalUnit = (item.asal || '').toLowerCase().trim();
                    return asalUnit.includes(myUnit) || myUnit.includes(asalUnit) || myUnit === asalUnit;
                },

                canApprovePenerima(item) {
                    if (!item || item.persetujuan_penerima || item.status === 'Ditolak') return false;
                    const destUnit = (item.tujuan || '').toLowerCase().trim();
                    const isReturnToGudang = item.jenis === 'Pengembalian' && (
                        destUnit.includes('perbekalan') || destUnit.includes('rumah tangga') || destUnit.includes('gudang')
                    );
                    if (isReturnToGudang) return false;
                    if (this.userRole !== 'sub_admin') return false;
                    if (!this.userUnit) return true;
                    const myUnit = (this.userUnit || '').toLowerCase().trim();
                    return destUnit.includes(myUnit) || myUnit.includes(destUnit) || myUnit === destUnit;
                },

                canApproveAdmin(item) {
                    if (!item || item.persetujuan_admin || item.status === 'Ditolak') return false;
                    return this.userRole === 'admin' || this.userRole === 'master_admin';
                },

                approvePengirim(item) {
                    if (!item) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/mutasi-aset/' + item.id + '/approve-pengirim', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(d => {
                        if (d.success) {
                            item.persetujuan_pengirim = true;
                            if (d.is_completed) {
                                item.status = 'Disetujui Admin (Selesai)';
                            } else {
                                item.status = 'Disetujui Pengirim (Menunggu Pihak Lain)';
                            }
                            this.showSimatToast('✅ Mutasi berhasil disetujui sebagai Pihak Pengirim!', 'success');
                            setTimeout(() => window.location.reload(), 800);
                        } else {
                            this.showSimatToast('⚠️ Gagal menyetujui mutasi.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('approvePengirim error:', err);
                        window.location.reload();
                    });
                },

                approvePenerima(item) {
                    if (!item) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/mutasi-aset/' + item.id + '/approve-penerima', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(d => {
                        if (d.success) {
                            item.persetujuan_penerima = true;
                            const newStatus = item.persetujuan_admin ? 'Disetujui Admin (Selesai)' : 'Disetujui 2 Pihak (Menunggu Admin)';
                            item.status = newStatus;
                            if (this.selectedMutasi && this.selectedMutasi.id === item.id) {
                                this.selectedMutasi.persetujuan_penerima = true;
                                this.selectedMutasi.status = newStatus;
                            }
                            this.showSimatToast('✅ Mutasi berhasil disetujui sebagai Pihak Penerima!', 'success');
                        } else {
                            this.showSimatToast('⚠️ Gagal menyetujui mutasi.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('approvePenerima error:', err);
                        window.location.reload();
                    });
                },

                approveAdmin(item) {
                    if (!item) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/mutasi-aset/' + item.id + '/approve-admin', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(d => {
                        if (d.success) {
                            item.persetujuan_admin = true;
                            const newStatus = item.persetujuan_penerima ? 'Disetujui Admin (Selesai)' : 'Disetujui Admin (Menunggu Penerima)';
                            item.status = newStatus;
                            if (this.selectedMutasi && this.selectedMutasi.id === item.id) {
                                this.selectedMutasi.persetujuan_admin = true;
                                this.selectedMutasi.status = newStatus;
                            }
                            this.showSimatToast('✅ Mutasi aset telah disetujui oleh Admin!', 'success');
                        } else {
                            this.showSimatToast('⚠️ Gagal menyetujui mutasi.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('approveAdmin error:', err);
                        window.location.reload();
                    });
                },

                showRejectModal: false,
                rejectTargetItem: null,
                rejectAlasan: '',

                openRejectModal(item) {
                    if (!item) return;
                    this.rejectTargetItem = item;
                    this.rejectAlasan = 'Lokasi penempatan belum siap / Kurang sesuai';
                    this.showRejectModal = true;
                },

                confirmRejectMutasi() {
                    if (!this.rejectTargetItem) return;
                    if (!this.rejectAlasan || !this.rejectAlasan.trim()) {
                        this.showSimatToast('⚠️ Silakan isi alasan penolakan terlebih dahulu.', 'warning');
                        return;
                    }
                    const item = this.rejectTargetItem;
                    const alasan = this.rejectAlasan.trim();
                    this.showRejectModal = false;

                    fetch('/mutasi-aset/' + item.id + '/reject', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ alasan_penolakan: alasan })
                    }).then(r => {
                        if (r.ok) {
                            item.status = 'Ditolak';
                            item.alasan_penolakan = alasan;
                            this.showSimatToast('🚫 Pengajuan mutasi berhasil ditolak.', 'info');
                        } else {
                            this.showSimatToast('⚠️ Gagal menolak pengajuan mutasi.', 'error');
                        }
                    }).catch(() => window.location.reload());
                },

                canCancelReject(item) {
                    if (!item || item.status !== 'Ditolak') return false;
                    if (this.userRole === 'admin' || this.userRole === 'master_admin') return true;
                    if (this.userRole === 'sub_admin') {
                        if (!this.userUnit) return true;
                        const myUnit = (this.userUnit || '').toLowerCase().trim();
                        const asal = (item.asal || '').toLowerCase().trim();
                        const tujuan = (item.tujuan || '').toLowerCase().trim();
                        return asal.includes(myUnit) || myUnit.includes(asal) || tujuan.includes(myUnit) || myUnit.includes(tujuan);
                    }
                    return false;
                },

                cancelRejectMutasi(item) {
                    if (!item) return;
                    this.askConfirmation({
                        title: 'Batalkan Penolakan Mutasi?',
                        message: 'Status penolakan akan dibatalkan, pengajuan mutasi akan aktif kembali, dan batas waktu 24 jam akan di-reset dari awal.',
                        itemName: (item.kode || 'Mutasi') + ' (' + (item.nama || '') + ')',
                        type: 'warning',
                        btnText: 'Ya, Batalkan Penolakan',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch('/mutasi-aset/' + item.id + '/cancel-reject', {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(d => {
                                if (d.success) {
                                    this.showSimatToast(d.message || '✅ Penolakan mutasi berhasil dibatalkan!', 'success');
                                    setTimeout(() => window.location.reload(), 700);
                                } else {
                                    this.showSimatToast(d.message || '⚠️ Gagal membatalkan penolakan.', 'error');
                                }
                            })
                            .catch(err => {
                                console.error('cancelReject error:', err);
                                window.location.reload();
                            });
                        }
                    });
                },

                deleteMutasi(item) {
                    if (!item) return;
                    const bNomor = item.kode || 'BAMB';
                    const targetName = (item.nama || 'Pengajuan Mutasi') + ' (' + (item.kode_barang && item.kode_barang !== '-' ? item.kode_barang : bNomor) + ')';
                    this.askConfirmation({
                        title: '⚠️ Konfirmasi Hapus Data Mutasi',
                        message: 'Apakah Anda yakin ingin menghapus data transaksi pengajuan mutasi aset ini dari sistem? Seluruh rincian register NIBAR terkait juga akan terhapus secara permanen.',
                        itemName: targetName,
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Mutasi Ini',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch('/mutasi-aset/' + item.id, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(d => {
                                if (d.success) {
                                    window.location.reload();
                                } else {
                                    this.showSimatToast('⚠️ Gagal menghapus: ' + (d.message || 'Terjadi kesalahan.'), 'error');
                                }
                            })
                            .catch(err => {
                                console.error('deleteMutasi error:', err);
                                window.location.reload();
                            });
                        }
                    });
                },

                openDetail(item) {
                    this.selectedMutasi = item;
                    this.showDetailModal = true;
                },


            };
        }
    </script>
