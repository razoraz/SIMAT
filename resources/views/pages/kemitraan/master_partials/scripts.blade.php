<!-- ========================================================================= -->
<!-- SCRIPTS: LOGIKA STATE MANAGEMENT MASTER KEMITRAAN ASET (AKUN 1.5.2)        -->
<!-- ========================================================================= -->
<script>
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);

    function masterKemitraan() {
        return {
            // Mode Tampilan Pemisah Tabel Kemitraan: 'both' | 'dimanfaatkan' | 'ditambahkan' | 'all'
            kemitraanTableTab: 'both',

            // State Modal Reklasifikasi Aset Tetap (RSDK) — Sama Seperti di Data ASTAP
            showReklasModal: false,
            selectedAstapReklas: null,
            reklasJenis: 'pindah_kib', // 'extracom' | 'intracom' | 'pindah_kib' | 'kdp' | 'koreksi_nilai'
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
            reklasTipeKoreksiNilai: 'kurang',
            reklasNominalKoreksi: 0,
            reklasNilaiRealisasiBaru: 0,
            reklasNilaiAnggaran: 0,
            reklasNoDokumenKoreksi: '',
            isSubmittingReklas: false,
            reklasKemitraanTipeFisik: 'mesin', // 'tanah' | 'mesin' | 'gedung' | 'jaringan' | 'lainnya'

            getReklasKemitraanPhysicalType() {
                if (this.reklasKemitraanTipeFisik) return this.reklasKemitraanTipeFisik;
                return this.detectKemitraanPhysicalType();
            },

            detectKemitraanPhysicalType(item) {
                const it = item || this.selectedAstapReklas;
                const kode = this.reklasSubSubRincianKode || this.reklasSubRincianKode || this.reklasTujuanKode || (it?.kode_barang || '');
                const nama = ((this.reklasSubSubRincianNama || '') + ' ' + (this.reklasSubRincianNama || '') + ' ' + (it?.nama_barang || '')).toLowerCase();

                if (kode) {
                    if (kode.endsWith('.001') || kode.endsWith('.01') || kode.includes('.01.01.001') || kode.startsWith('1.3.1')) return 'tanah';
                    if (kode.endsWith('.002') || kode.endsWith('.02') || kode.includes('.01.01.002') || kode.startsWith('1.3.2')) return 'mesin';
                    if (kode.endsWith('.003') || kode.endsWith('.03') || kode.includes('.01.01.003') || kode.startsWith('1.3.3')) return 'gedung';
                    if (kode.endsWith('.004') || kode.endsWith('.04') || kode.includes('.01.01.004') || kode.startsWith('1.3.4')) return 'jaringan';
                    if (kode.endsWith('.005') || kode.endsWith('.05') || kode.includes('.01.01.005') || kode.startsWith('1.3.5')) return 'lainnya';
                }

                if (nama.includes('tanah') || nama.includes('lahan') || nama.includes('kavling')) return 'tanah';
                if (nama.includes('gedung') || nama.includes('bangunan') || nama.includes('ruang') || nama.includes('paviliun') || nama.includes('rumah')) return 'gedung';
                if (nama.includes('jalan') || nama.includes('irigasi') || nama.includes('jaringan') || nama.includes('pipa') || nama.includes('saluran') || nama.includes('kabel')) return 'jaringan';
                if (nama.includes('lainnya') || nama.includes('buku') || nama.includes('seni') || nama.includes('hewan') || nama.includes('tanaman')) return 'lainnya';
                if (nama.includes('mesin') || nama.includes('alat') || nama.includes('kendaraan') || nama.includes('peralatan') || nama.includes('alkes')) return 'mesin';

                if (it) {
                    let spec = it.spesifikasi_json;
                    if (typeof spec === 'string') { try { spec = JSON.parse(spec); } catch(e){} }
                    if (spec && typeof spec === 'object') {
                        if (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) return 'tanah';
                        if (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) return 'gedung';
                        if (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) return 'jaringan';
                        if (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) return 'lainnya';
                        if (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) return 'mesin';
                        if (spec.luas_m2 || spec.hak_tanah || spec.sertifikat_no) return 'tanah';
                        if (spec.konstruksi_bertingkat || spec.luas_lantai_m2) return 'gedung';
                        if (spec.merk || spec.type || spec.no_pabrik) return 'mesin';
                    }
                }
                return 'mesin';
            },

            // State Autocomplete Mitra Rekanan Kemitraan (Sinkron dengan Form Kemitraan)
            masterMitraList: (window.dbMitraKemitraans && Array.isArray(window.dbMitraKemitraans)) ? [...window.dbMitraKemitraans] : [],
            isReklasMitraDropdownOpen: false,

            get filteredReklasMitraList() {
                const q = (this.reklasSpekBaru?.kemitraan_mitra || '').toLowerCase().trim();
                if (!q) return this.masterMitraList.slice(0, 15);
                return this.masterMitraList.filter(m => m && m.nama && m.nama.toLowerCase().includes(q));
            },

            onReklasMitraInput(val) {
                const q = (val !== undefined ? val : (this.reklasSpekBaru?.kemitraan_mitra || '')).trim().toLowerCase();
                if (!q) return;
                const match = this.masterMitraList.find(m => m && m.nama && m.nama.trim().toLowerCase() === q);
                if (match) {
                    if (match.pimpinan && !this.reklasSpekBaru.kemitraan_pimpinan) {
                        this.reklasSpekBaru.kemitraan_pimpinan = match.pimpinan;
                    }
                    if (match.alamat && !this.reklasSpekBaru.kemitraan_alamat) {
                        this.reklasSpekBaru.kemitraan_alamat = match.alamat;
                    }
                }
            },

            selectReklasMitra(mitra) {
                if (!this.reklasSpekBaru) this.reklasSpekBaru = {};
                if (typeof mitra === 'string') {
                    this.reklasSpekBaru.kemitraan_mitra = mitra;
                    const match = this.masterMitraList.find(m => m && m.nama && m.nama.trim().toLowerCase() === mitra.trim().toLowerCase());
                    if (match) {
                        if (match.pimpinan) this.reklasSpekBaru.kemitraan_pimpinan = match.pimpinan;
                        if (match.alamat) this.reklasSpekBaru.kemitraan_alamat = match.alamat;
                    }
                } else if (mitra && typeof mitra === 'object') {
                    this.reklasSpekBaru.kemitraan_mitra = mitra.nama || '';
                    if (mitra.pimpinan) this.reklasSpekBaru.kemitraan_pimpinan = mitra.pimpinan;
                    if (mitra.alamat) this.reklasSpekBaru.kemitraan_alamat = mitra.alamat;
                }
                this.isReklasMitraDropdownOpen = false;
            },

            reklasSpekBaruItems: [],
            activeSpekUnitTab: 0,

            reklasSpekBaru: {
                tanah_luas_m2: '',
                tanah_hak: 'Hak Pakai',
                tanah_sertifikat_no: '',
                tanah_sertifikat_tgl: '',
                tanah_penggunaan: '',
                tanah_asal_usul: 'Pengadaan APBD / BLUD',
                tanah_alamat: '',
                mesin_merk: '',
                mesin_type: '',
                mesin_no_pabrik: '',
                mesin_ukuran_cc: '',
                mesin_bahan: 'Logam / Komponen Elektronik',
                mesin_no_polisi: '',
                gedung_konstruksi_bertingkat: 'Bertingkat',
                gedung_konstruksi_beton: 'Beton',
                gedung_luas_lantai_m2: '',
                gedung_dokumen_nomor: '',
                gedung_dokumen_tgl: '',
                gedung_status_tanah: 'Tanah Pemda',
                gedung_alamat: '',
                jaringan_konstruksi: 'Aspal / Beton',
                jaringan_panjang_km: '',
                jaringan_lebar_m: '',
                jaringan_luas_m2: '',
                lainnya_judul_pencipta: '',
                lainnya_bahan: 'Kertas / Kanvas / Lainnya',
                atb_nama_software: '',
                atb_pengembang: '',
                atb_masa_manfaat: '4',
                atb_nomor_lisensi: '',
                kemitraan_skema: 'Sewa',
                kemitraan_mitra: '',
                kemitraan_pimpinan: '',
                kemitraan_alamat: '',
                kemitraan_perjanjian_no: '',
                kemitraan_tanggal_pks: new Date().toISOString().split('T')[0],
                kemitraan_tanggal_mulai: new Date().toISOString().split('T')[0],
                kemitraan_tanggal_selesai: '',
                kemitraan_jangka_waktu: '5 Tahun',
            },

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

                    // Informasi Mitra Rekanan
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
                    sisa_hari_konsesi: kemitraan?.sisa_hari_konsesi ?? null,
                    dokumen_path: kemitraan?.dokumen_path || spec.dokumen_path || null
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
            },

            // =========================================================================
            // METODE & HELPER REKLASIFIKASI ASET TETAP (SAMA SEPERTI DI DATA ASTAP)
            // =========================================================================
            isCurrentAstapExtracom() {
                if (!this.selectedAstapReklas) return false;
                const it = this.selectedAstapReklas;
                if (it.is_extracomtable === true || it.is_extracomtable === 1 || it.is_extracomtable === '1') return true;
                const cat = (it.category || '').toString().trim().toUpperCase();
                return cat === 'EXTRACOM';
            },

            isReklasExtracomDisabled() {
                if (!this.selectedAstapReklas) return false;
                if (this.isCurrentAstapExtracom()) return false;
                const it = this.selectedAstapReklas;
                const cat = it.category || '';
                const kode = it.kode_barang || '';
                if (kode.startsWith('1.3.1') || kode.startsWith('1.3.3') || kode.startsWith('1.3.4') || kode.startsWith('1.3.6') || kode.startsWith('1.5.3') || kode.startsWith('1.5.2')) {
                    return true;
                }
                return (cat === 'KIB A' || cat === 'KIB C' || cat === 'KIB D' || cat === 'KIB F' || cat === 'ATB' || cat === 'KEMITRAAN');
            },

            isCurrentAstapKdp() {
                if (!this.selectedAstapReklas) return false;
                const it = this.selectedAstapReklas;
                const cat = it.category || '';
                const kode = it.kode_barang || '';
                return (cat === 'KIB F' || kode.startsWith('1.3.6'));
            },

            isCurrentAstapKonstruksi() {
                return this.isCurrentAstapKdp();
            },

            isReklasKdpDisabled() {
                if (!this.selectedAstapReklas) return true;
                return !this.isCurrentAstapKdp();
            },

            getReklasNilaiBaru() {
                if (!this.selectedAstapReklas) return 0;
                if (this.reklasJenis === 'koreksi_nilai') {
                    return parseFloat(this.reklasNilaiRealisasiBaru) || this.getReklasExtracomTotal();
                }
                const baseVal = parseFloat(this.selectedAstapReklas.total_realisasi_num || this.selectedAstapReklas.harga_satuan || 0);
                const selisih = parseFloat(this.reklasNominalKoreksi) || 0;
                if (this.reklasTipeKoreksiNilai === 'kurang') {
                    return Math.max(0, baseVal - selisih);
                } else {
                    return baseVal + selisih;
                }
            },

            setReklasArahKoreksi(tipe) {
                this.reklasTipeKoreksiNilai = tipe;
                this.reklasNilaiRealisasiBaru = this.getReklasNilaiBaru();
            },

            onReklasSelisihChanged() {
                this.reklasNilaiRealisasiBaru = this.getReklasNilaiBaru();
            },

            onReklasNilaiBaruChanged() {
                if (!this.selectedAstapReklas) return;
                const baseVal = parseFloat(this.selectedAstapReklas.total_realisasi_num || this.selectedAstapReklas.harga_satuan || 0);
                const targetBaru = parseFloat(this.reklasNilaiRealisasiBaru) || 0;
                if (targetBaru < baseVal) {
                    this.reklasTipeKoreksiNilai = 'kurang';
                    this.reklasNominalKoreksi = Math.max(0, baseVal - targetBaru);
                } else {
                    this.reklasTipeKoreksiNilai = 'tambah';
                    this.reklasNominalKoreksi = Math.max(0, targetBaru - baseVal);
                }
            },

            formatRupiahInput(val) {
                if (val === undefined || val === null || val === '') return '';
                const num = Math.round(Number(val));
                if (isNaN(num)) return '';
                return num.toLocaleString('id-ID');
            },

            updateItemHargaSatuan(item, rawText) {
                const cleanDigits = String(rawText || '').replace(/[^0-9]/g, '');
                const num = cleanDigits ? parseInt(cleanDigits, 10) : 0;
                item.harga_satuan = num;
                this.onReklasItemPriceChanged();
            },

            onReklasItemPriceChanged() {
                if (!this.selectedAstapReklas) return;
                const baseVal = parseFloat(this.selectedAstapReklas.total_realisasi_num || this.selectedAstapReklas.harga_satuan || 0);
                const totalNew = this.getReklasExtracomTotal();
                this.reklasNilaiRealisasiBaru = totalNew;

                if (totalNew < baseVal) {
                    this.reklasTipeKoreksiNilai = 'kurang';
                    this.reklasNominalKoreksi = Math.max(0, baseVal - totalNew);
                } else {
                    this.reklasTipeKoreksiNilai = 'tambah';
                    this.reklasNominalKoreksi = Math.max(0, totalNew - baseVal);
                }
            },

            getRmbAsalLabel() {
                if (!this.selectedAstapReklas) return '-';
                return 'Akun 1.5.2 (Kemitraan Pihak Ketiga)';
            },

            getRmbTujuanLabel() {
                if (!this.selectedAstapReklas) return '-';
                const tujuanKib = this.reklasTujuanKib || 'KIB Tujuan';
                const tujuanKd = this.reklasTujuanKode ? ` (${this.reklasTujuanKode})` : '';
                return `${tujuanKib}${tujuanKd}`;
            },

            getReklasNoBuktiDisplay() {
                if (!this.selectedAstapReklas) return '-';
                const it = this.selectedAstapReklas;
                if (it.bast_dokumen_nomor) return 'PKS: ' + it.bast_dokumen_nomor;
                if (this.reklasNomorBa) return this.reklasNomorBa;
                return 'Dokumen Perjanjian Kemitraan';
            },

            get reklasTargetJenisKode() {
                const kibMap = {
                    'KIB A': '1.3.1',
                    'KIB B': '1.3.2',
                    'KIB C': '1.3.3',
                    'KIB D': '1.3.4',
                    'KIB E': '1.3.5',
                    'KIB F': '1.3.6',
                    'ATB': '1.5.3',
                    'ASET LAIN': '1.5.4',
                    'ASET LAIN-LAIN': '1.5.4',
                    'KEMITRAAN': '1.5.2',
                };
                return kibMap[this.reklasTujuanKib] || '';
            },

            get reklasTargetJenisAstap() {
                if (!window.dbMasterJenisAstap108 || !this.reklasTujuanKib) return null;
                const prefix = this.reklasTargetJenisKode;
                if (!prefix) return null;
                return window.dbMasterJenisAstap108.find(j => j.kode === prefix || (j.kode && j.kode.startsWith(prefix))) || null;
            },

            get availableReklasSubRincian108() {
                if (this.reklasTargetJenisAstap && this.reklasTargetJenisAstap.subRincian) {
                    return this.reklasTargetJenisAstap.subRincian;
                }
                let all = [];
                (window.dbMasterJenisAstap108 || []).forEach(j => {
                    if (j.subRincian) all = all.concat(j.subRincian);
                });
                return all;
            },

            get filteredReklasSubRincian108() {
                const list = this.availableReklasSubRincian108 || [];
                const q = (this.searchReklasSubRincian || '').toLowerCase().trim();
                if (!q) return list.slice(0, 30);
                return list.filter(s => 
                    (s.nama && s.nama.toLowerCase().includes(q)) || 
                    (s.kode && s.kode.toLowerCase().includes(q))
                ).slice(0, 30);
            },

            selectReklasSubRincian(s) {
                this.reklasSubRincianKode = s.kode;
                this.reklasSubRincianNama = s.nama;
                this.searchReklasSubRincian = '';
                this.isReklasSubRincianOpen = false;

                if (this.reklasSubSubRincianKode && !this.reklasSubSubRincianKode.startsWith(s.kode)) {
                    this.reklasSubSubRincianKode = '';
                    this.reklasSubSubRincianNama = '';
                    this.searchReklasSubSubRincian = '';
                }
                this.reklasTujuanKode = this.reklasSubSubRincianKode || s.kode;
                this.reklasTujuanNama = this.reklasSubSubRincianNama || s.nama;
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    this.reklasKemitraanTipeFisik = this.detectKemitraanPhysicalType();
                }
            },

            clearReklasSubRincian() {
                this.reklasSubRincianKode = '';
                this.reklasSubRincianNama = '';
                this.searchReklasSubRincian = '';
                this.isReklasSubRincianOpen = false;
                this.reklasTujuanKode = this.reklasSubSubRincianKode || '';
                this.reklasTujuanNama = this.reklasSubSubRincianNama || '';
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    this.reklasKemitraanTipeFisik = this.detectKemitraanPhysicalType();
                }
            },

            get currentReklasSubRincianObj() {
                if (!this.reklasSubRincianKode) return null;
                return (this.availableReklasSubRincian108 || []).find(s => s.kode === this.reklasSubRincianKode) || null;
            },

            get availableReklasSubSubRincian108() {
                if (this.currentReklasSubRincianObj && this.currentReklasSubRincianObj.subSubRincian) {
                    return this.currentReklasSubRincianObj.subSubRincian;
                }
                if (this.reklasTargetJenisAstap) {
                    if (!this.reklasTargetJenisAstap._flatSubSub) {
                        const flat = [];
                        (this.reklasTargetJenisAstap.subRincian || []).forEach(sr => {
                            if (sr.subSubRincian) {
                                sr.subSubRincian.forEach(ssr => flat.push(ssr));
                            }
                        });
                        this.reklasTargetJenisAstap._flatSubSub = flat;
                    }
                    return this.reklasTargetJenisAstap._flatSubSub;
                }
                let all = [];
                (window.dbMasterJenisAstap108 || []).forEach(j => {
                    (j.subRincian || []).forEach(sr => {
                        if (sr.subSubRincian) {
                            sr.subSubRincian.forEach(ssr => all.push(ssr));
                        }
                    });
                });
                return all;
            },

            get filteredReklasSubSubRincian108() {
                const list = this.availableReklasSubSubRincian108 || [];
                const q = (this.searchReklasSubSubRincian || '').toLowerCase().trim();
                if (!q) return list.slice(0, 30);
                return list.filter(item => 
                    (item.nama && item.nama.toLowerCase().includes(q)) || 
                    (item.kode && item.kode.toLowerCase().includes(q))
                ).slice(0, 30);
            },

            selectReklasSubSubRincian(item) {
                this.reklasSubSubRincianKode = item.kode;
                this.reklasSubSubRincianNama = item.nama;
                this.searchReklasSubSubRincian = '';
                this.isReklasSubSubRincianOpen = false;

                if (window.dbMasterJenisAstap108) {
                    for (const j of window.dbMasterJenisAstap108) {
                        for (const sr of (j.subRincian || [])) {
                            if (item.kode.startsWith(sr.kode)) {
                                this.reklasSubRincianKode = sr.kode;
                                this.reklasSubRincianNama = sr.nama;
                                if (!this.reklasTujuanKib) {
                                    const reverseKibMap = {
                                        '1.3.1': 'KIB A',
                                        '1.3.2': 'KIB B',
                                        '1.3.3': 'KIB C',
                                        '1.3.4': 'KIB D',
                                        '1.3.5': 'KIB E',
                                        '1.3.6': 'KIB F',
                                        '1.3.6.01': 'KIB F',
                                        '1.5.3': 'ATB',
                                        '1.5.4': 'ASET LAIN',
                                        '1.5.2': 'KEMITRAAN',
                                    };
                                    this.reklasTujuanKib = reverseKibMap[j.kode] || '';
                                }
                                break;
                            }
                        }
                    }
                }

                this.reklasTujuanKode = item.kode;
                this.reklasTujuanNama = item.nama;
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    this.reklasKemitraanTipeFisik = this.detectKemitraanPhysicalType();
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
                }
            },

            onReklasTujuanKibChange() {
                const prefix = this.reklasTargetJenisKode;
                if (prefix) {
                    if (this.reklasSubRincianKode && !this.reklasSubRincianKode.startsWith(prefix)) {
                        this.clearReklasSubRincian();
                        this.clearReklasSubSubRincian();
                    }
                    if (this.reklasSubSubRincianKode && !this.reklasSubSubRincianKode.startsWith(prefix)) {
                        this.clearReklasSubSubRincian();
                    }
                }
                this.reklasTujuanKode = this.reklasSubSubRincianKode || this.reklasSubRincianKode || '';
                this.reklasTujuanNama = this.reklasSubSubRincianNama || this.reklasSubRincianNama || '';
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    this.reklasKemitraanTipeFisik = this.detectKemitraanPhysicalType();
                }
                this.initReklasSpekBaru();
            },

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

                // Ambil repeater items dari helper detail atau langsung dari spec
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

                let existingItems = [];
                if (mItems.length > 0) existingItems = mItems;
                else if (lItems.length > 0) existingItems = lItems;
                else if (gItems.length > 0) existingItems = gItems;
                else if (jItems.length > 0) existingItems = jItems;
                else if (tItems.length > 0) existingItems = tItems;
                else if (aItems.length > 0) existingItems = aItems;

                const regs = Array.isArray(it.registers) ? it.registers : [];
                let totalUnits = existingItems.length > 1 ? existingItems.length : 1;

                const createDefaultSpekForUnit = (idx) => {
                    const reg = regs[idx] || null;
                    const currItem = existingItems[idx] || existingItems[0] || {};
                    const tItem = tItems[idx] || tItems[0] || currItem;
                    const mItem = mItems[idx] || mItems[0] || currItem;
                    const gItem = gItems[idx] || gItems[0] || currItem;
                    const jItem = jItems[idx] || jItems[0] || currItem;
                    const lItem = lItems[idx] || lItems[0] || currItem;
                    const aItem = aItems[idx] || aItems[0] || currItem;

                    const itemNama = currItem.mesin_nama_barang 
                        || currItem.lainnya_nama_barang 
                        || currItem.gedung_nama_bangunan 
                        || currItem.jaringan_nama 
                        || currItem.tanah_nama_barang 
                        || currItem.atb_nama_software 
                        || currItem.nama_barang 
                        || (this.reklasExtracomItems && this.reklasExtracomItems[idx] ? this.reklasExtracomItems[idx].nama_barang : null)
                        || it.nama_barang 
                        || `Barang #${idx + 1}`;

                    const itemVol = parseInt(currItem.mesin_jumlah_barang || currItem.lainnya_jumlah || currItem.gedung_jumlah_bangunan || currItem.jaringan_jumlah || currItem.tanah_jumlah_bidang || currItem.atb_jumlah_barang || currItem.jumlah_volume || (this.reklasExtracomItems && this.reklasExtracomItems[idx] ? this.reklasExtracomItems[idx].jumlah_volume : 1)) || 1;
                    const itemSatuan = currItem.mesin_satuan || currItem.lainnya_satuan || currItem.gedung_satuan || currItem.jaringan_satuan || currItem.tanah_satuan || currItem.atb_satuan || (this.reklasExtracomItems && this.reklasExtracomItems[idx] ? this.reklasExtracomItems[idx].satuan : null) || it.satuan || 'Unit';

                    return {
                        unit_index: idx,
                        item_nama: itemNama,
                        item_volume: itemVol,
                        item_satuan: itemSatuan,
                        nibar: reg?.nibar || reg?.no_register || `Unit #${idx + 1}`,
                        ruang_pemegang: reg?.ruang_pemegang || it.ruang_unit || '-',
                        kondisi: reg?.kondisi || 'Baik',

                        // KIB A - Tanah
                        tanah_luas_m2: tItem.tanah_luas_m2 || spec.luas_m2 || spec.tanah_luas_m2 || (it.luas_m2 && it.luas_m2 > 0 ? it.luas_m2 : '') || '',
                        tanah_hak: tItem.tanah_hak || tItem.hak_tanah || spec.hak_tanah || spec.tanah_hak || (it.hak_tanah && it.hak_tanah !== '-' ? it.hak_tanah : 'Hak Pakai'),
                        tanah_sertifikat_no: tItem.tanah_sertifikat_no || tItem.sertifikat_no || spec.sertifikat_no || spec.tanah_sertifikat_no || spec.sertifikat_nomor || (it.sertifikat_nomor && it.sertifikat_nomor !== '-' ? it.sertifikat_nomor : '') || '',
                        tanah_sertifikat_tgl: tItem.tanah_sertifikat_tgl || tItem.sertifikat_tgl || spec.sertifikat_tgl || spec.tanah_sertifikat_tgl || spec.sertifikat_tanggal || (it.sertifikat_tanggal && it.sertifikat_tanggal !== '-' ? it.sertifikat_tanggal : '') || '',
                        tanah_penggunaan: tItem.tanah_penggunaan || tItem.penggunaan || spec.penggunaan || spec.tanah_penggunaan || (it.penggunaan && it.penggunaan !== '-' ? it.penggunaan : '') || itemNama || 'Bangunan Rumah Sakit & Fasilitas',
                        tanah_asal_usul: tItem.tanah_asal_usul || spec.tanah_asal_usul || spec.asal_usul || (it.asal_usul && it.asal_usul !== '-' ? it.asal_usul : 'Pengadaan APBD / BLUD'),
                        tanah_alamat: tItem.tanah_alamat || spec.tanah_alamat || spec.alamat || it.alamat_barang || defaultAlamat,

                        // KIB B - Mesin
                        mesin_merk: mItem.mesin_merk || spec.merk || it.merk || (it.merk_type && it.merk_type !== '-' ? it.merk_type : '') || '',
                        mesin_type: mItem.mesin_type || spec.type || it.type || '',
                        mesin_no_pabrik: mItem.mesin_nomor_pabrik || mItem.mesin_no_pabrik || spec.no_pabrik || (it.no_pabrik && it.no_pabrik !== '-' ? it.no_pabrik : '') || '',
                        mesin_ukuran_cc: mItem.mesin_ukuran || spec.ukuran_cc || spec.ukuran || (it.ukuran && it.ukuran !== '-' ? it.ukuran : '') || '',
                        mesin_bahan: mItem.mesin_bahan || spec.bahan || (it.bahan && it.bahan !== '-' ? it.bahan : 'Logam / Komponen Elektronik'),
                        mesin_no_polisi: mItem.mesin_nomor_polisi || mItem.mesin_no_polisi || spec.no_polisi || (it.no_polisi && it.no_polisi !== '-' ? it.no_polisi : '') || '',

                        // KIB C - Gedung
                        gedung_konstruksi_bertingkat: gItem.gedung_bertingkat || spec.konstruksi_bertingkat || spec.bertingkat || (it.gedung_bertingkat && it.gedung_bertingkat !== '-' ? it.gedung_bertingkat : 'Bertingkat'),
                        gedung_konstruksi_beton: gItem.gedung_beton || spec.konstruksi_beton || spec.beton || (it.gedung_beton && it.gedung_beton !== '-' ? it.gedung_beton : 'Beton'),
                        gedung_luas_lantai_m2: gItem.gedung_luas_lantai_m2 || spec.luas_lantai_m2 || spec.luas_m2 || (it.luas_m2 && it.luas_m2 > 0 ? it.luas_m2 : '') || '',
                        gedung_dokumen_nomor: gItem.gedung_dokumen_nomor || spec.dokumen_nomor || it.spk_nomor || '',
                        gedung_dokumen_tgl: gItem.gedung_dokumen_tgl || spec.dokumen_tgl || it.spk_tanggal || '',
                        gedung_status_tanah: gItem.gedung_status_tanah || spec.status_tanah || (it.gedung_status_tanah && it.gedung_status_tanah !== '-' ? it.gedung_status_tanah : 'Tanah Pemda'),
                        gedung_alamat: gItem.gedung_alamat || spec.gedung_alamat || spec.alamat || it.alamat_barang || defaultAlamat,

                        // KIB D - Jaringan
                        jaringan_konstruksi: jItem.jaringan_konstruksi || spec.konstruksi || (it.jaringan_konstruksi && it.jaringan_konstruksi !== '-' ? it.jaringan_konstruksi : 'Aspal / Beton'),
                        jaringan_panjang_km: jItem.jaringan_panjang_m || spec.panjang_km || spec.panjang_m || (it.jaringan_panjang_m && it.jaringan_panjang_m > 0 ? it.jaringan_panjang_m : '') || '',
                        jaringan_lebar_m: jItem.jaringan_lebar_m || spec.lebar_m || (it.jaringan_lebar_m && it.jaringan_lebar_m > 0 ? it.jaringan_lebar_m : '') || '',
                        jaringan_luas_m2: jItem.jaringan_luas_m2 || spec.luas_m2 || (it.jaringan_luas_m2 && it.jaringan_luas_m2 > 0 ? it.jaringan_luas_m2 : '') || '',
                        jaringan_alamat: jItem.jaringan_alamat || spec.jaringan_alamat || spec.alamat || it.alamat_barang || defaultAlamat,

                        // KIB E - Lainnya
                        lainnya_judul_pencipta: lItem.lainnya_buku_judul || lItem.lainnya_judul_pencipta || spec.buku_judul || spec.judul || spec.judul_pencipta || itemNama || '',
                        lainnya_bahan: lItem.lainnya_bahan || spec.bahan || (it.bahan && it.bahan !== '-' ? it.bahan : 'Kertas / Kanvas / Lainnya'),

                        // ATB - Aset Tak Berwujud
                        atb_nama_software: aItem.atb_nama_software || spec.nama_software || itemNama || '',
                        atb_pengembang: aItem.atb_pengembang || spec.pengembang || '',
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

                        // ASET LAIN
                        aset_lain_kondisi: spec.kondisi_barang || spec.aset_lain_kondisi || (it.kondisi && it.kondisi !== '-' ? it.kondisi : 'Rusak Berat (Menunggu Penghapusan)'),
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
            },

            switchActiveSpekUnit(idx) {
                if (!this.reklasSpekBaruItems || idx < 0 || idx >= this.reklasSpekBaruItems.length) return;
                this.syncCurrentSpekToActiveItem();
                this.activeSpekUnitTab = idx;
                this.reklasSpekBaru = Object.assign({}, this.reklasSpekBaruItems[idx]);
            },

            syncCurrentSpekToActiveItem() {
                if (this.reklasSpekBaruItems && this.reklasSpekBaruItems[this.activeSpekUnitTab]) {
                    Object.assign(this.reklasSpekBaruItems[this.activeSpekUnitTab], this.reklasSpekBaru);
                }
            },

            copyActiveSpekToAll() {
                this.syncCurrentSpekToActiveItem();
                const current = Object.assign({}, this.reklasSpekBaru);
                this.reklasSpekBaruItems.forEach((item) => {
                    const keepProps = {
                        unit_index: item.unit_index,
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
                return item.item_nama || `Barang #${idx + 1}`;
            },

            getUnitTabSubtitle(uItem) {
                if (!uItem) return '';
                const vol = uItem.item_volume || 1;
                const sat = uItem.item_satuan || 'Unit';
                const r = uItem.ruang_pemegang && uItem.ruang_pemegang !== '-' ? uItem.ruang_pemegang : '';
                return r ? `${vol} ${sat} • ${r}` : `${vol} ${sat}`;
            },

            getReklasExtracomTotal() {
                if (!this.reklasExtracomItems || this.reklasExtracomItems.length === 0) {
                    return parseFloat(this.selectedAstapReklas?.total_realisasi_num || this.selectedAstapReklas?.harga_satuan || 0);
                }
                return this.reklasExtracomItems.reduce((acc, item) => {
                    const qty = parseInt(item.jumlah_volume) || 1;
                    const price = parseFloat(item.harga_satuan) || 0;
                    return acc + (qty * price);
                }, 0);
            },

            getReklasExtracomTotalVolume() {
                if (!this.reklasExtracomItems || this.reklasExtracomItems.length === 0) {
                    return parseInt(this.selectedAstapReklas?.jumlah_volume) || 1;
                }
                return this.reklasExtracomItems.reduce((acc, item) => {
                    return acc + (parseInt(item.jumlah_volume) || 1);
                }, 0);
            },

            addExtracomItem() {
                this.reklasExtracomItems.push({
                    nama_barang: '',
                    jumlah_volume: 1,
                    satuan: this.selectedAstapReklas?.satuan || 'Unit',
                    harga_satuan: 0,
                    kode_barang: this.selectedAstapReklas?.kode_barang || '',
                });
            },

            getReklasNarasiPreview() {
                const it = this.selectedAstapReklas;
                if (!it) return 'Pilih aset terlebih dahulu untuk melihat pratinjau narasi...';

                const subRek = it.kode_barang || '1.5.2';
                const subNama = it.nama_barang || '';
                const buktiStr = it.bast_dokumen_nomor ? `(PKS: ${it.bast_dokumen_nomor})` : '';
                const val = it.jumlah_realisasi || 'Rp 0';
                const vol = (it.jumlah_volume || 1) + ' ' + (it.satuan || 'Unit');
                const tujuanKib = this.reklasTujuanKib || 'KIB Definitif';
                const kodeTarget = this.reklasSubSubRincianKode || this.reklasSubRincianKode || this.reklasTujuanKode;
                const namaTarget = this.reklasSubSubRincianNama || this.reklasSubRincianNama || this.reklasTujuanNama;

                let tujuanStr = ` ke kelompok ${tujuanKib}`;
                if (kodeTarget) {
                    tujuanStr = ` ke rekening ${kodeTarget}` + (namaTarget ? ` (${namaTarget})` : '') + ` kelompok ${tujuanKib}`;
                }

                let narasi = `Reklasifikasi Aset Kemitraan Pihak Ketiga (Akun 1.5.2) rekening ${subRek} ${subNama} senilai ${val} ${buktiStr} pada RSUD Dr. H. Koesnandi${tujuanStr} berupa ${it.nama_barang || ''} (${vol}) karena masa konsesi kerja sama telah berakhir dan aset diserahkan menjadi Aset Tetap definitif RSUD.`;

                const alasanClean = (this.reklasAlasan || '').trim();
                if (alasanClean) {
                    narasi += ` Catatan/Alasan Reklas: ${alasanClean}.`;
                }
                return narasi;
            },

            openReklas(astapItem, kemitraanRow) {
                let item = Object.assign({}, astapItem || {});
                item.kemitraan = kemitraanRow || {};
                item.id = item.id || kemitraanRow?.astap_id;
                item.nama_barang = item.nama_barang || kemitraanRow?.keterangan || 'Aset Kemitraan';
                item.kode_barang = item.kode_108 || kemitraanRow?.kode_108 || (astapItem?.jenis_astap?.sub_sub_rincian_objek || '1.5.2');
                item.category = 'KEMITRAAN';
                item.jenis_aset_nama = 'Akun 1.5.2 - Kemitraan dengan Pihak Ketiga';
                item.total_realisasi_num = parseFloat(kemitraanRow?.nilai_aset || item.total_realisasi || 0);
                item.jumlah_realisasi = 'Rp ' + Number(item.total_realisasi_num).toLocaleString('id-ID');
                item.jumlah_volume = parseInt(kemitraanRow?.jumlah_volume || item.jumlah_volume) || 1;
                item.satuan = kemitraanRow?.satuan || item.satuan || 'Unit';
                item.bast_dokumen_nomor = kemitraanRow?.nomor_pks || item.bast_dokumen_nomor || '';
                item.spk_nomor = kemitraanRow?.nomor_pks || item.spk_nomor || '';
                item.tahun_perolehan = kemitraanRow?.tahun || item.tahun_perolehan || new Date().getFullYear();

                this.selectedAstapReklas = item;
                this.reklasJenis = 'pindah_kib';
                this.reklasTujuanKib = 'KEMITRAAN';
                this.reklasKemitraanTipeFisik = this.detectKemitraanPhysicalType(item);

                this.reklasTipeKoreksiNilai = 'kurang';
                this.reklasNominalKoreksi = 0;
                this.reklasNilaiRealisasiBaru = item.total_realisasi_num;
                this.reklasNilaiAnggaran = item.total_realisasi_num;
                this.reklasNoDokumenKoreksi = '';

                let spec = item.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                if (!spec || typeof spec !== 'object') spec = {};

                if (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                    this.reklasExtracomItems = spec.mesin_items.map((m, idx) => ({
                        nama_barang: m.mesin_nama_barang || item.nama_barang || ('Barang #' + (idx + 1)),
                        jumlah_volume: parseInt(m.mesin_jumlah_barang) || 1,
                        satuan: m.mesin_satuan || item.satuan || 'Unit',
                        harga_satuan: parseFloat(m.mesin_nilai_satuan || m.mesin_harga_satuan || item.harga_satuan || 0),
                        kode_barang: m.mesin_kode_barang || item.kode_barang || '',
                    }));
                } else {
                    const fallbackVol = parseInt(item.jumlah_volume) || 1;
                    const fallbackHarga = fallbackVol > 0 ? (item.total_realisasi_num / fallbackVol) : item.total_realisasi_num;
                    this.reklasExtracomItems = [{
                        nama_barang: item.nama_barang || 'Barang Aset Kemitraan',
                        jumlah_volume: fallbackVol,
                        satuan: item.satuan || 'Unit',
                        harga_satuan: fallbackHarga,
                        kode_barang: item.kode_barang || '',
                    }];
                }

                const docs = [];
                if (kemitraanRow?.nomor_pks) docs.push('PKS: ' + kemitraanRow.nomor_pks);
                if (item.bast_dokumen_nomor && item.bast_dokumen_nomor !== kemitraanRow?.nomor_pks) docs.push('BAST: ' + item.bast_dokumen_nomor);
                this.reklasNomorBa = docs.join(' | ');

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
                this.reklasTanggal = new Date().toLocaleDateString('en-CA');
                this.reklasAlasan = 'Masa konsesi kemitraan berakhir, dialihkan ke Aset Tetap definitif';

                this.initReklasSpekBaru();
                this.showReklasModal = true;
            },

            async submitReklas() {
                if (!this.selectedAstapReklas) return;

                this.syncCurrentSpekToActiveItem();
                this.isSubmittingReklas = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                try {
                    const it = this.selectedAstapReklas;
                    const tgl = this.reklasTanggal || new Date().toISOString().split('T')[0];
                    const dateObj = new Date(tgl);
                    const month = dateObj.getMonth() + 1;
                    const tw = Math.ceil(month / 3);
                    const thn = dateObj.getFullYear() || it.tahun_perolehan || new Date().getFullYear();

                    let jenisReklasDb = 'KOREKSI_REKENING';
                    if (this.reklasJenis === 'extracom') jenisReklasDb = 'EKSTRAKOMPTABEL';
                    else if (this.reklasJenis === 'intracom') jenisReklasDb = 'KAPITALISASI_INTRAKOM';
                    else if (this.reklasJenis === 'kdp') jenisReklasDb = 'KDP_TO_DEFINITIF';
                    else if (this.reklasJenis === 'pindah_kib') jenisReklasDb = 'KOREKSI_REKENING';
                    else if (this.reklasJenis === 'koreksi_nilai') jenisReklasDb = 'KOREKSI_LAIN';

                    let asalKib = 'KEMITRAAN';
                    let targetKib = this.reklasTujuanKib || 'KIB B';

                    let nilaiReklas = (this.reklasJenis === 'extracom' || this.reklasJenis === 'intracom') 
                        ? this.getReklasExtracomTotal()
                        : (this.reklasJenis === 'koreksi_nilai' ? (parseFloat(this.reklasNominalKoreksi) || 0) : parseFloat(it.total_realisasi_num || 0));

                    const targetKode = (this.reklasSubSubRincianKode || this.reklasSubRincianKode || this.reklasTujuanKode || '').trim() || null;
                    const targetNama = (this.reklasSubSubRincianNama || this.reklasSubRincianNama || this.reklasTujuanNama || '').trim() || null;

                    const payload = {
                        astap_id: it.id,
                        jenis_reklas: jenisReklasDb,
                        tipe_koreksi: this.reklasTipeKoreksiNilai,
                        asal_kib: asalKib,
                        tujuan_kib: targetKib,
                        tujuan_kode: targetKode,
                        tujuan_nama: targetNama,
                        kode_108: targetKode,
                        nilai_reklas: nilaiReklas,
                        tanggal_reklas: tgl,
                        triwulan: tw,
                        tahun: thn,
                        nomor_ba_reklas: (this.reklasNoDokumenKoreksi || this.reklasNomorBa || '').trim() || null,
                        alasan_reklas: (this.reklasAlasan || '').trim() || null,
                        keterangan: this.getReklasNarasiPreview(),
                        jumlah_anggaran: parseFloat(it.total_realisasi_num) || null,
                        reklas_items: this.reklasExtracomItems,
                        spesifikasi_baru: Object.assign({}, this.reklasSpekBaru, {
                            items: (this.reklasSpekBaruItems && this.reklasSpekBaruItems.length > 0) ? this.reklasSpekBaruItems : [this.reklasSpekBaru]
                        }),
                    };

                    const res = await fetch('{{ route("master.reklasifikasi.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify(payload),
                    });

                    const resJson = await res.json();

                    if (resJson.success) {
                        this.showToast('Reklasifikasi Aset Kemitraan berhasil disimpan!', 'success');
                        this.showReklasModal = false;
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } else {
                        this.showToast('Gagal memproses reklasifikasi: ' + (resJson.message || 'Terjadi kesalahan sistem'), 'error');
                    }
                } catch (err) {
                    console.error('Reklas submit error:', err);
                    this.showToast('Terjadi kendala saat menyimpan reklasifikasi: ' + err.message, 'error');
                } finally {
                    this.isSubmittingReklas = false;
                }
            }
        };
    }
</script>
