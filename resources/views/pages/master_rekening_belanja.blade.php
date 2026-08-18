<x-layout title="Rekening Belanja SIPD - SIMAT-RK">
    @section('page-title', 'Master Rekening Belanja SIPD')
    @section('breadcrumb', 'Master Data System / Rekening Belanja SIPD')

    <div x-data="{
        searchQuery: '',
        kelompokFilter: 'all',
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        selectedRek: null,

        newFormData: {
            kelompok: '5.2.02',
            nama_kelompok: 'Belanja Modal Peralatan dan Mesin',
            kode_rek: '5.2.02.05.02.0006',
            nama_belanja: 'Belanja Modal Alat Rumah Tangga Lainnya (Home Use)'
        },

        editFormData: {
            id: null,
            kelompok: '',
            nama_kelompok: '',
            kode_rek: '',
            nama_belanja: ''
        },

        rekeningList: [
            {
                id: 1,
                kelompok: '5.2.02',
                nama_kelompok: 'Belanja Modal Peralatan dan Mesin',
                kode_rek: '5.2.02.05.02.0006',
                nama_belanja: 'Belanja Modal Alat Rumah Tangga Lainnya (Home Use)'
            },
            {
                id: 2,
                kelompok: '5.2.02',
                nama_kelompok: 'Belanja Modal Peralatan dan Mesin',
                kode_rek: '5.2.02.08.01.0005',
                nama_belanja: 'Belanja Modal Alat Kedokteran Radiologi & Imaging'
            },
            {
                id: 3,
                kelompok: '5.2.02',
                nama_kelompok: 'Belanja Modal Peralatan dan Mesin',
                kode_rek: '5.2.02.08.01.0012',
                nama_belanja: 'Belanja Modal Alat Kedokteran ICU & Ruang Rawat Intensif'
            },
            {
                id: 4,
                kelompok: '5.2.03',
                nama_kelompok: 'Belanja Modal Gedung dan Bangunan',
                kode_rek: '5.2.03.01.01.0001',
                nama_belanja: 'Belanja Modal Bangunan Gedung Rawat Inap & Poliklinik'
            },
            {
                id: 5,
                kelompok: '5.2.04',
                nama_kelompok: 'Belanja Modal Jalan, Jaringan dan Irigasi',
                kode_rek: '5.2.04.03.01.0004',
                nama_belanja: 'Belanja Modal Instalasi Jaringan Pipa Gas Oksigen Sentral Medis'
            },
            {
                id: 6,
                kelompok: '5.2.01',
                nama_kelompok: 'Belanja Modal Tanah',
                kode_rek: '5.2.01.01.01.0002',
                nama_belanja: 'Belanja Modal Pengadaan Tanah Fasilitas Pelayanan Kesehatan'
            },
            {
                id: 7,
                kelompok: '5.2.05',
                nama_kelompok: 'Belanja Modal Aset Tetap Lainnya',
                kode_rek: '5.2.05.01.01.0003',
                nama_belanja: 'Belanja Modal Bahan Pustaka dan Jurnal Ilmiah Kedokteran'
            },
            {
                id: 8,
                kelompok: '5.2.06',
                nama_kelompok: 'Belanja Modal Aset Tidak Berwujud',
                kode_rek: '5.2.06.01.01.0001',
                nama_belanja: 'Belanja Modal Software Sistem Informasi Manajemen RS (SIMRS)'
            }
        ],

        get filteredRekenings() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.rekeningList.filter(item => {
                const matchSearch = (item.kode_rek || '').toLowerCase().includes(query) ||
                                    (item.nama_belanja || '').toLowerCase().includes(query) ||
                                    (item.kelompok || '').toLowerCase().includes(query) ||
                                    (item.nama_kelompok || '').toLowerCase().includes(query);

                const matchKelompok = this.kelompokFilter === 'all' || item.kelompok === this.kelompokFilter;
                return matchSearch && matchKelompok;
            });
        },

        get uniqueKelompoks() {
            const map = new Map();
            this.rekeningList.forEach(item => {
                if (!map.has(item.kelompok)) {
                    map.set(item.kelompok, item.nama_kelompok);
                }
            });
            return Array.from(map.entries()).map(([kode, nama]) => ({ kode, nama }));
        },

        resetFilters() {
            this.searchQuery = '';
            this.kelompokFilter = 'all';
        },

        openDetail(item) {
            this.selectedRek = item;
            this.showDetailModal = true;
        },

        openEdit(item) {
            this.editFormData = { ...item };
            this.showEditModal = true;
        },

        saveNew() {
            if (!this.newFormData.kode_rek || !this.newFormData.nama_belanja) {
                alert('⚠️ Harap lengkapi Kode Rekening dan Nama Belanja Pengadaan!');
                return;
            }
            const nextId = this.rekeningList.length > 0 ? Math.max(...this.rekeningList.map(i => i.id)) + 1 : 1;
            this.rekeningList.push({
                id: nextId,
                ...this.newFormData
            });
            this.showAddModal = false;
            this.newFormData = {
                kelompok: '5.2.02',
                nama_kelompok: 'Belanja Modal Peralatan dan Mesin',
                kode_rek: '',
                nama_belanja: ''
            };
            alert('✅ Berhasil menambahkan Rekening Belanja Pengadaan SIPD baru!');
        },

        saveEdit() {
            const index = this.rekeningList.findIndex(i => i.id === this.editFormData.id);
            if (index !== -1) {
                this.rekeningList[index] = { ...this.editFormData };
            }
            this.showEditModal = false;
            alert('✅ Perubahan Rekening Belanja SIPD berhasil disimpan!');
        },

        deleteItem(id) {
            if (confirm('Apakah Anda yakin ingin menghapus rekening belanja ini?')) {
                this.rekeningList = this.rekeningList.filter(i => i.id !== id);
                alert('🗑️ Rekening belanja berhasil dihapus.');
            }
        },

        onKelompokChangeNew() {
            const lookup = {
                '5.2.01': 'Belanja Modal Tanah',
                '5.2.02': 'Belanja Modal Peralatan dan Mesin',
                '5.2.03': 'Belanja Modal Gedung dan Bangunan',
                '5.2.04': 'Belanja Modal Jalan, Jaringan dan Irigasi',
                '5.2.05': 'Belanja Modal Aset Tetap Lainnya',
                '5.2.06': 'Belanja Modal Aset Tidak Berwujud'
            };
            if (lookup[this.newFormData.kelompok]) {
                this.newFormData.nama_kelompok = lookup[this.newFormData.kelompok];
            }
        },

        onKelompokChangeEdit() {
            const lookup = {
                '5.2.01': 'Belanja Modal Tanah',
                '5.2.02': 'Belanja Modal Peralatan dan Mesin',
                '5.2.03': 'Belanja Modal Gedung dan Bangunan',
                '5.2.04': 'Belanja Modal Jalan, Jaringan dan Irigasi',
                '5.2.05': 'Belanja Modal Aset Tetap Lainnya',
                '5.2.06': 'Belanja Modal Aset Tidak Berwujud'
            };
            if (lookup[this.editFormData.kelompok]) {
                this.editFormData.nama_kelompok = lookup[this.editFormData.kelompok];
            }
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-blue-600/15 via-slate-900 to-slate-900 border border-blue-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        <span>AKUN REKENING BELANJA PENGADAAN SIPD RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Rekening Belanja Pengadaan SIPD</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Daftar akun rekening belanja modal (Akun 5.2) untuk perolehan aset tetap rumah sakit sesuai kodefikasi DPA SIPD Kabupaten Bondowoso.
                    </p>
                </div>
                
                <button type="button" @click="showAddModal = true"
                    class="px-4 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-bold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Rekening Belanja</span>
                </button>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 text-lg">💳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Rekening</span>
                        <span class="text-sm sm:text-base font-extrabold text-blue-400" x-text="rekeningList.length + ' Akun Belanja'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">🔬</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Modal Alat & Mesin</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300">Akun 5.2.02</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏢</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Modal Bangunan</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300">Akun 5.2.03</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">🔗</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Sinkronisasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300">SIPD RSUD</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Kelompok Belanja Tabs -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Kelompok:</span>
                    <button type="button" @click="kelompokFilter = 'all'"
                        :class="kelompokFilter === 'all' ? 'bg-blue-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua Akun Belanja
                    </button>
                    <button type="button" @click="kelompokFilter = '5.2.01'"
                        :class="kelompokFilter === '5.2.01' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🌾 5.2.01 Tanah
                    </button>
                    <button type="button" @click="kelompokFilter = '5.2.02'"
                        :class="kelompokFilter === '5.2.02' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🔬 5.2.02 Alat & Mesin
                    </button>
                    <button type="button" @click="kelompokFilter = '5.2.03'"
                        :class="kelompokFilter === '5.2.03' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏢 5.2.03 Gedung
                    </button>
                    <button type="button" @click="kelompokFilter = '5.2.04'"
                        :class="kelompokFilter === '5.2.04' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🚰 5.2.04 Jaringan
                    </button>
                    <button type="button" @click="kelompokFilter = '5.2.05'"
                        :class="kelompokFilter === '5.2.05' ? 'bg-orange-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        📦 5.2.05 Aset Lainnya
                    </button>
                    <button type="button" @click="kelompokFilter = '5.2.06'"
                        :class="kelompokFilter === '5.2.06' ? 'bg-indigo-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        💾 5.2.06 Tak Berwujud
                    </button>
                </div>

                <!-- Search Bar & Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari kode rekening / nama belanja pengadaan SIPD..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all">
                        <svg class="w-4 h-4 text-blue-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-blue-400 font-bold" x-text="filteredRekenings.length"></span> dari <span class="text-white font-bold" x-text="rekeningList.length"></span> Akun Belanja
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
        <!-- TABEL REKENING BELANJA UNTUK PENGADAAN SIPD                               -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl overflow-hidden mb-6">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300 border-collapse">
                    
                    <!-- 2-Tier Header Sesuai Format Gambar -->
                    <thead>
                        <!-- Baris 1: Header Utama -->
                        <tr class="text-center font-extrabold uppercase tracking-wider text-xs border-b border-slate-800">
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 w-12 text-center align-middle">
                                NO
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-blue-950/60 text-blue-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>💳</span>
                                    <span>Rekening Belanja Untuk Pengadaan SIPD</span>
                                </div>
                            </th>
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 text-center align-middle w-48">
                                Kelompok Akun Belanja
                            </th>
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 text-center align-middle w-28">
                                Aksi
                            </th>
                        </tr>

                        <!-- Baris 2: Sub Kolom (Kode Rek & Nama Belanja Pengadaan) -->
                        <tr class="text-center font-bold uppercase tracking-wider text-[11px] border-b border-slate-800">
                            <th class="px-4 py-2.5 bg-blue-950/40 text-blue-400 border-r border-slate-800/80 w-48 text-center">
                                Kode Rek
                            </th>
                            <th class="px-6 py-2.5 bg-blue-950/40 text-blue-200 border-r border-slate-800/80 min-w-[320px] text-left">
                                Nama Belanja Pengadaan
                            </th>
                        </tr>
                    </thead>

                    <!-- Body Tabel -->
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredRekenings" :key="item.id">
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <!-- Kolom 1: No -->
                                <td class="px-4 py-4 text-center font-bold text-slate-400 border-r border-slate-800/80" x-text="index + 1"></td>

                                <!-- Kolom 2 (8): Kode Rek -->
                                <td class="px-4 py-4 text-center font-mono font-bold text-blue-400 bg-blue-950/10 border-r border-slate-800/80" x-text="item.kode_rek"></td>

                                <!-- Kolom 3 (9): Nama Belanja Pengadaan -->
                                <td class="px-6 py-4 font-semibold text-white bg-blue-950/10 border-r border-slate-800/80 text-sm" x-text="item.nama_belanja"></td>

                                <!-- Kolom 4: Kelompok Akun -->
                                <td class="px-4 py-4 text-center border-r border-slate-800/80">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border"
                                          :class="item.kelompok === '5.2.02' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30' : (item.kelompok === '5.2.03' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' : 'bg-blue-500/20 text-blue-300 border-blue-500/30')"
                                          x-text="item.kelompok + ' - ' + item.nama_kelompok"></span>
                                </td>

                                <!-- Kolom 5: Aksi -->
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
        <!-- MODAL DETAIL REKENING BELANJA                                             -->
        <!-- ========================================================================= -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-5 relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showDetailModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800">
                    <h3 class="text-base font-extrabold text-white">Detail Rekening Belanja SIPD</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian Akun Belanja Modal Pengadaan RSUD</p>
                </div>

                <div class="space-y-3.5 text-xs" x-if="selectedRek">
                    <!-- Kelompok -->
                    <div class="p-3.5 rounded-2xl bg-blue-950/30 border border-blue-500/30 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-blue-400 uppercase tracking-wider text-[10px]">Kelompok Akun Belanja</span>
                            <span class="font-mono font-bold text-blue-300" x-text="selectedRek.kelompok"></span>
                        </div>
                        <p class="font-bold text-white text-sm" x-text="selectedRek.nama_kelompok"></p>
                    </div>

                    <!-- Kode Rek & Nama Belanja -->
                    <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-3">
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase tracking-wider font-bold block mb-1">Kode Rekening Belanja (8)</span>
                            <p class="font-mono font-extrabold text-blue-400 text-base" x-text="selectedRek.kode_rek"></p>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] uppercase tracking-wider font-bold block mb-1">Nama Belanja Pengadaan (9)</span>
                            <p class="font-bold text-white text-sm leading-relaxed" x-text="selectedRek.nama_belanja"></p>
                        </div>
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
        <!-- MODAL TAMBAH REKENING BELANJA                                             -->
        <!-- ========================================================================= -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showAddModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white">Tambah Rekening Belanja Baru</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Input Kode Rekening & Nama Belanja Pengadaan SIPD</p>
                </div>

                <form @submit.prevent="saveNew()" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-400 mb-1.5 font-semibold">Kelompok Belanja Modal</label>
                        <select x-model="newFormData.kelompok" @change="onKelompokChangeNew()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-blue-400 font-bold focus:border-blue-500">
                            <option value="5.2.01">5.2.01 - Belanja Modal Tanah</option>
                            <option value="5.2.02">5.2.02 - Belanja Modal Peralatan dan Mesin</option>
                            <option value="5.2.03">5.2.03 - Belanja Modal Gedung dan Bangunan</option>
                            <option value="5.2.04">5.2.04 - Belanja Modal Jalan, Jaringan & Irigasi</option>
                            <option value="5.2.05">5.2.05 - Belanja Modal Aset Tetap Lainnya</option>
                            <option value="5.2.06">5.2.06 - Belanja Modal Aset Tidak Berwujud</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1.5 font-semibold">Kode Rek (8)</label>
                        <input type="text" x-model="newFormData.kode_rek" placeholder="Contoh: 5.2.02.05.02.0006" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-blue-400 font-mono font-bold focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1.5 font-semibold">Nama Belanja Pengadaan (9)</label>
                        <input type="text" x-model="newFormData.nama_belanja" placeholder="Contoh: Belanja Modal Alat Rumah Tangga Lainnya..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white font-semibold focus:border-blue-500">
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                        <button type="button" @click="showAddModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Rekening</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL UBAH REKENING BELANJA                                               -->
        <!-- ========================================================================= -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white">Ubah Rekening Belanja SIPD</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Perbarui Kode Rekening & Nama Belanja</p>
                </div>

                <form @submit.prevent="saveEdit()" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-400 mb-1.5 font-semibold">Kelompok Belanja Modal</label>
                        <select x-model="editFormData.kelompok" @change="onKelompokChangeEdit()" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-blue-400 font-bold focus:border-blue-500">
                            <option value="5.2.01">5.2.01 - Belanja Modal Tanah</option>
                            <option value="5.2.02">5.2.02 - Belanja Modal Peralatan dan Mesin</option>
                            <option value="5.2.03">5.2.03 - Belanja Modal Gedung dan Bangunan</option>
                            <option value="5.2.04">5.2.04 - Belanja Modal Jalan, Jaringan & Irigasi</option>
                            <option value="5.2.05">5.2.05 - Belanja Modal Aset Tetap Lainnya</option>
                            <option value="5.2.06">5.2.06 - Belanja Modal Aset Tidak Berwujud</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1.5 font-semibold">Kode Rek (8)</label>
                        <input type="text" x-model="editFormData.kode_rek" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-blue-400 font-mono font-bold focus:border-blue-500">
                    </div>

                    <div>
                        <label class="block text-slate-400 mb-1.5 font-semibold">Nama Belanja Pengadaan (9)</label>
                        <input type="text" x-model="editFormData.nama_belanja" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white font-semibold focus:border-blue-500">
                    </div>

                    <div class="pt-3 flex items-center justify-end space-x-2.5 border-t border-slate-800">
                        <button type="button" @click="showEditModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-rose-500/20 text-slate-300 hover:text-rose-300 border border-slate-700 hover:border-rose-500/30 font-bold text-xs shadow-md transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            <span>Batal</span>
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layout>
