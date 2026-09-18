    <!-- Library SheetJS dengan Dukungan Penuh Cell Styling (Warna, Font, Border & Alignment) -->
    <script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"></script>
    <!-- Library QR Code Generator Offline (Self-Hosted Standalone) -->
    <script src="{{ asset('js/qrcode.min.js') }}"></script>

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

    // STYLING ENGINE KHUSUS SHEET 1: REKAPITULASI
    function applyRekapSheetStyling(ws, rowCount, colCount, titleRowCount = 5, totalRowIdx = 14, signStartRow = 16) {
        const thinBorder = {
            top: { style: "thin", color: { rgb: "64748B" } },
            bottom: { style: "thin", color: { rgb: "64748B" } },
            left: { style: "thin", color: { rgb: "64748B" } },
            right: { style: "thin", color: { rgb: "64748B" } }
        };

        const doubleBottomBorder = {
            top: { style: "thin", color: { rgb: "0F172A" } },
            bottom: { style: "double", color: { rgb: "0F172A" } },
            left: { style: "thin", color: { rgb: "64748B" } },
            right: { style: "thin", color: { rgb: "64748B" } }
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
                let fontSize = 9.5;
                let numFmt = null;

                // 1. BARIS JUDUL LAPORAN (r < titleRowCount)
                if (r < titleRowCount) {
                    fill = "FFFFFF";
                    fontColor = "0F172A";
                    bold = true;
                    fontSize = (r === 0 || r === 1) ? 12 : 11;
                    align = "center";
                    border = null;
                }
                // 2. HEADER TABEL REKAPITULASI (r === titleRowCount)
                else if (r === titleRowCount) {
                    fill = "1E3A8A"; // Deep Navy Header
                    fontColor = "FFFFFF"; // White text
                    bold = true;
                    fontSize = 10;
                    align = "center";
                    border = thinBorder;
                }
                // 3. BARIS TOTAL / FOOTER TABEL (r === totalRowIdx)
                else if (r === totalRowIdx) {
                    fill = "93C5FD"; // Soft Blue matching master sheets
                    fontColor = "0F172A";
                    bold = true;
                    fontSize = 10;
                    border = doubleBottomBorder;

                    if (c === 0) {
                        align = "center";
                    } else if (c === 3 || c === 7) {
                        align = "center";
                    } else if (c === 4 || c === 5 || c === 6) {
                        align = "right";
                        numFmt = "Rp #,##0.00";
                    } else {
                        align = "left";
                    }
                }
                // 4. BAGIAN TANDA TANGAN / PENGESAHAN (r >= signStartRow)
                else if (r >= signStartRow) {
                    continue; // Ditangani khusus oleh applySignatureBlockStyling
                }
                // 5. BARIS DATA BIASA (r > titleRowCount && r < totalRowIdx)
                else {
                    fill = (r % 2 === 0) ? "FFFFFF" : "F8FAFC";

                    if (c === 0) {
                        align = "center"; // No
                    } else if (c === 1) {
                        align = "left"; // Kelompok Aset
                        bold = true;
                    } else if (c === 2) {
                        align = "center"; // Kode Rekening
                    } else if (c === 3) {
                        align = "center"; // Volume / Item
                    } else if (c === 4) {
                        align = "right"; // Anggaran
                        numFmt = "Rp #,##0.00";
                    } else if (c === 5) {
                        align = "right"; // Realisasi
                        bold = true;
                        fill = "EFF6FF"; // Soft blue highlight
                        numFmt = "Rp #,##0.00";
                    } else if (c === 6) {
                        align = "right"; // Selisih / Sisa
                        numFmt = "Rp #,##0.00";
                    } else if (c === 7) {
                        align = "center"; // Persentase
                        bold = true;
                    } else {
                        align = "left"; // Keterangan
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

    // HELPER MEMBUAT JUDUL LAPORAN BAKU PER KIB SESUAI FORMAT GAMBAR
    function getKibTitleRows(kibCategoryName, yearLabel, filterTw) {
        let isTw = false;
        let twRoman = "";
        if (filterTw && filterTw !== 'all') {
            const twKey = String(filterTw).replace(/[\s_]/g, '').toUpperCase();
            if (twKey === 'TWI' || twKey === 'TW1' || twKey === 'TRIWULAN1' || twKey === '1') {
                isTw = true;
                twRoman = "TRIWULAN I";
            } else if (twKey === 'TWII' || twKey === 'TW2' || twKey === 'TRIWULAN2' || twKey === '2') {
                isTw = true;
                twRoman = "TRIWULAN II";
            } else if (twKey === 'TWIII' || twKey === 'TW3' || twKey === 'TRIWULAN3' || twKey === '3') {
                isTw = true;
                twRoman = "TRIWULAN III";
            } else if (twKey === 'TWIV' || twKey === 'TW4' || twKey === 'TRIWULAN4' || twKey === '4') {
                isTw = true;
                twRoman = "TRIWULAN IV";
            }
        }

        if (isTw) {
            return [
                ["PEMERINTAH KABUPATEN BONDOWOSO"],
                ["RUMAH SAKIT UMUM DAERAH dr.H.KOESNANDI"],
                ["LAMPIRAN BERITA ACARA SERAH TERIMA BARANG BELANJA MODAL " + kibCategoryName + " TAHUN " + yearLabel],
                ["Nomor : 000.2.3.2/112/430.10.7/" + yearLabel],
                [twRoman + " TAHUN " + yearLabel]
            ];
        } else {
            return [
                ["PEMERINTAH KABUPATEN BONDOWOSO"],
                ["RUMAH SAKIT UMUM DAERAH dr.H.KOESNANDI"],
                ["LAMPIRAN BERITA ACARA SERAH TERIMA BARANG BELANJA MODAL " + kibCategoryName + " TAHUN " + yearLabel],
                ["TAHUN ANGGARAN " + yearLabel]
            ];
        }
    }

    // HELPER MERGE CELLS DINAMIS PER KIB (TITLE, FOOTER, HEADER)
    function getKibMerges(baseMerges, colCount, titleRowCount, totalRowCount, hasSignature = false) {
        const offset = titleRowCount - 3; // Baseline merge header tabel adalah index row 3
        const titleMerges = [];
        for (let r = 0; r < titleRowCount; r++) {
            titleMerges.push({ s: { r: r, c: 0 }, e: { r: r, c: colCount - 1 } });
        }
        const shiftedBaseMerges = baseMerges.map(m => ({
            s: { r: m.s.r + offset, c: m.s.c },
            e: { r: m.e.r + offset, c: m.e.c }
        }));
        const footerRowIdx = hasSignature ? (totalRowCount - 1 - 9) : (totalRowCount - 1);
        const footerMerge = {
            s: { r: footerRowIdx, c: 0 },
            e: { r: footerRowIdx, c: 12 }
        };
        return [...titleMerges, footerMerge, ...shiftedBaseMerges];
    }

    /**
     * Menghasilkan teks tanggal akhir periode untuk tanda tangan (misal: "30 Juni 2026")
     */
    function getReportSignDate(filterTw, filterYear) {
        const yr = (filterYear && filterYear !== 'all') ? filterYear : (new Date().getFullYear());
        const twKey = String(filterTw || '').replace(/[\s_]/g, '').toUpperCase();
        if (twKey === 'TWI' || twKey === 'TW1') return '31 Maret ' + yr;
        if (twKey === 'TWII' || twKey === 'TW2') return '30 Juni ' + yr;
        if (twKey === 'TWIII' || twKey === 'TW3') return '30 September ' + yr;
        if (twKey === 'TWIV' || twKey === 'TW4') return '31 Desember ' + yr;
        return new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    /**
     * Membuat 9 baris tanda tangan baku sesuai format resmi RSUD Koesnadi (screenshot):
     *   r+0: kosong (spasi pemisah)
     *   r+1: "MENGETAHUI,"                         /  "Bondowoso, [tanggal]" (di rightStartCol)
     *   r+2: "DIREKTUR"                            /  "PENGURUS BARANG ASET"
     *   r+3: "RSUD dr.H.KOESNADI BONDOWOSO"        /  ""
     *   r+4: kosong (spasi tanda tangan)
     *   r+5: kosong (spasi tanda tangan)
     *   r+6: "dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR" /  "BUDI HARTONO,S.sos" (bold & underline)
     *   r+7: "Pembina Tk.I-IV/b"                   /  "NIP. 19760229 200801 1 010"
     *   r+8: "NIP. 19771002 200604 1 007"         /  ""
     */
    function buildKibSignatureRows(numCols, rightStartCol, ppkNama = '', ppkNip = '', signDate = '', leftStartCol = 1) {
        const today = signDate || new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
        const dirNama  = ppkNama && ppkNama !== '-' && ppkNama !== 'Pejabat Pembuat Komitmen (PPK)' ? ppkNama : 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR';
        const dirNip   = ppkNip  && ppkNip  !== '-' && ppkNip  !== '19780101 200501 1 008' ? ppkNip  : '19771002 200604 1 007';

        function makeRow(leftVal, rightVal) {
            const row = Array(numCols).fill('');
            row[leftStartCol]   = leftVal;
            row[rightStartCol]  = rightVal;
            return row;
        }

        return [
            Array(numCols).fill(''),                                                              // r+0 kosong
            makeRow('MENGETAHUI,',                   'Bondowoso, ' + today),                      // r+1 tanggal & mengetahui
            makeRow('DIREKTUR',                      'PENGURUS BARANG ASET'),                     // r+2 jabatan
            makeRow('RSUD dr.H.KOESNADI BONDOWOSO', ''),                                          // r+3 instansi kiri
            Array(numCols).fill(''),                                                              // r+4 spasi tanda tangan
            Array(numCols).fill(''),                                                              // r+5 spasi tanda tangan
            makeRow(dirNama,                         'BUDI HARTONO,S.sos'),                       // r+6 nama pejabat (bold, underline)
            makeRow('Pembina Tk.I-IV/b',             'NIP. 19760229 200801 1 010'),               // r+7 pangkat kiri & NIP kanan
            makeRow('NIP. ' + dirNip,                '')                                          // r+8 NIP kiri
        ];
    }

    /**
     * Menghasilkan array merges untuk 9 baris tanda tangan agar lurus tegak presisi.
     */
    function getKibSignatureMerges(signStartRow, numCols, rightStartCol, leftStartCol = 1, leftEndCol = null, rightEndCol = null) {
        const lS = leftStartCol;
        const lE = leftEndCol !== null ? leftEndCol : Math.min(lS + 5, rightStartCol - 2);
        const rS = rightStartCol;
        const rE = rightEndCol !== null ? rightEndCol : (numCols - 1);

        const merges = [];
        // Baris r+1: "MENGETAHUI," (kiri) & "Bondowoso, [tanggal]" (kanan)
        merges.push({ s: { r: signStartRow + 1, c: lS }, e: { r: signStartRow + 1, c: lE } });
        merges.push({ s: { r: signStartRow + 1, c: rS }, e: { r: signStartRow + 1, c: rE } });

        // Baris r+2: "DIREKTUR" (kiri) & "PENGURUS BARANG ASET" (kanan)
        merges.push({ s: { r: signStartRow + 2, c: lS }, e: { r: signStartRow + 2, c: lE } });
        merges.push({ s: { r: signStartRow + 2, c: rS }, e: { r: signStartRow + 2, c: rE } });

        // Baris r+3: "RSUD dr.H.KOESNADI BONDOWOSO" (kiri)
        merges.push({ s: { r: signStartRow + 3, c: lS }, e: { r: signStartRow + 3, c: lE } });

        // Baris r+6: Nama Direktur (kiri) & Nama Pengurus Barang (kanan)
        merges.push({ s: { r: signStartRow + 6, c: lS }, e: { r: signStartRow + 6, c: lE } });
        merges.push({ s: { r: signStartRow + 6, c: rS }, e: { r: signStartRow + 6, c: rE } });

        // Baris r+7: Pembina Tk.I-IV/b (kiri) & NIP Pengurus Barang (kanan)
        merges.push({ s: { r: signStartRow + 7, c: lS }, e: { r: signStartRow + 7, c: lE } });
        merges.push({ s: { r: signStartRow + 7, c: rS }, e: { r: signStartRow + 7, c: rE } });

        // Baris r+8: NIP Direktur (kiri)
        merges.push({ s: { r: signStartRow + 8, c: lS }, e: { r: signStartRow + 8, c: lE } });

        return merges;
    }

    /**
     * Menerapkan style baku (putih bersih, tanpa border, alignment tengah, bold, underline) pada 9 baris tanda tangan.
     */
    function applySignatureBlockStyling(ws, signStartRow, numCols) {
        for (let offset = 0; offset < 9; offset++) {
            const r = signStartRow + offset;
            for (let c = 0; c < numCols; c++) {
                const cellRef = getColName(c) + (r + 1);
                if (!ws[cellRef]) {
                    ws[cellRef] = { v: "", t: "s" };
                }
                const cell = ws[cellRef];
                const hasText = cell.v && String(cell.v).trim() !== '';

                cell.s = {
                    fill: { fgColor: { rgb: "FFFFFF" } },
                    font: {
                        name: "Calibri",
                        sz: (offset === 6) ? 10.5 : (offset === 7 || offset === 8 ? 9.5 : 10),
                        bold: (offset === 1 || offset === 2 || offset === 3 || offset === 6),
                        underline: (offset === 6 && hasText),
                        color: { rgb: "000000" }
                    },
                    alignment: {
                        vertical: "center",
                        horizontal: "center",
                        wrapText: false
                    },
                    border: null
                };
            }
        }
    }

    // HELPER GENERATE UNIQUE GROUP KEY (PER TAHUN, PER TRIWULAN, PER PROGRAM, KEGIATAN, SUB-KEGIATAN, REKENING, & SUB-RINCIAN PMDN 108)
    function getAstapGroupKey(item, fallbackPrefix = 'DEFAULT') {
        const yearKey = item.tahun_perolehan ? String(item.tahun_perolehan) : '-';
        const twKey = item.triwulan ? String(item.triwulan).replace(/[\s_]/g, '').toUpperCase() : '-';
        const progKey = item.program_kode || '-';
        const kegKey = item.kegiatan_kode || '-';
        const subKegKey = item.sub_kegiatan_kode || '-';
        const rekKey = item.rekening_kode || '-';
        const subRincianKey = item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : fallbackPrefix));
        
        return `${yearKey}___${twKey}___${progKey}___${kegKey}___${subKegKey}___${rekKey}___${subRincianKey}`;
    }

    // HELPER FORMAT DATE DD/MM/YYYY
    function formatAstapDate(val) {
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
    }

    // STYLING ENGINE MASTER 4 LANGKAH (LANGKAH 1 s/d 4)
    function applyUnified4StepMasterSheetStyling(ws, rowCount, colCount, kibL3ColCount, titleRowCount = 5, hasSignature = false) {
        const thinBorder = {
            top: { style: "thin", color: { rgb: "64748B" } },
            bottom: { style: "thin", color: { rgb: "64748B" } },
            left: { style: "thin", color: { rgb: "64748B" } },
            right: { style: "thin", color: { rgb: "64748B" } }
        };

        const headerStartRow = titleRowCount;
        const headerEndRow = titleRowCount + 4;

        const l3Start = 15;
        const l3End = 15 + kibL3ColCount - 1;
        const l4Start = 15 + kibL3ColCount;
        const footerRowIdx = hasSignature ? (rowCount - 1 - 9) : (rowCount - 1);

        for (let r = 0; r < rowCount; r++) {
            if (hasSignature && r > footerRowIdx) {
                continue; // 9 baris tanda tangan ditangani khusus oleh applySignatureBlockStyling
            }
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

                // 1. BANNER JUDUL UTAMA (BARIS r < titleRowCount)
                if (r < titleRowCount) {
                    fill = "FFFFFF"; // White clean header background sesuai screenshot
                    fontColor = "000000";
                    bold = true;
                    fontSize = (r === 0 || r === 1) ? 12 : (r === 2 ? 11 : 10.5);
                    border = null;
                }
                // 2. HEADER TABEL 4 LANGKAH (BARIS r=headerStartRow s/d headerEndRow)
                else if (r >= headerStartRow && r <= headerEndRow) {
                    bold = true;
                    fontSize = r === headerStartRow ? 10.5 : (r === headerEndRow ? 9 : 9.5);
                    fontColor = "0F172A";

                    if (c === 0) {
                        fill = "D7E4BC"; // NO (Hijau Pastel)
                    }
                    // LANGKAH 1: PENGANGGARAN SIPD
                    else if (c >= 1 && c <= 2) {
                        fill = (r === headerStartRow) ? "D7E4BC" : "EAF1DD"; // Program (Hijau Pastel)
                    } else if (c >= 3 && c <= 4) {
                        fill = (r === headerStartRow) ? "FDE9D9" : "FFF2E8"; // Kegiatan (Peach / Soft Orange)
                    } else if (c >= 5 && c <= 6) {
                        fill = (r === headerStartRow) ? "E4DFEC" : "F2EEF8"; // Sub Kegiatan (Soft Lavender)
                    }
                    // LANGKAH 2: BELANJA MODAL & PMDN 108 (BLUE SOFT - SESUAI GAMBAR 1)
                    else if (c >= 7 && c <= 14) {
                        fill = (r === headerEndRow) ? "DBEAFE" : "BFDBFE"; // Kolom 8 s/d 15 Header Biru
                    }
                    // LANGKAH 3: RINCIAN KIB PER KATEGORI (SESUAI GAMBAR 2)
                    else if (c >= l3Start && c <= l3End) {
                        if (r === headerStartRow) {
                            fill = "D7E4BC"; // Top Banner Hijau Pastel
                        } else if (c === 15 || (c >= 28 && c <= 34 && colCount <= 49) || (c >= 36 && c <= 39)) {
                            fill = "D7E4BC"; // Sub headers Hijau Pastel (Nama Barang, Volume, Nilai Satuan, Admin Proyek)
                        } else {
                            fill = "FDE9D9"; // Sub headers Peach (Kode Barang, Spesifikasi, Riwayat Pembelian, SP2D, BAST, Ruang, dll)
                        }
                    }
                    // LANGKAH 4: REKANAN PENYEDIA & PPK (PEACH - SESUAI GAMBAR 3)
                    else if (c >= l4Start) {
                        fill = "FDE9D9"; // Peach header sesuai gambar 3
                    }
                }
                // 3. BARIS FOOTER / TOTAL
                else if (r === footerRowIdx) {
                    fill = "93C5FD"; // Soft Blue Background (sesuai gambar format baku)
                    fontColor = "0F172A";
                    bold = true;
                    fontSize = 10;
                    border = {
                        top: { style: "thin", color: { rgb: "0F172A" } },
                        bottom: { style: "double", color: { rgb: "0F172A" } },
                        left: { style: "thin", color: { rgb: "64748B" } },
                        right: { style: "thin", color: { rgb: "64748B" } }
                    };
                    if (typeof cell.v === 'number') {
                        align = "right";
                        if (cell.v >= 1000) {
                            numFmt = "Rp #,##0.00";
                        } else {
                            numFmt = "#,##0";
                        }
                    } else {
                        align = "center";
                    }
                }
                // 4. BARIS DATA BIASA
                else {
                    if (c >= 1 && c <= 2) {
                        fill = (r % 2 === 0) ? "F4F9EC" : "FFFFFF"; // Soft Green tint
                    } else if (c >= 3 && c <= 4) {
                        fill = (r % 2 === 0) ? "FFF7F2" : "FFFFFF"; // Soft Peach tint
                    } else if (c >= 5 && c <= 6) {
                        fill = (r % 2 === 0) ? "F8F6FC" : "FFFFFF"; // Soft Lavender tint
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

    function resolveItemCategory(item) {
        if (!item) return 'KIB B';
        const isExtracom = !!item.is_extracomtable || (item.category && item.category.toUpperCase() === 'EXTRACOM');
        if (isExtracom) return 'EXTRACOM';
        if (item.category) {
            const cUpper = item.category.toUpperCase().trim();
            if (cUpper === 'KIB A' || cUpper === 'A' || cUpper.includes('TANAH')) return 'KIB A';
            if (cUpper === 'KIB B' || cUpper === 'B' || cUpper.includes('MESIN') || cUpper.includes('PERALATAN')) return 'KIB B';
            if (cUpper === 'KIB C' || cUpper === 'C' || cUpper.includes('GEDUNG') || cUpper.includes('BANGUNAN')) return 'KIB C';
            if (cUpper === 'KIB D' || cUpper === 'D' || cUpper.includes('JALAN') || cUpper.includes('JARINGAN')) return 'KIB D';
            if (cUpper === 'KIB E' || cUpper === 'E' || cUpper.includes('LAINNYA')) return 'KIB E';
            if (cUpper === 'KIB F' || cUpper === 'F' || cUpper.includes('KDP') || cUpper.includes('KONSTRUKSI')) return 'KIB F';
            if (cUpper === 'ATB' || cUpper.includes('TIDAK BERWUJUD')) return 'ATB';
            if (cUpper === 'EXTRACOM') return 'EXTRACOM';
        }
        const kode = item.jenis_aset_kode || item.kode_barang || '';
        if (kode.startsWith('1.3.1')) return 'KIB A';
        if (kode.startsWith('1.3.2')) return 'KIB B';
        if (kode.startsWith('1.3.3')) return 'KIB C';
        if (kode.startsWith('1.3.4')) return 'KIB D';
        if (kode.startsWith('1.3.5')) return 'KIB E';
        if (kode.startsWith('1.3.6')) return 'KIB F';
        if (kode.startsWith('1.5.3')) return 'ATB';

        const rekKode = item.rekening_kode || '';
        const rekNama = (item.rekening_nama || '').toUpperCase();
        if (rekKode.startsWith('5.2.01.01') || rekNama.includes('TANAH')) return 'KIB A';
        if (rekKode.startsWith('5.2.01.02') || rekNama.includes('PERALATAN') || rekNama.includes('MESIN')) return 'KIB B';
        if (rekKode.startsWith('5.2.01.03') || rekNama.includes('GEDUNG') || rekNama.includes('BANGUNAN')) return 'KIB C';
        if (rekKode.startsWith('5.2.01.04') || rekNama.includes('JALAN') || rekNama.includes('JARINGAN') || rekNama.includes('IRIGASI')) return 'KIB D';
        if (rekKode.startsWith('5.2.01.05') || rekNama.includes('ASET TETAP LAINNYA')) return 'KIB E';
        if (rekKode.startsWith('5.2.01.06') || rekNama.includes('KONSTRUKSI') || rekNama.includes('KDP')) return 'KIB F';
        if (rekKode.startsWith('5.2.01.07') || rekKode.startsWith('5.2.05') || rekNama.includes('TIDAK BERWUJUD') || rekNama.includes('ATB')) return 'ATB';

        const nama = (item.nama_barang || '').toUpperCase();
        if (nama.includes('TANAH')) return 'KIB A';
        if (nama.includes('GEDUNG') || nama.includes('BANGUNAN')) return 'KIB C';
        if (nama.includes('JALAN') || nama.includes('IRIGASI') || nama.includes('JARINGAN')) return 'KIB D';
        if (nama.includes('GOODWILL') || nama.includes('LISENSI') || nama.includes('FRANCHISE') || nama.includes('FRENCHISE') || nama.includes('HAK CIPTA') || nama.includes('TIDAK BERWUJUD')) return 'ATB';
        if (nama.includes('PENGERJAAN') || nama.includes('KDP')) return 'KIB F';
        if (nama.includes('TRACTOR') || nama.includes('KENDARAAN') || nama.includes('MESIN') || nama.includes('ALAT') || nama.includes('AMBULANS')) return 'KIB B';

        return 'KIB B';
    }

    let isExportingAstap = false;
    function exportAstapToExcel(params = {}) {
        if (isExportingAstap) return;
        isExportingAstap = true;

        if (typeof XLSX === 'undefined') {
            alert('⚠️ Pustaka Excel sedang dimuat, silakan coba 1 detik lagi...');
            isExportingAstap = false;
            return;
        }

        const rawAstaps = window.__simatAstaps || [];
        const wb = XLSX.utils.book_new();

        const filterYear = params.year || 'all';
        const filterTw = params.triwulan || 'all';
        const filterCat = params.category || 'all';

        // Filter data berdasarkan Tahun, Triwulan, dan Klasifikasi KIB
        let filteredAstaps = rawAstaps.filter(item => {
            const matchYear = filterYear === 'all' || String(item.tahun_perolehan) === String(filterYear);
            
            let matchTw = true;
            if (filterTw !== 'all') {
                const targetKey = filterTw.replace(/[\s_]/g, '').toUpperCase(); // 'TWI', 'TW1', etc
                const itemTw = (item.triwulan || 'TWI').replace(/[\s_]/g, '').toUpperCase();
                matchTw = (itemTw === targetKey) ||
                          (targetKey === 'TWI' && itemTw === 'TW1') || (targetKey === 'TW1' && itemTw === 'TWI') ||
                          (targetKey === 'TWII' && itemTw === 'TW2') || (targetKey === 'TW2' && itemTw === 'TWII') ||
                          (targetKey === 'TWIII' && itemTw === 'TW3') || (targetKey === 'TW3' && itemTw === 'TWIII') ||
                          (targetKey === 'TWIV' && itemTw === 'TW4') || (targetKey === 'TW4' && itemTw === 'TWIV');
            }

            const itemCat = resolveItemCategory(item);
            const matchCategory = (filterCat === 'all' || filterCat === 'REKAP') || (itemCat === filterCat);
            return matchYear && matchTw && matchCategory;
        });

        // Kelompokkan data per Kategori Aset (Dengan Dukungan Pemilahan Item KIB B vs EXTRACOM)
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

        filteredAstaps.forEach(item => {
            const cat = resolveItemCategory(item);
            if (categories[cat]) {
                categories[cat].push(item);
            } else {
                categories['KIB B'].push(item);
            }
        });

        // Label Dinamis untuk Header Laporan BAST
        let twLabel = "KESELURUHAN (TAHUNAN)";
        if (filterTw === 'TW I' || filterTw === 'TW1') twLabel = "TRIWULAN I (JANUARI - MARET)";
        else if (filterTw === 'TW II' || filterTw === 'TW2') twLabel = "TRIWULAN II (APRIL - JUNI)";
        else if (filterTw === 'TW III' || filterTw === 'TW3') twLabel = "TRIWULAN III (JULI - SEPTEMBER)";
        else if (filterTw === 'TW IV' || filterTw === 'TW4') twLabel = "TRIWULAN IV (OKTOBER - DESEMBER)";

        const yearLabel = filterYear === 'all' ? (new Date().getFullYear()) : filterYear;
        const bannerHeader = `REKAPITULASI REALISASI BELANJA MODAL ASET TETAP (${twLabel}) TAHUN ANGGARAN ${yearLabel}`;

        const ppkNama = (filteredAstaps.find(a => a.ppk_nama && a.ppk_nama !== '-') || {}).ppk_nama || "dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR";
        const ppkNip  = (filteredAstaps.find(a => a.ppk_nip && a.ppk_nip !== '-') || {}).ppk_nip || "19771002 200604 1 007";
        const signDate = getReportSignDate(filterTw, filterYear);

        // ------------------------------------------------------------------------
        // 1. REKAPITULASI DYNAMIS (LENGKAP 9 KOLOM: ANGGARAN, REALISASI, SELISIH, PERSENTASE & TANDA TANGAN)
        // ------------------------------------------------------------------------
        function getCategorySummary(kibKey, isMesin = false) {
            const items = categories[kibKey] || [];
            let totalReal = 0;
            let totalAngg = 0;
            let totalUnits = 0;

            const groups = {};
            items.forEach(it => {
                const groupKey = getAstapGroupKey(it, 'DEFAULT');
                if (!groups[groupKey]) groups[groupKey] = [];
                groups[groupKey].push(it);
            });

            Object.keys(groups).forEach(groupKey => {
                const groupItems = groups[groupKey];
                const groupReal = groupItems.reduce((acc, it) => acc + (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0), 0);
                let groupAngg = 0;
                groupItems.forEach(it => {
                    groupAngg += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0));
                });
                
                totalReal += groupReal;
                totalAngg += groupAngg;

                groupItems.forEach(it => {
                    let spec = it.spesifikasi_json;
                    if (typeof spec === 'string') {
                        try { spec = JSON.parse(spec); } catch (e) { spec = {}; }
                    }
                    if (isMesin && spec && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                        spec.mesin_items.forEach(mi => {
                            totalUnits += Math.max(1, parseInt(mi.mesin_jumlah_barang) || 1);
                        });
                    } else if (spec && Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) {
                        spec.gedung_items.forEach(gi => {
                            totalUnits += Math.max(1, parseInt(gi.gedung_jumlah_bangunan) || 1);
                        });
                    } else if (spec && Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) {
                        spec.tanah_items.forEach(ti => {
                            totalUnits += Math.max(1, parseInt(ti.tanah_jumlah_bidang) || 1);
                        });
                    } else {
                        totalUnits += parseInt(it.jumlah_volume) || parseInt(it.jumlah_unit) || 1;
                    }
                });
            });

            const selisih = Math.max(0, totalAngg - totalReal);
            const persen = totalAngg > 0 ? ((totalReal / totalAngg) * 100).toFixed(2) + '%' : (totalReal > 0 ? '100.00%' : '0.00%');

            return {
                totalReal,
                totalAngg,
                totalUnits,
                selisih,
                persen,
                itemCount: items.length
            };
        }

        const kibASummary = getCategorySummary('KIB A', false);
        const kibBSummary = getCategorySummary('KIB B', true);
        const kibCSummary = getCategorySummary('KIB C', false);
        const kibDSummary = getCategorySummary('KIB D', false);
        const kibESummary = getCategorySummary('KIB E', false);
        const kibFSummary = getCategorySummary('KIB F', false);
        const atbSummary  = getCategorySummary('ATB', false);
        const extSummary  = getCategorySummary('EXTRACOM', true);

        const grandTotalAnggaran  = kibASummary.totalAngg + kibBSummary.totalAngg + kibCSummary.totalAngg + kibDSummary.totalAngg + kibESummary.totalAngg + kibFSummary.totalAngg + atbSummary.totalAngg + extSummary.totalAngg;
        const grandTotalRealisasi = kibASummary.totalReal + kibBSummary.totalReal + kibCSummary.totalReal + kibDSummary.totalReal + kibESummary.totalReal + kibFSummary.totalReal + atbSummary.totalReal + extSummary.totalReal;
        const grandTotalUnits     = kibASummary.totalUnits + kibBSummary.totalUnits + kibCSummary.totalUnits + kibDSummary.totalUnits + kibESummary.totalUnits + kibFSummary.totalUnits + atbSummary.totalUnits + extSummary.totalUnits;
        const grandTotalSelisih   = Math.max(0, grandTotalAnggaran - grandTotalRealisasi);
        const grandTotalPersen    = grandTotalAnggaran > 0 ? ((grandTotalRealisasi / grandTotalAnggaran) * 100).toFixed(2) + '%' : (grandTotalRealisasi > 0 ? '100.00%' : '0.00%');

        const bannerHeaderTw = filterTw === 'all' 
            ? ("KESELURUHAN (TAHUNAN) TAHUN ANGGARAN " + yearLabel)
            : (twLabel + " TAHUN ANGGARAN " + yearLabel);

        const rekapData = [
            // r0: Judul Laporan Baris 1
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            // r1: Judul Laporan Baris 2
            ["RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI"],
            // r2: Judul Laporan Baris 3
            ["REKAPITULASI REALISASI BELANJA MODAL ASET TETAP TAHUN ANGGARAN " + yearLabel],
            // r3: Judul Laporan Baris 4 (Periode)
            [bannerHeaderTw],
            // r4: Baris Kosong Pemisah
            [""],
            // r5: Header Tabel (9 Kolom)
            [
                "NO",
                "KELOMPOK / KATEGORI ASET",
                "KODE REKENING BELANJA",
                "JUMLAH ITEM / UNIT",
                "JUMLAH ANGGARAN (Rp)",
                "JUMLAH REALISASI (Rp)",
                "SELISIH / SISA (Rp)",
                "PERSENTASE",
                "KETERANGAN"
            ],
            // r6: KIB A
            [
                "1", "2. A - TANAH (KIB A)", "5.2.02.01.01.0001",
                kibASummary.totalUnits + " Bidang", kibASummary.totalAngg, kibASummary.totalReal, kibASummary.selisih, kibASummary.persen,
                "Lahan RSUD Hak Pakai BPN"
            ],
            // r7: KIB B
            [
                "2", "3. B - PERALATAN DAN MESIN (KIB B >= Rp 300.000)", "5.2.02.02.01.0005",
                kibBSummary.totalUnits + " Unit", kibBSummary.totalAngg, kibBSummary.totalReal, kibBSummary.selisih, kibBSummary.persen,
                "Alat Kedokteran, Komputer & Mesin"
            ],
            // r8: KIB C
            [
                "3", "4. C - GEDUNG DAN BANGUNAN (KIB C)", "5.2.02.03.01.0008",
                kibCSummary.totalUnits + " Bangunan", kibCSummary.totalAngg, kibCSummary.totalReal, kibCSummary.selisih, kibCSummary.persen,
                "Gedung Rawat Inap & Fasilitas Medis"
            ],
            // r9: KIB D
            [
                "4", "5. D - JALAN, IRIGASI DAN JARINGAN (KIB D)", "5.2.02.04.01.0004",
                kibDSummary.totalUnits + " Ruas / Jaringan", kibDSummary.totalAngg, kibDSummary.totalReal, kibDSummary.selisih, kibDSummary.persen,
                "Jaringan Pipa, IPAL & Instalasi Listrik"
            ],
            // r10: KIB E
            [
                "5", "6. E - ASET TETAP LAINNYA (KIB E)", "5.2.02.05.01.0002",
                kibESummary.totalUnits + " Unit / Buku", kibESummary.totalAngg, kibESummary.totalReal, kibESummary.selisih, kibESummary.persen,
                "Buku Medis, Koleksi & Seni Budaya"
            ],
            // r11: KIB F
            [
                "6", "7. F - KONSTRUKSI DALAM PENGERJAAN (KIB F)", "5.2.02.06.01.0001",
                kibFSummary.totalUnits + " Bangunan", kibFSummary.totalAngg, kibFSummary.totalReal, kibFSummary.selisih, kibFSummary.persen,
                "Proyek Fisik Konstruksi KDP"
            ],
            // r12: ATB
            [
                "7", "8. ATB - ASET TIDAK BERWUJUD (ATB / 1.5.3)", "5.2.02.08.01.0005",
                atbSummary.totalUnits + " Lisensi / Item", atbSummary.totalAngg, atbSummary.totalReal, atbSummary.selisih, atbSummary.persen,
                "Software SIMRS, Aplikasi & Lisensi"
            ],
            // r13: EXTRACOM
            [
                "8", "9. EXTRACOM - EKSTRAKOMTABEL (< Rp 300.000)", "5.2.02.02.01.0099",
                extSummary.totalUnits + " Barang", extSummary.totalAngg, extSummary.totalReal, extSummary.selisih, extSummary.persen,
                "Peralatan & Barang Kecil < Rp 300.000"
            ],
            // r14: Footer Total Row (JUMLAH)
            [
                "JUMLAH TOTAL REALISASI BELANJA MODAL", "", "",
                grandTotalUnits + " Item/Unit Total",
                grandTotalAnggaran,
                grandTotalRealisasi,
                grandTotalSelisih,
                grandTotalPersen,
                "Laporan Realisasi " + twLabel + " " + yearLabel
            ],
        ];

        // Tanda Tangan Rekapitulasi (Format Baku RSUD Koesnadi)
        const rekapSignStartRow = rekapData.length;
        const rekapSignRows = buildKibSignatureRows(9, 5, ppkNama, ppkNip, signDate, 1);
        rekapSignRows.forEach(r => rekapData.push(r));

        const wsRekap = XLSX.utils.aoa_to_sheet(rekapData);

        // Lebar Kolom yang Proporsional & Rapi (9 Kolom)
        wsRekap['!cols'] = [
            {wch: 6},   // c0: NO
            {wch: 48},  // c1: KELOMPOK / KATEGORI ASET
            {wch: 24},  // c2: KODE REKENING BELANJA
            {wch: 22},  // c3: JUMLAH ITEM / UNIT
            {wch: 25},  // c4: JUMLAH ANGGARAN (Rp)
            {wch: 25},  // c5: JUMLAH REALISASI (Rp)
            {wch: 22},  // c6: SELISIH / SISA (Rp)
            {wch: 16},  // c7: PERSENTASE (%)
            {wch: 40}   // c8: KETERANGAN
        ];

        // Merge Cells Rekapitulasi (Title, Footer, & Tanda Tangan Lurus Presisi)
        wsRekap['!merges'] = [
            // Title Banners (r0 - r3, c0 s/d c8)
            { s: { r: 0, c: 0 }, e: { r: 0, c: 8 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 8 } },
            { s: { r: 2, c: 0 }, e: { r: 2, c: 8 } },
            { s: { r: 3, c: 0 }, e: { r: 3, c: 8 } },

            // Footer Total Banner (r14: c0 s/d c2)
            { s: { r: 14, c: 0 }, e: { r: 14, c: 2 } },

            // Tanda Tangan Rekapitulasi (c1 s/d c3 kiri, c5 s/d c8 kanan)
            ...getKibSignatureMerges(rekapSignStartRow, 9, 5, 1, 3, 8)
        ];

        applyRekapSheetStyling(wsRekap, rekapData.length, 9, 5, 14, rekapSignStartRow);
        applySignatureBlockStyling(wsRekap, rekapSignStartRow, 9);
        if (filterCat === 'all' || filterCat === 'REKAP') {
            XLSX.utils.book_append_sheet(wb, wsRekap, filterCat === 'all' ? "1. Rekapitulasi" : "Rekapitulasi Realisasi");
        }

        // HELPER FUNGSI UNTUK MENGAMBIL DATA LANGKAH 1 & LANGKAH 2 (KOLOM 1-15)
        function getCommonColumns(item, idx) {
            const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
            const anggaranVal = typeof item.jumlah_anggaran === 'number' ? item.jumlah_anggaran : (parseFloat(item.jumlah_anggaran) || totalVal);
            return [
                idx + 1,
                item.program_kode || '-',
                item.program_nama || '-',
                item.kegiatan_kode || '-',
                item.kegiatan_nama || '-',
                item.sub_kegiatan_kode || '-',
                item.sub_kegiatan_nama || '-',
                item.rekening_kode || '-',
                item.rekening_nama || '-',
                item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '-'),
                item.jenis_aset_nama || '-',
                item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : '-'),
                item.sub_rincian_nama || '-',
                anggaranVal,
                totalVal,
            ];
        }

        // HELPER FUNGSI UNTUK MENGAMBIL DATA LANGKAH 4 (REKANAN PENYEDIA & PPK)
        function getStep4Columns(item, isSpecial = false) {
            let spec = item.spesifikasi_json;
            if (typeof spec === 'string') {
                try { spec = JSON.parse(spec); } catch (e) { spec = {}; }
            }
            const noHpWa = item.penyedia_telepon || (spec ? spec.penyedia_telepon : '') || item.penyedia_kontak || (spec ? spec.penyedia_kontak : '') || item.telepon || item.no_hp || '-';

            if (isSpecial) {
                return [
                    item.penyedia_nama || '-',
                    item.penyedia_pemilik || '-',
                    noHpWa,
                    item.penyedia_rekening_nama || (item.penyedia_nama || '-'),
                    item.penyedia_rekening_nomor || '-',
                    item.penyedia_alamat || '-',
                    item.ppk_nama || '-',
                    item.ppk_nip || '-',
                    item.keterangan_tambahan || item.keterangan || '-'
                ];
            }

            return [
                item.penyedia_nama || '-',
                item.penyedia_pemilik || '-',
                item.penyedia_rekening_nama || (item.penyedia_nama || '-'),
                item.penyedia_rekening_nomor || '-',
                item.penyedia_alamat || '-',
                item.ppk_nama || '-',
                item.ppk_nip || '-',
                item.keterangan_tambahan || item.keterangan || '-'
            ];
        }

        // HELPER FUNGSI UNTUK MENGAMBIL NIBAR DARI ASTAP REGISTERS (DENGAN FALLBACK)
        function getAstapNibar(item, subItem = null, idx = 0) {
            let spec = item ? item.spesifikasi_json : null;
            if (typeof spec === 'string') {
                try { spec = JSON.parse(spec); } catch (e) { spec = {}; }
            }

            // 1. Cek subItem registers jika ada
            if (subItem && subItem.registers && Array.isArray(subItem.registers) && subItem.registers.length > 0) {
                const list = subItem.registers.map(r => r.nibar || r.no_register).filter(Boolean);
                if (list.length > 0) return list.join(', ');
            }

            // 2. Cek item.registers (dari relasi astaps register)
            if (item && item.registers && Array.isArray(item.registers) && item.registers.length > 0) {
                if (subItem && item.registers[idx] && (item.registers[idx].nibar || item.registers[idx].no_register)) {
                    return item.registers[idx].nibar || item.registers[idx].no_register;
                }
                const list = item.registers.map(r => r.nibar || r.no_register).filter(Boolean);
                if (list.length > 0) return list.join(', ');
            }

            // 3. Fallback ke property kapitalisasi_nibar / nibar
            return (subItem && (subItem.gedung_kapitalisasi_nibar || subItem.jaringan_kapitalisasi_nibar || subItem.nibar))
                || (item && (item.gedung_kapitalisasi_nibar || item.jaringan_kapitalisasi_nibar || (spec ? spec.kapitalisasi_nibar : null) || item.nibar))
                || '-';
        }

        // ------------------------------------------------------------------------
        // 2. KIB A (TANAH) - COMPLETE 4-STEP MASTER SHEET (49 KOLOM SESUAI FORMAT BAKU)
        // ------------------------------------------------------------------------
        const kibATitleRows = getKibTitleRows("TANAH", yearLabel, filterTw);
        const kibARows = [
            ...kibATitleRows,
            [
                "NO",
                "Program Pengadaan SIPD", "",
                "Kegiatan Pengadaan SIPD", "",
                "Sub Kegiatan Pengadaan SIPD", "",
                "BELANJA MODAL", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE / " + yearLabel, "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "Letak / Alamat Barang",
                "PIHAK PENYEDIA", "", "", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            [
                "",
                "", "",
                "", "",
                "", "",
                "Rekening Belanja Untuk Pengadaan SIPD", "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH ANGGARAN (Rp)",
                "JUMLAH REALISASI (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "Status Tanah", "", "",
                "Riwayat Pembelian", "", "", "", "", "", "", "",
                "Kondisi\n(B/KB/RB)",
                "Penggunaan",
                "VOLUME", "",
                "Nilai Barang (Rp)", "", "",
                "Total Nilai Barang (Rp)",
                "SP2D", "",
                "BAST pada SPK/Surat Pesanan/Kwitansi/Invoice", "",
                "",
                "", "", "", "", "",
                "", "",
                ""
            ],
            [
                "",
                "Kode", "Nama Program",
                "Kode", "Nama Kegiatan Pengadaan",
                "Kode", "Nama Sub Kegiatan Pengadaan",
                "Kode Rek", "Nama Belanja Pengadaan",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "",
                "",
                "",
                "",
                "Hak Tanah\n(Hak Pakai / Hak Pengelolaan)", "Sertifikat", "",
                "SPK", "", "Surat Pesanan", "", "Kwitansi", "", "Invoice (Tanggal dan Nomor)", "",
                "",
                "",
                "Jumlah Bidang Tanah", "Luas Tanah (m²)",
                "Nilai Perencanaan (Rp)", "Nilai Fisik (Rp)", "Nilai Pengawasan",
                "36 = 33+34+35",
                "", "",
                "", "",
                "",
                "Nama Penyedia", "Pemilik Penyedia", "Rekening", "", "Alamat Penyedia",
                "Nama", "NIP",
                ""
            ],
            [
                "",
                "", "",
                "", "",
                "", "",
                "", "",
                "", "",
                "", "",
                "",
                "",
                "",
                "",
                "", "Tanggal", "Nomor",
                "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal",
                "",
                "",
                "", "",
                "", "", "",
                "",
                "NOMOR", "TANGGAL",
                "NOMOR", "TANGGAL",
                "",
                "", "", "Nama Rek", "Nomor Rek", "",
                "", "",
                ""
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10",
                "11", "12", "13", "14", "15", "16", "17", "18", "19", "20",
                "21", "22", "23", "24", "25", "26", "27", "28", "29", "30",
                "31", "32", "33", "34", "35", "36", "37", "38", "39", "40",
                "41", "42", "43", "44", "45", "46", "47", "48", "49"
            ]
        ];

        // ------------------------------------------------------------------------
        // 2. KIB A (TANAH) - HIERARCHICAL GROUPING SESUAI SUB RINCIAN OBJEK (PMDN 108)
        // ------------------------------------------------------------------------
        const kibAGroups = {};
        categories['KIB A'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.3.1.01.01.01');
            if (!kibAGroups[groupKey]) {
                kibAGroups[groupKey] = [];
            }
            kibAGroups[groupKey].push(item);
        });

        let globalKibANo = 1;
        let kibATotalAnggaran = 0, kibATotalRealisasi = 0, kibATotalUnit = 0, kibATotalLuas = 0;
        let kibATotalPerencanaan = 0, kibATotalFisik = 0, kibATotalPengawasan = 0, kibATotalNilaiBarang = 0;

        Object.keys(kibAGroups).forEach(groupKey => {
            const groupItems = kibAGroups[groupKey];
            const groupRealisasiTotal = groupItems.reduce((acc, it) => acc + (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0), 0);
            let groupAnggaranTotal = 0;
            groupItems.forEach(it => {
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0));
            });

            kibATotalAnggaran += groupAnggaranTotal;
            kibATotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                let spec = item.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch (e) { spec = {}; }
                }

                const tanahItems = (spec && Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0)
                    ? spec.tanah_items
                    : null;

                if (tanahItems) {
                    tanahItems.forEach((tItem) => {
                        const nilaiPerencanaan = parseFloat(tItem.tanah_nilai_perencanaan) || 0;
                        const nilaiFisik = parseFloat(tItem.tanah_nilai_fisik) || 0;
                        const nilaiPengawasan = parseFloat(tItem.tanah_nilai_pengawasan) || 0;
                        const totalNilaiBidang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan) || 0;
                        const jumlahBidang = parseInt(tItem.tanah_jumlah_bidang) || 1;
                        const luasM2 = parseFloat(tItem.tanah_luas_m2) || 0;

                        kibATotalUnit += jumlahBidang;
                        kibATotalLuas += luasM2;
                        kibATotalPerencanaan += nilaiPerencanaan;
                        kibATotalFisik += nilaiFisik;
                        kibATotalPengawasan += nilaiPengawasan;
                        kibATotalNilaiBarang += totalNilaiBidang;

                        const rawKondisi = tItem.tanah_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = rawKondisi === 'B' || rawKondisi === 'Baik' ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                        let col1to15 = [];
                        if (isFirstRowInGroup) {
                            // Baris Pertama Group Sub Rincian: Isi Lengkap Kolom 1 s/d 15
                            col1to15 = [
                                globalKibANo++,
                                item.program_kode || '-',
                                item.program_nama || '-',
                                item.kegiatan_kode || '-',
                                item.kegiatan_nama || '-',
                                item.sub_kegiatan_kode || '-',
                                item.sub_kegiatan_nama || '-',
                                item.rekening_kode || '-',
                                item.rekening_nama || '-',
                                item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.1'),
                                item.jenis_aset_nama || 'TANAH',
                                item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : '-'),
                                item.sub_rincian_nama || '-',
                                groupAnggaranTotal,
                                groupRealisasiTotal
                            ];
                            isFirstRowInGroup = false;
                        } else {
                            // Baris Anak (Sub-Sub Rincian / Bidang ke-2 dst): Kolom 1 s/d 15 Dikosongkan (Blank)
                            col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                        }

                        kibARows.push([
                            ...col1to15,
                            item.nama_barang || '-',
                            item.kode_barang || '-',
                            tItem.tanah_hak || item.hak_tanah || 'Hak Pakai',
                            tItem.tanah_sertifikat_tgl ? formatAstapDate(tItem.tanah_sertifikat_tgl) : (item.sertifikat_tanggal ? formatAstapDate(item.sertifikat_tanggal) : '-'),
                            tItem.tanah_sertifikat_no || item.sertifikat_nomor || '-',
                            item.spk_nomor || '-',
                            formatAstapDate(item.spk_tanggal),
                            item.surat_pesanan_nomor || '-',
                            formatAstapDate(item.surat_pesanan_tanggal),
                            item.kwitansi_nomor || '-',
                            formatAstapDate(item.kwitansi_tanggal),
                            item.faktur_nomor || '-',
                            formatAstapDate(item.faktur_tanggal),
                            kondisiLabel,
                            tItem.tanah_penggunaan || item.penggunaan || 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                            jumlahBidang,
                            luasM2,
                            nilaiPerencanaan,
                            nilaiFisik,
                            nilaiPengawasan,
                            totalNilaiBidang,
                            item.sp2d_nomor || '-',
                            formatAstapDate(item.sp2d_tanggal),
                            item.bast_dokumen_nomor || '-',
                            formatAstapDate(item.bast_dokumen_tanggal),
                            tItem.tanah_alamat || item.alamat_barang || '-',
                            ...getStep4Columns(item)
                        ]);
                    });
                } else {
                    // Fallback Single Item jika data belum memakai tanah_items array
                    const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                    const nilaiPerencanaan = parseFloat(item.nilai_perencanaan) || 0;
                    const nilaiFisik = parseFloat(item.nilai_fisik) || totalVal;
                    const nilaiPengawasan = parseFloat(item.nilai_pengawasan) || 0;
                    const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan) || totalVal;
                    const jumlahBidang = item.jumlah_bidang || (item.jumlah_volume || 1);
                    const luasM2 = parseFloat(item.luas_m2) || 0;

                    kibATotalUnit += jumlahBidang;
                    kibATotalLuas += luasM2;
                    kibATotalPerencanaan += nilaiPerencanaan;
                    kibATotalFisik += nilaiFisik;
                    kibATotalPengawasan += nilaiPengawasan;
                    kibATotalNilaiBarang += totalNilaiBarang;

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = rawKondisi === 'B' || rawKondisi === 'Baik' ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    let col1to15 = [];
                    if (isFirstRowInGroup) {
                        // Baris Pertama Group Sub Rincian: Isi Lengkap Kolom 1 s/d 15
                        col1to15 = [
                            globalKibANo++,
                            item.program_kode || '-',
                            item.program_nama || '-',
                            item.kegiatan_kode || '-',
                            item.kegiatan_nama || '-',
                            item.sub_kegiatan_kode || '-',
                            item.sub_kegiatan_nama || '-',
                            item.rekening_kode || '-',
                            item.rekening_nama || '-',
                            item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.1'),
                            item.jenis_aset_nama || 'TANAH',
                            item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : '-'),
                            item.sub_rincian_nama || '-',
                            groupAnggaranTotal,
                            groupRealisasiTotal
                        ];
                        isFirstRowInGroup = false;
                    } else {
                        // Baris Anak: Kolom 1 s/d 15 Dikosongkan (Blank)
                        col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                    }

                    kibARows.push([
                        ...col1to15,
                        item.nama_barang || '-',
                        item.kode_barang || '-',
                        item.hak_tanah || 'Hak Pakai',
                        formatAstapDate(item.sertifikat_tanggal),
                        item.sertifikat_nomor || '-',
                        item.spk_nomor || '-',
                        formatAstapDate(item.spk_tanggal),
                        item.surat_pesanan_nomor || '-',
                        formatAstapDate(item.surat_pesanan_tanggal),
                        item.kwitansi_nomor || '-',
                        formatAstapDate(item.kwitansi_tanggal),
                        item.faktur_nomor || '-',
                        formatAstapDate(item.faktur_tanggal),
                        kondisiLabel,
                        item.penggunaan || 'Bangunan Rumah Sakit & Fasilitas Kesehatan',
                        jumlahBidang,
                        luasM2,
                        nilaiPerencanaan,
                        nilaiFisik,
                        nilaiPengawasan,
                        totalNilaiBarang,
                        item.sp2d_nomor || '-',
                        formatAstapDate(item.sp2d_tanggal),
                        item.bast_dokumen_nomor || '-',
                        formatAstapDate(item.bast_dokumen_tanggal),
                        item.alamat_barang || '-',
                        ...getStep4Columns(item)
                    ]);
                }
            });
        });

        // ── Baris Footer Total KIB A (49 Kolom) ──────────────────────────────────
        const kibAFooterRow = Array(49).fill("");
        kibAFooterRow[0] = "JUMLAH";
        kibAFooterRow[13] = kibATotalAnggaran;
        kibAFooterRow[14] = kibATotalRealisasi;
        kibAFooterRow[30] = kibATotalUnit;
        kibAFooterRow[31] = kibATotalLuas;
        kibAFooterRow[32] = kibATotalPerencanaan;
        kibAFooterRow[33] = kibATotalFisik;
        kibAFooterRow[34] = kibATotalPengawasan;
        kibAFooterRow[35] = kibATotalNilaiBarang;
        kibARows.push(kibAFooterRow);

        // Tanda Tangan KIB A (Format Baku RSUD Koesnadi)
        const kibASignStartRow = kibARows.length;
        const kibASignRows = buildKibSignatureRows(49, 35, ppkNama, ppkNip, signDate, 1);
        kibASignRows.forEach(r => kibARows.push(r));

        const wsKibA = XLSX.utils.aoa_to_sheet(kibARows);
        wsKibA['!cols'] = Array(49).fill({wch: 18});
        wsKibA['!cols'][0] = {wch: 6};
        wsKibA['!cols'][1] = {wch: 14}; wsKibA['!cols'][2] = {wch: 32};
        wsKibA['!cols'][3] = {wch: 14}; wsKibA['!cols'][4] = {wch: 28};
        wsKibA['!cols'][5] = {wch: 18}; wsKibA['!cols'][6] = {wch: 30};
        wsKibA['!cols'][7] = {wch: 18}; wsKibA['!cols'][8] = {wch: 30};
        wsKibA['!cols'][9] = {wch: 14}; wsKibA['!cols'][10] = {wch: 24};
        wsKibA['!cols'][11] = {wch: 18}; wsKibA['!cols'][12] = {wch: 32};
        wsKibA['!cols'][13] = {wch: 22}; wsKibA['!cols'][14] = {wch: 22};
        wsKibA['!cols'][15] = {wch: 32}; wsKibA['!cols'][16] = {wch: 22};
        wsKibA['!cols'][17] = {wch: 38}; // Kolom 18: Hak Tanah diperlebar agar rapi
        wsKibA['!cols'][18] = {wch: 14}; wsKibA['!cols'][19] = {wch: 16};
        wsKibA['!cols'][20] = {wch: 16}; wsKibA['!cols'][21] = {wch: 14};
        wsKibA['!cols'][22] = {wch: 16}; wsKibA['!cols'][23] = {wch: 14};
        wsKibA['!cols'][24] = {wch: 16}; wsKibA['!cols'][25] = {wch: 14};
        wsKibA['!cols'][26] = {wch: 16}; wsKibA['!cols'][27] = {wch: 14};
        wsKibA['!cols'][28] = {wch: 14}; wsKibA['!cols'][29] = {wch: 26};
        wsKibA['!cols'][30] = {wch: 18}; wsKibA['!cols'][31] = {wch: 16};
        wsKibA['!cols'][32] = {wch: 20}; wsKibA['!cols'][33] = {wch: 20};
        wsKibA['!cols'][34] = {wch: 20}; wsKibA['!cols'][35] = {wch: 22};
        wsKibA['!cols'][36] = {wch: 18}; wsKibA['!cols'][37] = {wch: 14};
        wsKibA['!cols'][38] = {wch: 22}; wsKibA['!cols'][39] = {wch: 14};
        wsKibA['!cols'][40] = {wch: 32}; wsKibA['!cols'][41] = {wch: 28};
        wsKibA['!cols'][42] = {wch: 24}; wsKibA['!cols'][43] = {wch: 24};
        wsKibA['!cols'][44] = {wch: 24}; wsKibA['!cols'][45] = {wch: 30};
        wsKibA['!cols'][46] = {wch: 24}; wsKibA['!cols'][47] = {wch: 22};
        wsKibA['!cols'][48] = {wch: 26};

        // ── Merge Cells KIB A (Persis Format 49 Kolom Sesuai Gambar) ──────────────
        wsKibA['!merges'] = getKibMerges([
            // Column 1: NO (r3 to r6, c0)
            {s:{r:3,c:0}, e:{r:6,c:0}},

            // Column 2-3: Program Pengadaan SIPD
            {s:{r:3,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}}, // Kode
            {s:{r:5,c:2}, e:{r:6,c:2}}, // Nama Program

            // Column 4-5: Kegiatan Pengadaan SIPD
            {s:{r:3,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}}, // Kode
            {s:{r:5,c:4}, e:{r:6,c:4}}, // Nama Kegiatan Pengadaan

            // Column 6-7: Sub Kegiatan Pengadaan SIPD
            {s:{r:3,c:5}, e:{r:4,c:6}},
            {s:{r:5,c:5}, e:{r:6,c:5}}, // Kode
            {s:{r:5,c:6}, e:{r:6,c:6}}, // Nama Sub Kegiatan Pengadaan

            // Column 8-15: BELANJA MODAL (Top Banner r3, c7 to c14)
            {s:{r:3,c:7}, e:{r:3,c:14}},
            // Col 8-9: Rekening Belanja Untuk Pengadaan SIPD
            {s:{r:4,c:7}, e:{r:4,c:8}},
            {s:{r:5,c:7}, e:{r:6,c:7}}, // Kode Rek
            {s:{r:5,c:8}, e:{r:6,c:8}}, // Nama Belanja Pengadaan
            // Col 10-11: Jenis Aset (PMDN 108)
            {s:{r:4,c:9}, e:{r:4,c:10}},
            {s:{r:5,c:9}, e:{r:6,c:9}}, // Kode
            {s:{r:5,c:10}, e:{r:6,c:10}}, // Nama Jenis Aset
            // Col 12-13: Sub Rincian Objek (PMDN 108)
            {s:{r:4,c:11}, e:{r:4,c:12}},
            {s:{r:5,c:11}, e:{r:6,c:11}}, // Kode
            {s:{r:5,c:12}, e:{r:6,c:12}}, // Nama Uraian Sub Rincian Objek
            // Col 14: JUMLAH ANGGARAN (Rp)
            {s:{r:4,c:13}, e:{r:6,c:13}},
            // Col 15: JUMLAH REALISASI (Rp)
            {s:{r:4,c:14}, e:{r:6,c:14}},

            // Column 16-40: RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE / 2026 (r3, c15 to c39)
            {s:{r:3,c:15}, e:{r:3,c:39}},
            // Col 16: NAMA BARANG
            {s:{r:4,c:15}, e:{r:6,c:15}},
            // Col 17: Kode Barang
            {s:{r:4,c:16}, e:{r:6,c:16}},
            // Col 18-20: Status Tanah
            {s:{r:4,c:17}, e:{r:4,c:19}},
            {s:{r:5,c:17}, e:{r:6,c:17}}, // Hak Tanah (Hak Pakai / Hak Pengelolaan)
            {s:{r:5,c:18}, e:{r:5,c:19}}, // Sertifikat (r5) -> r6: Tanggal (c18), Nomor (c19)
            // Col 21-28: Riwayat Pembelian
            {s:{r:4,c:20}, e:{r:4,c:27}},
            {s:{r:5,c:20}, e:{r:5,c:21}}, // SPK -> r6: Nomor (c20), Tanggal (c21)
            {s:{r:5,c:22}, e:{r:5,c:23}}, // Surat Pesanan -> r6: Nomor (c22), Tanggal (c23)
            {s:{r:5,c:24}, e:{r:5,c:25}}, // Kwitansi -> r6: Nomor (c24), Tanggal (c25)
            {s:{r:5,c:26}, e:{r:5,c:27}}, // Invoice (Tanggal dan Nomor) -> r6: Nomor (c26), Tanggal (c27)
            // Col 29: Kondisi (B/KB/RB)
            {s:{r:4,c:28}, e:{r:6,c:28}},
            // Col 30: Penggunaan
            {s:{r:4,c:29}, e:{r:6,c:29}},
            // Col 31-32: VOLUME
            {s:{r:4,c:30}, e:{r:4,c:31}},
            {s:{r:5,c:30}, e:{r:6,c:30}}, // Jumlah Bidang Tanah
            {s:{r:5,c:31}, e:{r:6,c:31}}, // Luas Tanah (m²)
            // Col 33-35: Nilai Barang (Rp)
            {s:{r:4,c:32}, e:{r:4,c:34}},
            {s:{r:5,c:32}, e:{r:6,c:32}}, // Nilai Perencanaan (Rp)
            {s:{r:5,c:33}, e:{r:6,c:33}}, // Nilai Fisik (Rp)
            {s:{r:5,c:34}, e:{r:6,c:34}}, // Nilai Pengawasan
            // Col 36: Total Nilai Barang (Rp)
            {s:{r:4,c:35}, e:{r:4,c:35}},
            {s:{r:5,c:35}, e:{r:6,c:35}}, // 36 = 33+34+35
            // Col 37-38: SP2D
            {s:{r:4,c:36}, e:{r:5,c:37}}, // SP2D spans r4-r5 -> r6: NOMOR (c36), TANGGAL (c37)
            // Col 39-40: BAST pada SPK/Surat Pesanan/Kwitansi/Invoice
            {s:{r:4,c:38}, e:{r:5,c:39}}, // BAST spans r4-r5 -> r6: NOMOR (c38), TANGGAL (c39)

            // Column 41: LETAK / ALAMAT BARANG (Berdiri sendiri r3 sampai r6, c40)
            {s:{r:3,c:40}, e:{r:6,c:40}},

            // Column 42-46: PIHAK PENYEDIA (Top Banner r3 s/d r4, c41 to c45)
            {s:{r:3,c:41}, e:{r:4,c:45}},
            {s:{r:5,c:41}, e:{r:6,c:41}}, // Nama Penyedia
            {s:{r:5,c:42}, e:{r:6,c:42}}, // Pemilik Penyedia
            {s:{r:5,c:43}, e:{r:5,c:44}}, // Rekening -> r6: Nama Rek (c43), Nomor Rek (c44)
            {s:{r:5,c:45}, e:{r:6,c:45}}, // Alamat Penyedia

            // Column 47-48: Pejabat Pembuat Komitmen (Top Banner r3 s/d r4, c46 to c47)
            {s:{r:3,c:46}, e:{r:4,c:47}},
            {s:{r:5,c:46}, e:{r:6,c:46}}, // Nama
            {s:{r:5,c:47}, e:{r:6,c:47}}, // NIP

            // Column 49: KET. (Berdiri sendiri r3 sampai r6, c48)
            {s:{r:3,c:48}, e:{r:6,c:48}}
        ], 49, kibATitleRows.length, kibARows.length, true);
        wsKibA['!merges'].push(...getKibSignatureMerges(kibASignStartRow, 49, 35, 1, 6, 48));
        // ─────────────────────────────────────────────────────────────────────────
        applyUnified4StepMasterSheetStyling(wsKibA, kibARows.length, 49, 25, kibATitleRows.length, true);
        applySignatureBlockStyling(wsKibA, kibASignStartRow, 49);
        if (filterCat === 'all' || filterCat === 'KIB A') {
            XLSX.utils.book_append_sheet(wb, wsKibA, filterCat === 'all' ? "2. A" : "KIB A - Tanah");
        }

        // ------------------------------------------------------------------------
        // ------------------------------------------------------------------------
        // 3. KIB B (PERALATAN DAN MESIN) - COMPLETE 4-STEP MASTER SHEET (54 KOLOM)
        // Sesuai Format Baku: 1-15 (Langkah 1-2), 16-46 (Langkah 3), 47-54 (Langkah 4)
        // ------------------------------------------------------------------------
        const kibBTitleRows = getKibTitleRows("PERALATAN DAN MESIN", yearLabel, filterTw);
        const kibBRows = [
            ...kibBTitleRows,
            // r3: Main Banner (54 kolom)
            [
                "NO",
                "Program Pengadaan SIPD", "",
                "Kegiatan Pengadaan SIPD", "",
                "Sub Kegiatan Pengadaan SIPD", "",
                "BELANJA MODAL", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE / 2026",
                "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "",
                "RUANG /\nPEMEGANG",
                "PIHAK PENYEDIA", "", "", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            // r4: Sub-Banner Level 1 (54 kolom)
            [
                "",
                "", "",
                "", "",
                "", "",
                "Rekening Belanja Untuk Pengadaan SIPD", "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH ANGGARAN (Rp)",
                "JUMLAH REALISASI (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "Merk", "Type", "Ukuran / CC",
                "No. Pabrik", "No. Rangka", "No. Mesin", "No. BTKB", "No. POLISI",
                "BAHAN", "Tahun Perolehan",
                "Riwayat Pembelian", "", "", "", "", "", "", "",
                "Kondisi\n(B,KB,RB)",
                "VOLUME", "",
                "Nilai Satuan Barang (Rp)",
                "ADMINISTRASI PROYEK (Rp)",
                "Total Nilai Barang\n(Rp) = 39+40",
                "SP2D", "",
                "BAST pada SPK/Surat Pesanan/Kwitansi/Invoice", "",
                "",
                "", "", "", "", "",
                "", "",
                ""
            ],
            // r5: Sub-Banner Level 2 (54 kolom)
            [
                "",
                "Kode", "Nama Program",
                "Kode", "Nama Kegiatan",
                "Kode", "Nama Sub Kegiatan",
                "Kode Rek", "Nama Belanja Pengadaan",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "", "",
                "", "", "", "", "", "", "", "", "", "", "", "",
                "SPK", "", "Surat Pesanan", "", "Kwitansi", "", "Invoice", "",
                "",
                "Jumlah Barang", "Nama Satuan Barang",
                "", "", "",
                "", "",
                "", "",
                "",
                "Nama Penyedia", "Pemilik Penyedia", "Rekening", "", "Alamat Penyedia",
                "Nama", "NIP",
                ""
            ],
            // r6: Sub-Banner Level 3 / Nomor-Tanggal (54 kolom)
            [
                "",
                "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "", "", "",
                "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal",
                "", "", "", "", "", "",
                "NOMOR", "TANGGAL",
                "NOMOR", "TANGGAL",
                "",
                "", "", "Nama Rek", "No Rek", "",
                "", "",
                ""
            ],
            // r7: Nomor Kolom (54 kolom)
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27",
                "28", "29", "30", "31", "32", "33", "34", "35",
                "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46",
                "47", "48", "49", "50", "51", "52", "53", "54"
            ]
        ];

        // ── Kelompokkan Data KIB B per Sub Rincian Objek (PMDN 108) ───────────────
        const kibBGroups = {};
        categories['KIB B'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.3.2');
            if (!kibBGroups[groupKey]) {
                kibBGroups[groupKey] = [];
            }
            kibBGroups[groupKey].push(item);
        });

        let globalKibBNo = 1;
        let kibBTotalAnggaran = 0, kibBTotalRealisasi = 0, kibBTotalUnit = 0, kibBTotalAdminProyek = 0, kibBTotalNilaiBarang = 0;

        Object.keys(kibBGroups).forEach(groupKey => {
            const groupItems = kibBGroups[groupKey];
            const groupRealisasiTotal = groupItems.reduce((acc, it) => acc + (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0), 0);
            let groupAnggaranTotal = 0;
            groupItems.forEach(it => {
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0));
            });

            kibBTotalAnggaran += groupAnggaranTotal;
            kibBTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                let spec = item.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch (e) { spec = {}; }
                }

                const mesinItems = (spec && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0)
                    ? spec.mesin_items
                    : null;

                if (mesinItems) {
                    mesinItems.forEach((mItem) => {
                        const qty = Math.max(1, parseInt(mItem.mesin_jumlah_barang) || 1);
                        const nilaiSatuan = parseFloat(mItem.mesin_nilai_satuan) || 0;
                        const adminProyek = parseFloat(mItem.mesin_administrasi_proyek) || 0;
                        const totalNilaiBarang = (qty * nilaiSatuan) + adminProyek;

                        kibBTotalUnit += qty;
                        kibBTotalAdminProyek += adminProyek;
                        kibBTotalNilaiBarang += totalNilaiBarang;

                        const rawKondisi = mItem.mesin_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = rawKondisi === 'B' || rawKondisi === 'Baik' ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));
                        const ruangUnit = mItem.ruang_pemegang_mesin || item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '-');

                        let col1to15 = [];
                        if (isFirstRowInGroup) {
                            // Baris Pertama Group Sub Rincian: Isi Lengkap Kolom 1 s/d 15
                            col1to15 = [
                                globalKibBNo++,
                                item.program_kode || '-',
                                item.program_nama || '-',
                                item.kegiatan_kode || '-',
                                item.kegiatan_nama || '-',
                                item.sub_kegiatan_kode || '-',
                                item.sub_kegiatan_nama || '-',
                                item.rekening_kode || '-',
                                item.rekening_nama || '-',
                                item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.2'),
                                item.jenis_aset_nama || 'PERALATAN DAN MESIN',
                                item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                                item.sub_rincian_nama || '-',
                                groupAnggaranTotal,
                                groupRealisasiTotal
                            ];
                            isFirstRowInGroup = false;
                        } else {
                            // Baris Anak: Kolom 1 s/d 15 Dikosongkan (Blank)
                            col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                        }

                        kibBRows.push([
                            ...col1to15,                                 // c0-c14: cols 1-15
                            item.nama_barang || '-',                     // c15: col 16
                            item.kode_barang || '-',                     // c16: col 17
                            mItem.mesin_merk || item.merk || '-',        // c17: col 18
                            mItem.mesin_type || item.type || '-',        // c18: col 19
                            mItem.mesin_ukuran || item.ukuran || '-',    // c19: col 20
                            mItem.mesin_no_pabrik || item.no_pabrik || '-', // c20: col 21
                            mItem.mesin_no_rangka || item.no_rangka || '-', // c21: col 22
                            mItem.mesin_no_mesin || item.no_mesin || '-',   // c22: col 23
                            mItem.mesin_no_bpkb || item.no_btkb || '-',     // c23: col 24
                            mItem.mesin_no_polisi || item.no_polisi || '-', // c24: col 25
                            mItem.mesin_bahan || item.bahan || '-',         // c25: col 26
                            item.tahun_perolehan || '-',                 // c26: col 27
                            item.spk_nomor || '-',                       // c27: col 28 (SPK Nomor)
                            formatAstapDate(item.spk_tanggal),           // c28: col 29
                            item.surat_pesanan_nomor || '-',             // c29: col 30
                            formatAstapDate(item.surat_pesanan_tanggal), // c30: col 31
                            item.kwitansi_nomor || '-',                  // c31: col 32
                            formatAstapDate(item.kwitansi_tanggal),      // c32: col 33
                            item.faktur_nomor || '-',                    // c33: col 34 (Invoice Nomor)
                            formatAstapDate(item.faktur_tanggal),        // c34: col 35
                            kondisiLabel,                                // c35: col 36
                            qty,                                         // c36: col 37 (Jumlah Barang)
                            mItem.mesin_satuan || item.satuan || 'Unit', // c37: col 38 (Nama Satuan Barang)
                            nilaiSatuan,                                 // c38: col 39 (Nilai Satuan)
                            adminProyek,                                 // c39: col 40 (Admin Proyek)
                            totalNilaiBarang,                            // c40: col 41 (Total Nilai Barang)
                            item.sp2d_nomor || '-',                      // c41: col 42 (SP2D NOMOR)
                            formatAstapDate(item.sp2d_tanggal),          // c42: col 43 (SP2D TANGGAL)
                            item.bast_dokumen_nomor || '-',              // c43: col 44 (BAST NOMOR)
                            formatAstapDate(item.bast_dokumen_tanggal),  // c44: col 45 (BAST TANGGAL)
                            ruangUnit,                                   // c45: col 46 (Ruang / Unit Pemegang)
                            ...getStep4Columns(item)                     // c46-c53: cols 47-54
                        ]);
                    });
                } else {
                    const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                    const nilaiSatuan = typeof item.harga_satuan_num === 'number' ? item.harga_satuan_num : (parseFloat(item.harga_satuan) || 0);
                    const adminProyek = typeof item.biaya_administrasi_proyek_num === 'number' ? item.biaya_administrasi_proyek_num : (parseFloat(item.biaya_administrasi_proyek) || 0);
                    const jumlahBarang = typeof item.jumlah_volume === 'number' ? item.jumlah_volume : (parseInt(item.jumlah_volume) || 1);
                    const totalNilaiBarang = totalVal || ((jumlahBarang * nilaiSatuan) + adminProyek);
                    const ruangUnit = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '-');
                    
                    kibBTotalUnit += jumlahBarang;
                    kibBTotalAdminProyek += adminProyek;
                    kibBTotalNilaiBarang += totalNilaiBarang;

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = rawKondisi === 'B' || rawKondisi === 'Baik' ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    let col1to15 = [];
                    if (isFirstRowInGroup) {
                        col1to15 = [
                            globalKibBNo++,
                            item.program_kode || '-',
                            item.program_nama || '-',
                            item.kegiatan_kode || '-',
                            item.kegiatan_nama || '-',
                            item.sub_kegiatan_kode || '-',
                            item.sub_kegiatan_nama || '-',
                            item.rekening_kode || '-',
                            item.rekening_nama || '-',
                            item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.2'),
                            item.jenis_aset_nama || 'PERALATAN DAN MESIN',
                            item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                            item.sub_rincian_nama || '-',
                            groupAnggaranTotal,
                            groupRealisasiTotal
                        ];
                        isFirstRowInGroup = false;
                    } else {
                        col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                    }

                    kibBRows.push([
                        ...col1to15,                                 // c0-c14: cols 1-15
                        item.nama_barang || '-',                     // c15: col 16
                        item.kode_barang || '-',                     // c16: col 17
                        item.merk || '-',                            // c17: col 18
                        item.type || '-',                            // c18: col 19
                        item.ukuran || '-',                          // c19: col 20
                        item.no_pabrik || '-',                       // c20: col 21
                        item.no_rangka || '-',                       // c21: col 22
                        item.no_mesin || '-',                        // c22: col 23
                        item.no_btkb || '-',                         // c23: col 24
                        item.no_polisi || '-',                       // c24: col 25
                        item.bahan || '-',                           // c25: col 26
                        item.tahun_perolehan || '-',                 // c26: col 27
                        item.spk_nomor || '-',                       // c27: col 28 (SPK Nomor)
                        formatAstapDate(item.spk_tanggal),           // c28: col 29
                        item.surat_pesanan_nomor || '-',             // c29: col 30
                        formatAstapDate(item.surat_pesanan_tanggal), // c30: col 31
                        item.kwitansi_nomor || '-',                  // c31: col 32
                        formatAstapDate(item.kwitansi_tanggal),      // c32: col 33
                        item.faktur_nomor || '-',                    // c33: col 34 (Invoice Nomor)
                        formatAstapDate(item.faktur_tanggal),        // c34: col 35
                        kondisiLabel,                                // c35: col 36
                        jumlahBarang,                                // c36: col 37 (Jumlah Barang)
                        item.satuan || 'Unit',                       // c37: col 38 (Nama Satuan Barang)
                        nilaiSatuan,                                 // c38: col 39 (Nilai Satuan)
                        adminProyek,                                 // c39: col 40 (Admin Proyek)
                        totalNilaiBarang,                            // c40: col 41 (Total = 39+40)
                        item.sp2d_nomor || '-',                      // c41: col 42
                        formatAstapDate(item.sp2d_tanggal),          // c42: col 43
                        item.bast_dokumen_nomor || '-',              // c43: col 44
                        formatAstapDate(item.bast_dokumen_tanggal),  // c44: col 45
                        ruangUnit,                                   // c45: col 46
                        ...getStep4Columns(item)                     // c46-c53: cols 47-54
                    ]);
                }
            });
        });

        // ── Baris Footer Total KIB B (54 Kolom) ──────────────────────────────────
        const kibBFooterRow = Array(54).fill("");
        kibBFooterRow[0] = "JUMLAH";
        kibBFooterRow[13] = kibBTotalAnggaran;
        kibBFooterRow[14] = kibBTotalRealisasi;
        kibBFooterRow[36] = kibBTotalUnit;
        kibBFooterRow[39] = kibBTotalAdminProyek;
        kibBFooterRow[40] = kibBTotalNilaiBarang;
        kibBRows.push(kibBFooterRow);

        // Tanda Tangan KIB B (Format Baku RSUD Koesnadi)
        const kibBSignStartRow = kibBRows.length;
        const kibBSignRows = buildKibSignatureRows(54, 40, ppkNama, ppkNip, signDate, 1);
        kibBSignRows.forEach(r => kibBRows.push(r));

        const wsKibB = XLSX.utils.aoa_to_sheet(kibBRows);
        wsKibB['!cols'] = Array(54).fill({wch: 18});
        wsKibB['!cols'][0] = {wch: 6};
        wsKibB['!cols'][1] = {wch: 14}; wsKibB['!cols'][2] = {wch: 32};
        wsKibB['!cols'][3] = {wch: 14}; wsKibB['!cols'][4] = {wch: 28};
        wsKibB['!cols'][5] = {wch: 16}; wsKibB['!cols'][6] = {wch: 30};
        wsKibB['!cols'][7] = {wch: 18}; wsKibB['!cols'][8] = {wch: 30};
        wsKibB['!cols'][9] = {wch: 14}; wsKibB['!cols'][10] = {wch: 24};
        wsKibB['!cols'][11] = {wch: 18}; wsKibB['!cols'][12] = {wch: 32};
        wsKibB['!cols'][13] = {wch: 22}; wsKibB['!cols'][14] = {wch: 22};
        wsKibB['!cols'][15] = {wch: 32}; wsKibB['!cols'][16] = {wch: 22};
        wsKibB['!cols'][17] = {wch: 20}; wsKibB['!cols'][18] = {wch: 20};
        wsKibB['!cols'][19] = {wch: 22}; wsKibB['!cols'][20] = {wch: 20};
        wsKibB['!cols'][21] = {wch: 20}; wsKibB['!cols'][22] = {wch: 20};
        wsKibB['!cols'][23] = {wch: 20}; wsKibB['!cols'][24] = {wch: 16};
        wsKibB['!cols'][25] = {wch: 16}; wsKibB['!cols'][26] = {wch: 14};
        wsKibB['!cols'][27] = {wch: 22}; wsKibB['!cols'][28] = {wch: 14};
        wsKibB['!cols'][29] = {wch: 22}; wsKibB['!cols'][30] = {wch: 14};
        wsKibB['!cols'][31] = {wch: 22}; wsKibB['!cols'][32] = {wch: 14};
        wsKibB['!cols'][33] = {wch: 22}; wsKibB['!cols'][34] = {wch: 14};
        wsKibB['!cols'][35] = {wch: 14}; wsKibB['!cols'][36] = {wch: 14};
        wsKibB['!cols'][37] = {wch: 18}; wsKibB['!cols'][38] = {wch: 22};
        wsKibB['!cols'][39] = {wch: 22}; wsKibB['!cols'][40] = {wch: 22};
        wsKibB['!cols'][41] = {wch: 20}; wsKibB['!cols'][42] = {wch: 14};
        wsKibB['!cols'][43] = {wch: 28}; wsKibB['!cols'][44] = {wch: 14};
        wsKibB['!cols'][45] = {wch: 28}; wsKibB['!cols'][46] = {wch: 28};
        wsKibB['!cols'][47] = {wch: 24}; wsKibB['!cols'][48] = {wch: 24};
        wsKibB['!cols'][49] = {wch: 22}; wsKibB['!cols'][50] = {wch: 30};
        wsKibB['!cols'][51] = {wch: 24}; wsKibB['!cols'][52] = {wch: 22};
        wsKibB['!cols'][53] = {wch: 26};

        // ── Merge Cells KIB B (54 Kolom Sesuai Format Baku Gambar) ───────────────
        wsKibB['!merges'] = getKibMerges([
            // Col 1: NO (r3-r6, c0)
            {s:{r:3,c:0}, e:{r:6,c:0}},

            // Col 2-3: Program Pengadaan SIPD (r3-r4 banner, r5-r6 sub-headers)
            {s:{r:3,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},  // Kode
            {s:{r:5,c:2}, e:{r:6,c:2}},  // Nama Program

            // Col 4-5: Kegiatan Pengadaan SIPD
            {s:{r:3,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},  // Kode
            {s:{r:5,c:4}, e:{r:6,c:4}},  // Nama Kegiatan

            // Col 6-7: Sub Kegiatan Pengadaan SIPD
            {s:{r:3,c:5}, e:{r:4,c:6}},
            {s:{r:5,c:5}, e:{r:6,c:5}},  // Kode
            {s:{r:5,c:6}, e:{r:6,c:6}},  // Nama Sub Kegiatan

            // Col 8-15: BELANJA MODAL (Top Banner r3, c7-c14)
            {s:{r:3,c:7}, e:{r:3,c:14}},
            // Col 8-9: Rekening Belanja Untuk Pengadaan SIPD
            {s:{r:4,c:7}, e:{r:4,c:8}},
            {s:{r:5,c:7}, e:{r:6,c:7}},   // Kode Rek
            {s:{r:5,c:8}, e:{r:6,c:8}},   // Nama Belanja Pengadaan
            // Col 10-11: Jenis Aset (PMDN 108)
            {s:{r:4,c:9}, e:{r:4,c:10}},
            {s:{r:5,c:9}, e:{r:6,c:9}},   // Kode
            {s:{r:5,c:10}, e:{r:6,c:10}}, // Nama Jenis Aset
            // Col 12-13: Sub Rincian Objek (PMDN 108)
            {s:{r:4,c:11}, e:{r:4,c:12}},
            {s:{r:5,c:11}, e:{r:6,c:11}}, // Kode
            {s:{r:5,c:12}, e:{r:6,c:12}}, // Nama Uraian Sub Rincian Objek
            // Col 14: JUMLAH ANGGARAN (Rp)
            {s:{r:4,c:13}, e:{r:6,c:13}},
            // Col 15: JUMLAH REALISASI (Rp)
            {s:{r:4,c:14}, e:{r:6,c:14}},

            // Col 16-45: RINCIAN BELANJA MODAL ... (Top Banner r3, c15-c44)
            {s:{r:3,c:15}, e:{r:3,c:44}},
            // Kolom standalone (r4-r6 merged):
            {s:{r:4,c:15}, e:{r:6,c:15}},  // Col 16: NAMA BARANG
            {s:{r:4,c:16}, e:{r:6,c:16}},  // Col 17: Kode Barang
            {s:{r:4,c:17}, e:{r:6,c:17}},  // Col 18: Merk
            {s:{r:4,c:18}, e:{r:6,c:18}},  // Col 19: Type
            {s:{r:4,c:19}, e:{r:6,c:19}},  // Col 20: Ukuran / CC
            {s:{r:4,c:20}, e:{r:6,c:20}},  // Col 21: No. Pabrik
            {s:{r:4,c:21}, e:{r:6,c:21}},  // Col 22: No. Rangka
            {s:{r:4,c:22}, e:{r:6,c:22}},  // Col 23: No. Mesin
            {s:{r:4,c:23}, e:{r:6,c:23}},  // Col 24: No. BTKB
            {s:{r:4,c:24}, e:{r:6,c:24}},  // Col 25: No. POLISI
            {s:{r:4,c:25}, e:{r:6,c:25}},  // Col 26: BAHAN
            {s:{r:4,c:26}, e:{r:6,c:26}},  // Col 27: Tahun Perolehan
            // Col 28-35: Riwayat Pembelian (r4 banner c27-c34)
            {s:{r:4,c:27}, e:{r:4,c:34}},
            {s:{r:5,c:27}, e:{r:5,c:28}},  // SPK (r5) -> r6: Nomor (c27), Tanggal (c28)
            {s:{r:5,c:29}, e:{r:5,c:30}},  // Surat Pesanan -> r6: Nomor (c29), Tanggal (c30)
            {s:{r:5,c:31}, e:{r:5,c:32}},  // Kwitansi -> r6: Nomor (c31), Tanggal (c32)
            {s:{r:5,c:33}, e:{r:5,c:34}},  // Invoice -> r6: Nomor (c33), Tanggal (c34)
            // Col 36: Kondisi (B,KB,RB) standalone (r4-r6, c35)
            {s:{r:4,c:35}, e:{r:6,c:35}},
            // Col 37-38: VOLUME (r4 banner c36-c37)
            {s:{r:4,c:36}, e:{r:4,c:37}},
            {s:{r:5,c:36}, e:{r:6,c:36}},  // Jumlah Barang
            {s:{r:5,c:37}, e:{r:6,c:37}},  // Nama Satuan Barang
            // Col 39: Nilai Satuan Barang (r4-r6, c38)
            {s:{r:4,c:38}, e:{r:6,c:38}},
            // Col 40: ADMINISTRASI PROYEK (r4-r6, c39)
            {s:{r:4,c:39}, e:{r:6,c:39}},
            // Col 41: Total Nilai Barang (r4-r6, c40)
            {s:{r:4,c:40}, e:{r:6,c:40}},
            // Col 42-43: SP2D (r4-r5 banner, c41-c42) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:41}, e:{r:5,c:42}},
            // Col 44-45: BAST pada SPK/... (r4-r5 banner, c43-c44) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:43}, e:{r:5,c:44}},
            // Col 46: RUANG / PEMEGANG (Berdiri Sendiri r3-r6, c45)
            {s:{r:3,c:45}, e:{r:6,c:45}},

            // Col 47-51: PIHAK PENYEDIA (Top Banner r3-r4, c46-c50)
            {s:{r:3,c:46}, e:{r:4,c:50}},
            {s:{r:5,c:46}, e:{r:6,c:46}},  // Nama Penyedia
            {s:{r:5,c:47}, e:{r:6,c:47}},  // Pemilik Penyedia
            {s:{r:5,c:48}, e:{r:5,c:49}},  // Rekening -> r6: Nama Rek (c48), Nomor Rek (c49)
            {s:{r:5,c:50}, e:{r:6,c:50}},  // Alamat Penyedia

            // Col 52-53: Pejabat Pembuat Komitmen (r3-r4, c51-c52)
            {s:{r:3,c:51}, e:{r:4,c:52}},
            {s:{r:5,c:51}, e:{r:6,c:51}},  // Nama
            {s:{r:5,c:52}, e:{r:6,c:52}},  // NIP

            // Col 54: KET. (berdiri sendiri r3-r6, c53)
            {s:{r:3,c:53}, e:{r:6,c:53}}
        ], 54, kibBTitleRows.length, kibBRows.length, true);
        wsKibB['!merges'].push(...getKibSignatureMerges(kibBSignStartRow, 54, 40, 1, 6, 53));

        applyUnified4StepMasterSheetStyling(wsKibB, kibBRows.length, 54, 31, kibBTitleRows.length, true);
        applySignatureBlockStyling(wsKibB, kibBSignStartRow, 54);
        if (filterCat === 'all' || filterCat === 'KIB B') {
            XLSX.utils.book_append_sheet(wb, wsKibB, filterCat === 'all' ? "3. B" : "KIB B - Peralatan & Mesin");
        }

        // ------------------------------------------------------------------------
        // 4. KIB C (GEDUNG DAN BANGUNAN) - COMPLETE 4-STEP MASTER SHEET (55 KOLOM)
        // Sesuai Format Baku Gambar: 1-15 (Langkah 1-2), 16-46 (Langkah 3), 47-55 (Langkah 4)
        // ------------------------------------------------------------------------
        const kibCTitleRows = getKibTitleRows("GEDUNG DAN BANGUNAN", yearLabel, filterTw);
        const kibCRows = [
            ...kibCTitleRows,
            // r3: Main Banner (55 kolom)
            [
                "NO",
                "Program Pengadaan SIPD", "",
                "Kegiatan Pengadaan SIPD", "",
                "Sub Kegiatan Pengadaan SIPD", "",
                "BELANJA MODAL", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE / " + yearLabel, "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "Letak/ Alamat",
                "PIHAK PENYEDIA", "", "", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            // r4: Sub Banner (Level 2)
            [
                "",
                "", "",
                "", "",
                "", "",
                "Rekening Belanja Untuk Pengadaan SIPD", "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH ANGGARAN (Rp)",
                "JUMLAH REALISASI (Rp)",
                "NAMA BARANG (Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang (Kode Sub Sub Rincian Objek PMDN 108)",
                "Luas Lantai (m²)",
                "Kondisi / Spesifikasi", "", "",
                "Jenis Bangunan", "", "", "", "", "",
                "Riwayat Pembelian", "", "", "", "", "", "", "",
                "VOLUME", "",
                "Nilai Barang (Rp)", "", "", "",
                "Total Nilai Barang (Rp)",
                "SP2D", "",
                "BAST pada SPK/Surat Pesanan/Kwitansi/Invoice", "",
                "",
                "", "",
                "Rekening", "",
                "",
                "", "",
                ""
            ],
            // r5: Sub Header (Level 3)
            [
                "",
                "Kode", "Nama Program",
                "Kode", "Nama Kegiatan Pengadaan",
                "Kode", "Nama Sub Kegiatan Pengadaan",
                "Kode Rek", "Nama Belanja Pengadaan",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "",
                "",
                "",
                "",
                "",
                "(B,KB,RB)", "Bertingkat / Tidak", "Beton / Tidak",
                "Status Tanah",
                "Kode aset Tanah",
                "Baru",
                "Kapitalisasi", "", "",
                "SPK", "",
                "Surat Pesanan", "",
                "Kwitansi", "",
                "Invoice", "",
                "Jumlah Bangunan", "Nama Satuan Barang",
                "Nilai Perencanaan (Rp)", "Nilai Fisik (Rp)", "Nilai Pengawasan", "Nilai AP",
                "",
                "", "",
                "", "",
                "",
                "Nama Penyedia", "Pemilik Penyedia",
                "", "",
                "Alamat Penyedia",
                "Nama", "NIP",
                ""
            ],
            // r6: Technical Sub Detail (Level 4)
            [
                "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "",
                "", "", "",
                "Nibar", "Tahun Induk", "Nilai Induk s/d " + (parseInt(yearLabel) - 1 || '2025'),
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "", "",
                "", "", "", "",
                "",
                "NOMOR", "TANGGAL",
                "NOMOR", "TANGGAL",
                "",
                "", "", "Nama Rek", "Nomor Rek", "",
                "", "",
                ""
            ],
            // r7: Column Numbers (1 s/d 55)
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45",
                "46", "47", "48", "49", "50", "51", "52", "53", "54", "55"
            ]
        ];

        // ── Grouping KIB C Berdasarkan Sub Rincian Objek PMDN 108 ──────────────
        const kibCGroups = {};
        categories['KIB C'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.3.3.01.01.01');
            if (!kibCGroups[groupKey]) kibCGroups[groupKey] = [];
            kibCGroups[groupKey].push(item);
        });

        let globalKibCNo = 1;
        let kibCTotalAnggaran = 0, kibCTotalRealisasi = 0, kibCTotalLuas = 0, kibCTotalUnit = 0;
        let kibCTotalPerencanaan = 0, kibCTotalFisik = 0, kibCTotalPengawasan = 0, kibCTotalAp = 0, kibCTotalNilaiBarang = 0;

        Object.keys(kibCGroups).forEach(groupKey => {
            const groupItems = kibCGroups[groupKey];
            let groupAnggaranTotal = 0;
            groupItems.forEach(it => {
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0));
            });
            const groupRealisasiTotal = groupItems.reduce((acc, curr) => {
                const val = typeof curr.total_realisasi_num === 'number' ? curr.total_realisasi_num : (parseFloat(curr.total_realisasi) || 0);
                return acc + val;
            }, 0);

            kibCTotalAnggaran += groupAnggaranTotal;
            kibCTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const subGedungItems = (item.spesifikasi_json && Array.isArray(item.spesifikasi_json.gedung_items) && item.spesifikasi_json.gedung_items.length > 0)
                    ? item.spesifikasi_json.gedung_items
                    : null;

                if (subGedungItems) {
                    subGedungItems.forEach((gItem, gIdx) => {
                        const nilaiPerencanaan = parseFloat(gItem.gedung_nilai_perencanaan) || 0;
                        const nilaiFisik = parseFloat(gItem.gedung_nilai_fisik) || 0;
                        const nilaiPengawasan = parseFloat(gItem.gedung_nilai_pengawasan) || 0;
                        const nilaiAp = parseFloat(gItem.gedung_nilai_ap) || parseFloat(gItem.gedung_nilai_pip) || 0;
                        const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan + nilaiAp) || (parseFloat(item.total_realisasi) || 0);
                        const jumlahBangunan = parseInt(gItem.gedung_jumlah_bangunan) || 1;
                        const luasM2 = parseFloat(gItem.gedung_luas_m2) || 0;

                        kibCTotalLuas += luasM2;
                        kibCTotalUnit += jumlahBangunan;
                        kibCTotalPerencanaan += nilaiPerencanaan;
                        kibCTotalFisik += nilaiFisik;
                        kibCTotalPengawasan += nilaiPengawasan;
                        kibCTotalAp += nilaiAp;
                        kibCTotalNilaiBarang += totalNilaiBarang;

                        const rawKondisi = gItem.gedung_kondisi || item.kondisi || 'B';
                        const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' : ((rawKondisi === 'KB' || rawKondisi === 'Kurang Baik') ? 'Kurang Baik' : ((rawKondisi === 'RB' || rawKondisi === 'Rusak Berat') ? 'Rusak Berat' : rawKondisi));

                        let col1to15 = [];
                        if (isFirstRowInGroup) {
                            col1to15 = [
                                globalKibCNo++,
                                item.program_kode || '-',
                                item.program_nama || '-',
                                item.kegiatan_kode || '-',
                                item.kegiatan_nama || '-',
                                item.sub_kegiatan_kode || '-',
                                item.sub_kegiatan_nama || '-',
                                item.rekening_kode || '-',
                                item.rekening_nama || '-',
                                item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.3'),
                                item.jenis_aset_nama || 'GEDUNG DAN BANGUNAN',
                                item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                                item.sub_rincian_nama || '-',
                                groupAnggaranTotal,
                                groupRealisasiTotal
                            ];
                            isFirstRowInGroup = false;
                        } else {
                            col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                        }

                        const isGedungBaru = (gItem.gedung_is_baru === 'Baru' || !gItem.gedung_is_baru);

                        kibCRows.push([
                            ...col1to15,                                                 // c0-c14: cols 1-15
                            gItem.gedung_nama_barang || item.nama_barang || '-',         // c15: col 16 (Nama Barang)
                            gItem.gedung_kode_barang || item.kode_barang || '-',         // c16: col 17 (Kode Barang)
                            luasM2,                                                      // c17: col 18 (Luas Lantai M²)
                            kondisiLabel,                                                // c18: col 19 (Kondisi B/KB/RB)
                            gItem.gedung_bertingkat || 'Bertingkat',                      // c19: col 20 (Bertingkat / Tidak)
                            gItem.gedung_beton || 'Beton',                                // c20: col 21 (Beton / Tidak)
                            gItem.gedung_status_tanah || 'Tanah Hak Pakai RSUD',          // c21: col 22 (Status Tanah)
                            gItem.gedung_kode_aset_tanah || '-',                          // c22: col 23 (Kode aset Tanah)
                            isGedungBaru ? '1' : '-',                                     // c23: col 24 (Baru: '1' jika Baru, '-' jika Lama)
                            getAstapNibar(item, gItem, gIdx),                            // c24: col 25 (Nibar diambil dari astaps register)
                            isGedungBaru ? '-' : (gItem.gedung_kapitalisasi_tahun_induk || '-'), // c25: col 26 (Tahun Induk)
                            isGedungBaru ? '-' : (parseFloat(gItem.gedung_kapitalisasi_nilai_induk) || 0), // c26: col 27 (Nilai Induk s/d ...)
                            item.spk_nomor || '-',                                       // c27: col 28 (SPK No)
                            formatAstapDate(item.spk_tanggal),                           // c28: col 29 (SPK Tgl)
                            item.surat_pesanan_nomor || '-',                             // c29: col 30 (Surat Pesanan No)
                            formatAstapDate(item.surat_pesanan_tanggal),                 // c30: col 31 (Surat Pesanan Tgl)
                            item.kwitansi_nomor || '-',                                  // c31: col 32 (Kwitansi No)
                            formatAstapDate(item.kwitansi_tanggal),                      // c32: col 33 (Kwitansi Tgl)
                            item.faktur_nomor || '-',                                    // c33: col 34 (Invoice No)
                            formatAstapDate(item.faktur_tanggal),                        // c34: col 35 (Invoice Tgl)
                            jumlahBangunan,                                              // c35: col 36 (Jumlah Bangunan)
                            gItem.gedung_satuan || item.satuan || 'Gedung',              // c36: col 37 (Nama Satuan Barang)
                            nilaiPerencanaan,                                            // c37: col 38 (Nilai Perencanaan)
                            nilaiFisik,                                                  // c38: col 39 (Nilai Fisik)
                            nilaiPengawasan,                                             // c39: col 40 (Nilai Pengawasan)
                            nilaiAp,                                                     // c40: col 41 (Nilai AP)
                            totalNilaiBarang,                                            // c41: col 42 (Total Nilai Barang)
                            item.sp2d_nomor || '-',                                      // c42: col 43 (SP2D NOMOR)
                            formatAstapDate(item.sp2d_tanggal),                          // c43: col 44 (SP2D TANGGAL)
                            item.bast_dokumen_nomor || '-',                              // c44: col 45 (BAST NOMOR)
                            formatAstapDate(item.bast_dokumen_tanggal),                  // c45: col 46 (BAST TANGGAL)
                            gItem.gedung_alamat || item.alamat_barang || '-',            // c46: col 47 (Letak/ Alamat)
                            ...getStep4Columns(item)                                     // c47-c54: cols 48-55
                        ]);
                    });
                } else {
                    const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                    const nilaiPerencanaan = parseFloat(item.gedung_nilai_perencanaan) || parseFloat(item.nilai_perencanaan) || 0;
                    const nilaiFisik = parseFloat(item.gedung_nilai_fisik) || parseFloat(item.nilai_fisik) || totalVal;
                    const nilaiPengawasan = parseFloat(item.gedung_nilai_pengawasan) || parseFloat(item.nilai_pengawasan) || 0;
                    const nilaiAp = parseFloat(item.gedung_nilai_ap) || parseFloat(item.gedung_nilai_pip) || parseFloat(item.nilai_ap) || parseFloat(item.nilai_pip) || 0;
                    const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan + nilaiAp) || totalVal;
                    const jumlahBangunan = parseInt(item.jumlah_volume) || parseInt(item.jumlah_unit) || 1;
                    const luasM2 = parseFloat(item.luas_m2) || 0;

                    kibCTotalLuas += luasM2;
                    kibCTotalUnit += jumlahBangunan;
                    kibCTotalPerencanaan += nilaiPerencanaan;
                    kibCTotalFisik += nilaiFisik;
                    kibCTotalPengawasan += nilaiPengawasan;
                    kibCTotalAp += nilaiAp;
                    kibCTotalNilaiBarang += totalNilaiBarang;

                    const kondisiLabel = item.kondisi ? (item.kondisi === 'B' ? 'Baik' : (item.kondisi === 'KB' ? 'Kurang Baik' : (item.kondisi === 'RB' ? 'Rusak Berat' : item.kondisi))) : 'Baik';

                    let col1to15 = [];
                    if (isFirstRowInGroup) {
                        col1to15 = [
                            globalKibCNo++,
                            item.program_kode || '-',
                            item.program_nama || '-',
                            item.kegiatan_kode || '-',
                            item.kegiatan_nama || '-',
                            item.sub_kegiatan_kode || '-',
                            item.sub_kegiatan_nama || '-',
                            item.rekening_kode || '-',
                            item.rekening_nama || '-',
                            item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.3'),
                            item.jenis_aset_nama || 'GEDUNG DAN BANGUNAN',
                            item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                            item.sub_rincian_nama || '-',
                            groupAnggaranTotal,
                            groupRealisasiTotal
                        ];
                        isFirstRowInGroup = false;
                    } else {
                        col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                    }

                    const isGedungBaru = (item.gedung_is_baru === 'Baru' || !item.gedung_is_baru);

                    kibCRows.push([
                        ...col1to15,                                                 // c0-c14: cols 1-15
                        item.nama_barang || '-',                                     // c15: col 16 (Nama Barang)
                        item.kode_barang || '-',                                     // c16: col 17 (Kode Barang)
                        luasM2,                                                      // c17: col 18 (Luas Lantai M²)
                        kondisiLabel,                                                // c18: col 19 (Kondisi B/KB/RB)
                        item.gedung_bertingkat || 'Bertingkat',                      // c19: col 20 (Bertingkat / Tidak)
                        item.gedung_beton || 'Beton',                                // c20: col 21 (Beton / Tidak)
                        item.gedung_status_tanah || 'Tanah Hak Pakai RSUD',          // c21: col 22 (Status Tanah)
                        item.gedung_kode_aset_tanah || '-',                          // c22: col 23 (Kode aset Tanah)
                        isGedungBaru ? '1' : '-',                                    // c23: col 24 (Baru: '1' jika Baru, '-' jika Lama)
                        getAstapNibar(item),                                         // c24: col 25 (Nibar diambil dari astaps register)
                        isGedungBaru ? '-' : (item.gedung_kapitalisasi_tahun_induk || '-'), // c25: col 26 (Tahun Induk)
                        isGedungBaru ? '-' : (parseFloat(item.gedung_kapitalisasi_nilai_induk) || 0), // c26: col 27 (Nilai Induk s/d ...)
                        item.spk_nomor || '-',                                       // c27: col 28 (SPK No)
                        formatAstapDate(item.spk_tanggal),                           // c28: col 29 (SPK Tgl)
                        item.surat_pesanan_nomor || '-',                             // c29: col 30 (Surat Pesanan No)
                        formatAstapDate(item.surat_pesanan_tanggal),                 // c30: col 31 (Surat Pesanan Tgl)
                        item.kwitansi_nomor || '-',                                  // c31: col 32 (Kwitansi No)
                        formatAstapDate(item.kwitansi_tanggal),                      // c32: col 33 (Kwitansi Tgl)
                        item.faktur_nomor || '-',                                    // c33: col 34 (Invoice No)
                        formatAstapDate(item.faktur_tanggal),                        // c34: col 35 (Invoice Tgl)
                        jumlahBangunan,                                              // c35: col 36 (Jumlah Bangunan)
                        item.satuan || 'Unit Bangunan',                              // c36: col 37 (Nama Satuan Barang)
                        nilaiPerencanaan,                                            // c37: col 38 (Nilai Perencanaan)
                        nilaiFisik,                                                  // c38: col 39 (Nilai Fisik)
                        nilaiPengawasan,                                             // c39: col 40 (Nilai Pengawasan)
                        nilaiAp,                                                     // c40: col 41 (Nilai AP)
                        totalNilaiBarang,                                            // c41: col 42 (Total Nilai Barang)
                        item.sp2d_nomor || '-',                                      // c42: col 43 (SP2D NOMOR)
                        formatAstapDate(item.sp2d_tanggal),                          // c43: col 44 (SP2D TANGGAL)
                        item.bast_dokumen_nomor || '-',                              // c44: col 45 (BAST NOMOR)
                        formatAstapDate(item.bast_dokumen_tanggal),                  // c45: col 46 (BAST TANGGAL)
                        item.alamat_barang || '-',                                   // c46: col 47 (Letak/ Alamat)
                        ...getStep4Columns(item)                                     // c47-c54: cols 48-55
                    ]);
                }
            });
        });

        // ── Baris Footer Total KIB C (55 Kolom) ──────────────────────────────────
        const kibCFooterRow = Array(55).fill("");
        kibCFooterRow[0] = "JUMLAH";
        kibCFooterRow[13] = kibCTotalAnggaran;
        kibCFooterRow[14] = kibCTotalRealisasi;
        kibCFooterRow[17] = kibCTotalLuas;
        kibCFooterRow[35] = kibCTotalUnit;
        kibCFooterRow[37] = kibCTotalPerencanaan;
        kibCFooterRow[38] = kibCTotalFisik;
        kibCFooterRow[39] = kibCTotalPengawasan;
        kibCFooterRow[40] = kibCTotalAp;
        kibCFooterRow[41] = kibCTotalNilaiBarang;
        kibCRows.push(kibCFooterRow);

        // Tanda Tangan KIB C (Format Baku RSUD Koesnadi)
        const kibCSignStartRow = kibCRows.length;
        const kibCSignRows = buildKibSignatureRows(55, 41, ppkNama, ppkNip, signDate, 1);
        kibCSignRows.forEach(r => kibCRows.push(r));

        const wsKibC = XLSX.utils.aoa_to_sheet(kibCRows);
        wsKibC['!cols'] = Array(55).fill({wch: 18});
        wsKibC['!cols'][0] = {wch: 6};
        wsKibC['!cols'][1] = {wch: 14}; wsKibC['!cols'][2] = {wch: 32};
        wsKibC['!cols'][3] = {wch: 14}; wsKibC['!cols'][4] = {wch: 28};
        wsKibC['!cols'][5] = {wch: 16}; wsKibC['!cols'][6] = {wch: 30};
        wsKibC['!cols'][7] = {wch: 18}; wsKibC['!cols'][8] = {wch: 30};
        wsKibC['!cols'][9] = {wch: 14}; wsKibC['!cols'][10] = {wch: 24};
        wsKibC['!cols'][11] = {wch: 18}; wsKibC['!cols'][12] = {wch: 32};
        wsKibC['!cols'][13] = {wch: 22}; wsKibC['!cols'][14] = {wch: 22};
        wsKibC['!cols'][15] = {wch: 32}; wsKibC['!cols'][16] = {wch: 22};
        wsKibC['!cols'][17] = {wch: 16}; wsKibC['!cols'][18] = {wch: 14};
        wsKibC['!cols'][19] = {wch: 18}; wsKibC['!cols'][20] = {wch: 16};
        wsKibC['!cols'][21] = {wch: 24}; wsKibC['!cols'][22] = {wch: 20};
        wsKibC['!cols'][23] = {wch: 16}; wsKibC['!cols'][24] = {wch: 22};
        wsKibC['!cols'][25] = {wch: 16}; wsKibC['!cols'][26] = {wch: 22};
        wsKibC['!cols'][27] = {wch: 22}; wsKibC['!cols'][28] = {wch: 14};
        wsKibC['!cols'][29] = {wch: 22}; wsKibC['!cols'][30] = {wch: 14};
        wsKibC['!cols'][31] = {wch: 22}; wsKibC['!cols'][32] = {wch: 14};
        wsKibC['!cols'][33] = {wch: 22}; wsKibC['!cols'][34] = {wch: 14};
        wsKibC['!cols'][35] = {wch: 18}; wsKibC['!cols'][36] = {wch: 20};
        wsKibC['!cols'][37] = {wch: 22}; wsKibC['!cols'][38] = {wch: 22};
        wsKibC['!cols'][39] = {wch: 22}; wsKibC['!cols'][40] = {wch: 22};
        wsKibC['!cols'][41] = {wch: 22}; wsKibC['!cols'][42] = {wch: 20};
        wsKibC['!cols'][43] = {wch: 14}; wsKibC['!cols'][44] = {wch: 28};
        wsKibC['!cols'][45] = {wch: 14}; wsKibC['!cols'][46] = {wch: 32};
        wsKibC['!cols'][47] = {wch: 28}; wsKibC['!cols'][48] = {wch: 24};
        wsKibC['!cols'][49] = {wch: 24}; wsKibC['!cols'][50] = {wch: 22};
        wsKibC['!cols'][51] = {wch: 30}; wsKibC['!cols'][52] = {wch: 24};
        wsKibC['!cols'][53] = {wch: 22}; wsKibC['!cols'][54] = {wch: 26};

        // ── Merge Cells KIB C (55 Kolom Sesuai Format Baku Gambar) ──────────────
        wsKibC['!merges'] = getKibMerges([
            // Col 1: NO (r3-r6, c0)
            {s:{r:3,c:0}, e:{r:6,c:0}},

            // Col 2-3: Program Pengadaan SIPD (r3-r4 banner c1-c2, r5-r6 sub-headers)
            {s:{r:3,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},  // Kode
            {s:{r:5,c:2}, e:{r:6,c:2}},  // Nama Program

            // Col 4-5: Kegiatan Pengadaan SIPD (r3-r4 banner c3-c4)
            {s:{r:3,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},  // Kode
            {s:{r:5,c:4}, e:{r:6,c:4}},  // Nama Kegiatan Pengadaan

            // Col 6-7: Sub Kegiatan Pengadaan SIPD (r3-r4 banner c5-c6)
            {s:{r:3,c:5}, e:{r:4,c:6}},
            {s:{r:5,c:5}, e:{r:6,c:5}},  // Kode
            {s:{r:5,c:6}, e:{r:6,c:6}},  // Nama Sub Kegiatan Pengadaan

            // Col 8-15: BELANJA MODAL (Top Banner r3, c7-c14)
            {s:{r:3,c:7}, e:{r:3,c:14}},
            // Col 8-9: Rekening Belanja Untuk Pengadaan SIPD
            {s:{r:4,c:7}, e:{r:4,c:8}},
            {s:{r:5,c:7}, e:{r:6,c:7}},   // Kode Rek
            {s:{r:5,c:8}, e:{r:6,c:8}},   // Nama Belanja Pengadaan
            // Col 10-11: Jenis Aset (PMDN 108)
            {s:{r:4,c:9}, e:{r:4,c:10}},
            {s:{r:5,c:9}, e:{r:6,c:9}},   // Kode
            {s:{r:5,c:10}, e:{r:6,c:10}}, // Nama Jenis Aset
            // Col 12-13: Sub Rincian Objek (PMDN 108)
            {s:{r:4,c:11}, e:{r:4,c:12}},
            {s:{r:5,c:11}, e:{r:6,c:11}}, // Kode
            {s:{r:5,c:12}, e:{r:6,c:12}}, // Nama Uraian Sub Rincian Objek
            // Col 14: JUMLAH ANGGARAN (Rp)
            {s:{r:4,c:13}, e:{r:6,c:13}},
            // Col 15: JUMLAH REALISASI (Rp)
            {s:{r:4,c:14}, e:{r:6,c:14}},

            // Col 16-46: RINCIAN BELANJA MODAL ... (Top Banner r3, c15-c45)
            {s:{r:3,c:15}, e:{r:3,c:45}},
            // Kolom standalone (r4-r6 merged):
            {s:{r:4,c:15}, e:{r:6,c:15}},  // Col 16: NAMA BARANG
            {s:{r:4,c:16}, e:{r:6,c:16}},  // Col 17: Kode Barang
            {s:{r:4,c:17}, e:{r:6,c:17}},  // Col 18: Luas Lantai (m²)
            // Col 19-21: Kondisi / Spesifikasi (r4 banner c18-c20)
            {s:{r:4,c:18}, e:{r:4,c:20}},
            {s:{r:5,c:18}, e:{r:6,c:18}},  // Col 19: (B,KB,RB)
            {s:{r:5,c:19}, e:{r:6,c:19}},  // Col 20: Bertingkat / Tidak
            {s:{r:5,c:20}, e:{r:6,c:20}},  // Col 21: Beton / Tidak

            // Col 22-27: Jenis Bangunan (Top banner on r4, c21-c26)
            {s:{r:4,c:21}, e:{r:4,c:26}},
            {s:{r:5,c:21}, e:{r:6,c:21}},  // Col 22: Status Tanah (r5-r6)
            {s:{r:5,c:22}, e:{r:6,c:22}},  // Col 23: Kode aset Tanah (r5-r6)
            {s:{r:5,c:23}, e:{r:6,c:23}},  // Col 24: Baru (r5-r6)
            // Col 25-27: Kapitalisasi (Sub banner on r5, c24-c26)
            {s:{r:5,c:24}, e:{r:5,c:26}},
            // r6: Nibar (c24), Tahun Induk (c25), Nilai Induk (c26)

            // Col 28-35: Riwayat Pembelian (r4 banner c27-c34)
            {s:{r:4,c:27}, e:{r:4,c:34}},
            {s:{r:5,c:27}, e:{r:5,c:28}},  // SPK (r5) -> r6: Nomor (c27), Tanggal (c28)
            {s:{r:5,c:29}, e:{r:5,c:30}},  // Surat Pesanan -> r6: Nomor (c29), Tanggal (c30)
            {s:{r:5,c:31}, e:{r:5,c:32}},  // Kwitansi -> r6: Nomor (c31), Tanggal (c32)
            {s:{r:5,c:33}, e:{r:5,c:34}},  // Invoice -> r6: Nomor (c33), Tanggal (c34)
            // Col 36-37: VOLUME (r4 banner c35-c36)
            {s:{r:4,c:35}, e:{r:4,c:36}},
            {s:{r:5,c:35}, e:{r:6,c:35}},  // Jumlah Bangunan
            {s:{r:5,c:36}, e:{r:6,c:36}},  // Nama Satuan Barang
            // Col 38-41: Nilai Barang (Rp) (r4 banner c37-c40)
            {s:{r:4,c:37}, e:{r:4,c:40}},
            {s:{r:5,c:37}, e:{r:6,c:37}},  // Nilai Perencanaan (Rp)
            {s:{r:5,c:38}, e:{r:6,c:38}},  // Nilai Fisik (Rp)
            {s:{r:5,c:39}, e:{r:6,c:39}},  // Nilai Pengawasan
            {s:{r:5,c:40}, e:{r:6,c:40}},  // Nilai AP
            // Col 42: Total Nilai Barang (Rp) (r4-r6, c41)
            {s:{r:4,c:41}, e:{r:6,c:41}},
            // Col 43-44: SP2D (r4-r5 banner, c42-c43) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:42}, e:{r:5,c:43}},
            // Col 45-46: BAST pada SPK/... (r4-r5 banner, c44-c45) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:44}, e:{r:5,c:45}},

            // Col 47: Letak/ Alamat (Berdiri Sendiri r3-r6, c46)
            {s:{r:3,c:46}, e:{r:6,c:46}},

            // Col 48-52: PIHAK PENYEDIA (Top Banner r3-r4, c47-c51)
            {s:{r:3,c:47}, e:{r:4,c:51}},
            {s:{r:5,c:47}, e:{r:6,c:47}},  // Nama Penyedia
            {s:{r:5,c:48}, e:{r:6,c:48}},  // Pemilik Penyedia
            {s:{r:5,c:49}, e:{r:5,c:50}},  // Rekening -> r6: Nama Rek (c49), Nomor Rek (c50)
            {s:{r:5,c:51}, e:{r:6,c:51}},  // Alamat Penyedia

            // Col 53-54: Pejabat Pembuat Komitmen (r3-r4, c52-c53)
            {s:{r:3,c:52}, e:{r:4,c:53}},
            {s:{r:5,c:52}, e:{r:6,c:52}},  // Nama
            {s:{r:5,c:53}, e:{r:6,c:53}},  // NIP

            // Col 55: KET. (berdiri sendiri r3-r6, c54)
            {s:{r:3,c:54}, e:{r:6,c:54}}
        ], 55, kibCTitleRows.length, kibCRows.length, true);
        wsKibC['!merges'].push(...getKibSignatureMerges(kibCSignStartRow, 55, 41, 1, 6, 54));

        applyUnified4StepMasterSheetStyling(wsKibC, kibCRows.length, 55, 30, kibCTitleRows.length, true);
        applySignatureBlockStyling(wsKibC, kibCSignStartRow, 55);
        if (filterCat === 'all' || filterCat === 'KIB C') {
            XLSX.utils.book_append_sheet(wb, wsKibC, filterCat === 'all' ? "4. C" : "KIB C - Gedung & Bangunan");
        }

        // ------------------------------------------------------------------------
        // 5. KIB D (JALAN, IRIGASI DAN JARINGAN) - COMPLETE 4-STEP MASTER SHEET (54 KOLOM)
        // Sesuai Format Baku Gambar: 1-15 (Langkah 1-2), 16-45 (Langkah 3), 46-54 (Langkah 4)
        // ------------------------------------------------------------------------
        const kibDTitleRows = getKibTitleRows("JALAN, IRIGASI DAN JARINGAN", yearLabel, filterTw);
        const kibDRows = [
            ...kibDTitleRows,
            // r3: Main Banner (54 kolom)
            [
                "NO",
                "Program Pengadaan SIPD", "",
                "Kegiatan Pengadaan SIPD", "",
                "Sub Kegiatan Pengadaan SIPD", "",
                "BELANJA MODAL", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE / " + yearLabel, "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "Letak/ Alamat",
                "PIHAK PENYEDIA", "", "", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            // r4: Sub Banner (Level 2)
            [
                "",
                "", "",
                "", "",
                "", "",
                "Rekening Belanja Untuk Pengadaan SIPD", "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH ANGGARAN (Rp)",
                "JUMLAH REALISASI (Rp)",
                "NAMA BARANG (Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang (Kode Sub Sub Rincian Objek PMDN 108)",
                "Luas  (P X L)",
                "Kondisi / Spesifikasi", "", "",
                "Jenis Bangunan", "", "", "", "", "",
                "Riwayat Pembelian", "", "", "", "", "", "", "",
                "VOLUME", "",
                "Nilai Satuan Barang (Rp)", "", "",
                "Total Nilai Barang (Rp)",
                "SP2D", "",
                "BAST pada SPK/Surat Pesanan/Kwitansi/Invoice", "",
                "",
                "", "",
                "Rekening", "",
                "",
                "", "",
                ""
            ],
            // r5: Sub Header (Level 3)
            [
                "",
                "Kode", "Nama Program",
                "Kode", "Nama Kegiatan Pengadaan",
                "Kode", "Nama Sub Kegiatan Pengadaan",
                "Kode Rek", "Nama Belanja Pengadaan",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "",
                "",
                "",
                "",
                "",
                "(B,KB,RB)", "Bertingkat/ Tidak", "Beton/ Tidak",
                "Status Tanah",
                "Kode aset Tanah",
                "Baru",
                "Kapitalisasi", "", "",
                "SPK", "",
                "Surat Pesanan", "",
                "Kwitansi", "",
                "Invoice", "",
                "Jumlah", "Nama Satuan Barang",
                "Nilai Perencanaan (Rp)", "Nilai Fisik (Rp)", "Nilai Pengawasan",
                "",
                "", "",
                "", "",
                "",
                "Nama Penyedia", "Pemilik Penyedia",
                "", "",
                "Alamat Penyedia",
                "Nama", "NIP",
                ""
            ],
            // r6: Technical Sub Detail (Level 4)
            [
                "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "",
                "", "", "",
                "Nibar", "Tahun Induk", "Nilai Induk s/d " + (parseInt(yearLabel) - 1 || '2025'),
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "", "",
                "", "", "",
                "",
                "NOMOR", "TANGGAL",
                "NOMOR", "TANGGAL",
                "",
                "", "", "Nama Rek", "Nomor Rek", "",
                "", "",
                ""
            ],
            // r7: Column Numbers (1 s/d 54)
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45",
                "46", "47", "48", "49", "50", "51", "52", "53", "54"
            ]
        ];

        // ── Grouping KIB D Berdasarkan Sub Rincian Objek PMDN 108 ──────────────
        const kibDGroups = {};
        categories['KIB D'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.3.4.01.01.01');
            if (!kibDGroups[groupKey]) kibDGroups[groupKey] = [];
            kibDGroups[groupKey].push(item);
        });

        let globalKibDNo = 1;
        let kibDTotalAnggaran = 0, kibDTotalRealisasi = 0, kibDTotalLuas = 0, kibDTotalUnit = 0;
        let kibDTotalPerencanaan = 0, kibDTotalFisik = 0, kibDTotalPengawasan = 0, kibDTotalNilaiBarang = 0;

        Object.keys(kibDGroups).forEach(groupKey => {
            const groupItems = kibDGroups[groupKey];
            let groupAnggaranTotal = 0;
            groupItems.forEach(it => {
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0));
            });
            const groupRealisasiTotal = groupItems.reduce((acc, curr) => {
                const val = typeof curr.total_realisasi_num === 'number' ? curr.total_realisasi_num : (parseFloat(curr.total_realisasi) || 0);
                return acc + val;
            }, 0);

            kibDTotalAnggaran += groupAnggaranTotal;
            kibDTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                const nilaiPerencanaan = parseFloat(item.jaringan_nilai_perencanaan) || parseFloat(item.nilai_perencanaan) || 0;
                const nilaiFisik = parseFloat(item.jaringan_nilai_fisik) || parseFloat(item.nilai_fisik) || totalVal;
                const nilaiPengawasan = parseFloat(item.jaringan_nilai_pengawasan) || parseFloat(item.nilai_pengawasan) || 0;
                const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan) || totalVal;
                const jumlahUnit = parseInt(item.jumlah_volume) || parseInt(item.jumlah_unit) || 1;
                const luasM2 = parseFloat(item.jaringan_luas_m2) || parseFloat(item.luas_m2) || 0;

                kibDTotalLuas += luasM2;
                kibDTotalUnit += jumlahUnit;
                kibDTotalPerencanaan += nilaiPerencanaan;
                kibDTotalFisik += nilaiFisik;
                kibDTotalPengawasan += nilaiPengawasan;
                kibDTotalNilaiBarang += totalNilaiBarang;

                const kondisiLabel = item.kondisi ? (item.kondisi === 'B' ? 'Baik' : (item.kondisi === 'KB' ? 'Kurang Baik' : (item.kondisi === 'RB' ? 'Rusak Berat' : item.kondisi))) : 'Baik';

                let col1to15 = [];
                if (isFirstRowInGroup) {
                    col1to15 = [
                        globalKibDNo++,
                        item.program_kode || '-',
                        item.program_nama || '-',
                        item.kegiatan_kode || '-',
                        item.kegiatan_nama || '-',
                        item.sub_kegiatan_kode || '-',
                        item.sub_kegiatan_nama || '-',
                        item.rekening_kode || '-',
                        item.rekening_nama || '-',
                        item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.4'),
                        item.jenis_aset_nama || 'JALAN, IRIGASI DAN JARINGAN',
                        item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                        item.sub_rincian_nama || '-',
                        groupAnggaranTotal,
                        groupRealisasiTotal
                    ];
                    isFirstRowInGroup = false;
                } else {
                    col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                }

                const isBaru = (item.jaringan_is_baru === 'Baru' || item.gedung_is_baru === 'Baru' || !item.jaringan_is_baru);

                kibDRows.push([
                    ...col1to15,                                                 // c0-c14: cols 1-15
                    item.nama_barang || '-',                                     // c15: col 16 (Nama Barang)
                    item.kode_barang || '-',                                     // c16: col 17 (Kode Barang)
                    luasM2,                                                      // c17: col 18 (Luas M²)
                    kondisiLabel,                                                // c18: col 19 (Kondisi B/KB/RB)
                    item.jaringan_bertingkat || item.bertingkat || item.gedung_bertingkat || '-', // c19: col 20 (Bertingkat/ Tidak)
                    item.jaringan_beton || item.beton || item.gedung_beton || '-',                // c20: col 21 (Beton/ Tidak)
                    item.jaringan_status_tanah || item.gedung_status_tanah || 'Tanah Hak Pakai RSUD', // c21: col 22 (Status Tanah)
                    item.jaringan_kode_aset_tanah || item.gedung_kode_aset_tanah || '-', // c22: col 23 (Kode aset Tanah)
                    isBaru ? '1' : '-',                                          // c23: col 24 (Baru: '1' jika Baru, '-' jika Lama)
                    getAstapNibar(item),                                         // c24: col 25 (Nibar diambil dari astaps register)
                    isBaru ? '-' : (item.jaringan_kapitalisasi_tahun_induk || item.gedung_kapitalisasi_tahun_induk || '-'),                 // c25: col 26 (Tahun Induk)
                    isBaru ? '-' : (parseFloat(item.jaringan_kapitalisasi_nilai_induk || item.gedung_kapitalisasi_nilai_induk) || 0),       // c26: col 27 (Nilai Induk s/d ...)
                    item.spk_nomor || '-',                                       // c27: col 28 (SPK No)
                    formatAstapDate(item.spk_tanggal),                           // c28: col 29 (SPK Tgl)
                    item.surat_pesanan_nomor || '-',                             // c29: col 30 (Surat Pesanan No)
                    formatAstapDate(item.surat_pesanan_tanggal),                 // c30: col 31 (Surat Pesanan Tgl)
                    item.kwitansi_nomor || '-',                                  // c31: col 32 (Kwitansi No)
                    formatAstapDate(item.kwitansi_tanggal),                      // c32: col 33 (Kwitansi Tgl)
                    item.faktur_nomor || '-',                                    // c33: col 34 (Invoice No)
                    formatAstapDate(item.faktur_tanggal),                        // c34: col 35 (Invoice Tgl)
                    jumlahUnit,                                                  // c35: col 36 (Jumlah)
                    item.satuan || 'Meter / Jaringan',                           // c36: col 37 (Nama Satuan Barang)
                    nilaiPerencanaan,                                            // c37: col 38 (Nilai Perencanaan)
                    nilaiFisik,                                                  // c38: col 39 (Nilai Fisik)
                    nilaiPengawasan,                                             // c39: col 40 (Nilai Pengawasan)
                    totalNilaiBarang,                                            // c40: col 41 (Total Nilai Barang)
                    item.sp2d_nomor || '-',                                      // c41: col 42 (SP2D NOMOR)
                    formatAstapDate(item.sp2d_tanggal),                          // c42: col 43 (SP2D TANGGAL)
                    item.bast_dokumen_nomor || '-',                              // c43: col 44 (BAST NOMOR)
                    formatAstapDate(item.bast_dokumen_tanggal),                  // c44: col 45 (BAST TANGGAL)
                    item.alamat_barang || '-',                                   // c45: col 46 (Letak/ Alamat)
                    ...getStep4Columns(item)                                     // c46-c53: cols 47-54
                ]);
            });
        });

        // ── Baris Footer Total KIB D (54 Kolom) ──────────────────────────────────
        const kibDFooterRow = Array(54).fill("");
        kibDFooterRow[0] = "JUMLAH";
        kibDFooterRow[13] = kibDTotalAnggaran;
        kibDFooterRow[14] = kibDTotalRealisasi;
        kibDFooterRow[17] = kibDTotalLuas;
        kibDFooterRow[35] = kibDTotalUnit;
        kibDFooterRow[37] = kibDTotalPerencanaan;
        kibDFooterRow[38] = kibDTotalFisik;
        kibDFooterRow[39] = kibDTotalPengawasan;
        kibDFooterRow[40] = kibDTotalNilaiBarang;
        kibDRows.push(kibDFooterRow);

        // Tanda Tangan KIB D (Format Baku RSUD Koesnadi)
        const kibDSignStartRow = kibDRows.length;
        const kibDSignRows = buildKibSignatureRows(54, 40, ppkNama, ppkNip, signDate, 1);
        kibDSignRows.forEach(r => kibDRows.push(r));

        const wsKibD = XLSX.utils.aoa_to_sheet(kibDRows);
        wsKibD['!cols'] = Array(54).fill({wch: 18});
        wsKibD['!cols'][0] = {wch: 6};
        wsKibD['!cols'][1] = {wch: 14}; wsKibD['!cols'][2] = {wch: 32};
        wsKibD['!cols'][3] = {wch: 14}; wsKibD['!cols'][4] = {wch: 28};
        wsKibD['!cols'][5] = {wch: 16}; wsKibD['!cols'][6] = {wch: 30};
        wsKibD['!cols'][7] = {wch: 18}; wsKibD['!cols'][8] = {wch: 30};
        wsKibD['!cols'][9] = {wch: 14}; wsKibD['!cols'][10] = {wch: 24};
        wsKibD['!cols'][11] = {wch: 18}; wsKibD['!cols'][12] = {wch: 32};
        wsKibD['!cols'][13] = {wch: 22}; wsKibD['!cols'][14] = {wch: 22};
        wsKibD['!cols'][15] = {wch: 32}; wsKibD['!cols'][16] = {wch: 22};
        wsKibD['!cols'][17] = {wch: 16}; wsKibD['!cols'][18] = {wch: 14};
        wsKibD['!cols'][19] = {wch: 20}; wsKibD['!cols'][20] = {wch: 18};
        wsKibD['!cols'][21] = {wch: 24}; wsKibD['!cols'][22] = {wch: 20};
        wsKibD['!cols'][23] = {wch: 16}; wsKibD['!cols'][24] = {wch: 22};
        wsKibD['!cols'][25] = {wch: 16}; wsKibD['!cols'][26] = {wch: 22};
        wsKibD['!cols'][27] = {wch: 22}; wsKibD['!cols'][28] = {wch: 14};
        wsKibD['!cols'][29] = {wch: 22}; wsKibD['!cols'][30] = {wch: 14};
        wsKibD['!cols'][31] = {wch: 22}; wsKibD['!cols'][32] = {wch: 14};
        wsKibD['!cols'][33] = {wch: 22}; wsKibD['!cols'][34] = {wch: 14};
        wsKibD['!cols'][35] = {wch: 18}; wsKibD['!cols'][36] = {wch: 20};
        wsKibD['!cols'][37] = {wch: 22}; wsKibD['!cols'][38] = {wch: 22};
        wsKibD['!cols'][39] = {wch: 22}; wsKibD['!cols'][40] = {wch: 22};
        wsKibD['!cols'][41] = {wch: 20}; wsKibD['!cols'][42] = {wch: 14};
        wsKibD['!cols'][43] = {wch: 28}; wsKibD['!cols'][44] = {wch: 14};
        wsKibD['!cols'][45] = {wch: 32}; wsKibD['!cols'][46] = {wch: 28};
        wsKibD['!cols'][47] = {wch: 24}; wsKibD['!cols'][48] = {wch: 24};
        wsKibD['!cols'][49] = {wch: 22}; wsKibD['!cols'][50] = {wch: 30};
        wsKibD['!cols'][51] = {wch: 24}; wsKibD['!cols'][52] = {wch: 22};
        wsKibD['!cols'][53] = {wch: 26};

        // ── Merge Cells KIB D (54 Kolom Sesuai Format Baku Gambar) ──────────────
        wsKibD['!merges'] = getKibMerges([
            // Col 1: NO (r3-r6, c0)
            {s:{r:3,c:0}, e:{r:6,c:0}},

            // Col 2-3: Program Pengadaan SIPD (r3-r4 banner c1-c2, r5-r6 sub-headers)
            {s:{r:3,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},  // Kode
            {s:{r:5,c:2}, e:{r:6,c:2}},  // Nama Program

            // Col 4-5: Kegiatan Pengadaan SIPD (r3-r4 banner c3-c4)
            {s:{r:3,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},  // Kode
            {s:{r:5,c:4}, e:{r:6,c:4}},  // Nama Kegiatan Pengadaan

            // Col 6-7: Sub Kegiatan Pengadaan SIPD (r3-r4 banner c5-c6)
            {s:{r:3,c:5}, e:{r:4,c:6}},
            {s:{r:5,c:5}, e:{r:6,c:5}},  // Kode
            {s:{r:5,c:6}, e:{r:6,c:6}},  // Nama Sub Kegiatan Pengadaan

            // Col 8-15: BELANJA MODAL (Top Banner r3, c7-c14)
            {s:{r:3,c:7}, e:{r:3,c:14}},
            // Col 8-9: Rekening Belanja Untuk Pengadaan SIPD
            {s:{r:4,c:7}, e:{r:4,c:8}},
            {s:{r:5,c:7}, e:{r:6,c:7}},   // Kode Rek
            {s:{r:5,c:8}, e:{r:6,c:8}},   // Nama Belanja Pengadaan
            // Col 10-11: Jenis Aset (PMDN 108)
            {s:{r:4,c:9}, e:{r:4,c:10}},
            {s:{r:5,c:9}, e:{r:6,c:9}},   // Kode
            {s:{r:5,c:10}, e:{r:6,c:10}}, // Nama Jenis Aset
            // Col 12-13: Sub Rincian Objek (PMDN 108)
            {s:{r:4,c:11}, e:{r:4,c:12}},
            {s:{r:5,c:11}, e:{r:6,c:11}}, // Kode
            {s:{r:5,c:12}, e:{r:6,c:12}}, // Nama Uraian Sub Rincian Objek
            // Col 14: JUMLAH ANGGARAN (Rp)
            {s:{r:4,c:13}, e:{r:6,c:13}},
            // Col 15: JUMLAH REALISASI (Rp)
            {s:{r:4,c:14}, e:{r:6,c:14}},

            // Col 16-45: RINCIAN BELANJA MODAL ... (Top Banner r3, c15-c44)
            {s:{r:3,c:15}, e:{r:3,c:44}},
            // Kolom standalone (r4-r6 merged):
            {s:{r:4,c:15}, e:{r:6,c:15}},  // Col 16: NAMA BARANG
            {s:{r:4,c:16}, e:{r:6,c:16}},  // Col 17: Kode Barang
            {s:{r:4,c:17}, e:{r:6,c:17}},  // Col 18: Luas  (P X L)
            // Col 19-21: Kondisi / Spesifikasi (r4 banner c18-c20)
            {s:{r:4,c:18}, e:{r:4,c:20}},
            {s:{r:5,c:18}, e:{r:6,c:18}},  // Col 19: (B,KB,RB)
            {s:{r:5,c:19}, e:{r:6,c:19}},  // Col 20: Bertingkat/ Tidak
            {s:{r:5,c:20}, e:{r:6,c:20}},  // Col 21: Beton/ Tidak

            // Col 22-27: Jenis Bangunan (Top banner on r4, c21-c26)
            {s:{r:4,c:21}, e:{r:4,c:26}},
            {s:{r:5,c:21}, e:{r:6,c:21}},  // Col 22: Status Tanah (r5-r6)
            {s:{r:5,c:22}, e:{r:6,c:22}},  // Col 23: Kode aset Tanah (r5-r6)
            {s:{r:5,c:23}, e:{r:6,c:23}},  // Col 24: Baru (r5-r6)
            // Col 25-27: Kapitalisasi (Sub banner on r5, c24-c26)
            {s:{r:5,c:24}, e:{r:5,c:26}},
            // r6: Nibar (c24), Tahun Induk (c25), Nilai Induk (c26)

            // Col 28-35: Riwayat Pembelian (r4 banner c27-c34)
            {s:{r:4,c:27}, e:{r:4,c:34}},
            {s:{r:5,c:27}, e:{r:5,c:28}},  // SPK (r5) -> r6: Nomor (c27), Tanggal (c28)
            {s:{r:5,c:29}, e:{r:5,c:30}},  // Surat Pesanan -> r6: Nomor (c29), Tanggal (c30)
            {s:{r:5,c:31}, e:{r:5,c:32}},  // Kwitansi -> r6: Nomor (c31), Tanggal (c32)
            {s:{r:5,c:33}, e:{r:5,c:34}},  // Invoice -> r6: Nomor (c33), Tanggal (c34)
            // Col 36-37: VOLUME (r4 banner c35-c36)
            {s:{r:4,c:35}, e:{r:4,c:36}},
            {s:{r:5,c:35}, e:{r:6,c:35}},  // Jumlah
            {s:{r:5,c:36}, e:{r:6,c:36}},  // Nama Satuan Barang
            // Col 38-40: Nilai Satuan Barang (Rp) (r4 banner c37-c39)
            {s:{r:4,c:37}, e:{r:4,c:39}},
            {s:{r:5,c:37}, e:{r:6,c:37}},  // Nilai Perencanaan (Rp)
            {s:{r:5,c:38}, e:{r:6,c:38}},  // Nilai Fisik (Rp)
            {s:{r:5,c:39}, e:{r:6,c:39}},  // Nilai Pengawasan
            // Col 41: Total Nilai Barang (Rp) (r4-r6, c40)
            {s:{r:4,c:40}, e:{r:6,c:40}},
            // Col 42-43: SP2D (r4-r5 banner, c41-c42) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:41}, e:{r:5,c:42}},
            // Col 44-45: BAST pada SPK/... (r4-r5 banner, c43-c44) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:43}, e:{r:5,c:44}},

            // Col 46: Letak/ Alamat (Berdiri Sendiri r3-r6, c45)
            {s:{r:3,c:45}, e:{r:6,c:45}},

            // Col 47-51: PIHAK PENYEDIA (Top Banner r3-r4, c46-c50)
            {s:{r:3,c:46}, e:{r:4,c:50}},
            {s:{r:5,c:46}, e:{r:6,c:46}},  // Nama Penyedia
            {s:{r:5,c:47}, e:{r:6,c:47}},  // Pemilik Penyedia
            {s:{r:5,c:48}, e:{r:5,c:49}},  // Rekening -> r6: Nama Rek (c48), Nomor Rek (c49)
            {s:{r:5,c:50}, e:{r:6,c:50}},  // Alamat Penyedia

            // Col 52-53: Pejabat Pembuat Komitmen (r3-r4, c51-c52)
            {s:{r:3,c:51}, e:{r:4,c:52}},
            {s:{r:5,c:51}, e:{r:6,c:51}},  // Nama
            {s:{r:5,c:52}, e:{r:6,c:52}},  // NIP

            // Col 54: KET. (berdiri sendiri r3-r6, c53)
            {s:{r:3,c:53}, e:{r:6,c:53}}
        ], 54, kibDTitleRows.length, kibDRows.length, true);
        wsKibD['!merges'].push(...getKibSignatureMerges(kibDSignStartRow, 54, 40, 1, 6, 53));

        applyUnified4StepMasterSheetStyling(wsKibD, kibDRows.length, 54, 30, kibDTitleRows.length, true);
        applySignatureBlockStyling(wsKibD, kibDSignStartRow, 54);
        if (filterCat === 'all' || filterCat === 'KIB D') {
            XLSX.utils.book_append_sheet(wb, wsKibD, filterCat === 'all' ? "5. D" : "KIB D - Jalan & Jaringan");
        }

        // ------------------------------------------------------------------------
        // 6. KIB E (ASET TETAP LAINNYA) - COMPLETE 4-STEP MASTER SHEET
        // Sesuai Format Baku: 1-15 (Langkah 1-2), 16-45 (Langkah 3), 46-54 (Langkah 4)
        // ------------------------------------------------------------------------
        const kibETitleRows = getKibTitleRows("ASET TETAP LAINNYA", yearLabel, filterTw);
        const kibERows = [
            ...kibETitleRows,
            // r3: Main Banner (53 kolom: c0-c14 Langkah 1-2, c15-c43 Langkah 3, c44-c52 Langkah 4)
            [
                "NO",
                "Program Pengadaan SIPD", "",
                "Kegiatan Pengadaan SIPD", "",
                "Sub Kegiatan Pengadaan SIPD", "",
                "BELANJA MODAL", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE / " + yearLabel,
                "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "",
                "RUANG /\nPEMEGANG",
                "PIHAK PENYEDIA", "", "", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            // r4: Sub-Banner Level 1 (53 kolom)
            [
                "",
                "", "",
                "", "",
                "", "",
                "Rekening Belanja Untuk Pengadaan SIPD", "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH ANGGARAN (Rp)",
                "JUMLAH REALISASI (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "BUKU PERPUSTAKAAN", "", "",
                "Barang Bercorak Kesenian / Kebudayaan", "", "", "",
                "Hewan Ternak / Tumbuhan", "", "",
                "Riwayat Pembelian", "", "", "", "", "", "", "",
                "VOLUME", "", "",
                "ADMINISTRASI PROYEK (Rp)",
                "Total Nilai Barang (Rp)",
                "SP2D", "",
                "BAST pada SPK/Surat Pesanan/Kwitansi/Invoice", "",
                "",
                "Nama Penyedia", "Pemilik Penyedia", "Rekening", "", "Alamat Penyedia",
                "", "",
                ""
            ],
            // r5: Sub-Banner Level 2 (53 kolom: c0-c14, c15-c43, c44-c52)
            [
                "",
                "Kode", "Nama Program",
                "Kode", "Nama Kegiatan Pengadaan",
                "Kode", "Nama Sub Kegiatan Pengadaan",
                "Kode Rek", "Nama Belanja Pengadaan",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "", "",
                "",
                "",
                "", "", "",
                "", "", "", "",
                "", "", "",
                "SPK", "", "Surat Pesanan", "", "Kwitansi", "", "Invoice", "",
                "", "", "",
                "",
                "",
                "", "",
                "", "",
                "",
                "", "", "Nama Rek", "Nomor Rek", "",
                "Nama", "NIP",
                ""
            ],
            // r6: Technical Sub Detail (Level 4 - 53 kolom: c0-c14, c15-c43, c44-c52)
            [
                "",
                "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "",
                "",
                "Judul", "Pencipta", "Spesifikasi",
                "Asal Daerah", "Pencipta", "Spesifikasi", "Bahan",
                "Ukuran (m/cm)", "Judul", "Spesifikasi",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Jumlah Barang", "Nama Satuan Barang", "Nilai Satuan Barang (Rp)",
                "",
                "",
                "NOMOR", "TANGGAL",
                "NOMOR", "TANGGAL",
                "",
                "", "", "", "", "",
                "", "",
                ""
            ],
            // r7: Column Numbers (53 kolom: 1-15, 16-44, 45-53)
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40 = 38+39", "41", "42", "43", "44",
                "45", "46", "47", "48", "49", "50", "51", "52", "53"
            ]
        ];

        // ── Grouping KIB E Berdasarkan Sub Rincian Objek PMDN 108 ──────────────
        const kibEGroups = {};
        categories['KIB E'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.3.5.01.01.01');
            if (!kibEGroups[groupKey]) kibEGroups[groupKey] = [];
            kibEGroups[groupKey].push(item);
        });

        let globalKibENo = 1;
        let kibETotalAnggaran = 0, kibETotalRealisasi = 0, kibETotalUnit = 0, kibETotalAdminProyek = 0, kibETotalNilaiBarang = 0;
        Object.keys(kibEGroups).forEach(groupKey => {
            const groupItems = kibEGroups[groupKey];
            let groupAnggaranTotal = 0;
            groupItems.forEach(it => {
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0));
            });
            const groupRealisasiTotal = groupItems.reduce((acc, curr) => {
                const val = typeof curr.total_realisasi_num === 'number' ? curr.total_realisasi_num : (parseFloat(curr.total_realisasi) || 0);
                return acc + val;
            }, 0);

            kibETotalAnggaran += groupAnggaranTotal;
            kibETotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                let spec = item.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch (e) { spec = {}; }
                } else if (!spec || typeof spec !== 'object') {
                    spec = {};
                }

                if (spec.lainnya_items && Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) {
                    spec.lainnya_items.forEach((lItem) => {
                        const qty = parseInt(lItem.lainnya_jumlah_barang) || 1;
                        const nilaiSatuan = parseFloat(lItem.lainnya_nilai_satuan) || 0;
                        const adminProyek = parseFloat(lItem.lainnya_administrasi_proyek) || 0;
                        const totalNilaiBarang = (qty * nilaiSatuan) + adminProyek;
                        const ruangUnit = lItem.ruang_pemegang_lainnya || lItem.ruang_pemegang || item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '-');

                        kibETotalUnit += qty;
                        kibETotalAdminProyek += adminProyek;
                        kibETotalNilaiBarang += totalNilaiBarang;

                        const subType = lItem.kib_e_sub_type || spec.kib_e_sub_type || 'buku';

                        const judulBuku = (subType === 'buku') ? (lItem.lainnya_buku_judul || lItem.lainnya_nama_barang || item.nama_barang || '-') : '-';
                        const penciptaBuku = (subType === 'buku') ? (lItem.lainnya_buku_pencipta || '-') : '-';
                        const spesifikasiBuku = (subType === 'buku') ? (lItem.lainnya_buku_spesifikasi || '-') : '-';

                        const asalKesenian = (subType === 'kesenian') ? (lItem.lainnya_kesenian_asal || '-') : '-';
                        const penciptaKesenian = (subType === 'kesenian') ? (lItem.lainnya_kesenian_pencipta || '-') : '-';
                        const spesifikasiKesenian = (subType === 'kesenian') ? (lItem.lainnya_kesenian_spesifikasi || '-') : '-';
                        const bahanKesenian = (subType === 'kesenian') ? (lItem.lainnya_kesenian_bahan || '-') : '-';
                        const ukuranKesenian = (subType === 'kesenian') ? (lItem.lainnya_kesenian_ukuran || '-') : '-';

                        const judulHewan = (subType === 'hewan_tumbuhan') ? (lItem.lainnya_hewan_judul || lItem.lainnya_hewan_jenis || lItem.lainnya_nama_barang || item.nama_barang || '-') : '-';
                        const spesifikasiHewan = (subType === 'hewan_tumbuhan') ? (lItem.lainnya_hewan_spesifikasi || '-') : '-';

                        let col1to15 = [];
                        if (isFirstRowInGroup) {
                            col1to15 = [
                                globalKibENo++,
                                item.program_kode || '-',
                                item.program_nama || '-',
                                item.kegiatan_kode || '-',
                                item.kegiatan_nama || '-',
                                item.sub_kegiatan_kode || '-',
                                item.sub_kegiatan_nama || '-',
                                item.rekening_kode || '-',
                                item.rekening_nama || '-',
                                item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.5'),
                                item.jenis_aset_nama || 'ASET TETAP LAINNYA',
                                item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                                item.sub_rincian_nama || '-',
                                groupAnggaranTotal,
                                groupRealisasiTotal
                            ];
                            isFirstRowInGroup = false;
                        } else {
                            col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                        }

                        kibERows.push([
                            ...col1to15,                                                 // c0-c14: cols 1-15
                            lItem.lainnya_nama_barang || item.nama_barang || '-',        // c15: col 16/17 (Nama Barang)
                            lItem.lainnya_kode_barang || item.kode_barang || '-',        // c16: col 17/18 (Kode Barang)
                            judulBuku,                                                   // c17: col 19 (Judul Buku)
                            penciptaBuku,                                                // c18: col 20 (Pencipta Buku)
                            spesifikasiBuku,                                             // c19: col 21 (Spesifikasi Buku)
                            asalKesenian,                                                // c20: col 22 (Asal Daerah)
                            penciptaKesenian,                                            // c21: col 23 (Pencipta Seni)
                            spesifikasiKesenian,                                         // c22: col 24 (Spesifikasi Seni)
                            bahanKesenian,                                               // c23: col 25 (Bahan Seni)
                            ukuranKesenian,                                              // c24: col 26 (Ukuran m/cm)
                            judulHewan,                                                  // c25: col 27 (Judul/Jenis Hewan)
                            spesifikasiHewan,                                            // c26: col 28 (Spesifikasi Hewan)
                            item.spk_nomor || '-',                                       // c27: col 29 (SPK No)
                            formatAstapDate(item.spk_tanggal),                           // c28: col 30 (SPK Tgl)
                            item.surat_pesanan_nomor || '-',                             // c29: col 31 (Surat Pesanan No)
                            formatAstapDate(item.surat_pesanan_tanggal),                 // c30: col 32 (Surat Pesanan Tgl)
                            item.kwitansi_nomor || '-',                                  // c31: col 33 (Kwitansi No)
                            formatAstapDate(item.kwitansi_tanggal),                      // c32: col 34 (Kwitansi Tgl)
                            item.faktur_nomor || '-',                                    // c33: col 35 (Invoice No)
                            formatAstapDate(item.faktur_tanggal),                        // c34: col 36 (Invoice Tgl)
                            qty,                                                         // c35: col 37 (Jumlah Barang)
                            lItem.lainnya_satuan || item.satuan || 'Eksemplar',          // c36: col 38 (Nama Satuan Barang)
                            nilaiSatuan,                                                 // c37: col 39 (Nilai Satuan Barang)
                            adminProyek,                                                 // c38: col 40 (Administrasi Proyek)
                            totalNilaiBarang,                                            // c39: col 41 (Total Nilai Barang)
                            item.sp2d_nomor || '-',                                      // c40: col 42 (SP2D NOMOR)
                            item.sp2d_tanggal || '-',                                    // c41: col 43 (SP2D TANGGAL)
                            item.bast_dokumen_nomor || '-',                              // c42: col 44 (BAST NOMOR)
                            item.bast_dokumen_tanggal || '-',                            // c43: col 45 (BAST TANGGAL)
                            ruangUnit,                                                   // c44: col 46 (RUANG / PEMEGANG)
                            ...getStep4Columns(item)                                     // c45-c52: cols 47-54 (Penyedia, PPK, Ket)
                        ]);
                    });
                } else {
                    const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                    const jumlahUnit = parseInt(item.jumlah_volume) || parseInt(item.jumlah_unit) || 1;
                    const hargaSatuan = parseFloat(item.harga_satuan) || (jumlahUnit > 0 ? (totalVal / jumlahUnit) : totalVal);
                    const adminProyek = parseFloat(item.administrasi_proyek) || parseFloat(item.admin_proyek) || 0;
                    const totalNilaiBarang = (hargaSatuan * jumlahUnit + adminProyek) || totalVal;
                    const ruangUnit = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '-');

                    kibETotalUnit += jumlahUnit;
                    kibETotalAdminProyek += adminProyek;
                    kibETotalNilaiBarang += totalNilaiBarang;

                    const subType = spec.kib_e_sub_type || (spec.buku_judul || item.judul_buku ? 'buku' : (spec.kesenian_asal || item.asal_kesenian ? 'kesenian' : (spec.hewan_jenis || item.judul_hewan ? 'hewan_tumbuhan' : '')));

                    const judulBuku = (subType === 'buku' || (!subType && (spec.buku_judul || item.judul_buku))) ? (spec.buku_judul || item.judul_buku || item.judul_pencipta || item.nama_barang || '-') : '-';
                    const penciptaBuku = (subType === 'buku' || (!subType && (spec.buku_pencipta || item.pencipta))) ? (spec.buku_pencipta || item.pencipta || item.judul_pencipta || '-') : '-';
                    const spesifikasiBuku = (subType === 'buku' || (!subType && (spec.buku_spesifikasi || item.spesifikasi))) ? (spec.buku_spesifikasi || item.spesifikasi || '-') : '-';

                    const asalKesenian = (subType === 'kesenian' || (!subType && (spec.kesenian_asal || item.asal_kesenian))) ? (spec.kesenian_asal || item.asal_kesenian || '-') : '-';
                    const penciptaKesenian = (subType === 'kesenian' || (!subType && (spec.kesenian_pencipta || item.pencipta_kesenian))) ? (spec.kesenian_pencipta || item.pencipta_kesenian || item.pencipta || '-') : '-';
                    const spesifikasiKesenian = (subType === 'kesenian' || (!subType && (spec.kesenian_spesifikasi || item.spesifikasi))) ? (spec.kesenian_spesifikasi || item.spesifikasi || '-') : '-';
                    const bahanKesenian = (subType === 'kesenian' || (!subType && (spec.kesenian_bahan || spec.bahan || item.bahan))) ? (spec.kesenian_bahan || spec.bahan || item.bahan || '-') : '-';
                    const ukuranKesenian = (subType === 'kesenian' || (!subType && (spec.kesenian_ukuran || spec.ukuran || item.ukuran))) ? (spec.kesenian_ukuran || spec.ukuran || item.ukuran || '-') : '-';

                    const judulHewan = (subType === 'hewan_tumbuhan' || (!subType && (spec.hewan_judul || spec.hewan_jenis || item.judul_hewan))) ? (spec.hewan_judul || spec.hewan_jenis || item.judul_hewan || item.nama_barang || '-') : '-';
                    const spesifikasiHewan = (subType === 'hewan_tumbuhan' || (!subType && (spec.hewan_spesifikasi || item.spesifikasi))) ? (spec.hewan_spesifikasi || item.spesifikasi || '-') : '-';

                    let col1to15 = [];
                    if (isFirstRowInGroup) {
                        col1to15 = [
                            globalKibENo++,
                            item.program_kode || '-',
                            item.program_nama || '-',
                            item.kegiatan_kode || '-',
                            item.kegiatan_nama || '-',
                            item.sub_kegiatan_kode || '-',
                            item.sub_kegiatan_nama || '-',
                            item.rekening_kode || '-',
                            item.rekening_nama || '-',
                            item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.5'),
                            item.jenis_aset_nama || 'ASET TETAP LAINNYA',
                            item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                            item.sub_rincian_nama || '-',
                            groupAnggaranTotal,
                            groupRealisasiTotal
                        ];
                        isFirstRowInGroup = false;
                    } else {
                        col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                    }

                    kibERows.push([
                        ...col1to15,                                                 // c0-c14: cols 1-15
                        item.nama_barang || '-',                                     // c15: col 16/17 (Nama Barang)
                        item.kode_barang || '-',                                     // c16: col 17/18 (Kode Barang)
                        judulBuku,                                                   // c17: col 19 (Judul Buku)
                        penciptaBuku,                                                // c18: col 20 (Pencipta Buku)
                        spesifikasiBuku,                                             // c19: col 21 (Spesifikasi Buku)
                        asalKesenian,                                                // c20: col 22 (Asal Daerah)
                        penciptaKesenian,                                            // c21: col 23 (Pencipta Seni)
                        spesifikasiKesenian,                                         // c22: col 24 (Spesifikasi Seni)
                        bahanKesenian,                                               // c23: col 25 (Bahan Seni)
                        ukuranKesenian,                                              // c24: col 26 (Ukuran m/cm)
                        judulHewan,                                                  // c25: col 27 (Judul/Jenis Hewan)
                        spesifikasiHewan,                                            // c26: col 28 (Spesifikasi Hewan)
                        item.spk_nomor || '-',                                       // c27: col 29 (SPK No)
                        formatAstapDate(item.spk_tanggal),                           // c28: col 30 (SPK Tgl)
                        item.surat_pesanan_nomor || '-',                             // c29: col 31 (Surat Pesanan No)
                        formatAstapDate(item.surat_pesanan_tanggal),                 // c30: col 32 (Surat Pesanan Tgl)
                        item.kwitansi_nomor || '-',                                  // c31: col 33 (Kwitansi No)
                        formatAstapDate(item.kwitansi_tanggal),                      // c32: col 34 (Kwitansi Tgl)
                        item.faktur_nomor || '-',                                    // c33: col 35 (Invoice No)
                        formatAstapDate(item.faktur_tanggal),                        // c34: col 36 (Invoice Tgl)
                        jumlahUnit,                                                  // c35: col 37 (Jumlah Barang)
                        item.satuan || 'Eks / Buah',                                 // c36: col 38 (Nama Satuan Barang)
                        hargaSatuan,                                                 // c37: col 39 (Nilai Satuan Barang)
                        adminProyek,                                                 // c38: col 40 (Administrasi Proyek)
                        totalNilaiBarang,                                            // c39: col 41 (Total Nilai Barang)
                        item.sp2d_nomor || '-',                                      // c40: col 42 (SP2D NOMOR)
                        item.sp2d_tanggal || '-',                                    // c41: col 43 (SP2D TANGGAL)
                        item.bast_dokumen_nomor || '-',                              // c42: col 44 (BAST NOMOR)
                        item.bast_dokumen_tanggal || '-',                            // c43: col 45 (BAST TANGGAL)
                        ruangUnit,                                                   // c44: col 46 (RUANG / PEMEGANG)
                        ...getStep4Columns(item)                                     // c45-c52: cols 47-54 (Penyedia, PPK, Ket)
                    ]);
                }
            });
        });

        // ── Baris Footer Total KIB E (53 Kolom) ──────────────────────────────────
        const kibEFooterRow = Array(53).fill("");
        kibEFooterRow[0] = "JUMLAH";
        kibEFooterRow[13] = kibETotalAnggaran;
        kibEFooterRow[14] = kibETotalRealisasi;
        kibEFooterRow[35] = kibETotalUnit;
        kibEFooterRow[38] = kibETotalAdminProyek;
        kibEFooterRow[39] = kibETotalNilaiBarang;
        kibERows.push(kibEFooterRow);

        // Tanda Tangan KIB E (Format Baku RSUD Koesnadi)
        const kibESignStartRow = kibERows.length;
        const kibESignRows = buildKibSignatureRows(53, 39, ppkNama, ppkNip, signDate, 1);
        kibESignRows.forEach(r => kibERows.push(r));

        const wsKibE = XLSX.utils.aoa_to_sheet(kibERows);
        wsKibE['!cols'] = Array(53).fill({wch: 18});
        wsKibE['!cols'][0] = {wch: 6};
        wsKibE['!cols'][1] = {wch: 14}; wsKibE['!cols'][2] = {wch: 32};
        wsKibE['!cols'][3] = {wch: 14}; wsKibE['!cols'][4] = {wch: 28};
        wsKibE['!cols'][5] = {wch: 16}; wsKibE['!cols'][6] = {wch: 30};
        wsKibE['!cols'][7] = {wch: 18}; wsKibE['!cols'][8] = {wch: 30};
        wsKibE['!cols'][9] = {wch: 14}; wsKibE['!cols'][10] = {wch: 24};
        wsKibE['!cols'][11] = {wch: 18}; wsKibE['!cols'][12] = {wch: 32};
        wsKibE['!cols'][13] = {wch: 22}; wsKibE['!cols'][14] = {wch: 22};
        wsKibE['!cols'][15] = {wch: 32}; wsKibE['!cols'][16] = {wch: 22};
        wsKibE['!cols'][17] = {wch: 24}; wsKibE['!cols'][18] = {wch: 20};
        wsKibE['!cols'][19] = {wch: 24}; wsKibE['!cols'][20] = {wch: 20};
        wsKibE['!cols'][21] = {wch: 20}; wsKibE['!cols'][22] = {wch: 24};
        wsKibE['!cols'][23] = {wch: 18}; wsKibE['!cols'][24] = {wch: 16};
        wsKibE['!cols'][25] = {wch: 20}; wsKibE['!cols'][26] = {wch: 22};
        wsKibE['!cols'][27] = {wch: 22}; wsKibE['!cols'][28] = {wch: 14};
        wsKibE['!cols'][29] = {wch: 22}; wsKibE['!cols'][30] = {wch: 14};
        wsKibE['!cols'][31] = {wch: 22}; wsKibE['!cols'][32] = {wch: 14};
        wsKibE['!cols'][33] = {wch: 22}; wsKibE['!cols'][34] = {wch: 14};
        wsKibE['!cols'][35] = {wch: 14}; wsKibE['!cols'][36] = {wch: 18};
        wsKibE['!cols'][37] = {wch: 22}; wsKibE['!cols'][38] = {wch: 22};
        wsKibE['!cols'][39] = {wch: 22}; wsKibE['!cols'][40] = {wch: 20};
        wsKibE['!cols'][41] = {wch: 14}; wsKibE['!cols'][42] = {wch: 28};
        wsKibE['!cols'][43] = {wch: 14}; wsKibE['!cols'][44] = {wch: 28};
        wsKibE['!cols'][45] = {wch: 28}; wsKibE['!cols'][46] = {wch: 24};
        wsKibE['!cols'][47] = {wch: 24}; wsKibE['!cols'][48] = {wch: 22};
        wsKibE['!cols'][49] = {wch: 30}; wsKibE['!cols'][50] = {wch: 24};
        wsKibE['!cols'][51] = {wch: 22}; wsKibE['!cols'][52] = {wch: 26};

        // ── Merge Cells KIB E (53 Kolom Sesuai Format Baku Gambar) ──────────────
        wsKibE['!merges'] = getKibMerges([
            // Col 1: NO (r3-r6, c0)
            {s:{r:3,c:0}, e:{r:6,c:0}},

            // Col 2-3: Program Pengadaan SIPD (r3-r4 banner c1-c2, r5-r6 sub-headers)
            {s:{r:3,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},  // Kode
            {s:{r:5,c:2}, e:{r:6,c:2}},  // Nama Program

            // Col 4-5: Kegiatan Pengadaan SIPD (r3-r4 banner c3-c4)
            {s:{r:3,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},  // Kode
            {s:{r:5,c:4}, e:{r:6,c:4}},  // Nama Kegiatan Pengadaan

            // Col 6-7: Sub Kegiatan Pengadaan SIPD (r3-r4 banner c5-c6)
            {s:{r:3,c:5}, e:{r:4,c:6}},
            {s:{r:5,c:5}, e:{r:6,c:5}},  // Kode
            {s:{r:5,c:6}, e:{r:6,c:6}},  // Nama Sub Kegiatan Pengadaan

            // Col 8-15: BELANJA MODAL (Top Banner r3, c7-c14)
            {s:{r:3,c:7}, e:{r:3,c:14}},
            // Col 8-9: Rekening Belanja Untuk Pengadaan SIPD
            {s:{r:4,c:7}, e:{r:4,c:8}},
            {s:{r:5,c:7}, e:{r:6,c:7}},   // Kode Rek
            {s:{r:5,c:8}, e:{r:6,c:8}},   // Nama Belanja Pengadaan
            // Col 10-11: Jenis Aset (PMDN 108)
            {s:{r:4,c:9}, e:{r:4,c:10}},
            {s:{r:5,c:9}, e:{r:6,c:9}},   // Kode
            {s:{r:5,c:10}, e:{r:6,c:10}}, // Nama Jenis Aset
            // Col 12-13: Sub Rincian Objek (PMDN 108)
            {s:{r:4,c:11}, e:{r:4,c:12}},
            {s:{r:5,c:11}, e:{r:6,c:11}}, // Kode
            {s:{r:5,c:12}, e:{r:6,c:12}}, // Nama Uraian Sub Rincian Objek
            // Col 14: JUMLAH ANGGARAN (Rp)
            {s:{r:4,c:13}, e:{r:6,c:13}},
            // Col 15: JUMLAH REALISASI (Rp)
            {s:{r:4,c:14}, e:{r:6,c:14}},

            // Col 16-44: RINCIAN BELANJA MODAL ... (Top Banner r3, c15-c43)
            {s:{r:3,c:15}, e:{r:3,c:43}},
            // Kolom standalone (r4-r6 merged):
            {s:{r:4,c:15}, e:{r:6,c:15}},  // Col 16/17: NAMA BARANG
            {s:{r:4,c:16}, e:{r:6,c:16}},  // Col 17/18: Kode Barang
            // Col 18-20: BUKU PERPUSTAKAAN (r4-r5 banner c17-c19) -> r6: Judul, Pencipta, Spesifikasi
            {s:{r:4,c:17}, e:{r:5,c:19}},
            // Col 21-24: Barang Bercorak Kesenian / Kebudayaan (r4-r5 banner c20-c23) -> r6: Asal Daerah, Pencipta, Spesifikasi, Bahan
            {s:{r:4,c:20}, e:{r:5,c:23}},
            // Col 25-27: Hewan Ternak / Tumbuhan (r4-r5 banner c24-c26) -> r6: Ukuran (m/cm), Judul, Spesifikasi
            {s:{r:4,c:24}, e:{r:5,c:26}},
            // Col 28-35: Riwayat Pembelian (r4 banner c27-c34)
            {s:{r:4,c:27}, e:{r:4,c:34}},
            {s:{r:5,c:27}, e:{r:5,c:28}},  // SPK (r5) -> r6: Nomor (c27), Tanggal (c28)
            {s:{r:5,c:29}, e:{r:5,c:30}},  // Surat Pesanan -> r6: Nomor (c29), Tanggal (c30)
            {s:{r:5,c:31}, e:{r:5,c:32}},  // Kwitansi -> r6: Nomor (c31), Tanggal (c32)
            {s:{r:5,c:33}, e:{r:5,c:34}},  // Invoice -> r6: Nomor (c33), Tanggal (c34)
            // Col 36-38: VOLUME (r4-r5 banner c35-c37) -> r6: Jumlah Barang, Nama Satuan Barang, Nilai Satuan Barang (Rp)
            {s:{r:4,c:35}, e:{r:5,c:37}},
            // Col 39: ADMINISTRASI PROYEK (Rp) (r4-r6, c38)
            {s:{r:4,c:38}, e:{r:6,c:38}},
            // Col 40: Total Nilai Barang (Rp) (r4-r6, c39)
            {s:{r:4,c:39}, e:{r:6,c:39}},
            // Col 41-42: SP2D (r4-r5 banner, c40-c41) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:40}, e:{r:5,c:41}},
            // Col 43-44: BAST pada SPK/... (r4-r5 banner, c42-c43) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:42}, e:{r:5,c:43}},

            // Col 45: RUANG / PEMEGANG (Berdiri Sendiri r3-r6, c44)
            {s:{r:3,c:44}, e:{r:6,c:44}},

            // Col 46-50: PIHAK PENYEDIA (Top Banner r3, c45-c49)
            {s:{r:3,c:45}, e:{r:3,c:49}},
            {s:{r:4,c:45}, e:{r:6,c:45}},  // Nama Penyedia (r4-r6, c45)
            {s:{r:4,c:46}, e:{r:6,c:46}},  // Pemilik Penyedia (r4-r6, c46)
            {s:{r:4,c:47}, e:{r:4,c:48}},  // Rekening (r4, c47-c48)
            {s:{r:5,c:47}, e:{r:6,c:47}},  // Nama Rek (r5-r6, c47)
            {s:{r:5,c:48}, e:{r:6,c:48}},  // Nomor Rek (r5-r6, c48)
            {s:{r:4,c:49}, e:{r:6,c:49}},  // Alamat Penyedia (r4-r6, c49)

            // Col 51-52: Pejabat Pembuat Komitmen (Top Banner r3-r4, c50-c51)
            {s:{r:3,c:50}, e:{r:4,c:51}},
            {s:{r:5,c:50}, e:{r:6,c:50}},  // Nama (r5-r6, c50)
            {s:{r:5,c:51}, e:{r:6,c:51}},  // NIP (r5-r6, c51)

            // Col 53: KET. (Berdiri Sendiri r3-r6, c52)
            {s:{r:3,c:52}, e:{r:6,c:52}}
        ], 53, kibETitleRows.length, kibERows.length, true);
        wsKibE['!merges'].push(...getKibSignatureMerges(kibESignStartRow, 53, 39, 1, 6, 52));

        applyUnified4StepMasterSheetStyling(wsKibE, kibERows.length, 53, 29, kibETitleRows.length, true);
        applySignatureBlockStyling(wsKibE, kibESignStartRow, 53);
        if (filterCat === 'all' || filterCat === 'KIB E') {
            XLSX.utils.book_append_sheet(wb, wsKibE, filterCat === 'all' ? "6. E" : "KIB E - Aset Tetap Lainnya");
        }

        // ------------------------------------------------------------------------
        // 7. KIB F (KONSTRUKSI DALAM PENGERJAAN) - COMPLETE 4-STEP MASTER SHEET (55 KOLOM)
        // Sesuai Format Baku: 1-15 (Langkah 1-2), 16-46 (Langkah 3), 47-55 (Langkah 4)
        // ------------------------------------------------------------------------
        const kibFTitleRows = getKibTitleRows("KONSTRUKSI DALAM PENGERJAAN", yearLabel, filterTw);
        const kibFRows = [
            ...kibFTitleRows,
            // r3: Main Banner (55 kolom: c0-c14 Langkah 1-2, c15-c45 Langkah 3, c46-c54 Langkah 4)
            [
                "NO",
                "Program Pengadaan SIPD", "",
                "Kegiatan Pengadaan SIPD", "",
                "Sub Kegiatan Pengadaan SIPD", "",
                "BELANJA MODAL", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE / " + yearLabel,
                "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "",
                "Letak/ Alamat",
                "PIHAK PENYEDIA", "", "", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            // r4: Sub-Banner Level 1 (55 kolom)
            [
                "",
                "", "",
                "", "",
                "", "",
                "Rekening Belanja Untuk Pengadaan SIPD", "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH ANGGARAN (Rp)",
                "JUMLAH REALISASI (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "Luas Lantai\n(m²)",
                "Kondisi / Spesifikasi", "", "",
                "Jenis Bangunan", "", "", "", "", "",
                "Riwayat Pembelian", "", "", "", "", "", "", "",
                "VOLUME", "",
                "Nilai Barang (Rp)", "", "", "",
                "Total Nilai Barang (Rp)",
                "BAST pada SPK/Surat Pesanan/Kwitansi/Invoice", "",
                "SP2D", "",
                "",
                "Nama Penyedia", "Pemilik Penyedia", "Rekening", "", "Alamat Penyedia",
                "", "",
                ""
            ],
            // r5: Sub-Banner Level 2 (55 kolom)
            [
                "",
                "Kode", "Nama Program",
                "Kode", "Nama Kegiatan Pengadaan",
                "Kode", "Nama Sub Kegiatan Pengadaan",
                "Kode Rek", "Nama Belanja Pengadaan",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "", "",
                "",
                "",
                "",
                "(B,KB,RB)", "Bertingkat/\ntidak", "Beton/\ntidak",
                "Status Tanah", "Kode aset\nTanah", "Baru",
                "Kapitalisasi", "", "",
                "SPK", "", "Surat Pesanan", "", "Kwitansi", "", "Invoice", "",
                "Jumlah\nBangunan", "Nama Satuan\nBarang",
                "Nilai Perencanaan\n(Rp)", "Nilai Fisik (Rp)", "Nilai Pengawasan", "Nilai AP",
                "",
                "", "",
                "", "",
                "",
                "", "", "Nama Rek", "Nomor Rek", "",
                "Nama", "NIP",
                ""
            ],
            // r6: Technical Sub Detail (Level 4 - 55 kolom)
            [
                "",
                "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "",
                "Nibar", "Tahun\nInduk", "Nilai Induk s/d\n" + (parseInt(yearLabel) - 1 || '2025'),
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "", "",
                "", "", "", "",
                "",
                "NOMOR", "TANGGAL",
                "NOMOR", "TANGGAL",
                "",
                "", "", "", "", "",
                "", "",
                ""
            ],
            // r7: Column Numbers (55 kolom)
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41", "42", "43", "44", "45", "46",
                "47", "48", "49", "50", "51", "52", "53", "54", "55"
            ]
        ];

        // ── Grouping KIB F Berdasarkan Sub Rincian Objek PMDN 108 ──────────────
        const kibFGroups = {};
        categories['KIB F'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.3.6.01.01.01');
            if (!kibFGroups[groupKey]) kibFGroups[groupKey] = [];
            kibFGroups[groupKey].push(item);
        });

        let globalKibFNo = 1;
        let kibFTotalAnggaran = 0, kibFTotalRealisasi = 0, kibFTotalLuas = 0, kibFTotalUnit = 0, kibFTotalPerencanaan = 0, kibFTotalFisik = 0, kibFTotalPengawasan = 0, kibFTotalAdminProyek = 0, kibFTotalNilaiBarang = 0;
        Object.keys(kibFGroups).forEach(groupKey => {
            const groupItems = kibFGroups[groupKey];
            let groupAnggaranTotal = 0;
            groupItems.forEach(it => {
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0));
            });
            const groupRealisasiTotal = groupItems.reduce((acc, curr) => {
                const val = typeof curr.total_realisasi_num === 'number' ? curr.total_realisasi_num : (parseFloat(curr.total_realisasi) || 0);
                return acc + val;
            }, 0);

            kibFTotalAnggaran += groupAnggaranTotal;
            kibFTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                const nilaiPerencanaan = parseFloat(item.gedung_nilai_perencanaan) || parseFloat(item.nilai_perencanaan) || 0;
                const nilaiFisik = parseFloat(item.gedung_nilai_fisik) || parseFloat(item.nilai_fisik) || totalVal;
                const nilaiPengawasan = parseFloat(item.gedung_nilai_pengawasan) || parseFloat(item.nilai_pengawasan) || 0;
                const adminProyek = parseFloat(item.administrasi_proyek) || parseFloat(item.admin_proyek) || 0;
                const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan + adminProyek) || totalVal;
                const jumlahBangunan = parseInt(item.jumlah_volume) || parseInt(item.jumlah_unit) || 1;
                const luasM2 = parseFloat(item.luas_m2) || parseFloat(item.gedung_luas_m2) || 0;
                const kondisiLabel = item.kondisi ? (item.kondisi === 'B' ? 'Baik' : (item.kondisi === 'KB' ? 'Kurang Baik' : (item.kondisi === 'RB' ? 'Rusak Berat' : item.kondisi))) : 'Baik';

                kibFTotalLuas += luasM2;
                kibFTotalUnit += jumlahBangunan;
                kibFTotalPerencanaan += nilaiPerencanaan;
                kibFTotalFisik += nilaiFisik;
                kibFTotalPengawasan += nilaiPengawasan;
                kibFTotalAdminProyek += adminProyek;
                kibFTotalNilaiBarang += totalNilaiBarang;

                let col1to15 = [];
                if (isFirstRowInGroup) {
                    col1to15 = [
                        globalKibFNo++,
                        item.program_kode || '-',
                        item.program_nama || '-',
                        item.kegiatan_kode || '-',
                        item.kegiatan_nama || '-',
                        item.sub_kegiatan_kode || '-',
                        item.sub_kegiatan_nama || '-',
                        item.rekening_kode || '-',
                        item.rekening_nama || '-',
                        item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.3.6'),
                        item.jenis_aset_nama || 'KONSTRUKSI DALAM PENGERJAAN',
                        item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                        item.sub_rincian_nama || '-',
                        groupAnggaranTotal,
                        groupRealisasiTotal
                    ];
                    isFirstRowInGroup = false;
                } else {
                    col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                }

                kibFRows.push([
                    ...col1to15,                                                 // c0-c14: cols 1-15
                    item.nama_barang || '-',                                     // c15: col 16 (Nama Barang)
                    item.kode_barang || '-',                                     // c16: col 17 (Kode Barang)
                    luasM2,                                                      // c17: col 18 (Luas Lantai M²)
                    kondisiLabel,                                                // c18: col 19 (Kondisi B/KB/RB)
                    item.gedung_bertingkat || 'Bertingkat',                      // c19: col 20 (Bertingkat / Tidak)
                    item.gedung_beton || 'Beton',                                // c20: col 21 (Beton / Tidak)
                    item.gedung_status_tanah || 'Tanah Hak Pakai RSUD',          // c21: col 22 (Status Tanah)
                    item.gedung_kode_aset_tanah || '-',                          // c22: col 23 (Kode aset Tanah)
                    item.gedung_is_baru || 'Baru',                               // c23: col 24 (Baru)
                    getAstapNibar(item),                                         // c24: col 25 (Nibar diambil dari astaps register)
                    item.gedung_kapitalisasi_tahun_induk || '-',                 // c25: col 26 (Tahun Induk)
                    parseFloat(item.gedung_kapitalisasi_nilai_induk) || 0,       // c26: col 27 (Nilai Induk s/d ...)
                    item.spk_nomor || '-',                                       // c27: col 28 (SPK No)
                    formatAstapDate(item.spk_tanggal),                           // c28: col 29 (SPK Tgl)
                    item.surat_pesanan_nomor || '-',                             // c29: col 30 (Surat Pesanan No)
                    formatAstapDate(item.surat_pesanan_tanggal),                 // c30: col 31 (Surat Pesanan Tgl)
                    item.kwitansi_nomor || '-',                                  // c31: col 32 (Kwitansi No)
                    formatAstapDate(item.kwitansi_tanggal),                      // c32: col 33 (Kwitansi Tgl)
                    item.faktur_nomor || '-',                                    // c33: col 34 (Invoice No)
                    formatAstapDate(item.faktur_tanggal),                        // c34: col 35 (Invoice Tgl)
                    jumlahBangunan,                                              // c35: col 36 (Jumlah Bangunan)
                    item.satuan || 'Unit / Bangunan',                            // c36: col 37 (Nama Satuan Barang)
                    nilaiPerencanaan,                                            // c37: col 38 (Nilai Perencanaan)
                    nilaiFisik,                                                  // c38: col 39 (Nilai Fisik)
                    nilaiPengawasan,                                             // c39: col 40 (Nilai Pengawasan)
                    adminProyek,                                                 // c40: col 41 (Nilai AP)
                    totalNilaiBarang,                                            // c41: col 42 (Total Nilai Barang)
                    item.bast_dokumen_nomor || '-',                              // c42: col 43 (BAST NOMOR)
                    formatAstapDate(item.bast_dokumen_tanggal),                  // c43: col 44 (BAST TANGGAL)
                    item.sp2d_nomor || '-',                                      // c44: col 45 (SP2D NOMOR)
                    formatAstapDate(item.sp2d_tanggal),                          // c45: col 46 (SP2D TANGGAL)
                    item.alamat_barang || '-',                                   // c46: col 47 (Letak/ Alamat)
                    ...getStep4Columns(item)                                     // c47-c54: cols 48-55
                ]);
            });
        });

        // ── Baris Footer Total KIB F (55 Kolom) ──────────────────────────────────
        const kibFFooterRow = Array(55).fill("");
        kibFFooterRow[0] = "JUMLAH";
        kibFFooterRow[13] = kibFTotalAnggaran;
        kibFFooterRow[14] = kibFTotalRealisasi;
        kibFFooterRow[17] = kibFTotalLuas;
        kibFFooterRow[35] = kibFTotalUnit;
        kibFFooterRow[37] = kibFTotalPerencanaan;
        kibFFooterRow[38] = kibFTotalFisik;
        kibFFooterRow[39] = kibFTotalPengawasan;
        kibFFooterRow[40] = kibFTotalAdminProyek;
        kibFFooterRow[41] = kibFTotalNilaiBarang;
        kibFRows.push(kibFFooterRow);

        // Tanda Tangan KIB F (Format Baku RSUD Koesnadi)
        const kibFSignStartRow = kibFRows.length;
        const kibFSignRows = buildKibSignatureRows(55, 41, ppkNama, ppkNip, signDate, 1);
        kibFSignRows.forEach(r => kibFRows.push(r));

        const wsKibF = XLSX.utils.aoa_to_sheet(kibFRows);
        wsKibF['!cols'] = Array(55).fill({wch: 18});
        wsKibF['!cols'][0] = {wch: 6};
        wsKibF['!cols'][1] = {wch: 14}; wsKibF['!cols'][2] = {wch: 32};
        wsKibF['!cols'][3] = {wch: 14}; wsKibF['!cols'][4] = {wch: 28};
        wsKibF['!cols'][5] = {wch: 16}; wsKibF['!cols'][6] = {wch: 30};
        wsKibF['!cols'][7] = {wch: 18}; wsKibF['!cols'][8] = {wch: 30};
        wsKibF['!cols'][9] = {wch: 14}; wsKibF['!cols'][10] = {wch: 24};
        wsKibF['!cols'][11] = {wch: 18}; wsKibF['!cols'][12] = {wch: 32};
        wsKibF['!cols'][13] = {wch: 22}; wsKibF['!cols'][14] = {wch: 22};
        wsKibF['!cols'][15] = {wch: 32}; wsKibF['!cols'][16] = {wch: 22};
        wsKibF['!cols'][17] = {wch: 16}; wsKibF['!cols'][18] = {wch: 14};
        wsKibF['!cols'][19] = {wch: 18}; wsKibF['!cols'][20] = {wch: 18};
        wsKibF['!cols'][21] = {wch: 24}; wsKibF['!cols'][22] = {wch: 20};
        wsKibF['!cols'][23] = {wch: 16}; wsKibF['!cols'][24] = {wch: 22};
        wsKibF['!cols'][25] = {wch: 16}; wsKibF['!cols'][26] = {wch: 22};
        wsKibF['!cols'][27] = {wch: 22}; wsKibF['!cols'][28] = {wch: 14};
        wsKibF['!cols'][29] = {wch: 22}; wsKibF['!cols'][30] = {wch: 14};
        wsKibF['!cols'][31] = {wch: 22}; wsKibF['!cols'][32] = {wch: 14};
        wsKibF['!cols'][33] = {wch: 22}; wsKibF['!cols'][34] = {wch: 14};
        wsKibF['!cols'][35] = {wch: 18}; wsKibF['!cols'][36] = {wch: 20};
        wsKibF['!cols'][37] = {wch: 22}; wsKibF['!cols'][38] = {wch: 22};
        wsKibF['!cols'][39] = {wch: 22}; wsKibF['!cols'][40] = {wch: 20};
        wsKibF['!cols'][41] = {wch: 22}; wsKibF['!cols'][42] = {wch: 28};
        wsKibF['!cols'][43] = {wch: 14}; wsKibF['!cols'][44] = {wch: 20};
        wsKibF['!cols'][45] = {wch: 14}; wsKibF['!cols'][46] = {wch: 32};
        wsKibF['!cols'][47] = {wch: 28}; wsKibF['!cols'][48] = {wch: 24};
        wsKibF['!cols'][49] = {wch: 24}; wsKibF['!cols'][50] = {wch: 22};
        wsKibF['!cols'][51] = {wch: 30}; wsKibF['!cols'][52] = {wch: 24};
        wsKibF['!cols'][53] = {wch: 22}; wsKibF['!cols'][54] = {wch: 26};

        // ── Merge Cells KIB F (55 Kolom Sesuai Format Baku Gambar) ──────────────
        wsKibF['!merges'] = getKibMerges([
            // Col 1: NO (r3-r6, c0)
            {s:{r:3,c:0}, e:{r:6,c:0}},

            // Col 2-3: Program Pengadaan SIPD (r3-r4 banner c1-c2, r5-r6 sub-headers)
            {s:{r:3,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},  // Kode
            {s:{r:5,c:2}, e:{r:6,c:2}},  // Nama Program

            // Col 4-5: Kegiatan Pengadaan SIPD (r3-r4 banner c3-c4)
            {s:{r:3,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},  // Kode
            {s:{r:5,c:4}, e:{r:6,c:4}},  // Nama Kegiatan Pengadaan

            // Col 6-7: Sub Kegiatan Pengadaan SIPD (r3-r4 banner c5-c6)
            {s:{r:3,c:5}, e:{r:4,c:6}},
            {s:{r:5,c:5}, e:{r:6,c:5}},  // Kode
            {s:{r:5,c:6}, e:{r:6,c:6}},  // Nama Sub Kegiatan Pengadaan

            // Col 8-15: BELANJA MODAL (Top Banner r3, c7-c14)
            {s:{r:3,c:7}, e:{r:3,c:14}},
            // Col 8-9: Rekening Belanja Untuk Pengadaan SIPD
            {s:{r:4,c:7}, e:{r:4,c:8}},
            {s:{r:5,c:7}, e:{r:6,c:7}},   // Kode Rek
            {s:{r:5,c:8}, e:{r:6,c:8}},   // Nama Belanja Pengadaan
            // Col 10-11: Jenis Aset (PMDN 108)
            {s:{r:4,c:9}, e:{r:4,c:10}},
            {s:{r:5,c:9}, e:{r:6,c:9}},   // Kode
            {s:{r:5,c:10}, e:{r:6,c:10}}, // Nama Jenis Aset
            // Col 12-13: Sub Rincian Objek (PMDN 108)
            {s:{r:4,c:11}, e:{r:4,c:12}},
            {s:{r:5,c:11}, e:{r:6,c:11}}, // Kode
            {s:{r:5,c:12}, e:{r:6,c:12}}, // Nama Uraian Sub Rincian Objek
            // Col 14: JUMLAH ANGGARAN (Rp)
            {s:{r:4,c:13}, e:{r:6,c:13}},
            // Col 15: JUMLAH REALISASI (Rp)
            {s:{r:4,c:14}, e:{r:6,c:14}},

            // Col 16-46: RINCIAN BELANJA MODAL ... (Top Banner r3, c15-c45)
            {s:{r:3,c:15}, e:{r:3,c:45}},
            // Kolom standalone (r4-r6 merged):
            {s:{r:4,c:15}, e:{r:6,c:15}},  // Col 16: NAMA BARANG
            {s:{r:4,c:16}, e:{r:6,c:16}},  // Col 17: Kode Barang
            {s:{r:4,c:17}, e:{r:6,c:17}},  // Col 18: Luas Lantai (m²)
            // Col 19-21: Kondisi / Spesifikasi (r4 banner c18-c20) -> r5-r6: (B,KB,RB), Bertingkat, Beton
            {s:{r:4,c:18}, e:{r:4,c:20}},
            {s:{r:5,c:18}, e:{r:6,c:18}},  // Col 19: (B,KB,RB)
            {s:{r:5,c:19}, e:{r:6,c:19}},  // Col 20: Bertingkat/ tidak
            {s:{r:5,c:20}, e:{r:6,c:20}},  // Col 21: Beton/ tidak
            // Col 22-27: Jenis Bangunan (r4 banner c21-c26)
            {s:{r:4,c:21}, e:{r:4,c:26}},
            {s:{r:5,c:21}, e:{r:6,c:21}},  // Col 22: Status Tanah
            {s:{r:5,c:22}, e:{r:6,c:22}},  // Col 23: Kode aset Tanah
            {s:{r:5,c:23}, e:{r:6,c:23}},  // Col 24: Baru
            {s:{r:5,c:24}, e:{r:5,c:26}},  // Col 25-27: Kapitalisasi (r5) -> r6: Nilai, Tahun Induk, Nilai Induk
            // Col 28-35: Riwayat Pembelian (r4 banner c27-c34)
            {s:{r:4,c:27}, e:{r:4,c:34}},
            {s:{r:5,c:27}, e:{r:5,c:28}},  // SPK (r5) -> r6: Nomor (c27), Tanggal (c28)
            {s:{r:5,c:29}, e:{r:5,c:30}},  // Surat Pesanan -> r6: Nomor (c29), Tanggal (c30)
            {s:{r:5,c:31}, e:{r:5,c:32}},  // Kwitansi -> r6: Nomor (c31), Tanggal (c32)
            {s:{r:5,c:33}, e:{r:5,c:34}},  // Invoice -> r6: Nomor (c33), Tanggal (c34)
            // Col 36-37: VOLUME (r4 banner c35-c36)
            {s:{r:4,c:35}, e:{r:4,c:36}},
            {s:{r:5,c:35}, e:{r:6,c:35}},  // Col 36: Jumlah Bangunan
            {s:{r:5,c:36}, e:{r:6,c:36}},  // Col 37: Nama Satuan Barang
            // Col 38-41: Nilai Barang (Rp) (r4 banner c37-c40)
            {s:{r:4,c:37}, e:{r:4,c:40}},
            {s:{r:5,c:37}, e:{r:6,c:37}},  // Col 38: Nilai Perencanaan
            {s:{r:5,c:38}, e:{r:6,c:38}},  // Col 39: Nilai Fisik
            {s:{r:5,c:39}, e:{r:6,c:39}},  // Col 40: Nilai Pengawasan
            {s:{r:5,c:40}, e:{r:6,c:40}},  // Col 41: Nilai AP
            // Col 42: Total Nilai Barang (Rp) (r4-r6, c41)
            {s:{r:4,c:41}, e:{r:6,c:41}},
            // Col 43-44: BAST pada SPK/... (r4-r5 banner, c42-c43) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:42}, e:{r:5,c:43}},
            // Col 45-46: SP2D (r4-r5 banner, c44-c45) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:44}, e:{r:5,c:45}},

            // Col 47: Letak/ Alamat (Berdiri Sendiri r3-r6, c46)
            {s:{r:3,c:46}, e:{r:6,c:46}},

            // Col 48-52: PIHAK PENYEDIA (Top Banner r3, c47-c51)
            {s:{r:3,c:47}, e:{r:3,c:51}},
            {s:{r:4,c:47}, e:{r:6,c:47}},  // Nama Penyedia (r4-r6, c47)
            {s:{r:4,c:48}, e:{r:6,c:48}},  // Pemilik Penyedia (r4-r6, c48)
            {s:{r:4,c:49}, e:{r:4,c:50}},  // Rekening (r4, c49-c50)
            {s:{r:5,c:49}, e:{r:6,c:49}},  // Nama Rek (r5-r6, c49)
            {s:{r:5,c:50}, e:{r:6,c:50}},  // Nomor Rek (r5-r6, c50)
            {s:{r:4,c:51}, e:{r:6,c:51}},  // Alamat Penyedia (r4-r6, c51)

            // Col 53-54: Pejabat Pembuat Komitmen (Top Banner r3-r4, c52-c53)
            {s:{r:3,c:52}, e:{r:4,c:53}},
            {s:{r:5,c:52}, e:{r:6,c:52}},  // Nama (r5-r6, c52)
            {s:{r:5,c:53}, e:{r:6,c:53}},  // NIP (r5-r6, c53)

            // Col 55: KET. (Berdiri Sendiri r3-r6, c54)
            {s:{r:3,c:54}, e:{r:6,c:54}}
        ], 55, kibFTitleRows.length, kibFRows.length, true);
        wsKibF['!merges'].push(...getKibSignatureMerges(kibFSignStartRow, 55, 41, 1, 6, 54));

        applyUnified4StepMasterSheetStyling(wsKibF, kibFRows.length, 55, 31, kibFTitleRows.length, true);
        applySignatureBlockStyling(wsKibF, kibFSignStartRow, 55);
        if (filterCat === 'all' || filterCat === 'KIB F') {
            XLSX.utils.book_append_sheet(wb, wsKibF, filterCat === 'all' ? "7. F" : "KIB F - Konstruksi KDP");
        }

        // ------------------------------------------------------------------------
        // 8. ATB (ASET TIDAK BERWUJUD) - COMPLETE 4-STEP MASTER SHEET (47 KOLOM)
        // Sesuai Format Baku: 1-15 (Langkah 1-2), 16-37 (Langkah 3), 38-47 (Langkah 4)
        // ------------------------------------------------------------------------
        const atbTitleRows = getKibTitleRows("ASET TIDAK BERWUJUD", yearLabel, filterTw);
        const atbRows = [
            ...atbTitleRows,
            // r3: Main Banner (47 kolom: c0-c14 Langkah 1-2, c15-c36 Langkah 3, c37-c46 Langkah 4)
            [
                "NO",
                "Program Pengadaan SIPD", "",
                "Kegiatan Pengadaan SIPD", "",
                "Sub Kegiatan Pengadaan SIPD", "",
                "BELANJA MODAL", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE / " + yearLabel,
                "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "", "",
                "Ruang /\nPemegang",
                "PIHAK PENYEDIA", "", "", "", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            // r4: Sub-Banner Level 1 (47 kolom)
            [
                "",
                "", "",
                "", "",
                "", "",
                "Rekening Belanja Untuk Pengadaan SIPD", "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH ANGGARAN (Rp)",
                "JUMLAH REALISASI (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "Judul / Nama",
                "Pencipta",
                "Spesifikasi",
                "Riwayat Pembelian", "", "", "", "", "", "", "",
                "VOLUME", "",
                "Nilai Satuan Barang\n(Rp)",
                "ADMINISTRASI PROYEK (Rp)",
                "Total Nilai Barang (Rp)",
                "SP2D", "",
                "BAST pada SPK/Surat Pesanan/Kwitansi/Invoice", "",
                "",
                "Nama Penyedia", "Pemilik Penyedia", "No Hp / wa\nYang Aktif", "Rekening", "", "Alamat\nPenyedia",
                "", "",
                ""
            ],
            // r5: Sub-Banner Level 2 (47 kolom)
            [
                "",
                "Kode", "Nama Program",
                "Kode", "Nama Kegiatan Pengadaan",
                "Kode", "Nama Sub Kegiatan Pengadaan",
                "Kode Rek", "Nama Belanja Pengadaan",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "", "",
                "",
                "",
                "",
                "",
                "",
                "SPK", "", "Surat Pesanan", "", "Kwitansi", "", "Invoice", "",
                "Jumlah", "Nama Satuan\nBarang",
                "",
                "",
                "",
                "", "",
                "", "",
                "",
                "", "", "", "Nama Rek", "Nomor Rek", "",
                "Nama", "NIP",
                ""
            ],
            // r6: Technical Sub Detail (Level 4 - 47 kolom)
            [
                "",
                "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "Nomor", "Tanggal",
                "", "",
                "",
                "",
                "",
                "NOMOR", "TANGGAL",
                "NOMOR", "TANGGAL",
                "",
                "", "", "", "", "", "",
                "", "",
                ""
            ],
            // r7: Column Numbers (47 kolom: 1-15, 16-37, 38-47)
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33 = 31+32", "34", "35", "36", "37",
                "38", "39", "40", "41", "42", "43", "44", "45", "46", "47"
            ]
        ];

        // ── Grouping ATB Berdasarkan Sub Rincian Objek PMDN 108 ──────────────
        const atbGroups = {};
        categories['ATB'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.5.3.01.01.01');
            if (!atbGroups[groupKey]) atbGroups[groupKey] = [];
            atbGroups[groupKey].push(item);
        });

        let globalAtbNo = 1;
        let atbTotalAnggaran = 0, atbTotalRealisasi = 0, atbTotalUnit = 0, atbTotalAdminProyek = 0, atbTotalNilaiBarang = 0;
        Object.keys(atbGroups).forEach(groupKey => {
            const groupItems = atbGroups[groupKey];
            let groupAnggaranTotal = 0;
            groupItems.forEach(it => {
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0));
            });
            const groupRealisasiTotal = groupItems.reduce((acc, curr) => {
                const val = typeof curr.total_realisasi_num === 'number' ? curr.total_realisasi_num : (parseFloat(curr.total_realisasi) || 0);
                return acc + val;
            }, 0);

            atbTotalAnggaran += groupAnggaranTotal;
            atbTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                const jumlahUnit = parseInt(item.jumlah_volume) || parseInt(item.jumlah_unit) || 1;
                const hargaSatuan = parseFloat(item.harga_satuan) || (jumlahUnit > 0 ? (totalVal / jumlahUnit) : totalVal);
                const adminProyek = parseFloat(item.administrasi_proyek) || parseFloat(item.admin_proyek) || 0;
                const totalNilaiBarang = (hargaSatuan * jumlahUnit + adminProyek) || totalVal;
                const ruangUnit = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '-');
                const noHppWa = item.penyedia_kontak || item.penyedia_telepon || item.telepon || item.no_hp || '-';

                atbTotalUnit += jumlahUnit;
                atbTotalAdminProyek += adminProyek;
                atbTotalNilaiBarang += totalNilaiBarang;

                let col1to15 = [];
                if (isFirstRowInGroup) {
                    col1to15 = [
                        globalAtbNo++,
                        item.program_kode || '-',
                        item.program_nama || '-',
                        item.kegiatan_kode || '-',
                        item.kegiatan_nama || '-',
                        item.sub_kegiatan_kode || '-',
                        item.sub_kegiatan_nama || '-',
                        item.rekening_kode || '-',
                        item.rekening_nama || '-',
                        item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.5.3'),
                        item.jenis_aset_nama || 'ASET TIDAK BERWUJUD',
                        item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                        item.sub_rincian_nama || '-',
                        groupAnggaranTotal,
                        groupRealisasiTotal
                    ];
                    isFirstRowInGroup = false;
                } else {
                    col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                }

                atbRows.push([
                    ...col1to15,                                                 // c0-c14: cols 1-15
                    item.nama_barang || '-',                                     // c15: col 16 (Nama Barang)
                    item.kode_barang || '-',                                     // c16: col 17 (Kode Barang)
                    item.judul_pencipta || item.judul_buku || item.nama_barang || '-', // c17: col 18 (Judul / Nama)
                    item.pencipta || item.pengarang || item.vendor || '-',       // c18: col 19 (Pencipta)
                    item.spesifikasi || '-',                                     // c19: col 20 (Spesifikasi)
                    item.spk_nomor || '-',                                       // c20: col 21 (SPK No)
                    formatAstapDate(item.spk_tanggal),                           // c21: col 22 (SPK Tgl)
                    item.surat_pesanan_nomor || '-',                             // c22: col 23 (Surat Pesanan No)
                    formatAstapDate(item.surat_pesanan_tanggal),                 // c23: col 24 (Surat Pesanan Tgl)
                    item.kwitansi_nomor || '-',                                  // c24: col 25 (Kwitansi No)
                    formatAstapDate(item.kwitansi_tanggal),                      // c25: col 26 (Kwitansi Tgl)
                    item.faktur_nomor || '-',                                    // c26: col 27 (Invoice No)
                    formatAstapDate(item.faktur_tanggal),                        // c27: col 28 (Invoice Tgl)
                    jumlahUnit,                                                  // c28: col 29 (Jumlah)
                    item.satuan || 'Paket Lisensi',                              // c29: col 30 (Nama Satuan Barang)
                    hargaSatuan,                                                 // c30: col 31 (Nilai Satuan Barang)
                    adminProyek,                                                 // c31: col 32 (ADMINISTRASI PROYEK)
                    totalNilaiBarang,                                            // c32: col 33 (Total Nilai Barang)
                    item.sp2d_nomor || '-',                                      // c33: col 34 (SP2D NOMOR)
                    formatAstapDate(item.sp2d_tanggal),                          // c34: col 35 (SP2D TANGGAL)
                    item.bast_dokumen_nomor || '-',                              // c35: col 36 (BAST NOMOR)
                    formatAstapDate(item.bast_dokumen_tanggal),                  // c36: col 37 (BAST TANGGAL)
                    ruangUnit,                                                   // c37: col 38 (Ruang / Pemegang)
                    item.penyedia_nama || '-',                                   // c38: col 39 (Nama Penyedia)
                    item.penyedia_pemilik || '-',                                // c39: col 40 (Pemilik Penyedia)
                    noHppWa,                                                     // c40: col 41 (No Hp / wa Yang Aktif)
                    item.penyedia_rekening_nama || (item.penyedia_nama || '-'),  // c41: col 42 (Nama Rek)
                    item.penyedia_rekening_nomor || '-',                         // c42: col 43 (Nomor Rek)
                    item.penyedia_alamat || '-',                                 // c43: col 44 (Alamat Penyedia)
                    item.ppk_nama || '-',                                        // c44: col 45 (Nama PPK)
                    item.ppk_nip || '-',                                         // c45: col 46 (NIP PPK)
                    item.keterangan_tambahan || item.keterangan || '-'           // c46: col 47 (KET.)
                ]);
            });
        });

        // ── Baris Footer Total ATB (47 Kolom) ────────────────────────────────────
        const atbFooterRow = Array(47).fill("");
        atbFooterRow[0] = "JUMLAH";
        atbFooterRow[13] = atbTotalAnggaran;
        atbFooterRow[14] = atbTotalRealisasi;
        atbFooterRow[28] = atbTotalUnit;
        atbFooterRow[31] = atbTotalAdminProyek;
        atbFooterRow[32] = atbTotalNilaiBarang;
        atbRows.push(atbFooterRow);

        // Tanda Tangan ATB (Format Baku RSUD Koesnadi)
        const atbSignStartRow = atbRows.length;
        const atbSignRows = buildKibSignatureRows(47, 34, ppkNama, ppkNip, signDate, 1);
        atbSignRows.forEach(r => atbRows.push(r));

        const wsAtb = XLSX.utils.aoa_to_sheet(atbRows);
        wsAtb['!cols'] = Array(47).fill({wch: 18});
        wsAtb['!cols'][0] = {wch: 6};
        wsAtb['!cols'][1] = {wch: 14}; wsAtb['!cols'][2] = {wch: 32};
        wsAtb['!cols'][3] = {wch: 14}; wsAtb['!cols'][4] = {wch: 28};
        wsAtb['!cols'][5] = {wch: 16}; wsAtb['!cols'][6] = {wch: 30};
        wsAtb['!cols'][7] = {wch: 18}; wsAtb['!cols'][8] = {wch: 30};
        wsAtb['!cols'][9] = {wch: 14}; wsAtb['!cols'][10] = {wch: 24};
        wsAtb['!cols'][11] = {wch: 18}; wsAtb['!cols'][12] = {wch: 32};
        wsAtb['!cols'][13] = {wch: 22}; wsAtb['!cols'][14] = {wch: 22};
        wsAtb['!cols'][15] = {wch: 32}; wsAtb['!cols'][16] = {wch: 22};
        wsAtb['!cols'][17] = {wch: 26}; wsAtb['!cols'][18] = {wch: 22};
        wsAtb['!cols'][19] = {wch: 28}; wsAtb['!cols'][20] = {wch: 22};
        wsAtb['!cols'][21] = {wch: 14}; wsAtb['!cols'][22] = {wch: 22};
        wsAtb['!cols'][23] = {wch: 14}; wsAtb['!cols'][24] = {wch: 22};
        wsAtb['!cols'][25] = {wch: 14}; wsAtb['!cols'][26] = {wch: 22};
        wsAtb['!cols'][27] = {wch: 14}; wsAtb['!cols'][28] = {wch: 14};
        wsAtb['!cols'][29] = {wch: 18}; wsAtb['!cols'][30] = {wch: 22};
        wsAtb['!cols'][31] = {wch: 22}; wsAtb['!cols'][32] = {wch: 22};
        wsAtb['!cols'][33] = {wch: 20}; wsAtb['!cols'][34] = {wch: 14};
        wsAtb['!cols'][35] = {wch: 28}; wsAtb['!cols'][36] = {wch: 14};
        wsAtb['!cols'][37] = {wch: 24}; wsAtb['!cols'][38] = {wch: 28};
        wsAtb['!cols'][39] = {wch: 24}; wsAtb['!cols'][40] = {wch: 20};
        wsAtb['!cols'][41] = {wch: 24}; wsAtb['!cols'][42] = {wch: 22};
        wsAtb['!cols'][43] = {wch: 30}; wsAtb['!cols'][44] = {wch: 24};
        wsAtb['!cols'][45] = {wch: 22}; wsAtb['!cols'][46] = {wch: 26};

        // ── Merge Cells ATB (47 Kolom Sesuai Format Baku Gambar) ──────────────
        wsAtb['!merges'] = getKibMerges([
            // Col 1: NO (r3-r6, c0)
            {s:{r:3,c:0}, e:{r:6,c:0}},

            // Col 2-3: Program Pengadaan SIPD (r3-r4 banner c1-c2, r5-r6 sub-headers)
            {s:{r:3,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},  // Kode
            {s:{r:5,c:2}, e:{r:6,c:2}},  // Nama Program

            // Col 4-5: Kegiatan Pengadaan SIPD (r3-r4 banner c3-c4)
            {s:{r:3,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},  // Kode
            {s:{r:5,c:4}, e:{r:6,c:4}},  // Nama Kegiatan Pengadaan

            // Col 6-7: Sub Kegiatan Pengadaan SIPD (r3-r4 banner c5-c6)
            {s:{r:3,c:5}, e:{r:4,c:6}},
            {s:{r:5,c:5}, e:{r:6,c:5}},  // Kode
            {s:{r:5,c:6}, e:{r:6,c:6}},  // Nama Sub Kegiatan Pengadaan

            // Col 8-15: BELANJA MODAL (Top Banner r3, c7-c14)
            {s:{r:3,c:7}, e:{r:3,c:14}},
            // Col 8-9: Rekening Belanja Untuk Pengadaan SIPD
            {s:{r:4,c:7}, e:{r:4,c:8}},
            {s:{r:5,c:7}, e:{r:6,c:7}},   // Kode Rek
            {s:{r:5,c:8}, e:{r:6,c:8}},   // Nama Belanja Pengadaan
            // Col 10-11: Jenis Aset (PMDN 108)
            {s:{r:4,c:9}, e:{r:4,c:10}},
            {s:{r:5,c:9}, e:{r:6,c:9}},   // Kode
            {s:{r:5,c:10}, e:{r:6,c:10}}, // Nama Jenis Aset
            // Col 12-13: Sub Rincian Objek (PMDN 108)
            {s:{r:4,c:11}, e:{r:4,c:12}},
            {s:{r:5,c:11}, e:{r:6,c:11}}, // Kode
            {s:{r:5,c:12}, e:{r:6,c:12}}, // Nama Uraian Sub Rincian Objek
            // Col 14: JUMLAH ANGGARAN (Rp)
            {s:{r:4,c:13}, e:{r:6,c:13}},
            // Col 15: JUMLAH REALISASI (Rp)
            {s:{r:4,c:14}, e:{r:6,c:14}},

            // Col 16-37: RINCIAN BELANJA MODAL ... (Top Banner r3, c15-c36)
            {s:{r:3,c:15}, e:{r:3,c:36}},
            // Kolom standalone (r4-r6 merged):
            {s:{r:4,c:15}, e:{r:6,c:15}},  // Col 16: NAMA BARANG
            {s:{r:4,c:16}, e:{r:6,c:16}},  // Col 17: Kode Barang
            {s:{r:4,c:17}, e:{r:6,c:17}},  // Col 18: Judul / Nama
            {s:{r:4,c:18}, e:{r:6,c:18}},  // Col 19: Pencipta
            {s:{r:4,c:19}, e:{r:6,c:19}},  // Col 20: Spesifikasi
            // Col 21-28: Riwayat Pembelian (r4 banner c20-c27)
            {s:{r:4,c:20}, e:{r:4,c:27}},
            {s:{r:5,c:20}, e:{r:5,c:21}},  // SPK (r5) -> r6: Nomor (c20), Tanggal (c21)
            {s:{r:5,c:22}, e:{r:5,c:23}},  // Surat Pesanan -> r6: Nomor (c22), Tanggal (c23)
            {s:{r:5,c:24}, e:{r:5,c:25}},  // Kwitansi -> r6: Nomor (c24), Tanggal (c25)
            {s:{r:5,c:26}, e:{r:5,c:27}},  // Invoice -> r6: Nomor (c26), Tanggal (c27)
            // Col 29-30: VOLUME (r4 banner c28-c29)
            {s:{r:4,c:28}, e:{r:4,c:29}},
            {s:{r:5,c:28}, e:{r:6,c:28}},  // Col 29: Jumlah
            {s:{r:5,c:29}, e:{r:6,c:29}},  // Col 30: Nama Satuan Barang
            // Col 31: Nilai Satuan Barang (Rp) (r4-r6, c30)
            {s:{r:4,c:30}, e:{r:6,c:30}},
            // Col 32: ADMINISTRASI PROYEK (Rp) (r4-r6, c31)
            {s:{r:4,c:31}, e:{r:6,c:31}},
            // Col 33: Total Nilai Barang (Rp) (r4-r6, c32)
            {s:{r:4,c:32}, e:{r:6,c:32}},
            // Col 34-35: SP2D (r4-r5 banner, c33-c34) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:33}, e:{r:5,c:34}},
            // Col 36-37: BAST pada SPK/... (r4-r5 banner, c35-c36) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:35}, e:{r:5,c:36}},

            // Col 38: Ruang / Pemegang (Berdiri Sendiri r3-r6, c37)
            {s:{r:3,c:37}, e:{r:6,c:37}},

            // Col 39-44: PIHAK PENYEDIA (Top Banner r3, c38-c43)
            {s:{r:3,c:38}, e:{r:3,c:43}},
            {s:{r:4,c:38}, e:{r:6,c:38}},  // Nama Penyedia (r4-r6, c38)
            {s:{r:4,c:39}, e:{r:6,c:39}},  // Pemilik Penyedia (r4-r6, c39)
            {s:{r:4,c:40}, e:{r:6,c:40}},  // No Hp / wa Yang Aktif (r4-r6, c40)
            {s:{r:4,c:41}, e:{r:4,c:42}},  // Rekening (r4, c41-c42)
            {s:{r:5,c:41}, e:{r:6,c:41}},  // Nama Rek (r5-r6, c41)
            {s:{r:5,c:42}, e:{r:6,c:42}},  // Nomor Rek (r5-r6, c42)
            {s:{r:4,c:43}, e:{r:6,c:43}},  // Alamat Penyedia (r4-r6, c43)

            // Col 45-46: Pejabat Pembuat Komitmen (Top Banner r3-r4, c44-c45)
            {s:{r:3,c:44}, e:{r:4,c:45}},
            {s:{r:5,c:44}, e:{r:6,c:44}},  // Nama (r5-r6, c44)
            {s:{r:5,c:45}, e:{r:6,c:45}},  // NIP (r5-r6, c45)

            // Col 47: KET. (Berdiri Sendiri r3-r6, c46)
            {s:{r:3,c:46}, e:{r:6,c:46}}
        ], 47, atbTitleRows.length, atbRows.length, true);
        wsAtb['!merges'].push(...getKibSignatureMerges(atbSignStartRow, 47, 34, 1, 6, 46));

        applyUnified4StepMasterSheetStyling(wsAtb, atbRows.length, 47, 22, atbTitleRows.length, true);
        applySignatureBlockStyling(wsAtb, atbSignStartRow, 47);
        if (filterCat === 'all' || filterCat === 'ATB') {
            XLSX.utils.book_append_sheet(wb, wsAtb, filterCat === 'all' ? "8. ATB" : "ATB - Aset Tidak Berwujud");
        }

        // ------------------------------------------------------------------------
        // 9. EXTRACOM (EKSTRAKOMTABEL) - COMPLETE 4-STEP MASTER SHEET (54 KOLOM)
        // ------------------------------------------------------------------------
        // 9. EXTRACOM (EKSTRAKOMTABEL) - COMPLETE 4-STEP MASTER SHEET (51 KOLOM)
        // Format Khusus Tanpa Kolom Kendaraan (No. Rangka, Mesin, BPKB, Polisi)
        // Serta Kolom Khusus: No Hp / Wa Yang Aktif pada Pihak Penyedia
        // ------------------------------------------------------------------------
        const extracomTitleRows = getKibTitleRows("BARANG EKSTRAKOMTABEL", yearLabel, filterTw);
        const extracomRows = [
            ...extracomTitleRows,
            // r3: Main Banner (51 kolom)
            [
                "NO",
                "Program Pengadaan SIPD", "",
                "Kegiatan Pengadaan SIPD", "",
                "Sub Kegiatan Pengadaan SIPD", "",
                "BELANJA MODAL", "", "", "", "", "", "", "",
                "RINCIAN BELANJA MODAL EKSTRAKOMTABEL SESUAI SPK / SURAT PESANAN / KWITANSI / INVOICE / " + yearLabel,
                "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "", "",
                "", "", "",
                "RUANG /\nPEMEGANG",
                "PIHAK PENYEDIA", "", "", "", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            // r4: Sub-Banner Level 1 (51 kolom)
            [
                "",
                "", "",
                "", "",
                "", "",
                "Rekening Belanja Untuk Pengadaan SIPD", "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH ANGGARAN (Rp)",
                "JUMLAH REALISASI (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "Merk", "Type", "Ukuran / Kapasitas",
                "No. Pabrik",
                "BAHAN", "Tahun Perolehan",
                "Riwayat Pembelian", "", "", "", "", "", "", "",
                "Kondisi\n(B,KB,RB)",
                "VOLUME", "",
                "Nilai Satuan Barang (Rp)",
                "ADMINISTRASI PROYEK (Rp)",
                "Total Nilai Barang\n(Rp) = 35+36",
                "SP2D", "",
                "BAST pada SPK/Surat Pesanan/Kwitansi/Invoice", "",
                "",
                "Nama Penyedia", "Pemilik Penyedia", "No Hp / wa\nYang Aktif", "Rekening", "", "Alamat\nPenyedia",
                "", "",
                ""
            ],
            // r5: Sub-Banner Level 2 (51 kolom)
            [
                "",
                "Kode", "Nama Program",
                "Kode", "Nama Kegiatan",
                "Kode", "Nama Sub Kegiatan",
                "Kode Rek", "Nama Belanja Pengadaan",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "", "",
                "", "", "", "", "", "", "", "",
                "SPK", "", "Surat Pesanan", "", "Kwitansi", "", "Invoice", "",
                "",
                "Jumlah Barang", "Nama Satuan Barang",
                "", "", "",
                "", "",
                "", "",
                "",
                "", "", "", "Nama Rek", "Nomor Rek", "",
                "Nama", "NIP",
                ""
            ],
            // r6: Sub-Banner Level 3 / Nomor-Tanggal (51 kolom)
            [
                "",
                "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "", "",
                "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal", "Nomor", "Tanggal",
                "", "", "", "", "", "",
                "NOMOR", "TANGGAL",
                "NOMOR", "TANGGAL",
                "",
                "", "", "", "", "", "",
                "", "",
                ""
            ],
            // r7: Nomor Kolom (51 kolom)
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23",
                "24", "25", "26", "27", "28", "29", "30", "31",
                "32", "33", "34", "35", "36", "37", "38", "39", "40", "41",
                "42", "43", "44", "45", "46", "47", "48", "49", "50", "51"
            ]
        ];

        // ── Kelompokkan Data EXTRACOM per Sub Rincian Objek (PMDN 108) ─────────────
        const extracomGroups = {};
        categories['EXTRACOM'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.5.4.01.01.01');
            if (!extracomGroups[groupKey]) {
                extracomGroups[groupKey] = [];
            }
            extracomGroups[groupKey].push(item);
        });

        let globalExtracomNo = 1;
        let extracomTotalAnggaran = 0, extracomTotalRealisasi = 0, extracomTotalUnit = 0, extracomTotalAdminProyek = 0, extracomTotalNilaiBarang = 0;
        Object.keys(extracomGroups).forEach(groupKey => {
            const groupItems = extracomGroups[groupKey];
            let groupAnggaranTotal = 0;
            groupItems.forEach(it => {
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0));
            });
            const groupRealisasiTotal = groupItems.reduce((acc, it) => acc + (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0), 0);

            extracomTotalAnggaran += groupAnggaranTotal;
            extracomTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                let spec = item.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch (e) { spec = {}; }
                }

                const mesinItems = (spec && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0)
                    ? spec.mesin_items
                    : null;

                if (mesinItems) {
                    mesinItems.forEach((mItem) => {
                        const qty = Math.max(1, parseInt(mItem.mesin_jumlah_barang) || 1);
                        const nilaiSatuan = parseFloat(mItem.mesin_nilai_satuan) || 0;
                        const adminProyek = parseFloat(mItem.mesin_administrasi_proyek) || 0;
                        const totalNilaiBarang = (qty * nilaiSatuan) + adminProyek;

                        extracomTotalUnit += qty;
                        extracomTotalAdminProyek += adminProyek;
                        extracomTotalNilaiBarang += totalNilaiBarang;

                        const rawKondisi = mItem.mesin_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = rawKondisi === 'B' || rawKondisi === 'Baik' ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));
                        const ruangUnit = mItem.ruang_pemegang_mesin || item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '-');

                        let col1to15 = [];
                        if (isFirstRowInGroup) {
                            // Baris Pertama Group Sub Rincian: Isi Lengkap Kolom 1 s/d 15
                            col1to15 = [
                                globalExtracomNo++,
                                item.program_kode || '-',
                                item.program_nama || '-',
                                item.kegiatan_kode || '-',
                                item.kegiatan_nama || '-',
                                item.sub_kegiatan_kode || '-',
                                item.sub_kegiatan_nama || '-',
                                item.rekening_kode || '-',
                                item.rekening_nama || '-',
                                item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.5.4'),
                                item.jenis_aset_nama || 'EKSTRAKOMTABEL',
                                item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                                item.sub_rincian_nama || '-',
                                groupAnggaranTotal,
                                groupRealisasiTotal
                            ];
                            isFirstRowInGroup = false;
                        } else {
                            // Baris Anak: Kolom 1 s/d 15 Dikosongkan (Blank)
                            col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                        }

                        extracomRows.push([
                            ...col1to15,                                 // c0-c14: cols 1-15
                            item.nama_barang || '-',                     // c15: col 16
                            item.kode_barang || '-',                     // c16: col 17
                            mItem.mesin_merk || item.merk || '-',        // c17: col 18
                            mItem.mesin_type || item.type || '-',        // c18: col 19
                            mItem.mesin_ukuran || item.ukuran || '-',    // c19: col 20
                            mItem.mesin_no_pabrik || item.no_pabrik || '-', // c20: col 21
                            mItem.mesin_bahan || item.bahan || '-',         // c21: col 22
                            item.tahun_perolehan || '-',                 // c22: col 23
                            item.spk_nomor || '-',                       // c23: col 24 (SPK Nomor)
                            formatAstapDate(item.spk_tanggal),           // c24: col 25
                            item.surat_pesanan_nomor || '-',             // c25: col 26
                            formatAstapDate(item.surat_pesanan_tanggal), // c26: col 27
                            item.kwitansi_nomor || '-',                  // c27: col 28
                            formatAstapDate(item.kwitansi_tanggal),      // c28: col 29
                            item.faktur_nomor || '-',                    // c29: col 30 (Invoice Nomor)
                            formatAstapDate(item.faktur_tanggal),        // c30: col 31
                            kondisiLabel,                                // c31: col 32
                            qty,                                         // c32: col 33 (Jumlah Barang)
                            mItem.mesin_satuan || item.satuan || 'Unit', // c33: col 34 (Nama Satuan Barang)
                            nilaiSatuan,                                 // c34: col 35 (Nilai Satuan)
                            adminProyek,                                 // c35: col 36 (Admin Proyek)
                            totalNilaiBarang,                            // c36: col 37 (Total = 35+36)
                            item.sp2d_nomor || '-',                      // c37: col 38
                            formatAstapDate(item.sp2d_tanggal),          // c38: col 39
                            item.bast_dokumen_nomor || '-',              // c39: col 40
                            formatAstapDate(item.bast_dokumen_tanggal),  // c40: col 41
                            ruangUnit,                                   // c41: col 42
                            ...getStep4Columns(item, true)               // c42-c50: cols 43-51
                        ]);
                    });
                } else {
                    const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                    const adminProyek = parseFloat(item.biaya_administrasi_proyek) || parseFloat(item.admin_proyek) || 0;
                    const jumlahBarang = parseInt(item.jumlah_volume) || parseInt(item.jumlah_unit) || 1;
                    const nilaiSatuan = parseFloat(item.harga_satuan) || (jumlahBarang > 0 ? (totalVal / jumlahBarang) : totalVal);
                    const totalNilaiBarang = totalVal || (nilaiSatuan * jumlahBarang + adminProyek);
                    const ruangUnit = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '-');
                    
                    extracomTotalUnit += jumlahBarang;
                    extracomTotalAdminProyek += adminProyek;
                    extracomTotalNilaiBarang += totalNilaiBarang;

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = rawKondisi === 'B' || rawKondisi === 'Baik' ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    let col1to15 = [];
                    if (isFirstRowInGroup) {
                        col1to15 = [
                            globalExtracomNo++,
                            item.program_kode || '-',
                            item.program_nama || '-',
                            item.kegiatan_kode || '-',
                            item.kegiatan_nama || '-',
                            item.sub_kegiatan_kode || '-',
                            item.sub_kegiatan_nama || '-',
                            item.rekening_kode || '-',
                            item.rekening_nama || '-',
                            item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : '1.5.4'),
                            item.jenis_aset_nama || 'EKSTRAKOMTABEL',
                            item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-')),
                            item.sub_rincian_nama || '-',
                            groupAnggaranTotal,
                            groupRealisasiTotal
                        ];
                        isFirstRowInGroup = false;
                    } else {
                        col1to15 = ["", "", "", "", "", "", "", "", "", "", "", "", "", "", ""];
                    }

                    extracomRows.push([
                        ...col1to15,                                 // c0-c14: cols 1-15
                        item.nama_barang || '-',                     // c15: col 16
                        item.kode_barang || '-',                     // c16: col 17
                        item.merk || '-',                            // c17: col 18
                        item.type || '-',                            // c18: col 19
                        item.ukuran || '-',                          // c19: col 20
                        item.no_pabrik || '-',                       // c20: col 21
                        item.bahan || '-',                           // c21: col 22
                        item.tahun_perolehan || '-',                 // c22: col 23
                        item.spk_nomor || '-',                       // c23: col 24 (SPK Nomor)
                        formatAstapDate(item.spk_tanggal),           // c24: col 25
                        item.surat_pesanan_nomor || '-',             // c25: col 26
                        formatAstapDate(item.surat_pesanan_tanggal), // c26: col 27
                        item.kwitansi_nomor || '-',                  // c27: col 28
                        formatAstapDate(item.kwitansi_tanggal),      // c28: col 29
                        item.faktur_nomor || '-',                    // c29: col 30 (Invoice Nomor)
                        formatAstapDate(item.faktur_tanggal),        // c30: col 31
                        kondisiLabel,                                // c31: col 32
                        jumlahBarang,                                // c32: col 33 (Jumlah Barang)
                        item.satuan || 'Unit',                       // c33: col 34 (Nama Satuan Barang)
                        nilaiSatuan,                                 // c34: col 35 (Nilai Satuan)
                        adminProyek,                                 // c35: col 36 (Admin Proyek)
                        totalNilaiBarang,                            // c36: col 37 (Total = 35+36)
                        item.sp2d_nomor || '-',                      // c37: col 38
                        formatAstapDate(item.sp2d_tanggal),          // c38: col 39
                        item.bast_dokumen_nomor || '-',              // c39: col 40
                        formatAstapDate(item.bast_dokumen_tanggal),  // c40: col 41
                        ruangUnit,                                   // c41: col 42
                        ...getStep4Columns(item, true)               // c42-c50: cols 43-51
                    ]);
                }
            });
        });

        // ── Baris Footer Total EXTRACOM (51 Kolom) ───────────────────────────────
        const extracomFooterRow = Array(51).fill("");
        extracomFooterRow[0] = "JUMLAH";
        extracomFooterRow[13] = extracomTotalAnggaran;
        extracomFooterRow[14] = extracomTotalRealisasi;
        extracomFooterRow[32] = extracomTotalUnit;
        extracomFooterRow[35] = extracomTotalAdminProyek;
        extracomFooterRow[36] = extracomTotalNilaiBarang;
        extracomRows.push(extracomFooterRow);

        // Tanda Tangan EXTRACOM (Format Baku RSUD Koesnadi)
        const extracomSignStartRow = extracomRows.length;
        const extracomSignRows = buildKibSignatureRows(51, 37, ppkNama, ppkNip, signDate, 1);
        extracomSignRows.forEach(r => extracomRows.push(r));

        const wsExtracom = XLSX.utils.aoa_to_sheet(extracomRows);
        wsExtracom['!cols'] = Array(51).fill({wch: 18});
        wsExtracom['!cols'][0] = {wch: 6};
        wsExtracom['!cols'][1] = {wch: 14}; wsExtracom['!cols'][2] = {wch: 32};
        wsExtracom['!cols'][3] = {wch: 14}; wsExtracom['!cols'][4] = {wch: 28};
        wsExtracom['!cols'][5] = {wch: 16}; wsExtracom['!cols'][6] = {wch: 30};
        wsExtracom['!cols'][7] = {wch: 18}; wsExtracom['!cols'][8] = {wch: 30};
        wsExtracom['!cols'][9] = {wch: 14}; wsExtracom['!cols'][10] = {wch: 24};
        wsExtracom['!cols'][11] = {wch: 18}; wsExtracom['!cols'][12] = {wch: 32};
        wsExtracom['!cols'][13] = {wch: 22}; wsExtracom['!cols'][14] = {wch: 22};
        wsExtracom['!cols'][15] = {wch: 32}; wsExtracom['!cols'][16] = {wch: 22};
        wsExtracom['!cols'][17] = {wch: 20}; wsExtracom['!cols'][18] = {wch: 20};
        wsExtracom['!cols'][19] = {wch: 22}; wsExtracom['!cols'][20] = {wch: 20};
        wsExtracom['!cols'][21] = {wch: 16}; wsExtracom['!cols'][22] = {wch: 14};
        wsExtracom['!cols'][23] = {wch: 22}; wsExtracom['!cols'][24] = {wch: 14};
        wsExtracom['!cols'][25] = {wch: 22}; wsExtracom['!cols'][26] = {wch: 14};
        wsExtracom['!cols'][27] = {wch: 22}; wsExtracom['!cols'][28] = {wch: 14};
        wsExtracom['!cols'][29] = {wch: 22}; wsExtracom['!cols'][30] = {wch: 14};
        wsExtracom['!cols'][31] = {wch: 14}; wsExtracom['!cols'][32] = {wch: 14};
        wsExtracom['!cols'][33] = {wch: 18}; wsExtracom['!cols'][34] = {wch: 22};
        wsExtracom['!cols'][35] = {wch: 22}; wsExtracom['!cols'][36] = {wch: 22};
        wsExtracom['!cols'][37] = {wch: 20}; wsExtracom['!cols'][38] = {wch: 14};
        wsExtracom['!cols'][39] = {wch: 28}; wsExtracom['!cols'][40] = {wch: 14};
        wsExtracom['!cols'][41] = {wch: 28}; wsExtracom['!cols'][42] = {wch: 28};
        wsExtracom['!cols'][43] = {wch: 24}; wsExtracom['!cols'][44] = {wch: 20};
        wsExtracom['!cols'][45] = {wch: 24}; wsExtracom['!cols'][46] = {wch: 22};
        wsExtracom['!cols'][47] = {wch: 30}; wsExtracom['!cols'][48] = {wch: 24};
        wsExtracom['!cols'][49] = {wch: 22}; wsExtracom['!cols'][50] = {wch: 26};

        // ── Merge Cells EXTRACOM (51 Kolom Sesuai Format Baku Khusus Ekstrakomtabel) ──
        wsExtracom['!merges'] = getKibMerges([
            // Col 1: NO (r3-r6, c0)
            {s:{r:3,c:0}, e:{r:6,c:0}},

            // Col 2-3: Program Pengadaan SIPD (r3-r4 banner, r5-r6 sub-headers)
            {s:{r:3,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},  // Kode
            {s:{r:5,c:2}, e:{r:6,c:2}},  // Nama Program

            // Col 4-5: Kegiatan Pengadaan SIPD
            {s:{r:3,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},  // Kode
            {s:{r:5,c:4}, e:{r:6,c:4}},  // Nama Kegiatan

            // Col 6-7: Sub Kegiatan Pengadaan SIPD
            {s:{r:3,c:5}, e:{r:4,c:6}},
            {s:{r:5,c:5}, e:{r:6,c:5}},  // Kode
            {s:{r:5,c:6}, e:{r:6,c:6}},  // Nama Sub Kegiatan

            // Col 8-15: BELANJA MODAL (Top Banner r3, c7-c14)
            {s:{r:3,c:7}, e:{r:3,c:14}},
            // Col 8-9: Rekening Belanja Untuk Pengadaan SIPD
            {s:{r:4,c:7}, e:{r:4,c:8}},
            {s:{r:5,c:7}, e:{r:6,c:7}},   // Kode Rek
            {s:{r:5,c:8}, e:{r:6,c:8}},   // Nama Belanja Pengadaan
            // Col 10-11: Jenis Aset (PMDN 108)
            {s:{r:4,c:9}, e:{r:4,c:10}},
            {s:{r:5,c:9}, e:{r:6,c:9}},   // Kode
            {s:{r:5,c:10}, e:{r:6,c:10}}, // Nama Jenis Aset
            // Col 12-13: Sub Rincian Objek (PMDN 108)
            {s:{r:4,c:11}, e:{r:4,c:12}},
            {s:{r:5,c:11}, e:{r:6,c:11}}, // Kode
            {s:{r:5,c:12}, e:{r:6,c:12}}, // Nama Uraian Sub Rincian Objek
            // Col 14: JUMLAH ANGGARAN (Rp)
            {s:{r:4,c:13}, e:{r:6,c:13}},
            // Col 15: JUMLAH REALISASI (Rp)
            {s:{r:4,c:14}, e:{r:6,c:14}},

            // Col 16-41: RINCIAN BELANJA MODAL EKSTRAKOMTABEL ... (Top Banner r3, c15-c40)
            {s:{r:3,c:15}, e:{r:3,c:40}},
            // Kolom standalone (r4-r6 merged):
            {s:{r:4,c:15}, e:{r:6,c:15}},  // Col 16: NAMA BARANG
            {s:{r:4,c:16}, e:{r:6,c:16}},  // Col 17: Kode Barang
            {s:{r:4,c:17}, e:{r:6,c:17}},  // Col 18: Merk
            {s:{r:4,c:18}, e:{r:6,c:18}},  // Col 19: Type
            {s:{r:4,c:19}, e:{r:6,c:19}},  // Col 20: Ukuran / Kapasitas
            {s:{r:4,c:20}, e:{r:6,c:20}},  // Col 21: No. Pabrik
            {s:{r:4,c:21}, e:{r:6,c:21}},  // Col 22: BAHAN
            {s:{r:4,c:22}, e:{r:6,c:22}},  // Col 23: Tahun Perolehan
            // Col 24-31: Riwayat Pembelian (r4 banner c23-c30)
            {s:{r:4,c:23}, e:{r:4,c:30}},
            {s:{r:5,c:23}, e:{r:5,c:24}},  // SPK (r5) -> r6: Nomor (c23), Tanggal (c24)
            {s:{r:5,c:25}, e:{r:5,c:26}},  // Surat Pesanan -> r6: Nomor (c25), Tanggal (c26)
            {s:{r:5,c:27}, e:{r:5,c:28}},  // Kwitansi -> r6: Nomor (c27), Tanggal (c28)
            {s:{r:5,c:29}, e:{r:5,c:30}},  // Invoice -> r6: Nomor (c29), Tanggal (c30)
            // Col 32: Kondisi (B,KB,RB) standalone (r4-r6, c31)
            {s:{r:4,c:31}, e:{r:6,c:31}},
            // Col 33-34: VOLUME (r4 banner c32-c33)
            {s:{r:4,c:32}, e:{r:4,c:33}},
            {s:{r:5,c:32}, e:{r:6,c:32}},  // Jumlah Barang
            {s:{r:5,c:33}, e:{r:6,c:33}},  // Nama Satuan Barang
            // Col 35: Nilai Satuan Barang (r4-r6, c34)
            {s:{r:4,c:34}, e:{r:6,c:34}},
            // Col 36: ADMINISTRASI PROYEK (r4-r6, c35)
            {s:{r:4,c:35}, e:{r:6,c:35}},
            // Col 37: Total Nilai Barang (r4-r6, c36)
            {s:{r:4,c:36}, e:{r:6,c:36}},
            // Col 38-39: SP2D (r4-r5 banner, c37-c38) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:37}, e:{r:5,c:38}},
            // Col 40-41: BAST pada SPK/... (r4-r5 banner, c39-c40) -> r6: NOMOR, TANGGAL
            {s:{r:4,c:39}, e:{r:5,c:40}},
            // Col 42: RUANG / PEMEGANG (Berdiri Sendiri r3-r6, c41)
            {s:{r:3,c:41}, e:{r:6,c:41}},

            // Col 43-48: PIHAK PENYEDIA (Top Banner r3, c42-c47)
            {s:{r:3,c:42}, e:{r:3,c:47}},
            {s:{r:4,c:42}, e:{r:6,c:42}},  // Nama Penyedia (r4-r6, c42)
            {s:{r:4,c:43}, e:{r:6,c:43}},  // Pemilik Penyedia (r4-r6, c43)
            {s:{r:4,c:44}, e:{r:6,c:44}},  // No Hp / wa Yang Aktif (r4-r6, c44)
            {s:{r:4,c:45}, e:{r:4,c:46}},  // Rekening (r4, c45-c46)
            {s:{r:5,c:45}, e:{r:6,c:45}},  // Nama Rek (r5-r6, c45)
            {s:{r:5,c:46}, e:{r:6,c:46}},  // Nomor Rek (r5-r6, c46)
            {s:{r:4,c:47}, e:{r:6,c:47}},  // Alamat Penyedia (r4-r6, c47)

            // Col 49-50: Pejabat Pembuat Komitmen (Top Banner r3-r4, c48-c49)
            {s:{r:3,c:48}, e:{r:4,c:49}},
            {s:{r:5,c:48}, e:{r:6,c:48}},  // Nama (r5-r6, c48)
            {s:{r:5,c:49}, e:{r:6,c:49}},  // NIP (r5-r6, c49)

            // Col 51: KET. (Berdiri Sendiri r3-r6, c50)
            {s:{r:3,c:50}, e:{r:6,c:50}}
        ], 51, extracomTitleRows.length, extracomRows.length, true);
        wsExtracom['!merges'].push(...getKibSignatureMerges(extracomSignStartRow, 51, 37, 1, 6, 50));

        applyUnified4StepMasterSheetStyling(wsExtracom, extracomRows.length, 51, 27, extracomTitleRows.length, true);
        applySignatureBlockStyling(wsExtracom, extracomSignStartRow, 51);
        if (filterCat === 'all' || filterCat === 'EXTRACOM') {
            XLSX.utils.book_append_sheet(wb, wsExtracom, filterCat === 'all' ? "9. Extracom" : "Extracom");
        }

        // Pastikan ada lembar sheet yang dimasukkan ke workbook
        if (!wb.SheetNames || wb.SheetNames.length === 0) {
            alert('⚠️ Tidak ada lembar sheet yang dipilih atau data tidak ditemukan.');
            isExportingAstap = false;
            return;
        }

        // DOWNLOAD FILE EXCEL SESUAI PILIHAN KLASIFIKASI SHEET
        let sheetSlug = 'SEMUA_KIB_9_SHEET';
        if (filterCat === 'REKAP') sheetSlug = 'REKAPITULASI';
        else if (filterCat === 'KIB A') sheetSlug = 'KIB_A_TANAH';
        else if (filterCat === 'KIB B') sheetSlug = 'KIB_B_PERALATAN_MESIN';
        else if (filterCat === 'KIB C') sheetSlug = 'KIB_C_GEDUNG_BANGUNAN';
        else if (filterCat === 'KIB D') sheetSlug = 'KIB_D_JALAN_JARINGAN';
        else if (filterCat === 'KIB E') sheetSlug = 'KIB_E_ASET_TETAP_LAINNYA';
        else if (filterCat === 'KIB F') sheetSlug = 'KIB_F_KONSTRUKSI_KDP';
        else if (filterCat === 'ATB') sheetSlug = 'ATB_ASET_TIDAK_BERWUJUD';
        else if (filterCat === 'EXTRACOM') sheetSlug = 'EXTRACOM';

        const twSlug = filterTw === 'all' ? 'TAHUNAN' : filterTw.replace(/[\s_]/g, '');
        const fileName = "LAPORAN_ASTAP_" + sheetSlug + "_RSUD_KOESNANDI_" + yearLabel + "_" + twSlug + ".xlsx";
        XLSX.writeFile(wb, fileName);
        setTimeout(() => { isExportingAstap = false; }, 1500);
    }

    // =========================================================================
    // FUNGSI EKSPOR FORMAT BARU: PAKET REKAPITULASI TRIWULAN (4 SHEET LENGKAP)
    // 1. DAFTAR AT TW | 2. PENGURANGAN AT | 3. REKLAS | 4. RMB (EXCEL) RSDK
    // =========================================================================
    function applyCleanReportStyling(ws, rowCount, colCount, headerStartRow, headerRowCount, totalRowIndex) {
        if (!ws) return;
        for (let r = 0; r < rowCount; r++) {
            for (let c = 0; c < colCount; c++) {
                const cellRef = XLSX.utils.encode_cell({ r: r, c: c });
                if (!ws[cellRef]) ws[cellRef] = { t: 's', v: '' };
                const cell = ws[cellRef];

                let align = "left";
                let bold = false;
                let fill = "FFFFFF";
                let fontColor = "0F172A";
                let fontSize = 10;
                let numFmt = null;
                let border = {
                    top: { style: "thin", color: { rgb: "CBD5E1" } },
                    bottom: { style: "thin", color: { rgb: "CBD5E1" } },
                    left: { style: "thin", color: { rgb: "CBD5E1" } },
                    right: { style: "thin", color: { rgb: "CBD5E1" } }
                };

                if (r < 4) {
                    fill = r === 0 ? "064E3B" : (r === 1 ? "065F46" : (r === 2 ? "0F766E" : "115E59"));
                    fontColor = "FFFFFF";
                    bold = true;
                    fontSize = r <= 2 ? 11 : 10;
                    align = "center";
                    border = null;
                } else if (r >= headerStartRow && r < (headerStartRow + headerRowCount)) {
                    fill = "0F172A";
                    fontColor = "F8FAFC";
                    bold = true;
                    fontSize = 10;
                    align = "center";
                    border = {
                        top: { style: "medium", color: { rgb: "334155" } },
                        bottom: { style: "medium", color: { rgb: "334155" } },
                        left: { style: "thin", color: { rgb: "334155" } },
                        right: { style: "thin", color: { rgb: "334155" } }
                    };
                } else if (r === totalRowIndex) {
                    fill = "ECFDF5";
                    fontColor = "064E3B";
                    bold = true;
                    fontSize = 10.5;
                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = "#,##0";
                    } else {
                        align = "center";
                    }
                    border = {
                        top: { style: "medium", color: { rgb: "059669" } },
                        bottom: { style: "double", color: { rgb: "059669" } },
                        left: { style: "thin", color: { rgb: "94A3B8" } },
                        right: { style: "thin", color: { rgb: "94A3B8" } }
                    };
                } else if (r > (headerStartRow + headerRowCount - 1) && r < totalRowIndex) {
                    fill = (r % 2 === 0) ? "FFFFFF" : "F8FAFC";
                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = "#,##0";
                    } else if (c === 0 || c === 1 || c === 2 || c === 3 || c === 4 || c === 7 || c === 8 || c === 11) {
                        align = "center";
                    } else {
                        align = "left";
                    }
                } else if (r > totalRowIndex) {
                    continue; // Ditangani khusus oleh applySignatureBlockStyling
                }

                cell.s = {
                    font: { name: "Calibri", sz: fontSize, bold: bold, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: "center", wrapText: true },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt) cell.z = numFmt;
            }
        }
    }

    function formatAstapDate(val) {
        if (!val || val === '-' || val === '') return '-';
        if (typeof val === 'string' && /^\d{2}\/\d{2}\/\d{4}$/.test(val.trim())) return val.trim();
        try {
            const d = new Date(val);
            if (isNaN(d.getTime())) return String(val);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();
            return `${day}/${month}/${year}`;
        } catch(e) {
            return String(val);
        }
    }

    // STYLING ENGINE KHUSUS SHEET 1: DAFTAR AT (19 KOLOM RESMI SESUAI SPJ DENGAN WARNA PASTEL BLUE & GREEN)
    // STYLING ENGINE KHUSUS SHEET 1: DAFTAR AT (19 KOLOM RESMI SESUAI SPJ DENGAN WARNA PASTEL BLUE & GREEN)
    function applyDaftarAtReportStyling(ws, rowCount, colCount = 19, headerStartRow = 5, headerRowCount = 4, totalRowIndex = -1, categoryHeaderRows = [], groupRanges = []) {
        if (!ws) return;

        const thinBlackBorder = {
            top: { style: "thin", color: { rgb: "000000" } },
            bottom: { style: "thin", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "000000" } },
            right: { style: "thin", color: { rgb: "000000" } }
        };

        const numberRowBorder = {
            top: { style: "thin", color: { rgb: "000000" } },
            bottom: { style: "double", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "000000" } },
            right: { style: "thin", color: { rgb: "000000" } }
        };

        const totalRowBorder = {
            top: { style: "thin", color: { rgb: "000000" } },
            bottom: { style: "double", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "64748B" } },
            right: { style: "thin", color: { rgb: "64748B" } }
        };

        for (let r = 0; r < rowCount; r++) {
            for (let c = 0; c < colCount; c++) {
                const cellRef = XLSX.utils.encode_cell({ r: r, c: c });
                if (!ws[cellRef]) ws[cellRef] = { t: 's', v: '' };
                const cell = ws[cellRef];

                let align = "left";
                let bold = false;
                let italic = false;
                let fill = "FFFFFF";
                let fontColor = "000000";
                let fontSize = 9.5;
                let numFmt = null;
                let border = null;

                // 1. BANNER TITLE RESMI (Row 0 - 3) -> TEKS HITAM TEBAL DI TENGAH DENGAN LATAR BERSIH
                if (r < 4) {
                    fill = "FFFFFF";
                    fontColor = "000000";
                    bold = true;
                    fontSize = (r === 2) ? 13.5 : ((r === 0 || r === 1) ? 12.5 : 11.5);
                    align = "center";
                    border = null;
                }
                // 2. BARIS SUMBER DANA (Row 4) -> SUMBER DANA : KELOMPOK ANGGARAN (BLUD)
                else if (r === 4) {
                    fill = "FFFFFF";
                    fontColor = "000000";
                    bold = true;
                    fontSize = 10.5;
                    align = "left";
                    border = null;
                }
                // 3. HEADER TABEL (Row 5 - 7) -> WARNA PASTEL BLUE (#BDD7EE) SESUAI STANDAR RESMI
                else if (r >= headerStartRow && r < (headerStartRow + 3)) {
                    fill = "BDD7EE"; // Soft Pastel Blue as requested in screenshot
                    fontColor = "000000";
                    bold = true;
                    fontSize = 9.5;
                    align = "center";
                    border = thinBlackBorder;
                }
                // 4. BARIS NOMOR KOLOM (Row 8: 1 s/d 19) -> WARNA PASTEL GREEN/KHAKI (#E2EFDA)
                else if (r === (headerStartRow + 3)) {
                    fill = "E2EFDA"; // Soft Pastel Green as requested in screenshot
                    fontColor = "000000";
                    bold = true;
                    fontSize = 9.5;
                    align = "center";
                    border = numberRowBorder;
                    if (c === 14) { // "15 = 13 + 14"
                        italic = true;
                    }
                }
                // 4.5. BARIS HEADER KATEGORI / JENIS ASET (MISAL: Belanja Modal Tanah / Peralatan dan Mesin)
                else if (categoryHeaderRows && categoryHeaderRows.includes(r)) {
                    fill = "D9D9D9"; // Neutral Soft Grey as in template screenshot
                    fontColor = "000000";
                    bold = true;
                    fontSize = 10;
                    align = (c === 2) ? "left" : "center";
                    border = thinBlackBorder;
                }
                // 5. BARIS TOTAL AKHIR (JUMLAH TOTAL)
                else if (r === totalRowIndex) {
                    fill = "E2EFDA"; // Light green highlight for total row
                    fontColor = "000000";
                    bold = true;
                    fontSize = 10;
                    border = totalRowBorder;

                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = (c === 10) ? "#,##0" : "#,##0.00";
                        if (c === 4) bold = true;
                    } else {
                        align = "center";
                    }
                }
                // 6. BARIS DATA UTAMA (Row 9 s/d totalRowIndex - 1)
                else if (r > (headerStartRow + headerRowCount - 1) && r < totalRowIndex) {
                    fill = "FFFFFF"; // Bersih putih murni sesuai template resmi
                    fontColor = "000000";
                    fontSize = 9.5;

                    const grp = (groupRanges && groupRanges.length > 0) ? groupRanges.find(g => r >= g.start && r <= g.end) : null;
                    const isGroupStart = grp ? (r === grp.start) : true;
                    const isGroupEnd = grp ? (r === grp.end) : true;

                    // Seluruh Kolom 1 s/d 19 (TIDAK di-merge vertikal, sesuai contoh Gambar 1):
                    // - Garis vertikal pembatas kolom solid thin
                    // - Garis horizontal pemisah antar baris unit barang menggunakan garis putus-putus (dashed)
                    // - Garis penutup baris terakhir paket belanja menggunakan garis solid utuh
                    border = {
                        left: { style: "thin", color: { rgb: "000000" } },
                        right: { style: "thin", color: { rgb: "000000" } },
                        top: isGroupStart ? { style: "thin", color: { rgb: "000000" } } : { style: "dashed", color: { rgb: "000000" } },
                        bottom: isGroupEnd ? { style: "thin", color: { rgb: "000000" } } : { style: "dashed", color: { rgb: "000000" } }
                    };

                    // Format angka & penataan posisi isi sel
                    if (c === 0) {
                        align = "center"; // Kolom 1 (NO) selalu di tengah
                    } else if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = (c === 10) ? "#,##0" : "#,##0.00";
                        if (c === 4) bold = true; // Kolom 5: REALISASI SPM dibuat BOLD seperti di template resmi
                    } else if (c === 1 || c === 5 || c === 7 || c === 8 || c === 10 || c === 11 || c === 15 || c === 16) {
                        align = "center"; // Kolom Kode, Nomor, Tanggal, Satuan -> Center
                    } else {
                        align = "left"; // Kolom Nama Barang, Rekening, Spesifikasi, Lokasi, Keterangan -> Left
                    }
                }
                // 7. AREA TANDA TANGAN (Row > totalRowIndex)
                else if (r > totalRowIndex) {
                    continue; // Ditangani khusus oleh applySignatureBlockStyling
                }

                cell.s = {
                    font: { name: "Calibri", sz: fontSize, bold: bold, italic: italic, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: "center", wrapText: (r < 5 || (categoryHeaderRows && categoryHeaderRows.includes(r)) ? false : true) },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt) cell.z = numFmt;
            }
        }
    }

    function applyPenguranganAtReportStyling(ws, rowCount, colCount = 14, headerStartRow = 5, headerRowCount = 3, totalRowIndex = -1, categoryHeaderRows = [], groupRanges = []) {
        if (!ws) return;

        const thinBlackBorder = {
            top: { style: "thin", color: { rgb: "000000" } },
            bottom: { style: "thin", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "000000" } },
            right: { style: "thin", color: { rgb: "000000" } }
        };

        const numberRowBorder = {
            top: { style: "thin", color: { rgb: "000000" } },
            bottom: { style: "double", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "000000" } },
            right: { style: "thin", color: { rgb: "000000" } }
        };

        const totalRowBorder = {
            top: { style: "thin", color: { rgb: "000000" } },
            bottom: { style: "double", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "64748B" } },
            right: { style: "thin", color: { rgb: "64748B" } }
        };

        for (let r = 0; r < rowCount; r++) {
            for (let c = 0; c < colCount; c++) {
                const cellRef = XLSX.utils.encode_cell({ r: r, c: c });
                if (!ws[cellRef]) ws[cellRef] = { t: 's', v: '' };
                const cell = ws[cellRef];

                let align = "left";
                let bold = false;
                let italic = false;
                let fill = "FFFFFF";
                let fontColor = "000000";
                let fontSize = 9.5;
                let numFmt = null;
                let border = null;

                // 1. BANNER TITLE RESMI (Row 0 - 3)
                if (r < 4) {
                    fill = "FFFFFF";
                    fontColor = "000000";
                    bold = true;
                    fontSize = (r === 2) ? 13.5 : ((r === 0 || r === 1) ? 12.5 : 11.5);
                    align = "center";
                    border = null;
                }
                // 2. BARIS SUMBER DANA (Row 4)
                else if (r === 4) {
                    fill = "FFFFFF";
                    fontColor = "000000";
                    bold = true;
                    fontSize = 10.5;
                    align = "left";
                    border = null;
                }
                // 3. HEADER TABEL (Row 5 - 6) -> Soft Pastel Blue (#BDD7EE)
                else if (r >= headerStartRow && r < (headerStartRow + 2)) {
                    fill = "BDD7EE";
                    fontColor = "000000";
                    bold = true;
                    fontSize = 9.5;
                    align = "center";
                    border = thinBlackBorder;
                }
                // 4. BARIS NOMOR KOLOM (Row 7: 1 s/d 14) -> Soft Pastel Green (#E2EFDA)
                else if (r === (headerStartRow + 2)) {
                    fill = "E2EFDA";
                    fontColor = "000000";
                    bold = true;
                    fontSize = 9.5;
                    align = "center";
                    border = numberRowBorder;
                }
                // 4.5. BARIS HEADER KATEGORI (MISAL: KIB A / KIB B dst.) - TIDAK DI-MERGE, BERI GARIS UTUH ABU-ABU
                else if (categoryHeaderRows && categoryHeaderRows.includes(r)) {
                    fill = "D9D9D9"; // Neutral Soft Grey
                    fontColor = "000000";
                    bold = true;
                    fontSize = 10;
                    align = (c === 0 || c === 2) ? "left" : "center";
                    border = thinBlackBorder;
                }
                // 5. BARIS TOTAL AKHIR (JUMLAH TOTAL PENGURANGAN ASET TETAP)
                else if (r === totalRowIndex) {
                    fill = "E2EFDA"; // Light green highlight for total row
                    fontColor = "000000";
                    bold = true;
                    fontSize = 10;
                    border = totalRowBorder;

                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = (c === 11) ? "#,##0" : "#,##0.00";
                        bold = true;
                    } else {
                        align = "center";
                    }
                }
                // 6. BARIS DATA UTAMA (Row 8 s/d totalRowIndex - 1)
                else if (r > (headerStartRow + headerRowCount - 1) && r < totalRowIndex) {
                    fill = "FFFFFF";
                    fontColor = "000000";
                    fontSize = 9.5;

                    border = thinBlackBorder;

                    if (c === 0) {
                        align = "center"; // No. Urut
                    } else if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = (c === 11) ? "#,##0" : "#,##0.00";
                    } else if (c === 1 || c === 2 || c === 5 || c === 6 || c === 7 || c === 8 || c === 9 || c === 10) {
                        align = "center"; // Kode 108, Register, No Sertifikat/Pabrik/Mesin, Bahan, Asal Perolehan, Tahun, Satuan, Keadaan
                    } else {
                        align = "left"; // Nama Barang, Merk/Type, Keterangan
                    }
                }
                // 7. AREA TANDA TANGAN (Row > totalRowIndex)
                else if (r > totalRowIndex) {
                    continue; // Ditangani khusus oleh applySignatureBlockStyling
                }

                cell.s = {
                    font: { name: "Calibri", sz: fontSize, bold: bold, italic: italic, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: "center", wrapText: (r < 5 || (categoryHeaderRows && categoryHeaderRows.includes(r)) ? false : true) },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt) cell.z = numFmt;
            }
        }
    }

    // =========================================================================
    // STYLING ENGINE KHUSUS SHEET 3: REKLAS RSDK (5 KOLOM SESUAI FORMAT BAKU RSUD KOESNANDI)
    // =========================================================================
    function applySheet3ReklasRsdkStyling(ws, rowCount, colCount = 5, meta = {}) {
        if (!ws) return;

        const thinBorder = {
            top: { style: "thin", color: { rgb: "000000" } },
            bottom: { style: "thin", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "000000" } },
            right: { style: "thin", color: { rgb: "000000" } }
        };

        const dottedBorder = {
            top: { style: "hair", color: { rgb: "94A3B8" } },
            bottom: { style: "hair", color: { rgb: "94A3B8" } },
            left: { style: "thin", color: { rgb: "000000" } },
            right: { style: "thin", color: { rgb: "000000" } }
        };

        const doubleBottomBorder = {
            top: { style: "thin", color: { rgb: "000000" } },
            bottom: { style: "double", color: { rgb: "000000" } },
            left: { style: "thin", color: { rgb: "000000" } },
            right: { style: "thin", color: { rgb: "000000" } }
        };

        for (let r = 0; r < rowCount; r++) {
            for (let c = 0; c < colCount; c++) {
                const cellRef = XLSX.utils.encode_cell({ r: r, c: c });
                if (!ws[cellRef]) ws[cellRef] = { t: 's', v: '' };
                const cell = ws[cellRef];

                let align = "left";
                let bold = false;
                let italic = false;
                let underline = false;
                let fill = "FFFFFF";
                let fontColor = "000000";
                let fontSize = 10;
                let numFmt = null;
                let border = null;

                // 1. Judul Laporan (Baris 0-1)
                if (r === 0 || r === 1) {
                    bold = true;
                    fontSize = r === 0 ? 12 : 11;
                    align = "center";
                    fill = "FFFFFF";
                    border = null;
                }
                // 2. Header Tabel (Baris 3-5)
                else if (r >= 3 && r <= 5) {
                    fill = "FFFFFF";
                    bold = true;
                    fontSize = 10;
                    align = "center";
                    border = thinBorder;
                }
                // 3. Baris Induk ASET TETAP, JUMLAH ASET, REALISASI BELANJA [TAHUN]
                else if (meta.highlightRows && meta.highlightRows.includes(r)) {
                    fill = "D9D9D9";
                    bold = true;
                    fontSize = 10;
                    border = doubleBottomBorder;
                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = "#,##0.00";
                    } else {
                        align = "left";
                    }
                }
                // 4. Baris Header KIB / Kelompok (TANAH, PERALATAN DAN MESIN, GEDUNG, dll.)
                else if (meta.groupHeaderRows && meta.groupHeaderRows.includes(r)) {
                    fill = "FFFFFF";
                    bold = true;
                    fontSize = 10;
                    border = thinBorder;
                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = "#,##0.00";
                    } else {
                        align = "left";
                    }
                }
                // 5. Baris Koreksi Header & REALISASI BELANJA MODAL
                else if (meta.subHeaderRows && meta.subHeaderRows.includes(r)) {
                    fill = "D9D9D9";
                    bold = true;
                    fontSize = 10;
                    border = thinBorder;
                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = "#,##0.00";
                    } else {
                        align = "left";
                    }
                }
                // 6. Baris Sub-Rincian & Koreksi Items (Isi Data Biasa)
                else if (meta.dataRows && meta.dataRows.includes(r)) {
                    fill = "FFFFFF";
                    bold = false;
                    fontSize = 9.5;
                    border = dottedBorder;
                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = "#,##0.00";
                    } else if (cell.v === 'TRUE') {
                        align = "center";
                        bold = true;
                        fontColor = "64748B";
                    } else {
                        align = "left";
                    }
                }
                // 7. Header KETERANGAN :
                else if (r === meta.keteranganHeaderRow) {
                    bold = true;
                    italic = true;
                    underline = true;
                    fontSize = 10.5;
                    align = "left";
                    fill = "FFFFFF";
                    border = null;
                }
                // 8. Baris Isi Keterangan (Kotak Border)
                else if (meta.keteranganItemRows && meta.keteranganItemRows.includes(r)) {
                    fill = "FFFFFF";
                    fontSize = 9.5;
                    border = thinBorder;
                    if (c === 4) {
                        align = "right";
                        bold = true;
                        fontSize = 10;
                        numFmt = typeof cell.v === 'number' ? '"Rp"\\ #,##0.00' : null;
                    } else {
                        align = "left";
                    }
                }
                // 9. Area Tanda Tangan
                else if (meta.signStartRow && r >= meta.signStartRow) {
                    fill = "FFFFFF";
                    align = "center";
                    border = null;
                    if (meta.signBoldUnderlineRows && meta.signBoldUnderlineRows.includes(r)) {
                        bold = true;
                        underline = true;
                        fontSize = 10.5;
                    } else {
                        bold = (r === meta.signStartRow || r === meta.signStartRow + 1 || r === meta.signStartRow + 2);
                        fontSize = 9.5;
                    }
                }

                cell.s = {
                    font: { name: "Calibri", sz: fontSize, bold: bold, italic: italic, underline: underline, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: "center", wrapText: true },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt) cell.z = numFmt;
            }
        }
    }

    function formatRupiahReklas(num) {
        if (num == null || isNaN(num)) return '0,00';
        return Number(num).toLocaleString('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    let isExportingRekapTriwulan = false;
    function exportRekapTriwulanToExcel(params = {}) {
        if (isExportingRekapTriwulan) return;
        isExportingRekapTriwulan = true;

        if (typeof XLSX === 'undefined') {
            alert('⚠️ Pustaka Excel sedang dimuat, silakan coba 1 detik lagi...');
            isExportingRekapTriwulan = false;
            return;
        }

        const rawAstaps = window.__simatAstaps || [];
        const rawDeletedAstaps = window.__simatDeletedAstaps || [];
        const wb = XLSX.utils.book_new();

        const filterYear = params.year || 'all';
        const filterTw = params.triwulan || 'all';
        const filterSheet = params.sheet || 'all'; // 'all' | 'sheet1' | 'sheet2' | 'sheet3' | 'sheet4'

        // Filter data belanja modal (Aset Tetap) berdasarkan Tahun dan Triwulan
        let filteredAstaps = rawAstaps.filter(item => {
            const matchYear = filterYear === 'all' || String(item.tahun_perolehan) === String(filterYear);
            let matchTw = true;
            if (filterTw !== 'all') {
                const targetKey = filterTw.replace(/[\s_]/g, '').toUpperCase();
                const itemTw = (item.triwulan || 'TWI').replace(/[\s_]/g, '').toUpperCase();
                matchTw = (itemTw === targetKey) ||
                          (targetKey === 'TWI' && itemTw === 'TW1') || (targetKey === 'TW1' && itemTw === 'TWI') ||
                          (targetKey === 'TWII' && itemTw === 'TW2') || (targetKey === 'TW2' && itemTw === 'TWII') ||
                          (targetKey === 'TWIII' && itemTw === 'TW3') || (targetKey === 'TW3' && itemTw === 'TWIII') ||
                          (targetKey === 'TWIV' && itemTw === 'TW4') || (targetKey === 'TW4' && itemTw === 'TWIV');
            }
            return matchYear && matchTw;
        });

        // Filter data pengurangan aset tetap (Recycle Bin / is_deleted = 1) berdasarkan Tahun dan Triwulan
        let filteredDeletedAstaps = rawDeletedAstaps.filter(item => {
            const matchYear = filterYear === 'all' || String(item.deleted_year) === String(filterYear) || String(item.tahun_perolehan) === String(filterYear);
            let matchTw = true;
            if (filterTw !== 'all') {
                const targetKey = filterTw.replace(/[\s_]/g, '').toUpperCase();
                const itemTw = (item.deleted_tw || item.triwulan || 'TWI').replace(/[\s_]/g, '').toUpperCase();
                matchTw = (itemTw === targetKey) ||
                          (targetKey === 'TWI' && itemTw === 'TW1') || (targetKey === 'TW1' && itemTw === 'TWI') ||
                          (targetKey === 'TWII' && itemTw === 'TW2') || (targetKey === 'TW2' && itemTw === 'TWII') ||
                          (targetKey === 'TWIII' && itemTw === 'TW3') || (targetKey === 'TW3' && itemTw === 'TWIII') ||
                          (targetKey === 'TWIV' && itemTw === 'TW4') || (targetKey === 'TW4' && itemTw === 'TWIV');
            }
            return matchYear && matchTw;
        });

        // Label Dinamis untuk Periode Laporan
        let twLabel = "KESELURUHAN (TAHUNAN)";
        let twTabName = "TW";
        if (filterTw === 'TW I' || filterTw === 'TW1') { twLabel = "TRIWULAN I (JANUARI - MARET)"; twTabName = "TW I"; }
        else if (filterTw === 'TW II' || filterTw === 'TW2') { twLabel = "TRIWULAN II (APRIL - JUNI)"; twTabName = "TW II"; }
        else if (filterTw === 'TW III' || filterTw === 'TW3') { twLabel = "TRIWULAN III (JULI - SEPTEMBER)"; twTabName = "TW III"; }
        else if (filterTw === 'TW IV' || filterTw === 'TW4') { twLabel = "TRIWULAN IV (OKTOBER - DESEMBER)"; twTabName = "TW IV"; }

        const yearLabel = filterYear === 'all' ? (new Date().getFullYear()) : filterYear;
        const bannerTw = filterTw === 'all' ? ("TAHUN ANGGARAN " + yearLabel) : (twLabel + " TAHUN ANGGARAN " + yearLabel);

        // Judul Dinamis Periode Laporan Sesuai Standar Resmi
        let judulPeriode = "TAHUN ANGGARAN " + yearLabel;
        if (filterTw === 'TW I' || filterTw === 'TW1') { judulPeriode = "TRIWULAN I TAHUN ANGGARAN " + yearLabel; }
        else if (filterTw === 'TW II' || filterTw === 'TW2') { judulPeriode = "TRIWULAN II TAHUN ANGGARAN " + yearLabel; }
        else if (filterTw === 'TW III' || filterTw === 'TW3') { judulPeriode = "TRIWULAN III TAHUN ANGGARAN " + yearLabel; }
        else if (filterTw === 'TW IV' || filterTw === 'TW4') { judulPeriode = "TRIWULAN IV TAHUN ANGGARAN " + yearLabel; }

        const ppkNama = (filteredAstaps.find(a => a.ppk_nama && a.ppk_nama !== '-') || {}).ppk_nama || "dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR";
        const ppkNip  = (filteredAstaps.find(a => a.ppk_nip && a.ppk_nip !== '-') || {}).ppk_nip || "19771002 200604 1 007";
        const signDate = getReportSignDate(filterTw, filterYear);

        // =========================================================================
        // SHEET 1: 1. DAFTAR AT (DAFTAR ASET TETAP PENAMBAHAN SESUAI SPJ - 19 KOLOM)
        // KOLOM 1-5: BELANJA MODAL | KOLOM 6-18: RINCIAN ASET INVENTARIS | KOLOM 19: KET. (BERDIRI SENDIRI)
        // =========================================================================
        const superHeaderBelanja = "BELANJA MODAL";
        const superHeaderRincian = "RINCIAN ASET INVENTARIS PENAMBAHAN " + yearLabel + " SESUAI SPJ";

        let sheet1Rows = [
            // Baris 0 - 3: Judul Resmi Laporan Sesuai Permintaan
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            ["RUMAH SAKIT UMUM dr. H. KOESNANDI"],
            ["DAFTAR PENAMBAHAN ASET TETAP"],
            [judulPeriode],

            // Baris 4: Sumber Dana Sesuai Format Resmi (Col A & B untuk SUMBER DANA, Col C dst untuk sisanya)
            ["SUMBER DANA", "", ": KELOMPOK ANGGARAN  (BLUD)"],

            // Baris 5 (Header Level 1 - Superheader)
            // Kolom 19 (KET.) Berdiri Sendiri di Baris 5-7, tidak masuk dalam RINCIAN ASET INVENTARIS
            [
                "NO",                           // Col 0 (No 1)
                superHeaderBelanja, "", "", "", // Col 1, 2, 3, 4 (No 2-5: BELANJA MODAL)
                superHeaderRincian,             // Col 5 (No 6: No Rek 108)
                "",                             // Col 6 (No 7: Nama Aset)
                "",                             // Col 7 (No 8: Merk)
                "",                             // Col 8 (No 9: Type)
                "",                             // Col 9 (No 10: No Pabrik)
                "",                             // Col 10 (No 11: Volume Jumlah)
                "",                             // Col 11 (No 12: Volume Satuan)
                "",                             // Col 12 (No 13: Nilai Perolehan)
                "",                             // Col 13 (No 14: Administrasi Proyek)
                "",                             // Col 14 (No 15: Nilai Aset)
                "",                             // Col 15 (No 16: Bukti Nomor)
                "",                             // Col 16 (No 17: Bukti Tanggal)
                "",                             // Col 17 (No 18: Lokasi Barang)
                "KET."                          // Col 18 (No 19: KET. - BERDIRI SENDIRI!)
            ],

            // Baris 6 (Header Level 2 - Nama Kolom Utama & Sub-Superheader)
            [
                "",                             // Col 0: Merged with NO
                "No Rek. Bel. Modal",           // Col 1
                "RINCIAN BELANJA MODAL",        // Col 2
                "JUMLAH ANGGARAN\n(Rp)",        // Col 3
                "JUMLAH REALISASI\nSPM (Rp)",   // Col 4
                "No Rek. Menurut\nPERMENDAGRI 108/2016", // Col 5
                "NAMA ASET INVENTARIS",         // Col 6
                "MERK",                         // Col 7
                "TYPE",                         // Col 8
                "NO PABRIK/NO\nCHASIS/NO MESIN",// Col 9
                "VOLUME", "",                   // Col 10-11: Merged horizontal VOLUME
                "NILAI\nPEROLEHAN (Rp)",        // Col 12
                "ADMINISTRASI PROYEK\n(Pengawasan, Perencanaan,\nAP) (Rp)", // Col 13
                "NILAI ASET (Rp)",              // Col 14
                "BUKTI PENGADAAN", "",          // Col 15-16: Merged horizontal BUKTI PENGADAAN
                "LOKASI BARANG",                // Col 17
                "KET."                          // Col 18: KET. (Berdiri Sendiri)
            ],

            // Baris 7 (Header Level 3 - Sub Kolom VOLUME & BUKTI PENGADAAN)
            [
                "", "", "", "", "", "", "", "", "", "", // Col 0 s/d 9 (10 kolom)
                "JUMLAH\nBARANG",               // Col 10
                "NAMA\nSATUAN\nBARANG",         // Col 11
                "", "", "",                     // Col 12, 13, 14
                "NOMOR",                        // Col 15
                "TANGGAL",                      // Col 16
                "",                             // Col 17
                "KET."                          // Col 18: KET. (Berdiri Sendiri)
            ],

            // Baris 8 (Header Level 4 - Penomoran Kolom 1 s/d 19)
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10",
                "11", "12", "13", "14", "15 = 13 + 14", "16", "17", "18", "19"
            ]
        ];

        let totalS1Anggaran = 0;
        let totalS1RealisasiSpm = 0;
        let totalS1Volume = 0;
        let totalS1NilaiPerolehan = 0;
        let totalS1AdminProyek = 0;
        let totalS1NilaiAset = 0;

        const categoryConfigs = [
            { key: 'KIB A', title: 'Belanja Modal Tanah' },
            { key: 'KIB B', title: 'Belanja Modal Peralatan dan Mesin' },
            { key: 'KIB C', title: 'Belanja Modal Gedung dan Bangunan' },
            { key: 'KIB D', title: 'Belanja Modal Jalan, Irigasi dan Jaringan' },
            { key: 'KIB E', title: 'Belanja Modal Aset Tetap Lainnya' },
            { key: 'KIB F', title: 'Belanja Modal Konstruksi Dalam Pengerjaan' },
            { key: 'ATB',   title: 'Belanja Modal Aset Tidak Berwujud' },
            { key: 'EXTRACOM', title: 'Belanja Modal Ekstrakomptabel' }
        ];

        let globalS1No = 1;
        const categoryHeaderRowIndices = [];
        const groupRanges = [];

        function extractAstapRincianRows(item) {
            let spec = item.spesifikasi_json;
            if (typeof spec === 'string') {
                try { spec = JSON.parse(spec); } catch (e) { spec = {}; }
            }
            
            // 1. KIB B / EXTRACOM: mesin_items
            if (spec && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                return spec.mesin_items.map(m => {
                    const vol = parseInt(m.mesin_jumlah_barang) || 1;
                    const rawNilaiSatuan = parseFloat(m.mesin_nilai_satuan);
                    const nilaiSatuan = !isNaN(rawNilaiSatuan) && rawNilaiSatuan > 0 
                        ? rawNilaiSatuan 
                        : (parseFloat(item.harga_satuan) || (parseFloat(item.total_realisasi) / (parseInt(item.jumlah_volume) || 1)) || 0);
                    const adminProyek = parseFloat(m.mesin_administrasi_proyek) || 0;
                    const nilaiPerolehan = Math.max(0, vol * nilaiSatuan);
                    const nilaiAset = nilaiPerolehan + adminProyek;
                    
                    let noIdentifiers = [];
                    if (m.mesin_no_pabrik && m.mesin_no_pabrik !== '-') noIdentifiers.push(m.mesin_no_pabrik);
                    if (m.mesin_no_rangka && m.mesin_no_rangka !== '-') noIdentifiers.push('Chasis: ' + m.mesin_no_rangka);
                    if (m.mesin_no_mesin && m.mesin_no_mesin !== '-') noIdentifiers.push('Mesin: ' + m.mesin_no_mesin);
                    if (m.mesin_no_polisi && m.mesin_no_polisi !== '-') noIdentifiers.push('Nopol: ' + m.mesin_no_polisi);
                    const noPabrik = noIdentifiers.length > 0 ? noIdentifiers.join(' / ') : (item.no_pabrik || item.no_mesin || item.no_rangka || '-');

                    const lokasi = m.ruang_pemegang_mesin || m.ruang_pemegang || item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '') || item.alamat_barang || 'RSUD Dr. H. Koesnandi';

                    return {
                        kode_108: m.mesin_kode_barang || item.kode_barang || item.jenis_aset_kode || item.sub_rincian_kode || '-',
                        nama_barang: m.mesin_nama_barang || item.nama_barang || '-',
                        merk: m.mesin_merk || item.merk || '-',
                        type: m.mesin_type || item.type || '-',
                        no_pabrik: noPabrik,
                        vol: vol,
                        satuan: m.mesin_satuan || item.satuan || 'Buah',
                        nilai_perolehan: nilaiPerolehan,
                        admin_proyek: adminProyek,
                        nilai_aset: nilaiAset,
                        lokasi: lokasi
                    };
                });
            }

            // 2. KIB C: gedung_items
            if (spec && Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) {
                return spec.gedung_items.map(g => {
                    const vol = parseInt(g.gedung_jumlah_bangunan) || 1;
                    const nilaiSatuan = parseFloat(g.gedung_nilai_satuan) || 0;
                    const adminProyek = parseFloat(g.gedung_administrasi_proyek) || 0;
                    const nilaiPerolehan = Math.max(0, vol * nilaiSatuan);
                    const nilaiAset = nilaiPerolehan + adminProyek;
                    const lokasi = g.gedung_lokasi || g.ruang_pemegang || item.ruang_unit || item.alamat_barang || 'RSUD Dr. H. Koesnandi';

                    return {
                        kode_108: g.gedung_kode_barang || item.kode_barang || item.jenis_aset_kode || item.sub_rincian_kode || '-',
                        nama_barang: g.gedung_nama_bangunan || item.nama_barang || '-',
                        merk: '-',
                        type: g.gedung_konstruksi || item.type || '-',
                        no_pabrik: g.gedung_dokumen_nomor ? ('Dok: ' + g.gedung_dokumen_nomor) : '-',
                        vol: vol,
                        satuan: g.gedung_satuan || item.satuan || 'Unit',
                        nilai_perolehan: nilaiPerolehan,
                        admin_proyek: adminProyek,
                        nilai_aset: nilaiAset,
                        lokasi: lokasi
                    };
                });
            }

            // 3. KIB A: tanah_items
            if (spec && Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) {
                return spec.tanah_items.map(t => {
                    const vol = parseInt(t.tanah_jumlah_bidang) || 1;
                    const nilaiSatuan = parseFloat(t.tanah_nilai_satuan) || 0;
                    const adminProyek = parseFloat(t.tanah_administrasi_proyek) || 0;
                    const nilaiPerolehan = Math.max(0, vol * nilaiSatuan);
                    const nilaiAset = nilaiPerolehan + adminProyek;
                    const lokasi = t.tanah_letak_alamat || item.alamat_barang || 'RSUD Dr. H. Koesnandi';

                    return {
                        kode_108: t.tanah_kode_barang || item.kode_barang || item.jenis_aset_kode || item.sub_rincian_kode || '-',
                        nama_barang: t.tanah_nama_barang || item.nama_barang || '-',
                        merk: '-',
                        type: '-', // Tanah tidak memiliki tipe pabrikan
                        no_pabrik: t.tanah_sertifikat_no ? ('Sertifikat: ' + t.tanah_sertifikat_no) : (item.sertifikat_nomor && item.sertifikat_nomor !== '-' ? ('Sertifikat: ' + item.sertifikat_nomor) : '-'),
                        vol: vol,
                        satuan: t.tanah_satuan || item.satuan || 'Bidang',
                        nilai_perolehan: nilaiPerolehan,
                        admin_proyek: adminProyek,
                        nilai_aset: nilaiAset,
                        lokasi: lokasi
                    };
                });
            }

            // 4. Fallback Multi-Register jika registers > 1
            if (item.registers && Array.isArray(item.registers) && item.registers.length > 1) {
                const totalVol = parseInt(item.jumlah_volume) || item.registers.length;
                const realisasiSpm = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                const adminProyekTotal = typeof item.biaya_administrasi_proyek === 'number' ? item.biaya_administrasi_proyek : (parseFloat(item.biaya_administrasi_proyek) || 0);
                const nilaiPerolehanTotal = Math.max(0, realisasiSpm - adminProyekTotal);
                
                const unitPerolehan = Math.max(0, nilaiPerolehanTotal / totalVol);
                const unitAdmin = adminProyekTotal / totalVol;
                const unitAset = unitPerolehan + unitAdmin;

                let noIdentifiers = [];
                if (item.no_pabrik && item.no_pabrik !== '-') noIdentifiers.push('Pabrik: ' + item.no_pabrik);
                if (item.no_rangka && item.no_rangka !== '-') noIdentifiers.push('Chasis: ' + item.no_rangka);
                if (item.no_mesin && item.no_mesin !== '-') noIdentifiers.push('Mesin: ' + item.no_mesin);
                if (item.no_polisi && item.no_polisi !== '-') noIdentifiers.push('Nopol: ' + item.no_polisi);
                let noPabrikChasisMesin = noIdentifiers.length > 0 ? noIdentifiers.join(' / ') : (item.no_pabrik || item.no_mesin || item.no_rangka || '-');
                // Sanitasi ketat: Jangan biarkan NIBAR atau string register masuk ke Kolom No Pabrik/Chasis/Mesin
                if (typeof noPabrikChasisMesin === 'string' && noPabrikChasisMesin.length > 25 && /^\d+$/.test(noPabrikChasisMesin.replace(/[\.\s]/g, ''))) {
                    noPabrikChasisMesin = '-';
                }

                return item.registers.map(reg => ({
                    kode_108: item.kode_barang || item.jenis_aset_kode || item.sub_rincian_kode || '-',
                    nama_barang: item.nama_barang || '-',
                    merk: item.merk || '-',
                    type: (item.category === 'KIB A' || (item.kode_barang && item.kode_barang.startsWith('1.3.1'))) ? '-' : (item.type || '-'),
                    no_pabrik: noPabrikChasisMesin, // Murni no pabrik / chasis / mesin, BUKAN NIBAR
                    vol: 1,
                    satuan: item.satuan || 'Unit',
                    nilai_perolehan: unitPerolehan,
                    admin_proyek: unitAdmin,
                    nilai_aset: unitAset,
                    lokasi: reg.ruang_pemegang || item.ruang_unit || item.alamat_barang || 'RSUD Dr. H. Koesnandi'
                }));
            }

            // 5. Default / Single Item
            const vol = parseInt(item.jumlah_volume) || 1;
            const realisasiSpm = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
            const adminProyek = typeof item.biaya_administrasi_proyek === 'number' ? item.biaya_administrasi_proyek : (parseFloat(item.biaya_administrasi_proyek) || 0);
            const nilaiPerolehan = Math.max(0, realisasiSpm - adminProyek);
            const nilaiAset = realisasiSpm;

            let noIdentifiers = [];
            if (item.no_pabrik && item.no_pabrik !== '-') noIdentifiers.push('Pabrik: ' + item.no_pabrik);
            if (item.no_rangka && item.no_rangka !== '-') noIdentifiers.push('Chasis: ' + item.no_rangka);
            if (item.no_mesin && item.no_mesin !== '-') noIdentifiers.push('Mesin: ' + item.no_mesin);
            if (item.no_polisi && item.no_polisi !== '-') noIdentifiers.push('Nopol: ' + item.no_polisi);
            let noPabrikChasisMesin = noIdentifiers.length > 0 ? noIdentifiers.join(' / ') : (item.no_pabrik || item.no_mesin || item.no_rangka || '-');
            if (typeof noPabrikChasisMesin === 'string' && noPabrikChasisMesin.length > 25 && /^\d+$/.test(noPabrikChasisMesin.replace(/[\.\s]/g, ''))) {
                noPabrikChasisMesin = '-';
            }
            const lokasiBarang = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '') || item.alamat_barang || 'RSUD Dr. H. Koesnandi';

            return [{
                kode_108: item.kode_barang || item.jenis_aset_kode || item.sub_rincian_kode || '-',
                nama_barang: item.nama_barang || '-',
                merk: item.merk || '-',
                type: (item.category === 'KIB A' || (item.kode_barang && item.kode_barang.startsWith('1.3.1'))) ? '-' : (item.type || '-'),
                no_pabrik: noPabrikChasisMesin,
                vol: vol,
                satuan: item.satuan || 'Unit',
                nilai_perolehan: nilaiPerolehan,
                admin_proyek: adminProyek,
                nilai_aset: nilaiAset,
                lokasi: lokasiBarang
            }];
        }

        categoryConfigs.forEach(cfg => {
            const catItems = filteredAstaps.filter(it => resolveItemCategory(it) === cfg.key);
            // Sesuai permintaan user: jika jenis aset tidak ada barangnya (misal ATB kosong), jangan tulis
            if (catItems.length === 0) return;

            // Baris Header Kategori (Latar Abu-abu, Teks Bold di Kolom 3)
            // Sesuai Gambar 3: TIDAK di-merge, setiap kolom memiliki garis vertikal pemisah utuh
            categoryHeaderRowIndices.push(sheet1Rows.length);
            sheet1Rows.push([
                "",                     // 1. NO
                "",                     // 2. No Rek. Bel. Modal
                cfg.title,              // 3. RINCIAN BELANJA MODAL (e.g. Belanja Modal Peralatan dan Mesin)
                "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", ""
            ]);

            // Kelompokkan item berdasarkan Rekening Belanja & Nilai Anggaran/Realisasi yang sama
            const rekGroups = [];
            const rekGroupMap = new Map();

            catItems.forEach(item => {
                const rekKode = item.rekening_kode || '-';
                const rekNama = item.rekening_nama || '-';
                const anggaran = typeof item.jumlah_anggaran === 'number' ? item.jumlah_anggaran : (parseFloat(item.jumlah_anggaran) || 0);
                const realisasiSpm = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);

                const cleanRekKode = String(rekKode).trim();
                const cleanRekNama = String(rekNama).trim();
                const numAnggaran = Math.round(anggaran * 100) / 100;
                const numRealisasi = Math.round(realisasiSpm * 100) / 100;

                // Kunci kelompok: Rekening Belanja + Anggaran + Realisasi (dijadikan 1 sesuai instruksi user)
                const groupKey = (cleanRekKode !== '-' && cleanRekKode !== '') 
                    ? `${cleanRekKode}__${cleanRekNama}__${numAnggaran}__${numRealisasi}` 
                    : `ITEM_${item.id || Math.random()}`;

                if (!rekGroupMap.has(groupKey)) {
                    const newGrp = {
                        rekKode: cleanRekKode,
                        rekNama: cleanRekNama,
                        anggaran: numAnggaran,
                        realisasiSpm: numRealisasi,
                        items: []
                    };
                    rekGroupMap.set(groupKey, newGrp);
                    rekGroups.push(newGrp);
                }
                rekGroupMap.get(groupKey).items.push(item);
            });

            rekGroups.forEach(grp => {
                const groupStartRow = sheet1Rows.length;
                const currentNo = globalS1No++;

                // Akumulasi Anggaran & Realisasi dihitung 1x saja per Belanja Modal
                totalS1Anggaran += grp.anggaran;
                totalS1RealisasiSpm += grp.realisasiSpm;

                let isFirstRowInGroup = true;

                grp.items.forEach(item => {
                    // Bukti Pengadaan (Nomor & Tanggal)
                    const buktiNomor = (item.spk_nomor && item.spk_nomor !== '-') ? item.spk_nomor :
                                       ((item.surat_pesanan_nomor && item.surat_pesanan_nomor !== '-') ? item.surat_pesanan_nomor :
                                       ((item.bast_dokumen_nomor && item.bast_dokumen_nomor !== '-') ? item.bast_dokumen_nomor :
                                       ((item.faktur_nomor && item.faktur_nomor !== '-') ? item.faktur_nomor :
                                       ((item.sp2d_nomor && item.sp2d_nomor !== '-') ? item.sp2d_nomor :
                                       ((item.kwitansi_nomor && item.kwitansi_nomor !== '-') ? item.kwitansi_nomor : '-')))));

                    const rawBuktiDate = (item.spk_nomor && item.spk_nomor !== '-') ? item.spk_tanggal :
                                         ((item.surat_pesanan_nomor && item.surat_pesanan_nomor !== '-') ? item.surat_pesanan_tanggal :
                                         ((item.bast_dokumen_nomor && item.bast_dokumen_nomor !== '-') ? item.bast_dokumen_tanggal :
                                         ((item.faktur_nomor && item.faktur_nomor !== '-') ? item.faktur_tanggal :
                                         ((item.sp2d_nomor && item.sp2d_nomor !== '-') ? item.sp2d_tanggal :
                                         ((item.kwitansi_nomor && item.kwitansi_nomor !== '-') ? item.kwitansi_tanggal : (item.spk_tanggal || item.bast_dokumen_tanggal || '-'))))));

                    const buktiTanggal = (rawBuktiDate && rawBuktiDate !== '-') ? formatAstapDate(rawBuktiDate) : '-';

                    // Keterangan: Jika diisi maka gunakan isinya, jika kosong fallback format resmi Dana BLUD-BM.[NamaBarang]
                    let keterangan = item.keterangan_tambahan || item.keterangan || '';
                    if (!keterangan || keterangan === '-') {
                        const asalDana = item.asal_usul ? ('Dana ' + item.asal_usul) : 'Dana BLUD';
                        const namaSingkat = (item.nama_barang || '').replace(/^(Pengadaan|Belanja Modal|Pembelian)\s+/i, '');
                        keterangan = asalDana + '-BM.' + (namaSingkat || 'Aset Tetap');
                    }

                    const rincianList = extractAstapRincianRows(item);

                    rincianList.forEach(rincian => {
                        totalS1Volume += rincian.vol;
                        totalS1NilaiAset += rincian.nilai_aset;

                        let col1to5 = [];
                        if (isFirstRowInGroup) {
                            col1to5 = [
                                currentNo,          // 1. NO
                                grp.rekKode,        // 2. No Rek. Bel. Modal
                                grp.rekNama,        // 3. RINCIAN BELANJA MODAL
                                grp.anggaran,       // 4. JUMLAH ANGGARAN (Rp)
                                grp.realisasiSpm    // 5. JUMLAH REALISASI SPM (Rp) (Bold)
                            ];
                            isFirstRowInGroup = false;
                        } else {
                            // Baris ke-2 dst dalam kelompok Belanja Modal yang sama dikosongkan (akan di-merge vertikal)
                            col1to5 = ["", "", "", "", ""];
                        }

                        sheet1Rows.push([
                            ...col1to5,
                            rincian.kode_108,          // 6. No Rek. Menurut PERMENDAGRI 108/2016
                            rincian.nama_barang,       // 7. NAMA ASET INVENTARIS
                            rincian.merk,              // 8. MERK
                            rincian.type,              // 9. TYPE
                            rincian.no_pabrik,         // 10. NO PABRIK/NO CHASIS/NO MESIN
                            rincian.vol,               // 11. JUMLAH BARANG
                            rincian.satuan,            // 12. NAMA SATUAN BARANG
                            rincian.nilai_perolehan,   // 13. NILAI PEROLEHAN (Rp)
                            rincian.admin_proyek,      // 14. ADMINISTRASI PROYEK (Rp)
                            rincian.nilai_aset,        // 15 = 13 + 14. NILAI ASET (Rp)
                            buktiNomor,                // 16. BUKTI PENGADAAN NOMOR
                            buktiTanggal,              // 17. BUKTI PENGADAAN TANGGAL
                            rincian.lokasi,            // 18. LOKASI BARANG
                            keterangan                 // 19. KET. (Berdiri Sendiri)
                        ]);
                    });
                });

                const groupEndRow = sheet1Rows.length - 1;
                groupRanges.push({ start: groupStartRow, end: groupEndRow });
            });
        });

        // Baris Total Sheet 1
        const s1TotalRowIdx = sheet1Rows.length;
        sheet1Rows.push([
            "JUMLAH TOTAL", "", "",
            totalS1Anggaran,
            totalS1RealisasiSpm,
            "", "", "", "", "",
            totalS1Volume,
            "",
            "", // 13. NILAI PEROLEHAN: Kosong (tidak dijumlah di bawah)
            "", // 14. ADMINISTRASI PROYEK: Kosong (tidak dijumlah di bawah)
            totalS1NilaiAset, // 15. NILAI ASET: Tetap dijumlah
            "", "", "", ""
        ]);

        // Tanda Tangan Sheet 1 (Format Baku RSUD Koesnadi)
        const s1SignStartRow = sheet1Rows.length;
        const s1SignRows = buildKibSignatureRows(19, 13, ppkNama, ppkNip, signDate, 1);
        s1SignRows.forEach(r => sheet1Rows.push(r));

        const wsSheet1 = XLSX.utils.aoa_to_sheet(sheet1Rows);
        wsSheet1['!cols'] = [
            {wch: 6},   // 1. NO
            {wch: 22},  // 2. No Rek. Bel. Modal
            {wch: 38},  // 3. RINCIAN BELANJA MODAL
            {wch: 22},  // 4. JUMLAH ANGGARAN (Rp)
            {wch: 24},  // 5. JUMLAH REALISASI SPM (Rp)
            {wch: 26},  // 6. No Rek. Menurut PERMENDAGRI 108/2016
            {wch: 38},  // 7. NAMA ASET INVENTARIS
            {wch: 20},  // 8. MERK
            {wch: 32},  // 9. TYPE (Diperlebar dari 20 ke 32)
            {wch: 38},  // 10. NO PABRIK/NO CHASIS/NO MESIN (Diperlebar dari 28 ke 38)
            {wch: 14},  // 11. JUMLAH BARANG
            {wch: 16},  // 12. NAMA SATUAN BARANG
            {wch: 22},  // 13. NILAI PEROLEHAN (Rp)
            {wch: 26},  // 14. ADMINISTRASI PROYEK (Rp)
            {wch: 22},  // 15. NILAI ASET (Rp)
            {wch: 28},  // 16. BUKTI PENGADAAN NOMOR
            {wch: 16},  // 17. BUKTI PENGADAAN TANGGAL
            {wch: 28},  // 18. LOKASI BARANG
            {wch: 55}   // 19. KET. (Berdiri Sendiri) (Diperlebar dari 28 ke 55)
        ];

        const rowsHeights = [
            { hpt: 22 }, // 0: PEMERINTAH KABUPATEN BONDOWOSO
            { hpt: 22 }, // 1: RUMAH SAKIT UMUM dr. H. KOESNANDI
            { hpt: 26 }, // 2: DAFTAR PENAMBAHAN ASET TETAP
            { hpt: 22 }, // 3: TRIWULAN ... TAHUN ANGGARAN ...
            { hpt: 22 }, // 4: SUMBER DANA : KELOMPOK ANGGARAN  (BLUD)
            { hpt: 28 }, // 5: Superheader BELANJA MODAL & RINCIAN ASET...
            { hpt: 46 }, // 6: Header Kolom Utama & Sub-Superheader
            { hpt: 28 }, // 7: Subheader Volume & Bukti Pengadaan
            { hpt: 20 }  // 8: Baris Nomor 1 s/d 19
        ];
        for (let rIdx = 9; rIdx < sheet1Rows.length; rIdx++) {
            if (categoryHeaderRowIndices.includes(rIdx)) {
                rowsHeights.push({ hpt: 22 });
            } else if (rIdx === s1TotalRowIdx) {
                rowsHeights.push({ hpt: 24 });
            } else if (rIdx > s1TotalRowIdx) {
                rowsHeights.push({ hpt: 20 });
            } else {
                rowsHeights.push({ hpt: 24 });
            }
        }
        wsSheet1['!rows'] = rowsHeights;
        wsSheet1['!merges'] = [
            // Judul Laporan (Row 0 - 3, Col 0 - 18)
            { s: { r: 0, c: 0 }, e: { r: 0, c: 18 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 18 } },
            { s: { r: 2, c: 0 }, e: { r: 2, c: 18 } },
            { s: { r: 3, c: 0 }, e: { r: 3, c: 18 } },

            // Baris 4: Sumber Dana (Col A & B di-merge untuk SUMBER DANA, Col C dst untuk sisanya)
            { s: { r: 4, c: 0 }, e: { r: 4, c: 1 } },   // SUMBER DANA (Merge Kolom A & B)
            { s: { r: 4, c: 2 }, e: { r: 4, c: 8 } },   // : KELOMPOK ANGGARAN  (BLUD) (Merge Kolom C s/d I)

            // Superheader Row 5
            { s: { r: 5, c: 0 }, e: { r: 7, c: 0 } },   // NO (vertical merge Row 5 s/d Row 7 - Berdiri Sendiri)
            { s: { r: 5, c: 1 }, e: { r: 5, c: 4 } },   // BELANJA MODAL (Col 1 s/d Col 4)
            { s: { r: 5, c: 5 }, e: { r: 5, c: 17 } },  // RINCIAN ASET INVENTARIS... (Col 5 s/d Col 17 HANYA sampai LOKASI BARANG)
            { s: { r: 5, c: 18 }, e: { r: 7, c: 18 } }, // KET. (vertical merge Row 5 s/d Row 7 - BERDIRI SENDIRI!)

            // Sub-header Row 6 & 7 (vertical merges untuk kolom yang tidak punya subheader di row 7)
            { s: { r: 6, c: 1 }, e: { r: 7, c: 1 } },   // No Rek. Bel. Modal
            { s: { r: 6, c: 2 }, e: { r: 7, c: 2 } },   // RINCIAN BELANJA MODAL
            { s: { r: 6, c: 3 }, e: { r: 7, c: 3 } },   // JUMLAH ANGGARAN (Rp)
            { s: { r: 6, c: 4 }, e: { r: 7, c: 4 } },   // JUMLAH REALISASI SPM (Rp)
            { s: { r: 6, c: 5 }, e: { r: 7, c: 5 } },   // No Rek. Menurut PERMENDAGRI 108/2016
            { s: { r: 6, c: 6 }, e: { r: 7, c: 6 } },   // NAMA ASET INVENTARIS
            { s: { r: 6, c: 7 }, e: { r: 7, c: 7 } },   // MERK
            { s: { r: 6, c: 8 }, e: { r: 7, c: 8 } },   // TYPE
            { s: { r: 6, c: 9 }, e: { r: 7, c: 9 } },   // NO PABRIK/NO CHASIS/NO MESIN
            { s: { r: 6, c: 10 }, e: { r: 6, c: 11 } }, // VOLUME (horizontal merge Col 10 s/d Col 11 di row 6)
            { s: { r: 6, c: 12 }, e: { r: 7, c: 12 } }, // NILAI PEROLEHAN (Rp)
            { s: { r: 6, c: 13 }, e: { r: 7, c: 13 } }, // ADMINISTRASI PROYEK (Rp)
            { s: { r: 6, c: 14 }, e: { r: 7, c: 14 } }, // NILAI ASET (Rp)
            { s: { r: 6, c: 15 }, e: { r: 6, c: 16 } }, // BUKTI PENGADAAN (horizontal merge Col 15 s/d Col 16 di row 6)
            { s: { r: 6, c: 17 }, e: { r: 7, c: 17 } }, // LOKASI BARANG
            // Kolom 18 (KET.) berdiri sendiri, sudah di-merge vertikal dari row 5 s/d 7 di atas!

            // Baris Total Sheet 1 (JUMLAH TOTAL di Col 0 s/d Col 2)
            { s: { r: s1TotalRowIdx, c: 0 }, e: { r: s1TotalRowIdx, c: 2 } },

            // Tanda Tangan Sheet 1 (c1 s/d c5 kiri, c13 s/d c18 kanan)
            ...getKibSignatureMerges(s1SignStartRow, 19, 13, 1, 5, 18)
        ];
        applyDaftarAtReportStyling(wsSheet1, sheet1Rows.length, 19, 5, 4, s1TotalRowIdx, categoryHeaderRowIndices, groupRanges);
        applySignatureBlockStyling(wsSheet1, s1SignStartRow, 19);
        if (filterSheet === 'all' || filterSheet === 'sheet1') {
            const s1TabTitle = filterTw === 'all' ? "1. Daftar AT Tahunan" : ("1. Daftar AT " + twTabName);
            XLSX.utils.book_append_sheet(wb, wsSheet1, filterSheet === 'sheet1' ? ("Daftar AT " + (filterTw === 'all' ? 'Tahunan' : twTabName)) : s1TabTitle);
        }

        // =========================================================================
        // SHEET 2: 2. DAFTAR PENGURANGAN AT RSDK (Template Resmi 14 Kolom)
        // Data diambil murni dari Recycle Bin (is_deleted = 1), tanpa belanja modal 2-5
        // =========================================================================
        let sheet2Rows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            ["RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI"],
            ["DAFTAR PENGURANGAN ASET TETAP TAHUN ANGGARAN " + yearLabel],
            ["PERIODE: " + bannerTw],
            ["SUMBER DANA", "", ": KELOMPOK ANGGARAN (BLUD)"],
            [
                "No.\nUrut",
                "Kode Barang\nSesuai\nPERMENDAGRI\n108/2016",
                "Register",
                "Spesifikasi Barang",
                "",
                "",
                "Bahan",
                "Asal/Cara\nPerolehan",
                "Tahun\nBeli/\nPerolehan",
                "Satuan",
                "Keadaan\nBarang\n(B/KB/RB)",
                "Jumlah",
                "",
                "Keterangan"
            ],
            [
                "",
                "",
                "",
                "Nama/Jenis\nBarang",
                "Merk/Type",
                "No.Sertifikat\nNo. Pabrik\nNo. Mesin",
                "",
                "",
                "",
                "",
                "",
                "Barang",
                "Nilai",
                ""
            ],
            [
                1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14
            ]
        ];

        let totalS2Volume = 0;
        let totalS2Nilai = 0;
        let s2ItemCounter = 0;
        const s2CategoryHeaderRowIndices = [];
        const s2GroupRanges = [];

        const s2Categories = [
            { code: 'KIB A', label: 'Tanah' },
            { code: 'KIB B', label: 'Peralatan dan Mesin' },
            { code: 'KIB C', label: 'Gedung dan Bangunan' },
            { code: 'KIB D', label: 'Jalan, Irigasi dan Jaringan' },
            { code: 'KIB E', label: 'Aset Tetap Lainnya' },
            { code: 'KIB F', label: 'Konstruksi Dalam Pengerjaan' },
            { code: 'ATB', label: 'Aset Tak Berwujud' },
            { code: 'EXTRACOM', label: 'Aset Ekstrakomptabel' }
        ];

        s2Categories.forEach(cat => {
            const catItems = filteredDeletedAstaps.filter(item => {
                if (cat.code === 'EXTRACOM') return item.is_extracomtable;
                if (item.is_extracomtable) return false;
                const c = (item.category || '').toUpperCase();
                if (cat.code === 'ATB') return c === 'ATB' || c === 'ASET TAK BERWUJUD';
                return c === cat.code || c.startsWith(cat.code);
            });

            if (catItems.length === 0) return;

            // Baris header judul kategori aset - Diperlebar (merge Kolom 0 s/d 5) agar terbaca jelas & tidak terpotong
            const catRow = new Array(14).fill("");
            catRow[0] = "  " + cat.code + " : " + cat.label.toUpperCase();
            s2CategoryHeaderRowIndices.push(sheet2Rows.length);
            sheet2Rows.push(catRow);

            catItems.forEach(item => {
                const groupStartRow = sheet2Rows.length;
                const registers = (item.registers && item.registers.length > 0) ? item.registers : null;

                if (registers && registers.length > 0) {
                    registers.forEach((reg) => {
                        s2ItemCounter++;
                        const regNibar = reg.nibar || reg.no_register || "-";
                        const regKondisi = reg.kondisi || item.kondisi || "RB";
                        const vol = 1;
                        const nilai = registers.length > 1 ? (item.harga_satuan || (item.total_realisasi_num / registers.length) || 0) : (item.total_realisasi_num || item.harga_satuan || 0);

                        totalS2Volume += vol;
                        totalS2Nilai += nilai;

                        // Dokumen identitas: Sertifikat, No Pabrik, No Mesin, No Rangka, No Polisi, ISBN. NEVER display NIBAR in Kolom 6!
                        let docIdentitas = item.dokumen_identitas || item.sertifikat_nomor || item.no_pabrik || item.no_mesin || "-";
                        if (docIdentitas === regNibar || (typeof docIdentitas === 'string' && docIdentitas.length > 30 && docIdentitas.includes('.'))) {
                            docIdentitas = "-";
                        }

                        const s2MerkType = [item.merk, item.type].filter(x => x && x !== '-' && x !== 'Baik').join(' / ') || item.merk || item.type || "-";

                        sheet2Rows.push([
                            s2ItemCounter,                                                                  // 1. No. Urut (Berurutan 1, 2, 3...)
                            item.kode_barang || "-",                                                        // 2. Kode Barang 108
                            regNibar,                                                                       // 3. Register
                            item.nama_barang || "-",                                                        // 4. Nama/Jenis Barang
                            s2MerkType,                                                                     // 5. Merk/Type (Digabung rapi jika ada keduanya)
                            docIdentitas,                                                                   // 6. No.Sertifikat No. Pabrik No. Mesin
                            item.bahan || "-",                                                              // 7. Bahan
                            item.asal_usul || "Pembelian BLUD",                                             // 8. Asal/Cara Perolehan
                            item.tahun_perolehan || yearLabel,                                              // 9. Tahun Beli/Perolehan
                            item.satuan || "Unit",                                                          // 10. Satuan
                            regKondisi,                                                                     // 11. Keadaan Barang (B/KB/RB)
                            vol,                                                                            // 12. Jumlah Barang
                            nilai,                                                                          // 13. Jumlah Nilai
                            item.keterangan || item.alasan_hapus || "-"                                     // 14. Keterangan
                        ]);
                    });
                } else {
                    s2ItemCounter++;
                    const vol = item.jumlah_volume || 1;
                    const nilai = item.total_realisasi_num || (item.harga_satuan * vol) || 0;
                    totalS2Volume += vol;
                    totalS2Nilai += nilai;

                    const itemNibar = item.nibar || item.no_register || "-";
                    let docIdentitas = item.dokumen_identitas || item.sertifikat_nomor || item.no_pabrik || item.no_mesin || "-";
                    if (docIdentitas === itemNibar || (typeof docIdentitas === 'string' && docIdentitas.length > 30 && docIdentitas.includes('.'))) {
                        docIdentitas = "-";
                    }

                    const s2MerkType = [item.merk, item.type].filter(x => x && x !== '-' && x !== 'Baik').join(' / ') || item.merk || item.type || "-";

                    sheet2Rows.push([
                        s2ItemCounter,                                                                  // 1. No. Urut (Berurutan 1, 2, 3...)
                        item.kode_barang || "-",                                                        // 2. Kode Barang 108
                        itemNibar,                                                                      // 3. Register
                        item.nama_barang || "-",                                                        // 4. Nama/Jenis Barang
                        s2MerkType,                                                                     // 5. Merk/Type (Digabung rapi jika ada keduanya)
                        docIdentitas,                                                                   // 6. No.Sertifikat No. Pabrik No. Mesin
                        item.bahan || "-",                                                              // 7. Bahan
                        item.asal_usul || "Pembelian BLUD",                                             // 8. Asal/Cara Perolehan
                        item.tahun_perolehan || yearLabel,                                              // 9. Tahun Beli/Perolehan
                        item.satuan || "Unit",                                                          // 10. Satuan
                        item.kondisi || "RB",                                                           // 11. Keadaan Barang (B/KB/RB)
                        vol,                                                                            // 12. Jumlah Barang
                        nilai,                                                                          // 13. Jumlah Nilai
                        item.keterangan || item.alasan_hapus || "-"                                     // 14. Keterangan
                    ]);
                }

                const groupEndRow = sheet2Rows.length - 1;
                s2GroupRanges.push({ start: groupStartRow, end: groupEndRow });
            });
        });

        // Baris Total Sheet 2 (JUMLAH TOTAL PENGURANGAN ASET TETAP)
        const s2TotalRowIdx = sheet2Rows.length;
        sheet2Rows.push([
            "JUMLAH TOTAL PENGURANGAN ASET TETAP", "", "", "", "", "", "", "", "", "", "",
            totalS2Volume,
            totalS2Nilai,
            ""
        ]);

        // Tanda Tangan Sheet 2 (Format Baku RSUD Koesnadi)
        const s2SignStartRow = sheet2Rows.length;
        const s2SignRows = buildKibSignatureRows(14, 9, ppkNama, ppkNip, signDate, 1);
        s2SignRows.forEach(r => sheet2Rows.push(r));

        const wsSheet2 = XLSX.utils.aoa_to_sheet(sheet2Rows);
        wsSheet2['!cols'] = [
            { wch: 6 },  // 1. No. Urut (c=0)
            { wch: 22 }, // 2. Kode Barang 108 (c=1)
            { wch: 50 }, // 3. Register / NIBAR (c=2) - Lebar 50 muat NIBAR 45 digit tanpa terpotong
            { wch: 40 }, // 4. Nama/Jenis Barang (c=3) - Lebar diperbesar agar nama barang panjang terbaca rapi
            { wch: 22 }, // 5. Merk/Type (c=4)
            { wch: 38 }, // 6. No Sertifikat/Pabrik/Mesin (c=5) - Diperlebar dari 30 ke 38 agar teks header 3 baris dan nomor dokumen leluasa
            { wch: 16 }, // 7. Bahan (c=6)
            { wch: 20 }, // 8. Asal/Cara Perolehan (c=7)
            { wch: 14 }, // 9. Tahun Beli/Perolehan (c=8)
            { wch: 12 }, // 10. Satuan (c=9)
            { wch: 14 }, // 11. Keadaan Barang (B/KB/RB) (c=10)
            { wch: 12 }, // 12. Jumlah Barang (c=11)
            { wch: 22 }, // 13. Jumlah Nilai (c=12)
            { wch: 55 }  // 14. Keterangan (c=13) - Diperlebar dari 28 ke 55 agar keterangan panjang muat 1 baris utuh
        ];

        const s2RowHeights = [
            { hpt: 22 }, // 0: PEMERINTAH KABUPATEN BONDOWOSO
            { hpt: 22 }, // 1: RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI
            { hpt: 26 }, // 2: DAFTAR PENGURANGAN ASET TETAP
            { hpt: 22 }, // 3: PERIODE: ... TAHUN ANGGARAN ...
            { hpt: 22 }, // 4: SUMBER DANA : KELOMPOK ANGGARAN (BLUD)
            { hpt: 32 }, // 5: Superheader
            { hpt: 48 }, // 6: Subheader - Dipertinggi dari 30 ke 48 agar teks 3 baris "No.Sertifikat\nNo. Pabrik\nNo. Mesin" tidak terpotong
            { hpt: 20 }  // 7: Baris Nomor 1 s/d 14
        ];
        for (let rIdx = 8; rIdx < sheet2Rows.length; rIdx++) {
            if (s2CategoryHeaderRowIndices.includes(rIdx)) {
                s2RowHeights.push({ hpt: 22 });
            } else if (rIdx === s2TotalRowIdx) {
                s2RowHeights.push({ hpt: 24 });
            } else if (rIdx > s2TotalRowIdx) {
                s2RowHeights.push({ hpt: 20 });
            } else {
                s2RowHeights.push({ hpt: 24 });
            }
        }
        wsSheet2['!rows'] = s2RowHeights;

        wsSheet2['!merges'] = [
            // Banner Judul Laporan (Row 0 - 3, Col 0 - 13)
            { s: { r: 0, c: 0 }, e: { r: 0, c: 13 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 13 } },
            { s: { r: 2, c: 0 }, e: { r: 2, c: 13 } },
            { s: { r: 3, c: 0 }, e: { r: 3, c: 13 } },

            // Baris 4: Sumber Dana
            { s: { r: 4, c: 0 }, e: { r: 4, c: 1 } },   // SUMBER DANA
            { s: { r: 4, c: 2 }, e: { r: 4, c: 6 } },   // : KELOMPOK ANGGARAN (BLUD)

            // Superheader & Subheader (Row 5 & 6) - Mengikuti template resmi 14 kolom
            { s: { r: 5, c: 0 }, e: { r: 6, c: 0 } },   // 1. No. Urut (vertical)
            { s: { r: 5, c: 1 }, e: { r: 6, c: 1 } },   // 2. Kode Barang PERMENDAGRI 108/2016 (vertical)
            { s: { r: 5, c: 2 }, e: { r: 6, c: 2 } },   // 3. Register (vertical)
            { s: { r: 5, c: 3 }, e: { r: 5, c: 5 } },   // Spesifikasi Barang (horizontal merge Col 3 s/d 5 di Row 5)
            { s: { r: 5, c: 6 }, e: { r: 6, c: 6 } },   // 7. Bahan (vertical)
            { s: { r: 5, c: 7 }, e: { r: 6, c: 7 } },   // 8. Asal/Cara Perolehan (vertical)
            { s: { r: 5, c: 8 }, e: { r: 6, c: 8 } },   // 9. Tahun Beli/Perolehan (vertical)
            { s: { r: 5, c: 9 }, e: { r: 6, c: 9 } },   // 10. Satuan (vertical)
            { s: { r: 5, c: 10 }, e: { r: 6, c: 10 } }, // 11. Keadaan Barang (B/KB/RB) (vertical)
            { s: { r: 5, c: 11 }, e: { r: 5, c: 12 } }, // Jumlah (horizontal merge Col 11 s/d 12 di Row 5)
            { s: { r: 5, c: 13 }, e: { r: 6, c: 13 } }, // 14. Keterangan (vertical)

            // Baris Judul Kategori Aset (Merge Kolom 0 s/d 5 agar teks kategori lebar dan tidak terpotong)
            ...s2CategoryHeaderRowIndices.map(catRowIdx => ({ s: { r: catRowIdx, c: 0 }, e: { r: catRowIdx, c: 5 } })),

            // Baris Total Sheet 2 (JUMLAH TOTAL PENGURANGAN ASET TETAP di Col 0 s/d Col 10)
            { s: { r: s2TotalRowIdx, c: 0 }, e: { r: s2TotalRowIdx, c: 10 } },

            // Tanda Tangan Sheet 2 (c1 s/d c4 kiri, c9 s/d c13 kanan)
            ...getKibSignatureMerges(s2SignStartRow, 14, 9, 1, 4, 13)
        ];

        applyPenguranganAtReportStyling(wsSheet2, sheet2Rows.length, 14, 5, 3, s2TotalRowIdx, s2CategoryHeaderRowIndices, s2GroupRanges);
        applySignatureBlockStyling(wsSheet2, s2SignStartRow, 14);
        if (filterSheet === 'all' || filterSheet === 'sheet2') {
            const s2TabTitle = filterTw === 'all' ? "2. Pengurangan AT Tahunan" : ("2. Pengurangan AT " + twTabName);
            XLSX.utils.book_append_sheet(wb, wsSheet2, filterSheet === 'sheet2' ? ("Pengurangan AT " + (filterTw === 'all' ? 'Tahunan' : twTabName)) : s2TabTitle);
        }

        // =========================================================================
        // SHEET 3: 3. REKLAS RSDK (FORMAT RESMI RSUD dr. H. KOESNANDI)
        // REKLASIFIKASI ASET TETAP PER JENIS TRIWULAN BERJALAN
        // =========================================================================
        const reklasDefs = [
            // KIB A - TANAH
            { code: '1.3.1.01', group: 'TANAH', label: 'TANAH', prefix: '1.3.1' },

            // KIB B - PERALATAN DAN MESIN (19 Sub Rincian Baku)
            { code: '1.3.2.01', group: 'PERALATAN DAN MESIN', label: 'ALAT BESAR', prefix: '1.3.2.01' },
            { code: '1.3.2.02', group: 'PERALATAN DAN MESIN', label: 'ALAT ANGKUTAN', prefix: '1.3.2.02' },
            { code: '1.3.2.03', group: 'PERALATAN DAN MESIN', label: 'ALAT BENGKEL DAN ALAT UKUR', prefix: '1.3.2.03' },
            { code: '1.3.2.04', group: 'PERALATAN DAN MESIN', label: 'ALAT PERTANIAN', prefix: '1.3.2.04' },
            { code: '1.3.2.05', group: 'PERALATAN DAN MESIN', label: 'ALAT KANTOR DAN RUMAH TANGGA', prefix: '1.3.2.05' },
            { code: '1.3.2.06', group: 'PERALATAN DAN MESIN', label: 'ALAT STUDIO, KOMUNIKASI DAN PEMANCAR', prefix: '1.3.2.06' },
            { code: '1.3.2.07', group: 'PERALATAN DAN MESIN', label: 'ALAT KEDOKTERAN DAN KESEHATAN', prefix: '1.3.2.07' },
            { code: '1.3.2.08', group: 'PERALATAN DAN MESIN', label: 'ALAT LABORATORIUM', prefix: '1.3.2.08' },
            { code: '1.3.2.09', group: 'PERALATAN DAN MESIN', label: 'ALAT PERSENJATAAN', prefix: '1.3.2.09' },
            { code: '1.3.2.10', group: 'PERALATAN DAN MESIN', label: 'KOMPUTER', prefix: '1.3.2.10' },
            { code: '1.3.2.11', group: 'PERALATAN DAN MESIN', label: 'ALAT EKSPLORASI', prefix: '1.3.2.11' },
            { code: '1.3.2.12', group: 'PERALATAN DAN MESIN', label: 'ALAT PENGEBORAN', prefix: '1.3.2.12' },
            { code: '1.3.2.13', group: 'PERALATAN DAN MESIN', label: 'ALAT PRODUKSI, PENGOLAHAN DAN PEMURNIAN', prefix: '1.3.2.13' },
            { code: '1.3.2.14', group: 'PERALATAN DAN MESIN', label: 'ALAT BANTU EKSPLORASI', prefix: '1.3.2.14' },
            { code: '1.3.2.15', group: 'PERALATAN DAN MESIN', label: 'ALAT KESELAMATAN KERJA', prefix: '1.3.2.15' },
            { code: '1.3.2.16', group: 'PERALATAN DAN MESIN', label: 'ALAT PERAGA', prefix: '1.3.2.16' },
            { code: '1.3.2.17', group: 'PERALATAN DAN MESIN', label: 'PERALATAN PROSES/PRODUKSI', prefix: '1.3.2.17' },
            { code: '1.3.2.18', group: 'PERALATAN DAN MESIN', label: 'RAMBU - RAMBU', prefix: '1.3.2.18' },
            { code: '1.3.2.19', group: 'PERALATAN DAN MESIN', label: 'PERALATAN OLAH RAGA', prefix: '1.3.2.19' },

            // KIB C - GEDUNG DAN BANGUNAN (4 Sub Rincian Baku)
            { code: '1.3.3.01', group: 'GEDUNG DAN BANGUNAN', label: 'BANGUNAN GEDUNG', prefix: '1.3.3.01' },
            { code: '1.3.3.02', group: 'GEDUNG DAN BANGUNAN', label: 'MONUMEN', prefix: '1.3.3.02' },
            { code: '1.3.3.03', group: 'GEDUNG DAN BANGUNAN', label: 'BANGUNAN MENARA', prefix: '1.3.3.03' },
            { code: '1.3.3.04', group: 'GEDUNG DAN BANGUNAN', label: 'TUGU TITIK KONTROL/PASTI', prefix: '1.3.3.04' },

            // KIB D - JALAN, JARINGAN DAN IRIGASI (4 Sub Rincian Baku)
            { code: '1.3.4.01', group: 'JALAN, JARINGAN DAN IRIGASI', label: 'JALAN DAN JEMBATAN', prefix: '1.3.4.01' },
            { code: '1.3.4.02', group: 'JALAN, JARINGAN DAN IRIGASI', label: 'BANGUNAN AIR', prefix: '1.3.4.02' },
            { code: '1.3.4.03', group: 'JALAN, JARINGAN DAN IRIGASI', label: 'INSTALASI', prefix: '1.3.4.03' },
            { code: '1.3.4.04', group: 'JALAN, JARINGAN DAN IRIGASI', label: 'JARINGAN', prefix: '1.3.4.04' },

            // KIB E - ASET TETAP LAINNYA (7 Sub Rincian Baku)
            { code: '1.3.5.01', group: 'ASET TETAP LAINNYA', label: 'BAHAN PERPUSTAKAAN', prefix: '1.3.5.01' },
            { code: '1.3.5.02', group: 'ASET TETAP LAINNYA', label: 'BARANG BERCORAK KESENIAN/KEBUDAYAAN/OLAHRAGA', prefix: '1.3.5.02' },
            { code: '1.3.5.03', group: 'ASET TETAP LAINNYA', label: 'HEWAN', prefix: '1.3.5.03' },
            { code: '1.3.5.04', group: 'ASET TETAP LAINNYA', label: 'BIOTA PERAIRAN', prefix: '1.3.5.04' },
            { code: '1.3.5.05', group: 'ASET TETAP LAINNYA', label: 'TANAMAN', prefix: '1.3.5.05' },
            { code: '1.3.5.06', group: 'ASET TETAP LAINNYA', label: 'BARANG KOLEKSI NON BUDAYA', prefix: '1.3.5.06' },
            { code: '1.3.5.07', group: 'ASET TETAP LAINNYA', label: 'ASET TETAP DALAM RENOVASI', prefix: '1.3.5.07' },

            // KIB F - KONSTRUKSI DALAM PENGERJAAN
            { code: '1.3.6.01', group: 'KONSTRUKSI DALAM PENGERJAAN', label: 'KONSTRUKSI DALAM PENGERJAAN', prefix: '1.3.6' },

            // ASET LAINNYA
            { code: '1.4.01', group: 'ASET LAINNYA', subGroup: 'KEMITRAAN DENGAN PIHAK KETIGA', label: 'KEMITRAAN DENGAN PIHAK KETIGA', prefix: '1.4' },
            { code: '1.5.03', group: 'ASET LAINNYA', subGroup: 'ASET TIDAK BERWUJUD', label: 'ASET TIDAK BERWUJUD', prefix: '1.5.3' },
            { code: '1.5.04', group: 'ASET LAINNYA', subGroup: 'ASET LAIN-LAIN', label: 'ASET LAIN-LAIN', prefix: '1.5.4' }
        ];

        const rowVals = {};
        reklasDefs.forEach(d => {
            rowVals[d.code] = { saldo_awal: 0, tambah: 0, kurang: 0, akhir: 0 };
        });

        const reklasNotesItems = [];

        filteredAstaps.forEach(item => {
            const val = parseFloat(item.total_realisasi_num) || parseFloat(item.total_realisasi) || 0;
            const isExtracom = Boolean(item.is_extracomtable || resolveItemCategory(item) === 'EXTRACOM');

            let targetCode = '1.3.2.05'; // default ALAT KANTOR DAN RUMAH TANGGA
            const code = String(item.kode_barang || item.sub_rincian_kode || '').trim();
            const cat = String(item.category || '').toUpperCase();
            const nama = String(item.sub_rincian_nama || item.nama_barang || '').toUpperCase();

            const matchedDef = reklasDefs.find(d => code && code.startsWith(d.prefix));
            if (matchedDef) {
                targetCode = matchedDef.code;
            } else if (cat === 'KIB A') {
                targetCode = '1.3.1.01';
            } else if (cat === 'KIB B') {
                const found = reklasDefs.filter(d => d.group === 'PERALATAN DAN MESIN').find(d => nama.includes(d.label));
                targetCode = found ? found.code : '1.3.2.05';
            } else if (cat === 'KIB C') {
                targetCode = '1.3.3.01';
            } else if (cat === 'KIB D') {
                targetCode = '1.3.4.01';
            } else if (cat === 'KIB E') {
                targetCode = '1.3.5.01';
            } else if (cat === 'KIB F') {
                targetCode = '1.3.6.01';
            } else if (cat === 'ATB') {
                targetCode = '1.5.03';
            }

            if (rowVals[targetCode]) {
                rowVals[targetCode].saldo_awal += val;
                if (isExtracom) {
                    rowVals[targetCode].kurang += val;
                    reklasNotesItems.push({
                        item: item,
                        val: val,
                        targetCode: targetCode
                    });
                }
            }
        });

        // Hitung saldo akhir tiap sub-rincian
        reklasDefs.forEach(d => {
            const r = rowVals[d.code];
            r.akhir = r.saldo_awal + r.tambah - r.kurang;
        });

        // Agregat per Kelompok KIB
        const groupSums = {};
        reklasDefs.forEach(d => {
            if (!groupSums[d.group]) {
                groupSums[d.group] = { saldo_awal: 0, tambah: 0, kurang: 0, akhir: 0 };
            }
            groupSums[d.group].saldo_awal += rowVals[d.code].saldo_awal;
            groupSums[d.group].tambah += rowVals[d.code].tambah;
            groupSums[d.group].kurang += rowVals[d.code].kurang;
            groupSums[d.group].akhir += rowVals[d.code].akhir;
        });

        // Total ASET TETAP (KIB A s/d KIB F)
        const totalAsetTetap = { saldo_awal: 0, tambah: 0, kurang: 0, akhir: 0 };
        ['TANAH', 'PERALATAN DAN MESIN', 'GEDUNG DAN BANGUNAN', 'JALAN, JARINGAN DAN IRIGASI', 'ASET TETAP LAINNYA', 'KONSTRUKSI DALAM PENGERJAAN'].forEach(g => {
            if (groupSums[g]) {
                totalAsetTetap.saldo_awal += groupSums[g].saldo_awal;
                totalAsetTetap.tambah += groupSums[g].tambah;
                totalAsetTetap.kurang += groupSums[g].kurang;
                totalAsetTetap.akhir += groupSums[g].akhir;
            }
        });

        // Total ASET LAINNYA
        const totalAsetLainnya = groupSums['ASET LAINNYA'] || { saldo_awal: 0, tambah: 0, kurang: 0, akhir: 0 };

        // JUMLAH ASET
        const jumlahAset = {
            saldo_awal: totalAsetTetap.saldo_awal + totalAsetLainnya.saldo_awal,
            tambah: totalAsetTetap.tambah + totalAsetLainnya.tambah,
            kurang: totalAsetTetap.kurang + totalAsetLainnya.kurang,
            akhir: totalAsetTetap.akhir + totalAsetLainnya.akhir
        };

        const totalExtracomVal = jumlahAset.kurang;

        // Metadata Styling
        const s3Meta = {
            highlightRows: [],
            groupHeaderRows: [],
            subHeaderRows: [],
            dataRows: [],
            keteranganHeaderRow: -1,
            keteranganItemRows: [],
            signStartRow: -1,
            signBoldUnderlineRows: []
        };

        // Bangun Baris Excel Sheet 3
        let sheet3Rows = [
            ["REKLASIFIKASI ASET  TETAP PER JENIS", "", "", "", ""],
            [judulPeriode, "", "", "", ""],
            ["", "", "", "", ""],
            ["URAIAN", "RUMAH SAKIT UMUM DAERAH dr.H.KOESNADI", "", "", ""],
            ["", "SALDO AWAL", "MUTASI", "", "SALDO"],
            ["", "Berdasarkan Belanja\nModal", "Tambah", "Kurang", "Per " + signDate]
        ];

        const s3Merges = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 4 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 4 } },
            { s: { r: 3, c: 0 }, e: { r: 5, c: 0 } },
            { s: { r: 3, c: 1 }, e: { r: 3, c: 4 } },
            { s: { r: 4, c: 2 }, e: { r: 4, c: 3 } }
        ];

        // 1. Baris Induk ASET TETAP (Sesuai instruksi: ASET TETAP menggantikan AKTIVA TETAP)
        sheet3Rows.push([
            "ASET TETAP",
            totalAsetTetap.saldo_awal,
            totalAsetTetap.tambah,
            totalAsetTetap.kurang,
            totalAsetTetap.akhir
        ]);
        s3Meta.highlightRows.push(sheet3Rows.length - 1);

        // 2. Kelompok KIB A s/d KIB F beserta sub-rinciannya
        const kibGroups = [
            { name: 'TANAH', label: 'TANAH', items: reklasDefs.filter(d => d.group === 'TANAH') },
            { name: 'PERALATAN DAN MESIN', label: 'PERALATAN DAN MESIN', items: reklasDefs.filter(d => d.group === 'PERALATAN DAN MESIN') },
            { name: 'GEDUNG DAN BANGUNAN', label: 'GEDUNG DAN BANGUNAN', items: reklasDefs.filter(d => d.group === 'GEDUNG DAN BANGUNAN') },
            { name: 'JALAN, JARINGAN DAN IRIGASI', label: 'JALAN, JARINGAN DAN IRIGASI', items: reklasDefs.filter(d => d.group === 'JALAN, JARINGAN DAN IRIGASI') },
            { name: 'ASET TETAP LAINNYA', label: 'ASET TETAP LAINNYA', items: reklasDefs.filter(d => d.group === 'ASET TETAP LAINNYA') },
            { name: 'KONSTRUKSI DALAM PENGERJAAN', label: 'KONSTRUKSI DALAM PENGERJAAN', items: reklasDefs.filter(d => d.group === 'KONSTRUKSI DALAM PENGERJAAN') }
        ];

        kibGroups.forEach(kg => {
            const gSum = groupSums[kg.name] || { saldo_awal: 0, tambah: 0, kurang: 0, akhir: 0 };
            sheet3Rows.push([
                kg.label,
                gSum.saldo_awal,
                gSum.tambah,
                gSum.kurang,
                gSum.akhir
            ]);
            s3Meta.groupHeaderRows.push(sheet3Rows.length - 1);

            kg.items.forEach(it => {
                const vals = rowVals[it.code];
                sheet3Rows.push([
                    "  " + it.label,
                    vals.saldo_awal,
                    vals.tambah,
                    vals.kurang,
                    vals.akhir
                ]);
                s3Meta.dataRows.push(sheet3Rows.length - 1);
            });
        });

        // 3. Kelompok ASET LAINNYA
        sheet3Rows.push([
            "ASET LAINNYA",
            totalAsetLainnya.saldo_awal,
            totalAsetLainnya.tambah,
            totalAsetLainnya.kurang,
            totalAsetLainnya.akhir
        ]);
        s3Meta.highlightRows.push(sheet3Rows.length - 1);

        const asetLainnyaDefs = reklasDefs.filter(d => d.group === 'ASET LAINNYA');
        asetLainnyaDefs.forEach(it => {
            const vals = rowVals[it.code];
            sheet3Rows.push([
                it.label,
                vals.saldo_awal,
                vals.tambah,
                vals.kurang,
                vals.akhir
            ]);
            s3Meta.groupHeaderRows.push(sheet3Rows.length - 1);

            sheet3Rows.push([
                "  " + it.label,
                vals.saldo_awal,
                vals.tambah,
                vals.kurang,
                vals.akhir
            ]);
            s3Meta.dataRows.push(sheet3Rows.length - 1);
        });

        // 4. JUMLAH ASET
        sheet3Rows.push([
            "JUMLAH ASET",
            jumlahAset.saldo_awal,
            jumlahAset.tambah,
            jumlahAset.kurang,
            jumlahAset.akhir
        ]);
        s3Meta.highlightRows.push(sheet3Rows.length - 1);

        // 5. Bagian Koreksi Penyeimbang
        sheet3Rows.push([
            "Koreksi Atas Aset Tetap",
            0,
            totalExtracomVal,
            0,
            totalExtracomVal
        ]);
        s3Meta.subHeaderRows.push(sheet3Rows.length - 1);

        sheet3Rows.push(["  Hibah", 0, 0, 0, 0]);
        s3Meta.dataRows.push(sheet3Rows.length - 1);

        sheet3Rows.push(["  Dibawah Kapitalisasi (Extra Comptable)", 0, totalExtracomVal, 0, totalExtracomVal]);
        s3Meta.dataRows.push(sheet3Rows.length - 1);

        sheet3Rows.push(["  Koreksi Lain-Lain", 0, 0, 0, 0]);
        s3Meta.dataRows.push(sheet3Rows.length - 1);

        // 6. REALISASI BELANJA MODAL
        sheet3Rows.push([
            "REALISASI BELANJA MODAL",
            jumlahAset.saldo_awal,
            0,
            jumlahAset.kurang,
            jumlahAset.akhir
        ]);
        s3Meta.subHeaderRows.push(sheet3Rows.length - 1);

        // 7. Perencanaan/DED/SPV/AP
        sheet3Rows.push([
            "Perencanaan/DED/SPV/AP",
            "",
            "",
            "TRUE",
            ""
        ]);
        s3Meta.dataRows.push(sheet3Rows.length - 1);

        // 8. REALISASI BELANJA [TAHUN]
        sheet3Rows.push([
            "REALISASI BELANJA " + yearLabel,
            jumlahAset.saldo_awal,
            totalExtracomVal,
            totalExtracomVal,
            jumlahAset.saldo_awal
        ]);
        s3Meta.highlightRows.push(sheet3Rows.length - 1);

        // 9. KETERANGAN Narasi Reklasifikasi
        sheet3Rows.push(["", "", "", "", ""]);
        s3Meta.keteranganHeaderRow = sheet3Rows.length;
        sheet3Rows.push(["KETERANGAN :", "", "", "", ""]);

        if (reklasNotesItems.length > 0) {
            reklasNotesItems.forEach((n, idx) => {
                const it = n.item;
                const docType = (it.surat_pesanan_nomor && it.surat_pesanan_nomor !== '-') ? 'surat pesanan'
                    : ((it.kwitansi_nomor && it.kwitansi_nomor !== '-') ? 'Kwitansi'
                    : ((it.spk_nomor && it.spk_nomor !== '-') ? 'SPK'
                    : ((it.bast_dokumen_nomor && it.bast_dokumen_nomor !== '-') ? 'BAST' : 'bukti belanja')));
                const docNo = it.surat_pesanan_nomor || it.kwitansi_nomor || it.spk_nomor || it.bast_dokumen_nomor || '';
                const docDate = formatAstapDate(it.surat_pesanan_tanggal || it.kwitansi_tanggal || it.spk_tanggal || it.bast_dokumen_tanggal || '');
                const subRek = it.sub_rincian_kode || (it.kode_barang && it.kode_barang.length >= 14 ? it.kode_barang.substring(0, 14) : '1.3.2.05.02.006');
                const subNama = (it.sub_rincian_nama && it.sub_rincian_nama !== '-') ? it.sub_rincian_nama : 'ALAT RUMAH TANGGA LAINNYA (HOME USE)';
                const kdBrg = it.kode_barang || '1.3.2.05.02.06.111';
                const nmBrg = it.nama_barang || 'Barang Reklas';
                const vol = it.jumlah_volume || 1;
                const fmtNilaiText = formatRupiahReklas(n.val);

                // Susun kalimat bukti secara bersih tanpa duplikasi kata 'bukti' atau tanda '-' menggantung
                let buktiStr = "atas dasar bukti transaksi belanja";
                const hasRealDoc = docNo && docNo !== '-' && docNo !== '';
                const hasRealDate = docDate && docDate !== '-' && docDate !== '';

                if (hasRealDoc) {
                    const cleanDocLabel = (docType && docType !== 'bukti belanja') ? docType : 'dokumen';
                    buktiStr = `atas dasar bukti ${cleanDocLabel} ${docNo}`;
                    if (hasRealDate) {
                        buktiStr += ` tanggal ${docDate}`;
                    }
                } else if (hasRealDate) {
                    buktiStr = `atas dasar bukti transaksi belanja tanggal ${docDate}`;
                }

                const noteText = `${idx + 1}. Reklasifikasi dari rekening ${subRek} ${subNama} senilai Rp ${fmtNilaiText} ${buktiStr} pada RSUD dr.H.Koesnadi ke Extracompetable berupa ${kdBrg} ${nmBrg} (${vol}) karena sesuai dengan kode rekening Simda BMD 108`;

                const curRowIdx = sheet3Rows.length;
                sheet3Rows.push([noteText, "", "", "", n.val]);
                s3Merges.push({ s: { r: curRowIdx, c: 0 }, e: { r: curRowIdx, c: 3 } });
                s3Meta.keteranganItemRows.push(curRowIdx);
            });
        } else {
            const curRowIdx = sheet3Rows.length;
            sheet3Rows.push(["Tidak ada transaksi reklasifikasi aset pada periode ini.", "", "", "", ""]);
            s3Merges.push({ s: { r: curRowIdx, c: 0 }, e: { r: curRowIdx, c: 4 } });
            s3Meta.keteranganItemRows.push(curRowIdx);
        }

        // 10. Area Tanda Tangan Resmi
        sheet3Rows.push(["", "", "", "", ""]);
        sheet3Rows.push(["", "", "", "", ""]);

        const s3SignStartRow = sheet3Rows.length;
        s3Meta.signStartRow = s3SignStartRow;

        sheet3Rows.push(["MENGETAHUI,", "", "", "Bondowoso, " + signDate, ""]);
        sheet3Rows.push(["DIREKTUR", "", "", "PENGURUS BARANG ASET TETAP", ""]);
        sheet3Rows.push(["RSUD dr. H. KOESNADI BONDOWOSO", "", "", "RSUD dr.H.KOESNADI BONDOWOSO", ""]);
        sheet3Rows.push(["", "", "", "", ""]);
        sheet3Rows.push(["", "", "", "", ""]);

        const dirNama = ppkNama && ppkNama !== '-' ? ppkNama : 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR';
        const dirNip = ppkNip && ppkNip !== '-' ? ppkNip : '19771002 200604 1 007';
        const pengurusNama = 'BUDI HARTONO,S.Sos';
        const pengurusNip = '19760229 200801 1 010';

        const nameRowIdx = sheet3Rows.length;
        sheet3Rows.push([dirNama, "", "", pengurusNama, ""]);
        s3Meta.signBoldUnderlineRows = [nameRowIdx];

        sheet3Rows.push(["Pembina Tk.I-IV/b", "", "", "NIP. " + pengurusNip, ""]);
        sheet3Rows.push(["NIP. " + dirNip, "", "", "", ""]);

        for (let r = s3SignStartRow; r < sheet3Rows.length; r++) {
            s3Merges.push({ s: { r: r, c: 0 }, e: { r: r, c: 1 } });
            s3Merges.push({ s: { r: r, c: 3 }, e: { r: r, c: 4 } });
        }

        // Terapkan Tinggi Baris (Row Heights) Eksplisit agar teks keterangan tidak terpotong
        const s3RowHeights = [];
        for (let r = 0; r < sheet3Rows.length; r++) {
            if (r === 0 || r === 1) {
                s3RowHeights.push({ hpt: 22 });
            } else if (r === 2) {
                s3RowHeights.push({ hpt: 10 });
            } else if (r >= 3 && r <= 5) {
                s3RowHeights.push({ hpt: 21 });
            } else if (s3Meta.highlightRows && s3Meta.highlightRows.includes(r)) {
                s3RowHeights.push({ hpt: 20 });
            } else if (s3Meta.groupHeaderRows && s3Meta.groupHeaderRows.includes(r)) {
                s3RowHeights.push({ hpt: 19 });
            } else if (s3Meta.subHeaderRows && s3Meta.subHeaderRows.includes(r)) {
                s3RowHeights.push({ hpt: 19 });
            } else if (s3Meta.dataRows && s3Meta.dataRows.includes(r)) {
                s3RowHeights.push({ hpt: 18.5 });
            } else if (r === s3Meta.keteranganHeaderRow) {
                s3RowHeights.push({ hpt: 24 });
            } else if (s3Meta.keteranganItemRows && s3Meta.keteranganItemRows.includes(r)) {
                const rowData = sheet3Rows[r];
                const txt = (rowData && rowData[0]) ? String(rowData[0]) : '';
                const estLines = Math.max(2, Math.ceil(txt.length / 85));
                s3RowHeights.push({ hpt: Math.max(48, estLines * 22) });
            } else if (s3Meta.signStartRow && r >= s3Meta.signStartRow) {
                if (r === s3Meta.signStartRow + 3 || r === s3Meta.signStartRow + 4) {
                    s3RowHeights.push({ hpt: 26 });
                } else {
                    s3RowHeights.push({ hpt: 19 });
                }
            } else {
                s3RowHeights.push({ hpt: 16 });
            }
        }

        const wsSheet3 = XLSX.utils.aoa_to_sheet(sheet3Rows);
        wsSheet3['!cols'] = [
            { wch: 54 }, // URAIAN
            { wch: 20 }, // SALDO AWAL
            { wch: 18 }, // MUTASI Tambah
            { wch: 18 }, // MUTASI Kurang
            { wch: 22 }  // SALDO Per ...
        ];
        wsSheet3['!merges'] = s3Merges;
        wsSheet3['!rows'] = s3RowHeights;

        applySheet3ReklasRsdkStyling(wsSheet3, sheet3Rows.length, 5, s3Meta);

        if (filterSheet === 'all' || filterSheet === 'sheet3') {
            XLSX.utils.book_append_sheet(wb, wsSheet3, filterSheet === 'sheet3' ? "Reklas RSDK" : "3. Reklas RSDK");
        }

        // =========================================================================
        // SHEET 4: 4. RMB (excel) RSDK (REKAPITULASI MUTASI BARANG)
        // =========================================================================
        const kibReal = {
            'KIB A': 0, 'KIB B': 0, 'KIB C': 0, 'KIB D': 0,
            'KIB E': 0, 'KIB F': 0, 'ATB': 0, 'EXTRACOM': 0
        };
        const kibUnits = {
            'KIB A': 0, 'KIB B': 0, 'KIB C': 0, 'KIB D': 0,
            'KIB E': 0, 'KIB F': 0, 'ATB': 0, 'EXTRACOM': 0
        };

        filteredAstaps.forEach(item => {
            const cat = resolveItemCategory(item);
            const val = parseFloat(item.total_realisasi_num) || parseFloat(item.total_realisasi) || 0;
            const vol = parseInt(item.jumlah_volume) || 1;
            if (kibReal[cat] !== undefined) {
                kibReal[cat] += val;
                kibUnits[cat] += vol;
            } else {
                kibReal['KIB B'] += val;
                kibUnits['KIB B'] += vol;
            }
        });

        let sheet4Rows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            ["RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI"],
            ["REKAPITULASI MUTASI BARANG (RMB) ASET TETAP TAHUN ANGGARAN " + yearLabel],
            ["PERIODE: " + bannerTw],
            [""],
            [
                "NO",
                "KODE AKUN / KELOMPOK ASET",
                "SALDO AWAL", "",
                "MUTASI BERTAMBAH", "", "",
                "MUTASI BERKURANG", "",
                "SALDO AKHIR", "",
                "KETERANGAN"
            ],
            [
                "",
                "",
                "JUMLAH BARANG",
                "NILAI (Rp)",
                "BELANJA MODAL (Rp)",
                "REKLAS MASUK (Rp)",
                "TOTAL TAMBAH (Rp)",
                "PENGHAPUSAN (Rp)",
                "TOTAL KURANG (Rp)",
                "JUMLAH BARANG",
                "NILAI (Rp)",
                ""
            ]
        ];

        const rmbCategories = [
            { no: 1, kode: "1.3.1 - TANAH", name: "A. Tanah (KIB A)", cat: "KIB A" },
            { no: 2, kode: "1.3.2 - PERALATAN DAN MESIN", name: "B. Peralatan & Mesin (KIB B >= Rp 300.000)", cat: "KIB B" },
            { no: 3, kode: "1.3.3 - GEDUNG DAN BANGUNAN", name: "C. Gedung & Bangunan (KIB C)", cat: "KIB C" },
            { no: 4, kode: "1.3.4 - JALAN, IRIGASI DAN JARINGAN", name: "D. Jalan, Irigasi & Jaringan (KIB D)", cat: "KIB D" },
            { no: 5, kode: "1.3.5 - ASET TETAP LAINNYA", name: "E. Aset Tetap Lainnya (KIB E)", cat: "KIB E" },
            { no: 6, kode: "1.3.6 - KONSTRUKSI DALAM PENGERJAAN", name: "F. Konstruksi KDP (KIB F)", cat: "KIB F" },
            { no: 7, kode: "1.5.3 - ASET TIDAK BERWUJUD", name: "ATB. Aset Tidak Berwujud", cat: "ATB" },
            { no: 8, kode: "1.5.4 - EKSTRAKOMTABEL", name: "Extracom (< Rp 300.000)", cat: "EXTRACOM" }
        ];

        let grandBelanjaModal = 0;
        let grandTambah = 0;
        let grandKurang = 0;
        let grandAkhirNilai = 0;
        let grandAkhirUnit = 0;

        rmbCategories.forEach(item => {
            const belanja = kibReal[item.cat] || 0;
            const unit = kibUnits[item.cat] || 0;
            const reklasIn = 0;
            const totTambah = belanja + reklasIn;
            const totKurang = 0;
            const saldoAwalNilai = 0;
            const saldoAwalUnit = 0;
            const saldoAkhirNilai = saldoAwalNilai + totTambah - totKurang;
            const saldoAkhirUnit = saldoAwalUnit + unit;

            grandBelanjaModal += belanja;
            grandTambah += totTambah;
            grandKurang += totKurang;
            grandAkhirNilai += saldoAkhirNilai;
            grandAkhirUnit += saldoAkhirUnit;

            sheet4Rows.push([
                item.no,
                item.name,
                saldoAwalUnit,
                saldoAwalNilai,
                belanja,
                reklasIn,
                totTambah,
                totKurang,
                totKurang,
                saldoAkhirUnit,
                saldoAkhirNilai,
                item.kode
            ]);
        });

        const s4TotalRowIdx = sheet4Rows.length;
        sheet4Rows.push([
            "JUMLAH TOTAL REKAPITULASI MUTASI BARANG (RMB)", "",
            0, 0,
            grandBelanjaModal, 0, grandTambah,
            grandKurang, grandKurang,
            grandAkhirUnit, grandAkhirNilai,
            "Laporan Realisasi " + yearLabel
        ]);

        // Tanda Tangan Sheet 4 (Format Baku RSUD Koesnadi)
        const s4SignStartRow = sheet4Rows.length;
        const s4SignRows = buildKibSignatureRows(12, 8, ppkNama, ppkNip, signDate, 1);
        s4SignRows.forEach(r => sheet4Rows.push(r));

        const wsSheet4 = XLSX.utils.aoa_to_sheet(sheet4Rows);
        wsSheet4['!cols'] = [
            {wch: 6},   // NO
            {wch: 38},  // KELOMPOK ASET
            {wch: 16},  // SALDO AWAL JML
            {wch: 22},  // SALDO AWAL NILAI
            {wch: 22},  // BELANJA MODAL
            {wch: 20},  // REKLAS MASUK
            {wch: 22},  // TOTAL TAMBAH
            {wch: 20},  // PENGHAPUSAN
            {wch: 20},  // TOTAL KURANG
            {wch: 16},  // SALDO AKHIR JML
            {wch: 22},  // SALDO AKHIR NILAI
            {wch: 30}   // KET
        ];
        wsSheet4['!merges'] = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 11 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 11 } },
            { s: { r: 2, c: 0 }, e: { r: 2, c: 11 } },
            { s: { r: 3, c: 0 }, e: { r: 3, c: 11 } },
            { s: { r: 5, c: 0 }, e: { r: 6, c: 0 } },
            { s: { r: 5, c: 1 }, e: { r: 6, c: 1 } },
            { s: { r: 5, c: 2 }, e: { r: 5, c: 3 } },
            { s: { r: 5, c: 4 }, e: { r: 5, c: 6 } },
            { s: { r: 5, c: 7 }, e: { r: 5, c: 8 } },
            { s: { r: 5, c: 9 }, e: { r: 5, c: 10 } },
            { s: { r: 5, c: 11 }, e: { r: 6, c: 11 } },
            { s: { r: s4TotalRowIdx, c: 0 }, e: { r: s4TotalRowIdx, c: 1 } },

            // Tanda Tangan Sheet 4 (c1 s/d c4 kiri, c8 s/d c11 kanan)
            ...getKibSignatureMerges(s4SignStartRow, 12, 8, 1, 4, 11)
        ];
        applyCleanReportStyling(wsSheet4, sheet4Rows.length, 12, 5, 2, s4TotalRowIdx);
        applySignatureBlockStyling(wsSheet4, s4SignStartRow, 12);
        if (filterSheet === 'all' || filterSheet === 'sheet4') {
            XLSX.utils.book_append_sheet(wb, wsSheet4, filterSheet === 'sheet4' ? "RMB (excel) RSDK" : "4. RMB (excel) RSDK");
        }

        // Pastikan ada lembar sheet yang dimasukkan ke workbook
        if (!wb.SheetNames || wb.SheetNames.length === 0) {
            alert('⚠️ Tidak ada lembar sheet yang dipilih atau data tidak ditemukan.');
            isExportingRekapTriwulan = false;
            return;
        }

        // DOWNLOAD FILE EXCEL SESUAI PILIHAN SHEET REKAPITULASI
        let sheetSlug = 'PAKET_4_SHEET';
        if (filterSheet === 'sheet1') sheetSlug = 'DAFTAR_AT';
        else if (filterSheet === 'sheet2') sheetSlug = 'PENGURANGAN_AT';
        else if (filterSheet === 'sheet3') sheetSlug = 'REKLAS';
        else if (filterSheet === 'sheet4') sheetSlug = 'RMB_RSDK';

        const twSlug = filterTw === 'all' ? 'TAHUNAN' : filterTw.replace(/[\s_]/g, '');
        const fileName = "LAPORAN_REKAP_TRIWULAN_" + sheetSlug + "_RSDK_" + yearLabel + "_" + twSlug + ".xlsx";
        XLSX.writeFile(wb, fileName);
        setTimeout(() => { isExportingRekapTriwulan = false; }, 1500);
    }
    </script>

    <script>
        window.__simatAstaps = @json(!empty($astaps) ? $astaps : []);
        window.__simatDeletedAstaps = @json(!empty($deletedAstaps) ? $deletedAstaps : []);

        function astapCatalog() {
            return {
                astaps: window.__simatAstaps || [],
                
                // State Modal Export Excel Berita Acara / Laporan
                showExportModal: false,
                exportFormatType: 'sipenerbang', // 'sipenerbang' | 'rekap_triwulan'
                exportYear: '2026',
                exportTriwulan: 'all',
                exportCategory: 'all',
                exportRekapSheet: 'all', // 'all' | 'sheet1' | 'sheet2' | 'sheet3' | 'sheet4'
                isSubmittingExport: false,

                // State Modal Rapikan / Urutkan Ulang NIBAR (Auto-Resequence)
                showResequenceModal: false,
                resequenceYear: 'all',
                resequenceCategory: 'all',
                isSubmittingResequence: false,

                openResequenceModal() {
                    this.resequenceYear = this.tahunFilter !== 'all' ? this.tahunFilter : 'all';
                    this.resequenceCategory = this.categoryFilter !== 'all' ? this.categoryFilter : 'all';
                    this.showResequenceModal = true;
                },

                async submitResequence() {
                    this.isSubmittingResequence = true;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    try {
                        const res = await fetch('{{ route('astap.resequence_nibar') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                tahun: this.resequenceYear,
                                category: this.resequenceCategory
                            })
                        });
                        const data = await res.json();
                        if (data.success) {
                            this.showResequenceModal = false;
                            this.showToast(data.message, 'success');
                            setTimeout(() => { window.location.reload(); }, 1200);
                        } else {
                            this.showToast('⚠️ Gagal: ' + (data.message || 'Terjadi kesalahan'), 'error');
                        }
                    } catch (err) {
                        this.showToast('⚠️ Terjadi kendala saat merapikan NIBAR: ' + err.message, 'error');
                    } finally {
                        this.isSubmittingResequence = false;
                    }
                },

                resequenceSingleAstap(astap) {
                    if (!astap || !astap.id) return;
                    this.askConfirmation({
                        title: '🔄 Konfirmasi Rapikan NIBAR Barang Ini',
                        message: 'Sistem akan merapatkan nomor urut register (NIBAR) yang kosong khusus untuk aset yang BELUM DITEMPATKAN (di gudang).\n\n🔒 Aset yang SUDAH DITEMPATKAN di unit/ruangan TIDAK AKAN BERUBAH agar label stiker QR fisik di ruangan tidak tertukar.\n\n📦 Aset gudang setelahnya akan dimajukan untuk mengisi nomor yang kosong. Jika tidak ada aset gudang setelahnya, celah nomor dibiarkan dulu menunggu ada inputan baru dengan jenis & tahun yang sama atau sampai aset ruangan dikembalikan ke gudang.',
                        itemName: (astap.nama_barang || 'Barang') + ' (Tahun ' + (astap.tahun_perolehan || '2026') + ')',
                        type: 'warning',
                        btnText: '⚡ Ya, Rapikan NIBAR',
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                const res = await fetch('{{ route('astap.resequence_nibar') }}', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': token,
                                        'Accept': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        astap_id: astap.id
                                    })
                                });
                                const data = await res.json();
                                if (data.success) {
                                    this.showDetailModal = false;
                                    this.showToast(data.message, 'success');
                                    setTimeout(() => { window.location.reload(); }, 1200);
                                } else {
                                    this.showToast('⚠️ Gagal: ' + (data.message || 'Terjadi kesalahan'), 'error');
                                }
                            } catch (err) {
                                this.showToast('⚠️ Terjadi kendala saat merapikan NIBAR: ' + err.message, 'error');
                            }
                        }
                    });
                },

                openExportModal() {
                    this.exportFormatType = 'sipenerbang';
                    this.exportRekapSheet = 'all';
                    this.exportYear = this.tahunFilter !== 'all' ? this.tahunFilter : (this.availableYears.length > 0 ? this.availableYears[0] : '2026');
                    this.exportTriwulan = this.triwulanFilter !== 'all' ? this.triwulanFilter : 'all';
                    this.exportCategory = this.categoryFilter !== 'all' ? this.categoryFilter : 'all';
                    this.showExportModal = true;
                },

                get availableYears() {
                    const yearsSet = new Set();
                    (this.astaps || []).forEach(item => {
                        if (item.tahun_perolehan) {
                            const yr = parseInt(item.tahun_perolehan);
                            if (!isNaN(yr)) yearsSet.add(yr);
                        }
                    });
                    // Pastikan tahun sekarang selalu ada jika data masih kosong
                    yearsSet.add(new Date().getFullYear());
                    return Array.from(yearsSet).sort((a, b) => b - a);
                },

                get exportFilteredCount() {
                    const fYear = this.exportYear;
                    const fTw = this.exportTriwulan;
                    const fCat = this.exportCategory;
                    const isRekap = this.exportFormatType === 'rekap_triwulan';

                    if (isRekap && this.exportRekapSheet === 'sheet2') {
                        const deletedItems = window.__simatDeletedAstaps || [];
                        return deletedItems.filter(item => {
                            const matchYear = fYear === 'all' || String(item.deleted_year) === String(fYear) || String(item.tahun_perolehan) === String(fYear);
                            let matchTw = true;
                            if (fTw !== 'all') {
                                const targetKey = fTw.replace(/[\s_]/g, '').toUpperCase();
                                const itemTw = (item.deleted_tw || item.triwulan || 'TWI').replace(/[\s_]/g, '').toUpperCase();
                                matchTw = (itemTw === targetKey) ||
                                          (targetKey === 'TWI' && itemTw === 'TW1') || (targetKey === 'TW1' && itemTw === 'TWI') ||
                                          (targetKey === 'TWII' && itemTw === 'TW2') || (targetKey === 'TW2' && itemTw === 'TWII') ||
                                          (targetKey === 'TWIII' && itemTw === 'TW3') || (targetKey === 'TW3' && itemTw === 'TWIII') ||
                                          (targetKey === 'TWIV' && itemTw === 'TW4') || (targetKey === 'TW4' && itemTw === 'TWIV');
                            }
                            return matchYear && matchTw;
                        }).length;
                    }

                    return (this.astaps || []).filter(item => {
                        const matchYear = fYear === 'all' || String(item.tahun_perolehan) === String(fYear);
                        let matchTw = true;
                        if (fTw !== 'all') {
                            const targetKey = fTw.replace(/[\s_]/g, '').toUpperCase();
                            const itemTw = (item.triwulan || 'TWI').replace(/[\s_]/g, '').toUpperCase();
                            matchTw = (itemTw === targetKey) ||
                                      (targetKey === 'TWI' && itemTw === 'TW1') || (targetKey === 'TW1' && itemTw === 'TWI') ||
                                      (targetKey === 'TWII' && itemTw === 'TW2') || (targetKey === 'TW2' && itemTw === 'TWII') ||
                                      (targetKey === 'TWIII' && itemTw === 'TW3') || (targetKey === 'TW3' && itemTw === 'TWIII') ||
                                      (targetKey === 'TWIV' && itemTw === 'TW4') || (targetKey === 'TW4' && itemTw === 'TWIV');
                        }
                        if (isRekap) {
                            return matchYear && matchTw;
                        }
                        const itemCat = typeof resolveItemCategory === 'function' ? resolveItemCategory(item) : item.category;
                        const matchCat = (fCat === 'all' || fCat === 'REKAP') || (itemCat === fCat);
                        return matchYear && matchTw && matchCat;
                    }).length;
                },

                submitExport() {
                    this.isSubmittingExport = true;
                    if (this.exportFormatType === 'rekap_triwulan') {
                        exportRekapTriwulanToExcel({
                            year: this.exportYear,
                            triwulan: this.exportTriwulan,
                            sheet: this.exportRekapSheet
                        });
                        setTimeout(() => {
                            this.isSubmittingExport = false;
                            this.showExportModal = false;
                            let sheetName = 'Paket Lengkap 4 Sheet';
                            if (this.exportRekapSheet === 'sheet1') sheetName = 'Daftar AT ' + (this.exportTriwulan === 'all' ? 'Tahunan' : this.exportTriwulan);
                            else if (this.exportRekapSheet === 'sheet2') sheetName = 'Daftar Pengurangan AT RSDK';
                            else if (this.exportRekapSheet === 'sheet3') sheetName = 'Reklas RSDK';
                            else if (this.exportRekapSheet === 'sheet4') sheetName = 'RMB (excel) RSDK';
                            this.showToast('Berhasil mengekspor Laporan ' + sheetName + ' ' + (this.exportTriwulan === 'all' ? 'Tahunan' : this.exportTriwulan) + ' ' + this.exportYear + '!', 'success');
                        }, 800);
                    } else {
                        exportAstapToExcel({
                            year: this.exportYear,
                            triwulan: this.exportTriwulan,
                            category: this.exportCategory
                        });
                        setTimeout(() => {
                            this.isSubmittingExport = false;
                            this.showExportModal = false;
                            const catLabel = this.exportCategory === 'all'
                                ? 'Lengkap (9 Sheet)'
                                : (this.exportCategory === 'REKAP' ? 'Rekapitulasi Realisasi' : this.exportCategory);
                            this.showToast('Berhasil mengekspor Laporan ASTAP ' + catLabel + ' ' + (this.exportTriwulan === 'all' ? 'Tahunan' : this.exportTriwulan) + ' ' + this.exportYear + '!', 'success');
                        }, 800);
                    }
                },

                downloadExcel() {
                    this.openExportModal();
                },
                searchQuery: '',
                categoryFilter: 'all',
                kondisiFilter: 'all',
                asalUsulFilter: 'all',
                tahunFilter: 'all',
                triwulanFilter: 'all',
                viewMode: 'catalog',
                showAddModal: false,
                showEditModal: false,
                showDetailModal: false,
                showQrModal: false,
                selectedAstap: null,
                selectedAstapDetail: null,
                selectedQrItem: null,
                qrDataUrl: '',
                isGeneratingQr: false,
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
                    itemDetails: null,
                    type: 'danger',
                    btnText: 'Ya, Lanjutkan',
                    assetWarning: null,
                    isBlocked: false,
                    actionUrl: null,
                    actionText: null,
                    onConfirm: null
                },

                askConfirmation({ title, message, itemName, itemDetails = null, type = 'danger', btnText, assetWarning = null, isBlocked = false, actionUrl = null, actionText = null, onConfirm }) {
                    this.confirmData = {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        itemDetails: itemDetails,
                        type: type,
                        btnText: isBlocked ? null : (btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan'))),
                        isBlocked: Boolean(isBlocked),
                        actionUrl: actionUrl,
                        actionText: actionText,
                        assetWarning: assetWarning,
                        onConfirm: onConfirm
                    };
                    this.showConfirmModal = true;
                },

                isRegisterPlacedInUnit(reg) {
                    if (!reg) return false;
                    if (reg.unit_id) return true;
                    if (!reg.ruang_pemegang) return false;
                    const clean = String(reg.ruang_pemegang).trim().toLowerCase();
                    const unplacedPlaceholders = [
                        '', '-', 'belum ditempatkan', 'belum ditempatkan / di gudang',
                        'belum ditempatkan / di gudang aset', 'gudang aset',
                        'gudang aset utama / belum ditempatkan', 'gudang perbekalan'
                    ];
                    return !unplacedPlaceholders.includes(clean);
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
                    if (!dateStr || dateStr === '-') return '-';
                    if (String(dateStr).length === 4) return '01 Jan ' + dateStr;
                    try {
                        let str = String(dateStr).trim();
                        // Jika berformat DD/MM/YYYY (misal: 12/05/2026), ubah ke YYYY-MM-DD agar JS tidak salah membaca bulan/hari (US format)
                        if (str.includes('/')) {
                            const parts = str.split('/');
                            if (parts.length === 3) {
                                const day = parts[0].padStart(2, '0');
                                const month = parts[1].padStart(2, '0');
                                const year = parts[2];
                                str = `${year}-${month}-${day}`;
                            }
                        }
                        const d = new Date(str);
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
                                    if (this.selectedAstapDetail && this.selectedAstapDetail.registers) {
                                        this.selectedAstapDetail.registers = this.selectedAstapDetail.registers.map(r => 
                                            r.id === reg.id ? { ...r, kondisi: this.newKondisiValue } : { ...r }
                                        );
                                        if (data.spesifikasi_json) {
                                            this.selectedAstapDetail.spesifikasi_json = data.spesifikasi_json;
                                        }
                                        if (data.stats) {
                                            if (!this.selectedAstapDetail.spesifikasi_json) this.selectedAstapDetail.spesifikasi_json = {};
                                            this.selectedAstapDetail.spesifikasi_json.kondisi_stats = data.stats;
                                            this.selectedAstapDetail.spesifikasi_json.kondisi = data.stats.kondisi_dominan;
                                            this.selectedAstapDetail.kondisi_barang = data.stats.kondisi_dominan;
                                        }
                                        this.selectedAstapDetail = { ...this.selectedAstapDetail };

                                        this.astaps = this.astaps.map(a => {
                                            if (a.id === this.selectedAstapDetail.id) {
                                                const updatedRegs = (a.registers || []).map(r => 
                                                    r.id === reg.id ? { ...r, kondisi: this.newKondisiValue } : { ...r }
                                                );
                                                let spec = a.spesifikasi_json || {};
                                                if (typeof spec === 'string') {
                                                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                                                }
                                                if (data.stats) {
                                                    spec.kondisi_stats = data.stats;
                                                    spec.kondisi = data.stats.kondisi_dominan;
                                                }
                                                return {
                                                    ...a,
                                                    kondisi_barang: data.stats ? data.stats.kondisi_dominan : a.kondisi_barang,
                                                    spesifikasi_json: spec,
                                                    registers: updatedRegs
                                                };
                                            }
                                            return a;
                                        });
                                    }
                                    this.showEditKondisiModal = false;
                                    this.showToast('Kondisi unit berhasil diperbarui menjadi ' + this.newKondisiValue + '!', 'success');
                                } else {
                                    this.showToast('⚠️ Gagal memperbarui: ' + (data.message || 'Terjadi kesalahan'), 'error');
                                }
                            } catch(err) {
                                reg.kondisi = this.newKondisiValue;
                                if (this.selectedAstapDetail && this.selectedAstapDetail.registers) {
                                    this.selectedAstapDetail.registers = this.selectedAstapDetail.registers.map(r => 
                                        r.id === reg.id ? { ...r, kondisi: this.newKondisiValue } : { ...r }
                                    );
                                    this.selectedAstapDetail = { ...this.selectedAstapDetail };
                                }
                                this.showEditKondisiModal = false;
                                this.showToast('Kondisi unit berhasil diperbarui!', 'success');
                            } finally {
                                this.isSavingKondisi = false;
                            }
                        }
                    });
                },

                // Modal Cek Riwayat Mutasi State
                showRiwayatModal: false,
                selectedRiwayatRegister: null,
                selectedRiwayatMutasis: [],
                isLoadingRiwayat: false,

                async openRiwayatModal(reg) {
                    if (!reg) return;
                    this.selectedRiwayatRegister = reg;
                    this.selectedRiwayatMutasis = Array.isArray(reg.mutasis) ? reg.mutasis : [];
                    this.showRiwayatModal = true;

                    // Fetch data terbaru dari backend
                    try {
                        this.isLoadingRiwayat = true;
                        const res = await fetch(`/astap/register-mutasi/${reg.id}`);
                        if (res.ok) {
                            const data = await res.json();
                            if (data.success && Array.isArray(data.mutasis)) {
                                this.selectedRiwayatMutasis = data.mutasis;
                                reg.mutasis = data.mutasis;
                                if (data.kondisi) reg.kondisi = data.kondisi;
                                if (data.ruang) reg.ruang_pemegang = data.ruang;
                            }
                        }
                    } catch (err) {
                        console.error('Gagal memuat riwayat mutasi:', err);
                    } finally {
                        this.isLoadingRiwayat = false;
                    }
                },

                deleteRegister(reg) {
                    if (!reg) return;

                    // 1. Validasi Penempatan: Cek apakah unit register SUDAH DITEMPATKAN di unit / paviliun
                    if (this.isRegisterPlacedInUnit(reg)) {
                        const roomName = String(reg.ruang_pemegang || 'Unit / Paviliun RSUD').trim();
                        const parentName = this.selectedAstapDetail?.nama_barang || 'Aset';
                        const nibarStr = reg.nibar || reg.no_register || 'NIBAR';

                        this.askConfirmation({
                            title: 'Unit Tidak Dapat Dihapus',
                            message: `Unit register NIBAR "${nibarStr}" saat ini belum dapat dihapus karena masih aktif ditempatkan di ruangan "${roomName}" di database RSUD.`,
                            itemName: `${parentName} (NIBAR: ${nibarStr})`,
                            itemDetails: {
                                nama: parentName,
                                kode: nibarStr,
                                badgeText: '1 ASET AKTIF',
                                totalAset: 1,
                                nilaiFmt: this.selectedAstapDetail?.total_realisasi || 'Rp 0'
                            },
                            type: 'danger',
                            isBlocked: true,
                            actionUrl: '/mutasi-aset',
                            actionText: 'Ajukan Mutasi Aset',
                            assetWarning: `Sistem mendeteksi bahwa ruangan "${roomName}" saat ini masih memegang aset aktif dengan NIBAR ${nibarStr}. Demi akuntabilitas dan pencegahan kehilangan aset RSUD Koesnadi, seluruh aset harus dipindahkan (mutasi) ke ruangan lain terlebih dahulu sampai ruangan ini kosong atau dikembalikan ke gudang perbekalan.`,
                            btnText: null,
                            onConfirm: null
                        });
                        return;
                    }

                    // 2. Jika belum ditempatkan (di gudang): Izinkan hapus ke Tong Sampah
                    this.askConfirmation({
                        title: 'Konfirmasi Hapus Register Unit NIBAR',
                        message: 'Apakah Anda yakin ingin memindahkan unit register NIBAR ini ke Recycle Bin (Tong Sampah)? Data dapat dipulihkan kembali jika diperlukan.',
                        itemName: 'NIBAR: ' + (reg.nibar || reg.no_register),
                        itemDetails: {
                            nama: this.selectedAstapDetail?.nama_barang || 'Aset',
                            kode: reg.nibar || reg.no_register,
                            badgeText: 'Belum Ditempatkan',
                            totalAset: 0,
                            nilaiFmt: 'Gudang Aset'
                        },
                        type: 'danger',
                        isBlocked: false,
                        btnText: 'Pindahkan ke Sampah',
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                const res = await fetch('/astap-register/' + reg.id, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                                });
                                const data = await res.json();
                                if (data.success) {
                                    if (this.selectedAstapDetail && this.selectedAstapDetail.registers) {
                                        this.selectedAstapDetail.registers = this.selectedAstapDetail.registers.filter(r => r.id !== reg.id);
                                        this.selectedAstapDetail.jumlah_volume = this.selectedAstapDetail.registers.length;
                                        this.selectedAstapDetail.volume_satuan = this.selectedAstapDetail.jumlah_volume + ' Aset';
                                        if (data.spesifikasi_json) {
                                            this.selectedAstapDetail.spesifikasi_json = data.spesifikasi_json;
                                        }
                                        if (data.stats) {
                                            if (!this.selectedAstapDetail.spesifikasi_json) this.selectedAstapDetail.spesifikasi_json = {};
                                            this.selectedAstapDetail.spesifikasi_json.kondisi_stats = data.stats;
                                            this.selectedAstapDetail.spesifikasi_json.kondisi = data.stats.kondisi_dominan;
                                            this.selectedAstapDetail.kondisi_barang = data.stats.kondisi_dominan;
                                        }
                                        this.selectedAstapDetail = { ...this.selectedAstapDetail };

                                        // Jika master ASTAP otomatis dihapus (NIBAR terakhir habis)
                                        // → hapus baris dari list tabel & tutup modal detail
                                        if (data.astap_auto_deleted && data.astap_id) {
                                            this.astaps = this.astaps.filter(a => a.id !== data.astap_id);
                                            this.showDetailModal = false;
                                            this.selectedAstapDetail = null;
                                            this.showToast(data.message || 'Paket ASTAP otomatis dipindahkan ke Recycle Bin karena semua unit NIBAR telah dihapus.', 'success');
                                        } else {
                                            this.astaps = this.astaps.map(a => {
                                                if (a.id === this.selectedAstapDetail.id) {
                                                    const updatedRegs = (a.registers || []).filter(r => r.id !== reg.id);
                                                    let spec = (data && data.spesifikasi_json) ? data.spesifikasi_json : (a.spesifikasi_json || {});
                                                    if (typeof spec === 'string') {
                                                        try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                                                    }
                                                    if (data.stats) {
                                                        spec.kondisi_stats = data.stats;
                                                        spec.kondisi = data.stats.kondisi_dominan;
                                                    }
                                                    return {
                                                        ...a,
                                                        jumlah_volume: updatedRegs.length,
                                                        volume_satuan: updatedRegs.length + ' Aset',
                                                        kondisi_barang: data.stats ? data.stats.kondisi_dominan : a.kondisi_barang,
                                                        spesifikasi_json: spec,
                                                        registers: updatedRegs
                                                    };
                                                }
                                                return a;
                                            });
                                            this.showToast(data.message || 'Unit register NIBAR berhasil dipindahkan ke Recycle Bin!', 'success');
                                        }
                                    }
                                } else if (data.is_blocked) {
                                    this.askConfirmation({
                                        title: 'Unit Tidak Dapat Dihapus',
                                        message: data.message,
                                        itemName: 'NIBAR: ' + (reg.nibar || reg.no_register),
                                        itemDetails: {
                                            nama: this.selectedAstapDetail?.nama_barang || 'Aset',
                                            kode: reg.nibar || reg.no_register,
                                            badgeText: '1 ASET AKTIF',
                                            totalAset: 1
                                        },
                                        type: 'danger',
                                        isBlocked: true,
                                        actionUrl: data.action_url || '/mutasi-aset',
                                        actionText: 'Ajukan Mutasi Aset',
                                        assetWarning: data.message,
                                        btnText: null
                                    });
                                } else {
                                    this.showToast(data.message || '⚠️ Gagal menghapus unit NIBAR.', 'error');
                                }
                            } catch(err) {
                                this.showToast('Terjadi kesalahan jaringan saat menghapus unit NIBAR.', 'error');
                            }
                        }
                    });
                },

                deleteAstap(item) {
                    if (!item) return;

                    // 1. Validasi Penempatan: Cek apakah ada unit register yang SUDAH DITEMPATKAN di unit & paviliun
                    const regs = Array.isArray(item.registers) ? item.registers : [];
                    const placedRegs = regs.filter(r => this.isRegisterPlacedInUnit(r));

                    if (placedRegs.length > 0) {
                        const roomNames = Array.from(new Set(placedRegs.map(r => String(r.ruang_pemegang).trim()).filter(Boolean)));
                        const roomSummary = roomNames.slice(0, 3).join(', ') + (roomNames.length > 3 ? ` dan ${roomNames.length - 3} ruangan lainnya` : '');
                        const totalPlaced = placedRegs.length;
                        const nilaiFmt = item.total_realisasi || this.formatRupiah(item.total_realisasi_num || 0);

                        this.askConfirmation({
                            title: 'Unit Tidak Dapat Dihapus',
                            message: `Aset "${item.nama_barang || 'Aset'}" saat ini belum dapat dihapus karena masih menampung ${totalPlaced} barang inventaris/aset (${nilaiFmt}) di database RSUD.`,
                            itemName: `${item.nama_barang || 'Aset'} (Kode: ${item.kode_barang || '-'})`,
                            itemDetails: {
                                nama: item.nama_barang,
                                kode: item.kode_barang,
                                badgeText: `${totalPlaced} ASET AKTIF`,
                                totalAset: totalPlaced,
                                nilaiFmt: nilaiFmt
                            },
                            type: 'danger',
                            isBlocked: true,
                            actionUrl: '/mutasi-aset',
                            actionText: 'Ajukan Mutasi Aset',
                            assetWarning: `Sistem mendeteksi bahwa ruangan "${roomSummary || 'Unit/Paviliun'}" saat ini masih memegang ${totalPlaced} aset aktif bernilai ${nilaiFmt}. Demi akuntabilitas dan pencegahan kehilangan aset RSUD Koesnadi, seluruh aset harus dipindahkan (mutasi) ke ruangan lain terlebih dahulu sampai ruangan ini kosong.`,
                            btnText: null,
                            onConfirm: null
                        });
                        return;
                    }

                    // 2. Jika belum ditempatkan (semua di gudang): Buka konfirmasi hapus biasa ke Tong Sampah
                    this.askConfirmation({
                        title: 'Konfirmasi Hapus Master ASTAP',
                        message: 'Apakah Anda yakin ingin memindahkan data aset tetap ini ke Recycle Bin (Tong Sampah)? Seluruh unit register NIBAR terkait juga akan dipindahkan ke Recycle Bin.',
                        itemName: (item.nama_barang || 'ASTAP') + ' (' + (item.kode_barang || '-') + ')',
                        itemDetails: {
                            nama: item.nama_barang,
                            kode: item.kode_barang,
                            badgeText: `${(item.registers || []).length} Unit di Gudang`,
                            totalAset: 0,
                            nilaiFmt: item.total_realisasi || 'Rp 0'
                        },
                        type: 'danger',
                        isBlocked: false,
                        btnText: 'Pindahkan ke Tong Sampah',
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                const res = await fetch('/astap/' + item.id, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                                });
                                const data = await res.json();
                                if (data.success) {
                                    window.location.reload();
                                } else if (data.is_blocked) {
                                    this.askConfirmation({
                                        title: 'Unit Tidak Dapat Dihapus',
                                        message: data.message,
                                        itemName: (item.nama_barang || 'ASTAP') + ' (' + (item.kode_barang || '-') + ')',
                                        itemDetails: {
                                            nama: item.nama_barang,
                                            kode: item.kode_barang,
                                            badgeText: 'ASET AKTIF'
                                        },
                                        type: 'danger',
                                        isBlocked: true,
                                        actionUrl: data.action_url || '/mutasi-aset',
                                        actionText: 'Ajukan Mutasi Aset',
                                        assetWarning: data.message,
                                        btnText: null
                                    });
                                } else {
                                    this.showToast(data.message || '⚠️ Gagal menghapus data ASTAP.', 'error');
                                }
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
                    if (this.selectedAstapDetail && typeof this.selectedAstapDetail.spesifikasi_json === 'string') {
                        try {
                            this.selectedAstapDetail.spesifikasi_json = JSON.parse(this.selectedAstapDetail.spesifikasi_json);
                        } catch(e) {}
                    }
                    if (this.selectedAstapDetail && Array.isArray(this.selectedAstapDetail.registers)) {
                        this.selectedAstapDetail.registers.sort((a, b) => {
                            const numA = a.no_register_int || parseInt((a.nibar || a.no_register || '').slice(-7)) || 0;
                            const numB = b.no_register_int || parseInt((b.nibar || b.no_register || '').slice(-7)) || 0;
                            if (numA !== numB) return numA - numB;
                            return (a.nibar || a.no_register || '').localeCompare(b.nibar || b.no_register || '');
                        });
                    }
                    this.detailKondisiFilter = 'all';
                    this.detailPenempatanFilter = 'all';
                    this.detailSearchQuery = '';
                    this.showDetailModal = true;
                },

                syncRepeaterItemsWithVolume(items, targetTotal, qtyKeys = []) {
                    if (!Array.isArray(items) || items.length === 0) return [];
                    if (targetTotal <= 0) return [];

                    let remainingQuota = targetTotal;
                    let result = [];

                    for (let i = 0; i < items.length; i++) {
                        if (remainingQuota <= 0) break;
                        let item = JSON.parse(JSON.stringify(items[i]));

                        let activeQtyKey = null;
                        let curQty = 1;
                        for (let k of qtyKeys) {
                            if (item[k] !== undefined && item[k] !== null && item[k] !== '') {
                                activeQtyKey = k;
                                curQty = parseFloat(item[k]) || 1;
                                break;
                            }
                        }

                        if (curQty <= remainingQuota) {
                            if (activeQtyKey) item[activeQtyKey] = curQty;
                            result.push(item);
                            remainingQuota -= curQty;
                        } else {
                            if (activeQtyKey) item[activeQtyKey] = remainingQuota;
                            result.push(item);
                            remainingQuota = 0;
                            break;
                        }
                    }

                    return result;
                },

                getTanahItemsForDetail(astap) {
                    if (!astap) return [];
                    let spec = astap.spesifikasi_json;
                    if (typeof spec === 'string') {
                        try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                    }
                    const targetTotal = (astap.registers && astap.registers.length > 0) ? astap.registers.length : (parseInt(astap.jumlah_volume) || 1);
                    if (spec && Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) {
                        return this.syncRepeaterItemsWithVolume(spec.tanah_items, targetTotal, ['tanah_jumlah_bidang']);
                    }
                    return [];
                },

                getMesinItemsForDetail(astap) {
                    if (!astap) return [];
                    let spec = astap.spesifikasi_json;
                    if (typeof spec === 'string') {
                        try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                    }
                    const targetTotal = (astap.registers && astap.registers.length > 0) ? astap.registers.length : (parseInt(astap.jumlah_volume) || 1);
                    if (spec && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                        return this.syncRepeaterItemsWithVolume(spec.mesin_items, targetTotal, ['mesin_jumlah_barang']);
                    }
                    return [];
                },

                getGedungItemsForDetail(astap) {
                    if (!astap) return [];
                    let spec = astap.spesifikasi_json;
                    if (typeof spec === 'string') {
                        try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                    }
                    const targetTotal = (astap.registers && astap.registers.length > 0) ? astap.registers.length : (parseInt(astap.jumlah_volume) || 1);
                    if (spec && Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) {
                        return this.syncRepeaterItemsWithVolume(spec.gedung_items, targetTotal, ['gedung_jumlah_bangunan']);
                    }
                    return [];
                },

                getJaringanItemsForDetail(astap) {
                    if (!astap) return [];
                    let spec = astap.spesifikasi_json;
                    if (typeof spec === 'string') {
                        try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                    }
                    const targetTotal = (astap.registers && astap.registers.length > 0) ? astap.registers.length : (parseInt(astap.jumlah_volume) || 1);
                    if (spec && Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) {
                        return this.syncRepeaterItemsWithVolume(spec.jaringan_items, targetTotal, ['jaringan_jumlah', 'jaringan_jumlah_barang']);
                    }
                    // Fallback jika single item / legacy
                    return [{
                        jaringan_nama_barang: (spec && spec.jaringan_nama_barang) || astap.nama_barang || 'Jalan, Irigasi dan Jaringan',
                        jaringan_kode_barang: (spec && spec.jaringan_kode_barang) || astap.kode_barang || '',
                        jaringan_konstruksi: (spec && spec.konstruksi) || (spec && spec.jaringan_konstruksi) || '',
                        jaringan_panjang_m: (spec && spec.panjang_m) || 0,
                        jaringan_lebar_m: (spec && spec.lebar_m) || 0,
                        jaringan_luas_m2: (spec && spec.luas_m2) || astap.luas_m2 || 0,
                        jaringan_kondisi: astap.kondisi || (spec && spec.jaringan_kondisi) || 'B',
                        jaringan_bertingkat: (spec && (spec.jaringan_bertingkat || spec.bertingkat)) || 'Bertingkat',
                        jaringan_beton: (spec && (spec.jaringan_beton || spec.beton)) || 'Beton',
                        jaringan_status_tanah: (spec && (spec.jaringan_status_tanah || spec.status_tanah)) || 'Tanah Hak Pakai RSUD',
                        jaringan_kode_aset_tanah: (spec && (spec.jaringan_kode_aset_tanah || spec.kode_aset_tanah)) || '1.3.1.01.01.02.013',
                        jaringan_is_baru: (spec && (spec.jaringan_is_baru || spec.is_baru)) || 'Baru',
                        jaringan_kapitalisasi_tahun_induk: (spec && (spec.jaringan_kapitalisasi_tahun_induk || spec.kapitalisasi_tahun_induk)) || '',
                        jaringan_kapitalisasi_nilai_induk: (spec && (spec.jaringan_kapitalisasi_nilai_induk || spec.kapitalisasi_nilai_induk)) || 0,
                        jaringan_jumlah: targetTotal,
                        jaringan_satuan: astap.satuan || (spec && spec.jaringan_satuan) || 'Ruas',
                        jaringan_nilai_perencanaan: (spec && (spec.jaringan_nilai_perencanaan || spec.nilai_perencanaan)) || 0,
                        jaringan_nilai_fisik: (spec && (spec.jaringan_nilai_fisik || spec.nilai_fisik)) || astap.nilai_realisasi || 0,
                        jaringan_nilai_pengawasan: (spec && (spec.jaringan_nilai_pengawasan || spec.nilai_pengawasan)) || 0,
                        jaringan_nilai_ap: (spec && (spec.jaringan_nilai_ap || spec.nilai_ap || spec.nilai_pip)) || 0,
                        jaringan_alamat: (spec && spec.jaringan_alamat) || astap.alamat_barang || '-'
                    }];
                },

                getKdpItemsForDetail(astap) {
                    if (!astap) return [];
                    let spec = astap.spesifikasi_json;
                    if (typeof spec === 'string') {
                        try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                    }
                    const targetTotal = (astap.registers && astap.registers.length > 0) ? astap.registers.length : (parseInt(astap.jumlah_volume) || 1);
                    if (spec && Array.isArray(spec.kdp_items) && spec.kdp_items.length > 0) {
                        return this.syncRepeaterItemsWithVolume(spec.kdp_items, targetTotal, ['kdp_jumlah_bangunan']);
                    }
                    return [{
                        kdp_nama_barang: (spec && spec.kdp_nama_barang) || astap.nama_barang || 'Konstruksi Dalam Pengerjaan',
                        kdp_kode_barang: (spec && spec.kdp_kode_barang) || astap.kode_barang || '',
                        kdp_luas_m2: (spec && (spec.kdp_luas_m2 || spec.luas_m2)) || astap.luas_m2 || 0,
                        kdp_kondisi: astap.kondisi || (spec && spec.kdp_kondisi) || 'B',
                        kdp_progres_persen: (spec && (spec.kdp_progres_persen ?? spec.progres_persen)) || 0,
                        kdp_bertingkat: (spec && (spec.kdp_bertingkat || spec.bertingkat)) || 'Bertingkat',
                        kdp_beton: (spec && (spec.kdp_beton || spec.beton)) || 'Beton',
                        kdp_status_tanah: (spec && (spec.kdp_status_tanah || spec.status_tanah)) || 'Tanah Hak Pakai RSUD',
                        kdp_kode_aset_tanah: (spec && (spec.kdp_kode_aset_tanah || spec.kode_aset_tanah)) || '1.3.1.01.01.02.013',
                        kdp_is_baru: (spec && (spec.kdp_is_baru || spec.is_baru)) || 'Baru',
                        kdp_kapitalisasi_tahun_induk: (spec && (spec.kdp_kapitalisasi_tahun_induk || spec.kapitalisasi_tahun_induk)) || '',
                        kdp_kapitalisasi_nilai_induk: (spec && (spec.kdp_kapitalisasi_nilai_induk || spec.kapitalisasi_nilai_induk)) || 0,
                        kdp_jumlah_bangunan: targetTotal,
                        kdp_satuan: astap.satuan || (spec && spec.kdp_satuan) || 'Gedung',
                        kdp_nilai_perencanaan: (spec && (spec.kdp_nilai_perencanaan || spec.nilai_perencanaan)) || 0,
                        kdp_nilai_fisik: (spec && (spec.kdp_nilai_fisik || spec.nilai_fisik)) || astap.nilai_realisasi || 0,
                        kdp_nilai_pengawasan: (spec && (spec.kdp_nilai_pengawasan || spec.nilai_pengawasan)) || 0,
                        kdp_nilai_ap: (spec && (spec.kdp_nilai_ap || spec.nilai_ap || spec.nilai_pip)) || 0,
                        kdp_alamat: (spec && spec.kdp_alamat) || astap.alamat_barang || '-'
                    }];
                },

                getAtbItemsForDetail(astap) {
                    if (!astap) return [];
                    let spec = astap.spesifikasi_json;
                    if (typeof spec === 'string') {
                        try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                    }
                    const targetTotal = (astap.registers && astap.registers.length > 0) ? astap.registers.length : (parseInt(astap.jumlah_volume) || 1);
                    if (spec && Array.isArray(spec.atb_items) && spec.atb_items.length > 0) {
                        return this.syncRepeaterItemsWithVolume(spec.atb_items, targetTotal, ['atb_jumlah']);
                    }
                    // Fallback: legacy single-item data
                    return [{
                        atb_nama_barang: (spec && spec.atb_nama_barang) || astap.nama_barang || 'Aset Tidak Berwujud',
                        atb_kode_barang: (spec && spec.atb_kode_barang) || astap.kode_barang || '',
                        atb_judul_nama: (spec && (spec.atb_judul_nama || spec.atb_judul)) || '',
                        atb_pencipta: (spec && spec.atb_pencipta) || astap.penyedia_nama || '',
                        atb_spesifikasi: (spec && spec.atb_spesifikasi) || '',
                        atb_jumlah: targetTotal,
                        atb_satuan: astap.satuan || (spec && spec.atb_satuan) || 'Lisensi',
                        atb_kondisi: astap.kondisi || (spec && spec.atb_kondisi) || 'Baik',
                        atb_nilai_satuan: astap.harga_satuan || (spec && spec.atb_nilai_satuan) || 0,
                        atb_administrasi_proyek: astap.biaya_administrasi_proyek || (spec && spec.atb_administrasi_proyek) || 0,
                        atb_ruang_pemegang: (spec && (spec.atb_ruang_pemegang || spec.ruang_pemegang)) || astap.ruang_pemegang || ''
                    }];
                },

                getLainnyaItemsForDetail(astap) {
                    if (!astap) return [];
                    let spec = astap.spesifikasi_json;
                    if (typeof spec === 'string') {
                        try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                    }
                    const targetTotal = (astap.registers && astap.registers.length > 0) ? astap.registers.length : (parseInt(astap.jumlah_volume) || 1);
                    if (spec && Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) {
                        return this.syncRepeaterItemsWithVolume(spec.lainnya_items, targetTotal, ['lainnya_jumlah_barang']);
                    }
                    // Fallback legacy: bangun 1 item dari field level atas di spesifikasi_json
                    const subType = (spec && spec.kib_e_sub_type) || (spec && spec.buku_judul ? 'buku' : (spec && (spec.kesenian_asal || spec.kesenian_pencipta) ? 'kesenian' : (spec && (spec.hewan_jenis || spec.hewan_judul) ? 'hewan_tumbuhan' : 'buku')));
                    return [{
                        kib_e_sub_type: subType,
                        lainnya_nama_barang: (spec && spec.lainnya_nama_barang) || astap.nama_barang || 'Aset Tetap Lainnya',
                        lainnya_buku_judul: (spec && spec.buku_judul) || (spec && spec.lainnya_buku_judul) || '',
                        lainnya_buku_pencipta: (spec && spec.buku_pencipta) || (spec && spec.lainnya_buku_pencipta) || '',
                        lainnya_buku_spesifikasi: (spec && spec.buku_spesifikasi) || (spec && spec.lainnya_buku_spesifikasi) || '',
                        lainnya_kesenian_asal: (spec && spec.kesenian_asal) || (spec && spec.lainnya_kesenian_asal) || '',
                        lainnya_kesenian_pencipta: (spec && spec.kesenian_pencipta) || (spec && spec.lainnya_kesenian_pencipta) || '',
                        lainnya_kesenian_spesifikasi: (spec && spec.kesenian_spesifikasi) || (spec && spec.lainnya_kesenian_spesifikasi) || '',
                        lainnya_kesenian_bahan: (spec && spec.kesenian_bahan) || (spec && spec.bahan) || (spec && spec.lainnya_kesenian_bahan) || '',
                        lainnya_kesenian_ukuran: (spec && spec.kesenian_ukuran) || (spec && spec.ukuran) || (spec && spec.lainnya_kesenian_ukuran) || '',
                        lainnya_hewan_jenis: (spec && spec.hewan_jenis) || (spec && spec.hewan_judul) || (spec && spec.lainnya_hewan_jenis) || '',
                        lainnya_hewan_judul: (spec && spec.hewan_judul) || (spec && spec.hewan_jenis) || (spec && spec.lainnya_hewan_judul) || '',
                        lainnya_hewan_spesifikasi: (spec && spec.hewan_spesifikasi) || (spec && spec.lainnya_hewan_spesifikasi) || '',
                        lainnya_jumlah_barang: astap.jumlah_volume || 1,
                        lainnya_satuan: astap.satuan || 'Eksemplar',
                        lainnya_kondisi: astap.kondisi || (spec && spec.lainnya_kondisi) || 'Baik',
                        lainnya_nilai_satuan: astap.harga_satuan || (spec && spec.lainnya_nilai_satuan) || (spec && spec.harga_satuan) || 0,
                        lainnya_administrasi_proyek: astap.biaya_administrasi_proyek || (spec && spec.lainnya_administrasi_proyek) || 0,
                        ruang_pemegang: (spec && spec.ruang_pemegang) || astap.ruang_unit || '-',
                        ruang_pemegang_lainnya: (spec && spec.ruang_pemegang) || astap.ruang_unit || '-'
                    }];
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
                    this.triwulanFilter = 'all';
                },

                astaps: window.__simatAstaps || [],

                // Hitung statistik kondisi dari registers suatu aset (Baik, Kurang Baik, Rusak Ringan, Rusak Berat)
                getKondisiStats(item) {
                    if (!item) return { total: 0, baik: 0, kurang_baik: 0, rusak_ringan: 0, rusak_berat: 0, pct_baik: 100, pct_kb: 0, pct_rr: 0, pct_rb: 0, kondisi_dominan: 'Baik', is_multi: false, text: 'Baik (100%)', badge_class: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30', dot_class: 'bg-emerald-400' };
                    const regs = item.registers || [];
                    const total = regs.length;
                    if (total === 0) {
                        const k = item.kondisi || item.kondisi_barang || 'Baik';
                        const isKb = k === 'Kurang Baik' || k === 'KB';
                        const isRr = k === 'Rusak Ringan' || k === 'RR';
                        const isRb = k === 'Rusak Berat' || k === 'RB' || k === 'Rusak';
                        const dominan = isKb ? 'Kurang Baik' : (isRr ? 'Rusak Ringan' : (isRb ? 'Rusak Berat' : 'Baik'));
                        const badgeClass = dominan === 'Baik' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : (dominan === 'Kurang Baik' ? 'bg-amber-500/15 text-amber-300 border-amber-500/30' : (dominan === 'Rusak Ringan' ? 'bg-orange-500/15 text-orange-300 border-orange-500/30' : 'bg-rose-500/15 text-rose-300 border-rose-500/30'));
                        const dotClass = dominan === 'Baik' ? 'bg-emerald-400' : (dominan === 'Kurang Baik' ? 'bg-amber-400' : (dominan === 'Rusak Ringan' ? 'bg-orange-400' : 'bg-rose-400'));
                        return {
                            total: 1,
                            baik: dominan === 'Baik' ? 1 : 0,
                            kurang_baik: isKb ? 1 : 0,
                            rusak_ringan: isRr ? 1 : 0,
                            rusak_berat: isRb ? 1 : 0,
                            pct_baik: dominan === 'Baik' ? 100 : 0,
                            pct_kb: isKb ? 100 : 0,
                            pct_rr: isRr ? 100 : 0,
                            pct_rb: isRb ? 100 : 0,
                            kondisi_dominan: dominan,
                            is_multi: false,
                            text: dominan + ' (100%)',
                            badge_class: badgeClass,
                            dot_class: dotClass
                        };
                    }
                    const baik = regs.filter(r => (r.kondisi || 'Baik') === 'Baik' || r.kondisi === 'B').length;
                    const kb   = regs.filter(r => r.kondisi === 'Kurang Baik' || r.kondisi === 'KB').length;
                    const rr   = regs.filter(r => r.kondisi === 'Rusak Ringan' || r.kondisi === 'RR').length;
                    const rb   = regs.filter(r => r.kondisi === 'Rusak Berat' || r.kondisi === 'RB' || r.kondisi === 'Rusak').length;
                    const dominan = (baik >= kb && baik >= rr && baik >= rb) ? 'Baik' : ((kb >= rr && kb >= rb) ? 'Kurang Baik' : ((rr >= rb) ? 'Rusak Ringan' : 'Rusak Berat'));
                    const isSingle = (baik === total) || (kb === total) || (rr === total) || (rb === total);

                    const pct_baik = Math.round((baik / total) * 100);
                    const pct_kb   = Math.round((kb   / total) * 100);
                    const pct_rr   = Math.round((rr   / total) * 100);
                    const pct_rb   = Math.round((rb   / total) * 100);

                    let parts = [];
                    if (baik > 0) parts.push(`${pct_baik}% Baik (${baik}/${total})`);
                    if (kb > 0)   parts.push(`${pct_kb}% Kurang Baik (${kb}/${total})`);
                    if (rr > 0)   parts.push(`${pct_rr}% Rusak Ringan (${rr}/${total})`);
                    if (rb > 0)   parts.push(`${pct_rb}% Rusak Berat (${rb}/${total})`);

                    let text = parts.join(' • ');
                    if (isSingle) {
                        if (baik === total) text = total > 1 ? `Baik (${total} Aset)` : 'Baik';
                        else if (kb === total) text = total > 1 ? `Kurang Baik (${total} Aset)` : 'Kurang Baik';
                        else if (rr === total) text = total > 1 ? `Rusak Ringan (${total} Aset)` : 'Rusak Ringan';
                        else if (rb === total) text = total > 1 ? `Rusak Berat (${total} Aset)` : 'Rusak Berat';
                    }

                    let badgeClass = 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30';
                    if (rb > 0 && rb >= baik && rb >= kb) {
                        badgeClass = 'bg-rose-500/15 text-rose-300 border-rose-500/30';
                    } else if (kb > 0 && kb >= baik) {
                        badgeClass = 'bg-amber-500/15 text-amber-300 border-amber-500/30';
                    } else if (rr > 0 && rr >= baik) {
                        badgeClass = 'bg-orange-500/15 text-orange-300 border-orange-500/30';
                    } else if (!isSingle) {
                        badgeClass = 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30';
                    }

                    let dotClass = pct_baik === 100 ? 'bg-emerald-400' : (pct_rb > 0 ? 'bg-rose-400' : (pct_rr > 0 ? 'bg-orange-400' : 'bg-amber-400'));

                    return {
                        total,
                        baik, kurang_baik: kb, rusak_ringan: rr, rusak_berat: rb,
                        pct_baik, pct_kb, pct_rr, pct_rb,
                        kondisi_dominan: dominan,
                        is_multi: !isSingle,
                        parts,
                        text,
                        badge_class: badgeClass,
                        dot_class: dotClass
                    };
                },

                // Hitung statistik persentase kondisi untuk rincian item atau master ASTAP
                getRincianKondisiStats(astap, idx = 0, type = null) {
                    if (!astap) return { total: 0, text: 'Baik (100%)', pct_baik: 100, pct_kb: 0, pct_rr: 0, pct_rb: 0, is_multi: false, badge_class: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' };

                    let regs = astap.registers || [];

                    // Jika ada multi-item dalam spesifikasi_json dan type diberikan
                    if (type) {
                        let items = [];
                        if (type === 'tanah_items') items = this.getTanahItemsForDetail(astap);
                        else if (type === 'mesin_items') items = this.getMesinItemsForDetail(astap);
                        else if (type === 'gedung_items') items = this.getGedungItemsForDetail(astap);
                        else if (type === 'jaringan_items') items = this.getJaringanItemsForDetail(astap);
                        else if (type === 'lainnya_items') items = this.getLainnyaItemsForDetail(astap);
                        else if (type === 'kdp_items') items = this.getKdpItemsForDetail(astap);
                        else if (type === 'atb_items') items = this.getAtbItemsForDetail(astap);

                        if (items.length > 1) {
                            const qtyKeyMap = {
                                'tanah_items': 'tanah_jumlah_bidang',
                                'mesin_items': 'mesin_jumlah_barang',
                                'gedung_items': 'gedung_jumlah_bangunan',
                                'jaringan_items': 'jaringan_jumlah',
                                'lainnya_items': 'lainnya_jumlah_barang',
                                'kdp_items': 'kdp_jumlah_bangunan',
                                'atb_items': 'atb_jumlah'
                            };
                            const qtyKey = qtyKeyMap[type] || 'jumlah';
                            
                            let start = 0;
                            for (let i = 0; i < idx && i < items.length; i++) {
                                start += Math.max(1, parseInt(items[i][qtyKey] || items[i].jumlah || 1));
                            }
                            const count = Math.max(1, parseInt(items[idx]?.[qtyKey] || items[idx]?.jumlah || 1));
                            regs = regs.slice(start, start + count);
                        }
                    }

                    const total = regs.length;
                    if (total === 0) {
                        let fallbackKondisi = 'Baik';
                        if (type && astap.spesifikasi_json?.[type]?.[idx]) {
                            const it = astap.spesifikasi_json[type][idx];
                            fallbackKondisi = it.mesin_kondisi || it.tanah_kondisi || it.gedung_kondisi || it.jaringan_kondisi || it.lainnya_kondisi || it.kdp_kondisi || it.atb_kondisi || astap.kondisi_barang || 'Baik';
                        } else {
                            fallbackKondisi = astap.kondisi_barang || 'Baik';
                        }
                        if (fallbackKondisi === 'B') fallbackKondisi = 'Baik';
                        if (fallbackKondisi === 'KB') fallbackKondisi = 'Kurang Baik';
                        if (fallbackKondisi === 'RR') fallbackKondisi = 'Rusak Ringan';
                        if (fallbackKondisi === 'RB' || fallbackKondisi === 'Rusak') fallbackKondisi = 'Rusak Berat';

                        return {
                            total: 1,
                            baik: fallbackKondisi === 'Baik' ? 1 : 0,
                            kurang_baik: fallbackKondisi === 'Kurang Baik' ? 1 : 0,
                            rusak_ringan: fallbackKondisi === 'Rusak Ringan' ? 1 : 0,
                            rusak_berat: fallbackKondisi === 'Rusak Berat' ? 1 : 0,
                            pct_baik: fallbackKondisi === 'Baik' ? 100 : 0,
                            pct_kb: fallbackKondisi === 'Kurang Baik' ? 100 : 0,
                            pct_rr: fallbackKondisi === 'Rusak Ringan' ? 100 : 0,
                            pct_rb: fallbackKondisi === 'Rusak Berat' ? 100 : 0,
                            is_multi: false,
                            text: fallbackKondisi + ' (100%)',
                            badge_class: fallbackKondisi === 'Baik' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' :
                                        (fallbackKondisi === 'Kurang Baik' ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' :
                                        (fallbackKondisi === 'Rusak Ringan' ? 'bg-orange-500/20 text-orange-300 border-orange-500/30' :
                                        'bg-rose-500/20 text-rose-300 border-rose-500/30')),
                            dot_class: fallbackKondisi === 'Baik' ? 'bg-emerald-400' :
                                      (fallbackKondisi === 'Kurang Baik' ? 'bg-amber-400' :
                                      (fallbackKondisi === 'Rusak Ringan' ? 'bg-orange-400' : 'bg-rose-400'))
                        };
                    }

                    const baik = regs.filter(r => (r.kondisi || 'Baik') === 'Baik' || r.kondisi === 'B').length;
                    const kb   = regs.filter(r => r.kondisi === 'Kurang Baik' || r.kondisi === 'KB').length;
                    const rr   = regs.filter(r => r.kondisi === 'Rusak Ringan' || r.kondisi === 'RR').length;
                    const rb   = regs.filter(r => r.kondisi === 'Rusak Berat' || r.kondisi === 'RB' || r.kondisi === 'Rusak').length;

                    const pct_baik = Math.round((baik / total) * 100);
                    const pct_kb   = Math.round((kb   / total) * 100);
                    const pct_rr   = Math.round((rr   / total) * 100);
                    const pct_rb   = Math.round((rb   / total) * 100);

                    const isSingle = (baik === total) || (kb === total) || (rr === total) || (rb === total);

                    let parts = [];
                    if (baik > 0) parts.push(`${pct_baik}% Baik (${baik}/${total})`);
                    if (kb > 0)   parts.push(`${pct_kb}% Kurang Baik (${kb}/${total})`);
                    if (rr > 0)   parts.push(`${pct_rr}% Rusak Ringan (${rr}/${total})`);
                    if (rb > 0)   parts.push(`${pct_rb}% Rusak Berat (${rb}/${total})`);

                    let text = parts.join(' • ');
                    if (isSingle) {
                        if (baik === total) text = `Baik (100%)`;
                        else if (kb === total) text = `Kurang Baik (100%)`;
                        else if (rr === total) text = `Rusak Ringan (100%)`;
                        else if (rb === total) text = `Rusak Berat (100%)`;
                    }

                    let badgeClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
                    if (rb > 0 && rb >= baik && rb >= kb) {
                        badgeClass = 'bg-rose-500/20 text-rose-300 border-rose-500/30';
                    } else if (kb > 0 && kb >= baik) {
                        badgeClass = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
                    } else if (rr > 0 && rr >= baik) {
                        badgeClass = 'bg-orange-500/20 text-orange-300 border-orange-500/30';
                    } else if (!isSingle) {
                        badgeClass = 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30';
                    }

                    let dotClass = pct_baik === 100 ? 'bg-emerald-400' : (pct_rb > 0 ? 'bg-rose-400' : (pct_rr > 0 ? 'bg-orange-400' : 'bg-amber-400'));

                    return {
                        total,
                        baik, kurang_baik: kb, rusak_ringan: rr, rusak_berat: rb,
                        pct_baik, pct_kb, pct_rr, pct_rb,
                        is_multi: !isSingle,
                        parts,
                        text,
                        badge_class: badgeClass,
                        dot_class: dotClass
                    };
                },

                // Ambil info NIBAR terdaftar untuk kartu rincian spesifikasi (mendukung unit tunggal maupun rentang banyak unit)
                getRincianNibar(astap, idx = 0, type = null) {
                    if (!astap || !astap.registers || astap.registers.length === 0) return null;

                    let regs = astap.registers;

                    if (type) {
                        let items = [];
                        if (type === 'tanah_items') items = this.getTanahItemsForDetail(astap);
                        else if (type === 'mesin_items') items = this.getMesinItemsForDetail(astap);
                        else if (type === 'gedung_items') items = this.getGedungItemsForDetail(astap);
                        else if (type === 'jaringan_items') items = this.getJaringanItemsForDetail(astap);
                        else if (type === 'lainnya_items') items = this.getLainnyaItemsForDetail(astap);
                        else if (type === 'kdp_items') items = this.getKdpItemsForDetail(astap);
                        else if (type === 'atb_items') items = this.getAtbItemsForDetail(astap);

                        if (items && items.length > 0) {
                            const qtyKeyMap = {
                                'tanah_items': 'tanah_jumlah_bidang',
                                'mesin_items': 'mesin_jumlah_barang',
                                'gedung_items': 'gedung_jumlah_bangunan',
                                'jaringan_items': 'jaringan_jumlah',
                                'lainnya_items': 'lainnya_jumlah_barang',
                                'kdp_items': 'kdp_jumlah_bangunan',
                                'atb_items': 'atb_jumlah'
                            };
                            const qtyKey = qtyKeyMap[type] || 'jumlah';

                            let start = 0;
                            for (let i = 0; i < idx && i < items.length; i++) {
                                start += Math.max(1, parseInt(items[i][qtyKey] || items[i].jumlah || 1));
                            }
                            const count = Math.max(1, parseInt(items[idx]?.[qtyKey] || items[idx]?.jumlah || 1));
                            regs = regs.slice(start, start + count);
                        }
                    }

                    if (!regs || regs.length === 0) return null;

                    const firstNibar = regs[0].nibar || regs[0].no_register || '';
                    if (!firstNibar) return null;

                    if (regs.length === 1) {
                        return {
                            label: 'NIBAR: ' + firstNibar,
                            tooltip: 'Unit Register NIBAR: ' + firstNibar,
                            is_range: false,
                            count: 1
                        };
                    }

                    const lastNibar = regs[regs.length - 1].nibar || regs[regs.length - 1].no_register || '';
                    if (!lastNibar || lastNibar === firstNibar) {
                        return {
                            label: 'NIBAR: ' + firstNibar,
                            tooltip: 'Unit Register NIBAR: ' + firstNibar,
                            is_range: false,
                            count: 1
                        };
                    }

                    // Ambil 7 digit terakhir sebagai nomor urut register penutup rentang
                    const lastSuffix = lastNibar.slice(-7);
                    return {
                        label: 'NIBAR: ' + firstNibar + ' - ' + lastSuffix,
                        tooltip: 'Rentang NIBAR: ' + firstNibar + ' s/d ' + lastNibar + ' (' + regs.length + ' Unit Aset)',
                        is_range: true,
                        count: regs.length
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
                        // Filter kondisi: mencakup jika ada unit dengan kondisi tsb
                        const stats = this.getKondisiStats(item);
                        let matchKondisi = true;
                        if (this.kondisiFilter !== 'all') {
                            if (this.kondisiFilter === 'Baik') {
                                matchKondisi = stats.baik > 0 || stats.kondisi_dominan === 'Baik';
                            } else if (this.kondisiFilter === 'Kurang Baik') {
                                matchKondisi = stats.kurang_baik > 0 || stats.kondisi_dominan === 'Kurang Baik';
                            } else if (this.kondisiFilter === 'Rusak Berat') {
                                matchKondisi = stats.rusak_berat > 0 || stats.kondisi_dominan === 'Rusak Berat';
                            } else {
                                matchKondisi = stats.kondisi_dominan === this.kondisiFilter;
                            }
                        }
                        const matchAsalUsul = this.asalUsulFilter === 'all' || item.asal_usul === this.asalUsulFilter;
                        const matchTahun = this.tahunFilter === 'all' || String(item.tahun_perolehan) === String(this.tahunFilter);
                        
                        let matchTriwulan = true;
                        if (this.triwulanFilter !== 'all') {
                            const targetKey = this.triwulanFilter.replace(/[\s_]/g, '').toUpperCase();
                            const itemTw = (item.triwulan || 'TWI').replace(/[\s_]/g, '').toUpperCase();
                            matchTriwulan = (itemTw === targetKey) ||
                                      (targetKey === 'TWI' && itemTw === 'TW1') || (targetKey === 'TW1' && itemTw === 'TWI') ||
                                      (targetKey === 'TWII' && itemTw === 'TW2') || (targetKey === 'TW2' && itemTw === 'TWII') ||
                                      (targetKey === 'TWIII' && itemTw === 'TW3') || (targetKey === 'TW3' && itemTw === 'TWIII') ||
                                      (targetKey === 'TWIV' && itemTw === 'TW4') || (targetKey === 'TW4' && itemTw === 'TWIV');
                        }

                        return matchSearch && matchCategory && matchKondisi && matchAsalUsul && matchTahun && matchTriwulan;
                    });
                },

                get filteredRegisters() {
                    if (!this.selectedAstapDetail || !this.selectedAstapDetail.registers) return [];
                    const query = (this.detailSearchQuery || '').toLowerCase().trim();
                    const filtered = this.selectedAstapDetail.registers.filter(reg => {
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

                    // Pastikan selalu terurut berdasarkan nomor urut NIBAR ascending (0001 di paling atas)
                    return filtered.sort((a, b) => {
                        const numA = a.no_register_int || parseInt((a.nibar || a.no_register || '').slice(-7)) || 0;
                        const numB = b.no_register_int || parseInt((b.nibar || b.no_register || '').slice(-7)) || 0;
                        if (numA !== numB) return numA - numB;
                        return (a.nibar || a.no_register || '').localeCompare(b.nibar || b.no_register || '');
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
                    this.generateQrImage(this.selectedQrItem);
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
                    this.generateQrImage(this.selectedQrItem);
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

                generateQrImage(item) {
                    if (!item) return;
                    this.isGeneratingQr = true;
                    this.qrDataUrl = '';
                    const scanUrl = this.getQrPayloadUrl(item);

                    const renderQr = () => {
                        if (window.QRCode && typeof window.QRCode.toDataURL === 'function') {
                            window.QRCode.toDataURL(scanUrl, {
                                width: 320,
                                margin: 2,
                                color: {
                                    dark: '#000000',
                                    light: '#ffffff'
                                },
                                errorCorrectionLevel: 'M'
                            }).then(url => {
                                this.qrDataUrl = url;
                                this.isGeneratingQr = false;
                            }).catch(err => {
                                console.error('Gagal generate QR Code lokal:', err);
                                this.qrDataUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=10&data=' + encodeURIComponent(scanUrl);
                                this.isGeneratingQr = false;
                            });
                        } else {
                            this.qrDataUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=320x320&margin=10&data=' + encodeURIComponent(scanUrl);
                            this.isGeneratingQr = false;
                        }
                    };

                    if (this.$nextTick) {
                        this.$nextTick(() => renderQr());
                    } else {
                        setTimeout(renderQr, 50);
                    }
                },

                downloadQrImage() {
                    if (!this.selectedQrItem || !this.qrDataUrl) return;
                    const cleanFilename = 'QR_CODE_' + (this.selectedQrItem.kode_barang || 'ASET').replace(/[\/\.\s]/g, '_') + '.png';

                    if (this.qrDataUrl.startsWith('data:image/')) {
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = this.qrDataUrl;
                        a.download = cleanFilename;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        return;
                    }

                    // Fallback jika berupa external url
                    fetch(this.qrDataUrl)
                        .then(res => res.blob())
                        .then(blob => {
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.style.display = 'none';
                            a.href = url;
                            a.download = cleanFilename;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                        })
                        .catch(() => {
                            window.open(this.qrDataUrl, '_blank');
                        });
                },

                downloadExcel() {
                    this.openExportModal();
                },

                openEdit(item) {
                    if (!item || !item.id) return;
                    window.location.href = '/astap/' + item.id + '/edit';
                }
            };
        }
    </script>
