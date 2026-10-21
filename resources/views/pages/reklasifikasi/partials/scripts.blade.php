<!-- PUSTAKA EXCEL DENGAN OFFLINE FALLBACK -->
<script src="{{ asset('js/xlsx.bundle.js') }}"></script>
<script>
    if (typeof XLSX === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"><\/script>');
    }
</script>

<!-- ALPINE.JS SCRIPTS MASTER REKLASIFIKASI (PMDN 108 & SPEK DINAMIS) -->
<script>
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : \App\Models\JenisAstap::getNested108());
    window.kandidatAstaps = @json($kandidatAstaps);
    window.templateRows = @json($templateRows);

    function masterReklasifikasi() {
        return {
            activeTab: (new URLSearchParams(window.location.search).get('tab') === 'log') ? 'log' : 'matriks',
            showModalTambah: false,
            showModalDetail: false,
            showConfirmDelete: false,
            showPanduanModal: false,
            panduanSearch: '',
            panduanFilterKib: 'all',
            deleteTargetId: null,
            deleteTargetName: '',
            isSubmitting: false,
            detailItem: null,
            selectedAstap: null,

            // State Dropdown Autocomplete PMDN 108
            searchReklasSubRincian: '',
            isReklasSubRincianOpen: false,
            reklasSubRincianKode: '',
            reklasSubRincianNama: '',

            searchReklasSubSubRincian: '',
            isReklasSubSubRincianOpen: false,
            reklasSubSubRincianKode: '',
            reklasSubSubRincianNama: '',

            // State Autocomplete Mitra Rekanan Kemitraan (Sinkron dengan Form Kemitraan)
            masterMitraList: (window.dbMitraKemitraans && Array.isArray(window.dbMitraKemitraans)) ? [...window.dbMitraKemitraans] : [],
            isReklasMitraDropdownOpen: false,

            init() {
                if (!this.masterMitraList || this.masterMitraList.length === 0) {
                    fetch('/api/kemitraan/mitras')
                        .then(res => res.json())
                        .then(res => {
                            if (res && res.success && Array.isArray(res.data)) {
                                this.masterMitraList = res.data;
                            }
                        })
                        .catch(err => console.log('Fetch mitras silent fallback', err));
                }
            },

            get filteredReklasMitraList() {
                const q = (this.formData?.spekBaru?.kemitraan_mitra || '').toLowerCase().trim();
                if (!q) return this.masterMitraList.slice(0, 15);
                return this.masterMitraList.filter(m => m && m.nama && m.nama.toLowerCase().includes(q));
            },

            onReklasMitraInput(val) {
                const q = (val !== undefined ? val : (this.formData?.spekBaru?.kemitraan_mitra || '')).trim().toLowerCase();
                if (!q) return;
                const match = this.masterMitraList.find(m => m && m.nama && m.nama.trim().toLowerCase() === q);
                if (match) {
                    if (match.pimpinan && !this.formData.spekBaru.kemitraan_pimpinan) {
                        this.formData.spekBaru.kemitraan_pimpinan = match.pimpinan;
                    }
                    if (match.alamat && !this.formData.spekBaru.kemitraan_alamat) {
                        this.formData.spekBaru.kemitraan_alamat = match.alamat;
                    }
                }
            },

            selectReklasMitra(mitra) {
                if (!this.formData.spekBaru) this.formData.spekBaru = {};
                if (typeof mitra === 'string') {
                    this.formData.spekBaru.kemitraan_mitra = mitra;
                    const match = this.masterMitraList.find(m => m && m.nama && m.nama.trim().toLowerCase() === mitra.trim().toLowerCase());
                    if (match) {
                        if (match.pimpinan) this.formData.spekBaru.kemitraan_pimpinan = match.pimpinan;
                        if (match.alamat) this.formData.spekBaru.kemitraan_alamat = match.alamat;
                    }
                } else if (mitra && typeof mitra === 'object') {
                    this.formData.spekBaru.kemitraan_mitra = mitra.nama || '';
                    if (mitra.pimpinan) this.formData.spekBaru.kemitraan_pimpinan = mitra.pimpinan;
                    if (mitra.alamat) this.formData.spekBaru.kemitraan_alamat = mitra.alamat;
                }
                this.isReklasMitraDropdownOpen = false;
            },

            openPanduan(prefix = '') {
                this.panduanSearch = prefix || '';
                this.panduanFilterKib = 'all';
                this.showPanduanModal = true;
            },

            get filteredPanduanList() {
                const q = (this.panduanSearch || '').toLowerCase().trim();
                const kib = this.panduanFilterKib;
                return this.panduanList.filter(item => {
                    const matchKib = (kib === 'all' || item.kib === kib);
                    if (!matchKib) return false;
                    if (!q) return true;
                    return item.prefix.toLowerCase().includes(q) ||
                           item.nama.toLowerCase().includes(q) ||
                           item.contohRs.toLowerCase().includes(q) ||
                           item.subRincian.toLowerCase().includes(q);
                });
            },

            panduanList: [
                {
                    prefix: '1.3.1.01',
                    kib: 'KIB A',
                    nama: 'TANAH',
                    contohRs: 'Tanah Bangunan Utama RSUD, Rumah Dinas Direktur/Dokter, Mess Pegawai RS, Pos Satpam, Area Parkir Pengunjung & Ambulans.',
                    subRincian: 'Tanah Bangunan Gedung Tempat Kerja, Tanah Rumah Dinas, Lapangan.'
                },
                {
                    prefix: '1.3.2.01',
                    kib: 'KIB B',
                    nama: 'ALAT BESAR',
                    contohRs: 'Genset Utama / Pembangkit Listrik Sentral Darurat RS, Forklift Gudang Farmasi/Logistik, Kompresor Sentral Gas Medis, Pompa Sedot Sentral IPAL / Pengolahan Limbah Cair RS.',
                    subRincian: 'Tractor, Excavator, Grader, Loader, Alat Pengangkat (Forklift, Crane), Mesin Proses, Compressor, Electric Generating Set (Genset), Pompa.'
                },
                {
                    prefix: '1.3.2.02',
                    kib: 'KIB B',
                    nama: 'ALAT ANGKUTAN',
                    contohRs: 'Mobil Ambulans Emergency 118, Mobil Jenazah, Mobil Operasional Dinas Direksi, Sepeda Motor Kurir Obat/Dokumen, Mobil Layanan Farmasi Keliling, Truk Angkut Sampah RS.',
                    subRincian: 'Kendaraan Dinas Bermotor, Ambulans, Truk, Sepeda Motor, Kendaraan Operasional.'
                },
                {
                    prefix: '1.3.2.03',
                    kib: 'KIB B',
                    nama: 'ALAT BENGKEL DAN ALAT UKUR',
                    contohRs: 'Mesin Las Listrik IPSRS (Pemeliharaan Sarana RS), Mesin Bubut/Gerinda Bengkel RS, Multitester Kalibrasi Alat Medis, Timbangan Kalibrasi Presisi.',
                    subRincian: 'Alat Bengkel Bermesin/Tak Bermesin, Alat Ukur Universal, Alat Ukur Elektronik.'
                },
                {
                    prefix: '1.3.2.04',
                    kib: 'KIB B',
                    nama: 'ALAT PERTANIAN',
                    contohRs: 'Mesin Potong Rumput Halaman RS, Mesin Babat Rumput Gendong, Sprinkler Penyiram Rumput Taman Rumah Sakit.',
                    subRincian: 'Alat Pengolahan Tanah, Alat Pemeliharaan Tanaman.'
                },
                {
                    prefix: '1.3.2.05',
                    kib: 'KIB B',
                    nama: 'ALAT KANTOR DAN RUMAH TANGGA',
                    contohRs: 'Meja & Kursi Kerja Dokter/Perawat, Lemari Besi Rekam Medis (RM), AC Split Bangsal, Kulkas Penyimpan Vaksin/Obat, Water Dispenser Pasien, Brankas Keuangan RS, Lemari Narkotika.',
                    subRincian: 'Alat Kantor (Meja, Kursi, Lemari, Filing Cabinet), Alat Rumah Tangga (AC, Kulkas, Kipas Angin, Dispenser).'
                },
                {
                    prefix: '1.3.2.06',
                    kib: 'KIB B',
                    nama: 'ALAT STUDIO, KOMUNIKASI DAN PEMANCAR',
                    contohRs: 'Handy Talkie (HT) Satpam/IPSRS, PABX Sentral Telepon RS, Sound System Informasi Ruang Tunggu, Kamera CCTV Keamanan RS, TV LED Antrian Pasien.',
                    subRincian: 'Alat Komunikasi Telepon/HT, Alat Studio Video/Audio, Alat Pemancar Informasi.'
                },
                {
                    prefix: '1.3.2.07',
                    kib: 'KIB B',
                    nama: 'ALAT KEDOKTERAN DAN KESEHATAN',
                    contohRs: 'USG 4D/Doppler, Mesin Rontgen X-Ray, CT-Scan, Defibrillator (DC Shock), Syringe Pump, Infusion Pump, Bed Pasien Elektrik (ICU/Rawat Inap), Ventilator, Mesin Anastesi, Dental Chair Unit, Lampu Operasi.',
                    subRincian: 'Alat Kedokteran Umum, Radiologi, Bedah, Kebidanan/Kandungan, Gigi, Mata, THT, Fisioterapi, Anastesi.'
                },
                {
                    prefix: '1.3.2.08',
                    kib: 'KIB B',
                    nama: 'ALAT LABORATORIUM',
                    contohRs: 'Hematology Analyzer, Clinical Chemistry Analyzer, Mikroskop Binokuler Elektrik, Centrifuge Darah/Urin, Autoclave Sterilisasi Laboratorium, Inkubator Bakteri, Biosafety Cabinet.',
                    subRincian: 'Alat Lab Kimia, Alat Lab Biologi/Medis, Alat Lab Patologi Anatomi/Klinik.'
                },
                {
                    prefix: '1.3.2.09',
                    kib: 'KIB B',
                    nama: 'ALAT PERSENJATAAN',
                    contohRs: 'Senjata Pelumpuh Gas Air Mata Satpam, Borgol Khusus Pasien Gaduh Gelisah / Psikiatri, Perlengkapan Keamanan Khusus.',
                    subRincian: 'Senjata Pelumpuh, Borgol, Alat Pengamanan Fisik.'
                },
                {
                    prefix: '1.3.2.10',
                    kib: 'KIB B',
                    nama: 'KOMPUTER',
                    contohRs: 'Komputer Server Database SIMRS, PC All-in-One Kasir & Loket Pendaftaran, Laptop Dokter/Rekam Medis, Printer Resep / Label Barcode NIBAR, Scanner Dokumen BPJS, Switch & Router Jaringan LAN.',
                    subRincian: 'Komputer PC/Unit, Laptop/Notebook, Peralatan Jaringan, Printer, Scanner, UPS Server.'
                },
                {
                    prefix: '1.3.2.11 s/d 1.3.2.19',
                    kib: 'KIB B',
                    nama: 'ALAT EKSPLORASI S/D OLAH RAGA',
                    contohRs: 'Phantom / Boneka Manekin CPR Pelatihan Medis Diklat, Alat Treadmill Uji Jantung / Fisioterapi, Matras Senam Hamil, Peralatan Olah Raga Karyawan.',
                    subRincian: 'Alat Peraga Medis, Alat Olah Raga, Alat Keselamatan Kerja.'
                },
                {
                    prefix: '1.3.3.01',
                    kib: 'KIB C',
                    nama: 'BANGUNAN GEDUNG',
                    contohRs: 'Gedung IGD, Gedung Poliklinik Terpadu, Gedung Rawat Inap / Paviliun, Bangunan Instalasi Farmasi, Selasar Penghubung Ruangan, Pos Satpam, Musholla RS, Pagar Keliling RS.',
                    subRincian: 'Bangunan Gedung Tempat Kerja, Bangunan Fasilitas Kesehatan, Pos Keamanan, Pagar.'
                },
                {
                    prefix: '1.3.4.01 s/d 1.3.4.04',
                    kib: 'KIB D',
                    nama: 'JALAN, IRIGASI DAN JARINGAN',
                    contohRs: 'Jalan Aspal Akses Masuk IGD/Ambulans, Saluran Drainase Air Hujan RS, Instalasi Pengolahan Air Limbah (IPAL), Jaringan Pipa Gas Oksigen Sentral Medis, Jaringan Kabel Fiber Optik SIMRS.',
                    subRincian: 'Jalan dan Jembatan, Bangunan Air/Drainase, Instalasi Listrik/Pipa Medis, Jaringan Komunikasi/Data.'
                },
                {
                    prefix: '1.3.5.01',
                    kib: 'KIB E',
                    nama: 'BUKU / KEPUSTAKAAN',
                    contohRs: 'Buku Referensi Medis Kedokteran, Buku Pedoman Farmakope, Jurnal Ilmiah Akreditasi RS, Buku Pedoman Standar Pelayanan Minimal (SPM).',
                    subRincian: 'Buku Umum, Buku Khusus Medis, Terbitan Berkala/Jurnal.'
                },
                {
                    prefix: '1.3.5.02',
                    kib: 'KIB E',
                    nama: 'BARANG BERCORAK KESENIAN',
                    contohRs: 'Lukisan Hiasan Dinding Ruang Rapat Direksi/Auditorium, Patung Dekorasi Taman Rumah Sakit, Ornamen Kesenian Daerah.',
                    subRincian: 'Barang Kesenian Daerah, Seni Rupa, Lukisan.'
                },
                {
                    prefix: '1.3.5.03',
                    kib: 'KIB E',
                    nama: 'HEWAN',
                    contohRs: 'Tikus/Mencit Putih untuk Uji Coba Laboratorium Farmasi/Medis, Anjing Pelacak Pengamanan Wilayah Rumah Sakit.',
                    subRincian: 'Hewan Pengaman, Hewan Laboratorium, Hewan Ternak.'
                },
                {
                    prefix: '1.3.5.04 s/d 1.3.5.07',
                    kib: 'KIB E',
                    nama: 'BIOTA, TANAMAN & RENOVASI',
                    contohRs: 'Pohon Peneduh & Tanaman Hias Taman Rumah Sakit, Biota Akuarium Terapi Pasien, Aset Renovasi pada Bangunan Sewa/Bukan Milik Sendiri.',
                    subRincian: 'Biota Perairan, Tanaman Hias, Aset Tetap Renovasi.'
                },
                {
                    prefix: '1.3.6.01',
                    kib: 'KIB F',
                    nama: 'KONSTRUKSI DALAM PENGERJAAN (KDP)',
                    contohRs: 'Proyek Pembangunan Gedung Baru / Ruang Operasi yang saat ini masih dalam proses pekerjaan fisik dan belum 100% rampung (belum terbit BAST selesai).',
                    subRincian: 'KDP Gedung dan Bangunan, KDP Jalan/Instalasi.'
                },
                {
                    prefix: '1.5.1 s/d 1.5.4',
                    kib: 'ASET LAINNYA',
                    nama: 'ASET TIDAK BERWUJUD (ATB) & ASET LAIN',
                    contohRs: 'Lisensi Software SIMRS (Sistem Informasi Manajemen Rumah Sakit), Lisensi Database Oracle / Windows Server, Hak Cipta Aplikasi Antrian Online, Aset Rusak Berat Menunggu SK Penghapusan BPKAD.',
                    subRincian: 'Aset Tidak Berwujud (ATB), Kemitraan Pihak Ketiga, Aset Lain-Lain.'
                },
                {
                    prefix: 'KOREKSI',
                    kib: 'KOREKSI',
                    nama: 'KOREKSI & PENYEIMBANG',
                    contohRs: 'Koreksi Hibah Mesin Hemodialisa Kemenkes, Pengalihan barang nilai di bawah kapitalisasi (< Rp 300.000 / Rp 500.000) ke Ekstrakomptabel, Koreksi Audit BPK.',
                    subRincian: 'Koreksi Hibah, Koreksi Ekstrakomptabel, Koreksi Lain-Lain.'
                }
            ],

            skpdDirectory: @json($dbSkpdDirectory ?? []),
            skpdList: @json($dbSkpdAsals ?? []),
            dbPejabats: @json($dbPejabats ?? []),
            isSkpdDropdownOpen: false,
            isPejabatTujuanDropdownOpen: false,
            isPejabatAsalDropdownOpen: false,
            selectedDokumenFile: null,
            kemitraanSubGroupOverride: null,

            formData: {
                astap_id: '',
                nama_barang: '',
                jenis_reklas: 'KOREKSI_REKENING',
                jenis_reklasifikasi_asal_id: '',
                jenis_reklasifikasi_tujuan_id: '',
                tujuan_kib: '',
                tujuan_kode: '',
                tujuan_nama: '',
                nilai_reklas: 0,
                tanggal_reklas: new Date().toISOString().split('T')[0],
                triwulan: {{ $selectedTw === 'all' ? 1 : (int)$selectedTw }},
                tahun: {{ $selectedTahun }},
                nomor_ba_reklas: '',
                keterangan: '',
                spekBaru: {
                    tanah_luas_m2: '',
                    tanah_hak: 'Hak Pakai',
                    tanah_sertifikat_no: '',
                    tanah_sertifikat_tgl: '',
                    tanah_penggunaan: '',
                    tanah_asal_usul: 'Pengadaan APBD / BLUD',
                    tanah_alamat: 'RSUD Dr. H. Koesnandi',
                    mesin_nama_barang: '',
                    mesin_merk: '',
                    mesin_type: '',
                    mesin_no_pabrik: '',
                    mesin_ukuran_cc: '',
                    mesin_bahan: 'Logam / Komponen Elektronik',
                    mesin_no_polisi: '',
                    mesin_tahun_pembuatan: '',
                    mesin_kondisi: 'Baik',
                    mesin_no_rangka: '',
                    mesin_no_mesin: '',
                    mesin_no_bpkb: '',
                    mesin_keterangan: '',
                    gedung_konstruksi_bertingkat: 'Bertingkat',
                    gedung_konstruksi_beton: 'Beton',
                    gedung_luas_lantai_m2: '',
                    gedung_dokumen_nomor: '',
                    gedung_dokumen_tgl: '',
                    gedung_status_tanah: 'Tanah Pemda',
                    gedung_alamat: 'Kompleks RSUD Dr. H. Koesnandi',
                    gedung_is_baru: 'Lama',
                    gedung_kapitalisasi_tahun_induk: '',
                    gedung_kapitalisasi_nilai_induk: '',
                    jaringan_konstruksi: 'Aspal / Beton',
                    jaringan_panjang_km: '',
                    jaringan_lebar_m: '',
                    jaringan_luas_m2: '',
                    jaringan_alamat: 'Kompleks RSUD',
                    jaringan_is_baru: 'Lama',
                    jaringan_kapitalisasi_tahun_induk: '',
                    jaringan_kapitalisasi_nilai_induk: '',
                    lainnya_nama_barang: '',
                    lainnya_judul: '',
                    lainnya_pencipta: '',
                    lainnya_judul_pencipta: '',
                    lainnya_spesifikasi: '',
                    lainnya_ukuran: '',
                    lainnya_asal_daerah: '',
                    lainnya_no_pabrik: '',
                    lainnya_bahan: 'Kertas / Kanvas / Lainnya',
                    lainnya_kondisi: 'Baik',
                    lainnya_keterangan: '',
                    kib_e_type: 'buku',
                    is_extracom: null,
                    atb_nama_software: '',
                    atb_pengembang: '',
                    atb_masa_manfaat: 4,
                    kemitraan_skema: 'Sewa',
                    kemitraan_mitra: '',
                    kemitraan_pimpinan: '',
                    kemitraan_alamat: '',
                    kemitraan_perjanjian_no: '',
                    kemitraan_tanggal_pks: new Date().toISOString().split('T')[0],
                    kemitraan_tanggal_mulai: new Date().toISOString().split('T')[0],
                    kemitraan_tanggal_selesai: '',
                    kemitraan_jangka_waktu: '5 Tahun',
                    kemitraan_harga_sewa: '',
                    kemitraan_periode_sewa: 'Per Tahun',
                    kemitraan_nilai_kontribusi: '',
                    kemitraan_keterangan_kontribusi: '',
                },
                // Fields tambahan (wajib ada di default agar tidak undefined)
                alasan_reklas: '',
                tipe_koreksi: 'kurang',
                nilai_realisasi_baru: 0,
                skpd_tujuan: '',
                tanggal_bast: new Date().toISOString().split('T')[0],
                pejabat_opd_tujuan: '',
                nip_pejabat_opd_tujuan: '',
                jabatan_opd_tujuan: '',
                pj_asal_nama: 'BUDI HARTONO, S.Sos',
                pj_asal_nip: '19760229 200801 1 010',
                pj_asal_jabatan: 'Pengurus Barang Pengguna',
                alamat_instansi: '',
                nomor_sk_dasar: '',
                selected_register_ids: [],
                register_kondisi_map: {},
            },

            // 108 Hierarchy Getters
            get targetGroupPrefix() {
                const map = {
                    'KIB A': '1.3.1',
                    'KIB B': '1.3.2',
                    'KIB C': '1.3.3',
                    'KIB D': '1.3.4',
                    'KIB E': '1.3.5',
                    'KIB F': '1.3.6',
                    'ATB':   '1.5.3',
                    'KEMITRAAN': '1.5.2',
                };
                return map[this.formData.tujuan_kib] || '';
            },

            get targetJenisAstap() {
                if (!window.dbMasterJenisAstap108 || !this.targetGroupPrefix) return null;
                return window.dbMasterJenisAstap108.find(j => j.kode === this.targetGroupPrefix || j.kode.startsWith(this.targetGroupPrefix)) || null;
            },

            get availableSubRincian108() {
                if (this.targetJenisAstap && this.targetJenisAstap.subRincian) {
                    return this.targetJenisAstap.subRincian;
                }
                let all = [];
                (window.dbMasterJenisAstap108 || []).forEach(j => {
                    if (j.subRincian) all = all.concat(j.subRincian);
                });
                return all;
            },

            get filteredSubRincian108() {
                const list = this.availableSubRincian108 || [];
                const q = (this.searchReklasSubRincian || '').toLowerCase().trim();
                if (!q) return list.slice(0, 30);
                return list.filter(s =>
                    (s.nama && s.nama.toLowerCase().includes(q)) ||
                    (s.kode && s.kode.toLowerCase().includes(q))
                ).slice(0, 30);
            },

            detectKemitraanSkema(kode, nama) {
                const k = (kode || '').trim();
                const n = (nama || '').toLowerCase();
                if (k.includes('.01.01.02') || k.endsWith('.01.02') || k === '1.5.2.01.01.02' || n.includes('kerjasama pemanfaatan') || n.includes('kerja sama pemanfaatan') || n.includes('pemanfaatan') || n.includes('kp')) {
                    return 'Kerjasama Pemanfaatan (KP)';
                }
                if (k.includes('.01.01.03') || k.endsWith('.01.03') || k === '1.5.2.01.01.03' || n.includes('bangun guna') || n.includes('bangun serah') || n.includes('bgs') || n.includes('bsg')) {
                    return 'BGS/BSG';
                }
                if (k.includes('.01.01.04') || k.endsWith('.01.04') || k === '1.5.2.01.01.04' || n.includes('infrastruktur') || n.includes('kspi')) {
                    return 'KSPI';
                }
                if (k.includes('.01.01.01') || k.endsWith('.01.01') || k === '1.5.2.01.01.01' || n.includes('sewa')) {
                    return 'Sewa';
                }
                if (k.endsWith('.02')) return 'Kerjasama Pemanfaatan (KP)';
                if (k.endsWith('.03')) return 'BGS/BSG';
                if (k.endsWith('.04')) return 'KSPI';
                if (k.endsWith('.01')) return 'Sewa';
                return 'Sewa';
            },

            syncKemitraanSkema(kode, nama) {
                const targetKode = kode || this.reklasSubRincianKode || this.reklasSubSubRincianKode || this.formData?.tujuan_kode || '';
                const targetNama = nama || this.reklasSubRincianNama || this.reklasSubSubRincianNama || this.formData?.tujuan_nama || '';
                const detected = this.detectKemitraanSkema(targetKode, targetNama);
                if (this.formData && this.formData.spekBaru) {
                    this.formData.spekBaru.kemitraan_skema = detected;
                }
                return detected;
            },

            selectSubRincian(s) {
                this.reklasSubRincianKode = s.kode;
                this.reklasSubRincianNama = s.nama;
                this.searchReklasSubRincian = '';
                this.isReklasSubRincianOpen = false;

                if (this.reklasSubSubRincianKode && !this.reklasSubSubRincianKode.startsWith(s.kode)) {
                    this.reklasSubSubRincianKode = '';
                    this.reklasSubSubRincianNama = '';
                    this.searchReklasSubSubRincian = '';
                }

                this.formData.tujuan_kode = this.reklasSubSubRincianKode || s.kode;
                this.formData.tujuan_nama = this.reklasSubSubRincianNama || s.nama;

                // Auto match tujuan row in 42 rows template
                this.matchTujuanRowByPrefix(s.kode);
                this.syncKemitraanSkema(s.kode, s.nama);
            },

            clearSubRincian() {
                this.reklasSubRincianKode = '';
                this.reklasSubRincianNama = '';
                this.searchReklasSubRincian = '';
                this.isReklasSubRincianOpen = false;
                this.formData.tujuan_kode = this.reklasSubSubRincianKode || '';
                this.formData.tujuan_nama = this.reklasSubSubRincianNama || '';
                this.syncKemitraanSkema();
            },

            get currentSubRincianObj() {
                if (!this.reklasSubRincianKode) return null;
                return (this.availableSubRincian108 || []).find(s => s.kode === this.reklasSubRincianKode) || null;
            },

            get availableSubSubRincian108() {
                if (this.currentSubRincianObj && this.currentSubRincianObj.subSubRincian) {
                    return this.currentSubRincianObj.subSubRincian;
                }
                if (this.targetJenisAstap) {
                    const flat = [];
                    (this.targetJenisAstap.subRincian || []).forEach(sr => {
                        if (sr.subSubRincian) {
                            sr.subSubRincian.forEach(ssr => flat.push(ssr));
                        }
                    });
                    return flat;
                }
                let all = [];
                (window.dbMasterJenisAstap108 || []).forEach(j => {
                    (j.subRincian || []).forEach(sr => {
                        if (sr.subSubRincian) all = all.concat(sr.subSubRincian);
                    });
                });
                return all;
            },

            get filteredSubSubRincian108() {
                const list = this.availableSubSubRincian108 || [];
                const q = (this.searchReklasSubSubRincian || '').toLowerCase().trim();
                if (!q) return list.slice(0, 30);
                return list.filter(item =>
                    (item.nama && item.nama.toLowerCase().includes(q)) ||
                    (item.kode && item.kode.toLowerCase().includes(q))
                ).slice(0, 30);
            },

            selectSubSubRincian(item) {
                this.reklasSubSubRincianKode = item.kode;
                this.reklasSubSubRincianNama = item.nama;
                this.searchReklasSubSubRincian = '';
                this.isReklasSubSubRincianOpen = false;

                this.formData.tujuan_kode = item.kode;
                this.formData.tujuan_nama = item.nama;

                // Sync parent sub rincian if empty
                if (!this.reklasSubRincianKode && window.dbMasterJenisAstap108) {
                    for (const j of window.dbMasterJenisAstap108) {
                        for (const sr of (j.subRincian || [])) {
                            if (sr.subSubRincian && sr.subSubRincian.some(ssr => ssr.kode === item.kode)) {
                                this.reklasSubRincianKode = sr.kode;
                                this.reklasSubRincianNama = sr.nama;
                                this.matchTujuanRowByPrefix(sr.kode);
                                break;
                            }
                        }
                    }
                }
                this.syncKemitraanSkema(item.kode, item.nama);
            },

            clearSubSubRincian() {
                this.reklasSubSubRincianKode = '';
                this.reklasSubSubRincianNama = '';
                this.searchReklasSubSubRincian = '';
                this.isReklasSubSubRincianOpen = false;
                this.formData.tujuan_kode = this.reklasSubRincianKode || '';
                this.formData.tujuan_nama = this.reklasSubRincianNama || '';
                this.syncKemitraanSkema();
            },

            matchTujuanRowByPrefix(prefix) {
                if (!prefix || !window.templateRows) return;
                const p3 = prefix.substring(0, 8);
                const found = window.templateRows.find(r => r.kode_prefix && (r.kode_prefix.startsWith(p3) || p3.startsWith(r.kode_prefix)));
                if (found) {
                    this.formData.jenis_reklasifikasi_tujuan_id = found.id;
                }
            },

            // ===== SKPD & PEJABAT MUTASI EKSTERNAL KELUAR =====
            get filteredSkpdList() {
                const q = (this.formData.skpd_tujuan || '').toLowerCase().trim();
                const list = this.skpdList || [];
                if (!q) return list.slice(0, 25);
                return list.filter(s => s.toLowerCase().includes(q)).slice(0, 25);
            },

            get availablePejabatTujuanList() {
                const skpd = this.formData.skpd_tujuan;
                if (!skpd || !this.skpdDirectory || !this.skpdDirectory[skpd]) return [];
                return this.skpdDirectory[skpd].pejabats || [];
            },

            get filteredPejabatTujuanList() {
                const list = this.availablePejabatTujuanList;
                const q = (this.formData.pejabat_opd_tujuan || '').toLowerCase().trim();
                if (!q) return list;
                return list.filter(p => p.nama && p.nama.toLowerCase().includes(q));
            },

            selectSkpd(skpd) {
                this.formData.skpd_tujuan = skpd;
                this.isSkpdDropdownOpen = false;
                if (this.skpdDirectory && this.skpdDirectory[skpd]) {
                    if (this.skpdDirectory[skpd].alamat) {
                        this.formData.alamat_instansi = this.skpdDirectory[skpd].alamat;
                    }
                    if (this.skpdDirectory[skpd].pj_nama) {
                        this.formData.pejabat_opd_tujuan = this.skpdDirectory[skpd].pj_nama;
                        this.formData.nip_pejabat_opd_tujuan = this.skpdDirectory[skpd].pj_nip || '-';
                        this.formData.jabatan_opd_tujuan = this.skpdDirectory[skpd].pj_jabatan || 'Pengurus Barang';
                    }
                }
            },

            selectPejabatTujuan(p) {
                this.formData.pejabat_opd_tujuan = p.nama;
                this.formData.nip_pejabat_opd_tujuan = p.nip || '-';
                this.formData.jabatan_opd_tujuan = p.jabatan || 'Pengurus Barang';
                this.isPejabatTujuanDropdownOpen = false;
            },

            selectPejabatAsal(p) {
                this.formData.pj_asal_nama = p.nama;
                this.formData.pj_asal_nip = p.nip || '-';
                this.formData.pj_asal_jabatan = p.jabatan || 'Pengurus Barang Pengguna';
                this.isPejabatAsalDropdownOpen = false;
            },

            handleDokumenFile(event) {
                const file = event.target.files ? event.target.files[0] : null;
                if (!file) {
                    this.selectedDokumenFile = null;
                    return;
                }
                if (file.size > 10 * 1024 * 1024) {
                    if (typeof window.showSimatToast === 'function') window.showSimatToast('Ukuran berkas lampiran BAST melebihi batas 10 MB!', 'error');
                    else alert('Ukuran berkas lampiran BAST melebihi batas 10 MB!');
                    event.target.value = '';
                    this.selectedDokumenFile = null;
                    return;
                }
                this.selectedDokumenFile = file;
            },

            clearDokumenFile() {
                this.selectedDokumenFile = null;
                const fileInput = document.getElementById('reklas_dokumen_file_input');
                if (fileInput) fileInput.value = '';
            },

            isTanggalBastInvalid() {
                if (!this.formData.tanggal_bast) return false;
                const today = new Date().toISOString().split('T')[0];
                return this.formData.tanggal_bast > today;
            },

            openModalTambah() {
                this.selectedAstap = null;
                this.reklasSubRincianKode = '';
                this.reklasSubRincianNama = '';
                this.reklasSubSubRincianKode = '';
                this.reklasSubSubRincianNama = '';
                this.selectedDokumenFile = null;
                this.isSkpdDropdownOpen = false;
                this.isPejabatTujuanDropdownOpen = false;
                this.isPejabatAsalDropdownOpen = false;
                this.formData = {
                    astap_id: '',
                    nama_barang: '',
                    jenis_reklas: 'KOREKSI_REKENING',
                    sub_koreksi: 'biasa',
                    jenis_reklasifikasi_asal_id: '',
                    jenis_reklasifikasi_tujuan_id: '',
                    tujuan_kib: '',
                    tujuan_kode: '',
                    tujuan_nama: '',
                    nilai_reklas: 0,
                    tanggal_reklas: new Date().toISOString().split('T')[0],
                    triwulan: {{ $selectedTw === 'all' ? 1 : (int)$selectedTw }},
                    tahun: {{ $selectedTahun }},
                    nomor_ba_reklas: '',
                    alasan_reklas: '',
                    keterangan: '',
                    tipe_koreksi: 'kurang',
                    nilai_realisasi_baru: 0,
                    pihak_hibah: '',
                    skpd_tujuan: '',
                    tanggal_bast: new Date().toISOString().split('T')[0],
                    pejabat_opd_tujuan: '',
                    nip_pejabat_opd_tujuan: '',
                    jabatan_opd_tujuan: '',
                    pj_asal_nama: 'BUDI HARTONO, S.Sos',
                    pj_asal_nip: '19760229 200801 1 010',
                    pj_asal_jabatan: 'Pengurus Barang Pengguna',
                    alamat_instansi: '',
                    nomor_sk_dasar: '',
                    selected_register_ids: [],
                    register_kondisi_map: {},
                    spekBaru: {
                        tanah_luas_m2: '',
                        tanah_hak: 'Hak Pakai',
                        tanah_sertifikat_no: '',
                        tanah_sertifikat_tgl: '',
                        tanah_penggunaan: '',
                        tanah_asal_usul: 'Pengadaan APBD / BLUD',
                        tanah_alamat: 'RSUD Dr. H. Koesnandi',
                        mesin_merk: '',
                        mesin_type: '',
                        mesin_no_pabrik: '',
                        mesin_ukuran_cc: '',
                        mesin_bahan: 'Logam / Komponen Elektronik',
                        mesin_no_polisi: '',
                        gedung_konstruksi_bertingkat: 'Bertingkat',
                        gedung_konstruksi_beton: 'Beton',
                        gedung_luas_lantai_m2: '',
                        gedung_dokumen_nomor: '',
                        gedung_dokumen_tgl: '',
                        gedung_status_tanah: 'Tanah Pemda',
                        gedung_alamat: 'Kompleks RSUD Dr. H. Koesnandi',
                        jaringan_konstruksi: 'Aspal / Beton',
                        jaringan_panjang_km: '',
                        jaringan_lebar_m: '',
                        jaringan_luas_m2: '',
                        jaringan_alamat: 'Kompleks RSUD',
                        lainnya_judul_pencipta: '',
                        lainnya_bahan: 'Kertas / Kanvas / Lainnya',
                        atb_nama_software: '',
                        atb_pengembang: '',
                        atb_masa_manfaat: 4,
                        atb_nomor_lisensi: '',
                    },
                };
                this.showModalTambah = true;
            },

            getReklasAvailableRegisters() {
                if (!this.selectedAstap) return [];
                if (this.selectedAstap.registers && Array.isArray(this.selectedAstap.registers) && this.selectedAstap.registers.length > 0) {
                    return this.selectedAstap.registers;
                }
                return [];
            },

            toggleSelectAllReklasNibars(selectAll = true) {
                if (!selectAll) {
                    this.formData.selected_register_ids = [];
                } else {
                    const regs = this.getReklasAvailableRegisters();
                    this.formData.selected_register_ids = regs
                        .filter(r => r.status !== 'Mutasi Keluar OPD')
                        .map(r => r.id);
                }
                this.onSelectedNibarsChange();
            },

            calculateSelectedNibarsValue() {
                if (!this.selectedAstap) return 0;
                const regs = this.getReklasAvailableRegisters();
                if (!regs || regs.length === 0) {
                    return parseFloat(this.selectedAstap.total_realisasi || 0);
                }
                const selectedIds = this.formData.selected_register_ids || [];
                if (selectedIds.length === 0) return 0;

                const defaultUnitVal = parseFloat(this.selectedAstap.total_realisasi || 0) / Math.max(1, regs.length);
                let total = 0;
                for (const reg of regs) {
                    if (selectedIds.includes(reg.id)) {
                        total += parseFloat(reg.harga_satuan || defaultUnitVal);
                    }
                }
                return total;
            },

            onSelectedNibarsChange() {
                if (this.formData.jenis_reklas === 'MUTASI_EKSTERNAL') {
                    this.formData.nilai_reklas = this.calculateSelectedNibarsValue();
                }
            },

            onSelectAstap(astapId) {
                if (!astapId) {
                    this.selectedAstap = null;
                    this.formData.selected_register_ids = [];
                    return;
                }
                const found = (window.kandidatAstaps || []).find(it => String(it.id) === String(astapId));
                if (!found) return;

                this.selectedAstap = found;
                this.formData.nama_barang = found.nama_barang || '';
                this.formData.nilai_realisasi_baru = parseFloat(found.total_realisasi || 0);

                if (found.registers && found.registers.length > 0) {
                    this.formData.selected_register_ids = found.registers
                        .filter(r => r.status !== 'Mutasi Keluar OPD')
                        .map(r => r.id);
                    this.formData.register_kondisi_map = {};
                    found.registers.forEach(r => {
                        this.formData.register_kondisi_map[r.id] = r.kondisi || 'Baik';
                    });
                } else {
                    this.formData.selected_register_ids = [];
                    this.formData.register_kondisi_map = {};
                }

                if (this.formData.jenis_reklas === 'KOREKSI_LAIN') {
                    this.formData.nilai_reklas = 0;
                } else if (this.formData.jenis_reklas === 'MUTASI_EKSTERNAL') {
                    this.formData.nilai_reklas = this.calculateSelectedNibarsValue();
                } else {
                    this.formData.nilai_reklas = parseFloat(found.total_realisasi || 0);
                }

                // Auto-pilih baris asal berdasarkan prefix kode atau kategori KIB
                const prefix = found.jenis_astap?.sub_rincian_objek ? found.jenis_astap.sub_rincian_objek.substring(0, 8) : '';
                let asalRow = null;
                if (prefix && window.templateRows) {
                    asalRow = window.templateRows.find(r => r.kode_prefix && (r.kode_prefix.startsWith(prefix) || prefix.startsWith(r.kode_prefix)));
                }
                if (!asalRow && window.templateRows) {
                    const cat = found.category || found.asal_kib || 'KIB B';
                    asalRow = window.templateRows.find(r => r.kelompok_kib === cat);
                }
                if (asalRow) {
                    this.formData.jenis_reklasifikasi_asal_id = asalRow.id;
                    this.formData.asal_kib = asalRow.kelompok_kib;
                }

                // Sinkronkan baris jika jenis KOREKSI_LAIN
                if (this.formData.jenis_reklas === 'KOREKSI_LAIN') {
                    this.syncKorLainRows();
                }

                // Inisialisasi spek baru
                this.initSpekBaru();
            },

            onJenisReklasChange() {
                if (this.formData.jenis_reklas === 'KDP_TO_DEFINITIF') {
                    const kdpRow = (window.templateRows || []).find(r => r.kode_prefix === '1.3.6.01');
                    if (kdpRow) this.formData.jenis_reklasifikasi_asal_id = kdpRow.id;
                    this.formData.asal_kib = 'KIB F';
                    this.formData.tujuan_kib = 'KIB C';
                    this.onTujuanKibChange();
                } else if (this.formData.jenis_reklas === 'EKSTRAKOMPTABEL') {
                    this.formData.tujuan_kib = 'EKSTRAKOMPTABEL';
                    // Cari baris Koreksi Ekstrakomptabel untuk Tujuan
                    const extraRow = (window.templateRows || []).find(r => r.kode_prefix === 'KOR_EXTRACOM');
                    if (extraRow) {
                        this.formData.jenis_reklasifikasi_tujuan_id = extraRow.id;
                    }
                } else if (this.formData.jenis_reklas === 'KAPITALISASI_INTRAKOM') {
                    const extraRow = (window.templateRows || []).find(r => r.kode_prefix === 'KOR_EXTRACOM');
                    if (extraRow) {
                        this.formData.jenis_reklasifikasi_asal_id = extraRow.id;
                    }
                    this.formData.asal_kib = 'EKSTRAKOMPTABEL';
                    // Default tujuan KIB B, bisa diubah user
                    if (!this.formData.tujuan_kib) {
                        this.formData.tujuan_kib = 'KIB B';
                    }
                    this.onTujuanKibChange();
                    // Pre-fill nilai reklas dari aset terpilih
                    if (this.selectedAstap) {
                        this.formData.nilai_reklas = parseFloat(this.selectedAstap.total_realisasi || 0);
                    }
                } else if (this.formData.jenis_reklas === 'HIBAH_KELUAR') {
                    const hibahRow = (window.templateRows || []).find(r => r.kode_prefix === 'KOR_HIBAH');
                    if (hibahRow) {
                        this.formData.jenis_reklasifikasi_tujuan_id = hibahRow.id;
                    }
                    this.formData.tujuan_kib = 'HIBAH';
                } else if (this.formData.jenis_reklas === 'HIBAH_MASUK') {
                    const hibahRow = (window.templateRows || []).find(r => r.kode_prefix === 'KOR_HIBAH');
                    if (hibahRow) {
                        this.formData.jenis_reklasifikasi_asal_id = hibahRow.id;
                    }
                    this.formData.asal_kib = 'HIBAH';
                } else if (this.formData.jenis_reklas === 'MUTASI_EKSTERNAL') {
                    const mutasiRow = (window.templateRows || []).find(r => r.kode_prefix === 'KOR_LAIN');
                    if (mutasiRow) {
                        this.formData.jenis_reklasifikasi_tujuan_id = mutasiRow.id;
                    }
                    this.formData.tujuan_kib = 'KOREKSI';
                    if (this.selectedAstap && this.selectedAstap.registers && this.selectedAstap.registers.length > 0) {
                        if (!this.formData.selected_register_ids || this.formData.selected_register_ids.length === 0) {
                            this.formData.selected_register_ids = this.selectedAstap.registers
                                .filter(r => r.status !== 'Mutasi Keluar OPD')
                                .map(r => r.id);
                        }
                        this.formData.nilai_reklas = this.calculateSelectedNibarsValue();
                    } else if (this.selectedAstap) {
                        this.formData.nilai_reklas = parseFloat(this.selectedAstap.total_realisasi || 0);
                    }
                } else if (this.formData.jenis_reklas === 'KOREKSI_LAIN') {
                    const currentTot = parseFloat(this.selectedAstap?.total_realisasi || 0);
                    if (!this.formData.nilai_realisasi_baru || this.formData.nilai_realisasi_baru === 0) {
                        this.formData.nilai_realisasi_baru = currentTot;
                    }
                    this.formData.nilai_reklas = Math.abs(parseFloat(this.formData.nilai_realisasi_baru) - currentTot);
                    this.syncKorLainRows();
                }
            },

            onNilaiBaruInput() {
                const lama = parseFloat(this.selectedAstap?.total_realisasi || 0);
                const baru = parseFloat(this.formData.nilai_realisasi_baru || 0);
                const diff = Math.abs(baru - lama);
                this.formData.nilai_reklas = diff;
                this.formData.tipe_koreksi = (baru >= lama) ? 'tambah' : 'kurang';
                this.syncKorLainRows();
            },

            onNominalSelisihInput() {
                const lama = parseFloat(this.selectedAstap?.total_realisasi || 0);
                const selisih = parseFloat(this.formData.nilai_reklas || 0);
                if (this.formData.tipe_koreksi === 'tambah') {
                    this.formData.nilai_realisasi_baru = lama + selisih;
                } else {
                    this.formData.nilai_realisasi_baru = Math.max(0, lama - selisih);
                }
                this.syncKorLainRows();
            },

            onTipeKoreksiChange() {
                const lama = parseFloat(this.selectedAstap?.total_realisasi || 0);
                const selisih = parseFloat(this.formData.nilai_reklas || 0);
                if (this.formData.tipe_koreksi === 'tambah') {
                    this.formData.nilai_realisasi_baru = lama + selisih;
                } else {
                    this.formData.nilai_realisasi_baru = Math.max(0, lama - selisih);
                }
                this.syncKorLainRows();
            },

            syncKorLainRows() {
                if (this.formData.jenis_reklas !== 'KOREKSI_LAIN') return;
                const korLainRow = (window.templateRows || []).find(r => r.kode_prefix === 'KOR_LAIN');
                let astapRow = null;
                const prefix = this.selectedAstap?.jenis_astap?.sub_rincian_objek ? this.selectedAstap.jenis_astap.sub_rincian_objek.substring(0, 8) : '';
                if (prefix && window.templateRows) {
                    astapRow = window.templateRows.find(r => r.kode_prefix && (r.kode_prefix.startsWith(prefix) || prefix.startsWith(r.kode_prefix)));
                }
                if (!astapRow && window.templateRows) {
                    const cat = this.selectedAstap?.category || 'KIB B';
                    astapRow = window.templateRows.find(r => r.kelompok_kib === cat);
                }

                if (this.formData.tipe_koreksi === 'tambah') {
                    // Penambahan Nilai: Dari KOR_LAIN (+) ke Akun Aset Tetap
                    if (korLainRow) this.formData.jenis_reklasifikasi_asal_id = korLainRow.id;
                    if (astapRow) this.formData.jenis_reklasifikasi_tujuan_id = astapRow.id;
                    this.formData.asal_kib = 'KOREKSI';
                    this.formData.tujuan_kib = astapRow ? astapRow.kelompok_kib : 'KIB B';
                } else {
                    // Pengurangan Nilai: Dari Akun Aset Tetap (-) ke KOR_LAIN
                    if (astapRow) this.formData.jenis_reklasifikasi_asal_id = astapRow.id;
                    if (korLainRow) this.formData.jenis_reklasifikasi_tujuan_id = korLainRow.id;
                    this.formData.asal_kib = astapRow ? astapRow.kelompok_kib : 'KIB B';
                    this.formData.tujuan_kib = 'KOREKSI';
                }
            },

            onTujuanKibChange() {
                this.clearSubRincian();
                this.clearSubSubRincian();

                // Auto match tujuan row dari kelompok_kib
                if (this.formData.tujuan_kib && window.templateRows) {
                    const kibRow = window.templateRows.find(r => r.kelompok_kib === this.formData.tujuan_kib);
                    if (kibRow) {
                        this.formData.jenis_reklasifikasi_tujuan_id = kibRow.id;
                    }
                }

                this.initSpekBaru();
            },

            initSpekBaru() {
                if (!this.selectedAstap) return;
                const it = this.selectedAstap;
                let spec = {};
                try {
                    spec = typeof it.spesifikasi_json === 'string' ? JSON.parse(it.spesifikasi_json) : (it.spesifikasi_json || {});
                } catch (e) {
                    spec = {};
                }

                const defaultAlamat = it.alamat_barang || 'Kompleks RSUD Dr. H. Koesnandi';

                const gItem = (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) ? spec.gedung_items[0] : {};
                const mItem = (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) ? spec.mesin_items[0] : {};
                const tItem = (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) ? spec.tanah_items[0] : {};
                const jItem = (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) ? spec.jaringan_items[0] : {};
                const lItem = (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) ? spec.lainnya_items[0] : {};

                // KIB A - Tanah
                this.formData.spekBaru.tanah_luas_m2 = tItem.tanah_luas_m2 || tItem.tanah_luas || spec.luas_m2 || spec.tanah_luas_m2 || '';
                this.formData.spekBaru.tanah_hak = tItem.tanah_hak || spec.hak_tanah || spec.tanah_hak || 'Hak Pakai';
                this.formData.spekBaru.tanah_sertifikat_no = tItem.tanah_sertifikat_no || spec.sertifikat_no || spec.tanah_sertifikat_no || '';
                this.formData.spekBaru.tanah_sertifikat_tgl = tItem.tanah_sertifikat_tgl || spec.sertifikat_tgl || spec.tanah_sertifikat_tgl || '';
                this.formData.spekBaru.tanah_penggunaan = tItem.tanah_penggunaan || spec.penggunaan || spec.tanah_penggunaan || it.nama_barang || 'Kompleks RSUD';
                this.formData.spekBaru.tanah_asal_usul = tItem.tanah_asal_usul || spec.tanah_asal_usul || 'Pengadaan APBD / BLUD';
                this.formData.spekBaru.tanah_alamat = tItem.tanah_alamat || defaultAlamat;

                // KIB B - Mesin & Peralatan
                this.formData.spekBaru.mesin_nama_barang = mItem.mesin_nama_barang || spec.nama_barang || it.nama_barang || '';
                this.formData.spekBaru.mesin_merk = mItem.mesin_merk || spec.merk || it.merk_type || '';
                this.formData.spekBaru.mesin_type = mItem.mesin_type || spec.type || '';
                this.formData.spekBaru.mesin_no_pabrik = mItem.mesin_no_pabrik || mItem.mesin_nomor_pabrik || spec.no_pabrik || '';
                this.formData.spekBaru.mesin_ukuran_cc = mItem.mesin_ukuran_cc || mItem.mesin_ukuran || spec.ukuran_cc || spec.ukuran || '';
                this.formData.spekBaru.mesin_bahan = mItem.mesin_bahan || spec.bahan || 'Logam / Komponen Elektronik';
                this.formData.spekBaru.mesin_no_polisi = mItem.mesin_no_polisi || mItem.mesin_nomor_polisi || spec.no_polisi || '';
                this.formData.spekBaru.mesin_tahun_pembuatan = mItem.mesin_tahun_pembuatan || spec.tahun_pembuatan || spec.tahun_pembuatan_pabrik || (it.tahun_perolehan || '');
                this.formData.spekBaru.mesin_kondisi = mItem.mesin_kondisi || spec.kondisi || it.kondisi || 'Baik';
                this.formData.spekBaru.mesin_no_rangka = mItem.mesin_no_rangka || spec.no_rangka || '';
                this.formData.spekBaru.mesin_no_mesin = mItem.mesin_no_mesin || spec.no_mesin || '';
                this.formData.spekBaru.mesin_no_bpkb = mItem.mesin_no_bpkb || spec.no_bpkb || '';
                this.formData.spekBaru.mesin_keterangan = mItem.mesin_keterangan || spec.keterangan || '';

                const isFromKemitraan = (it.category === 'KEMITRAAN') 
                    || (it.sumber_dana === 'kemitraan') 
                    || (this.formData.asal_kib === 'KEMITRAAN')
                    || (it.kode_108 && it.kode_108.startsWith('1.5.2'))
                    || (it.kemitraan !== null && it.kemitraan !== undefined);

                // KIB C - Gedung & Bangunan
                let btk = gItem.gedung_bertingkat || spec.konstruksi_bertingkat || spec.bertingkat || 'Bertingkat';
                this.formData.spekBaru.gedung_konstruksi_bertingkat = (btk === 'Tidak') ? 'Tidak Bertingkat' : btk;
                let btn = gItem.gedung_beton || spec.konstruksi_beton || spec.beton || 'Beton';
                this.formData.spekBaru.gedung_konstruksi_beton = (btn === 'Tidak' || btn === 'Bukan Beton') ? 'Tidak' : 'Beton';
                this.formData.spekBaru.gedung_luas_lantai_m2 = gItem.gedung_luas_lantai || gItem.gedung_luas_lantai_m2 || gItem.luas_lantai || spec.gedung_luas_lantai || spec.luas_lantai_m2 || '';
                this.formData.spekBaru.gedung_dokumen_nomor = gItem.gedung_dokumen_no || gItem.gedung_dokumen_nomor || spec.gedung_dokumen_no || spec.dokumen_nomor || '';
                let gDocTgl = gItem.gedung_dokumen_tgl || spec.gedung_dokumen_tgl || spec.dokumen_tgl || '';
                if (gDocTgl && typeof gDocTgl === 'string' && gDocTgl.includes('/')) {
                    let parts = gDocTgl.split('/');
                    if (parts.length === 3 && parts[2].length === 4) gDocTgl = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                }
                this.formData.spekBaru.gedung_dokumen_tgl = gDocTgl;
                this.formData.spekBaru.gedung_status_tanah = gItem.gedung_status_tanah || spec.gedung_status_tanah || spec.status_tanah || 'Tanah Pemda';
                this.formData.spekBaru.gedung_alamat = gItem.gedung_alamat || defaultAlamat;
                this.formData.spekBaru.gedung_is_baru = gItem.gedung_is_baru || spec.gedung_is_baru || spec.is_baru || (isFromKemitraan ? 'Lama' : 'Baru');
                this.formData.spekBaru.gedung_kapitalisasi_tahun_induk = (this.formData.spekBaru.gedung_is_baru === 'Baru') ? '' : (gItem.gedung_kapitalisasi_tahun_induk || spec.gedung_kapitalisasi_tahun_induk || spec.tahun_induk || '');
                this.formData.spekBaru.gedung_kapitalisasi_nilai_induk = (this.formData.spekBaru.gedung_is_baru === 'Baru') ? '' : (gItem.gedung_kapitalisasi_nilai_induk || spec.gedung_kapitalisasi_nilai_induk || spec.nilai_induk || '');

                // KIB D - Jalan, Jaringan & Irigasi
                this.formData.spekBaru.jaringan_konstruksi = spec.konstruksi || 'Aspal / Beton';
                this.formData.spekBaru.jaringan_panjang_km = spec.panjang_km || '';
                this.formData.spekBaru.jaringan_lebar_m = spec.lebar_m || '';
                this.formData.spekBaru.jaringan_luas_m2 = spec.luas_m2 || '';
                this.formData.spekBaru.jaringan_alamat = defaultAlamat;
                this.formData.spekBaru.jaringan_is_baru = spec.jaringan_is_baru || spec.is_baru || (isFromKemitraan ? 'Lama' : 'Baru');
                this.formData.spekBaru.jaringan_kapitalisasi_tahun_induk = (this.formData.spekBaru.jaringan_is_baru === 'Baru') ? '' : (spec.jaringan_kapitalisasi_tahun_induk || spec.tahun_induk || '');
                this.formData.spekBaru.jaringan_kapitalisasi_nilai_induk = (this.formData.spekBaru.jaringan_is_baru === 'Baru') ? '' : (spec.jaringan_kapitalisasi_nilai_induk || spec.nilai_induk || '');

                // KIB E - Lainnya
                this.formData.spekBaru.lainnya_nama_barang = lItem.lainnya_nama_barang || spec.nama_barang || it.nama_barang || '';
                this.formData.spekBaru.lainnya_judul = lItem.lainnya_judul || spec.judul || spec.judul_pencipta || it.nama_barang || '';
                this.formData.spekBaru.lainnya_pencipta = lItem.lainnya_pencipta || spec.pencipta || spec.pengarang || '';
                this.formData.spekBaru.lainnya_judul_pencipta = spec.judul_pencipta || (this.formData.spekBaru.lainnya_judul + (this.formData.spekBaru.lainnya_pencipta ? ' / ' + this.formData.spekBaru.lainnya_pencipta : '')) || it.nama_barang || '';
                this.formData.spekBaru.lainnya_spesifikasi = lItem.lainnya_spesifikasi || spec.spesifikasi || spec.type || '';
                this.formData.spekBaru.lainnya_ukuran = lItem.lainnya_ukuran || spec.ukuran || '';
                this.formData.spekBaru.lainnya_asal_daerah = lItem.lainnya_asal_daerah || spec.asal_daerah || '';
                this.formData.spekBaru.lainnya_no_pabrik = lItem.lainnya_no_pabrik || spec.no_pabrik || '';
                this.formData.spekBaru.lainnya_bahan = lItem.lainnya_bahan || spec.bahan || 'Kertas / Kanvas / Lainnya';
                this.formData.spekBaru.lainnya_kondisi = lItem.lainnya_kondisi || spec.kondisi || it.kondisi || 'Baik';
                this.formData.spekBaru.lainnya_keterangan = lItem.lainnya_keterangan || spec.keterangan || '';
                this.formData.spekBaru.kib_e_type = lItem.kib_e_type || spec.kib_e_type || 'buku';

                // Auto-deteksi Extracom
                this.formData.spekBaru.is_extracom = this.detectIsExtracom(it, spec);
                this.kemitraanSubGroupOverride = null;

                // ATB
                this.formData.spekBaru.atb_nama_software = spec.nama_software || it.nama_barang || '';
                this.formData.spekBaru.atb_pengembang = spec.pengembang || '';
                this.formData.spekBaru.atb_masa_manfaat = spec.masa_manfaat || 4;
                this.formData.spekBaru.atb_nomor_lisensi = spec.nomor_lisensi || '';

                // KEMITRAAN (Akun 1.5.2)
                this.formData.spekBaru.kemitraan_skema = spec.kemitraan_skema || spec.skema || 'Sewa';
                this.formData.spekBaru.kemitraan_mitra = spec.mitra || spec.kemitraan_mitra || '';
                this.formData.spekBaru.kemitraan_pimpinan = spec.pimpinan || spec.mitra_pimpinan || spec.kemitraan_pimpinan || '';
                this.formData.spekBaru.kemitraan_alamat = spec.alamat || spec.mitra_alamat || spec.kemitraan_alamat || '';
                this.formData.spekBaru.kemitraan_perjanjian_no = spec.perjanjian_no || spec.nomor_pks || spec.kemitraan_perjanjian_no || '';
                this.formData.spekBaru.kemitraan_tanggal_pks = spec.tanggal_pks || spec.kemitraan_tanggal_pks || new Date().toISOString().split('T')[0];
                this.formData.spekBaru.kemitraan_tanggal_mulai = spec.tanggal_mulai || spec.kemitraan_tanggal_mulai || new Date().toISOString().split('T')[0];
                this.formData.spekBaru.kemitraan_tanggal_selesai = spec.tanggal_selesai || spec.kemitraan_tanggal_selesai || '';
                this.formData.spekBaru.kemitraan_jangka_waktu = spec.jangka_waktu || spec.kemitraan_jangka_waktu || '5 Tahun';
                this.formData.spekBaru.kemitraan_harga_sewa = spec.harga_sewa || spec.kemitraan_harga_sewa || '';
                this.formData.spekBaru.kemitraan_periode_sewa = spec.periode_sewa || spec.kemitraan_periode_sewa || 'Per Tahun';
                this.formData.spekBaru.kemitraan_nilai_kontribusi = spec.nilai_kontribusi || spec.kemitraan_nilai_kontribusi || '';
                this.formData.spekBaru.kemitraan_keterangan_kontribusi = spec.keterangan_kontribusi || spec.kemitraan_keterangan_kontribusi || '';

                // ASET LAIN (1.5.4)
                this.formData.spekBaru.aset_lain_kondisi = spec.kondisi_barang || spec.aset_lain_kondisi || 'Rusak Berat (Menunggu Penghapusan)';
                this.formData.spekBaru.aset_lain_alasan = spec.alasan || spec.aset_lain_alasan || 'Pengalihan ke Akun 1.5.4 Aset Lain-Lain';

                this.syncKemitraanSkema();
            },

            detectIsExtracom(item, spec = null) {
                const it = item || this.selectedAstap;
                if (!it) return false;
                if (!spec) {
                    try {
                        spec = typeof it.spesifikasi_json === 'string' ? JSON.parse(it.spesifikasi_json) : (it.spesifikasi_json || {});
                    } catch (e) {
                        spec = {};
                    }
                }
                spec = (spec && typeof spec === 'object') ? spec : {};

                if (it.category && it.category.toUpperCase() === 'EXTRACOM') return true;
                if (it.is_extracomtable || spec.is_extracomtable) return true;
                if (Array.isArray(spec.mesin_items) && spec.mesin_items.some(m => !!m.is_extracom)) return true;
                if (Array.isArray(spec.lainnya_items) && spec.lainnya_items.some(l => !!l.is_extracom)) return true;

                // Cek harga satuan barang / nilai reklas (<= 300.000)
                const vol = (it.volume && it.volume > 0) ? it.volume : (it.jumlah_volume || 1);
                const nilaiTotal = parseFloat(this.formData?.nilai_reklas || it.total_realisasi || it.nilai_realisasi || 0);
                const unitPrice = (nilaiTotal > 0 && vol > 0) ? (nilaiTotal / vol) : nilaiTotal;
                if (unitPrice > 0 && unitPrice <= 300000) return true;

                return false;
            },

            isKemitraanExtracom() {
                if (this.formData?.spekBaru?.is_extracom !== undefined && this.formData?.spekBaru?.is_extracom !== null) {
                    return Boolean(this.formData.spekBaru.is_extracom);
                }
                return this.detectIsExtracom(this.selectedAstap);
            },

            getKemitraanTargetSubGroup() {
                if (this.kemitraanSubGroupOverride) return this.kemitraanSubGroupOverride;

                const code = (this.formData.tujuan_kode || this.reklasSubSubRincianKode || this.reklasSubRincianKode || '').trim();
                if (code.endsWith('.001') || code.endsWith('.01.001') || code.includes('.001.')) return 'tanah';
                if (code.endsWith('.002') || code.endsWith('.01.002') || code.includes('.002.')) return 'mesin';
                if (code.endsWith('.003') || code.endsWith('.01.003') || code.includes('.003.')) return 'gedung';
                if (code.endsWith('.004') || code.endsWith('.01.004') || code.includes('.004.')) return 'jaringan';
                if (code.endsWith('.005') || code.endsWith('.01.005') || code.includes('.005.')) return 'lainnya';

                // Fallback: deteksi dari aset asal
                return this.detectKemitraanPhysicalType(this.selectedAstap);
            },

            getSourcePhysicalType(item) {
                const it = item || this.selectedAstap;
                if (!it) return null;
                const effKib = this.getEffectiveKibCategory(it);
                if (effKib === 'KIB C') return 'gedung';
                if (effKib === 'KIB B') return 'mesin';
                if (effKib === 'KIB A') return 'tanah';
                if (effKib === 'KIB D') return 'jaringan';
                if (effKib === 'KIB E') return 'lainnya';
                if (effKib === 'ATB') return 'atb';
                return this.detectKemitraanPhysicalType(it);
            },

            getTargetPhysicalType() {
                const kib = this.formData.tujuan_kib;
                if (!kib) return null;
                if (kib === 'KIB A') return 'tanah';
                if (kib === 'KIB B') return 'mesin';
                if (kib === 'KIB C') return 'gedung';
                if (kib === 'KIB D') return 'jaringan';
                if (kib === 'KIB E') return 'lainnya';
                if (kib === 'ATB') return 'atb';
                if (kib === 'ASET LAIN' || kib === 'ASET LAIN-LAIN') return 'aset_lain';
                return null;
            },

            isReklasSamePhysicalType() {
                const src = this.getSourcePhysicalType();
                const tgt = this.getTargetPhysicalType();
                return Boolean(src && tgt && src === tgt);
            },

            getPhysicalTypeLabel(type) {
                if (!type) return '';
                const map = {
                    'tanah': 'Tanah (KIB A)',
                    'mesin': 'Peralatan & Mesin (KIB B)',
                    'gedung': 'Gedung & Bangunan (KIB C)',
                    'jaringan': 'Jalan, Irigasi & Jaringan (KIB D)',
                    'lainnya': 'Aset Tetap Lainnya (KIB E)',
                    'atb': 'Aset Tak Berwujud (ATB)',
                    'aset_lain': 'Aset Lain-Lain'
                };
                return map[type] || String(type).toUpperCase();
            },

            getEffectiveKibCategory(astap) {
                if (!astap) return 'KIB B';
                let cat = String(astap.category || '').toUpperCase().trim();
                if (cat === 'KIB A' || cat === 'KIB B' || cat === 'KIB C' || cat === 'KIB D' || cat === 'KIB E') {
                    return cat;
                }

                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }

                // 1. Periksa kategori_kib dari spesifikasi_json
                const kibSpec = String(spec?.kategori_kib || '').toUpperCase();
                if (kibSpec.includes('KIB A') || kibSpec.includes('TANAH')) return 'KIB A';
                if (kibSpec.includes('KIB B') || kibSpec.includes('MESIN') || kibSpec.includes('PERALATAN')) return 'KIB B';
                if (kibSpec.includes('KIB C') || kibSpec.includes('GEDUNG') || kibSpec.includes('BANGUNAN')) return 'KIB C';
                if (kibSpec.includes('KIB D') || kibSpec.includes('JARINGAN') || kibSpec.includes('JALAN') || kibSpec.includes('IRIGASI')) return 'KIB D';
                if (kibSpec.includes('KIB E') || kibSpec.includes('LAINNYA') || kibSpec.includes('BUKU')) return 'KIB E';

                // 2. Periksa kode barang 108
                const kd = String(astap.kode_barang || astap.kode_108 || astap.jenis_astap?.sub_sub_rincian_objek || '');
                if (kd.endsWith('.001') || kd.includes('.01.001') || kd.startsWith('1.3.1')) return 'KIB A';
                if (kd.endsWith('.002') || kd.includes('.01.002') || kd.startsWith('1.3.2')) return 'KIB B';
                if (kd.endsWith('.003') || kd.includes('.01.003') || kd.startsWith('1.3.3')) return 'KIB C';
                if (kd.endsWith('.004') || kd.includes('.01.004') || kd.startsWith('1.3.4')) return 'KIB D';
                if (kd.endsWith('.005') || kd.includes('.01.005') || kd.startsWith('1.3.5')) return 'KIB E';

                // 3. Periksa isi repeater yang valid
                if (spec?.mesin_items?.some(m => m.mesin_nama_barang || m.mesin_merk || m.mesin_type || m.mesin_no_pabrik || (parseFloat(m.mesin_nilai_satuan) > 0))) return 'KIB B';
                if (spec?.tanah_items?.some(t => t.tanah_luas_m2 || t.tanah_hak || t.tanah_sertifikat_no || (parseFloat(t.tanah_nilai_satuan) > 0))) return 'KIB A';
                if (spec?.gedung_items?.some(g => g.gedung_nama_barang || g.gedung_luas_m2 || g.gedung_luas_lantai || g.gedung_bertingkat || (parseFloat(g.gedung_nilai_satuan) > 0))) return 'KIB C';
                if (spec?.jaringan_items?.some(j => j.jaringan_nama_barang || j.jaringan_konstruksi || j.jaringan_panjang || (parseFloat(j.jaringan_nilai_satuan) > 0))) return 'KIB D';
                if (spec?.lainnya_items?.some(l => l.lainnya_nama_barang || l.lainnya_judul || l.lainnya_pencipta || (parseFloat(l.lainnya_nilai_satuan) > 0))) return 'KIB E';

                // 4. Periksa nama barang
                const nama = String(astap.nama_barang || '').toLowerCase();
                if (nama.includes('tanah')) return 'KIB A';
                if (nama.includes('gedung') || nama.includes('bangunan')) return 'KIB C';
                if (nama.includes('jalan') || nama.includes('jaringan') || nama.includes('irigasi')) return 'KIB D';
                if (nama.includes('buku') || nama.includes('hewan') || nama.includes('kesenian')) return 'KIB E';

                return 'KIB B';
            },

            detectKemitraanPhysicalType(item) {
                const it = item || this.selectedAstap;
                let spec = it?.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                spec = (spec && typeof spec === 'object') ? spec : {};

                const kibSpec = String(spec.kategori_kib || '').toUpperCase();
                if (kibSpec.includes('KIB C') || kibSpec.includes('GEDUNG') || kibSpec.includes('BANGUNAN')) return 'gedung';
                if (kibSpec.includes('KIB B') || kibSpec.includes('MESIN') || kibSpec.includes('PERALATAN')) return 'mesin';
                if (kibSpec.includes('KIB A') || kibSpec.includes('TANAH')) return 'tanah';
                if (kibSpec.includes('KIB D') || kibSpec.includes('JARINGAN') || kibSpec.includes('JALAN') || kibSpec.includes('IRIGASI')) return 'jaringan';
                if (kibSpec.includes('KIB E') || kibSpec.includes('LAINNYA') || kibSpec.includes('BUKU')) return 'lainnya';

                if (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) return 'gedung';
                if (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) return 'mesin';
                if (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) return 'tanah';
                if (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) return 'jaringan';
                if (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) return 'lainnya';

                const nama = String(it?.nama_barang || '').toLowerCase();
                if (nama.includes('tanah')) return 'tanah';
                if (nama.includes('gedung') || nama.includes('bangunan')) return 'gedung';
                if (nama.includes('jalan') || nama.includes('jaringan') || nama.includes('irigasi')) return 'jaringan';
                return 'mesin';
            },

            parseKemitraanDuration(text) {
                if (!text || typeof text !== 'string') return null;
                const str = text.trim().toLowerCase();
                if (!str) return null;

                let totalMonths = 0;
                let totalDays = 0;
                let matched = false;

                // Match decimal years e.g. "2.5 tahun" or "2,5 thn"
                const decimalYearMatch = str.match(/^([\d]+[.,][\d]+)\s*(?:tahun|thn|th|y|year|years)?$/);
                if (decimalYearMatch) {
                    const num = parseFloat(decimalYearMatch[1].replace(',', '.'));
                    if (!isNaN(num) && num > 0) {
                        const m = Math.round(num * 12);
                        return { years: Math.floor(m / 12), months: m % 12, days: 0 };
                    }
                }

                // Years: e.g. "2 tahun", "2 thn", "2 th", "2y", "2 year"
                const yearMatch = str.match(/(\d+)\s*(?:tahun|thn|th|y|year|years)\b/);
                if (yearMatch) {
                    totalMonths += parseInt(yearMatch[1], 10) * 12;
                    matched = true;
                }

                // Months: e.g. "6 bulan", "6 bln", "6 mo", "6 month"
                const monthMatch = str.match(/(\d+)\s*(?:bulan|bln|bl|mo|month|months)\b/);
                if (monthMatch) {
                    totalMonths += parseInt(monthMatch[1], 10);
                    matched = true;
                }

                // Days: e.g. "90 hari", "90 hr", "90 d"
                const dayMatch = str.match(/(\d+)\s*(?:hari|hr|d|day|days)\b/);
                if (dayMatch) {
                    totalDays += parseInt(dayMatch[1], 10);
                    matched = true;
                }

                // Plain digits without unit (e.g. "2" or "5")
                if (!matched) {
                    const plainNumMatch = str.match(/^(\d+)$/);
                    if (plainNumMatch) {
                        const num = parseInt(plainNumMatch[1], 10);
                        if (num > 0) {
                            if (num <= 30) {
                                totalMonths += num * 12;
                            } else {
                                totalDays += num;
                            }
                            matched = true;
                        }
                    }
                }

                if (!matched || (totalMonths === 0 && totalDays === 0)) return null;

                return {
                    years: Math.floor(totalMonths / 12),
                    months: totalMonths % 12,
                    days: totalDays
                };
            },

            computeKemitraanEndDate(startDateStr, durationObj) {
                if (!startDateStr || !durationObj) return null;
                let d;
                if (typeof startDateStr === 'string' && startDateStr.includes('/')) {
                    const p = startDateStr.split('/');
                    d = new Date(parseInt(p[2], 10), parseInt(p[1], 10) - 1, parseInt(p[0], 10));
                } else {
                    const p = String(startDateStr).split('T')[0].split('-');
                    d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                }
                if (isNaN(d.getTime())) return null;

                if (durationObj.years > 0) {
                    d.setFullYear(d.getFullYear() + durationObj.years);
                }
                if (durationObj.months > 0) {
                    d.setMonth(d.getMonth() + durationObj.months);
                }
                if (durationObj.days > 0) {
                    d.setDate(d.getDate() + durationObj.days);
                }

                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            },

            formatKemitraanDurationFromDates(startDateStr, endDateStr) {
                if (!startDateStr || !endDateStr) return '';
                let dM, dS;
                if (typeof startDateStr === 'string' && startDateStr.includes('/')) {
                    const p = startDateStr.split('/');
                    dM = new Date(parseInt(p[2], 10), parseInt(p[1], 10) - 1, parseInt(p[0], 10));
                } else {
                    const p = String(startDateStr).split('T')[0].split('-');
                    dM = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                }
                if (typeof endDateStr === 'string' && endDateStr.includes('/')) {
                    const p = endDateStr.split('/');
                    dS = new Date(parseInt(p[2], 10), parseInt(p[1], 10) - 1, parseInt(p[0], 10));
                } else {
                    const p = String(endDateStr).split('T')[0].split('-');
                    dS = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                }
                if (isNaN(dM.getTime()) || isNaN(dS.getTime()) || dS < dM) return '';

                let years = dS.getFullYear() - dM.getFullYear();
                let months = dS.getMonth() - dM.getMonth();
                let days = dS.getDate() - dM.getDate();

                if (days < 0) {
                    months -= 1;
                    const prevMonthDate = new Date(dS.getFullYear(), dS.getMonth(), 0);
                    days += prevMonthDate.getDate();
                }
                if (months < 0) {
                    years -= 1;
                    months += 12;
                }

                const parts = [];
                if (years > 0) parts.push(`${years} Tahun`);
                if (months > 0) parts.push(`${months} Bulan`);
                if (days > 0 && years === 0) parts.push(`${days} Hari`);

                if (parts.length === 0) {
                    const diffMs = dS.getTime() - dM.getTime();
                    const diffDays = Math.round(diffMs / (1000 * 60 * 60 * 24));
                    return diffDays > 0 ? `${diffDays} Hari` : '1 Hari';
                }
                return parts.join(' ');
            },

            calcReklasKemitraanDurasi() {
                const tM = this.formData?.spekBaru?.kemitraan_tanggal_mulai;
                const tS = this.formData?.spekBaru?.kemitraan_tanggal_selesai;
                if (!tM || !tS) return;
                const durText = this.formatKemitraanDurationFromDates(tM, tS);
                if (durText) {
                    this.formData.spekBaru.kemitraan_jangka_waktu = durText;
                }
            },

            onReklasKemitraanDurasiInput() {
                const durText = this.formData?.spekBaru?.kemitraan_jangka_waktu;
                if (!durText) return;
                const parsed = this.parseKemitraanDuration(durText);
                if (!parsed) return;

                let tM = this.formData.spekBaru.kemitraan_tanggal_mulai;
                if (!tM) {
                    tM = new Date().toISOString().split('T')[0];
                    this.formData.spekBaru.kemitraan_tanggal_mulai = tM;
                }
                const newEnd = this.computeKemitraanEndDate(tM, parsed);
                if (newEnd) {
                    this.formData.spekBaru.kemitraan_tanggal_selesai = newEnd;
                }
            },

            onReklasKemitraanTanggalMulaiChange() {
                const durText = this.formData?.spekBaru?.kemitraan_jangka_waktu;
                const parsed = durText ? this.parseKemitraanDuration(durText) : null;
                const tM = this.formData?.spekBaru?.kemitraan_tanggal_mulai;
                if (parsed && tM) {
                    const newEnd = this.computeKemitraanEndDate(tM, parsed);
                    if (newEnd) {
                        this.formData.spekBaru.kemitraan_tanggal_selesai = newEnd;
                        return;
                    }
                }
                this.calcReklasKemitraanDurasi();
            },

            setReklasKemitraanDurasiQuick(amount, unit) {
                const unitText = unit === 'tahun' ? 'Tahun' : (unit === 'bulan' ? 'Bulan' : 'Hari');
                this.formData.spekBaru.kemitraan_jangka_waktu = `${amount} ${unitText}`;
                let tM = this.formData.spekBaru.kemitraan_tanggal_mulai;
                if (!tM) {
                    tM = new Date().toISOString().split('T')[0];
                    this.formData.spekBaru.kemitraan_tanggal_mulai = tM;
                }
                const parsed = {
                    years: unit === 'tahun' ? amount : 0,
                    months: unit === 'bulan' ? amount : 0,
                    days: unit === 'hari' ? amount : 0
                };
                const newEnd = this.computeKemitraanEndDate(tM, parsed);
                if (newEnd) {
                    this.formData.spekBaru.kemitraan_tanggal_selesai = newEnd;
                }
            },

            getReklasEstimasiTotalSewa() {
                const harga = parseFloat(this.formData?.spekBaru?.kemitraan_harga_sewa || 0);
                if (!harga || harga <= 0) return 0;
                const periode = this.formData?.spekBaru?.kemitraan_periode_sewa || 'Per Tahun';
                if (periode === 'Total Selama Kontrak') return harga;

                const tM = this.formData?.spekBaru?.kemitraan_tanggal_mulai;
                const tS = this.formData?.spekBaru?.kemitraan_tanggal_selesai;
                if (!tM || !tS) return harga;
                const dM = new Date(tM);
                const dS = new Date(tS);
                if (isNaN(dM.getTime()) || isNaN(dS.getTime()) || dS <= dM) return harga;

                const diffDays = Math.round((dS - dM) / (1000 * 60 * 60 * 24));
                if (periode === 'Per Tahun') {
                    const years = Math.max(1, Math.round(diffDays / 365));
                    return harga * years;
                } else if (periode === 'Per Bulan') {
                    const months = Math.max(1, Math.round(diffDays / 30));
                    return harga * months;
                }
                return harga;
            },

            openDetailReklas(item) {
                this.detailItem = item;
                this.showModalDetail = true;
            },

            getJenisReklasLabel(code) {
                const map = {
                    'KOREKSI_REKENING': 'Koreksi Rekening / Pindah KIB',
                    'KDP_TO_DEFINITIF': 'KDP Selesai ➔ Definitif',
                    'DEFINITIF_TO_KDP': 'Definitif ➔ KDP (Reversi)',
                    'EKSTRAKOMPTABEL': 'Ekstrakomptabel (Nilai ≤ Rp 300.000)',
                    'KAPITALISASI_INTRAKOM': 'Kapitalisasi Intrakomptabel',
                    'HIBAH_KELUAR': 'Hibah Keluar (Dihibahkan ke Luar)',
                    'HIBAH_MASUK': 'Hibah Masuk / Bantuan Pemerintah',
                    'MUTASI_EKSTERNAL': 'Mutasi Eksternal (Antar-OPD)',
                    'KOREKSI_LAIN': 'Koreksi Nilai / Audit BPK',
                };
                return map[code] || code;
            },

            formatDateIndo(dateStr) {
                if (!dateStr) return '-';
                const parts = dateStr.split('-');
                if (parts.length === 3) {
                    return `${parts[2]}/${parts[1]}/${parts[0]}`;
                }
                return dateStr;
            },

            getFormattedSpec(spec) {
                if (!spec) return {};
                let raw = spec;
                if (typeof spec === 'string') {
                    try { raw = JSON.parse(spec); } catch (e) { return {}; }
                }
                if (typeof raw !== 'object' || raw === null) return {};

                const out = {};
                // If repeater items exist
                if (raw.tanah_items && Array.isArray(raw.tanah_items) && raw.tanah_items[0]) {
                    const t = raw.tanah_items[0];
                    if (t.tanah_luas_m2) out['Luas Tanah'] = t.tanah_luas_m2 + ' m²';
                    if (t.tanah_hak) out['Status Hak'] = t.tanah_hak;
                    if (t.tanah_sertifikat_no) out['No. Sertifikat'] = t.tanah_sertifikat_no;
                    if (t.tanah_penggunaan) out['Penggunaan'] = t.tanah_penggunaan;
                    if (t.tanah_alamat) out['Alamat Lokasi'] = t.tanah_alamat;
                } else if (raw.mesin_items && Array.isArray(raw.mesin_items) && raw.mesin_items[0]) {
                    const m = raw.mesin_items[0];
                    if (m.mesin_nama_barang) out['Nama Barang'] = m.mesin_nama_barang;
                    if (m.mesin_merk) out['Merk / Pabrikan'] = m.mesin_merk;
                    if (m.mesin_type) out['Tipe / Model'] = m.mesin_type;
                    if (m.mesin_no_pabrik) out['No. Pabrik / Seri'] = m.mesin_no_pabrik;
                    if (m.mesin_ukuran_cc) out['Ukuran / Kapasitas'] = m.mesin_ukuran_cc;
                    if (m.mesin_tahun_pembuatan) out['Tahun Pabrik'] = m.mesin_tahun_pembuatan;
                    if (m.mesin_bahan) out['Bahan Material'] = m.mesin_bahan;
                    if (m.mesin_kondisi) out['Kondisi Fisik'] = m.mesin_kondisi;
                    if (m.mesin_no_polisi) out['No. Polisi'] = m.mesin_no_polisi;
                    if (m.mesin_no_rangka) out['No. Rangka'] = m.mesin_no_rangka;
                    if (m.mesin_no_mesin) out['No. Mesin'] = m.mesin_no_mesin;
                    if (m.mesin_no_bpkb) out['No. BPKB'] = m.mesin_no_bpkb;
                } else if (raw.gedung_items && Array.isArray(raw.gedung_items) && raw.gedung_items[0]) {
                    const g = raw.gedung_items[0];
                    if (g.gedung_konstruksi_bertingkat) out['Konstruksi'] = g.gedung_konstruksi_bertingkat + ' (' + (g.gedung_konstruksi_beton || 'Beton') + ')';
                    if (g.gedung_luas_lantai_m2) out['Luas Lantai'] = g.gedung_luas_lantai_m2 + ' m²';
                    if (g.gedung_status_tanah) out['Status Tanah'] = g.gedung_status_tanah;
                    if (g.gedung_dokumen_nomor) out['Dokumen IMB / PBG'] = g.gedung_dokumen_nomor;
                    if (g.gedung_alamat) out['Letak Gedung'] = g.gedung_alamat;
                } else if (raw.jaringan_items && Array.isArray(raw.jaringan_items) && raw.jaringan_items[0]) {
                    const j = raw.jaringan_items[0];
                    if (j.jaringan_konstruksi) out['Konstruksi'] = j.jaringan_konstruksi;
                    if (j.jaringan_luas_m2) out['Luas'] = j.jaringan_luas_m2 + ' m²';
                    if (j.jaringan_panjang_km) out['Panjang'] = j.jaringan_panjang_km;
                    if (j.jaringan_alamat) out['Alamat Lokasi'] = j.jaringan_alamat;
                } else if (raw.lainnya_items && Array.isArray(raw.lainnya_items) && raw.lainnya_items[0]) {
                    const l = raw.lainnya_items[0];
                    if (l.lainnya_nama_barang) out['Nama Barang'] = l.lainnya_nama_barang;
                    if (l.lainnya_judul) out['Judul / Jenis'] = l.lainnya_judul;
                    if (l.lainnya_pencipta) out['Pencipta / Seniman'] = l.lainnya_pencipta;
                    if (l.lainnya_spesifikasi) out['Spesifikasi'] = l.lainnya_spesifikasi;
                    if (l.lainnya_ukuran) out['Ukuran / Dimensi'] = l.lainnya_ukuran;
                    if (l.lainnya_bahan) out['Bahan Material'] = l.lainnya_bahan;
                    if (l.lainnya_asal_daerah) out['Asal Daerah'] = l.lainnya_asal_daerah;
                    if (l.lainnya_no_pabrik) out['No. Pabrik / SN'] = l.lainnya_no_pabrik;
                    if (l.lainnya_kondisi) out['Kondisi Fisik'] = l.lainnya_kondisi;
                } else if (raw.atb_items && Array.isArray(raw.atb_items) && raw.atb_items[0]) {
                    const a = raw.atb_items[0];
                    if (a.atb_nama_software) out['Software / Sistem'] = a.atb_nama_software;
                    if (a.atb_pengembang) out['Pengembang / Vendor'] = a.atb_pengembang;
                    if (a.atb_masa_manfaat) out['Masa Manfaat'] = a.atb_masa_manfaat + ' Tahun';
                    if (a.atb_nomor_lisensi) out['No. Lisensi / HAKI'] = a.atb_nomor_lisensi;
                } else {
                    // Direct keys
                    if (raw.merk) out['Merk / Pabrikan'] = raw.merk;
                    if (raw.type) out['Tipe / Model'] = raw.type;
                    if (raw.no_pabrik) out['No. Pabrik'] = raw.no_pabrik;
                    if (raw.luas_m2 || raw.tanah_luas_m2) out['Luas'] = (raw.luas_m2 || raw.tanah_luas_m2) + ' m²';
                    if (raw.hak_tanah || raw.tanah_hak) out['Status Hak'] = raw.hak_tanah || raw.tanah_hak;
                    if (raw.sertifikat_no || raw.tanah_sertifikat_no) out['No. Sertifikat'] = raw.sertifikat_no || raw.tanah_sertifikat_no;
                    if (raw.konstruksi_bertingkat || raw.gedung_konstruksi_bertingkat) out['Konstruksi'] = (raw.konstruksi_bertingkat || raw.gedung_konstruksi_bertingkat) + ' (' + (raw.konstruksi_beton || raw.gedung_konstruksi_beton || 'Beton') + ')';
                    if (raw.luas_lantai_m2 || raw.gedung_luas_lantai_m2) out['Luas Lantai'] = (raw.luas_lantai_m2 || raw.gedung_luas_lantai_m2) + ' m²';
                    if (raw.konstruksi || raw.jaringan_konstruksi) out['Konstruksi'] = raw.konstruksi || raw.jaringan_konstruksi;
                    if (raw.nama_software || raw.atb_nama_software) out['Software'] = raw.nama_software || raw.atb_nama_software;
                    if (raw.alamat || raw.tanah_alamat || raw.gedung_alamat || raw.jaringan_alamat) out['Alamat Lokasi'] = raw.alamat || raw.tanah_alamat || raw.gedung_alamat || raw.jaringan_alamat;
                }

                // Metadata Kemitraan Pihak Ketiga (Akun 1.5.2)
                if (raw.kemitraan_items && Array.isArray(raw.kemitraan_items) && raw.kemitraan_items[0]) {
                    const k = raw.kemitraan_items[0];
                    out['Skema Kemitraan'] = k.skema_kemitraan || raw.skema_kemitraan || 'Sewa';
                    out['Mitra Rekanan'] = k.mitra_nama || raw.mitra_nama || '-';
                    if (k.mitra_pimpinan || raw.mitra_pimpinan) out['Pimpinan Mitra'] = k.mitra_pimpinan || raw.mitra_pimpinan;
                    if (k.perjanjian_nomor || raw.perjanjian_nomor) out['No. PKS / MoU'] = k.perjanjian_nomor || raw.perjanjian_nomor;
                    if (k.tanggal_mulai && k.tanggal_selesai) out['Masa Konsesi'] = k.tanggal_mulai + ' s/d ' + k.tanggal_selesai + (k.jangka_waktu ? ` (${k.jangka_waktu})` : '');
                    if (k.harga_sewa || raw.harga_sewa) {
                        out['Tarif Sewa'] = 'Rp ' + Number(k.harga_sewa || raw.harga_sewa).toLocaleString('id-ID') + ' (' + (k.periode_sewa || raw.periode_sewa || 'Per Tahun') + ')';
                    }
                    if (k.nilai_kontribusi || raw.nilai_kontribusi) {
                        out['Nilai Kontribusi'] = 'Rp ' + Number(k.nilai_kontribusi || raw.nilai_kontribusi).toLocaleString('id-ID') + (k.keterangan_kontribusi ? ` - ${k.keterangan_kontribusi}` : '');
                    }
                } else if (raw.mitra_nama || raw.kemitraan_mitra) {
                    out['Skema Kemitraan'] = raw.skema_kemitraan || raw.kemitraan_skema || 'Sewa';
                    out['Mitra Rekanan'] = raw.mitra_nama || raw.kemitraan_mitra || '-';
                    if (raw.mitra_pimpinan || raw.kemitraan_pimpinan) out['Pimpinan Mitra'] = raw.mitra_pimpinan || raw.kemitraan_pimpinan;
                    if (raw.perjanjian_nomor || raw.kemitraan_perjanjian_no || raw.nomor_pks) out['No. PKS / MoU'] = raw.perjanjian_nomor || raw.kemitraan_perjanjian_no || raw.nomor_pks;
                    if (raw.tanggal_mulai && raw.tanggal_selesai) out['Masa Konsesi'] = raw.tanggal_mulai + ' s/d ' + raw.tanggal_selesai + (raw.jangka_waktu ? ` (${raw.jangka_waktu})` : '');
                    if (raw.harga_sewa || raw.kemitraan_harga_sewa) {
                        out['Tarif Sewa'] = 'Rp ' + Number(raw.harga_sewa || raw.kemitraan_harga_sewa).toLocaleString('id-ID') + ' (' + (raw.periode_sewa || raw.kemitraan_periode_sewa || 'Per Tahun') + ')';
                    }
                    if (raw.nilai_kontribusi || raw.kemitraan_nilai_kontribusi) {
                        out['Nilai Kontribusi'] = 'Rp ' + Number(raw.nilai_kontribusi || raw.kemitraan_nilai_kontribusi).toLocaleString('id-ID') + (raw.keterangan_kontribusi ? ` - ${raw.keterangan_kontribusi}` : '');
                    }
                }

                // Metadata Hibah & Mutasi Eksternal
                if (raw.hibah_info) {
                    out['Tipe Hibah'] = raw.hibah_info.tipe === 'keluar' ? 'Hibah Keluar (Dihibahkan ke Pihak Lain)' : 'Hibah Masuk (Bantuan Pemerintah)';
                    if (raw.hibah_info.pihak) out['Pihak Terkait / Lembaga'] = raw.hibah_info.pihak;
                    if (raw.hibah_info.nomor_bast) out['No. BAST Hibah'] = raw.hibah_info.nomor_bast;
                    if (raw.hibah_info.tanggal_bast) out['Tgl. BAST Hibah'] = raw.hibah_info.tanggal_bast;
                }
                if (raw.mutasi_info) {
                    out['Tipe Mutasi'] = 'Mutasi Keluar Antar-OPD / SKPD';
                    if (raw.mutasi_info.skpd_tujuan) out['SKPD / OPD Penerima'] = raw.mutasi_info.skpd_tujuan;
                    if (raw.mutasi_info.nomor_bast) out['No. BAST Mutasi'] = raw.mutasi_info.nomor_bast;
                    if (raw.mutasi_info.tanggal_bast) out['Tgl. BAST Mutasi'] = raw.mutasi_info.tanggal_bast;
                }

                return out;
            },

            getNarasiPreview() {
                const nama = this.formData.nama_barang || (this.selectedAstap?.nama_barang) || 'Aset Terpilih';
                const nilai = new Intl.NumberFormat('id-ID').format(this.formData.nilai_reklas || 0);
                const noBa = this.formData.nomor_ba_reklas ? ` dengan Nomor BA: ${this.formData.nomor_ba_reklas}` : '';
                const tujuan = this.formData.tujuan_kib ? ` ke ${this.formData.tujuan_kib}` : '';

                switch (this.formData.jenis_reklas) {
                    case 'KOREKSI_REKENING':
                        return `Telah dilakukan koreksi rekening belanja / pemindahan bukuan${tujuan} atas barang "${nama}" senilai Rp ${nilai}${noBa} karena adanya penyesuaian klasifikasi PMDN 108 dan spesifikasi fisik.`;
                    case 'KDP_TO_DEFINITIF':
                        return `Telah diselesaikan konstruksi fisik / KDP atas aset "${nama}" senilai Rp ${nilai}${noBa} dan dikapitalisasi menjadi aset tetap definitif (${this.formData.tujuan_kib || 'Gedung dan Bangunan'}).`;
                    case 'EKSTRAKOMPTABEL':
                        return `Telah dilakukan koreksi pengalihan ke Ekstrakomptabel atas aset "${nama}" senilai Rp ${nilai}${noBa} karena nilai perolehan satuan berada di bawah batas kapitalisasi (≤ Rp 300.000).`;
                    case 'KAPITALISASI_INTRAKOM':
                        return `Telah dilakukan kapitalisasi intrakomptabel atas aset ekstrakomptabel "${nama}" senilai Rp ${nilai}${noBa} ke dalam aset tetap ${this.formData.tujuan_kib || 'KIB B'} karena nilai satuan memenuhi kriteria kapitalisasi.`;
                    case 'HIBAH_KELUAR':
                        const pihakKeluar = this.formData.pihak_hibah ? ` kepada ${this.formData.pihak_hibah}` : '';
                        return `Telah dihibahkan aset tetap "${nama}" senilai Rp ${nilai}${pihakKeluar}${noBa} berdasarkan Berita Acara Serah Terima (BAST) Hibah.`;
                    case 'HIBAH_MASUK':
                        const pihakMasuk = this.formData.pihak_hibah ? ` dari ${this.formData.pihak_hibah}` : '';
                        return `Telah dicatat penambahan aset tetap melalui reklasifikasi hibah/bantuan pemerintah${pihakMasuk} atas barang "${nama}" senilai Rp ${nilai}${noBa}.`;
                    case 'MUTASI_EKSTERNAL':
                        const skpd = this.formData.skpd_tujuan ? ` ke ${this.formData.skpd_tujuan}` : '';
                        return `Telah dilakukan mutasi keluar antar-OPD atas aset "${nama}" senilai Rp ${nilai}${skpd}${noBa} berdasarkan BAST Mutasi Antar-SKPD.`;
                    case 'KOREKSI_LAIN':
                        const tipeText = this.formData.tipe_koreksi === 'tambah' ? 'penambahan nilai buku (kapitalisasi susulan)' : 'pengurangan nilai buku (temuan audit BPK / penyesuaian dana)';
                        const lamaFmt = new Intl.NumberFormat('id-ID').format(this.selectedAstap?.total_realisasi || 0);
                        const baruFmt = new Intl.NumberFormat('id-ID').format(this.formData.nilai_realisasi_baru || 0);
                        return `Telah dilakukan koreksi nilai / ${tipeText} atas aset "${nama}" sebesar penyesuaian Rp ${nilai}${noBa}, sehingga nilai buku aset disesuaikan dari semula Rp ${lamaFmt} menjadi Rp ${baruFmt}.`;
                    default:
                        return `Telah dilakukan reklasifikasi aset tetap atas barang "${nama}" senilai Rp ${nilai}${noBa}.`;
                }
            },

            async submitFormTambah() {
                if (this.isSubmitting) return;

                // Validasi form Kemitraan Pihak Ketiga jika tujuan KIB adalah KEMITRAAN
                if (this.formData.tujuan_kib === 'KEMITRAAN') {
                    const spek = this.formData.spekBaru || {};
                    const mitraNama = (spek.kemitraan_mitra || '').trim();
                    if (!mitraNama) {
                        if (typeof window.showSimatToast === 'function') window.showSimatToast('Nama Perusahaan Mitra / Rekanan wajib diisi!', 'warning');
                        else alert('Nama Perusahaan Mitra / Rekanan wajib diisi!');
                        return;
                    }
                    const pimpinan = (spek.kemitraan_pimpinan || '').trim();
                    if (!pimpinan) {
                        if (typeof window.showSimatToast === 'function') window.showSimatToast('Pejabat Mitra / Direktur Rekanan wajib diisi!', 'warning');
                        else alert('Pejabat Mitra / Direktur Rekanan wajib diisi!');
                        return;
                    }
                    const pksNo = (spek.kemitraan_perjanjian_no || '').trim();
                    if (!pksNo) {
                        if (typeof window.showSimatToast === 'function') window.showSimatToast('Nomor Dokumen Perjanjian (PKS / MoU) wajib diisi!', 'warning');
                        else alert('Nomor Dokumen Perjanjian (PKS / MoU) wajib diisi!');
                        return;
                    }
                    const pksTgl = (spek.kemitraan_tanggal_pks || '').trim();
                    if (!pksTgl) {
                        if (typeof window.showSimatToast === 'function') window.showSimatToast('Tanggal Penandatanganan PKS wajib dipilih!', 'warning');
                        else alert('Tanggal Penandatanganan PKS wajib dipilih!');
                        return;
                    }
                    const tglMulai = (spek.kemitraan_tanggal_mulai || '').trim();
                    if (!tglMulai) {
                        if (typeof window.showSimatToast === 'function') window.showSimatToast('Tanggal Mulai Berlaku Konsesi wajib dipilih!', 'warning');
                        else alert('Tanggal Mulai Berlaku Konsesi wajib dipilih!');
                        return;
                    }
                    const tglSelesai = (spek.kemitraan_tanggal_selesai || '').trim();
                    if (!tglSelesai) {
                        if (typeof window.showSimatToast === 'function') window.showSimatToast('Tanggal Berakhir Konsesi wajib dipilih!', 'warning');
                        else alert('Tanggal Berakhir Konsesi wajib dipilih!');
                        return;
                    }
                    if (new Date(tglSelesai) < new Date(tglMulai)) {
                        if (typeof window.showSimatToast === 'function') window.showSimatToast('Tanggal Berakhir Konsesi tidak boleh mendahului Tanggal Mulai!', 'warning');
                        else alert('Tanggal Berakhir Konsesi tidak boleh mendahului Tanggal Mulai!');
                        return;
                    }
                }

                // Validasi khusus Mutasi Eksternal jika aset memiliki register
                if (this.formData.jenis_reklas === 'MUTASI_EKSTERNAL') {
                    if (!this.formData.skpd_tujuan || !this.formData.skpd_tujuan.trim()) {
                        const errMsg = '⚠️ SKPD / OPD Penerima wajib dipilih atau diisi!';
                        if (typeof window.showSimatToast === 'function') window.showSimatToast(errMsg, 'warning');
                        else alert(errMsg);
                        return;
                    }
                    if (!this.formData.tanggal_bast) {
                        const errMsg = '⚠️ Tanggal Dokumen BAST Mutasi Eksternal wajib diisi!';
                        if (typeof window.showSimatToast === 'function') window.showSimatToast(errMsg, 'warning');
                        else alert(errMsg);
                        return;
                    }
                    if (this.isTanggalBastInvalid()) {
                        const errMsg = '⚠️ Tanggal BAST Mutasi tidak boleh melebihi tanggal hari ini!';
                        if (typeof window.showSimatToast === 'function') window.showSimatToast(errMsg, 'error');
                        else alert(errMsg);
                        return;
                    }
                    if (this.selectedAstap && this.selectedAstap.registers && this.selectedAstap.registers.length > 0) {
                        if (!this.formData.selected_register_ids || this.formData.selected_register_ids.length === 0) {
                            const errMsg = '⚠️ Pilih minimal 1 unit register/NIBAR yang akan dimutasikan keluar OPD.';
                            if (typeof window.showSimatToast === 'function') window.showSimatToast(errMsg, 'error');
                            else alert(errMsg);
                            return;
                        }
                    }
                }

                this.isSubmitting = true;

                try {
                    const payload = {
                        ...this.formData,
                        selected_register_ids: (this.formData.jenis_reklas === 'MUTASI_EKSTERNAL') ? (this.formData.selected_register_ids || []) : null,
                        register_kondisi_map: (this.formData.jenis_reklas === 'MUTASI_EKSTERNAL') ? (this.formData.register_kondisi_map || {}) : null,
                        tipe_koreksi: this.formData.tipe_koreksi || 'kurang',
                        sub_koreksi: (this.formData.jenis_reklas === 'KOREKSI_LAIN') ? (this.formData.sub_koreksi || 'biasa') : null,
                        nilai_realisasi_baru: parseFloat(this.formData.nilai_realisasi_baru || 0),
                        alasan_reklas: (this.formData.alasan_reklas || '').trim() || (this.formData.keterangan || '').trim() || null,
                        keterangan: (this.formData.keterangan || '').trim() || this.getNarasiPreview(),
                        spesifikasi_baru: (['KOREKSI_REKENING', 'KDP_TO_DEFINITIF'].includes(this.formData.jenis_reklas) && this.formData.tujuan_kib) ? this.formData.spekBaru : null,
                        pihak_hibah: this.formData.pihak_hibah || null,
                        skpd_tujuan: this.formData.skpd_tujuan || null,
                        tanggal_bast: this.formData.tanggal_bast || null,
                        pejabat_opd_tujuan: this.formData.pejabat_opd_tujuan || null,
                        nip_pejabat_opd_tujuan: this.formData.nip_pejabat_opd_tujuan || null,
                        jabatan_opd_tujuan: this.formData.jabatan_opd_tujuan || null,
                        pj_asal_nama: this.formData.pj_asal_nama || 'BUDI HARTONO, S.Sos',
                        pj_asal_nip: this.formData.pj_asal_nip || '19760229 200801 1 010',
                        pj_asal_jabatan: this.formData.pj_asal_jabatan || 'Pengurus Barang Pengguna',
                        alamat_instansi: this.formData.alamat_instansi || null,
                        nomor_sk_dasar: this.formData.nomor_sk_dasar || null,
                    };

                    let bodyData;
                    let headers = {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    };

                    if (this.selectedDokumenFile) {
                        const fd = new FormData();
                        for (const key in payload) {
                            if (payload[key] === null || payload[key] === undefined) continue;
                            if (typeof payload[key] === 'object') {
                                fd.append(key, JSON.stringify(payload[key]));
                            } else {
                                fd.append(key, payload[key]);
                            }
                        }
                        fd.append('dokumen_file', this.selectedDokumenFile);
                        bodyData = fd;
                    } else {
                        headers['Content-Type'] = 'application/json';
                        bodyData = JSON.stringify(payload);
                    }

                    const response = await fetch('{{ route("master.reklasifikasi.store") }}', {
                        method: 'POST',
                        headers: headers,
                        body: bodyData,
                    });

                    const result = await response.json();

                    if (result.success) {
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast(result.message, 'success');
                        }
                        setTimeout(() => window.location.reload(), 600);
                    } else {
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast(result.message || 'Gagal menyimpan data reklasifikasi.', 'error');
                        } else {
                            alert(result.message || 'Gagal menyimpan data reklasifikasi.');
                        }
                    }
                } catch (error) {
                    if (typeof window.showSimatToast === 'function') {
                        window.showSimatToast('Terjadi kesalahan jaringan: ' + error.message, 'error');
                    } else {
                        alert('Terjadi kesalahan jaringan saat menyimpan data: ' + error.message);
                    }
                } finally {
                    this.isSubmitting = false;
                }
            },

            confirmHapusReklas(id, nama) {
                this.deleteTargetId = id;
                this.deleteTargetName = nama;
                this.showConfirmDelete = true;
            },

            async executeHapusReklas() {
                if (!this.deleteTargetId) return;

                try {
                    const url = `{{ url('/master-data/reklasifikasi') }}/${this.deleteTargetId}`;
                    const response = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                    });

                    const result = await response.json();

                    if (result.success) {
                        const targetId = this.deleteTargetId;
                        const row = document.getElementById('row-reklas-' + targetId);
                        if (row) {
                            row.remove();
                        }
                        const countEl = document.getElementById('total-reklas-count');
                        if (countEl) {
                            const current = parseInt(countEl.innerText.replace(/\D/g, '')) || 0;
                            countEl.innerText = 'Total ' + Math.max(0, current - 1) + ' Transaksi';
                        }
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast(result.message || 'Transaksi reklasifikasi berhasil dibatalkan/dihapus.', 'success');
                        }
                        setTimeout(() => window.location.reload(), 700);
                    } else {
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast(result.message || 'Gagal menghapus transaksi.', 'error');
                        } else {
                            alert(result.message || 'Gagal menghapus transaksi.');
                        }
                    }
                } catch (error) {
                    if (typeof window.showSimatToast === 'function') {
                        window.showSimatToast('Terjadi kesalahan saat menghapus transaksi: ' + error.message, 'error');
                    } else {
                        alert('Terjadi kesalahan saat menghapus transaksi: ' + error.message);
                    }
                } finally {
                    this.showConfirmDelete = false;
                }
            },

            exportToExcel() {
                if (typeof XLSX === 'undefined') {
                    alert('⚠️ Pustaka Excel sedang dimuat. Silakan coba kembali sesaat lagi.');
                    return;
                }

                // Pilih tabel sesuai tab aktif
                let table = null;
                if (this.activeTab === 'log') {
                    // Tabel log transaksi (tabel kedua di DOM)
                    const tables = document.querySelectorAll('table');
                    table = tables.length >= 2 ? tables[1] : tables[0];
                } else {
                    // Tabel matriks neraca (tabel pertama)
                    table = document.querySelector('table');
                }

                if (!table) {
                    alert('⚠️ Tabel tidak ditemukan.');
                    return;
                }

                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);

                // Format ulang angka numerik agar tidak menjadi string teks pada Excel
                for (const cellKey in ws) {
                    if (cellKey[0] === '!') continue;
                    const cell = ws[cellKey];
                    if (cell && cell.t === 's' && typeof cell.v === 'string') {
                        const str = cell.v.trim();
                        if (str.startsWith('Rp') || str.includes('Rp ')) {
                            const cleanNum = str.replace(/Rp\s*/g, '').replace(/\./g, '').replace(/,/g, '.').trim();
                            if (!isNaN(cleanNum) && cleanNum !== '') {
                                cell.t = 'n';
                                cell.v = parseFloat(cleanNum);
                                cell.z = '#,##0';
                            }
                        }
                    }
                }

                const sheetName = this.activeTab === 'log' ? 'Log_Transaksi' : 'Sheet3_REKLAS';
                XLSX.utils.book_append_sheet(wb, ws, sheetName);

                const suffix = this.activeTab === 'log' ? 'LOG' : 'MATRIKS';
                const filename = `REKLAS_${suffix}_RSUD_KOESNANDI_{{ $selectedTahun }}_TW{{ $selectedTw }}.xlsx`;
                XLSX.writeFile(wb, filename);
            }
        };
    }
</script>
