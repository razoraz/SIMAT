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
                    alasan_penolakan: '',
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
                        this.formData.alasan_penolakan = loadedData.alasan_penolakan || loadedData.alasan_tolak || '';
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
                        this.formData.alasan_penolakan = '';
                        if (autoUnit) {
                            this.unitSearch = autoUnit.nama;
                            this.selectedUnitObj = autoUnit;
                        }
                    }
                    this.updateYearInKode();

                    if (!this.formData.items || this.formData.items.length === 0) {
                        this.formData.items = [{ id: Date.now(), jenis_astap_kode: '', jenis_astap_nama: '', nama_barang: '', kode_barang: '', merk_type: '', qty: 1, qty_acc: 0, satuan: 'Unit', kondisi: '-', nibar_selected: [] }];
                    }

                    this.$watch('formData.tgl', () => {
                        this.updateYearInKode();
                    });
                },
                async updateYearInKode() {
                    if (!this.formData.tgl) return;
                    let tahun = '';
                    const match = String(this.formData.tgl).match(/(\d{4})/);
                    tahun = match ? match[1] : (new Date().getFullYear().toString());

                    if (tahun && this.formData.kode && this.formData.kode.startsWith('DST-')) {
                        const parts = this.formData.kode.split('-');
                        if (parts.length === 3) {
                            this.formData.kode = parts[0] + '-' + tahun + '-' + parts[2];
                        }
                    }
                    if (['Dalam Pengiriman', 'Telah Diterima', 'Dikirim', 'Diterima'].includes(this.formData.status)) {
                        await this.fetchNextBast(tahun);
                    }
                },
                async fetchNextBast(tahun) {
                    if (!tahun) {
                        const match = String(this.formData.tgl || '').match(/(\d{4})/);
                        tahun = match ? match[1] : (new Date().getFullYear().toString());
                    }

                    // Jika ini mode Edit dan tahun tanggal distribusi sama dengan data aslinya yang sudah memiliki nomor BAST resmi
                    if (this.isEdit && window.editingDistribusi && window.editingDistribusi.bast_nomor) {
                        const origBast = window.editingDistribusi.bast_nomor;
                        if (origBast.includes('/ ' + tahun) || origBast.endsWith('/' + tahun)) {
                            this.formData.bast_nomor = origBast;
                            return;
                        }
                    }

                    try {
                        const excludeParam = this.editId ? `&exclude_id=${this.editId}` : '';
                        const res = await fetch(`{{ route('distribusi.next-bast') }}?tahun=${tahun}${excludeParam}`);
                        if (res.ok) {
                            const data = await res.json();
                            if (data.success && data.bast_nomor) {
                                this.formData.bast_nomor = data.bast_nomor;
                                return;
                            }
                        }
                    } catch (e) {
                        console.error('Gagal mengambil nomor BAST otomatis:', e);
                    }

                    // Fallback jika fetch offline
                    if (this.formData.bast_nomor && this.formData.bast_nomor.includes('430.10.7')) {
                        const bParts = this.formData.bast_nomor.split('/');
                        if (bParts.length === 4) {
                            this.formData.bast_nomor = bParts[0].trim() + ' / ' + bParts[1].trim() + ' / ' + bParts[2].trim() + ' / ' + tahun;
                        }
                    } else {
                        this.formData.bast_nomor = '032 / 001 / 430.10.7 / ' + tahun;
                    }
                },
                async onStatusChange() {
                    if (this.formData.status === 'Ditolak') {
                        this.formData.bast_nomor = '(tidak diterbitkan)';
                    } else if (this.formData.status === 'Menunggu Konfirmasi' || this.formData.status === 'Draft') {
                        this.formData.bast_nomor = '(Menunggu Konfirmasi)';
                    } else if (this.formData.status === 'Dalam Pengiriman' || this.formData.status === 'Telah Diterima') {
                        const match = String(this.formData.tgl || '').match(/(\d{4})/);
                        const tahun = match ? match[1] : (new Date().getFullYear().toString());
                        await this.fetchNextBast(tahun);
                    }
                },
                addItem() {
                    if (this.formData.status === 'Ditolak') return;
                    this.formData.items.push({ id: Date.now(), jenis_astap_kode: '', jenis_astap_nama: '', nama_barang: '', kode_barang: '', merk_type: '', qty: 1, qty_acc: 0, satuan: 'Unit', kondisi: '-', nibar_selected: [] });
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
                    // Gabungkan NIBAR dari semua astap yang punya nama_barang sama (toleran terhadap simbol ellipsis … dan ...)
                    const clean = s => (s || '').replace(/…/g, '...').trim().toLowerCase();
                    if (item.nama_barang && n.nama_barang &&
                        clean(item.nama_barang) === clean(n.nama_barang)) return true;
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
                    const q = query.toLowerCase().trim();
                    return list.filter(a => (a.nama||'').toLowerCase().includes(q) || (a.kode||'').toLowerCase().includes(q));
                },
                getSlicedFilteredAstap(item, query, limit = 10) {
                    return this.getFilteredAstap(item, query).slice(0, limit);
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
                    item.astap_id = ast.id; item.nama_barang = ast.nama; item.kode_barang = ast.kode; item.merk_type = ast.merk || ''; item.satuan = ast.satuan || 'Unit'; if (!item.jenis_astap_nama && ast.jenis_nama) item.jenis_astap_nama = ast.jenis_nama; this.activeDropdownIndex = null;
                    item.nibar_selected = [];
                    item.qty_acc = 0;
                },
                clearItemBarang(item, idx) {
                    if (this.formData.status === 'Ditolak') return;
                    item.astap_id = null; item.nama_barang = ''; item.kode_barang = ''; item.merk_type = ''; item.satuan = 'Unit'; item.nibar_selected = []; item.qty_acc = 0; if (idx !== undefined) this.activeDropdownIndex = idx;
                },
                onNamaBarangInput(item) {
                    if (!item.nama_barang || item.nama_barang.trim() === '') { item.astap_id = null; item.kode_barang = ''; item.nibar_selected = []; item.qty_acc = 0; return; }
                    const match = (this.katalogAstap || []).find(a => a.nama && a.nama.toLowerCase().trim() === item.nama_barang.toLowerCase().trim());
                    if (match) {
                        if (this.isItemAlreadySelected(match, item)) {
                            alert('⚠️ Barang "' + match.nama + '" sudah dipilih pada baris lain!\n\nDalam satu transaksi distribusi tidak diperbolehkan menginput 2 nama barang yang sama.');
                            item.astap_id = null;
                            item.nama_barang = '';
                            item.kode_barang = '';
                            item.nibar_selected = [];
                            item.qty_acc = 0;
                            return;
                        }
                        item.astap_id = match.id; item.kode_barang = match.kode; item.merk_type = match.merk || ''; item.satuan = match.satuan || 'Unit'; if (!item.jenis_astap_nama) item.jenis_astap_nama = match.jenis_nama || '';
                    } else {
                        item.astap_id = null;
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
                    const cleanMsg = String(message || '').replace(/^[\s✅✔️☑️✓✔⚠️❌🚫⛔ℹ️🗑️✏️🔑💾]+/, '').trim();
                    this.toast = { show: true, message: cleanMsg, type };
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },
                async submitForm() {
                    console.log('[submitForm] status saat ini:', this.formData.status);
                    // Otomatis: Jika ada minimal 1 NIBAR yang di-ACC / dipilih oleh Admin, status otomatis 'Dalam Pengiriman'
                    const hasAnyAccNibar = (this.formData.items || []).some(it => {
                        const acc = (it.qty_acc !== null && it.qty_acc !== undefined && it.qty_acc !== '') ? parseInt(it.qty_acc) : ((it.nibar_selected || []).length);
                        return acc > 0;
                    });
                    if (!this.isSubAdmin && ['Menunggu Konfirmasi', 'Draft', 'Pending'].includes(this.formData.status) && hasAnyAccNibar) {
                        this.formData.status = 'Dalam Pengiriman';
                        await this.onStatusChange();
                    }

                    // Saat status Ditolak, pastikan keterangan tidak kosong agar tidak blocked di backend
                    if (this.formData.status === 'Ditolak') {
                        if (!this.formData.keterangan || this.formData.keterangan.trim() === '') {
                            this.formData.keterangan = '-';
                        }
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
                        await this.fetchNextBast();
                    }
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

                    // ── 4. Validasi Alasan Penolakan (Khusus Status Ditolak) ───────────
                    if (this.formData.status === 'Ditolak') {
                        if (!this.formData.alasan_penolakan || this.formData.alasan_penolakan.trim() === '' || this.formData.alasan_penolakan.trim() === '-') {
                            alert('⚠️ Mohon isi Alasan Penolakan terlebih dahulu!\n\nTransaksi dengan status Ditolak wajib mencantumkan alasan penolakan.');
                            return;
                        }
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
                                    alasan_penolakan: this.formData.status === 'Ditolak' ? (this.formData.alasan_penolakan || null) : null,
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
                                        (this.isEdit ? 'Distribusi berhasil diperbarui!' : 'Distribusi berhasil disimpan!') +
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

                                    // Jika terdapat ID register NIBAR yang konflik, otomatis lepaskan dari pilihan item formulir
                                    if (result.conflicted_register_ids && Array.isArray(result.conflicted_register_ids)) {
                                        const confIds = result.conflicted_register_ids.map(Number);
                                        (this.formData.items || []).forEach(it => {
                                            if (it.nibar_selected) {
                                                it.nibar_selected = it.nibar_selected.filter(n => !confIds.includes(Number(n.id)));
                                                it.qty_acc = it.nibar_selected.length;
                                            }
                                        });
                                        // Update status di nibarList lokal agar tidak dipilih kembali
                                        (this.nibarList || []).forEach(n => {
                                            if (confIds.includes(Number(n.id))) {
                                                n.status = 'Tidak Tersedia';
                                            }
                                        });
                                    }

                                    alert(errorMsg);
                                    this.showSimatToast('Gagal: ' + (result.message ? 'Terdapat konflik data alokasi.' : errorMsg), 'error');
                                }
                            } catch(e) {
                                console.error(e);
                                this.showSimatToast('Terjadi kesalahan jaringan / server saat menyimpan data.', 'error');
                            } finally {
                                this.isSaving = false;
                            }
                        }
                    });
                }
            };
        }
    </script>
