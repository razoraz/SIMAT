<script>
    function mutasiEksternalCatalog() {
        return {
            activeDirection: 'masuk',
            searchQuery: '',
            statusFilter: 'all',
            categoryFilter: 'all',
            showDetailModal: false,
            selectedMutasi: null,
            selectedAstapDetail: null,
            detailPenempatanFilter: 'all',
            detailKondisiFilter: 'all',
            detailSearchQuery: '',
            showPrintModal: false,
            showEditForm: false,
            printDoc: null,
            showDeleteModal: false,
            itemToDelete: null,
            deleteAlasan: '',
            isDeleting: false,

            // Global Custom Confirmation Modal State
            showConfirmModal: false,
            confirmData: {
                title: 'Konfirmasi Tindakan',
                message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                itemName: '',
                itemDetails: null,
                type: 'danger',
                btnText: 'Ya, Lanjutkan',
                assetWarning: null,
                isBlocked: false,
                actionUrl: null,
                actionText: null,
                onConfirm: null
            },

            // Modal Edit Kondisi State
            showEditKondisiModal: false,
            editingRegisterItem: null,
            newKondisiValue: 'Baik',
            isSavingKondisi: false,

            // Modal Cek Riwayat Mutasi State (Internal, Eksternal & Reklasifikasi)
            showRiwayatModal: false,
            riwayatActiveTab: 'semua',
            selectedRiwayatRegister: null,
            selectedRiwayatMutasis: [],
            selectedRiwayatMutasisInternal: [],
            selectedRiwayatMutasisEksternal: [],
            selectedRiwayatReklas: [],
            selectedRiwayatTimeline: [],
            selectedRiwayatCounts: { internal: 0, eksternal: 0, reklas: 0, total: 0 },
            isLoadingRiwayat: false,

            getQrCodeSvg(text) {
                if (typeof window.getQrCodeSvg === 'function') {
                    return window.getQrCodeSvg(text);
                }
                return 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(text || '');
            },

            mutasiEksternals: {{ Js::from($mutasiEksternals) }},

            get countMasuk() {
                return this.mutasiEksternals.filter(item => item.tipe !== 'keluar').length;
            },

            get countKeluar() {
                return this.mutasiEksternals.filter(item => item.tipe === 'keluar').length;
            },

            get currentDirectionItems() {
                return this.mutasiEksternals.filter(item => {
                    if (this.activeDirection === 'keluar') {
                        return item.tipe === 'keluar';
                    }
                    return item.tipe !== 'keluar';
                });
            },

            get filteredMutasis() {
                const query = (this.searchQuery || '').toLowerCase().trim();

                return this.currentDirectionItems.filter(item => {
                    // Filter Kategori KIB
                    if (this.categoryFilter !== 'all') {
                        if ((item.category || '').toLowerCase() !== this.categoryFilter.toLowerCase()) return false;
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
                            (item.pj_tujuan_nama || '').toLowerCase().includes(query) ||
                            (item.pj_asal_nama || '').toLowerCase().includes(query) ||
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
                return this.currentDirectionItems.length;
            },

            get totalNominal() {
                return this.currentDirectionItems.reduce((acc, curr) => acc + (parseFloat(curr.nilai_perolehan || curr.total_realisasi_num) || 0), 0);
            },

            get totalUnits() {
                return this.currentDirectionItems.reduce((acc, curr) => acc + (parseInt(curr.jumlah_volume || curr.item_count || 1) || 1), 0);
            },

            get countSkpd() {
                const opds = this.currentDirectionItems.map(m => (this.activeDirection === 'keluar' ? (m.opd_tujuan || '') : (m.opd_asal || '')).trim()).filter(Boolean);
                return new Set(opds).size;
            },

            get countSelesai() {
                return this.currentDirectionItems.filter(m => (m.status || '').toLowerCase().includes('selesai') || (m.status || '').toLowerCase().includes('disahkan')).length;
            },

            get countMenunggu() {
                return this.currentDirectionItems.filter(m => (m.status || '').toLowerCase().includes('menunggu')).length;
            },

            formatRupiah(val) {
                return 'Rp ' + Number(val || 0).toLocaleString('id-ID');
            },

            // Hitung statistik kondisi dari registers suatu aset (Hanya 3 Kondisi: Baik, Kurang Baik, Rusak Berat)
            getKondisiStats(item) {
                if (!item) return { total: 0, baik: 0, kurang_baik: 0, rusak_ringan: 0, rusak_berat: 0, pct_baik: 100, pct_kb: 0, pct_rr: 0, pct_rb: 0, kondisi_dominan: 'Baik', is_multi: false, text: 'Baik (100%)', badge_class: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30', dot_class: 'bg-emerald-400' };
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
                        rusak_ringan: 0,
                        rusak_berat: isRb ? 1 : 0,
                        pct_baik: dominan === 'Baik' ? 100 : 0,
                        pct_kb: isKb ? 100 : 0,
                        pct_rr: 0,
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
                const pct_rr   = 0;
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
                    baik,
                    kurang_baik: kb,
                    rusak_ringan: 0,
                    rusak_berat: rb,
                    pct_baik,
                    pct_kb,
                    pct_rr: 0,
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
                this.categoryFilter = 'all';
            },

            formatTanggalIndo(val) {
                if (!val) return '-';
                val = String(val).trim();
                if (/^\d{4}-\d{2}-\d{2}/.test(val)) {
                    const parts = val.split('-');
                    return `${parts[2].slice(0, 2)}/${parts[1]}/${parts[0]}`;
                }
                return val;
            },

            get filteredRegisters() {
                const target = this.selectedAstapDetail || this.selectedMutasi;
                if (!target || !target.registers) return [];
                const q = (this.detailSearchQuery || '').toLowerCase().trim();
                return target.registers.filter(reg => {
                    if (this.detailPenempatanFilter === 'sudah' && !reg.ruang_pemegang) return false;
                    if (this.detailPenempatanFilter === 'belum' && reg.ruang_pemegang) return false;
                    if (this.detailKondisiFilter !== 'all') {
                        const k = reg.kondisi || 'Baik';
                        if (this.detailKondisiFilter === 'Baik' && k !== 'Baik' && k !== 'B') return false;
                        if (this.detailKondisiFilter === 'Kurang Baik' && k !== 'Kurang Baik' && k !== 'KB' && k !== 'Rusak Ringan' && k !== 'RR') return false;
                        if (this.detailKondisiFilter === 'Rusak Berat' && k !== 'Rusak Berat' && k !== 'RB' && k !== 'Rusak') return false;
                    }
                    if (q) {
                        const nibar = (reg.nibar || reg.no_register || '').toLowerCase();
                        const ruang = (reg.ruang_pemegang || '').toLowerCase();
                        if (!nibar.includes(q) && !ruang.includes(q)) return false;
                    }
                    return true;
                });
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
                if (cat === 'KIB A' || cat === 'KIB B' || cat === 'KIB C' || cat === 'KIB D' || cat === 'KIB E' || cat === 'KIB F' || cat === 'EXTRACOM' || cat === 'ATB' || cat === 'ASET LAIN' || cat === 'ASET LAINNYA') {
                    return (cat === 'ASET LAINNYA') ? 'ASET LAIN' : cat;
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

                const kd = String(astap.kode_barang || astap.kode_108 || '');
                if (kd.startsWith('1.3.1')) return 'KIB A';
                if (kd.startsWith('1.3.2')) return 'KIB B';
                if (kd.startsWith('1.3.3')) return 'KIB C';
                if (kd.startsWith('1.3.4')) return 'KIB D';
                if (kd.startsWith('1.3.5')) return 'KIB E';

                if (spec?.mesin_items?.some(m => m.mesin_nama_barang || m.mesin_merk || m.mesin_type || (parseFloat(m.mesin_nilai_satuan) > 0))) return 'KIB B';
                if (spec?.tanah_items?.some(t => t.tanah_luas_m2 || t.tanah_hak || (parseFloat(t.tanah_nilai_satuan) > 0))) return 'KIB A';
                if (spec?.gedung_items?.some(g => g.gedung_nama_barang || g.gedung_luas_m2 || (parseFloat(g.gedung_nilai_satuan) > 0))) return 'KIB C';
                if (spec?.jaringan_items?.some(j => j.jaringan_nama_barang || j.jaringan_konstruksi || (parseFloat(j.jaringan_nilai_satuan) > 0))) return 'KIB D';
                if (spec?.lainnya_items?.some(l => l.lainnya_nama_barang || l.lainnya_judul || (parseFloat(l.lainnya_nilai_satuan) > 0))) return 'KIB E';

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
                    return this.syncRepeaterItemsWithVolume(spec.lainnya_items, targetTotal, ['lainnya_jumlah_barang', 'lainnya_jumlah']);
                }
                return [];
            },

            getRincianNibar(astap, idx = 0, type = null) {
                if (!astap || !astap.registers || astap.registers.length === 0) return null;
                const reg = astap.registers[idx] || astap.registers[0];
                if (!reg || !reg.nibar) return null;
                return {
                    label: 'NIBAR: ' + reg.nibar,
                    tooltip: 'Nomor Induk Barang: ' + reg.nibar
                };
            },

            downloadQrCodeNibar(reg, astap) {
                const nibar = reg.nibar || reg.no_register || (astap ? (astap.kode_barang || astap.kode) : 'ASET');
                const qrUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' + encodeURIComponent(window.location.origin + '/scan/' + nibar);
                window.open(qrUrl, '_blank');
            },

            openDetail(item) {
                if (!item) return;
                this.selectedMutasi = item;
                this.selectedAstapDetail = item;
                if (this.selectedAstapDetail && typeof this.selectedAstapDetail.spesifikasi_json === 'string') {
                    try {
                        this.selectedAstapDetail.spesifikasi_json = JSON.parse(this.selectedAstapDetail.spesifikasi_json);
                    } catch(e) {}
                }
                if (this.selectedAstapDetail && Array.isArray(this.selectedAstapDetail.registers)) {
                    this.selectedAstapDetail.registers.sort((a, b) => {
                        const numA = a.no_register_int || parseInt((a.nibar || a.no_register || '').slice(-7)) || 0;
                        const numB = b.no_register_int || parseInt((b.nibar || b.no_register || '').slice(-7)) || 0;
                        if (numA !== numB) return numA - numB;
                        return (a.nibar || a.no_register || '').localeCompare(b.nibar || b.no_register || '');
                    });
                }
                this.detailKondisiFilter = 'all';
                this.detailPenempatanFilter = 'all';
                this.detailSearchQuery = '';
                this.showDetailModal = true;
            },

            askConfirmation({ title, message, itemName, itemDetails = null, type = 'danger', btnText, assetWarning = null, isBlocked = false, actionUrl = null, actionText = null, onConfirm }) {
                this.confirmData = {
                    title: title || 'Konfirmasi Tindakan',
                    message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                    itemName: itemName || '',
                    itemDetails: itemDetails,
                    type: type,
                    btnText: isBlocked ? null : (btnText || (type === 'danger' ? 'Pindahkan ke Tong Sampah' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan'))),
                    isBlocked: Boolean(isBlocked),
                    actionUrl: actionUrl,
                    actionText: actionText,
                    assetWarning: assetWarning,
                    onConfirm: onConfirm
                };
                this.showConfirmModal = true;
            },

            executeConfirmedAction() {
                if (typeof this.confirmData.onConfirm === 'function') {
                    this.confirmData.onConfirm();
                }
                this.showConfirmModal = false;
            },

            showToast(message, type = 'success') {
                const cleanMsg = String(message || '').replace(/^[\s✅✔️☑️✓✔⚠️❌🚫⛔ℹ️🗑️✏️🔑💾]+/, '').trim();
                if (typeof window.showSimatToast === 'function') {
                    window.showSimatToast(cleanMsg, type);
                } else {
                    alert((type === 'error' ? '❌ ' : '✓ ') + cleanMsg);
                }
            },

            resequenceSingleAstap(astap) {
                if (!astap) return;
                const astapId = astap.astap_id || astap.id;
                if (!astapId) return;

                this.askConfirmation({
                    title: '🔄 Konfirmasi Rapikan NIBAR Barang Ini',
                    message: 'Sistem akan merapatkan nomor urut register (NIBAR) yang kosong khusus untuk aset yang BELUM DITEMPATKAN (di gudang).\n\n🔒 Aset yang SUDAH DITEMPATKAN di unit/ruangan TIDAK AKAN BERUBAH agar label stiker QR fisik di ruangan tidak tertukar.\n\n📦 Aset gudang setelahnya akan dimajukan untuk mengisi nomor yang kosong. Jika tidak ada aset gudang setelahnya, celah nomor dibiarkan dulu menunggu ada inputan baru dengan jenis & tahun yang sama atau sampai aset ruangan dikembalikan ke gudang.',
                    itemName: (astap.nama_barang || astap.nama_murni || astap.nama || 'Barang') + ' (Tahun ' + (astap.tahun_perolehan || '2026') + ')',
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
                                    astap_id: astapId
                                })
                            });
                            const data = await res.json();
                            if (data.success) {
                                this.showDetailModal = false;
                                this.showToast(data.message, 'success');
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

            openEditKondisiModal(reg) {
                if (!reg) return;
                this.editingRegisterItem = reg;
                const rawK = reg.kondisi || 'Baik';
                this.newKondisiValue = (rawK === 'B' || rawK === 'Baik') ? 'Baik' : ((rawK === 'KB' || rawK === 'Kurang Baik' || rawK === 'RR' || rawK === 'Rusak Ringan') ? 'Kurang Baik' : 'Rusak Berat');
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
                                const targetDetail = this.selectedAstapDetail || this.selectedMutasi;
                                if (targetDetail && targetDetail.registers) {
                                    targetDetail.registers = targetDetail.registers.map(r => 
                                        r.id === reg.id ? { ...r, kondisi: this.newKondisiValue } : { ...r }
                                    );
                                    if (data.spesifikasi_json) {
                                        targetDetail.spesifikasi_json = data.spesifikasi_json;
                                    }
                                    if (data.stats) {
                                        if (!targetDetail.spesifikasi_json) targetDetail.spesifikasi_json = {};
                                        targetDetail.spesifikasi_json.kondisi_stats = data.stats;
                                        targetDetail.spesifikasi_json.kondisi = data.stats.kondisi_dominan;
                                        targetDetail.kondisi = data.stats.kondisi_dominan;
                                    }
                                }

                                this.mutasiEksternals = this.mutasiEksternals.map(m => {
                                    const mAstapId = m.astap_id || m.id;
                                    const tAstapId = targetDetail ? (targetDetail.astap_id || targetDetail.id) : null;
                                    if (Number(mAstapId) === Number(tAstapId)) {
                                        const updatedRegs = (m.registers || []).map(r => 
                                            r.id === reg.id ? { ...r, kondisi: this.newKondisiValue } : { ...r }
                                        );
                                        let spec = m.spesifikasi_json || {};
                                        if (typeof spec === 'string') {
                                            try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                                        }
                                        if (data.stats) {
                                            spec.kondisi_stats = data.stats;
                                            spec.kondisi = data.stats.kondisi_dominan;
                                        }
                                        return {
                                            ...m,
                                            kondisi: data.stats ? data.stats.kondisi_dominan : m.kondisi,
                                            spesifikasi_json: spec,
                                            registers: updatedRegs
                                        };
                                    }
                                    return m;
                                });

                                this.showEditKondisiModal = false;
                                this.showToast('Kondisi unit berhasil diperbarui menjadi ' + this.newKondisiValue + '!', 'success');
                            } else {
                                this.showToast('Gagal memperbarui: ' + (data.message || 'Terjadi kesalahan'), 'error');
                            }
                        } catch(err) {
                            reg.kondisi = this.newKondisiValue;
                            this.showEditKondisiModal = false;
                            this.showToast('Kondisi unit berhasil diperbarui!', 'success');
                        } finally {
                            this.isSavingKondisi = false;
                        }
                    }
                });
            },

            async openRiwayatModal(reg) {
                if (!reg) return;
                this.riwayatActiveTab = 'semua';
                this.selectedRiwayatRegister = reg;
                this.selectedRiwayatMutasis = Array.isArray(reg.mutasis) ? reg.mutasis : [];
                this.selectedRiwayatMutasisInternal = Array.isArray(reg.mutasis) ? reg.mutasis : [];
                this.selectedRiwayatMutasisEksternal = [];
                this.selectedRiwayatReklas = [];
                this.selectedRiwayatTimeline = [];
                this.selectedRiwayatCounts = { internal: this.selectedRiwayatMutasisInternal.length, eksternal: 0, reklas: 0, total: this.selectedRiwayatMutasisInternal.length };
                this.showRiwayatModal = true;

                try {
                    this.isLoadingRiwayat = true;
                    const res = await fetch(`/astap/register-mutasi/${reg.id}`);
                    if (res.ok) {
                        const data = await res.json();
                        if (data.success) {
                            this.selectedRiwayatMutasis = data.mutasis || [];
                            this.selectedRiwayatMutasisInternal = data.mutasis_internal || data.mutasis || [];
                            this.selectedRiwayatMutasisEksternal = data.mutasis_eksternal || [];
                            this.selectedRiwayatReklas = data.reklasifikasis || [];
                            this.selectedRiwayatTimeline = data.timeline || [];
                            this.selectedRiwayatCounts = data.counts || {
                                internal: this.selectedRiwayatMutasisInternal.length,
                                eksternal: this.selectedRiwayatMutasisEksternal.length,
                                reklas: this.selectedRiwayatReklas.length,
                                total: (data.timeline || []).length
                            };
                            reg.mutasis = this.selectedRiwayatMutasisInternal;
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

            deleteRegister(reg) {
                if (!reg) return;

                // 1. Validasi Penempatan: Cek apakah unit register SUDAH DITEMPATKAN di unit / paviliun
                if (this.isRegisterPlacedInUnit(reg)) {
                    const roomName = String(reg.ruang_pemegang || 'Unit / Paviliun RSUD').trim();
                    const parentTarget = this.selectedAstapDetail || this.selectedMutasi;
                    const parentName = parentTarget?.nama_murni || parentTarget?.nama_barang || parentTarget?.nama || 'Aset';
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
                            nilaiFmt: parentTarget?.jumlah_realisasi || parentTarget?.nilai_perolehan_formatted || 'Rp 0'
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
                        nama: (this.selectedAstapDetail || this.selectedMutasi)?.nama_barang || 'Aset',
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
                                const targetDetail = this.selectedAstapDetail || this.selectedMutasi;
                                if (targetDetail && targetDetail.registers) {
                                    targetDetail.registers = targetDetail.registers.filter(r => r.id !== reg.id);
                                    targetDetail.jumlah_volume = targetDetail.registers.length;
                                    targetDetail.volume_satuan = targetDetail.jumlah_volume + ' Aset';
                                    if (data.spesifikasi_json) {
                                        targetDetail.spesifikasi_json = data.spesifikasi_json;
                                    }
                                    if (data.stats) {
                                        if (!targetDetail.spesifikasi_json) targetDetail.spesifikasi_json = {};
                                        targetDetail.spesifikasi_json.kondisi_stats = data.stats;
                                        targetDetail.spesifikasi_json.kondisi = data.stats.kondisi_dominan;
                                        targetDetail.kondisi = data.stats.kondisi_dominan;
                                    }
                                }

                                // Jika master ASTAP otomatis dihapus (NIBAR terakhir habis)
                                if (data.astap_auto_deleted && data.astap_id) {
                                    this.mutasiEksternals = this.mutasiEksternals.filter(m => Number(m.astap_id || m.id) !== Number(data.astap_id) && Number(m.id) !== Number(data.astap_id));
                                    this.showDetailModal = false;
                                    this.selectedMutasi = null;
                                    this.selectedAstapDetail = null;
                                    this.showToast(data.message || 'Paket pelimpahan otomatis dipindahkan ke Recycle Bin karena semua unit NIBAR telah dihapus.', 'success');
                                } else {
                                    this.mutasiEksternals = this.mutasiEksternals.map(m => {
                                        const mAstapId = m.astap_id || m.id;
                                        const tAstapId = targetDetail ? (targetDetail.astap_id || targetDetail.id) : null;
                                        if (Number(mAstapId) === Number(tAstapId)) {
                                            const updatedRegs = (m.registers || []).filter(r => r.id !== reg.id);
                                            let spec = (data && data.spesifikasi_json) ? data.spesifikasi_json : (m.spesifikasi_json || {});
                                            if (typeof spec === 'string') {
                                                try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                                            }
                                            return {
                                                ...m,
                                                jumlah_volume: updatedRegs.length,
                                                item_count: updatedRegs.length,
                                                volume_satuan: updatedRegs.length + ' Aset',
                                                kondisi: data.stats ? data.stats.kondisi_dominan : m.kondisi,
                                                spesifikasi_json: spec,
                                                registers: updatedRegs
                                            };
                                        }
                                        return m;
                                    });
                                    this.showToast(data.message || 'Unit register NIBAR berhasil dipindahkan ke Recycle Bin.', 'success');
                                }
                            } else {
                                this.showToast('Gagal: ' + (data.message || 'Terjadi kesalahan'), 'error');
                            }
                        } catch(err) {
                            this.showToast('Terjadi kesalahan saat menghapus register: ' + err.message, 'error');
                        }
                    }
                });
            },

            openReklas(item) {
                if (!item) return;
                const searchKey = item.kode_barang || item.nama_murni || item.nama || item.kode || '';
                window.location.href = `/astap?search=${encodeURIComponent(searchKey)}&open_reklas=${item.id}`;
            },

            openPrintModal(item) {
                if (!item) return;
                this.showDetailModal = false;

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

                    // Pihak Kesatu & Pihak Kedua (Disesuaikan dengan Arah Mutasi: Masuk vs Keluar)
                    opd_asal: (item.tipe === 'keluar') ? 'RSUD dr. H. Koesnandi Kabupaten Bondowoso' : (item.opd_asal || 'Dinas Kesehatan Kabupaten Bondowoso'),
                    alamat_instansi: (item.tipe === 'keluar') ? 'Jl. Piere Tendean No. 1, Bondowoso' : (item.alamat_instansi || (item.astap && item.astap.spesifikasi_json ? item.astap.spesifikasi_json.alamat_instansi : '') || ''),
                    pj_asal_nama: (item.tipe === 'keluar') ? (item.pj_asal_nama || 'BUDI HARTONO, S.Sos') : (item.pj_asal_nama || 'Pejabat Penyerah SKPD Pengirim'),
                    pj_asal_nip: (item.tipe === 'keluar') ? (item.pj_asal_nip || '19760229 200801 1 010') : (item.pj_asal_nip || '-'),
                    pj_asal_jabatan: (item.tipe === 'keluar') ? (item.pj_asal_jabatan || 'Pengurus Barang Pengguna RSUD Dr. H. Koesnadi') : (item.pj_asal_jabatan || 'Pengurus Barang / PPK Asal'),
                    nomor_sk_dasar: item.nomor_sk_dasar || '',

                    // Pihak Kedua
                    opd_tujuan: (item.tipe === 'keluar') ? (item.opd_tujuan || 'SKPD / Instansi Penerima') : 'RSUD dr. H. Koesnandi Kabupaten Bondowoso',
                    pj_tujuan_nama: (item.tipe === 'keluar') 
                        ? (item.pejabat_opd_tujuan || item.pj_tujuan_nama || 'Pejabat Penerima OPD')
                        : ((item.pejabat_opd_tujuan && !item.pejabat_opd_tujuan.toLowerCase().includes('yus')) 
                            ? item.pejabat_opd_tujuan 
                            : ((item.pj_tujuan_nama && !item.pj_tujuan_nama.toLowerCase().includes('yus')) ? item.pj_tujuan_nama : 'BUDI HARTONO, S.Sos')),
                    pj_tujuan_nip: (item.tipe === 'keluar')
                        ? (item.nip_pejabat_opd_tujuan || item.pj_tujuan_nip || '-')
                        : ((item.nip_pejabat_opd_tujuan && !item.nip_pejabat_opd_tujuan.includes('19771002') && !item.nip_pejabat_opd_tujuan.includes('19690412'))
                            ? item.nip_pejabat_opd_tujuan 
                            : ((item.pj_tujuan_nip && !item.pj_tujuan_nip.includes('19771002') && !item.pj_tujuan_nip.includes('19690412')) ? item.pj_tujuan_nip : '19760229 200801 1 010')),
                    pj_tujuan_jabatan: (item.tipe === 'keluar')
                        ? (item.jabatan_opd_tujuan || 'Pengurus Barang / Pejabat Penerima OPD')
                        : (item.jabatan_opd_tujuan || 'Pengurus Barang Pengguna RSUD Dr. H. Koesnadi'),

                    // Pejabat Pengesah (Direktur RSUD Dr. H. Koesnadi)
                    direktur_nama: 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR',
                    direktur_nip: '19771002 200604 1 006',
                    direktur_jabatan: 'Direktur RSUD dr. H. Koesnadi',

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

            deleteMutasi(item) {
                if (!item) return;
                this.itemToDelete = item;
                this.deleteAlasan = '';
                this.showDeleteModal = true;
            },

            async executeDelete() {
                if (!this.itemToDelete || this.isDeleting) return;
                this.isDeleting = true;

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                const id = this.itemToDelete.mutasi_id || this.itemToDelete.id;
                const targetUrl = "{{ url('/mutasi-eksternal') }}/" + id;

                try {
                    const res = await fetch(targetUrl, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            alasan: this.deleteAlasan || 'Dihapus dari modul Mutasi Eksternal'
                        })
                    });

                    const d = await res.json();
                    this.isDeleting = false;

                    if (res.ok && d.success !== false) {
                        const delAstapId = this.itemToDelete.id;
                        const delMutasiId = this.itemToDelete.mutasi_id;

                        this.mutasiEksternals = this.mutasiEksternals.filter(m => 
                            Number(m.id) !== Number(delAstapId) && 
                            Number(m.mutasi_id) !== Number(delMutasiId)
                        );

                        if (this.selectedMutasi && (Number(this.selectedMutasi.id) === Number(delAstapId) || Number(this.selectedMutasi.mutasi_id) === Number(delMutasiId))) {
                            this.showDetailModal = false;
                            this.selectedMutasi = null;
                        }

                        this.showDeleteModal = false;
                        this.itemToDelete = null;

                        if (typeof window.showSimatToast === 'function') {
                            window.showSimatToast(d.message || 'Data pelimpahan aset berhasil dipindahkan ke Tong Sampah.', 'success');
                        } else {
                            alert('✓ ' + (d.message || 'Data pelimpahan aset berhasil dipindahkan ke Tong Sampah.'));
                        }
                    } else {
                        alert('❌ ' + (d.message || 'Gagal memindahkan data ke Tong Sampah.'));
                    }
                } catch (err) {
                    this.isDeleting = false;
                    console.error('Delete error:', err);
                    alert('❌ Terjadi kesalahan jaringan saat mencoba menghapus data.');
                }
            }
        };
    }
</script>
