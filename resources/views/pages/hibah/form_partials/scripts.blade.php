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

            // ─── File Upload State ───────────────────────────────────────────
            selectedFile: null,
            existingDokumenPath: null,
            removeExistingDokumen: false,

            // ─── Per-Step Error Banners ──────────────────────────────────────
            stepErrors: { 1: null, 2: null, 3: null },

            // ─── Validation Modal Dialog State ───────────────────────────────
            showValidationModal: false,
            validationModal: {
                badge: '',
                title: '',
                message: '',
                missingFields: []
            },
            openValidationModal(title, message, missingFields = [], badge = 'Pemeriksaan Formulir') {
                this.validationModal.title = title;
                this.validationModal.message = message;
                this.validationModal.missingFields = missingFields;
                this.validationModal.badge = badge;
                this.showValidationModal = true;
            },
            closeValidationModal() {
                this.showValidationModal = false;
            },

            // ─── Verification Checklist & PPK State (Langkah 3) ──────────────
            isDataVerified: false,
            isPpkDropdownOpen: false,

            get filteredPpkList() {
                const q = (this.formData.ppk_nama || '').toLowerCase().trim();
                if (!q) return this.pejabatsList.slice(0, 10);
                return this.pejabatsList.filter(p => (p.nama || '').toLowerCase().includes(q) || (p.nip || '').includes(q));
            },

            selectPpk(p) {
                this.formData.ppk_nama = p.nama || '';
                this.formData.ppk_nip = p.nip || '';
                this.isPpkDropdownOpen = false;
            },

            onPpkInput(val) {
                this.formData.ppk_nama = val;
                this.isPpkDropdownOpen = true;
                const found = this.pejabatsList.find(p => (p.nama || '').toLowerCase() === (val || '').toLowerCase());
                if (found) {
                    this.formData.ppk_nip = found.nip || '';
                }
            },

            // Master Data
            master108: window.dbMasterJenisAstap108 || [],
            flat108: [],
            masterRekeningBelanja: window.dbRekeningBelanjas || [],
            pejabatsList: window.dbPejabats || [],
            unitsList: window.dbUnits || [],
            masterUnits: window.dbUnits || [],

            // Autocomplete Riwayat Instansi Pemberi Hibah
            masterInstansiList: ((window.dbPemberiHibahs && window.dbPemberiHibahs.length > 0)
                ? window.dbPemberiHibahs
                : [
                    {
                        nama: 'Kementerian Kesehatan Republik Indonesia',
                        pimpinan: 'Ir. Budi Gunadi Sadikin, CHFC, CLU — Menteri Kesehatan RI',
                        alamat: 'Jl. H.R. Rasuna Said Blok X-5 Kav. 4-9, Kuningan, Jakarta Selatan 12950'
                    },
                    {
                        nama: 'Dinas Kesehatan Provinsi Jawa Timur',
                        pimpinan: 'Dr. dr. Erwin Astha Triyono, Sp.PD, K-PTI, FINASIM — Kepala Dinas Kesehatan Prov. Jatim',
                        alamat: 'Jl. Ahmad Yani No. 118, Gayungan, Kota Surabaya, Jawa Timur 60231'
                    },
                    {
                        nama: 'Pemerintah Kabupaten Bondowoso',
                        pimpinan: 'Bupati Bondowoso / Sekretaris Daerah Kab. Bondowoso',
                        alamat: 'Jl. Letnan Amir Kusman No. 2, Dabasah, Kabupaten Bondowoso 68211'
                    },
                    {
                        nama: 'Donatur Swasta / Yayasan CSR',
                        pimpinan: 'Ketua / Pimpinan Lembaga Donatur Swasta',
                        alamat: 'Kabupaten Bondowoso / Jawa Timur'
                    }
                ]).map(item => {
                    if (typeof item === 'string') {
                        return { nama: item, pimpinan: '', alamat: '' };
                    }
                    return {
                        nama: item.nama || '',
                        pimpinan: item.pimpinan || '',
                        alamat: item.alamat || ''
                    };
                }),
            isInstansiDropdownOpen: false,

            get filteredInstansiList() {
                const q = (this.formData.hibah_pemberi || '').toLowerCase().trim();
                if (!q) {
                    return this.masterInstansiList.slice(0, 15);
                }
                return this.masterInstansiList.filter(inst => {
                    const n = (inst.nama || '').toLowerCase();
                    const p = (inst.pimpinan || '').toLowerCase();
                    const a = (inst.alamat || '').toLowerCase();
                    return n.includes(q) || p.includes(q) || a.includes(q);
                });
            },

            selectInstansi(target) {
                let inst = null;
                if (typeof target === 'string') {
                    inst = this.masterInstansiList.find(i => i.nama.toLowerCase() === target.toLowerCase())
                        || { nama: target, pimpinan: '', alamat: '' };
                } else if (target && typeof target === 'object') {
                    inst = target;
                }

                if (!inst) return;

                this.formData.hibah_pemberi = inst.nama || '';

                let autoFilled = false;
                if (inst.pimpinan) {
                    this.formData.hibah_pimpinan = inst.pimpinan;
                    autoFilled = true;
                }
                if (inst.alamat) {
                    this.formData.hibah_alamat_pemberi = inst.alamat;
                    autoFilled = true;
                }

                this.isInstansiDropdownOpen = false;

                if (autoFilled && typeof this.showToast === 'function') {
                    this.showToast('✓ Profil Instansi Terisi', 'Nama pimpinan dan alamat kantor otomatis diisi dari riwayat.', 'success');
                }
            },

            checkAutoFillInstansi() {
                const val = (this.formData.hibah_pemberi || '').trim().toLowerCase();
                if (!val) return;
                const match = this.masterInstansiList.find(i => i.nama.trim().toLowerCase() === val);
                if (match) {
                    let filled = false;
                    if (!this.formData.hibah_pimpinan && match.pimpinan) {
                        this.formData.hibah_pimpinan = match.pimpinan;
                        filled = true;
                    }
                    if (!this.formData.hibah_alamat_pemberi && match.alamat) {
                        this.formData.hibah_alamat_pemberi = match.alamat;
                        filled = true;
                    }
                    if (filled && typeof this.showToast === 'function') {
                        this.showToast('✓ Profil Instansi Terisi', 'Nama pimpinan dan alamat kantor otomatis diisi dari riwayat.', 'success');
                    }
                }
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
                if (!file) return;
                const maxMb = 10;
                if (file.size > maxMb * 1024 * 1024) {
                    this.showToast('❌ File Terlalu Besar', `Ukuran file maksimal ${maxMb} MB. File Anda: ${(file.size/1024/1024).toFixed(2)} MB`, 'error');
                    event.target.value = '';
                    this.selectedFile = null;
                    return;
                }
                this.selectedFile = file;
                this.removeExistingDokumen = false;
            },

            cancelSelectedFile() {
                this.selectedFile = null;
                const fi = document.getElementById('hibahDokumenInput');
                if (fi) fi.value = '';
            },

            toggleRemoveExistingFile() {
                this.removeExistingDokumen = !this.removeExistingDokumen;
                if (this.removeExistingDokumen) {
                    this.selectedFile = null;
                    const fi = document.getElementById('hibahDokumenInput');
                    if (fi) fi.value = '';
                }
            },

            isExistingFileImage() {
                if (!this.existingDokumenPath) return false;
                const ext = String(this.existingDokumenPath).split('.').pop().toLowerCase();
                return ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext);
            },

            getExistingFileName() {
                if (!this.existingDokumenPath) return '';
                return String(this.existingDokumenPath).split('/').pop();
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
                triwulan: (function() {
                    const m = new Date().getMonth() + 1;
                    if (m >= 4 && m <= 6) return 'TW II';
                    if (m >= 7 && m <= 9) return 'TW III';
                    if (m >= 10 && m <= 12) return 'TW IV';
                    return 'TW I';
                })(),
                tipe_hibah: 'masuk',
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
                        is_extracom: false,
                        mesin_nama_barang: '',
                        mesin_kode_barang: '',
                        isFilterOpen: false,
                        searchFilter: '',
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
                        isFilterOpen: false,
                        searchFilter: '',
                        gedung_luas_m2: 0,
                        gedung_kondisi: 'Baik',
                        gedung_bertingkat: 'Tidak',
                        gedung_beton: 'Beton',
                        gedung_status_tanah: 'Tanah Hak Pakai RSUD',
                        gedung_kode_aset_tanah: '1.3.1.01.01.02.013',
                        gedung_is_baru: 'Lama',
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
            isHydrating: false,

            // Hitung otomatis Triwulan dan Tahun dari tanggal dokumen BAST
            calcTriwulanFromDate(dateStr) {
                if (!dateStr) return { year: new Date().getFullYear(), tw: 'TW I' };
                let month = 1;
                let year = null;
                const str = String(dateStr).trim();

                if (/^\d{4}-\d{2}-\d{2}/.test(str)) {
                    const parts = str.split('-');
                    year = parseInt(parts[0], 10);
                    month = parseInt(parts[1], 10);
                } else if (/^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{4}/.test(str)) {
                    const parts = str.split(/[\/\-]/);
                    const d = parseInt(parts[0], 10);
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

                let tw = 'TW I';
                if (month >= 1 && month <= 3) tw = 'TW I';
                else if (month >= 4 && month <= 6) tw = 'TW II';
                else if (month >= 7 && month <= 9) tw = 'TW III';
                else if (month >= 10 && month <= 12) tw = 'TW IV';

                return { year, tw };
            },

            onTanggalBastChange(val) {
                if (this.isHydrating) return;
                if (!val) return;
                const res = this.calcTriwulanFromDate(val);
                if (res.year && res.year >= 1990 && res.year <= 2100) {
                    this.formData.tahun_perolehan = res.year;
                }
                if (res.tw) {
                    this.formData.triwulan = res.tw;
                }
            },

            init() {
                this.buildFlat108();

                if (this.pejabatsList.length > 0) {
                    this.formData.ppk_nama = this.pejabatsList[0].nama || '';
                    this.formData.ppk_nip = this.pejabatsList[0].nip || '';
                }

                if (window.editAstapData && window.editAstapData.id) {
                    this.isEditMode = true;
                    this.editId = window.editAstapData.id;
                    this.hydrateFromEditData(window.editAstapData);
                } else {
                    if (this.formData.hibah_tanggal_bast) {
                        this.onTanggalBastChange(this.formData.hibah_tanggal_bast);
                    }
                }

                this.$watch('formData.hibah_tanggal_bast', (val) => {
                    this.onTanggalBastChange(val);
                });

                this.$watch('formData.mesin_items', () => { this.syncTotalsFromItems(); }, { deep: true });
                this.$watch('formData.tanah_items', () => { this.syncTotalsFromItems(); }, { deep: true });
                this.$watch('formData.gedung_items', () => { this.syncTotalsFromItems(); }, { deep: true });
                this.$watch('formData.jaringan_items', () => { this.syncTotalsFromItems(); }, { deep: true });
                this.$watch('formData.lainnya_items', () => { this.syncTotalsFromItems(); }, { deep: true });
            },

            hydrateFromEditData(d) {
                if (!d) return;
                this.isHydrating = true;
                const spec = (typeof d.spesifikasi_json === 'object' && d.spesifikasi_json !== null)
                    ? d.spesifikasi_json
                    : (typeof d.spesifikasi_json === 'string' ? (JSON.parse(d.spesifikasi_json) || {}) : {});
                
                const hibah = (d.hibahs && d.hibahs.length > 0) ? d.hibahs[0] : (d.hibah || d.hibah_masuk || d.hibahMasuk || {});

                // Langkah 1: BAST & Pemberi
                this.formData.tahun_perolehan = d.tahun_perolehan || hibah.tahun || new Date().getFullYear();
                this.formData.triwulan = d.triwulan || hibah.triwulan || 'TW I';
                this.formData.tipe_hibah = spec.tipe_hibah || hibah.tipe_hibah || 'masuk';
                this.formData.hibah_pemberi = d.hibah_pemberi || hibah.pihak_hibah || spec.pemberi_hibah || spec.hibah_pemberi || '';
                this.formData.hibah_pimpinan = spec.hibah_pimpinan || spec.pimpinan_pemberi || '';
                this.formData.hibah_alamat_pemberi = spec.hibah_alamat_pemberi || spec.alamat_pemberi || '';
                this.formData.hibah_nomor_bast = d.hibah_nomor_bast || hibah.nomor_bast || d.bast_dokumen_nomor || spec.hibah_nomor_bast || '';
                this.formData.hibah_tanggal_bast = d.hibah_tanggal_bast || hibah.tanggal_bast || d.bast_dokumen_tanggal || spec.hibah_tanggal_bast || '';
                this.formData.total_realisasi = Number(d.total_realisasi || hibah.nilai_aset || 0);
                this.formData.jumlah_realisasi = this.formData.total_realisasi;
                this.formData.hibah_keterangan = d.hibah_keterangan || d.keterangan_tambahan || hibah.keterangan || spec.hibah_keterangan || '';

                // Berkas Dokumen BAST (jika sebelumnya sudah ada yang diunggah)
                this.existingDokumenPath = spec.dokumen_bast_path || spec.dokumen_path || hibah.dokumen_path || d.dokumen_bast_path || d.dokumen_path || '';
                this.removeExistingDokumen = false;
                this.selectedFile = null;

                // Langkah 2: Rekening Belanja & Klasifikasi 108 & Barang
                this.formData.rekening_belanja_id = d.rekening_belanja_id || null;
                if (d.rekening_belanja) {
                    this.formData.kode_rek = d.rekening_belanja.kode_rek || '';
                    this.formData.nama_belanja = d.rekening_belanja.nama_belanja || '';
                }

                this.formData.nama_barang = d.nama_barang || '';
                this.formData.jenis_astap_id = d.jenis_astap_id || null;
                this.formData.satuan = d.satuan || 'Unit';
                this.formData.jumlah_volume = d.jumlah_volume || hibah.jumlah_volume || 1;
                this.formData.is_extracomtable = Boolean(d.is_extracomtable || spec.is_extracomtable);

                if (d.jenis_astap) {
                    this.formData.jenis_aset_kode = d.jenis_astap.jenis || d.jenis_astap.kode || '';
                    this.formData.jenis_aset_nama = d.jenis_astap.nama_jenis || d.jenis_astap.nama || '';
                    this.formData.sub_rincian_kode = d.jenis_astap.sub_rincian_objek || '';
                    this.formData.sub_rincian_nama = d.jenis_astap.nama_sub_rincian_objek || '';
                    this.selectedSubSub = {
                        id: d.jenis_astap.id,
                        kode: d.jenis_astap.sub_sub_rincian_objek || d.kode_108,
                        nama: d.jenis_astap.nama_sub_sub_rincian_objek || d.nama_barang
                    };
                } else if (d.kode_108) {
                    this.formData.jenis_aset_kode = d.kode_108;
                    this.selectedSubSub = {
                        id: d.jenis_astap_id,
                        kode: d.kode_108,
                        nama: d.nama_barang
                    };
                }

                // Langkah 3: Penempatan Ruangan & PPK
                this.formData.unit_id = d.unit_id || (d.unit ? d.unit.id : '');
                this.formData.alamat_barang = d.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1';
                if (d.ppk_nama || spec.ppk_nama) this.formData.ppk_nama = d.ppk_nama || spec.ppk_nama;
                if (d.ppk_nip || spec.ppk_nip)   this.formData.ppk_nip  = d.ppk_nip || spec.ppk_nip;

                // Rehydrate Repeater Items from spec, OR fallback from registers if available
                if (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) {
                    this.formData.tanah_items = spec.tanah_items;
                } else if (spec.hak_tanah || spec.sertifikat_no) {
                    this.formData.tanah_items = [{
                        tanah_nama_barang: d.nama_barang || '',
                        tanah_kode_barang: this.selectedSubSub ? this.selectedSubSub.kode : '',
                        tanah_hak: spec.hak_tanah || 'Hak Pakai',
                        tanah_sertifikat_no: spec.sertifikat_no || '',
                        tanah_sertifikat_tgl: spec.sertifikat_tgl || '',
                        tanah_kondisi: spec.kondisi || 'Baik',
                        tanah_penggunaan: spec.penggunaan || 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                        tanah_jumlah_bidang: 1,
                        tanah_luas_m2: spec.luas_m2 || '',
                        tanah_alamat: d.alamat_barang || '',
                        tanah_nilai_fisik: Number(d.total_realisasi || 0)
                    }];
                }

                const isAsetExtracom = Boolean(d.is_extracomtable || spec.is_extracomtable || (d.category === 'EXTRACOM'));
                if (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                    this.formData.mesin_items = spec.mesin_items.map(m => ({
                        ...m,
                        is_extracom: (m.is_extracom !== undefined && m.is_extracom !== null) ? Boolean(m.is_extracom) : isAsetExtracom,
                        isFilterOpen: false,
                        searchFilter: ''
                    }));
                } else if (Array.isArray(d.registers) && d.registers.length > 0) {
                    this.formData.mesin_items = d.registers.map(r => ({
                        mesin_nama_barang: d.nama_barang || '',
                        mesin_kode_barang: this.selectedSubSub ? this.selectedSubSub.kode : '',
                        mesin_merk: r.merk || spec.merk || spec.mesin_merk || '',
                        mesin_type: r.type || spec.type || spec.mesin_type || '',
                        mesin_ukuran: r.ukuran || spec.ukuran || spec.mesin_ukuran || '',
                        mesin_no_pabrik: r.no_pabrik || spec.no_pabrik || spec.mesin_no_pabrik || '',
                        mesin_bahan: r.bahan || spec.bahan || spec.mesin_bahan || '',
                        mesin_no_rangka: r.no_rangka || spec.no_rangka || '',
                        mesin_no_mesin: r.no_mesin || spec.no_mesin || '',
                        mesin_no_bpkb: r.no_bpkb || spec.no_bpkb || '',
                        mesin_no_polisi: r.no_polisi || spec.no_polisi || '',
                        mesin_kondisi: r.kondisi || spec.kondisi || 'Baik',
                        mesin_jumlah_barang: 1,
                        mesin_satuan: d.satuan || 'Unit',
                        mesin_nilai_satuan: Number(r.harga_satuan || r.nilai_satuan || d.harga_satuan || 0),
                        mesin_administrasi_proyek: 0,
                        ruang_pemegang: r.ruang_pemegang || '',
                        is_extracom: isAsetExtracom,
                        isFilterOpen: false,
                        searchFilter: ''
                    }));
                } else if (spec.merk || spec.type || spec.no_pabrik) {
                    this.formData.mesin_items = [{
                        mesin_nama_barang: d.nama_barang || '',
                        mesin_kode_barang: this.selectedSubSub ? this.selectedSubSub.kode : '',
                        mesin_merk: spec.merk || spec.mesin_merk || '',
                        mesin_type: spec.type || spec.mesin_type || '',
                        mesin_ukuran: spec.ukuran || spec.mesin_ukuran || '',
                        mesin_no_pabrik: spec.no_pabrik || spec.mesin_no_pabrik || '',
                        mesin_bahan: spec.bahan || spec.mesin_bahan || '',
                        mesin_no_rangka: spec.no_rangka || '',
                        mesin_no_mesin: spec.no_mesin || '',
                        mesin_no_bpkb: spec.no_bpkb || '',
                        mesin_no_polisi: spec.no_polisi || '',
                        mesin_kondisi: spec.kondisi || 'Baik',
                        mesin_jumlah_barang: d.jumlah_volume || 1,
                        mesin_satuan: d.satuan || 'Unit',
                        mesin_nilai_satuan: Number(d.harga_satuan || 0),
                        mesin_administrasi_proyek: 0,
                        ruang_pemegang: '',
                        is_extracom: isAsetExtracom,
                        isFilterOpen: false,
                        searchFilter: ''
                    }];
                }

                if (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) {
                    this.formData.gedung_items = spec.gedung_items.map(g => ({
                        ...g,
                        gedung_is_baru: g.gedung_is_baru || spec.gedung_is_baru || 'Lama',
                        isFilterOpen: false,
                        searchFilter: ''
                    }));
                } else if (spec.gedung_luas_m2 || spec.gedung_bertingkat || spec.gedung_beton || spec.gedung_is_baru) {
                    this.formData.gedung_items = [{
                        gedung_nama_barang: d.nama_barang || '',
                        gedung_kode_barang: this.selectedSubSub ? this.selectedSubSub.kode : '',
                        isFilterOpen: false,
                        searchFilter: '',
                        gedung_is_baru: spec.gedung_is_baru || 'Lama',
                        gedung_luas_m2: spec.gedung_luas_m2 || spec.luas_lantai_m2 || 0,
                        gedung_kondisi: spec.kondisi || 'Baik',
                        gedung_bertingkat: spec.gedung_bertingkat || 'Tidak',
                        gedung_beton: spec.gedung_beton || 'Beton',
                        gedung_status_tanah: spec.status_tanah || 'Tanah Hak Pakai RSUD',
                        gedung_kode_aset_tanah: spec.kode_aset_tanah || '1.3.1.01.01.02.013',
                        gedung_jumlah_bangunan: d.jumlah_volume || 1,
                        gedung_satuan: d.satuan || 'Gedung',
                        gedung_nilai_fisik: Number(d.total_realisasi || 0),
                        gedung_alamat: d.alamat_barang || ''
                    }];
                }

                if (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) this.formData.jaringan_items = spec.jaringan_items;
                if (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) this.formData.lainnya_items = spec.lainnya_items;
                if (Array.isArray(spec.atb_items) && spec.atb_items.length > 0) this.formData.atb_items = spec.atb_items;
                if (Array.isArray(spec.kdp_items) && spec.kdp_items.length > 0) this.formData.kdp_items = spec.kdp_items;

                this.syncActiveKibTotals();
                this.$nextTick(() => { this.isHydrating = false; });
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
                } else {
                    this.formData.jenis_aset_kode = kodePrefix;
                    this.formData.jenis_aset_nama = namaKib;
                }
                this.adjustSatuanForKib();
                this.syncTotalsFromItems();
            },

            get kibLabel() {
                if (this.isTanah) return 'KIB A (Tanah)';
                if (this.isMesin) return 'KIB B (Peralatan & Mesin)';
                if (this.isGedung) return 'KIB C (Gedung & Bangunan)';
                if (this.isJaringan) return 'KIB D (Jalan, Irigasi & Jaringan)';
                if (this.isAsetLainnya || this.isLainnya) return 'KIB E (Aset Tetap Lainnya)';
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
                } else if (this.isAsetLainnya || this.isLainnya) {
                    this.formData.satuan = (this.formData.kib_e_default_type === 'buku') ? 'Eksemplar' : 'Buah';
                } else if (this.isAtb) {
                    this.formData.satuan = 'Lisensi';
                } else if (this.isKdp) {
                    this.formData.satuan = 'Gedung';
                } else {
                    this.formData.satuan = 'Unit';
                }
            },

            // ─── Global Extracom Switcher ───────────────────────────────────────
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

            // ─── Subtotal Calculation Helpers untuk Langkah 3 Preview ───────────
            getTanahSubtotal(item) {
                return parseFloat(item?.tanah_nilai_fisik) || 0;
            },

            getMesinSubtotal(item) {
                if (!item) return 0;
                const vol = Math.max(1, parseInt(item.mesin_jumlah_barang, 10) || 1);
                const sat = parseFloat(item.mesin_nilai_satuan) || 0;
                const adm = parseFloat(item.mesin_administrasi_proyek) || 0;
                return (vol * sat) + adm;
            },

            getGedungSubtotal(item) {
                if (!item) return 0;
                const direct = Number(item.gedung_nilai_satuan || item.gedung_nilai_fisik || 0);
                if (direct > 0) return direct;
                const p = parseFloat(item.gedung_nilai_perencanaan) || 0;
                const f = parseFloat(item.gedung_nilai_fisik) || 0;
                const w = parseFloat(item.gedung_nilai_pengawasan) || 0;
                const ap = parseFloat(item.gedung_nilai_ap || item.gedung_nilai_pip) || 0;
                return p + f + w + ap;
            },

            getJaringanSubtotal(item) {
                if (!item) return 0;
                const direct = Number(item.jaringan_nilai_satuan || item.jaringan_nilai_fisik || 0);
                if (direct > 0) return direct;
                const p = parseFloat(item.jaringan_nilai_perencanaan) || 0;
                const f = parseFloat(item.jaringan_nilai_fisik) || 0;
                const w = parseFloat(item.jaringan_nilai_pengawasan) || 0;
                const ap = parseFloat(item.jaringan_nilai_ap || item.jaringan_nilai_pip) || 0;
                return p + f + w + ap;
            },

            getLainnyaSubtotal(item) {
                if (!item) return 0;
                const vol = Math.max(1, parseInt(item.lainnya_jumlah || item.lainnya_jumlah_barang, 10) || 1);
                const sat = parseFloat(item.lainnya_nilai_satuan) || 0;
                const adm = parseFloat(item.lainnya_administrasi_proyek) || 0;
                return (vol * sat) + adm;
            },

            get isLainnya() {
                return this.isAsetLainnya;
            },

            get totalNilaiTanah() {
                if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    return this.formData.tanah_items.reduce((s, it) => s + (parseFloat(it.tanah_nilai_fisik) || 0), 0);
                }
                return Number(this.formData.total_realisasi || 0);
            },

            get totalVolumeTanah() {
                if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                    return this.formData.tanah_items.reduce((s, it) => s + Math.max(1, parseInt(it.tanah_jumlah_bidang, 10) || 1), 0);
                }
                return Math.max(1, parseInt(this.formData.jumlah_volume, 10) || 1);
            },

            get totalNilaiLainnya() {
                return this.totalNilaiAsetLainnya;
            },

            get totalVolumeLainnya() {
                return this.totalVolumeAsetLainnya;
            },

            getKibEPrefix(item) {
                if (!item) return '1.3.5';
                if (item.kib_e_type === 'kesenian') return '1.3.5.02';
                if (item.kib_e_type === 'hewan_tumbuhan') return '1.3.5.03';
                return '1.3.5.01';
            },

            buildFlat108() {
                const flat = [];
                const raw = this.master108 || [];
                if (Array.isArray(raw)) {
                    raw.forEach(j => {
                        if (!j) return;
                        const subs = j.subRincian || j.subs || [];
                        subs.forEach(s => {
                            if (!s) return;
                            const subSubs = s.subSubRincian || s.subSubs || [];
                            subSubs.forEach(ss => {
                                if (!ss) return;
                                flat.push({
                                    id: ss.id,
                                    kode: ss.kode,
                                    nama: ss.nama,
                                    jenisKode: j.kode,
                                    subKode: s.kode
                                });
                            });
                        });
                    });
                }
                this.flat108 = flat;
            },

            filterJenisAstap108(prefix, query, isOpen) {
                if (!isOpen) return [];
                const q = (query || '').toLowerCase().trim();
                const results = [];
                const list = this.flat108 || [];
                const pfx = prefix || '';

                for (let i = 0; i < list.length; i++) {
                    const it = list[i];
                    if (!it || !it.kode || !it.kode.startsWith(pfx)) continue;
                    if (!q || (it.nama && it.nama.toLowerCase().includes(q)) || (it.kode && it.kode.includes(q))) {
                        results.push(it);
                        if (results.length >= 5) break;
                    }
                }

                if (results.length === 0 && pfx.length > 5) {
                    const parentPrefix = pfx.substring(0, 5);
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

            select108ForItem(item, opt, type) {
                if (type === 'tanah') {
                    item.tanah_kode_barang = opt.kode;
                    item.tanah_nama_barang = opt.nama;
                    item.searchFilter = opt.nama;
                } else if (type === 'mesin') {
                    item.mesin_kode_barang = opt.kode;
                    item.mesin_nama_barang = opt.nama;
                    item.searchFilter = opt.nama;
                } else if (type === 'gedung') {
                    item.gedung_kode_barang = opt.kode;
                    item.gedung_nama_barang = opt.nama;
                    item.gedung_nama_bangunan = opt.nama;
                    item.searchFilter = opt.nama;
                } else if (type === 'jaringan') {
                    item.jaringan_kode_barang = opt.kode;
                    item.jaringan_nama_barang = opt.nama;
                    item.searchFilter = opt.nama;
                } else if (type === 'lainnya') {
                    item.lainnya_kode_barang = opt.kode;
                    item.lainnya_nama_barang = opt.nama;
                    item.searchFilter = opt.nama;
                }
                item.isFilterOpen = false;
                this.syncTotalsFromItems();
            },

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

            syncTotalsFromItems() {
                this.syncActiveKibTotals();
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
                    if (first.tanah_nama_barang) this.formData.nama_barang = first.tanah_nama_barang;
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
                    return this.formData.mesin_items.reduce((sum, item) => sum + Math.max(1, parseInt(item.mesin_jumlah_barang, 10) || 1), 0);
                }
                return Math.max(1, parseInt(this.formData.jumlah_volume, 10) || 1);
            },

            getMesinSubtotal(item) {
                const vol = Math.max(1, parseInt(item.mesin_jumlah_barang, 10) || 1);
                return (vol * Number(item.mesin_nilai_satuan || 0)) + Number(item.mesin_administrasi_proyek || 0);
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
                    if (first.mesin_nama_barang) this.formData.nama_barang = first.mesin_nama_barang;
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
                if (unit && unit.id) {
                    this.formData.unit_id = unit.id;
                }
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
                    return this.formData.gedung_items.reduce((sum, item) => sum + Math.max(1, parseInt(item.gedung_jumlah_bangunan, 10) || 1), 0);
                }
                return Math.max(1, parseInt(this.formData.jumlah_volume, 10) || 1);
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
                    if (first.gedung_nama_barang) this.formData.nama_barang = first.gedung_nama_barang;
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
                    return this.formData.jaringan_items.reduce((sum, item) => sum + Math.max(1, parseInt(item.jaringan_jumlah, 10) || 1), 0);
                }
                return Math.max(1, parseInt(this.formData.jumlah_volume, 10) || 1);
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
                    if (first.jaringan_nama_barang) this.formData.nama_barang = first.jaringan_nama_barang;
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
                    return this.formData.lainnya_items.reduce((sum, item) => sum + Math.max(1, parseInt(item.lainnya_jumlah_barang || item.lainnya_jumlah, 10) || 1), 0);
                }
                return Math.max(1, parseInt(this.formData.jumlah_volume, 10) || 1);
            },

            getLainnyaSubtotal(item) {
                const vol = Math.max(1, parseInt(item.lainnya_jumlah_barang || item.lainnya_jumlah, 10) || 1);
                return (vol * Number(item.lainnya_nilai_satuan || 0)) + Number(item.lainnya_administrasi_proyek || 0);
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
                    const nLain = first.lainnya_nama_barang || first.lainnya_judul;
                    if (nLain) this.formData.nama_barang = nLain;
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
                    const totalNilai = this.totalNilaiTanah;
                    this.formData.total_realisasi = totalNilai;
                    this.formData.jumlah_realisasi = totalNilai;
                    this.formData.jumlah_volume = this.totalVolumeTanah;
                } else if (this.isMesin) {
                    this.syncMesinFieldsToMain();
                    const totalNilai = this.totalNilaiMesin;
                    this.formData.total_realisasi = totalNilai;
                    this.formData.jumlah_realisasi = totalNilai;
                    this.formData.jumlah_volume = this.totalVolumeMesin;
                } else if (this.isGedung) {
                    this.syncGedungFieldsToMain();
                    const totalNilai = this.totalNilaiGedung;
                    this.formData.total_realisasi = totalNilai;
                    this.formData.jumlah_realisasi = totalNilai;
                    this.formData.jumlah_volume = this.totalVolumeGedung;
                } else if (this.isJaringan) {
                    this.syncJaringanFieldsToMain();
                    const totalNilai = this.totalNilaiJaringan;
                    this.formData.total_realisasi = totalNilai;
                    this.formData.jumlah_realisasi = totalNilai;
                    this.formData.jumlah_volume = this.totalVolumeJaringan;
                } else if (this.isAsetLainnya || this.isLainnya) {
                    this.syncLainnyaFieldsToMain();
                    const totalNilai = this.totalNilaiLainnya;
                    this.formData.total_realisasi = totalNilai;
                    this.formData.jumlah_realisasi = totalNilai;
                    this.formData.jumlah_volume = this.totalVolumeLainnya;
                } else if (this.isAtb) {
                    this.syncAtbFieldsToMain();
                    const totalNilai = this.totalNilaiAtb;
                    this.formData.total_realisasi = totalNilai;
                    this.formData.jumlah_realisasi = totalNilai;
                    this.formData.jumlah_volume = this.totalVolumeAtb;
                } else if (this.isKdp) {
                    this.syncKdpFieldsToMain();
                    const totalNilai = this.totalNilaiKdp;
                    this.formData.total_realisasi = totalNilai;
                    this.formData.jumlah_realisasi = totalNilai;
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
                const missing = [];

                if (s === 1) {
                    if (!this.formData.hibah_pemberi || !this.formData.hibah_pemberi.trim()) {
                        missing.push({
                            label: 'Nama Instansi / Pemberi Hibah',
                            detail: 'Nama instansi, kementerian, dinas, atau pihak ketiga yang menyerahkan hibah wajib diisi.'
                        });
                    }
                    if (!this.formData.hibah_nomor_bast || !this.formData.hibah_nomor_bast.trim()) {
                        missing.push({
                            label: 'Nomor BAST / NPHD',
                            detail: 'Nomor naskah Berita Acara Serah Terima atau NPHD wajib diisi sebagai rujukan legalitas.'
                        });
                    }
                    if (!this.formData.hibah_tanggal_bast) {
                        missing.push({
                            label: 'Tanggal BAST / NPHD',
                            detail: 'Tanggal naskah BAST / serah terima hibah wajib ditentukan.'
                        });
                    }
                    if (!this.formData.tahun_perolehan) {
                        missing.push({
                            label: 'Tahun Pembukuan Perolehan',
                            detail: 'Tahun anggaran pembukuan aset hibah wajib ditentukan.'
                        });
                    }
                    if (!this.formData.triwulan) {
                        missing.push({
                            label: 'Triwulan Pembukuan',
                            detail: 'Triwulan periode pembukuan (TW I - TW IV) wajib dipilih.'
                        });
                    }

                    if (missing.length > 0) {
                        this.stepErrors[1] = missing.map(m => m.label).join(', ') + ' wajib diisi.';
                        this.openValidationModal(
                            'Data Dokumen BAST & Pemberi Belum Lengkap',
                            'Beberapa kolom informasi legalitas hibah di Langkah 1 wajib dilengkapi sebelum melanjutkan:',
                            missing,
                            'LANGKAH 1 — BAST & PEMBERI HIBAH'
                        );
                        return false;
                    }
                    return true;
                }

                if (s === 2) {
                    if (!this.formData.jenis_astap_id && !this.formData.jenis_aset_kode) {
                        missing.push({
                            label: 'Klasifikasi Kode Barang 108 / Kelompok KIB',
                            detail: 'Pilih salah satu shortcut kategori KIB (Tanah, Mesin, Gedung, Jaringan, Lainnya) atau filter kode 108.'
                        });
                    }
                    if (!this.formData.nama_barang || !this.formData.nama_barang.trim()) {
                        missing.push({
                            label: 'Nama Lengkap Barang Hibah',
                            detail: 'Tuliskan nama barang atau peralatan hibah secara jelas dan lengkap.'
                        });
                    }
                    if (!this.formData.jumlah_volume || Number(this.formData.jumlah_volume) < 1) {
                        missing.push({
                            label: 'Volume / Kuantitas Barang Tidak Valid',
                            detail: 'Kuantitas fisik barang hibah tidak boleh 0 atau bernilai minus (minimal 1 unit).'
                        });
                    }

                    // ── Validasi Kuantitas Tiap Rincian Fisik (Multi-Item) ──
                    if (this.isMesin && Array.isArray(this.formData.mesin_items)) {
                        this.formData.mesin_items.forEach((item, idx) => {
                            if (!item.mesin_jumlah_barang || Number(item.mesin_jumlah_barang) < 1) {
                                missing.push({
                                    label: `Kuantitas Item Mesin #${idx + 1} (${item.mesin_merk || 'Mesin'}) Tidak Boleh 0 atau Minus`,
                                    detail: 'Jumlah kuantitas fisik harus minimal 1 unit.'
                                });
                            }
                        });
                    } else if (this.isAsetLainnya && Array.isArray(this.formData.lainnya_items)) {
                        this.formData.lainnya_items.forEach((item, idx) => {
                            const q = item.lainnya_jumlah || item.lainnya_jumlah_barang;
                            if (!q || Number(q) < 1) {
                                missing.push({
                                    label: `Volume Item Lainnya #${idx + 1} Tidak Boleh 0 atau Minus`,
                                    detail: 'Jumlah volume fisik harus minimal 1 unit.'
                                });
                            }
                        });
                    } else if (this.isTanah && Array.isArray(this.formData.tanah_items)) {
                        this.formData.tanah_items.forEach((item, idx) => {
                            if (!item.tanah_jumlah_bidang || Number(item.tanah_jumlah_bidang) < 1) {
                                missing.push({
                                    label: `Bidang Tanah #${idx + 1} Tidak Boleh 0 atau Minus`,
                                    detail: 'Jumlah bidang tanah minimal 1 bidang.'
                                });
                            }
                        });
                    } else if (this.isGedung && Array.isArray(this.formData.gedung_items)) {
                        this.formData.gedung_items.forEach((item, idx) => {
                            if (!item.gedung_jumlah_bangunan || Number(item.gedung_jumlah_bangunan) < 1) {
                                missing.push({
                                    label: `Volume Bangunan #${idx + 1} Tidak Boleh 0 atau Minus`,
                                    detail: 'Jumlah bangunan minimal 1 unit.'
                                });
                            }
                        });
                    } else if (this.isJaringan && Array.isArray(this.formData.jaringan_items)) {
                        this.formData.jaringan_items.forEach((item, idx) => {
                            if (!item.jaringan_jumlah || Number(item.jaringan_jumlah) < 1) {
                                missing.push({
                                    label: `Volume Ruas #${idx + 1} Tidak Boleh 0 atau Minus`,
                                    detail: 'Jumlah ruas minimal 1 ruas.'
                                });
                            }
                        });
                    }
                    if (!this.formData.total_realisasi || this.formData.total_realisasi <= 0) {
                        missing.push({
                            label: 'Taksiran Nilai Wajar Perolehan Hibah',
                            detail: 'Taksiran nilai aset hibah harus lebih besar dari Rp 0 (sesuai naskah BAST / appraisal).'
                        });
                    }

                    // ─── Validasi Batas Nilai Akuntansi (Reguler > Rp 300.000 vs Ekstrakomtabel <= Rp 300.000) ───
                    if (this.isMesin || this.isAsetLainnya) {
                        const isExtracom = Boolean(this.formData.is_extracomtable);

                        if (this.isMesin && Array.isArray(this.formData.mesin_items) && this.formData.mesin_items.length > 0) {
                            this.formData.mesin_items.forEach((item, idx) => {
                                const val = Number(item.mesin_nilai_satuan || 0);
                                if (!isExtracom && val <= 300000) {
                                    missing.push({
                                        label: `Nilai Satuan Item #${idx + 1} (${item.mesin_merk || 'Mesin'}) Tidak Sesuai`,
                                        detail: `Aset Tetap Reguler wajib memiliki nilai satuan > Rp 300.000 (Ditemukan: Rp ${val.toLocaleString('id-ID')}). Silakan pilih status Ekstrakomtabel jika barang bernilai ≤ Rp 300.000.`
                                    });
                                } else if (isExtracom && val > 300000) {
                                    missing.push({
                                        label: `Nilai Satuan Item #${idx + 1} (${item.mesin_merk || 'Mesin'}) Melebihi Batas`,
                                        detail: `Barang Ekstrakomtabel maksimal bernilai Rp 300.000 (Ditemukan: Rp ${val.toLocaleString('id-ID')}). Silakan pilih status Aset Tetap Reguler jika barang bernilai > Rp 300.000.`
                                    });
                                }
                            });
                        } else if (this.isAsetLainnya && Array.isArray(this.formData.lainnya_items) && this.formData.lainnya_items.length > 0) {
                            this.formData.lainnya_items.forEach((item, idx) => {
                                const val = Number(item.lainnya_nilai_satuan || 0);
                                if (!isExtracom && val <= 300000) {
                                    missing.push({
                                        label: `Nilai Satuan Item #${idx + 1} (${item.lainnya_nama_barang || item.lainnya_judul || 'Aset Lainnya'}) Tidak Sesuai`,
                                        detail: `Aset Tetap Reguler wajib memiliki nilai satuan > Rp 300.000 (Ditemukan: Rp ${val.toLocaleString('id-ID')}). Silakan pilih status Ekstrakomtabel jika barang bernilai ≤ Rp 300.000.`
                                    });
                                } else if (isExtracom && val > 300000) {
                                    missing.push({
                                        label: `Nilai Satuan Item #${idx + 1} (${item.lainnya_nama_barang || item.lainnya_judul || 'Aset Lainnya'}) Melebihi Batas`,
                                        detail: `Barang Ekstrakomtabel maksimal bernilai Rp 300.000 (Ditemukan: Rp ${val.toLocaleString('id-ID')}). Silakan pilih status Aset Tetap Reguler jika barang bernilai > Rp 300.000.`
                                    });
                                }
                            });
                        } else {
                            // Single item fallback
                            const vol = Number(this.formData.jumlah_volume) || 1;
                            const unitPrice = Math.round((Number(this.formData.total_realisasi) || 0) / vol);
                            if (!isExtracom && unitPrice <= 300000) {
                                missing.push({
                                    label: 'Nilai Satuan Aset Reguler Wajib > Rp 300.000',
                                    detail: `Aset Tetap Reguler (Kapitalisasi) wajib bernilai satuan > Rp 300.000 (Saat ini: Rp ${unitPrice.toLocaleString('id-ID')}). Silakan alihkan status ke Ekstrakomtabel (Extracom).`
                                });
                            } else if (isExtracom && unitPrice > 300000) {
                                missing.push({
                                    label: 'Nilai Satuan Ekstrakomtabel Maksimal Rp 300.000',
                                    detail: `Barang Ekstrakomtabel maksimal bernilai satuan Rp 300.000 (Saat ini: Rp ${unitPrice.toLocaleString('id-ID')}). Silakan alihkan status ke Aset Tetap Reguler.`
                                });
                            }
                        }
                    }

                    if (missing.length > 0) {
                        this.stepErrors[2] = missing.map(m => m.label).join(', ') + ' wajib diisi.';
                        this.openValidationModal(
                            'Data Klasifikasi 108 & Nilai Akuntansi Belum Sesuai',
                            'Periksa dan sesuaikan klasifikasi aset serta batasan nilai kapitalisasi akuntansi berikut sebelum melanjutkan:',
                            missing,
                            'LANGKAH 2 — KLASIFIKASI 108 & SPESIFIKASI'
                        );
                        return false;
                    }
                    return true;
                }

                if (s === 3) {
                    this.syncActiveKibTotals();
                    if (!this.formData.ppk_nama || !this.formData.ppk_nama.trim()) {
                        missing.push({
                            label: 'Nama PPK / Pejabat Penandatangan',
                            detail: 'Pilih atau ketik nama Pejabat Pembuat Komitmen (PPK) yang berwenang.'
                        });
                    }
                    if (!this.formData.total_realisasi || this.formData.total_realisasi <= 0) {
                        missing.push({
                            label: 'Taksiran Nilai Perolehan Aset',
                            detail: 'Taksiran nilai aset hibah tidak boleh Rp 0. Periksa kembali di Langkah 2.'
                        });
                    }
                    if (!this.isDataVerified) {
                        missing.push({
                            label: 'Pernyataan Keabsahan Data Belum Dicentang',
                            detail: 'Centang kotak konfirmasi legalitas & keabsahan data di bagian bawah Langkah 3 sebelum menyimpan.'
                        });
                    }

                    if (missing.length > 0) {
                        this.stepErrors[3] = missing.map(m => m.label).join(', ') + ' wajib diisi.';
                        this.openValidationModal(
                            'Verifikasi Data Hibah Belum Lengkap',
                            'Periksa dan lengkapi data penempatan ruangan serta konfirmasi keabsahan sebelum menyimpan data:',
                            missing,
                            'LANGKAH 3 — VERIFIKASI & LEGALITAS'
                        );
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

                // File Dokumen BAST (opsional / penggantian)
                if (this.selectedFile) {
                    fd.append('dokumen_bast', this.selectedFile, this.selectedFile.name);
                } else if (this.removeExistingDokumen) {
                    fd.append('remove_dokumen_bast', '1');
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
