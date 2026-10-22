<!-- ========================================================================= -->
<!-- SCRIPTS: LOGIKA STATE MANAGEMENT MASTER KEMITRAAN ASET (AKUN 1.5.2)        -->
<!-- ========================================================================= -->
<style>
    .custom-scrollbar::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: rgba(15, 23, 42, 0.6);
        border-radius: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: #334155;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: #a855f7;
    }
</style>
<script>
    window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : []);

    function masterKemitraan() {
        return {
            // Mode Tampilan Pemisah Tabel Kemitraan: 'both' | 'dimanfaatkan' | 'ditambahkan' | 'all'
            kemitraanTableTab: 'both',

            // Filter Siklus Status Keberadaan Aset: 'all' | 'kemitraan' | 'dikembalikan' | 'milik_rsud'
            statusSiklusFilter: 'all',

            // State Filter Client-Side Instan Kemitraan (0ms Tanpa Reload persis seperti Data ASTAP)
            filterSearch: '{{ $search ?? '' }}',
            filterKib: '{{ $filterKib ?? 'all' }}',
            filterSkema: '{{ $filterSkema ?? 'all' }}',
            filterTahun: '{{ $filterTahun ?? 'all' }}',
            filterTriwulan: '{{ $filterTw ?? 'all' }}',
            filterStatus: '{{ $filterStatus ?? 'all' }}',

            dimanfaatkanMetaList: window.__dimanfaatkanMetaList || [],
            ditambahkanMetaList: window.__ditambahkanMetaList || [],
            allMetaList: window.__allMetaList || [],
            allDitambahkanDetailList: window.__allDitambahkanDetailList || [],
            allDimanfaatkanDetailList: window.__allDimanfaatkanDetailList || [],

            // State Extend Konsesi (Adendum PKS)
            showModalExtend: false,
            isSubmittingExtend: false,
            extendData: {},
            extendForm: {
                id: null,
                nomor_adendum: '',
                tanggal_adendum: '',
                tanggal_selesai_baru: '',
                nilai_aset_baru: '',
                keterangan_adendum: '',
                berkas_adendum: null,
            },

            // State Confirm Extend Mitra (Tabel 2: Aset Ditambahkan Mitra)
            showModalConfirmExtendMitra: false,
            isSubmittingExtendMitra: false,
            extendMitraState: {
                nomor_adendum: '',
                tanggal_selesai_baru: '',
                tanggal_selesai_fmt: '',
                linked_mitra_assets: []
            },
            selectedMitraIds: [],
            extendMitraKeterangan: '',

            initMetaLists(dim, dit, all, allDitDetails, allDimDetails) {
                if (dim && Array.isArray(dim)) this.dimanfaatkanMetaList = dim;
                if (dit && Array.isArray(dit)) this.ditambahkanMetaList = dit;
                if (all && Array.isArray(all)) this.allMetaList = all;
                if (allDitDetails && Array.isArray(allDitDetails)) this.allDitambahkanDetailList = allDitDetails;
                if (allDimDetails && Array.isArray(allDimDetails)) this.allDimanfaatkanDetailList = allDimDetails;
            },

            get countVisibleDimanfaatkan() {
                return (this.dimanfaatkanMetaList || []).filter(row => this.matchKemitraan(row)).length;
            },

            get countVisibleDitambahkan() {
                return (this.ditambahkanMetaList || []).filter(row => this.matchKemitraan(row)).length;
            },

            get countVisibleAll() {
                return (this.allMetaList || []).filter(row => this.matchKemitraan(row)).length;
            },

            // Counter untuk Filter Siklus Keberadaan Aset (Khusus Aset Ditambahkan Mitra / Tabel 2)
            get countSiklusKemitraanDitambahkan() {
                return (this.ditambahkanMetaList || []).filter(row => (row.siklus || 'kemitraan') === 'kemitraan').length;
            },

            get countSiklusDikembalikanDitambahkan() {
                return (this.ditambahkanMetaList || []).filter(row => row.siklus === 'dikembalikan').length;
            },

            get countSiklusMilikRsudDitambahkan() {
                return (this.ditambahkanMetaList || []).filter(row => row.siklus === 'milik_rsud').length;
            },

            resetAllFilters() {
                this.statusSiklusFilter = 'all';
                this.filterSearch = '';
                this.filterKib = 'all';
                this.filterSkema = 'all';
                this.filterTahun = 'all';
                this.filterTriwulan = 'all';
                this.filterStatus = 'all';
            },

            matchKemitraan(row) {
                if (!row) return false;

                // 0. Filter Siklus Keberadaan Aset (HANYA BERLAKU UNTUK ASET DITAMBAHKAN MITRA / TABEL 2)
                if (!row.is_dimanfaatkan && this.statusSiklusFilter && this.statusSiklusFilter !== 'all') {
                    const rowSiklus = row.siklus || 'kemitraan';
                    if (rowSiklus !== this.statusSiklusFilter) {
                        return false;
                    }
                }

                // 1. Live Text Search (real-time 0ms)
                if (this.filterSearch && this.filterSearch.trim() !== '') {
                    const q = this.filterSearch.toLowerCase().trim();
                    if (!row.search_text || !row.search_text.includes(q)) {
                        return false;
                    }
                }

                // 2. Klasifikasi KIB (seperti Data ASTAP: milih KIB Tanah langsung disuguhkan!)
                if (this.filterKib && this.filterKib !== 'all') {
                    const rowCat = (row.category || '').toUpperCase().trim();
                    const targetCat = this.filterKib.toUpperCase().trim();
                    if (targetCat === 'KIB A' || targetCat.includes('TANAH')) {
                        if (!rowCat.includes('KIB A') && !rowCat.includes('TANAH')) return false;
                    } else if (targetCat === 'KIB B' || targetCat.includes('MESIN') || targetCat.includes('ALAT')) {
                        if (!rowCat.includes('KIB B') && !rowCat.includes('MESIN') && !rowCat.includes('ALAT')) return false;
                    } else if (targetCat === 'KIB C' || targetCat.includes('GEDUNG') || targetCat.includes('BANGUNAN')) {
                        if (!rowCat.includes('KIB C') && !rowCat.includes('GEDUNG') && !rowCat.includes('BANGUNAN')) return false;
                    } else if (targetCat === 'KIB D' || targetCat.includes('JARINGAN') || targetCat.includes('JALAN')) {
                        if (!rowCat.includes('KIB D') && !rowCat.includes('JARINGAN') && !rowCat.includes('JALAN')) return false;
                    } else if (targetCat === 'KIB E' || targetCat.includes('LAIN')) {
                        if (!rowCat.includes('KIB E') && !rowCat.includes('LAIN')) return false;
                    } else if (rowCat !== targetCat) {
                        return false;
                    }
                }

                // 3. Skema Kemitraan
                if (this.filterSkema && this.filterSkema !== 'all') {
                    const rowSkema = (row.skema || '').toUpperCase().trim();
                    const targetSkema = this.filterSkema.toUpperCase().trim();
                    if (targetSkema === 'KSP' || targetSkema === 'KSO') {
                        if (!rowSkema.includes('KSP') && !rowSkema.includes('KSO')) return false;
                    } else if (targetSkema === 'BGS' || targetSkema === 'BSG' || targetSkema.includes('BGS/BSG')) {
                        if (!rowSkema.includes('BGS') && !rowSkema.includes('BSG')) return false;
                    } else if (targetSkema === 'SEWA') {
                        if (!rowSkema.includes('SEWA')) return false;
                    } else if (targetSkema === 'KSPI') {
                        if (!rowSkema.includes('KSPI')) return false;
                    } else if (rowSkema !== targetSkema) {
                        return false;
                    }
                }

                // 4. Tahun Perolehan
                if (this.filterTahun && this.filterTahun !== 'all') {
                    if (String(row.tahun) !== String(this.filterTahun)) {
                        return false;
                    }
                }

                // 5. Periode Triwulan
                if (this.filterTriwulan && this.filterTriwulan !== 'all') {
                    const rowTw = String(row.triwulan || '').replace(/[\s_]/g, '').toUpperCase();
                    const targetTw = String(this.filterTriwulan).replace(/[\s_]/g, '').toUpperCase();
                    const isMatch = (rowTw === targetTw) ||
                        (targetTw === 'TWI' && (rowTw === 'TW1' || rowTw === '1')) ||
                        (targetTw === 'TWII' && (rowTw === 'TW2' || rowTw === '2')) ||
                        (targetTw === 'TWIII' && (rowTw === 'TW3' || rowTw === '3')) ||
                        (targetTw === 'TWIV' && (rowTw === 'TW4' || rowTw === '4'));
                    if (!isMatch) return false;
                }

                // 6. Status Konsesi
                if (this.filterStatus && this.filterStatus !== 'all') {
                    const rowStatus = (row.status || '').toLowerCase().trim();
                    const targetStatus = this.filterStatus.toLowerCase().trim();
                    if (targetStatus === 'akan_berakhir_7') {
                        if (row.sisa_hari === null || row.sisa_hari === undefined || row.sisa_hari < 0 || row.sisa_hari > 7) return false;
                        if (rowStatus.includes('berakhir') || rowStatus.includes('selesai') || rowStatus.includes('kembali') || rowStatus.includes('dihentikan')) return false;
                    } else if (targetStatus === 'akan_berakhir_30') {
                        if (row.sisa_hari === null || row.sisa_hari === undefined || row.sisa_hari < 0 || row.sisa_hari > 30) return false;
                        if (rowStatus.includes('berakhir') || rowStatus.includes('selesai') || rowStatus.includes('kembali') || rowStatus.includes('dihentikan')) return false;
                    } else if (targetStatus === 'aktif') {
                        if (!rowStatus.includes('aktif') || rowStatus.includes('berakhir')) return false;
                    } else if (targetStatus.includes('berakhir')) {
                        if (!rowStatus.includes('berakhir')) return false;
                    } else if (targetStatus.includes('kembali')) {
                        if (!rowStatus.includes('kembali')) return false;
                    } else if (targetStatus.includes('reklas') || targetStatus.includes('rsud')) {
                        if (!rowStatus.includes('reklas') && !rowStatus.includes('rsud') && row.siklus !== 'milik_rsud') return false;
                    } else if (targetStatus.includes('selesai')) {
                        if (!rowStatus.includes('selesai')) return false;
                    } else if (targetStatus.includes('dihentikan')) {
                        if (!rowStatus.includes('dihentikan')) return false;
                    } else if (targetStatus.includes('batal')) {
                        if (!rowStatus.includes('batal')) return false;
                    } else if (rowStatus !== targetStatus) {
                        return false;
                    }
                }

                return true;
            },

            // Generator QR Code Offline (SVG Data URI) untuk TTD Elektronik BSrE Pengurus Barang (Pak Budi)
            getQrCodeSvg(text) {
                if (typeof window.getQrCodeSvg === 'function') {
                    return window.getQrCodeSvg(text);
                }
                if (window.QRCode && typeof window.QRCode.toString === 'function') {
                    let svgStr = '';
                    window.QRCode.toString(String(text || ''), { type: 'svg', margin: 1 }, (err, svg) => {
                        if (!err && svg) svgStr = svg;
                    });
                    if (svgStr) {
                        return 'data:image/svg+xml;utf8,' + encodeURIComponent(svgStr);
                    }
                }
                return '';
            },

            // State Modal Cetak BAST Kemitraan (Modal Standar SIMAT)
            showModalPrintBast: false,
            showEditBastForm: false,
            isBastModified: false,
            currentBastStorageKey: '',
            currentBastTargetId: null,
            currentBastItem: null,
            currentBastIsDim: true,
            defaultBastDoc: null,
            bastDoc: null,
            toast: {
                show: false,
                message: '',
                type: 'success'
            },

            showToast(msg, type = 'success') {
                this.toast = {
                    show: true,
                    message: msg,
                    type: type
                };
                setTimeout(() => { this.toast.show = false; }, 3500);
            },

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
            reklasDokumenTglKoreksi: new Date().toLocaleDateString('en-CA'),
            reklasSubKoreksi: 'biasa',
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
            reklasKdpProgresAwal: 0,
            isSubmittingReklas: false,
            reklasKemitraanTipeFisik: 'mesin', // 'tanah' | 'mesin' | 'gedung' | 'jaringan' | 'lainnya'

            getReklasKemitraanPhysicalType() {
                if (this.reklasKemitraanTipeFisik) return this.reklasKemitraanTipeFisik;
                return this.detectKemitraanPhysicalType();
            },

            detectKemitraanPhysicalType(item) {
                const it = item || this.selectedAstapReklas;
                const kem = it?.kemitraan || {};

                // 1. PRIORITAS TERTINGGI: Periksa spesifikasi_json (kategori_kib & repeater items)
                let spec = it?.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                spec = (spec && typeof spec === 'object') ? spec : {};

                const kibSpec = String(spec.kategori_kib || '').toUpperCase();
                if (kibSpec.includes('KIB C') || kibSpec.includes('GEDUNG') || kibSpec.includes('BANGUNAN')) return 'gedung';
                if (kibSpec.includes('KIB B') || kibSpec.includes('MESIN') || kibSpec.includes('PERALATAN')) return 'mesin';
                if (kibSpec.includes('KIB A') || kibSpec.includes('TANAH')) return 'tanah';
                if (kibSpec.includes('KIB D') || kibSpec.includes('JARINGAN') || kibSpec.includes('JALAN') || kibSpec.includes('IRIGASI')) return 'jaringan';
                if (kibSpec.includes('KIB E') || kibSpec.includes('LAINNYA') || kibSpec.includes('BUKU')) return 'lainnya';

                if (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0 && spec.gedung_items.some(g => g.gedung_nama_barang || g.gedung_luas_m2 || g.gedung_luas_lantai || g.gedung_bertingkat || (parseFloat(g.gedung_nilai_satuan) > 0))) return 'gedung';
                if (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0 && spec.mesin_items.some(m => m.mesin_nama_barang || m.mesin_merk || m.mesin_type || m.mesin_no_pabrik || (parseFloat(m.mesin_nilai_satuan) > 0))) return 'mesin';
                if (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0 && spec.tanah_items.some(t => t.tanah_nama_barang || t.tanah_luas_m2 || t.tanah_hak || t.tanah_sertifikat_no || (parseFloat(t.tanah_nilai_satuan) > 0))) return 'tanah';
                if (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0 && spec.jaringan_items.some(j => j.jaringan_nama_barang || j.jaringan_konstruksi || (parseFloat(j.jaringan_nilai_satuan) > 0))) return 'jaringan';
                if (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0 && spec.lainnya_items.some(l => l.lainnya_nama_barang || l.lainnya_judul || (parseFloat(l.lainnya_nilai_satuan) > 0))) return 'lainnya';

                // 2. PRIORITAS KEDUA: Sub-Sub 108 / Sub-Rincian yang aktif dipilih di modal
                const subSubKd = this.reklasSubSubRincianKode || '';
                const subSubNm = (this.reklasSubSubRincianNama || '').toLowerCase();
                if (subSubKd) {
                    if (subSubKd.endsWith('.001') || subSubKd.endsWith('.01') || subSubKd.includes('.01.01.001') || subSubKd.startsWith('1.3.1')) return 'tanah';
                    if (subSubKd.endsWith('.003') || subSubKd.endsWith('.03') || subSubKd.includes('.01.01.003') || subSubKd.startsWith('1.3.3')) return 'gedung';
                    if (subSubKd.endsWith('.004') || subSubKd.endsWith('.04') || subSubKd.includes('.01.01.004') || subSubKd.startsWith('1.3.4')) return 'jaringan';
                    if (subSubKd.endsWith('.005') || subSubKd.endsWith('.05') || subSubKd.includes('.01.01.005') || subSubKd.startsWith('1.3.5')) return 'lainnya';
                    if (subSubKd.endsWith('.002') || subSubKd.endsWith('.02') || subSubKd.includes('.01.01.002') || subSubKd.startsWith('1.3.2')) return 'mesin';
                }
                if (subSubNm) {
                    if (subSubNm.includes('tanah') || subSubNm.includes('lahan')) return 'tanah';
                    if (subSubNm.includes('gedung') || subSubNm.includes('bangunan')) return 'gedung';
                    if (subSubNm.includes('jalan') || subSubNm.includes('jaringan') || subSubNm.includes('irigasi') || subSubNm.includes('pipa')) return 'jaringan';
                    if (subSubNm.includes('lainnya') || subSubNm.includes('buku') || subSubNm.includes('seni')) return 'lainnya';
                    if (subSubNm.includes('peralatan') || subSubNm.includes('mesin') || subSubNm.includes('alat') || subSubNm.includes('kendaraan') || subSubNm.includes('alkes')) return 'mesin';
                }

                // 3. PRIORITAS KETIGA: Kode 108 Aset Asal / Sub-Sub Rincian Akun Kemitraan (1.5.2)
                const asalKode = String(it?.kode_barang || it?.kode_108 || kem?.kode_108 || it?.jenis_aset_kode || it?.jenis_astap?.sub_sub_rincian_objek || '').trim();
                if (asalKode) {
                    if (asalKode.endsWith('.001') || asalKode.includes('.01.01.01.001') || asalKode.includes('.01.01.001') || asalKode.startsWith('1.3.1')) return 'tanah';
                    if (asalKode.endsWith('.003') || asalKode.includes('.01.01.01.003') || asalKode.includes('.01.01.003') || asalKode.startsWith('1.3.3')) return 'gedung';
                    if (asalKode.endsWith('.004') || asalKode.includes('.01.01.01.004') || asalKode.includes('.01.01.004') || asalKode.startsWith('1.3.4')) return 'jaringan';
                    if (asalKode.endsWith('.005') || asalKode.includes('.01.01.01.005') || asalKode.includes('.01.01.005') || asalKode.startsWith('1.3.5')) return 'lainnya';
                    if (asalKode.endsWith('.002') || asalKode.includes('.01.01.01.002') || asalKode.includes('.01.01.002') || asalKode.startsWith('1.3.2')) return 'mesin';
                }

                // 4. PRIORITAS KEEMPAT: Uraian Sub-Sub Rincian 108 Aset Asal
                const uraianSub = String(it?.jenis_astap?.uraian_sub_sub_rincian || it?.jenis_astap?.uraian_sub_rincian || kem?.jenis_astap?.uraian_sub_sub_rincian || '').toLowerCase();
                if (uraianSub.includes('tanah')) return 'tanah';
                if (uraianSub.includes('gedung') || uraianSub.includes('bangunan')) return 'gedung';
                if (uraianSub.includes('jalan') || uraianSub.includes('jaringan') || uraianSub.includes('irigasi')) return 'jaringan';
                if (uraianSub.includes('lainnya') || uraianSub.includes('buku') || uraianSub.includes('seni')) return 'lainnya';
                if (uraianSub.includes('mesin') || uraianSub.includes('peralatan') || uraianSub.includes('alat')) return 'mesin';

                // 5. PRIORITAS KELIMA: Satuan Barang
                const satuan = String(it?.satuan || kem?.satuan || '').toLowerCase().trim();
                if (satuan.includes('gedung') || satuan.includes('bangunan') || satuan.includes('ruang') || satuan.includes('lantai')) return 'gedung';
                if (satuan.includes('bidang') || satuan.includes('persil') || satuan.includes('hektar') || satuan.includes('are')) return 'tanah';
                if (satuan.includes('km') || satuan.includes('meter') || satuan.includes('ruas')) return 'jaringan';

                // 6. PRIORITAS KEENAM: Nama Barang / Keterangan
                const asalNama = String(it?.nama_barang || kem?.keterangan || '').toLowerCase();
                if (asalNama.includes('tanah') || asalNama.includes('lahan') || asalNama.includes('kavling')) return 'tanah';
                if (asalNama.includes('gedung') || asalNama.includes('bangunan') || asalNama.includes('rumah potong') || asalNama.includes('ruang') || asalNama.includes('paviliun') || asalNama.includes('rumah') || asalNama.includes('gudang') || asalNama.includes('instalasi') || asalNama.includes('kantor') || asalNama.includes('aula') || asalNama.includes('rph')) return 'gedung';
                if (asalNama.includes('jalan') || asalNama.includes('irigasi') || asalNama.includes('jaringan') || asalNama.includes('pipa') || asalNama.includes('saluran') || asalNama.includes('kabel') || asalNama.includes('drainase') || asalNama.includes('aspal')) return 'jaringan';
                if (asalNama.includes('buku') || asalNama.includes('seni') || asalNama.includes('hewan') || asalNama.includes('tanaman') || asalNama.includes('software') || asalNama.includes('aplikasi')) return 'lainnya';
                if (asalNama.includes('mesin') || asalNama.includes('alat') || asalNama.includes('kendaraan') || asalNama.includes('peralatan') || asalNama.includes('alkes') || asalNama.includes('analyzer') || asalNama.includes('centrifuge') || asalNama.includes('ventilator') || asalNama.includes('komputer') || asalNama.includes('printer') || asalNama.includes('server') || asalNama.includes('genset') || asalNama.includes('pompa')) return 'mesin';

                // 7. PRIORITAS KETUJUH: Atribut spesifikasi individu
                if (spec.konstruksi_bertingkat || spec.luas_lantai_m2 || spec.luas_m2_gedung || spec.bertingkat || spec.beton) return 'gedung';
                if (spec.merk || spec.type || spec.no_pabrik || spec.no_rangka || spec.no_mesin || spec.ukuran_cc) return 'mesin';
                if (spec.luas_m2 || spec.hak_tanah || spec.sertifikat_no || spec.tanah_hak) return 'tanah';
                if (spec.panjang_km || spec.lebar_m) return 'jaringan';

                // 8. Asal KIB jika ada
                const asalKib = String(it?.asal_kib || kem?.asal_kib || '').toUpperCase().trim();
                if (asalKib === 'KIB C') return 'gedung';
                if (asalKib === 'KIB B') return 'mesin';
                if (asalKib === 'KIB A') return 'tanah';
                if (asalKib === 'KIB D') return 'jaringan';
                if (asalKib === 'KIB E') return 'lainnya';

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
            dokumenPathCache: {},

            // QR Code Modal States
            showQrModal: false,
            selectedQrItem: null,
            isGeneratingQr: false,
            qrDataUrl: '',

            // Status Update Form State
            statusForm: {
                id: null,
                status_konsesi: 'Aktif'
            },
            isUpdatingStatus: false,

            // State Pembatalan Kemitraan (Batal Status Kemitraan)
            showModalBatal: false,
            isSubmittingBatal: false,
            batalData: {},
            batalForm: {
                alasan_batal: ''
            },

            // State Terminasi Kemitraan (Pengembalian ke Mitra Rekanan)
            showModalTerminasi: false,
            terminasiTab: 'kembalikan',
            isSubmittingTerminasi: false,
            isDraggingTerminasi: false,
            terminasiData: {},
            terminasiForm: {
                id: null,
                nomor_ba_terminasi: '',
                tanggal_terminasi: '',
                tanggal_ba_terminasi: '',
                kondisi_akhir: 'Baik',
                pihak_penyerah_nama: '',
                pihak_penyerah_jabatan: '',
                pihak_penerima_nama: '',
                pihak_penerima_jabatan: '',
                catatan_terminasi: '',
                berkas_ba_terminasi: null
            },

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
                (this.allMetaList || []).forEach(row => {
                    const yr = parseInt(row.tahun);
                    if (!isNaN(yr) && yr > 1900) yearsSet.add(yr);
                });
                const rawList = window.__simatAstaps || [];
                rawList.forEach(item => {
                    const yr = parseInt(item.tahun_perolehan || (item.kemitraan && item.kemitraan.tahun));
                    if (!isNaN(yr) && yr > 1900) yearsSet.add(yr);
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

            formatDateIndo(dateString) {
                if (!dateString || dateString === '-') return '-';
                try {
                    const d = new Date(dateString);
                    if (isNaN(d.getTime())) return String(dateString);
                    const bList = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
                    return `${d.getDate()} ${bList[d.getMonth()]} ${d.getFullYear()}`;
                } catch (e) {
                    return String(dateString);
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
            openDetail(kemitraan, astap, register, isDimanfaatkan = false) {
                let spec = astap?.spesifikasi_json || {};
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }

                // Warisi spesifikasi fisik dari objek asal HANYA jika aset ini sendiri memang berwujud Tanah (KIB A)
                // dan belum memiliki rincian tanah. JANGAN PERNAH menyuntikkan tanah_items ke Gedung, Mesin, atau Jaringan!
                const isAssetTanah = (astap?.category === 'KIB A') || 
                    String(astap?.nama_barang || '').toLowerCase().includes('tanah') ||
                    String(astap?.kode_barang || astap?.kode_108 || '').startsWith('1.3.1') ||
                    String(astap?.kode_barang || astap?.kode_108 || '').startsWith('1.5.2.01.01.01.001');

                const hasOtherPhysicalSpecs = (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) ||
                    (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) ||
                    (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) ||
                    (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0);

                if (!hasOtherPhysicalSpecs && isAssetTanah && kemitraan && kemitraan.objek_astap) {
                    let parentSpec = kemitraan.objek_astap.spesifikasi_json || {};
                    if (typeof parentSpec === 'string') {
                        try { parentSpec = JSON.parse(parentSpec); } catch(e) { parentSpec = {}; }
                    }
                    if (parentSpec && typeof parentSpec === 'object') {
                        if ((!spec.tanah_items || !spec.tanah_items.length) && Array.isArray(parentSpec.tanah_items) && parentSpec.tanah_items.length > 0) {
                            spec.tanah_items = parentSpec.tanah_items;
                        }
                        if (!spec.luas_m2 && parentSpec.luas_m2) spec.luas_m2 = parentSpec.luas_m2;
                        if (!spec.hak_tanah && parentSpec.hak_tanah) spec.hak_tanah = parentSpec.hak_tanah;
                        if (!spec.sertifikat_no && parentSpec.sertifikat_no) spec.sertifikat_no = parentSpec.sertifikat_no;
                        if (!spec.sertifikat_tgl && parentSpec.sertifikat_tgl) spec.sertifikat_tgl = parentSpec.sertifikat_tgl;
                        if (!spec.penggunaan && parentSpec.penggunaan) spec.penggunaan = parentSpec.penggunaan;
                    }
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

                // Resolusi objek aset & sumber perolehan awal pra-kemitraan
                const targetAstap = astap || kemitraan?.astap || kemitraan?.objek_astap || kemitraan?.objekAstap || null;
                const objekAstap = kemitraan?.objek_astap || kemitraan?.objekAstap || null;

                const bm = targetAstap?.belanja_modal || targetAstap?.belanjaModal || objekAstap?.belanja_modal || objekAstap?.belanjaModal || null;
                const bb = targetAstap?.belanja_barang || targetAstap?.belanjaBarang || null;
                const hibahList = targetAstap?.hibahs || (targetAstap?.hibah ? [targetAstap.hibah] : []);
                const mutasiList = targetAstap?.mutasi_eksternals || targetAstap?.mutasiEksternals || [];

                // Cari riwayat reklasifikasi (baik dari astap maupun objekAstap)
                const reklasArray = Array.isArray(targetAstap?.reklas) ? targetAstap.reklas : (Array.isArray(targetAstap?.reklases) ? targetAstap.reklases : (Array.isArray(objekAstap?.reklas) ? objekAstap.reklas : []));
                const reklasOrigin = reklasArray.find(r => r.tujuan_kib === 'KEMITRAAN' || String(r.tujuan_kode || '').startsWith('1.5.2')) || (reklasArray.length > 0 ? reklasArray[reklasArray.length - 1] : null);

                // Tentukan klasifikasi sumber asal pra-kemitraan
                let asalSumberDana = 'belanja_modal';
                let asalSumberDanaLabel = '🏛️ Belanja Modal (APBD / BLUD RSUD)';
                let asalSumberDanaIcon = '🏛️';
                let asalSumberDanaBadge = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40';

                const isHibahOrigin = (hibahList && hibahList.length > 0) || targetAstap?.sumber_dana === 'hibah' || targetAstap?.hibah_pemberi || spec?.hibah_info || spec?.pemberi;
                const isMutasiOrigin = (mutasiList && mutasiList.length > 0) || ['mutasi_eksternal', 'pelimpahan_skpd', 'mutasi_masuk', 'pelimpahan', 'mutasi'].includes(targetAstap?.sumber_dana) || spec?.mutasi_info;
                const isBarangOrigin = bb || targetAstap?.sumber_dana === 'belanja_barang' || targetAstap?.sumber_dana === 'belanja_rekening';

                if (isHibahOrigin) {
                    asalSumberDana = 'hibah';
                    asalSumberDanaLabel = '🎁 Hibah Masuk (Pemerintah / Swasta)';
                    asalSumberDanaIcon = '🎁';
                    asalSumberDanaBadge = 'bg-amber-500/20 text-amber-300 border-amber-500/40';
                } else if (isMutasiOrigin) {
                    asalSumberDana = 'mutasi_eksternal';
                    asalSumberDanaLabel = '🔄 Mutasi Masuk / Pelimpahan SKPD';
                    asalSumberDanaIcon = '🔄';
                    asalSumberDanaBadge = 'bg-purple-500/20 text-purple-300 border-purple-500/40';
                } else if (isBarangOrigin) {
                    asalSumberDana = 'belanja_barang';
                    asalSumberDanaLabel = '🛍️ Belanja Barang & Jasa (Perbekalan)';
                    asalSumberDanaIcon = '🛍️';
                    asalSumberDanaBadge = 'bg-indigo-500/20 text-indigo-300 border-indigo-500/40';
                } else if (bm || targetAstap?.sumber_dana === 'belanja_modal' || isDimanfaatkan || !targetAstap?.sumber_dana) {
                    asalSumberDana = 'belanja_modal';
                    asalSumberDanaLabel = '🏛️ Belanja Modal (APBD / BLUD RSUD)';
                    asalSumberDanaIcon = '🏛️';
                    asalSumberDanaBadge = 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40';
                } else {
                    asalSumberDana = 'kemitraan_murni';
                    asalSumberDanaLabel = '🤝 Kemitraan Pihak Ketiga';
                    asalSumberDanaIcon = '🤝';
                    asalSumberDanaBadge = 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40';
                }

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

                    // Sumber Asal Perolehan Pra-Kemitraan (BMD RSUD)
                    asal_sumber_dana: asalSumberDana,
                    asal_sumber_dana_label: asalSumberDanaLabel,
                    asal_sumber_dana_icon: asalSumberDanaIcon,
                    asal_sumber_dana_badge: asalSumberDanaBadge,

                    // Data Belanja Modal Pengadaan Asal
                    penyedia_pengadaan_nama: bm?.penyedia_nama || targetAstap?.penyedia_nama || spec.penyedia_nama || bb?.toko_penyedia || '-',
                    penyedia_pengadaan_pemilik: bm?.penyedia_pemilik || targetAstap?.penyedia_pemilik || spec.penyedia_pemilik || '-',
                    penyedia_pengadaan_alamat: bm?.penyedia_alamat || targetAstap?.penyedia_alamat || spec.penyedia_alamat || '-',
                    penyedia_pengadaan_rekening: (bm?.penyedia_rekening_nama ? (bm.penyedia_rekening_nama + ' - ') : '') + (bm?.penyedia_rekening_nomor || targetAstap?.penyedia_rekening_nomor || '-'),
                    spk_nomor_pengadaan: bm?.spk_nomor || targetAstap?.spk_nomor || spec.spk_nomor || '-',
                    spk_tanggal_pengadaan: bm?.spk_tanggal || targetAstap?.spk_tanggal || spec.spk_tanggal || null,
                    faktur_nomor_pengadaan: bm?.faktur_nomor || targetAstap?.faktur_nomor || spec.faktur_nomor || bb?.nomor_faktur || '-',
                    faktur_tanggal_pengadaan: bm?.faktur_tanggal || targetAstap?.faktur_tanggal || spec.faktur_tanggal || bb?.tanggal_faktur || null,
                    sp2d_nomor_pengadaan: bm?.sp2d_nomor || targetAstap?.sp2d_nomor || spec.sp2d_nomor || '-',
                    sp2d_tanggal_pengadaan: bm?.sp2d_tanggal || targetAstap?.sp2d_tanggal || spec.sp2d_tanggal || null,
                    bast_nomor_pengadaan: bm?.bast_dokumen_nomor || targetAstap?.bast_dokumen_nomor || spec.bast_dokumen_nomor || '-',
                    bast_tanggal_pengadaan: bm?.bast_dokumen_tanggal || targetAstap?.bast_dokumen_tanggal || spec.bast_dokumen_tanggal || null,
                    rekening_belanja_pengadaan: (bm?.rekening_belanja?.kode_rek ? (bm.rekening_belanja.kode_rek + ' - ') : '') + (bm?.rekening_belanja?.nama_belanja || targetAstap?.rekening_belanja?.nama_belanja || spec.rekening_nama || '-'),
                    metode_pengadaan: bm?.jenis_pengadaan?.nama_pengadaan || targetAstap?.jenis_pengadaan?.nama_pengadaan || spec.metode_pengadaan || spec.cara_perolehan || 'Pembelian Langsung / DPA',

                    // Data Hibah / Mutasi / Belanja Barang
                    hibah_pemberi: targetAstap?.hibah_pemberi || (hibahList && hibahList[0]?.pemberi_hibah) || spec.hibah_info?.pihak || spec.pemberi || '-',
                    hibah_nomor_bast: targetAstap?.hibah_nomor_bast || (hibahList && hibahList[0]?.nomor_bast) || spec.hibah_info?.nomor_bast || spec.nomor_bast || '-',
                    hibah_tanggal_bast: targetAstap?.hibah_tanggal_bast || (hibahList && hibahList[0]?.tanggal_bast) || spec.hibah_info?.tanggal_bast || spec.tanggal_bast || null,
                    mutasi_skpd_asal: (mutasiList && mutasiList[0]?.opd_asal) || spec.mutasi_info?.skpd_asal || spec.opd_asal || 'SKPD Pemerintah Daerah',
                    mutasi_nomor_sk: (mutasiList && mutasiList[0]?.nomor_sk_dasar) || spec.mutasi_info?.nomor_sk || spec.nomor_sk || '-',
                    mutasi_nomor_bamb: (mutasiList && mutasiList[0]?.nomor_bamb) || spec.mutasi_info?.nomor_bast || spec.nomor_bamb || '-',
                    mutasi_tanggal: (mutasiList && mutasiList[0]?.tanggal_mutasi) || spec.mutasi_info?.tanggal_bast || null,

                    // Data Riwayat Transisi Reklasifikasi
                    has_reklas_origin: Boolean(reklasOrigin || (targetAstap?.is_reklas && targetAstap?.jenis_reklas) || (kemitraan?.is_reklas_pending)),
                    reklas_nomor_ba: reklasOrigin?.nomor_ba_reklas || spec.nomor_ba_reklas || '-',
                    reklas_tanggal: reklasOrigin?.tanggal_reklas || spec.tanggal_reklas || null,
                    reklas_asal_kib: reklasOrigin?.asal_kib || spec.asal_kib || (targetAstap?.category || 'KIB Asal'),
                    reklas_asal_kode: reklasOrigin?.asal_kode || spec.asal_kode || '-',
                    reklas_asal_nama: reklasOrigin?.asal_nama || spec.asal_nama || (targetAstap?.nama_barang || '-'),
                    reklas_alasan: reklasOrigin?.alasan_reklas || reklasOrigin?.keterangan || spec.alasan_reklas || 'Optimalisasi Pemanfaatan Barang Milik Daerah (BMD) bersama Pihak Ketiga',

                    // Informasi Mitra Rekanan
                    mitra_nama: kemitraan?.mitra_nama || spec.mitra_nama || '-',
                    mitra_pimpinan: kemitraan?.mitra_pimpinan || spec.mitra_pimpinan || '-',
                    mitra_alamat: kemitraan?.mitra_alamat || spec.mitra_alamat || '-',
                    mitra_telepon: spec.mitra_telepon || spec.penyedia_telepon || spec.penyedia_kontak || '-',

                    // Kompatibilitas mundur
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
                    skema_kemitraan: kemitraan?.skema_kemitraan || spec.skema_kemitraan || 'Sewa',
                    harga_sewa: spec.harga_sewa || spec.kemitraan_harga_sewa || kemitraan?.harga_sewa || null,
                    periode_sewa: spec.periode_sewa || spec.kemitraan_periode_sewa || kemitraan?.periode_sewa || 'Per Tahun',
                    nilai_kontribusi: spec.nilai_kontribusi || spec.kemitraan_nilai_kontribusi || kemitraan?.nilai_kontribusi || null,
                    keterangan_kontribusi: spec.keterangan_kontribusi || spec.kemitraan_keterangan_kontribusi || kemitraan?.keterangan_kontribusi || null,
                    tanggal_mulai: kemitraan?.tanggal_mulai || spec.tanggal_mulai || null,
                    tanggal_selesai: kemitraan?.tanggal_selesai || spec.tanggal_selesai || null,
                    status_konsesi: kemitraan?.status_konsesi || 'Aktif',
                    sisa_hari_konsesi: kemitraan?.sisa_hari_konsesi ?? null,
                    dokumen_path: (() => {
                        const targetAstapId = astap?.id || kemitraan?.astap_id;
                        const targetKmtId = kemitraan?.id;
                        if (this.dokumenPathCache) {
                            if (targetAstapId && this.dokumenPathCache[targetAstapId] !== undefined) {
                                return this.dokumenPathCache[targetAstapId];
                            }
                            if (targetKmtId && this.dokumenPathCache['k_' + targetKmtId] !== undefined) {
                                return this.dokumenPathCache['k_' + targetKmtId];
                            }
                        }
                        return kemitraan?.dokumen_path || spec.dokumen_path || null;
                    })(),
                    is_dimanfaatkan: Boolean(isDimanfaatkan || (kemitraan && kemitraan.tipe_kemitraan === 'dimanfaatkan') || kemitraan?.is_reklas_pending),

                    // Relasi Objek Aset BMD RSUD (untuk aset yang ditambahkan mitra)
                    objek_astap_id: kemitraan?.objek_astap_id || spec.objek_astap_id || null,
                    objek_register_id: kemitraan?.objek_register_id || spec.objek_register_id || null,
                    objek_nibar: kemitraan?.objek_nibar || spec.objek_nibar || null,
                    objek_astap: kemitraan?.objek_astap || kemitraan?.objekAstap || null,
                    objek_register: kemitraan?.objek_register || kemitraan?.objekRegister || null,

                    // Riwayat Adendum & Status Extend PKS
                    is_extended: Boolean(kemitraan?.is_extended),
                    jumlah_extend: kemitraan?.jumlah_extend || 0,
                    riwayat_adendum: kemitraan?.riwayat_adendum || [],
                    dokumen_adendum_path: kemitraan?.dokumen_adendum_path || null,

                    // Informasi Terminasi Akhir Masa Konsesi
                    jenis_terminasi: kemitraan?.jenis_terminasi || spec?.terminasi?.jenis || null,
                    nomor_ba_terminasi: kemitraan?.nomor_ba_terminasi || spec?.terminasi?.nomor_ba || null,
                    tanggal_terminasi: kemitraan?.tanggal_terminasi || spec?.terminasi?.tanggal_terminasi || null,
                    berkas_ba_terminasi: kemitraan?.berkas_ba_terminasi || spec?.terminasi?.berkas_path || null,
                    kondisi_akhir: kemitraan?.kondisi_akhir || spec?.terminasi?.kondisi_akhir || null,
                    pihak_penyerah_nama: kemitraan?.pihak_penyerah_nama || spec?.terminasi?.pihak_penyerah || null,
                    pihak_penyerah_jabatan: kemitraan?.pihak_penyerah_jabatan || null,
                    pihak_penerima_nama: kemitraan?.pihak_penerima_nama || spec?.terminasi?.pihak_penerima || null,
                    pihak_penerima_jabatan: kemitraan?.pihak_penerima_jabatan || null,
                    catatan_terminasi: kemitraan?.catatan_terminasi || spec?.terminasi?.catatan || null,
                    is_transferred_to_rsud: Boolean(kemitraan?.is_transferred_to_rsud || (spec?.terminasi?.jenis === 'serah_terima_rsud')),
                    target_kib_reklas: kemitraan?.target_kib_reklas || spec?.terminasi?.target_kib || null,
                    target_kode_108: kemitraan?.target_kode_108 || spec?.terminasi?.target_kode_108 || null,
                    nilai_perolehan_rsud: kemitraan?.nilai_perolehan_rsud || spec?.terminasi?.nilai_perolehan_rsud || null,

                    // Informasi Pembatalan Status Kemitraan
                    alasan_batal: kemitraan?.alasan_batal || spec?.alasan_batal || null,
                    dibatalkan_at: kemitraan?.dibatalkan_at || spec?.dibatalkan_at || null,
                    dibatalkan_by: kemitraan?.dibatalkan_by || spec?.dibatalkan_by || null,

                    raw_row: kemitraan || {},
                    raw_astap: astap || {}
                };

                // Kompatibilitas state lama
                this.activeDetail = {
                    kemitraan: kemitraan || {},
                    astap: this.selectedAstapDetail,
                    register: register || (registers[0] || {})
                };

                this.statusForm = {
                    id: kemitraan ? kemitraan.id : null,
                    status_konsesi: kemitraan?.status_konsesi || 'Aktif'
                };

                this.detailKondisiFilter = 'all';
                this.detailPenempatanFilter = 'all';
                this.detailSearchQuery = '';
                this.showDetailModal = true;
            },

            // ─── Logika Relasi Aset Ditambahkan Mitra pada Objek Pemanfaatan BMD ───
            getAsetDitambahkanMitraForDetail(detailItem) {
                if (!detailItem) return [];

                const pks = (detailItem.nomor_pks || '').trim().toLowerCase();
                const mitra = (detailItem.penyedia_nama || detailItem.mitra_nama || '').trim().toLowerCase();
                const astapId = detailItem.id || detailItem.astap_id;
                const kemitraanId = detailItem.kemitraan_id;

                const nibarList = (detailItem.registers || [])
                    .map(r => (r.nibar || r.no_register || '').trim().toLowerCase())
                    .filter(n => n && n !== '-');
                if (detailItem.objek_nibar) {
                    nibarList.push(String(detailItem.objek_nibar).trim().toLowerCase());
                }

                const list = this.allDitambahkanDetailList || window.__allDitambahkanDetailList || [];

                return list.filter(item => {
                    if (!item) return false;

                    // 1. Cek kesamaan Nomor PKS (jika nomor PKS valid dan bukan placeholder)
                    const itemPks = (item.nomor_pks || '').trim().toLowerCase();
                    if (pks && pks !== '-' && pks !== 'belum ada pks' && itemPks === pks) {
                        return true;
                    }

                    // 2. Cek kesamaan ID Objek Astap / Kemitraan
                    if (item.objek_astap_id && (item.objek_astap_id == astapId || item.objek_astap_id == kemitraanId)) {
                        return true;
                    }

                    // 3. Cek kesamaan NIBAR objek
                    const itemObjNibar = (item.objek_nibar || '').trim().toLowerCase();
                    if (itemObjNibar && nibarList.includes(itemObjNibar)) {
                        return true;
                    }

                    // 4. Jika nama mitra cocok dan nomor PKS cocok
                    if (mitra && mitra !== '-' && (item.mitra_nama || '').trim().toLowerCase() === mitra) {
                        if (pks && pks !== '-' && itemPks === pks) {
                            return true;
                        }
                    }

                    return false;
                });
            },

            getTotalNilaiAsetDitambahkanMitra(detailItem) {
                const items = this.getAsetDitambahkanMitraForDetail(detailItem);
                return items.reduce((sum, it) => sum + (parseFloat(it.nilai_aset) || 0), 0);
            },

            getTotalVolumeAsetDitambahkanMitra(detailItem) {
                const items = this.getAsetDitambahkanMitraForDetail(detailItem);
                return items.reduce((sum, it) => sum + (parseInt(it.jumlah_volume) || 1), 0);
            },

            getLinkTambahAsetMitra(detailItem) {
                const baseUrl = '{{ route('astap.create_kemitraan') }}';
                if (!detailItem) return baseUrl + '?tipe=ditambahkan';
                const p = new URLSearchParams();
                p.set('tipe', 'ditambahkan');
                if (detailItem.nomor_pks && detailItem.nomor_pks !== '-' && detailItem.nomor_pks !== 'Belum Ada PKS') {
                    p.set('pks', detailItem.nomor_pks);
                }
                if (detailItem.penyedia_nama && detailItem.penyedia_nama !== '-') {
                    p.set('mitra', detailItem.penyedia_nama);
                }
                if (detailItem.penyedia_pemilik && detailItem.penyedia_pemilik !== '-') {
                    p.set('pimpinan', detailItem.penyedia_pemilik);
                }
                if (detailItem.penyedia_alamat && detailItem.penyedia_alamat !== '-') {
                    p.set('alamat', detailItem.penyedia_alamat);
                }
                if (detailItem.skema_kemitraan && detailItem.skema_kemitraan !== '-') {
                    p.set('skema', detailItem.skema_kemitraan);
                }
                if (detailItem.id) {
                    p.set('objek_id', detailItem.id);
                }
                const nibar = detailItem.objek_nibar || (detailItem.registers && detailItem.registers[0] ? detailItem.registers[0].nibar : null);
                if (nibar && nibar !== '-') {
                    p.set('objek_nibar', nibar);
                }
                return baseUrl + '?' + p.toString();
            },

            openDetailFromDitambahkan(item) {
                if (!item) return;
                this.openDetail(item.raw_row, item.raw_astap, item.raw_register, false);
            },

            // ─── Logika Pencarian Objek Pemanfaatan BMD Asal untuk Aset Ditambahkan Mitra ───
            getObjekPemanfaatanAsal(detailItem) {
                if (!detailItem) return null;

                const pks = (detailItem.nomor_pks || '').trim().toLowerCase();
                const mitra = (detailItem.penyedia_nama || detailItem.mitra_nama || '').trim().toLowerCase();
                const objekAstapId = detailItem.objek_astap_id || detailItem.kemitraan?.objek_astap_id || detailItem.spesifikasi_json?.objek_astap_id;
                const objekNibar = (detailItem.objek_nibar || detailItem.kemitraan?.objek_nibar || detailItem.spesifikasi_json?.objek_nibar || '').trim().toLowerCase();

                const dimList = this.allDimanfaatkanDetailList || window.__allDimanfaatkanDetailList || [];

                // 1. Cari berdasarkan kecocokan ID Objek Astap
                if (objekAstapId) {
                    const match = dimList.find(d => d.astap_id == objekAstapId || d.objek_astap_id == objekAstapId || d.id == objekAstapId);
                    if (match) return match;
                }

                // 2. Cari berdasarkan kecocokan NIBAR objek
                if (objekNibar && objekNibar !== '-') {
                    const match = dimList.find(d => {
                        const dNibar = (d.nibar || d.objek_nibar || '').trim().toLowerCase();
                        return dNibar === objekNibar;
                    });
                    if (match) return match;
                }

                // 3. Cari berdasarkan kecocokan nomor PKS yang sama
                if (pks && pks !== '-' && pks !== 'belum ada pks') {
                    const match = dimList.find(d => {
                        const dPks = (d.nomor_pks || '').trim().toLowerCase();
                        return dPks === pks;
                    });
                    if (match) return match;
                }

                // 4. Jika ada relasi langsung kemitraan.objekAstap atau objek_astap
                const oa = detailItem.objek_astap || detailItem.kemitraan?.objekAstap || detailItem.kemitraan?.objek_astap;
                if (oa) {
                    let oSpec = oa.spesifikasi_json;
                    if (typeof oSpec === 'string') {
                        try { oSpec = JSON.parse(oSpec); } catch(e) { oSpec = {}; }
                    }
                    oSpec = oSpec || {};
                    return {
                        nama_barang: oSpec.tanah_nama_barang || oSpec.gedung_nama_bangunan || oa.nama_barang || 'Objek BMD RSUD',
                        category: this.getEffectiveKibCategory(oa),
                        kode_barang: oa.kode_barang || oa.kode_108 || '-',
                        nibar: detailItem.objek_nibar || (oa.registers && oa.registers[0] ? oa.registers[0].nibar : '-'),
                        alamat: oa.alamat_barang || '-',
                        luas: oSpec.luas_m2 || oSpec.tanah_luas_m2 || oSpec.gedung_luas_lantai || null,
                        skema_kemitraan: detailItem.skema_kemitraan || 'KSO',
                        nomor_pks: detailItem.nomor_pks || '-',
                        status_konsesi: detailItem.status_konsesi || 'Aktif',
                        raw_row: null,
                        raw_astap: oa,
                        raw_register: oa.registers ? oa.registers[0] : null
                    };
                }

                // 5. Fallback representasi jika ada objek_nibar di catatan
                if (objekNibar && objekNibar !== '-') {
                    return {
                        nama_barang: detailItem.spesifikasi_json?.objek_nama_barang || 'Objek BMD RSUD Dr. H. Koesnandi',
                        category: detailItem.spesifikasi_json?.objek_kib || 'KIB A',
                        nibar: detailItem.objek_nibar || detailItem.spesifikasi_json?.objek_nibar,
                        kode_barang: detailItem.spesifikasi_json?.objek_kode_108 || '-',
                        alamat: detailItem.alamat_barang || 'RSUD Dr. H. Koesnandi',
                        skema_kemitraan: detailItem.skema_kemitraan || 'KSO',
                        nomor_pks: detailItem.nomor_pks || '-',
                        status_konsesi: detailItem.status_konsesi || 'Aktif',
                        raw_row: null,
                        raw_astap: null,
                        raw_register: null
                    };
                }

                return null;
            },

            openDetailFromDimanfaatkan(item) {
                if (!item) return;
                if (item.raw_row && item.raw_astap) {
                    this.openDetail(item.raw_row, item.raw_astap, item.raw_register, true);
                } else if (item.raw_astap) {
                    this.openDetail(null, item.raw_astap, item.raw_register, true);
                }
            },

            // ─── Fitur Modal Cetak BAST Pemanfaatan / Penambahan Aset Kemitraan (Format Modal SIMAT) ───
            openPrintBast(item, isDim = null) {
                if (!item) return;

                // Tutup modal detail jika sedang terbuka agar pratinjau BAST tampil bersih & mulus
                this.showDetailModal = false;

                try {
                    let astap = item.astap || (item.nama_barang ? item : null);
                    let kemitraan = item.astap ? item : (item.kemitraan || null);
                    let spec = astap?.spesifikasi_json || item.spesifikasi_json || {};
                    if (typeof spec === 'string') {
                        try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                    }

                    // Tentukan apakah tipe 'dimanfaatkan', 'ditambahkan', 'pengembalian', atau 'serah_terima_rsud'
                    let formatTipe = 'ditambahkan';
                    let isDimanfaatkan = false;

                    if (isDim === 'pengembalian' || isDim === 'serah_terima_rsud') {
                        formatTipe = isDim;
                        isDimanfaatkan = (item.is_dimanfaatkan !== undefined) ? !!item.is_dimanfaatkan : false;
                    } else if (isDim === true || isDim === 'dimanfaatkan') {
                        formatTipe = 'dimanfaatkan';
                        isDimanfaatkan = true;
                    } else if (isDim === false || isDim === 'ditambahkan') {
                        formatTipe = 'ditambahkan';
                        isDimanfaatkan = false;
                    } else {
                        if (item.is_dimanfaatkan !== undefined) {
                            isDimanfaatkan = !!item.is_dimanfaatkan;
                        } else if (astap && (astap.is_reklas || (astap.reklas && astap.reklas.length > 0))) {
                            isDimanfaatkan = true;
                        } else if (item.objek_nibar || item.objek_register_id || item.objek_astap_id || spec.objek_nibar || spec.objek_register_id || spec.objek_astap_id || item.is_reklas_pending) {
                            isDimanfaatkan = true;
                        } else {
                            isDimanfaatkan = false;
                        }
                        formatTipe = isDimanfaatkan ? 'dimanfaatkan' : 'ditambahkan';
                    }

                    let targetId = kemitraan?.id || item.kemitraan_id || astap?.id || item.id || 'draft';
                    this.currentBastTargetId = targetId;
                    this.currentBastItem = item;
                    this.currentBastIsDim = isDimanfaatkan;
                    this.currentBastStorageKey = `bast_kemitraan_modal_${formatTipe}_${targetId}`;

                    // Resolusi Nama Fisik Asli
                    let namaFisik = astap?.nama_barang || item.nama_barang || (isDimanfaatkan ? 'Objek Aset BMD RSUD' : 'Peralatan / Objek Aset Mitra');
                    if (isDimanfaatkan) {
                        if (astap?.reklas && Array.isArray(astap.reklas) && astap.reklas.length > 0) {
                            let lastReklas = astap.reklas[astap.reklas.length - 1];
                            if (lastReklas?.asal_nama) namaFisik = lastReklas.asal_nama;
                        } else if (item.objekAstap?.nama_barang) {
                            namaFisik = item.objekAstap.nama_barang;
                        } else if (spec?.mesin_items?.[0]?.mesin_nama_barang) {
                            namaFisik = spec.mesin_items[0].mesin_nama_barang;
                        } else if (spec?.tanah_items?.[0]?.tanah_nama_barang) {
                            namaFisik = spec.tanah_items[0].tanah_nama_barang;
                        }
                    } else {
                        if (item.nama_barang) {
                            namaFisik = item.nama_barang;
                        } else if (astap?.nama_barang) {
                            namaFisik = astap.nama_barang;
                        }
                    }

                    // Tanggal PKS & Tanggal BAST
                    let rawTglPks = (formatTipe === 'pengembalian' || formatTipe === 'serah_terima_rsud') 
                        ? (kemitraan?.tanggal_terminasi || kemitraan?.tanggal_selesai || item.tanggal_terminasi || new Date().toISOString().slice(0, 10))
                        : (kemitraan?.tanggal_pks || item.tanggal_pks || spec?.tanggal_pks || astap?.bast_dokumen_tanggal || new Date().toISOString().slice(0, 10));
                    let dPks = new Date(rawTglPks);
                    if (isNaN(dPks.getTime())) dPks = new Date();

                    const namaHari = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'][dPks.getDay()];
                    const namaBulan = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'][dPks.getMonth()];
                    let tglPksStr = `${dPks.getDate()} ${namaBulan} ${dPks.getFullYear()}`;
                    let hariTglStr = `${namaHari} tanggal ${tglPksStr}`;

                    let thnPks = dPks.getFullYear();
                    let prefixNoBast = isDimanfaatkan ? 'BAST-KMT-MANFAAT' : 'BAST-KMT-PENGADAAN';
                    if (formatTipe === 'pengembalian') prefixNoBast = 'BAST-KMT-KEMBALI';
                    if (formatTipe === 'serah_terima_rsud') prefixNoBast = 'BAST-KMT-MILIK';

                    let noBastDefault = ((formatTipe === 'pengembalian' || formatTipe === 'serah_terima_rsud') && kemitraan?.nomor_ba_terminasi) 
                        ? kemitraan.nomor_ba_terminasi 
                        : `000.2.3.2/${prefixNoBast}/${targetId}/430.10.7/${thnPks}`;
                    let noPksDefault = kemitraan?.nomor_pks || item.nomor_pks || spec?.nomor_pks || astap?.bast_dokumen_nomor || `000.2.3.2/PKS-KMT/${targetId}/${thnPks}`;

                    // Masa Konsesi
                    let masaKonsesi = '';
                    let tglMulai = kemitraan?.tanggal_mulai || item.tanggal_mulai || spec?.tanggal_mulai;
                    let tglSelesai = kemitraan?.tanggal_selesai || item.tanggal_selesai || spec?.tanggal_selesai;
                    if (tglMulai && tglSelesai) {
                        masaKonsesi = `${this.formatDateIndo(tglMulai)} s.d. ${this.formatDateIndo(tglSelesai)}`;
                    } else {
                        masaKonsesi = kemitraan?.jangka_waktu || item.jangka_waktu || spec?.jangka_waktu || '5 (Lima) Tahun';
                    }

                    // Nilai Aset
                    let nilaiNum = parseFloat(kemitraan?.nilai_aset || item.nilai_aset || astap?.total_realisasi || item.total_realisasi || 0);
                    let nilaiFormatted = (nilaiNum || 0).toLocaleString('id-ID');

                    // NIBAR & Identitas Barang
                    let nibar = item.objek_nibar || item.nibar || astap?.registers?.[0]?.nibar || spec?.objek_nibar || spec?.nibar || (isDimanfaatkan ? '-' : 'BARU-KMT-' + targetId);
                    let volStr = `${kemitraan?.jumlah_volume || item.jumlah_volume || astap?.jumlah_volume || 1} ${kemitraan?.satuan || item.satuan || astap?.satuan || 'Unit'}`;
                    if (spec?.luas_m2 || spec?.tanah_luas_m2) {
                        volStr = `${spec.luas_m2 || spec.tanah_luas_m2} m²`;
                    }

                    // Merk & Spesifikasi untuk Aset Ditambahkan Mitra
                    let merkSpec = spec?.merk || spec?.mesin_merk || '';
                    let typeSpec = spec?.type || spec?.mesin_type || '';
                    let noPabrikSpec = spec?.no_pabrik || spec?.mesin_no_pabrik || '';

                    let defaultDoc = {
                        format_tipe: formatTipe, // 'dimanfaatkan' | 'ditambahkan'
                        nomor_bast: noBastDefault,
                        hari_tanggal: hariTglStr,
                        lokasi: 'RSUD Dr. H. Koesnandi Kabupaten Bondowoso',
                        nomor_pks: noPksDefault,
                        tanggal_pks: tglPksStr,
                        skema_kemitraan: kemitraan?.skema_kemitraan || item.skema_kemitraan || spec?.skema_kemitraan || (isDimanfaatkan ? 'Sewa Barang Milik Daerah' : 'Kerja Sama Operasional (KSO)'),
                        
                        // Pihak Kesatu (RSUD)
                        p1_nama: 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR',
                        p1_nip: '19771002 200604 1 006',
                        p1_pangkat: 'Pembina Tingkat I (IV/b)',
                        p1_jabatan: 'Direktur RSUD dr. H. Koesnandi Bondowoso',
                        p1_instansi: 'RSUD dr. H. Koesnandi Kabupaten Bondowoso',
                        
                        // Pihak Kedua (Mitra)
                        p2_perusahaan: kemitraan?.mitra_nama || item.mitra_nama || item.penyedia_nama || spec?.mitra_nama || 'Mitra Kerja Sama',
                        p2_pimpinan: kemitraan?.mitra_pimpinan || item.mitra_pimpinan || item.penyedia_pemilik || spec?.mitra_pimpinan || 'Pimpinan / Direktur Rekanan',
                        p2_jabatan: 'Pimpinan / Kuasa Direksi',
                        p2_alamat: kemitraan?.mitra_alamat || item.mitra_alamat || item.penyedia_alamat || spec?.mitra_alamat || 'Jl. Kapten Piere Tendean, Bondowoso',
                        
                        // Objek Barang
                        aset_nama: namaFisik,
                        aset_merk: merkSpec,
                        aset_type: typeSpec,
                        aset_no_pabrik: noPabrikSpec,
                        aset_lokasi: astap?.alamat_barang || item.alamat_barang || 'Kompleks RSUD Dr. H. Koesnandi Bondowoso',
                        aset_kode108: astap?.kode_108 || item.kode_108 || (isDimanfaatkan ? '1.5.2.01.01.02.001' : '1.5.2.01.01.02.002'),
                        aset_nibar: nibar,
                        aset_volume: volStr,
                        aset_kondisi: item.kondisi || astap?.kondisi_barang || (isDimanfaatkan ? 'Baik' : 'Baik (100% Baru)'),
                        aset_keterangan: masaKonsesi,
                        aset_nilai: nilaiFormatted,
                        
                        // Pengurus Barang & TTE BSrE
                        pb_nama: 'BUDI HARTONO, S.Sos',
                        pb_nip: '19760229 200801 1 010',
                        qr_hash: noBastDefault ? noBastDefault.replace(/[^a-zA-Z0-9]/g, '-') : 'BSRE-KEMITRAAN-PENGURUS'
                    };

                    this.defaultBastDoc = Object.assign({}, defaultDoc);

                    // Muat data dari localStorage jika pernah diedit
                    this.isBastModified = false;
                    try {
                        let saved = localStorage.getItem(this.currentBastStorageKey);
                        if (saved) {
                            let parsed = JSON.parse(saved);
                            this.bastDoc = Object.assign({}, defaultDoc, parsed);
                            if (!this.bastDoc.format_tipe) {
                                this.bastDoc.format_tipe = formatTipe;
                            }
                            if (!this.bastDoc.qr_hash) {
                                this.bastDoc.qr_hash = defaultDoc.qr_hash;
                            }
                            this.isBastModified = true;
                        } else {
                            this.bastDoc = Object.assign({}, defaultDoc);
                        }
                    } catch(e) {
                        this.bastDoc = Object.assign({}, defaultDoc);
                    }
                } catch(e) {
                    console.error('Error generating BAST document:', e);
                }

                this.showEditBastForm = false;
                this.showModalPrintBast = true;
            },

            // Beralih Template Format BAST secara Live di Modal
            switchBastFormat(newType) {
                if (!this.bastDoc || this.bastDoc.format_tipe === newType) return;
                this.bastDoc.format_tipe = newType;
                
                // Update storage key
                let targetId = this.currentBastTargetId || 'draft';
                this.currentBastStorageKey = `bast_kemitraan_modal_${newType}_${targetId}`;

                // Update nomor surat jika masih memakai format standar
                if (this.bastDoc.nomor_bast) {
                    if (newType === 'ditambahkan' && this.bastDoc.nomor_bast.includes('BAST-KMT-MANFAAT')) {
                        this.bastDoc.nomor_bast = this.bastDoc.nomor_bast.replace('BAST-KMT-MANFAAT', 'BAST-KMT-PENGADAAN');
                    } else if (newType === 'dimanfaatkan' && this.bastDoc.nomor_bast.includes('BAST-KMT-PENGADAAN')) {
                        this.bastDoc.nomor_bast = this.bastDoc.nomor_bast.replace('BAST-KMT-PENGADAAN', 'BAST-KMT-MANFAAT');
                    } else if (this.bastDoc.nomor_bast.includes('/BAST-KMT/')) {
                        let prefixNoBast = newType === 'dimanfaatkan' ? 'BAST-KMT-MANFAAT' : 'BAST-KMT-PENGADAAN';
                        this.bastDoc.nomor_bast = this.bastDoc.nomor_bast.replace('/BAST-KMT/', `/${prefixNoBast}/`);
                    }
                }

                // Cek apakah ada saved draft untuk tipe yang dipilih
                try {
                    let saved = localStorage.getItem(this.currentBastStorageKey);
                    if (saved) {
                        let parsed = JSON.parse(saved);
                        this.bastDoc = Object.assign({}, this.bastDoc, parsed, { format_tipe: newType });
                        this.isBastModified = true;
                    }
                } catch(e) {}
            },

            // Cetak Dokumen BAST Langsung via iFrame Tanpa Ganti Halaman
            printCurrentBast() {
                const el = document.getElementById('print-area-bast-kemitraan');
                if (!el) {
                    window.print();
                    return;
                }

                let iframe = document.getElementById('simat-kemitraan-print-frame');
                if (iframe) iframe.remove();

                iframe = document.createElement('iframe');
                iframe.id = 'simat-kemitraan-print-frame';
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
    <title>BAST Kemitraan - ${this.bastDoc?.nomor_bast || 'RSUD Dr. H. Koesnandi'}</title>
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
            font-family: Arial, 'Helvetica Neue', Helvetica, sans-serif;
            font-size: 9.5pt;
            line-height: 1.4;
        }
        table {
            border-collapse: collapse;
            width: 100%;
            table-layout: fixed;
        }
        .no-print { display: none !important; }
    </style>
</head>
<body style="background:#ffffff; color:#000000;">
    ${el.innerHTML}
</body>
</html>`);
                doc.close();

                setTimeout(() => {
                    try {
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                    } catch(e) {
                        window.print();
                    }
                }, 400);
            },

            // Simpan Perubahan Data BAST ke localStorage
            saveBastData() {
                if (!this.bastDoc || !this.currentBastStorageKey) return;
                try {
                    localStorage.setItem(this.currentBastStorageKey, JSON.stringify(this.bastDoc));
                    this.isBastModified = true;
                    this.showToast('Data BAST berhasil disimpan!');
                } catch(e) {
                    console.error('Error saving BAST data:', e);
                }
            },

            // Reset Data BAST ke Standar Awal
            resetBastData() {
                if (confirm('Apakah Anda yakin ingin membatalkan semua perubahan dan mengembalikan BAST ke data asli sistem?')) {
                    try {
                        localStorage.removeItem(this.currentBastStorageKey);
                    } catch(e) {}
                    this.isBastModified = false;
                    if (this.defaultBastDoc) {
                        this.bastDoc = Object.assign({}, this.defaultBastDoc);
                    }
                    this.showToast('Data BAST berhasil dikembalikan ke standar awal.');
                }
            },

            // ─── Fitur Dokumen BAST & Berkas Kerjasama (Modal Detail) ───────────
            showDokumenImageModal: false,
            previewDokumenUrl: '',
            previewDokumenNama: '',
            isUploadingDokumen: false,
            isDeletingDokumen: false,

            // Cek apakah berkas merupakan format gambar
            isDokumenImage(path) {
                if (!path) return false;
                const ext = String(path).split('.').pop().toLowerCase();
                return ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'].includes(ext);
            },

            // Buka Pratinjau Dokumen BAST (Gambar via Lightbox, PDF di Tab Baru)
            openDokumenPreview(path, nama) {
                if (!path) return;
                const url = path.startsWith('http') || path.startsWith('/') ? path : ('/storage/' + path);
                if (this.isDokumenImage(path)) {
                    this.previewDokumenUrl = url;
                    this.previewDokumenNama = nama || (path.split('/').pop() || 'Pratinjau Berkas BAST');
                    this.showDokumenImageModal = true;
                } else {
                    window.open(url, '_blank');
                }
            },

            // Memicu dialog upload file BAST dari detail
            triggerUploadDokumenDetail() {
                const input = document.getElementById('fileInputDokumenDetail');
                if (input) input.click();
            },

            // Upload atau Ubah Dokumen BAST dari Modal Detail
            async handleUploadDokumenDetail(event) {
                const file = event.target.files && event.target.files[0];
                if (!file) return;

                if (file.size > 10 * 1024 * 1024) {
                    this.showToast('Gagal Mengunggah', 'Ukuran berkas melebihi batas maksimal 10 MB.', 'danger');
                    event.target.value = '';
                    return;
                }

                const astapId = this.selectedAstapDetail?.id;
                if (!astapId) {
                    this.showToast('Error', 'ID aset tidak ditemukan.', 'danger');
                    return;
                }

                const formData = new FormData();
                formData.append('dokumen_file', file);
                formData.append('_token', '{{ csrf_token() }}');

                this.isUploadingDokumen = true;
                try {
                    const res = await fetch(`/astap/kemitraan/${astapId}/upload-dokumen`, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const json = await res.json();
                    if (json.success) {
                        this.selectedAstapDetail.dokumen_path = json.dokumen_path;
                        if (this.selectedAstapDetail.kemitraan) {
                            this.selectedAstapDetail.kemitraan.dokumen_path = json.dokumen_path;
                        }
                        if (!this.dokumenPathCache) this.dokumenPathCache = {};
                        if (astapId) this.dokumenPathCache[astapId] = json.dokumen_path;
                        if (this.selectedAstapDetail.kemitraan_id) {
                            this.dokumenPathCache['k_' + this.selectedAstapDetail.kemitraan_id] = json.dokumen_path;
                        }
                        this.showToast('Berhasil', json.message || 'Berkas BAST berhasil diperbarui.', 'success');
                    } else {
                        this.showToast('Gagal', json.message || 'Terjadi kesalahan saat mengunggah berkas.', 'danger');
                    }
                } catch (e) {
                    this.showToast('Error', 'Gagal menghubungi server.', 'danger');
                } finally {
                    this.isUploadingDokumen = false;
                    event.target.value = '';
                }
            },

            // Hapus Berkas Dokumen BAST dari Modal Detail
            async deleteDokumenDetail() {
                const astapId = this.selectedAstapDetail?.id;
                if (!astapId) return;

                if (!confirm('Apakah Anda yakin ingin menghapus berkas dokumen BAST ini dari sistem?')) {
                    return;
                }

                this.isDeletingDokumen = true;
                try {
                    const res = await fetch(`/astap/kemitraan/${astapId}/delete-dokumen`, {
                        method: 'DELETE',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const json = await res.json();
                    if (json.success) {
                        this.selectedAstapDetail.dokumen_path = null;
                        if (this.selectedAstapDetail.kemitraan) {
                            this.selectedAstapDetail.kemitraan.dokumen_path = null;
                        }
                        if (!this.dokumenPathCache) this.dokumenPathCache = {};
                        if (astapId) this.dokumenPathCache[astapId] = null;
                        if (this.selectedAstapDetail.kemitraan_id) {
                            this.dokumenPathCache['k_' + this.selectedAstapDetail.kemitraan_id] = null;
                        }
                        this.showToast('Berhasil', json.message || 'Berkas BAST berhasil dihapus.', 'success');
                    } else {
                        this.showToast('Gagal', json.message || 'Gagal menghapus berkas.', 'danger');
                    }
                } catch (e) {
                    this.showToast('Error', 'Gagal menghubungi server.', 'danger');
                } finally {
                    this.isDeletingDokumen = false;
                }
            },

            // Helper untuk mengambil hanya unit register aktif (tidak soft-deleted)
            getActiveRegisters(astap) {
                if (!astap || !Array.isArray(astap.registers)) return [];
                return astap.registers.filter(r => !(r.is_deleted == 1 || r.is_deleted === true || r.is_deleted === '1'));
            },

            // Hitung statistik kondisi aset terdaftar (Standar 3 Kondisi: Baik, Kurang Baik, Rusak Berat)
            getKondisiStats(item) {
                if (!item) return { total: 0, baik: 0, kurang_baik: 0, rusak_berat: 0, pct_baik: 100, pct_kb: 0, pct_rb: 0, kondisi_dominan: 'Baik', is_multi: false, text: 'Baik (100%)', badge_class: 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30', dot_class: 'bg-emerald-400' };
                const regs = this.getActiveRegisters(item);
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

                let regs = this.getActiveRegisters(astap);
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
                if (!astap || !astap.registers) return null;
                let regs = this.getActiveRegisters(astap);
                if (regs.length === 0) return null;

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

                // JIKA remainingQuota masih bersisa (misal repeater hanya 2 baris, tetapi jumlah volume/registers ada 3 unit):
                // Pad kartu tambahan menggunakan template item terakhir agar SEMUA unit register NIBAR memiliki kartu spesifikasi!
                if (remainingQuota > 0 && result.length > 0) {
                    const lastTemplate = result[result.length - 1];
                    while (remainingQuota > 0) {
                        let newItem = JSON.parse(JSON.stringify(lastTemplate));
                        for (let k of qtyKeys) {
                            if (newItem[k] !== undefined) newItem[k] = 1;
                        }
                        result.push(newItem);
                        remainingQuota -= 1;
                    }
                }

                return result;
            },

            getEffectiveKibCategory(astap) {
                if (!astap) return 'KIB B';
                let cat = String(astap.category || '').toUpperCase().trim();
                if (cat === 'KIB A' || cat === 'KIB B' || cat === 'KIB C' || cat === 'KIB D' || cat === 'KIB E' || cat === 'KIB F' || cat === 'EXTRACOM' || cat === 'ATB') {
                    return cat;
                }

                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                spec = spec || {};

                // 1. Periksa isi repeater yang nyata (Prioritas tertinggi sesuai form fisik yang diinput!)
                if (spec?.gedung_items?.some(g => g.gedung_nama_barang || g.gedung_nama_bangunan || g.gedung_luas_m2 || g.gedung_luas_lantai || g.gedung_luas_lantai_m2 || g.gedung_bertingkat || (parseFloat(g.gedung_nilai_satuan || g.gedung_nilai_fisik) > 0))) return 'KIB C';
                if (spec?.mesin_items?.some(m => m.mesin_nama_barang || m.mesin_merk || m.mesin_type || m.mesin_no_pabrik || (parseFloat(m.mesin_nilai_satuan || m.mesin_nilai_fisik) > 0))) return 'KIB B';
                if (spec?.jaringan_items?.some(j => j.jaringan_nama_barang || j.jaringan_konstruksi || j.jaringan_panjang || (parseFloat(j.jaringan_nilai_satuan || j.jaringan_nilai_fisik) > 0))) return 'KIB D';
                if (spec?.lainnya_items?.some(l => l.lainnya_nama_barang || l.lainnya_judul || l.lainnya_pencipta || (parseFloat(l.lainnya_nilai_satuan || l.lainnya_nilai_fisik) > 0))) return 'KIB E';
                if (spec?.tanah_items?.some(t => t.tanah_nama_barang || t.tanah_luas_m2 || t.tanah_hak || t.tanah_sertifikat_no || (parseFloat(t.tanah_nilai_satuan || t.tanah_nilai_fisik) > 0))) return 'KIB A';

                // 2. Periksa kategori_kib dari spesifikasi_json
                const kibSpec = String(spec?.kategori_kib || '').toUpperCase();
                if (kibSpec.includes('KIB C') || kibSpec.includes('GEDUNG') || kibSpec.includes('BANGUNAN')) return 'KIB C';
                if (kibSpec.includes('KIB B') || kibSpec.includes('MESIN') || kibSpec.includes('PERALATAN')) return 'KIB B';
                if (kibSpec.includes('KIB A') || kibSpec.includes('TANAH')) return 'KIB A';
                if (kibSpec.includes('KIB D') || kibSpec.includes('JARINGAN') || kibSpec.includes('JALAN') || kibSpec.includes('IRIGASI')) return 'KIB D';
                if (kibSpec.includes('KIB E') || kibSpec.includes('LAINNYA') || kibSpec.includes('BUKU')) return 'KIB E';

                // 3. Periksa kode barang 108 / 1.5.2 Kemitraan secara presisi
                const kd = String(astap.kode_barang || astap.kode_108 || astap.jenis_astap?.sub_sub_rincian_objek || astap.jenis_astap?.sub_rincian_objek || astap.jenis_astap?.jenis || '').trim();
                if (kd.startsWith('1.3.1') || kd.startsWith('1.5.2.01.01.01.001') || kd.startsWith('1.5.2.01.01.02.001')) return 'KIB A';
                if (kd.startsWith('1.3.2') || kd.startsWith('1.5.2.01.01.01.002') || kd.startsWith('1.5.2.01.01.02.002')) return 'KIB B';
                if (kd.startsWith('1.3.3') || kd.startsWith('1.5.2.01.01.01.003') || kd.startsWith('1.5.2.01.01.02.003')) return 'KIB C';
                if (kd.startsWith('1.3.4') || kd.startsWith('1.5.2.01.01.01.004') || kd.startsWith('1.5.2.01.01.02.004')) return 'KIB D';
                if (kd.startsWith('1.3.5') || kd.startsWith('1.5.2.01.01.01.005') || kd.startsWith('1.5.2.01.01.02.005')) return 'KIB E';

                // 4. Periksa nama barang
                const nama = String(astap.nama_barang || astap.jenis_astap?.nama_jenis || '').toLowerCase();
                if (nama.includes('gedung') || nama.includes('bangunan')) return 'KIB C';
                if (nama.includes('tanah')) return 'KIB A';
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
                spec = spec || {};
                if (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) {
                    const valid = spec.tanah_items.filter(t => t.tanah_nama_barang || t.tanah_luas_m2 || t.tanah_hak || t.tanah_sertifikat_no || (parseFloat(t.tanah_nilai_satuan) > 0));
                    const toSync = valid.length > 0 ? valid : spec.tanah_items;
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.tanah_jumlah_bidang || it.tanah_jumlah_barang) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['tanah_jumlah_bidang', 'tanah_jumlah_barang']);
                }

                // Fallback jika tidak ada array tanah_items tapi ada luas_m2 / hak_tanah / sertifikat_no di spesifikasi_json
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

            // Helper kalkulasi nilai taksiran wajar per bidang tanah secara defensif (mencegah Rp 0)
            getTanahItemNilai(tItem, astap) {
                if (!tItem && !astap) return 0;
                const direct = Number(tItem?.tanah_nilai_satuan || tItem?.tanah_nilai_fisik || tItem?.nilai_satuan || tItem?.nilai_perolehan || 0);
                if (direct > 0) return direct;
                const items = this.getTanahItemsForDetail(astap);
                const count = Math.max(1, (items && items.length) ? items.length : 1);
                const parentTotal = Number(astap?.nilai_aset || astap?.total_realisasi || astap?.jumlah_realisasi || 0);
                return parentTotal > 0 ? Math.round(parentTotal / count) : 0;
            },

            getMesinItemsForDetail(astap) {
                if (!astap) return [];
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                if (spec && Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                    const valid = spec.mesin_items.filter(m => m.mesin_nama_barang || m.mesin_merk || m.mesin_type || m.mesin_no_pabrik || (parseFloat(m.mesin_nilai_satuan) > 0));
                    const toSync = valid.length > 0 ? valid : spec.mesin_items;
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.mesin_jumlah_barang) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
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
                if (spec && Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) {
                    const valid = spec.gedung_items.filter(g => g.gedung_nama_barang || g.gedung_nama_bangunan || g.gedung_luas_lantai || g.gedung_luas_lantai_m2 || g.gedung_luas_m2 || (parseFloat(g.gedung_nilai_satuan || g.gedung_nilai_fisik) > 0));
                    const toSync = valid.length > 0 ? valid : spec.gedung_items;
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.gedung_jumlah_bangunan) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['gedung_jumlah_bangunan']);
                }
                return [];
            },

            getJaringanItemsForDetail(astap) {
                if (!astap) return [];
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                if (spec && Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) {
                    const valid = spec.jaringan_items.filter(j => j.jaringan_nama_barang || j.jaringan_konstruksi || j.jaringan_panjang || (parseFloat(j.jaringan_nilai_satuan) > 0));
                    const toSync = valid.length > 0 ? valid : spec.jaringan_items;
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.jaringan_jumlah || it.jaringan_jumlah_barang) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['jaringan_jumlah', 'jaringan_jumlah_barang']);
                }
                return [];
            },

            getLainnyaItemsForDetail(astap) {
                if (!astap) return [];
                let spec = astap.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                if (spec && Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) {
                    const valid = spec.lainnya_items.filter(l => l.lainnya_nama_barang || l.lainnya_judul || l.lainnya_pencipta || (parseFloat(l.lainnya_nilai_satuan) > 0));
                    const toSync = valid.length > 0 ? valid : spec.lainnya_items;
                    const sumQty = toSync.reduce((s, it) => s + Math.max(1, parseInt(it.lainnya_jumlah || it.lainnya_jumlah_barang) || 1), 0);
                    const targetTotal = Math.max(
                        this.getActiveRegisters(astap).length,
                        parseInt(astap.jumlah_volume) || 0,
                        sumQty,
                        toSync.length
                    );
                    return this.syncRepeaterItemsWithVolume(toSync, targetTotal, ['lainnya_jumlah', 'lainnya_jumlah_barang']);
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
                    if (reg.is_deleted == 1 || reg.is_deleted === true || reg.is_deleted === '1') return false;
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
                    ruang_pemegang: reg.ruang_pemegang || (astap?.kemitraan?.mitra_nama ? ('Mitra: ' + astap.kemitraan.mitra_nama) : (astap?.mitra_nama ? ('Mitra: ' + astap.mitra_nama) : 'Dikelola Mitra Kemitraan')),
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
                            status_konsesi: this.statusForm.status_konsesi
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

            // =========================================================================
            // METODE EXTEND / PERPANJANG MASA KONSESI (ADENDUM PKS KEMITRAAN)
            // Helper parsing tanggal lokal YYYY-MM-DD tanpa offset timezone UTC
            parseLocalDate(dateStr) {
                if (!dateStr) return new Date();
                if (typeof dateStr === 'string' && dateStr.includes('-')) {
                    const parts = dateStr.split('T')[0].split('-').map(Number);
                    if (parts.length === 3) {
                        return new Date(parts[0], parts[1] - 1, parts[2]);
                    }
                }
                const d = new Date(dateStr);
                return isNaN(d.getTime()) ? new Date() : d;
            },

            formatLocalDate(d) {
                if (!d || isNaN(d.getTime())) return '';
                const year = d.getFullYear();
                const month = String(d.getMonth() + 1).padStart(2, '0');
                const day = String(d.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            },

            // =========================================================================
            // METODE EXTEND / PERPANJANG MASA KONSESI (ADENDUM PKS KEMITRAAN)
            // =========================================================================
            openExtendModal(row, astap, isDimanfaatkan, linkedCount = 0) {
                if (!row && !astap) return;
                const todayStr = this.formatLocalDate(new Date());
                const currentTglSelesai = row?.tanggal_selesai || astap?.tanggal_selesai || (row?.kemitraan?.tanggal_selesai) || null;
                
                // Hitung batas minimal tanggal selesai baru: harus lebih besar dari tanggal selesai saat ini (+1 hari)
                let minEndStr = '';
                if (currentTglSelesai) {
                    const minD = this.parseLocalDate(currentTglSelesai);
                    minD.setDate(minD.getDate() + 1);
                    minEndStr = this.formatLocalDate(minD);
                } else {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    minEndStr = this.formatLocalDate(tomorrow);
                }

                // Hitung default tanggal selesai baru (+1 tahun dari tgl selesai saat ini, atau +1 tahun dari hari ini)
                let defaultNewEnd = '';
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (currentTglSelesai) {
                    const d = this.parseLocalDate(currentTglSelesai);
                    if (d < today) {
                        const nextYear = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());
                        defaultNewEnd = this.formatLocalDate(nextYear);
                    } else {
                        d.setFullYear(d.getFullYear() + 1);
                        defaultNewEnd = this.formatLocalDate(d);
                    }
                } else {
                    const d = new Date(today.getFullYear() + 1, today.getMonth(), today.getDate());
                    defaultNewEnd = this.formatLocalDate(d);
                }

                // Pastikan defaultNewEnd tidak pernah di bawah minEndStr
                if (minEndStr && defaultNewEnd < minEndStr) {
                    defaultNewEnd = minEndStr;
                }

                // Resolusi Nama Barang (Deduplikasi jika nama berulang seperti "Sewa Aset Tetap, Sewa Aset Tetap")
                let rawNama = astap?.nama_barang || row?.nama_barang || 'Aset Kemitraan';
                let namaBarang = rawNama;
                if (typeof rawNama === 'string' && rawNama.includes(',')) {
                    const parts = rawNama.split(',').map(s => s.trim()).filter(Boolean);
                    namaBarang = [...new Set(parts)].join(', ');
                }
                let formatTglLama = currentTglSelesai ? this.formatTanggalIndo(currentTglSelesai) : '-';

                const targetId = row?.id || row?.kemitraan_id || astap?.kemitraan?.id || astap?.id || null;

                this.extendData = {
                    id: targetId,
                    astap_id: astap?.id || row?.astap_id || null,
                    nama_barang: namaBarang,
                    mitra_nama: row?.mitra_nama || 'Mitra Rekanan',
                    nomor_pks: row?.nomor_pks || '-',
                    status_konsesi: row?.status_konsesi || 'Aktif',
                    tanggal_mulai: row?.tanggal_mulai || null,
                    tanggal_selesai: currentTglSelesai,
                    tanggal_selesai_lama_fmt: formatTglLama,
                    nilai_aset: row?.nilai_aset || 0,
                    jumlah_extend: row?.jumlah_extend || 0,
                    is_dimanfaatkan: !!isDimanfaatkan,
                    linked_mitra_count: linkedCount || 0
                };

                this.extendForm = {
                    id: targetId,
                    nomor_adendum: '',
                    tanggal_adendum: todayStr,
                    tanggal_selesai_baru: defaultNewEnd,
                    nilai_aset_baru: row?.nilai_aset ? Number(row.nilai_aset).toLocaleString('id-ID') : '',
                    keterangan_adendum: '',
                    berkas_adendum: null
                };

                this.showModalExtend = true;
            },

            openExtendModalFromDetail() {
                if (!this.selectedAstapDetail) return;
                const detail = this.selectedAstapDetail;
                const linkedAsets = this.getAsetDitambahkanMitraForDetail(detail);
                const targetRow = (detail.raw_row && Object.keys(detail.raw_row).length > 0) ? detail.raw_row : (detail.kemitraan || detail);
                const targetAstap = (detail.raw_astap && Object.keys(detail.raw_astap).length > 0) ? detail.raw_astap : detail;
                // Tetap pertahankan showDetailModal tetap aktif agar saat modal extend ditutup, detail tetap muncul
                this.openExtendModal(targetRow, targetAstap, detail.is_dimanfaatkan, linkedAsets.length);
            },

            closeExtendModal() {
                this.showModalExtend = false;
                this.isSubmittingExtend = false;
            },

            // Hitung tanggal minimal yang boleh dipilih (harus di atas tanggal konsesi berakhir lama)
            getMinExtendEndDate() {
                if (!this.extendData?.tanggal_selesai) {
                    const tomorrow = new Date();
                    tomorrow.setDate(tomorrow.getDate() + 1);
                    return this.formatLocalDate(tomorrow);
                }
                const d = this.parseLocalDate(this.extendData.tanggal_selesai);
                // Tambah 1 hari dari tanggal selesai lama (tidak bisa memilih tanggal di bawah atau sama dengan tanggal selesai lama)
                d.setDate(d.getDate() + 1);
                return this.formatLocalDate(d);
            },

            // Validasi input tanggal konsesi baru secara realtime saat input berubah
            validateExtendEndDate() {
                if (!this.extendForm.tanggal_selesai_baru) return;
                const minDateStr = this.getMinExtendEndDate();
                if (minDateStr && this.extendForm.tanggal_selesai_baru < minDateStr) {
                    const minDateFmt = this.formatTanggalIndo(minDateStr);
                    const tglLamaFmt = this.extendData.tanggal_selesai_lama_fmt || (this.extendData.tanggal_selesai ? this.formatTanggalIndo(this.extendData.tanggal_selesai) : 'saat ini');
                    const msg = `Tanggal berakhir konsesi baru tidak boleh sama atau di bawah tanggal berakhir konsesi saat ini (${tglLamaFmt}). Minimal tanggal perpanjangan adalah ${minDateFmt}.`;
                    if (typeof this.showToast === 'function') {
                        this.showToast('Tanggal Tidak Valid', msg, 'danger');
                    } else {
                        alert(msg);
                    }
                    this.extendForm.tanggal_selesai_baru = minDateStr;
                }
            },

            setExtendDuration(amount, unit) {
                let base = this.parseLocalDate(this.extendData.tanggal_selesai);
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                // Jika tanggal selesai lama sudah lewat jauh dari hari ini (sudah expired), hitung dari hari ini
                if (base < today) {
                    base = new Date();
                }
                const newD = new Date(base.getFullYear(), base.getMonth(), base.getDate());
                if (unit === 'months') {
                    newD.setMonth(newD.getMonth() + amount);
                } else if (unit === 'years') {
                    newD.setFullYear(newD.getFullYear() + amount);
                }
                let resDateStr = this.formatLocalDate(newD);
                const minDateStr = this.getMinExtendEndDate();
                if (minDateStr && resDateStr < minDateStr) {
                    resDateStr = minDateStr;
                }
                this.extendForm.tanggal_selesai_baru = resDateStr;
            },

            formatExtendNilai(e) {
                let val = (e.target.value || '').replace(/[^0-9]/g, '');
                if (!val) {
                    this.extendForm.nilai_aset_baru = '';
                    return;
                }
                this.extendForm.nilai_aset_baru = Number(val).toLocaleString('id-ID');
            },

            handleExtendFileChange(event) {
                if (event.target && event.target.files && event.target.files[0]) {
                    this.extendForm.berkas_adendum = event.target.files[0];
                } else {
                    this.extendForm.berkas_adendum = null;
                }
            },

            async submitExtendForm() {
                if (!this.extendForm.id || !this.extendForm.nomor_adendum || !this.extendForm.tanggal_selesai_baru) {
                    alert('Harap isi Nomor Adendum dan Tanggal Selesai Baru.');
                    return;
                }

                // Validasi batas minimal tanggal perpanjangan
                const minDateStr = this.getMinExtendEndDate();
                if (minDateStr && this.extendForm.tanggal_selesai_baru < minDateStr) {
                    const minDateFmt = this.formatTanggalIndo(minDateStr);
                    const tglLamaFmt = this.extendData.tanggal_selesai_lama_fmt || (this.extendData.tanggal_selesai ? this.formatTanggalIndo(this.extendData.tanggal_selesai) : 'saat ini');
                    const msg = `Tanggal berakhir konsesi baru tidak boleh sama atau di bawah tanggal berakhir konsesi saat ini (${tglLamaFmt}). Minimal tanggal perpanjangan adalah ${minDateFmt}.`;
                    if (typeof this.showToast === 'function') {
                        this.showToast('Tanggal Tidak Valid', msg, 'danger');
                    } else {
                        alert(msg);
                    }
                    this.extendForm.tanggal_selesai_baru = minDateStr;
                    return;
                }

                this.isSubmittingExtend = true;
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const url = `{{ url('/master-data/kemitraan') }}/${this.extendForm.id}/extend`;

                    const fd = new FormData();
                    fd.append('nomor_adendum', this.extendForm.nomor_adendum);
                    fd.append('tanggal_adendum', this.extendForm.tanggal_adendum);
                    fd.append('tanggal_selesai_baru', this.extendForm.tanggal_selesai_baru);
                    if (this.extendForm.nilai_aset_baru) {
                        fd.append('nilai_aset_baru', this.extendForm.nilai_aset_baru);
                    }
                    if (this.extendForm.keterangan_adendum) {
                        fd.append('keterangan_adendum', this.extendForm.keterangan_adendum);
                    }
                    if (this.extendForm.berkas_adendum) {
                        fd.append('berkas_adendum', this.extendForm.berkas_adendum);
                    }

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: fd
                    });

                    const json = await res.json();
                    if (json.success) {
                        this.closeExtendModal();

                        // Cek apakah ada linked aset mitra (Tabel 2)
                        if (json.has_linked_mitra && json.linked_mitra_assets && json.linked_mitra_assets.length > 0) {
                            this.extendMitraState = {
                                nomor_adendum: json.nomor_adendum,
                                tanggal_selesai_baru: json.tanggal_selesai_baru,
                                tanggal_selesai_fmt: json.tanggal_selesai_fmt,
                                linked_mitra_assets: json.linked_mitra_assets
                            };
                            this.selectedMitraIds = json.linked_mitra_assets.map(m => m.id);
                            this.extendMitraKeterangan = `Ikut perpanjangan masa pemanfaatan PKS Adendum No. ${json.nomor_adendum}`;
                            this.showModalConfirmExtendMitra = true;
                        } else {
                            if (typeof this.showToast === 'function') {
                                this.showToast(json.message, 'success');
                            } else {
                                alert(json.message);
                            }
                            setTimeout(() => window.location.reload(), 1200);
                        }
                    } else {
                        alert(json.message || 'Gagal menyimpan perpanjangan konsesi.');
                    }
                } catch (err) {
                    console.error('Extend error:', err);
                    alert('Terjadi kesalahan saat memproses perpanjangan konsesi.');
                } finally {
                    this.isSubmittingExtend = false;
                }
            },

            selectAllMitra() {
                this.selectedMitraIds = (this.extendMitraState.linked_mitra_assets || []).map(m => m.id);
            },

            deselectAllMitra() {
                this.selectedMitraIds = [];
            },

            closeConfirmExtendMitraModal() {
                this.showModalConfirmExtendMitra = false;
                this.isSubmittingExtendMitra = false;
                if (typeof this.showToast === 'function') {
                    this.showToast('Objek pemanfaatan berhasil diperpanjang. Aset mitra tetap pada masa konsesi sebelumnya.', 'info');
                }
                setTimeout(() => window.location.reload(), 1200);
            },

            async submitExtendMitraBatch() {
                if (this.selectedMitraIds.length === 0) {
                    alert('Pilih setidaknya satu aset mitra untuk diperpanjang.');
                    return;
                }

                this.isSubmittingExtendMitra = true;
                try {
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const url = `{{ route('master.kemitraan.extend_mitra_batch') }}`;

                    const res = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            mitra_ids: this.selectedMitraIds,
                            tanggal_selesai_baru: this.extendMitraState.tanggal_selesai_baru,
                            nomor_adendum: this.extendMitraState.nomor_adendum,
                            tanggal_adendum: this.extendMitraState.tanggal_adendum || null,
                            keterangan: this.extendMitraKeterangan
                        })
                    });

                    const json = await res.json();
                    if (json.success) {
                        this.showModalConfirmExtendMitra = false;
                        if (typeof this.showToast === 'function') {
                            this.showToast(json.message, 'success');
                        } else {
                            alert(json.message);
                        }
                        setTimeout(() => window.location.reload(), 1200);
                    } else {
                        alert(json.message || 'Gagal memperpanjang aset mitra.');
                    }
                } catch (err) {
                    console.error('Batch extend mitra error:', err);
                    alert('Terjadi kesalahan jaringan atau server saat memperpanjang aset mitra.');
                } finally {
                    this.isSubmittingExtendMitra = false;
                }
            },

            // Konfirmasi Penghapusan Normal
            confirmDelete(id, nama) {
                this.deleteItem = { id: id, nama: nama || 'Aset Kemitraan', alasan: '' };
                this.showDeleteModal = true;
            },

            // Konfirmasi Penghapusan Khusus Objek Dimanfaatkan (Opsi 1: Proteksi Ketat Terhadap Aset Mitra)
            confirmDeleteDimanfaatkan(id, nama, linkedAsets, mitraNama, nomorPks) {
                if (linkedAsets && Array.isArray(linkedAsets) && linkedAsets.length > 0) {
                    const count = linkedAsets.length;
                    const totalNilai = linkedAsets.reduce((sum, it) => sum + (it.nilai || 0), 0);
                    const totalNilaiFmt = 'Rp ' + Number(totalNilai).toLocaleString('id-ID');
                    const mitraStr = mitraNama || 'Mitra Rekanan';
                    const daftarNama = linkedAsets.map(a => `• ${a.nama} (${a.nilai_fmt})`).slice(0, 3).join('\n') + (linkedAsets.length > 3 ? `\n• ...dan ${linkedAsets.length - 3} aset mitra lainnya` : '');

                    this.askConfirmation({
                        title: '🛡️ Objek Pemanfaatan Tidak Dapat Dihapus Langsung',
                        message: `Objek pemanfaatan "${nama}" saat ini belum dapat dihapus karena masih menampung ${count} barang inventaris/aset yang ditambahkan oleh mitra rekanan (${mitraStr}).\n\nDemi ketertiban akuntabilitas dan pencegahan kehilangan catatan aset RSUD Koesnandi, seluruh aset yang ditambahkan mitra di dalam objek ini harus dihapus / dibatalkan terlebih dahulu di Tabel 2 (Aset Ditambahkan Mitra).`,
                        itemName: `${nama} (${mitraStr})`,
                        itemDetails: {
                            nama: nama,
                            badgeText: `${count} ASET MITRA AKTIF`,
                            totalAset: count,
                            nilaiFmt: totalNilaiFmt
                        },
                        type: 'danger',
                        isBlocked: true,
                        actionText: '🔍 Buka & Hapus Aset Mitra Dulu',
                        assetWarning: `Daftar Aset Mitra di Dalam Objek Ini:\n${daftarNama}\n\n💡 Petunjuk: Bersihkan / hapus aset mitra di atas terlebih dahulu pada Tabel "Aset Ditambahkan Mitra" sebelum menghapus objek pemanfaatan ini.`,
                        onActionClick: () => {
                            this.showConfirmModal = false;
                            this.kemitraanTableTab = 'ditambahkan';
                            if (nomorPks && nomorPks !== '-' && nomorPks !== 'Belum Ada PKS') {
                                this.filterSearch = nomorPks;
                            } else if (mitraStr && mitraStr !== 'Mitra Rekanan' && mitraStr !== '-') {
                                this.filterSearch = mitraStr;
                            }
                            this.showToast(`Menampilkan aset mitra terkait (${mitraStr}). Silakan hapus aset mitra terlebih dahulu.`, 'info');
                            setTimeout(() => {
                                const el = document.getElementById('table-kemitraan-ditambahkan');
                                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            }, 300);
                        }
                    });
                    return;
                }

                // Jika tidak ada aset yang ditambahkan mitra, lanjutkan konfirmasi hapus biasa
                this.confirmDelete(id, nama);
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
                        body: JSON.stringify({})
                    });

                    const json = await res.json();
                    if (json.success) {
                        this.showDeleteModal = false;
                        this.showToast(json.message || 'Data Aset Kemitraan berhasil dipindahkan ke Pusat Pemulihan Data (Recycle Bin).', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 900);
                    } else if (json.is_blocked) {
                        this.showDeleteModal = false;
                        this.askConfirmation({
                            title: '🛡️ Objek Pemanfaatan Tidak Dapat Dihapus',
                            message: json.message,
                            itemName: this.deleteItem.nama || 'Aset Kemitraan',
                            type: 'danger',
                            isBlocked: true,
                            actionText: '🔍 Buka Tabel Aset Mitra',
                            onActionClick: () => {
                                this.showConfirmModal = false;
                                this.kemitraanTableTab = 'ditambahkan';
                                setTimeout(() => {
                                    const el = document.getElementById('table-kemitraan-ditambahkan');
                                    if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                                }, 300);
                            }
                        });
                    } else {
                        this.showToast(json.message || 'Gagal menghapus data.', 'error');
                    }
                } catch (err) {
                    console.error('Delete error:', err);
                    this.showToast('Terjadi kesalahan jaringan atau server saat menghapus data.', 'error');
                } finally {
                    this.isDeleting = false;
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
                    onActionClick: opts.onActionClick || null,
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
                if (it.jenis_reklas === 'EKSTRAKOMPTABEL' || it.tujuan_kib === 'EKSTRAKOMPTABEL') return true;
                const cat = (it.category || '').toString().trim().toUpperCase();
                return cat === 'EXTRACOM';
            },

            isKemitraanAktif() {
                const it = this.selectedAstapReklas;
                if (!it) return false;
                const cat = (it.category || '').toString().trim().toUpperCase();
                const kode = (it.kode_barang || it.kode_108 || it.jenis_aset_kode || '').toString().trim();
                const isKemitraan = cat === 'KEMITRAAN' || kode.startsWith('1.5.2') || !!it.kemitraan;
                if (!isKemitraan) return false;

                const rawStatus = (it.status_konsesi || it.kemitraan?.status_konsesi || '').toString().trim();
                if (!rawStatus) return true;

                const endedStatuses = [
                    'konsesi berakhir',
                    'selesai / reklasifikasi',
                    'selesai',
                    'dihentikan',
                    'berakhir',
                    'putus kontrak'
                ];
                return !endedStatuses.includes(rawStatus.toLowerCase());
            },

            isReklasExtracomDisabled() {
                if (!this.selectedAstapReklas) return false;
                if (this.isKemitraanAktif()) return true;
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
                if (this.isKemitraanAktif()) return true;
                return !this.isCurrentAstapKdp();
            },

            isCurrentAstapMutasiKeluarFull() {
                if (!this.selectedAstapReklas) return false;
                const it = this.selectedAstapReklas;
                if (it.jenis_reklas === 'MUTASI_EKSTERNAL' || it.is_mutasi_keluar_full) return true;
                if (it.mutasi_keluar && !it.is_mutasi_keluar_partial) return true;
                if (it.registers && Array.isArray(it.registers) && it.registers.length > 0) {
                    const activeCount = it.registers.filter(r => r.status !== 'Mutasi Keluar OPD').length;
                    if (activeCount === 0) return true;
                }
                return false;
            },

            selectSkpdTujuan(skpd) {
                if (!skpd) return;
                this.reklasSkpdTujuan = typeof skpd === 'string' ? skpd : (skpd.nama || skpd.skpd || '');
                this.isSkpdDropdownOpen = false;
            },

            selectPejabatTujuan(p) {
                if (!p) return;
                this.reklasPejabatOpdTujuan = p.nama || '';
                this.reklasNipPejabatOpdTujuan = p.nip || '';
                if (p.jabatan) this.reklasJabatanOpdTujuan = p.jabatan;
                this.isPejabatTujuanDropdownOpen = false;
            },

            selectPejabatAsal(p) {
                if (!p) return;
                this.reklasPjAsalNama = p.nama || '';
                this.reklasPjAsalNip = p.nip || '';
                this.reklasPjAsalJabatan = p.jabatan || '';
                this.isPejabatAsalDropdownOpen = false;
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

            clearDokumenFile() {
                this.selectedDokumenFile = null;
                const el = document.getElementById('astap_reklas_dokumen_file_input');
                if (el) el.value = '';
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

            reklasKemitraanTipeFisik: 'mesin',

            getReklasKemitraanPhysicalType() {
                return this.detectKemitraanPhysicalType();
            },

            getSourcePhysicalType(item) {
                const it = item || this.selectedAstapReklas;
                if (!it) return null;
                const effKib = (typeof this.getEffectiveKibCategory === 'function') ? this.getEffectiveKibCategory(it) : (it.category || '');
                if (effKib === 'KIB C') return 'gedung';
                if (effKib === 'KIB B') return 'mesin';
                if (effKib === 'KIB A') return 'tanah';
                if (effKib === 'KIB D') return 'jaringan';
                if (effKib === 'KIB E') return 'lainnya';
                if (effKib === 'ATB') return 'atb';
                return this.detectKemitraanPhysicalType(it);
            },

            getTargetPhysicalType() {
                if (!this.reklasTujuanKib) return null;
                if (this.reklasTujuanKib === 'KEMITRAAN') {
                    return this.getReklasKemitraanPhysicalType();
                }
                if (this.reklasTujuanKib === 'KIB A') return 'tanah';
                if (this.reklasTujuanKib === 'KIB B') return 'mesin';
                if (this.reklasTujuanKib === 'KIB C') return 'gedung';
                if (this.reklasTujuanKib === 'KIB D') return 'jaringan';
                if (this.reklasTujuanKib === 'KIB E') return 'lainnya';
                if (this.reklasTujuanKib === 'ATB') return 'atb';
                if (this.reklasTujuanKib === 'ASET LAIN' || this.reklasTujuanKib === 'ASET LAIN-LAIN') return 'aset_lain';
                return null;
            },

            isReklasSamePhysicalType() {
                const src = this.getSourcePhysicalType();
                const tgt = this.getTargetPhysicalType();
                return Boolean(src && tgt && src === tgt);
            },

            getPhysicalTypeLabel(type) {
                if (!type) return '';
                const map = {
                    'tanah': 'Tanah (KIB A)',
                    'mesin': 'Peralatan & Mesin (KIB B)',
                    'gedung': 'Gedung & Bangunan (KIB C)',
                    'jaringan': 'Jalan, Irigasi & Jaringan (KIB D)',
                    'lainnya': 'Aset Tetap Lainnya (KIB E)',
                    'atb': 'Aset Tak Berwujud (ATB)',
                    'aset_lain': 'Aset Lain-Lain'
                };
                return map[type] || String(type).toUpperCase();
            },

            detectKemitraanPhysicalType(item) {
                const it = item || this.selectedAstapReklas;
                const kem = it?.kemitraan || {};

                // 1. PRIORITAS TERTINGGI: Periksa spesifikasi_json (kategori_kib & repeater items)
                let spec = it?.spesifikasi_json;
                if (typeof spec === 'string') {
                    try { spec = JSON.parse(spec); } catch(e) { spec = {}; }
                }
                spec = (spec && typeof spec === 'object') ? spec : {};

                const kibSpec = String(spec.kategori_kib || '').toUpperCase();
                if (kibSpec.includes('KIB C') || kibSpec.includes('GEDUNG') || kibSpec.includes('BANGUNAN')) return 'gedung';
                if (kibSpec.includes('KIB B') || kibSpec.includes('MESIN') || kibSpec.includes('PERALATAN')) return 'mesin';
                if (kibSpec.includes('KIB A') || kibSpec.includes('TANAH')) return 'tanah';
                if (kibSpec.includes('KIB D') || kibSpec.includes('JARINGAN') || kibSpec.includes('JALAN') || kibSpec.includes('IRIGASI')) return 'jaringan';
                if (kibSpec.includes('KIB E') || kibSpec.includes('LAINNYA') || kibSpec.includes('BUKU')) return 'lainnya';

                if (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0 && spec.gedung_items.some(g => g.gedung_nama_barang || g.gedung_luas_m2 || g.gedung_luas_lantai || g.gedung_bertingkat || (parseFloat(g.gedung_nilai_satuan) > 0))) return 'gedung';
                if (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0 && spec.mesin_items.some(m => m.mesin_nama_barang || m.mesin_merk || m.mesin_type || m.mesin_no_pabrik || (parseFloat(m.mesin_nilai_satuan) > 0))) return 'mesin';
                if (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0 && spec.tanah_items.some(t => t.tanah_nama_barang || t.tanah_luas_m2 || t.tanah_hak || t.tanah_sertifikat_no || (parseFloat(t.tanah_nilai_satuan) > 0))) return 'tanah';
                if (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0 && spec.jaringan_items.some(j => j.jaringan_nama_barang || j.jaringan_konstruksi || (parseFloat(j.jaringan_nilai_satuan) > 0))) return 'jaringan';
                if (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0 && spec.lainnya_items.some(l => l.lainnya_nama_barang || l.lainnya_judul || (parseFloat(l.lainnya_nilai_satuan) > 0))) return 'lainnya';

                // 2. PRIORITAS KEDUA: Sub-Sub 108 / Sub-Rincian yang aktif dipilih di modal
                const subSubKd = this.reklasSubSubRincianKode || '';
                const subSubNm = (this.reklasSubSubRincianNama || '').toLowerCase();
                if (subSubKd) {
                    if (subSubKd.endsWith('.001') || subSubKd.endsWith('.01') || subSubKd.includes('.01.01.001') || subSubKd.startsWith('1.3.1')) return 'tanah';
                    if (subSubKd.endsWith('.003') || subSubKd.endsWith('.03') || subSubKd.includes('.01.01.003') || subSubKd.startsWith('1.3.3')) return 'gedung';
                    if (subSubKd.endsWith('.004') || subSubKd.endsWith('.04') || subSubKd.includes('.01.01.004') || subSubKd.startsWith('1.3.4')) return 'jaringan';
                    if (subSubKd.endsWith('.005') || subSubKd.endsWith('.05') || subSubKd.includes('.01.01.005') || subSubKd.startsWith('1.3.5')) return 'lainnya';
                    if (subSubKd.endsWith('.002') || subSubKd.endsWith('.02') || subSubKd.includes('.01.01.002') || subSubKd.startsWith('1.3.2')) return 'mesin';
                }
                if (subSubNm) {
                    if (subSubNm.includes('tanah') || subSubNm.includes('lahan')) return 'tanah';
                    if (subSubNm.includes('gedung') || subSubNm.includes('bangunan')) return 'gedung';
                    if (subSubNm.includes('jalan') || subSubNm.includes('jaringan') || subSubNm.includes('irigasi') || subSubNm.includes('pipa')) return 'jaringan';
                    if (subSubNm.includes('lainnya') || subSubNm.includes('buku') || subSubNm.includes('seni')) return 'lainnya';
                    if (subSubNm.includes('peralatan') || subSubNm.includes('mesin') || subSubNm.includes('alat') || subSubNm.includes('kendaraan') || subSubNm.includes('alkes')) return 'mesin';
                }

                // 3. PRIORITAS KETIGA: Kode 108 Aset Asal / Sub-Sub Rincian Akun Kemitraan (1.5.2)
                const asalKode = String(it?.kode_barang || it?.kode_108 || kem?.kode_108 || it?.jenis_aset_kode || it?.jenis_astap?.sub_sub_rincian_objek || '').trim();
                if (asalKode) {
                    if (asalKode.endsWith('.001') || asalKode.includes('.01.01.01.001') || asalKode.includes('.01.01.001') || asalKode.startsWith('1.3.1')) return 'tanah';
                    if (asalKode.endsWith('.003') || asalKode.includes('.01.01.01.003') || asalKode.includes('.01.01.003') || asalKode.startsWith('1.3.3')) return 'gedung';
                    if (asalKode.endsWith('.004') || asalKode.includes('.01.01.01.004') || asalKode.includes('.01.01.004') || asalKode.startsWith('1.3.4')) return 'jaringan';
                    if (asalKode.endsWith('.005') || asalKode.includes('.01.01.01.005') || asalKode.includes('.01.01.005') || asalKode.startsWith('1.3.5')) return 'lainnya';
                    if (asalKode.endsWith('.002') || asalKode.includes('.01.01.01.002') || asalKode.includes('.01.01.002') || asalKode.startsWith('1.3.2')) return 'mesin';
                }

                // 4. PRIORITAS KEEMPAT: Uraian Sub-Sub Rincian 108 Aset Asal
                const uraianSub = String(it?.jenis_astap?.uraian_sub_sub_rincian || it?.jenis_astap?.uraian_sub_rincian || kem?.jenis_astap?.uraian_sub_sub_rincian || '').toLowerCase();
                if (uraianSub.includes('tanah')) return 'tanah';
                if (uraianSub.includes('gedung') || uraianSub.includes('bangunan')) return 'gedung';
                if (uraianSub.includes('jalan') || uraianSub.includes('jaringan') || uraianSub.includes('irigasi')) return 'jaringan';
                if (uraianSub.includes('lainnya') || uraianSub.includes('buku') || uraianSub.includes('seni')) return 'lainnya';
                if (uraianSub.includes('mesin') || uraianSub.includes('peralatan') || uraianSub.includes('alat')) return 'mesin';

                // 5. PRIORITAS KELIMA: Satuan Barang
                const satuan = String(it?.satuan || kem?.satuan || '').toLowerCase().trim();
                if (satuan.includes('gedung') || satuan.includes('bangunan') || satuan.includes('ruang') || satuan.includes('lantai')) return 'gedung';
                if (satuan.includes('bidang') || satuan.includes('persil') || satuan.includes('hektar') || satuan.includes('are')) return 'tanah';
                if (satuan.includes('km') || satuan.includes('meter') || satuan.includes('ruas')) return 'jaringan';

                // 6. PRIORITAS KEENAM: Nama Barang / Keterangan
                const asalNama = String(it?.nama_barang || kem?.keterangan || '').toLowerCase();
                if (asalNama.includes('tanah') || asalNama.includes('lahan') || asalNama.includes('kavling')) return 'tanah';
                if (asalNama.includes('gedung') || asalNama.includes('bangunan') || asalNama.includes('rumah potong') || asalNama.includes('ruang') || asalNama.includes('paviliun') || asalNama.includes('rumah') || asalNama.includes('gudang') || asalNama.includes('instalasi') || asalNama.includes('kantor') || asalNama.includes('aula') || asalNama.includes('rph')) return 'gedung';
                if (asalNama.includes('jalan') || asalNama.includes('irigasi') || asalNama.includes('jaringan') || asalNama.includes('pipa') || asalNama.includes('saluran') || asalNama.includes('kabel') || asalNama.includes('drainase') || asalNama.includes('aspal')) return 'jaringan';
                if (asalNama.includes('buku') || asalNama.includes('seni') || asalNama.includes('hewan') || asalNama.includes('tanaman') || asalNama.includes('software') || asalNama.includes('aplikasi')) return 'lainnya';
                if (asalNama.includes('mesin') || asalNama.includes('alat') || asalNama.includes('kendaraan') || asalNama.includes('peralatan') || asalNama.includes('alkes') || asalNama.includes('analyzer') || asalNama.includes('centrifuge') || asalNama.includes('ventilator') || asalNama.includes('komputer') || asalNama.includes('printer') || asalNama.includes('server') || asalNama.includes('genset') || asalNama.includes('pompa')) return 'mesin';

                // 7. PRIORITAS KETUJUH: Atribut spesifikasi individu
                if (spec.konstruksi_bertingkat || spec.luas_lantai_m2 || spec.luas_m2_gedung || spec.bertingkat || spec.beton) return 'gedung';
                if (spec.merk || spec.type || spec.no_pabrik || spec.no_rangka || spec.no_mesin || spec.ukuran_cc) return 'mesin';
                if (spec.luas_m2 || spec.hak_tanah || spec.sertifikat_no || spec.tanah_hak) return 'tanah';
                if (spec.panjang_km || spec.lebar_m) return 'jaringan';

                // 8. Asal KIB jika ada
                const asalKib = String(it?.asal_kib || kem?.asal_kib || '').toUpperCase().trim();
                if (asalKib === 'KIB C') return 'gedung';
                if (asalKib === 'KIB B') return 'mesin';
                if (asalKib === 'KIB A') return 'tanah';
                if (asalKib === 'KIB D') return 'jaringan';
                if (asalKib === 'KIB E') return 'lainnya';

                return 'mesin';
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
                    this.syncKemitraanSkema(s.kode, s.nama);
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
                    this.syncKemitraanSkema();
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
                    this.syncKemitraanSkema(item.kode, item.nama);
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

            onReklasTujuanKibChange() {
                if (this.isKemitraanAktif() && this.reklasTujuanKib !== 'KEMITRAAN') {
                    this.reklasTujuanKib = 'KEMITRAAN';
                    if (typeof this.showToast === 'function') {
                        this.showToast('Status kemitraan masih aktif: tujuan reklasifikasi terkunci pada Akun 1.5.2 (Kemitraan).', 'warning');
                    }
                }
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
                } else if (this.reklasTujuanKib === 'KIB C') {
                    this.reklasKemitraanTipeFisik = 'gedung';
                } else if (this.reklasTujuanKib === 'KIB B') {
                    this.reklasKemitraanTipeFisik = 'mesin';
                } else if (this.reklasTujuanKib === 'KIB A') {
                    this.reklasKemitraanTipeFisik = 'tanah';
                } else if (this.reklasTujuanKib === 'KIB D') {
                    this.reklasKemitraanTipeFisik = 'jaringan';
                } else if (this.reklasTujuanKib === 'KIB E') {
                    this.reklasKemitraanTipeFisik = 'lainnya';
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
                const pType = (typeof this.detectKemitraanPhysicalType === 'function') ? this.detectKemitraanPhysicalType(it) : '';
                if (pType === 'gedung' && gItems.length > 0) existingItems = gItems;
                else if (pType === 'mesin' && mItems.length > 0) existingItems = mItems;
                else if (pType === 'tanah' && tItems.length > 0) existingItems = tItems;
                else if (pType === 'jaringan' && jItems.length > 0) existingItems = jItems;
                else if (pType === 'lainnya' && lItems.length > 0) existingItems = lItems;
                else if (pType === 'atb' && aItems.length > 0) existingItems = aItems;
                else if (gItems.length > 0) existingItems = gItems;
                else if (mItems.length > 0) existingItems = mItems;
                else if (tItems.length > 0) existingItems = tItems;
                else if (jItems.length > 0) existingItems = jItems;
                else if (lItems.length > 0) existingItems = lItems;
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
                        tanah_asal_usul: tItem.tanah_asal_usul || spec.tanah_asal_usul || spec.asal_usul || (it.asal_usul && it.asal_usul !== '-' ? it.asal_usul : 'Pengadaan APBD / BLUD'),
                        tanah_alamat: tItem.tanah_alamat || spec.tanah_alamat || spec.alamat || it.alamat_barang || defaultAlamat,

                        // KIB B - Mesin
                        mesin_merk: mItem.mesin_merk || spec.merk || it.merk || (it.merk_type && it.merk_type !== '-' ? it.merk_type : '') || '',
                        mesin_type: mItem.mesin_type || spec.type || it.type || '',
                        mesin_no_pabrik: mItem.mesin_nomor_pabrik || mItem.mesin_no_pabrik || spec.no_pabrik || spec.mesin_no_pabrik || (it.no_pabrik && it.no_pabrik !== '-' ? it.no_pabrik : '') || '',
                        mesin_ukuran_cc: mItem.mesin_ukuran || mItem.mesin_ukuran_cc || spec.ukuran_cc || spec.ukuran || (it.ukuran && it.ukuran !== '-' ? it.ukuran : '') || '',
                        mesin_bahan: mItem.mesin_bahan || spec.bahan || (it.bahan && it.bahan !== '-' ? it.bahan : 'Logam / Komponen Elektronik'),
                        mesin_no_polisi: mItem.mesin_nomor_polisi || mItem.mesin_no_polisi || spec.no_polisi || (it.no_polisi && it.no_polisi !== '-' ? it.no_polisi : '') || '',

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
                        gedung_dokumen_nomor: gItem.gedung_dokumen_no || gItem.gedung_dokumen_nomor || gItem.dokumen_nomor || spec.gedung_dokumen_no || spec.dokumen_nomor || it.spk_nomor || '',
                        gedung_dokumen_tgl: (() => {
                            let t = gItem.gedung_dokumen_tgl || spec.gedung_dokumen_tgl || spec.dokumen_tgl || it.spk_tanggal || '';
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
                if (yearMatch) {
                    totalMonths += parseInt(yearMatch[1], 10) * 12;
                    matched = true;
                }

                const monthMatch = str.match(/(\d+)\s*(?:bulan|bln|bl|mo|month|months)\b/);
                if (monthMatch) {
                    totalMonths += parseInt(monthMatch[1], 10);
                    matched = true;
                }

                const dayMatch = str.match(/(\d+)\s*(?:hari|hr|d|day|days)\b/);
                if (dayMatch) {
                    totalDays += parseInt(dayMatch[1], 10);
                    matched = true;
                }

                if (!matched) {
                    const plainNumMatch = str.match(/^(\d+)$/);
                    if (plainNumMatch) {
                        const num = parseInt(plainNumMatch[1], 10);
                        if (num > 0) {
                            if (num <= 30) {
                                totalMonths += num * 12;
                            } else {
                                totalDays += num;
                            }
                            matched = true;
                        }
                    }
                }

                if (!matched || (totalMonths === 0 && totalDays === 0)) return null;

                return {
                    years: Math.floor(totalMonths / 12),
                    months: totalMonths % 12,
                    days: totalDays
                };
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

                if (durationObj.years > 0) {
                    d.setFullYear(d.getFullYear() + durationObj.years);
                }
                if (durationObj.months > 0) {
                    d.setMonth(d.getMonth() + durationObj.months);
                }
                if (durationObj.days > 0) {
                    d.setDate(d.getDate() + durationObj.days);
                }

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
                if (days > 0 && years === 0) parts.push(`${days} Hari`);

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
                if (durText) {
                    this.reklasSpekBaru.kemitraan_jangka_waktu = durText;
                    this.syncCurrentSpekToActiveItem();
                }
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
                if (newEnd) {
                    this.reklasSpekBaru.kemitraan_tanggal_selesai = newEnd;
                    this.syncCurrentSpekToActiveItem();
                }
            },

            onReklasKemitraanTanggalMulaiChange() {
                const durText = this.reklasSpekBaru?.kemitraan_jangka_waktu;
                const parsed = durText ? this.parseKemitraanDuration(durText) : null;
                const tM = this.reklasSpekBaru?.kemitraan_tanggal_mulai;
                if (parsed && tM) {
                    const newEnd = this.computeKemitraanEndDate(tM, parsed);
                    if (newEnd) {
                        this.reklasSpekBaru.kemitraan_tanggal_selesai = newEnd;
                        this.syncCurrentSpekToActiveItem();
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
                if (newEnd) {
                    this.reklasSpekBaru.kemitraan_tanggal_selesai = newEnd;
                    this.syncCurrentSpekToActiveItem();
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
                item.status_konsesi = kemitraanRow?.status_konsesi || item.kemitraan?.status_konsesi || item.status_konsesi || 'Aktif';

                if (item.status_konsesi === 'Dikembalikan ke Rekanan' || item.status_konsesi === 'Dibatalkan') {
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            icon: 'warning',
                            title: item.status_konsesi === 'Dibatalkan' ? 'Kemitraan Dibatalkan' : 'Aset Telah Dikembalikan',
                            text: item.status_konsesi === 'Dibatalkan'
                                ? 'Status kerja sama kemitraan aset ini telah dibatalkan, sehingga aset terkunci dari reklasifikasi.'
                                : 'Aset ini telah dikembalikan ke pihak rekanan dan bukan lagi objek aset di RSUD, sehingga tidak dapat direklasifikasi.',
                            confirmButtonColor: '#06b6d4',
                            background: '#0f172a',
                            color: '#f8fafc',
                        });
                    } else {
                        alert(item.status_konsesi === 'Dibatalkan' ? 'Status kemitraan dibatalkan dan tidak dapat direklasifikasi.' : 'Aset ini telah dikembalikan ke pihak rekanan dan tidak dapat direklasifikasi.');
                    }
                    return;
                }

                item.total_realisasi_num = parseFloat(kemitraanRow?.nilai_aset || item.total_realisasi || 0);
                item.jumlah_realisasi = 'Rp ' + Number(item.total_realisasi_num).toLocaleString('id-ID');
                item.jumlah_volume = parseInt(kemitraanRow?.jumlah_volume || item.jumlah_volume) || 1;
                item.satuan = kemitraanRow?.satuan || item.satuan || 'Unit';
                item.bast_dokumen_nomor = kemitraanRow?.nomor_pks || item.bast_dokumen_nomor || '';
                item.spk_nomor = kemitraanRow?.nomor_pks || item.spk_nomor || '';
                item.tahun_perolehan = kemitraanRow?.tahun || item.tahun_perolehan || new Date().getFullYear();

                this.selectedAstapReklas = item;

                // Reset field pemilihan rincian rekening terlebih dahulu agar tidak membaca sisa modal sebelumnya
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

                const isActive = this.isKemitraanAktif();
                this.reklasJenis = 'pindah_kib';

                // Otomatisasi KIB Tujuan & Tipe Fisik Berdasarkan Karakteristik Aset Kemitraan:
                // Bangunan / Gedung -> otomatis KIB C (Gedung & Bangunan)
                // Mesin / Alat / Alkes -> otomatis KIB B (Peralatan & Mesin)
                // Tanah -> otomatis KIB A (Tanah)
                // Jalan / Jaringan -> otomatis KIB D (Jalan, Jaringan & Irigasi)
                // Lainnya -> otomatis KIB E (Aset Tetap Lainnya)
                const physicalType = this.detectKemitraanPhysicalType(item);
                this.reklasKemitraanTipeFisik = physicalType;

                let autoTargetKib = 'KIB B';
                if (physicalType === 'gedung') {
                    autoTargetKib = 'KIB C';
                } else if (physicalType === 'mesin') {
                    autoTargetKib = 'KIB B';
                } else if (physicalType === 'tanah') {
                    autoTargetKib = 'KIB A';
                } else if (physicalType === 'jaringan') {
                    autoTargetKib = 'KIB D';
                } else if (physicalType === 'lainnya') {
                    autoTargetKib = 'KIB E';
                }

                this.reklasTujuanKib = isActive ? 'KEMITRAAN' : autoTargetKib;

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

                let rawRepeaterItems = [];
                if (Array.isArray(spec.gedung_items) && spec.gedung_items.length > 0) {
                    rawRepeaterItems = spec.gedung_items.map((g, idx) => ({
                        nama_barang: g.gedung_nama_barang || item.nama_barang || ('Gedung #' + (idx + 1)),
                        jumlah_volume: parseInt(g.gedung_jumlah_bangunan) || 1,
                        satuan: g.gedung_satuan || item.satuan || 'Gedung',
                        harga_satuan: parseFloat(g.gedung_nilai_satuan || item.harga_satuan || 0),
                        kode_barang: g.gedung_kode_barang || item.kode_barang || '',
                    }));
                } else if (Array.isArray(spec.mesin_items) && spec.mesin_items.length > 0) {
                    rawRepeaterItems = spec.mesin_items.map((m, idx) => ({
                        nama_barang: m.mesin_nama_barang || item.nama_barang || ('Barang #' + (idx + 1)),
                        jumlah_volume: parseInt(m.mesin_jumlah_barang) || 1,
                        satuan: m.mesin_satuan || item.satuan || 'Unit',
                        harga_satuan: parseFloat(m.mesin_nilai_satuan || m.mesin_harga_satuan || item.harga_satuan || 0),
                        kode_barang: m.mesin_kode_barang || item.kode_barang || '',
                    }));
                } else if (Array.isArray(spec.tanah_items) && spec.tanah_items.length > 0) {
                    rawRepeaterItems = spec.tanah_items.map((t, idx) => ({
                        nama_barang: t.tanah_nama_barang || item.nama_barang || ('Bidang Tanah #' + (idx + 1)),
                        jumlah_volume: parseInt(t.tanah_jumlah_bidang) || 1,
                        satuan: t.tanah_satuan || item.satuan || 'Bidang',
                        harga_satuan: parseFloat(t.tanah_nilai_satuan || item.harga_satuan || 0),
                        kode_barang: t.tanah_kode_barang || item.kode_barang || '',
                    }));
                } else if (Array.isArray(spec.jaringan_items) && spec.jaringan_items.length > 0) {
                    rawRepeaterItems = spec.jaringan_items.map((j, idx) => ({
                        nama_barang: j.jaringan_nama_barang || item.nama_barang || ('Jaringan #' + (idx + 1)),
                        jumlah_volume: parseInt(j.jaringan_jumlah) || 1,
                        satuan: j.jaringan_satuan || item.satuan || 'Ruas',
                        harga_satuan: parseFloat(j.jaringan_nilai_satuan || item.harga_satuan || 0),
                        kode_barang: j.jaringan_kode_barang || item.kode_barang || '',
                    }));
                } else if (Array.isArray(spec.lainnya_items) && spec.lainnya_items.length > 0) {
                    rawRepeaterItems = spec.lainnya_items.map((l, idx) => ({
                        nama_barang: l.lainnya_nama_barang || item.nama_barang || ('Aset Lain #' + (idx + 1)),
                        jumlah_volume: parseInt(l.lainnya_jumlah) || 1,
                        satuan: l.lainnya_satuan || item.satuan || 'Buah',
                        harga_satuan: parseFloat(l.lainnya_nilai_satuan || item.harga_satuan || 0),
                        kode_barang: l.lainnya_kode_barang || item.kode_barang || '',
                    }));
                }

                if (rawRepeaterItems.length > 0) {
                    this.reklasExtracomItems = rawRepeaterItems;
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

                this.reklasTanggal = new Date().toLocaleDateString('en-CA');
                this.reklasAlasan = isActive 
                    ? 'Penyesuaian / koreksi sub-rincian rekening 108 pemanfaatan kemitraan (Akun 1.5.2)' 
                    : 'Masa konsesi kemitraan berakhir, dialihkan ke Aset Tetap definitif';

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

                    // Proteksi Kemitraan Aktif: Hanya boleh antar-rekening 1.5.2 Kemitraan
                    if (this.isKemitraanAktif()) {
                        if (targetKib !== 'KEMITRAAN') {
                            this.showToast('Status kemitraan masih aktif: reklasifikasi hanya diperbolehkan ke kelompok KEMITRAAN (Akun 1.5.2).', 'error');
                            this.isSubmittingReklas = false;
                            return;
                        }
                        if (targetKode && !targetKode.startsWith('1.5.2')) {
                            this.showToast('Kode rekening tujuan harus berada dalam kelompok Akun 1.5.2 Kemitraan Pihak Ketiga.', 'error');
                            this.isSubmittingReklas = false;
                            return;
                        }
                    }

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
            },

            // =========================================================================
            // METODE TERMINASI KEMITRAAN (PENGEMBALIAN KE MITRA / SERAH TERIMA RSUD)
            // =========================================================================
            openTerminasiModal(row, defaultTab = 'kembalikan') {
                if (!row) return;

                const kemitraanId = row.kemitraan_id || (row.kemitraan ? row.kemitraan.id : row.id);
                const rawRow = (row.raw_row && Object.keys(row.raw_row).length > 0) ? row.raw_row : (row.kemitraan || row);
                const isDimanfaatkan = Boolean(row.is_dimanfaatkan ?? rawRow.is_dimanfaatkan ?? (rawRow.tipe_kemitraan === 'dimanfaatkan'));
                const namaBarang = row.nama_barang || row.astap?.nama_barang || rawRow.nama_barang || 'Aset Kemitraan';
                const nomorPks = row.nomor_pks || rawRow.nomor_pks || '-';
                const mitraNama = row.mitra_nama || rawRow.mitra_nama || rawRow.penyedia_nama || 'Mitra Rekanan';
                const mitraPimpinan = rawRow.mitra_pimpinan || rawRow.penyedia_pemilik || 'Pimpinan Mitra Rekanan';
                const nilaiNominal = parseFloat(row.total_realisasi_num || row.nilai_aset || rawRow.nilai_aset || 0);

                const currentYear = new Date().getFullYear();
                const todayStr = this.formatLocalDate(new Date());

                const tglMulai = row.tanggal_mulai || rawRow.tanggal_mulai || row.tanggal_pks || rawRow.tanggal_pks;
                const tglSelesai = row.tanggal_selesai || rawRow.tanggal_selesai || row.tanggal_akhir || rawRow.tanggal_akhir;

                this.terminasiData = {
                    id: kemitraanId,
                    nama_barang: namaBarang,
                    nomor_pks: nomorPks,
                    mitra_nama: row.penyedia_nama || mitraNama,
                    mitra_pimpinan: row.penyedia_pemilik || mitraPimpinan,
                    is_dimanfaatkan: isDimanfaatkan,
                    tipe_kemitraan: isDimanfaatkan ? 'dimanfaatkan' : 'ditambahkan_mitra',
                    tanggal_mulai_fmt: tglMulai ? (this.formatDateIndo ? this.formatDateIndo(tglMulai) : tglMulai) : '-',
                    tanggal_selesai_fmt: tglSelesai ? (this.formatDateIndo ? this.formatDateIndo(tglSelesai) : tglSelesai) : '-',
                    status_konsesi: row.status_konsesi || rawRow.status_konsesi || 'Aktif',
                    category: row.category || rawRow.kategori_barang || '',
                    nilai_aset: nilaiNominal,
                    raw: rawRow
                };

                this.terminasiTab = 'kembalikan';
                this.terminasiForm = {
                    id: kemitraanId,
                    nomor_ba_terminasi: `000.2.3.2/BAST-KMT-KEMBALI/${kemitraanId || '001'}/430.10.7/${currentYear}`,
                    tanggal_terminasi: todayStr,
                    tanggal_ba_terminasi: todayStr,
                    kondisi_akhir: row.kondisi || 'Baik',
                    pihak_penyerah_nama: 'dr. YUS PRIYATNA ADRYANTO, Sp.P, FISR',
                    pihak_penyerah_jabatan: 'Direktur RSUD Dr. H. Koesnandi',
                    pihak_penerima_nama: mitraPimpinan,
                    pihak_penerima_jabatan: 'Direktur / Penanggung Jawab Mitra',
                    catatan_terminasi: 'Pengembalian aset operasional kepada mitra rekanan sehubungan dengan selesainya masa konsesi kerja sama.',
                    berkas_ba_terminasi: null
                };

                this.showModalTerminasi = true;
            },

            openTerminasiModalFromDetail(defaultTab = 'kembalikan') {
                if (!this.selectedAstapDetail) return;
                const detail = this.selectedAstapDetail;
                const targetRow = (detail.raw_row && Object.keys(detail.raw_row).length > 0) ? detail.raw_row : (detail.kemitraan || detail);
                this.openTerminasiModal(targetRow, defaultTab);
            },

            closeTerminasiModal() {
                this.showModalTerminasi = false;
                this.isSubmittingTerminasi = false;
                this.isDraggingTerminasi = false;
                this.removeTerminasiFile();
            },

            handleTerminasiFileChange(e) {
                const file = e.target.files ? e.target.files[0] : null;
                if (file) {
                    if (file.size > 10 * 1024 * 1024) {
                        this.showToast('Ukuran file maksimal 10MB.', 'warning');
                        return;
                    }
                    this.terminasiForm.berkas_ba_terminasi = file;
                }
            },

            removeTerminasiFile() {
                this.terminasiForm.berkas_ba_terminasi = null;
                if (this.$refs.fileInputTerminasi) {
                    this.$refs.fileInputTerminasi.value = '';
                }
            },

            async submitKembalikanForm() {
                if (!this.terminasiForm.nomor_ba_terminasi || !this.terminasiForm.tanggal_terminasi) {
                    this.showToast('Harap lengkapi Nomor BAST dan Tanggal Pengembalian.', 'warning');
                    return;
                }
                if (!this.terminasiForm.pihak_penyerah_nama || !this.terminasiForm.pihak_penerima_nama) {
                    this.showToast('Harap lengkapi nama pihak penyerah dan penerima.', 'warning');
                    return;
                }

                this.isSubmittingTerminasi = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                try {
                    const fd = new FormData();
                    fd.append('_token', token);
                    fd.append('nomor_ba_terminasi', this.terminasiForm.nomor_ba_terminasi);
                    fd.append('tanggal_terminasi', this.terminasiForm.tanggal_terminasi);
                    fd.append('tanggal_ba_terminasi', this.terminasiForm.tanggal_ba_terminasi || this.terminasiForm.tanggal_terminasi);
                    fd.append('kondisi_akhir', this.terminasiForm.kondisi_akhir || 'Baik');
                    fd.append('pihak_penyerah_nama', this.terminasiForm.pihak_penyerah_nama);
                    fd.append('pihak_penyerah_jabatan', this.terminasiForm.pihak_penyerah_jabatan || '');
                    fd.append('pihak_penerima_nama', this.terminasiForm.pihak_penerima_nama);
                    fd.append('pihak_penerima_jabatan', this.terminasiForm.pihak_penerima_jabatan || '');
                    fd.append('catatan_terminasi', this.terminasiForm.catatan_terminasi || '');

                    if (this.terminasiForm.berkas_ba_terminasi instanceof File) {
                        fd.append('berkas_ba_terminasi', this.terminasiForm.berkas_ba_terminasi);
                    }

                    const kemitraanId = this.terminasiData.id;
                    const res = await fetch(`/master-data/kemitraan/${kemitraanId}/kembalikan-ke-mitra`, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: fd
                    });

                    const resJson = await res.json();
                    if (resJson.success) {
                        this.showToast(resJson.message || 'Aset kemitraan berhasil dikembalikan kepada mitra rekanan!', 'success');
                        this.closeTerminasiModal();
                        setTimeout(() => {
                            window.location.reload();
                        }, 1200);
                    } else {
                        this.showToast('Gagal memproses pengembalian: ' + (resJson.message || 'Terjadi kesalahan sistem.'), 'error');
                    }
                } catch (err) {
                    console.error('Kembalikan submit error:', err);
                    this.showToast('Terjadi kesalahan jaringan: ' + err.message, 'error');
                } finally {
                    this.isSubmittingTerminasi = false;
                }
            },

            printTerminasiBast(tipe) {
                const tData = this.terminasiData;
                const fData = this.terminasiForm;

                const rawRow = tData.raw || {};
                const astapObj = {
                    id: tData.id,
                    nama_barang: tData.nama_barang,
                    kode_barang: tData.kode_barang || rawRow.kode_barang || '1.5.2.01.01.001',
                    category: tData.category || rawRow.kategori_barang || '',
                    jumlah_volume: 1,
                    satuan: 'Unit',
                    kondisi: fData.kondisi_akhir || 'Baik',
                    harga_satuan: tData.nilai_aset || 0,
                    total_realisasi_num: tData.nilai_aset || 0
                };

                this.openPrintBast(rawRow, tipe);
            },

            printTerminasiFromDetail() {
                if (!this.selectedAstapDetail) return;
                const detail = this.selectedAstapDetail;
                const rawRow = (detail.raw_row && Object.keys(detail.raw_row).length > 0) ? detail.raw_row : (detail.kemitraan || detail);
                const tipe = detail.jenis_terminasi === 'serah_terima_rsud' ? 'serah_terima_rsud' : 'pengembalian';
                this.openPrintBast(rawRow, tipe);
            },

            batalDikembalikanKeMitra(detail) {
                if (!detail) return;
                const kemitraanId = detail.kemitraan_id || detail.id;
                const namaAset = detail.nama_barang || 'Aset Kemitraan';
                const noPks = detail.nomor_pks || '-';

                this.askConfirmation({
                    title: '↩️ Batalkan Pengembalian Aset?',
                    message: `Anda akan membatalkan status pengembalian aset "${namaAset}".\n\nRiwayat Berita Acara (BA) Pengembalian akan di-reset dan status aset dipulihkan kembali ke "Konsesi Berakhir / Aktif" sehingga aset dapat diubah atau direklasifikasi.`,
                    itemName: `${namaAset} (PKS: ${noPks})`,
                    type: 'danger',
                    btnText: '↩️ Ya, Batalkan Pengembalian',
                    onConfirm: async () => {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        try {
                            this.showToast('Sedang membatalkan status pengembalian aset...', 'info');

                            const res = await fetch(`/master-data/kemitraan/${kemitraanId}/batal-kembalikan`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token
                                }
                            });

                            const resJson = await res.json();
                            if (resJson.success) {
                                this.showDetailModal = false;
                                this.showToast(resJson.message || 'Status aset berhasil dipulihkan!', 'success');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1200);
                            } else {
                                this.showToast('Gagal membatalkan: ' + (resJson.message || 'Terjadi kesalahan sistem.'), 'error');
                            }
                        } catch (err) {
                            console.error('Batal kembalikan error:', err);
                            this.showToast('Terjadi kesalahan jaringan: ' + err.message, 'error');
                        }
                    }
                });
            },

            // =========================================================================
            // METODE PEMBATALAN STATUS KEMITRAAN (STATUS DIBATALKAN)
            // =========================================================================
            openModalBatalKemitraan(row) {
                if (!row) return;

                const kemitraanId = row.kemitraan_id 
                    || (row.kemitraan && row.kemitraan.id ? row.kemitraan.id : null) 
                    || row.id 
                    || row.astap_id 
                    || (row.raw && row.raw.id ? row.raw.id : null);

                const rawRow = (row.raw_row && Object.keys(row.raw_row).length > 0) ? row.raw_row : (row.kemitraan || row);
                const isDimanfaatkan = Boolean(
                    row.is_dimanfaatkan || 
                    (row.kemitraan && row.kemitraan.tipe_kemitraan === 'dimanfaatkan') || 
                    (rawRow && rawRow.tipe_kemitraan === 'dimanfaatkan') || 
                    (row.tipe_kemitraan === 'dimanfaatkan')
                );
                const namaBarang = row.nama_barang || row.astap?.nama_barang || rawRow.nama_barang || 'Aset Kemitraan';
                const nomorPks = row.nomor_pks || rawRow.nomor_pks || '-';
                const mitraNama = row.mitra_nama || rawRow.mitra_nama || rawRow.penyedia_nama || 'Mitra Rekanan';

                this.batalData = {
                    id: kemitraanId,
                    nama_barang: namaBarang,
                    nomor_pks: nomorPks,
                    mitra_nama: mitraNama,
                    is_dimanfaatkan: isDimanfaatkan,
                    raw: rawRow
                };

                this.batalForm = {
                    alasan_batal: ''
                };

                this.showModalBatal = true;
            },

            closeModalBatalKemitraan() {
                this.showModalBatal = false;
                this.isSubmittingBatal = false;
                this.batalForm.alasan_batal = '';
            },

            async submitBatalKemitraan() {
                if (!this.batalData.id) {
                    this.showToast('ID data kemitraan tidak valid atau tidak ditemukan.', 'error');
                    console.error('submitBatalKemitraan: batalData.id is missing', this.batalData);
                    return;
                }
                const alasan = (this.batalForm.alasan_batal || '').trim();
                if (!alasan) {
                    this.showToast('Harap masukkan alasan pembatalan kemitraan.', 'warning');
                    return;
                }

                this.isSubmittingBatal = true;
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                try {
                    const res = await fetch(`/master-data/kemitraan/${this.batalData.id}/batalkan`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify({
                            alasan_batal: alasan
                        })
                    });

                    const resJson = await res.json();
                    if (resJson.success) {
                        this.showModalBatal = false;
                        this.showDetailModal = false;
                        this.showToast(resJson.message || 'Status kemitraan berhasil dibatalkan!', 'success');
                        setTimeout(() => {
                            window.location.reload();
                        }, 1000);
                    } else {
                        this.showToast('Gagal membatalkan kemitraan: ' + (resJson.message || 'Terjadi kesalahan sistem.'), 'error');
                    }
                } catch (err) {
                    console.error('Submit batal kemitraan error:', err);
                    this.showToast('Terjadi kesalahan jaringan: ' + err.message, 'error');
                } finally {
                    this.isSubmittingBatal = false;
                }
            },

            batalPembatalanKemitraan(detail) {
                if (!detail) return;
                const kemitraanId = detail.kemitraan_id 
                    || (detail.kemitraan && detail.kemitraan.id ? detail.kemitraan.id : null) 
                    || detail.id;
                const namaAset = detail.nama_barang || 'Aset Kemitraan';
                const noPks = detail.nomor_pks || '-';

                this.askConfirmation({
                    title: '↩️ Pulihkan Status Kemitraan?',
                    message: `Anda akan membatalkan status pembatalan aset "${namaAset}".\n\nStatus kemitraan akan dipulihkan kembali ke "Aktif" dan operasional kerja sama dapat berjalan normal.`,
                    itemName: `${namaAset} (PKS: ${noPks})`,
                    type: 'danger',
                    btnText: '↩️ Ya, Pulihkan Kemitraan',
                    onConfirm: async () => {
                        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                        try {
                            this.showToast('Sedang memulihkan status kemitraan...', 'info');

                            const res = await fetch(`/master-data/kemitraan/${kemitraanId}/batal-pembatalan`, {
                                method: 'POST',
                                headers: {
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': token
                                }
                            });

                            const resJson = await res.json();
                            if (resJson.success) {
                                this.showDetailModal = false;
                                this.showToast(resJson.message || 'Status kemitraan berhasil dipulihkan!', 'success');
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1200);
                            } else {
                                this.showToast('Gagal memulihkan: ' + (resJson.message || 'Terjadi kesalahan sistem.'), 'error');
                            }
                        } catch (err) {
                            console.error('Batal pembatalan error:', err);
                            this.showToast('Terjadi kesalahan jaringan: ' + err.message, 'error');
                        }
                    }
                });
            }
        };
    }
</script>
