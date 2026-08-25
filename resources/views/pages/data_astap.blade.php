<x-layout title="Data ASTAP - SIMAT-RK">
    @section('page-title', 'Data ASTAP')
    @section('breadcrumb', 'Master Utama / Data ASTAP')

    <!-- Library SheetJS untuk Multi-Sheet Excel Export -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

    <!-- Script Global Export Multi-Sheet Excel (Rekapitulasi, KIB A-F, ATB, Exstracom) -->
    <script>
    function exportAstapToExcel() {
        if (typeof XLSX === 'undefined') {
            alert('⚠️ Pustaka Excel sedang dimuat, silakan coba 1 detik lagi...');
            return;
        }

        const wb = XLSX.utils.book_new();

        // 1. REKAPITULASI
        const rekapData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            ["RUMAH SAKIT UMUM DAERAH DR. H. KOESNANDI BONDOWOSO"],
            ["REKAPITULASI REALISASI BELANJA MODAL ASET TETAP (BAST TRIWULAN) TAHUN ANGGARAN 2026"],
            [""],
            ["NO", "KELOMPOK ASET (KIB / ATB / EXTRACOM)", "KODE REKENING BELANJA", "JUMLAH ITEM", "TOTAL REALISASI (RP)", "KETERANGAN"],
            ["1", "2. A - TANAH (KIB A)", "5.2.02.01.01.0001", "1 Bidang", 8500000000, "Lahan RSUD Hak Pakai BPN"],
            ["2", "3. B - PERALATAN DAN MESIN (>= RP 300.000)", "5.2.02.02.01.0005", "3 Unit", 1494351900, "Alat Kesehatan & Mesin Pompa"],
            ["3", "4. C - GEDUNG DAN BANGUNAN (KIB C)", "5.2.02.03.01.0008", "2 Gedung", 11000000000, "Paviliun Amukti & IBS 2 Lantai"],
            ["4", "5. D - JALAN, IRIGASI DAN JARINGAN (KIB D)", "5.2.02.04.01.0004", "2 Jaringan", 1500000000, "Pipa Gas Medis & IPAL Sentral"],
            ["5", "6. E - ASET TETAP LAINNYA (KIB E)", "5.2.02.05.01.0002", "2 Paket", 120000000, "Buku Medis & Seni Budaya"],
            ["6", "7. F - KONSTRUKSI DALAM PENGERJAAN (KIB F)", "5.2.02.06.01.0001", "2 Proyek", 8700000000, "Gedung Rawat Inap & Diagnostik KDP"],
            ["7", "8. ATB - ASET TIDAK BERWUJUD (1.5.3)", "5.2.02.08.01.0005", "2 Lisensi", 725000000, "Software SIMRS & PACS Cloud"],
            ["8", "9. EXTRACOM - EKSTRAKOMTABEL (< RP 300.000)", "5.2.02.02.01.0099", "23 Unit", 4315000, "Alat Medis Kecil < Rp 300rb"],
            ["", "TOTAL REKAPITULASI REALISASI BELANJA ASET", "", "37 Item", 32043666900, "Lengkap 8 Kelompok"],
            [""],
            [""],
            ["", "", "", "Bondowoso, " + new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})],
            ["", "Mengetahui,", "", "Pengurus Barang Pengelola,"],
            ["", "Pejabat Pembuat Komitmen (PPK)", "", "RSUD Dr. H. Koesnandi"],
            [""],
            [""],
            ["", "( ................................................ )", "", "( ................................................ )"],
            ["", "NIP. 19780101 200501 1 008", "", "NIP. 19850615 201001 2 015"]
        ];
        const wsRekap = XLSX.utils.aoa_to_sheet(rekapData);
        wsRekap['!cols'] = [{wch: 6}, {wch: 45}, {wch: 25}, {wch: 15}, {wch: 25}, {wch: 40}];
        XLSX.utils.book_append_sheet(wb, wsRekap, "1. Rekapitulasi");

        // 2. KIB A
        const kibAData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["RINCIAN BELANJA MODAL TANAH (KIB A) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "NAMA BARANG", "KODE BARANG (108)", "LUAS (M2)", "TAHUN PENGADAAN", "LETAK / ALAMAT", 
                "HAK TANAH", "TGL SERTIFIKAT", "NO SERTIFIKAT", "PENGGUNAAN", "ASAL USUL",
                "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO FAKTUR/INVOICE", "TGL FAKTUR/INVOICE",
                "NILAI PERENCANAAN (RP)", "NILAI PENGADAAN/FISIK (RP)", "NILAI PENGAWASAN (RP)", "TOTAL NILAI TANAH (RP)",
                "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "KETERANGAN"
            ],
            [
                1, "Lahan Bangunan RSUD Dr. H. Koesnandi", "1.3.1.01.01.02.013", 35400, "1984", "Jl. Piere Tendean No. 1 Bondowoso",
                "Hak Pakai", "1984-03-15", "HP-108/1984", "Bangunan Rumah Sakit & Fasilitas Kesehatan", "APBD Kabupaten",
                "SK-BPN/1984/01", "1984-03-12", "SK-BPN/1984", "1984-03-15", "KW-TNH-1984", "1984-03-16", "DOK-BPN-1984", "1984-03-16",
                150000000, 8300000000, 50000000, 8500000000,
                "042/SP2D/1984", "1984-03-20", "000.2.3.2/042/1984", "1984-03-22", "Batas lahan terpagar penuh dan sertifikat hak pakai aktif"
            ]
        ];
        const wsKibA = XLSX.utils.aoa_to_sheet(kibAData);
        XLSX.utils.book_append_sheet(wb, wsKibA, "2. A");

        // 3. KIB B
        const kibBData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["RINCIAN BELANJA MODAL PERALATAN DAN MESIN (KIB B >= RP 300.000) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "NAMA BARANG", "KODE BARANG (108)", "MERK", "TYPE", "UKURAN / SPESIFIKASI", "BAHAN", "NO PABRIK / RANGKA", "TAHUN PEROLEHAN",
                "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO FAKTUR/INVOICE", "TGL FAKTUR/INVOICE",
                "JUMLAH", "SATUAN", "HARGA SATUAN (RP)", "BIAYA ADM PROYEK (RP)", "TOTAL NILAI REALISASI (RP)",
                "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "RUANG / UNIT PEMEGANG", "KETERANGAN"
            ],
            [
                1, "Submersible Pump 7.5 HP", "1.3.2.01.03.05.005", "Franklin Electric", "2347288602G", "7.5hp, 3phase, max 139m", "Campuran Baja", "23K14-17-0006", "2025",
                "019/SPK-PMP/VI/2025", "2025-06-01", "019/BN.BA/VI/2025", "2025-06-04", "KW-019/PMP/2025", "2025-06-05", "INV-2025-091", "2025-06-05",
                1, "Unit", 41501900, 1000000, 42501900,
                "019/SP2D/BLUD/2025", "2025-06-10", "000.2.3.2/019/2025", "2025-06-12", "Instalasi Sanitasi & IPSRS", "Operasional normal pompa cadangan air bersih"
            ],
            [
                2, "CT-Scan 128 Slice High Resolution", "1.3.2.02.01.01.005", "Siemens SOMATOM", "128-Slice Perspective", "Unit Radiologi Medis Lengkap", "Logam & Elektronik Medis", "SN-99812-RAD", "2024",
                "045/SPK-RAD/VII/2024", "2024-07-10", "045/RSUD/VII/2024", "2024-07-15", "KW-045/RAD/2024", "2024-07-16", "INV-RAD-2024-01", "2024-07-16",
                1, "Unit", 1445000000, 5000000, 1450000000,
                "045/SP2D/DAK/2024", "2024-07-20", "000.2.3.2/045/2024", "2024-07-22", "Instalasi Radiologi", "Terkalibrasi BAPETEN dan operasional 24 jam"
            ],
            [
                3, "Pompa Air Shimizu PS-130", "1.3.2.01.03.05.010", "Shimizu", "PS-130 BIT", "Daya hisap 9m dorong 35m", "Campuran Besi", "PS-130-001", "2025",
                "020/SPK-PMP/VI/2025", "2025-06-01", "020/BN.BA/VI/2025", "2025-06-04", "KW-020/PMP/2025", "2025-06-05", "INV-2025-092", "2025-06-05",
                1, "Unit", 1850000, 0, 1850000,
                "020/SP2D/BLUD/2025", "2025-06-10", "000.2.3.2/020/2025", "2025-06-12", "Instalasi Gizi", "Pendorong air ke tandon instalasi gizi klinik"
            ]
        ];
        const wsKibB = XLSX.utils.aoa_to_sheet(kibBData);
        XLSX.utils.book_append_sheet(wb, wsKibB, "3. B");

        // 4. KIB C
        const kibCData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["RINCIAN BELANJA MODAL GEDUNG DAN BANGUNAN (KIB C) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "NAMA BANGUNAN", "KODE BARANG (108)", "BERTINGKAT", "BETON", "LUAS (M2)", "LETAK / LOKASI",
                "STATUS TANAH", "KODE ASET TANAH KIB A", "NO SERTIFIKAT",
                "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO FAKTUR/INVOICE", "TGL FAKTUR/INVOICE",
                "NILAI PERENCANAAN (RP)", "NILAI FISIK TERMIN (RP)", "NILAI PENGAWASAN (RP)", "NILAI PIP (RP)", "TOTAL NILAI GEDUNG (RP)",
                "NO SP2D", "TGL SP2D", "NO BAST MC", "TGL BAST MC", "KETERANGAN"
            ],
            [
                1, "Gedung Paviliun Graha Amukti VIP", "1.3.3.01.01.01.008", "Bertingkat (2 Lt)", "Beton Bertulang", 2800, "Kompleks Barat RSUD Dr. H. Koesnandi",
                "Tanah Hak Pakai RSUD", "1.3.1.01.01.02.013", "HP-108/1984",
                "SPK-GRH/2018/01", "2018-02-10", "SPK-GRH/2018", "2018-02-15", "KW-GRH-2018", "2018-02-16", "INV-GRH-2018", "2018-02-16",
                120000000, 3950000000, 90000000, 40000000, 4200000000,
                "078/SP2D/2018", "2018-03-01", "000.2.3.2/078/2018", "2018-03-05", "Kapasitas 24 kamar VIP & VVIP fasilitas lengkap"
            ],
            [
                2, "Gedung Instalasi Bedah Sentral IBS 2 Lantai", "1.3.3.01.01.01.012", "Bertingkat (2 Lt)", "Beton Bertulang", 3200, "Kompleks Utama Sentral Medis",
                "Tanah Hak Pakai RSUD", "1.3.1.01.01.02.013", "HP-108/1984",
                "SPK-IBS/2022/03", "2022-04-10", "SPK-IBS/2022", "2022-04-15", "KW-IBS-2022", "2022-04-16", "INV-IBS-2022", "2022-04-16",
                180000000, 6420000000, 130000000, 70000000, 6800000000,
                "112/SP2D/2022", "2022-05-02", "000.2.3.2/112/2022", "2022-05-05", "6 Ruang Operasi Modular MOT standard internasional"
            ]
        ];
        const wsKibC = XLSX.utils.aoa_to_sheet(kibCData);
        XLSX.utils.book_append_sheet(wb, wsKibC, "4. C");

        // 5. KIB D
        const kibDData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["RINCIAN BELANJA MODAL JALAN, IRIGASI DAN JARINGAN (KIB D) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "NAMA JARINGAN", "KODE BARANG (108)", "KONSTRUKSI JARINGAN", "PANJANG (M)", "LEBAR (M)", "LUAS (M2)", "LETAK / LOKASI",
                "STATUS TANAH", "KODE ASET TANAH KIB A",
                "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO FAKTUR/INVOICE", "TGL FAKTUR/INVOICE",
                "NILAI PERENCANAAN (RP)", "NILAI FISIK (RP)", "NILAI PENGAWASAN (RP)", "NILAI PIP (RP)", "TOTAL NILAI JARINGAN (RP)",
                "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "KETERANGAN"
            ],
            [
                1, "Jaringan Pipa Oksigen Sentral Medis", "1.3.4.03.01.01.004", "Copper Pipe Medical Grade Sentral", 1200, 0.05, 60, "Seluruh Paviliun & IGD RSUD",
                "Tanah Hak Pakai RSUD", "1.3.1.01.01.02.013",
                "SPK-OKS/2020/08", "2020-05-14", "SPK-OKS/2020", "2020-05-20", "KW-OKS-2020", "2020-05-21", "INV-OKS-2020", "2020-05-21",
                30000000, 580000000, 25000000, 15000000, 650000000,
                "088/SP2D/2020", "2020-06-01", "000.2.3.2/088/2020", "2020-06-05", "Pipa gas medis tembaga terpasang ke 120 bed pasien"
            ],
            [
                2, "Jaringan Instalasi Pengolahan Air Limbah (IPAL)", "1.3.4.03.01.02.001", "Pipa HDPE Bawah Tanah & Reaktor Anaerob", 850, 0.2, 170, "Area IPAL Belakang RSUD",
                "Tanah Hak Pakai RSUD", "1.3.1.01.01.02.013",
                "SPK-IPL/2023/04", "2023-06-10", "SPK-IPL/2023", "2023-06-15", "KW-IPL-2023", "2023-06-16", "INV-IPL-2023", "2023-06-16",
                40000000, 750000000, 38000000, 22000000, 850000000,
                "145/SP2D/2023", "2023-07-02", "000.2.3.2/145/2023", "2023-07-05", "Kapasitas olah 250 m3/hari sesuai baku mutu lingkungan"
            ]
        ];
        const wsKibD = XLSX.utils.aoa_to_sheet(kibDData);
        XLSX.utils.book_append_sheet(wb, wsKibD, "5. D");

        // 6. KIB E
        const kibEData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["RINCIAN BELANJA MODAL ASET TETAP LAINNYA (KIB E) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "NAMA BARANG", "KODE BARANG (108)", "JUDUL / SPESIFIKASI", "PENCIPTA / PENERBIT / ASAL", "TAHUN PEROLEHAN",
                "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO FAKTUR/INVOICE", "TGL FAKTUR/INVOICE",
                "JUMLAH", "SATUAN", "HARGA SATUAN (RP)", "BIAYA ADM PROYEK (RP)", "TOTAL NILAI REALISASI (RP)",
                "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "RUANG / UNIT PEMEGANG", "KETERANGAN"
            ],
            [
                1, "Buku Jurnal Kedokteran & Farmakologi", "1.3.5.01.01.01.002", "Hardcover Vol 1-12 Lengkap Edisi Internasional", "Elsevier / PubMed Press", "2021",
                "012/SPK-BKO/2021", "2021-08-01", "012/PERPUS/2021", "2021-08-05", "KW-BKO-2021", "2021-08-06", "INV-BKO-2021", "2021-08-06",
                50, "Buku", 1660000, 2000000, 85000000,
                "055/SP2D/2021", "2021-08-20", "000.2.3.2/055/2021", "2021-08-22", "Perpustakaan & Diklit Medis", "Tersedia untuk referensi dokter spesialis & residen"
            ],
            [
                2, "Lukisan Sejarah RSUD & Ornamen Seni Budaya", "1.3.5.02.01.01.005", "Lukisan Kanvas Cat Minyak Bingkai Kayu Jati Ukir", "Seniman Budaya Bondowoso", "2023",
                "033/SPK-ART/2023", "2023-09-10", "033/ART/2023", "2023-09-15", "KW-ART-2023", "2023-09-16", "INV-ART-2023", "2023-09-16",
                5, "Unit", 6800000, 1000000, 35000000,
                "099/SP2D/2023", "2023-09-28", "000.2.3.2/099/2023", "2023-09-30", "Lobi Utama & Aula Pertemuan", "Hiasan bernilai sejarah perkembangan RSUD"
            ]
        ];
        const wsKibE = XLSX.utils.aoa_to_sheet(kibEData);
        XLSX.utils.book_append_sheet(wb, wsKibE, "6. E");

        // 7. KIB F
        const kibFData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["RINCIAN BELANJA MODAL KONSTRUKSI DALAM PENGERJAAN (KIB F) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "NAMA PROYEK KDP", "KODE BARANG (108)", "LUAS RENCANA (M2)", "PROGRES (%)", "BANGUNAN", "KONSTRUKSI BETON",
                "LETAK / LOKASI PROYEK", "STATUS TANAH", "KODE ASET TANAH KIB A", "NO SERTIFIKAT", "TGL MULAI (SPMK)", "TARGET SELESAI (PHO)",
                "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI TERMIN", "TGL KWITANSI TERMIN", "NO INVOICE", "TGL INVOICE",
                "NILAI PERENCANAAN (RP)", "NILAI FISIK TERMIN (RP)", "NILAI PENGAWASAN (RP)", "NILAI PIP (RP)", "TOTAL AKUMULASI BIAYA KDP (RP)",
                "NO SP2D", "TGL SP2D", "NO BAST MC", "TGL BAST MC", "KETERANGAN"
            ],
            [
                1, "Pembangunan Gedung Rawat Inap Terpadu Lt 3", "1.3.6.01.01.01.001", 3200, "60%", "Bertingkat (3 Lt)", "Beton Bertulang K-350",
                "Kompleks Belakang Paviliun Melati", "Tanah Hak Pakai RSUD", "1.3.1.01.01.02.013", "HP-108/1984", "2026-01-15", "2026-11-30",
                "015/SPK-KDP/2026", "2026-01-10", "015/KDP/2026", "2026-01-15", "KW-KDP-2026", "2026-06-20", "INV-KDP-2026", "2026-06-20",
                125000000, 3250000000, 85000000, 40000000, 3500000000,
                "015/SP2D/2026", "2026-06-28", "000.2.3.2/015/MC-03/2026", "2026-06-30", "Progres fisik pengerjaan struktur kolom & dak lantai 3 (60%)"
            ],
            [
                2, "Pembangunan Gedung Pusat Diagnostik Terpadu", "1.3.6.01.01.01.002", 4500, "45%", "Bertingkat (4 Lt)", "Beton Bertulang & Baja WF",
                "Area Timur Parkir Sentral RSUD", "Tanah Hak Pakai RSUD", "1.3.1.01.01.02.013", "HP-108/1984", "2026-02-01", "2026-12-15",
                "022/SPK-PDT/2026", "2026-01-20", "022/PDT/2026", "2026-01-25", "KW-PDT-2026", "2026-06-15", "INV-PDT-2026", "2026-06-15",
                180000000, 4850000000, 110000000, 60000000, 5200000000,
                "022/SP2D/2026", "2026-06-25", "000.2.3.2/022/MC-02/2026", "2026-06-27", "Pengerjaan fisik lantai 2 dan instalasi utilitas dasar (45%)"
            ]
        ];
        const wsKibF = XLSX.utils.aoa_to_sheet(kibFData);
        XLSX.utils.book_append_sheet(wb, wsKibF, "7. F");

        // 8. ATB
        const atbData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["RINCIAN BELANJA MODAL ASET TIDAK BERWUJUD (ATB / 1.5.3) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "NAMA ASET / LISENSI", "KODE BARANG (108)", "JUDUL SISTEM / LISENSI", "PENCIPTA / VENDOR", "SPESIFIKASI LISENSI",
                "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO FAKTUR/INVOICE", "TGL FAKTUR/INVOICE",
                "JUMLAH", "SATUAN", "HARGA SATUAN (RP)", "BIAYA ADM PROYEK (RP)", "TOTAL NILAI ATB (RP)",
                "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "RUANG / UNIT PEMEGANG", "KETERANGAN"
            ],
            [
                1, "Software SIMRS Terintegrasi & EMR Cloud", "1.5.3.01.01.01.005", "SIMAT Health Enterprise Server V4.2", "PT. Medika Solusindo Digital", "Enterprise Server Multi-Unit Unlimited Client",
                "077/SPK-SIMRS/2024", "2024-03-20", "077/SIMRS/2024", "2024-03-25", "KW-SIMRS-2024", "2024-03-26", "INV-SIMRS-2024", "2024-03-26",
                1, "Lisensi", 445000000, 5000000, 450000000,
                "077/SP2D/2024", "2024-04-05", "000.2.3.2/077/2024", "2024-04-08", "Instalasi IT & PDE", "Terintegrasi RME SatuSehat & BPJS VClaim"
            ],
            [
                2, "Lisensi PACS Radiologi Digital Server", "1.5.3.01.01.01.008", "PACS DICOM Radiology Server 3D", "PT. Siemens Healthcare", "Perpetual License DICOM Viewer & Storage",
                "089/SPK-PACS/2024", "2024-05-12", "089/PACS/2024", "2024-05-15", "KW-PACS-2024", "2024-05-16", "INV-PACS-2024", "2024-05-16",
                1, "Lisensi", 270000000, 5000000, 275000000,
                "089/SP2D/2024", "2024-05-25", "000.2.3.2/089/2024", "2024-05-28", "Instalasi Radiologi", "Arsip gambar digital CT-Scan & Rontgen terpusat"
            ]
        ];
        const wsAtb = XLSX.utils.aoa_to_sheet(atbData);
        XLSX.utils.book_append_sheet(wb, wsAtb, "8. ATB");

        // 9. EXTRACOM
        const extracomData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["RINCIAN BELANJA BARANG EKSTRAKOMTABEL (PERALATAN DAN MESIN < RP 300.000) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "NAMA BARANG", "KODE BARANG (108)", "MERK", "TYPE", "UKURAN / SPESIFIKASI", "BAHAN", "NO PABRIK / SERI", "TAHUN PEROLEHAN",
                "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO FAKTUR/INVOICE", "TGL FAKTUR/INVOICE",
                "JUMLAH", "SATUAN", "HARGA SATUAN (RP)", "BIAYA ADM PROYEK (RP)", "TOTAL NILAI REALISASI (RP)",
                "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "RUANG / UNIT PEMEGANG", "KETERANGAN"
            ],
            [
                1, "Gunting Angkat Jahitan Littauer 14cm", "1.3.2.02.01.01.099", "Surgical Instrument", "Littauer 14cm", "Panjang 14cm Stainless Steel", "Stainless Steel Medis", "LT-14-001", "2026",
                "011/SPK-EXT/2026", "2026-02-05", "011/EXT/2026", "2026-02-10", "KW-EXT-011", "2026-02-11", "INV-EXT-011", "2026-02-11",
                10, "Pcs", 85000, 0, 850000,
                "011/SP2D/2026", "2026-02-20", "000.2.3.2/011/2026", "2026-02-22", "IGD & Poliklinik Bedah", "Barang Ekstrakomtabel (Nilai < Rp 300.000)"
            ],
            [
                2, "Timbangan Bayi Analog Akurat", "1.3.2.02.01.02.045", "Crown Baby", "CR-20 Analog", "Kapasitas 20kg Akurasi 50gr", "Plastik ABS & Pegas Baja", "CRW-2026-01", "2026",
                "012/SPK-EXT/2026", "2026-02-15", "012/EXT/2026", "2026-02-20", "KW-EXT-012", "2026-02-21", "INV-EXT-012", "2026-02-21",
                5, "Unit", 245000, 0, 1225000,
                "012/SP2D/2026", "2026-03-01", "000.2.3.2/012/2026", "2026-03-05", "Paviliun Anak & Perinatologi", "Barang Ekstrakomtabel (Nilai < Rp 300.000)"
            ],
            [
                3, "Tensimeter Raksa Meja Standard", "1.3.2.02.01.02.088", "General Care", "Desk Type GC-01", "Skala 0-300 mmHg dengan Manset", "Aluminium & Karet Medis", "GC-DESK-2026", "2026",
                "014/SPK-EXT/2026", "2026-03-05", "014/EXT/2026", "2026-03-10", "KW-EXT-014", "2026-03-11", "INV-EXT-014", "2026-03-11",
                8, "Unit", 280000, 0, 2240000,
                "014/SP2D/2026", "2026-03-20", "000.2.3.2/014/2026", "2026-03-22", "Rawat Inap & Poliklinik", "Barang Ekstrakomtabel (Nilai < Rp 300.000)"
            ]
        ];
        const wsExtracom = XLSX.utils.aoa_to_sheet(extracomData);
        XLSX.utils.book_append_sheet(wb, wsExtracom, "9. Exstracom");

        // DOWNLOAD
        const fileName = 'DATA_ASTAP_RSUD_KOESNANDI_2026_KIB_A-F_ATB_EXTRACOM.xlsx';
        XLSX.writeFile(wb, fileName);
        alert('✅ Berhasil mendownload: ' + fileName + '\nFile Excel telah dibagi menjadi 9 Sheet: Rekapitulasi, 2. A, 3. B, 4. C, 5. D, 6. E, 7. F, 8. ATB, dan 9. Exstracom!');
    }
    </script>

    <script>
        window.__simatAstaps = @json(!empty($astaps) ? $astaps : []);

        function astapCatalog() {
            return {
                searchQuery: '',
                categoryFilter: 'all',
                kondisiFilter: 'all',
                asalUsulFilter: 'all',
                tahunFilter: 'all',
                showAddModal: false,
                showEditModal: false,
                showDetailModal: false,
                showQrModal: false,
                selectedAstap: null,
                selectedAstapDetail: null,
                selectedQrItem: null,
                detailKondisiFilter: 'all',
                detailPenempatanFilter: 'all',
                detailSearchQuery: '',
                
                // Multi-Step Form State
                currentStep: 1,
                totalSteps: 4,
                
                // Form Data Model
                formData: {
                    category: 'KIB A',
                    jenis_aset_kode: '1.3.1',
                    jenis_aset_nama: 'TANAH',
                    sub_rincian_kode: '1.3.1.01.01.01',
                    sub_rincian_nama: 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL',
                    kode_barang: '1.3.1.01.01.01.001',
                    nama_barang: '',
                    volume_satuan: '1,5 Hektar (15.000 m²)',
                    
                    // Blok 1: Penganggaran & Akuntansi
                    program_kode: '0.00.01',
                    program_nama: 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota',
                    kegiatan_kode: '0.00.01.2.10',
                    kegiatan_nama: 'Peningkatan Pelayanan BLUD',
                    sub_kegiatan_kode: '0.00.01.2.10.0001',
                    sub_kegiatan_nama: 'Pelayanan dan Penunjang Pelayanan BLUD',
                    rekening_kode: '5.2.02.01.01.0001',
                    rekening_nama: 'Belanja Modal Tanah',
                    jumlah_anggaran: '',
                    jumlah_realisasi: '',
                    
                    // Blok 2: Dokumen Pembelian & Pengadaan
                    spk_nomor: '',
                    spk_tanggal: '',
                    surat_pesanan_nomor: '',
                    surat_pesanan_tanggal: '',
                    kwitansi_nomor: '',
                    kwitansi_tanggal: '',
                    faktur_nomor: '',
                    faktur_tanggal: '',
                    
                    // Blok 3: Spesifikasi Teknis
                    luas_tanah: '',
                    tahun_pengadaan: new Date().getFullYear(),
                    letak_lokasi: '',
                    hak_tanah: 'Hak Pakai',
                    sertifikat_nomor: '',
                    sertifikat_tanggal: '',
                    penggunaan: 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                    asal_usul: 'APBD Kabupaten',
                    harga_satuan: '',
                    kondisi: 'Baik',
                    keterangan: '',
                    
                    // Non-tanah extras
                    merk: '',
                    type: '',
                    ukuran: '',
                    bahan: '',
                    no_pabrik: ''
                },

                selectCategory(kib, kode, nama) {
                    this.formData.category = kib;
                    this.formData.jenis_aset_kode = kode;
                    this.formData.jenis_aset_nama = nama;
                },

                resetModal() {
                    this.currentStep = 1;
                    this.showAddModal = false;
                },

                openDetail(item) {
                    this.selectedAstapDetail = item;
                    this.detailKondisiFilter = 'all';
                    this.detailPenempatanFilter = 'all';
                    this.detailSearchQuery = '';
                    this.showDetailModal = true;
                },

                getRiwayatServis(astap) {
                    if (!astap) return [];
                    const stored = localStorage.getItem('simat_pemeliharaans');
                    if (!stored) {
                        const defaultList = [
                            { id: 1, kode: 'MTN-2026-003', nama: 'CT-Scan 128 Slice Siemens', kode_barang: '1.3.2.02.01.01.005', jenis: 'Kalibrasi Rutin & QC BAPETEN', tgl: '05 Ags 2026', tgl_selesai: '10 Ags 2026', biaya: 'Rp 25.000.000', pelaksana: 'PT. Siemens Healthcare Indonesia', status: 'Selesai', keterangan: 'Hasil uji fungsi akurat dan sertifikat kalibrasi terbit resmi' },
                            { id: 2, kode: 'MTN-2026-007', nama: 'Submersible Pump 7.5 HP', kode_barang: '1.3.2.01.03.05.005', jenis: 'Penggantian Seal & Bearing', tgl: '10 Ags 2026', tgl_selesai: '-', biaya: 'Rp 4.500.000', pelaksana: 'Teknisi IPSRS RSUD', status: 'Dalam Pengerjaan', keterangan: 'Sedang dibongkar untuk pembersihan kerak impeller' }
                        ];
                        localStorage.setItem('simat_pemeliharaans', JSON.stringify(defaultList));
                        return defaultList.filter(p => this.isMatchAstap(p, astap));
                    }
                    try {
                        const list = JSON.parse(stored);
                        return list.filter(p => this.isMatchAstap(p, astap));
                    } catch(e) {
                        return [];
                    }
                },

                isMatchAstap(pemeliharaan, astap) {
                    if (!pemeliharaan || !astap) return false;
                    const pNama = (pemeliharaan.nama || '').toLowerCase().trim();
                    const aNama = (astap.nama_barang || '').toLowerCase().trim();
                    const pKode = (pemeliharaan.kode_barang || '').toLowerCase().trim();
                    const aKode = (astap.kode_barang || '').toLowerCase().trim();

                    if (pKode && aKode && pKode === aKode) return true;
                    if (pNama && aNama) {
                        if (pNama === aNama || pNama.includes(aNama) || aNama.includes(pNama)) return true;
                        const words = aNama.split(' ').filter(w => w.length > 3);
                        return words.some(w => pNama.includes(w));
                    }
                    return false;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.categoryFilter = 'all';
                    this.kondisiFilter = 'all';
                    this.asalUsulFilter = 'all';
                    this.tahunFilter = 'all';
                },

                astaps: window.__simatAstaps || [],

                // Hitung statistik kondisi dari registers suatu aset (Baik, Rusak Ringan, Rusak Berat)
                getKondisiStats(item) {
                    const regs = item.registers || [];
                    const total = regs.length;
                    if (total === 0) {
                        const k = item.kondisi || 'Baik';
                        return { total: 1, baik: k==='Baik'?1:0, rusak_ringan: k==='Rusak Ringan'?1:0, rusak_berat: k==='Rusak Berat'?1:0, pct_baik: k==='Baik'?100:0, pct_rr: k==='Rusak Ringan'?100:0, pct_rb: k==='Rusak Berat'?100:0, kondisi_dominan: k };
                    }
                    const baik = regs.filter(r => (r.kondisi||'Baik') === 'Baik').length;
                    const rr   = regs.filter(r => r.kondisi === 'Rusak Ringan').length;
                    const rb   = regs.filter(r => r.kondisi === 'Rusak Berat').length;
                    const dominan = baik >= rr && baik >= rb ? 'Baik' : (rr >= rb ? 'Rusak Ringan' : 'Rusak Berat');
                    return {
                        total,
                        baik, rusak_ringan: rr, rusak_berat: rb,
                        pct_baik: Math.round(baik / total * 100),
                        pct_rr:   Math.round(rr   / total * 100),
                        pct_rb:   Math.round(rb   / total * 100),
                        kondisi_dominan: dominan
                    };
                },

                get filteredAstaps() {
                    const query = (this.searchQuery || '').toLowerCase();
                    return this.astaps.filter(item => {
                        const matchSearch = (item.nama_barang || '').toLowerCase().includes(query) || 
                                            (item.volume_satuan || '').toLowerCase().includes(query) ||
                                            (item.tahun_perolehan || '').toLowerCase().includes(query) ||
                                            (item.kode_barang || '').toLowerCase().includes(query) ||
                                            (item.merk || '').toLowerCase().includes(query);
                                            
                        const matchCategory = this.categoryFilter === 'all' || item.category === this.categoryFilter;
                        // Filter kondisi berdasarkan kondisi dominan dari registers
                        const stats = this.getKondisiStats(item);
                        const matchKondisi = this.kondisiFilter === 'all' || stats.kondisi_dominan === this.kondisiFilter;
                        const matchAsalUsul = this.asalUsulFilter === 'all' || item.asal_usul === this.asalUsulFilter;
                        const matchTahun = this.tahunFilter === 'all' || item.tahun_perolehan === this.tahunFilter;
                        
                        return matchSearch && matchCategory && matchKondisi && matchAsalUsul && matchTahun;
                    });
                },

                get filteredRegisters() {
                    if (!this.selectedAstapDetail || !this.selectedAstapDetail.registers) return [];
                    const query = (this.detailSearchQuery || '').toLowerCase().trim();
                    return this.selectedAstapDetail.registers.filter(reg => {
                        const matchKondisi = this.detailKondisiFilter === 'all' || reg.kondisi === this.detailKondisiFilter;
                        
                        let matchPenempatan = true;
                        if (this.detailPenempatanFilter === 'sudah') {
                            matchPenempatan = !!reg.ruang_pemegang;
                        } else if (this.detailPenempatanFilter === 'belum') {
                            matchPenempatan = !reg.ruang_pemegang;
                        }

                        const matchQuery = !query || 
                            (reg.nibar || '').toLowerCase().includes(query) ||
                            (reg.no_register || '').toLowerCase().includes(query) ||
                            (reg.ruang_pemegang || '').toLowerCase().includes(query);

                        return matchKondisi && matchPenempatan && matchQuery;
                    });
                },

                downloadQrCode(item) {
                    const riwayat = this.getRiwayatServis(item);
                    let riwayatText = 'Tidak Ada Riwayat Perbaikan';
                    if (riwayat && riwayat.length > 0) {
                        riwayatText = riwayat.length + ' Kali Perbaikan (Terakhir: ' + riwayat[0].jenis + ' - ' + riwayat[0].status + ')';
                    }

                    this.selectedQrItem = {
                        kode_barang: item.kode_barang,
                        nama_barang: item.nama_barang,
                        category: item.category,
                        tahun_perolehan: item.tahun_perolehan,
                        ruang_pemegang: item.letak_lokasi || 'Gudang Aset Utama / Belum Ditempatkan',
                        kondisi: item.kondisi || 'Baik',
                        riwayat_servis: riwayatText
                    };
                    this.showQrModal = true;
                },

                downloadQrCodeNibar(reg, astap) {
                    const riwayat = this.getRiwayatServis(astap);
                    let riwayatText = 'Tidak Ada Riwayat Perbaikan';
                    if (riwayat && riwayat.length > 0) {
                        riwayatText = riwayat.length + ' Kali Perbaikan (Terakhir: ' + riwayat[0].jenis + ' - ' + riwayat[0].status + ')';
                    }

                    this.selectedQrItem = {
                        kode_barang: reg.nibar || (astap ? astap.kode_barang : 'ASET'),
                        nama_barang: (astap ? astap.nama_barang : 'Aset') + ' (Register ' + reg.no_register + ')',
                        category: astap ? astap.category : 'NIBAR',
                        tahun_perolehan: astap ? astap.tahun_perolehan : '-',
                        ruang_pemegang: reg.ruang_pemegang || 'Belum Ditempatkan / Di Gudang Aset',
                        kondisi: reg.kondisi || (astap ? astap.kondisi : 'Baik'),
                        riwayat_servis: riwayatText
                    };
                    this.showQrModal = true;
                },

                getQrPayloadUrl(item) {
                    if (!item) return '';
                    return window.location.origin + '/scan/' + encodeURIComponent(item.kode_barang || '');
                },

                getQrPayloadString(item) {
                    if (!item) return '';
                    return [
                        '=== SIMAT-RK RSUD DR. H. KOESNANDI ===',
                        '📦 Nama Barang : ' + (item.nama_barang || '-'),
                        '🏷️ NIBAR / Kode : ' + (item.kode_barang || '-'),
                        '📅 Thn Perolehan : ' + (item.tahun_perolehan || '-'),
                        '📍 Lokasi Penempatan: ' + (item.ruang_pemegang || 'Belum Ditempatkan'),
                        '⚙️ Kondisi Aset : ' + (item.kondisi || 'Baik'),
                        '🛠️ Riwayat Servis: ' + (item.riwayat_servis || 'Tidak Ada Riwayat Perbaikan'),
                        '---------------------------------------',
                        'Verifikasi Publik: ' + this.getQrPayloadUrl(item)
                    ].join('\n');
                },

                downloadQrImage() {
                    if (!this.selectedQrItem) return;
                    const scanUrl = this.getQrPayloadUrl(this.selectedQrItem);
                    const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=400x400&margin=10&data=' + encodeURIComponent(scanUrl);
                    fetch(qrUrl)
                        .then(res => res.blob())
                        .then(blob => {
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            a.download = 'QR_CODE_' + (this.selectedQrItem.kode_barang || 'ASET').replace(/\./g, '_') + '.png';
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                        })
                        .catch(() => {
                            window.open(qrUrl, '_blank');
                        });
                },

                downloadExcel() {
                    exportAstapToExcel();
                },

                editRegister(reg) {
                    if (!reg) return;
                    const newRuang = prompt('✏️ UBAH LOKASI PENEMPATAN RUANGAN:\n\nUnit NIBAR: ' + (reg.nibar || reg.no_register) + '\n\nMasukkan nama ruangan / penempatan baru:', reg.ruang_pemegang || '');
                    if (newRuang === null) return;
                    
                    const newKondisi = prompt('⚙️ UBAH KONDISI UNIT:\n\nPilihan kondisi valid: Baik, Rusak Ringan, Rusak Berat\n\nMasukkan kondisi baru:', reg.kondisi || 'Baik');
                    if (newKondisi === null) return;

                    const cleanedKondisi = newKondisi.trim();
                    if (!['Baik', 'Rusak Ringan', 'Rusak Berat'].includes(cleanedKondisi)) {
                        alert('⚠️ Kondisi tidak valid! Mohon masukkan salah satu: Baik, Rusak Ringan, atau Rusak Berat.');
                        return;
                    }

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/astap-register/' + reg.id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            ruang_pemegang: newRuang.trim(),
                            kondisi: cleanedKondisi
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            reg.ruang_pemegang = newRuang.trim();
                            reg.kondisi = cleanedKondisi;
                            alert('✅ Data register unit berhasil diperbarui!');
                        } else {
                            alert('⚠️ Gagal memperbarui: ' + (data.message || 'Terjadi kesalahan'));
                        }
                    })
                    .catch(err => console.log(err));
                },

                deleteRegister(reg) {
                    if (!reg) return;
                    if (confirm('⚠️ HAPUS REGISTER UNIT NIBAR?\n\nApakah Anda yakin ingin menghapus unit register:\nNIBAR: ' + (reg.nibar || reg.no_register) + '?')) {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        fetch('/astap-register/' + reg.id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                if (this.selectedAstapDetail && this.selectedAstapDetail.registers) {
                                    this.selectedAstapDetail.registers = this.selectedAstapDetail.registers.filter(r => r.id !== reg.id);
                                }
                                alert('✅ Unit register NIBAR berhasil dihapus.');
                            }
                        })
                        .catch(err => console.log(err));
                    }
                },

                deleteAstap(item) {
                    if (!item) return;
                    if (confirm('⚠️ HAPUS DATA ASTAP?\n\nApakah Anda yakin ingin menghapus data aset:\n"' + item.nama_barang + '" (' + item.kode_barang + ')?\n\nSemua data register NIBAR terkait juga akan dihapus secara permanen.')) {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        fetch('/astap/' + item.id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        }).catch(err => console.log(err));

                        this.astaps = this.astaps.filter(a => a.id !== item.id);
                    }
                },

                openEdit(item) {
                    if (!item || !item.id) return;
                    window.location.href = '/astap/' + item.id + '/edit';
                }
            };
        }
    </script>

    <div x-data="astapCatalog()" x-cloak class="space-y-6">

        <!-- ========================================================================= -->
        <!-- HEADER BANNER & STATISTIK RINGKAS                                         -->
        <!-- ========================================================================= -->
        <div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>KATALOG INVENTARIS ASET TETAP RSUD DR. H. KOESNANDI</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Data ASTAP</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pencatatan daftar aset tetap rumah sakit, tahun perolehan, lokasi penempatan unit/paviliun, nilai realisasi anggaran, serta status kondisi terkini.
                    </p>
                </div>
                
                <!-- Aksi Utama -->
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                    <a href="{{ route('bast.index') }}"
                        class="px-3.5 py-2.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>📑 Cetak BAST Triwulan</span>
                    </a>
                    @endif

                    <button type="button" @click="downloadExcel()"
                        class="px-3.5 py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-emerald-400 border border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export Excel</span>
                    </button>

                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                    <a href="{{ route('astap.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Tambah ASTAP</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Aset</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="astaps.length + ' Item'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Investasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400 font-mono">Rp 15,87 M</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🟢</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Kondisi Baik</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-300">75% (6 Aset)</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Unit Tersebar</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300">6 Lokasi RSUD</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- AREA PEMFILTERAN & PENCARIAN (FULL WIDTH, RAPID & BERSIH)                 -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Category Pills (Wrapping & Always Visible) -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">KIB:</span>
                    <button type="button" @click="categoryFilter = 'all'"
                        :class="categoryFilter === 'all' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua KIB
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB A'"
                        :class="categoryFilter === 'KIB A' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🌾 KIB A (Tanah)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB B'"
                        :class="categoryFilter === 'KIB B' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🔬 KIB B (Mesin)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB C'"
                        :class="categoryFilter === 'KIB C' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏢 KIB C (Gedung)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB D'"
                        :class="categoryFilter === 'KIB D' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🚰 KIB D (Jaringan)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB E'"
                        :class="categoryFilter === 'KIB E' ? 'bg-orange-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        📦 KIB E (Lainnya)
                    </button>
                    <button type="button" @click="categoryFilter = 'KIB F'"
                        :class="categoryFilter === 'KIB F' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏗️ KIB F (KDP)
                    </button>
                    <button type="button" @click="categoryFilter = 'ATB'"
                        :class="categoryFilter === 'ATB' ? 'bg-indigo-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        💻 8. ATB (Tidak Berwujud)
                    </button>
                    <button type="button" @click="categoryFilter = 'EXTRACOM'"
                        :class="categoryFilter === 'EXTRACOM' ? 'bg-amber-400 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏷️ 9. Ekstrakomtabel (&lt; Rp 300rb)
                    </button>
                </div>

                <!-- Live Search Bar + Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama barang / lokasi penempatan / tahun / merk..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                        <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-emerald-400 font-bold" x-text="filteredAstaps.length"></span> dari <span class="text-white font-bold" x-text="astaps.length"></span> Data
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>

                <!-- Advanced Filter Collapsible Bar -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 pt-3 border-t border-slate-800/60">
                    
                    <!-- Filter KIB -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Klasifikasi KIB</label>
                        <select x-model="categoryFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua KIB (A - F, ATB &amp; Extracom)</option>
                            <option value="KIB A">2. A - Aset Tanah (1.3.1)</option>
                            <option value="KIB B">3. B - Peralatan &amp; Mesin &gt;= 300rb (1.3.2)</option>
                            <option value="KIB C">4. C - Gedung &amp; Bangunan (1.3.3)</option>
                            <option value="KIB D">5. D - Jalan, Irigasi &amp; Jaringan (1.3.4)</option>
                            <option value="KIB E">6. E - Aset Tetap Lainnya (1.3.5)</option>
                            <option value="KIB F">7. F - Konstruksi KDP (1.3.6)</option>
                            <option value="ATB">8. ATB - Aset Tidak Berwujud (1.5.3)</option>
                            <option value="EXTRACOM">9. Exstracom - Ekstrakomtabel &lt; 300rb</option>
                        </select>
                    </div>

                    <!-- Filter Sumber Dana / Asal Usul -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Sumber Dana / Asal Usul</label>
                        <select x-model="asalUsulFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua Sumber Dana</option>
                            <option value="BLUD RSUD">BLUD RSUD</option>
                            <option value="APBD Kabupaten">APBD Kabupaten</option>
                            <option value="DAK Kesehatan">DAK Kesehatan</option>
                        </select>
                    </div>

                    <!-- Filter Kondisi -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Aset</label>
                        <select x-model="kondisiFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua Kondisi</option>
                            <option value="Baik">Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                            <option value="Dalam Renovasi">Dalam Renovasi</option>
                        </select>
                    </div>

                    <!-- Filter Tahun -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Perolehan</label>
                        <select x-model="tahunFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua Tahun Masuk</option>
                            <option value="2026">2026</option>
                            <option value="2025">2025</option>
                            <option value="2024">2024</option>
                            <option value="2021">2021</option>
                            <option value="2020">2020</option>
                            <option value="2018">2018</option>
                            <option value="1984">1984</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL KATALOG DATA ASTAP                                                  -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap">No</th>
                        <th class="px-4 py-3.5 text-left min-w-[240px]">Nama Barang / ASTAP</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tahun Masuk</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Volume / Kuantitas</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Nilai Realisasi</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Kondisi</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredAstaps" :key="item.id">
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <!-- Nomor Urut 1, 2, 3... -->
                            <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                            
                            <!-- Nama Barang / ASTAP -->
                            <td class="px-4 py-4">
                                <div class="font-bold text-white text-sm" x-text="item.nama_barang"></div>
                                <div class="text-[11px] font-mono text-cyan-400/90 font-medium mt-0.5" x-text="'Kode: ' + item.kode_barang"></div>
                                <div class="flex items-center space-x-1.5 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold whitespace-nowrap leading-none shrink-0"
                                        :class="{
                                            'bg-amber-500/20 text-amber-300 border border-amber-500/30': item.category === 'KIB A',
                                            'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': item.category === 'KIB B',
                                            'bg-purple-500/20 text-purple-300 border border-purple-500/30': item.category === 'KIB C',
                                            'bg-teal-500/20 text-teal-300 border border-teal-500/30': item.category === 'KIB D',
                                            'bg-orange-500/20 text-orange-300 border border-orange-500/30': item.category === 'KIB E',
                                            'bg-rose-500/20 text-rose-300 border border-rose-500/30': item.category === 'KIB F',
                                            'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30': item.category === 'ATB',
                                            'bg-amber-400/20 text-amber-300 border border-amber-400/30': item.category === 'EXTRACOM'
                                        }"
                                        x-text="item.category === 'ATB' ? '8. ATB' : (item.category === 'EXTRACOM' ? '9. Exstracom' : item.category)"></span>
                                    <span class="text-[11px] text-slate-400 truncate" x-text="item.jenis_aset_nama"></span>
                                </div>
                            </td>

                            <!-- Tahun Masuk / Perolehan -->
                            <td class="px-4 py-4 text-center font-mono font-bold text-slate-200 whitespace-nowrap" x-text="item.tahun_perolehan"></td>

                            <!-- Volume / Kuantitas / Luas Aset -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-slate-800 text-teal-300 font-semibold font-mono text-xs whitespace-nowrap">
                                    <span>📏</span>
                                    <span x-text="item.volume_satuan"></span>
                                </div>
                            </td>

                            <!-- Nilai Realisasi -->
                            <td class="px-4 py-4 text-center font-bold text-emerald-400 font-mono whitespace-nowrap" x-text="item.jumlah_realisasi"></td>

                            <!-- Kondisi — progress bar persentase dari registers -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <template x-data="{}" x-if="true">
                                    <div x-data="{ st: getKondisiStats(item) }">
                                        <!-- Jika hanya 1 unit / semua kondisi sama: tampilkan badge tunggal -->
                                        <template x-if="st.total <= 1 || (st.pct_baik === 100 || st.pct_rr === 100 || st.pct_rb === 100)">
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-[11px] font-bold border shadow-sm select-none"
                                                  :class="{
                                                      'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': st.kondisi_dominan === 'Baik',
                                                      'bg-amber-500/15 text-amber-300 border-amber-500/30': st.kondisi_dominan === 'Rusak Ringan',
                                                      'bg-rose-500/15 text-rose-300 border-rose-500/30': st.kondisi_dominan === 'Rusak Berat'
                                                  }">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                      :class="{
                                                          'bg-emerald-400': st.kondisi_dominan === 'Baik',
                                                          'bg-amber-400': st.kondisi_dominan === 'Rusak Ringan',
                                                          'bg-rose-400': st.kondisi_dominan === 'Rusak Berat'
                                                      }"></span>
                                                <span x-text="st.kondisi_dominan + (st.total > 1 ? ' 100%' : '')"></span>
                                            </span>
                                        </template>
                                        <!-- Jika multi kondisi: tampilkan progress bar breakdown -->
                                        <template x-if="st.total > 1 && !(st.pct_baik === 100 || st.pct_rr === 100 || st.pct_rb === 100)">
                                            <div class="min-w-[130px]">
                                                <!-- Mini progress bar gabungan -->
                                                <div class="flex h-2 rounded-full overflow-hidden bg-slate-800 mb-1.5">
                                                    <div x-show="st.pct_baik > 0" class="bg-emerald-400 transition-all" :style="'width:' + st.pct_baik + '%'"></div>
                                                    <div x-show="st.pct_rr > 0"   class="bg-amber-400 transition-all"   :style="'width:' + st.pct_rr + '%'"></div>
                                                    <div x-show="st.pct_rb > 0"   class="bg-rose-400 transition-all"    :style="'width:' + st.pct_rb + '%'"></div>
                                                </div>
                                                <!-- Label persentase per kondisi -->
                                                <div class="flex flex-wrap gap-x-2 gap-y-0.5 justify-center">
                                                    <template x-if="st.baik > 0">
                                                        <span class="text-[9.5px] font-bold text-emerald-400" x-text="st.pct_baik + '% Baik'"></span>
                                                    </template>
                                                    <template x-if="st.rusak_ringan > 0">
                                                        <span class="text-[9.5px] font-bold text-amber-400" x-text="st.pct_rr + '% R.Ringan'"></span>
                                                    </template>
                                                    <template x-if="st.rusak_berat > 0">
                                                        <span class="text-[9.5px] font-bold text-rose-400" x-text="st.pct_rb + '% R.Berat'"></span>
                                                    </template>
                                                </div>
                                                <!-- Jumlah unit keterangan -->
                                                <div class="text-[9px] text-slate-500 mt-0.5" x-text="'dari ' + st.total + ' unit'"></div>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                            </td>

                            <!-- Aksi (Rincian, Edit, Hapus) -->
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center space-x-2">
                                    <!-- Tombol Rincian / Detail -->
                                    <button type="button" @click="openDetail(item)"
                                        title="Lihat Detail & Rincian NIBAR"
                                        class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-emerald-500 hover:bg-emerald-500/20 text-slate-300 hover:text-emerald-300 font-bold text-xs transition-all shadow-sm hover:scale-105 active:scale-95 group cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span class="leading-none pt-0.5">Rincian</span>
                                    </button>
                                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                    <!-- Tombol Edit (Link ke halaman form edit lengkap) -->
                                    <a :href="'/astap/' + item.id + '/edit'"
                                        title="Edit Data ASTAP (Form Lengkap)"
                                        class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-cyan-500 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 font-bold text-xs transition-all shadow-sm hover:scale-105 active:scale-95 group cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-cyan-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span class="leading-none pt-0.5">Edit</span>
                                    </a>
                                    <!-- Tombol Hapus -->
                                    <button type="button" @click="deleteAstap(item)"
                                        title="Hapus Data ASTAP"
                                        class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-slate-950 border border-slate-800 hover:border-rose-500 hover:bg-rose-500/20 text-slate-300 hover:text-rose-400 font-bold text-xs transition-all shadow-sm hover:scale-105 active:scale-95 group cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-rose-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span class="leading-none pt-0.5">Hapus</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- FRONTEND MODAL: DETAIL ASTAP -->
        <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl overflow-y-auto max-h-[90vh]">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block" x-text="selectedAstapDetail ? selectedAstapDetail.category + ' • ' + selectedAstapDetail.kode_barang : ''"></span>
                        <h3 class="text-lg font-extrabold text-white" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : ''"></h3>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-500 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <template x-if="selectedAstapDetail">
                    <div class="space-y-4 text-xs text-slate-300">
                        <div class="grid grid-cols-2 gap-3 p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                            <div>
                                <span class="text-slate-500 text-[10px] block font-semibold">Jenis Aset PMDN 108</span>
                                <span class="text-white font-bold" x-text="selectedAstapDetail.jenis_aset_nama"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] block font-semibold">Tahun Perolehan</span>
                                <span class="text-white font-bold font-mono" x-text="selectedAstapDetail.tahun_perolehan"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] block font-semibold">Volume / Kuantitas</span>
                                <span class="text-teal-300 font-bold" x-text="selectedAstapDetail.volume_satuan"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] block font-semibold">Nilai Realisasi Belanja</span>
                                <span class="text-emerald-400 font-extrabold font-mono" x-text="selectedAstapDetail.jumlah_realisasi"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                            <div>
                                <span class="text-slate-500 text-[10px] block">Merk / Brand</span>
                                <span class="text-white font-semibold" x-text="selectedAstapDetail.merk || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] block">Type / Model</span>
                                <span class="text-white font-semibold" x-text="selectedAstapDetail.type || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] block">Bahan / Material</span>
                                <span class="text-white font-semibold" x-text="selectedAstapDetail.bahan || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] block">No. Pabrik / Seri</span>
                                <span class="text-white font-mono font-semibold" x-text="selectedAstapDetail.no_pabrik || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] block">Asal Usul Perolehan</span>
                                <span class="text-white font-semibold" x-text="selectedAstapDetail.asal_usul || '-'"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 text-[10px] block">Kondisi Aset</span>
                                <span class="text-emerald-300 font-bold" x-text="selectedAstapDetail.kondisi"></span>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                            <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">📄 Dokumen Pengadaan Sesuai Rekening & BAST:</span>
                            <div class="grid grid-cols-2 gap-2 text-[11px]">
                                <div>
                                    <span class="text-slate-500 block text-[9.5px]">Nomor SPK / Kontrak:</span>
                                    <span class="text-cyan-300 font-mono font-semibold" x-text="selectedAstapDetail.spk_nomor || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[9.5px]">Nomor Surat Pesanan / BAP:</span>
                                    <span class="text-purple-300 font-mono font-semibold" x-text="selectedAstapDetail.surat_pesanan_nomor || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[9.5px]">Nomor Kwitansi:</span>
                                    <span class="text-amber-300 font-mono font-semibold" x-text="selectedAstapDetail.kwitansi_nomor || '-'"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[9.5px]">Nomor Faktur / Invoice:</span>
                                    <span class="text-emerald-300 font-mono font-semibold" x-text="selectedAstapDetail.faktur_nomor || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- TABEL RINCIAN REGISTER NIBAR PER-UNIT -->
                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-800 pb-3">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider">🏷️ RINCIAN NIBAR &amp; PENEMPATAN RUANGAN (REGISTER):</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Filter kondisi aset dan lokasi penempatan ruangan di bawah ini.</p>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
                                    <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-lg bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-mono"
                                          x-text="filteredRegisters.length + ' / ' + (selectedAstapDetail.registers ? selectedAstapDetail.registers.length : 0) + ' Unit'"></span>
                                </div>
                            </div>

                            <!-- FILTER BAR INTERAKTIF RINCIAN MODAL -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 bg-slate-900/70 p-3 rounded-xl border border-slate-800">
                                <!-- Filter Status Penempatan -->
                                <div>
                                    <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Penempatan</label>
                                    <select x-model="detailPenempatanFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                        <option value="all">Semua Penempatan</option>
                                        <option value="sudah">📍 Sudah Ditempatkan (Ada Ruangan)</option>
                                        <option value="belum">⚠️ Belum Ditempatkan (Gudang)</option>
                                    </select>
                                </div>

                                <!-- Filter Kondisi -->
                                <div>
                                    <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Unit</label>
                                    <select x-model="detailKondisiFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                        <option value="all">Semua Kondisi</option>
                                        <option value="Baik">Baik (B)</option>
                                        <option value="Rusak Ringan">Rusak Ringan (RR)</option>
                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                    </select>
                                </div>

                                <!-- Cari NIBAR / Ruangan -->
                                <div>
                                    <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari NIBAR / Ruangan</label>
                                    <input type="text" x-model="detailSearchQuery" placeholder="Cari NIBAR / No Reg / Ruang..."
                                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                                </div>
                            </div>

                            <div class="overflow-x-auto rounded-xl border border-slate-800/80">
                                <table class="w-full text-left text-[11px] text-slate-300">
                                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px]">
                                        <tr>
                                            <th class="px-3 py-2.5">NIBAR &amp; No. Register Resmi</th>
                                            <th class="px-3 py-2.5">Status Penempatan Ruangan</th>
                                            <th class="px-3 py-2.5 text-center">Kondisi</th>
                                            <th class="px-3 py-2.5 text-center whitespace-nowrap">QR Code</th>
                                            <th class="px-3 py-2.5 text-center whitespace-nowrap">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                        <template x-for="reg in filteredRegisters" :key="reg.id">
                                            <tr class="hover:bg-slate-800/60 transition-colors">
                                                <td class="px-3 py-2.5 font-mono font-semibold text-emerald-400 whitespace-nowrap" x-text="reg.nibar || reg.no_register"></td>
                                                <td class="px-3 py-2.5">
                                                    <template x-if="reg.ruang_pemegang">
                                                        <span class="inline-flex items-center space-x-1.5 text-slate-200 font-medium">
                                                            <span class="text-teal-400 text-xs">📍</span>
                                                            <span x-text="reg.ruang_pemegang"></span>
                                                        </span>
                                                    </template>
                                                    <template x-if="!reg.ruang_pemegang">
                                                        <span class="inline-flex items-center space-x-1.5 text-amber-400 font-bold bg-amber-500/10 px-2.5 py-1 rounded-lg border border-amber-500/30 text-[10px]">
                                                            <span>⚠️</span>
                                                            <span>Belum Ditempatkan / Di Gudang Aset</span>
                                                        </span>
                                                    </template>
                                                </td>
                                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                                          :class="{
                                                              'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': reg.kondisi === 'Baik',
                                                              'bg-amber-500/20 text-amber-300 border border-amber-500/30': reg.kondisi === 'Rusak Ringan',
                                                              'bg-rose-500/20 text-rose-300 border border-rose-500/30': reg.kondisi === 'Rusak Berat'
                                                          }" x-text="reg.kondisi"></span>
                                                </td>
                                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                                    <button type="button" @click="downloadQrCodeNibar(reg, selectedAstapDetail)"
                                                        title="Pratinjau & Download QR NIBAR Unit Ini"
                                                        class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/25 border border-emerald-500/30 hover:border-emerald-400 text-emerald-400 hover:text-emerald-300 font-bold text-[10.5px] transition-all shadow-sm hover:scale-105 active:scale-95 group cursor-pointer leading-none">
                                                        <svg class="w-3 h-3 text-emerald-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                        <span class="leading-none pt-0.5">Download QR</span>
                                                    </button>
                                                </td>
                                                <td class="px-3 py-2.5 text-center whitespace-nowrap">
                                                    <div class="flex items-center justify-center space-x-1.5">
                                                        <button type="button" @click="editRegister(reg)" title="Edit Ruangan & Kondisi Unit Register Ini"
                                                                class="p-1.5 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 hover:text-amber-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 012.828 0L20.586 7a2 2 0 010 2.828l-8.586 8.586z"/></svg>
                                                        </button>
                                                        <button type="button" @click="deleteRegister(reg)" title="Hapus Unit Register Ini"
                                                                class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="filteredRegisters.length === 0">
                                            <tr>
                                                <td colspan="5" class="px-3 py-4 text-center text-slate-500 italic text-xs">
                                                    Tidak ditemukan rincian register NIBAR yang sesuai dengan filter pencarian.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                            <span class="text-slate-500 text-[10px] block font-semibold">Keterangan Catatan Aset:</span>
                            <p class="text-slate-200 text-xs italic" x-text="selectedAstapDetail.keterangan || '-'"></p>
                        </div>
                    </div>
                </template>

                <div class="pt-4 mt-4 border-t border-slate-800 flex justify-end">
                    <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>

        <!-- FRONTEND MODAL: PRATINJAU & DOWNLOAD QR CODE -->
        <div x-show="showQrModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4">
            <div @click.away="showQrModal = false" class="bg-slate-900 border border-emerald-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl text-center space-y-5">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">📱</span>
                        <h3 class="text-base font-extrabold text-white">Label QR Code Aset ASTAP</h3>
                    </div>
                    <button type="button" @click="showQrModal = false" class="text-slate-500 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <template x-if="selectedQrItem">
                    <div class="space-y-4">
                        <!-- Gambar QR Code yang Berisi URL Publik (Bisa Di-scan HP Tanpa Login) -->
                        <div class="p-4 bg-white rounded-2xl inline-block shadow-lg border-2 border-emerald-500/40">
                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=280x280&margin=10&data=' + encodeURIComponent(getQrPayloadUrl(selectedQrItem))"
                                 :alt="selectedQrItem.nama_barang"
                                 class="w-52 h-52 mx-auto object-contain" />
                        </div>

                        <!-- Kartu Informasi Detail Barang Sesuai QR Code -->
                        <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 text-left space-y-2 text-xs">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">📦 Nama Barang:</span>
                                <span class="text-white font-extrabold text-right max-w-[200px] truncate" x-text="selectedQrItem.nama_barang"></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">🏷️ NIBAR / Kode:</span>
                                <span class="text-emerald-400 font-bold font-mono text-right text-[11px]" x-text="selectedQrItem.kode_barang"></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">📅 Tahun Perolehan:</span>
                                <span class="text-slate-200 font-bold font-mono" x-text="selectedQrItem.tahun_perolehan"></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">📍 Penempatan Ruangan:</span>
                                <span class="text-teal-300 font-bold text-right max-w-[190px] truncate" x-text="selectedQrItem.ruang_pemegang"></span>
                            </div>
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                                <span class="text-slate-400 font-semibold text-[10.5px]">⚙️ Kondisi Aset:</span>
                                <span class="text-emerald-300 font-extrabold" x-text="selectedQrItem.kondisi"></span>
                            </div>
                            <div class="flex items-start justify-between pt-0.5">
                                <span class="text-slate-400 font-semibold text-[10.5px] shrink-0 mr-2">🛠️ Riwayat Perbaikan:</span>
                                <span class="text-amber-300 font-medium text-right text-[10.5px]" x-text="selectedQrItem.riwayat_servis"></span>
                            </div>
                        </div>

                        <!-- Box URL Publik Scan -->
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-left space-y-1.5">
                            <span class="text-[9.5px] font-bold text-slate-500 uppercase tracking-wider block">🔗 URL Publik Terenkripsi QR Code (Tanpa Login):</span>
                            <a :href="getQrPayloadUrl(selectedQrItem)" target="_blank"
                               class="font-mono text-[10.5px] text-cyan-400 hover:underline block truncate" x-text="getQrPayloadUrl(selectedQrItem)"></a>
                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 pt-1">
                            <button type="button" @click="downloadQrImage()"
                                class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center justify-center space-x-2 active:scale-95 cursor-pointer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                <span>Unduh Gambar QR (PNG)</span>
                            </button>
                            <a :href="getQrPayloadUrl(selectedQrItem)" target="_blank"
                               class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 font-bold text-xs transition-all flex items-center justify-center space-x-1.5">
                                <span>🌐 Buka Halaman Scan</span>
                            </a>
                            <button type="button" @click="showQrModal = false"
                                class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">
                                Tutup
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Modal Edit ASTAP dihapus: tombol Edit sudah mengarah langsung ke halaman form edit lengkap /astap/{id}/edit -->

    </div>
</x-layout>
