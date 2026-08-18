<x-layout title="Jenis Pengadaan SIPD - SIMAT-RK">
    @section('page-title', 'Master Jenis Pengadaan (SIPD)')
    @section('breadcrumb', 'Master Data System / Jenis Pengadaan SIPD')

    <div x-data="{
        searchQuery: '',
        programFilter: 'all',
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        selectedItem: null,

        newFormData: {
            program_kode: '0.00.01',
            program_nama: '',
            kegiatan_kode: '0.00.01.2.10',
            kegiatan_nama: '',
            sub_kegiatan_kode: '0.00.01.2.10.0001',
            sub_kegiatan_nama: '',
            keterangan: ''
        },

        editFormData: {
            id: null,
            program_kode: '',
            program_nama: '',
            kegiatan_kode: '',
            kegiatan_nama: '',
            sub_kegiatan_kode: '',
            sub_kegiatan_nama: '',
            keterangan: ''
        },

        sipdList: [
            {
                id: 1,
                program_kode: '0.00.01',
                program_nama: 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
                kegiatan_kode: '0.00.01.2.10',
                kegiatan_nama: 'Peningkatan Pelayanan BLUD',
                sub_kegiatan_kode: '0.00.01.2.10.0001',
                sub_kegiatan_nama: 'Pelayanan dan Penunjang Pelayanan BLUD',
                keterangan: 'Alokasi pengadaan operasional, sarana dan prasarana penunjang BLUD RSUD Dr. H. Koesnandi'
            },
            {
                id: 2,
                program_kode: '0.00.01',
                program_nama: 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
                kegiatan_kode: '0.00.01.2.10',
                kegiatan_nama: 'Peningkatan Pelayanan BLUD',
                sub_kegiatan_kode: '0.00.01.2.10.0002',
                sub_kegiatan_nama: 'Pengadaan Sarana dan Prasarana Pendukung Fasilitas Pelayanan Kesehatan',
                keterangan: 'Belanja modal alat medis ICU, Bed Patient, Instalasi Gas Medis dan Genset Cadangan'
            },
            {
                id: 3,
                program_kode: '1.02.02',
                program_nama: 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat',
                kegiatan_kode: '1.02.02.2.02',
                kegiatan_nama: 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKP dan UKM Rujukan',
                sub_kegiatan_kode: '1.02.02.2.02.0005',
                sub_kegiatan_nama: 'Pembangunan / Renovasi Gedung Rumah Sakit dan Sarana Penunjang',
                keterangan: 'Alokasi APBD/DAK untuk pekerjaan fisik renovasi paviliun dan gedung poliklinik'
            },
            {
                id: 4,
                program_kode: '1.02.02',
                program_nama: 'Program Pemenuhan Upaya Kesehatan Perorangan dan Upaya Kesehatan Masyarakat',
                kegiatan_kode: '1.02.02.2.02',
                kegiatan_nama: 'Penyediaan Fasilitas Pelayanan Kesehatan untuk UKP dan UKM Rujukan',
                sub_kegiatan_kode: '1.02.02.2.02.0012',
                sub_kegiatan_nama: 'Pengadaan Alat Kesehatan / Alat Penunjang Medik Fasilitas Pelayanan Kesehatan',
                keterangan: 'Pengadaan CT-Scan 128 Slice, USG Doppler 4D, Radiologi & Alat Kamar Operasi (IBS)'
            },
            {
                id: 5,
                program_kode: '1.02.03',
                program_nama: 'Program Peningkatan Kapasitas Sumber Daya Manusia Kesehatan',
                kegiatan_kode: '1.02.03.2.01',
                kegiatan_nama: 'Pengembangan Mutu dan Akreditasi Fasilitas Pelayanan Kesehatan',
                sub_kegiatan_kode: '1.02.03.2.01.0003',
                sub_kegiatan_nama: 'Pengadaan Sistem Informasi Kesehatan & Software Manajemen SIMRS',
                keterangan: 'Pengadaan lisensi server, software Rekam Medis Elektronik (RME) & Integrasi SatuSehat'
            }
        ],

        get filteredSipd() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.sipdList.filter(item => {
                const matchSearch = (item.program_kode || '').toLowerCase().includes(query) ||
                                    (item.program_nama || '').toLowerCase().includes(query) ||
                                    (item.kegiatan_kode || '').toLowerCase().includes(query) ||
                                    (item.kegiatan_nama || '').toLowerCase().includes(query) ||
                                    (item.sub_kegiatan_kode || '').toLowerCase().includes(query) ||
                                    (item.sub_kegiatan_nama || '').toLowerCase().includes(query) ||
                                    (item.keterangan || '').toLowerCase().includes(query);

                const matchProgram = this.programFilter === 'all' || item.program_kode === this.programFilter;
                return matchSearch && matchProgram;
            });
        },

        get uniquePrograms() {
            const map = new Map();
            this.sipdList.forEach(item => {
                if (!map.has(item.program_kode)) {
                    map.set(item.program_kode, item.program_nama);
                }
            });
            return Array.from(map.entries()).map(([kode, nama]) => ({ kode, nama }));
        },

        get uniqueKegiatan() {
            const map = new Map();
            this.sipdList.forEach(item => {
                if (!map.has(item.kegiatan_kode)) {
                    map.set(item.kegiatan_kode, item.kegiatan_nama);
                }
            });
            return Array.from(map.entries()).map(([kode, nama]) => ({ kode, nama }));
        },

        resetFilters() {
            this.searchQuery = '';
            this.programFilter = 'all';
        },

        openDetail(item) {
            this.selectedItem = item;
            this.showDetailModal = true;
        },

        openEdit(item) {
            this.editFormData = { ...item };
            this.showEditModal = true;
        },

        saveNew() {
            if (!this.newFormData.program_nama || !this.newFormData.kegiatan_nama || !this.newFormData.sub_kegiatan_nama) {
                alert('⚠️ Harap lengkapi semua nama Program, Kegiatan, dan Sub Kegiatan!');
                return;
            }
            const nextId = this.sipdList.length > 0 ? Math.max(...this.sipdList.map(i => i.id)) + 1 : 1;
            this.sipdList.push({
                id: nextId,
                ...this.newFormData
            });
            this.showAddModal = false;
            this.newFormData = {
                program_kode: '0.00.01',
                program_nama: '',
                kegiatan_kode: '0.00.01.2.10',
                kegiatan_nama: '',
                sub_kegiatan_kode: '0.00.01.2.10.0001',
                sub_kegiatan_nama: '',
                keterangan: ''
            };
            alert('✅ Berhasil menambahkan Jenis Pengadaan SIPD baru!');
        },

        saveEdit() {
            const index = this.sipdList.findIndex(i => i.id === this.editFormData.id);
            if (index !== -1) {
                this.sipdList[index] = { ...this.editFormData };
            }
            this.showEditModal = false;
            alert('✅ Perubahan Jenis Pengadaan SIPD berhasil disimpan!');
        },

        deleteItem(id) {
            if (confirm('Apakah Anda yakin ingin menghapus data pengadaan SIPD ini?')) {
                this.sipdList = this.sipdList.filter(i => i.id !== id);
                alert('🗑️ Data pengadaan SIPD berhasil dihapus.');
            }
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-emerald-600/15 via-slate-900 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>HIERARKI PENGADAAN SIPD (SISTEM INFORMASI PEMERINTAH DAERAH)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Jenis & Hierarki Pengadaan SIPD</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Struktur 3 tingkatan penganggaran pengadaan barang & jasa (Program Pengadaan, Kegiatan Pengadaan, dan Sub Kegiatan Pengadaan) sesuai standar DPA SIPD.
                    </p>
                </div>
                
                <button type="button" @click="showAddModal = true"
                    class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Pengadaan SIPD</span>
                </button>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📑</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Program</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400" x-text="uniquePrograms.length + ' Program'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">📁</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Kegiatan</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300" x-text="uniqueKegiatan.length + ' Kegiatan'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">📄</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Sub Kegiatan</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300" x-text="sipdList.length + ' Sub Kegiatan'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">🔗</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Sinkronisasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300">DPA SIPD RSUD</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Program Tabs -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Program:</span>
                    <button type="button" @click="programFilter = 'all'"
                        :class="programFilter === 'all' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua Program
                    </button>
                    <template x-for="prog in uniquePrograms" :key="prog.kode">
                        <button type="button" @click="programFilter = prog.kode"
                            :class="programFilter === prog.kode ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                            class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5">
                            <span class="font-mono font-bold" x-text="prog.kode"></span>
                            <span class="truncate max-w-[180px] sm:max-w-xs" x-text="prog.nama"></span>
                        </button>
                    </template>
                </div>

                <!-- Search Bar & Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari kode / nama program / kegiatan / sub kegiatan SIPD..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                        <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-emerald-400 font-bold" x-text="filteredSipd.length"></span> dari <span class="text-white font-bold" x-text="sipdList.length"></span> Data SIPD
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
        <!-- TABEL STRUKTUR JENIS PENGADAAN SIPD (3 BLOK SESUAI FORMAT EXCEL)          -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300 border-collapse">
                    
                    <!-- 2-Tier Header Sesuai Desain Gambar -->
                    <thead>
                        <!-- Baris Header 1 (Kelompok 3 Blok) -->
                        <tr class="text-center font-extrabold uppercase tracking-wider text-xs border-b border-slate-800">
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 w-12 text-center align-middle">
                                NO
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-emerald-950/60 text-emerald-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>📑</span>
                                    <span>Program Pengadaan SIPD</span>
                                </div>
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-amber-950/60 text-amber-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>📁</span>
                                    <span>Kegiatan Pengadaan SIPD</span>
                                </div>
                            </th>
                            <th colspan="2" class="px-4 py-3 bg-purple-950/60 text-purple-300 border-r border-slate-800">
                                <div class="flex items-center justify-center space-x-2">
                                    <span>📄</span>
                                    <span>Sub Kegiatan Pengadaan SIPD</span>
                                </div>
                            </th>
                            <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 text-center align-middle w-28">
                                Aksi
                            </th>
                        </tr>

                        <!-- Baris Header 2 (Sub Kolom: Kode & Nama) -->
                        <tr class="text-center font-bold uppercase tracking-wider text-[11px] border-b border-slate-800">
                            <!-- Program -->
                            <th class="px-3 py-2.5 bg-emerald-950/40 text-emerald-400 border-r border-slate-800/80 w-28">
                                Kode
                            </th>
                            <th class="px-4 py-2.5 bg-emerald-950/40 text-emerald-200 border-r border-slate-800/80 min-w-[220px]">
                                Nama Program
                            </th>

                            <!-- Kegiatan -->
                            <th class="px-3 py-2.5 bg-amber-950/40 text-amber-400 border-r border-slate-800/80 w-32">
                                Kode
                            </th>
                            <th class="px-4 py-2.5 bg-amber-950/40 text-amber-200 border-r border-slate-800/80 min-w-[220px]">
                                Nama Kegiatan Pengadaan
                            </th>

                            <!-- Sub Kegiatan -->
                            <th class="px-3 py-2.5 bg-purple-950/40 text-purple-400 border-r border-slate-800/80 w-36">
                                Kode
                            </th>
                            <th class="px-4 py-2.5 bg-purple-950/40 text-purple-200 border-r border-slate-800/80 min-w-[240px]">
                                Nama Sub Kegiatan Pengadaan
                            </th>
                        </tr>
                    </thead>

                    <!-- Body Tabel -->
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredSipd" :key="item.id">
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <!-- Kolom 1: No -->
                                <td class="px-4 py-4 text-center font-bold text-slate-400 border-r border-slate-800/80" x-text="index + 1"></td>

                                <!-- Kolom 2: Kode Program -->
                                <td class="px-3 py-4 text-center font-mono font-bold text-emerald-400 bg-emerald-950/10 border-r border-slate-800/80" x-text="item.program_kode"></td>

                                <!-- Kolom 3: Nama Program -->
                                <td class="px-4 py-4 font-semibold text-slate-200 bg-emerald-950/10 border-r border-slate-800/80" x-text="item.program_nama"></td>

                                <!-- Kolom 4: Kode Kegiatan -->
                                <td class="px-3 py-4 text-center font-mono font-bold text-amber-400 bg-amber-950/10 border-r border-slate-800/80" x-text="item.kegiatan_kode"></td>

                                <!-- Kolom 5: Nama Kegiatan -->
                                <td class="px-4 py-4 font-semibold text-slate-200 bg-amber-950/10 border-r border-slate-800/80" x-text="item.kegiatan_nama"></td>

                                <!-- Kolom 6: Kode Sub Kegiatan -->
                                <td class="px-3 py-4 text-center font-mono font-bold text-purple-400 bg-purple-950/10 border-r border-slate-800/80" x-text="item.sub_kegiatan_kode"></td>

                                <!-- Kolom 7: Nama Sub Kegiatan -->
                                <td class="px-4 py-4 font-semibold text-white bg-purple-950/10 border-r border-slate-800/80" x-text="item.sub_kegiatan_nama"></td>

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
                <!-- ========================================================================= -->
                <!-- MODAL DETAIL STRUKTUR PENGADAAN SIPD                                      -->
                <!-- ========================================================================= -->
                <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
                    <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-5 relative">
                        <!-- Tombol Close Corner -->
                <button type="button" @click="showDetailModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800">
                    <h3 class="text-base font-extrabold text-white">Detail Hierarki Pengadaan SIPD</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Klasifikasi Program, Kegiatan & Sub Kegiatan DPA SIPD</p>
                </div>

                <div class="space-y-3.5 text-xs" x-if="selectedItem">
                    <!-- Blok 1: Program -->
                    <div class="p-3.5 rounded-2xl bg-emerald-950/30 border border-emerald-500/30 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px]">1. Program Pengadaan SIPD</span>
                            <span class="font-mono font-bold text-emerald-300" x-text="selectedItem.program_kode"></span>
                        </div>
                        <p class="font-bold text-white text-sm" x-text="selectedItem.program_nama"></p>
                    </div>

                    <!-- Blok 2: Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-amber-950/30 border border-amber-500/30 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px]">2. Kegiatan Pengadaan SIPD</span>
                            <span class="font-mono font-bold text-amber-300" x-text="selectedItem.kegiatan_kode"></span>
                        </div>
                        <p class="font-bold text-white text-sm" x-text="selectedItem.kegiatan_nama"></p>
                    </div>

                    <!-- Blok 3: Sub Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-purple-950/30 border border-purple-500/30 space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px]">3. Sub Kegiatan Pengadaan SIPD</span>
                            <span class="font-mono font-bold text-purple-300" x-text="selectedItem.sub_kegiatan_kode"></span>
                        </div>
                        <p class="font-bold text-white text-sm" x-text="selectedItem.sub_kegiatan_nama"></p>
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
        <!-- MODAL TAMBAH JENIS PENGADAAN SIPD                                         -->
        <!-- ========================================================================= -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showAddModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white">Tambah Pengadaan SIPD Baru</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Input struktur 3 tingkatan (Program, Kegiatan, Sub Kegiatan)</p>
                </div>

                <form @submit.prevent="saveNew()" class="space-y-3.5 text-xs">
                    <!-- Blok 1: Program -->
                    <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                        <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Program Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Program</label>
                                <input type="text" x-model="newFormData.program_kode" placeholder="0.00.01" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Program</label>
                                <input type="text" x-model="newFormData.program_nama" placeholder="Contoh: Program Penunjang Urusan Pemda..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 2: Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                        <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Kegiatan Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Kegiatan</label>
                                <input type="text" x-model="newFormData.kegiatan_kode" placeholder="0.00.01.2.10" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Kegiatan Pengadaan</label>
                                <input type="text" x-model="newFormData.kegiatan_nama" placeholder="Contoh: Peningkatan Pelayanan BLUD..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 3: Sub Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                        <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub Kegiatan Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub Kegiatan</label>
                                <input type="text" x-model="newFormData.sub_kegiatan_kode" placeholder="0.00.01.2.10.0001" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Sub Kegiatan Pengadaan</label>
                                <input type="text" x-model="newFormData.sub_kegiatan_nama" placeholder="Contoh: Pelayanan dan Penunjang Pelayanan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
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
                            <span>Simpan Pengadaan</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL UBAH JENIS PENGADAAN SIPD                                           -->
        <!-- ========================================================================= -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative">
                <!-- Tombol Close Corner -->
                <button type="button" @click="showEditModal = false" class="absolute right-5 top-5 w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center text-sm font-bold transition-all">&times;</button>

                <!-- Header Center -->
                <div class="text-center pb-3 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-extrabold text-white">Ubah Data Pengadaan SIPD</h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Perbarui struktur 3 tingkatan (Program, Kegiatan, Sub Kegiatan)</p>
                </div>

                <form @submit.prevent="saveEdit()" class="space-y-3.5 text-xs">
                    <!-- Blok 1: Program -->
                    <div class="p-3.5 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 space-y-2">
                        <span class="font-bold text-emerald-400 uppercase tracking-wider text-[10px] block">1. Program Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Program</label>
                                <input type="text" x-model="editFormData.program_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-emerald-400 font-mono font-bold focus:border-emerald-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Program</label>
                                <input type="text" x-model="editFormData.program_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-emerald-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 2: Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-amber-950/20 border border-amber-500/30 space-y-2">
                        <span class="font-bold text-amber-400 uppercase tracking-wider text-[10px] block">2. Kegiatan Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Kegiatan</label>
                                <input type="text" x-model="editFormData.kegiatan_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-amber-400 font-mono font-bold focus:border-amber-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Kegiatan Pengadaan</label>
                                <input type="text" x-model="editFormData.kegiatan_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-amber-500">
                            </div>
                        </div>
                    </div>

                    <!-- Blok 3: Sub Kegiatan -->
                    <div class="p-3.5 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-2">
                        <span class="font-bold text-purple-400 uppercase tracking-wider text-[10px] block">3. Sub Kegiatan Pengadaan SIPD</span>
                        <div class="grid grid-cols-3 gap-2">
                            <div>
                                <label class="block text-slate-400 mb-1">Kode Sub Kegiatan</label>
                                <input type="text" x-model="editFormData.sub_kegiatan_kode" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-purple-400 font-mono font-bold focus:border-purple-500">
                            </div>
                            <div class="col-span-2">
                                <label class="block text-slate-400 mb-1">Nama Sub Kegiatan Pengadaan</label>
                                <input type="text" x-model="editFormData.sub_kegiatan_nama" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-white font-semibold focus:border-purple-500">
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
