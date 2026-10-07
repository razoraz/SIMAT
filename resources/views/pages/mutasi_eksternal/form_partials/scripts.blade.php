<!-- ========================================================================= -->
<!-- ALPINE.JS STATE & SCRIPT LOGIC (PELIMPAHAN SKPD / MUTASI EKSTERNAL)       -->
<!-- ========================================================================= -->
<script>
    function formMutasiEksternal() {
        return {
            currentStep: 1,
            totalSteps: 3,
            stepErrors: { 1: '', 2: '', 3: '' },
            isSubmitting: false,
            isDataVerified: false,
            isEdit: {{ Js::from($isEdit) }},
            astapId: {{ Js::from($isEdit ? $astap->id : null) }},
            initialAstap: {{ Js::from($initialAstap) }},

            // Master Data Lists
            master108: {{ Js::from($dbMaster108 ?? []) }},
            pejabatsList: {{ Js::from($dbPejabats ?? []) }},
            unitsList: {{ Js::from($dbUnits ?? []) }},
            skpdDirectory: {{ Js::from($dbSkpdDirectory ?? []) }},
            pejabatPenyerahList: {{ Js::from($dbPejabatPenyerahs ?? []) }},
            masterSkpdList: {{ Js::from(!empty($dbSkpdAsals) ? $dbSkpdAsals : [
                'Dinas Kesehatan Kabupaten Bondowoso',
                'BPKAD Kabupaten Bondowoso',
                'Pemerintah Kabupaten Bondowoso',
                'Dinas Kesehatan Provinsi Jawa Timur'
            ]) }},
            isSkpdDropdownOpen: false,
            isPejabatDropdownOpen: false,

            activeKibCategory: 'mesin',

            // Form Data State
            formData: {
                from: {{ Js::from(request('from', 'eksternal')) }},
                sumber_dana: 'pelimpahan_skpd',
                is_extracomtable: false,
                tahun_perolehan: new Date().getFullYear(),
                triwulan: (function() {
                    const m = new Date().getMonth() + 1;
                    if (m >= 4 && m <= 6) return 'TW II';
                    if (m >= 7 && m <= 9) return 'TW III';
                    if (m >= 10 && m <= 12) return 'TW IV';
                    return 'TW I';
                })(),
                mutasi_asal: '',
                mutasi_nomor_bamb: '',
                mutasi_tanggal: new Date().toISOString().split('T')[0],
                nomor_sk_dasar: '',
                alamat_instansi: '',
                pj_asal_nama: '',
                pj_asal_nip: '',
                pj_asal_jabatan: '',
                ppk_nama: 'BUDI HARTONO, S.Sos',
                ppk_nip: '19760229 200801 1 010',
                mutasi_keterangan: '',
                dokumen_lampiran_path: '',
                jenis_astap_id: '',
                nama_barang: '',
                jumlah_volume: 1,
                satuan: 'Unit',
                total_realisasi: 0,
                unit_id: '',
                kondisi: 'Baik',
                alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',

                // Multi-Item Repeaters per KIB
                tanah_items: [],
                mesin_items: [],
                gedung_items: [],
                jaringan_items: [],
                lainnya_items: []
            },

            selectedFile: null,

            // Toast State
            toast: {
                show: false,
                title: '',
                message: '',
                type: 'success'
            },

            showToast(title, message, type = 'success') {
                this.toast.title = title;
                this.toast.message = message;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => {
                    this.toast.show = false;
                }, 5000);
            },

            // Helper: set / clear error pada step tertentu
            setStepError(s, msg) {
                this.stepErrors[s] = msg || '';
            },

            clearStepError(s) {
                this.stepErrors[s] = '';
            },

            clearAllStepErrors() {
                this.stepErrors = { 1: '', 2: '', 3: '' };
            },

            parseDateToTimestamp(d) {
                if (!d) return 0;
                if (d.includes('/')) {
                    const parts = d.split('/');
                    if (parts.length === 3) {
                        return new Date(parts[2], parts[1] - 1, parts[0]).getTime();
                    }
                }
                return new Date(d).getTime();
            },

            getTodayTimestamp() {
                const now = new Date();
                return new Date(now.getFullYear(), now.getMonth(), now.getDate(), 23, 59, 59, 999).getTime();
            },

            isTanggalBambInvalid() {
                if (!this.formData.mutasi_tanggal) return false;
                const t = this.parseDateToTimestamp(this.formData.mutasi_tanggal);
                return t > 0 && t > this.getTodayTimestamp();
            },

            // Formatters
            formatRupiah(val) {
                if (!val) return '0';
                return Number(val).toLocaleString('id-ID');
            },

            formatTanggalIndo(val) {
                if (!val) return '';
                val = String(val).trim();
                if (/^\d{4}-\d{2}-\d{2}$/.test(val)) {
                    const parts = val.split('-');
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
                return val;
            },

            // Hitung otomatis Triwulan dan Tahun dari tanggal dokumen
            calcTriwulanFromDate(dateStr) {
                if (!dateStr) return 'TW I';
                let month = 1;
                let year = null;
                const str = String(dateStr).trim();

                if (/^\d{4}-\d{2}-\d{2}/.test(str)) {
                    const parts = str.split('-');
                    year = parseInt(parts[0], 10);
                    month = parseInt(parts[1], 10);
                } else if (/^\d{1,2}\/\d{1,2}\/\d{4}/.test(str)) {
                    const parts = str.split('/');
                    const m = parseInt(parts[1], 10);
                    const y = parseInt(parts[2], 10);
                    if (!isNaN(y) && !isNaN(m)) {
                        year = y;
                        month = m;
                    }
                } else {
                    const d = new Date(str);
                    if (!isNaN(d.getTime())) {
                        year = d.getFullYear();
                        month = d.getMonth() + 1;
                    }
                }

                if (year && year >= 1990 && year <= 2100) {
                    this.formData.tahun_perolehan = year;
                }

                if (month >= 1 && month <= 3) return 'TW I';
                if (month >= 4 && month <= 6) return 'TW II';
                if (month >= 7 && month <= 9) return 'TW III';
                if (month >= 10 && month <= 12) return 'TW IV';
                return 'TW I';
            },

            onTanggalChange(val) {
                if (!val) return;
                this.formData.triwulan = this.calcTriwulanFromDate(val);
            },

            // Autocomplete SKPD Asal
            get filteredSkpdList() {
                const q = (this.formData.mutasi_asal || '').toLowerCase().trim();
                if (!q) {
                    return this.masterSkpdList.slice(0, 15);
                }
                return this.masterSkpdList.filter(skpd => skpd && skpd.toLowerCase().includes(q));
            },

            // Ambil ringkasan pejabat untuk item dropdown SKPD
            getSkpdPejabatInfo(skpdName) {
                if (!skpdName || !this.skpdDirectory) return '';
                const item = this.skpdDirectory[skpdName];
                if (!item || !item.pj_nama) return '';
                return item.pj_nama + (item.pj_jabatan ? ' (' + item.pj_jabatan + ')' : '');
            },

            // Ambil ringkasan alamat untuk item dropdown SKPD
            getSkpdAlamatInfo(skpdName) {
                if (!skpdName || !this.skpdDirectory) return '';
                const item = this.skpdDirectory[skpdName];
                if (item && item.alamat) return item.alamat;

                const lowerTarget = skpdName.toLowerCase().trim();
                for (const [key, val] of Object.entries(this.skpdDirectory)) {
                    if (key.toLowerCase() === lowerTarget || (val.nama && val.nama.toLowerCase() === lowerTarget)) {
                        return val.alamat || '';
                    }
                }
                return '';
            },

            // Sinkronisasi data Pejabat Penyerah (Pihak Pertama) dari SKPD yang dipilih
            syncPejabatFromSkpd(skpdName, force = false) {
                const target = (skpdName || this.formData.mutasi_asal || '').trim();
                if (!target) return;

                let matched = this.skpdDirectory ? this.skpdDirectory[target] : null;
                if (!matched && this.skpdDirectory) {
                    const lowerTarget = target.toLowerCase();
                    for (const [key, val] of Object.entries(this.skpdDirectory)) {
                        if (key.toLowerCase() === lowerTarget || (val.nama && val.nama.toLowerCase() === lowerTarget)) {
                            matched = val;
                            break;
                        }
                    }
                }

                if (matched && matched.pj_nama) {
                    if (force || !this.formData.pj_asal_nama) {
                        this.formData.pj_asal_nama = matched.pj_nama;
                        this.formData.pj_asal_nip = matched.pj_nip || '';
                        this.formData.pj_asal_jabatan = matched.pj_jabatan || '';
                        this.showToast('Pihak Pertama Terhubung', 'Data Pejabat Penyerah otomatis disesuaikan dengan riwayat ' + matched.nama, 'info');
                    }
                }
            },

            // Daftar pejabat yang tersedia untuk SKPD yang sedang dipilih
            get availablePejabatPenyerahs() {
                const curSkpd = (this.formData.mutasi_asal || '').trim().toLowerCase();
                if (curSkpd && this.skpdDirectory) {
                    for (const [key, info] of Object.entries(this.skpdDirectory)) {
                        if (key.toLowerCase() === curSkpd || (info.nama && info.nama.toLowerCase() === curSkpd)) {
                            if (info.pejabats && info.pejabats.length > 0) {
                                return info.pejabats.map(p => ({
                                    ...p,
                                    skpd: info.nama
                                }));
                            }
                        }
                    }
                }
                return this.pejabatPenyerahList || [];
            },

            // Filter dropdown Pejabat Penyerah
            get filteredPejabatPenyerahList() {
                const q = (this.formData.pj_asal_nama || '').toLowerCase().trim();
                const list = this.availablePejabatPenyerahs || [];
                if (!q) {
                    return list.slice(0, 15);
                }
                return list.filter(p => 
                    (p.nama && p.nama.toLowerCase().includes(q)) ||
                    (p.jabatan && p.jabatan.toLowerCase().includes(q)) ||
                    (p.nip && p.nip.includes(q))
                );
            },

            // Pilih pejabat penyerah dari dropdown
            selectPejabatPenyerah(p) {
                if (!p) return;
                this.formData.pj_asal_nama = p.nama || '';
                this.formData.pj_asal_nip = p.nip || '';
                this.formData.pj_asal_jabatan = p.jabatan || '';
                this.isPejabatDropdownOpen = false;
                if (p.skpd && !this.formData.mutasi_asal) {
                    this.formData.mutasi_asal = p.skpd;
                }
                this.showToast('Pejabat Dipilih', 'Pejabat Penyerah diset: ' + p.nama, 'info');
            },

            selectSkpd(name) {
                this.formData.mutasi_asal = name;
                this.isSkpdDropdownOpen = false;
                this.syncPejabatFromSkpd(name, true);
                const alamat = this.getSkpdAlamatInfo(name);
                if (alamat) {
                    this.formData.alamat_instansi = alamat;
                }
            },

            // KIB Category Helpers (Single Source of Truth)
            get isTanah() {
                return this.activeKibCategory === 'tanah';
            },

            get isMesin() {
                return this.activeKibCategory === 'mesin';
            },

            get isGedung() {
                return this.activeKibCategory === 'gedung';
            },

            get isJaringan() {
                return this.activeKibCategory === 'jaringan';
            },

            get isLainnya() {
                return this.activeKibCategory === 'lainnya';
            },

            get hasSelectedKib() {
                return true;
            },

            get kibLabel() {
                if (this.isTanah) return 'KIB A (Tanah)';
                if (this.isMesin) return 'KIB B (Peralatan & Mesin)';
                if (this.isGedung) return 'KIB C (Gedung & Bangunan)';
                if (this.isJaringan) return 'KIB D (Jalan, Irigasi & Jaringan)';
                if (this.isLainnya) return 'KIB E (Aset Tetap Lainnya)';
                return 'KIB B (Peralatan & Mesin)';
            },

            get activeKibCode() {
                if (this.isTanah) return '1.3.1';
                if (this.isMesin) return '1.3.2';
                if (this.isGedung) return '1.3.3';
                if (this.isJaringan) return '1.3.4';
                if (this.isLainnya) return '1.3.5';
                return '1.3.2';
            },

            // True Multi-Item hanya aktif bila jumlah rincian barang > 1
            get isMultiItemActive() {
                if (this.isTanah) return this.formData.tanah_items && this.formData.tanah_items.length > 1;
                if (this.isMesin) return this.formData.mesin_items && this.formData.mesin_items.length > 1;
                if (this.isGedung) return this.formData.gedung_items && this.formData.gedung_items.length > 1;
                if (this.isJaringan) return this.formData.jaringan_items && this.formData.jaringan_items.length > 1;
                if (this.isLainnya) return this.formData.lainnya_items && this.formData.lainnya_items.length > 1;
                return false;
            },

            // Selected Unit Name
            get selectedUnitName() {
                if (!this.formData.unit_id) return '';
                const u = this.unitsList.find(item => Number(item.id) === Number(this.formData.unit_id));
                return u ? u.nama : '';
            },

            // Flat 108 Items for Quick Lookup (Mendukung subRincian, subSubRincian, children)
            allFlat108: [],

            initFlat108() {
                const list = [];
                const recurse = (items, path = '') => {
                    if (!items) return;
                    const arr = Array.isArray(items) ? items : Object.values(items);
                    for (const it of arr) {
                        if (!it || typeof it !== 'object') continue;
                        const curPath = path ? `${path} > ${it.nama || ''}` : (it.nama || '');
                        const children = it.subRincian || it.subSubRincian || it.children || it.subs || it.subSubs;
                        if (children && (Array.isArray(children) ? children.length > 0 : Object.keys(children).length > 0)) {
                            recurse(children, curPath);
                        } else if (it.id) {
                            list.push({
                                id: it.id,
                                kode: it.kode || it.sub_sub_rincian_objek || it.jenis || '',
                                nama: it.nama || it.uraian || '',
                                path: curPath
                            });
                        }
                    }
                };
                recurse(this.master108);
                this.allFlat108 = list;
            },

            get selected108Item() {
                if (!this.formData.jenis_astap_id) return null;
                return this.allFlat108.find(i => Number(i.id) === Number(this.formData.jenis_astap_id)) || null;
            },

            selectKibCategory(cat, seedDefault = true) {
                this.activeKibCategory = cat;
                if (seedDefault) {
                    if (cat === 'tanah' && (!this.formData.tanah_items || this.formData.tanah_items.length === 0)) {
                        this.addTanahItem();
                    } else if (cat === 'mesin' && (!this.formData.mesin_items || this.formData.mesin_items.length === 0)) {
                        this.addMesinItem();
                    } else if (cat === 'gedung' && (!this.formData.gedung_items || this.formData.gedung_items.length === 0)) {
                        this.addGedungItem();
                    } else if (cat === 'jaringan' && (!this.formData.jaringan_items || this.formData.jaringan_items.length === 0)) {
                        this.addJaringanItem();
                    } else if (cat === 'lainnya' && (!this.formData.lainnya_items || this.formData.lainnya_items.length === 0)) {
                        this.addLainnyaItem();
                    }
                }
                this.ensureJenisAstapId();
                this.syncTotalsFromItems();
            },

            // Pastikan jenis_astap_id terisi otomatis sesuai KIB aktif jika belum dipilih
            ensureJenisAstapId() {
                let activeItems = [];
                let codeKey = '';
                if (this.isTanah) { activeItems = this.formData.tanah_items || []; codeKey = 'tanah_kode_barang'; }
                else if (this.isMesin) { activeItems = this.formData.mesin_items || []; codeKey = 'mesin_kode_barang'; }
                else if (this.isGedung) { activeItems = this.formData.gedung_items || []; codeKey = 'gedung_kode_barang'; }
                else if (this.isJaringan) { activeItems = this.formData.jaringan_items || []; codeKey = 'jaringan_kode_barang'; }
                else if (this.isLainnya) { activeItems = this.formData.lainnya_items || []; codeKey = 'lainnya_kode_barang'; }

                if (activeItems.length > 0 && codeKey) {
                    const itemWithKode = activeItems.find(i => i && i[codeKey]);
                    if (itemWithKode) {
                        const found = this.allFlat108.find(x => x.kode === itemWithKode[codeKey]);
                        if (found) {
                            this.formData.jenis_astap_id = found.id;
                            return;
                        }
                    }
                }
                const prefix = this.activeKibCode;
                if (!this.formData.jenis_astap_id || !this.selected108Item?.kode?.startsWith(prefix)) {
                    const match = this.allFlat108.find(x => x.kode && x.kode.startsWith(prefix));
                    if (match) {
                        this.formData.jenis_astap_id = match.id;
                    }
                }
            },

            // Sinkronisasi Nama Barang ke Item Pertama
            syncSingleItemName() {
                const name = this.formData.nama_barang || '';
                if (!name) return;
                if (this.isTanah && this.formData.tanah_items && this.formData.tanah_items[0] && !this.formData.tanah_items[0].tanah_nama_barang) {
                    this.formData.tanah_items[0].tanah_nama_barang = name;
                } else if (this.isMesin && this.formData.mesin_items && this.formData.mesin_items[0] && !this.formData.mesin_items[0].mesin_nama_barang) {
                    this.formData.mesin_items[0].mesin_nama_barang = name;
                } else if (this.isGedung && this.formData.gedung_items && this.formData.gedung_items[0] && !this.formData.gedung_items[0].gedung_nama_barang) {
                    this.formData.gedung_items[0].gedung_nama_barang = name;
                } else if (this.isJaringan && this.formData.jaringan_items && this.formData.jaringan_items[0] && !this.formData.jaringan_items[0].jaringan_nama_barang) {
                    this.formData.jaringan_items[0].jaringan_nama_barang = name;
                } else if (this.isLainnya && this.formData.lainnya_items && this.formData.lainnya_items[0] && !this.formData.lainnya_items[0].lainnya_judul) {
                    this.formData.lainnya_items[0].lainnya_judul = name;
                }
            },

            // Repeater Actions: Tanah
            addTanahItem() {
                if (!this.formData.tanah_items) {
                    this.formData.tanah_items = [];
                }
                this.formData.tanah_items.push({
                    tanah_kode_barang: '',
                    tanah_nama_barang: '',
                    isFilterOpen: false,
                    searchFilter: '',
                    tanah_hak: 'Hak Pakai',
                    tanah_sertifikat_tgl: '',
                    tanah_sertifikat_no: '',
                    tanah_kondisi: 'Baik',
                    tanah_penggunaan: 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                    tanah_jumlah_barang: 1,
                    tanah_satuan: 'Bidang',
                    tanah_luas_m2: '',
                    tanah_alamat: this.formData.alamat_barang || '',
                    tanah_nilai_satuan: 0,
                    tanah_nilai_fisik: 0
                });
                this.syncTotalsFromItems();
            },

            removeTanahItem(idx) {
                if (this.formData.tanah_items && this.formData.tanah_items.length > 1) {
                    this.formData.tanah_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getTanahSubtotal(item) {
                const qty = parseInt(item.tanah_jumlah_barang) || parseInt(item.tanah_jumlah_bidang) || 1;
                const unitVal = parseFloat(item.tanah_nilai_satuan) || parseFloat(item.tanah_nilai_fisik) || 0;
                return qty * unitVal;
            },

            get totalNilaiTanah() {
                if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    return this.formData.tanah_items.reduce((sum, item) => sum + this.getTanahSubtotal(item), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            // Setter Global Status Akuntansi (Aset Tetap Reguler vs Ekstrakomtabel)
            setGlobalExtracom(val) {
                const isExtra = Boolean(val);
                this.formData.is_extracomtable = isExtra;
                if (this.formData.mesin_items && Array.isArray(this.formData.mesin_items)) {
                    this.formData.mesin_items.forEach(m => {
                        m.is_extracom = isExtra;
                    });
                }
                if (this.formData.lainnya_items && Array.isArray(this.formData.lainnya_items)) {
                    this.formData.lainnya_items.forEach(l => {
                        l.is_extracom = isExtra;
                    });
                }
                this.syncTotalsFromItems();
            },

            // Repeater Actions: Mesin
            addMesinItem() {
                if (!this.formData.mesin_items) {
                    this.formData.mesin_items = [];
                }
                this.formData.mesin_items.push({
                    is_extracom: Boolean(this.formData.is_extracomtable),
                    mesin_kode_barang: '',
                    mesin_nama_barang: '',
                    isFilterOpen: false,
                    searchFilter: '',
                    mesin_merk: '',
                    mesin_type: '',
                    mesin_ukuran: '',
                    mesin_no_pabrik: '',
                    mesin_bahan: '',
                    mesin_tahun_pembuatan: null,
                    mesin_kondisi: 'Baik',
                    mesin_no_rangka: '',
                    mesin_no_mesin: '',
                    mesin_no_bpkb: '',
                    mesin_no_polisi: '',
                    mesin_jumlah_barang: 1,
                    mesin_satuan: this.formData.satuan || 'Unit',
                    mesin_nilai_satuan: 0,
                    mesin_keterangan: '',
                    ruang_pemegang: '',
                    isRuangOpen: false,
                    searchRuang: ''
                });
                this.syncTotalsFromItems();
            },

            removeMesinItem(idx) {
                if (this.formData.mesin_items.length > 1) {
                    this.formData.mesin_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getMesinSubtotal(item) {
                const qty = parseInt(item.mesin_jumlah_barang) || 1;
                const unitVal = parseFloat(item.mesin_nilai_satuan) || 0;
                return qty * unitVal;
            },

            get totalNilaiMesin() {
                if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    return this.formData.mesin_items.reduce((sum, item) => sum + this.getMesinSubtotal(item), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            // Repeater Actions: Gedung
            addGedungItem() {
                if (!this.formData.gedung_items) {
                    this.formData.gedung_items = [];
                }
                this.formData.gedung_items.push({
                    gedung_kode_barang: '',
                    gedung_nama_barang: '',
                    isFilterOpen: false,
                    searchFilter: '',
                    gedung_luas_m2: '',
                    gedung_bertingkat: 'Tidak',
                    gedung_beton: 'Beton',
                    gedung_kondisi: 'Baik',
                    gedung_status_tanah: 'Tanah Pemkab Bondowoso',
                    gedung_dokumen_no: '',
                    gedung_dokumen_tgl: '',
                    gedung_fungsi: '',
                    gedung_alamat: this.formData.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                    gedung_jumlah_bangunan: 1,
                    gedung_satuan: 'Gedung',
                    gedung_nilai_satuan: 0
                });
                this.syncTotalsFromItems();
            },

            removeGedungItem(idx) {
                if (this.formData.gedung_items && this.formData.gedung_items.length > 1) {
                    this.formData.gedung_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getGedungSubtotal(item) {
                const qty = parseInt(item.gedung_jumlah_bangunan) || 1;
                const unitVal = parseFloat(item.gedung_nilai_satuan) || 0;
                return qty * unitVal;
            },

            get totalNilaiGedung() {
                if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    return this.formData.gedung_items.reduce((sum, item) => sum + this.getGedungSubtotal(item), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            // Repeater Actions: Jaringan
            addJaringanItem() {
                if (!this.formData.jaringan_items) {
                    this.formData.jaringan_items = [];
                }
                this.formData.jaringan_items.push({
                    jaringan_kode_barang: '',
                    jaringan_nama_barang: '',
                    isFilterOpen: false,
                    searchFilter: '',
                    jaringan_luas_m2: '',
                    jaringan_panjang_m: '',
                    jaringan_lebar_m: '',
                    jaringan_bertingkat: 'Tidak',
                    jaringan_konstruksi: 'Aspal Hotmix',
                    jaringan_beton: 'Beton',
                    jaringan_kondisi: 'Baik',
                    jaringan_status_tanah: 'Tanah Pemkab Bondowoso',
                    jaringan_dokumen_no: '',
                    jaringan_dokumen_tgl: '',
                    jaringan_keterangan: '',
                    jaringan_alamat: this.formData.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                    jaringan_jumlah: 1,
                    jaringan_satuan: 'Ruas',
                    jaringan_nilai_satuan: 0
                });
                this.syncTotalsFromItems();
            },

            removeJaringanItem(idx) {
                if (this.formData.jaringan_items && this.formData.jaringan_items.length > 1) {
                    this.formData.jaringan_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getJaringanSubtotal(item) {
                const qty = parseInt(item.jaringan_jumlah) || 1;
                const unitVal = parseFloat(item.jaringan_nilai_satuan) || 0;
                return qty * unitVal;
            },

            get totalNilaiJaringan() {
                if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    return this.formData.jaringan_items.reduce((sum, item) => sum + this.getJaringanSubtotal(item), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            // Repeater Actions: Lainnya (KIB E)
            addLainnyaItem() {
                if (!this.formData.lainnya_items) {
                    this.formData.lainnya_items = [];
                }
                this.formData.lainnya_items.push({
                    is_extracom: Boolean(this.formData.is_extracomtable),
                    kib_e_type: 'buku',
                    lainnya_kode_barang: '',
                    lainnya_nama_barang: '',
                    isFilterOpen: false,
                    searchFilter: '',
                    lainnya_judul: '',
                    lainnya_pencipta: '',
                    lainnya_spesifikasi: '',
                    lainnya_tahun: null,
                    lainnya_ukuran: '',
                    lainnya_asal_daerah: '',
                    lainnya_bahan: '',
                    lainnya_jenis: 'Buku / Kepustakaan Medis',
                    lainnya_kondisi: 'Baik',
                    lainnya_jumlah: 1,
                    lainnya_satuan: 'Eksemplar',
                    lainnya_nilai_satuan: 0,
                    lainnya_no_pabrik: '',
                    lainnya_keterangan: '',
                    ruang_pemegang: this.selectedUnitName || '',
                    isRuangOpen: false,
                    searchRuang: ''
                });
                this.syncTotalsFromItems();
            },

            removeLainnyaItem(idx) {
                if (this.formData.lainnya_items && this.formData.lainnya_items.length > 1) {
                    this.formData.lainnya_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getLainnyaSubtotal(item) {
                const qty = parseInt(item.lainnya_jumlah) || 1;
                const unitVal = parseFloat(item.lainnya_nilai_satuan) || 0;
                return qty * unitVal;
            },

            getKibEPrefix(item) {
                if (!item) return '1.3.5';
                if (item.kib_e_type === 'kesenian') return '1.3.5.02';
                if (item.kib_e_type === 'hewan_tumbuhan') return '1.3.5.03';
                return '1.3.5.01';
            },

            get totalNilaiLainnya() {
                if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                    return this.formData.lainnya_items.reduce((sum, item) => sum + this.getLainnyaSubtotal(item), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            get totalVolumeLainnya() {
                if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                    return this.formData.lainnya_items.reduce((sum, item) => sum + (parseInt(item.lainnya_jumlah) || 1), 0);
                }
                return parseInt(this.formData.jumlah_volume) || 1;
            },

            // Auto-Sync Calculations
            syncTotalsFromItems() {
                if (this.isTanah && this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    let totalQty = 0;
                    let totalVal = 0;
                    const kCounts = {};
                    this.formData.tanah_items.forEach(it => {
                        const q = parseInt(it.tanah_jumlah_barang) || parseInt(it.tanah_jumlah_bidang) || 1;
                        const v = parseFloat(it.tanah_nilai_satuan) || parseFloat(it.tanah_nilai_fisik) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                        const k = it.tanah_kondisi || 'Baik';
                        kCounts[k] = (kCounts[k] || 0) + q;
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.tanah_items[0].tanah_satuan || 'Bidang';
                    this.formData.total_realisasi = totalVal;
                    
                    // Hitung kondisi dominan
                    let dominantK = 'Baik', maxC = -1;
                    for (const [k, c] of Object.entries(kCounts)) {
                        if (c > maxC) { maxC = c; dominantK = k; }
                    }
                    this.formData.kondisi = dominantK;

                    if (this.formData.tanah_items[0].tanah_alamat) {
                        this.formData.alamat_barang = this.formData.tanah_items[0].tanah_alamat;
                    }
                    if (this.formData.tanah_items.length === 1) {
                        if (this.formData.tanah_items[0].tanah_nama_barang) {
                            this.formData.nama_barang = this.formData.tanah_items[0].tanah_nama_barang;
                        }
                    } else {
                        const names = this.formData.tanah_items.map(m => m.tanah_nama_barang).filter(Boolean);
                        if (names.length > 0) {
                            this.formData.nama_barang = names.join(', ');
                        }
                    }
                } else if (this.isMesin && this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    let totalQty = 0;
                    let totalVal = 0;
                    const kCounts = {};
                    this.formData.mesin_items.forEach(it => {
                        const q = parseInt(it.mesin_jumlah_barang) || 1;
                        const v = parseFloat(it.mesin_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                        const k = it.mesin_kondisi || 'Baik';
                        kCounts[k] = (kCounts[k] || 0) + q;
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.mesin_items[0].mesin_satuan || 'Unit';
                    this.formData.total_realisasi = totalVal;
                    
                    // Hitung kondisi dominan
                    let dominantK = 'Baik', maxC = -1;
                    for (const [k, c] of Object.entries(kCounts)) {
                        if (c > maxC) { maxC = c; dominantK = k; }
                    }
                    this.formData.kondisi = dominantK;

                    if (this.formData.mesin_items[0].ruang_pemegang) {
                        const matchedUnit = (this.unitsList || []).find(u => u.nama === this.formData.mesin_items[0].ruang_pemegang);
                        if (matchedUnit) {
                            this.formData.unit_id = matchedUnit.id;
                        }
                    }
                    const first = this.formData.mesin_items[0];
                    if (this.formData.mesin_items.length === 1) {
                        if (first.mesin_nama_barang) {
                            this.formData.nama_barang = first.mesin_nama_barang;
                        }
                    } else {
                        const names = this.formData.mesin_items.map(m => m.mesin_nama_barang || (m.mesin_merk ? m.mesin_merk + ' ' + m.mesin_type : '')).filter(Boolean);
                        if (names.length > 0) {
                            this.formData.nama_barang = names.join(', ');
                        }
                    }
                } else if (this.isGedung && this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    let totalQty = 0;
                    let totalVal = 0;
                    const kCounts = {};
                    this.formData.gedung_items.forEach(it => {
                        const q = parseInt(it.gedung_jumlah_bangunan) || 1;
                        const v = parseFloat(it.gedung_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                        const k = it.gedung_kondisi || 'Baik';
                        kCounts[k] = (kCounts[k] || 0) + q;
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.gedung_items[0].gedung_satuan || 'Gedung';
                    this.formData.total_realisasi = totalVal;
                    
                    // Hitung kondisi dominan
                    let dominantK = 'Baik', maxC = -1;
                    for (const [k, c] of Object.entries(kCounts)) {
                        if (c > maxC) { maxC = c; dominantK = k; }
                    }
                    this.formData.kondisi = dominantK;

                    if (this.formData.gedung_items[0].gedung_alamat) {
                        this.formData.alamat_barang = this.formData.gedung_items[0].gedung_alamat;
                    }
                    if (this.formData.gedung_items.length === 1) {
                        if (this.formData.gedung_items[0].gedung_nama_barang) {
                            this.formData.nama_barang = this.formData.gedung_items[0].gedung_nama_barang;
                        }
                    } else {
                        const names = this.formData.gedung_items.map(m => m.gedung_nama_barang).filter(Boolean);
                        if (names.length > 0) {
                            this.formData.nama_barang = names.join(', ');
                        }
                    }
                } else if (this.isJaringan && this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    let totalQty = 0;
                    let totalVal = 0;
                    const kCounts = {};
                    this.formData.jaringan_items.forEach(it => {
                        const q = parseInt(it.jaringan_jumlah) || 1;
                        const v = parseFloat(it.jaringan_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                        const k = it.jaringan_kondisi || 'Baik';
                        kCounts[k] = (kCounts[k] || 0) + q;
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.jaringan_items[0].jaringan_satuan || 'Ruas';
                    this.formData.total_realisasi = totalVal;
                    
                    // Hitung kondisi dominan
                    let dominantK = 'Baik', maxC = -1;
                    for (const [k, c] of Object.entries(kCounts)) {
                        if (c > maxC) { maxC = c; dominantK = k; }
                    }
                    this.formData.kondisi = dominantK;

                    if (this.formData.jaringan_items[0].jaringan_alamat) {
                        this.formData.alamat_barang = this.formData.jaringan_items[0].jaringan_alamat;
                    }
                    if (this.formData.jaringan_items.length === 1) {
                        if (this.formData.jaringan_items[0].jaringan_nama_barang) {
                            this.formData.nama_barang = this.formData.jaringan_items[0].jaringan_nama_barang;
                        }
                    } else {
                        const names = this.formData.jaringan_items.map(m => m.jaringan_nama_barang).filter(Boolean);
                        if (names.length > 0) {
                            this.formData.nama_barang = names.join(', ');
                        }
                    }
                } else if (this.isLainnya && this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                    let totalQty = 0;
                    let totalVal = 0;
                    const kCounts = {};
                    this.formData.lainnya_items.forEach(it => {
                        const q = parseInt(it.lainnya_jumlah) || 1;
                        const v = parseFloat(it.lainnya_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                        const k = it.lainnya_kondisi || 'Baik';
                        kCounts[k] = (kCounts[k] || 0) + q;
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.lainnya_items[0].lainnya_satuan || 'Buah';
                    this.formData.total_realisasi = totalVal;
                    
                    // Hitung kondisi dominan
                    let dominantK = 'Baik', maxC = -1;
                    for (const [k, c] of Object.entries(kCounts)) {
                        if (c > maxC) { maxC = c; dominantK = k; }
                    }
                    this.formData.kondisi = dominantK;

                    if (this.formData.lainnya_items[0].ruang_pemegang) {
                        const matchedUnit = (this.unitsList || []).find(u => u.nama === this.formData.lainnya_items[0].ruang_pemegang);
                        if (matchedUnit) {
                            this.formData.unit_id = matchedUnit.id;
                        }
                    }
                    const first = this.formData.lainnya_items[0];
                    if (this.formData.lainnya_items.length === 1) {
                        if (first.lainnya_nama_barang) {
                            this.formData.nama_barang = first.lainnya_nama_barang;
                        } else if (first.lainnya_judul) {
                            this.formData.nama_barang = first.lainnya_judul;
                        }
                    } else {
                        const names = this.formData.lainnya_items.map(m => m.lainnya_nama_barang || m.lainnya_judul).filter(Boolean);
                        if (names.length > 0) {
                            this.formData.nama_barang = names.join(', ');
                        }
                    }
                }

                // Otomatis pastikan nama_barang terisi dari Kode 108 jika belum ada
                if (!this.formData.nama_barang || this.formData.nama_barang.trim() === '') {
                    if (this.selected108Item && this.selected108Item.nama) {
                        this.formData.nama_barang = this.selected108Item.nama;
                    }
                }
            },

            // Helper Live Search 108 untuk Tiap Item Sheet KIB (Max 5 hasil, Zero-Lag)
            filterJenisAstap108(prefix, query, isOpen) {
                if (!isOpen) return [];
                const q = (query || '').toLowerCase().trim();
                const results = [];
                const list = this.allFlat108 || [];
                for (let i = 0; i < list.length; i++) {
                    const it = list[i];
                    if (!it || !it.kode || !it.kode.startsWith(prefix)) continue;
                    if (!q || (it.nama && it.nama.toLowerCase().includes(q)) || (it.kode && it.kode.includes(q))) {
                        results.push(it);
                        if (results.length >= 5) break; // Strict 5-item cutoff agar tidak lag!
                    }
                }
                // Fallback jika tidak ada hasil spesifik di sub-prefix (misal 1.3.5.01), cari di parent prefix 1.3.5
                if (results.length === 0 && prefix && prefix.length > 5) {
                    const parentPrefix = prefix.substring(0, 5); // '1.3.5'
                    for (let i = 0; i < list.length; i++) {
                        const it = list[i];
                        if (!it || !it.kode || !it.kode.startsWith(parentPrefix)) continue;
                        if (!q || (it.nama && it.nama.toLowerCase().includes(q)) || (it.kode && it.kode.includes(q))) {
                            results.push(it);
                            if (results.length >= 5) break;
                        }
                    }
                }
                return results;
            },

            // Pilih barang 108 dari filter dropdown
            select108ForItem(item, opt, type) {
                if (type === 'mesin') {
                    item.mesin_kode_barang = opt.kode;
                    item.mesin_nama_barang = opt.nama;
                    item.searchFilter = opt.nama;
                } else if (type === 'tanah') {
                    item.tanah_kode_barang = opt.kode;
                    item.tanah_nama_barang = opt.nama;
                    item.searchFilter = opt.nama;
                } else if (type === 'gedung') {
                    item.gedung_kode_barang = opt.kode;
                    item.gedung_nama_barang = opt.nama;
                    item.searchFilter = opt.nama;
                } else if (type === 'jaringan') {
                    item.jaringan_kode_barang = opt.kode;
                    item.jaringan_nama_barang = opt.nama;
                    item.searchFilter = opt.nama;
                } else if (type === 'lainnya') {
                    item.lainnya_kode_barang = opt.kode;
                    item.lainnya_nama_barang = opt.nama;
                    item.searchFilter = opt.nama;
                    if (!item.lainnya_judul || item.lainnya_judul.trim() === '') {
                        item.lainnya_judul = opt.nama;
                    }
                }
                item.isFilterOpen = false;
                if (opt.id) {
                    this.formData.jenis_astap_id = opt.id;
                }
                this.syncTotalsFromItems();
            },

            // Helper Pencarian & Pemilihan Unit/Ruangan Penempatan
            filterUnitsForItem(item) {
                let list = this.unitsList || [];
                if (!item.searchRuang || item.searchRuang.trim() === '') return list;
                const q = item.searchRuang.toLowerCase().trim();
                return list.filter(u => (u.nama || '').toLowerCase().includes(q) || (u.kepala || '').toLowerCase().includes(q) || (u.tipe || '').toLowerCase().includes(q));
            },

            selectUnitForItem(item, unit) {
                item.ruang_pemegang = unit.nama;
                item.isRuangOpen = false;
                item.searchRuang = '';
                if (unit.id) {
                    this.formData.unit_id = unit.id;
                }
                this.syncTotalsFromItems();
            },

            // Getters for Step 3 Summary
            get totalLuasTanah() {
                if (!this.formData.tanah_items) return 0;
                return this.formData.tanah_items.reduce((s, it) => s + (parseFloat(it.tanah_luas_m2) || 0), 0);
            },

            get totalLuasGedung() {
                if (!this.formData.gedung_items) return 0;
                return this.formData.gedung_items.reduce((s, it) => s + (parseFloat(it.gedung_luas_m2) || 0), 0);
            },

            get totalLuasJaringan() {
                if (!this.formData.jaringan_items) return 0;
                return this.formData.jaringan_items.reduce((s, it) => s + (parseFloat(it.jaringan_luas_m2) || 0), 0);
            },

            get firstMesinItem() {
                return (this.formData.mesin_items && this.formData.mesin_items[0]) ? this.formData.mesin_items[0] : null;
            },

            get firstGedungItem() {
                return (this.formData.gedung_items && this.formData.gedung_items[0]) ? this.formData.gedung_items[0] : null;
            },

            get firstJaringanItem() {
                return (this.formData.jaringan_items && this.formData.jaringan_items[0]) ? this.formData.jaringan_items[0] : null;
            },

            get firstLainnyaItem() {
                return (this.formData.lainnya_items && this.formData.lainnya_items[0]) ? this.formData.lainnya_items[0] : null;
            },

            // Rincian unit individual hasil flattening dari repeater KIB aktif
            get simulatedUnits() {
                const list = [];
                if (this.isTanah && this.formData.tanah_items) {
                    this.formData.tanah_items.forEach((it, itIdx) => {
                        const qty = parseInt(it.tanah_jumlah_barang) || parseInt(it.tanah_jumlah_bidang) || 1;
                        const kondisi = it.tanah_kondisi || 'Baik';
                        const ruang = it.tanah_alamat || this.formData.alamat_barang || this.selectedUnitName || 'RSUD Dr. H. Koesnandi';
                        const nama = it.tanah_nama_barang || this.formData.nama_barang || `Bidang Tanah #${itIdx + 1}`;
                        for (let q = 0; q < qty; q++) {
                            list.push({
                                unitNumber: list.length + 1,
                                nama: nama,
                                kondisi: kondisi,
                                ruang: ruang,
                                itemIdx: itIdx + 1
                            });
                        }
                    });
                } else if (this.isMesin && this.formData.mesin_items) {
                    this.formData.mesin_items.forEach((it, itIdx) => {
                        const qty = parseInt(it.mesin_jumlah_barang) || 1;
                        const kondisi = it.mesin_kondisi || 'Baik';
                        const ruang = it.ruang_pemegang || this.selectedUnitName || 'RSUD Dr. H. Koesnandi';
                        const nama = it.mesin_nama_barang || (it.mesin_merk ? `${it.mesin_merk} ${it.mesin_type || ''}`.trim() : '') || this.formData.nama_barang || `Barang Mesin #${itIdx + 1}`;
                        for (let q = 0; q < qty; q++) {
                            list.push({
                                unitNumber: list.length + 1,
                                nama: nama,
                                kondisi: kondisi,
                                ruang: ruang,
                                itemIdx: itIdx + 1
                            });
                        }
                    });
                } else if (this.isGedung && this.formData.gedung_items) {
                    this.formData.gedung_items.forEach((it, itIdx) => {
                        const qty = parseInt(it.gedung_jumlah_bangunan) || 1;
                        const kondisi = it.gedung_kondisi || 'Baik';
                        const ruang = it.gedung_alamat || this.formData.alamat_barang || this.selectedUnitName || 'RSUD Dr. H. Koesnandi';
                        const nama = it.gedung_nama_barang || this.formData.nama_barang || `Bangunan Gedung #${itIdx + 1}`;
                        for (let q = 0; q < qty; q++) {
                            list.push({
                                unitNumber: list.length + 1,
                                nama: nama,
                                kondisi: kondisi,
                                ruang: ruang,
                                itemIdx: itIdx + 1
                            });
                        }
                    });
                } else if (this.isJaringan && this.formData.jaringan_items) {
                    this.formData.jaringan_items.forEach((it, itIdx) => {
                        const qty = parseInt(it.jaringan_jumlah) || 1;
                        const kondisi = it.jaringan_kondisi || 'Baik';
                        const ruang = it.jaringan_alamat || this.formData.alamat_barang || this.selectedUnitName || 'RSUD Dr. H. Koesnandi';
                        const nama = it.jaringan_nama_barang || this.formData.nama_barang || `Ruas Jaringan #${itIdx + 1}`;
                        for (let q = 0; q < qty; q++) {
                            list.push({
                                unitNumber: list.length + 1,
                                nama: nama,
                                kondisi: kondisi,
                                ruang: ruang,
                                itemIdx: itIdx + 1
                            });
                        }
                    });
                } else if (this.isLainnya && this.formData.lainnya_items) {
                    this.formData.lainnya_items.forEach((it, itIdx) => {
                        const qty = parseInt(it.lainnya_jumlah) || parseInt(it.lainnya_jumlah_barang) || 1;
                        const kondisi = it.lainnya_kondisi || 'Baik';
                        const ruang = it.ruang_pemegang || this.selectedUnitName || 'RSUD Dr. H. Koesnandi';
                        const nama = it.lainnya_nama_barang || it.lainnya_judul || this.formData.nama_barang || `Item Lainnya #${itIdx + 1}`;
                        for (let q = 0; q < qty; q++) {
                            list.push({
                                unitNumber: list.length + 1,
                                nama: nama,
                                kondisi: kondisi,
                                ruang: ruang,
                                itemIdx: itIdx + 1
                            });
                        }
                    });
                }

                if (list.length === 0) {
                    const total = Math.max(1, parseInt(this.formData.jumlah_volume) || 1);
                    for (let i = 0; i < total; i++) {
                        list.push({
                            unitNumber: i + 1,
                            nama: this.formData.nama_barang || `Aset BMD #${i + 1}`,
                            kondisi: this.formData.kondisi || 'Baik',
                            ruang: this.selectedUnitName || 'RSUD Dr. H. Koesnandi',
                            itemIdx: 1
                        });
                    }
                }
                return list;
            },

            // Ringkasan kondisi untuk banner
            get kondisiSummary() {
                const units = this.simulatedUnits || [];
                if (units.length === 0) return this.formData.kondisi || 'Baik';
                const counts = { 'Baik': 0, 'Kurang Baik': 0, 'Rusak Berat': 0 };
                units.forEach(u => {
                    const k = u.kondisi || 'Baik';
                    if (counts[k] !== undefined) counts[k]++;
                    else counts['Baik']++;
                });
                const total = units.length;
                if (counts['Baik'] === total) return 'Baik (Semua)';
                if (counts['Kurang Baik'] === total) return 'Kurang Baik (Semua)';
                if (counts['Rusak Berat'] === total) return 'Rusak Berat (Semua)';

                const parts = [];
                if (counts['Baik'] > 0) parts.push(`${counts['Baik']} Baik`);
                if (counts['Kurang Baik'] > 0) parts.push(`${counts['Kurang Baik']} KB`);
                if (counts['Rusak Berat'] > 0) parts.push(`${counts['Rusak Berat']} RB`);
                return parts.join(' • ');
            },

            // File selection
            handleFileSelect(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.size > 10 * 1024 * 1024) {
                        this.showToast('File Terlalu Besar', 'Ukuran dokumen maksimal adalah 10 MB.', 'error');
                        event.target.value = '';
                        return;
                    }
                    this.selectedFile = file;
                    this.showToast('Berkas Terpilih', `Berkas "${file.name}" siap diunggah.`, 'info');
                }
            },

            // NIBAR Simulator (45 Digit)
            generateSimulatedNibar(index) {
                const thn = this.formData.tahun_perolehan || new Date().getFullYear();
                let kode108Clean = '132000000000';
                if (this.selected108Item && this.selected108Item.kode) {
                    kode108Clean = this.selected108Item.kode.replace(/\./g, '').padEnd(12, '0').slice(0, 12);
                } else if (this.isTanah) {
                    kode108Clean = '131010100100';
                } else if (this.isGedung) {
                    kode108Clean = '133010100100';
                } else if (this.isJaringan) {
                    kode108Clean = '134010100100';
                } else if (this.isLainnya) {
                    kode108Clean = '135010100100';
                }
                const noReg = String(index).padStart(7, '0');
                return `1201351102000000280000${thn}${kode108Clean}${noReg}`;
            },

            // Step Navigation & Validation
            goToStep(s) {
                if (s < this.currentStep) {
                    this.currentStep = s;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
                if (s > this.currentStep) {
                    for (let stepCheck = this.currentStep; stepCheck < s; stepCheck++) {
                        if (!this.validateStep(stepCheck)) return;
                    }
                }
                this.currentStep = s;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            nextStep() {
                if (this.validateStep(this.currentStep)) {
                    this.currentStep++;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.currentStep--;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            },

            // Helper validasi batasan Ekstrakomtabel (Extracom <= Rp 300.000)
            getExtracomViolations() {
                const violations = [];
                if (!this.formData.is_extracomtable) return violations;

                if (this.isMesin && Array.isArray(this.formData.mesin_items)) {
                    this.formData.mesin_items.forEach((item, idx) => {
                        const val = parseFloat(item.mesin_nilai_satuan) || 0;
                        if (val > 300000) {
                            violations.push({
                                index: idx,
                                type: 'mesin',
                                itemNumber: idx + 1,
                                nama: (item.mesin_nama_barang || this.formData.nama_barang || `Barang Mesin #${idx + 1}`),
                                nilai: val,
                                nilaiFormatted: 'Rp ' + Number(val).toLocaleString('id-ID')
                            });
                        }
                    });
                } else if (this.isLainnya && Array.isArray(this.formData.lainnya_items)) {
                    this.formData.lainnya_items.forEach((item, idx) => {
                        const val = parseFloat(item.lainnya_nilai_satuan) || 0;
                        if (val > 300000) {
                            violations.push({
                                index: idx,
                                type: 'lainnya',
                                itemNumber: idx + 1,
                                nama: (item.lainnya_nama_barang || item.lainnya_judul || this.formData.nama_barang || `Item Lainnya #${idx + 1}`),
                                nilai: val,
                                nilaiFormatted: 'Rp ' + Number(val).toLocaleString('id-ID')
                            });
                        }
                    });
                }
                return violations;
            },

            get hasExtracomViolation() {
                return this.getExtracomViolations().length > 0;
            },

            showExtracomAlert(violations) {
                const listHtml = violations.map(v => `
                    <li class="flex items-start gap-2.5 text-rose-300 py-1.5 border-b border-slate-800/80 last:border-0">
                        <span class="text-rose-400 font-bold shrink-0 mt-0.5">⚠️</span> 
                        <div class="text-left leading-snug">
                            <span class="font-extrabold text-white block text-xs">${v.nama}</span>
                            <span class="text-[11px] text-slate-300">
                                Nilai Satuan Input: <strong class="font-mono text-rose-400 font-bold">${v.nilaiFormatted}</strong> 
                                <span class="text-rose-300/80">(Batas Maksimal Ekstrakomtabel: <strong class="font-mono text-amber-300">Rp 300.000</strong>)</span>
                            </span>
                        </div>
                    </li>
                `).join('');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Nilai Ekstrakomtabel Melebihi Rp 300.000!',
                        html: `
                            <div class="text-left text-xs space-y-3 text-slate-300">
                                <p class="text-slate-200 leading-relaxed">
                                    Anda <strong>tidak dapat melanjutkan ke Tahap 3</strong> karena item berikut dipilih berstatus <span class="px-2 py-0.5 rounded text-[10.5px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">Ekstrakomtabel (Extracom)</span> tetapi nilai satuannya melebihi batas regulasi <strong>Rp 300.000</strong>:
                                </p>
                                <ul class="p-3.5 rounded-2xl bg-slate-950/90 border border-rose-500/40 space-y-1.5 shadow-inner">
                                    ${listHtml}
                                </ul>
                                <div class="p-3 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-200 text-[11px] leading-relaxed space-y-1">
                                    <div class="font-bold text-indigo-300 flex items-center gap-1.5">
                                        <span>💡</span> Petunjuk Solusi:
                                    </div>
                                    <ul class="list-disc pl-4 space-y-1 text-slate-300 text-[10.5px]">
                                        <li>Jika nilai perolehan per unit memang <strong>&gt; Rp 300.000</strong>: Ubah status akuntansi item menjadi <strong>⚙️ Aset Tetap Reguler (Intrakomptabel)</strong>.</li>
                                        <li>Jika barang benar-benar <strong>Ekstrakomtabel</strong>: Perbaiki nilai satuan agar <strong>&le; Rp 300.000</strong>.</li>
                                    </ul>
                                </div>
                            </div>
                        `,
                        confirmButtonText: 'Perbaiki Nilai Satuan &rarr;',
                        confirmButtonColor: '#f43f5e',
                        background: '#0f172a',
                        color: '#ffffff',
                        customClass: {
                            popup: 'rounded-3xl border border-rose-500/30 shadow-2xl'
                        }
                    }).then(() => {
                        this.focusFirstExtracomViolation();
                    });
                } else {
                    const msg = violations.map(v => `${v.nama}: ${v.nilaiFormatted} (Maks. Rp 300.000)`).join(', ');
                    this.showToast('Batas Nilai Extracom Terlampaui', msg, 'error');
                    this.focusFirstExtracomViolation();
                }
            },

            focusFirstExtracomViolation() {
                setTimeout(() => {
                    const el = document.querySelector('.border-rose-500[placeholder*="300.000"]') ||
                               document.querySelector('input.border-rose-500') ||
                               document.querySelector('input[placeholder*="Maks. 300.000"]');
                    if (el) {
                        el.focus();
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 200);
            },

            validateStep(s) {
                // Bersihkan error lama untuk step ini sebelum validasi ulang
                this.clearStepError(s);

                if (s === 1) {
                    if (!this.formData.mutasi_asal || !this.formData.mutasi_asal.trim()) {
                        const msg = 'Mohon isi nama instansi / SKPD asal pengirim BMD.';
                        this.showToast('Validasi Langkah 1 Gagal', msg, 'error');
                        this.setStepError(1, msg);
                        this.focusFirstMissingField(1);
                        return false;
                    }
                    if (!this.formData.mutasi_nomor_bamb || !this.formData.mutasi_nomor_bamb.trim()) {
                        const msg = 'Mohon isi nomor dokumen Berita Acara (BAMB / BAST).';
                        this.showToast('Validasi Langkah 1 Gagal', msg, 'error');
                        this.setStepError(1, msg);
                        this.focusFirstMissingField(1);
                        return false;
                    }
                    if (!this.formData.mutasi_tanggal) {
                        const msg = 'Mohon isi tanggal dokumen Berita Acara (BAMB).';
                        this.showToast('Validasi Langkah 1 Gagal', msg, 'error');
                        this.setStepError(1, msg);
                        this.focusFirstMissingField(1);
                        return false;
                    }
                    const tBamb = this.parseDateToTimestamp(this.formData.mutasi_tanggal);
                    const today = this.getTodayTimestamp();
                    if (tBamb > 0 && tBamb > today) {
                        const msg = 'Tanggal dokumen Berita Acara tidak boleh melebihi tanggal hari ini.';
                        this.showToast('Validasi Langkah 1 Gagal', msg, 'error');
                        this.setStepError(1, msg);
                        this.focusFirstMissingField(1);
                        return false;
                    }
                    if (!this.formData.tahun_perolehan) {
                        const msg = 'Mohon tentukan tahun pembukuan BMD.';
                        this.showToast('Validasi Langkah 1 Gagal', msg, 'error');
                        this.setStepError(1, msg);
                        this.focusFirstMissingField(1);
                        return false;
                    }
                    if (!this.formData.triwulan) {
                        const msg = 'Mohon pilih periode triwulan pembukuan.';
                        this.showToast('Validasi Langkah 1 Gagal', msg, 'error');
                        this.setStepError(1, msg);
                        this.focusFirstMissingField(1);
                        return false;
                    }
                } else if (s === 2) {
                    if (!this.formData.jenis_astap_id) {
                        this.ensureJenisAstapId();
                    }
                    if (!this.formData.jenis_astap_id) {
                        const msg = 'Mohon pilih klasifikasi kode barang 108 — pilih salah satu kategori KIB aset tetap.';
                        this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                        this.setStepError(2, msg);
                        return false;
                    }

                    // Validasi rincian repeater spesifik KIB
                    const today = this.getTodayTimestamp();
                    if (this.isMesin) {
                        this.syncTotalsFromItems();
                        if (!this.formData.mesin_items || this.formData.mesin_items.length === 0) {
                            const msg = 'Mohon tambahkan minimal 1 item barang / unit pada rincian mesin.';
                            this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                            this.setStepError(2, msg);
                            return false;
                        }
                        for (let i = 0; i < this.formData.mesin_items.length; i++) {
                            const it = this.formData.mesin_items[i];
                            const num = i + 1;
                            if (!it.mesin_nama_barang || !it.mesin_nama_barang.trim()) {
                                const msg = `Nama Barang / Unit #${num} tidak boleh kosong.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (!it.mesin_jumlah_barang || parseInt(it.mesin_jumlah_barang) < 1) {
                                const msg = `Jumlah volume pada Barang #${num} minimal 1 unit.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (parseFloat(it.mesin_nilai_satuan) <= 0 || isNaN(parseFloat(it.mesin_nilai_satuan))) {
                                const msg = `Nilai satuan pada Barang #${num} harus lebih dari 0.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (it.is_extracom && parseFloat(it.mesin_nilai_satuan) > 300000) {
                                const msg = `Nilai satuan pada Barang Extracom #${num} tidak boleh melebihi Rp 300.000.`;
                                this.showToast('Validasi Extracom Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                        }
                    } else if (this.isTanah) {
                        this.syncTotalsFromItems();
                        if (!this.formData.tanah_items || this.formData.tanah_items.length === 0) {
                            const msg = 'Mohon tambahkan minimal 1 bidang tanah pada rincian tanah.';
                            this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                            this.setStepError(2, msg);
                            return false;
                        }
                        for (let i = 0; i < this.formData.tanah_items.length; i++) {
                            const it = this.formData.tanah_items[i];
                            const num = i + 1;
                            if (!it.tanah_nama_barang || !it.tanah_nama_barang.trim()) {
                                const msg = `Nama/Jenis Tanah pada Bidang #${num} tidak boleh kosong.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (!it.tanah_luas_m2 || parseFloat(it.tanah_luas_m2) <= 0) {
                                const msg = `Luas tanah (m²) pada Bidang Tanah #${num} harus lebih dari 0.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (it.tanah_sertifikat_tgl && this.parseDateToTimestamp(it.tanah_sertifikat_tgl) > today) {
                                const msg = `Tanggal Sertifikat pada Bidang Tanah #${num} tidak boleh melebihi tanggal hari ini.`;
                                this.showToast('Validasi Tanggal Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (!it.tanah_jumlah_barang || parseInt(it.tanah_jumlah_barang) < 1) {
                                const msg = `Jumlah volume pada Bidang Tanah #${num} minimal 1.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (parseFloat(it.tanah_nilai_satuan) <= 0 || isNaN(parseFloat(it.tanah_nilai_satuan))) {
                                const msg = `Nilai perolehan satuan pada Bidang Tanah #${num} harus lebih dari 0.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                        }
                    } else if (this.isGedung) {
                        this.syncTotalsFromItems();
                        if (!this.formData.gedung_items || this.formData.gedung_items.length === 0) {
                            const msg = 'Mohon tambahkan minimal 1 bangunan gedung pada rincian gedung.';
                            this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                            this.setStepError(2, msg);
                            return false;
                        }
                        for (let i = 0; i < this.formData.gedung_items.length; i++) {
                            const it = this.formData.gedung_items[i];
                            const num = i + 1;
                            if (!it.gedung_nama_barang || !it.gedung_nama_barang.trim()) {
                                const msg = `Nama Bangunan #${num} tidak boleh kosong.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (!it.gedung_jumlah_bangunan || parseInt(it.gedung_jumlah_bangunan) < 1) {
                                const msg = `Jumlah unit pada Bangunan #${num} minimal 1.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (parseFloat(it.gedung_nilai_satuan) <= 0 || isNaN(parseFloat(it.gedung_nilai_satuan))) {
                                const msg = `Nilai satuan pada Bangunan #${num} harus lebih dari 0.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (it.gedung_dokumen_tgl && this.parseDateToTimestamp(it.gedung_dokumen_tgl) > today) {
                                const msg = `Tanggal Dokumen Izin pada Bangunan #${num} tidak boleh melebihi tanggal hari ini.`;
                                this.showToast('Validasi Tanggal Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                        }
                    } else if (this.isJaringan) {
                        this.syncTotalsFromItems();
                        if (!this.formData.jaringan_items || this.formData.jaringan_items.length === 0) {
                            const msg = 'Mohon tambahkan minimal 1 ruas pada rincian jalan & jaringan.';
                            this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                            this.setStepError(2, msg);
                            return false;
                        }
                        for (let i = 0; i < this.formData.jaringan_items.length; i++) {
                            const it = this.formData.jaringan_items[i];
                            const num = i + 1;
                            if (!it.jaringan_nama_barang || !it.jaringan_nama_barang.trim()) {
                                const msg = `Nama Ruas #${num} tidak boleh kosong.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (!it.jaringan_jumlah || parseInt(it.jaringan_jumlah) < 1) {
                                const msg = `Jumlah volume pada Ruas #${num} minimal 1.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (parseFloat(it.jaringan_nilai_satuan) <= 0 || isNaN(parseFloat(it.jaringan_nilai_satuan))) {
                                const msg = `Nilai satuan pada Ruas #${num} harus lebih dari 0.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (it.jaringan_dokumen_tgl && this.parseDateToTimestamp(it.jaringan_dokumen_tgl) > today) {
                                const msg = `Tanggal Dokumen Kontrak pada Ruas #${num} tidak boleh melebihi tanggal hari ini.`;
                                this.showToast('Validasi Tanggal Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                        }
                    } else if (this.isLainnya) {
                        this.syncTotalsFromItems();
                        if (!this.formData.lainnya_items || this.formData.lainnya_items.length === 0) {
                            const msg = 'Mohon tambahkan minimal 1 item pada rincian aset tetap lainnya.';
                            this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                            this.setStepError(2, msg);
                            return false;
                        }
                        for (let i = 0; i < this.formData.lainnya_items.length; i++) {
                            const it = this.formData.lainnya_items[i];
                            const num = i + 1;
                            const nama = it.lainnya_nama_barang || it.lainnya_judul;
                            if (!nama || !nama.trim()) {
                                const msg = `Nama Barang/Judul pada Item #${num} tidak boleh kosong.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (!it.lainnya_jumlah || parseInt(it.lainnya_jumlah) < 1) {
                                const msg = `Jumlah volume pada Item #${num} minimal 1.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (parseFloat(it.lainnya_nilai_satuan) <= 0 || isNaN(parseFloat(it.lainnya_nilai_satuan))) {
                                const msg = `Nilai satuan pada Item #${num} harus lebih dari 0.`;
                                this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                            if (it.is_extracom && parseFloat(it.lainnya_nilai_satuan) > 300000) {
                                const msg = `Nilai satuan pada Barang Extracom #${num} tidak boleh melebihi Rp 300.000.`;
                                this.showToast('Validasi Extracom Gagal', msg, 'error');
                                this.setStepError(2, msg);
                                return false;
                            }
                        }
                    }

                    if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
                        const msg = 'Mohon isi nama spesifik barang pelimpahan BMD.';
                        this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                        this.setStepError(2, msg);
                        return false;
                    }
                    if (!this.formData.jumlah_volume || this.formData.jumlah_volume < 1) {
                        const msg = 'Jumlah volume barang minimal 1 unit.';
                        this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                        this.setStepError(2, msg);
                        return false;
                    }
                    if (!this.formData.satuan || !this.formData.satuan.trim()) {
                        const msg = 'Mohon isi satuan barang (contoh: Unit, Set, Buah).';
                        this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                        this.setStepError(2, msg);
                        return false;
                    }
                    if (!this.formData.total_realisasi || Number(this.formData.total_realisasi) <= 0) {
                        const msg = 'Mohon masukkan total nilai perolehan BMD (Rp).';
                        this.showToast('Validasi Langkah 2 Gagal', msg, 'error');
                        this.setStepError(2, msg);
                        return false;
                    }

                    // VALIDASI EXTRACOM: Nilai satuan tidak boleh melebihi Rp 300.000
                    const extracomViolations = this.getExtracomViolations();
                    if (extracomViolations.length > 0) {
                        this.showExtracomAlert(extracomViolations);
                        const msg = 'Nilai satuan barang Extracom tidak boleh melebihi Rp 300.000.';
                        this.showToast('Validasi Extracom Gagal', msg, 'error');
                        this.setStepError(2, msg);
                        return false;
                    }
                } else if (s === 3) {
                    if (!this.isDataVerified) {
                        const msg = 'Mohon centang pernyataan bahwa data serah terima BMD telah diverifikasi dengan benar sebelum disimpan.';
                        this.showToast('Verifikasi Diperlukan', msg, 'warning');
                        this.setStepError(3, msg);
                        return false;
                    }
                }
                return true;
            },

            focusFirstMissingField(stepNum) {
                setTimeout(() => {
                    if (stepNum === 1) {
                        if (!this.formData.mutasi_asal || !this.formData.mutasi_asal.trim()) {
                            const el = document.querySelector('input[x-model="formData.mutasi_asal"]');
                            if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                        } else if (!this.formData.mutasi_nomor_bamb || !this.formData.mutasi_nomor_bamb.trim()) {
                            const el = document.querySelector('input[x-model="formData.mutasi_nomor_bamb"]');
                            if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                        }
                    } else if (stepNum === 2) {
                        if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
                            const el = document.querySelector('input[x-model="formData.nama_barang"]');
                            if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                        } else if (!this.formData.unit_id) {
                            const el = document.querySelector('select[x-model="formData.unit_id"]');
                            if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                        } else if (!this.formData.total_realisasi || Number(this.formData.total_realisasi) <= 0) {
                            const el = document.querySelector('input[x-model.number="formData.total_realisasi"]') || document.querySelector('input[placeholder="0"]');
                            if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                        }
                    }
                }, 150);
            },

            // Form Submit via AJAX
            async submitForm() {
                // Bersihkan semua error lama terlebih dahulu
                this.clearAllStepErrors();

                // Validasi per step dan otomatis navigasi ke step bermasalah
                if (!this.validateStep(1)) {
                    this.currentStep = 1;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
                if (!this.validateStep(2)) {
                    this.currentStep = 2;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
                if (!this.validateStep(3)) {
                    this.currentStep = 3;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }

                this.syncTotalsFromItems();

                // Siapkan payload spesifikasi JSON
                let specJson = {
                    sumber_dana: 'pelimpahan_skpd',
                    skpd_asal: this.formData.mutasi_asal,
                    nomor_bamb: this.formData.mutasi_nomor_bamb,
                    tanggal_bamb: this.formData.mutasi_tanggal,
                    kondisi: this.formData.kondisi,
                    keterangan: this.formData.mutasi_keterangan,
                    nomor_sk_dasar: this.formData.nomor_sk_dasar || '',
                    alamat_instansi: this.formData.alamat_instansi || '',
                    pj_asal_nama: this.formData.pj_asal_nama,
                    pj_asal_nip: this.formData.pj_asal_nip,
                    pj_asal_jabatan: this.formData.pj_asal_jabatan,
                    ppk_nama: this.formData.ppk_nama,
                    ppk_nip: this.formData.ppk_nip
                };

                if (this.isTanah) {
                    specJson.kategori_kib = 'KIB A (Tanah)';
                    specJson.tanah_items = this.formData.tanah_items;
                    if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                        const firstT = this.formData.tanah_items[0];
                        specJson.luas_m2 = this.totalLuasTanah;
                        specJson.hak_tanah = firstT.tanah_hak || 'Hak Pakai';
                        specJson.sertifikat_no = firstT.tanah_sertifikat_no || '';
                        specJson.sertifikat_tgl = firstT.tanah_sertifikat_tgl || '';
                        specJson.penggunaan = firstT.tanah_penggunaan || '';
                    }
                } else if (this.isMesin) {
                    specJson.kategori_kib = 'KIB B (Peralatan & Mesin)';
                    specJson.mesin_items = this.formData.mesin_items;
                    if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                        const firstM = this.formData.mesin_items[0];
                        specJson.is_extracom = firstM.is_extracom || false;
                        specJson.merk = firstM.mesin_merk || '';
                        specJson.type = firstM.mesin_type || '';
                        specJson.no_pabrik = firstM.mesin_no_pabrik || '';
                        specJson.ukuran = firstM.mesin_ukuran || '';
                        specJson.bahan = firstM.mesin_bahan || '';
                        specJson.tahun_pembuatan = firstM.mesin_tahun_pembuatan || null;
                        specJson.no_rangka = firstM.mesin_no_rangka || '';
                        specJson.no_mesin = firstM.mesin_no_mesin || '';
                        specJson.no_bpkb = firstM.mesin_no_bpkb || '';
                        specJson.no_polisi = firstM.mesin_no_polisi || '';
                        specJson.keterangan = firstM.mesin_keterangan || '';
                        specJson.ruang_pemegang = firstM.ruang_pemegang || '';
                    }
                } else if (this.isGedung) {
                    specJson.kategori_kib = 'KIB C (Gedung & Bangunan)';
                    specJson.gedung_items = this.formData.gedung_items;
                    if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                        const firstG = this.formData.gedung_items[0];
                        specJson.gedung_luas_m2 = this.totalLuasGedung;
                        specJson.gedung_bertingkat = firstG.gedung_bertingkat || 'Tidak';
                        specJson.gedung_beton = firstG.gedung_beton || 'Beton';
                        specJson.gedung_status_tanah = firstG.gedung_status_tanah || 'Tanah Pemda';
                        specJson.gedung_alamat = firstG.gedung_alamat || this.formData.alamat_barang || '';
                    }
                } else if (this.isJaringan) {
                    specJson.kategori_kib = 'KIB D (Jalan, Irigasi & Jaringan)';
                    specJson.jaringan_items = this.formData.jaringan_items;
                    if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                        const firstJ = this.formData.jaringan_items[0];
                        specJson.jaringan_luas_m2 = this.totalLuasJaringan;
                        specJson.jaringan_bertingkat = firstJ.jaringan_bertingkat || 'Tidak';
                        specJson.jaringan_beton = firstJ.jaringan_beton || 'Beton';
                        specJson.jaringan_status_tanah = firstJ.jaringan_status_tanah || 'Tanah Pemda';
                        specJson.jaringan_dokumen_no = firstJ.jaringan_dokumen_no || '';
                        specJson.jaringan_alamat = firstJ.jaringan_alamat || this.formData.alamat_barang || '';
                    }
                } else if (this.isLainnya) {
                    specJson.kategori_kib = 'KIB E (Aset Tetap Lainnya)';
                    specJson.lainnya_items = this.formData.lainnya_items;
                }

                this.isSubmitting = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const url = this.isEdit 
                    ? `/astap/update-mutasi-eksternal/${this.astapId}` 
                    : "{{ route('astap.store_mutasi_eksternal') }}";

                const postData = new FormData();
                postData.append('nama_barang', this.formData.nama_barang);
                postData.append('jenis_astap_id', this.formData.jenis_astap_id);
                postData.append('tahun_perolehan', this.formData.tahun_perolehan);
                postData.append('jumlah_volume', this.formData.jumlah_volume);
                postData.append('satuan', this.formData.satuan);
                postData.append('total_realisasi', this.formData.total_realisasi);
                postData.append('triwulan', this.formData.triwulan);
                postData.append('mutasi_asal', this.formData.mutasi_asal);
                postData.append('mutasi_nomor_bamb', this.formData.mutasi_nomor_bamb);
                postData.append('mutasi_tanggal', this.formData.mutasi_tanggal);
                postData.append('mutasi_keterangan', this.formData.mutasi_keterangan || '');
                postData.append('unit_id', this.formData.unit_id || '');
                postData.append('alamat_barang', this.formData.alamat_barang || '');
                postData.append('kondisi', this.formData.kondisi || 'Baik');
                postData.append('nomor_sk_dasar', this.formData.nomor_sk_dasar || '');
                postData.append('alamat_instansi', this.formData.alamat_instansi || '');
                postData.append('pj_asal_nama', this.formData.pj_asal_nama || '');
                postData.append('pj_asal_nip', this.formData.pj_asal_nip || '');
                postData.append('pj_asal_jabatan', this.formData.pj_asal_jabatan || '');
                postData.append('ppk_nama', this.formData.ppk_nama || '');
                postData.append('ppk_nip', this.formData.ppk_nip || '');
                postData.append('from', this.formData.from || 'eksternal');
                postData.append('is_extracomtable', this.formData.is_extracomtable ? 1 : 0);

                // Append specs (hanya kirim items sesuai kategori KIB aktif agar tidak mengotori data)
                postData.append('tanah_items', JSON.stringify(this.isTanah ? (this.formData.tanah_items || []) : []));
                postData.append('mesin_items', JSON.stringify(this.isMesin ? (this.formData.mesin_items || []) : []));
                postData.append('gedung_items', JSON.stringify(this.isGedung ? (this.formData.gedung_items || []) : []));
                postData.append('jaringan_items', JSON.stringify(this.isJaringan ? (this.formData.jaringan_items || []) : []));
                postData.append('lainnya_items', JSON.stringify(this.isLainnya ? (this.formData.lainnya_items || []) : []));
                specJson.is_extracomtable = this.formData.is_extracomtable;
                specJson.is_extracom = this.formData.is_extracomtable;
                postData.append('spesifikasi_json', JSON.stringify(specJson));

                if (this.selectedFile) {
                    postData.append('dokumen_file', this.selectedFile);
                }
                if (this.isEdit) {
                    postData.append('_method', 'PUT');
                }

                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: postData
                    });

                    const result = await res.json();
                    this.isSubmitting = false;

                    if (res.ok && result.success) {
                        this.showToast('Berhasil Disimpan', result.message || 'Data pelimpahan BMD berhasil disimpan ke SIMAT-RK.', 'success');
                        setTimeout(() => {
                            window.location.href = result.redirect || "{{ route('mutasi.eksternal') }}";
                        }, 1200);
                    } else {
                        const errMsg = (result.errors && Object.keys(result.errors).length > 0)
                            ? Object.values(result.errors).flat().join('\n')
                            : (result.message || 'Gagal menyimpan data pelimpahan.');
                        this.showToast('Gagal Menyimpan', errMsg, 'error');
                    }
                } catch (err) {
                    this.isSubmitting = false;
                    console.error(err);
                    this.showToast('Kesalahan Jaringan', 'Gagal menghubungi server. Silakan coba kembali.', 'error');
                }
            },

            // Initialization on mount
            init() {
                this.initFlat108();

                // If editing, populate from initialAstap
                if (this.isEdit && this.initialAstap) {
                    const init = this.initialAstap;
                    Object.keys(init).forEach(k => {
                        if (init[k] !== undefined && init[k] !== null) {
                            this.formData[k] = init[k];
                        }
                    });

                    this.formData.is_extracomtable = Boolean(
                        init.is_extracomtable || 
                        (init.spesifikasi_json && (init.spesifikasi_json.is_extracomtable || init.spesifikasi_json.is_extracom)) || 
                        init.is_extracom || 
                        (init.mesin_items && init.mesin_items[0] && init.mesin_items[0].is_extracom) ||
                        (init.lainnya_items && init.lainnya_items[0] && init.lainnya_items[0].is_extracom)
                    );

                    // Determine active KIB from selected 108 item code, or items array, or default
                    if (this.selected108Item && this.selected108Item.kode) {
                        const kd = this.selected108Item.kode;
                        if (kd.startsWith('1.3.1')) this.activeKibCategory = 'tanah';
                        else if (kd.startsWith('1.3.2')) this.activeKibCategory = 'mesin';
                        else if (kd.startsWith('1.3.3')) this.activeKibCategory = 'gedung';
                        else if (kd.startsWith('1.3.4')) this.activeKibCategory = 'jaringan';
                        else if (kd.startsWith('1.3.5')) this.activeKibCategory = 'lainnya';
                    } else if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                        this.activeKibCategory = 'tanah';
                    } else if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                        this.activeKibCategory = 'gedung';
                    } else if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                        this.activeKibCategory = 'jaringan';
                    } else if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                        this.activeKibCategory = 'lainnya';
                    } else {
                        this.activeKibCategory = 'mesin';
                    }

                    // Pastikan lembar repeater KIB aktif memiliki data (fallback dari flat specs legacy)
                    if (this.activeKibCategory === 'tanah' && (!this.formData.tanah_items || this.formData.tanah_items.length === 0)) {
                        this.formData.tanah_items = [{
                            tanah_nama_barang: init.nama_barang || '',
                            tanah_hak: init.tanah_hak || 'Hak Pakai',
                            tanah_sertifikat_tgl: init.tanah_sertifikat_tgl || '',
                            tanah_sertifikat_no: init.tanah_sertifikat_no || init.sertifikat_nomor || '',
                            tanah_kondisi: init.kondisi || 'Baik',
                            tanah_penggunaan: init.tanah_penggunaan || 'Bangunan Fasilitas Pelayanan Rumah Sakit',
                            tanah_jumlah_barang: init.jumlah_volume || 1,
                            tanah_jumlah_bidang: init.jumlah_volume || 1,
                            tanah_satuan: init.satuan || 'Bidang',
                            tanah_luas_m2: init.tanah_luas_m2 || '',
                            tanah_alamat: init.tanah_alamat || init.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                            tanah_nilai_satuan: init.jumlah_volume > 0 ? Math.round(init.total_realisasi / init.jumlah_volume) : init.total_realisasi,
                            tanah_nilai_fisik: init.total_realisasi || 0
                        }];
                    } else if (this.activeKibCategory === 'gedung' && (!this.formData.gedung_items || this.formData.gedung_items.length === 0)) {
                        this.formData.gedung_items = [{
                            gedung_kode_barang: this.selected108Item?.kode || (init.kode_barang || ''),
                            gedung_nama_barang: init.nama_barang || '',
                            isFilterOpen: false,
                            searchFilter: '',
                            gedung_luas_m2: init.gedung_luas_m2 || '',
                            gedung_bertingkat: init.gedung_bertingkat || 'Tidak',
                            gedung_beton: init.gedung_beton || 'Beton',
                            gedung_kondisi: init.kondisi || 'Baik',
                            gedung_status_tanah: init.gedung_status_tanah || 'Tanah Pemda',
                            gedung_dokumen_no: init.gedung_dokumen_no || '',
                            gedung_dokumen_tgl: init.gedung_dokumen_tgl || '',
                            gedung_fungsi: init.gedung_fungsi || init.keterangan || '',
                            gedung_alamat: init.gedung_alamat || init.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                            gedung_jumlah_bangunan: init.jumlah_volume || 1,
                            gedung_satuan: init.satuan || 'Gedung',
                            gedung_nilai_satuan: init.jumlah_volume > 0 ? Math.round(init.total_realisasi / init.jumlah_volume) : init.total_realisasi
                        }];
                    } else if (this.activeKibCategory === 'jaringan' && (!this.formData.jaringan_items || this.formData.jaringan_items.length === 0)) {
                        this.formData.jaringan_items = [{
                            jaringan_kode_barang: this.selected108Item?.kode || (init.kode_barang || ''),
                            jaringan_nama_barang: init.nama_barang || '',
                            isFilterOpen: false,
                            searchFilter: '',
                            jaringan_luas_m2: init.jaringan_luas_m2 || '',
                            jaringan_panjang_m: init.jaringan_panjang_m || '',
                            jaringan_lebar_m: init.jaringan_lebar_m || '',
                            jaringan_bertingkat: init.jaringan_bertingkat || 'Tidak',
                            jaringan_konstruksi: init.jaringan_konstruksi || 'Aspal Hotmix',
                            jaringan_beton: init.jaringan_beton || 'Beton',
                            jaringan_kondisi: init.kondisi || 'Baik',
                            jaringan_status_tanah: init.jaringan_status_tanah || 'Tanah Pemda',
                            jaringan_dokumen_no: init.jaringan_dokumen_no || '',
                            jaringan_dokumen_tgl: init.jaringan_dokumen_tgl || '',
                            jaringan_keterangan: init.jaringan_keterangan || init.keterangan || '',
                            jaringan_alamat: init.jaringan_alamat || init.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                            jaringan_jumlah: init.jumlah_volume || 1,
                            jaringan_satuan: init.satuan || 'Ruas',
                            jaringan_nilai_satuan: init.jumlah_volume > 0 ? Math.round(init.total_realisasi / init.jumlah_volume) : init.total_realisasi
                        }];
                    } else if (this.activeKibCategory === 'lainnya' && (!this.formData.lainnya_items || this.formData.lainnya_items.length === 0)) {
                        this.formData.lainnya_items = [{
                            is_extracom: init.is_extracom || false,
                            kib_e_type: init.kib_e_type || 'buku',
                            lainnya_kode_barang: this.selected108Item?.kode || (init.kode_barang || ''),
                            lainnya_nama_barang: init.nama_barang || (init.nama || ''),
                            isFilterOpen: false,
                            searchFilter: '',
                            lainnya_judul: init.lainnya_judul || init.nama_barang || '',
                            lainnya_pencipta: init.lainnya_pencipta || '',
                            lainnya_spesifikasi: init.lainnya_spesifikasi || '',
                            lainnya_tahun: init.lainnya_tahun || null,
                            lainnya_ukuran: init.lainnya_ukuran || '',
                            lainnya_asal_daerah: init.lainnya_asal_daerah || '',
                            lainnya_bahan: init.lainnya_bahan || '',
                            lainnya_jenis: init.lainnya_jenis || 'Buku / Kepustakaan Medis',
                            lainnya_kondisi: init.kondisi || 'Baik',
                            lainnya_jumlah: init.jumlah_volume || 1,
                            lainnya_satuan: init.satuan || 'Buah',
                            lainnya_nilai_satuan: init.jumlah_volume > 0 ? Math.round(init.total_realisasi / init.jumlah_volume) : init.total_realisasi,
                            lainnya_no_pabrik: init.no_pabrik || '',
                            lainnya_keterangan: init.mutasi_keterangan || (init.keterangan || ''),
                            ruang_pemegang: init.ruang_pemegang || (this.selectedUnitName || ''),
                            isRuangOpen: false,
                            searchRuang: ''
                        }];
                    } else if (this.activeKibCategory === 'mesin' && (!this.formData.mesin_items || this.formData.mesin_items.length === 0)) {
                        this.formData.mesin_items = [{
                            is_extracom: init.is_extracom || false,
                            mesin_kode_barang: this.selected108Item?.kode || '',
                            mesin_nama_barang: init.nama_barang || '',
                            isFilterOpen: false,
                            searchFilter: '',
                            mesin_merk: init.merk || '',
                            mesin_type: init.type || '',
                            mesin_ukuran: init.ukuran || '',
                            mesin_bahan: init.bahan || '',
                            mesin_no_pabrik: init.no_pabrik || '',
                            mesin_tahun_pembuatan: init.tahun_pembuatan || null,
                            mesin_kondisi: init.kondisi || 'Baik',
                            mesin_no_rangka: init.no_rangka || '',
                            mesin_no_mesin: init.no_mesin || '',
                            mesin_no_bpkb: init.no_bpkb || '',
                            mesin_no_polisi: init.no_polisi || '',
                            mesin_jumlah_barang: init.jumlah_volume || 1,
                            mesin_satuan: init.satuan || 'Unit',
                            mesin_nilai_satuan: init.jumlah_volume > 0 ? Math.round(init.total_realisasi / init.jumlah_volume) : init.total_realisasi,
                            mesin_keterangan: init.mutasi_keterangan || (init.keterangan || ''),
                            ruang_pemegang: init.ruang_pemegang || (this.selectedUnitName || ''),
                            isRuangOpen: false,
                            searchRuang: ''
                        }];
                    }

                    // Normalisasi tanah_items yang ada
                    if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                        this.formData.tanah_items.forEach(it => {
                            if (it.isFilterOpen === undefined) it.isFilterOpen = false;
                            if (it.searchFilter === undefined) it.searchFilter = '';
                            if (!it.tanah_alamat) it.tanah_alamat = this.formData.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1';
                            if (!it.tanah_jumlah_barang && it.tanah_jumlah_bidang) it.tanah_jumlah_barang = it.tanah_jumlah_bidang;
                            if (!it.tanah_satuan) it.tanah_satuan = this.formData.satuan || 'Bidang';
                            if (!it.tanah_nilai_satuan && it.tanah_nilai_fisik) it.tanah_nilai_satuan = it.tanah_nilai_fisik;
                        });
                    }

                    // Normalisasi gedung_items yang ada
                    if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                        this.formData.gedung_items.forEach(it => {
                            if (it.isFilterOpen === undefined) it.isFilterOpen = false;
                            if (it.searchFilter === undefined) it.searchFilter = '';
                            if (!it.gedung_alamat) it.gedung_alamat = this.formData.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1';
                        });
                    }

                    // Normalisasi jaringan_items yang ada
                    if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                        this.formData.jaringan_items.forEach(it => {
                            if (it.isFilterOpen === undefined) it.isFilterOpen = false;
                            if (it.searchFilter === undefined) it.searchFilter = '';
                            if (!it.jaringan_alamat) it.jaringan_alamat = this.formData.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1';
                        });
                    }

                    // Normalisasi mesin_items yang ada agar properti reaktif Alpine tidak undefined
                    if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                        this.formData.mesin_items.forEach(it => {
                            if (it.is_extracom === undefined) it.is_extracom = false;
                            if (it.isFilterOpen === undefined) it.isFilterOpen = false;
                            if (it.isRuangOpen === undefined) it.isRuangOpen = false;
                            if (it.searchRuang === undefined) it.searchRuang = '';
                            if (!it.ruang_pemegang && this.selectedUnitName) it.ruang_pemegang = this.selectedUnitName;
                        });
                    }

                    // Normalisasi lainnya_items yang ada agar properti reaktif Alpine tidak undefined
                    if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                        this.formData.lainnya_items.forEach(it => {
                            if (it.is_extracom === undefined) it.is_extracom = false;
                            if (it.kib_e_type === undefined) it.kib_e_type = 'buku';
                            if (it.isFilterOpen === undefined) it.isFilterOpen = false;
                            if (it.isRuangOpen === undefined) it.isRuangOpen = false;
                            if (it.searchRuang === undefined) it.searchRuang = '';
                            if (!it.ruang_pemegang && this.selectedUnitName) it.ruang_pemegang = this.selectedUnitName;
                        });
                    }
                } else {
                    // Default seed for new form
                    this.selectKibCategory('mesin', true);
                }

                // Hitung otomatis triwulan dan tahun perolehan dari tanggal dokumen
                if (this.formData.mutasi_tanggal) {
                    this.onTanggalChange(this.formData.mutasi_tanggal);
                }

                // Pasang watcher Alpine agar triwulan dan tahun selalu otomatis mengikuti tanggal
                this.$watch('formData.mutasi_tanggal', (val) => {
                    this.onTanggalChange(val);
                });

                // Jika sudah ada mutasi_asal tapi pejabat penyerah belum terisi, coba sinkronkan
                if (this.formData.mutasi_asal && !this.formData.pj_asal_nama) {
                    this.syncPejabatFromSkpd(this.formData.mutasi_asal, false);
                }

                // Sinkronkan total volume, nilai, dan ringkasan kondisi dominan
                this.syncTotalsFromItems();
            }
        };
    }
</script>

<style>
    .custom-scrollbar {
        scrollbar-width: thin;
        scrollbar-color: rgba(99, 102, 241, 0.5) rgba(15, 23, 42, 0.6);
    }
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.6);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(99, 102, 241, 0.5);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(99, 102, 241, 0.8);
    }
</style>
