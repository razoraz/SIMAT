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
(function() {
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

    // Helper ekstrak detail transaksi mutasi secara mendalam (Asal vs Tujuan, Pejabat, BAST)
    function getMutasiItemDetails(item) {
        const spec = getSafeSpec(item);
        const mutInfo = (spec.mutasi_info && typeof spec.mutasi_info === 'object') ? spec.mutasi_info : {};

        const isKeluar = (item.tipe === 'keluar') || 
                         (String(item.jenis || '').toLowerCase().includes('keluar')) || 
                         (String(item.kategori_label || '').toLowerCase().includes('keluar'));

        // 1. OPD Mitra (Luar)
        let opdMitra = '-';
        if (isKeluar) {
            opdMitra = item.opd_tujuan || mutInfo.skpd_tujuan || 'SKPD Penerima';
        } else {
            opdMitra = spec.skpd_asal || item.opd_asal || item.mutasi_asal || 'SKPD Pengirim';
        }
        if (opdMitra === 'SKPD / Instansi Luar' && spec.skpd_asal) {
            opdMitra = spec.skpd_asal;
        }

        // 2. Pejabat Mitra (Luar)
        let pjMitra = '-';
        if (isKeluar) {
            pjMitra = item.pejabat_opd_tujuan || item.pj_tujuan_nama || mutInfo.pj_tujuan_nama || ('Pejabat Penerima ' + opdMitra);
        } else {
            pjMitra = spec.pj_asal_nama || item.pj_asal_nama || ('Pejabat Penyerah ' + opdMitra);
            if (pjMitra.toLowerCase().includes('budi hartono')) {
                pjMitra = spec.pj_asal_nama || ('Pejabat Penyerah ' + opdMitra);
            }
        }

        // 3. Alamat Mitra
        const alamatMitra = spec.alamat_instansi || item.alamat_instansi || mutInfo.alamat_instansi || '-';

        // 4. Dokumen BAST / BAMB
        let docNo = '-';
        let docTglRaw = '';
        if (isKeluar) {
            docNo = mutInfo.nomor_bast || item.nomor_sk_dasar || item.mutasi_nomor_bamb || item.bast_dokumen_nomor || item.kode || '-';
            docTglRaw = mutInfo.tanggal_bast || item.mutasi_tanggal || item.bast_dokumen_tanggal || item.tgl_raw || '';
        } else {
            docNo = spec.nomor_bamb || item.nomor_bamb || item.mutasi_nomor_bamb || item.bast_dokumen_nomor || item.bast_nomor || item.kode || '-';
            docTglRaw = spec.tanggal_bamb || item.mutasi_tanggal || item.bast_dokumen_tanggal || item.tgl_raw || '';
        }
        const docTgl = formatAstapDate(docTglRaw);

        // 5. Pejabat RSUD dr. H. Koesnadi
        let pejabatRsudNama = 'BUDI HARTONO, S.Sos';
        let pejabatRsudNip = '19760229 200801 1 010';
        if (isKeluar) {
            pejabatRsudNama = item.pj_asal_nama || 'BUDI HARTONO, S.Sos';
            pejabatRsudNip = item.pj_asal_nip || '19760229 200801 1 010';
        } else {
            pejabatRsudNama = item.pejabat_opd_tujuan || item.pj_tujuan_nama || item.ppk_nama || spec.ppk_nama || 'BUDI HARTONO, S.Sos';
            pejabatRsudNip = item.nip_pejabat_opd_tujuan || item.pj_tujuan_nip || item.ppk_nip || spec.ppk_nip || '19760229 200801 1 010';
        }

        // 6. Ruangan Penempatan / Asal RSUD
        let ruangPenempatan = 'RSUD dr. H. Koesnadi';
        if (isKeluar) {
            ruangPenempatan = item.ruangan_asal || spec.ruangan_asal || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : 'RSUD dr. H. Koesnadi');
        } else {
            ruangPenempatan = item.ruangan_tujuan || spec.ruang_unit || item.ruang_unit || (item.registers && item.registers.length > 0 ? item.registers[0].ruang_pemegang : ((item.unit && item.unit.nama) ? item.unit.nama : 'RSUD dr. H. Koesnadi'));
        }

        return {
            isKeluar,
            spec,
            mutInfo,
            opdMitra,
            pjMitra,
            alamatMitra,
            dokumenNomor: docNo,
            dokumenTanggal: docTgl,
            pejabatRsudNama,
            pejabatRsudNip,
            ruangPenempatan
        };
    }

    // Helper resolusi PMDN 108: Jenis, Sub Rincian, dan Nama Barang
    function resolve108Info(item, defaultPrefix, defaultJenisNama) {
        const spec = getSafeSpec(item);
        const kd108 = String(item.kode_108 || item.kode_barang || item.jenis_astap?.sub_sub_rincian_objek || (defaultPrefix + '.01.01.01.001')).trim();
        const parts = kd108.split('.');

        const jenisKode = item.jenis_aset_kode || (parts.length >= 3 ? parts.slice(0, 3).join('.') : defaultPrefix);
        
        let jenisNama = defaultJenisNama;
        if (jenisKode === '1.3.1') jenisNama = 'TANAH';
        else if (jenisKode === '1.3.2') jenisNama = 'PERALATAN DAN MESIN';
        else if (jenisKode === '1.3.3') jenisNama = 'GEDUNG DAN BANGUNAN';
        else if (jenisKode === '1.3.4') jenisNama = 'JALAN, IRIGASI DAN JARINGAN';
        else if (jenisKode === '1.3.5') jenisNama = 'ASET TETAP LAINNYA';
        else if (jenisKode === '1.5.4') jenisNama = 'ASET LAIN-LAIN';
        else if (item.jenis_aset_nama && !['TRANSFER KE OPD', 'MUTASI EKSTERNAL', 'TRANSFER ANTAR-OPD'].includes(item.jenis_aset_nama.toUpperCase())) {
            jenisNama = item.jenis_aset_nama;
        }

        let subRincianKode = item.sub_rincian_kode || (item.jenis_astap && item.jenis_astap.sub_rincian_objek);
        if (!subRincianKode) {
            subRincianKode = (parts.length >= 6) ? parts.slice(0, 6).join('.') : (jenisKode + '.01.01.01');
        }

        let subRincianNama = item.sub_rincian_nama || (item.jenis_astap && item.jenis_astap.uraian_sub_rincian);
        if (!subRincianNama || subRincianNama === '-' || subRincianNama === item.nama_barang) {
            if (window.dbMasterJenisAstap108 && Array.isArray(window.dbMasterJenisAstap108)) {
                const found = window.dbMasterJenisAstap108.find(m => m.sub_rincian_objek === subRincianKode || m.sub_sub_rincian_objek === kd108);
                if (found && found.uraian_sub_rincian) {
                    subRincianNama = found.uraian_sub_rincian;
                }
            }
        }
        if (!subRincianNama || subRincianNama === '-') {
            subRincianNama = jenisNama;
        }

        const namaBarang = item.nama_murni || item.nama_barang || (item.jenis_astap && item.jenis_astap.uraian_sub_sub_rincian) || defaultJenisNama;

        return {
            jenisKode,
            jenisNama,
            subRincianKode,
            subRincianNama,
            kodeBarang: kd108,
            namaBarang
        };
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
                if (!ws[cellRef]) ws[cellRef] = { v: '', t: 's' };
                const cell = ws[cellRef];
                const hasText = cell.v && String(cell.v).trim() !== '';

                cell.s = {
                    fill: { fgColor: { rgb: 'FFFFFF' } },
                    font: {
                        name: 'Calibri',
                        sz: (offset === 6) ? 10.5 : (offset === 7 || offset === 8 ? 9.5 : 10),
                        bold: (offset === 1 || offset === 2 || offset === 3 || offset === 6),
                        underline: (offset === 6 && hasText),
                        color: { rgb: '000000' }
                    },
                    alignment: {
                        vertical: 'center',
                        horizontal: 'center',
                        wrapText: false
                    },
                    border: null
                };
            }
        }
    }

    // Helper styling Lembar Rekapitulasi (Sheet 1 - Standar Buku Induk Aset Resmi BPKAD)
    function applyRekapSheetStyling(ws, rowCount, colCount, titleRowCount = 5, totalRowIdx = 12, signStartRow = 14) {
        const thinBorder = {
            top: { style: 'thin', color: { rgb: '64748B' } },
            bottom: { style: 'thin', color: { rgb: '64748B' } },
            left: { style: 'thin', color: { rgb: '64748B' } },
            right: { style: 'thin', color: { rgb: '64748B' } }
        };

        const doubleBottomBorder = {
            top: { style: 'thin', color: { rgb: '0F172A' } },
            bottom: { style: 'double', color: { rgb: '0F172A' } },
            left: { style: 'thin', color: { rgb: '64748B' } },
            right: { style: 'thin', color: { rgb: '64748B' } }
        };

        for (let r = 0; r < rowCount; r++) {
            for (let c = 0; c < colCount; c++) {
                const cellRef = getColName(c) + (r + 1);
                if (!ws[cellRef]) {
                    ws[cellRef] = { v: '', t: 's' };
                }

                const cell = ws[cellRef];
                let fill = 'FFFFFF';
                let fontColor = '0F172A';
                let bold = false;
                let align = 'center';
                let border = thinBorder;
                let fontSize = 9.5;
                let numFmt = null;

                // 1. BARIS JUDUL LAPORAN (r < titleRowCount)
                if (r < titleRowCount) {
                    fill = 'FFFFFF';
                    fontColor = '0F172A';
                    bold = true;
                    fontSize = (r === 0 || r === 1) ? 12 : 11;
                    align = 'center';
                    border = null;
                }
                // 2. HEADER TABEL REKAPITULASI (r === titleRowCount)
                else if (r === titleRowCount) {
                    fill = '1E3A8A'; // Deep Navy Header (Sama persis seperti Template Resmi)
                    fontColor = 'FFFFFF'; // White text
                    bold = true;
                    fontSize = 10;
                    align = 'center';
                    border = thinBorder;
                }
                // 3. BARIS TOTAL / FOOTER TABEL (r === totalRowIdx)
                else if (r === totalRowIdx) {
                    fill = '93C5FD'; // Soft Blue matching master sheets
                    fontColor = '0F172A';
                    bold = true;
                    fontSize = 10;
                    border = doubleBottomBorder;

                    if (c === 0) {
                        align = 'center';
                    } else if (c === 4) {
                        align = 'center';
                    } else if (c === 5) {
                        align = 'right';
                        numFmt = 'Rp #,##0.00';
                    } else {
                        align = 'left';
                    }
                }
                // 4. BAGIAN TANDA TANGAN / PENGESAHAN (r >= signStartRow)
                else if (r >= signStartRow) {
                    continue; // Ditangani khusus oleh applySignatureBlockStyling
                }
                // 5. BARIS DATA BIASA (r > titleRowCount && r < totalRowIdx)
                else {
                    fill = (r % 2 === 0) ? 'FFFFFF' : 'F8FAFC';

                    if (c === 0) {
                        align = 'center'; // No
                    } else if (c === 1) {
                        align = 'left'; // Kelompok Aset
                        bold = true;
                    } else if (c === 2) {
                        align = 'center'; // Kode Akun
                    } else if (c === 3) {
                        align = 'left'; // Deskripsi
                    } else if (c === 4) {
                        align = 'center'; // Jumlah Item / Unit
                    } else if (c === 5) {
                        align = 'right'; // Nilai Rupiah
                        numFmt = 'Rp #,##0.00';
                        bold = true;
                        fill = 'E0F2FE'; // Soft Sky Blue accent untuk kolom nilai
                    } else if (c === 6) {
                        align = 'left'; // Keterangan
                    } else {
                        align = 'center';
                    }
                }

                cell.s = {
                    fill: fill ? { fgColor: { rgb: fill } } : undefined,
                    font: {
                        name: 'Arial',
                        sz: fontSize,
                        bold: bold,
                        color: { rgb: fontColor }
                    },
                    alignment: {
                        horizontal: align,
                        vertical: 'center',
                        wrapText: true
                    },
                    border: border
                };

                if (numFmt && typeof cell.v === 'number') {
                    cell.z = numFmt;
                }
            }
        }
    }

    // Helper styling lembar master KIB A s/d E & Extracom
    function applyMutasiMasterSheetStyling(ws, rowCount, colCount, kibL3ColCount, titleRowCount = 5, hasSignature = false) {
        const thinBorder = {
            top: { style: 'thin', color: { rgb: '64748B' } },
            bottom: { style: 'thin', color: { rgb: '64748B' } },
            left: { style: 'thin', color: { rgb: '64748B' } },
            right: { style: 'thin', color: { rgb: '64748B' } }
        };
        const doubleBottomBorder = {
            top: { style: 'thin', color: { rgb: '0F172A' } },
            bottom: { style: 'double', color: { rgb: '0F172A' } },
            left: { style: 'thin', color: { rgb: '64748B' } },
            right: { style: 'thin', color: { rgb: '64748B' } }
        };

        const headerStartRow = titleRowCount;
        const headerEndRow = titleRowCount + 4;
        const footerRowIdx = hasSignature ? (rowCount - 1 - 9) : (rowCount - 1);

        for (let r = 0; r < rowCount; r++) {
            if (hasSignature && r > footerRowIdx) continue;

            for (let c = 0; c < colCount; c++) {
                const cellRef = getColName(c) + (r + 1);
                if (!ws[cellRef]) ws[cellRef] = { v: '', t: 's' };

                const cell = ws[cellRef];
                let fill = 'FFFFFF';
                let fontColor = '0F172A';
                let bold = false;
                let align = 'left';
                let border = thinBorder;
                let fontSize = 9;
                let numFmt = null;

                if (r < titleRowCount) {
                    fill = 'FFFFFF';
                    fontColor = '0F172A';
                    bold = true;
                    fontSize = (r === 0 || r === 1) ? 12 : 11;
                    align = 'center';
                    border = null;
                } else if (r >= headerStartRow && r <= headerEndRow) {
                    fill = '312E81'; // Deep Indigo
                    fontColor = 'FFFFFF';
                    bold = true;
                    align = 'center';
                    fontSize = (r === headerEndRow) ? 8 : 9;
                    if (r === headerEndRow) fill = '1E1B4B';
                } else if (r === footerRowIdx) {
                    fill = 'E0E7FF';
                    fontColor = '0F172A';
                    bold = true;
                    border = doubleBottomBorder;
                    fontSize = 10;
                    if (c === 0) align = 'left';
                    else if (c === 5 || c === 6 || c === 18 || c === 24 || c === 25 || c === 23) {
                        align = 'right';
                        numFmt = 'Rp #,##0.00';
                    } else if (c === 16 || c === 17 || c === 21 || c === 22) {
                        align = 'center';
                    }
                } else {
                    fill = (r % 2 === 0) ? 'FFFFFF' : 'F5F3FF';
                    if (c === 0) { align = 'center'; bold = true; }
                    else if (c === 1 || c === 3 || c === 8) align = 'center';
                    else if (c === 5 || c === 6 || c === 18 || c === 24 || c === 25 || c === 23) {
                        align = 'right';
                        numFmt = 'Rp #,##0.00';
                    } else if (c === 16 || c === 17 || c === 21 || c === 22) {
                        align = 'center';
                    }
                }

                cell.s = {
                    font: { name: 'Calibri', sz: fontSize, bold: bold, color: { rgb: fontColor } },
                    alignment: { horizontal: align, vertical: 'center', wrapText: true },
                    fill: { fgColor: { rgb: fill } },
                    border: border
                };
                if (numFmt && typeof cell.v === 'number') cell.z = numFmt;
            }
        }
    }

    // Helper baris judul KIB Mutasi Eksternal
    function getMutasiKibTitleRows(kibName, yearLabel, filterTw, arahLabel) {
        let twLabel = 'TAHUNAN';
        if (filterTw === 'TW I' || filterTw === 'TW1') twLabel = 'TRIWULAN I';
        else if (filterTw === 'TW II' || filterTw === 'TW2') twLabel = 'TRIWULAN II';
        else if (filterTw === 'TW III' || filterTw === 'TW3') twLabel = 'TRIWULAN III';
        else if (filterTw === 'TW IV' || filterTw === 'TW4') twLabel = 'TRIWULAN IV';

        return [
            ['PEMERINTAH KABUPATEN BONDOWOSO'],
            ['RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI'],
            [`DAFTAR ASET MUTASI EKSTERNAL (ANTAR-OPD) - ${kibName} TAHUN ${yearLabel}`],
            [`ARAH MUTASI: ${arahLabel} · TRANSFER BARANG MILIK DAERAH (BMD) RESMI`],
            [`${twLabel} TAHUN ANGGARAN ${yearLabel}`]
        ];
    }

    // Helper baris tanda tangan resmi
    function buildMutasiSignRows(totalCols, rightCol, leftCol = 1) {
        const signDate = getReportSignDate('all', new Date().getFullYear());
        const makeRow = (lVal, rVal) => {
            const arr = Array(totalCols).fill('');
            arr[leftCol] = lVal || '';
            arr[rightCol] = rVal || '';
            return arr;
        };

        return [
            Array(totalCols).fill(''),
            makeRow('MENGETAHUI,', `Bondowoso, ${signDate}`),
            makeRow('DIREKTUR RSUD dr. H. KOESNANDI', 'PENGURUS BARANG PENGGUNA'),
            makeRow('KABUPATEN BONDOWOSO', 'RSUD dr. H. KOESNANDI BONDOWOSO'),
            Array(totalCols).fill(''),
            Array(totalCols).fill(''),
            makeRow('dr. YUS PRIYATNA ADISIWIBOWO, Sp.P', 'BUDI HARTONO, S.Sos'),
            makeRow('NIP. 19760814 200212 1 005', 'NIP. 19760229 200801 1 010'),
            Array(totalCols).fill('')
        ];
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

        const rawMutasis = (window.simatMutasiEksternals && window.simatMutasiEksternals.length > 0)
            ? window.simatMutasiEksternals
            : (window.__simatAstaps || []).filter(item => {
                const s = String(item.sumber_dana || item.sumber_dana_raw || '').toLowerCase();
                return s === 'mutasi_eksternal' || s === 'pelimpahan' || s === 'pelimpahan_skpd' || s === 'mutasi_masuk' || !!item.mutasi_asal;
            });
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

            const det = getMutasiItemDetails(item);

            // 1. Filter Arah
            if (filterDirection === 'masuk' && det.isKeluar) return false;
            if (filterDirection === 'keluar' && !det.isKeluar) return false;

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
        let arahLabel = 'SEMUA ARAH MUTASI (MASUK & TRANSFER KELUAR)';
        let arahSlug = 'SEMUA_ARAH';
        if (filterDirection === 'masuk') {
            arahLabel = 'MUTASI EKSTERNAL MASUK (DARI OPD LUAR)';
            arahSlug = 'MUTASI_MASUK';
        } else if (filterDirection === 'keluar') {
            arahLabel = 'TRANSFER KELUAR (ASET RSUD KE OPD LUAR)';
            arahSlug = 'TRANSFER_KELUAR';
        }

        let twLabel = 'KESELURUHAN (TAHUNAN)';
        let twSlug = 'TAHUNAN';
        if (filterTw === 'TW I' || filterTw === 'TW1') { twLabel = 'TRIWULAN I'; twSlug = 'TW1'; }
        else if (filterTw === 'TW II' || filterTw === 'TW2') { twLabel = 'TRIWULAN II'; twSlug = 'TW2'; }
        else if (filterTw === 'TW III' || filterTw === 'TW3') { twLabel = 'TRIWULAN III'; twSlug = 'TW3'; }
        else if (filterTw === 'TW IV' || filterTw === 'TW4') { twLabel = 'TRIWULAN IV'; twSlug = 'TW4'; }

        const yearLabel = (filterYear === 'all') ? 'SEMUA TAHUN' : filterYear;
        const catSlug = (filterCat === 'all') ? 'LENGKAP_7SHEET' : filterCat.replace(/\s+/g, '_');

        // Helper hitung summary per kategori
        const getCatSummary = (catList) => {
            let unitTotal = 0;
            let nilaiTotal = 0;
            catList.forEach(it => {
                const det = getMutasiItemDetails(it);
                const spec = det.spec;
                let uCount = parseInt(it.jumlah_volume) || 1;
                if (spec.mesin_items && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                    uCount = spec.mesin_items.reduce((acc, m) => acc + (parseInt(m.mesin_jumlah_barang || m.mesin_jumlah) || 1), 0);
                }
                unitTotal += uCount;
                nilaiTotal += parseFloat(it.nilai_perolehan || it.total_realisasi_num || 0);
            });
            return { unitTotal, nilaiTotal, itemCount: catList.length };
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
        // 1. LEMBAR REKAPITULASI (SHEET 1) - STANDAR RESMI BPKAD (1 TABEL RESMI)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'REKAP') {
            const rekapData = [
                ['PEMERINTAH KABUPATEN BONDOWOSO'],
                ['RUMAH SAKIT UMUM DAERAH dr. H. KOESNANDI'],
                ['BUKU REKAPITULASI ASET MUTASI EKSTERNAL (ANTAR-OPD)'],
                [`ARAH / ALIRAN: ${arahLabel} · PERIODE: ${twLabel} TAHUN ANGGARAN ${yearLabel}`],
                [''],
                [
                    'NO',
                    'KLASIFIKASI KIB / AKUN 108 MUTASI',
                    'KODE AKUN REKENING BMD',
                    'DESKRIPSI / SIFAT TRANSAKSI MUTASI',
                    'JUMLAH ITEM / UNIT',
                    'TOTAL NILAI PEROLEHAN ASET (Rp)',
                    'KETERANGAN / DOKUMEN BAST'
                ],
                ['1', 'KIB A - TANAH MUTASI', '1.3.1.01.01.01.001', 'Tanah Bangunan, Fasilitas Medis & Pelayanan', `${sumA.unitTotal} Bidang`, sumA.nilaiTotal, sumA.itemCount > 0 ? 'Aset Sah Tercatat' : 'Nihil'],
                ['2', 'KIB B - PERALATAN DAN MESIN', '1.3.2.01.01.04.002', 'Peralatan, Mesin & Fasilitas Medis / Alkes', `${sumB.unitTotal} Unit`, sumB.nilaiTotal, sumB.itemCount > 0 ? 'Aset Sah Tercatat' : 'Nihil'],
                ['3', 'KIB C - GEDUNG DAN BANGUNAN', '1.3.3.01.01.03.003', 'Gedung & Bangunan Fisik Rumah Sakit', `${sumC.unitTotal} Bangunan`, sumC.nilaiTotal, sumC.itemCount > 0 ? 'Aset Sah Tercatat' : 'Nihil'],
                ['4', 'KIB D - JALAN, IRIGASI DAN JARINGAN', '1.3.4.01.01.04.004', 'Infrastruktur Jaringan & Utilitas Instalasi', `${sumD.unitTotal} Jaringan`, sumD.nilaiTotal, sumD.itemCount > 0 ? 'Aset Sah Tercatat' : 'Nihil'],
                ['5', 'KIB E - ASET TETAP LAINNYA', '1.3.5.01.01.02.005', 'Buku Perpustakaan, Kesenian & Aset Tetap Lain', `${sumE.unitTotal} Unit/Item`, sumE.nilaiTotal, sumE.itemCount > 0 ? 'Aset Sah Tercatat' : 'Nihil'],
                ['6', 'EXTRACOM - BARANG EKSTRAKOMTABEL (< Rp 300.000)', '1.3.2.01.01.04.002', 'Peralatan & Barang Ekstrakomtabel Non-Kapitalisasi', `${sumExtracom.unitTotal} Unit/Item`, sumExtracom.nilaiTotal, sumExtracom.itemCount > 0 ? 'Barang Ekstrakomtabel Nilai < Rp 300.000' : 'Nihil'],
                [
                    'JUMLAH TOTAL NILAI ASET MUTASI EKSTERNAL',
                    '', '', '',
                    `${grandTotalUnit} Unit Total`,
                    grandTotalNilai,
                    `Rekapitulasi ${twLabel} ${yearLabel}`
                ],
                ['']
            ];

            const signStartRow = rekapData.length;
            const signRows = buildMutasiSignRows(7, 4, 1);
            signRows.forEach(r => rekapData.push(r));

            const wsRekap = XLSX.utils.aoa_to_sheet(rekapData);
            wsRekap['!cols'] = [
                { wch: 6 },
                { wch: 46 },
                { wch: 25 },
                { wch: 42 },
                { wch: 22 },
                { wch: 28 },
                { wch: 36 }
            ];

            wsRekap['!merges'] = [
                { s: { r: 0, c: 0 }, e: { r: 0, c: 6 } },
                { s: { r: 1, c: 0 }, e: { r: 1, c: 6 } },
                { s: { r: 2, c: 0 }, e: { r: 2, c: 6 } },
                { s: { r: 3, c: 0 }, e: { r: 3, c: 6 } },
                { s: { r: 12, c: 0 }, e: { r: 12, c: 3 } },
                ...getKibSignatureMerges(signStartRow, 7, 4, 1, 2, 6)
            ];

            applyRekapSheetStyling(wsRekap, rekapData.length, 7, 5, 12, signStartRow);
            applySignatureBlockStyling(wsRekap, signStartRow, 7);
            XLSX.utils.book_append_sheet(wb, wsRekap, filterCat === 'all' ? '1. Rekapitulasi' : 'Rekapitulasi Mutasi');
        }

        // 2. KIB A - TANAH (26 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB A') {
            const kibATitleRows = getMutasiKibTitleRows('TANAH MUTASI EKSTERNAL', yearLabel, filterTw, arahLabel);
            const kibARows = [
                ...kibATitleRows,
                [
                    'NO',
                    'ASET MUTASI EKSTERNAL (PMDN 108)', '', '', '', '', '',
                    'RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / ' + yearLabel,
                    '', '', '', '', '', '', '', '', '', '', '',
                    'LETAK / ALAMAT BARANG',
                    'SKPD / OPD MITRA MUTASI', '', '',
                    'Pengurus Barang / Pejabat RSUD', '',
                    'KET.'
                ],
                [
                    '',
                    'Jenis Aset (PMDN 108)', '',
                    'Sub Rincian Objek (PMDN 108)', '',
                    'JUMLAH ANGGARAN (Rp)',
                    'NILAI PEROLEHAN BMD (Rp)',
                    'NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)',
                    'Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)',
                    'Status Tanah', '', '',
                    'Dokumen BAST Mutasi', '',
                    'Kondisi\n(B/KB/RB)',
                    'Penggunaan',
                    'VOLUME', '',
                    'Total Nilai Barang (Rp)',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    '',
                    'Kode', 'Nama Jenis Aset',
                    'Kode', 'Nama Uraian Sub Rincian Objek',
                    '', '',
                    '', '',
                    'Hak Tanah\n(Hak Pakai / Pengelolaan)',
                    'Sertifikat', '',
                    '', '',
                    '',
                    '',
                    '',
                    'Jumlah Bidang Tanah', 'Luas Tanah (m²)',
                    '',
                    '',
                    'Nama Instansi Mitra', 'Pejabat Mitra', 'Alamat Mitra',
                    'Nama', 'NIP',
                    ''
                ],
                [
                    '',
                    '', '', '', '', '', '',
                    '', '',
                    '',
                    'Tanggal', 'Nomor',
                    'Nomor', 'Tanggal',
                    '',
                    '',
                    '', '',
                    '',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26
                ]
            ];

            const kibAGroups = {};
            categories['KIB A'].forEach(item => {
                const info = resolve108Info(item, '1.3.1', 'TANAH');
                const groupKey = info.subRincianKode;
                if (!kibAGroups[groupKey]) kibAGroups[groupKey] = [];
                kibAGroups[groupKey].push(item);
            });

            let globalKibANo = 1;
            let kibATotalAnggaran = 0;
            let kibATotalRealisasi = 0;
            let kibATotalUnit = 0;
            let kibATotalLuas = 0;
            let kibATotalNilaiBarang = 0;

            Object.keys(kibAGroups).forEach(groupKey => {
                const groupItems = kibAGroups[groupKey];
                let groupRealisasiTotal = 0;
                groupItems.forEach(it => {
                    groupRealisasiTotal += parseFloat(it.nilai_perolehan || it.total_realisasi_num || 0);
                });

                kibATotalRealisasi += groupRealisasiTotal;
                let isFirstRowInGroup = true;

                groupItems.forEach(item => {
                    const info = resolve108Info(item, '1.3.1', 'TANAH');
                    const details = getMutasiItemDetails(item);
                    const spec = details.spec;
                    const jumlahBidang = parseInt(item.jumlah_volume) || 1;
                    const luasM2 = parseFloat(spec.luas_m2 || spec.tanah_luas || 0);
                    const totalNilaiBarang = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);

                    kibATotalUnit += jumlahBidang;
                    kibATotalLuas += luasM2;
                    kibATotalNilaiBarang += totalNilaiBarang;

                    const col1to7 = isFirstRowInGroup ? [
                        globalKibANo,
                        info.jenisKode,
                        info.jenisNama,
                        info.subRincianKode,
                        info.subRincianNama,
                        0,
                        groupRealisasiTotal
                    ] : ['', '', '', '', '', 0, 0];

                    if (isFirstRowInGroup) { globalKibANo++; isFirstRowInGroup = false; }

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    kibARows.push([
                        ...col1to7,
                        info.namaBarang,
                        info.kodeBarang,
                        spec.hak_tanah || spec.tanah_hak || 'Hak Pakai',
                        formatAstapDate(spec.sertifikat_tgl || spec.tanah_sertifikat_tgl || item.tgl_raw),
                        spec.sertifikat_no || spec.tanah_sertifikat_no || '-',
                        details.dokumenNomor,
                        details.dokumenTanggal,
                        kondisiLabel,
                        spec.penggunaan || spec.tanah_penggunaan || 'Fasilitas RSUD',
                        jumlahBidang,
                        luasM2,
                        totalNilaiBarang,
                        details.ruangPenempatan,
                        details.opdMitra,
                        details.pjMitra,
                        details.alamatMitra,
                        details.pejabatRsudNama,
                        details.pejabatRsudNip,
                        (details.isKeluar ? 'Transfer Keluar ke ' + details.opdMitra : 'Mutasi Masuk dari ' + details.opdMitra)
                    ]);
                });
            });

            // Baris Footer Total KIB A
            const kibAFooterRow = Array(26).fill('');
            kibAFooterRow[0] = 'JUMLAH';
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
            wsKibA['!cols'][1] = { wch: 14 }; wsKibA['!cols'][2] = { wch: 24 };
            wsKibA['!cols'][3] = { wch: 18 }; wsKibA['!cols'][4] = { wch: 28 };
            wsKibA['!cols'][5] = { wch: 20 }; wsKibA['!cols'][6] = { wch: 22 };
            wsKibA['!cols'][7] = { wch: 30 }; wsKibA['!cols'][8] = { wch: 22 };
            wsKibA['!cols'][9] = { wch: 18 }; wsKibA['!cols'][10] = { wch: 14 };
            wsKibA['!cols'][11] = { wch: 20 }; wsKibA['!cols'][12] = { wch: 22 };
            wsKibA['!cols'][13] = { wch: 14 }; wsKibA['!cols'][14] = { wch: 14 };
            wsKibA['!cols'][15] = { wch: 24 }; wsKibA['!cols'][16] = { wch: 18 };
            wsKibA['!cols'][17] = { wch: 18 }; wsKibA['!cols'][18] = { wch: 24 };
            wsKibA['!cols'][19] = { wch: 24 }; wsKibA['!cols'][20] = { wch: 28 };
            wsKibA['!cols'][21] = { wch: 24 }; wsKibA['!cols'][22] = { wch: 28 };
            wsKibA['!cols'][23] = { wch: 22 }; wsKibA['!cols'][24] = { wch: 22 };
            wsKibA['!cols'][25] = { wch: 26 };

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
            XLSX.utils.book_append_sheet(wb, wsKibA, filterCat === 'all' ? '2. KIB A - Tanah' : 'KIB A - Tanah');
        }

        // ========================================================================
        // 3. KIB B - PERALATAN DAN MESIN (33 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB B') {
            const kibBTitleRows = getMutasiKibTitleRows('PERALATAN DAN MESIN MUTASI EKSTERNAL', yearLabel, filterTw, arahLabel);
            const kibBRows = [
                ...kibBTitleRows,
                [
                    'NO',
                    'ASET MUTASI EKSTERNAL (PMDN 108)', '', '', '', '', '',
                    'RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / ' + yearLabel,
                    '', '', '', '', '', '', '', '', '', '', '',
                    '', '', '', '', '', '', '',
                    'RUANGAN /\nPEMEGANG',
                    'SKPD / OPD MITRA MUTASI', '', '',
                    'Pengurus Barang / Pejabat RSUD', '',
                    'KET.'
                ],
                [
                    '',
                    'Jenis Aset (PMDN 108)', '',
                    'Sub Rincian Objek (PMDN 108)', '',
                    'JUMLAH ANGGARAN (Rp)',
                    'NILAI PEROLEHAN BMD (Rp)',
                    'NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)',
                    'Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)',
                    'Merk', 'Type', 'Ukuran / CC',
                    'No. Pabrik', 'No. Rangka', 'No. Mesin', 'No. BPKB', 'No. POLISI',
                    'BAHAN', 'Tahun Perolehan',
                    'Dokumen BAST Mutasi', '',
                    'Kondisi\n(B,KB,RB)',
                    'VOLUME', '',
                    'Nilai Barang (Rp)', '',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    '',
                    'Kode', 'Nama Jenis Aset',
                    'Kode', 'Nama Uraian Sub Rincian Objek',
                    '', '',
                    '', '',
                    '', '', '', '', '', '', '', '',
                    '', '',
                    'Nomor', 'Tanggal',
                    '',
                    'Jumlah Barang', 'Nama Satuan Barang',
                    'Nilai Satuan Barang (Rp)', 'Total Nilai Barang (Rp)',
                    '',
                    'Nama Instansi Mitra', 'Pejabat Mitra', 'Alamat Mitra',
                    'Nama', 'NIP',
                    ''
                ],
                [
                    '',
                    '', '', '', '', '', '',
                    '', '',
                    '', '', '', '', '', '', '', '', '', '',
                    '', '',
                    '',
                    '', '',
                    '', '',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                    31, 32, 33
                ]
            ];

            const kibBGroups = {};
            categories['KIB B'].forEach(item => {
                const info = resolve108Info(item, '1.3.2', 'PERALATAN DAN MESIN');
                const groupKey = info.subRincianKode;
                if (!kibBGroups[groupKey]) kibBGroups[groupKey] = [];
                kibBGroups[groupKey].push(item);
            });

            let globalKibBNo = 1;
            let kibBTotalAnggaran = 0;
            let kibBTotalRealisasi = 0;
            let kibBTotalUnit = 0;
            let kibBTotalNilaiBarang = 0;

            Object.keys(kibBGroups).forEach(groupKey => {
                const groupItems = kibBGroups[groupKey];
                let groupRealisasiTotal = 0;
                groupItems.forEach(it => {
                    groupRealisasiTotal += parseFloat(it.nilai_perolehan || it.total_realisasi_num || 0);
                });

                kibBTotalRealisasi += groupRealisasiTotal;
                let isFirstRowInGroup = true;

                groupItems.forEach(item => {
                    const info = resolve108Info(item, '1.3.2', 'PERALATAN DAN MESIN');
                    const details = getMutasiItemDetails(item);
                    const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);
                    const subMesin = (details.spec.mesin_items && Array.isArray(details.spec.mesin_items) && details.spec.mesin_items.length > 0) ? details.spec.mesin_items : null;
                    const thnPerolehan = item.tahun_perolehan || details.spec.tahun_pembuatan || new Date().getFullYear();

                    const makeCol1to7 = (isFirst) => {
                        if (!isFirst) return ['', '', '', '', '', 0, 0];
                        return [
                            globalKibBNo,
                            info.jenisKode,
                            info.jenisNama,
                            info.subRincianKode,
                            info.subRincianNama,
                            0,
                            groupRealisasiTotal
                        ];
                    };

                    if (subMesin) {
                        subMesin.forEach((mItem) => {
                            const qty = Math.max(1, parseInt(mItem.mesin_jumlah_barang || mItem.mesin_jumlah || 1));
                            const valSatuan = parseFloat(mItem.mesin_nilai_satuan || 0) || (qty > 0 ? totalItemNilai / subMesin.length / qty : 0);
                            const subTotal = (qty * valSatuan);

                            kibBTotalUnit += qty;
                            kibBTotalNilaiBarang += subTotal;

                            const col1to7 = makeCol1to7(isFirstRowInGroup);
                            if (isFirstRowInGroup) { globalKibBNo++; isFirstRowInGroup = false; }

                            const rawKondisi = mItem.mesin_kondisi || item.kondisi || 'Baik';
                            const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                               : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                               : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                            const ruangUnit = mItem.ruang_pemegang || details.ruangPenempatan;

                            kibBRows.push([
                                ...col1to7,
                                mItem.mesin_nama_barang || info.namaBarang,
                                mItem.kode_108 || info.kodeBarang,
                                mItem.mesin_merk || details.spec.merk || item.merk || '-',
                                mItem.mesin_type || details.spec.type || item.type || '-',
                                mItem.mesin_ukuran || mItem.mesin_ukuran_cc || details.spec.ukuran || item.ukuran || '-',
                                mItem.mesin_no_pabrik || details.spec.no_pabrik || item.no_pabrik || '-',
                                mItem.mesin_no_rangka || details.spec.no_rangka || item.no_rangka || '-',
                                mItem.mesin_no_mesin || details.spec.no_mesin || item.no_mesin || '-',
                                mItem.mesin_no_bpkb || details.spec.no_bpkb || item.no_bpkb || '-',
                                mItem.mesin_no_polisi || details.spec.no_polisi || item.no_polisi || '-',
                                mItem.mesin_bahan || details.spec.bahan || item.bahan || '-',
                                thnPerolehan,
                                details.dokumenNomor,
                                details.dokumenTanggal,
                                kondisiLabel,
                                qty,
                                mItem.mesin_satuan || item.satuan || 'Unit',
                                valSatuan,
                                subTotal,
                                ruangUnit,
                                details.opdMitra,
                                details.pjMitra,
                                details.alamatMitra,
                                details.pejabatRsudNama,
                                details.pejabatRsudNip,
                                (details.isKeluar ? 'Transfer Keluar ke ' + details.opdMitra : 'Mutasi Masuk dari ' + details.opdMitra)
                            ]);
                        });
                    } else {
                        const qty = parseInt(item.jumlah_volume) || 1;
                        const valSatuan = parseFloat(item.harga_satuan) || (qty > 0 ? totalItemNilai / qty : totalItemNilai);

                        kibBTotalUnit += qty;
                        kibBTotalNilaiBarang += totalItemNilai;

                        const col1to7 = makeCol1to7(isFirstRowInGroup);
                        if (isFirstRowInGroup) { globalKibBNo++; isFirstRowInGroup = false; }

                        const rawKondisi = item.kondisi || 'Baik';
                        const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                           : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                           : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                        kibBRows.push([
                            ...col1to7,
                            info.namaBarang,
                            info.kodeBarang,
                            details.spec.merk || item.merk || '-',
                            details.spec.type || item.type || '-',
                            details.spec.ukuran || item.ukuran || '-',
                            details.spec.no_pabrik || item.no_pabrik || '-',
                            details.spec.no_rangka || item.no_rangka || '-',
                            details.spec.no_mesin || item.no_mesin || '-',
                            details.spec.no_bpkb || item.no_bpkb || '-',
                            details.spec.no_polisi || item.no_polisi || '-',
                            details.spec.bahan || item.bahan || '-',
                            thnPerolehan,
                            details.dokumenNomor,
                            details.dokumenTanggal,
                            kondisiLabel,
                            qty,
                            item.satuan || 'Unit',
                            valSatuan,
                            totalItemNilai,
                            details.ruangPenempatan,
                            details.opdMitra,
                            details.pjMitra,
                            details.alamatMitra,
                            details.pejabatRsudNama,
                            details.pejabatRsudNip,
                            (details.isKeluar ? 'Transfer Keluar ke ' + details.opdMitra : 'Mutasi Masuk dari ' + details.opdMitra)
                        ]);
                    }
                });
            });

            // Baris Footer Total KIB B
            const kibBFooterRow = Array(33).fill('');
            kibBFooterRow[0] = 'JUMLAH';
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
            XLSX.utils.book_append_sheet(wb, wsKibB, filterCat === 'all' ? '3. KIB B - Mesin & Alkes' : 'KIB B - Peralatan & Mesin');
        }

        // ========================================================================
        // 4. KIB C - GEDUNG DAN BANGUNAN (31 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB C') {
            const kibCTitleRows = getMutasiKibTitleRows('GEDUNG DAN BANGUNAN MUTASI EKSTERNAL', yearLabel, filterTw, arahLabel);
            const kibCRows = [
                ...kibCTitleRows,
                [
                    'NO',
                    'ASET MUTASI EKSTERNAL (PMDN 108)', '', '', '', '', '',
                    'RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / ' + yearLabel,
                    '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                    'Letak / Lokasi',
                    'SKPD / OPD MITRA MUTASI', '', '',
                    'Pengurus Barang / Pejabat RSUD', '',
                    'KET.'
                ],
                [
                    '',
                    'Jenis Aset (PMDN 108)', '',
                    'Sub Rincian Objek (PMDN 108)', '',
                    'JUMLAH ANGGARAN (Rp)',
                    'NILAI PEROLEHAN BMD (Rp)',
                    'NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)',
                    'Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)',
                    'Luas Lantai (m²)',
                    'Kondisi / Spesifikasi', '', '',
                    'Jenis Bangunan', '', '', '', '', '',
                    'Dokumen BAST Mutasi', '',
                    'VOLUME', '',
                    'Total Nilai Barang (Rp)',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    '',
                    'Kode', 'Nama Jenis Aset',
                    'Kode', 'Nama Uraian Sub Rincian Objek',
                    '', '',
                    '', '',
                    '',
                    '(B,KB,RB)', 'Bertingkat / Tidak', 'Beton / Tidak',
                    'Status Tanah', 'Kode Aset Tanah', 'Baru',
                    'Kapitalisasi', '', '',
                    'Nomor', 'Tanggal',
                    'Jumlah Bangunan', 'Nama Satuan Barang',
                    '',
                    '',
                    'Nama Instansi Mitra', 'Pejabat Mitra', 'Alamat Mitra',
                    'Nama', 'NIP',
                    ''
                ],
                [
                    '', '', '', '', '', '', '',
                    '', '', '', '', '', '',
                    '', '', '',
                    'Nibar', 'Tahun Induk', 'Nilai Induk s/d ' + (parseInt(yearLabel) - 1 || '2025'),
                    '', '',
                    '', '',
                    '',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                    31
                ]
            ];

            const kibCGroups = {};
            categories['KIB C'].forEach(item => {
                const info = resolve108Info(item, '1.3.3', 'GEDUNG DAN BANGUNAN');
                const groupKey = info.subRincianKode;
                if (!kibCGroups[groupKey]) kibCGroups[groupKey] = [];
                kibCGroups[groupKey].push(item);
            });

            let globalKibCNo = 1;
            let kibCTotalAnggaran = 0;
            let kibCTotalRealisasi = 0;
            let kibCTotalUnit = 0;
            let kibCTotalNilaiBarang = 0;

            Object.keys(kibCGroups).forEach(groupKey => {
                const groupItems = kibCGroups[groupKey];
                let groupRealisasiTotal = 0;
                groupItems.forEach(it => {
                    groupRealisasiTotal += parseFloat(it.nilai_perolehan || it.total_realisasi_num || 0);
                });

                kibCTotalRealisasi += groupRealisasiTotal;
                let isFirstRowInGroup = true;

                groupItems.forEach(item => {
                    const info = resolve108Info(item, '1.3.3', 'GEDUNG DAN BANGUNAN');
                    const details = getMutasiItemDetails(item);
                    const spec = details.spec;
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);
                    const luasLantai = parseFloat(spec.luas_m2 || spec.gedung_luas_m2 || spec.luas_lantai || 0);

                    kibCTotalUnit += qty;
                    kibCTotalNilaiBarang += totalItemNilai;

                    const col1to7 = isFirstRowInGroup ? [
                        globalKibCNo,
                        info.jenisKode,
                        info.jenisNama,
                        info.subRincianKode,
                        info.subRincianNama,
                        0,
                        groupRealisasiTotal
                    ] : ['', '', '', '', '', 0, 0];

                    if (isFirstRowInGroup) { globalKibCNo++; isFirstRowInGroup = false; }

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    kibCRows.push([
                        ...col1to7,
                        info.namaBarang,
                        info.kodeBarang,
                        luasLantai,
                        kondisiLabel,
                        spec.bertingkat || spec.gedung_bertingkat || 'Bertingkat',
                        spec.beton || spec.gedung_beton || 'Beton',
                        spec.status_tanah || spec.gedung_status_tanah || 'Tanah Hak Pakai RSUD',
                        spec.kode_tanah || spec.gedung_kode_aset_tanah || '-',
                        spec.is_baru || spec.gedung_is_baru || 'Pengadaan Baru',
                        spec.nibar_induk || spec.kapitalisasi_nibar || '-',
                        spec.tahun_induk || spec.kapitalisasi_tahun_induk || '-',
                        parseFloat(spec.nilai_induk || spec.kapitalisasi_nilai_induk || 0),
                        details.dokumenNomor,
                        details.dokumenTanggal,
                        qty,
                        item.satuan || 'Bangunan',
                        totalItemNilai,
                        details.ruangPenempatan,
                        details.opdMitra,
                        details.pjMitra,
                        details.alamatMitra,
                        details.pejabatRsudNama,
                        details.pejabatRsudNip,
                        (details.isKeluar ? 'Transfer Keluar ke ' + details.opdMitra : 'Mutasi Masuk dari ' + details.opdMitra)
                    ]);
                });
            });

            // Baris Footer Total KIB C
            const kibCFooterRow = Array(31).fill('');
            kibCFooterRow[0] = 'JUMLAH';
            kibCFooterRow[5] = kibCTotalAnggaran;
            kibCFooterRow[6] = kibCTotalRealisasi;
            kibCFooterRow[21] = kibCTotalUnit;
            kibCFooterRow[23] = kibCTotalNilaiBarang;
            kibCRows.push(kibCFooterRow);

            const kibCSignStartRow = kibCRows.length;
            const kibCSignRows = buildMutasiSignRows(31, 22, 1);
            kibCSignRows.forEach(r => kibCRows.push(r));

            const wsKibC = XLSX.utils.aoa_to_sheet(kibCRows);
            wsKibC['!cols'] = Array(31).fill({ wch: 18 });
            wsKibC['!cols'][0] = { wch: 6 };
            wsKibC['!cols'][1] = { wch: 14 }; wsKibC['!cols'][2] = { wch: 24 };
            wsKibC['!cols'][3] = { wch: 18 }; wsKibC['!cols'][4] = { wch: 28 };
            wsKibC['!cols'][5] = { wch: 20 }; wsKibC['!cols'][6] = { wch: 22 };
            wsKibC['!cols'][7] = { wch: 30 }; wsKibC['!cols'][8] = { wch: 22 };
            wsKibC['!cols'][23] = { wch: 24 }; wsKibC['!cols'][24] = { wch: 24 };
            wsKibC['!cols'][25] = { wch: 26 }; wsKibC['!cols'][26] = { wch: 22 };
            wsKibC['!cols'][27] = { wch: 26 }; wsKibC['!cols'][28] = { wch: 22 };
            wsKibC['!cols'][29] = { wch: 22 }; wsKibC['!cols'][30] = { wch: 26 };

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
                { s: { r: 3, c: 7 }, e: { r: 3, c: 23 } },
                { s: { r: 4, c: 7 }, e: { r: 6, c: 7 } },
                { s: { r: 4, c: 8 }, e: { r: 6, c: 8 } },
                { s: { r: 4, c: 9 }, e: { r: 6, c: 9 } },
                { s: { r: 4, c: 10 }, e: { r: 4, c: 12 } },
                { s: { r: 5, c: 10 }, e: { r: 6, c: 10 } },
                { s: { r: 5, c: 11 }, e: { r: 6, c: 11 } },
                { s: { r: 5, c: 12 }, e: { r: 6, c: 12 } },
                { s: { r: 4, c: 13 }, e: { r: 4, c: 18 } },
                { s: { r: 5, c: 13 }, e: { r: 6, c: 13 } },
                { s: { r: 5, c: 14 }, e: { r: 6, c: 14 } },
                { s: { r: 5, c: 15 }, e: { r: 6, c: 15 } },
                { s: { r: 5, c: 16 }, e: { r: 5, c: 18 } },
                { s: { r: 4, c: 19 }, e: { r: 4, c: 20 } },
                { s: { r: 5, c: 19 }, e: { r: 6, c: 19 } },
                { s: { r: 5, c: 20 }, e: { r: 6, c: 20 } },
                { s: { r: 4, c: 21 }, e: { r: 4, c: 22 } },
                { s: { r: 5, c: 21 }, e: { r: 6, c: 21 } },
                { s: { r: 5, c: 22 }, e: { r: 6, c: 22 } },
                { s: { r: 4, c: 23 }, e: { r: 6, c: 23 } },
                { s: { r: 3, c: 24 }, e: { r: 6, c: 24 } },
                { s: { r: 3, c: 25 }, e: { r: 4, c: 27 } },
                { s: { r: 5, c: 25 }, e: { r: 6, c: 25 } },
                { s: { r: 5, c: 26 }, e: { r: 6, c: 26 } },
                { s: { r: 5, c: 27 }, e: { r: 6, c: 27 } },
                { s: { r: 3, c: 28 }, e: { r: 4, c: 29 } },
                { s: { r: 5, c: 28 }, e: { r: 6, c: 28 } },
                { s: { r: 5, c: 29 }, e: { r: 6, c: 29 } },
                { s: { r: 3, c: 30 }, e: { r: 6, c: 30 } }
            ], 31, kibCTitleRows.length, kibCRows.length, true);
            wsKibC['!merges'].push(...getKibSignatureMerges(kibCSignStartRow, 31, 22, 1, 8, 30));

            applyMutasiMasterSheetStyling(wsKibC, kibCRows.length, 31, 17, kibCTitleRows.length, true);
            applySignatureBlockStyling(wsKibC, kibCSignStartRow, 31);
            XLSX.utils.book_append_sheet(wb, wsKibC, filterCat === 'all' ? '4. KIB C - Gedung' : 'KIB C - Gedung & Bangunan');
        }

        // ========================================================================
        // 5. KIB D - JALAN, IRIGASI DAN JARINGAN (31 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB D') {
            const kibDTitleRows = getMutasiKibTitleRows('JALAN, IRIGASI DAN JARINGAN MUTASI EKSTERNAL', yearLabel, filterTw, arahLabel);
            const kibDRows = [
                ...kibDTitleRows,
                [
                    'NO',
                    'ASET MUTASI EKSTERNAL (PMDN 108)', '', '', '', '', '',
                    'RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / ' + yearLabel,
                    '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '',
                    'Letak / Lokasi',
                    'SKPD / OPD MITRA MUTASI', '', '',
                    'Pengurus Barang / Pejabat RSUD', '',
                    'KET.'
                ],
                [
                    '',
                    'Jenis Aset (PMDN 108)', '',
                    'Sub Rincian Objek (PMDN 108)', '',
                    'JUMLAH ANGGARAN (Rp)',
                    'NILAI PEROLEHAN BMD (Rp)',
                    'NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)',
                    'Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)',
                    'Luas / Panjang (m² / m)',
                    'Kondisi / Konstruksi', '', '',
                    'Jenis Jaringan / Fasilitas', '', '', '', '', '',
                    'Dokumen BAST Mutasi', '',
                    'VOLUME', '',
                    'Total Nilai Barang (Rp)',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    '',
                    'Kode', 'Nama Jenis Aset',
                    'Kode', 'Nama Uraian Sub Rincian Objek',
                    '', '',
                    '', '',
                    '',
                    '(B,KB,RB)', 'Bertingkat / Tidak', 'Beton / Tidak',
                    'Status Tanah', 'Kode Aset Tanah', 'Baru',
                    'Kapitalisasi', '', '',
                    'Nomor', 'Tanggal',
                    'Jumlah Unit', 'Nama Satuan Barang',
                    '',
                    '',
                    'Nama Instansi Mitra', 'Pejabat Mitra', 'Alamat Mitra',
                    'Nama', 'NIP',
                    ''
                ],
                [
                    '', '', '', '', '', '', '',
                    '', '', '', '', '', '',
                    '', '', '',
                    'Nibar', 'Tahun Induk', 'Nilai Induk s/d ' + (parseInt(yearLabel) - 1 || '2025'),
                    '', '',
                    '', '',
                    '',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                    31
                ]
            ];

            const kibDGroups = {};
            categories['KIB D'].forEach(item => {
                const info = resolve108Info(item, '1.3.4', 'JALAN, IRIGASI DAN JARINGAN');
                const groupKey = info.subRincianKode;
                if (!kibDGroups[groupKey]) kibDGroups[groupKey] = [];
                kibDGroups[groupKey].push(item);
            });

            let globalKibDNo = 1;
            let kibDTotalAnggaran = 0;
            let kibDTotalRealisasi = 0;
            let kibDTotalUnit = 0;
            let kibDTotalNilaiBarang = 0;

            Object.keys(kibDGroups).forEach(groupKey => {
                const groupItems = kibDGroups[groupKey];
                let groupRealisasiTotal = 0;
                groupItems.forEach(it => {
                    groupRealisasiTotal += parseFloat(it.nilai_perolehan || it.total_realisasi_num || 0);
                });

                kibDTotalRealisasi += groupRealisasiTotal;
                let isFirstRowInGroup = true;

                groupItems.forEach(item => {
                    const info = resolve108Info(item, '1.3.4', 'JALAN, IRIGASI DAN JARINGAN');
                    const details = getMutasiItemDetails(item);
                    const spec = details.spec;
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);
                    const luasPanjang = parseFloat(spec.luas_m2 || spec.panjang_m || spec.panjang_km || 0);

                    kibDTotalUnit += qty;
                    kibDTotalNilaiBarang += totalItemNilai;

                    const col1to7 = isFirstRowInGroup ? [
                        globalKibDNo,
                        info.jenisKode,
                        info.jenisNama,
                        info.subRincianKode,
                        info.subRincianNama,
                        0,
                        groupRealisasiTotal
                    ] : ['', '', '', '', '', 0, 0];

                    if (isFirstRowInGroup) { globalKibDNo++; isFirstRowInGroup = false; }

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    kibDRows.push([
                        ...col1to7,
                        info.namaBarang,
                        info.kodeBarang,
                        luasPanjang,
                        kondisiLabel,
                        spec.konstruksi || spec.jenis_konstruksi || 'Permanen',
                        spec.beton_aspal || spec.tipe_jaringan || 'Instalasi Jaringan',
                        spec.status_tanah || 'Tanah Hak Pakai RSUD',
                        spec.kode_tanah || '-',
                        spec.is_baru || 'Baru',
                        spec.nibar_induk || '-',
                        spec.tahun_induk || '-',
                        parseFloat(spec.nilai_induk || 0),
                        details.dokumenNomor,
                        details.dokumenTanggal,
                        qty,
                        item.satuan || 'Jaringan',
                        totalItemNilai,
                        details.ruangPenempatan,
                        details.opdMitra,
                        details.pjMitra,
                        details.alamatMitra,
                        details.pejabatRsudNama,
                        details.pejabatRsudNip,
                        (details.isKeluar ? 'Transfer Keluar ke ' + details.opdMitra : 'Mutasi Masuk dari ' + details.opdMitra)
                    ]);
                });
            });

            // Baris Footer Total KIB D
            const kibDFooterRow = Array(31).fill('');
            kibDFooterRow[0] = 'JUMLAH';
            kibDFooterRow[5] = kibDTotalAnggaran;
            kibDFooterRow[6] = kibDTotalRealisasi;
            kibDFooterRow[21] = kibDTotalUnit;
            kibDFooterRow[23] = kibDTotalNilaiBarang;
            kibDRows.push(kibDFooterRow);

            const kibDSignStartRow = kibDRows.length;
            const kibDSignRows = buildMutasiSignRows(31, 22, 1);
            kibDSignRows.forEach(r => kibDRows.push(r));

            const wsKibD = XLSX.utils.aoa_to_sheet(kibDRows);
            wsKibD['!cols'] = Array(31).fill({ wch: 18 });
            wsKibD['!cols'][0] = { wch: 6 };
            wsKibD['!cols'][1] = { wch: 14 }; wsKibD['!cols'][2] = { wch: 24 };
            wsKibD['!cols'][3] = { wch: 18 }; wsKibD['!cols'][4] = { wch: 28 };
            wsKibD['!cols'][5] = { wch: 20 }; wsKibD['!cols'][6] = { wch: 22 };
            wsKibD['!cols'][7] = { wch: 30 }; wsKibD['!cols'][8] = { wch: 22 };
            wsKibD['!cols'][23] = { wch: 24 }; wsKibD['!cols'][24] = { wch: 24 };
            wsKibD['!cols'][25] = { wch: 26 }; wsKibD['!cols'][26] = { wch: 22 };
            wsKibD['!cols'][27] = { wch: 26 }; wsKibD['!cols'][28] = { wch: 22 };
            wsKibD['!cols'][29] = { wch: 22 }; wsKibD['!cols'][30] = { wch: 26 };

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
                { s: { r: 3, c: 7 }, e: { r: 3, c: 23 } },
                { s: { r: 4, c: 7 }, e: { r: 6, c: 7 } },
                { s: { r: 4, c: 8 }, e: { r: 6, c: 8 } },
                { s: { r: 4, c: 9 }, e: { r: 6, c: 9 } },
                { s: { r: 4, c: 10 }, e: { r: 4, c: 12 } },
                { s: { r: 5, c: 10 }, e: { r: 6, c: 10 } },
                { s: { r: 5, c: 11 }, e: { r: 6, c: 11 } },
                { s: { r: 5, c: 12 }, e: { r: 6, c: 12 } },
                { s: { r: 4, c: 13 }, e: { r: 4, c: 18 } },
                { s: { r: 5, c: 13 }, e: { r: 6, c: 13 } },
                { s: { r: 5, c: 14 }, e: { r: 6, c: 14 } },
                { s: { r: 5, c: 15 }, e: { r: 6, c: 15 } },
                { s: { r: 5, c: 16 }, e: { r: 5, c: 18 } },
                { s: { r: 4, c: 19 }, e: { r: 4, c: 20 } },
                { s: { r: 5, c: 19 }, e: { r: 6, c: 19 } },
                { s: { r: 5, c: 20 }, e: { r: 6, c: 20 } },
                { s: { r: 4, c: 21 }, e: { r: 4, c: 22 } },
                { s: { r: 5, c: 21 }, e: { r: 6, c: 21 } },
                { s: { r: 5, c: 22 }, e: { r: 6, c: 22 } },
                { s: { r: 4, c: 23 }, e: { r: 6, c: 23 } },
                { s: { r: 3, c: 24 }, e: { r: 6, c: 24 } },
                { s: { r: 3, c: 25 }, e: { r: 4, c: 27 } },
                { s: { r: 5, c: 25 }, e: { r: 6, c: 25 } },
                { s: { r: 5, c: 26 }, e: { r: 6, c: 26 } },
                { s: { r: 5, c: 27 }, e: { r: 6, c: 27 } },
                { s: { r: 3, c: 28 }, e: { r: 4, c: 29 } },
                { s: { r: 5, c: 28 }, e: { r: 6, c: 28 } },
                { s: { r: 5, c: 29 }, e: { r: 6, c: 29 } },
                { s: { r: 3, c: 30 }, e: { r: 6, c: 30 } }
            ], 31, kibDTitleRows.length, kibDRows.length, true);
            wsKibD['!merges'].push(...getKibSignatureMerges(kibDSignStartRow, 31, 22, 1, 8, 30));

            applyMutasiMasterSheetStyling(wsKibD, kibDRows.length, 31, 17, kibDTitleRows.length, true);
            applySignatureBlockStyling(wsKibD, kibDSignStartRow, 31);
            XLSX.utils.book_append_sheet(wb, wsKibD, filterCat === 'all' ? '5. KIB D - Jaringan' : 'KIB D - Jalan & Jaringan');
        }

        // ========================================================================
        // 6. KIB E - ASET TETAP LAINNYA (32 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'KIB E') {
            const kibETitleRows = getMutasiKibTitleRows('ASET TETAP LAINNYA MUTASI EKSTERNAL', yearLabel, filterTw, arahLabel);
            const kibERows = [
                ...kibETitleRows,
                [
                    'NO',
                    'ASET MUTASI EKSTERNAL (PMDN 108)', '', '', '', '', '',
                    'RINCIAN ASET MUTASI EKSTERNAL SESUAI BAST / SK BUPATI / ' + yearLabel,
                    '', '', '', '', '', '', '', '', '', '', '',
                    '', '', '', '', '', '', '',
                    'RUANG /\nPEMEGANG',
                    'SKPD / OPD MITRA MUTASI', '', '',
                    'Pengurus Barang / Pejabat RSUD', '',
                    'KET.'
                ],
                [
                    '',
                    'Jenis Aset (PMDN 108)', '',
                    'Sub Rincian Objek (PMDN 108)', '',
                    'JUMLAH ANGGARAN (Rp)',
                    'NILAI PEROLEHAN BMD (Rp)',
                    'NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)',
                    'Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)',
                    'BUKU PERPUSTAKAAN', '', '',
                    'Barang Bercorak Kesenian / Kebudayaan', '', '', '',
                    'Hewan Ternak / Tumbuhan', '', '',
                    'Dokumen BAST Mutasi', '',
                    'VOLUME', '',
                    'Nilai Barang (Rp)', '',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    '',
                    'Kode', 'Nama Jenis Aset',
                    'Kode', 'Nama Uraian Sub Rincian Objek',
                    '', '',
                    '', '',
                    'Judul', 'Pencipta', 'Spesifikasi',
                    'Asal Daerah', 'Pencipta', 'Spesifikasi', 'Bahan',
                    'Ukuran (m/cm)', 'Judul', 'Spesifikasi',
                    'Nomor', 'Tanggal',
                    'Jumlah Barang', 'Nama Satuan Barang',
                    'Nilai Satuan Barang (Rp)', 'Total Nilai Barang (Rp)',
                    '',
                    'Nama Instansi Mitra', 'Pejabat Mitra', 'Alamat Mitra',
                    'Nama', 'NIP',
                    ''
                ],
                [
                    '', '', '', '', '', '', '',
                    '', '',
                    '', '', '',
                    '', '', '', '',
                    '', '', '',
                    '', '',
                    '', '',
                    '', '',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29, 30,
                    31, 32
                ]
            ];

            const kibEGroups = {};
            categories['KIB E'].forEach(item => {
                const info = resolve108Info(item, '1.3.5', 'ASET TETAP LAINNYA');
                const groupKey = info.subRincianKode;
                if (!kibEGroups[groupKey]) kibEGroups[groupKey] = [];
                kibEGroups[groupKey].push(item);
            });

            let globalKibENo = 1;
            let kibETotalAnggaran = 0;
            let kibETotalRealisasi = 0;
            let kibETotalUnit = 0;
            let kibETotalNilaiBarang = 0;

            Object.keys(kibEGroups).forEach(groupKey => {
                const groupItems = kibEGroups[groupKey];
                let groupRealisasiTotal = 0;
                groupItems.forEach(it => {
                    groupRealisasiTotal += parseFloat(it.nilai_perolehan || it.total_realisasi_num || 0);
                });

                kibETotalRealisasi += groupRealisasiTotal;
                let isFirstRowInGroup = true;

                groupItems.forEach(item => {
                    const info = resolve108Info(item, '1.3.5', 'ASET TETAP LAINNYA');
                    const details = getMutasiItemDetails(item);
                    const spec = details.spec;
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);
                    const valSatuan = qty > 0 ? (totalItemNilai / qty) : totalItemNilai;

                    kibETotalUnit += qty;
                    kibETotalNilaiBarang += totalItemNilai;

                    const col1to7 = isFirstRowInGroup ? [
                        globalKibENo,
                        info.jenisKode,
                        info.jenisNama,
                        info.subRincianKode,
                        info.subRincianNama,
                        0,
                        groupRealisasiTotal
                    ] : ['', '', '', '', '', 0, 0];

                    if (isFirstRowInGroup) { globalKibENo++; isFirstRowInGroup = false; }

                    kibERows.push([
                        ...col1to7,
                        info.namaBarang,
                        info.kodeBarang,
                        spec.buku_judul || spec.judul || '-',
                        spec.buku_pencipta || spec.pengarang || '-',
                        spec.buku_spesifikasi || spec.spesifikasi || '-',
                        spec.kesenian_asal || spec.asal_daerah || '-',
                        spec.kesenian_pencipta || '-',
                        spec.kesenian_bahan || spec.bahan || '-',
                        spec.bahan || '-',
                        spec.ukuran || '-',
                        spec.judul_tambahan || '-',
                        spec.spesifikasi_tumbuhan || '-',
                        details.dokumenNomor,
                        details.dokumenTanggal,
                        qty,
                        item.satuan || 'Unit',
                        valSatuan,
                        totalItemNilai,
                        details.ruangPenempatan,
                        details.opdMitra,
                        details.pjMitra,
                        details.alamatMitra,
                        details.pejabatRsudNama,
                        details.pejabatRsudNip,
                        (details.isKeluar ? 'Transfer Keluar ke ' + details.opdMitra : 'Mutasi Masuk dari ' + details.opdMitra)
                    ]);
                });
            });

            // Baris Footer Total KIB E
            const kibEFooterRow = Array(32).fill('');
            kibEFooterRow[0] = 'JUMLAH';
            kibEFooterRow[5] = kibETotalAnggaran;
            kibEFooterRow[6] = kibETotalRealisasi;
            kibEFooterRow[21] = kibETotalUnit;
            kibEFooterRow[24] = kibETotalNilaiBarang;
            kibERows.push(kibEFooterRow);

            const kibESignStartRow = kibERows.length;
            const kibESignRows = buildMutasiSignRows(32, 23, 1);
            kibESignRows.forEach(r => kibERows.push(r));

            const wsKibE = XLSX.utils.aoa_to_sheet(kibERows);
            wsKibE['!cols'] = Array(32).fill({ wch: 18 });
            wsKibE['!cols'][0] = { wch: 6 };
            wsKibE['!cols'][1] = { wch: 14 }; wsKibE['!cols'][2] = { wch: 24 };
            wsKibE['!cols'][3] = { wch: 18 }; wsKibE['!cols'][4] = { wch: 28 };
            wsKibE['!cols'][5] = { wch: 20 }; wsKibE['!cols'][6] = { wch: 22 };
            wsKibE['!cols'][7] = { wch: 30 }; wsKibE['!cols'][8] = { wch: 22 };
            wsKibE['!cols'][23] = { wch: 22 }; wsKibE['!cols'][24] = { wch: 24 };
            wsKibE['!cols'][25] = { wch: 24 }; wsKibE['!cols'][26] = { wch: 26 };
            wsKibE['!cols'][27] = { wch: 22 }; wsKibE['!cols'][28] = { wch: 26 };
            wsKibE['!cols'][29] = { wch: 22 }; wsKibE['!cols'][30] = { wch: 22 };
            wsKibE['!cols'][31] = { wch: 26 };

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
            wsKibE['!merges'].push(...getKibSignatureMerges(kibESignStartRow, 32, 23, 1, 8, 31));

            applyMutasiMasterSheetStyling(wsKibE, kibERows.length, 32, 18, kibETitleRows.length, true);
            applySignatureBlockStyling(wsKibE, kibESignStartRow, 32);
            XLSX.utils.book_append_sheet(wb, wsKibE, filterCat === 'all' ? '6. KIB E - Aset Lainnya' : 'KIB E - Aset Tetap Lainnya');
        }

        // ========================================================================
        // 7. EXTRACOM - BARANG EKSTRAKOMTABEL (< Rp 300.000) (29 KOLOM)
        // ========================================================================
        if (filterCat === 'all' || filterCat === 'EXTRACOM') {
            const extracomTitleRows = getMutasiKibTitleRows('BARANG EKSTRAKOMTABEL (< Rp 300.000) MUTASI EKSTERNAL', yearLabel, filterTw, arahLabel);
            const extracomRows = [
                ...extracomTitleRows,
                [
                    'NO',
                    'ASET MUTASI EKSTERNAL (PMDN 108)', '', '', '', '', '',
                    'RINCIAN ASET EKSTRAKOMTABEL MUTASI SESUAI BAST / ' + yearLabel,
                    '', '', '', '', '', '', '', '',
                    '', '', '', '',
                    'RUANG /\nPEMEGANG',
                    'SKPD / OPD MITRA MUTASI', '', '',
                    'Pengurus Barang / Pejabat RSUD', '',
                    'KET.'
                ],
                [
                    '',
                    'Jenis Aset (PMDN 108)', '',
                    'Sub Rincian Objek (PMDN 108)', '',
                    'JUMLAH ANGGARAN (Rp)',
                    'NILAI PEROLEHAN BMD (Rp)',
                    'NAMA BARANG\n(Uraian Sub Sub Rincian Objek PMDN 108)',
                    'Kode Barang\n(Kode Sub Sub Rincian Objek PMDN 108)',
                    'Merk', 'Type', 'Ukuran',
                    'No. Pabrik', 'Bahan', 'Tahun Perolehan',
                    'Dokumen BAST Mutasi', '',
                    'Kondisi\n(B,KB,RB)',
                    'VOLUME', '',
                    'Nilai Satuan (Rp)', 'Total Nilai Barang (Rp)',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    '',
                    'Kode', 'Nama Jenis Aset',
                    'Kode', 'Nama Uraian Sub Rincian Objek',
                    '', '',
                    '', '',
                    '', '', '',
                    '', '', '',
                    'Nomor', 'Tanggal',
                    '',
                    'Jumlah Barang', 'Nama Satuan Barang',
                    '', '',
                    '',
                    'Nama Instansi Mitra', 'Pejabat Mitra', 'Alamat Mitra',
                    'Nama', 'NIP',
                    ''
                ],
                [
                    '', '', '', '', '', '', '',
                    '', '', '', '', '', '', '', '',
                    '', '',
                    '',
                    '', '',
                    '', '',
                    '',
                    '', '', '',
                    '', '',
                    ''
                ],
                [
                    1, 2, 3, 4, 5, 6, 7, 8, 9, 10,
                    11, 12, 13, 14, 15, 16, 17, 18, 19, 20,
                    21, 22, 23, 24, 25, 26, 27, 28, 29
                ]
            ];

            const extracomGroups = {};
            categories['EXTRACOM'].forEach(item => {
                const info = resolve108Info(item, '1.3.2', 'PERALATAN DAN MESIN (NON-KAPITALISASI)');
                const groupKey = info.subRincianKode;
                if (!extracomGroups[groupKey]) extracomGroups[groupKey] = [];
                extracomGroups[groupKey].push(item);
            });

            let globalExtracomNo = 1;
            let extTotalAnggaran = 0;
            let extTotalRealisasi = 0;
            let extTotalUnit = 0;
            let extTotalNilaiBarang = 0;

            Object.keys(extracomGroups).forEach(groupKey => {
                const groupItems = extracomGroups[groupKey];
                let groupRealisasiTotal = 0;
                groupItems.forEach(it => {
                    groupRealisasiTotal += parseFloat(it.nilai_perolehan || it.total_realisasi_num || 0);
                });

                extTotalRealisasi += groupRealisasiTotal;
                let isFirstRowInGroup = true;

                groupItems.forEach(item => {
                    const info = resolve108Info(item, '1.3.2', 'PERALATAN DAN MESIN (NON-KAPITALISASI)');
                    const details = getMutasiItemDetails(item);
                    const spec = details.spec;
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const totalItemNilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);
                    const valSatuan = qty > 0 ? (totalItemNilai / qty) : totalItemNilai;
                    const thnPerolehan = item.tahun_perolehan || spec.tahun_pembuatan || new Date().getFullYear();

                    extTotalUnit += qty;
                    extTotalNilaiBarang += totalItemNilai;

                    const col1to7 = isFirstRowInGroup ? [
                        globalExtracomNo,
                        info.jenisKode,
                        info.jenisNama,
                        info.subRincianKode,
                        info.subRincianNama,
                        0,
                        groupRealisasiTotal
                    ] : ['', '', '', '', '', 0, 0];

                    if (isFirstRowInGroup) { globalExtracomNo++; isFirstRowInGroup = false; }

                    const rawKondisi = item.kondisi || 'Baik';
                    const kondisiLabel = (rawKondisi === 'B' || rawKondisi === 'Baik') ? 'Baik' 
                                       : (rawKondisi === 'KB' || rawKondisi === 'Kurang Baik' ? 'Kurang Baik' 
                                       : (rawKondisi === 'RB' || rawKondisi === 'Rusak Berat' ? 'Rusak Berat' : rawKondisi));

                    extracomRows.push([
                        ...col1to7,
                        info.namaBarang,
                        info.kodeBarang,
                        spec.merk || item.merk || '-',
                        spec.type || item.type || '-',
                        spec.ukuran || item.ukuran || '-',
                        spec.no_pabrik || item.no_pabrik || '-',
                        spec.bahan || item.bahan || '-',
                        thnPerolehan,
                        details.dokumenNomor,
                        details.dokumenTanggal,
                        kondisiLabel,
                        qty,
                        item.satuan || 'Unit',
                        valSatuan,
                        totalItemNilai,
                        details.ruangPenempatan,
                        details.opdMitra,
                        details.pjMitra,
                        details.alamatMitra,
                        details.pejabatRsudNama,
                        details.pejabatRsudNip,
                        (details.isKeluar ? 'Transfer Keluar ke ' + details.opdMitra : 'Mutasi Masuk dari ' + details.opdMitra)
                    ]);
                });
            });

            // Baris Footer Total Extracom
            const extracomFooterRow = Array(29).fill('');
            extracomFooterRow[0] = 'JUMLAH';
            extracomFooterRow[5] = extTotalAnggaran;
            extracomFooterRow[6] = extTotalRealisasi;
            extracomFooterRow[18] = extTotalUnit;
            extracomFooterRow[21] = extTotalNilaiBarang;
            extracomRows.push(extracomFooterRow);

            const extracomSignStartRow = extracomRows.length;
            const extracomSignRows = buildMutasiSignRows(29, 21, 1);
            extracomSignRows.forEach(r => extracomRows.push(r));

            const wsExtracom = XLSX.utils.aoa_to_sheet(extracomRows);
            wsExtracom['!cols'] = Array(29).fill({ wch: 18 });
            wsExtracom['!cols'][0] = { wch: 6 };
            const wsKibExtracomCols = wsExtracom['!cols'];
            wsKibExtracomCols[1] = { wch: 14 }; wsKibExtracomCols[2] = { wch: 24 };
            wsKibExtracomCols[3] = { wch: 18 }; wsKibExtracomCols[4] = { wch: 28 };
            wsKibExtracomCols[5] = { wch: 20 }; wsKibExtracomCols[6] = { wch: 22 };
            wsKibExtracomCols[7] = { wch: 30 }; wsKibExtracomCols[8] = { wch: 22 };
            wsKibExtracomCols[20] = { wch: 20 }; wsKibExtracomCols[21] = { wch: 24 };
            wsKibExtracomCols[22] = { wch: 24 }; wsKibExtracomCols[23] = { wch: 26 };
            wsKibExtracomCols[24] = { wch: 22 }; wsKibExtracomCols[25] = { wch: 26 };
            wsKibExtracomCols[26] = { wch: 22 }; wsKibExtracomCols[27] = { wch: 22 };
            wsKibExtracomCols[28] = { wch: 26 };

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
            XLSX.utils.book_append_sheet(wb, wsExtracom, filterCat === 'all' ? '7. Extracom' : 'Extracom - Barang Ekstrakomtabel');
        }

        // Simpan File Excel
        const fileName = `BUKU_ASET_MUTASI_EKSTERNAL_RSDK_${arahSlug}_${catSlug}_${yearLabel}_${twSlug}.xlsx`;
        XLSX.writeFile(wb, fileName);
        setTimeout(() => { isExportingMutasi = false; }, 1500);
    }

    // Ekspor ke window global untuk modul Mutasi Eksternal & ASTAP
    window.exportMutasiEksternalToExcel = exportMutasiEksternalToExcel;
    window.resolveItemCategory = resolveItemCategory;
})();
</script>
