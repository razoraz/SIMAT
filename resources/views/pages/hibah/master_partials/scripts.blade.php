<!-- SCRIPTS: LOGIKA STATE MANAGEMENT & EKSPOR EXCEL MULTI-SHEET HIBAH -->
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

<script>
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
                ppkNama: 'dr. YUS PRIYATNA, Sp.P',
                ppkNip: '19760815 200501 1 009',
                tanggalCetak: new Date().toISOString().split('T')[0]
            },

            init() {
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
            // EKSPOR EXCEL BERSTANDAR SIPENERBANG / HIBAH
            // =========================================================================
            openExportModal() {
                this.exportConfig.tahun = this.selectedYear !== 'all' ? this.selectedYear : (this.availableYears[0] || new Date().getFullYear());
                this.exportConfig.triwulan = this.selectedTw;
                this.showModalExport = true;
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
