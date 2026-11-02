<!-- SCRIPTS: LOGIKA STATE MANAGEMENT & EKSPOR EXCEL MULTI-SHEET HIBAH -->
<script>
    window.masterHibahRecords = @json($hibahRecords ?? []);
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : \App\Models\JenisAstap::getNested108());

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
            detailSearchQuery: '',
            detailKondisiFilter: 'all',
            detailPenempatanFilter: 'all',

            // QR Code States
            showQrModal: false,
            selectedQrItem: null,
            isGeneratingQr: false,
            qrDataUrl: '',

            // Edit Kondisi States
            showEditKondisiModal: false,
            editingRegisterItem: null,
            newKondisiValue: 'Baik',
            isSavingKondisi: false,

            // Riwayat Mutasi States
            showRiwayatModal: false,
            selectedRiwayatRegister: null,
            selectedRiwayatHibah: [],
            selectedRiwayatMutasis: [],
            selectedRiwayatMutasisInternal: [],
            selectedRiwayatMutasisEksternal: [],
            selectedRiwayatReklas: [],
            selectedRiwayatTimeline: [],
            selectedRiwayatCounts: { hibah: 0, internal: 0, eksternal: 0, mutasi: 0, reklas: 0, total: 0 },
            riwayatActiveTab: 'semua',
            mutasiSubFilter: 'semua',
            isLoadingRiwayat: false,

            // Generic Dialog Confirmation State
            showConfirmModal: false,
            confirmData: {},

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

            // State Modal Reklasifikasi Aset Tetap (RSDK) - Standar sama dengan ASTAP & Kemitraan
            showReklasModal: false,
            selectedAstapReklas: null,
            reklasJenis: 'pindah_kib', // 'extracom' | 'intracom' | 'pindah_kib' | 'kdp' | 'koreksi_nilai' | 'hibah' | 'mutasi_eksternal'
            reklasTujuanKib: '',
            reklasTujuanKode: '',
            reklasTujuanNama: '',
            reklasSubRincianKode: '',
            reklasSubRincianNama: '',
            reklasSubSubRincianKode: '',
            reklasSubSubRincianNama: '',
            searchReklasSubRincian: '',
            isReklasSubRincianOpen: false,
            searchReklasSubSubRincian: '',
            isReklasSubSubRincianOpen: false,
            reklasNomorBa: '',
            reklasTanggal: new Date().toLocaleDateString('en-CA'),
            reklasAlasan: '',
            reklasExtracomItems: [],
            reklasTipeKoreksiNilai: 'kurang', // 'kurang' | 'tambah'
            reklasSubKoreksi: 'biasa', // 'biasa' | 'lkd' | 'manset'
            reklasNominalKoreksi: 0,
            reklasNilaiRealisasiBaru: 0,
            reklasNilaiAnggaran: 0,
            reklasNoDokumenKoreksi: '',
            reklasDokumenTglKoreksi: new Date().toLocaleDateString('en-CA'),
            reklasTipeHibah: 'keluar',
            reklasNomorBastHibah: '',
            reklasTanggalBastHibah: new Date().toLocaleDateString('en-CA'),
            reklasPihakHibah: '',
            reklasNomorBastMutasi: '',
            reklasTanggalBastMutasi: new Date().toLocaleDateString('en-CA'),
            reklasSkpdTujuan: '',
            reklasPejabatOpdTujuan: '',
            reklasNipPejabatOpdTujuan: '',
            reklasJabatanOpdTujuan: 'Pengurus Barang / Pengelola Aset OPD',
            reklasPjAsalNama: 'BUDI HARTONO, S.Sos',
            reklasPjAsalNip: '19760229 200801 1 010',
            reklasPjAsalJabatan: 'Pengurus Barang Pengguna',
            reklasAlamatInstansi: '',
            reklasNomorSkDasar: '',
            reklasSelectedNibarIds: [],
            reklasRegisterKondisiMap: {},
            skpdDirectory: window.dbSkpdDirectory || {},
            skpdList: window.dbSkpdAsals || [],
            dbPejabats: window.dbPejabats || [],
            isSkpdDropdownOpen: false,
            isPejabatTujuanDropdownOpen: false,
            isPejabatAsalDropdownOpen: false,
            selectedDokumenFile: null,
            isSubmittingReklas: false,
            reklasKdpProgresAwal: 0,
            reklasKemitraanTipeFisik: 'mesin',
            // Spesifikasi baru (minimal - untuk modal reklas)
            reklasSpekBaru: {},
            reklasSpekBaruItems: [],
            activeSpekUnitTab: 0,

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

            resetFilter() {
                this.activeTab = 'all';
                this.selectedYear = 'all';
                this.selectedTw = 'all';
                this.searchQuery = '';
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

            // Validasi Ruangan Riil (Bukan RSUD, Gudang, atau tanda belum ditempatkan)
            hasRealRuang(reg) {
                if (!reg) return false;
                const rVal = (typeof reg === 'string') ? reg : (reg.ruang_pemegang || reg.ruang || '');
                if (!rVal) return false;
                const r = String(rVal).trim().toLowerCase();
                if (r === '' || r === '-' || r.includes('belum ditempatkan') || r.includes('rsud dr. h. koesnandi') || r.includes('piere tendean') || r === 'gudang aset' || r === 'gudang aset rsud') {
                    return false;
                }
                return true;
            },

            // Label Ruangan / Penempatan Unit
            getRuangLabel(item) {
                if (!item) return '-';
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
                    if (this.hasRealRuang(r0)) {
                        return r0.ruang_pemegang;
                    }
                }
                if (item.register && this.hasRealRuang(item.register)) {
                    return item.register.ruang_pemegang;
                }
                return '-';
            },

            // Ekstrak Spesifikasi Fisik KIB
            getSpecDetail(item) {
                if (!item) return {};
                let spec = item.astap?.spesifikasi_json || {};
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                const firstMesin = (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) ? spec.mesin_items[0] : {};
                const firstGedung = (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) ? spec.gedung_items[0] : {};
                const firstTanah = (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) ? spec.tanah_items[0] : {};
                const firstJaringan = (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) ? spec.jaringan_items[0] : {};
                const firstLainnya = (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) ? spec.lainnya_items[0] : {};

                return {
                    merk: firstMesin.mesin_merk || spec.merk || spec.mesin_merk || item.astap?.merk_type || '',
                    type: firstMesin.mesin_type || spec.type || spec.mesin_type || '',
                    ukuran: firstMesin.mesin_ukuran || spec.ukuran || spec.ukuran_cc || spec.mesin_ukuran_cc || '',
                    bahan: firstMesin.mesin_bahan || spec.bahan || spec.mesin_bahan || '',
                    no_pabrik: firstMesin.mesin_no_pabrik || spec.no_pabrik || spec.mesin_no_pabrik || '',
                    no_rangka: firstMesin.mesin_no_rangka || spec.no_rangka || spec.mesin_no_rangka || '',
                    no_mesin: firstMesin.mesin_no_mesin || spec.no_mesin || spec.mesin_no_mesin || '',
                    no_polisi: firstMesin.mesin_no_polisi || spec.no_polisi || spec.mesin_no_polisi || '',
                    no_bpkb: firstMesin.mesin_no_bpkb || spec.no_bpkb || spec.mesin_no_bpkb || '',
                    is_extracom: Boolean(firstMesin.is_extracom || spec.is_extracom || item.astap?.is_extracomtable),
                    tanah_luas_m2: firstTanah.tanah_luas_m2 || spec.tanah_luas_m2 || spec.luas_m2 || '',
                    tanah_sertifikat_no: firstTanah.tanah_sertifikat_no || spec.tanah_sertifikat_no || spec.sertifikat_no || '',
                    gedung_luas_m2: firstGedung.gedung_luas_m2 || firstGedung.gedung_luas_lantai || spec.gedung_luas_m2 || spec.luas_lantai_m2 || '',
                    gedung_bertingkat: firstGedung.gedung_bertingkat || spec.gedung_bertingkat || spec.konstruksi_bertingkat || '',
                    gedung_beton: firstGedung.gedung_beton || spec.gedung_beton || spec.konstruksi_beton || '',
                    gedung_is_baru: firstGedung.gedung_is_baru || spec.gedung_is_baru || '',
                    jaringan_konstruksi: firstJaringan.jaringan_konstruksi || spec.jaringan_konstruksi || '',
                    jaringan_panjang_m: firstJaringan.jaringan_panjang_m || spec.jaringan_panjang_m || '',
                    jaringan_lebar_m: firstJaringan.jaringan_lebar_m || spec.jaringan_lebar_m || '',
                    jaringan_luas_m2: firstJaringan.jaringan_luas_m2 || spec.jaringan_luas_m2 || '',
                    lainnya_judul: firstLainnya.lainnya_judul_pencipta || spec.lainnya_judul_pencipta || '',
                    lainnya_bahan: firstLainnya.lainnya_bahan || spec.lainnya_bahan || '',
                    lainnya_ukuran: firstLainnya.lainnya_ukuran || spec.lainnya_ukuran || '',
                    hibah_pimpinan: spec.hibah_pimpinan || spec.pimpinan_pemberi || item.astap?.hibah_pimpinan || '',
                    hibah_alamat_pemberi: spec.hibah_alamat_pemberi || spec.alamat_pemberi || item.astap?.hibah_alamat_pemberi || '',
                    dokumen_path: item.dokumen_path || spec.dokumen_bast_path || spec.dokumen_path || item.astap?.dokumen_bast_path || '',
                    mesin_items: Array.isArray(spec.mesin_items) ? spec.mesin_items : []
                };
            },

            isDokumenImage(path) {
                if (!path) return false;
                const ext = String(path).split('.').pop().toLowerCase();
                return ['jpg', 'jpeg', 'png', 'webp', 'gif'].includes(ext);
            },

            // Filter Daftar Unit Register NIBAR
            get filteredDetailRegisters() {
                if (!this.selectedDetail) return [];
                let regs = this.getRegistersList(this.selectedDetail);
                if (this.detailKondisiFilter && this.detailKondisiFilter !== 'all') {
                    regs = regs.filter(r => (r.kondisi || 'Baik') === this.detailKondisiFilter || r.kondisi === this.detailKondisiFilter);
                }
                if (this.detailPenempatanFilter === 'sudah') {
                    regs = regs.filter(r => this.hasRealRuang(r));
                } else if (this.detailPenempatanFilter === 'belum') {
                    regs = regs.filter(r => !this.hasRealRuang(r));
                }
                if (this.detailSearchQuery && this.detailSearchQuery.trim() !== '') {
                    const q = this.detailSearchQuery.toLowerCase().trim();
                    regs = regs.filter(r => 
                        (r.nibar && String(r.nibar).toLowerCase().includes(q)) || 
                        (r.no_register && String(r.no_register).toLowerCase().includes(q)) ||
                        (r.ruang_pemegang && String(r.ruang_pemegang).toLowerCase().includes(q))
                    );
                }
                return regs;
            },

            // Daftar Register NIBAR
            getRegistersList(item) {
                if (!item) return [];
                if (item.astap && Array.isArray(item.astap.registers)) {
                    return item.astap.registers;
                }
                if (Array.isArray(item.registers)) {
                    return item.registers;
                }
                if (item.register) {
                    return [item.register];
                }
                let regs = [];
                const vol = parseInt(item.jumlah_volume) || 0;
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
                return regs;
            },

            getQrCodeSvg(text) {
                if (typeof window.getQrCodeSvg === 'function') {
                    return window.getQrCodeSvg(text);
                }
                return 'https://api.qrserver.com/v1/create-qr-code/?size=120x120&data=' + encodeURIComponent(text || '');
            },

            // =========================================================================
            // DIALOG KONFIRMASI CUSTOM & AKSI REGISTER
            // =========================================================================
            askConfirmation(cfg) {
                this.confirmData = {
                    title: cfg.title || 'Konfirmasi Tindakan',
                    message: cfg.message || '',
                    itemName: cfg.itemName || '',
                    itemDetails: cfg.itemDetails || null,
                    type: cfg.type || 'warning',
                    btnText: cfg.btnText || 'Ya, Lanjutkan',
                    isBlocked: Boolean(cfg.isBlocked),
                    actionUrl: cfg.actionUrl || null,
                    actionText: cfg.actionText || '',
                    onConfirm: cfg.onConfirm || null
                };
                this.showConfirmModal = true;
            },

            async executeConfirmedAction() {
                this.showConfirmModal = false;
                if (typeof this.confirmData.onConfirm === 'function') {
                    await this.confirmData.onConfirm();
                }
            },

            // =========================================================================
            // PRATINJAU & UNDUH LABEL QR CODE NIBAR ASET HIBAH
            // =========================================================================
            downloadQrCodeNibar(reg, hibahItem) {
                const astap = hibahItem ? (hibahItem.astap || hibahItem) : null;
                this.selectedQrItem = {
                    kode_barang: reg.nibar || (astap ? astap.kode_barang : 'ASET-HIBAH'),
                    nama_barang: (astap ? astap.nama_barang : (hibahItem?.nama_barang || 'Aset Hibah')) + ' (Register ' + (reg.no_register || reg.nibar) + ')',
                    category: astap ? (astap.category || 'HIBAH') : 'HIBAH',
                    tahun_perolehan: hibahItem ? (hibahItem.tahun || astap?.tahun_perolehan || '-') : '-',
                    ruang_pemegang: reg.ruang_pemegang || (astap?.unit?.nama || 'Gudang Aset RSUD'),
                    kondisi: reg.kondisi || (astap ? astap.kondisi : 'Baik'),
                    pihak_hibah: hibahItem?.pihak_hibah || '-',
                    nomor_bast: hibahItem?.nomor_bast || '-'
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
                const url = this.getQrPayloadUrl(item);

                const renderQr = () => {
                    if (typeof QRCode !== 'undefined') {
                        const tempDiv = document.createElement('div');
                        try {
                            new QRCode(tempDiv, {
                                text: url,
                                width: 260,
                                height: 260,
                                colorDark: '#020617',
                                colorLight: '#ffffff',
                                correctLevel: QRCode.CorrectLevel.H
                            });
                            setTimeout(() => {
                                const img = tempDiv.querySelector('img');
                                const canvas = tempDiv.querySelector('canvas');
                                if (img && img.src) {
                                    this.qrDataUrl = img.src;
                                } else if (canvas) {
                                    this.qrDataUrl = canvas.toDataURL('image/png');
                                }
                                this.isGeneratingQr = false;
                            }, 100);
                        } catch (e) {
                            this.qrDataUrl = `https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=${encodeURIComponent(url)}`;
                            this.isGeneratingQr = false;
                        }
                    } else {
                        this.qrDataUrl = `https://api.qrserver.com/v1/create-qr-code/?size=260x260&data=${encodeURIComponent(url)}`;
                        this.isGeneratingQr = false;
                    }
                };

                setTimeout(renderQr, 50);
            },

            downloadQrImage() {
                if (!this.qrDataUrl || !this.selectedQrItem) return;
                const link = document.createElement('a');
                link.download = `QR_${this.selectedQrItem.kode_barang || 'NIBAR'}.png`;
                link.href = this.qrDataUrl;
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                this.showToast('Label QR Code berhasil diunduh!', 'success');
            },

            // =========================================================================
            // UBAH KONDISI REGISTER
            // =========================================================================
            openEditKondisiModal(reg) {
                if (!reg) return;
                this.editingRegisterItem = reg;
                this.newKondisiValue = reg.kondisi === 'B' ? 'Baik' : ((reg.kondisi === 'KB' || reg.kondisi === 'RR' || reg.kondisi === 'Rusak Ringan') ? 'Kurang Baik' : ((reg.kondisi === 'RB' || reg.kondisi === 'Rusak') ? 'Rusak Berat' : (reg.kondisi || 'Baik')));
                this.showEditKondisiModal = true;
            },

            saveKondisiChange() {
                if (!this.editingRegisterItem) return;
                const reg = this.editingRegisterItem;
                this.askConfirmation({
                    title: '⚙️ Konfirmasi Perubahan Kondisi Barang',
                    message: 'Apakah Anda yakin ingin memperbarui kondisi barang unit ini menjadi "' + this.newKondisiValue + '"?',
                    itemName: 'NIBAR: ' + (reg.nibar || reg.no_register),
                    type: 'warning',
                    btnText: '✓ Ya, Simpan Kondisi',
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
                                    kondisi: this.newKondisiValue
                                })
                            });
                            const data = await res.json();
                            if (data.success) {
                                reg.kondisi = this.newKondisiValue;
                                this.showToast('Kondisi unit register berhasil diperbarui menjadi ' + this.newKondisiValue, 'success');
                                this.showEditKondisiModal = false;
                            } else {
                                this.showToast(data.message || 'Gagal mengubah kondisi barang', 'error');
                            }
                        } catch (err) {
                            this.showToast('Terjadi kesalahan jaringan.', 'error');
                        } finally {
                            this.isSavingKondisi = false;
                        }
                    }
                });
            },

            // =========================================================================
            // RIWAYAT & REKAM JEJAK SIKLUS ASET
            // =========================================================================
            async openRiwayatModal(reg) {
                if (!reg) return;
                this.riwayatActiveTab = 'semua';
                this.mutasiSubFilter = 'semua';
                this.selectedRiwayatRegister = reg;
                this.selectedRiwayatHibah = [];
                this.selectedRiwayatMutasis = Array.isArray(reg.mutasis) ? reg.mutasis : [];
                this.selectedRiwayatMutasisInternal = Array.isArray(reg.mutasis) ? reg.mutasis : [];
                this.selectedRiwayatMutasisEksternal = [];
                this.selectedRiwayatReklas = [];
                this.selectedRiwayatTimeline = [];
                this.selectedRiwayatCounts = { hibah: 0, internal: this.selectedRiwayatMutasisInternal.length, eksternal: 0, mutasi: this.selectedRiwayatMutasisInternal.length, reklas: 0, total: this.selectedRiwayatMutasisInternal.length };
                this.showRiwayatModal = true;

                try {
                    this.isLoadingRiwayat = true;
                    const res = await fetch(`/astap/register-mutasi/${reg.id}`);
                    if (res.ok) {
                        const data = await res.json();
                        if (data.success) {
                            this.selectedRiwayatHibah = data.hibah || [];
                            this.selectedRiwayatMutasis = data.mutasis || [];
                            this.selectedRiwayatMutasisInternal = data.mutasis_internal || data.mutasis || [];
                            this.selectedRiwayatMutasisEksternal = data.mutasis_eksternal || [];
                            this.selectedRiwayatReklas = data.reklasifikasis || data.reklas || [];
                            this.selectedRiwayatTimeline = data.timeline || [];
                            const intCount = this.selectedRiwayatMutasisInternal.length;
                            const eksCount = this.selectedRiwayatMutasisEksternal.length;
                            const hibahCount = (data.hibah || []).length;
                            const reklasCount = this.selectedRiwayatReklas.length;
                            this.selectedRiwayatCounts = data.counts || {
                                hibah: hibahCount,
                                internal: intCount,
                                eksternal: eksCount,
                                mutasi: intCount + eksCount,
                                reklas: reklasCount,
                                total: (data.timeline || []).length || (hibahCount + intCount + eksCount + reklasCount)
                            };
                            reg.mutasis = this.selectedRiwayatMutasisInternal;
                            if (data.kondisi) reg.kondisi = data.kondisi;
                            if (data.ruang) reg.ruang_pemegang = data.ruang;
                        }
                    }
                } catch(e) {
                    console.warn('Gagal load riwayat register:', e);
                } finally {
                    this.isLoadingRiwayat = false;
                }
            },

            // =========================================================================
            // RAPIKAN NIBAR (AUTO-RESEQUENCE)
            // =========================================================================
            resequenceSingleAstap(hibahItem) {
                const astapId = hibahItem?.astap?.id || hibahItem?.astap_id || (hibahItem?.category ? hibahItem?.id : null);
                if (!astapId) {
                    this.showToast('Data ASTAP induk untuk barang ini tidak ditemukan.', 'warning');
                    return;
                }
                const astap = hibahItem ? (hibahItem.astap || hibahItem) : null;
                this.askConfirmation({
                    title: '🔄 Konfirmasi Rapikan NIBAR Barang Ini',
                    message: 'Sistem akan merapatkan nomor urut register (NIBAR) yang kosong khusus untuk aset yang BELUM DITEMPATKAN (di gudang).\n\n🔒 Aset yang SUDAH DITEMPATKAN di unit/ruangan TIDAK AKAN BERUBAH agar label stiker QR fisik di ruangan tidak tertukar.\n\n⚡ Aset gudang setelahnya akan dimajukan untuk mengisi nomor yang kosong.',
                    itemName: (astap?.nama_barang || hibahItem?.nama_barang || 'Barang Hibah') + ' (Tahun ' + (hibahItem?.tahun || astap?.tahun_perolehan || '2026') + ')',
                    type: 'warning',
                    btnText: '✓ Ya, Rapikan NIBAR',
                    onConfirm: async () => {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        try {
                            const res = await fetch('/astap/resequence-nibar', {
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
                                this.showToast(data.message || 'NIBAR berhasil dirapikan!', 'success');
                                setTimeout(() => window.location.reload(), 1200);
                            } else {
                                this.showToast(data.message || 'Gagal merapikan NIBAR', 'error');
                            }
                        } catch (err) {
                            this.showToast('Terjadi kesalahan jaringan.', 'error');
                        }
                    }
                });
            },

            // =========================================================================
            // HAPUS UNIT REGISTER
            // =========================================================================
            isRegisterPlacedInUnit(reg) {
                if (!reg) return false;
                const r = String(reg.ruang_pemegang || '').trim().toLowerCase();
                if (!r || r === '-' || r.includes('belum ditempatkan') || r.includes('gudang')) {
                    return false;
                }
                return true;
            },

            deleteRegister(reg) {
                if (!reg) return;
                if (this.isRegisterPlacedInUnit(reg)) {
                    const roomName = String(reg.ruang_pemegang || 'Unit / Paviliun RSUD').trim();
                    const nibarStr = reg.nibar || reg.no_register || 'NIBAR';
                    this.askConfirmation({
                        title: 'Unit Tidak Dapat Dihapus',
                        message: `Unit register NIBAR "${nibarStr}" saat ini belum dapat dihapus karena masih aktif ditempatkan di ruangan "${roomName}" di database RSUD.\n\nSilakan ajukan mutasi pemindahan aset terlebih dahulu atau kembalikan ke gudang perbekalan sebelum menghapus register.`,
                        itemName: `NIBAR: ${nibarStr}`,
                        type: 'danger',
                        isBlocked: true
                    });
                    return;
                }

                const nibarStr = reg.nibar || reg.no_register || 'NIBAR';
                this.askConfirmation({
                    title: 'Konfirmasi Hapus Unit Register',
                    message: `Apakah Anda yakin ingin menghapus unit register NIBAR "${nibarStr}" ini? Unit register akan dipindahkan ke Recycle Bin.`,
                    itemName: `NIBAR: ${nibarStr}`,
                    type: 'danger',
                    btnText: 'Hapus Unit Register',
                    onConfirm: async () => {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        try {
                            const res = await fetch('/astap-register/' + reg.id, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token,
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();
                            if (data.success) {
                                if (this.selectedDetail) {
                                    if (this.selectedDetail.astap && Array.isArray(this.selectedDetail.astap.registers)) {
                                        this.selectedDetail.astap.registers = this.selectedDetail.astap.registers.filter(r => r.id !== reg.id);
                                    }
                                    if (Array.isArray(this.selectedDetail.registers)) {
                                        this.selectedDetail.registers = this.selectedDetail.registers.filter(r => r.id !== reg.id);
                                    }
                                }

                                if (data.astap_auto_deleted) {
                                    if (data.astap_id) {
                                        this.hibahList = this.hibahList.filter(h => (h.astap_id !== data.astap_id && h.astap?.id !== data.astap_id));
                                    } else if (this.selectedDetail) {
                                        this.hibahList = this.hibahList.filter(h => h.id !== this.selectedDetail.id);
                                    }
                                    this.showModalDetail = false;
                                    this.selectedDetail = null;
                                    this.showToast(data.message || 'Transaksi hibah otomatis dipindahkan ke Recycle Bin karena seluruh unit NIBAR telah dihapus.', 'success');
                                } else {
                                    if (this.selectedDetail) {
                                        const remainingCount = (this.selectedDetail.astap?.registers || this.selectedDetail.registers || []).length;
                                        this.selectedDetail.jumlah_volume = remainingCount;
                                        if (this.selectedDetail.astap) {
                                            this.selectedDetail.astap.jumlah_volume = remainingCount;
                                        }
                                        const itemInList = this.hibahList.find(h => h.id === this.selectedDetail.id);
                                        if (itemInList) {
                                            itemInList.jumlah_volume = remainingCount;
                                            if (itemInList.astap) itemInList.astap.jumlah_volume = remainingCount;
                                        }
                                    }
                                    this.showToast(data.message || 'Unit register berhasil dihapus ke Recycle Bin', 'success');
                                }
                            } else {
                                this.showToast(data.message || 'Gagal menghapus unit register.', 'error');
                            }
                        } catch (err) {
                            this.showToast('Terjadi kesalahan jaringan.', 'error');
                        }
                    }
                });
            },

            // =========================================================================
            // EXTRACTOR REPEATER KIB A - E UNTUK MODAL DETAIL
            // =========================================================================
            getActiveRegisters(item) {
                if (!item) return [];
                const astap = item.astap || item;
                if (astap.registers && Array.isArray(astap.registers)) {
                    return astap.registers.filter(r => !r.is_deleted && r.status !== 'Mutasi Keluar OPD');
                }
                return [];
            },

            syncRepeaterItemsWithVolume(items, targetTotal, qtyKeys = []) {
                if (!Array.isArray(items) || items.length === 0) return [];
                if (targetTotal <= 0) return [];

                // 1. Konsolidasi item-item yang sejenis / berspesifikasi identik
                let consolidated = [];
                for (let it of items) {
                    if (!it) continue;
                    let activeQtyKey = qtyKeys[0];
                    let curQty = 1;
                    for (let k of qtyKeys) {
                        if (it[k] !== undefined && it[k] !== null && it[k] !== '') {
                            activeQtyKey = k;
                            curQty = parseFloat(it[k]) || 1;
                            break;
                        }
                    }

                    // Signature spesifikasi fisik barang
                    const sig = [
                        it.mesin_nama_barang || it.tanah_nama_barang || it.gedung_nama_barang || it.jaringan_nama_barang || it.lainnya_nama_barang || it.nama_barang || '',
                        it.mesin_merk || it.merk || '',
                        it.mesin_type || it.type || '',
                        it.mesin_ukuran || it.mesin_ukuran_cc || it.ukuran || it.tanah_luas_m2 || it.gedung_luas_m2 || '',
                        it.mesin_bahan || it.bahan || '',
                        it.mesin_no_pabrik || it.no_pabrik || it.tanah_sertifikat_no || '',
                        it.mesin_no_rangka || it.no_rangka || '',
                        it.mesin_no_mesin || it.no_mesin || '',
                        it.mesin_nilai_satuan || it.tanah_nilai_satuan || it.gedung_nilai_satuan || it.jaringan_nilai_satuan || it.lainnya_nilai_satuan || 0
                    ].join('||');

                    const existing = consolidated.find(c => c._sig === sig);
                    if (existing) {
                        existing[activeQtyKey] = (parseFloat(existing[activeQtyKey]) || 0) + curQty;
                    } else {
                        let clone = JSON.parse(JSON.stringify(it));
                        clone._sig = sig;
                        if (activeQtyKey) clone[activeQtyKey] = curQty;
                        consolidated.push(clone);
                    }
                }

                // Hapus properti pembantu _sig
                consolidated.forEach(c => delete c._sig);

                // 2. Alokasikan kuota volume ke item-item yang telah dikonsolidasi
                let remainingQuota = targetTotal;
                let result = [];

                for (let i = 0; i < consolidated.length; i++) {
                    if (remainingQuota <= 0) break;
                    let item = JSON.parse(JSON.stringify(consolidated[i]));

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

                // 3. Jika masih ada sisa kuota (misal volume paket lebih besar dari rincian item):
                // Gabungkan sisa kuota ke kartu barang sejenis terakhir, JANGAN membuat kartu duplikat terpisah!
                if (remainingQuota > 0 && result.length > 0) {
                    const lastItem = result[result.length - 1];
                    let activeQtyKey = qtyKeys[0];
                    for (let k of qtyKeys) {
                        if (lastItem[k] !== undefined && lastItem[k] !== null && lastItem[k] !== '') {
                            activeQtyKey = k;
                            break;
                        }
                    }
                    if (activeQtyKey) {
                        lastItem[activeQtyKey] = (parseFloat(lastItem[activeQtyKey]) || 0) + remainingQuota;
                    }
                    remainingQuota = 0;
                }

                return result;
            },

            getTanahItemsForDetail(item) {
                if (!item) return [];
                const astap = item.astap || item;
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                spec = spec || {};
                if (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) {
                    const valid = spec.tanah_items.filter(t => t.tanah_nama_barang || t.tanah_luas_m2 || t.tanah_hak || t.tanah_sertifikat_no || (parseFloat(t.tanah_nilai_satuan) > 0));
                    const rawList = valid.length > 0 ? valid : spec.tanah_items;
                    const toSync = rawList.map(t => {
                        const luas = t.tanah_luas_m2 || t.tanah_luas || t.luas_m2 || spec.luas_m2 || '';
                        const hak = t.tanah_hak || t.hak_tanah || spec.hak_tanah || 'Hak Pakai';
                        const sertifikat = t.tanah_sertifikat_no || t.sertifikat_no || spec.sertifikat_no || '';
                        const sertifikatTgl = t.tanah_sertifikat_tgl || t.sertifikat_tgl || '';
                        const penggunaan = t.tanah_penggunaan || t.penggunaan || '';
                        const alamat = t.tanah_alamat || t.alamat || astap.alamat_barang || '';
                        return {
                            ...t,
                            tanah_luas_m2: luas,
                            tanah_hak: hak,
                            tanah_sertifikat_no: sertifikat,
                            tanah_sertifikat_tgl: sertifikatTgl,
                            tanah_penggunaan: penggunaan,
                            tanah_alamat: alamat,
                            tanah_jumlah_bidang: t.tanah_jumlah_bidang || t.tanah_jumlah_barang || 1,
                        };
                    });
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.tanah_jumlah_bidang || it.tanah_jumlah_barang) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume || item.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['tanah_jumlah_bidang', 'tanah_jumlah_barang']);
                }

                const luas = parseFloat(spec.tanah_luas_m2 || spec.luas_m2 || 0);
                const hak = spec.tanah_hak || spec.hak_tanah || '';
                const sertifikat = spec.tanah_sertifikat_no || spec.sertifikat_no || '';
                const alamat = spec.tanah_alamat || spec.alamat || astap.alamat_barang || '';
                if (luas > 0 || hak || sertifikat) {
                    return [{
                        tanah_luas_m2: luas,
                        tanah_hak: hak || 'Hak Pakai',
                        tanah_sertifikat_no: sertifikat,
                        tanah_sertifikat_tgl: spec.tanah_sertifikat_tgl || spec.sertifikat_tgl || '',
                        tanah_penggunaan: spec.tanah_penggunaan || spec.penggunaan || '',
                        tanah_alamat: alamat,
                        tanah_jumlah_bidang: 1,
                        tanah_nilai_satuan: parseFloat(astap.nilai_aset || astap.total_realisasi || 0)
                    }];
                }
                return [];
            },

            getTanahItemNilai(tItem, item) {
                if (!tItem && !item) return 0;
                const direct = Number(tItem?.tanah_nilai_satuan || tItem?.tanah_nilai_fisik || tItem?.nilai_satuan || tItem?.nilai_perolehan || 0);
                if (direct > 0) return direct;
                const items = this.getTanahItemsForDetail(item);
                const count = Math.max(1, (items && items.length) ? items.length : 1);
                const astap = item.astap || item;
                const parentTotal = Number(astap?.nilai_aset || astap?.total_realisasi || item?.nilai_aset || 0);
                return parentTotal > 0 ? Math.round(parentTotal / count) : 0;
            },

            getMesinItemsForDetail(item) {
                if (!item) return [];
                const astap = item.astap || item;
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                spec = spec || {};
                if (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                    const valid = spec.mesin_items.filter(m => m.mesin_nama_barang || m.mesin_merk || m.mesin_type || m.mesin_no_pabrik || (parseFloat(m.mesin_nilai_satuan) > 0));
                    const rawList = valid.length > 0 ? valid : spec.mesin_items;
                    const toSync = rawList.map(m => {
                        const uk = m.mesin_ukuran || m.mesin_ukuran_cc || m.ukuran || m.ukuran_cc || spec.ukuran || spec.mesin_ukuran || spec.mesin_ukuran_cc || '';
                        return {
                            ...m,
                            mesin_ukuran: uk,
                            mesin_ukuran_cc: uk,
                            mesin_merk: m.mesin_merk || m.merk || spec.merk || '',
                            mesin_type: m.mesin_type || m.type || spec.type || '',
                            mesin_bahan: m.mesin_bahan || m.bahan || spec.bahan || '',
                            mesin_no_pabrik: m.mesin_no_pabrik || m.no_pabrik || spec.no_pabrik || '',
                            mesin_no_rangka: m.mesin_no_rangka || m.no_rangka || spec.no_rangka || '',
                            mesin_no_mesin: m.mesin_no_mesin || m.no_mesin || spec.no_mesin || '',
                            mesin_no_polisi: m.mesin_no_polisi || m.no_polisi || spec.no_polisi || '',
                            mesin_no_bpkb: m.mesin_no_bpkb || m.no_bpkb || spec.no_bpkb || '',
                            mesin_kondisi: m.mesin_kondisi || m.kondisi || spec.kondisi || 'Baik',
                            mesin_keterangan: m.mesin_keterangan || m.keterangan || spec.keterangan || '',
                            mesin_jumlah_barang: m.mesin_jumlah_barang || m.item_volume || 1,
                        };
                    });
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.mesin_jumlah_barang) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume || item.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['mesin_jumlah_barang']);
                }

                if (spec && (spec.merk || spec.type || spec.ukuran || spec.ukuran_cc || spec.mesin_ukuran || spec.mesin_ukuran_cc || spec.no_pabrik || spec.bahan)) {
                    const uk = spec.mesin_ukuran || spec.mesin_ukuran_cc || spec.ukuran || spec.ukuran_cc || '';
                    const totalVol = Math.max(this.getActiveRegisters(astap).length, parseInt(astap.jumlah_volume || item.jumlah_volume) || 1);
                    return [{
                        mesin_nama_barang: astap.nama_barang || item.nama_barang,
                        mesin_merk: spec.merk || spec.mesin_merk || '',
                        mesin_type: spec.type || spec.mesin_type || '',
                        mesin_ukuran: uk,
                        mesin_ukuran_cc: uk,
                        mesin_bahan: spec.bahan || spec.mesin_bahan || '',
                        mesin_no_pabrik: spec.no_pabrik || spec.mesin_no_pabrik || '',
                        mesin_no_rangka: spec.no_rangka || spec.mesin_no_rangka || '',
                        mesin_no_mesin: spec.no_mesin || spec.mesin_no_mesin || '',
                        mesin_no_polisi: spec.no_polisi || spec.mesin_no_polisi || '',
                        mesin_no_bpkb: spec.no_bpkb || spec.mesin_no_bpkb || '',
                        mesin_kondisi: spec.kondisi || astap.kondisi || 'Baik',
                        mesin_jumlah_barang: totalVol,
                        mesin_satuan: astap.satuan || item.satuan || 'Unit',
                        mesin_nilai_satuan: parseFloat(astap.harga_satuan || (item.nilai_aset / totalVol) || 0),
                        is_extracom: Boolean(spec.is_extracomtable || astap.is_extracomtable)
                    }];
                }
                return [];
            },

            getGedungItemsForDetail(item) {
                if (!item) return [];
                const astap = item.astap || item;
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                spec = spec || {};
                if (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) {
                    const valid = spec.gedung_items.filter(g => g.gedung_nama_barang || g.gedung_nama_bangunan || g.gedung_luas_lantai || g.gedung_luas_lantai_m2 || g.gedung_luas_m2 || (parseFloat(g.gedung_nilai_satuan || g.gedung_nilai_fisik) > 0));
                    const rawList = valid.length > 0 ? valid : spec.gedung_items;
                    const toSync = rawList.map(g => {
                        const luas = g.gedung_luas_lantai_m2 || g.gedung_luas_lantai || g.gedung_luas_m2 || g.luas_lantai_m2 || spec.luas_lantai_m2 || '';
                        const dokNo = g.gedung_dokumen_nomor || g.gedung_dokumen_no || g.dokumen_nomor || spec.dokumen_nomor || '';
                        const bertingkat = g.gedung_konstruksi_bertingkat || g.gedung_bertingkat || g.bertingkat || spec.konstruksi_bertingkat || spec.bertingkat || 'Bertingkat';
                        const beton = g.gedung_konstruksi_beton || g.gedung_beton || g.beton || spec.konstruksi_beton || spec.beton || 'Beton';
                        const statusTanah = g.gedung_status_tanah || g.status_tanah || spec.status_tanah || 'Tanah Milik RSUD';
                        return {
                            ...g,
                            gedung_luas_lantai: luas,
                            gedung_luas_lantai_m2: luas,
                            gedung_dokumen_no: dokNo,
                            gedung_dokumen_nomor: dokNo,
                            gedung_konstruksi_bertingkat: bertingkat,
                            gedung_bertingkat: bertingkat,
                            gedung_konstruksi_beton: beton,
                            gedung_beton: beton,
                            gedung_status_tanah: statusTanah,
                            gedung_jumlah_bangunan: g.gedung_jumlah_bangunan || 1,
                        };
                    });
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.gedung_jumlah_bangunan) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume || item.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['gedung_jumlah_bangunan']);
                }

                if (spec && (spec.luas_lantai_m2 || spec.gedung_luas_lantai || spec.bertingkat || spec.konstruksi_bertingkat || spec.dokumen_nomor)) {
                    const totalVol = Math.max(this.getActiveRegisters(astap).length, parseInt(astap.jumlah_volume || item.jumlah_volume) || 1);
                    const luas = spec.luas_lantai_m2 || spec.gedung_luas_lantai || spec.gedung_luas_lantai_m2 || spec.luas_m2 || '';
                    const dokNo = spec.dokumen_nomor || spec.gedung_dokumen_nomor || spec.dokumen_no || '';
                    const bertingkat = spec.konstruksi_bertingkat || spec.bertingkat || spec.gedung_bertingkat || 'Bertingkat';
                    const beton = spec.konstruksi_beton || spec.beton || spec.gedung_beton || 'Beton';
                    return [{
                        gedung_nama_bangunan: astap.nama_barang || item.nama_barang,
                        gedung_luas_lantai: luas,
                        gedung_luas_lantai_m2: luas,
                        gedung_dokumen_no: dokNo,
                        gedung_dokumen_nomor: dokNo,
                        gedung_konstruksi_bertingkat: bertingkat,
                        gedung_bertingkat: bertingkat,
                        gedung_konstruksi_beton: beton,
                        gedung_beton: beton,
                        gedung_status_tanah: spec.status_tanah || 'Tanah Milik RSUD',
                        gedung_alamat: astap.alamat_barang || 'Kompleks RSUD Dr. H. Koesnandi',
                        gedung_nilai_satuan: parseFloat(astap.harga_satuan || (item.nilai_aset / totalVol) || 0),
                        gedung_jumlah_bangunan: totalVol,
                    }];
                }
                return [];
            },

            getJaringanItemsForDetail(item) {
                if (!item) return [];
                const astap = item.astap || item;
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                spec = spec || {};
                if (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) {
                    const valid = spec.jaringan_items.filter(j => j.jaringan_nama_barang || j.jaringan_konstruksi || j.jaringan_panjang || j.jaringan_panjang_km || (parseFloat(j.jaringan_nilai_satuan) > 0));
                    const rawList = valid.length > 0 ? valid : spec.jaringan_items;
                    const toSync = rawList.map(j => {
                        const panjang = j.jaringan_panjang || j.jaringan_panjang_km || spec.jaringan_panjang || spec.panjang_km || '';
                        const lebar = j.jaringan_lebar || j.jaringan_lebar_m || spec.jaringan_lebar || spec.lebar_m || '';
                        const luas = j.jaringan_luas || j.jaringan_luas_m2 || spec.jaringan_luas || spec.luas_m2 || '';
                        const dokNo = j.jaringan_dokumen_no || j.jaringan_dokumen_nomor || spec.dokumen_nomor || '';
                        const konstruksi = j.jaringan_konstruksi || spec.konstruksi || 'Aspal / Beton';
                        return {
                            ...j,
                            jaringan_panjang: panjang,
                            jaringan_panjang_km: panjang,
                            jaringan_lebar: lebar,
                            jaringan_lebar_m: lebar,
                            jaringan_luas: luas,
                            jaringan_luas_m2: luas,
                            jaringan_dokumen_no: dokNo,
                            jaringan_dokumen_nomor: dokNo,
                            jaringan_konstruksi: konstruksi,
                            jaringan_jumlah: j.jaringan_jumlah || j.jaringan_jumlah_barang || 1,
                            jaringan_jumlah_barang: j.jaringan_jumlah_barang || j.jaringan_jumlah || 1,
                        };
                    });
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.jaringan_jumlah || it.jaringan_jumlah_barang) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume || item.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['jaringan_jumlah', 'jaringan_jumlah_barang']);
                }

                if (spec && (spec.konstruksi || spec.luas_m2 || spec.panjang_km || spec.jaringan_luas || spec.jaringan_panjang)) {
                    const totalVol = Math.max(this.getActiveRegisters(astap).length, parseInt(astap.jumlah_volume || item.jumlah_volume) || 1);
                    const panjang = spec.jaringan_panjang || spec.panjang_km || '';
                    const lebar = spec.jaringan_lebar || spec.lebar_m || '';
                    const luas = spec.jaringan_luas || spec.luas_m2 || '';
                    return [{
                        jaringan_nama_barang: astap.nama_barang || item.nama_barang,
                        jaringan_konstruksi: spec.konstruksi || 'Aspal / Beton',
                        jaringan_panjang: panjang,
                        jaringan_panjang_km: panjang,
                        jaringan_lebar: lebar,
                        jaringan_lebar_m: lebar,
                        jaringan_luas: luas,
                        jaringan_luas_m2: luas,
                        jaringan_alamat: astap.alamat_barang || 'Kompleks RSUD',
                        jaringan_status_tanah: spec.status_tanah || 'Tanah Hak Pakai RSUD',
                        jaringan_nilai_satuan: parseFloat(astap.harga_satuan || (item.nilai_aset / totalVol) || 0),
                        jaringan_jumlah: totalVol,
                        jaringan_jumlah_barang: totalVol,
                    }];
                }
                return [];
            },

            getLainnyaItemsForDetail(item) {
                if (!item) return [];
                const astap = item.astap || item;
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                spec = spec || {};
                if (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) {
                    const valid = spec.lainnya_items.filter(l => l.lainnya_nama_barang || l.lainnya_judul || l.lainnya_pencipta || (parseFloat(l.lainnya_nilai_satuan) > 0));
                    const rawList = valid.length > 0 ? valid : spec.lainnya_items;
                    const toSync = rawList.map(l => {
                        const judul = l.lainnya_judul || l.lainnya_judul_pencipta || l.lainnya_nama_barang || spec.judul || astap.nama_barang || '';
                        const pencipta = l.lainnya_pencipta || spec.pencipta || '';
                        const spesifikasi = l.lainnya_spesifikasi || spec.spesifikasi || '';
                        const bahan = l.lainnya_bahan || spec.bahan || '';
                        const ukuran = l.lainnya_ukuran || spec.ukuran || '';
                        return {
                            ...l,
                            lainnya_judul: judul,
                            lainnya_pencipta: pencipta,
                            lainnya_spesifikasi: spesifikasi,
                            lainnya_bahan: bahan,
                            lainnya_ukuran: ukuran,
                            lainnya_jumlah: l.lainnya_jumlah || l.lainnya_jumlah_barang || 1,
                            lainnya_jumlah_barang: l.lainnya_jumlah_barang || l.lainnya_jumlah || 1,
                        };
                    });
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.lainnya_jumlah || it.lainnya_jumlah_barang) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume || item.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['lainnya_jumlah', 'lainnya_jumlah_barang']);
                }

                if (spec && (spec.judul || spec.pencipta || spec.nama_barang || spec.ukuran || spec.bahan || (astap.nama_barang && (astap.nama_barang.toLowerCase().includes('buku') || astap.nama_barang.toLowerCase().includes('kesenian'))))) {
                    const targetTotal = Math.max(this.getActiveRegisters(astap).length, parseInt(astap.jumlah_volume || item.jumlah_volume) || 1);
                    return [{
                        is_extracom: Boolean(spec.is_extracomtable || astap.is_extracomtable),
                        kib_e_type: spec.kib_e_type || 'buku',
                        lainnya_nama_barang: spec.nama_barang || astap.nama_barang,
                        lainnya_judul: spec.judul || astap.nama_barang,
                        lainnya_pencipta: spec.pencipta || '',
                        lainnya_spesifikasi: spec.spesifikasi || spec.keterangan || '',
                        lainnya_bahan: spec.bahan || '',
                        lainnya_jumlah: targetTotal,
                        lainnya_nilai_satuan: parseFloat(astap.harga_satuan || (item.nilai_aset / targetTotal) || 0)
                    }];
                }
                return [];
            },

            getRincianNibar(item, idx = 0, type = null) {
                if (!item) return null;
                const astap = item.astap || item;
                let regs = this.getActiveRegisters(astap);
                if (regs.length === 0) {
                    regs = this.getRegistersList(item);
                }
                if (regs.length === 0) return null;

                if (type) {
                    let items = [];
                    if (type === 'tanah_items') items = this.getTanahItemsForDetail(item);
                    else if (type === 'mesin_items') items = this.getMesinItemsForDetail(item);
                    else if (type === 'gedung_items') items = this.getGedungItemsForDetail(item);
                    else if (type === 'jaringan_items') items = this.getJaringanItemsForDetail(item);
                    else if (type === 'lainnya_items') items = this.getLainnyaItemsForDetail(item);

                    if (items && items.length > 0) {
                        const qtyKeyMap = {
                            'tanah_items': 'tanah_jumlah_bidang',
                            'mesin_items': 'mesin_jumlah_barang',
                            'gedung_items': 'gedung_jumlah_bangunan',
                            'jaringan_items': 'jaringan_jumlah',
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
                if (!firstNibar || firstNibar === '-') return null;

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

            getRincianKondisiStats(item, idx = 0, type = null) {
                if (!item) return { total: 0, text: 'Baik', badge_class: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30', dot_class: 'bg-emerald-400' };
                const astap = item.astap || item;
                let regs = this.getActiveRegisters(astap);
                if (regs.length === 0) regs = this.getRegistersList(item);
                let items = [];

                if (type) {
                    if (type === 'tanah_items') items = this.getTanahItemsForDetail(item);
                    else if (type === 'mesin_items') items = this.getMesinItemsForDetail(item);
                    else if (type === 'gedung_items') items = this.getGedungItemsForDetail(item);
                    else if (type === 'jaringan_items') items = this.getJaringanItemsForDetail(item);
                    else if (type === 'lainnya_items') items = this.getLainnyaItemsForDetail(item);

                    if (items.length > 1) {
                        const qtyKeyMap = {
                            'tanah_items': 'tanah_jumlah_bidang',
                            'mesin_items': 'mesin_jumlah_barang',
                            'gedung_items': 'gedung_jumlah_bangunan',
                            'jaringan_items': 'jaringan_jumlah',
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
                    return {
                        total: 1,
                        baik: 1,
                        kurang_baik: 0,
                        rusak_berat: 0,
                        pct_baik: 100,
                        pct_kb: 0,
                        pct_rb: 0,
                        is_multi: false,
                        text: 'Baik',
                        badge_class: 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30',
                        dot_class: 'bg-emerald-400'
                    };
                }

                let baik = regs.filter(r => (r.kondisi || 'Baik') === 'Baik' || r.kondisi === 'B').length;
                let kb   = regs.filter(r => r.kondisi === 'Kurang Baik' || r.kondisi === 'KB' || r.kondisi === 'Rusak Ringan' || r.kondisi === 'RR').length;
                let rb   = regs.filter(r => r.kondisi === 'Rusak Berat' || r.kondisi === 'RB' || r.kondisi === 'Rusak' || (r.kondisi && (r.kondisi.includes('Rusak') || r.kondisi.includes('Usang') || r.kondisi.includes('Hilang')))).length;

                if (baik === 0 && kb === 0 && rb === 0) {
                    baik = total;
                }

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
                    if (baik === total) text = total > 1 ? `Baik (100%)` : 'Baik';
                    else if (kb === total) text = total > 1 ? `Kurang Baik (100%)` : 'Kurang Baik';
                    else if (rb === total) text = total > 1 ? `Rusak Berat (100%)` : 'Rusak Berat';
                }
                if (!text) text = 'Baik';

                let badgeClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30';
                if (rb > 0) {
                    badgeClass = 'bg-rose-500/20 text-rose-300 border-rose-500/30';
                } else if (kb > 0) {
                    badgeClass = 'bg-amber-500/20 text-amber-300 border-amber-500/30';
                }

                let dotClass = (pct_baik === 100) ? 'bg-emerald-400' : (pct_rb > 0 ? 'bg-rose-400' : 'bg-amber-400');

                const dominan = (baik >= kb && baik >= rb) ? 'Baik' : ((kb >= rb) ? 'Kurang Baik' : 'Rusak Berat');

                return {
                    total,
                    baik, kurang_baik: kb, rusak_berat: rb,
                    pct_baik, pct_kb, pct_rb,
                    kondisi_dominan: dominan,
                    is_multi: !isSingle,
                    parts,
                    text: text,
                    badge_class: badgeClass,
                    dot_class: dotClass
                };
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
                this.detailSearchQuery = '';
                this.detailKondisiFilter = 'all';
                this.detailPenempatanFilter = 'all';
                this.showModalDetail = true;
            },

            // =========================================================================
            // MODAL REKLASIFIKASI ASET TETAP - STANDAR (SAMA DENGAN ASTAP & KEMITRAAN)
            // =========================================================================
            openReklas(item, specificReg = null) {
                if (!item) return;
                const astap = item.astap ? item.astap : item;
                const hibah = item.astap ? item : (item.hibah_detail || item.hibah || {});

                let target = Object.assign({}, astap);

                // 1. Kode Barang 108
                target.kode_barang = target.kode_barang 
                    || target.kode_108 
                    || (target.jenis_astap?.sub_sub_rincian_objek || target.jenis_astap?.sub_rincian_objek || target.jenis_astap?.jenis || '-');
                target.kode_108 = target.kode_barang;

                // 2. Kategori KIB
                target.category = (typeof this.getEffectiveKibCategory === 'function')
                    ? this.getEffectiveKibCategory(target)
                    : (target.category || 'KIB B');

                // 3. Nama Jenis Aset (Kategori)
                target.jenis_aset_nama = target.jenis_aset_nama 
                    || target.jenis_astap?.nama_jenis 
                    || target.spesifikasi_json?.jenis_aset_nama 
                    || target.jenis_astap?.uraian_sub_rincian 
                    || '';

                // 4. Nilai Realisasi & Format Rupiah
                let rawVal = target.total_realisasi_num ?? target.total_realisasi ?? hibah.nilai_aset ?? target.nilai_aset ?? target.harga_satuan ?? 0;
                if (typeof rawVal === 'string') rawVal = parseFloat(rawVal.replace(/[^0-9.-]/g, '')) || 0;
                target.total_realisasi_num = rawVal;
                target.total_realisasi = rawVal;
                target.jumlah_realisasi = 'Rp ' + Number(rawVal).toLocaleString('id-ID');

                // 5. Volume & Satuan
                target.jumlah_volume = parseInt(target.jumlah_volume || hibah.jumlah_volume || 1);
                target.satuan = target.satuan || hibah.satuan || 'Unit';

                // 6. Dokumen BAST / Bukti
                const bNo = hibah.nomor_bast || target.bast_dokumen_nomor || target.bast_nomor || target.spesifikasi_json?.nomor_bast || '';
                if (bNo) {
                    target.bast_dokumen_nomor = bNo;
                    target.nomor_bast = bNo;
                }

                // 7. Registers
                if ((!target.registers || target.registers.length === 0) && hibah.registers) {
                    target.registers = hibah.registers;
                }

                this.selectedAstapReklas = target;

                const cat = target.category;
                const kode = target.kode_barang || target.kode_rekening || '';
                const isExtracomNow = Boolean(target.is_extracomtable || target.is_extracom);
                const isNonExtracom = (kode.startsWith('1.3.1') || kode.startsWith('1.3.3') || kode.startsWith('1.3.4') || kode.startsWith('1.3.6') || kode.startsWith('1.5.3') || cat === 'KIB A' || cat === 'KIB C' || cat === 'KIB D' || cat === 'KIB F' || cat === 'ATB');
                const isKdp = (cat === 'KIB F' || kode.startsWith('1.3.6'));

                // Inisialisasi NIBAR yang dipilih (semua register aktif)
                const activeRegs = (target.registers || []).filter(r => r.status !== 'Mutasi Keluar OPD');
                this.reklasSelectedNibarIds = activeRegs.map(r => r.id);

                // Tentukan jenis reklas default yang paling relevan untuk aset hibah
                if (isExtracomNow) {
                    this.reklasJenis = 'intracom';
                    this.reklasTujuanKib = 'KIB B';
                } else if (isKdp) {
                    this.reklasJenis = 'kdp';
                    this.reklasTujuanKib = 'KIB C';
                } else if (isNonExtracom) {
                    this.reklasJenis = 'pindah_kib';
                    this.reklasTujuanKib = cat === 'KIB A' ? 'KIB C' : 'KIB B';
                } else {
                    this.reklasJenis = 'pindah_kib';
                    this.reklasTujuanKib = (cat === 'KIB B') ? 'KIB C' : 'KIB B';
                }

                // Reset semua field form
                this.reklasTujuanKode = '';
                this.reklasTujuanNama = '';
                this.reklasSubRincianKode = '';
                this.reklasSubRincianNama = '';
                this.reklasSubSubRincianKode = '';
                this.reklasSubSubRincianNama = '';
                this.searchReklasSubRincian = '';
                this.isReklasSubRincianOpen = false;
                this.searchReklasSubSubRincian = '';
                this.isReklasSubSubRincianOpen = false;
                this.reklasTipeKoreksiNilai = 'kurang';
                this.reklasSubKoreksi = 'biasa';
                this.reklasNominalKoreksi = 0;
                this.reklasNilaiRealisasiBaru = target.total_realisasi_num;
                this.reklasNilaiAnggaran = target.total_realisasi_num;
                this.reklasNoDokumenKoreksi = '';
                this.reklasDokumenTglKoreksi = new Date().toLocaleDateString('en-CA');
                this.reklasTanggal = new Date().toLocaleDateString('en-CA');
                this.reklasNomorBa = (target.bast_dokumen_nomor ? ('BAR-' + target.bast_dokumen_nomor) : ('028/BAR-HB/' + new Date().getFullYear()));
                this.reklasAlasan = 'Penyesuaian klasifikasi akun aset hibah masuk sesuai standar pembukuan RSUD.';
                this.reklasExtracomItems = [];
                this.reklasKdpProgresAwal = 0;
                this.isSubmittingReklas = false;
                this.selectedDokumenFile = null;

                this.clearDokumenFile();
                this.initReklasSpekBaru();
                this.showReklasModal = true;
            },

            // =========================================================================
            // REKLAS HELPER FUNCTIONS - SHARED WITH ASTAP & KEMITRAAN MODAL
            // =========================================================================

            // --- Boolean Checks ---
            isItemExtracom(item) {
                if (!item) return false;
                return Boolean(item.is_extracomtable || item.is_extracom);
            },
            isCurrentAstapExtracom() {
                return this.isItemExtracom(this.selectedAstapReklas);
            },
            isTargetExtracom() {
                if (this.reklasJenis === 'extracom') return true;
                if (this.reklasJenis === 'intracom') return false;
                return this.isCurrentAstapExtracom();
            },
            // Hibah tidak punya status kemitraan aktif — selalu false
            isKemitraanAktif() { return false; },
            isKemitraanReturned() { return false; },
            isMutasiKeluarFull() { return false; },
            isCurrentAstapMutasiKeluarFull() {
                if (!this.selectedAstapReklas) return false;
                const it = this.selectedAstapReklas;
                if (it.jenis_reklas === 'MUTASI_EKSTERNAL' || it.is_mutasi_keluar_full) return true;
                if (it.registers && Array.isArray(it.registers) && it.registers.length > 0) {
                    return it.registers.filter(r => r.status !== 'Mutasi Keluar OPD').length === 0;
                }
                return false;
            },
            isCurrentAstapKdp() {
                if (!this.selectedAstapReklas) return false;
                const it = this.selectedAstapReklas;
                const cat = it.category || '';
                const kode = it.kode_barang || it.jenis_aset_kode || '';
                return (cat === 'KIB F' || kode.startsWith('1.3.6'));
            },
            isCurrentAstapKonstruksi() { return this.isCurrentAstapKdp(); },
            isReklasExtracomDisabled() {
                if (!this.selectedAstapReklas) return false;
                if (this.isCurrentAstapExtracom()) return false;
                const it = this.selectedAstapReklas;
                const cat = it.category || '';
                const kode = it.kode_barang || it.jenis_aset_kode || '';
                if (kode.startsWith('1.3.1') || kode.startsWith('1.3.3') || kode.startsWith('1.3.4') || kode.startsWith('1.3.6') || kode.startsWith('1.5.3') || kode.startsWith('1.5.2')) return true;
                return (cat === 'KIB A' || cat === 'KIB C' || cat === 'KIB D' || cat === 'KIB F' || cat === 'ATB' || cat === 'KEMITRAAN');
            },
            isReklasKdpDisabled() {
                if (!this.selectedAstapReklas) return true;
                return !this.isCurrentAstapKdp();
            },
            isReklasBelanjaModal() {
                const it = this.selectedAstapReklas;
                if (!it) return false;
                const sd = String(it.sumber_dana || '').toLowerCase();
                if (sd === 'hibah' || sd === 'kemitraan' || sd === 'belanja_barang' || sd === 'mutasi') return false;
                return true;
            },
            isPartialReklas() {
                if (!this.reklasSpekBaruItems || this.reklasSpekBaruItems.length <= 1) return false;
                const selected = this.getSelectedReklasItems();
                return selected.length > 0 && selected.length < this.reklasSpekBaruItems.length;
            },
            isTanggalBastInvalid() {
                if (!this.reklasTanggalBastMutasi) return false;
                const today = new Date().toISOString().split('T')[0];
                return this.reklasTanggalBastMutasi > today;
            },

            // --- Getters ---
            getReklasNoBuktiDisplay() {
                if (!this.selectedAstapReklas) return '-';
                const it = this.selectedAstapReklas;
                const isValidDoc = (val) => val && typeof val === 'string' && val.trim() !== '' && val.trim() !== '-';
                const bNo = it.bast_dokumen_nomor || it.nomor_bast || it.hibah_nomor_bast || it.spesifikasi_json?.nomor_bast || '';
                if (isValidDoc(bNo)) return `BAST: ${bNo.trim()}`;
                if (isValidDoc(it.spk_nomor)) return `SPK: ${it.spk_nomor.trim()}`;
                if (isValidDoc(this.reklasNomorBa)) return this.reklasNomorBa.trim();
                return 'Bukti Transaksi Hibah';
            },
            getReklasExtracomTotal() {
                if (!this.reklasExtracomItems || this.reklasExtracomItems.length === 0) {
                    return parseFloat(this.selectedAstapReklas?.total_realisasi_num || this.selectedAstapReklas?.harga_satuan || 0);
                }
                return this.reklasExtracomItems.reduce((acc, item) => {
                    return acc + ((parseInt(item.jumlah_volume) || 1) * (parseFloat(item.harga_satuan) || 0));
                }, 0);
            },
            getReklasExtracomTotalVolume() {
                if (!this.reklasExtracomItems || this.reklasExtracomItems.length === 0) {
                    return parseInt(this.selectedAstapReklas?.jumlah_volume) || 1;
                }
                return this.reklasExtracomItems.reduce((acc, item) => acc + (parseInt(item.jumlah_volume) || 1), 0);
            },
            getSelectedReklasItems() {
                if (!this.reklasSpekBaruItems || this.reklasSpekBaruItems.length === 0) {
                    return this.reklasSpekBaru ? [this.reklasSpekBaru] : [];
                }
                return this.reklasSpekBaruItems.filter(item => item.is_selected !== false);
            },
            getUnselectedReklasItems() {
                if (!this.reklasSpekBaruItems || this.reklasSpekBaruItems.length <= 1) return [];
                return this.reklasSpekBaruItems.filter(item => item.is_selected === false);
            },
            getReklasSelectedNilai() {
                const it = this.selectedAstapReklas;
                if (!it) return 0;
                let rawNilai = it.jumlah_realisasi_raw ?? it.total_realisasi_num ?? it.total_realisasi ?? it.harga_satuan ?? 0;
                if (typeof rawNilai === 'string') rawNilai = parseFloat(rawNilai.replace(/[^0-9.-]/g, '')) || 0;
                if (!this.reklasSpekBaruItems || this.reklasSpekBaruItems.length <= 1) return rawNilai;
                const totalUnits = this.reklasSpekBaruItems.length;
                const selectedCount = this.getSelectedReklasItems().length;
                if (selectedCount === totalUnits) return rawNilai;
                return Math.round((rawNilai / totalUnits) * selectedCount);
            },
            getReklasRemainingNilai() {
                const it = this.selectedAstapReklas;
                if (!it) return 0;
                let rawNilai = it.jumlah_realisasi_raw ?? it.total_realisasi_num ?? it.total_realisasi ?? it.harga_satuan ?? 0;
                if (typeof rawNilai === 'string') rawNilai = parseFloat(rawNilai.replace(/[^0-9.-]/g, '')) || 0;
                return Math.max(0, rawNilai - this.getReklasSelectedNilai());
            },
            getReklasAvailableRegisters() {
                if (!this.selectedAstapReklas) return [];
                const it = this.selectedAstapReklas;
                return (it.registers && Array.isArray(it.registers)) ? it.registers : [];
            },
            getRegisterNilaiSatuan(reg) {
                if (reg && reg.harga_satuan && parseFloat(reg.harga_satuan) > 0) return parseFloat(reg.harga_satuan);
                const it = this.selectedAstapReklas;
                if (!it) return 0;
                const totalVal = parseFloat(it.total_realisasi_num || it.total_realisasi || 0);
                const totalVol = parseInt(it.jumlah_volume) || (it.registers ? it.registers.length : 1) || 1;
                return totalVol > 0 ? (totalVal / totalVol) : totalVal;
            },
            getReklasMutasiSelectedVolume() {
                const regs = this.getReklasAvailableRegisters();
                if (!regs || regs.length === 0) return this.selectedAstapReklas ? (parseInt(this.selectedAstapReklas.jumlah_volume) || 1) : 0;
                return this.reklasSelectedNibarIds.length;
            },
            getReklasMutasiSelectedNilai() {
                const regs = this.getReklasAvailableRegisters();
                if (!regs || regs.length === 0) return this.selectedAstapReklas ? parseFloat(this.selectedAstapReklas.total_realisasi_num || 0) : 0;
                let total = 0;
                for (const id of this.reklasSelectedNibarIds) {
                    const r = regs.find(x => x.id === id);
                    if (r) total += this.getRegisterNilaiSatuan(r);
                }
                return total;
            },
            getReklasMutasiRemainingVolume() {
                const regs = this.getReklasAvailableRegisters();
                const activeRegs = regs.filter(r => r.status !== 'Mutasi Keluar OPD');
                return Math.max(0, activeRegs.length - this.reklasSelectedNibarIds.length);
            },
            getReklasMutasiRemainingNilai() {
                const regs = this.getReklasAvailableRegisters();
                const activeRegs = regs.filter(r => r.status !== 'Mutasi Keluar OPD');
                let totalActive = 0;
                for (const r of activeRegs) totalActive += this.getRegisterNilaiSatuan(r);
                return Math.max(0, totalActive - this.getReklasMutasiSelectedNilai());
            },
            getReklasEstimasiTotalSewa() { return 0; },
            getReklasNarasiPreview() {
                const it = this.selectedAstapReklas;
                if (!it) return 'Pilih aset terlebih dahulu...';
                const namaBarang = it.nama_barang || it.astap?.nama_barang || 'Aset Hibah';
                const kodeBarang = it.kode_barang || '';
                const nilaiStr = 'Rp ' + Number(this.getReklasExtracomTotal() || it.total_realisasi_num || 0).toLocaleString('id-ID');
                if (this.reklasJenis === 'extracom') return `Reklasifikasi ${namaBarang} (${kodeBarang}) senilai ${nilaiStr} ke Ekstrakomptabel karena nilai satuan ≤ Rp 300.000.`;
                if (this.reklasJenis === 'intracom') return `Kapitalisasi ${namaBarang} (${kodeBarang}) senilai ${nilaiStr} dari Ekstrakomptabel ke ${this.reklasTujuanKib || 'KIB B'}.`;
                if (this.reklasJenis === 'pindah_kib') return `Reklasifikasi ${namaBarang} (${kodeBarang}) dari ${it.category || 'KIB'} ke ${this.reklasTujuanKib || 'KIB Tujuan'} sesuai standar pembukuan RSUD.`;
                if (this.reklasJenis === 'koreksi_nilai') return `Koreksi nilai ${namaBarang} (${kodeBarang}) sebesar Rp ${Number(this.reklasNominalKoreksi || 0).toLocaleString('id-ID')} (${this.reklasTipeKoreksiNilai === 'kurang' ? 'pengurangan' : 'penambahan'}).`;
                return `Reklasifikasi aset hibah: ${namaBarang}.`;
            },

            // --- Physical Type Helpers (untuk kemitraan - stub untuk hibah) ---
            detectKemitraanPhysicalType(item) {
                const it = item || this.selectedAstapReklas;
                if (!it) return 'mesin';
                const cat = (typeof this.getEffectiveKibCategory === 'function') ? this.getEffectiveKibCategory(it) : (it.category || 'KIB B');
                if (cat === 'KIB A') return 'tanah';
                if (cat === 'KIB C') return 'gedung';
                if (cat === 'KIB D') return 'jaringan';
                if (cat === 'KIB E') return 'lainnya';
                if (cat === 'ATB') return 'atb';
                return 'mesin';
            },
            getReklasKemitraanPhysicalType() { return this.detectKemitraanPhysicalType(); },
            getSourcePhysicalType(item) { return this.detectKemitraanPhysicalType(item || this.selectedAstapReklas); },
            getTargetPhysicalType() { return this.detectKemitraanPhysicalType(); },
            getPhysicalTypeLabel(type) {
                const map = { tanah: 'Tanah (KIB A)', mesin: 'Peralatan & Mesin (KIB B)', gedung: 'Gedung & Bangunan (KIB C)', jaringan: 'Jalan, Irigasi & Jaringan (KIB D)', lainnya: 'Aset Tetap Lainnya (KIB E)', atb: 'Aset Tak Berwujud (ATB)' };
                return map[type] || String(type || '').toUpperCase();
            },

            // --- Nilai & Koreksi ---
            getReklasNilaiBaru() {
                if (!this.selectedAstapReklas) return 0;
                if (this.reklasJenis === 'koreksi_nilai') return parseFloat(this.reklasNilaiRealisasiBaru) || this.getReklasExtracomTotal();
                const baseVal = parseFloat(this.selectedAstapReklas.total_realisasi_num || this.selectedAstapReklas.harga_satuan || 0);
                const selisih = parseFloat(this.reklasNominalKoreksi) || 0;
                return this.reklasTipeKoreksiNilai === 'kurang' ? Math.max(0, baseVal - selisih) : (baseVal + selisih);
            },
            setReklasArahKoreksi(tipe) {
                this.reklasTipeKoreksiNilai = tipe;
                this.reklasNilaiRealisasiBaru = this.getReklasNilaiBaru();
            },
            onReklasSelisihChanged() { this.reklasNilaiRealisasiBaru = this.getReklasNilaiBaru(); },
            onReklasNilaiBaruChanged() {
                if (!this.selectedAstapReklas) return;
                const baseVal = parseFloat(this.selectedAstapReklas.total_realisasi_num || this.selectedAstapReklas.harga_satuan || 0);
                const targetBaru = parseFloat(this.reklasNilaiRealisasiBaru) || 0;
                if (targetBaru < baseVal) { this.reklasTipeKoreksiNilai = 'kurang'; this.reklasNominalKoreksi = Math.max(0, baseVal - targetBaru); }
                else { this.reklasTipeKoreksiNilai = 'tambah'; this.reklasNominalKoreksi = Math.max(0, targetBaru - baseVal); }
            },
            onReklasItemPriceChanged() {
                if (!this.selectedAstapReklas) return;
                const baseVal = parseFloat(this.selectedAstapReklas.total_realisasi_num || this.selectedAstapReklas.harga_satuan || 0);
                const totalNew = this.getReklasExtracomTotal();
                this.reklasNilaiRealisasiBaru = totalNew;
                if (totalNew < baseVal) { this.reklasTipeKoreksiNilai = 'kurang'; this.reklasNominalKoreksi = Math.max(0, baseVal - totalNew); }
                else { this.reklasTipeKoreksiNilai = 'tambah'; this.reklasNominalKoreksi = Math.max(0, totalNew - baseVal); }
            },
            formatRupiahInput(val) {
                const num = Math.round(Number(val));
                return isNaN(num) ? '' : num.toLocaleString('id-ID');
            },
            updateItemHargaSatuan(item, eventOrText) {
                let rawText = '';
                let targetEl = null;
                if (eventOrText && typeof eventOrText === 'object' && eventOrText.target) {
                    targetEl = eventOrText.target; rawText = targetEl.value || '';
                } else { rawText = String(eventOrText || ''); }
                const num = parseInt(String(rawText).replace(/[^0-9]/g, '')) || 0;
                item.harga_satuan = num;
                if (targetEl) targetEl.value = num > 0 ? num.toLocaleString('id-ID') : '';
                this.onReklasItemPriceChanged();
            },
            addExtracomItem() {
                this.reklasExtracomItems.push({
                    nama_barang: '', jumlah_volume: 1,
                    satuan: this.selectedAstapReklas?.satuan || 'Unit',
                    harga_satuan: 0, kode_barang: this.selectedAstapReklas?.kode_barang || '',
                });
            },

            // --- On Change Handlers ---
            onReklasJenisChange() {
                const it = this.selectedAstapReklas;
                if (!it) return;
                const cat = (typeof this.getEffectiveKibCategory === 'function') ? this.getEffectiveKibCategory(it) : (it.category || 'KIB B');
                if (this.reklasJenis === 'pindah_kib') {
                    if (!this.reklasTujuanKib || this.reklasTujuanKib === 'EKSTRAKOMPTABEL') this.reklasTujuanKib = cat && cat !== 'EXTRACOM' ? cat : 'KIB B';
                } else if (this.reklasJenis === 'intracom') {
                    this.reklasTujuanKib = (it.kode_barang || '').startsWith('1.3.5') || cat === 'KIB E' ? 'KIB E' : 'KIB B';
                } else if (this.reklasJenis === 'kdp') {
                    this.reklasTujuanKib = 'KIB C';
                } else if (this.reklasJenis === 'extracom') {
                    this.reklasTujuanKib = 'EKSTRAKOMPTABEL';
                } else if (this.reklasJenis === 'hibah') {
                    this.reklasTujuanKib = 'HIBAH';
                } else if (this.reklasJenis === 'mutasi_eksternal') {
                    this.reklasTujuanKib = 'MUTASI_OPD';
                } else if (this.reklasJenis === 'koreksi_nilai') {
                    this.reklasTujuanKib = cat && cat !== 'EXTRACOM' ? cat : 'KIB B';
                }
                this.clearDokumenFile();
                if (typeof this.initReklasSpekBaru === 'function') this.initReklasSpekBaru();
            },
            onReklasTujuanKibChange() {
                const prefix = this.reklasTargetJenisKode;
                if (prefix) {
                    if (this.reklasSubRincianKode && !this.reklasSubRincianKode.startsWith(prefix)) { this.clearReklasSubRincian(); this.clearReklasSubSubRincian(); }
                    if (this.reklasSubSubRincianKode && !this.reklasSubSubRincianKode.startsWith(prefix)) { this.clearReklasSubSubRincian(); }
                }
                this.reklasTujuanKode = this.reklasSubSubRincianKode || this.reklasSubRincianKode || '';
                this.reklasTujuanNama = this.reklasSubSubRincianNama || this.reklasSubRincianNama || '';
                if (typeof this.initReklasSpekBaru === 'function') this.initReklasSpekBaru();
            },

            // --- Sub-Rincian 108 ---
            get reklasTargetJenisKode() {
                const kibMap = { 'KIB A': '1.3.1', 'KIB B': '1.3.2', 'KIB C': '1.3.3', 'KIB D': '1.3.4', 'KIB E': '1.3.5', 'KIB F': '1.3.6', 'ATB': '1.5.3', 'ASET LAIN': '1.5.4', 'ASET LAIN-LAIN': '1.5.4', 'KEMITRAAN': '1.5.2' };
                return kibMap[this.reklasTujuanKib] || '';
            },
            get reklasTargetJenisAstap() {
                if (!window.dbMasterJenisAstap108 || !this.reklasTujuanKib) return null;
                const prefix = this.reklasTargetJenisKode;
                if (!prefix) return null;
                return window.dbMasterJenisAstap108.find(j => j.kode === prefix || j.kode.startsWith(prefix)) || null;
            },
            get availableReklasSubRincian108() {
                if (this.reklasTargetJenisAstap && this.reklasTargetJenisAstap.subRincian) return this.reklasTargetJenisAstap.subRincian;
                let all = [];
                (window.dbMasterJenisAstap108 || []).forEach(j => { if (j.subRincian) all = all.concat(j.subRincian); });
                return all;
            },
            get filteredReklasSubRincian108() {
                const list = this.availableReklasSubRincian108 || [];
                const q = (this.searchReklasSubRincian || '').toLowerCase().trim();
                if (!q) return list.slice(0, 30);
                return list.filter(s => (s.nama && s.nama.toLowerCase().includes(q)) || (s.kode && s.kode.toLowerCase().includes(q))).slice(0, 30);
            },
            get availableReklasSubSubRincian108() {
                if (this.currentReklasSubRincianObj && this.currentReklasSubRincianObj.subSubRincian) return this.currentReklasSubRincianObj.subSubRincian;
                if (this.reklasTargetJenisAstap) {
                    if (!this.reklasTargetJenisAstap._flatSubSub) {
                        const flat = [];
                        (this.reklasTargetJenisAstap.subRincian || []).forEach(sr => { if (sr.subSubRincian) sr.subSubRincian.forEach(ssr => flat.push(ssr)); });
                        this.reklasTargetJenisAstap._flatSubSub = flat;
                    }
                    return this.reklasTargetJenisAstap._flatSubSub;
                }
                let all = [];
                (window.dbMasterJenisAstap108 || []).forEach(j => { (j.subRincian || []).forEach(sr => { if (sr.subSubRincian) sr.subSubRincian.forEach(ssr => all.push(ssr)); }); });
                return all;
            },
            get filteredReklasSubSubRincian108() {
                const list = this.availableReklasSubSubRincian108 || [];
                const q = (this.searchReklasSubSubRincian || '').toLowerCase().trim();
                if (!q) return [...list].sort((a, b) => (this.getReklas108ItemMeta(b).isOriginMatch ? 1 : 0) - (this.getReklas108ItemMeta(a).isOriginMatch ? 1 : 0)).slice(0, 30);
                return list.filter(item => (item.nama && item.nama.toLowerCase().includes(q)) || (item.kode && item.kode.toLowerCase().includes(q))).slice(0, 30);
            },
            get currentReklasSubRincianObj() {
                if (!this.reklasSubRincianKode) return null;
                return (this.availableReklasSubRincian108 || []).find(s => s.kode === this.reklasSubRincianKode) || null;
            },
            getReklas108ItemMeta(item) {
                if (!item) return { kibTag: '', badgeClass: '', isOriginMatch: false };
                const cd = (item.kode || '').trim();
                const nm = (item.nama || '').toLowerCase().trim();
                const originCat = (this.selectedAstapReklas?.category || '').toUpperCase().trim();
                let kibTag = '', badgeClass = 'bg-slate-800 text-slate-300 border-slate-700', targetKib = '';
                if (cd.startsWith('1.5.4.01.01.02') || (cd.startsWith('1.5.4') && nm.includes('tidak digunakan'))) { kibTag = 'ASET NON-OPERASIONAL'; badgeClass = 'bg-amber-500/20 text-amber-300 border-amber-500/40'; }
                else if (cd.startsWith('1.5.4.01.01.01') || (cd.startsWith('1.5.4') && nm.includes('rusak berat'))) { kibTag = 'RUSAK BERAT / USANG'; badgeClass = 'bg-red-500/20 text-red-300 border-red-500/40'; }
                else if (cd.startsWith('1.5.4')) { kibTag = 'ASET LAIN-LAIN'; badgeClass = 'bg-violet-500/20 text-violet-300 border-violet-500/40'; }
                else if (cd.startsWith('1.5.2')) { kibTag = 'KEMITRAAN'; badgeClass = 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40'; targetKib = 'KEMITRAAN'; }
                else if (cd.startsWith('1.5.3')) { kibTag = 'ATB'; badgeClass = 'bg-fuchsia-500/20 text-fuchsia-300 border-fuchsia-500/40'; targetKib = 'ATB'; }
                else if (cd.startsWith('1.3.1')) { kibTag = 'KIB A • TANAH'; badgeClass = 'bg-rose-500/20 text-rose-300 border-rose-500/40'; targetKib = 'KIB A'; }
                else if (cd.startsWith('1.3.2')) { kibTag = 'KIB B • PERALATAN & MESIN'; badgeClass = 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40'; targetKib = 'KIB B'; }
                else if (cd.startsWith('1.3.3')) { kibTag = 'KIB C • GEDUNG & BANGUNAN'; badgeClass = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'; targetKib = 'KIB C'; }
                else if (cd.startsWith('1.3.4')) { kibTag = 'KIB D • JALAN & JARINGAN'; badgeClass = 'bg-blue-500/20 text-blue-300 border-blue-500/40'; targetKib = 'KIB D'; }
                else if (cd.startsWith('1.3.5')) { kibTag = 'KIB E • ASET TETAP LAINNYA'; badgeClass = 'bg-violet-500/20 text-violet-300 border-violet-500/40'; targetKib = 'KIB E'; }
                else if (cd.startsWith('1.3.6')) { kibTag = 'KIB F • KONSTRUKSI (KDP)'; badgeClass = 'bg-amber-500/20 text-amber-300 border-amber-500/40'; targetKib = 'KIB F'; }
                const isOriginMatch = !!(targetKib && originCat && (targetKib === originCat || (originCat === 'EXTRACOM' && (targetKib === 'KIB B' || targetKib === 'KIB E'))));
                return { kibTag, badgeClass, isOriginMatch };
            },
            selectReklasSubRincian(s) {
                this.reklasSubRincianKode = s.kode;
                this.reklasSubRincianNama = s.nama;
                this.searchReklasSubRincian = '';
                this.isReklasSubRincianOpen = false;
                this.reklasSubSubRincianKode = '';
                this.reklasSubSubRincianNama = '';
                this.searchReklasSubSubRincian = '';
                this.isReklasSubSubRincianOpen = false;
                this.reklasTujuanKode = s.kode;
                this.reklasTujuanNama = s.nama;
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    this.syncKemitraanSkema(s.kode, s.nama);
                }
                if (this.reklasTujuanKib === 'ASET LAIN' || this.reklasTujuanKib === 'ASET LAIN-LAIN' || s.kode.startsWith('1.5.4')) {
                    this.syncAsetLainConditionFromAccount(s.kode, s.nama);
                }
            },
            clearReklasSubRincian() {
                this.reklasSubRincianKode = '';
                this.reklasSubRincianNama = '';
                this.searchReklasSubRincian = '';
                this.isReklasSubRincianOpen = false;
                this.reklasSubSubRincianKode = '';
                this.reklasSubSubRincianNama = '';
                this.searchReklasSubSubRincian = '';
                this.isReklasSubSubRincianOpen = false;
                this.reklasTujuanKode = '';
                this.reklasTujuanNama = '';
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    this.syncKemitraanSkema();
                }
            },
            selectReklasSubSubRincian(item) {
                this.reklasSubSubRincianKode = item.kode;
                this.reklasSubSubRincianNama = item.nama;
                this.searchReklasSubSubRincian = '';
                this.isReklasSubSubRincianOpen = false;
                if (window.dbMasterJenisAstap108 && !this.reklasTujuanKib) {
                    const reverseKibMap = {
                        '1.3.1': 'KIB A',
                        '1.3.2': 'KIB B',
                        '1.3.3': 'KIB C',
                        '1.3.4': 'KIB D',
                        '1.3.5': 'KIB E',
                        '1.3.6': 'KIB F',
                        '1.5.3': 'ATB',
                        '1.5.4': 'ASET LAIN',
                        '1.5.2': 'KEMITRAAN'
                    };
                    for (const j of window.dbMasterJenisAstap108) {
                        for (const sr of (j.subRincian || [])) {
                            if (item.kode.startsWith(sr.kode)) {
                                this.reklasSubRincianKode = sr.kode;
                                this.reklasSubRincianNama = sr.nama;
                                this.reklasTujuanKib = reverseKibMap[j.kode] || '';
                                break;
                            }
                        }
                    }
                }
                this.reklasTujuanKode = item.kode;
                this.reklasTujuanNama = item.nama;
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    this.syncKemitraanSkema(item.kode, item.nama);
                }
                if (this.reklasTujuanKib === 'ASET LAIN' || this.reklasTujuanKib === 'ASET LAIN-LAIN' || item.kode.startsWith('1.5.4')) {
                    this.syncAsetLainConditionFromAccount(item.kode, item.nama);
                }
            },
            clearReklasSubSubRincian() {
                this.reklasSubSubRincianKode = '';
                this.reklasSubSubRincianNama = '';
                this.searchReklasSubSubRincian = '';
                this.isReklasSubSubRincianOpen = false;
                this.reklasTujuanKode = this.reklasSubRincianKode || '';
                this.reklasTujuanNama = this.reklasSubRincianNama || '';
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    this.reklasKemitraanTipeFisik = this.detectKemitraanPhysicalType();
                    this.syncKemitraanSkema();
                }
            },
            toggleSelectAllNibars(selectAll = true) {
                if (!selectAll) { this.reklasSelectedNibarIds = []; return; }
                const regs = this.getReklasAvailableRegisters();
                this.reklasSelectedNibarIds = regs.filter(r => r.status !== 'Mutasi Keluar OPD').map(r => r.id);
            },

            // --- Dok File & Misc ---
            clearDokumenFile() {
                this.selectedDokumenFile = null;
                const el = document.getElementById('astap_reklas_dokumen_file_input');
                if (el) el.value = '';
                const elKor = document.getElementById('astap_reklas_koreksi_dokumen_file');
                if (elKor) elKor.value = '';
            },

            detectKemitraanSkema(kode, nama) {
                const k = (kode || '').trim();
                const n = (nama || '').toLowerCase();
                if (k.includes('.01.01.02') || k.endsWith('.01.02') || k === '1.5.2.01.01.02' || n.includes('kerjasama pemanfaatan') || n.includes('kerja sama pemanfaatan') || n.includes('pemanfaatan') || n.includes('kp')) {
                    return 'Kerjasama Pemanfaatan (KP)';
                }
                if (k.includes('.01.01.03') || k.endsWith('.01.03') || k === '1.5.2.01.01.03' || n.includes('bangun guna') || n.includes('bangun serah') || n.includes('bgs') || n.includes('bsg')) {
                    return 'BGS/BSG';
                }
                if (k.includes('.01.01.04') || k.endsWith('.01.04') || k === '1.5.2.01.01.04' || n.includes('infrastruktur') || n.includes('kspi')) {
                    return 'KSPI';
                }
                if (k.includes('.01.01.01') || k.endsWith('.01.01') || k === '1.5.2.01.01.01' || n.includes('sewa')) {
                    return 'Sewa';
                }
                if (k.endsWith('.02')) return 'Kerjasama Pemanfaatan (KP)';
                if (k.endsWith('.03')) return 'BGS/BSG';
                if (k.endsWith('.04')) return 'KSPI';
                if (k.endsWith('.01')) return 'Sewa';
                return 'Sewa';
            },

            syncKemitraanSkema(kode, nama) {
                const targetKode = kode || this.reklasSubRincianKode || this.reklasSubSubRincianKode || this.reklasTujuanKode || '';
                const targetNama = nama || this.reklasSubRincianNama || this.reklasSubSubRincianNama || this.reklasTujuanNama || '';
                const detected = this.detectKemitraanSkema(targetKode, targetNama);
                if (this.reklasSpekBaru) {
                    this.reklasSpekBaru.kemitraan_skema = detected;
                }
                if (Array.isArray(this.reklasSpekBaruItems)) {
                    this.reklasSpekBaruItems.forEach(item => {
                        if (item) item.kemitraan_skema = detected;
                    });
                }
                return detected;
            },

            syncAsetLainConditionFromAccount(kode, nama) {
                if (!this.reklasSpekBaru) this.reklasSpekBaru = {};
                const cd = (kode || this.reklasSubSubRincianKode || this.reklasSubRincianKode || this.reklasTujuanKode || '').trim();
                const nm = (nama || this.reklasSubSubRincianNama || this.reklasSubRincianNama || this.reklasTujuanNama || '').toLowerCase().trim();

                let targetKondisi = 'Rusak Berat (Menunggu Penghapusan)';
                let mappedStd = 'Rusak Berat';

                if (cd.startsWith('1.5.4.01.01.01') || nm.includes('rusak berat') || nm.includes('usang')) {
                    targetKondisi = 'Rusak Berat (Menunggu Penghapusan)';
                    mappedStd = 'Rusak Berat';
                } else if (cd.startsWith('1.5.4.01.01.03.019') || nm.includes('sewa dibayar dimuka') || nm.includes('pinjam pakai') || nm.includes('akan diserahkan')) {
                    targetKondisi = 'Baik';
                    mappedStd = 'Baik';
                } else if (cd.startsWith('1.5.4.01.01.03.003') || nm.includes('hilang')) {
                    targetKondisi = 'Hilang / Tidak Ditemukan';
                    mappedStd = 'Rusak Berat';
                } else {
                    targetKondisi = 'Rusak Berat (Menunggu Penghapusan)';
                    mappedStd = 'Rusak Berat';
                }

                this.reklasSpekBaru.aset_lain_kondisi = targetKondisi;
                this.reklasSpekBaru.kondisi = mappedStd;

                if (Array.isArray(this.reklasSpekBaruItems)) {
                    this.reklasSpekBaruItems.forEach(u => {
                        if (u) {
                            u.aset_lain_kondisi = targetKondisi;
                            u.kondisi = mappedStd;
                        }
                    });
                }
            },

            // --- Pembentukan Spesifikasi Fisik Baru Sesuai KIB Tujuan ---
            initReklasSpekBaru() {
                const it = this.selectedAstapReklas;
                if (!it) return;
                let spec = it.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                if (!spec || typeof spec !== 'object') spec = {};

                const target = this.reklasTujuanKib;
                const defaultAlamat = it.alamat_barang || spec.alamat || 'RSUD Dr. H. Koesnandi';

                // Sub-item arrays jika ada (dari form pengadaan astap multi-item)
                const mItemsDetail = (typeof this.getMesinItemsForDetail === 'function') ? this.getMesinItemsForDetail(it) : [];
                const tItemsDetail = (typeof this.getTanahItemsForDetail === 'function') ? this.getTanahItemsForDetail(it) : [];
                const gItemsDetail = (typeof this.getGedungItemsForDetail === 'function') ? this.getGedungItemsForDetail(it) : [];
                const jItemsDetail = (typeof this.getJaringanItemsForDetail === 'function') ? this.getJaringanItemsForDetail(it) : [];
                const lItemsDetail = (typeof this.getLainnyaItemsForDetail === 'function') ? this.getLainnyaItemsForDetail(it) : [];
                const aItemsDetail = (typeof this.getAtbItemsForDetail === 'function') ? this.getAtbItemsForDetail(it) : [];

                const mItems = mItemsDetail.length > 0 ? mItemsDetail : (Array.isArray(spec.mesin_items) ? spec.mesin_items : []);
                const tItems = tItemsDetail.length > 0 ? tItemsDetail : (Array.isArray(spec.tanah_items) ? spec.tanah_items : []);
                const gItems = gItemsDetail.length > 0 ? gItemsDetail : (Array.isArray(spec.gedung_items) ? spec.gedung_items : []);
                const jItems = jItemsDetail.length > 0 ? jItemsDetail : (Array.isArray(spec.jaringan_items) ? spec.jaringan_items : []);
                const lItems = lItemsDetail.length > 0 ? lItemsDetail : (Array.isArray(spec.lainnya_items) ? spec.lainnya_items : []);
                const aItems = aItemsDetail.length > 0 ? aItemsDetail : (Array.isArray(spec.atb_items) ? spec.atb_items : []);
                const kData = it.kemitraan || {};

                const sourceKib = (typeof this.getEffectiveKibCategory === 'function')
                    ? this.getEffectiveKibCategory(it)
                    : (it.category || 'KIB B');

                let existingItems = [];
                const pType = (typeof this.getSourcePhysicalType === 'function')
                    ? this.getSourcePhysicalType(it)
                    : ((typeof this.detectKemitraanPhysicalType === 'function') ? this.detectKemitraanPhysicalType(it) : '');
                if (pType === 'gedung' && gItems.length > 0) existingItems = gItems;
                else if (pType === 'mesin' && mItems.length > 0) existingItems = mItems;
                else if (pType === 'tanah' && tItems.length > 0) existingItems = tItems;
                else if (pType === 'jaringan' && jItems.length > 0) existingItems = jItems;
                else if (pType === 'lainnya' && lItems.length > 0) existingItems = lItems;
                else if (pType === 'atb' && aItems.length > 0) existingItems = aItems;
                else if (sourceKib === 'KIB A' && tItems.length > 0) existingItems = tItems;
                else if (sourceKib === 'KIB B' && mItems.length > 0) existingItems = mItems;
                else if (sourceKib === 'KIB C' && gItems.length > 0) existingItems = gItems;
                else if (sourceKib === 'KIB D' && jItems.length > 0) existingItems = jItems;
                else if (sourceKib === 'KIB E' && Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) existingItems = lItems;
                else if (sourceKib === 'ATB' && Array.isArray(spec.atb_items) && spec.atb_items.length > 0) existingItems = aItems;
                else if (this.reklasExtracomItems && this.reklasExtracomItems.length > 1) {
                    existingItems = this.reklasExtracomItems;
                } else {
                    if (gItems.length > 0) existingItems = gItems;
                    else if (mItems.length > 0) existingItems = mItems;
                    else if (tItems.length > 0) existingItems = tItems;
                    else if (jItems.length > 0) existingItems = jItems;
                    else if (lItems.length > 0) existingItems = lItems;
                    else if (aItems.length > 0) existingItems = aItems;
                }

                const regs = Array.isArray(it.registers) ? it.registers : [];
                let totalUnits = Math.max(
                    regs.length > 0 ? regs.length : 1,
                    parseInt(it.jumlah_volume || 1) || 1,
                    existingItems.length > 1 ? existingItems.length : 1,
                    (this.reklasExtracomItems && this.reklasExtracomItems.length > 1 ? this.reklasExtracomItems.length : 1)
                );

                const createDefaultSpekForUnit = (idx) => {
                    const reg = regs[idx] || null;
                    const currItem = existingItems[idx]
                        || (this.reklasExtracomItems && this.reklasExtracomItems[idx])
                        || existingItems[0]
                        || {};

                    const tItem = tItems[idx] || (sourceKib === 'KIB A' || pType === 'tanah' ? currItem : (tItems[0] || currItem || {}));
                    const mItem = mItems[idx] || (sourceKib === 'KIB B' || pType === 'mesin' ? currItem : (mItems[0] || currItem || {}));
                    const gItem = gItems[idx] || (sourceKib === 'KIB C' || pType === 'gedung' ? currItem : (gItems[0] || currItem || {}));
                    const jItem = jItems[idx] || (sourceKib === 'KIB D' || pType === 'jaringan' ? currItem : (jItems[0] || currItem || {}));
                    const lItem = lItems[idx] || (sourceKib === 'KIB E' || pType === 'lainnya' ? currItem : (lItems[0] || currItem || {}));
                    const aItem = aItems[idx] || (sourceKib === 'ATB' || pType === 'atb' ? currItem : (aItems[0] || currItem || {}));

                    const isDash = (v) => !v || String(v).trim() === '' || String(v).trim() === '-' || String(v).trim() === '--' || String(v).trim() === '- -';
                    const candidateNames = [
                        currItem.mesin_nama_barang,
                        currItem.lainnya_nama_barang,
                        currItem.gedung_nama_bangunan,
                        currItem.jaringan_nama,
                        currItem.tanah_nama_barang,
                        currItem.atb_judul_nama,
                        currItem.atb_nama_software,
                        currItem.nama_barang,
                        (this.reklasExtracomItems && this.reklasExtracomItems[idx] ? this.reklasExtracomItems[idx].nama_barang : null),
                        it.nama_barang
                    ];
                    const itemNama = candidateNames.find(n => !isDash(n)) || `Barang #${idx + 1}`;

                    const itemVol = parseInt(currItem.mesin_jumlah_barang || currItem.lainnya_jumlah || currItem.gedung_jumlah_bangunan || currItem.jaringan_jumlah || currItem.tanah_jumlah_bidang || currItem.atb_jumlah_barang || currItem.jumlah_volume || (this.reklasExtracomItems && this.reklasExtracomItems[idx] ? this.reklasExtracomItems[idx].jumlah_volume : 1)) || 1;
                    const itemSatuan = currItem.mesin_satuan || currItem.lainnya_satuan || currItem.gedung_satuan || currItem.jaringan_satuan || currItem.tanah_satuan || currItem.atb_satuan || (this.reklasExtracomItems && this.reklasExtracomItems[idx] ? this.reklasExtracomItems[idx].satuan : null) || it.satuan || 'Unit';

                    const targetIsAsetLain = (target === 'ASET LAIN' || target === 'ASET LAIN-LAIN' || (this.reklasTargetJenisKode && this.reklasTargetJenisKode.startsWith('1.5.4')));
                    const cd108 = (this.reklasSubSubRincianKode || this.reklasSubRincianKode || this.reklasTujuanKode || '').trim();
                    const nm108 = (this.reklasSubSubRincianNama || this.reklasSubRincianNama || this.reklasTujuanNama || '').toLowerCase().trim();

                    const defAsetLainKondisi = (() => {
                        if (spec.aset_lain_kondisi) return spec.aset_lain_kondisi;
                        if (cd108.startsWith('1.5.4.01.01.01') || nm108.includes('rusak berat') || nm108.includes('usang')) {
                            return 'Rusak Berat (Menunggu Penghapusan)';
                        }
                        if (cd108.startsWith('1.5.4.01.01.03.019') || nm108.includes('sewa dibayar dimuka') || nm108.includes('pinjam pakai')) {
                            return 'Baik';
                        }
                        if (cd108.startsWith('1.5.4.01.01.03.003') || nm108.includes('hilang')) {
                            return 'Hilang / Tidak Ditemukan';
                        }
                        return targetIsAsetLain ? 'Rusak Berat (Menunggu Penghapusan)' : (reg?.kondisi || it.kondisi || 'Baik');
                    })();

                    const defUnitKondisi = (() => {
                        if (targetIsAsetLain) {
                            if (defAsetLainKondisi === 'Baik' || defAsetLainKondisi.startsWith('Baik')) return 'Baik';
                            if (defAsetLainKondisi === 'Kurang Baik' || defAsetLainKondisi.startsWith('Kurang Baik')) return 'Kurang Baik';
                            return 'Rusak Berat';
                        }
                        return reg?.kondisi || it.kondisi || 'Baik';
                    })();

                    return {
                        unit_index: idx,
                        is_selected: true,
                        item_nama: itemNama,
                        item_volume: itemVol,
                        item_satuan: itemSatuan,
                        nibar: reg?.nibar || reg?.no_register || `Unit #${idx + 1}`,
                        ruang_pemegang: reg?.ruang_pemegang || it.ruang_unit || '-',
                        kondisi: defUnitKondisi,

                        // KIB A - Tanah
                        tanah_luas_m2: tItem.tanah_luas_m2 || tItem.tanah_luas || spec.luas_m2 || spec.tanah_luas_m2 || (it.luas_m2 && it.luas_m2 > 0 ? it.luas_m2 : '') || '',
                        tanah_hak: tItem.tanah_hak || tItem.hak_tanah || spec.hak_tanah || spec.tanah_hak || (it.hak_tanah && it.hak_tanah !== '-' ? it.hak_tanah : 'Hak Pakai'),
                        tanah_sertifikat_no: tItem.tanah_sertifikat_no || tItem.sertifikat_no || spec.sertifikat_no || spec.tanah_sertifikat_no || spec.sertifikat_nomor || (it.sertifikat_nomor && it.sertifikat_nomor !== '-' ? it.sertifikat_nomor : '') || '',
                        tanah_sertifikat_tgl: (() => {
                            let t = tItem.tanah_sertifikat_tgl || tItem.sertifikat_tgl || spec.sertifikat_tgl || spec.tanah_sertifikat_tgl || spec.sertifikat_tanggal || (it.sertifikat_tanggal && it.sertifikat_tanggal !== '-' ? it.sertifikat_tanggal : '') || '';
                            if (t && typeof t === 'string' && t.includes('/')) {
                                let parts = t.split('/');
                                if (parts.length === 3 && parts[2].length === 4) return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                            }
                            return t;
                        })(),
                        tanah_penggunaan: tItem.tanah_penggunaan || tItem.penggunaan || spec.penggunaan || spec.tanah_penggunaan || (it.penggunaan && it.penggunaan !== '-' ? it.penggunaan : '') || itemNama || 'Bangunan Rumah Sakit & Fasilitas',
                        tanah_asal_usul: tItem.tanah_asal_usul || spec.tanah_asal_usul || spec.asal_usul || (it.asal_usul && it.asal_usul !== '-' ? it.asal_usul : 'Hibah Masuk'),
                        tanah_alamat: tItem.tanah_alamat || spec.tanah_alamat || spec.alamat || it.alamat_barang || defaultAlamat,

                        // KIB B - Mesin
                        mesin_merk: mItem.mesin_merk || spec.merk || it.merk || (it.merk_type && it.merk_type !== '-' ? it.merk_type : '') || '',
                        mesin_type: mItem.mesin_type || spec.type || it.type || '',
                        mesin_no_pabrik: mItem.mesin_nomor_pabrik || mItem.mesin_no_pabrik || spec.no_pabrik || spec.mesin_no_pabrik || (it.no_pabrik && it.no_pabrik !== '-' ? it.no_pabrik : '') || '',
                        mesin_ukuran: mItem.mesin_ukuran || mItem.mesin_ukuran_cc || spec.ukuran_cc || spec.ukuran || (it.ukuran && it.ukuran !== '-' ? it.ukuran : '') || '',
                        mesin_ukuran_cc: mItem.mesin_ukuran || mItem.mesin_ukuran_cc || spec.ukuran_cc || spec.ukuran || (it.ukuran && it.ukuran !== '-' ? it.ukuran : '') || '',
                        mesin_bahan: mItem.mesin_bahan || spec.bahan || (it.bahan && it.bahan !== '-' ? it.bahan : 'Logam / Komponen Elektronik'),
                        mesin_no_rangka: mItem.mesin_no_rangka || spec.no_rangka || spec.mesin_no_rangka || (it.no_rangka && it.no_rangka !== '-' ? it.no_rangka : '') || '',
                        mesin_no_mesin: mItem.mesin_no_mesin || spec.no_mesin || spec.mesin_no_mesin || (it.no_mesin && it.no_mesin !== '-' ? it.no_mesin : '') || '',
                        mesin_no_bpkb: mItem.mesin_no_bpkb || spec.no_bpkb || spec.mesin_no_bpkb || (it.no_bpkb && it.no_bpkb !== '-' ? it.no_bpkb : '') || '',
                        mesin_no_polisi: mItem.mesin_nomor_polisi || mItem.mesin_no_polisi || spec.no_polisi || (it.no_polisi && it.no_polisi !== '-' ? it.no_polisi : '') || '',
                        mesin_kondisi: mItem.mesin_kondisi || reg?.kondisi || it.kondisi || 'Baik',

                        // KIB C - Gedung
                        gedung_konstruksi_bertingkat: (() => {
                            let b = gItem.gedung_bertingkat || spec.konstruksi_bertingkat || spec.bertingkat || (it.gedung_bertingkat && it.gedung_bertingkat !== '-' ? it.gedung_bertingkat : 'Bertingkat');
                            return (b === 'Tidak') ? 'Tidak Bertingkat' : b;
                        })(),
                        gedung_konstruksi_beton: (() => {
                            let bt = gItem.gedung_beton || spec.konstruksi_beton || spec.beton || (it.gedung_beton && it.gedung_beton !== '-' ? it.gedung_beton : 'Beton');
                            return (bt === 'Tidak' || bt === 'Bukan Beton') ? 'Tidak' : 'Beton';
                        })(),
                        gedung_luas_lantai_m2: gItem.gedung_luas_lantai || gItem.gedung_luas_lantai_m2 || gItem.luas_lantai || gItem.gedung_luas_m2 || spec.gedung_luas_lantai || spec.luas_lantai_m2 || spec.luas_m2 || (it.luas_m2 && it.luas_m2 > 0 ? it.luas_m2 : '') || '',
                        gedung_dokumen_nomor: gItem.gedung_dokumen_no || gItem.gedung_dokumen_nomor || gItem.dokumen_nomor || spec.gedung_dokumen_no || spec.dokumen_nomor || it.bast_dokumen_nomor || it.nomor_bast || '',
                        gedung_dokumen_tgl: (() => {
                            let t = gItem.gedung_dokumen_tgl || spec.gedung_dokumen_tgl || spec.dokumen_tgl || it.spk_tanggal || it.bast_tanggal || '';
                            if (t && typeof t === 'string' && t.includes('/')) {
                                let parts = t.split('/');
                                if (parts.length === 3 && parts[2].length === 4) return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
                            }
                            return t;
                        })(),
                        gedung_status_tanah: gItem.gedung_status_tanah || spec.gedung_status_tanah || spec.status_tanah || (it.gedung_status_tanah && it.gedung_status_tanah !== '-' ? it.gedung_status_tanah : 'Tanah Pemda'),
                        gedung_alamat: gItem.gedung_alamat || spec.gedung_alamat || spec.alamat || it.alamat_barang || defaultAlamat,

                        // KIB D - Jaringan
                        jaringan_konstruksi: jItem.jaringan_konstruksi || spec.konstruksi || (it.jaringan_konstruksi && it.jaringan_konstruksi !== '-' ? it.jaringan_konstruksi : 'Aspal / Beton'),
                        jaringan_panjang_km: jItem.jaringan_panjang_m || spec.panjang_km || spec.panjang_m || (it.jaringan_panjang_m && it.jaringan_panjang_m > 0 ? it.jaringan_panjang_m : '') || '',
                        jaringan_lebar_m: jItem.jaringan_lebar_m || spec.lebar_m || (it.jaringan_lebar_m && it.jaringan_lebar_m > 0 ? it.jaringan_lebar_m : '') || '',
                        jaringan_luas_m2: jItem.jaringan_luas_m2 || spec.luas_m2 || (it.jaringan_luas_m2 && it.jaringan_luas_m2 > 0 ? it.jaringan_luas_m2 : '') || '',
                        jaringan_alamat: jItem.jaringan_alamat || spec.jaringan_alamat || spec.alamat || it.alamat_barang || defaultAlamat,

                        // KIB E - Lainnya
                        is_extracom: !!(lItem.is_extracom || currItem.is_extracom || it.is_extracomtable || (spec && spec.is_extracomtable) || (parseFloat(lItem.lainnya_nilai_satuan || currItem.harga_satuan || it.harga_satuan || 0) <= 300000)),
                        kib_e_type: (() => {
                            if (lItem.kib_e_sub_type) return lItem.kib_e_sub_type;
                            if (lItem.kib_e_type) return lItem.kib_e_type;
                            if (spec.kib_e_sub_type) return spec.kib_e_sub_type;
                            if (spec.kib_e_type) return spec.kib_e_type;
                            const kode = (this.reklasSubSubRincianKode || this.reklasSubRincianKode || this.reklasTujuanKode || it.kode_108 || it.kode_barang || '').trim();
                            if (kode.includes('.02') || kode.includes('1.3.5.02') || lItem.lainnya_kesenian_asal || lItem.lainnya_kesenian_pencipta || (lItem.lainnya_asal_daerah && lItem.lainnya_asal_daerah !== '-')) return 'kesenian';
                            if (kode.includes('.03') || kode.includes('1.3.5.03') || lItem.lainnya_hewan_jenis || lItem.lainnya_hewan_judul || spec.hewan_jenis || spec.hewan_judul) return 'hewan_tumbuhan';
                            return 'buku';
                        })(),
                        ...(() => {
                            const kibESub = lItem.kib_e_sub_type || lItem.kib_e_type || spec.kib_e_sub_type || spec.kib_e_type || 'buku';
                            const specificJudul = (kibESub === 'kesenian')
                                ? (lItem.lainnya_kesenian_judul || lItem.lainnya_judul || spec.kesenian_judul || spec.judul)
                                : ((kibESub === 'hewan_tumbuhan')
                                    ? (lItem.lainnya_hewan_judul || lItem.lainnya_hewan_jenis || lItem.lainnya_judul || spec.hewan_judul || spec.hewan_jenis || spec.judul)
                                    : (lItem.lainnya_buku_judul || lItem.lainnya_judul || spec.buku_judul || spec.judul));

                            const specificPencipta = (kibESub === 'kesenian')
                                ? (lItem.lainnya_kesenian_pencipta || lItem.lainnya_pencipta || spec.kesenian_pencipta || spec.pencipta)
                                : (lItem.lainnya_buku_pencipta || lItem.lainnya_pencipta || spec.buku_pencipta || spec.pencipta);

                            const specificSpesifikasi = (kibESub === 'kesenian')
                                ? (lItem.lainnya_kesenian_spesifikasi || lItem.lainnya_spesifikasi || spec.kesenian_spesifikasi || spec.spesifikasi)
                                : ((kibESub === 'hewan_tumbuhan')
                                    ? (lItem.lainnya_hewan_spesifikasi || lItem.lainnya_spesifikasi || spec.hewan_spesifikasi || spec.spesifikasi)
                                    : (lItem.lainnya_buku_spesifikasi || lItem.lainnya_spesifikasi || spec.buku_spesifikasi || spec.spesifikasi));

                            const finalJudul = specificJudul
                                || lItem.lainnya_buku_judul
                                || lItem.lainnya_kesenian_judul
                                || lItem.lainnya_hewan_judul
                                || lItem.lainnya_hewan_jenis
                                || lItem.lainnya_judul
                                || spec.buku_judul
                                || spec.kesenian_judul
                                || spec.hewan_judul
                                || spec.judul
                                || lItem.lainnya_nama_barang
                                || itemNama
                                || '';

                            const finalPencipta = specificPencipta
                                || lItem.lainnya_buku_pencipta
                                || lItem.lainnya_kesenian_pencipta
                                || lItem.lainnya_pencipta
                                || spec.buku_pencipta
                                || spec.kesenian_pencipta
                                || spec.pencipta
                                || (it.pencipta && it.pencipta !== '-' ? it.pencipta : '')
                                || '';

                            const finalSpesifikasi = specificSpesifikasi
                                || lItem.lainnya_buku_spesifikasi
                                || lItem.lainnya_kesenian_spesifikasi
                                || lItem.lainnya_hewan_spesifikasi
                                || lItem.lainnya_spesifikasi
                                || spec.buku_spesifikasi
                                || spec.kesenian_spesifikasi
                                || spec.hewan_spesifikasi
                                || spec.spesifikasi
                                || (it.spesifikasi && it.spesifikasi !== '-' ? it.spesifikasi : '')
                                || '';

                            const finalAsalDaerah = lItem.lainnya_kesenian_asal
                                || lItem.lainnya_asal_daerah
                                || spec.kesenian_asal
                                || spec.asal_daerah
                                || (it.asal_daerah && it.asal_daerah !== '-' ? it.asal_daerah : '')
                                || '';

                            const finalBahan = lItem.lainnya_kesenian_bahan
                                || lItem.lainnya_bahan
                                || spec.kesenian_bahan
                                || spec.bahan
                                || (it.bahan && it.bahan !== '-' ? it.bahan : 'Kertas / Kanvas / Lainnya');

                            const finalUkuran = lItem.lainnya_kesenian_ukuran
                                || lItem.lainnya_ukuran
                                || spec.kesenian_ukuran
                                || spec.ukuran
                                || (it.ukuran && it.ukuran !== '-' ? it.ukuran : '')
                                || '';

                            const finalJudulPencipta = (finalJudul && finalPencipta)
                                ? `${finalJudul} - ${finalPencipta}`
                                : (finalJudul || lItem.lainnya_nama_barang || itemNama || '');

                            return {
                                lainnya_judul: finalJudul,
                                lainnya_pencipta: finalPencipta,
                                lainnya_spesifikasi: finalSpesifikasi,
                                lainnya_asal_daerah: finalAsalDaerah,
                                lainnya_bahan: finalBahan,
                                lainnya_ukuran: finalUkuran,
                                lainnya_no_pabrik: lItem.lainnya_no_pabrik || lItem.no_pabrik || spec.no_pabrik || '',
                                lainnya_keterangan: lItem.lainnya_keterangan || spec.keterangan || '',
                                lainnya_kondisi: lItem.lainnya_kondisi || reg?.kondisi || it.kondisi || 'Baik',
                                lainnya_judul_pencipta: finalJudulPencipta,
                                lainnya_buku_judul: finalJudul,
                                lainnya_buku_pencipta: finalPencipta,
                                lainnya_buku_spesifikasi: finalSpesifikasi,
                                lainnya_kesenian_judul: finalJudul,
                                lainnya_kesenian_asal: finalAsalDaerah,
                                lainnya_kesenian_pencipta: finalPencipta,
                                lainnya_kesenian_spesifikasi: finalSpesifikasi,
                                lainnya_kesenian_bahan: finalBahan,
                                lainnya_kesenian_ukuran: finalUkuran,
                                lainnya_hewan_jenis: finalJudul,
                                lainnya_hewan_judul: finalJudul,
                                lainnya_hewan_spesifikasi: finalSpesifikasi,
                            };
                        })(),

                        // ATB - Aset Tak Berwujud
                        atb_judul_nama: aItem.atb_judul_nama || aItem.atb_nama_software || spec.atb_judul_nama || spec.nama_software || itemNama || '',
                        atb_nama_software: aItem.atb_judul_nama || aItem.atb_nama_software || spec.atb_judul_nama || spec.nama_software || itemNama || '',
                        atb_pencipta: aItem.atb_pencipta || aItem.atb_pengembang || spec.atb_pencipta || spec.pengembang || '',
                        atb_pengembang: aItem.atb_pencipta || aItem.atb_pengembang || spec.atb_pencipta || spec.pengembang || '',
                        atb_spesifikasi: aItem.atb_spesifikasi || spec.atb_spesifikasi || spec.spesifikasi || '',
                        atb_masa_manfaat: aItem.atb_masa_manfaat || spec.masa_manfaat || '4',
                        atb_nomor_lisensi: aItem.atb_nomor_lisensi || spec.nomor_lisensi || '',

                        // KEMITRAAN - Kemitraan Pihak Ketiga (1.5.2)
                        kemitraan_skema: kData.skema_kemitraan || spec.kemitraan_skema || spec.skema || 'Sewa',
                        kemitraan_mitra: kData.mitra_nama || spec.mitra || spec.mitra_nama || spec.kemitraan_mitra || '',
                        kemitraan_pimpinan: spec.pimpinan || spec.mitra_pimpinan || spec.kemitraan_pimpinan || '',
                        kemitraan_alamat: spec.alamat || spec.mitra_alamat || spec.kemitraan_alamat || '',
                        kemitraan_perjanjian_no: kData.nomor_pks || spec.perjanjian_no || spec.nomor_pks || spec.kemitraan_perjanjian_no || '',
                        kemitraan_tanggal_pks: kData.tanggal_pks || spec.tanggal_pks || spec.kemitraan_tanggal_pks || new Date().toISOString().split('T')[0],
                        kemitraan_tanggal_mulai: kData.tanggal_mulai || spec.tanggal_mulai || spec.kemitraan_tanggal_mulai || new Date().toISOString().split('T')[0],
                        kemitraan_tanggal_selesai: kData.tanggal_selesai || spec.tanggal_selesai || spec.kemitraan_tanggal_selesai || '',
                        kemitraan_jangka_waktu: spec.jangka_waktu || spec.kemitraan_jangka_waktu || '5 Tahun',
                        kemitraan_harga_sewa: spec.harga_sewa || spec.kemitraan_harga_sewa || kData.harga_sewa || '',
                        kemitraan_periode_sewa: spec.periode_sewa || spec.kemitraan_periode_sewa || kData.periode_sewa || 'Per Tahun',
                        kemitraan_nilai_kontribusi: spec.nilai_kontribusi || spec.kemitraan_nilai_kontribusi || kData.nilai_kontribusi || '',
                        kemitraan_keterangan_kontribusi: spec.keterangan_kontribusi || spec.kemitraan_keterangan_kontribusi || '',

                        // ASET LAIN
                        aset_lain_kondisi: defAsetLainKondisi,
                        aset_lain_alasan: spec.alasan || spec.aset_lain_alasan || 'Pengalihan ke Akun 1.5.4 Aset Lain-Lain',
                    };
                };

                this.reklasSpekBaruItems = [];
                for (let i = 0; i < totalUnits; i++) {
                    this.reklasSpekBaruItems.push(createDefaultSpekForUnit(i));
                }

                this.activeSpekUnitTab = 0;
                if (this.reklasSpekBaruItems.length > 0) {
                    this.reklasSpekBaru = Object.assign({}, this.reklasSpekBaruItems[0]);
                }
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    this.syncKemitraanSkema();
                }
            },

            toggleItemReklasSelection(idx) {
                if (!this.reklasSpekBaruItems || !this.reklasSpekBaruItems[idx]) return;
                this.syncCurrentSpekToActiveItem();
                const current = this.reklasSpekBaruItems[idx].is_selected !== false;
                const selectedCount = this.reklasSpekBaruItems.filter(it => it.is_selected !== false).length;
                if (current && selectedCount <= 1) {
                    this.showToast('Minimal 1 rincian barang harus dipilih untuk dialihkan!', 'warning');
                    return;
                }
                this.reklasSpekBaruItems[idx].is_selected = !current;
                if (this.activeSpekUnitTab === idx) {
                    this.reklasSpekBaru.is_selected = !current;
                }
            },

            getUnitEstimatedPrice(idx) {
                const it = this.selectedAstapReklas;
                if (!it) return 0;
                let rawNilai = it.jumlah_realisasi_raw ?? it.total_realisasi_num ?? it.total_realisasi ?? it.harga_satuan ?? 0;
                if (typeof rawNilai === 'string') rawNilai = parseFloat(rawNilai.replace(/[^0-9.-]/g, '')) || 0;
                const totalUnits = (this.reklasSpekBaruItems && this.reklasSpekBaruItems.length > 0) ? this.reklasSpekBaruItems.length : 1;
                return Math.round(rawNilai / totalUnits);
            },

            switchActiveSpekUnit(idx) {
                if (!this.reklasSpekBaruItems || idx < 0 || idx >= this.reklasSpekBaruItems.length) return;
                this.syncCurrentSpekToActiveItem();
                this.activeSpekUnitTab = idx;
                this.reklasSpekBaru = Object.assign({}, this.reklasSpekBaruItems[idx]);
            },

            syncCurrentSpekToActiveItem() {
                if (this.reklasSpekBaruItems && this.reklasSpekBaruItems[this.activeSpekUnitTab]) {
                    if (this.reklasSpekBaru.kondisi) {
                        this.reklasSpekBaru.mesin_kondisi = this.reklasSpekBaru.kondisi;
                        this.reklasSpekBaru.lainnya_kondisi = this.reklasSpekBaru.kondisi;
                    }
                    if (this.reklasSpekBaru.mesin_ukuran_cc) {
                        this.reklasSpekBaru.mesin_ukuran = this.reklasSpekBaru.mesin_ukuran_cc;
                    }
                    if (this.reklasSpekBaru.lainnya_judul) {
                        this.reklasSpekBaru.item_nama = this.reklasSpekBaru.lainnya_judul;
                        this.reklasSpekBaru.lainnya_nama_barang = this.reklasSpekBaru.lainnya_judul;
                        this.reklasSpekBaru.lainnya_judul_pencipta = (this.reklasSpekBaru.lainnya_judul || '') + (this.reklasSpekBaru.lainnya_pencipta ? ' - ' + this.reklasSpekBaru.lainnya_pencipta : '');
                    }
                    if (this.reklasSpekBaru.kib_e_type === 'buku') {
                        this.reklasSpekBaru.lainnya_buku_judul = this.reklasSpekBaru.lainnya_judul;
                        this.reklasSpekBaru.lainnya_buku_pencipta = this.reklasSpekBaru.lainnya_pencipta;
                        this.reklasSpekBaru.lainnya_buku_spesifikasi = this.reklasSpekBaru.lainnya_spesifikasi;
                    } else if (this.reklasSpekBaru.kib_e_type === 'kesenian') {
                        this.reklasSpekBaru.lainnya_kesenian_judul = this.reklasSpekBaru.lainnya_judul;
                        this.reklasSpekBaru.lainnya_kesenian_asal = this.reklasSpekBaru.lainnya_asal_daerah;
                        this.reklasSpekBaru.lainnya_kesenian_pencipta = this.reklasSpekBaru.lainnya_pencipta;
                        this.reklasSpekBaru.lainnya_kesenian_spesifikasi = this.reklasSpekBaru.lainnya_spesifikasi;
                        this.reklasSpekBaru.lainnya_kesenian_bahan = this.reklasSpekBaru.lainnya_bahan;
                        this.reklasSpekBaru.lainnya_kesenian_ukuran = this.reklasSpekBaru.lainnya_ukuran;
                    } else if (this.reklasSpekBaru.kib_e_type === 'hewan_tumbuhan') {
                        this.reklasSpekBaru.lainnya_hewan_jenis = this.reklasSpekBaru.lainnya_judul;
                        this.reklasSpekBaru.lainnya_hewan_judul = this.reklasSpekBaru.lainnya_judul;
                        this.reklasSpekBaru.lainnya_hewan_spesifikasi = this.reklasSpekBaru.lainnya_spesifikasi;
                    }

                    if (this.reklasTujuanKib === 'ATB') {
                        if (this.reklasSpekBaru.atb_judul_nama) {
                            this.reklasSpekBaru.atb_nama_software = this.reklasSpekBaru.atb_judul_nama;
                            this.reklasSpekBaru.item_nama = this.reklasSpekBaru.atb_judul_nama;
                        }
                        if (this.reklasSpekBaru.atb_pencipta) {
                            this.reklasSpekBaru.atb_pengembang = this.reklasSpekBaru.atb_pencipta;
                        }
                    }

                    if (this.reklasTujuanKib === 'ASET LAIN' || this.reklasTujuanKib === 'ASET LAIN-LAIN' || (this.reklasTargetJenisKode && this.reklasTargetJenisKode.startsWith('1.5.4'))) {
                        const alk = this.reklasSpekBaru.aset_lain_kondisi || 'Rusak Berat (Menunggu Penghapusan)';
                        let std = 'Rusak Berat';
                        if (alk === 'Baik' || alk.startsWith('Baik')) std = 'Baik';
                        else if (alk === 'Kurang Baik' || alk.startsWith('Kurang Baik')) std = 'Kurang Baik';
                        this.reklasSpekBaru.kondisi = std;
                    }
                    Object.assign(this.reklasSpekBaruItems[this.activeSpekUnitTab], this.reklasSpekBaru);
                }
            },

            copyActiveSpekToAll() {
                this.syncCurrentSpekToActiveItem();
                const current = Object.assign({}, this.reklasSpekBaru);
                this.reklasSpekBaruItems.forEach((item) => {
                    const keepProps = {
                        unit_index: item.unit_index,
                        is_selected: item.is_selected,
                        nibar: item.nibar,
                        ruang_pemegang: item.ruang_pemegang,
                        kondisi: item.kondisi,
                        item_nama: item.item_nama,
                        item_volume: item.item_volume,
                        item_satuan: item.item_satuan,
                    };
                    Object.assign(item, current, keepProps);
                });
                this.showToast('Spesifikasi item aktif berhasil disalin ke seluruh ' + this.reklasSpekBaruItems.length + ' rincian barang!', 'success');
            },

            getUnitTabTitle(idx) {
                const item = this.reklasSpekBaruItems[idx];
                if (!item) return `Barang #${idx + 1}`;
                const num = idx + 1;
                const cat = (typeof this.getEffectiveKibCategory === 'function') 
                    ? this.getEffectiveKibCategory(this.selectedAstapReklas) 
                    : (this.selectedAstapReklas?.category || '');

                if (cat === 'KIB A' || String(item.item_nama || '').toLowerCase().includes('tanah')) {
                    const raw = (item.tanah_nama_barang || item.item_nama || '').trim();
                    const astapName = (this.selectedAstapReklas?.nama_barang || '').trim();
                    if (raw && raw !== astapName && !raw.toLowerCase().startsWith('bidang tanah') && !raw.toLowerCase().startsWith('tanah bangunan')) {
                        return `Bidang #${num}: ${raw}`;
                    }
                    return `Bidang Tanah #${num}`;
                }

                if (cat === 'KIB C' || String(item.item_nama || '').toLowerCase().includes('gedung') || String(item.item_nama || '').toLowerCase().includes('bangunan')) {
                    const raw = (item.gedung_nama_bangunan || item.item_nama || '').trim();
                    const astapName = (this.selectedAstapReklas?.nama_barang || '').trim();
                    if (raw && raw !== astapName && !raw.toLowerCase().startsWith('bangunan #') && !raw.toLowerCase().startsWith('gedung #')) {
                        return `Bangunan #${num}: ${raw}`;
                    }
                    return `Bangunan #${num}`;
                }

                if (cat === 'KIB D' || String(item.item_nama || '').toLowerCase().includes('jaringan') || String(item.item_nama || '').toLowerCase().includes('pipa')) {
                    const raw = (item.jaringan_nama || item.item_nama || '').trim();
                    const astapName = (this.selectedAstapReklas?.nama_barang || '').trim();
                    if (raw && raw !== astapName && !raw.toLowerCase().startsWith('jaringan #')) {
                        return `Jaringan #${num}: ${raw}`;
                    }
                    return `Jaringan #${num}`;
                }

                if (cat === 'KIB B') {
                    const merk = (item.mesin_merk || '').trim();
                    const type = (item.mesin_type || '').trim();
                    const merkType = [merk, type].filter(Boolean).join(' ');
                    const raw = (item.mesin_nama_barang || item.item_nama || '').trim();
                    const astapName = (this.selectedAstapReklas?.nama_barang || '').trim();

                    if (merkType && !raw.toLowerCase().startsWith('barang #')) {
                        return `Barang #${num}: ${merkType}`;
                    }
                    if (raw && raw !== astapName && !raw.toLowerCase().startsWith('barang #')) {
                        return `Barang #${num}: ${raw}`;
                    }
                    return `Barang #${num}`;
                }

                if (cat === 'ATB') {
                    const sw = (item.atb_judul_nama || item.atb_nama_software || item.item_nama || '').trim();
                    const astapName = (this.selectedAstapReklas?.nama_barang || '').trim();
                    if (sw && sw !== astapName && !sw.toLowerCase().startsWith('software #')) {
                        return `Software #${num}: ${sw}`;
                    }
                    return `Software #${num}`;
                }

                const raw = (item.item_nama || '').trim();
                const astapName = (this.selectedAstapReklas?.nama_barang || '').trim();
                if (raw && raw !== astapName && !raw.toLowerCase().startsWith('barang #')) {
                    return `Barang #${num}: ${raw}`;
                }
                return `Barang #${num}`;
            },

            getUnitFullTitle(idx) {
                const item = this.reklasSpekBaruItems[idx];
                if (!item) return '';
                const astapName = (this.selectedAstapReklas?.nama_barang || '').trim();
                const cat = (typeof this.getEffectiveKibCategory === 'function') 
                    ? this.getEffectiveKibCategory(this.selectedAstapReklas) 
                    : (this.selectedAstapReklas?.category || '');

                if (cat === 'KIB A') {
                    const p = item.tanah_penggunaan || item.tanah_alamat || astapName;
                    return p || 'Aset Tanah RSUD';
                }
                if (cat === 'KIB B') {
                    const merk = [item.mesin_merk, item.mesin_type].filter(Boolean).join(' ');
                    return merk || item.mesin_nama_barang || astapName || 'Peralatan & Mesin';
                }
                if (cat === 'KIB C') {
                    return item.gedung_nama_bangunan || astapName || 'Bangunan Gedung';
                }
                if (cat === 'KIB D') {
                    return item.jaringan_nama || astapName || 'Jaringan / Instalasi';
                }
                return item.item_nama || astapName || 'Barang Aset';
            },

            getUnitTabSubtitle(uItem) {
                if (!uItem) return '';
                const vol = uItem.item_volume || 1;
                const sat = uItem.item_satuan || 'Unit';
                const r = uItem.ruang_pemegang && uItem.ruang_pemegang !== '-' ? uItem.ruang_pemegang : '';
                return r ? `${vol} ${sat} • ${r}` : `${vol} ${sat}`;
            },

            parseKemitraanDuration(text) {
                if (!text || typeof text !== 'string') return null;
                const str = text.trim().toLowerCase();
                if (!str) return null;
                let totalMonths = 0;
                let totalDays = 0;
                let matched = false;

                const decimalYearMatch = str.match(/^([\d]+[.,][\d]+)\s*(?:tahun|thn|th|y|year|years)?$/);
                if (decimalYearMatch) {
                    const num = parseFloat(decimalYearMatch[1].replace(',', '.'));
                    if (!isNaN(num) && num > 0) {
                        const m = Math.round(num * 12);
                        return { years: Math.floor(m / 12), months: m % 12, days: 0 };
                    }
                }

                const yearMatch = str.match(/(\d+)\s*(?:tahun|thn|th|y|year|years)\b/);
                if (yearMatch) { totalMonths += parseInt(yearMatch[1], 10) * 12; matched = true; }

                const monthMatch = str.match(/(\d+)\s*(?:bulan|bln|bl|mo|month|months)\b/);
                if (monthMatch) { totalMonths += parseInt(monthMatch[1], 10); matched = true; }

                const dayMatch = str.match(/(\d+)\s*(?:hari|hr|d|day|days)\b/);
                if (dayMatch) { totalDays += parseInt(dayMatch[1], 10); matched = true; }

                if (!matched) {
                    const plainNumMatch = str.match(/^(\d+)$/);
                    if (plainNumMatch) {
                        const num = parseInt(plainNumMatch[1], 10);
                        if (num > 0) {
                            if (num <= 30) totalMonths += num * 12;
                            else totalDays += num;
                            matched = true;
                        }
                    }
                }
                if (!matched || (totalMonths === 0 && totalDays === 0)) return null;
                return { years: Math.floor(totalMonths / 12), months: totalMonths % 12, days: totalDays };
            },

            computeKemitraanEndDate(startDateStr, durationObj) {
                if (!startDateStr || !durationObj) return null;
                let d;
                if (typeof startDateStr === 'string' && startDateStr.includes('/')) {
                    const p = startDateStr.split('/');
                    d = new Date(parseInt(p[2], 10), parseInt(p[1], 10) - 1, parseInt(p[0], 10));
                } else {
                    const p = String(startDateStr).split('T')[0].split('-');
                    d = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                }
                if (isNaN(d.getTime())) return null;
                if (durationObj.years > 0) d.setFullYear(d.getFullYear() + durationObj.years);
                if (durationObj.months > 0) d.setMonth(d.getMonth() + durationObj.months);
                if (durationObj.days > 0) d.setDate(d.getDate() + durationObj.days);
                const yyyy = d.getFullYear();
                const mm = String(d.getMonth() + 1).padStart(2, '0');
                const dd = String(d.getDate()).padStart(2, '0');
                return `${yyyy}-${mm}-${dd}`;
            },

            formatKemitraanDurationFromDates(startDateStr, endDateStr) {
                if (!startDateStr || !endDateStr) return '';
                let dM, dS;
                if (typeof startDateStr === 'string' && startDateStr.includes('/')) {
                    const p = startDateStr.split('/');
                    dM = new Date(parseInt(p[2], 10), parseInt(p[1], 10) - 1, parseInt(p[0], 10));
                } else {
                    const p = String(startDateStr).split('T')[0].split('-');
                    dM = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                }
                if (typeof endDateStr === 'string' && endDateStr.includes('/')) {
                    const p = endDateStr.split('/');
                    dS = new Date(parseInt(p[2], 10), parseInt(p[1], 10) - 1, parseInt(p[0], 10));
                } else {
                    const p = String(endDateStr).split('T')[0].split('-');
                    dS = new Date(parseInt(p[0], 10), parseInt(p[1], 10) - 1, parseInt(p[2], 10));
                }
                if (isNaN(dM.getTime()) || isNaN(dS.getTime()) || dS < dM) return '';
                let years = dS.getFullYear() - dM.getFullYear();
                let months = dS.getMonth() - dM.getMonth();
                let days = dS.getDate() - dM.getDate();
                if (days < 0) {
                    months -= 1;
                    const prevMonthDate = new Date(dS.getFullYear(), dS.getMonth(), 0);
                    days += prevMonthDate.getDate();
                }
                if (months < 0) {
                    years -= 1;
                    months += 12;
                }
                const parts = [];
                if (years > 0) parts.push(`${years} Tahun`);
                if (months > 0) parts.push(`${months} Bulan`);
                if (days > 0) parts.push(`${days} Hari`);
                if (parts.length === 0) {
                    const diffMs = dS.getTime() - dM.getTime();
                    const diffDays = Math.round(diffMs / (1000 * 60 * 60 * 24));
                    return diffDays > 0 ? `${diffDays} Hari` : '1 Hari';
                }
                return parts.join(' ');
            },

            calcReklasKemitraanDurasi() {
                const tM = this.reklasSpekBaru?.kemitraan_tanggal_mulai;
                const tS = this.reklasSpekBaru?.kemitraan_tanggal_selesai;
                if (!tM || !tS) return;
                const durText = this.formatKemitraanDurationFromDates(tM, tS);
                if (durText) this.reklasSpekBaru.kemitraan_jangka_waktu = durText;
            },

            onReklasKemitraanDurasiInput() {
                const durText = this.reklasSpekBaru?.kemitraan_jangka_waktu;
                if (!durText) return;
                const parsed = this.parseKemitraanDuration(durText);
                if (!parsed) return;
                let tM = this.reklasSpekBaru.kemitraan_tanggal_mulai;
                if (!tM) {
                    tM = new Date().toISOString().split('T')[0];
                    this.reklasSpekBaru.kemitraan_tanggal_mulai = tM;
                }
                const newEnd = this.computeKemitraanEndDate(tM, parsed);
                if (newEnd) this.reklasSpekBaru.kemitraan_tanggal_selesai = newEnd;
            },

            onReklasKemitraanTanggalMulaiChange() {
                const durText = this.reklasSpekBaru?.kemitraan_jangka_waktu;
                const parsed = durText ? this.parseKemitraanDuration(durText) : null;
                const tM = this.reklasSpekBaru?.kemitraan_tanggal_mulai;
                if (parsed && tM) {
                    const newEnd = this.computeKemitraanEndDate(tM, parsed);
                    if (newEnd) {
                        this.reklasSpekBaru.kemitraan_tanggal_selesai = newEnd;
                        return;
                    }
                }
                this.calcReklasKemitraanDurasi();
            },

            setReklasKemitraanDurasiQuick(amount, unit) {
                const unitText = unit === 'tahun' ? 'Tahun' : (unit === 'bulan' ? 'Bulan' : 'Hari');
                this.reklasSpekBaru.kemitraan_jangka_waktu = `${amount} ${unitText}`;
                let tM = this.reklasSpekBaru.kemitraan_tanggal_mulai;
                if (!tM) {
                    tM = new Date().toISOString().split('T')[0];
                    this.reklasSpekBaru.kemitraan_tanggal_mulai = tM;
                }
                const parsed = {
                    years: unit === 'tahun' ? amount : 0,
                    months: unit === 'bulan' ? amount : 0,
                    days: unit === 'hari' ? amount : 0
                };
                const newEnd = this.computeKemitraanEndDate(tM, parsed);
                if (newEnd) this.reklasSpekBaru.kemitraan_tanggal_selesai = newEnd;
            },

            // --- SKPD & Pejabat Helpers ---
            selectSkpd(skpd) {
                this.reklasSkpdTujuan = skpd;
                this.isSkpdDropdownOpen = false;
                if (this.skpdDirectory && this.skpdDirectory[skpd]) {
                    if (this.skpdDirectory[skpd].alamat) {
                        this.reklasAlamatInstansi = this.skpdDirectory[skpd].alamat;
                    }
                    const pejabats = this.skpdDirectory[skpd].pejabats || [];
                    if (pejabats.length === 1) {
                        this.selectPejabatTujuan(pejabats[0]);
                    }
                }
            },

            rememberCustomPejabat(nama, nip, jabatan) {
                if (!nama || !nama.trim()) return;
                const cleanNama = nama.trim();
                const cleanNip = (nip || '').trim();
                const cleanJabatan = (jabatan || '').trim();
                try {
                    let custom = JSON.parse(localStorage.getItem('rsud_custom_pejabats') || '[]');
                    const idx = custom.findIndex(p => p.nama && p.nama.toLowerCase() === cleanNama.toLowerCase());
                    if (idx >= 0) {
                        custom[idx] = { nama: cleanNama, nip: cleanNip || custom[idx].nip, jabatan: cleanJabatan || custom[idx].jabatan };
                    } else {
                        custom.unshift({ nama: cleanNama, nip: cleanNip, jabatan: cleanJabatan });
                    }
                    localStorage.setItem('rsud_custom_pejabats', JSON.stringify(custom.slice(0, 50)));

                    if (!this.dbPejabats) this.dbPejabats = [];
                    const memIdx = this.dbPejabats.findIndex(p => p.nama && p.nama.toLowerCase() === cleanNama.toLowerCase());
                    if (memIdx >= 0) {
                        this.dbPejabats[memIdx] = { nama: cleanNama, nip: cleanNip || this.dbPejabats[memIdx].nip, jabatan: cleanJabatan || this.dbPejabats[memIdx].jabatan };
                    } else {
                        this.dbPejabats.unshift({ nama: cleanNama, nip: cleanNip, jabatan: cleanJabatan });
                    }
                } catch (e) {}
            },

            selectPejabatTujuan(p) {
                if (!p) return;
                this.reklasPejabatOpdTujuan = p.nama || '';
                this.reklasNipPejabatOpdTujuan = p.nip || '';
                if (p.jabatan) this.reklasJabatanOpdTujuan = p.jabatan;
                this.isPejabatTujuanDropdownOpen = false;
                this.rememberCustomPejabat(p.nama, p.nip, p.jabatan);
            },

            selectPejabatAsal(p) {
                if (!p) return;
                this.reklasPjAsalNama = p.nama || '';
                this.reklasPjAsalNip = p.nip || '';
                this.reklasPjAsalJabatan = p.jabatan || '';
                this.isPejabatAsalDropdownOpen = false;
                this.rememberCustomPejabat(p.nama, p.nip, p.jabatan);
            },

            handleDokumenFile(e) {
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                if (file.size > 10 * 1024 * 1024) {
                    this.showToast('Ukuran file dokumen melebihi 10MB!', 'error');
                    e.target.value = '';
                    return;
                }
                this.selectedDokumenFile = file;
            },

            get filteredSkpdList() {
                const q = (this.reklasSkpdTujuan || '').toLowerCase().trim();
                const list = this.skpdList || [];
                if (!q) return list.slice(0, 25);
                return list.filter(s => s.toLowerCase().includes(q)).slice(0, 25);
            },
            get availablePejabatTujuanList() {
                const skpd = this.reklasSkpdTujuan;
                if (!skpd || !this.skpdDirectory || !this.skpdDirectory[skpd]) return [];
                return this.skpdDirectory[skpd].pejabats || [];
            },
            get filteredPejabatTujuanList() {
                const list = this.availablePejabatTujuanList;
                const q = (this.reklasPejabatOpdTujuan || '').toLowerCase().trim();
                if (!q) return list;
                return list.filter(p => (p.nama || p).toLowerCase().includes(q));
            },
            get availablePejabatAsalList() {
                let localSaved = [];
                try {
                    localSaved = JSON.parse(localStorage.getItem('rsud_custom_pejabats') || '[]');
                } catch (e) {}

                const defaults = [
                    { nama: 'BUDI HARTONO, S.Sos', nip: '19760229 200801 1 010', jabatan: 'Pengurus Barang Pengguna' },
                    { nama: 'dr. YUS PRIYATNA ADISIWIBOWO, Sp.P', nip: '19760814 200212 1 005', jabatan: 'Direktur RSUD dr. H. Koesnadi' }
                ];

                const combined = [...localSaved, ...(this.dbPejabats || []), ...defaults];
                const seen = new Set();
                const list = [];
                for (const p of combined) {
                    if (!p || !p.nama) continue;
                    const key = p.nama.trim().toLowerCase();
                    if (!seen.has(key)) {
                        seen.add(key);
                        list.push({
                            nama: p.nama.trim(),
                            nip: (p.nip || '').trim(),
                            jabatan: (p.jabatan || '').trim() || 'Pengurus Barang / Pejabat RSUD'
                        });
                    }
                }
                return list;
            },
            get filteredPejabatAsalList() {
                const list = this.availablePejabatAsalList;
                const q = (this.reklasPjAsalNama || '').toLowerCase().trim();
                if (!q) return list;
                return list.filter(p => 
                    (p.nama && p.nama.toLowerCase().includes(q)) ||
                    (p.nip && p.nip.toLowerCase().includes(q)) ||
                    (p.jabatan && p.jabatan.toLowerCase().includes(q))
                );
            },

            // --- Label Helpers ---
            getRmbAsalLabel() {
                if (!this.selectedAstapReklas) return '-';
                const cat = this.selectedAstapReklas.category || 'KIB B';
                if (this.reklasJenis === 'intracom') return 'Ekstrakomptabel (Luar Neraca)';
                if (this.reklasJenis === 'extracom') return `${cat} (Neraca Aset Tetap)`;
                return `${cat} (KIB Asal / Akun Lama)`;
            },
            getRmbTujuanLabel() {
                if (!this.selectedAstapReklas) return '-';
                if (this.reklasJenis === 'extracom') return 'Ekstrakomptabel (Luar Neraca)';
                if (this.reklasJenis === 'intracom') return `${this.reklasTujuanKib || 'KIB B'} (Neraca Aset Tetap)`;
                if (this.reklasJenis === 'pindah_kib') return `${this.reklasTujuanKib || 'KIB Tujuan'}${this.reklasTujuanKode ? ' (' + this.reklasTujuanKode + ')' : ''}`;
                if (this.reklasJenis === 'koreksi_nilai') { const sign = this.reklasTipeKoreksiNilai === 'kurang' ? '-' : '+'; return `Koreksi ${sign} Rp ${Number(this.reklasNominalKoreksi || 0).toLocaleString('id-ID')}`; }
                return 'Rekening Simda Baru';
            },

            // --- SUBMIT REKLAS (Identik & Lengkap Seperti ASTAP) ---
            async submitReklas() {
                if (!this.selectedAstapReklas) return;
                const alasanClean = (this.reklasAlasan || '').trim();
                if (!alasanClean) { this.showToast('Alasan reklasifikasi wajib diisi!', 'error'); return; }

                if (this.isCurrentAstapKdp() && ['pindah_kib', 'hibah', 'mutasi_eksternal', 'extracom', 'intracom'].includes(this.reklasJenis)) {
                    this.showToast('Aset KDP tidak dapat dipindah KIB, dihibahkan, atau dimutasi. Gunakan Kapitalisasi KDP Selesai atau Koreksi Nilai.', 'error');
                    return;
                }
                if (this.reklasJenis === 'extracom') {
                    if (!this.reklasExtracomItems || this.reklasExtracomItems.length === 0) { this.showToast('Minimal 1 rincian barang diperlukan untuk Ekstrakomtable.', 'error'); return; }
                    for (let i = 0; i < this.reklasExtracomItems.length; i++) {
                        const rItem = this.reklasExtracomItems[i];
                        const hrg = parseFloat(rItem.harga_satuan) || 0;
                        const vol = parseInt(rItem.jumlah_volume) || 0;
                        if (vol < 1) { this.showToast(`Volume minimal 1 untuk "${rItem.nama_barang || 'Barang #' + (i+1)}"`, 'error'); return; }
                        if (hrg <= 0) { this.showToast(`Harga satuan harus > Rp 0 untuk "${rItem.nama_barang || 'Barang #' + (i+1)}"`, 'error'); return; }
                        if (hrg > 300000) { this.showToast(`Harga satuan melebihi batas Ekstrakomtable (max Rp 300.000)!`, 'error'); return; }
                    }
                } else if (this.reklasJenis === 'intracom') {
                    if (!this.reklasExtracomItems || this.reklasExtracomItems.length === 0) { this.showToast('Minimal 1 rincian barang diperlukan untuk Intrakomtable.', 'error'); return; }
                    for (let i = 0; i < this.reklasExtracomItems.length; i++) {
                        const rItem = this.reklasExtracomItems[i];
                        const hrg = parseFloat(rItem.harga_satuan) || 0;
                        const vol = parseInt(rItem.jumlah_volume) || 0;
                        if (vol < 1) { this.showToast(`Volume minimal 1!`, 'error'); return; }
                        if (hrg <= 300000) { this.showToast(`Harga satuan harus > Rp 300.000 untuk Intrakomtable!`, 'error'); return; }
                    }
                } else if (this.reklasJenis === 'koreksi_nilai') {
                    const nom = parseFloat(this.reklasNominalKoreksi) || 0;
                    if (nom <= 0) { this.showToast('Selisih koreksi nilai tidak boleh Rp 0!', 'error'); return; }
                } else if (this.reklasJenis === 'hibah') {
                    if (!this.reklasNomorBastHibah || !this.reklasNomorBastHibah.trim()) { this.showToast('Nomor BAST Hibah wajib diisi!', 'warning'); return; }
                } else if (this.reklasJenis === 'mutasi_eksternal') {
                    if (!this.reklasSkpdTujuan || !this.reklasSkpdTujuan.trim()) { this.showToast('SKPD / Dinas Penerima Mutasi wajib diisi!', 'warning'); return; }
                    if (!this.reklasNomorBastMutasi || !this.reklasNomorBastMutasi.trim()) { this.showToast('Nomor BAST Mutasi wajib diisi!', 'warning'); return; }
                    if (this.isTanggalBastInvalid()) { this.showToast('Tanggal BAST tidak boleh melebihi hari ini!', 'error'); return; }
                }

                if (typeof this.syncCurrentSpekToActiveItem === 'function') this.syncCurrentSpekToActiveItem();
                this.isSubmittingReklas = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                try {
                    const it = this.selectedAstapReklas;
                    const tgl = this.reklasTanggal || new Date().toISOString().split('T')[0];
                    const month = new Date(tgl).getMonth() + 1;
                    const tw = Math.ceil(month / 3);
                    const thn = new Date(tgl).getFullYear() || new Date().getFullYear();

                    let jenisReklasDb = 'KOREKSI_REKENING';
                    if (this.reklasJenis === 'extracom') jenisReklasDb = 'EKSTRAKOMPTABEL';
                    else if (this.reklasJenis === 'intracom') jenisReklasDb = 'KAPITALISASI_INTRAKOM';
                    else if (this.reklasJenis === 'kdp') jenisReklasDb = 'KDP_TO_DEFINITIF';
                    else if (this.reklasJenis === 'koreksi_nilai') jenisReklasDb = 'KOREKSI_LAIN';
                    else if (this.reklasJenis === 'hibah') jenisReklasDb = 'HIBAH_KELUAR';
                    else if (this.reklasJenis === 'mutasi_eksternal') jenisReklasDb = 'MUTASI_EKSTERNAL';

                    let asalKib = it.category || 'KIB B';
                    let targetKib = this.reklasTujuanKib || 'KIB C';
                    if (this.reklasJenis === 'intracom') { asalKib = 'EKSTRAKOMPTABEL'; targetKib = this.reklasTujuanKib || 'KIB B'; }
                    else if (this.reklasJenis === 'extracom') { targetKib = 'EKSTRAKOMPTABEL'; }
                    else if (this.reklasJenis === 'kdp') { asalKib = 'KIB F'; targetKib = this.reklasTujuanKib === 'KIB D' ? 'KIB D' : 'KIB C'; }
                    else if (this.reklasJenis === 'hibah') { asalKib = it.category || 'KIB B'; targetKib = 'HIBAH'; }
                    else if (this.reklasJenis === 'mutasi_eksternal') { targetKib = 'MUTASI_OPD'; }

                    const isPartial = this.isPartialReklas();
                    const selectedItems = this.getSelectedReklasItems();
                    const unselectedItems = this.getUnselectedReklasItems();
                    const selectedIndices = (this.reklasSpekBaruItems && this.reklasSpekBaruItems.length > 0)
                        ? this.reklasSpekBaruItems.map((item, idx) => item.is_selected !== false ? idx : null).filter(x => x !== null)
                        : [0];

                    if (this.reklasSpekBaruItems && this.reklasSpekBaruItems.length > 1 && selectedItems.length === 0) {
                        this.showToast('Pilih minimal 1 rincian barang yang akan dialihkan!', 'warning');
                        this.isSubmittingReklas = false;
                        return;
                    }

                    let rawNilai = it.jumlah_realisasi_raw ?? it.total_realisasi_num ?? it.total_realisasi ?? it.harga_satuan ?? 0;
                    if (typeof rawNilai === 'string') rawNilai = parseFloat(rawNilai.replace(/[^0-9.-]/g, '')) || 0;
                    let nilaiReklas = (this.reklasJenis === 'extracom' || this.reklasJenis === 'intracom') ? this.getReklasExtracomTotal() : (this.reklasJenis === 'koreksi_nilai' ? (parseFloat(this.reklasNominalKoreksi) || 0) : (this.reklasJenis === 'mutasi_eksternal' ? this.getReklasMutasiSelectedNilai() : (isPartial ? this.getReklasSelectedNilai() : rawNilai)));

                    const targetKode = (this.reklasSubSubRincianKode || this.reklasSubRincianKode || this.reklasTujuanKode || '').trim() || null;
                    const targetNama = (this.reklasSubSubRincianNama || this.reklasSubRincianNama || this.reklasTujuanNama || '').trim() || null;
                    let nomorBaFinal = (this.reklasNoDokumenKoreksi || this.reklasNomorBa || '').trim() || null;
                    if (this.reklasJenis === 'hibah') nomorBaFinal = (this.reklasNomorBastHibah || '').trim() || nomorBaFinal;
                    else if (this.reklasJenis === 'mutasi_eksternal') nomorBaFinal = (this.reklasNomorBastMutasi || '').trim() || nomorBaFinal;

                    const payload = {
                        astap_id: it.id,
                        jenis_reklas: jenisReklasDb,
                        is_partial_reklas: isPartial,
                        selected_indices: isPartial ? selectedIndices : null,
                        selected_items: isPartial ? selectedItems : null,
                        unselected_items: isPartial ? unselectedItems : null,
                        tipe_koreksi: this.reklasTipeKoreksiNilai,
                        sub_koreksi: this.reklasJenis === 'koreksi_nilai' ? this.reklasSubKoreksi : null,
                        asal_kib: asalKib,
                        tujuan_kib: targetKib,
                        tujuan_kode: targetKode,
                        tujuan_nama: targetNama,
                        kode_108: targetKode,
                        nilai_reklas: nilaiReklas,
                        tanggal_reklas: tgl,
                        triwulan: tw,
                        tahun: thn,
                        nomor_ba_reklas: nomorBaFinal,
                        alasan_reklas: (this.reklasAlasan || '').trim() || null,
                        pihak_hibah: this.reklasJenis === 'hibah' ? this.reklasPihakHibah : null,
                        skpd_tujuan: this.reklasJenis === 'mutasi_eksternal' ? this.reklasSkpdTujuan : null,
                        pejabat_opd_tujuan: this.reklasJenis === 'mutasi_eksternal' ? this.reklasPejabatOpdTujuan : null,
                        nip_pejabat_opd_tujuan: this.reklasJenis === 'mutasi_eksternal' ? this.reklasNipPejabatOpdTujuan : null,
                        jabatan_opd_tujuan: this.reklasJenis === 'mutasi_eksternal' ? this.reklasJabatanOpdTujuan : null,
                        pj_asal_nama: this.reklasJenis === 'mutasi_eksternal' ? this.reklasPjAsalNama : null,
                        pj_asal_nip: this.reklasJenis === 'mutasi_eksternal' ? this.reklasPjAsalNip : null,
                        pj_asal_jabatan: this.reklasJenis === 'mutasi_eksternal' ? this.reklasPjAsalJabatan : null,
                        alamat_instansi: this.reklasJenis === 'mutasi_eksternal' ? this.reklasAlamatInstansi : null,
                        nomor_sk_dasar: this.reklasJenis === 'mutasi_eksternal' ? this.reklasNomorSkDasar : null,
                        tanggal_bast: this.reklasJenis === 'hibah' ? this.reklasTanggalBastHibah : (this.reklasJenis === 'mutasi_eksternal' ? this.reklasTanggalBastMutasi : null),
                        tanggal_dokumen_koreksi: this.reklasJenis === 'koreksi_nilai' ? this.reklasDokumenTglKoreksi : null,
                        selected_register_ids: (this.reklasJenis === 'mutasi_eksternal' && this.selectedAstapReklas?.registers?.length > 0) ? this.reklasSelectedNibarIds : null,
                        reklas_items: (this.reklasJenis === 'extracom' || this.reklasJenis === 'intracom' || this.reklasJenis === 'koreksi_nilai') ? this.reklasExtracomItems : null,
                        keterangan: this.getReklasNarasiPreview(),
                        spesifikasi_baru: (this.reklasJenis === 'pindah_kib' || this.reklasJenis === 'kdp') 
                            ? Object.assign({}, this.reklasSpekBaru, {
                                progres_persen: (this.reklasJenis === 'kdp') ? 100 : (this.reklasSpekBaru.progres_persen || undefined),
                                kdp_progres_persen: (this.reklasJenis === 'kdp') ? 100 : undefined,
                                progres_fisik: (this.reklasJenis === 'kdp') ? '100%' : undefined,
                                items: isPartial
                                    ? selectedItems
                                    : ((this.reklasSpekBaruItems && this.reklasSpekBaruItems.length > 0) ? this.reklasSpekBaruItems : [this.reklasSpekBaru])
                            }) 
                            : null,
                    };

                    let bodyData, headers = { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' };
                    if (this.selectedDokumenFile) {
                        const fd = new FormData();
                        for (const key in payload) { if (payload[key] === null || payload[key] === undefined) continue; if (typeof payload[key] === 'object') fd.append(key, JSON.stringify(payload[key])); else fd.append(key, payload[key]); }
                        fd.append('dokumen_file', this.selectedDokumenFile);
                        bodyData = fd;
                    } else {
                        headers['Content-Type'] = 'application/json';
                        bodyData = JSON.stringify(payload);
                    }

                    const res = await fetch('{{ route("master.reklasifikasi.store") }}', { method: 'POST', headers, body: bodyData });
                    const resJson = await res.json();

                    if (!res.ok || !resJson.success) {
                        let errMsg = resJson.message || 'Gagal mencatat reklasifikasi.';
                        if (resJson.errors && typeof resJson.errors === 'object') {
                            const firstErr = Object.values(resJson.errors).flat()[0];
                            if (firstErr) errMsg = firstErr;
                        }
                        this.showToast(errMsg, 'error');
                        return;
                    }

                    this.showToast(resJson.message || 'Reklasifikasi aset hibah berhasil dicatat!', 'success');
                    this.showReklasModal = false;
                    setTimeout(() => {
                        const url = new URL(window.location.href);
                        window.location.href = url.pathname + (url.search ? url.search : '');
                    }, 1200);

                } catch (err) {
                    console.error('Reklas submit error (hibah):', err);
                    this.showToast('Terjadi kesalahan jaringan saat menyimpan reklasifikasi.', 'error');
                } finally {
                    this.isSubmittingReklas = false;
                }
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
