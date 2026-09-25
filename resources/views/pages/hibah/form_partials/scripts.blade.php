<script>
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);
    window.dbRekeningBelanjas = @json(!empty($dbRekeningBelanjas) ? $dbRekeningBelanjas : []);
    window.dbUnits = @json(!empty($dbUnits) ? $dbUnits : []);
    window.dbPenyedias = @json(!empty($dbPenyedias) ? $dbPenyedias : []);
    window.dbPejabats = @json(!empty($dbPejabats) ? $dbPejabats : []);

    function formHibah() {
        return {
            currentStep: 1,
            totalSteps: 3,
            isSubmitting: false,

            // Master Data
            master108: window.dbMasterJenisAstap108 || [],
            masterRekeningBelanja: window.dbRekeningBelanjas || [],
            pejabatsList: window.dbPejabats || [],
            unitsList: window.dbUnits || [],
            masterUnits: window.dbUnits || [],

            // Filter States (Sama Persis seperti Belanja Modal)
            searchRekening: '',
            isRekeningOpen: false,
            searchJenis108: '',
            isJenis108Open: false,
            searchSubRincian108: '',
            isSubRincian108Open: false,
            searchNamaBarang108: '',
            isNamaBarang108Open: false,

            // Active Selected Item
            selectedSubSub: null,

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

            // Form Data Payload
            formData: {
                // Langkah 1: BAST & Pemberi (Tahun & Triwulan sekarang di Langkah 2 per instruksi PM)
                tahun_perolehan: new Date().getFullYear(),
                triwulan: 'TW I',
                hibah_pemberi: '',
                hibah_nomor_bast: '',
                hibah_tanggal_bast: new Date().toISOString().split('T')[0],
                total_realisasi: 0,
                jumlah_realisasi: 0,

                // Langkah 2: Klasifikasi 108 & Identitas Barang
                rekening_belanja_id: null,
                kode_rek: '',
                nama_belanja: '',
                jenis_astap_id: null,
                jenis_aset_kode: '',
                jenis_aset_nama: '',
                sub_rincian_kode: '',
                sub_rincian_nama: '',
                nama_barang: '',
                satuan: 'Unit',
                jumlah_volume: 1,
                is_extracomtable: false,

                // Langkah 3: Rincian KIB Multi-Item
                // KIB A (Tanah) Repeater
                tanah_items: [
                    {
                        tanah_nama_barang: '',
                        tanah_kode_barang: '',
                        tanah_hak: 'Hak Pakai',
                        tanah_sertifikat_no: '',
                        tanah_sertifikat_tgl: '',
                        tanah_kondisi: 'Baik',
                        tanah_penggunaan: 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                        tanah_jumlah_bidang: 1,
                        tanah_luas_m2: '',
                        tanah_alamat: '',
                        tanah_nilai_fisik: 0
                    }
                ],

                // KIB B (Peralatan & Mesin) Multi-Item Repeater
                mesin_items: [
                    {
                        mesin_nama_barang: '',
                        mesin_kode_barang: '',
                        mesin_merk: '',
                        mesin_type: '',
                        mesin_ukuran: '',
                        mesin_no_pabrik: '',
                        mesin_bahan: '',
                        mesin_no_rangka: '',
                        mesin_no_mesin: '',
                        mesin_no_bpkb: '',
                        mesin_no_polisi: '',
                        mesin_kondisi: 'Baik',
                        mesin_jumlah_barang: 1,
                        mesin_satuan: 'Unit',
                        mesin_nilai_satuan: 0,
                        mesin_administrasi_proyek: 0,
                        ruang_pemegang: '',
                        isRuangOpen: false,
                        searchRuang: ''
                    }
                ],

                // KIB C (Gedung & Bangunan) Multi-Item Repeater
                gedung_items: [
                    {
                        gedung_nama_barang: '',
                        gedung_kode_barang: '',
                        gedung_luas_m2: 0,
                        gedung_kondisi: 'B',
                        gedung_bertingkat: 'Bertingkat',
                        gedung_beton: 'Beton',
                        gedung_status_tanah: 'Tanah Hak Pakai RSUD',
                        gedung_kode_aset_tanah: '1.3.1.01.01.02.013',
                        gedung_is_baru: 'Baru',
                        gedung_kapitalisasi_tahun_induk: '',
                        gedung_kapitalisasi_nilai_induk: 0,
                        gedung_jumlah_bangunan: 1,
                        gedung_satuan: 'Gedung',
                        gedung_nilai_perencanaan: 0,
                        gedung_nilai_fisik: 0,
                        gedung_nilai_pengawasan: 0,
                        gedung_nilai_ap: 0,
                        gedung_nilai_pip: 0,
                        gedung_alamat: ''
                    }
                ],

                // KIB D (Jalan, Irigasi & Jaringan) Multi-Item Repeater
                jaringan_items: [
                    {
                        jaringan_nama_barang: '',
                        jaringan_kode_barang: '',
                        jaringan_konstruksi: '',
                        jaringan_panjang_m: 0,
                        jaringan_lebar_m: 0,
                        jaringan_luas_m2: 0,
                        jaringan_kondisi: 'B',
                        jaringan_bertingkat: 'Bertingkat',
                        jaringan_beton: 'Beton',
                        jaringan_status_tanah: 'Tanah Hak Pakai RSUD',
                        jaringan_kode_aset_tanah: '1.3.1.01.01.02.013',
                        jaringan_is_baru: 'Baru',
                        jaringan_kapitalisasi_tahun_induk: '',
                        jaringan_kapitalisasi_nilai_induk: 0,
                        jaringan_jumlah: 1,
                        jaringan_satuan: 'Paket',
                        jaringan_nilai_perencanaan: 0,
                        jaringan_nilai_fisik: 0,
                        jaringan_nilai_pengawasan: 0,
                        jaringan_nilai_ap: 0,
                        jaringan_nilai_pip: 0,
                        jaringan_alamat: ''
                    }
                ],

                // KIB E (Aset Tetap Lainnya) Multi-Item Repeater
                kib_e_default_type: 'buku',
                kib_e_sub_type: 'buku',
                lainnya_items: [
                    {
                        kib_e_sub_type: 'buku',
                        lainnya_nama_barang: '',
                        lainnya_kode_barang: '',
                        lainnya_buku_judul: '',
                        lainnya_buku_pencipta: '',
                        lainnya_buku_spesifikasi: '',
                        lainnya_kesenian_asal: '',
                        lainnya_kesenian_pencipta: '',
                        lainnya_kesenian_spesifikasi: '',
                        lainnya_kesenian_bahan: '',
                        lainnya_kesenian_ukuran: '',
                        lainnya_hewan_judul: '',
                        lainnya_hewan_jenis: '',
                        lainnya_hewan_spesifikasi: '',
                        ruang_pemegang: '',
                        ruang_pemegang_lainnya: '',
                        lainnya_kondisi: 'Baik',
                        lainnya_jumlah_barang: 1,
                        lainnya_satuan: 'Eksemplar',
                        lainnya_nilai_satuan: 0,
                        lainnya_administrasi_proyek: 0,
                        isRuangOpen: false,
                        searchRuang: ''
                    }
                ],

                // ATB (Aset Tidak Berwujud) Multi-Item Repeater
                atb_items: [
                    {
                        atb_nama_barang: '',
                        atb_kode_barang: '',
                        atb_judul_nama: '',
                        atb_pencipta: '',
                        atb_spesifikasi: '',
                        atb_jumlah: 1,
                        atb_satuan: 'Lisensi',
                        atb_kondisi: 'Baik',
                        atb_nilai_satuan: 0,
                        atb_administrasi_proyek: 0,
                        atb_ruang_pemegang: '',
                        isRuangOpen: false,
                        searchRuang: ''
                    }
                ],

                // KIB F (Konstruksi Dalam Pengerjaan) Multi-Item Repeater
                kdp_items: [
                    {
                        kdp_nama_barang: '',
                        kdp_kode_barang: '',
                        kdp_luas_m2: 0,
                        kdp_kondisi: 'B',
                        kdp_progres_persen: 0,
                        kdp_bertingkat: 'Bertingkat',
                        kdp_beton: 'Beton',
                        kdp_status_tanah: 'Tanah Hak Pakai RSUD',
                        kdp_kode_aset_tanah: '1.3.1.01.01.02.013',
                        kdp_is_baru: 'Baru',
                        kdp_kapitalisasi_tahun_induk: '',
                        kdp_kapitalisasi_nilai_induk: 0,
                        kdp_jumlah_bangunan: 1,
                        kdp_satuan: 'Gedung',
                        kdp_nilai_perencanaan: 0,
                        kdp_nilai_fisik: 0,
                        kdp_nilai_pengawasan: 0,
                        kdp_nilai_ap: 0,
                        kdp_nilai_pip: 0,
                        kdp_alamat: ''
                    }
                ],

                // Fallback spesifikasi umum
                spesifikasi_barang: '',
                keadaan_barang: 'Baik',

                // Penempatan Ruangan & PPK RSUD
                unit_id: '',
                alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                ppk_nama: '',
                ppk_nip: '',
                hibah_keterangan: ''
            },

            init() {
                if (this.pejabatsList.length > 0) {
                    this.formData.ppk_nama = this.pejabatsList[0].nama || '';
                    this.formData.ppk_nip = this.pejabatsList[0].nip || '';
                }
            },

            // ─── Filtered Getters PMDN 108 ─────────────────────────────────────
            get filteredJenisAstap108() {
                const q = (this.searchJenis108 || '').toLowerCase().trim();
                if (!q) return this.master108;
                return this.master108.filter(j => 
                    (j.kode && j.kode.toLowerCase().includes(q)) ||
                    (j.nama && j.nama.toLowerCase().includes(q))
                );
            },

            get filteredSubRincian108() {
                let list = [];
                if (this.formData.jenis_aset_kode) {
                    const found = this.master108.find(j => j.kode === this.formData.jenis_aset_kode);
                    if (found && found.subRincian) list = found.subRincian;
                } else {
                    this.master108.forEach(j => {
                        if (j.subRincian) list = list.concat(j.subRincian);
                    });
                }
                const q = (this.searchSubRincian108 || '').toLowerCase().trim();
                if (!q) return list;
                return list.filter(s => 
                    (s.kode && s.kode.toLowerCase().includes(q)) ||
                    (s.nama && s.nama.toLowerCase().includes(q))
                );
            },

            get filteredSubSubRincian108() {
                let list = [];
                if (this.formData.sub_rincian_kode) {
                    const found = this.filteredSubRincian108.find(s => s.kode === this.formData.sub_rincian_kode);
                    if (found && found.subSubRincian) list = found.subSubRincian;
                } else if (this.formData.jenis_aset_kode) {
                    const foundJ = this.master108.find(j => j.kode === this.formData.jenis_aset_kode);
                    if (foundJ && foundJ.subRincian) {
                        foundJ.subRincian.forEach(s => {
                            if (s.subSubRincian) list = list.concat(s.subSubRincian);
                        });
                    }
                } else {
                    this.master108.forEach(j => {
                        if (j.subRincian) {
                            j.subRincian.forEach(s => {
                                if (s.subSubRincian) list = list.concat(s.subSubRincian);
                            });
                        }
                    });
                }
                const q = (this.searchNamaBarang108 || '').toLowerCase().trim();
                if (!q) return list.slice(0, 50);
                return list.filter(item => 
                    (item.kode && item.kode.toLowerCase().includes(q)) ||
                    (item.nama && item.nama.toLowerCase().includes(q))
                ).slice(0, 50);
            },

            get activeKodeBarang() {
                return this.selectedSubSub?.kode || this.formData.jenis_aset_kode || '';
            },

            get activeNamaBarang() {
                return this.selectedSubSub?.nama || this.formData.nama_barang || '';
            },

            // ─── Selection Handlers PMDN 108 ───────────────────────────────────
            selectJenisAstap(j) {
                this.formData.jenis_aset_kode = j.kode;
                this.formData.jenis_aset_nama = j.nama;
                this.isJenis108Open = false;
                this.searchJenis108 = '';

                // Reset sub rincian & barang jika ganti kelompok jenis
                this.formData.sub_rincian_kode = '';
                this.formData.sub_rincian_nama = '';
                this.selectedSubSub = null;
                this.formData.jenis_astap_id = null;

                this.adjustSatuanForKib();
            },

            selectSubRincian(s) {
                this.formData.sub_rincian_kode = s.kode;
                this.formData.sub_rincian_nama = s.nama;
                this.isSubRincian108Open = false;
                this.searchSubRincian108 = '';

                // Otomatis isi parent jenis aset jika belum terpilih
                if (!this.formData.jenis_aset_kode) {
                    for (const j of this.master108) {
                        if (j.subRincian && j.subRincian.some(sub => sub.kode === s.kode)) {
                            this.formData.jenis_aset_kode = j.kode;
                            this.formData.jenis_aset_nama = j.nama;
                            break;
                        }
                    }
                }
                this.adjustSatuanForKib();
            },

            selectSubSubRincianItem(item) {
                this.selectedSubSub = item;
                this.formData.jenis_astap_id = item.id;
                this.formData.nama_barang = item.nama;
                this.isNamaBarang108Open = false;
                this.searchNamaBarang108 = '';

                // Otomatis cari dan isi parent jika belum terpilih
                let foundSub = null;
                let foundJenis = null;
                for (const j of this.master108) {
                    if (!j.subRincian) continue;
                    for (const s of j.subRincian) {
                        if (s.subSubRincian && s.subSubRincian.some(ss => ss.kode === item.kode || ss.id === item.id)) {
                            foundSub = s;
                            foundJenis = j;
                            break;
                        }
                    }
                    if (foundJenis) break;
                }

                if (foundJenis) {
                    this.formData.jenis_aset_kode = foundJenis.kode;
                    this.formData.jenis_aset_nama = foundJenis.nama;
                }
                if (foundSub) {
                    this.formData.sub_rincian_kode = foundSub.kode;
                    this.formData.sub_rincian_nama = foundSub.nama;
                }

                this.adjustSatuanForKib();

                // Propagasi nama dan kode barang ke seluruh repeater item di Step 3
                this.propagateItemIdentity(item.nama, item.kode);
            },

            propagateItemIdentity(nama, kode) {
                if (this.formData.tanah_items) {
                    this.formData.tanah_items.forEach(it => { it.tanah_nama_barang = nama; it.tanah_kode_barang = kode; });
                }
                if (this.formData.mesin_items) {
                    this.formData.mesin_items.forEach(it => { it.mesin_nama_barang = nama; it.mesin_kode_barang = kode; });
                }
                if (this.formData.gedung_items) {
                    this.formData.gedung_items.forEach(it => { it.gedung_nama_barang = nama; it.gedung_kode_barang = kode; });
                }
                if (this.formData.jaringan_items) {
                    this.formData.jaringan_items.forEach(it => { it.jaringan_nama_barang = nama; it.jaringan_kode_barang = kode; });
                }
                if (this.formData.lainnya_items) {
                    this.formData.lainnya_items.forEach(it => { it.lainnya_nama_barang = nama; it.lainnya_kode_barang = kode; });
                }
                if (this.formData.atb_items) {
                    this.formData.atb_items.forEach(it => { it.atb_nama_barang = nama; it.atb_kode_barang = kode; });
                }
                if (this.formData.kdp_items) {
                    this.formData.kdp_items.forEach(it => { it.kdp_nama_barang = nama; it.kdp_kode_barang = kode; });
                }
            },

            // ─── KIB Detection Getters ─────────────────────────────────────────
            get isTanah() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.1') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('tanah');
            },

            get isMesin() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.2') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('peralatan');
            },

            get isGedung() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.3') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('gedung');
            },

            get isJaringan() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.4') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('jaringan') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('jalan');
            },

            get isAsetLainnya() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.5') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('lainnya');
            },

            get isKdp() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.3.6') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('konstruksi');
            },

            get isAtb() {
                const k = this.formData.jenis_aset_kode || '';
                return k.startsWith('1.5') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('berwujud') || (this.formData.jenis_aset_nama || '').toLowerCase().includes('software');
            },

            get hasSelectedKib() {
                return !!(this.formData.jenis_aset_kode || this.formData.jenis_astap_id);
            },

            get kibLabel() {
                if (this.isTanah) return 'KIB A (Tanah)';
                if (this.isMesin) return 'KIB B (Peralatan & Mesin)';
                if (this.isGedung) return 'KIB C (Gedung & Bangunan)';
                if (this.isJaringan) return 'KIB D (Jalan, Irigasi & Jaringan)';
                if (this.isAsetLainnya) return 'KIB E (Aset Tetap Lainnya)';
                if (this.isAtb) return 'ATB (Aset Tidak Berwujud)';
                if (this.isKdp) return 'KIB F (Konstruksi Dalam Pengerjaan)';
                return 'Aset Tetap Hibah';
            },

            adjustSatuanForKib() {
                if (this.isTanah) {
                    this.formData.satuan = 'Bidang';
                } else if (this.isGedung) {
                    this.formData.satuan = 'Gedung';
                } else if (this.isJaringan) {
                    this.formData.satuan = 'Paket';
                } else if (this.isAsetLainnya) {
                    this.formData.satuan = (this.formData.kib_e_default_type === 'buku') ? 'Eksemplar' : 'Buah';
                } else if (this.isAtb) {
                    this.formData.satuan = 'Lisensi';
                } else if (this.isKdp) {
                    this.formData.satuan = 'Gedung';
                } else {
                    this.formData.satuan = 'Unit';
                }
            },

            // ─── 1. KIB A (Tanah) Handlers ──────────────────────────────────────
            get totalLuasTanah() {
                if (!this.formData.tanah_items) return 0;
                return this.formData.tanah_items.reduce((s, it) => s + (parseFloat(it.tanah_luas_m2) || 0), 0);
            },

            addTanahItem() {
                if (!this.formData.tanah_items) this.formData.tanah_items = [];
                this.formData.tanah_items.push({
                    tanah_nama_barang: this.formData.nama_barang || '',
                    tanah_kode_barang: this.activeKodeBarang || '',
                    tanah_hak: 'Hak Pakai',
                    tanah_sertifikat_no: '',
                    tanah_sertifikat_tgl: '',
                    tanah_kondisi: 'Baik',
                    tanah_penggunaan: 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                    tanah_jumlah_bidang: 1,
                    tanah_luas_m2: '',
                    tanah_alamat: '',
                    tanah_nilai_fisik: 0
                });
                this.syncActiveKibTotals();
            },

            removeTanahItem(index) {
                if (this.formData.tanah_items && this.formData.tanah_items.length > 1) {
                    this.formData.tanah_items.splice(index, 1);
                    this.syncActiveKibTotals();
                }
            },

            syncTanahFieldsToMain() {
                if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    const first = this.formData.tanah_items[0];
                    this.formData.kondisi = first.tanah_kondisi;
                    const totalBidang = this.formData.tanah_items.reduce((s, it) => s + (parseInt(it.tanah_jumlah_bidang) || 1), 0);
                    this.formData.jumlah_volume = totalBidang;
                    this.formData.satuan = 'Bidang';
                    if (first.tanah_alamat) {
                        this.formData.alamat_barang = first.tanah_alamat;
                    }
                }
            },

            // ─── 2. KIB B (Peralatan & Mesin) Handlers ──────────────────────────
            get totalNilaiMesin() {
                if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    return this.formData.mesin_items.reduce((sum, item) => sum + this.getMesinSubtotal(item), 0);
                }
                return 0;
            },

            get totalVolumeMesin() {
                if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    return this.formData.mesin_items.reduce((sum, item) => sum + (parseInt(item.mesin_jumlah_barang) || 1), 0);
                }
                return 1;
            },

            getMesinSubtotal(item) {
                return (Number(item.mesin_jumlah_barang || 1) * Number(item.mesin_nilai_satuan || 0)) + Number(item.mesin_administrasi_proyek || 0);
            },

            addMesinItem() {
                if (!this.formData.mesin_items) this.formData.mesin_items = [];
                this.formData.mesin_items.push({
                    mesin_nama_barang: this.formData.nama_barang || this.formData.sub_rincian_nama || '',
                    mesin_kode_barang: this.activeKodeBarang || this.formData.sub_rincian_kode || '',
                    mesin_merk: '',
                    mesin_type: '',
                    mesin_ukuran: '',
                    mesin_no_pabrik: '',
                    mesin_bahan: '',
                    mesin_no_rangka: '',
                    mesin_no_mesin: '',
                    mesin_no_bpkb: '',
                    mesin_no_polisi: '',
                    mesin_kondisi: 'Baik',
                    mesin_jumlah_barang: 1,
                    mesin_satuan: 'Unit',
                    mesin_nilai_satuan: 0,
                    mesin_administrasi_proyek: 0,
                    ruang_pemegang: '',
                    isRuangOpen: false,
                    searchRuang: ''
                });
                this.syncActiveKibTotals();
            },

            removeMesinItem(index) {
                if (this.formData.mesin_items && this.formData.mesin_items.length > 1) {
                    this.formData.mesin_items.splice(index, 1);
                    this.syncActiveKibTotals();
                }
            },

            syncMesinFieldsToMain() {
                if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                    const first = this.formData.mesin_items[0];
                    this.formData.merk = first.mesin_merk;
                    this.formData.type = first.mesin_type;
                    this.formData.ukuran = first.mesin_ukuran;
                    this.formData.no_pabrik = first.mesin_no_pabrik;
                    this.formData.bahan = first.mesin_bahan;
                    this.formData.kondisi = first.mesin_kondisi;
                    this.formData.no_rangka = first.mesin_no_rangka;
                    this.formData.no_mesin = first.mesin_no_mesin;
                    this.formData.no_polisi = first.mesin_no_polisi;
                    this.formData.satuan = first.mesin_satuan || 'Unit';
                    if (first.ruang_pemegang) {
                        this.formData.alamat_barang = first.ruang_pemegang;
                    }
                }
            },

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
                this.syncActiveKibTotals();
            },

            // ─── 3. KIB C (Gedung & Bangunan) Handlers ──────────────────────────
            get totalNilaiGedung() {
                if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    return this.formData.gedung_items.reduce((sum, item) => sum + this.getGedungSubtotal(item), 0);
                }
                return 0;
            },

            get totalVolumeGedung() {
                if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    return this.formData.gedung_items.reduce((sum, item) => sum + (parseInt(item.gedung_jumlah_bangunan) || 1), 0);
                }
                return 1;
            },

            getGedungSubtotal(item) {
                return Number(item.gedung_nilai_perencanaan || 0) + 
                       Number(item.gedung_nilai_fisik || 0) + 
                       Number(item.gedung_nilai_pengawasan || 0) + 
                       Number(item.gedung_nilai_ap || item.gedung_nilai_pip || 0);
            },

            addGedungItem() {
                if (!this.formData.gedung_items) this.formData.gedung_items = [];
                this.formData.gedung_items.push({
                    gedung_nama_barang: this.formData.nama_barang || this.formData.sub_rincian_nama || '',
                    gedung_kode_barang: this.activeKodeBarang || this.formData.sub_rincian_kode || '',
                    gedung_luas_m2: 0,
                    gedung_kondisi: 'B',
                    gedung_bertingkat: 'Bertingkat',
                    gedung_beton: 'Beton',
                    gedung_status_tanah: 'Tanah Hak Pakai RSUD',
                    gedung_kode_aset_tanah: '1.3.1.01.01.02.013',
                    gedung_is_baru: 'Baru',
                    gedung_kapitalisasi_tahun_induk: '',
                    gedung_kapitalisasi_nilai_induk: 0,
                    gedung_jumlah_bangunan: 1,
                    gedung_satuan: 'Gedung',
                    gedung_nilai_perencanaan: 0,
                    gedung_nilai_fisik: 0,
                    gedung_nilai_pengawasan: 0,
                    gedung_nilai_ap: 0,
                    gedung_nilai_pip: 0,
                    gedung_alamat: ''
                });
                this.syncActiveKibTotals();
            },

            removeGedungItem(index) {
                if (this.formData.gedung_items && this.formData.gedung_items.length > 1) {
                    this.formData.gedung_items.splice(index, 1);
                    this.syncActiveKibTotals();
                }
            },

            syncGedungFieldsToMain() {
                if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                    const first = this.formData.gedung_items[0];
                    this.formData.kondisi = first.gedung_kondisi;
                    this.formData.satuan = first.gedung_satuan || 'Gedung';
                    if (first.gedung_alamat) {
                        this.formData.alamat_barang = first.gedung_alamat;
                    }
                }
            },

            // ─── 4. KIB D (Jalan, Irigasi & Jaringan) Handlers ──────────────────
            get totalNilaiJaringan() {
                if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    return this.formData.jaringan_items.reduce((sum, item) => sum + this.getJaringanSubtotal(item), 0);
                }
                return 0;
            },

            get totalVolumeJaringan() {
                if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    return this.formData.jaringan_items.reduce((sum, item) => sum + (parseInt(item.jaringan_jumlah) || 1), 0);
                }
                return 1;
            },

            getJaringanSubtotal(item) {
                return Number(item.jaringan_nilai_perencanaan || 0) + 
                       Number(item.jaringan_nilai_fisik || 0) + 
                       Number(item.jaringan_nilai_pengawasan || 0) + 
                       Number(item.jaringan_nilai_ap || item.jaringan_nilai_pip || 0);
            },

            addJaringanItem() {
                if (!this.formData.jaringan_items) this.formData.jaringan_items = [];
                this.formData.jaringan_items.push({
                    jaringan_nama_barang: this.formData.nama_barang || this.formData.sub_rincian_nama || '',
                    jaringan_kode_barang: this.activeKodeBarang || this.formData.sub_rincian_kode || '',
                    jaringan_konstruksi: '',
                    jaringan_panjang_m: 0,
                    jaringan_lebar_m: 0,
                    jaringan_luas_m2: 0,
                    jaringan_kondisi: 'B',
                    jaringan_bertingkat: 'Bertingkat',
                    jaringan_beton: 'Beton',
                    jaringan_status_tanah: 'Tanah Hak Pakai RSUD',
                    jaringan_kode_aset_tanah: '1.3.1.01.01.02.013',
                    jaringan_is_baru: 'Baru',
                    jaringan_kapitalisasi_tahun_induk: '',
                    jaringan_kapitalisasi_nilai_induk: 0,
                    jaringan_jumlah: 1,
                    jaringan_satuan: 'Paket',
                    jaringan_nilai_perencanaan: 0,
                    jaringan_nilai_fisik: 0,
                    jaringan_nilai_pengawasan: 0,
                    jaringan_nilai_ap: 0,
                    jaringan_nilai_pip: 0,
                    jaringan_alamat: ''
                });
                this.syncActiveKibTotals();
            },

            removeJaringanItem(index) {
                if (this.formData.jaringan_items && this.formData.jaringan_items.length > 1) {
                    this.formData.jaringan_items.splice(index, 1);
                    this.syncActiveKibTotals();
                }
            },

            syncJaringanFieldsToMain() {
                if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                    const first = this.formData.jaringan_items[0];
                    this.formData.kondisi = first.jaringan_kondisi;
                    this.formData.satuan = first.jaringan_satuan || 'Paket';
                    if (first.jaringan_alamat) {
                        this.formData.alamat_barang = first.jaringan_alamat;
                    }
                }
            },

            // ─── 5. KIB E (Aset Tetap Lainnya) Handlers ─────────────────────────
            get totalNilaiAsetLainnya() {
                if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                    return this.formData.lainnya_items.reduce((sum, item) => sum + this.getLainnyaSubtotal(item), 0);
                }
                return 0;
            },

            get totalVolumeAsetLainnya() {
                if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                    return this.formData.lainnya_items.reduce((sum, item) => sum + (parseInt(item.lainnya_jumlah_barang) || 1), 0);
                }
                return 1;
            },

            getLainnyaSubtotal(item) {
                return (Number(item.lainnya_jumlah_barang || 1) * Number(item.lainnya_nilai_satuan || 0)) + Number(item.lainnya_administrasi_proyek || 0);
            },

            addLainnyaItem() {
                if (!this.formData.lainnya_items) this.formData.lainnya_items = [];
                this.formData.lainnya_items.push({
                    kib_e_sub_type: this.formData.kib_e_default_type || 'buku',
                    lainnya_nama_barang: this.formData.nama_barang || this.formData.sub_rincian_nama || '',
                    lainnya_kode_barang: this.activeKodeBarang || this.formData.sub_rincian_kode || '',
                    lainnya_buku_judul: '',
                    lainnya_buku_pencipta: '',
                    lainnya_buku_spesifikasi: '',
                    lainnya_kesenian_asal: '',
                    lainnya_kesenian_pencipta: '',
                    lainnya_kesenian_spesifikasi: '',
                    lainnya_kesenian_bahan: '',
                    lainnya_kesenian_ukuran: '',
                    lainnya_hewan_judul: '',
                    lainnya_hewan_jenis: '',
                    lainnya_hewan_spesifikasi: '',
                    ruang_pemegang: '',
                    ruang_pemegang_lainnya: '',
                    lainnya_kondisi: 'Baik',
                    lainnya_jumlah_barang: 1,
                    lainnya_satuan: (this.formData.kib_e_default_type === 'buku') ? 'Eksemplar' : 'Buah',
                    lainnya_nilai_satuan: 0,
                    lainnya_administrasi_proyek: 0,
                    isRuangOpen: false,
                    searchRuang: ''
                });
                this.syncActiveKibTotals();
            },

            removeLainnyaItem(index) {
                if (this.formData.lainnya_items && this.formData.lainnya_items.length > 1) {
                    this.formData.lainnya_items.splice(index, 1);
                    this.syncActiveKibTotals();
                }
            },

            syncLainnyaFieldsToMain() {
                if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                    const first = this.formData.lainnya_items[0];
                    this.formData.kondisi = first.lainnya_kondisi;
                    this.formData.satuan = first.lainnya_satuan || 'Eksemplar';
                    if (first.ruang_pemegang) {
                        this.formData.alamat_barang = first.ruang_pemegang;
                    }
                }
            },

            filterUnitsForLainnyaItem(item) {
                let list = this.masterUnits || [];
                if (!item.searchRuang || item.searchRuang.trim() === '') return list;
                const q = item.searchRuang.toLowerCase().trim();
                return list.filter(u => (u.nama || '').toLowerCase().includes(q) || (u.kode || '').toLowerCase().includes(q) || (u.tipe || '').toLowerCase().includes(q));
            },

            selectUnitForLainnyaItem(item, unit) {
                item.ruang_pemegang = unit.nama;
                item.ruang_pemegang_lainnya = unit.nama;
                item.isRuangOpen = false;
                item.searchRuang = '';
                this.syncActiveKibTotals();
            },

            // ─── 6. ATB (Aset Tidak Berwujud) Handlers ──────────────────────────
            get totalNilaiAtb() {
                if (this.formData.atb_items && this.formData.atb_items.length > 0) {
                    return this.formData.atb_items.reduce((sum, item) => sum + this.getAtbSubtotal(item), 0);
                }
                return 0;
            },

            get totalVolumeAtb() {
                if (this.formData.atb_items && this.formData.atb_items.length > 0) {
                    return this.formData.atb_items.reduce((sum, item) => sum + (parseInt(item.atb_jumlah) || 1), 0);
                }
                return 1;
            },

            getAtbSubtotal(item) {
                return (Number(item.atb_jumlah || 1) * Number(item.atb_nilai_satuan || 0)) + Number(item.atb_administrasi_proyek || 0);
            },

            addAtbItem() {
                if (!this.formData.atb_items) this.formData.atb_items = [];
                this.formData.atb_items.push({
                    atb_nama_barang: this.formData.nama_barang || this.formData.sub_rincian_nama || '',
                    atb_kode_barang: this.activeKodeBarang || this.formData.sub_rincian_kode || '',
                    atb_judul_nama: '',
                    atb_pencipta: '',
                    atb_spesifikasi: '',
                    atb_jumlah: 1,
                    atb_satuan: 'Lisensi',
                    atb_kondisi: 'Baik',
                    atb_nilai_satuan: 0,
                    atb_administrasi_proyek: 0,
                    atb_ruang_pemegang: '',
                    isRuangOpen: false,
                    searchRuang: ''
                });
                this.syncActiveKibTotals();
            },

            removeAtbItem(index) {
                if (this.formData.atb_items && this.formData.atb_items.length > 1) {
                    this.formData.atb_items.splice(index, 1);
                    this.syncActiveKibTotals();
                }
            },

            syncAtbFieldsToMain() {
                if (this.formData.atb_items && this.formData.atb_items.length > 0) {
                    const first = this.formData.atb_items[0];
                    this.formData.kondisi = first.atb_kondisi;
                    this.formData.satuan = first.atb_satuan || 'Lisensi';
                    if (first.atb_ruang_pemegang) {
                        this.formData.alamat_barang = first.atb_ruang_pemegang;
                    }
                }
            },

            // ─── 7. KIB F (Konstruksi Dalam Pengerjaan) Handlers ─────────────────
            get totalNilaiKdp() {
                if (this.formData.kdp_items && this.formData.kdp_items.length > 0) {
                    return this.formData.kdp_items.reduce((sum, item) => sum + this.getKdpSubtotal(item), 0);
                }
                return 0;
            },

            get totalVolumeKdp() {
                if (this.formData.kdp_items && this.formData.kdp_items.length > 0) {
                    return this.formData.kdp_items.reduce((sum, item) => sum + (parseInt(item.kdp_jumlah_bangunan) || 1), 0);
                }
                return 1;
            },

            getKdpSubtotal(item) {
                return Number(item.kdp_nilai_perencanaan || 0) + 
                       Number(item.kdp_nilai_fisik || 0) + 
                       Number(item.kdp_nilai_pengawasan || 0) + 
                       Number(item.kdp_nilai_ap || item.kdp_nilai_pip || 0);
            },

            addKdpItem() {
                if (!this.formData.kdp_items) this.formData.kdp_items = [];
                this.formData.kdp_items.push({
                    kdp_nama_barang: this.formData.nama_barang || this.formData.sub_rincian_nama || '',
                    kdp_kode_barang: this.activeKodeBarang || this.formData.sub_rincian_kode || '',
                    kdp_luas_m2: 0,
                    kdp_kondisi: 'B',
                    kdp_progres_persen: 0,
                    kdp_bertingkat: 'Bertingkat',
                    kdp_beton: 'Beton',
                    kdp_status_tanah: 'Tanah Hak Pakai RSUD',
                    kdp_kode_aset_tanah: '1.3.1.01.01.02.013',
                    kdp_is_baru: 'Baru',
                    kdp_kapitalisasi_tahun_induk: '',
                    kdp_kapitalisasi_nilai_induk: 0,
                    kdp_jumlah_bangunan: 1,
                    kdp_satuan: 'Gedung',
                    kdp_nilai_perencanaan: 0,
                    kdp_nilai_fisik: 0,
                    kdp_nilai_pengawasan: 0,
                    kdp_nilai_ap: 0,
                    kdp_nilai_pip: 0,
                    kdp_alamat: ''
                });
                this.syncActiveKibTotals();
            },

            removeKdpItem(index) {
                if (this.formData.kdp_items && this.formData.kdp_items.length > 1) {
                    this.formData.kdp_items.splice(index, 1);
                    this.syncActiveKibTotals();
                }
            },

            syncKdpFieldsToMain() {
                if (this.formData.kdp_items && this.formData.kdp_items.length > 0) {
                    const first = this.formData.kdp_items[0];
                    this.formData.kondisi = first.kdp_kondisi;
                    this.formData.satuan = first.kdp_satuan || 'Gedung';
                    if (first.kdp_alamat) {
                        this.formData.alamat_barang = first.kdp_alamat;
                    }
                }
            },

            // ─── Master Sync Function for Active KIB ─────────────────────────────
            syncActiveKibTotals() {
                if (this.isTanah) {
                    this.syncTanahFieldsToMain();
                    const totalNilai = (this.formData.tanah_items || []).reduce((s, it) => s + (parseFloat(it.tanah_nilai_fisik) || 0), 0);
                    if (totalNilai > 0) {
                        this.formData.total_realisasi = totalNilai;
                        this.formData.jumlah_realisasi = totalNilai;
                    }
                } else if (this.isMesin) {
                    this.syncMesinFieldsToMain();
                    const totalNilai = this.totalNilaiMesin;
                    if (totalNilai > 0) {
                        this.formData.total_realisasi = totalNilai;
                        this.formData.jumlah_realisasi = totalNilai;
                    }
                    this.formData.jumlah_volume = this.totalVolumeMesin;
                } else if (this.isGedung) {
                    this.syncGedungFieldsToMain();
                    const totalNilai = this.totalNilaiGedung;
                    if (totalNilai > 0) {
                        this.formData.total_realisasi = totalNilai;
                        this.formData.jumlah_realisasi = totalNilai;
                    }
                    this.formData.jumlah_volume = this.totalVolumeGedung;
                } else if (this.isJaringan) {
                    this.syncJaringanFieldsToMain();
                    const totalNilai = this.totalNilaiJaringan;
                    if (totalNilai > 0) {
                        this.formData.total_realisasi = totalNilai;
                        this.formData.jumlah_realisasi = totalNilai;
                    }
                    this.formData.jumlah_volume = this.totalVolumeJaringan;
                } else if (this.isAsetLainnya) {
                    this.syncLainnyaFieldsToMain();
                    const totalNilai = this.totalNilaiAsetLainnya;
                    if (totalNilai > 0) {
                        this.formData.total_realisasi = totalNilai;
                        this.formData.jumlah_realisasi = totalNilai;
                    }
                    this.formData.jumlah_volume = this.totalVolumeAsetLainnya;
                } else if (this.isAtb) {
                    this.syncAtbFieldsToMain();
                    const totalNilai = this.totalNilaiAtb;
                    if (totalNilai > 0) {
                        this.formData.total_realisasi = totalNilai;
                        this.formData.jumlah_realisasi = totalNilai;
                    }
                    this.formData.jumlah_volume = this.totalVolumeAtb;
                } else if (this.isKdp) {
                    this.syncKdpFieldsToMain();
                    const totalNilai = this.totalNilaiKdp;
                    if (totalNilai > 0) {
                        this.formData.total_realisasi = totalNilai;
                        this.formData.jumlah_realisasi = totalNilai;
                    }
                    this.formData.jumlah_volume = this.totalVolumeKdp;
                }
            },

            onPpkSelect() {
                const found = this.pejabatsList.find(p => p.nama === this.formData.ppk_nama);
                if (found) {
                    this.formData.ppk_nip = found.nip || '';
                }
            },

            // ─── Utilities ──────────────────────────────────────────────────────
            formatRupiah(val) {
                const num = Number(val || 0);
                return num.toLocaleString('id-ID');
            },

            formatTanggalIndo(dateStr) {
                if (!dateStr) return '-';
                try {
                    const d = new Date(dateStr);
                    if (isNaN(d.getTime())) return dateStr;
                    return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
                } catch {
                    return dateStr;
                }
            },

            // ─── Multi-Step Navigation & Validation ─────────────────────────────
            goToStep(s) {
                if (s > this.currentStep) {
                    for (let i = this.currentStep; i < s; i++) {
                        if (!this.validateStep(i)) return;
                    }
                }
                this.syncActiveKibTotals();
                this.currentStep = s;
                window.scrollTo({ top: 0, behavior: 'smooth' });
            },

            nextStep() {
                if (!this.validateStep(this.currentStep)) return;
                this.syncActiveKibTotals();
                if (this.currentStep < this.totalSteps) {
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
                    if (!this.formData.hibah_pemberi.trim()) {
                        alert('⚠️ Mohon isi Nama Instansi Pemberi Hibah.');
                        return false;
                    }
                    if (!this.formData.hibah_nomor_bast.trim()) {
                        alert('⚠️ Mohon isi Nomor BAST Hibah.');
                        return false;
                    }
                    if (!this.formData.hibah_tanggal_bast) {
                        alert('⚠️ Mohon isi Tanggal BAST Hibah.');
                        return false;
                    }
                    return true;
                }

                if (s === 2) {
                    if (!this.formData.tahun_perolehan) {
                        alert('⚠️ Mohon tentukan Tahun Pembukuan Hibah.');
                        return false;
                    }
                    if (!this.formData.triwulan) {
                        alert('⚠️ Mohon tentukan Triwulan Pembukuan.');
                        return false;
                    }
                    if (!this.formData.jenis_astap_id && !this.formData.jenis_aset_kode) {
                        alert('⚠️ Mohon pilih Klasifikasi Jenis Aset / Kode Barang 108.');
                        return false;
                    }
                    if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
                        alert('⚠️ Mohon isi Nama Lengkap Barang.');
                        return false;
                    }
                    if (!this.formData.jumlah_volume || this.formData.jumlah_volume < 1) {
                        alert('⚠️ Mohon tentukan Volume / Kuantitas Barang.');
                        return false;
                    }
                    return true;
                }

                if (s === 3) {
                    this.syncActiveKibTotals();
                    if (!this.formData.unit_id) {
                        alert('⚠️ Mohon pilih Unit / Ruangan Penempatan Aset (KIR).');
                        return false;
                    }
                    return true;
                }

                return true;
            },

            submitForm() {
                if (!this.validateStep(1) || !this.validateStep(2) || !this.validateStep(3)) return;

                this.syncActiveKibTotals();

                this.isSubmitting = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                fetch("{{ route('astap.store_hibah') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(this.formData)
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(result => {
                    this.isSubmitting = false;
                    if (result.status === 200 && result.body.success) {
                        alert('🎉 Berhasil! ' + (result.body.message || 'Data Hibah berhasil disimpan ke database.'));
                        window.location.href = "{{ route('master.hibah') }}";
                    } else {
                        const errMsg = result.body.message || (result.body.errors ? Object.values(result.body.errors).flat().join('\n') : 'Gagal menyimpan data hibah.');
                        alert('❌ Terjadi Kesalahan:\n' + errMsg);
                    }
                })
                .catch(err => {
                    this.isSubmitting = false;
                    console.error(err);
                    alert('❌ Gagal menghubungi server. Silakan coba kembali.');
                });
            }
        };
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.6);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(245, 158, 11, 0.4);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(245, 158, 11, 0.7);
    }
</style>
