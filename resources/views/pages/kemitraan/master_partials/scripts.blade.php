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
                let registers = [];
                if (astap?.registers && Array.isArray(astap.registers) && astap.registers.length > 0) {
                    registers = astap.registers.map(r => ({
                        id: r.id,
                        nibar: r.nibar || r.no_register || '-',
                        no_register: r.no_register || r.nibar || '-',
                        no_register_int: r.no_register_int || parseInt((r.nibar || r.no_register || '').slice(-7)) || 0,
                        ruang_pemegang: r.ruang_pemegang || r.unit?.nama || astap?.unit?.nama || (register?.ruang_pemegang || ''),
                        kondisi: r.kondisi || 'Baik',
                        created_at: r.created_at
                    }));
                } else if (register && Object.keys(register).length > 0) {
                    registers = [{
                        id: register.id || 1,
                        nibar: register.nibar || register.no_register || '-',
                        no_register: register.no_register || register.nibar || '-',
                        no_register_int: register.no_register_int || 1,
                        ruang_pemegang: register.ruang_pemegang || astap?.unit?.nama || '',
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

            // Hitung statistik kondisi aset terdaftar
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

            // Hitung kondisi per-item repeater spesifikasi
            getRincianKondisiStats(astap, idx = 0, type = null) {
                if (!astap) return { total: 0, text: 'Baik (100%)', pct_baik: 100, pct_kb: 0, pct_rr: 0, pct_rb: 0, is_multi: false, badge_class: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' };

                let regs = astap.registers || [];

                if (type) {
                    let items = [];
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
                    if (type && astap.spesifikasi_json?.[type]?.[idx]) {
                        const it = astap.spesifikasi_json[type][idx];
                        fallbackKondisi = it.mesin_kondisi || it.tanah_kondisi || it.gedung_kondisi || it.jaringan_kondisi || it.lainnya_kondisi || 'Baik';
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
                    return this.syncRepeaterItemsWithVolume(spec.lainnya_items, targetTotal, ['lainnya_jumlah_barang']);
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
            }
        };
    }
</script>
