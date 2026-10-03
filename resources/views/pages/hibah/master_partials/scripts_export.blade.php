<!-- ========================================================================= -->
<!-- SCRIPTS: ENGINE EKSPOR EXCEL MULTI-SHEET ASET HIBAH (STANDAR PEMKAB/BPKAD) -->
<!-- ========================================================================= -->
<script src="{{ asset('js/xlsx.bundle.js') }}"></script>
<script>
    if (typeof XLSX === 'undefined') {
        document.write('<script src="https://cdn.jsdelivr.net/npm/xlsx-js-style@1.2.0/dist/xlsx.bundle.js"><\/script>');
    }
</script>

<script>
    window.__simatHibahAstaps = @json($hibahAstaps ?? []);

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

    function getSafeSpec(item) {
        if (!item || !item.spesifikasi_json) return {};
        if (typeof item.spesifikasi_json === 'object') return item.spesifikasi_json;
        try {
            return JSON.parse(item.spesifikasi_json) || {};
        } catch (e) {
            return {};
        }
    }

    function resolveHibahCategory(item) {
        if (!item) return 'KIB B';
        const kode = item.kode_108 || item.astap?.kode_108 || item.jenis_aset_kode || '';
        if (kode.startsWith('1.3.1')) return 'KIB A';
        if (kode.startsWith('1.3.2')) return 'KIB B';
        if (kode.startsWith('1.3.3')) return 'KIB C';
        if (kode.startsWith('1.3.4')) return 'KIB D';
        if (kode.startsWith('1.3.5')) return 'KIB E';
        if (kode.startsWith('1.3.6')) return 'KIB F';
        if (kode.startsWith('1.5.3') || kode.startsWith('1.5')) return 'ATB';

        const cat = item.category || item.astap?.category || '';
        if (cat) {
            const cUpper = cat.toUpperCase().trim();
            if (cUpper.includes('KIB A') || cUpper.includes('TANAH')) return 'KIB A';
            if (cUpper.includes('KIB C') || cUpper.includes('GEDUNG') || cUpper.includes('BANGUNAN')) return 'KIB C';
            if (cUpper.includes('KIB D') || cUpper.includes('JALAN') || cUpper.includes('JARINGAN')) return 'KIB D';
            if (cUpper.includes('KIB E') || cUpper.includes('LAINNYA')) return 'KIB E';
            if (cUpper.includes('KIB F') || cUpper.includes('KONSTRUKSI') || cUpper.includes('KDP')) return 'KIB F';
            if (cUpper.includes('ATB') || cUpper.includes('BERWUJUD') || cUpper.includes('SOFTWARE')) return 'ATB';
            if (cUpper.includes('KIB B') || cUpper.includes('MESIN') || cUpper.includes('PERALATAN')) return 'KIB B';
        }

        const nama = ((item.astap ? item.astap.nama_barang : item.nama_barang) || '').toUpperCase();
        if (nama.includes('TANAH') || nama.includes('LAHAN')) return 'KIB A';
        if (nama.includes('GEDUNG') || nama.includes('BANGUNAN') || nama.includes('RUANG') || nama.includes('PAVILIUN')) return 'KIB C';
        if (nama.includes('JALAN') || nama.includes('IRIGASI') || nama.includes('JARINGAN') || nama.includes('PIPA') || nama.includes('SERVER')) return 'KIB D';
        if (nama.includes('BUKU') || nama.includes('KESENIAN') || nama.includes('HEWAN') || nama.includes('TANAMAN')) return 'KIB E';
        if (nama.includes('APLIKASI') || nama.includes('SOFTWARE') || nama.includes('LISENSI') || nama.includes('SISTEM INFORMASI')) return 'ATB';

        return 'KIB B';
    }

    // Helper penerapan style pada rentang cell ExcelJS
    function applyCellStyleRange(ws, startCol, startRow, endCol, endRow, styleOptions = {}) {
        const borderThin = {
            top: { style: 'thin', color: { rgb: 'CBD5E1' } },
            bottom: { style: 'thin', color: { rgb: 'CBD5E1' } },
            left: { style: 'thin', color: { rgb: 'CBD5E1' } },
            right: { style: 'thin', color: { rgb: 'CBD5E1' } }
        };

        const {
            font = { name: 'Arial', sz: 9, color: { rgb: '000000' } },
            fill = null,
            alignment = { vertical: 'center', horizontal: 'left', wrapText: true },
            border = borderThin,
            numFmt = null
        } = styleOptions;

        for (let r = startRow; r <= endRow; r++) {
            for (let c = startCol; c <= endCol; c++) {
                const colLetter = getColName(c);
                const cellRef = `${colLetter}${r + 1}`;
                if (!ws[cellRef]) {
                    ws[cellRef] = { t: 's', v: '' };
                }
                const cell = ws[cellRef];
                cell.s = {
                    font: { ...font },
                    alignment: { ...alignment },
                    border: border ? { ...border } : undefined
                };
                if (fill) cell.s.fill = { ...fill };
                if (numFmt) cell.z = numFmt;
            }
        }
    }

    let isExportingHibah = false;

    function exportHibahToExcel(params = {}) {
        if (isExportingHibah) return;
        isExportingHibah = true;

        if (typeof XLSX === 'undefined') {
            alert('⚠️ Pustaka Excel sedang dimuat, silakan coba sebentar lagi...');
            isExportingHibah = false;
            return;
        }

        const rawHibahs = window.masterHibahRecords || [];
        const wb = XLSX.utils.book_new();

        const filterYear    = params.year || 'all';
        const filterTw      = params.triwulan || 'all';
        const filterFormat  = params.format || 'all'; // 'all', 'masuk', 'keluar', 'kib'
        const filterCat     = params.category || 'all';
        const ppkNama       = params.ppkNama || 'dr. YUS PRIYATNA, Sp.P';
        const ppkNip        = params.ppkNip || '19760815 200501 1 009';
        const tanggalCetak  = params.tanggalCetak || new Date().toISOString().split('T')[0];

        const isTwMatch = (itemTw, targetTw) => {
            if (targetTw === 'all') return true;
            const targetKey = String(targetTw).replace(/[\s_]/g, '').toUpperCase();
            const curTw = String(itemTw || 'TWI').replace(/[\s_]/g, '').toUpperCase();
            return (curTw === targetKey) ||
                   (targetKey === 'TWI' && curTw === 'TW1') || (targetKey === 'TW1' && curTw === 'TWI') ||
                   (targetKey === 'TWII' && curTw === 'TW2') || (targetKey === 'TW2' && curTw === 'TWII') ||
                   (targetKey === 'TWIII' && curTw === 'TW3') || (targetKey === 'TW3' && curTw === 'TWIII') ||
                   (targetKey === 'TWIV' && curTw === 'TW4') || (targetKey === 'TW4' && curTw === 'TWIV');
        };

        // Filter list hibah
        let dataToExport = rawHibahs.filter(h => {
            const matchYear = filterYear === 'all' || String(h.tahun) === String(filterYear);
            const matchTw   = isTwMatch(h.triwulan, filterTw);
            
            let matchCat = true;
            if (filterCat !== 'all') {
                const itemCat = resolveHibahCategory(h);
                matchCat = (itemCat === filterCat);
            }
            return matchYear && matchTw && matchCat;
        });

        const masukList  = dataToExport.filter(h => h.tipe_hibah === 'masuk');
        const keluarList = dataToExport.filter(h => h.tipe_hibah === 'keluar');

        const yearLabel = filterYear === 'all' ? 'SEMUA TAHUN' : ('TAHUN ' + filterYear);
        let twLabel = 'KESELURUHAN (TAHUNAN)';
        if (filterTw === 'TW I' || filterTw === 'TW1') twLabel = 'TRIWULAN I (JANUARI - MARET)';
        else if (filterTw === 'TW II' || filterTw === 'TW2') twLabel = 'TRIWULAN II (APRIL - JUNI)';
        else if (filterTw === 'TW III' || filterTw === 'TW3') twLabel = 'TRIWULAN III (JULI - SEPTEMBER)';
        else if (filterTw === 'TW IV' || filterTw === 'TW4') twLabel = 'TRIWULAN IV (OKTOBER - DESEMBER)';

        // =========================================================================
        // SHEET 1: REKAPITULASI MUTASI HIBAH BARANG MILIK DAERAH (BMD)
        // =========================================================================
        if (filterFormat === 'all') {
            const rekapRows = [];
            rekapRows.push(["PEMERINTAH KABUPATEN BONDOWOSO"]);
            rekapRows.push(["RUMAH SAKIT UMUM DAERAH Dr. H. KOESNANDI BONDOWOSO"]);
            rekapRows.push(["REKAPITULASI REALISASI MUTASI HIBAH BARANG MILIK DAERAH (BMD)"]);
            rekapRows.push([`PERIODE: ${twLabel} — ${yearLabel}`]);
            rekapRows.push([]); // blank

            rekapRows.push([
                "NO",
                "URAIAN KELOMPOK MUTASI HIBAH",
                "JUMLAH BERKAS",
                "VOLUME FISIK",
                "TOTAL NILAI ASET (RP)",
                "KETERANGAN / SUMBER"
            ]);

            const totalMasukVol  = masukList.reduce((s, h) => s + (h.jumlah_volume || 1), 0);
            const totalMasukNom  = masukList.reduce((s, h) => s + (parseFloat(h.nilai_aset) || 0), 0);
            const totalKeluarVol = keluarList.reduce((s, h) => s + (h.jumlah_volume || 1), 0);
            const totalKeluarNom = keluarList.reduce((s, h) => s + (parseFloat(h.nilai_aset) || 0), 0);
            const nettoVol       = totalMasukVol - totalKeluarVol;
            const nettoNom       = totalMasukNom - totalKeluarNom;

            rekapRows.push([
                1,
                "Mutasi Bertambah: Penerimaan Hibah / Bantuan Pihak Ketiga (Hibah Masuk)",
                masukList.length + " Berkas",
                totalMasukVol + " Unit",
                totalMasukNom,
                "Perolehan aset dari Kemenkes RI / Dinas Kesehatan / Pihak Ketiga"
            ]);

            rekapRows.push([
                2,
                "Mutasi Berkurang: Barang Inventaris RSUD Dihibahkan ke Luar (Pengurangan AT)",
                keluarList.length + " Berkas",
                totalKeluarVol + " Unit",
                totalKeluarNom,
                "Penyerahan aset ke Puskesmas / Dinas / Lembaga luar"
            ]);

            const totalRowIdx = rekapRows.length;
            rekapRows.push([
                "SALDO MUTASI BERSIH HIBAH (NETTO)",
                "",
                (masukList.length + keluarList.length) + " Berkas",
                (nettoVol >= 0 ? "+" : "") + nettoVol + " Unit",
                nettoNom,
                nettoNom >= 0 ? "Surplus Penambahan Aset RSUD" : "Defisit Pengurangan Aset RSUD"
            ]);

            // Tanda Tangan PPK
            rekapRows.push([]);
            rekapRows.push([]);
            rekapRows.push(["", "", "", "", `Bondowoso, ${formatAstapDate(tanggalCetak)}`]);
            rekapRows.push(["", "", "", "", "Pejabat Pembuat Komitmen (PPK)"]);
            rekapRows.push([]);
            rekapRows.push([]);
            rekapRows.push([]);
            rekapRows.push(["", "", "", "", ppkNama]);
            rekapRows.push(["", "", "", "", "NIP. " + ppkNip]);

            const wsRekap = XLSX.utils.aoa_to_sheet(rekapRows);
            wsRekap['!cols'] = [
                { wch: 6 },
                { wch: 48 },
                { wch: 18 },
                { wch: 18 },
                { wch: 24 },
                { wch: 38 }
            ];
            wsRekap['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 5 } },
                { s: { r: 1, c: 0 }, e: { r: 1, c: 5 } },
                { s: { r: 2, c: 0 }, e: { r: 2, c: 5 } },
                { s: { r: 3, c: 0 }, e: { r: 3, c: 5 } },
                { s: { r: totalRowIdx, c: 0 }, e: { r: totalRowIdx, c: 1 } },
            ];

            // Styling Kop
            applyCellStyleRange(wsRekap, 0, 0, 5, 3, {
                font: { name: 'Arial', sz: 11, bold: true, color: { rgb: '0F172A' } },
                alignment: { horizontal: 'center', vertical: 'center' },
                border: null
            });

            // Styling Table Header
            applyCellStyleRange(wsRekap, 0, 5, 5, 5, {
                font: { name: 'Arial', sz: 10, bold: true, color: { rgb: 'FFFFFF' } },
                fill: { fgColor: { rgb: 'D97706' } }, // Amber-600
                alignment: { horizontal: 'center', vertical: 'center', wrapText: true }
            });

            // Styling Data Rows
            applyCellStyleRange(wsRekap, 0, 6, 5, 7, {
                font: { name: 'Arial', sz: 9, color: { rgb: '000000' } },
                alignment: { vertical: 'center' }
            });

            // Format Nilai Rupiah
            applyCellStyleRange(wsRekap, 4, 6, 4, 7, {
                numFmt: '#,##0',
                alignment: { horizontal: 'right', vertical: 'center' }
            });

            // Styling Total Row
            applyCellStyleRange(wsRekap, 0, totalRowIdx, 5, totalRowIdx, {
                font: { name: 'Arial', sz: 10, bold: true, color: { rgb: '0F172A' } },
                fill: { fgColor: { rgb: 'FEF3C7' } }, // Amber-100
                alignment: { vertical: 'center' }
            });
            applyCellStyleRange(wsRekap, 4, totalRowIdx, 4, totalRowIdx, {
                font: { name: 'Arial', sz: 10, bold: true, color: { rgb: 'B45309' } },
                fill: { fgColor: { rgb: 'FEF3C7' } },
                numFmt: '#,##0',
                alignment: { horizontal: 'right', vertical: 'center' }
            });

            XLSX.utils.book_append_sheet(wb, wsRekap, "1. Rekapitulasi Hibah");
        }

        // =========================================================================
        // SHEET 2: DAFTAR REALISASI HIBAH MASUK (BERTAMBAH)
        // =========================================================================
        if (filterFormat === 'all' || filterFormat === 'masuk') {
            const masukRows = [];
            masukRows.push(["PEMERINTAH KABUPATEN BONDOWOSO"]);
            masukRows.push(["RUMAH SAKIT UMUM DAERAH Dr. H. KOESNANDI BONDOWOSO"]);
            masukRows.push(["DAFTAR REALISASI PENERIMAAN HIBAH / BANTUAN (MUTASI BERTAMBAH BMD)"]);
            masukRows.push([`PERIODE: ${twLabel} — ${yearLabel}`]);
            masukRows.push([]); // blank

            masukRows.push([
                "NO",
                "KODE BARANG (108)",
                "NAMA BARANG / JENIS ASET",
                "SPESIFIKASI / MERK / MODEL",
                "TAHUN",
                "VOL",
                "SATUAN",
                "HARGA SATUAN (RP)",
                "TOTAL NILAI (RP)",
                "INSTANSI PEMBERI HIBAH",
                "NOMOR BAST",
                "TANGGAL BAST",
                "RUANGAN / PENEMPATAN",
                "KONDISI",
                "KETERANGAN"
            ]);

            let sumMasuk = 0;
            masukList.forEach((item, idx) => {
                const val = parseFloat(item.nilai_aset) || 0;
                sumMasuk += val;
                const vol = Math.max(1, item.jumlah_volume || 1);
                const hargaSat = val / vol;
                const spec = getSafeSpec(item.astap);
                const spekStr = spec.merk ? `${spec.merk} ${spec.type || ''}`.trim() : (spec.spesifikasi || '-');
                const ruangStr = item.astap?.unit?.nama || (item.astap?.registers && item.astap.registers[0]?.ruang_pemegang) || 'Gudang Aset';
                const kondisiStr = (item.astap?.registers && item.astap.registers[0]?.kondisi) || 'Baik';

                masukRows.push([
                    idx + 1,
                    item.astap?.kode_108 || item.kode_108 || '-',
                    item.astap ? item.astap.nama_barang : (item.nama_barang || '-'),
                    spekStr,
                    item.tahun || '-',
                    vol,
                    item.satuan || 'Unit',
                    hargaSat,
                    val,
                    item.pihak_hibah || '-',
                    item.nomor_bast || '-',
                    formatAstapDate(item.tanggal_bast),
                    ruangStr,
                    kondisiStr,
                    item.keterangan || '-'
                ]);
            });

            const totalMasukIdx = masukRows.length;
            masukRows.push([
                "JUMLAH TOTAL REALISASI HIBAH MASUK",
                "", "", "", "", "", "", "",
                sumMasuk,
                `Total ${masukList.length} Item Hibah Masuk`,
                "", "", "", "", ""
            ]);

            // Tanda Tangan PPK
            masukRows.push([]);
            masukRows.push([]);
            masukRows.push(["", "", "", "", "", "", "", "", `Bondowoso, ${formatAstapDate(tanggalCetak)}`]);
            masukRows.push(["", "", "", "", "", "", "", "", "Pejabat Pembuat Komitmen (PPK)"]);
            masukRows.push([]);
            masukRows.push([]);
            masukRows.push([]);
            masukRows.push(["", "", "", "", "", "", "", "", ppkNama]);
            masukRows.push(["", "", "", "", "", "", "", "", "NIP. " + ppkNip]);

            const wsMasuk = XLSX.utils.aoa_to_sheet(masukRows);
            wsMasuk['!cols'] = [
                { wch: 5 },  // No
                { wch: 18 }, // Kode 108
                { wch: 35 }, // Nama Barang
                { wch: 25 }, // Spek/Merk
                { wch: 8 },  // Tahun
                { wch: 7 },  // Vol
                { wch: 9 },  // Satuan
                { wch: 18 }, // Harga Satuan
                { wch: 20 }, // Total Nilai
                { wch: 30 }, // Pemberi
                { wch: 25 }, // No BAST
                { wch: 15 }, // Tgl BAST
                { wch: 22 }, // Ruangan
                { wch: 12 }, // Kondisi
                { wch: 28 }  // Ket
            ];
            wsMasuk['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 14 } },
                { s: { r: 1, c: 0 }, e: { r: 1, c: 14 } },
                { s: { r: 2, c: 0 }, e: { r: 2, c: 14 } },
                { s: { r: 3, c: 0 }, e: { r: 3, c: 14 } },
                { s: { r: totalMasukIdx, c: 0 }, e: { r: totalMasukIdx, c: 7 } },
            ];

            // Styling Header Table
            applyCellStyleRange(wsMasuk, 0, 5, 14, 5, {
                font: { name: 'Arial', sz: 9, bold: true, color: { rgb: 'FFFFFF' } },
                fill: { fgColor: { rgb: '0F766E' } }, // Teal-700
                alignment: { horizontal: 'center', vertical: 'center', wrapText: true }
            });

            // Styling Data Rows
            if (masukList.length > 0) {
                applyCellStyleRange(wsMasuk, 0, 6, 14, 5 + masukList.length, {
                    font: { name: 'Arial', sz: 8.5, color: { rgb: '000000' } },
                    alignment: { vertical: 'center' }
                });
                // Numbers
                applyCellStyleRange(wsMasuk, 7, 6, 8, 5 + masukList.length, {
                    numFmt: '#,##0',
                    alignment: { horizontal: 'right', vertical: 'center' }
                });
            }

            // Total row styling
            applyCellStyleRange(wsMasuk, 0, totalMasukIdx, 14, totalMasukIdx, {
                font: { name: 'Arial', sz: 9.5, bold: true, color: { rgb: '0F172A' } },
                fill: { fgColor: { rgb: 'CCFBF1' } }, // Teal-100
                alignment: { vertical: 'center' }
            });
            applyCellStyleRange(wsMasuk, 8, totalMasukIdx, 8, totalMasukIdx, {
                font: { name: 'Arial', sz: 9.5, bold: true, color: { rgb: '0F766E' } },
                fill: { fgColor: { rgb: 'CCFBF1' } },
                numFmt: '#,##0',
                alignment: { horizontal: 'right', vertical: 'center' }
            });

            XLSX.utils.book_append_sheet(wb, wsMasuk, filterFormat === 'masuk' ? "Hibah Masuk RSDK" : "2. Hibah (Masuk)");
        }

        // =========================================================================
        // SHEET 3: DAFTAR PENGURANGAN HIBAH KELUAR
        // =========================================================================
        if (filterFormat === 'all' || filterFormat === 'keluar') {
            const keluarRows = [];
            keluarRows.push(["PEMERINTAH KABUPATEN BONDOWOSO"]);
            keluarRows.push(["RUMAH SAKIT UMUM DAERAH Dr. H. KOESNANDI BONDOWOSO"]);
            keluarRows.push(["DAFTAR PENGURANGAN ASET TETAP (DIHIBAHKAN KE LUAR RSUD)"]);
            keluarRows.push([`PERIODE: ${twLabel} — ${yearLabel}`]);
            keluarRows.push([]); // blank

            keluarRows.push([
                "NO",
                "KODE BARANG (108)",
                "NAMA BARANG / SPESIFIKASI",
                "NIBAR / NO REGISTER",
                "TAHUN PEROLEHAN",
                "VOL",
                "SATUAN",
                "NILAI ASET (RP)",
                "PENERIMA HIBAH",
                "NOMOR BAST PENYERAHAN",
                "TANGGAL BAST",
                "ALASAN / KETERANGAN"
            ]);

            let sumKeluar = 0;
            keluarList.forEach((item, idx) => {
                const val = parseFloat(item.nilai_aset) || 0;
                sumKeluar += val;
                const regStr = item.register?.nibar || item.register?.no_register || (item.astap?.registers && item.astap.registers[0]?.nibar) || '-';

                keluarRows.push([
                    idx + 1,
                    item.astap?.kode_108 || item.kode_108 || '-',
                    item.astap ? item.astap.nama_barang : (item.nama_barang || '-'),
                    regStr,
                    item.astap?.tahun_perolehan || item.tahun || '-',
                    item.jumlah_volume || 1,
                    item.satuan || 'Unit',
                    val,
                    item.pihak_hibah || '-',
                    item.nomor_bast || '-',
                    formatAstapDate(item.tanggal_bast),
                    item.keterangan || 'Dihibahkan ke luar RSUD'
                ]);
            });

            const totalKeluarIdx = keluarRows.length;
            keluarRows.push([
                "JUMLAH TOTAL PENGURANGAN ASET DIHIBAHKAN",
                "", "", "", "", "", "",
                sumKeluar,
                `Total ${keluarList.length} Item Hibah Keluar`,
                "", "", ""
            ]);

            // Tanda Tangan PPK
            keluarRows.push([]);
            keluarRows.push([]);
            keluarRows.push(["", "", "", "", "", "", `Bondowoso, ${formatAstapDate(tanggalCetak)}`]);
            keluarRows.push(["", "", "", "", "", "", "Pejabat Pembuat Komitmen (PPK)"]);
            keluarRows.push([]);
            keluarRows.push([]);
            keluarRows.push([]);
            keluarRows.push(["", "", "", "", "", "", ppkNama]);
            keluarRows.push(["", "", "", "", "", "", "NIP. " + ppkNip]);

            const wsKeluar = XLSX.utils.aoa_to_sheet(keluarRows);
            wsKeluar['!cols'] = [
                { wch: 5 },  // No
                { wch: 18 }, // Kode 108
                { wch: 35 }, // Nama Barang
                { wch: 22 }, // NIBAR
                { wch: 12 }, // Tahun
                { wch: 7 },  // Vol
                { wch: 9 },  // Satuan
                { wch: 20 }, // Nilai
                { wch: 30 }, // Penerima
                { wch: 25 }, // No BAST
                { wch: 15 }, // Tgl BAST
                { wch: 30 }  // Ket
            ];
            wsKeluar['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 11 } },
                { s: { r: 1, c: 0 }, e: { r: 1, c: 11 } },
                { s: { r: 2, c: 0 }, e: { r: 2, c: 11 } },
                { s: { r: 3, c: 0 }, e: { r: 3, c: 11 } },
                { s: { r: totalKeluarIdx, c: 0 }, e: { r: totalKeluarIdx, c: 6 } },
            ];

            // Styling Table Header (Rose-700)
            applyCellStyleRange(wsKeluar, 0, 5, 11, 5, {
                font: { name: 'Arial', sz: 9, bold: true, color: { rgb: 'FFFFFF' } },
                fill: { fgColor: { rgb: 'BE123C' } },
                alignment: { horizontal: 'center', vertical: 'center', wrapText: true }
            });

            // Data rows
            if (keluarList.length > 0) {
                applyCellStyleRange(wsKeluar, 0, 6, 11, 5 + keluarList.length, {
                    font: { name: 'Arial', sz: 8.5, color: { rgb: '000000' } },
                    alignment: { vertical: 'center' }
                });
                applyCellStyleRange(wsKeluar, 7, 6, 7, 5 + keluarList.length, {
                    numFmt: '#,##0',
                    alignment: { horizontal: 'right', vertical: 'center' }
                });
            }

            // Total row styling
            applyCellStyleRange(wsKeluar, 0, totalKeluarIdx, 11, totalKeluarIdx, {
                font: { name: 'Arial', sz: 9.5, bold: true, color: { rgb: '0F172A' } },
                fill: { fgColor: { rgb: 'FFE4E6' } }, // Rose-100
                alignment: { vertical: 'center' }
            });
            applyCellStyleRange(wsKeluar, 7, totalKeluarIdx, 7, totalKeluarIdx, {
                font: { name: 'Arial', sz: 9.5, bold: true, color: { rgb: 'BE123C' } },
                fill: { fgColor: { rgb: 'FFE4E6' } },
                numFmt: '#,##0',
                alignment: { horizontal: 'right', vertical: 'center' }
            });

            XLSX.utils.book_append_sheet(wb, wsKeluar, filterFormat === 'keluar' ? "Pengurangan Hibah Keluar" : "3. Pengurangan AT (Keluar)");
        }

        // =========================================================================
        // SAVE FILE
        // =========================================================================
        const cleanYear = filterYear === 'all' ? 'SEMUA_TAHUN' : filterYear;
        const cleanTw = filterTw.replace(/\s+/g, '_');
        const filename = `LAPORAN_HIBAH_BMD_RSDK_${cleanYear}_${cleanTw}.xlsx`;

        XLSX.writeFile(wb, filename);

        setTimeout(() => {
            isExportingHibah = false;
        }, 800);
    }
</script>
