<!-- ========================================================================= -->
<!-- SCRIPTS: ENGINE EKSPOR EXCEL MULTI-SHEET ASET KEMITRAAN (AKUN 1.5.2)      -->
<!-- ========================================================================= -->
<script src="{{ asset('js/xlsx.bundle.js') }}"></script>
<script>
    if (typeof XLSX === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"><\/script>');
    }
</script>

<script>
    window.__simatAstaps = @json($kemitraanAstaps ?? []);

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

    function getReportSignDate(filterTw, filterYear) {
        const yr = (filterYear && filterYear !== 'all') ? filterYear : (new Date().getFullYear());
        const twKey = String(filterTw || '').replace(/[\s_]/g, '').toUpperCase();
        if (twKey === 'TWI' || twKey === 'TW1') return '31 Maret ' + yr;
        if (twKey === 'TWII' || twKey === 'TW2') return '30 Juni ' + yr;
        if (twKey === 'TWIII' || twKey === 'TW3') return '30 September ' + yr;
        if (twKey === 'TWIV' || twKey === 'TW4') return '31 Desember ' + yr;
        return new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
    }

    function getAstapGroupKey(item, fallbackPrefix = 'DEFAULT') {
        const yearKey = (item.kemitraan && item.kemitraan.tahun) || item.tahun_perolehan || '-';
        const twKey = (item.kemitraan && item.kemitraan.triwulan) || item.triwulan ? String((item.kemitraan && item.kemitraan.triwulan) || item.triwulan).replace(/[\s_]/g, '').toUpperCase() : '-';
        const progKey = item.program_kode || '-';
        const kegKey = item.kegiatan_kode || '-';
        const subKegKey = item.sub_kegiatan_kode || '-';
        const rekKey = item.rekening_kode || '-';
        const subRincianKey = item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : fallbackPrefix));
        return `${yearKey}___${twKey}___${progKey}___${kegKey}___${subKegKey}___${rekKey}___${subRincianKey}`;
    }

    function getAstapNibar(item, subItem = null, idx = 0) {
        if (subItem && (subItem.nibar || subItem.no_register)) {
            return subItem.nibar || subItem.no_register;
        }
        if (item && Array.isArray(item.registers) && item.registers.length > 0) {
            if (typeof idx === 'number' && item.registers[idx]) {
                return item.registers[idx].nibar || item.registers[idx].no_register || '-';
            }
            return item.registers[0].nibar || item.registers[0].no_register || '-';
        }
        return item?.nibar || item?.no_register || '-';
    }

    function resolveItemCategory(item) {
        if (!item) return 'KIB B';
        const kode = item.jenis_aset_kode || item.kode_barang || '';
        if (kode.startsWith('1.3.1') || kode.startsWith('1.5.2.01.01.01')) return 'KIB A';
        if (kode.startsWith('1.3.3') || kode.startsWith('1.5.2.01.01.03')) return 'KIB C';
        if (kode.startsWith('1.3.4') || kode.startsWith('1.5.2.01.01.04.004')) return 'KIB D';
        if (kode.startsWith('1.3.5') || kode.startsWith('1.5.2.01.01.02.005')) return 'KIB E';
        if (kode.startsWith('1.3.2') || kode.startsWith('1.5.2.01.01.04.002')) return 'KIB B';

        if (item.category) {
            const cUpper = item.category.toUpperCase().trim();
            if (cUpper === 'KIB A' || cUpper === 'A' || cUpper.includes('TANAH')) return 'KIB A';
            if (cUpper === 'KIB C' || cUpper === 'C' || cUpper.includes('GEDUNG') || cUpper.includes('BANGUNAN')) return 'KIB C';
            if (cUpper === 'KIB D' || cUpper === 'D' || cUpper.includes('JALAN') || cUpper.includes('JARINGAN')) return 'KIB D';
            if (cUpper === 'KIB E' || cUpper === 'E' || cUpper.includes('LAINNYA')) return 'KIB E';
            if (cUpper === 'KIB B' || cUpper === 'B' || cUpper.includes('MESIN') || cUpper.includes('PERALATAN')) return 'KIB B';
        }

        const nama = (item.nama_barang || '').toUpperCase();
        if (nama.includes('TANAH') || nama.includes('LAHAN')) return 'KIB A';
        if (nama.includes('GEDUNG') || nama.includes('BANGUNAN')) return 'KIB C';
        if (nama.includes('JALAN') || nama.includes('IRIGASI') || nama.includes('JARINGAN') || nama.includes('SERVER')) return 'KIB D';
        if (nama.includes('BUKU') || nama.includes('KESENIAN') || nama.includes('HEWAN') || nama.includes('TANAMAN')) return 'KIB E';

        return 'KIB B';
    }

    function normalizeSkemaKemitraan(raw) {
        if (!raw) return 'sewa';
        const s = String(raw).toUpperCase().trim();
        if (s.includes('SEWA')) return 'sewa';
        if (s.includes('KSP') || s.includes('PEMANFAATAN')) return 'ksp';
        if (s.includes('BGS') || s.includes('BSG') || s.includes('BANGUN')) return 'bgs_bsg';
        if (s.includes('KSPI') || s.includes('INFRASTRUKTUR') || s.includes('KSO') || s.includes('OPERASIONAL')) return 'kspi';
        return 'sewa';
    }

    function getSkemaLabelKemitraan(skemaKey) {
        if (skemaKey === 'sewa') return 'SEWA (1.5.2.01.01.01)';
        if (skemaKey === 'ksp') return 'KERJA SAMA PEMANFAATAN / KSP (1.5.2.01.01.02)';
        if (skemaKey === 'bgs_bsg') return 'BANGUN GUNA SERAH / BSG (1.5.2.01.01.03)';
        if (skemaKey === 'kspi' || skemaKey === 'kso') return 'KERJA SAMA PENYEDIAAN INFRASTRUKTUR / KSPI (1.5.2.01.01.04)';
        return 'SEMUA SKEMA KEMITRAAN (AKUN 1.5.2)';
    }

    function getKibSignatureMerges(signStartRow, numCols, rightStartCol, leftStartCol = 1, leftEndCol = null, rightEndCol = null) {
        const lS = leftStartCol;
        const lE = leftEndCol !== null ? leftEndCol : Math.min(lS + 5, rightStartCol - 2);
        const rS = rightStartCol;
        const rE = rightEndCol !== null ? rightEndCol : (numCols - 1);

        const merges = [];
        merges.push({ s: { r: signStartRow + 1, c: lS }, e: { r: signStartRow + 1, c: lE } });
        merges.push({ s: { r: signStartRow + 1, c: rS }, e: { r: signStartRow + 1, c: rE } });
        merges.push({ s: { r: signStartRow + 2, c: lS }, e: { r: signStartRow + 2, c: lE } });
        merges.push({ s: { r: signStartRow + 2, c: rS }, e: { r: signStartRow + 2, c: rE } });
        merges.push({ s: { r: signStartRow + 3, c: lS }, e: { r: signStartRow + 3, c: lE } });
        merges.push({ s: { r: signStartRow + 6, c: lS }, e: { r: signStartRow + 6, c: lE } });
        merges.push({ s: { r: signStartRow + 6, c: rS }, e: { r: signStartRow + 6, c: rE } });
        merges.push({ s: { r: signStartRow + 7, c: lS }, e: { r: signStartRow + 7, c: lE } });
        merges.push({ s: { r: signStartRow + 7, c: rS }, e: { r: signStartRow + 7, c: rE } });
        merges.push({ s: { r: signStartRow + 8, c: lS }, e: { r: signStartRow + 8, c: lE } });
        return merges;
    }

    function applySignatureBlockStyling(ws, signStartRow, numCols) {
        for (let offset = 0; offset < 9; offset++) {
            const r = signStartRow + offset;
            for (let c = 0; c < numCols; c++) {
                const cellRef = getColName(c) + (r + 1);
                if (!ws[cellRef]) ws[cellRef] = { v: "", t: "s" };
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

    function applyRekapSheetStyling(ws, rowCount, colCount, titleRowCount = 5, totalRowIdx = 12, signStartRow = 14) {
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
                if (!ws[cellRef]) ws[cellRef] = { v: "", t: "s" };

                const cell = ws[cellRef];
                let fill = "FFFFFF";
                let fontColor = "0F172A";
                let bold = false;
                let align = "center";
                let border = thinBorder;
                let fontSize = 9.5;
                let numFmt = null;

                if (r < titleRowCount) {
                    fill = "FFFFFF";
                    fontColor = "0F172A";
                    bold = true;
                    fontSize = (r === 0 || r === 1) ? 12 : 11;
                    align = "center";
                    border = null;
                } else if (r === titleRowCount) {
                    fill = "0891B2"; // Cyan / Teal Deep Header
                    fontColor = "FFFFFF";
                    bold = true;
                    fontSize = 10;
                    align = "center";
                    border = thinBorder;
                } else if (r === totalRowIdx) {
                    fill = "A5F3FC"; // Soft Cyan total row
                    fontColor = "0F172A";
                    bold = true;
                    fontSize = 10;
                    border = doubleBottomBorder;
                    if (c === 0) align = "center";
                    else if (c === 4) align = "center";
                    else if (c === 5) {
                        align = "right";
                        numFmt = "Rp #,##0.00";
                    } else align = "left";
                } else if (r >= signStartRow) {
                    continue;
                } else {
                    fill = (r % 2 === 0) ? "FFFFFF" : "F0FDFA";
                    if (c === 0) align = "center";
                    else if (c === 1) {
                        align = "left";
                        bold = true;
                    } else if (c === 2) align = "center";
                    else if (c === 3) align = "left";
                    else if (c === 4) align = "center";
                    else if (c === 5) {
                        align = "right";
                        bold = true;
                        fill = "E0F2FE";
                        numFmt = "Rp #,##0.00";
                    } else align = "left";
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

    let isExportingKemitraan = false;

    function exportKemitraanToExcel(params = {}) {
        if (isExportingKemitraan) return;
        isExportingKemitraan = true;

        if (typeof XLSX === 'undefined') {
            alert('⚠️ Pustaka Excel sedang dimuat, silakan coba 1 detik lagi...');
            isExportingKemitraan = false;
            return;
        }

        const rawAstaps = window.__simatAstaps || [];
        const wb = XLSX.utils.book_new();

        const filterYear = params.year || 'all';
        const filterTw   = params.triwulan || 'all';
        const filterSkema= params.skema || 'all';
        const filterCat  = params.category || 'all';

        const isTwMatch = (itemTw, targetTw) => {
            if (targetTw === 'all') return true;
            const targetKey = targetTw.replace(/[\s_]/g, '').toUpperCase();
            const curTw = (itemTw || 'TWI').replace(/[\s_]/g, '').toUpperCase();
            return (curTw === targetKey) ||
                   (targetKey === 'TWI' && curTw === 'TW1') || (targetKey === 'TW1' && curTw === 'TWI') ||
                   (targetKey === 'TWII' && curTw === 'TW2') || (targetKey === 'TW2' && curTw === 'TWII') ||
                   (targetKey === 'TWIII' && curTw === 'TW3') || (targetKey === 'TW3' && curTw === 'TWIII') ||
                   (targetKey === 'TWIV' && curTw === 'TW4') || (targetKey === 'TW4' && curTw === 'TWIV');
        };

        // Filter data khusus kemitraan
        let filteredKemitraans = rawAstaps.filter(item => {
            const isKemitraan = item.sumber_dana === 'kemitraan' || !!item.kemitraan;
            if (!isKemitraan) return false;

            const itemYear = (item.kemitraan && item.kemitraan.tahun) || item.tahun_perolehan;
            const matchYear = filterYear === 'all' || String(itemYear) === String(filterYear);

            const itemTw = (item.kemitraan && item.kemitraan.triwulan) || item.triwulan || 'TWI';
            const matchTw = isTwMatch(itemTw, filterTw);

            let matchSkema = true;
            if (filterSkema !== 'all') {
                const rawSkema = (item.kemitraan && item.kemitraan.skema_kemitraan) 
                    || (item.spesifikasi_json && item.spesifikasi_json.skema_kemitraan) 
                    || '';
                matchSkema = normalizeSkemaKemitraan(rawSkema) === filterSkema;
            }

            let matchCat = true;
            if (filterCat !== 'all' && filterCat !== 'REKAP') {
                const spec = getSafeSpec(item);
                const isItemExtracom = !!item.is_extracomtable || 
                                       (item.category && item.category.toUpperCase() === 'EXTRACOM') || 
                                       (spec && spec.is_extracomtable) ||
                                       (Array.isArray(spec.mesin_items) && spec.mesin_items.some(m => !!m.is_extracom)) ||
                                       (Array.isArray(spec.lainnya_items) && spec.lainnya_items.some(l => !!l.is_extracom));
                if (filterCat === 'EXTRACOM') {
                    matchCat = isItemExtracom;
                } else {
                    if (isItemExtracom) {
                        matchCat = false;
                    } else {
                        const itemCat = typeof resolveItemCategory === 'function' ? resolveItemCategory(item) : item.category;
                        matchCat = (itemCat === filterCat);
                    }
                }
            }

            return matchYear && matchTw && matchSkema && matchCat;
        });

        // Pengelompokan Data per Kategori KIB Kemitraan (Khusus Akun 1.5.2: KIB A s/d E & EXTRACOM)
        const categories = {
            'KIB A': [],
            'KIB B': [],
            'KIB C': [],
            'KIB D': [],
            'KIB E': [],
            'EXTRACOM': []
        };

        filteredKemitraans.forEach(item => {
            const spec = getSafeSpec(item);
            const isItemExtracom = !!item.is_extracomtable || 
                                   (item.category && item.category.toUpperCase() === 'EXTRACOM') || 
                                   (spec && spec.is_extracomtable) ||
                                   (Array.isArray(spec.mesin_items) && spec.mesin_items.some(m => !!m.is_extracom)) ||
                                   (Array.isArray(spec.lainnya_items) && spec.lainnya_items.some(l => !!l.is_extracom));

            if (isItemExtracom) {
                categories['EXTRACOM'].push(item);
            } else {
                const cat = typeof resolveItemCategory === 'function' ? resolveItemCategory(item) : (item.category || 'KIB B');
                if (categories[cat]) {
                    categories[cat].push(item);
                } else {
                    categories['KIB B'].push(item);
                }
            }
        });

        // Label Dinamis
        let twLabel = "KESELURUHAN (TAHUNAN)";
        if (filterTw === 'TW I' || filterTw === 'TW1') twLabel = "TRIWULAN I (JANUARI - MARET)";
        else if (filterTw === 'TW II' || filterTw === 'TW2') twLabel = "TRIWULAN II (APRIL - JUNI)";
        else if (filterTw === 'TW III' || filterTw === 'TW3') twLabel = "TRIWULAN III (JULI - SEPTEMBER)";
        else if (filterTw === 'TW IV' || filterTw === 'TW4') twLabel = "TRIWULAN IV (OKTOBER - DESEMBER)";

        const yearLabel = filterYear === 'all' ? (new Date().getFullYear()) : filterYear;
        const skemaLabel = getSkemaLabelKemitraan(filterSkema);
        const signDate = typeof getReportSignDate === 'function' ? getReportSignDate(filterTw, filterYear) : (new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }));

        let sampleMitra = '';
        for (const it of filteredKemitraans) {
            const m = (it.kemitraan && it.kemitraan.mitra_nama) || (it.spesifikasi_json && it.spesifikasi_json.mitra_nama);
            if (m && m !== '-' && m !== 'Mitra Pihak Ketiga') {
                sampleMitra = m;
                break;
            }
        }

        function buildKemitraanSignRows(numCols, rightStartCol, leftStartCol = 1) {
            const today = signDate || (new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }));
            const mTitle = sampleMitra ? sampleMitra : 'MITRA / REKANAN KERJA SAMA';

            function makeRow(leftVal, rightVal) {
                const row = Array(numCols).fill('');
                row[leftStartCol]  = leftVal;
                row[rightStartCol] = rightVal;
                return row;
            }

            return [
                Array(numCols).fill(''),
                makeRow('MENGETAHUI,',                      'Bondowoso, ' + today),
                makeRow('PENGURUS BARANG PENGGUNA',         'PIHAK KETIGA / REKANAN'),
                makeRow('RSUD dr. H. KOESNANDI BONDOWOSO',  mTitle),
                Array(numCols).fill(''),
                Array(numCols).fill(''),
                makeRow('BUDI HARTONO, S.Sos',              '( .................................................. )'),
                makeRow('NIP. 19760229 200801 1 010',       'Direktur / Pimpinan Rekanan'),
                Array(numCols).fill('')
            ];
        }

        function getKemitraanCatSummary(kibKey) {
            const items = categories[kibKey] || [];
            let totalVal = 0;
            let totalUnits = 0;

            items.forEach(it => {
                const val = (it.kemitraan && typeof it.kemitraan.nilai_aset === 'number')
                    ? it.kemitraan.nilai_aset
                    : (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0);
                totalVal += val;

                const vol = (it.kemitraan && it.kemitraan.jumlah_volume)
                    ? it.kemitraan.jumlah_volume
                    : (parseInt(it.jumlah_volume) || 1);
                totalUnits += vol;
            });

            return { totalVal, totalUnits, itemCount: items.length };
        }

        const sumA = getKemitraanCatSummary('KIB A');
        const sumB = getKemitraanCatSummary('KIB B');
        const sumC = getKemitraanCatSummary('KIB C');
        const sumD = getKemitraanCatSummary('KIB D');
        const sumE = getKemitraanCatSummary('KIB E');
        const sumExtracom = getKemitraanCatSummary('EXTRACOM');

        const grandTotalVal = sumA.totalVal + sumB.totalVal + sumC.totalVal + sumD.totalVal + sumE.totalVal + sumExtracom.totalVal;
        const grandTotalUnits = sumA.totalUnits + sumB.totalUnits + sumC.totalUnits + sumD.totalUnits + sumE.totalUnits + sumExtracom.totalUnits;

        // 1. REKAPITULASI KEMITRAAN (Sheet 1)
        const rekapData = [
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            ["RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI"],
            ["BUKU REKAPITULASI ASET KEMITRAAN DENGAN PIHAK KETIGA (AKUN 1.5.2)"],
            [`SKEMA: ${skemaLabel} · PERIODE: ${twLabel} TAHUN ANGGARAN ${yearLabel}`],
            [""],
            [
                "NO",
                "KLASIFIKASI KIB / AKUN 108 KEMITRAAN",
                "KODE AKUN REKENING BMD",
                "SKEMA KERJA SAMA",
                "JUMLAH ITEM / UNIT",
                "TOTAL TAKSIRAN NILAI ASET (Rp)",
                "KETERANGAN / STATUS KONSESI"
            ],
            [
                "1", "KIB A - TANAH KEMITRAAN", "1.5.2.01.01.01.001",
                "Sewa / KSP Lahan & Lapangan",
                sumA.totalUnits + " Bidang", sumA.totalVal,
                sumA.itemCount > 0 ? "Tercatat di Buku Tanah Akun 1.5.2" : "-"
            ],
            [
                "2", "KIB B - PERALATAN DAN MESIN KEMITRAAN (ALAT KESEHATAN)", "1.5.2.01.01.04.002",
                "Alat Medis, Laboratorium & Radiologi",
                sumB.totalUnits + " Unit", sumB.totalVal,
                sumB.itemCount > 0 ? "Aktif Operasional Penunjang Medis" : "-"
            ],
            [
                "3", "KIB C - GEDUNG DAN BANGUNAN (BGS/BSG)", "1.5.2.01.01.03.003",
                "Bangun Guna Serah Fasilitas Rekanan",
                sumC.totalUnits + " Bangunan", sumC.totalVal,
                sumC.itemCount > 0 ? "Fasilitas Bangunan Konsesi Mitra" : "-"
            ],
            [
                "4", "KIB D - JALAN, IRIGASI DAN JARINGAN", "1.5.2.01.01.04.004",
                "Infrastruktur Server & Jaringan IT",
                sumD.totalUnits + " Jaringan", sumD.totalVal,
                sumD.itemCount > 0 ? "Jaringan & Utilitas Kerja Sama" : "-"
            ],
            [
                "5", "KIB E - ASET TETAP LAINNYA", "1.5.2.01.01.02.005",
                "Aset Kerja Sama Lainnya",
                sumE.totalUnits + " Unit/Item", sumE.totalVal,
                sumE.itemCount > 0 ? "Aset Kemitraan Khusus Lainnya" : "-"
            ],
            [
                "6", "EXTRACOM - BARANG EKSTRAKOMTABEL (< Rp 300.000)", "1.5.2.01.01.04.002",
                "Peralatan, Mesin & Barang Ekstrakomtabel",
                sumExtracom.totalUnits + " Unit/Item", sumExtracom.totalVal,
                sumExtracom.itemCount > 0 ? "Barang Ekstrakomtabel Nilai < Rp 300.000" : "-"
            ],
            [
                "JUMLAH TOTAL NILAI ASET KEMITRAAN (AKUN 1.5.2)", "", "", "",
                grandTotalUnits + " Unit Total",
                grandTotalVal,
                "Rekapitulasi " + twLabel + " " + yearLabel
            ]
        ];

        const rekapSignStart = rekapData.length;
        const rekapSigns = buildKemitraanSignRows(7, 4, 1);
        rekapSigns.forEach(r => rekapData.push(r));

        const wsRekap = XLSX.utils.aoa_to_sheet(rekapData);

        wsRekap['!cols'] = [
            { wch: 6 }, { wch: 42 }, { wch: 22 }, { wch: 38 }, { wch: 20 }, { wch: 28 }, { wch: 36 }
        ];
        wsRekap['!merges'] = [
            { s: { r: 0, c: 0 }, e: { r: 0, c: 6 } },
            { s: { r: 1, c: 0 }, e: { r: 1, c: 6 } },
            { s: { r: 2, c: 0 }, e: { r: 2, c: 6 } },
            { s: { r: 3, c: 0 }, e: { r: 3, c: 6 } },
            { s: { r: 12, c: 0 }, e: { r: 12, c: 3 } },
            ...getKibSignatureMerges(rekapSignStart, 7, 4, 1, 2, 6)
        ];

        applyRekapSheetStyling(wsRekap, rekapData.length, 7, 5, 12, rekapSignStart);
        applySignatureBlockStyling(wsRekap, rekapSignStart, 7);

        if (filterCat === 'all' || filterCat === 'REKAP') {
            XLSX.utils.book_append_sheet(wb, wsRekap, filterCat === 'all' ? "1. Rekapitulasi" : "Rekapitulasi Kemitraan");
        }

        function getSafeSpec(it) {
            let spec = it.spesifikasi_json || {};
            if (typeof spec === 'string') {
                try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
            }
            return spec || {};
        }

        function getKemitraanKibTitleRows(kibCategoryName, yearLabel, filterTw, skemaLabel) {
            let twRoman = "TAHUNAN";
            if (filterTw && filterTw !== 'all') {
                const twKey = String(filterTw).replace(/[\s_]/g, '').toUpperCase();
                if (twKey === 'TWI' || twKey === 'TW1') twRoman = "TRIWULAN I";
                else if (twKey === 'TWII' || twKey === 'TW2') twRoman = "TRIWULAN II";
                else if (twKey === 'TWIII' || twKey === 'TW3') twRoman = "TRIWULAN III";
                else if (twKey === 'TWIV' || twKey === 'TW4') twRoman = "TRIWULAN IV";
            }

            return [
                ["PEMERINTAH KABUPATEN BONDOWOSO"],
                ["RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI"],
                ["DAFTAR ASET KEMITRAAN DENGAN PIHAK KETIGA (AKUN 1.5.2) - " + kibCategoryName.toUpperCase() + " TAHUN " + yearLabel],
                ["SKEMA KERJA SAMA: " + (skemaLabel || 'SEMUA SKEMA') + " · KONSESI OPERASIONAL FASILITAS RUMAH SAKIT"],
                [twRoman + " TAHUN ANGGARAN " + yearLabel]
            ];
        }

        function getKemitraanStep4Cols(item) {
            const k = item.kemitraan || {};
            const spec = getSafeSpec(item);
            const mitraNama = k.mitra_nama || spec.mitra_nama || item.penyedia_nama || 'Mitra Rekanan';
            const mitraPimpinan = k.mitra_pimpinan || spec.mitra_pimpinan || item.penyedia_pemilik || '-';
            const mitraAlamat = k.mitra_alamat || spec.mitra_alamat || item.penyedia_alamat || '-';
            const ppkNama = (item.ppk_nama && item.ppk_nama !== '-') ? item.ppk_nama : ((spec.ppk_nama && spec.ppk_nama !== '-') ? spec.ppk_nama : 'BUDI HARTONO, S.Sos');
            const ppkNip = (item.ppk_nip && item.ppk_nip !== '-') ? item.ppk_nip : ((spec.ppk_nip && spec.ppk_nip !== '-') ? spec.ppk_nip : '19760229 200801 1 010');
            const ket = (k.keterangan && k.keterangan !== '-') ? k.keterangan : (item.keterangan_tambahan && item.keterangan_tambahan !== '-' ? item.keterangan_tambahan : (k.status_konsesi ? ('Konsesi: ' + k.status_konsesi) : '-'));

            return [
                mitraNama,
                mitraPimpinan,
                mitraAlamat,
                ppkNama,
                ppkNip,
                ket
            ];
        }

        function getKemitraanKontrakCols(item) {
            const k = item.kemitraan || {};
            const spec = getSafeSpec(item);
            const spkNo = k.nomor_pks || spec.nomor_pks || item.bast_dokumen_nomor || item.spk_nomor || '-';
            const spkTgl = formatAstapDate(k.tanggal_pks || spec.tanggal_pks || item.bast_dokumen_tanggal || item.spk_tanggal);

            return [
                spkNo, spkTgl
            ];
        }

        function buildKemitraanCol1to7(item, globalNo, isFirstRow, groupTotalAnggaran, groupTotalRealisasi, defaultPrefix, defaultAsetNama) {
            if (!isFirstRow) {
                return ["", "", "", "", "", "", ""];
            }
            const jenisKode = item.jenis_aset_kode || (item.kode_barang ? item.kode_barang.substring(0, 5) : defaultPrefix);
            const jenisNama = item.jenis_aset_nama || defaultAsetNama;
            const subRincianKode = item.sub_rincian_kode || (item.kode_barang && item.kode_barang.length >= 14 ? item.kode_barang.substring(0, 14) : (item.kode_barang ? item.kode_barang.substring(0, 11) : '-'));
            const subRincianNama = item.sub_rincian_nama || item.nama_barang || defaultAsetNama;

            return [
                globalNo,
                jenisKode, jenisNama,
                subRincianKode, subRincianNama,
                groupTotalAnggaran,
                groupTotalRealisasi
            ];
        }

        function getKemitraanKibMerges(baseMerges, colCount, titleRowCount, totalRowCount, hasSignature = false) {
            const offset = titleRowCount - 3;
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
                e: { r: footerRowIdx, c: 4 }
            };
            return [...titleMerges, footerMerge, ...shiftedBaseMerges];
        }

        function applyKemitraanMasterSheetStyling(ws, rowCount, colCount, kibL3ColCount, titleRowCount = 5, hasSignature = false) {
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

            const headerStartRow = titleRowCount;
            const headerEndRow = titleRowCount + 4;
            const l3Start = 7;
            const l3End = 7 + kibL3ColCount - 1;
            const footerRowIdx = hasSignature ? (rowCount - 1 - 9) : (rowCount - 1);

            for (let r = 0; r < rowCount; r++) {
                if (hasSignature && r > footerRowIdx) continue;

                for (let c = 0; c < colCount; c++) {
                    const cellRef = getColName(c) + (r + 1);
                    if (!ws[cellRef]) ws[cellRef] = { v: "", t: "s" };

                    const cell = ws[cellRef];
                    let fill = "FFFFFF";
                    let fontColor = "0F172A";
                    let bold = false;
                    let align = "left";
                    let border = thinBorder;
                    let fontSize = 9;
                    let numFmt = null;

                    if (r < titleRowCount) {
                        fill = "FFFFFF";
                        fontColor = "000000";
                        bold = true;
                        align = "center";
                        fontSize = (r === 0 || r === 1) ? 12 : (r === 2 ? 11 : 10);
                        border = null;
                    } else if (r >= headerStartRow && r <= headerEndRow) {
                        bold = true;
                        fontSize = 9;
                        fontColor = "0F172A";
                        align = "center";

                        if (r === headerEndRow) {
                            fill = "CBD5E1";
                            fontSize = 8.5;
                        } else if (c === 0) {
                            fill = "D7E4BC";
                        } else if (c >= 1 && c <= 4) {
                            fill = "D7E4BC";
                        } else if (c === 5 || c === 6) {
                            fill = "BFDBFE";
                        } else if (c >= l3Start && c <= l3End) {
                            fill = "E4DFEC";
                        } else {
                            fill = "FDE9D9";
                        }
                    } else if (r === footerRowIdx) {
                        fill = "93C5FD";
                        fontColor = "0F172A";
                        bold = true;
                        fontSize = 9.5;
                        border = doubleBottomBorder;
                        align = (c === 0) ? "center" : "right";
                        if (typeof cell.v === 'number') numFmt = "Rp #,##0.00";
                    } else {
                        fill = (r % 2 === 0) ? "FFFFFF" : "F8FAFC";
                        if (c === 0) align = "center";
                        else if (c === 1 || c === 3 || c === 8) align = "center";
                        else align = "left";

                        if (c === 5 || c === 6) {
                            fill = "EFF6FF";
                            bold = true;
                            align = "right";
                            if (typeof cell.v === 'number') numFmt = "Rp #,##0.00";
                        } else if (typeof cell.v === 'number') {
                            align = "right";
                            if (cell.v >= 1000) numFmt = "Rp #,##0.00";
                            else numFmt = "#,##0";
                        }
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

        // ========================================================================
        // 2. KIB A (TANAH KEMITRAAN)
        // ========================================================================
        const kibATitleRows = getKemitraanKibTitleRows("TANAH KEMITRAAN", yearLabel, filterTw, skemaLabel);
        const kibARows = [
            ...kibATitleRows,
            [
                "NO",
                "ASET KEMITRAAN (AKUN 1.5.2)", "", "", "", "", "",
                "RINCIAN ASET KEMITRAAN SESUAI PKS / PERJANJIAN KERJA SAMA / " + yearLabel,
                "", "", "", "", "", "", "", "", "", "", "",
                "LETAK / ALAMAT BARANG",
                "PIHAK PENYEDIA / MITRA", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            [
                "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH TAKSIRAN (Rp)",
                "NILAI ASET WAJAR (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "Status Tanah", "", "",
                "Dokumen PKS / Kontrak", "",
                "Kondisi\n(B/KB/RB)",
                "Penggunaan",
                "VOLUME", "",
                "Total Nilai Barang (Rp)",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "",
                "",
                "",
                "",
                "Hak Tanah\n(Hak Pakai / Hak Pengelolaan)", "Sertifikat", "",
                "", "",
                "",
                "",
                "Jumlah Bidang Tanah", "Luas Tanah (m²)",
                "",
                "",
                "Nama Mitra Rekanan", "Pimpinan Mitra", "Alamat Mitra",
                "Nama", "NIP",
                ""
            ],
            [
                "", "", "", "", "", "", "",
                "", "", "",
                "Tanggal", "Nomor",
                "Nomor", "Tanggal",
                "", "", "", "", "",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26"
            ]
        ];

        const kibAGroups = {};
        categories['KIB A'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.5.2.01.01.01');
            if (!kibAGroups[groupKey]) kibAGroups[groupKey] = [];
            kibAGroups[groupKey].push(item);
        });

        let globalKibANo = 1;
        let kibATotalAnggaran = 0, kibATotalRealisasi = 0, kibATotalUnit = 0, kibATotalLuas = 0, kibATotalNilaiBarang = 0;

        Object.keys(kibAGroups).forEach(groupKey => {
            const groupItems = kibAGroups[groupKey];
            let groupAnggaranTotal = 0;
            let groupRealisasiTotal = 0;

            groupItems.forEach(it => {
                const k = it.kemitraan || {};
                const val = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0);
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || val));
                groupRealisasiTotal += val;
            });

            kibATotalAnggaran += groupAnggaranTotal;
            kibATotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const k = item.kemitraan || {};
                const spec = getSafeSpec(item);
                const subTanahItems = (spec.tanah_items && Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0)
                    ? spec.tanah_items
                    : null;

                if (subTanahItems) {
                    subTanahItems.forEach((tItem) => {
                        const jumlahBidang = Math.max(1, parseInt(tItem.tanah_jumlah_barang) || 1);
                        const luasM2 = parseFloat(tItem.tanah_luas_m2) || 0;
                        const totalNilaiBidang = typeof tItem.tanah_nilai_satuan === 'number' ? tItem.tanah_nilai_satuan : (parseFloat(tItem.tanah_nilai_satuan) || (groupRealisasiTotal / subTanahItems.length) || 0);

                        kibATotalUnit += jumlahBidang;
                        kibATotalLuas += luasM2;
                        kibATotalNilaiBarang += totalNilaiBidang;

                        const rawKondisi = tItem.tanah_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                        const col1to7 = buildKemitraanCol1to7(item, globalKibANo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'TANAH KEMITRAAN');
                        if (isFirstRowInGroup) { globalKibANo++; isFirstRowInGroup = false; }

                        kibARows.push([
                            ...col1to7,
                            tItem.tanah_nama_barang || item.nama_barang || '-',
                            tItem.tanah_kode_barang || spec.tanah_kode_barang || item.kode_barang || '1.5.2.01.01.01.001',
                            tItem.tanah_hak || spec.hak_tanah || item.hak_tanah || 'Hak Pakai',
                            formatAstapDate(tItem.tanah_sertifikat_tgl || spec.sertifikat_tgl || item.sertifikat_tanggal),
                            tItem.tanah_sertifikat_no || spec.sertifikat_no || item.sertifikat_nomor || '-',
                            ...getKemitraanKontrakCols(item),
                            kondisiLabel,
                            tItem.tanah_penggunaan || spec.penggunaan || 'Lahan RSUD Kerja Sama Mitra',
                            jumlahBidang,
                            luasM2,
                            totalNilaiBidang,
                            tItem.tanah_alamat || item.alamat_barang || '-',
                            ...getKemitraanStep4Cols(item)
                        ]);
                    });
                } else {
                    const totalVal = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(item.total_realisasi_num) || parseFloat(item.total_realisasi) || 0);
                    const totalNilaiBarang = totalVal;
                    const jumlahBidang = parseInt(item.jumlah_bidang) || (parseInt(item.jumlah_volume) || 1);
                    const luasM2 = parseFloat(spec.luas_m2 || item.luas_m2) || 0;

                    kibATotalUnit += jumlahBidang;
                    kibATotalLuas += luasM2;
                    kibATotalNilaiBarang += totalNilaiBarang;

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    const col1to7 = buildKemitraanCol1to7(item, globalKibANo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'TANAH KEMITRAAN');
                    if (isFirstRowInGroup) { globalKibANo++; isFirstRowInGroup = false; }

                    kibARows.push([
                        ...col1to7,
                        item.nama_barang || '-',
                        spec.tanah_kode_barang || item.kode_barang || '1.5.2.01.01.01.001',
                        spec.hak_tanah || item.hak_tanah || 'Hak Pakai',
                        formatAstapDate(spec.sertifikat_tgl || item.sertifikat_tanggal),
                        spec.sertifikat_no || item.sertifikat_nomor || '-',
                        ...getKemitraanKontrakCols(item),
                        kondisiLabel,
                        spec.penggunaan || 'Lahan RSUD Kerja Sama Mitra',
                        jumlahBidang,
                        luasM2,
                        totalNilaiBarang,
                        item.alamat_barang || '-',
                        ...getKemitraanStep4Cols(item)
                    ]);
                }
            });
        });

        // Baris Footer Total KIB A
        const kibAFooterRow = Array(26).fill("");
        kibAFooterRow[0] = "JUMLAH";
        kibAFooterRow[5] = kibATotalAnggaran;
        kibAFooterRow[6] = kibATotalRealisasi;
        kibAFooterRow[16] = kibATotalUnit;
        kibAFooterRow[17] = kibATotalLuas;
        kibAFooterRow[18] = kibATotalNilaiBarang;
        kibARows.push(kibAFooterRow);

        const kibASignStartRow = kibARows.length;
        const kibASignRows = buildKemitraanSignRows(26, 17, 1);
        kibASignRows.forEach(r => kibARows.push(r));

        const wsKibA = XLSX.utils.aoa_to_sheet(kibARows);
        wsKibA['!cols'] = Array(26).fill({wch: 18});
        wsKibA['!cols'][0] = {wch: 6};
        wsKibA['!cols'][1] = {wch: 14}; wsKibA['!cols'][2] = {wch: 26};
        wsKibA['!cols'][3] = {wch: 18}; wsKibA['!cols'][4] = {wch: 32};
        wsKibA['!cols'][5] = {wch: 22}; wsKibA['!cols'][6] = {wch: 22};
        wsKibA['!cols'][7] = {wch: 32}; wsKibA['!cols'][8] = {wch: 22};
        wsKibA['!cols'][9] = {wch: 28}; wsKibA['!cols'][10] = {wch: 14};
        wsKibA['!cols'][11] = {wch: 18}; wsKibA['!cols'][12] = {wch: 24};
        wsKibA['!cols'][13] = {wch: 14}; wsKibA['!cols'][14] = {wch: 14};
        wsKibA['!cols'][15] = {wch: 22}; wsKibA['!cols'][16] = {wch: 18};
        wsKibA['!cols'][17] = {wch: 16}; wsKibA['!cols'][18] = {wch: 22};
        wsKibA['!cols'][19] = {wch: 28}; wsKibA['!cols'][20] = {wch: 26};
        wsKibA['!cols'][21] = {wch: 22}; wsKibA['!cols'][22] = {wch: 26};
        wsKibA['!cols'][23] = {wch: 24}; wsKibA['!cols'][24] = {wch: 22};
        wsKibA['!cols'][25] = {wch: 24};

        wsKibA['!merges'] = getKemitraanKibMerges([
            {s:{r:3,c:0}, e:{r:6,c:0}},
            {s:{r:3,c:1}, e:{r:3,c:6}},
            {s:{r:4,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},
            {s:{r:5,c:2}, e:{r:6,c:2}},
            {s:{r:4,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},
            {s:{r:5,c:4}, e:{r:6,c:4}},
            {s:{r:4,c:5}, e:{r:6,c:5}},
            {s:{r:4,c:6}, e:{r:6,c:6}},
            {s:{r:3,c:7}, e:{r:3,c:18}},
            {s:{r:4,c:7}, e:{r:6,c:7}},
            {s:{r:4,c:8}, e:{r:6,c:8}},
            {s:{r:4,c:9}, e:{r:4,c:11}},
            {s:{r:5,c:9}, e:{r:6,c:9}},
            {s:{r:5,c:10}, e:{r:5,c:11}},
            {s:{r:4,c:12}, e:{r:5,c:13}},
            {s:{r:4,c:14}, e:{r:6,c:14}},
            {s:{r:4,c:15}, e:{r:6,c:15}},
            {s:{r:4,c:16}, e:{r:4,c:17}},
            {s:{r:5,c:16}, e:{r:6,c:16}},
            {s:{r:5,c:17}, e:{r:6,c:17}},
            {s:{r:4,c:18}, e:{r:6,c:18}},
            {s:{r:3,c:19}, e:{r:6,c:19}},
            {s:{r:3,c:20}, e:{r:4,c:22}},
            {s:{r:5,c:20}, e:{r:6,c:20}},
            {s:{r:5,c:21}, e:{r:6,c:21}},
            {s:{r:5,c:22}, e:{r:6,c:22}},
            {s:{r:3,c:23}, e:{r:4,c:24}},
            {s:{r:5,c:23}, e:{r:6,c:23}},
            {s:{r:5,c:24}, e:{r:6,c:24}},
            {s:{r:3,c:25}, e:{r:6,c:25}}
        ], 26, kibATitleRows.length, kibARows.length, true);
        wsKibA['!merges'].push(...getKibSignatureMerges(kibASignStartRow, 26, 17, 1, 6, 25));

        applyKemitraanMasterSheetStyling(wsKibA, kibARows.length, 26, 12, kibATitleRows.length, true);
        applySignatureBlockStyling(wsKibA, kibASignStartRow, 26);
        if (filterCat === 'all' || filterCat === 'KIB A') {
            XLSX.utils.book_append_sheet(wb, wsKibA, filterCat === 'all' ? "2. KIB A - Tanah" : "KIB A - Tanah");
        }

        // ========================================================================
        // 3. KIB B (PERALATAN DAN MESIN KEMITRAAN)
        // ========================================================================
        const kibBTitleRows = getKemitraanKibTitleRows("PERALATAN DAN MESIN KEMITRAAN", yearLabel, filterTw, skemaLabel);
        const kibBRows = [
            ...kibBTitleRows,
            [
                "NO",
                "ASET KEMITRAAN (AKUN 1.5.2)", "", "", "", "", "",
                "RINCIAN ASET KEMITRAAN SESUAI PKS / PERJANJIAN KERJA SAMA / " + yearLabel,
                "", "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "",
                "RUANG /\nPEMEGANG",
                "PIHAK PENYEDIA / MITRA", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            [
                "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH TAKSIRAN (Rp)",
                "NILAI ASET WAJAR (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "Merk", "Type", "Ukuran / CC",
                "No. Pabrik", "No. Rangka", "No. Mesin", "No. BPKB", "No. POLISI",
                "BAHAN", "Tahun Perolehan",
                "Riwayat Kerja Sama / PKS", "",
                "Kondisi\n(B,KB,RB)",
                "VOLUME", "",
                "Nilai Barang (Rp)", "",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "", "",
                "", "", "", "", "", "", "", "", "", "", "", "",
                "PKS", "",
                "",
                "Jumlah Barang", "Nama Satuan Barang",
                "Nilai Satuan Barang (Rp)", "Total Nilai Barang (Rp)",
                "",
                "Nama Mitra Rekanan", "Pimpinan Mitra", "Alamat Mitra",
                "Nama", "NIP",
                ""
            ],
            [
                "",
                "", "", "", "", "", "",
                "", "", "", "", "", "", "", "", "", "", "", "", "",
                "Nomor", "Tanggal",
                "",
                "", "",
                "", "",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32", "33"
            ]
        ];

        const kibBGroups = {};
        categories['KIB B'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.5.2.01.01.04');
            if (!kibBGroups[groupKey]) kibBGroups[groupKey] = [];
            kibBGroups[groupKey].push(item);
        });

        let globalKibBNo = 1;
        let kibBTotalAnggaran = 0, kibBTotalRealisasi = 0, kibBTotalUnit = 0, kibBTotalNilaiBarang = 0;

        Object.keys(kibBGroups).forEach(groupKey => {
            const groupItems = kibBGroups[groupKey];
            let groupAnggaranTotal = 0;
            let groupRealisasiTotal = 0;

            groupItems.forEach(it => {
                const k = it.kemitraan || {};
                const val = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0);
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || val));
                groupRealisasiTotal += val;
            });

            kibBTotalAnggaran += groupAnggaranTotal;
            kibBTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const k = item.kemitraan || {};
                const spec = getSafeSpec(item);
                const subMesinItems = (spec.mesin_items && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0)
                    ? spec.mesin_items
                    : null;

                if (subMesinItems) {
                    subMesinItems.forEach((mItem) => {
                        const qty = Math.max(1, parseInt(mItem.mesin_jumlah_barang) || 1);
                        const nilaiSatuan = parseFloat(mItem.mesin_nilai_satuan) || (parseFloat(k.nilai_aset) / qty) || 0;
                        const adminProyek = parseFloat(mItem.mesin_administrasi_proyek) || 0;
                        const totalNilaiBarang = (qty * nilaiSatuan) + adminProyek;

                        kibBTotalUnit += qty;
                        kibBTotalNilaiBarang += totalNilaiBarang;

                        const rawKondisi = mItem.mesin_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));
                        const ruangUnit = mItem.ruang_pemegang || spec.ruang_pemegang || item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnandi');

                        const col1to7 = buildKemitraanCol1to7(item, globalKibBNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'PERALATAN DAN MESIN');
                        if (isFirstRowInGroup) { globalKibBNo++; isFirstRowInGroup = false; }

                        kibBRows.push([
                            ...col1to7,
                            mItem.mesin_nama_barang || item.nama_barang || '-',
                            mItem.mesin_kode_barang || spec.mesin_kode_barang || item.kode_barang || '1.5.2.01.01.04.002',
                            mItem.mesin_merk || spec.merk || item.merk || '-',
                            mItem.mesin_type || spec.type || item.type || '-',
                            mItem.mesin_ukuran || spec.ukuran || item.ukuran || '-',
                            mItem.mesin_no_pabrik || spec.no_pabrik || item.no_pabrik || '-',
                            mItem.mesin_no_rangka || spec.no_rangka || item.no_rangka || '-',
                            mItem.mesin_no_mesin || spec.no_mesin || item.no_mesin || '-',
                            mItem.mesin_no_bpkb || spec.no_bpkb || item.no_bpkb || '-',
                            mItem.mesin_no_polisi || spec.no_polisi || item.no_polisi || '-',
                            mItem.mesin_bahan || spec.bahan || item.bahan || '-',
                            mItem.mesin_tahun_pembuatan || spec.tahun_pembuatan || item.tahun_perolehan || '-',
                            ...getKemitraanKontrakCols(item),
                            kondisiLabel,
                            qty,
                            mItem.mesin_satuan || item.satuan || 'Unit',
                            nilaiSatuan,
                            totalNilaiBarang,
                            ruangUnit,
                            ...getKemitraanStep4Cols(item)
                        ]);
                    });
                } else {
                    const totalVal = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(item.total_realisasi_num) || parseFloat(item.total_realisasi) || 0);
                    const nilaiSatuan = typeof item.harga_satuan_num === 'number' ? item.harga_satuan_num : (parseFloat(item.harga_satuan) || totalVal);
                    const adminProyek = typeof item.biaya_administrasi_proyek_num === 'number' ? item.biaya_administrasi_proyek_num : (parseFloat(item.biaya_administrasi_proyek) || 0);
                    const jumlahBarang = typeof k.jumlah_volume === 'number' ? k.jumlah_volume : (parseInt(item.jumlah_volume) || 1);
                    const totalNilaiBarang = totalVal || ((jumlahBarang * nilaiSatuan) + adminProyek);
                    const ruangUnit = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnandi');

                    kibBTotalUnit += jumlahBarang;
                    kibBTotalNilaiBarang += totalNilaiBarang;

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    const col1to7 = buildKemitraanCol1to7(item, globalKibBNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'PERALATAN DAN MESIN');
                    if (isFirstRowInGroup) { globalKibBNo++; isFirstRowInGroup = false; }

                    kibBRows.push([
                        ...col1to7,
                        item.nama_barang || '-',
                        spec.mesin_kode_barang || item.kode_barang || '1.5.2.01.01.04.002',
                        item.merk || spec.merk || '-',
                        item.type || spec.type || '-',
                        item.ukuran || spec.ukuran || '-',
                        item.no_pabrik || spec.no_pabrik || '-',
                        item.no_rangka || spec.no_rangka || '-',
                        item.no_mesin || spec.no_mesin || '-',
                        item.no_btkb || spec.no_bpkb || '-',
                        item.no_polisi || spec.no_polisi || '-',
                        item.bahan || spec.bahan || '-',
                        item.tahun_perolehan || spec.tahun_pembuatan || '-',
                        ...getKemitraanKontrakCols(item),
                        kondisiLabel,
                        jumlahBarang,
                        item.satuan || 'Unit',
                        nilaiSatuan,
                        totalNilaiBarang,
                        ruangUnit,
                        ...getKemitraanStep4Cols(item)
                    ]);
                }
            });
        });

        // Baris Footer Total KIB B
        const kibBFooterRow = Array(33).fill("");
        kibBFooterRow[0] = "JUMLAH";
        kibBFooterRow[5] = kibBTotalAnggaran;
        kibBFooterRow[6] = kibBTotalRealisasi;
        kibBFooterRow[22] = kibBTotalUnit;
        kibBFooterRow[25] = kibBTotalNilaiBarang;
        kibBRows.push(kibBFooterRow);

        const kibBSignStartRow = kibBRows.length;
        const kibBSignRows = buildKemitraanSignRows(33, 22, 1);
        kibBSignRows.forEach(r => kibBRows.push(r));

        const wsKibB = XLSX.utils.aoa_to_sheet(kibBRows);
        wsKibB['!cols'] = Array(33).fill({wch: 18});
        wsKibB['!cols'][0] = {wch: 6};
        wsKibB['!cols'][1] = {wch: 14}; wsKibB['!cols'][2] = {wch: 26};
        wsKibB['!cols'][3] = {wch: 18}; wsKibB['!cols'][4] = {wch: 32};
        wsKibB['!cols'][5] = {wch: 22}; wsKibB['!cols'][6] = {wch: 22};
        wsKibB['!cols'][7] = {wch: 32}; wsKibB['!cols'][8] = {wch: 22};
        wsKibB['!cols'][9] = {wch: 20}; wsKibB['!cols'][10] = {wch: 20};
        wsKibB['!cols'][11] = {wch: 22}; wsKibB['!cols'][12] = {wch: 20};
        wsKibB['!cols'][13] = {wch: 20}; wsKibB['!cols'][14] = {wch: 20};
        wsKibB['!cols'][15] = {wch: 20}; wsKibB['!cols'][16] = {wch: 16};
        wsKibB['!cols'][17] = {wch: 16}; wsKibB['!cols'][18] = {wch: 14};
        wsKibB['!cols'][19] = {wch: 22}; wsKibB['!cols'][20] = {wch: 14};
        wsKibB['!cols'][21] = {wch: 14}; wsKibB['!cols'][22] = {wch: 14};
        wsKibB['!cols'][23] = {wch: 16}; wsKibB['!cols'][24] = {wch: 22};
        wsKibB['!cols'][25] = {wch: 22}; wsKibB['!cols'][26] = {wch: 20};
        wsKibB['!cols'][27] = {wch: 28}; wsKibB['!cols'][28] = {wch: 24};
        wsKibB['!cols'][29] = {wch: 30}; wsKibB['!cols'][30] = {wch: 24};
        wsKibB['!cols'][31] = {wch: 22}; wsKibB['!cols'][32] = {wch: 26};

        wsKibB['!merges'] = getKemitraanKibMerges([
            {s:{r:3,c:0}, e:{r:6,c:0}},
            {s:{r:3,c:1}, e:{r:3,c:6}},
            {s:{r:4,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},
            {s:{r:5,c:2}, e:{r:6,c:2}},
            {s:{r:4,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},
            {s:{r:5,c:4}, e:{r:6,c:4}},
            {s:{r:4,c:5}, e:{r:6,c:5}},
            {s:{r:4,c:6}, e:{r:6,c:6}},
            {s:{r:3,c:7}, e:{r:3,c:25}},
            {s:{r:4,c:7}, e:{r:6,c:7}},
            {s:{r:4,c:8}, e:{r:6,c:8}},
            {s:{r:4,c:9}, e:{r:6,c:9}},
            {s:{r:4,c:10}, e:{r:6,c:10}},
            {s:{r:4,c:11}, e:{r:6,c:11}},
            {s:{r:4,c:12}, e:{r:6,c:12}},
            {s:{r:4,c:13}, e:{r:6,c:13}},
            {s:{r:4,c:14}, e:{r:6,c:14}},
            {s:{r:4,c:15}, e:{r:6,c:15}},
            {s:{r:4,c:16}, e:{r:6,c:16}},
            {s:{r:4,c:17}, e:{r:6,c:17}},
            {s:{r:4,c:18}, e:{r:6,c:18}},
            {s:{r:4,c:19}, e:{r:4,c:20}},
            {s:{r:5,c:19}, e:{r:5,c:20}},
            {s:{r:4,c:21}, e:{r:6,c:21}},
            {s:{r:4,c:22}, e:{r:4,c:23}},
            {s:{r:5,c:22}, e:{r:6,c:22}},
            {s:{r:5,c:23}, e:{r:6,c:23}},
            {s:{r:4,c:24}, e:{r:4,c:25}},
            {s:{r:5,c:24}, e:{r:6,c:24}},
            {s:{r:5,c:25}, e:{r:6,c:25}},
            {s:{r:3,c:26}, e:{r:6,c:26}},
            {s:{r:3,c:27}, e:{r:4,c:29}},
            {s:{r:5,c:27}, e:{r:6,c:27}},
            {s:{r:5,c:28}, e:{r:6,c:28}},
            {s:{r:5,c:29}, e:{r:6,c:29}},
            {s:{r:3,c:30}, e:{r:4,c:31}},
            {s:{r:5,c:30}, e:{r:6,c:30}},
            {s:{r:5,c:31}, e:{r:6,c:31}},
            {s:{r:3,c:32}, e:{r:6,c:32}}
        ], 33, kibBTitleRows.length, kibBRows.length, true);
        wsKibB['!merges'].push(...getKibSignatureMerges(kibBSignStartRow, 33, 22, 1, 6, 32));

        applyKemitraanMasterSheetStyling(wsKibB, kibBRows.length, 33, 19, kibBTitleRows.length, true);
        applySignatureBlockStyling(wsKibB, kibBSignStartRow, 33);
        if (filterCat === 'all' || filterCat === 'KIB B') {
            XLSX.utils.book_append_sheet(wb, wsKibB, filterCat === 'all' ? "3. KIB B - Mesin & Alkes" : "KIB B - Peralatan & Mesin");
        }

        // ========================================================================
        // 4. KIB C (GEDUNG DAN BANGUNAN / BGS/BSG)
        // ========================================================================
        const kibCTitleRows = getKemitraanKibTitleRows("GEDUNG DAN BANGUNAN (BGS/BSG)", yearLabel, filterTw, skemaLabel);
        const kibCRows = [
            ...kibCTitleRows,
            [
                "NO",
                "ASET KEMITRAAN (AKUN 1.5.2)", "", "", "", "", "",
                "RINCIAN ASET KEMITRAAN SESUAI PKS / PERJANJIAN KERJA SAMA / " + yearLabel, "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "Letak/ Alamat",
                "PIHAK PENYEDIA / MITRA", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            [
                "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH TAKSIRAN (Rp)",
                "NILAI ASET WAJAR (Rp)",
                "NAMA BARANG (Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang (Kode Sub Sub Rincian Objek PMDN 108)",
                "Luas Lantai (m²)",
                "Kondisi / Spesifikasi", "", "",
                "Jenis Bangunan", "", "", "", "", "",
                "Riwayat Kerja Sama / PKS", "",
                "VOLUME", "",
                "Total Nilai Barang (Rp)",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "",
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
                "PKS", "",
                "Jumlah Bangunan", "Nama Satuan Barang",
                "",
                "",
                "Nama Mitra Rekanan", "Pimpinan Mitra", "Alamat Mitra",
                "Nama", "NIP",
                ""
            ],
            [
                "", "", "", "", "", "", "",
                "", "", "", "", "", "",
                "", "", "",
                "Nibar", "Tahun Induk", "Nilai Induk s/d " + (parseInt(yearLabel) - 1 || '2025'),
                "Nomor", "Tanggal",
                "", "",
                "",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31"
            ]
        ];

        const kibCGroups = {};
        categories['KIB C'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.5.2.01.01.03');
            if (!kibCGroups[groupKey]) kibCGroups[groupKey] = [];
            kibCGroups[groupKey].push(item);
        });

        let globalKibCNo = 1;
        let kibCTotalAnggaran = 0, kibCTotalRealisasi = 0, kibCTotalLuas = 0, kibCTotalUnit = 0;
        let kibCTotalNilaiBarang = 0;

        Object.keys(kibCGroups).forEach(groupKey => {
            const groupItems = kibCGroups[groupKey];
            let groupAnggaranTotal = 0;
            let groupRealisasiTotal = 0;

            groupItems.forEach(it => {
                const k = it.kemitraan || {};
                const val = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0);
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || val));
                groupRealisasiTotal += val;
            });

            kibCTotalAnggaran += groupAnggaranTotal;
            kibCTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const k = item.kemitraan || {};
                const spec = getSafeSpec(item);
                const subGedungItems = (spec.gedung_items && Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0)
                    ? spec.gedung_items
                    : null;

                if (subGedungItems) {
                    subGedungItems.forEach((gItem, gIdx) => {
                        const nilaiPerencanaan = parseFloat(gItem.gedung_nilai_perencanaan) || 0;
                        const nilaiFisik = parseFloat(gItem.gedung_nilai_satuan) || parseFloat(gItem.gedung_nilai_fisik) || (parseFloat(k.nilai_aset) || parseFloat(item.total_realisasi) || 0);
                        const nilaiPengawasan = parseFloat(gItem.gedung_nilai_pengawasan) || 0;
                        const nilaiAp = parseFloat(gItem.gedung_nilai_ap) || 0;
                        const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan + nilaiAp) || nilaiFisik;
                        const jumlahBangunan = Math.max(1, parseInt(gItem.gedung_jumlah_bangunan) || 1);
                        const luasM2 = parseFloat(gItem.gedung_luas_lantai) || parseFloat(gItem.gedung_luas_m2) || 0;

                        kibCTotalLuas += luasM2;
                        kibCTotalUnit += jumlahBangunan;
                        kibCTotalNilaiBarang += totalNilaiBarang;

                        const rawKondisi = gItem.gedung_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                        const isGedungBaru = (gItem.gedung_is_baru === 'Baru' || !gItem.gedung_is_baru);

                        const col1to7 = buildKemitraanCol1to7(item, globalKibCNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'GEDUNG DAN BANGUNAN');
                        if (isFirstRowInGroup) { globalKibCNo++; isFirstRowInGroup = false; }

                        kibCRows.push([
                            ...col1to7,
                            gItem.gedung_nama_barang || item.nama_barang || '-',
                            gItem.gedung_kode_barang || spec.gedung_kode_barang || item.kode_barang || '1.5.2.01.01.03.003',
                            luasM2,
                            kondisiLabel,
                            gItem.gedung_bertingkat || 'Bertingkat',
                            gItem.gedung_beton || 'Beton',
                            gItem.gedung_status_tanah || 'Tanah Hak Pakai RSUD',
                            gItem.gedung_kode_aset_tanah || '-',
                            isGedungBaru ? '1' : '-',
                            getAstapNibar(item, gItem, gIdx),
                            isGedungBaru ? '-' : (gItem.gedung_kapitalisasi_tahun_induk || '-'),
                            isGedungBaru ? '-' : (parseFloat(gItem.gedung_kapitalisasi_nilai_induk) || 0),
                            ...getKemitraanKontrakCols(item),
                            jumlahBangunan,
                            gItem.gedung_satuan || item.satuan || 'Bangunan',
                            totalNilaiBarang,
                            gItem.gedung_alamat || item.alamat_barang || '-',
                            ...getKemitraanStep4Cols(item)
                        ]);
                    });
                } else {
                    const totalVal = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(item.total_realisasi_num) || parseFloat(item.total_realisasi) || 0);
                    const nilaiPerencanaan = parseFloat(item.nilai_perencanaan) || 0;
                    const nilaiFisik = parseFloat(item.nilai_fisik) || totalVal;
                    const nilaiPengawasan = parseFloat(item.nilai_pengawasan) || 0;
                    const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan) || totalVal;
                    const jumlahBangunan = parseInt(item.jumlah_volume) || 1;
                    const luasM2 = parseFloat(spec.luas_m2 || item.luas_m2) || 0;

                    kibCTotalLuas += luasM2;
                    kibCTotalUnit += jumlahBangunan;
                    kibCTotalNilaiBarang += totalNilaiBarang;

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    const isGedungBaru = true;

                    const col1to7 = buildKemitraanCol1to7(item, globalKibCNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'GEDUNG DAN BANGUNAN');
                    if (isFirstRowInGroup) { globalKibCNo++; isFirstRowInGroup = false; }

                    kibCRows.push([
                        ...col1to7,
                        item.nama_barang || '-',
                        spec.gedung_kode_barang || item.kode_barang || '1.5.2.01.01.03.003',
                        luasM2,
                        kondisiLabel,
                        spec.gedung_bertingkat || 'Bertingkat',
                        spec.gedung_beton || 'Beton',
                        spec.gedung_status_tanah || 'Tanah Hak Pakai RSUD',
                        spec.gedung_kode_aset_tanah || '-',
                        isGedungBaru ? '1' : '-',
                        getAstapNibar(item),
                        '-',
                        0,
                        ...getKemitraanKontrakCols(item),
                        jumlahBangunan,
                        item.satuan || 'Bangunan',
                        totalNilaiBarang,
                        item.alamat_barang || '-',
                        ...getKemitraanStep4Cols(item)
                    ]);
                }
            });
        });

        // Baris Footer Total KIB C
        const kibCFooterRow = Array(31).fill("");
        kibCFooterRow[0] = "JUMLAH";
        kibCFooterRow[5] = kibCTotalAnggaran;
        kibCFooterRow[6] = kibCTotalRealisasi;
        kibCFooterRow[9] = kibCTotalLuas;
        kibCFooterRow[21] = kibCTotalUnit;
        kibCFooterRow[23] = kibCTotalNilaiBarang;
        kibCRows.push(kibCFooterRow);

        const kibCSignStartRow = kibCRows.length;
        const kibCSignRows = buildKemitraanSignRows(31, 21, 1);
        kibCSignRows.forEach(r => kibCRows.push(r));

        const wsKibC = XLSX.utils.aoa_to_sheet(kibCRows);
        wsKibC['!cols'] = Array(31).fill({wch: 18});
        wsKibC['!cols'][0] = {wch: 6};
        wsKibC['!cols'][1] = {wch: 14}; wsKibC['!cols'][2] = {wch: 26};
        wsKibC['!cols'][3] = {wch: 18}; wsKibC['!cols'][4] = {wch: 32};
        wsKibC['!cols'][5] = {wch: 22}; wsKibC['!cols'][6] = {wch: 22};
        wsKibC['!cols'][7] = {wch: 32}; wsKibC['!cols'][8] = {wch: 22};
        wsKibC['!cols'][9] = {wch: 16}; wsKibC['!cols'][10] = {wch: 14};
        wsKibC['!cols'][11] = {wch: 20}; wsKibC['!cols'][12] = {wch: 18};
        wsKibC['!cols'][13] = {wch: 24}; wsKibC['!cols'][14] = {wch: 20};
        wsKibC['!cols'][15] = {wch: 16}; wsKibC['!cols'][16] = {wch: 22};
        wsKibC['!cols'][17] = {wch: 16}; wsKibC['!cols'][18] = {wch: 22};
        wsKibC['!cols'][19] = {wch: 22}; wsKibC['!cols'][20] = {wch: 14};
        wsKibC['!cols'][21] = {wch: 18}; wsKibC['!cols'][22] = {wch: 20};
        wsKibC['!cols'][23] = {wch: 22}; wsKibC['!cols'][24] = {wch: 24};
        wsKibC['!cols'][25] = {wch: 28}; wsKibC['!cols'][26] = {wch: 24};
        wsKibC['!cols'][27] = {wch: 30}; wsKibC['!cols'][28] = {wch: 24};
        wsKibC['!cols'][29] = {wch: 22}; wsKibC['!cols'][30] = {wch: 26};

        wsKibC['!merges'] = getKemitraanKibMerges([
            {s:{r:3,c:0}, e:{r:6,c:0}},
            {s:{r:3,c:1}, e:{r:3,c:6}},
            {s:{r:4,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},
            {s:{r:5,c:2}, e:{r:6,c:2}},
            {s:{r:4,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},
            {s:{r:5,c:4}, e:{r:6,c:4}},
            {s:{r:4,c:5}, e:{r:6,c:5}},
            {s:{r:4,c:6}, e:{r:6,c:6}},
            {s:{r:3,c:7}, e:{r:3,c:23}},
            {s:{r:4,c:7}, e:{r:6,c:7}},
            {s:{r:4,c:8}, e:{r:6,c:8}},
            {s:{r:4,c:9}, e:{r:6,c:9}},
            {s:{r:4,c:10}, e:{r:4,c:12}},
            {s:{r:5,c:10}, e:{r:6,c:10}},
            {s:{r:5,c:11}, e:{r:6,c:11}},
            {s:{r:5,c:12}, e:{r:6,c:12}},
            {s:{r:4,c:13}, e:{r:4,c:18}},
            {s:{r:5,c:13}, e:{r:6,c:13}},
            {s:{r:5,c:14}, e:{r:6,c:14}},
            {s:{r:5,c:15}, e:{r:6,c:15}},
            {s:{r:5,c:16}, e:{r:5,c:18}},
            {s:{r:4,c:19}, e:{r:4,c:20}},
            {s:{r:5,c:19}, e:{r:5,c:20}},
            {s:{r:4,c:21}, e:{r:4,c:22}},
            {s:{r:5,c:21}, e:{r:6,c:21}},
            {s:{r:5,c:22}, e:{r:6,c:22}},
            {s:{r:4,c:23}, e:{r:6,c:23}},
            {s:{r:3,c:24}, e:{r:6,c:24}},
            {s:{r:3,c:25}, e:{r:4,c:27}},
            {s:{r:5,c:25}, e:{r:6,c:25}},
            {s:{r:5,c:26}, e:{r:6,c:26}},
            {s:{r:5,c:27}, e:{r:6,c:27}},
            {s:{r:3,c:28}, e:{r:4,c:29}},
            {s:{r:5,c:28}, e:{r:6,c:28}},
            {s:{r:5,c:29}, e:{r:6,c:29}},
            {s:{r:3,c:30}, e:{r:6,c:30}}
        ], 31, kibCTitleRows.length, kibCRows.length, true);
        wsKibC['!merges'].push(...getKibSignatureMerges(kibCSignStartRow, 31, 21, 1, 6, 30));

        applyKemitraanMasterSheetStyling(wsKibC, kibCRows.length, 31, 17, kibCTitleRows.length, true);
        applySignatureBlockStyling(wsKibC, kibCSignStartRow, 31);
        if (filterCat === 'all' || filterCat === 'KIB C') {
            XLSX.utils.book_append_sheet(wb, wsKibC, filterCat === 'all' ? "4. KIB C - Gedung" : "KIB C - Gedung & Bangunan");
        }

        // ========================================================================
        // 5. KIB D (JALAN, IRIGASI DAN JARINGAN)
        // ========================================================================
        const kibDTitleRows = getKemitraanKibTitleRows("JALAN, IRIGASI DAN JARINGAN", yearLabel, filterTw, skemaLabel);
        const kibDRows = [
            ...kibDTitleRows,
            [
                "NO",
                "ASET KEMITRAAN (AKUN 1.5.2)", "", "", "", "", "",
                "RINCIAN ASET KEMITRAAN SESUAI PKS / PERJANJIAN KERJA SAMA / " + yearLabel, "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                "Letak/ Alamat",
                "PIHAK PENYEDIA / MITRA", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            [
                "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH TAKSIRAN (Rp)",
                "NILAI ASET WAJAR (Rp)",
                "NAMA BARANG (Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang (Kode Sub Sub Rincian Objek PMDN 108)",
                "Luas / Panjang (m² / m)",
                "Kondisi / Konstruksi", "", "",
                "Jenis Jaringan / Fasilitas", "", "", "", "", "",
                "Riwayat Kerja Sama / PKS", "",
                "VOLUME", "",
                "Total Nilai Barang (Rp)",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "",
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
                "PKS", "",
                "Jumlah Unit", "Nama Satuan Barang",
                "",
                "",
                "Nama Mitra Rekanan", "Pimpinan Mitra", "Alamat Mitra",
                "Nama", "NIP",
                ""
            ],
            [
                "", "", "", "", "", "", "",
                "", "", "", "", "", "",
                "", "", "",
                "Nibar", "Tahun Induk", "Nilai Induk s/d " + (parseInt(yearLabel) - 1 || '2025'),
                "Nomor", "Tanggal",
                "", "",
                "",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31"
            ]
        ];

        const kibDGroups = {};
        categories['KIB D'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.5.2.01.01.04');
            if (!kibDGroups[groupKey]) kibDGroups[groupKey] = [];
            kibDGroups[groupKey].push(item);
        });

        let kibDTotalAnggaran = 0, kibDTotalRealisasi = 0, kibDTotalLuas = 0, kibDTotalUnit = 0;
        let kibDTotalNilaiBarang = 0;
        let globalKibDNo = 1;

        Object.keys(kibDGroups).forEach(groupKey => {
            const groupItems = kibDGroups[groupKey];
            let groupAnggaranTotal = 0;
            let groupRealisasiTotal = 0;

            groupItems.forEach(it => {
                const k = it.kemitraan || {};
                const val = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0);
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || val));
                groupRealisasiTotal += val;
            });

            kibDTotalAnggaran += groupAnggaranTotal;
            kibDTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const k = item.kemitraan || {};
                const spec = getSafeSpec(item);
                const subJaringanItems = (spec.jaringan_items && Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0)
                    ? spec.jaringan_items
                    : null;

                if (subJaringanItems) {
                    subJaringanItems.forEach((jItem, jIdx) => {
                        const nilaiPerencanaan = parseFloat(jItem.jaringan_nilai_perencanaan) || 0;
                        const nilaiFisik = parseFloat(jItem.jaringan_nilai_satuan) || parseFloat(jItem.jaringan_nilai_fisik) || (parseFloat(k.nilai_aset) || parseFloat(item.total_realisasi) || 0);
                        const nilaiPengawasan = parseFloat(jItem.jaringan_nilai_pengawasan) || 0;
                        const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan) || nilaiFisik;
                        const jumlahUnit = Math.max(1, parseInt(jItem.jaringan_jumlah) || 1);
                        const luasM2 = parseFloat(jItem.jaringan_luas) || (parseFloat(jItem.jaringan_panjang) && parseFloat(jItem.jaringan_lebar) ? parseFloat(jItem.jaringan_panjang) * parseFloat(jItem.jaringan_lebar) : 0);

                        kibDTotalLuas += luasM2;
                        kibDTotalUnit += jumlahUnit;
                        kibDTotalNilaiBarang += totalNilaiBarang;

                        const rawKondisi = jItem.jaringan_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                        const isBaru = (jItem.jaringan_is_baru === 'Baru' || !jItem.jaringan_is_baru);

                        const col1to7 = buildKemitraanCol1to7(item, globalKibDNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'JALAN, IRIGASI DAN JARINGAN');
                        if (isFirstRowInGroup) { globalKibDNo++; isFirstRowInGroup = false; }

                        kibDRows.push([
                            ...col1to7,
                            jItem.jaringan_nama_barang || item.nama_barang || '-',
                            jItem.jaringan_kode_barang || spec.jaringan_kode_barang || item.kode_barang || '1.5.2.01.01.04.004',
                            luasM2,
                            kondisiLabel,
                            jItem.jaringan_bertingkat || '-',
                            jItem.jaringan_beton || '-',
                            jItem.jaringan_status_tanah || 'Tanah Hak Pakai RSUD',
                            jItem.jaringan_kode_aset_tanah || '-',
                            isBaru ? '1' : '-',
                            getAstapNibar(item, jItem, jIdx),
                            isBaru ? '-' : (jItem.jaringan_kapitalisasi_tahun_induk || '-'),
                            isBaru ? '-' : (parseFloat(jItem.jaringan_kapitalisasi_nilai_induk) || 0),
                            ...getKemitraanKontrakCols(item),
                            jumlahUnit,
                            jItem.jaringan_satuan || item.satuan || 'Paket / Jaringan',
                            totalNilaiBarang,
                            jItem.jaringan_alamat || item.alamat_barang || '-',
                            ...getKemitraanStep4Cols(item)
                        ]);
                    });
                } else {
                    const totalVal = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(item.total_realisasi_num) || parseFloat(item.total_realisasi) || 0);
                    const nilaiPerencanaan = parseFloat(item.nilai_perencanaan) || 0;
                    const nilaiFisik = parseFloat(item.nilai_fisik) || totalVal;
                    const nilaiPengawasan = parseFloat(item.nilai_pengawasan) || 0;
                    const totalNilaiBarang = (nilaiPerencanaan + nilaiFisik + nilaiPengawasan) || totalVal;
                    const jumlahUnit = parseInt(item.jumlah_volume) || 1;
                    const luasM2 = parseFloat(spec.luas_m2 || item.luas_m2) || 0;

                    kibDTotalLuas += luasM2;
                    kibDTotalUnit += jumlahUnit;
                    kibDTotalNilaiBarang += totalNilaiBarang;

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    const isBaru = true;

                    const col1to7 = buildKemitraanCol1to7(item, globalKibDNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'JALAN, IRIGASI DAN JARINGAN');
                    if (isFirstRowInGroup) { globalKibDNo++; isFirstRowInGroup = false; }

                    kibDRows.push([
                        ...col1to7,
                        item.nama_barang || '-',
                        spec.jaringan_kode_barang || item.kode_barang || '1.5.2.01.01.04.004',
                        luasM2,
                        kondisiLabel,
                        spec.jaringan_bertingkat || '-',
                        spec.jaringan_beton || '-',
                        spec.jaringan_status_tanah || 'Tanah Hak Pakai RSUD',
                        spec.jaringan_kode_aset_tanah || '-',
                        isBaru ? '1' : '-',
                        getAstapNibar(item),
                        '-',
                        0,
                        ...getKemitraanKontrakCols(item),
                        jumlahUnit,
                        item.satuan || 'Paket / Jaringan',
                        totalNilaiBarang,
                        item.alamat_barang || '-',
                        ...getKemitraanStep4Cols(item)
                    ]);
                }
            });
        });

        // Baris Footer Total KIB D
        const kibDFooterRow = Array(31).fill("");
        kibDFooterRow[0] = "JUMLAH";
        kibDFooterRow[5] = kibDTotalAnggaran;
        kibDFooterRow[6] = kibDTotalRealisasi;
        kibDFooterRow[9] = kibDTotalLuas;
        kibDFooterRow[21] = kibDTotalUnit;
        kibDFooterRow[23] = kibDTotalNilaiBarang;
        kibDRows.push(kibDFooterRow);

        const kibDSignStartRow = kibDRows.length;
        const kibDSignRows = buildKemitraanSignRows(31, 21, 1);
        kibDSignRows.forEach(r => kibDRows.push(r));

        const wsKibD = XLSX.utils.aoa_to_sheet(kibDRows);
        wsKibD['!cols'] = Array(31).fill({wch: 18});
        wsKibD['!cols'][0] = {wch: 6};
        wsKibD['!cols'][1] = {wch: 14}; wsKibD['!cols'][2] = {wch: 26};
        wsKibD['!cols'][3] = {wch: 18}; wsKibD['!cols'][4] = {wch: 32};
        wsKibD['!cols'][5] = {wch: 22}; wsKibD['!cols'][6] = {wch: 22};
        wsKibD['!cols'][7] = {wch: 32}; wsKibD['!cols'][8] = {wch: 22};
        wsKibD['!cols'][9] = {wch: 16}; wsKibD['!cols'][10] = {wch: 14};
        wsKibD['!cols'][11] = {wch: 20}; wsKibD['!cols'][12] = {wch: 18};
        wsKibD['!cols'][13] = {wch: 24}; wsKibD['!cols'][14] = {wch: 20};
        wsKibD['!cols'][15] = {wch: 16}; wsKibD['!cols'][16] = {wch: 22};
        wsKibD['!cols'][17] = {wch: 16}; wsKibD['!cols'][18] = {wch: 22};
        wsKibD['!cols'][19] = {wch: 22}; wsKibD['!cols'][20] = {wch: 14};
        wsKibD['!cols'][21] = {wch: 18}; wsKibD['!cols'][22] = {wch: 20};
        wsKibD['!cols'][23] = {wch: 22}; wsKibD['!cols'][24] = {wch: 24};
        wsKibD['!cols'][25] = {wch: 28}; wsKibD['!cols'][26] = {wch: 24};
        wsKibD['!cols'][27] = {wch: 30}; wsKibD['!cols'][28] = {wch: 24};
        wsKibD['!cols'][29] = {wch: 22}; wsKibD['!cols'][30] = {wch: 26};

        wsKibD['!merges'] = getKemitraanKibMerges([
            {s:{r:3,c:0}, e:{r:6,c:0}},
            {s:{r:3,c:1}, e:{r:3,c:6}},
            {s:{r:4,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},
            {s:{r:5,c:2}, e:{r:6,c:2}},
            {s:{r:4,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},
            {s:{r:5,c:4}, e:{r:6,c:4}},
            {s:{r:4,c:5}, e:{r:6,c:5}},
            {s:{r:4,c:6}, e:{r:6,c:6}},
            {s:{r:3,c:7}, e:{r:3,c:23}},
            {s:{r:4,c:7}, e:{r:6,c:7}},
            {s:{r:4,c:8}, e:{r:6,c:8}},
            {s:{r:4,c:9}, e:{r:6,c:9}},
            {s:{r:4,c:10}, e:{r:4,c:12}},
            {s:{r:5,c:10}, e:{r:6,c:10}},
            {s:{r:5,c:11}, e:{r:6,c:11}},
            {s:{r:5,c:12}, e:{r:6,c:12}},
            {s:{r:4,c:13}, e:{r:4,c:18}},
            {s:{r:5,c:13}, e:{r:6,c:13}},
            {s:{r:5,c:14}, e:{r:6,c:14}},
            {s:{r:5,c:15}, e:{r:6,c:15}},
            {s:{r:5,c:16}, e:{r:5,c:18}},
            {s:{r:4,c:19}, e:{r:4,c:20}},
            {s:{r:5,c:19}, e:{r:5,c:20}},
            {s:{r:4,c:21}, e:{r:4,c:22}},
            {s:{r:5,c:21}, e:{r:6,c:21}},
            {s:{r:5,c:22}, e:{r:6,c:22}},
            {s:{r:4,c:23}, e:{r:6,c:23}},
            {s:{r:3,c:24}, e:{r:6,c:24}},
            {s:{r:3,c:25}, e:{r:4,c:27}},
            {s:{r:5,c:25}, e:{r:6,c:25}},
            {s:{r:5,c:26}, e:{r:6,c:26}},
            {s:{r:5,c:27}, e:{r:6,c:27}},
            {s:{r:3,c:28}, e:{r:4,c:29}},
            {s:{r:5,c:28}, e:{r:6,c:28}},
            {s:{r:5,c:29}, e:{r:6,c:29}},
            {s:{r:3,c:30}, e:{r:6,c:30}}
        ], 31, kibDTitleRows.length, kibDRows.length, true);
        wsKibD['!merges'].push(...getKibSignatureMerges(kibDSignStartRow, 31, 21, 1, 6, 30));

        applyKemitraanMasterSheetStyling(wsKibD, kibDRows.length, 31, 17, kibDTitleRows.length, true);
        applySignatureBlockStyling(wsKibD, kibDSignStartRow, 31);
        if (filterCat === 'all' || filterCat === 'KIB D') {
            XLSX.utils.book_append_sheet(wb, wsKibD, filterCat === 'all' ? "5. KIB D - Jaringan" : "KIB D - Jalan & Jaringan");
        }

        // ========================================================================
        // 6. KIB E (ASET TETAP LAINNYA)
        // ========================================================================
        const kibETitleRows = getKemitraanKibTitleRows("ASET TETAP LAINNYA", yearLabel, filterTw, skemaLabel);
        const kibERows = [
            ...kibETitleRows,
            [
                "NO",
                "ASET KEMITRAAN (AKUN 1.5.2)", "", "", "", "", "",
                "RINCIAN ASET KEMITRAAN SESUAI PKS / PERJANJIAN KERJA SAMA / " + yearLabel,
                "", "", "", "", "", "", "", "", "", "",
                "", "", "", "", "", "", "",
                "RUANG /\nPEMEGANG",
                "PIHAK PENYEDIA / MITRA", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            [
                "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH TAKSIRAN (Rp)",
                "NILAI ASET WAJAR (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "BUKU PERPUSTAKAAN", "", "",
                "Barang Bercorak Kesenian / Kebudayaan", "", "", "",
                "Hewan Ternak / Tumbuhan", "", "",
                "Riwayat Kerja Sama / PKS", "",
                "VOLUME", "",
                "Nilai Barang (Rp)", "",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "", "",
                "",
                "",
                "Judul", "Pencipta", "Spesifikasi",
                "Asal Daerah", "Pencipta", "Spesifikasi", "Bahan",
                "Ukuran (m/cm)", "Judul", "Spesifikasi",
                "PKS", "",
                "Jumlah Barang", "Nama Satuan Barang",
                "Nilai Satuan Barang (Rp)", "Total Nilai Barang (Rp)",
                "",
                "Nama Mitra Rekanan", "Pimpinan Mitra", "Alamat Mitra",
                "Nama", "NIP",
                ""
            ],
            [
                "",
                "", "", "", "", "", "",
                "",
                "",
                "", "", "",
                "", "", "", "",
                "", "", "",
                "Nomor", "Tanggal",
                "", "",
                "", "",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29", "30", "31", "32"
            ]
        ];

        const kibEGroups = {};
        categories['KIB E'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.5.2.01.01.02');
            if (!kibEGroups[groupKey]) kibEGroups[groupKey] = [];
            kibEGroups[groupKey].push(item);
        });

        let globalKibENo = 1;
        let kibETotalAnggaran = 0, kibETotalRealisasi = 0, kibETotalUnit = 0, kibETotalNilaiBarang = 0;

        Object.keys(kibEGroups).forEach(groupKey => {
            const groupItems = kibEGroups[groupKey];
            let groupAnggaranTotal = 0;
            let groupRealisasiTotal = 0;

            groupItems.forEach(it => {
                const k = it.kemitraan || {};
                const val = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0);
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || val));
                groupRealisasiTotal += val;
            });

            kibETotalAnggaran += groupAnggaranTotal;
            kibETotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const k = item.kemitraan || {};
                const spec = getSafeSpec(item);
                const subLainnyaItems = (spec.lainnya_items && Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0)
                    ? spec.lainnya_items
                    : null;

                if (subLainnyaItems) {
                    subLainnyaItems.forEach((lItem) => {
                        const qty = Math.max(1, parseInt(lItem.lainnya_jumlah) || parseInt(lItem.lainnya_jumlah_barang) || 1);
                        const nilaiSatuan = parseFloat(lItem.lainnya_nilai_satuan) || (parseFloat(k.nilai_aset) / qty) || 0;
                        const adminProyek = parseFloat(lItem.lainnya_administrasi_proyek) || 0;
                        const totalNilaiBarang = (qty * nilaiSatuan) + adminProyek;

                        kibETotalUnit += qty;
                        kibETotalNilaiBarang += totalNilaiBarang;

                        const rawKondisi = lItem.lainnya_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));
                        const ruangUnit = lItem.ruang_pemegang_lainnya || lItem.ruang_pemegang || item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnandi');

                        const col1to7 = buildKemitraanCol1to7(item, globalKibENo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'ASET TETAP LAINNYA');
                        if (isFirstRowInGroup) { globalKibENo++; isFirstRowInGroup = false; }

                        kibERows.push([
                            ...col1to7,
                            lItem.lainnya_nama_barang || item.nama_barang || '-',
                            lItem.lainnya_kode_barang || spec.lainnya_kode_barang || item.kode_barang || '1.5.2.01.01.02.005',
                            lItem.lainnya_judul || spec.judul || '-',
                            lItem.lainnya_pencipta || spec.pencipta || '-',
                            lItem.lainnya_ukuran || spec.ukuran || lItem.lainnya_spesifikasi || '-',
                            lItem.lainnya_daerah || spec.daerah || '-',
                            lItem.lainnya_pencipta_seni || '-',
                            lItem.lainnya_spesifikasi_seni || '-',
                            lItem.lainnya_bahan || spec.bahan || '-',
                            '-', '-', '-',
                            ...getKemitraanKontrakCols(item),
                            kondisiLabel,
                            qty,
                            lItem.lainnya_satuan || item.satuan || 'Buah',
                            nilaiSatuan,
                            totalNilaiBarang,
                            ruangUnit,
                            ...getKemitraanStep4Cols(item)
                        ]);
                    });
                } else {
                    const totalVal = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(item.total_realisasi_num) || parseFloat(item.total_realisasi) || 0);
                    const nilaiSatuan = typeof item.harga_satuan_num === 'number' ? item.harga_satuan_num : (parseFloat(item.harga_satuan) || totalVal);
                    const adminProyek = typeof item.biaya_administrasi_proyek_num === 'number' ? item.biaya_administrasi_proyek_num : (parseFloat(item.biaya_administrasi_proyek) || 0);
                    const jumlahBarang = typeof k.jumlah_volume === 'number' ? k.jumlah_volume : (parseInt(item.jumlah_volume) || 1);
                    const totalNilaiBarang = totalVal || ((jumlahBarang * nilaiSatuan) + adminProyek);
                    const ruangUnit = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnandi');

                    kibETotalUnit += jumlahBarang;
                    kibETotalNilaiBarang += totalNilaiBarang;

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    const col1to7 = buildKemitraanCol1to7(item, globalKibENo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'ASET TETAP LAINNYA');
                    if (isFirstRowInGroup) { globalKibENo++; isFirstRowInGroup = false; }

                    kibERows.push([
                        ...col1to7,
                        item.nama_barang || '-',
                        spec.lainnya_kode_barang || item.kode_barang || '1.5.2.01.01.02.005',
                        spec.judul || '-',
                        spec.pencipta || '-',
                        spec.ukuran || spec.spesifikasi || '-',
                        spec.daerah || '-',
                        '-',
                        '-',
                        spec.bahan || '-',
                        '-', '-', '-',
                        ...getKemitraanKontrakCols(item),
                        kondisiLabel,
                        jumlahBarang,
                        item.satuan || 'Buah',
                        nilaiSatuan,
                        totalNilaiBarang,
                        ruangUnit,
                        ...getKemitraanStep4Cols(item)
                    ]);
                }
            });
        });

        // Baris Footer Total KIB E
        const kibEFooterRow = Array(32).fill("");
        kibEFooterRow[0] = "JUMLAH";
        kibEFooterRow[5] = kibETotalAnggaran;
        kibEFooterRow[6] = kibETotalRealisasi;
        kibEFooterRow[21] = kibETotalUnit;
        kibEFooterRow[24] = kibETotalNilaiBarang;
        kibERows.push(kibEFooterRow);

        const kibESignStartRow = kibERows.length;
        const kibESignRows = buildKemitraanSignRows(32, 21, 1);
        kibESignRows.forEach(r => kibERows.push(r));

        const wsKibE = XLSX.utils.aoa_to_sheet(kibERows);
        wsKibE['!cols'] = Array(32).fill({wch: 18});
        wsKibE['!cols'][0] = {wch: 6};
        wsKibE['!cols'][1] = {wch: 14}; wsKibE['!cols'][2] = {wch: 26};
        wsKibE['!cols'][3] = {wch: 18}; wsKibE['!cols'][4] = {wch: 32};
        wsKibE['!cols'][5] = {wch: 22}; wsKibE['!cols'][6] = {wch: 22};
        wsKibE['!cols'][7] = {wch: 32}; wsKibE['!cols'][8] = {wch: 22};
        wsKibE['!cols'][9] = {wch: 22}; wsKibE['!cols'][10] = {wch: 20};
        wsKibE['!cols'][11] = {wch: 22}; wsKibE['!cols'][12] = {wch: 20};
        wsKibE['!cols'][13] = {wch: 20}; wsKibE['!cols'][14] = {wch: 20};
        wsKibE['!cols'][15] = {wch: 18}; wsKibE['!cols'][16] = {wch: 16};
        wsKibE['!cols'][17] = {wch: 20}; wsKibE['!cols'][18] = {wch: 20};
        wsKibE['!cols'][19] = {wch: 22}; wsKibE['!cols'][20] = {wch: 14};
        wsKibE['!cols'][21] = {wch: 14}; wsKibE['!cols'][22] = {wch: 14};
        wsKibE['!cols'][23] = {wch: 16}; wsKibE['!cols'][24] = {wch: 22};
        wsKibE['!cols'][25] = {wch: 22}; wsKibE['!cols'][26] = {wch: 20};
        wsKibE['!cols'][27] = {wch: 28}; wsKibE['!cols'][28] = {wch: 24};
        wsKibE['!cols'][29] = {wch: 30}; wsKibE['!cols'][30] = {wch: 24};
        wsKibE['!cols'][31] = {wch: 26};

        wsKibE['!merges'] = getKemitraanKibMerges([
            {s:{r:3,c:0}, e:{r:6,c:0}},
            {s:{r:3,c:1}, e:{r:3,c:6}},
            {s:{r:4,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},
            {s:{r:5,c:2}, e:{r:6,c:2}},
            {s:{r:4,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},
            {s:{r:5,c:4}, e:{r:6,c:4}},
            {s:{r:4,c:5}, e:{r:6,c:5}},
            {s:{r:4,c:6}, e:{r:6,c:6}},
            {s:{r:3,c:7}, e:{r:3,c:24}},
            {s:{r:4,c:7}, e:{r:6,c:7}},
            {s:{r:4,c:8}, e:{r:6,c:8}},
            {s:{r:4,c:9}, e:{r:4,c:11}},
            {s:{r:5,c:9}, e:{r:6,c:9}},
            {s:{r:5,c:10}, e:{r:6,c:10}},
            {s:{r:5,c:11}, e:{r:6,c:11}},
            {s:{r:4,c:12}, e:{r:4,c:15}},
            {s:{r:5,c:12}, e:{r:6,c:12}},
            {s:{r:5,c:13}, e:{r:6,c:13}},
            {s:{r:5,c:14}, e:{r:6,c:14}},
            {s:{r:5,c:15}, e:{r:6,c:15}},
            {s:{r:4,c:16}, e:{r:4,c:18}},
            {s:{r:5,c:16}, e:{r:6,c:16}},
            {s:{r:5,c:17}, e:{r:6,c:17}},
            {s:{r:5,c:18}, e:{r:6,c:18}},
            {s:{r:4,c:19}, e:{r:4,c:20}},
            {s:{r:5,c:19}, e:{r:5,c:20}},
            {s:{r:4,c:21}, e:{r:6,c:21}},
            {s:{r:4,c:22}, e:{r:4,c:23}},
            {s:{r:5,c:22}, e:{r:6,c:22}},
            {s:{r:5,c:23}, e:{r:6,c:23}},
            {s:{r:4,c:24}, e:{r:6,c:24}},
            {s:{r:3,c:25}, e:{r:6,c:25}},
            {s:{r:3,c:26}, e:{r:4,c:28}},
            {s:{r:5,c:26}, e:{r:6,c:26}},
            {s:{r:5,c:27}, e:{r:6,c:27}},
            {s:{r:5,c:28}, e:{r:6,c:28}},
            {s:{r:3,c:29}, e:{r:4,c:30}},
            {s:{r:5,c:29}, e:{r:6,c:29}},
            {s:{r:5,c:30}, e:{r:6,c:30}},
            {s:{r:3,c:31}, e:{r:6,c:31}}
        ], 32, kibETitleRows.length, kibERows.length, true);
        wsKibE['!merges'].push(...getKibSignatureMerges(kibESignStartRow, 32, 21, 1, 6, 31));

        applyKemitraanMasterSheetStyling(wsKibE, kibERows.length, 32, 18, kibETitleRows.length, true);
        applySignatureBlockStyling(wsKibE, kibESignStartRow, 32);
        if (filterCat === 'all' || filterCat === 'KIB E') {
            XLSX.utils.book_append_sheet(wb, wsKibE, filterCat === 'all' ? "6. KIB E - Lainnya" : "KIB E - Aset Tetap Lainnya");
        }

        // ========================================================================
        // 7. EXTRACOM (BARANG EKSTRAKOMTABEL < Rp 300.000)
        // ========================================================================
        const extracomTitleRows = getKemitraanKibTitleRows("BARANG EKSTRAKOMTABEL (< Rp 300.000)", yearLabel, filterTw, skemaLabel);
        const extracomRows = [
            ...extracomTitleRows,
            [
                "NO",
                "ASET KEMITRAAN (AKUN 1.5.2)", "", "", "", "", "",
                "RINCIAN ASET EKSTRAKOMTABEL SESUAI PKS / " + yearLabel,
                "", "", "", "", "", "", "", "",
                "", "", "", "",
                "RUANG /\nPEMEGANG",
                "PIHAK PENYEDIA / MITRA", "", "",
                "Pejabat Pembuat Komitmen", "",
                "KET."
            ],
            [
                "",
                "Jenis Aset (PMDN 108)", "",
                "Sub Rincian Objek (PMDN 108)", "",
                "JUMLAH TAKSIRAN (Rp)",
                "NILAI ASET WAJAR (Rp)",
                "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                "Merk", "Type", "Ukuran",
                "No. Pabrik", "Bahan", "Tahun Perolehan",
                "Riwayat Kerja Sama / PKS", "",
                "Kondisi\n(B,KB,RB)",
                "VOLUME", "",
                "Nilai Satuan (Rp)", "Total Nilai Barang (Rp)",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "",
                "Kode", "Nama Jenis Aset",
                "Kode", "Nama Uraian Sub Rincian Objek",
                "", "",
                "", "", "", "", "", "", "", "",
                "PKS", "",
                "",
                "Jumlah Barang", "Nama Satuan Barang",
                "", "",
                "",
                "Nama Mitra Rekanan", "Pimpinan Mitra", "Alamat Mitra",
                "Nama", "NIP",
                ""
            ],
            [
                "",
                "", "", "", "", "", "",
                "", "", "", "", "", "", "", "",
                "Nomor", "Tanggal",
                "",
                "", "",
                "", "",
                "",
                "", "", "",
                "", "",
                ""
            ],
            [
                "1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15",
                "16", "17", "18", "19", "20", "21", "22", "23", "24", "25", "26", "27", "28", "29"
            ]
        ];

        const extracomGroups = {};
        categories['EXTRACOM'].forEach(item => {
            const groupKey = getAstapGroupKey(item, '1.5.2.01.01.04.002');
            if (!extracomGroups[groupKey]) extracomGroups[groupKey] = [];
            extracomGroups[groupKey].push(item);
        });

        let globalExtracomNo = 1;
        let extTotalAnggaran = 0, extTotalRealisasi = 0, extTotalUnit = 0, extTotalNilaiBarang = 0;

        Object.keys(extracomGroups).forEach(groupKey => {
            const groupItems = extracomGroups[groupKey];
            let groupAnggaranTotal = 0;
            let groupRealisasiTotal = 0;

            groupItems.forEach(it => {
                const k = it.kemitraan || {};
                const val = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(it.total_realisasi_num) || parseFloat(it.total_realisasi) || 0);
                groupAnggaranTotal += (typeof it.anggaran_num === 'number' ? it.anggaran_num : (parseFloat(it.jumlah_anggaran) || val));
                groupRealisasiTotal += val;
            });

            extTotalAnggaran += groupAnggaranTotal;
            extTotalRealisasi += groupRealisasiTotal;

            let isFirstRowInGroup = true;

            groupItems.forEach((item) => {
                const k = item.kemitraan || {};
                const spec = getSafeSpec(item);
                const subMesinItems = (spec.mesin_items && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0)
                    ? spec.mesin_items
                    : null;
                const subLainnyaItems = (spec.lainnya_items && Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0)
                    ? spec.lainnya_items
                    : null;

                if (subMesinItems) {
                    subMesinItems.forEach((mItem) => {
                        const qty = Math.max(1, parseInt(mItem.mesin_jumlah_barang) || 1);
                        const nilaiSatuan = parseFloat(mItem.mesin_nilai_satuan) || (parseFloat(k.nilai_aset) / qty) || 0;
                        const adminProyek = parseFloat(mItem.mesin_administrasi_proyek) || 0;
                        const totalNilaiBarang = (qty * nilaiSatuan) + adminProyek;

                        extTotalUnit += qty;
                        extTotalNilaiBarang += totalNilaiBarang;

                        const rawKondisi = mItem.mesin_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));
                        const ruangUnit = mItem.ruang_pemegang || spec.ruang_pemegang || item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnandi');

                        const col1to7 = buildKemitraanCol1to7(item, globalExtracomNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'BARANG EKSTRAKOMTABEL');
                        if (isFirstRowInGroup) { globalExtracomNo++; isFirstRowInGroup = false; }

                        extracomRows.push([
                            ...col1to7,
                            mItem.mesin_nama_barang || item.nama_barang || '-',
                            mItem.mesin_kode_barang || spec.mesin_kode_barang || item.kode_barang || '1.5.2.01.01.04.002',
                            mItem.mesin_merk || spec.merk || item.merk || '-',
                            mItem.mesin_type || spec.type || item.type || '-',
                            mItem.mesin_ukuran || spec.ukuran || item.ukuran || '-',
                            mItem.mesin_no_pabrik || spec.no_pabrik || item.no_pabrik || '-',
                            mItem.mesin_bahan || spec.bahan || item.bahan || '-',
                            mItem.mesin_tahun_pembuatan || spec.tahun_pembuatan || item.tahun_perolehan || '-',
                            ...getKemitraanKontrakCols(item),
                            kondisiLabel,
                            qty,
                            mItem.mesin_satuan || item.satuan || 'Unit',
                            nilaiSatuan,
                            totalNilaiBarang,
                            ruangUnit,
                            ...getKemitraanStep4Cols(item)
                        ]);
                    });
                } else if (subLainnyaItems) {
                    subLainnyaItems.forEach((lItem) => {
                        const qty = Math.max(1, parseInt(lItem.lainnya_jumlah) || parseInt(lItem.lainnya_jumlah_barang) || 1);
                        const nilaiSatuan = parseFloat(lItem.lainnya_nilai_satuan) || (parseFloat(k.nilai_aset) / qty) || 0;
                        const adminProyek = parseFloat(lItem.lainnya_administrasi_proyek) || 0;
                        const totalNilaiBarang = (qty * nilaiSatuan) + adminProyek;

                        extTotalUnit += qty;
                        extTotalNilaiBarang += totalNilaiBarang;

                        const rawKondisi = lItem.lainnya_kondisi || item.kondisi || 'Baik';
                        const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));
                        const ruangUnit = lItem.ruang_pemegang_lainnya || lItem.ruang_pemegang || item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnandi');

                        const col1to7 = buildKemitraanCol1to7(item, globalExtracomNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'BARANG EKSTRAKOMTABEL');
                        if (isFirstRowInGroup) { globalExtracomNo++; isFirstRowInGroup = false; }

                        extracomRows.push([
                            ...col1to7,
                            lItem.lainnya_nama_barang || item.nama_barang || '-',
                            lItem.lainnya_kode_barang || spec.lainnya_kode_barang || item.kode_barang || '1.5.2.01.01.02.005',
                            lItem.lainnya_judul || spec.judul || '-',
                            lItem.lainnya_pencipta || spec.pencipta || '-',
                            lItem.lainnya_ukuran || spec.ukuran || lItem.lainnya_spesifikasi || '-',
                            '-',
                            lItem.lainnya_bahan || spec.bahan || '-',
                            lItem.lainnya_tahun || spec.tahun || item.tahun_perolehan || '-',
                            ...getKemitraanKontrakCols(item),
                            kondisiLabel,
                            qty,
                            lItem.lainnya_satuan || item.satuan || 'Buah',
                            nilaiSatuan,
                            totalNilaiBarang,
                            ruangUnit,
                            ...getKemitraanStep4Cols(item)
                        ]);
                    });
                } else {
                    const qty = parseInt(k.jumlah_volume) || parseInt(item.jumlah_volume) || 1;
                    const totalVal = typeof k.nilai_aset === 'number' ? k.nilai_aset : (parseFloat(item.total_realisasi_num) || parseFloat(item.total_realisasi) || 0);
                    const nilaiSatuan = typeof item.harga_satuan_num === 'number' ? item.harga_satuan_num : (parseFloat(item.harga_satuan) || (totalVal / qty));
                    const adminProyek = typeof item.biaya_administrasi_proyek_num === 'number' ? item.biaya_administrasi_proyek_num : (parseFloat(item.biaya_administrasi_proyek) || 0);
                    const totalNilaiBarang = totalVal || ((qty * nilaiSatuan) + adminProyek);
                    const ruangUnit = item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnandi');

                    extTotalUnit += qty;
                    extTotalNilaiBarang += totalNilaiBarang;

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    const col1to7 = buildKemitraanCol1to7(item, globalExtracomNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, '1.5.2', 'BARANG EKSTRAKOMTABEL');
                    if (isFirstRowInGroup) { globalExtracomNo++; isFirstRowInGroup = false; }

                    extracomRows.push([
                        ...col1to7,
                        item.nama_barang || '-',
                        spec.mesin_kode_barang || item.kode_barang || '1.5.2.01.01.04.002',
                        item.merk || spec.merk || '-',
                        item.type || spec.type || '-',
                        item.ukuran || spec.ukuran || '-',
                        item.no_pabrik || spec.no_pabrik || '-',
                        item.bahan || spec.bahan || '-',
                        item.tahun_perolehan || spec.tahun_pembuatan || '-',
                        ...getKemitraanKontrakCols(item),
                        kondisiLabel,
                        qty,
                        item.satuan || 'Unit',
                        nilaiSatuan,
                        totalNilaiBarang,
                        ruangUnit,
                        ...getKemitraanStep4Cols(item)
                    ]);
                }
            });
        });

        // Baris Footer Total Extracom
        const extracomFooterRow = Array(29).fill("");
        extracomFooterRow[0] = "JUMLAH";
        extracomFooterRow[5] = extTotalAnggaran;
        extracomFooterRow[6] = extTotalRealisasi;
        extracomFooterRow[18] = extTotalUnit;
        extracomFooterRow[21] = extTotalNilaiBarang;
        extracomRows.push(extracomFooterRow);

        const extracomSignStartRow = extracomRows.length;
        const extracomSignRows = buildKemitraanSignRows(29, 23, 1);
        extracomSignRows.forEach(r => extracomRows.push(r));

        const wsExtracom = XLSX.utils.aoa_to_sheet(extracomRows);
        wsExtracom['!cols'] = Array(29).fill({wch: 18});
        wsExtracom['!cols'][0] = {wch: 6};
        wsExtracom['!cols'][1] = {wch: 14}; wsExtracom['!cols'][2] = {wch: 26};
        wsExtracom['!cols'][3] = {wch: 18}; wsExtracom['!cols'][4] = {wch: 32};
        wsExtracom['!cols'][5] = {wch: 22}; wsExtracom['!cols'][6] = {wch: 22};
        wsExtracom['!cols'][7] = {wch: 32}; wsExtracom['!cols'][8] = {wch: 22};
        wsExtracom['!cols'][9] = {wch: 18}; wsExtracom['!cols'][10] = {wch: 18};
        wsExtracom['!cols'][11] = {wch: 18}; wsExtracom['!cols'][12] = {wch: 18};
        wsExtracom['!cols'][13] = {wch: 16}; wsExtracom['!cols'][14] = {wch: 16};
        wsExtracom['!cols'][15] = {wch: 22}; wsExtracom['!cols'][16] = {wch: 14};
        wsExtracom['!cols'][17] = {wch: 14}; wsExtracom['!cols'][18] = {wch: 14};
        wsExtracom['!cols'][19] = {wch: 16}; wsExtracom['!cols'][20] = {wch: 22};
        wsExtracom['!cols'][21] = {wch: 22}; wsExtracom['!cols'][22] = {wch: 20};
        wsExtracom['!cols'][23] = {wch: 28}; wsExtracom['!cols'][24] = {wch: 24};
        wsExtracom['!cols'][25] = {wch: 30}; wsExtracom['!cols'][26] = {wch: 24};
        wsExtracom['!cols'][27] = {wch: 22}; wsExtracom['!cols'][28] = {wch: 26};

        wsExtracom['!merges'] = getKemitraanKibMerges([
            {s:{r:3,c:0}, e:{r:6,c:0}},
            {s:{r:3,c:1}, e:{r:3,c:6}},
            {s:{r:4,c:1}, e:{r:4,c:2}},
            {s:{r:5,c:1}, e:{r:6,c:1}},
            {s:{r:5,c:2}, e:{r:6,c:2}},
            {s:{r:4,c:3}, e:{r:4,c:4}},
            {s:{r:5,c:3}, e:{r:6,c:3}},
            {s:{r:5,c:4}, e:{r:6,c:4}},
            {s:{r:4,c:5}, e:{r:6,c:5}},
            {s:{r:4,c:6}, e:{r:6,c:6}},
            {s:{r:3,c:7}, e:{r:3,c:21}},
            {s:{r:4,c:7}, e:{r:6,c:7}},
            {s:{r:4,c:8}, e:{r:6,c:8}},
            {s:{r:4,c:9}, e:{r:6,c:9}},
            {s:{r:4,c:10}, e:{r:6,c:10}},
            {s:{r:4,c:11}, e:{r:6,c:11}},
            {s:{r:4,c:12}, e:{r:6,c:12}},
            {s:{r:4,c:13}, e:{r:6,c:13}},
            {s:{r:4,c:14}, e:{r:6,c:14}},
            {s:{r:4,c:15}, e:{r:4,c:16}},
            {s:{r:5,c:15}, e:{r:5,c:16}},
            {s:{r:4,c:17}, e:{r:6,c:17}},
            {s:{r:4,c:18}, e:{r:4,c:19}},
            {s:{r:5,c:18}, e:{r:6,c:18}},
            {s:{r:5,c:19}, e:{r:6,c:19}},
            {s:{r:4,c:20}, e:{r:4,c:21}},
            {s:{r:5,c:20}, e:{r:6,c:20}},
            {s:{r:5,c:21}, e:{r:6,c:21}},
            {s:{r:3,c:22}, e:{r:6,c:22}},
            {s:{r:3,c:23}, e:{r:4,c:25}},
            {s:{r:5,c:23}, e:{r:6,c:23}},
            {s:{r:5,c:24}, e:{r:6,c:24}},
            {s:{r:5,c:25}, e:{r:6,c:25}},
            {s:{r:3,c:26}, e:{r:4,c:27}},
            {s:{r:5,c:26}, e:{r:6,c:26}},
            {s:{r:5,c:27}, e:{r:6,c:27}},
            {s:{r:3,c:28}, e:{r:6,c:28}}
        ], 29, extracomTitleRows.length, extracomRows.length, true);
        wsExtracom['!merges'].push(...getKibSignatureMerges(extracomSignStartRow, 29, 23, 1, 6, 28));

        applyKemitraanMasterSheetStyling(wsExtracom, extracomRows.length, 29, 15, extracomTitleRows.length, true);
        applySignatureBlockStyling(wsExtracom, extracomSignStartRow, 29);
        if (filterCat === 'all' || filterCat === 'EXTRACOM') {
            XLSX.utils.book_append_sheet(wb, wsExtracom, filterCat === 'all' ? "7. Extracom" : "Extracom - Barang Ekstrakomtabel");
        }

        // Simpan File Excel
        const twSlug = filterTw === 'all' ? 'TAHUNAN' : filterTw.replace(/[\s_]/g, '');
        const skemaSlug = filterSkema === 'all' ? 'SEMUA_SKEMA' : filterSkema.toUpperCase();
        const catSlug = filterCat === 'all' ? 'LENGKAP_7SHEET' : filterCat.replace(/[\s_]/g, '');
        const fileName = `BUKU_ASET_KEMITRAAN_AKUN_152_RSDK_${skemaSlug}_${catSlug}_${yearLabel}_${twSlug}.xlsx`;

        XLSX.writeFile(wb, fileName);
        setTimeout(() => { isExportingKemitraan = false; }, 1500);
    }
</script>
