<!-- PUSTAKA EXCEL DENGAN STYLING DUKUNGAN FULL -->
<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>

<!-- ALPINE.JS SCRIPTS MASTER REKLASIFIKASI (PMDN 108 & SPEK DINAMIS) -->
<script>
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : \App\Models\JenisAstap::getNested108());
    window.kandidatAstaps = @json($kandidatAstaps);
    window.templateRows = @json($templateRows);

    function masterReklasifikasi() {
        return {
            activeTab: 'matriks',
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
                    aset_lain_kondisi: 'Rusak Berat',
                    aset_lain_alasan: 'Tidak digunakan lagi dalam operasional RSUD / Menunggu Penghapusan',
                    aset_lain_lokasi: '',
                    kemitraan_mitra: '',
                    kemitraan_perjanjian_no: '',
                    kemitraan_jangka_waktu: '5 Tahun',
                },
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
                    'ASET LAIN': '1.5.4',
                    'ASET LAIN-LAIN': '1.5.4',
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
            },

            clearSubRincian() {
                this.reklasSubRincianKode = '';
                this.reklasSubRincianNama = '';
                this.searchReklasSubRincian = '';
                this.isReklasSubRincianOpen = false;
                this.formData.tujuan_kode = this.reklasSubSubRincianKode || '';
                this.formData.tujuan_nama = this.reklasSubSubRincianNama || '';
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
            },

            clearSubSubRincian() {
                this.reklasSubSubRincianKode = '';
                this.reklasSubSubRincianNama = '';
                this.searchReklasSubSubRincian = '';
                this.isReklasSubSubRincianOpen = false;
                this.formData.tujuan_kode = this.reklasSubRincianKode || '';
                this.formData.tujuan_nama = this.reklasSubRincianNama || '';
            },

            matchTujuanRowByPrefix(prefix) {
                if (!prefix || !window.templateRows) return;
                const p3 = prefix.substring(0, 8);
                const found = window.templateRows.find(r => r.kode_prefix && (r.kode_prefix.startsWith(p3) || p3.startsWith(r.kode_prefix)));
                if (found) {
                    this.formData.jenis_reklasifikasi_tujuan_id = found.id;
                }
            },

            openModalTambah() {
                this.selectedAstap = null;
                this.reklasSubRincianKode = '';
                this.reklasSubRincianNama = '';
                this.reklasSubSubRincianKode = '';
                this.reklasSubSubRincianNama = '';
                this.formData = {
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
                    alasan_reklas: '',
                    keterangan: '',
                    tipe_koreksi: 'kurang',
                    nilai_realisasi_baru: 0,
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

            onSelectAstap(astapId) {
                if (!astapId) {
                    this.selectedAstap = null;
                    return;
                }
                const found = (window.kandidatAstaps || []).find(it => String(it.id) === String(astapId));
                if (!found) return;

                this.selectedAstap = found;
                this.formData.nama_barang = found.nama_barang || '';
                this.formData.nilai_realisasi_baru = parseFloat(found.total_realisasi || 0);

                if (this.formData.jenis_reklas === 'KOREKSI_LAIN') {
                    this.formData.nilai_reklas = 0;
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
                    this.formData.tujuan_kib = 'KIB B';
                    this.onTujuanKibChange();
                } else if (this.formData.jenis_reklas === 'HIBAH_MASUK') {
                    const hibahRow = (window.templateRows || []).find(r => r.kode_prefix === 'KOR_HIBAH');
                    if (hibahRow) {
                        this.formData.jenis_reklasifikasi_asal_id = hibahRow.id;
                    }
                    this.formData.asal_kib = 'HIBAH';
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

                // KIB A - Tanah
                this.formData.spekBaru.tanah_luas_m2 = spec.luas_m2 || spec.tanah_luas_m2 || '';
                this.formData.spekBaru.tanah_hak = spec.hak_tanah || spec.tanah_hak || 'Hak Pakai';
                this.formData.spekBaru.tanah_sertifikat_no = spec.sertifikat_no || spec.tanah_sertifikat_no || '';
                this.formData.spekBaru.tanah_sertifikat_tgl = spec.sertifikat_tgl || spec.tanah_sertifikat_tgl || '';
                this.formData.spekBaru.tanah_penggunaan = spec.penggunaan || spec.tanah_penggunaan || it.nama_barang || 'Kompleks RSUD';
                this.formData.spekBaru.tanah_asal_usul = spec.tanah_asal_usul || 'Pengadaan APBD / BLUD';
                this.formData.spekBaru.tanah_alamat = defaultAlamat;

                // KIB B - Mesin & Peralatan
                this.formData.spekBaru.mesin_merk = spec.merk || it.merk_type || '';
                this.formData.spekBaru.mesin_type = spec.type || '';
                this.formData.spekBaru.mesin_no_pabrik = spec.no_pabrik || '';
                this.formData.spekBaru.mesin_ukuran_cc = spec.ukuran_cc || '';
                this.formData.spekBaru.mesin_bahan = spec.bahan || 'Logam / Komponen Elektronik';
                this.formData.spekBaru.mesin_no_polisi = spec.no_polisi || '';

                // KIB C - Gedung & Bangunan
                this.formData.spekBaru.gedung_konstruksi_bertingkat = spec.konstruksi_bertingkat || spec.bertingkat || 'Bertingkat';
                this.formData.spekBaru.gedung_konstruksi_beton = spec.konstruksi_beton || spec.beton || 'Beton';
                this.formData.spekBaru.gedung_luas_lantai_m2 = spec.luas_lantai_m2 || '';
                this.formData.spekBaru.gedung_dokumen_nomor = spec.dokumen_nomor || '';
                this.formData.spekBaru.gedung_dokumen_tgl = spec.dokumen_tgl || '';
                this.formData.spekBaru.gedung_status_tanah = spec.status_tanah || 'Tanah Pemda';
                this.formData.spekBaru.gedung_alamat = defaultAlamat;

                // KIB D - Jalan, Jaringan & Irigasi
                this.formData.spekBaru.jaringan_konstruksi = spec.konstruksi || 'Aspal / Beton';
                this.formData.spekBaru.jaringan_panjang_km = spec.panjang_km || '';
                this.formData.spekBaru.jaringan_lebar_m = spec.lebar_m || '';
                this.formData.spekBaru.jaringan_luas_m2 = spec.luas_m2 || '';
                this.formData.spekBaru.jaringan_alamat = defaultAlamat;

                // KIB E - Lainnya
                this.formData.spekBaru.lainnya_judul_pencipta = spec.judul_pencipta || it.nama_barang || '';
                this.formData.spekBaru.lainnya_bahan = spec.bahan || 'Kertas / Kanvas / Lainnya';

                // ATB
                this.formData.spekBaru.atb_nama_software = spec.nama_software || it.nama_barang || '';
                this.formData.spekBaru.atb_pengembang = spec.pengembang || '';
                this.formData.spekBaru.atb_masa_manfaat = spec.masa_manfaat || 4;
                this.formData.spekBaru.atb_nomor_lisensi = spec.nomor_lisensi || '';
            },

            openDetailReklas(item) {
                this.detailItem = item;
                this.showModalDetail = true;
            },

            getJenisReklasLabel(code) {
                const map = {
                    'KOREKSI_REKENING': 'Koreksi Rekening / Pindah KIB',
                    'KDP_TO_DEFINITIF': 'KDP Selesai ➔ Definitif',
                    'EKSTRAKOMPTABEL': 'Ekstrakomptabel (Nilai ≤ Rp 300.000)',
                    'HIBAH_MASUK': 'Hibah / Bantuan Masuk',
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
                    if (m.mesin_merk) out['Merk / Pabrikan'] = m.mesin_merk;
                    if (m.mesin_type) out['Tipe / Model'] = m.mesin_type;
                    if (m.mesin_no_pabrik) out['No. Pabrik / Seri'] = m.mesin_no_pabrik;
                    if (m.mesin_ukuran_cc) out['Ukuran / Kapasitas'] = m.mesin_ukuran_cc;
                    if (m.mesin_bahan) out['Bahan Material'] = m.mesin_bahan;
                    if (m.mesin_no_polisi) out['No. Polisi'] = m.mesin_no_polisi;
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
                    case 'HIBAH_MASUK':
                        return `Telah dicatat penambahan aset tetap melalui reklasifikasi hibah/bantuan pemerintah atas barang "${nama}" senilai Rp ${nilai}${noBa}.`;
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
                this.isSubmitting = true;

                try {
                    const payload = {
                        ...this.formData,
                        tipe_koreksi: this.formData.tipe_koreksi || 'kurang',
                        nilai_realisasi_baru: parseFloat(this.formData.nilai_realisasi_baru || 0),
                        alasan_reklas: (this.formData.alasan_reklas || '').trim() || (this.formData.keterangan || '').trim() || null,
                        keterangan: (this.formData.keterangan || '').trim() || this.getNarasiPreview(),
                        spesifikasi_baru: (['KOREKSI_REKENING', 'KDP_TO_DEFINITIF'].includes(this.formData.jenis_reklas) && this.formData.tujuan_kib) ? this.formData.spekBaru : null,
                    };

                    const response = await fetch('{{ route("master.reklasifikasi.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
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

                const table = document.querySelector('table');
                if (!table) {
                    alert('⚠️ Tabel tidak ditemukan.');
                    return;
                }

                const wb = XLSX.utils.book_new();
                const ws = XLSX.utils.table_to_sheet(table);
                XLSX.utils.book_append_sheet(wb, ws, "Sheet3_REKLAS");

                const filename = `REKLAS_ASET_RSUD_KOESNANDI_{{ $selectedTahun }}_TW{{ $selectedTw }}.xlsx`;
                XLSX.writeFile(wb, filename);
            }
        };
    }
</script>
