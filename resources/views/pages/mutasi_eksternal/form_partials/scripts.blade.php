<!-- ========================================================================= -->
<!-- ALPINE.JS STATE & SCRIPT LOGIC (PELIMPAHAN SKPD / MUTASI EKSTERNAL)       -->
<!-- ========================================================================= -->
<script>
    function formMutasiEksternal() {
        return {
            currentStep: 1,
            totalSteps: 3,
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

            // Filter & Search 108
            search108Query: '',
            filtered108Results: [],
            activeKibCategory: 'mesin',

            // Form Data State
            formData: {
                from: {{ Js::from(request('from', 'eksternal')) }},
                sumber_dana: 'pelimpahan_skpd',
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
                lainnya_items: [],

                // Legacy flat specs support
                merk: '',
                type: '',
                no_pabrik: '',
                ukuran: '',
                bahan: '',
                no_rangka: '',
                no_mesin: '',
                no_polisi: '',
                sertifikat_nomor: '',
                gedung_luas_m2: '',
                gedung_bertingkat: 'Tidak',
                gedung_beton: 'Beton',
                gedung_status_tanah: 'Tanah Pemda'
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
                } else if (/^\d{2}\/\d{2}\/\d{4}/.test(str)) {
                    const d = new Date(str);
                    if (!isNaN(d.getTime())) {
                        year = d.getFullYear();
                        month = d.getMonth() + 1;
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

            // Handler ketika Pejabat Penyerah dipilih dari input langsung
            onPejabatPenyerahSelect(event) {
                const val = (event.target.value || '').trim();
                if (!val) return;
                const found = (this.availablePejabatPenyerahs || []).find(p => p.nama && p.nama.toLowerCase() === val.toLowerCase())
                    || (this.pejabatPenyerahList || []).find(p => p.nama && p.nama.toLowerCase() === val.toLowerCase());
                if (found) {
                    if (found.nip) this.formData.pj_asal_nip = found.nip;
                    if (found.jabatan) this.formData.pj_asal_jabatan = found.jabatan;
                    if (found.skpd && !this.formData.mutasi_asal) {
                        this.formData.mutasi_asal = found.skpd;
                    }
                }
            },

            selectSkpd(name) {
                this.formData.mutasi_asal = name;
                this.isSkpdDropdownOpen = false;
                this.syncPejabatFromSkpd(name, false);
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

            filter108List() {
                const q = (this.search108Query || '').toLowerCase().trim();
                if (!q) {
                    this.filtered108Results = [];
                    return;
                }
                const activePrefix = this.activeKibCode;
                this.filtered108Results = this.allFlat108
                    .filter(i => (i.kode && i.kode.toLowerCase().includes(q)) || (i.nama && i.nama.toLowerCase().includes(q)) || (i.path && i.path.toLowerCase().includes(q)))
                    .sort((a, b) => {
                        const aMatch = a.kode && a.kode.startsWith(activePrefix) ? 1 : 0;
                        const bMatch = b.kode && b.kode.startsWith(activePrefix) ? 1 : 0;
                        return bMatch - aMatch;
                    })
                    .slice(0, 30);
            },

            select108FromSearch(item) {
                this.formData.jenis_astap_id = item.id;
                if (!this.formData.nama_barang || this.formData.nama_barang.trim() === '') {
                    this.formData.nama_barang = item.nama;
                }
                this.search108Query = '';
                this.filtered108Results = [];

                // Otomatis sesuaikan activeKibCategory dari kode 108 dengan seed default true
                if (item.kode.startsWith('1.3.1')) this.selectKibCategory('tanah', true);
                else if (item.kode.startsWith('1.3.2')) this.selectKibCategory('mesin', true);
                else if (item.kode.startsWith('1.3.3')) this.selectKibCategory('gedung', true);
                else if (item.kode.startsWith('1.3.4')) this.selectKibCategory('jaringan', true);
                else if (item.kode.startsWith('1.3.5')) this.selectKibCategory('lainnya', true);
                
                // Pastikan nama barang item pertama terisi jika masih kosong
                this.syncSingleItemName();
            },

            clear108Selection() {
                this.formData.jenis_astap_id = '';
                this.search108Query = '';
                this.filtered108Results = [];
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
                this.syncTotalsFromItems();
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

            // Input Sinkronisasi Form Induk Langkah 2
            onNamaBarangInput(val) {
                this.formData.nama_barang = val;
                this.syncSingleItemName();
            },

            onTotalRealisasiInput(val) {
                let raw = String(val).replace(/\D/g, '');
                const num = raw ? parseInt(raw, 10) : 0;
                this.formData.total_realisasi = num;
                
                // Jika hanya 1 item dalam repeater, sinkronkan nilai satuannya
                if (!this.isMultiItemActive) {
                    const vol = Math.max(1, parseInt(this.formData.jumlah_volume) || 1);
                    const unitPrice = Math.round(num / vol);
                    if (this.isTanah && this.formData.tanah_items && this.formData.tanah_items.length === 1) {
                        this.formData.tanah_items[0].tanah_nilai_fisik = num;
                    } else if (this.isMesin && this.formData.mesin_items && this.formData.mesin_items.length === 1) {
                        this.formData.mesin_items[0].mesin_nilai_satuan = unitPrice;
                    } else if (this.isGedung && this.formData.gedung_items && this.formData.gedung_items.length === 1) {
                        this.formData.gedung_items[0].gedung_nilai_satuan = unitPrice;
                    } else if (this.isJaringan && this.formData.jaringan_items && this.formData.jaringan_items.length === 1) {
                        this.formData.jaringan_items[0].jaringan_nilai_satuan = unitPrice;
                    } else if (this.isLainnya && this.formData.lainnya_items && this.formData.lainnya_items.length === 1) {
                        this.formData.lainnya_items[0].lainnya_nilai_satuan = unitPrice;
                    }
                }
            },

            onJumlahVolumeInput(val) {
                const vol = Math.max(1, parseInt(val) || 1);
                this.formData.jumlah_volume = vol;
                if (!this.isMultiItemActive) {
                    if (this.isMesin && this.formData.mesin_items && this.formData.mesin_items.length === 1) {
                        this.formData.mesin_items[0].mesin_jumlah_barang = vol;
                        if (this.formData.total_realisasi > 0) {
                            this.formData.mesin_items[0].mesin_nilai_satuan = Math.round(this.formData.total_realisasi / vol);
                        }
                    } else if (this.isGedung && this.formData.gedung_items && this.formData.gedung_items.length === 1) {
                        this.formData.gedung_items[0].gedung_jumlah_bangunan = vol;
                        if (this.formData.total_realisasi > 0) {
                            this.formData.gedung_items[0].gedung_nilai_satuan = Math.round(this.formData.total_realisasi / vol);
                        }
                    } else if (this.isJaringan && this.formData.jaringan_items && this.formData.jaringan_items.length === 1) {
                        this.formData.jaringan_items[0].jaringan_jumlah = vol;
                        if (this.formData.total_realisasi > 0) {
                            this.formData.jaringan_items[0].jaringan_nilai_satuan = Math.round(this.formData.total_realisasi / vol);
                        }
                    } else if (this.isLainnya && this.formData.lainnya_items && this.formData.lainnya_items.length === 1) {
                        this.formData.lainnya_items[0].lainnya_jumlah = vol;
                        if (this.formData.total_realisasi > 0) {
                            this.formData.lainnya_items[0].lainnya_nilai_satuan = Math.round(this.formData.total_realisasi / vol);
                        }
                    }
                }
            },

            // Repeater Actions: Tanah
            addTanahItem() {
                this.formData.tanah_items.push({
                    tanah_nama_barang: this.formData.nama_barang || '',
                    tanah_hak: 'Hak Pakai',
                    tanah_sertifikat_tgl: '',
                    tanah_sertifikat_no: '',
                    tanah_kondisi: 'Baik',
                    tanah_penggunaan: 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                    tanah_jumlah_bidang: 1,
                    tanah_luas_m2: '',
                    tanah_alamat: this.formData.alamat_barang || '',
                    tanah_nilai_fisik: 0
                });
                this.syncTotalsFromItems();
            },

            removeTanahItem(idx) {
                if (this.formData.tanah_items.length > 1) {
                    this.formData.tanah_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getTanahSubtotal(item) {
                return parseFloat(item.tanah_nilai_fisik) || 0;
            },

            // Repeater Actions: Mesin
            addMesinItem() {
                this.formData.mesin_items.push({
                    mesin_nama_barang: this.formData.nama_barang || '',
                    mesin_merk: '',
                    mesin_type: '',
                    mesin_ukuran: '',
                    mesin_bahan: '',
                    mesin_no_pabrik: '',
                    mesin_no_rangka: '',
                    mesin_no_mesin: '',
                    mesin_no_polisi: '',
                    mesin_kondisi: 'Baik',
                    mesin_jumlah_barang: 1,
                    mesin_satuan: this.formData.satuan || 'Unit',
                    mesin_nilai_satuan: 0
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

            // Repeater Actions: Gedung
            addGedungItem() {
                this.formData.gedung_items.push({
                    gedung_nama_barang: this.formData.nama_barang || '',
                    gedung_luas_m2: '',
                    gedung_bertingkat: 'Tidak',
                    gedung_beton: 'Beton',
                    gedung_kondisi: 'Baik',
                    gedung_status_tanah: 'Tanah Pemkab Bondowoso',
                    gedung_dokumen_no: '',
                    gedung_jumlah_bangunan: 1,
                    gedung_satuan: 'Gedung',
                    gedung_nilai_satuan: 0
                });
                this.syncTotalsFromItems();
            },

            removeGedungItem(idx) {
                if (this.formData.gedung_items.length > 1) {
                    this.formData.gedung_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getGedungSubtotal(item) {
                const qty = parseInt(item.gedung_jumlah_bangunan) || 1;
                const unitVal = parseFloat(item.gedung_nilai_satuan) || 0;
                return qty * unitVal;
            },

            // Repeater Actions: Jaringan
            addJaringanItem() {
                this.formData.jaringan_items.push({
                    jaringan_nama_barang: this.formData.nama_barang || '',
                    jaringan_konstruksi: '',
                    jaringan_panjang_m: '',
                    jaringan_luas_m2: '',
                    jaringan_lokasi: this.formData.alamat_barang || '',
                    jaringan_kondisi: 'Baik',
                    jaringan_jumlah: 1,
                    jaringan_satuan: 'Ruas',
                    jaringan_nilai_satuan: 0
                });
                this.syncTotalsFromItems();
            },

            removeJaringanItem(idx) {
                if (this.formData.jaringan_items.length > 1) {
                    this.formData.jaringan_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getJaringanSubtotal(item) {
                const qty = parseInt(item.jaringan_jumlah) || 1;
                const unitVal = parseFloat(item.jaringan_nilai_satuan) || 0;
                return qty * unitVal;
            },

            // Repeater Actions: Lainnya
            addLainnyaItem() {
                this.formData.lainnya_items.push({
                    lainnya_judul: this.formData.nama_barang || '',
                    lainnya_jenis: 'Buku / Kepustakaan Medis',
                    lainnya_pencipta: '',
                    lainnya_spesifikasi: '',
                    lainnya_kondisi: 'Baik',
                    lainnya_jumlah: 1,
                    lainnya_satuan: 'Eksemplar',
                    lainnya_nilai_satuan: 0
                });
                this.syncTotalsFromItems();
            },

            removeLainnyaItem(idx) {
                if (this.formData.lainnya_items.length > 1) {
                    this.formData.lainnya_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getLainnyaSubtotal(item) {
                const qty = parseInt(item.lainnya_jumlah) || 1;
                const unitVal = parseFloat(item.lainnya_nilai_satuan) || 0;
                return qty * unitVal;
            },

            // Auto-Sync Calculations
            syncTotalsFromItems() {
                if (this.isTanah && this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    this.formData.jumlah_volume = this.formData.tanah_items.length;
                    this.formData.satuan = 'Bidang';
                    const sum = this.formData.tanah_items.reduce((acc, it) => acc + (parseFloat(it.tanah_nilai_fisik) || 0), 0);
                    this.formData.total_realisasi = sum;
                    if (this.formData.tanah_items[0].tanah_kondisi) {
                        this.formData.kondisi = this.formData.tanah_items[0].tanah_kondisi;
                    }
                } else if (this.isMesin && this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    let totalQty = 0;
                    let totalVal = 0;
                    this.formData.mesin_items.forEach(it => {
                        const q = parseInt(it.mesin_jumlah_barang) || 1;
                        const v = parseFloat(it.mesin_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.mesin_items[0].mesin_satuan || 'Unit';
                    this.formData.total_realisasi = totalVal;
                    if (this.formData.mesin_items[0].mesin_kondisi) {
                        this.formData.kondisi = this.formData.mesin_items[0].mesin_kondisi;
                    }
                } else if (this.isGedung && this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    let totalQty = 0;
                    let totalVal = 0;
                    this.formData.gedung_items.forEach(it => {
                        const q = parseInt(it.gedung_jumlah_bangunan) || 1;
                        const v = parseFloat(it.gedung_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.gedung_items[0].gedung_satuan || 'Gedung';
                    this.formData.total_realisasi = totalVal;
                    if (this.formData.gedung_items[0].gedung_kondisi) {
                        this.formData.kondisi = this.formData.gedung_items[0].gedung_kondisi;
                    }
                } else if (this.isJaringan && this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    let totalQty = 0;
                    let totalVal = 0;
                    this.formData.jaringan_items.forEach(it => {
                        const q = parseInt(it.jaringan_jumlah) || 1;
                        const v = parseFloat(it.jaringan_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.jaringan_items[0].jaringan_satuan || 'Ruas';
                    this.formData.total_realisasi = totalVal;
                    if (this.formData.jaringan_items[0].jaringan_kondisi) {
                        this.formData.kondisi = this.formData.jaringan_items[0].jaringan_kondisi;
                    }
                } else if (this.isLainnya && this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                    let totalQty = 0;
                    let totalVal = 0;
                    this.formData.lainnya_items.forEach(it => {
                        const q = parseInt(it.lainnya_jumlah) || 1;
                        const v = parseFloat(it.lainnya_nilai_satuan) || 0;
                        totalQty += q;
                        totalVal += (q * v);
                    });
                    this.formData.jumlah_volume = totalQty > 0 ? totalQty : 1;
                    this.formData.satuan = this.formData.lainnya_items[0].lainnya_satuan || 'Item';
                    this.formData.total_realisasi = totalVal;
                    if (this.formData.lainnya_items[0].lainnya_kondisi) {
                        this.formData.kondisi = this.formData.lainnya_items[0].lainnya_kondisi;
                    }
                }

                // Otomatis pastikan nama_barang terisi dari Kode 108 jika belum ada
                if (!this.formData.nama_barang || this.formData.nama_barang.trim() === '') {
                    if (this.selected108Item && this.selected108Item.nama) {
                        this.formData.nama_barang = this.selected108Item.nama;
                    }
                }
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
                if (this.validateStep(this.currentStep)) {
                    this.currentStep = s;
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
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

            validateStep(s) {
                if (s === 1) {
                    let missing = [];
                    if (!this.formData.mutasi_asal || !this.formData.mutasi_asal.trim()) {
                        missing.push('Instansi / SKPD Asal Pengirim BMD');
                    }
                    if (!this.formData.mutasi_nomor_bamb || !this.formData.mutasi_nomor_bamb.trim()) {
                        missing.push('Nomor Berita Acara (BAMB / BAST)');
                    }
                    if (!this.formData.mutasi_tanggal) {
                        missing.push('Tanggal Dokumen Berita Acara');
                    }
                    if (!this.formData.tahun_perolehan) {
                        missing.push('Tahun Perolehan BMD');
                    }

                    if (missing.length > 0) {
                        this.showValidationAlert(1, missing);
                        return false;
                    }
                } else if (s === 2) {
                    let missing = [];
                    if (!this.formData.jenis_astap_id) {
                        missing.push('Klasifikasi Kode Rekening Permendagri 108');
                    }
                    if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
                        if (this.selected108Item && this.selected108Item.nama) {
                            this.formData.nama_barang = this.selected108Item.nama;
                        } else if (this.firstMesinItem && (this.firstMesinItem.mesin_merk || this.firstMesinItem.mesin_type)) {
                            this.formData.nama_barang = [this.firstMesinItem.mesin_merk, this.firstMesinItem.mesin_type].filter(Boolean).join(' ');
                        } else if (this.formData.tanah_items?.[0]?.tanah_nama_barang) {
                            this.formData.nama_barang = this.formData.tanah_items[0].tanah_nama_barang;
                        } else if (this.formData.gedung_items?.[0]?.gedung_nama_barang) {
                            this.formData.nama_barang = this.formData.gedung_items[0].gedung_nama_barang;
                        } else if (this.formData.jaringan_items?.[0]?.jaringan_nama_barang) {
                            this.formData.nama_barang = this.formData.jaringan_items[0].jaringan_nama_barang;
                        } else if (this.formData.lainnya_items?.[0]?.lainnya_judul) {
                            this.formData.nama_barang = this.formData.lainnya_items[0].lainnya_judul;
                        } else {
                            this.formData.nama_barang = 'Aset Pelimpahan SKPD';
                        }
                    }
                    if (!this.formData.total_realisasi || Number(this.formData.total_realisasi) <= 0) {
                        missing.push('Nilai Perolehan Satuan pada rincian unit KIB di atas (Total Nilai BMD harus > 0)');
                    }

                    if (missing.length > 0) {
                        this.showValidationAlert(2, missing);
                        return false;
                    }
                } else if (s === 3) {
                    if (!this.isDataVerified) {
                        if (typeof Swal !== 'undefined') {
                            Swal.fire({
                                icon: 'warning',
                                title: 'Verifikasi Data Belum Dicentang',
                                text: 'Mohon centang kotak pernyataan verifikasi data serah terima di bagian bawah sebelum menyimpan ke database.',
                                confirmButtonText: 'Baik, Saya Mengerti',
                                confirmButtonColor: '#6366f1',
                                background: '#0f172a',
                                color: '#ffffff'
                            });
                        } else {
                            this.showToast('Verifikasi Diperlukan', 'Mohon centang pernyataan verifikasi di bagian bawah.', 'warning');
                        }
                        return false;
                    }
                }
                return true;
            },

            showValidationAlert(stepNum, missingFields) {
                const listHtml = missingFields.map(f => `
                    <li class="flex items-center gap-2 text-rose-300">
                        <span class="text-rose-400 font-bold">✕</span> 
                        <span class="font-bold text-white">${f}</span>
                    </li>
                `).join('');
                
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Kolom Wajib Belum Diisi',
                        html: `
                            <div class="text-left text-xs space-y-3 text-slate-300">
                                <p class="text-slate-200">
                                    Tombol <strong>Lanjut ke Langkah ${stepNum + 1}</strong> belum dapat memproses karena kolom wajib berikut masih kosong:
                                </p>
                                <ul class="p-3.5 rounded-2xl bg-slate-900/90 border border-rose-500/40 space-y-2">
                                    ${listHtml}
                                </ul>
                                <div class="p-3 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-200 text-[11px] leading-relaxed">
                                    💡 <strong>Catatan: Tidak harus mengisi semua kolom di layar!</strong> Kolom lain seperti SK Bupati, Pejabat Penyerah, dan Berkas Lampiran bersifat <em>opsional</em> (bisa dikosongkan/diisi nanti).
                                </div>
                            </div>
                        `,
                        confirmButtonText: 'Lengkapi Sekarang &rarr;',
                        confirmButtonColor: '#6366f1',
                        background: '#0f172a',
                        color: '#ffffff'
                    }).then(() => {
                        this.focusFirstMissingField(stepNum);
                    });
                } else {
                    this.showToast('Kolom Wajib Belum Diisi', missingFields.join(', '), 'error');
                    this.focusFirstMissingField(stepNum);
                }
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
                        if (!this.formData.jenis_astap_id) {
                            const el = document.querySelector('input[x-model="search108Query"]');
                            if (el) { el.focus(); el.scrollIntoView({ behavior: 'smooth', block: 'center' }); }
                        } else if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
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
                if (!this.validateStep(1) || !this.validateStep(2) || !this.validateStep(3)) return;

                this.syncTotalsFromItems();

                // Siapkan payload spesifikasi JSON
                let specJson = {
                    sumber_dana: 'pelimpahan_skpd',
                    skpd_asal: this.formData.mutasi_asal,
                    nomor_bamb: this.formData.mutasi_nomor_bamb,
                    tanggal_bamb: this.formData.mutasi_tanggal,
                    kondisi: this.formData.kondisi,
                    keterangan: this.formData.mutasi_keterangan,
                    nomor_sk_dasar: this.formData.nomor_sk_dasar,
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
                        specJson.merk = firstM.mesin_merk || '';
                        specJson.type = firstM.mesin_type || '';
                        specJson.no_pabrik = firstM.mesin_no_pabrik || '';
                        specJson.ukuran = firstM.mesin_ukuran || '';
                        specJson.bahan = firstM.mesin_bahan || '';
                        specJson.no_rangka = firstM.mesin_no_rangka || '';
                        specJson.no_mesin = firstM.mesin_no_mesin || '';
                        specJson.no_polisi = firstM.mesin_no_polisi || '';
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
                    }
                } else if (this.isJaringan) {
                    specJson.kategori_kib = 'KIB D (Jalan, Irigasi & Jaringan)';
                    specJson.jaringan_items = this.formData.jaringan_items;
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
                postData.append('pj_asal_nama', this.formData.pj_asal_nama || '');
                postData.append('pj_asal_nip', this.formData.pj_asal_nip || '');
                postData.append('pj_asal_jabatan', this.formData.pj_asal_jabatan || '');
                postData.append('ppk_nama', this.formData.ppk_nama || '');
                postData.append('ppk_nip', this.formData.ppk_nip || '');
                postData.append('from', this.formData.from || 'eksternal');

                // Append specs
                postData.append('tanah_items', JSON.stringify(this.formData.tanah_items || []));
                postData.append('mesin_items', JSON.stringify(this.formData.mesin_items || []));
                postData.append('gedung_items', JSON.stringify(this.formData.gedung_items || []));
                postData.append('jaringan_items', JSON.stringify(this.formData.jaringan_items || []));
                postData.append('lainnya_items', JSON.stringify(this.formData.lainnya_items || []));
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
                            tanah_jumlah_bidang: init.jumlah_volume || 1,
                            tanah_luas_m2: init.tanah_luas_m2 || '',
                            tanah_alamat: init.alamat_barang || '',
                            tanah_nilai_fisik: init.total_realisasi || 0
                        }];
                    } else if (this.activeKibCategory === 'gedung' && (!this.formData.gedung_items || this.formData.gedung_items.length === 0)) {
                        this.formData.gedung_items = [{
                            gedung_nama_barang: init.nama_barang || '',
                            gedung_luas_m2: init.gedung_luas_m2 || '',
                            gedung_bertingkat: init.gedung_bertingkat || 'Tidak',
                            gedung_beton: init.gedung_beton || 'Beton',
                            gedung_kondisi: init.kondisi || 'Baik',
                            gedung_status_tanah: init.gedung_status_tanah || 'Tanah Pemda',
                            gedung_dokumen_no: init.gedung_dokumen_no || '',
                            gedung_jumlah_bangunan: init.jumlah_volume || 1,
                            gedung_satuan: init.satuan || 'Gedung',
                            gedung_nilai_satuan: init.jumlah_volume > 0 ? Math.round(init.total_realisasi / init.jumlah_volume) : init.total_realisasi
                        }];
                    } else if (this.activeKibCategory === 'jaringan' && (!this.formData.jaringan_items || this.formData.jaringan_items.length === 0)) {
                        this.formData.jaringan_items = [{
                            jaringan_nama_barang: init.nama_barang || '',
                            jaringan_konstruksi: init.jaringan_konstruksi || '',
                            jaringan_panjang_m: init.jaringan_panjang_m || '',
                            jaringan_luas_m2: init.jaringan_luas_m2 || '',
                            jaringan_lokasi: init.alamat_barang || '',
                            jaringan_kondisi: init.kondisi || 'Baik',
                            jaringan_jumlah: init.jumlah_volume || 1,
                            jaringan_satuan: init.satuan || 'Ruas',
                            jaringan_nilai_satuan: init.jumlah_volume > 0 ? Math.round(init.total_realisasi / init.jumlah_volume) : init.total_realisasi
                        }];
                    } else if (this.activeKibCategory === 'lainnya' && (!this.formData.lainnya_items || this.formData.lainnya_items.length === 0)) {
                        this.formData.lainnya_items = [{
                            lainnya_judul: init.nama_barang || '',
                            lainnya_jenis: 'Buku / Kepustakaan Medis',
                            lainnya_pencipta: '',
                            lainnya_spesifikasi: '',
                            lainnya_kondisi: init.kondisi || 'Baik',
                            lainnya_jumlah: init.jumlah_volume || 1,
                            lainnya_satuan: init.satuan || 'Eksemplar',
                            lainnya_nilai_satuan: init.jumlah_volume > 0 ? Math.round(init.total_realisasi / init.jumlah_volume) : init.total_realisasi
                        }];
                    } else if (this.activeKibCategory === 'mesin' && (!this.formData.mesin_items || this.formData.mesin_items.length === 0)) {
                        this.formData.mesin_items = [{
                            mesin_nama_barang: init.nama_barang || '',
                            mesin_merk: init.merk || '',
                            mesin_type: init.type || '',
                            mesin_ukuran: init.ukuran || '',
                            mesin_bahan: init.bahan || '',
                            mesin_no_pabrik: init.no_pabrik || '',
                            mesin_no_rangka: init.no_rangka || '',
                            mesin_no_mesin: init.no_mesin || '',
                            mesin_no_polisi: init.no_polisi || '',
                            mesin_kondisi: init.kondisi || 'Baik',
                            mesin_jumlah_barang: init.jumlah_volume || 1,
                            mesin_satuan: init.satuan || 'Unit',
                            mesin_nilai_satuan: init.jumlah_volume > 0 ? Math.round(init.total_realisasi / init.jumlah_volume) : init.total_realisasi
                        }];
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
