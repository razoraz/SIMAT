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

            formatTanggalIndo(val) {
                if (!val) return '';
                val = String(val).trim();
                if (/^\d{4}-\d{2}-\d{2}$/.test(val)) {
                    const parts = val.split('-');
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
                return val;
            },

            parseDateToTimestamp(val) {
                if (!val) return 0;
                if (val instanceof Date) return val.getTime();
                val = String(val).trim();
                if (/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}$/.test(val)) {
                    const parts = val.split(/[\/\-]/);
                    return new Date(parseInt(parts[2], 10), parseInt(parts[1], 10) - 1, parseInt(parts[0], 10)).getTime();
                }
                const d = new Date(val);
                return isNaN(d.getTime()) ? 0 : d.getTime();
            },

            isTanggalSelesaiInvalid() {
                if (!this.formData.tanggal_mulai || !this.formData.tanggal_selesai) return false;
                const tMulai = this.parseDateToTimestamp(this.formData.tanggal_mulai);
                const tSelesai = this.parseDateToTimestamp(this.formData.tanggal_selesai);
                return tMulai > 0 && tSelesai > 0 && tSelesai < tMulai;
            },

            get durasiKonsesiText() {
                if (!this.formData.tanggal_mulai || !this.formData.tanggal_selesai) return '';
                const tMulai = this.parseDateToTimestamp(this.formData.tanggal_mulai);
                const tSelesai = this.parseDateToTimestamp(this.formData.tanggal_selesai);
                if (tSelesai < tMulai) return '';
                const diffDays = Math.round((tSelesai - tMulai) / (1000 * 60 * 60 * 24));
                if (diffDays <= 0) return '1 Hari';
                const years = Math.floor(diffDays / 365);
                const remainingDays = diffDays % 365;
                const months = Math.floor(remainingDays / 30);
                const days = remainingDays % 30;
                const parts = [];
                if (years > 0) parts.push(`${years} Tahun`);
                if (months > 0) parts.push(`${months} Bulan`);
                if (days > 0 && years === 0) parts.push(`${days} Hari`);
                return parts.join(' ') || `${diffDays} Hari`;
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
                tanah_kondisi: 'Baik',
                tanah_batas: '',
                tanah_alamat: '',
                tanah_items: [
                    {
                        tanah_nama_barang: '',
                        tanah_luas_m2: null,
                        tanah_hak: 'Hak Pakai',
                        tanah_sertifikat_no: '',
                        tanah_sertifikat_tgl: '',
                        tanah_penggunaan: '',
                        tanah_kondisi: 'Baik',
                        tanah_batas: '',
                        tanah_alamat: '',
                        tanah_jumlah_barang: 1,
                        tanah_satuan: 'Bidang',
                        tanah_nilai_satuan: 0
                    }
                ],

                // Sheet KIB B: Peralatan & Mesin (Repeater Multi-Item)
                merk: '',
                type: '',
                no_pabrik: '',
                bahan: '',
                ukuran: '',
                tahun_pembuatan: null,
                no_rangka: '',
                no_mesin: '',
                no_bpkb: '',
                no_polisi: '',
                mesin_items: [
                    {
                        mesin_nama_barang: '',
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
                        mesin_satuan: 'Unit',
                        mesin_nilai_satuan: 0,
                        mesin_keterangan: '',
                        ruang_pemegang: '',
                        isRuangOpen: false,
                        searchRuang: ''
                    }
                ],

                // Sheet KIB C: Gedung & Bangunan (Multi-Item Repeater)
                gedung_bertingkat: 'Tidak',
                gedung_beton: 'Beton Bertulang',
                gedung_luas_lantai: null,
                gedung_kondisi: 'Baik',
                gedung_dokumen_no: '',
                gedung_dokumen_tgl: '',
                gedung_status_tanah: 'Tanah Milik RSUD',
                gedung_alamat: '',
                gedung_fungsi: '',
                gedung_items: [
                    {
                        gedung_nama_barang: '',
                        gedung_luas_lantai: null,
                        gedung_kondisi: 'Baik',
                        gedung_bertingkat: 'Tidak',
                        gedung_beton: 'Beton Bertulang',
                        gedung_status_tanah: 'Tanah Milik RSUD',
                        gedung_dokumen_no: '',
                        gedung_dokumen_tgl: '',
                        gedung_alamat: '',
                        gedung_fungsi: '',
                        gedung_jumlah_bangunan: 1,
                        gedung_satuan: 'Gedung',
                        gedung_nilai_satuan: 0,
                        ruang_pemegang: '',
                        isRuangOpen: false,
                        searchRuang: ''
                    }
                ],

                // Sheet KIB D: Jalan, Irigasi & Jaringan (Multi-Item Repeater)
                jaringan_konstruksi: '',
                jaringan_luas: null,
                jaringan_panjang: null,
                jaringan_lebar: null,
                jaringan_kondisi: 'Baik',
                jaringan_dokumen_no: '',
                jaringan_dokumen_tgl: '',
                jaringan_alamat: '',
                jaringan_items: [
                    {
                        jaringan_nama_barang: '',
                        jaringan_konstruksi: '',
                        jaringan_panjang: null,
                        jaringan_lebar: null,
                        jaringan_luas: null,
                        jaringan_bertingkat: 'Tidak',
                        jaringan_beton: 'Beton',
                        jaringan_kondisi: 'Baik',
                        jaringan_dokumen_no: '',
                        jaringan_dokumen_tgl: '',
                        jaringan_status_tanah: 'Tanah Hak Pakai RSUD',
                        jaringan_kode_aset_tanah: '',
                        jaringan_alamat: '',
                        jaringan_jumlah: 1,
                        jaringan_satuan: 'Ruas',
                        jaringan_nilai_satuan: 0,
                        jaringan_keterangan: '',
                        ruang_pemegang: '',
                        isRuangOpen: false,
                        searchRuang: ''
                    }
                ],

                // Sheet KIB E: Aset Tetap Lainnya (Multi-Item Repeater)
                kib_e_type: 'buku',
                lainnya_judul: '',
                lainnya_pencipta: '',
                lainnya_spesifikasi: '',
                lainnya_penerbit: '',
                lainnya_tahun: null,
                lainnya_asal_daerah: '',
                lainnya_jenis: '',
                lainnya_ukuran: '',
                lainnya_bahan: '',
                lainnya_asal: '',
                lainnya_items: [
                    {
                        kib_e_type: 'buku',
                        lainnya_nama_barang: '',
                        lainnya_judul: '',
                        lainnya_pencipta: '',
                        lainnya_spesifikasi: '',
                        lainnya_tahun: null,
                        lainnya_ukuran: '',
                        lainnya_asal_daerah: '',
                        lainnya_bahan: '',
                        lainnya_jenis: '',
                        lainnya_kondisi: 'Baik',
                        lainnya_jumlah: 1,
                        lainnya_satuan: 'Buah',
                        lainnya_nilai_satuan: 0,
                        lainnya_keterangan: '',
                        ruang_pemegang: '',
                        isRuangOpen: false,
                        searchRuang: ''
                    }
                ],

                // Spesifikasi JSON
                spesifikasi_json: {}
            },

            // Master Data & Filtering
            masterUnits: window.dbUnits || [],
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
                this.syncTotalsFromItems();

                this.$watch('formData.tanggal_pks', (newVal) => {
                    this.syncTahunTriwulanFromPks(newVal);
                });

                this.$watch('formData.skema_kemitraan', (newVal) => {
                    this.syncCascadingToActiveSkema();
                });

                // Sinkronisasi otomatis setiap kali kategori objek (KIB / 108) berubah
                this.$watch('selectedObjekType', () => {
                    this.$nextTick(() => {
                        this.syncTotalsFromItems();
                    });
                });

                this.$watch('selectedSubSub', () => {
                    this.$nextTick(() => {
                        this.syncTotalsFromItems();
                    });
                });

                this.$watch('formData.mesin_items', () => {
                    this.syncTotalsFromItems();
                }, { deep: true });

                this.$watch('formData.tanah_items', () => {
                    this.syncTotalsFromItems();
                }, { deep: true });

                this.$watch('formData.gedung_items', () => {
                    this.syncTotalsFromItems();
                }, { deep: true });

                this.$watch('formData.jaringan_items', () => {
                    this.syncTotalsFromItems();
                }, { deep: true });

                this.$watch('formData.lainnya_items', () => {
                    this.syncTotalsFromItems();
                }, { deep: true });

                // Validasi relasi Tanggal Mulai dan Tanggal Selesai Kerjasama
                this.$watch('formData.tanggal_mulai', (newVal) => {
                    if (newVal && this.formData.tanggal_selesai) {
                        const tMulai = this.parseDateToTimestamp(newVal);
                        const tSelesai = this.parseDateToTimestamp(this.formData.tanggal_selesai);
                        if (tMulai > 0 && tSelesai > 0 && tSelesai < tMulai) {
                            this.formData.tanggal_selesai = '';
                            this.showToast('Penyesuaian Tanggal', 'Tanggal berakhir dikosongkan karena tidak boleh lebih awal dari tanggal mulai kerjasama.', 'warning');
                        }
                    }
                });

                this.$watch('formData.tanggal_selesai', (newVal) => {
                    if (newVal && this.formData.tanggal_mulai) {
                        const tMulai = this.parseDateToTimestamp(this.formData.tanggal_mulai);
                        const tSelesai = this.parseDateToTimestamp(newVal);
                        if (tMulai > 0 && tSelesai > 0 && tSelesai < tMulai) {
                            this.formData.tanggal_selesai = '';
                            this.showToast('Tanggal Tidak Valid', 'Tanggal berakhir kerjasama tidak boleh di bawah (lebih awal dari) tanggal mulai kerjasama.', 'error');
                        }
                    }
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

            getSimulatedNibar(idx = 0) {
                const tahun = this.formData.tahun_perolehan || '{{ date('Y') }}';
                const rawKode = this.selectedKode108 || '1.5.2.01.01.01.001';
                const kodeClean = rawKode.replace(/\./g, '');
                const seq = String(idx + 1).padStart(7, '0');
                return `1201351102000000280000${tahun}${kodeClean}${seq}`;
            },

            get simulatedNibar() {
                return this.getSimulatedNibar(0);
            },

            // Getter: Mengecek apakah sheet aktif mendukung multi-item repeater
            get isMultiItemActive() {
                return this.isMesin || this.isTanah || this.isGedung || this.isJaringan || this.isLainnya;
            },

            // Getter: Daftar simulasi NIBAR untuk seluruh unit register aset
            get simulatedRegistersList() {
                const list = [];
                let runningOffset = 0;
                if (this.isMesin && this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    this.formData.mesin_items.forEach((m, mIdx) => {
                        const qty = Math.max(1, parseInt(m.mesin_jumlah_barang) || 1);
                        for (let q = 0; q < qty; q++) {
                            const subUnitNo = qty > 1 ? ` (Unit #${q + 1})` : '';
                            list.push({
                                unitNo: runningOffset + 1,
                                itemIndex: mIdx,
                                itemName: (m.mesin_nama_barang || this.formData.nama_barang || `Barang #${mIdx + 1}`) + subUnitNo,
                                spec: [m.mesin_merk, m.mesin_type, m.mesin_no_pabrik ? ('SN: ' + m.mesin_no_pabrik) : ''].filter(Boolean).join(' • ') || 'Spesifikasi Standar',
                                kondisi: m.mesin_kondisi || 'Baik',
                                nibar: this.getSimulatedNibar(runningOffset)
                            });
                            runningOffset++;
                        }
                    });
                } else if (this.isTanah && this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    this.formData.tanah_items.forEach((t, tIdx) => {
                        const qty = Math.max(1, parseInt(t.tanah_jumlah_barang) || 1);
                        for (let q = 0; q < qty; q++) {
                            list.push({
                                unitNo: runningOffset + 1,
                                itemIndex: tIdx,
                                itemName: (t.tanah_nama_barang || this.formData.nama_barang || 'Tanah') + ` (Bidang #${tIdx + 1})`,
                                spec: (t.tanah_luas_m2 ? t.tanah_luas_m2 + ' m²' : '') + (t.tanah_hak ? ' • ' + t.tanah_hak : '') + (t.tanah_sertifikat_no ? ' • Sert: ' + t.tanah_sertifikat_no : ''),
                                kondisi: t.tanah_kondisi || 'Baik',
                                nibar: this.getSimulatedNibar(runningOffset)
                            });
                            runningOffset++;
                        }
                    });
                } else if (this.isGedung && this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    this.formData.gedung_items.forEach((g, gIdx) => {
                        const qty = Math.max(1, parseInt(g.gedung_jumlah_bangunan) || 1);
                        for (let q = 0; q < qty; q++) {
                            const subGedungNo = qty > 1 ? ` (Bangunan #${q + 1})` : '';
                            list.push({
                                unitNo: runningOffset + 1,
                                itemIndex: gIdx,
                                itemName: (g.gedung_nama_barang || this.formData.nama_barang || `Gedung #${gIdx + 1}`) + subGedungNo,
                                spec: [g.gedung_luas_lantai ? (g.gedung_luas_lantai + ' m²') : '', g.gedung_bertingkat === 'Bertingkat' ? 'Bertingkat' : '', g.gedung_beton].filter(Boolean).join(' • ') || 'Konstruksi Gedung',
                                kondisi: g.gedung_kondisi || 'Baik',
                                nibar: this.getSimulatedNibar(runningOffset)
                            });
                            runningOffset++;
                        }
                    });
                } else if (this.isJaringan && this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    this.formData.jaringan_items.forEach((j, jIdx) => {
                        const qty = Math.max(1, parseInt(j.jaringan_jumlah) || 1);
                        for (let q = 0; q < qty; q++) {
                            const subJarNo = qty > 1 ? ` (Ruas #${q + 1})` : '';
                            list.push({
                                unitNo: runningOffset + 1,
                                itemIndex: jIdx,
                                itemName: (j.jaringan_nama_barang || this.formData.nama_barang || `Ruas Jaringan #${jIdx + 1}`) + subJarNo,
                                spec: [j.jaringan_konstruksi, j.jaringan_luas ? (j.jaringan_luas + ' m²') : (j.jaringan_panjang ? (j.jaringan_panjang + ' M') : '')].filter(Boolean).join(' • ') || 'Fisik Jaringan',
                                kondisi: j.jaringan_kondisi || 'Baik',
                                nibar: this.getSimulatedNibar(runningOffset)
                            });
                            runningOffset++;
                        }
                    });
                } else if (this.isLainnya && this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                    this.formData.lainnya_items.forEach((l, lIdx) => {
                        const qty = Math.max(1, parseInt(l.lainnya_jumlah) || 1);
                        for (let q = 0; q < qty; q++) {
                            const subLainNo = qty > 1 ? ` #${q + 1}` : '';
                            const catLabel = l.kib_e_type === 'buku' ? 'Buku' : (l.kib_e_type === 'kesenian' ? 'Seni' : 'Tanaman/Hewan');
                            list.push({
                                unitNo: runningOffset + 1,
                                itemIndex: lIdx,
                                itemName: (l.lainnya_judul || this.formData.nama_barang || `Item Aset Lainnya #${lIdx + 1}`) + subLainNo,
                                spec: [catLabel, l.lainnya_pencipta || l.lainnya_spesifikasi || l.lainnya_bahan].filter(Boolean).join(' • ') || 'Aset Tetap Lainnya',
                                kondisi: l.lainnya_kondisi || 'Baik',
                                nibar: this.getSimulatedNibar(runningOffset)
                            });
                            runningOffset++;
                        }
                    });
                } else {
                    const totalVol = Math.max(1, parseInt(this.formData.jumlah_volume) || 1);
                    for (let q = 0; q < totalVol; q++) {
                        list.push({
                            unitNo: q + 1,
                            itemIndex: 0,
                            itemName: (this.formData.nama_barang || 'Unit') + (totalVol > 1 ? ` #${q + 1}` : ''),
                            spec: this.formData.satuan || 'Unit',
                            kondisi: this.formData.kondisi || 'Baik',
                            nibar: this.getSimulatedNibar(q)
                        });
                    }
                }
                return list;
            },

            // --- MULTI-ITEM REPEATER PERALATAN & MESIN ---
            addMesinItem() {
                if (!this.formData.mesin_items) {
                    this.formData.mesin_items = [];
                }
                const defaultNama = this.selectedSubSub ? this.selectedSubSub.nama : (this.formData.nama_barang || '');
                this.formData.mesin_items.push({
                    mesin_nama_barang: defaultNama,
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
                    mesin_satuan: 'Unit',
                    mesin_nilai_satuan: 0,
                    mesin_keterangan: '',
                    ruang_pemegang: '',
                    isRuangOpen: false,
                    searchRuang: ''
                });
                this.syncTotalsFromItems();
            },

            removeMesinItem(idx) {
                if (this.formData.mesin_items && this.formData.mesin_items.length > 1) {
                    this.formData.mesin_items.splice(idx, 1);
                    this.syncTotalsFromItems();
                }
            },

            getMesinSubtotal(item) {
                return (Number(item.mesin_jumlah_barang || 1) * Number(item.mesin_nilai_satuan || 0));
            },

            get totalNilaiMesin() {
                if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    return this.formData.mesin_items.reduce((sum, item) => sum + this.getMesinSubtotal(item), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            get totalVolumeMesin() {
                if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    return this.formData.mesin_items.reduce((sum, item) => sum + (parseInt(item.mesin_jumlah_barang) || 1), 0);
                }
                return parseInt(this.formData.jumlah_volume) || 1;
            },

            // --- MULTI-ITEM REPEATER TANAH ---
            addTanahItem() {
                if (!this.formData.tanah_items) {
                    this.formData.tanah_items = [];
                }
                this.formData.tanah_items.push({
                    tanah_nama_barang: this.selectedSubSub ? this.selectedSubSub.nama : (this.formData.nama_barang || 'Bidang Tanah'),
                    tanah_luas_m2: null,
                    tanah_hak: 'Hak Pakai',
                    tanah_sertifikat_no: '',
                    tanah_sertifikat_tgl: '',
                    tanah_penggunaan: '',
                    tanah_kondisi: 'Baik',
                    tanah_batas: '',
                    tanah_alamat: '',
                    tanah_jumlah_barang: 1,
                    tanah_satuan: 'Bidang',
                    tanah_nilai_satuan: 0
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
                // Untuk aset tanah (KIB A), taksiran nilai wajar adalah nilai keseluruhan per bidang (lump-sum)
                return Number(item.tanah_nilai_satuan || 0);
            },

            get totalNilaiTanah() {
                if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    return this.formData.tanah_items.reduce((sum, item) => sum + this.getTanahSubtotal(item), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            get totalVolumeTanah() {
                if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    return this.formData.tanah_items.reduce((sum, item) => sum + (parseInt(item.tanah_jumlah_barang) || 1), 0);
                }
                return parseInt(this.formData.jumlah_volume) || 1;
            },

            // --- MULTI-ITEM REPEATER GEDUNG & BANGUNAN ---
            addGedungItem() {
                if (!this.formData.gedung_items) {
                    this.formData.gedung_items = [];
                }
                this.formData.gedung_items.push({
                    gedung_nama_barang: this.selectedSubSub ? this.selectedSubSub.nama : (this.formData.nama_barang || 'Bangunan Gedung'),
                    gedung_luas_lantai: null,
                    gedung_kondisi: 'Baik',
                    gedung_bertingkat: 'Tidak',
                    gedung_beton: 'Beton Bertulang',
                    gedung_status_tanah: 'Tanah Milik RSUD',
                    gedung_dokumen_no: '',
                    gedung_dokumen_tgl: '',
                    gedung_alamat: '',
                    gedung_fungsi: '',
                    gedung_jumlah_bangunan: 1,
                    gedung_satuan: 'Gedung',
                    gedung_nilai_satuan: 0,
                    ruang_pemegang: '',
                    isRuangOpen: false,
                    searchRuang: ''
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
                // Untuk aset gedung & bangunan (KIB C), taksiran nilai wajar adalah nilai keseluruhan per bangunan (lump-sum)
                return Number(item.gedung_nilai_satuan || 0);
            },

            get totalNilaiGedung() {
                if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    return this.formData.gedung_items.reduce((sum, item) => sum + this.getGedungSubtotal(item), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            get totalVolumeGedung() {
                if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    return this.formData.gedung_items.reduce((sum, item) => sum + (parseInt(item.gedung_jumlah_bangunan) || 1), 0);
                }
                return parseInt(this.formData.jumlah_volume) || 1;
            },

            // --- MULTI-ITEM REPEATER JALAN, IRIGASI & JARINGAN ---
            addJaringanItem() {
                if (!this.formData.jaringan_items) {
                    this.formData.jaringan_items = [];
                }
                this.formData.jaringan_items.push({
                    jaringan_nama_barang: this.selectedSubSub ? this.selectedSubSub.nama : (this.formData.nama_barang || 'Ruas Jaringan'),
                    jaringan_konstruksi: '',
                    jaringan_panjang: null,
                    jaringan_lebar: null,
                    jaringan_luas: null,
                    jaringan_bertingkat: 'Tidak',
                    jaringan_beton: 'Beton',
                    jaringan_kondisi: 'Baik',
                    jaringan_dokumen_no: '',
                    jaringan_dokumen_tgl: '',
                    jaringan_status_tanah: 'Tanah Hak Pakai RSUD',
                    jaringan_kode_aset_tanah: '',
                    jaringan_alamat: '',
                    jaringan_jumlah: 1,
                    jaringan_satuan: 'Ruas',
                    jaringan_nilai_satuan: 0,
                    jaringan_keterangan: '',
                    ruang_pemegang: '',
                    isRuangOpen: false,
                    searchRuang: ''
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
                // Untuk aset jalan, irigasi & jaringan (KIB D), taksiran nilai wajar adalah nilai keseluruhan per ruas/jaringan (lump-sum)
                return Number(item.jaringan_nilai_satuan || 0);
            },

            get totalNilaiJaringan() {
                if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    return this.formData.jaringan_items.reduce((sum, item) => sum + this.getJaringanSubtotal(item), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            get totalVolumeJaringan() {
                if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    return this.formData.jaringan_items.reduce((sum, item) => sum + (parseInt(item.jaringan_jumlah) || 1), 0);
                }
                return parseInt(this.formData.jumlah_volume) || 1;
            },

            // --- MULTI-ITEM REPEATER ASET TETAP LAINNYA ---
            addLainnyaItem() {
                if (!this.formData.lainnya_items) {
                    this.formData.lainnya_items = [];
                }
                this.formData.lainnya_items.push({
                    kib_e_type: 'buku',
                    lainnya_nama_barang: this.selectedSubSub ? this.selectedSubSub.nama : (this.formData.nama_barang || 'Item Aset Lainnya'),
                    lainnya_judul: '',
                    lainnya_pencipta: '',
                    lainnya_spesifikasi: '',
                    lainnya_tahun: null,
                    lainnya_ukuran: '',
                    lainnya_asal_daerah: '',
                    lainnya_bahan: '',
                    lainnya_jenis: '',
                    lainnya_kondisi: 'Baik',
                    lainnya_jumlah: 1,
                    lainnya_satuan: 'Buah',
                    lainnya_nilai_satuan: 0,
                    lainnya_keterangan: '',
                    ruang_pemegang: '',
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
                return (Number(item.lainnya_jumlah || 1) * Number(item.lainnya_nilai_satuan || 0));
            },

            // Helper Pencarian & Pemilihan Unit/Ruangan Penempatan
            filterUnitsForItem(item) {
                let list = this.masterUnits || [];
                if (!item.searchRuang || item.searchRuang.trim() === '') return list;
                const q = item.searchRuang.toLowerCase().trim();
                return list.filter(u => (u.nama || '').toLowerCase().includes(q) || (u.kode || '').toLowerCase().includes(q) || (u.tipe || '').toLowerCase().includes(q));
            },

            selectUnitForItem(item, unit) {
                item.ruang_pemegang = unit.nama;
                item.isRuangOpen = false;
                item.searchRuang = '';
                this.syncTotalsFromItems();
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

            // Sinkronisasi total volume, realisasi, dan nama dari repeater ke formData utama
            syncTotalsFromItems() {
                if (this.isMesin) {
                    if (!this.formData.mesin_items || this.formData.mesin_items.length === 0) return;
                    this.formData.jumlah_volume = this.totalVolumeMesin;
                    this.formData.total_realisasi = this.totalNilaiMesin;
                    
                    const first = this.formData.mesin_items[0];
                    if (this.formData.mesin_items.length === 1) {
                        if (first.mesin_nama_barang) {
                            this.formData.nama_barang = first.mesin_nama_barang;
                        } else if (this.selectedSubSub?.nama) {
                            this.formData.nama_barang = this.selectedSubSub.nama;
                        }
                        this.formData.satuan = first.mesin_satuan || 'Unit';
                    } else {
                        const names = this.formData.mesin_items.map(m => m.mesin_nama_barang || (m.mesin_merk ? m.mesin_merk + ' ' + m.mesin_type : '')).filter(Boolean);
                        if (names.length > 0) {
                            this.formData.nama_barang = names.join(', ');
                        }
                        this.formData.satuan = 'Unit';
                    }

                    if (first) {
                        this.formData.merk = first.mesin_merk || '';
                        this.formData.type = first.mesin_type || '';
                        this.formData.no_pabrik = first.mesin_no_pabrik || '';
                        this.formData.bahan = first.mesin_bahan || '';
                        this.formData.ukuran = first.mesin_ukuran || '';
                        this.formData.tahun_pembuatan = first.mesin_tahun_pembuatan || null;
                        this.formData.kondisi = first.mesin_kondisi || 'Baik';
                        this.formData.no_rangka = first.mesin_no_rangka || '';
                        this.formData.no_mesin = first.mesin_no_mesin || '';
                        this.formData.no_bpkb = first.mesin_no_bpkb || '';
                        this.formData.no_polisi = first.mesin_no_polisi || '';
                    }
                } else if (this.isTanah) {
                    if (!this.formData.tanah_items || this.formData.tanah_items.length === 0) return;
                    this.formData.jumlah_volume = this.totalVolumeTanah;
                    this.formData.total_realisasi = this.totalNilaiTanah;
                    const first = this.formData.tanah_items[0];
                    if (this.formData.tanah_items.length === 1) {
                        if (first.tanah_nama_barang) {
                            this.formData.nama_barang = first.tanah_nama_barang;
                        } else if (this.selectedSubSub?.nama) {
                            this.formData.nama_barang = this.selectedSubSub.nama;
                        }
                        this.formData.satuan = first.tanah_satuan || 'Bidang';
                    } else {
                        const names = this.formData.tanah_items.map(t => t.tanah_nama_barang).filter(Boolean);
                        if (names.length > 0) this.formData.nama_barang = names.join(', ');
                        this.formData.satuan = 'Bidang';
                    }
                    if (first) {
                        this.formData.tanah_luas_m2 = first.tanah_luas_m2;
                        this.formData.tanah_hak = first.tanah_hak;
                        this.formData.tanah_sertifikat_no = first.tanah_sertifikat_no;
                        this.formData.tanah_sertifikat_tgl = first.tanah_sertifikat_tgl;
                        this.formData.tanah_penggunaan = first.tanah_penggunaan;
                        this.formData.tanah_kondisi = first.tanah_kondisi;
                        this.formData.tanah_batas = first.tanah_batas;
                        this.formData.tanah_alamat = first.tanah_alamat;
                        this.formData.kondisi = first.tanah_kondisi;
                    }
                } else if (this.isGedung) {
                    if (!this.formData.gedung_items || this.formData.gedung_items.length === 0) return;
                    this.formData.jumlah_volume = this.totalVolumeGedung;
                    this.formData.total_realisasi = this.totalNilaiGedung;
                    const first = this.formData.gedung_items[0];
                    if (this.formData.gedung_items.length === 1) {
                        if (first.gedung_nama_barang) {
                            this.formData.nama_barang = first.gedung_nama_barang;
                        } else if (this.selectedSubSub?.nama) {
                            this.formData.nama_barang = this.selectedSubSub.nama;
                        }
                        this.formData.satuan = first.gedung_satuan || 'Gedung';
                    } else {
                        const names = this.formData.gedung_items.map(g => g.gedung_nama_barang).filter(Boolean);
                        if (names.length > 0) this.formData.nama_barang = names.join(', ');
                        this.formData.satuan = 'Gedung';
                    }
                    if (first) {
                        this.formData.gedung_luas_lantai = first.gedung_luas_lantai;
                        this.formData.gedung_kondisi = first.gedung_kondisi;
                        this.formData.gedung_bertingkat = first.gedung_bertingkat;
                        this.formData.gedung_beton = first.gedung_beton;
                        this.formData.gedung_status_tanah = first.gedung_status_tanah;
                        this.formData.gedung_dokumen_no = first.gedung_dokumen_no;
                        this.formData.gedung_dokumen_tgl = first.gedung_dokumen_tgl;
                        this.formData.gedung_fungsi = first.gedung_fungsi;
                        this.formData.kondisi = first.gedung_kondisi;
                    }
                } else if (this.isJaringan) {
                    if (!this.formData.jaringan_items || this.formData.jaringan_items.length === 0) return;
                    this.formData.jumlah_volume = this.totalVolumeJaringan;
                    this.formData.total_realisasi = this.totalNilaiJaringan;
                    const first = this.formData.jaringan_items[0];
                    if (this.formData.jaringan_items.length === 1) {
                        if (first.jaringan_nama_barang) {
                            this.formData.nama_barang = first.jaringan_nama_barang;
                        } else if (this.selectedSubSub?.nama) {
                            this.formData.nama_barang = this.selectedSubSub.nama;
                        }
                        this.formData.satuan = first.jaringan_satuan || 'Ruas';
                    } else {
                        const names = this.formData.jaringan_items.map(j => j.jaringan_nama_barang).filter(Boolean);
                        if (names.length > 0) this.formData.nama_barang = names.join(', ');
                        this.formData.satuan = 'Ruas';
                    }
                    if (first) {
                        this.formData.jaringan_konstruksi = first.jaringan_konstruksi;
                        this.formData.jaringan_panjang = first.jaringan_panjang;
                        this.formData.jaringan_lebar = first.jaringan_lebar;
                        this.formData.jaringan_luas = first.jaringan_luas;
                        this.formData.jaringan_kondisi = first.jaringan_kondisi;
                        this.formData.jaringan_dokumen_no = first.jaringan_dokumen_no;
                        this.formData.jaringan_dokumen_tgl = first.jaringan_dokumen_tgl;
                        this.formData.kondisi = first.jaringan_kondisi;
                    }
                } else if (this.isLainnya) {
                    if (!this.formData.lainnya_items || this.formData.lainnya_items.length === 0) return;
                    this.formData.jumlah_volume = this.totalVolumeLainnya;
                    this.formData.total_realisasi = this.totalNilaiLainnya;
                    const first = this.formData.lainnya_items[0];
                    if (this.formData.lainnya_items.length === 1) {
                        if (first.lainnya_judul) {
                            this.formData.nama_barang = first.lainnya_judul;
                        } else if (this.selectedSubSub?.nama) {
                            this.formData.nama_barang = this.selectedSubSub.nama;
                        }
                        this.formData.satuan = first.lainnya_satuan || 'Buah';
                    } else {
                        const names = this.formData.lainnya_items.map(l => l.lainnya_judul).filter(Boolean);
                        if (names.length > 0) this.formData.nama_barang = names.join(', ');
                        this.formData.satuan = 'Unit';
                    }
                    if (first) {
                        this.formData.kib_e_type = first.kib_e_type;
                        this.formData.lainnya_judul = first.lainnya_judul;
                        this.formData.lainnya_pencipta = first.lainnya_pencipta;
                        this.formData.lainnya_spesifikasi = first.lainnya_spesifikasi;
                        this.formData.lainnya_tahun = first.lainnya_tahun;
                        this.formData.lainnya_ukuran = first.lainnya_ukuran;
                        this.formData.lainnya_asal_daerah = first.lainnya_asal_daerah;
                        this.formData.lainnya_bahan = first.lainnya_bahan;
                        this.formData.lainnya_kondisi = first.lainnya_kondisi;
                        this.formData.kondisi = first.lainnya_kondisi;
                    }
                }
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
                const prevNama = this.formData.nama_barang || '';
                this.selectedSubSub = item;
                this.formData.jenis_astap_id = item.id;
                this.formData.nama_barang = item.nama;

                const kode = item.kode || '';
                const nama = (item.nama || '').toLowerCase();

                // Deteksi tipe objek yang baru dipilih (tanah, mesin, gedung, jaringan, lainnya)
                let targetType = 'mesin';
                if (kode.endsWith('.001') || kode.startsWith('1.3.1') || nama.includes('tanah') || nama.includes('lahan') || nama.includes('kavling')) {
                    targetType = 'tanah';
                } else if (kode.endsWith('.002') || kode.startsWith('1.3.2') || nama.includes('peralatan') || nama.includes('mesin') || nama.includes('alat') || nama.includes('medis')) {
                    targetType = 'mesin';
                } else if (kode.endsWith('.003') || kode.startsWith('1.3.3') || nama.includes('gedung') || nama.includes('bangunan') || nama.includes('ruang') || nama.includes('rumah')) {
                    targetType = 'gedung';
                } else if (kode.endsWith('.004') || kode.startsWith('1.3.4') || nama.includes('jalan') || nama.includes('irigasi') || nama.includes('jaringan') || nama.includes('pipa')) {
                    targetType = 'jaringan';
                } else if (kode.endsWith('.005') || kode.startsWith('1.3.5') || nama.includes('lainnya') || nama.includes('buku') || nama.includes('hewan')) {
                    targetType = 'lainnya';
                }

                // Helper pengecekan: apakah nama yang ada sekarang adalah nama bawaan/generic sistem
                const isGenericOrEmpty = (val) => {
                    if (!val) return true;
                    val = String(val).trim();
                    if (val === '' || val === prevNama) return true;
                    return /^(Sewa|Kerja Sama|Bangun|Penyediaan|KSO|KSP|BGS|BSG|KSPI)/i.test(val);
                };

                // Sinkronkan nama spesifik pada sheet yang aktif sesuai targetType
                if (targetType === 'tanah' && this.formData.tanah_items) {
                    this.formData.tanah_items.forEach(t => {
                        if (isGenericOrEmpty(t.tanah_nama_barang)) {
                            t.tanah_nama_barang = item.nama;
                        }
                    });
                } else if (targetType === 'mesin' && this.formData.mesin_items) {
                    this.formData.mesin_items.forEach(m => {
                        if (isGenericOrEmpty(m.mesin_nama_barang)) {
                            m.mesin_nama_barang = item.nama;
                        }
                    });
                } else if (targetType === 'gedung' && this.formData.gedung_items) {
                    this.formData.gedung_items.forEach(g => {
                        if (isGenericOrEmpty(g.gedung_nama_barang)) {
                            g.gedung_nama_barang = item.nama;
                        }
                    });
                } else if (targetType === 'jaringan' && this.formData.jaringan_items) {
                    this.formData.jaringan_items.forEach(j => {
                        if (isGenericOrEmpty(j.jaringan_nama_barang)) {
                            j.jaringan_nama_barang = item.nama;
                        }
                    });
                } else if (targetType === 'lainnya' && this.formData.lainnya_items) {
                    this.formData.lainnya_items.forEach(l => {
                        if (isGenericOrEmpty(l.lainnya_judul)) {
                            l.lainnya_judul = item.nama;
                        }
                    });
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

                // Sinkronkan langsung total volume, nilai wajar, dan satuan dari sheet aktif
                this.syncTotalsFromItems();
                this.$nextTick(() => {
                    this.syncTotalsFromItems();
                });

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
                    if (!this.selectedSubSub && this.currentSubSubRecommendations && this.currentSubSubRecommendations.length > 0) {
                        this.selectSubSubItem(this.currentSubSubRecommendations[0]);
                    }
                }
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            nextStep() {
                if (this.validateStep(this.currentStep)) {
                    this.currentStep++;
                    if (this.currentStep === 2) {
                        this.syncCascadingToActiveSkema();
                        if (!this.selectedSubSub && this.currentSubSubRecommendations && this.currentSubSubRecommendations.length > 0) {
                            this.selectSubSubItem(this.currentSubSubRecommendations[0]);
                        }
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
                    if (this.formData.tanggal_mulai && this.formData.tanggal_selesai) {
                        const tMulai = this.parseDateToTimestamp(this.formData.tanggal_mulai);
                        const tSelesai = this.parseDateToTimestamp(this.formData.tanggal_selesai);
                        if (tMulai > 0 && tSelesai > 0 && tSelesai < tMulai) {
                            this.showToast('Validasi Gagal', 'Tanggal berakhir kerjasama tidak boleh di bawah (lebih awal dari) tanggal mulai kerjasama.', 'error');
                            return false;
                        }
                    }
                } else if (s === 2) {
                    if (!this.formData.jenis_astap_id) {
                        this.showToast('Validasi Gagal', 'Mohon pilih klasifikasi kode barang 108 (rekomendasi Akun 1.5.2 Kemitraan).', 'error');
                        return false;
                    }

                    // Validasi khusus repeater Peralatan & Mesin (KIB B)
                    if (this.isMesin) {
                        this.syncTotalsFromItems();
                        if (!this.formData.mesin_items || this.formData.mesin_items.length === 0) {
                            this.showToast('Validasi Gagal', 'Mohon tambahkan minimal 1 item barang / unit pada rincian mesin.', 'error');
                            return false;
                        }
                        for (let i = 0; i < this.formData.mesin_items.length; i++) {
                            const it = this.formData.mesin_items[i];
                            const num = i + 1;
                            if (!it.mesin_nama_barang || !it.mesin_nama_barang.trim()) {
                                this.showToast('Validasi Gagal', `Nama Barang / Unit #${num} tidak boleh kosong.`, 'error');
                                return false;
                            }
                            if (!it.mesin_jumlah_barang || parseInt(it.mesin_jumlah_barang) < 1) {
                                this.showToast('Validasi Gagal', `Jumlah volume pada Barang #${num} minimal 1 unit.`, 'error');
                                return false;
                            }
                            if (parseFloat(it.mesin_nilai_satuan) <= 0 || isNaN(parseFloat(it.mesin_nilai_satuan))) {
                                this.showToast('Validasi Gagal', `Taksiran nilai satuan pada Barang #${num} harus lebih dari 0.`, 'error');
                                return false;
                            }
                        }
                    } else if (this.isTanah) {
                        this.syncTotalsFromItems();
                        if (!this.formData.tanah_items || this.formData.tanah_items.length === 0) {
                            this.showToast('Validasi Gagal', 'Mohon tambahkan minimal 1 bidang tanah pada rincian tanah.', 'error');
                            return false;
                        }
                        for (let i = 0; i < this.formData.tanah_items.length; i++) {
                            const it = this.formData.tanah_items[i];
                            const num = i + 1;
                            if (!it.tanah_luas_m2 || parseFloat(it.tanah_luas_m2) <= 0) {
                                this.showToast('Validasi Gagal', `Luas tanah (m²) pada Bidang #${num} harus lebih dari 0.`, 'error');
                                return false;
                            }
                        }
                    } else if (this.isGedung) {
                        this.syncTotalsFromItems();
                        if (!this.formData.gedung_items || this.formData.gedung_items.length === 0) {
                            this.showToast('Validasi Gagal', 'Mohon tambahkan minimal 1 bangunan gedung pada rincian gedung.', 'error');
                            return false;
                        }
                        for (let i = 0; i < this.formData.gedung_items.length; i++) {
                            const it = this.formData.gedung_items[i];
                            const num = i + 1;
                            if (!it.gedung_nama_barang || !it.gedung_nama_barang.trim()) {
                                this.showToast('Validasi Gagal', `Nama Bangunan #${num} tidak boleh kosong.`, 'error');
                                return false;
                            }
                            if (!it.gedung_jumlah_bangunan || parseInt(it.gedung_jumlah_bangunan) < 1) {
                                this.showToast('Validasi Gagal', `Jumlah unit pada Bangunan #${num} minimal 1.`, 'error');
                                return false;
                            }
                        }
                    } else if (this.isJaringan) {
                        this.syncTotalsFromItems();
                        if (!this.formData.jaringan_items || this.formData.jaringan_items.length === 0) {
                            this.showToast('Validasi Gagal', 'Mohon tambahkan minimal 1 ruas pada rincian jalan & jaringan.', 'error');
                            return false;
                        }
                        for (let i = 0; i < this.formData.jaringan_items.length; i++) {
                            const it = this.formData.jaringan_items[i];
                            const num = i + 1;
                            if (!it.jaringan_nama_barang || !it.jaringan_nama_barang.trim()) {
                                this.showToast('Validasi Gagal', `Nama Ruas #${num} tidak boleh kosong.`, 'error');
                                return false;
                            }
                            if (!it.jaringan_jumlah || parseInt(it.jaringan_jumlah) < 1) {
                                this.showToast('Validasi Gagal', `Jumlah volume pada Ruas #${num} minimal 1.`, 'error');
                                return false;
                            }
                        }
                    } else if (this.isLainnya) {
                        this.syncTotalsFromItems();
                        if (!this.formData.lainnya_items || this.formData.lainnya_items.length === 0) {
                            this.showToast('Validasi Gagal', 'Mohon tambahkan minimal 1 item pada rincian aset tetap lainnya.', 'error');
                            return false;
                        }
                        for (let i = 0; i < this.formData.lainnya_items.length; i++) {
                            const it = this.formData.lainnya_items[i];
                            const num = i + 1;
                            if (!it.lainnya_judul || !it.lainnya_judul.trim()) {
                                this.showToast('Validasi Gagal', `Judul / Nama pada Item #${num} tidak boleh kosong.`, 'error');
                                return false;
                            }
                            if (!it.lainnya_jumlah || parseInt(it.lainnya_jumlah) < 1) {
                                this.showToast('Validasi Gagal', `Jumlah volume pada Item #${num} minimal 1.`, 'error');
                                return false;
                            }
                        }
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

                this.syncTotalsFromItems();

                // Siapkan payload spesifikasi JSON terstruktur sesuai sheet KIB yang aktif
                let specJson = {};

                if (this.isTanah) {
                    const firstT = (this.formData.tanah_items && this.formData.tanah_items[0]) ? this.formData.tanah_items[0] : {};
                    const totalLuas = this.formData.tanah_items ? this.formData.tanah_items.reduce((s, it) => s + (parseFloat(it.tanah_luas_m2) || 0), 0) : (parseFloat(this.formData.tanah_luas_m2) || 0);

                    specJson = {
                        kategori_kib: 'KIB A (Tanah)',
                        luas_m2: totalLuas,
                        hak_tanah: firstT.tanah_hak || this.formData.tanah_hak || 'Hak Pakai',
                        sertifikat_no: firstT.tanah_sertifikat_no || this.formData.tanah_sertifikat_no || '',
                        sertifikat_tgl: firstT.tanah_sertifikat_tgl || this.formData.tanah_sertifikat_tgl || '',
                        kondisi: firstT.tanah_kondisi || this.formData.tanah_kondisi || this.formData.kondisi || 'Baik',
                        penggunaan: firstT.tanah_penggunaan || this.formData.tanah_penggunaan || '',
                        batas_wilayah: firstT.tanah_batas || this.formData.tanah_batas || '',
                        alamat_lahan: firstT.tanah_alamat || this.formData.tanah_alamat || this.formData.alamat_barang || '',
                        tanah_items: this.formData.tanah_items
                    };
                } else if (this.isMesin) {
                    const firstM = (this.formData.mesin_items && this.formData.mesin_items[0]) ? this.formData.mesin_items[0] : {};

                    specJson = {
                        kategori_kib: 'KIB B (Peralatan & Mesin)',
                        merk: firstM.mesin_merk || this.formData.merk || '',
                        type: firstM.mesin_type || this.formData.type || '',
                        no_pabrik: firstM.mesin_no_pabrik || this.formData.no_pabrik || '',
                        bahan: firstM.mesin_bahan || this.formData.bahan || '',
                        ukuran: firstM.mesin_ukuran || this.formData.ukuran || '',
                        tahun_pembuatan: firstM.mesin_tahun_pembuatan || this.formData.tahun_pembuatan || null,
                        kondisi: firstM.mesin_kondisi || this.formData.kondisi || 'Baik',
                        no_rangka: firstM.mesin_no_rangka || this.formData.no_rangka || '',
                        no_mesin: firstM.mesin_no_mesin || this.formData.no_mesin || '',
                        no_bpkb: firstM.mesin_no_bpkb || this.formData.no_bpkb || '',
                        no_polisi: firstM.mesin_no_polisi || this.formData.no_polisi || '',
                        keterangan: firstM.mesin_keterangan || '',
                        mesin_items: this.formData.mesin_items
                    };
                } else if (this.isGedung) {
                    const firstG = (this.formData.gedung_items && this.formData.gedung_items[0]) ? this.formData.gedung_items[0] : {};
                    const totalLuasLantai = this.formData.gedung_items ? this.formData.gedung_items.reduce((s, it) => s + (parseFloat(it.gedung_luas_lantai) || 0), 0) : (parseFloat(this.formData.gedung_luas_lantai) || 0);

                    specJson = {
                        kategori_kib: 'KIB C (Gedung & Bangunan)',
                        bertingkat: firstG.gedung_bertingkat || this.formData.gedung_bertingkat || 'Tidak',
                        beton: firstG.gedung_beton || this.formData.gedung_beton || 'Beton Bertulang',
                        luas_lantai_m2: totalLuasLantai,
                        kondisi: firstG.gedung_kondisi || this.formData.gedung_kondisi || this.formData.kondisi || 'Baik',
                        dokumen_no: firstG.gedung_dokumen_no || this.formData.gedung_dokumen_no || '',
                        dokumen_tgl: firstG.gedung_dokumen_tgl || this.formData.gedung_dokumen_tgl || '',
                        status_tanah: firstG.gedung_status_tanah || this.formData.gedung_status_tanah || 'Tanah Milik RSUD',
                        fungsi_gedung: firstG.gedung_fungsi || this.formData.gedung_fungsi || '',
                        keterangan: firstG.gedung_fungsi || this.formData.gedung_fungsi || '',
                        gedung_items: this.formData.gedung_items
                    };
                } else if (this.isJaringan) {
                    const firstJ = (this.formData.jaringan_items && this.formData.jaringan_items[0]) ? this.formData.jaringan_items[0] : {};
                    const totalLuasJar = this.formData.jaringan_items ? this.formData.jaringan_items.reduce((s, it) => s + (parseFloat(it.jaringan_luas) || 0), 0) : (parseFloat(this.formData.jaringan_luas) || 0);

                    specJson = {
                        kategori_kib: 'KIB D (Jalan, Irigasi & Jaringan)',
                        konstruksi: firstJ.jaringan_konstruksi || this.formData.jaringan_konstruksi || '',
                        luas_m2: totalLuasJar,
                        panjang_m: parseFloat(firstJ.jaringan_panjang) || parseFloat(this.formData.jaringan_panjang) || 0,
                        lebar_m: parseFloat(firstJ.jaringan_lebar) || parseFloat(this.formData.jaringan_lebar) || 0,
                        bertingkat: firstJ.jaringan_bertingkat || 'Tidak',
                        beton: firstJ.jaringan_beton || 'Beton',
                        kondisi: firstJ.jaringan_kondisi || this.formData.jaringan_kondisi || this.formData.kondisi || 'Baik',
                        dokumen_no: firstJ.jaringan_dokumen_no || this.formData.jaringan_dokumen_no || '',
                        dokumen_tgl: firstJ.jaringan_dokumen_tgl || this.formData.jaringan_dokumen_tgl || '',
                        status_tanah: firstJ.jaringan_status_tanah || '',
                        kode_aset_tanah: firstJ.jaringan_kode_aset_tanah || '',
                        keterangan: firstJ.jaringan_keterangan || '',
                        jaringan_items: this.formData.jaringan_items
                    };
                } else if (this.isLainnya) {
                    const firstL = (this.formData.lainnya_items && this.formData.lainnya_items[0]) ? this.formData.lainnya_items[0] : {};

                    specJson = {
                        kategori_kib: 'KIB E (Aset Tetap Lainnya)',
                        kib_e_type: firstL.kib_e_type || this.formData.kib_e_type || 'buku',
                        judul: firstL.lainnya_judul || this.formData.lainnya_judul || '',
                        pencipta: firstL.lainnya_pencipta || this.formData.lainnya_pencipta || '',
                        spesifikasi: firstL.lainnya_spesifikasi || this.formData.lainnya_spesifikasi || '',
                        keterangan: firstL.lainnya_keterangan || firstL.lainnya_spesifikasi || '',
                        penerbit: firstL.lainnya_penerbit || this.formData.lainnya_penerbit || '',
                        tahun: firstL.lainnya_tahun || this.formData.lainnya_tahun || null,
                        asal_daerah: firstL.lainnya_asal_daerah || this.formData.lainnya_asal_daerah || '',
                        ukuran: firstL.lainnya_ukuran || this.formData.lainnya_ukuran || '',
                        bahan: firstL.lainnya_bahan || this.formData.lainnya_bahan || '',
                        asal_usul: firstL.lainnya_asal || this.formData.lainnya_asal || '',
                        lainnya_items: this.formData.lainnya_items
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
