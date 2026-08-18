<x-layout title="Distribusi ASTAP - SIMAT-RK">
    @section('page-title', 'Distribusi ASTAP')
    @section('breadcrumb', 'Master Utama / Distribusi ASTAP')

    <div x-data="{
        searchQuery: '',
        unitFilter: 'all',
        statusFilter: 'all',
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        selectedDistribusi: null,

        distribusis: [
            { id: 1, kode: 'DST-2026-004', nama: 'Bed Patient Electric 3 Crank', tujuan: 'Paviliun Graha Amukti', tgl: '12 Ags 2026', penerima: 'Siti Aminah, A.Md.Kep', status: 'Telah Diterima', bast_nomor: 'BAST-2026-044', keterangan: 'Penempatan di Ruang VIP 01-04' },
            { id: 2, kode: 'DST-2026-008', nama: 'Patient Monitor 6 Parameter', tujuan: 'Instalasi Gawat Darurat (IGD)', tgl: '14 Ags 2026', penerima: 'Ns. Hendra, S.Kep', status: 'Telah Diterima', bast_nomor: 'BAST-2026-048', keterangan: 'Cadangan darurat ruang resusitasi' },
            { id: 3, kode: 'DST-2026-012', nama: 'Submersible Pump Franklin 7.5 HP', tujuan: 'Ruang Utility & Pompa Sentral', tgl: '15 Ags 2026', penerima: 'Budi Santoso, ST', status: 'Dalam Pengiriman', bast_nomor: 'BAST-2026-051', keterangan: 'Pemasangan oleh tim teknisi IPSRS' },
            { id: 4, kode: 'DST-2026-015', nama: 'Laptop Operasional Asus ExpertBook', tujuan: 'Instalasi Rekam Medis', tgl: '16 Ags 2026', penerima: 'Dewi Lestari, A.Md.RMIK', status: 'Menunggu Konfirmasi', bast_nomor: 'BAST-2026-055', keterangan: 'Peremajaan unit input data SIMRS' }
        ],

        get filteredDistribusis() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.distribusis.filter(item => {
                const matchSearch = (item.nama || '').toLowerCase().includes(query) || (item.kode || '').toLowerCase().includes(query) || (item.penerima || '').toLowerCase().includes(query);
                const matchUnit = this.unitFilter === 'all' || item.tujuan === this.unitFilter;
                const matchStatus = this.statusFilter === 'all' || item.status === this.statusFilter;
                return matchSearch && matchUnit && matchStatus;
            });
        },

        resetFilters() {
            this.searchQuery = '';
            this.unitFilter = 'all';
            this.statusFilter = 'all';
        },

        openDetail(item) {
            this.selectedDistribusi = item;
            this.showDetailModal = true;
        },

        openEdit(item) {
            this.selectedDistribusi = { ...item };
            this.showEditModal = true;
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-teal-600/15 via-slate-900 to-slate-900 border border-teal-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                        <span>PENYERAHAN & ALOKASI BARANG ASET RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Distribusi ASTAP</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pengelolaan alokasi penyerahan dan penyaluran aset dari gudang pusat ke paviliun rawat inap, poliklinik, dan instalasi RSUD.
                    </p>
                </div>
                
                <a href="{{ route('distribusi.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>+ Input Distribusi Baru</span>
                </a>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">🚚</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Distribusi</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="distribusis.length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Telah Diterima</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300">2 Transaksi</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">🚛</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Dalam Kirim</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300">1 Transaksi</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">⏳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Menunggu Konfirmasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300">1 Transaksi</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Unit Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Unit:</span>
                    <button type="button" @click="unitFilter = 'all'"
                        :class="unitFilter === 'all' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        Semua Unit
                    </button>
                    <button type="button" @click="unitFilter = 'Paviliun Graha Amukti'"
                        :class="unitFilter === 'Paviliun Graha Amukti' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        🏥 Pav. Graha Amukti
                    </button>
                    <button type="button" @click="unitFilter = 'Instalasi Gawat Darurat (IGD)'"
                        :class="unitFilter === 'Instalasi Gawat Darurat (IGD)' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        🚑 IGD Utama
                    </button>
                    <button type="button" @click="unitFilter = 'Ruang Utility & Pompa Sentral'"
                        :class="unitFilter === 'Ruang Utility & Pompa Sentral' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        ⚙️ Utility & Pompa
                    </button>
                    <button type="button" @click="unitFilter = 'Instalasi Rekam Medis'"
                        :class="unitFilter === 'Instalasi Rekam Medis' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        📁 Rekam Medis
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nomor distribusi / nama aset / pegawai penerima..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all">
                        <svg class="w-4 h-4 text-teal-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-teal-400 font-bold" x-text="filteredDistribusis.length"></span> dari <span class="text-white font-bold" x-text="distribusis.length"></span> Data
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-800/80">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Penyerahan Distribusi</label>
                        <select x-model="statusFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-teal-500">
                            <option value="all">Semua Status</option>
                            <option value="Telah Diterima">Telah Diterima</option>
                            <option value="Dalam Pengiriman">Dalam Pengiriman</option>
                            <option value="Menunggu Konfirmasi">Menunggu Konfirmasi</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <span class="text-xs text-slate-400 italic">Setiap distribusi otomatis menghasilkan nomor dokumen BAST resmi.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Distribusi -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-center">No. Distribusi</th>
                        <th class="px-4 py-3.5 text-center">Nama Barang / ASTAP</th>
                        <th class="px-4 py-3.5 text-center">Tujuan Unit / Paviliun</th>
                        <th class="px-4 py-3.5 text-center">Tgl Distribusi</th>
                        <th class="px-4 py-3.5 text-center">Penerima</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredDistribusis" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            <td class="px-4 py-4 text-center font-mono font-semibold text-teal-400" x-text="item.kode"></td>
                            <td class="px-4 py-4 font-bold text-white" x-text="item.nama"></td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-200" x-text="item.tujuan"></td>
                            <td class="px-4 py-4 text-center font-mono text-slate-300" x-text="item.tgl"></td>
                            <td class="px-4 py-4 text-center font-semibold text-white" x-text="item.penerima"></td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold"
                                    :class="{
                                        'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': item.status === 'Telah Diterima',
                                        'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': item.status === 'Dalam Pengiriman',
                                        'bg-amber-500/20 text-amber-300 border border-amber-500/30': item.status === 'Menunggu Konfirmasi'
                                    }"
                                    x-text="item.status"></span>
                            </td>
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                <a href="{{ route('bast.index') }}"
                                    class="px-2.5 py-1.5 rounded-xl bg-purple-500/15 text-purple-300 hover:bg-purple-500/25 border border-purple-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>BAST</span>
                                </a>
                                <button type="button" @click="openDetail(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>
                                <a :href="'/distribusi/' + item.id + '/edit'"
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

        <!-- MODAL DETAIL DISTRIBUSI -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-teal-400 font-bold">🚚</span>
                        <h3 class="text-base font-bold text-white">Detail Alokasi Distribusi</h3>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <div class="space-y-3 text-xs" x-if="selectedDistribusi">
                    <div>
                        <span class="text-slate-400">Nomor Distribusi:</span>
                        <p class="font-mono font-bold text-teal-400" x-text="selectedDistribusi.kode"></p>
                    </div>
                    <div>
                        <span class="text-slate-400">Nama Barang:</span>
                        <p class="font-bold text-white text-sm" x-text="selectedDistribusi.nama"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                        <div>
                            <span class="text-slate-400">Tujuan Unit:</span>
                            <p class="font-semibold text-white" x-text="selectedDistribusi.tujuan"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Penerima Barang:</span>
                            <p class="font-semibold text-white" x-text="selectedDistribusi.penerima"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Tanggal Distribusi:</span>
                            <p class="font-semibold text-slate-300" x-text="selectedDistribusi.tgl"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Nomor BAST:</span>
                            <p class="font-mono font-semibold text-purple-300" x-text="selectedDistribusi.bast_nomor"></p>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                        <span class="text-slate-500 block mb-1">Catatan Distribusi:</span>
                        <p class="text-slate-300" x-text="selectedDistribusi.keterangan"></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-800 flex justify-end">
                    <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH DISTRIBUSI -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">+ Input Distribusi Barang Baru</h3>
                    <button type="button" @click="showAddModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showAddModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Barang ASTAP</label>
                        <input type="text" placeholder="Pilih atau cari barang..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Tujuan Unit / Paviliun</label>
                        <select class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                            <option>Paviliun Graha Amukti</option>
                            <option>Instalasi Gawat Darurat (IGD)</option>
                            <option>Ruang Utility & Pompa Sentral</option>
                            <option>Instalasi Radiologi</option>
                            <option>Unit Rawat Inap & ICU</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Pegawai Penerima</label>
                        <input type="text" placeholder="Nama pegawai penerima..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-teal-500 text-slate-950 font-bold">Simpan Distribusi</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL UBAH DISTRIBUSI -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">✏️ Ubah Status Distribusi</h3>
                    <button type="button" @click="showEditModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Barang</label>
                        <input type="text" x-model="selectedDistribusi ? selectedDistribusi.nama : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Penerima Barang</label>
                        <input type="text" x-model="selectedDistribusi ? selectedDistribusi.penerima : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Status Distribusi</label>
                        <select x-model="selectedDistribusi ? selectedDistribusi.status : 'Telah Diterima'" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                            <option value="Telah Diterima">Telah Diterima</option>
                            <option value="Dalam Pengiriman">Dalam Pengiriman</option>
                            <option value="Menunggu Konfirmasi">Menunggu Konfirmasi</option>
                        </select>
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-teal-500 text-slate-950 font-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layout>
