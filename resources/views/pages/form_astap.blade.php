<x-layout :title="request()->routeIs('astap.edit') ? 'Ubah Data ASTAP - SIMAT-RK' : 'Tambah Data ASTAP Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('astap.edit') ? 'Ubah Data ASTAP' : 'Tambah Data ASTAP Baru')
    @section('breadcrumb', request()->routeIs('astap.edit') ? 'Master Utama / Data ASTAP / Ubah Data' : 'Master Utama / Data ASTAP / Tambah Baru')

    <script>
        window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);
        window.dbJenisPengadaans = @json(!empty($dbJenisPengadaans) ? $dbJenisPengadaans : []);
        window.dbRekeningBelanjas = @json(!empty($dbRekeningBelanjas) ? $dbRekeningBelanjas : []);
        window.dbUnits = @json(!empty($dbUnits) ? $dbUnits : []);
        window.editingAstap = @json(!empty($astap) ? $astap : null);

        function astapForm() {
            return {
                isEdit: {{ request()->routeIs('astap.edit') ? 'true' : 'false' }},
                currentStep: 1,
                totalSteps: 4,

                // Master Data Unit & Paviliun (Diisi dari Database RSUD)
                masterUnits: window.dbUnits || [],

                // State Search Filter Ketik Langkah 1 & Langkah 2
                searchProgram: '',
                isProgramOpen: false,
                searchKegiatan: '',
                isKegiatanOpen: false,
                searchSubKegiatan: '',
                isSubKegiatanOpen: false,
                searchRekening: '',
                isRekeningOpen: false,
                searchJenis108: '',
                isJenis108Open: false,
                searchSubRincian108: '',
                isSubRincian108Open: false,

                // State Search Filter Unit & Paviliun untuk Ruang Pemegang (KIB B, KIB E, ATB)
                searchRuangPemegang: '',
                isRuangPemegangOpen: false,
                searchRuangPemegangLainnya: '',
                isRuangPemegangLainnyaOpen: false,
                searchRuangPemegangAtb: '',
                isRuangPemegangAtbOpen: false,

                // State Auto-Loaded Pagu Anggaran dari Riwayat Database
                isAnggaranAutoLoaded: false,
                anggaranAutoLoadedMessage: '',
                existingRealisasiDb: 0,
                nilaiBarangSaatIni: 0,

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

                // Data Model Multi-Step — default kosong, diisi oleh init() bila mode edit
                formData: (() => {
                    const ea = window.editingAstap || null;
                    let spec = {};
                    if (ea && ea.spesifikasi_json) {
                        try {
                            spec = typeof ea.spesifikasi_json === 'string' ? JSON.parse(ea.spesifikasi_json) : ea.spesifikasi_json;
                        } catch(e) {
                            spec = {};
                        }
                    }
                    const reg0 = ea && ea.registers && ea.registers[0] ? ea.registers[0] : null;
                    const jp = ea && ea.jenis_pengadaan ? ea.jenis_pengadaan : null;
                    const rb = ea && ea.rekening_belanja ? ea.rekening_belanja : null;
                    const ja = ea && ea.jenis_astap ? ea.jenis_astap : null;
                    const nama = ea ? (ea.nama_barang || '') : '';
                    const kode108Val = ea ? (ea.kode_108 || (ja ? (ja.sub_sub_rincian_objek || ja.jenis) : '')) : '';
                    const fmtDate = (d) => d ? String(d).substring(0, 10) : '';
                    return {
                        id: ea ? ea.id : null,
                        // LANGKAH 1
                        jenis_pengadaan_id: jp ? jp.id : null,
                        program_kode: jp ? (jp.program_kode || '') : '',
                        program_nama: jp ? (jp.program_nama || '') : '',
                        kegiatan_kode: jp ? (jp.kegiatan_kode || '') : '',
                        kegiatan_nama: jp ? (jp.kegiatan_nama || '') : '',
                        sub_kegiatan_kode: jp ? (jp.sub_kegiatan_kode || '') : '',
                        sub_kegiatan_nama: jp ? (jp.sub_kegiatan_nama || '') : '',
                        keterangan_pengadaan: '',
                        // LANGKAH 2
                        tahun_anggaran: ea ? (ea.tahun_perolehan || '') : '',
                        triwulan: ea ? (ea.triwulan || (spec.triwulan || '')) : '',
                        kode_rek: rb ? (rb.kode_rek || '') : '',
                        nama_belanja: rb ? (rb.nama_belanja || '') : '',
                        jenis_aset_kode: ja ? (ja.jenis || (kode108Val ? kode108Val.substring(0, 5) : '')) : (kode108Val ? kode108Val.substring(0, 5) : ''),
                        jenis_aset_nama: ja ? (ja.nama_jenis || '') : '',
                        sub_rincian_kode: ja ? (ja.sub_sub_rincian_objek || ja.sub_rincian_objek || '') : '',
                        sub_rincian_nama: ja ? (ja.uraian_sub_sub_rincian || ja.uraian_sub_rincian || '') : '',
                        jumlah_anggaran: ea ? (ea.jumlah_anggaran ?? (spec.jumlah_anggaran ?? (ea.total_realisasi || 0))) : 0,
                        jumlah_realisasi: ea ? (ea.jumlah_realisasi ?? (ea.total_realisasi || 0)) : 0,
                        // LANGKAH 3 — KIB A Tanah (Multi-Item Repeater)
                        tanah_nama_barang: nama || '',
                        tanah_kode_barang: kode108Val || '',
                        tanah_hak: spec.hak_tanah || 'Hak Pakai',
                        tanah_sertifikat_tgl: spec.sertifikat_tgl || '',
                        tanah_sertifikat_no: spec.sertifikat_no || '',
                        tanah_kondisi: reg0 ? (reg0.kondisi || 'Baik') : 'Baik',
                        tanah_penggunaan: spec.penggunaan || 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                        tanah_jumlah_bidang: ea ? (ea.jumlah_volume || 1) : 1,
                        tanah_luas_m2: spec.luas_m2 || 0,
                        tanah_nilai_perencanaan: spec.nilai_perencanaan || 0,
                        tanah_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                        tanah_nilai_pengawasan: spec.nilai_pengawasan || 0,
                        tanah_items: (spec && spec.tanah_items && Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0)
                            ? spec.tanah_items
                            : [
                                {
                                    tanah_hak: spec.hak_tanah || 'Hak Pakai',
                                    tanah_sertifikat_tgl: spec.sertifikat_tgl || '',
                                    tanah_sertifikat_no: spec.sertifikat_no || '',
                                    tanah_kondisi: reg0 ? (reg0.kondisi || 'Baik') : 'Baik',
                                    tanah_penggunaan: spec.penggunaan || 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                                    tanah_jumlah_bidang: ea ? (ea.jumlah_volume || 1) : 1,
                                    tanah_luas_m2: spec.luas_m2 || 0,
                                    tanah_nilai_perencanaan: spec.nilai_perencanaan || 0,
                                    tanah_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                                    tanah_nilai_pengawasan: spec.nilai_pengawasan || 0,
                                    tanah_alamat: ea ? (ea.alamat_barang || '') : ''
                                }
                            ],
                        // KIB B Mesin (Multi-Item Repeater)
                        mesin_nama_barang: nama || '',
                        mesin_kode_barang: kode108Val || '',
                        mesin_merk: spec.merk || '',
                        mesin_type: spec.type || '',
                        mesin_ukuran: spec.ukuran || '',
                        mesin_no_pabrik: spec.no_pabrik || '',
                        mesin_no_rangka: spec.no_rangka || '',
                        mesin_no_mesin: spec.no_mesin || '',
                        mesin_no_bpkb: spec.no_bpkb || '',
                        mesin_no_polisi: spec.no_polisi || '',
                        mesin_bahan: spec.bahan || '',
                        mesin_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
                        ruang_pemegang_mesin: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                        ruang_pemegang: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                        mesin_jumlah_barang: ea ? (ea.jumlah_volume || 1) : 1,
                        mesin_satuan: ea ? (ea.satuan || 'Unit') : 'Unit',
                        mesin_nilai_satuan: ea ? (ea.harga_satuan || 0) : 0,
                        mesin_administrasi_proyek: ea ? (ea.biaya_administrasi_proyek || 0) : 0,
                        mesin_items: (spec && spec.mesin_items && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0)
                            ? spec.mesin_items.map(m => ({
                                mesin_nama_barang: m.mesin_nama_barang || nama || '',
                                mesin_kode_barang: m.mesin_kode_barang || kode108Val || '',
                                mesin_merk: m.mesin_merk || '',
                                mesin_type: m.mesin_type || '',
                                mesin_ukuran: m.mesin_ukuran || '',
                                mesin_no_pabrik: m.mesin_no_pabrik || '',
                                mesin_no_rangka: m.mesin_no_rangka || '',
                                mesin_no_mesin: m.mesin_no_mesin || '',
                                mesin_no_bpkb: m.mesin_no_bpkb || '',
                                mesin_no_polisi: m.mesin_no_polisi || '',
                                mesin_bahan: m.mesin_bahan || '',
                                mesin_kondisi: m.mesin_kondisi || 'B',
                                mesin_jumlah_barang: m.mesin_jumlah_barang || 1,
                                mesin_satuan: m.mesin_satuan || 'Unit',
                                mesin_nilai_satuan: m.mesin_nilai_satuan || 0,
                                mesin_administrasi_proyek: m.mesin_administrasi_proyek || 0,
                                ruang_pemegang: m.ruang_pemegang || m.ruang_pemegang_mesin || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                                ruang_pemegang_mesin: m.ruang_pemegang || m.ruang_pemegang_mesin || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                                isRuangOpen: false,
                                searchRuang: ''
                            }))
                            : [
                                {
                                    mesin_nama_barang: nama || '',
                                    mesin_kode_barang: kode108Val || '',
                                    mesin_merk: spec.merk || '',
                                    mesin_type: spec.type || '',
                                    mesin_ukuran: spec.ukuran || '',
                                    mesin_no_pabrik: spec.no_pabrik || '',
                                    mesin_no_rangka: spec.no_rangka || '',
                                    mesin_no_mesin: spec.no_mesin || '',
                                    mesin_no_bpkb: spec.no_bpkb || '',
                                    mesin_no_polisi: spec.no_polisi || '',
                                    mesin_bahan: spec.bahan || '',
                                    mesin_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
                                    mesin_jumlah_barang: ea ? (ea.jumlah_volume || 1) : 1,
                                    mesin_satuan: ea ? (ea.satuan || 'Unit') : 'Unit',
                                    mesin_nilai_satuan: ea ? (ea.harga_satuan || 0) : 0,
                                    mesin_administrasi_proyek: ea ? (ea.biaya_administrasi_proyek || 0) : 0,
                                    ruang_pemegang: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                                    isRuangOpen: false,
                                    searchRuang: ''
                                }
                            ],
                        // KIB C Gedung (Multi-Item Repeater)
                        gedung_nama_barang: nama || '',
                        gedung_kode_barang: kode108Val || '',
                        gedung_luas_m2: spec.luas_m2 || 0,
                        gedung_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
                        gedung_bertingkat: spec.bertingkat || 'Bertingkat',
                        gedung_beton: spec.beton || 'Beton',
                        gedung_status_tanah: spec.status_tanah || 'Tanah Hak Pakai RSUD',
                        gedung_kode_aset_tanah: spec.kode_aset_tanah || '1.3.1.01.01.02.013',
                        gedung_is_baru: spec.is_baru || 'Baru',
                        gedung_kapitalisasi_tahun_induk: spec.kapitalisasi_tahun_induk || '',
                        gedung_kapitalisasi_nilai_induk: spec.kapitalisasi_nilai_induk || 0,
                        gedung_jumlah_bangunan: ea ? (ea.jumlah_volume || 1) : 1,
                        gedung_satuan: ea ? (ea.satuan || 'Gedung') : 'Gedung',
                        gedung_nilai_perencanaan: spec.nilai_perencanaan || 0,
                        gedung_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                        gedung_nilai_pengawasan: spec.nilai_pengawasan || 0,
                        gedung_nilai_ap: spec.nilai_ap || spec.nilai_pip || 0,
                        gedung_nilai_pip: spec.nilai_ap || spec.nilai_pip || 0,
                        gedung_items: (spec && spec.gedung_items && Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0)
                            ? spec.gedung_items.map(g => ({
                                gedung_nama_barang: g.gedung_nama_barang || nama || '',
                                gedung_kode_barang: g.gedung_kode_barang || kode108Val || '',
                                gedung_luas_m2: g.gedung_luas_m2 || 0,
                                gedung_kondisi: g.gedung_kondisi || 'B',
                                gedung_bertingkat: g.gedung_bertingkat || 'Bertingkat',
                                gedung_beton: g.gedung_beton || 'Beton',
                                gedung_status_tanah: g.gedung_status_tanah || 'Tanah Hak Pakai RSUD',
                                gedung_kode_aset_tanah: g.gedung_kode_aset_tanah || '1.3.1.01.01.02.013',
                                gedung_is_baru: g.gedung_is_baru || 'Baru',
                                gedung_kapitalisasi_tahun_induk: g.gedung_kapitalisasi_tahun_induk || '',
                                gedung_kapitalisasi_nilai_induk: g.gedung_kapitalisasi_nilai_induk || 0,
                                gedung_jumlah_bangunan: g.gedung_jumlah_bangunan || 1,
                                gedung_satuan: g.gedung_satuan || 'Gedung',
                                gedung_nilai_perencanaan: g.gedung_nilai_perencanaan || 0,
                                gedung_nilai_fisik: g.gedung_nilai_fisik || 0,
                                gedung_nilai_pengawasan: g.gedung_nilai_pengawasan || 0,
                                gedung_nilai_ap: g.gedung_nilai_ap || g.gedung_nilai_pip || 0,
                                gedung_nilai_pip: g.gedung_nilai_ap || g.gedung_nilai_pip || 0,
                                gedung_alamat: g.gedung_alamat || (ea ? (ea.alamat_barang || '') : '')
                            }))
                            : [
                                {
                                    gedung_nama_barang: nama || '',
                                    gedung_kode_barang: kode108Val || '',
                                    gedung_luas_m2: spec.luas_m2 || 0,
                                    gedung_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
                                    gedung_bertingkat: spec.bertingkat || 'Bertingkat',
                                    gedung_beton: spec.beton || 'Beton',
                                    gedung_status_tanah: spec.status_tanah || 'Tanah Hak Pakai RSUD',
                                    gedung_kode_aset_tanah: spec.kode_aset_tanah || '1.3.1.01.01.02.013',
                                    gedung_is_baru: spec.is_baru || 'Baru',
                                    gedung_kapitalisasi_tahun_induk: spec.kapitalisasi_tahun_induk || '',
                                    gedung_kapitalisasi_nilai_induk: spec.kapitalisasi_nilai_induk || 0,
                                    gedung_jumlah_bangunan: ea ? (ea.jumlah_volume || 1) : 1,
                                    gedung_satuan: ea ? (ea.satuan || 'Gedung') : 'Gedung',
                                    gedung_nilai_perencanaan: spec.nilai_perencanaan || 0,
                                    gedung_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                                    gedung_nilai_pengawasan: spec.nilai_pengawasan || 0,
                                    gedung_nilai_ap: spec.nilai_ap || spec.nilai_pip || 0,
                                    gedung_nilai_pip: spec.nilai_ap || spec.nilai_pip || 0,
                                    gedung_alamat: ea ? (ea.alamat_barang || '') : ''
                                }
                            ],
                        // KIB D Jaringan (Multi-Item Repeater)
                        jaringan_nama_barang: nama || '',
                        jaringan_kode_barang: kode108Val || '',
                        jaringan_konstruksi: spec.konstruksi || '',
                        jaringan_panjang_m: spec.panjang_m || 0,
                        jaringan_lebar_m: spec.lebar_m || 0,
                        jaringan_luas_m2: spec.luas_m2 || 0,
                        jaringan_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
                        jaringan_bertingkat: spec.bertingkat || 'Bertingkat',
                        jaringan_beton: spec.beton || 'Beton',
                        jaringan_status_tanah: spec.status_tanah || 'Tanah Hak Pakai RSUD',
                        jaringan_kode_aset_tanah: spec.kode_aset_tanah || '1.3.1.01.01.02.013',
                        jaringan_is_baru: spec.is_baru || 'Baru',
                        jaringan_kapitalisasi_tahun_induk: spec.kapitalisasi_tahun_induk || '',
                        jaringan_kapitalisasi_nilai_induk: spec.kapitalisasi_nilai_induk || 0,
                        jaringan_jumlah: ea ? (ea.jumlah_volume || 1) : 1,
                        jaringan_satuan: ea ? (ea.satuan || 'Paket') : 'Paket',
                        jaringan_nilai_perencanaan: spec.nilai_perencanaan || 0,
                        jaringan_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                        jaringan_nilai_pengawasan: spec.nilai_pengawasan || 0,
                        jaringan_nilai_ap: spec.nilai_ap || spec.nilai_pip || 0,
                        jaringan_nilai_pip: spec.nilai_ap || spec.nilai_pip || 0,
                        jaringan_items: (spec && spec.jaringan_items && Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0)
                            ? spec.jaringan_items.map(j => ({
                                jaringan_nama_barang: j.jaringan_nama_barang || nama || '',
                                jaringan_kode_barang: j.jaringan_kode_barang || kode108Val || '',
                                jaringan_konstruksi: j.jaringan_konstruksi || '',
                                jaringan_panjang_m: j.jaringan_panjang_m || 0,
                                jaringan_lebar_m: j.jaringan_lebar_m || 0,
                                jaringan_luas_m2: j.jaringan_luas_m2 || ((parseFloat(j.jaringan_panjang_m) || 0) * (parseFloat(j.jaringan_lebar_m) || 0)) || 0,
                                jaringan_kondisi: j.jaringan_kondisi || 'B',
                                jaringan_bertingkat: j.jaringan_bertingkat || 'Bertingkat',
                                jaringan_beton: j.jaringan_beton || 'Beton',
                                jaringan_status_tanah: j.jaringan_status_tanah || 'Tanah Hak Pakai RSUD',
                                jaringan_kode_aset_tanah: j.jaringan_kode_aset_tanah || '1.3.1.01.01.02.013',
                                jaringan_is_baru: j.jaringan_is_baru || 'Baru',
                                jaringan_kapitalisasi_tahun_induk: j.jaringan_kapitalisasi_tahun_induk || '',
                                jaringan_kapitalisasi_nilai_induk: j.jaringan_kapitalisasi_nilai_induk || 0,
                                jaringan_jumlah: j.jaringan_jumlah || 1,
                                jaringan_satuan: j.jaringan_satuan || 'Paket',
                                jaringan_nilai_perencanaan: j.jaringan_nilai_perencanaan || 0,
                                jaringan_nilai_fisik: j.jaringan_nilai_fisik || 0,
                                jaringan_nilai_pengawasan: j.jaringan_nilai_pengawasan || 0,
                                jaringan_nilai_ap: j.jaringan_nilai_ap || j.jaringan_nilai_pip || 0,
                                jaringan_nilai_pip: j.jaringan_nilai_ap || j.jaringan_nilai_pip || 0,
                                jaringan_alamat: j.jaringan_alamat || (ea ? (ea.alamat_barang || '') : '')
                            }))
                            : [
                                {
                                    jaringan_nama_barang: nama || '',
                                    jaringan_kode_barang: kode108Val || '',
                                    jaringan_konstruksi: spec.konstruksi || '',
                                    jaringan_panjang_m: spec.panjang_m || 0,
                                    jaringan_lebar_m: spec.lebar_m || 0,
                                    jaringan_luas_m2: spec.luas_m2 || ((parseFloat(spec.panjang_m) || 0) * (parseFloat(spec.lebar_m) || 0)) || 0,
                                    jaringan_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
                                    jaringan_bertingkat: spec.bertingkat || 'Bertingkat',
                                    jaringan_beton: spec.beton || 'Beton',
                                    jaringan_status_tanah: spec.status_tanah || 'Tanah Hak Pakai RSUD',
                                    jaringan_kode_aset_tanah: spec.kode_aset_tanah || '1.3.1.01.01.02.013',
                                    jaringan_is_baru: spec.is_baru || 'Baru',
                                    jaringan_kapitalisasi_tahun_induk: spec.kapitalisasi_tahun_induk || '',
                                    jaringan_kapitalisasi_nilai_induk: spec.kapitalisasi_nilai_induk || 0,
                                    jaringan_jumlah: ea ? (ea.jumlah_volume || 1) : 1,
                                    jaringan_satuan: ea ? (ea.satuan || 'Paket') : 'Paket',
                                    jaringan_nilai_perencanaan: spec.nilai_perencanaan || 0,
                                    jaringan_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                                    jaringan_nilai_pengawasan: spec.nilai_pengawasan || 0,
                                    jaringan_nilai_ap: spec.nilai_ap || spec.nilai_pip || 0,
                                    jaringan_nilai_pip: spec.nilai_ap || spec.nilai_pip || 0,
                                    jaringan_alamat: ea ? (ea.alamat_barang || '') : ''
                                }
                            ],
                        // KIB E Lainnya (Multi-Item Repeater)
                        kib_e_sub_type: ea ? (spec.kib_e_sub_type || (spec.buku_judul ? 'buku' : (spec.kesenian_asal || spec.kesenian_pencipta || spec.kesenian_bahan ? 'kesenian' : (spec.hewan_jenis || spec.hewan_judul ? 'hewan_tumbuhan' : 'buku')))) : 'buku',
                        kib_e_default_type: ea ? (spec.kib_e_sub_type || (spec.buku_judul ? 'buku' : (spec.kesenian_asal || spec.kesenian_pencipta || spec.kesenian_bahan ? 'kesenian' : (spec.hewan_jenis || spec.hewan_judul ? 'hewan_tumbuhan' : 'buku')))) : 'buku',
                        lainnya_nama_barang: nama || '',
                        lainnya_kode_barang: kode108Val || '',
                        lainnya_buku_judul: spec.buku_judul || '',
                        lainnya_buku_pencipta: spec.buku_pencipta || '',
                        lainnya_buku_spesifikasi: spec.buku_spesifikasi || '',
                        lainnya_kesenian_asal: spec.kesenian_asal || '',
                        lainnya_kesenian_pencipta: spec.kesenian_pencipta || '',
                        lainnya_kesenian_spesifikasi: spec.kesenian_spesifikasi || '',
                        lainnya_kesenian_bahan: spec.kesenian_bahan || '',
                        lainnya_kesenian_ukuran: spec.kesenian_ukuran || '',
                        lainnya_hewan_judul: spec.hewan_judul || spec.hewan_jenis || '',
                        lainnya_hewan_jenis: spec.hewan_jenis || spec.hewan_judul || '',
                        lainnya_hewan_spesifikasi: spec.hewan_spesifikasi || '',
                        ruang_pemegang_lainnya: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                        lainnya_kondisi: reg0 ? (reg0.kondisi || 'Baik') : 'Baik',
                        lainnya_jumlah_barang: ea ? (ea.jumlah_volume || 1) : 1,
                        lainnya_satuan: ea ? (ea.satuan || 'Eksemplar') : 'Eksemplar',
                        lainnya_nilai_satuan: ea ? (ea.harga_satuan || 0) : 0,
                        lainnya_administrasi_proyek: ea ? (ea.biaya_administrasi_proyek || 0) : 0,
                        lainnya_items: (spec && spec.lainnya_items && Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0)
                            ? spec.lainnya_items.map(l => ({
                                kib_e_sub_type: l.kib_e_sub_type || (l.lainnya_buku_judul ? 'buku' : (l.lainnya_kesenian_asal || l.lainnya_kesenian_pencipta ? 'kesenian' : (l.lainnya_hewan_jenis || l.lainnya_hewan_judul ? 'hewan_tumbuhan' : 'buku'))),
                                lainnya_nama_barang: l.lainnya_nama_barang || nama || '',
                                lainnya_kode_barang: l.lainnya_kode_barang || kode108Val || '',
                                lainnya_buku_judul: l.lainnya_buku_judul || '',
                                lainnya_buku_pencipta: l.lainnya_buku_pencipta || '',
                                lainnya_buku_spesifikasi: l.lainnya_buku_spesifikasi || '',
                                lainnya_kesenian_asal: l.lainnya_kesenian_asal || '',
                                lainnya_kesenian_pencipta: l.lainnya_kesenian_pencipta || '',
                                lainnya_kesenian_spesifikasi: l.lainnya_kesenian_spesifikasi || '',
                                lainnya_kesenian_bahan: l.lainnya_kesenian_bahan || '',
                                lainnya_kesenian_ukuran: l.lainnya_kesenian_ukuran || '',
                                lainnya_hewan_judul: l.lainnya_hewan_judul || l.lainnya_hewan_jenis || '',
                                lainnya_hewan_jenis: l.lainnya_hewan_jenis || l.lainnya_hewan_judul || '',
                                lainnya_hewan_spesifikasi: l.lainnya_hewan_spesifikasi || '',
                                ruang_pemegang: l.ruang_pemegang || l.ruang_pemegang_lainnya || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                                ruang_pemegang_lainnya: l.ruang_pemegang || l.ruang_pemegang_lainnya || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                                lainnya_kondisi: l.lainnya_kondisi || (reg0 ? (reg0.kondisi || 'Baik') : 'Baik'),
                                lainnya_jumlah_barang: l.lainnya_jumlah_barang || 1,
                                lainnya_satuan: l.lainnya_satuan || 'Eksemplar',
                                lainnya_nilai_satuan: l.lainnya_nilai_satuan || 0,
                                lainnya_administrasi_proyek: l.lainnya_administrasi_proyek || 0,
                                isRuangOpen: false,
                                searchRuang: ''
                            }))
                            : [
                                {
                                    kib_e_sub_type: spec.kib_e_sub_type || (spec.buku_judul ? 'buku' : (spec.kesenian_asal || spec.kesenian_pencipta || spec.kesenian_bahan ? 'kesenian' : (spec.hewan_jenis || spec.hewan_judul ? 'hewan_tumbuhan' : 'buku'))),
                                    lainnya_nama_barang: nama || '',
                                    lainnya_kode_barang: kode108Val || '',
                                    lainnya_buku_judul: spec.buku_judul || '',
                                    lainnya_buku_pencipta: spec.buku_pencipta || '',
                                    lainnya_buku_spesifikasi: spec.buku_spesifikasi || '',
                                    lainnya_kesenian_asal: spec.kesenian_asal || '',
                                    lainnya_kesenian_pencipta: spec.kesenian_pencipta || '',
                                    lainnya_kesenian_spesifikasi: spec.kesenian_spesifikasi || '',
                                    lainnya_kesenian_bahan: spec.kesenian_bahan || '',
                                    lainnya_kesenian_ukuran: spec.kesenian_ukuran || '',
                                    lainnya_hewan_judul: spec.hewan_judul || spec.hewan_jenis || '',
                                    lainnya_hewan_jenis: spec.hewan_jenis || spec.hewan_judul || '',
                                    lainnya_hewan_spesifikasi: spec.hewan_spesifikasi || '',
                                    ruang_pemegang: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                                    ruang_pemegang_lainnya: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                                    lainnya_kondisi: reg0 ? (reg0.kondisi || 'Baik') : 'Baik',
                                    lainnya_jumlah_barang: ea ? (ea.jumlah_volume || 1) : 1,
                                    lainnya_satuan: ea ? (ea.satuan || 'Eksemplar') : 'Eksemplar',
                                    lainnya_nilai_satuan: ea ? (ea.harga_satuan || 0) : 0,
                                    lainnya_administrasi_proyek: ea ? (ea.biaya_administrasi_proyek || 0) : 0,
                                    isRuangOpen: false,
                                    searchRuang: ''
                                }
                            ],
                        // ATB (Multi-Item Repeater - sama seperti KIB F)
                        atb_nama_barang: nama || '',
                        atb_kode_barang: kode108Val || '',
                        atb_judul_nama: spec.atb_judul || '',
                        atb_judul: spec.atb_judul || '',
                        atb_pencipta: spec.atb_pencipta || '',
                        atb_jenis_lisensi: spec.atb_jenis_lisensi || '',
                        atb_spesifikasi: spec.atb_spesifikasi || '',
                        ruang_pemegang_atb: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                        atb_kondisi: reg0 ? (reg0.kondisi || 'Baik') : 'Baik',
                        atb_jumlah: ea ? (ea.jumlah_volume || 1) : 1,
                        atb_satuan: ea ? (ea.satuan || 'Lisensi') : 'Lisensi',
                        atb_nilai_satuan: ea ? (ea.harga_satuan || 0) : 0,
                        atb_administrasi_proyek: ea ? (ea.biaya_administrasi_proyek || 0) : 0,
                        atb_items: (spec && spec.atb_items && Array.isArray(spec.atb_items) && spec.atb_items.length > 0)
                            ? spec.atb_items.map(a => ({
                                atb_nama_barang: a.atb_nama_barang || nama || '',
                                atb_kode_barang: a.atb_kode_barang || kode108Val || '',
                                atb_judul_nama: a.atb_judul_nama || a.atb_judul || '',
                                atb_pencipta: a.atb_pencipta || '',
                                atb_spesifikasi: a.atb_spesifikasi || '',
                                atb_jumlah: a.atb_jumlah || 1,
                                atb_satuan: a.atb_satuan || 'Lisensi',
                                atb_kondisi: a.atb_kondisi || 'Baik',
                                atb_nilai_satuan: a.atb_nilai_satuan || 0,
                                atb_administrasi_proyek: a.atb_administrasi_proyek || 0,
                                atb_ruang_pemegang: a.atb_ruang_pemegang || spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                                isRuangOpen: false,
                                searchRuang: ''
                            }))
                            : [{
                                atb_nama_barang: nama || '',
                                atb_kode_barang: kode108Val || '',
                                atb_judul_nama: spec.atb_judul || '',
                                atb_pencipta: spec.atb_pencipta || '',
                                atb_spesifikasi: spec.atb_spesifikasi || '',
                                atb_jumlah: ea ? (ea.jumlah_volume || 1) : 1,
                                atb_satuan: ea ? (ea.satuan || 'Lisensi') : 'Lisensi',
                                atb_kondisi: reg0 ? (reg0.kondisi || 'Baik') : 'Baik',
                                atb_nilai_satuan: ea ? (ea.harga_satuan || 0) : 0,
                                atb_administrasi_proyek: ea ? (ea.biaya_administrasi_proyek || 0) : 0,
                                atb_ruang_pemegang: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                                isRuangOpen: false,
                                searchRuang: ''
                            }],

                        kdp_nama_barang: nama || '',
                        kdp_kode_barang: kode108Val || '',
                        kdp_bangunan: spec.bertingkat || 'Bertingkat',
                        kdp_beton: spec.beton || 'Beton',
                        kdp_luas_m2: spec.luas_m2 || 0,
                        kdp_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
                        kdp_progres_persen: spec.progres_persen || 0,
                        kdp_status_tanah: spec.status_tanah || 'Tanah Hak Pakai RSUD',
                        kdp_kode_aset_tanah: spec.kode_aset_tanah || '1.3.1.01.01.02.013',
                        kdp_is_baru: spec.is_baru || 'Baru',
                        kdp_kapitalisasi_tahun_induk: spec.kapitalisasi_tahun_induk || '',
                        kdp_kapitalisasi_nilai_induk: spec.kapitalisasi_nilai_induk || 0,
                        kdp_jumlah_bangunan: ea ? (ea.jumlah_volume || 1) : 1,
                        kdp_satuan: ea ? (ea.satuan || 'Gedung') : 'Gedung',
                        kdp_nilai_perencanaan: spec.nilai_perencanaan || 0,
                        kdp_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                        kdp_nilai_pengawasan: spec.nilai_pengawasan || 0,
                        kdp_nilai_ap: spec.nilai_ap || spec.nilai_pip || 0,
                        kdp_nilai_pip: spec.nilai_ap || spec.nilai_pip || 0,
                        kdp_items: (spec && spec.kdp_items && Array.isArray(spec.kdp_items) && spec.kdp_items.length > 0)
                            ? spec.kdp_items.map(k => ({
                                kdp_nama_barang: k.kdp_nama_barang || nama || '',
                                kdp_kode_barang: k.kdp_kode_barang || kode108Val || '',
                                kdp_luas_m2: k.kdp_luas_m2 || 0,
                                kdp_kondisi: k.kdp_kondisi || 'B',
                                kdp_progres_persen: k.kdp_progres_persen || 0,
                                kdp_bertingkat: k.kdp_bertingkat || k.kdp_bangunan || 'Bertingkat',
                                kdp_beton: k.kdp_beton || 'Beton',
                                kdp_status_tanah: k.kdp_status_tanah || 'Tanah Hak Pakai RSUD',
                                kdp_kode_aset_tanah: k.kdp_kode_aset_tanah || '1.3.1.01.01.02.013',
                                kdp_is_baru: k.kdp_is_baru || 'Baru',
                                kdp_kapitalisasi_tahun_induk: k.kdp_kapitalisasi_tahun_induk || '',
                                kdp_kapitalisasi_nilai_induk: k.kdp_kapitalisasi_nilai_induk || 0,
                                kdp_jumlah_bangunan: k.kdp_jumlah_bangunan || 1,
                                kdp_satuan: k.kdp_satuan || 'Gedung',
                                kdp_nilai_perencanaan: k.kdp_nilai_perencanaan || 0,
                                kdp_nilai_fisik: k.kdp_nilai_fisik || 0,
                                kdp_nilai_pengawasan: k.kdp_nilai_pengawasan || 0,
                                kdp_nilai_ap: k.kdp_nilai_ap || k.kdp_nilai_pip || 0,
                                kdp_nilai_pip: k.kdp_nilai_ap || k.kdp_nilai_pip || 0,
                                kdp_alamat: k.kdp_alamat || (ea ? (ea.alamat_barang || '') : '')
                            }))
                            : [
                                {
                                    kdp_nama_barang: nama || '',
                                    kdp_kode_barang: kode108Val || '',
                                    kdp_luas_m2: spec.luas_m2 || 0,
                                    kdp_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
                                    kdp_progres_persen: spec.progres_persen || 0,
                                    kdp_bertingkat: spec.bertingkat || 'Bertingkat',
                                    kdp_beton: spec.beton || 'Beton',
                                    kdp_status_tanah: spec.status_tanah || 'Tanah Hak Pakai RSUD',
                                    kdp_kode_aset_tanah: spec.kode_aset_tanah || '1.3.1.01.01.02.013',
                                    kdp_is_baru: spec.is_baru || 'Baru',
                                    kdp_kapitalisasi_tahun_induk: spec.kapitalisasi_tahun_induk || '',
                                    kdp_kapitalisasi_nilai_induk: spec.kapitalisasi_nilai_induk || 0,
                                    kdp_jumlah_bangunan: ea ? (ea.jumlah_volume || 1) : 1,
                                    kdp_satuan: ea ? (ea.satuan || 'Gedung') : 'Gedung',
                                    kdp_nilai_perencanaan: spec.nilai_perencanaan || 0,
                                    kdp_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                                    kdp_nilai_pengawasan: spec.nilai_pengawasan || 0,
                                    kdp_nilai_ap: spec.nilai_ap || spec.nilai_pip || 0,
                                    kdp_nilai_pip: spec.nilai_ap || spec.nilai_pip || 0,
                                    kdp_alamat: ea ? (ea.alamat_barang || '') : ''
                                }
                            ],
                        // Dokumen Pengadaan
                        spk_nomor: ea ? (ea.spk_nomor || '') : '',
                        spk_tanggal: ea ? fmtDate(ea.spk_tanggal) : '',
                        surat_pesanan_nomor: ea ? (ea.surat_pesanan_nomor || '') : '',
                        surat_pesanan_tanggal: ea ? fmtDate(ea.surat_pesanan_tanggal) : '',
                        kwitansi_nomor: ea ? (ea.kwitansi_nomor || '') : '',
                        kwitansi_tanggal: ea ? fmtDate(ea.kwitansi_tanggal) : '',
                        faktur_nomor: ea ? (ea.faktur_nomor || '') : '',
                        faktur_tanggal: ea ? fmtDate(ea.faktur_tanggal) : '',
                        sp2d_nomor: ea ? (ea.sp2d_nomor || '') : '',
                        sp2d_tanggal: ea ? fmtDate(ea.sp2d_tanggal) : '',
                        bast_dokumen_nomor: ea ? (ea.bast_dokumen_nomor || '') : '',
                        bast_dokumen_tanggal: ea ? fmtDate(ea.bast_dokumen_tanggal) : '',
                        // Langkah 4
                        tahun_perolehan: ea ? (ea.tahun_perolehan || '') : '',
                        alamat_barang: ea ? (ea.alamat_barang || '') : '',
                        penyedia_nama: ea ? (ea.penyedia_nama || '') : '',
                        penyedia_pemilik: ea ? (ea.penyedia_pemilik || '') : '',
                        penyedia_telepon: ea ? (ea.penyedia_telepon || (spec ? (spec.penyedia_telepon || spec.penyedia_kontak) : '') || '') : '',
                        penyedia_rekening_nama: ea ? (ea.penyedia_rekening_nama || '') : '',
                        penyedia_rekening_nomor: ea ? (ea.penyedia_rekening_nomor || '') : '',
                        penyedia_alamat: ea ? (ea.penyedia_alamat || '') : '',
                        ppk_nama: ea ? (ea.ppk_nama || '') : '',
                        ppk_nip: ea ? (ea.ppk_nip || '') : '',
                        keterangan_tambahan: ea ? (ea.keterangan_tambahan || '') : '',
                        is_extracomtable: ea ? !!ea.is_extracomtable : false,
                        doc_type: ea ? (ea.spk_nomor ? 'spk' : (ea.surat_pesanan_nomor ? 'surat_pesanan' : (ea.kwitansi_nomor ? 'kwitansi' : (ea.faktur_nomor ? 'faktur' : 'spk')))) : 'spk'
                    };
                })(),

                init() {
                    // Membangun Hirarki SIPD Langkah 1 dari Database SQLite (jenis_pengadaans)
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
                    }

                    // Membangun Rekening Belanja Langkah 2 dari Database SQLite (rekening_belanjas)
                    if (window.dbRekeningBelanjas && window.dbRekeningBelanjas.length > 0) {
                        this.masterRekeningBelanja = window.dbRekeningBelanjas.map(item => ({
                            id: item.id,
                            kode_rek: item.kode_rek,
                            nama_belanja: item.nama_belanja,
                            kelompok: item.kelompok,
                            default_jenis_kode: item.kelompok === 'Tanah' ? '1.3.1' : (item.kelompok === 'Bangunan' ? '1.3.3' : '1.3.2')
                        }));
                    }

                    // Pre-fill Mode Edit (Langkah 1, Langkah 2, dan Langkah 3/4)
                    if (window.editingAstap) {
                        const ea = window.editingAstap;
                        
                        // 1. Pre-fill Langkah 1 (SIPD)
                        const jp = ea.jenis_pengadaan 
                            || (window.dbJenisPengadaans || []).find(j => j.id === ea.jenis_pengadaan_id) 
                            || ((window.dbJenisPengadaans && window.dbJenisPengadaans.length > 0) ? window.dbJenisPengadaans[0] : null);
                        
                        if (jp) {
                            this.formData.jenis_pengadaan_id = jp.id || ea.jenis_pengadaan_id;
                            this.formData.program_kode = jp.program_kode || '';
                            this.formData.program_nama = jp.program_nama || '';
                            this.formData.kegiatan_kode = jp.kegiatan_kode || '';
                            this.formData.kegiatan_nama = jp.kegiatan_nama || '';
                            this.formData.sub_kegiatan_kode = jp.sub_kegiatan_kode || '';
                            this.formData.sub_kegiatan_nama = jp.sub_kegiatan_nama || '';
                        }

                        // 2. Pre-fill Langkah 2 (Rekening Belanja)
                        const rb = ea.rekening_belanja 
                            || (window.dbRekeningBelanjas || []).find(r => r.id === ea.rekening_belanja_id)
                            || ((window.dbRekeningBelanjas && window.dbRekeningBelanjas.length > 0) ? window.dbRekeningBelanjas[0] : null);

                        if (rb) {
                            this.formData.kode_rek = rb.kode_rek || '';
                            this.formData.nama_belanja = rb.nama_belanja || '';
                        }

                        // 3. Pre-fill Langkah 2 (PMDN 108 Sub Rincian Objek)
                        const ja = ea.jenis_astap || null;
                        const kode108Val = ea.kode_108 || (ja ? (ja.sub_sub_rincian_objek || ja.jenis) : '');

                        if (ja) {
                            this.formData.jenis_aset_kode = ja.jenis || (kode108Val ? kode108Val.substring(0, 5) : '1.3.2');
                            this.formData.jenis_aset_nama = ja.nama_jenis || 'PERALATAN DAN MESIN';
                        }

                        let srKode = ja ? (ja.sub_rincian_objek || '') : '';
                        if (!srKode && kode108Val && kode108Val.length >= 14) {
                            srKode = kode108Val.substring(0, 14);
                        }
                        
                        if (srKode) {
                            this.formData.sub_rincian_kode = srKode;
                            const foundSub = (this.availableSubRincian108 || []).find(s => s.kode === srKode);
                            if (foundSub) {
                                this.formData.sub_rincian_nama = foundSub.nama;
                            } else if (ja && ja.uraian_sub_rincian) {
                                this.formData.sub_rincian_nama = ja.uraian_sub_rincian;
                            }
                        }
                    }

                    this.updateExtracomStatus();

                    // Watcher perpindahan Step
                    this.$watch('currentStep', (step) => {
                        this.$nextTick(() => {
                            this.scrollToTop();
                        });
                        if (step === 3) {
                            this.syncRealisasiFromStep3();
                        }
                    });

                    // Watchers Langkah 2: Otomatis Muat Pagu Anggaran jika Sub Rincian / Tahun / TW berubah
                    this.$watch('formData.sub_rincian_kode', () => this.fetchExistingAnggaran());
                    this.$watch('formData.tahun_anggaran', () => this.fetchExistingAnggaran());
                    this.$watch('formData.triwulan', () => this.fetchExistingAnggaran());

                    // Watchers Langkah 3: Otomatis Sinkronisasi Realisasi Langkah 2 dari Total Nilai Barang
                    this.$watch('formData.tanah_items', () => {
                        this.syncTanahFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }, { deep: true });
                    this.$watch('formData.mesin_items', () => {
                        this.syncMesinFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }, { deep: true });
                    this.$watch('formData.gedung_items', () => {
                        this.syncGedungFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }, { deep: true });
                    this.$watch('formData.jaringan_items', () => {
                        this.syncJaringanFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }, { deep: true });
                    this.$watch('formData.lainnya_items', () => {
                        this.syncLainnyaFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }, { deep: true });
                    this.$watch('formData.kdp_items', () => {
                        this.syncKdpFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }, { deep: true });
                    this.$watch('formData.tanah_nilai_perencanaan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.tanah_nilai_fisik', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.tanah_nilai_pengawasan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.mesin_nilai_satuan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.mesin_jumlah_barang', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.mesin_administrasi_proyek', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.gedung_nilai_perencanaan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.gedung_nilai_fisik', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.gedung_nilai_pengawasan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.gedung_nilai_ap', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.gedung_nilai_pip', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.jaringan_nilai_perencanaan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.jaringan_nilai_fisik', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.jaringan_nilai_pengawasan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.jaringan_nilai_pip', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.lainnya_nilai_satuan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.lainnya_jumlah_barang', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.lainnya_administrasi_proyek', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.atb_nilai_satuan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.atb_jumlah', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.atb_administrasi_proyek', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.atb_items', () => { this.syncAtbFieldsToMain(); this.syncRealisasiFromStep3(); }, { deep: true });

                    this.$watch('formData.kdp_nilai_perencanaan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.kdp_nilai_fisik', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.kdp_nilai_pengawasan', () => this.syncRealisasiFromStep3());
                    this.$watch('formData.kdp_nilai_pip', () => this.syncRealisasiFromStep3());
                },

                fetchExistingAnggaran() {
                    if (!this.formData.sub_rincian_kode || !this.formData.tahun_anggaran || !this.formData.triwulan) {
                        this.isAnggaranAutoLoaded = false;
                        this.anggaranAutoLoadedMessage = '';
                        this.existingRealisasiDb = 0;
                        this.syncRealisasiFromStep3();
                        return;
                    }
                    const subKode = this.formData.sub_rincian_kode;
                    const thn = this.formData.tahun_anggaran;
                    const tw = this.formData.triwulan;

                    fetch('{{ route("astap.checkSubRincianAnggaran") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            sub_rincian_kode: subKode,
                            tahun: thn,
                            triwulan: tw,
                            exclude_id: this.isEdit && this.formData.id ? this.formData.id : null
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.found && data.jumlah_anggaran) {
                            this.formData.jumlah_anggaran = data.jumlah_anggaran;
                            this.existingRealisasiDb = data.total_realisasi_existing || 0;
                            this.isAnggaranAutoLoaded = true;
                            this.anggaranAutoLoadedMessage = '✨ Pagu anggaran Rp ' + Number(data.jumlah_anggaran).toLocaleString('id-ID') + ' dimuat otomatis dari penetapan ' + tw + ' ' + thn;
                        } else {
                            this.formData.jumlah_anggaran = 0;
                            this.existingRealisasiDb = 0;
                            this.isAnggaranAutoLoaded = false;
                            this.anggaranAutoLoadedMessage = '';
                        }
                        this.syncRealisasiFromStep3();
                    })
                    .catch(err => {
                        console.warn('Gagal cek riwayat anggaran sub rincian:', err);
                    });
                },

                syncRealisasiFromStep3() {
                    let currentVal = 0;
                    if (this.isTanah) {
                        currentVal = this.totalNilaiTanah;
                    } else if (this.isMesin) {
                        currentVal = this.totalNilaiMesin;
                    } else if (this.isGedung) {
                        currentVal = this.totalNilaiGedung;
                    } else if (this.isJaringan) {
                        currentVal = this.totalNilaiJaringan;
                    } else if (this.isAsetLainnya) {
                        currentVal = this.totalNilaiAsetLainnya;
                    } else if (this.isAtb) {
                        currentVal = this.totalNilaiAtb;
                    } else if (this.isKdp) {
                        currentVal = this.totalNilaiKdp;
                    }
                    this.nilaiBarangSaatIni = currentVal;
                    this.formData.jumlah_realisasi = (Number(this.existingRealisasiDb) || 0) + currentVal;
                },

                selectDocType(type) {
                    this.formData.doc_type = type;
                    if (type !== 'spk') { this.formData.spk_nomor = ''; this.formData.spk_tanggal = ''; }
                    if (type !== 'surat_pesanan') { this.formData.surat_pesanan_nomor = ''; this.formData.surat_pesanan_tanggal = ''; }
                    if (type !== 'kwitansi') { this.formData.kwitansi_nomor = ''; this.formData.kwitansi_tanggal = ''; }
                    if (type !== 'faktur') { this.formData.faktur_nomor = ''; this.formData.faktur_tanggal = ''; }
                },

                maxDateToday: new Date().toISOString().split('T')[0],
                maxYear: new Date().getFullYear(),

                validateTahunAnggaran() {
                    const currentYear = new Date().getFullYear();
                    if (this.formData.tahun_anggaran) {
                        const val = Number(this.formData.tahun_anggaran);
                        if (val > currentYear) {
                            alert('Tahun anggaran tidak boleh melebihi tahun saat ini (' + currentYear + ')!');
                            this.formData.tahun_anggaran = currentYear;
                            this.formData.tahun_perolehan = currentYear;
                        } else if (val < 1900) {
                            alert('Tahun anggaran harus berupa 4 digit tahun yang valid (minimal tahun 1900)!');
                            this.formData.tahun_anggaran = currentYear;
                            this.formData.tahun_perolehan = currentYear;
                        }
                    }
                },

                get minDateTriwulan() {
                    const year = this.formData.tahun_anggaran || new Date().getFullYear();
                    const tw = String(this.formData.triwulan || '').toUpperCase();
                    if (tw.includes('TW I') || tw.includes('TW 1') || tw.includes('TRIWULAN I') || (tw.includes('I') && !tw.includes('II') && !tw.includes('III') && !tw.includes('IV'))) {
                        return `${year}-01-01`;
                    } else if (tw.includes('TW II') || tw.includes('TW 2') || tw.includes('TRIWULAN II') || (tw.includes('II') && !tw.includes('III'))) {
                        return `${year}-04-01`;
                    } else if (tw.includes('TW III') || tw.includes('TW 3') || tw.includes('TRIWULAN III') || tw.includes('III')) {
                        return `${year}-07-01`;
                    } else if (tw.includes('TW IV') || tw.includes('TW 4') || tw.includes('TRIWULAN IV') || tw.includes('IV')) {
                        return `${year}-10-01`;
                    }
                    return `${year}-01-01`;
                },

                get maxDateTriwulan() {
                    const year = this.formData.tahun_anggaran || new Date().getFullYear();
                    const tw = String(this.formData.triwulan || '').toUpperCase();
                    let endStr = `${year}-12-31`;
                    if (tw.includes('TW I') || tw.includes('TW 1') || tw.includes('TRIWULAN I') || (tw.includes('I') && !tw.includes('II') && !tw.includes('III') && !tw.includes('IV'))) {
                        endStr = `${year}-03-31`;
                    } else if (tw.includes('TW II') || tw.includes('TW 2') || tw.includes('TRIWULAN II') || (tw.includes('II') && !tw.includes('III'))) {
                        endStr = `${year}-06-30`;
                    } else if (tw.includes('TW III') || tw.includes('TW 3') || tw.includes('TRIWULAN III') || tw.includes('III')) {
                        endStr = `${year}-09-30`;
                    } else if (tw.includes('TW IV') || tw.includes('TW 4') || tw.includes('TRIWULAN IV') || tw.includes('IV')) {
                        endStr = `${year}-12-31`;
                    }
                    return endStr;
                },

                get triwulanDateLabel() {
                    if (!this.formData.tahun_anggaran || !this.formData.triwulan) return '';
                    const tw = String(this.formData.triwulan || '').toUpperCase();
                    const year = this.formData.tahun_anggaran;
                    if (tw.includes('TW I') || tw.includes('TW 1') || tw.includes('TRIWULAN I') || (tw.includes('I') && !tw.includes('II') && !tw.includes('III') && !tw.includes('IV'))) {
                        return `01 Jan ${year} s/d 31 Mar ${year}`;
                    } else if (tw.includes('TW II') || tw.includes('TW 2') || tw.includes('TRIWULAN II') || (tw.includes('II') && !tw.includes('III'))) {
                        return `01 Apr ${year} s/d 30 Jun ${year}`;
                    } else if (tw.includes('TW III') || tw.includes('TW 3') || tw.includes('TRIWULAN III') || tw.includes('III')) {
                        return `01 Jul ${year} s/d 30 Sep ${year}`;
                    } else if (tw.includes('TW IV') || tw.includes('TW 4') || tw.includes('TRIWULAN IV') || tw.includes('IV')) {
                        return `01 Okt ${year} s/d 31 Des ${year}`;
                    }
                    return `01 Jan ${year} s/d 31 Des ${year}`;
                },

                syncDatesWithTriwulan() {
                    if (!this.formData.tahun_anggaran || !this.formData.triwulan) return;
                    const minD = this.minDateTriwulan;
                    const maxD = this.maxDateTriwulan;

                    const dateFields = ['spk_tanggal', 'surat_pesanan_tanggal', 'kwitansi_tanggal', 'faktur_tanggal', 'sp2d_tanggal', 'bast_dokumen_tanggal'];
                    dateFields.forEach(field => {
                        if (this.formData[field]) {
                            if (this.formData[field] < minD || this.formData[field] > maxD) {
                                this.formData[field] = minD;
                            }
                        }
                    });
                },

                onDocDateChange(dateStr, fieldName = null) {
                    if (dateStr) {
                        const minD = this.minDateTriwulan;
                        const maxD = this.maxDateTriwulan;
                        
                        if (this.formData.tahun_anggaran && this.formData.triwulan) {
                            if (dateStr < minD || dateStr > maxD) {
                                const twName = this.formData.triwulan;
                                const yr = this.formData.tahun_anggaran;
                                alert(`⚠️ Tanggal dokumen (${dateStr}) harus berada dalam periode ${twName} Tahun ${yr} (${minD} s/d ${maxD})!`);
                                const clamped = dateStr < minD ? minD : maxD;
                                if (fieldName && this.formData[fieldName] !== undefined) {
                                    this.formData[fieldName] = clamped;
                                } else {
                                    const activeDoc = this.formData.doc_type;
                                    if (activeDoc === 'spk') this.formData.spk_tanggal = clamped;
                                    else if (activeDoc === 'surat_pesanan') this.formData.surat_pesanan_tanggal = clamped;
                                    else if (activeDoc === 'kwitansi') this.formData.kwitansi_tanggal = clamped;
                                    else if (activeDoc === 'faktur') this.formData.faktur_tanggal = clamped;
                                }
                                return;
                            }
                        }

                        if (dateStr.length >= 4) {
                            const year = parseInt(dateStr.substring(0, 4));
                            if (year > 1900 && year < 2100) {
                                this.formData.tahun_perolehan = year;
                            }
                        }
                    }
                },

                validateMaxDate(fieldName) {
                    if (this.formData[fieldName]) {
                        this.onDocDateChange(this.formData[fieldName], fieldName);
                    }
                },

                updateExtracomStatus() {
                    // Status extracomtable sekarang ditentukan langsung oleh pilihan eksplisit user di Langkah 3
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
                    if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                        return this.formData.tanah_items.reduce((sum, item) => {
                            return sum + (Number(item.tanah_nilai_perencanaan || 0) + 
                                          Number(item.tanah_nilai_fisik || 0) + 
                                          Number(item.tanah_nilai_pengawasan || 0));
                        }, 0);
                    }
                    return Number(this.formData.tanah_nilai_perencanaan || 0) + 
                           Number(this.formData.tanah_nilai_fisik || 0) + 
                           Number(this.formData.tanah_nilai_pengawasan || 0);
                },

                addTanahItem() {
                    if (!this.formData.tanah_items) {
                        this.formData.tanah_items = [];
                    }
                    this.formData.tanah_items.push({
                        tanah_hak: 'Hak Pakai',
                        tanah_sertifikat_tgl: '',
                        tanah_sertifikat_no: '',
                        tanah_kondisi: 'Baik',
                        tanah_penggunaan: 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                        tanah_jumlah_bidang: 1,
                        tanah_luas_m2: 0,
                        tanah_nilai_perencanaan: 0,
                        tanah_nilai_fisik: 0,
                        tanah_nilai_pengawasan: 0,
                        tanah_alamat: ''
                    });
                    this.syncRealisasiFromStep3();
                },

                removeTanahItem(index) {
                    if (this.formData.tanah_items && this.formData.tanah_items.length > 1) {
                        this.formData.tanah_items.splice(index, 1);
                        this.syncTanahFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }
                },

                syncTanahFieldsToMain() {
                    if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                        const first = this.formData.tanah_items[0];
                        this.formData.tanah_hak = first.tanah_hak;
                        this.formData.tanah_sertifikat_tgl = first.tanah_sertifikat_tgl;
                        this.formData.tanah_sertifikat_no = first.tanah_sertifikat_no;
                        this.formData.tanah_kondisi = first.tanah_kondisi;
                        this.formData.tanah_penggunaan = first.tanah_penggunaan;
                        
                        const totalBidang = this.formData.tanah_items.reduce((sum, item) => sum + (parseInt(item.tanah_jumlah_bidang) || 1), 0);
                        this.formData.tanah_jumlah_bidang = totalBidang;
                        this.formData.jumlah_volume = totalBidang;
                        
                        const totalLuas = this.formData.tanah_items.reduce((sum, item) => sum + (parseFloat(item.tanah_luas_m2) || 0), 0);
                        this.formData.tanah_luas_m2 = totalLuas;
                        
                        this.formData.tanah_nilai_perencanaan = this.formData.tanah_items.reduce((sum, item) => sum + (parseFloat(item.tanah_nilai_perencanaan) || 0), 0);
                        this.formData.tanah_nilai_fisik = this.formData.tanah_items.reduce((sum, item) => sum + (parseFloat(item.tanah_nilai_fisik) || 0), 0);
                        this.formData.tanah_nilai_pengawasan = this.formData.tanah_items.reduce((sum, item) => sum + (parseFloat(item.tanah_nilai_pengawasan) || 0), 0);
                        
                        const alamatList = this.formData.tanah_items.map(item => item.tanah_alamat).filter(Boolean);
                        this.formData.alamat_barang = alamatList.length > 0 ? alamatList.join('; ') : (first.tanah_alamat || '');
                    }
                },

                getTanahSubtotal(item) {
                    return Number(item.tanah_nilai_perencanaan || 0) + Number(item.tanah_nilai_fisik || 0) + Number(item.tanah_nilai_pengawasan || 0);
                },

                get totalNilaiMesin() {
                    if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                        return this.formData.mesin_items.reduce((sum, item) => {
                            return sum + this.getMesinSubtotal(item);
                        }, 0);
                    }
                    return (Number(this.formData.mesin_jumlah_barang || 1) * Number(this.formData.mesin_nilai_satuan || 0)) + 
                           Number(this.formData.mesin_administrasi_proyek || 0);
                },

                get totalVolumeMesin() {
                    if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                        return this.formData.mesin_items.reduce((sum, item) => {
                            return sum + (parseInt(item.mesin_jumlah_barang) || 1);
                        }, 0);
                    }
                    return parseInt(this.formData.mesin_jumlah_barang) || 1;
                },

                addMesinItem() {
                    if (!this.formData.mesin_items) {
                        this.formData.mesin_items = [];
                    }
                    this.formData.mesin_items.push({
                        mesin_nama_barang: this.formData.mesin_nama_barang || this.formData.sub_rincian_nama || '',
                        mesin_kode_barang: this.formData.mesin_kode_barang || this.formData.sub_rincian_kode || '',
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
                    this.syncMesinFieldsToMain();
                    this.syncRealisasiFromStep3();
                },

                removeMesinItem(index) {
                    if (this.formData.mesin_items && this.formData.mesin_items.length > 1) {
                        this.formData.mesin_items.splice(index, 1);
                        this.syncMesinFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }
                },

                getMesinSubtotal(item) {
                    return (Number(item.mesin_jumlah_barang || 1) * Number(item.mesin_nilai_satuan || 0)) + Number(item.mesin_administrasi_proyek || 0);
                },

                syncMesinFieldsToMain() {
                    if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                        const first = this.formData.mesin_items[0];
                        if (first.mesin_nama_barang && first.mesin_nama_barang.trim() !== '') {
                            this.formData.mesin_nama_barang = first.mesin_nama_barang;
                        } else if (this.formData.mesin_nama_barang) {
                            first.mesin_nama_barang = this.formData.mesin_nama_barang;
                        }
                        if (first.mesin_kode_barang && first.mesin_kode_barang.trim() !== '') {
                            this.formData.mesin_kode_barang = first.mesin_kode_barang;
                        } else if (this.formData.mesin_kode_barang) {
                            first.mesin_kode_barang = this.formData.mesin_kode_barang;
                        }
                        this.formData.mesin_merk = first.mesin_merk;
                        this.formData.mesin_type = first.mesin_type;
                        this.formData.mesin_ukuran = first.mesin_ukuran;
                        this.formData.mesin_no_pabrik = first.mesin_no_pabrik;
                        this.formData.mesin_bahan = first.mesin_bahan;
                        this.formData.mesin_no_rangka = first.mesin_no_rangka;
                        this.formData.mesin_no_mesin = first.mesin_no_mesin;
                        this.formData.mesin_no_bpkb = first.mesin_no_bpkb;
                        this.formData.mesin_no_polisi = first.mesin_no_polisi;
                        this.formData.mesin_kondisi = first.mesin_kondisi;
                        this.formData.mesin_satuan = first.mesin_satuan;
                        this.formData.ruang_pemegang = first.ruang_pemegang;
                        this.formData.ruang_pemegang_mesin = first.ruang_pemegang;

                        const totalVol = this.formData.mesin_items.reduce((sum, it) => sum + (parseInt(it.mesin_jumlah_barang) || 1), 0);
                        this.formData.mesin_jumlah_barang = totalVol;
                        this.formData.jumlah_volume = totalVol;

                        const totalAdmin = this.formData.mesin_items.reduce((sum, it) => sum + (parseFloat(it.mesin_administrasi_proyek) || 0), 0);
                        this.formData.mesin_administrasi_proyek = totalAdmin;

                        if (this.formData.mesin_items.length === 1) {
                            this.formData.mesin_nilai_satuan = first.mesin_nilai_satuan;
                            this.formData.harga_satuan = first.mesin_nilai_satuan;
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
                    this.syncMesinFieldsToMain();
                },

                get totalNilaiGedung() {
                    if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                        return this.formData.gedung_items.reduce((sum, item) => sum + this.getGedungSubtotal(item), 0);
                    }
                    return Number(this.formData.gedung_nilai_perencanaan || 0) + 
                           Number(this.formData.gedung_nilai_fisik || 0) + 
                           Number(this.formData.gedung_nilai_pengawasan || 0) + 
                           Number(this.formData.gedung_nilai_ap || this.formData.gedung_nilai_pip || 0);
                },

                get totalVolumeGedung() {
                    if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                        return this.formData.gedung_items.reduce((sum, item) => sum + (parseInt(item.gedung_jumlah_bangunan) || 1), 0);
                    }
                    return parseInt(this.formData.gedung_jumlah_bangunan) || 1;
                },

                getGedungSubtotal(item) {
                    return Number(item.gedung_nilai_perencanaan || 0) + 
                           Number(item.gedung_nilai_fisik || 0) + 
                           Number(item.gedung_nilai_pengawasan || 0) + 
                           Number(item.gedung_nilai_ap || item.gedung_nilai_pip || 0);
                },

                addGedungItem() {
                    if (!this.formData.gedung_items) {
                        this.formData.gedung_items = [];
                    }
                    this.formData.gedung_items.push({
                        gedung_nama_barang: this.formData.gedung_nama_barang || this.formData.sub_rincian_nama || '',
                        gedung_kode_barang: this.formData.gedung_kode_barang || this.formData.sub_rincian_kode || '',
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
                    this.syncGedungFieldsToMain();
                    this.syncRealisasiFromStep3();
                },

                removeGedungItem(index) {
                    if (this.formData.gedung_items && this.formData.gedung_items.length > 1) {
                        this.formData.gedung_items.splice(index, 1);
                        this.syncGedungFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }
                },

                syncGedungFieldsToMain() {
                    if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                        const first = this.formData.gedung_items[0];
                        if (first.gedung_nama_barang && first.gedung_nama_barang.trim() !== '') {
                            this.formData.gedung_nama_barang = first.gedung_nama_barang;
                        } else if (this.formData.gedung_nama_barang) {
                            first.gedung_nama_barang = this.formData.gedung_nama_barang;
                        }
                        if (first.gedung_kode_barang && first.gedung_kode_barang.trim() !== '') {
                            this.formData.gedung_kode_barang = first.gedung_kode_barang;
                        } else if (this.formData.gedung_kode_barang) {
                            first.gedung_kode_barang = this.formData.gedung_kode_barang;
                        }
                        this.formData.gedung_kondisi = first.gedung_kondisi;
                        this.formData.gedung_bertingkat = first.gedung_bertingkat;
                        this.formData.gedung_beton = first.gedung_beton;
                        this.formData.gedung_status_tanah = first.gedung_status_tanah;
                        this.formData.gedung_kode_aset_tanah = first.gedung_kode_aset_tanah;
                        this.formData.gedung_is_baru = first.gedung_is_baru;
                        this.formData.gedung_kapitalisasi_tahun_induk = first.gedung_kapitalisasi_tahun_induk;
                        this.formData.gedung_kapitalisasi_nilai_induk = first.gedung_kapitalisasi_nilai_induk;
                        this.formData.gedung_satuan = first.gedung_satuan;

                        const totalLuas = this.formData.gedung_items.reduce((sum, it) => sum + (parseFloat(it.gedung_luas_m2) || 0), 0);
                        this.formData.gedung_luas_m2 = totalLuas;

                        const totalBangunan = this.formData.gedung_items.reduce((sum, it) => sum + (parseInt(it.gedung_jumlah_bangunan) || 1), 0);
                        this.formData.gedung_jumlah_bangunan = totalBangunan;
                        this.formData.jumlah_volume = totalBangunan;

                        this.formData.gedung_nilai_perencanaan = this.formData.gedung_items.reduce((sum, it) => sum + (parseFloat(it.gedung_nilai_perencanaan) || 0), 0);
                        this.formData.gedung_nilai_fisik = this.formData.gedung_items.reduce((sum, it) => sum + (parseFloat(it.gedung_nilai_fisik) || 0), 0);
                        this.formData.gedung_nilai_pengawasan = this.formData.gedung_items.reduce((sum, it) => sum + (parseFloat(it.gedung_nilai_pengawasan) || 0), 0);
                        this.formData.gedung_nilai_ap = this.formData.gedung_items.reduce((sum, it) => sum + (parseFloat(it.gedung_nilai_ap || it.gedung_nilai_pip) || 0), 0);
                        this.formData.gedung_nilai_pip = this.formData.gedung_nilai_ap;

                        const alamatList = this.formData.gedung_items.map(it => it.gedung_alamat).filter(Boolean);
                        this.formData.alamat_barang = alamatList.length > 0 ? alamatList.join('; ') : (first.gedung_alamat || '');
                    }
                },

                get totalNilaiJaringan() {
                    if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                        return this.formData.jaringan_items.reduce((sum, item) => sum + this.getJaringanSubtotal(item), 0);
                    }
                    return Number(this.formData.jaringan_nilai_perencanaan || 0) + 
                           Number(this.formData.jaringan_nilai_fisik || 0) + 
                           Number(this.formData.jaringan_nilai_pengawasan || 0) + 
                           Number(this.formData.jaringan_nilai_ap || this.formData.jaringan_nilai_pip || 0);
                },

                get totalVolumeJaringan() {
                    if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                        return this.formData.jaringan_items.reduce((sum, item) => sum + (parseInt(item.jaringan_jumlah) || 1), 0);
                    }
                    return parseInt(this.formData.jaringan_jumlah) || 1;
                },

                getJaringanSubtotal(item) {
                    return Number(item.jaringan_nilai_perencanaan || 0) + 
                           Number(item.jaringan_nilai_fisik || 0) + 
                           Number(item.jaringan_nilai_pengawasan || 0) + 
                           Number(item.jaringan_nilai_ap || item.jaringan_nilai_pip || 0);
                },

                addJaringanItem() {
                    if (!this.formData.jaringan_items) {
                        this.formData.jaringan_items = [];
                    }
                    this.formData.jaringan_items.push({
                        jaringan_nama_barang: this.formData.jaringan_nama_barang || this.formData.sub_rincian_nama || '',
                        jaringan_kode_barang: this.formData.jaringan_kode_barang || this.formData.sub_rincian_kode || '',
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
                    this.syncJaringanFieldsToMain();
                    this.syncRealisasiFromStep3();
                },

                removeJaringanItem(index) {
                    if (this.formData.jaringan_items && this.formData.jaringan_items.length > 1) {
                        this.formData.jaringan_items.splice(index, 1);
                        this.syncJaringanFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }
                },

                syncJaringanFieldsToMain() {
                    if (this.formData.jaringan_items && this.formData.jaringan_items.length > 0) {
                        const first = this.formData.jaringan_items[0];
                        if (first.jaringan_nama_barang && first.jaringan_nama_barang.trim() !== '') {
                            this.formData.jaringan_nama_barang = first.jaringan_nama_barang;
                        } else if (this.formData.jaringan_nama_barang) {
                            first.jaringan_nama_barang = this.formData.jaringan_nama_barang;
                        }
                        if (first.jaringan_kode_barang && first.jaringan_kode_barang.trim() !== '') {
                            this.formData.jaringan_kode_barang = first.jaringan_kode_barang;
                        } else if (this.formData.jaringan_kode_barang) {
                            first.jaringan_kode_barang = this.formData.jaringan_kode_barang;
                        }
                        this.formData.jaringan_konstruksi = first.jaringan_konstruksi;
                        this.formData.jaringan_kondisi = first.jaringan_kondisi;
                        this.formData.jaringan_bertingkat = first.jaringan_bertingkat;
                        this.formData.jaringan_beton = first.jaringan_beton;
                        this.formData.jaringan_status_tanah = first.jaringan_status_tanah;
                        this.formData.jaringan_kode_aset_tanah = first.jaringan_kode_aset_tanah;
                        this.formData.jaringan_is_baru = first.jaringan_is_baru;
                        this.formData.jaringan_kapitalisasi_tahun_induk = first.jaringan_kapitalisasi_tahun_induk;
                        this.formData.jaringan_kapitalisasi_nilai_induk = first.jaringan_kapitalisasi_nilai_induk;
                        this.formData.jaringan_satuan = first.jaringan_satuan;

                        const totalPanjang = this.formData.jaringan_items.reduce((sum, it) => sum + (parseFloat(it.jaringan_panjang_m) || 0), 0);
                        this.formData.jaringan_panjang_m = totalPanjang;

                        const totalLebar = this.formData.jaringan_items.reduce((sum, it) => sum + (parseFloat(it.jaringan_lebar_m) || 0), 0);
                        this.formData.jaringan_lebar_m = totalLebar;

                        const totalLuas = this.formData.jaringan_items.reduce((sum, it) => sum + (parseFloat(it.jaringan_luas_m2) || 0), 0);
                        this.formData.jaringan_luas_m2 = totalLuas;

                        const totalRuas = this.formData.jaringan_items.reduce((sum, it) => sum + (parseInt(it.jaringan_jumlah) || 1), 0);
                        this.formData.jaringan_jumlah = totalRuas;
                        this.formData.jumlah_volume = totalRuas;

                        this.formData.jaringan_nilai_perencanaan = this.formData.jaringan_items.reduce((sum, it) => sum + (parseFloat(it.jaringan_nilai_perencanaan) || 0), 0);
                        this.formData.jaringan_nilai_fisik = this.formData.jaringan_items.reduce((sum, it) => sum + (parseFloat(it.jaringan_nilai_fisik) || 0), 0);
                        this.formData.jaringan_nilai_pengawasan = this.formData.jaringan_items.reduce((sum, it) => sum + (parseFloat(it.jaringan_nilai_pengawasan) || 0), 0);
                        this.formData.jaringan_nilai_ap = this.formData.jaringan_items.reduce((sum, it) => sum + (parseFloat(it.jaringan_nilai_ap || it.jaringan_nilai_pip) || 0), 0);
                        this.formData.jaringan_nilai_pip = this.formData.jaringan_nilai_ap;

                        const alamatList = this.formData.jaringan_items.map(it => it.jaringan_alamat).filter(Boolean);
                        this.formData.alamat_barang = alamatList.length > 0 ? alamatList.join('; ') : (first.jaringan_alamat || '');
                    }
                },

                get totalNilaiAsetLainnya() {
                    if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                        return this.formData.lainnya_items.reduce((sum, item) => sum + this.getLainnyaSubtotal(item), 0);
                    }
                    return (Number(this.formData.lainnya_jumlah_barang || 1) * Number(this.formData.lainnya_nilai_satuan || 0)) + 
                           Number(this.formData.lainnya_administrasi_proyek || 0);
                },

                get totalVolumeAsetLainnya() {
                    if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                        return this.formData.lainnya_items.reduce((sum, item) => sum + (parseInt(item.lainnya_jumlah_barang) || 1), 0);
                    }
                    return parseInt(this.formData.lainnya_jumlah_barang) || 1;
                },

                getLainnyaSubtotal(item) {
                    return (Number(item.lainnya_jumlah_barang || 1) * Number(item.lainnya_nilai_satuan || 0)) + Number(item.lainnya_administrasi_proyek || 0);
                },

                setKibESubType(type) {
                    this.formData.kib_e_sub_type = type;
                    if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                        this.formData.lainnya_items.forEach(it => {
                            it.kib_e_sub_type = type;
                        });
                    }
                    this.syncLainnyaFieldsToMain();
                    this.syncRealisasiFromStep3();
                },

                addLainnyaItem() {
                    if (!this.formData.lainnya_items) {
                        this.formData.lainnya_items = [];
                    }
                    this.formData.lainnya_items.push({
                        kib_e_sub_type: this.formData.kib_e_default_type || this.formData.kib_e_sub_type || 'buku',
                        lainnya_nama_barang: this.formData.lainnya_nama_barang || this.formData.sub_rincian_nama || '',
                        lainnya_kode_barang: this.formData.lainnya_kode_barang || this.formData.sub_rincian_kode || '',
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
                        ruang_pemegang: this.formData.ruang_pemegang || '',
                        ruang_pemegang_lainnya: this.formData.ruang_pemegang_lainnya || '',
                        lainnya_kondisi: 'Baik',
                        lainnya_jumlah_barang: 1,
                        lainnya_satuan: 'Eksemplar',
                        lainnya_nilai_satuan: 0,
                        lainnya_administrasi_proyek: 0,
                        isRuangOpen: false,
                        searchRuang: ''
                    });
                    this.syncLainnyaFieldsToMain();
                    this.syncRealisasiFromStep3();
                },

                removeLainnyaItem(index) {
                    if (this.formData.lainnya_items && this.formData.lainnya_items.length > 1) {
                        this.formData.lainnya_items.splice(index, 1);
                        this.syncLainnyaFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }
                },

                syncLainnyaFieldsToMain() {
                    if (this.formData.lainnya_items && this.formData.lainnya_items.length > 0) {
                        const first = this.formData.lainnya_items[0];
                        if (first.lainnya_nama_barang && first.lainnya_nama_barang.trim() !== '') {
                            this.formData.lainnya_nama_barang = first.lainnya_nama_barang;
                        } else if (this.formData.lainnya_nama_barang) {
                            first.lainnya_nama_barang = this.formData.lainnya_nama_barang;
                        }
                        if (first.lainnya_kode_barang && first.lainnya_kode_barang.trim() !== '') {
                            this.formData.lainnya_kode_barang = first.lainnya_kode_barang;
                        } else if (this.formData.lainnya_kode_barang) {
                            first.lainnya_kode_barang = this.formData.lainnya_kode_barang;
                        }
                        this.formData.kib_e_sub_type = first.kib_e_sub_type;
                        this.formData.lainnya_buku_judul = first.lainnya_buku_judul;
                        this.formData.lainnya_buku_pencipta = first.lainnya_buku_pencipta;
                        this.formData.lainnya_buku_spesifikasi = first.lainnya_buku_spesifikasi;
                        this.formData.lainnya_kesenian_asal = first.lainnya_kesenian_asal;
                        this.formData.lainnya_kesenian_pencipta = first.lainnya_kesenian_pencipta;
                        this.formData.lainnya_kesenian_spesifikasi = first.lainnya_kesenian_spesifikasi;
                        this.formData.lainnya_kesenian_bahan = first.lainnya_kesenian_bahan;
                        this.formData.lainnya_kesenian_ukuran = first.lainnya_kesenian_ukuran;
                        this.formData.lainnya_hewan_judul = first.lainnya_hewan_judul;
                        this.formData.lainnya_hewan_jenis = first.lainnya_hewan_jenis;
                        this.formData.lainnya_hewan_spesifikasi = first.lainnya_hewan_spesifikasi;
                        this.formData.lainnya_kondisi = first.lainnya_kondisi;
                        this.formData.lainnya_satuan = first.lainnya_satuan;
                        this.formData.ruang_pemegang = first.ruang_pemegang || first.ruang_pemegang_lainnya;
                        this.formData.ruang_pemegang_lainnya = first.ruang_pemegang || first.ruang_pemegang_lainnya;

                        const totalVol = this.formData.lainnya_items.reduce((sum, it) => sum + (parseInt(it.lainnya_jumlah_barang) || 1), 0);
                        this.formData.lainnya_jumlah_barang = totalVol;
                        this.formData.jumlah_volume = totalVol;

                        const totalAdmin = this.formData.lainnya_items.reduce((sum, it) => sum + (parseFloat(it.lainnya_administrasi_proyek) || 0), 0);
                        this.formData.lainnya_administrasi_proyek = totalAdmin;

                        if (this.formData.lainnya_items.length === 1) {
                            this.formData.lainnya_nilai_satuan = first.lainnya_nilai_satuan;
                            this.formData.harga_satuan = first.lainnya_nilai_satuan;
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
                    this.syncLainnyaFieldsToMain();
                },

                get totalNilaiAtb() {
                    if (this.formData.atb_items && this.formData.atb_items.length > 0) {
                        return this.formData.atb_items.reduce((sum, item) => sum + this.getAtbSubtotal(item), 0);
                    }
                    return (Number(this.formData.atb_jumlah || 1) * Number(this.formData.atb_nilai_satuan || 0)) +
                           Number(this.formData.atb_administrasi_proyek || 0);
                },

                get totalVolumeAtb() {
                    if (this.formData.atb_items && this.formData.atb_items.length > 0) {
                        return this.formData.atb_items.reduce((sum, item) => sum + (parseInt(item.atb_jumlah) || 1), 0);
                    }
                    return parseInt(this.formData.atb_jumlah) || 1;
                },

                getAtbSubtotal(item) {
                    return (Number(item.atb_jumlah || 1) * Number(item.atb_nilai_satuan || 0)) +
                           Number(item.atb_administrasi_proyek || 0);
                },

                addAtbItem() {
                    if (!this.formData.atb_items) {
                        this.formData.atb_items = [];
                    }
                    this.formData.atb_items.push({
                        atb_nama_barang: this.formData.atb_nama_barang || this.formData.sub_rincian_nama || '',
                        atb_kode_barang: this.formData.atb_kode_barang || this.formData.sub_rincian_kode || '',
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
                    this.syncAtbFieldsToMain();
                    this.syncRealisasiFromStep3();
                },

                removeAtbItem(index) {
                    if (this.formData.atb_items && this.formData.atb_items.length > 1) {
                        this.formData.atb_items.splice(index, 1);
                        this.syncAtbFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }
                },

                syncAtbFieldsToMain() {
                    if (this.formData.atb_items && this.formData.atb_items.length > 0) {
                        const first = this.formData.atb_items[0];
                        if (first.atb_nama_barang && first.atb_nama_barang.trim() !== '') {
                            this.formData.atb_nama_barang = first.atb_nama_barang;
                        } else if (this.formData.atb_nama_barang) {
                            first.atb_nama_barang = this.formData.atb_nama_barang;
                        }
                        if (first.atb_kode_barang && first.atb_kode_barang.trim() !== '') {
                            this.formData.atb_kode_barang = first.atb_kode_barang;
                        } else if (this.formData.atb_kode_barang) {
                            first.atb_kode_barang = this.formData.atb_kode_barang;
                        }
                        this.formData.atb_judul_nama = first.atb_judul_nama;
                        this.formData.atb_judul = first.atb_judul_nama;
                        this.formData.atb_pencipta = first.atb_pencipta;
                        this.formData.atb_spesifikasi = first.atb_spesifikasi;
                        this.formData.atb_kondisi = first.atb_kondisi;
                        this.formData.atb_satuan = first.atb_satuan;
                        this.formData.ruang_pemegang_atb = first.atb_ruang_pemegang;

                        const totalJumlah = this.formData.atb_items.reduce((sum, it) => sum + (parseInt(it.atb_jumlah) || 1), 0);
                        this.formData.atb_jumlah = totalJumlah;
                        this.formData.jumlah_volume = totalJumlah;

                        this.formData.atb_nilai_satuan = first.atb_nilai_satuan;
                        this.formData.atb_administrasi_proyek = this.formData.atb_items.reduce((sum, it) => sum + (parseFloat(it.atb_administrasi_proyek) || 0), 0);
                    }
                },

                get totalNilaiKdp() {
                    if (this.formData.kdp_items && this.formData.kdp_items.length > 0) {
                        return this.formData.kdp_items.reduce((sum, item) => sum + this.getKdpSubtotal(item), 0);
                    }
                    return Number(this.formData.kdp_nilai_perencanaan || 0) + 
                           Number(this.formData.kdp_nilai_fisik || 0) + 
                           Number(this.formData.kdp_nilai_pengawasan || 0) + 
                           Number(this.formData.kdp_nilai_ap || this.formData.kdp_nilai_pip || 0);
                },

                get totalVolumeKdp() {
                    if (this.formData.kdp_items && this.formData.kdp_items.length > 0) {
                        return this.formData.kdp_items.reduce((sum, item) => sum + (parseInt(item.kdp_jumlah_bangunan) || 1), 0);
                    }
                    return parseInt(this.formData.kdp_jumlah_bangunan) || 1;
                },

                getKdpSubtotal(item) {
                    return Number(item.kdp_nilai_perencanaan || 0) + 
                           Number(item.kdp_nilai_fisik || 0) + 
                           Number(item.kdp_nilai_pengawasan || 0) + 
                           Number(item.kdp_nilai_ap || item.kdp_nilai_pip || 0);
                },

                addKdpItem() {
                    if (!this.formData.kdp_items) {
                        this.formData.kdp_items = [];
                    }
                    this.formData.kdp_items.push({
                        kdp_nama_barang: this.formData.kdp_nama_barang || this.formData.sub_rincian_nama || '',
                        kdp_kode_barang: this.formData.kdp_kode_barang || this.formData.sub_rincian_kode || '',
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
                    this.syncKdpFieldsToMain();
                    this.syncRealisasiFromStep3();
                },

                removeKdpItem(index) {
                    if (this.formData.kdp_items && this.formData.kdp_items.length > 1) {
                        this.formData.kdp_items.splice(index, 1);
                        this.syncKdpFieldsToMain();
                        this.syncRealisasiFromStep3();
                    }
                },

                syncKdpFieldsToMain() {
                    if (this.formData.kdp_items && this.formData.kdp_items.length > 0) {
                        const first = this.formData.kdp_items[0];
                        if (first.kdp_nama_barang && first.kdp_nama_barang.trim() !== '') {
                            this.formData.kdp_nama_barang = first.kdp_nama_barang;
                        } else if (this.formData.kdp_nama_barang) {
                            first.kdp_nama_barang = this.formData.kdp_nama_barang;
                        }
                        if (first.kdp_kode_barang && first.kdp_kode_barang.trim() !== '') {
                            this.formData.kdp_kode_barang = first.kdp_kode_barang;
                        } else if (this.formData.kdp_kode_barang) {
                            first.kdp_kode_barang = this.formData.kdp_kode_barang;
                        }
                        this.formData.kdp_kondisi = first.kdp_kondisi;
                        this.formData.kdp_progres_persen = first.kdp_progres_persen;
                        this.formData.kdp_bertingkat = first.kdp_bertingkat;
                        this.formData.kdp_bangunan = first.kdp_bertingkat;
                        this.formData.kdp_beton = first.kdp_beton;
                        this.formData.kdp_status_tanah = first.kdp_status_tanah;
                        this.formData.kdp_kode_aset_tanah = first.kdp_kode_aset_tanah;
                        this.formData.kdp_is_baru = first.kdp_is_baru;
                        this.formData.kdp_kapitalisasi_tahun_induk = first.kdp_kapitalisasi_tahun_induk;
                        this.formData.kdp_kapitalisasi_nilai_induk = first.kdp_kapitalisasi_nilai_induk;
                        this.formData.kdp_satuan = first.kdp_satuan;

                        const totalLuas = this.formData.kdp_items.reduce((sum, it) => sum + (parseFloat(it.kdp_luas_m2) || 0), 0);
                        this.formData.kdp_luas_m2 = totalLuas;

                        const totalBangunan = this.formData.kdp_items.reduce((sum, it) => sum + (parseInt(it.kdp_jumlah_bangunan) || 1), 0);
                        this.formData.kdp_jumlah_bangunan = totalBangunan;
                        this.formData.jumlah_volume = totalBangunan;

                        this.formData.kdp_nilai_perencanaan = this.formData.kdp_items.reduce((sum, it) => sum + (parseFloat(it.kdp_nilai_perencanaan) || 0), 0);
                        this.formData.kdp_nilai_fisik = this.formData.kdp_items.reduce((sum, it) => sum + (parseFloat(it.kdp_nilai_fisik) || 0), 0);
                        this.formData.kdp_nilai_pengawasan = this.formData.kdp_items.reduce((sum, it) => sum + (parseFloat(it.kdp_nilai_pengawasan) || 0), 0);
                        this.formData.kdp_nilai_ap = this.formData.kdp_items.reduce((sum, it) => sum + (parseFloat(it.kdp_nilai_ap || it.kdp_nilai_pip) || 0), 0);
                        this.formData.kdp_nilai_pip = this.formData.kdp_nilai_ap;

                        const alamatList = this.formData.kdp_items.map(it => it.kdp_alamat).filter(Boolean);
                        this.formData.alamat_barang = alamatList.length > 0 ? alamatList.join('; ') : (first.kdp_alamat || '');
                    }
                },

                get currentProgram() {
                    if (!this.sipdData || !this.formData.program_kode) return null;
                    return this.sipdData.find(p => p.kode === this.formData.program_kode) || null;
                },

                get availableKegiatans() {
                    if (this.currentProgram) {
                        return this.currentProgram.kegiatans || [];
                    }
                    // Jika program belum dipilih, tampilkan seluruh kegiatan
                    let allKegs = [];
                    (this.sipdData || []).forEach(p => {
                        if (p.kegiatans) allKegs = allKegs.concat(p.kegiatans);
                    });
                    return allKegs;
                },

                get currentKegiatan() {
                    if (!this.formData.kegiatan_kode) return null;
                    return this.availableKegiatans.find(k => k.kode === this.formData.kegiatan_kode) || null;
                },

                get availableSubKegiatans() {
                    if (this.currentKegiatan) {
                        return this.currentKegiatan.subKegiatans || [];
                    }
                    // Jika kegiatan belum dipilih, tampilkan seluruh sub kegiatan
                    let allSubs = [];
                    (this.availableKegiatans || []).forEach(k => {
                        if (k.subKegiatans) allSubs = allSubs.concat(k.subKegiatans);
                    });
                    return allSubs;
                },

                onProgramChange(kode) {
                    this.formData.program_kode = kode;
                    const prog = (this.sipdData || []).find(p => p.kode === kode);
                    this.formData.program_nama = prog ? prog.nama : '';
                    // Reset turunan jika program diganti
                    this.formData.kegiatan_kode = '';
                    this.formData.kegiatan_nama = '';
                    this.formData.sub_kegiatan_kode = '';
                    this.formData.sub_kegiatan_nama = '';
                },

                onKegiatanChange(kode) {
                    this.formData.kegiatan_kode = kode;
                    const keg = (this.availableKegiatans || []).find(k => k.kode === kode);
                    this.formData.kegiatan_nama = keg ? keg.nama : '';
                    // Reset turunan jika kegiatan diganti
                    this.formData.sub_kegiatan_kode = '';
                    this.formData.sub_kegiatan_nama = '';
                },

                onSubKegiatanChange(kode) {
                    this.formData.sub_kegiatan_kode = kode;
                    const sub = (this.availableSubKegiatans || []).find(s => s.kode === kode);
                    if (sub) {
                        this.formData.sub_kegiatan_nama = sub.nama;
                        this.formData.keterangan_pengadaan = sub.keterangan;
                        this.formData.jenis_pengadaan_id = sub.id || 1;
                    } else {
                        this.formData.sub_kegiatan_nama = '';
                        this.formData.keterangan_pengadaan = '';
                    }
                },

                get currentJenisAstap() {
                    if (!window.dbMasterJenisAstap108 || !this.formData.jenis_aset_kode) return null;
                    return window.dbMasterJenisAstap108.find(j => j.kode === this.formData.jenis_aset_kode) || null;
                },

                get availableSubRincian108() {
                    if (this.currentJenisAstap && this.currentJenisAstap.subRincian) {
                        return this.currentJenisAstap.subRincian;
                    }
                    let allSubRincian = [];
                    (window.dbMasterJenisAstap108 || []).forEach(j => {
                        if (j.subRincian) allSubRincian = allSubRincian.concat(j.subRincian);
                    });
                    return allSubRincian;
                },

                get currentSubRincianObj() {
                    if (!this.formData.sub_rincian_kode) return null;
                    return (this.availableSubRincian108 || []).find(s => s.kode === this.formData.sub_rincian_kode) || null;
                },

                get availableSubSubRincian108() {
                    let list = [];

                    // 1. Ambil dari currentSubRincianObj jika sudah memilih sub-rincian spesifik
                    if (this.currentSubRincianObj && this.currentSubRincianObj.subSubRincian) {
                        list = [...this.currentSubRincianObj.subSubRincian];
                    }

                    // 2. Jika belum, filter HANYA dari kelompok jenis ASTAP yang dipilih di Langkah 2 (currentJenisAstap)
                    if (list.length === 0 && this.currentJenisAstap && this.currentJenisAstap.subRincian) {
                        this.currentJenisAstap.subRincian.forEach(s => {
                            if (s.subSubRincian) list = list.concat(s.subSubRincian);
                        });
                    }

                    // 3. Fallback berdasarkan kelompok jenis aset (Tanah 1.3.1, Mesin 1.3.2, Gedung 1.3.3, Jaringan 1.3.4, Lainnya 1.3.5, ATB 1.5.3, KDP 1.3.6)
                    if (list.length === 0) {
                        const targetGroupKode = this.isTanah ? '1.3.1' : (this.isMesin ? '1.3.2' : (this.isGedung ? '1.3.3' : (this.isJaringan ? '1.3.4' : (this.isAsetLainnya ? '1.3.5' : (this.isAtb ? '1.5.3' : (this.isKdp ? '1.3.6' : ''))))));
                        const matchedGroup = (window.dbMasterJenisAstap108 || []).find(j => j.kode === targetGroupKode);
                        if (matchedGroup && matchedGroup.subRincian) {
                            matchedGroup.subRincian.forEach(s => {
                                if (s.subSubRincian) list = list.concat(s.subSubRincian);
                            });
                        }
                    }

                    // 4. Pastikan barang aktif saat ini terdaftar di list agar opsi select terisi otomatis
                    const activeKode = this.isTanah ? this.formData.tanah_kode_barang : (this.isMesin ? this.formData.mesin_kode_barang : (this.isGedung ? this.formData.gedung_kode_barang : (this.isJaringan ? this.formData.jaringan_kode_barang : (this.isAsetLainnya ? this.formData.lainnya_kode_barang : (this.isAtb ? this.formData.atb_kode_barang : (this.isKdp ? this.formData.kdp_kode_barang : ''))))));
                    const activeNama = this.isTanah ? this.formData.tanah_nama_barang : (this.isMesin ? this.formData.mesin_nama_barang : (this.isGedung ? this.formData.gedung_nama_barang : (this.isJaringan ? this.formData.jaringan_nama_barang : (this.isAsetLainnya ? this.formData.lainnya_nama_barang : (this.isAtb ? this.formData.atb_nama_barang : (this.isKdp ? this.formData.kdp_nama_barang : ''))))));

                    if (activeKode && !list.some(item => item.kode === activeKode)) {
                        list.unshift({ kode: activeKode, nama: activeNama || ('Barang Terpilih (' + activeKode + ')') });
                    }

                    return list;
                },

                searchNamaBarang108: '',
                isNamaBarang108Open: false,

                get filteredSubSubRincian108() {
                    const list = this.availableSubSubRincian108 || [];
                    if (!this.searchNamaBarang108 || this.searchNamaBarang108.trim() === '') {
                        return list;
                    }
                    const q = this.searchNamaBarang108.toLowerCase().trim();
                    return list.filter(item => 
                        (item.nama && item.nama.toLowerCase().includes(q)) || 
                        (item.kode && item.kode.toLowerCase().includes(q))
                    );
                },

                getActiveKodeBarang() {
                    if (this.isTanah) return this.formData.tanah_kode_barang;
                    if (this.isMesin) return this.formData.mesin_kode_barang;
                    if (this.isGedung) return this.formData.gedung_kode_barang;
                    if (this.isJaringan) return this.formData.jaringan_kode_barang;
                    if (this.isAsetLainnya) return this.formData.lainnya_kode_barang;
                    if (this.isAtb) return this.formData.atb_kode_barang;
                    if (this.isKdp) return this.formData.kdp_kode_barang;
                    return '';
                },

                getActiveNamaBarang() {
                    if (this.isTanah) return this.formData.tanah_nama_barang;
                    if (this.isMesin) return this.formData.mesin_nama_barang;
                    if (this.isGedung) return this.formData.gedung_nama_barang;
                    if (this.isJaringan) return this.formData.jaringan_nama_barang;
                    if (this.isAsetLainnya) return this.formData.lainnya_nama_barang;
                    if (this.isAtb) return this.formData.atb_nama_barang;
                    if (this.isKdp) return this.formData.kdp_nama_barang;
                    return '';
                },

                selectSubSubRincianItem(item) {
                    if (!item) return;
                    this.onSubSubRincianChange(item.kode);
                    this.isNamaBarang108Open = false;
                    this.searchNamaBarang108 = '';
                },

                // Getters Filter Pencarian Langkah 1 (SIPD)
                get filteredPrograms() {
                    if (!this.searchProgram || this.searchProgram.trim() === '') {
                        return this.sipdData;
                    }
                    const q = this.searchProgram.toLowerCase();
                    return this.sipdData.filter(p => 
                        (p.kode && p.kode.toLowerCase().includes(q)) || 
                        (p.nama && p.nama.toLowerCase().includes(q))
                    );
                },

                get filteredKegiatans() {
                    const list = this.availableKegiatans;
                    if (!this.searchKegiatan || this.searchKegiatan.trim() === '') {
                        return list;
                    }
                    const q = this.searchKegiatan.toLowerCase();
                    return list.filter(k => 
                        (k.kode && k.kode.toLowerCase().includes(q)) || 
                        (k.nama && k.nama.toLowerCase().includes(q))
                    );
                },

                get filteredSubKegiatans() {
                    const list = this.availableSubKegiatans;
                    if (!this.searchSubKegiatan || this.searchSubKegiatan.trim() === '') {
                        return list;
                    }
                    const q = this.searchSubKegiatan.toLowerCase();
                    return list.filter(s => 
                        (s.kode && s.kode.toLowerCase().includes(q)) || 
                        (s.nama && s.nama.toLowerCase().includes(q)) || 
                        (s.keterangan && s.keterangan.toLowerCase().includes(q))
                    );
                },

                // Getters Filter Unit & Paviliun RSUD untuk Ruang Pemegang (Mesin, Lainnya, ATB)
                get filteredUnitsMesin() {
                    let list = this.masterUnits || [];
                    if (!this.searchRuangPemegang || this.searchRuangPemegang.trim() === '') return list;
                    const q = this.searchRuangPemegang.toLowerCase().trim();
                    return list.filter(u => (u.nama || '').toLowerCase().includes(q) || (u.kode || '').toLowerCase().includes(q) || (u.tipe || '').toLowerCase().includes(q));
                },
                get filteredUnitsLainnya() {
                    let list = this.masterUnits || [];
                    if (!this.searchRuangPemegangLainnya || this.searchRuangPemegangLainnya.trim() === '') return list;
                    const q = this.searchRuangPemegangLainnya.toLowerCase().trim();
                    return list.filter(u => (u.nama || '').toLowerCase().includes(q) || (u.kode || '').toLowerCase().includes(q) || (u.tipe || '').toLowerCase().includes(q));
                },
                get filteredUnitsAtb() {
                    let list = this.masterUnits || [];
                    if (!this.searchRuangPemegangAtb || this.searchRuangPemegangAtb.trim() === '') return list;
                    const q = this.searchRuangPemegangAtb.toLowerCase().trim();
                    return list.filter(u => (u.nama || '').toLowerCase().includes(q) || (u.kode || '').toLowerCase().includes(q) || (u.tipe || '').toLowerCase().includes(q));
                },
                selectUnitMesin(unit) {
                    this.formData.ruang_pemegang = unit.nama;
                    this.isRuangPemegangOpen = false;
                },
                selectUnitLainnya(unit) {
                    this.formData.ruang_pemegang_lainnya = unit.nama;
                    this.isRuangPemegangLainnyaOpen = false;
                },
                selectUnitAtb(unit) {
                    this.formData.ruang_pemegang_atb = unit.nama;
                    this.isRuangPemegangAtbOpen = false;
                },

                // Getters Filter Pencarian Langkah 2 (Rekening Belanja & PMDN 108)
                get filteredRekeningBelanja() {
                    if (!this.searchRekening || this.searchRekening.trim() === '') {
                        return this.masterRekeningBelanja;
                    }
                    const q = this.searchRekening.toLowerCase();
                    return this.masterRekeningBelanja.filter(r => 
                        (r.kode_rek && r.kode_rek.toLowerCase().includes(q)) || 
                        (r.nama_belanja && r.nama_belanja.toLowerCase().includes(q)) ||
                        (r.kelompok && r.kelompok.toLowerCase().includes(q))
                    );
                },

                get filteredJenisAstap108() {
                    let list = (this.masterJenisAstap108 || []).filter(j => j && j.kode && j.nama && j.nama.trim() !== '');
                    // Exclude "Aset Tetap Dalam Renovasi" (1.3.5.07)
                    list = list.filter(j => 
                        !(j.kode && j.kode.includes('1.3.5.07')) && 
                        !(j.nama && j.nama.toLowerCase().includes('dalam renovasi'))
                    );
                    if (!this.searchJenis108 || this.searchJenis108.trim() === '') {
                        return list;
                    }
                    const q = this.searchJenis108.toLowerCase();
                    return list.filter(j => 
                        (j.kode && j.kode.toLowerCase().includes(q)) || 
                        (j.nama && j.nama.toLowerCase().includes(q))
                    );
                },

                get filteredSubRincian108() {
                    let list = (this.availableSubRincian108 || []).filter(s => s && s.kode && s.nama && s.nama.trim() !== '');
                    if (!this.searchSubRincian108 || this.searchSubRincian108.trim() === '') {
                        return list;
                    }
                    const q = this.searchSubRincian108.toLowerCase();
                    return list.filter(s => 
                        (s.kode && s.kode.toLowerCase().includes(q)) || 
                        (s.nama && s.nama.toLowerCase().includes(q))
                    );
                },

                // Helper Selection Pilihan Filter Card Model (Langkah 1 & Langkah 2)
                selectProgram(p) {
                    this.onProgramChange(p.kode);
                    this.isProgramOpen = false;
                    this.searchProgram = '';
                },

                selectKegiatan(k) {
                    this.onKegiatanChange(k.kode);
                    this.isKegiatanOpen = false;
                    this.searchKegiatan = '';
                },

                selectSubKegiatan(s) {
                    this.onSubKegiatanChange(s.kode);
                    this.isSubKegiatanOpen = false;
                    this.searchSubKegiatan = '';
                },

                selectRekening(r) {
                    this.onRekeningBelanjaChange(r.kode_rek);
                    this.isRekeningOpen = false;
                    this.searchRekening = '';
                },

                selectJenisAstap(j) {
                    this.onJenisAstapChange(j.kode);
                    this.isJenis108Open = false;
                    this.searchJenis108 = '';
                },

                selectSubRincian(s) {
                    this.onSubRincianChange(s.kode);
                    this.isSubRincian108Open = false;
                    this.searchSubRincian108 = '';
                },

                onRekeningBelanjaChange(kodeRek) {
                    this.formData.kode_rek = kodeRek;
                    const found = this.masterRekeningBelanja.find(r => r.kode_rek === kodeRek);
                    if (found) {
                        this.formData.nama_belanja = found.nama_belanja;
                    }
                },

                onJenisAstapChange(kodeJenis) {
                    this.formData.jenis_aset_kode = kodeJenis;
                    const found = (window.dbMasterJenisAstap108 || []).find(j => j.kode === kodeJenis);
                    if (found) {
                        this.formData.jenis_aset_nama = found.nama;
                    } else {
                        this.formData.jenis_aset_nama = '';
                    }
                    // Reset sub rincian agar pengguna memilih sendiri secara mandiri
                    this.formData.sub_rincian_kode = '';
                    this.formData.sub_rincian_nama = '';
                },

                onSubRincianChange(kodeSub) {
                    this.formData.sub_rincian_kode = kodeSub;
                    if (kodeSub && kodeSub.length >= 5) {
                        const parentJenisKode = kodeSub.substring(0, 5);
                        if (!this.formData.jenis_aset_kode || !kodeSub.startsWith(this.formData.jenis_aset_kode)) {
                            this.formData.jenis_aset_kode = parentJenisKode;
                            const foundJenis = (window.dbMasterJenisAstap108 || []).find(j => j.kode === parentJenisKode);
                            if (foundJenis) {
                                this.formData.jenis_aset_nama = foundJenis.nama;
                            }
                        }
                    }
                    const found = (this.availableSubRincian108 || []).find(s => s.kode === kodeSub);
                    if (found) {
                        this.formData.sub_rincian_nama = found.nama;
                    } else {
                        this.formData.sub_rincian_nama = '';
                    }

                    // Reset pilihan Nama Barang di Langkah 3 agar pengguna memilih sendiri secara mandiri
                    this.formData.tanah_kode_barang = '';
                    this.formData.tanah_nama_barang = '';
                    this.formData.mesin_kode_barang = '';
                    this.formData.mesin_nama_barang = '';
                    this.formData.gedung_kode_barang = '';
                    this.formData.gedung_nama_barang = '';
                    this.formData.jaringan_kode_barang = '';
                    this.formData.jaringan_nama_barang = '';
                    this.formData.lainnya_kode_barang = '';
                    this.formData.lainnya_nama_barang = '';
                    this.formData.atb_kode_barang = '';
                    this.formData.atb_nama_barang = '';
                    this.formData.kdp_kode_barang = '';
                    this.formData.kdp_nama_barang = '';

                    if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                        this.formData.mesin_items.forEach(it => {
                            it.mesin_kode_barang = '';
                            it.mesin_nama_barang = this.formData.sub_rincian_nama || '';
                        });
                    }
                    if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                        this.formData.tanah_items.forEach(it => {
                            it.tanah_kode_barang = '';
                            it.tanah_nama_barang = this.formData.sub_rincian_nama || '';
                        });
                    }
                    if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                        this.formData.gedung_items.forEach(it => {
                            it.gedung_kode_barang = '';
                            it.gedung_nama_barang = this.formData.sub_rincian_nama || '';
                        });
                    }
                },

                onSubSubRincianChange(kodeSubSub) {
                    const found = this.availableSubSubRincian108.find(s => s.kode === kodeSubSub);
                    if (this.isTanah) {
                        this.formData.tanah_kode_barang = kodeSubSub;
                        if (found) {
                            this.formData.tanah_nama_barang = found.nama;
                            if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                                this.formData.tanah_items.forEach(it => {
                                    it.tanah_kode_barang = kodeSubSub;
                                    it.tanah_nama_barang = found.nama;
                                });
                            }
                        }
                    } else if (this.isMesin) {
                        this.formData.mesin_kode_barang = kodeSubSub;
                        if (found) {
                            this.formData.mesin_nama_barang = found.nama;
                            if (this.formData.mesin_items && this.formData.mesin_items.length > 0) {
                                this.formData.mesin_items.forEach(it => {
                                    it.mesin_kode_barang = kodeSubSub;
                                    it.mesin_nama_barang = found.nama;
                                });
                            }
                        }
                    } else if (this.isGedung) {
                        this.formData.gedung_kode_barang = kodeSubSub;
                        if (found) {
                            this.formData.gedung_nama_barang = found.nama;
                            if (this.formData.gedung_items && this.formData.gedung_items.length > 0) {
                                this.formData.gedung_items.forEach(it => {
                                    it.gedung_kode_barang = kodeSubSub;
                                    it.gedung_nama_barang = found.nama;
                                });
                            }
                        }
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

                    // Otomatis terisi Sub Rincian Objek PMDN 108 & Jenis Aset jika Nama Barang di Langkah 3 dipilih
                    if (kodeSubSub) {
                        let parentSubRincian = null;
                        let parentJenis = null;

                        (window.dbMasterJenisAstap108 || []).forEach(j => {
                            if (j.subRincian) {
                                j.subRincian.forEach(sr => {
                                    if (sr.subSubRincian && sr.subSubRincian.some(ssr => ssr.kode === kodeSubSub)) {
                                        parentSubRincian = sr;
                                        parentJenis = j;
                                    }
                                });
                            }
                        });

                        if (parentSubRincian) {
                            this.formData.sub_rincian_kode = parentSubRincian.kode;
                            this.formData.sub_rincian_nama = parentSubRincian.nama;
                        }
                        if (parentJenis) {
                            this.formData.jenis_aset_kode = parentJenis.kode;
                            this.formData.jenis_aset_nama = parentJenis.nama;
                        }
                    }
                },

                nextStep() {
                    if (this.currentStep < this.totalSteps) {
                        this.goToStep(this.currentStep + 1);
                    }
                },

                prevStep() {
                    if (this.currentStep > 1) {
                        this.currentStep--;
                        this.scrollToTop();
                    }
                },

                goToStep(step) {
                    if (step > this.currentStep) {
                        for (let s = 1; s < step; s++) {
                            if (s === 1) {
                                if (!this.formData.program_kode || !this.formData.kegiatan_kode || !this.formData.sub_kegiatan_kode) {
                                    alert('⚠️ Mohon lengkapi seluruh pilihan pada Langkah 1 (Program, Kegiatan, dan Sub Kegiatan SIPD) terlebih dahulu!');
                                    this.currentStep = 1;
                                    this.scrollToTop();
                                    return;
                                }
                            }
                            if (s === 2) {
                                if (!this.formData.kode_rek || !this.formData.jenis_aset_kode) {
                                    alert('⚠️ Mohon lengkapi seluruh pilihan pada Langkah 2 (Rekening Belanja & Jenis PMDN 108) terlebih dahulu!');
                                    this.currentStep = 2;
                                    this.scrollToTop();
                                    return;
                                }
                                if (!this.formData.jumlah_anggaran || Number(this.formData.jumlah_anggaran) <= 0) {
                                    alert('⚠️ Jumlah Anggaran (Rp) (Kolom 14) wajib diisi terlebih dahulu dan tidak boleh Rp 0!');
                                    this.currentStep = 2;
                                    this.scrollToTop();
                                    return;
                                }
                            }
                            if (s === 3) {
                                this.syncRealisasiFromStep3();
                                const realisasi = Number(this.formData.jumlah_realisasi || 0);
                                const anggaran = Number(this.formData.jumlah_anggaran || 0);

                                if (realisasi <= 0) {
                                    this.toast = { 
                                        show: true, 
                                        message: '⚠️ Total Nilai Realisasi pada Langkah 3 belum diisi / masih Rp 0!', 
                                        type: 'warning' 
                                    };
                                    alert('⚠️ Total Nilai Realisasi pada Langkah 3 belum diisi atau masih bernilai Rp 0!\n\nMohon lengkapi rincian harga/nilai perolehan barang pada Langkah 3 sebelum melanjutkan ke Langkah 4.');
                                    this.currentStep = 3;
                                    this.scrollToTop();
                                    return;
                                }

                                if (this.isMesin && this.formData.is_extracomtable) {
                                    const invalidItem = (this.formData.mesin_items || []).find(it => Number(it.mesin_nilai_satuan || 0) > 300000);
                                    if (invalidItem) {
                                        this.toast = { 
                                            show: true, 
                                            message: '⚠️ Nilai satuan barang Ekstrakomtabel tidak boleh lebih dari Rp 300.000! (Ditemukan: Rp ' + this.formatRupiah(invalidItem.mesin_nilai_satuan) + ')', 
                                            type: 'warning' 
                                        };
                                        alert('⚠️ Nilai Satuan Barang Ekstrakomtabel TIDAK BOLEH lebih dari Rp 300.000!\n\nDitemukan barang dengan nilai satuan: Rp ' + this.formatRupiah(invalidItem.mesin_nilai_satuan) + '.\n\nSilakan sesuaikan harga satuan barang atau pilih kategori Peralatan & Mesin (KIB B Reguler).');
                                        this.currentStep = 3;
                                        this.scrollToTop();
                                        return;
                                    }
                                }

                                if (anggaran > 0 && realisasi > anggaran) {
                                    const selisih = realisasi - anggaran;
                                    this.toast = { 
                                        show: true, 
                                        message: '⚠️ Total Nilai Realisasi (Rp ' + this.formatRupiah(realisasi) + ') melebihi Pagu Anggaran (Rp ' + this.formatRupiah(anggaran) + ')!', 
                                        type: 'warning' 
                                    };
                                    alert('⚠️ Total Nilai Realisasi MELEBIHI Pagu Anggaran!\n\n• Pagu Anggaran: Rp ' + this.formatRupiah(anggaran) + '\n• Total Realisasi: Rp ' + this.formatRupiah(realisasi) + '\n• Selisih Kelebihan: Rp ' + this.formatRupiah(selisih) + '\n\nMohon sesuaikan rincian nilai barang pada Langkah 3 sebelum lanjut ke Langkah 4.');
                                    this.currentStep = 3;
                                    this.scrollToTop();
                                    return;
                                }
                            }
                        }
                    }
                    this.currentStep = step;
                    this.scrollToTop();
                },

                scrollToTop() {
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    document.documentElement.scrollTop = 0;
                    document.body.scrollTop = 0;
                },

                formatRupiah(val) {
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
                },

                formatDateDisplay(val) {
                    if (!val || val === '-' || val === '') return '-';
                    if (/^\d{2}\/\d{2}\/\d{4}$/.test(val)) return val;
                    const match = String(val).match(/^(\d{4})-(\d{2})-(\d{2})/);
                    if (match) {
                        return `${match[3]}/${match[2]}/${match[1]}`;
                    }
                    const d = new Date(val);
                    if (!isNaN(d.getTime())) {
                        const day = String(d.getDate()).padStart(2, '0');
                        const month = String(d.getMonth() + 1).padStart(2, '0');
                        const year = d.getFullYear();
                        return `${day}/${month}/${year}`;
                    }
                    return val;
                },

                showConfirmModal: false,
                confirmData: {
                    title: 'Konfirmasi Tindakan',
                    message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                    itemName: '',
                    type: 'success',
                    btnText: 'Ya, Lanjutkan',
                    onConfirm: null
                },

                toast: { show: false, message: '', type: 'success' },

                askConfirmation({ title, message, itemName, type = 'success', btnText, onConfirm }) {
                    this.confirmData = {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        type: type,
                        btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Simpan ASTAP')),
                        onConfirm: onConfirm
                    };
                    this.showConfirmModal = true;
                },

                executeConfirmedAction() {
                    const callback = this.confirmData.onConfirm;
                    this.showConfirmModal = false;
                    if (typeof callback === 'function') {
                        callback();
                    }
                },

                async submitForm() {
                    if (!this.formData.program_kode || !this.formData.kegiatan_kode || !this.formData.sub_kegiatan_kode) {
                        this.toast = { show: true, message: '⚠️ Mohon lengkapi pilihan pada Langkah 1 terlebih dahulu!', type: 'warning' };
                        setTimeout(() => { this.toast.show = false; }, 4000);
                        this.currentStep = 1;
                        return;
                    }
                    if (!this.formData.kode_rek || !this.formData.jenis_aset_kode) {
                        this.toast = { show: true, message: '⚠️ Mohon lengkapi pilihan pada Langkah 2 (Rekening Belanja & Jenis Aset) terlebih dahulu!', type: 'warning' };
                        setTimeout(() => { this.toast.show = false; }, 4000);
                        this.currentStep = 2;
                        return;
                    }
                    if (!this.formData.jumlah_anggaran || Number(this.formData.jumlah_anggaran) <= 0) {
                        this.toast = { show: true, message: '⚠️ Jumlah Anggaran (Rp) (Kolom 14) wajib diisi dan tidak boleh kosong!', type: 'warning' };
                        setTimeout(() => { this.toast.show = false; }, 4000);
                        this.currentStep = 2;
                        return;
                    }

                    // Sinkronisasi otomatis nilai realisasi dari rincian Nilai Barang Langkah 3
                    if (this.isTanah) this.syncTanahFieldsToMain();
                    if (this.isMesin) this.syncMesinFieldsToMain();
                    if (this.isGedung) this.syncGedungFieldsToMain();
                    this.syncRealisasiFromStep3();

                    if (!this.formData.jumlah_realisasi || Number(this.formData.jumlah_realisasi) <= 0) {
                        this.toast = { show: true, message: '⚠️ Total Nilai Barang (Realisasi) pada Langkah 3 wajib diisi dan tidak boleh Rp 0!', type: 'warning' };
                        setTimeout(() => { this.toast.show = false; }, 4000);
                        this.currentStep = 3;
                        return;
                    }
                    if (this.isMesin && this.formData.is_extracomtable) {
                        const invalidItem = (this.formData.mesin_items || []).find(it => Number(it.mesin_nilai_satuan || 0) > 300000);
                        if (invalidItem) {
                            this.toast = { 
                                show: true, 
                                message: '⚠️ Nilai satuan barang Ekstrakomtabel tidak boleh lebih dari Rp 300.000! (Ditemukan: Rp ' + this.formatRupiah(invalidItem.mesin_nilai_satuan) + ')', 
                                type: 'warning' 
                            };
                            this.currentStep = 3;
                            return;
                        }
                    }

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const astapId = '{{ $id ?? "" }}';
                    const isEdit = this.isEdit && astapId;

                    // Ambil kode 108 aktif berdasarkan jenis aset (dengan fallback ke sub rincian / jenis aset)
                    const activeKode108 = this.isTanah ? (this.formData.tanah_kode_barang || this.formData.sub_rincian_kode || this.formData.jenis_aset_kode) 
                        : (this.isMesin ? (this.formData.mesin_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isGedung ? (this.formData.gedung_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isJaringan ? (this.formData.jaringan_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isAsetLainnya ? (this.formData.lainnya_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isAtb ? (this.formData.atb_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isKdp ? (this.formData.kdp_kode_barang || this.formData.sub_rincian_kode) : ''))))));

                    if (!activeKode108) {
                        this.toast = { show: true, message: '⚠️ Mohon pilih Nama Barang (Sub-Sub Rincian PMDN 108) terlebih dahulu!', type: 'warning' };
                        setTimeout(() => { this.toast.show = false; }, 4000);
                        this.currentStep = this.isTanah ? 2 : 3;
                        return;
                    }

                    const namaBarangActive = this.isTanah ? (this.formData.tanah_nama_barang || this.formData.sub_rincian_nama || this.formData.jenis_aset_nama || 'Tanah')
                        : (this.isMesin ? (this.formData.mesin_nama_barang || this.formData.sub_rincian_nama || 'Peralatan dan Mesin')
                        : (this.isGedung ? (this.formData.gedung_nama_barang || this.formData.sub_rincian_nama || 'Gedung dan Bangunan')
                        : (this.isJaringan ? (this.formData.jaringan_nama_barang || this.formData.sub_rincian_nama || 'Jalan, Irigasi dan Jaringan')
                        : (this.isAsetLainnya ? (this.formData.lainnya_nama_barang || this.formData.sub_rincian_nama || 'Aset Tetap Lainnya')
                        : (this.isAtb ? (this.formData.atb_nama_barang || this.formData.sub_rincian_nama || 'Aset Tidak Berwujud')
                        : (this.isKdp ? (this.formData.kdp_nama_barang || this.formData.sub_rincian_nama || 'Konstruksi Dalam Pengerjaan') : 'Aset Tetap'))))));

                    const tahun = this.formData.tahun_perolehan || new Date().getFullYear();

                    const performHttpSave = () => {
                        const url = isEdit ? '/astap/' + astapId : '/astap';
                        const method = isEdit ? 'PUT' : 'POST';

                        const payload = { ...this.formData };
                        if (this.isMesin) {
                            delete payload.tanah_items;
                            delete payload.gedung_items;
                            delete payload.jaringan_items;
                        } else if (this.isTanah) {
                            delete payload.mesin_items;
                            delete payload.gedung_items;
                            delete payload.jaringan_items;
                        } else if (this.isGedung) {
                            delete payload.tanah_items;
                            delete payload.mesin_items;
                            delete payload.jaringan_items;
                        } else if (this.isJaringan) {
                            delete payload.tanah_items;
                            delete payload.mesin_items;
                            delete payload.gedung_items;
                        } else {
                            delete payload.tanah_items;
                            delete payload.mesin_items;
                            delete payload.gedung_items;
                            delete payload.jaringan_items;
                        }

                        fetch(url, {
                            method: method,
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(async res => {
                            const data = await res.json().catch(() => ({}));
                            if (!res.ok || data.success === false) {
                                throw new Error(data.message || ('Gagal memproses permintaan (Status: ' + res.status + ')'));
                            }
                            return data;
                        })
                        .then(data => {
                            this.toast = { show: true, message: '✅ ' + (data.message || 'Data ASTAP berhasil disimpan!'), type: 'success' };
                            setTimeout(() => {
                                window.location.href = '{{ route('astap.index') }}';
                            }, 1200);
                        })
                        .catch(err => {
                            console.error('Submit error:', err);
                            this.toast = { show: true, message: '❌ ' + (err.message || 'Gagal menyimpan data ASTAP ke database!'), type: 'error' };
                            alert('❌ Gagal menyimpan data ASTAP ke database: ' + (err.message || 'Terjadi kesalahan sistem'));
                        });
                    };

                    const executeSave = () => {
                        this.askConfirmation({
                            title: isEdit ? '✏️ Konfirmasi Simpan Perubahan ASTAP' : '➕ Konfirmasi Register Data ASTAP',
                            message: isEdit ? 'Apakah Anda yakin ingin menyimpan perubahan data perolehan ASTAP ini?' : 'Apakah Anda yakin ingin mendaftarkan data ASTAP lengkap ini ke database SIMAT-RK?',
                            itemName: (namaBarangActive || 'Data ASTAP') + ' (' + (activeKode108 || 'Kode 108') + ')',
                            type: isEdit ? 'warning' : 'success',
                            btnText: isEdit ? '✏️ Ya, Simpan Perubahan' : '➕ Ya, Simpan Data ASTAP',
                            onConfirm: () => {
                                performHttpSave();
                            }
                        });
                    };

                    // Cek duplikat hanya saat TAMBAH BARU (bukan edit)
                    if (!isEdit && activeKode108) {
                        try {
                            const checkRes = await fetch('/astap/check-duplicate', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ kode_108: activeKode108, tahun: tahun })
                            });
                            const checkData = await checkRes.json();

                            if (checkData.exists) {
                                const vol = parseInt(this.formData.jumlah_volume || this.formData.tanah_jumlah_bidang || 1);
                                const nibarMulai = checkData.nibar_selanjutnya;
                                const nibarAkhir = nibarMulai + vol - 1;
                                this.askConfirmation({
                                    title: '⚠️ Barang Serupa Sudah Ada dalam Database',
                                    message: `Ditemukan ${checkData.total_unit} unit serupa (${checkData.jumlah_astap} ASTAP) pada tahun ${tahun}. NIBAR akan dilanjutkan dari ...${String(nibarMulai).padStart(7, '0')} s/d ...${String(nibarAkhir).padStart(7, '0')}. Lanjutkan simpan?`,
                                    itemName: (checkData.nama_barang || namaBarangActive) + ' (' + activeKode108 + ')',
                                    type: 'warning',
                                    btnText: '➕ Ya, Lanjutkan Register',
                                    onConfirm: () => {
                                        performHttpSave();
                                    }
                                });
                                return;
                            }
                        } catch (e) {
                            console.warn('Gagal cek duplikat:', e);
                        }
                    }

                    executeSave();
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
                <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
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
                <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
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
                <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
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
                <button type="button" @click="goToStep(4)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 sm:hover:bg-transparent transition-all">
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
                    
                    <!-- Tingkat 1: PROGRAM PENGADAAN (SIPD) Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isProgramOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[10px] font-black flex items-center justify-center">1</span>
                                <span>Program Pengadaan SIPD (<span x-text="filteredPrograms.length"></span> Program Terdaftar)</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Program (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.program_kode && !isProgramOpen" 
                                    @click="isProgramOpen = true; searchProgram = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Program</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box dengan Icon Magnifying Glass -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isProgramOpen && formData.program_kode) ? (formData.program_kode + ' - ' + formData.program_nama) : searchProgram"
                                   @input="searchProgram = $event.target.value; isProgramOpen = true"
                                   @focus="isProgramOpen = true"
                                   :placeholder="formData.program_kode ? (formData.program_kode + ' - ' + formData.program_nama) : 'Ketik untuk memfilter nama / kode program (contoh: Penunjang, BLUD, Pelayanan)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.program_kode && !isProgramOpen ? 'border-purple-500/60 text-purple-200' : 'border-purple-500/40 text-white focus:border-purple-400'">
                            <svg class="w-4 h-4 text-purple-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isProgramOpen" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-purple-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="p in filteredPrograms" :key="p.kode">
                                <div @click="selectProgram(p)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="p.kode === formData.program_kode ? 'border-purple-500 bg-purple-950/40 shadow-lg' : 'border-slate-800 hover:border-purple-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-purple-300 transition-colors truncate" x-text="p.kode + ' - ' + p.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'PROGRAM SIPD • ' + (p.kegiatans ? p.kegiatans.length : 0) + ' Kegiatan Terdaftar'"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectProgram(p)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="p.kode === formData.program_kode ? 'bg-purple-500 text-slate-950 shadow-lg shadow-purple-500/30' : 'bg-purple-500/20 text-purple-300 border border-purple-500/40 hover:bg-purple-500 hover:text-slate-950'">
                                        <span x-text="p.kode === formData.program_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 2: KEGIATAN PENGADAAN (SIPD) Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isKegiatanOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center">2</span>
                                <span>Kegiatan Pengadaan SIPD (<span x-text="filteredKegiatans.length"></span> Kegiatan Terdaftar)</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Kegiatan (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.kegiatan_kode && !isKegiatanOpen" 
                                    @click="isKegiatanOpen = true; searchKegiatan = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Kegiatan</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isKegiatanOpen && formData.kegiatan_kode) ? (formData.kegiatan_kode + ' - ' + formData.kegiatan_nama) : searchKegiatan"
                                   @input="searchKegiatan = $event.target.value; isKegiatanOpen = true"
                                   @focus="isKegiatanOpen = true"
                                   :placeholder="formData.kegiatan_kode ? (formData.kegiatan_kode + ' - ' + formData.kegiatan_nama) : 'Ketik untuk memfilter nama / kode kegiatan (contoh: Peningkatan BLUD, Sarana Prasarana)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.kegiatan_kode && !isKegiatanOpen ? 'border-cyan-500/60 text-cyan-200' : 'border-cyan-500/40 text-white focus:border-cyan-400'">
                            <svg class="w-4 h-4 text-cyan-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isKegiatanOpen" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-cyan-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="k in filteredKegiatans" :key="k.kode">
                                <div @click="selectKegiatan(k)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="k.kode === formData.kegiatan_kode ? 'border-cyan-500 bg-cyan-950/40 shadow-lg' : 'border-slate-800 hover:border-cyan-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors truncate" x-text="k.kode + ' - ' + k.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'KEGIATAN SIPD • Terfilter dari Program: ' + formData.program_nama"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectKegiatan(k)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="k.kode === formData.kegiatan_kode ? 'bg-cyan-500 text-slate-950 shadow-lg shadow-cyan-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 hover:bg-cyan-500 hover:text-slate-950'">
                                        <span x-text="k.kode === formData.kegiatan_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 3: SUB KEGIATAN / JENIS PENGADAAN Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isSubKegiatanOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-black flex items-center justify-center">3</span>
                                <span>Sub Kegiatan / Jenis Pengadaan Spesifik (<span x-text="filteredSubKegiatans.length"></span> Sub Kegiatan Terdaftar)</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Sub Kegiatan (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.sub_kegiatan_kode && !isSubKegiatanOpen" 
                                    @click="isSubKegiatanOpen = true; searchSubKegiatan = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Sub Kegiatan</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isSubKegiatanOpen && formData.sub_kegiatan_kode) ? (formData.sub_kegiatan_kode + ' - ' + formData.sub_kegiatan_nama) : searchSubKegiatan"
                                   @input="searchSubKegiatan = $event.target.value; isSubKegiatanOpen = true"
                                   @focus="isSubKegiatanOpen = true"
                                   :placeholder="formData.sub_kegiatan_kode ? (formData.sub_kegiatan_kode + ' - ' + formData.sub_kegiatan_nama) : 'Ketik untuk memfilter nama / kode sub kegiatan (contoh: Pelayanan BLUD, Alat Medis)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.sub_kegiatan_kode && !isSubKegiatanOpen ? 'border-emerald-500/60 text-emerald-200' : 'border-emerald-500/40 text-white focus:border-emerald-400'">
                            <svg class="w-4 h-4 text-emerald-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isSubKegiatanOpen" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-emerald-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="s in filteredSubKegiatans" :key="s.kode">
                                <div @click="selectSubKegiatan(s)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="s.kode === formData.sub_kegiatan_kode ? 'border-emerald-500 bg-emerald-950/40 shadow-lg' : 'border-slate-800 hover:border-emerald-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors truncate" x-text="s.kode + ' - ' + s.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'SUB KEGIATAN • ' + s.keterangan"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectSubKegiatan(s)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="s.kode === formData.sub_kegiatan_kode ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500 hover:text-slate-950'">
                                        <span x-text="s.kode === formData.sub_kegiatan_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
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
                    
                    <!-- Tingkat 1: REKENING BELANJA PENGADAAN SIPD Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isRekeningOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/40 text-[10px] font-black flex items-center justify-center">1</span>
                                <span>Rekening Belanja Pengadaan SIPD</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Rekening (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.kode_rek && !isRekeningOpen" 
                                    @click="isRekeningOpen = true; searchRekening = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Rekening</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box dengan Icon Magnifying Glass -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isRekeningOpen && formData.kode_rek) ? (formData.kode_rek + ' - ' + formData.nama_belanja) : searchRekening"
                                   @input="searchRekening = $event.target.value; isRekeningOpen = true"
                                   @focus="isRekeningOpen = true"
                                   :placeholder="formData.kode_rek ? (formData.kode_rek + ' - ' + formData.nama_belanja) : 'Ketik untuk memfilter nama / kode rekening belanja (contoh: 5.2.02, Radiologi, Tanah, Gedung)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.kode_rek && !isRekeningOpen ? 'border-blue-500/60 text-blue-200' : 'border-blue-500/40 text-white focus:border-blue-400'">
                            <svg class="w-4 h-4 text-blue-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isRekeningOpen" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-blue-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="r in filteredRekeningBelanja" :key="r.kode_rek">
                                <div @click="selectRekening(r)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="r.kode_rek === formData.kode_rek ? 'border-blue-500 bg-blue-950/40 shadow-lg' : 'border-slate-800 hover:border-blue-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-blue-300 transition-colors truncate" x-text="r.kode_rek + ' - ' + r.nama_belanja"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'REKENING BELANJA • ' + (r.kelompok || 'Kode Account SIPD')"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectRekening(r)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="r.kode_rek === formData.kode_rek ? 'bg-blue-500 text-slate-950 shadow-lg shadow-blue-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/40 hover:bg-blue-500 hover:text-slate-950'">
                                        <span x-text="r.kode_rek === formData.kode_rek ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 2: JENIS ASET PMDN 108 Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isJenis108Open = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center">2</span>
                                <span>Jenis Aset PMDN 108</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Jenis Aset (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.jenis_aset_kode && !isJenis108Open" 
                                    @click="isJenis108Open = true; searchJenis108 = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Jenis Aset</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isJenis108Open && formData.jenis_aset_kode) ? (formData.jenis_aset_kode + ' - ' + formData.jenis_aset_nama) : searchJenis108"
                                   @input="searchJenis108 = $event.target.value; isJenis108Open = true"
                                   @focus="isJenis108Open = true"
                                   :placeholder="formData.jenis_aset_kode ? (formData.jenis_aset_kode + ' - ' + formData.jenis_aset_nama) : 'Ketik untuk memfilter kode / nama jenis PMDN 108 (contoh: 1.3.1, TANAH, PERALATAN, GEDUNG)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.jenis_aset_kode && !isJenis108Open ? 'border-cyan-500/60 text-cyan-200' : 'border-cyan-500/40 text-white focus:border-cyan-400'">
                            <svg class="w-4 h-4 text-cyan-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isJenis108Open" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-cyan-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="j in filteredJenisAstap108" :key="j.kode">
                                <div @click="selectJenisAstap(j)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="j.kode === formData.jenis_aset_kode ? 'border-cyan-500 bg-cyan-950/40 shadow-lg' : 'border-slate-800 hover:border-cyan-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors truncate" x-text="j.kode + ' - ' + j.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'PMDN 108 • Kode Kelompok Permendagri 108'"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectJenisAstap(j)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="j.kode === formData.jenis_aset_kode ? 'bg-cyan-500 text-slate-950 shadow-lg shadow-cyan-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 hover:bg-cyan-500 hover:text-slate-950'">
                                        <span x-text="j.kode === formData.jenis_aset_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 3: SUB RINCIAN OBJEK PMDN 108 Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isSubRincian108Open = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-black flex items-center justify-center">3</span>
                                <span>Sub Rincian Objek PMDN 108</span>
                                <span class="text-[10px] text-emerald-400 font-normal italic">(Boleh Dikosongkan)</span>
                            </label>
                            
                            <div class="flex items-center space-x-3">
                                <!-- Tombol Kosongkan Pilihan (Reset ke Kosong) -->
                                <button type="button" 
                                        x-show="formData.sub_rincian_kode" 
                                        @click="formData.sub_rincian_kode = ''; formData.sub_rincian_nama = ''; searchSubRincian108 = ''; isSubRincian108Open = false" 
                                        class="text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors flex items-center space-x-1 cursor-pointer">
                                    <span>🗑️ Kosongkan Pilihan</span>
                                </button>

                                <!-- Tombol Red ✕ Ganti Sub Rincian (Muncul bila sudah terpilih) -->
                                <button type="button" 
                                        x-show="formData.sub_rincian_kode && !isSubRincian108Open" 
                                        @click="isSubRincian108Open = true; searchSubRincian108 = ''" 
                                        class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                    <span>✕ Ganti Sub Rincian</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isSubRincian108Open && formData.sub_rincian_kode) ? (formData.sub_rincian_kode + ' - ' + formData.sub_rincian_nama) : searchSubRincian108"
                                   @input="searchSubRincian108 = $event.target.value; isSubRincian108Open = true"
                                   @focus="isSubRincian108Open = true"
                                   :placeholder="formData.sub_rincian_kode ? (formData.sub_rincian_kode + ' - ' + formData.sub_rincian_nama) : 'Ketik untuk memfilter sub rincian PMDN 108 (opsional, terisi otomatis saat memilih Nama Barang)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.sub_rincian_kode && !isSubRincian108Open ? 'border-emerald-500/60 text-emerald-200' : 'border-emerald-500/40 text-white focus:border-emerald-400'">
                            <svg class="w-4 h-4 text-emerald-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Catatan Edukatif untuk Pengguna -->
                        <p class="text-[10.5px] text-slate-400 italic">
                            💡 <strong class="text-emerald-300">Catatan:</strong> Bagian ini boleh dikosongkan jika Anda tidak hafal kode sub-rincian 108. Saat memilih <strong>Nama Barang</strong> pada nomor 4 di bawah, Sub Rincian 108 ini akan otomatis terisi.
                        </p>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isSubRincian108Open" x-transition x-cloak style="max-height: 220px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-emerald-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <!-- Opsi Kosongkan Pilihan di dalam Dropdown -->
                            <div @click="formData.sub_rincian_kode = ''; formData.sub_rincian_nama = ''; searchSubRincian108 = ''; isSubRincian108Open = false"
                                 class="p-2.5 rounded-xl bg-amber-950/40 hover:bg-amber-900/60 border border-amber-500/40 hover:border-amber-400 cursor-pointer transition-all flex items-center justify-between group">
                                <div class="min-w-0 pr-3 flex items-center space-x-2">
                                    <span class="text-xs">🗑️</span>
                                    <div>
                                        <h4 class="text-xs font-bold text-amber-300 group-hover:text-amber-200 transition-colors">Kosongkan Pilihan Sub Rincian Objek</h4>
                                        <p class="text-[10px] text-amber-400/80">Biarkan kosong, akan otomatis terisi saat memilih Nama Barang di Langkah 2 nomor 4</p>
                                    </div>
                                </div>
                                <span class="shrink-0 px-3 py-1 rounded-lg text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">Kosongkan →</span>
                            </div>

                            <template x-for="s in filteredSubRincian108" :key="s.kode">
                                <div @click="selectSubRincian(s)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="s.kode === formData.sub_rincian_kode ? 'border-emerald-500 bg-emerald-950/40 shadow-lg' : 'border-slate-800 hover:border-emerald-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors truncate" x-text="s.kode + ' - ' + s.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'SUB RINCIAN OBJEK • ' + (s.keterangan || s.kelompok || 'Kode Sub Rincian PMDN 108')"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectSubRincian(s)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="s.kode === formData.sub_rincian_kode ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500 hover:text-slate-950'">
                                        <span x-text="s.kode === formData.sub_rincian_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 4: IDENTITAS BARANG PMDN 108 Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isNamaBarang108Open = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[10px] font-black flex items-center justify-center">4</span>
                                <span>Identitas Barang PMDN 108</span>
                                <span class="text-[10px] text-purple-400 font-normal italic">(Nama & Kode Barang 108)</span>
                            </label>
                            
                            <button type="button" 
                                    x-show="getActiveKodeBarang() && !isNamaBarang108Open" 
                                    @click="isNamaBarang108Open = true; searchNamaBarang108 = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Barang</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isNamaBarang108Open && getActiveNamaBarang()) ? (getActiveKodeBarang() + ' - ' + getActiveNamaBarang()) : searchNamaBarang108"
                                   @input="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true"
                                   @focus="isNamaBarang108Open = true"
                                   :placeholder="getActiveKodeBarang() ? (getActiveKodeBarang() + ' - ' + getActiveNamaBarang()) : 'Ketik untuk memfilter nama / kode barang 108...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="getActiveKodeBarang() && !isNamaBarang108Open ? 'border-purple-500/60 text-purple-200' : 'border-purple-500/40 text-white focus:border-purple-400'">
                            <svg class="w-4 h-4 text-purple-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isNamaBarang108Open" x-transition x-cloak style="max-height: 220px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-purple-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                                <div @click="selectSubSubRincianItem(item)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="item.kode === getActiveKodeBarang() ? 'border-purple-500 bg-purple-950/40 shadow-lg' : 'border-slate-800 hover:border-purple-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-purple-300 transition-colors truncate" x-text="item.kode + ' - ' + item.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'SUB-SUB RINCIAN 108 • Kode Barang PMDN 108'"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectSubSubRincianItem(item)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="item.kode === getActiveKodeBarang() ? 'bg-purple-500 text-slate-950 shadow-lg shadow-purple-500/30' : 'bg-purple-500/20 text-purple-300 border border-purple-500/40 hover:bg-purple-500 hover:text-slate-950'">
                                        <span x-text="item.kode === getActiveKodeBarang() ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                            <template x-if="filteredSubSubRincian108.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400 italic">
                                    Tidak ada nama barang 108 yang cocok.
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Input Tahun Anggaran & Triwulan Pengadaan (SIPD) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- TAHUN ANGGARAN -->
                        <div>
                            <label class="block text-slate-300 font-semibold text-xs mb-1 flex items-center justify-between">
                                <span>📅 TAHUN ANGGARAN</span>
                                <span class="text-[10px] text-cyan-400 font-mono" x-text="'1900 - ' + maxYear"></span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       min="1900" 
                                       :max="maxYear" 
                                       x-model.number="formData.tahun_anggaran" 
                                       @input="
                                           let val = String($event.target.value || '');
                                           if (val.length > 4) {
                                               val = val.slice(0, 4);
                                               $event.target.value = val;
                                           }
                                           formData.tahun_anggaran = val ? parseInt(val, 10) : '';
                                           formData.tahun_perolehan = formData.tahun_anggaran;
                                       "
                                       @change="validateTahunAnggaran(); fetchExistingAnggaran(); syncDatesWithTriwulan();"
                                       @blur="validateTahunAnggaran(); fetchExistingAnggaran(); syncDatesWithTriwulan();"
                                       placeholder="Contoh: 2026 atau 1994 (4 Digit)"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none focus:border-cyan-500 placeholder:text-slate-500 placeholder:font-normal">
                            </div>
                        </div>

                        <!-- TRIWULAN -->
                        <div>
                            <label class="block text-cyan-400 font-semibold text-xs mb-1 flex items-center justify-between">
                                <span>📊 TRIWULAN PENGADAAN</span>
                                <span class="text-[10px] text-cyan-300/80 font-mono">TW I - IV</span>
                            </label>
                            <select x-model="formData.triwulan"
                                    @change="fetchExistingAnggaran(); syncDatesWithTriwulan();"
                                    class="w-full bg-slate-950 border border-cyan-500/50 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-cyan-400"
                                    :class="formData.triwulan ? 'text-cyan-300' : 'text-slate-500 font-normal'">
                                <option value="" disabled selected class="text-slate-500">-- Pilih Triwulan Pengadaan --</option>
                                <option value="TW I" class="text-cyan-300 bg-slate-900 font-bold">Triwulan I (TW I)</option>
                                <option value="TW II" class="text-cyan-300 bg-slate-900 font-bold">Triwulan II (TW II)</option>
                                <option value="TW III" class="text-cyan-300 bg-slate-900 font-bold">Triwulan III (TW III)</option>
                                <option value="TW IV" class="text-cyan-300 bg-slate-900 font-bold">Triwulan IV (TW IV)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Input Nilai Anggaran & Realisasi (Kolom 14 & 15) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Kolom 14: JUMLAH ANGGARAN -->
                            <div>
                                <label class="block text-slate-300 font-semibold text-xs mb-1 flex items-center justify-between">
                                    <span>JUMLAH ANGGARAN (Rp) (Kolom 14) <span class="text-rose-500 font-bold">*</span></span>
                                    <span class="text-[10px] text-slate-400">Pagu Sub Rincian</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-slate-500 text-xs font-bold">Rp</span>
                                    <input type="text" 
                                        :value="formData.jumlah_anggaran ? Number(formData.jumlah_anggaran).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            formData.jumlah_anggaran = raw ? parseInt(raw, 10) : '';
                                            $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                            isAnggaranAutoLoaded = false;"
                                        placeholder="1.000.000.000"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 pl-10 text-xs text-white font-mono font-bold focus:outline-none focus:border-blue-500">
                                </div>
                            </div>
        
                            <!-- Kolom 15: JUMLAH REALISASI -->
                            <div>
                                <label class="block text-emerald-400 font-semibold text-xs mb-1 flex items-center justify-between">
                                    <span>JUMLAH REALISASI (Rp) (Kolom 15)</span>
                                    <span class="text-[10px] text-emerald-400 font-mono">⚡ Otomatis Akumulasi</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-emerald-500 text-xs font-bold">Rp</span>
                                    <input type="text" 
                                        :value="formData.jumlah_realisasi ? Number(formData.jumlah_realisasi).toLocaleString('id-ID') : '0'"
                                        readonly
                                        placeholder="Otomatis dari Langkah 3..."
                                        class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-3.5 py-2.5 pl-10 text-xs text-emerald-400 font-mono font-extrabold focus:outline-none cursor-not-allowed">
                                </div>
                                <div class="mt-1 flex flex-wrap items-center justify-between gap-1 text-[10px]">
                                    <span class="text-emerald-400/90 font-medium">⚡ Akumulasi Realisasi TW Ini</span>
                                    <span x-show="existingRealisasiDb > 0" class="text-amber-300 font-mono font-semibold"
                                          x-text="'(Rp ' + formatRupiah(existingRealisasiDb) + ' lama + Rp ' + formatRupiah(nilaiBarangSaatIni) + ' baru)'"></span>
                                </div>
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
                                <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b border-slate-600 text-[11px]">
                                    <th colspan="8" class="py-2 border border-slate-600 tracking-wider">
                                        BELANJA MODAL
                                    </th>
                                </tr>
                                <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b-2 border-slate-700 text-[10px]">
                                    <th class="px-3 py-2 border border-slate-600">Kode Rekening</th>
                                    <th class="px-3 py-2 border border-slate-600">Nama Rekening Belanja</th>
                                    <th class="px-3 py-2 border border-slate-600">Kode 108</th>
                                    <th class="px-3 py-2 border border-slate-600">Nama Barang (Jenis 108)</th>
                                    <th class="px-3 py-2 border border-slate-600">Kode Sub Rincian</th>
                                    <th class="px-3 py-2 border border-slate-600">Sub Rincian Objek</th>
                                    <th class="px-3 py-2 border border-slate-600">Jumlah Anggaran (Rp)</th>
                                    <th class="px-3 py-2 border border-slate-600">Jumlah Realisasi (Rp)</th>
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
                        <h2 class="text-base sm:text-lg font-bold text-white tracking-tight"
                            x-text="isTanah ? 'Langkah 3: Rincian Belanja Modal Tanah Sesuai SPK / Kwitansi / Invoice' : (isMesin ? 'Langkah 3: Rincian Peralatan dan Mesin Sesuai SPK / Kwitansi / Invoice' : (isGedung ? 'Langkah 3: Rincian Belanja Gedung dan Bangunan Sesuai SPK / Invoice' : (isJaringan ? 'Langkah 3: Rincian Belanja Jalan, Irigasi dan Jaringan Sesuai SPK / Invoice' : (isAsetLainnya ? 'Langkah 3: Rincian Aset Tetap Lainnya Sesuai SPK / Invoice' : (isAtb ? 'Langkah 3: Rincian Aset Tidak Berwujud Sesuai SPK / Invoice' : (isKdp ? 'Langkah 3: Rincian Konstruksi Dalam Pengerjaan Sesuai SPK / MC' : 'Langkah 3: Dokumen Pengadaan & Bukti Transaksi'))))))"></h2>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- DASHBOARD INFO: JUMLAH ANGGARAN, REALISASI & PERINGATAN OVERBUDGET (L3)  -->
                <!-- ========================================================================= -->
                <div class="p-4 sm:p-5 rounded-3xl bg-slate-900/95 border transition-all shadow-2xl space-y-4"
                     :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'border-rose-500/80 bg-rose-950/20 shadow-rose-950/50' : 'border-slate-800'">
                    
                    <!-- 4 Kartu Metrik Anggaran & Realisasi Terperinci -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <!-- 1. Pagu Anggaran Triwulan -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 shadow-inner">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">1. PAGU ANGGARAN (TW)</span>
                            <div class="text-sm sm:text-base font-black font-mono text-white truncate" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></div>
                            <span class="text-[10px] text-slate-500 mt-1 block truncate" x-text="'Pagu ' + (formData.triwulan || 'TW') + ' ' + (formData.tahun_anggaran || '')"></span>
                        </div>

                        <!-- 2. Realisasi Sebelumnya (TW Ini) -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 shadow-inner">
                            <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider block mb-1">2. REALISASI SEBELUMNYA</span>
                            <div class="text-sm sm:text-base font-black font-mono text-amber-300 truncate" x-text="'Rp ' + formatRupiah(existingRealisasiDb)"></div>
                            <span class="text-[10px] text-slate-500 mt-1 block">Dari pengadaan lain di TW ini</span>
                        </div>

                        <!-- 3. Nilai Pengadaan Barang Ini (Langkah 3) -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-cyan-500/40 bg-cyan-950/10 shadow-inner">
                            <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider block mb-1">3. NILAI PENGADAAN INI</span>
                            <div class="text-sm sm:text-base font-black font-mono text-cyan-300 truncate" x-text="'Rp ' + formatRupiah(nilaiBarangSaatIni)"></div>
                            <span class="text-[10px] text-cyan-400/80 mt-1 block">⚡ Terhitung otomatis dari rincian</span>
                        </div>

                        <!-- 4. Total Akumulasi Realisasi -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border transition-colors shadow-inner"
                             :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'border-rose-500/60 bg-rose-950/30' : 'border-emerald-500/40 bg-emerald-950/10'">
                            <span class="text-[10px] font-bold uppercase tracking-wider block mb-1"
                                  :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'text-rose-400' : 'text-emerald-400'">
                                4. TOTAL AKUMULASI (TW)
                            </span>
                            <div class="text-sm sm:text-base font-black font-mono truncate"
                                 :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'text-rose-400' : 'text-emerald-300'"
                                 x-text="'Rp ' + formatRupiah(formData.jumlah_realisasi)"></div>
                            <span class="text-[10px] mt-1 block truncate"
                                  :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'text-rose-400 font-bold' : 'text-slate-400'"
                                  x-text="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? '❌ Overbudget Rp ' + formatRupiah((formData.jumlah_realisasi || 0) - (formData.jumlah_anggaran || 0)) : 'Sisa Pagu: Rp ' + formatRupiah(Math.max(0, (formData.jumlah_anggaran || 0) - (formData.jumlah_realisasi || 0)))"></span>
                        </div>
                    </div>

                    <!-- Progress Bar Persentase Penyerapan -->
                    <div class="space-y-1.5 pt-1">
                        <div class="flex items-center justify-between text-[11px] font-mono">
                            <span class="text-slate-400">Persentase Penyerapan Anggaran Triwulan:</span>
                            <span class="font-black"
                                  :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'text-rose-400' : 'text-emerald-400'"
                                  x-text="(Number(formData.jumlah_anggaran || 0) > 0 ? ((Number(formData.jumlah_realisasi || 0) / Number(formData.jumlah_anggaran || 1)) * 100).toFixed(2) : 0) + '%'"></span>
                        </div>
                        <div class="w-full bg-slate-950 rounded-full h-2.5 overflow-hidden border border-slate-800">
                            <div class="h-2.5 rounded-full transition-all duration-300"
                                 :class="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0 ? 'bg-rose-500' : 'bg-gradient-to-r from-cyan-500 via-teal-400 to-emerald-400'"
                                 :style="'width: ' + Math.min(100, Math.round(((Number(formData.jumlah_realisasi || 0)) / (Number(formData.jumlah_anggaran || 1))) * 100)) + '%'"></div>
                        </div>
                    </div>

                    <!-- ALERT BOX MERAH: MUNCUL JIKA NILAI REALISASI MELEBIHI PAGU ANGGARAN -->
                    <div x-show="Number(formData.jumlah_realisasi || 0) > Number(formData.jumlah_anggaran || 0) && Number(formData.jumlah_anggaran || 0) > 0"
                         x-cloak
                         x-transition
                         class="p-4 rounded-2xl bg-rose-950/80 border-2 border-rose-500/80 text-rose-200 text-xs flex items-start space-x-3.5 shadow-2xl">
                        <div class="w-9 h-9 rounded-xl bg-rose-500/20 text-rose-400 border border-rose-500/40 flex items-center justify-center text-lg shrink-0 font-bold">
                            ⚠️
                        </div>
                        <div class="space-y-1 min-w-0 flex-1">
                            <h4 class="font-extrabold text-white text-sm tracking-wide">PERINGATAN: Total Realisasi Melebihi Pagu Anggaran!</h4>
                            <p class="leading-relaxed text-slate-300 text-xs">
                                Total nilai barang yang diinputkan saat ini (<strong class="text-rose-300 font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_realisasi)"></strong>) telah <strong class="text-rose-400">melebihi pagu anggaran</strong> yang ditetapkan (<strong class="text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></strong>) untuk <span class="font-bold text-amber-300" x-text="(formData.triwulan || 'Triwulan Ini') + ' ' + (formData.tahun_anggaran || '')"></span>.
                            </p>
                            <div class="pt-1 flex items-center space-x-2 text-[11px]">
                                <span class="text-slate-400">Selisih Kelebihan:</span>
                                <span class="px-2 py-0.5 rounded-lg bg-rose-500/30 text-rose-300 font-mono font-black border border-rose-500/50"
                                      x-text="'Rp ' + formatRupiah((formData.jumlah_realisasi || 0) - (formData.jumlah_anggaran || 0))"></span>
                                <span class="text-slate-400 italic">Mohon koreksi kembali nominal rincian nilai barang Anda sebelum lanjut.</span>
                            </div>
                        </div>
                    </div>

                    <!-- ALERT BOX KUNING: MUNCUL JIKA NILAI REALISASI BELUM DIISI / RP 0 -->
                    <div x-show="Number(formData.jumlah_realisasi || 0) <= 0"
                         x-cloak
                         x-transition
                         class="p-4 rounded-2xl bg-amber-950/80 border-2 border-amber-500/80 text-amber-200 text-xs flex items-start space-x-3.5 shadow-2xl">
                        <div class="w-9 h-9 rounded-xl bg-amber-500/20 text-amber-400 border border-amber-500/40 flex items-center justify-center text-lg shrink-0 font-bold">
                            ⚠️
                        </div>
                        <div class="space-y-1 min-w-0 flex-1">
                            <h4 class="font-extrabold text-white text-sm tracking-wide">PERINGATAN: Nilai Realisasi Belum Diisi!</h4>
                            <p class="leading-relaxed text-slate-300 text-xs">
                                Total nilai perolehan barang saat ini masih <strong class="text-amber-300 font-mono">Rp 0</strong>. Anda wajib mengisi rincian harga/nilai perolehan barang pada Langkah 3 ini sebelum dapat melanjutkan ke <strong class="text-white">Langkah 4 (Data Rekanan & Pengesahan)</strong>.
                            </p>
                        </div>
                    </div>

                </div>

                <!-- ===================================================================== -->
                <!-- KONDISI A: JIKA MEMILIH ASET TANAH (KIB A) DI LANGKAH 2               -->
                <!-- ===================================================================== -->
                <template x-if="isTanah">
                    <div class="space-y-6">

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30">
                                        📅 Periode: <span x-text="triwulanDateLabel"></span>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Klik pada kartu atau radio button untuk memilih jenis dokumen</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- 1. SPK -->
                                <div @click="selectDocType('spk')" 
                                     :class="formData.doc_type === 'spk' ? 'border-cyan-500 bg-cyan-950/30 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_a" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-[11px] font-extrabold text-cyan-400">📄 SPK (Kontrak)</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'spk'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026" 
                                               :disabled="formData.doc_type !== 'spk'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal SPK</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.spk_tanggal" @change="onDocDateChange(formData.spk_tanggal, 'spk_tanggal')" 
                                               :disabled="formData.doc_type !== 'spk'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 2. Surat Pesanan -->
                                <div @click="selectDocType('surat_pesanan')" 
                                     :class="formData.doc_type === 'surat_pesanan' ? 'border-purple-500 bg-purple-950/30 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_a" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-[11px] font-extrabold text-purple-400">📦 Surat Pesanan</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'surat_pesanan'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Surat Pesanan</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.surat_pesanan_tanggal" @change="onDocDateChange(formData.surat_pesanan_tanggal, 'surat_pesanan_tanggal')" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 3. Kwitansi -->
                                <div @click="selectDocType('kwitansi')" 
                                     :class="formData.doc_type === 'kwitansi' ? 'border-amber-500 bg-amber-950/30 ring-1 ring-amber-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_a" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-[11px] font-extrabold text-amber-400">🧾 Kwitansi</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'kwitansi'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Kwitansi</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.kwitansi_tanggal" @change="onDocDateChange(formData.kwitansi_tanggal, 'kwitansi_tanggal')" 
                                               :disabled="formData.doc_type !== 'kwitansi'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 4. Invoice -->
                                <div @click="selectDocType('faktur')" 
                                     :class="formData.doc_type === 'faktur' ? 'border-emerald-500 bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_a" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-[11px] font-extrabold text-emerald-400">📑 Invoice / Faktur</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'faktur'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Invoice</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.faktur_tanggal" @change="onDocDateChange(formData.faktur_tanggal, 'faktur_tanggal')" 
                                               :disabled="formData.doc_type !== 'faktur'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                                <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold border border-amber-500/30">
                                    📅 Periode: <span x-text="triwulanDateLabel"></span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.sp2d_tanggal" @change="onDocDateChange(formData.sp2d_tanggal, 'sp2d_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="000.2.3.2/224/..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.bast_dokumen_tanggal" @change="onDocDateChange(formData.bast_dokumen_tanggal, 'bast_dokumen_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- ========================================================================= -->
                        <!-- PEMBUNGKUS BIDANG TANAH MULTI-ITEM (BISA TAMBAH BIDANG TANAH JAMAK)       -->
                        <!-- ========================================================================= -->
                        <div class="space-y-4">
                            
                            <!-- Header Pembungkus Bidang Tanah -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 text-sm">🌾</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            RINCIAN BIDANG TANAH (<span class="text-emerald-400" x-text="formData.tanah_items.length"></span> Bidang Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap bidang tanah memiliki rincian sertifikat, luas, kondisi, nilai perolehan, dan alamat lokasi fisik masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addTanahItem()" 
                                        class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-emerald-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Bidang Tanah</span>
                                </button>
                            </div>

                            <!-- List Kartu Bidang Tanah (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.tanah_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 hover:border-emerald-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap Bidang Tanah -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-mono font-extrabold text-xs border border-emerald-500/40 flex items-center space-x-1.5">
                                                    <span>🌾 Bidang Tanah #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Luas: <strong class="text-cyan-300" x-text="(item.tanah_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getTanahSubtotal(item))"></strong>
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus Bidang (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.tanah_items.length > 1" 
                                                    @click="removeTanahItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Bidang Ini</span>
                                            </button>
                                        </div>

                                        <!-- Grid Status Sertifikat & Kondisi/Luas -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- Status Tanah & Sertifikat (Tanpa Penomoran 3) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📜 Status Tanah & Sertifikat:</span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[11px] mb-1 font-semibold">Hak Tanah</label>
                                                    <select x-model="item.tanah_hak" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                        <option value="Hak Pakai">Hak Pakai</option>
                                                        <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                                    </select>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Nomor</label>
                                                        <input type="text" x-model="item.tanah_sertifikat_no" placeholder="HP-108/1984"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Tanggal</label>
                                                        <input type="text" x-datepicker="{ maxDate: maxDateToday }" x-model="item.tanah_sertifikat_tgl" placeholder="dd/mm/yyyy"
                                                               @change="if(item.tanah_sertifikat_tgl > maxDateToday) item.tanah_sertifikat_tgl = maxDateToday"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-amber-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kondisi, Penggunaan & Volume (Tanpa Penomoran 4) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📐 Kondisi, Penggunaan & Volume:</span>
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Kondisi (B/KB/RB)</label>
                                                        <select x-model="item.tanah_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                                            <option value="Baik">Baik (B)</option>
                                                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Jumlah Bidang</label>
                                                        <input type="number" min="1" x-model.number="item.tanah_jumlah_bidang" 
                                                               @input="if (item.tanah_jumlah_bidang < 1) item.tanah_jumlah_bidang = 1;"
                                                               placeholder="1"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-cyan-500">
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Luas Tanah (m²)</label>
                                                        <input type="number" min="0" step="any" x-model.number="item.tanah_luas_m2" 
                                                               @input="if (item.tanah_luas_m2 < 0) item.tanah_luas_m2 = 0;"
                                                               placeholder="35400"
                                                               class="w-full bg-slate-950 border border-cyan-500/40 rounded-xl px-2.5 py-2 text-xs text-cyan-300 font-mono font-bold focus:border-cyan-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Penggunaan Lahan</label>
                                                        <input type="text" x-model="item.tanah_penggunaan" placeholder="Fasilitas RSUD"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Nilai Barang (Rp) (Tanpa Penomoran 5) -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>💰 Nilai Barang (Rp):</span>
                                                </span>
                                                <span class="text-[10px] text-slate-400">Rincian Komponen Nilai Tanah</span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Nilai Perencanaan (Rp)</label>
                                                    <input type="number" x-model.number="item.tanah_nilai_perencanaan" placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Nilai Fisik (Rp)</label>
                                                    <input type="number" x-model.number="item.tanah_nilai_fisik" placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Nilai Pengawasan (Rp)</label>
                                                    <input type="number" x-model.number="item.tanah_nilai_pengawasan" placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                            </div>
                                            <!-- Subtotal Kartu Bidang Tanah Ini -->
                                            <div class="p-2.5 rounded-xl bg-slate-950 border border-emerald-500/30 flex items-center justify-between text-xs">
                                                <span class="text-slate-400 font-medium text-[11px]">Subtotal Nilai Bidang Tanah #<span x-text="idx + 1"></span>:</span>
                                                <span class="font-extrabold font-mono text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(getTanahSubtotal(item))"></span>
                                            </div>
                                        </div>

                                        <!-- Letak / Alamat Barang (Tanpa Penomoran) -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-2">
                                            <div class="flex items-center justify-between border-b border-amber-500/20 pb-1.5">
                                                <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📍 Letak / Alamat Tanah & Aset:</span>
                                                </label>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Fisik Bidang #<span x-text="idx + 1"></span></span>
                                            </div>
                                            <input type="text" x-model="item.tanah_alamat" placeholder="Contoh: Jl. Piere Tendean No. 3, Kel. Badean, Kec. Bondowoso (Area Paviliun RSUD Dr. H. Koesnandi)"
                                                   class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-medium focus:outline-none focus:border-amber-500 transition-all">
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Bidang Tanah Baru (Besar & Jelas) -->
                            <button type="button" @click="addTanahItem()" 
                                    class="w-full py-3.5 border-2 border-dashed border-emerald-500/50 hover:border-emerald-400 bg-emerald-950/20 hover:bg-emerald-950/40 text-emerald-300 hover:text-emerald-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Bidang Tanah Lainnya</span>
                            </button>

                            <!-- Ringkasan Anggaran vs Realisasi Keseluruhan Tanah -->
                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Pagu Anggaran (Langkah 2):</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Total Realisasi Semua Pengadaan:</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_realisasi)"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                                    <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">Total Nilai Semua Bidang Tanah Ini:</span>
                                    <span class="text-base font-extrabold text-cyan-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiTanah)"></span>
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
                                <span class="text-[10px] text-emerald-400 font-mono" x-text="formData.tanah_items.length + ' Baris Bidang Terdaftar'">Format Excel KIB A RSUD (26 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1300px]">
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="25" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Letak / Alamat<br>Barang
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Status Tanah</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Kondisi<br><span class="font-normal text-[9px]">(B,KB,RB)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-32 align-middle bg-[#fde9d9]">Penggunaan</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Nilai Barang (Rp)</th>
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
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Bidang Tanah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Luas Tanah (m²)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Perencanaan (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Fisik (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Pengawasan</th>
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
                                    <!-- Body Data Live Sesuai Input Multi-Item User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(tItem, tIdx) in formData.tanah_items" :key="tIdx">
                                            <tr>
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="formData.tanah_nama_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.tanah_kode_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="tItem.tanah_hak"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(tItem.tanah_sertifikat_tgl)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="tItem.tanah_sertifikat_no"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="(tItem.tanah_kondisi === 'Baik' || tItem.tanah_kondisi === 'B') ? 'Baik' : ((tItem.tanah_kondisi === 'Kurang Baik' || tItem.tanah_kondisi === 'KB') ? 'Kurang Baik' : 'Rusak Berat')"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="tItem.tanah_penggunaan"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="tItem.tanah_jumlah_bidang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right" x-text="formatRupiah(tItem.tanah_luas_m2)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(tItem.tanah_nilai_perencanaan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(tItem.tanah_nilai_fisik)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(tItem.tanah_nilai_pengawasan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-emerald-800" x-text="formatRupiah(getTanahSubtotal(tItem))"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="tItem.tanah_alamat || formData.alamat_barang"></td>
                                            </tr>
                                        </template>
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
                        
                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30">
                                        📅 Periode: <span x-text="triwulanDateLabel"></span>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Klik pada kartu atau radio button untuk memilih jenis dokumen</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- 1. SPK -->
                                <div @click="selectDocType('spk')" 
                                     :class="formData.doc_type === 'spk' ? 'border-cyan-500 bg-cyan-950/30 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_b" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-[11px] font-extrabold text-cyan-400">📄 SPK (Kontrak)</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'spk'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026" 
                                               :disabled="formData.doc_type !== 'spk'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal SPK</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.spk_tanggal" @change="onDocDateChange(formData.spk_tanggal, 'spk_tanggal')" 
                                               :disabled="formData.doc_type !== 'spk'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 2. Surat Pesanan -->
                                <div @click="selectDocType('surat_pesanan')" 
                                     :class="formData.doc_type === 'surat_pesanan' ? 'border-purple-500 bg-purple-950/30 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_b" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-[11px] font-extrabold text-purple-400">📦 Surat Pesanan</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'surat_pesanan'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Surat Pesanan</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.surat_pesanan_tanggal" @change="onDocDateChange(formData.surat_pesanan_tanggal, 'surat_pesanan_tanggal')" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 3. Kwitansi -->
                                <div @click="selectDocType('kwitansi')" 
                                     :class="formData.doc_type === 'kwitansi' ? 'border-amber-500 bg-amber-950/30 ring-1 ring-amber-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_b" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-[11px] font-extrabold text-amber-400">🧾 Kwitansi</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'kwitansi'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Kwitansi</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.kwitansi_tanggal" @change="onDocDateChange(formData.kwitansi_tanggal, 'kwitansi_tanggal')" 
                                               :disabled="formData.doc_type !== 'kwitansi'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 4. Invoice -->
                                <div @click="selectDocType('faktur')" 
                                     :class="formData.doc_type === 'faktur' ? 'border-emerald-500 bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_b" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-[11px] font-extrabold text-emerald-400">📑 Invoice / Faktur</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'faktur'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Invoice</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.faktur_tanggal" @change="onDocDateChange(formData.faktur_tanggal, 'faktur_tanggal')" 
                                               :disabled="formData.doc_type !== 'faktur'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                                <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold border border-amber-500/30">
                                    📅 Periode: <span x-text="triwulanDateLabel"></span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="Contoh: 0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.sp2d_tanggal" @change="onDocDateChange(formData.sp2d_tanggal, 'sp2d_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="Contoh: 000.2.3.2/224/RSUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.bast_dokumen_tanggal" @change="onDocDateChange(formData.bast_dokumen_tanggal, 'bast_dokumen_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- 3. PILIHAN KATEGORI PENCATATAN: KIB B STANDAR vs EKSTRAKOMTABEL -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">3. PILIHAN KATEGORI PENCATATAN BARANG:</span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Pilih salah satu kategori pencatatan aset di bawah ini</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                <!-- Opsi 1: Peralatan & Mesin (KIB B Reguler) -->
                                <div @click="formData.is_extracomtable = false" 
                                     :class="!formData.is_extracomtable ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="kategori_extracom_choice" :checked="!formData.is_extracomtable" @change="formData.is_extracomtable = false" class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-xs font-black text-purple-300">⚙️ Peralatan & Mesin (KIB B Reguler)</span>
                                        </div>
                                        <span x-show="!formData.is_extracomtable" class="text-[9px] px-2 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-300 leading-relaxed">
                                        Aset peralatan & mesin standar kapitalisasi penuh. Mendukung spesifikasi lengkap termasuk legalitas kendaraan bermotor (<strong class="text-amber-300">No. Rangka, No. Mesin, No. BPKB, dan No. Polisi</strong>) dengan nilai perolehan bebas/standar.
                                    </p>
                                </div>

                                <!-- Opsi 2: Barang Ekstrakomtabel (Extracom) -->
                                <div @click="formData.is_extracomtable = true" 
                                     :class="formData.is_extracomtable ? 'border-cyan-500 bg-cyan-950/40 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="kategori_extracom_choice" :checked="formData.is_extracomtable" @change="formData.is_extracomtable = true" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-xs font-black text-cyan-300">📦 Barang Ekstrakomtabel (Extracom)</span>
                                        </div>
                                        <span x-show="formData.is_extracomtable" class="text-[9px] px-2 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-300 leading-relaxed">
                                        Barang non-kapitalisasi (ekstrakomtabel) dengan nilai perolehan satuan <strong class="text-amber-300">maksimal Rp 300.000 / unit</strong>. Inputan legalitas kendaraan (No. Rangka, No. Mesin, No. BPKB, No. Polisi) ditiadakan.
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- PEMBUNGKUS BARANG PERALATAN DAN MESIN / EXTRACOM MULTI-ITEM               -->
                        <!-- ========================================================================= -->
                        <div class="space-y-4">
                            
                            <!-- Header Pembungkus Peralatan dan Mesin / Extracom -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-purple-950/30 border border-purple-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-purple-500/20 text-purple-400 text-sm" x-text="formData.is_extracomtable ? '📦' : '⚙️'"></span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            <span x-text="formData.is_extracomtable ? 'RINCIAN BARANG EKSTRAKOMTABEL' : 'RINCIAN PERALATAN DAN MESIN'"></span>
                                            (<span class="text-purple-400" x-text="formData.mesin_items.length"></span> Barang / Unit Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        <span x-show="!formData.is_extracomtable">Setiap barang memiliki spesifikasi (Merk, Type, Ukuran, No. Pabrik/SN), Legalitas Kendaraan, Volume, Nilai Satuan, dan Ruang/Pemegang penempatan masing-masing.</span>
                                        <span x-show="formData.is_extracomtable">Setiap barang memiliki spesifikasi (Merk, Type, Ukuran, No. Pabrik/SN), Bahan, Kondisi, Volume, Nilai Satuan (Maks. Rp 300.000), dan Ruang/Pemegang penempatan masing-masing.</span>
                                    </p>
                                </div>
                                <button type="button" @click="addMesinItem()" 
                                        class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-purple-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Barang / Unit Baru</span>
                                </button>
                            </div>

                            <!-- List Kartu Barang Peralatan & Mesin (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.mesin_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap Barang -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-purple-500/20 text-purple-300 font-mono font-extrabold text-xs border border-purple-500/40 flex items-center space-x-1.5">
                                                    <span>⚙️ Barang / Unit #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.mesin_merk || item.mesin_type">
                                                    • <span x-text="(item.mesin_merk || '') + ' ' + (item.mesin_type || '')"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Qty: <strong class="text-cyan-300" x-text="(item.mesin_jumlah_barang || 1) + ' ' + (item.mesin_satuan || 'Unit')"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getMesinSubtotal(item))"></strong>
                                                </span>
                                                <span x-show="formData.is_extracomtable" class="text-[10px] px-2 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/30">
                                                    Ekstrakomtabel (≤ 300rb)
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus Barang (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.mesin_items.length > 1" 
                                                    @click="removeMesinItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Barang Ini</span>
                                            </button>
                                        </div>

                                        <!-- Grid Form Pengisian Spesifikasi Peralatan dan Mesin -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- 3. Spesifikasi Fisik (Merk, Type, Ukuran & Nama) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>⚙️ Merk, Type & Ukuran:</span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nama Barang (PMDN 108)</span>
                                                        <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
                                                            <span>🔒</span>
                                                            <span>Otomatis dari Langkah 2</span>
                                                        </span>
                                                    </label>
                                                    <div class="relative">
                                                        <input type="text" 
                                                               :value="item.mesin_nama_barang || formData.mesin_nama_barang || formData.sub_rincian_nama || 'Peralatan dan Mesin'"
                                                               readonly
                                                               class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                    </div>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Merk Barang</label>
                                                    <input type="text" x-model="item.mesin_merk" placeholder="Siemens / Mindray / Daikin / Dell"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Type / Model</label>
                                                        <input type="text" x-model="item.mesin_type" placeholder="SOMATOM go.Now / OptiPlex"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Ukuran / Kapasitas</label>
                                                        <input type="text" x-model="item.mesin_ukuran" placeholder="128 Slice / 2 PK / 16GB"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 4. Spesifikasi No Pabrik, Kendaraan, Bahan & Kondisi -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>🏷️ No Pabrik, Kendaraan, Bahan & Kondisi:</span>
                                                    </span>
                                                </div>

                                                <div class="grid grid-cols-2 gap-2.5">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">No Pabrik / SN</label>
                                                        <input type="text" x-model="item.mesin_no_pabrik" placeholder="SN-RAD-2026-88192"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Bahan Pembuatan</label>
                                                        <input type="text" x-model="item.mesin_bahan" placeholder="Logam & Elektronik"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500">
                                                    </div>
                                                </div>

                                                <!-- Detail Kendaraan (2x2 Grid Rapi) - Hanya tampil jika BUKAN Extracom -->
                                                <div x-show="!formData.is_extracomtable" class="p-2.5 rounded-xl bg-slate-950/60 border border-slate-800 space-y-1.5 transition-all">
                                                    <span class="text-[9.5px] font-bold text-slate-400 block uppercase tracking-wider">🚗 Legality Kendaraan (Jika Ada):</span>
                                                    <div class="grid grid-cols-2 gap-2">
                                                        <div>
                                                            <label class="block text-slate-500 text-[9px] mb-0.5">No Rangka</label>
                                                            <input type="text" x-model="item.mesin_no_rangka" placeholder="MH1JM..."
                                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-white font-mono">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-500 text-[9px] mb-0.5">No Mesin</label>
                                                            <input type="text" x-model="item.mesin_no_mesin" placeholder="JM51E..."
                                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-white font-mono">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-500 text-[9px] mb-0.5">No BPKB</label>
                                                            <input type="text" x-model="item.mesin_no_bpkb" placeholder="BPKB-88..."
                                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-white font-mono">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-500 text-[9px] mb-0.5">No POLISI / Plat</label>
                                                            <input type="text" x-model="item.mesin_no_polisi" placeholder="P 1234 WB"
                                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2 py-1.5 text-xs text-amber-300 font-mono font-bold">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Barang</label>
                                                    <select x-model="item.mesin_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                                        <option value="Baik">Baik (B)</option>
                                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- 5. Volume & Nilai Satuan Barang -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                                            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-1.5">
                                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>💰 Volume & Nilai Satuan Barang (Rp):</span>
                                                </span>
                                                <div class="flex items-center space-x-1.5 bg-emerald-950/60 border border-emerald-500/30 px-2.5 py-0.5 rounded-lg">
                                                    <span class="text-[10px] text-slate-300 font-semibold">Sub Total Item #<span x-text="idx + 1"></span>:</span>
                                                    <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + Number(getMesinSubtotal(item)).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah (Volume)</label>
                                                    <input type="number" min="1" x-model.number="item.mesin_jumlah_barang" placeholder="1"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                                    <input type="text" x-model="item.mesin_satuan" placeholder="Unit / Buah / Set"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nilai Satuan (Rp)</span>
                                                        <span x-show="formData.is_extracomtable" class="text-[9px] font-bold text-amber-400">Maks. Rp 300.000</span>
                                                    </label>
                                                    <input type="text" 
                                                           :value="item.mesin_nilai_satuan ? Number(item.mesin_nilai_satuan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.mesin_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           :class="formData.is_extracomtable && Number(item.mesin_nilai_satuan || 0) > 300000 ? 'border-rose-500 text-rose-300 focus:border-rose-400 ring-1 ring-rose-500' : 'border-slate-700 text-emerald-300 focus:border-emerald-500'"
                                                           :placeholder="formData.is_extracomtable ? 'Maks: 300.000' : '185.000.000'"
                                                           class="w-full bg-slate-950 border rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none">
                                                    <span x-show="formData.is_extracomtable && Number(item.mesin_nilai_satuan || 0) > 300000" class="text-[9px] font-bold text-rose-400 block mt-1">
                                                        ⚠️ Nilai satuan Extracom tidak boleh > Rp 300.000!
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Admin Proyek (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.mesin_administrasi_proyek ? Number(item.mesin_administrasi_proyek).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.mesin_administrasi_proyek = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div class="col-span-2 sm:col-span-1">
                                                    <label class="block text-emerald-400 text-[10px] mb-1 font-bold">Sub Total (Rp)</label>
                                                    <div class="w-full bg-slate-950/90 border border-emerald-500/50 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-black flex items-center justify-between shadow-inner">
                                                        <span class="text-emerald-500 text-[10px]">Rp</span>
                                                        <span x-text="Number(getMesinSubtotal(item)).toLocaleString('id-ID')"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 6. Ruang / Pemegang Aset -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/40 space-y-2 relative" @click.away="item.isRuangOpen = false">
                                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-1.5">
                                                <label class="block text-amber-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📍 Ruang / Unit Pemegang (Penanggung Jawab & Lokasi):</span>
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                                        <span>🏥</span>
                                                        <span>Unit & Paviliun</span>
                                                    </span>
                                                    <button type="button" 
                                                            x-show="item.ruang_pemegang" 
                                                            @click="item.ruang_pemegang = ''; item.searchRuang = ''; item.isRuangOpen = true" 
                                                            class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                                        ✕ Reset
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="relative">
                                                <input type="text" 
                                                       :value="!item.isRuangOpen ? item.ruang_pemegang : item.searchRuang"
                                                       @input="item.ruang_pemegang = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true"
                                                       @focus="item.isRuangOpen = true"
                                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 pl-9 text-xs text-white font-semibold focus:outline-none transition-all">
                                                <svg class="w-3.5 h-3.5 text-amber-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>

                                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                                            <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height: 180px;" class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                                <div class="px-2.5 py-1 bg-slate-950/80 rounded-lg text-[9.5px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between">
                                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                                    <span class="text-slate-400 font-mono text-[9px]" x-text="filterUnitsForItem(item).length + ' Unit/Ruangan'"></span>
                                                </div>
                                                <template x-for="u in filterUnitsForItem(item)" :key="u.id">
                                                    <div @click="selectUnitForItem(item, u)" class="p-2 rounded-xl bg-slate-950/50 hover:bg-amber-500/15 border border-slate-800/60 hover:border-amber-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                                        <div class="min-w-0 pr-2">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-xs font-bold text-white group-hover:text-amber-300 truncate" x-text="u.nama"></span>
                                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                                            </div>
                                                            <p class="text-[9.5px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                                        </div>
                                                        <span class="px-2 py-0.5 rounded-lg bg-slate-900 text-amber-300 border border-amber-500/30 text-[9.5px] font-bold shrink-0">Pilih →</span>
                                                    </div>
                                                </template>
                                                <template x-if="filterUnitsForItem(item).length === 0">
                                                    <div class="p-2.5 text-center text-xs text-slate-400">
                                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Barang Peralatan & Mesin Baru (Besar & Jelas Sesuai Kebutuhan) -->
                            <button type="button" @click="addMesinItem()" 
                                    class="w-full py-3.5 border-2 border-dashed border-cyan-500/50 hover:border-cyan-400 bg-cyan-950/20 hover:bg-cyan-950/40 text-cyan-300 hover:text-cyan-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm" x-text="formData.is_extracomtable ? 'Klik Disini untuk Menambah Barang Ekstrakomtabel Lainnya' : 'Klik Disini untuk Menambah Barang Peralatan & Mesin Lainnya'"></span>
                            </button>

                            <!-- Ringkasan Anggaran & Akumulasi Realisasi KIB B / Extracom -->
                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-purple-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Jumlah Anggaran (Pagu):</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-purple-400 font-semibold block uppercase tracking-wider">📦 Total Volume / Unit:</span>
                                        <span class="text-sm font-black text-purple-300 font-mono" x-text="totalVolumeMesin + ' Unit/Barang'"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Jumlah Realisasi (Akumulasi):</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                                    <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">Total Nilai Realisasi Pengadaan:</span>
                                    <span class="text-base font-extrabold text-cyan-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
                                </div>
                            </div>

                        </div>

                        <!-- ============================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL: KIB B STANDAR vs EKSTRAKOMTABEL      -->
                        <!-- ============================================================= -->
                        <div class="space-y-2 pt-2">
                            
                            <!-- 1. LIVE PREVIEW TABEL KIB B REGULER (JIKA BUKAN EXTRACOM - 31 KOLOM) -->
                            <div x-show="!formData.is_extracomtable" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>📄 Live Preview Tabel Rincian Belanja Modal Peralatan dan Mesin (Sesuai SPK/Invoice):</span>
                                    </span>
                                    <span class="text-[10px] text-amber-400 font-mono" x-text="formData.mesin_items.length + ' Baris Barang Terdaftar (KIB B 31 Kolom)'">Format Excel KIB B RSUD (31 Kolom)</span>
                                </div>

                                <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                    <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1450px]">
                                        <!-- Header Utama Pastel Senada -->
                                        <thead>
                                            <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                                <th colspan="30" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                    RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                                </th>
                                                <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                    RUANG /<br>PEMEGANG
                                                </th>
                                            </tr>
                                            <!-- Header Tingkat 1 -->
                                            <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">Merk</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">Type</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-20 align-middle bg-[#fde9d9]">Ukuran</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">No Pabrik</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">No Rangka</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">No Mesin</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">No BPKB</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">No Polisi</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">BAHAN</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Tahun Perolehan</th>
                                                <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Kondisi<br><span class="font-normal text-[9px]">(B,KB,RB)</span></th>
                                                <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                                <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">ADMINISTRASI PROYEK (Rp)</th>
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
                                                <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Barang</th>
                                                <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
                                                <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
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
                                            <template x-for="(mItem, mIdx) in formData.mesin_items" :key="mIdx">
                                                <tr>
                                                    <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="mItem.mesin_nama_barang || formData.mesin_nama_barang"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="mItem.mesin_kode_barang || formData.mesin_kode_barang"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="mItem.mesin_merk || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_type || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400" x-text="mItem.mesin_ukuran || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_no_pabrik || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_no_rangka || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_no_mesin || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_no_bpkb || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_no_polisi || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400" x-text="mItem.mesin_bahan || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="formData.tahun_perolehan || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="mItem.mesin_kondisi || 'Baik'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="mItem.mesin_jumlah_barang || 1"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="mItem.mesin_satuan || 'Unit'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(mItem.mesin_nilai_satuan)"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(mItem.mesin_administrasi_proyek)"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-emerald-800" x-text="formatRupiah(getMesinSubtotal(mItem))"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                    <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="mItem.ruang_pemegang || '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- 2. LIVE PREVIEW TABEL EKSTRAKOMTABEL (JIKA MEMILIH EXTRACOM - 27 KOLOM TANPA KENDARAAN) -->
                            <div x-show="formData.is_extracomtable" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-cyan-300 uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>📄 Live Preview Tabel Rincian Belanja Ekstrakomtabel (Sesuai SPK/Invoice):</span>
                                    </span>
                                    <span class="text-[10px] text-cyan-400 font-mono" x-text="formData.mesin_items.length + ' Baris Barang Terdaftar (Extracom 27 Kolom)'">Format Excel Ekstrakomtabel RSUD (27 Kolom)</span>
                                </div>

                                <div class="overflow-x-auto rounded-2xl border border-cyan-500/40 shadow-2xl">
                                    <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1350px]">
                                        <!-- Header Utama Pastel Senada -->
                                        <thead>
                                            <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                                <th colspan="26" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                    RINCIAN BELANJA MODAL EKSTRAKOMTABEL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                                </th>
                                                <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                    RUANG /<br>PEMEGANG
                                                </th>
                                            </tr>
                                            <!-- Header Tingkat 1 -->
                                            <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">Merk</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">Type</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-20 align-middle bg-[#fde9d9]">Ukuran</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">No Pabrik</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-24 align-middle bg-[#fde9d9]">BAHAN</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Tahun Perolehan</th>
                                                <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                                <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Kondisi<br><span class="font-normal text-[9px]">(B,KB,RB)</span></th>
                                                <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                                <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">ADMINISTRASI PROYEK (Rp)</th>
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
                                                <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Barang</th>
                                                <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
                                                <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
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
                                            <template x-for="(mItem, mIdx) in formData.mesin_items" :key="mIdx">
                                                <tr>
                                                    <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="mItem.mesin_nama_barang || formData.mesin_nama_barang"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="mItem.mesin_kode_barang || formData.mesin_kode_barang"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="mItem.mesin_merk || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_type || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400" x-text="mItem.mesin_ukuran || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="mItem.mesin_no_pabrik || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400" x-text="mItem.mesin_bahan || '-'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono" x-text="formData.tahun_perolehan || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="mItem.mesin_kondisi || 'Baik'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="mItem.mesin_jumlah_barang || 1"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="mItem.mesin_satuan || 'Unit'"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(mItem.mesin_nilai_satuan)"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(mItem.mesin_administrasi_proyek)"></td>
                                                    <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-cyan-800" x-text="formatRupiah(getMesinSubtotal(mItem))"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor || '-'"></td>
                                                    <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                    <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="mItem.ruang_pemegang || '-'"></td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>

                    </div>
                </template>

                <!-- ===================================================================== -->
                <!-- KONDISI C: JIKA MEMILIH GEDUNG DAN BANGUNAN (KIB C) DI LANGKAH 2      -->
                <!-- ===================================================================== -->
                <template x-if="isGedung">
                    <div class="space-y-6">
                        
                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30">
                                        📅 Periode: <span x-text="triwulanDateLabel"></span>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Klik pada kartu atau radio button untuk memilih jenis dokumen</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- 1. SPK -->
                                <div @click="selectDocType('spk')" 
                                     :class="formData.doc_type === 'spk' ? 'border-cyan-500 bg-cyan-950/30 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_c" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-[11px] font-extrabold text-cyan-400">📄 SPK (Kontrak)</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'spk'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026" 
                                               :disabled="formData.doc_type !== 'spk'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal SPK</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.spk_tanggal" @change="onDocDateChange(formData.spk_tanggal, 'spk_tanggal')" 
                                               :disabled="formData.doc_type !== 'spk'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 2. Surat Pesanan -->
                                <div @click="selectDocType('surat_pesanan')" 
                                     :class="formData.doc_type === 'surat_pesanan' ? 'border-purple-500 bg-purple-950/30 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_c" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-[11px] font-extrabold text-purple-400">📦 Surat Pesanan</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'surat_pesanan'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Surat Pesanan</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.surat_pesanan_tanggal" @change="onDocDateChange(formData.surat_pesanan_tanggal, 'surat_pesanan_tanggal')" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 3. Kwitansi -->
                                <div @click="selectDocType('kwitansi')" 
                                     :class="formData.doc_type === 'kwitansi' ? 'border-amber-500 bg-amber-950/30 ring-1 ring-amber-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_c" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-[11px] font-extrabold text-amber-400">🧾 Kwitansi</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'kwitansi'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Kwitansi</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.kwitansi_tanggal" @change="onDocDateChange(formData.kwitansi_tanggal, 'kwitansi_tanggal')" 
                                               :disabled="formData.doc_type !== 'kwitansi'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 4. Invoice -->
                                <div @click="selectDocType('faktur')" 
                                     :class="formData.doc_type === 'faktur' ? 'border-emerald-500 bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_c" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-[11px] font-extrabold text-emerald-400">📑 Invoice / Faktur</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'faktur'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Invoice</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.faktur_tanggal" @change="onDocDateChange(formData.faktur_tanggal, 'faktur_tanggal')" 
                                               :disabled="formData.doc_type !== 'faktur'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                                <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold border border-amber-500/30">
                                    📅 Periode: <span x-text="triwulanDateLabel"></span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.sp2d_tanggal" @change="onDocDateChange(formData.sp2d_tanggal, 'sp2d_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="000.2.3.2/224/..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.bast_dokumen_tanggal" @change="onDocDateChange(formData.bast_dokumen_tanggal, 'bast_dokumen_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- PEMBUNGKUS GEDUNG DAN BANGUNAN MULTI-ITEM (KIB C)                         -->
                        <!-- ========================================================================= -->
                        <div class="space-y-4">
                            
                            <!-- Header Pembungkus Gedung dan Bangunan -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-purple-950/30 border border-purple-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-purple-500/20 text-purple-400 text-sm">🏢</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            RINCIAN GEDUNG DAN BANGUNAN (<span class="text-purple-400" x-text="formData.gedung_items.length"></span> Gedung / Bangunan Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap gedung/bangunan memiliki Luas, Kondisi, Status Tanah, Volume, Komponen Nilai (Perencanaan, Fisik, Pengawasan, AP), dan Alamat Lokasi Fisik masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addGedungItem()" 
                                        class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-purple-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Gedung / Bangunan Baru</span>
                                </button>
                            </div>

                            <!-- List Kartu Gedung & Bangunan (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.gedung_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap Gedung -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-purple-500/20 text-purple-300 font-mono font-extrabold text-xs border border-purple-500/40 flex items-center space-x-1.5">
                                                    <span>🏢 Gedung / Bangunan #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.gedung_nama_barang">
                                                    • <span x-text="item.gedung_nama_barang"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Luas: <strong class="text-cyan-300" x-text="(item.gedung_luas_m2 || 0) + ' M²'"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Qty: <strong class="text-amber-300" x-text="(item.gedung_jumlah_bangunan || 1) + ' ' + (item.gedung_satuan || 'Gedung')"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></strong>
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus Gedung (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.gedung_items.length > 1" 
                                                    @click="removeGedungItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Gedung Ini</span>
                                            </button>
                                        </div>

                                        <!-- Grid Form Pengisian Spesifikasi Gedung dan Bangunan -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- Kondisi & Spesifikasi Bangunan (Luas, Kondisi, Bertingkat, Beton) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>🏗️ Kondisi & Spesifikasi:</span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="flex items-center justify-between mb-1">
                                                        <label class="block text-slate-400 text-[10px] font-semibold">Nama Bangunan (PMDN 108)</label>
                                                        <span class="text-[9px] text-amber-400/80 flex items-center gap-1 font-medium bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                            Terkunci (Mengikuti Langkah 2)
                                                        </span>
                                                    </div>
                                                    <input type="text" :value="item.gedung_nama_barang || formData.gedung_nama_barang || formData.sub_rincian_nama || 'Bangunan Gedung'" readonly
                                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas (M2/Lt)</label>
                                                        <input type="number" min="0" step="any" x-model.number="item.gedung_luas_m2" 
                                                               @input="if (item.gedung_luas_m2 < 0) item.gedung_luas_m2 = 0;"
                                                               placeholder="850"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi (B/KB/RB)</label>
                                                        <select x-model="item.gedung_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-amber-500">
                                                            <option value="B">B (Baik)</option>
                                                            <option value="KB">KB (Kurang Baik)</option>
                                                            <option value="RB">RB (Rusak Berat)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bertingkat / Tidak</label>
                                                        <select x-model="item.gedung_bertingkat" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                            <option value="Bertingkat">Bertingkat</option>
                                                            <option value="Tidak">Tidak Bertingkat</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Beton / Tidak</label>
                                                        <select x-model="item.gedung_beton" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                            <option value="Beton">Beton</option>
                                                            <option value="Tidak">Bukan Beton</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Jenis Bangunan & Status Tanah (KIB A) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📜 Status Tanah & Kapitalisasi:</span>
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Tanah</label>
                                                        <input type="text" x-model="item.gedung_status_tanah" placeholder="Hak Pakai RSUD"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kode Aset Tanah</label>
                                                        <input type="text" x-model="item.gedung_kode_aset_tanah" placeholder="1.3.1.01.01.02.013"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono focus:border-cyan-500">
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-3 gap-1.5">
                                                    <div>
                                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Bangunan Baru</label>
                                                        <select x-model="item.gedung_is_baru" 
                                                                @change="if (item.gedung_is_baru === 'Baru') { item.gedung_kapitalisasi_tahun_induk = ''; item.gedung_kapitalisasi_nilai_induk = 0; }"
                                                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-cyan-500">
                                                            <option value="Baru">Baru</option>
                                                            <option value="Lama">Lama</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Tahun Induk</label>
                                                        <input type="text" x-model="item.gedung_kapitalisasi_tahun_induk" 
                                                               :disabled="item.gedung_is_baru === 'Baru'"
                                                               :class="item.gedung_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700 focus:border-cyan-500 text-white'"
                                                               placeholder="2020"
                                                               class="w-full border rounded-xl px-2 py-2 text-xs font-mono transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Induk</label>
                                                        <input type="text" 
                                                               :disabled="item.gedung_is_baru === 'Baru'"
                                                               :class="item.gedung_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700 focus:border-cyan-500 text-amber-300'"
                                                               :value="item.gedung_is_baru === 'Baru' ? '' : (item.gedung_kapitalisasi_nilai_induk ? Number(item.gedung_kapitalisasi_nilai_induk).toLocaleString('id-ID') : '')"
                                                               @input="
                                                                   let raw = $event.target.value.replace(/\D/g, '');
                                                                   item.gedung_kapitalisasi_nilai_induk = raw ? parseInt(raw, 10) : 0;
                                                                   $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                               "
                                                               placeholder="3.500.000.000"
                                                               class="w-full border rounded-xl px-1.5 py-2 text-[10px] font-mono transition-all">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Volume & Nilai Satuan Bangunan (Rp) -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">💰 Volume & Rincian Nilai Bangunan (Rp):</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Bangunan</label>
                                                    <input type="number" min="1" x-model.number="item.gedung_jumlah_bangunan" 
                                                           @input="if (item.gedung_jumlah_bangunan < 1) item.gedung_jumlah_bangunan = 1;"
                                                           placeholder="1"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan Barang</label>
                                                    <input type="text" x-model="item.gedung_satuan" placeholder="Gedung / Unit / Paket / M²"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-emerald-500">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Perencanaan (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.gedung_nilai_perencanaan ? Number(item.gedung_nilai_perencanaan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.gedung_nilai_perencanaan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="75.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Fisik (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.gedung_nilai_fisik ? Number(item.gedung_nilai_fisik).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.gedung_nilai_fisik = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="1.850.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Pengawasan (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.gedung_nilai_pengawasan ? Number(item.gedung_nilai_pengawasan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.gedung_nilai_pengawasan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="50.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai AP (Rp)</label>
                                                    <input type="text" 
                                                           :value="(item.gedung_nilai_ap || item.gedung_nilai_pip) ? Number(item.gedung_nilai_ap || item.gedung_nilai_pip).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.gedung_nilai_ap = raw ? parseInt(raw, 10) : 0;
                                                               item.gedung_nilai_pip = item.gedung_nilai_ap;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="25.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                            </div>

                                            <div class="pt-1 flex items-center justify-between border-t border-slate-800">
                                                <span class="text-[10px] text-slate-400 font-semibold uppercase">Subtotal Nilai Gedung Ini:</span>
                                                <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(getGedungSubtotal(item))"></span>
                                            </div>
                                        </div>

                                        <!-- Letak / Alamat Lokasi Fisik Gedung -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/40 space-y-1.5">
                                            <div class="flex items-center justify-between border-b border-emerald-500/30 pb-1.5">
                                                <label class="block text-emerald-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📍 Letak / Alamat Lokasi Fisik Bangunan:</span>
                                                </label>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Lokasi Fisik</span>
                                            </div>
                                            <input type="text" x-model="item.gedung_alamat" placeholder="Contoh: Jl. Piere Tendean No. 3 Bondowoso (Kompleks RSUD Dr. H. Koesnandi - Blok Paviliun Melati)"
                                                   class="w-full bg-slate-950 border border-slate-700 hover:border-emerald-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500 transition-all">
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Gedung / Bangunan Baru (Besar & Jelas) -->
                            <button type="button" @click="addGedungItem()" 
                                    class="w-full py-3.5 border-2 border-dashed border-purple-500/50 hover:border-purple-400 bg-purple-950/20 hover:bg-purple-950/40 text-purple-300 hover:text-purple-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Gedung & Bangunan Lainnya</span>
                            </button>

                            <!-- Ringkasan Anggaran & Akumulasi Realisasi KIB C -->
                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-purple-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Jumlah Anggaran (Pagu):</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-purple-400 font-semibold block uppercase tracking-wider">🏢 Total Volume / Bangunan:</span>
                                        <span class="text-sm font-black text-purple-300 font-mono" x-text="totalVolumeGedung + ' Bangunan'"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Jumlah Realisasi (Akumulasi):</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiGedung)"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                                    <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">Total Nilai Realisasi Pengadaan:</span>
                                    <span class="text-base font-extrabold text-cyan-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiGedung)"></span>
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
                                <span class="text-[10px] text-purple-400 font-mono" x-text="formData.gedung_items.length + ' Gedung/Bangunan Terdaftar'">Format Excel KIB C RSUD (31 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1600px]">
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="30" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Letak / Alamat
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-20 align-middle bg-[#fde9d9]">Luas (M2/Lt)</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Kondisi / Spesifikasi</th>
                                            <th colspan="5" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Jenis Bangunan</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th colspan="4" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
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
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Bangunan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Perencanaan (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Fisik (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Pengawasan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai AP</th>
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
                                    <!-- Body Data Live Sesuai Input User (Looping gedung_items) -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(gItem, gIdx) in formData.gedung_items" :key="gIdx">
                                             <tr>
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="gItem.gedung_nama_barang || formData.gedung_nama_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="gItem.gedung_kode_barang || formData.gedung_kode_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono" x-text="gItem.gedung_luas_m2"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="gItem.gedung_kondisi"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="gItem.gedung_bertingkat"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="gItem.gedung_beton"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="gItem.gedung_status_tanah"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="gItem.gedung_kode_aset_tanah"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="gItem.gedung_is_baru === 'Baru' ? '1' : '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="gItem.gedung_is_baru === 'Baru' ? '-' : (gItem.gedung_kapitalisasi_tahun_induk || '-')"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="gItem.gedung_is_baru === 'Baru' ? '-' : (gItem.gedung_kapitalisasi_nilai_induk ? formatRupiah(gItem.gedung_kapitalisasi_nilai_induk) : '-')"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="gItem.gedung_jumlah_bangunan"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="gItem.gedung_satuan"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(gItem.gedung_nilai_perencanaan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(gItem.gedung_nilai_fisik)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(gItem.gedung_nilai_pengawasan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(gItem.gedung_nilai_ap || gItem.gedung_nilai_pip)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-emerald-800" x-text="formatRupiah(getGedungSubtotal(gItem))"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="gItem.gedung_alamat || formData.alamat_barang"></td>
                                            </tr>
                                        </template>
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

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30">
                                        📅 Periode: <span x-text="triwulanDateLabel"></span>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Klik pada kartu atau radio button untuk memilih jenis dokumen</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- 1. SPK -->
                                <div @click="selectDocType('spk')" 
                                     :class="formData.doc_type === 'spk' ? 'border-cyan-500 bg-cyan-950/30 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_d" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-[11px] font-extrabold text-cyan-400">📄 SPK (Kontrak)</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'spk'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026" 
                                               :disabled="formData.doc_type !== 'spk'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal SPK</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.spk_tanggal" @change="onDocDateChange(formData.spk_tanggal, 'spk_tanggal')" 
                                               :disabled="formData.doc_type !== 'spk'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 2. Surat Pesanan -->
                                <div @click="selectDocType('surat_pesanan')" 
                                     :class="formData.doc_type === 'surat_pesanan' ? 'border-purple-500 bg-purple-950/30 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_d" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-[11px] font-extrabold text-purple-400">📦 Surat Pesanan</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'surat_pesanan'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Surat Pesanan</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.surat_pesanan_tanggal" @change="onDocDateChange(formData.surat_pesanan_tanggal, 'surat_pesanan_tanggal')" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 3. Kwitansi -->
                                <div @click="selectDocType('kwitansi')" 
                                     :class="formData.doc_type === 'kwitansi' ? 'border-amber-500 bg-amber-950/30 ring-1 ring-amber-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_d" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-[11px] font-extrabold text-amber-400">🧾 Kwitansi</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'kwitansi'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Kwitansi</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.kwitansi_tanggal" @change="onDocDateChange(formData.kwitansi_tanggal, 'kwitansi_tanggal')" 
                                               :disabled="formData.doc_type !== 'kwitansi'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 4. Invoice -->
                                <div @click="selectDocType('faktur')" 
                                     :class="formData.doc_type === 'faktur' ? 'border-emerald-500 bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_d" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-[11px] font-extrabold text-emerald-400">📑 Invoice / Faktur</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'faktur'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Invoice</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.faktur_tanggal" @change="onDocDateChange(formData.faktur_tanggal, 'faktur_tanggal')" 
                                               :disabled="formData.doc_type !== 'faktur'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                                <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold border border-amber-500/30">
                                    📅 Periode: <span x-text="triwulanDateLabel"></span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="Contoh: 0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.sp2d_tanggal" @change="onDocDateChange(formData.sp2d_tanggal, 'sp2d_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="Contoh: 000.2.3.2/224/RSUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.bast_dokumen_tanggal" @change="onDocDateChange(formData.bast_dokumen_tanggal, 'bast_dokumen_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- ========================================================================= -->
                        <!-- PEMBUNGKUS JALAN, IRIGASI DAN JARINGAN MULTI-ITEM (KIB D)                  -->
                        <!-- ========================================================================= -->
                        <div class="space-y-4">
                            
                            <!-- Header Pembungkus Jalan, Irigasi & Jaringan -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-teal-950/30 border border-teal-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-teal-500/20 text-teal-400 text-sm">🛣️</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            RINCIAN JALAN, IRIGASI DAN JARINGAN (<span class="text-teal-400" x-text="formData.jaringan_items.length"></span> Jaringan / Ruas Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap jaringan/ruas memiliki Konstruksi (Bertingkat/Beton), Dimensi (Panjang, Lebar, Luas P × L, Kondisi), Status Tanah & Kapitalisasi, Volume, Rincian Nilai (Perencanaan, Fisik, Pengawasan, AP), dan Alamat Lokasi Fisik masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addJaringanItem()" 
                                        class="px-4 py-2 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-teal-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Jaringan / Ruas Baru</span>
                                </button>
                            </div>

                            <!-- List Kartu Jaringan & Ruas (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.jaringan_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-teal-500/30 hover:border-teal-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap Jaringan / Ruas -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-teal-500/20 text-teal-300 font-mono font-extrabold text-xs border border-teal-500/40 flex items-center space-x-1.5">
                                                    <span>🛣️ Jaringan / Ruas #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.jaringan_nama_barang">
                                                    • <span x-text="item.jaringan_nama_barang"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Dimensi: <strong class="text-cyan-300" x-text="(item.jaringan_panjang_m || 0) + ' M × ' + (item.jaringan_lebar_m || 0) + ' M (' + (item.jaringan_luas_m2 || 0) + ' M²)'"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Qty: <strong class="text-amber-300" x-text="(item.jaringan_jumlah || 1) + ' ' + (item.jaringan_satuan || 'Paket')"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getJaringanSubtotal(item))"></strong>
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus Jaringan (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.jaringan_items.length > 1" 
                                                    @click="removeJaringanItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Jaringan Ini</span>
                                            </button>
                                        </div>

                                        <!-- Grid Form Pengisian Spesifikasi Jalan & Jaringan (Seksi 1 & 2) -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- 1. Kondisi & Spesifikasi Jaringan (Panjang, Lebar, Luas P x L, Kondisi, Bertingkat, Beton) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>🏗️ Kondisi & Spesifikasi:</span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Jaringan / Ruas (Spesifik / Custom)</label>
                                                    <input type="text" x-model="item.jaringan_nama_barang" placeholder="Contoh: Jaringan Pipa Gas Oksigen Sentral Medis / Ruas Jalan Paviliun"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                </div>
                                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Panjang (M)</label>
                                                        <input type="number" min="0" step="any" x-model.number="item.jaringan_panjang_m" 
                                                               @input="
                                                                   if (item.jaringan_panjang_m < 0) item.jaringan_panjang_m = 0;
                                                                   if (item.jaringan_panjang_m !== null && item.jaringan_panjang_m !== undefined && item.jaringan_lebar_m !== null && item.jaringan_lebar_m !== undefined) { 
                                                                       item.jaringan_luas_m2 = parseFloat(((parseFloat(item.jaringan_panjang_m) || 0) * (parseFloat(item.jaringan_lebar_m) || 0)).toFixed(2)); 
                                                                   }
                                                               "
                                                               placeholder="450"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono font-bold focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Lebar (M)</label>
                                                        <input type="number" min="0" step="any" x-model.number="item.jaringan_lebar_m" 
                                                               @input="
                                                                   if (item.jaringan_lebar_m < 0) item.jaringan_lebar_m = 0;
                                                                   if (item.jaringan_panjang_m !== null && item.jaringan_panjang_m !== undefined && item.jaringan_lebar_m !== null && item.jaringan_lebar_m !== undefined) { 
                                                                       item.jaringan_luas_m2 = parseFloat(((parseFloat(item.jaringan_panjang_m) || 0) * (parseFloat(item.jaringan_lebar_m) || 0)).toFixed(2)); 
                                                                   }
                                                               "
                                                               placeholder="0"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                            <span>Luas (M2/Lt)</span>
                                                            <span class="text-[9px] text-amber-400 font-mono">(P × L)</span>
                                                        </label>
                                                        <input type="number" min="0" step="any" x-model.number="item.jaringan_luas_m2" 
                                                               @input="if (item.jaringan_luas_m2 < 0) item.jaringan_luas_m2 = 0;"
                                                               placeholder="0"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-cyan-300 font-mono font-bold focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi (B/KB/RB)</label>
                                                        <select x-model="item.jaringan_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-bold focus:border-amber-500">
                                                            <option value="B">B (Baik)</option>
                                                            <option value="KB">KB (Kurang Baik)</option>
                                                            <option value="RB">RB (Rusak Berat)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bertingkat / Tidak</label>
                                                        <select x-model="item.jaringan_bertingkat" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                            <option value="Bertingkat">Bertingkat</option>
                                                            <option value="Tidak">Tidak Bertingkat</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Beton / Tidak</label>
                                                        <select x-model="item.jaringan_beton" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                            <option value="Beton">Beton</option>
                                                            <option value="Tidak">Bukan Beton</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 2. Status Tanah & Kapitalisasi Jaringan -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📜 Status Tanah & Kapitalisasi:</span>
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Tanah</label>
                                                        <input type="text" x-model="item.jaringan_status_tanah" placeholder="Hak Pakai RSUD"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kode Aset Tanah</label>
                                                        <input type="text" x-model="item.jaringan_kode_aset_tanah" placeholder="1.3.1.01.01.02.013"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono focus:border-cyan-500">
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-3 gap-1.5">
                                                    <div>
                                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Jaringan Baru</label>
                                                        <select x-model="item.jaringan_is_baru" 
                                                                @change="if (item.jaringan_is_baru === 'Baru') { item.jaringan_kapitalisasi_tahun_induk = ''; item.jaringan_kapitalisasi_nilai_induk = 0; }"
                                                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-cyan-500">
                                                            <option value="Baru">Baru</option>
                                                            <option value="Lama">Lama</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Tahun Induk</label>
                                                        <input type="text" x-model="item.jaringan_kapitalisasi_tahun_induk" 
                                                               :disabled="item.jaringan_is_baru === 'Baru'"
                                                               :class="item.jaringan_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700 focus:border-cyan-500 text-white'"
                                                               placeholder="2021"
                                                               class="w-full border rounded-xl px-2 py-2 text-xs font-mono transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Induk</label>
                                                        <input type="text" 
                                                               :disabled="item.jaringan_is_baru === 'Baru'"
                                                               :class="item.jaringan_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700 focus:border-cyan-500 text-amber-300'"
                                                               :value="item.jaringan_is_baru === 'Baru' ? '' : (item.jaringan_kapitalisasi_nilai_induk ? Number(item.jaringan_kapitalisasi_nilai_induk).toLocaleString('id-ID') : '')"
                                                               @input="
                                                                   let raw = $event.target.value.replace(/\D/g, '');
                                                                   item.jaringan_kapitalisasi_nilai_induk = raw ? parseInt(raw, 10) : 0;
                                                                   $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                               "
                                                               placeholder="850.000.000"
                                                               class="w-full border rounded-xl px-1.5 py-2 text-[10px] font-mono transition-all">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- 3. Volume & Nilai Satuan Jaringan (Rp) -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-teal-500/30 space-y-2.5">
                                            <span class="text-xs font-bold text-teal-400 block uppercase tracking-wider">💰 Volume & Rincian Nilai Jaringan (Rp):</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Jaringan / Ruas</label>
                                                    <input type="number" min="1" x-model.number="item.jaringan_jumlah" 
                                                           @input="if (item.jaringan_jumlah < 1) item.jaringan_jumlah = 1;"
                                                           placeholder="1"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-teal-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan Barang</label>
                                                    <input type="text" x-model="item.jaringan_satuan" placeholder="Paket / Ruas / Meter / Titik / Unit"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-teal-500">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Perencanaan (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.jaringan_nilai_perencanaan ? Number(item.jaringan_nilai_perencanaan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.jaringan_nilai_perencanaan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="35.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-teal-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Fisik (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.jaringan_nilai_fisik ? Number(item.jaringan_nilai_fisik).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.jaringan_nilai_fisik = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="620.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-teal-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Pengawasan (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.jaringan_nilai_pengawasan ? Number(item.jaringan_nilai_pengawasan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.jaringan_nilai_pengawasan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="25.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-teal-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai AP (Rp)</label>
                                                    <input type="text" 
                                                           :value="(item.jaringan_nilai_ap || item.jaringan_nilai_pip) ? Number(item.jaringan_nilai_ap || item.jaringan_nilai_pip).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.jaringan_nilai_ap = raw ? parseInt(raw, 10) : 0;
                                                               item.jaringan_nilai_pip = item.jaringan_nilai_ap;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="15.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-teal-500">
                                                </div>
                                            </div>

                                            <div class="pt-1 flex items-center justify-between border-t border-slate-800">
                                                <span class="text-[10px] text-slate-400 font-semibold uppercase">Subtotal Nilai Jaringan Ini:</span>
                                                <span class="text-xs font-black text-teal-400 font-mono" x-text="'Rp ' + formatRupiah(getJaringanSubtotal(item))"></span>
                                            </div>
                                        </div>

                                        <!-- Letak / Alamat Lokasi Fisik Jaringan -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-teal-500/40 space-y-1.5">
                                            <div class="flex items-center justify-between border-b border-teal-500/30 pb-1.5">
                                                <label class="block text-teal-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📍 Letak / Alamat Lokasi Fisik Jaringan & Bangunan:</span>
                                                </label>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 font-bold">Lokasi Fisik Jaringan</span>
                                            </div>
                                            <input type="text" x-model="item.jaringan_alamat" placeholder="Contoh: Jalur Utilitas Gedung Bedah Sentral & Paviliun Teratai RSUD Dr. H. Koesnandi"
                                                   class="w-full bg-slate-950 border border-slate-700 hover:border-teal-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-teal-500 transition-all">
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Jaringan / Ruas Baru (Besar & Jelas) -->
                            <button type="button" @click="addJaringanItem()" 
                                    class="w-full py-3.5 border-2 border-dashed border-teal-500/50 hover:border-teal-400 bg-teal-950/20 hover:bg-teal-950/40 text-teal-300 hover:text-teal-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Jaringan / Ruas Jalan Lainnya</span>
                            </button>

                            <!-- Ringkasan Anggaran & Akumulasi Realisasi KIB D -->
                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-teal-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Jumlah Anggaran (Pagu):</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-teal-400 font-semibold block uppercase tracking-wider">🛣️ Total Volume / Ruas:</span>
                                        <span class="text-sm font-black text-teal-300 font-mono" x-text="totalVolumeJaringan + ' ' + (formData.jaringan_satuan || 'Paket')"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Jumlah Realisasi (Akumulasi):</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiJaringan)"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                                    <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">Total Nilai Realisasi Pengadaan:</span>
                                    <span class="text-base font-extrabold text-cyan-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiJaringan)"></span>
                                </div>
                            </div>

                        </div>

                        <!-- ============================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL KHUSUS JALAN, IRIGASI & JARINGAN     -->
                        <!-- ============================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Rincian Belanja Modal Jalan, Irigasi dan Jaringan (Sesuai SPK/Invoice):</span>
                                </span>
                                <span class="text-[10px] text-teal-400 font-mono" x-text="formData.jaringan_items.length + ' Jaringan/Ruas Terdaftar'">Format Excel KIB D RSUD (31 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1600px]">
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="30" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Letak / Alamat
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-20 align-middle bg-[#fde9d9]">Luas (M2/Lt)</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Kondisi / Spesifikasi</th>
                                            <th colspan="5" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Jenis Jaringan</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th colspan="4" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
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
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Jaringan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Perencanaan (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Fisik (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Pengawasan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai AP</th>
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
                                    <!-- Body Data Live Sesuai Input User (Looping jaringan_items) -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(jItem, jIdx) in formData.jaringan_items" :key="jIdx">
                                             <tr class="hover:bg-slate-50 transition-colors">
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="jItem.jaringan_nama_barang || formData.jaringan_nama_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="jItem.jaringan_kode_barang || formData.jaringan_kode_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono" x-text="jItem.jaringan_luas_m2"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="jItem.jaringan_kondisi"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="jItem.jaringan_bertingkat"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="jItem.jaringan_beton"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="jItem.jaringan_status_tanah"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="jItem.jaringan_kode_aset_tanah"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="jItem.jaringan_is_baru === 'Baru' ? '1' : '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="jItem.jaringan_is_baru === 'Baru' ? '-' : (jItem.jaringan_kapitalisasi_tahun_induk || '-')"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="jItem.jaringan_is_baru === 'Baru' ? '-' : (jItem.jaringan_kapitalisasi_nilai_induk ? formatRupiah(jItem.jaringan_kapitalisasi_nilai_induk) : '-')"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="jItem.jaringan_jumlah"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="jItem.jaringan_satuan"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(jItem.jaringan_nilai_perencanaan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(jItem.jaringan_nilai_fisik)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(jItem.jaringan_nilai_pengawasan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(jItem.jaringan_nilai_ap || jItem.jaringan_nilai_pip)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-teal-800" x-text="formatRupiah(getJaringanSubtotal(jItem))"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="jItem.jaringan_alamat || formData.alamat_barang"></td>
                                            </tr>
                                        </template>
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

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-orange-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-orange-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-orange-500/20 text-orange-300 font-mono font-bold border border-orange-500/30">
                                        📅 Periode: <span x-text="triwulanDateLabel"></span>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Klik pada kartu atau radio button untuk memilih jenis dokumen</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- 1. SPK -->
                                <div @click="selectDocType('spk')" 
                                     :class="formData.doc_type === 'spk' ? 'border-cyan-500 bg-cyan-950/30 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_e" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-[11px] font-extrabold text-cyan-400">📄 SPK (Kontrak)</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'spk'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026" 
                                               :disabled="formData.doc_type !== 'spk'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal SPK</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.spk_tanggal" @change="onDocDateChange(formData.spk_tanggal, 'spk_tanggal')" 
                                               :disabled="formData.doc_type !== 'spk'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 2. Surat Pesanan -->
                                <div @click="selectDocType('surat_pesanan')" 
                                     :class="formData.doc_type === 'surat_pesanan' ? 'border-purple-500 bg-purple-950/30 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_e" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-[11px] font-extrabold text-purple-400">📦 Surat Pesanan</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'surat_pesanan'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Surat Pesanan</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.surat_pesanan_tanggal" @change="onDocDateChange(formData.surat_pesanan_tanggal, 'surat_pesanan_tanggal')" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 3. Kwitansi -->
                                <div @click="selectDocType('kwitansi')" 
                                     :class="formData.doc_type === 'kwitansi' ? 'border-amber-500 bg-amber-950/30 ring-1 ring-amber-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_e" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-[11px] font-extrabold text-amber-400">🧾 Kwitansi</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'kwitansi'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Kwitansi</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.kwitansi_tanggal" @change="onDocDateChange(formData.kwitansi_tanggal, 'kwitansi_tanggal')" 
                                               :disabled="formData.doc_type !== 'kwitansi'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 4. Invoice -->
                                <div @click="selectDocType('faktur')" 
                                     :class="formData.doc_type === 'faktur' ? 'border-emerald-500 bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_e" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-[11px] font-extrabold text-emerald-400">📑 Invoice / Faktur</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'faktur'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Invoice</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.faktur_tanggal" @change="onDocDateChange(formData.faktur_tanggal, 'faktur_tanggal')" 
                                               :disabled="formData.doc_type !== 'faktur'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                                <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold border border-amber-500/30">
                                    📅 Periode: <span x-text="triwulanDateLabel"></span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="Contoh: 0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.sp2d_tanggal" @change="onDocDateChange(formData.sp2d_tanggal, 'sp2d_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="Contoh: 000.2.3.2/224/RSUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.bast_dokumen_tanggal" @change="onDocDateChange(formData.bast_dokumen_tanggal, 'bast_dokumen_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. PILIH KATEGORI ASET TETAP LAINNYA -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-orange-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-orange-400 block uppercase tracking-wider">3. PILIH KATEGORI ASET TETAP LAINNYA:</span>
                                    <span class="text-[10px] text-slate-400 font-medium">Kategori ini berlaku sebagai default untuk setiap item baru</span>
                                </div>
                                <!-- Badge kategori terpilih -->
                                <span class="text-[10px] px-2.5 py-1 rounded-full font-bold border"
                                      :class="{
                                          'bg-amber-500/20 text-amber-300 border-amber-500/40': (formData.kib_e_default_type || 'buku') === 'buku',
                                          'bg-purple-500/20 text-purple-300 border-purple-500/40': formData.kib_e_default_type === 'kesenian',
                                          'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': formData.kib_e_default_type === 'hewan_tumbuhan'
                                      }"
                                      x-text="formData.kib_e_default_type === 'kesenian' ? '🎨 Kesenian & Budaya Terpilih' : (formData.kib_e_default_type === 'hewan_tumbuhan' ? '🌿 Hewan & Tumbuhan Terpilih' : '📚 Buku Perpustakaan Terpilih')">
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                                <!-- Opsi 1: Buku Perpustakaan -->
                                <div @click="formData.kib_e_default_type = 'buku'; syncLainnyaFieldsToMain();"
                                     :class="(formData.kib_e_default_type || 'buku') === 'buku' ? 'border-amber-500 bg-amber-950/40 ring-1 ring-amber-500 shadow-lg shadow-amber-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-amber-500/50'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5">
                                            <input type="radio" name="kib_e_default_type_radio" value="buku"
                                                   :checked="(formData.kib_e_default_type || 'buku') === 'buku'"
                                                   @change="formData.kib_e_default_type = 'buku'; syncLainnyaFieldsToMain();"
                                                   class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-sm font-extrabold text-amber-400">📚 Buku Perpustakaan</span>
                                        </div>
                                        <span x-show="(formData.kib_e_default_type || 'buku') === 'buku'"
                                              class="text-[9px] px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 leading-relaxed">Buku, jurnal ilmiah kedokteran, literatur medis, dan arsip pustaka rumah sakit. Kolom: Judul, Pencipta, Spesifikasi.</p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-950/60 text-amber-400 border border-amber-700/30">📖 Judul Buku</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-950/60 text-amber-400 border border-amber-700/30">✍️ Pencipta</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-950/60 text-amber-400 border border-amber-700/30">📋 Spesifikasi</span>
                                    </div>
                                </div>

                                <!-- Opsi 2: Kesenian & Kebudayaan -->
                                <div @click="formData.kib_e_default_type = 'kesenian'; syncLainnyaFieldsToMain();"
                                     :class="formData.kib_e_default_type === 'kesenian' ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500 shadow-lg shadow-purple-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-purple-500/50'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5">
                                            <input type="radio" name="kib_e_default_type_radio" value="kesenian"
                                                   :checked="formData.kib_e_default_type === 'kesenian'"
                                                   @change="formData.kib_e_default_type = 'kesenian'; syncLainnyaFieldsToMain();"
                                                   class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-sm font-extrabold text-purple-400">🎨 Kesenian & Kebudayaan</span>
                                        </div>
                                        <span x-show="formData.kib_e_default_type === 'kesenian'"
                                              class="text-[9px] px-2 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 leading-relaxed">Lukisan, patung, ornamen dekoratif, dan benda seni atau budaya bersejarah milik rumah sakit. Kolom: Asal Daerah, Pencipta, Spesifikasi, Bahan, Ukuran.</p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">🗺️ Asal Daerah</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">🎭 Pencipta</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">🧱 Bahan</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">📐 Ukuran</span>
                                    </div>
                                </div>

                                <!-- Opsi 3: Hewan & Tumbuhan -->
                                <div @click="formData.kib_e_default_type = 'hewan_tumbuhan'; syncLainnyaFieldsToMain();"
                                     :class="formData.kib_e_default_type === 'hewan_tumbuhan' ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500 shadow-lg shadow-emerald-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-emerald-500/50'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5">
                                            <input type="radio" name="kib_e_default_type_radio" value="hewan_tumbuhan"
                                                   :checked="formData.kib_e_default_type === 'hewan_tumbuhan'"
                                                   @change="formData.kib_e_default_type = 'hewan_tumbuhan'; syncLainnyaFieldsToMain();"
                                                   class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-sm font-extrabold text-emerald-400">🌿 Hewan & Tumbuhan</span>
                                        </div>
                                        <span x-show="formData.kib_e_default_type === 'hewan_tumbuhan'"
                                              class="text-[9px] px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 leading-relaxed">Tanaman taman, pohon peneduh, hewan ternak, dan hewan peliharaan yang merupakan aset tetap milik rumah sakit. Kolom: Judul/Jenis, Spesifikasi.</p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-950/60 text-emerald-400 border border-emerald-700/30">🐄 Jenis Hewan</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-950/60 text-emerald-400 border border-emerald-700/30">🌳 Spesifikasi</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- PEMBUNGKUS ASET TETAP LAINNYA MULTI-ITEM REPEATER (MODEL PERSIS KIB B)     -->
                        <!-- ========================================================================= -->
                        <div class="space-y-4">
                            
                            <!-- Header Pembungkus Aset Tetap Lainnya Multi-Item -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-orange-950/30 border border-orange-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-orange-500/20 text-orange-400 text-sm">📚</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            RINCIAN ASET TETAP LAINNYA
                                            (<span class="text-orange-400" x-text="formData.lainnya_items.length"></span> Item Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap item aset tetap lainnya memiliki kategori (Buku Perpustakaan, Kesenian/Kebudayaan, Hewan/Tumbuhan), spesifikasi, volume, nilai satuan, dan ruang/pemegang penempatan masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addLainnyaItem()" 
                                        class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-orange-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Item Aset Tetap Lainnya Baru</span>
                                </button>
                            </div>

                            <!-- List Kartu Item Aset Tetap Lainnya (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.lainnya_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-orange-500/30 hover:border-orange-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap Item -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-orange-500/20 text-orange-300 font-mono font-extrabold text-xs border border-orange-500/40 flex items-center space-x-1.5">
                                                    <span>📚 Item #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold"
                                                      :class="{
                                                          'bg-amber-500/20 text-amber-300 border border-amber-500/40': (item.kib_e_sub_type || 'buku') === 'buku',
                                                          'bg-purple-500/20 text-purple-300 border border-purple-500/40': item.kib_e_sub_type === 'kesenian',
                                                          'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40': item.kib_e_sub_type === 'hewan_tumbuhan'
                                                      }"
                                                      x-text="(item.kib_e_sub_type === 'kesenian' ? '🎨 Kesenian & Budaya' : (item.kib_e_sub_type === 'hewan_tumbuhan' ? '🌿 Hewan & Tumbuhan' : '📚 Buku Perpustakaan'))">
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.lainnya_buku_judul || item.lainnya_kesenian_asal || item.lainnya_hewan_jenis">
                                                    • <span x-text="item.kib_e_sub_type === 'buku' ? item.lainnya_buku_judul : (item.kib_e_sub_type === 'kesenian' ? item.lainnya_kesenian_asal : (item.lainnya_hewan_jenis || item.lainnya_hewan_judul))"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Qty: <strong class="text-cyan-300" x-text="(item.lainnya_jumlah_barang || 1) + ' ' + (item.lainnya_satuan || 'Eksemplar')"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(item))"></strong>
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus Item (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.lainnya_items.length > 1" 
                                                    @click="removeLainnyaItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Item Ini</span>
                                            </button>
                                        </div>

                                        <!-- Sync kategori dari Section No. 3 global ke tiap item -->
                                        <template x-effect="item.kib_e_sub_type = formData.kib_e_default_type || 'buku'"></template>

                                        <!-- Grid Form Pengisian Spesifikasi Aset Tetap Lainnya -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- Sisi Kiri: Nama & Identitas Kategori -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span x-text="(item.kib_e_sub_type === 'kesenian' ? '🎨 Identitas Karya Seni' : (item.kib_e_sub_type === 'hewan_tumbuhan' ? '🌿 Identitas Hewan & Tumbuhan' : '📚 Identitas Buku Perpustakaan'))"></span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nama Barang (PMDN 108)</span>
                                                        <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
                                                            <span>🔒</span>
                                                            <span>Otomatis dari Langkah 2</span>
                                                        </span>
                                                    </label>
                                                    <input type="text" 
                                                           :value="item.lainnya_nama_barang || formData.lainnya_nama_barang || formData.sub_rincian_nama || 'Aset Tetap Lainnya'"
                                                           readonly
                                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                </div>

                                                <!-- Form Spesifik: Buku -->
                                                <template x-if="(item.kib_e_sub_type || 'buku') === 'buku'">
                                                    <div class="space-y-2.5">
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul Buku / Literatur Medis</label>
                                                            <input type="text" x-model="item.lainnya_buku_judul" placeholder="Pedoman Standar Pelayanan Klinis Kedokteran 2026"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Penulis / Penerbit</label>
                                                            <input type="text" x-model="item.lainnya_buku_pencipta" placeholder="Komite Medik & Tim Farmasi RSUD"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Form Spesifik: Kesenian -->
                                                <template x-if="item.kib_e_sub_type === 'kesenian'">
                                                    <div class="space-y-2.5">
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Asal Daerah / Wilayah Budaya</label>
                                                            <input type="text" x-model="item.lainnya_kesenian_asal" placeholder="Jawa Timur / Bondowoso"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-purple-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Seniman</label>
                                                            <input type="text" x-model="item.lainnya_kesenian_pencipta" placeholder="Sanggar Seni Budaya Bondowoso"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Form Spesifik: Hewan & Tumbuhan -->
                                                <template x-if="item.kib_e_sub_type === 'hewan_tumbuhan'">
                                                    <div class="space-y-2.5">
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul / Jenis Hewan & Tumbuhan</label>
                                                            <input type="text" x-model="item.lainnya_hewan_jenis" @input="item.lainnya_hewan_judul = $event.target.value" placeholder="Pohon Tabebuya Emas / Tanaman Lanskap Taman"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-emerald-500">
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Sisi Kanan: Spesifikasi Fisik, Bahan, Ukuran & Kondisi -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>🔍 Spesifikasi Fisik & Kondisi:</span>
                                                    </span>
                                                </div>

                                                <!-- Spesifikasi Buku -->
                                                <template x-if="(item.kib_e_sub_type || 'buku') === 'buku'">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Spesifikasi Buku / Literatur</label>
                                                        <input type="text" x-model="item.lainnya_buku_spesifikasi" placeholder="Edisi Revisi 2026 / Hardcover Lux / 850 Halaman"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 focus:border-amber-500">
                                                    </div>
                                                </template>

                                                <!-- Spesifikasi Kesenian -->
                                                <template x-if="item.kib_e_sub_type === 'kesenian'">
                                                    <div class="space-y-2.5">
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-medium">Spesifikasi Karya Seni</label>
                                                            <input type="text" x-model="item.lainnya_kesenian_spesifikasi" placeholder="Lukisan Panorama Rumah Sakit / Patung Lambang Daerah"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-purple-300 focus:border-purple-500">
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <div>
                                                                <label class="block text-slate-400 text-[10px] mb-1 font-medium">Bahan Seni</label>
                                                                <input type="text" x-model="item.lainnya_kesenian_bahan" placeholder="Kanvas & Kayu Jati"
                                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-purple-500">
                                                            </div>
                                                            <div>
                                                                <label class="block text-slate-400 text-[10px] mb-1 font-medium">Ukuran (m/cm)</label>
                                                                <input type="text" x-model="item.lainnya_kesenian_ukuran" placeholder="200 x 120 cm"
                                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-purple-500">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Spesifikasi Hewan & Tumbuhan -->
                                                <template x-if="item.kib_e_sub_type === 'hewan_tumbuhan'">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Spesifikasi Tanaman / Hewan</label>
                                                        <input type="text" x-model="item.lainnya_hewan_spesifikasi" placeholder="Bibit Unggul Tinggi 3.5 Meter Siap Tanam"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-300 focus:border-emerald-500">
                                                    </div>
                                                </template>

                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Barang</label>
                                                    <select x-model="item.lainnya_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                                        <option value="Baik">Baik (B)</option>
                                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Volume & Nilai Satuan Barang -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                                            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-1.5">
                                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>💰 Volume & Nilai Satuan Barang (Rp):</span>
                                                </span>
                                                <div class="flex items-center space-x-1.5 bg-emerald-950/60 border border-emerald-500/30 px-2.5 py-0.5 rounded-lg">
                                                    <span class="text-[10px] text-slate-300 font-semibold">Sub Total Item #<span x-text="idx + 1"></span>:</span>
                                                    <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + Number(getLainnyaSubtotal(item)).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah (Volume)</label>
                                                    <input type="number" min="1" x-model.number="item.lainnya_jumlah_barang" placeholder="1"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                                    <input type="text" x-model="item.lainnya_satuan" placeholder="Eksemplar / Buah / Batang / Unit"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nilai Satuan (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.lainnya_nilai_satuan ? Number(item.lainnya_nilai_satuan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.lainnya_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="450.000"
                                                           class="w-full bg-slate-950 border border-slate-700 text-emerald-300 rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Admin Proyek (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.lainnya_administrasi_proyek ? Number(item.lainnya_administrasi_proyek).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.lainnya_administrasi_proyek = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div class="col-span-2 sm:col-span-1">
                                                    <label class="block text-emerald-400 text-[10px] mb-1 font-bold">Sub Total (Rp)</label>
                                                    <div class="w-full bg-slate-950/90 border border-emerald-500/50 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-black flex items-center justify-between shadow-inner">
                                                        <span class="text-emerald-500 text-[10px]">Rp</span>
                                                        <span x-text="Number(getLainnyaSubtotal(item)).toLocaleString('id-ID')"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ruang / Pemegang Aset -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/40 space-y-2 relative" @click.away="item.isRuangOpen = false">
                                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-1.5">
                                                <label class="block text-amber-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📍 Ruang / Unit Pemegang (Penanggung Jawab & Lokasi):</span>
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                                        <span>🏥</span>
                                                        <span>Unit & Paviliun</span>
                                                    </span>
                                                    <button type="button" 
                                                            x-show="item.ruang_pemegang || item.ruang_pemegang_lainnya" 
                                                            @click="item.ruang_pemegang = ''; item.ruang_pemegang_lainnya = ''; item.searchRuang = ''; item.isRuangOpen = true; syncLainnyaFieldsToMain();" 
                                                            class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                                        ✕ Reset
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="relative">
                                                <input type="text" 
                                                       :value="!item.isRuangOpen ? (item.ruang_pemegang || item.ruang_pemegang_lainnya) : item.searchRuang"
                                                       @input="item.ruang_pemegang = $event.target.value; item.ruang_pemegang_lainnya = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true; syncLainnyaFieldsToMain();"
                                                       @focus="item.isRuangOpen = true"
                                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 pl-9 text-xs text-white font-semibold focus:outline-none transition-all">
                                                <svg class="w-3.5 h-3.5 text-amber-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>

                                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                                            <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height: 180px;" class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                                <div class="px-2.5 py-1 bg-slate-950/80 rounded-lg text-[9.5px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between">
                                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                                    <span class="text-slate-400 font-mono text-[9px]" x-text="filterUnitsForLainnyaItem(item).length + ' Unit/Ruangan'"></span>
                                                </div>
                                                <template x-for="u in filterUnitsForLainnyaItem(item)" :key="u.id">
                                                    <div @click="selectUnitForLainnyaItem(item, u)" class="p-2 rounded-xl bg-slate-950/50 hover:bg-amber-500/15 border border-slate-800/60 hover:border-amber-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                                        <div class="min-w-0 pr-2">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-xs font-bold text-white group-hover:text-amber-300 truncate" x-text="u.nama"></span>
                                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                                            </div>
                                                            <p class="text-[9.5px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                                        </div>
                                                        <span class="px-2 py-0.5 rounded-lg bg-slate-900 text-amber-300 border border-amber-500/30 text-[9.5px] font-bold shrink-0">Pilih →</span>
                                                    </div>
                                                </template>
                                                <template x-if="filterUnitsForLainnyaItem(item).length === 0">
                                                    <div class="p-2.5 text-center text-xs text-slate-400">
                                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Item Aset Tetap Lainnya Baru (Besar & Jelas) -->
                            <button type="button" @click="addLainnyaItem()" 
                                    class="w-full py-3.5 border-2 border-dashed border-orange-500/50 hover:border-orange-400 bg-orange-950/20 hover:bg-orange-950/40 text-orange-300 hover:text-orange-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Item Aset Tetap Lainnya Baru</span>
                            </button>

                            <!-- Ringkasan Anggaran & Akumulasi Realisasi KIB E -->
                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-orange-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Jumlah Anggaran (Pagu):</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-orange-400 font-semibold block uppercase tracking-wider">📦 Total Volume / Item:</span>
                                        <span class="text-sm font-black text-orange-300 font-mono" x-text="totalVolumeAsetLainnya + ' Item/Barang'"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Jumlah Realisasi (Akumulasi):</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAsetLainnya)"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                                    <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">Total Nilai Realisasi Pengadaan:</span>
                                    <span class="text-base font-extrabold text-cyan-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAsetLainnya)"></span>
                                </div>
                            </div>

                        </div>

                        <!-- ========================================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL RESMI KIB E (30 KOLOM) DENGAN LOOP MULTI-ITEM   -->
                        <!-- ========================================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Aset Tetap Lainnya (Format Excel Resmi KIB E RSUD):</span>
                                </span>
                                <span class="text-[10px] text-orange-400 font-mono" x-text="formData.lainnya_items.length + ' Baris Item Terdaftar (KIB E 30 Kolom)'">Format Excel Resmi KIB E (30 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1650px]">
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="29" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                RUANG /<br>PEMEGANG
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BUKU PERPUSTAKAAN</th>
                                            <th colspan="5" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Barang Bercorak Kesenian / Kebudayaan</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Hewan Ternak / Tumbuhan</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">ADMINISTRASI PROYEK (Rp)</th>
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
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Judul</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Spesifikasi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
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
                                    <!-- Body Data Live Sesuai Input User (Looping lainnya_items) -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(lItem, lIdx) in formData.lainnya_items" :key="lIdx">
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <!-- 1. NAMA BARANG -->
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="lItem.lainnya_nama_barang || formData.lainnya_nama_barang || formData.sub_rincian_nama"></td>
                                                <!-- 2. KODE BARANG -->
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="lItem.lainnya_kode_barang || formData.lainnya_kode_barang || formData.sub_rincian_kode"></td>
                                                <!-- 3-5. BUKU PERPUSTAKAAN -->
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_buku_judul || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_buku_pencipta || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_buku_spesifikasi || '-'"></td>
                                                <!-- 6-10. KESENIAN / KEBUDAYAAN -->
                                                <td class="px-2 py-2 border border-slate-400" x-text="lItem.lainnya_kesenian_asal || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_kesenian_pencipta || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_kesenian_spesifikasi || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="lItem.lainnya_kesenian_bahan || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono" x-text="lItem.lainnya_kesenian_ukuran || '-'"></td>
                                                <!-- 11-12. HEWAN / TUMBUHAN -->
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_hewan_judul || lItem.lainnya_hewan_jenis || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_hewan_spesifikasi || '-'"></td>
                                                <!-- 13-20. RIWAYAT PEMBELIAN -->
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <!-- 21-23. VOLUME & NILAI SATUAN -->
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="lItem.lainnya_jumlah_barang || 1"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="lItem.lainnya_satuan || 'Eksemplar'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(lItem.lainnya_nilai_satuan || 0)"></td>
                                                <!-- 24-25. ADMIN PROYEK & TOTAL NILAI -->
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(lItem.lainnya_administrasi_proyek || 0)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-orange-800" x-text="formatRupiah(getLainnyaSubtotal(lItem))"></td>
                                                <!-- 26-27. SP2D -->
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <!-- 28-29. BAST -->
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <!-- 30. RUANG / PEMEGANG -->
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="lItem.ruang_pemegang || lItem.ruang_pemegang_lainnya || formData.ruang_pemegang || formData.ruang_pemegang_lainnya || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <template x-if="isAtb">
                    <div class="space-y-6">

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30">
                                        📅 Periode: <span x-text="triwulanDateLabel"></span>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Klik pada kartu atau radio button untuk memilih jenis dokumen</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- 1. SPK -->
                                <div @click="selectDocType('spk')" 
                                     :class="formData.doc_type === 'spk' ? 'border-cyan-500 bg-cyan-950/30 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_atb" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-[11px] font-extrabold text-cyan-400">📄 SPK (Kontrak)</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'spk'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026" 
                                               :disabled="formData.doc_type !== 'spk'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal SPK</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.spk_tanggal" @change="onDocDateChange(formData.spk_tanggal, 'spk_tanggal')" 
                                               :disabled="formData.doc_type !== 'spk'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 2. Surat Pesanan -->
                                <div @click="selectDocType('surat_pesanan')" 
                                     :class="formData.doc_type === 'surat_pesanan' ? 'border-purple-500 bg-purple-950/30 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_atb" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-[11px] font-extrabold text-purple-400">📦 Surat Pesanan</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'surat_pesanan'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Surat Pesanan</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.surat_pesanan_tanggal" @change="onDocDateChange(formData.surat_pesanan_tanggal, 'surat_pesanan_tanggal')" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 3. Kwitansi -->
                                <div @click="selectDocType('kwitansi')" 
                                     :class="formData.doc_type === 'kwitansi' ? 'border-amber-500 bg-amber-950/30 ring-1 ring-amber-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_atb" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-[11px] font-extrabold text-amber-400">🧾 Kwitansi</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'kwitansi'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Kwitansi</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.kwitansi_tanggal" @change="onDocDateChange(formData.kwitansi_tanggal, 'kwitansi_tanggal')" 
                                               :disabled="formData.doc_type !== 'kwitansi'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 4. Invoice -->
                                <div @click="selectDocType('faktur')" 
                                     :class="formData.doc_type === 'faktur' ? 'border-emerald-500 bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_atb" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-[11px] font-extrabold text-emerald-400">📑 Invoice / Faktur</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'faktur'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Invoice</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.faktur_tanggal" @change="onDocDateChange(formData.faktur_tanggal, 'faktur_tanggal')" 
                                               :disabled="formData.doc_type !== 'faktur'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                                <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold border border-amber-500/30">
                                    📅 Periode: <span x-text="triwulanDateLabel"></span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="Contoh: 0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.sp2d_tanggal" @change="onDocDateChange(formData.sp2d_tanggal, 'sp2d_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="Contoh: 000.2.3.2/224/RSUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.bast_dokumen_tanggal" @change="onDocDateChange(formData.bast_dokumen_tanggal, 'bast_dokumen_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================================= -->
                        <!-- MULTI-ITEM REPEATER KHUSUS ATB (ASET TIDAK BERWUJUD)           -->
                        <!-- ============================================================= -->
                        <div class="space-y-4">

                            <!-- Header Pembungkus ATB -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-violet-950/30 border border-violet-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-violet-500/20 text-violet-400 text-sm">💻</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            RINCIAN ASET TIDAK BERWUJUD (<span class="text-violet-400" x-text="formData.atb_items.length"></span> Item ATB Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap item ATB memiliki Judul/Nama, Pencipta/Vendor, Spesifikasi, Jumlah, Satuan, Kondisi, Nilai Satuan, Administrasi Proyek, dan Ruang/Pemegang masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addAtbItem()" 
                                        class="px-4 py-2 rounded-xl bg-violet-500 hover:bg-violet-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-violet-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Item ATB Baru</span>
                                </button>
                            </div>

                            <!-- List Kartu ATB (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.atb_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-violet-500/30 hover:border-violet-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap ATB -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-violet-500/20 text-violet-300 font-mono font-extrabold text-xs border border-violet-500/40 flex items-center space-x-1.5">
                                                    <span>💻 Item ATB #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.atb_nama_barang">
                                                    • <span x-text="item.atb_nama_barang"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono" x-show="item.atb_judul_nama">
                                                    • <span x-text="item.atb_judul_nama"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Qty: <strong class="text-cyan-300" x-text="(item.atb_jumlah || 1) + ' ' + (item.atb_satuan || 'Lisensi')"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getAtbSubtotal(item))"></strong>
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus ATB (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.atb_items.length > 1" 
                                                    @click="removeAtbItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Item Ini</span>
                                            </button>
                                        </div>

                                        <!-- Grid Form Pengisian Spesifikasi ATB -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- Kolom Kiri: Identitas ATB -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>💻 Identitas &amp; Spesifikasi ATB:</span>
                                                    </span>
                                                </div>

                                                <!-- Nama Barang (Terkunci) -->
                                                <div>
                                                    <div class="flex items-center justify-between mb-1">
                                                        <label class="block text-slate-400 text-[10px] font-semibold">Nama Barang (PMDN 108)</label>
                                                        <span class="text-[9px] text-amber-400/80 flex items-center gap-1 font-medium bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                            Terkunci (Mengikuti Langkah 2)
                                                        </span>
                                                    </div>
                                                    <input type="text" :value="item.atb_nama_barang || formData.atb_nama_barang || formData.sub_rincian_nama || 'Aset Tidak Berwujud'" readonly
                                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                </div>

                                                <!-- Judul / Nama Software -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul / Nama Software &amp; Lisensi</label>
                                                    <input type="text" x-model="item.atb_judul_nama" placeholder="Aplikasi SIMAT-RK (Sistem Informasi Manajemen Aset)..."
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                                                </div>

                                                <!-- Pencipta / Vendor -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Vendor / Pengembang</label>
                                                    <input type="text" x-model="item.atb_pencipta" placeholder="Tim IT SIMRS RSUD & Pengembang Sistem"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                                                </div>

                                                <!-- Spesifikasi -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi Software / Hak Cipta</label>
                                                    <textarea rows="2" x-model="item.atb_spesifikasi" placeholder="Web-Based, Multi-Role Access, Integrasi SatuSehat & RME..."
                                                              class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-cyan-300 focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                                                </div>
                                            </div>

                                            <!-- Kolom Kanan: Volume & Nilai -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-violet-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📦 Volume, Nilai &amp; Ruang Pemegang:</span>
                                                    </span>
                                                </div>

                                                <!-- Jumlah, Satuan, Kondisi -->
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah</label>
                                                        <input type="number" min="1" x-model.number="item.atb_jumlah" placeholder="1"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-violet-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan</label>
                                                        <input type="text" x-model="item.atb_satuan" placeholder="Lisensi"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-violet-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi</label>
                                                        <select x-model="item.atb_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-violet-500">
                                                            <option value="Baik">Baik (B)</option>
                                                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Nilai Satuan -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nilai Satuan Barang (Rp)</label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                                        <input type="number" x-model.number="item.atb_nilai_satuan" placeholder="145000000"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-violet-300 font-mono font-bold focus:border-violet-500">
                                                    </div>
                                                </div>

                                                <!-- Administrasi Proyek -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Administrasi Proyek (Rp)</label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                                        <input type="number" x-model.number="item.atb_administrasi_proyek" placeholder="5000000"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-amber-300 font-mono font-bold focus:border-violet-500">
                                                    </div>
                                                </div>

                                                <!-- Subtotal Item -->
                                                <div class="p-3 rounded-xl bg-violet-950/40 border border-violet-500/30 flex items-center justify-between">
                                                    <span class="text-[10px] text-violet-400 font-semibold uppercase tracking-wider">Subtotal Item Ini:</span>
                                                    <span class="text-sm font-black text-violet-300 font-mono" x-text="'Rp ' + formatRupiah(getAtbSubtotal(item))"></span>
                                                </div>

                                                <!-- Ruang / Pemegang per Item -->
                                                <div class="relative" @click.away="item.isRuangOpen = false">
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">📍 Ruang / Pemegang Item Ini</label>
                                                    <div class="relative">
                                                        <input type="text" 
                                                               :value="!item.isRuangOpen ? item.atb_ruang_pemegang : item.searchRuang"
                                                               @input="item.atb_ruang_pemegang = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true"
                                                               @focus="item.isRuangOpen = true"
                                                               placeholder="Ketik nama Ruang/Unit/Paviliun..."
                                                               class="w-full bg-slate-950 border border-slate-700 hover:border-violet-500 focus:border-violet-500 rounded-xl px-3 py-2 pl-8 text-xs text-white font-semibold focus:outline-none transition-all">
                                                        <svg class="w-3.5 h-3.5 text-violet-400 absolute left-2.5 top-2.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                                    </div>
                                                    <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height:180px;" class="absolute left-0 right-0 z-40 mt-1 bg-slate-900 border border-violet-500/50 rounded-xl shadow-2xl overflow-y-auto p-1.5 space-y-1">
                                                        <template x-for="u in (masterUnits || []).filter(u => !item.searchRuang || u.nama.toLowerCase().includes(item.searchRuang.toLowerCase()))" :key="u.id">
                                                            <div @click="item.atb_ruang_pemegang = u.nama; item.isRuangOpen = false; item.searchRuang = ''"
                                                                 class="px-3 py-2 rounded-lg hover:bg-violet-500/20 cursor-pointer text-xs text-white hover:text-violet-300 transition-all flex items-center justify-between">
                                                                <div>
                                                                    <span class="font-semibold" x-text="u.nama"></span>
                                                                    <span class="text-[9px] text-slate-400 ml-1" x-text="'• ' + (u.tipe || 'Unit')"></span>
                                                                </div>
                                                                <span class="text-violet-400 text-[10px] font-bold">Pilih →</span>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Ringkasan Total ATB -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-violet-500/30 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-4">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Jumlah Anggaran:</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Jumlah Realisasi (Kolom 15):</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_realisasi)"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-violet-400 font-semibold block uppercase tracking-wider">Total Nilai Semua ATB:</span>
                                    <span class="text-base font-extrabold text-violet-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAtb)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- LIVE PREVIEW TABEL ATB (Format Excel Resmi ATB 23 Kolom)               -->
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
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="22" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Ruang /<br>Pemegang
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">Judul / Nama</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-32 align-middle bg-[#fde9d9]">Pencipta</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">Spesifikasi</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">ADMINISTRASI PROYEK (Rp)</th>
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
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
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
                                    <!-- Body Data Live - Multi-Row per item ATB -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(item, idx) in formData.atb_items" :key="idx">
                                            <tr>
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="item.atb_nama_barang || formData.atb_nama_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="item.atb_kode_barang || formData.atb_kode_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="item.atb_judul_nama"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="item.atb_pencipta"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="item.atb_spesifikasi"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="item.atb_jumlah || 1"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="item.atb_satuan || 'Lisensi'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(item.atb_nilai_satuan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(item.atb_administrasi_proyek)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-violet-800" x-text="formatRupiah(getAtbSubtotal(item))"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="item.atb_ruang_pemegang || formData.ruang_pemegang_atb"></td>
                                            </tr>
                                        </template>
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

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30">
                                        📅 Periode: <span x-text="triwulanDateLabel"></span>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Klik pada kartu atau radio button untuk memilih jenis dokumen</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- 1. SPK -->
                                <div @click="selectDocType('spk')" 
                                     :class="formData.doc_type === 'spk' ? 'border-cyan-500 bg-cyan-950/30 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_f" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-[11px] font-extrabold text-cyan-400">📄 SPK (Kontrak)</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'spk'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026" 
                                               :disabled="formData.doc_type !== 'spk'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal SPK</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.spk_tanggal" @change="onDocDateChange(formData.spk_tanggal, 'spk_tanggal')" 
                                               :disabled="formData.doc_type !== 'spk'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 2. Surat Pesanan -->
                                <div @click="selectDocType('surat_pesanan')" 
                                     :class="formData.doc_type === 'surat_pesanan' ? 'border-purple-500 bg-purple-950/30 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_f" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-[11px] font-extrabold text-purple-400">📦 Surat Pesanan / BAP</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'surat_pesanan'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Surat Pesanan</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.surat_pesanan_tanggal" @change="onDocDateChange(formData.surat_pesanan_tanggal, 'surat_pesanan_tanggal')" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 3. Kwitansi -->
                                <div @click="selectDocType('kwitansi')" 
                                     :class="formData.doc_type === 'kwitansi' ? 'border-amber-500 bg-amber-950/30 ring-1 ring-amber-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_f" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-[11px] font-extrabold text-amber-400">🧾 Kwitansi Termin</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'kwitansi'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Kwitansi</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.kwitansi_tanggal" @change="onDocDateChange(formData.kwitansi_tanggal, 'kwitansi_tanggal')" 
                                               :disabled="formData.doc_type !== 'kwitansi'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 4. Invoice -->
                                <div @click="selectDocType('faktur')" 
                                     :class="formData.doc_type === 'faktur' ? 'border-emerald-500 bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_f" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-[11px] font-extrabold text-emerald-400">📑 Invoice / Faktur</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'faktur'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Invoice</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.faktur_tanggal" @change="onDocDateChange(formData.faktur_tanggal, 'faktur_tanggal')" 
                                               :disabled="formData.doc_type !== 'faktur'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                                <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold border border-amber-500/30">
                                    📅 Periode: <span x-text="triwulanDateLabel"></span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.sp2d_tanggal" @change="onDocDateChange(formData.sp2d_tanggal, 'sp2d_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="000.2.3.2/224/..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.bast_dokumen_tanggal" @change="onDocDateChange(formData.bast_dokumen_tanggal, 'bast_dokumen_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ============================================================= -->
                        <!-- MULTI-ITEM REPEATER KHUSUS KIB F (KDP / KONSTRUKSI)             -->
                        <!-- ============================================================= -->
                        <div class="space-y-4">
                            
                            <!-- Header Pembungkus KDP -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-rose-950/30 border border-rose-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-rose-500/20 text-rose-400 text-sm">🏗️</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            RINCIAN KONSTRUKSI DALAM PENGERJAAN (<span class="text-rose-400" x-text="formData.kdp_items.length"></span> Proyek KDP Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap proyek KDP memiliki Luas Rencana, Progres Fisik (%), Kondisi, Status Tanah, Volume, Komponen Nilai (Perencanaan, Fisik, Pengawasan, AP), dan Alamat Lokasi Fisik masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addKdpItem()" 
                                        class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-rose-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Proyek KDP Baru</span>
                                </button>
                            </div>

                            <!-- List Kartu KDP (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.kdp_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-rose-500/30 hover:border-rose-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap KDP -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-rose-500/20 text-rose-300 font-mono font-extrabold text-xs border border-rose-500/40 flex items-center space-x-1.5">
                                                    <span>🏗️ Proyek KDP #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.kdp_nama_barang">
                                                    • <span x-text="item.kdp_nama_barang"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Luas: <strong class="text-cyan-300" x-text="(item.kdp_luas_m2 || 0) + ' M²'"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Progres: <strong class="text-rose-400 font-bold" x-text="(item.kdp_progres_persen || 0) + '%'"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Qty: <strong class="text-amber-300" x-text="(item.kdp_jumlah_bangunan || 1) + ' ' + (item.kdp_satuan || 'Gedung')"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getKdpSubtotal(item))"></strong>
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus KDP (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.kdp_items.length > 1" 
                                                    @click="removeKdpItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus KDP Ini</span>
                                            </button>
                                        </div>

                                        <!-- Grid Form Pengisian Spesifikasi KDP -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- Kondisi & Spesifikasi Bangunan KDP -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>🏗️ Kondisi, Spesifikasi & Progres Fisik:</span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="flex items-center justify-between mb-1">
                                                        <label class="block text-slate-400 text-[10px] font-semibold">Nama Bangunan (PMDN 108)</label>
                                                        <span class="text-[9px] text-amber-400/80 flex items-center gap-1 font-medium bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                            Terkunci (Mengikuti Langkah 2)
                                                        </span>
                                                    </div>
                                                    <input type="text" :value="item.kdp_nama_barang || formData.kdp_nama_barang || formData.sub_rincian_nama || 'Konstruksi Dalam Pengerjaan'" readonly
                                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas Rencana (M2/Lt)</label>
                                                        <input type="number" min="0" step="any" x-model.number="item.kdp_luas_m2" 
                                                               @input="if (item.kdp_luas_m2 < 0) item.kdp_luas_m2 = 0;"
                                                               placeholder="850"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi (B/KB/RB)</label>
                                                        <select x-model="item.kdp_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-amber-500">
                                                            <option value="B">B (Baik)</option>
                                                            <option value="KB">KB (Kurang Baik)</option>
                                                            <option value="RB">RB (Rusak Berat)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bertingkat / Tidak</label>
                                                        <select x-model="item.kdp_bertingkat" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                            <option value="Bertingkat">Bertingkat</option>
                                                            <option value="Tidak">Tidak Bertingkat</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Beton / Tidak</label>
                                                        <select x-model="item.kdp_beton" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                            <option value="Beton">Beton</option>
                                                            <option value="Tidak">Bukan Beton</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <!-- Field Khusus KIB F: Progres Fisik (%) -->
                                                <div class="p-3 rounded-xl bg-slate-950 border border-rose-500/30 space-y-2">
                                                    <div class="flex items-center justify-between">
                                                        <label class="block text-rose-300 text-[10px] font-bold uppercase tracking-wider flex items-center space-x-1">
                                                            <span>📊 Progres Fisik Pengerjaan (%):</span>
                                                        </label>
                                                        <span class="text-rose-400 font-mono font-black text-xs" x-text="(item.kdp_progres_persen || 0) + '%'"></span>
                                                    </div>
                                                    <div class="relative">
                                                        <input type="number" min="0" max="100" x-model.number="item.kdp_progres_persen" 
                                                               @input="if (item.kdp_progres_persen < 0) item.kdp_progres_persen = 0; if (item.kdp_progres_persen > 100) item.kdp_progres_persen = 100;"
                                                               placeholder="65"
                                                               class="w-full bg-slate-900 border border-rose-500/50 rounded-xl px-3 py-2 text-xs text-rose-300 font-mono font-black focus:outline-none focus:border-rose-400">
                                                        <span class="absolute right-3 top-2 text-rose-400 text-xs font-bold">%</span>
                                                    </div>
                                                    <div class="w-full bg-slate-900 rounded-full h-2 overflow-hidden border border-slate-800">
                                                        <div class="bg-gradient-to-r from-rose-500 via-amber-400 to-emerald-400 h-2 rounded-full transition-all duration-300" :style="'width: ' + (item.kdp_progres_persen || 0) + '%'"></div>
                                                    </div>
                                                    <span class="text-[9px] text-slate-400 italic block">* Catatan: Progres fisik hanya masuk di form & detail data, tidak masuk live preview tabel.</span>
                                                </div>
                                            </div>

                                            <!-- Jenis Bangunan & Status Tanah (KIB A) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📜 Status Tanah & Kapitalisasi:</span>
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Tanah</label>
                                                        <input type="text" x-model="item.kdp_status_tanah" placeholder="Hak Pakai RSUD"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kode Aset Tanah</label>
                                                        <input type="text" x-model="item.kdp_kode_aset_tanah" placeholder="1.3.1.01.01.02.013"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono focus:border-cyan-500">
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-3 gap-1.5">
                                                    <div>
                                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Bangunan Baru</label>
                                                        <select x-model="item.kdp_is_baru" 
                                                                @change="if (item.kdp_is_baru === 'Baru') { item.kdp_kapitalisasi_tahun_induk = ''; item.kdp_kapitalisasi_nilai_induk = 0; }"
                                                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-cyan-500">
                                                            <option value="Baru">Baru</option>
                                                            <option value="Lama">Lama</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Tahun Induk</label>
                                                        <input type="text" x-model="item.kdp_kapitalisasi_tahun_induk" 
                                                               :disabled="item.kdp_is_baru === 'Baru'"
                                                               :class="item.kdp_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700 focus:border-cyan-500 text-white'"
                                                               placeholder="2020"
                                                               class="w-full border rounded-xl px-2 py-2 text-xs font-mono transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Induk</label>
                                                        <input type="text" 
                                                               :disabled="item.kdp_is_baru === 'Baru'"
                                                               :class="item.kdp_is_baru === 'Baru' ? 'opacity-40 cursor-not-allowed bg-slate-900/60 border-slate-800 text-slate-500' : 'bg-slate-950 border-slate-700 focus:border-cyan-500 text-amber-300'"
                                                               :value="item.kdp_is_baru === 'Baru' ? '' : (item.kdp_kapitalisasi_nilai_induk ? Number(item.kdp_kapitalisasi_nilai_induk).toLocaleString('id-ID') : '')"
                                                               @input="
                                                                   let raw = $event.target.value.replace(/\D/g, '');
                                                                   item.kdp_kapitalisasi_nilai_induk = raw ? parseInt(raw, 10) : 0;
                                                                   $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                               "
                                                               placeholder="3.500.000.000"
                                                               class="w-full border rounded-xl px-1.5 py-2 text-[10px] font-mono transition-all">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Volume & Nilai Satuan KDP (Rp) -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">💰 Volume & Rincian Nilai KDP (Rp):</span>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Bangunan</label>
                                                    <input type="number" min="1" x-model.number="item.kdp_jumlah_bangunan" 
                                                           @input="if (item.kdp_jumlah_bangunan < 1) item.kdp_jumlah_bangunan = 1;"
                                                           placeholder="1"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan Barang</label>
                                                    <input type="text" x-model="item.kdp_satuan" placeholder="Gedung / Unit / Paket / M²"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-emerald-500">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Perencanaan (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.kdp_nilai_perencanaan ? Number(item.kdp_nilai_perencanaan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.kdp_nilai_perencanaan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="75.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Fisik (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.kdp_nilai_fisik ? Number(item.kdp_nilai_fisik).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.kdp_nilai_fisik = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="1.850.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai Pengawasan (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.kdp_nilai_pengawasan ? Number(item.kdp_nilai_pengawasan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.kdp_nilai_pengawasan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="50.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[9px] mb-1 font-semibold">Nilai AP (Rp)</label>
                                                    <input type="text" 
                                                           :value="(item.kdp_nilai_ap || item.kdp_nilai_pip) ? Number(item.kdp_nilai_ap || item.kdp_nilai_pip).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.kdp_nilai_ap = raw ? parseInt(raw, 10) : 0;
                                                               item.kdp_nilai_pip = item.kdp_nilai_ap;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="25.000.000"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                            </div>

                                            <div class="pt-1 flex items-center justify-between border-t border-slate-800">
                                                <span class="text-[10px] text-slate-400 font-semibold uppercase">Subtotal Nilai KDP Ini:</span>
                                                <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(getKdpSubtotal(item))"></span>
                                            </div>
                                        </div>

                                        <!-- Letak / Alamat Lokasi Fisik Proyek KDP -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-rose-500/40 space-y-1.5">
                                            <div class="flex items-center justify-between border-b border-rose-500/30 pb-1.5">
                                                <label class="block text-rose-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📍 Letak / Alamat Lokasi Fisik Proyek KDP:</span>
                                                </label>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold">Lokasi Fisik KDP</span>
                                            </div>
                                            <input type="text" x-model="item.kdp_alamat" placeholder="Contoh: Kompleks Paviliun Melati & Gedung Rawat Inap Baru RSUD Dr. H. Koesnandi"
                                                   class="w-full bg-slate-950 border border-slate-700 hover:border-rose-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-rose-500 transition-all">
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Proyek KDP Baru (Besar & Jelas) -->
                            <button type="button" @click="addKdpItem()" 
                                    class="w-full py-3.5 border-2 border-dashed border-rose-500/50 hover:border-rose-400 bg-rose-950/20 hover:bg-rose-950/40 text-rose-300 hover:text-rose-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Proyek KDP Lainnya</span>
                            </button>

                            <!-- Ringkasan Anggaran & Akumulasi Realisasi KIB F -->
                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-rose-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Jumlah Anggaran (Pagu):</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">📊 Akumulasi Realisasi:</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_realisasi)"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                                    <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">Total Nilai Realisasi Pengadaan KDP:</span>
                                    <span class="text-base font-extrabold text-cyan-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiKdp)"></span>
                                </div>
                            </div>

                        </div>

                        <!-- ============================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL KHUSUS KIB F (PERSIS SEPERTI KIB C)   -->
                        <!-- ============================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Rincian Belanja Modal Konstruksi Dalam Pengerjaan (Sesuai SPK/Invoice):</span>
                                </span>
                                <span class="text-[10px] text-rose-400 font-mono" x-text="formData.kdp_items.length + ' Proyek KDP Terdaftar'">Format Excel KIB F RSUD (31 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1600px]">
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="30" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Letak / Alamat
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-20 align-middle bg-[#fde9d9]">Luas (M2/Lt)</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Kondisi / Spesifikasi</th>
                                            <th colspan="5" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Jenis Bangunan</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th colspan="4" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
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
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Bangunan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Perencanaan (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Fisik (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Pengawasan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai AP</th>
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
                                    <!-- Body Data Live Sesuai Input User (Looping kdp_items) -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(kItem, kIdx) in formData.kdp_items" :key="kIdx">
                                             <tr>
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="kItem.kdp_nama_barang || formData.kdp_nama_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="kItem.kdp_kode_barang || formData.kdp_kode_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono" x-text="kItem.kdp_luas_m2"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="kItem.kdp_kondisi"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="kItem.kdp_bertingkat"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="kItem.kdp_beton"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="kItem.kdp_status_tanah"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="kItem.kdp_kode_aset_tanah"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="kItem.kdp_is_baru === 'Baru' ? '1' : '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="kItem.kdp_is_baru === 'Baru' ? '-' : (kItem.kdp_kapitalisasi_tahun_induk || '-')"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="kItem.kdp_is_baru === 'Baru' ? '-' : (kItem.kdp_kapitalisasi_nilai_induk ? formatRupiah(kItem.kdp_kapitalisasi_nilai_induk) : '-')"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="kItem.kdp_jumlah_bangunan"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="kItem.kdp_satuan"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(kItem.kdp_nilai_perencanaan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(kItem.kdp_nilai_fisik)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(kItem.kdp_nilai_pengawasan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(kItem.kdp_nilai_ap || kItem.kdp_nilai_pip)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-rose-800" x-text="formatRupiah(getKdpSubtotal(kItem))"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="kItem.kdp_alamat || formData.alamat_barang"></td>
                                            </tr>
                                        </template>
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
                                    <input type="text" x-datepicker x-model="formData.spk_tanggal" :min="minDateTriwulan" :max="maxDateTriwulan" @change="onDocDateChange($event.target.value, 'spk_tanggal')" placeholder="dd/mm/yyyy"
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
                                    <input type="text" x-datepicker x-model="formData.surat_pesanan_tanggal" :min="minDateTriwulan" :max="maxDateTriwulan" @change="onDocDateChange($event.target.value, 'surat_pesanan_tanggal')" placeholder="dd/mm/yyyy"
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
                                    <input type="text" x-datepicker x-model="formData.kwitansi_tanggal" :min="minDateTriwulan" :max="maxDateTriwulan" @change="onDocDateChange($event.target.value, 'kwitansi_tanggal')" placeholder="dd/mm/yyyy"
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
                                        <input type="text" x-datepicker x-model="formData.faktur_tanggal" :min="minDateTriwulan" :max="maxDateTriwulan" @change="onDocDateChange($event.target.value, 'faktur_tanggal')" placeholder="dd/mm/yyyy"
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

                        <div :class="(formData.is_extracomtable || isAtb) ? 'grid grid-cols-1 md:grid-cols-3 gap-4' : 'grid grid-cols-1 md:grid-cols-2 gap-4'">
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
                            <template x-if="formData.is_extracomtable || isAtb">
                                <div>
                                    <label class="block text-amber-400 text-xs mb-1 font-semibold flex items-center space-x-1.5">
                                        <span>No Hp / Wa Yang Aktif</span>
                                        <span class="text-[10px] text-amber-400/80 font-normal">(Khusus Extracom & ATB)</span>
                                    </label>
                                    <input type="text" x-model="formData.penyedia_telepon" placeholder="Contoh: 0812-3456-7890 / 0852-9876-5432"
                                           class="w-full bg-slate-900 border border-amber-500/50 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-400">
                                </div>
                            </template>
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
                                    <th :colspan="(formData.is_extracomtable || isAtb) ? 6 : 5" class="py-2.5 border border-slate-600 bg-[#fde9d9] uppercase tracking-wider">
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
                                    <template x-if="formData.is_extracomtable || isAtb">
                                        <th rowspan="2" class="px-3 py-1.5 border border-slate-600 align-middle">No Hp / wa<br>Yang Aktif</th>
                                    </template>
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
                                    <td class="px-3 py-3 border border-slate-400 font-semibold" x-text="formData.penyedia_nama || '-'"></td>
                                    <td class="px-3 py-3 border border-slate-400" x-text="formData.penyedia_pemilik || '-'"></td>
                                    <template x-if="formData.is_extracomtable || isAtb">
                                        <td class="px-3 py-3 border border-slate-400 font-mono font-bold text-amber-900" x-text="formData.penyedia_telepon || '-'"></td>
                                    </template>
                                    <td class="px-2.5 py-3 border border-slate-400" x-text="formData.penyedia_rekening_nama || '-'"></td>
                                    <td class="px-2.5 py-3 border border-slate-400 font-mono font-bold text-amber-900" x-text="formData.penyedia_rekening_nomor || '-'"></td>
                                    <td class="px-4 py-3 border border-slate-400 text-left" x-text="formData.penyedia_alamat || '-'"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-bold" x-text="formData.ppk_nama || '-'"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.ppk_nip || '-'"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-left text-[10px]" x-text="formData.keterangan_tambahan || '-'"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <!-- Bottom Navigation Between Steps -->
            <div class="pt-6 sm:pt-8 mt-6 sm:mt-8 border-t border-slate-800 flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-between gap-3 sm:gap-4">
                <div class="w-full sm:w-auto">
                    <button type="button" x-show="currentStep > 1" @click="prevStep()"
                            class="w-full sm:w-auto px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all flex items-center justify-center space-x-2">
                        <span>&larr; Langkah Sebelumnya</span>
                    </button>
                </div>

                <div class="flex items-center justify-end space-x-2.5 sm:space-x-3 w-full sm:w-auto">
                    <a href="{{ route('astap.index') }}" class="flex-1 sm:flex-initial text-center px-4 sm:px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs transition-all">
                        Batal
                    </a>

                    <!-- Next Step Button -->
                    <button type="button" x-show="currentStep < totalSteps" @click="nextStep()"
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

        <!-- GLOBAL CUSTOM CONFIRMATION DIALOG MODAL (Sleek Dark Theme) -->
        <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div @click.away="showConfirmModal = false"
                 x-show="showConfirmModal"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-slate-900 border rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative"
                 :class="{
                     'border-rose-500/40': confirmData.type === 'danger',
                     'border-amber-500/40': confirmData.type === 'warning',
                     'border-emerald-500/40': confirmData.type === 'success',
                     'border-cyan-500/40': confirmData.type === 'info'
                 }">
                
                <!-- Header Icon & Title -->
                <div class="flex items-start space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 font-bold border"
                         :class="{
                             'bg-rose-500/20 text-rose-400 border-rose-500/30': confirmData.type === 'danger',
                             'bg-amber-500/20 text-amber-300 border-amber-500/30': confirmData.type === 'warning',
                             'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': confirmData.type === 'success',
                             'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': confirmData.type === 'info'
                         }">
                        <span x-text="confirmData.type === 'danger' ? '🗑️' : (confirmData.type === 'warning' ? '✏️' : '➕')"></span>
                    </div>
                    <div class="space-y-1 min-w-0 flex-1">
                        <h3 class="text-base font-extrabold text-white leading-snug" x-text="confirmData.title"></h3>
                        <p class="text-slate-300 text-xs leading-relaxed" x-text="confirmData.message"></p>
                    </div>
                </div>

                <!-- Item Target Preview Card -->
                <template x-if="confirmData.itemName">
                    <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Item Target:</span>
                        <p class="text-xs font-bold text-cyan-300 truncate font-mono" x-text="confirmData.itemName"></p>
                    </div>
                </template>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmedAction()"
                        class="px-5 py-2.5 rounded-xl font-extrabold text-xs shadow-lg transition-all active:scale-95 cursor-pointer flex items-center space-x-1.5"
                        :class="{
                            'bg-rose-500 hover:bg-rose-400 text-white shadow-rose-500/20': confirmData.type === 'danger',
                            'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/20': confirmData.type === 'warning',
                            'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/20': confirmData.type === 'success',
                            'bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-cyan-500/20': confirmData.type === 'info'
                        }">
                        <span x-text="confirmData.btnText"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- GLOBAL FLOATING TOAST NOTIFICATION POPUP -->
        <div x-show="toast.show" x-cloak
             x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4 scale-95"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform opacity-100 translate-y-0 scale-100"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="fixed bottom-6 right-6 z-50 max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3"
             :class="{
                 'border-emerald-500/40 text-emerald-300': toast.type === 'success',
                 'border-rose-500/40 text-rose-300': toast.type === 'error',
                 'border-amber-500/40 text-amber-300': toast.type === 'warning',
                 'border-cyan-500/40 text-cyan-300': toast.type === 'info'
             }">
            <div class="flex items-center space-x-2.5 min-w-0">
                <span class="text-base shrink-0" x-text="toast.type === 'success' ? '✅' : (toast.type === 'error' ? '⚠️' : 'ℹ️')"></span>
                <p class="text-xs font-bold leading-snug truncate" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white text-base font-bold shrink-0">&times;</button>
        </div>

    </div>
</x-layout>
