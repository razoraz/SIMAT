<!-- SCRIPTS: LOGIKA STATE MANAGEMENT & EKSPOR EXCEL MULTI-SHEET HIBAH -->
<script>
    window.masterHibahRecords = @json($hibahRecords ?? []);

    function masterHibah() {
        return {
            // Master Datasets from Controller
            hibahList: @json($hibahRecords ?? []),
            activeAstaps: @json($activeAstaps ?? []),
            availableYears: @json($availableYears ?? [date('Y')]),

            // UI Filters
            activeTab: '{{ $filterTipe ?? "all" }}',
            selectedYear: '{{ $filterTahun ?? "all" }}',
            selectedTw: '{{ $filterTw ?? "all" }}',
            searchQuery: '{{ $search ?? "" }}',

            // Modal States
            showModalDetail: false,
            selectedDetail: null,
            detailActiveTab: 'bast',

            // Global Toast Notification State
            toast: {
                show: false,
                message: '',
                type: 'success'
            },

            // Cetak BAST States
            showModalPrintBast: false,
            printBastDoc: null,
            showEditBastForm: false,

            // Modal Konfirmasi Bespoke (z-[60] di atas modal form)
            showConfirmKeluarModal: false,
            showConfirmDeleteModal: false,
            itemToDelete: null,
            isDeleting: false,

            showModalHibahKeluar: false,
            searchAsetKeluar: '',
            isAsetKeluarDropdownOpen: false,
            selectedAstapForKeluar: null,
            isSubmittingKeluar: false,
            keluarData: {
                astap_id: '',
                register_ids: [],
                penerima_hibah: '',
                nomor_bast: '',
                tanggal_bast: new Date().toISOString().split('T')[0],
                nilai_aset: 0,
                tahun: new Date().getFullYear(),
                triwulan: 'TW I',
                keterangan: ''
            },

            // Export Excel States
            showModalExport: false,
            isExporting: false,
            exportConfig: {
                format: 'all', // 'all', 'masuk', 'keluar'
                tahun: new Date().getFullYear(),
                triwulan: 'all',
                category: 'all',
                ppkNama: 'dr. YUS PRIYATNA, Sp.P',
                ppkNip: '19760815 200501 1 009',
                tanggalCetak: new Date().toISOString().split('T')[0]
            },

            init() {
                window.showSimatToast = (msg, type = 'success') => {
                    this.showToast(msg, type);
                };

                // Set auto triwulan based on current month
                const m = new Date().getMonth() + 1;
                if (m >= 1 && m <= 3) this.keluarData.triwulan = 'TW I';
                else if (m >= 4 && m <= 6) this.keluarData.triwulan = 'TW II';
                else if (m >= 7 && m <= 9) this.keluarData.triwulan = 'TW III';
                else this.keluarData.triwulan = 'TW IV';
            },

            // =========================================================================
            // COMPUTED & FILTERING
            // =========================================================================
            get filteredHibahList() {
                let list = this.hibahList || [];

                // 1. Filter Tab (all, masuk, keluar)
                if (this.activeTab !== 'all') {
                    list = list.filter(h => h.tipe_hibah === this.activeTab);
                }

                // 2. Filter Tahun
                if (this.selectedYear !== 'all') {
                    list = list.filter(h => String(h.tahun) === String(this.selectedYear));
                }

                // 3. Filter Triwulan
                if (this.selectedTw !== 'all') {
                    list = list.filter(h => h.triwulan === this.selectedTw);
                }

                // 4. Search Query
                if (this.searchQuery && this.searchQuery.trim() !== '') {
                    const q = this.searchQuery.trim().toLowerCase();
                    list = list.filter(h => {
                        const nama = (h.astap?.nama_barang || h.nama_barang || '').toLowerCase();
                        const kode = (h.astap?.kode_108 || h.kode_108 || '').toLowerCase();
                        const pihak = (h.pihak_hibah || '').toLowerCase();
                        const bast = (h.nomor_bast || '').toLowerCase();
                        const ket = (h.keterangan || '').toLowerCase();
                        return nama.includes(q) || kode.includes(q) || pihak.includes(q) || bast.includes(q) || ket.includes(q);
                    });
                }

                return list;
            },

            get computedFilteredTotal() {
                return this.filteredHibahList.reduce((acc, h) => acc + (parseFloat(h.nilai_aset) || 0), 0);
            },

            get filteredActiveAstaps() {
                if (!this.searchAsetKeluar || this.searchAsetKeluar.trim() === '') {
                    return (this.activeAstaps || []).slice(0, 30);
                }
                const q = this.searchAsetKeluar.trim().toLowerCase();
                return (this.activeAstaps || []).filter(a => 
                    (a.nama_barang || '').toLowerCase().includes(q) ||
                    (a.kode_barang || '').toLowerCase().includes(q)
                ).slice(0, 30);
            },

            resetFilter() {
                this.activeTab = 'all';
                this.selectedYear = 'all';
                this.selectedTw = 'all';
                this.searchQuery = '';
            },

            formatRupiah(val) {
                const num = parseFloat(val) || 0;
                return 'Rp ' + Math.round(num).toLocaleString('id-ID');
            },

            formatTanggalIndo(dateStr) {
                if (!dateStr) return '-';
                try {
                    const d = new Date(dateStr);
                    if (isNaN(d.getTime())) return dateStr;
                    return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                } catch (e) {
                    return dateStr;
                }
            },

            // Toast Helper
            showToast(message, type = 'success') {
                this.toast.message = message;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => {
                    this.toast.show = false;
                }, 4000);
            },

            // Kategori KIB Helper (KIB A - F / ATB)
            getEffectiveKibCategory(item) {
                if (!item) return 'KIB B';
                const astap = item.astap || item;
                let cat = String(astap.category || '').toUpperCase().trim();
                if (cat === 'KIB A' || cat === 'KIB B' || cat === 'KIB C' || cat === 'KIB D' || cat === 'KIB E' || cat === 'KIB F' || cat === 'ATB') {
                    return cat;
                }

                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }

                const kibSpec = String(spec?.kategori_kib || '').toUpperCase();
                if (kibSpec.includes('KIB A') || kibSpec.includes('TANAH')) return 'KIB A';
                if (kibSpec.includes('KIB B') || kibSpec.includes('MESIN') || kibSpec.includes('PERALATAN')) return 'KIB B';
                if (kibSpec.includes('KIB C') || kibSpec.includes('GEDUNG') || kibSpec.includes('BANGUNAN')) return 'KIB C';
                if (kibSpec.includes('KIB D') || kibSpec.includes('JARINGAN') || kibSpec.includes('JALAN') || kibSpec.includes('IRIGASI')) return 'KIB D';
                if (kibSpec.includes('KIB E') || kibSpec.includes('LAINNYA') || kibSpec.includes('BUKU')) return 'KIB E';
                if (kibSpec.includes('KIB F') || kibSpec.includes('KONSTRUKSI') || kibSpec.includes('KDP')) return 'KIB F';
                if (kibSpec.includes('ATB') || kibSpec.includes('BERWUJUD') || kibSpec.includes('SOFTWARE')) return 'ATB';

                const kd = String(astap.kode_108 || item.kode_108 || astap.jenis_astap?.sub_sub_rincian_objek || '');
                if (kd.startsWith('1.3.1')) return 'KIB A';
                if (kd.startsWith('1.3.2')) return 'KIB B';
                if (kd.startsWith('1.3.3')) return 'KIB C';
                if (kd.startsWith('1.3.4')) return 'KIB D';
                if (kd.startsWith('1.3.5')) return 'KIB E';
                if (kd.startsWith('1.3.6')) return 'KIB F';
                if (kd.startsWith('1.5.3') || kd.startsWith('1.5')) return 'ATB';

                const nama = String(astap.nama_barang || item.nama_barang || '').toLowerCase();
                if (nama.includes('tanah') || nama.includes('lahan')) return 'KIB A';
                if (nama.includes('gedung') || nama.includes('bangunan') || nama.includes('ruang') || nama.includes('paviliun')) return 'KIB C';
                if (nama.includes('jalan') || nama.includes('jaringan') || nama.includes('irigasi') || nama.includes('pipa')) return 'KIB D';
                if (nama.includes('buku') || nama.includes('hewan') || nama.includes('kesenian') || nama.includes('tanaman')) return 'KIB E';
                if (nama.includes('aplikasi') || nama.includes('software') || nama.includes('lisensi')) return 'ATB';

                return 'KIB B';
            },

            // Hitung statistik kondisi aset terdaftar (Standar 3 Kondisi: Baik, Kurang Baik, Rusak Berat)
            getKondisiStats(item) {
                if (!item) return { total: 0, baik: 0, kurang_baik: 0, rusak_berat: 0, pct_baik: 100, pct_kb: 0, pct_rb: 0, kondisi_dominan: 'Baik', is_multi: false, text: 'Baik (100%)', badge_class: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30', dot_class: 'bg-emerald-400' };
                
                const regs = (item.astap && Array.isArray(item.astap.registers) && item.astap.registers.length > 0)
                    ? item.astap.registers
                    : (item.register ? [item.register] : []);
                
                const total = regs.length;
                if (total === 0) {
                    const k = item.kondisi || item.astap?.kondisi || 'Baik';
                    const isKb = k === 'Kurang Baik' || k === 'KB' || k === 'Rusak Ringan' || k === 'RR';
                    const isRb = k === 'Rusak Berat' || k === 'RB' || k === 'Rusak';
                    const dominan = isKb ? 'Kurang Baik' : (isRb ? 'Rusak Berat' : 'Baik');
                    const badgeClass = dominan === 'Baik' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : (dominan === 'Kurang Baik' ? 'bg-amber-500/15 text-amber-300 border-amber-500/30' : 'bg-rose-500/15 text-rose-300 border-rose-500/30');
                    const dotClass = dominan === 'Baik' ? 'bg-emerald-400' : (dominan === 'Kurang Baik' ? 'bg-amber-400' : 'bg-rose-400');
                    return {
                        total: 1,
                        baik: dominan === 'Baik' ? 1 : 0,
                        kurang_baik: isKb ? 1 : 0,
                        rusak_berat: isRb ? 1 : 0,
                        pct_baik: dominan === 'Baik' ? 100 : 0,
                        pct_kb: isKb ? 100 : 0,
                        pct_rb: isRb ? 100 : 0,
                        kondisi_dominan: dominan,
                        is_multi: false,
                        text: dominan + ' (100%)',
                        badge_class: badgeClass,
                        dot_class: dotClass
                    };
                }

                const baik = regs.filter(r => (r.kondisi || 'Baik') === 'Baik' || r.kondisi === 'B').length;
                const kb   = regs.filter(r => r.kondisi === 'Kurang Baik' || r.kondisi === 'KB' || r.kondisi === 'Rusak Ringan' || r.kondisi === 'RR').length;
                const rb   = regs.filter(r => r.kondisi === 'Rusak Berat' || r.kondisi === 'RB' || r.kondisi === 'Rusak').length;
                const dominan = (baik >= kb && baik >= rb) ? 'Baik' : ((kb >= rb) ? 'Kurang Baik' : 'Rusak Berat');
                const isSingle = (baik === total) || (kb === total) || (rb === total);

                const pct_baik = Math.round((baik / total) * 100);
                const pct_kb   = Math.round((kb   / total) * 100);
                const pct_rb   = Math.round((rb   / total) * 100);

                let parts = [];
                if (baik > 0) parts.push(`${pct_baik}% Baik (${baik}/${total})`);
                if (kb > 0)   parts.push(`${pct_kb}% Kurang Baik (${kb}/${total})`);
                if (rb > 0)   parts.push(`${pct_rb}% Rusak Berat (${rb}/${total})`);

                let text = parts.join(' • ');
                if (isSingle) {
                    if (baik === total) text = total > 1 ? `Baik (${total} Aset)` : 'Baik';
                    else if (kb === total) text = total > 1 ? `Kurang Baik (${total} Aset)` : 'Kurang Baik';
                    else if (rb === total) text = total > 1 ? `Rusak Berat (${total} Aset)` : 'Rusak Berat';
                }

                let badgeClass = 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30';
                if (rb > 0 && rb >= baik && rb >= kb) {
                    badgeClass = 'bg-rose-500/15 text-rose-300 border-rose-500/30';
                } else if (kb > 0 && kb >= baik) {
                    badgeClass = 'bg-amber-500/15 text-amber-300 border-amber-500/30';
                } else if (!isSingle) {
                    badgeClass = 'bg-amber-500/15 text-amber-300 border-amber-500/30';
                }

                let dotClass = pct_baik === 100 ? 'bg-emerald-400' : (pct_rb > 0 ? 'bg-rose-400' : 'bg-amber-400');

                return {
                    total,
                    baik, kurang_baik: kb, rusak_berat: rb,
                    pct_baik, pct_kb, pct_rb,
                    kondisi_dominan: dominan,
                    is_multi: !isSingle,
                    parts,
                    text,
                    badge_class: badgeClass,
                    dot_class: dotClass
                };
            },

            // Label Ruangan / Penempatan Unit
            getRuangLabel(item) {
                if (!item) return 'Gudang Aset';
                if (item.tipe_hibah === 'keluar') {
                    return 'Diserahkan ke Luar RSUD';
                }
                if (item.astap && item.astap.unit && item.astap.unit.nama) {
                    return item.astap.unit.nama;
                }
                if (item.register && item.register.unit && item.register.unit.nama) {
                    return item.register.unit.nama;
                }
                if (item.astap && Array.isArray(item.astap.registers) && item.astap.registers.length > 0) {
                    const r0 = item.astap.registers[0];
                    if (r0.unit && r0.unit.nama) return r0.unit.nama;
                    if (r0.ruang_pemegang && r0.ruang_pemegang !== '-' && !r0.ruang_pemegang.includes('Belum Ditempatkan')) {
                        return r0.ruang_pemegang;
                    }
                }
                if (item.register && item.register.ruang_pemegang && item.register.ruang_pemegang !== '-' && !item.register.ruang_pemegang.includes('Belum Ditempatkan')) {
                    return item.register.ruang_pemegang;
                }
                return 'Gudang Aset RSUD';
            },

            // Ekstrak Spesifikasi Fisik KIB
            getSpecDetail(item) {
                if (!item) return {};
                let spec = item.astap?.spesifikasi_json || {};
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                return {
                    merk: spec.merk || spec.mesin_merk || item.astap?.merk_type || '',
                    type: spec.type || spec.mesin_type || '',
                    ukuran: spec.ukuran || spec.ukuran_cc || spec.mesin_ukuran_cc || '',
                    bahan: spec.bahan || spec.mesin_bahan || '',
                    no_pabrik: spec.no_pabrik || spec.mesin_no_pabrik || '',
                    no_rangka: spec.no_rangka || spec.mesin_no_rangka || '',
                    no_mesin: spec.no_mesin || spec.mesin_no_mesin || '',
                    no_polisi: spec.no_polisi || spec.mesin_no_polisi || '',
                    no_bpkb: spec.no_bpkb || spec.mesin_no_bpkb || '',
                    tanah_luas_m2: spec.tanah_luas_m2 || spec.luas_m2 || '',
                    tanah_sertifikat_no: spec.tanah_sertifikat_no || spec.sertifikat_no || '',
                    gedung_luas_m2: spec.gedung_luas_m2 || spec.luas_lantai_m2 || '',
                    gedung_bertingkat: spec.gedung_bertingkat || spec.konstruksi_bertingkat || '',
                    gedung_beton: spec.gedung_beton || spec.konstruksi_beton || ''
                };
            },

            // Daftar Register NIBAR
            getRegistersList(item) {
                if (!item) return [];
                let regs = [];
                if (item.astap && Array.isArray(item.astap.registers) && item.astap.registers.length > 0) {
                    regs = item.astap.registers;
                } else if (item.register) {
                    regs = [item.register];
                } else {
                    const vol = parseInt(item.jumlah_volume) || 1;
                    for (let i = 1; i <= vol; i++) {
                        regs.push({
                            id: i,
                            nibar: '-',
                            no_register: i,
                            ruang_pemegang: this.getRuangLabel(item),
                            kondisi: 'Baik',
                            status: item.tipe_hibah === 'keluar' ? 'Dihibahkan' : 'Tersedia'
                        });
                    }
                }
                return regs;
            },

            getQrCodeSvg(text) {
                if (typeof window.getQrCodeSvg === 'function') {
                    return window.getQrCodeSvg(text);
                }
                return 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(text || '');
            },

            // =========================================================================
            // CETAK BAST HIBAH ASET (RESMI)
            // =========================================================================
            openPrintBast(item) {
                if (!item) return;

                const hariMap = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                const bulanMap = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                let dateObj = new Date();
                if (item.tanggal_bast) {
                    const parsed = new Date(item.tanggal_bast);
                    if (!isNaN(parsed.getTime())) dateObj = parsed;
                }

                const hari = hariMap[dateObj.getDay()] || 'Selasa';
                const tglAngka = String(dateObj.getDate()).padStart(2, '0');
                const bulan = bulanMap[dateObj.getMonth() + 1] || 'September';
                const tahun = dateObj.getFullYear() || item.tahun || 2026;

                const id = item.id || 1;
                const nomorBast = item.nomor_bast || `000.2.3.2/BAST-HB-${String(id).padStart(3, '0')}/430.10.7/${tahun}`;

                const namaBarang = item.astap ? item.astap.nama_barang : (item.nama_barang || 'Barang Hibah Aset Daerah');
                const kode108 = item.astap?.kode_108 || item.kode_108 || (item.astap?.jenis_astap?.sub_sub_rincian_objek || '1.3.2.00.00.00');
                const satuan = item.satuan || item.astap?.satuan || 'Unit';
                const vol = parseInt(item.jumlah_volume || 1);
                const nilaiTotal = parseFloat(item.nilai_aset || 0);
                const hargaSat = vol > 0 ? (nilaiTotal / vol) : nilaiTotal;

                let itemsList = [];
                if (item.register) {
                    itemsList.push({
                        nama: namaBarang,
                        kode_108: kode108,
                        nibar: item.register.nibar || item.register.no_register || '-',
                        volume: 1,
                        satuan: satuan,
                        kondisi: item.register.kondisi || 'Baik',
                        harga_satuan: hargaSat,
                        nilai_total: hargaSat,
                    });
                } else if (item.astap && item.astap.registers && Array.isArray(item.astap.registers) && item.astap.registers.length > 0) {
                    const regs = item.astap.registers.slice(0, vol);
                    regs.forEach((r) => {
                        itemsList.push({
                            nama: namaBarang,
                            kode_108: kode108,
                            nibar: r.nibar || r.no_register || '-',
                            volume: 1,
                            satuan: satuan,
                            kondisi: r.kondisi || 'Baik',
                            harga_satuan: hargaSat,
                            nilai_total: hargaSat,
                        });
                    });
                }

                if (itemsList.length === 0) {
                    itemsList.push({
                        nama: namaBarang,
                        kode_108: kode108,
                        nibar: '-',
                        volume: vol,
                        satuan: satuan,
                        kondisi: 'Baik',
                        harga_satuan: hargaSat,
                        nilai_total: nilaiTotal,
                    });
                }

                const isMasuk = item.tipe_hibah === 'masuk';

                this.printBastDoc = {
                    id: item.id,
                    tipe_hibah: item.tipe_hibah || 'masuk',
                    nomor_bast: nomorBast,
                    hari: hari,
                    tgl_angka: tglAngka,
                    bulan: bulan,
                    tahun: tahun,
                    jumlah_volume: vol,
                    satuan: satuan,
                    nilai_aset: nilaiTotal,
                    keterangan: item.keterangan || '',

                    // Pihak 1 (Yang Menyerahkan)
                    pihak_1_nama: isMasuk ? (item.pihak_hibah || 'Pemberi Hibah') : 'BUDI HARTONO, S.Sos',
                    pihak_1_nip: isMasuk ? '-' : '19760229 200801 1 010',
                    pihak_1_jabatan: isMasuk ? 'Pemberi Hibah' : 'Pengurus Barang Pengguna Aset',
                    pihak_1_instansi: isMasuk ? (item.pihak_hibah || '-') : 'RSUD dr. H. Koesnadi Kabupaten Bondowoso',

                    // Pihak 2 (Yang Menerima)
                    pihak_2_nama: isMasuk ? 'BUDI HARTONO, S.Sos' : (item.pihak_hibah || 'Penerima Hibah'),
                    pihak_2_nip: isMasuk ? '19760229 200801 1 010' : '-',
                    pihak_2_jabatan: isMasuk ? 'Pengurus Barang Pengguna Aset' : 'Penerima Hibah',
                    pihak_2_instansi: isMasuk ? 'RSUD dr. H. Koesnadi Kabupaten Bondowoso' : (item.pihak_hibah || '-'),

                    // Mengetahui Direktur
                    direktur_nama: 'dr. DIAN ARISANDI, M.Kes',
                    direktur_nip: '19730514 200212 2 003',
                    direktur_jabatan: 'Direktur RSUD dr. H. Koesnadi',

                    signed: true,
                    qr_hash: `BSRE-KOESNANDI-HIBAH-${id}-${tahun}`,
                    items: itemsList,
                };

                this.showModalPrintBast = true;
                this.showEditBastForm = false;
            },

            toggleSignBast() {
                if (!this.printBastDoc) return;
                this.printBastDoc.signed = !this.printBastDoc.signed;
                if (this.printBastDoc.signed) {
                    if (typeof window.showSimatToast === 'function') {
                        window.showSimatToast('✍️ BAST Hibah berhasil disahkan secara elektronik (BSrE Aktif)!', 'success');
                    }
                } else {
                    if (typeof window.showSimatToast === 'function') {
                        window.showSimatToast('↩️ Tanda tangan elektronik BSrE dinonaktifkan.', 'info');
                    }
                }
            },

            printCurrentBast() {
                const el = document.getElementById('print-area-bast-hibah');
                if (!el) {
                    window.print();
                    return;
                }

                let iframe = document.getElementById('simat-hibah-print-frame');
                if (iframe) {
                    iframe.remove();
                }

                iframe = document.createElement('iframe');
                iframe.id = 'simat-hibah-print-frame';
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
    <title>BAST Hibah Aset - ${this.printBastDoc?.nomor_bast || 'RSUD Dr. H. Koesnadi'}</title>
    ${headStyles}
    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm 12mm 15mm;
        }
        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .no-print { display: none !important; }
    </style>
</head>
<body style="background:#ffffff; color:#000000;">
    ${el.outerHTML}
</body>
</html>`);
                doc.close();

                setTimeout(() => {
                    iframe.contentWindow.focus();
                    iframe.contentWindow.print();
                }, 400);
            },

            // =========================================================================
            // MODAL DETAIL
            // =========================================================================
            openDetail(item) {
                this.selectedDetail = item;
                this.detailActiveTab = 'bast';
                this.showModalDetail = true;
            },

            // =========================================================================
            // MODAL HIBAH KELUAR
            // =========================================================================
            openModalHibahKeluar() {
                this.selectedAstapForKeluar = null;
                this.searchAsetKeluar = '';
                this.isAsetKeluarDropdownOpen = false;

                const m = new Date().getMonth() + 1;
                let currentTw = 'TW I';
                if (m >= 4 && m <= 6) currentTw = 'TW II';
                else if (m >= 7 && m <= 9) currentTw = 'TW III';
                else if (m >= 10) currentTw = 'TW IV';

                this.keluarData = {
                    astap_id: '',
                    register_ids: [],
                    penerima_hibah: '',
                    nomor_bast: '',
                    tanggal_bast: new Date().toISOString().split('T')[0],
                    nilai_aset: 0,
                    tahun: new Date().getFullYear(),
                    triwulan: currentTw,
                    keterangan: ''
                };
                this.showModalHibahKeluar = true;
            },

            clearAstapSelection() {
                this.selectedAstapForKeluar = null;
                this.searchAsetKeluar = '';
                this.keluarData.astap_id = '';
                this.keluarData.register_ids = [];
                this.keluarData.nilai_aset = 0;
                this.isAsetKeluarDropdownOpen = false;
            },

            get availableRegisters() {
                if (!this.selectedAstapForKeluar || !this.selectedAstapForKeluar.registers) return [];
                return this.selectedAstapForKeluar.registers.filter(r => 
                    r.status === 'Tersedia' && 
                    (!r.ruang || r.ruang === '-' || r.ruang === 'Belum Ditempatkan / Di Gudang' || r.ruang === 'Gudang Aset')
                );
            },

            selectAstapForKeluar(a) {
                this.selectedAstapForKeluar = a;
                this.keluarData.astap_id = a.id;
                this.isAsetKeluarDropdownOpen = false;
                this.searchAsetKeluar = '';

                // Default pilih semua register yang berstatus 'Tersedia' dan belum ditempatkan
                const availableRegs = this.availableRegisters;
                this.keluarData.register_ids = availableRegs.map(r => r.id);
                this.recomputeKeluarValue();
            },

            toggleSelectAllRegisters() {
                const availableRegs = this.availableRegisters;
                if (!availableRegs || availableRegs.length === 0) return;

                if (this.keluarData.register_ids.length === availableRegs.length) {
                    this.keluarData.register_ids = [];
                } else {
                    this.keluarData.register_ids = availableRegs.map(r => r.id);
                }
                this.recomputeKeluarValue();
            },

            recomputeKeluarValue() {
                if (!this.selectedAstapForKeluar) return;
                const availableRegs = this.availableRegisters;
                const totalRegs = (availableRegs && availableRegs.length > 0)
                    ? availableRegs.length
                    : Math.max(1, parseInt(this.selectedAstapForKeluar.jumlah_volume) || 1);
                
                let unitPrice = parseFloat(this.selectedAstapForKeluar.harga_satuan) || 0;
                if (!unitPrice && this.selectedAstapForKeluar.total_realisasi) {
                    unitPrice = (parseFloat(this.selectedAstapForKeluar.total_realisasi) || 0) / totalRegs;
                }
                
                const selectedCount = this.keluarData.register_ids.length > 0 
                    ? this.keluarData.register_ids.length 
                    : totalRegs;
                this.keluarData.nilai_aset = Math.round(unitPrice * selectedCount);
            },

            submitHibahKeluar() {
                if (!this.keluarData.astap_id) {
                    alert('⚠️ Mohon pilih barang inventaris yang akan dihibahkan.');
                    return;
                }
                if (!(this.keluarData.penerima_hibah || '').trim()) {
                    alert('⚠️ Mohon isi instansi / pihak penerima hibah.');
                    return;
                }
                if (!(this.keluarData.nomor_bast || '').trim()) {
                    alert('⚠️ Mohon isi nomor BAST hibah keluar.');
                    return;
                }
                if (!(this.keluarData.tanggal_bast || '').trim()) {
                    alert('⚠️ Mohon tentukan tanggal BAST hibah.');
                    return;
                }

                // Sembunyikan modal form hibah keluar sementara agar tidak menumpuk, lalu buka modal konfirmasi
                this.showModalHibahKeluar = false;
                this.showConfirmKeluarModal = true;
            },

            executeSubmitHibahKeluar() {
                // Normalisasi tanggal_bast jika berformat dd/mm/yyyy menjadi YYYY-MM-DD
                let payload = Object.assign({}, this.keluarData);
                if (payload.tanggal_bast && typeof payload.tanggal_bast === 'string' && payload.tanggal_bast.includes('/')) {
                    const parts = payload.tanggal_bast.split('/');
                    if (parts.length === 3) {
                        payload.tanggal_bast = `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                    }
                }

                this.isSubmittingKeluar = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                fetch("{{ route('master.hibah.keluar') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json().then(data => ({ status: res.status, body: data })))
                .then(result => {
                    this.isSubmittingKeluar = false;
                    if (result.status === 200 && result.body.success) {
                        this.showConfirmKeluarModal = false;
                        this.showModalHibahKeluar = false;
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast(result.body.message, 'success');
                        } else {
                            alert('🎉 ' + result.body.message);
                        }
                        setTimeout(() => window.location.reload(), 600);
                    } else {
                        // Jika ada kesalahan atau validasi gagal, kembalikan tampilan form
                        this.showConfirmKeluarModal = false;
                        this.showModalHibahKeluar = true;

                        let errMsg = result.body.message || 'Terjadi kesalahan saat memproses hibah keluar.';
                        if (result.body.errors) {
                            const errList = Object.values(result.body.errors).flat().join('\n• ');
                            errMsg += '\n\nDetail:\n• ' + errList;
                        }
                        alert('❌ Gagal: ' + errMsg);
                    }
                })
                .catch(err => {
                    this.isSubmittingKeluar = false;
                    this.showConfirmKeluarModal = false;
                    this.showModalHibahKeluar = true;
                    console.error(err);
                    alert('❌ Gagal menghubungi server. Silakan muat ulang halaman dan coba kembali.');
                });
            },

            confirmDelete(item) {
                if (!item) return;
                this.itemToDelete = item;
                this.showConfirmDeleteModal = true;
            },

            executeDeleteHibah() {
                if (!this.itemToDelete) return;
                this.isDeleting = true;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                fetch("{{ url('/master-data/hibah') }}/" + this.itemToDelete.id, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token
                    }
                })
                .then(res => res.json())
                .then(result => {
                    this.isDeleting = false;
                    this.showConfirmDeleteModal = false;
                    if (result.success) {
                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast(result.message || 'Catatan transaksi hibah berhasil dihapus.', 'success');
                        } else {
                            alert('✓ ' + result.message);
                        }
                        this.hibahList = this.hibahList.filter(h => h.id !== this.itemToDelete.id);
                        this.itemToDelete = null;
                    } else {
                        alert('❌ Gagal: ' + (result.message || 'Tidak dapat menghapus transaksi.'));
                    }
                })
                .catch(err => {
                    this.isDeleting = false;
                    console.error(err);
                    alert('❌ Terjadi kesalahan saat menghapus data transaksi.');
                });
            },

            // =========================================================================
            // EKSPOR EXCEL BERSTANDAR PEMKAB BONDOWOSO / HIBAH
            // =========================================================================
            openExportModal() {
                this.exportConfig.tahun = this.selectedYear !== 'all' ? this.selectedYear : (this.availableYears[0] || new Date().getFullYear());
                this.exportConfig.triwulan = this.selectedTw !== 'all' ? this.selectedTw : 'all';
                this.exportConfig.format = 'all';
                this.exportConfig.category = 'all';
                this.showModalExport = true;
            },

            get exportFilteredCount() {
                const rawList = this.hibahList || [];
                const cfg = this.exportConfig;
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

                return rawList.filter(item => {
                    const matchYear = cfg.tahun === 'all' || String(item.tahun) === String(cfg.tahun);
                    const matchTw = isTwMatch(item.triwulan, cfg.triwulan);
                    
                    let matchFormat = true;
                    if (cfg.format === 'masuk') matchFormat = item.tipe_hibah === 'masuk';
                    else if (cfg.format === 'keluar') matchFormat = item.tipe_hibah === 'keluar';

                    let matchCat = true;
                    if (cfg.category && cfg.category !== 'all') {
                        matchCat = this.getEffectiveKibCategory(item) === cfg.category;
                    }

                    return matchYear && matchTw && matchFormat && matchCat;
                }).length;
            },

            submitExportExcel() {
                this.isExporting = true;
                try {
                    if (typeof exportHibahToExcel === 'function') {
                        exportHibahToExcel({
                            year: this.exportConfig.tahun,
                            triwulan: this.exportConfig.triwulan,
                            format: this.exportConfig.format,
                            category: this.exportConfig.category,
                            ppkNama: this.exportConfig.ppkNama,
                            ppkNip: this.exportConfig.ppkNip,
                            tanggalCetak: this.exportConfig.tanggalCetak
                        });
                        setTimeout(() => {
                            this.isExporting = false;
                            this.showModalExport = false;
                            this.showToast('✅ Berhasil mengekspor Laporan Hibah Aset (BMD)!', 'success');
                        }, 1000);
                    } else {
                        this.executeExportExcel();
                    }
                } catch (err) {
                    console.error('Error export hibah:', err);
                    this.isExporting = false;
                    this.showToast('❌ Gagal mengekspor file: ' + (err.message || 'Terjadi kesalahan sistem'), 'error');
                }
            },

            executeExportExcel() {
                this.isExporting = true;

                try {
                    const wb = XLSX.utils.book_new();
                    const cfg = this.exportConfig;
                    const yearLabel = cfg.tahun === 'all' ? 'SEMUA TAHUN' : cfg.tahun;
                    const twLabel = cfg.triwulan === 'all' ? 'TAHUNAN' : cfg.triwulan;

                    // Filter dataset sesuai opsi ekspor
                    let dataToExport = this.hibahList || [];
                    if (cfg.tahun !== 'all') {
                        dataToExport = dataToExport.filter(h => String(h.tahun) === String(cfg.tahun));
                    }
                    if (cfg.triwulan !== 'all') {
                        dataToExport = dataToExport.filter(h => h.triwulan === cfg.triwulan);
                    }

                    const masukList = dataToExport.filter(h => h.tipe_hibah === 'masuk');
                    const keluarList = dataToExport.filter(h => h.tipe_hibah === 'keluar');

                    // =========================================================
                    // SHEET 1: REKAPITULASI REALISASI HIBAH (JIKA FORMAT 'all')
                    // =========================================================
                    if (cfg.format === 'all') {
                        const rekapRows = [];
                        rekapRows.push(["PEMERINTAH KABUPATEN BONDOWOSO"]);
                        rekapRows.push(["RUMAH SAKIT UMUM DAERAH Dr. H. KOESNANDI"]);
                        rekapRows.push(["REKAPITULASI MUTASI HIBAH BARANG MILIK DAERAH (BMD)"]);
                        rekapRows.push([`TAHUN ANGGARAN ${yearLabel} — PERIODE ${twLabel}`]);
                        rekapRows.push([]); // blank

                        rekapRows.push([
                            "NO",
                            "URAIAN KELOMPOK MUTASI HIBAH",
                            "JUMLAH TRANSAKSI",
                            "JUMLAH VOLUME",
                            "TOTAL NILAI (RP)",
                            "KETERANGAN"
                        ]);

                        const totalMasukVol = masukList.reduce((s, h) => s + (h.jumlah_volume || 1), 0);
                        const totalMasukNom = masukList.reduce((s, h) => s + (parseFloat(h.nilai_aset) || 0), 0);
                        const totalKeluarVol = keluarList.reduce((s, h) => s + (h.jumlah_volume || 1), 0);
                        const totalKeluarNom = keluarList.reduce((s, h) => s + (parseFloat(h.nilai_aset) || 0), 0);
                        const nettoVol = totalMasukVol - totalKeluarVol;
                        const nettoNom = totalMasukNom - totalKeluarNom;

                        rekapRows.push([
                            1,
                            "Mutasi Bertambah: Hibah / Bantuan Pihak Ketiga (Hibah Masuk)",
                            masukList.length + " Berkas",
                            totalMasukVol + " Unit",
                            totalMasukNom,
                            "Perolehan aset dari Kementerian / Pemprov / Pihak Ketiga"
                        ]);

                        rekapRows.push([
                            2,
                            "Mutasi Berkurang: Barang Inventaris RSUD Dihibahkan ke Luar (Pengurangan AT)",
                            keluarList.length + " Berkas",
                            totalKeluarVol + " Unit",
                            totalKeluarNom,
                            "Penyerahan aset ke Puskesmas / Dinas / Lembaga luar"
                        ]);

                        const rekapTotalRowIdx = rekapRows.length;
                        rekapRows.push([
                            "SALDO MUTASI BERSIH HIBAH (NETTO)",
                            "",
                            (masukList.length + keluarList.length) + " Berkas",
                            nettoVol + " Unit",
                            nettoNom,
                            nettoNom >= 0 ? "Surplus Penambahan Aset" : "Defisit Pengurangan Aset"
                        ]);

                        // Tanda Tangan PPK
                        rekapRows.push([]);
                        rekapRows.push([]);
                        rekapRows.push(["", "", "", "", `Bondowoso, ${this.formatTanggalIndo(cfg.tanggalCetak)}`]);
                        rekapRows.push(["", "", "", "", "Pejabat Pembuat Komitmen (PPK)"]);
                        rekapRows.push([]);
                        rekapRows.push([]);
                        rekapRows.push([]);
                        rekapRows.push(["", "", "", "", cfg.ppkNama]);
                        rekapRows.push(["", "", "", "", "NIP. " + cfg.ppkNip]);

                        const wsRekap = XLSX.utils.aoa_to_sheet(rekapRows);
                        wsRekap['!cols'] = [
                            { wch: 6 },
                            { wch: 45 },
                            { wch: 18 },
                            { wch: 16 },
                            { wch: 22 },
                            { wch: 35 }
                        ];
                        wsRekap['!merges'] = [
                            { s: { r: 0, c: 0 }, e: { r: 0, c: 5 } },
                            { s: { r: 1, c: 0 }, e: { r: 1, c: 5 } },
                            { s: { r: 2, c: 0 }, e: { r: 2, c: 5 } },
                            { s: { r: 3, c: 0 }, e: { r: 3, c: 5 } },
                            { s: { r: rekapTotalRowIdx, c: 0 }, e: { r: rekapTotalRowIdx, c: 1 } },
                        ];
                        XLSX.utils.book_append_sheet(wb, wsRekap, "1. Rekapitulasi Hibah");
                    }

                    // =========================================================
                    // SHEET 2: DAFTAR HIBAH MASUK (FORMAT 'all' atau 'masuk')
                    // =========================================================
                    if (cfg.format === 'all' || cfg.format === 'masuk') {
                        const masukRows = [];
                        masukRows.push(["PEMERINTAH KABUPATEN BONDOWOSO"]);
                        masukRows.push(["RUMAH SAKIT UMUM DAERAH Dr. H. KOESNANDI"]);
                        masukRows.push(["REKAPITULASI REALISASI MUTASI BERTAMBAH HIBAH / BANTUAN"]);
                        masukRows.push([`TAHUN ANGGARAN ${yearLabel} — PERIODE ${twLabel}`]);
                        masukRows.push([]); // blank

                        masukRows.push([
                            "NO",
                            "NAMA BARANG / SPESIFIKASI",
                            "KODE BARANG (108)",
                            "TAHUN",
                            "VOL",
                            "SATUAN",
                            "PEMBERI HIBAH",
                            "NOMOR BAST",
                            "TANGGAL BAST",
                            "NILAI HIBAH (RP)",
                            "KETERANGAN"
                        ]);

                        let sumMasuk = 0;
                        masukList.forEach((item, idx) => {
                            const val = parseFloat(item.nilai_aset) || 0;
                            sumMasuk += val;
                            masukRows.push([
                                idx + 1,
                                item.astap ? item.astap.nama_barang : (item.nama_barang || '-'),
                                item.astap?.kode_108 || item.kode_108 || '-',
                                item.tahun || '-',
                                item.jumlah_volume || 1,
                                item.satuan || 'Unit',
                                item.pihak_hibah || '-',
                                item.nomor_bast || '-',
                                item.tanggal_bast ? this.formatTanggalIndo(item.tanggal_bast) : '-',
                                val,
                                item.keterangan || '-'
                            ]);
                        });

                        const masukTotalIdx = masukRows.length;
                        masukRows.push([
                            "JUMLAH TOTAL REALISASI HIBAH MASUK",
                            "", "", "", "", "", "", "", "",
                            sumMasuk,
                            `Total ${masukList.length} Item Hibah Masuk`
                        ]);

                        // Tanda Tangan PPK
                        masukRows.push([]);
                        masukRows.push([]);
                        masukRows.push(["", "", "", "", "", "", "", "", `Bondowoso, ${this.formatTanggalIndo(cfg.tanggalCetak)}`]);
                        masukRows.push(["", "", "", "", "", "", "", "", "Pejabat Pembuat Komitmen (PPK)"]);
                        masukRows.push([]);
                        masukRows.push([]);
                        masukRows.push([]);
                        masukRows.push(["", "", "", "", "", "", "", "", cfg.ppkNama]);
                        masukRows.push(["", "", "", "", "", "", "", "", "NIP. " + cfg.ppkNip]);

                        const wsMasuk = XLSX.utils.aoa_to_sheet(masukRows);
                        wsMasuk['!cols'] = [
                            { wch: 5 },
                            { wch: 36 },
                            { wch: 18 },
                            { wch: 10 },
                            { wch: 8 },
                            { wch: 8 },
                            { wch: 32 },
                            { wch: 28 },
                            { wch: 20 },
                            { wch: 22 },
                            { wch: 30 }
                        ];
                        wsMasuk['!merges'] = [
                            { s: { r: 0, c: 0 }, e: { r: 0, c: 10 } },
                            { s: { r: 1, c: 0 }, e: { r: 1, c: 10 } },
                            { s: { r: 2, c: 0 }, e: { r: 2, c: 10 } },
                            { s: { r: 3, c: 0 }, e: { r: 3, c: 10 } },
                            { s: { r: masukTotalIdx, c: 0 }, e: { r: masukTotalIdx, c: 8 } },
                        ];
                        XLSX.utils.book_append_sheet(wb, wsMasuk, cfg.format === 'masuk' ? "Hibah Masuk RSDK" : "2. Hibah (Masuk)");
                    }

                    // =========================================================
                    // SHEET 3: PENGURANGAN HIBAH KELUAR (FORMAT 'all' atau 'keluar')
                    // =========================================================
                    if (cfg.format === 'all' || cfg.format === 'keluar') {
                        const keluarRows = [];
                        keluarRows.push(["PEMERINTAH KABUPATEN BONDOWOSO"]);
                        keluarRows.push(["RUMAH SAKIT UMUM DAERAH Dr. H. KOESNANDI"]);
                        keluarRows.push(["DAFTAR PENGURANGAN ASET TETAP (DIHIBAHKAN KE LUAR)"]);
                        keluarRows.push([`TAHUN ANGGARAN ${yearLabel} — PERIODE ${twLabel}`]);
                        keluarRows.push([]); // blank

                        keluarRows.push([
                            "NO",
                            "NAMA BARANG / SPESIFIKASI",
                            "KODE BARANG (108)",
                            "TAHUN PEROLEHAN",
                            "VOL",
                            "SATUAN",
                            "PENERIMA HIBAH",
                            "NOMOR BAST PENYERAHAN",
                            "TANGGAL BAST",
                            "NILAI BUKU / ASET (RP)",
                            "ALASAN / KETERANGAN"
                        ]);

                        let sumKeluar = 0;
                        keluarList.forEach((item, idx) => {
                            const val = parseFloat(item.nilai_aset) || 0;
                            sumKeluar += val;
                            keluarRows.push([
                                idx + 1,
                                item.astap ? item.astap.nama_barang : (item.nama_barang || '-'),
                                item.astap?.kode_108 || item.kode_108 || '-',
                                item.astap?.tahun_perolehan || item.tahun || '-',
                                item.jumlah_volume || 1,
                                item.satuan || 'Unit',
                                item.pihak_hibah || '-',
                                item.nomor_bast || '-',
                                item.tanggal_bast ? this.formatTanggalIndo(item.tanggal_bast) : '-',
                                val,
                                item.keterangan || 'Dihibahkan ke luar RSUD'
                            ]);
                        });

                        const keluarTotalIdx = keluarRows.length;
                        keluarRows.push([
                            "JUMLAH TOTAL PENGURANGAN ASET DIHIBAHKAN",
                            "", "", "", "", "", "", "", "",
                            sumKeluar,
                            `Total ${keluarList.length} Item Hibah Keluar`
                        ]);

                        // Tanda Tangan PPK
                        keluarRows.push([]);
                        keluarRows.push([]);
                        keluarRows.push(["", "", "", "", "", "", "", "", `Bondowoso, ${this.formatTanggalIndo(cfg.tanggalCetak)}`]);
                        keluarRows.push(["", "", "", "", "", "", "", "", "Pejabat Pembuat Komitmen (PPK)"]);
                        keluarRows.push([]);
                        keluarRows.push([]);
                        keluarRows.push([]);
                        keluarRows.push(["", "", "", "", "", "", "", "", cfg.ppkNama]);
                        keluarRows.push(["", "", "", "", "", "", "", "", "NIP. " + cfg.ppkNip]);

                        const wsKeluar = XLSX.utils.aoa_to_sheet(keluarRows);
                        wsKeluar['!cols'] = [
                            { wch: 5 },
                            { wch: 36 },
                            { wch: 18 },
                            { wch: 14 },
                            { wch: 8 },
                            { wch: 8 },
                            { wch: 32 },
                            { wch: 28 },
                            { wch: 20 },
                            { wch: 22 },
                            { wch: 35 }
                        ];
                        wsKeluar['!merges'] = [
                            { s: { r: 0, c: 0 }, e: { r: 0, c: 10 } },
                            { s: { r: 1, c: 0 }, e: { r: 1, c: 10 } },
                            { s: { r: 2, c: 0 }, e: { r: 2, c: 10 } },
                            { s: { r: 3, c: 0 }, e: { r: 3, c: 10 } },
                            { s: { r: keluarTotalIdx, c: 0 }, e: { r: keluarTotalIdx, c: 8 } },
                        ];
                        XLSX.utils.book_append_sheet(wb, wsKeluar, cfg.format === 'keluar' ? "Pengurangan Hibah Keluar" : "3. Pengurangan AT (Keluar)");
                    }

                    // Save workbook
                    const filename = `LAPORAN_HIBAH_BMD_RSDK_${yearLabel}_${twLabel}.xlsx`.replace(/\s+/g, '_');
                    XLSX.writeFile(wb, filename);

                    setTimeout(() => {
                        this.isExporting = false;
                        this.showModalExport = false;
                    }, 800);

                } catch (e) {
                    console.error("Export Error: ", e);
                    alert("❌ Gagal mengekspor berkas Excel: " + e.message);
                    this.isExporting = false;
                }
            }
        };
    }
</script>

<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.6);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: rgba(245, 158, 11, 0.4);
        border-radius: 9999px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: rgba(245, 158, 11, 0.7);
    }
</style>
