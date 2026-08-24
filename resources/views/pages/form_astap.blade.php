<x-layout :title="request()->routeIs('astap.edit') ? 'Ubah Data ASTAP - SIMAT-RK' : 'Tambah Data ASTAP Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('astap.edit') ? 'Ubah Data ASTAP' : 'Tambah Data ASTAP Baru')
    @section('breadcrumb', request()->routeIs('astap.edit') ? 'Master Utama / Data ASTAP / Ubah Data' : 'Master Utama / Data ASTAP / Tambah Baru')

    <script>
        window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);
        window.dbJenisPengadaans = @json(!empty($dbJenisPengadaans) ? $dbJenisPengadaans : []);
        window.dbRekeningBelanjas = @json(!empty($dbRekeningBelanjas) ? $dbRekeningBelanjas : []);

        function astapForm() {
            return {
                isEdit: {{ request()->routeIs('astap.edit') ? 'true' : 'false' }},
                currentStep: 1,
                totalSteps: 4,

                // Master Data Hierarki SIPD Langkah 1 (Diisi dinamis 100% dari SQLite Database)
                sipdData: [],

                // Master Data Rekening Belanja SIPD Langkah 2 (Diisi dinamis 100% dari SQLite Database)
                masterRekeningBelanja: (window.dbRekeningBelanjas && window.dbRekeningBelanjas.length > 0)
                    ? window.dbRekeningBelanjas.map(item => ({
                        id: item.id,
                        kode_rek: item.kode_rek,
                        nama_belanja: item.nama_belanja,
                        kelompok: item.kelompok,
                        default_jenis_kode: item.kelompok === 'Tanah' ? '1.3.1' : (item.kelompok === 'Bangunan' ? '1.3.3' : '1.3.2')
                      }))
                    : [
                        { kode_rek: '5.2.01.01.01.0002', nama_belanja: 'Belanja Modal Pengadaan Tanah Fasilitas Pelayanan Kesehatan', default_jenis_kode: '1.3.1' },
                        { kode_rek: '5.2.02.08.01.0005', nama_belanja: 'Belanja Modal Alat Kedokteran Radiologi & Imaging (CT-Scan / X-Ray)', default_jenis_kode: '1.3.2' },
                        { kode_rek: '5.2.03.01.01.0001', nama_belanja: 'Belanja Modal Bangunan Gedung Rawat Inap & Poliklinik', default_jenis_kode: '1.3.3' },
                        { kode_rek: '5.2.04.03.01.0004', nama_belanja: 'Belanja Modal Instalasi Jaringan Pipa Gas Oksigen Sentral Medis', default_jenis_kode: '1.3.4' },
                        { kode_rek: '5.2.05.01.01.0003', nama_belanja: 'Belanja Modal Bahan Pustaka dan Jurnal Ilmiah Kedokteran', default_jenis_kode: '1.3.5' },
                        { kode_rek: '5.2.06.01.01.0001', nama_belanja: 'Belanja Modal Lisensi Software SIMRS & Aset Tidak Berwujud', default_jenis_kode: '1.5.3' },
                        { kode_rek: '5.2.07.01.01.0001', nama_belanja: 'Belanja Modal Konstruksi Dalam Pengerjaan Gedung Rawat Inap', default_jenis_kode: '1.3.6' }
                      ],

                // Master Data Jenis ASTAP & Sub Rincian Objek PMDN 108 Langkah 2
                masterJenisAstap108: (window.dbMasterJenisAstap108 && window.dbMasterJenisAstap108.length > 0)
                    ? window.dbMasterJenisAstap108
                    : [
                        {
                            kode: '1.3.1',
                            nama: 'TANAH',
                            subRincian: [
                                { 
                                    kode: '1.3.1.01.01.02', 
                                    nama: 'TANAH UNTUK BANGUNAN GEDUNG RSUD & FASILITAS',
                                    subSubRincian: [
                                        { kode: '1.3.1.01.01.02.013', nama: 'Tanah Bangunan Apotik / Rumah Sakit' }
                                    ]
                                }
                            ]
                        },
                        {
                            kode: '1.3.2',
                            nama: 'PERALATAN DAN MESIN',
                            subRincian: [
                                { 
                                    kode: '1.3.2.02.01.01', 
                                    nama: 'ALAT KEDOKTERAN UMUM & RADIOLOGI',
                                    subSubRincian: [
                                        { kode: '1.3.2.02.01.01.005', nama: 'CT-Scan 128 Slice High Resolution' }
                                    ]
                                }
                            ]
                        },
                        {
                            kode: '1.3.3',
                            nama: 'GEDUNG DAN BANGUNAN',
                            subRincian: [
                                { 
                                    kode: '1.3.3.01.01.08', 
                                    nama: 'BANGUNAN GEDUNG RAWAT INAP VIP & PAVILIUN',
                                    subSubRincian: [
                                        { kode: '1.3.3.01.01.08.001', nama: 'Gedung Rawat Inap VIP Terpadu Lt 2' }
                                    ]
                                }
                            ]
                        },
                        {
                            kode: '1.3.4',
                            nama: 'JALAN, IRIGASI DAN JARINGAN',
                            subRincian: [
                                { 
                                    kode: '1.3.4.03.01.04', 
                                    nama: 'JARINGAN DISTRIBUSI GAS MEDIS & OKSIGEN',
                                    subSubRincian: [
                                        { kode: '1.3.4.03.01.04.004', nama: 'Jaringan Pipa Oksigen Sentral Medis & Vakum' }
                                    ]
                                }
                            ]
                        },
                        {
                            kode: '1.3.5',
                            nama: 'ASET TETAP LAINNYA',
                            subRincian: [
                                { 
                                    kode: '1.3.5.01.01.01', 
                                    nama: 'BUKU PERPUSTAKAAN & JURNAL RISET KEDOKTERAN',
                                    subSubRincian: [
                                        { kode: '1.3.5.01.01.01.002', nama: 'Buku Jurnal Kedokteran, Farmakologi & Riset Klinis' }
                                    ]
                                }
                            ]
                        },
                        {
                            kode: '1.3.6',
                            nama: 'KONSTRUKSI DALAM PENGERJAAN',
                            subRincian: [
                                { 
                                    kode: '1.3.6.01.01.01', 
                                    nama: 'KONSTRUKSI DALAM PENGERJAAN (KDP)',
                                    subSubRincian: [
                                        { kode: '1.3.6.01.01.01.001', nama: 'Pembangunan Gedung Rawat Inap Baru Lt 3 (KDP)' }
                                    ]
                                }
                            ]
                        },
                        {
                            kode: '1.5.3',
                            nama: 'ASET TIDAK BERWUJUD',
                            subRincian: [
                                { 
                                    kode: '1.5.3.01.01.01', 
                                    nama: 'SOFTWARE SISTEM INFORMASI KESEHATAN (SIMRS)',
                                    subSubRincian: [
                                        { kode: '1.5.3.01.01.01.001', nama: 'Software SIMAT-RK RSUD Dr. H. Koesnandi' }
                                    ]
                                }
                            ]
                        }
                    ],

                // Data Model Multi-Step
                formData: {
                    // ===============================================================
                    // LANGKAH 1: MEMILIH JENIS PENGADAAN (FILTER BERTINGKAT SIPD)
                    // ===============================================================
                    jenis_pengadaan_id: 1,
                    program_kode: '',
                    program_nama: '',
                    kegiatan_kode: '',
                    kegiatan_nama: '',
                    sub_kegiatan_kode: '',
                    sub_kegiatan_nama: '',
                    keterangan_pengadaan: '',

                    // ===============================================================
                    // LANGKAH 2: REKENING BELANJA SIPD & JENIS ASTAP (FILTER BERTINGKAT)
                    // ===============================================================
                    kode_rek: '5.2.01.01.01.0002',
                    nama_belanja: 'Belanja Modal Pengadaan Tanah Fasilitas Pelayanan Kesehatan',
                    jenis_aset_kode: '1.3.1',
                    jenis_aset_nama: 'TANAH',
                    sub_rincian_kode: '1.3.1.01.01.02',
                    sub_rincian_nama: 'TANAH UNTUK BANGUNAN GEDUNG RSUD & FASILITAS',
                    jumlah_anggaran: 8500000000,
                    jumlah_realisasi: 8500000000,

                    // ===============================================================
                    // LANGKAH 3: RINCIAN BELANJA MODAL / DOKUMEN PENGADAAN (PMDN 108)
                    // ===============================================================
                    // 1. Khusus Tanah (PMDN 108 KIB A)
                    tanah_nama_barang: 'Tanah Bangunan Apotik / Rumah Sakit',
                    tanah_kode_barang: '1.3.1.01.01.02.013',
                    tanah_hak: 'Hak Pakai',
                    tanah_sertifikat_tgl: '1984-03-12',
                    tanah_sertifikat_no: 'HP-108/1984',
                    tanah_kondisi: 'B',
                    tanah_penggunaan: 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                    tanah_jumlah_bidang: 1,
                    tanah_luas_m2: 35400,
                    tanah_nilai_perencanaan: 150000000,
                    tanah_nilai_fisik: 8200000000,
                    tanah_nilai_pengawasan: 150000000,

                    // 2. Khusus Peralatan dan Mesin (PMDN 108 KIB B)
                    mesin_nama_barang: 'CT-Scan 128 Slice High Resolution',
                    mesin_kode_barang: '1.3.2.02.01.01.005',
                    mesin_merk: 'Siemens SOMATOM go.Now',
                    mesin_type: '128 Slice Dual Energy',
                    mesin_ukuran: '128 Slices / 0.33s',
                    mesin_no_pabrik: 'SN-RAD-2026-88192',
                    mesin_bahan: 'Logam & Komponen Elektronik Medis',
                    mesin_kondisi: 'B',
                    ruang_pemegang: 'Instalasi Radiologi & Imaging Sentral',
                    mesin_jumlah_barang: 1,
                    mesin_satuan: 'Unit',
                    mesin_nilai_satuan: 8475000000,
                    mesin_administrasi_proyek: 25000000,

                    // 3. Khusus Gedung dan Bangunan (PMDN 108 KIB C)
                    gedung_nama_barang: 'Gedung Rawat Inap VIP Terpadu Lt 2',
                    gedung_kode_barang: '1.3.3.01.01.08.001',
                    gedung_luas_m2: 850,
                    gedung_kondisi: 'B',
                    gedung_bertingkat: 'Bertingkat',
                    gedung_beton: 'Beton',
                    gedung_status_tanah: 'Tanah Hak Pakai Pemkab',
                    gedung_kode_aset_tanah: '1.3.1.01.01.02.013',
                    gedung_is_baru: 'Baru',
                    gedung_kapitalisasi_tahun_induk: '2020',
                    gedung_kapitalisasi_nilai_induk: 3500000000,
                    gedung_jumlah_bangunan: 1,
                    gedung_satuan: 'Gedung',
                    gedung_nilai_perencanaan: 75000000,
                    gedung_nilai_fisik: 1850000000,
                    gedung_nilai_pengawasan: 50000000,
                    gedung_nilai_pip: 25000000,

                    // 4. Khusus Jalan, Irigasi dan Jaringan (PMDN 108 KIB D)
                    jaringan_nama_barang: 'Jaringan Pipa Oksigen Sentral Medis & Vakum',
                    jaringan_kode_barang: '1.3.4.03.01.04.004',
                    jaringan_konstruksi: 'Pipa Tembaga Medis ASTM B819 & Box Zone Valve',
                    jaringan_panjang_m: 450,
                    jaringan_lebar_m: 0,
                    jaringan_luas_m2: 0,
                    jaringan_kondisi: 'B',
                    jaringan_status_tanah: 'Tanah Hak Pakai RSUD',
                    jaringan_kode_aset_tanah: '1.3.1.01.01.02.013',
                    jaringan_is_baru: 'Baru',
                    jaringan_kapitalisasi_tahun_induk: '2021',
                    jaringan_kapitalisasi_nilai_induk: 850000000,
                    jaringan_jumlah: 1,
                    jaringan_satuan: 'Paket',
                    jaringan_nilai_perencanaan: 35000000,
                    jaringan_nilai_fisik: 620000000,
                    jaringan_nilai_pengawasan: 25000000,
                    jaringan_nilai_pip: 15000000,

                    // 5. Khusus Aset Tetap Lainnya (PMDN 108 KIB E)
                    lainnya_nama_barang: 'Buku Jurnal Kedokteran, Farmakologi & Riset Klinis',
                    lainnya_kode_barang: '1.3.5.01.01.01.002',
                    lainnya_buku_judul: 'Pedoman Standar Pelayanan Klinis & Formularium RSUD Dr. H. Koesnandi',
                    lainnya_buku_pencipta: 'Komite Medik & Tim Farmasi Klinis RSUD',
                    lainnya_buku_spesifikasi: 'Edisi Revisi 2026 / Hardcover Lux / 850 Halaman',
                    lainnya_kesenian_asal: 'Jawa Timur / Bondowoso',
                    lainnya_kesenian_pencipta: 'Sanggar Seni Budaya Daerah',
                    lainnya_kesenian_spesifikasi: 'Lukisan Sejarah Rumah Sakit & Tokoh Pendiri',
                    lainnya_kesenian_bahan: 'Kanvas & Cat Minyak / Frame Kayu Jati',
                    lainnya_kesenian_ukuran: '200 x 120 cm',
                    lainnya_hewan_jenis: 'Tanaman Peneduh & Taman Herbal Medis',
                    lainnya_hewan_spesifikasi: 'Pohon Tabebuya & Palem Raja Tinggi 3M',
                    ruang_pemegang_lainnya: 'Instalasi Perpustakaan Medis & Diklat RSUD',
                    lainnya_jumlah_barang: 15,
                    lainnya_satuan: 'Eksemplar',
                    lainnya_nilai_satuan: 450000,
                    lainnya_administrasi_proyek: 250000,

                    // 6. Khusus Aset Tidak Berwujud (PMDN 108 ATB / 1.5.3)
                    atb_nama_barang: 'Software SIMAT-RK RSUD Dr. H. Koesnandi',
                    atb_kode_barang: '1.5.3.01.01.01.001',
                    atb_judul_nama: 'Aplikasi SIMAT-RK (Sistem Informasi Manajemen Aset Tetap Terintegrasi)',
                    atb_pencipta: 'Tim IT SIMRS RSUD & Tim Pengembang Sistem',
                    atb_spesifikasi: 'Web-Based (Laravel 12, Alpine.js, Tailwind), Role-Based Access Control, Realtime ASTAP Export & Integrasi SatuSehat',
                    ruang_pemegang_atb: 'Instalasi SIMRS & Rekam Medis RSUD Dr. H. Koesnandi',
                    atb_jumlah: 1,
                    atb_satuan: 'Paket',
                    atb_nilai_satuan: 145000000,
                    atb_administrasi_proyek: 5000000,

                    // 7. Khusus Konstruksi Dalam Pengerjaan (PMDN 108 KIB F / 1.3.6)
                    kdp_nama_barang: 'Pembangunan Gedung Rawat Inap Baru Lt 3 (KDP)',
                    kdp_kode_barang: '1.3.6.01.01.01.001',
                    kdp_bangunan: 'Bertingkat',
                    kdp_beton: 'Beton',
                    kdp_luas_m2: 1200,
                    kdp_progres_persen: 65,
                    kdp_status_tanah: 'Tanah Hak Pakai RSUD',
                    kdp_sertifikat_no: 'HP-108/1984',
                    kdp_sertifikat_tgl: '1984-03-12',
                    kdp_kode_aset_tanah: '1.3.1.01.01.02.013',
                    kdp_tgl_mulai: '2026-02-01',
                    kdp_tgl_target_selesai: '2026-11-30',
                    kdp_jumlah_bangunan: 1,
                    kdp_satuan: 'Gedung',
                    kdp_nilai_perencanaan: 125000000,
                    kdp_nilai_fisik: 2450000000,
                    kdp_nilai_pengawasan: 85000000,
                    kdp_nilai_pip: 40000000,

                    // 8. Riwayat Pembelian (SPK, SP, Kwitansi, Invoice)
                    spk_nomor: '028/SPK-KTR/V/2026',
                    spk_tanggal: '2026-05-12',
                    surat_pesanan_nomor: '028/SP-RSUD/V/2026',
                    surat_pesanan_tanggal: '2026-05-15',
                    kwitansi_nomor: 'KW-028/KTR/2026',
                    kwitansi_tanggal: '2026-06-02',
                    faktur_nomor: 'INV-2026-028',
                    faktur_tanggal: '2026-06-05',

                    // 9. SP2D & BAST
                    sp2d_nomor: '0129/SP2D/BLUD/2026',
                    sp2d_tanggal: '2026-06-15',
                    bast_dokumen_nomor: '000.2.3.2/224/430.10.7/2026',
                    bast_dokumen_tanggal: '2026-06-30',

                    // ===============================================================
                    // LANGKAH 4: LOKASI, PIHAK PENYEDIA, PPK & KETERANGAN
                    // ===============================================================
                    alamat_barang: 'Jl. Piere Tendean No. 3, Kel. Badean, Kec. Bondowoso (Area Paviliun RSUD Dr. H. Koesnandi)',
                    penyedia_nama: 'PT. Medika Sarana Utama',
                    penyedia_pemilik: 'Ir. H. Budi Santoso, M.T.',
                    penyedia_rekening_nama: 'PT. Medika Sarana Utama',
                    penyedia_rekening_nomor: '143-00-9876543-2 (Bank Jatim Cab. Bondowoso)',
                    penyedia_alamat: 'Jl. Raya Darmo No. 45, Wonokromo, Kota Surabaya, Jawa Timur',
                    ppk_nama: 'dr. Slamet Widodo, M.Kes',
                    ppk_nip: '19760229 200801 1 010',
                    keterangan_tambahan: 'Aset telah selesai diverifikasi dan siap dibukukan ke dalam KIB RSUD Dr. H. Koesnandi Tahun Anggaran 2026.'
                },

                init() {
                    // 1. Membangun Hirarki SIPD Langkah 1 murni dari Database SQLite (jenis_pengadaans)
                    if (window.dbJenisPengadaans && window.dbJenisPengadaans.length > 0) {
                        const dynamicSipd = [];
                        window.dbJenisPengadaans.forEach(item => {
                            let prog = dynamicSipd.find(p => p.kode === item.program_kode);
                            if (!prog) {
                                prog = { kode: item.program_kode, nama: item.program_nama, kegiatans: [] };
                                dynamicSipd.push(prog);
                            }
                            let keg = prog.kegiatans.find(k => k.kode === item.kegiatan_kode);
                            if (!keg) {
                                keg = { kode: item.kegiatan_kode, nama: item.kegiatan_nama, subKegiatans: [] };
                                prog.kegiatans.push(keg);
                            }
                            let sub = keg.subKegiatans.find(s => s.kode === item.sub_kegiatan_kode);
                            if (!sub) {
                                keg.subKegiatans.push({
                                    id: item.id,
                                    kode: item.sub_kegiatan_kode,
                                    nama: item.sub_kegiatan_nama,
                                    keterangan: 'Alokasi Pengadaan SIPD ' + item.sub_kegiatan_nama + ' (' + item.sub_kegiatan_kode + ')'
                                });
                            }
                        });
                        this.sipdData = dynamicSipd;

                        // Set default terpilih pada Langkah 1 jika data tersedia
                        if (this.sipdData.length > 0) {
                            const firstProg = this.sipdData[0];
                            this.formData.program_kode = firstProg.kode;
                            this.formData.program_nama = firstProg.nama;
                            if (firstProg.kegiatans && firstProg.kegiatans.length > 0) {
                                const firstKeg = firstProg.kegiatans[0];
                                this.formData.kegiatan_kode = firstKeg.kode;
                                this.formData.kegiatan_nama = firstKeg.nama;
                                if (firstKeg.subKegiatans && firstKeg.subKegiatans.length > 0) {
                                    const firstSub = firstKeg.subKegiatans[0];
                                    this.formData.sub_kegiatan_kode = firstSub.kode;
                                    this.formData.sub_kegiatan_nama = firstSub.nama;
                                    this.formData.keterangan_pengadaan = firstSub.keterangan;
                                    this.formData.jenis_pengadaan_id = firstSub.id;
                                }
                            }
                        }
                    }

                    // 2. Membangun Rekening Belanja Langkah 2 murni dari Database SQLite (rekening_belanjas)
                    if (window.dbRekeningBelanjas && window.dbRekeningBelanjas.length > 0) {
                        this.masterRekeningBelanja = window.dbRekeningBelanjas.map(item => ({
                            id: item.id,
                            kode_rek: item.kode_rek,
                            nama_belanja: item.nama_belanja,
                            kelompok: item.kelompok,
                            default_jenis_kode: item.kelompok === 'Tanah' ? '1.3.1' : (item.kelompok === 'Bangunan' ? '1.3.3' : '1.3.2')
                        }));
                        if (this.masterRekeningBelanja.length > 0) {
                            this.formData.kode_rek = this.masterRekeningBelanja[0].kode_rek;
                            this.formData.nama_belanja = this.masterRekeningBelanja[0].nama_belanja;
                        }
                    }

                    // 3. Membangun Jenis ASTAP PMDN 108 murni dari Database SQLite (jenis_astaps)
                    if (window.dbMasterJenisAstap108 && window.dbMasterJenisAstap108.length > 0) {
                        const currentJenis = window.dbMasterJenisAstap108.find(j => j.kode === this.formData.jenis_aset_kode);
                        if (!currentJenis) {
                            this.onJenisAstapChange(window.dbMasterJenisAstap108[0].kode);
                        } else {
                            const currentSub = currentJenis.subRincian ? currentJenis.subRincian.find(s => s.kode === this.formData.sub_rincian_kode) : null;
                            if (!currentSub && currentJenis.subRincian && currentJenis.subRincian.length > 0) {
                                this.onSubRincianChange(currentJenis.subRincian[0].kode);
                            }
                        }
                    }
                },

                get isTanah() {
                    return this.formData.jenis_aset_kode === '1.3.1' || this.formData.jenis_aset_nama.includes('TANAH');
                },

                get isMesin() {
                    return this.formData.jenis_aset_kode === '1.3.2' || this.formData.jenis_aset_nama.includes('PERALATAN');
                },

                get isGedung() {
                    return this.formData.jenis_aset_kode === '1.3.3' || this.formData.jenis_aset_nama.includes('GEDUNG') || this.formData.jenis_aset_nama.includes('BANGUNAN');
                },

                get isJaringan() {
                    return this.formData.jenis_aset_kode === '1.3.4' || this.formData.jenis_aset_nama.includes('JARINGAN') || this.formData.jenis_aset_nama.includes('JALAN') || this.formData.jenis_aset_nama.includes('IRIGASI');
                },

                get isAsetLainnya() {
                    return this.formData.jenis_aset_kode === '1.3.5' || this.formData.jenis_aset_nama.includes('ASET TETAP LAINNYA') || this.formData.jenis_aset_nama.includes('LAINNYA');
                },

                get isAtb() {
                    return this.formData.jenis_aset_kode === '1.5.3' || this.formData.jenis_aset_nama.includes('TIDAK BERWUJUD') || this.formData.jenis_aset_nama.includes('ATB');
                },

                get isKdp() {
                    return this.formData.jenis_aset_kode === '1.3.6' || this.formData.jenis_aset_nama.includes('KONSTRUKSI') || this.formData.jenis_aset_nama.includes('KDP');
                },

                get totalNilaiTanah() {
                    return Number(this.formData.tanah_nilai_perencanaan || 0) + 
                           Number(this.formData.tanah_nilai_fisik || 0) + 
                           Number(this.formData.tanah_nilai_pengawasan || 0);
                },

                get totalNilaiMesin() {
                    return (Number(this.formData.mesin_jumlah_barang || 1) * Number(this.formData.mesin_nilai_satuan || 0)) + 
                           Number(this.formData.mesin_administrasi_proyek || 0);
                },

                get totalNilaiGedung() {
                    return Number(this.formData.gedung_nilai_perencanaan || 0) + 
                           Number(this.formData.gedung_nilai_fisik || 0) + 
                           Number(this.formData.gedung_nilai_pengawasan || 0) + 
                           Number(this.formData.gedung_nilai_pip || 0);
                },

                get totalNilaiJaringan() {
                    return Number(this.formData.jaringan_nilai_perencanaan || 0) + 
                           Number(this.formData.jaringan_nilai_fisik || 0) + 
                           Number(this.formData.jaringan_nilai_pengawasan || 0) + 
                           Number(this.formData.jaringan_nilai_pip || 0);
                },

                get totalNilaiAsetLainnya() {
                    return (Number(this.formData.lainnya_jumlah_barang || 1) * Number(this.formData.lainnya_nilai_satuan || 0)) + 
                           Number(this.formData.lainnya_administrasi_proyek || 0);
                },

                get totalNilaiAtb() {
                    return (Number(this.formData.atb_jumlah || 1) * Number(this.formData.atb_nilai_satuan || 0)) + 
                           Number(this.formData.atb_administrasi_proyek || 0);
                },

                get totalNilaiKdp() {
                    return Number(this.formData.kdp_nilai_perencanaan || 0) + 
                           Number(this.formData.kdp_nilai_fisik || 0) + 
                           Number(this.formData.kdp_nilai_pengawasan || 0) + 
                           Number(this.formData.kdp_nilai_pip || 0);
                },

                get currentProgram() {
                    if (!this.sipdData || this.sipdData.length === 0) return null;
                    return this.sipdData.find(p => p.kode === this.formData.program_kode) || this.sipdData[0];
                },

                get availableKegiatans() {
                    return this.currentProgram ? this.currentProgram.kegiatans : [];
                },

                get currentKegiatan() {
                    if (!this.availableKegiatans || this.availableKegiatans.length === 0) return null;
                    return this.availableKegiatans.find(k => k.kode === this.formData.kegiatan_kode) || this.availableKegiatans[0];
                },

                get availableSubKegiatans() {
                    return this.currentKegiatan ? this.currentKegiatan.subKegiatans : [];
                },

                onProgramChange(kode) {
                    this.formData.program_kode = kode;
                    const prog = this.sipdData.find(p => p.kode === kode);
                    if (prog) {
                        this.formData.program_nama = prog.nama;
                        if (prog.kegiatans && prog.kegiatans.length > 0) {
                            this.onKegiatanChange(prog.kegiatans[0].kode);
                        }
                    }
                },

                onKegiatanChange(kode) {
                    this.formData.kegiatan_kode = kode;
                    const keg = this.availableKegiatans.find(k => k.kode === kode);
                    if (keg) {
                        this.formData.kegiatan_nama = keg.nama;
                        if (keg.subKegiatans && keg.subKegiatans.length > 0) {
                            this.onSubKegiatanChange(keg.subKegiatans[0].kode);
                        }
                    }
                },

                onSubKegiatanChange(kode) {
                    this.formData.sub_kegiatan_kode = kode;
                    const sub = this.availableSubKegiatans.find(s => s.kode === kode);
                    if (sub) {
                        this.formData.sub_kegiatan_nama = sub.nama;
                        this.formData.keterangan_pengadaan = sub.keterangan;
                        this.formData.jenis_pengadaan_id = sub.id || 1;
                    }
                },

                get currentJenisAstap() {
                    if (!window.dbMasterJenisAstap108 || window.dbMasterJenisAstap108.length === 0) return null;
                    return window.dbMasterJenisAstap108.find(j => j.kode === this.formData.jenis_aset_kode) || window.dbMasterJenisAstap108[0];
                },

                get availableSubRincian108() {
                    return (this.currentJenisAstap && this.currentJenisAstap.subRincian) ? this.currentJenisAstap.subRincian : [];
                },

                get currentSubRincianObj() {
                    if (this.availableSubRincian108.length === 0) return null;
                    return this.availableSubRincian108.find(s => s.kode === this.formData.sub_rincian_kode) || this.availableSubRincian108[0];
                },

                get availableSubSubRincian108() {
                    return (this.currentSubRincianObj && this.currentSubRincianObj.subSubRincian) ? this.currentSubRincianObj.subSubRincian : [];
                },

                onRekeningBelanjaChange(kodeRek) {
                    this.formData.kode_rek = kodeRek;
                    const found = this.masterRekeningBelanja.find(r => r.kode_rek === kodeRek);
                    if (found) {
                        this.formData.nama_belanja = found.nama_belanja;
                        if (found.default_jenis_kode) {
                            this.onJenisAstapChange(found.default_jenis_kode);
                        }
                    }
                },

                onJenisAstapChange(kodeJenis) {
                    this.formData.jenis_aset_kode = kodeJenis;
                    const found = (window.dbMasterJenisAstap108 || []).find(j => j.kode === kodeJenis);
                    if (found) {
                        this.formData.jenis_aset_nama = found.nama;
                        if (found.subRincian && found.subRincian.length > 0) {
                            this.onSubRincianChange(found.subRincian[0].kode);
                        } else {
                            this.formData.sub_rincian_kode = '';
                            this.formData.sub_rincian_nama = '';
                        }
                    }
                },

                onSubRincianChange(kodeSub) {
                    this.formData.sub_rincian_kode = kodeSub;
                    const found = this.availableSubRincian108.find(s => s.kode === kodeSub);
                    if (found) {
                        this.formData.sub_rincian_nama = found.nama;
                        if (found.subSubRincian && found.subSubRincian.length > 0) {
                            this.onSubSubRincianChange(found.subSubRincian[0].kode);
                        }
                    }
                },

                onSubSubRincianChange(kodeSubSub) {
                    const found = this.availableSubSubRincian108.find(s => s.kode === kodeSubSub);
                    if (this.isTanah) {
                        this.formData.tanah_kode_barang = kodeSubSub;
                        if (found) this.formData.tanah_nama_barang = found.nama;
                    } else if (this.isMesin) {
                        this.formData.mesin_kode_barang = kodeSubSub;
                        if (found) this.formData.mesin_nama_barang = found.nama;
                    } else if (this.isGedung) {
                        this.formData.gedung_kode_barang = kodeSubSub;
                        if (found) this.formData.gedung_nama_barang = found.nama;
                    } else if (this.isJaringan) {
                        this.formData.jaringan_kode_barang = kodeSubSub;
                        if (found) this.formData.jaringan_nama_barang = found.nama;
                    } else if (this.isAsetLainnya) {
                        this.formData.lainnya_kode_barang = kodeSubSub;
                        if (found) this.formData.lainnya_nama_barang = found.nama;
                    } else if (this.isAtb) {
                        this.formData.atb_kode_barang = kodeSubSub;
                        if (found) this.formData.atb_nama_barang = found.nama;
                    } else if (this.isKdp) {
                        this.formData.kdp_kode_barang = kodeSubSub;
                        if (found) this.formData.kdp_nama_barang = found.nama;
                    }
                },

                autoFillDokumen() {
                    const dateStr = new Date().toISOString().slice(0, 10);
                    const randomNo = Math.floor(100 + Math.random() * 900);
                    this.formData.spk_nomor = '0' + randomNo.toString().slice(0, 2) + '/SPK-KTR/VI/2026';
                    this.formData.spk_tanggal = dateStr;
                    this.formData.surat_pesanan_nomor = '0' + randomNo.toString().slice(0, 2) + '/SP-RSUD/VI/2026';
                    this.formData.surat_pesanan_tanggal = dateStr;
                    this.formData.kwitansi_nomor = 'KW-' + randomNo + '/RSUD/2026';
                    this.formData.kwitansi_tanggal = dateStr;
                    this.formData.faktur_nomor = 'INV-2026-' + randomNo;
                    this.formData.faktur_tanggal = dateStr;
                    this.formData.sp2d_nomor = '0' + randomNo + '/SP2D/BLUD/2026';
                    this.formData.sp2d_tanggal = dateStr;
                    this.formData.bast_dokumen_nomor = '000.2.3.2/' + randomNo + '/430.10.7/2026';
                    this.formData.bast_dokumen_tanggal = dateStr;
                    alert('✨ Dokumen pembelian terisi otomatis dengan format resmi!');
                },

                formatRupiah(val) {
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
                },

                submitForm() {
                    const namaBarang = this.isTanah ? this.formData.tanah_nama_barang : (this.isMesin ? this.formData.mesin_nama_barang : (this.isGedung ? this.formData.gedung_nama_barang : (this.isJaringan ? this.formData.jaringan_nama_barang : (this.isAsetLainnya ? this.formData.lainnya_nama_barang : (this.isAtb ? this.formData.atb_nama_barang : (this.isKdp ? this.formData.kdp_nama_barang : 'Aset Belanja Modal'))))));
                    alert('✅ Data ASTAP (' + namaBarang + ') berhasil disimpan ke database SIMAT-RK!');
                    window.location.href = '{{ route('astap.index') }}';
                }
            };
        }
    </script>

    <div x-data="astapForm()" x-cloak class="space-y-6">

        <!-- Top Navigation Bar (Back + Title) -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl">
            <div class="flex items-center space-x-3 sm:space-x-4 w-full sm:w-auto">
                <a href="{{ route('astap.index') }}" 
                   class="w-9 h-9 sm:w-10 sm:h-10 rounded-xl sm:rounded-2xl bg-slate-950 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all shadow-sm shrink-0">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div class="min-w-0 flex-1">
                    <div class="inline-flex items-center space-x-2 px-2 sm:px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[9px] sm:text-[10px] font-bold mb-1">
                        <span x-text="isEdit ? '✏️ MODE EDIT DATA ASTAP' : '📝 FORM PENAMBAHAN DATA ASTAP'"></span>
                    </div>
                    <h1 class="text-base sm:text-xl md:text-2xl font-extrabold text-white tracking-tight truncate" x-text="isEdit ? 'Ubah Data ASTAP: ' + (isTanah ? formData.tanah_nama_barang : (isMesin ? formData.mesin_nama_barang : (isGedung ? formData.gedung_nama_barang : (isJaringan ? formData.jaringan_nama_barang : (isAsetLainnya ? formData.lainnya_nama_barang : (isAtb ? formData.atb_nama_barang : (isKdp ? formData.kdp_nama_barang : 'Aset Tetap'))))))) : 'Input Penambahan Aset Tetap (ASTAP)'"></h1>
                </div>
            </div>
        </div>

        <!-- Multi-Step Stepper Header (1 s/d 4) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-6 shadow-xl">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-4">
                
                <!-- Step 1 Tab -->
                <button type="button" @click="currentStep = 1" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="currentStep === 1 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : (currentStep > 1 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="currentStep <= 1">1</span>
                            <span x-show="currentStep > 1">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 1 ? 'text-emerald-400' : 'text-slate-500'">Langkah 1</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Jenis Pengadaan</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 1 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 2 Tab (Rekening Belanja & Jenis ASTAP PMDN 108) -->
                <button type="button" @click="currentStep = 2" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="currentStep === 2 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : (currentStep > 2 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="currentStep <= 2">2</span>
                            <span x-show="currentStep > 2">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 2 ? 'text-emerald-400' : 'text-slate-500'">Langkah 2</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Rekening & Jenis 108</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 2 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 3 Tab (Dokumen Pembelian / Rincian Belanja Modal) -->
                <button type="button" @click="currentStep = 3" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="currentStep === 3 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : (currentStep > 3 ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="currentStep <= 3">3</span>
                            <span x-show="currentStep > 3">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 3 ? 'text-emerald-400' : 'text-slate-500'">Langkah 3</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate" x-text="isTanah ? 'Rincian Tanah' : (isMesin ? 'Rincian Mesin' : (isGedung ? 'Rincian Gedung' : (isJaringan ? 'Rincian Jaringan' : (isAsetLainnya ? 'Rincian Lainnya' : (isAtb ? 'Rincian ATB' : (isKdp ? 'Rincian KDP' : 'Rincian Aset'))))))"></span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 3 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 4 Tab (Penyedia & PPK) -->
                <button type="button" @click="currentStep = 4" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-lg sm:rounded-xl font-bold text-[11px] sm:text-xs flex items-center justify-center transition-all shrink-0"
                             :class="currentStep === 4 ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                            <span>4</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="currentStep === 4 ? 'text-emerald-400' : 'text-slate-500'">Langkah 4</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Penyedia, PPK & Ket.</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="currentStep >= 4 ? 'bg-emerald-500' : 'bg-slate-950'"></div>
                </button>

            </div>
        </div>

        <!-- Main Form Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
            
            <!-- ========================================================================= -->
            <!-- LANGKAH 1: FILTERING BERTINGKAT JENIS PENGADAAN (PROVINSI ➔ KOTA ➔ KEC)   -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 1" class="space-y-6">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold mb-2">
                        <span>🗺️ FILTERING BERTINGKAT SIPD</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-purple-500/10 text-purple-400 text-sm">🏛️</span>
                        <span>Langkah 1: Memilih Jenis Pengadaan Berdasarkan Program & Kegiatan (SIPD)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Pilih Program ➔ Kegiatan ➔ Sub Kegiatan (Jenis Pengadaan Detail) secara berjenjang:</p>
                </div>

                <!-- 3 Tingkat Filter Berjenjang (Ibarat Provinsi ➔ Kota ➔ Kecamatan) -->
                <div class="p-6 rounded-3xl bg-slate-950/80 border border-purple-500/30 space-y-5 shadow-2xl">
                    
                    <!-- Tingkat 1: PROGRAM (Ibarat Provinsi) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[10px] font-black flex items-center justify-center">1</span>
                                <span>Pilih Program Pengadaan (SIPD):</span>
                            </label>
                            <span class="text-[10px] font-mono text-purple-400 font-bold" x-text="'Kode: ' + formData.program_kode"></span>
                        </div>
                        <div class="relative">
                            <select :value="formData.program_kode" 
                                    @change="onProgramChange($event.target.value)"
                                    class="w-full bg-slate-900 border border-slate-700 hover:border-purple-500 rounded-2xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-purple-500 transition-all">
                                <template x-for="p in sipdData" :key="p.kode">
                                    <option :value="p.kode" :selected="p.kode === formData.program_kode" x-text="p.kode + ' - ' + p.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Tingkat 2: KEGIATAN (Ibarat Kota / Kabupaten - Terfilter Sesuai Program) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center">2</span>
                                <span>Pilih Kegiatan Pengadaan (SIPD):</span>
                            </label>
                            <span class="text-[10px] font-mono text-cyan-400 font-bold" x-text="'Kode: ' + formData.kegiatan_kode"></span>
                        </div>
                        <div class="relative">
                            <select :value="formData.kegiatan_kode" 
                                    @change="onKegiatanChange($event.target.value)"
                                    class="w-full bg-slate-900 border border-slate-700 hover:border-cyan-500 rounded-2xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-cyan-500 transition-all">
                                <template x-for="k in availableKegiatans" :key="k.kode">
                                    <option :value="k.kode" :selected="k.kode === formData.kegiatan_kode" x-text="k.kode + ' - ' + k.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Tingkat 3: SUB KEGIATAN / JENIS PENGADAAN (Ibarat Kecamatan - Terfilter Sesuai Kegiatan) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-black flex items-center justify-center">3</span>
                                <span>Pilih Sub Kegiatan (Jenis Pengadaan Spesifik):</span>
                            </label>
                            <span class="text-[10px] font-mono text-emerald-400 font-bold" x-text="'Kode: ' + formData.sub_kegiatan_kode"></span>
                        </div>
                        <div class="relative">
                            <select :value="formData.sub_kegiatan_kode" 
                                    @change="onSubKegiatanChange($event.target.value)"
                                    class="w-full bg-slate-900 border border-emerald-500/50 hover:border-emerald-400 rounded-2xl px-4 py-3 text-xs text-emerald-300 font-bold focus:outline-none focus:border-emerald-400 transition-all">
                                <template x-for="s in availableSubKegiatans" :key="s.kode">
                                    <option :value="s.kode" :selected="s.kode === formData.sub_kegiatan_kode" x-text="s.kode + ' - ' + s.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Kotak Info Jalur Hierarki Aktif -->
                    <div class="p-4 rounded-2xl bg-purple-950/20 border border-purple-500/30 text-xs space-y-2">
                        <div class="flex items-center space-x-2 text-purple-300 font-bold text-[11px] uppercase tracking-wider">
                            <span>✅ Jalur Pengadaan Terpilih:</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-[11px]">
                            <span class="px-2.5 py-1 rounded-xl bg-purple-500/20 text-purple-300 border border-purple-500/30 font-semibold" x-text="'Prog: ' + formData.program_nama"></span>
                            <span class="text-slate-500">&rarr;</span>
                            <span class="px-2.5 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-semibold" x-text="'Keg: ' + formData.kegiatan_nama"></span>
                            <span class="text-slate-500">&rarr;</span>
                            <span class="px-2.5 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold" x-text="'Sub Keg: ' + formData.sub_kegiatan_nama"></span>
                        </div>
                        <p class="text-[10px] text-slate-400 italic pt-1" x-text="formData.keterangan_pengadaan"></p>
                    </div>

                </div>
            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 2: FILTERING BERTINGKAT REKENING BELANJA & JENIS ASTAP 108        -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 2" class="space-y-6">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold mb-2">
                        <span>🗺️ FILTERING BERTINGKAT BELANJA MODAL</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-blue-500/10 text-blue-400 text-sm">📊</span>
                        <span>Langkah 2: Memilih Rekening Belanja SIPD & Jenis ASTAP (PMDN 108)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Pilih Rekening Belanja ➔ Jenis Aset PMDN 108 ➔ Sub Rincian Objek secara berjenjang:</p>
                </div>

                <!-- 3 Tingkat Filter Berjenjang Rekening Belanja & PMDN 108 -->
                <div class="p-6 rounded-3xl bg-slate-950/80 border border-blue-500/30 space-y-5 shadow-2xl">
                    
                    <!-- Tingkat 1: REKENING BELANJA PENGADAAN SIPD (Kolom 8 & 9) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/40 text-[10px] font-black flex items-center justify-center">1</span>
                                <span>Pilih Rekening Belanja Untuk Pengadaan SIPD (Kolom 8 & 9):</span>
                            </label>
                            <span class="text-[10px] font-mono text-blue-400 font-bold" x-text="'Kode Rek: ' + formData.kode_rek"></span>
                        </div>
                        <div class="relative">
                            <select :value="formData.kode_rek" 
                                    @change="onRekeningBelanjaChange($event.target.value)"
                                    class="w-full bg-slate-900 border border-slate-700 hover:border-blue-500 rounded-2xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-blue-500 transition-all">
                                <template x-for="r in masterRekeningBelanja" :key="r.kode_rek">
                                    <option :value="r.kode_rek" :selected="r.kode_rek === formData.kode_rek" x-text="r.kode_rek + ' - ' + r.nama_belanja"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Tingkat 2: JENIS ASET PMDN 108 (Kolom 10 & 11) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center">2</span>
                                <span>Pilih Jenis Aset (PMDN 108) (Kolom 10 & 11):</span>
                            </label>
                            <span class="text-[10px] font-mono text-cyan-400 font-bold" x-text="'Kode: ' + formData.jenis_aset_kode"></span>
                        </div>
                        <div class="relative">
                            <select :value="formData.jenis_aset_kode" 
                                    @change="onJenisAstapChange($event.target.value)"
                                    class="w-full bg-slate-900 border border-slate-700 hover:border-cyan-500 rounded-2xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-cyan-500 transition-all">
                                <template x-for="j in masterJenisAstap108" :key="j.kode">
                                    <option :value="j.kode" :selected="j.kode === formData.jenis_aset_kode" x-text="j.kode + ' - ' + j.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Tingkat 3: SUB RINCIAN OBJEK PMDN 108 (Kolom 12 & 13 - Terfilter Sesuai Jenis Aset) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-black flex items-center justify-center">3</span>
                                <span>Pilih Sub Rincian Objek (PMDN 108) (Kolom 12 & 13):</span>
                            </label>
                            <span class="text-[10px] font-mono text-emerald-400 font-bold" x-text="'Kode: ' + formData.sub_rincian_kode"></span>
                        </div>
                        <div class="relative">
                            <select :value="formData.sub_rincian_kode" 
                                    @change="onSubRincianChange($event.target.value)"
                                    class="w-full bg-slate-900 border border-emerald-500/50 hover:border-emerald-400 rounded-2xl px-4 py-3 text-xs text-emerald-300 font-bold focus:outline-none focus:border-emerald-400 transition-all">
                                <template x-for="s in availableSubRincian108" :key="s.kode">
                                    <option :value="s.kode" :selected="s.kode === formData.sub_rincian_kode" x-text="s.kode + ' - ' + s.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Input Nilai Anggaran & Realisasi (Kolom 14 & 15) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-slate-300 font-semibold text-xs mb-1">JUMLAH ANGGARAN (Rp) (Kolom 14)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-3 text-slate-500 text-xs font-bold">Rp</span>
                                <input type="number" x-model.number="formData.jumlah_anggaran" placeholder="544100000"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 pl-10 text-xs text-white font-mono font-bold focus:outline-none focus:border-blue-500">
                            </div>
                        </div>
                        <div>
                            <label class="block text-emerald-400 font-semibold text-xs mb-1">JUMLAH REALISASI (Rp) (Kolom 15)</label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-3 text-emerald-500 text-xs font-bold">Rp</span>
                                <input type="number" x-model.number="formData.jumlah_realisasi" placeholder="516156650"
                                       class="w-full bg-slate-950 border border-emerald-500/50 rounded-xl px-3.5 py-2.5 pl-10 text-xs text-emerald-400 font-mono font-extrabold focus:outline-none focus:border-emerald-400">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ========================================================================= -->
                <!-- LIVE PREVIEW TABEL PERSIS SEPERTI GAMBAR SCREENSHOT USER (LANGKAH 2)      -->
                <!-- ========================================================================= -->
                <div class="space-y-2 pt-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                            <span>📄 Live Preview Tabel Belanja Modal:</span>
                        </span>
                        <span class="text-[10px] text-emerald-400 font-mono">Format Excel Sesuai Standar Laporan</span>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-xl">
                        <table class="w-full min-w-[760px] text-center text-xs border-collapse font-sans">
                            <!-- Header Atas: BELANJA MODAL -->
                            <thead>
                                <tr class="bg-blue-300 text-slate-950 font-black border-b border-slate-600">
                                    <th colspan="8" class="py-2.5 text-sm uppercase tracking-widest border border-slate-600 bg-blue-300">
                                        BELANJA MODAL
                                    </th>
                                </tr>
                                <!-- Header Tingkat 1 -->
                                <tr class="bg-blue-200 text-slate-950 font-bold border-b border-slate-600 text-[11px]">
                                    <th colspan="2" class="px-3 py-2 border border-slate-600">Rekening Belanja Untuk Pengadaan SIPD</th>
                                    <th colspan="2" class="px-3 py-2 border border-slate-600">Jenis Aset (PMDN 108)</th>
                                    <th colspan="2" class="px-3 py-2 border border-slate-600">Sub Rincian Objek (PMDN 108)</th>
                                    <th rowspan="2" class="px-3 py-2 border border-slate-600 align-middle">JUMLAH ANGGARAN (Rp)</th>
                                    <th rowspan="2" class="px-3 py-2 border border-slate-600 align-middle">JUMLAH REALISASI (Rp)</th>
                                </tr>
                                <!-- Header Tingkat 2 (Nama Kolom & Nomor Kolom 8 s/d 15) -->
                                <tr class="bg-blue-200 text-slate-950 font-bold border-b border-slate-600 text-[10px]">
                                    <th class="px-2 py-1.5 border border-slate-600">Kode Rek</th>
                                    <th class="px-3 py-1.5 border border-slate-600">Nama Belanja Pengadaan</th>
                                    <th class="px-2 py-1.5 border border-slate-600">Kode</th>
                                    <th class="px-3 py-1.5 border border-slate-600">Nama Jenis Aset</th>
                                    <th class="px-2 py-1.5 border border-slate-600">Kode</th>
                                    <th class="px-3 py-1.5 border border-slate-600">Nama Uraian Sub Rincian Objek</th>
                                </tr>
                                <!-- Nomor Kolom 8 s/d 15 -->
                                <tr class="bg-blue-100 text-slate-800 font-bold text-[10px] border-b-2 border-slate-700">
                                    <th class="py-1 border border-slate-600">8</th>
                                    <th class="py-1 border border-slate-600">9</th>
                                    <th class="py-1 border border-slate-600">10</th>
                                    <th class="py-1 border border-slate-600">11</th>
                                    <th class="py-1 border border-slate-600">12</th>
                                    <th class="py-1 border border-slate-600">13</th>
                                    <th class="py-1 border border-slate-600">14</th>
                                    <th class="py-1 border border-slate-600">15</th>
                                </tr>
                            </thead>
                            <!-- Baris Data Isi Live Sesuai Input User -->
                            <tbody class="bg-white text-slate-950 font-medium text-[11px]">
                                <tr>
                                    <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.kode_rek"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-left font-semibold" x-text="formData.nama_belanja"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.jenis_aset_kode"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-left font-semibold uppercase" x-text="formData.jenis_aset_nama"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.sub_rincian_kode"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-left font-semibold uppercase" x-text="formData.sub_rincian_nama"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-right font-mono font-bold" x-text="formatRupiah(formData.jumlah_anggaran)"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-right font-mono font-bold text-emerald-800" x-text="formatRupiah(formData.jumlah_realisasi)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 3: RINCIAN BELANJA MODAL (TANAH / MESIN / GEDUNG / JARINGAN / LAIN)-->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 3" class="space-y-6">
                
                <!-- Header Langkah 3 -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                    <div>
                        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2"
                             :class="isTanah ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : (isMesin ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : (isGedung ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : (isJaringan ? 'bg-teal-500/20 text-teal-300 border border-teal-500/30' : (isAsetLainnya ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : (isAtb ? 'bg-violet-500/20 text-violet-300 border border-violet-500/30' : (isKdp ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30'))))))">
                            <span x-text="isTanah ? '🌾 RINCIAN KHUSUS BELANJA MODAL TANAH (KIB A)' : (isMesin ? '⚙️ RINCIAN KHUSUS PERALATAN DAN MESIN (KIB B)' : (isGedung ? '🏢 RINCIAN KHUSUS GEDUNG DAN BANGUNAN (KIB C)' : (isJaringan ? '🚰 RINCIAN KHUSUS JALAN, IRIGASI & JARINGAN (KIB D)' : (isAsetLainnya ? '📚 RINCIAN KHUSUS ASET TETAP LAINNYA (KIB E)' : (isAtb ? '💻 RINCIAN KHUSUS ASET TIDAK BERWUJUD (1.5.3)' : (isKdp ? '🏗️ RINCIAN KHUSUS KONSTRUKSI DALAM PENGERJAAN (KIB F)' : '📑 DOKUMEN PEMBELIAN BARANG'))))))"></span>
                        </div>
                        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                            <span class="p-2 rounded-xl text-sm" :class="isTanah ? 'bg-emerald-500/10 text-emerald-400' : (isMesin ? 'bg-amber-500/10 text-amber-400' : (isGedung ? 'bg-indigo-500/10 text-indigo-400' : (isJaringan ? 'bg-teal-500/10 text-teal-400' : (isAsetLainnya ? 'bg-rose-500/10 text-rose-400' : (isAtb ? 'bg-violet-500/10 text-violet-400' : (isKdp ? 'bg-amber-500/10 text-amber-400' : 'bg-cyan-500/10 text-cyan-400'))))))">💻</span>
                            <span x-text="isTanah ? 'Langkah 3: Rincian Belanja Modal Tanah Sesuai SPK / Surat Pesanan / Kwitansi / Invoice' : (isMesin ? 'Langkah 3: Rincian Peralatan dan Mesin Sesuai SPK / Surat Pesanan / Kwitansi / Invoice' : (isGedung ? 'Langkah 3: Rincian Belanja Gedung dan Bangunan Sesuai SPK / Invoice' : (isJaringan ? 'Langkah 3: Rincian Belanja Jalan, Irigasi dan Jaringan Sesuai SPK / Invoice' : (isAsetLainnya ? 'Langkah 3: Rincian Aset Tetap Lainnya (Buku / Kesenian / Hewan & Tumbuhan) Sesuai SPK / Invoice' : (isAtb ? 'Langkah 3: Rincian Aset Tidak Berwujud (Software / Lisensi / Hak Cipta) Sesuai SPK / Invoice' : (isKdp ? 'Langkah 3: Rincian Konstruksi Dalam Pengerjaan (KDP / Fisik & Termin Kontrak) Sesuai SPK / MC' : 'Langkah 3: Dokumen Pengadaan & Bukti Transaksi')))))))"></span>
                        </h2>
                        <p class="text-xs text-slate-400 mt-1" x-text="isTanah ? 'Pilih Sub-Sub Rincian (Nama/Kode Barang 108), letak/alamat tanah, status tanah, sertifikat, riwayat pembelian, dan nilai barang:' : (isMesin ? 'Pilih Sub-Sub Rincian 108, spesifikasi (merk, type, ukuran, bahan), riwayat pembelian, volume, administrasi proyek dan ruangan:' : (isGedung ? 'Pilih Sub-Sub Rincian 108, luas m2, kondisi, status tanah KIB A, kapitalisasi, riwayat pembelian, volume dan rincian nilai bangunan:' : (isJaringan ? 'Pilih Sub-Sub Rincian 108, konstruksi, panjang/luas, status tanah KIB A, riwayat pembelian, volume dan rincian nilai jaringan:' : (isAsetLainnya ? 'Pilih Sub-Sub Rincian 108, buku perpustakaan, kesenian/kebudayaan, tanaman/hewan, riwayat pembelian, volume dan administrasi proyek:' : (isAtb ? 'Pilih Sub-Sub Rincian 108, judul/nama software, pencipta, spesifikasi, riwayat pembelian, volume, administrasi proyek dan ruangan:' : (isKdp ? 'Pilih Sub-Sub Rincian 108, spesifikasi konstruksi, luas rencana, progres %, status tanah KIB A, periode pengerjaan, dan akumulasi nilai realisasi:' : 'Lengkapi nomor dokumen pembelian atau klik tombol otomatis di kanan:'))))))))"></p>
                    </div>

                    <!-- Tombol Cepat Otomatis -->
                    <button type="button" @click="autoFillDokumen()"
                            class="px-3.5 py-2 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 text-xs font-bold transition-all flex items-center space-x-1.5 active:scale-95">
                        <span>✨ Isi Otomatis Format Nomor Dokumen</span>
                    </button>
                </div>

                <!-- ===================================================================== -->
                <!-- KONDISI A: JIKA MEMILIH ASET TANAH (KIB A) DI LANGKAH 2               -->
                <!-- ===================================================================== -->
                <template x-if="isTanah">
                    <div class="space-y-6">
                        
                        <!-- Letak / Alamat Barang (Dipindahkan ke Langkah 3) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/40 space-y-2 shadow-lg">
                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-2">
                                <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 LETAK / ALAMAT TANAH & ASET:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Fisik Barang</span>
                            </div>
                            <input type="text" x-model="formData.alamat_barang" placeholder="Contoh: Jl. Piere Tendean No. 3, Kel. Badean, Kec. Bondowoso (Area Paviliun RSUD Dr. H. Koesnandi)"
                                   class="w-full bg-slate-900 border border-slate-700 hover:border-amber-500 rounded-xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-amber-500 transition-all">
                        </div>

                        <!-- Grid Form Pengisian Rincian Tanah -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- 1. IDENTITAS BARANG (KODE 108) DENGAN PEMFILTERAN DROPDOWN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/40 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-emerald-500/30 pb-2">
                                    <span class="text-xs font-bold text-emerald-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                        <span>🏛️ 1. IDENTITAS BARANG (KODE 108):</span>
                                    </span>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Terfilter dari Langkah 2</span>
                                </div>

                                <!-- Filter Dropdown Sub-Sub Rincian dari Master Jenis ASTAP -->
                                <div class="space-y-1">
                                    <label class="block text-slate-300 text-[11px] font-bold">Pilih Barang (Sub-Sub Rincian 108):</label>
                                    <select :value="formData.tanah_kode_barang"
                                            @change="onSubSubRincianChange($event.target.value)"
                                            class="w-full bg-slate-900 border border-emerald-500/60 hover:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-400 transition-all">
                                        <template x-for="item in availableSubSubRincian108" :key="item.kode">
                                            <option :value="item.kode" :selected="item.kode === formData.tanah_kode_barang" x-text="item.kode + ' - ' + item.nama"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Tampilan Nama Barang & Kode Barang yang Terpilih -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Nama Barang (Uraian Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.tanah_nama_barang" placeholder="Tanah Bangunan Apotik / Rumah Sakit"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Kode Barang (Kode Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.tanah_kode_barang" placeholder="1.3.1.01.01.02.013"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>

                            <!-- 2. Status Tanah & Sertifikat -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">📜 2. Status Tanah & Sertifikat:</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1 font-semibold">Hak Tanah</label>
                                    <select x-model="formData.tanah_hak" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold">
                                        <option value="Hak Pakai">Hak Pakai</option>
                                        <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                        <option value="Hak Milik">Hak Milik</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Nomor</label>
                                        <input type="text" x-model="formData.tanah_sertifikat_no" placeholder="HP-108/1984"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Tanggal</label>
                                        <input type="date" x-model="formData.tanah_sertifikat_tgl"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white">
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Kondisi, Penggunaan & Volume -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">📐 3. Kondisi, Penggunaan & Volume:</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kondisi (B/KB/RB)</label>
                                        <select x-model="formData.tanah_kondisi" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold">
                                            <option value="B">B (Baik)</option>
                                            <option value="KB">KB (Kurang Baik)</option>
                                            <option value="RB">RB (Rusak Berat)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Jumlah Bidang Tanah</label>
                                        <input type="number" x-model.number="formData.tanah_jumlah_bidang" placeholder="1"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Luas Tanah (m²)</label>
                                        <input type="number" x-model.number="formData.tanah_luas_m2" placeholder="35400"
                                               class="w-full bg-slate-900 border border-cyan-500/40 rounded-xl px-2.5 py-2 text-xs text-cyan-300 font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Penggunaan Lahan</label>
                                        <input type="text" x-model="formData.tanah_penggunaan" placeholder="Fasilitas RSUD"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Riwayat Pembelian (SPK, Surat Pesanan, Kwitansi, Invoice) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4 shadow-lg">
                            <span class="text-xs font-bold text-purple-300 block uppercase tracking-wider">4. Riwayat Dokumen Pembelian:</span>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- SPK -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-cyan-400 block">SPK (Kontrak)</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SPK</label>
                                        <input type="date" x-model="formData.spk_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Surat Pesanan -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-purple-400 block">Surat Pesanan</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Surat Pesanan</label>
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Kwitansi -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-amber-400 block">Kwitansi</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Kwitansi</label>
                                        <input type="date" x-model="formData.kwitansi_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Invoice -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-emerald-400 block">Invoice / Faktur</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Invoice</label>
                                        <input type="date" x-model="formData.faktur_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Nilai Rincian Barang (Perencanaan + Fisik + Pengawasan = Total) & SP2D/BAST -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            
                            <!-- Rincian Nilai Barang (Rp) -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">5. Nilai Barang (Rp):</span>
                                <div class="grid grid-cols-3 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Nilai Perencanaan</label>
                                        <input type="number" x-model.number="formData.tanah_nilai_perencanaan" placeholder="150000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Nilai Fisik (Rp)</label>
                                        <input type="number" x-model.number="formData.tanah_nilai_fisik" placeholder="8200000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Nilai Pengawasan</label>
                                        <input type="number" x-model.number="formData.tanah_nilai_pengawasan" placeholder="150000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono">
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-emerald-950/30 border border-emerald-500/40 flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-emerald-300">Total Nilai Barang (Rp):</span>
                                    <span class="text-sm font-extrabold text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiTanah)"></span>
                                </div>
                            </div>

                            <!-- SP2D & BAST -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">6. Dokumen SP2D & BAST:</span>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                            <input type="text" x-model="formData.sp2d_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                            <input type="date" x-model="formData.sp2d_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                            <input type="text" x-model="formData.bast_dokumen_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                            <input type="date" x-model="formData.bast_dokumen_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ============================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL SESUAI GAMBAR USER (KHUSUS TANAH)    -->
                        <!-- ============================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Rincian Belanja Modal Tanah (Sesuai SPK / SP / Kwitansi / Invoice):</span>
                                </span>
                                <span class="text-[10px] text-emerald-400 font-mono">Format Excel KIB A RSUD (26 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1300px]">
                                    <!-- Header Utama Hijau Pastel -->
                                    <thead>
                                        <tr class="bg-[#d7e4bc] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="25" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#d7e4bc]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Letak / Alamat<br>Barang
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#d7e4bc]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Status Tanah</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Kondisi<br><span class="font-normal text-[9px]">(B,KB,RB)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-32 align-middle bg-[#d7e4bc]">Penggunaan</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">VOLUME</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">Nilai Barang (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Hak Tanah<br><span class="font-normal text-[8.5px]">(Hak Pakai / Hak Pengelolaan)</span></th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Sertifikat</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice (Tanggal dan Nomor)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Jumlah Bidang Tanah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Luas Tanah (m²)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Perencanaan (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Fisik (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Pengawasan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- Body Data Live Sesuai Input User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <tr>
                                            <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="formData.tanah_nama_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.tanah_kode_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="formData.tanah_hak"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.tanah_sertifikat_tgl"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.tanah_sertifikat_no"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.spk_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.surat_pesanan_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.kwitansi_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.faktur_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="formData.tanah_kondisi"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.tanah_penggunaan"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="formData.tanah_jumlah_bidang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right" x-text="formatRupiah(formData.tanah_luas_m2)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.tanah_nilai_perencanaan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.tanah_nilai_fisik)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.tanah_nilai_pengawasan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-emerald-800" x-text="formatRupiah(totalNilaiTanah)"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.sp2d_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.bast_dokumen_tanggal"></td>
                                            <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="formData.alamat_barang"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <!-- ===================================================================== -->
                <!-- KONDISI B: JIKA MEMILIH PERALATAN DAN MESIN (KIB B) DI LANGKAH 2      -->
                <!-- ===================================================================== -->
                <template x-if="isMesin">
                    <div class="space-y-6">
                        
                        <!-- 1. Ruang / Pemegang Aset (Sesuai Kolom Terakhir Gambar KIB B) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/40 space-y-2 shadow-lg">
                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-2">
                                <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 RUANG / PEMEGANG (PENANGGUNG JAWAB & LOKASI):</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Kolom Kanan KIB B</span>
                            </div>
                            <input type="text" x-model="formData.ruang_pemegang" placeholder="Contoh: Instalasi Radiologi & Imaging Sentral / dr. Hendra, Sp.Rad"
                                   class="w-full bg-slate-900 border border-slate-700 hover:border-amber-500 rounded-xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-amber-500 transition-all">
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi Peralatan dan Mesin -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- 1. IDENTITAS BARANG (KODE 108) DENGAN PEMFILTERAN DROPDOWN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/40 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-emerald-500/30 pb-2">
                                    <span class="text-xs font-bold text-emerald-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                        <span>🏛️ 1. IDENTITAS BARANG (KODE 108):</span>
                                    </span>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Terfilter dari Langkah 2</span>
                                </div>

                                <!-- Filter Dropdown Sub-Sub Rincian dari Master Jenis ASTAP -->
                                <div class="space-y-1">
                                    <label class="block text-slate-300 text-[11px] font-bold">Pilih Barang (Sub-Sub Rincian 108):</label>
                                    <select :value="formData.mesin_kode_barang"
                                            @change="onSubSubRincianChange($event.target.value)"
                                            class="w-full bg-slate-900 border border-emerald-500/60 hover:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-400 transition-all">
                                        <template x-for="item in availableSubSubRincian108" :key="item.kode">
                                            <option :value="item.kode" :selected="item.kode === formData.mesin_kode_barang" x-text="item.kode + ' - ' + item.nama"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Tampilan Nama Barang & Kode Barang yang Terpilih -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Nama Barang (Uraian Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.mesin_nama_barang" placeholder="CT-Scan 128 Slice High Resolution"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Kode Barang (Kode Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.mesin_kode_barang" placeholder="1.3.2.02.01.01.005"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>

                            <!-- 2. Spesifikasi Fisik (Merk, Type, Ukuran) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">⚙️ 2. Merk, Type & Ukuran:</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Merk Barang</label>
                                    <input type="text" x-model="formData.mesin_merk" placeholder="Siemens / Mindray / Daikin"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Type / Model</label>
                                        <input type="text" x-model="formData.mesin_type" placeholder="SOMATOM go.Now"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Ukuran / Kapasitas</label>
                                        <input type="text" x-model="formData.mesin_ukuran" placeholder="128 Slice / 2 PK"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                </div>
                            </div>

                            <!-- 3. No Pabrik, Bahan & Kondisi -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">🏷️ 3. No Pabrik, Bahan & Kondisi:</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">No Pabrik / Serial Number (SN)</label>
                                    <input type="text" x-model="formData.mesin_no_pabrik" placeholder="SN-RAD-2026-88192"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Bahan Pembuatan</label>
                                        <input type="text" x-model="formData.mesin_bahan" placeholder="Logam & Elektronik"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kondisi (B/KB/RB)</label>
                                        <select x-model="formData.mesin_kondisi" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold">
                                            <option value="B">B (Baik)</option>
                                            <option value="KB">KB (Kurang Baik)</option>
                                            <option value="RB">RB (Rusak Berat)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- 4. Riwayat Dokumen Pembelian (SPK, Surat Pesanan, Kwitansi, Invoice) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4 shadow-lg">
                            <span class="text-xs font-bold text-purple-300 block uppercase tracking-wider">4. Riwayat Dokumen Pembelian:</span>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- SPK -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-cyan-400 block">SPK (Kontrak)</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SPK</label>
                                        <input type="date" x-model="formData.spk_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Surat Pesanan -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-purple-400 block">Surat Pesanan</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Surat Pesanan</label>
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Kwitansi -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-amber-400 block">Kwitansi</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Kwitansi</label>
                                        <input type="date" x-model="formData.kwitansi_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Invoice -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-emerald-400 block">Invoice / Faktur</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Invoice</label>
                                        <input type="date" x-model="formData.faktur_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Volume, Administrasi Proyek, Nilai Total & SP2D/BAST -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            
                            <!-- Volume & Nilai Barang -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">5. Volume & Nilai Satuan Barang (Rp):</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Jumlah Barang</label>
                                        <input type="number" x-model.number="formData.mesin_jumlah_barang" placeholder="1"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Nama Satuan Barang</label>
                                        <select x-model="formData.mesin_satuan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold">
                                            <option value="Unit">Unit</option>
                                            <option value="Set">Set</option>
                                            <option value="Buah">Buah</option>
                                            <option value="Pcs">Pcs</option>
                                            <option value="Paket">Paket</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Nilai Satuan Barang (Rp)</label>
                                        <input type="number" x-model.number="formData.mesin_nilai_satuan" placeholder="8475000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Administrasi Proyek (Rp)</label>
                                        <input type="number" x-model.number="formData.mesin_administrasi_proyek" placeholder="25000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono">
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-emerald-950/30 border border-emerald-500/40 flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-emerald-300">Total Nilai Barang (Rp):</span>
                                    <span class="text-sm font-extrabold text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
                                </div>
                            </div>

                            <!-- SP2D & BAST -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">6. Dokumen SP2D & BAST:</span>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                            <input type="text" x-model="formData.sp2d_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                            <input type="date" x-model="formData.sp2d_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                            <input type="text" x-model="formData.bast_dokumen_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                            <input type="date" x-model="formData.bast_dokumen_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ============================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL SESUAI GAMBAR USER (KHUSUS MESIN)    -->
                        <!-- ============================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Rincian Belanja Modal Peralatan dan Mesin (Sesuai SPK/Invoice):</span>
                                </span>
                                <span class="text-[10px] text-amber-400 font-mono">Format Excel KIB B RSUD (27 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1450px]">
                                    <!-- Header Utama Hijau Pastel -->
                                    <thead>
                                        <tr class="bg-[#d7e4bc] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="26" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#d7e4bc]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                RUANG /<br>PEMEGANG
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#d7e4bc]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">Merk</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">Type</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-20 align-middle bg-[#fde9d9]">Ukuran</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">No Pabrik</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">BAHAN</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Kondisi<br><span class="font-normal text-[9px]">(B,KB,RB)</span></th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Kondisi<br><span class="font-normal text-[9px]">(B,KB,RB)</span></th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">VOLUME</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">ADMINISTRASI PROYEK (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice (Tanggal dan Nomor)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Jumlah Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Satuan Barang (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- Body Data Live Sesuai Input User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <tr>
                                            <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="formData.mesin_nama_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.mesin_kode_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="formData.mesin_merk"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono" x-text="formData.mesin_type"></td>
                                            <td class="px-2 py-2 border border-slate-400" x-text="formData.mesin_ukuran"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono" x-text="formData.mesin_no_pabrik"></td>
                                            <td class="px-2 py-2 border border-slate-400" x-text="formData.mesin_bahan"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="formData.mesin_kondisi"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.spk_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.surat_pesanan_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.kwitansi_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.faktur_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="formData.mesin_kondisi"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="formData.mesin_jumlah_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="formData.mesin_satuan"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.mesin_nilai_satuan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.mesin_administrasi_proyek)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-emerald-800" x-text="formatRupiah(totalNilaiMesin)"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.sp2d_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.bast_dokumen_tanggal"></td>
                                            <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="formData.ruang_pemegang"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <!-- ===================================================================== -->
                <!-- KONDISI C: JIKA MEMILIH GEDUNG DAN BANGUNAN (KIB C) DI LANGKAH 2      -->
                <!-- ===================================================================== -->
                <template x-if="isGedung">
                    <div class="space-y-6">
                        
                        <!-- 1. Letak / Alamat Bangunan (Sesuai Kolom Terakhir Gambar KIB C) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/40 space-y-2 shadow-lg">
                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-2">
                                <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 LETAK / ALAMAT GEDUNG & BANGUNAN:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Kolom Kanan KIB C</span>
                            </div>
                            <input type="text" x-model="formData.alamat_barang" placeholder="Contoh: Jl. Piere Tendean No. 3, Kel. Badean, Kec. Bondowoso (Kompleks RSUD Dr. H. Koesnandi)"
                                   class="w-full bg-slate-900 border border-slate-700 hover:border-amber-500 rounded-xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-amber-500 transition-all">
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi Gedung dan Bangunan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- 1. IDENTITAS BARANG (KODE 108) DENGAN PEMFILTERAN DROPDOWN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/40 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-emerald-500/30 pb-2">
                                    <span class="text-xs font-bold text-emerald-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                        <span>🏛️ 1. IDENTITAS BARANG (KODE 108):</span>
                                    </span>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Terfilter dari Langkah 2</span>
                                </div>

                                <!-- Filter Dropdown Sub-Sub Rincian dari Master Jenis ASTAP -->
                                <div class="space-y-1">
                                    <label class="block text-slate-300 text-[11px] font-bold">Pilih Bangunan (Sub-Sub Rincian 108):</label>
                                    <select :value="formData.gedung_kode_barang"
                                            @change="onSubSubRincianChange($event.target.value)"
                                            class="w-full bg-slate-900 border border-emerald-500/60 hover:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-400 transition-all">
                                        <template x-for="item in availableSubSubRincian108" :key="item.kode">
                                            <option :value="item.kode" :selected="item.kode === formData.gedung_kode_barang" x-text="item.kode + ' - ' + item.nama"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Tampilan Nama Barang & Kode Barang yang Terpilih -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Nama Barang (Uraian Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.gedung_nama_barang" placeholder="Gedung Rawat Inap VIP Terpadu Lt 2"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Kode Barang (Kode Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.gedung_kode_barang" placeholder="1.3.3.01.01.08.001"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>

                            <!-- 2. Kondisi & Spesifikasi Bangunan (Luas, Kondisi, Bertingkat, Beton) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">🏗️ 2. Kondisi & Spesifikasi:</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Luas (M2/Lt)</label>
                                        <input type="number" x-model.number="formData.gedung_luas_m2" placeholder="850"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kondisi (B/KB/RB)</label>
                                        <select x-model="formData.gedung_kondisi" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold">
                                            <option value="B">B (Baik)</option>
                                            <option value="KB">KB (Kurang Baik)</option>
                                            <option value="RB">RB (Rusak Berat)</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Bertingkat / Tidak</label>
                                        <select x-model="formData.gedung_bertingkat" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold">
                                            <option value="Bertingkat">Bertingkat</option>
                                            <option value="Tidak">Tidak Bertingkat</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Beton / Tidak</label>
                                        <select x-model="formData.gedung_beton" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold">
                                            <option value="Beton">Beton</option>
                                            <option value="Tidak">Bukan Beton</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Jenis Bangunan & Status Tanah (KIB A) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">📜 3. Status Tanah & Kapitalisasi:</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Status Tanah</label>
                                        <input type="text" x-model="formData.gedung_status_tanah" placeholder="Hak Pakai Pemkab"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kode Aset Tanah</label>
                                        <input type="text" x-model="formData.gedung_kode_aset_tanah" placeholder="1.3.1.01.01.02.013"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono">
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-1.5">
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Bangunan Baru</label>
                                        <select x-model="formData.gedung_is_baru" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white">
                                            <option value="Baru">Baru</option>
                                            <option value="Tidak">Renovasi</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Tahun Induk</label>
                                        <input type="text" x-model="formData.gedung_kapitalisasi_tahun_induk" placeholder="2020"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Induk 2026</label>
                                        <input type="number" x-model.number="formData.gedung_kapitalisasi_nilai_induk" placeholder="3500000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-1.5 py-2 text-[10px] text-amber-300 font-mono">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- 4. Riwayat Dokumen Pembelian (SPK, Surat Pesanan, Kwitansi, Invoice) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4 shadow-lg">
                            <span class="text-xs font-bold text-purple-300 block uppercase tracking-wider">4. Riwayat Dokumen Pembelian:</span>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- SPK -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-cyan-400 block">SPK (Kontrak)</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SPK</label>
                                        <input type="date" x-model="formData.spk_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Surat Pesanan -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-purple-400 block">Surat Pesanan</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Surat Pesanan</label>
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Kwitansi -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-amber-400 block">Kwitansi</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Kwitansi</label>
                                        <input type="date" x-model="formData.kwitansi_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Invoice -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-emerald-400 block">Invoice / Faktur</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Invoice</label>
                                        <input type="date" x-model="formData.faktur_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Volume, Nilai Rincian Gedung & SP2D/BAST -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            
                            <!-- Volume & Rincian Nilai Bangunan -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">5. Volume & Nilai Satuan Bangunan (Rp):</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Jumlah Bangunan</label>
                                        <input type="number" x-model.number="formData.gedung_jumlah_bangunan" placeholder="1"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Nama Satuan Barang</label>
                                        <select x-model="formData.gedung_satuan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold">
                                            <option value="Gedung">Gedung</option>
                                            <option value="Unit">Unit</option>
                                            <option value="Paket">Paket</option>
                                            <option value="M²">M²</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Perencanaan</label>
                                        <input type="number" x-model.number="formData.gedung_nilai_perencanaan" placeholder="75000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Fisik (Rp)</label>
                                        <input type="number" x-model.number="formData.gedung_nilai_fisik" placeholder="1850000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Pengawasan</label>
                                        <input type="number" x-model.number="formData.gedung_nilai_pengawasan" placeholder="50000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai PIP (Rp)</label>
                                        <input type="number" x-model.number="formData.gedung_nilai_pip" placeholder="25000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-emerald-950/30 border border-emerald-500/40 flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-emerald-300">Total Nilai Barang (Rp):</span>
                                    <span class="text-sm font-extrabold text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiGedung)"></span>
                                </div>
                            </div>

                            <!-- SP2D & BAST -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">6. Dokumen SP2D & BAST:</span>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                            <input type="text" x-model="formData.sp2d_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                            <input type="date" x-model="formData.sp2d_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                            <input type="text" x-model="formData.bast_dokumen_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                            <input type="date" x-model="formData.bast_dokumen_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ============================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL SESUAI GAMBAR USER (KHUSUS GEDUNG)   -->
                        <!-- ============================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Rincian Belanja Modal Gedung dan Bangunan (Sesuai SPK/Invoice):</span>
                                </span>
                                <span class="text-[10px] text-indigo-400 font-mono">Format Excel KIB C RSUD (31 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1600px]">
                                    <!-- Header Utama Hijau Pastel -->
                                    <thead>
                                        <tr class="bg-[#d7e4bc] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="30" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#d7e4bc]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Letak / Alamat
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#d7e4bc]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-20 align-middle bg-[#fde9d9]">Luas (M2/Lt)</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Kondisi / Spesifikasi</th>
                                            <th colspan="5" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Jenis Bangunan</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">VOLUME</th>
                                            <th colspan="4" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">Nilai Satuan Barang (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">(B,KB,RB)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Bertingkat/<br>Tidak</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Beton/<br>Tidak</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Status Tanah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Kode Aset<br>Tanah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Baru</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kapitalisasi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Jumlah Bangunan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Perencanaan (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Fisik (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Pengawasan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai PIP</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tahun Induk</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nilai Induk s/d 2026</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- Body Data Live Sesuai Input User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <tr>
                                            <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="formData.gedung_nama_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.gedung_kode_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono" x-text="formData.gedung_luas_m2"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="formData.gedung_kondisi"></td>
                                            <td class="px-2 py-2 border border-slate-400" x-text="formData.gedung_bertingkat"></td>
                                            <td class="px-2 py-2 border border-slate-400" x-text="formData.gedung_beton"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.gedung_status_tanah"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.gedung_kode_aset_tanah"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.gedung_is_baru"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.gedung_kapitalisasi_tahun_induk"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.gedung_kapitalisasi_nilai_induk)"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.spk_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.surat_pesanan_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.kwitansi_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.faktur_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="formData.gedung_jumlah_bangunan"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="formData.gedung_satuan"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.gedung_nilai_perencanaan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.gedung_nilai_fisik)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.gedung_nilai_pengawasan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.gedung_nilai_pip)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-emerald-800" x-text="formatRupiah(totalNilaiGedung)"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.sp2d_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.bast_dokumen_tanggal"></td>
                                            <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="formData.alamat_barang"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <!-- ===================================================================== -->
                <!-- KONDISI D: JIKA MEMILIH JALAN, IRIGASI & JARINGAN (KIB D) DI LANGKAH 2 -->
                <!-- ===================================================================== -->
                <template x-if="isJaringan">
                    <div class="space-y-6">
                        
                        <!-- 1. Letak / Lokasi Jaringan (Sesuai Kolom Terakhir Gambar KIB D) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/40 space-y-2 shadow-lg">
                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-2">
                                <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 LETAK / LOKASI JALAN, IRIGASI & JARINGAN:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Kolom Kanan KIB D</span>
                            </div>
                            <input type="text" x-model="formData.alamat_barang" placeholder="Contoh: Area Instalasi Farmasi, Sentral Gas Medis & Gedung Rawat Inap RSUD Dr. H. Koesnandi"
                                   class="w-full bg-slate-900 border border-slate-700 hover:border-amber-500 rounded-xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-amber-500 transition-all">
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi Jalan & Jaringan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- 1. IDENTITAS BARANG (KODE 108) DENGAN PEMFILTERAN DROPDOWN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-teal-500/40 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-teal-500/30 pb-2">
                                    <span class="text-xs font-bold text-teal-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                        <span>🏛️ 1. IDENTITAS BARANG (KODE 108):</span>
                                    </span>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 font-bold">Terfilter dari Langkah 2</span>
                                </div>

                                <!-- Filter Dropdown Sub-Sub Rincian dari Master Jenis ASTAP -->
                                <div class="space-y-1">
                                    <label class="block text-slate-300 text-[11px] font-bold">Pilih Jaringan / Jalan (Sub-Sub Rincian 108):</label>
                                    <select :value="formData.jaringan_kode_barang"
                                            @change="onSubSubRincianChange($event.target.value)"
                                            class="w-full bg-slate-900 border border-teal-500/60 hover:border-teal-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-teal-400 transition-all">
                                        <template x-for="item in availableSubSubRincian108" :key="item.kode">
                                            <option :value="item.kode" :selected="item.kode === formData.jaringan_kode_barang" x-text="item.kode + ' - ' + item.nama"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Tampilan Nama Barang & Kode Barang yang Terpilih -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Nama Barang (Uraian Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.jaringan_nama_barang" placeholder="Jaringan Pipa Oksigen Sentral Medis & Vakum"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none focus:border-teal-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Kode Barang (Kode Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.jaringan_kode_barang" placeholder="1.3.4.03.01.04.004"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-teal-400 font-mono font-bold focus:outline-none focus:border-teal-500">
                                </div>
                            </div>

                            <!-- 2. Konstruksi & Dimensi Jaringan (Panjang, Lebar, Luas, Kondisi) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">📐 2. Konstruksi & Dimensi:</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Bahan Konstruksi</label>
                                    <input type="text" x-model="formData.jaringan_konstruksi" placeholder="Pipa Tembaga Medis ASTM B819 & Zone Valve"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Panjang (M)</label>
                                        <input type="number" x-model.number="formData.jaringan_panjang_m" placeholder="450"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Lebar (M)</label>
                                        <input type="number" x-model.number="formData.jaringan_lebar_m" placeholder="0"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Luas (M²)</label>
                                        <input type="number" x-model.number="formData.jaringan_luas_m2" placeholder="0"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kondisi</label>
                                        <select x-model="formData.jaringan_kondisi" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-bold">
                                            <option value="B">B (Baik)</option>
                                            <option value="KB">KB (Kurang Baik)</option>
                                            <option value="RB">RB (Rusak Berat)</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Status Tanah KIB A & Kapitalisasi Jaringan -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">📜 3. Status Tanah & Kapitalisasi:</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Status Tanah</label>
                                        <input type="text" x-model="formData.jaringan_status_tanah" placeholder="Hak Pakai RSUD"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kode Aset Tanah</label>
                                        <input type="text" x-model="formData.jaringan_kode_aset_tanah" placeholder="1.3.1.01.01.02.013"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono">
                                    </div>
                                </div>
                                <div class="grid grid-cols-3 gap-1.5">
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Jaringan Baru</label>
                                        <select x-model="formData.jaringan_is_baru" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white">
                                            <option value="Baru">Baru</option>
                                            <option value="Tidak">Peningkatan</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Tahun Induk</label>
                                        <input type="text" x-model="formData.jaringan_kapitalisasi_tahun_induk" placeholder="2021"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Induk 2026</label>
                                        <input type="number" x-model.number="formData.jaringan_kapitalisasi_nilai_induk" placeholder="850000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-1.5 py-2 text-[10px] text-amber-300 font-mono">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- 4. Riwayat Dokumen Pembelian (SPK, Surat Pesanan, Kwitansi, Invoice) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4 shadow-lg">
                            <span class="text-xs font-bold text-purple-300 block uppercase tracking-wider">4. Riwayat Dokumen Pembelian:</span>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- SPK -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-cyan-400 block">SPK (Kontrak)</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SPK</label>
                                        <input type="date" x-model="formData.spk_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Surat Pesanan -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-purple-400 block">Surat Pesanan</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Surat Pesanan</label>
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Kwitansi -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-amber-400 block">Kwitansi</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Kwitansi</label>
                                        <input type="date" x-model="formData.kwitansi_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Invoice -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-emerald-400 block">Invoice / Faktur</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Invoice</label>
                                        <input type="date" x-model="formData.faktur_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Volume, Nilai Rincian Jaringan & SP2D/BAST -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            
                            <!-- Volume & Rincian Nilai Jaringan -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-teal-400 block uppercase tracking-wider">5. Volume & Nilai Jaringan (Rp):</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Jumlah Jaringan / Ruas</label>
                                        <input type="number" x-model.number="formData.jaringan_jumlah" placeholder="1"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Nama Satuan Barang</label>
                                        <select x-model="formData.jaringan_satuan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold">
                                            <option value="Paket">Paket</option>
                                            <option value="Ruas">Ruas</option>
                                            <option value="Meter">Meter</option>
                                            <option value="Titik">Titik</option>
                                            <option value="Unit">Unit</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Perencanaan</label>
                                        <input type="number" x-model.number="formData.jaringan_nilai_perencanaan" placeholder="35000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Fisik (Rp)</label>
                                        <input type="number" x-model.number="formData.jaringan_nilai_fisik" placeholder="620000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Pengawasan</label>
                                        <input type="number" x-model.number="formData.jaringan_nilai_pengawasan" placeholder="25000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai PIP (Rp)</label>
                                        <input type="number" x-model.number="formData.jaringan_nilai_pip" placeholder="15000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-teal-950/30 border border-teal-500/40 flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-teal-300">Total Nilai Barang (Rp):</span>
                                    <span class="text-sm font-extrabold text-teal-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiJaringan)"></span>
                                </div>
                            </div>

                            <!-- SP2D & BAST -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">6. Dokumen SP2D & BAST:</span>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                            <input type="text" x-model="formData.sp2d_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                            <input type="date" x-model="formData.sp2d_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                            <input type="text" x-model="formData.bast_dokumen_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                            <input type="date" x-model="formData.bast_dokumen_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ============================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL KHUSUS JALAN, IRIGASI & JARINGAN     -->
                        <!-- ============================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Rincian Belanja Modal Jalan, Irigasi dan Jaringan (Format Excel):</span>
                                </span>
                                <span class="text-[10px] text-teal-400 font-mono">Format Excel KIB D RSUD (32 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1650px]">
                                    <!-- Header Utama Hijau Pastel -->
                                    <thead>
                                        <tr class="bg-[#d7e4bc] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="31" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#d7e4bc]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Letak / Lokasi<br>Jaringan
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#d7e4bc]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Konstruksi</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Panjang<br>(M)</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Lebar<br>(M)</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Luas<br>(M²)</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Kondisi<br><span class="font-normal text-[9px]">(B,KB,RB)</span></th>
                                            <th colspan="5" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Jenis Jaringan / Status Tanah</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">VOLUME</th>
                                            <th colspan="4" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">Nilai Satuan Barang (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Status Tanah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Kode Aset<br>Tanah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Baru</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kapitalisasi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Jumlah Jaringan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Perencanaan (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Fisik (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Pengawasan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai PIP</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tahun Induk</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nilai Induk s/d 2026</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- Body Data Live Sesuai Input User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <tr>
                                            <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="formData.jaringan_nama_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.jaringan_kode_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.jaringan_konstruksi"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono" x-text="formData.jaringan_panjang_m"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono" x-text="formData.jaringan_lebar_m"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono" x-text="formData.jaringan_luas_m2"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="formData.jaringan_kondisi"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.jaringan_status_tanah"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.jaringan_kode_aset_tanah"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.jaringan_is_baru"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.jaringan_kapitalisasi_tahun_induk"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.jaringan_kapitalisasi_nilai_induk)"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.spk_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.surat_pesanan_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.kwitansi_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.faktur_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="formData.jaringan_jumlah"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="formData.jaringan_satuan"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.jaringan_nilai_perencanaan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.jaringan_nilai_fisik)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.jaringan_nilai_pengawasan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.jaringan_nilai_pip)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-teal-800" x-text="formatRupiah(totalNilaiJaringan)"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.sp2d_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.bast_dokumen_tanggal"></td>
                                            <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="formData.alamat_barang"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <!-- ===================================================================== -->
                <!-- KONDISI E: JIKA MEMILIH ASET TETAP LAINNYA (KIB E / 1.3.5) DI LANGKAH 2-->
                <!-- ===================================================================== -->
                <template x-if="isAsetLainnya">
                    <div class="space-y-6">
                        
                        <!-- Ruang / Pemegang (KIB E Sesuai Kolom Terakhir Excel) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-rose-500/40 space-y-2 shadow-lg">
                            <div class="flex items-center justify-between border-b border-rose-500/30 pb-2">
                                <label class="block text-rose-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 RUANG / PEMEGANG (PENANGGUNG JAWAB & LOKASI):</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold">Kolom Kanan Tabel (KIB E)</span>
                            </div>
                            <input type="text" x-model="formData.ruang_pemegang_lainnya" placeholder="Contoh: Instalasi Perpustakaan Medis & Diklat RSUD Dr. H. Koesnandi"
                                   class="w-full bg-slate-900 border border-slate-700 hover:border-rose-500 rounded-xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-rose-500 transition-all">
                        </div>

                        <!-- Grid Form Pengisian Rincian Aset Tetap Lainnya -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- 1. IDENTITAS BARANG (KODE 108) DENGAN PEMFILTERAN DROPDOWN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-rose-500/40 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-rose-500/30 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">1. Identitas Barang (Kode 108)</span>
                                    </div>
                                    <span class="text-[9px] font-mono text-rose-400 font-bold">PMDN 108</span>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-semibold text-xs">Pilih Sub-Sub Rincian Barang:</label>
                                    <select :value="formData.lainnya_kode_barang" 
                                            @change="onSubSubRincianChange($event.target.value)"
                                            class="w-full bg-slate-900 border border-rose-500/50 hover:border-rose-400 rounded-xl px-3 py-2.5 text-xs text-rose-300 font-bold focus:outline-none focus:border-rose-400 transition-all">
                                        <template x-for="item in availableSubSubRincian108" :key="item.kode">
                                            <option :value="item.kode" :selected="item.kode === formData.lainnya_kode_barang" x-text="item.kode + ' - ' + item.nama"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Nama Barang (Uraian Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.lainnya_nama_barang" placeholder="Buku Jurnal Kedokteran..."
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-rose-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Kode Barang (Kode Sub-Sub Rincian 108)</label>
                                    <input type="text" x-model="formData.lainnya_kode_barang" placeholder="1.3.5.01.01.01.002"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-rose-300 font-mono font-bold focus:outline-none focus:border-rose-500">
                                </div>
                            </div>

                            <!-- 2. BUKU PERPUSTAKAAN (JUDUL, PENCIPTA, SPESIFIKASI) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">2. Buku Perpustakaan</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Kolom Excel</span>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Judul Buku</label>
                                    <input type="text" x-model="formData.lainnya_buku_judul" placeholder="Pedoman Standar Pelayanan Klinis..."
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Pencipta / Penulis / Penerbit</label>
                                    <input type="text" x-model="formData.lainnya_buku_pencipta" placeholder="Komite Medik & Tim Farmasi RSUD"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Spesifikasi Buku</label>
                                    <input type="text" x-model="formData.lainnya_buku_spesifikasi" placeholder="Edisi Revisi 2026 / Hardcover Lux / 850 Hal"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 focus:outline-none focus:border-amber-500">
                                </div>
                            </div>

                            <!-- 3. BARANG BERCORAK KESENIAN / KEBUDAYAAN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">3. Barang Kesenian / Budaya</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 font-bold">Kolom Excel</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Asal Daerah</label>
                                        <input type="text" x-model="formData.lainnya_kesenian_asal" placeholder="Jatim / Bondowoso"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Pencipta</label>
                                        <input type="text" x-model="formData.lainnya_kesenian_pencipta" placeholder="Sanggar Budaya"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Spesifikasi Kesenian</label>
                                    <input type="text" x-model="formData.lainnya_kesenian_spesifikasi" placeholder="Lukisan Sejarah Rumah Sakit..."
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-purple-300 focus:outline-none focus:border-purple-500">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Bahan</label>
                                        <input type="text" x-model="formData.lainnya_kesenian_bahan" placeholder="Kanvas & Kayu"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Ukuran (m/cm)</label>
                                        <input type="text" x-model="formData.lainnya_kesenian_ukuran" placeholder="200 x 120 cm"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                                    </div>
                                </div>
                            </div>

                            <!-- 4. HEWAN TERNAK / TUMBUHAN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">4. Hewan Ternak / Tumbuhan</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Kolom Excel</span>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Jenis Hewan / Tanaman</label>
                                    <input type="text" x-model="formData.lainnya_hewan_jenis" placeholder="Tanaman Peneduh & Taman Medis"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-emerald-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Spesifikasi Hewan / Tanaman</label>
                                    <input type="text" x-model="formData.lainnya_hewan_spesifikasi" placeholder="Pohon Tabebuya & Palem Raja Tinggi 3M"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-300 focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>

                            <!-- 5. RIWAYAT PEMBELIAN (SPK, SP, KWITANSI, INVOICE) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">5. Riwayat Pembelian</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 font-bold">SPK, SP, KW, INV</span>
                                </div>
                                <!-- SPK -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">SPK - Nomor</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-[11px] text-cyan-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">SPK - Tanggal</label>
                                        <input type="date" x-model="formData.spk_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-[11px] text-white">
                                    </div>
                                </div>
                                <!-- Surat Pesanan -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Surat Pesanan - Nomor</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-[11px] text-purple-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Surat Pesanan - Tanggal</label>
                                        <input type="date" x-model="formData.surat_pesanan_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-[11px] text-white">
                                    </div>
                                </div>
                                <!-- Kwitansi -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kwitansi - Nomor</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-[11px] text-amber-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kwitansi - Tanggal</label>
                                        <input type="date" x-model="formData.kwitansi_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-[11px] text-white">
                                    </div>
                                </div>
                                <!-- Invoice -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Invoice - Nomor</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-[11px] text-emerald-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Invoice - Tanggal</label>
                                        <input type="date" x-model="formData.faktur_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-[11px] text-white">
                                    </div>
                                </div>
                            </div>

                            <!-- 6. VOLUME, ADMINISTRASI PROYEK & TOTAL NILAI -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-rose-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">6. Volume & Nilai Barang</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold">Kalkulasi Otomatis</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Jumlah Barang</label>
                                        <input type="number" x-model.number="formData.lainnya_jumlah_barang" placeholder="15"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Nama Satuan</label>
                                        <select x-model="formData.lainnya_satuan"
                                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                            <option value="Eksemplar">Eksemplar</option>
                                            <option value="Buah">Buah</option>
                                            <option value="Batang">Batang</option>
                                            <option value="Ekor">Ekor</option>
                                            <option value="Paket">Paket</option>
                                            <option value="Unit">Unit</option>
                                            <option value="Set">Set</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Nilai Satuan Barang (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                        <input type="number" x-model.number="formData.lainnya_nilai_satuan" placeholder="450000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-rose-300 font-mono font-bold">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Administrasi Proyek (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                        <input type="number" x-model.number="formData.lainnya_administrasi_proyek" placeholder="250000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-amber-300 font-mono font-bold">
                                    </div>
                                </div>
                                <div class="p-3 rounded-xl bg-rose-950/30 border border-rose-500/30 text-xs">
                                    <div class="text-[10px] text-slate-400">Total Nilai Barang (Rp):</div>
                                    <div class="text-sm font-extrabold text-rose-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAsetLainnya)"></div>
                                </div>
                            </div>

                            <!-- 7. DOKUMEN SP2D & BAST -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">7. Dokumen SP2D & BAST</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Pembayaran</span>
                                </div>
                                <!-- SP2D -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">SP2D - Nomor</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="0129/SP2D/BLUD/2026"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-[11px] text-emerald-300 font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">SP2D - Tanggal</label>
                                        <input type="date" x-model="formData.sp2d_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-[11px] text-white">
                                    </div>
                                </div>
                                <!-- BAST -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">BAST - Nomor</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="000.2.3.2/224/..."
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-[11px] text-purple-300 font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">BAST - Tanggal</label>
                                        <input type="date" x-model="formData.bast_dokumen_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-[11px] text-white">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ========================================================================= -->
                        <!-- LIVE PREVIEW TABEL PERSIS SEPERTI GAMBAR SCREENSHOT USER (KIB E)          -->
                        <!-- ========================================================================= -->
                        <div class="space-y-2 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Aset Tetap Lainnya Sesuai Gambar:</span>
                                </span>
                                <span class="text-[10px] text-rose-400 font-mono">Format Excel Resmi KIB E (30 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1650px]">
                                    <!-- Header Utama Hijau Pastel -->
                                    <thead>
                                        <tr class="bg-[#d7e4bc] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="29" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#d7e4bc]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                RUANG /<br>PEMEGANG
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#d7e4bc]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BUKU PERPUSTAKAAN</th>
                                            <th colspan="5" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Barang Bercorak Kesenian / Kebudayaan</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Hewan Ternak / Tumbuhan</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">VOLUME</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">ADMINISTRASI PROYEK (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Judul</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Pencipta</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Spesifikasi</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Asal Daerah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Pencipta</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Spesifikasi</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Bahan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Ukuran<br>(m/cm)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Jenis</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Spesifikasi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Jumlah Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Satuan Barang (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- Body Data Live Sesuai Input User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <tr>
                                            <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="formData.lainnya_nama_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.lainnya_kode_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.lainnya_buku_judul"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.lainnya_buku_pencipta"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.lainnya_buku_spesifikasi"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.lainnya_kesenian_asal"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.lainnya_kesenian_pencipta"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.lainnya_kesenian_spesifikasi"></td>
                                            <td class="px-2 py-2 border border-slate-400" x-text="formData.lainnya_kesenian_bahan"></td>
                                            <td class="px-2 py-2 border border-slate-400" x-text="formData.lainnya_kesenian_ukuran"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.lainnya_hewan_jenis"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.lainnya_hewan_spesifikasi"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.spk_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.surat_pesanan_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.kwitansi_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.faktur_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="formData.lainnya_jumlah_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="formData.lainnya_satuan"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.lainnya_nilai_satuan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.lainnya_administrasi_proyek)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-rose-800" x-text="formatRupiah(totalNilaiAsetLainnya)"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.sp2d_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.bast_dokumen_tanggal"></td>
                                            <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="formData.ruang_pemegang_lainnya"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <!-- ===================================================================== -->
                <!-- KONDISI F: JIKA MEMILIH ASET TIDAK BERWUJUD (ATB / 1.5.3) DI LANGKAH 2 -->
                <!-- ===================================================================== -->
                <template x-if="isAtb">
                    <div class="space-y-6">
                        
                        <!-- Ruang / Pemegang (ATB Sesuai Kolom Terakhir Excel) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-violet-500/40 space-y-2 shadow-lg">
                            <div class="flex items-center justify-between border-b border-violet-500/30 pb-2">
                                <label class="block text-violet-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 RUANG / PEMEGANG (PENANGGUNG JAWAB & LOKASI):</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-violet-500/20 text-violet-300 border border-violet-500/30 font-bold">Kolom Kanan Tabel (ATB)</span>
                            </div>
                            <input type="text" x-model="formData.ruang_pemegang_atb" placeholder="Contoh: Instalasi SIMRS & Rekam Medis RSUD Dr. H. Koesnandi"
                                   class="w-full bg-slate-900 border border-slate-700 hover:border-violet-500 rounded-xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-violet-500 transition-all">
                        </div>

                        <!-- Grid Form Pengisian Rincian Aset Tidak Berwujud -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- 1. IDENTITAS BARANG (KODE 108) DENGAN PEMFILTERAN DROPDOWN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-violet-500/40 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-violet-500/30 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-violet-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">1. Identitas Barang (Kode 108)</span>
                                    </div>
                                    <span class="text-[9px] font-mono text-violet-400 font-bold">PMDN 108</span>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-semibold text-xs">Pilih Sub-Sub Rincian Barang:</label>
                                    <select :value="formData.atb_kode_barang" 
                                            @change="onSubSubRincianChange($event.target.value)"
                                            class="w-full bg-slate-900 border border-violet-500/50 hover:border-violet-400 rounded-xl px-3 py-2.5 text-xs text-violet-300 font-bold focus:outline-none focus:border-violet-400 transition-all">
                                        <template x-for="item in availableSubSubRincian108" :key="item.kode">
                                            <option :value="item.kode" :selected="item.kode === formData.atb_kode_barang" x-text="item.kode + ' - ' + item.nama"></option>
                                        </template>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Nama Barang (Uraian Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.atb_nama_barang" placeholder="Software SIMAT-RK..."
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-violet-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Kode Barang (Kode Sub-Sub Rincian 108)</label>
                                    <input type="text" x-model="formData.atb_kode_barang" placeholder="1.5.3.01.01.01.001"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-violet-300 font-mono font-bold focus:outline-none focus:border-violet-500">
                                </div>
                            </div>

                            <!-- 2. JUDUL / NAMA, PENCIPTA & SPESIFIKASI ATB -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">2. Judul, Pencipta & Spesifikasi</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold">Kolom Excel</span>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Judul / Nama Software & Lisensi</label>
                                    <input type="text" x-model="formData.atb_judul_nama" placeholder="Aplikasi SIMAT-RK (Sistem Informasi Manajemen Aset)..."
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Pencipta / Vendor / Pengembang</label>
                                    <input type="text" x-model="formData.atb_pencipta" placeholder="Tim IT SIMRS RSUD & Pengembang Sistem"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Spesifikasi Software / Hak Cipta</label>
                                    <textarea rows="2" x-model="formData.atb_spesifikasi" placeholder="Web-Based, Multi-Role Access, Integrasi SatuSehat & RME..."
                                              class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-cyan-300 focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                                </div>
                            </div>

                            <!-- 3. RIWAYAT PEMBELIAN (SPK, SP, KWITANSI, INVOICE) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">3. Riwayat Pembelian</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 font-bold">SPK, SP, KW, INV</span>
                                </div>
                                <!-- SPK -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">SPK - Nomor</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-[11px] text-cyan-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">SPK - Tanggal</label>
                                        <input type="date" x-model="formData.spk_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-[11px] text-white">
                                    </div>
                                </div>
                                <!-- Surat Pesanan -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Surat Pesanan - Nomor</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-[11px] text-purple-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Surat Pesanan - Tanggal</label>
                                        <input type="date" x-model="formData.surat_pesanan_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-[11px] text-white">
                                    </div>
                                </div>
                                <!-- Kwitansi -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kwitansi - Nomor</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-[11px] text-amber-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kwitansi - Tanggal</label>
                                        <input type="date" x-model="formData.kwitansi_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-[11px] text-white">
                                    </div>
                                </div>
                                <!-- Invoice -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Invoice - Nomor</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-[11px] text-emerald-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Invoice - Tanggal</label>
                                        <input type="date" x-model="formData.faktur_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-[11px] text-white">
                                    </div>
                                </div>
                            </div>

                            <!-- 4. VOLUME, ADMINISTRASI PROYEK & TOTAL NILAI -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-violet-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">4. Volume & Nilai ATB</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-violet-500/20 text-violet-300 border border-violet-500/30 font-bold">Kalkulasi Otomatis</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Jumlah</label>
                                        <input type="number" x-model.number="formData.atb_jumlah" placeholder="1"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Nama Satuan</label>
                                        <select x-model="formData.atb_satuan"
                                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                            <option value="Paket">Paket</option>
                                            <option value="Lisensi">Lisensi</option>
                                            <option value="Modul">Modul</option>
                                            <option value="Sistem">Sistem</option>
                                            <option value="Unit">Unit</option>
                                            <option value="Aplikasi">Aplikasi</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Nilai Satuan Barang (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                        <input type="number" x-model.number="formData.atb_nilai_satuan" placeholder="145000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-violet-300 font-mono font-bold">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Administrasi Proyek (Rp)</label>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                        <input type="number" x-model.number="formData.atb_administrasi_proyek" placeholder="5000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-amber-300 font-mono font-bold">
                                    </div>
                                </div>
                                <div class="p-3 rounded-xl bg-violet-950/30 border border-violet-500/30 text-xs">
                                    <div class="text-[10px] text-slate-400">Total Nilai Barang (Rp):</div>
                                    <div class="text-sm font-extrabold text-violet-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAtb)"></div>
                                </div>
                            </div>

                            <!-- 5. DOKUMEN SP2D & BAST -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">5. Dokumen SP2D & BAST</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Pembayaran</span>
                                </div>
                                <!-- SP2D -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">SP2D - Nomor</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="0129/SP2D/BLUD/2026"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-[11px] text-emerald-300 font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">SP2D - Tanggal</label>
                                        <input type="date" x-model="formData.sp2d_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-[11px] text-white">
                                    </div>
                                </div>
                                <!-- BAST -->
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">BAST - Nomor</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="000.2.3.2/224/..."
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-[11px] text-purple-300 font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">BAST - Tanggal</label>
                                        <input type="date" x-model="formData.bast_dokumen_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-[11px] text-white">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ========================================================================= -->
                        <!-- LIVE PREVIEW TABEL PERSIS SEPERTI GAMBAR SCREENSHOT USER (ATB / 1.5.3)    -->
                        <!-- ========================================================================= -->
                        <div class="space-y-2 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Aset Tidak Berwujud Sesuai Gambar:</span>
                                </span>
                                <span class="text-[10px] text-violet-400 font-mono">Format Excel Resmi ATB (23 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1450px]">
                                    <!-- Header Utama Hijau Pastel -->
                                    <thead>
                                        <tr class="bg-[#d7e4bc] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="22" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#d7e4bc]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Ruang /<br>Pemegang
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#d7e4bc]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">Judul / Nama</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-32 align-middle bg-[#fde9d9]">Pencipta</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">Spesifikasi</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">VOLUME</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">Nilai Satuan Barang (Rp)</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">ADMINISTRASI PROYEK (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Jumlah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- Body Data Live Sesuai Input User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <tr>
                                            <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="formData.atb_nama_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.atb_kode_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.atb_judul_nama"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.atb_pencipta"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.atb_spesifikasi"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.spk_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.surat_pesanan_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.kwitansi_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.faktur_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="formData.atb_jumlah"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="formData.atb_satuan"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.atb_nilai_satuan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.atb_administrasi_proyek)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-violet-800" x-text="formatRupiah(totalNilaiAtb)"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.sp2d_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.bast_dokumen_tanggal"></td>
                                            <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="formData.ruang_pemegang_atb"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <!-- ===================================================================== -->
                <!-- KONDISI F: JIKA MEMILIH KONSTRUKSI DALAM PENGERJAAN (KIB F / 1.3.6)   -->
                <!-- ===================================================================== -->
                <template x-if="isKdp">
                    <div class="space-y-6">
                        
                        <!-- Letak / Alamat Lokasi Proyek Konstruksi -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/40 space-y-2 shadow-lg">
                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-2">
                                <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 LETAK / LOKASI PROYEK KONSTRUKSI (KDP):</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Fisik KDP</span>
                            </div>
                            <input type="text" x-model="formData.alamat_barang" placeholder="Contoh: Kompleks Paviliun Melati & Gedung Rawat Inap Baru RSUD Dr. H. Koesnandi"
                                   class="w-full bg-slate-900 border border-slate-700 hover:border-amber-500 rounded-xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-amber-500 transition-all">
                        </div>

                        <!-- Grid Form Pengisian Rincian KDP -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            
                            <!-- 1. IDENTITAS BARANG (KODE 108) DENGAN PEMFILTERAN DROPDOWN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/40 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-amber-500/30 pb-2">
                                    <span class="text-xs font-bold text-amber-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                        <span>🏗️ 1. IDENTITAS PROYEK KDP (KODE 108):</span>
                                    </span>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Terfilter dari Langkah 2</span>
                                </div>

                                <!-- Filter Dropdown Sub-Sub Rincian dari Master Jenis ASTAP -->
                                <div class="space-y-1">
                                    <label class="block text-slate-300 text-[11px] font-bold">Pilih Proyek KDP (Sub-Sub Rincian 108):</label>
                                    <select :value="formData.kdp_kode_barang"
                                            @change="onSubSubRincianChange($event.target.value)"
                                            class="w-full bg-slate-900 border border-amber-500/60 hover:border-amber-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-amber-400 transition-all">
                                        <template x-for="item in availableSubSubRincian108" :key="item.kode">
                                            <option :value="item.kode" :selected="item.kode === formData.kdp_kode_barang" x-text="item.kode + ' - ' + item.nama"></option>
                                        </template>
                                    </select>
                                </div>

                                <!-- Tampilan Nama Barang & Kode Barang yang Terpilih -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Nama Proyek KDP (Uraian Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.kdp_nama_barang" placeholder="Pembangunan Gedung Rawat Inap Baru Lt 3 (KDP)"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none focus:border-amber-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Kode Barang (Kode Sub-Sub Rincian)</label>
                                    <input type="text" x-model="formData.kdp_kode_barang" placeholder="1.3.6.01.01.01.001"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-400 font-mono font-bold focus:outline-none focus:border-amber-500">
                                </div>
                            </div>

                            <!-- 2. Spesifikasi Konstruksi & Progres Fisik -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">📊 2. Konstruksi & Progres Fisik:</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Luas Rencana (M²)</label>
                                        <input type="number" x-model.number="formData.kdp_luas_m2" placeholder="1200"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-amber-400 text-[10px] mb-1 font-bold">Progres Fisik (%)</label>
                                        <div class="relative">
                                            <input type="number" min="0" max="100" x-model.number="formData.kdp_progres_persen" placeholder="65"
                                                   class="w-full bg-slate-900 border border-amber-500/60 rounded-xl px-2.5 py-2 text-xs text-amber-300 font-mono font-black focus:outline-none focus:border-amber-400">
                                            <span class="absolute right-2.5 top-2 text-amber-400 text-xs font-bold">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Bangunan</label>
                                        <select x-model="formData.kdp_bangunan" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold">
                                            <option value="Bertingkat">Bertingkat</option>
                                            <option value="Tidak">Tidak Bertingkat</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Konstruksi Beton</label>
                                        <select x-model="formData.kdp_beton" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold">
                                            <option value="Beton">Beton</option>
                                            <option value="Tidak">Bukan Beton</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- Visual Progres Bar -->
                                <div class="pt-1">
                                    <div class="flex items-center justify-between text-[10px] font-mono text-slate-400 mb-1">
                                        <span>Realisasi Kemajuan Fisik:</span>
                                        <span class="text-amber-400 font-bold" x-text="(formData.kdp_progres_persen || 0) + '%'"></span>
                                    </div>
                                    <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden border border-slate-800">
                                        <div class="bg-gradient-to-r from-amber-500 to-emerald-400 h-2 rounded-full transition-all" :style="'width: ' + (formData.kdp_progres_persen || 0) + '%'"></div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. Status Tanah, Sertifikat & Waktu Pengerjaan -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">📜 3. Status Tanah & Waktu:</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Status Tanah</label>
                                        <input type="text" x-model="formData.kdp_status_tanah" placeholder="Tanah Hak Pakai RSUD"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kode Aset Tanah (KIB A)</label>
                                        <input type="text" x-model="formData.kdp_kode_aset_tanah" placeholder="1.3.1.01.01.02.013"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Tgl Mulai Pengerjaan</label>
                                        <input type="date" x-model="formData.kdp_tgl_mulai"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Target Selesai</label>
                                        <input type="date" x-model="formData.kdp_tgl_target_selesai"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- 4. Riwayat Dokumen Kontrak & Pembelian (SPK, SP/BAP, Kwitansi, Invoice) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4 shadow-lg">
                            <span class="text-xs font-bold text-purple-300 block uppercase tracking-wider">4. Riwayat Dokumen Kontrak & Pembelian Termin:</span>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- SPK -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-cyan-400 block">SPK (Kontrak Konstruksi)</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SPK</label>
                                        <input type="date" x-model="formData.spk_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Surat Pesanan / BAP -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-purple-400 block">Surat Pesanan / BAP</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Surat Pesanan</label>
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Kwitansi Termin -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-amber-400 block">Kwitansi Pembayaran Termin</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Kwitansi</label>
                                        <input type="date" x-model="formData.kwitansi_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Invoice Kontraktor -->
                                <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-2">
                                    <span class="text-[11px] font-bold text-emerald-400 block">Invoice Kontraktor</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal Invoice</label>
                                        <input type="date" x-model="formData.faktur_tanggal" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 5. Akumulasi Nilai Realisasi Biaya KDP (Rp) & SP2D/BAST -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            
                            <!-- Akumulasi Nilai Biaya KDP -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">5. Akumulasi Nilai Realisasi Biaya KDP (Rp):</span>
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Perencanaan</label>
                                        <input type="number" x-model.number="formData.kdp_nilai_perencanaan" placeholder="125000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Fisik Termin (Rp)</label>
                                        <input type="number" x-model.number="formData.kdp_nilai_fisik" placeholder="2450000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai Pengawasan</label>
                                        <input type="number" x-model.number="formData.kdp_nilai_pengawasan" placeholder="85000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-1">Nilai PIP (Rp)</label>
                                        <input type="number" x-model.number="formData.kdp_nilai_pip" placeholder="40000000"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono">
                                    </div>
                                </div>
                                <div class="p-2.5 rounded-xl bg-amber-950/30 border border-amber-500/40 flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-amber-300">Total Akumulasi Biaya KDP (Rp):</span>
                                    <span class="text-sm font-extrabold text-amber-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiKdp)"></span>
                                </div>
                            </div>

                            <!-- SP2D & BAST Kemajuan Fisik / MC -->
                            <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">6. Dokumen SP2D & BAST Kemajuan Fisik:</span>
                                <div class="grid grid-cols-2 gap-3">
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">SP2D (Pencairan Termin)</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                            <input type="text" x-model="formData.sp2d_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                            <input type="date" x-model="formData.sp2d_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        <span class="text-[10px] font-bold text-slate-300 block">BAST Kemajuan Fisik / MC</span>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Nomor BAST MC</label>
                                            <input type="text" x-model="formData.bast_dokumen_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px]">Tanggal BAST MC</label>
                                            <input type="date" x-model="formData.bast_dokumen_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- ============================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL RESMI KHUSUS KIB F (KDP)              -->
                        <!-- ============================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Rincian Konstruksi Dalam Pengerjaan (Format Excel):</span>
                                </span>
                                <span class="text-[10px] text-amber-400 font-mono">Format Excel KIB F RSUD (28 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1600px]">
                                    <!-- Header Utama Hijau Pastel -->
                                    <thead>
                                        <tr class="bg-[#d7e4bc] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="27" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#d7e4bc]">
                                                RINCIAN BELANJA MODAL KONSTRUKSI DALAM PENGERJAAN (KIB F) SESUAI KONTRAK/SPK/INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Letak / Lokasi<br>Proyek KDP
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#d7e4bc]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#d7e4bc]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-20 align-middle bg-[#fde9d9]">Luas Rencana<br>(M²)</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Progres<br>(%)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Konstruksi</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Status Tanah KIB A</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Waktu Pengerjaan</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Kontrak & Pembelian</th>
                                            <th colspan="4" class="px-2 py-1 border border-slate-500 bg-[#d7e4bc]">Nilai Realisasi Biaya KDP (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Akumulasi Biaya KDP (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST Kemajuan Fisik / MC</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Bertingkat/<br>Tidak</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Beton/<br>Tidak</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Status Tanah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Kode Aset Tanah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">No. Sertifikat</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Tgl Mulai</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Target Selesai</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK / Kontrak</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan / BAP</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi Termin</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice Kontraktor</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Perencanaan (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Fisik Termin (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai Pengawasan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#d7e4bc]">Nilai PIP</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- Body Data Live Sesuai Input User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <tr>
                                            <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="formData.kdp_nama_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.kdp_kode_barang"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.kdp_luas_m2"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-bold font-mono text-amber-700" x-text="(formData.kdp_progres_persen || 0) + '%'"></td>
                                            <td class="px-2 py-2 border border-slate-400" x-text="formData.kdp_bangunan"></td>
                                            <td class="px-2 py-2 border border-slate-400" x-text="formData.kdp_beton"></td>
                                            <td class="px-2 py-2 border border-slate-400 text-left" x-text="formData.kdp_status_tanah"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.kdp_kode_aset_tanah"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kdp_sertifikat_no"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.kdp_tgl_mulai"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.kdp_tgl_target_selesai"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.spk_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.surat_pesanan_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.kwitansi_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.faktur_tanggal"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.kdp_nilai_perencanaan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.kdp_nilai_fisik)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.kdp_nilai_pengawasan)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(formData.kdp_nilai_pip)"></td>
                                            <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-amber-800" x-text="formatRupiah(totalNilaiKdp)"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.sp2d_tanggal"></td>
                                            <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                            <td class="px-1.5 py-2 border border-slate-400" x-text="formData.bast_dokumen_tanggal"></td>
                                            <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="formData.alamat_barang"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <!-- ===================================================================== -->
                <!-- KONDISI G: JIKA MEMILIH KATEGORI LAINNYA (RENOVASI)                   -->
                <!-- ===================================================================== -->
                <template x-if="!isTanah && !isMesin && !isGedung && !isJaringan && !isAsetLainnya && !isAtb && !isKdp">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            
                            <!-- 1. SPK / Kontrak Kerja -->
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                                    <span class="text-xs font-bold text-white uppercase tracking-wider">1. Surat Perintah Kerja (SPK / Kontrak)</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Nomor SPK / Kontrak</label>
                                    <input type="text" x-model="formData.spk_nomor" placeholder="Contoh: 028/SPK-KTR/V/2026"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-cyan-300 font-mono font-bold focus:outline-none focus:border-cyan-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Tanggal SPK</label>
                                    <input type="date" x-model="formData.spk_tanggal"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                                </div>
                            </div>

                            <!-- 2. Surat Pesanan (SP / BAP) -->
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                                    <span class="text-xs font-bold text-white uppercase tracking-wider">2. Surat Pesanan (SP / BAP)</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Nomor Surat Pesanan</label>
                                    <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="Contoh: 028/SP-RSUD/V/2026"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-purple-300 font-mono font-bold focus:outline-none focus:border-purple-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Tanggal Surat Pesanan</label>
                                    <input type="date" x-model="formData.surat_pesanan_tanggal"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                                </div>
                            </div>

                            <!-- 3. Kwitansi Pembayaran -->
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                    <span class="text-xs font-bold text-white uppercase tracking-wider">3. Bukti Kwitansi Pembayaran</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Nomor Kwitansi</label>
                                    <input type="text" x-model="formData.kwitansi_nomor" placeholder="Contoh: KW-028/KTR/2026"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-amber-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Tanggal Kwitansi</label>
                                    <input type="date" x-model="formData.kwitansi_tanggal"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                                </div>
                            </div>

                            <!-- 4. Faktur Pajak / Invoice / Rekanan -->
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                    <span class="text-xs font-bold text-white uppercase tracking-wider">4. Faktur Invoice & Rekanan</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Nomor Faktur</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-300 font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Tanggal Faktur</label>
                                        <input type="date" x-model="formData.faktur_tanggal"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Nama Perusahaan Rekanan / Penyedia</label>
                                    <input type="text" x-model="formData.penyedia_nama" placeholder="PT / CV Penyedia Barang"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white font-semibold">
                                </div>
                            </div>

                        </div>
                    </div>
                </template>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 4: PIHAK PENYEDIA, PPK & KETERANGAN (SESUAI GAMBAR USER)          -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 4" class="space-y-6">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold mb-2">
                        <span>🏢 DETAIL PENYEDIA, PPK & PENGESAHAN</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-amber-500/10 text-amber-400 text-sm">🏢</span>
                        <span>Langkah 4: Pihak Penyedia, Pejabat Pembuat Komitmen & Catatan Pengadaan</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Lengkapi identitas perusahaan rekanan, rekening bank rekanan, PPK dan catatan pengadaan:</p>
                </div>

                <!-- Formulir Input Sesuai Tabel Excel Gambar User -->
                <div class="space-y-5">

                    <!-- 1. Pihak Penyedia (Nama, Pemilik, Rekening Nama & Nomor, Alamat) -->
                    <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/30 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="text-xs font-bold text-white uppercase tracking-wider flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                <span>1. PIHAK PENYEDIA (Rekanan / Vendor)</span>
                            </span>
                            <span class="text-[10px] text-amber-400 font-mono font-bold">Data Rekanan & Rekening Bank</span>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-300 text-xs mb-1 font-semibold">Nama Penyedia (Perusahaan / Badan Usaha)</label>
                                <input type="text" x-model="formData.penyedia_nama" placeholder="Contoh: PT. Medika Sarana Utama"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none focus:border-amber-500">
                            </div>
                            <div>
                                <label class="block text-slate-300 text-xs mb-1 font-semibold">Pemilik Penyedia (Direktur / Penanggung Jawab)</label>
                                <input type="text" x-model="formData.penyedia_pemilik" placeholder="Contoh: Ir. H. Budi Santoso, M.T."
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                            </div>
                        </div>

                        <!-- Rekening Bank Penyedia (Nama Rek & Nomor Rek) -->
                        <div class="p-4 rounded-xl bg-slate-900/90 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Rekening: Nama Rekening (Atas Nama)</label>
                                <input type="text" x-model="formData.penyedia_rekening_nama" placeholder="Contoh: PT. Medika Sarana Utama"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[11px] mb-1">Rekening: Nomor Rekening & Nama Bank</label>
                                <input type="text" x-model="formData.penyedia_rekening_nomor" placeholder="Contoh: 143-00-9876543-2 (Bank Jatim Cab. Bondowoso)"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-slate-300 text-xs mb-1 font-semibold">Alamat Penyedia</label>
                            <input type="text" x-model="formData.penyedia_alamat" placeholder="Contoh: Jl. Raya Darmo No. 45, Wonokromo, Kota Surabaya, Jawa Timur"
                                   class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                        </div>
                    </div>

                    <!-- 2. Pejabat Pembuat Komitmen (PPK) -->
                    <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                                <span>2. PEJABAT PEMBUAT KOMITMEN (PPK)</span>
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-slate-300 text-xs mb-1 font-semibold">Nama Pejabat Pembuat Komitmen (PPK)</label>
                                <input type="text" x-model="formData.ppk_nama" placeholder="Contoh: dr. Slamet Widodo, M.Kes"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none focus:border-cyan-500">
                            </div>
                            <div>
                                <label class="block text-slate-300 text-xs mb-1 font-semibold">NIP PPK</label>
                                <input type="text" x-model="formData.ppk_nip" placeholder="Contoh: 19760229 200801 1 010"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-cyan-300 font-mono font-bold focus:outline-none focus:border-cyan-500">
                            </div>
                        </div>
                    </div>

                    <!-- 3. Keterangan (KET.) -->
                    <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                        <label class="block text-slate-300 font-bold text-xs uppercase tracking-wider">
                            <span>📝 3. Keterangan Tambahan (KET.)</span>
                        </label>
                        <textarea x-model="formData.keterangan_tambahan" rows="2" placeholder="Catatan atau keterangan penting terkait pengadaan..."
                                  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-emerald-500"></textarea>
                    </div>

                </div>

                <!-- ========================================================================= -->
                <!-- LIVE PREVIEW TABEL EXCEL PERSIS SEPERTI GAMBAR SCREENSHOT USER (LANGKAH 4)-->
                <!-- ========================================================================= -->
                <div class="space-y-2 pt-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                            <span>📄 Live Preview Tabel Rekanan Penyedia & PPK (Format Excel Laporan):</span>
                        </span>
                        <span class="text-[10px] text-amber-400 font-mono">Format Excel Sesuai Kolom SPK/Invoice</span>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                        <table class="w-full text-center text-xs border-collapse font-sans min-w-[750px]">
                            <!-- Header Excel Peach/Krem Sesuai Gambar -->
                            <thead>
                                <!-- Header Baris 1 -->
                                <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-600 text-[11px]">
                                    <th colspan="5" class="py-2.5 border border-slate-600 bg-[#fde9d9] uppercase tracking-wider">
                                        PIHAK PENYEDIA
                                    </th>
                                    <th colspan="2" class="py-2.5 border border-slate-600 bg-[#fde9d9] uppercase tracking-wider">
                                        Pejabat Pembuat Komitmen
                                    </th>
                                    <th rowspan="3" class="px-3 py-3 border border-slate-600 align-middle w-48 bg-[#fde9d9]">
                                        KET.
                                    </th>
                                </tr>
                                <!-- Header Baris 2 -->
                                <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-600 text-[10px]">
                                    <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">Nama Penyedia</th>
                                    <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">Pemilik Penyedia</th>
                                    <th colspan="2" class="py-1 border border-slate-600">Rekening</th>
                                    <th rowspan="2" class="px-4 py-1.5 border border-slate-600 align-middle">Alamat Penyedia</th>
                                    <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">Nama</th>
                                    <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">NIP</th>
                                </tr>
                                <!-- Header Baris 3 (Nama Rek & Nomor Rek) -->
                                <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b-2 border-slate-700 text-[10px]">
                                    <th class="px-2.5 py-1 border border-slate-600">Nama Rek</th>
                                    <th class="px-2.5 py-1 border border-slate-600">Nomor Rek</th>
                                </tr>
                            </thead>
                            <!-- Baris Data Live Sesuai Input User -->
                            <tbody class="bg-white text-slate-950 font-medium text-[11px]">
                                <tr>
                                    <td class="px-3 py-3 border border-slate-400 font-semibold" x-text="formData.penyedia_nama"></td>
                                    <td class="px-3 py-3 border border-slate-400" x-text="formData.penyedia_pemilik"></td>
                                    <td class="px-2.5 py-3 border border-slate-400" x-text="formData.penyedia_rekening_nama"></td>
                                    <td class="px-2.5 py-3 border border-slate-400 font-mono font-bold text-amber-900" x-text="formData.penyedia_rekening_nomor"></td>
                                    <td class="px-4 py-3 border border-slate-400 text-left" x-text="formData.penyedia_alamat"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-bold" x-text="formData.ppk_nama"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.ppk_nip"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-left text-[10px]" x-text="formData.keterangan_tambahan"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Bottom Navigation Between Steps -->
            <div class="pt-6 sm:pt-8 mt-6 sm:mt-8 border-t border-slate-800 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                <div class="w-full sm:w-auto">
                    <button type="button" x-show="currentStep > 1" @click="currentStep--"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all flex items-center justify-center space-x-2">
                        <span>&larr; Langkah Sebelumnya</span>
                    </button>
                </div>

                <div class="flex items-center justify-end space-x-2.5 sm:space-x-3 w-full sm:w-auto">
                    <a href="{{ route('astap.index') }}" class="flex-1 sm:flex-initial text-center px-4 sm:px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                        Batal
                    </a>

                    <!-- Next Step Button -->
                    <button type="button" x-show="currentStep < totalSteps" @click="currentStep++"
                            class="flex-1 sm:flex-initial px-5 sm:px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center space-x-2">
                        <span>Lanjut Langkah <span x-text="currentStep + 1"></span> &rarr;</span>
                    </button>

                    <!-- Submit Button -->
                    <button type="button" x-show="currentStep === totalSteps" @click="submitForm()"
                            class="flex-1 sm:flex-initial px-5 sm:px-6 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/30 transition-all flex items-center justify-center space-x-2 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Data ASTAP Lengkap'"></span>
                    </button>
                </div>
            </div>

        </div>

    </div>
</x-layout>
