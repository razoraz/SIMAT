<!-- ========================================================================= -->
<!-- ALPINE.JS STATE & SCRIPT LOGIC (BELANJA BARANG / PERBEKALAN)              -->
<!-- 100% IDENTIK DENGAN ARSITEKTUR MUTASI EKSTERNAL & KEMITRAAN              -->
<!-- ========================================================================= -->
<script>
    function formBelanjaBarang() {
        return {
            currentStep: 1,
            step: 1,
            totalSteps: 3,
            stepErrors: { 1: '', 2: '', 3: '' },
            isSubmitting: false,
            isDataVerified: false,
            isEdit: false,

            // Master Data Lists dari Backend Controller
            master108: @json($dbMaster108 ?? []),
            masterTokoList: @json($dbTokoPenyedias ?? []),
            pejabatsList: @json($dbPejabats ?? []),
            unitsList: @json($dbUnits ?? []),

            // Dropdown UI States
            isTokoDropdownOpen: false,
            isPejabatDropdownOpen: false,
            isEditPpk: false,

            // Kategori KIB Aktif (Default Mesin untuk Belanja Barang/Alkes)
            activeKibCategory: 'mesin',

            // Mode Alokasi Fisik Global (Disamaratakan ke Belum Ditempatkan)
            globalAlokasiMode: 'belum_ditempatkan',
            globalUnitId: null,
            globalAlamatBarang: 'Belum Ditempatkan',

            // Form Data State
            formData: {
                sumber_dana: 'belanja_barang',
                is_extracomtable: false, // Default false: Reguler (sama persis seperti Mutasi Eksternal & Kemitraan)
                tahun_perolehan: new Date().getFullYear(),
                triwulan: (function() {
                    const m = new Date().getMonth() + 1;
                    if (m >= 4 && m <= 6) return 'TW II';
                    if (m >= 7 && m <= 9) return 'TW III';
                    if (m >= 10 && m <= 12) return 'TW IV';
                    return 'TW I';
                })(),
                rekening_penyedia: '',
                rekening_nomor_faktur: '',
                rekening_tanggal_faktur: new Date().toISOString().split('T')[0],
                rekening_keterangan: '',
                ppk_nama: 'BUDI HARTONO, S.Sos',
                ppk_nip: '19760229 200801 1 010',
                jenis_astap_id: '',
                nama_barang: '',
                jumlah_volume: 1,
                satuan: 'Unit',
                total_realisasi: 0,
                unit_id: '',
                kondisi: 'Baik',
                alamat_barang: '',

                // Multi-Item Repeaters per KIB (Identik dengan Mutasi Eksternal)
                tanah_items: [],
                mesin_items: [],
                gedung_items: [],
                jaringan_items: [],
                lainnya_items: []
            },

            // File Uploads
            fileFaktur: null,
            fileKuitansi: null,

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

            // Inisialisasi State Saat Komponen Dimuat
            init() {
                if ('scrollRestoration' in history) {
                    history.scrollRestoration = 'manual';
                }
                window.scrollTo({ top: 0, behavior: 'instant' });
                document.documentElement.scrollTop = 0;
                document.body.scrollTop = 0;

                this.initFlat108();
                if (!this.formData.mesin_items || this.formData.mesin_items.length === 0) {
                    this.addMesinItem();
                }
                this.syncTotalsFromItems();
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

            isTanggalFakturInvalid() {
                if (!this.formData.rekening_tanggal_faktur) return false;
                const t = this.parseDateToTimestamp(this.formData.rekening_tanggal_faktur);
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

            formatFileSize(bytes) {
                if (!bytes || bytes === 0) return '0 B';
                const k = 1024;
                const sizes = ['B', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            },

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

            onTanggalFakturChange(val) {
                if (!val) return;
                this.formData.triwulan = this.calcTriwulanFromDate(val);
            },

            // File Handlers
            handleFileChange(event, type) {
                const file = event.target.files[0];
                if (!file) return;

                if (file.size > 5 * 1024 * 1024) {
                    alert('⚠️ Ukuran file melebihi batas maksimal 5MB. Silakan pilih file yang lebih kecil.');
                    event.target.value = '';
                    return;
                }

                if (type === 'faktur') {
                    this.fileFaktur = file;
                } else if (type === 'kuitansi') {
                    this.fileKuitansi = file;
                }
            },

            removeFile(type) {
                if (type === 'faktur') {
                    this.fileFaktur = null;
                    const el = document.getElementById('dokumen_faktur_input');
                    if (el) el.value = '';
                } else if (type === 'kuitansi') {
                    this.fileKuitansi = null;
                    const el = document.getElementById('dokumen_kuitansi_input');
                    if (el) el.value = '';
                }
            },

            // Autocomplete Toko Penyedia
            get filteredTokoList() {
                const q = (this.formData.rekening_penyedia || '').toLowerCase().trim();
                const list = this.masterTokoList || [];
                if (!q) return list.slice(0, 15);
                return list.filter(item => item && item.toLowerCase().includes(q));
            },

            selectToko(name) {
                this.formData.rekening_penyedia = name;
                this.isTokoDropdownOpen = false;
            },

            // Autocomplete Pejabat PPK
            get filteredPejabatList() {
                const q = (this.formData.ppk_nama || '').toLowerCase().trim();
                const list = this.pejabatsList || [];
                if (!q) return list.slice(0, 15);
                return list.filter(item => {
                    const n = typeof item === 'object' ? item.nama : item;
                    return n && n.toLowerCase().includes(q);
                });
            },

            selectPejabat(item) {
                if (typeof item === 'object') {
                    this.formData.ppk_nama = item.nama || '';
                    if (item.nip) this.formData.ppk_nip = item.nip;
                } else {
                    this.formData.ppk_nama = item;
                }
                this.isPejabatDropdownOpen = false;
            },

            // KIB Category Helpers
            get isTanah() { return this.activeKibCategory === 'tanah'; },
            get isMesin() { return this.activeKibCategory === 'mesin'; },
            get isGedung() { return this.activeKibCategory === 'gedung'; },
            get isJaringan() { return this.activeKibCategory === 'jaringan'; },
            get isLainnya() { return this.activeKibCategory === 'lainnya'; },
            get hasSelectedKib() { return true; },

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

            // Selected Unit Name
            get selectedUnitName() {
                if (!this.formData.unit_id) return '';
                const u = (this.unitsList || []).find(item => Number(item.id) === Number(this.formData.unit_id));
                return u ? u.nama : '';
            },

            getUnitNameById(id) {
                if (!id) return '';
                const u = (this.unitsList || []).find(item => Number(item.id) === Number(id));
                return u ? u.nama : '';
            },

            cleanLegacyDefaultRuang(val) {
                if (!val) return '';
                const trimmed = String(val).trim();
                const lower = trimmed.toLowerCase();
                if (
                    lower === 'gudang perbekalan rsud dr. h. koesnandi' ||
                    lower === 'gudang perbekalan rsud' ||
                    lower === 'gudang perbekalan' ||
                    lower === 'gudang aset' ||
                    lower === 'gudang aset utama' ||
                    lower === 'gudang aset utama / belum ditempatkan' ||
                    lower === 'belum ditempatkan' ||
                    lower === 'belum ditempatkan / di gudang' ||
                    lower === 'belum ditempatkan / di gudang aset'
                ) {
                    return '';
                }
                return trimmed;
            },

            // Mode Alokasi Handlers
            setGlobalAlokasiMode(mode) {
                this.globalAlokasiMode = mode;
                if (mode === 'gudang') {
                    this.formData.unit_id = '';
                    this.formData.alamat_barang = '';
                } else if (mode === 'ruangan' && this.globalUnitId) {
                    this.formData.unit_id = this.globalUnitId;
                    this.formData.alamat_barang = this.globalAlamatBarang || '';
                }
                this.syncTotalsFromItems();
            },

            applyGlobalUnit() {
                if (this.globalAlokasiMode === 'ruangan') {
                    this.formData.unit_id = this.globalUnitId;
                    const uName = this.getUnitNameById(this.globalUnitId);
                    if (this.isMesin && this.formData.mesin_items) {
                        this.formData.mesin_items.forEach(m => { m.ruang_pemegang = uName; });
                    }
                    if (this.isLainnya && this.formData.lainnya_items) {
                        this.formData.lainnya_items.forEach(l => { l.ruang_pemegang = uName; });
                    }
                }
            },

            applyGlobalAlamat() {
                if (this.globalAlokasiMode === 'ruangan') {
                    this.formData.alamat_barang = this.globalAlamatBarang;
                }
            },

            // Flat 108 Items for Quick Lookup
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

            // Validasi & Proteksi Input Jumlah Volume
            handleVolumeKeydown(e) {
                if (e.key === '-' || e.key === 'e' || e.key === '.') {
                    e.preventDefault();
                    return;
                }
                if (e.key === '0') {
                    const target = e.target;
                    const val = target.value;
                    const isAllSelected = (target.selectionStart === 0 && target.selectionEnd === val.length);
                    if (!val || val === '0' || isAllSelected || (target.selectionStart === 0 && target.selectionEnd === 0)) {
                        e.preventDefault();
                    }
                }
            },

            normalizeVolume(item, key) {
                if (!item) return;
                const val = parseInt(item[key]);
                if (isNaN(val) || val < 1) {
                    item[key] = 1;
                } else {
                    item[key] = val;
                }
                this.syncTotalsFromItems();
            },

            // Setter Global Status Akuntansi
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

            // Repeater Actions: Tanah
            addTanahItem() {
                if (!this.formData.tanah_items) this.formData.tanah_items = [];
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
                    tanah_batas: '',
                    tanah_alamat: '',
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

            // Repeater Actions: Mesin (KIB B)
            addMesinItem() {
                if (!this.formData.mesin_items) this.formData.mesin_items = [];

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
                    ruang_pemegang: 'Belum Ditempatkan'
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

            // Repeater Actions: Gedung (KIB C)
            addGedungItem() {
                if (!this.formData.gedung_items) this.formData.gedung_items = [];
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
                    gedung_alamat: '',
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

            // Repeater Actions: Jaringan (KIB D)
            addJaringanItem() {
                if (!this.formData.jaringan_items) this.formData.jaringan_items = [];
                this.formData.jaringan_items.push({
                    jaringan_kode_barang: '',
                    jaringan_nama_barang: '',
                    isFilterOpen: false,
                    searchFilter: '',
                    jaringan_luas_m2: '',
                    jaringan_panjang_m: '',
                    jaringan_lebar_m: '',
                    jaringan_bertingkat: 'Tidak',
                    jaringan_konstruksi: 'Instalasi Pipa/Kabel',
                    jaringan_beton: 'Beton',
                    jaringan_kondisi: 'Baik',
                    jaringan_status_tanah: 'Tanah Pemkab Bondowoso',
                    jaringan_dokumen_no: '',
                    jaringan_dokumen_tgl: '',
                    jaringan_keterangan: '',
                    jaringan_alamat: '',
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
                if (!this.formData.lainnya_items) this.formData.lainnya_items = [];

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
                    lainnya_jenis: 'Buku / Pedoman Medis Perbekalan',
                    lainnya_kondisi: 'Baik',
                    lainnya_jumlah: 1,
                    lainnya_satuan: 'Buah',
                    lainnya_nilai_satuan: 0,
                    lainnya_no_pabrik: '',
                    lainnya_keterangan: '',
                    ruang_pemegang: 'Belum Ditempatkan'
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

            // Auto-Sync Calculations dari Sheet KIB Aktif
            syncTotalsFromItems() {
                if (this.isTanah && this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    let totalQty = 0, totalVal = 0;
                    this.formData.tanah_items.forEach(it => {
                        const q = parseInt(it.tanah_jumlah_barang) || 1;
                        const v = parseFloat(it.tanah_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.tanah_items[0].tanah_satuan || 'Bidang';
                    this.formData.total_realisasi = totalVal;
                    if (this.formData.tanah_items[0].tanah_nama_barang) {
                        this.formData.nama_barang = this.formData.tanah_items[0].tanah_nama_barang;
                    }
                } else if (this.isMesin && this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    let totalQty = 0, totalVal = 0;
                    this.formData.mesin_items.forEach(it => {
                        const q = parseInt(it.mesin_jumlah_barang) || 1;
                        const v = parseFloat(it.mesin_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.mesin_items[0].mesin_satuan || 'Unit';
                    this.formData.total_realisasi = totalVal;
                    
                    const first = this.formData.mesin_items[0];
                    if (first.mesin_nama_barang) {
                        this.formData.nama_barang = first.mesin_nama_barang;
                    }
                } else if (this.isGedung && this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    let totalQty = 0, totalVal = 0;
                    this.formData.gedung_items.forEach(it => {
                        const q = parseInt(it.gedung_jumlah_bangunan) || 1;
                        const v = parseFloat(it.gedung_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.gedung_items[0].gedung_satuan || 'Gedung';
                    this.formData.total_realisasi = totalVal;
                    if (this.formData.gedung_items[0].gedung_nama_barang) {
                        this.formData.nama_barang = this.formData.gedung_items[0].gedung_nama_barang;
                    }
                } else if (this.isJaringan && this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    let totalQty = 0, totalVal = 0;
                    this.formData.jaringan_items.forEach(it => {
                        const q = parseInt(it.jaringan_jumlah) || 1;
                        const v = parseFloat(it.jaringan_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.jaringan_items[0].jaringan_satuan || 'Ruas';
                    this.formData.total_realisasi = totalVal;
                    if (this.formData.jaringan_items[0].jaringan_nama_barang) {
                        this.formData.nama_barang = this.formData.jaringan_items[0].jaringan_nama_barang;
                    }
                } else if (this.isLainnya && this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                    let totalQty = 0, totalVal = 0;
                    this.formData.lainnya_items.forEach(it => {
                        const q = parseInt(it.lainnya_jumlah) || 1;
                        const v = parseFloat(it.lainnya_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.lainnya_items[0].lainnya_satuan || 'Buah';
                    this.formData.total_realisasi = totalVal;
                    if (this.formData.lainnya_items[0].lainnya_nama_barang || this.formData.lainnya_items[0].lainnya_judul) {
                        this.formData.nama_barang = this.formData.lainnya_items[0].lainnya_nama_barang || this.formData.lainnya_items[0].lainnya_judul;
                    }
                }
            },

            // Live Search 108 untuk Tiap Item Sheet KIB
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
                        if (results.length >= 5) break;
                    }
                }
                return results;
            },

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

            // Bersihkan pilihan jenis 108 pada item dan buka dropdown rekomendasi
            clearItem108(item, type) {
                if (!item) return;
                if (type === 'tanah') {
                    item.tanah_kode_barang = '';
                    item.tanah_nama_barang = '';
                    item.searchFilter = '';
                } else if (type === 'mesin') {
                    item.mesin_kode_barang = '';
                    item.mesin_nama_barang = '';
                    item.searchFilter = '';
                } else if (type === 'gedung') {
                    item.gedung_kode_barang = '';
                    item.gedung_nama_barang = '';
                    item.gedung_nama_bangunan = '';
                    item.searchFilter = '';
                } else if (type === 'jaringan') {
                    item.jaringan_kode_barang = '';
                    item.jaringan_nama_barang = '';
                    item.searchFilter = '';
                } else if (type === 'lainnya') {
                    item.lainnya_kode_barang = '';
                    item.lainnya_nama_barang = '';
                    item.lainnya_judul = '';
                    item.searchFilter = '';
                }
                item.isFilterOpen = true;
                this.syncTotalsFromItems();
            },

            getKibEPrefix(item) {
                if (!item) return '1.3.5';
                if (item.kib_e_type === 'kesenian') return '1.3.5.02';
                if (item.kib_e_type === 'hewan_tumbuhan') return '1.3.5.03';
                return '1.3.5.01';
            },

            // Helper Pencarian & Pemilihan Unit/Ruangan Penempatan
            filterUnitsForItem(item) {
                let list = this.unitsList || [];
                if (!item.searchRuang || item.searchRuang.trim() === '') return list;
                const q = item.searchRuang.toLowerCase().trim();
                return list.filter(u => (u.nama || '').toLowerCase().includes(q) || (u.tipe || '').toLowerCase().includes(q));
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
                                nama: (item.mesin_nama_barang || `Barang Mesin #${idx + 1}`),
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
                                nama: (item.lainnya_nama_barang || item.lainnya_judul || `Item Lainnya #${idx + 1}`),
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
                const first = violations[0] || {};
                const msg = `Nilai satuan barang "${first.nama || 'ini'}" (${first.nilaiFormatted || ''}) melebihi batas maksimal Ekstrakomtabel Rp 300.000.`;
                this.showToast('Batas Nilai Extracom Terlampaui', msg, 'error');
                this.focusFirstExtracomViolation();
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

            // Helper validasi kebalikan: Jika di mode Reguler tapi nilai satuan <= Rp 300.000 (Wajib Ekstrakomtabel)
            getRegulerUnder300kViolations() {
                const violations = [];
                // Hanya dicek jika status saat ini Aset Tetap Reguler (bukan extracom)
                if (this.formData.is_extracomtable) return violations;

                if (this.isMesin && Array.isArray(this.formData.mesin_items)) {
                    this.formData.mesin_items.forEach((item, idx) => {
                        const val = parseFloat(item.mesin_nilai_satuan) || 0;
                        if (val > 0 && val <= 300000) {
                            violations.push({
                                index: idx,
                                type: 'mesin',
                                itemNumber: idx + 1,
                                nama: (item.mesin_nama_barang || `Barang Mesin #${idx + 1}`),
                                nilai: val,
                                nilaiFormatted: 'Rp ' + Number(val).toLocaleString('id-ID')
                            });
                        }
                    });
                } else if (this.isLainnya && Array.isArray(this.formData.lainnya_items)) {
                    this.formData.lainnya_items.forEach((item, idx) => {
                        const val = parseFloat(item.lainnya_nilai_satuan) || 0;
                        if (val > 0 && val <= 300000) {
                            violations.push({
                                index: idx,
                                type: 'lainnya',
                                itemNumber: idx + 1,
                                nama: (item.lainnya_nama_barang || item.lainnya_judul || `Item Lainnya #${idx + 1}`),
                                nilai: val,
                                nilaiFormatted: 'Rp ' + Number(val).toLocaleString('id-ID')
                            });
                        }
                    });
                }
                return violations;
            },

            get hasRegulerUnder300kViolation() {
                return this.getRegulerUnder300kViolations().length > 0;
            },

            showRegulerUnder300kAlert(violations) {
                const first = violations[0] || {};
                const msg = `Nilai satuan barang "${first.nama || 'ini'}" (${first.nilaiFormatted || ''}) berada di bawah Rp 300.000. Wajib dicatat sebagai Ekstrakomtabel.`;
                this.showToast('Kriteria Ekstrakomtabel', msg, 'warning');
                this.focusFirstRegulerUnder300kViolation();
            },

            focusFirstRegulerUnder300kViolation() {
                setTimeout(() => {
                    const el = document.querySelector('.border-amber-500') ||
                               document.querySelector('input.text-amber-300') ||
                               document.querySelector('input[placeholder*="Contoh"]');
                    if (el) {
                        el.focus();
                        el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 200);
            },

            // Normalized Items untuk Step 3 Table dan NIBAR Preview
            get activeSheetItems() {
                const results = [];
                if (this.isMesin && this.formData.mesin_items) {
                    this.formData.mesin_items.forEach((it, idx) => {
                        results.push({
                            nama_barang: it.mesin_nama_barang || (it.mesin_merk ? it.mesin_merk + ' ' + (it.mesin_type || '') : '-'),
                            kode_108: it.mesin_kode_barang || '1.3.2',
                            jumlah_volume: parseInt(it.mesin_jumlah_barang) || 1,
                            satuan: it.mesin_satuan || 'Unit',
                            harga_satuan: parseFloat(it.mesin_nilai_satuan) || 0,
                            subtotal: this.getMesinSubtotal(it),
                            spesifikasi: [it.mesin_merk, it.mesin_type, it.mesin_ukuran].filter(Boolean).join(' • '),
                            kondisi: it.mesin_kondisi || 'Baik',
                            penempatan: 'Belum Ditempatkan'
                        });
                    });
                } else if (this.isLainnya && this.formData.lainnya_items) {
                    this.formData.lainnya_items.forEach((it, idx) => {
                        results.push({
                            nama_barang: it.lainnya_nama_barang || it.lainnya_judul || '-',
                            kode_108: it.lainnya_kode_barang || '1.3.5',
                            jumlah_volume: parseInt(it.lainnya_jumlah) || 1,
                            satuan: it.lainnya_satuan || 'Buah',
                            harga_satuan: parseFloat(it.lainnya_nilai_satuan) || 0,
                            subtotal: this.getLainnyaSubtotal(it),
                            spesifikasi: [it.lainnya_pencipta, it.lainnya_spesifikasi].filter(Boolean).join(' • '),
                            kondisi: it.lainnya_kondisi || 'Baik',
                            penempatan: 'Belum Ditempatkan'
                        });
                    });
                } else if (this.isTanah && this.formData.tanah_items) {
                    this.formData.tanah_items.forEach((it, idx) => {
                        results.push({
                            nama_barang: it.tanah_nama_barang || '-',
                            kode_108: it.tanah_kode_barang || '1.3.1',
                            jumlah_volume: parseInt(it.tanah_jumlah_barang) || 1,
                            satuan: it.tanah_satuan || 'Bidang',
                            harga_satuan: parseFloat(it.tanah_nilai_satuan) || 0,
                            subtotal: this.getTanahSubtotal(it),
                            spesifikasi: (it.tanah_luas_m2 ? it.tanah_luas_m2 + ' m² • ' : '') + (it.tanah_hak || 'Hak Pakai'),
                            kondisi: it.tanah_kondisi || 'Baik',
                            penempatan: 'Belum Ditempatkan'
                        });
                    });
                } else if (this.isGedung && this.formData.gedung_items) {
                    this.formData.gedung_items.forEach((it, idx) => {
                        results.push({
                            nama_barang: it.gedung_nama_barang || '-',
                            kode_108: it.gedung_kode_barang || '1.3.3',
                            jumlah_volume: parseInt(it.gedung_jumlah_bangunan) || 1,
                            satuan: it.gedung_satuan || 'Gedung',
                            harga_satuan: parseFloat(it.gedung_nilai_satuan) || 0,
                            subtotal: this.getGedungSubtotal(it),
                            spesifikasi: (it.gedung_luas_m2 ? it.gedung_luas_m2 + ' m² • ' : '') + (it.gedung_beton || 'Beton'),
                            kondisi: it.gedung_kondisi || 'Baik',
                            penempatan: 'Belum Ditempatkan'
                        });
                    });
                } else if (this.isJaringan && this.formData.jaringan_items) {
                    this.formData.jaringan_items.forEach((it, idx) => {
                        results.push({
                            nama_barang: it.jaringan_nama_barang || '-',
                            kode_108: it.jaringan_kode_barang || '1.3.4',
                            jumlah_volume: parseInt(it.jaringan_jumlah) || 1,
                            satuan: it.jaringan_satuan || 'Ruas',
                            harga_satuan: parseFloat(it.jaringan_nilai_satuan) || 0,
                            subtotal: this.getJaringanSubtotal(it),
                            spesifikasi: it.jaringan_konstruksi || 'Instalasi Jaringan',
                            kondisi: it.jaringan_kondisi || 'Baik',
                            penempatan: 'Belum Ditempatkan'
                        });
                    });
                }
                return results;
            },

            get totalVolumeFaktur() {
                return this.activeSheetItems.reduce((sum, it) => sum + (it.jumlah_volume || 1), 0);
            },

            get totalNilaiFaktur() {
                return this.activeSheetItems.reduce((sum, it) => sum + (it.subtotal || 0), 0);
            },

            // Getters spesifik untuk Step 3
            get firstMesinItem() {
                return (this.formData.mesin_items && this.formData.mesin_items[0]) || null;
            },

            get firstLainnyaItem() {
                return (this.formData.lainnya_items && this.formData.lainnya_items[0]) || null;
            },

            get firstGedungItem() {
                return (this.formData.gedung_items && this.formData.gedung_items[0]) || null;
            },

            get firstJaringanItem() {
                return (this.formData.jaringan_items && this.formData.jaringan_items[0]) || null;
            },

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

            get kondisiSummary() {
                const items = this.activeSheetItems;
                if (!items || items.length === 0) return 'Baik';
                const counts = { 'Baik': 0, 'Kurang Baik': 0, 'Rusak Berat': 0 };
                items.forEach(u => {
                    const k = u.kondisi || 'Baik';
                    if (counts[k] !== undefined) counts[k]++;
                    else counts['Baik']++;
                });
                const total = items.length;
                if (counts['Baik'] === total) return 'Baik (Semua)';
                if (counts['Kurang Baik'] === total) return 'Kurang Baik (Semua)';
                if (counts['Rusak Berat'] === total) return 'Rusak Berat (Semua)';
                const parts = [];
                if (counts['Baik'] > 0) parts.push(`${counts['Baik']} Baik`);
                if (counts['Kurang Baik'] > 0) parts.push(`${counts['Kurang Baik']} KB`);
                if (counts['Rusak Berat'] > 0) parts.push(`${counts['Rusak Berat']} RB`);
                return parts.join(', ');
            },

            // Simulasi List Unit Fisik untuk Preview NIBAR
            get simulatedUnits() {
                const list = [];
                const items = this.activeSheetItems || [];
                let count = 0;
                items.forEach((it) => {
                    const qty = it.jumlah_volume || 1;
                    for (let i = 0; i < qty; i++) {
                        count++;
                        list.push({
                            unitNumber: count,
                            nama: it.nama_barang,
                            kode108: it.kode_108,
                            ruang: 'Belum Ditempatkan',
                            kondisi: it.kondisi || 'Baik'
                        });
                    }
                });
                return list;
            },

            // Simulasi Register NIBAR (28-Digit)
            generateSimulatedNibar(index, customKode108) {
                const opdCode = '1.02.0.00.0.00.01.0000';
                const year = this.formData.tahun_perolehan || new Date().getFullYear();
                const code108 = (customKode108 || this.activeKibCode).padEnd(10, '0').substring(0, 10);
                const regNumber = String(index || 1).padStart(4, '0');
                return `${opdCode}.${year}.${code108}.${regNumber}`;
            },

            // Validasi Data Langkah 1 (Identitas Toko & Faktur)
            validateStep1() {
                this.clearStepError(1);
                if (!this.formData.rekening_penyedia || !this.formData.rekening_penyedia.trim()) {
                    this.setStepError(1, 'Nama Toko / Supplier / Rekanan Penyedia wajib diisi.');
                    this.showToast('Data Belum Lengkap', 'Nama Toko / Supplier Rekanan wajib diisi.', 'error');
                    return false;
                }
                if (!this.formData.rekening_nomor_faktur || !this.formData.rekening_nomor_faktur.trim()) {
                    this.setStepError(1, 'Nomor Faktur / Nota Toko wajib diisi.');
                    this.showToast('Data Belum Lengkap', 'Nomor Faktur / Nota Toko wajib diisi.', 'error');
                    return false;
                }
                if (!this.formData.rekening_tanggal_faktur || !String(this.formData.rekening_tanggal_faktur).trim()) {
                    this.setStepError(1, 'Tanggal Faktur / Pembelian wajib diisi.');
                    this.showToast('Data Belum Lengkap', 'Tanggal Faktur / Pembelian wajib diisi.', 'error');
                    return false;
                }
                if (this.isTanggalFakturInvalid()) {
                    this.setStepError(1, 'Tanggal Faktur / Pembelian tidak boleh melebihi tanggal hari ini.');
                    this.showToast('Periksa Tanggal Faktur', 'Tanggal Faktur tidak boleh tanggal masa depan.', 'error');
                    return false;
                }
                return true;
            },

            // Validasi Data Langkah 2 (Rincian Item Barang KIB)
            validateStep2() {
                this.clearStepError(2);
                let items = [];
                let sheetName = '';

                if (this.isMesin) {
                    items = this.formData.mesin_items || [];
                    sheetName = 'KIB B (Peralatan & Mesin)';
                } else if (this.isLainnya) {
                    items = this.formData.lainnya_items || [];
                    sheetName = 'KIB E (Aset Tetap Lainnya)';
                } else if (this.isTanah) {
                    items = this.formData.tanah_items || [];
                    sheetName = 'KIB A (Tanah)';
                } else if (this.isGedung) {
                    items = this.formData.gedung_items || [];
                    sheetName = 'KIB C (Gedung & Bangunan)';
                } else if (this.isJaringan) {
                    items = this.formData.jaringan_items || [];
                    sheetName = 'KIB D (Jalan, Irigasi & Jaringan)';
                }

                if (items.length === 0) {
                    this.setStepError(2, `Belum ada barang yang ditambahkan pada ${sheetName}. Silakan tambahkan minimal 1 item barang.`);
                    this.showToast('Data Belum Lengkap', 'Tambahkan minimal 1 item barang belanja.', 'error');
                    return false;
                }

                for (let i = 0; i < items.length; i++) {
                    const it = items[i];
                    let nama = '';
                    let vol = 1;
                    let hrg = 0;

                    if (this.isMesin) {
                        nama = (it.mesin_nama_barang || '').trim();
                        vol = parseInt(it.mesin_jumlah_barang) || 0;
                        hrg = parseFloat(it.mesin_nilai_satuan) || 0;
                    } else if (this.isLainnya) {
                        nama = (it.lainnya_nama_barang || it.lainnya_judul || '').trim();
                        vol = parseInt(it.lainnya_jumlah) || 0;
                        hrg = parseFloat(it.lainnya_nilai_satuan) || 0;
                    } else if (this.isTanah) {
                        nama = (it.tanah_nama_barang || '').trim();
                        vol = parseInt(it.tanah_jumlah_barang) || 0;
                        hrg = parseFloat(it.tanah_nilai_satuan) || 0;
                    } else if (this.isGedung) {
                        nama = (it.gedung_nama_barang || '').trim();
                        vol = parseInt(it.gedung_jumlah_bangunan) || 0;
                        hrg = parseFloat(it.gedung_nilai_satuan) || 0;
                    } else if (this.isJaringan) {
                        nama = (it.jaringan_nama_barang || '').trim();
                        vol = parseInt(it.jaringan_jumlah) || 0;
                        hrg = parseFloat(it.jaringan_nilai_satuan) || 0;
                    }

                    if (!nama) {
                        this.setStepError(2, `Nama barang pada baris ke-${i + 1} (${sheetName}) belum diisi.`);
                        this.showToast('Data Belum Lengkap', `Nama barang baris ke-${i + 1} belum diisi.`, 'error');
                        return false;
                    }
                    if (vol < 1) {
                        this.setStepError(2, `Jumlah volume pada baris ke-${i + 1} (${sheetName}) minimal 1.`);
                        this.showToast('Data Belum Lengkap', `Volume baris ke-${i + 1} minimal 1.`, 'error');
                        return false;
                    }
                    if (hrg <= 0) {
                        this.setStepError(2, `Harga satuan pada baris ke-${i + 1} (${sheetName}) wajib diisi lebih dari Rp 0.`);
                        this.showToast('Data Belum Lengkap', `Harga satuan baris ke-${i + 1} harus lebih dari Rp 0.`, 'error');
                        return false;
                    }
                }

                // 1. VALIDASI EXTRACOM: Nilai satuan tidak boleh melebihi Rp 300.000 jika mode Ekstrakomtabel
                const extracomViolations = this.getExtracomViolations();
                if (extracomViolations.length > 0) {
                    this.showExtracomAlert(extracomViolations);
                    const msg = 'Nilai satuan barang Extracom tidak boleh melebihi Rp 300.000.';
                    this.showToast('Validasi Extracom Gagal', msg, 'error');
                    this.setStepError(2, msg);
                    return false;
                }

                // 2. VALIDASI REGULER: Nilai satuan <= Rp 300.000 harus dicatat sebagai Ekstrakomtabel
                const regulerUnder300kViolations = this.getRegulerUnder300kViolations();
                if (regulerUnder300kViolations.length > 0) {
                    this.showRegulerUnder300kAlert(regulerUnder300kViolations);
                    const msg = 'Nilai satuan barang ≤ Rp 300.000 harus dicatat sebagai Ekstrakomtabel.';
                    this.showToast('Kriteria Ekstrakomtabel', msg, 'warning');
                    this.setStepError(2, msg);
                    return false;
                }

                return true;
            },

            // Validasi Data Langkah 3 (Verifikasi & Pengesahan Pembukuan)
            validateStep3() {
                this.clearStepError(3);
                if (!this.isDataVerified) {
                    const msg = 'Mohon centang pernyataan bahwa data serah terima BMD telah diverifikasi dengan benar sebelum disimpan.';
                    this.showToast('Verifikasi Diperlukan', msg, 'warning');
                    this.setStepError(3, msg);
                    return false;
                }
                return true;
            },

            // Navigasi Antar Tab Stepper
            goToStep(stepNumber) {
                if (stepNumber > this.currentStep) {
                    if (this.currentStep === 1 || stepNumber > 1) {
                        if (!this.validateStep1()) return;
                    }
                    if (this.currentStep === 2 || stepNumber > 2) {
                        if (!this.validateStep2()) return;
                    }
                }
                this.currentStep = stepNumber;
                this.step = stepNumber;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            nextStep() {
                if (this.currentStep === 1) {
                    if (!this.validateStep1()) return;
                    this.clearStepError(1);
                    this.goToStep(2);
                } else if (this.currentStep === 2) {
                    if (!this.validateStep2()) return;
                    this.clearStepError(2);
                    this.syncTotalsFromItems();
                    this.goToStep(3);
                }
            },

            prevStep() {
                if (this.currentStep > 1) {
                    this.goToStep(this.currentStep - 1);
                }
            },

            // Form Submit via AJAX (Mendukung Payload Multi-Item & File Upload)
            async submitForm() {
                this.clearAllStepErrors();
                if (!this.validateStep1()) {
                    this.goToStep(1);
                    return;
                }
                if (!this.validateStep2()) {
                    this.goToStep(2);
                    return;
                }
                if (!this.validateStep3()) {
                    this.goToStep(3);
                    return;
                }
                this.isSubmitting = true;
                this.syncTotalsFromItems();

                try {
                    const payload = new FormData();
                    payload.append('_token', '{{ csrf_token() }}');
                    payload.append('tahun_perolehan', this.formData.tahun_perolehan || new Date().getFullYear());
                    payload.append('triwulan', this.formData.triwulan || 'TW I');
                    payload.append('rekening_penyedia', this.formData.rekening_penyedia);
                    payload.append('rekening_nomor_faktur', this.formData.rekening_nomor_faktur);
                    payload.append('rekening_tanggal_faktur', this.formData.rekening_tanggal_faktur);
                    payload.append('rekening_keterangan', this.formData.rekening_keterangan || '');
                    payload.append('status_alokasi_global', 'belum_ditempatkan');
                    payload.append('is_extracomtable', this.formData.is_extracomtable ? 1 : 0);
                    payload.append('is_data_verified', this.isDataVerified ? 1 : 0);

                    // Berkas Dokumen Faktur & Kuitansi jika ada
                    if (this.fileFaktur) {
                        payload.append('dokumen_faktur', this.fileFaktur);
                    }
                    if (this.fileKuitansi) {
                        payload.append('dokumen_kuitansi', this.fileKuitansi);
                    }

                    // Petakan Items dari Sheet KIB Aktif (Semua penempatan disamaratakan ke 'Belum Ditempatkan')
                    const mappedItems = [];
                    const defaultJenisId = this.formData.jenis_astap_id || (this.allFlat108[0]?.id || 1);

                    if (this.isMesin && this.formData.mesin_items) {
                        this.formData.mesin_items.forEach(m => {
                            const found108 = this.allFlat108.find(x => x.kode === m.mesin_kode_barang);
                            mappedItems.push({
                                nama_barang: (m.mesin_nama_barang || '').trim(),
                                jenis_astap_id: found108 ? found108.id : defaultJenisId,
                                jumlah_volume: maxVal(m.mesin_jumlah_barang, 1),
                                satuan: m.mesin_satuan || 'Unit',
                                harga_satuan: parseFloat(m.mesin_nilai_satuan) || 0,
                                mode_penempatan: 'belum_ditempatkan',
                                unit_id: null,
                                kondisi: m.mesin_kondisi || 'Baik',
                                alamat_barang: 'Belum Ditempatkan',
                                spesifikasi_json: {
                                    merk: m.mesin_merk || '',
                                    type: m.mesin_type || '',
                                    ukuran: m.mesin_ukuran || '',
                                    bahan: m.mesin_bahan || '',
                                    no_pabrik: m.mesin_no_pabrik || '',
                                    tahun_pembuatan: m.mesin_tahun_pembuatan || null,
                                    ruang_pemegang: 'Belum Ditempatkan',
                                    is_extracom: Boolean(m.is_extracom)
                                }
                            });
                        });
                    } else if (this.isLainnya && this.formData.lainnya_items) {
                        this.formData.lainnya_items.forEach(l => {
                            const found108 = this.allFlat108.find(x => x.kode === l.lainnya_kode_barang);
                            mappedItems.push({
                                nama_barang: (l.lainnya_nama_barang || l.lainnya_judul || '').trim(),
                                jenis_astap_id: found108 ? found108.id : defaultJenisId,
                                jumlah_volume: maxVal(l.lainnya_jumlah, 1),
                                satuan: l.lainnya_satuan || 'Buah',
                                harga_satuan: parseFloat(l.lainnya_nilai_satuan) || 0,
                                mode_penempatan: 'belum_ditempatkan',
                                unit_id: null,
                                kondisi: l.lainnya_kondisi || 'Baik',
                                alamat_barang: 'Belum Ditempatkan',
                                spesifikasi_json: {
                                    lainnya_judul: l.lainnya_judul || '',
                                    lainnya_pencipta: l.lainnya_pencipta || '',
                                    lainnya_spesifikasi: l.lainnya_spesifikasi || '',
                                    ruang_pemegang: 'Belum Ditempatkan',
                                    is_extracom: Boolean(l.is_extracom)
                                }
                            });
                        });
                    } else if (this.isTanah && this.formData.tanah_items) {
                        this.formData.tanah_items.forEach(t => {
                            mappedItems.push({
                                nama_barang: (t.tanah_nama_barang || '').trim(),
                                jenis_astap_id: defaultJenisId,
                                jumlah_volume: maxVal(t.tanah_jumlah_barang, 1),
                                satuan: t.tanah_satuan || 'Bidang',
                                harga_satuan: parseFloat(t.tanah_nilai_satuan) || 0,
                                mode_penempatan: 'belum_ditempatkan',
                                unit_id: null,
                                kondisi: t.tanah_kondisi || 'Baik',
                                alamat_barang: 'Belum Ditempatkan',
                                spesifikasi_json: { luas_m2: t.tanah_luas_m2, hak_tanah: t.tanah_hak, ruang_pemegang: 'Belum Ditempatkan' }
                            });
                        });
                    } else if (this.isGedung && this.formData.gedung_items) {
                        this.formData.gedung_items.forEach(g => {
                            mappedItems.push({
                                nama_barang: (g.gedung_nama_barang || '').trim(),
                                jenis_astap_id: defaultJenisId,
                                jumlah_volume: maxVal(g.gedung_jumlah_bangunan, 1),
                                satuan: g.gedung_satuan || 'Gedung',
                                harga_satuan: parseFloat(g.gedung_nilai_satuan) || 0,
                                mode_penempatan: 'belum_ditempatkan',
                                unit_id: null,
                                kondisi: g.gedung_kondisi || 'Baik',
                                alamat_barang: 'Belum Ditempatkan',
                                spesifikasi_json: { gedung_luas_m2: g.gedung_luas_m2, beton: g.gedung_beton, ruang_pemegang: 'Belum Ditempatkan' }
                            });
                        });
                    } else if (this.isJaringan && this.formData.jaringan_items) {
                        this.formData.jaringan_items.forEach(j => {
                            mappedItems.push({
                                nama_barang: (j.jaringan_nama_barang || '').trim(),
                                jenis_astap_id: defaultJenisId,
                                jumlah_volume: maxVal(j.jaringan_jumlah, 1),
                                satuan: j.jaringan_satuan || 'Ruas',
                                harga_satuan: parseFloat(j.jaringan_nilai_satuan) || 0,
                                mode_penempatan: 'belum_ditempatkan',
                                unit_id: null,
                                kondisi: j.jaringan_kondisi || 'Baik',
                                alamat_barang: 'Belum Ditempatkan',
                                spesifikasi_json: { jaringan_panjang_m: j.jaringan_panjang_m, ruang_pemegang: 'Belum Ditempatkan' }
                            });
                        });
                    }

                    if (mappedItems.length === 0) {
                        this.setStepError(2, 'Belum ada item barang yang diisi pada formulir KIB.');
                        this.showToast('Gagal Menyimpan', 'Daftar item belanja barang kosong.', 'error');
                        this.isSubmitting = false;
                        this.goToStep(2);
                        return;
                    }

                    payload.append('items_json', JSON.stringify(mappedItems));

                    const response = await fetch('{{ route('astap.store_belanja_barang') }}', {
                        method: 'POST',
                        body: payload,
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const res = await response.json();

                    if (response.ok && (res.success || res.status === 'success')) {
                        this.showToast('Transaksi Berhasil Disimpan', res.message || 'Data belanja barang berhasil disimpan ke SIMAT-RK.', 'success');
                        setTimeout(() => {
                            window.location.href = res.redirect_url || res.redirect || '{{ route('master.belanja_barang') }}';
                        }, 1200);
                    } else {
                        let errMsg = res.message || 'Terjadi kesalahan sistem saat menyimpan.';
                        if (res.errors) {
                            const firstErr = Object.values(res.errors)[0];
                            if (Array.isArray(firstErr) && firstErr.length > 0) {
                                errMsg = firstErr[0];
                            }
                        }
                        throw new Error(errMsg);
                    }
                } catch (err) {
                    console.error('Error submit form belanja barang:', err);
                    this.showToast('Gagal Menyimpan Data', err.message || 'Silakan cek koneksi atau input data Anda.', 'error');
                    this.setStepError(3, err.message || 'Terjadi kendala saat menyimpan data.');
                } finally {
                    this.isSubmitting = false;
                }
            }
        };
    }

    function maxVal(v, fallback) {
        const p = parseInt(v, 10);
        return isNaN(p) || p < fallback ? fallback : p;
    }
</script>
