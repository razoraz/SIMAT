<x-layout title="Pemeliharaan ASTAP - SIMAT-RK">
    @section('page-title', 'Pemeliharaan ASTAP')
    @section('breadcrumb', 'Master Utama / Pemeliharaan ASTAP')

    <div x-data="{
        searchQuery: '',
        statusFilter: 'all',
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        selectedPemeliharaan: null,

        pemeliharaans: [
            { id: 1, kode: 'MTN-2026-003', nama: 'CT-Scan 128 Slice Siemens', jenis: 'Kalibrasi Rutin & QC BAPETEN', tgl: '05 Ags 2026', biaya: 'Rp 25.000.000', pelaksana: 'PT. Siemens Healthcare Indonesia', status: 'Selesai', keterangan: 'Hasil uji fungsi akurat dan sertifikat kalibrasi terbit' },
            { id: 2, kode: 'MTN-2026-007', nama: 'Submersible Pump Pompa Sentral', jenis: 'Penggantian Seal & Bearing', tgl: '10 Ags 2026', biaya: 'Rp 4.500.000', pelaksana: 'Teknisi IPSRS RSUD', status: 'Dalam Pengerjaan', keterangan: 'Sedang dibongkar untuk pembersihan kerak impeller' },
            { id: 3, kode: 'MTN-2026-009', nama: 'Instalasi Jaringan Pipa Oksigen IGD', jenis: 'Perbaikan Kebocoran Valve Outlet', tgl: '14 Ags 2026', biaya: 'Rp 8.200.000', pelaksana: 'CV. Gas Medika Sentosa', status: 'Menunggu Sparepart', keterangan: 'Menunggu pengiriman flowmeter dan digital sensor dari Surabaya' },
            { id: 4, kode: 'MTN-2026-012', nama: 'Gedung Paviliun Graha Amukti Lt 1', jenis: 'Pengecatan & Perbaikan Plafon', tgl: '15 Ags 2026', biaya: 'Rp 15.000.000', pelaksana: 'Tim Pemeliharaan Sarpras', status: 'Dalam Pengerjaan', keterangan: 'Perapian koridor utama ruang rawat inap VIP' }
        ],

        get filteredPemeliharaans() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.pemeliharaans.filter(item => {
                const matchSearch = (item.nama || '').toLowerCase().includes(query) || (item.kode || '').toLowerCase().includes(query) || (item.pelaksana || '').toLowerCase().includes(query);
                const matchStatus = this.statusFilter === 'all' || item.status === this.statusFilter;
                return matchSearch && matchStatus;
            });
        },

        resetFilters() {
            this.searchQuery = '';
            this.statusFilter = 'all';
        },

        openDetail(item) {
            this.selectedPemeliharaan = item;
            this.showDetailModal = true;
        },

        openEdit(item) {
            this.selectedPemeliharaan = { ...item };
            this.showEditModal = true;
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-amber-600/15 via-slate-900 to-slate-900 border border-amber-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                        <span>PERAWATAN, SERVIS & KALIBRASI ASET RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Pemeliharaan ASTAP</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Monitoring servis berkala alat medis, kalibrasi instrumen kedokteran, perbaikan sarana gedung, dan pencatatan biaya pemeliharaan.
                    </p>
                </div>
                
                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                <a href="{{ route('pemeliharaan.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Catat Servis / Perbaikan</span>
                </a>
                @endif
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">🛠️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Servis</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="pemeliharaans.length + ' Kegiatan'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">✅</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Selesai Dikerjakan</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300">1 Kegiatan</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">⚙️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Dalam Pengerjaan</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300">2 Kegiatan</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-rose-500/10 text-rose-400 text-lg">⏳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Tunggu Sparepart</span>
                        <span class="text-sm sm:text-base font-extrabold text-rose-300">1 Kegiatan</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Status Servis Tabs -->
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Status:</span>
                    <button type="button" @click="statusFilter = 'all'"
                        :class="statusFilter === 'all' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua Status
                    </button>
                    <button type="button" @click="statusFilter = 'Dalam Pengerjaan'"
                        :class="statusFilter === 'Dalam Pengerjaan' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        ⚙️ Dalam Pengerjaan
                    </button>
                    <button type="button" @click="statusFilter = 'Menunggu Sparepart'"
                        :class="statusFilter === 'Menunggu Sparepart' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        ⏳ Menunggu Sparepart
                    </button>
                    <button type="button" @click="statusFilter = 'Selesai'"
                        :class="statusFilter === 'Selesai' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        ✅ Selesai Dikerjakan
                    </button>
                </div>

                <!-- Search Bar & Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nama alat / nomor registrasi servis / teknisi..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500 transition-all">
                        <svg class="w-4 h-4 text-amber-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-amber-400 font-bold" x-text="filteredPemeliharaans.length"></span> dari <span class="text-white font-bold" x-text="pemeliharaans.length"></span> Servis
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Pemeliharaan -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap">No</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">No. Servis</th>
                        <th class="px-4 py-3.5 text-left min-w-[220px]">Nama Aset</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Jenis Pemeliharaan</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tgl Servis</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Biaya Servis</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredPemeliharaans" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                            <td class="px-4 py-4 text-center font-mono font-semibold text-amber-400 whitespace-nowrap" x-text="item.kode"></td>
                            <td class="px-4 py-4 font-bold text-white" x-text="item.nama"></td>
                            <td class="px-4 py-4 text-center text-slate-300 whitespace-nowrap" x-text="item.jenis"></td>
                            <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap" x-text="item.tgl"></td>
                            <td class="px-4 py-4 text-center font-bold text-emerald-400 font-mono whitespace-nowrap" x-text="item.biaya"></td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none border shadow-sm select-none"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': item.status === 'Selesai',
                                        'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': item.status === 'Dalam Pengerjaan',
                                        'bg-rose-500/15 text-rose-300 border-rose-500/30': item.status === 'Menunggu Sparepart'
                                    }"
                                    x-text="item.status"></span>
                            </td>
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                <button type="button" @click="openDetail(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>
                                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                <a :href="'/pemeliharaan/' + item.id + '/edit'"
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Ubah</span>
                                </a>
                                <button type="button"
                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                                @endif
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- MODAL DETAIL PEMELIHARAAN -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-amber-400 font-bold">🛠️</span>
                        <h3 class="text-base font-bold text-white">Detail Riwayat Pemeliharaan</h3>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <div class="space-y-3 text-xs" x-if="selectedPemeliharaan">
                    <div>
                        <span class="text-slate-400">Nomor Registrasi Servis:</span>
                        <p class="font-mono font-bold text-amber-400" x-text="selectedPemeliharaan.kode"></p>
                    </div>
                    <div>
                        <span class="text-slate-400">Nama Aset:</span>
                        <p class="font-bold text-white text-sm" x-text="selectedPemeliharaan.nama"></p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                        <div>
                            <span class="text-slate-400">Jenis Tindakan:</span>
                            <p class="font-semibold text-white" x-text="selectedPemeliharaan.jenis"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Biaya Servis:</span>
                            <p class="font-bold text-emerald-400 font-mono" x-text="selectedPemeliharaan.biaya"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Pelaksana Teknisi:</span>
                            <p class="font-semibold text-slate-300" x-text="selectedPemeliharaan.pelaksana"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Status Servis:</span>
                            <p class="font-semibold text-amber-300" x-text="selectedPemeliharaan.status"></p>
                        </div>
                    </div>
                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                        <span class="text-slate-500 block mb-1">Catatan Hasil Servis:</span>
                        <p class="text-slate-300" x-text="selectedPemeliharaan.keterangan"></p>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-slate-800 flex justify-end">
                    <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-bold text-xs">Tutup</button>
                </div>
            </div>
        </div>

        <!-- MODAL TAMBAH PEMELIHARAAN -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showAddModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">+ Catat Pemeliharaan / Servis</h3>
                    <button type="button" @click="showAddModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showAddModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Aset yang Diservis</label>
                        <input type="text" placeholder="Pilih barang..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Jenis Tindakan Pemeliharaan</label>
                        <input type="text" placeholder="Kalibrasi / Servis Berkala / Perbaikan..." class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Perkiraan Biaya (Rp)</label>
                        <input type="text" placeholder="Rp 5.000.000" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white font-mono">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Teknisi Pelaksana</label>
                        <input type="text" placeholder="IPSRS RSUD / Vendor Rekanan" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-slate-950 font-bold">Simpan Log Servis</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL UBAH PEMELIHARAAN -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">✏️ Ubah Status Pemeliharaan</h3>
                    <button type="button" @click="showEditModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Nama Aset</label>
                        <input type="text" x-model="selectedPemeliharaan ? selectedPemeliharaan.nama : ''" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Status Servis</label>
                        <select x-model="selectedPemeliharaan ? selectedPemeliharaan.status : 'Selesai'" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-white">
                            <option value="Selesai">Selesai</option>
                            <option value="Dalam Pengerjaan">Dalam Pengerjaan</option>
                            <option value="Menunggu Sparepart">Menunggu Sparepart</option>
                        </select>
                    </div>
                    <div class="pt-4 flex items-center justify-end space-x-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300">Batal</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 text-slate-950 font-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</x-layout>
