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
                    fill = "FFFFFF";
                    fontColor = "0F172A";
                    border = null;
                    align = "center";
                    fontSize = (r === signStartRow + 5) ? 10.5 : 9.5;
                    bold = (r === signStartRow + 1 || r === signStartRow + 2 || r === signStartRow + 5 || r === signStartRow + 6);
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
    function getKibMerges(baseMerges, colCount, titleRowCount, totalRowCount) {
        const offset = titleRowCount - 3; // Baseline merge header tabel adalah index row 3
        const titleMerges = [];
        for (let r = 0; r < titleRowCount; r++) {
            titleMerges.push({ s: { r: r, c: 0 }, e: { r: r, c: colCount - 1 } });
        }
        const shiftedBaseMerges = baseMerges.map(m => ({
            s: { r: m.s.r + offset, c: m.s.c },
            e: { r: m.e.r + offset, c: m.e.c }
        }));
        const footerMerge = {
            s: { r: totalRowCount - 1, c: 0 },
            e: { r: totalRowCount - 1, c: 12 }
        };
        return [...titleMerges, footerMerge, ...shiftedBaseMerges];
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
    function applyUnified4StepMasterSheetStyling(ws, rowCount, colCount, kibL3ColCount, titleRowCount = 5) {
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
                // 3. BARIS FOOTER / TOTAL (BARIS TERAKHIR)
                else if (r === rowCount - 1) {
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

        // Filter data berdasarkan Tahun dan Triwulan yang dipilih
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

            const matchCategory = filterCat === 'all' || item.category === filterCat;
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
            const isExtracom = !!item.is_extracomtable || (item.category === 'EXTRACOM');
            const cat = isExtracom ? 'EXTRACOM' : (item.category || (item.jenis_aset_kode === '1.3.1' ? 'KIB A' : (item.jenis_aset_kode === '1.3.3' ? 'KIB C' : (item.jenis_aset_kode === '1.3.4' ? 'KIB D' : (item.jenis_aset_kode === '1.3.5' ? 'KIB E' : (item.jenis_aset_kode === '1.3.6' ? 'KIB F' : (item.jenis_aset_kode === '1.5.3' ? 'ATB' : 'KIB B')))))));

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
            // r15: Baris Kosong Pemisah
            [""],
            // r16: Tanggal Laporan
            ["", "", "", "", "", "Bondowoso, " + new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})],
            // r17: Label Mengetahui
            ["", "Mengetahui,", "", "", "", "Pengurus Barang Pengelola,"],
            // r18: Jabatan
            ["", "Pejabat Pembuat Komitmen (PPK)", "", "", "", "RSUD Dr. H. Koesnandi"],
            // r19: Spasi TTD 1
            [""],
            // r20: Spasi TTD 2
            [""],
            // r21: Nama Pejabat
            ["", "( ................................................ )", "", "", "", "( ................................................ )"],
            // r22: NIP Pejabat
            ["", "NIP. 19780101 200501 1 008", "", "", "", "NIP. 19850615 201001 2 015"]
        ];

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

        // Merge Cells Rekapitulasi (Title, Footer, & Tanda Tangan)
        wsRekap['!merges'] = [
            // Title Banners (r0 - r3, c0 s/d c8)
            { s: { r: 0, c: 0 }, e: { r: 0, c: 8 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 8 } },
            { s: { r: 2, c: 0 }, e: { r: 2, c: 8 } },
            { s: { r: 3, c: 0 }, e: { r: 3, c: 8 } },

            // Footer Total Banner (r14: c0 s/d c2)
            { s: { r: 14, c: 0 }, e: { r: 14, c: 2 } },

            // Tanda Tangan: Tanggal (r16, c5 s/d c8)
            { s: { r: 16, c: 5 }, e: { r: 16, c: 8 } },

            // Tanda Tangan: PPK (c1 s/d c3) & Pengurus Barang (c5 s/d c8)
            { s: { r: 17, c: 1 }, e: { r: 17, c: 3 } },
            { s: { r: 17, c: 5 }, e: { r: 17, c: 8 } },

            { s: { r: 18, c: 1 }, e: { r: 18, c: 3 } },
            { s: { r: 18, c: 5 }, e: { r: 18, c: 8 } },

            { s: { r: 21, c: 1 }, e: { r: 21, c: 3 } },
            { s: { r: 21, c: 5 }, e: { r: 21, c: 8 } },

            { s: { r: 22, c: 1 }, e: { r: 22, c: 3 } },
            { s: { r: 22, c: 5 }, e: { r: 22, c: 8 } }
        ];

        applyRekapSheetStyling(wsRekap, rekapData.length, 9, 5, 14, 16);
        XLSX.utils.book_append_sheet(wb, wsRekap, "1. Rekapitulasi");

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
        ], 49, kibATitleRows.length, kibARows.length);
        // ─────────────────────────────────────────────────────────────────────────
        applyUnified4StepMasterSheetStyling(wsKibA, kibARows.length, 49, 25, kibATitleRows.length);
        XLSX.utils.book_append_sheet(wb, wsKibA, "2. A");

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
        ], 54, kibBTitleRows.length, kibBRows.length);

        applyUnified4StepMasterSheetStyling(wsKibB, kibBRows.length, 54, 31, kibBTitleRows.length);
        XLSX.utils.book_append_sheet(wb, wsKibB, "3. B");

        // ------------------------------------------------------------------------
        // 4. KIB C (GEDUNG DAN BANGUNAN) - COMPLETE 4-STEP MASTER SHEET (54 KOLOM)
        // Sesuai Format Baku Gambar: 1-15 (Langkah 1-2), 16-45 (Langkah 3), 46-54 (Langkah 4)
        // ------------------------------------------------------------------------
        const kibCTitleRows = getKibTitleRows("GEDUNG DAN BANGUNAN", yearLabel, filterTw);
        const kibCRows = [
            ...kibCTitleRows,
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
                "Luas Lantai (m²)",
                "Kondisi / Spesifikasi", "", "",
                "Jenis Bangunan", "", "", "", "", "",
                "Riwayat Pembelian", "", "", "", "", "", "", "",
                "VOLUME", "",
                "Nilai Barang (Rp)", "", "",
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

        // ── Grouping KIB C Berdasarkan Sub Rincian Objek PMDN 108 ──────────────
        const kibCGroups = {};
        categories['KIB C'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.3.3.01.01.01');
            if (!kibCGroups[groupKey]) kibCGroups[groupKey] = [];
            kibCGroups[groupKey].push(item);
        });

        let globalKibCNo = 1;
        let kibCTotalAnggaran = 0, kibCTotalRealisasi = 0, kibCTotalLuas = 0, kibCTotalUnit = 0;
        let kibCTotalPerencanaan = 0, kibCTotalFisik = 0, kibCTotalPengawasan = 0, kibCTotalNilaiBarang = 0;

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
                        const nilaiPip = parseFloat(gItem.gedung_nilai_pip) || 0;
                        const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan + nilaiPip) || (parseFloat(item.total_realisasi) || 0);
                        const jumlahBangunan = parseInt(gItem.gedung_jumlah_bangunan) || 1;
                        const luasM2 = parseFloat(gItem.gedung_luas_m2) || 0;

                        kibCTotalLuas += luasM2;
                        kibCTotalUnit += jumlahBangunan;
                        kibCTotalPerencanaan += nilaiPerencanaan;
                        kibCTotalFisik += nilaiFisik;
                        kibCTotalPengawasan += nilaiPengawasan;
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
                            totalNilaiBarang,                                            // c40: col 41 (Total Nilai Barang)
                            item.sp2d_nomor || '-',                                      // c41: col 42 (SP2D NOMOR)
                            formatAstapDate(item.sp2d_tanggal),                          // c42: col 43 (SP2D TANGGAL)
                            item.bast_dokumen_nomor || '-',                              // c43: col 44 (BAST NOMOR)
                            formatAstapDate(item.bast_dokumen_tanggal),                  // c44: col 45 (BAST TANGGAL)
                            gItem.gedung_alamat || item.alamat_barang || '-',            // c45: col 46 (Letak/ Alamat)
                            ...getStep4Columns(item)                                     // c46-c53: cols 47-54
                        ]);
                    });
                } else {
                    const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                    const nilaiPerencanaan = parseFloat(item.gedung_nilai_perencanaan) || parseFloat(item.nilai_perencanaan) || 0;
                    const nilaiFisik = parseFloat(item.gedung_nilai_fisik) || parseFloat(item.nilai_fisik) || totalVal;
                    const nilaiPengawasan = parseFloat(item.gedung_nilai_pengawasan) || parseFloat(item.nilai_pengawasan) || 0;
                    const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan) || totalVal;
                    const jumlahBangunan = parseInt(item.jumlah_volume) || parseInt(item.jumlah_unit) || 1;
                    const luasM2 = parseFloat(item.luas_m2) || 0;

                    kibCTotalLuas += luasM2;
                    kibCTotalUnit += jumlahBangunan;
                    kibCTotalPerencanaan += nilaiPerencanaan;
                    kibCTotalFisik += nilaiFisik;
                    kibCTotalPengawasan += nilaiPengawasan;
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
                        totalNilaiBarang,                                            // c40: col 41 (Total Nilai Barang)
                        item.sp2d_nomor || '-',                                      // c41: col 42 (SP2D NOMOR)
                        formatAstapDate(item.sp2d_tanggal),                          // c42: col 43 (SP2D TANGGAL)
                        item.bast_dokumen_nomor || '-',                              // c43: col 44 (BAST NOMOR)
                        formatAstapDate(item.bast_dokumen_tanggal),                  // c44: col 45 (BAST TANGGAL)
                        item.alamat_barang || '-',                                   // c45: col 46 (Letak/ Alamat)
                        ...getStep4Columns(item)                                     // c46-c53: cols 47-54
                    ]);
                }
            });
        });

        // ── Baris Footer Total KIB C (54 Kolom) ──────────────────────────────────
        const kibCFooterRow = Array(54).fill("");
        kibCFooterRow[0] = "JUMLAH";
        kibCFooterRow[13] = kibCTotalAnggaran;
        kibCFooterRow[14] = kibCTotalRealisasi;
        kibCFooterRow[17] = kibCTotalLuas;
        kibCFooterRow[35] = kibCTotalUnit;
        kibCFooterRow[37] = kibCTotalPerencanaan;
        kibCFooterRow[38] = kibCTotalFisik;
        kibCFooterRow[39] = kibCTotalPengawasan;
        kibCFooterRow[40] = kibCTotalNilaiBarang;
        kibCRows.push(kibCFooterRow);

        const wsKibC = XLSX.utils.aoa_to_sheet(kibCRows);
        wsKibC['!cols'] = Array(54).fill({wch: 18});
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
        wsKibC['!cols'][41] = {wch: 20}; wsKibC['!cols'][42] = {wch: 14};
        wsKibC['!cols'][43] = {wch: 28}; wsKibC['!cols'][44] = {wch: 14};
        wsKibC['!cols'][45] = {wch: 32}; wsKibC['!cols'][46] = {wch: 28};
        wsKibC['!cols'][47] = {wch: 24}; wsKibC['!cols'][48] = {wch: 24};
        wsKibC['!cols'][49] = {wch: 22}; wsKibC['!cols'][50] = {wch: 30};
        wsKibC['!cols'][51] = {wch: 24}; wsKibC['!cols'][52] = {wch: 22};
        wsKibC['!cols'][53] = {wch: 26};

        // ── Merge Cells KIB C (54 Kolom Sesuai Format Baku Gambar) ──────────────
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

            // Col 16-45: RINCIAN BELANJA MODAL ... (Top Banner r3, c15-c44)
            {s:{r:3,c:15}, e:{r:3,c:44}},
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
            // Col 38-40: Nilai Barang (Rp) (r4 banner c37-c39)
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
        ], 54, kibCTitleRows.length, kibCRows.length);

        applyUnified4StepMasterSheetStyling(wsKibC, kibCRows.length, 54, 30, kibCTitleRows.length);
        XLSX.utils.book_append_sheet(wb, wsKibC, "4. C");

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
                "Luas (m²)",
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
                "(B,KB,RB)", "Konstruksi Jaringan", "Bahan Jaringan",
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
                    item.jaringan_konstruksi || item.gedung_bertingkat || '-',  // c19: col 20 (Konstruksi Jaringan)
                    item.gedung_beton || '-',                                    // c20: col 21 (Bahan Jaringan)
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
            {s:{r:4,c:17}, e:{r:6,c:17}},  // Col 18: Luas (m²)
            // Col 19-21: Kondisi / Spesifikasi (r4 banner c18-c20)
            {s:{r:4,c:18}, e:{r:4,c:20}},
            {s:{r:5,c:18}, e:{r:6,c:18}},  // Col 19: (B,KB,RB)
            {s:{r:5,c:19}, e:{r:6,c:19}},  // Col 20: Konstruksi Jaringan
            {s:{r:5,c:20}, e:{r:6,c:20}},  // Col 21: Bahan Jaringan

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
        ], 54, kibDTitleRows.length, kibDRows.length);

        applyUnified4StepMasterSheetStyling(wsKibD, kibDRows.length, 54, 30, kibDTitleRows.length);
        XLSX.utils.book_append_sheet(wb, wsKibD, "5. D");

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
            // r7: Column Numbers (Sesuai Format Template: 1-15, 17-45, 46-54)
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33", "34", "35", "36", "37", "38", "39", "40", "41 = 39+40", "42", "43", "44", "45",
                "46", "47", "48", "49", "50", "51", "52", "53", "54"
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
                const totalVal = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
                const jumlahUnit = parseInt(item.jumlah_volume) || parseInt(item.jumlah_unit) || 1;
                const hargaSatuan = parseFloat(item.harga_satuan) || (jumlahUnit > 0 ? (totalVal / jumlahUnit) : totalVal);
                const adminProyek = parseFloat(item.administrasi_proyek) || parseFloat(item.admin_proyek) || 0;
                const totalNilaiBarang = (hargaSatuan * jumlahUnit + adminProyek) || totalVal;
                const ruangUnit = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '-');

                kibETotalUnit += jumlahUnit;
                kibETotalAdminProyek += adminProyek;
                kibETotalNilaiBarang += totalNilaiBarang;

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
                    item.judul_buku || item.judul_pencipta || item.nama_barang || '-', // c17: col 19 (Judul Buku)
                    item.pencipta || item.judul_pencipta || '-',                // c18: col 20 (Pencipta Buku)
                    item.spesifikasi || '-',                                     // c19: col 21 (Spesifikasi Buku)
                    item.asal_kesenian || '-',                                   // c20: col 22 (Asal Daerah)
                    item.pencipta_kesenian || item.judul_pencipta || '-',        // c21: col 23 (Pencipta Seni)
                    item.spesifikasi || '-',                                     // c22: col 24 (Spesifikasi Seni)
                    item.bahan || '-',                                           // c23: col 25 (Bahan Seni)
                    item.ukuran || '-',                                          // c24: col 26 (Ukuran m/cm)
                    item.judul_hewan || item.nama_barang || '-',                 // c25: col 27 (Judul/Jenis Hewan)
                    item.spesifikasi || '-',                                     // c26: col 28 (Spesifikasi Hewan)
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
        ], 53, kibETitleRows.length, kibERows.length);

        applyUnified4StepMasterSheetStyling(wsKibE, kibERows.length, 53, 29, kibETitleRows.length);
        XLSX.utils.book_append_sheet(wb, wsKibE, "6. E");

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
                "Nilai", "Tahun\nInduk", "Nilai Induk s/d\n" + (parseInt(yearLabel) - 1 || '2025'),
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
                    totalNilaiBarang,                                            // c24: col 25 (Nilai Kapitalisasi/Fisik)
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
        ], 55, kibFTitleRows.length, kibFRows.length);

        applyUnified4StepMasterSheetStyling(wsKibF, kibFRows.length, 55, 31, kibFTitleRows.length);
        XLSX.utils.book_append_sheet(wb, wsKibF, "7. F");

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
        ], 47, atbTitleRows.length, atbRows.length);

        applyUnified4StepMasterSheetStyling(wsAtb, atbRows.length, 47, 22, atbTitleRows.length);
        XLSX.utils.book_append_sheet(wb, wsAtb, "8. ATB");

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
        ], 51, extracomTitleRows.length, extracomRows.length);

        applyUnified4StepMasterSheetStyling(wsExtracom, extracomRows.length, 51, 27, extracomTitleRows.length);
        XLSX.utils.book_append_sheet(wb, wsExtracom, "9. Extracom");

        // DOWNLOAD FILE EXCEL 4 LANGKAH
        const twSlug = filterTw === 'all' ? 'TAHUNAN' : filterTw.replace(/[\s_]/g, '');
        const fileName = "LAPORAN_ASTAP_RSUD_KOESNANDI_" + yearLabel + "_" + twSlug + ".xlsx";
        XLSX.writeFile(wb, fileName);
        setTimeout(() => { isExportingAstap = false; }, 1500);
    }
    </script>

    <script>
        window.__simatAstaps = @json(!empty($astaps) ? $astaps : []);

        function astapCatalog() {
            return {
                astaps: window.__simatAstaps || [],
                
                // State Modal Export Excel Berita Acara / Laporan
                showExportModal: false,
                exportYear: '2026',
                exportTriwulan: 'all',
                exportCategory: 'all',
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
                            this.showToast('✅ ' + data.message, 'success');
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
                        message: 'Sistem akan menyusun ulang dan merapatkan nomor urut register (NIBAR) untuk seluruh unit pada barang ini beserta barang sejenis di tahun yang sama dari nomor 1 secara berurutan tanpa celah.\n\n⚠️ Pastikan mencetak ulang stiker QR Code jika sudah pernah dicetak sebelumnya.',
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
                                    this.showToast('✅ ' + data.message, 'success');
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
                        const matchCat = fCat === 'all' || item.category === fCat;
                        return matchYear && matchTw && matchCat;
                    }).length;
                },

                submitExport() {
                    this.isSubmittingExport = true;
                    exportAstapToExcel({
                        year: this.exportYear,
                        triwulan: this.exportTriwulan,
                        category: this.exportCategory
                    });
                    setTimeout(() => {
                        this.isSubmittingExport = false;
                        this.showExportModal = false;
                        this.showToast('✅ Berhasil mengekspor Laporan ASTAP ' + (this.exportTriwulan === 'all' ? 'Tahunan' : this.exportTriwulan) + ' ' + this.exportYear + '!', 'success');
                    }, 800);
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
                    this.triwulanFilter = 'all';
                },

                astaps: window.__simatAstaps || [],

                // Hitung statistik kondisi dari registers suatu aset (Baik, Kurang Baik, Rusak Berat)
                getKondisiStats(item) {
                    const regs = item.registers || [];
                    const total = regs.length;
                    if (total === 0) {
                        const k = item.kondisi || 'Baik';
                        const isKb = k === 'Kurang Baik';
                        return { total: 1, baik: k==='Baik'?1:0, kurang_baik: isKb?1:0, rusak_berat: k==='Rusak Berat'?1:0, pct_baik: k==='Baik'?100:0, pct_kb: isKb?100:0, pct_rb: k==='Rusak Berat'?100:0, kondisi_dominan: isKb ? 'Kurang Baik' : k };
                    }
                    const baik = regs.filter(r => (r.kondisi||'Baik') === 'Baik').length;
                    const kb   = regs.filter(r => r.kondisi === 'Kurang Baik').length;
                    const rb   = regs.filter(r => r.kondisi === 'Rusak Berat').length;
                    const dominan = baik >= kb && baik >= rb ? 'Baik' : (kb >= rb ? 'Kurang Baik' : 'Rusak Berat');
                    return {
                        total,
                        baik, kurang_baik: kb, rusak_berat: rb,
                        pct_baik: Math.round(baik / total * 100),
                        pct_kb:   Math.round(kb   / total * 100),
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

                    <button type="button" @click="openExportModal()"
                        class="px-3.5 py-2.5 rounded-xl bg-slate-900/80 hover:bg-slate-800 text-emerald-400 border border-emerald-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-1.5 cursor-pointer active:scale-95">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>Export Excel (Pilih TW)</span>
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

                <!-- Advanced Filter Collapsible Bar (3 Kolom: KIB, Tahun, Triwulan) -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-3 border-t border-slate-800/60">
                    
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
                                <template x-for="yr in availableYears" :key="yr">
                                    <option :value="yr" class="bg-slate-900 text-cyan-300 py-2 font-medium" x-text="yr"></option>
                                </template>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Triwulan Pengadaan -->
                    <div>
                        <label class="block text-[10px] font-bold text-cyan-400 uppercase tracking-wider mb-1">Triwulan Pengadaan</label>
                        <div class="relative">
                            <select x-model="triwulanFilter"
                                style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-semibold text-cyan-300 focus:outline-none focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500/30 cursor-pointer hover:bg-slate-900/80 transition-all">
                                <option value="all" class="bg-slate-900 text-slate-200 py-2 font-medium">Semua Triwulan</option>
                                <option value="TW I" class="bg-slate-900 text-cyan-300 py-2 font-medium">Triwulan I (TW I)</option>
                                <option value="TW II" class="bg-slate-900 text-cyan-300 py-2 font-medium">Triwulan II (TW II)</option>
                                <option value="TW III" class="bg-slate-900 text-cyan-300 py-2 font-medium">Triwulan III (TW III)</option>
                                <option value="TW IV" class="bg-slate-900 text-cyan-300 py-2 font-medium">Triwulan IV (TW IV)</option>
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-400">
                                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
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
                                            <template x-if="st.total <= 1 || (st.pct_baik === 100 || st.pct_kb === 100 || st.pct_rb === 100)">
                                                <span class="inline-flex items-center px-3 py-1 rounded-xl text-[11px] font-bold border shadow-sm select-none"
                                                      :class="{
                                                          'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': st.kondisi_dominan === 'Baik',
                                                          'bg-amber-500/15 text-amber-300 border-amber-500/30': st.kondisi_dominan === 'Kurang Baik',
                                                          'bg-rose-500/15 text-rose-300 border-rose-500/30': st.kondisi_dominan === 'Rusak Berat'
                                                      }">
                                                    <span class="w-1.5 h-1.5 rounded-full mr-1.5"
                                                          :class="{
                                                              'bg-emerald-400': st.kondisi_dominan === 'Baik',
                                                              'bg-amber-400': st.kondisi_dominan === 'Kurang Baik',
                                                              'bg-rose-400': st.kondisi_dominan === 'Rusak Berat'
                                                          }"></span>
                                                    <span x-text="st.kondisi_dominan + (st.total > 1 ? ' 100%' : '')"></span>
                                                </span>
                                            </template>
                                            <!-- Jika multi kondisi: tampilkan progress bar breakdown -->
                                            <template x-if="st.total > 1 && !(st.pct_baik === 100 || st.pct_kb === 100 || st.pct_rb === 100)">
                                                <div class="min-w-[130px]">
                                                    <!-- Mini progress bar gabungan -->
                                                    <div class="flex h-2 rounded-full overflow-hidden bg-slate-800 mb-1.5">
                                                        <div x-show="st.pct_baik > 0" class="bg-emerald-400 transition-all" :style="'width:' + st.pct_baik + '%'"></div>
                                                        <div x-show="st.pct_kb > 0"   class="bg-amber-400 transition-all"   :style="'width:' + st.pct_kb + '%'"></div>
                                                        <div x-show="st.pct_rb > 0"   class="bg-rose-400 transition-all"    :style="'width:' + st.pct_rb + '%'"></div>
                                                    </div>
                                                    <!-- Label persentase per kondisi -->
                                                    <div class="flex flex-wrap gap-x-2 gap-y-0.5 justify-center">
                                                        <template x-if="st.baik > 0">
                                                            <span class="text-[9.5px] font-bold text-emerald-400" x-text="st.pct_baik + '% Baik'"></span>
                                                        </template>
                                                        <template x-if="st.kurang_baik > 0">
                                                            <span class="text-[9.5px] font-bold text-amber-400" x-text="st.pct_kb + '% K.Baik'"></span>
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
        <div x-show="showDetailModal" x-cloak @click.self="showDetailModal = false" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 50;">
            <div class="border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-5 my-auto" style="background-color: #0f172a;">
                
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
                                <div class="space-y-2.5">
                                    <!-- Rincian Masing-Masing Bidang Tanah -->
                                    <template x-if="selectedAstapDetail.spesifikasi_json?.tanah_items && selectedAstapDetail.spesifikasi_json.tanah_items.length > 0">
                                        <div class="space-y-2.5">
                                            <template x-for="(tItem, tIdx) in selectedAstapDetail.spesifikasi_json.tanah_items" :key="tIdx">
                                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2.5 shadow-sm">
                                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[11px] border border-emerald-500/30">
                                                                🌾 Bidang Tanah #<span x-text="tIdx + 1"></span>
                                                            </span>
                                                            <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-[10.5px] border border-cyan-500/30"
                                                                  x-text="(tItem.tanah_jumlah_bidang || 1) + ' ' + (selectedAstapDetail.satuan || 'Bidang')"></span>
                                                            <template x-if="selectedAstapDetail.registers && selectedAstapDetail.registers[tIdx]">
                                                                <span class="text-[11px] text-cyan-400 font-mono font-bold" x-text="'NIBAR: ' + (selectedAstapDetail.registers[tIdx].nibar || selectedAstapDetail.registers[tIdx].no_register)"></span>
                                                            </template>
                                                        </div>
                                                        <div class="text-[11px] font-mono">
                                                            <span class="text-slate-400">Total Realisasi: </span>
                                                            <strong class="text-emerald-400 font-bold" x-text="'Rp ' + (Number(tItem.tanah_nilai_perencanaan || 0) + Number(tItem.tanah_nilai_fisik || 0) + Number(tItem.tanah_nilai_pengawasan || 0)).toLocaleString('id-ID')"></strong>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📜 Hak &amp; Sertifikat</span>
                                                            <span class="text-amber-300 font-bold block" x-text="tItem.tanah_hak || 'Hak Pakai'"></span>
                                                            <span class="text-cyan-300 font-mono text-[10px] block truncate" x-text="tItem.tanah_sertifikat_no ? ('No: ' + tItem.tanah_sertifikat_no) : 'Tanpa No Sertifikat'"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Luas &amp; Kondisi</span>
                                                            <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(tItem.tanah_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                                            <span class="text-emerald-300 font-semibold text-[10px]" x-text="'Kondisi: ' + (tItem.tanah_kondisi === 'B' ? 'Baik (B)' : (tItem.tanah_kondisi === 'KB' ? 'Kurang Baik (KB)' : (tItem.tanah_kondisi === 'RB' ? 'Rusak Berat (RB)' : (tItem.tanah_kondisi || 'Baik'))))"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Rincian Komponen Nilai</span>
                                                            <span class="text-slate-200 font-medium block text-[10px]" x-text="'Fisik: Rp ' + Number(tItem.tanah_nilai_fisik || 0).toLocaleString('id-ID')"></span>
                                                            <span class="text-slate-400 text-[9px]" x-text="'Pln: ' + Number(tItem.tanah_nilai_perencanaan || 0).toLocaleString('id-ID') + ' • Was: ' + Number(tItem.tanah_nilai_pengawasan || 0).toLocaleString('id-ID')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Alamat Lokasi</span>
                                                            <span class="text-teal-300 font-medium block truncate" :title="tItem.tanah_alamat" x-text="tItem.tanah_alamat || '-'"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Fallback jika data tanah tunggal / legacy -->
                                    <template x-if="!selectedAstapDetail.spesifikasi_json?.tanah_items || selectedAstapDetail.spesifikasi_json.tanah_items.length === 0">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2.5 shadow-sm">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[11px] border border-emerald-500/30">
                                                        🌾 Bidang Tanah #1
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-[10.5px] border border-cyan-500/30"
                                                          x-text="(selectedAstapDetail.jumlah_volume || 1) + ' ' + (selectedAstapDetail.satuan || 'Bidang')"></span>
                                                    <template x-if="selectedAstapDetail.registers && selectedAstapDetail.registers[0]">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold" x-text="'NIBAR: ' + (selectedAstapDetail.registers[0].nibar || selectedAstapDetail.registers[0].no_register)"></span>
                                                    </template>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Total Realisasi: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(selectedAstapDetail.nilai_realisasi || 0).toLocaleString('id-ID')"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📜 Hak &amp; Sertifikat</span>
                                                    <span class="text-amber-300 font-bold block" x-text="selectedAstapDetail.spesifikasi_json?.hak_tanah || selectedAstapDetail.hak_tanah || 'Hak Pakai'"></span>
                                                    <span class="text-cyan-300 font-mono text-[10px] block truncate" x-text="selectedAstapDetail.spesifikasi_json?.sertifikat_no || selectedAstapDetail.sertifikat_no ? ('No: ' + (selectedAstapDetail.spesifikasi_json?.sertifikat_no || selectedAstapDetail.sertifikat_no)) : 'Tanpa No Sertifikat'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Luas &amp; Kondisi</span>
                                                    <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(selectedAstapDetail.spesifikasi_json?.luas_m2 || selectedAstapDetail.luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                                    <span class="text-emerald-300 font-semibold text-[10px]" x-text="'Kondisi: ' + (selectedAstapDetail.kondisi_barang || 'Baik')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Rincian Komponen Nilai</span>
                                                    <span class="text-slate-200 font-medium block text-[10px]" x-text="'Fisik: Rp ' + Number(selectedAstapDetail.spesifikasi_json?.nilai_fisik || selectedAstapDetail.nilai_realisasi || 0).toLocaleString('id-ID')"></span>
                                                    <span class="text-slate-400 text-[9px]" x-text="'Pln: ' + Number(selectedAstapDetail.spesifikasi_json?.nilai_perencanaan || 0).toLocaleString('id-ID') + ' • Was: ' + Number(selectedAstapDetail.spesifikasi_json?.nilai_pengawasan || 0).toLocaleString('id-ID')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Alamat Lokasi</span>
                                                    <span class="text-teal-300 font-medium block truncate" :title="selectedAstapDetail.alamat_barang" x-text="selectedAstapDetail.alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- 2. KIB B (PERALATAN & MESIN) & EXTRACOM -->
                            <template x-if="selectedAstapDetail.category === 'KIB B' || selectedAstapDetail.category === 'EXTRACOM'">
                                <div class="space-y-3">
                                    <!-- Multi-Item Repeater List jika ada mesin_items -->
                                    <template x-if="selectedAstapDetail.spesifikasi_json?.mesin_items && selectedAstapDetail.spesifikasi_json.mesin_items.length > 0">
                                        <div class="space-y-2.5">
                                            <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                                <span class="flex items-center gap-1.5 text-cyan-400">
                                                    📦 Rincian Barang Terdaftar (<span x-text="selectedAstapDetail.spesifikasi_json.mesin_items.length"></span> Item)
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Vol: ' + (selectedAstapDetail.jumlah_volume || selectedAstapDetail.spesifikasi_json.mesin_items.length) + ' ' + (selectedAstapDetail.satuan || 'Unit')"></span>
                                            </div>
                                            <template x-for="(mItem, mIdx) in selectedAstapDetail.spesifikasi_json.mesin_items" :key="mIdx">
                                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-cyan-500/30 space-y-2.5 shadow-sm hover:border-cyan-400/50 transition-all">
                                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <span class="px-2.5 py-0.5 rounded-lg bg-cyan-500/20 text-cyan-300 font-mono font-bold text-[11px] border border-cyan-500/30"
                                                                  x-text="'Item #' + (mIdx + 1)"></span>
                                                            <template x-if="(parseFloat(mItem.mesin_nilai_satuan) || 0) >= 300000">
                                                                <span class="px-2 py-0.5 rounded-lg bg-cyan-500/20 text-cyan-300 font-bold text-[10px] border border-cyan-500/40">
                                                                    ⚙️ KIB B (≥ Rp 300rb)
                                                                </span>
                                                            </template>
                                                            <template x-if="(parseFloat(mItem.mesin_nilai_satuan) || 0) < 300000">
                                                                <span class="px-2 py-0.5 rounded-lg bg-amber-500/20 text-amber-300 font-bold text-[10px] border border-amber-500/40">
                                                                    📦 EXTRACOM (&lt; Rp 300rb)
                                                                </span>
                                                            </template>
                                                            <span class="text-white font-bold text-xs" x-text="(mItem.mesin_merk || '-') + ' ' + (mItem.mesin_type || '')"></span>
                                                            <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                                  x-text="(mItem.mesin_jumlah_barang || 1) + ' ' + (mItem.mesin_satuan || 'Unit')"></span>
                                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold border"
                                                                  :class="{
                                                                      'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': (mItem.mesin_kondisi === 'Baik' || mItem.mesin_kondisi === 'B'),
                                                                      'bg-amber-500/20 text-amber-300 border-amber-500/30': (mItem.mesin_kondisi === 'Kurang Baik' || mItem.mesin_kondisi === 'KB'),
                                                                      'bg-rose-500/20 text-rose-300 border-rose-500/30': (mItem.mesin_kondisi === 'Rusak Berat' || mItem.mesin_kondisi === 'RB')
                                                                  }"
                                                                  x-text="'Kondisi: ' + (mItem.mesin_kondisi || 'Baik')"></span>
                                                        </div>
                                                        <div class="text-[11px] font-mono">
                                                            <span class="text-slate-400">Subtotal: </span>
                                                            <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(((parseFloat(mItem.mesin_jumlah_barang) || 1) * (parseFloat(mItem.mesin_nilai_satuan) || 0)) + (parseFloat(mItem.mesin_administrasi_proyek) || 0)).toLocaleString('id-ID')"></strong>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏷️ Merk / Type / Bahan</span>
                                                            <span class="text-cyan-300 font-bold block truncate" x-text="(mItem.mesin_merk || '-') + ' / ' + (mItem.mesin_type || '-')"></span>
                                                            <span class="text-slate-300 text-[10px]" x-text="'Bhn: ' + (mItem.mesin_bahan || '-') + ' • Uk: ' + (mItem.mesin_ukuran || '-')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🔢 Identitas / No. Seri</span>
                                                            <span class="text-cyan-300 font-mono font-semibold block truncate" x-text="'Pabrik: ' + (mItem.mesin_no_pabrik || '-')"></span>
                                                            <span class="text-slate-400 font-mono text-[9px] block truncate" x-text="'Rgk: ' + (mItem.mesin_no_rangka || '-') + ' • Msn: ' + (mItem.mesin_no_mesin || '-')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Satuan &amp; Adm</span>
                                                            <span class="text-emerald-300 font-medium block text-[10px]" x-text="'@ Rp ' + Number(mItem.mesin_nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                                            <span class="text-slate-400 text-[9px]" x-text="'Adm: Rp ' + Number(mItem.mesin_administrasi_proyek || 0).toLocaleString('id-ID')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏥 Ruang / Unit Pemegang</span>
                                                            <span class="text-amber-300 font-medium block truncate" :title="mItem.ruang_pemegang || mItem.ruang_pemegang_mesin" x-text="mItem.ruang_pemegang || mItem.ruang_pemegang_mesin || '-'"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Fallback jika data single item / legacy -->
                                    <template x-if="!selectedAstapDetail.spesifikasi_json?.mesin_items || selectedAstapDetail.spesifikasi_json.mesin_items.length === 0">
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
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 col-span-full">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏥 Ruang / Unit Pemegang</span>
                                                <span class="text-amber-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.ruang_pemegang || (selectedAstapDetail.registers && selectedAstapDetail.registers[0] ? selectedAstapDetail.registers[0].ruang_pemegang : '-')"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- 3. KIB C (GEDUNG & BANGUNAN) -->
                            <template x-if="selectedAstapDetail.category === 'KIB C'">
                                <div class="space-y-3">
                                    <!-- Jika Multi-Item Gedung -->
                                    <template x-if="selectedAstapDetail.spesifikasi_json?.gedung_items && selectedAstapDetail.spesifikasi_json.gedung_items.length > 0">
                                        <div class="space-y-2.5">
                                            <div class="flex items-center justify-between px-1">
                                                <span class="text-[11px] font-bold text-purple-300 uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>🏢 Rincian Gedung & Bangunan:</span>
                                                </span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30" 
                                                      x-text="selectedAstapDetail.spesifikasi_json.gedung_items.length + ' Gedung/Bangunan Terdaftar'"></span>
                                            </div>
                                            <template x-for="(gItem, gIdx) in selectedAstapDetail.spesifikasi_json.gedung_items" :key="gIdx">
                                                <div class="p-3 rounded-2xl bg-slate-900/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-2.5">
                                                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 pb-2">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="px-2 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40" x-text="'Gedung #' + (gIdx + 1)"></span>
                                                            <span class="text-xs font-bold text-white" x-text="gItem.gedung_nama_barang || selectedAstapDetail.nama_barang"></span>
                                                        </div>
                                                        <div class="flex items-center space-x-3 text-[10.5px] font-mono">
                                                            <span class="text-slate-400">Luas: <strong class="text-cyan-300" x-text="(gItem.gedung_luas_m2 || 0) + ' M²'"></strong></span>
                                                            <span class="text-slate-400">Kondisi: <strong class="text-white" x-text="gItem.gedung_kondisi || 'B'"></strong></span>
                                                            <span class="text-emerald-400 font-bold" x-text="'Rp ' + Number(Number(gItem.gedung_nilai_perencanaan || 0) + Number(gItem.gedung_nilai_fisik || 0) + Number(gItem.gedung_nilai_pengawasan || 0) + Number(gItem.gedung_nilai_pip || 0)).toLocaleString('id-ID')"></span>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏢 Konstruksi</span>
                                                            <span class="text-purple-300 font-bold block" x-text="(gItem.gedung_bertingkat || 'Bertingkat') + ' • ' + (gItem.gedung_beton || 'Beton')"></span>
                                                            <span class="text-slate-300 text-[9.5px]" x-text="'Bangunan: ' + (gItem.gedung_is_baru === 'Baru' ? 'Baru (1)' : 'Lama (-)')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status & Kode Tanah</span>
                                                            <span class="text-teal-300 font-semibold block truncate" x-text="gItem.gedung_status_tanah || 'Hak Pakai RSUD'"></span>
                                                            <span class="text-cyan-400 font-mono text-[9px] block truncate" x-text="gItem.gedung_kode_aset_tanah || '1.3.1.01.01.02.013'"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Komponen Nilai</span>
                                                            <span class="text-slate-300 block text-[9.5px]" x-text="'Fisik: Rp ' + Number(gItem.gedung_nilai_fisik || 0).toLocaleString('id-ID')"></span>
                                                            <span class="text-slate-400 text-[9px]" x-text="'Pln: ' + Number(gItem.gedung_nilai_perencanaan || 0).toLocaleString('id-ID') + ' • Pws: ' + Number(gItem.gedung_nilai_pengawasan || 0).toLocaleString('id-ID')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Lokasi Fisik</span>
                                                            <span class="text-emerald-300 font-medium block truncate" :title="gItem.gedung_alamat" x-text="gItem.gedung_alamat || selectedAstapDetail.alamat_barang || '-'"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Fallback jika data single item / legacy -->
                                    <template x-if="!selectedAstapDetail.spesifikasi_json?.gedung_items || selectedAstapDetail.spesifikasi_json.gedung_items.length === 0">
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
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 text-[11px]">
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
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor SP2D:</span>
                                    <span class="text-teal-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.sp2d_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor BAST:</span>
                                    <span class="text-rose-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.bast_nomor || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Pihak Penyedia & Pejabat Pembuat Komitmen (PPK) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <h4 class="text-xs font-extrabold text-teal-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>🏢 Pihak Penyedia (Rekanan) &amp; Pejabat Pembuat Komitmen (PPK)</span>
                                </h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400">Langkah 4 - Rekanan &amp; PPK</span>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 text-[11px]">
                                <!-- Card Pihak Penyedia -->
                                <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-2.5">
                                    <span class="text-[10.5px] font-extrabold text-cyan-400 uppercase tracking-wider block flex items-center space-x-1">
                                        <span>🏢 Informasi Rekanan / Vendor</span>
                                    </span>
                                    <div class="space-y-1.5 divide-y divide-slate-800/60">
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-slate-400">Nama Perusahaan / Rekanan:</span>
                                            <strong class="text-white font-bold truncate max-w-[55%]" :title="selectedAstapDetail.penyedia_nama" x-text="selectedAstapDetail.penyedia_nama || '-'"></strong>
                                        </div>
                                        <div class="flex items-center justify-between pt-1.5">
                                            <span class="text-slate-400">Nama Pimpinan / Pemilik:</span>
                                            <span class="text-slate-200 font-medium truncate max-w-[55%]" :title="selectedAstapDetail.penyedia_pemilik" x-text="selectedAstapDetail.penyedia_pemilik || '-'"></span>
                                        </div>
                                        <template x-if="selectedAstapDetail.category === 'EXTRACOM' || selectedAstapDetail.category === 'ATB' || selectedAstapDetail.penyedia_telepon">
                                            <div class="flex items-center justify-between pt-1.5">
                                                <span class="text-slate-400">No. HP / WhatsApp Aktif:</span>
                                                <span class="text-amber-400 font-mono font-bold truncate max-w-[55%] flex items-center gap-1.5">
                                                    <span class="text-xs">📱</span>
                                                    <span x-text="selectedAstapDetail.penyedia_telepon || selectedAstapDetail.spesifikasi_json?.penyedia_telepon || selectedAstapDetail.spesifikasi_json?.penyedia_kontak || '-'"></span>
                                                </span>
                                            </div>
                                        </template>
                                        <div class="flex items-center justify-between pt-1.5">
                                            <span class="text-slate-400">Rekening Bank:</span>
                                            <span class="text-amber-300 font-mono font-bold truncate max-w-[55%]" x-text="selectedAstapDetail.penyedia_rekening_nomor ? ((selectedAstapDetail.penyedia_rekening_nama ? selectedAstapDetail.penyedia_rekening_nama + ' - ' : '') + selectedAstapDetail.penyedia_rekening_nomor) : (selectedAstapDetail.penyedia_rekening_nama || '-')"></span>
                                        </div>
                                        <div class="flex items-start justify-between pt-1.5">
                                            <span class="text-slate-400 shrink-0">Alamat Perusahaan:</span>
                                            <span class="text-teal-300 font-medium text-right truncate max-w-[55%]" :title="selectedAstapDetail.penyedia_alamat" x-text="selectedAstapDetail.penyedia_alamat || '-'"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card PPK -->
                                <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-2.5">
                                    <span class="text-[10.5px] font-extrabold text-amber-400 uppercase tracking-wider block flex items-center space-x-1">
                                        <span>👔 Pejabat Pembuat Komitmen (PPK)</span>
                                    </span>
                                    <div class="space-y-1.5 divide-y divide-slate-800/60">
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-slate-400">Nama Pejabat (PPK):</span>
                                            <strong class="text-white font-bold truncate max-w-[55%]" :title="selectedAstapDetail.ppk_nama" x-text="selectedAstapDetail.ppk_nama || '-'"></strong>
                                        </div>
                                        <div class="flex items-center justify-between pt-1.5">
                                            <span class="text-slate-400">NIP Pejabat (PPK):</span>
                                            <span class="text-cyan-300 font-mono font-bold truncate max-w-[55%]" x-text="selectedAstapDetail.ppk_nip || '-'"></span>
                                        </div>
                                        <div class="flex items-start justify-between pt-1.5">
                                            <span class="text-slate-400 shrink-0">Keterangan / Catatan:</span>
                                            <span class="text-slate-300 italic text-right truncate max-w-[55%]" :title="selectedAstapDetail.keterangan_tambahan || selectedAstapDetail.keterangan" x-text="selectedAstapDetail.keterangan_tambahan || selectedAstapDetail.keterangan || '-'"></span>
                                        </div>
                                    </div>
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
                                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                    <button type="button" @click="resequenceSingleAstap(selectedAstapDetail)"
                                        class="px-2.5 py-1 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-[10.5px] font-bold transition-all flex items-center space-x-1 cursor-pointer shadow-sm active:scale-95"
                                        title="Urutkan ulang register NIBAR barang ini agar berurutan tanpa celah">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>🔄 Rapikan NIBAR Barang Ini</span>
                                    </button>
                                    @endif
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
                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
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
                                                              'bg-amber-500/20 text-amber-300 border-amber-500/30': reg.kondisi === 'Kurang Baik',
                                                              'bg-rose-500/20 text-rose-300 border-rose-500/30': reg.kondisi === 'Rusak Berat'
                                                          }" x-text="reg.kondisi"></span>
                                                </td>
                                                <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                    <button type="button" @click.stop="downloadQrCodeNibar(reg, selectedAstapDetail)"
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
                                                        <button type="button" @click.stop="openRiwayatModal(reg)" title="Cek Riwayat Mutasi Unit Ini"
                                                                class="p-1.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-400 hover:text-purple-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </button>

                                                        <!-- 2. Tombol Ubah Kondisi Barang (Modal Khusus) -->
                                                        <button type="button" @click.stop="openEditKondisiModal(reg)" title="Ubah Kondisi Barang Unit Ini"
                                                                class="p-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 hover:text-amber-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L20.586 7a2 2 0 010 2.828l-8.586 8.586z"/></svg>
                                                        </button>

                                                        <!-- 3. Tombol Hapus Register -->
                                                        <button type="button" @click.stop="deleteRegister(reg)" title="Hapus Unit Register Ini"
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
        <div x-show="showQrModal" x-cloak @click.self="showQrModal = false" class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999;">
            <div class="border border-emerald-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl text-center space-y-5 max-h-[90vh] overflow-y-auto my-auto" style="background-color: #0f172a;">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">📱</span>
                        <h3 class="text-base font-extrabold text-white">Label QR Code Aset ASTAP</h3>
                    </div>
                    <button type="button" @click.stop="showQrModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
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
                                <span class="text-white font-bold text-right max-w-[200px] truncate" x-text="selectedQrItem.nama_barang"></span>
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
                                <span>Unduh QR</span>
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
        <div x-show="showEditKondisiModal" x-cloak @click.self="showEditKondisiModal = false" class="fixed inset-0 flex items-center justify-center p-4" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999;">
            <div class="border border-amber-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" style="background-color: #0f172a;">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2">
                        <span class="text-lg">⚙️</span>
                        <h3 class="text-base font-extrabold text-white">Ubah Kondisi Unit Barang</h3>
                    </div>
                    <button type="button" @click.stop="showEditKondisiModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <template x-if="editingRegisterItem">
                    <div class="space-y-4 text-xs">
                        <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 space-y-1">
                            <span class="text-slate-400 text-[10px] uppercase font-bold block">Target Unit NIBAR:</span>
                            <p class="text-emerald-400 font-mono font-bold text-sm" x-text="editingRegisterItem.nibar || editingRegisterItem.no_register"></p>
                            <p class="text-slate-300 font-semibold text-[11px]" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : ''"></p>
                        </div>

                        <!-- Pilihan Kondisi -->
                        <div class="space-y-2">
                            <label class="font-bold text-slate-300 text-xs">Pilih Kondisi Terkini Unit:</label>
                            <div class="space-y-2">
                                <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                    :class="newKondisiValue === 'Baik' ? 'bg-emerald-500/15 border-emerald-500/50 text-emerald-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                    <input type="radio" value="Baik" x-model="newKondisiValue" class="text-emerald-500 focus:ring-0">
                                    <div>
                                        <span class="font-extrabold text-xs block text-emerald-300">🟢 Baik (B)</span>
                                        <span class="text-[10px] text-slate-400 block">Unit berfungsi sempurna dan siap digunakan.</span>
                                    </div>
                                </label>

                                <label class="flex items-center space-x-3 p-3 rounded-2xl border cursor-pointer transition-all"
                                    :class="newKondisiValue === 'Kurang Baik' ? 'bg-amber-500/15 border-amber-500/50 text-amber-300' : 'bg-slate-950/60 border-slate-800 text-slate-400 hover:bg-slate-800/40'">
                                    <input type="radio" value="Kurang Baik" x-model="newKondisiValue" class="text-amber-500 focus:ring-0">
                                    <div>
                                        <span class="font-extrabold text-xs block text-amber-300">🟡 Kurang Baik (KB)</span>
                                        <span class="text-[10px] text-slate-400 block">Ada kendala kecil / penurunan performa namun masih dapat difungsikan.</span>
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
                            <button type="button" @click.stop="showEditKondisiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold hover:bg-slate-700 cursor-pointer">Batal</button>
                            <button type="button" @click.stop="saveKondisiChange()" :disabled="isSavingKondisi" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold shadow-lg shadow-amber-500/20 active:scale-95 disabled:opacity-50 cursor-pointer">
                                <span x-text="isSavingKondisi ? 'Menyimpan...' : 'Simpan Kondisi'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- FRONTEND MODAL: CEK RIWAYAT MUTASI REGISTER NIBAR -->
        <div x-show="showRiwayatModal" x-cloak @click.self="showRiwayatModal = false" class="fixed inset-0 flex items-center justify-center p-4" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999;">
            <div class="border border-purple-500/40 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-4 max-h-[88vh] overflow-y-auto" style="background-color: #0f172a;">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-purple-500/20 border border-purple-500/30 flex items-center justify-center text-purple-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Riwayat Mutasi Unit Barang</h3>
                            <p class="text-[11px] text-slate-400">Histori pergerakan, tanggal, kondisi saat mutasi, dan alasan mutasi</p>
                        </div>
                    </div>
                    <button type="button" @click.stop="showRiwayatModal = false" class="text-slate-500 hover:text-white text-xl font-bold p-1 cursor-pointer">&times;</button>
                </div>

                <template x-if="selectedRiwayatRegister">
                    <div class="space-y-4 text-xs">
                        <!-- Info Register Card -->
                        <div class="p-3.5 rounded-2xl border border-slate-800 flex items-center justify-between gap-3" style="background-color: #020617;">
                            <div class="space-y-0.5 min-w-0 flex-1">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block truncate" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : 'Unit Barang'"></span>
                                <div class="flex items-center space-x-2 flex-wrap gap-y-1">
                                    <span class="text-purple-300 font-mono font-bold text-xs" x-text="selectedRiwayatRegister.nibar || selectedRiwayatRegister.no_register"></span>
                                    <span class="text-slate-500 text-[11px]">•</span>
                                    <span class="text-cyan-400 text-[11px] font-semibold truncate" x-text="selectedRiwayatRegister.ruang_pemegang || 'Gudang Aset'"></span>
                                </div>
                            </div>
                            <div class="shrink-0 text-right">
                                <span class="text-[9px] uppercase font-bold text-slate-500 block mb-0.5">Kondisi Sekarang</span>
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold border inline-block"
                                      :class="{
                                          'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': selectedRiwayatRegister.kondisi === 'Baik',
                                          'bg-amber-500/20 text-amber-300 border-amber-500/30': selectedRiwayatRegister.kondisi === 'Kurang Baik',
                                          'bg-rose-500/20 text-rose-300 border-rose-500/30': selectedRiwayatRegister.kondisi === 'Rusak Berat'
                                      }" x-text="selectedRiwayatRegister.kondisi"></span>
                            </div>
                        </div>

                        <!-- Loading State -->
                        <template x-if="isLoadingRiwayat">
                            <div class="py-8 text-center space-y-2">
                                <div class="inline-block w-6 h-6 border-2 border-purple-500 border-t-transparent rounded-full animate-spin"></div>
                                <p class="text-xs text-slate-400">Memuat riwayat mutasi barang...</p>
                            </div>
                        </template>

                        <!-- Empty State: Belum Pernah Mutasi -->
                        <template x-if="!isLoadingRiwayat && (!selectedRiwayatMutasis || selectedRiwayatMutasis.length === 0)">
                            <div class="p-6 text-center rounded-2xl border border-dashed border-slate-800 space-y-2" style="background-color: #020617;">
                                <span class="text-3xl block">📦</span>
                                <h4 class="text-sm font-bold text-slate-200">Belum Ada Riwayat Mutasi</h4>
                                <p class="text-xs text-slate-400 max-w-sm mx-auto leading-relaxed">
                                    Unit register ini belum pernah dimutasi ke ruangan atau unit lain. Unit saat ini berada di lokasi penempatan: <strong class="text-slate-300" x-text="selectedRiwayatRegister.ruang_pemegang || 'Gudang Aset'"></strong> dengan kondisi <strong class="text-emerald-400" x-text="selectedRiwayatRegister.kondisi"></strong>.
                                </p>
                            </div>
                        </template>

                        <!-- List Riwayat Mutasi Timeline -->
                        <template x-if="!isLoadingRiwayat && selectedRiwayatMutasis && selectedRiwayatMutasis.length > 0">
                            <div class="space-y-3 relative pl-4 border-l-2 border-purple-500/30 my-2">
                                <template x-for="(m, idx) in selectedRiwayatMutasis" :key="m.id || idx">
                                    <div class="relative group">
                                        <!-- Timeline dot -->
                                        <div class="absolute -left-[21px] top-2 w-2.5 h-2.5 rounded-full bg-purple-500 ring-4 ring-slate-900"></div>
                                        
                                        <div class="p-4 rounded-2xl border border-slate-800 space-y-2.5 hover:border-slate-700 transition-colors" style="background-color: #020617;">
                                            <!-- Row 1: Tanggal Mutasi, Jenis Mutasi, & Kondisi Saat Itu -->
                                            <div class="flex flex-wrap items-center justify-between gap-2">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-xs font-mono font-extrabold text-white flex items-center space-x-1">
                                                        <span>📅</span>
                                                        <span x-text="m.tanggal_mutasi"></span>
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30" x-text="m.jenis_mutasi || 'Mutasi'"></span>
                                                    <span class="text-[10px] font-mono text-slate-400" x-text="'(' + (m.nomor_bamb || 'BAMB') + ')'"></span>
                                                </div>

                                                <!-- KONDISI SAAT ITU (HIGHLIGHTED) -->
                                                <div class="flex items-center space-x-1.5">
                                                    <span class="text-[10px] text-slate-400 font-bold uppercase">Kondisi saat mutasi:</span>
                                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black border"
                                                          :class="{
                                                              'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': m.kondisi === 'Baik',
                                                              'bg-amber-500/20 text-amber-300 border-amber-500/30': m.kondisi === 'Kurang Baik',
                                                              'bg-rose-500/20 text-rose-300 border-rose-500/30': m.kondisi === 'Rusak Berat'
                                                          }" x-text="m.kondisi || 'Baik'"></span>
                                                </div>
                                            </div>

                                            <!-- Row 2: Alur Perpindahan Ruangan -->
                                            <div class="p-2.5 bg-slate-900/80 rounded-xl border border-slate-800/80 flex items-center justify-between text-xs">
                                                <div class="flex items-center space-x-2 min-w-0">
                                                    <div class="text-slate-300">
                                                        <span class="text-[9.5px] uppercase font-bold text-slate-500 block">Dari Ruangan:</span>
                                                        <span class="font-semibold text-slate-200" x-text="m.ruangan_asal"></span>
                                                    </div>
                                                    <span class="text-purple-400 font-extrabold text-sm px-1">➔</span>
                                                    <div class="text-cyan-300">
                                                        <span class="text-[9.5px] uppercase font-bold text-slate-500 block">Ke Ruangan:</span>
                                                        <span class="font-bold text-cyan-300" x-text="m.ruangan_tujuan"></span>
                                                    </div>
                                                </div>
                                                <span class="px-2 py-0.5 rounded-full text-[9px] font-extrabold border"
                                                      :class="m.status && m.status.includes('Disetujui') ? 'bg-emerald-500/10 text-emerald-300 border-emerald-500/30' : (m.status && m.status.includes('Ditolak') ? 'bg-rose-500/10 text-rose-300 border-rose-500/30' : 'bg-cyan-500/10 text-cyan-300 border-cyan-500/30')"
                                                      x-text="m.status || 'Tercatat'"></span>
                                            </div>

                                            <!-- Row 3: ALASAN MUTASI (CLEAR & PROMINENT) -->
                                            <div class="p-3 bg-slate-900/95 rounded-xl border border-amber-500/20 space-y-1">
                                                <div class="flex items-center space-x-1.5 text-amber-400">
                                                    <span class="text-xs">📝</span>
                                                    <span class="text-[10px] font-extrabold uppercase tracking-wider">Alasan Mutasi:</span>
                                                </div>
                                                <p class="text-xs text-slate-200 leading-relaxed italic pl-1" x-text="m.alasan_mutasi || 'Tidak ada alasan khusus dicatat'"></p>
                                            </div>

                                            <!-- Row 4: Info Penanggung Jawab -->
                                            <div class="flex flex-wrap items-center justify-between text-[10px] text-slate-400 pt-0.5 px-1 border-t border-slate-800/60">
                                                <span>Pengirim: <strong class="text-slate-300 font-semibold" x-text="m.penanggung_jawab_asal || '-'"></strong></span>
                                                <span>Penerima: <strong class="text-slate-300 font-semibold" x-text="m.penanggung_jawab_tujuan || '-'"></strong></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <div class="pt-3 border-t border-slate-800 flex justify-end">
                            <button type="button" @click.stop="showRiwayatModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs cursor-pointer">
                                Tutup Riwayat
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- GLOBAL CUSTOM CONFIRMATION DIALOG MODAL (Sleek Dark Theme) -->
        <div x-show="showConfirmModal" x-cloak @click.self="showConfirmModal = false" class="fixed inset-0 flex items-center justify-center p-4" style="background-color: rgba(2, 6, 23, 0.9); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 10000;">
            <div x-show="showConfirmModal"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="border rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative"
                 style="background-color: #0f172a;"
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

        <!-- ========================================================================= -->
        <!-- MODAL PILIH TAHUN & TRIWULAN UNTUK EKSPOR EXCEL                           -->
        <!-- ========================================================================= -->
        <div x-show="showExportModal" x-cloak
             class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            <div @click.away="showExportModal = false"
                 class="bg-slate-900 border border-slate-700/80 rounded-3xl p-6 max-w-lg w-full shadow-2xl space-y-5"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xl">
                            📊
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Ekspor Laporan ASTAP</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Pilih Tahun &amp; Triwulan pengadaan untuk format Excel resmi.</p>
                        </div>
                    </div>
                    <button type="button" @click="showExportModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <!-- Form Filter Periode Ekspor -->
                <div class="space-y-4">
                    <!-- 1. Pilihan Tahun Anggaran -->
                    <div>
                        <label class="block text-slate-300 font-bold text-xs mb-1.5 flex items-center justify-between">
                            <span>📅 TAHUN ANGGARAN</span>
                            <span class="text-[10px] text-slate-400">Periode Pelaporan</span>
                        </label>
                        <select x-model="exportYear"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-white focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua Tahun (Seluruh Riwayat Aset 1980 - Sekarang)</option>
                            <template x-for="yr in availableYears" :key="yr">
                                <option :value="yr" x-text="'Tahun Anggaran ' + yr"></option>
                            </template>
                        </select>
                    </div>

                    <!-- 2. Pilihan Triwulan Pengadaan -->
                    <div>
                        <label class="block text-cyan-300 font-bold text-xs mb-1.5 flex items-center justify-between">
                            <span>📊 TRIWULAN PENGADAAN (BAST)</span>
                            <span class="text-[10px] text-cyan-400/80 font-mono">TW I - IV</span>
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="exportTriwulan = 'all'"
                                class="p-2.5 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between"
                                :class="exportTriwulan === 'all' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/60 shadow-lg' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>📑 Semua (Tahunan)</span>
                                <span x-show="exportTriwulan === 'all'" class="text-cyan-400 font-black">✓</span>
                            </button>
                            <button type="button" @click="exportTriwulan = 'TW I'"
                                class="p-2.5 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between"
                                :class="exportTriwulan === 'TW I' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/60 shadow-lg' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>🌱 Triwulan I (TW I)</span>
                                <span x-show="exportTriwulan === 'TW I'" class="text-emerald-400 font-black">✓</span>
                            </button>
                            <button type="button" @click="exportTriwulan = 'TW II'"
                                class="p-2.5 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between"
                                :class="exportTriwulan === 'TW II' ? 'bg-blue-500/20 text-blue-300 border-blue-500/60 shadow-lg' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>☀️ Triwulan II (TW II)</span>
                                <span x-show="exportTriwulan === 'TW II'" class="text-blue-400 font-black">✓</span>
                            </button>
                            <button type="button" @click="exportTriwulan = 'TW III'"
                                class="p-2.5 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between"
                                :class="exportTriwulan === 'TW III' ? 'bg-amber-500/20 text-amber-300 border-amber-500/60 shadow-lg' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>🍂 Triwulan III (TW III)</span>
                                <span x-show="exportTriwulan === 'TW III'" class="text-amber-400 font-black">✓</span>
                            </button>
                            <button type="button" @click="exportTriwulan = 'TW IV'"
                                class="col-span-2 p-2.5 rounded-xl border text-xs font-bold transition-all text-left flex items-center justify-between"
                                :class="exportTriwulan === 'TW IV' ? 'bg-purple-500/20 text-purple-300 border-purple-500/60 shadow-lg' : 'bg-slate-950/60 text-slate-400 border-slate-800 hover:bg-slate-950'">
                                <span>❄️ Triwulan IV (TW IV - Akhir Tahun)</span>
                                <span x-show="exportTriwulan === 'TW IV'" class="text-purple-400 font-black">✓</span>
                            </button>
                        </div>
                    </div>

                    <!-- 3. Pilihan Kategori KIB -->
                    <div>
                        <label class="block text-slate-300 font-bold text-xs mb-1.5 flex items-center justify-between">
                            <span>📦 KLASIFIKASI KIB</span>
                            <span class="text-[10px] text-slate-400">Sheet Excel</span>
                        </label>
                        <select x-model="exportCategory"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-semibold text-slate-200 focus:outline-none focus:border-emerald-500">
                            <option value="all">Semua KIB (Buku Aset Lengkap 9 Sheet)</option>
                            <option value="KIB A">KIB A - Tanah</option>
                            <option value="KIB B">KIB B - Peralatan &amp; Mesin</option>
                            <option value="KIB C">KIB C - Gedung &amp; Bangunan</option>
                            <option value="KIB D">KIB D - Jalan &amp; Jaringan</option>
                            <option value="KIB E">KIB E - Aset Tetap Lainnya</option>
                            <option value="KIB F">KIB F - Konstruksi KDP</option>
                            <option value="ATB">ATB - Aset Tidak Berwujud</option>
                            <option value="EXTRACOM">Extracom</option>
                        </select>
                    </div>

                    <!-- Info Ringkasan Data -->
                    <div class="p-3 bg-slate-950/80 border border-emerald-500/30 rounded-2xl flex items-center justify-between">
                        <div class="flex items-center space-x-2 text-xs">
                            <span class="text-emerald-400 text-base">📋</span>
                            <span class="text-slate-300">Aset siap diekspor:</span>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-xs border border-emerald-500/40"
                              x-text="exportFilteredCount + ' Item Data'"></span>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showExportModal = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all">
                        Batal
                    </button>
                    <button type="button" @click="submitExport()"
                        :disabled="isSubmittingExport"
                        class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-black text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 cursor-pointer active:scale-95 disabled:opacity-50">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-text="isSubmittingExport ? 'Mengekspor...' : 'Unduh File Excel'"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL DIALOG: RAPIKAN & URUTKAN ULANG NIBAR (AUTO-RESEQUENCE)             -->
        <!-- ========================================================================= -->
        <div x-show="showResequenceModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="showResequenceModal = false"
                 class="bg-slate-900 border border-amber-500/50 rounded-3xl p-6 max-w-xl w-full shadow-2xl space-y-5"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-amber-500/20 text-amber-300 border border-amber-500/30 text-2xl shadow-md">
                            🔄
                        </div>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-white">Rapikan &amp; Urutkan Ulang NIBAR</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Menyusun ulang nomor register agar berurutan rapi tanpa ada nomor yang loncat/kosong.</p>
                        </div>
                    </div>
                    <button type="button" @click="showResequenceModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg text-lg font-bold cursor-pointer">&times;</button>
                </div>

                <!-- Alert Penjelasan & Peringatan -->
                <div class="space-y-3">
                    <div class="p-4 rounded-2xl bg-cyan-950/40 border border-cyan-500/40 space-y-2">
                        <div class="flex items-center space-x-2 text-cyan-300 font-bold text-xs">
                            <span>ℹ️</span>
                            <span>Bagaimana Fitur Ini Bekerja?</span>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Ketika ada data barang yang <strong>dihapus</strong> atau <strong>diubah namanya</strong>, nomor register unit setelahnya bisa menjadi tidak berurutan (misal: unit 1-5 dihapus, sehingga unit selanjutnya tetap bernomor 0000006). 
                            Fitur ini akan memindai seluruh aset dan <strong>merapatkan kembali urutan NIBAR</strong> dari nomor <strong class="text-cyan-300 font-mono">0000001</strong> secara berurutan berdasarkan urutan Triwulan (TW I ➔ TW IV) dan tanggal pendaftaran.
                        </p>
                    </div>

                    <div class="p-4 rounded-2xl bg-amber-950/40 border border-amber-500/50 space-y-2">
                        <div class="flex items-center space-x-2 text-amber-300 font-bold text-xs">
                            <span>⚠️</span>
                            <span>Peringatan Penting Sebelum Melanjutkan:</span>
                        </div>
                        <ul class="text-[11px] text-amber-200/90 list-disc list-inside space-y-1 leading-relaxed">
                            <li>Kode 45-digit NIBAR dan URL QR Code pada database akan disesuaikan ke nomor urut baru.</li>
                            <li>Jika stiker QR Code fisik pada unit barang <strong>sudah pernah dicetak/ditempel</strong>, pastikan untuk <strong>mencetak ulang label QR Code</strong> yang baru agar sinkron dengan sistem.</li>
                        </ul>
                    </div>
                </div>

                <!-- Form Filter Scope Resequence -->
                <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3.5">
                    <span class="text-xs font-extrabold text-slate-200 block uppercase tracking-wider">🎯 Pilih Lingkup Data yang Ingin Dibereskan:</span>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <!-- 1. Pilihan Tahun Anggaran -->
                        <div>
                            <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📅 Tahun Perolehan</label>
                            <select x-model="resequenceYear"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-amber-500">
                                <option value="all">Semua Tahun (Seluruh Database)</option>
                                <template x-for="yr in availableYears" :key="yr">
                                    <option :value="yr" x-text="'Tahun Anggaran ' + yr"></option>
                                </template>
                            </select>
                        </div>

                        <!-- 2. Pilihan Kategori KIB -->
                        <div>
                            <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📦 Klasifikasi Aset (KIB)</label>
                            <select x-model="resequenceCategory"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-amber-500">
                                <option value="all">Semua Kategori (KIB A s/d ATB)</option>
                                <option value="KIB A">KIB A - Tanah</option>
                                <option value="KIB B">KIB B - Peralatan &amp; Mesin</option>
                                <option value="KIB C">KIB C - Gedung &amp; Bangunan</option>
                                <option value="KIB D">KIB D - Jalan &amp; Jaringan</option>
                                <option value="KIB E">KIB E - Aset Tetap Lainnya</option>
                                <option value="KIB F">KIB F - Konstruksi KDP</option>
                                <option value="ATB">ATB - Aset Tidak Berwujud</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer Actions -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showResequenceModal = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="submitResequence()"
                        :disabled="isSubmittingResequence"
                        class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-2 cursor-pointer active:scale-95 disabled:opacity-50">
                        <svg class="w-4 h-4 text-slate-950" :class="{ 'animate-spin': isSubmittingResequence }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        <span x-text="isSubmittingResequence ? 'Sedang Merapikan NIBAR...' : '⚡ Ya, Rapikan NIBAR Sekarang'"></span>
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
