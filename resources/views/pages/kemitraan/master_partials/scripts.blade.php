<!-- ========================================================================= -->
<!-- SCRIPTS: LOGIKA STATE MANAGEMENT MASTER KEMITRAAN ASET (AKUN 1.5.2)        -->
<!-- ========================================================================= -->
<script>
    function masterKemitraan() {
        return {
            // Modal Detail States
            showDetailModal: false,
            selectedAstapDetail: null,
            activeDetail: {
                kemitraan: null,
                astap: null,
                register: null
            },
            detailKondisiFilter: 'all',
            detailPenempatanFilter: 'all',
            detailSearchQuery: '',

            // QR Code Modal States
            showQrModal: false,
            selectedQrItem: null,
            isGeneratingQr: false,
            qrDataUrl: '',

            // Status Update Form State
            statusForm: {
                id: null,
                status_konsesi: 'Aktif',
                keterangan: ''
            },
            isUpdatingStatus: false,

            // Modal Delete States
            showDeleteModal: false,
            deleteItem: {
                id: null,
                nama: ''
            },
            isDeleting: false,

            // Global Custom Confirmation Modal State
            showConfirmModal: false,
            confirmData: {
                title: '',
                message: '',
                itemName: '',
                itemDetails: null,
                type: 'warning',
                btnText: '',
                isBlocked: false,
                actionUrl: null,
                actionText: null,
                assetWarning: null,
                onConfirm: null
            },

            // Modal Export Excel Kemitraan (Akun 1.5.2) States
            showExportModal: false,
            exportYear: 'all',
            exportTriwulan: 'all',
            exportKemitraanSkema: 'all',
            exportKemitraanCategory: 'all',
            isSubmittingExport: false,

            openExportModal() {
                this.exportKemitraanSkema = 'all';
                this.exportKemitraanCategory = 'all';
                this.exportYear = 'all';
                this.exportTriwulan = 'all';
                this.showExportModal = true;
            },

            get availableYears() {
                const yearsSet = new Set();
                const rawList = window.__simatAstaps || [];
                rawList.forEach(item => {
                    const yr = parseInt(item.tahun_perolehan || (item.kemitraan && item.kemitraan.tahun));
                    if (!isNaN(yr)) yearsSet.add(yr);
                });
                yearsSet.add(new Date().getFullYear());
                return Array.from(yearsSet).sort((a, b) => b - a);
            },

            get exportFilteredCount() {
                const rawList = window.__simatAstaps || [];
                const fYear = this.exportYear;
                const fTw = this.exportTriwulan;
                const fSkema = this.exportKemitraanSkema;
                const fCat = this.exportKemitraanCategory;

                const isTwMatch = (itemTw, targetTw) => {
                    if (targetTw === 'all') return true;
                    const targetKey = String(targetTw).replace(/[\s_]/g, '').toUpperCase();
                    const curTw = (itemTw || 'TWI').replace(/[\s_]/g, '').toUpperCase();
                    return (curTw === targetKey) ||
                           (targetKey === 'TWI' && curTw === 'TW1') || (targetKey === 'TW1' && curTw === 'TWI') ||
                           (targetKey === 'TWII' && curTw === 'TW2') || (targetKey === 'TW2' && curTw === 'TWII') ||
                           (targetKey === 'TWIII' && curTw === 'TW3') || (targetKey === 'TW3' && curTw === 'TWIII') ||
                           (targetKey === 'TWIV' && curTw === 'TW4') || (targetKey === 'TW4' && curTw === 'TWIV');
                };

                return rawList.filter(item => {
                    const itemYear = (item.kemitraan && item.kemitraan.tahun) || item.tahun_perolehan;
                    const matchYear = fYear === 'all' || String(itemYear) === String(fYear);

                    const itemTw = (item.kemitraan && item.kemitraan.triwulan) || item.triwulan || 'TWI';
                    const matchTw = isTwMatch(itemTw, fTw);

                    let matchSkema = true;
                    if (fSkema !== 'all') {
                        const rawSkema = (item.kemitraan && item.kemitraan.skema_kemitraan) 
                            || (item.spesifikasi_json && item.spesifikasi_json.skema_kemitraan) 
                            || '';
                        matchSkema = typeof normalizeSkemaKemitraan === 'function' ? (normalizeSkemaKemitraan(rawSkema) === fSkema) : true;
                    }

                    let matchCat = true;
                    if (fCat !== 'all' && fCat !== 'REKAP') {
                        let spec = item.spesifikasi_json || {};
                        if (typeof spec === 'string') {
                            try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                        }
                        const isItemExtracom = !!item.is_extracomtable || 
                                               (item.category && item.category.toUpperCase() === 'EXTRACOM') || 
                                               (spec && spec.is_extracomtable) ||
                                               (Array.isArray(spec?.mesin_items) && spec.mesin_items.some(m => !!m.is_extracom)) ||
                                               (Array.isArray(spec?.lainnya_items) && spec.lainnya_items.some(l => !!l.is_extracom));
                        if (fCat === 'EXTRACOM') {
                            matchCat = isItemExtracom;
                        } else {
                            if (isItemExtracom) {
                                matchCat = false;
                            } else {
                                const itemCat = typeof resolveItemCategory === 'function' ? resolveItemCategory(item) : item.category;
                                matchCat = (itemCat === fCat);
                            }
                        }
                    }

                    return matchYear && matchTw && matchSkema && matchCat;
                }).length;
            },

            submitExport() {
                this.isSubmittingExport = true;
                try {
                    exportKemitraanToExcel({
                        year: this.exportYear,
                        triwulan: this.exportTriwulan,
                        skema: this.exportKemitraanSkema,
                        category: this.exportKemitraanCategory
                    });
                    setTimeout(() => {
                        this.isSubmittingExport = false;
                        this.showExportModal = false;
                        const catLabel = this.exportKemitraanCategory === 'all'
                            ? 'Lengkap (7 Sheet: Rekap, KIB A-E & Extracom)'
                            : (this.exportKemitraanCategory === 'REKAP' ? 'Rekapitulasi' : (this.exportKemitraanCategory === 'EXTRACOM' ? 'Extracom' : this.exportKemitraanCategory));
                        this.showToast('Berhasil mengekspor Laporan Aset Kemitraan ' + catLabel + ' (' + (this.exportTriwulan === 'all' ? 'Tahunan' : this.exportTriwulan) + ') ' + (this.exportYear === 'all' ? 'Semua Tahun' : this.exportYear) + '!', 'success');
                    }, 1000);
                } catch (err) {
                    console.error('Error ekspor excel kemitraan:', err);
                    this.isSubmittingExport = false;
                    if (typeof isExportingKemitraan !== 'undefined') isExportingKemitraan = false;
                    this.showToast('Gagal mengekspor file: ' + (err.message || 'Terjadi kesalahan sistem'), 'error');
                }
            },

            // Global Toast Notification State
            toast: {
                show: false,
                message: '',
                type: 'success'
            },

            // Modal Edit Kondisi State
            showEditKondisiModal: false,
            editingRegisterItem: null,
            newKondisiValue: 'Baik',
            isSavingKondisi: false,

            // Modal Cek Riwayat Mutasi State
            showRiwayatModal: false,
            selectedRiwayatRegister: null,
            selectedRiwayatMutasis: [],
            isLoadingRiwayat: false,

            // Helpers Formatters
            formatRupiah(value) {
                if (value === null || value === undefined || isNaN(value)) return '0';
                return new Intl.NumberFormat('id-ID').format(value);
            },

            formatTanggal(dateString) {
                if (!dateString) return '-';
                try {
                    const d = new Date(dateString);
                    if (isNaN(d.getTime())) return dateString;
                    return d.toLocaleDateString('id-ID', {
                        day: '2-digit',
                        month: 'short',
                        year: 'numeric'
                    });
                } catch (e) {
                    return dateString;
                }
            },

            formatTanggalIndo(dateStr) {
                if (!dateStr || dateStr === '-') return '-';
                if (String(dateStr).length === 4) return '01 Jan ' + dateStr;
                try {
                    let str = String(dateStr).trim();
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

            // Buka Modal Detail & Siapkan Data Terstandar ASTAP
            openDetail(kemitraan, astap, register) {
                let spec = astap?.spesifikasi_json || {};
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }

                // Deteksi Kategori KIB yang sesuai
                let category = this.getEffectiveKibCategory(astap);

                // Normalisasi Data Register NIBAR
                const cleanModalRuang = (val) => {
                    if (!val) return '';
                    const s = String(val).trim();
                    if (s.includes('Piere Tendean') || s.includes('RSUD Dr. H. Koesnandi Bondowoso, Jl') || ['Belum Ditempatkan / Di Gudang', 'Gudang Aset', '-'].includes(s)) {
                        return '';
                    }
                    return s;
                };

                let registers = [];
                if (astap?.registers && Array.isArray(astap.registers) && astap.registers.length > 0) {
                    registers = astap.registers.map(r => ({
                        id: r.id,
                        nibar: r.nibar || r.no_register || '-',
                        no_register: r.no_register || r.nibar || '-',
                        no_register_int: r.no_register_int || parseInt((r.nibar || r.no_register || '').slice(-7)) || 0,
                        ruang_pemegang: cleanModalRuang(r.ruang_pemegang || r.unit?.nama || astap?.unit?.nama || (register?.ruang_pemegang || '')),
                        kondisi: r.kondisi || 'Baik',
                        created_at: r.created_at
                    }));
                } else if (register && Object.keys(register).length > 0) {
                    registers = [{
                        id: register.id || 1,
                        nibar: register.nibar || register.no_register || '-',
                        no_register: register.no_register || register.nibar || '-',
                        no_register_int: register.no_register_int || 1,
                        ruang_pemegang: cleanModalRuang(register.ruang_pemegang || astap?.unit?.nama || ''),
                        kondisi: register.kondisi || 'Baik',
                        created_at: register.created_at
                    }];
                }

                // Urutkan register NIBAR ascending
                registers.sort((a, b) => {
                    const numA = a.no_register_int || parseInt((a.nibar || a.no_register || '').slice(-7)) || 0;
                    const numB = b.no_register_int || parseInt((b.nibar || b.no_register || '').slice(-7)) || 0;
                    if (numA !== numB) return numA - numB;
                    return (a.nibar || a.no_register || '').localeCompare(b.nibar || b.no_register || '');
                });

                const nilaiAsetNum = parseFloat(kemitraan?.nilai_aset || astap?.total_realisasi || 0);
                const volumeNum = parseInt(kemitraan?.jumlah_volume || astap?.jumlah_volume || (registers.length > 0 ? registers.length : 1));
                const satuanStr = kemitraan?.satuan || astap?.satuan || 'Unit';

                this.selectedAstapDetail = {
                    id: astap?.id || kemitraan?.astap_id,
                    kemitraan_id: kemitraan?.id,
                    nama_barang: astap?.nama_barang || 'Aset Kemitraan Pihak Ketiga',
                    kode_barang: astap?.kode_barang || kemitraan?.nomor_pks || '1.5.2',
                    category: category,
                    sumber_dana: 'kemitraan',
                    sumber_dana_label: '🤝 KEMITRAAN AKUN 1.5.2',
                    tahun_perolehan: kemitraan?.tahun || astap?.tahun_perolehan || new Date().getFullYear(),
                    triwulan: kemitraan?.triwulan || astap?.triwulan || 'TW I',
                    jumlah_volume: volumeNum,
                    satuan: satuanStr,
                    volume_satuan: volumeNum + ' ' + satuanStr,
                    nilai_aset: nilaiAsetNum,
                    jumlah_realisasi: 'Rp ' + this.formatRupiah(nilaiAsetNum),
                    total_realisasi: 'Rp ' + this.formatRupiah(nilaiAsetNum),
                    jenis_aset_nama: astap?.jenis_astap?.nama_sub_sub_rincian_objek || astap?.jenis_astap?.nama || 'Aset Kemitraan (Akun 1.5.2)',
                    spesifikasi_json: spec,
                    registers: registers,
                    alamat_barang: astap?.alamat_barang || (register?.ruang_pemegang ? ('Ruang ' + register.ruang_pemegang + ' RSUD Dr. H. Koesnandi') : 'RSUD Dr. H. Koesnandi'),
                    kemitraan: kemitraan || {},

                    // Informasi Mitra Rekanan (Kolom 22 & Kolom 23)
                    penyedia_nama: kemitraan?.mitra_nama || spec.mitra_nama || '-',
                    penyedia_pemilik: kemitraan?.mitra_pimpinan || spec.mitra_pimpinan || '-',
                    penyedia_alamat: kemitraan?.mitra_alamat || spec.mitra_alamat || '-',
                    penyedia_telepon: spec.mitra_telepon || spec.penyedia_telepon || spec.penyedia_kontak || '-',

                    // Informasi Pejabat Pembuat Komitmen (Kolom 24 & Kolom 25)
                    ppk_nama: astap?.ppk_nama || spec.ppk_nama || '-',
                    ppk_nip: astap?.ppk_nip || spec.ppk_nip || '-',
                    keterangan: kemitraan?.keterangan || astap?.keterangan_tambahan || spec.keterangan || '-',

                    // Informasi Legalitas PKS & Konsesi
                    nomor_pks: kemitraan?.nomor_pks || spec.nomor_pks || '-',
                    tanggal_pks: kemitraan?.tanggal_pks || spec.tanggal_pks || null,
                    skema_kemitraan: kemitraan?.skema_kemitraan || spec.skema_kemitraan || 'KSO',
                    tanggal_mulai: kemitraan?.tanggal_mulai || spec.tanggal_mulai || null,
                    tanggal_selesai: kemitraan?.tanggal_selesai || spec.tanggal_selesai || null,
                    status_konsesi: kemitraan?.status_konsesi || 'Aktif',
                    sisa_hari_konsesi: kemitraan?.sisa_hari_konsesi ?? null
                };

                // Kompatibilitas state lama
                this.activeDetail = {
                    kemitraan: kemitraan || {},
                    astap: this.selectedAstapDetail,
                    register: register || (registers[0] || {})
                };

                this.statusForm = {
                    id: kemitraan ? kemitraan.id : null,
                    status_konsesi: kemitraan?.status_konsesi || 'Aktif',
                    keterangan: ''
                };

                this.detailKondisiFilter = 'all';
                this.detailPenempatanFilter = 'all';
                this.detailSearchQuery = '';
                this.showDetailModal = true;
            },

            // Hitung statistik kondisi aset terdaftar (Standar 3 Kondisi: Baik, Kurang Baik, Rusak Berat)
            getKondisiStats(item) {
                if (!item) return { total: 0, baik: 0, kurang_baik: 0, rusak_berat: 0, pct_baik: 100, pct_kb: 0, pct_rb: 0, kondisi_dominan: 'Baik', is_multi: false, text: 'Baik (100%)', badge_class: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30', dot_class: 'bg-emerald-400' };
                const regs = item.registers || [];
                const total = regs.length;
                if (total === 0) {
                    const k = item.kondisi || item.kondisi_barang || 'Baik';
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
                    badgeClass = 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30';
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

            // Hitung kondisi per-item repeater spesifikasi
            getRincianKondisiStats(astap, idx = 0, type = null) {
                if (!astap) return { total: 0, text: 'Baik (100%)', pct_baik: 100, pct_kb: 0, pct_rr: 0, pct_rb: 0, is_multi: false, badge_class: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' };

                let regs = astap.registers || [];
                let items = [];

                if (type) {
                    if (type === 'tanah_items') items = this.getTanahItemsForDetail(astap);
                    else if (type === 'mesin_items') items = this.getMesinItemsForDetail(astap);
                    else if (type === 'gedung_items') items = this.getGedungItemsForDetail(astap);
                    else if (type === 'jaringan_items') items = this.getJaringanItemsForDetail(astap);
                    else if (type === 'lainnya_items') items = this.getLainnyaItemsForDetail(astap);

                    if (items.length > 1) {
                        const qtyKeyMap = {
                            'tanah_items': 'tanah_jumlah_bidang',
                            'mesin_items': 'mesin_jumlah_barang',
                            'gedung_items': 'gedung_jumlah_bangunan',
                            'jaringan_items': 'jaringan_jumlah',
                            // BUG-09 FIX: key yang disimpan form adalah 'lainnya_jumlah', bukan 'lainnya_jumlah_barang'
                            'lainnya_items': 'lainnya_jumlah'
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
                    let it = null;
                    if (items && items[idx]) {
                        it = items[idx];
                    } else if (type && astap.spesifikasi_json) {
                        let spec = astap.spesifikasi_json;
                        if (typeof spec === 'string') {
                            try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                        }
                        if (spec && spec[type] && spec[type][idx]) {
                            it = spec[type][idx];
                        }
                    }
                    if (it) {
                        fallbackKondisi = it.tanah_kondisi || it.mesin_kondisi || it.gedung_kondisi || it.jaringan_kondisi || it.lainnya_kondisi || astap.kondisi_barang || 'Baik';
                    } else {
                        fallbackKondisi = astap.kondisi_barang || 'Baik';
                    }
                    if (fallbackKondisi === 'B') fallbackKondisi = 'Baik';
                    if (fallbackKondisi === 'KB' || fallbackKondisi === 'RR' || fallbackKondisi === 'Rusak Ringan') fallbackKondisi = 'Kurang Baik';
                    if (fallbackKondisi === 'RB' || fallbackKondisi === 'Rusak') fallbackKondisi = 'Rusak Berat';

                    return {
                        total: 1,
                        baik: fallbackKondisi === 'Baik' ? 1 : 0,
                        kurang_baik: fallbackKondisi === 'Kurang Baik' ? 1 : 0,
                        rusak_berat: fallbackKondisi === 'Rusak Berat' ? 1 : 0,
                        pct_baik: fallbackKondisi === 'Baik' ? 100 : 0,
                        pct_kb: fallbackKondisi === 'Kurang Baik' ? 100 : 0,
                        pct_rb: fallbackKondisi === 'Rusak Berat' ? 100 : 0,
                        is_multi: false,
                        text: fallbackKondisi + ' (100%)',
                        badge_class: fallbackKondisi === 'Baik' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' :
                                    (fallbackKondisi === 'Kurang Baik' ? 'bg-amber-500/20 text-amber-300 border-amber-500/30' :
                                    'bg-rose-500/20 text-rose-300 border-rose-500/30'),
                        dot_class: fallbackKondisi === 'Baik' ? 'bg-emerald-400' :
                                  (fallbackKondisi === 'Kurang Baik' ? 'bg-amber-400' : 'bg-rose-400')
                    };
                }

                const baik = regs.filter(r => (r.kondisi || 'Baik') === 'Baik' || r.kondisi === 'B').length;
                const kb   = regs.filter(r => r.kondisi === 'Kurang Baik' || r.kondisi === 'KB' || r.kondisi === 'Rusak Ringan' || r.kondisi === 'RR').length;
                const rb   = regs.filter(r => r.kondisi === 'Rusak Berat' || r.kondisi === 'RB' || r.kondisi === 'Rusak').length;

                const pct_baik = Math.round((baik / total) * 100);
                const pct_kb   = Math.round((kb   / total) * 100);
                const pct_rb   = Math.round((rb   / total) * 100);

                const isSingle = (baik === total) || (kb === total) || (rb === total);

                let parts = [];
                if (baik > 0) parts.push(`${pct_baik}% Baik (${baik}/${total})`);
                if (kb > 0)   parts.push(`${pct_kb}% Kurang Baik (${kb}/${total})`);
                if (rb > 0)   parts.push(`${pct_rb}% Rusak Berat (${rb}/${total})`);

                let text = parts.join(' • ');
                if (isSingle) {
                    if (baik === total) text = `Baik (100%)`;
                    else if (kb === total) text = `Kurang Baik (100%)`;
                    else if (rb === total) text = `Rusak Berat (100%)`;
                }

                let badgeClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
                if (rb > 0 && rb >= baik && rb >= kb) {
                    badgeClass = 'bg-rose-500/20 text-rose-300 border-rose-500/30';
                } else if (kb > 0 && kb >= baik) {
                    badgeClass = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
                } else if (!isSingle) {
                    badgeClass = 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30';
                }

                let dotClass = pct_baik === 100 ? 'bg-emerald-400' : (pct_rb > 0 ? 'bg-rose-400' : 'bg-amber-400');

                return {
                    total,
                    baik, kurang_baik: kb, rusak_berat: rb,
                    pct_baik, pct_kb, pct_rb,
                    is_multi: !isSingle,
                    parts,
                    text: text || 'Baik',
                    badge_class: badgeClass,
                    dot_class: dotClass
                };
            },

            // Ambil info NIBAR terdaftar untuk kartu rincian spesifikasi
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

                    if (items && items.length > 0) {
                        const qtyKeyMap = {
                            'tanah_items': 'tanah_jumlah_bidang',
                            'mesin_items': 'mesin_jumlah_barang',
                            'gedung_items': 'gedung_jumlah_bangunan',
                            'jaringan_items': 'jaringan_jumlah',
                            // BUG-09 FIX: key yang disimpan form adalah 'lainnya_jumlah', bukan 'lainnya_jumlah_barang'
                            'lainnya_items': 'lainnya_jumlah'
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

                const lastSuffix = lastNibar.slice(-7);
                return {
                    label: 'NIBAR: ' + firstNibar + ' - ' + lastSuffix,
                    tooltip: 'Rentang NIBAR: ' + firstNibar + ' s/d ' + lastNibar + ' (' + regs.length + ' Unit Aset)',
                    is_range: true,
                    count: regs.length
                };
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

            getEffectiveKibCategory(astap) {
                if (!astap) return 'KIB B';
                let cat = String(astap.category || '').toUpperCase().trim();
                if (cat === 'KIB A' || cat === 'KIB B' || cat === 'KIB C' || cat === 'KIB D' || cat === 'KIB E') {
                    return cat;
                }

                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }

                // 1. Periksa kategori_kib dari spesifikasi_json
                const kibSpec = String(spec?.kategori_kib || '').toUpperCase();
                if (kibSpec.includes('KIB A') || kibSpec.includes('TANAH')) return 'KIB A';
                if (kibSpec.includes('KIB B') || kibSpec.includes('MESIN') || kibSpec.includes('PERALATAN')) return 'KIB B';
                if (kibSpec.includes('KIB C') || kibSpec.includes('GEDUNG') || kibSpec.includes('BANGUNAN')) return 'KIB C';
                if (kibSpec.includes('KIB D') || kibSpec.includes('JARINGAN') || kibSpec.includes('JALAN') || kibSpec.includes('IRIGASI')) return 'KIB D';
                if (kibSpec.includes('KIB E') || kibSpec.includes('LAINNYA') || kibSpec.includes('BUKU')) return 'KIB E';

                // 2. Periksa kode barang 108
                const kd = String(astap.kode_barang || astap.kode_108 || astap.jenis_astap?.sub_sub_rincian_objek || '');
                if (kd.endsWith('.001') || kd.includes('.01.001') || kd.startsWith('1.3.1') || kd.includes('.01.01.01.001')) return 'KIB A';
                if (kd.endsWith('.002') || kd.includes('.01.002') || kd.startsWith('1.3.2') || kd.includes('.01.01.01.002')) return 'KIB B';
                if (kd.endsWith('.003') || kd.includes('.01.003') || kd.startsWith('1.3.3') || kd.includes('.01.01.01.003')) return 'KIB C';
                if (kd.endsWith('.004') || kd.includes('.01.004') || kd.startsWith('1.3.4') || kd.includes('.01.01.01.004')) return 'KIB D';
                if (kd.endsWith('.005') || kd.includes('.01.005') || kd.startsWith('1.3.5') || kd.includes('.01.01.01.005')) return 'KIB E';

                // 3. Periksa isi repeater yang valid
                if (spec?.mesin_items?.some(m => m.mesin_nama_barang || m.mesin_merk || m.mesin_type || m.mesin_no_pabrik || (parseFloat(m.mesin_nilai_satuan) > 0))) return 'KIB B';
                if (spec?.tanah_items?.some(t => t.tanah_luas_m2 || t.tanah_hak || t.tanah_sertifikat_no || (parseFloat(t.tanah_nilai_satuan) > 0))) return 'KIB A';
                if (spec?.gedung_items?.some(g => g.gedung_nama_barang || g.gedung_luas_m2 || g.gedung_luas_lantai || g.gedung_bertingkat || (parseFloat(g.gedung_nilai_satuan) > 0))) return 'KIB C';
                if (spec?.jaringan_items?.some(j => j.jaringan_nama_barang || j.jaringan_konstruksi || j.jaringan_panjang || (parseFloat(j.jaringan_nilai_satuan) > 0))) return 'KIB D';
                if (spec?.lainnya_items?.some(l => l.lainnya_nama_barang || l.lainnya_judul || l.lainnya_pencipta || (parseFloat(l.lainnya_nilai_satuan) > 0))) return 'KIB E';

                // 4. Periksa nama barang
                const nama = String(astap.nama_barang || '').toLowerCase();
                if (nama.includes('tanah')) return 'KIB A';
                if (nama.includes('gedung') || nama.includes('bangunan')) return 'KIB C';
                if (nama.includes('jalan') || nama.includes('jaringan') || nama.includes('irigasi')) return 'KIB D';
                if (nama.includes('buku') || nama.includes('hewan') || nama.includes('kesenian')) return 'KIB E';

                return 'KIB B';
            },

            getTanahItemsForDetail(astap) {
                if (!astap) return [];
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                const targetTotal = (astap.registers && astap.registers.length > 0) ? astap.registers.length : (parseInt(astap.jumlah_volume) || 1);
                if (spec && Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) {
                    const valid = spec.tanah_items.filter(t => t.tanah_luas_m2 || t.tanah_hak || t.tanah_sertifikat_no || (parseFloat(t.tanah_nilai_satuan) > 0));
                    const toSync = valid.length > 0 ? valid : spec.tanah_items;
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['tanah_jumlah_bidang', 'tanah_jumlah_barang']);
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
                    const valid = spec.mesin_items.filter(m => m.mesin_nama_barang || m.mesin_merk || m.mesin_type || m.mesin_no_pabrik || (parseFloat(m.mesin_nilai_satuan) > 0));
                    const toSync = valid.length > 0 ? valid : spec.mesin_items;
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['mesin_jumlah_barang']);
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
                return [];
            },

            getLainnyaItemsForDetail(astap) {
                if (!astap) return [];
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                const targetTotal = (astap.registers && astap.registers.length > 0) ? astap.registers.length : (parseInt(astap.jumlah_volume) || 1);
                if (spec && Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) {
                    return this.syncRepeaterItemsWithVolume(spec.lainnya_items, targetTotal, ['lainnya_jumlah', 'lainnya_jumlah_barang']);
                }
                // Fallback untuk data historis lama sebelum repeater multi-item KIB E
                if (spec && (spec.judul || spec.pencipta || spec.nama_barang || (astap.nama_barang && astap.nama_barang.toLowerCase().includes('buku')))) {
                    return [{
                        is_extracom: Boolean(spec.is_extracomtable),
                        kib_e_type: spec.kib_e_type || 'buku',
                        lainnya_nama_barang: spec.nama_barang || astap.nama_barang,
                        lainnya_judul: spec.judul || astap.nama_barang,
                        lainnya_pencipta: spec.pencipta || '',
                        lainnya_spesifikasi: spec.spesifikasi || spec.keterangan || '',
                        lainnya_bahan: spec.bahan || '',
                        lainnya_ukuran: spec.ukuran || '',
                        lainnya_tahun: spec.tahun || astap.tahun_perolehan,
                        lainnya_kondisi: spec.kondisi || astap.kondisi || 'Baik',
                        lainnya_jumlah: targetTotal,
                        lainnya_satuan: astap.satuan || 'Buah',
                        lainnya_nilai_satuan: (astap.harga_satuan || (astap.total_realisasi / targetTotal) || 0),
                        ruang_pemegang: astap.alamat_barang || ''
                    }];
                }
                return [];
            },

            // Filtered Registers Getter
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

                return filtered.sort((a, b) => {
                    const numA = a.no_register_int || parseInt((a.nibar || a.no_register || '').slice(-7)) || 0;
                    const numB = b.no_register_int || parseInt((b.nibar || b.no_register || '').slice(-7)) || 0;
                    if (numA !== numB) return numA - numB;
                    return (a.nibar || a.no_register || '').localeCompare(b.nibar || b.no_register || '');
                });
            },

            // QR Code Methods
            downloadQrCodeNibar(reg, astap) {
                this.selectedQrItem = {
                    kode_barang: reg.nibar || (astap ? astap.kode_barang : 'ASET-KEMITRAAN'),
                    nama_barang: (astap ? astap.nama_barang : 'Aset Kemitraan') + ' (Register ' + (reg.no_register || reg.nibar) + ')',
                    category: astap ? astap.category : 'KEMITRAAN',
                    tahun_perolehan: astap ? astap.tahun_perolehan : '-',
                    ruang_pemegang: reg.ruang_pemegang || 'Ruang Belum Ditempatkan / Gudang Aset',
                    kondisi: reg.kondisi || (astap ? astap.kondisi : 'Baik'),
                    riwayat_servis: 'Konsesi Kemitraan: ' + (astap?.kemitraan?.mitra_nama || 'Pihak Ketiga')
                };
                this.showQrModal = true;
                this.generateQrImage(this.selectedQrItem);
            },

            getQrPayloadUrl(item) {
                if (!item) return '';
                return window.location.origin + '/scan/' + encodeURIComponent(item.kode_barang || '');
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
                            color: { dark: '#000000', light: '#ffffff' },
                            errorCorrectionLevel: 'M'
                        }).then(url => {
                            this.qrDataUrl = url;
                            this.isGeneratingQr = false;
                        }).catch(err => {
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
                const cleanFilename = 'QR_KEMITRAAN_' + (this.selectedQrItem.kode_barang || 'ASET').replace(/[\/\.\s]/g, '_') + '.png';
                const a = document.createElement('a');
                a.href = this.qrDataUrl;
                a.download = cleanFilename;
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
            },

            // Simpan Pembaruan Status Konsesi Kerjasama
            async saveStatusUpdate() {
                if (!this.statusForm.id) return;
                this.isUpdatingStatus = true;

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const url = `{{ url('/master-data/kemitraan') }}/${this.statusForm.id}/status`;

                    const res = await fetch(url, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            status_konsesi: this.statusForm.status_konsesi,
                            keterangan: this.statusForm.keterangan
                        })
                    });

                    const json = await res.json();
                    if (json.success) {
                        alert(json.message || 'Status kerjasama berhasil diperbarui!');
                        window.location.reload();
                    } else {
                        alert(json.message || 'Gagal memperbarui status kerjasama.');
                    }
                } catch (err) {
                    console.error('Update status error:', err);
                    alert('Terjadi kesalahan jaringan atau server saat menyimpan status.');
                } finally {
                    this.isUpdatingStatus = false;
                }
            },

            // Konfirmasi Penghapusan
            confirmDelete(id, nama) {
                this.deleteItem = { id: id, nama: nama || 'Aset Kemitraan', alasan: '' };
                this.showDeleteModal = true;
            },

            // Eksekusi Penghapusan (Soft Delete)
            async executeDelete() {
                if (!this.deleteItem.id) return;
                this.isDeleting = true;

                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const url = `{{ url('/master-data/kemitraan') }}/${this.deleteItem.id}`;

                    const res = await fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            alasan_hapus: this.deleteItem.alasan || 'Dihapus dari Kelola Kemitraan Aset'
                        })
                    });

                    const json = await res.json();
                    if (json.success) {
                        alert(json.message || 'Data Aset Kemitraan berhasil dipindahkan ke Pusat Pemulihan Data.');
                        window.location.reload();
                    } else {
                        alert(json.message || 'Gagal menghapus data.');
                    }
                } catch (err) {
                    console.error('Delete error:', err);
                    alert('Terjadi kesalahan server saat menghapus data.');
                } finally {
                    this.isDeleting = false;
                    this.showDeleteModal = false;
                }
            },

            // Toast Notification Helper
            showToast(message, type = 'success') {
                this.toast.message = message;
                this.toast.type = type;
                this.toast.show = true;
                setTimeout(() => {
                    this.toast.show = false;
                }, 4000);
            },

            // Custom Dialog Confirmation Helper
            askConfirmation(opts) {
                this.confirmData = {
                    title: opts.title || 'Konfirmasi',
                    message: opts.message || 'Apakah Anda yakin?',
                    itemName: opts.itemName || '',
                    itemDetails: opts.itemDetails || null,
                    type: opts.type || 'warning',
                    btnText: opts.btnText || 'Ya, Lanjutkan',
                    isBlocked: !!opts.isBlocked,
                    actionUrl: opts.actionUrl || null,
                    actionText: opts.actionText || null,
                    assetWarning: opts.assetWarning || null,
                    onConfirm: opts.onConfirm || null
                };
                this.showConfirmModal = true;
            },

            executeConfirmedAction() {
                if (typeof this.confirmData.onConfirm === 'function') {
                    this.confirmData.onConfirm();
                }
                this.showConfirmModal = false;
            },

            // Rapikan NIBAR Barang Kemitraan Ini (Auto-Resequence)
            resequenceSingleAstap(astap) {
                if (!astap || !astap.id) return;
                this.askConfirmation({
                    title: '🔄 Konfirmasi Rapikan NIBAR Barang Ini',
                    message: 'Sistem akan merapatkan nomor urut register (NIBAR) yang kosong khusus untuk aset yang BELUM DITEMPATKAN (di gudang).\n\n🔒 Aset yang SUDAH DITEMPATKAN di unit/ruangan TIDAK AKAN BERUBAH agar label stiker QR fisik di ruangan tidak tertukar.\n\n📦 Aset gudang setelahnya akan dimajukan untuk mengisi nomor yang kosong. Jika tidak ada aset gudang setelahnya, celah nomor dibiarkan dulu menunggu ada inputan baru dengan jenis & tahun yang sama atau sampai aset ruangan dikembalikan ke gudang.',
                    itemName: (astap.nama_barang || 'Barang Kemitraan') + ' (Tahun ' + (astap.tahun_perolehan || '2026') + ')',
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
                                this.showToast(data.message || 'NIBAR berhasil dirapikan!', 'success');
                                setTimeout(() => { window.location.reload(); }, 1200);
                            } else {
                                this.showToast('Gagal: ' + (data.message || 'Terjadi kesalahan'), 'error');
                            }
                        } catch (err) {
                            this.showToast('Terjadi kendala saat merapikan NIBAR: ' + err.message, 'error');
                        }
                    }
                });
            },

            // Modal Ubah Kondisi Barang Unit Register
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
                                    if (data.stats) {
                                        this.selectedAstapDetail.kondisi_barang = data.stats.kondisi_dominan;
                                    }
                                    this.selectedAstapDetail = { ...this.selectedAstapDetail };
                                }
                                this.showEditKondisiModal = false;
                                this.showToast('Kondisi unit berhasil diperbarui menjadi ' + this.newKondisiValue + '!', 'success');
                            } else {
                                this.showToast('Gagal memperbarui: ' + (data.message || 'Terjadi kesalahan'), 'error');
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

            // Modal Cek Riwayat Mutasi Barang
            async openRiwayatModal(reg) {
                if (!reg) return;
                this.selectedRiwayatRegister = reg;
                this.selectedRiwayatMutasis = Array.isArray(reg.mutasis) ? reg.mutasis : [];
                this.showRiwayatModal = true;

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

            // Cek apakah register sedang aktif ditempatkan di Unit / Ruangan
            isRegisterPlacedInUnit(reg) {
                if (!reg) return false;
                if (reg.unit_id) return true;
                if (!reg.ruang_pemegang) return false;
                const clean = String(reg.ruang_pemegang).trim().toLowerCase();
                const unplacedPlaceholders = [
                    '', '-', 'belum ditempatkan', 'belum ditempatkan / di gudang',
                    'belum ditempatkan / di gudang aset', 'gudang aset',
                    'gudang aset utama / belum ditempatkan', 'gudang perbekalan'
                ];
                return !unplacedPlaceholders.includes(clean);
            },

            // Hapus Unit Register NIBAR (dengan proteksi ruangan aktif)
            deleteRegister(reg) {
                if (!reg) return;

                // 1. Validasi Penempatan: Cek apakah unit register SUDAH DITEMPATKAN di unit / paviliun
                if (this.isRegisterPlacedInUnit(reg)) {
                    const roomName = String(reg.ruang_pemegang || 'Unit / Paviliun RSUD').trim();
                    const parentName = this.selectedAstapDetail?.nama_barang || 'Aset Kemitraan';
                    const nibarStr = reg.nibar || reg.no_register || 'NIBAR';

                    this.askConfirmation({
                        title: 'Unit Tidak Dapat Dihapus',
                        message: `Unit register NIBAR "${nibarStr}" saat ini belum dapat dihapus karena masih aktif ditempatkan di ruangan "${roomName}" di database RSUD.`,
                        itemName: `${parentName} (NIBAR: ${nibarStr})`,
                        itemDetails: {
                            nama: parentName,
                            kode: nibarStr,
                            badgeText: '1 ASET AKTIF',
                            totalAset: 1,
                            nilaiFmt: this.selectedAstapDetail?.total_realisasi ? ('Rp ' + Number(this.selectedAstapDetail.total_realisasi).toLocaleString('id-ID')) : 'Rp 0'
                        },
                        type: 'danger',
                        isBlocked: true,
                        actionUrl: '/mutasi-aset',
                        actionText: 'Ajukan Mutasi Aset',
                        assetWarning: `Sistem mendeteksi bahwa ruangan "${roomName}" saat ini masih memegang aset aktif dengan NIBAR ${nibarStr}. Demi akuntabilitas dan pencegahan kehilangan aset RSUD Koesnadi, seluruh aset harus dipindahkan (mutasi) ke ruangan lain terlebih dahulu sampai ruangan ini kosong atau dikembalikan ke gudang perbekalan.`,
                        btnText: null,
                        onConfirm: null
                    });
                    return;
                }

                // 2. Jika belum ditempatkan (di gudang): Izinkan hapus ke Tong Sampah
                this.askConfirmation({
                    title: 'Konfirmasi Pindahkan ke Tong Sampah',
                    message: 'Apakah Anda yakin ingin memindahkan unit register NIBAR ini ke Recycle Bin (Tong Sampah)? Data dapat dipulihkan kembali jika diperlukan.',
                    itemName: 'NIBAR: ' + (reg.nibar || reg.no_register),
                    itemDetails: {
                        nama: this.selectedAstapDetail?.nama_barang || 'Aset Kemitraan',
                        kode: reg.nibar || reg.no_register,
                        badgeText: 'Belum Ditempatkan',
                        totalAset: 0,
                        nilaiFmt: 'Gudang Aset'
                    },
                    type: 'danger',
                    isBlocked: false,
                    btnText: 'Pindahkan ke Tong Sampah',
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
                                    if (data.stats) {
                                        this.selectedAstapDetail.kondisi_barang = data.stats.kondisi_dominan;
                                    }
                                    this.selectedAstapDetail = { ...this.selectedAstapDetail };

                                    if (data.astap_auto_deleted && data.astap_id) {
                                        this.showDetailModal = false;
                                        this.selectedAstapDetail = null;
                                        this.showToast(data.message || 'Paket Kemitraan otomatis dipindahkan ke Recycle Bin karena semua unit NIBAR telah dihapus.', 'success');
                                        setTimeout(() => { window.location.reload(); }, 1200);
                                    } else {
                                        this.showToast(data.message || 'Unit register NIBAR berhasil dipindahkan ke Recycle Bin!', 'success');
                                    }
                                }
                            } else if (data.is_blocked) {
                                this.askConfirmation({
                                    title: 'Unit Tidak Dapat Dihapus',
                                    message: data.message,
                                    itemName: 'NIBAR: ' + (reg.nibar || reg.no_register),
                                    itemDetails: {
                                        nama: this.selectedAstapDetail?.nama_barang || 'Aset Kemitraan',
                                        kode: reg.nibar || reg.no_register,
                                        badgeText: '1 ASET AKTIF',
                                        totalAset: 1,
                                        nilaiFmt: this.selectedAstapDetail?.total_realisasi ? ('Rp ' + Number(this.selectedAstapDetail.total_realisasi).toLocaleString('id-ID')) : 'Rp 0'
                                    },
                                    type: 'danger',
                                    isBlocked: true,
                                    actionUrl: data.action_url || '/mutasi-aset',
                                    actionText: data.action_text || 'Ajukan Mutasi Aset',
                                    assetWarning: data.message,
                                    btnText: null,
                                    onConfirm: null
                                });
                            } else {
                                this.showToast('Gagal menghapus unit: ' + (data.message || 'Terjadi kesalahan sistem'), 'error');
                            }
                        } catch (err) {
                            this.showToast('Terjadi kesalahan saat menghapus: ' + err.message, 'error');
                        }
                    }
                });
            }
        };
    }
</script>
