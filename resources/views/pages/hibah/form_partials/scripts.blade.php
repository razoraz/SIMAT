<script>
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);
    window.dbRekeningBelanjas = @json(!empty($dbRekeningBelanjas) ? $dbRekeningBelanjas : []);
    window.dbUnits = @json(!empty($dbUnits) ? $dbUnits : []);
    window.dbPenyedias = @json(!empty($dbPenyedias) ? $dbPenyedias : []);
    window.dbPejabats = @json(!empty($dbPejabats) ? $dbPejabats : []);
    window.dbPemberiHibahs = @json(!empty($dbPemberiHibahs) ? $dbPemberiHibahs : []);

    function formHibah() {
        return {
            currentStep: 1,
            totalSteps: 3,
            isSubmitting: false,
            isEditMode: !!window.editAstapData,
            editId: window.editAstapData ? window.editAstapData.id : null,

            init() {
                if (window.editAstapData) {
                    this.hydrateFromEditData(window.editAstapData);
                }
            },

            hydrateFromEditData(data) {
                if (!data) return;

                this.formData.tahun_perolehan = data.tahun_perolehan || new Date().getFullYear();
                this.formData.triwulan = data.triwulan || 'TW I';

                const hibah = data.hibah_masuk || data.hibahMasuk;
                if (hibah) {
                    this.formData.hibah_pemberi = hibah.pihak_hibah || '';
                    this.formData.hibah_nomor_bast = hibah.nomor_bast || '';
                    this.formData.hibah_tanggal_bast = hibah.tanggal_bast || '';
                    this.formData.hibah_keterangan = hibah.keterangan || '';
                }

                let spec = data.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                if (spec && typeof spec === 'object') {
                    if (spec.hibah_pemberi && !this.formData.hibah_pemberi) this.formData.hibah_pemberi = spec.hibah_pemberi;
                    if (spec.hibah_nomor_bast && !this.formData.hibah_nomor_bast) this.formData.hibah_nomor_bast = spec.hibah_nomor_bast;
                    if (spec.hibah_tanggal_bast && !this.formData.hibah_tanggal_bast) this.formData.hibah_tanggal_bast = spec.hibah_tanggal_bast;
                    if (spec.hibah_pimpinan) this.formData.hibah_pimpinan = spec.hibah_pimpinan;
                    if (spec.hibah_alamat_pemberi) this.formData.hibah_alamat_pemberi = spec.hibah_alamat_pemberi;
                    if (spec.hibah_keterangan && !this.formData.hibah_keterangan) this.formData.hibah_keterangan = spec.hibah_keterangan;
                    if (spec.tipe_hibah) this.formData.tipe_hibah = spec.tipe_hibah;
                }

                this.formData.nama_barang = data.nama_barang || '';
                this.formData.satuan = data.satuan || 'Unit';
                this.formData.jumlah_volume = parseInt(data.jumlah_volume) || 1;
                this.formData.total_realisasi = parseFloat(data.total_realisasi) || 0;
                this.formData.jumlah_realisasi = this.formData.total_realisasi;
                this.formData.is_extracomtable = !!data.is_extracomtable;
                this.formData.jenis_astap_id = data.jenis_astap_id || null;
                this.formData.rekening_belanja_id = data.rekening_belanja_id || null;

                if (data.unit_id) {
                    this.formData.unit_id = data.unit_id;
                } else if (data.unit && data.unit.id) {
                    this.formData.unit_id = data.unit.id;
                }

                this.formData.alamat_barang = data.alamat_barang || '';
                this.formData.ppk_nama = data.ppk_nama || (spec && spec.ppk_nama) || '';
                this.formData.ppk_nip = data.ppk_nip || (spec && spec.ppk_nip) || '';
                if (data.keterangan_tambahan && !this.formData.hibah_keterangan) {
                    this.formData.hibah_keterangan = data.keterangan_tambahan;
                }

                if (data.jenis_astap) {
                    this.formData.jenis_aset_kode = data.jenis_astap.kode || '';
                    this.formData.jenis_aset_nama = data.jenis_astap.nama || '';
                    this.formData.sub_rincian_kode = data.jenis_astap.sub_rincian_objek || '';
                    this.formData.sub_rincian_nama = data.jenis_astap.nama_sub_rincian_objek || '';
                    this.selectedSubSub = {
                        id: data.jenis_astap.id,
                        kode: data.jenis_astap.sub_sub_rincian_objek || data.kode_108,
                        nama: data.jenis_astap.nama_sub_sub_rincian_objek || data.nama_barang
                    };
                } else if (data.kode_108) {
                    this.formData.jenis_aset_kode = data.kode_108;
                    this.selectedSubSub = {
                        id: data.jenis_astap_id,
                        kode: data.kode_108,
                        nama: data.nama_barang
                    };
                }

                if (data.rekening_belanja) {
                    this.formData.kode_rek = data.rekening_belanja.kode_rek || '';
                    this.formData.nama_belanja = data.rekening_belanja.nama_belanja || '';
                }
            },

            // ─── File Upload State ───────────────────────────────────────────
            selectedFile: null,

            // ─── Per-Step Error Banners ──────────────────────────────────────
            stepErrors: { 1: null, 2: null, 3: null },

            // Master Data
            master108: window.dbMasterJenisAstap108 || [],
            masterRekeningBelanja: window.dbRekeningBelanjas || [],
            pejabatsList: window.dbPejabats || [],
            unitsList: window.dbUnits || [],
            masterUnits: window.dbUnits || [],

            // Autocomplete Riwayat Instansi Pemberi Hibah
            masterInstansiList: (window.dbPemberiHibahs && window.dbPemberiHibahs.length > 0)
                ? window.dbPemberiHibahs
                : [
                    'Kementerian Kesehatan Republik Indonesia',
                    'Dinas Kesehatan Provinsi Jawa Timur',
                    'Pemerintah Kabupaten Bondowoso',
                    'Donatur Swasta / Yayasan CSR'
                ],
            isInstansiDropdownOpen: false,

            get filteredInstansiList() {
                const q = (this.formData.hibah_pemberi || '').toLowerCase().trim();
                if (!q) {
                    return this.masterInstansiList.slice(0, 15);
                }
                return this.masterInstansiList.filter(inst => inst && inst.toLowerCase().includes(q));
            },

            selectInstansi(name) {
                this.formData.hibah_pemberi = name;
                this.isInstansiDropdownOpen = false;
            },

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

            // ─── Helpers ────────────────────────────────────────────────────
            clearStepError(step) {
                this.stepErrors[step] = null;
            },

            handleFileSelect(event) {
                const file = event.target.files[0];
                if (!file) { this.selectedFile = null; return; }
                const maxMb = 5;
                if (file.size > maxMb * 1024 * 1024) {
                    this.showToast('❌ File Terlalu Besar', `Ukuran file maksimal ${maxMb} MB. File Anda: ${(file.size/1024/1024).toFixed(2)} MB`, 'error');
                    event.target.value = '';
                    this.selectedFile = null;
                    return;
                }
                this.selectedFile = file;
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

            // Form Data Payload
            formData: {
                // Langkah 1: BAST & Pemberi
                tahun_perolehan: new Date().getFullYear(),
                triwulan: 'TW I',
                tipe_hibah: 'pemerintah_pusat',
                hibah_pemberi: '',
                hibah_pimpinan: '',
                hibah_alamat_pemberi: '',
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

            isEditMode: false,
            editId: null,

            init() {
                if (this.pejabatsList.length > 0) {
                    this.formData.ppk_nama = this.pejabatsList[0].nama || '';
                    this.formData.ppk_nip = this.pejabatsList[0].nip || '';
                }

                if (window.editAstapData && window.editAstapData.id) {
                    this.isEditMode = true;
                    this.editId = window.editAstapData.id;
                    this.hydrateFromEditData(window.editAstapData);
                }
            },

            hydrateFromEditData(d) {
                if (!d) return;
                const spec = (typeof d.spesifikasi_json === 'object' && d.spesifikasi_json !== null)
                    ? d.spesifikasi_json
                    : (typeof d.spesifikasi_json === 'string' ? (JSON.parse(d.spesifikasi_json) || {}) : {});
                
                const hibah = (d.hibahs && d.hibahs.length > 0) ? d.hibahs[0] : (d.hibah || {});

                // Langkah 1: BAST & Pemberi
                this.formData.tahun_perolehan = d.tahun_perolehan || hibah.tahun || new Date().getFullYear();
                this.formData.triwulan = d.triwulan || hibah.triwulan || 'TW I';
                this.formData.tipe_hibah = spec.tipe_hibah || hibah.tipe_hibah || 'pemerintah_pusat';
                this.formData.hibah_pemberi = d.hibah_pemberi || hibah.pihak_hibah || spec.pemberi_hibah || '';
                this.formData.hibah_pimpinan = spec.hibah_pimpinan || spec.pimpinan_pemberi || '';
                this.formData.hibah_alamat_pemberi = spec.hibah_alamat_pemberi || spec.alamat_pemberi || '';
                this.formData.hibah_nomor_bast = d.hibah_nomor_bast || hibah.nomor_bast || d.bast_dokumen_nomor || '';
                this.formData.hibah_tanggal_bast = d.hibah_tanggal_bast || hibah.tanggal_bast || d.bast_dokumen_tanggal || '';
                this.formData.total_realisasi = Number(d.total_realisasi || hibah.nilai_aset || 0);
                this.formData.jumlah_realisasi = this.formData.total_realisasi;
                this.formData.hibah_keterangan = d.hibah_keterangan || d.keterangan_tambahan || hibah.keterangan || '';

                // Langkah 2: Klasifikasi 108 & Barang
                this.formData.nama_barang = d.nama_barang || '';
                this.formData.jenis_astap_id = d.jenis_astap_id || null;
                this.formData.satuan = d.satuan || 'Unit';
                this.formData.jumlah_volume = d.jumlah_volume || hibah.jumlah_volume || 1;
                this.formData.is_extracomtable = Boolean(d.is_extracomtable || spec.is_extracomtable);

                if (d.jenis_astap) {
                    this.formData.jenis_aset_kode = d.jenis_astap.jenis || '';
                    this.formData.jenis_aset_nama = d.jenis_astap.nama_jenis || '';
                    this.formData.sub_rincian_kode = d.jenis_astap.sub_rincian_objek || '';
                    this.formData.sub_rincian_nama = d.jenis_astap.nama_sub_rincian_objek || '';
                    this.selectedSubSub = {
                        id: d.jenis_astap.id,
                        kode: d.jenis_astap.sub_sub_rincian_objek,
                        nama: d.jenis_astap.nama_sub_sub_rincian_objek
                    };
                }

                // Langkah 3: Penempatan Ruangan & PPK
                this.formData.unit_id = d.unit_id || '';
                this.formData.alamat_barang = d.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1';
                if (d.ppk_nama) this.formData.ppk_nama = d.ppk_nama;
                if (d.ppk_nip)  this.formData.ppk_nip  = d.ppk_nip;

                // Rehydrate Repeater Items from spec
                if (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) this.formData.tanah_items = spec.tanah_items;
                if (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) this.formData.mesin_items = spec.mesin_items;
                if (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) this.formData.gedung_items = spec.gedung_items;
                if (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) this.formData.jaringan_items = spec.jaringan_items;
                if (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) this.formData.lainnya_items = spec.lainnya_items;
                if (Array.isArray(spec.atb_items) && spec.atb_items.length > 0) this.formData.atb_items = spec.atb_items;
                if (Array.isArray(spec.kdp_items) && spec.kdp_items.length > 0) this.formData.kdp_items = spec.kdp_items;

                this.syncActiveKibTotals();
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

            // ─── isMultiItemActive: true jika repeater KIB punya data rincian ──
            get isMultiItemActive() {
                if (this.isTanah) return this.formData.tanah_items && this.formData.tanah_items.length > 0;
                if (this.isMesin) return this.formData.mesin_items && this.formData.mesin_items.length > 0;
                if (this.isGedung) return this.formData.gedung_items && this.formData.gedung_items.length > 0;
                if (this.isJaringan) return this.formData.jaringan_items && this.formData.jaringan_items.length > 0;
                if (this.isAsetLainnya) return this.formData.lainnya_items && this.formData.lainnya_items.length > 0;
                if (this.isAtb) return this.formData.atb_items && this.formData.atb_items.length > 0;
                if (this.isKdp) return this.formData.kdp_items && this.formData.kdp_items.length > 0;
                return false;
            },

            // ─── quickSelectKib: shortcut dari kartu KIB di Step 2 ────────────
            quickSelectKib(kodePrefix, namaKib) {
                // Reset cascading
                this.formData.jenis_aset_kode = '';
                this.formData.jenis_aset_nama = '';
                this.formData.sub_rincian_kode = '';
                this.formData.sub_rincian_nama = '';
                this.selectedSubSub = null;
                this.formData.jenis_astap_id = null;
                this.searchJenis108 = kodePrefix;
                this.isJenis108Open = false;

                // Cari jenis aset yang cocok
                const found = this.master108.find(j => j.kode && j.kode.startsWith(kodePrefix));
                if (found) {
                    this.formData.jenis_aset_kode = found.kode;
                    this.formData.jenis_aset_nama = found.nama;
                }
                this.adjustSatuanForKib();
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
                this.stepErrors[s] = null;

                if (s === 1) {
                    if (!this.formData.hibah_pemberi || !this.formData.hibah_pemberi.trim()) {
                        this.stepErrors[1] = 'Nama Instansi / Pemberi Hibah wajib diisi.';
                        return false;
                    }
                    if (!this.formData.hibah_nomor_bast || !this.formData.hibah_nomor_bast.trim()) {
                        this.stepErrors[1] = 'Nomor BAST / NPHD wajib diisi.';
                        return false;
                    }
                    if (!this.formData.hibah_tanggal_bast) {
                        this.stepErrors[1] = 'Tanggal BAST / NPHD wajib diisi.';
                        return false;
                    }
                    if (!this.formData.tahun_perolehan) {
                        this.stepErrors[1] = 'Tahun Pembukuan wajib diisi.';
                        return false;
                    }
                    if (!this.formData.triwulan) {
                        this.stepErrors[1] = 'Triwulan Pembukuan wajib dipilih.';
                        return false;
                    }
                    return true;
                }

                if (s === 2) {
                    if (!this.formData.jenis_astap_id && !this.formData.jenis_aset_kode) {
                        this.stepErrors[2] = 'Mohon pilih Klasifikasi Jenis Aset / Kode Barang 108 terlebih dahulu.';
                        return false;
                    }
                    if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
                        this.stepErrors[2] = 'Nama Lengkap Barang Hibah wajib diisi.';
                        return false;
                    }
                    if (!this.formData.jumlah_volume || this.formData.jumlah_volume < 1) {
                        this.stepErrors[2] = 'Volume / Kuantitas Barang minimal 1 unit.';
                        return false;
                    }
                    return true;
                }

                if (s === 3) {
                    this.syncActiveKibTotals();
                    if (!this.formData.unit_id) {
                        this.stepErrors[3] = 'Unit / Ruangan Penempatan Aset (KIR) wajib dipilih.';
                        return false;
                    }
                    if (!this.formData.total_realisasi || this.formData.total_realisasi <= 0) {
                        this.stepErrors[3] = 'Taksiran Nilai Aset harus lebih dari Rp 0. Isi di Langkah 2.';
                        return false;
                    }
                    return true;
                }

                return true;
            },

            submitForm() {
                // Validasi semua langkah terlebih dahulu
                if (!this.validateStep(1)) { this.currentStep = 1; window.scrollTo({top:0,behavior:'smooth'}); return; }
                if (!this.validateStep(2)) { this.currentStep = 2; window.scrollTo({top:0,behavior:'smooth'}); return; }
                if (!this.validateStep(3)) { this.currentStep = 3; window.scrollTo({top:0,behavior:'smooth'}); return; }

                this.syncActiveKibTotals();
                this.isSubmitting = true;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                // ── Gunakan FormData untuk mendukung file upload dokumen BAST ──
                const fd = new FormData();
                fd.append('_token', token);

                // Langkah 1: BAST & Pemberi
                fd.append('hibah_pemberi',      this.formData.hibah_pemberi);
                fd.append('hibah_nomor_bast',   this.formData.hibah_nomor_bast);
                fd.append('hibah_tanggal_bast', this.formData.hibah_tanggal_bast);
                fd.append('tahun_perolehan',    this.formData.tahun_perolehan);
                fd.append('triwulan',           this.formData.triwulan);
                if (this.formData.tipe_hibah)          fd.append('tipe_hibah',       this.formData.tipe_hibah || 'masuk');
                if (this.formData.hibah_pimpinan)      fd.append('hibah_pimpinan',   this.formData.hibah_pimpinan);
                if (this.formData.hibah_alamat_pemberi) fd.append('hibah_alamat_pemberi', this.formData.hibah_alamat_pemberi);

                // File Dokumen BAST (opsional)
                if (this.selectedFile) {
                    fd.append('dokumen_bast', this.selectedFile, this.selectedFile.name);
                }

                // Langkah 2: Klasifikasi 108 & Barang
                if (this.formData.jenis_astap_id)   fd.append('jenis_astap_id',   this.formData.jenis_astap_id);
                if (this.formData.jenis_aset_kode)  fd.append('jenis_aset_kode',  this.formData.jenis_aset_kode);
                if (this.formData.jenis_aset_nama)  fd.append('jenis_aset_nama',  this.formData.jenis_aset_nama);
                if (this.formData.sub_rincian_kode) fd.append('sub_rincian_kode', this.formData.sub_rincian_kode);
                if (this.formData.sub_rincian_nama) fd.append('sub_rincian_nama', this.formData.sub_rincian_nama);
                fd.append('nama_barang',       this.formData.nama_barang);
                fd.append('satuan',            this.formData.satuan || 'Unit');
                fd.append('jumlah_volume',     this.formData.jumlah_volume || 1);
                fd.append('total_realisasi',   this.formData.total_realisasi || 0);
                fd.append('is_extracomtable',  this.formData.is_extracomtable ? '1' : '0');

                // Spesifikasi teknis KIB (dikirim sebagai JSON string)
                const spesifikasi = {};
                if (this.isTanah && this.formData.tanah_items?.length)        spesifikasi.tanah_items   = this.formData.tanah_items;
                if (this.isMesin && this.formData.mesin_items?.length)        spesifikasi.mesin_items   = this.formData.mesin_items;
                if (this.isGedung && this.formData.gedung_items?.length)      spesifikasi.gedung_items  = this.formData.gedung_items;
                if (this.isJaringan && this.formData.jaringan_items?.length)  spesifikasi.jaringan_items = this.formData.jaringan_items;
                if (this.isAsetLainnya && this.formData.lainnya_items?.length) spesifikasi.lainnya_items = this.formData.lainnya_items;
                if (this.isAtb && this.formData.atb_items?.length)            spesifikasi.atb_items     = this.formData.atb_items;
                if (this.isKdp && this.formData.kdp_items?.length)            spesifikasi.kdp_items     = this.formData.kdp_items;
                if (Object.keys(spesifikasi).length > 0) {
                    fd.append('spesifikasi_json', JSON.stringify(spesifikasi));
                }

                // Langkah 3: Penempatan & PPK
                if (this.formData.unit_id)          fd.append('unit_id',          this.formData.unit_id);
                if (this.formData.alamat_barang)    fd.append('alamat_barang',    this.formData.alamat_barang);
                if (this.formData.ppk_nama)         fd.append('ppk_nama',         this.formData.ppk_nama);
                if (this.formData.ppk_nip)          fd.append('ppk_nip',          this.formData.ppk_nip);
                if (this.formData.hibah_keterangan) fd.append('hibah_keterangan', this.formData.hibah_keterangan);
                if (this.formData.rekening_belanja_id) fd.append('rekening_belanja_id', this.formData.rekening_belanja_id);

                const submitUrl = this.isEditMode
                    ? ('/astap/update-hibah/' + this.editId)
                    : "{{ route('astap.store_hibah') }}";

                fetch(submitUrl, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': token },
                    body: fd
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(result => {
                    this.isSubmitting = false;
                    if (result.status === 200 && result.body.success) {
                        this.showToast(
                            this.isEditMode ? '🎉 Perubahan Aset Hibah Tersimpan!' : '🎉 Aset Hibah Tersimpan!',
                            result.body.message || (this.isEditMode ? 'Perubahan data hibah berhasil disimpan.' : 'Data hibah berhasil dicatat ke inventaris RSUD.'),
                            'success'
                        );
                        setTimeout(() => { window.location.href = result.body.redirect || "{{ route('master.hibah') }}"; }, 1800);
                    } else {
                        const errMsg = result.body.message ||
                            (result.body.errors ? Object.values(result.body.errors).flat().join(' • ') : 'Gagal menyimpan data hibah.');
                        this.stepErrors[3] = errMsg;
                        this.showToast('❌ Gagal Menyimpan', errMsg, 'error');
                    }
                })
                .catch(err => {
                    this.isSubmitting = false;
                    console.error(err);
                    const msg = 'Gagal menghubungi server. Periksa koneksi internet Anda.';
                    this.stepErrors[3] = msg;
                    this.showToast('❌ Koneksi Error', msg, 'error');
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
