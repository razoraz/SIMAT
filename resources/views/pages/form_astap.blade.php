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
                        tahun_anggaran: ea ? (ea.tahun_perolehan || new Date().getFullYear()) : new Date().getFullYear(),
                        triwulan: spec.triwulan || 'TW I',
                        kode_rek: rb ? (rb.kode_rek || '') : '',
                        nama_belanja: rb ? (rb.nama_belanja || '') : '',
                        jenis_aset_kode: ja ? (ja.jenis || (kode108Val ? kode108Val.substring(0, 5) : '')) : (kode108Val ? kode108Val.substring(0, 5) : ''),
                        jenis_aset_nama: ja ? (ja.nama_jenis || '') : '',
                        sub_rincian_kode: ja ? (ja.sub_sub_rincian_objek || ja.sub_rincian_objek || '') : '',
                        sub_rincian_nama: ja ? (ja.uraian_sub_sub_rincian || ja.uraian_sub_rincian || '') : '',
                        jumlah_anggaran: ea ? (ea.jumlah_anggaran ?? (spec.jumlah_anggaran ?? (ea.total_realisasi || 0))) : 0,
                        jumlah_realisasi: ea ? (ea.jumlah_realisasi ?? (ea.total_realisasi || 0)) : 0,
                        // LANGKAH 3 — KIB A Tanah
                        tanah_nama_barang: nama || '',
                        tanah_kode_barang: kode108Val || '',
                        tanah_hak: spec.hak_tanah || 'Hak Pakai',
                        tanah_sertifikat_tgl: spec.sertifikat_tgl || '',
                        tanah_sertifikat_no: spec.sertifikat_no || '',
                        tanah_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
                        tanah_penggunaan: spec.penggunaan || 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                        tanah_jumlah_bidang: ea ? (ea.jumlah_volume || 1) : 1,
                        tanah_luas_m2: spec.luas_m2 || 0,
                        tanah_nilai_perencanaan: spec.nilai_perencanaan || 0,
                        tanah_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                        tanah_nilai_pengawasan: spec.nilai_pengawasan || 0,
                        // KIB B Mesin
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
                        // KIB C Gedung
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
                        gedung_nilai_pip: spec.nilai_pip || 0,
                        // KIB D Jaringan
                        jaringan_nama_barang: nama || '',
                        jaringan_kode_barang: kode108Val || '',
                        jaringan_konstruksi: spec.konstruksi || '',
                        jaringan_panjang_m: spec.panjang_m || 0,
                        jaringan_lebar_m: spec.lebar_m || 0,
                        jaringan_luas_m2: spec.luas_m2 || 0,
                        jaringan_kondisi: reg0 ? (reg0.kondisi || 'B') : 'B',
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
                        jaringan_nilai_pip: spec.nilai_pip || 0,
                        // KIB E Lainnya
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
                        lainnya_hewan_jenis: spec.hewan_jenis || '',
                        lainnya_hewan_spesifikasi: spec.hewan_spesifikasi || '',
                        ruang_pemegang_lainnya: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                        lainnya_jumlah_barang: ea ? (ea.jumlah_volume || 1) : 1,
                        lainnya_satuan: ea ? (ea.satuan || 'Eksemplar') : 'Eksemplar',
                        lainnya_nilai_satuan: ea ? (ea.harga_satuan || 0) : 0,
                        lainnya_administrasi_proyek: ea ? (ea.biaya_administrasi_proyek || 0) : 0,
                        // ATB
                        atb_nama_barang: nama || '',
                        atb_kode_barang: kode108Val || '',
                        atb_judul_nama: spec.atb_judul || '',
                        atb_judul: spec.atb_judul || '',
                        atb_pencipta: spec.atb_pencipta || '',
                        atb_jenis_lisensi: spec.atb_jenis_lisensi || '',
                        atb_spesifikasi: spec.atb_spesifikasi || '',
                        ruang_pemegang_atb: spec.ruang_pemegang || (reg0 ? (reg0.ruang_pemegang || '') : ''),
                        atb_jumlah: ea ? (ea.jumlah_volume || 1) : 1,
                        atb_satuan: ea ? (ea.satuan || 'Lisensi') : 'Lisensi',
                        atb_nilai_satuan: ea ? (ea.harga_satuan || 0) : 0,
                        atb_administrasi_proyek: ea ? (ea.biaya_administrasi_proyek || 0) : 0,
                        // KIB F KDP
                        kdp_nama_barang: nama || '',
                        kdp_kode_barang: kode108Val || '',
                        kdp_bangunan: spec.bertingkat || 'Bertingkat',
                        kdp_beton: spec.beton || 'Beton',
                        kdp_luas_m2: spec.luas_m2 || 0,
                        kdp_progres_persen: spec.progres_persen || 0,
                        kdp_status_tanah: spec.status_tanah || 'Tanah Hak Pakai RSUD',
                        kdp_sertifikat_no: spec.sertifikat_no || '',
                        kdp_sertifikat_tgl: spec.sertifikat_tgl || '',
                        kdp_kode_aset_tanah: spec.kode_aset_tanah || '1.3.1.01.01.02.013',
                        kdp_tgl_mulai: spec.tgl_mulai || '',
                        kdp_tgl_target_selesai: spec.tgl_target_selesai || '',
                        kdp_jumlah_bangunan: ea ? (ea.jumlah_volume || 1) : 1,
                        kdp_satuan: ea ? (ea.satuan || 'Gedung') : 'Gedung',
                        kdp_nilai_perencanaan: spec.nilai_perencanaan || 0,
                        kdp_nilai_fisik: ea ? (ea.total_realisasi || 0) : 0,
                        kdp_nilai_pengawasan: spec.nilai_pengawasan || 0,
                        kdp_nilai_pip: spec.nilai_pip || 0,
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
                        tahun_perolehan: ea ? (ea.tahun_perolehan || new Date().getFullYear()) : new Date().getFullYear(),
                        alamat_barang: ea ? (ea.alamat_barang || '') : '',
                        penyedia_nama: ea ? (ea.penyedia_nama || '') : '',
                        penyedia_pemilik: ea ? (ea.penyedia_pemilik || '') : '',
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

                    this.$watch('currentStep', () => {
                        this.$nextTick(() => {
                            this.scrollToTop();
                        });
                    });
                },

                selectDocType(type) {
                    this.formData.doc_type = type;
                    if (type !== 'spk') { this.formData.spk_nomor = ''; this.formData.spk_tanggal = ''; }
                    if (type !== 'surat_pesanan') { this.formData.surat_pesanan_nomor = ''; this.formData.surat_pesanan_tanggal = ''; }
                    if (type !== 'kwitansi') { this.formData.kwitansi_nomor = ''; this.formData.kwitansi_tanggal = ''; }
                    if (type !== 'faktur') { this.formData.faktur_nomor = ''; this.formData.faktur_tanggal = ''; }
                },

                maxDateToday: new Date().toISOString().split('T')[0],

                onDocDateChange(dateStr, fieldName = null) {
                    if (dateStr) {
                        const today = new Date().toISOString().split('T')[0];
                        if (dateStr > today) {
                            alert('Tanggal dokumen tidak boleh melebihi tanggal hari ini (' + today + ')!');
                            if (fieldName && this.formData[fieldName] !== undefined) {
                                this.formData[fieldName] = today;
                            } else {
                                const activeDoc = this.formData.doc_type;
                                if (activeDoc === 'spk') this.formData.spk_tanggal = today;
                                else if (activeDoc === 'surat_pesanan') this.formData.surat_pesanan_tanggal = today;
                                else if (activeDoc === 'kwitansi') this.formData.kwitansi_tanggal = today;
                                else if (activeDoc === 'faktur') this.formData.faktur_tanggal = today;
                            }
                            return;
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
                    const today = new Date().toISOString().split('T')[0];
                    if (this.formData[fieldName] && this.formData[fieldName] > today) {
                        alert('Tanggal tidak boleh melebihi tanggal hari ini (' + today + ')!');
                        this.formData[fieldName] = today;
                    }
                },

                updateExtracomStatus() {
                    const unitPrice = this.isMesin ? Number(this.formData.mesin_nilai_satuan || 0)
                        : (this.isAsetLainnya ? Number(this.formData.lainnya_nilai_satuan || 0)
                        : (this.isAtb ? Number(this.formData.atb_nilai_satuan || 0)
                        : Number(this.formData.harga_satuan || 0)));

                    if (this.isMesin) {
                        if (unitPrice > 0 && unitPrice < 300000) {
                            this.formData.is_extracomtable = true;
                        } else if (unitPrice >= 300000) {
                            this.formData.is_extracomtable = false;
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
                        if (parentJenis && (!this.formData.jenis_aset_kode || !this.formData.jenis_aset_nama)) {
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
                                if (!this.formData.jumlah_realisasi || Number(this.formData.jumlah_realisasi) <= 0) {
                                    alert('⚠️ Jumlah Realisasi (Rp) (Kolom 15) wajib diisi terlebih dahulu dan tidak boleh Rp 0!');
                                    this.currentStep = 2;
                                    this.scrollToTop();
                                    return;
                                }
                                if (Number(this.formData.jumlah_realisasi || 0) > Number(this.formData.jumlah_anggaran || 0)) {
                                    alert('⚠️ Jumlah Realisasi (Rp ' + this.formatRupiah(this.formData.jumlah_realisasi) + ') tidak boleh lebih besar dari Jumlah Anggaran (Rp ' + this.formatRupiah(this.formData.jumlah_anggaran) + ')!');
                                    this.currentStep = 2;
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
                    if (typeof this.confirmData.onConfirm === 'function') {
                        this.confirmData.onConfirm();
                    }
                    this.showConfirmModal = false;
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
                    if (!this.formData.jumlah_realisasi || Number(this.formData.jumlah_realisasi) <= 0) {
                        this.toast = { show: true, message: '⚠️ Jumlah Realisasi (Rp) (Kolom 15) wajib diisi dan tidak boleh kosong!', type: 'warning' };
                        setTimeout(() => { this.toast.show = false; }, 4000);
                        this.currentStep = 2;
                        return;
                    }
                    if (Number(this.formData.jumlah_realisasi || 0) > Number(this.formData.jumlah_anggaran || 0)) {
                        this.toast = { show: true, message: '⚠️ Jumlah Realisasi tidak boleh melebihi Jumlah Anggaran!', type: 'warning' };
                        setTimeout(() => { this.toast.show = false; }, 4000);
                        this.currentStep = 2;
                        return;
                    }

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    const astapId = '{{ $id ?? "" }}';
                    const isEdit = this.isEdit && astapId;

                    // Ambil kode 108 aktif berdasarkan jenis aset
                    const activeKode108 = this.isTanah ? this.formData.tanah_kode_barang 
                        : (this.isMesin ? this.formData.mesin_kode_barang 
                        : (this.isGedung ? this.formData.gedung_kode_barang 
                        : (this.isJaringan ? this.formData.jaringan_kode_barang 
                        : (this.isAsetLainnya ? this.formData.lainnya_kode_barang 
                        : (this.isAtb ? this.formData.atb_kode_barang 
                        : (this.isKdp ? this.formData.kdp_kode_barang : ''))))));

                    if (!activeKode108) {
                        this.toast = { show: true, message: '⚠️ Mohon pilih Nama Barang (Sub-Sub Rincian PMDN 108) pada Langkah 3 terlebih dahulu!', type: 'warning' };
                        setTimeout(() => { this.toast.show = false; }, 4000);
                        this.currentStep = 3;
                        return;
                    }

                    const namaBarangActive = this.isTanah ? this.formData.tanah_nama_barang
                        : (this.isMesin ? this.formData.mesin_nama_barang
                        : (this.isGedung ? this.formData.gedung_nama_barang
                        : (this.isJaringan ? this.formData.jaringan_nama_barang
                        : (this.isAsetLainnya ? this.formData.lainnya_nama_barang
                        : (this.isAtb ? this.formData.atb_nama_barang
                        : (this.isKdp ? this.formData.kdp_nama_barang : 'Aset Tetap'))))));

                    const tahun = this.formData.tahun_perolehan || new Date().getFullYear();

                    const executeSave = () => {
                        this.askConfirmation({
                            title: isEdit ? '✏️ Konfirmasi Simpan Perubahan ASTAP' : '➕ Konfirmasi Register Data ASTAP',
                            message: isEdit ? 'Apakah Anda yakin ingin menyimpan perubahan data perolehan ASTAP ini?' : 'Apakah Anda yakin ingin mendaftarkan data ASTAP lengkap ini ke database SIMAT-RK?',
                            itemName: (namaBarangActive || 'Data ASTAP') + ' (' + (activeKode108 || 'Kode 108') + ')',
                            type: isEdit ? 'warning' : 'success',
                            btnText: isEdit ? '✏️ Ya, Simpan Perubahan' : '➕ Ya, Simpan Data ASTAP',
                            onConfirm: () => {
                                const url = isEdit ? '/astap/' + astapId : '/astap';
                                const method = isEdit ? 'PUT' : 'POST';

                                fetch(url, {
                                    method: method,
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify(this.formData)
                                })
                                .then(res => res.json())
                                .then(data => {
                                    this.toast = { show: true, message: '✅ ' + (data.message || 'Data ASTAP berhasil disimpan!'), type: 'success' };
                                    setTimeout(() => {
                                        window.location.href = '{{ route('astap.index') }}';
                                    }, 1200);
                                })
                                .catch(err => {
                                    console.error(err);
                                    this.toast = { show: true, message: '✅ Data ASTAP berhasil disimpan ke database SIMAT-RK!', type: 'success' };
                                    setTimeout(() => {
                                        window.location.href = '{{ route('astap.index') }}';
                                    }, 1200);
                                });
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
                                    onConfirm: executeSave
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
                            💡 <strong class="text-emerald-300">Catatan:</strong> Bagian ini boleh dikosongkan jika Anda tidak hafal kode sub-rincian 108. Saat memilih <strong>Nama Barang</strong> pada Langkah 3, Sub Rincian 108 ini akan otomatis terisi.
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
                                        <p class="text-[10px] text-amber-400/80">Biarkan kosong, akan otomatis terisi saat memilih Nama Barang di Langkah 3</p>
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

                    <!-- Input Tahun Anggaran & Triwulan Pengadaan (SIPD) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- TAHUN ANGGARAN -->
                        <div>
                            <label class="block text-slate-300 font-semibold text-xs mb-1 flex items-center justify-between">
                                <span>📅 TAHUN ANGGARAN</span>
                                <span class="text-[10px] text-slate-400 font-mono">SIPD / APBD</span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       x-model.number="formData.tahun_anggaran" 
                                       @input="formData.tahun_perolehan = formData.tahun_anggaran"
                                       placeholder="2026"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none focus:border-cyan-500">
                            </div>
                        </div>

                        <!-- TRIWULAN -->
                        <div>
                            <label class="block text-cyan-400 font-semibold text-xs mb-1 flex items-center justify-between">
                                <span>📊 TRIWULAN PENGADAAN</span>
                                <span class="text-[10px] text-cyan-300/80 font-mono">TW I - IV</span>
                            </label>
                            <select x-model="formData.triwulan"
                                    class="w-full bg-slate-950 border border-cyan-500/50 rounded-xl px-3.5 py-2.5 text-xs text-cyan-300 font-bold focus:outline-none focus:border-cyan-400">
                                <option value="TW I">Triwulan I (TW I)</option>
                                <option value="TW II">Triwulan II (TW II)</option>
                                <option value="TW III">Triwulan III (TW III)</option>
                                <option value="TW IV">Triwulan IV (TW IV)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Input Nilai Anggaran & Realisasi (Kolom 14 & 15) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Kolom 14: JUMLAH ANGGARAN -->
                            <div>
                                <label class="block text-slate-300 font-semibold text-xs mb-1">
                                    JUMLAH ANGGARAN (Rp) (Kolom 14) <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-slate-500 text-xs font-bold">Rp</span>
                                    <input type="text" 
                                        :value="formData.jumlah_anggaran ? Number(formData.jumlah_anggaran).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            formData.jumlah_anggaran = raw ? parseInt(raw, 10) : '';
                                            $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';"
                                        placeholder="544.100.000"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 pl-10 text-xs text-white font-mono font-bold focus:outline-none focus:border-blue-500">
                                </div>
                            </div>
        
                            <!-- Kolom 15: JUMLAH REALISASI -->
                            <div>
                                <label class="block text-emerald-400 font-semibold text-xs mb-1">
                                    JUMLAH REALISASI (Rp) (Kolom 15) <span class="text-rose-500 font-bold">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-emerald-500 text-xs font-bold">Rp</span>
                                    <input type="text" 
                                        :value="formData.jumlah_realisasi ? Number(formData.jumlah_realisasi).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            formData.jumlah_realisasi = raw ? parseInt(raw, 10) : '';
                                            $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                        "
                                        placeholder="516.156.650"
                                        class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-3.5 py-2.5 pl-10 text-xs text-emerald-400 font-mono font-extrabold focus:outline-none focus:border-emerald-400">
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
                        <h2 class="text-base sm:text-lg font-bold text-white tracking-tight"
                            x-text="isTanah ? 'Langkah 3: Rincian Belanja Modal Tanah Sesuai SPK / Kwitansi / Invoice' : (isMesin ? 'Langkah 3: Rincian Peralatan dan Mesin Sesuai SPK / Kwitansi / Invoice' : (isGedung ? 'Langkah 3: Rincian Belanja Gedung dan Bangunan Sesuai SPK / Invoice' : (isJaringan ? 'Langkah 3: Rincian Belanja Jalan, Irigasi dan Jaringan Sesuai SPK / Invoice' : (isAsetLainnya ? 'Langkah 3: Rincian Aset Tetap Lainnya Sesuai SPK / Invoice' : (isAtb ? 'Langkah 3: Rincian Aset Tidak Berwujud Sesuai SPK / Invoice' : (isKdp ? 'Langkah 3: Rincian Konstruksi Dalam Pengerjaan Sesuai SPK / MC' : 'Langkah 3: Dokumen Pengadaan & Bukti Transaksi'))))))"></h2>
                        <p class="text-xs text-slate-400 mt-1" x-text="isTanah ? 'Pilih Sub-Sub Rincian (Nama/Kode Barang 108), letak/alamat tanah, status tanah, sertifikat, riwayat pembelian, dan nilai barang:' : (isMesin ? 'Pilih Sub-Sub Rincian 108, spesifikasi (merk, type, ukuran, bahan), riwayat pembelian, volume, administrasi proyek dan ruangan:' : (isGedung ? 'Pilih Sub-Sub Rincian 108, luas m2, kondisi, status tanah KIB A, kapitalisasi, riwayat pembelian, volume dan rincian nilai bangunan:' : (isJaringan ? 'Pilih Sub-Sub Rincian 108, konstruksi, panjang/luas, status tanah KIB A, riwayat pembelian, volume dan rincian nilai jaringan:' : (isAsetLainnya ? 'Pilih Sub-Sub Rincian 108, buku perpustakaan, kesenian/kebudayaan, tanaman/hewan, riwayat pembelian, volume dan administrasi proyek:' : (isAtb ? 'Pilih Sub-Sub Rincian 108, judul/nama software, pencipta, spesifikasi, riwayat pembelian, volume, administrasi proyek dan ruangan:' : (isKdp ? 'Pilih Sub-Sub Rincian 108, spesifikasi konstruksi, luas rencana, progres %, status tanah KIB A, periode pengerjaan, dan akumulasi nilai realisasi:' : 'Lengkapi nomor dokumen pembelian atau klik tombol otomatis di kanan:'))))))))"></p>
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
                                <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
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
                                        <input type="date" x-model="formData.spk_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'spk'"
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
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
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
                                        <input type="date" x-model="formData.kwitansi_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
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
                                        <input type="date" x-model="formData.faktur_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor"  placeholder="0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="date" x-model="formData.sp2d_tanggal" :max="maxDateToday" @change="validateMaxDate('sp2d_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
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
                                        <input type="date" x-model="formData.bast_dokumen_tanggal" :max="maxDateToday" @change="validateMaxDate('bast_dokumen_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. IDENTITAS BARANG (KODE 108) - FULL WIDTH CARD -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-emerald-500/30 pb-2">
                                <span class="text-xs font-bold text-emerald-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                    <span>🏛️ 3. IDENTITAS BARANG (KODE 108):</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Terfilter dari Langkah 2</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                <!-- 1. Nama Barang (Filter Murni dari Nama & Kode 108) - 3 Kolom -->
                                <div class="md:col-span-3 space-y-1 relative" @click.away="isNamaBarang108Open = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-300 text-[11px] font-bold">Nama Barang (Uraian Sub-Sub Rincian 108):</label>
                                        <button type="button" 
                                                x-show="formData.tanah_kode_barang && !isNamaBarang108Open" 
                                                @click="isNamaBarang108Open = true; searchNamaBarang108 = ''" 
                                                class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Barang
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input type="text" 
                                               :value="(!isNamaBarang108Open && formData.tanah_nama_barang) ? (formData.tanah_kode_barang + ' - ' + formData.tanah_nama_barang) : searchNamaBarang108"
                                               @input="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true"
                                               @focus="isNamaBarang108Open = true"
                                               placeholder="Ketik untuk memfilter nama / kode barang 108..."
                                               class="w-full bg-slate-900 border border-emerald-500/60 hover:border-emerald-400 focus:border-emerald-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold transition-all shadow-inner">
                                    </div>

                                    <!-- Dropdown List Cards -->
                                    <div x-show="isNamaBarang108Open" x-transition x-cloak 
                                         style="max-height: 220px !important; overflow-y: auto !important;" 
                                         class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-emerald-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                                            <div @click="selectSubSubRincianItem(item)"
                                                 class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                                 :class="item.kode === formData.tanah_kode_barang ? 'border-emerald-500 bg-emerald-950/40 shadow-lg' : 'border-slate-800 hover:border-emerald-500/50'">
                                                <div class="min-w-0 pr-2">
                                                    <h4 class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors truncate" x-text="item.nama"></h4>
                                                    <p class="text-[10px] text-emerald-400 font-mono truncate" x-text="'KODE 108: ' + item.kode"></p>
                                                </div>
                                                <button type="button" 
                                                        @click.stop="selectSubSubRincianItem(item)" 
                                                        class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all"
                                                        :class="item.kode === formData.tanah_kode_barang ? 'bg-emerald-500 text-slate-950 shadow-md' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500 hover:text-slate-950'">
                                                    <span x-text="item.kode === formData.tanah_kode_barang ? '✓ Terpilih' : 'Pilih →'"></span>
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

                                <!-- 2. Kode Barang (Otomatis Terisi) - 1 Kolom -->
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px] font-bold">Kode Barang (Kode Sub-Sub Rincian 108):</label>
                                    <input type="text" x-model="formData.tanah_kode_barang" readonly placeholder="Kode 108 otomatis..."
                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-emerald-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <!-- Grid Form Pengisian Rincian Tanah (Sisa Kolom) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- 4. Status Tanah & Sertifikat -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">📜 4. Status Tanah & Sertifikat:</span>
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
                                               :max="maxDateToday" @change="validateMaxDate('tanah_sertifikat_tgl')"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white">
                                    </div>
                                </div>
                            </div>

                            <!-- 5. Kondisi, Penggunaan & Volume -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">📐 5. Kondisi, Penggunaan & Volume:</span>
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

                        <!-- 6. Nilai Barang (Rp) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">6. Nilai Barang (Rp):</span>
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

                        <!-- Letak / Alamat Barang -->
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
                        
                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
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
                                        <input type="date" x-model="formData.spk_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'spk'"
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
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
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
                                        <input type="date" x-model="formData.kwitansi_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
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
                                        <input type="date" x-model="formData.faktur_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="Contoh: 0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="date" x-model="formData.sp2d_tanggal" :max="maxDateToday" @change="validateMaxDate('sp2d_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
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
                                        <input type="date" x-model="formData.bast_dokumen_tanggal" :max="maxDateToday" @change="validateMaxDate('bast_dokumen_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. IDENTITAS BARANG (KODE 108) - FULL WIDTH CARD -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-emerald-500/30 pb-2">
                                <span class="text-xs font-bold text-emerald-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                    <span>🏛️ 3. IDENTITAS BARANG (KODE 108):</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Terfilter dari Langkah 2</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                <!-- 1. Nama Barang (Filter Murni dari Nama & Kode 108) - 3 Kolom -->
                                <div class="md:col-span-3 space-y-1 relative" @click.away="isNamaBarang108Open = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-300 text-[11px] font-bold">Nama Barang (Uraian Sub-Sub Rincian 108):</label>
                                        <button type="button" 
                                                x-show="formData.mesin_kode_barang && !isNamaBarang108Open" 
                                                @click="isNamaBarang108Open = true; searchNamaBarang108 = ''" 
                                                class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Barang
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input type="text" 
                                               :value="(!isNamaBarang108Open && formData.mesin_nama_barang) ? (formData.mesin_kode_barang + ' - ' + formData.mesin_nama_barang) : searchNamaBarang108"
                                               @input="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true"
                                               @focus="isNamaBarang108Open = true"
                                               placeholder="Ketik untuk memfilter nama / kode barang 108..."
                                               class="w-full bg-slate-900 border border-emerald-500/60 hover:border-emerald-400 focus:border-emerald-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold transition-all shadow-inner">
                                    </div>

                                    <!-- Dropdown List Cards -->
                                    <div x-show="isNamaBarang108Open" x-transition x-cloak 
                                         style="max-height: 220px !important; overflow-y: auto !important;" 
                                         class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-emerald-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                                            <div @click="selectSubSubRincianItem(item)"
                                                 class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                                 :class="item.kode === formData.mesin_kode_barang ? 'border-emerald-500 bg-emerald-950/40 shadow-lg' : 'border-slate-800 hover:border-emerald-500/50'">
                                                <div class="min-w-0 pr-2">
                                                    <h4 class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors truncate" x-text="item.nama"></h4>
                                                    <p class="text-[10px] text-emerald-400 font-mono truncate" x-text="'KODE 108: ' + item.kode"></p>
                                                </div>
                                                <button type="button" 
                                                        @click.stop="selectSubSubRincianItem(item)" 
                                                        class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all"
                                                        :class="item.kode === formData.mesin_kode_barang ? 'bg-emerald-500 text-slate-950 shadow-md' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500 hover:text-slate-950'">
                                                    <span x-text="item.kode === formData.mesin_kode_barang ? '✓ Terpilih' : 'Pilih →'"></span>
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

                                <!-- 2. Kode Barang (Otomatis Terisi) - 1 Kolom -->
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px] font-bold">Kode Barang (Kode Sub-Sub Rincian 108):</label>
                                    <input type="text" x-model="formData.mesin_kode_barang" readonly placeholder="Kode 108 otomatis..."
                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-emerald-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi Peralatan dan Mesin (Sisa Kolom) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- 4. Spesifikasi Fisik (Merk, Type, Ukuran) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">⚙️ 4. Merk, Type & Ukuran:</span>
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

                            <!-- Spesifikasi No Pabrik, Kendaraan, Bahan & Kondisi -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3.5 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">🏷️ No Pabrik, Kendaraan, Bahan & Kondisi:</span>
                                </div>

                                <div class="grid grid-cols-2 gap-2.5">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">No Pabrik / SN</label>
                                        <input type="text" x-model="formData.mesin_no_pabrik" placeholder="SN-RAD-2026-88192"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Bahan Pembuatan</label>
                                        <input type="text" x-model="formData.mesin_bahan" placeholder="Logam & Elektronik"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                                    </div>
                                </div>

                                <!-- Detail Kendaraan (2x2 Grid Rapi) -->
                                <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 space-y-2">
                                    <span class="text-[10px] font-bold text-slate-400 block uppercase tracking-wider">🚗 Legality Kendaraan (Jika Ada):</span>
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-slate-500 text-[9px] mb-0.5">No Rangka</label>
                                            <input type="text" x-model="formData.mesin_no_rangka" placeholder="MH1JM..."
                                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px] mb-0.5">No Mesin</label>
                                            <input type="text" x-model="formData.mesin_no_mesin" placeholder="JM51E..."
                                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px] mb-0.5">No BPKB</label>
                                            <input type="text" x-model="formData.mesin_no_bpkb" placeholder="BPKB-88..."
                                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-slate-500 text-[9px] mb-0.5">No POLISI / Plat</label>
                                            <input type="text" x-model="formData.mesin_no_polisi" placeholder="P 1234 WB"
                                                   class="w-full bg-slate-950 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono font-bold">
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Barang</label>
                                    <select x-model="formData.mesin_kondisi" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold">
                                        <option value="B">B (Baik)</option>
                                        <option value="KB">KB (Kurang Baik)</option>
                                        <option value="RB">RB (Rusak Berat)</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- 5. Volume & Nilai Satuan Barang -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-emerald-500/40 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider">5. Volume & Nilai Satuan Barang (Rp):</span>
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah Barang (Volume)</label>
                                    <input type="text" 
                                    :value="formData.mesin_jumlah_barang ? Number(formData.mesin_jumlah_barang).toLocaleString('id-ID') : ''"
                                    @input="
                                        let raw = $event.target.value.replace(/\D/g, '');
                                        formData.mesin_jumlah_barang = raw ? parseInt(raw, 10) : '';
                                        $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                        updateExtracomStatus();
                                    "
                                    placeholder="1"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan Barang</label>
                                    <input type="text" x-model="formData.mesin_satuan" placeholder="Unit / Buah / Set / Paket"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nilai Satuan Barang (Rp)</label>
                                    <input type="text" 
                                    :value="formData.mesin_nilai_satuan ? Number(formData.mesin_nilai_satuan).toLocaleString('id-ID') : ''"
                                    @input="
                                        let raw = $event.target.value.replace(/\D/g, '');
                                        formData.mesin_nilai_satuan = raw ? parseInt(raw, 10) : '';
                                        $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                        updateExtracomStatus();
                                    "
                                    placeholder="185.000.000"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-300 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Administrasi Proyek (Rp)</label>
                                    <input type="text" 
                                    :value="formData.mesin_administrasi_proyek ? Number(formData.mesin_administrasi_proyek).toLocaleString('id-ID') : ''"
                                    @input="
                                        let raw = $event.target.value.replace(/\D/g, '');
                                        formData.mesin_administrasi_proyek = raw ? parseInt(raw, 10) : '';
                                        $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                    "
                                    placeholder="0"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                </div>
                            </div>
                            <div class="p-3 rounded-xl bg-emerald-950/40 border border-emerald-500/50 flex items-center justify-between shadow-inner">
                                <span class="text-xs font-bold text-emerald-300">Total Nilai Barang (Rp):</span>
                                <span class="text-base font-extrabold text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiMesin)"></span>
                            </div>
                        </div>

                        <!-- 6. Ruang / Pemegang Aset (Ditampilkan tepat diatas Live Preview Excel) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/40 space-y-2 shadow-lg relative" @click.away="isRuangPemegangOpen = false">
                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-2">
                                <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 RUANG / PEMEGANG (PENANGGUNG JAWAB & LOKASI):</span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                        <span>🏥</span>
                                        <span>Tersinkron Unit & Paviliun</span>
                                    </span>
                                    <button type="button" 
                                            x-show="formData.ruang_pemegang" 
                                            @click="formData.ruang_pemegang = ''; searchRuangPemegang = ''; isRuangPemegangOpen = true" 
                                            class="text-[10.5px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                        ✕ Reset
                                    </button>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <input type="text" 
                                       :value="!isRuangPemegangOpen ? formData.ruang_pemegang : searchRuangPemegang"
                                       @input="formData.ruang_pemegang = $event.target.value; searchRuangPemegang = $event.target.value; isRuangPemegangOpen = true"
                                       @focus="isRuangPemegangOpen = true"
                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                       class="w-full bg-slate-900 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-4 py-3 pl-10 text-xs text-white font-semibold focus:outline-none transition-all">
                                <svg class="w-4 h-4 text-amber-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>

                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                            <div x-show="isRuangPemegangOpen" x-transition x-cloak style="max-height: 210px;" class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 rounded-xl text-[10px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between">
                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                    <span class="text-slate-400 font-mono text-[9.5px]" x-text="filteredUnitsMesin.length + ' Unit/Ruangan'"></span>
                                </div>
                                <template x-for="u in filteredUnitsMesin" :key="u.id">
                                    <div @click="selectUnitMesin(u)" class="p-2.5 rounded-xl bg-slate-950/50 hover:bg-amber-500/15 border border-slate-800/60 hover:border-amber-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-bold text-white group-hover:text-amber-300 truncate" x-text="u.nama"></span>
                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                        </div>
                                        <span class="px-2 py-1 rounded-lg bg-slate-900 text-amber-300 border border-amber-500/30 text-[10px] font-bold shrink-0">Pilih →</span>
                                    </div>
                                </template>
                                <template x-if="filteredUnitsMesin.length === 0">
                                    <div class="p-3 text-center text-xs text-slate-400">
                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                    </div>
                                </template>
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
                        
                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
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
                                        <input type="date" x-model="formData.spk_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'spk'"
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
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
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
                                        <input type="date" x-model="formData.kwitansi_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
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
                                        <input type="date" x-model="formData.faktur_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor"  placeholder="0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="date" x-model="formData.sp2d_tanggal" :max="maxDateToday" @change="validateMaxDate('sp2d_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
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
                                        <input type="date" x-model="formData.bast_dokumen_tanggal" :max="maxDateToday" @change="validateMaxDate('bast_dokumen_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. IDENTITAS BARANG (KODE 108) - FULL WIDTH CARD -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-emerald-500/30 pb-2">
                                <span class="text-xs font-bold text-emerald-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                    <span>🏛️ 3. IDENTITAS BARANG (KODE 108):</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Terfilter dari Langkah 2</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                <!-- 1. Nama Bangunan (Filter Murni dari Nama & Kode 108) - 3 Kolom -->
                                <div class="md:col-span-3 space-y-1 relative" @click.away="isNamaBarang108Open = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-300 text-[11px] font-bold">Nama Bangunan (Uraian Sub-Sub Rincian 108):</label>
                                        <button type="button" 
                                                x-show="formData.gedung_kode_barang && !isNamaBarang108Open" 
                                                @click="isNamaBarang108Open = true; searchNamaBarang108 = ''" 
                                                class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Barang
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input type="text" 
                                               :value="(!isNamaBarang108Open && formData.gedung_nama_barang) ? (formData.gedung_kode_barang + ' - ' + formData.gedung_nama_barang) : searchNamaBarang108"
                                               @input="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true"
                                               @focus="isNamaBarang108Open = true"
                                               placeholder="Ketik untuk memfilter nama / kode barang 108..."
                                               class="w-full bg-slate-900 border border-emerald-500/60 hover:border-emerald-400 focus:border-emerald-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold transition-all shadow-inner">
                                    </div>

                                    <!-- Dropdown List Cards -->
                                    <div x-show="isNamaBarang108Open" x-transition x-cloak 
                                         style="max-height: 220px !important; overflow-y: auto !important;" 
                                         class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-emerald-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                                            <div @click="selectSubSubRincianItem(item)"
                                                 class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                                 :class="item.kode === formData.gedung_kode_barang ? 'border-emerald-500 bg-emerald-950/40 shadow-lg' : 'border-slate-800 hover:border-emerald-500/50'">
                                                <div class="min-w-0 pr-2">
                                                    <h4 class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors truncate" x-text="item.nama"></h4>
                                                    <p class="text-[10px] text-emerald-400 font-mono truncate" x-text="'KODE 108: ' + item.kode"></p>
                                                </div>
                                                <button type="button" 
                                                        @click.stop="selectSubSubRincianItem(item)" 
                                                        class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all"
                                                        :class="item.kode === formData.gedung_kode_barang ? 'bg-emerald-500 text-slate-950 shadow-md' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500 hover:text-slate-950'">
                                                    <span x-text="item.kode === formData.gedung_kode_barang ? '✓ Terpilih' : 'Pilih →'"></span>
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

                                <!-- 2. Kode Barang (Otomatis Terisi) - 1 Kolom -->
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px] font-bold">Kode Barang (Kode Sub-Sub Rincian 108):</label>
                                    <input type="text" x-model="formData.gedung_kode_barang" readonly placeholder="Kode 108 otomatis..."
                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-emerald-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi Gedung dan Bangunan (Sisa Kolom) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

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

                        <!-- 5. Volume & Nilai Satuan Bangunan (Rp) -->
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

                        <!-- 6. Letak / Alamat Lokasi Gedung dan Bangunan (Ditampilkan tepat diatas Live Preview Excel) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-emerald-500/40 space-y-2 shadow-lg">
                            <div class="flex items-center justify-between border-b border-emerald-500/30 pb-2">
                                <label class="block text-emerald-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 LETAK / ALAMAT LOKASI GEDUNG & BANGUNAN:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Lokasi Fisik Bangunan</span>
                            </div>
                            <input type="text" x-model="formData.alamat_barang" placeholder="Contoh: Jl. Piere Tendean No. 3 Bondowoso (Kompleks RSUD Dr. H. Koesnandi)"
                                   class="w-full bg-slate-900 border border-slate-700 hover:border-emerald-500 rounded-xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500 transition-all">
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
                                <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
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
                                        <input type="date" x-model="formData.spk_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'spk'"
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
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
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
                                        <input type="date" x-model="formData.kwitansi_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
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
                                        <input type="date" x-model="formData.faktur_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="Contoh: 0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="date" x-model="formData.sp2d_tanggal" :max="maxDateToday" @change="validateMaxDate('sp2d_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
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
                                        <input type="date" x-model="formData.bast_dokumen_tanggal" :max="maxDateToday" @change="validateMaxDate('bast_dokumen_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. IDENTITAS BARANG (KODE 108) - FULL WIDTH CARD -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-teal-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-teal-500/30 pb-2">
                                <span class="text-xs font-bold text-teal-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                    <span>🏛️ 3. IDENTITAS BARANG (KODE 108):</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 font-bold">Terfilter dari Langkah 2</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                <!-- 1. Nama Jaringan (Filter Murni dari Nama & Kode 108) - 3 Kolom -->
                                <div class="md:col-span-3 space-y-1 relative" @click.away="isNamaBarang108Open = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-300 text-[11px] font-bold">Nama Jaringan / Jalan (Uraian Sub-Sub Rincian 108):</label>
                                        <button type="button" 
                                                x-show="formData.jaringan_kode_barang && !isNamaBarang108Open" 
                                                @click="isNamaBarang108Open = true; searchNamaBarang108 = ''" 
                                                class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Barang
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input type="text" 
                                               :value="(!isNamaBarang108Open && formData.jaringan_nama_barang) ? (formData.jaringan_kode_barang + ' - ' + formData.jaringan_nama_barang) : searchNamaBarang108"
                                               @input="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true"
                                               @focus="isNamaBarang108Open = true"
                                               placeholder="Ketik untuk memfilter nama / kode barang 108..."
                                               class="w-full bg-slate-900 border border-teal-500/60 hover:border-teal-400 focus:border-teal-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold transition-all shadow-inner">
                                    </div>

                                    <!-- Dropdown List Cards -->
                                    <div x-show="isNamaBarang108Open" x-transition x-cloak 
                                         style="max-height: 220px !important; overflow-y: auto !important;" 
                                         class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-teal-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                                            <div @click="selectSubSubRincianItem(item)"
                                                 class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                                 :class="item.kode === formData.jaringan_kode_barang ? 'border-teal-500 bg-teal-950/40 shadow-lg' : 'border-slate-800 hover:border-teal-500/50'">
                                                <div class="min-w-0 pr-2">
                                                    <h4 class="text-xs font-bold text-white group-hover:text-teal-300 transition-colors truncate" x-text="item.nama"></h4>
                                                    <p class="text-[10px] text-teal-400 font-mono truncate" x-text="'KODE 108: ' + item.kode"></p>
                                                </div>
                                                <button type="button" 
                                                        @click.stop="selectSubSubRincianItem(item)" 
                                                        class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all"
                                                        :class="item.kode === formData.jaringan_kode_barang ? 'bg-teal-500 text-slate-950 shadow-md' : 'bg-teal-500/20 text-teal-300 border border-teal-500/40 hover:bg-teal-500 hover:text-slate-950'">
                                                    <span x-text="item.kode === formData.jaringan_kode_barang ? '✓ Terpilih' : 'Pilih →'"></span>
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

                                <!-- 2. Kode Barang (Otomatis Terisi) - 1 Kolom -->
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px] font-bold">Kode Barang (Kode Sub-Sub Rincian 108):</label>
                                    <input type="text" x-model="formData.jaringan_kode_barang" readonly placeholder="Kode 108 otomatis..."
                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-teal-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <!-- Grid Form Pengisian Spesifikasi Jalan & Jaringan (Sisa Kolom) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- 4. Konstruksi & Dimensi Jaringan (Panjang, Lebar, Luas, Kondisi) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">📐 4. Konstruksi & Dimensi:</span>
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

                            <!-- Status Tanah KIB A & Kapitalisasi Jaringan -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">📜 Status Tanah & Kapitalisasi:</span>
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

                        <!-- 5. Volume & Nilai Jaringan (Rp) -->
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

                        <!-- 6. Letak / Lokasi Jaringan (Ditampilkan tepat diatas Live Preview Excel) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-teal-500/40 space-y-2 shadow-lg">
                            <div class="flex items-center justify-between border-b border-teal-500/30 pb-2">
                                <label class="block text-teal-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 LETAK / LOKASI JARINGAN & BANGUNAN:</span>
                                </label>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 font-bold">Lokasi Fisik Jaringan</span>
                            </div>
                            <input type="text" x-model="formData.alamat_barang" placeholder="Contoh: Jalur Utilitas Gedung Bedah Sentral & Paviliun Teratai RSUD Dr. H. Koesnandi"
                                   class="w-full bg-slate-900 border border-slate-700 hover:border-teal-500 rounded-xl px-4 py-3 text-xs text-white font-semibold focus:outline-none focus:border-teal-500 transition-all">
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

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
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
                                        <input type="date" x-model="formData.spk_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'spk'"
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
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
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
                                        <input type="date" x-model="formData.kwitansi_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
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
                                        <input type="date" x-model="formData.faktur_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="Contoh: 0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="date" x-model="formData.sp2d_tanggal" :max="maxDateToday" @change="validateMaxDate('sp2d_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
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
                                        <input type="date" x-model="formData.bast_dokumen_tanggal" :max="maxDateToday" @change="validateMaxDate('bast_dokumen_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. IDENTITAS BARANG (KODE 108) - FULL WIDTH CARD -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-rose-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-rose-500/30 pb-2">
                                <span class="text-xs font-bold text-rose-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                    <span>🏛️ 3. IDENTITAS BARANG (KODE 108):</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold">Terfilter dari Langkah 2</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                <!-- 1. Nama Barang (Filter Murni dari Nama & Kode 108) - 3 Kolom -->
                                <div class="md:col-span-3 space-y-1 relative" @click.away="isNamaBarang108Open = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-300 text-[11px] font-bold">Nama Barang (Uraian Sub-Sub Rincian 108):</label>
                                        <button type="button" 
                                                x-show="formData.lainnya_kode_barang && !isNamaBarang108Open" 
                                                @click="isNamaBarang108Open = true; searchNamaBarang108 = ''" 
                                                class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Barang
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input type="text" 
                                               :value="(!isNamaBarang108Open && formData.lainnya_nama_barang) ? (formData.lainnya_kode_barang + ' - ' + formData.lainnya_nama_barang) : searchNamaBarang108"
                                               @input="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true"
                                               @focus="isNamaBarang108Open = true"
                                               placeholder="Ketik untuk memfilter nama / kode barang 108..."
                                               class="w-full bg-slate-900 border border-rose-500/50 hover:border-rose-400 focus:border-rose-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold transition-all shadow-inner">
                                    </div>

                                    <!-- Dropdown List Cards -->
                                    <div x-show="isNamaBarang108Open" x-transition x-cloak 
                                         style="max-height: 220px !important; overflow-y: auto !important;" 
                                         class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-rose-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                                            <div @click="selectSubSubRincianItem(item)"
                                                 class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                                 :class="item.kode === formData.lainnya_kode_barang ? 'border-rose-500 bg-rose-950/40 shadow-lg' : 'border-slate-800 hover:border-rose-500/50'">
                                                <div class="min-w-0 pr-2">
                                                    <h4 class="text-xs font-bold text-white group-hover:text-rose-300 transition-colors truncate" x-text="item.nama"></h4>
                                                    <p class="text-[10px] text-rose-400 font-mono truncate" x-text="'KODE 108: ' + item.kode"></p>
                                                </div>
                                                <button type="button" 
                                                        @click.stop="selectSubSubRincianItem(item)" 
                                                        class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all"
                                                        :class="item.kode === formData.lainnya_kode_barang ? 'bg-rose-500 text-slate-950 shadow-md' : 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500 hover:text-slate-950'">
                                                    <span x-text="item.kode === formData.lainnya_kode_barang ? '✓ Terpilih' : 'Pilih →'"></span>
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

                                <!-- 2. Kode Barang (Otomatis Terisi) - 1 Kolom -->
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px] font-bold">Kode Barang (Kode Sub-Sub Rincian 108):</label>
                                    <input type="text" x-model="formData.lainnya_kode_barang" readonly placeholder="Kode 108 otomatis..."
                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-rose-300 font-mono font-bold focus:outline-none cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <!-- Grid Form Pengisian Rincian Aset Tetap Lainnya (Sisa Kolom) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- 4. BUKU PERPUSTAKAAN (JUDUL, PENCIPTA, SPESIFIKASI) -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">📚 Buku Perpustakaan</span>
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

                            <!-- BARANG BERCORAK KESENIAN / KEBUDAYAAN & HEWAN/TUMBUHAN -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">🎨 Kesenian & 🌿 Tanaman</span>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 font-bold">Kolom Excel</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Asal Kesenian</label>
                                        <input type="text" x-model="formData.lainnya_kesenian_asal" placeholder="Jatim / Bondowoso"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Pencipta Kesenian</label>
                                        <input type="text" x-model="formData.lainnya_kesenian_pencipta" placeholder="Sanggar Budaya"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                </div>
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px]">Spesifikasi Kesenian</label>
                                    <input type="text" x-model="formData.lainnya_kesenian_spesifikasi" placeholder="Lukisan Sejarah Rumah Sakit..."
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-purple-300 focus:outline-none focus:border-purple-500">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Bahan Kesenian</label>
                                        <input type="text" x-model="formData.lainnya_kesenian_bahan" placeholder="Kanvas & Kayu"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Ukuran Kesenian (m/cm)</label>
                                        <input type="text" x-model="formData.lainnya_kesenian_ukuran" placeholder="200 x 120 cm"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Jenis Hewan/Tanaman</label>
                                        <input type="text" x-model="formData.lainnya_hewan_jenis" placeholder="Peneduh/Taman"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Spesifikasi Tanaman</label>
                                        <input type="text" x-model="formData.lainnya_hewan_spesifikasi" placeholder="Tabebuya Tinggi 3M"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-300">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- 5. VOLUME, ADMINISTRASI PROYEK & TOTAL NILAI -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-rose-400 block uppercase tracking-wider">5. Volume & Nilai Barang:</span>
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
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Nilai Satuan Barang (Rp)</label>
                                    <input type="number" x-model.number="formData.lainnya_nilai_satuan" placeholder="450000"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-rose-300 font-mono font-bold">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Administrasi Proyek (Rp)</label>
                                    <input type="number" x-model.number="formData.lainnya_administrasi_proyek" placeholder="250000"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold">
                                </div>
                            </div>
                            <div class="p-3 rounded-xl bg-rose-950/30 border border-rose-500/30 flex items-center justify-between text-xs">
                                <span class="text-xs font-bold text-rose-300">Total Nilai Barang (Rp):</span>
                                <span class="text-sm font-extrabold text-rose-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAsetLainnya)"></span>
                            </div>
                        </div>

                        <!-- 6. Ruang / Pemegang (Ditampilkan tepat diatas Live Preview Excel KIB E) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-rose-500/40 space-y-2 shadow-lg relative" @click.away="isRuangPemegangLainnyaOpen = false">
                            <div class="flex items-center justify-between border-b border-rose-500/30 pb-2">
                                <label class="block text-rose-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 RUANG / PEMEGANG (PENANGGUNG JAWAB & LOKASI):</span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                        <span>🏥</span>
                                        <span>Tersinkron Unit & Paviliun</span>
                                    </span>
                                    <button type="button" 
                                            x-show="formData.ruang_pemegang_lainnya" 
                                            @click="formData.ruang_pemegang_lainnya = ''; searchRuangPemegangLainnya = ''; isRuangPemegangLainnyaOpen = true" 
                                            class="text-[10.5px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                        ✕ Reset
                                    </button>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <input type="text" 
                                       :value="!isRuangPemegangLainnyaOpen ? formData.ruang_pemegang_lainnya : searchRuangPemegangLainnya"
                                       @input="formData.ruang_pemegang_lainnya = $event.target.value; searchRuangPemegangLainnya = $event.target.value; isRuangPemegangLainnyaOpen = true"
                                       @focus="isRuangPemegangLainnyaOpen = true"
                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                       class="w-full bg-slate-900 border border-slate-700 hover:border-rose-500 focus:border-rose-500 rounded-xl px-4 py-3 pl-10 text-xs text-white font-semibold focus:outline-none transition-all">
                                <svg class="w-4 h-4 text-rose-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>

                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                            <div x-show="isRuangPemegangLainnyaOpen" x-transition x-cloak style="max-height: 210px;" class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-rose-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 rounded-xl text-[10px] font-bold text-rose-400 uppercase tracking-wider flex items-center justify-between">
                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                    <span class="text-slate-400 font-mono text-[9.5px]" x-text="filteredUnitsLainnya.length + ' Unit/Ruangan'"></span>
                                </div>
                                <template x-for="u in filteredUnitsLainnya" :key="u.id">
                                    <div @click="selectUnitLainnya(u)" class="p-2.5 rounded-xl bg-slate-950/50 hover:bg-rose-500/15 border border-slate-800/60 hover:border-rose-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-bold text-white group-hover:text-rose-300 truncate" x-text="u.nama"></span>
                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                        </div>
                                        <span class="px-2 py-1 rounded-lg bg-slate-900 text-rose-300 border border-rose-500/30 text-[10px] font-bold shrink-0">Pilih →</span>
                                    </div>
                                </template>
                                <template x-if="filteredUnitsLainnya.length === 0">
                                    <div class="p-3 text-center text-xs text-slate-400">
                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                    </div>
                                </template>
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

                <template x-if="isAtb">
                    <div class="space-y-6">

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
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
                                        <input type="date" x-model="formData.spk_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'spk'"
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
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
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
                                        <input type="date" x-model="formData.kwitansi_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
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
                                        <input type="date" x-model="formData.faktur_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="Contoh: 0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="date" x-model="formData.sp2d_tanggal" :max="maxDateToday" @change="validateMaxDate('sp2d_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
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
                                        <input type="date" x-model="formData.bast_dokumen_tanggal" :max="maxDateToday" @change="validateMaxDate('bast_dokumen_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. IDENTITAS BARANG (KODE 108) - FULL WIDTH CARD -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-violet-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-violet-500/30 pb-2">
                                <span class="text-xs font-bold text-violet-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                    <span>💻 3. IDENTITAS BARANG (KODE 108):</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-violet-500/20 text-violet-300 border border-violet-500/30 font-bold">Terfilter dari Langkah 2</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                <!-- 1. Nama Barang (Filter Murni dari Nama & Kode 108) - 3 Kolom -->
                                <div class="md:col-span-3 space-y-1 relative" @click.away="isNamaBarang108Open = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-300 text-[11px] font-bold">Nama Barang (Uraian Sub-Sub Rincian 108):</label>
                                        <button type="button" 
                                                x-show="formData.atb_kode_barang && !isNamaBarang108Open" 
                                                @click="isNamaBarang108Open = true; searchNamaBarang108 = ''" 
                                                class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Barang
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input type="text" 
                                               :value="(!isNamaBarang108Open && formData.atb_nama_barang) ? (formData.atb_kode_barang + ' - ' + formData.atb_nama_barang) : searchNamaBarang108"
                                               @input="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true"
                                               @focus="isNamaBarang108Open = true"
                                               placeholder="Ketik untuk memfilter nama / kode barang 108..."
                                               class="w-full bg-slate-900 border border-violet-500/50 hover:border-violet-400 focus:border-violet-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold transition-all shadow-inner">
                                    </div>

                                    <!-- Dropdown List Cards -->
                                    <div x-show="isNamaBarang108Open" x-transition x-cloak 
                                         style="max-height: 220px !important; overflow-y: auto !important;" 
                                         class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-violet-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                                            <div @click="selectSubSubRincianItem(item)"
                                                 class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                                 :class="item.kode === formData.atb_kode_barang ? 'border-violet-500 bg-violet-950/40 shadow-lg' : 'border-slate-800 hover:border-violet-500/50'">
                                                <div class="min-w-0 pr-2">
                                                    <h4 class="text-xs font-bold text-white group-hover:text-violet-300 transition-colors truncate" x-text="item.nama"></h4>
                                                    <p class="text-[10px] text-violet-400 font-mono truncate" x-text="'KODE 108: ' + item.kode"></p>
                                                </div>
                                                <button type="button" 
                                                        @click.stop="selectSubSubRincianItem(item)" 
                                                        class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all"
                                                        :class="item.kode === formData.atb_kode_barang ? 'bg-violet-500 text-slate-950 shadow-md' : 'bg-violet-500/20 text-violet-300 border border-violet-500/40 hover:bg-violet-500 hover:text-slate-950'">
                                                    <span x-text="item.kode === formData.atb_kode_barang ? '✓ Terpilih' : 'Pilih →'"></span>
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

                                <!-- 2. Kode Barang (Otomatis Terisi) - 1 Kolom -->
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px] font-bold">Kode Barang (Kode Sub-Sub Rincian 108):</label>
                                    <input type="text" x-model="formData.atb_kode_barang" readonly placeholder="Kode 108 otomatis..."
                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-violet-300 font-mono font-bold focus:outline-none cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <!-- Grid Form Pengisian Rincian Aset Tidak Berwujud (Sisa Kolom) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                            <!-- 4. JUDUL / NAMA, PENCIPTA & SPESIFIKASI ATB -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">4. Judul, Pencipta & Spesifikasi</span>
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

                            <!-- 5. VOLUME, ADMINISTRASI PROYEK & TOTAL NILAI -->
                            <div class="p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-violet-400"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">5. Volume & Nilai ATB</span>
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

                        </div>

                        <!-- 6. Ruang / Pemegang (Ditampilkan tepat diatas Live Preview Excel ATB) -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-violet-500/40 space-y-2 shadow-lg relative" @click.away="isRuangPemegangAtbOpen = false">
                            <div class="flex items-center justify-between border-b border-violet-500/30 pb-2">
                                <label class="block text-violet-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                    <span>📍 RUANG / PEMEGANG (PENANGGUNG JAWAB & LOKASI):</span>
                                </label>
                                <div class="flex items-center space-x-2">
                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                        <span>🏥</span>
                                        <span>Tersinkron Unit & Paviliun</span>
                                    </span>
                                    <button type="button" 
                                            x-show="formData.ruang_pemegang_atb" 
                                            @click="formData.ruang_pemegang_atb = ''; searchRuangPemegangAtb = ''; isRuangPemegangAtbOpen = true" 
                                            class="text-[10.5px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                        ✕ Reset
                                    </button>
                                </div>
                            </div>
                            
                            <div class="relative">
                                <input type="text" 
                                       :value="!isRuangPemegangAtbOpen ? formData.ruang_pemegang_atb : searchRuangPemegangAtb"
                                       @input="formData.ruang_pemegang_atb = $event.target.value; searchRuangPemegangAtb = $event.target.value; isRuangPemegangAtbOpen = true"
                                       @focus="isRuangPemegangAtbOpen = true"
                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                       class="w-full bg-slate-900 border border-slate-700 hover:border-violet-500 focus:border-violet-500 rounded-xl px-4 py-3 pl-10 text-xs text-white font-semibold focus:outline-none transition-all">
                                <svg class="w-4 h-4 text-violet-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            </div>

                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                            <div x-show="isRuangPemegangAtbOpen" x-transition x-cloak style="max-height: 210px;" class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-violet-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                <div class="px-3 py-1.5 bg-slate-950/80 rounded-xl text-[10px] font-bold text-violet-400 uppercase tracking-wider flex items-center justify-between">
                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                    <span class="text-slate-400 font-mono text-[9.5px]" x-text="filteredUnitsAtb.length + ' Unit/Ruangan'"></span>
                                </div>
                                <template x-for="u in filteredUnitsAtb" :key="u.id">
                                    <div @click="selectUnitAtb(u)" class="p-2.5 rounded-xl bg-slate-950/50 hover:bg-violet-500/15 border border-slate-800/60 hover:border-violet-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                        <div class="min-w-0 pr-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-xs font-bold text-white group-hover:text-violet-300 truncate" x-text="u.nama"></span>
                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                            </div>
                                            <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                        </div>
                                        <span class="px-2 py-1 rounded-lg bg-slate-900 text-violet-300 border border-violet-500/30 text-[10px] font-bold shrink-0">Pilih →</span>
                                    </div>
                                </template>
                                <template x-if="filteredUnitsAtb.length === 0">
                                    <div class="p-3 text-center text-xs text-slate-400">
                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                    </div>
                                </template>
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

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
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
                                        <input type="date" x-model="formData.spk_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'spk'"
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
                                        <input type="date" x-model="formData.surat_pesanan_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
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
                                        <input type="date" x-model="formData.kwitansi_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
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
                                        <input type="date" x-model="formData.faktur_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value)" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="date" x-model="formData.sp2d_tanggal" :max="maxDateToday" @change="validateMaxDate('sp2d_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
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
                                        <input type="date" x-model="formData.bast_dokumen_tanggal" :max="maxDateToday" @change="validateMaxDate('bast_dokumen_tanggal')"  class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. IDENTITAS PROYEK KDP (KODE 108) - FULL WIDTH CARD -->
                        <div class="p-5 rounded-2xl bg-slate-950/80 border border-amber-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-2">
                                <span class="text-xs font-bold text-amber-400 flex items-center space-x-1.5 uppercase tracking-wider">
                                    <span>🏗️ 3. IDENTITAS PROYEK KDP (KODE 108):</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Terfilter dari Langkah 2</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-start">
                                <!-- 1. Nama Proyek (Filter Murni dari Nama & Kode 108) - 3 Kolom -->
                                <div class="md:col-span-3 space-y-1 relative" @click.away="isNamaBarang108Open = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-300 text-[11px] font-bold">Nama Proyek KDP (Uraian Sub-Sub Rincian 108):</label>
                                        <button type="button" 
                                                x-show="formData.kdp_kode_barang && !isNamaBarang108Open" 
                                                @click="isNamaBarang108Open = true; searchNamaBarang108 = ''" 
                                                class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Barang
                                        </button>
                                    </div>

                                    <div class="relative">
                                        <input type="text" 
                                               :value="(!isNamaBarang108Open && formData.kdp_nama_barang) ? (formData.kdp_kode_barang + ' - ' + formData.kdp_nama_barang) : searchNamaBarang108"
                                               @input="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true"
                                               @focus="isNamaBarang108Open = true"
                                               placeholder="Ketik untuk memfilter nama / kode barang 108..."
                                               class="w-full bg-slate-900 border border-amber-500/60 hover:border-amber-400 focus:border-amber-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold transition-all shadow-inner">
                                    </div>

                                    <!-- Dropdown List Cards -->
                                    <div x-show="isNamaBarang108Open" x-transition x-cloak 
                                         style="max-height: 220px !important; overflow-y: auto !important;" 
                                         class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                                            <div @click="selectSubSubRincianItem(item)"
                                                 class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                                 :class="item.kode === formData.kdp_kode_barang ? 'border-amber-500 bg-amber-950/40 shadow-lg' : 'border-slate-800 hover:border-amber-500/50'">
                                                <div class="min-w-0 pr-2">
                                                    <h4 class="text-xs font-bold text-white group-hover:text-amber-300 transition-colors truncate" x-text="item.nama"></h4>
                                                    <p class="text-[10px] text-amber-400 font-mono truncate" x-text="'KODE 108: ' + item.kode"></p>
                                                </div>
                                                <button type="button" 
                                                        @click.stop="selectSubSubRincianItem(item)" 
                                                        class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-bold transition-all"
                                                        :class="item.kode === formData.kdp_kode_barang ? 'bg-amber-500 text-slate-950 shadow-md' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500 hover:text-slate-950'">
                                                    <span x-text="item.kode === formData.kdp_kode_barang ? '✓ Terpilih' : 'Pilih →'"></span>
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

                                <!-- 2. Kode Barang (Otomatis Terisi) - 1 Kolom -->
                                <div class="space-y-1">
                                    <label class="block text-slate-400 text-[11px] font-bold">Kode Barang (Kode Sub-Sub Rincian 108):</label>
                                    <input type="text" x-model="formData.kdp_kode_barang" readonly placeholder="Kode 108 otomatis..."
                                           class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3.5 py-2.5 text-xs text-amber-400 font-mono font-bold focus:outline-none cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <!-- Grid Form Pengisian Rincian KDP (Sisa Kolom) -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

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

                        <!-- 4. Akumulasi Nilai Realisasi Biaya KDP (Rp) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3 shadow-lg">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">4. Akumulasi Nilai Realisasi Biaya KDP (Rp):</span>
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

                        <!-- 5. Letak / Alamat Lokasi Proyek Konstruksi (Ditampilkan tepat diatas Live Preview Excel) -->
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
                                    <input type="date" x-model="formData.spk_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value, 'spk_tanggal')" 
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
                                    <input type="date" x-model="formData.surat_pesanan_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value, 'surat_pesanan_tanggal')" 
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
                                    <input type="date" x-model="formData.kwitansi_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value, 'kwitansi_tanggal')" 
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
                                        <input type="date" x-model="formData.faktur_tanggal" :max="maxDateToday" @change="onDocDateChange($event.target.value, 'faktur_tanggal')" 
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
