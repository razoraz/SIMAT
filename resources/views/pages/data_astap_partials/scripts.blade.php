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

    function resolveItemCategory(item) {
        if (!item) return 'KIB B';
        const isExtracom = !!item.is_extracomtable || (item.category && item.category.toUpperCase() === 'EXTRACOM');
        if (isExtracom) return 'EXTRACOM';
        if (item.category) {
            const cUpper = item.category.toUpperCase().trim();
            if (cUpper === 'KIB A' || cUpper === 'A') return 'KIB A';
            if (cUpper === 'KIB B' || cUpper === 'B') return 'KIB B';
            if (cUpper === 'KIB C' || cUpper === 'C') return 'KIB C';
            if (cUpper === 'KIB D' || cUpper === 'D') return 'KIB D';
            if (cUpper === 'KIB E' || cUpper === 'E') return 'KIB E';
            if (cUpper === 'KIB F' || cUpper === 'F') return 'KIB F';
            if (cUpper === 'ATB') return 'ATB';
            if (cUpper === 'EXTRACOM') return 'EXTRACOM';
        }
        const kode = item.jenis_aset_kode || item.kode_barang || '';
        if (kode.startsWith('1.3.1')) return 'KIB A';
        if (kode.startsWith('1.3.3')) return 'KIB C';
        if (kode.startsWith('1.3.4')) return 'KIB D';
        if (kode.startsWith('1.3.5')) return 'KIB E';
        if (kode.startsWith('1.3.6')) return 'KIB F';
        if (kode.startsWith('1.5.3')) return 'ATB';
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
        ], 55, kibCTitleRows.length, kibCRows.length);

        applyUnified4StepMasterSheetStyling(wsKibC, kibCRows.length, 55, 30, kibCTitleRows.length);
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
        ], 54, kibDTitleRows.length, kibDRows.length);

        applyUnified4StepMasterSheetStyling(wsKibD, kibDRows.length, 54, 30, kibDTitleRows.length);
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
                    border = null;
                    fill = "FFFFFF";
                    fontSize = 10;
                    align = "center";
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
    function applyDaftarAtReportStyling(ws, rowCount, colCount = 19, headerStartRow = 5, headerRowCount = 4, totalRowIndex = -1) {
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

        const dataCellBorder = {
            top: { style: "thin", color: { rgb: "94A3B8" } },
            bottom: { style: "dashed", color: { rgb: "94A3B8" } },
            left: { style: "thin", color: { rgb: "94A3B8" } },
            right: { style: "thin", color: { rgb: "94A3B8" } }
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
                    fontSize = (r === 0 || r === 1 || r === 2) ? 11 : 10.5;
                    align = "center";
                    border = null;
                }
                // 2. BARIS SUMBER DANA (Row 4) -> SUMBER DANA : KELOMPOK ANGGARAN (BLUD)
                else if (r === 4) {
                    fill = "FFFFFF";
                    fontColor = "000000";
                    bold = true;
                    fontSize = 10;
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
                // 5. BARIS TOTAL AKHIR (JUMLAH TOTAL)
                else if (r === totalRowIndex) {
                    fill = "E2EFDA"; // Light green highlight for total row
                    fontColor = "000000";
                    bold = true;
                    fontSize = 10;
                    border = totalRowBorder;

                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = "#,##0";
                    } else {
                        align = "center";
                    }
                }
                // 6. BARIS DATA UTAMA (Row 9 s/d totalRowIndex - 1)
                else if (r > (headerStartRow + headerRowCount - 1) && r < totalRowIndex) {
                    fill = (r % 2 === 0) ? "FFFFFF" : "F9FAFB";
                    fontColor = "0F172A";
                    fontSize = 9.5;
                    border = dataCellBorder;

                    // Format angka & penataan posisi isi sel
                    if (typeof cell.v === 'number') {
                        align = "right";
                        numFmt = "#,##0";
                    } else if (c === 0 || c === 1 || c === 5 || c === 7 || c === 8 || c === 10 || c === 11 || c === 15 || c === 16) {
                        align = "center"; // Kolom Kode, Nomor, Tanggal, Satuan -> Center
                    } else {
                        align = "left"; // Kolom Nama Barang, Rekening, Spesifikasi, Lokasi, Keterangan -> Left
                    }
                }
                // 7. AREA TANDA TANGAN (Row > totalRowIndex)
                else if (r > totalRowIndex) {
                    border = null;
                    fill = "FFFFFF";
                    fontColor = "0F172A";
                    fontSize = 10;
                    align = "center";
                    if (cell.v && (cell.v.includes("PPK") || cell.v.includes("Pengurus Barang") || cell.v.includes("19780101") || cell.v.includes("19850615"))) {
                        bold = true;
                    }
                }

                cell.s = {
                    font: { name: "Calibri", sz: fontSize, bold: bold, italic: italic, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: "center", wrapText: true },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt) cell.z = numFmt;
            }
        }
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

            // Baris 4: Sumber Dana Sesuai Format Resmi
            ["SUMBER DANA", ": KELOMPOK ANGGARAN  (BLUD)"],

            // Baris 5 (Header Level 1 - Superheader)
            // Kolom 19 (KET.) Berdiri Sendiri di Baris 5-7, tidak masuk dalam RINCIAN ASET INVENTARIS
            [
                "NO",                           // Col 0 (No 1) - Merged Row 5 s/d 7 (Berdiri Sendiri)
                superHeaderBelanja, "", "", "", // Col 1-4 (No 2-5: BELANJA MODAL)
                superHeaderRincian, "", "", "", "", "", "", "", "", "", "", "", // Col 5-17 (No 6-18: RINCIAN ASET INVENTARIS... s/d LOKASI BARANG)
                "KET."                          // Col 18 (No 19: KET.) - Merged Row 5 s/d 7 (BERDIRI SENDIRI!)
            ],

            // Baris 6 (Header Level 2 - Nama Kolom Utama & Sub-Superheader)
            [
                "", // Merged with NO (col 0)
                "No Rek. Bel. Modal",
                "RINCIAN BELANJA MODAL",
                "JUMLAH ANGGARAN\n(Rp)",
                "JUMLAH REALISASI\nSPM (Rp)",
                "No Rek. Menurut\nPERMENDAGRI 108/2016",
                "NAMA ASET INVENTARIS",
                "MERK",
                "TYPE",
                "NO PABRIK/NO\nCHASIS/NO MESIN",
                "VOLUME", "", // Kolom 10-11: Merged horizontal VOLUME
                "NILAI\nPEROLEHAN (Rp)",
                "ADMINISTRASI PROYEK\n(Pengawasan, Perencanaan,\nAP) (Rp)",
                "NILAI ASET (Rp)",
                "BUKTI PENGADAAN", "", // Kolom 15-16: Merged horizontal BUKTI PENGADAAN
                "LOKASI BARANG",
                "" // Merged with KET. from row 5 (col 18)
            ],

            // Baris 7 (Header Level 3 - Sub Kolom VOLUME & BUKTI PENGADAAN)
            [
                "", "", "", "", "", "", "", "", "", "",
                "JUMLAH\nBARANG",
                "NAMA\nSATUAN\nBARANG",
                "", "", "",
                "NOMOR",
                "TANGGAL",
                "",
                "" // Merged with KET. from row 5 (col 18)
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

        filteredAstaps.forEach((item, idx) => {
            const vol = parseInt(item.jumlah_volume) || 1;
            const anggaran = typeof item.jumlah_anggaran === 'number' ? item.jumlah_anggaran : (parseFloat(item.jumlah_anggaran) || 0);
            const realisasiSpm = typeof item.total_realisasi_num === 'number' ? item.total_realisasi_num : (parseFloat(item.total_realisasi) || 0);
            const adminProyek = typeof item.biaya_administrasi_proyek === 'number' ? item.biaya_administrasi_proyek : (parseFloat(item.biaya_administrasi_proyek) || 0);
            
            // Rumus Akuntansi: Nilai Aset (15) = Nilai Perolehan (13) + Administrasi Proyek (14)
            const nilaiPerolehan = Math.max(0, realisasiSpm - adminProyek);
            const nilaiAset = realisasiSpm;

            totalS1Anggaran += anggaran;
            totalS1RealisasiSpm += realisasiSpm;
            totalS1Volume += vol;
            totalS1NilaiPerolehan += nilaiPerolehan;
            totalS1AdminProyek += adminProyek;
            totalS1NilaiAset += nilaiAset;

            // Gabungan No Pabrik / No Chasis (Rangka) / No Mesin / No Polisi
            let noIdentifiers = [];
            if (item.no_pabrik && item.no_pabrik !== '-') noIdentifiers.push('Pabrik: ' + item.no_pabrik);
            if (item.no_rangka && item.no_rangka !== '-') noIdentifiers.push('Chasis: ' + item.no_rangka);
            if (item.no_mesin && item.no_mesin !== '-') noIdentifiers.push('Mesin: ' + item.no_mesin);
            if (item.no_polisi && item.no_polisi !== '-') noIdentifiers.push('Nopol: ' + item.no_polisi);
            const noPabrikChasisMesin = noIdentifiers.length > 0 ? noIdentifiers.join(' / ') : (item.no_pabrik || item.no_mesin || item.no_rangka || '-');

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

            const lokasiBarang = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : '') || item.alamat_barang || 'RSUD Dr. H. Koesnandi';
            
            // Keterangan: Jika diisi maka gunakan isinya, jika kosong fallback format resmi Dana BLUD-BM.[NamaBarang]
            let keterangan = item.keterangan_tambahan || item.keterangan || '';
            if (!keterangan || keterangan === '-') {
                const asalDana = item.asal_usul ? ('Dana ' + item.asal_usul) : 'Dana BLUD';
                const namaSingkat = (item.nama_barang || '').replace(/^(Pengadaan|Belanja Modal|Pembelian)\s+/i, '');
                keterangan = asalDana + '-BM.' + (namaSingkat || 'Aset Tetap');
            }

            sheet1Rows.push([
                idx + 1,                                                        // 1. NO
                item.rekening_kode || '-',                                      // 2. No Rek. Bel. Modal
                item.rekening_nama || '-',                                      // 3. RINCIAN BELANJA MODAL
                anggaran,                                                       // 4. JUMLAH ANGGARAN (Rp)
                realisasiSpm,                                                   // 5. JUMLAH REALISASI SPM (Rp)
                item.kode_barang || item.jenis_aset_kode || item.sub_rincian_kode || '-', // 6. No Rek. Menurut PERMENDAGRI 108/2016
                item.nama_barang || '-',                                        // 7. NAMA ASET INVENTARIS
                item.merk || '-',                                               // 8. MERK
                item.type || '-',                                               // 9. TYPE
                noPabrikChasisMesin,                                            // 10. NO PABRIK/NO CHASIS/NO MESIN
                vol,                                                            // 11. JUMLAH BARANG
                item.satuan || 'Unit',                                          // 12. NAMA SATUAN BARANG
                nilaiPerolehan,                                                 // 13. NILAI PEROLEHAN (Rp)
                adminProyek,                                                    // 14. ADMINISTRASI PROYEK (Rp)
                nilaiAset,                                                      // 15 = 13 + 14. NILAI ASET (Rp)
                buktiNomor,                                                     // 16. BUKTI PENGADAAN NOMOR
                buktiTanggal,                                                   // 17. BUKTI PENGADAAN TANGGAL
                lokasiBarang,                                                   // 18. LOKASI BARANG
                keterangan                                                      // 19. KET. (Berdiri Sendiri)
            ]);
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
            totalS1NilaiPerolehan,
            totalS1AdminProyek,
            totalS1NilaiAset,
            "", "", "", ""
        ]);

        // Tanda Tangan Sheet 1
        const ppkNama = (filteredAstaps.find(a => a.ppk_nama && a.ppk_nama !== '-') || {}).ppk_nama || "Pejabat Pembuat Komitmen (PPK)";
        const ppkNip = (filteredAstaps.find(a => a.ppk_nip && a.ppk_nip !== '-') || {}).ppk_nip || "19780101 200501 1 008";

        sheet1Rows.push([""]);
        sheet1Rows.push(["", "", "", "", "", "", "", "", "", "", "", "", "", "Bondowoso, " + new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})]);
        sheet1Rows.push(["", "Mengetahui,", "", "", "", "", "", "", "", "", "", "", "", "Pengurus Barang Pengelola,"]);
        sheet1Rows.push(["", "Pejabat Pembuat Komitmen (PPK)", "", "", "", "", "", "", "", "", "", "", "", "RSUD Dr. H. Koesnandi"]);
        sheet1Rows.push([""]);
        sheet1Rows.push([""]);
        sheet1Rows.push(["", "( " + ppkNama + " )", "", "", "", "", "", "", "", "", "", "", "", "( ................................................ )"]);
        sheet1Rows.push(["", "NIP. " + ppkNip, "", "", "", "", "", "", "", "", "", "", "", "NIP. 19850615 201001 2 015"]);

        const wsSheet1 = XLSX.utils.aoa_to_sheet(sheet1Rows);
        wsSheet1['!cols'] = [
            {wch: 6},   // 1. NO
            {wch: 22},  // 2. No Rek. Bel. Modal
            {wch: 32},  // 3. RINCIAN BELANJA MODAL
            {wch: 22},  // 4. JUMLAH ANGGARAN (Rp)
            {wch: 24},  // 5. JUMLAH REALISASI SPM (Rp)
            {wch: 26},  // 6. No Rek. Menurut PERMENDAGRI 108/2016
            {wch: 35},  // 7. NAMA ASET INVENTARIS
            {wch: 18},  // 8. MERK
            {wch: 18},  // 9. TYPE
            {wch: 28},  // 10. NO PABRIK/NO CHASIS/NO MESIN
            {wch: 14},  // 11. JUMLAH BARANG
            {wch: 16},  // 12. NAMA SATUAN BARANG
            {wch: 22},  // 13. NILAI PEROLEHAN (Rp)
            {wch: 24},  // 14. ADMINISTRASI PROYEK (Rp)
            {wch: 22},  // 15. NILAI ASET (Rp)
            {wch: 26},  // 16. BUKTI PENGADAAN NOMOR
            {wch: 16},  // 17. BUKTI PENGADAAN TANGGAL
            {wch: 26},  // 18. LOKASI BARANG
            {wch: 28}   // 19. KET. (Berdiri Sendiri)
        ];
        wsSheet1['!rows'] = [
            { hpt: 20 }, // 0: PEMERINTAH KABUPATEN BONDOWOSO
            { hpt: 20 }, // 1: RUMAH SAKIT UMUM dr. H. KOESNANDI
            { hpt: 22 }, // 2: DAFTAR PENAMBAHAN ASET TETAP
            { hpt: 20 }, // 3: TRIWULAN ... TAHUN ANGGARAN ...
            { hpt: 20 }, // 4: SUMBER DANA : KELOMPOK ANGGARAN  (BLUD)
            { hpt: 24 }, // 5: Superheader BELANJA MODAL & RINCIAN ASET...
            { hpt: 32 }, // 6: Header Kolom Utama & Sub-Superheader
            { hpt: 24 }, // 7: Subheader Volume & Bukti Pengadaan
            { hpt: 20 }  // 8: Baris Nomor 1 s/d 19
        ];
        wsSheet1['!merges'] = [
            // Judul Laporan (Row 0 - 3, Col 0 - 18)
            { s: { r: 0, c: 0 }, e: { r: 0, c: 18 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 18 } },
            { s: { r: 2, c: 0 }, e: { r: 2, c: 18 } },
            { s: { r: 3, c: 0 }, e: { r: 3, c: 18 } },

            // Baris 4: Sumber Dana (Col 1 s/d Col 5)
            { s: { r: 4, c: 1 }, e: { r: 4, c: 5 } },

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
            { s: { r: s1TotalRowIdx, c: 0 }, e: { r: s1TotalRowIdx, c: 2 } }
        ];
        applyDaftarAtReportStyling(wsSheet1, sheet1Rows.length, 19, 5, 4, s1TotalRowIdx);
        if (filterSheet === 'all' || filterSheet === 'sheet1') {
            const s1TabTitle = filterTw === 'all' ? "1. Daftar AT Tahunan" : ("1. Daftar AT " + twTabName);
            XLSX.utils.book_append_sheet(wb, wsSheet1, filterSheet === 'sheet1' ? ("Daftar AT " + (filterTw === 'all' ? 'Tahunan' : twTabName)) : s1TabTitle);
        }

        // =========================================================================
        // SHEET 2: 2. DAFTAR PENGURANGAN AT RSDK
        // =========================================================================
        let sheet2Rows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            ["RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI"],
            ["DAFTAR PENGURANGAN ASET TETAP TAHUN ANGGARAN " + yearLabel],
            ["PERIODE: " + bannerTw],
            [""],
            [
                "NO",
                "KODE BARANG 108",
                "NO. REGISTER / NIBAR",
                "NAMA BARANG",
                "MERK / TYPE",
                "TAHUN PEROLEHAN",
                "NILAI BUKU / PEROLEHAN (Rp)",
                "SEBAB PENGURANGAN (RUSAK BERAT / HIBAH / PENGHAPUSAN)",
                "DASAR DOKUMEN / SK PENGHAPUSAN",
                "KETERANGAN"
            ]
        ];

        // Ambil item yang tercatat rusak berat jika ada
        const rusakItems = rawAstaps.filter(it => it.kondisi === 'RB' || it.kondisi === 'Rusak Berat');
        let totalS2Nilai = 0;
        if (rusakItems.length > 0) {
            rusakItems.slice(0, 15).forEach((it, idx) => {
                const val = parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0;
                totalS2Nilai += val;
                const nibar = it.registers && it.registers.length > 0 ? (it.registers[0].nibar || it.registers[0].no_register || '-') : '-';
                sheet2Rows.push([
                    idx + 1,
                    it.kode_barang || it.jenis_aset_kode || '-',
                    nibar,
                    it.nama_barang || '-',
                    it.merk || it.type || '-',
                    it.tahun_perolehan || yearLabel,
                    val,
                    "Kondisi Rusak Berat (Usulan Penghapusan)",
                    "Surat Usulan No. 032/RSDK/" + yearLabel,
                    it.ruang_unit || "Gudang Rusak"
                ]);
            });
        } else {
            sheet2Rows.push([
                1,
                "1.3.2.05.01.01.001",
                "NIBAR-0001",
                "(Contoh Item Pengurangan Aset Tetap)",
                "Merk / Type",
                yearLabel,
                0,
                "Rusak Berat / Usulan Penghapusan",
                "Surat Keputusan Bupati No. 188.45/430.10.7/" + yearLabel,
                "Dalam proses usulan penghapusan BPKAD"
            ]);
        }

        const s2TotalRowIdx = sheet2Rows.length;
        sheet2Rows.push([
            "JUMLAH TOTAL PENGURANGAN ASET TETAP", "", "", "", "", "",
            totalS2Nilai, "", "", ""
        ]);

        // Tanda Tangan Sheet 2
        sheet2Rows.push([""]);
        sheet2Rows.push(["", "", "", "", "", "", "Bondowoso, " + new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})]);
        sheet2Rows.push(["", "Mengetahui,", "", "", "", "", "Pengurus Barang Pengelola,"]);
        sheet2Rows.push(["", "Pejabat Pembuat Komitmen (PPK)", "", "", "", "", "RSUD Dr. H. Koesnandi"]);
        sheet2Rows.push([""]);
        sheet2Rows.push([""]);
        sheet2Rows.push(["", "( ................................................ )", "", "", "", "", "( ................................................ )"]);
        sheet2Rows.push(["", "NIP. 19780101 200501 1 008", "", "", "", "", "NIP. 19850615 201001 2 015"]);

        const wsSheet2 = XLSX.utils.aoa_to_sheet(sheet2Rows);
        wsSheet2['!cols'] = [
            {wch: 6},   // NO
            {wch: 22},  // KODE 108
            {wch: 22},  // NIBAR
            {wch: 32},  // NAMA BARANG
            {wch: 20},  // MERK/TYPE
            {wch: 16},  // TAHUN
            {wch: 22},  // NILAI
            {wch: 36},  // SEBAB
            {wch: 32},  // DOKUMEN
            {wch: 28}   // KET
        ];
        wsSheet2['!merges'] = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 9 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 9 } },
            { s: { r: 2, c: 0 }, e: { r: 2, c: 9 } },
            { s: { r: 3, c: 0 }, e: { r: 3, c: 9 } },
            { s: { r: s2TotalRowIdx, c: 0 }, e: { r: s2TotalRowIdx, c: 5 } },
            { s: { r: s2TotalRowIdx + 2, c: 6 }, e: { r: s2TotalRowIdx + 2, c: 9 } },
            { s: { r: s2TotalRowIdx + 3, c: 1 }, e: { r: s2TotalRowIdx + 3, c: 3 } },
            { s: { r: s2TotalRowIdx + 3, c: 6 }, e: { r: s2TotalRowIdx + 3, c: 9 } },
            { s: { r: s2TotalRowIdx + 4, c: 1 }, e: { r: s2TotalRowIdx + 4, c: 3 } },
            { s: { r: s2TotalRowIdx + 4, c: 6 }, e: { r: s2TotalRowIdx + 4, c: 9 } },
            { s: { r: s2TotalRowIdx + 7, c: 1 }, e: { r: s2TotalRowIdx + 7, c: 3 } },
            { s: { r: s2TotalRowIdx + 7, c: 6 }, e: { r: s2TotalRowIdx + 7, c: 9 } },
            { s: { r: s2TotalRowIdx + 8, c: 1 }, e: { r: s2TotalRowIdx + 8, c: 3 } },
            { s: { r: s2TotalRowIdx + 8, c: 6 }, e: { r: s2TotalRowIdx + 8, c: 9 } }
        ];
        applyCleanReportStyling(wsSheet2, sheet2Rows.length, 10, 5, 1, s2TotalRowIdx);
        if (filterSheet === 'all' || filterSheet === 'sheet2') {
            XLSX.utils.book_append_sheet(wb, wsSheet2, filterSheet === 'sheet2' ? "Pengurangan AT RSDK" : "2. Daftar Pengurangan AT RSDK");
        }

        // =========================================================================
        // SHEET 3: 3. REKLAS RSDK
        // =========================================================================
        let sheet3Rows = [
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            ["RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI"],
            ["DAFTAR REKLASIFIKASI ASET TETAP TAHUN ANGGARAN " + yearLabel],
            ["PERIODE: " + bannerTw],
            [""],
            [
                "NO",
                "KODE AKUN ASAL",
                "NAMA AKUN / KIB ASAL",
                "KODE AKUN TUJUAN",
                "NAMA AKUN / KIB TUJUAN",
                "NAMA BARANG / URAIAN REKLASIFIKASI",
                "NILAI REKLASIFIKASI (Rp)",
                "NO. BERITA ACARA REKLAS",
                "TANGGAL REKLAS",
                "KETERANGAN REKLASIFIKASI"
            ],
            [
                1,
                "1.3.6.01.01.01.001",
                "KIB F - Konstruksi Dalam Pengerjaan",
                "1.3.3.01.01.01.001",
                "KIB C - Gedung dan Bangunan",
                "Pembangunan Gedung Selesai Dikerjakan (Reklas KDP ke Gedung)",
                0,
                "BAST-KDP/01/RSDK/" + yearLabel,
                "31/12/" + yearLabel,
                "Reklasifikasi Proyek Fisik Rampung Serah Terima"
            ],
            [
                2,
                "1.3.2.05.01.01.001",
                "KIB B - Peralatan dan Mesin",
                "1.5.4.01.01.01.001",
                "Ekstrakomptabel",
                "Penyesuaian Batas Nilai Kapitalisasi (< Rp 300.000)",
                0,
                "BA-KOREKSI/02/RSDK/" + yearLabel,
                "31/12/" + yearLabel,
                "Reklas ke Akun Ekstrakomptabel"
            ]
        ];

        const s3TotalRowIdx = sheet3Rows.length;
        sheet3Rows.push([
            "JUMLAH TOTAL REKLASIFIKASI ASET TETAP", "", "", "", "", "",
            0, "", "", ""
        ]);

        // Tanda Tangan Sheet 3
        sheet3Rows.push([""]);
        sheet3Rows.push(["", "", "", "", "", "", "Bondowoso, " + new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})]);
        sheet3Rows.push(["", "Mengetahui,", "", "", "", "", "Pengurus Barang Pengelola,"]);
        sheet3Rows.push(["", "Pejabat Pembuat Komitmen (PPK)", "", "", "", "", "RSUD Dr. H. Koesnandi"]);
        sheet3Rows.push([""]);
        sheet3Rows.push([""]);
        sheet3Rows.push(["", "( ................................................ )", "", "", "", "", "( ................................................ )"]);
        sheet3Rows.push(["", "NIP. 19780101 200501 1 008", "", "", "", "", "NIP. 19850615 201001 2 015"]);

        const wsSheet3 = XLSX.utils.aoa_to_sheet(sheet3Rows);
        wsSheet3['!cols'] = [
            {wch: 6},   // NO
            {wch: 22},  // KODE ASAL
            {wch: 28},  // NAMA ASAL
            {wch: 22},  // KODE TUJUAN
            {wch: 28},  // NAMA TUJUAN
            {wch: 36},  // NAMA BARANG
            {wch: 22},  // NILAI REKLAS
            {wch: 26},  // NO BA
            {wch: 16},  // TANGGAL
            {wch: 32}   // KET
        ];
        wsSheet3['!merges'] = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 9 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 9 } },
            { s: { r: 2, c: 0 }, e: { r: 2, c: 9 } },
            { s: { r: 3, c: 0 }, e: { r: 3, c: 9 } },
            { s: { r: s3TotalRowIdx, c: 0 }, e: { r: s3TotalRowIdx, c: 5 } },
            { s: { r: s3TotalRowIdx + 2, c: 6 }, e: { r: s3TotalRowIdx + 2, c: 9 } },
            { s: { r: s3TotalRowIdx + 3, c: 1 }, e: { r: s3TotalRowIdx + 3, c: 3 } },
            { s: { r: s3TotalRowIdx + 3, c: 6 }, e: { r: s3TotalRowIdx + 3, c: 9 } },
            { s: { r: s3TotalRowIdx + 4, c: 1 }, e: { r: s3TotalRowIdx + 4, c: 3 } },
            { s: { r: s3TotalRowIdx + 4, c: 6 }, e: { r: s3TotalRowIdx + 4, c: 9 } },
            { s: { r: s3TotalRowIdx + 7, c: 1 }, e: { r: s3TotalRowIdx + 7, c: 3 } },
            { s: { r: s3TotalRowIdx + 7, c: 6 }, e: { r: s3TotalRowIdx + 7, c: 9 } },
            { s: { r: s3TotalRowIdx + 8, c: 1 }, e: { r: s3TotalRowIdx + 8, c: 3 } },
            { s: { r: s3TotalRowIdx + 8, c: 6 }, e: { r: s3TotalRowIdx + 8, c: 9 } }
        ];
        applyCleanReportStyling(wsSheet3, sheet3Rows.length, 10, 5, 1, s3TotalRowIdx);
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

        // Tanda Tangan Sheet 4
        sheet4Rows.push([""]);
        sheet4Rows.push(["", "", "", "", "", "", "", "", "Bondowoso, " + new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'})]);
        sheet4Rows.push(["", "Mengetahui,", "", "", "", "", "", "", "Pengurus Barang Pengelola,"]);
        sheet4Rows.push(["", "Pejabat Pembuat Komitmen (PPK)", "", "", "", "", "", "", "RSUD Dr. H. Koesnandi"]);
        sheet4Rows.push([""]);
        sheet4Rows.push([""]);
        sheet4Rows.push(["", "( ................................................ )", "", "", "", "", "", "", "( ................................................ )"]);
        sheet4Rows.push(["", "NIP. 19780101 200501 1 008", "", "", "", "", "", "", "NIP. 19850615 201001 2 015"]);

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
            { s: { r: s4TotalRowIdx + 2, c: 8 }, e: { r: s4TotalRowIdx + 2, c: 11 } },
            { s: { r: s4TotalRowIdx + 3, c: 1 }, e: { r: s4TotalRowIdx + 3, c: 4 } },
            { s: { r: s4TotalRowIdx + 3, c: 8 }, e: { r: s4TotalRowIdx + 3, c: 11 } },
            { s: { r: s4TotalRowIdx + 4, c: 1 }, e: { r: s4TotalRowIdx + 4, c: 4 } },
            { s: { r: s4TotalRowIdx + 4, c: 8 }, e: { r: s4TotalRowIdx + 4, c: 11 } },
            { s: { r: s4TotalRowIdx + 7, c: 1 }, e: { r: s4TotalRowIdx + 7, c: 4 } },
            { s: { r: s4TotalRowIdx + 7, c: 8 }, e: { r: s4TotalRowIdx + 7, c: 11 } },
            { s: { r: s4TotalRowIdx + 8, c: 1 }, e: { r: s4TotalRowIdx + 8, c: 4 } },
            { s: { r: s4TotalRowIdx + 8, c: 8 }, e: { r: s4TotalRowIdx + 8, c: 11 } }
        ];
        applyCleanReportStyling(wsSheet4, sheet4Rows.length, 12, 5, 2, s4TotalRowIdx);
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
                    this.askConfirmation({
                        title: '⚠️ Konfirmasi Hapus Register Unit NIBAR',
                        message: 'Apakah Anda yakin ingin menghapus unit register NIBAR ini secara permanen dari katalog?',
                        itemName: 'NIBAR: ' + (reg.nibar || reg.no_register),
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Unit NIBAR',
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
                                    }
                                    this.showToast('Unit register NIBAR berhasil dihapus!', 'success');
                                } else {
                                    window.location.reload();
                                }
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
