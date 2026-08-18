<x-layout title="Katalog Pengadaan ASTAP - SIMAT-RK">
    @section('page-title', 'Katalog Pengadaan ASTAP')
    @section('breadcrumb', 'Master Utama / Pengadaan ASTAP')

    <div x-data="{
        searchQuery: '',
        statusFilter: 'all',
        programFilter: 'all',
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        selectedPengadaan: null,
        
        pengadaans: [
            {
                id: 1,
                kode: 'PGD-2026-001',
                nama: 'Pengadaan Alat Medis ICU & Radiologi',
                program_kode: '0.00.01',
                program_nama: 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
                kegiatan_kode: '0.00.01.2.10',
                kegiatan_nama: 'Peningkatan Pelayanan BLUD',
                sub_kegiatan_kode: '0.00.01.2.10.0002',
                sub_kegiatan_nama: 'Pengadaan Sarana dan Prasarana Pendukung Faskes',
                sumber_dana: 'DAK Kesehatan',
                tahun: '2026',
                nilai: 'Rp 1.450.000.000',
                status: 'Selesai',
                rekanan: 'PT. Medika Sejahtera Jaya',
                tgl_kontrak: '2026-02-10',
                keterangan: 'Pengadaan paket ventilator, patient monitor uMEC, dan defibrillator unit ICU'
            },
            {
                id: 2,
                kode: 'PGD-2026-002',
                nama: 'Renovasi Interior Paviliun Graha Amukti',
                program_kode: '1.02.02',
                program_nama: 'Program Pemenuhan Upaya Kesehatan (UKP & UKM)',
                kegiatan_kode: '1.02.02.2.02',
                kegiatan_nama: 'Penyediaan Fasilitas Pelayanan Kesehatan Rujukan',
                sub_kegiatan_kode: '1.02.02.2.02.0005',
                sub_kegiatan_nama: 'Pembangunan / Renovasi Gedung RS dan Sarana',
                sumber_dana: 'BLUD RSUD',
                tahun: '2026',
                nilai: 'Rp 450.000.000',
                status: 'Proses Lelang',
                rekanan: 'CV. Cipta Graha Utama',
                tgl_kontrak: '2026-04-15',
                keterangan: 'Peremajaan interior 24 kamar rawat inap VIP dan nurse station'
            },
            {
                id: 3,
                kode: 'PGD-2025-014',
                nama: 'Pengadaan Pompa Sentral & Jaringan Pipa Air Bersih',
                program_kode: '0.00.01',
                program_nama: 'Program Penunjang Urusan Pemerintah Daerah Kabupaten/kota',
                kegiatan_kode: '0.00.01.2.10',
                kegiatan_nama: 'Peningkatan Pelayanan BLUD',
                sub_kegiatan_kode: '0.00.01.2.10.0001',
                sub_kegiatan_nama: 'Pelayanan dan Penunjang Pelayanan BLUD',
                sumber_dana: 'BLUD RSUD',
                tahun: '2025',
                nilai: 'Rp 85.000.000',
                status: 'Selesai',
                rekanan: 'PT. Sumber Teknik Mandiri',
                tgl_kontrak: '2025-06-01',
                keterangan: 'Submersible pump dan pompa pendorong instalasi gizi & hemodialisa'
            },
            {
                id: 4,
                kode: 'PGD-2025-019',
                nama: 'Pengadaan Server SIMRS & Jaringan Fiber Optic',
                program_kode: '1.02.03',
                program_nama: 'Program Peningkatan Kapasitas SDM Kesehatan',
                kegiatan_kode: '1.02.03.2.01',
                kegiatan_nama: 'Pengembangan Mutu & Akreditasi Faskes',
                sub_kegiatan_kode: '1.02.03.2.01.0003',
                sub_kegiatan_nama: 'Pengadaan Sistem Informasi Kesehatan SIMRS',
                sumber_dana: 'APBD Kab. Bondowoso',
                tahun: '2025',
                nilai: 'Rp 650.000.000',
                status: 'Selesai',
                rekanan: 'PT. Solusi Data Nusantara',
                tgl_kontrak: '2025-08-20',
                keterangan: 'Infrastruktur datacenter, server Rekam Medis Elektronik (RME) & LAN RSUD'
            }
        ],

        get filteredPengadaans() {
            const query = (this.searchQuery || '').toLowerCase();
            return this.pengadaans.filter(item => {
                const matchSearch = (item.nama || '').toLowerCase().includes(query) || 
                                    (item.kode || '').toLowerCase().includes(query) || 
                                    (item.rekanan || '').toLowerCase().includes(query) ||
                                    (item.program_nama || '').toLowerCase().includes(query) ||
                                    (item.sub_kegiatan_nama || '').toLowerCase().includes(query);

                const matchStatus = this.statusFilter === 'all' || item.status === this.statusFilter;
                const matchProg = this.programFilter === 'all' || item.program_kode === this.programFilter;
                return matchSearch && matchStatus && matchProg;
            });
        },

        resetFilters() {
            this.searchQuery = '';
            this.statusFilter = 'all';
            this.programFilter = 'all';
        },

        openDetail(item) {
            this.selectedPengadaan = item;
            this.showDetailModal = true;
        }
    }" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-cyan-600/15 via-slate-900 to-slate-900 border border-cyan-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span>MANAJEMEN PENGADAAN BARANG & JASA (SIPD RSUD)</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Pengadaan ASTAP</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Pencatatan paket pengadaan aset tetap, verifikasi program/kegiatan SIPD, nomor kontrak SPK, rekanan vendor, serta pemantauan status proses lelang.
                    </p>
                </div>
                
                <a href="{{ route('pengadaan.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Pengadaan</span>
                </a>
            </div>

            <!-- Mini Summary KPI Cards Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-6 pt-6 border-t border-slate-800/80">
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">🛒</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Paket</span>
                        <span class="text-sm sm:text-base font-extrabold text-white" x-text="pengadaans.length + ' Paket'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 text-lg">💰</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Anggaran</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400 font-mono">Rp 2,63 M</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 text-lg">✅</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Selesai Kontrak</span>
                        <span class="text-sm sm:text-base font-extrabold text-teal-300">3 Paket</span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">⏳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Proses Lelang</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300">1 Paket</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Program SIPD Tabs -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Program:</span>
                    <button type="button" @click="programFilter = 'all'"
                        :class="programFilter === 'all' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        Semua Program
                    </button>
                    <button type="button" @click="programFilter = '0.00.01'"
                        :class="programFilter === '0.00.01' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏥 0.00.01 Pelayanan BLUD
                    </button>
                    <button type="button" @click="programFilter = '1.02.02'"
                        :class="programFilter === '1.02.02' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        🏢 1.02.02 Sarana UKP/UKM
                    </button>
                    <button type="button" @click="programFilter = '1.02.03'"
                        :class="programFilter === '1.02.03' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all">
                        💾 1.02.03 SIMRS & Akreditasi
                    </button>
                </div>

                <!-- Search Bar & Counter -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nomor pengadaan / nama paket / rekanan vendor / program SIPD..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-all">
                        <svg class="w-4 h-4 text-cyan-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-cyan-400 font-bold" x-text="filteredPengadaans.length"></span> dari <span class="text-white font-bold" x-text="pengadaans.length"></span> Paket
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>

                <!-- Dropdown Filters (Status & Sumber) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-3 border-t border-slate-800/80">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Proses Pengadaan</label>
                        <select x-model="statusFilter" class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs text-slate-200 focus:outline-none focus:border-cyan-500">
                            <option value="all">Semua Status Pengadaan</option>
                            <option value="Selesai">Selesai</option>
                            <option value="Proses Lelang">Proses Lelang</option>
                        </select>
                    </div>

                    <div class="flex items-end">
                        <span class="text-xs text-slate-400 italic">Seluruh paket pengadaan terhubung dengan Program, Kegiatan, dan Sub Kegiatan SIPD.</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Pengadaan -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-center">No. Registrasi</th>
                        <th class="px-4 py-3.5 text-center">Nama Paket Pengadaan</th>
                        <th class="px-4 py-3.5 text-center">Program & Sub Kegiatan SIPD</th>
                        <th class="px-4 py-3.5 text-center">Tahun</th>
                        <th class="px-4 py-3.5 text-center">Nilai Anggaran</th>
                        <th class="px-4 py-3.5 text-center">Status</th>
                        <th class="px-4 py-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredPengadaans" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            <td class="px-4 py-4 text-center font-mono font-semibold text-cyan-400" x-text="item.kode"></td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-white text-sm" x-text="item.nama"></div>
                                <div class="text-[11px] text-slate-400 mt-0.5" x-text="'Vendor: ' + item.rekanan"></div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-1 mb-1">
                                    <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-mono text-[10px] font-bold" x-text="item.program_kode"></span>
                                    <span class="text-[11px] text-slate-300 font-semibold truncate max-w-xs" x-text="item.program_nama"></span>
                                </div>
                                <div class="text-[10px] text-purple-300" x-text="'↳ ' + item.sub_kegiatan_nama"></div>
                            </td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-300 font-mono" x-text="item.tahun"></td>
                            <td class="px-4 py-4 text-center font-bold text-emerald-400 font-mono" x-text="item.nilai"></td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold"
                                    :class="item.status === 'Selesai' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30'"
                                    x-text="item.status"></span>
                            </td>
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                <button type="button" @click="openDetail(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>
                                <a :href="'/pengadaan/' + item.id + '/edit'"
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

        <!-- MODAL DETAIL PENGADAAN -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="text-cyan-400 font-bold">🛒</span>
                        <h3 class="text-base font-bold text-white">Detail Paket Pengadaan</h3>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                
                <div class="space-y-3 text-xs" x-if="selectedPengadaan">
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <span class="text-slate-400">Nomor Registrasi:</span>
                            <p class="font-mono font-bold text-cyan-400 text-sm" x-text="selectedPengadaan.kode"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Tahun Anggaran:</span>
                            <p class="font-mono font-bold text-white text-sm" x-text="selectedPengadaan.tahun"></p>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400">Nama Paket Barang / Jasa:</span>
                        <p class="font-bold text-white text-sm" x-text="selectedPengadaan.nama"></p>
                    </div>

                    <!-- 3 Blok SIPD -->
                    <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 space-y-2">
                        <span class="font-bold text-cyan-400 uppercase tracking-wider text-[10px] block">Hierarki Penganggaran SIPD</span>
                        <div>
                            <span class="text-[10px] text-emerald-400 font-bold block">1. Program SIPD:</span>
                            <p class="text-slate-200" x-text="selectedPengadaan.program_kode + ' - ' + selectedPengadaan.program_nama"></p>
                        </div>
                        <div>
                            <span class="text-[10px] text-amber-400 font-bold block">2. Kegiatan SIPD:</span>
                            <p class="text-slate-200" x-text="selectedPengadaan.kegiatan_kode + ' - ' + selectedPengadaan.kegiatan_nama"></p>
                        </div>
                        <div>
                            <span class="text-[10px] text-purple-400 font-bold block">3. Sub Kegiatan SIPD:</span>
                            <p class="text-slate-200" x-text="selectedPengadaan.sub_kegiatan_kode + ' - ' + selectedPengadaan.sub_kegiatan_nama"></p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                        <div>
                            <span class="text-slate-400">Rekanan / Vendor:</span>
                            <p class="font-semibold text-slate-200" x-text="selectedPengadaan.rekanan"></p>
                        </div>
                        <div>
                            <span class="text-slate-400">Total Nilai Kontrak:</span>
                            <p class="font-bold text-emerald-400 font-mono text-sm" x-text="selectedPengadaan.nilai"></p>
                        </div>
                    </div>

                    <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                        <span class="text-slate-500 block mb-1">Keterangan:</span>
                        <p class="text-slate-300" x-text="selectedPengadaan.keterangan"></p>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-800 flex justify-end">
                    <button type="button" @click="showDetailModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs">Tutup</button>
                </div>
            </div>
        </div>

    </div>
</x-layout>
