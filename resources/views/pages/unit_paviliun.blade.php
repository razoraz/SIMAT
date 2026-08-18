<x-layout title="Unit & Paviliun - SIMAT-RK">
    @section('page-title', 'Unit & Paviliun')
    @section('breadcrumb', 'Master Utama / Unit & Paviliun')

    <div x-data="{
        searchQuery: '',
        viewMode: 'grid', // 'grid' or 'table'
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        selectedUnit: null,

        units: [
            { id: 1, kode: 'UNIT-001', nama: 'Paviliun Graha Amukti', tipe: 'Rawat Inap VIP & VVIP', kepala: 'dr. H. Rahmat Hidayat, Sp.PD', total_aset: 48, total_nilai: 'Rp 4.250.000.000', kapasitas: '24 Kamar', pj_aset: 'Siti Aminah, A.Md.Kep' },
            { id: 2, kode: 'UNIT-002', nama: 'Instalasi Gawat Darurat (IGD)', tipe: 'Pelayanan Kedaruratan Medis', kepala: 'dr. Anita Wijaya, Sp.Em', total_aset: 65, total_nilai: 'Rp 2.850.000.000', kapasitas: '30 Bed Resusitasi', pj_aset: 'Ns. Hendra, S.Kep' },
            { id: 3, kode: 'UNIT-003', nama: 'Instalasi Radiologi & Imaging', tipe: 'Penunjang Diagnostik', kepala: 'dr. Bambang Soedibyo, Sp.Rad', total_aset: 28, total_nilai: 'Rp 6.120.000.000', kapasitas: '4 Ruang Pemeriksaan', pj_aset: 'Wahyu Hidayat, A.Md.Rad' },
            { id: 4, kode: 'UNIT-004', nama: 'Instalasi Bedah Sentral (IBS)', tipe: 'Operasi & Bedah Medis', kepala: 'dr. Fajar Santoso, Sp.B', total_aset: 52, total_nilai: 'Rp 5.400.000.000', kapasitas: '6 Kamar Operasi', pj_aset: 'Ns. Ratna Dewi, S.Kep' },
            { id: 5, kode: 'UNIT-005', nama: 'Instalasi Rawat Intensif (ICU)', tipe: 'Perawatan Intensif Kritis', kepala: 'dr. Maya Kusuma, Sp.An-TI', total_aset: 42, total_nilai: 'Rp 3.900.000.000', kapasitas: '12 Bed Terisolasi', pj_aset: 'Ns. Aditya, S.Kep' },
            { id: 6, kode: 'UNIT-006', nama: 'Instalasi Laboratorium Patologi', tipe: 'Penunjang Diagnostik Lab', kepala: 'dr. Yuniar Rahma, Sp.PK', total_aset: 38, total_nilai: 'Rp 1.950.000.000', kapasitas: '5 Ruang Analisa Lab', pj_aset: 'Diana Putri, A.Md.AK' }
        ],

        get filteredUnits() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.units.filter(item => {
                return (item.nama || '').toLowerCase().includes(query) || 
                       (item.kode || '').toLowerCase().includes(query) ||
                       (item.kepala || '').toLowerCase().includes(query) ||
                       (item.tipe || '').toLowerCase().includes(query);
            });
        },

        resetFilters() {
            this.searchQuery = '';
        },

        openDetail(item) {
            this.selectedUnit = item;
            this.showDetailModal = true;
        },

        openEdit(item) {
            this.selectedUnit = { ...item };
            this.showEditModal = true;
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-blue-600/15 via-slate-900 to-slate-900 border border-blue-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                        <span>LOKASI RUANGAN, PAVILIUN & INSTALASI RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Unit & Paviliun</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Daftar lokasi penempatan aset tetap, kepala penanggung jawab ruangan, volume barang terpasang, serta total nilai aset tiap paviliun.
                    </p>
                </div>
                
                <a href="{{ route('unit.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-bold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Unit Baru</span>
                </a>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Unit</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="units.length + ' Lokasi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Nilai Terdistribusi</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400 font-mono">Rp 24,47 M</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Aset Aktif</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300">273 Item</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">👨‍⚕️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Penanggung Jawab</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300">6 Kepala Unit</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Toggle Grid/Table & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
                <div class="relative flex-1 w-full">
                    <input type="text" x-model="searchQuery" placeholder="Cari unit ruangan / kode lokasi / nama kepala ruangan..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all">
                    <svg class="w-4 h-4 text-blue-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                </div>

                <div class="flex items-center space-x-2 shrink-0">
                    <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                        Menampilkan <span class="text-blue-400 font-bold" x-text="filteredUnits.length"></span> dari <span class="text-white font-bold" x-text="units.length"></span> Unit
                    </span>
                    
                    <!-- View Toggle -->
                    <div class="flex items-center bg-slate-950 border border-slate-800 rounded-xl p-1">
                        <button type="button" @click="viewMode = 'grid'" 
                                :class="viewMode === 'grid' ? 'bg-blue-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'"
                                class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center space-x-1">
                            <span>Grid</span>
                        </button>
                        <button type="button" @click="viewMode = 'table'" 
                                :class="viewMode === 'table' ? 'bg-blue-500 text-slate-950 font-bold' : 'text-slate-400 hover:text-white'"
                                class="px-2.5 py-1.5 rounded-lg text-xs transition-all flex items-center space-x-1">
                            <span>Tabel</span>
                        </button>
                    </div>

                    <button type="button" @click="resetFilters()"
                        class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                        🔄 Reset
                    </button>
                </div>
            </div>
        </div>

        <!-- VIEW MODE 1: GRID CARDS -->
        <div x-show="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <template x-for="item in filteredUnits" :key="item.id">
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl hover:border-blue-500/50 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between mb-3">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30" x-text="item.kode"></span>
                            <span class="text-xs font-semibold text-slate-400" x-text="item.kapasitas"></span>
                        </div>
                        <h3 class="text-base font-extrabold text-white group-hover:text-blue-400 transition-colors" x-text="item.nama"></h3>
                        <p class="text-xs text-slate-400 mt-0.5 mb-4" x-text="item.tipe"></p>

                        <div class="space-y-2 py-3 border-y border-slate-800/80 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Kepala Unit:</span>
                                <span class="font-semibold text-white" x-text="item.kepala"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Total Aset:</span>
                                <span class="font-bold text-cyan-400" x-text="item.total_aset + ' Item'"></span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Nilai Aset:</span>
                                <span class="font-bold text-emerald-400 font-mono" x-text="item.total_nilai"></span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 flex items-center justify-end space-x-1">
                        <button type="button" @click="openDetail(item)"
                            class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Detail</span>
                        </button>
                        <a :href="'/unit-paviliun/' + item.id + '/edit'"
                            class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Ubah</span>
                        </a>
                        <button type="button"
                            class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                    </div>
                </div>
            </template>
        </div>

        <!-- VIEW MODE 2: TABLE VIEW -->
        <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-center">Kode Unit</th>
                        <th class="px-4 py-3.5 text-center">Nama Unit / Paviliun</th>
                        <th class="px-4 py-3.5 text-center">Tipe Pelayanan</th>
                        <th class="px-4 py-3.5 text-center">Kepala Ruangan</th>
                        <th class="px-4 py-3.5 text-center">Total Aset</th>
                        <th class="px-4 py-3.5 text-center">Total Nilai</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredUnits" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            <td class="px-4 py-4 text-center font-mono font-semibold text-blue-400" x-text="item.kode"></td>
                            <td class="px-4 py-4 font-bold text-white" x-text="item.nama"></td>
                            <td class="px-4 py-4 text-center text-slate-300" x-text="item.tipe"></td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-200" x-text="item.kepala"></td>
                            <td class="px-4 py-4 text-center font-bold text-cyan-400" x-text="item.total_aset + ' Item'"></td>
                            <td class="px-4 py-4 text-center font-bold text-emerald-400 font-mono" x-text="item.total_nilai"></td>
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                <button type="button" @click="openDetail(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>
                                <a :href="'/unit-paviliun/' + item.id + '/edit'"
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Ubah</span>
                                </a>
                                <button type="button"
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

        <!-- MODAL DETAIL UNIT -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-blue-400 font-bold">🏥</span>
                        <h3 class="text-base font-bold text-white">Detail Ruangan & Inventaris Unit</h3>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <div class="space-y-3 text-xs" x-if="selectedUnit">
                    <div>
                        <span class="text-slate-400">Kode Ruangan:</span>
                        <p class="font-mono font-bold text-blue-400" x-text="selectedUnit.kode"></p>
                    </div>
                    <div>
                        <span class="text-slate-400">Nama Unit / Paviliun:</span>
                        <p class="font-bold text-white text-sm" x-text="selectedUnit.nama"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                        <div>
                            <span class="text-slate-400">Kepala Ruangan:</span>
                            <p class="font-semibold text-white" x-text="selectedUnit.kepala"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Penanggung Jawab Aset:</span>
                            <p class="font-semibold text-blue-300" x-text="selectedUnit.pj_aset"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Jumlah Aset Terpasang:</span>
                            <p class="font-bold text-cyan-400" x-text="selectedUnit.total_aset + ' Item'"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Nilai Aset:</span>
                            <p class="font-bold text-emerald-400 font-mono" x-text="selectedUnit.total_nilai"></p>
                        </div>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-800 flex justify-end">
                    <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH UNIT -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">+ Tambah Unit / Paviliun Baru</h3>
                    <button type="button" @click="showAddModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showAddModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Unit / Paviliun</label>
                        <input type="text" placeholder="Paviliun..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Tipe Pelayanan Ruangan</label>
                        <input type="text" placeholder="Rawat Inap / Penunjang / Administrasi" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Kepala Ruangan</label>
                        <input type="text" placeholder="dr..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-500 text-slate-950 font-bold">Simpan Unit</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL UBAH UNIT -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">✏️ Ubah Data Unit</h3>
                    <button type="button" @click="showEditModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Unit</label>
                        <input type="text" x-model="selectedUnit ? selectedUnit.nama : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Kepala Ruangan</label>
                        <input type="text" x-model="selectedUnit ? selectedUnit.kepala : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-blue-500 text-slate-950 font-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layout>
