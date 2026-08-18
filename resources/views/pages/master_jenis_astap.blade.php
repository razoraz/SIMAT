<x-layout title="Jenis ASTAP Kode 108 BMD - SIMAT-RK">
    @section('page-title', 'Master Jenis ASTAP (Kode 108 BMD)')
    @section('breadcrumb', 'Master Data System / Jenis ASTAP Kode 108')

    <div x-data="{
        searchQuery: '',
        jenisFilter: 'all',
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        selectedKode: null,

        newFormData: {
            jenis: '1.3.1',
            nama_jenis: 'TANAH',
            sub_rincian_objek: '1.3.1.01.01.01',
            uraian_sub_rincian: '',
            sub_sub_rincian_objek: '1.3.1.01.01.01.001',
            uraian_sub_sub_rincian: ''
        },

        editFormData: {
            id: null,
            jenis: '',
            nama_jenis: '',
            sub_rincian_objek: '',
            uraian_sub_rincian: '',
            sub_sub_rincian_objek: '',
            uraian_sub_sub_rincian: ''
        },

        kode108List: [
            {
                id: 1,
                jenis: '1.3.1',
                nama_jenis: 'TANAH',
                sub_rincian_objek: '1.3.1.01.01.01',
                uraian_sub_rincian: 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL',
                sub_sub_rincian_objek: '1.3.1.01.01.01.001',
                uraian_sub_sub_rincian: 'Tanah Bangunan Rumah Negara Golongan I'
            },
            {
                id: 2,
                jenis: '1.3.1',
                nama_jenis: 'TANAH',
                sub_rincian_objek: '1.3.1.01.01.01',
                uraian_sub_rincian: 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL',
                sub_sub_rincian_objek: '1.3.1.01.01.01.002',
                uraian_sub_sub_rincian: 'Tanah Bangunan Rumah Negara Golongan II'
            },
            {
                id: 3,
                jenis: '1.3.1',
                nama_jenis: 'TANAH',
                sub_rincian_objek: '1.3.1.01.01.01',
                uraian_sub_rincian: 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL',
                sub_sub_rincian_objek: '1.3.1.01.01.01.003',
                uraian_sub_sub_rincian: 'Tanah Bangunan Rumah Negara Golongan III'
            },
            {
                id: 4,
                jenis: '1.3.1',
                nama_jenis: 'TANAH',
                sub_rincian_objek: '1.3.1.01.01.01',
                uraian_sub_rincian: 'TANAH BANGUNAN PERUMAHAN/G.TEMPAT TINGGAL',
                sub_sub_rincian_objek: '1.3.1.01.01.01.004',
                uraian_sub_sub_rincian: 'Tanah Bangunan Rumah Negara Tanpa Golongan'
            },
            {
                id: 5,
                jenis: '1.3.1',
                nama_jenis: 'TANAH',
                sub_rincian_objek: '1.3.1.01.01.02',
                uraian_sub_rincian: 'TANAH UNTUK BANGUNAN GED.PERDAGANGAN/PERUSAHAAN',
                sub_sub_rincian_objek: '1.3.1.01.01.02.013',
                uraian_sub_sub_rincian: 'Tanah Bangunan Apotik / Rumah Sakit'
            },
            {
                id: 6,
                jenis: '1.3.2',
                nama_jenis: 'PERALATAN DAN MESIN',
                sub_rincian_objek: '1.3.2.02.01.01',
                uraian_sub_rincian: 'ALAT KEDOKTERAN KESEHATAN MATRA',
                sub_sub_rincian_objek: '1.3.2.02.01.01.005',
                uraian_sub_sub_rincian: 'CT-Scan 128 Slice High Resolution'
            },
            {
                id: 7,
                jenis: '1.3.2',
                nama_jenis: 'PERALATAN DAN MESIN',
                sub_rincian_objek: '1.3.2.02.01.02',
                uraian_sub_rincian: 'ALAT KEDOKTERAN UMUM / ICU',
                sub_sub_rincian_objek: '1.3.2.02.01.02.012',
                uraian_sub_sub_rincian: 'Patient Monitor uMEC & Defibrillator'
            },
            {
                id: 8,
                jenis: '1.3.3',
                nama_jenis: 'GEDUNG DAN BANGUNAN',
                sub_rincian_objek: '1.3.3.01.01.01',
                uraian_sub_rincian: 'BANGUNAN GEDUNG TEMPAT KERJA',
                sub_sub_rincian_objek: '1.3.3.01.01.01.008',
                uraian_sub_sub_rincian: 'Gedung Rumah Sakit / Paviliun Graha Amukti'
            },
            {
                id: 9,
                jenis: '1.3.4',
                nama_jenis: 'JALAN, IRIGASI DAN JARINGAN',
                sub_rincian_objek: '1.3.4.03.01.01',
                uraian_sub_rincian: 'INSTALASI LISTRIK & OKSIGEN MEDIS',
                sub_sub_rincian_objek: '1.3.4.03.01.01.004',
                uraian_sub_sub_rincian: 'Jaringan Pipa Oksigen Sentral Medis'
            },
            {
                id: 10,
                jenis: '1.3.5',
                nama_jenis: 'ASET TETAP LAINNYA',
                sub_rincian_objek: '1.3.5.01.01.01',
                uraian_sub_rincian: 'BUKU DAN PERPUSTAKAAN MEDIS',
                sub_sub_rincian_objek: '1.3.5.01.01.01.002',
                uraian_sub_sub_rincian: 'Buku Jurnal Kedokteran & Farmakologi'
            },
            {
                id: 11,
                jenis: '1.3.6',
                nama_jenis: 'KONSTRUKSI DALAM PENGERJAAN',
                sub_rincian_objek: '1.3.6.01.01.01',
                uraian_sub_rincian: 'KONSTRUKSI DALAM PENGERJAAN (KDP)',
                sub_sub_rincian_objek: '1.3.6.01.01.01.001',
                uraian_sub_sub_rincian: 'Pembangunan Gedung Rawat Inap Baru Lt 3'
            },
            {
                id: 12,
                jenis: '1.5.3',
                nama_jenis: 'ASET TIDAK BERWUJUD',
                sub_rincian_objek: '1.5.3.01.01.01',
                uraian_sub_rincian: 'SOFTWARE DAN LISENSI SISTEM',
                sub_sub_rincian_objek: '1.5.3.01.01.01.001',
                uraian_sub_sub_rincian: 'Software SIMAT-RK RSUD Dr. H. Koesnandi'
            },
            {
                id: 13,
                jenis: '1.3.7',
                nama_jenis: 'ASET TETAP DALAM RENOVASI',
                sub_rincian_objek: '1.3.7.01.01.01',
                uraian_sub_rincian: 'REHABILITASI DAN RENOVASI GEDUNG',
                sub_sub_rincian_objek: '1.3.7.01.01.01.002',
                uraian_sub_sub_rincian: 'Rehabilitasi & Renovasi Gedung Poliklinik Lt 2'
            }
        ],

        get filteredKode() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.kode108List.filter(item => {
                const matchSearch = (item.uraian_sub_sub_rincian || '').toLowerCase().includes(query) || 
                                    (item.sub_sub_rincian_objek || '').toLowerCase().includes(query) ||
                                    (item.uraian_sub_rincian || '').toLowerCase().includes(query) ||
                                    (item.sub_rincian_objek || '').toLowerCase().includes(query) ||
                                    (item.nama_jenis || '').toLowerCase().includes(query) ||
                                    (item.jenis || '').toLowerCase().includes(query);

                const matchJenis = this.jenisFilter === 'all' || item.jenis === this.jenisFilter;
                return matchSearch && matchJenis;
            });
        },

        get uniqueJenis() {
            const map = new Map();
            this.kode108List.forEach(item => {
                if (!map.has(item.jenis)) {
                    map.set(item.jenis, item.nama_jenis);
                }
            });
            return Array.from(map.entries()).map(([kode, nama]) => ({ kode, nama }));
        },

        get uniqueSubRincian() {
            const set = new Set(this.kode108List.map(i => i.sub_rincian_objek));
            return set.size;
        },

        resetFilters() {
            this.searchQuery = '';
            this.jenisFilter = 'all';
        },

        openDetail(item) {
            this.selectedKode = item;
            this.showDetailModal = true;
        },

        openEdit(item) {
            this.editFormData = { ...item };
            this.showEditModal = true;
        },

        saveNew() {
            if (!this.newFormData.uraian_sub_rincian || !this.newFormData.uraian_sub_sub_rincian) {
                alert('⚠️ Harap lengkapi semua Uraian Sub Rincian dan Sub-Sub Rincian Objek!');
                return;
            }
            const nextId = this.kode108List.length > 0 ? Math.max(...this.kode108List.map(i => i.id)) + 1 : 1;
            this.kode108List.push({
                id: nextId,
                ...this.newFormData
            });
            this.showAddModal = false;
            this.newFormData = {
                jenis: '1.3.1',
                nama_jenis: 'TANAH',
                sub_rincian_objek: '1.3.1.01.01.01',
                uraian_sub_rincian: '',
                sub_sub_rincian_objek: '1.3.1.01.01.01.001',
                uraian_sub_sub_rincian: ''
            };
            alert('✅ Berhasil menambahkan Kode 108 BMD baru!');
        },

        saveEdit() {
            const index = this.kode108List.findIndex(i => i.id === this.editFormData.id);
            if (index !== -1) {
                this.kode108List[index] = { ...this.editFormData };
            }
            this.showEditModal = false;
            alert('✅ Perubahan Kode 108 BMD berhasil disimpan!');
        },

        deleteItem(id) {
            if (confirm('Apakah Anda yakin ingin menghapus klasifikasi Kode 108 ini?')) {
                this.kode108List = this.kode108List.filter(i => i.id !== id);
                alert('🗑️ Data Kode 108 BMD berhasil dihapus.');
            }
        },

        onJenisChangeNew() {
            const lookup = {
                '1.3.1': { nama: 'TANAH', sub_prefix: '1.3.1.01.01.01', sub_sub_prefix: '1.3.1.01.01.01.001' },
                '1.3.2': { nama: 'PERALATAN DAN MESIN', sub_prefix: '1.3.2.02.01.01', sub_sub_prefix: '1.3.2.02.01.01.001' },
                '1.3.3': { nama: 'GEDUNG DAN BANGUNAN', sub_prefix: '1.3.3.01.01.01', sub_sub_prefix: '1.3.3.01.01.01.001' },
                '1.3.4': { nama: 'JALAN, IRIGASI DAN JARINGAN', sub_prefix: '1.3.4.03.01.01', sub_sub_prefix: '1.3.4.03.01.01.001' },
                '1.3.5': { nama: 'ASET TETAP LAINNYA', sub_prefix: '1.3.5.01.01.01', sub_sub_prefix: '1.3.5.01.01.01.001' },
                '1.3.6': { nama: 'KONSTRUKSI DALAM PENGERJAAN', sub_prefix: '1.3.6.01.01.01', sub_sub_prefix: '1.3.6.01.01.01.001' },
                '1.5.3': { nama: 'ASET TIDAK BERWUJUD', sub_prefix: '1.5.3.01.01.01', sub_sub_prefix: '1.5.3.01.01.01.001' },
                '1.3.7': { nama: 'ASET TETAP DALAM RENOVASI', sub_prefix: '1.3.7.01.01.01', sub_sub_prefix: '1.3.7.01.01.01.001' }
            };
            const item = lookup[this.newFormData.jenis];
            if (item) {
                this.newFormData.nama_jenis = item.nama;
                this.newFormData.sub_rincian_objek = item.sub_prefix;
                this.newFormData.sub_sub_rincian_objek = item.sub_sub_prefix;
            }
        },

        onJenisChangeEdit() {
            const lookup = {
                '1.3.1': 'TANAH',
                '1.3.2': 'PERALATAN DAN MESIN',
                '1.3.3': 'GEDUNG DAN BANGUNAN',
                '1.3.4': 'JALAN, IRIGASI DAN JARINGAN',
                '1.3.5': 'ASET TETAP LAINNYA',
                '1.3.6': 'KONSTRUKSI DALAM PENGERJAAN',
                '1.5.3': 'ASET TIDAK BERWUJUD',
                '1.3.7': 'ASET TETAP DALAM RENOVASI'
            };
            if (lookup[this.editFormData.jenis]) {
                this.editFormData.nama_jenis = lookup[this.editFormData.jenis];
            }
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>KODE 108 PERMENDAGRI (BARANG MILIK DAERAH)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Master Jenis ASTAP (Kode 108 BMD)</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Struktur 3 tingkatan klasifikasi aset tetap resmi pemerintah daerah (Jenis Utama, Sub Rincian Objek, dan Sub-Sub Rincian Objek).
                    </p>
                </div>
                
                <button type="button" @click="showAddModal = true"
                    class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Jenis ASTAP</span>
                </button>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">🏛️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Jenis</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400" x-text="uniqueJenis.length + ' Jenis Objek'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">📁</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Sub Rincian</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300" x-text="uniqueSubRincian + ' Sub Rincian'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏷️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Sub-Sub Rincian</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300" x-text="kode108List.length + ' Objek Spesifik'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">📋</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Klasifikasi KIB</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300">KIB A s/d KIB H</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Jenis Utama Tabs (Wrapping & Always Visible) -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">KIB:</span>
                    <button type="button" @click="jenisFilter = 'all'"
                        :class="jenisFilter === 'all' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua KIB
                    </button>
                    <button type="button" @click="jenisFilter = '1.3.1'"
                        :class="jenisFilter === '1.3.1' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🌾 KIB A (Tanah)
                    </button>
                    <button type="button" @click="jenisFilter = '1.3.2'"
                        :class="jenisFilter === '1.3.2' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🔬 KIB B (Mesin)
                    </button>
                    <button type="button" @click="jenisFilter = '1.3.3'"
                        :class="jenisFilter === '1.3.3' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏢 KIB C (Gedung)
                    </button>
                    <button type="button" @click="jenisFilter = '1.3.4'"
                        :class="jenisFilter === '1.3.4' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🚰 KIB D (Jaringan)
                    </button>
                    <button type="button" @click="jenisFilter = '1.3.5'"
                        :class="jenisFilter === '1.3.5' ? 'bg-orange-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        📦 KIB E (Lainnya)
                    </button>
                    <button type="button" @click="jenisFilter = '1.3.6'"
                        :class="jenisFilter === '1.3.6' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏗️ KIB F (KDP)
                    </button>
                    <button type="button" @click="jenisFilter = '1.5.3'"
                        :class="jenisFilter === '1.5.3' ? 'bg-indigo-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        💾 KIB G (Tidak Berwujud)
                    </button>
                    <button type="button" @click="jenisFilter = '1.3.7'"
                        :class="jenisFilter === '1.3.7' ? 'bg-pink-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🔨 KIB H (Renovasi)
                    </button>
                </div>

                <!-- Search Bar & Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari kode 108 / nama jenis / sub rincian / uraian spesifik barang..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                        <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-emerald-400 font-bold" x-text="filteredKode.length"></span> dari <span class="text-white font-bold" x-text="kode108List.length"></span> Data Kode 108
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- TABEL STRUKTUR KODE 108 BMD (PERSIS SESUAI FORMAT EXCEL GAMBAR)           -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300 border-collapse">
                    
                    <!-- 2-Tier Header Sesuai Gambar -->
                    <thead>
                        <!-- Baris 1: Kelompok 3 Blok Header -->
                        <tr class="text-center font-extrabold uppercase tracking-wider text-xs border-b border-slate-800">
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 w-12 text-center align-middle">
                                NO
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-emerald-950/60 text-emerald-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>🏛️</span>
                                    <span>JENIS UTAMA</span>
                                </div>
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-amber-950/60 text-amber-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>📁</span>
                                    <span>SUB RINCIAN OBJEK</span>
                                </div>
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-purple-950/60 text-purple-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>🏷️</span>
                                    <span>SUB - SUB RINCIAN OBJEK</span>
                                </div>
                            </th>
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 text-center align-middle w-28">
                                Aksi
                            </th>
                        </tr>

                        <!-- Baris 2: Sub Kolom Sesuai Nomor Kolom 2 - 7 di Gambar -->
                        <tr class="text-center font-bold uppercase tracking-wider text-[11px] border-b border-slate-800">
                            <!-- Kolom 2 & 3: Jenis -->
                            <th class="px-3 py-2.5 bg-emerald-950/40 text-emerald-400 border-r border-slate-800/80 w-24">
                                JENIS
                            </th>
                            <th class="px-4 py-2.5 bg-emerald-950/40 text-emerald-200 border-r border-slate-800/80 min-w-[180px]">
                                NAMA JENIS
                            </th>

                            <!-- Kolom 4 & 5: Sub Rincian Objek -->
                            <th class="px-3 py-2.5 bg-amber-950/40 text-amber-400 border-r border-slate-800/80 w-36">
                                SUB RINCIAN OBJEK
                            </th>
                            <th class="px-4 py-2.5 bg-amber-950/40 text-amber-200 border-r border-slate-800/80 min-w-[240px]">
                                URAIAN
                            </th>

                            <!-- Kolom 6 & 7: Sub - Sub Rincian Objek -->
                            <th class="px-3 py-2.5 bg-purple-950/40 text-purple-400 border-r border-slate-800/80 w-44">
                                SUB - SUB RINCIAN OBJEK
                            </th>
                            <th class="px-4 py-2.5 bg-purple-950/40 text-purple-200 border-r border-slate-800/80 min-w-[260px]">
                                URAIAN
                            </th>
                        </tr>
                    </thead>

                    <!-- Body Tabel -->
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredKode" :key="item.id">
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <!-- Kolom 1: No -->
                                <td class="px-4 py-4 text-center font-bold text-slate-400 border-r border-slate-800/80" x-text="index + 1"></td>

                                <!-- Kolom 2: JENIS -->
                                <td class="px-3 py-4 text-center font-mono font-bold text-emerald-400 bg-emerald-950/10 border-r border-slate-800/80" x-text="item.jenis"></td>

                                <!-- Kolom 3: NAMA JENIS -->
                                <td class="px-4 py-4 font-bold text-slate-200 uppercase bg-emerald-950/10 border-r border-slate-800/80" x-text="item.nama_jenis"></td>

                                <!-- Kolom 4: SUB RINCIAN OBJEK -->
                                <td class="px-3 py-4 text-center font-mono font-bold text-amber-400 bg-amber-950/10 border-r border-slate-800/80" x-text="item.sub_rincian_objek"></td>

                                <!-- Kolom 5: URAIAN SUB RINCIAN -->
                                <td class="px-4 py-4 font-semibold text-slate-300 uppercase bg-amber-950/10 border-r border-slate-800/80 text-[11px]" x-text="item.uraian_sub_rincian"></td>

                                <!-- Kolom 6: SUB - SUB RINCIAN OBJEK -->
                                <td class="px-3 py-4 text-center font-mono font-bold text-purple-400 bg-purple-950/10 border-r border-slate-800/80" x-text="item.sub_sub_rincian_objek"></td>

                                <!-- Kolom 7: URAIAN SUB-SUB RINCIAN -->
                                <td class="px-4 py-4 font-semibold text-white bg-purple-950/10 border-r border-slate-800/80" x-text="item.uraian_sub_sub_rincian"></td>

                                <!-- Kolom 8: Aksi -->
                                <td class="px-3 py-4 text-center space-x-1 whitespace-nowrap">
                                    <button type="button" @click="openDetail(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>

                                    <button type="button" @click="openEdit(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Ubah</span>
                                    </button>

                                    <button type="button" @click="deleteItem(item.id)"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span>Hapus</span>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL DETAIL KLASIFIKASI KODE 108 BMD                                     -->
        <!-- ========================================================================= -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-5 relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showDetailModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800">
                    <h3 class="text-base font-extrabold text-white">Detail Klasifikasi Kode 108 BMD</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Hierarki Standar Permendagri No. 108</p>
                </div>

                <div class="space-y-3.5 text-xs" x-if="selectedKode">
                    <!-- Blok 1: Jenis Utama -->
                    <div class="p-3.5 rounded-2xl bg-emerald-950/30 border border-emerald-500/30 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px]">1. Jenis Utama</span>
                            <span class="font-mono font-bold text-emerald-300" x-text="selectedKode.jenis"></span>
                        </div>
                        <p class="font-bold text-white text-sm uppercase" x-text="selectedKode.nama_jenis"></p>
                    </div>

                    <!-- Blok 2: Sub Rincian Objek -->
                    <div class="p-3.5 rounded-2xl bg-amber-950/30 border border-amber-500/30 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px]">2. Sub Rincian Objek</span>
                            <span class="font-mono font-bold text-amber-300" x-text="selectedKode.sub_rincian_objek"></span>
                        </div>
                        <p class="font-semibold text-white text-xs uppercase" x-text="selectedKode.uraian_sub_rincian"></p>
                    </div>

                    <!-- Blok 3: Sub - Sub Rincian Objek -->
                    <div class="p-3.5 rounded-2xl bg-purple-950/30 border border-purple-500/30 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px]">3. Sub - Sub Rincian Objek (Spesifik)</span>
                            <span class="font-mono font-bold text-purple-300" x-text="selectedKode.sub_sub_rincian_objek"></span>
                        </div>
                        <p class="font-bold text-white text-sm" x-text="selectedKode.uraian_sub_sub_rincian"></p>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex justify-center">
                    <button type="button" @click="showDetailModal = false" class="px-6 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span>Tutup</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL TAMBAH KODE 108 BMD                                                 -->
        <!-- ========================================================================= -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showAddModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white">Tambah Jenis ASTAP (Kode 108)</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Input struktur 3 tingkatan (Jenis, Sub Rincian, Sub-Sub Rincian)</p>
                </div>

                <form @submit.prevent="saveNew()" class="space-y-3.5 text-xs">
                    <!-- Blok 1: Jenis Utama -->
                    <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                        <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Jenis Utam</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Jenis</label>
                                <select x-model="newFormData.jenis" @change="onJenisChangeNew()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                                    <option value="1.3.1">1.3.1 - TANAH</option>
                                    <option value="1.3.2">1.3.2 - PERALATAN & MESIN</option>
                                    <option value="1.3.3">1.3.3 - GEDUNG & BANGUNAN</option>
                                    <option value="1.3.4">1.3.4 - JALAN & JARINGAN</option>
                                    <option value="1.3.5">1.3.5 - ASET TETAP LAINNYA</option>
                                    <option value="1.3.6">1.3.6 - KONSTRUKSI KDP</option>
                                    <option value="1.5.3">1.5.3 - TIDAK BERWUJUD</option>
                                    <option value="1.3.7">1.3.7 - DALAM RENOVASI</option>
                                </select>
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Jenis</label>
                                <input type="text" x-model="newFormData.nama_jenis" readonly class="w-full bg-slate-950/60 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 2: Sub Rincian Objek -->
                    <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                        <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Sub Rincian Objek</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub Rincian</label>
                                <input type="text" x-model="newFormData.sub_rincian_objek" placeholder="1.3.1.01.01.01" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Uraian Sub Rincian</label>
                                <input type="text" x-model="newFormData.uraian_sub_rincian" placeholder="Contoh: TANAH BANGUNAN PERUMAHAN..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 3: Sub - Sub Rincian Objek -->
                    <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                        <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub - Sub Rincian Objek (Spesifik)</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub-Sub Rincian</label>
                                <input type="text" x-model="newFormData.sub_sub_rincian_objek" placeholder="1.3.1.01.01.01.001" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Uraian Sub-Sub Rincian (Nama Barang)</label>
                                <input type="text" x-model="newFormData.uraian_sub_sub_rincian" placeholder="Contoh: Tanah Bangunan Rumah Negara Golongan I..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                        <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Kode 108</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL UBAH KODE 108 BMD                                                   -->
        <!-- ========================================================================= -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white">Ubah Jenis ASTAP (Kode 108)</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Perbarui struktur 3 tingkatan (Jenis, Sub Rincian, Sub-Sub Rincian)</p>
                </div>

                <form @submit.prevent="saveEdit()" class="space-y-3.5 text-xs">
                    <!-- Blok 1: Jenis Utama -->
                    <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                        <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Jenis Utama</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Jenis</label>
                                <input type="text" x-model="editFormData.jenis" @input="onJenisChangeEdit()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Jenis</label>
                                <input type="text" x-model="editFormData.nama_jenis" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 2: Sub Rincian Objek -->
                    <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                        <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Sub Rincian Objek</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub Rincian</label>
                                <input type="text" x-model="editFormData.sub_rincian_objek" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Uraian Sub Rincian</label>
                                <input type="text" x-model="editFormData.uraian_sub_rincian" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 3: Sub - Sub Rincian Objek -->
                    <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                        <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub - Sub Rincian Objek (Spesifik)</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub-Sub Rincian</label>
                                <input type="text" x-model="editFormData.sub_sub_rincian_objek" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Uraian Sub-Sub Rincian (Nama Barang)</label>
                                <input type="text" x-model="editFormData.uraian_sub_sub_rincian" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layout>
