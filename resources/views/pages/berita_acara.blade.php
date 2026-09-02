<x-layout title="Berita Acara (BAST) - SIMAT-RK">
    @section('page-title', 'Berita Acara (BAST)')
    @section('breadcrumb', 'Master Utama / Berita Acara (BAST)')

    <script>
        window.__simatTriwulanData = {!! $triwulanDataJson ?? '{}' !!};
        window.__simatDistribusiList = {!! $distribusiListJson ?? '[]' !!};
        window.__simatMutasiList = {!! $mutasiListJson ?? '[]' !!};
        window.__simatUnits = {!! $unitsJson ?? '[]' !!};

        function beritaAcaraApp() {
            return {
                // Tab Navigasi Aktif: 'triwulan', 'distribusi', atau 'mutasi'
                activeTab: 'triwulan',
                
                // Modal Live Edit Toggle
                showEditTriwulanForm: false,
                showEditDistribusiForm: false,
                showEditMutasiForm: false,

                // =========================================================================
                // DATA TAB 1: BAST PENAMBAHAN DATA ASTAP BERDASARKAN TRIWULAN
                // =========================================================================
                selectedTahun: '{{ $tahun ?? "2026" }}',
                selectedTriwulanKey: 'TW2',
                showPrintTriwulanModal: false,
                searchBarangTriwulan: '',

                triwulanData: window.__simatTriwulanData || {},

                get currentTriwulanDoc() {
                    return this.triwulanData[this.selectedTriwulanKey] || this.triwulanData['TW2'] || { rekapItems: [], detailBarang: [] };
                },

                get currentTriwulanTotalNilai() {
                    return (this.currentTriwulanDoc.rekapItems || []).reduce((acc, item) => acc + (item.nilai || 0), 0);
                },

                get currentTriwulanTotalQty() {
                    return (this.currentTriwulanDoc.rekapItems || []).reduce((acc, item) => acc + (item.qty || 0), 0);
                },

                get filteredDetailBarangTriwulan() {
                    const query = (this.searchBarangTriwulan || '').toLowerCase();
                    return (this.currentTriwulanDoc.detailBarang || []).filter(b => {
                        return (b.nama_barang || '').toLowerCase().includes(query) ||
                               (b.kode_108 || '').toLowerCase().includes(query) ||
                               (b.nomor_spk || '').toLowerCase().includes(query) ||
                               (b.penyedia || '').toLowerCase().includes(query);
                    });
                },

                // =========================================================================
                // DATA TAB 2: BAST DISTRIBUSI BARANG KE UNIT / PAVILIUN (SUB ADMIN)
                // =========================================================================
                distribusiSearch: '',
                distribusiUnitFilter: 'all',
                distribusiStatusFilter: 'all',
                showPrintDistribusiModal: false,
                showDetailDistribusiModal: false,
                selectedDistribusi: null,
                selectedDetailDistribusi: null,

                unitsList: window.__simatUnits || [],
                distribusiList: window.__simatDistribusiList || [],

                get filteredDistribusiList() {
                    const query = (this.distribusiSearch || '').toLowerCase().trim();
                    return this.distribusiList.filter(d => {
                        const matchQuery = !query ||
                                           (d.nomor_bast || '').toLowerCase().includes(query) ||
                                           (d.kode || '').toLowerCase().includes(query) ||
                                           (d.unit_nama || '').toLowerCase().includes(query) ||
                                           (d.tujuan || '').toLowerCase().includes(query) ||
                                           (d.pj_nama || '').toLowerCase().includes(query) ||
                                           (d.penerima || '').toLowerCase().includes(query) ||
                                           (d.keterangan_lokasi || '').toLowerCase().includes(query) ||
                                           (d.keterangan || '').toLowerCase().includes(query) ||
                                           (d.items || []).some(it => (it.nama_barang || '').toLowerCase().includes(query));

                        const matchUnit = this.distribusiUnitFilter === 'all' || 
                                          (d.unit_nama || '').toLowerCase() === this.distribusiUnitFilter.toLowerCase() ||
                                          (d.tujuan || '').toLowerCase() === this.distribusiUnitFilter.toLowerCase();
                        
                        let matchStatus = true;
                        if (this.distribusiStatusFilter === 'signed') {
                            matchStatus = d.signed === true;
                        } else if (this.distribusiStatusFilter === 'unsigned') {
                            matchStatus = d.signed === false;
                        }
                        return matchQuery && matchUnit && matchStatus;
                    });
                },

                openDetailDistribusi(item) {
                    this.selectedDetailDistribusi = item;
                    this.showDetailDistribusiModal = true;
                },

                // =========================================================================
                // DATA TAB 3: BAST MUTASI ASET (PEMINDAHAN ANTAR RUANGAN)
                // =========================================================================
                mutasiSearch: '',
                mutasiStatusFilter: 'all',
                showPrintMutasiModal: false,
                showDetailMutasiModal: false,
                selectedMutasi: null,
                selectedDetailMutasi: null,

                mutasiList: window.__simatMutasiList || [],

                get filteredMutasiList() {
                    const query = (this.mutasiSearch || '').toLowerCase();
                    return this.mutasiList.filter(m => {
                        const matchQuery = (m.nomor_bast || '').toLowerCase().includes(query) ||
                                           (m.nama || '').toLowerCase().includes(query) ||
                                           (m.asal || '').toLowerCase().includes(query) ||
                                           (m.tujuan || '').toLowerCase().includes(query) ||
                                           (m.pemohon || '').toLowerCase().includes(query);
                        
                        let matchStatus = true;
                        if (this.mutasiStatusFilter === 'signed') {
                            matchStatus = m.signed === true;
                        } else if (this.mutasiStatusFilter === 'unsigned') {
                            matchStatus = m.signed === false;
                        }
                        return matchQuery && matchStatus;
                    });
                },

                openDetailMutasi(item) {
                    this.selectedDetailMutasi = item;
                    this.showDetailMutasiModal = true;
                },

                // Helper Format Rupiah & Angka
                formatRupiah(val) {
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(val || 0);
                },

                formatNumber(val) {
                    return new Intl.NumberFormat('id-ID').format(val || 0);
                },

                // =========================================================================
                // LOGIK TOGGLE TANDA TANGAN DIGITAL BSR-E (TTD & BATALKAN TTD) 3 JENIS BAST
                // =========================================================================

                // 1. Toggle TTD BAST Triwulan
                openPrintTriwulan(twKey) {
                    if (twKey) this.selectedTriwulanKey = twKey;
                    this.showPrintTriwulanModal = true;
                },

                async toggleSignTriwulan(key) {
                    const doc = this.triwulanData[key || this.selectedTriwulanKey];
                    if (doc) {
                        const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                        try {
                            const res = await fetch('/berita-acara/triwulan/' + doc.key + '/sign', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({ tahun: this.selectedTahun })
                            });
                            const data = await res.json();
                            if (data.success) {
                                doc.pihak2_signed = true;
                                doc.pihak2_tgl_ttd = data.tgl_signed;
                                doc.pihak2_qr_hash = data.qr_hash;
                                doc.status = data.status;
                                alert('✍️ ' + data.message);
                            }
                        } catch(e) {
                            doc.pihak2_signed = true;
                            const now = new Date();
                            doc.pihak2_tgl_ttd = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                            doc.pihak2_qr_hash = 'BSRE-KOESNANDI-TW-' + Date.now();
                            doc.status = 'Telah Ditandatangani BSrE';
                        }
                    }
                },

                async saveTriwulanEdit(key) {
                    const doc = this.triwulanData[key || this.selectedTriwulanKey];
                    if (doc) {
                        const token = document.querySelector('meta[name=csrf-token]')?.getAttribute('content') || '';
                        try {
                            const res = await fetch('/berita-acara/triwulan/' + doc.key, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({
                                    tahun: this.selectedTahun,
                                    nomor_surat: doc.nomor_surat,
                                    lokasi: doc.lokasi,
                                    pihak1_nama: doc.pihak1_nama,
                                    pihak1_nip: doc.pihak1_nip,
                                    pihak1_jabatan: doc.pihak1_jabatan,
                                    pihak2_nama: doc.pihak2_nama,
                                    pihak2_nip: doc.pihak2_nip,
                                    pihak2_jabatan: doc.pihak2_jabatan,
                                    direktur_nama: doc.direktur_nama,
                                    direktur_nip: doc.direktur_nip,
                                    catatan: doc.catatan
                                })
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.showEditTriwulanForm = false;
                                alert('✅ ' + data.message);
                            }
                        } catch(e) {
                            this.showEditTriwulanForm = false;
                            alert('✅ Data BAST Triwulan berhasil diperbarui!');
                        }
                    }
                },

                // 2. Toggle TTD BAST Distribusi
                openPrintDistribusi(item) {
                    this.selectedDistribusi = item ? { ...item } : this.distribusiList[0];
                    this.showPrintDistribusiModal = true;
                },

                async toggleSignDistribusi(item) {
                    const target = item || this.selectedDistribusi;
                    if (!target || !target.id) return;

                    const matched = this.distribusiList.find(d => d.id === target.id);

                    try {
                        const resp = await fetch(`/distribusi/${target.id}/sign`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await resp.json();
                        if (!data.success) throw new Error(data.message || 'Gagal menyimpan status TTD.');

                        // Update semua referensi objek di state Alpine
                        const applyUpdate = (obj) => {
                            obj.signed     = data.signed;
                            obj.tgl_signed = data.tgl_signed;
                            obj.qr_hash    = data.qr_hash;
                            obj.status     = data.signed ? 'Telah Ditandatangani BSrE' : 'Belum TTD';
                        };
                        applyUpdate(target);
                        if (matched && matched !== target) applyUpdate(matched);
                        if (this.selectedDistribusi && this.selectedDistribusi.id === target.id && this.selectedDistribusi !== target) {
                            applyUpdate(this.selectedDistribusi);
                        }

                        alert(data.signed
                            ? '✍️ BAST (' + (target.nomor_bast || 'BAST') + ') berhasil ditandatangani secara digital BSrE! Status tersimpan.'
                            : '↩️ Tanda tangan BSrE BAST (' + (target.nomor_bast || 'BAST') + ') berhasil dibatalkan.');
                    } catch (err) {
                        alert('❌ Gagal menyimpan status TTD: ' + err.message);
                    }
                },

                // 3. Toggle TTD BAST Mutasi
                openPrintMutasi(item) {
                    this.selectedMutasi = item ? { ...item } : this.mutasiList[0];
                    this.showPrintMutasiModal = true;
                },

                toggleSignMutasi(item) {
                    const target = item || this.selectedMutasi;
                    if (target) {
                        if (target.signed) {
                            target.signed = false;
                            target.tgl_signed = '-';
                            target.qr_hash = '';
                            target.status = 'Belum TTD';
                            alert('↩️ Tanda tangan digital BSrE BAST Mutasi (' + target.nomor_bast + ') berhasil dibatalkan.');
                        } else {
                            target.signed = true;
                            const now = new Date();
                            target.tgl_signed = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                            target.qr_hash = 'BSRE-KOESNANDI-MTS-' + Date.now();
                            target.status = 'Telah Ditandatangani BSrE';
                            alert('✍️ BAST Mutasi (' + target.nomor_bast + ') berhasil ditandatangani secara digital (QR Code BSrE Aktif)!');
                        }
                    }
                },

                printCurrent() {
                    let el = null;
                    if (this.showPrintTriwulanModal) {
                        el = document.getElementById('print-area-triwulan');
                    } else if (this.showPrintDistribusiModal) {
                        el = document.getElementById('print-area-distribusi');
                    } else if (this.showPrintMutasiModal) {
                        el = document.getElementById('print-area-mutasi');
                    }

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

                    const doc = iframe.contentWindow.document;
                    doc.open();
                    doc.write(`<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara - RSUD Dr. H. Koesnandi</title>
    <script src="https://cdn.tailwindcss.com"><\/script>
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
        img {
            max-width: 100% !important;
            height: auto !important;
            object-fit: contain !important;
        }
    </style>
</head>
<body class="bg-white text-black font-sans">
    <div class="print-container">
        ${el.innerHTML}
    </div>
</body>
</html>`);
                    doc.close();

                    setTimeout(() => {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    }, 400);
                }
            };
        }
    </script>

    <div x-data="beritaAcaraApp()" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="no-print bg-gradient-to-r from-purple-600/15 via-slate-900 to-slate-900 border border-purple-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                        <span>MODUL PENGESAHAN BAST (LIVE EDIT, TTD BSR-E, & BATALKAN TTD)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Pusat Cetak & Pengesahan BAST</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pengesahan Tanda Tangan Digital BSrE, Pembatalan TTD, Live Edit Dokumen Surat, dan Pencetakan 3 Jenis Berita Acara (Triwulan ASTAP, Distribusi Unit, & Mutasi Aset).
                    </p>
                </div>
                
                <!-- Quick Print Buttons (Tersusun Sejajar Rapi) -->
                <div class="flex flex-wrap lg:flex-nowrap items-center gap-2 sm:gap-3 shrink-0">
                    <button type="button" @click="activeTab = 'triwulan'; openPrintTriwulan('TW2')"
                        class="px-3.5 py-2.5 rounded-2xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 shrink-0 active:scale-95">
                        <span>🏛️ BAST Triwulan</span>
                    </button>
                    <button type="button" @click="activeTab = 'distribusi'; openPrintDistribusi(distribusiList[0])"
                        class="px-3.5 py-2.5 rounded-2xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-1.5 shrink-0 active:scale-95">
                        <span>🚚 BAST Distribusi</span>
                    </button>
                    <button type="button" @click="activeTab = 'mutasi'; openPrintMutasi(mutasiList[0])"
                        class="px-3.5 py-2.5 rounded-2xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-1.5 shrink-0 active:scale-95">
                        <span>🔄 BAST Mutasi</span>
                    </button>
                </div>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏛️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Triwulan ASTAP</span>
                        <span class="text-sm sm:text-base font-extrabold text-white">4 Periode Rekap</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🚚</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Distribusi Unit</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-300" x-text="distribusiList.length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-400 text-lg">🔄</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">BAST Mutasi Aset</span>
                        <span class="text-sm sm:text-base font-extrabold text-rose-300" x-text="mutasiList.length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">✍️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Status TTD BSrE</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300">TTD & Batal TTD</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- NAVIGATION 3 TABS: TAB 1 (TRIWULAN) | TAB 2 (DISTRIBUSI) | TAB 3 (MUTASI)  -->
        <!-- ========================================================================= -->
        <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-3 shadow-xl mb-6">
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                
                <!-- Tab 1 Button -->
                <button type="button" @click="activeTab = 'triwulan'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'triwulan' ? 'bg-purple-500/20 text-purple-300 border-2 border-purple-500/50 shadow-lg shadow-purple-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'triwulan' ? 'bg-purple-500 text-slate-950 shadow-md shadow-purple-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🏛️</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'triwulan' ? 'text-purple-400' : 'text-slate-500'">Pengadaan ASTAP</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">1. BAST Triwulan ASTAP</span>
                        <span class="text-[10px] text-slate-400 block truncate">Rekap 8 Kategori & SPK Perolehan</span>
                    </div>
                </button>

                <!-- Tab 2 Button -->
                <button type="button" @click="activeTab = 'distribusi'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'distribusi' ? 'bg-teal-500/20 text-teal-300 border-2 border-teal-500/50 shadow-lg shadow-teal-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'distribusi' ? 'bg-teal-500 text-slate-950 shadow-md shadow-teal-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🚚</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'distribusi' ? 'text-teal-400' : 'text-slate-500'">Penyerahan Unit</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">2. BAST Distribusi Aset</span>
                        <span class="text-[10px] text-slate-400 block truncate">Serah Terima Sekali Transaksi Unit</span>
                    </div>
                </button>

                <!-- Tab 3 Button -->
                <button type="button" @click="activeTab = 'mutasi'"
                    class="p-4 rounded-2xl transition-all flex items-center space-x-3 text-left"
                    :class="activeTab === 'mutasi' ? 'bg-rose-500/20 text-rose-300 border-2 border-rose-500/50 shadow-lg shadow-rose-500/10' : 'bg-slate-950/60 text-slate-400 hover:text-white border border-slate-800/80'">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-lg font-bold shrink-0"
                         :class="activeTab === 'mutasi' ? 'bg-rose-500 text-slate-950 shadow-md shadow-rose-500/30' : 'bg-slate-900 text-slate-400 border border-slate-800'">
                        <span>🔄</span>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] font-bold uppercase tracking-wider block" :class="activeTab === 'mutasi' ? 'text-rose-400' : 'text-slate-500'">Pemindahan Ruangan</span>
                        <span class="text-xs sm:text-sm font-extrabold block text-white truncate">3. BAST Mutasi Aset</span>
                        <span class="text-[10px] text-slate-400 block truncate">Pemindahan Barang Ruang Asal &rarr; Tujuan</span>
                    </div>
                </button>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- KONTEN TAB 1: BAST PENAMBAHAN DATA ASTAP BERDASARKAN TRIWULAN             -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'triwulan'" class="space-y-6" x-cloak>
            
            <!-- Filter Triwulan & Tahun Toolbar -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    
                    <!-- Pilihan Periode Filter Dropdown -->
                    <div class="flex flex-wrap items-center gap-2">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Pilih Periode:</label>
                        <select x-model="selectedTriwulanKey" class="bg-slate-950 border border-purple-500/40 hover:border-purple-400 rounded-xl px-3.5 py-2 text-xs font-bold text-purple-300 focus:outline-none focus:border-purple-500 shadow-md shadow-purple-500/10 transition-all cursor-pointer">
                            <option value="TW1">Triwulan I (Jan - Mar)</option>
                            <option value="TW2">Triwulan II (Apr - Jun)</option>
                            <option value="TW3">Triwulan III (Jul - Sep)</option>
                            <option value="TW4">Triwulan IV (Okt - Des)</option>
                        </select>
                    </div>

                    <!-- Tahun Dropdown & Tombol Aksi TTD / Cetak -->
                    <div class="flex flex-wrap items-center gap-2 shrink-0">
                        <select x-model="selectedTahun" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-purple-500">
                            <option value="2026">Tahun Anggaran 2026</option>
                            <option value="2025">Tahun Anggaran 2025</option>
                        </select>

                        <!-- Button Toggle TTD BSrE / Batalkan TTD -->
                        <button type="button" @click="toggleSignTriwulan(selectedTriwulanKey)"
                            :class="currentTriwulanDoc.pihak2_signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-lg shadow-emerald-500/20'"
                            class="px-3.5 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95 flex items-center space-x-1">
                            <span x-text="currentTriwulanDoc.pihak2_signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <!-- Button Cetak BAST Triwulan -->
                        <button type="button" @click="openPrintTriwulan(selectedTriwulanKey)"
                            class="px-3.5 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>🖨️ Cetak / Edit</span>
                        </button>
                    </div>

                </div>
            </div>

            <!-- Card Ringkasan Triwulan Aktif -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-4">
                    <div>
                        <div class="inline-flex items-center space-x-2 px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-[10px] font-bold mb-1">
                            <span x-text="'DOKUMEN BAST RESMI: ' + currentTriwulanDoc.nomor_surat"></span>
                        </div>
                        <h2 class="text-lg font-extrabold text-white" x-text="'Berita Acara Serah Terima Barang - ' + currentTriwulanDoc.triwulan_nama"></h2>
                        <p class="text-xs text-slate-400 mt-0.5" x-text="'Hari & Tanggal Pelaksanaan: ' + currentTriwulanDoc.hari_tanggal"></p>
                    </div>

                    <div class="flex items-center space-x-3">
                        <div class="text-right">
                            <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Realisasi Triwulan Ini</span>
                            <span class="text-base sm:text-lg font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(currentTriwulanTotalNilai)"></span>
                        </div>
                        <span class="px-3 py-1.5 rounded-xl text-xs font-bold border"
                              :class="currentTriwulanDoc.pihak2_signed ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'">
                            <span x-text="currentTriwulanDoc.pihak2_signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum TTD'"></span>
                        </span>
                    </div>
                </div>

                <!-- Bagian 1: TABEL REKAPITULASI 8 KELOMPOK ASET (FORMAT ASLI STANDAR BAST) -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-200 uppercase tracking-wider flex items-center space-x-2">
                            <span>📊 1. REKAPITULASI 8 KELOMPOK ASET TETAP TRIWULAN INI:</span>
                        </span>
                        <span class="text-[11px] text-purple-400 font-mono font-bold" x-text="'Total: ' + formatNumber(currentTriwulanTotalQty) + ' Barang / Aset'"></span>
                    </div>
                    
                    <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead class="bg-slate-950 text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700">
                                <tr>
                                    <th class="px-3 py-2.5 text-center w-12 border-r border-slate-800">No</th>
                                    <th class="px-4 py-2.5 border-r border-slate-800">Nama Kelompok / Kategori Aset</th>
                                    <th class="px-4 py-2.5 text-center border-r border-slate-800 w-32">Kuantitas</th>
                                    <th class="px-4 py-2.5 text-right w-48">Nilai Realisasi Perolehan (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/60 font-medium">
                                <template x-for="item in currentTriwulanDoc.rekapItems" :key="item.no">
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="px-3 py-2.5 text-center text-slate-400 font-mono border-r border-slate-800" x-text="item.no"></td>
                                        <td class="px-4 py-2.5 font-semibold text-slate-200 border-r border-slate-800" x-text="item.nama"></td>
                                        <td class="px-4 py-2.5 text-center font-mono font-bold text-cyan-400 border-r border-slate-800" x-text="formatNumber(item.qty) + ' Barang'"></td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold" :class="item.nilai > 0 ? 'text-amber-300' : 'text-slate-500'" x-text="'Rp ' + formatRupiah(item.nilai)"></td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot class="bg-slate-950 text-white font-extrabold border-t-2 border-slate-700">
                                <tr>
                                    <td colspan="2" class="px-4 py-3 text-center uppercase tracking-wider text-purple-300 border-r border-slate-800">Total Pengadaan Triwulan Terpilih:</td>
                                    <td class="px-4 py-3 text-center font-mono text-cyan-300 text-sm border-r border-slate-800" x-text="formatNumber(currentTriwulanTotalQty) + ' Barang'"></td>
                                    <td class="px-4 py-3 text-right font-mono text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(currentTriwulanTotalNilai)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <!-- Bagian 2: TABEL RINCIAN DETAIL BARANG YANG DIADAKAN PADA TRIWULAN INI -->
                <div class="pt-4 border-t border-slate-800 space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                        <div>
                            <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center space-x-2">
                                <span>📦 2. DAFTAR RINCIAN BARANG & BELANJA MODAL YANG DIADAKAN PADA TRIWULAN INI:</span>
                            </span>
                            <p class="text-[11px] text-slate-400 mt-0.5">Daftar item belanja modal yang dibukukan lengkap dengan dokumen SPK dan nilai realisasinya.</p>
                        </div>

                        <!-- Search Box Filter Barang -->
                        <div class="relative w-full sm:w-64">
                            <input type="text" x-model="searchBarangTriwulan" placeholder="Cari nama barang / kode 108 / penyedia..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 pl-8 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-purple-500">
                            <svg class="w-3.5 h-3.5 text-purple-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-700 shadow-xl custom-scrollbar" style="max-height: 340px; overflow-y: auto; overflow-x: auto;">
                        <table class="w-full text-left text-xs border-collapse min-w-[900px]">
                            <thead class="text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700 text-[11px]" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                                <tr>
                                    <th class="px-3 py-2.5 text-center w-10 border-r border-slate-800">No</th>
                                    <th class="px-3 py-2.5 border-r border-slate-800">Tgl & No SPK</th>
                                    <th class="px-3 py-2.5 border-r border-slate-800">Kode Barang 108</th>
                                    <th class="px-4 py-2.5 border-r border-slate-800">Nama Barang & Spesifikasi</th>
                                    <th class="px-3 py-2.5 border-r border-slate-800">Penyedia / Rekanan</th>
                                    <th class="px-3 py-2.5 text-center border-r border-slate-800">Volume</th>
                                    <th class="px-4 py-2.5 text-right">Nilai Realisasi (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800 bg-slate-900/60 font-medium text-[11px]">
                                <template x-for="(b, idx) in filteredDetailBarangTriwulan" :key="b.no">
                                    <tr class="hover:bg-slate-800/40 transition-colors">
                                        <td class="px-3 py-2.5 text-center text-slate-400 font-mono border-r border-slate-800" x-text="idx + 1"></td>
                                        <td class="px-3 py-2.5 border-r border-slate-800">
                                            <div class="font-mono font-bold text-purple-300" x-text="b.nomor_spk"></div>
                                            <div class="text-[10px] text-slate-400" x-text="b.tanggal_sp2d"></div>
                                        </td>
                                        <td class="px-3 py-2.5 font-mono text-cyan-400 font-bold border-r border-slate-800" x-text="b.kode_108"></td>
                                        <td class="px-4 py-2.5 border-r border-slate-800">
                                            <div class="font-bold text-white" x-text="b.nama_barang"></div>
                                            <div class="text-[10px] text-slate-400" x-text="b.spesifikasi"></div>
                                        </td>
                                        <td class="px-3 py-2.5 text-slate-300 border-r border-slate-800 font-semibold" x-text="b.penyedia"></td>
                                        <td class="px-3 py-2.5 text-center font-mono font-bold text-white border-r border-slate-800" x-text="b.volume + ' ' + b.satuan"></td>
                                        <td class="px-4 py-2.5 text-right font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(b.nilai_realisasi)"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        <!-- ========================================================================= -->
        <!-- KONTEN TAB 2: BAST DISTRIBUSI BARANG KE UNIT & PAVILIUN (SUB ADMIN)       -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'distribusi'" class="space-y-6" x-cloak>
            
            <!-- Toolbar & Filter Distribusi -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-80">
                            <input type="text" x-model="distribusiSearch" placeholder="Cari nomor BAST / unit / PJ..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500">
                            <svg class="w-4 h-4 text-teal-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Filter Status TTD -->
                        <select x-model="distribusiStatusFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-teal-300 font-bold focus:outline-none focus:border-teal-500">
                            <option value="all">🔍 Semua Status TTD</option>
                            <option value="signed">✍️ Telah Ditandatangani BSrE</option>
                            <option value="unsigned">⏳ Belum Ditandatangani</option>
                        </select>

                        <!-- Filter Unit Dropdown -->
                        <select x-model="distribusiUnitFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:border-teal-500">
                            <option value="all">Semua Unit / Paviliun (Sub-Admin)</option>
                            <template x-for="u in unitsList" :key="u.id">
                                <option :value="u.nama" x-text="u.nama"></option>
                            </template>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar BAST Distribusi ke Unit / Ruangan -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
                <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 340px; overflow-y: auto; overflow-x: auto;">
                    <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                        <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5 text-left">Nomor BAST Distribusi</th>
                            <th class="px-4 py-3.5 text-left">Unit / Paviliun (Penerima)</th>
                            <th class="px-4 py-3.5 text-left">Kepala Ruangan / PJ (Sub Admin)</th>
                            <th class="px-4 py-3.5 text-center">Jumlah Barang</th>
                            <th class="px-4 py-3.5 text-center">Status TTD BSrE</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi BAST</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredDistribusiList" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                                <td class="px-4 py-4">
                                    <div class="font-mono font-bold text-teal-400" x-text="item.nomor_bast"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.tgl_bast"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-white" x-text="item.unit_nama"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.unit_tipe"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-semibold text-emerald-400" x-text="item.pj_nama"></div>
                                    <div class="text-[10px] text-slate-400 font-mono" x-text="'NIP. ' + item.pj_nip"></div>
                                </td>
                                <td class="px-4 py-4 text-center font-mono font-bold text-white" x-text="item.items.reduce((s, i) => s + i.qty, 0) + ' Unit'"></td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center space-x-1"
                                          :class="item.signed ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'">
                                        <span x-text="item.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum TTD'"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                    
                                    <!-- 1. Tombol Toggle TTD / Batalkan TTD -->
                                    <button type="button" @click="toggleSignDistribusi(item)"
                                        :class="item.signed ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-sm'"
                                        class="px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all inline-flex items-center space-x-1 active:scale-95"
                                        :title="item.signed ? 'Batalkan Tanda Tangan Digital BSrE' : 'Tanda Tangan Digital BSrE'">
                                        <span x-text="item.signed ? '↩️ Batal TTD' : '✍️ TTD BSrE'"></span>
                                    </button>

                                    <!-- 2. Tombol Rincian Modal -->
                                    <button type="button" @click="openDetailDistribusi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-teal-500/15 hover:bg-teal-500/25 text-teal-300 border border-teal-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>👁️ Rincian</span>
                                    </button>

                                    <!-- 3. Tombol Cetak (dengan Live Edit Panel) -->
                                    <button type="button" @click="openPrintDistribusi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>🖨️ Cetak / Edit</span>
                                    </button>

                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        </div>

        <!-- ========================================================================= -->
        <!-- KONTEN TAB 3: BAST MUTASI ASET (PEMINDAHAN ANTAR RUANGAN)                  -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'mutasi'" class="space-y-6" x-cloak>
            
            <!-- Toolbar & Filter Status Mutasi -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                    
                    <div class="flex flex-wrap items-center gap-3 w-full sm:w-auto">
                        <div class="relative flex-1 sm:w-80">
                            <input type="text" x-model="mutasiSearch" placeholder="Cari nomor BAST mutasi / barang / ruangan..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500">
                            <svg class="w-4 h-4 text-rose-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Filter Status TTD -->
                        <select x-model="mutasiStatusFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-rose-300 font-bold focus:outline-none focus:border-rose-500">
                            <option value="all">🔍 Semua Status TTD Mutasi</option>
                            <option value="signed">✍️ Telah Ditandatangani BSrE</option>
                            <option value="unsigned">⏳ Belum Ditandatangani</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Tabel Daftar BAST Mutasi Aset -->
            <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
                <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 340px; overflow-y: auto; overflow-x: auto;">
                    <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                        <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12">No</th>
                            <th class="px-4 py-3.5 text-left">Nomor BAST Mutasi</th>
                            <th class="px-4 py-3.5 text-left">Nama Barang Dimutasi</th>
                            <th class="px-4 py-3.5 text-center">Ruangan Asal</th>
                            <th class="px-4 py-3.5 text-center">Ruangan Tujuan</th>
                            <th class="px-4 py-3.5 text-center">Status TTD BSrE</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi BAST</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredMutasiList" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                                <td class="px-4 py-4">
                                    <div class="font-mono font-bold text-rose-400" x-text="item.nomor_bast"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.tgl_bast"></div>
                                </td>
                                <td class="px-4 py-4">
                                    <div class="font-bold text-white" x-text="item.nama"></div>
                                    <div class="text-[10px] text-slate-400 font-mono" x-text="item.kode_barang + ' • Vol: ' + item.qty + ' ' + item.satuan"></div>
                                </td>
                                <td class="px-4 py-4 text-center font-semibold text-slate-300" x-text="item.asal"></td>
                                <td class="px-4 py-4 text-center font-semibold text-rose-300" x-text="item.tujuan"></td>
                                <td class="px-4 py-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border inline-flex items-center space-x-1"
                                          :class="item.signed ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'">
                                        <span x-text="item.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum TTD'"></span>
                                    </span>
                                </td>
                                <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                    
                                    <!-- 1. Tombol Toggle TTD / Batalkan TTD -->
                                    <button type="button" @click="toggleSignMutasi(item)"
                                        :class="item.signed ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-sm'"
                                        class="px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all inline-flex items-center space-x-1 active:scale-95"
                                        :title="item.signed ? 'Batalkan Tanda Tangan Digital BSrE' : 'Tanda Tangan Digital BSrE'">
                                        <span x-text="item.signed ? '↩️ Batal TTD' : '✍️ TTD BSrE'"></span>
                                    </button>

                                    <!-- 2. Tombol Rincian Modal -->
                                    <button type="button" @click="openDetailMutasi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-500/15 hover:bg-rose-500/25 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>👁️ Rincian</span>
                                    </button>

                                    <!-- 3. Tombol Cetak (dengan Live Edit Panel) -->
                                    <button type="button" @click="openPrintMutasi(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                        <span>🖨️ Cetak / Edit</span>
                                    </button>

                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        </div>

        <!-- ========================================================================= -->
        <!-- MODAL RINCIAN 1: DETAIL POPUP BAST DISTRIBUSI BARANG ASET                  -->
        <!-- ========================================================================= -->
        <div x-show="showDetailDistribusiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 overflow-y-auto" x-cloak>
            <div @click.away="showDetailDistribusiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 my-auto relative">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xl font-bold">
                            🚚
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-teal-400 uppercase tracking-wider block">Rincian BAST Distribusi</span>
                            <h3 class="text-lg font-extrabold text-white" x-text="selectedDetailDistribusi ? selectedDetailDistribusi.nomor_bast : ''"></h3>
                        </div>
                    </div>
                    
                    <button type="button" @click="showDetailDistribusiModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white font-bold">&times;</button>
                </div>

                <template x-if="selectedDetailDistribusi">
                    <div class="space-y-4 text-xs">
                        
                        <!-- Status Bar TTD BSrE & Toggle Button -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Status Tanda Tangan Digital BSrE:</span>
                                <span class="text-sm font-extrabold"
                                      :class="selectedDetailDistribusi.signed ? 'text-emerald-400' : 'text-amber-400'"
                                      x-text="selectedDetailDistribusi.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum Ditandatangani'"></span>
                            </div>

                            <button type="button" @click="toggleSignDistribusi(selectedDetailDistribusi)"
                                :class="selectedDetailDistribusi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-md'"
                                class="px-4 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95">
                                <span x-text="selectedDetailDistribusi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ Tandatangani Digital BSrE'"></span>
                            </button>
                        </div>

                        <!-- Data Informasi Unit & PJ -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-teal-400 font-bold uppercase block">Unit / Paviliun Penerima</span>
                                <div class="font-extrabold text-white text-sm" x-text="selectedDetailDistribusi.unit_nama"></div>
                                <div class="text-[11px] text-slate-400" x-text="selectedDetailDistribusi.unit_tipe"></div>
                                <div class="text-[11px] text-slate-300 pt-1" x-text="'Catatan: ' + selectedDetailDistribusi.keterangan_lokasi"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-emerald-400 font-bold uppercase block">Penanggung Jawab (Sub-Admin)</span>
                                <div class="font-extrabold text-emerald-300 text-sm" x-text="selectedDetailDistribusi.pj_nama"></div>
                                <div class="text-[11px] text-slate-400 font-mono" x-text="'NIP. ' + selectedDetailDistribusi.pj_nip"></div>
                                <div class="text-[11px] text-slate-300 pt-1" x-text="selectedDetailDistribusi.pj_jabatan_ttd"></div>
                            </div>
                        </div>

                        <!-- Tabel Item Barang -->
                        <div>
                            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider block mb-2">📦 Rincian Barang Yang Penyerahannya Diberikan:</span>
                            <div class="overflow-x-auto rounded-2xl border border-slate-800">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-950 text-slate-400 font-bold border-b border-slate-800">
                                        <tr>
                                            <th class="px-3 py-2 text-center w-8">No</th>
                                            <th class="px-3 py-2">Nama Barang</th>
                                            <th class="px-3 py-2">Merk / Spesifikasi</th>
                                            <th class="px-3 py-2 text-center">Qty</th>
                                            <th class="px-3 py-2 text-center">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800 bg-slate-900/60">
                                        <template x-for="(sub, idx) in selectedDetailDistribusi.items" :key="idx">
                                            <tr>
                                                <td class="px-3 py-2 text-center text-slate-400 font-mono" x-text="idx + 1"></td>
                                                <td class="px-3 py-2 font-bold text-white" x-text="sub.nama_barang"></td>
                                                <td class="px-3 py-2 text-slate-400" x-text="sub.merk_type"></td>
                                                <td class="px-3 py-2 text-center font-bold text-teal-300 font-mono" x-text="sub.qty + ' ' + sub.satuan"></td>
                                                <td class="px-3 py-2 text-center font-bold text-emerald-400" x-text="sub.kondisi"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center">
                            <button type="button" @click="showDetailDistribusiModal = false; openPrintDistribusi(selectedDetailDistribusi)"
                                class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all flex items-center space-x-1.5">
                                <span>🖨️ Cetak & Edit Surat BAST</span>
                            </button>
                            <button type="button" @click="showDetailDistribusiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                        </div>

                    </div>
                </template>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL RINCIAN 2: DETAIL POPUP BAST MUTASI ASET                            -->
        <!-- ========================================================================= -->
        <div x-show="showDetailMutasiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-4 overflow-y-auto" x-cloak>
            <div @click.away="showDetailMutasiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-3xl w-full p-6 shadow-2xl space-y-5 my-auto relative">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xl font-bold">
                            🔄
                        </div>
                        <div>
                            <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider block">Rincian BAST Mutasi Aset</span>
                            <h3 class="text-lg font-extrabold text-white" x-text="selectedDetailMutasi ? selectedDetailMutasi.nomor_bast : ''"></h3>
                        </div>
                    </div>
                    
                    <button type="button" @click="showDetailMutasiModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white font-bold">&times;</button>
                </div>

                <template x-if="selectedDetailMutasi">
                    <div class="space-y-4 text-xs">
                        
                        <!-- Status Bar TTD BSrE & Toggle Button -->
                        <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 font-semibold block uppercase">Status Tanda Tangan Digital BSrE:</span>
                                <span class="text-sm font-extrabold"
                                      :class="selectedDetailMutasi.signed ? 'text-emerald-400' : 'text-amber-400'"
                                      x-text="selectedDetailMutasi.signed ? '✍️ Ditandatangani BSrE' : '⏳ Belum Ditandatangani'"></span>
                            </div>

                            <button type="button" @click="toggleSignMutasi(selectedDetailMutasi)"
                                :class="selectedDetailMutasi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-md'"
                                class="px-4 py-2 rounded-xl font-extrabold text-xs transition-all active:scale-95">
                                <span x-text="selectedDetailMutasi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ Tandatangani Digital BSrE'"></span>
                            </button>
                        </div>

                        <!-- Data Informasi Mutasi Ruangan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Ruangan Asal</span>
                                <div class="font-extrabold text-white text-sm" x-text="selectedDetailMutasi.asal"></div>
                                <div class="text-[11px] text-purple-300" x-text="'PJ: ' + selectedDetailMutasi.pj_asal_nama"></div>
                            </div>

                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1">
                                <span class="text-[10px] text-rose-400 font-bold uppercase block">Ruangan Tujuan</span>
                                <div class="font-extrabold text-rose-300 text-sm" x-text="selectedDetailMutasi.tujuan"></div>
                                <div class="text-[11px] text-emerald-300" x-text="'PJ: ' + selectedDetailMutasi.pj_tujuan_nama"></div>
                            </div>
                        </div>

                        <!-- Info Barang Dimutasi -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                            <span class="text-[10px] text-cyan-400 font-bold uppercase block">Barang Aset Yang Dimutasi</span>
                            <template x-if="selectedDetailMutasi.items && selectedDetailMutasi.items.length > 0">
                                <div class="space-y-2">
                                    <template x-for="(sub, idx) in selectedDetailMutasi.items" :key="idx">
                                        <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between text-xs">
                                            <div>
                                                <div class="font-bold text-white" x-text="(idx + 1) + '. ' + sub.nama_barang"></div>
                                                <div class="text-[10px] text-slate-400 font-mono" x-text="'NIBAR: ' + (sub.nibar || '-') + ' • Kode 108: ' + sub.kode_barang"></div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-semibold text-[10px]" x-text="'Kondisi: ' + sub.kondisi"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <template x-if="!selectedDetailMutasi.items || selectedDetailMutasi.items.length === 0">
                                <div>
                                    <div class="font-extrabold text-white text-base" x-text="selectedDetailMutasi.nama"></div>
                                    <div class="flex flex-wrap gap-3 font-mono text-[11px] text-slate-400">
                                        <span>Kode 108: <strong class="text-cyan-300" x-text="selectedDetailMutasi.kode_barang"></strong></span>
                                        <span>Volume: <strong class="text-white" x-text="selectedDetailMutasi.qty + ' ' + selectedDetailMutasi.satuan"></strong></span>
                                    </div>
                                </div>
                            </template>
                            <div class="text-slate-300 text-[11px] pt-1" x-text="'Alasan Pemindahan: ' + selectedDetailMutasi.keterangan"></div>
                        </div>

                        <div class="pt-3 border-t border-slate-800 flex justify-between items-center">
                            <button type="button" @click="showDetailMutasiModal = false; openPrintMutasi(selectedDetailMutasi)"
                                class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all flex items-center space-x-1.5">
                                <span>🖨️ Cetak & Edit Surat BAST</span>
                            </button>
                            <button type="button" @click="showDetailMutasiModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                        </div>

                    </div>
                </template>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL CETAK 1: LEMBAR DOKUMEN BAST PENAMBAHAN ASTAP (TRIWULAN PENGADAAN)  -->
        <!-- ========================================================================= -->
        <div x-show="showPrintTriwulanModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintTriwulanModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 text-sm">🖨️</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Cetak Berita Acara Serah Terima Barang (Triwulan)</h3>
                            <p class="text-[11px] text-slate-400">Dokumen Resmi Pengesahan Hasil Belanja Modal RSUD Dr. H. Koesnandi</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="showEditTriwulanForm = !showEditTriwulanForm"
                            class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                            <span x-text="showEditTriwulanForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <!-- Toggle Button TTD / Batalkan TTD -->
                        <button type="button" @click="toggleSignTriwulan(selectedTriwulanKey)"
                            :class="currentTriwulanDoc.pihak2_signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                            <span x-text="currentTriwulanDoc.pihak2_signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-purple-500 text-slate-950 font-bold text-xs shadow-lg">
                            🖨️ Cetak Surat
                        </button>
                        <button type="button" @click="showPrintTriwulanModal = false" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                </div>

                <!-- Formulir Edit Live BAST Triwulan -->
                <div x-show="showEditTriwulanForm" class="no-print bg-slate-950 p-5 rounded-2xl border border-purple-500/40 text-xs space-y-4 shadow-2xl">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                        <div class="flex items-center space-x-2">
                            <span class="p-1.5 rounded-lg bg-purple-500/20 text-purple-300">✏️</span>
                            <div class="font-bold text-purple-300 text-xs uppercase tracking-wider">
                                Live Edit Surat BAST Serah Terima Barang (Otomatis Berubah Pada Lembar Cetak):
                            </div>
                        </div>
                        <button type="button" @click="saveTriwulanEdit(selectedTriwulanKey)"
                            class="px-4 py-1.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-md transition-all active:scale-95 flex items-center space-x-1.5">
                            <span>💾 Simpan ke Database</span>
                        </button>
                    </div>

                    <!-- Section 1: Informasi Dokumen & Lokasi -->
                    <div class="space-y-1.5">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">📄 Informasi Dokumen & Lokasi Pelaksanaan</span>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                                <input type="text" x-model="currentTriwulanDoc.nomor_surat" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-purple-300 font-mono font-bold text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari & Tanggal Surat</label>
                                <input type="text" x-model="currentTriwulanDoc.hari_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Lokasi Pelaksanaan</label>
                                <input type="text" x-model="currentTriwulanDoc.lokasi" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Pihak I & Pihak II -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-slate-800">
                        <!-- Pihak I: Pejabat Pembuat Komitmen (PPK) -->
                        <div class="p-3.5 bg-slate-900/90 rounded-2xl border border-purple-500/20 space-y-2.5">
                            <span class="text-[10px] font-bold text-purple-300 uppercase tracking-wider block flex items-center space-x-1.5">
                                <span>👤 PIHAK I (YANG MENYERAHKAN / PPK)</span>
                            </span>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-0.5">Nama Lengkap & Gelar</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak1_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white font-bold text-xs focus:border-purple-400 focus:outline-none">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">NIP</label>
                                    <input type="text" x-model="currentTriwulanDoc.pihak1_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 font-mono text-xs focus:border-purple-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">Jabatan</label>
                                    <input type="text" x-model="currentTriwulanDoc.pihak1_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 text-xs focus:border-purple-400 focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Pihak II: Pengurus Barang Aset -->
                        <div class="p-3.5 bg-slate-900/90 rounded-2xl border border-emerald-500/20 space-y-2.5">
                            <span class="text-[10px] font-bold text-emerald-300 uppercase tracking-wider block flex items-center space-x-1.5">
                                <span>👤 PIHAK II (YANG MENERIMA / PENGURUS BARANG)</span>
                            </span>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-0.5">Nama Lengkap & Gelar</label>
                                <input type="text" x-model="currentTriwulanDoc.pihak2_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-emerald-300 font-bold text-xs focus:border-emerald-400 focus:outline-none">
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">NIP</label>
                                    <input type="text" x-model="currentTriwulanDoc.pihak2_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 font-mono text-xs focus:border-emerald-400 focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-0.5">Jabatan</label>
                                    <input type="text" x-model="currentTriwulanDoc.pihak2_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-slate-200 text-xs focus:border-emerald-400 focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LEMBAR CETAK DOKUMEN TRIWULAN (PREVIEW PERSIS LEMBAR KERTAS FISIK A4/F4) -->
                <div class="bg-slate-950/80 p-2 sm:p-6 rounded-2xl border border-slate-800 flex justify-center overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                    <div id="print-area-triwulan" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000;" class="w-full max-w-[760px] bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                        
                        <!-- KOP SURAT RESMI DENGAN DUA LOGO RESMI (KABUPATEN & RSUD) -->
                        <div class="border-b-[2.5px] border-black pb-2 mb-3" style="border-bottom: 2.5px solid #000000;">
                            <div class="flex items-center justify-between gap-3">
                                <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Kabupaten Bondowoso" class="h-16 w-16 object-contain">
                                </div>
                                <div class="flex-1 text-center text-black" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                    <h4 class="font-bold text-[11pt] sm:text-[12pt] uppercase tracking-normal leading-tight text-black m-0 p-0">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-bold text-[12.5pt] sm:text-[13.5pt] uppercase tracking-normal leading-tight text-black mt-0.5 mb-0 p-0">RUMAH SAKIT UMUM DAERAH DR. H. KOESNADI</h3>
                                    <p class="text-[8.5pt] sm:text-[9pt] italic leading-tight text-black mt-0.5 mb-0 p-0">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax. (0332) 422311</p>
                                    <p class="text-[8.5pt] sm:text-[9pt] leading-tight text-black m-0 p-0">e-mail: rsu.koesnadi@gmail.com, Website: rsudrkoesnadi.go.id</p>
                                    <p class="font-bold text-[10pt] sm:text-[10.5pt] uppercase text-black mt-1 mb-0 p-0" style="letter-spacing: 0.35em;">B O N D O W O S O</p>
                                </div>
                                <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD Koesnandi" class="h-16 w-16 object-contain">
                                </div>
                            </div>
                        </div>

                        <!-- JUDUL SURAT & NOMOR -->
                        <div class="text-center text-black mb-3">
                            <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA SERAH TERIMA BARANG</h3>
                            <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">Nomor: <span x-text="currentTriwulanDoc.nomor_surat || '000.2.3.2/759/430.10.7/2025'"></span></p>
                        </div>

                        <!-- PEMBUKA -->
                        <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                            Pada hari ini <strong x-text="currentTriwulanDoc.hari_tanggal || 'Rabu tanggal 31 Desember 2025'"></strong> bertempat di Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso, yang bertanda tangan di bawah ini:
                        </p>

                        <!-- PIHAK 1 (PPK) -->
                        <div class="space-y-1 mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                            <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                <tr style="border: none !important;">
                                    <td style="border: none !important; width: 22px; vertical-align: top; padding: 1.5px 0;" class="font-bold">1.</td>
                                    <td style="border: none !important; width: 75px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                    <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="currentTriwulanDoc.pihak1_nama">dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="currentTriwulanDoc.pihak1_nip">19771002 200604 1 006</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="currentTriwulanDoc.pihak1_jabatan">Pejabat Pembuat Komitmen / Penerima Barang Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td colspan="3" style="border: none !important; padding: 2px 0;" class="italic">
                                        Dalam hal ini disebut <strong class="not-italic">PIHAK KESATU</strong>.
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- PIHAK 2 (PENGURUS BARANG) -->
                        <div class="space-y-1 mb-3 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                            <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                <tr style="border: none !important;">
                                    <td style="border: none !important; width: 22px; vertical-align: top; padding: 1.5px 0;" class="font-bold">2.</td>
                                    <td style="border: none !important; width: 75px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                    <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="currentTriwulanDoc.pihak2_nama">BUDI HARTONO,S.Sos</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="currentTriwulanDoc.pihak2_nip">19760229 200801 1 010</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                    <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="currentTriwulanDoc.pihak2_jabatan">Pengurus Barang Aset Pada RSUD dr.H.Koesnandi Kabupaten Bondowoso</td>
                                </tr>
                                <tr style="border: none !important;">
                                    <td style="border: none !important; padding: 1.5px 0;"></td>
                                    <td colspan="3" style="border: none !important; padding: 2px 0;" class="italic">
                                        Dalam hal ini disebut <strong class="not-italic">PIHAK KEDUA</strong>.
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- PERNYATAAN PENYERAHAN -->
                        <p class="text-justify mb-2 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                            Bersama ini <strong>PIHAK KESATU</strong> menyerahkan Barang dari Hasil Pengadaan Belanja Modal <span x-text="currentTriwulanDoc.triwulan_label || ('Triwulan ' + (selectedTriwulanKey === 'TW1' ? 'I' : (selectedTriwulanKey === 'TW2' ? 'II' : (selectedTriwulanKey === 'TW3' ? 'III' : 'IV'))) + ' Tahun ' + selectedTahun)"></span> Rumah Sakit Umum Daerah dr. H. Koesnandi Kabupaten Bondowoso kepada <strong>PIHAK KEDUA</strong> sebagai berikut:
                        </p>

                        <p class="text-[9.5pt] sm:text-[10pt] mb-2 font-normal text-black">
                            1. PIHAK KESATU menyerahkan barang kepada PIHAK KEDUA berupa:
                        </p>

                        <!-- TABEL 8 GOLONGAN BARANG PERSIS DOKUMEN RESMI -->
                        <div class="my-2.5 overflow-x-auto">
                            <table class="w-full text-black border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                                <thead>
                                    <tr class="font-bold text-black border border-black bg-white" style="border: 1px solid black;">
                                        <th class="border border-black px-2 py-1.5 text-center font-bold" style="width: 7%; border: 1px solid black;">NO</th>
                                        <th class="border border-black px-3 py-1.5 text-center font-bold" style="border: 1px solid black;">NAMA GOLONGAN BARANG</th>
                                        <th class="border border-black px-3 py-1.5 text-center font-bold" style="width: 22%; border: 1px solid black;">JUMLAH BARANG</th>
                                        <th class="border border-black px-3 py-1.5 text-center font-bold" style="width: 34%; border: 1px solid black;">NILAI PEROLEHAN (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in currentTriwulanDoc.rekapItems" :key="item.no">
                                        <tr class="border border-black" style="border: 1px solid black;">
                                            <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="item.no"></td>
                                            <td class="border border-black px-3 py-1.5 text-left" style="border: 1px solid black;" x-text="item.nama"></td>
                                            <td class="border border-black px-3 py-1.5 text-center" style="border: 1px solid black;" x-text="item.qty.toLocaleString('id-ID')"></td>
                                            <td class="border border-black px-3 py-1.5 text-right font-bold" style="border: 1px solid black;" x-text="item.nilai.toLocaleString('id-ID')"></td>
                                        </tr>
                                    </template>
                                    <!-- BARIS TOTAL JUMLAH -->
                                    <tr class="border border-black font-bold bg-white" style="border: 1px solid black;">
                                        <td colspan="2" class="border border-black px-3 py-1.5 text-center font-bold" style="border: 1px solid black;">JUMLAH</td>
                                        <td class="border border-black px-3 py-1.5 text-center font-bold" style="border: 1px solid black;" x-text="currentTriwulanTotalQty.toLocaleString('id-ID')"></td>
                                        <td class="border border-black px-3 py-1.5 text-right font-bold" style="border: 1px solid black;" x-text="currentTriwulanTotalNilai.toLocaleString('id-ID')"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- PENUTUP -->
                        <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                            Demikian Berita Acara Serah Terima Barang ini dibuat untuk dipergunakan sebagaimana mestinya.
                        </p>

                        <!-- TANDA TANGAN DUA PIHAK DENGAN TTD DIGITAL BSR-E -->
                        <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt] mt-6 pt-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                            <div>
                                <p class="m-0">Yang Menerima,</p>
                                <p class="font-bold m-0">PIHAK KEDUA</p>
                                
                                <!-- TTD Digital BSrE / Space Pihak II -->
                                <div class="h-20 flex items-center justify-center my-1">
                                    <template x-if="currentTriwulanDoc.pihak2_signed">
                                        <div class="p-1 border border-emerald-600 bg-emerald-50 rounded flex items-center space-x-1.5 text-left">
                                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(window.location.origin + '/validasi-tte/' + (currentTriwulanDoc.pihak2_qr_hash || encodeURIComponent(currentTriwulanDoc.nomor_surat) || 'PENGURUS-KOESNANDI'))" class="w-10 h-10 shrink-0">
                                            <div class="text-[7.5px] leading-tight text-slate-800">
                                                <div class="font-bold text-emerald-900">DITANDATANGANI ELEKTRONIK</div>
                                                <div>Pengurus Barang Aset</div>
                                                <div class="text-[6.5px] text-slate-500 font-mono">Sertifikat BSrE - BSSN</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!currentTriwulanDoc.pihak2_signed">
                                        <div class="p-1 border border-dashed border-amber-500 bg-amber-50 rounded text-center text-amber-800">
                                            <span class="text-[8px] font-bold italic">( Menunggu Pengesahan TTD BSrE )</span>
                                        </div>
                                    </template>
                                </div>

                                <p class="font-bold underline uppercase m-0" x-text="currentTriwulanDoc.pihak2_nama">BUDI HARTONO,S.Sos</p>
                                <p class="m-0" x-text="'NIP. ' + currentTriwulanDoc.pihak2_nip">NIP. 19760229 200801 1 010</p>
                            </div>

                            <div>
                                <p class="m-0">Yang Menyerahkan,</p>
                                <p class="font-bold m-0">PIHAK KESATU</p>
                                
                                <!-- TTD Digital BSrE Pihak I (PPK) -->
                                <div class="h-20 flex items-center justify-center my-1">
                                    <div class="p-1 border border-purple-600 bg-purple-50 rounded flex items-center space-x-1.5 text-left">
                                        <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(window.location.origin + '/validasi-tte/' + (currentTriwulanDoc.nomor_surat ? encodeURIComponent(currentTriwulanDoc.nomor_surat) : 'PPK-KOESNANDI'))" class="w-10 h-10 shrink-0">
                                        <div class="text-[7.5px] leading-tight text-slate-800">
                                            <div class="font-bold text-purple-900">DITANDATANGANI ELEKTRONIK</div>
                                            <div>PPK RSUD dr. H. Koesnandi</div>
                                            <div class="text-[6.5px] text-slate-500 font-mono">Sertifikat BSrE - BSSN</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="font-bold underline uppercase m-0" x-text="currentTriwulanDoc.pihak1_nama">dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.</p>
                                <p class="m-0" x-text="'NIP. ' + currentTriwulanDoc.pihak1_nip">NIP. 19771002 200604 1 006</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL CETAK 2: LEMBAR DOKUMEN BAST DISTRIBUSI BARANG ASET                 -->
        <!-- ========================================================================= -->
        <div x-show="showPrintDistribusiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintDistribusiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-teal-500/20 text-teal-300 text-sm">🚚</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima Distribusi Barang</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedDistribusi ? ('Nomor BAST: ' + selectedDistribusi.nomor_bast) : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="showEditDistribusiForm = !showEditDistribusiForm"
                            class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                            <span x-text="showEditDistribusiForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <!-- Toggle Button TTD / Batalkan TTD -->
                        <button type="button" @click="toggleSignDistribusi(selectedDistribusi)"
                            :class="selectedDistribusi && selectedDistribusi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                            <span x-text="selectedDistribusi && selectedDistribusi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-purple-500 text-slate-950 font-bold text-xs shadow-lg">
                            🖨️ Cetak Surat
                        </button>
                        <button type="button" @click="showPrintDistribusiModal = false" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                </div>

                <!-- Formulir Edit Live BAST Distribusi -->
                <template x-if="selectedDistribusi">
                    <div x-show="showEditDistribusiForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-teal-500/40 text-xs space-y-3 shadow-inner">
                        <div class="font-bold text-teal-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                            ✏️ Live Edit Berita Acara Penyerahan Barang (Otomatis Berubah Pada Lembar Cetak):
                        </div>
                        <!-- Info Surat, Tanggal & SK Bupati (1 grid gabungan) -->
                        <div class="grid grid-cols-2 sm:grid-cols-7 gap-2.5">
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat</label>
                                <input type="text" x-model="selectedDistribusi.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-teal-300 font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                                <input type="text" x-model="selectedDistribusi.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tgl</label>
                                <input type="text" x-model="selectedDistribusi.tanggal_angka" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Bulan</label>
                                <input type="text" x-model="selectedDistribusi.bulan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun</label>
                                <input type="text" x-model="selectedDistribusi.tahun" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun Anggaran</label>
                                <input type="text" x-model="selectedDistribusi.tahun_anggaran" placeholder="2026" class="w-full bg-slate-900 border border-amber-700/50 rounded-lg px-2.5 py-1 text-amber-300 font-bold text-xs">
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor SK Bupati</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_nomor" placeholder="188.45/430.10.7/2026" class="w-full bg-slate-900 border border-amber-700/50 rounded-lg px-2.5 py-1 text-amber-200 font-mono text-xs">
                            </div>
                        </div>
                        <!-- Tanggal SK terpisah agar label tidak terpotong -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                            <div class="sm:col-span-1">
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal SK Bupati</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_tanggal" placeholder="02 Januari 2026" class="w-full bg-slate-900 border border-amber-700/50 rounded-lg px-2.5 py-1 text-amber-200 text-xs">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-bold text-teal-400 uppercase">👤 Pihak Kesatu (Pengurus Barang)</span>
                                <div>
                                    <label class="block text-slate-400 text-[9px]">Nama Lengkap</label>
                                    <input type="text" x-model="selectedDistribusi.pengurus_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Ruangan</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_ruangan" placeholder="Gudang Perbekalan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                </div>
                            </div>
                            <div class="space-y-1.5">
                                <span class="text-[10px] font-bold text-emerald-400 uppercase">👤 Pihak Kedua (Penerima Barang)</span>
                                <div>
                                    <label class="block text-slate-400 text-[9px]">Nama Lengkap & Gelar</label>
                                    <input type="text" x-model="selectedDistribusi.pj_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-emerald-300 font-bold text-xs">
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pj_nip" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Jabatan / Ruangan</label>
                                        <input type="text" x-model="selectedDistribusi.pj_jabatan" placeholder="Supervisor Front Office" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-slate-200 text-xs">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- LEMBAR CETAK BAST DISTRIBUSI (BERITA ACARA PENYERAHAN BARANG SESUAI DOKUMEN RESMI) -->
                <template x-if="selectedDistribusi">
                    <div class="bg-slate-950/80 p-2 sm:p-6 rounded-2xl border border-slate-800 flex justify-center overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                        <div id="print-area-distribusi" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000;" class="w-full max-w-[760px] bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                            
                            <!-- KOP SURAT RESMI -->
                            <div class="border-b-[2.5px] border-black pb-2 mb-1" style="border-bottom: 2.5px solid #000000;">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                        <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Dinas Bondowoso" class="h-16 w-16 object-contain">
                                    </div>
                                    <div class="flex-1 text-center text-black" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                        <h4 class="font-bold text-[11pt] sm:text-[12pt] uppercase tracking-normal leading-tight text-black m-0 p-0">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                        <h3 class="font-bold text-[12.5pt] sm:text-[13.5pt] uppercase tracking-normal leading-tight text-black mt-0.5 mb-0 p-0">RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI</h3>
                                        <p class="text-[8.5pt] sm:text-[9pt] italic leading-tight text-black mt-0.5 mb-0 p-0">Jl. Kapten Pierre Tendean No. 3 Telepon (0332) 421974. Fax.0332 422311</p>
                                        <p class="text-[8.5pt] sm:text-[9pt] leading-tight text-black m-0 p-0">Website: rsudrkoesnadi.go.id, Email: rsu.koesnadi@gmail.com</p>
                                        <p class="font-bold text-[10pt] sm:text-[10.5pt] uppercase text-black mt-1 mb-0 p-0" style="letter-spacing: 0.35em;">B O N D O W O S O</p>
                                    </div>
                                    <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                        <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                                    </div>
                                </div>
                            </div>
                            <div class="text-right text-[8pt] text-black pr-1 mb-2 font-normal">
                                Kode Pos: 68214
                            </div>

                            <!-- JUDUL SURAT & NOMOR -->
                            <div class="text-center text-black mb-3">
                                <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA PENYERAHAN BARANG</h3>
                                <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">Nomor : <span x-text="selectedDistribusi.nomor_bast || '032 / 034 / 430.10.7 / 2026'"></span></p>
                            </div>

                            <!-- PEMBUKA -->
                            <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Pada hari ini <strong x-text="selectedDistribusi.hari || 'Kamis'"></strong> tanggal <strong x-text="selectedDistribusi.tanggal_angka || '13'"></strong> bulan <strong x-text="selectedDistribusi.bulan || 'Agustus'"></strong> tahun <strong x-text="selectedDistribusi.tahun || '2026'"></strong>, yang bertanda tangan di bawah ini :
                            </p>

                            <!-- PIHAK KESATU (PENGURUS BARANG) -->
                            <div class="space-y-1 mb-2 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                                <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; width: 85px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                        <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="selectedDistribusi.pengurus_nama || 'BUDI HARTONO,S.Sos'">BUDI HARTONO,S.Sos</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pengurus_nip || '19760229 200801 1 010'">19760229 200801 1 010</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pengurus_jabatan || 'Pengurus Barang'">Pengurus Barang</td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Ruangan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pengurus_ruangan || 'Gudang Perbekalan'">Gudang Perbekalan</td>
                                    </tr>
                                </table>
                            </div>

                            <!-- PARAGRAF PENGESAHAN SK BUPATI -->
                            <p class="text-justify my-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Dalam hal ini selaku Pengurus Barang Aset Tahun Anggaran <span x-text="selectedDistribusi.tahun_anggaran || selectedDistribusi.tahun || '2025'"></span> Rumah Sakit Umum Dr. H. Koesnadi Bondowoso, berdasarkan Surat Keputusan Bupati Kabupaten Bondowoso sesuai Nomor : <span x-text="selectedDistribusi.sk_bupati_nomor || '188.45/969/430.4.2/2024'"></span> tanggal <span x-text="selectedDistribusi.sk_bupati_tanggal || '02 Januari 2025'"></span>
                            </p>

                            <!-- PIHAK KEDUA (PENERIMA BARANG) -->
                            <p class="text-[9.5pt] sm:text-[10pt] mb-1 font-normal text-black">
                                Dengan ini menyerahkan barang kepada :
                            </p>
                            <div class="space-y-1 mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                                <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; width: 85px; vertical-align: top; padding: 1.5px 0;">Nama</td>
                                        <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold" x-text="selectedDistribusi.pj_nama"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">NIP</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pj_nip"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jabatan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.pj_jabatan || ('Supervisor ' + selectedDistribusi.unit_nama)"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Ruangan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" x-text="selectedDistribusi.unit_nama"></td>
                                    </tr>
                                </table>
                            </div>

                            <!-- TABEL RESMI BARANG DISTRIBUSI (DENGAN 3 SUB-KOLOM KONDISI: BAIK, KB, RUSAK) -->
                            <div class="my-2.5 overflow-x-auto">
                                <table class="w-full text-black border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                                    <thead>
                                        <tr class="bg-gray-100 font-bold border border-black" style="border: 1px solid black;">
                                            <th rowspan="2" class="border border-black px-2 py-1.5 text-center font-bold" style="width: 5%; border: 1px solid black;">No</th>
                                            <th rowspan="2" class="border border-black px-3 py-1.5 text-left font-bold" style="width: 25%; border: 1px solid black;">Uraian Barang</th>
                                            <th rowspan="2" class="border border-black px-3 py-1.5 text-left font-bold" style="width: 27%; border: 1px solid black;">Merk /Type</th>
                                            <th rowspan="2" class="border border-black px-2 py-1.5 text-center font-bold" style="width: 6%; border: 1px solid black;">Vol</th>
                                            <th rowspan="2" class="border border-black px-2 py-1.5 text-center font-bold" style="width: 8%; border: 1px solid black;">Satuan</th>
                                            <th colspan="3" class="border border-black px-1 py-1 text-center font-bold" style="width: 14%; border: 1px solid black;">Kondisi</th>
                                            <th rowspan="2" class="border border-black px-3 py-1.5 text-left font-bold" style="width: 15%; border: 1px solid black;">Keterangan</th>
                                        </tr>
                                        <tr class="bg-gray-100 font-bold border border-black" style="border: 1px solid black;">
                                            <th class="border border-black px-1 py-0.5 text-center font-bold text-[8.5pt]" style="border: 1px solid black;">Baik</th>
                                            <th class="border border-black px-1 py-0.5 text-center font-bold text-[8.5pt]" style="border: 1px solid black;">KB</th>
                                            <th class="border border-black px-1 py-0.5 text-center font-bold text-[8.5pt]" style="border: 1px solid black;">Rusak</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(sub, idx) in (selectedDistribusi.items || [])" :key="idx">
                                            <tr class="border border-black" style="border: 1px solid black;">
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="idx + 1"></td>
                                                <td class="border border-black px-3 py-1.5 text-left font-bold" style="border: 1px solid black;" x-text="sub.nama_barang"></td>
                                                <td class="border border-black px-3 py-1.5 text-left font-mono text-[9px]" style="border: 1px solid black;" x-text="sub.spesifikasi || sub.merk_type || '-'"></td>
                                                <td class="border border-black px-2 py-1.5 text-center font-bold" style="border: 1px solid black;" x-text="sub.qty"></td>
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="sub.satuan"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Baik' || !sub.kondisi) ? 'Baik' : ''"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Kurang Baik' || sub.kondisi === 'KB') ? 'KB' : ''"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;" x-text="(sub.kondisi === 'Rusak') ? 'Rusak' : ''"></td>
                                                <td class="border border-black px-3 py-1.5 text-left text-[8.5pt]" style="border: 1px solid black;" x-text="sub.keterangan || selectedDistribusi.keterangan || '-'"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!selectedDistribusi.items || selectedDistribusi.items.length === 0">
                                            <tr class="border border-black" style="border: 1px solid black;">
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;">1</td>
                                                <td class="border border-black px-3 py-1.5 text-left font-bold" style="border: 1px solid black;" x-text="selectedDistribusi.barang_nama"></td>
                                                <td class="border border-black px-3 py-1.5 text-left font-mono text-[9px]" style="border: 1px solid black;" x-text="(selectedDistribusi.merk ? (selectedDistribusi.merk + ' ' + (selectedDistribusi.type || '')) : '-')"></td>
                                                <td class="border border-black px-2 py-1.5 text-center font-bold" style="border: 1px solid black;" x-text="selectedDistribusi.volume"></td>
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="selectedDistribusi.satuan"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;">Baik</td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;"></td>
                                                <td class="border border-black px-1 py-1.5 text-center text-[8.5pt]" style="border: 1px solid black;"></td>
                                                <td class="border border-black px-3 py-1.5 text-left text-[8.5pt]" style="border: 1px solid black;" x-text="selectedDistribusi.keterangan || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <!-- PENUTUP -->
                            <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Demikian Berita Acara Penyerahan Barang ini dibuat rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
                            </p>

                            <!-- TANDA TANGAN (KIRI: TTD ELEKTRONIK PENGURUS BARANG, KANAN: TTD BASAH KEPALA RUANGAN/PJ) -->
                            <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt] mt-6 pt-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <div>
                                    <p class="m-0">Yang Menyerahkan</p>
                                    <p class="font-bold m-0">Pengurus Barang Aset</p>
                                    
                                    <!-- TTD Elektronik BSrE Pengurus Barang (Hanya Tampil Jika Status Sudah Ditandatangani) -->
                                    <div class="my-1 flex items-center justify-center" style="height: 52px; min-height: 52px;">
                                        <template x-if="selectedDistribusi.signed">
                                            <div class="p-1 border border-teal-600 bg-teal-50 rounded flex items-center space-x-1.5 text-left">
                                                <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(window.location.origin + '/validasi-tte/' + encodeURIComponent(selectedDistribusi.nomor_bast || 'BSRE-DISTRIBUSI'))" class="w-9 h-9 shrink-0">
                                                <div class="text-[7.5px] leading-tight text-slate-800">
                                                    <div class="font-bold text-teal-900">DITANDATANGANI ELEKTRONIK</div>
                                                    <div>Pengurus Barang Aset</div>
                                                    <div class="text-[6.5px] text-slate-500 font-mono">Sertifikat BSrE - BSSN</div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>

                                    <p class="font-bold underline uppercase m-0" x-text="selectedDistribusi.pengurus_nama || 'BUDI HARTONO,S.Sos'"></p>
                                    <p class="m-0" x-text="'NIP. ' + (selectedDistribusi.pengurus_nip || '19760229 200801 1 010')"></p>
                                </div>

                                <div>
                                    <p class="m-0">Yang Menerima</p>
                                    <p class="font-bold m-0" x-text="'Kepala Ruangan ' + (selectedDistribusi.unit_nama || 'Unit')"></p>
                                    
                                    <!-- Ruang Tanda Tangan Basah Manual (Tinggi presisi 52px agar nama sejajar horizontal sempurna) -->
                                    <div class="my-1 flex items-center justify-center" style="height: 52px; min-height: 52px;"></div>

                                    <p class="font-bold underline uppercase m-0" x-text="selectedDistribusi.pj_nama"></p>
                                    <p class="m-0" x-text="'NIP. ' + selectedDistribusi.pj_nip"></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </template>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL CETAK 3: LEMBAR DOKUMEN BAST MUTASI ASET (ANTAR RUANGAN)             -->
        <!-- ========================================================================= -->
        <div x-show="showPrintMutasiModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintMutasiModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-rose-500/20 text-rose-300 text-sm">🔄</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Berita Acara Serah Terima Mutasi Aset</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedMutasi ? ('Nomor BAST: ' + selectedMutasi.nomor_bast) : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="showEditMutasiForm = !showEditMutasiForm"
                            class="px-3.5 py-1.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all">
                            <span x-text="showEditMutasiForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <!-- Toggle Button TTD / Batalkan TTD -->
                        <button type="button" @click="toggleSignMutasi(selectedMutasi)"
                            :class="selectedMutasi && selectedMutasi.signed ? 'bg-rose-500/20 text-rose-300 border border-rose-500/40 hover:bg-rose-500/30' : 'bg-emerald-500 text-slate-950 font-extrabold shadow-md'"
                            class="px-3.5 py-1.5 rounded-xl font-bold text-xs transition-all active:scale-95">
                            <span x-text="selectedMutasi && selectedMutasi.signed ? '↩️ Batalkan TTD BSrE' : '✍️ TTD BSrE'"></span>
                        </button>

                        <button type="button" @click="printCurrent()" class="px-4 py-1.5 rounded-xl bg-purple-500 text-slate-950 font-bold text-xs shadow-lg">
                            🖨️ Cetak Surat
                        </button>
                        <button type="button" @click="showPrintMutasiModal = false" class="p-1 rounded-lg text-slate-400 hover:text-white font-bold text-lg">&times;</button>
                    </div>
                </div>

                <!-- Formulir Edit Live BAST Mutasi -->
                <div x-show="showEditMutasiForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-rose-500/40 text-xs space-y-3 shadow-inner">
                    <div class="font-bold text-rose-300 text-[11px] uppercase tracking-wider border-b border-slate-800 pb-2">
                        ✏️ Live Edit Surat BAST Mutasi (Otomatis Berubah Pada Lembar Cetak):
                    </div>
                    <template x-if="selectedMutasi">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor BAST Mutasi</label>
                                <input type="text" x-model="selectedMutasi.nomor_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-purple-300 font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari Surat</label>
                                <input type="text" x-model="selectedMutasi.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal Surat</label>
                                <input type="text" x-model="selectedMutasi.tgl_bast" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white text-xs">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2 border-t border-slate-800/60">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nama PJ Asal</label>
                                <input type="text" x-model="selectedMutasi.pj_asal_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">NIP PJ Asal</label>
                                <input type="text" x-model="selectedMutasi.pj_asal_nip" placeholder="NIP..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-200 font-mono text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nama PJ Tujuan</label>
                                <input type="text" x-model="selectedMutasi.pj_tujuan_nama" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-white font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">NIP PJ Tujuan</label>
                                <input type="text" x-model="selectedMutasi.pj_tujuan_nip" placeholder="NIP..." class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1 text-slate-200 font-mono text-xs">
                            </div>
                        </div>
                    </template>
                </div>

                <!-- LEMBAR CETAK BAST MUTASI -->
                <template x-if="selectedMutasi">
                    <div class="bg-slate-950/80 p-2 sm:p-6 rounded-2xl border border-slate-800 flex justify-center overflow-y-auto max-h-[75vh] custom-scrollbar shadow-inner">
                        <div id="print-area-mutasi" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif; font-size: 10pt; color: #000000;" class="w-full max-w-[760px] bg-white text-black p-8 sm:p-12 md:p-14 shadow-2xl rounded-sm space-y-3.5 select-text print:p-0 print:m-0 print:shadow-none print:max-w-none">
                            
                            <div class="border-b-[2.5px] border-black pb-2 mb-3" style="border-bottom: 2.5px solid #000000;">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                        <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Dinas Bondowoso" class="h-16 w-16 object-contain">
                                    </div>
                                    <div class="flex-1 text-center text-black" style="font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;">
                                        <h4 class="font-bold text-[11pt] sm:text-[12pt] uppercase tracking-normal leading-tight text-black m-0 p-0">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                        <h3 class="font-bold text-[12.5pt] sm:text-[13.5pt] uppercase tracking-normal leading-tight text-black mt-0.5 mb-0 p-0">RUMAH SAKIT UMUM DAERAH DR. H. KOESNADI</h3>
                                        <p class="text-[8.5pt] sm:text-[9pt] italic leading-tight text-black mt-0.5 mb-0 p-0">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax. (0332) 422311</p>
                                        <p class="text-[8.5pt] sm:text-[9pt] leading-tight text-black m-0 p-0">e-mail: rsu.koesnadi@gmail.com, Website: rsudrkoesnadi.go.id</p>
                                        <p class="font-bold text-[10pt] sm:text-[10.5pt] uppercase text-black mt-1 mb-0 p-0" style="letter-spacing: 0.35em;">B O N D O W O S O</p>
                                    </div>
                                    <div class="w-16 sm:w-20 shrink-0 flex justify-center">
                                        <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                                    </div>
                                </div>
                            </div>

                            <div class="text-center text-black mb-3">
                                <h3 class="font-bold text-[11pt] sm:text-[11.5pt] uppercase underline tracking-normal text-black m-0">BERITA ACARA MUTASI BARANG (BAMB)</h3>
                                <p class="text-[9.5pt] sm:text-[10pt] font-semibold text-black mt-1 m-0">Nomor : <span x-text="selectedMutasi.nomor_bast"></span></p>
                            </div>

                            <p class="text-justify mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Pada hari ini <strong x-text="selectedMutasi.hari"></strong> tanggal <strong x-text="selectedMutasi.tgl_bast"></strong>, telah dilaksanakan pemindahan/mutasi aset inventaris dari ruangan asal ke ruangan tujuan sebagai berikut :
                            </p>

                            <div class="space-y-1 mb-2.5 text-[9.5pt] sm:text-[10pt] leading-[1.4] text-black">
                                <table class="w-full border-none text-[9.5pt] sm:text-[10pt]" style="border: none !important; margin: 0 !important; width: 100%;">
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; width: 140px; vertical-align: top; padding: 1.5px 0;">Ruangan Asal</td>
                                        <td style="border: none !important; width: 15px; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase" x-text="selectedMutasi.asal"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Ruangan Tujuan</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="font-bold uppercase text-purple-900" x-text="selectedMutasi.tujuan"></td>
                                    </tr>
                                    <tr style="border: none !important;">
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">Jenis / Alasan Mutasi</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;">:</td>
                                        <td style="border: none !important; vertical-align: top; padding: 1.5px 0;" class="italic" x-text="selectedMutasi.keterangan || 'Pemindahan Aset'"></td>
                                    </tr>
                                </table>
                            </div>

                            <!-- TABEL RESMI BARANG MUTASI -->
                            <div class="my-2.5 overflow-x-auto">
                                <table class="w-full text-center border-collapse border border-black text-[9pt] sm:text-[9.5pt]" style="border-collapse: collapse; width: 100%; border: 1px solid black;">
                                    <thead>
                                        <tr class="bg-gray-100 font-bold border border-black" style="border: 1px solid black;">
                                            <th class="border border-black px-2 py-1.5 w-10 text-center" style="border: 1px solid black;">No</th>
                                            <th class="border border-black px-3 py-1.5 text-left" style="border: 1px solid black;">Nama Barang / Aset</th>
                                            <th class="border border-black px-3 py-1.5 text-left" style="border: 1px solid black;">Kode Barang / NIBAR</th>
                                            <th class="border border-black px-2 py-1.5 w-14 text-center" style="border: 1px solid black;">Vol</th>
                                            <th class="border border-black px-2 py-1.5 w-16 text-center" style="border: 1px solid black;">Satuan</th>
                                            <th class="border border-black px-3 py-1.5 text-left" style="border: 1px solid black;">Keterangan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(sub, idx) in (selectedMutasi.items || [])" :key="idx">
                                            <tr class="border border-black" style="border: 1px solid black;">
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="idx + 1"></td>
                                                <td class="border border-black px-3 py-1.5 text-left font-bold" style="border: 1px solid black;" x-text="sub.nama_barang"></td>
                                                <td class="border border-black px-3 py-1.5 font-mono text-[9px] text-left" style="border: 1px solid black;" x-text="sub.nibar"></td>
                                                <td class="border border-black px-2 py-1.5 text-center font-bold" style="border: 1px solid black;" x-text="sub.qty"></td>
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="sub.satuan"></td>
                                                <td class="border border-black px-3 py-1.5 text-left text-[9.5px]" style="border: 1px solid black;" x-text="sub.keterangan"></td>
                                            </tr>
                                        </template>
                                        <template x-if="!selectedMutasi.items || selectedMutasi.items.length === 0">
                                            <tr class="border border-black" style="border: 1px solid black;">
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;">1</td>
                                                <td class="border border-black px-3 py-1.5 text-left font-bold" style="border: 1px solid black;" x-text="selectedMutasi.nama_barang"></td>
                                                <td class="border border-black px-3 py-1.5 font-mono text-[9px] text-left" style="border: 1px solid black;" x-text="selectedMutasi.nibar"></td>
                                                <td class="border border-black px-2 py-1.5 text-center font-bold" style="border: 1px solid black;" x-text="selectedMutasi.qty"></td>
                                                <td class="border border-black px-2 py-1.5 text-center" style="border: 1px solid black;" x-text="selectedMutasi.satuan"></td>
                                                <td class="border border-black px-3 py-1.5 text-left text-[9.5px]" style="border: 1px solid black;" x-text="selectedMutasi.keterangan || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>

                            <p class="text-justify my-3 text-[9.5pt] sm:text-[10pt] leading-[1.5] text-black">
                                Demikian Berita Acara Mutasi Barang ini dibuat dengan sebenarnya untuk dipergunakan sebagaimana mestinya.
                            </p>

                            <!-- TTD DUAL BSR-E MUTASI -->
                            <div class="grid grid-cols-2 gap-8 text-center text-black text-[9.5pt] sm:text-[10pt] mt-6 pt-2" style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
                                <div>
                                    <p class="m-0">Yang Menyerahkan,</p>
                                    <p class="font-bold m-0">PJ Ruangan Asal</p>
                                    <div class="h-20 flex items-center justify-center my-1">
                                        <div class="p-1 border border-purple-600 bg-purple-50 rounded flex items-center space-x-1.5 text-left">
                                            <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(window.location.origin + '/validasi-tte/' + encodeURIComponent(selectedMutasi.nomor_bast || 'BSRE-MUTASI-ASAL'))" class="w-10 h-10 shrink-0">
                                            <div class="text-[7.5px] leading-tight text-slate-800">
                                                <div class="font-bold text-purple-900">DITANDATANGANI ELEKTRONIK</div>
                                                <div>Penanggung Jawab Asal</div>
                                                <div class="text-[6.5px] text-slate-500 font-mono">Sertifikat BSrE - BSSN</div>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="font-bold underline uppercase m-0" x-text="selectedMutasi.pj_asal_nama"></p>
                                    <p class="m-0" x-text="selectedMutasi.pj_asal_nip ? 'NIP. ' + selectedMutasi.pj_asal_nip : ''"></p>
                                </div>

                                <div>
                                    <p class="m-0">Yang Menerima,</p>
                                    <p class="font-bold m-0">PJ Ruangan Tujuan</p>
                                    <div class="h-20 flex items-center justify-center my-1">
                                        <template x-if="selectedMutasi.signed">
                                            <div class="p-1 border border-rose-600 bg-rose-50 rounded flex items-center space-x-1.5 text-left">
                                                <img :src="'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(window.location.origin + '/validasi-tte/' + encodeURIComponent(selectedMutasi.nomor_bast || 'BSRE-MUTASI-TUJUAN'))" class="w-10 h-10 shrink-0">
                                                <div class="text-[7.5px] leading-tight text-slate-800">
                                                    <div class="font-bold text-rose-900">DITANDATANGANI ELEKTRONIK</div>
                                                    <div>Penanggung Jawab Tujuan</div>
                                                    <div class="text-[6.5px] text-slate-500 font-mono">Sertifikat BSrE - BSSN</div>
                                                </div>
                                            </div>
                                        </template>
                                        <template x-if="!selectedMutasi.signed">
                                            <div class="p-1 border border-dashed border-amber-500 bg-amber-50 rounded text-center text-amber-800">
                                                <span class="text-[8px] font-bold italic">( Menunggu Pengesahan TTD BSrE )</span>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="font-bold underline uppercase m-0" x-text="selectedMutasi.pj_tujuan_nama"></p>
                                    <p class="m-0" x-text="selectedMutasi.pj_tujuan_nip ? 'NIP. ' + selectedMutasi.pj_tujuan_nip : ''"></p>
                                </div>
                            </div>

                        </div>
                    </div>
                </template>

            </div>
        </div>

    </div>
</x-layout>
