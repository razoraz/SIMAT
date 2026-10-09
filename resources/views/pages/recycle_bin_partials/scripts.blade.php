{{-- ========================================================================= --}}
{{-- SCRIPT LOGIC RECYCLE BIN APP (ALPINE.JS CONTROLLER & HANDLERS)            --}}
{{-- ========================================================================= --}}
<script>
    function recycleBinApp() {
        return {
            activeModule: '{{ $activeTab }}',
            mutasiSubTab: 'internal',
            mutasis: {{ Js::from($deletedMutasis) }},
            mutasiEksternals: {{ Js::from($deletedMutasiEksternals ?? []) }},
            astaps: {{ Js::from($deletedAstaps) }},
            nibars: {{ Js::from($deletedNibars) }},
            distribusis: {{ Js::from($deletedDistribusis) }},
            units: {{ Js::from($deletedUnits) }},
            users: {{ Js::from($deletedUsers ?? []) }},
            hibahs: {{ Js::from($deletedHibahs ?? []) }},
            kemitraans: {{ Js::from($deletedKemitraans ?? []) }},
            belanjaBarangs: {{ Js::from($deletedBelanjaBarangs ?? []) }},
            reklas: {{ Js::from($deletedReklas ?? []) }},
            totalThisMonth: {{ (int) $totalThisMonth }},
            astapSubTab: 'packet',
            kemitraanSubTab: 'dimanfaatkan',

            selectedIds: [],
            searchQuery: '',
            timeFilter: 'all',
            showDetailModal: false,
            selectedItem: null,

            setTimeFilter(tf) {
                this.timeFilter = tf;
                this.selectedIds = [];
            },

            changeTab(tab) {
                this.activeModule = tab;
                this.selectedIds = [];
                this.searchQuery = '';
                this.timeFilter = 'all';
            },

            changeMutasiSubTab(subTab) {
                this.mutasiSubTab = subTab;
                this.selectedIds = [];
                this.searchQuery = '';
                this.timeFilter = 'all';
            },

            changeAstapSubTab(subTab) {
                this.astapSubTab = subTab;
                this.selectedIds = [];
                this.searchQuery = '';
                this.timeFilter = 'all';
            },

            changeKemitraanSubTab(subTab) {
                this.kemitraanSubTab = subTab;
                this.selectedIds = [];
                this.searchQuery = '';
                this.timeFilter = 'all';
            },

            get kemitraanDimanfaatkan() {
                return (this.kemitraans || []).filter(k => !k.is_ditambahkan);
            },

            get kemitraanDitambahkan() {
                return (this.kemitraans || []).filter(k => !!k.is_ditambahkan);
            },

            get currentTargetModule() {
                if (this.activeModule === 'mutasi') {
                    return this.mutasiSubTab === 'eksternal' ? 'mutasi_eksternal' : 'mutasi';
                }
                if (this.activeModule === 'astap' && this.astapSubTab === 'nibar') {
                    return 'nibar';
                }
                return this.activeModule;
            },

            get totalCount() {
                return this.mutasis.length + this.mutasiEksternals.length + this.astaps.length + this.nibars.length + this.distribusis.length + this.units.length + this.users.length + this.hibahs.length + this.kemitraans.length + this.belanjaBarangs.length + this.reklas.length;
            },

            getModuleCount(mod) {
                if (mod === 'mutasi') return this.mutasis.length + this.mutasiEksternals.length;
                if (mod === 'astap') return this.astaps.length + this.nibars.length;
                if (mod === 'distribusi') return this.distribusis.length;
                if (mod === 'unit') return this.units.length;
                if (mod === 'users') return this.users.length;
                if (mod === 'hibah') return this.hibahs.length;
                if (mod === 'kemitraan') return this.kemitraans.length;
                if (mod === 'belanja_barang') return this.belanjaBarangs.length;
                if (mod === 'reklas') return this.reklas.length;
                return 0;
            },

            get activeModuleName() {
                if (this.activeModule === 'mutasi') {
                    return this.mutasiSubTab === 'eksternal' ? 'Mutasi Eksternal (Antar-OPD)' : 'Mutasi Internal (Antar-Ruangan)';
                }
                if (this.activeModule === 'astap') {
                    return this.astapSubTab === 'nibar' ? 'Register NIBAR' : 'Paket Master ASTAP';
                }
                if (this.activeModule === 'kemitraan') {
                    return this.kemitraanSubTab === 'ditambahkan' 
                        ? 'Kemitraan (Aset Ditambahkan Mitra)' 
                        : 'Kemitraan (Aset Dimanfaatkan Mitra)';
                }
                const map = {
                    distribusi: 'Distribusi Aset',
                    unit: 'Unit & Paviliun',
                    users: 'Akun Pengguna',
                    hibah: 'Hibah Aset',
                    kemitraan: 'Kemitraan Aset',
                    belanja_barang: 'Belanja Barang (Akun 5.1.02)',
                    reklas: 'Reklasifikasi Aset'
                };
                return map[this.activeModule] || 'Modul';
            },

            get searchPlaceholder() {
                if (this.activeModule === 'mutasi') {
                    return this.mutasiSubTab === 'eksternal' 
                        ? 'Cari BAST / OPD asal / ruangan tujuan / nama aset / penghapus...' 
                        : 'Cari BAMB / nama aset / NIBAR / ruangan asal / tujuan / penghapus...';
                }
                if (this.activeModule === 'astap') {
                    if (this.astapSubTab === 'nibar') {
                        return 'Cari NIBAR (45-digit) / nama aset / kode 108 / ruangan / kondisi / penghapus...';
                    }
                    return 'Cari nama aset / kode 108 / NIBAR / penyedia / SPK / penghapus...';
                }
                if (this.activeModule === 'kemitraan') {
                    return this.kemitraanSubTab === 'ditambahkan'
                        ? 'Cari nomor kontrak / rekanan mitra / nama alat & mesin / merk / tipe / penghapus...'
                        : 'Cari nomor PKS / rekanan mitra / objek aset BMD / skema / penghapus...';
                }
                if (this.activeModule === 'distribusi') return 'Cari kode distribusi / BAST / unit tujuan / tanggal / penghapus...';
                if (this.activeModule === 'unit') return 'Cari kode unit / nama ruangan / kepala ruangan / NIP / penghapus...';
                if (this.activeModule === 'users') return 'Cari nama pengguna / email / NIP / role / unit penugasan / penghapus...';
                if (this.activeModule === 'hibah') return 'Cari nomor BAST / nama barang / pihak hibah / tahun / penghapus...';
                if (this.activeModule === 'belanja_barang') return 'Cari nomor faktur / toko penyedia / nama barang / ruangan / penghapus...';
                if (this.activeModule === 'reklas') return 'Cari nomor BA / nama barang / KIB asal / tujuan / kode 108 / penghapus...';
                return 'Cari data terhapus...';
            },

            get currentList() {
                if (this.activeModule === 'mutasi') {
                    return this.mutasiSubTab === 'eksternal' ? this.mutasiEksternals : this.mutasis;
                }
                if (this.activeModule === 'astap') {
                    return this.astapSubTab === 'nibar' ? this.nibars : this.astaps;
                }
                if (this.activeModule === 'distribusi') return this.distribusis;
                if (this.activeModule === 'unit') return this.units;
                if (this.activeModule === 'users') return this.users;
                if (this.activeModule === 'hibah') return this.hibahs;
                if (this.activeModule === 'kemitraan') {
                    return this.kemitraanSubTab === 'ditambahkan' ? this.kemitraanDitambahkan : this.kemitraanDimanfaatkan;
                }
                if (this.activeModule === 'belanja_barang') return this.belanjaBarangs;
                if (this.activeModule === 'reklas') return this.reklas;
                return [];
            },

            get filteredItems() {
                const q = (this.searchQuery || '').toLowerCase().trim();
                let list = this.currentList;

                // Filter Waktu Cepat (Quick Preset Ribbon: 7 Hari / 30 Hari)
                if (this.timeFilter !== 'all') {
                    const now = Date.now();
                    const days = this.timeFilter === '7d' ? 7 : 30;
                    const threshold = now - (days * 24 * 60 * 60 * 1000);
                    list = list.filter(item => {
                        if (!item.deleted_at_raw) return false;
                        const itemTime = new Date(item.deleted_at_raw).getTime();
                        return !isNaN(itemTime) && itemTime >= threshold;
                    });
                }

                if (!q) return list;

                return list.filter(item => {
                    const fields = Object.values(item).map(v => typeof v === 'string' ? v.toLowerCase() : '').join(' ');
                    return fields.includes(q);
                });
            },

            get isAllSelected() {
                return this.filteredItems.length > 0 && this.selectedIds.length === this.filteredItems.length;
            },

            toggleSelectAll(event) {
                if (event.target.checked) {
                    this.selectedIds = this.filteredItems.map(m => m.id);
                } else {
                    this.selectedIds = [];
                }
            },

            openDetail(item) {
                this.selectedItem = item;
                this.showDetailModal = true;
            },

            removeItemsFromState(targetMod, ids, extra = {}) {
                if (!Array.isArray(ids)) {
                    ids = [ids];
                }
                const idSet = new Set(ids.map(id => Number(id)));

                if (targetMod === 'mutasi' || targetMod === 'mutasi_internal') {
                    this.mutasis = this.mutasis.filter(i => !idSet.has(Number(i.id)));
                } else if (targetMod === 'mutasi_eksternal') {
                    this.mutasiEksternals = this.mutasiEksternals.filter(i => !idSet.has(Number(i.id)));
                    // Jika ada astap_id terkait yang dipulihkan, singkirkan juga dari astaps & nibars
                    const astapIds = ids.map(id => {
                        const found = this.mutasiEksternals.find(m => Number(m.id) === Number(id));
                        return found ? Number(found.astap_id) : null;
                    }).filter(Boolean);
                    if (astapIds.length > 0) {
                        const astapIdSet = new Set(astapIds);
                        this.astaps = this.astaps.filter(a => !astapIdSet.has(Number(a.id)));
                        this.nibars = this.nibars.filter(n => !astapIdSet.has(Number(n.astap_id)));
                    }
                } else if (targetMod === 'nibar') {
                    this.nibars = this.nibars.filter(i => !idSet.has(Number(i.id)));
                    if (extra && extra.astap_id && extra.astap_restored) {
                        this.astaps = this.astaps.filter(i => Number(i.id) !== Number(extra.astap_id));
                    }
                    if (extra && Array.isArray(extra.restored_parent_ids)) {
                        const parentSet = new Set(extra.restored_parent_ids.map(Number));
                        this.astaps = this.astaps.filter(i => !parentSet.has(Number(i.id)));
                    }
                } else if (targetMod === 'astap') {
                    this.astaps = this.astaps.filter(i => !idSet.has(Number(i.id)));
                    this.nibars = this.nibars.filter(n => !idSet.has(Number(n.astap_id)));
                    this.mutasiEksternals = this.mutasiEksternals.filter(m => !idSet.has(Number(m.astap_id)));
                } else if (targetMod === 'distribusi') {
                    this.distribusis = this.distribusis.filter(i => !idSet.has(Number(i.id)));
                } else if (targetMod === 'unit') {
                    this.units = this.units.filter(i => !idSet.has(Number(i.id)));
                } else if (targetMod === 'users') {
                    this.users = this.users.filter(i => !idSet.has(Number(i.id)));
                } else if (targetMod === 'hibah') {
                    this.hibahs = this.hibahs.filter(i => !idSet.has(Number(i.id)));
                } else if (targetMod === 'kemitraan') {
                    const allRemovedKemitraanIds = new Set(ids.map(Number));
                    if (extra && Array.isArray(extra.affected_kemitraan_ids)) {
                        extra.affected_kemitraan_ids.forEach(xId => allRemovedKemitraanIds.add(Number(xId)));
                    }
                    // Kumpulkan astap_id terlebih dahulu sebelum kemitraans difilter
                    const astapIds = this.kemitraans
                        .filter(k => allRemovedKemitraanIds.has(Number(k.id)))
                        .map(k => Number(k.astap_id))
                        .filter(Boolean);

                    this.kemitraans = this.kemitraans.filter(i => !allRemovedKemitraanIds.has(Number(i.id)));

                    if (astapIds.length > 0) {
                        const astapIdSet = new Set(astapIds);
                        this.astaps = this.astaps.filter(a => !astapIdSet.has(Number(a.id)));
                        this.nibars = this.nibars.filter(n => !astapIdSet.has(Number(n.astap_id)));
                    }
                } else if (targetMod === 'belanja_barang') {
                    this.belanjaBarangs = this.belanjaBarangs.filter(i => !idSet.has(Number(i.id)));
                    // Jika ada astap_id terkait yang dipulihkan, singkirkan juga dari astaps & nibars
                    const astapIds = ids.map(id => {
                        const found = this.belanjaBarangs.find(b => Number(b.id) === Number(id));
                        return found ? Number(found.astap_id) : null;
                    }).filter(Boolean);
                    if (astapIds.length > 0) {
                        const astapIdSet = new Set(astapIds);
                        this.astaps = this.astaps.filter(a => !astapIdSet.has(Number(a.id)));
                        this.nibars = this.nibars.filter(n => !astapIdSet.has(Number(n.astap_id)));
                    }
                } else if (targetMod === 'reklas' || targetMod === 'reklasifikasi') {
                    this.reklas = this.reklas.filter(i => !idSet.has(Number(i.id)));
                }

                // Kurangi total aktivitas hapus 30 hari terakhir
                this.totalThisMonth = Math.max(0, this.totalThisMonth - ids.length);

                // Hilangkan ID yang sudah diproses dari seleksi checkbox
                this.selectedIds = this.selectedIds.filter(id => !idSet.has(Number(id)));

                // Tutup modal detail jika item yang sedang dibuka terpengaruh
                if (this.selectedItem && idSet.has(Number(this.selectedItem.id))) {
                    this.showDetailModal = false;
                    this.selectedItem = null;
                }
            },

            restoreSingle(module, item) {
                if (!item) return;
                const targetMod = module || this.currentTargetModule;
                const bNomor = item.nibar || item.nomor_bast || item.kode || item.nama || 'Data';
                let confirmMsg = `Apakah Anda yakin ingin mengembalikan ${bNomor} ke status aktif? Data akan kembali muncul di katalog operasional.`;

                if (targetMod === 'nibar') {
                    confirmMsg = `Apakah Anda yakin ingin mengembalikan register NIBAR ${bNomor} ke paket pengadaan aset induk? Volume barang akan bertambah +1 unit.`;
                } else if (targetMod === 'kemitraan') {
                    if (item.is_ditambahkan) {
                        confirmMsg = `Apakah Anda yakin ingin memulihkan aset mitra "${item.nama_barang || bNomor}" ke status aktif? Jika objek pemanfaatan induknya saat ini juga berada di Tong Sampah, objek induk akan otomatis ikut dipulihkan bersamaan ke daftar aktif.`;
                    } else if (Number(item.linked_mitras_count) > 0) {
                        confirmMsg = `Apakah Anda yakin ingin memulihkan objek pemanfaatan "${item.nama_barang || bNomor}" beserta ${item.linked_mitras_count} aset mitra terkait ke status aktif?`;
                    }
                }

                this.askConfirmation({
                    title: 'Konfirmasi Pulihkan Data',
                    message: confirmMsg,
                    itemName: bNomor,
                    type: 'info',
                    btnText: 'Pulihkan Data',
                    onConfirm: () => {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        fetch(`/recycle-bin/${targetMod}/${item.id}/restore`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(async res => {
                            const d = await res.json().catch(() => ({}));
                            if (!res.ok || !d.success) {
                                throw new Error(d.message || 'Gagal memulihkan data.');
                            }
                            return d;
                        })
                        .then(d => {
                            this.showSimatToast(d.message || 'Data berhasil dipulihkan!', 'success');
                            this.removeItemsFromState(targetMod, [item.id], d);
                        })
                        .catch(err => {
                            console.error('restore error:', err);
                            this.showSimatToast(err.message || 'Gagal memulihkan data.', 'error');
                        });
                    }
                });
            },

            bulkRestore(module) {
                if (this.selectedIds.length === 0) return;
                const count = this.selectedIds.length;
                const targetMod = module || this.currentTargetModule;
                const entityName = targetMod === 'nibar' ? 'register NIBAR' : 'data';
                const targetIds = [...this.selectedIds];

                this.askConfirmation({
                    title: 'Konfirmasi Pulihkan Massal',
                    message: `Apakah Anda yakin ingin memulihkan ${count} ${entityName} terpilih kembali ke status aktif?`,
                    itemName: `${count} Data Terpilih`,
                    type: 'info',
                    btnText: 'Pulihkan Semua Terpilih',
                    onConfirm: () => {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        fetch(`/recycle-bin/${targetMod}/bulk-restore`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ ids: targetIds })
                        })
                        .then(async res => {
                            const d = await res.json().catch(() => ({}));
                            if (!res.ok || !d.success) {
                                throw new Error(d.message || 'Gagal memulihkan massal.');
                            }
                            return d;
                        })
                        .then(d => {
                            this.showSimatToast(d.message || 'Data berhasil dipulihkan!', 'success');
                            this.removeItemsFromState(targetMod, targetIds, d);
                        })
                        .catch(err => {
                            console.error('bulk restore error:', err);
                            this.showSimatToast(err.message || 'Gagal memulihkan massal.', 'error');
                        });
                    }
                });
            },

            bulkForceDelete(module) {
                if (this.selectedIds.length === 0) return;
                const count = this.selectedIds.length;
                const targetMod = module || this.currentTargetModule;
                const targetIds = [...this.selectedIds];

                // BLOKIR PENGHAPUSAN MASSAL JIKA ADA OBJEK PEMANFAATAN YANG MASIH MEMILIKI ASET MITRA TERKAIT
                if (targetMod === 'kemitraan') {
                    const parentsWithMitras = this.kemitraanDimanfaatkan.filter(k => targetIds.includes(k.id) && Number(k.linked_mitras_count) > 0);
                    if (parentsWithMitras.length > 0) {
                        const totalMitraCount = parentsWithMitras.reduce((sum, k) => sum + Number(k.linked_mitras_count), 0);
                        const parentNames = parentsWithMitras.map(k => k.nama_barang).slice(0, 3).join(', ') + (parentsWithMitras.length > 3 ? '...' : '');
                        this.askConfirmation({
                            title: 'Penghapusan Massal Ditolak',
                            message: `Terdapat ${parentsWithMitras.length} objek pemanfaatan terpilih (${parentNames}) yang masih menampung total ${totalMitraCount} aset mitra terkait di Tong Sampah.`,
                            itemName: `${parentsWithMitras.length} Objek Terpilih Masih Memiliki Aset Mitra (Total ${totalMitraCount} Aset Mitra)`,
                            type: 'danger',
                            isBlocked: true,
                            actionUrl: null,
                            actionText: null,
                            assetWarning: `Sistem mendeteksi bahwa objek-objek pemanfaatan ini masih memiliki aset yang ditambahkan oleh mitra rekanan di Tong Sampah. Silakan hapus permanen aset mitra terkait terlebih dahulu di sub-tab "📦 Aset Ditambahkan Mitra" atau batalkan centang pada objek pemanfaatan tersebut.`,
                            btnText: null,
                            onConfirm: null
                        });
                        return;
                    }
                }

                // BLOKIR PENGHAPUSAN MASSAL JIKA ADA UNIT YANG MASIH MEMILIKI ASET
                if (targetMod === 'unit') {
                    const unitsWithAssets = this.units.filter(u => targetIds.includes(u.id) && Number(u.total_aset) > 0);
                    if (unitsWithAssets.length > 0) {
                        const totalAsetCount = unitsWithAssets.reduce((sum, u) => sum + Number(u.total_aset), 0);
                        const unitNames = unitsWithAssets.map(u => u.nama).slice(0, 3).join(', ') + (unitsWithAssets.length > 3 ? '...' : '');
                        this.askConfirmation({
                            title: 'Penghapusan Massal Ditolak',
                            message: `Terdapat ${unitsWithAssets.length} unit terpilih (${unitNames}) yang masih menampung total ${totalAsetCount} aset aktif di database RSUD. Unit yang memiliki aset tidak dapat dihapus.`,
                            itemName: `${unitsWithAssets.length} Unit Terpilih Masih Memiliki Aset (Total ${totalAsetCount} Aset)`,
                            type: 'danger',
                            isBlocked: true,
                            actionUrl: '/mutasi-aset',
                            actionText: 'Ajukan Mutasi Aset',
                            assetWarning: `Demi integritas data aset RSUD Koesnadi, Anda tidak dapat menghapus unit yang masih memegang inventaris barang. Silakan batalkan centang pada unit yang memiliki aset, atau pulihkan unit tersebut dan ajukan mutasi aset ke ruangan lain terlebih dahulu.`,
                            btnText: null,
                            onConfirm: null
                        });
                        return;
                    }

                    // BLOKIR PENGHAPUSAN MASSAL JIKA ADA UNIT YANG MEMILIKI RIWAYAT BAST DISTRIBUSI
                    const unitsWithBasts = this.units.filter(u => targetIds.includes(u.id) && Number(u.total_bast) > 0);
                    if (unitsWithBasts.length > 0) {
                        const totalBastCount = unitsWithBasts.reduce((sum, u) => sum + Number(u.total_bast), 0);
                        const unitNames = unitsWithBasts.map(u => u.nama).slice(0, 3).join(', ') + (unitsWithBasts.length > 3 ? '...' : '');
                        this.askConfirmation({
                            title: 'Proteksi Audit: Penghapusan Massal Ditolak',
                            message: `Terdapat ${unitsWithBasts.length} unit terpilih (${unitNames}) yang memiliki total ${totalBastCount} dokumen riwayat BAST Distribusi resmi.`,
                            itemName: `${unitsWithBasts.length} Unit Terpilih Memiliki Riwayat BAST Resmi (${totalBastCount} Dokumen)`,
                            type: 'danger',
                            isBlocked: true,
                            actionUrl: '/berita-acara',
                            actionText: 'Buka Arsip BAST',
                            assetWarning: `Sesuai standar audit BPK dan Inspektorat, dokumen Berita Acara Serah Terima (BAST) adalah bukti legalitas penyerahan barang yang dilindungi undang-undang dan tidak boleh dihapus dari sistem. Unit-unit ini hanya dapat dinonaktifkan/dipulihkan, tidak boleh dimusnahkan permanen dari database.`,
                            btnText: null,
                            onConfirm: null
                        });
                        return;
                    }
                }

                let title = 'Konfirmasi Hapus Permanen Massal';
                let message = `PERINGATAN: Apakah Anda yakin ingin MENGHAPUS PERMANEN ${count} data terpilih dari database? Tindakan ini TIDAK DAPAT DIBATALKAN!`;
                let itemName = `${count} Data Terpilih`;

                this.askConfirmation({
                    title: title,
                    message: message,
                    itemName: itemName,
                    type: 'danger',
                    isBlocked: false,
                    btnText: 'Hapus Permanen Sekarang',
                    onConfirm: () => {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        fetch(`/recycle-bin/${targetMod}/bulk-force-delete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ ids: targetIds })
                        })
                        .then(async res => {
                            const d = await res.json().catch(() => ({}));
                            if (!res.ok || !d.success) {
                                throw new Error(d.message || 'Gagal menghapus data permanen.');
                            }
                            return d;
                        })
                        .then(d => {
                            this.showSimatToast(d.message || 'Data terpilih berhasil dihapus permanen!', 'success');
                            this.removeItemsFromState(targetMod, targetIds, d);
                        })
                        .catch(err => {
                            console.error('bulk force delete error:', err);
                            this.showSimatToast(err.message || 'Gagal menghapus data permanen.', 'error');
                        });
                    }
                });
            },

            forceDeleteSingle(module, item) {
                if (!item) return;
                const targetMod = module || this.currentTargetModule;

                // JIKA OBJEK PEMANFAATAN KEMITRAAN MEMILIKI ASET MITRA TERKAIT: BLOKIR HAPUS PERMANEN
                if (targetMod === 'kemitraan' && !item.is_ditambahkan && Number(item.linked_mitras_count) > 0) {
                    this.askConfirmation({
                        title: 'Objek Pemanfaatan Tidak Dapat Dihapus Permanen',
                        message: `Objek pemanfaatan "${item.nama_barang || item.nama}" saat ini tidak dapat dimusnahkan permanen karena masih memiliki ${item.linked_mitras_count} aset mitra terkait di Tong Sampah (sub-tab Aset Ditambahkan Mitra).`,
                        itemName: `${item.nama_barang || item.nama} — Menampung ${item.linked_mitras_count} Aset Mitra`,
                        type: 'danger',
                        isBlocked: true,
                        actionUrl: null,
                        actionText: null,
                        assetWarning: `Demi integritas data inventaris RSUD Koesnadi, seluruh aset yang ditambahkan oleh mitra pada objek ini harus dimusnahkan permanen terlebih dahulu di sub-tab "📦 Aset Ditambahkan Mitra" sebelum objek pemanfaatan ini dapat dihapus permanen.`,
                        btnText: null,
                        onConfirm: null
                    });
                    return;
                }

                // JIKA RUANGAN / UNIT MEMILIKI ASET: BLOKIR PENGHAPUSAN DAN TAMPILKAN LINK AJUKAN MUTASI
                if (targetMod === 'unit' && Number(item.total_aset) > 0) {
                    this.askConfirmation({
                        title: 'Unit Tidak Dapat Dihapus Permanen',
                        message: `Ruangan "${item.nama}" saat ini tidak dapat dihapus permanen karena masih tercatat menampung ${item.total_aset} barang inventaris/aset di database RSUD.`,
                        itemName: `${item.nama} (${item.kode || 'UNIT'}) — Memiliki ${item.total_aset} Aset Aktif`,
                        type: 'danger',
                        isBlocked: true,
                        actionUrl: '/mutasi-aset',
                        actionText: 'Ajukan Mutasi Aset',
                        assetWarning: `Sistem mendeteksi bahwa ruangan ini masih tercatat menampung ${item.total_aset} aset aktif. Demi akuntabilitas inventaris RSUD Koesnadi, unit yang memiliki aset tidak diperkenankan untuk dihapus permanen. Silakan pulihkan unit ini lalu ajukan mutasi aset ke ruangan lain terlebih dahulu sampai ruangan ini kosong (0 aset).`,
                        btnText: null,
                        onConfirm: null
                    });
                    return;
                }

                // JIKA RUANGAN / UNIT MEMILIKI RIWAYAT BAST DISTRIBUSI: BLOKIR PENGHAPUSAN DEMI KEPATUHAN AUDIT BPK
                if (targetMod === 'unit' && Number(item.total_bast) > 0) {
                    this.askConfirmation({
                        title: 'Proteksi Audit: Unit Tidak Dapat Dihapus Permanen',
                        message: `Ruangan "${item.nama}" tidak dapat dihapus permanen karena memiliki ${item.total_bast} riwayat dokumen Berita Acara Serah Terima (BAST) Distribusi resmi.`,
                        itemName: `${item.nama} (${item.kode || 'UNIT'}) — Memiliki ${item.total_bast} Dokumen BAST Resmi`,
                        type: 'danger',
                        isBlocked: true,
                        actionUrl: '/berita-acara',
                        actionText: 'Buka Arsip BAST',
                        assetWarning: `Dokumen Berita Acara Serah Terima (BAST) bernomor resmi dilindungi untuk kepentingan audit berkala BPK & Inspektorat sebagai bukti sah penyerahan barang milik daerah. Unit yang pernah memiliki transaksi BAST tidak boleh dihapus permanen dari database. Silakan pulihkan unit ini ke katalog aktif jika diperlukan.`,
                        btnText: null,
                        onConfirm: null
                    });
                    return;
                }

                let bNomor = item.nibar || item.nomor_bast || item.kode || item.nama || 'Item';
                let title = targetMod === 'nibar' ? 'Konfirmasi Hapus Permanen NIBAR' : 'Konfirmasi Hapus Permanen';
                let message = targetMod === 'nibar'
                    ? `TINDAKAN BERBAHAYA: Register NIBAR "${bNomor}" akan dimusnahkan secara PERMANEN dari database RSUD. Tindakan ini TIDAK DAPAT DIBATALKAN!`
                    : 'TINDAKAN BERBAHAYA: Data ini akan dihapus secara PERMANEN dari database dan seluruh relasinya akan hilang. Tindakan ini TIDAK DAPAT DIBATALKAN!';
                let btnText = 'Hapus Permanen Sekarang';

                this.askConfirmation({
                    title: title,
                    message: message,
                    itemName: bNomor,
                    type: 'danger',
                    isBlocked: false,
                    btnText: btnText,
                    onConfirm: () => {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        fetch(`/recycle-bin/${targetMod}/${item.id}/force-delete`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            }
                        })
                        .then(async res => {
                            const d = await res.json().catch(() => ({}));
                            if (!res.ok || !d.success) {
                                throw new Error(d.message || 'Gagal menghapus permanen.');
                            }
                            return d;
                        })
                        .then(d => {
                            this.showSimatToast(d.message || 'Data telah dihapus permanen.', 'success');
                            this.removeItemsFromState(targetMod, [item.id], d);
                        })
                        .catch(err => {
                            console.error('force delete error:', err);
                            this.showSimatToast(err.message || 'Gagal menghapus permanen.', 'error');
                        });
                    }
                });
            },

            askConfirmation(config) {
                if (typeof window.askSimatConfirm === 'function') {
                    window.askSimatConfirm(config);
                } else if (window.dispatchEvent) {
                    window.dispatchEvent(new CustomEvent('ask-confirm', {
                        detail: config
                    }));
                } else if (confirm(config.message)) {
                    config.onConfirm();
                }
            },

            showSimatToast(message, type = 'info') {
                if (typeof window.showSimatToast === 'function') {
                    window.showSimatToast(message, type);
                } else if (window.dispatchEvent) {
                    window.dispatchEvent(new CustomEvent('show-toast', {
                        detail: { message, type }
                    }));
                } else {
                    alert(message);
                }
            }
        };
    }
</script>
