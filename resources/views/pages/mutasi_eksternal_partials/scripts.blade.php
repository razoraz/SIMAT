<script>
    function mutasiEksternalCatalog() {
        return {
            userRole: {{ Js::from(Auth::user()?->role ?? 'admin') }},
            userName: {{ Js::from(Auth::user()?->name ?? 'Admin') }},

            searchQuery: '',
            statusFilter: 'all',
            jenisFilter: 'all',
            categoryFilter: 'all',
            showDetailModal: false,
            selectedMutasi: null,
            showPrintModal: false,
            showEditForm: false,
            printDoc: null,

            getQrCodeSvg(text) {
                if (typeof window.getQrCodeSvg === 'function') {
                    return window.getQrCodeSvg(text);
                }
                return 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(text || '');
            },

            mutasiEksternals: {{ Js::from($mutasiEksternals) }},

            get filteredMutasis() {
                const query = (this.searchQuery || '').toLowerCase().trim();

                return this.mutasiEksternals.filter(item => {
                    // Filter Kategori KIB
                    if (this.categoryFilter !== 'all') {
                        if ((item.category || '').toLowerCase() !== this.categoryFilter.toLowerCase()) return false;
                    }

                    // Filter Jenis Transaksi
                    if (this.jenisFilter !== 'all') {
                        if (item.jenis !== this.jenisFilter) return false;
                    }

                    // Filter Status BAST
                    const itemStatus = (item.status || '').toLowerCase();
                    if (this.statusFilter === 'selesai') {
                        if (!itemStatus.includes('selesai') && !itemStatus.includes('disahkan')) return false;
                    } else if (this.statusFilter === 'pinjam_aktif') {
                        if (!itemStatus.includes('peminjaman')) return false;
                    } else if (this.statusFilter === 'menunggu_verifikasi') {
                        if (!itemStatus.includes('menunggu')) return false;
                    }

                    // Search Query (Nama Barang, Kode 108, NIBAR, Tahun, No BAST, OPD)
                    if (query) {
                        const match =
                            (item.nama_barang || '').toLowerCase().includes(query) ||
                            (item.nama || '').toLowerCase().includes(query) ||
                            (item.kode_barang || '').toLowerCase().includes(query) ||
                            (item.kode_108 || '').toLowerCase().includes(query) ||
                            (item.tahun_perolehan || '').toLowerCase().includes(query) ||
                            (item.kode || '').toLowerCase().includes(query) ||
                            (item.opd_asal || '').toLowerCase().includes(query) ||
                            (item.opd_tujuan || '').toLowerCase().includes(query) ||
                            (item.pejabat_opd_tujuan || '').toLowerCase().includes(query) ||
                            (item.nomor_sk_dasar || '').toLowerCase().includes(query) ||
                            (item.category || '').toLowerCase().includes(query) ||
                            (item.jenis || '').toLowerCase().includes(query) ||
                            (item.ruangan_asal || '').toLowerCase().includes(query);
                        if (!match) return false;
                    }

                    return true;
                });
            },

            get countAll() {
                return this.mutasiEksternals.length;
            },

            get totalNominal() {
                return this.mutasiEksternals.reduce((acc, curr) => acc + (parseFloat(curr.nilai_perolehan || curr.total_realisasi_num) || 0), 0);
            },

            get totalUnits() {
                return this.mutasiEksternals.reduce((acc, curr) => acc + (parseInt(curr.jumlah_volume || curr.item_count || 1) || 1), 0);
            },

            get countSkpd() {
                const opds = this.mutasiEksternals.map(m => (m.opd_asal || '').trim()).filter(Boolean);
                return new Set(opds).size;
            },

            get countSelesai() {
                return this.mutasiEksternals.filter(m => (m.status || '').toLowerCase().includes('selesai') || (m.status || '').toLowerCase().includes('disahkan')).length;
            },

            get countPinjamAktif() {
                return this.mutasiEksternals.filter(m => (m.status || '').toLowerCase().includes('peminjaman')).length;
            },

            get countMenunggu() {
                return this.mutasiEksternals.filter(m => (m.status || '').toLowerCase().includes('menunggu')).length;
            },

            get countTransfer() {
                return this.mutasiEksternals.filter(m => m.jenis === 'Transfer Antar-OPD').length;
            },

            get countBpkad() {
                return this.mutasiEksternals.filter(m => m.jenis === 'Penyerahan ke BPKAD').length;
            },

            formatRupiah(val) {
                return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
            },

            openDetail(item) {
                this.selectedMutasi = item;
                this.showDetailModal = true;
            },

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
                    baik,
                    kurang_baik: kb,
                    rusak_ringan: rr,
                    rusak_berat: rb,
                    pct_baik,
                    pct_kb,
                    pct_rr,
                    pct_rb,
                    kondisi_dominan: dominan,
                    is_multi: !isSingle,
                    text,
                    badge_class: badgeClass,
                    dot_class: dotClass
                };
            },

            resetFilters() {
                this.searchQuery = '';
                this.statusFilter = 'all';
                this.jenisFilter = 'all';
                this.categoryFilter = 'all';
            },

            openReklas(item) {
                if (!item) return;
                const searchKey = item.kode_barang || item.nama_murni || item.nama || item.kode || '';
                window.location.href = `/astap?search=${encodeURIComponent(searchKey)}&open_reklas=${item.id}`;
            },

            openPrintModal(item) {
                if (!item) return;

                const hariMap = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const bulanMap = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                let dateObj = new Date();
                if (item.tgl_raw) {
                    const parsed = new Date(item.tgl_raw);
                    if (!isNaN(parsed.getTime())) dateObj = parsed;
                } else if (item.tgl) {
                    const parts = String(item.tgl).split('/');
                    if (parts.length === 3) {
                        const d = parseInt(parts[0], 10);
                        const m = parseInt(parts[1], 10) - 1;
                        const y = parseInt(parts[2], 10);
                        dateObj = new Date(y, m, d);
                    }
                }

                const hari = hariMap[dateObj.getDay()] || 'Selasa';
                const tglAngka = dateObj.getDate();
                const bulan = bulanMap[dateObj.getMonth() + 1] || 'September';
                const tahun = dateObj.getFullYear();

                const id = item.mutasi_id || item.id || 1;
                const nomorBast = item.kode || `000.2.3.2/PLP-${String(id).padStart(3, '0')}/430.10.7/${tahun}`;

                let itemsList = [];
                if (item.items && Array.isArray(item.items) && item.items.length > 0) {
                    itemsList = item.items.map((it, idx) => ({
                        no: idx + 1,
                        nama_barang: it.nama_barang || item.nama_barang || item.nama || 'Barang Milik Daerah',
                        spesifikasi: it.spesifikasi || it.merk || item.keterangan || '-',
                        nibar: it.nibar || item.kode_barang || '-',
                        kode_108: it.kode_108 || item.kode_108 || '-',
                        volume: it.volume || it.qty || 1,
                        satuan: it.satuan || item.satuan || 'Unit',
                        kondisi: it.kondisi || item.kondisi || 'Baik',
                        harga_satuan: it.harga_satuan || item.harga_satuan || 0,
                        nilai_total: it.nilai_total || (it.volume ? (it.volume * (it.harga_satuan || item.harga_satuan || 0)) : (item.nilai_perolehan || 0))
                    }));
                } else {
                    const vol = parseInt(item.jumlah_volume || 1, 10);
                    const nilai = parseFloat(item.nilai_perolehan || item.total_realisasi_num || 0);
                    itemsList = [{
                        no: 1,
                        nama_barang: item.nama_barang || item.nama || 'Barang Milik Daerah',
                        spesifikasi: item.keterangan || item.spesifikasi || '-',
                        nibar: item.kode_barang || '-',
                        kode_108: item.kode_108 || '-',
                        volume: vol,
                        satuan: item.satuan || 'Unit',
                        kondisi: item.kondisi || 'Baik',
                        harga_satuan: item.harga_satuan || (vol > 0 ? (nilai / vol) : nilai),
                        nilai_total: nilai
                    }];
                }

                this.printDoc = {
                    ...item,
                    nomor_bast: nomorBast,
                    hari: hari,
                    tgl_angka: tglAngka,
                    bulan: bulan,
                    tahun: tahun,
                    tahun_anggaran: String(item.tahun_perolehan || tahun),
                    tgl_bast: `${tglAngka} ${bulan} ${tahun}`,

                    // Pihak Kesatu (Yang Menyerahkan / SKPD Pengirim)
                    opd_asal: item.opd_asal || 'Dinas Kesehatan Kabupaten Bondowoso',
                    pj_asal_nama: item.pj_asal_nama || 'Pejabat Penyerah SKPD Pengirim',
                    pj_asal_nip: item.pj_asal_nip || '-',
                    pj_asal_jabatan: item.pj_asal_jabatan || 'Pengurus Barang / PPK Asal',

                    // Pihak Kedua (Yang Menerima / RSUD Dr. H. Koesnandi)
                    opd_tujuan: 'RSUD dr. H. Koesnandi Kabupaten Bondowoso',
                    pj_tujuan_nama: item.pejabat_opd_tujuan || 'BUDI HARTONO, S.Sos',
                    pj_tujuan_nip: item.nip_pejabat_opd_tujuan || '19760229 200801 1 010',
                    pj_tujuan_jabatan: item.jabatan_opd_tujuan || 'Pengurus Barang Aset RSUD dr. H. Koesnandi',

                    // Pejabat Pengesah (Direktur RSUD)
                    direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                    direktur_nip: '19730514 200212 2 003',
                    direktur_jabatan: 'Direktur RSUD dr. H. Koesnandi',

                    signed: true,
                    qr_hash: `BSRE-KOESNANDI-PLP-${id}-${tahun}`,
                    items: itemsList
                };

                this.showPrintModal = true;
                this.showEditForm = false;
            },

            toggleSign(doc) {
                if (!doc) return;
                doc.signed = !doc.signed;
                if (doc.signed) {
                    doc.qr_hash = doc.qr_hash || `BSRE-KOESNANDI-PLP-${doc.id || 1}-${doc.tahun || 2026}`;
                    if (typeof window.showSimatToast === 'function') {
                        window.showSimatToast('✍️ BAST Pelimpahan berhasil disahkan secara digital (BSrE Aktif)!', 'success');
                    } else {
                        alert('✍️ BAST Pelimpahan berhasil disahkan secara digital (BSrE Aktif)!');
                    }
                } else {
                    if (typeof window.showSimatToast === 'function') {
                        window.showSimatToast('↩️ Tanda tangan digital BSrE berhasil dibatalkan.', 'info');
                    } else {
                        alert('↩️ Tanda tangan digital BSrE berhasil dibatalkan.');
                    }
                }
            },

            printCurrent() {
                const el = document.getElementById('print-area-bast-eksternal');
                if (!el) {
                    window.print();
                    return;
                }

                let iframe = document.getElementById('simat-print-frame');
                if (iframe) {
                    iframe.remove();
                }

                iframe = document.createElement('iframe');
                iframe.id = 'simat-print-frame';
                iframe.style.position = 'fixed';
                iframe.style.right = '0';
                iframe.style.bottom = '0';
                iframe.style.width = '0';
                iframe.style.height = '0';
                iframe.style.border = '0';
                document.body.appendChild(iframe);

                const headStyles = Array.from(document.querySelectorAll('link[rel="stylesheet"], style'))
                    .map(elem => elem.outerHTML)
                    .join('\n');

                const doc = iframe.contentWindow.document;
                doc.open();
                doc.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>BAST Pelimpahan BMD - ${this.printDoc?.nomor_bast || 'RSUD Dr. H. Koesnandi'}</title>
    ${headStyles}
    <style>
        @page {
            size: auto;
            margin: 12mm 15mm 12mm 15mm;
        }
        *, *::before, *::after {
            box-sizing: border-box !important;
        }
        html, body {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #ffffff !important;
            color: #000000 !important;
            font-family: Arial, "Helvetica Neue", Helvetica, sans-serif !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }
        .print-container {
            width: 100% !important;
            max-width: 100% !important;
            margin: 0 auto !important;
            padding: 0 !important;
        }
        table {
            border-collapse: collapse !important;
            width: 100% !important;
            max-width: 100% !important;
            table-layout: fixed !important;
        }
        th, td {
            border: 1px solid #000000 !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
        }
        tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
        .kop-section, .ttd-section {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }
    </style>
</head>
<body style="background:#ffffff; color:#000000; padding:0; margin:0;">
    <div style="width:100%; max-width:100%;">
        ${el.innerHTML}
    </div>
</body>
</html>`);
                doc.close();

                setTimeout(() => {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                }, 350);
            },

            cetakBast(item) {
                if (!item) return;
                this.openPrintModal(item);
            },

            deleteMutasi(item) {
                if (!item) return;
                const namaAset = item.nama_murni || item.nama || 'Aset';
                if (!confirm(`⚠️ Apakah Anda yakin ingin memindahkan data pelimpahan "${namaAset}" ke Recycle Bin (Tong Sampah)?\n\nSeluruh unit register NIBAR terkait juga akan dipindahkan ke Recycle Bin.`)) {
                    return;
                }

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const id = item.mutasi_id || item.id;
                fetch(`/mutasi-eksternal/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(d => {
                    if (d.success !== false) {
                        this.mutasiEksternals = this.mutasiEksternals.filter(m => Number(m.id) !== Number(item.id) && Number(m.mutasi_id) !== Number(item.mutasi_id));
                        if (this.selectedMutasi && (Number(this.selectedMutasi.id) === Number(item.id) || Number(this.selectedMutasi.mutasi_id) === Number(item.mutasi_id))) {
                            this.showDetailModal = false;
                            this.selectedMutasi = null;
                        }
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast(d.message || 'Data pelimpahan aset berhasil dipindahkan ke Tong Sampah.', 'success');
                        } else {
                            alert('✓ Data pelimpahan aset berhasil dipindahkan ke Tong Sampah.');
                        }
                    } else {
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast('Gagal menghapus: ' + (d.message || 'Terjadi kesalahan.'), 'error');
                        } else {
                            alert('❌ Gagal menghapus: ' + (d.message || 'Terjadi kesalahan.'));
                        }
                    }
                })
                .catch(err => {
                    console.error('Delete error:', err);
                    alert('❌ Terjadi kesalahan jaringan saat mencoba menghapus data.');
                });
            }
        };
    }
</script>
