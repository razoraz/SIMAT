<x-layout title="Mutasi Aset - SIMAT-RK">
    @section('page-title', 'Mutasi Aset')
    @section('breadcrumb', 'Master Utama / Mutasi Aset')

    <div x-data="{
        searchQuery: '',
        statusFilter: 'all',
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        selectedMutasi: null,

        mutasis: [
            { id: 1, kode: 'MTS-2026-002', nama: 'Bed Pasien Crank Manual (3 Unit)', asal: 'Ruang Rawat Inap Melati', tujuan: 'Paviliun Graha Amukti', tgl: '10 Ags 2026', pemohon: 'dr. H. Rahmat, Sp.PD', status: 'Disetujui', keterangan: 'Penambahan kapasitas ranjang cadangan ruang isolasi VIP' },
            { id: 2, kode: 'MTS-2026-005', nama: 'Infusion Pump Terumo TE-112', asal: 'Instalasi Gawat Darurat (IGD)', tujuan: 'Ruang ICU Medis', tgl: '12 Ags 2026', pemohon: 'dr. Anita Wijaya, Sp.Em', status: 'Disetujui', keterangan: 'Kebutuhan mendesak monitoring cairan pasien kritis ICU' },
            { id: 3, kode: 'MTS-2026-009', nama: 'Komputer Desktop All-in-One Core i5', asal: 'Gudang Inventaris Pusat', tujuan: 'Poliklinik Jantung Terpadu', tgl: '14 Ags 2026', pemohon: 'Ns. Bagus, S.Kep', status: 'Menunggu Persetujuan', keterangan: 'Penggantian PC lama unit entri resep elektronik' }
        ],

        get filteredMutasis() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.mutasis.filter(item => {
                const matchSearch = (item.nama || '').toLowerCase().includes(query) || (item.kode || '').toLowerCase().includes(query) || (item.tujuan || '').toLowerCase().includes(query) || (item.asal || '').toLowerCase().includes(query);
                const matchStatus = this.statusFilter === 'all' || item.status === this.statusFilter;
                return matchSearch && matchStatus;
            });
        },

        resetFilters() {
            this.searchQuery = '';
            this.statusFilter = 'all';
        },

        openDetail(item) {
            this.selectedMutasi = item;
            this.showDetailModal = true;
        },

        openEdit(item) {
            this.selectedMutasi = { ...item };
            this.showEditModal = true;
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-rose-600/15 via-slate-900 to-slate-900 border border-rose-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-rose-500/20 text-rose-300 border border-rose-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-rose-400 animate-pulse"></span>
                        <span>PERPINDAHAN & MUTASI RUANGAN ASET RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Mutasi Aset</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pencatatan perpindahan lokasi unit penempatan barang antar ruangan, tracking riwayat pergerakan aset, dan otorisasi persetujuan mutasi.
                    </p>
                </div>
                
                <a href="{{ route('mutasi.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-bold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Pengajuan Mutasi Baru</span>
                </a>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-400 text-lg">🔄</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Mutasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="mutasis.length + ' Pengajuan'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">✅</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Disetujui</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300">2 Pengajuan</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">⏳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Menunggu ACC</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300">1 Pengajuan</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Unit Terlibat</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300">5 Ruangan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Status Mutasi Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Status:</span>
                    <button type="button" @click="statusFilter = 'all'"
                        :class="statusFilter === 'all' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        Semua Status
                    </button>
                    <button type="button" @click="statusFilter = 'Disetujui'"
                        :class="statusFilter === 'Disetujui' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        ✅ Disetujui
                    </button>
                    <button type="button" @click="statusFilter = 'Menunggu Persetujuan'"
                        :class="statusFilter === 'Menunggu Persetujuan' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        ⏳ Menunggu Persetujuan
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nomor mutasi / nama aset / ruangan asal / ruangan tujuan..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 transition-all">
                        <svg class="w-4 h-4 text-rose-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-rose-400 font-bold" x-text="filteredMutasis.length"></span> dari <span class="text-white font-bold" x-text="mutasis.length"></span> Mutasi
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Mutasi -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-center">No. Mutasi</th>
                        <th class="px-4 py-3.5 text-center">Nama Barang</th>
                        <th class="px-4 py-3.5 text-center">Ruangan Asal</th>
                        <th class="px-4 py-3.5 text-center">Ruangan Tujuan</th>
                        <th class="px-4 py-3.5 text-center">Tgl Pengajuan</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredMutasis" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            <td class="px-4 py-4 text-center font-mono font-semibold text-rose-400" x-text="item.kode"></td>
                            <td class="px-4 py-4 font-bold text-white" x-text="item.nama"></td>
                            <td class="px-4 py-4 text-center text-slate-300" x-text="item.asal"></td>
                            <td class="px-4 py-4 text-center font-semibold text-rose-300" x-text="item.tujuan"></td>
                            <td class="px-4 py-4 text-center font-mono text-slate-300" x-text="item.tgl"></td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold"
                                    :class="item.status === 'Disetujui' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'"
                                    x-text="item.status"></span>
                            </td>
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                <button type="button" @click="openDetail(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>
                                <a :href="'/mutasi-aset/' + item.id + '/edit'"
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

        <!-- MODAL DETAIL MUTASI -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-rose-400 font-bold">🔄</span>
                        <h3 class="text-base font-bold text-white">Detail Pengajuan Mutasi</h3>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <div class="space-y-3 text-xs" x-if="selectedMutasi">
                    <div>
                        <span class="text-slate-400">Nomor Mutasi:</span>
                        <p class="font-mono font-bold text-rose-400" x-text="selectedMutasi.kode"></p>
                    </div>
                    <div>
                        <span class="text-slate-400">Nama Barang:</span>
                        <p class="font-bold text-white text-sm" x-text="selectedMutasi.nama"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                        <div>
                            <span class="text-slate-400">Ruangan Asal:</span>
                            <p class="font-semibold text-slate-300" x-text="selectedMutasi.asal"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Ruangan Tujuan:</span>
                            <p class="font-semibold text-rose-300" x-text="selectedMutasi.tujuan"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Pemohon Mutasi:</span>
                            <p class="font-semibold text-white" x-text="selectedMutasi.pemohon"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Status Otorisasi:</span>
                            <p class="font-semibold text-emerald-400" x-text="selectedMutasi.status"></p>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                        <span class="text-slate-500 block mb-1">Alasan / Catatan Mutasi:</span>
                        <p class="text-slate-300" x-text="selectedMutasi.keterangan"></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-800 flex justify-end">
                    <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH MUTASI -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">+ Ajukan Mutasi Aset Baru</h3>
                    <button type="button" @click="showAddModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showAddModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Pilih Barang yang Dimutasi</label>
                        <input type="text" placeholder="Cari nama atau barcode barang..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Ruangan Asal</label>
                            <input type="text" placeholder="Asal..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                        <div>
                            <label class="block text-slate-300 font-semibold mb-1">Ruangan Tujuan</label>
                            <input type="text" placeholder="Tujuan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                        </div>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Alasan Pemindahan / Mutasi</label>
                        <textarea placeholder="Uraikan alasan mutasi..." rows="2" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2 text-white"></textarea>
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-500 text-slate-950 font-bold">Kirim Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL UBAH MUTASI -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">✏️ Ubah Status Mutasi</h3>
                    <button type="button" @click="showEditModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Barang</label>
                        <input type="text" x-model="selectedMutasi ? selectedMutasi.nama : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Status Persetujuan</label>
                        <select x-model="selectedMutasi ? selectedMutasi.status : 'Disetujui'" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                            <option value="Disetujui">Disetujui</option>
                            <option value="Menunggu Persetujuan">Menunggu Persetujuan</option>
                            <option value="Ditolak">Ditolak</option>
                        </select>
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-rose-500 text-slate-950 font-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layout>
