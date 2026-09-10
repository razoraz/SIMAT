@php
    $isSubAdmin = (Auth::user()->role ?? '') === 'sub_admin';
    $pageTitle = request()->routeIs('distribusi.edit') 
        ? 'Ubah Distribusi ASTAP' 
        : ($isSubAdmin ? 'Input Pengajuan Baru' : 'Input Distribusi Baru');
    $breadcrumbTitle = request()->routeIs('distribusi.edit') 
        ? 'Master Utama / Distribusi ASTAP / Ubah' 
        : ($isSubAdmin ? 'Master Utama / Pengajuan Baru / Input Baru' : 'Master Utama / Distribusi ASTAP / Input Baru');
@endphp

<x-layout :title="$pageTitle . ' - SIMAT-RK'">
    @section('page-title', $pageTitle)
    @section('breadcrumb', $breadcrumbTitle)

    <script>
        window.editingDistribusi = {{ Js::from($distribusiData ?? null) }};

        function formDistribusiApp() {
            return {
                isEdit: {{ request()->routeIs('distribusi.edit') ? 'true' : 'false' }},
                editId: {{ isset($id) ? Js::from($id) : 'null' }},
                userRole: {{ Js::from(Auth::user()->role ?? 'admin') }},
                isSubAdmin: {{ Js::from(Auth::user() ? Auth::user()->isSubAdmin() : false) }},
                userUnit: {{ Js::from(Auth::user()?->unitModel ? [
                    'id' => Auth::user()->unitModel->id,
                    'nama' => Auth::user()->unitModel->nama,
                    'tipe' => Auth::user()->unitModel->tipe,
                    'kepala' => Auth::user()->unitModel->kepala,
                    'nip' => Auth::user()->unitModel->nip ?: '-',
                    'jabatan' => 'Kepala / Penanggung Jawab ' . Auth::user()->unitModel->nama
                ] : null) }},
                unitList: {{ Js::from($units ?? []) }},
                nibarList: {{ Js::from($nibarList ?? []) }},
                dbAstapList: {{ Js::from($astapList ?? []) }},
                jenisAstapList: {{ Js::from($jenisAstapList ?? []) }},
                activeJenisDropdownIndex: null,
                activeDropdownIndex: null,
                activeNibarDropdownIndex: null,
                unitSearch: '',
                isSearchingUnit: false,
                selectedUnitObj: null,
                nibarSearch: {},
                toast: { show: false, message: '', type: 'success' },
                confirmData: { show: false, title: '', message: '', itemName: '', btnText: '', type: 'danger', onConfirm: null },
                // katalogAstap langsung dari database — deduplikasi by nama agar item nama sama tampil 1x
                katalogAstap: (() => {
                    const all = ({{ Js::from($astapList ?? []) }}).filter(a => a && a.nama);
                    const seen = new Set();
                    return all.filter(a => {
                        const key = a.nama.trim().toLowerCase();
                        if (seen.has(key)) return false;
                        seen.add(key);
                        return true;
                    });
                })(),
                formData: {
                    kode: '',
                    bast_nomor: '',
                    tujuan: '',
                    unit_id: null,
                    tgl: (() => {
                        const d = new Date();
                        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                    })(),
                    penerima: '',
                    penerima_nip: '',
                    penerima_jabatan: '',
                    status: 'Menunggu Konfirmasi',
                    keterangan: '',
                    items: []
                },
                init() {
                    let loadedData = null;
                    if (this.isEdit) {
                        if (window.editingDistribusi && typeof window.editingDistribusi === 'object') {
                            loadedData = window.editingDistribusi;
                        } else if (this.editId) {
                            try {
                                const stored = localStorage.getItem('simat_distribusis');
                                if (stored) {
                                    const storedList = JSON.parse(stored);
                                    if (Array.isArray(storedList)) {
                                        loadedData = storedList.find(d => String(d.id) === String(this.editId) || d.kode === String(this.editId));
                                    }
                                }
                            } catch(e) {}
                        }
                    }

                    const todayStr = (() => {
                        const d = new Date();
                        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
                    })();

                    if (loadedData) {
                        const uObj = (this.unitList || []).find(u => u.id === loadedData.unit_id || u.nama === (loadedData.unit ? loadedData.unit.nama : (loadedData.tujuan || ''))) || null;
                        
                        // Tanggal Penyerahan untuk Admin otomatis hari ini (kecuali transaksi historis yang statusnya sudah Telah Diterima)
                        // Untuk pengajuan baru yang disetujui / diproses penyerahannya, tanggal penyerahan default otomatis hari ini.
                        let tglStr;
                        if (!this.isSubAdmin) {
                            if (loadedData.status === 'Telah Diterima' && loadedData.tanggal_distribusi) {
                                tglStr = String(loadedData.tanggal_distribusi).substring(0, 10);
                            } else {
                                tglStr = todayStr;
                            }
                        } else {
                            tglStr = loadedData.tanggal_distribusi 
                                ? String(loadedData.tanggal_distribusi).substring(0, 10) 
                                : todayStr;
                        }

                        let itemsMapped = [];
                        if (loadedData.items && Array.isArray(loadedData.items) && loadedData.items.length > 0) {
                            itemsMapped = loadedData.items.map((it, idx) => {
                                const astapObj = it.astap || null;
                                let nibarSelectedObj = [];
                                if (it.registers && Array.isArray(it.registers)) {
                                    nibarSelectedObj = it.registers.map(dir => {
                                        const reg = dir.astap_register || dir.astapRegister || (this.nibarList || []).find(nr => nr.id === dir.astap_register_id);
                                        return {
                                            id: dir.astap_register_id || (reg ? reg.id : null),
                                            nibar: reg ? (reg.nibar || reg.no_register) : (dir.nibar || '-'),
                                            ruang: reg ? (reg.ruang_pemegang || reg.ruang) : 'Gudang Aset',
                                            kondisi: reg ? reg.kondisi : 'Baik'
                                        };
                                    });
                                }
                                return {
                                    id: Date.now() + idx,
                                    astap_id: it.astap_id || (astapObj ? astapObj.id : null),
                                    jenis_astap_kode: astapObj && astapObj.jenis_astap ? astapObj.jenis_astap.jenis : (it.jenis_astap_kode || '1.3.2'),
                                    jenis_astap_nama: astapObj && astapObj.jenis_astap ? astapObj.jenis_astap.nama_jenis : (it.jenis_astap_nama || 'PERALATAN DAN MESIN'),
                                    nama_barang: astapObj ? astapObj.nama_barang : (it.nama_barang || ''),
                                    kode_barang: astapObj ? astapObj.kode_108 : (it.kode_barang || ''),
                                    merk_type: it.merk_type || '',
                                    qty: it.qty || 1,
                                    qty_acc: nibarSelectedObj.length > 0 ? nibarSelectedObj.length : ((it.qty_acc !== undefined && it.qty_acc !== null) ? it.qty_acc : 0),
                                    satuan: astapObj ? (astapObj.satuan || 'Unit') : (it.satuan || 'Unit'),
                                    kondisi: it.kondisi || '-',
                                    keterangan: it.keterangan || '',
                                    nibar_selected: nibarSelectedObj
                                };
                            });
                        }

                        this.formData.kode = loadedData.kode || ('DST-2026-' + Math.floor(Math.random() * 900 + 100));
                        const isShippingOrReceived = ['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima'].includes(loadedData.status);
                        const isValidLoadedBast = loadedData.bast_nomor && !loadedData.bast_nomor.includes('Diterbitkan') && loadedData.bast_nomor !== '-' && !loadedData.bast_nomor.includes('Menunggu') && !loadedData.bast_nomor.includes('tidak');
                        
                        this.formData.status = loadedData.status || (this.isSubAdmin ? 'Menunggu Konfirmasi' : 'Dalam Pengiriman');
                        
                        // Otomatis: Jika sudah ada minimal 1 NIBAR yang diinputkan/di-ACC, status otomatis beralih ke 'Dalam Pengiriman'
                        const hasAnyNibar = itemsMapped.some(it => (it.nibar_selected && it.nibar_selected.length > 0) || (it.qty_acc && it.qty_acc > 0));
                        if (!this.isSubAdmin && hasAnyNibar && ['Menunggu Konfirmasi', 'Draft', 'Pending'].includes(this.formData.status)) {
                            this.formData.status = 'Dalam Pengiriman';
                        }

                        const isShippingOrReceivedNow = ['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima'].includes(this.formData.status);
                        if (this.formData.status === 'Ditolak') {
                            this.formData.bast_nomor = '(tidak diterbitkan)';
                        } else if (this.formData.status === 'Menunggu Konfirmasi' || this.formData.status === 'Draft') {
                            this.formData.bast_nomor = '(Menunggu Konfirmasi)';
                        } else if (isShippingOrReceivedNow && isValidLoadedBast) {
                            this.formData.bast_nomor = loadedData.bast_nomor;
                        } else if (isShippingOrReceivedNow) {
                            this.formData.bast_nomor = {{ Js::from($nextBastNomor ?? '') }};
                        } else {
                            this.formData.bast_nomor = '(Menunggu Konfirmasi)';
                        }
                        this.formData.tujuan = loadedData.unit ? loadedData.unit.nama : (uObj ? uObj.nama : (loadedData.tujuan || ''));
                        this.formData.unit_id = loadedData.unit_id || (uObj ? uObj.id : null);
                        this.formData.tgl = tglStr;
                        this.formData.penerima = uObj ? (uObj.kepala || '') : (loadedData.penerima || loadedData.pj_nama || '');
                        this.formData.penerima_nip = uObj ? (uObj.nip || '') : (loadedData.pj_nip || '');
                        this.formData.penerima_jabatan = uObj ? ('Kepala / Penanggung Jawab ' + uObj.nama) : (loadedData.pj_jabatan || '');
                        this.formData.keterangan = loadedData.keterangan || '';
                        this.formData.items = itemsMapped;
                        this.unitSearch = this.formData.tujuan;
                        this.selectedUnitObj = uObj || null;
                    } else {
                        const autoUnit = (this.isSubAdmin && this.userUnit) ? this.userUnit : null;
                        this.formData.kode = {{ Js::from($nextKode ?? ('DST-'.date('Y').'-001')) }};
                        this.formData.status = this.isSubAdmin ? 'Menunggu Konfirmasi' : 'Dalam Pengiriman';
                        this.formData.bast_nomor = this.isSubAdmin ? '(Menunggu Konfirmasi)' : {{ Js::from($nextBastNomor ?? ('032 / 001 / 430.10.7 / '.date('Y'))) }};
                        this.formData.tujuan = autoUnit ? autoUnit.nama : '';
                        this.formData.unit_id = autoUnit ? autoUnit.id : null;
                        this.formData.tgl = todayStr;
                        this.formData.penerima = autoUnit ? (autoUnit.kepala || '') : '';
                        this.formData.penerima_nip = autoUnit ? (autoUnit.nip || '') : '';
                        this.formData.penerima_jabatan = autoUnit ? (autoUnit.jabatan || ('Kepala / PJ ' + autoUnit.nama)) : '';
                        this.formData.keterangan = '';
                        if (autoUnit) {
                            this.unitSearch = autoUnit.nama;
                            this.selectedUnitObj = autoUnit;
                        }
                    }
                    this.updateYearInKode();

                    if (!this.formData.items || this.formData.items.length === 0) {
                        this.formData.items = [{ id: Date.now(), jenis_astap_kode: '', jenis_astap_nama: '', nama_barang: '', kode_barang: '', merk_type: '', qty: 1, qty_acc: 0, satuan: 'Unit', kondisi: '-', keterangan: '', nibar_selected: [] }];
                    }
                },
                updateYearInKode() {
                    if (!this.formData.tgl) return;
                    const tahun = this.formData.tgl.split('-')[0];
                    if (tahun && this.formData.kode && this.formData.kode.startsWith('DST-')) {
                        const parts = this.formData.kode.split('-');
                        if (parts.length === 3) {
                            this.formData.kode = parts[0] + '-' + tahun + '-' + parts[2];
                        }
                    }
                    if (['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima'].includes(this.formData.status)) {
                        if (this.formData.bast_nomor && this.formData.bast_nomor.includes('430.10.7')) {
                            const bParts = this.formData.bast_nomor.split('/');
                            if (bParts.length === 4) {
                                this.formData.bast_nomor = bParts[0].trim() + ' / ' + bParts[1].trim() + ' / ' + bParts[2].trim() + ' / ' + tahun;
                            }
                        }
                    }
                },
                onStatusChange() {
                    if (this.formData.status === 'Ditolak') {
                        this.formData.bast_nomor = '(tidak diterbitkan)';
                    } else if (this.formData.status === 'Menunggu Konfirmasi' || this.formData.status === 'Draft') {
                        this.formData.bast_nomor = '(Menunggu Konfirmasi)';
                    } else if (this.formData.status === 'Dalam Pengiriman' || this.formData.status === 'Telah Diterima') {
                        if (!this.formData.bast_nomor || this.formData.bast_nomor === '-' || this.formData.bast_nomor === '(tidak diterbitkan)' || this.formData.bast_nomor.includes('Menunggu') || this.formData.bast_nomor.includes('Diterbitkan')) {
                            this.formData.bast_nomor = {{ Js::from($nextBastNomor ?? ('032 / 001 / 430.10.7 / '.date('Y'))) }};
                        }
                    }
                },
                addItem() {
                    if (this.formData.status === 'Ditolak') return;
                    this.formData.items.push({ id: Date.now(), jenis_astap_kode: '', jenis_astap_nama: '', nama_barang: '', kode_barang: '', merk_type: '', qty: 1, qty_acc: 0, satuan: 'Unit', kondisi: '-', keterangan: '', nibar_selected: [] });
                },
                removeItem(index) {
                    if (this.formData.status === 'Ditolak') return;
                    if (this.formData.items.length <= 1) { alert('⚠️ Minimal harus ada 1 barang!'); return; }
                    this.formData.items.splice(index, 1);
                },
                getTotalItemVolume() {
                    return (this.formData.items || []).reduce((sum, it) => sum + (parseInt(it.qty) || 0), 0);
                },
                getTotalRincianAcc() {
                    return (this.formData.items || []).filter(it => {
                        const acc = (it.qty_acc !== null && it.qty_acc !== undefined && it.qty_acc !== '') ? parseInt(it.qty_acc) : ((it.nibar_selected || []).length);
                        return acc > 0;
                    }).length;
                },
                getTotalItemVolumeAcc() {
                    return (this.formData.items || []).reduce((sum, it) => {
                        const acc = (it.qty_acc !== null && it.qty_acc !== undefined && it.qty_acc !== '') ? parseInt(it.qty_acc) : ((it.nibar_selected || []).length);
                        return sum + (acc > 0 ? acc : 0);
                    }, 0);
                },
                getItemKode(item) {
                    if (!item) return '';
                    if (item.kode_barang && item.kode_barang.trim() !== '') return item.kode_barang.trim();
                    if (item.nama_barang && item.nama_barang.trim() !== '') {
                        const match = (this.katalogAstap || []).find(k => k.nama && k.nama.toLowerCase().trim() === item.nama_barang.toLowerCase().trim());
                        if (match && match.kode) { item.kode_barang = match.kode; return match.kode; }
                    }
                    return '';
                },
                isNibarMatch(n, item) {
                    if (!item || !n) return false;
                    if (item.astap_id && n.astap_id && String(item.astap_id) === String(n.astap_id)) return true;
                    const itemKode = this.getItemKode(item);
                    if (itemKode && n.kode && itemKode.trim() === n.kode.trim()) return true;
                    // Gabungkan NIBAR dari semua astap yang punya nama_barang sama (untuk barang diinput >1x)
                    if (item.nama_barang && n.nama_barang &&
                        item.nama_barang.trim().toLowerCase() === n.nama_barang.trim().toLowerCase()) return true;
                    return false;
                },
                isNibarKondisiBaik(n) {
                    if (!n) return false;
                    const k = (n.kondisi || '').trim().toLowerCase();
                    return k === 'baik' || k === '' || !n.kondisi;
                },
                getMatchingNibarCount(item) { 
                    return (this.nibarList || []).filter(n => this.isNibarMatch(n, item) && (n.status === 'Tersedia' || !n.status) && this.isNibarKondisiBaik(n)).length; 
                },
                isNibarEmpty(item) { 
                    if (!item || (!item.nama_barang && !item.kode_barang)) return false; 
                    return this.getMatchingNibarCount(item) === 0; 
                },
                getFilteredNibar(item, query) {
                    if (!item) return [];
                    let list = (this.nibarList || []).filter(n => this.isNibarMatch(n, item) && (n.status === 'Tersedia' || !n.status) && this.isNibarKondisiBaik(n));
                    const chosen = (item.nibar_selected || []).map(n => n.nibar);
                    list = list.filter(n => !chosen.includes(n.nibar));
                    if (query && query.trim() !== '') { 
                        const q = query.toLowerCase(); 
                        list = list.filter(n => (n.nibar||'').toLowerCase().includes(q) || (n.ruang||'').toLowerCase().includes(q)); 
                    }
                    return list;
                },
                getItemMaxQty(item) {
                    if (!item) return 1;
                    const qtyPengajuan = parseInt(item.qty) || 0;
                    return Math.max(qtyPengajuan, 1);
                },
                selectNibar(item, n) {
                    if (this.formData.status === 'Ditolak') return;
                    if (!item || !n || (n.status && n.status !== 'Tersedia') || !this.isNibarKondisiBaik(n)) return;
                    if (!item.nibar_selected) item.nibar_selected = [];
                    
                    // Cek jika sudah dipilih agar tidak duplikat
                    if (item.nibar_selected.some(sel => sel.nibar === n.nibar || (sel.id && n.id && String(sel.id) === String(n.id)))) {
                        return;
                    }

                    // Validasi: Jumlah NIBAR tidak boleh melebihi Volume Pengajuan (item.qty)
                    const maxQty = parseInt(item.qty) || 1;
                    if (item.nibar_selected.length >= maxQty) {
                        alert('⚠️ Jumlah NIBAR yang dipilih (' + (item.nibar_selected.length + 1) + ') tidak boleh melebihi Volume Pengajuan (' + maxQty + ' ' + (item.satuan || 'Unit') + ').\n\nVolume Pengajuan tidak boleh berubah otomatis. Jika ingin menambah NIBAR, silakan ubah Volume Pengajuan terlebih dahulu.');
                        return;
                    }

                    item.nibar_selected.push({ id: n.id, nibar: n.nibar, ruang: n.ruang, kondisi: n.kondisi || 'Baik' });
                    // Volume Di-ACC otomatis mengikuti jumlah NIBAR yang diinput
                    item.qty_acc = item.nibar_selected.length;
                    this.activeNibarDropdownIndex = null;

                    // Jika ada minimal 1 NIBAR yang diinput, otomatis dianggap di-ACC (status beralih ke 'Dalam Pengiriman' agar BAST terbit & siap dicetak)
                    if (!this.isSubAdmin && ['Menunggu Konfirmasi', 'Draft', 'Pending'].includes(this.formData.status)) {
                        this.formData.status = 'Dalam Pengiriman';
                        this.onStatusChange();
                    }
                },
                removeNibar(item, nibarStr) {
                    if (this.formData.status === 'Ditolak') return;
                    item.nibar_selected = (item.nibar_selected || []).filter(n => n.nibar !== nibarStr);
                    // Volume Di-ACC otomatis mengikuti jumlah NIBAR yang diinput
                    item.qty_acc = item.nibar_selected.length;

                    // Jika seluruh NIBAR dihapus (0 NIBAR di seluruh item) dan status masih 'Dalam Pengiriman', kembalikan ke 'Menunggu Konfirmasi'
                    const totalNibarAll = (this.formData.items || []).reduce((sum, it) => sum + (it.nibar_selected ? it.nibar_selected.length : 0), 0);
                    if (!this.isSubAdmin && totalNibarAll === 0 && this.formData.status === 'Dalam Pengiriman') {
                        this.formData.status = 'Menunggu Konfirmasi';
                        this.onStatusChange();
                    }
                },
                validateItemQtyAcc(item) {
                    if (!item || this.formData.status === 'Ditolak') return;
                    item.qty_acc = (item.nibar_selected || []).length;
                },
                getFilteredJenisAstap(query) {
                    const validList = (this.jenisAstapList || []).filter(j => j && j.nama);
                    if (!query || query.trim() === '') return validList;
                    const q = query.toLowerCase().trim();
                    return validList.filter(j => (j.nama || '').toLowerCase().includes(q));
                },
                selectJenisAstap(item, j) {
                    if (this.formData.status === 'Ditolak') return;
                    item.jenis_astap_kode = j.kode; item.jenis_astap_nama = j.nama; this.activeJenisDropdownIndex = null;
                },
                clearJenisAstap(item, idx) {
                    if (this.formData.status === 'Ditolak') return;
                    item.jenis_astap_kode = ''; item.jenis_astap_nama = ''; item.nama_barang = ''; item.kode_barang = ''; item.merk_type = ''; item.satuan = 'Unit'; if (idx !== undefined) this.activeJenisDropdownIndex = idx;
                },
                getFilteredAstap(item, query) {
                    let list = this.katalogAstap || [];
                    if (item && item.jenis_astap_kode) { list = list.filter(a => a.jenis_kode === item.jenis_astap_kode || (a.kode && a.kode.startsWith(item.jenis_astap_kode))); }
                    if (!query || query.trim() === '') return list;
                    const q = query.toLowerCase();
                    return list.filter(a => (a.nama||'').toLowerCase().includes(q) || (a.kode||'').toLowerCase().includes(q));
                },
                isItemAlreadySelected(ast, currentItem) {
                    if (!ast) return false;
                    const astNama = (ast.nama || '').trim().toLowerCase();
                    const astKode = (ast.kode || '').trim().toLowerCase();
                    return (this.formData.items || []).some(it => {
                        if (it === currentItem) return false;
                        const itNama = (it.nama_barang || '').trim().toLowerCase();
                        const itKode = (it.kode_barang || '').trim().toLowerCase();
                        return (astNama && itNama === astNama) || (astKode && itKode && itKode === astKode);
                    });
                },
                selectAstapItem(item, ast) {
                    if (this.formData.status === 'Ditolak') return;
                    if (this.isItemAlreadySelected(ast, item)) {
                        alert('⚠️ Barang "' + ast.nama + '" sudah dipilih pada baris lain!\n\nDalam satu transaksi distribusi tidak diperbolehkan memilih 2 nama barang yang sama. Silakan tambahkan Volume (Qty) pada baris yang sudah ada.');
                        return;
                    }
                    item.nama_barang = ast.nama; item.kode_barang = ast.kode; item.merk_type = ast.merk || ''; item.satuan = ast.satuan || 'Unit'; if (!item.jenis_astap_nama && ast.jenis_nama) item.jenis_astap_nama = ast.jenis_nama; this.activeDropdownIndex = null;
                    item.nibar_selected = [];
                    item.qty_acc = 0;
                },
                clearItemBarang(item, idx) {
                    if (this.formData.status === 'Ditolak') return;
                    item.nama_barang = ''; item.kode_barang = ''; item.merk_type = ''; item.satuan = 'Unit'; item.nibar_selected = []; item.qty_acc = 0; if (idx !== undefined) this.activeDropdownIndex = idx;
                },
                onNamaBarangInput(item) {
                    if (!item.nama_barang || item.nama_barang.trim() === '') { item.kode_barang = ''; item.nibar_selected = []; item.qty_acc = 0; return; }
                    const match = (this.katalogAstap || []).find(a => a.nama && a.nama.toLowerCase().trim() === item.nama_barang.toLowerCase().trim());
                    if (match) {
                        if (this.isItemAlreadySelected(match, item)) {
                            alert('⚠️ Barang "' + match.nama + '" sudah dipilih pada baris lain!\n\nDalam satu transaksi distribusi tidak diperbolehkan menginput 2 nama barang yang sama.');
                            item.nama_barang = '';
                            item.kode_barang = '';
                            item.nibar_selected = [];
                            item.qty_acc = 0;
                            return;
                        }
                        item.kode_barang = match.kode; item.merk_type = match.merk || ''; item.satuan = match.satuan || 'Unit'; if (!item.jenis_astap_nama) item.jenis_astap_nama = match.jenis_nama || '';
                    }
                    item.nibar_selected = [];
                    item.qty_acc = 0;
                },
                get filteredUnitList() {
                    if (!this.unitSearch || this.unitSearch.trim().length === 0) return (this.unitList || []).slice(0, 10);
                    const q = this.unitSearch.toLowerCase();
                    return (this.unitList || []).filter(u => u.nama.toLowerCase().includes(q));
                },
                selectUnit(u) {
                    if (this.formData.status === 'Ditolak') return;
                    this.formData.tujuan = u.nama; this.formData.penerima = u.kepala || ''; this.formData.penerima_nip = u.nip || ''; this.formData.penerima_jabatan = 'Kepala / Penanggung Jawab ' + u.nama; this.formData.unit_id = u.id; this.unitSearch = u.nama; this.selectedUnitObj = u; this.isSearchingUnit = false;
                },
                clearUnit() {
                    if (this.formData.status === 'Ditolak') return;
                    this.formData.tujuan = ''; this.formData.unit_id = null; this.unitSearch = ''; this.selectedUnitObj = null; this.isSearchingUnit = true;
                },
                showConfirmModal: false,
                confirmData: { show: false, title: '', message: '', itemName: '', btnText: '', type: 'danger', onConfirm: null },
                isSaving: false,
                toast: { show: false, message: '', type: 'success' },
                askConfirmation(opts) {
                    this.confirmData = {
                        title: opts.title || 'Konfirmasi',
                        message: opts.message || '',
                        itemName: opts.itemName || '',
                        btnText: opts.btnText || 'Ya',
                        type: opts.type || 'danger',
                        onConfirm: opts.onConfirm || null
                    };
                    this.showConfirmModal = true;
                },
                async executeConfirmedAction() {
                    this.showConfirmModal = false;
                    if (typeof this.confirmData.onConfirm === 'function') {
                        await this.confirmData.onConfirm();
                    }
                },
                showSimatToast(message, type = 'success') {
                    this.toast = { show: true, message, type };
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },
                async submitForm() {
                    console.log('[submitForm] status saat ini:', this.formData.status);
                    // Jika Admin menginput minimal 1 NIBAR, otomatis dianggap di-ACC (status beralih ke Dalam Pengiriman agar BAST terbit & siap dicetak)
                    // Catatan: Jika user secara manual memilih 'Ditolak', promosi otomatis ini tidak berjalan
                    if (!this.isSubAdmin && this.formData.status !== 'Ditolak' && this.getTotalItemVolumeAcc() > 0 && ['Menunggu Konfirmasi', 'Draft', 'Pending'].includes(this.formData.status)) {
                        this.formData.status = 'Dalam Pengiriman';
                        this.onStatusChange();
                    }

                    // Saat status Ditolak, pastikan keterangan tidak kosong agar tidak blocked di backend
                    if (this.formData.status === 'Ditolak') {
                        if (!this.formData.keterangan || this.formData.keterangan.trim() === '') {
                            this.formData.keterangan = '-';
                        }
                        (this.formData.items || []).forEach(it => {
                            if (!it.keterangan || it.keterangan.trim() === '') {
                                it.keterangan = '-';
                            }
                        });
                    }

                    // ── 1. Validasi Header Distribusi ──────────────────────────────────
                    console.log('[submitForm] step 1: tujuan =', this.formData.tujuan, 'unit_id =', this.formData.unit_id);
                    if (!this.formData.tujuan || this.formData.tujuan.trim() === '' || !this.formData.unit_id) {
                        alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nTujuan Unit / Ruangan / Paviliun belum dipilih.');
                        return;
                    }

                    // Dievaluasi SETELAH kemungkinan promosi status di atas, agar status 'Ditolak' yang dipilih user tidak terblokir oleh validasi BAST
                    const isShippingOrReceived = ['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima'].includes(this.formData.status);
                    console.log('[submitForm] step 2: isShippingOrReceived =', isShippingOrReceived, 'bast_nomor =', this.formData.bast_nomor);
                    if (!this.isSubAdmin && isShippingOrReceived && (!this.formData.bast_nomor || this.formData.bast_nomor.trim() === '' || this.formData.bast_nomor === '(tidak diterbitkan)' || this.formData.bast_nomor.includes('Menunggu'))) {
                        alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nNo. BAST Distribusi belum diisi / belum terbit.');
                        return;
                    }

                    console.log('[submitForm] step 3: tgl =', this.formData.tgl);
                    if (!this.formData.tgl || this.formData.tgl.trim() === '') {
                        alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nTanggal ' + (this.isSubAdmin ? 'Pengajuan' : 'Penyerahan') + ' belum diisi.');
                        return;
                    }

                    console.log('[submitForm] step 4: penerima =', this.formData.penerima);
                    if (!this.formData.penerima || this.formData.penerima.trim() === '') {
                        alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nNama Penerima / Penanggung Jawab belum diisi (pilih unit tujuan).');
                        return;
                    }

                    console.log('[submitForm] step 5: penerima_jabatan =', this.formData.penerima_jabatan);
                    if (!this.formData.penerima_jabatan || this.formData.penerima_jabatan.trim() === '') {
                        alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nJabatan Penerima belum diisi.');
                        return;
                    }

                    // ── 2. Validasi Daftar Barang (Multi-Barang) ───────────────────────
                    console.log('[submitForm] step 6: items.length =', this.formData.items.length);
                    if (!this.formData.items || this.formData.items.length === 0) {
                        alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nMinimal harus ada 1 barang dalam transaksi distribusi.');
                        return;
                    }

                    // Validasi Duplikasi Nama Barang dalam Satu Transaksi
                    const seenNames = {};
                    for (let i = 0; i < this.formData.items.length; i++) {
                        const it = this.formData.items[i];
                        const urut = i + 1;
                        const key = (it.nama_barang || '').trim().toLowerCase();
                        if (key) {
                            if (seenNames[key] !== undefined) {
                                alert('⚠️ Terdapat nama barang yang sama:\n\nBarang #' + urut + ' ("' + it.nama_barang + '") sama dengan Barang #' + (seenNames[key] + 1) + '.\n\nDalam 1 transaksi distribusi tidak diperbolehkan menginput 2 nama barang yang sama. Silakan gabungkan volumenya pada satu baris atau hapus baris yang duplikat.');
                                return;
                            }
                            seenNames[key] = i;
                        }
                    }

                    for (let i = 0; i < this.formData.items.length; i++) {
                        const it = this.formData.items[i];
                        const urut = i + 1;
                        console.log('[submitForm] item #' + urut + ':', JSON.stringify({jenis: it.jenis_astap_nama, nama: it.nama_barang, qty: it.qty, keterangan: it.keterangan, nibar_count: (it.nibar_selected||[]).length}));

                        // a. Validasi Kategori Jenis ASTAP
                        if (!it.jenis_astap_nama || it.jenis_astap_nama.trim() === '') {
                            alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nKategori Jenis ASTAP pada Barang #' + urut + ' belum dipilih.');
                            return;
                        }

                        // b. Validasi Nama Barang
                        if (!it.nama_barang || it.nama_barang.trim() === '') {
                            alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nNama Barang pada Barang #' + urut + ' belum dipilih.');
                            return;
                        }

                        // c. Sinkronisasi Volume Di-ACC mengikuti jumlah NIBAR yang dipilih
                        // Jika stok kosong atau belum dipilihkan NIBAR, qty_acc bernilai 0 (barang ini tidak di-ACC)
                        it.qty_acc = (it.nibar_selected || []).length;

                        // d. Validasi Volume Pengajuan (Qty)
                        const qtyPengajuan = parseInt(it.qty);
                        if (!it.qty || isNaN(qtyPengajuan) || qtyPengajuan <= 0) {
                            alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nVolume Pengajuan pada Barang #' + urut + ' ("' + (it.nama_barang || 'Aset') + '") belum diisi atau bernilai 0.');
                            return;
                        }

                        if ((it.nibar_selected || []).length > qtyPengajuan) {
                            alert('⚠️ Volume Di-ACC / NIBAR terpilih (' + it.nibar_selected.length + ') pada Barang #' + urut + ' ("' + (it.nama_barang || 'Aset') + '") melebihi Volume Pengajuan (' + qtyPengajuan + ').\n\nSilakan kurangi pilihan NIBAR atau sesuaikan Volume Pengajuan.');
                            return;
                        }

                        // e. Validasi Keterangan / Catatan Spesifik Item Barang
                        if (this.formData.status !== 'Ditolak' && (!it.keterangan || it.keterangan.trim() === '' || it.keterangan.trim() === '-')) {
                            alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nKeterangan / Catatan Peruntukan Barang pada Barang #' + urut + ' ("' + (it.nama_barang || 'Aset') + '") belum diisi.');
                            return;
                        }
                    }

                    // ── Validasi Akumulasi Volume Di-ACC (Khusus Admin jika status Dalam Pengiriman / Telah Diterima) ──
                    console.log('[submitForm] step 7: acc check, status =', this.formData.status);
                    if (!this.isSubAdmin && ['Dalam Pengiriman', 'Telah Diterima'].includes(this.formData.status)) {
                        const totalAcc = this.getTotalItemVolumeAcc();
                        if (totalAcc <= 0) {
                            alert('⚠️ Validasi Persetujuan Distribusi:\n\nBelum ada barang yang disetujui (di-ACC). Minimal harus ada 1 barang yang dipilihkan NIBAR-nya.\n\nJika seluruh permohonan barang tidak disetujui, silakan ubah Status Transaksi menjadi "Ditolak".');
                            return;
                        }
                    }

                    // ── 3. Validasi Catatan Umum Distribusi ─────────────────────────────
                    console.log('[submitForm] step 8: keterangan umum =', this.formData.keterangan);
                    if (this.formData.status !== 'Ditolak' && (!this.formData.keterangan || this.formData.keterangan.trim() === '' || this.formData.keterangan.trim() === '-')) {
                        alert('⚠️ Mohon isi seluruh form terlebih dahulu!\n\nCatatan Umum / Keterangan Penempatan belum diisi.');
                        return;
                    }

                    console.log('[submitForm] semua validasi lolos, menampilkan konfirmasi...');
                    this.askConfirmation({
                        title: this.isEdit ? '✏️ Konfirmasi Ubah Distribusi' : '🚚 Konfirmasi Simpan Distribusi',
                        message: this.isEdit ? 'Apakah Anda yakin ingin menyimpan perubahan data distribusi ini?' : 'Apakah Anda yakin ingin menyimpan data distribusi baru ini?',
                        itemName: (this.formData.kode || 'DIST') + ' ➔ ' + (this.formData.tujuan || '-'),
                        type: this.isEdit ? 'info' : 'success',
                        btnText: this.isEdit ? '✏️ Ya, Simpan Perubahan' : '✅ Ya, Simpan Distribusi',
                        onConfirm: async () => {
                            this.isSaving = true;
                            try {
                                let normalizedTgl = this.formData.tgl;
                                if (normalizedTgl) {
                                    const parts = normalizedTgl.split(/[\/\-]/);
                                    if (parts.length === 3 && parts[2].length === 4) {
                                        normalizedTgl = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                                    }
                                }

                                const dbPayload = {
                                    kode: this.formData.kode,
                                    bast_nomor: (this.formData.status !== 'Ditolak' && isShippingOrReceived) ? this.formData.bast_nomor : null,
                                    tujuan: this.formData.tujuan,
                                    unit_id: this.formData.unit_id,
                                    tanggal_distribusi: normalizedTgl,
                                    pj_nama: this.formData.penerima,
                                    pj_nip: this.formData.penerima_nip,
                                    pj_jabatan: this.formData.penerima_jabatan,
                                    status: this.formData.status || 'Draft',
                                    keterangan: this.formData.keterangan || '-',
                                    items: this.formData.items.map(it => {
                                        const resolvedKode = it.kode_barang || this.getItemKode(it) || '';
                                        const astapObj = (this.dbAstapList || []).find(a => a.kode === resolvedKode || a.nama === it.nama_barang);
                                        const registerIds = (it.nibar_selected || []).map(n => n.id ? parseInt(n.id) : null).filter(Boolean);
                                        return {
                                            astap_id: astapObj ? astapObj.id : (it.astap_id || 1),
                                            nama_barang: it.nama_barang,
                                            kode_barang: resolvedKode,
                                            qty: parseInt(it.qty) || 1,
                                            qty_acc: this.formData.status === 'Ditolak' ? 0 : (this.isSubAdmin ? null : registerIds.length),
                                            keterangan: it.keterangan || '-',
                                            register_ids: registerIds
                                        };
                                    })
                                };

                                const url = this.isEdit ? ('/distribusi/' + this.editId) : '{{ route("distribusi.save") }}';
                                const method = this.isEdit ? 'PUT' : 'POST';

                                const response = await fetch(url, {
                                    method: method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(dbPayload)
                                });

                                const result = await response.json();
                                console.log('[submitForm] server response:', response.status, result);

                                if (response.ok && result.success) {
                                    // Update nomor BAST dari server (ID sudah terbentuk)
                                    if (result.bast_nomor) {
                                        this.formData.bast_nomor = result.bast_nomor;
                                    }
                                    sessionStorage.setItem('flash_success', this.isEdit ? ('Data transaksi distribusi ' + this.formData.kode + ' berhasil diperbarui!') : ('Data transaksi distribusi ' + this.formData.kode + ' berhasil ditambahkan! No. BAST: ' + (result.bast_nomor || '')));
                                    this.showSimatToast(
                                        (this.isEdit ? '✏️ Distribusi berhasil diperbarui!' : '✅ Distribusi berhasil disimpan!') +
                                        (result.bast_nomor ? ' | No. BAST: ' + result.bast_nomor : ''),
                                        'success'
                                    );
                                    setTimeout(() => {
                                        window.location.href = '{{ route("distribusi.index") }}';
                                    }, 1200);
                                } else {
                                    let errorMsg = result.message || 'Terjadi kesalahan pada server';
                                    if (result.errors) {
                                        const errorDetails = Object.values(result.errors).flat().join('; ');
                                        if (errorDetails) errorMsg += ' (' + errorDetails + ')';
                                    }
                                    this.showSimatToast('❌ Gagal menyimpan distribusi: ' + errorMsg, 'error');
                                }
                            } catch(e) {
                                console.error(e);
                                this.showSimatToast('❌ Terjadi kesalahan jaringan / server saat menyimpan data.', 'error');
                            } finally {
                                this.isSaving = false;
                            }
                        }
                    });
                }
            };
        }
    </script>

    <div x-data="formDistribusiApp()" x-cloak class="space-y-6">

        <!-- Top Navigation Bar -->
        <div class="flex items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <a href="{{ route('distribusi.index') }}" 
                   class="w-10 h-10 rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold mb-1">
                        <span x-text="isSubAdmin ? '📋 PENGAJUAN PERMINTAAN ASTAP RUANGAN' : (isEdit ? '✏️ UBAH DISTRIBUSI BARANG' : '🚚 INPUT DISTRIBUSI MULTI-BARANG')"></span>
                    </div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-white tracking-tight"
                        x-text="isSubAdmin ? 'Form Input Pengajuan Baru' : (isEdit ? 'Form Ubah Distribusi ASTAP' : 'Form Distribusi & Penyerahan ASTAP')"></h1>
                    <p class="text-xs text-slate-400 mt-0.5"
                       x-text="isSubAdmin ? 'Pengajuan kebutuhan barang untuk unit ruangan Anda — penentuan NIBAR & verifikasi fisik diproses oleh Admin.' : 'Dapat memasukkan beberapa barang berbeda sekaligus dalam satu transaksi penyerahan ke ruangan.'"></p>
                </div>
            </div>
        </div>

        <!-- Form Card Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            
            <!-- Banner Peringatan Status Ditolak -->
            <div x-show="formData.status === 'Ditolak'" 
                 x-transition
                 class="p-4 sm:p-5 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-start sm:items-center space-x-3.5 text-rose-300 shadow-lg">
                <div class="w-10 h-10 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/30 flex items-center justify-center text-lg shrink-0 font-bold">
                    🚫
                </div>
                <div class="space-y-0.5 min-w-0 flex-1">
                    <h4 class="font-extrabold text-white text-xs sm:text-sm flex items-center space-x-2">
                        <span>Status Distribusi: Ditolak</span>
                        <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 text-[10px] font-bold border border-rose-500/30">Terkunci</span>
                    </h4>
                    <p class="text-[11px] sm:text-xs text-rose-200/90 leading-relaxed">
                        Formulir distribusi ini berstatus <strong>Ditolak</strong> sehingga seluruh isian data terkunci dan tidak dapat diedit. Untuk dapat mengisi atau memperbarui data formulir, silakan ubah <strong>Status Distribusi</strong> ke status lain.
                    </p>
                </div>
            </div>

            <!-- BAGIAN 1: INFORMASI TRANSAKSI & TUJUAN PENERIMA (AUTOFILL DATA UNIT) -->
            <div class="space-y-4">
                <h3 class="text-sm font-extrabold text-teal-300 uppercase tracking-wider flex items-center space-x-2 border-b border-slate-800 pb-3">
                    <span x-text="isSubAdmin ? '1. Informasi Pengajuan & Pegawai Ruangan' : '1. Informasi Penyerahan & Pegawai Penerima Ruangan'"></span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kode Transaksi Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">No. Registrasi Distribusi</label>
                        <input type="text" x-model="formData.kode" readonly
                               class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-teal-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                    </div>

                    <!-- Nomor BAST Rujukan -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">
                            No. BAST Distribusi
                        </label>
                        <input type="text" x-model="formData.bast_nomor"
                               :readonly="isSubAdmin || formData.status === 'Ditolak' || formData.status === 'Menunggu Konfirmasi'"
                               :disabled="formData.status === 'Ditolak'"
                               :placeholder="formData.status === 'Ditolak' ? '(tidak diterbitkan)' : (formData.status === 'Menunggu Konfirmasi' ? '(Menunggu Konfirmasi)' : '032 / ... / 430.10.7 / 2026')"
                               :class="(isSubAdmin || formData.status === 'Ditolak' || formData.status === 'Menunggu Konfirmasi') ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed' : 'bg-slate-950 text-white'"
                               class="w-full border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-mono focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Tanggal Distribusi / Pengajuan -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                            <span x-text="isSubAdmin ? 'Tanggal Pengajuan' : 'Tanggal Penyerahan'"></span>
                            <span class="text-[10px] text-teal-400 font-normal">Auto Hari Ini</span>
                        </label>
                        <input type="text" x-datepicker x-model="formData.tgl"
                               :value="formData.tgl"
                               @change="updateYearInKode()"
                               placeholder="dd/mm/yyyy"
                               :readonly="isSubAdmin || formData.status === 'Ditolak'"
                               :disabled="formData.status === 'Ditolak'"
                               :class="(isSubAdmin || formData.status === 'Ditolak') ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed pointer-events-none' : 'bg-slate-950 text-white'"
                               class="w-full border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs focus:outline-none focus:border-teal-500">
                    </div>

                    <!-- Status Distribusi -->
                    <div>
                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                            <span>Status Distribusi</span>
                            <span class="text-teal-400 text-[10px] font-bold" x-text="isSubAdmin ? '🔒 Dikelola Admin' : '⚡ Status Transaksi'"></span>
                        </label>
                        
                        <!-- Dropdown Status (Hanya untuk Admin & Master Admin) -->
                        <template x-if="!isSubAdmin">
                            <select x-model="formData.status"
                                    @change="onStatusChange()"
                                    class="w-full bg-slate-950 border rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-teal-500 transition-all cursor-pointer"
                                    :class="{
                                        'border-rose-500/50 ring-1 ring-rose-500/30 text-rose-400': formData.status === 'Ditolak',
                                        'border-slate-800 text-emerald-400': formData.status === 'Telah Diterima' || formData.status === 'Diterima',
                                        'border-slate-800 text-amber-400': formData.status === 'Dalam Pengiriman' || formData.status === 'Dikirim',
                                        'border-slate-800 text-cyan-400': formData.status === 'Menunggu Konfirmasi' || formData.status === 'Pending'
                                    }">
                                <option value="Menunggu Konfirmasi">⏳ Menunggu Konfirmasi</option>
                                <option value="Dalam Pengiriman">🚚 Dalam Pengiriman</option>
                                <option value="Telah Diterima">🟢 Telah Diterima</option>
                                <option value="Ditolak">❌ Ditolak</option>
                            </select>
                        </template>

                        <!-- Readonly Badge Status (Untuk Sub Admin Ruangan) -->
                        <template x-if="isSubAdmin">
                            <div class="w-full h-10 bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 flex items-center text-xs font-bold space-x-2 cursor-not-allowed"
                                 :class="{
                                     'text-emerald-400': formData.status === 'Telah Diterima' || formData.status === 'Diterima',
                                     'text-amber-400': formData.status === 'Dalam Pengiriman' || formData.status === 'Dikirim',
                                     'text-cyan-400': formData.status === 'Menunggu Konfirmasi' || formData.status === 'Pending',
                                     'text-rose-400': formData.status === 'Ditolak',
                                     'text-slate-400': formData.status === 'Draft' || !formData.status
                                 }">
                                <span x-text="formData.status === 'Ditolak' ? '❌' : (formData.status === 'Telah Diterima' ? '🟢' : (formData.status === 'Dalam Pengiriman' ? '🚚' : (formData.status === 'Draft' || !formData.status ? '📝' : '⏳')))"></span>
                                <span x-text="formData.status || 'Draft'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Input Unit & Data PIC Penerima -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-950 border border-slate-800 space-y-4">
                    
                    <!-- KONDISI A: Akun Sub Admin (Unit Terkunci Otomatis Sesuai Akun Login) -->
                    <template x-if="isSubAdmin">
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-300 flex items-center space-x-1.5">
                                    <span>🏥 Unit / Ruangan Anda</span>
                                    <span class="text-teal-400 font-mono text-[10px] bg-teal-500/20 px-2 py-0.5 rounded-full border border-teal-500/30">🔒 Terkunci Otomatis Sesuai Akun</span>
                                </label>
                            </div>
                            <div class="relative flex items-center">
                                <input type="text" :value="formData.tujuan || (userUnit ? userUnit.nama : 'Unit Ruangan')" readonly
                                       class="w-full bg-slate-900/70 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 text-xs text-teal-300 font-extrabold cursor-not-allowed">
                                <svg class="w-4 h-4 text-teal-400 absolute left-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>
                        </div>
                    </template>

                    <!-- KONDISI B: Admin & Master Admin (Bisa Cari & Pilih Unit Bebas) -->
                    <template x-if="!isSubAdmin">
                        <div class="relative" @click.away="isSearchingUnit = false">
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="block text-xs font-bold text-slate-300 flex items-center space-x-1.5">
                                    <span>🏥 Unit / Ruangan / Paviliun Tujuan</span>
                                    <span class="text-teal-400 font-mono text-[11px]" x-text="'(' + unitList.length + ' Unit Terdaftar)'"></span>
                                </label>
                                <template x-if="formData.tujuan && formData.status !== 'Ditolak'">
                                    <button type="button" @click="clearUnit()" class="text-xs text-rose-400 hover:text-rose-300 font-semibold cursor-pointer">
                                        ✕ Ganti Unit
                                    </button>
                                </template>
                            </div>

                            <div class="relative">
                                <input type="text" x-model="unitSearch" 
                                       :disabled="formData.status === 'Ditolak'"
                                       :readonly="formData.status === 'Ditolak'"
                                       @focus="if (formData.status !== 'Ditolak') isSearchingUnit = true" 
                                       @input="if (formData.status !== 'Ditolak') isSearchingUnit = true" 
                                       :placeholder="formData.status === 'Ditolak' ? 'Tujuan unit terkunci' : 'Ketik nama unit / ruangan (contoh: IGD, Melati, Radiologi, Bedah)...'" 
                                       :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white border-slate-800 focus:border-teal-500'"
                                       class="w-full border rounded-xl px-4 py-2.5 pl-10 text-xs placeholder-slate-500 focus:outline-none transition-all font-semibold">
                                <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>

                            <!-- Dropdown Autocomplete Unit (Dark Themed) -->
                            <div x-show="isSearchingUnit && formData.status !== 'Ditolak'" 
                                 x-transition 
                                 class="absolute left-0 right-0 z-30 mt-1 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-56 overflow-y-auto divide-y divide-slate-800">
                                <template x-for="u in filteredUnitList.slice(0, 5)" :key="u.id">
                                    <div @click="selectUnit(u)" 
                                         class="p-3 hover:bg-teal-500/15 cursor-pointer transition-colors flex items-center justify-between group">
                                        <div>
                                            <p class="font-bold text-white text-xs group-hover:text-teal-300" x-text="u.nama"></p>
                                            <p class="text-[10px] text-slate-400" x-text="(u.kode || 'UNIT') + ' • ' + (u.tipe || 'Unit') + ' • PJ: ' + u.kepala"></p>
                                        </div>
                                        <span class="px-2 py-1 rounded bg-teal-500/20 text-teal-300 text-[10px] font-bold">Pilih &rarr;</span>
                                    </div>
                                </template>
                                <template x-if="filteredUnitList.length === 0">
                                    <div class="p-3 text-center text-xs text-slate-500">Unit tidak ditemukan</div>
                                </template>
                            </div>
                        </div>
                    </template>

                    <!-- Auto-filled PIC Penerima Details (SELALU Readonly - otomatis dari unit) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 border-t border-slate-800/80">
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">Pegawai Penerima (Kepala/PJ)</span>
                            <input type="text" x-model="formData.penerima" 
                                   readonly
                                   placeholder="Terisi otomatis saat pilih unit..." 
                                   class="w-full bg-slate-900/60 text-slate-300 border border-slate-800 rounded-lg px-3 py-2 text-xs font-bold cursor-not-allowed focus:outline-none"
                                   title="Otomatis terisi dari data kepala unit">
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">NIP Pegawai</span>
                            <input type="text" x-model="formData.penerima_nip" 
                                   readonly
                                   placeholder="Terisi otomatis saat pilih unit..." 
                                   class="w-full bg-slate-900/60 text-slate-400 border border-slate-800 rounded-lg px-3 py-2 text-xs font-mono cursor-not-allowed focus:outline-none"
                                   title="Otomatis terisi dari data NIP kepala unit">
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold mb-1">Jabatan Penerima</span>
                            <input type="text" x-model="formData.penerima_jabatan" 
                                   readonly
                                   placeholder="Terisi otomatis saat pilih unit..." 
                                   class="w-full bg-slate-900/60 text-slate-400 border border-slate-800 rounded-lg px-3 py-2 text-xs cursor-not-allowed focus:outline-none"
                                   title="Otomatis terisi dari jabatan kepala unit">
                        </div>
                    </div>

                </div>
            </div>

            <!-- BAGIAN 2: DAFTAR BARANG YANG DIDISTRIBUSIKAN (MULTI-BARANG DALAM 1 TRANSAKSI) -->
            <div class="space-y-4 pt-2">
                <div class="border-b border-slate-800 pb-3">
                    <h3 class="text-sm font-extrabold text-teal-300 uppercase tracking-wider flex items-center space-x-2">
                        <span>2. Rincian Barang Aset yang Didistribusikan</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pilih Jenis ASTAP untuk membatasi daftar nama barang — Satuan, kode 108, dan spesifikasi terisi otomatis</p>
                </div>

                <!-- Daftar Input Multi-Barang (Layout Card Terstruktur & Rapi) -->
                <div class="space-y-5">
                    <template x-for="(item, idx) in formData.items" :key="item.id">
                        <div class="bg-slate-950/90 border border-slate-800 hover:border-slate-700/80 rounded-3xl p-5 sm:p-6 transition-all shadow-lg space-y-5">

                            <!-- Card Header: Nomor Barang, Nama Terpilih Dinamis, Badge Kode 108 & Tombol Hapus Barang di Pojok Kanan -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-800 gap-3">
                                <div class="flex items-center space-x-3 min-w-0">
                                    <span class="w-7 h-7 rounded-xl bg-teal-500/20 text-teal-300 font-extrabold text-xs flex items-center justify-center border border-teal-500/30 shrink-0" x-text="idx + 1"></span>
                                    
                                    <!-- Judul Dinamis Mengikuti Barang yang Dipilih -->
                                    <div class="min-w-0 flex items-center space-x-2">
                                        <span class="text-sm font-extrabold text-white tracking-wide truncate" 
                                              x-text="item.nama_barang ? item.nama_barang : ('Rincian Barang #' + (idx + 1))"></span>
                                        <template x-if="item.jenis_astap_nama">
                                            <span class="px-2.5 py-0.5 rounded-md bg-teal-500/10 border border-teal-500/20 text-teal-300 text-[10px] font-bold hidden md:inline-block truncate max-w-[220px]" x-text="item.jenis_astap_nama"></span>
                                        </template>
                                    </div>
                                    
                                    <!-- Badge Otomatis Kode 108 -->
                                    <template x-if="item.kode_barang">
                                        <span class="px-2.5 py-0.5 rounded-lg bg-slate-900 border border-cyan-500/30 text-cyan-400 font-mono font-bold text-[10px] shrink-0 hidden sm:inline-block" x-text="'Kode: ' + item.kode_barang"></span>
                                    </template>
                                </div>

                                <!-- Tombol Hapus Barang — Masuk di Dalam Form Pojok Kanan Atas Header -->
                                <template x-if="formData.status !== 'Ditolak'">
                                    <button type="button" @click="removeItem(idx)"
                                            class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 text-xs font-semibold flex items-center space-x-1.5 transition-all active:scale-95 shrink-0 cursor-pointer"
                                            title="Hapus baris barang ini">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                        <span class="hidden sm:inline">Hapus</span>
                                    </button>
                                </template>
                            </div>

                            <!-- Grid Form Input Barang -->
                            <div class="space-y-4">
                                
                                <!-- Baris 0: Jenis ASTAP (Diatas Nama Barang, Format Sama Seperti Nama Barang) -->
                                <div class="relative" @click.away="if (activeJenisDropdownIndex === idx) activeJenisDropdownIndex = null">
                                    <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                        <span class="flex items-center space-x-1.5">
                                            <span class="text-teal-400">🏷️</span>
                                            <span>Jenis ASTAP</span>
                                            <span class="text-slate-400 font-normal text-[11px] hidden sm:inline">(Pilih jenis untuk memfilter daftar barang)</span>
                                        </span>
                                        <span class="text-teal-400 font-mono text-[10px] hidden sm:inline">⚡ Pilih Jenis Aset</span>
                                    </label>
                                    <div class="relative flex items-center">
                                        <input type="text" 
                                               x-model="item.jenis_astap_nama" 
                                               :disabled="formData.status === 'Ditolak'"
                                               :readonly="formData.status === 'Ditolak'"
                                               @focus="if (formData.status !== 'Ditolak') activeJenisDropdownIndex = idx"
                                               @input="if (formData.status !== 'Ditolak') activeJenisDropdownIndex = idx"
                                               placeholder="Ketik atau pilih Jenis ASTAP (contoh: Peralatan dan Mesin, Gedung, Tanah)..." 
                                               :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white cursor-pointer border-slate-700/90 focus:border-teal-500 focus:ring-1 focus:ring-teal-500'"
                                               class="w-full h-11 border rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs font-bold placeholder-slate-500 focus:outline-none transition-all">
                                        
                                        <svg class="w-4 h-4 text-teal-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                        </svg>

                                        <!-- Tombol Silang Bersihkan Jenis ASTAP di Pojok Kanan Dalam Input -->
                                        <template x-if="formData.status !== 'Ditolak' && item.jenis_astap_nama && item.jenis_astap_nama.trim() !== ''">
                                            <button type="button" 
                                                    @click.stop="clearJenisAstap(item, idx)" 
                                                    style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); z-index: 20;"
                                                    class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm border border-slate-700/60 hover:border-rose-500/40"
                                                    title="Kosongkan jenis ASTAP">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </template>
                                    </div>

                                    <!-- Floating Dropdown Hasil Filter Jenis ASTAP (Hanya Menampilkan Nama Jenis) -->
                                    <div x-show="formData.status !== 'Ditolak' && activeJenisDropdownIndex === idx" 
                                         x-transition 
                                         class="absolute left-0 right-0 z-50 mt-1.5 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">
                                        
                                        <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                            <span>Pilih Master Jenis ASTAP</span>
                                            <span class="text-teal-400 font-mono" x-text="getFilteredJenisAstap(item.jenis_astap_nama).length + ' jenis tersedia'"></span>
                                        </div>

                                        <template x-for="j in getFilteredJenisAstap(item.jenis_astap_nama)" :key="j.kode || j.nama">
                                            <div @click="selectJenisAstap(item, j)"
                                                 class="px-4 py-3 hover:bg-teal-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3"
                                                 :class="{'bg-teal-500/10': item.jenis_astap_nama === j.nama}">
                                                <div class="flex items-center space-x-2.5">
                                                    <span class="w-2 h-2 rounded-full bg-teal-400"></span>
                                                    <p class="font-bold text-xs text-white group-hover:text-teal-300" x-text="j.nama"></p>
                                                </div>
                                                <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-700 text-teal-300 text-[10px] font-bold shrink-0">Pilih &rarr;</span>
                                            </div>
                                        </template>

                                        <template x-if="getFilteredJenisAstap(item.jenis_astap_nama).length === 0">
                                            <div class="p-4 text-center text-xs text-slate-400">
                                                <p class="text-amber-400 font-semibold">Tidak ditemukan Jenis ASTAP</p>
                                                <p class="text-[10px] text-slate-500 mt-0.5">Coba gunakan kata kunci pencarian yang lain</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- Baris 1: Nama Barang & Kode Rekening 108 (Selalu Sejajar Berdampingan) -->
                                <div class="flex flex-row items-end gap-3 w-full">
                                    
                                    <!-- 1. Nama Barang / Aset (Autocomplete Search Langsung Berdasarkan Filter Jenis ASTAP) -->
                                    <div class="flex-1 min-w-0 relative" @click.away="if (activeDropdownIndex === idx) activeDropdownIndex = null">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span class="flex items-center space-x-1.5">
                                                <span>Nama Barang / Aset ASTAP</span>
                                                <template x-if="item.jenis_astap_nama">
                                                    <span class="text-teal-400 text-[10px] bg-teal-500/10 px-2 py-0.5 rounded border border-teal-500/20 font-semibold" x-text="'Filter: ' + item.jenis_astap_nama"></span>
                                                </template>
                                            </span>
                                            <span class="text-teal-400 font-mono text-[10px] hidden sm:inline" x-text="item.jenis_astap_nama ? '⚡ Sesuai Jenis Terpilih' : '⚡ Ketik untuk filter'"></span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <input type="text" 
                                                   x-model="item.nama_barang" 
                                                   :disabled="formData.status === 'Ditolak'"
                                                   :readonly="formData.status === 'Ditolak'"
                                                   @focus="if (formData.status !== 'Ditolak') activeDropdownIndex = idx"
                                                   @input="if (formData.status !== 'Ditolak') { activeDropdownIndex = idx; onNamaBarangInput(item); }"
                                                   :placeholder="item.jenis_astap_nama ? ('Ketik nama barang dari ' + item.jenis_astap_nama + '...') : 'Ketik nama barang aset (contoh: laptop, monitor, kasur)...'" 
                                                   :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white border-slate-700/90 focus:border-teal-500 focus:ring-1 focus:ring-teal-500'"
                                                   class="w-full h-11 border rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs font-bold placeholder-slate-500 focus:outline-none transition-all">
                                            
                                            <svg class="w-4 h-4 text-teal-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                            </svg>

                                            <!-- Tombol Silang Bersihkan Nama Barang di Pojok Kanan Dalam Input -->
                                            <template x-if="formData.status !== 'Ditolak' && item.nama_barang && item.nama_barang.trim() !== ''">
                                                <button type="button" 
                                                        @click.stop="clearItemBarang(item, idx)" 
                                                        style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); z-index: 20;"
                                                        class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm border border-slate-700/60 hover:border-rose-500/40"
                                                        title="Kosongkan nama barang">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Floating Dropdown Hasil Ketik Filter ASTAP -->
                                        <div x-show="formData.status !== 'Ditolak' && activeDropdownIndex === idx" 
                                             x-transition 
                                             class="absolute left-0 right-0 z-40 mt-1.5 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">
                                            
                                            <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                                <span x-text="item.jenis_astap_nama ? ('Pilih Master ASTAP (' + item.jenis_astap_nama + ')') : 'Pilih Data Master ASTAP'"></span>
                                                <span class="text-teal-400 font-mono" x-text="getFilteredAstap(item, item.nama_barang).length + ' barang tersedia'"></span>
                                            </div>

                                            <template x-for="ast in getFilteredAstap(item, item.nama_barang)" :key="ast.id">
                                                <div @click="selectAstapItem(item, ast)"
                                                     :class="isItemAlreadySelected(ast, item) ? 'opacity-40 cursor-not-allowed bg-slate-950/40' : 'hover:bg-teal-500/15 cursor-pointer'"
                                                     class="px-4 py-2.5 transition-colors group flex items-center justify-between gap-3">
                                                    <div class="space-y-0.5">
                                                        <div class="flex items-center gap-2">
                                                            <p class="font-bold text-xs text-white group-hover:text-teal-300" x-text="ast.nama"></p>
                                                            <template x-if="isItemAlreadySelected(ast, item)">
                                                                <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold">Sudah Dipilih</span>
                                                            </template>
                                                        </div>
                                                        <p class="text-[10px] text-slate-400" x-text="ast.kode + (ast.jenis_nama ? ' • ' + ast.jenis_nama : (ast.kategori ? ' • ' + ast.kategori : '')) + (ast.merk ? ' • ' + ast.merk : '')"></p>
                                                    </div>
                                                    <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-slate-700 text-teal-300 font-mono text-[10px] font-bold shrink-0" x-text="ast.satuan || 'Unit'"></span>
                                                </div>
                                            </template>

                                            <template x-if="getFilteredAstap(item, item.nama_barang).length === 0">
                                                <div class="p-4 text-center text-xs text-slate-400">
                                                    <p class="text-amber-400 font-semibold" x-text="item.jenis_astap_nama ? ('Tidak ditemukan barang untuk ' + item.jenis_astap_nama) : 'Tidak ditemukan barang ASTAP'"></p>
                                                    <p class="text-[10px] text-slate-500 mt-0.5">Ketik nama lain atau isi nama barang secara manual</p>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- 2. Kode Rekening 108 (Sejajar di Samping Nama Barang) -->
                                    <div class="w-48 sm:w-56 shrink-0">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5">Kode Rekening 108</label>
                                        <input type="text" x-model="item.kode_barang" placeholder="Terisi otomatis..." readonly
                                               :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-500 border-slate-800' : 'bg-slate-900 text-cyan-300 border-slate-700/90'"
                                               class="w-full h-11 border rounded-xl px-4 py-2.5 text-xs font-mono font-bold placeholder-slate-500 focus:outline-none transition-all cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- Baris 2: Volume Pengajuan, Volume ACC (Admin) -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full">
                                    <!-- Vol 1: Volume Pengajuan (Qty) — Sub Admin & Admin bisa isi -->
                                    <div>
                                        <label class="block font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span class="flex items-center space-x-1.5">
                                                <span class="text-slate-300">Volume Pengajuan</span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-teal-500/15 border border-teal-500/30 text-teal-300 font-bold" x-text="isSubAdmin ? '📋 Diisi Anda' : '📋 Qty Diajukan'"></span>
                                            </span>
                                        </label>
                                        <div class="relative flex items-center">
                                            <input type="text"
                                                :value="item.qty ? Number(item.qty).toLocaleString('id-ID') : ''"
                                                :disabled="formData.status === 'Ditolak'"
                                                :readonly="formData.status === 'Ditolak'"
                                                @input="
                                                    if (formData.status === 'Ditolak') return;
                                                    let raw = $event.target.value.replace(/\D/g, '');
                                                    item.qty = raw ? parseInt(raw, 10) : '';
                                                    $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                    item.qty_acc = (item.nibar_selected || []).length;
                                                "
                                                placeholder="1"
                                                :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white border-slate-700/90 focus:border-teal-500'"
                                                class="w-full h-11 border rounded-xl px-4 py-2.5 pr-20 text-xs font-mono font-bold focus:outline-none transition-all">
                                            <!-- Suffix Satuan otomatis -->
                                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-teal-300 font-bold text-xs pointer-events-none px-2 py-0.5 rounded-lg bg-teal-500/10 border border-teal-500/20"
                                                  x-text="item.satuan || 'Unit'"></span>
                                        </div>
                                    </div>

                                    <!-- Vol 2: Volume ACC — Mengikuti NIBAR yang diinput & tidak dapat diedit -->
                                    <template x-if="!isSubAdmin">
                                        <div>
                                             <label class="block font-semibold text-xs mb-1.5 flex items-center justify-between">
                                                <span class="text-emerald-300">Volume Di-ACC</span>
                                                <template x-if="item.qty_acc && item.qty_acc > 0">
                                                    <span class="text-[10px] text-emerald-400 font-semibold" x-text="'✅ ACC: ' + item.qty_acc + ' ' + (item.satuan || 'Unit')"></span>
                                                </template>
                                            </label>
                                            <div class="relative flex items-center">
                                                <input type="text"
                                                    :value="((item.qty_acc !== null && item.qty_acc !== undefined && item.qty_acc !== '') ? item.qty_acc : (item.nibar_selected ? item.nibar_selected.length : 0)) + ' ' + (item.satuan || 'Unit')"
                                                    readonly
                                                    tabindex="-1"
                                                    title="Volume Di-ACC terisi otomatis mengikuti jumlah NIBAR yang diinput dan tidak dapat diedit manual"
                                                    :class="(!item.qty_acc || item.qty_acc <= 0) ? 'text-slate-400 border-slate-800' : 'text-emerald-400 border-emerald-500/30'"
                                                    class="w-full h-11 bg-slate-950/80 font-mono font-bold border rounded-xl px-4 py-2.5 text-xs cursor-not-allowed select-none focus:outline-none shadow-inner">
                                                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center space-x-1 pointer-events-none" :class="(!item.qty_acc || item.qty_acc <= 0) ? 'text-slate-600' : 'text-emerald-400/80'">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                </div>
                                            </div>
                                            <p class="text-[10px] text-slate-400 mt-1 flex items-center space-x-1">
                                                <template x-if="!isNibarEmpty(item) && (item.nibar_selected || []).length > 0">
                                                    <span>ℹ️ Otomatis mengikuti total NIBAR terpilih (<strong class="text-emerald-300 font-mono" x-text="(item.nibar_selected || []).length"></strong> NIBAR)</span>
                                                </template>
                                                <template x-if="!isNibarEmpty(item) && (!item.nibar_selected || item.nibar_selected.length === 0)">
                                                    <span class="text-amber-400/90">ℹ️ Belum ada NIBAR dipilih (barang ini tidak di-ACC)</span>
                                                </template>
                                                <template x-if="isNibarEmpty(item)">
                                                    <span class="text-rose-400">⚠️ Stok kosong — barang ini tidak di-ACC (0 Unit)</span>
                                                </template>
                                            </p>
                                        </div>
                                    </template>

                                    <!-- Info Box Volume ACC untuk Sub Admin (readonly, tidak bisa isi) -->
                                    <template x-if="isSubAdmin">
                                        <div>
                                            <label class="block text-slate-400 font-semibold text-xs mb-1.5 flex items-center space-x-1.5">
                                                <span>Volume Di-ACC</span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-700/60 border border-slate-700 text-slate-400 font-bold">🔒 Admin Only</span>
                                            </label>
                                            <div class="w-full h-11 bg-slate-950/80 border border-slate-800 rounded-xl px-4 flex items-center text-xs cursor-not-allowed"
                                                 :class="(item.qty_acc !== null && item.qty_acc !== '') ? 'text-emerald-400 font-bold border-emerald-500/30' : 'text-slate-500'">
                                                <span x-text="(item.qty_acc !== null && item.qty_acc !== '') ? ('✅ ' + Number(item.qty_acc).toLocaleString('id-ID') + ' ' + (item.satuan || 'Unit')) : '⏳ Menunggu Keputusan Admin'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Baris 1b: NIBAR Multi-Select (Admin & Master Admin bisa isi saat input baru maupun ubah) -->

                                <!-- Petunjuk: isi Volume Pengajuan dulu sebelum input NIBAR -->
                                <template x-if="!isSubAdmin && item.nama_barang && (!item.qty || item.qty <= 0)">
                                    <div class="flex items-center space-x-2 px-4 py-3 rounded-xl bg-amber-500/8 border border-amber-500/25 text-amber-300">
                                        <span class="text-base shrink-0">📋</span>
                                        <span class="text-xs font-semibold">Isi <strong>Volume Pengajuan</strong> terlebih dahulu sebelum memilih NIBAR.</span>
                                    </div>
                                </template>

                                <template x-if="!isSubAdmin && item.qty > 0">
                                    <div class="relative" @click.away="if(activeNibarDropdownIndex === idx) activeNibarDropdownIndex = null">
                                        <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center justify-between">
                                            <span class="flex items-center space-x-1.5">
                                                <span class="text-amber-400">🔖</span>
                                                <span>NIBAR (Nomor Induk Barang)</span>
                                                <template x-if="!item.nama_barang && !item.kode_barang">
                                                    <span class="text-slate-400 font-normal text-[11px] hidden sm:inline">— pilih nama barang terlebih dahulu</span>
                                                </template>
                                                <template x-if="(item.nama_barang || item.kode_barang) && !isNibarEmpty(item)">
                                                    <span class="text-slate-400 font-normal text-[11px] hidden sm:inline">— pilih NIBAR yang didistribusikan</span>
                                                </template>
                                            </span>
                                            
                                            <!-- Status Badge -->
                                            <div>
                                                <template x-if="!item.nama_barang && !item.kode_barang">
                                                    <span class="text-slate-500 font-mono text-[10px]">Pilih barang dulu</span>
                                                </template>
                                                <template x-if="(item.nama_barang || item.kode_barang) && isNibarEmpty(item)">
                                                    <span class="px-2 py-0.5 rounded bg-rose-500/15 border border-rose-500/30 text-rose-300 text-[10px] font-bold">
                                                        ⚠️ Barang Kosong (0 NIBAR)
                                                    </span>
                                                </template>
                                                <template x-if="(item.nama_barang || item.kode_barang) && !isNibarEmpty(item)">
                                                    <span class="text-amber-400 font-mono text-[10px]" x-text="(item.nibar_selected || []).length + ' NIBAR dipilih'"></span>
                                                </template>
                                            </div>
                                        </label>

                                        <!-- List NIBAR yang Dipilih dengan Pengaturan Kondisi Fisik Per Unit -->
                                        <template x-if="(item.nibar_selected || []).length > 0">
                                            <div class="space-y-2 mb-3 p-3 rounded-2xl bg-slate-950/60 border border-slate-800/80">
                                                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between pb-1 border-b border-slate-800/60">
                                                    <span class="flex items-center space-x-1.5">
                                                        <span>📋</span>
                                                        <span>Kondisi Fisik Per Unit NIBAR:</span>
                                                    </span>
                                                    <span class="text-amber-400 font-mono text-[10.5px]" x-text="(item.nibar_selected || []).length + ' Unit NIBAR'"></span>
                                                </div>
                                                
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 pt-1">
                                                    <template x-for="(n, nIdx) in item.nibar_selected" :key="n.nibar">
                                                        <div class="flex items-center justify-between gap-2 p-2.5 rounded-xl bg-slate-900 border border-slate-700/80 hover:border-amber-500/40 transition-all shadow-sm">
                                                             <!-- Info Nomor NIBAR & Ruang -->
                                                            <div class="min-w-0 flex-1">
                                                                <div class="flex items-center space-x-1.5">
                                                                    <span class="w-4 h-4 rounded-full bg-amber-500/20 text-amber-300 flex items-center justify-center text-[9px] font-bold font-mono" x-text="nIdx + 1"></span>
                                                                    <span class="font-mono font-bold text-xs text-amber-300 truncate" x-text="n.nibar"></span>
                                                                </div>
                                                                <p class="text-[9.5px] text-slate-400 truncate mt-0.5 pl-5" x-text="'Ruang: ' + (n.ruang || 'Gudang Aset')"></p>
                                                            </div>

                                                            <!-- Badge Kondisi Per Unit NIBAR (Read-Only / Selalu Kondisi Baik) -->
                                                            <div class="shrink-0 flex items-center space-x-1.5">
                                                                <span class="text-[10px] font-bold rounded-lg border px-2 py-1 select-none flex items-center space-x-1 bg-emerald-500/15 text-emerald-300 border-emerald-500/40">
                                                                    <span>🟢 Baik</span>
                                                                </span>

                                                                <!-- Tombol Hapus NIBAR -->
                                                                <template x-if="formData.status !== 'Ditolak'">
                                                                    <button type="button" @click.stop="removeNibar(item, n.nibar)"
                                                                            class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all border border-slate-700 hover:border-rose-500/40 cursor-pointer"
                                                                            title="Hapus NIBAR ini">
                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                                                    </button>
                                                                </template>
                                                            </div>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>

                                        <!-- KONDISI 1: Belum Pilih Barang -->
                                        <template x-if="!item.nama_barang && !item.kode_barang">
                                            <div class="relative flex items-center">
                                                <input type="text" 
                                                       disabled
                                                       placeholder="Pilih nama barang di atas terlebih dahulu untuk memilih NIBAR..." 
                                                       class="w-full h-11 bg-slate-900/50 border border-slate-800 rounded-xl px-4 py-2.5 pl-10 pr-4 text-xs text-slate-500 placeholder-slate-600 cursor-not-allowed">
                                                <svg class="w-4 h-4 text-slate-600 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                                            </div>
                                        </template>

                                        <!-- KONDISI 2: Barang Terpilih Tapi Data NIBAR Kosong -->
                                        <template x-if="(item.nama_barang || item.kode_barang) && isNibarEmpty(item)">
                                            <div class="space-y-1.5">
                                                <div class="relative flex items-center">
                                                    <input type="text" 
                                                           disabled
                                                           value="⚠️ Stok Kosong — Data register NIBAR belum tersedia di sistem" 
                                                           class="w-full h-11 bg-rose-950/20 border border-rose-500/40 rounded-xl px-4 py-2.5 pl-10 pr-4 text-xs text-rose-300 font-semibold cursor-not-allowed">
                                                    <svg class="w-4 h-4 text-rose-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                </div>
                                                <p class="text-[11px] text-rose-400/90 flex items-center space-x-1.5 pl-1">
                                                    <span>ℹ️ Register NIBAR aktif tidak ditemukan untuk <span class="font-mono font-bold text-white" x-text="getItemKode(item) || item.nama_barang"></span>. Volume Di-ACC tetap 0.</span>
                                                </p>
                                            </div>
                                        </template>

                                        <!-- KONDISI 3: Barang Terpilih & Ada NIBAR Tersedia -->
                                        <template x-if="(item.nama_barang || item.kode_barang) && !isNibarEmpty(item)">
                                            <div>
                                                <!-- Input Pencarian NIBAR -->
                                                <div class="relative flex items-center">
                                                    <input type="text" 
                                                           :value="nibarSearch[item.id] || ''"
                                                           :disabled="formData.status === 'Ditolak'"
                                                           :readonly="formData.status === 'Ditolak'"
                                                           @input="if (formData.status !== 'Ditolak') { nibarSearch = {...nibarSearch, [item.id]: $event.target.value}; activeNibarDropdownIndex = idx; }"
                                                           @focus="if (formData.status !== 'Ditolak') activeNibarDropdownIndex = idx"
                                                           :placeholder="formData.status === 'Ditolak' ? 'NIBAR terkunci' : 'Ketik atau klik untuk cari / pilih NIBAR...'" 
                                                           :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-500 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-white border-amber-500/40 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/50'"
                                                           class="w-full h-11 border rounded-xl px-4 py-2.5 pl-10 pr-10 text-xs font-mono placeholder-slate-500 focus:outline-none transition-all">
                                                    <svg class="w-4 h-4 text-amber-400 pointer-events-none" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%);" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>

                                                    <!-- Tombol Silang Reset Input Pencarian NIBAR di Pojok Kanan Dalam Input -->
                                                    <template x-if="formData.status !== 'Ditolak' && (nibarSearch[item.id] || '').trim() !== ''">
                                                        <button type="button" 
                                                                @click.stop="nibarSearch[item.id] = ''" 
                                                                style="position: absolute; right: 12px; left: auto; top: 50%; transform: translateY(-50%); z-index: 20;"
                                                                class="w-6 h-6 rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center transition-all cursor-pointer shadow-sm border border-slate-700/60 hover:border-rose-500/40"
                                                                title="Bersihkan pencarian NIBAR">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                        </button>
                                                    </template>
                                                </div>

                                                <!-- Dropdown NIBAR -->
                                                <div x-show="formData.status !== 'Ditolak' && activeNibarDropdownIndex === idx"
                                                     x-transition
                                                     class="absolute left-0 right-0 z-40 mt-1.5 bg-slate-900 border border-amber-500/30 rounded-2xl shadow-2xl overflow-hidden max-h-60 overflow-y-auto divide-y divide-slate-800">

                                                    <div class="px-4 py-2 bg-slate-950/90 text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                                        <span x-text="'Pilih NIBAR untuk ' + (item.nama_barang || '-')"></span>
                                                        <span class="text-emerald-400 font-bold" x-text="getFilteredNibar(item, nibarSearch[item.id] || '').length + ' Tersedia'"></span>
                                                    </div>

                                                    <template x-for="n in getFilteredNibar(item, nibarSearch[item.id] || '')" :key="n.nibar">
                                                        <div @click="selectNibar(item, n)"
                                                             class="px-4 py-2.5 cursor-pointer transition-colors group flex items-center justify-between gap-3 hover:bg-amber-500/15">
                                                            <div class="space-y-0.5 min-w-0">
                                                                <div class="flex items-center space-x-2">
                                                                    <p class="font-mono font-bold text-xs text-white group-hover:text-amber-300 truncate" x-text="n.nibar"></p>
                                                                    <span class="text-[9px] px-2 py-0.5 rounded font-extrabold shrink-0 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400">Tersedia</span>
                                                                </div>
                                                                <p class="text-[10px] text-slate-400 truncate" x-text="'Ruang: ' + n.ruang + ' • ' + n.kondisi"></p>
                                                            </div>
                                                            <span class="px-2.5 py-1 rounded-lg bg-slate-950 border border-amber-500/30 text-amber-300 text-[10px] font-bold shrink-0 shadow-sm">Pilih →</span>
                                                        </div>
                                                    </template>

                                                    <template x-if="getFilteredNibar(item, nibarSearch[item.id] || '').length === 0">
                                                        <div class="p-4 text-center text-xs text-slate-400">
                                                            <p class="text-amber-400 font-semibold">Tidak ada NIBAR yang cocok</p>
                                                            <p class="text-[10px] text-slate-500 mt-0.5">Semua NIBAR mungkin sudah dipilih</p>
                                                        </div>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </template>

                                <!-- Notice Box Khusus Akun Sub Admin Ruangan (NIBAR Dikelola Admin) -->
                                <template x-if="isSubAdmin">
                                    <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800 text-xs text-slate-400 flex items-start space-x-3 shadow-inner">
                                        <span class="text-teal-400 text-base shrink-0 mt-0.5">ℹ️</span>
                                        <div class="space-y-0.5">
                                            <p class="font-extrabold text-slate-200 text-xs flex items-center space-x-2">
                                                <span>Penomoran NIBAR 45 Karakter Dikelola Pengurus Barang</span>
                                                <span class="px-2 py-0.5 rounded bg-teal-500/20 text-teal-300 text-[10px] font-bold">Admin Only</span>
                                            </p>
                                            <p class="text-[11px] text-slate-400 leading-relaxed">
                                                Alokasi nomor register fisik NIBAR dan verifikasi kondisi unit akan diproses oleh <strong>Pengurus Barang / Admin</strong> saat permohonan disetujui. Cukup pilih nama barang dan volume (qty) yang diajukan.
                                            </p>
                                        </div>
                                    </div>
                                </template>

                                <!-- Baris 3: Keterangan / Catatan Spesifik Item (Sendiri / Full-Width) -->
                                <div>
                                    <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center space-x-1">
                                        <span>Keterangan / Catatan Peruntukan Barang</span>
                                        <span class="text-rose-400 font-bold" x-show="formData.status !== 'Ditolak'">*</span>
                                    </label>
                                    <input type="text" 
                                           x-model="item.keterangan" 
                                           :disabled="formData.status === 'Ditolak'"
                                           :readonly="formData.status === 'Ditolak'"
                                           placeholder="Contoh: u/ Ruang Tindakan IGD / Bed No. 04 / Pengadaan DAK Kesehatan..." 
                                           :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-900 text-slate-200 border-slate-700/90 focus:border-teal-500'"
                                           class="w-full h-11 border rounded-xl px-4 py-2.5 text-xs placeholder-slate-500 focus:outline-none transition-all">
                                </div>

                            </div>

                        </div>
                    </template>
                </div>

                <!-- Ringkasan Akumulasi Volume Multi-Barang & Tombol Tambah Bawah -->
                <div class="mt-4 p-4 sm:p-5 bg-slate-950/90 rounded-2xl border border-slate-800 shadow-lg space-y-3">
                    
                    <!-- Baris Atas: Tombol Tambah Barang di Atas -->
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1.5">
                            <span>📊</span>
                            <span>Ringkasan Rincian & Akumulasi Volume</span>
                        </span>

                        <template x-if="formData.status !== 'Ditolak'">
                            <button type="button" @click="addItem()" 
                                    class="px-4 py-2 rounded-xl bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 font-bold text-xs flex items-center space-x-2 transition-all active:scale-95 cursor-pointer shadow-sm">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Barang Lagi</span>
                            </button>
                        </template>
                    </div>

                    <!-- Baris Bawah: Grid 2 Kotak (Pengajuan & ACC Tetap Kanan-Kiri, Lebih Pendek & Proporsional) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        
                        <!-- Kotak 1: Ringkasan Pengajuan -->
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-900/90 border border-teal-500/30 shadow-sm hover:border-teal-500/50 transition-all">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-300 text-xs shrink-0">
                                    📋
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-extrabold tracking-wider uppercase text-teal-400 block leading-tight">Pengajuan</span>
                                    <p class="text-xs font-semibold text-slate-300 truncate">
                                        <strong class="text-white font-extrabold" x-text="formData.items.length"></strong>
                                        <span class="text-slate-400 text-[11px] ml-1">Jenis Barang</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right pl-3.5 border-l border-slate-800 shrink-0">
                                <span class="text-[9.5px] text-slate-400 font-semibold block leading-tight">Akumulasi Vol</span>
                                <span class="text-xs sm:text-sm font-mono font-black text-teal-300" x-text="getTotalItemVolume() + ' Unit'"></span>
                            </div>
                        </div>

                        <!-- Kotak 2: Ringkasan ACC (Ukuran & Layout Sama Persis) -->
                        <div class="flex items-center justify-between px-3.5 py-2.5 rounded-xl bg-slate-900/90 border border-emerald-500/30 shadow-sm hover:border-emerald-500/50 transition-all">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-300 text-xs shrink-0">
                                    ✅
                                </div>
                                <div class="min-w-0">
                                    <span class="text-[10px] font-extrabold tracking-wider uppercase text-emerald-400 block leading-tight">ACC</span>
                                    <p class="text-xs font-semibold text-slate-300 truncate">
                                        <strong class="text-white font-extrabold" x-text="getTotalRincianAcc()"></strong>
                                        <span class="text-slate-400 text-[11px] ml-1">Jenis Barang</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right pl-3.5 border-l border-slate-800 shrink-0">
                                <span class="text-[9.5px] text-slate-400 font-semibold block leading-tight">Akumulasi Vol</span>
                                <span class="text-xs sm:text-sm font-mono font-black text-emerald-400" x-text="getTotalItemVolumeAcc() + ' Unit'"></span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- BAGIAN 3: CATATAN UMUM PENEMPATAN -->
            <div class="pt-4 border-t border-slate-800">
                <label class="block text-slate-300 font-semibold text-xs mb-1.5 flex items-center space-x-1">
                    <span>Catatan Umum / Keterangan Penempatan</span>
                    <span class="text-rose-400 font-bold" x-show="formData.status !== 'Ditolak'">*</span>
                </label>
                <textarea x-model="formData.keterangan" 
                          :disabled="formData.status === 'Ditolak'"
                          :readonly="formData.status === 'Ditolak'"
                          rows="2" 
                          placeholder="Contoh: Pengadaan DAK Kesehatan / BLUD untuk kelengkapan ruangan..." 
                          :class="formData.status === 'Ditolak' ? 'bg-slate-950/80 text-slate-400 cursor-not-allowed border-slate-800' : 'bg-slate-950 text-white border-slate-800 focus:border-teal-500'"
                          class="w-full border rounded-xl px-4 py-3 text-xs placeholder-slate-500 focus:outline-none"></textarea>
            </div>

            <!-- Tombol Aksi Batal & Simpan (Hanya di Bagian Bawah Form Sesuai Permintaan) -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-end space-x-3">
                <a href="{{ route('distribusi.index') }}" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                    Batal
                </a>
                <button type="button" @click="submitForm()" :disabled="isSaving" class="px-6 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5 active:scale-95 disabled:opacity-50 cursor-pointer">
                    <svg x-show="!isSaving" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span x-text="isSaving ? 'Menyimpan...' : (isSubAdmin ? 'Kirim Pengajuan Distribusi' : (isEdit ? 'Simpan Perubahan' : 'Simpan Distribusi Baru'))"></span>
                </button>
            </div>
        </div>

        <!-- GLOBAL CUSTOM CONFIRMATION DIALOG MODAL (Sleek Dark Theme) -->
        <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div @click.away="showConfirmModal = false"
                 x-show="showConfirmModal"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-slate-900 border rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative"
                 :class="{
                     'border-rose-500/40': confirmData.type === 'danger',
                     'border-amber-500/40': confirmData.type === 'warning',
                     'border-emerald-500/40': confirmData.type === 'success',
                     'border-cyan-500/40': confirmData.type === 'info'
                 }">
                
                <!-- Header Icon & Title -->
                <div class="flex items-start space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 font-bold border"
                         :class="{
                             'bg-rose-500/20 text-rose-400 border-rose-500/30': confirmData.type === 'danger',
                             'bg-amber-500/20 text-amber-300 border-amber-500/30': confirmData.type === 'warning',
                             'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': confirmData.type === 'success',
                             'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': confirmData.type === 'info'
                         }">
                        <span x-text="confirmData.type === 'danger' ? '🗑️' : (confirmData.type === 'warning' ? '✏️' : '📦')"></span>
                    </div>
                    <div class="space-y-1 min-w-0 flex-1">
                        <h3 class="text-base font-extrabold text-white leading-snug" x-text="confirmData.title"></h3>
                        <p class="text-slate-300 text-xs leading-relaxed" x-text="confirmData.message"></p>
                    </div>
                </div>

                <!-- Item Target Preview Card -->
                <template x-if="confirmData.itemName">
                    <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Item Target:</span>
                        <p class="text-xs font-bold text-cyan-300 truncate font-mono" x-text="confirmData.itemName"></p>
                    </div>
                </template>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmedAction()"
                        class="px-5 py-2.5 rounded-xl font-extrabold text-xs shadow-lg transition-all active:scale-95 cursor-pointer flex items-center space-x-1.5"
                        :class="{
                            'bg-rose-500 hover:bg-rose-400 text-white shadow-rose-500/20': confirmData.type === 'danger',
                            'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/20': confirmData.type === 'warning',
                            'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/20': confirmData.type === 'success',
                            'bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-cyan-500/20': confirmData.type === 'info'
                        }">
                        <span x-text="confirmData.btnText"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- TOAST NOTIFICATION -->
        <div x-show="toast.show" x-cloak
             class="no-print fixed bottom-6 right-6 max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3"
             style="z-index: 100000 !important;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             :class="{
                 'border-emerald-500/40': toast.type === 'success',
                 'border-rose-500/40': toast.type === 'error',
                 'border-amber-500/40': toast.type === 'warning',
                 'border-cyan-500/40': toast.type === 'info'
             }">
            <div class="flex items-start space-x-3 flex-1 min-w-0">
                <span class="text-xl shrink-0 mt-0.5"
                      x-text="toast.type === 'success' ? '✅' : (toast.type === 'error' ? '❌' : (toast.type === 'warning' ? '⚠️' : 'ℹ️'))"></span>
                <p class="text-sm font-semibold leading-snug"
                   :class="{
                       'text-emerald-300': toast.type === 'success',
                       'text-rose-300': toast.type === 'error',
                       'text-amber-300': toast.type === 'warning',
                       'text-cyan-300': toast.type === 'info'
                   }"
                   x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white text-base font-bold shrink-0">&times;</button>
        </div>

    </div>

</x-layout>
