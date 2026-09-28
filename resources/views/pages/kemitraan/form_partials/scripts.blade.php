<!-- ========================================================================= -->
<!-- ALPINE.JS STATE & SCRIPT LOGIC (KEMITRAAN PIHAK KETIGA 1.5.2)             -->
<!-- ========================================================================= -->
<script>
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);
    window.dbUnits = @json(!empty($dbUnits) ? $dbUnits : []);
    window.dbPejabats = @json(!empty($dbPejabats) ? $dbPejabats : []);
    window.dbPenyedias = @json(!empty($dbPenyedias) ? $dbPenyedias : []);
    window.dbMitraKemitraans = @json(!empty($dbMitraKemitraans) ? $dbMitraKemitraans : []);

    function formKemitraan() {
        return {
            currentStep: 1,
            totalSteps: 3,
            isSubmitting: false,
            isDataVerified: false,

            // Autocomplete Riwayat Mitra Kemitraan
            masterMitraList: (window.dbMitraKemitraans && window.dbMitraKemitraans.length > 0)
                ? window.dbMitraKemitraans
                : [
                    'PT. Roche Indonesia',
                    'PT. Fresenius Medical Care Indonesia',
                    'PT. Kimia Farma Diagnostika',
                    'PT. Sysmex Indonesia',
                    'CV. Penyedia Sarana Medika'
                ],
            isMitraDropdownOpen: false,

            get filteredMitraList() {
                const q = (this.formData.mitra_nama || '').toLowerCase().trim();
                if (!q) {
                    return this.masterMitraList.slice(0, 15);
                }
                return this.masterMitraList.filter(m => m && m.toLowerCase().includes(q));
            },

            selectMitra(name) {
                this.formData.mitra_nama = name;
                this.isMitraDropdownOpen = false;
            },

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

            formatRupiah(val) {
                if (!val) return '0';
                return Number(val).toLocaleString('id-ID');
            },

            // Form Data Payload (Sesuai Controller backend `astap.store_kemitraan`)
            formData: {
                // Step 1: Legalitas PKS & Mitra
                mitra_nama: '',
                nomor_pks: '',
                tanggal_pks: '{{ date('d/m/Y') }}',
                skema_kemitraan: 'Sewa',
                tanggal_mulai: '',
                tanggal_selesai: '',
                tahun_perolehan: {{ date('Y') }},
                triwulan: '{{ (date('n') <= 3) ? 'TW I' : ((date('n') <= 6) ? 'TW II' : ((date('n') <= 9) ? 'TW III' : 'TW IV')) }}',
                kemitraan_keterangan: '',

                // Step 2: Klasifikasi 108 & Nilai Aset
                nama_barang: '',
                jenis_astap_id: null,
                jumlah_volume: 1,
                satuan: 'Unit',
                total_realisasi: 0,

                // Step 3: Rincian Fisik, Ruangan & PPK
                unit_id: '',
                kondisi: 'Baik',
                alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                ppk_nama: '',
                ppk_nip: '',

                // Sheet KIB A: Tanah
                tanah_luas_m2: null,
                tanah_hak: 'Hak Pakai',
                tanah_sertifikat_no: '',
                tanah_sertifikat_tgl: '',
                tanah_penggunaan: '',
                tanah_batas: '',
                tanah_alamat: '',
                tanah_items: [],

                // Sheet KIB B: Peralatan & Mesin
                merk: '',
                type: '',
                no_pabrik: '',
                bahan: '',
                ukuran: '',
                tahun_pembuatan: null,
                no_rangka: '',
                no_mesin: '',
                no_polisi: '',

                // Sheet KIB C: Gedung & Bangunan
                gedung_bertingkat: 'Tidak',
                gedung_beton: 'Beton Bertulang',
                gedung_luas_lantai: null,
                gedung_dokumen_no: '',
                gedung_dokumen_tgl: '',
                gedung_status_tanah: 'Tanah Milik RSUD',
                gedung_fungsi: '',

                // Sheet KIB D: Jalan, Irigasi & Jaringan
                jaringan_konstruksi: '',
                jaringan_luas: null,
                jaringan_panjang: null,
                jaringan_lebar: null,
                jaringan_dokumen_no: '',
                jaringan_dokumen_tgl: '',

                // Sheet KIB E: Aset Tetap Lainnya
                lainnya_judul: '',
                lainnya_jenis: '',
                lainnya_ukuran: '',
                lainnya_bahan: '',
                lainnya_asal: '',

                // Spesifikasi JSON
                spesifikasi_json: {}
            },

            // Master Data & Filtering
            pejabatsList: window.dbPejabats || [],
            master108Raw: window.dbMasterJenisAstap108 || {},
            master108: [],
            flat108: [],
            search108: '',
            searchResults108: [],

            selectedJenisIdx: '',
            selectedSubIdx: '',
            currentSubList: [],
            currentSubSubList: [],
            selectedSubSub: null,

            init() {
                this.prepareMaster108();
                this.syncTahunTriwulanFromPks();
                this.syncCascadingToActiveSkema();

                this.$watch('formData.tanggal_pks', (newVal) => {
                    this.syncTahunTriwulanFromPks(newVal);
                });

                this.$watch('formData.skema_kemitraan', (newVal) => {
                    this.syncCascadingToActiveSkema();
                });
            },

            // Sinkronisasi otomatis Tahun Pembukuan dan Triwulan dari Tanggal Penandatanganan PKS
            syncTahunTriwulanFromPks(explicitVal = null) {
                let val = explicitVal;
                if (typeof val !== 'string' || !val) {
                    val = this.formData.tanggal_pks;
                }
                if (!val) return;
                val = String(val).trim();

                if (val.includes('T')) {
                    val = val.split('T')[0];
                }

                let year = null;
                let month = null;

                // Format DD/MM/YYYY atau D/M/YYYY
                if (/^\d{1,2}\/\d{1,2}\/\d{4}$/.test(val)) {
                    const parts = val.split('/');
                    month = parseInt(parts[1], 10);
                    year = parseInt(parts[2], 10);
                } 
                // Format YYYY-MM-DD atau YYYY-M-D
                else if (/^\d{4}-\d{1,2}-\d{1,2}$/.test(val)) {
                    const parts = val.split('-');
                    year = parseInt(parts[0], 10);
                    month = parseInt(parts[1], 10);
                }
                // Format DD-MM-YYYY atau D-M-YYYY
                else if (/^\d{1,2}-\d{1,2}-\d{4}$/.test(val)) {
                    const parts = val.split('-');
                    month = parseInt(parts[1], 10);
                    year = parseInt(parts[2], 10);
                }
                // Fallback standard Date parsing
                else {
                    const d = new Date(val);
                    if (!isNaN(d.getTime())) {
                        year = d.getFullYear();
                        month = d.getMonth() + 1;
                    }
                }

                if (year && year >= 1990 && year <= 2100) {
                    this.formData.tahun_perolehan = year;
                }

                if (month && month >= 1 && month <= 12) {
                    if (month <= 3) {
                        this.formData.triwulan = 'TW I';
                    } else if (month <= 6) {
                        this.formData.triwulan = 'TW II';
                    } else if (month <= 9) {
                        this.formData.triwulan = 'TW III';
                    } else {
                        this.formData.triwulan = 'TW IV';
                    }
                }
            },

            prepareMaster108() {
                const raw = this.master108Raw || [];
                const parsed = [];
                const flat = [];

                if (Array.isArray(raw)) {
                    raw.forEach(j => {
                        if (!j || !j.kode) return;
                        const subList = [];
                        const subs = j.subRincian || j.subs || [];
                        subs.forEach(s => {
                            if (!s || !s.kode) return;
                            const ssList = [];
                            const subSubs = s.subSubRincian || s.subSubs || [];
                            subSubs.forEach(ss => {
                                if (!ss || !ss.id) return;
                                const entry = {
                                    id: ss.id,
                                    kode: ss.kode,
                                    nama: ss.nama
                                };
                                ssList.push(entry);
                                flat.push({
                                    ...entry,
                                    jenisKode: j.kode,
                                    subKode: s.kode
                                });
                            });
                            subList.push({
                                kode: s.kode,
                                nama: s.nama,
                                subSubs: ssList
                            });
                        });
                        parsed.push({
                            kode: j.kode,
                            nama: j.nama,
                            subs: subList
                        });
                    });
                } else if (typeof raw === 'object') {
                    Object.entries(raw).forEach(([jenisKode, jObj]) => {
                        if (!jObj || typeof jObj !== 'object') return;
                        const subList = [];
                        Object.entries(jObj).forEach(([subKode, sObj]) => {
                            if (!sObj) return;
                            const ssList = [];
                            const arr = Array.isArray(sObj) ? sObj : (sObj.subSubRincian || sObj.subSubs || []);
                            arr.forEach(item => {
                                if (!item || !item.id) return;
                                const entry = {
                                    id: item.id,
                                    kode: item.kode,
                                    nama: item.nama
                                };
                                ssList.push(entry);
                                flat.push({
                                    ...entry,
                                    jenisKode: jenisKode,
                                    subKode: subKode
                                });
                            });
                            subList.push({
                                kode: subKode,
                                nama: arr[0]?.nama_sub || subKode,
                                subSubs: ssList
                            });
                        });
                        parsed.push({
                            kode: jenisKode,
                            nama: ('Kelompok ' + jenisKode),
                            subs: subList
                        });
                    });
                }

                this.master108 = parsed;
                this.flat108 = flat;
            },

            // Getter: Deteksi jenis objek aset berdasarkan kode 108 atau nama barang
            get selectedKode108() {
                if (this.selectedSubSub && this.selectedSubSub.kode) return this.selectedSubSub.kode;
                if (this.formData.jenis_astap_id) {
                    const found = this.flat108.find(x => x.id === this.formData.jenis_astap_id);
                    if (found && found.kode) return found.kode;
                }
                return '';
            },

            get selectedObjekType() {
                const kode = this.selectedKode108;
                const nama = ((this.selectedSubSub?.nama || '') + ' ' + (this.formData.nama_barang || '')).toLowerCase();

                // 1. Berdasarkan Kode 108 Permendagri:
                // Akun 1.5.2 Kemitraan: .001 (Tanah), .002 (Mesin/Alat), .003 (Gedung), .004 (Jaringan), .005 (Lainnya)
                // Akun 1.3 Belanja Modal: 1.3.1 (Tanah), 1.3.2 (Mesin), 1.3.3 (Gedung), 1.3.4 (Jaringan), 1.3.5 (Lainnya)
                if (kode) {
                    if (kode.endsWith('.001') || kode.startsWith('1.3.1')) return 'tanah';
                    if (kode.endsWith('.002') || kode.startsWith('1.3.2')) return 'mesin';
                    if (kode.endsWith('.003') || kode.startsWith('1.3.3')) return 'gedung';
                    if (kode.endsWith('.004') || kode.startsWith('1.3.4')) return 'jaringan';
                    if (kode.endsWith('.005') || kode.startsWith('1.3.5')) return 'lainnya';
                }

                // 2. Berdasarkan kecocokan teks nama
                if (nama.includes('tanah') || nama.includes('lahan') || nama.includes('kavling')) return 'tanah';
                if (nama.includes('gedung') || nama.includes('bangunan') || nama.includes('ruang') || nama.includes('paviliun') || nama.includes('rumah')) return 'gedung';
                if (nama.includes('jalan') || nama.includes('irigasi') || nama.includes('jaringan') || nama.includes('pipa') || nama.includes('saluran') || nama.includes('kabel')) return 'jaringan';
                if (nama.includes('lainnya') || nama.includes('buku') || nama.includes('seni') || nama.includes('hewan') || nama.includes('tanaman')) return 'lainnya';

                // Default jenis kemitraan umum: Peralatan & Mesin (Alat medis / operasional)
                return 'mesin';
            },

            get isTanah() {
                return this.selectedObjekType === 'tanah';
            },

            get isMesin() {
                return this.selectedObjekType === 'mesin';
            },

            get isGedung() {
                return this.selectedObjekType === 'gedung';
            },

            get isJaringan() {
                return this.selectedObjekType === 'jaringan';
            },

            get isLainnya() {
                return this.selectedObjekType === 'lainnya';
            },

            get hasSelected108() {
                return !!(this.selectedSubSub && this.selectedSubSub.kode) || !!this.formData.jenis_astap_id;
            },

            get kibLabel() {
                if (this.isTanah) return 'Tanah (KIB A)';
                if (this.isMesin) return 'Peralatan & Mesin (KIB B)';
                if (this.isGedung) return 'Gedung & Bangunan (KIB C)';
                if (this.isJaringan) return 'Jalan & Jaringan (KIB D)';
                if (this.isLainnya) return 'Aset Tetap Lainnya (KIB E)';
                return 'Spesifikasi Aset';
            },

            get kibStepTitle() {
                return 'Spesifikasi Aset';
            },

            get kibStepSubtitle() {
                return 'Penjelasan Aset (Spesifikasi JSON)';
            },

            getUnitName(id) {
                if (!id) return 'Belum ditentukan';
                const u = (window.dbUnits || []).find(item => String(item.id) === String(id));
                return u ? (u.nama + (u.kode_unit ? ' (' + u.kode_unit + ')' : '')) : ('Unit ID #' + id);
            },

            get simulatedNibar() {
                const tahun = this.formData.tahun_perolehan || '{{ date('Y') }}';
                const rawKode = this.selectedKode108 || '1.5.2.01.01.01.001';
                const kodeClean = rawKode.replace(/\./g, '');
                return `1201351102000000280000${tahun}${kodeClean}0000001`;
            },

            // Getter: Kode sub-rincian akun 1.5.2 berdasarkan skema kemitraan aktif di Langkah 1
            get activeSkemaKode() {
                const skema = (this.formData.skema_kemitraan || 'Sewa').toUpperCase();
                if (skema.includes('KSP')) return '1.5.2.01.01.02';
                if (skema.includes('BGS') || skema.includes('BSG')) return '1.5.2.01.01.03';
                if (skema.includes('KSPI') || skema.includes('KSO')) return '1.5.2.01.01.04';
                return '1.5.2.01.01.01'; // Default Sewa
            },

            // Getter: Label nama skema aktif
            get activeSkemaLabel() {
                const code = this.activeSkemaKode;
                if (code === '1.5.2.01.01.02') return 'KSP (Kerja Sama Pemanfaatan)';
                if (code === '1.5.2.01.01.03') return 'BGS / BSG (Bangun Guna / Serah Guna)';
                if (code === '1.5.2.01.01.04') return 'KSPI (Penyediaan Infrastruktur)';
                return 'Sewa (Sewa Barang / Alat)';
            },

            // Getter: Daftar 5 Sub-Sub Rincian Objek Permendagri 108 sesuai skema aktif
            get currentSubSubRecommendations() {
                const prefix = this.activeSkemaKode;
                let items = this.flat108.filter(it => it.kode && it.kode.startsWith(prefix));
                
                // Fallback otomatis jika data master flat108 belum termuat
                if (!items || items.length === 0) {
                    const fallbackData = {
                        '1.5.2.01.01.01': [
                            { id: 14972, kode: '1.5.2.01.01.01.001', nama: 'Sewa Tanah' },
                            { id: 14973, kode: '1.5.2.01.01.01.002', nama: 'Sewa Peralatan dan Mesin' },
                            { id: 14974, kode: '1.5.2.01.01.01.003', nama: 'Sewa Gedung dan Bangunan' },
                            { id: 14975, kode: '1.5.2.01.01.01.004', nama: 'Sewa Jalam, Irigasi dan Jaringan' },
                            { id: 14976, kode: '1.5.2.01.01.01.005', nama: 'Sewa Aset Tetap lainnya' }
                        ],
                        '1.5.2.01.01.02': [
                            { id: 14977, kode: '1.5.2.01.01.02.001', nama: 'Kerja Sama Pemanfaatan Tanah' },
                            { id: 14978, kode: '1.5.2.01.01.02.002', nama: 'Kerja Sama Pemanfaatan Peralatan dan Mesin' },
                            { id: 14979, kode: '1.5.2.01.01.02.003', nama: 'Kerja Sama Pemanfaatan Gedung dan Bangunan' },
                            { id: 14980, kode: '1.5.2.01.01.02.004', nama: 'Kerja Sama Pemanfaatan Jalan, Irigasi dan Jaringan' },
                            { id: 14981, kode: '1.5.2.01.01.02.005', nama: 'Kerja Sama Pemanfaatan Aset Tetap Lainnya' }
                        ],
                        '1.5.2.01.01.03': [
                            { id: 14982, kode: '1.5.2.01.01.03.001', nama: 'Bangun Guna Serah/Bangun Serah Guna (BGS/BSG) Tanah' },
                            { id: 14983, kode: '1.5.2.01.01.03.002', nama: 'Bangun Serah Guna (BSG) Peralatan dan Mesin' },
                            { id: 14984, kode: '1.5.2.01.01.03.003', nama: 'Bangun Serah Guna (BSG) Gedung dan Bangunan' },
                            { id: 14985, kode: '1.5.2.01.01.03.004', nama: 'Bangun Serah Guna (BSG) Jalan, Irigasi dan Jaringan' },
                            { id: 14986, kode: '1.5.2.01.01.03.005', nama: 'Bangun Serah Guna (BSG) Aset Tetap Lainnya' }
                        ],
                        '1.5.2.01.01.04': [
                            { id: 14987, kode: '1.5.2.01.01.04.001', nama: 'Kerja Sama Penyediaan Infrastruktur Tanah' },
                            { id: 14988, kode: '1.5.2.01.01.04.002', nama: 'Kerja Sama Penyediaan Infrastruktur Peralatan dan Mesin' },
                            { id: 14989, kode: '1.5.2.01.01.04.003', nama: 'Kerja Sama Penyediaan Infrastruktur Bangunan dan Gedung' },
                            { id: 14990, kode: '1.5.2.01.01.04.004', nama: 'Kerja Sama Penyediaan Infrastruktur Jalan, Irigasi dan Jaringan' },
                            { id: 14991, kode: '1.5.2.01.01.04.005', nama: 'Kerja Sama Penyediaan Infrastruktur Aset Tetap Lainnya' }
                        ]
                    };
                    items = (fallbackData[prefix] || []).map(f => ({
                        ...f,
                        jenisKode: '1.5.2',
                        subKode: prefix
                    }));
                }

                return items.sort((a, b) => a.kode.localeCompare(b.kode));
            },

            // Helper Icon per Kategori Objek
            getSubSubIcon(kode) {
                if (!kode) return '📦';
                if (kode.endsWith('.001')) return '🏞️'; // Tanah
                if (kode.endsWith('.002')) return '⚙️'; // Peralatan dan Mesin
                if (kode.endsWith('.003')) return '🏢'; // Gedung dan Bangunan
                if (kode.endsWith('.004')) return '🌐'; // Jalan, Irigasi dan Jaringan
                if (kode.endsWith('.005')) return '📦'; // Aset Tetap Lainnya
                return '📑';
            },

            // Helper Nama Pendek Objek
            getSubSubShortLabel(kode, rawNama) {
                if (!kode) return rawNama || 'Objek Aset';
                if (kode.endsWith('.001')) return 'Tanah';
                if (kode.endsWith('.002')) return 'Peralatan & Mesin';
                if (kode.endsWith('.003')) return 'Gedung & Bangunan';
                if (kode.endsWith('.004')) return 'Jalan, Irigasi & Jaringan';
                if (kode.endsWith('.005')) return 'Aset Tetap Lainnya';
                return rawNama;
            },

            // Ganti Skema Kemitraan dari Langkah 2
            setSkemaFromStep2(skemaName) {
                this.formData.skema_kemitraan = skemaName;
                this.syncCascadingToActiveSkema();
                this.showToast('Skema Diganti', `Menampilkan 5 sub-sub rincian untuk ${skemaName}`, 'info');
            },

            // Klik salah satu kartu Sub-Sub Rincian Objek
            selectSubSubItem(item) {
                this.selectFromSearch(item);
            },

            // Sinkronisasi dropdown cascading ke akun 1.5.2 dan sub-rincian skema aktif
            syncCascadingToActiveSkema() {
                const subKode = this.activeSkemaKode;
                const jIdx = this.master108.findIndex(j => j.kode === '1.5.2');
                if (jIdx !== -1) {
                    this.selectedJenisIdx = jIdx;
                    this.currentSubList = this.master108[jIdx].subs || [];
                    const sIdx = this.currentSubList.findIndex(s => s.kode === subKode);
                    if (sIdx !== -1) {
                        this.selectedSubIdx = sIdx;
                        this.currentSubSubList = this.currentSubList[sIdx].subSubs || [];
                    }
                }
            },

            // Live Search 108
            performSearch108() {
                const q = (this.search108 || '').trim().toLowerCase();
                if (!q || q.length < 2) {
                    this.searchResults108 = [];
                    return;
                }
                this.searchResults108 = this.flat108.filter(it => 
                    it.kode.toLowerCase().includes(q) || it.nama.toLowerCase().includes(q)
                ).slice(0, 15);
            },

            selectFromSearch(item) {
                this.selectedSubSub = item;
                this.formData.jenis_astap_id = item.id;
                if (!this.formData.nama_barang) {
                    this.formData.nama_barang = item.nama;
                }
                this.search108 = '';
                this.searchResults108 = [];

                // Sinkronkan dropdown cascading
                if (item.jenisKode && this.master108) {
                    const jIdx = this.master108.findIndex(j => j.kode === item.jenisKode);
                    if (jIdx !== -1) {
                        this.selectedJenisIdx = jIdx;
                        this.currentSubList = this.master108[jIdx].subs || [];
                        if (item.subKode) {
                            const sIdx = this.currentSubList.findIndex(s => s.kode === item.subKode);
                            if (sIdx !== -1) {
                                this.selectedSubIdx = sIdx;
                                this.currentSubSubList = this.currentSubList[sIdx].subSubs || [];
                            }
                        }
                    }
                }

                // Sinkronkan skema bentuk kemitraan di Langkah 1 sesuai kode akun 1.5.2
                if (item.kode) {
                    if (item.kode.startsWith('1.5.2.01.01.01')) {
                        this.formData.skema_kemitraan = 'Sewa';
                    } else if (item.kode.startsWith('1.5.2.01.01.02')) {
                        this.formData.skema_kemitraan = 'KSP';
                    } else if (item.kode.startsWith('1.5.2.01.01.03')) {
                        this.formData.skema_kemitraan = 'BGS/BSG';
                    } else if (item.kode.startsWith('1.5.2.01.01.04')) {
                        this.formData.skema_kemitraan = 'KSPI';
                    }
                }

                // Otomatis atur satuan default sesuai tipe objek aset jika masih default 'Unit'
                if (this.isTanah && (!this.formData.satuan || this.formData.satuan === 'Unit')) {
                    this.formData.satuan = 'Bidang';
                } else if (this.isGedung && (!this.formData.satuan || this.formData.satuan === 'Unit')) {
                    this.formData.satuan = 'Bangunan';
                } else if (this.isJaringan && (!this.formData.satuan || this.formData.satuan === 'Unit')) {
                    this.formData.satuan = 'Ruas / Titik';
                }

                this.showToast('Klasifikasi Terpilih', `${item.kode} • ${item.nama}`, 'info');
            },

            // Quick select shortcut Akun 1.5.2 (Sesuai Permendagri 108 Gambar 2 & 3)
            quickSelectKemitraan(subKode) {
                if (subKode === '1.5.2.01.01.01') {
                    this.setSkemaFromStep2('Sewa');
                } else if (subKode === '1.5.2.01.01.02') {
                    this.setSkemaFromStep2('KSP');
                } else if (subKode === '1.5.2.01.01.03') {
                    this.setSkemaFromStep2('BGS/BSG');
                } else if (subKode === '1.5.2.01.01.04') {
                    this.setSkemaFromStep2('KSPI');
                }
            },

            onJenisChange() {
                if (this.selectedJenisIdx === '') {
                    this.currentSubList = [];
                    this.currentSubSubList = [];
                    this.selectedSubIdx = '';
                    return;
                }
                this.currentSubList = this.master108[this.selectedJenisIdx]?.subs || [];
                this.currentSubSubList = [];
                this.selectedSubIdx = '';
            },

            onSubChange() {
                if (this.selectedSubIdx === '') {
                    this.currentSubSubList = [];
                    return;
                }
                this.currentSubSubList = this.currentSubList[this.selectedSubIdx]?.subSubs || [];
            },

            onSubSubChange(e) {
                const id = parseInt(e.target.value);
                const it = this.flat108.find(x => x.id === id);
                if (it) {
                    this.selectFromSearch(it);
                }
            },

            onPpkSelect() {
                const found = this.pejabatsList.find(p => p.nama === this.formData.ppk_nama);
                if (found) {
                    this.formData.ppk_nip = found.nip || '';
                } else {
                    this.formData.ppk_nip = '';
                }
            },

            // Stepper Navigation
            goToStep(s) {
                if (s > this.currentStep) {
                    for (let stepCheck = this.currentStep; stepCheck < s; stepCheck++) {
                        if (!this.validateStep(stepCheck)) return;
                    }
                }
                this.currentStep = s;
                if (s === 2) {
                    this.syncCascadingToActiveSkema();
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            nextStep() {
                if (this.validateStep(this.currentStep)) {
                    this.currentStep++;
                    if (this.currentStep === 2) {
                        this.syncCascadingToActiveSkema();
                    }
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
                    if (!this.formData.mitra_nama || !this.formData.mitra_nama.trim()) {
                        this.showToast('Validasi Gagal', 'Mohon isi nama perusahaan mitra / rekanan pihak ketiga.', 'error');
                        return false;
                    }
                    if (!this.formData.nomor_pks || !this.formData.nomor_pks.trim()) {
                        this.showToast('Validasi Gagal', 'Mohon isi nomor dokumen Perjanjian Kerja Sama (PKS).', 'error');
                        return false;
                    }
                    if (!this.formData.tanggal_pks) {
                        this.showToast('Validasi Gagal', 'Mohon isi tanggal penandatanganan PKS.', 'error');
                        return false;
                    }
                    if (!this.formData.tahun_perolehan) {
                        this.showToast('Validasi Gagal', 'Mohon tentukan tahun pembukuan.', 'error');
                        return false;
                    }
                } else if (s === 2) {
                    if (!this.formData.jenis_astap_id) {
                        this.showToast('Validasi Gagal', 'Mohon pilih klasifikasi kode barang 108 (rekomendasi Akun 1.5.2 Kemitraan).', 'error');
                        return false;
                    }
                    if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
                        this.showToast('Validasi Gagal', 'Mohon isi nama spesifik barang kemitraan.', 'error');
                        return false;
                    }
                    if (!this.formData.jumlah_volume || this.formData.jumlah_volume < 1) {
                        this.showToast('Validasi Gagal', 'Jumlah volume barang minimal 1 unit.', 'error');
                        return false;
                    }
                    if (!this.formData.satuan || !this.formData.satuan.trim()) {
                        this.showToast('Validasi Gagal', 'Mohon isi satuan barang (contoh: Unit, Set, Buah).', 'error');
                        return false;
                    }
                    if (!this.formData.total_realisasi || this.formData.total_realisasi <= 0) {
                        this.showToast('Validasi Gagal', 'Mohon masukkan total taksiran nilai wajar aset kemitraan (Rp).', 'error');
                        return false;
                    }
                    if (!this.formData.unit_id) {
                        this.showToast('Validasi Gagal', 'Mohon pilih unit / ruangan penempatan barang di RSUD.', 'error');
                        return false;
                    }
                    if (this.isTanah && (!this.formData.tanah_luas_m2 || this.formData.tanah_luas_m2 <= 0)) {
                        this.showToast('Validasi Gagal', 'Mohon isi luas tanah (m²) pada spesifikasi KIB A.', 'error');
                        return false;
                    }
                } else if (s === 3) {
                    if (!this.isDataVerified) {
                        this.showToast('Verifikasi Diperlukan', 'Mohon centang pernyataan bahwa data aset telah diverifikasi dengan benar sebelum disimpan.', 'warning');
                        return false;
                    }
                }
                return true;
            },

            // Submit Form via AJAX
            async submitForm() {
                if (!this.validateStep(1) || !this.validateStep(2) || !this.validateStep(3)) return;

                // Siapkan payload spesifikasi JSON terstruktur sesuai sheet KIB yang aktif
                let specJson = {};

                if (this.isTanah) {
                    this.formData.tanah_items = [{
                        tanah_luas_m2: parseFloat(this.formData.tanah_luas_m2) || 0,
                        tanah_hak: this.formData.tanah_hak || 'Hak Pakai',
                        tanah_sertifikat_no: this.formData.tanah_sertifikat_no || '',
                        tanah_sertifikat_tgl: this.formData.tanah_sertifikat_tgl || '',
                        tanah_penggunaan: this.formData.tanah_penggunaan || '',
                        tanah_batas: this.formData.tanah_batas || '',
                        tanah_alamat: this.formData.tanah_alamat || this.formData.alamat_barang || ''
                    }];

                    specJson = {
                        kategori_kib: 'KIB A (Tanah)',
                        luas_m2: parseFloat(this.formData.tanah_luas_m2) || 0,
                        hak_tanah: this.formData.tanah_hak || 'Hak Pakai',
                        sertifikat_no: this.formData.tanah_sertifikat_no || '',
                        sertifikat_tgl: this.formData.tanah_sertifikat_tgl || '',
                        penggunaan: this.formData.tanah_penggunaan || '',
                        batas_wilayah: this.formData.tanah_batas || '',
                        alamat_lahan: this.formData.tanah_alamat || this.formData.alamat_barang || '',
                        tanah_items: this.formData.tanah_items
                    };
                } else if (this.isMesin) {
                    specJson = {
                        kategori_kib: 'KIB B (Peralatan & Mesin)',
                        merk: this.formData.merk || '',
                        type: this.formData.type || '',
                        no_pabrik: this.formData.no_pabrik || '',
                        bahan: this.formData.bahan || '',
                        ukuran: this.formData.ukuran || '',
                        tahun_pembuatan: this.formData.tahun_pembuatan || null,
                        no_rangka: this.formData.no_rangka || '',
                        no_mesin: this.formData.no_mesin || '',
                        no_polisi: this.formData.no_polisi || ''
                    };
                } else if (this.isGedung) {
                    specJson = {
                        kategori_kib: 'KIB C (Gedung & Bangunan)',
                        bertingkat: this.formData.gedung_bertingkat || 'Tidak',
                        beton: this.formData.gedung_beton || 'Beton Bertulang',
                        luas_lantai_m2: parseFloat(this.formData.gedung_luas_lantai) || 0,
                        dokumen_no: this.formData.gedung_dokumen_no || '',
                        dokumen_tgl: this.formData.gedung_dokumen_tgl || '',
                        status_tanah: this.formData.gedung_status_tanah || 'Tanah Milik RSUD',
                        fungsi_gedung: this.formData.gedung_fungsi || ''
                    };
                } else if (this.isJaringan) {
                    specJson = {
                        kategori_kib: 'KIB D (Jalan, Irigasi & Jaringan)',
                        konstruksi: this.formData.jaringan_konstruksi || '',
                        luas_m2: parseFloat(this.formData.jaringan_luas) || 0,
                        panjang_m: parseFloat(this.formData.jaringan_panjang) || 0,
                        lebar_m: parseFloat(this.formData.jaringan_lebar) || 0,
                        dokumen_no: this.formData.jaringan_dokumen_no || '',
                        dokumen_tgl: this.formData.jaringan_dokumen_tgl || ''
                    };
                } else if (this.isLainnya) {
                    specJson = {
                        kategori_kib: 'KIB E (Aset Tetap Lainnya)',
                        judul: this.formData.lainnya_judul || '',
                        jenis: this.formData.lainnya_jenis || '',
                        ukuran: this.formData.lainnya_ukuran || '',
                        bahan: this.formData.lainnya_bahan || '',
                        asal_usul: this.formData.lainnya_asal || ''
                    };
                }

                this.formData.spesifikasi_json = specJson;
                this.isSubmitting = true;

                try {
                    const res = await fetch('{{ route('astap.store_kemitraan') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify(this.formData)
                    });

                    const json = await res.json();

                    if (res.ok && json.success) {
                        this.showToast('Berhasil Disimpan!', json.message || 'Data Aset Kemitraan berhasil dicatat ke SIMAT-RK.', 'success');
                        setTimeout(() => {
                            window.location.href = json.redirect || '{{ route('astap.index') }}';
                        }, 1200);
                    } else {
                        let errMsg = json.message || 'Terjadi kesalahan saat menyimpan data.';
                        if (json.errors) {
                            errMsg += '\n' + Object.values(json.errors).flat().join('\n');
                        }
                        this.showToast('Gagal Menyimpan', errMsg, 'error');
                    }
                } catch (err) {
                    console.error(err);
                    this.showToast('Kesalahan Jaringan', 'Terjadi kesalahan jaringan atau server saat menyimpan data.', 'error');
                } finally {
                    this.isSubmitting = false;
                }
            }
        };
    }
</script>
