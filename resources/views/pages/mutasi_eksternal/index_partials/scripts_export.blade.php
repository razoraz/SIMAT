<!-- ========================================================================= -->
<!-- SCRIPTS: ENGINE EKSPOR EXCEL MULTI-SHEET ASET MUTASI EKSTERNAL (ANTAR-OPD) -->
<!-- STANDAR BPKAD / SIMAT-RK 7 LEMBAR SHEET PERMENDAGRI 108                    -->
<!-- ========================================================================= -->
<script src="{{ asset('js/xlsx.bundle.js') }}"></script>
<script>
    if (typeof XLSX === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"><\/script>');
    }
</script>

<script>
    // Helper nama kolom Excel (0 -> A, 1 -> B, 26 -> AA, dst)
    function getColName(colIdx) {
        let name = '';
        let num = colIdx;
        while (num >= 0) {
            name = String.fromCharCode((num % 26) + 65) + name;
            num = Math.floor(num / 26) - 1;
        }
        return name;
    }

    // Helper format tanggal Indonesia
    function formatAstapDate(dateVal) {
        if (!dateVal) return '-';
        try {
            const d = new Date(dateVal);
            if (isNaN(d.getTime())) return String(dateVal);
            const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
            return `${d.getDate()} ${months[d.getMonth()]} ${d.getFullYear()}`;
        } catch(e) {
            return String(dateVal);
        }
    }

    // Helper tanggal tanda tangan dinamis
    function getReportSignDate(triwulan, year) {
        const y = (year && year !== 'all') ? year : new Date().getFullYear();
        if (triwulan === 'TW I' || triwulan === 'TW1') return `31 Maret ${y}`;
        if (triwulan === 'TW II' || triwulan === 'TW2') return `30 Juni ${y}`;
        if (triwulan === 'TW III' || triwulan === 'TW3') return `30 September ${y}`;
        if (triwulan === 'TW IV' || triwulan === 'TW4') return `31 Desember ${y}`;
        const now = new Date();
        const months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];
        return `${now.getDate()} ${months[now.getMonth()]} ${y}`;
    }

    // Helper ambil data spesifikasi JSON aman
    function getSafeSpec(it) {
        if (!it) return {};
        let spec = it.spesifikasi_json || {};
        if (typeof spec === 'string') {
            try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
        }
        return spec || {};
    }

    // Helper penentuan kategori KIB
    function resolveItemCategory(item) {
        if (!item) return 'KIB B';
        if (item.is_extracomtable || item.category === 'EXTRACOM') return 'EXTRACOM';
        
        const cat = (item.category || '').toUpperCase();
        if (cat.includes('KIB A') || cat.includes('TANAH')) return 'KIB A';
        if (cat.includes('KIB B') || cat.includes('MESIN') || cat.includes('PERALATAN')) return 'KIB B';
        if (cat.includes('KIB C') || cat.includes('GEDUNG') || cat.includes('BANGUNAN')) return 'KIB C';
        if (cat.includes('KIB D') || cat.includes('JARINGAN') || cat.includes('JALAN')) return 'KIB D';
        if (cat.includes('KIB E') || cat.includes('LAINNYA') || cat.includes('ASET TETAP LAIN')) return 'KIB E';

        const kd = String(item.kode_108 || item.kode_barang || '');
        if (kd.startsWith('1.3.1')) return 'KIB A';
        if (kd.startsWith('1.3.2')) return 'KIB B';
        if (kd.startsWith('1.3.3')) return 'KIB C';
        if (kd.startsWith('1.3.4')) return 'KIB D';
        if (kd.startsWith('1.3.5')) return 'KIB E';
        if (kd.startsWith('1.5.4')) return 'KIB E';
        return 'KIB B';
    }

    // Helper merge blok tanda tangan
    function getKibSignatureMerges(signStartRow, numCols, rightStartCol, leftStartCol = 1, leftEndCol = null, rightEndCol = null) {
        const lS = leftStartCol;
        const lE = leftEndCol !== null ? leftEndCol : Math.min(lS + 5, rightStartCol - 2);
        const rS = rightStartCol;
        const rE = rightEndCol !== null ? rightEndCol : (numCols - 1);
        const merges = [];
        for (let offset = 0; offset < 9; offset++) {
            const r = signStartRow + offset;
            merges.push({ s: { r, c: lS }, e: { r, c: lE } });
            merges.push({ s: { r, c: rS }, e: { r, c: rE } });
        }
        return merges;
    }

    // Helper styling blok tanda tangan
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

    // Helper styling Lembar Rekapitulasi (Sheet 1)
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
            const firstCellRef = getColName(0) + (r + 1);
            const firstCellVal = ws[firstCellRef] ? String(ws[firstCellRef].v || '').trim() : '';

            const isTitle = (r < titleRowCount);
            const isBanner = firstCellVal.startsWith('BAGIAN');
            const isHeader = (firstCellVal === 'NO');
            const isTotal = firstCellVal.startsWith('JUMLAH') || firstCellVal.startsWith('TOTAL');
            const isSign = (r >= signStartRow);

            if (isSign) continue;

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

                if (isTitle) {
                    fill = "FFFFFF";
                    fontColor = "0F172A";
                    bold = true;
                    fontSize = (r === 0 || r === 1) ? 12 : 11;
                    align = "center";
                    border = null;
                } else if (isBanner) {
                    fill = "1E1B4B"; // Deep Indigo Banner
                    fontColor = "FFFFFF";
                    bold = true;
                    fontSize = 10;
                    align = "left";
                    border = thinBorder;
                } else if (isHeader) {
                    fill = "4338CA"; // Indigo Header
                    fontColor = "FFFFFF";
                    bold = true;
                    fontSize = 9.5;
                    align = "center";
                    border = thinBorder;
                } else if (isTotal) {
                    fill = "C7D2FE"; // Soft Indigo Total
                    fontColor = "0F172A";
                    bold = true;
                    fontSize = 10;
                    border = doubleBottomBorder;
                    if (c === 0) align = "left";
                    else if (c === 4) align = "center";
                    else if (c === 5) {
                        align = "right";
                        numFmt = "Rp #,##0.00";
                    } else align = "left";
                } else {
                    fill = (r % 2 === 0) ? "FFFFFF" : "F5F3FF";
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
                        fill = "E0E7FF";
                        numFmt = "Rp #,##0.00";
                    } else align = "left";
                }

                cell.s = {
                    font: { name: "Calibri", sz: fontSize, bold: bold, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: "center", wrapText: true },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt && typeof cell.v === 'number') cell.z = numFmt;
            }
        }
    }

    // Helper styling lembar kerja KIB Mutasi Eksternal (Sheet 2 - 7)
    function applyMutasiMasterSheetStyling(ws, rowCount, colCount, kibL3ColCount, titleRowCount = 5, hasSignature = false) {
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
                    } else if (c >= 1 && c <= 6) {
                        fill = "DCE6F1";
                    } else if (c >= l3Start && c <= l3End) {
                        fill = "FDE9D9";
                    } else if (c === (colCount - 7)) {
                        fill = "E4DFEC";
                    } else if (c >= (colCount - 6) && c <= (colCount - 4)) {
                        fill = "F2DCDB";
                    } else if (c >= (colCount - 3) && c <= (colCount - 2)) {
                        fill = "FFF2CC";
                    } else if (c === (colCount - 1)) {
                        fill = "D7E4BC";
                    } else {
                        fill = "E2EFDA";
                    }
                } else if (r === footerRowIdx) {
                    fill = "D9E1F2";
                    fontColor = "0F172A";
                    bold = true;
                    fontSize = 9.5;
                    border = doubleBottomBorder;
                    if (c === 0) align = "center";
                    else if (typeof cell.v === 'number') {
                        align = "right";
                        if (c === 5 || c === 6 || c === (colCount - 8) || c === (colCount - 9)) {
                            numFmt = "Rp #,##0.00";
                        } else {
                            numFmt = "#,##0";
                        }
                    } else {
                        align = "center";
                    }
                } else {
                    fill = (r % 2 === 0) ? "FFFFFF" : "F8FAFC";
                    if (c === 0) align = "center";
                    else if (c === 1 || c === 3 || c === 8) align = "center";
                    else if (c === 5 || c === 6) {
                        align = "right";
                        numFmt = "Rp #,##0.00";
                    } else if (typeof cell.v === 'number') {
                        align = "right";
                        if (String(ws[getColName(c) + (headerStartRow + 2)]?.v || '').toLowerCase().includes('nilai') ||
                            String(ws[getColName(c) + (headerStartRow + 1)]?.v || '').toLowerCase().includes('nilai') ||
                            String(ws[getColName(c) + (headerStartRow + 2)]?.v || '').toLowerCase().includes('rp')) {
                            numFmt = "Rp #,##0.00";
                        } else {
                            numFmt = "#,##0";
                        }
                    } else if (typeof cell.v === 'string' && (cell.v === 'Baik' || cell.v === 'Kurang Baik' || cell.v === 'Rusak Berat' || cell.v.includes('Unit') || cell.v.includes('Bidang') || cell.v.includes('Bangunan'))) {
                        align = "center";
                    }
                }

                cell.s = {
                    font: { name: "Calibri", sz: fontSize, bold: bold, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: "center", wrapText: true },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt && typeof cell.v === 'number') cell.z = numFmt;
            }
        }
    }

    // Helper baris judul KIB Mutasi Eksternal
    function getMutasiKibTitleRows(kibName, yearLabel, filterTw, arahLabel) {
        let twLabel = "TAHUNAN";
        if (filterTw === 'TW I' || filterTw === 'TW1') twLabel = "TRIWULAN I";
        else if (filterTw === 'TW II' || filterTw === 'TW2') twLabel = "TRIWULAN II";
        else if (filterTw === 'TW III' || filterTw === 'TW3') twLabel = "TRIWULAN III";
        else if (filterTw === 'TW IV' || filterTw === 'TW4') twLabel = "TRIWULAN IV";

        return [
            ["PEMERINTAH KABUPATEN BONDOWOSO"],
            ["RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI"],
            [`DAFTAR ASET MUTASI EKSTERNAL (ANTAR-OPD) - ${kibName} TAHUN ${yearLabel}`],
            [`ARAH MUTASI: ${arahLabel} · TRANSFER BARANG MILIK DAERAH (BMD) RESMI`],
            [`${twLabel} TAHUN ANGGARAN ${yearLabel}`]
        ];
    }

    // Helper baris tanda tangan resmi
    function buildMutasiSignRows(totalCols, rightCol, leftCol = 1) {
        const signDate = getReportSignDate('all', new Date().getFullYear());
        const makeRow = (lVal, rVal) => {
            const arr = Array(totalCols).fill("");
            arr[leftCol] = lVal || "";
            arr[rightCol] = rVal || "";
            return arr;
        };

        return [
            Array(totalCols).fill(""),
            makeRow("MENGETAHUI,", `Bondowoso, ${signDate}`),
            makeRow("DIREKTUR RSUD dr. H. KOESNANDI", "PENGURUS BARANG PENGGUNA"),
            makeRow("KABUPATEN BONDOWOSO", "RSUD dr. H. KOESNANDI BONDOWOSO"),
            Array(totalCols).fill(""),
            Array(totalCols).fill(""),
            makeRow("dr. YUS PRIYATNA ADISIWIBOWO, Sp.P", "BUDI HARTONO, S.Sos"),
            makeRow("NIP. 19760814 200212 1 005", "NIP. 19760229 200801 1 010"),
            Array(totalCols).fill("")
        ];
    }

    // Helper data kolom 1 s/d 7 (Akun & Klasifikasi 108)
    function buildMutasiCol1to7(item, globalNo, isFirstRowInGroup, groupAnggaranTotal, groupRealisasiTotal, defaultAkunPrefix = '1.3.2', defaultKlasifikasi = 'PERALATAN DAN MESIN') {
        if (!isFirstRowInGroup) {
            return ["", "", "", "", "", 0, 0];
        }
        const kd108 = String(item.kode_108 || item.kode_barang || '');
        const kdParts = kd108.split('.');
        
        let kodeJenis = (kdParts.length >= 3) ? kdParts.slice(0, 3).join('.') : defaultAkunPrefix;
        let namaJenis = item.jenis_aset_nama || defaultKlasifikasi;
        let kodeSubRincian = (kdParts.length >= 5) ? kdParts.slice(0, 5).join('.') : (kodeJenis + '.01.01');
        let namaSubRincian = item.nama_murni || item.nama_barang || defaultKlasifikasi;

        return [
            globalNo,
            kodeJenis,
            namaJenis,
            kodeSubRincian,
            namaSubRincian,
            groupAnggaranTotal,
            groupRealisasiTotal
        ];
    }

    // Helper data kolom SKPD Mitra Mutasi (Asal / Tujuan)
    function getMutasiMitraCols(item) {
        const isKeluar = (item.tipe === 'keluar');
        if (isKeluar) {
            const instansi = item.opd_tujuan || 'SKPD / Instansi Luar';
            const pejabat = item.pejabat_opd_tujuan || 'Pejabat Penerima OPD';
            const alamat = item.alamat_instansi || '-';
            return [instansi, pejabat, alamat];
        } else {
            const instansi = item.opd_asal || 'SKPD / Instansi Luar';
            const pejabat = item.pj_asal_nama || 'Pejabat Penyerah SKPD';
            const alamat = item.alamat_instansi || '-';
            return [instansi, pejabat, alamat];
        }
    }

    // Helper data kolom Pejabat RSUD Koesnadi
    function getMutasiPejabatRsudCols(item) {
        const isKeluar = (item.tipe === 'keluar');
        if (isKeluar) {
            return [item.pj_asal_nama || 'BUDI HARTONO, S.Sos', item.pj_asal_nip || '19760229 200801 1 010'];
        } else {
            return [item.pejabat_opd_tujuan || 'BUDI HARTONO, S.Sos', item.nip_pejabat_opd_tujuan || '19760229 200801 1 010'];
        }
    }

    // Helper data kolom Dokumen BAST Mutasi
    function getMutasiDokumenCols(item) {
        const noBast = item.mutasi_nomor_bamb || item.bast_nomor || item.kode || '-';
        const tglBast = formatAstapDate(item.mutasi_tanggal || item.tgl_raw);
        return [noBast, tglBast];
    }

    // Helper merge header master KIB
    function getMutasiKibMerges(baseMerges, numCols, titleRowCount, totalRowCount, hasSignature = false) {
        const merges = [];
        for (let i = 0; i < titleRowCount; i++) {
            merges.push({ s: { r: i, c: 0 }, e: { r: i, c: numCols - 1 } });
        }
        baseMerges.forEach(m => {
            merges.push({
                s: { r: m.s.r + titleRowCount - 3, c: m.s.c },
                e: { r: m.e.r + titleRowCount - 3, c: m.e.c }
            });
        });
        const footerRowIdx = hasSignature ? (totalRowCount - 1 - 9) : (totalRowCount - 1);
        merges.push({ s: { r: footerRowIdx, c: 0 }, e: { r: footerRowIdx, c: 4 } });
        return merges;
    }

    // =========================================================================
    // FUNGSI UTAMA ENGINE EKSPOR EXCEL MUTASI EKSTERNAL (STANDAR 7 SHEET BPKAD)
    // =========================================================================
    let isExportingMutasi = false;

    function exportMutasiEksternalToExcel(params = {}) {
        if (isExportingMutasi) return;
        isExportingMutasi = true;

        if (typeof XLSX === 'undefined') {
            alert('⚠️ Pustaka Excel sedang dimuat, silakan coba 1 detik lagi...');
            isExportingMutasi = false;
            return;
        }

        const rawMutasis = window.simatMutasiEksternals || [];
        const wb = XLSX.utils.book_new();

        const filterDirection = params.direction || 'all';
        const filterYear      = params.year || 'all';
        const filterTw        = params.triwulan || 'all';
        const filterCat       = params.category || 'all';

        const isTwMatch = (itemDate, itemTw, targetTw) => {
            if (targetTw === 'all') return true;
            let tw = itemTw;
            if (!tw && itemDate) {
                const m = new Date(itemDate).getMonth() + 1;
                if (m <= 3) tw = 'TW I';
                else if (m <= 6) tw = 'TW II';
                else if (m <= 9) tw = 'TW III';
                else tw = 'TW IV';
            }
            const targetKey = String(targetTw).replace(/[\s_]/g, '').toUpperCase();
            const curTw = String(tw || 'TWI').replace(/[\s_]/g, '').toUpperCase();
            return (curTw === targetKey) ||
                   (targetKey === 'TWI' && curTw === 'TW1') || (targetKey === 'TW1' && curTw === 'TWI') ||
                   (targetKey === 'TWII' && curTw === 'TW2') || (targetKey === 'TW2' && curTw === 'TWII') ||
                   (targetKey === 'TWIII' && curTw === 'TW3') || (targetKey === 'TW3' && curTw === 'TWIII') ||
                   (targetKey === 'TWIV' && curTw === 'TW4') || (targetKey === 'TW4' && curTw === 'TWIV');
        };

        // Filter data mutasi
        let filteredMutasis = rawMutasis.filter(item => {
            if (item.is_deleted === 1) return false;

            // 1. Filter Arah
            if (filterDirection === 'masuk' && item.tipe === 'keluar') return false;
            if (filterDirection === 'keluar' && item.tipe !== 'keluar') return false;

            // 2. Filter Tahun
            const itemYear = String(item.tahun_perolehan || (item.tgl_raw ? new Date(item.tgl_raw).getFullYear() : ''));
            if (filterYear !== 'all' && itemYear !== String(filterYear)) return false;

            // 3. Filter Triwulan
            if (!isTwMatch(item.tgl_raw || item.mutasi_tanggal, item.triwulan, filterTw)) return false;

            // 4. Filter Kategori
            if (filterCat !== 'all' && filterCat !== 'REKAP') {
                const itemCat = resolveItemCategory(item);
                if (itemCat !== filterCat) return false;
            }

            return true;
        });

        // Pengelompokan Data per Kategori KIB
        const categories = {
            'KIB A': [],
            'KIB B': [],
            'KIB C': [],
            'KIB D': [],
            'KIB E': [],
            'EXTRACOM': []
        };

        filteredMutasis.forEach(item => {
            const cat = resolveItemCategory(item);
            if (categories[cat]) {
                categories[cat].push(item);
            } else {
                categories['KIB B'].push(item);
            }
        });

        // Label Dinamis
        let arahLabel = "SEMUA ARAH MUTASI (MASUK & TRANSFER KELUAR)";
        let arahSlug = "SEMUA_ARAH";
        if (filterDirection === 'masuk') {
            arahLabel = "MUTASI EKSTERNAL MASUK (DARI OPD LUAR)";
            arahSlug = "MUTASI_MASUK";
        } else if (filterDirection === 'keluar') {
            arahLabel = "TRANSFER KELUAR (ASET RSUD KE OPD LUAR)";
            arahSlug = "TRANSFER_KELUAR";
        }

        let twLabel = "KESELURUHAN (TAHUNAN)";
        let twSlug = "TAHUNAN";
        if (filterTw === 'TW I' || filterTw === 'TW1') { twLabel = "TRIWULAN I"; twSlug = "TW1"; }
        else if (filterTw === 'TW II' || filterTw === 'TW2') { twLabel = "TRIWULAN II"; twSlug = "TW2"; }
        else if (filterTw === 'TW III' || filterTw === 'TW3') { twLabel = "TRIWULAN III"; twSlug = "TW3"; }
        else if (filterTw === 'TW IV' || filterTw === 'TW4') { twLabel = "TRIWULAN IV"; twSlug = "TW4"; }

        const yearLabel = (filterYear === 'all') ? 'SEMUA TAHUN' : filterYear;
        const catSlug = (filterCat === 'all') ? 'LENGKAP_7SHEET' : filterCat.replace(/\s+/g, '_');

        // Helper hitung summary
        const getCatSummary = (catList) => {
            let unitTotal = 0;
            let nilaiTotal = 0;
            catList.forEach(it => {
                const spec = getSafeSpec(it);
                let uCount = parseInt(it.jumlah_volume) || 1;
                if (spec.mesin_items && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                    uCount = spec.mesin_items.reduce((acc, m) => acc + (parseInt(m.mesin_jumlah_barang) || 1), 0);
                }
                unitTotal += uCount;
                nilaiTotal += parseFloat(it.nilai_perolehan || it.total_realisasi_num || 0);
            });
            return { unitTotal, nilaiTotal };
        };

        const sumA = getCatSummary(categories['KIB A']);
        const sumB = getCatSummary(categories['KIB B']);
        const sumC = getCatSummary(categories['KIB C']);
        const sumD = getCatSummary(categories['KIB D']);
        const sumE = getCatSummary(categories['KIB E']);
        const sumExtracom = getCatSummary(categories['EXTRACOM']);

        const grandTotalUnit = sumA.unitTotal + sumB.unitTotal + sumC.unitTotal + sumD.unitTotal + sumE.unitTotal + sumExtracom.unitTotal;
        const grandTotalNilai = sumA.nilaiTotal + sumB.nilaiTotal + sumC.nilaiTotal + sumD.nilaiTotal + sumE.nilaiTotal + sumExtracom.nilaiTotal;

        // ========================================================================
        // 1. LEMBAR REKAPITULASI (SHEET 1)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'REKAP') {
            const rekapData = [
                ["PEMERINTAH KABUPATEN BONDOWOSO"],
                ["RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI"],
                ["BUKU REKAPITULASI ASET MUTASI EKSTERNAL (ANTAR-OPD / SKPD)"],
                [`ARAH: ${arahLabel} · PERIODE: ${twLabel} TAHUN ANGGARAN ${yearLabel}`],
                [""],
                [
                    "NO",
                    "KLASIFIKASI KIB / AKUN 108 MUTASI",
                    "KODE AKUN REKENING BMD",
                    "DESKRIPSI / ARAH MUTASI",
                    "JUMLAH ITEM / UNIT",
                    "TOTAL NILAI PEROLEHAN ASET (Rp)",
                    "KETERANGAN / STATUS MUTASI"
                ],
                [1, "KIB A - TANAH MUTASI", "1.3.1.01.01.01.001", "Tanah Bidang Antar-OPD", `${sumA.unitTotal} Bidang`, sumA.nilaiTotal, "Selesai"],
                [2, "KIB B - PERALATAN DAN MESIN", "1.3.2.01.01.04.002", "Peralatan, Mesin & Fasilitas Medis", `${sumB.unitTotal} Unit`, sumB.nilaiTotal, "Selesai"],
                [3, "KIB C - GEDUNG DAN BANGUNAN", "1.3.3.01.01.03.003", "Gedung & Bangunan Fisik", `${sumC.unitTotal} Bangunan`, sumC.nilaiTotal, "Selesai"],
                [4, "KIB D - JALAN, IRIGASI DAN JARINGAN", "1.3.4.01.01.04.004", "Infrastruktur Jaringan & Instalasi", `${sumD.unitTotal} Jaringan`, sumD.nilaiTotal, "Selesai"],
                [5, "KIB E - ASET TETAP LAINNYA", "1.3.5.01.01.02.005", "Buku, Kesenian & Aset Tetap Lain", `${sumE.unitTotal} Unit/Item`, sumE.nilaiTotal, "Selesai"],
                [6, "EXTRACOM - BARANG EKSTRAKOMTABEL (< Rp 300.000)", "1.3.2.01.01.04.002", "Peralatan & Barang Ekstrakomtabel", `${sumExtracom.unitTotal} Unit/Item`, sumExtracom.nilaiTotal, "Selesai"],
                [
                    "JUMLAH TOTAL NILAI ASET MUTASI EKSTERNAL",
                    "", "", "",
                    `${grandTotalUnit} Unit Total`,
                    grandTotalNilai,
                    `Rekapitulasi ${twLabel} ${yearLabel}`
                ],
                [""]
            ];

            const signRows = buildMutasiSignRows(7, 4, 1);
            signRows.forEach(r => rekapData.push(r));

            const wsRekap = XLSX.utils.aoa_to_sheet(rekapData);
            wsRekap['!cols'] = [
                { wch: 6 },
                { wch: 46 },
                { wch: 25 },
                { wch: 38 },
                { wch: 20 },
                { wch: 28 },
                { wch: 32 }
            ];

            wsRekap['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 6 } },
                { s: { r: 1, c: 0 }, e: { r: 1, c: 6 } },
                { s: { r: 2, c: 0 }, e: { r: 2, c: 6 } },
                { s: { r: 3, c: 0 }, e: { r: 3, c: 6 } },
                { s: { r: 12, c: 0 }, e: { r: 12, c: 3 } },
                ...getKibSignatureMerges(14, 7, 4, 1, 2, 6)
            ];

            applyRekapSheetStyling(wsRekap, rekapData.length, 7, 5, 12, 14);
            applySignatureBlockStyling(wsRekap, 14, 7);
            XLSX.utils.book_append_sheet(wb, wsRekap, filterCat === 'all' ? "1. Rekapitulasi" : "Rekapitulasi Mutasi");
        }

        // ========================================================================
        // 2. KIB A - TANAH (26 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB A') {
            const kibATitleRows = getMutasiKibTitleRows("TANAH MUTASI EKSTERNAL", yearLabel, filterTw, arahLabel);
            const kibARows = [
                ...kibATitleRows,
                [
                    "NO",
                    "ASET MUTASI EKSTERNAL (PMDN 108)", "", "", "", "", "",
                    "RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / " + yearLabel,
                    "", "", "", "", "", "", "", "", "", "", "",
                    "LETAK / ALAMAT BARANG",
                    "SKPD / OPD MITRA MUTASI", "", "",
                    "Pengurus Barang / Pejabat RSUD", "",
                    "KET."
                ],
                [
                    "",
                    "Jenis Aset (PMDN 108)", "",
                    "Sub Rincian Objek (PMDN 108)", "",
                    "JUMLAH ANGGARAN (Rp)",
                    "NILAI PEROLEHAN BMD (Rp)",
                    "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                    "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                    "Status Tanah", "", "",
                    "Dokumen BAST Mutasi", "",
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
                    "", "",
                    "", "",
                    "Hak Tanah\n(Hak Pakai / Pengelolaan)",
                    "Sertifikat", "",
                    "", "",
                    "",
                    "",
                    "Jumlah Bidang Tanah", "Luas Tanah (m²)",
                    "",
                    "",
                    "Nama Instansi Mitra", "Pejabat Mitra", "Alamat Mitra",
                    "Nama", "NIP",
                    ""
                ],
                [
                    "",
                    "", "", "", "", "", "",
                    "", "",
                    "",
                    "Tanggal", "Nomor",
                    "Nomor", "Tanggal",
                    "",
                    "",
                    "", "",
                    "",
                    "",
                    "", "", "",
                    "", "",
                    ""
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26
                ]
            ];

            let kibATotalAnggaran = 0;
            let kibATotalRealisasi = 0;
            let kibATotalUnit = 0;
            let kibATotalLuas = 0;
            let kibATotalNilaiBarang = 0;
            let globalKibANo = 1;

            categories['KIB A'].forEach(item => {
                const spec = getSafeSpec(item);
                const jumlahBidang = parseInt(item.jumlah_volume) || 1;
                const luasM2 = parseFloat(spec.luas_m2 || spec.tanah_luas || 0);
                const totalNilaiBarang = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);

                kibATotalAnggaran += 0;
                kibATotalRealisasi += totalNilaiBarang;
                kibATotalUnit += jumlahBidang;
                kibATotalLuas += luasM2;
                kibATotalNilaiBarang += totalNilaiBarang;

                const col1to7 = buildMutasiCol1to7(item, globalKibANo, true, 0, totalNilaiBarang, '1.3.1', 'TANAH');
                globalKibANo++;

                const docCols = getMutasiDokumenCols(item);
                const mitraCols = getMutasiMitraCols(item);
                const rsudCols = getMutasiPejabatRsudCols(item);

                kibARows.push([
                    ...col1to7,
                    item.nama_barang || item.nama_murni || '-',
                    item.kode_108 || item.kode_barang || '1.3.1.01.01.01.001',
                    spec.hak_tanah || item.hak_tanah || 'Hak Pakai',
                    formatAstapDate(spec.sertifikat_tgl || spec.sertifikat_tanggal),
                    spec.sertifikat_no || spec.sertifikat_nomor || '-',
                    docCols[0],
                    docCols[1],
                    item.kondisi || 'Baik',
                    spec.penggunaan || 'Operasional RSUD Dr. H. Koesnadi',
                    jumlahBidang,
                    luasM2,
                    totalNilaiBarang,
                    item.alamat_barang || spec.letak_alamat || 'Kabupaten Bondowoso',
                    ...mitraCols,
                    ...rsudCols,
                    (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                ]);
            });

            // Footer
            const kibAFooterRow = Array(26).fill("");
            kibAFooterRow[0] = "JUMLAH";
            kibAFooterRow[5] = kibATotalAnggaran;
            kibAFooterRow[6] = kibATotalRealisasi;
            kibAFooterRow[16] = kibATotalUnit;
            kibAFooterRow[17] = kibATotalLuas;
            kibAFooterRow[18] = kibATotalNilaiBarang;
            kibARows.push(kibAFooterRow);

            const kibASignStartRow = kibARows.length;
            const kibASignRows = buildMutasiSignRows(26, 17, 1);
            kibASignRows.forEach(r => kibARows.push(r));

            const wsKibA = XLSX.utils.aoa_to_sheet(kibARows);
            wsKibA['!cols'] = Array(26).fill({ wch: 18 });
            wsKibA['!cols'][0] = { wch: 6 };
            wsKibA['!cols'][1] = { wch: 14 }; wsKibA['!cols'][2] = { wch: 26 };
            wsKibA['!cols'][3] = { wch: 18 }; wsKibA['!cols'][4] = { wch: 32 };
            wsKibA['!cols'][5] = { wch: 22 }; wsKibA['!cols'][6] = { wch: 22 };
            wsKibA['!cols'][7] = { wch: 32 }; wsKibA['!cols'][8] = { wch: 22 };
            wsKibA['!cols'][9] = { wch: 24 }; wsKibA['!cols'][10] = { wch: 14 };
            wsKibA['!cols'][11] = { wch: 18 }; wsKibA['!cols'][12] = { wch: 24 };
            wsKibA['!cols'][13] = { wch: 14 }; wsKibA['!cols'][14] = { wch: 14 };
            wsKibA['!cols'][15] = { wch: 22 }; wsKibA['!cols'][16] = { wch: 18 };
            wsKibA['!cols'][17] = { wch: 16 }; wsKibA['!cols'][18] = { wch: 22 };
            wsKibA['!cols'][19] = { wch: 28 }; wsKibA['!cols'][20] = { wch: 26 };
            wsKibA['!cols'][21] = { wch: 22 }; wsKibA['!cols'][22] = { wch: 26 };
            wsKibA['!cols'][23] = { wch: 24 }; wsKibA['!cols'][24] = { wch: 22 };
            wsKibA['!cols'][25] = { wch: 24 };

            wsKibA['!merges'] = getMutasiKibMerges([
                { s: { r: 3, c: 0 }, e: { r: 6, c: 0 } },
                { s: { r: 3, c: 1 }, e: { r: 3, c: 6 } },
                { s: { r: 4, c: 1 }, e: { r: 4, c: 2 } },
                { s: { r: 5, c: 1 }, e: { r: 6, c: 1 } },
                { s: { r: 5, c: 2 }, e: { r: 6, c: 2 } },
                { s: { r: 4, c: 3 }, e: { r: 4, c: 4 } },
                { s: { r: 5, c: 3 }, e: { r: 6, c: 3 } },
                { s: { r: 5, c: 4 }, e: { r: 6, c: 4 } },
                { s: { r: 4, c: 5 }, e: { r: 6, c: 5 } },
                { s: { r: 4, c: 6 }, e: { r: 6, c: 6 } },
                { s: { r: 3, c: 7 }, e: { r: 3, c: 18 } },
                { s: { r: 4, c: 7 }, e: { r: 6, c: 7 } },
                { s: { r: 4, c: 8 }, e: { r: 6, c: 8 } },
                { s: { r: 4, c: 9 }, e: { r: 4, c: 11 } },
                { s: { r: 5, c: 9 }, e: { r: 6, c: 9 } },
                { s: { r: 5, c: 10 }, e: { r: 5, c: 11 } },
                { s: { r: 4, c: 12 }, e: { r: 4, c: 13 } },
                { s: { r: 5, c: 12 }, e: { r: 6, c: 12 } },
                { s: { r: 5, c: 13 }, e: { r: 6, c: 13 } },
                { s: { r: 4, c: 14 }, e: { r: 6, c: 14 } },
                { s: { r: 4, c: 15 }, e: { r: 6, c: 15 } },
                { s: { r: 4, c: 16 }, e: { r: 4, c: 17 } },
                { s: { r: 5, c: 16 }, e: { r: 6, c: 16 } },
                { s: { r: 5, c: 17 }, e: { r: 6, c: 17 } },
                { s: { r: 4, c: 18 }, e: { r: 6, c: 18 } },
                { s: { r: 3, c: 19 }, e: { r: 6, c: 19 } },
                { s: { r: 3, c: 20 }, e: { r: 4, c: 22 } },
                { s: { r: 5, c: 20 }, e: { r: 6, c: 20 } },
                { s: { r: 5, c: 21 }, e: { r: 6, c: 21 } },
                { s: { r: 5, c: 22 }, e: { r: 6, c: 22 } },
                { s: { r: 3, c: 23 }, e: { r: 4, c: 24 } },
                { s: { r: 5, c: 23 }, e: { r: 6, c: 23 } },
                { s: { r: 5, c: 24 }, e: { r: 6, c: 24 } },
                { s: { r: 3, c: 25 }, e: { r: 6, c: 25 } }
            ], 26, kibATitleRows.length, kibARows.length, true);
            wsKibA['!merges'].push(...getKibSignatureMerges(kibASignStartRow, 26, 17, 1, 6, 25));

            applyMutasiMasterSheetStyling(wsKibA, kibARows.length, 26, 12, kibATitleRows.length, true);
            applySignatureBlockStyling(wsKibA, kibASignStartRow, 26);
            XLSX.utils.book_append_sheet(wb, wsKibA, filterCat === 'all' ? "2. KIB A - Tanah" : "KIB A - Tanah");
        }

        // ========================================================================
        // 3. KIB B - PERALATAN DAN MESIN (33 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB B') {
            const kibBTitleRows = getMutasiKibTitleRows("PERALATAN DAN MESIN MUTASI EKSTERNAL", yearLabel, filterTw, arahLabel);
            const kibBRows = [
                ...kibBTitleRows,
                [
                    "NO",
                    "ASET MUTASI EKSTERNAL (PMDN 108)", "", "", "", "", "",
                    "RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / " + yearLabel,
                    "", "", "", "", "", "", "", "", "", "", "",
                    "", "", "", "", "", "", "",
                    "RUANGAN /\nPEMEGANG",
                    "SKPD / OPD MITRA MUTASI", "", "",
                    "Pengurus Barang / Pejabat RSUD", "",
                    "KET."
                ],
                [
                    "",
                    "Jenis Aset (PMDN 108)", "",
                    "Sub Rincian Objek (PMDN 108)", "",
                    "JUMLAH ANGGARAN (Rp)",
                    "NILAI PEROLEHAN BMD (Rp)",
                    "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                    "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                    "Merk", "Type", "Ukuran / CC",
                    "No. Pabrik", "No. Rangka", "No. Mesin", "No. BPKB", "No. POLISI",
                    "BAHAN", "Tahun Perolehan",
                    "Dokumen BAST Mutasi", "",
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
                    "", "",
                    "", "", "", "", "", "", "", "",
                    "", "",
                    "Nomor", "Tanggal",
                    "",
                    "Jumlah Barang", "Nama Satuan Barang",
                    "Nilai Satuan Barang (Rp)", "Total Nilai Barang (Rp)",
                    "",
                    "Nama Instansi Mitra", "Pejabat Mitra", "Alamat Mitra",
                    "Nama", "NIP",
                    ""
                ],
                [
                    "",
                    "", "", "", "", "", "",
                    "", "",
                    "", "", "", "", "", "", "", "", "", "",
                    "", "",
                    "",
                    "", "",
                    "", "",
                    "",
                    "", "", "",
                    "", "",
                    ""
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                    31, 32, 33
                ]
            ];

            let kibBTotalAnggaran = 0;
            let kibBTotalRealisasi = 0;
            let kibBTotalUnit = 0;
            let kibBTotalNilaiBarang = 0;
            let globalKibBNo = 1;

            categories['KIB B'].forEach(item => {
                const spec = getSafeSpec(item);
                const subMesin = (spec.mesin_items && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) ? spec.mesin_items : null;
                const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);

                kibBTotalAnggaran += 0;
                kibBTotalRealisasi += totalItemNilai;

                const docCols = getMutasiDokumenCols(item);
                const mitraCols = getMutasiMitraCols(item);
                const rsudCols = getMutasiPejabatRsudCols(item);
                const thnPerolehan = item.tahun_perolehan || spec.tahun_pembuatan || new Date().getFullYear();
                const defaultRuang = item.ruangan_tujuan || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnadi');
                let isFirst = true;

                if (subMesin) {
                    subMesin.forEach(mItem => {
                        const qty = Math.max(1, parseInt(mItem.mesin_jumlah_barang || mItem.mesin_jumlah || 1));
                        const valSatuan = parseFloat(mItem.mesin_nilai_satuan || 0) || (qty > 0 ? totalItemNilai / subMesin.length / qty : 0);
                        const subTotal = (qty * valSatuan);

                        kibBTotalUnit += qty;
                        kibBTotalNilaiBarang += subTotal;

                        const col1to7 = buildMutasiCol1to7(item, globalKibBNo, isFirst, 0, totalItemNilai, '1.3.2', 'PERALATAN DAN MESIN');
                        if (isFirst) { globalKibBNo++; isFirst = false; }

                        kibBRows.push([
                            ...col1to7,
                            mItem.mesin_nama_barang || item.nama_murni || item.nama_barang || '-',
                            mItem.kode_108 || item.kode_108 || item.kode_barang || '1.3.2.01.01.04.002',
                            mItem.mesin_merk || spec.merk || '-',
                            mItem.mesin_type || spec.type || '-',
                            mItem.mesin_ukuran_cc || spec.ukuran || '-',
                            mItem.mesin_nomor_pabrik || spec.no_pabrik || '-',
                            mItem.mesin_nomor_rangka || spec.no_rangka || '-',
                            mItem.mesin_nomor_mesin || spec.no_mesin || '-',
                            mItem.mesin_nomor_bpkb || spec.no_bpkb || '-',
                            mItem.mesin_nomor_polisi || spec.no_polisi || '-',
                            mItem.mesin_bahan || spec.bahan || '-',
                            thnPerolehan,
                            docCols[0],
                            docCols[1],
                            mItem.mesin_kondisi || item.kondisi || 'Baik',
                            qty,
                            mItem.mesin_satuan || item.satuan || 'Unit',
                            valSatuan,
                            subTotal,
                            mItem.ruang_pemegang || defaultRuang,
                            ...mitraCols,
                            ...rsudCols,
                            (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                        ]);
                    });
                } else {
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const valSatuan = parseFloat(item.harga_satuan) || (qty > 0 ? totalItemNilai / qty : totalItemNilai);

                    kibBTotalUnit += qty;
                    kibBTotalNilaiBarang += totalItemNilai;

                    const col1to7 = buildMutasiCol1to7(item, globalKibBNo, true, 0, totalItemNilai, '1.3.2', 'PERALATAN DAN MESIN');
                    globalKibBNo++;

                    kibBRows.push([
                        ...col1to7,
                        item.nama_murni || item.nama_barang || '-',
                        item.kode_108 || item.kode_barang || '1.3.2.01.01.04.002',
                        spec.merk || spec.mesin_merk || '-',
                        spec.type || spec.tipe || '-',
                        spec.ukuran || spec.mesin_ukuran_cc || '-',
                        spec.no_pabrik || spec.mesin_nomor_pabrik || '-',
                        spec.no_rangka || spec.mesin_nomor_rangka || '-',
                        spec.no_mesin || spec.mesin_nomor_mesin || '-',
                        spec.no_bpkb || spec.mesin_nomor_bpkb || '-',
                        spec.no_polisi || spec.mesin_nomor_polisi || '-',
                        spec.bahan || spec.mesin_bahan || '-',
                        thnPerolehan,
                        docCols[0],
                        docCols[1],
                        item.kondisi || 'Baik',
                        qty,
                        item.satuan || 'Unit',
                        valSatuan,
                        totalItemNilai,
                        defaultRuang,
                        ...mitraCols,
                        ...rsudCols,
                        (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                    ]);
                }
            });

            // Footer
            const kibBFooterRow = Array(33).fill("");
            kibBFooterRow[0] = "JUMLAH";
            kibBFooterRow[5] = kibBTotalAnggaran;
            kibBFooterRow[6] = kibBTotalRealisasi;
            kibBFooterRow[22] = kibBTotalUnit;
            kibBFooterRow[25] = kibBTotalNilaiBarang;
            kibBRows.push(kibBFooterRow);

            const kibBSignStartRow = kibBRows.length;
            const kibBSignRows = buildMutasiSignRows(33, 24, 1);
            kibBSignRows.forEach(r => kibBRows.push(r));

            const wsKibB = XLSX.utils.aoa_to_sheet(kibBRows);
            wsKibB['!cols'] = Array(33).fill({ wch: 17 });
            wsKibB['!cols'][0] = { wch: 6 };
            wsKibB['!cols'][1] = { wch: 14 }; wsKibB['!cols'][2] = { wch: 26 };
            wsKibB['!cols'][3] = { wch: 18 }; wsKibB['!cols'][4] = { wch: 32 };
            wsKibB['!cols'][5] = { wch: 22 }; wsKibB['!cols'][6] = { wch: 22 };
            wsKibB['!cols'][7] = { wch: 32 }; wsKibB['!cols'][8] = { wch: 22 };
            wsKibB['!cols'][9] = { wch: 20 }; wsKibB['!cols'][10] = { wch: 18 };
            wsKibB['!cols'][11] = { wch: 16 }; wsKibB['!cols'][12] = { wch: 18 };
            wsKibB['!cols'][13] = { wch: 20 }; wsKibB['!cols'][14] = { wch: 20 };
            wsKibB['!cols'][15] = { wch: 18 }; wsKibB['!cols'][16] = { wch: 16 };
            wsKibB['!cols'][17] = { wch: 16 }; wsKibB['!cols'][18] = { wch: 16 };
            wsKibB['!cols'][19] = { wch: 24 }; wsKibB['!cols'][20] = { wch: 14 };
            wsKibB['!cols'][21] = { wch: 14 }; wsKibB['!cols'][22] = { wch: 14 };
            wsKibB['!cols'][23] = { wch: 16 }; wsKibB['!cols'][24] = { wch: 22 };
            wsKibB['!cols'][25] = { wch: 22 }; wsKibB['!cols'][26] = { wch: 26 };
            wsKibB['!cols'][27] = { wch: 26 }; wsKibB['!cols'][28] = { wch: 22 };
            wsKibB['!cols'][29] = { wch: 26 }; wsKibB['!cols'][30] = { wch: 24 };
            wsKibB['!cols'][31] = { wch: 22 }; wsKibB['!cols'][32] = { wch: 24 };

            wsKibB['!merges'] = getMutasiKibMerges([
                { s: { r: 3, c: 0 }, e: { r: 6, c: 0 } },
                { s: { r: 3, c: 1 }, e: { r: 3, c: 6 } },
                { s: { r: 4, c: 1 }, e: { r: 4, c: 2 } },
                { s: { r: 5, c: 1 }, e: { r: 6, c: 1 } },
                { s: { r: 5, c: 2 }, e: { r: 6, c: 2 } },
                { s: { r: 4, c: 3 }, e: { r: 4, c: 4 } },
                { s: { r: 5, c: 3 }, e: { r: 6, c: 3 } },
                { s: { r: 5, c: 4 }, e: { r: 6, c: 4 } },
                { s: { r: 4, c: 5 }, e: { r: 6, c: 5 } },
                { s: { r: 4, c: 6 }, e: { r: 6, c: 6 } },
                { s: { r: 3, c: 7 }, e: { r: 3, c: 25 } },
                { s: { r: 4, c: 7 }, e: { r: 6, c: 7 } },
                { s: { r: 4, c: 8 }, e: { r: 6, c: 8 } },
                { s: { r: 4, c: 9 }, e: { r: 6, c: 9 } },
                { s: { r: 4, c: 10 }, e: { r: 6, c: 10 } },
                { s: { r: 4, c: 11 }, e: { r: 6, c: 11 } },
                { s: { r: 4, c: 12 }, e: { r: 6, c: 12 } },
                { s: { r: 4, c: 13 }, e: { r: 6, c: 13 } },
                { s: { r: 4, c: 14 }, e: { r: 6, c: 14 } },
                { s: { r: 4, c: 15 }, e: { r: 6, c: 15 } },
                { s: { r: 4, c: 16 }, e: { r: 6, c: 16 } },
                { s: { r: 4, c: 17 }, e: { r: 6, c: 17 } },
                { s: { r: 4, c: 18 }, e: { r: 6, c: 18 } },
                { s: { r: 4, c: 19 }, e: { r: 4, c: 20 } },
                { s: { r: 5, c: 19 }, e: { r: 6, c: 19 } },
                { s: { r: 5, c: 20 }, e: { r: 6, c: 20 } },
                { s: { r: 4, c: 21 }, e: { r: 6, c: 21 } },
                { s: { r: 4, c: 22 }, e: { r: 4, c: 23 } },
                { s: { r: 5, c: 22 }, e: { r: 6, c: 22 } },
                { s: { r: 5, c: 23 }, e: { r: 6, c: 23 } },
                { s: { r: 4, c: 24 }, e: { r: 4, c: 25 } },
                { s: { r: 5, c: 24 }, e: { r: 6, c: 24 } },
                { s: { r: 5, c: 25 }, e: { r: 6, c: 25 } },
                { s: { r: 3, c: 26 }, e: { r: 6, c: 26 } },
                { s: { r: 3, c: 27 }, e: { r: 4, c: 29 } },
                { s: { r: 5, c: 27 }, e: { r: 6, c: 27 } },
                { s: { r: 5, c: 28 }, e: { r: 6, c: 28 } },
                { s: { r: 5, c: 29 }, e: { r: 6, c: 29 } },
                { s: { r: 3, c: 30 }, e: { r: 4, c: 31 } },
                { s: { r: 5, c: 30 }, e: { r: 6, c: 30 } },
                { s: { r: 5, c: 31 }, e: { r: 6, c: 31 } },
                { s: { r: 3, c: 32 }, e: { r: 6, c: 32 } }
            ], 33, kibBTitleRows.length, kibBRows.length, true);
            wsKibB['!merges'].push(...getKibSignatureMerges(kibBSignStartRow, 33, 24, 1, 8, 32));

            applyMutasiMasterSheetStyling(wsKibB, kibBRows.length, 33, 19, kibBTitleRows.length, true);
            applySignatureBlockStyling(wsKibB, kibBSignStartRow, 33);
            XLSX.utils.book_append_sheet(wb, wsKibB, filterCat === 'all' ? "3. KIB B - Mesin & Alkes" : "KIB B - Peralatan & Mesin");
        }

        // ========================================================================
        // 4. KIB C - BANGUNAN (31 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB C') {
            const kibCTitleRows = getMutasiKibTitleRows("GEDUNG DAN BANGUNAN MUTASI EKSTERNAL", yearLabel, filterTw, arahLabel);
            const kibCRows = [
                ...kibCTitleRows,
                [
                    "NO",
                    "ASET MUTASI EKSTERNAL (PMDN 108)", "", "", "", "", "",
                    "RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / " + yearLabel,
                    "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                    "SKPD / OPD MITRA MUTASI", "", "",
                    "Pengurus Barang / Pejabat RSUD", "",
                    "KET."
                ],
                [
                    "",
                    "Jenis Aset (PMDN 108)", "",
                    "Sub Rincian Objek (PMDN 108)", "",
                    "JUMLAH ANGGARAN (Rp)",
                    "NILAI PEROLEHAN BMD (Rp)",
                    "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                    "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                    "Kondisi\n(B,KB,RB)",
                    "Konstruksi Bangunan", "",
                    "Luas Lantai\n(m²)",
                    "Letak / Alamat Lokasi",
                    "Dokumen Gedung", "",
                    "Luas Tanah\nDasar (m²)",
                    "Status Tanah",
                    "Kode Tanah\nInduk",
                    "Dokumen BAST Mutasi", "",
                    "VOLUME", "",
                    "Nilai Barang (Rp)", "",
                    "", "", "",
                    "", "",
                    ""
                ],
                [
                    "",
                    "Kode", "Nama Jenis Aset",
                    "Kode", "Nama Uraian Sub Rincian Objek",
                    "", "",
                    "", "",
                    "",
                    "Bertingkat/\nTidak", "Beton/\nTidak",
                    "",
                    "",
                    "Tanggal", "Nomor",
                    "",
                    "",
                    "",
                    "Nomor", "Tanggal",
                    "Jumlah", "Satuan",
                    "Nilai Satuan (Rp)", "Total Nilai (Rp)",
                    "Nama Instansi Mitra", "Pejabat Mitra", "Alamat Mitra",
                    "Nama", "NIP",
                    ""
                ],
                [
                    "",
                    "", "", "", "", "", "",
                    "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                    "", "", "",
                    "", "",
                    ""
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                    31
                ]
            ];

            let kibCTotalAnggaran = 0;
            let kibCTotalRealisasi = 0;
            let kibCTotalUnit = 0;
            let kibCTotalNilaiBarang = 0;
            let globalKibCNo = 1;

            categories['KIB C'].forEach(item => {
                const spec = getSafeSpec(item);
                const subGedung = (spec.gedung_items && Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) ? spec.gedung_items : null;
                const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);

                kibCTotalAnggaran += 0;
                kibCTotalRealisasi += totalItemNilai;

                const docCols = getMutasiDokumenCols(item);
                const mitraCols = getMutasiMitraCols(item);
                const rsudCols = getMutasiPejabatRsudCols(item);
                let isFirst = true;

                if (subGedung) {
                    subGedung.forEach(gItem => {
                        const qty = Math.max(1, parseInt(gItem.gedung_jumlah_bangunan || 1));
                        const valSatuan = parseFloat(gItem.gedung_nilai_satuan || 0) || (qty > 0 ? totalItemNilai / subGedung.length / qty : 0);
                        const subTotal = (qty * valSatuan);

                        kibCTotalUnit += qty;
                        kibCTotalNilaiBarang += subTotal;

                        const col1to7 = buildMutasiCol1to7(item, globalKibCNo, isFirst, 0, totalItemNilai, '1.3.3', 'GEDUNG DAN BANGUNAN');
                        if (isFirst) { globalKibCNo++; isFirst = false; }

                        kibCRows.push([
                            ...col1to7,
                            gItem.gedung_nama_barang || item.nama_murni || item.nama_barang || '-',
                            gItem.kode_108 || item.kode_108 || item.kode_barang || '1.3.3.01.01.03.003',
                            gItem.gedung_kondisi || item.kondisi || 'Baik',
                            gItem.gedung_bertingkat || spec.gedung_bertingkat || 'Tidak',
                            gItem.gedung_beton || spec.gedung_beton || 'Beton',
                            parseFloat(gItem.gedung_luas_lantai || spec.luas_lantai || 0),
                            gItem.gedung_lokasi || item.alamat_barang || 'RSUD dr. H. Koesnadi',
                            formatAstapDate(gItem.gedung_dokumen_tanggal || spec.gedung_dokumen_tanggal),
                            gItem.gedung_dokumen_nomor || spec.gedung_dokumen_nomor || '-',
                            parseFloat(gItem.gedung_luas_tanah || spec.gedung_luas_tanah || 0),
                            gItem.gedung_status_tanah || spec.gedung_status_tanah || 'Tanah Pemkab',
                            gItem.gedung_kode_tanah || spec.gedung_kode_tanah || '-',
                            docCols[0],
                            docCols[1],
                            qty,
                            gItem.gedung_satuan || item.satuan || 'Bangunan',
                            valSatuan,
                            subTotal,
                            ...mitraCols,
                            ...rsudCols,
                            (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                        ]);
                    });
                } else {
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const valSatuan = parseFloat(item.harga_satuan) || (qty > 0 ? totalItemNilai / qty : totalItemNilai);

                    kibCTotalUnit += qty;
                    kibCTotalNilaiBarang += totalItemNilai;

                    const col1to7 = buildMutasiCol1to7(item, globalKibCNo, true, 0, totalItemNilai, '1.3.3', 'GEDUNG DAN BANGUNAN');
                    globalKibCNo++;

                    kibCRows.push([
                        ...col1to7,
                        item.nama_murni || item.nama_barang || '-',
                        item.kode_108 || item.kode_barang || '1.3.3.01.01.03.003',
                        item.kondisi || 'Baik',
                        spec.gedung_bertingkat || 'Tidak',
                        spec.gedung_beton || 'Beton',
                        parseFloat(spec.luas_lantai || spec.gedung_luas_lantai || 0),
                        item.alamat_barang || spec.gedung_lokasi || 'RSUD dr. H. Koesnadi',
                        formatAstapDate(spec.gedung_dokumen_tanggal),
                        spec.gedung_dokumen_nomor || '-',
                        parseFloat(spec.gedung_luas_tanah || 0),
                        spec.gedung_status_tanah || 'Tanah Pemkab',
                        spec.gedung_kode_tanah || '-',
                        docCols[0],
                        docCols[1],
                        qty,
                        item.satuan || 'Bangunan',
                        valSatuan,
                        totalItemNilai,
                        ...mitraCols,
                        ...rsudCols,
                        (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                    ]);
                }
            });

            // Footer
            const kibCFooterRow = Array(31).fill("");
            kibCFooterRow[0] = "JUMLAH";
            kibCFooterRow[5] = kibCTotalAnggaran;
            kibCFooterRow[6] = kibCTotalRealisasi;
            kibCFooterRow[21] = kibCTotalUnit;
            kibCFooterRow[24] = kibCTotalNilaiBarang;
            kibCRows.push(kibCFooterRow);

            const kibCSignStartRow = kibCRows.length;
            const kibCSignRows = buildMutasiSignRows(31, 23, 1);
            kibCSignRows.forEach(r => kibCRows.push(r));

            const wsKibC = XLSX.utils.aoa_to_sheet(kibCRows);
            wsKibC['!cols'] = Array(31).fill({ wch: 18 });
            wsKibC['!cols'][0] = { wch: 6 };
            wsKibC['!cols'][1] = { wch: 14 }; wsKibC['!cols'][2] = { wch: 26 };
            wsKibC['!cols'][3] = { wch: 18 }; wsKibC['!cols'][4] = { wch: 32 };
            wsKibC['!cols'][5] = { wch: 22 }; wsKibC['!cols'][6] = { wch: 22 };
            wsKibC['!cols'][7] = { wch: 32 }; wsKibC['!cols'][8] = { wch: 22 };
            wsKibC['!cols'][9] = { wch: 14 }; wsKibC['!cols'][10] = { wch: 16 };
            wsKibC['!cols'][11] = { wch: 16 }; wsKibC['!cols'][12] = { wch: 16 };
            wsKibC['!cols'][13] = { wch: 28 }; wsKibC['!cols'][14] = { wch: 14 };
            wsKibC['!cols'][15] = { wch: 20 }; wsKibC['!cols'][16] = { wch: 16 };
            wsKibC['!cols'][17] = { wch: 18 }; wsKibC['!cols'][18] = { wch: 20 };
            wsKibC['!cols'][19] = { wch: 24 }; wsKibC['!cols'][20] = { wch: 14 };
            wsKibC['!cols'][21] = { wch: 14 }; wsKibC['!cols'][22] = { wch: 14 };
            wsKibC['!cols'][23] = { wch: 22 }; wsKibC['!cols'][24] = { wch: 22 };
            wsKibC['!cols'][25] = { wch: 26 }; wsKibC['!cols'][26] = { wch: 22 };
            wsKibC['!cols'][27] = { wch: 26 }; wsKibC['!cols'][28] = { wch: 24 };
            wsKibC['!cols'][29] = { wch: 22 }; wsKibC['!cols'][30] = { wch: 24 };

            wsKibC['!merges'] = getMutasiKibMerges([
                { s: { r: 3, c: 0 }, e: { r: 6, c: 0 } },
                { s: { r: 3, c: 1 }, e: { r: 3, c: 6 } },
                { s: { r: 4, c: 1 }, e: { r: 4, c: 2 } },
                { s: { r: 5, c: 1 }, e: { r: 6, c: 1 } },
                { s: { r: 5, c: 2 }, e: { r: 6, c: 2 } },
                { s: { r: 4, c: 3 }, e: { r: 4, c: 4 } },
                { s: { r: 5, c: 3 }, e: { r: 6, c: 3 } },
                { s: { r: 5, c: 4 }, e: { r: 6, c: 4 } },
                { s: { r: 4, c: 5 }, e: { r: 6, c: 5 } },
                { s: { r: 4, c: 6 }, e: { r: 6, c: 6 } },
                { s: { r: 3, c: 7 }, e: { r: 3, c: 24 } },
                { s: { r: 4, c: 7 }, e: { r: 6, c: 7 } },
                { s: { r: 4, c: 8 }, e: { r: 6, c: 8 } },
                { s: { r: 4, c: 9 }, e: { r: 6, c: 9 } },
                { s: { r: 4, c: 10 }, e: { r: 4, c: 11 } },
                { s: { r: 5, c: 10 }, e: { r: 6, c: 10 } },
                { s: { r: 5, c: 11 }, e: { r: 6, c: 11 } },
                { s: { r: 4, c: 12 }, e: { r: 6, c: 12 } },
                { s: { r: 4, c: 13 }, e: { r: 6, c: 13 } },
                { s: { r: 4, c: 14 }, e: { r: 4, c: 15 } },
                { s: { r: 5, c: 14 }, e: { r: 6, c: 14 } },
                { s: { r: 5, c: 15 }, e: { r: 6, c: 15 } },
                { s: { r: 4, c: 16 }, e: { r: 6, c: 16 } },
                { s: { r: 4, c: 17 }, e: { r: 6, c: 17 } },
                { s: { r: 4, c: 18 }, e: { r: 6, c: 18 } },
                { s: { r: 4, c: 19 }, e: { r: 4, c: 20 } },
                { s: { r: 5, c: 19 }, e: { r: 6, c: 19 } },
                { s: { r: 5, c: 20 }, e: { r: 6, c: 20 } },
                { s: { r: 4, c: 21 }, e: { r: 4, c: 22 } },
                { s: { r: 5, c: 21 }, e: { r: 6, c: 21 } },
                { s: { r: 5, c: 22 }, e: { r: 6, c: 22 } },
                { s: { r: 4, c: 23 }, e: { r: 4, c: 24 } },
                { s: { r: 5, c: 23 }, e: { r: 6, c: 23 } },
                { s: { r: 5, c: 24 }, e: { r: 6, c: 24 } },
                { s: { r: 3, c: 25 }, e: { r: 4, c: 27 } },
                { s: { r: 5, c: 25 }, e: { r: 6, c: 25 } },
                { s: { r: 5, c: 26 }, e: { r: 6, c: 26 } },
                { s: { r: 5, c: 27 }, e: { r: 6, c: 27 } },
                { s: { r: 3, c: 28 }, e: { r: 4, c: 29 } },
                { s: { r: 5, c: 28 }, e: { r: 6, c: 28 } },
                { s: { r: 5, c: 29 }, e: { r: 6, c: 29 } },
                { s: { r: 3, c: 30 }, e: { r: 6, c: 30 } }
            ], 31, kibCTitleRows.length, kibCRows.length, true);
            wsKibC['!merges'].push(...getKibSignatureMerges(kibCSignStartRow, 31, 23, 1, 8, 30));

            applyMutasiMasterSheetStyling(wsKibC, kibCRows.length, 31, 18, kibCTitleRows.length, true);
            applySignatureBlockStyling(wsKibC, kibCSignStartRow, 31);
            XLSX.utils.book_append_sheet(wb, wsKibC, filterCat === 'all' ? "4. KIB C - Bangunan" : "KIB C - Bangunan");
        }

        // ========================================================================
        // 5. KIB D - JARINGAN (28 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB D') {
            const kibDTitleRows = getMutasiKibTitleRows("JALAN, IRIGASI DAN JARINGAN MUTASI EKSTERNAL", yearLabel, filterTw, arahLabel);
            const kibDRows = [
                ...kibDTitleRows,
                [
                    "NO",
                    "ASET MUTASI EKSTERNAL (PMDN 108)", "", "", "", "", "",
                    "RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / " + yearLabel,
                    "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                    "SKPD / OPD MITRA MUTASI", "", "",
                    "Pengurus Barang / Pejabat RSUD", "",
                    "KET."
                ],
                [
                    "",
                    "Jenis Aset (PMDN 108)", "",
                    "Sub Rincian Objek (PMDN 108)", "",
                    "JUMLAH ANGGARAN (Rp)",
                    "NILAI PEROLEHAN BMD (Rp)",
                    "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                    "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                    "Konstruksi",
                    "Panjang\n(Km/m)",
                    "Lebar\n(m)",
                    "Luas\n(m²)",
                    "Letak / Lokasi",
                    "Dokumen Jaringan", "",
                    "Status Tanah",
                    "Kode Tanah",
                    "Kondisi\n(B,KB,RB)",
                    "Dokumen BAST Mutasi", "",
                    "Nilai Barang (Rp)", "",
                    "", "", "",
                    "", "",
                    ""
                ],
                [
                    "",
                    "Kode", "Nama Jenis Aset",
                    "Kode", "Nama Uraian Sub Rincian Objek",
                    "", "",
                    "", "",
                    "", "", "", "", "",
                    "Tanggal", "Nomor",
                    "", "", "",
                    "Nomor", "Tanggal",
                    "Nilai Satuan (Rp)", "Total Nilai (Rp)",
                    "Nama Instansi Mitra", "Pejabat Mitra", "Alamat Mitra",
                    "Nama", "NIP",
                    ""
                ],
                [
                    "",
                    "", "", "", "", "", "",
                    "", "",
                    "", "", "", "", "",
                    "", "",
                    "", "", "",
                    "", "",
                    "", "",
                    "", "", "",
                    "", "",
                    ""
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28
                ]
            ];

            let kibDTotalAnggaran = 0;
            let kibDTotalRealisasi = 0;
            let kibDTotalNilaiBarang = 0;
            let globalKibDNo = 1;

            categories['KIB D'].forEach(item => {
                const spec = getSafeSpec(item);
                const subJaringan = (spec.jaringan_items && Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) ? spec.jaringan_items : null;
                const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);

                kibDTotalAnggaran += 0;
                kibDTotalRealisasi += totalItemNilai;

                const docCols = getMutasiDokumenCols(item);
                const mitraCols = getMutasiMitraCols(item);
                const rsudCols = getMutasiPejabatRsudCols(item);
                let isFirst = true;

                if (subJaringan) {
                    subJaringan.forEach(jItem => {
                        const qty = Math.max(1, parseInt(jItem.jaringan_jumlah || 1));
                        const valSatuan = parseFloat(jItem.jaringan_nilai_satuan || 0) || (qty > 0 ? totalItemNilai / subJaringan.length / qty : 0);
                        const subTotal = (qty * valSatuan);

                        kibDTotalNilaiBarang += subTotal;

                        const col1to7 = buildMutasiCol1to7(item, globalKibDNo, isFirst, 0, totalItemNilai, '1.3.4', 'JALAN, IRIGASI DAN JARINGAN');
                        if (isFirst) { globalKibDNo++; isFirst = false; }

                        kibDRows.push([
                            ...col1to7,
                            jItem.jaringan_nama_barang || item.nama_murni || item.nama_barang || '-',
                            jItem.kode_108 || item.kode_108 || item.kode_barang || '1.3.4.01.01.04.004',
                            jItem.jaringan_konstruksi || spec.jaringan_konstruksi || 'Permanen',
                            parseFloat(jItem.jaringan_panjang || spec.jaringan_panjang || 0),
                            parseFloat(jItem.jaringan_lebar || spec.jaringan_lebar || 0),
                            parseFloat(jItem.jaringan_luas || spec.jaringan_luas || 0),
                            item.alamat_barang || 'RSUD dr. H. Koesnadi',
                            formatAstapDate(jItem.jaringan_dokumen_tanggal || spec.jaringan_dokumen_tanggal),
                            jItem.jaringan_dokumen_nomor || spec.jaringan_dokumen_nomor || '-',
                            jItem.jaringan_status_tanah || spec.jaringan_status_tanah || 'Tanah Pemkab',
                            jItem.jaringan_kode_tanah || spec.jaringan_kode_tanah || '-',
                            jItem.jaringan_kondisi || item.kondisi || 'Baik',
                            docCols[0],
                            docCols[1],
                            valSatuan,
                            subTotal,
                            ...mitraCols,
                            ...rsudCols,
                            (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                        ]);
                    });
                } else {
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const valSatuan = parseFloat(item.harga_satuan) || (qty > 0 ? totalItemNilai / qty : totalItemNilai);

                    kibDTotalNilaiBarang += totalItemNilai;

                    const col1to7 = buildMutasiCol1to7(item, globalKibDNo, true, 0, totalItemNilai, '1.3.4', 'JALAN, IRIGASI DAN JARINGAN');
                    globalKibDNo++;

                    kibDRows.push([
                        ...col1to7,
                        item.nama_murni || item.nama_barang || '-',
                        item.kode_108 || item.kode_barang || '1.3.4.01.01.04.004',
                        spec.jaringan_konstruksi || 'Permanen',
                        parseFloat(spec.jaringan_panjang || 0),
                        parseFloat(spec.jaringan_lebar || 0),
                        parseFloat(spec.jaringan_luas || 0),
                        item.alamat_barang || 'RSUD dr. H. Koesnadi',
                        formatAstapDate(spec.jaringan_dokumen_tanggal),
                        spec.jaringan_dokumen_nomor || '-',
                        spec.jaringan_status_tanah || 'Tanah Pemkab',
                        spec.jaringan_kode_tanah || '-',
                        item.kondisi || 'Baik',
                        docCols[0],
                        docCols[1],
                        valSatuan,
                        totalItemNilai,
                        ...mitraCols,
                        ...rsudCols,
                        (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                    ]);
                }
            });

            // Footer
            const kibDFooterRow = Array(28).fill("");
            kibDFooterRow[0] = "JUMLAH";
            kibDFooterRow[5] = kibDTotalAnggaran;
            kibDFooterRow[6] = kibDTotalRealisasi;
            kibDFooterRow[22] = kibDTotalNilaiBarang;
            kibDRows.push(kibDFooterRow);

            const kibDSignStartRow = kibDRows.length;
            const kibDSignRows = buildMutasiSignRows(28, 20, 1);
            kibDSignRows.forEach(r => kibDRows.push(r));

            const wsKibD = XLSX.utils.aoa_to_sheet(kibDRows);
            wsKibD['!cols'] = Array(28).fill({ wch: 18 });
            wsKibD['!cols'][0] = { wch: 6 };
            wsKibD['!cols'][1] = { wch: 14 }; wsKibD['!cols'][2] = { wch: 26 };
            wsKibD['!cols'][3] = { wch: 18 }; wsKibD['!cols'][4] = { wch: 32 };
            wsKibD['!cols'][5] = { wch: 22 }; wsKibD['!cols'][6] = { wch: 22 };
            wsKibD['!cols'][7] = { wch: 32 }; wsKibD['!cols'][8] = { wch: 22 };
            wsKibD['!cols'][9] = { wch: 16 }; wsKibD['!cols'][10] = { wch: 14 };
            wsKibD['!cols'][11] = { wch: 14 }; wsKibD['!cols'][12] = { wch: 14 };
            wsKibD['!cols'][13] = { wch: 26 }; wsKibD['!cols'][14] = { wch: 14 };
            wsKibD['!cols'][15] = { wch: 20 }; wsKibD['!cols'][16] = { wch: 16 };
            wsKibD['!cols'][17] = { wch: 18 }; wsKibD['!cols'][18] = { wch: 14 };
            wsKibD['!cols'][19] = { wch: 24 }; wsKibD['!cols'][20] = { wch: 14 };
            wsKibD['!cols'][21] = { wch: 22 }; wsKibD['!cols'][22] = { wch: 22 };
            wsKibD['!cols'][23] = { wch: 26 }; wsKibD['!cols'][24] = { wch: 22 };
            wsKibD['!cols'][25] = { wch: 26 }; wsKibD['!cols'][26] = { wch: 24 };
            wsKibD['!cols'][27] = { wch: 22 };

            wsKibD['!merges'] = getMutasiKibMerges([
                { s: { r: 3, c: 0 }, e: { r: 6, c: 0 } },
                { s: { r: 3, c: 1 }, e: { r: 3, c: 6 } },
                { s: { r: 4, c: 1 }, e: { r: 4, c: 2 } },
                { s: { r: 5, c: 1 }, e: { r: 6, c: 1 } },
                { s: { r: 5, c: 2 }, e: { r: 6, c: 2 } },
                { s: { r: 4, c: 3 }, e: { r: 4, c: 4 } },
                { s: { r: 5, c: 3 }, e: { r: 6, c: 3 } },
                { s: { r: 5, c: 4 }, e: { r: 6, c: 4 } },
                { s: { r: 4, c: 5 }, e: { r: 6, c: 5 } },
                { s: { r: 4, c: 6 }, e: { r: 6, c: 6 } },
                { s: { r: 3, c: 7 }, e: { r: 3, c: 22 } },
                { s: { r: 4, c: 7 }, e: { r: 6, c: 7 } },
                { s: { r: 4, c: 8 }, e: { r: 6, c: 8 } },
                { s: { r: 4, c: 9 }, e: { r: 6, c: 9 } },
                { s: { r: 4, c: 10 }, e: { r: 6, c: 10 } },
                { s: { r: 4, c: 11 }, e: { r: 6, c: 11 } },
                { s: { r: 4, c: 12 }, e: { r: 6, c: 12 } },
                { s: { r: 4, c: 13 }, e: { r: 6, c: 13 } },
                { s: { r: 4, c: 14 }, e: { r: 4, c: 15 } },
                { s: { r: 5, c: 14 }, e: { r: 6, c: 14 } },
                { s: { r: 5, c: 15 }, e: { r: 6, c: 15 } },
                { s: { r: 4, c: 16 }, e: { r: 6, c: 16 } },
                { s: { r: 4, c: 17 }, e: { r: 6, c: 17 } },
                { s: { r: 4, c: 18 }, e: { r: 6, c: 18 } },
                { s: { r: 4, c: 19 }, e: { r: 4, c: 20 } },
                { s: { r: 5, c: 19 }, e: { r: 6, c: 19 } },
                { s: { r: 5, c: 20 }, e: { r: 6, c: 20 } },
                { s: { r: 4, c: 21 }, e: { r: 4, c: 22 } },
                { s: { r: 5, c: 21 }, e: { r: 6, c: 21 } },
                { s: { r: 5, c: 22 }, e: { r: 6, c: 22 } },
                { s: { r: 3, c: 23 }, e: { r: 4, c: 25 } },
                { s: { r: 5, c: 23 }, e: { r: 6, c: 23 } },
                { s: { r: 5, c: 24 }, e: { r: 6, c: 24 } },
                { s: { r: 5, c: 25 }, e: { r: 6, c: 25 } },
                { s: { r: 3, c: 26 }, e: { r: 4, c: 27 } },
                { s: { r: 5, c: 26 }, e: { r: 6, c: 26 } },
                { s: { r: 5, c: 27 }, e: { r: 6, c: 27 } }
            ], 28, kibDTitleRows.length, kibDRows.length, true);
            wsKibD['!merges'].push(...getKibSignatureMerges(kibDSignStartRow, 28, 20, 1, 8, 27));

            applyMutasiMasterSheetStyling(wsKibD, kibDRows.length, 28, 16, kibDTitleRows.length, true);
            applySignatureBlockStyling(wsKibD, kibDSignStartRow, 28);
            XLSX.utils.book_append_sheet(wb, wsKibD, filterCat === 'all' ? "5. KIB D - Jaringan" : "KIB D - Jaringan");
        }

        // ========================================================================
        // 6. KIB E - ASET TETAP LAINNYA (32 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB E') {
            const kibETitleRows = getMutasiKibTitleRows("ASET TETAP LAINNYA MUTASI EKSTERNAL", yearLabel, filterTw, arahLabel);
            const kibERows = [
                ...kibETitleRows,
                [
                    "NO",
                    "ASET MUTASI EKSTERNAL (PMDN 108)", "", "", "", "", "",
                    "RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / " + yearLabel,
                    "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "", "",
                    "RUANGAN /\nPEMEGANG",
                    "SKPD / OPD MITRA MUTASI", "", "",
                    "Pengurus Barang / Pejabat RSUD", "",
                    "KET."
                ],
                [
                    "",
                    "Jenis Aset (PMDN 108)", "",
                    "Sub Rincian Objek (PMDN 108)", "",
                    "JUMLAH ANGGARAN (Rp)",
                    "NILAI PEROLEHAN BMD (Rp)",
                    "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                    "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                    "BUKU PERPUSTAKAAN", "", "",
                    "Barang Bercorak Seni / Budaya", "", "", "",
                    "Hewan Ternak / Tumbuhan", "", "",
                    "Dokumen BAST Mutasi", "",
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
                    "", "",
                    "Judul", "Pencipta", "Spesifikasi",
                    "Asal Daerah", "Pencipta", "Bahan", "Spesifikasi",
                    "Ukuran (m/cm)", "Jenis", "Spesifikasi",
                    "Nomor", "Tanggal",
                    "Jumlah", "Satuan",
                    "Nilai Satuan (Rp)", "Total Nilai (Rp)",
                    "",
                    "Nama Instansi Mitra", "Pejabat Mitra", "Alamat Mitra",
                    "Nama", "NIP",
                    ""
                ],
                [
                    "",
                    "", "", "", "", "", "",
                    "", "",
                    "", "", "",
                    "", "", "", "",
                    "", "", "",
                    "", "",
                    "", "",
                    "", "",
                    "",
                    "", "", "",
                    "", "",
                    ""
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                    31, 32
                ]
            ];

            let kibETotalAnggaran = 0;
            let kibETotalRealisasi = 0;
            let kibETotalUnit = 0;
            let kibETotalNilaiBarang = 0;
            let globalKibENo = 1;

            categories['KIB E'].forEach(item => {
                const spec = getSafeSpec(item);
                const subLainnya = (spec.lainnya_items && Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) ? spec.lainnya_items : null;
                const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);

                kibETotalAnggaran += 0;
                kibETotalRealisasi += totalItemNilai;

                const docCols = getMutasiDokumenCols(item);
                const mitraCols = getMutasiMitraCols(item);
                const rsudCols = getMutasiPejabatRsudCols(item);
                const defaultRuang = item.ruangan_tujuan || 'RSUD dr. H. Koesnadi';
                let isFirst = true;

                if (subLainnya) {
                    subLainnya.forEach(lItem => {
                        const qty = Math.max(1, parseInt(lItem.lainnya_jumlah || 1));
                        const valSatuan = parseFloat(lItem.lainnya_nilai_satuan || 0) || (qty > 0 ? totalItemNilai / subLainnya.length / qty : 0);
                        const subTotal = (qty * valSatuan);

                        kibETotalUnit += qty;
                        kibETotalNilaiBarang += subTotal;

                        const col1to7 = buildMutasiCol1to7(item, globalKibENo, isFirst, 0, totalItemNilai, '1.3.5', 'ASET TETAP LAINNYA');
                        if (isFirst) { globalKibENo++; isFirst = false; }

                        kibERows.push([
                            ...col1to7,
                            lItem.lainnya_nama_barang || lItem.lainnya_judul || item.nama_murni || item.nama_barang || '-',
                            lItem.kode_108 || item.kode_108 || item.kode_barang || '1.3.5.01.01.02.005',
                            lItem.lainnya_judul || spec.buku_judul || '-',
                            lItem.lainnya_spesifikasi || spec.buku_pengarang || '-',
                            spec.buku_penerbit || '-',
                            spec.kesenian_asal || '-',
                            spec.kesenian_pencipta || '-',
                            spec.kesenian_bahan || spec.bahan || '-',
                            spec.kesenian_spesifikasi || '-',
                            spec.hewan_ukuran || '-',
                            spec.hewan_jenis || '-',
                            spec.hewan_spesifikasi || '-',
                            docCols[0],
                            docCols[1],
                            qty,
                            lItem.lainnya_satuan || item.satuan || 'Unit',
                            valSatuan,
                            subTotal,
                            lItem.ruang_pemegang || defaultRuang,
                            ...mitraCols,
                            ...rsudCols,
                            (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                        ]);
                    });
                } else {
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const valSatuan = parseFloat(item.harga_satuan) || (qty > 0 ? totalItemNilai / qty : totalItemNilai);

                    kibETotalUnit += qty;
                    kibETotalNilaiBarang += totalItemNilai;

                    const col1to7 = buildMutasiCol1to7(item, globalKibENo, true, 0, totalItemNilai, '1.3.5', 'ASET TETAP LAINNYA');
                    globalKibENo++;

                    kibERows.push([
                        ...col1to7,
                        item.nama_murni || item.nama_barang || '-',
                        item.kode_108 || item.kode_barang || '1.3.5.01.01.02.005',
                        spec.buku_judul || spec.lainnya_judul || '-',
                        spec.buku_pengarang || spec.lainnya_spesifikasi || '-',
                        spec.buku_penerbit || '-',
                        spec.kesenian_asal || '-',
                        spec.kesenian_pencipta || '-',
                        spec.kesenian_bahan || spec.bahan || '-',
                        spec.kesenian_spesifikasi || '-',
                        spec.hewan_ukuran || '-',
                        spec.hewan_jenis || '-',
                        spec.hewan_spesifikasi || '-',
                        docCols[0],
                        docCols[1],
                        qty,
                        item.satuan || 'Unit',
                        valSatuan,
                        totalItemNilai,
                        defaultRuang,
                        ...mitraCols,
                        ...rsudCols,
                        (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                    ]);
                }
            });

            // Footer
            const kibEFooterRow = Array(32).fill("");
            kibEFooterRow[0] = "JUMLAH";
            kibEFooterRow[5] = kibETotalAnggaran;
            kibEFooterRow[6] = kibETotalRealisasi;
            kibEFooterRow[21] = kibETotalUnit;
            kibEFooterRow[24] = kibETotalNilaiBarang;
            kibERows.push(kibEFooterRow);

            const kibESignStartRow = kibERows.length;
            const kibESignRows = buildMutasiSignRows(32, 24, 1);
            kibESignRows.forEach(r => kibERows.push(r));

            const wsKibE = XLSX.utils.aoa_to_sheet(kibERows);
            wsKibE['!cols'] = Array(32).fill({ wch: 18 });
            wsKibE['!cols'][0] = { wch: 6 };
            wsKibE['!cols'][1] = { wch: 14 }; wsKibE['!cols'][2] = { wch: 26 };
            wsKibE['!cols'][3] = { wch: 18 }; wsKibE['!cols'][4] = { wch: 32 };
            wsKibE['!cols'][5] = { wch: 22 }; wsKibE['!cols'][6] = { wch: 22 };
            wsKibE['!cols'][7] = { wch: 32 }; wsKibE['!cols'][8] = { wch: 22 };
            wsKibE['!cols'][9] = { wch: 22 }; wsKibE['!cols'][10] = { wch: 18 };
            wsKibE['!cols'][11] = { wch: 18 }; wsKibE['!cols'][12] = { wch: 18 };
            wsKibE['!cols'][13] = { wch: 18 }; wsKibE['!cols'][14] = { wch: 16 };
            wsKibE['!cols'][15] = { wch: 18 }; wsKibE['!cols'][16] = { wch: 16 };
            wsKibE['!cols'][17] = { wch: 16 }; wsKibE['!cols'][18] = { wch: 18 };
            wsKibE['!cols'][19] = { wch: 24 }; wsKibE['!cols'][20] = { wch: 14 };
            wsKibE['!cols'][21] = { wch: 14 }; wsKibE['!cols'][22] = { wch: 14 };
            wsKibE['!cols'][23] = { wch: 22 }; wsKibE['!cols'][24] = { wch: 22 };
            wsKibE['!cols'][25] = { wch: 26 }; wsKibE['!cols'][26] = { wch: 26 };
            wsKibE['!cols'][27] = { wch: 22 }; wsKibE['!cols'][28] = { wch: 26 };
            wsKibE['!cols'][29] = { wch: 24 }; wsKibE['!cols'][30] = { wch: 22 };
            wsKibE['!cols'][31] = { wch: 24 };

            wsKibE['!merges'] = getMutasiKibMerges([
                { s: { r: 3, c: 0 }, e: { r: 6, c: 0 } },
                { s: { r: 3, c: 1 }, e: { r: 3, c: 6 } },
                { s: { r: 4, c: 1 }, e: { r: 4, c: 2 } },
                { s: { r: 5, c: 1 }, e: { r: 6, c: 1 } },
                { s: { r: 5, c: 2 }, e: { r: 6, c: 2 } },
                { s: { r: 4, c: 3 }, e: { r: 4, c: 4 } },
                { s: { r: 5, c: 3 }, e: { r: 6, c: 3 } },
                { s: { r: 5, c: 4 }, e: { r: 6, c: 4 } },
                { s: { r: 4, c: 5 }, e: { r: 6, c: 5 } },
                { s: { r: 4, c: 6 }, e: { r: 6, c: 6 } },
                { s: { r: 3, c: 7 }, e: { r: 3, c: 24 } },
                { s: { r: 4, c: 7 }, e: { r: 6, c: 7 } },
                { s: { r: 4, c: 8 }, e: { r: 6, c: 8 } },
                { s: { r: 4, c: 9 }, e: { r: 4, c: 11 } },
                { s: { r: 5, c: 9 }, e: { r: 6, c: 9 } },
                { s: { r: 5, c: 10 }, e: { r: 6, c: 10 } },
                { s: { r: 5, c: 11 }, e: { r: 6, c: 11 } },
                { s: { r: 4, c: 12 }, e: { r: 4, c: 15 } },
                { s: { r: 5, c: 12 }, e: { r: 6, c: 12 } },
                { s: { r: 5, c: 13 }, e: { r: 6, c: 13 } },
                { s: { r: 5, c: 14 }, e: { r: 6, c: 14 } },
                { s: { r: 5, c: 15 }, e: { r: 6, c: 15 } },
                { s: { r: 4, c: 16 }, e: { r: 4, c: 18 } },
                { s: { r: 5, c: 16 }, e: { r: 6, c: 16 } },
                { s: { r: 5, c: 17 }, e: { r: 6, c: 17 } },
                { s: { r: 5, c: 18 }, e: { r: 6, c: 18 } },
                { s: { r: 4, c: 19 }, e: { r: 4, c: 20 } },
                { s: { r: 5, c: 19 }, e: { r: 6, c: 19 } },
                { s: { r: 5, c: 20 }, e: { r: 6, c: 20 } },
                { s: { r: 4, c: 21 }, e: { r: 4, c: 22 } },
                { s: { r: 5, c: 21 }, e: { r: 6, c: 21 } },
                { s: { r: 5, c: 22 }, e: { r: 6, c: 22 } },
                { s: { r: 4, c: 23 }, e: { r: 4, c: 24 } },
                { s: { r: 5, c: 23 }, e: { r: 6, c: 23 } },
                { s: { r: 5, c: 24 }, e: { r: 6, c: 24 } },
                { s: { r: 3, c: 25 }, e: { r: 6, c: 25 } },
                { s: { r: 3, c: 26 }, e: { r: 4, c: 28 } },
                { s: { r: 5, c: 26 }, e: { r: 6, c: 26 } },
                { s: { r: 5, c: 27 }, e: { r: 6, c: 27 } },
                { s: { r: 5, c: 28 }, e: { r: 6, c: 28 } },
                { s: { r: 3, c: 29 }, e: { r: 4, c: 30 } },
                { s: { r: 5, c: 29 }, e: { r: 6, c: 29 } },
                { s: { r: 5, c: 30 }, e: { r: 6, c: 30 } },
                { s: { r: 3, c: 31 }, e: { r: 6, c: 31 } }
            ], 32, kibETitleRows.length, kibERows.length, true);
            wsKibE['!merges'].push(...getKibSignatureMerges(kibESignStartRow, 32, 24, 1, 8, 31));

            applyMutasiMasterSheetStyling(wsKibE, kibERows.length, 32, 18, kibETitleRows.length, true);
            applySignatureBlockStyling(wsKibE, kibESignStartRow, 32);
            XLSX.utils.book_append_sheet(wb, wsKibE, filterCat === 'all' ? "6. KIB E - Aset Lainnya" : "KIB E - Aset Tetap Lainnya");
        }

        // ========================================================================
        // 7. EXTRACOM - BARANG EKSTRAKOMTABEL (29 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'EXTRACOM') {
            const extracomTitleRows = getMutasiKibTitleRows("BARANG EKSTRAKOMTABEL (< RP 300.000) MUTASI EKSTERNAL", yearLabel, filterTw, arahLabel);
            const extracomRows = [
                ...extracomTitleRows,
                [
                    "NO",
                    "ASET MUTASI EKSTERNAL (PMDN 108)", "", "", "", "", "",
                    "RINCIAN ASET EKSTRAKOMTABEL SESUAI BAST / SK BUPATI / " + yearLabel,
                    "", "", "", "", "", "", "", "", "", "", "", "", "",
                    "RUANGAN /\nPEMEGANG",
                    "SKPD / OPD MITRA MUTASI", "", "",
                    "Pengurus Barang / Pejabat RSUD", "",
                    "KET."
                ],
                [
                    "",
                    "Jenis Aset (PMDN 108)", "",
                    "Sub Rincian Objek (PMDN 108)", "",
                    "JUMLAH ANGGARAN (Rp)",
                    "NILAI PEROLEHAN BMD (Rp)",
                    "NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)",
                    "Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)",
                    "Merk", "Type", "Ukuran / Spesifikasi",
                    "No. Pabrik", "BAHAN", "Tahun Perolehan",
                    "Dokumen BAST Mutasi", "",
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
                    "", "",
                    "", "", "", "", "", "",
                    "Nomor", "Tanggal",
                    "",
                    "Jumlah Barang", "Nama Satuan Barang",
                    "Nilai Satuan Barang (Rp)", "Total Nilai Barang (Rp)",
                    "",
                    "Nama Instansi Mitra", "Pejabat Mitra", "Alamat Mitra",
                    "Nama", "NIP",
                    ""
                ],
                [
                    "",
                    "", "", "", "", "", "",
                    "", "",
                    "", "", "", "", "", "",
                    "", "",
                    "",
                    "", "",
                    "", "",
                    "",
                    "", "", "",
                    "", "",
                    ""
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29
                ]
            ];

            let extracomTotalAnggaran = 0;
            let extracomTotalRealisasi = 0;
            let extracomTotalUnit = 0;
            let extracomTotalNilaiBarang = 0;
            let globalExtracomNo = 1;

            categories['EXTRACOM'].forEach(item => {
                const spec = getSafeSpec(item);
                const subMesin = (spec.mesin_items && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) ? spec.mesin_items : null;
                const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);

                extracomTotalAnggaran += 0;
                extracomTotalRealisasi += totalItemNilai;

                const docCols = getMutasiDokumenCols(item);
                const mitraCols = getMutasiMitraCols(item);
                const rsudCols = getMutasiPejabatRsudCols(item);
                const thnPerolehan = item.tahun_perolehan || spec.tahun_pembuatan || new Date().getFullYear();
                const defaultRuang = item.ruangan_tujuan || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnadi');
                let isFirst = true;

                if (subMesin) {
                    subMesin.forEach(mItem => {
                        const qty = Math.max(1, parseInt(mItem.mesin_jumlah_barang || mItem.mesin_jumlah || 1));
                        const valSatuan = parseFloat(mItem.mesin_nilai_satuan || 0) || (qty > 0 ? totalItemNilai / subMesin.length / qty : 0);
                        const subTotal = (qty * valSatuan);

                        extracomTotalUnit += qty;
                        extracomTotalNilaiBarang += subTotal;

                        const col1to7 = buildMutasiCol1to7(item, globalExtracomNo, isFirst, 0, totalItemNilai, '1.3.2', 'PERALATAN DAN MESIN');
                        if (isFirst) { globalExtracomNo++; isFirst = false; }

                        extracomRows.push([
                            ...col1to7,
                            mItem.mesin_nama_barang || item.nama_murni || item.nama_barang || '-',
                            mItem.kode_108 || item.kode_108 || item.kode_barang || '1.3.2.01.01.04.002',
                            mItem.mesin_merk || spec.merk || '-',
                            mItem.mesin_type || spec.type || '-',
                            mItem.mesin_ukuran_cc || spec.ukuran || '-',
                            mItem.mesin_nomor_pabrik || spec.no_pabrik || '-',
                            mItem.mesin_bahan || spec.bahan || '-',
                            thnPerolehan,
                            docCols[0],
                            docCols[1],
                            mItem.mesin_kondisi || item.kondisi || 'Baik',
                            qty,
                            mItem.mesin_satuan || item.satuan || 'Unit',
                            valSatuan,
                            subTotal,
                            mItem.ruang_pemegang || defaultRuang,
                            ...mitraCols,
                            ...rsudCols,
                            (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                        ]);
                    });
                } else {
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const valSatuan = parseFloat(item.harga_satuan) || (qty > 0 ? totalItemNilai / qty : totalItemNilai);

                    extracomTotalUnit += qty;
                    extracomTotalNilaiBarang += totalItemNilai;

                    const col1to7 = buildMutasiCol1to7(item, globalExtracomNo, true, 0, totalItemNilai, '1.3.2', 'PERALATAN DAN MESIN');
                    globalExtracomNo++;

                    extracomRows.push([
                        ...col1to7,
                        item.nama_murni || item.nama_barang || '-',
                        item.kode_108 || item.kode_barang || '1.3.2.01.01.04.002',
                        spec.merk || spec.mesin_merk || '-',
                        spec.type || spec.tipe || '-',
                        spec.ukuran || spec.mesin_ukuran_cc || '-',
                        spec.no_pabrik || spec.mesin_nomor_pabrik || '-',
                        spec.bahan || spec.mesin_bahan || '-',
                        thnPerolehan,
                        docCols[0],
                        docCols[1],
                        item.kondisi || 'Baik',
                        qty,
                        item.satuan || 'Unit',
                        valSatuan,
                        totalItemNilai,
                        defaultRuang,
                        ...mitraCols,
                        ...rsudCols,
                        (item.tipe === 'keluar' ? 'Transfer Keluar' : 'Mutasi Masuk') + (item.nomor_sk_dasar ? ' · SK: ' + item.nomor_sk_dasar : '')
                    ]);
                }
            });

            // Footer
            const extracomFooterRow = Array(29).fill("");
            extracomFooterRow[0] = "JUMLAH";
            extracomFooterRow[5] = extracomTotalAnggaran;
            extracomFooterRow[6] = extracomTotalRealisasi;
            extracomFooterRow[18] = extracomTotalUnit;
            extracomFooterRow[21] = extracomTotalNilaiBarang;
            extracomRows.push(extracomFooterRow);

            const extracomSignStartRow = extracomRows.length;
            const extracomSignRows = buildMutasiSignRows(29, 21, 1);
            extracomSignRows.forEach(r => extracomRows.push(r));

            const wsExtracom = XLSX.utils.aoa_to_sheet(extracomRows);
            wsExtracom['!cols'] = Array(29).fill({ wch: 17 });
            wsExtracom['!cols'][0] = { wch: 6 };
            wsExtracom['!cols'][1] = { wch: 14 }; wsExtracom['!cols'][2] = { wch: 26 };
            wsExtracom['!cols'][3] = { wch: 18 }; wsExtracom['!cols'][4] = { wch: 32 };
            wsExtracom['!cols'][5] = { wch: 22 }; wsExtracom['!cols'][6] = { wch: 22 };
            wsExtracom['!cols'][7] = { wch: 32 }; wsExtracom['!cols'][8] = { wch: 22 };
            wsExtracom['!cols'][9] = { wch: 20 }; wsExtracom['!cols'][10] = { wch: 18 };
            wsExtracom['!cols'][11] = { wch: 20 }; wsExtracom['!cols'][12] = { wch: 18 };
            wsExtracom['!cols'][13] = { wch: 16 }; wsExtracom['!cols'][14] = { wch: 16 };
            wsExtracom['!cols'][15] = { wch: 24 }; wsExtracom['!cols'][16] = { wch: 14 };
            wsExtracom['!cols'][17] = { wch: 14 }; wsExtracom['!cols'][18] = { wch: 14 };
            wsExtracom['!cols'][19] = { wch: 16 }; wsExtracom['!cols'][20] = { wch: 22 };
            wsExtracom['!cols'][21] = { wch: 22 }; wsExtracom['!cols'][22] = { wch: 26 };
            wsExtracom['!cols'][23] = { wch: 26 }; wsExtracom['!cols'][24] = { wch: 22 };
            wsExtracom['!cols'][25] = { wch: 26 }; wsExtracom['!cols'][26] = { wch: 24 };
            wsExtracom['!cols'][27] = { wch: 22 }; wsExtracom['!cols'][28] = { wch: 24 };

            wsExtracom['!merges'] = getMutasiKibMerges([
                { s: { r: 3, c: 0 }, e: { r: 6, c: 0 } },
                { s: { r: 3, c: 1 }, e: { r: 3, c: 6 } },
                { s: { r: 4, c: 1 }, e: { r: 4, c: 2 } },
                { s: { r: 5, c: 1 }, e: { r: 6, c: 1 } },
                { s: { r: 5, c: 2 }, e: { r: 6, c: 2 } },
                { s: { r: 4, c: 3 }, e: { r: 4, c: 4 } },
                { s: { r: 5, c: 3 }, e: { r: 6, c: 3 } },
                { s: { r: 5, c: 4 }, e: { r: 6, c: 4 } },
                { s: { r: 4, c: 5 }, e: { r: 6, c: 5 } },
                { s: { r: 4, c: 6 }, e: { r: 6, c: 6 } },
                { s: { r: 3, c: 7 }, e: { r: 3, c: 21 } },
                { s: { r: 4, c: 7 }, e: { r: 6, c: 7 } },
                { s: { r: 4, c: 8 }, e: { r: 6, c: 8 } },
                { s: { r: 4, c: 9 }, e: { r: 6, c: 9 } },
                { s: { r: 4, c: 10 }, e: { r: 6, c: 10 } },
                { s: { r: 4, c: 11 }, e: { r: 6, c: 11 } },
                { s: { r: 4, c: 12 }, e: { r: 6, c: 12 } },
                { s: { r: 4, c: 13 }, e: { r: 6, c: 13 } },
                { s: { r: 4, c: 14 }, e: { r: 6, c: 14 } },
                { s: { r: 4, c: 15 }, e: { r: 4, c: 16 } },
                { s: { r: 5, c: 15 }, e: { r: 6, c: 15 } },
                { s: { r: 5, c: 16 }, e: { r: 6, c: 16 } },
                { s: { r: 4, c: 17 }, e: { r: 6, c: 17 } },
                { s: { r: 4, c: 18 }, e: { r: 4, c: 19 } },
                { s: { r: 5, c: 18 }, e: { r: 6, c: 18 } },
                { s: { r: 5, c: 19 }, e: { r: 6, c: 19 } },
                { s: { r: 4, c: 20 }, e: { r: 4, c: 21 } },
                { s: { r: 5, c: 20 }, e: { r: 6, c: 20 } },
                { s: { r: 5, c: 21 }, e: { r: 6, c: 21 } },
                { s: { r: 3, c: 22 }, e: { r: 6, c: 22 } },
                { s: { r: 3, c: 23 }, e: { r: 4, c: 25 } },
                { s: { r: 5, c: 23 }, e: { r: 6, c: 23 } },
                { s: { r: 5, c: 24 }, e: { r: 6, c: 24 } },
                { s: { r: 5, c: 25 }, e: { r: 6, c: 25 } },
                { s: { r: 3, c: 26 }, e: { r: 4, c: 27 } },
                { s: { r: 5, c: 26 }, e: { r: 6, c: 26 } },
                { s: { r: 5, c: 27 }, e: { r: 6, c: 27 } },
                { s: { r: 3, c: 28 }, e: { r: 6, c: 28 } }
            ], 29, extracomTitleRows.length, extracomRows.length, true);
            wsExtracom['!merges'].push(...getKibSignatureMerges(extracomSignStartRow, 29, 21, 1, 8, 28));

            applyMutasiMasterSheetStyling(wsExtracom, extracomRows.length, 29, 15, extracomTitleRows.length, true);
            applySignatureBlockStyling(wsExtracom, extracomSignStartRow, 29);
            XLSX.utils.book_append_sheet(wb, wsExtracom, filterCat === 'all' ? "7. Extracom" : "Extracom - Barang Ekstrakomtabel");
        }

        // Simpan File Excel
        const fileName = `BUKU_ASET_MUTASI_EKSTERNAL_RSDK_${arahSlug}_${catSlug}_${yearLabel}_${twSlug}.xlsx`;
        XLSX.writeFile(wb, fileName);
        setTimeout(() => { isExportingMutasi = false; }, 1500);
    }
</script>
