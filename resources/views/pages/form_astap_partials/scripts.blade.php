    <script>
        window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);
        window.dbJenisPengadaans = @json(!empty($dbJenisPengadaans) ? $dbJenisPengadaans : []);
        window.dbRekeningBelanjas = @json(!empty($dbRekeningBelanjas) ? $dbRekeningBelanjas : []);
        window.dbUnits = @json(!empty($dbUnits) ? $dbUnits : []);
        window.editingAstap = @json(!empty($astap) ? $astap : null);
        window.dbPenyedias = @json(!empty($dbPenyedias) ? $dbPenyedias : []);
        window.dbPejabats = @json(!empty($dbPejabats) ? $dbPejabats : []);

        function astapForm() {
            return {
                isEdit: {{ request()->routeIs('astap.edit') ? 'true' : 'false' }},
                currentStep: 1,
                totalSteps: 4,

                // Master Data Unit & Paviliun (Diisi dari Database RSUD)
                masterUnits: window.dbUnits || [],

                // Master Data Rekanan Penyedia & PPK dari Data ASTAP
                masterPenyedias: window.dbPenyedias || [],
                masterPejabats: window.dbPejabats || [],
                isPenyediaDropdownOpen: false,
                isPpkDropdownOpen: false,

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

                    // Pre-index Master Data 108 untuk performa super cepat (0ms lookup & zero GC overhead)
                    if (window.dbMasterJenisAstap108 && window.dbMasterJenisAstap108.length > 0) {
                        this.master108Map = {};
                        window.dbMasterJenisAstap108.forEach(j => {
                            const flatSubSub = [];
                            if (j.subRincian) {
                                j.subRincian.forEach(sr => {
                                    if (sr.subSubRincian) {
                                        sr.subSubRincian.forEach(ssr => {
                                            flatSubSub.push(ssr);
                                            this.master108Map[ssr.kode] = {
                                                subSub: ssr,
                                                subRincian: sr,
                                                jenis: j
                                            };
                                        });
                                    }
                                });
                            }
                            j._flatSubSub = flatSubSub;
                        });
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

                    // Watchers Langkah 2: Otomatis Muat Pagu Anggaran jika Sub Rincian / Tahun / TW / Ekstrakom berubah
                    this.$watch('formData.sub_rincian_kode', () => this.fetchExistingAnggaran());
                    this.$watch('formData.tahun_anggaran', () => this.fetchExistingAnggaran());
                    this.$watch('formData.triwulan', () => this.fetchExistingAnggaran());
                    this.$watch('formData.is_extracomtable', () => {
                        this.fetchExistingAnggaran();
                        this.syncRealisasiFromStep3();
                    });

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
                            is_extracomtable: this.formData.is_extracomtable ? 1 : 0,
                            exclude_id: this.isEdit && this.formData.id ? this.formData.id : null
                        })
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data && data.found && data.jumlah_anggaran) {
                            this.formData.jumlah_anggaran = data.jumlah_anggaran;
                            this.existingRealisasiDb = data.total_realisasi_existing || 0;
                            this.isAnggaranAutoLoaded = true;
                            const catLabel = this.formData.is_extracomtable ? 'Ekstrakomtabel' : 'Aset Reguler';
                            this.anggaranAutoLoadedMessage = '✨ Pagu anggaran (' + catLabel + ') Rp ' + Number(data.jumlah_anggaran).toLocaleString('id-ID') + ' dimuat otomatis dari penetapan ' + tw + ' ' + thn;
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
                    if (this.formData.is_extracomtable) {
                        currentVal = this.totalNilaiMesin;
                    } else if (this.isTanah) {
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

                syncExtracomOnModeSwitch() {
                    if (!this.formData.is_extracomtable) return;

                    const activeKode = this.formData.mesin_kode_barang || this.activeKodeBarang || this.formData.sub_rincian_kode || this.formData.jenis_aset_kode || '';
                    const activeNama = this.formData.mesin_nama_barang || this.activeNamaBarang || this.formData.sub_rincian_nama || 'Barang Ekstrakomtabel';

                    this.formData.mesin_kode_barang = activeKode;
                    this.formData.mesin_nama_barang = activeNama;

                    if (!this.formData.mesin_items || this.formData.mesin_items.length === 0) {
                        this.formData.mesin_items = [
                            {
                                mesin_nama_barang: activeNama,
                                mesin_kode_barang: activeKode,
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
                        ];
                    } else {
                        this.formData.mesin_items.forEach(it => {
                            if (!it.mesin_kode_barang && activeKode) it.mesin_kode_barang = activeKode;
                            if (!it.mesin_nama_barang || it.mesin_nama_barang === 'Barang Ekstrakomtabel') it.mesin_nama_barang = activeNama;
                        });
                    }

                    this.syncMesinFieldsToMain();
                    this.syncRealisasiFromStep3();
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
                            if (this.formData.mesin_nama_barang !== first.mesin_nama_barang) this.formData.mesin_nama_barang = first.mesin_nama_barang;
                        } else if (this.formData.mesin_nama_barang) {
                            first.mesin_nama_barang = this.formData.mesin_nama_barang;
                        }
                        if (first.mesin_kode_barang && first.mesin_kode_barang.trim() !== '') {
                            if (this.formData.mesin_kode_barang !== first.mesin_kode_barang) this.formData.mesin_kode_barang = first.mesin_kode_barang;
                        } else if (this.formData.mesin_kode_barang) {
                            first.mesin_kode_barang = this.formData.mesin_kode_barang;
                        }
                        const fields = ['mesin_merk', 'mesin_type', 'mesin_ukuran', 'mesin_no_pabrik', 'mesin_bahan', 'mesin_no_rangka', 'mesin_no_mesin', 'mesin_no_bpkb', 'mesin_no_polisi', 'mesin_kondisi', 'mesin_satuan'];
                        fields.forEach(f => {
                            if (this.formData[f] !== first[f]) this.formData[f] = first[f];
                        });
                        if (this.formData.ruang_pemegang !== first.ruang_pemegang) {
                            this.formData.ruang_pemegang = first.ruang_pemegang;
                            this.formData.ruang_pemegang_mesin = first.ruang_pemegang;
                        }

                        const totalVol = this.formData.mesin_items.reduce((sum, it) => sum + (parseInt(it.mesin_jumlah_barang) || 1), 0);
                        if (this.formData.mesin_jumlah_barang !== totalVol) {
                            this.formData.mesin_jumlah_barang = totalVol;
                            this.formData.jumlah_volume = totalVol;
                        }

                        const totalAdmin = this.formData.mesin_items.reduce((sum, it) => sum + (parseFloat(it.mesin_administrasi_proyek) || 0), 0);
                        if (this.formData.mesin_administrasi_proyek !== totalAdmin) {
                            this.formData.mesin_administrasi_proyek = totalAdmin;
                        }

                        if (this.formData.mesin_items.length === 1) {
                            if (this.formData.mesin_nilai_satuan !== first.mesin_nilai_satuan) {
                                this.formData.mesin_nilai_satuan = first.mesin_nilai_satuan;
                                this.formData.harga_satuan = first.mesin_nilai_satuan;
                            }
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
                    // 1. Ambil dari currentSubRincianObj jika sudah memilih sub-rincian spesifik
                    if (this.currentSubRincianObj && this.currentSubRincianObj.subSubRincian) {
                        return this.currentSubRincianObj.subSubRincian;
                    }

                    // 2. Jika belum, ambil dari pre-indexed flat array dari currentJenisAstap (0ms, zero allocations)
                    if (this.currentJenisAstap) {
                        if (!this.currentJenisAstap._flatSubSub) {
                            const flat = [];
                            (this.currentJenisAstap.subRincian || []).forEach(sr => {
                                if (sr.subSubRincian) {
                                    sr.subSubRincian.forEach(ssr => flat.push(ssr));
                                }
                            });
                            this.currentJenisAstap._flatSubSub = flat;
                        }
                        return this.currentJenisAstap._flatSubSub;
                    }

                    // 3. Fallback berdasarkan kelompok jenis aset
                    const targetGroupKode = this.isTanah ? '1.3.1' : (this.isMesin ? '1.3.2' : (this.isGedung ? '1.3.3' : (this.isJaringan ? '1.3.4' : (this.isAsetLainnya ? '1.3.5' : (this.isAtb ? '1.5.3' : (this.isKdp ? '1.3.6' : ''))))));
                    const matchedGroup = (window.dbMasterJenisAstap108 || []).find(j => j.kode === targetGroupKode);
                    if (matchedGroup) {
                        if (!matchedGroup._flatSubSub) {
                            const flat = [];
                            (matchedGroup.subRincian || []).forEach(sr => {
                                if (sr.subSubRincian) {
                                    sr.subSubRincian.forEach(ssr => flat.push(ssr));
                                }
                            });
                            matchedGroup._flatSubSub = flat;
                        }
                        return matchedGroup._flatSubSub;
                    }

                    return [];
                },

                searchNamaBarang108: '',
                isNamaBarang108Open: false,

                get filteredSubSubRincian108() {
                    // Jika dropdown tidak terbuka, jangan proses apa-apa (0 CPU overhead & 0 diff DOM)
                    if (!this.isNamaBarang108Open) {
                        return [];
                    }

                    const list = this.availableSubSubRincian108 || [];
                    const q = (this.searchNamaBarang108 || '').toLowerCase().trim();

                    // Jika tidak mencari, ambil 50 teratas saja
                    if (!q) {
                        return list.slice(0, 50);
                    }

                    // Pencarian cepat dengan batasan 50 hasil pertama
                    const results = [];
                    for (let i = 0; i < list.length; i++) {
                        const item = list[i];
                        if (!item) continue;
                        if ((item.nama && item.nama.toLowerCase().includes(q)) || 
                            (item.kode && item.kode.toLowerCase().includes(q))) {
                            results.push(item);
                            if (results.length >= 50) {
                                break;
                            }
                        }
                    }
                    return results;
                },

                // Getter (bukan fungsi biasa) agar Alpine cache hasil per reactive cycle.
                // Sebelumnya dipanggil 5-6x per render sebagai fungsi → sekarang 1x evaluasi.
                get activeKodeBarang() {
                    if (this.formData.is_extracomtable) return this.formData.mesin_kode_barang || '';
                    if (this.isTanah) return this.formData.tanah_kode_barang;
                    if (this.isMesin) return this.formData.mesin_kode_barang;
                    if (this.isGedung) return this.formData.gedung_kode_barang;
                    if (this.isJaringan) return this.formData.jaringan_kode_barang;
                    if (this.isAsetLainnya) return this.formData.lainnya_kode_barang;
                    if (this.isAtb) return this.formData.atb_kode_barang;
                    if (this.isKdp) return this.formData.kdp_kode_barang;
                    return '';
                },

                get activeNamaBarang() {
                    if (this.formData.is_extracomtable) return this.formData.mesin_nama_barang || '';
                    if (this.isTanah) return this.formData.tanah_nama_barang;
                    if (this.isMesin) return this.formData.mesin_nama_barang;
                    if (this.isGedung) return this.formData.gedung_nama_barang;
                    if (this.isJaringan) return this.formData.jaringan_nama_barang;
                    if (this.isAsetLainnya) return this.formData.lainnya_nama_barang;
                    if (this.isAtb) return this.formData.atb_nama_barang;
                    if (this.isKdp) return this.formData.kdp_nama_barang;
                    return '';
                },

                // Alias fungsi untuk backward compatibility (bagian lain yang mungkin masih memanggil sebagai fungsi)
                getActiveKodeBarang() { return this.activeKodeBarang; },
                getActiveNamaBarang() { return this.activeNamaBarang; },

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
                    if (!this.isRekeningOpen) {
                        return [];
                    }
                    const list = this.masterRekeningBelanja || [];
                    const q = (this.searchRekening || '').toLowerCase().trim();
                    if (!q) {
                        return list.slice(0, 30);
                    }
                    const results = [];
                    for (let i = 0; i < list.length; i++) {
                        const r = list[i];
                        if (!r) continue;
                        if ((r.kode_rek && r.kode_rek.toLowerCase().includes(q)) || 
                            (r.nama_belanja && r.nama_belanja.toLowerCase().includes(q)) ||
                            (r.kelompok && r.kelompok.toLowerCase().includes(q))) {
                            results.push(r);
                            if (results.length >= 31) break;
                        }
                    }
                    return results;
                },

                get filteredJenisAstap108() {
                    if (!this.isJenis108Open) {
                        return [];
                    }
                    let list = (this.masterJenisAstap108 || []).filter(j => j && j.kode && j.nama && j.nama.trim() !== '');
                    // Exclude "Aset Tetap Dalam Renovasi" (1.3.5.07)
                    list = list.filter(j => 
                        !(j.kode && j.kode.includes('1.3.5.07')) && 
                        !(j.nama && j.nama.toLowerCase().includes('dalam renovasi'))
                    );
                    const q = (this.searchJenis108 || '').toLowerCase().trim();
                    if (!q) {
                        return list;
                    }
                    return list.filter(j => 
                        (j.kode && j.kode.toLowerCase().includes(q)) || 
                        (j.nama && j.nama.toLowerCase().includes(q))
                    );
                },

                get filteredSubRincian108() {
                    if (!this.isSubRincian108Open) {
                        return [];
                    }
                    let list = (this.availableSubRincian108 || []).filter(s => s && s.kode && s.nama && s.nama.trim() !== '');
                    const q = (this.searchSubRincian108 || '').toLowerCase().trim();
                    if (!q) {
                        return list.slice(0, 30);
                    }
                    const results = [];
                    for (let i = 0; i < list.length; i++) {
                        const s = list[i];
                        if (!s) continue;
                        if ((s.kode && s.kode.toLowerCase().includes(q)) || 
                            (s.nama && s.nama.toLowerCase().includes(q))) {
                            results.push(s);
                            if (results.length >= 31) break;
                        }
                    }
                    return results;
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
                    const meta = (this.master108Map && this.master108Map[kodeSubSub]) ? this.master108Map[kodeSubSub] : null;
                    const found = meta ? meta.subSub : (this.availableSubSubRincian108 || []).find(s => s.kode === kodeSubSub);

                    if (this.formData.is_extracomtable) {
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
                    } else if (this.isTanah) {
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
                        if (found) {
                            this.formData.atb_nama_barang = found.nama;
                            if (this.formData.atb_items && this.formData.atb_items.length > 0) {
                                this.formData.atb_items.forEach(it => {
                                    it.atb_kode_barang = kodeSubSub;
                                    it.atb_nama_barang = found.nama;
                                });
                            }
                        }
                    } else if (this.isKdp) {
                        this.formData.kdp_kode_barang = kodeSubSub;
                        if (found) this.formData.kdp_nama_barang = found.nama;
                    }

                    // Otomatis terisi Sub Rincian Objek PMDN 108 & Jenis Aset jika Nama Barang di Langkah 2/3 dipilih (0ms lookup)
                    if (kodeSubSub) {
                        if (meta) {
                            if (meta.subRincian) {
                                this.formData.sub_rincian_kode = meta.subRincian.kode;
                                this.formData.sub_rincian_nama = meta.subRincian.nama;
                            }
                            if (meta.jenis) {
                                this.formData.jenis_aset_kode = meta.jenis.kode;
                                this.formData.jenis_aset_nama = meta.jenis.nama;
                            }
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

                validateStep3Assets() {
                    // Sinkronisasi data terlebih dahulu
                    if (this.formData.is_extracomtable) {
                        this.syncMesinFieldsToMain();
                    } else if (this.isTanah) this.syncTanahFieldsToMain();
                    else if (this.isMesin) this.syncMesinFieldsToMain();
                    else if (this.isGedung) this.syncGedungFieldsToMain();
                    else if (this.isAsetLainnya) this.syncLainnyaFieldsToMain();
                    else if (this.isAtb) this.syncAtbFieldsToMain();
                    else if (this.isKdp) this.syncKdpFieldsToMain();
                    this.syncRealisasiFromStep3();

                    const realisasi = Number(this.formData.jumlah_realisasi || 0);
                    const anggaran = Number(this.formData.jumlah_anggaran || 0);

                    if (realisasi <= 0) {
                        return {
                            valid: false,
                            message: '⚠️ Total Nilai Realisasi pada Langkah 3 belum diisi atau masih bernilai Rp 0!\n\nMohon lengkapi rincian harga/nilai perolehan barang pada Langkah 3 sebelum melanjutkan ke Langkah 4.'
                        };
                    }

                    // 1. Validasi Mode Ekstrakomtabel (Barang Satuan / Eceran <= Rp 300.000)
                    if (this.formData.is_extracomtable) {
                        const items = this.formData.mesin_items || [];
                        for (let i = 0; i < items.length; i++) {
                            const val = Number(items[i].mesin_nilai_satuan || 0);
                            const nama = items[i].mesin_nama_barang || this.activeNamaBarang || 'Barang Ekstrakomtabel';
                            if (val <= 0) {
                                return {
                                    valid: false,
                                    message: `⚠️ Nilai satuan barang Ekstrakomtabel item #${i + 1} (${nama}) belum diisi atau masih Rp 0!`
                                };
                            }
                            if (val > 300000) {
                                return {
                                    valid: false,
                                    message: `⚠️ Nilai Satuan Barang Ekstrakomtabel TIDAK BOLEH lebih dari Rp 300.000!\n\nItem #${i + 1} (${nama}) memiliki nilai satuan: Rp ${this.formatRupiah(val)}.\n\nSesuai regulasi, barang dengan nilai satuan di atas Rp 300.000 harus dicatat sebagai Aset Tetap Reguler (silakan pilih Kategori Reguler pada Langkah 2).`
                                };
                            }
                        }
                    } else {
                        // 2. Validasi Mode Aset Tetap Reguler (!is_extracomtable)
                        
                        // A. Kelompok Barang Satuan / Per Unit (Wajib > Rp 300.000)
                        if (this.isMesin) {
                            const items = this.formData.mesin_items || [];
                            for (let i = 0; i < items.length; i++) {
                                const val = Number(items[i].mesin_nilai_satuan || 0);
                                const nama = items[i].mesin_nama_barang || this.activeNamaBarang || 'Peralatan & Mesin';
                                if (val <= 0) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai satuan Peralatan & Mesin item #${i + 1} (${nama}) belum diisi atau masih Rp 0!`
                                    };
                                }
                                if (val <= 300000) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai Satuan Aset Peralatan & Mesin (Reguler) WAJIB lebih dari Rp 300.000!\n\nItem #${i + 1} (${nama}) bernilai: Rp ${this.formatRupiah(val)}.\n\nBarang dengan nilai satuan ≤ Rp 300.000 bukan aset tetap reguler, melainkan harus dicatat sebagai Aset Ekstrakomtabel (silakan pilih mode Ekstrakomtabel pada Langkah 2).`
                                    };
                                }
                            }
                        }

                        if (this.isAsetLainnya) {
                            const items = this.formData.lainnya_items || [];
                            for (let i = 0; i < items.length; i++) {
                                const val = Number(items[i].lainnya_nilai_satuan || 0);
                                const nama = items[i].lainnya_nama_barang || this.activeNamaBarang || 'Aset Tetap Lainnya';
                                if (val <= 0) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai satuan Aset Tetap Lainnya item #${i + 1} (${nama}) belum diisi atau masih Rp 0!`
                                    };
                                }
                                if (val <= 300000) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai Satuan Aset Tetap Lainnya WAJIB lebih dari Rp 300.000!\n\nItem #${i + 1} (${nama}) bernilai: Rp ${this.formatRupiah(val)}.\n\nBarang dengan nilai satuan ≤ Rp 300.000 tidak memenuhi batas kapitalisasi Aset Tetap Reguler.`
                                    };
                                }
                            }
                        }

                        if (this.isAtb) {
                            const items = this.formData.atb_items || [];
                            for (let i = 0; i < items.length; i++) {
                                const val = Number(items[i].atb_nilai_satuan || 0);
                                const nama = items[i].atb_nama_barang || this.activeNamaBarang || 'Aset Tidak Berwujud';
                                if (val <= 0) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai satuan Aset Tidak Berwujud (ATB) item #${i + 1} (${nama}) belum diisi atau masih Rp 0!`
                                    };
                                }
                                if (val <= 300000) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai Satuan Aset Tidak Berwujud (ATB) WAJIB lebih dari Rp 300.000!\n\nItem #${i + 1} (${nama}) bernilai: Rp ${this.formatRupiah(val)}.\n\nNilai perolehan ≤ Rp 300.000 tidak memenuhi batas kapitalisasi Aset Tidak Berwujud.`
                                    };
                                }
                            }
                        }

                        // B. Kelompok Aset Fisik / Konstruksi / Bidang / Paket (Subtotal Perolehan Wajib >= Rp 300.000)
                        if (this.isTanah) {
                            const items = this.formData.tanah_items || [];
                            for (let i = 0; i < items.length; i++) {
                                const subtotal = this.getTanahSubtotal(items[i]);
                                const nama = items[i].tanah_nama_barang || this.activeNamaBarang || 'Tanah';
                                if (subtotal <= 0) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai perolehan bidang tanah #${i + 1} (${nama}) belum diisi atau masih Rp 0!`
                                    };
                                }
                                if (subtotal < 300000) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Subtotal Nilai Perolehan Bidang Tanah minimal Rp 300.000!\n\nBidang #${i + 1} (${nama}) memiliki subtotal: Rp ${this.formatRupiah(subtotal)}.\n\nNilai perolehan (Perencanaan + Fisik + Pengawasan) tidak boleh di bawah Rp 300.000 untuk dapat dikapitalisasi sebagai Aset Tetap.`
                                    };
                                }
                            }
                        }

                        if (this.isGedung) {
                            const items = this.formData.gedung_items || [];
                            for (let i = 0; i < items.length; i++) {
                                const subtotal = this.getGedungSubtotal(items[i]);
                                const nama = items[i].gedung_nama_barang || this.activeNamaBarang || 'Gedung & Bangunan';
                                if (subtotal <= 0) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai perolehan gedung/bangunan #${i + 1} (${nama}) belum diisi atau masih Rp 0!`
                                    };
                                }
                                if (subtotal < 300000) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Subtotal Nilai Perolehan Gedung/Bangunan minimal Rp 300.000!\n\nBangunan #${i + 1} (${nama}) memiliki subtotal: Rp ${this.formatRupiah(subtotal)}.\n\nNilai perolehan gedung & bangunan tidak boleh di bawah Rp 300.000 untuk dapat dikapitalisasi sebagai Aset Tetap.`
                                    };
                                }
                            }
                        }

                        if (this.isJaringan) {
                            const items = this.formData.jaringan_items || [];
                            for (let i = 0; i < items.length; i++) {
                                const subtotal = this.getJaringanSubtotal(items[i]);
                                const nama = items[i].jaringan_nama_barang || this.activeNamaBarang || 'Jalan, Irigasi & Jaringan';
                                if (subtotal <= 0) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai perolehan jaringan/irigasi #${i + 1} (${nama}) belum diisi atau masih Rp 0!`
                                    };
                                }
                                if (subtotal < 300000) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Subtotal Nilai Perolehan Jaringan/Irigasi minimal Rp 300.000!\n\nRuas/Paket #${i + 1} (${nama}) memiliki subtotal: Rp ${this.formatRupiah(subtotal)}.\n\nNilai perolehan jaringan tidak boleh di bawah Rp 300.000 untuk dapat dikapitalisasi sebagai Aset Tetap.`
                                    };
                                }
                            }
                        }

                        if (this.isKdp) {
                            const items = this.formData.kdp_items || [];
                            for (let i = 0; i < items.length; i++) {
                                const subtotal = this.getKdpSubtotal(items[i]);
                                const nama = items[i].kdp_nama_barang || this.activeNamaBarang || 'Konstruksi Dalam Pengerjaan';
                                if (subtotal <= 0) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Nilai perolehan KDP #${i + 1} (${nama}) belum diisi atau masih Rp 0!`
                                    };
                                }
                                if (subtotal < 300000) {
                                    return {
                                        valid: false,
                                        message: `⚠️ Subtotal Nilai Perolehan KDP minimal Rp 300.000!\n\nProyek KDP #${i + 1} (${nama}) memiliki subtotal: Rp ${this.formatRupiah(subtotal)}.\n\nNilai perolehan KDP tidak boleh di bawah Rp 300.000 untuk dapat dikapitalisasi sebagai Aset Tetap.`
                                    };
                                }
                            }
                        }
                    }

                    // 3. Cek apakah Total Realisasi melebihi Anggaran
                    if (anggaran > 0 && realisasi > anggaran) {
                        const selisih = realisasi - anggaran;
                        return {
                            valid: false,
                            message: `⚠️ Total Nilai Realisasi MELEBIHI Pagu Anggaran!\n\n• Pagu Anggaran: Rp ${this.formatRupiah(anggaran)}\n• Total Realisasi: Rp ${this.formatRupiah(realisasi)}\n• Selisih Kelebihan: Rp ${this.formatRupiah(selisih)}\n\nMohon sesuaikan rincian nilai barang pada Langkah 3 sebelum lanjut ke Langkah 4.`
                        };
                    }

                    return { valid: true };
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
                                const valResult = this.validateStep3Assets();
                                if (!valResult.valid) {
                                    this.toast = { 
                                        show: true, 
                                        message: valResult.message.split('\n')[0], 
                                        type: 'warning' 
                                    };
                                    alert(valResult.message);
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
                    if (this.formData.is_extracomtable) {
                        this.syncMesinFieldsToMain();
                    } else if (this.isTanah) this.syncTanahFieldsToMain();
                    else if (this.isMesin) this.syncMesinFieldsToMain();
                    else if (this.isGedung) this.syncGedungFieldsToMain();
                    else if (this.isAsetLainnya) this.syncLainnyaFieldsToMain();
                    else if (this.isAtb) this.syncAtbFieldsToMain();
                    else if (this.isKdp) this.syncKdpFieldsToMain();
                    this.syncRealisasiFromStep3();

                    const valResult = this.validateStep3Assets();
                    if (!valResult.valid) {
                        this.toast = { 
                            show: true, 
                            message: valResult.message.split('\n')[0], 
                            type: 'warning' 
                        };
                        alert(valResult.message);
                        this.currentStep = 3;
                        this.scrollToTop();
                        return;
                    }

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const astapId = '{{ $id ?? "" }}';
                    const isEdit = this.isEdit && astapId;

                    // Ambil kode 108 aktif berdasarkan jenis aset (dengan fallback ke sub rincian / jenis aset)
                    const activeKode108 = this.formData.is_extracomtable ? (this.formData.mesin_kode_barang || this.formData.sub_rincian_kode || this.formData.jenis_aset_kode)
                        : (this.isTanah ? (this.formData.tanah_kode_barang || this.formData.sub_rincian_kode || this.formData.jenis_aset_kode) 
                        : (this.isMesin ? (this.formData.mesin_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isGedung ? (this.formData.gedung_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isJaringan ? (this.formData.jaringan_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isAsetLainnya ? (this.formData.lainnya_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isAtb ? (this.formData.atb_kode_barang || this.formData.sub_rincian_kode)
                        : (this.isKdp ? (this.formData.kdp_kode_barang || this.formData.sub_rincian_kode) : '')))))));

                    if (!activeKode108) {
                        this.toast = { show: true, message: '⚠️ Mohon pilih Nama Barang (Sub-Sub Rincian PMDN 108) terlebih dahulu!', type: 'warning' };
                        setTimeout(() => { this.toast.show = false; }, 4000);
                        this.currentStep = this.isTanah ? 2 : 3;
                        return;
                    }

                    const namaBarangActive = this.formData.is_extracomtable ? (this.formData.mesin_nama_barang || this.formData.sub_rincian_nama || 'Barang Ekstrakomtabel')
                        : (this.isTanah ? (this.formData.tanah_nama_barang || this.formData.sub_rincian_nama || this.formData.jenis_aset_nama || 'Tanah')
                        : (this.isMesin ? (this.formData.mesin_nama_barang || this.formData.sub_rincian_nama || 'Peralatan dan Mesin')
                        : (this.isGedung ? (this.formData.gedung_nama_barang || this.formData.sub_rincian_nama || 'Gedung dan Bangunan')
                        : (this.isJaringan ? (this.formData.jaringan_nama_barang || this.formData.sub_rincian_nama || 'Jalan, Irigasi dan Jaringan')
                        : (this.isAsetLainnya ? (this.formData.lainnya_nama_barang || this.formData.sub_rincian_nama || 'Aset Tetap Lainnya')
                        : (this.isAtb ? (this.formData.atb_nama_barang || this.formData.sub_rincian_nama || 'Aset Tidak Berwujud')
                        : (this.isKdp ? (this.formData.kdp_nama_barang || this.formData.sub_rincian_nama || 'Konstruksi Dalam Pengerjaan') : 'Aset Tetap')))))));

                    const tahun = this.formData.tahun_perolehan || new Date().getFullYear();

                    const performHttpSave = () => {
                        const url = isEdit ? '/astap/' + astapId : '/astap';
                        const method = isEdit ? 'PUT' : 'POST';

                        const payload = { ...this.formData };
                        const allItemArrays = ['tanah_items', 'mesin_items', 'gedung_items', 'jaringan_items', 'lainnya_items', 'atb_items', 'kdp_items'];
                        let activeArray = null;
                        if (this.formData.is_extracomtable) {
                            activeArray = 'mesin_items';
                            payload.mesin_kode_barang = this.activeKodeBarang || this.formData.mesin_kode_barang || this.formData.sub_rincian_kode;
                            payload.mesin_nama_barang = this.activeNamaBarang || this.formData.mesin_nama_barang || this.formData.sub_rincian_nama || 'Barang Ekstrakomtabel';
                        } else if (this.isTanah) {
                            activeArray = 'tanah_items';
                        } else if (this.isMesin) {
                            activeArray = 'mesin_items';
                        } else if (this.isGedung) {
                            activeArray = 'gedung_items';
                        } else if (this.isJaringan) {
                            activeArray = 'jaringan_items';
                        } else if (this.isAsetLainnya) {
                            activeArray = 'lainnya_items';
                        } else if (this.isAtb) {
                            activeArray = 'atb_items';
                        } else if (this.isKdp) {
                            activeArray = 'kdp_items';
                        }

                        allItemArrays.forEach(arrKey => {
                            if (arrKey !== activeArray) {
                                delete payload[arrKey];
                            }
                        });

                        // Jika Ekstrakomtabel, pastikan item-item terisi kode barang, nama barang, dan satuannya
                        if (this.formData.is_extracomtable && Array.isArray(payload.mesin_items)) {
                            payload.mesin_items = payload.mesin_items.map(it => ({
                                ...it,
                                mesin_kode_barang: it.mesin_kode_barang || payload.mesin_kode_barang || this.formData.mesin_kode_barang || this.formData.sub_rincian_kode,
                                mesin_nama_barang: it.mesin_nama_barang || payload.mesin_nama_barang || this.formData.mesin_nama_barang || this.formData.sub_rincian_nama || 'Barang Ekstrakomtabel',
                                mesin_satuan: it.mesin_satuan || 'Unit'
                            }));
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
                },

                // =========================================================================
                // AUTOCOMPLETE & AUTO-FILL PENYEDIA (REKANAN) & PPK DARI DATA ASTAP
                // =========================================================================
                get filteredPenyediaList() {
                    let list = this.masterPenyedias || [];
                    const q = (this.formData.penyedia_nama || '').trim().toLowerCase();
                    if (!q) return list.slice(0, 8);
                    return list.filter(p => {
                        const n = (p.nama || '').toLowerCase();
                        const m = (p.pemilik || '').toLowerCase();
                        const a = (p.alamat || '').toLowerCase();
                        return n.includes(q) || m.includes(q) || a.includes(q);
                    }).slice(0, 8);
                },

                selectPenyedia(p) {
                    if (!p) return;
                    this.formData.penyedia_nama = p.nama || '';
                    if (p.pemilik) this.formData.penyedia_pemilik = p.pemilik;
                    if (p.telepon) this.formData.penyedia_telepon = p.telepon;
                    if (p.rekening_nama) this.formData.penyedia_rekening_nama = p.rekening_nama;
                    if (p.rekening_nomor) this.formData.penyedia_rekening_nomor = p.rekening_nomor;
                    if (p.alamat) this.formData.penyedia_alamat = p.alamat;
                    this.isPenyediaDropdownOpen = false;
                    this.toast = {
                        show: true,
                        message: 'Data Rekanan "' + p.nama + '" berhasil dimuat otomatis.',
                        type: 'success'
                    };
                    setTimeout(() => { this.toast.show = false; }, 3500);
                },

                onPenyediaInput() {
                    this.isPenyediaDropdownOpen = true;
                    const q = (this.formData.penyedia_nama || '').trim().toLowerCase();
                    if (!q) return;
                    const match = (this.masterPenyedias || []).find(p => (p.nama || '').trim().toLowerCase() === q);
                    if (match) {
                        if (!this.formData.penyedia_pemilik && match.pemilik) this.formData.penyedia_pemilik = match.pemilik;
                        if (!this.formData.penyedia_telepon && match.telepon) this.formData.penyedia_telepon = match.telepon;
                        if (!this.formData.penyedia_rekening_nama && match.rekening_nama) this.formData.penyedia_rekening_nama = match.rekening_nama;
                        if (!this.formData.penyedia_rekening_nomor && match.rekening_nomor) this.formData.penyedia_rekening_nomor = match.rekening_nomor;
                        if (!this.formData.penyedia_alamat && match.alamat) this.formData.penyedia_alamat = match.alamat;
                    }
                },

                clearPenyedia() {
                    this.formData.penyedia_nama = '';
                    this.isPenyediaDropdownOpen = false;
                },

                get filteredPpkList() {
                    let list = this.masterPejabats || [];
                    const q = (this.formData.ppk_nama || '').trim().toLowerCase();
                    if (!q) return list.slice(0, 8);
                    return list.filter(k => {
                        const n = (k.nama || '').toLowerCase();
                        const nip = (k.nip || '').toLowerCase();
                        return n.includes(q) || nip.includes(q);
                    }).slice(0, 8);
                },

                selectPpk(k) {
                    if (!k) return;
                    this.formData.ppk_nama = k.nama || '';
                    if (k.nip) this.formData.ppk_nip = k.nip;
                    this.isPpkDropdownOpen = false;
                    this.toast = {
                        show: true,
                        message: 'Data PPK "' + k.nama + '" berhasil dimuat otomatis.',
                        type: 'success'
                    };
                    setTimeout(() => { this.toast.show = false; }, 3500);
                },

                onPpkInput() {
                    this.isPpkDropdownOpen = true;
                    const q = (this.formData.ppk_nama || '').trim().toLowerCase();
                    if (!q) return;
                    const match = (this.masterPejabats || []).find(k => (k.nama || '').trim().toLowerCase() === q);
                    if (match && match.nip && !this.formData.ppk_nip) {
                        this.formData.ppk_nip = match.nip;
                    }
                },

                clearPpk() {
                    this.formData.ppk_nama = '';
                    this.isPpkDropdownOpen = false;
                }
            };
        }
    </script>
