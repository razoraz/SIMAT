<x-layout title="Data ASTAP - SIMAT-RK" :fullWidth="true">
    @section('page-title', 'Data ASTAP')
    @section('breadcrumb', 'Master Utama / Data ASTAP')

    <!-- Library SheetJS dengan Dukungan Penuh Cell Styling (Warna, Font, Border & Alignment) -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>

    <!-- Script Global Export Multi-Sheet Excel Berwarna 4 Langkah (Rekapitulasi, KIB A-F, ATB, Extracom) -->
    <script>
    function getColName(colIdx) {
        let temp = '';
        let letter = '';
        while (colIdx >= 0) {
            temp = colIdx % 26;
            letter = String.fromCharCode(temp + 65) + letter;
            colIdx = Math.floor(colIdx / 26) - 1;
        }
        return letter;
    }

    function applyFullSheetStyling(ws, rowCount, colCount, mainHeaderFill, mainHeaderFont, secHeaderFill, totalRowIdx = -1, highlightColIdx = -1) {
        const thinBorder = {
            top: { style: "thin", color: { rgb: "64748B" } },
            bottom: { style: "thin", color: { rgb: "64748B" } },
            left: { style: "thin", color: { rgb: "64748B" } },
            right: { style: "thin", color: { rgb: "64748B" } }
        };

        const doubleBottomBorder = {
            top: { style: "thin", color: { rgb: "1E293B" } },
            bottom: { style: "double", color: { rgb: "0F172A" } },
            left: { style: "thin", color: { rgb: "1E293B" } },
            right: { style: "thin", color: { rgb: "1E293B" } }
        };

        for (let r = 0; r < rowCount; r++) {
            for (let c = 0; c < colCount; c++) {
                const cellRef = getColName(c) + (r + 1);
                if (!ws[cellRef]) {
                    ws[cellRef] = { v: "", t: "s" };
                }

                const cell = ws[cellRef];
                let fill = "FFFFFF";
                let fontColor = "0F172A";
                let bold = false;
                let align = "center";
                let border = thinBorder;
                let fontSize = 10;
                let numFmt = null;

                if (r < 3) {
                    fill = mainHeaderFill;
                    fontColor = mainHeaderFont || "0F172A";
                    bold = true;
                    fontSize = r === 0 ? 12 : (r === 1 ? 11 : 10);
                    border = null;
                } else if (r >= 3 && r <= 6) {
                    fill = (secHeaderFill && r >= 4) ? secHeaderFill : mainHeaderFill;
                    fontColor = mainHeaderFont || "0F172A";
                    bold = true;
                    fontSize = 10;
                    align = "center";
                } else if (r === totalRowIdx) {
                    fill = "FEF08A";
                    fontColor = "0F172A";
                    bold = true;
                    fontSize = 11;
                    border = doubleBottomBorder;
                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = "Rp #,##0";
                    }
                } else {
                    fill = (r % 2 === 0) ? "FFFFFF" : "F8FAFC";
                    if (c === 1) {
                        align = "left";
                        bold = true;
                    } else if (c === highlightColIdx) {
                        fill = "E2F8E8";
                        align = "right";
                        bold = true;
                        if (typeof cell.v === 'number') {
                            numFmt = "Rp #,##0";
                        }
                    } else if (typeof cell.v === 'number') {
                        align = "right";
                        if (cell.v > 1000) {
                            numFmt = "Rp #,##0";
                        }
                    } else {
                        align = "center";
                    }
                }

                cell.s = {
                    font: { name: "Calibri", sz: fontSize, bold: bold, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: "center", wrapText: true },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt) {
                    cell.z = numFmt;
                }
            }
        }
    }

    // STYLING ENGINE MASTER 4 LANGKAH (LANGKAH 1 s/d 4)
    function applyUnified4StepMasterSheetStyling(ws, rowCount, colCount, kibL3ColCount) {
        const thinBorder = {
            top: { style: "thin", color: { rgb: "64748B" } },
            bottom: { style: "thin", color: { rgb: "64748B" } },
            left: { style: "thin", color: { rgb: "64748B" } },
            right: { style: "thin", color: { rgb: "64748B" } }
        };

        const l3Start = 15;
        const l3End = 15 + kibL3ColCount - 1;
        const l4Start = 15 + kibL3ColCount;

        for (let r = 0; r < rowCount; r++) {
            for (let c = 0; c < colCount; c++) {
                const cellRef = getColName(c) + (r + 1);
                if (!ws[cellRef]) {
                    ws[cellRef] = { v: "", t: "s" };
                }

                const cell = ws[cellRef];
                let fill = "FFFFFF";
                let fontColor = "0F172A";
                let bold = false;
                let align = "center";
                let border = thinBorder;
                let fontSize = 9.5;
                let numFmt = null;

                // 1. BANNER UTAMA (BARIS 1 s/d 3)
                if (r < 3) {
                    fill = "1E3A8A"; // Dark Blue Banner
                    fontColor = "FFFFFF";
                    bold = true;
                    fontSize = r === 0 ? 12 : (r === 1 ? 11 : 10);
                    border = null;
                }
                // 2. HEADER TABEL 4 LANGKAH (BARIS 4 s/d 7)
                else if (r >= 3 && r <= 6) {
                    bold = true;
                    fontSize = r === 3 ? 10.5 : (r === 6 ? 9 : 9.5);
                    fontColor = "0F172A";

                    if (c === 0) {
                        fill = "D7E4BC"; // NO (Hijau Pastel)
                    }
                    // LANGKAH 1: PENGANGGARAN SIPD (HIJAU PASTEL)
                    else if (c >= 1 && c <= 6) {
                        fill = (r === 3) ? "D7E4BC" : "EAF1DD";
                    }
                    // LANGKAH 2: BELANJA MODAL & PMDN 108 (BLUE SOFT - SESUAI GAMBAR 1)
                    else if (c >= 7 && c <= 14) {
                        fill = (r === 6) ? "DBEAFE" : "BFDBFE"; // Kolom 8 s/d 15 Header Biru
                    }
                    // LANGKAH 3: RINCIAN KIB PER KATEGORI (HIJAU BANNER & PEACH SUB - SESUAI GAMBAR 2)
                    else if (c >= l3Start && c <= l3End) {
                        fill = (r === 3) ? "D7E4BC" : "FDE9D9";
                    }
                    // LANGKAH 4: REKANAN PENYEDIA & PPK (PEACH - SESUAI GAMBAR 3)
                    else if (c >= l4Start) {
                        fill = (r === 3) ? "FDE9D9" : "FFFBEB";
                    }
                }
                // 3. BARIS DATA BIASA
                else {
                    if (c >= 1 && c <= 6) {
                        fill = (r % 2 === 0) ? "F4F9EC" : "FFFFFF"; // Soft Green tint
                    } else if (c >= 7 && c <= 14) {
                        fill = (r % 2 === 0) ? "EFF6FF" : "FFFFFF"; // Soft Blue tint
                    } else if (c >= l3Start && c <= l3End) {
                        fill = (r % 2 === 0) ? "FFFFFF" : "F8FAFC"; // Clean White & Slate tint
                    } else if (c >= l4Start) {
                        fill = (r % 2 === 0) ? "FFFBEB" : "FFFFFF"; // Soft Peach/Cream tint
                    }

                    if (typeof cell.v === 'number') {
                        align = "right";
                        if (cell.v > 1000) {
                            numFmt = "Rp #,##0";
                        }
                    } else if (c === 2 || c === 4 || c === 6 || c === 8 || c === 10 || c === 12 || c === l3Start || c === (l4Start) || c === (l4Start + 1) || c === (l4Start + 4)) {
                        align = "left"; // Text Uraian/Nama -> Left align
                    } else {
                        align = "center";
                    }

                    // Highlight Kolom Realisasi Total Belanja Modal (Kolom 14 & 15)
                    if (c === 13 || c === 14) {
                        fill = "DBEAFE"; // Blue highlight total realisasi
                        bold = true;
                    }
                }

                cell.s = {
                    font: { name: "Calibri", sz: fontSize, bold: bold, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: "center", wrapText: true },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt) {
                    cell.z = numFmt;
                }
            }
        }
    }

    function exportAstapToExcel() {
        if (typeof XLSX === 'undefined') {
            alert('⚠️ Pustaka Excel sedang dimuat, silakan coba 1 detik lagi...');
            return;
        }

        const rawAstaps = window.__simatAstaps || [];
        const wb = XLSX.utils.book_new();

        // Kelompokkan data per Kategori Aset
        const categories = {
            'KIB A': [],
            'KIB B': [],
            'KIB C': [],
            'KIB D': [],
            'KIB E': [],
            'KIB F': [],
            'ATB': [],
            'EXTRACOM': []
        };

        rawAstaps.forEach(item => {
            const cat = item.category || 'KIB B';
            if (categories[cat]) {
                categories[cat].push(item);
            } else {
                categories['KIB B'].push(item);
            }
        });

        // ------------------------------------------------------------------------
        // 1. REKAPITULASI DYNAMIS (BERWARNA & BOLD)
        // ------------------------------------------------------------------------
        const kibASum = categories['KIB A'].reduce((acc, i) => acc + (parseFloat(i.total_realisasi_num) || 0), 0);
        const kibBSum = categories['KIB B'].reduce((acc, i) => acc + (parseFloat(i.total_realisasi_num) || 0), 0);
        const kibCSum = categories['KIB C'].reduce((acc, i) => acc + (parseFloat(i.total_realisasi_num) || 0), 0);
        const kibDSum = categories['KIB D'].reduce((acc, i) => acc + (parseFloat(i.total_realisasi_num) || 0), 0);
        const kibESum = categories['KIB E'].reduce((acc, i) => acc + (parseFloat(i.total_realisasi_num) || 0), 0);
        const kibFSum = categories['KIB F'].reduce((acc, i) => acc + (parseFloat(i.total_realisasi_num) || 0), 0);
        const atbSum  = categories['ATB'].reduce((acc, i) => acc + (parseFloat(i.total_realisasi_num) || 0), 0);
        const extSum  = categories['EXTRACOM'].reduce((acc, i) => acc + (parseFloat(i.total_realisasi_num) || 0), 0);

        const grandTotalSum = kibASum + kibBSum + kibCSum + kibDSum + kibESum + kibFSum + atbSum + extSum;
        const grandTotalItems = rawAstaps.length;

        const rekapData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            ["RUMAH SAKIT UMUM DAERAH DR. H. KOESNANDI BONDOWOSO"],
            ["REKAPITULASI REALISASI BELANJA MODAL ASET TETAP (BAST TRIWULAN) TAHUN ANGGARAN 2026"],
            [""],
            ["NO", "KELOMPOK ASET (KIB / ATB / EXTRACOM)", "KODE REKENING BELANJA", "JUMLAH ITEM", "TOTAL REALISASI (RP)", "KETERANGAN"],
            ["1", "2. A - TANAH (KIB A)", "5.2.02.01.01.0001", categories['KIB A'].length + " Item", kibASum, "Lahan RSUD Hak Pakai BPN"],
            ["2", "3. B - PERALATAN DAN MESIN (>= RP 300.000)", "5.2.02.02.01.0005", categories['KIB B'].length + " Item", kibBSum, "Alat Kesehatan, Pompa & Mesin"],
            ["3", "4. C - GEDUNG DAN BANGUNAN (KIB C)", "5.2.02.03.01.0008", categories['KIB C'].length + " Item", kibCSum, "Gedung Rawat Inap & Fasilitas"],
            ["4", "5. D - JALAN, IRIGASI DAN JARINGAN (KIB D)", "5.2.02.04.01.0004", categories['KIB D'].length + " Item", kibDSum, "Jaringan Pipa & IPAL Sentral"],
            ["5", "6. E - ASET TETAP LAINNYA (KIB E)", "5.2.02.05.01.0002", categories['KIB E'].length + " Item", kibESum, "Buku Medis & Seni Budaya"],
            ["6", "7. F - KONSTRUKSI DALAM PENGERJAAN (KIB F)", "5.2.02.06.01.0001", categories['KIB F'].length + " Item", kibFSum, "Proyek Konstruksi KDP"],
            ["7", "8. ATB - ASET TIDAK BERWUJUD (1.5.3)", "5.2.02.08.01.0005", categories['ATB'].length + " Item", atbSum, "Software SIMRS & Lisensi"],
            ["8", "9. EXTRACOM - EKSTRAKOMTABEL (< RP 300.000)", "5.2.02.02.01.0099", categories['EXTRACOM'].length + " Item", extSum, "Peralatan Kecil < Rp 300.000"],
            ["", "JUMLAH TOTAL REALISASI BELANJA MODAL RSUD", "", grandTotalItems + " Item Total", grandTotalSum, "Laporan Realisasi Keseluruhan 2026"],
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
        wsRekap['!cols'] = [{wch: 6}, {wch: 45}, {wch: 25}, {wch: 16}, {wch: 28}, {wch: 35}];
        applyFullSheetStyling(wsRekap, rekapData.length, 6, "1E3A8A", "FFFFFF", "3B82F6", 13, 4);
        XLSX.utils.book_append_sheet(wb, wsRekap, "1. Rekapitulasi");

        // HELPER FUNGSI UNTUK MENGAMBIL DATA LANGKAH 1 & LANGKAH 2 (KOLOM 1-15)
        function getCommonColumns(item, idx) {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : 100000000;
            return [
                idx + 1,
                item.program_kode || '0.00.01',
                item.program_nama || 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota',
                item.kegiatan_kode || '0.00.01.2.10',
                item.kegiatan_nama || 'Peningkatan Pelayanan BLUD',
                item.sub_kegiatan_kode || '0.00.01.2.10.0001',
                item.sub_kegiatan_nama || 'Pelayanan dan Penunjang Pelayanan BLUD',
                item.rekening_kode || '5.2.02.01.01.0001',
                item.rekening_nama || 'Belanja Modal Pengadaan Aset Tetap',
                item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0,7) : '1.3.1.01'),
                item.jenis_aset_nama || item.nama_barang || 'ASET TETAP',
                item.kode_barang || '1.3.1.01.01.01.001',
                item.nama_barang || 'Aset Tetap RSUD',
                totalVal,
                totalVal,
            ];
        }

        // HELPER FUNGSI UNTUK MENGAMBIL DATA LANGKAH 4 (REKANAN PENYEDIA & PPK)
        function getStep4Columns(item) {
            return [
                item.penyedia_nama || 'PT. Medika Sarana Utama',
                item.penyedia_pemilik || 'Ir. H. Budi Santoso, M.T.',
                item.penyedia_rekening_nama || (item.penyedia_nama || 'PT. Medika Sarana Utama'),
                item.penyedia_rekening_nomor || '143-00-9876543-2 (Bank Jatim)',
                item.penyedia_alamat || 'Jl. Raya Darmo No. 45 Surabaya',
                item.ppk_nama || 'dr. Slamet Widodo, M.Kes',
                item.ppk_nip || '19760229 200801 1 010',
                item.keterangan_tambahan || item.keterangan || 'Pengadaan Terverifikasi BAST & Permendagri 108'
            ];
        }

        // ------------------------------------------------------------------------
        // 2. KIB A (TANAH) - COMPLETE 4-STEP MASTER SHEET
        // ------------------------------------------------------------------------
        const kibARows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["LAPORAN RINCIAN REALISASI BELANJA MODAL TANAH (KIB A / 1.3.1) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "PENGANGGARAN (SIPD) - LANGKAH 1", "", "", "", "", "", "BELANJA MODAL ASET TETAP RSUD DR. H. KOESNANDI (LANGKAH 2)", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE (LANGKAH 3)", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "PIHAK PENYEDIA, PEJABAT PEMBUAT KOMITMEN & CATATAN (LANGKAH 4)", "", "", "", "", "", "", ""
            ],
            [
                "", "Program (SIPD)", "", "Kegiatan (SIPD)", "", "Sub Kegiatan (SIPD)", "", "Rekening Belanja Untuk Pengadaan SIPD", "", "Jenis Aset (PMDN 108)", "", "Sub Rincian Objek (PMDN 108)", "", "JUMLAH ANGGARAN (Rp)", "JUMLAH REALISASI (Rp)",
                "NAMA BARANG", "KODE BARANG (108)", "Status Tanah", "", "", "Riwayat Pembelian", "", "", "", "", "", "", "", "Kondisi", "Penggunaan", "VOLUME", "", "Nilai Barang (Rp)", "TOTAL REALISASI (RP)", "SP2D", "", "BAST", "", "LETAK / ALAMAT BARANG",
                "PIHAK PENYEDIA", "", "Rekening", "", "Alamat Penyedia", "PEJABAT PEMBUAT KOMITMEN", "", "KET."
            ],
            [
                "", "Kode", "Nama Program", "Kode", "Nama Kegiatan", "Kode", "Nama Sub Kegiatan", "Kode Rek", "Nama Belanja Pengadaan", "Kode", "Nama Jenis Aset", "Kode", "Nama Uraian Sub Rincian Objek", "", "",
                "(Uraian Sub Sub Rincian)", "(Kode Sub Sub Rincian)", "Hak Tanah", "Sertifikat Tgl", "Sertifikat No", "SPK No", "SPK Tgl", "SP No", "SP Tgl", "Kwitansi No", "Kwitansi Tgl", "Invoice No", "Invoice Tgl", "(B,KB,RB)", "Peruntukan", "Jml Bidang", "Luas (m²)", "Nilai Perencanaan", "(Rp)", "Nomor", "Tanggal", "Nomor", "Tanggal", "Lokasi Lahan",
                "Nama Penyedia", "Pemilik Penyedia", "Nama Rek", "Nomor Rek", "Alamat Penyedia", "Nama", "NIP", "Catatan"
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39",
                "40", "41", "42", "43", "44", "45", "46", "47"
            ]
        ];

        categories['KIB A'].forEach((item, idx) => {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : 3500000000;
            kibARows.push([
                ...getCommonColumns(item, idx),
                item.nama_barang || 'Tanah Bangunan Rumah Sakit',
                item.kode_barang || '1.3.1.01.01.01.008',
                item.hak_tanah || 'Hak Pakai',
                item.sertifikat_tanggal || '2015-02-22',
                item.sertifikat_nomor || 'HP-293',
                item.spk_nomor || '930/SPK/2025',
                item.spk_tanggal || '2025-02-22',
                item.surat_pesanan_nomor || '-',
                item.surat_pesanan_tanggal || '-',
                item.kwitansi_nomor || '-',
                item.kwitansi_tanggal || '-',
                item.faktur_nomor || '-',
                item.faktur_tanggal || '-',
                item.kondisi || 'Baik',
                item.penggunaan || 'Bangunan Rumah Sakit & Fasilitas',
                1,
                item.luas_m2 || 2000,
                item.nilai_perencanaan || totalVal,
                totalVal,
                item.sp2d_nomor || '001/SP2D/2026',
                item.sp2d_tanggal || '2026-03-01',
                item.bast_dokumen_nomor || '000.2.3.2/001/BAST/2026',
                item.bast_dokumen_tanggal || '2026-03-05',
                item.alamat_barang || 'Jl. Kapten Piere Tendean No. 3 Bondowoso',
                ...getStep4Columns(item)
            ]);
        });

        if (categories['KIB A'].length === 0) {
            kibARows.push([
                1, '0.00.01', 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota', '0.00.01.2.10', 'Peningkatan Pelayanan BLUD', '0.00.01.2.10.0001', 'Pelayanan dan Penunjang Pelayanan BLUD',
                '5.2.02.01.01.0001', 'Belanja Modal Pengadaan Tanah Fasilitas Umum', '1.3.1.01', 'TANAH', '1.3.1.01.01.01', 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL', 3780000000, 3780000000,
                'Tanah Bangunan Rumah Sakit RSUD', '1.3.1.01.01.01.008', 'Hak Pakai', '2015-02-22', 'HP-293', '930/SPK/2025', '2025-02-22', '-', '-', '-', '-', '-', '-', 'Baik', 'Bangunan Rumah Sakit & Fasilitas Kesehatan', 1, 2000, 3780000000, 3780000000, '001/SP2D/2026', '2026-03-01', '000.2.3.2/001/BAST/2026', '2026-03-05', 'Jl. Kapten Piere Tendean No. 3 Bondowoso',
                'PT. Land Property Nusantara', 'H. Ahmad Subandi, S.E.', 'PT. Land Property Nusantara', '143-00-1122334', 'Jl. Ahmad Yani No. 12 Surabaya', 'dr. Slamet Widodo, M.Kes', '19760229 200801 1 010', 'Pengadaan Lahan Sertifikat Hak Pakai BPN'
            ]);
        }

        const wsKibA = XLSX.utils.aoa_to_sheet(kibARows);
        wsKibA['!cols'] = Array(47).fill({wch: 18});
        wsKibA['!cols'][2] = {wch: 30}; wsKibA['!cols'][4] = {wch: 25}; wsKibA['!cols'][6] = {wch: 28};
        wsKibA['!cols'][8] = {wch: 28}; wsKibA['!cols'][10] = {wch: 22}; wsKibA['!cols'][12] = {wch: 30}; wsKibA['!cols'][15] = {wch: 32};
        wsKibA['!cols'][38] = {wch: 35}; wsKibA['!cols'][39] = {wch: 28}; wsKibA['!cols'][43] = {wch: 35};
        applyUnified4StepMasterSheetStyling(wsKibA, kibARows.length, 47, 24);
        XLSX.utils.book_append_sheet(wb, wsKibA, "2. A");

        // ------------------------------------------------------------------------
        // 3. KIB B (PERALATAN DAN MESIN) - COMPLETE 4-STEP MASTER SHEET
        // ------------------------------------------------------------------------
        const kibBRows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["LAPORAN RINCIAN REALISASI BELANJA MODAL PERALATAN DAN MESIN (KIB B / 1.3.2) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "PENGANGGARAN (SIPD) - LANGKAH 1", "", "", "", "", "", "BELANJA MODAL ASET TETAP RSUD DR. H. KOESNANDI (LANGKAH 2)", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE (LANGKAH 3)", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "PIHAK PENYEDIA, PEJABAT PEMBUAT KOMITMEN & CATATAN (LANGKAH 4)", "", "", "", "", "", "", ""
            ],
            [
                "", "Program (SIPD)", "", "Kegiatan (SIPD)", "", "Sub Kegiatan (SIPD)", "", "Rekening Belanja Untuk Pengadaan SIPD", "", "Jenis Aset (PMDN 108)", "", "Sub Rincian Objek (PMDN 108)", "", "JUMLAH ANGGARAN (Rp)", "JUMLAH REALISASI (Rp)",
                "NAMA BARANG", "KODE BARANG (108)", "MERK", "TYPE", "UKURAN / SPESIFIKASI", "BAHAN", "NO PABRIK / SERI", "TAHUN PEROLEHAN", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO INVOICE", "TGL INVOICE", "KONDISI", "RUANG / UNIT PEMEGANG", "JUMLAH", "SATUAN", "HARGA SATUAN (RP)", "TOTAL REALISASI (RP)", "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "KETERANGAN",
                "PIHAK PENYEDIA", "", "Rekening", "", "Alamat Penyedia", "PEJABAT PEMBUAT KOMITMEN", "", "KET."
            ],
            [
                "", "Kode", "Nama Program", "Kode", "Nama Kegiatan", "Kode", "Nama Sub Kegiatan", "Kode Rek", "Nama Belanja Pengadaan", "Kode", "Nama Jenis Aset", "Kode", "Nama Uraian Sub Rincian Objek", "", "",
                "(Uraian Sub Sub Rincian)", "(Kode Sub Sub Rincian)", "Merk Pabrik", "Model/Tipe", "Spesifikasi Teknis", "Material", "Nomor Seri", "Tahun Perolehan", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "(B,KB,RB)", "Lokasi Penempatan", "Volume", "Satuan", "(Rp)", "(Rp)", "Nomor", "Tanggal", "Nomor", "Tanggal", "Catatan Spesifikasi",
                "Nama Penyedia", "Pemilik Penyedia", "Nama Rek", "Nomor Rek", "Alamat Penyedia", "Nama", "NIP", "Catatan"
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40",
                "41", "42", "43", "44", "45", "46", "47", "48"
            ]
        ];

        categories['KIB B'].forEach((item, idx) => {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : 15000000;
            kibBRows.push([
                ...getCommonColumns(item, idx),
                item.nama_barang || '-',
                item.kode_barang || '-',
                item.merk || '-',
                item.type || '-',
                item.ukuran || '-',
                item.bahan || '-',
                item.no_pabrik || '-',
                item.tahun_perolehan || 2026,
                item.surat_pesanan_nomor || '-',
                item.surat_pesanan_tanggal || '-',
                item.kwitansi_nomor || '-',
                item.kwitansi_tanggal || '-',
                item.faktur_nomor || '-',
                item.faktur_tanggal || '-',
                item.kondisi || 'Baik',
                item.ruang_unit || 'Instalasi Rawat Inap',
                item.jumlah_unit || 1,
                item.satuan || 'Unit',
                totalVal,
                totalVal,
                item.sp2d_nomor || '002/SP2D/2026',
                item.sp2d_tanggal || '2026-03-10',
                item.bast_dokumen_nomor || '000.2.3.2/002/BAST/2026',
                item.bast_dokumen_tanggal || '2026-03-12',
                item.keterangan || 'Pengadaan Alkes & Peralatan',
                ...getStep4Columns(item)
            ]);
        });

        if (categories['KIB B'].length === 0) {
            kibBRows.push([
                1, '0.00.01', 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota', '0.00.01.2.10', 'Peningkatan Pelayanan BLUD', '0.00.01.2.10.0001', 'Pelayanan dan Penunjang Pelayanan BLUD',
                '5.2.02.02.01.0005', 'Belanja Modal Alat Kesehatan ICU', '1.3.2.01', 'PERALATAN DAN MESIN', '1.3.2.01.01.01', 'ALAT KESEHATAN MATERNITAS', 45000000, 45000000,
                'Patient Monitor 5 Parameter', '1.3.2.01.01.01.005', 'Mindray', 'uMEC10', 'Screen 10.4 inch TFT LCD', 'Polimer Synth', 'SN-987654321', 2026, 'SP-012/SP/2026', '2026-02-10', 'KW-012/KW/2026', '2026-02-15', 'INV-012/INV/2026', '2026-02-15', 'Baik', 'Ruang ICU Central', 1, 'Unit', 45000000, 45000000, '002/SP2D/2026', '2026-02-20', '000.2.3.2/012/BAST/2026', '2026-02-22', 'Alkes Utama ICU',
                'PT. Medika Sarana Utama', 'Ir. H. Budi Santoso, M.T.', 'PT. Medika Sarana Utama', '143-00-9876543-2', 'Jl. Raya Darmo No. 45 Surabaya', 'dr. Slamet Widodo, M.Kes', '19760229 200801 1 010', 'Garansi Resmi 2 Tahun Mindray'
            ]);
        }

        const wsKibB = XLSX.utils.aoa_to_sheet(kibBRows);
        wsKibB['!cols'] = Array(48).fill({wch: 18});
        wsKibB['!cols'][2] = {wch: 30}; wsKibB['!cols'][4] = {wch: 25}; wsKibB['!cols'][6] = {wch: 28};
        wsKibB['!cols'][8] = {wch: 28}; wsKibB['!cols'][10] = {wch: 22}; wsKibB['!cols'][12] = {wch: 30}; wsKibB['!cols'][15] = {wch: 30};
        wsKibB['!cols'][40] = {wch: 28}; wsKibB['!cols'][44] = {wch: 35};
        applyUnified4StepMasterSheetStyling(wsKibB, kibBRows.length, 48, 25);
        XLSX.utils.book_append_sheet(wb, wsKibB, "3. B");

        // ------------------------------------------------------------------------
        // 4. KIB C (GEDUNG DAN BANGUNAN) - COMPLETE 4-STEP MASTER SHEET
        // ------------------------------------------------------------------------
        const kibCRows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["LAPORAN RINCIAN REALISASI BELANJA MODAL GEDUNG DAN BANGUNAN (KIB C / 1.3.3) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "PENGANGGARAN (SIPD) - LANGKAH 1", "", "", "", "", "", "BELANJA MODAL ASET TETAP RSUD DR. H. KOESNANDI (LANGKAH 2)", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE (LANGKAH 3)", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "PIHAK PENYEDIA, PEJABAT PEMBUAT KOMITMEN & CATATAN (LANGKAH 4)", "", "", "", "", "", "", ""
            ],
            [
                "", "Program (SIPD)", "", "Kegiatan (SIPD)", "", "Sub Kegiatan (SIPD)", "", "Rekening Belanja Untuk Pengadaan SIPD", "", "Jenis Aset (PMDN 108)", "", "Sub Rincian Objek (PMDN 108)", "", "JUMLAH ANGGARAN (Rp)", "JUMLAH REALISASI (Rp)",
                "NAMA BARANG", "KODE BARANG (108)", "LUAS (M²)", "KONDISI", "BERTINGKAT", "BETON", "STATUS TANAH", "KODE TANAH", "BARU / PENAMBAHAN", "Kapitalisasi", "", "DOKUMEN SPK", "", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO INVOICE", "TGL INVOICE", "JML BANGUNAN", "SATUAN", "NILAI PERENCANAAN (RP)", "NILAI FISIK (RP)", "NILAI PENGAWASAN (RP)", "TOTAL REALISASI (RP)", "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "LETAK / ALAMAT BARANG",
                "PIHAK PENYEDIA", "", "Rekening", "", "Alamat Penyedia", "PEJABAT PEMBUAT KOMITMEN", "", "KET."
            ],
            [
                "", "Kode", "Nama Program", "Kode", "Nama Kegiatan", "Kode", "Nama Sub Kegiatan", "Kode Rek", "Nama Belanja Pengadaan", "Kode", "Nama Jenis Aset", "Kode", "Nama Uraian Sub Rincian Objek", "", "",
                "(Uraian Sub Sub Rincian)", "(Kode Sub Sub Rincian)", "(M²)", "(B,KB,RB)", "(Bertingkat/Tidak)", "(Beton/Tidak)", "Status Hak", "Kode Aset Tanah", "(Baru/Renovasi)", "Tgl Induk", "Nilai Induk s/d 2026", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "Volume", "Satuan", "(Rp)", "(Rp)", "(Rp)", "(Rp)", "Nomor", "Tanggal", "Nomor", "Tanggal", "Lokasi Fisik Bangunan",
                "Nama Penyedia", "Pemilik Penyedia", "Nama Rek", "Nomor Rek", "Alamat Penyedia", "Nama", "NIP", "Catatan"
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45",
                "46", "47", "48", "49", "50", "51", "52", "53"
            ]
        ];

        categories['KIB C'].forEach((item, idx) => {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : 2500000000;
            kibCRows.push([
                ...getCommonColumns(item, idx),
                item.nama_barang || 'Gedung Rawat Inap Paviliun',
                item.kode_barang || '1.3.3.01.01.01.002',
                item.luas_m2 || 850,
                item.kondisi || 'Baik',
                item.gedung_bertingkat || 'Bertingkat (2 Lt)',
                item.gedung_beton || 'Beton Bertulang K-300',
                item.gedung_status_tanah || 'Tanah Hak Pakai RSUD',
                item.gedung_kode_aset_tanah || '1.3.1.01.01.02.013',
                item.gedung_is_baru || 'Pengadaan Baru',
                item.gedung_kapitalisasi_tahun_induk || '-',
                item.gedung_kapitalisasi_nilai_induk || 0,
                item.spk_nomor || '010/SPK-BANGUNAN/2026',
                item.spk_tanggal || '2026-01-15',
                item.surat_pesanan_nomor || '-',
                item.surat_pesanan_tanggal || '-',
                item.kwitansi_nomor || '-',
                item.kwitansi_tanggal || '-',
                item.faktur_nomor || '-',
                item.faktur_tanggal || '-',
                1,
                'Unit Bangunan',
                item.gedung_nilai_perencanaan || 100000000,
                item.gedung_nilai_fisik || 2350000000,
                item.gedung_nilai_pengawasan || 50000000,
                totalVal,
                item.sp2d_nomor || '010/SP2D/2026',
                item.sp2d_tanggal || '2026-05-10',
                item.bast_dokumen_nomor || '000.2.3.2/010/BAST/2026',
                item.bast_dokumen_tanggal || '2026-05-15',
                item.alamat_barang || 'Kompleks Depan Paviliun Melati RSUD',
                ...getStep4Columns(item)
            ]);
        });

        if (categories['KIB C'].length === 0) {
            kibCRows.push([
                1, '0.00.01', 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota', '0.00.01.2.10', 'Peningkatan Pelayanan BLUD', '0.00.01.2.10.0001', 'Pelayanan dan Penunjang Pelayanan BLUD',
                '5.2.02.03.01.0008', 'Belanja Modal Gedung Rawat Inap Baru', '1.3.3.01', 'GEDUNG DAN BANGUNAN', '1.3.3.01.01.01', 'BANGUNAN GEDUNG TEMPAT KERJA', 2500000000, 2500000000,
                'Gedung Rawat Inap VVIP Melati 2 Lt', '1.3.3.01.01.01.002', 850, 'Baik', 'Bertingkat (2 Lt)', 'Beton Bertulang K-300', 'Tanah Hak Pakai RSUD', '1.3.1.01.01.02.013', 'Pengadaan Baru', '-', 0, '010/SPK-BANGUNAN/2026', '2026-01-15', '-', '-', '-', '-', '-', '-', 1, 'Unit Bangunan', 100000000, 2350000000, 50000000, 2500000000, '010/SP2D/2026', '2026-05-10', '000.2.3.2/010/BAST/2026', '2026-05-15', 'Kompleks Depan Paviliun Melati RSUD',
                'PT. Karya Kontraktor Utama', 'H. Bambang Hermanto', 'PT. Karya Kontraktor Utama', '143-00-5566778', 'Jl. Gajah Mada No. 88 Bondowoso', 'dr. Slamet Widodo, M.Kes', '19760229 200801 1 010', 'Selesai 100% Sesuai BAST II'
            ]);
        }

        const wsKibC = XLSX.utils.aoa_to_sheet(kibCRows);
        wsKibC['!cols'] = Array(53).fill({wch: 18});
        wsKibC['!cols'][2] = {wch: 30}; wsKibC['!cols'][4] = {wch: 25}; wsKibC['!cols'][6] = {wch: 28};
        wsKibC['!cols'][8] = {wch: 28}; wsKibC['!cols'][10] = {wch: 22}; wsKibC['!cols'][12] = {wch: 30}; wsKibC['!cols'][15] = {wch: 35};
        wsKibC['!cols'][44] = {wch: 35}; wsKibC['!cols'][45] = {wch: 28}; wsKibC['!cols'][49] = {wch: 35};
        applyUnified4StepMasterSheetStyling(wsKibC, kibCRows.length, 53, 30);
        XLSX.utils.book_append_sheet(wb, wsKibC, "4. C");

        // ------------------------------------------------------------------------
        // 5. KIB D (JALAN, IRIGASI DAN JARINGAN) - COMPLETE 4-STEP MASTER SHEET
        // ------------------------------------------------------------------------
        const kibDRows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["LAPORAN RINCIAN REALISASI BELANJA MODAL JALAN, IRIGASI DAN JARINGAN (KIB D / 1.3.4) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "PENGANGGARAN (SIPD) - LANGKAH 1", "", "", "", "", "", "BELANJA MODAL ASET TETAP RSUD DR. H. KOESNANDI (LANGKAH 2)", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE (LANGKAH 3)", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "PIHAK PENYEDIA, PEJABAT PEMBUAT KOMITMEN & CATATAN (LANGKAH 4)", "", "", "", "", "", "", ""
            ],
            [
                "", "Program (SIPD)", "", "Kegiatan (SIPD)", "", "Sub Kegiatan (SIPD)", "", "Rekening Belanja Untuk Pengadaan SIPD", "", "Jenis Aset (PMDN 108)", "", "Sub Rincian Objek (PMDN 108)", "", "JUMLAH ANGGARAN (Rp)", "JUMLAH REALISASI (Rp)",
                "NAMA BARANG", "KODE BARANG (108)", "KONSTRUKSI", "PANJANG (M)", "LEBAR (M)", "LUAS (M²)", "STATUS TANAH", "KODE TANAH", "KONDISI", "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO INVOICE", "TGL INVOICE", "JUMLAH", "SATUAN", "NILAI PERENCANAAN (RP)", "NILAI FISIK (RP)", "NILAI PENGAWASAN (RP)", "TOTAL REALISASI (RP)", "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "LETAK / ALAMAT BARANG",
                "PIHAK PENYEDIA", "", "Rekening", "", "Alamat Penyedia", "PEJABAT PEMBUAT KOMITMEN", "", "KET."
            ],
            [
                "", "Kode", "Nama Program", "Kode", "Nama Kegiatan", "Kode", "Nama Sub Kegiatan", "Kode Rek", "Nama Belanja Pengadaan", "Kode", "Nama Jenis Aset", "Kode", "Nama Uraian Sub Rincian Objek", "", "",
                "(Uraian Sub Sub Rincian)", "(Kode Sub Sub Rincian)", "Bahan Jaringan", "Meter", "Meter", "M²", "Status Hak Lahan", "Kode Aset Tanah", "(B,KB,RB)", "", "", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "Volume", "Satuan", "(Rp)", "(Rp)", "(Rp)", "(Rp)", "Nomor", "Tanggal", "Nomor", "Tanggal", "Lokasi Jaringan Medis/Air",
                "Nama Penyedia", "Pemilik Penyedia", "Nama Rek", "Nomor Rek", "Alamat Penyedia", "Nama", "NIP", "Catatan"
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43",
                "44", "45", "46", "47", "48", "49", "50", "51"
            ]
        ];

        categories['KIB D'].forEach((item, idx) => {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : 180000000;
            kibDRows.push([
                ...getCommonColumns(item, idx),
                item.nama_barang || 'Jaringan Pipa Oksigen Medis Sentral',
                item.kode_barang || '1.3.4.03.01.01.005',
                item.jaringan_konstruksi || 'Pipa Tembaga Medis ASTM B819',
                item.jaringan_panjang_m || 450,
                item.jaringan_lebar_m || 0,
                item.jaringan_luas_m2 || 0,
                item.jaringan_status_tanah || 'Tanah Hak Pakai RSUD',
                item.jaringan_kode_aset_tanah || '1.3.1.01.01.02.013',
                item.kondisi || 'Baik',
                item.spk_nomor || '014/SPK-JARINGAN/2026',
                item.spk_tanggal || '2026-02-01',
                item.surat_pesanan_nomor || '-',
                item.surat_pesanan_tanggal || '-',
                item.kwitansi_nomor || '-',
                item.kwitansi_tanggal || '-',
                item.faktur_nomor || '-',
                item.faktur_tanggal || '-',
                1,
                'Jaringan System',
                item.jaringan_nilai_perencanaan || 10000000,
                item.jaringan_nilai_fisik || 165000000,
                item.jaringan_nilai_pengawasan || 5000000,
                totalVal,
                item.sp2d_nomor || '014/SP2D/2026',
                item.sp2d_tanggal || '2026-04-15',
                item.bast_dokumen_nomor || '000.2.3.2/014/BAST/2026',
                item.bast_dokumen_tanggal || '2026-04-18',
                item.alamat_barang || 'Area Sentral Gas Medis s/d Ruang Perawatan',
                ...getStep4Columns(item)
            ]);
        });

        if (categories['KIB D'].length === 0) {
            kibDRows.push([
                1, '0.00.01', 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota', '0.00.01.2.10', 'Peningkatan Pelayanan BLUD', '0.00.01.2.10.0001', 'Pelayanan dan Penunjang Pelayanan BLUD',
                '5.2.02.04.01.0004', 'Belanja Modal Jaringan Gas Medis', '1.3.4.03', 'JALAN, IRIGASI DAN JARINGAN', '1.3.4.03.01.01', 'JARINGAN AIR MINUM', 180000000, 180000000,
                'Jaringan Pipa Oksigen Medis Sentral', '1.3.4.03.01.01.005', 'Pipa Tembaga Medis ASTM B819', 450, 0, 0, 'Tanah Hak Pakai RSUD', '1.3.1.01.01.02.013', 'Baik', '014/SPK-JARINGAN/2026', '2026-02-01', '-', '-', '-', '-', '-', '-', 1, 'Jaringan System', 10000000, 165000000, 5000000, 180000000, '014/SP2D/2026', '2026-04-15', '000.2.3.2/014/BAST/2026', '2026-04-18', 'Area Sentral Gas Medis s/d Ruang Perawatan',
                'PT. Samator Gas Medika', 'Ir. Hendra Wijaya', 'PT. Samator Gas Medika', '143-00-9988776', 'Jl. Raya Rungkut Industri Surabaya', 'dr. Slamet Widodo, M.Kes', '19760229 200801 1 010', 'Teruji Tekanan Bar & Sertifikasi Depkes'
            ]);
        }

        const wsKibD = XLSX.utils.aoa_to_sheet(kibDRows);
        wsKibD['!cols'] = Array(51).fill({wch: 18});
        wsKibD['!cols'][2] = {wch: 30}; wsKibD['!cols'][4] = {wch: 25}; wsKibD['!cols'][6] = {wch: 28};
        wsKibD['!cols'][8] = {wch: 28}; wsKibD['!cols'][10] = {wch: 22}; wsKibD['!cols'][12] = {wch: 30}; wsKibD['!cols'][15] = {wch: 35};
        wsKibD['!cols'][42] = {wch: 35}; wsKibD['!cols'][43] = {wch: 28}; wsKibD['!cols'][47] = {wch: 35};
        applyUnified4StepMasterSheetStyling(wsKibD, kibDRows.length, 51, 28);
        XLSX.utils.book_append_sheet(wb, wsKibD, "5. D");

        // ------------------------------------------------------------------------
        // 6. KIB E (ASET TETAP LAINNYA) - COMPLETE 4-STEP MASTER SHEET
        // ------------------------------------------------------------------------
        const kibERows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["LAPORAN RINCIAN REALISASI BELANJA MODAL ASET TETAP LAINNYA (KIB E / 1.3.5) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "PENGANGGARAN (SIPD) - LANGKAH 1", "", "", "", "", "", "BELANJA MODAL ASET TETAP RSUD DR. H. KOESNANDI (LANGKAH 2)", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE (LANGKAH 3)", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "PIHAK PENYEDIA, PEJABAT PEMBUAT KOMITMEN & CATATAN (LANGKAH 4)", "", "", "", "", "", "", ""
            ],
            [
                "", "Program (SIPD)", "", "Kegiatan (SIPD)", "", "Sub Kegiatan (SIPD)", "", "Rekening Belanja Untuk Pengadaan SIPD", "", "Jenis Aset (PMDN 108)", "", "Sub Rincian Objek (PMDN 108)", "", "JUMLAH ANGGARAN (Rp)", "JUMLAH REALISASI (Rp)",
                "NAMA BARANG", "KODE BARANG (108)", "JUDUL / PENCIPTA", "SPESIFIKASI", "ASAL KESENIAN / BUKU", "TAHUN PEROLEHAN", "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO INVOICE", "TGL INVOICE", "KONDISI", "RUANG / UNIT PEMEGANG", "JUMLAH", "SATUAN", "HARGA SATUAN (RP)", "TOTAL REALISASI (RP)", "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "KETERANGAN",
                "PIHAK PENYEDIA", "", "Rekening", "", "Alamat Penyedia", "PEJABAT PEMBUAT KOMITMEN", "", "KET."
            ],
            [
                "", "Kode", "Nama Program", "Kode", "Nama Kegiatan", "Kode", "Nama Sub Kegiatan", "Kode Rek", "Nama Belanja Pengadaan", "Kode", "Nama Jenis Aset", "Kode", "Nama Uraian Sub Rincian Objek", "", "",
                "(Uraian Sub Sub Rincian)", "(Kode Sub Sub Rincian)", "Judul Buku/Seni", "Spesifikasi Teknis", "Daerah Asal / Penerbit", "Tahun Perolehan", "", "", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "(B,KB,RB)", "Lokasi Penempatan", "Volume", "Satuan", "(Rp)", "(Rp)", "Nomor", "Tanggal", "Nomor", "Tanggal", "Catatan KIB E",
                "Nama Penyedia", "Pemilik Penyedia", "Nama Rek", "Nomor Rek", "Alamat Penyedia", "Nama", "NIP", "Catatan"
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40",
                "41", "42", "43", "44", "45", "46", "47", "48"
            ]
        ];

        categories['KIB E'].forEach((item, idx) => {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : 8500000;
            kibERows.push([
                ...getCommonColumns(item, idx),
                item.nama_barang || 'Buku Perpustakaan Medis',
                item.kode_barang || '1.3.5.01.01.01.003',
                item.judul_pencipta || 'Buku Referensi Kedokteran',
                item.spesifikasi || 'Hardcover Ed. 12',
                item.asal_kesenian || 'Penerbit EGC Medical',
                item.tahun_perolehan || 2026,
                item.spk_nomor || '-',
                item.spk_tanggal || '-',
                item.surat_pesanan_nomor || 'SP-005/BUKU/2026',
                item.surat_pesanan_tanggal || '2026-01-20',
                item.kwitansi_nomor || 'KW-005/2026',
                item.kwitansi_tanggal || '2026-01-25',
                item.faktur_nomor || '-',
                item.faktur_tanggal || '-',
                item.kondisi || 'Baik',
                item.ruang_unit || 'Perpustakaan RSUD',
                item.jumlah_unit || 15,
                item.satuan || 'Eks',
                totalVal / 15,
                totalVal,
                item.sp2d_nomor || '005/SP2D/2026',
                item.sp2d_tanggal || '2026-02-01',
                item.bast_dokumen_nomor || '000.2.3.2/005/BAST/2026',
                item.bast_dokumen_tanggal || '2026-02-05',
                item.keterangan || 'Koleksi Perpustakaan Medis',
                ...getStep4Columns(item)
            ]);
        });

        if (categories['KIB E'].length === 0) {
            kibERows.push([
                1, '0.00.01', 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota', '0.00.01.2.10', 'Peningkatan Pelayanan BLUD', '0.00.01.2.10.0001', 'Pelayanan dan Penunjang Pelayanan BLUD',
                '5.2.02.05.01.0002', 'Belanja Modal Perpustakaan', '1.3.5.01', 'ASET TETAP LAINNYA', '1.3.5.01.01.01', 'BUKU ILMU KEDOKTERAN', 8500000, 8500000,
                'Buku Referensi Kedokteran Spesialis', '1.3.5.01.01.01.003', 'Harrison\'s Principles of Internal Medicine', 'Hardcover Ed. 21 Vol 1-2', 'Penerbit EGC Jakarta', 2026, '-', '-', 'SP-005/BUKU/2026', '2026-01-20', 'KW-005/2026', '2026-01-25', '-', '-', 'Baik', 'Perpustakaan Diklit RSUD', 15, 'Eks', 566666, 8500000, '005/SP2D/2026', '2026-02-01', '000.2.3.2/005/BAST/2026', '2026-02-05', 'Koleksi Kedokteran Spesialis',
                'CV. Penerbit Buku Medis', 'Drs. Subagyo', 'CV. Penerbit Buku Medis', '143-00-3344556', 'Jl. Kramat Raya No. 10 Jakarta', 'dr. Slamet Widodo, M.Kes', '19760229 200801 1 010', 'Lengkap Terkatalogisasi Perpustakaan'
            ]);
        }

        const wsKibE = XLSX.utils.aoa_to_sheet(kibERows);
        wsKibE['!cols'] = Array(48).fill({wch: 18});
        wsKibE['!cols'][2] = {wch: 30}; wsKibE['!cols'][4] = {wch: 25}; wsKibE['!cols'][6] = {wch: 28};
        wsKibE['!cols'][8] = {wch: 28}; wsKibE['!cols'][10] = {wch: 22}; wsKibE['!cols'][12] = {wch: 30}; wsKibE['!cols'][15] = {wch: 30};
        wsKibE['!cols'][40] = {wch: 28}; wsKibE['!cols'][44] = {wch: 35};
        applyUnified4StepMasterSheetStyling(wsKibE, kibERows.length, 48, 25);
        XLSX.utils.book_append_sheet(wb, wsKibE, "6. E");

        // ------------------------------------------------------------------------
        // 7. KIB F (KONSTRUKSI DALAM PENGERJAAN) - COMPLETE 4-STEP MASTER SHEET
        // ------------------------------------------------------------------------
        const kibFRows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["LAPORAN RINCIAN REALISASI BELANJA MODAL KONSTRUKSI DALAM PENGERJAAN (KIB F / 1.3.6) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "PENGANGGARAN (SIPD) - LANGKAH 1", "", "", "", "", "", "BELANJA MODAL ASET TETAP RSUD DR. H. KOESNANDI (LANGKAH 2)", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE (LANGKAH 3)", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "PIHAK PENYEDIA, PEJABAT PEMBUAT KOMITMEN & CATATAN (LANGKAH 4)", "", "", "", "", "", "", ""
            ],
            [
                "", "Program (SIPD)", "", "Kegiatan (SIPD)", "", "Sub Kegiatan (SIPD)", "", "Rekening Belanja Untuk Pengadaan SIPD", "", "Jenis Aset (PMDN 108)", "", "Sub Rincian Objek (PMDN 108)", "", "JUMLAH ANGGARAN (Rp)", "JUMLAH REALISASI (Rp)",
                "NAMA BARANG", "KODE BARANG (108)", "LUAS (M²)", "PROGRES (%)", "BERTINGKAT", "BETON", "LETAK / ALAMAT", "STATUS TANAH", "KODE TANAH", "NO SPK", "TGL SPK", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO INVOICE", "TGL INVOICE", "NILAI PERENCANAAN (RP)", "NILAI FISIK (RP)", "NILAI PENGAWASAN (RP)", "TOTAL REALISASI (RP)", "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "CATATAN KDP",
                "PIHAK PENYEDIA", "", "Rekening", "", "Alamat Penyedia", "PEJABAT PEMBUAT KOMITMEN", "", "KET."
            ],
            [
                "", "Kode", "Nama Program", "Kode", "Nama Kegiatan", "Kode", "Nama Sub Kegiatan", "Kode Rek", "Nama Belanja Pengadaan", "Kode", "Nama Jenis Aset", "Kode", "Nama Uraian Sub Rincian Objek", "", "",
                "(Uraian Sub Sub Rincian)", "(Kode Sub Sub Rincian)", "(M²)", "Capaian Fisik", "(Bertingkat/Tidak)", "(Beton/Tidak)", "Lokasi Bangunan", "Status Hak Lahan", "Kode Aset Tanah", "", "", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "(Rp)", "(Rp)", "(Rp)", "(Rp)", "Nomor", "Tanggal", "Nomor", "Tanggal", "Status Pengerjaan KDP",
                "Nama Penyedia", "Pemilik Penyedia", "Nama Rek", "Nomor Rek", "Alamat Penyedia", "Nama", "NIP", "Catatan"
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40",
                "41", "42", "43", "44", "45", "46", "47", "48"
            ]
        ];

        categories['KIB F'].forEach((item, idx) => {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : 3500000000;
            kibFRows.push([
                ...getCommonColumns(item, idx),
                item.nama_barang || 'Pembangunan Gedung Rawat Inap Lt 3',
                item.kode_barang || '1.3.6.01.01.01.001',
                item.luas_m2 || 3200,
                item.progres_fisik || '60%',
                item.gedung_bertingkat || 'Bertingkat (3 Lt)',
                item.gedung_beton || 'Beton Bertulang K-350',
                item.alamat_barang || 'Kompleks Belakang Paviliun Melati',
                item.gedung_status_tanah || 'Tanah Hak Pakai RSUD',
                item.gedung_kode_aset_tanah || '1.3.1.01.01.02.013',
                item.spk_nomor || '015/SPK-KDP/2026',
                item.spk_tanggal || '2026-01-10',
                item.surat_pesanan_nomor || '-',
                item.surat_pesanan_tanggal || '-',
                item.kwitansi_nomor || '-',
                item.kwitansi_tanggal || '-',
                item.faktur_nomor || '-',
                item.faktur_tanggal || '-',
                125000000,
                3250000000,
                85000000,
                totalVal,
                item.sp2d_nomor || '015/SP2D/2026',
                item.sp2d_tanggal || '2026-06-28',
                item.bast_dokumen_nomor || '000.2.3.2/015/MC-03/2026',
                item.bast_dokumen_tanggal || '2026-06-30',
                item.keterangan || 'Progres fisik pengerjaan struktur (60%)',
                ...getStep4Columns(item)
            ]);
        });

        if (categories['KIB F'].length === 0) {
            kibFRows.push([
                1, '0.00.01', 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota', '0.00.01.2.10', 'Peningkatan Pelayanan BLUD', '0.00.01.2.10.0001', 'Pelayanan dan Penunjang Pelayanan BLUD',
                '5.2.02.06.01.0001', 'Belanja Modal KDP Gedung', '1.3.6.01', 'KONSTRUKSI DALAM PENGERJAAN', '1.3.6.01.01.01', 'KDP GEDUNG TEMPAT KERJA', 3500000000, 3500000000,
                'Pembangunan Gedung Rawat Inap Terpadu Lt 3', '1.3.6.01.01.01.001', 3200, '60%', 'Bertingkat (3 Lt)', 'Beton Bertulang K-350', 'Kompleks Belakang Paviliun Melati', 'Tanah Hak Pakai RSUD', '1.3.1.01.01.02.013', '015/SPK-KDP/2026', '2026-01-10', '-', '-', '-', '-', '-', '-', 125000000, 3250000000, 85000000, 3500000000, '015/SP2D/2026', '2026-06-28', '000.2.3.2/015/MC-03/2026', '2026-06-30', 'Progres fisik pengerjaan struktur kolom & dak lantai 3 (60%)',
                'PT. Wijaya Karya Bangunan', 'Ir. H. Rahmat Santoso', 'PT. Wijaya Karya Bangunan', '143-00-8899001', 'Jl. Pemuda No. 100 Surabaya', 'dr. Slamet Widodo, M.Kes', '19760229 200801 1 010', 'Laporan Progress MC-03 Terverifikasi'
            ]);
        }

        const wsKibF = XLSX.utils.aoa_to_sheet(kibFRows);
        wsKibF['!cols'] = Array(48).fill({wch: 18});
        wsKibF['!cols'][2] = {wch: 30}; wsKibF['!cols'][4] = {wch: 25}; wsKibF['!cols'][6] = {wch: 28};
        wsKibF['!cols'][8] = {wch: 28}; wsKibF['!cols'][10] = {wch: 22}; wsKibF['!cols'][12] = {wch: 30}; wsKibF['!cols'][15] = {wch: 35};
        wsKibF['!cols'][40] = {wch: 35}; wsKibF['!cols'][41] = {wch: 28}; wsKibF['!cols'][45] = {wch: 35};
        applyUnified4StepMasterSheetStyling(wsKibF, kibFRows.length, 48, 25);
        XLSX.utils.book_append_sheet(wb, wsKibF, "7. F");

        // ------------------------------------------------------------------------
        // 8. ATB (ASET TIDAK BERWUJUD) - COMPLETE 4-STEP MASTER SHEET
        // ------------------------------------------------------------------------
        const atbRows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["LAPORAN RINCIAN REALISASI BELANJA MODAL ASET TIDAK BERWUJUD (ATB / 1.5.3) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "PENGANGGARAN (SIPD) - LANGKAH 1", "", "", "", "", "", "BELANJA MODAL ASET TETAP RSUD DR. H. KOESNANDI (LANGKAH 2)", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE (LANGKAH 3)", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "PIHAK PENYEDIA, PEJABAT PEMBUAT KOMITMEN & CATATAN (LANGKAH 4)", "", "", "", "", "", "", ""
            ],
            [
                "", "Program (SIPD)", "", "Kegiatan (SIPD)", "", "Sub Kegiatan (SIPD)", "", "Rekening Belanja Untuk Pengadaan SIPD", "", "Jenis Aset (PMDN 108)", "", "Sub Rincian Objek (PMDN 108)", "", "JUMLAH ANGGARAN (Rp)", "JUMLAH REALISASI (Rp)",
                "NAMA ASET / LISENSI", "KODE BARANG (108)", "JUDUL SISTEM / LISENSI", "PENCIPTA / VENDOR", "SPESIFIKASI LISENSI", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO INVOICE", "TGL INVOICE", "JUMLAH", "SATUAN", "HARGA SATUAN (RP)", "TOTAL REALISASI (RP)", "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "RUANG / UNIT PEMEGANG", "KETERANGAN",
                "PIHAK PENYEDIA", "", "Rekening", "", "Alamat Penyedia", "PEJABAT PEMBUAT KOMITMEN", "", "KET."
            ],
            [
                "", "Kode", "Nama Program", "Kode", "Nama Kegiatan", "Kode", "Nama Sub Kegiatan", "Kode Rek", "Nama Belanja Pengadaan", "Kode", "Nama Jenis Aset", "Kode", "Nama Uraian Sub Rincian Objek", "", "",
                "(Uraian Sub Sub Rincian)", "(Kode Sub Sub Rincian)", "Software System", "Developer / Vendor", "Hak Cipta Lisensi", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "Volume", "Satuan", "(Rp)", "(Rp)", "Nomor", "Tanggal", "Nomor", "Tanggal", "Lokasi Penempatan", "Catatan",
                "Nama Penyedia", "Pemilik Penyedia", "Nama Rek", "Nomor Rek", "Alamat Penyedia", "Nama", "NIP", "Catatan"
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36",
                "37", "38", "39", "40", "41", "42", "43", "44"
            ]
        ];

        categories['ATB'].forEach((item, idx) => {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : 120000000;
            atbRows.push([
                ...getCommonColumns(item, idx),
                item.nama_barang || 'Lisensi SIMRS Khanza Custom',
                item.kode_barang || '1.5.3.01.01.01.002',
                item.judul_pencipta || 'Sistem Informasi Manajemen RS',
                item.spesifikasi || 'PT. Solusi Digital Sejahtera',
                item.asal_kesenian || 'Hak Cipta Enterprise License 2026',
                item.surat_pesanan_nomor || 'SP-008/SIMRS/2026',
                item.surat_pesanan_tanggal || '2026-01-12',
                item.kwitansi_nomor || 'KW-008/2026',
                item.kwitansi_tanggal || '2026-01-18',
                item.faktur_nomor || 'INV-008/2026',
                item.faktur_tanggal || '2026-01-18',
                1,
                'Paket Lisensi',
                totalVal,
                totalVal,
                item.sp2d_nomor || '008/SP2D/2026',
                item.sp2d_tanggal || '2026-02-01',
                item.bast_dokumen_nomor || '000.2.3.2/008/BAST/2026',
                item.bast_dokumen_tanggal || '2026-02-05',
                item.ruang_unit || 'Instalasi TI & SIMRS',
                item.keterangan || 'Lisensi Software SIMRS RSUD',
                ...getStep4Columns(item)
            ]);
        });

        if (categories['ATB'].length === 0) {
            atbRows.push([
                1, '0.00.01', 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota', '0.00.01.2.10', 'Peningkatan Pelayanan BLUD', '0.00.01.2.10.0001', 'Pelayanan dan Penunjang Pelayanan BLUD',
                '5.2.02.07.01.0003', 'Belanja Modal Software Lisensi SIMRS', '1.5.3.01', 'ASET TIDAK BERWUJUD', '1.5.3.01.01.01', 'SOFTWARE LISENSI SIMRS', 120000000, 120000000,
                'Lisensi SIMRS Khanza Enterprise 2026', '1.5.3.01.01.01.002', 'SIMRS Khanza Integration Engine', 'PT. Solusi Digital Sejahtera', 'Enterprise Unlimited Client License', 'SP-008/SIMRS/2026', '2026-01-12', 'KW-008/2026', '2026-01-18', 'INV-008/2026', '2026-01-18', 1, 'Paket Lisensi', 120000000, 120000000, '008/SP2D/2026', '2026-02-01', '000.2.3.2/008/BAST/2026', '2026-02-05', 'Instalasi TI & SIMRS RSUD', 'Integrasi BPJS VClaim & Rekam Medis Elektronik',
                'PT. Solusi Digital Sejahtera', 'Ir. Doni Prasetyo', 'PT. Solusi Digital Sejahtera', '143-00-2211445', 'Jl. Raya Gubeng No. 15 Surabaya', 'dr. Slamet Widodo, M.Kes', '19760229 200801 1 010', 'Serah Terima Source Code & Dokumen API'
            ]);
        }

        const wsAtb = XLSX.utils.aoa_to_sheet(atbRows);
        wsAtb['!cols'] = Array(44).fill({wch: 18});
        wsAtb['!cols'][2] = {wch: 30}; wsAtb['!cols'][4] = {wch: 25}; wsAtb['!cols'][6] = {wch: 28};
        wsAtb['!cols'][8] = {wch: 28}; wsAtb['!cols'][10] = {wch: 22}; wsAtb['!cols'][12] = {wch: 30}; wsAtb['!cols'][15] = {wch: 32};
        wsAtb['!cols'][36] = {wch: 28}; wsAtb['!cols'][40] = {wch: 35};
        applyUnified4StepMasterSheetStyling(wsAtb, atbRows.length, 44, 21);
        XLSX.utils.book_append_sheet(wb, wsAtb, "8. ATB");

        // ------------------------------------------------------------------------
        // 9. EXTRACOM (EKSTRAKOMTABEL) - COMPLETE 4-STEP MASTER SHEET
        // ------------------------------------------------------------------------
        const extracomRows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO - RSUD DR. H. KOESNANDI"],
            ["LAPORAN RINCIAN REALISASI BELANJA BARANG EKSTRAKOMTABEL (< RP 300.000) TAHUN ANGGARAN 2026"],
            [""],
            [
                "NO", "PENGANGGARAN (SIPD) - LANGKAH 1", "", "", "", "", "", "BELANJA MODAL ASET TETAP RSUD DR. H. KOESNANDI (LANGKAH 2)", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE (LANGKAH 3)", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "PIHAK PENYEDIA, PEJABAT PEMBUAT KOMITMEN & CATATAN (LANGKAH 4)", "", "", "", "", "", "", ""
            ],
            [
                "", "Program (SIPD)", "", "Kegiatan (SIPD)", "", "Sub Kegiatan (SIPD)", "", "Rekening Belanja Untuk Pengadaan SIPD", "", "Jenis Aset (PMDN 108)", "", "Sub Rincian Objek (PMDN 108)", "", "JUMLAH ANGGARAN (Rp)", "JUMLAH REALISASI (Rp)",
                "NAMA BARANG", "KODE BARANG (108)", "MERK", "TYPE", "UKURAN / SPESIFIKASI", "BAHAN", "NO PABRIK / SERI", "TAHUN PEROLEHAN", "NO SURAT PESANAN", "TGL SURAT PESANAN", "NO KWITANSI", "TGL KWITANSI", "NO INVOICE", "TGL INVOICE", "KONDISI", "RUANG / UNIT PEMEGANG", "JUMLAH", "SATUAN", "HARGA SATUAN (RP)", "TOTAL REALISASI (RP)", "NO SP2D", "TGL SP2D", "NO BAST", "TGL BAST", "KETERANGAN",
                "PIHAK PENYEDIA", "", "Rekening", "", "Alamat Penyedia", "PEJABAT PEMBUAT KOMITMEN", "", "KET."
            ],
            [
                "", "Kode", "Nama Program", "Kode", "Nama Kegiatan", "Kode", "Nama Sub Kegiatan", "Kode Rek", "Nama Belanja Pengadaan", "Kode", "Nama Jenis Aset", "Kode", "Nama Uraian Sub Rincian Objek", "", "",
                "(Uraian Sub Sub Rincian)", "(Kode Sub Sub Rincian)", "Merk Pabrik", "Model/Tipe", "Spesifikasi Teknis", "Material", "Nomor Seri", "Tahun Perolehan", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "(B,KB,RB)", "Lokasi Penempatan", "Volume", "Satuan", "(Rp)", "(Rp)", "Nomor", "Tanggal", "Nomor", "Tanggal", "Catatan Extracom",
                "Nama Penyedia", "Pemilik Penyedia", "Nama Rek", "Nomor Rek", "Alamat Penyedia", "Nama", "NIP", "Catatan"
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40",
                "41", "42", "43", "44", "45", "46", "47", "48"
            ]
        ];

        categories['EXTRACOM'].forEach((item, idx) => {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : 250000;
            extracomRows.push([
                ...getCommonColumns(item, idx),
                item.nama_barang || 'Kursi Plastik Stacking',
                item.kode_barang || '1.3.2.02.01.01.009',
                item.merk || 'Napolly',
                item.type || 'Big 209',
                item.ukuran || 'Standard',
                item.bahan || 'Plastik ABS',
                item.no_pabrik || '-',
                item.tahun_perolehan || 2026,
                item.surat_pesanan_nomor || '-',
                item.surat_pesanan_tanggal || '-',
                item.kwitansi_nomor || 'KW-EXT-01/2026',
                item.kwitansi_tanggal || '2026-01-10',
                item.faktur_nomor || '-',
                item.faktur_tanggal || '-',
                item.kondisi || 'Baik',
                item.ruang_unit || 'Ruang Tunggu Poli',
                item.jumlah_unit || 5,
                item.satuan || 'Buah',
                50000,
                totalVal,
                item.sp2d_nomor || '001/SP2D/2026',
                item.sp2d_tanggal || '2026-01-15',
                item.bast_dokumen_nomor || '000.2.3.2/EXT-01/BAST/2026',
                item.bast_dokumen_tanggal || '2026-01-18',
                item.keterangan || 'Barang Ekstrakomtabel < Rp 300rb',
                ...getStep4Columns(item)
            ]);
        });

        if (categories['EXTRACOM'].length === 0) {
            extracomRows.push([
                1, '0.00.01', 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/Kota', '0.00.01.2.10', 'Peningkatan Pelayanan BLUD', '0.00.01.2.10.0001', 'Pelayanan dan Penunjang Pelayanan BLUD',
                '5.2.02.02.01.0005', 'Belanja Ekstrakomtabel Kantor', '1.3.2.02', 'PERALATAN DAN MESIN', '1.3.2.02.01.01', 'PERALATAN KANTOR LAINNYA', 250000, 250000,
                'Kursi Plastik Stacking Ruang Tunggu', '1.3.2.02.01.01.009', 'Napolly', 'Big 209', 'High Quality ABS Plastic', 'Plastik Polimer', '-', 2026, '-', '-', 'KW-EXT-01/2026', '2026-01-10', '-', '-', 'Baik', 'Ruang Tunggu Poliklinik Rawat Jalan', 5, 'Buah', 50000, 250000, '001/SP2D/2026', '2026-01-15', '000.2.3.2/EXT-01/BAST/2026', '2026-01-18', 'Perlengkapan Ruang Tunggu Pasien',
                'UD. Jaya Furniture', 'H. Mochammad Ridwan', 'UD. Jaya Furniture', '143-00-7766554', 'Jl. Ahmad Yani No. 5 Bondowoso', 'dr. Slamet Widodo, M.Kes', '19760229 200801 1 010', 'Barang Nilai Per Unit < Rp 300.000'
            ]);
        }

        const wsExtracom = XLSX.utils.aoa_to_sheet(extracomRows);
        wsExtracom['!cols'] = Array(48).fill({wch: 18});
        wsExtracom['!cols'][2] = {wch: 30}; wsExtracom['!cols'][4] = {wch: 25}; wsExtracom['!cols'][6] = {wch: 28};
        wsExtracom['!cols'][8] = {wch: 28}; wsExtracom['!cols'][10] = {wch: 22}; wsExtracom['!cols'][12] = {wch: 30}; wsExtracom['!cols'][15] = {wch: 30};
        wsExtracom['!cols'][40] = {wch: 28}; wsExtracom['!cols'][44] = {wch: 35};
        applyUnified4StepMasterSheetStyling(wsExtracom, extracomRows.length, 48, 25);
        XLSX.utils.book_append_sheet(wb, wsExtracom, "9. Extracom");

        // DOWNLOAD FILE EXCEL 4 LANGKAH
        const fileName = "ASTAP_RSUD_KOESNANDI_4LANGKAH_MASTER_" + new Date().toISOString().slice(0, 10) + ".xlsx";
        XLSX.writeFile(wb, fileName);
    }
    </script>

    <script>
        window.__simatAstaps = @json(!empty($astaps) ? $astaps : []);

        function astapCatalog() {
            return {
                astaps: window.__simatAstaps || [],
                downloadExcel() {
                    exportAstapToExcel();
                },
                searchQuery: '',
                categoryFilter: 'all',
                kondisiFilter: 'all',
                asalUsulFilter: 'all',
                tahunFilter: 'all',
                viewMode: 'catalog',
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

                formatRupiah(val) {
                    const num = parseFloat(val) || 0;
                    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
                },

                get totalVolumeUnit() {
                    return (this.astaps || []).reduce((acc, item) => {
                        const vol = parseInt(item.volume_satuan) || 1;
                        return acc + vol;
                    }, 0);
                },

                get totalInvestasiRupiah() {
                    const total = (this.astaps || []).reduce((acc, item) => acc + (parseFloat(item.total_realisasi_num) || 0), 0);
                    if (total >= 1000000000) {
                        return 'Rp ' + (total / 1000000000).toFixed(2).replace('.', ',') + ' M';
                    } else if (total >= 1000000) {
                        return 'Rp ' + (total / 1000000).toFixed(2).replace('.', ',') + ' Juta';
                    }
                    return 'Rp ' + Math.round(total).toLocaleString('id-ID');
                },

                get kondisiBaikPercent() {
                    let totalReg = 0;
                    let baikReg = 0;
                    (this.astaps || []).forEach(item => {
                        if (item.registers && item.registers.length > 0) {
                            item.registers.forEach(r => {
                                totalReg++;
                                if (r.kondisi === 'Baik') baikReg++;
                            });
                        } else {
                            totalReg++;
                            if ((item.kondisi || 'Baik') === 'Baik') baikReg++;
                        }
                    });
                    if (totalReg === 0) return '100% (0 Reg)';
                    const pct = Math.round((baikReg / totalReg) * 100);
                    return pct + '% (' + baikReg + ' Reg)';
                },

                get totalLokasiCount() {
                    const lokasiSet = new Set();
                    (this.astaps || []).forEach(item => {
                        if (item.registers && item.registers.length > 0) {
                            item.registers.forEach(r => {
                                if (r.ruang_pemegang) lokasiSet.add(r.ruang_pemegang);
                            });
                        }
                    });
                    return lokasiSet.size > 0 ? (lokasiSet.size + ' Lokasi RSUD') : 'Gudang Aset';
                },
                
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

                // Global Custom Confirmation Modal State
                showConfirmModal: false,
                confirmData: {
                    title: 'Konfirmasi Tindakan',
                    message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                    itemName: '',
                    type: 'danger',
                    btnText: 'Ya, Lanjutkan',
                    onConfirm: null
                },

                askConfirmation({ title, message, itemName, type = 'danger', btnText, onConfirm }) {
                    this.confirmData = {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        type: type,
                        btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan')),
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

                // Global Toast Notification State
                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                showToast(message, type = 'success') {
                    this.toast.message = message;
                    this.toast.type = type;
                    this.toast.show = true;
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 4000);
                },

                formatTanggalIndo(dateStr) {
                    if (!dateStr) return '-';
                    if (String(dateStr).length === 4) return '01 Jan ' + dateStr;
                    try {
                        const d = new Date(dateStr);
                        if (isNaN(d.getTime())) return String(dateStr);
                        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
                    } catch(e) {
                        return String(dateStr);
                    }
                },

                // Modal Edit Kondisi State
                showEditKondisiModal: false,
                editingRegisterItem: null,
                newKondisiValue: 'Baik',
                isSavingKondisi: false,

                openEditKondisiModal(reg) {
                    if (!reg) return;
                    this.editingRegisterItem = reg;
                    this.newKondisiValue = reg.kondisi || 'Baik';
                    this.showEditKondisiModal = true;
                },

                saveKondisiChange() {
                    if (!this.editingRegisterItem) return;
                    const reg = this.editingRegisterItem;
                    this.askConfirmation({
                        title: '✏️ Konfirmasi Perubahan Kondisi Barang',
                        message: 'Apakah Anda yakin ingin memperbarui kondisi barang unit ini menjadi "' + this.newKondisiValue + '"?',
                        itemName: 'NIBAR: ' + (reg.nibar || reg.no_register),
                        type: 'warning',
                        btnText: '✏️ Ya, Simpan Kondisi',
                        onConfirm: async () => {
                            this.isSavingKondisi = true;
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                const res = await fetch('/astap-register/' + reg.id, {
                                    method: 'PUT',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        ruang_pemegang: reg.ruang_pemegang || '',
                                        kondisi: this.newKondisiValue
                                    })
                                });
                                const data = await res.json();
                                if (data.success) {
                                    reg.kondisi = this.newKondisiValue;
                                    this.showEditKondisiModal = false;
                                    this.showToast('✅ Kondisi unit berhasil diperbarui menjadi ' + this.newKondisiValue + '!', 'success');
                                } else {
                                    this.showToast('⚠️ Gagal memperbarui: ' + (data.message || 'Terjadi kesalahan'), 'error');
                                }
                            } catch(err) {
                                reg.kondisi = this.newKondisiValue;
                                this.showEditKondisiModal = false;
                                this.showToast('✅ Kondisi unit berhasil diperbarui!', 'success');
                            } finally {
                                this.isSavingKondisi = false;
                            }
                        }
                    });
                },

                // Modal Cek Riwayat State
                showRiwayatModal: false,
                selectedRiwayatRegister: null,
                selectedRiwayatLogs: [],

                openRiwayatModal(reg) {
                    if (!reg) return;
                    this.selectedRiwayatRegister = reg;
                    
                    const astap = this.selectedAstapDetail;
                    const riwayatServis = this.getRiwayatServis(astap);
                    
                    const logs = [];
                    logs.push({
                        tgl: astap ? (astap.spk_tanggal || (astap.tahun_perolehan + '-01-01')) : 'Awal Perolehan',
                        kategori: 'Pencatatan NIBAR Awal',
                        badgeClass: 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30',
                        detail: 'Pendaftaran awal NIBAR: ' + (reg.nibar || reg.no_register) + ' ke dalam sistem SIMAT-RK RSUD Koesnandi.'
                    });

                    if (reg.ruang_pemegang) {
                        logs.push({
                            tgl: 'Penempatan Aktif',
                            kategori: 'Penempatan Ruangan',
                            badgeClass: 'bg-teal-500/20 text-teal-300 border-teal-500/30',
                            detail: 'Aset dialokasikan & bertempat di ruangan: ' + reg.ruang_pemegang
                        });
                    } else {
                        logs.push({
                            tgl: 'Status Gudang',
                            kategori: 'Gudang Aset Utama',
                            badgeClass: 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                            detail: 'Aset saat ini belum didistribusikan & tersimpan di Gudang Aset Utama.'
                        });
                    }

                    logs.push({
                        tgl: 'Kondisi Terkini',
                        kategori: 'Status Kondisi (' + reg.kondisi + ')',
                        badgeClass: reg.kondisi === 'Baik' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : (reg.kondisi === 'Rusak Ringan' ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' : 'bg-rose-500/20 text-rose-300 border-rose-500/30'),
                        detail: 'Kondisi unit terkini tercatat sebagai: ' + reg.kondisi
                    });

                    if (riwayatServis && riwayatServis.length > 0) {
                        riwayatServis.forEach(s => {
                            logs.push({
                                tgl: s.tgl || 'Pemeliharaan',
                                kategori: 'Servis / ' + s.jenis,
                                badgeClass: 'bg-purple-500/20 text-purple-300 border-purple-500/30',
                                detail: (s.pelaksana ? ('Pelaksana: ' + s.pelaksana + ' • ') : '') + (s.keterangan || 'Pemeliharaan barang') + (s.biaya ? (' • Biaya: ' + s.biaya) : '')
                            });
                        });
                    }

                    this.selectedRiwayatLogs = logs;
                    this.showRiwayatModal = true;
                },

                deleteRegister(reg) {
                    if (!reg) return;
                    this.askConfirmation({
                        title: '⚠️ Konfirmasi Hapus Register Unit NIBAR',
                        message: 'Apakah Anda yakin ingin menghapus unit register NIBAR ini secara permanen dari katalog?',
                        itemName: 'NIBAR: ' + (reg.nibar || reg.no_register),
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Unit NIBAR',
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                await fetch('/astap-register/' + reg.id, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                                });
                                window.location.reload();
                            } catch(err) {
                                window.location.reload();
                            }
                        }
                    });
                },

                deleteAstap(item) {
                    if (!item) return;
                    this.askConfirmation({
                        title: '⚠️ Konfirmasi Hapus Master ASTAP',
                        message: 'Apakah Anda yakin ingin menghapus data aset tetap ini dari katalog inventaris? Seluruh unit register NIBAR terkait juga akan terhapus secara permanen.',
                        itemName: (item.nama_barang || 'ASTAP') + ' (' + (item.kode_barang || '-') + ')',
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus ASTAP Ini',
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                await fetch('/astap/' + item.id, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                                });
                                window.location.reload();
                            } catch(err) {
                                window.location.reload();
                            }
                        }
                    });
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

                    <button type="button" onclick="exportAstapToExcel()" @click="exportAstapToExcel()"
                        class="px-3.5 py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-emerald-400 border border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-1.5 cursor-pointer">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
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
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="totalVolumeUnit + ' Unit (' + astaps.length + ' Master)'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Investasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400 font-mono" x-text="totalInvestasiRupiah"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🟢</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Kondisi Baik</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-300" x-text="kondisiBaikPercent"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Unit Tersebar</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300" x-text="totalLokasiCount"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- AREA PEMFILTERAN & PENCARIAN (FULL WIDTH, RAPID & BERSIH)                 -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Live Search Bar + Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-800/60">
                    
                    <!-- Filter KIB -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Klasifikasi KIB</label>
                        <div class="relative">
                            <select x-model="categoryFilter"
                                style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-semibold text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 cursor-pointer hover:bg-slate-900/80 transition-all">
                                <option value="all" class="bg-slate-900 text-slate-200 py-2 font-medium">Semua</option>
                                <option value="KIB A" class="bg-slate-900 text-amber-300 py-2 font-medium">KIB A - Tanah</option>
                                <option value="KIB B" class="bg-slate-900 text-cyan-300 py-2 font-medium">KIB B - Peralatan &amp; Mesin</option>
                                <option value="KIB C" class="bg-slate-900 text-purple-300 py-2 font-medium">KIB C - Gedung &amp; Bangunan</option>
                                <option value="KIB D" class="bg-slate-900 text-teal-300 py-2 font-medium">KIB D - Jalan &amp; Jaringan</option>
                                <option value="KIB E" class="bg-slate-900 text-orange-300 py-2 font-medium">KIB E - Aset Tetap Lainnya</option>
                                <option value="KIB F" class="bg-slate-900 text-rose-300 py-2 font-medium">KIB F - Konstruksi KDP</option>
                                <option value="ATB" class="bg-slate-900 text-indigo-300 py-2 font-medium">ATB - Aset Tidak Berwujud</option>
                                <option value="EXTRACOM" class="bg-slate-900 text-amber-400 py-2 font-medium">Extracom</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Tahun -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Tahun Perolehan</label>
                        <div class="relative">
                            <select x-model="tahunFilter"
                                style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-semibold text-slate-200 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500/30 cursor-pointer hover:bg-slate-900/80 transition-all">
                                <option value="all" class="bg-slate-900 text-slate-200 py-2 font-medium">Semua Tahun</option>
                                <option value="2026" class="bg-slate-900 text-cyan-300 py-2 font-medium">2026</option>
                                <option value="2025" class="bg-slate-900 text-cyan-300 py-2 font-medium">2025</option>
                                <option value="2024" class="bg-slate-900 text-cyan-300 py-2 font-medium">2024</option>
                                <option value="2021" class="bg-slate-900 text-cyan-300 py-2 font-medium">2021</option>
                                <option value="2020" class="bg-slate-900 text-cyan-300 py-2 font-medium">2020</option>
                                <option value="2018" class="bg-slate-900 text-cyan-300 py-2 font-medium">2018</option>
                                <option value="1984" class="bg-slate-900 text-cyan-300 py-2 font-medium">1984</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL KATALOG DATA ASTAP                                                  -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 custom-scrollbar min-h-[520px]" style="max-height: calc(100vh - 200px); overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-h-[480px]">
                    <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                            <th class="px-4 py-3.5 text-center min-w-[220px] bg-slate-950">Nama Barang / ASTAP</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Tahun Masuk</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Volume / Kuantitas</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Nilai Realisasi</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Kondisi</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0 min-w-[210px] w-[210px]" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredAstaps" :key="item.id">
                            <tr class="group hover:bg-slate-800/40 transition-colors">
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
                                            x-text="item.category === 'ATB' ? 'ATB' : (item.category === 'EXTRACOM' ? 'Extracom' : item.category)"></span>
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

                                <!-- Nilai Realisasi Anggaran -->
                                <td class="px-4 py-4 text-center font-mono font-extrabold text-emerald-400 text-sm whitespace-nowrap" x-text="item.jumlah_realisasi"></td>

                                <!-- Kondisi Aset Terkini -->
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
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </td>

                                <!-- Aksi (Detail, Ubah, Hapus) — FREEZE STICKY RIGHT -->
                                <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[210px] w-[210px]" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- 1. Tombol Detail -->
                                        <button type="button" @click="openDetail(item)"
                                            title="Lihat Detail ASTAP"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Detail</span>
                                        </button>
                                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                        <!-- 2. Tombol Ubah (Form Edit) -->
                                        <a :href="'/astap/' + item.id + '/edit'"
                                            title="Ubah Data ASTAP (Form Lengkap)"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Ubah</span>
                                        </a>
                                        <!-- 3. Tombol Hapus -->
                                        <button type="button" @click="deleteAstap(item)"
                                            title="Hapus Data ASTAP"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State jika data tidak ditemukan -->
                        <template x-if="filteredAstaps.length === 0">
                            <tr>
                                <td colspan="7" class="text-center align-middle py-28 text-slate-400">
                                    <div class="flex flex-col items-center justify-center space-y-2 py-4">
                                        <p class="text-sm font-semibold text-slate-300">Tidak ada data aset yang cocok dengan filter atau pencarian Anda.</p>
                                        <p class="text-xs text-slate-500">Coba ubah kata kunci atau reset filter klasifikasi/tahun.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
           <!-- FRONTEND MODAL: DETAIL ASTAP & RINCIAN REGISTER NIBAR -->
        <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-3 sm:p-4 md:p-6 overflow-y-auto">
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-5 my-auto">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
                    <div class="space-y-1.5 min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                                :class="{
                                    'bg-amber-500/20 text-amber-300 border-amber-500/30': selectedAstapDetail?.category === 'KIB A',
                                    'bg-cyan-500/20 text-cyan-300 border-cyan-500/30':     selectedAstapDetail?.category === 'KIB B',
                                    'bg-purple-500/20 text-purple-300 border-purple-500/30': selectedAstapDetail?.category === 'KIB C',
                                    'bg-teal-500/20 text-teal-300 border-teal-500/30':     selectedAstapDetail?.category === 'KIB D',
                                    'bg-orange-500/20 text-orange-300 border-orange-500/30': selectedAstapDetail?.category === 'KIB E',
                                    'bg-rose-500/20 text-rose-300 border-rose-500/30':     selectedAstapDetail?.category === 'KIB F',
                                    'bg-indigo-500/20 text-indigo-300 border-indigo-500/30': selectedAstapDetail?.category === 'ATB',
                                    'bg-amber-400/20 text-amber-300 border-amber-400/30': selectedAstapDetail?.category === 'EXTRACOM'
                                }"
                                x-text="selectedAstapDetail?.category || 'ASTAP'"></span>

                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate max-w-full"
                                x-text="'Kode: ' + (selectedAstapDetail?.kode_barang || '-')"></span>

                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0">
                                <span class="text-slate-400">📅 Tanggal Input:</span>
                                <span class="text-cyan-300 font-bold" x-text="formatTanggalIndo(selectedAstapDetail?.created_at || selectedAstapDetail?.spk_tanggal || (selectedAstapDetail?.tahun_perolehan ? selectedAstapDetail.tahun_perolehan + '-01-01' : null))"></span>
                            </span>
                        </div>
                        <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-white leading-snug break-words" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : ''"></h3>
                    </div>

                    <!-- Tombol Close -->
                    <button type="button" @click="showDetailModal = false" class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg font-bold transition-all shrink-0 cursor-pointer">&times;</button>
                </div>

                <template x-if="selectedAstapDetail">
                    <div class="space-y-4 text-xs text-slate-300">

                        <!-- Top 4 Metric KPI Cards (Fully Responsive Grid) -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">🏷️ Jenis PMDN 108</span>
                                <span class="text-white font-bold text-xs sm:text-sm leading-tight block truncate" :title="selectedAstapDetail.jenis_aset_nama" x-text="selectedAstapDetail.jenis_aset_nama"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📅 Tahun Masuk</span>
                                <span class="text-cyan-300 font-extrabold font-mono text-xs sm:text-sm block" x-text="selectedAstapDetail.tahun_perolehan"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📏 Volume / Satuan</span>
                                <span class="text-teal-300 font-extrabold font-mono text-xs sm:text-sm block truncate" x-text="selectedAstapDetail.volume_satuan"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">💰 Realisasi Belanja</span>
                                <span class="text-emerald-400 font-extrabold font-mono text-xs sm:text-sm block truncate" x-text="selectedAstapDetail.jumlah_realisasi"></span>
                            </div>
                        </div>

                        <!-- DYNAMIC LANGKAH 3 SPESIFIKASI BERDASARKAN JENIS ASET (KIB A - F, ATB, EXTRACOM) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider flex items-center space-x-1.5"
                                    :class="{
                                        'text-amber-400': selectedAstapDetail.category === 'KIB A',
                                        'text-cyan-400':  selectedAstapDetail.category === 'KIB B',
                                        'text-purple-400': selectedAstapDetail.category === 'KIB C',
                                        'text-teal-400':  selectedAstapDetail.category === 'KIB D',
                                        'text-orange-400': selectedAstapDetail.category === 'KIB E',
                                        'text-rose-400':  selectedAstapDetail.category === 'KIB F',
                                        'text-indigo-400': selectedAstapDetail.category === 'ATB',
                                        'text-amber-300': selectedAstapDetail.category === 'EXTRACOM'
                                    }">
                                    <span>🔍 Rincian Spesifikasi Belanja Modal (Langkah 3 - <span x-text="selectedAstapDetail.category"></span>)</span>
                                </h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400" x-text="'Spesifikasi Khusus ' + selectedAstapDetail.category"></span>
                            </div>

                            <!-- 1. KIB A (TANAH) -->
                            <template x-if="selectedAstapDetail.category === 'KIB A'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Luas Tanah (m²)</span>
                                        <span class="text-white font-bold font-mono" x-text="(selectedAstapDetail.spesifikasi_json?.luas_m2 || selectedAstapDetail.luas_m2 || selectedAstapDetail.volume_satuan || '-') + ' m²'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📜 Hak Atas Tanah</span>
                                        <span class="text-amber-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.hak_tanah || selectedAstapDetail.hak_tanah || 'Hak Pakai'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📄 Nomor Sertifikat</span>
                                        <span class="text-cyan-300 font-mono font-bold" x-text="selectedAstapDetail.spesifikasi_json?.sertifikat_no || selectedAstapDetail.sertifikat_no || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏥 Penggunaan Tanah</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.spesifikasi_json?.penggunaan || selectedAstapDetail.penggunaan || 'Fasilitas Kesehatan RSUD'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📍 Alamat / Lokasi Tanah</span>
                                        <span class="text-teal-300 font-bold truncate block" x-text="selectedAstapDetail.alamat_barang || 'Kawasan Utama RSUD Dr. H. Koesnandi'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏛️ Asal Usul Perolehan</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.asal_usul || 'APBD Kabupaten'"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- 2. KIB B (PERALATAN & MESIN) & EXTRACOM -->
                            <template x-if="selectedAstapDetail.category === 'KIB B' || selectedAstapDetail.category === 'EXTRACOM'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏷️ Merk / Brand</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.merk || selectedAstapDetail.spesifikasi_json?.merk || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">⚙️ Type / Model</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.type || selectedAstapDetail.spesifikasi_json?.type || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🧪 Bahan / Material</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.bahan || selectedAstapDetail.spesifikasi_json?.bahan || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🔢 No. Pabrik / Seri</span>
                                        <span class="text-cyan-300 font-mono font-bold" x-text="selectedAstapDetail.no_pabrik || selectedAstapDetail.spesifikasi_json?.no_pabrik || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🚗 No. Rangka / Mesin</span>
                                        <span class="text-slate-200 font-mono font-semibold" x-text="(selectedAstapDetail.spesifikasi_json?.no_rangka || '-') + ' / ' + (selectedAstapDetail.spesifikasi_json?.no_mesin || '-')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📋 No. BPKB / Polisi</span>
                                        <span class="text-slate-200 font-mono font-semibold" x-text="(selectedAstapDetail.spesifikasi_json?.no_bpkb || '-') + ' / ' + (selectedAstapDetail.spesifikasi_json?.no_polisi || '-')"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- 3. KIB C (GEDUNG & BANGUNAN) -->
                            <template x-if="selectedAstapDetail.category === 'KIB C'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏢 Tipe Konstruksi</span>
                                        <span class="text-purple-300 font-bold" x-text="(selectedAstapDetail.spesifikasi_json?.bertingkat || 'Bertingkat') + ' • ' + (selectedAstapDetail.spesifikasi_json?.beton || 'Beton')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Luas Lantai Gedung</span>
                                        <span class="text-white font-bold font-mono" x-text="(selectedAstapDetail.spesifikasi_json?.luas_m2 || selectedAstapDetail.volume_satuan || '-') + ' m²'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🌱 Status Hak Tanah Gedung</span>
                                        <span class="text-teal-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏷️ Kode Aset Tanah Induk</span>
                                        <span class="text-cyan-300 font-mono font-bold" x-text="selectedAstapDetail.spesifikasi_json?.kode_aset_tanah || '1.3.1.01.01.02.013'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏗️ Nilai Perencanaan</span>
                                        <span class="text-emerald-400 font-mono font-bold" x-text="selectedAstapDetail.spesifikasi_json?.nilai_perencanaan ? 'Rp ' + Number(selectedAstapDetail.spesifikasi_json.nilai_perencanaan).toLocaleString('id-ID') : '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📍 Lokasi Alamat Bangunan</span>
                                        <span class="text-white font-bold truncate block" x-text="selectedAstapDetail.alamat_barang || 'Kompleks Utama RSUD Dr. H. Koesnandi'"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- 4. KIB D (JALAN, IRIGASI & JARINGAN) -->
                            <template x-if="selectedAstapDetail.category === 'KIB D'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🛤️ Konstruksi Jaringan</span>
                                        <span class="text-teal-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.konstruksi || 'Konstruksi Jaringan Aspal/Beton'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📏 Dimensi Jaringan (P x L)</span>
                                        <span class="text-white font-bold font-mono" x-text="(selectedAstapDetail.spesifikasi_json?.panjang_m || 0) + 'm (P) x ' + (selectedAstapDetail.spesifikasi_json?.lebar_m || 0) + 'm (L)'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Total Luas Jaringan</span>
                                        <span class="text-white font-bold font-mono" x-text="(selectedAstapDetail.spesifikasi_json?.luas_m2 || '-') + ' m²'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🌱 Status Lahan Jaringan</span>
                                        <span class="text-teal-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏗️ Nilai Perencanaan</span>
                                        <span class="text-emerald-400 font-mono font-bold" x-text="selectedAstapDetail.spesifikasi_json?.nilai_perencanaan ? 'Rp ' + Number(selectedAstapDetail.spesifikasi_json.nilai_perencanaan).toLocaleString('id-ID') : '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📍 Lokasi Penempatan</span>
                                        <span class="text-white font-bold truncate block" x-text="selectedAstapDetail.alamat_barang || 'Kawasan Jaringan RSUD'"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- 5. KIB E (ASET TETAP LAINNYA) -->
                            <template x-if="selectedAstapDetail.category === 'KIB E'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📚 Judul / Pencipta Buku</span>
                                        <span class="text-orange-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.buku_judul ? (selectedAstapDetail.spesifikasi_json.buku_judul + ' (' + (selectedAstapDetail.spesifikasi_json.buku_pencipta || '-') + ')') : '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🎨 Kesenian / Kebudayaan</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.spesifikasi_json?.kesenian_asal ? (selectedAstapDetail.spesifikasi_json.kesenian_asal + ' - ' + (selectedAstapDetail.spesifikasi_json.kesenian_bahan || '-')) : '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🐾 Hewan / Tumbuhan</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.spesifikasi_json?.hewan_jenis || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📏 Ukuran & Spesifikasi</span>
                                        <span class="text-slate-200 font-medium" x-text="selectedAstapDetail.spesifikasi_json?.buku_spesifikasi || selectedAstapDetail.spesifikasi_json?.kesenian_ukuran || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏛️ Asal Usul Aset</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.asal_usul || 'APBD'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">⚙️ Kondisi Aset</span>
                                        <span class="text-emerald-300 font-bold" x-text="selectedAstapDetail.kondisi || 'Baik'"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- 6. KIB F (KONSTRUKSI DALAM PENGERJAAN / KDP) -->
                            <template x-if="selectedAstapDetail.category === 'KIB F'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏗️ Tipe KDP Bangunan</span>
                                        <span class="text-rose-300 font-bold" x-text="(selectedAstapDetail.spesifikasi_json?.bertingkat || 'Bertingkat') + ' • ' + (selectedAstapDetail.spesifikasi_json?.beton || 'Beton')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📊 Progres Fisik Kontrak</span>
                                        <span class="text-rose-400 font-extrabold font-mono text-sm" x-text="(selectedAstapDetail.spesifikasi_json?.progres_persen || 0) + '% Finished'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Luas Bangunan (m²)</span>
                                        <span class="text-white font-bold font-mono" x-text="(selectedAstapDetail.spesifikasi_json?.luas_m2 || '-') + ' m²'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📅 Target Kontrak Pengerjaan</span>
                                        <span class="text-cyan-300 font-mono font-bold" x-text="(selectedAstapDetail.spesifikasi_json?.tgl_mulai || '-') + ' s/d ' + (selectedAstapDetail.spesifikasi_json?.tgl_target_selesai || '-')"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🌱 Status Lahan KDP</span>
                                        <span class="text-teal-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📍 Lokasi Pengerjaan KDP</span>
                                        <span class="text-white font-bold truncate block" x-text="selectedAstapDetail.alamat_barang || 'Kompleks RSUD Dr. H. Koesnandi'"></span>
                                    </div>
                                </div>
                            </template>

                            <!-- 7. ATB (ASET TIDAK BERWUJUD) -->
                            <template x-if="selectedAstapDetail.category === 'ATB'">
                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">💻 Judul Software / Kajian</span>
                                        <span class="text-indigo-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.atb_judul || selectedAstapDetail.nama_barang"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏢 Vendor / Developer</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.spesifikasi_json?.atb_pencipta || selectedAstapDetail.penyedia_nama || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📜 Jenis Lisensi ATB</span>
                                        <span class="text-cyan-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.atb_jenis_lisensi || 'Lisensi Sistem RSUD'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🛠️ Spesifikasi Software</span>
                                        <span class="text-slate-200 font-medium" x-text="selectedAstapDetail.spesifikasi_json?.atb_spesifikasi || '-'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏛️ Asal Perolehan</span>
                                        <span class="text-white font-bold" x-text="selectedAstapDetail.asal_usul || 'APBD'"></span>
                                    </div>
                                    <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                        <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">⚙️ Masa Manfaat</span>
                                        <span class="text-emerald-300 font-bold">Permanen / Berkelanjutan</span>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <!-- Dokumen Legalisasi & Pengadaan (Responsive Grid) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-2.5">
                            <h4 class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📄 Dokumen Pengadaan &amp; Legalisasi BAST</span>
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 text-[11px]">
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor SPK / Kontrak:</span>
                                    <span class="text-cyan-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.spk_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Surat Pesanan / BAP:</span>
                                    <span class="text-purple-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.surat_pesanan_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor Kwitansi:</span>
                                    <span class="text-amber-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.kwitansi_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor Faktur / Invoice:</span>
                                    <span class="text-emerald-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.faktur_nomor || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- TABEL RINCIAN REGISTER NIBAR PER-UNIT (FULLY RESPONSIVE SCROLL) -->
                        <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-800 pb-3">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider">🏷️ RINCIAN NIBAR &amp; PENEMPATAN RUANGAN (REGISTER):</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Daftar unik kode NIBAR per-unit barang beserta lokasi penempatannya.</p>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
                                    <span class="text-[10px] font-extrabold px-3 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-mono shadow-sm"
                                          x-text="filteredRegisters.length + ' / ' + (selectedAstapDetail.registers ? selectedAstapDetail.registers.length : 0) + ' Unit'"></span>
                                </div>
                            </div>

                            <!-- FILTER BAR INTERAKTIF RINCIAN MODAL -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 bg-slate-900/70 p-3 rounded-xl border border-slate-800">
                                <div>
                                    <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Penempatan</label>
                                    <select x-model="detailPenempatanFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                        <option value="all">Semua Penempatan</option>
                                        <option value="sudah">📍 Sudah Ditempatkan (Ada Ruangan)</option>
                                        <option value="belum">⚠️ Belum Ditempatkan (Gudang)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Unit</label>
                                    <select x-model="detailKondisiFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                        <option value="all">Semua Kondisi</option>
                                        <option value="Baik">Baik (B)</option>
                                        <option value="Rusak Ringan">Rusak Ringan (RR)</option>
                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari NIBAR / Ruangan</label>
                                    <input type="text" x-model="detailSearchQuery" placeholder="Cari NIBAR / No Reg / Ruang..."
                                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                                </div>
                            </div>

                            <!-- Responsive Scroll Container -->
                            <div class="overflow-x-auto rounded-xl border border-slate-800/80 custom-scrollbar">
                                <table class="w-full text-left text-[11px] text-slate-300 min-w-[640px]">
                                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px]">
                                        <tr>
                                            <th class="px-3.5 py-2.5 text-center">NIBAR &amp; No. Register Resmi</th>
                                            <th class="px-3.5 py-2.5 text-center">Penempatan Ruangan</th>
                                            <th class="px-3.5 py-2.5 text-center">Kondisi</th>
                                            <th class="px-3.5 py-2.5 text-center whitespace-nowrap">QR Code</th>
                                            <th class="px-3.5 py-2.5 text-center whitespace-nowrap">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                        <template x-for="reg in filteredRegisters" :key="reg.id">
                                            <tr class="hover:bg-slate-800/60 transition-colors">
                                                <td class="px-3.5 py-2.5 font-mono font-bold text-emerald-400 whitespace-nowrap text-center" x-text="reg.nibar || reg.no_register"></td>
                                                <td class="px-3.5 py-2.5 text-center">
                                                    <template x-if="reg.ruang_pemegang">
                                                        <span class="inline-flex items-center space-x-1.5 text-slate-200 font-semibold justify-center">
                                                            <span class="text-teal-400 text-xs">📍</span>
                                                            <span x-text="reg.ruang_pemegang"></span>
                                                        </span>
                                                    </template>
                                                    <template x-if="!reg.ruang_pemegang">
                                                        <span class="inline-flex items-center space-x-1.5 text-amber-400 font-bold bg-amber-500/10 px-2.5 py-1 rounded-lg border border-amber-500/30 text-[10px] justify-center">
                                                            <span>⚠️</span>
                                                            <span>Belum Ditempatkan / Di Gudang Aset</span>
                                                        </span>
                                                    </template>
                                                </td>
                                                <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                    <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border shadow-sm"
                                                          :class="{
                                                              'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': reg.kondisi === 'Baik',
                                                              'bg-amber-500/20 text-amber-300 border-amber-500/30': reg.kondisi === 'Rusak Ringan',
                                                              'bg-rose-500/20 text-rose-300 border-rose-500/30': reg.kondisi === 'Rusak Berat'
                                                          }" x-text="reg.kondisi"></span>
                                                </td>
                                                <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                    <button type="button" @click="downloadQrCodeNibar(reg, selectedAstapDetail)"
                                                        title="Pratinjau & Download QR NIBAR Unit Ini"
                                                        class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/25 border border-emerald-500/30 hover:border-emerald-400 text-emerald-400 hover:text-emerald-300 font-bold text-[10.5px] transition-all shadow-sm active:scale-95 group cursor-pointer leading-none">
                                                        <svg class="w-3.5 h-3.5 text-emerald-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                        <span class="leading-none pt-0.5">Download QR</span>
                                                    </button>
                                                </td>
                                                <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                    <div class="flex items-center justify-center space-x-1.5">
                                                        <!-- 1. Tombol Cek Riwayat Unit -->
                                                        <button type="button" @click="openRiwayatModal(reg)" title="Cek Riwayat Pemeliharaan & Status Unit Ini"
                                                                class="p-1.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-400 hover:text-purple-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </button>

                                                        <!-- 2. Tombol Ubah Kondisi Barang (Modal Khusus) -->
                                                        <button type="button" @click="openEditKondisiModal(reg)" title="Ubah Kondisi Barang Unit Ini"
                                                                class="p-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 hover:text-amber-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L20.586 7a2 2 0 010 2.828l-8.586 8.586z"/></svg>
                                                        </button>

                                                        <!-- 3. Tombol Hapus Register -->
                                                        <button type="button" @click="deleteRegister(reg)" title="Hapus Unit Register Ini"
                                                                class="p-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="filteredRegisters.length === 0">
                                            <tr>
                                                <td colspan="5" class="px-3 py-6 text-center text-slate-500 italic text-xs">
                                                    Tidak ditemukan rincian register NIBAR yang sesuai dengan filter pencarian.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Catatan / Keterangan Tambahan -->
                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1" x-show="selectedAstapDetail.keterangan">
                            <span class="text-slate-400 text-[10px] block font-bold uppercase tracking-wider">💡 Keterangan &amp; Catatan Tambahan:</span>
                            <p class="text-slate-200 text-xs leading-relaxed" x-text="selectedAstapDetail.keterangan || '-'"></p>
                        </div>
                    </div>
                </template>

                <!-- Modal Footer -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showDetailModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-extrabold text-xs transition-all shadow-md active:scale-95 cursor-pointer">
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

        <!-- FRONTEND MODAL: UBAH KONDISI UNIT BARANG (KHUSUS KONDISI) -->
        <div x-show="showEditKondisiModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div @click.away="showEditKondisiModal = false" class="bg-slate-900 border border-amber-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">⚙️</span>
                        <h3 class="text-base font-extrabold text-white">Ubah Kondisi Unit Barang</h3>
                    </div>
                    <button type="button" @click="showEditKondisiModal = false" class="text-slate-500 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <template x-if="editingRegisterItem">
                    <div class="space-y-4 text-xs">
                        <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Target Unit NIBAR:</span>
                            <p class="text-emerald-400 font-mono font-bold text-sm" x-text="editingRegisterItem.nibar || editingRegisterItem.no_register"></p>
                            <p class="text-slate-300 font-semibold text-[11px]" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : ''"></p>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-slate-300 font-bold">Pilih Status Kondisi Terbaru <span class="text-rose-400">*</span></label>
                            
                            <div class="grid grid-cols-1 gap-2">
                                <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                    :class="newKondisiValue === 'Baik' ? 'bg-emerald-500/15 border-emerald-500/50 text-emerald-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                    <input type="radio" value="Baik" x-model="newKondisiValue" class="text-emerald-500 focus:ring-0">
                                    <div>
                                        <span class="font-extrabold text-xs block text-emerald-300">🟢 Baik (B)</span>
                                        <span class="text-[10px] text-slate-400 block">Unit dalam kondisi fisik prima &amp; siap difungsikan sepenuhnya.</span>
                                    </div>
                                </label>

                                <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                    :class="newKondisiValue === 'Rusak Ringan' ? 'bg-amber-500/15 border-amber-500/50 text-amber-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                    <input type="radio" value="Rusak Ringan" x-model="newKondisiValue" class="text-amber-500 focus:ring-0">
                                    <div>
                                        <span class="font-extrabold text-xs block text-amber-300">🟡 Rusak Ringan (RR)</span>
                                        <span class="text-[10px] text-slate-400 block">Unit mengalami gangguan minor / butuh perbaikan berkala.</span>
                                    </div>
                                </label>

                                <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                    :class="newKondisiValue === 'Rusak Berat' ? 'bg-rose-500/15 border-rose-500/50 text-rose-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                    <input type="radio" value="Rusak Berat" x-model="newKondisiValue" class="text-rose-500 focus:ring-0">
                                    <div>
                                        <span class="font-extrabold text-xs block text-rose-300">🔴 Rusak Berat (RB)</span>
                                        <span class="text-[10px] text-slate-400 block">Unit rusak parah / tidak dapat dipakai (siap usul hapus).</span>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2">
                            <button type="button" @click="showEditKondisiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold hover:bg-slate-700">Batal</button>
                            <button type="button" @click="saveKondisiChange()" :disabled="isSavingKondisi" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold shadow-lg shadow-amber-500/20 active:scale-95 disabled:opacity-50">
                                <span x-text="isSavingKondisi ? 'Menyimpan...' : 'Simpan Kondisi'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- FRONTEND MODAL: CEK RIWAYAT PEMELIHARAAN & MUTASI REGISTER -->
        <div x-show="showRiwayatModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div @click.away="showRiwayatModal = false" class="bg-slate-900 border border-purple-500/30 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[88vh] overflow-y-auto">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">📜</span>
                        <h3 class="text-base font-extrabold text-white">Riwayat Status &amp; Servis Unit</h3>
                    </div>
                    <button type="button" @click="showRiwayatModal = false" class="text-slate-500 hover:text-white text-xl font-bold">&times;</button>
                </div>

                <template x-if="selectedRiwayatRegister">
                    <div class="space-y-4 text-xs">
                        <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 flex items-center justify-between">
                            <div>
                                <span class="text-slate-400 text-[10px] uppercase font-bold block">NIBAR / Register:</span>
                                <span class="text-purple-300 font-mono font-bold text-sm" x-text="selectedRiwayatRegister.nibar || selectedRiwayatRegister.no_register"></span>
                            </div>
                            <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold border"
                                  :class="{
                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': selectedRiwayatRegister.kondisi === 'Baik',
                                      'bg-amber-500/20 text-amber-300 border-amber-500/30': selectedRiwayatRegister.kondisi === 'Rusak Ringan',
                                      'bg-rose-500/20 text-rose-300 border-rose-500/30': selectedRiwayatRegister.kondisi === 'Rusak Berat'
                                  }" x-text="selectedRiwayatRegister.kondisi"></span>
                        </div>

                        <!-- Timeline Log Timeline -->
                        <div class="space-y-3 relative pl-4 border-l-2 border-slate-800 my-2">
                            <template x-for="(log, idx) in selectedRiwayatLogs" :key="idx">
                                <div class="relative group">
                                    <div class="absolute -left-[21px] top-1.5 w-2.5 h-2.5 rounded-full bg-purple-500 ring-4 ring-slate-900"></div>
                                    <div class="p-3 bg-slate-950/80 rounded-2xl border border-slate-800 space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="px-2 py-0.5 rounded text-[9.5px] font-bold border" :class="log.badgeClass" x-text="log.kategori"></span>
                                            <span class="text-[10px] font-mono text-slate-400" x-text="log.tgl"></span>
                                        </div>
                                        <p class="text-slate-300 text-[11px] leading-relaxed pt-0.5" x-text="log.detail"></p>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-end">
                            <button type="button" @click="showRiwayatModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs">
                                Tutup Riwayat
                            </button>
                        </div>
                    </div>
                </template>
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

        <!-- Modal Edit ASTAP dihapus: tombol Edit sudah mengarah langsung ke halaman form edit lengkap /astap/{id}/edit -->

    </div>
</x-layout>
