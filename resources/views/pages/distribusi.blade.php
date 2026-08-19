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
        showPrintBastModal: false,
        showEditBastForm: false,
        selectedDistribusi: null,

        distribusis: [
            {
                id: 1,
                kode: 'DST-2026-004',
                nama: 'Kasur Matras spoon & Ranjang Pasien',
                tujuan: 'Front Office (FO) & Rawat Inap',
                tgl: '13 Ags 2026',
                penerima: 'ESTU PRATIKA SARI, SST',
                status: 'Telah Diterima',
                bast_nomor: '032 / 034 / 430.10.7 / 2026',
                hari: 'Kamis',
                tanggal_angka: '13',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                pengurus_nama: 'BUDI HARTONO, S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang',
                pengurus_ruangan: 'Gudang Perbekalan',
                pj_nama: 'ESTU PRATIKA SARI, SST',
                pj_nip: '199409242023212002',
                pj_jabatan: 'Supervisor Front Office',
                pj_ruangan: 'FO',
                pj_jabatan_ttd: 'Kepala Ruangan FO R.Inap',
                signed: true,
                tgl_signed: '13/08/2026 11:30 WIB',
                keterangan: 'BLUD-2024 u/Petugas Jaga FO R.Inap',
                items: [
                    {
                        no: 1,
                        nama_barang: 'Kasur Matras spoon',
                        merk_type: 'Mattres Cover (Matras Spon) / Mattress Foam Adult 200x90x10',
                        qty: 2,
                        satuan: 'Unit',
                        kondisi: 'Baik',
                        keterangan: 'BLUD-2024 u/Petugas Jaga FO R.Inap'
                    },
                    {
                        no: 2,
                        nama_barang: 'Bed Patient Manual 2 Crank',
                        merk_type: 'Paramount Bed Model Standard with Side Rail',
                        qty: 2,
                        satuan: 'Unit',
                        kondisi: 'Baik',
                        keterangan: 'Ruang Rawat Observasi FO'
                    }
                ]
            },
            {
                id: 2,
                kode: 'DST-2026-008',
                nama: 'Patient Monitor 6 Parameter Mindray',
                tujuan: 'Instalasi Gawat Darurat (IGD)',
                tgl: '14 Ags 2026',
                penerima: 'Ns. Hendra, S.Kep',
                status: 'Telah Diterima',
                bast_nomor: '034 / 034 / 430.10.7 / 2026',
                hari: 'Jumat',
                tanggal_angka: '14',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                pengurus_nama: 'BUDI HARTONO, S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang',
                pengurus_ruangan: 'Gudang Perbekalan',
                pj_nama: 'Ns. Hendra, S.Kep',
                pj_nip: '19880719 201202 1 002',
                pj_jabatan: 'Kepala Ruangan IGD',
                pj_ruangan: 'IGD',
                pj_jabatan_ttd: 'Kepala Ruangan IGD',
                signed: true,
                tgl_signed: '14/08/2026 14:15 WIB',
                keterangan: 'Pengadaan DAK Kesehatan 2024 u/IGD Kritis',
                items: [
                    {
                        no: 1,
                        nama_barang: 'Patient Monitor 6 Parameter',
                        merk_type: 'Mindray ePM 12 / Display 12.1 Inch Multi-Lead ECG',
                        qty: 4,
                        satuan: 'Unit',
                        kondisi: 'Baik',
                        keterangan: 'Zona Kritis Resusitasi IGD'
                    },
                    {
                        no: 2,
                        nama_barang: 'Emergency Crash Cart Trolley',
                        merk_type: 'Stainless Steel 5 Laci + Tiang Infus & CPR Board',
                        qty: 2,
                        satuan: 'Unit',
                        kondisi: 'Baik',
                        keterangan: 'Peralatan Siaga Resusitasi IGD'
                    }
                ]
            },
            {
                id: 3,
                kode: 'DST-2026-012',
                nama: 'Submersible Pump Franklin 7.5 HP',
                tujuan: 'Ruang Utility & Pompa Sentral',
                tgl: '15 Ags 2026',
                penerima: 'Budi Santoso, ST',
                status: 'Dalam Pengiriman',
                bast_nomor: '037 / 034 / 430.10.7 / 2026',
                hari: 'Sabtu',
                tanggal_angka: '15',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                pengurus_nama: 'BUDI HARTONO, S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang',
                pengurus_ruangan: 'Gudang Perbekalan',
                pj_nama: 'Budi Santoso, ST',
                pj_nip: '19820510 200902 1 004',
                pj_jabatan: 'Kepala Instalasi IPSRS',
                pj_ruangan: 'IPSRS / Utility',
                pj_jabatan_ttd: 'Kepala Instalasi IPSRS',
                signed: false,
                tgl_signed: '-',
                keterangan: 'Pemasangan & Testing oleh Tim Teknisi IPSRS',
                items: [
                    {
                        no: 1,
                        nama_barang: 'Submersible Pump Franklin 7.5 HP',
                        merk_type: 'Franklin Electric 4 Inch Super Stainless 3-Phase',
                        qty: 1,
                        satuan: 'Unit',
                        kondisi: 'Baik',
                        keterangan: 'Sumur Dalam Sentral Gedung Utama'
                    }
                ]
            },
            {
                id: 4,
                kode: 'DST-2026-015',
                nama: 'Laptop Operasional Asus ExpertBook',
                tujuan: 'Instalasi Rekam Medis',
                tgl: '16 Ags 2026',
                penerima: 'Dewi Lestari, A.Md.RMIK',
                status: 'Menunggu Konfirmasi',
                bast_nomor: '039 / 034 / 430.10.7 / 2026',
                hari: 'Minggu',
                tanggal_angka: '16',
                bulan: 'Agustus',
                tahun: '2026',
                tahun_anggaran: '2025',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                pengurus_nama: 'BUDI HARTONO, S.Sos',
                pengurus_nip: '19760229 200801 1 010',
                pengurus_jabatan: 'Pengurus Barang',
                pengurus_ruangan: 'Gudang Perbekalan',
                pj_nama: 'Dewi Lestari, A.Md.RMIK',
                pj_nip: '19930814 201703 2 006',
                pj_jabatan: 'Penanggung Jawab SIMRS Rekam Medis',
                pj_ruangan: 'Rekam Medis',
                pj_jabatan_ttd: 'Kepala Instalasi Rekam Medis',
                signed: false,
                tgl_signed: '-',
                keterangan: 'Peremajaan Unit Entri Data SIMRS & EMR',
                items: [
                    {
                        no: 1,
                        nama_barang: 'Laptop Asus ExpertBook B1',
                        merk_type: 'Core i7-1355U, 16GB DDR4, 512GB SSD, Windows 11 Pro',
                        qty: 3,
                        satuan: 'Unit',
                        kondisi: 'Baik',
                        keterangan: 'Loket Pendaftaran & Coding Klaim BPJS'
                    }
                ]
            }
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
        },

        openPrintBast(item) {
            this.selectedDistribusi = { ...item };
            this.showPrintBastModal = true;
        },

        printCurrentBast() {
            window.print();
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
                
                <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                    <button type="button" @click="openPrintBast(distribusis[0])"
                        class="px-3.5 py-2.5 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs shadow-lg transition-all flex items-center space-x-1.5 active:scale-95">
                        <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span>📑 Cetak Berita Acara Distribusi</span>
                    </button>
                    @endif

                    <a href="{{ route('distribusi.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>Input Distribusi Baru</span>
                    </a>
                </div>
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
                        <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap">No</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">No. Distribusi</th>
                        <th class="px-4 py-3.5 text-left min-w-[220px]">Nama Barang / ASTAP</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tujuan Unit / Paviliun</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tgl Distribusi</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Penerima</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredDistribusis" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                            <td class="px-4 py-4 text-center font-mono font-semibold text-teal-400 whitespace-nowrap" x-text="item.kode"></td>
                            <td class="px-4 py-4 font-bold text-white" x-text="item.nama"></td>
                            <td class="px-4 py-4 text-center font-semibold text-slate-200 whitespace-nowrap" x-text="item.tujuan"></td>
                            <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap" x-text="item.tgl"></td>
                            <td class="px-4 py-4 text-center font-semibold text-white whitespace-nowrap" x-text="item.penerima"></td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none border shadow-sm select-none"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': item.status === 'Telah Diterima',
                                        'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': item.status === 'Dalam Pengiriman',
                                        'bg-amber-500/15 text-amber-300 border-amber-500/30': item.status === 'Menunggu Konfirmasi'
                                    }"
                                    x-text="item.status"></span>
                            </td>
                            <td class="px-4 py-4 text-center space-x-1 whitespace-nowrap">
                                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                <button type="button" @click="openPrintBast(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-purple-500/15 text-purple-300 hover:bg-purple-500/25 border border-purple-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    <span>BAST</span>
                                </button>
                                @endif
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

        <!-- ========================================================================= -->
        <!-- MODAL CETAK BERITA ACARA PENYERAHAN BARANG (DISTRIBUSI RSUD KOESNANDI)   -->
        <!-- FORMAT RESMI 100% SESUAI FISIK STANDAR RUMAH SAKIT UMUM DAERAH           -->
        <!-- ========================================================================= -->
        <div x-show="showPrintBastModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintBastModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal (Hidden When Printed) -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 text-sm">📑</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Berita Acara Penyerahan Barang (Distribusi)</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedDistribusi ? ('Penyerahan kepada: ' + (selectedDistribusi.pj_nama || selectedDistribusi.penerima) + ' (' + selectedDistribusi.tujuan + ')') : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="showEditBastForm = !showEditBastForm"
                            class="px-3.5 py-2 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <span x-text="showEditBastForm ? '✕ Tutup Form Edit' : '✏️ Edit Data & Pejabat BAST'"></span>
                        </button>

                        <button type="button" @click="printCurrentBast()"
                            class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak (Print / PDF)</span>
                        </button>

                        <button type="button" @click="showPrintBastModal = false" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Formulir Cepat Live Edit Pejabat & Data Surat BAST Distribusi -->
                <template x-if="selectedDistribusi">
                    <div x-show="showEditBastForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-purple-500/40 text-xs space-y-3 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                            <span class="font-bold text-purple-300 text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>✏️ Sesuaikan Data Surat, Tanggal, Pihak Menyerahkan & Pihak Penerima:</span>
                            </span>
                            <span class="text-[10px] text-emerald-400 font-mono">Teks pada lembar BAST otomatis sinkron live</span>
                        </div>

                        <!-- Edit Nomor Surat & Waktu Pelaksanaan -->
                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2.5">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor Surat BAST</label>
                                <input type="text" x-model="selectedDistribusi.bast_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-purple-300 font-mono font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Hari</label>
                                <input type="text" x-model="selectedDistribusi.hari" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal & Bulan</label>
                                <div class="flex space-x-1">
                                    <input type="text" x-model="selectedDistribusi.tanggal_angka" placeholder="13" class="w-12 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-white text-xs text-center font-bold">
                                    <input type="text" x-model="selectedDistribusi.bulan" placeholder="Agustus" class="flex-1 bg-slate-900 border border-slate-700 rounded-lg px-2 py-1.5 text-white text-xs">
                                </div>
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun</label>
                                <input type="text" x-model="selectedDistribusi.tahun" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs text-center font-mono font-bold">
                            </div>
                        </div>

                        <!-- Edit Dasar Hukum SK Bupati -->
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 pt-1">
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tahun Anggaran Aset</label>
                                <input type="text" x-model="selectedDistribusi.tahun_anggaran" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Nomor SK Bupati Bondowoso</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_nomor" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1">Tanggal SK Bupati</label>
                                <input type="text" x-model="selectedDistribusi.sk_bupati_tanggal" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                            </div>
                        </div>

                        <!-- Edit 2 Pihak: Pengurus Barang & Yang Menerima -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 pt-2 border-t border-slate-800/80">
                            <!-- Pihak 1 (Yang Menyerahkan - Pengurus Barang) -->
                            <div class="p-3 rounded-xl bg-slate-900/80 border border-emerald-500/30 space-y-2">
                                <span class="text-[10px] font-bold text-emerald-400 block uppercase">1. Pihak Yang Menyerahkan (Pengurus Barang):</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Nama Lengkap & Gelar</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-emerald-400 font-bold text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Jabatan</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Ruangan / Unit Kerja</label>
                                        <input type="text" x-model="selectedDistribusi.pengurus_ruangan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                    </div>
                                </div>
                            </div>

                            <!-- Pihak 2 (Yang Menerima - Sub Admin / Unit) -->
                            <div class="p-3 rounded-xl bg-slate-900/80 border border-purple-500/30 space-y-2">
                                <span class="text-[10px] font-bold text-purple-400 block uppercase">2. Pihak Yang Menerima (Sub Admin / Ruangan):</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Nama Lengkap & Gelar</label>
                                        <input type="text" x-model="selectedDistribusi.pj_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-purple-300 font-bold text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">NIP</label>
                                        <input type="text" x-model="selectedDistribusi.pj_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Jabatan</label>
                                        <input type="text" x-model="selectedDistribusi.pj_jabatan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px]">Ruangan / Paviliun</label>
                                        <input type="text" x-model="selectedDistribusi.pj_ruangan" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs font-bold">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[9px]">Judul Tanda Tangan Penerima</label>
                                    <input type="text" x-model="selectedDistribusi.pj_jabatan_ttd" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white text-xs">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- LEMBAR DOKUMEN CETAK ASLI BAST PENYERAHAN BARANG (KERTAS PUTIH STANDAR RUMAH SAKIT) -->
                <template x-if="selectedDistribusi">
                    <div id="print-area-bast" class="bg-white text-black p-6 sm:p-10 rounded-2xl shadow-xl max-h-[70vh] overflow-y-auto font-serif text-[11px] leading-relaxed select-text print:max-h-none print:overflow-visible print:p-0 print:m-0 print:shadow-none print:rounded-none">
                        
                        <!-- KOP SURAT RESMI RSUD -->
                        <div class="border-b-[3px] border-black pb-1 mb-0.5">
                            <div class="flex items-center justify-between gap-4">
                                <!-- Logo Daerah Bondowoso -->
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo Daerah" class="w-16 h-16 object-contain">
                                </div>

                                <!-- Teks Header Kop -->
                                <div class="flex-1 text-center font-sans text-black">
                                    <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI</h3>
                                    <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Pierre Tendean No. 3 Telepon (0332) 421974. Fax.0332 422311</p>
                                    <p class="text-[10px] leading-tight">Website: rsudrkoesnadi.go.id, Email: rsu.koesnadi@gmail.com</p>
                                    <div class="flex items-center justify-between mt-0.5 px-4">
                                        <span class="text-[9px] font-sans"></span>
                                        <h4 class="font-bold text-xs tracking-[0.3em] uppercase">B O N D O W O S O</h4>
                                        <span class="text-[9.5px] font-sans font-semibold">Kode Pos: 68214</span>
                                    </div>
                                </div>

                                <!-- Logo RSUD Koesnandi -->
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-black mb-4"></div>

                        <!-- JUDUL & NOMOR SURAT -->
                        <div class="text-center font-sans mb-3">
                            <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">BERITA ACARA PENYERAHAN BARANG</h3>
                            <p class="text-[11px] font-semibold">Nomor : <span x-text="selectedDistribusi.bast_nomor"></span></p>
                        </div>

                        <!-- PARAGRAF PEMBUKA -->
                        <p class="text-justify mb-2 leading-relaxed">
                            Pada hari ini <strong x-text="selectedDistribusi.hari || 'Kamis'"></strong> tanggal <strong x-text="selectedDistribusi.tanggal_angka || '13'"></strong> bulan <strong x-text="selectedDistribusi.bulan || 'Agustus'"></strong> tahun <strong x-text="selectedDistribusi.tahun || '2026'"></strong>, yang bertanda tangan di bawah ini :
                        </p>

                        <!-- IDENTITAS PIHAK PERTAMA (PENGURUS BARANG) -->
                        <div class="space-y-0.5 mb-2 ml-4 font-sans text-[10.5px]">
                            <div class="flex">
                                <div class="w-28 font-medium">Nama</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 font-bold uppercase" x-text="selectedDistribusi.pengurus_nama"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">NIP</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 font-mono" x-text="selectedDistribusi.pengurus_nip"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">Jabatan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1" x-text="selectedDistribusi.pengurus_jabatan"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">Ruangan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1" x-text="selectedDistribusi.pengurus_ruangan || 'Gudang Perbekalan'"></div>
                            </div>
                        </div>

                        <!-- DASAR HUKUM SK BUPATI -->
                        <p class="text-justify mb-2 leading-relaxed">
                            Dalam hal ini selaku Pengurus Barang Aset Tahun Anggaran <span x-text="selectedDistribusi.tahun_anggaran || '2025'"></span> Rumah Sakit Umum Dr. H. Koesnandi Bondowoso, berdasarkan Surat Keputusan Bupati Kabupaten Bondowoso sesuai Nomor : <strong x-text="selectedDistribusi.sk_bupati_nomor || '188.45/969/430.4.2/2024'"></strong> tanggal <strong x-text="selectedDistribusi.sk_bupati_tanggal || '02 Januari 2025'"></strong>
                        </p>

                        <!-- IDENTITAS PIHAK KEDUA (PENERIMA / SUB ADMIN) -->
                        <p class="mb-1 leading-relaxed">Dengan ini menyerahkan barang kepada :</p>
                        <div class="space-y-0.5 mb-3 ml-4 font-sans text-[10.5px]">
                            <div class="flex">
                                <div class="w-28 font-medium">Nama</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 font-bold uppercase" x-text="selectedDistribusi.pj_nama"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">NIP</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 font-mono" x-text="selectedDistribusi.pj_nip"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">Jabatan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1" x-text="selectedDistribusi.pj_jabatan"></div>
                            </div>
                            <div class="flex">
                                <div class="w-28 font-medium">Ruangan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 uppercase font-bold" x-text="selectedDistribusi.pj_ruangan || selectedDistribusi.tujuan"></div>
                            </div>
                        </div>

                        <!-- TABEL RESMI DAFTAR BARANG YANG DISERAHKAN (7 KOLOM DENGAN SUB-KOLOM KONDISI) -->
                        <div class="my-3">
                            <table class="w-full text-center border-collapse border border-black text-[10px] font-sans">
                                <thead>
                                    <tr class="bg-gray-200 font-bold border-b border-black">
                                        <th rowspan="2" class="border border-black px-2 py-1.5 w-8">No</th>
                                        <th rowspan="2" class="border border-black px-3 py-1.5 text-left">Uraian Barang</th>
                                        <th rowspan="2" class="border border-black px-3 py-1.5 text-left">Merk /Type</th>
                                        <th rowspan="2" class="border border-black px-2 py-1.5 w-12">Vol</th>
                                        <th rowspan="2" class="border border-black px-2 py-1.5 w-14">Satuan</th>
                                        <th colspan="3" class="border border-black px-2 py-1">Kondisi</th>
                                        <th rowspan="2" class="border border-black px-3 py-1.5 text-left w-48">Keterangan</th>
                                    </tr>
                                    <tr class="bg-gray-200 font-bold border-b border-black text-[9px]">
                                        <th class="border border-black px-1.5 py-0.5 w-10">Baik</th>
                                        <th class="border border-black px-1.5 py-0.5 w-10">KB</th>
                                        <th class="border border-black px-1.5 py-0.5 w-10">Rusak</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, idx) in selectedDistribusi.items" :key="idx">
                                        <tr>
                                            <td class="border border-black px-2 py-1.5 font-mono text-center" x-text="idx + 1"></td>
                                            <td class="border border-black px-3 py-1.5 text-left font-semibold" x-text="item.nama_barang"></td>
                                            <td class="border border-black px-3 py-1.5 text-left text-[9.5px]" x-text="item.merk_type || item.spesifikasi"></td>
                                            <td class="border border-black px-2 py-1.5 font-mono font-bold text-center" x-text="item.qty"></td>
                                            <td class="border border-black px-2 py-1.5 text-center" x-text="item.satuan"></td>
                                            <td class="border border-black px-1.5 py-1.5 text-center font-bold" x-text="item.kondisi === 'Baik' ? 'Baik' : ''"></td>
                                            <td class="border border-black px-1.5 py-1.5 text-center font-bold" x-text="item.kondisi === 'KB' || item.kondisi === 'Kurang Baik' ? 'KB' : ''"></td>
                                            <td class="border border-black px-1.5 py-1.5 text-center font-bold" x-text="item.kondisi === 'Rusak' || item.kondisi === 'Rusak Berat' ? 'Rusak' : ''"></td>
                                            <td class="border border-black px-3 py-1.5 text-left text-[9px]" x-text="item.keterangan || selectedDistribusi.keterangan"></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>

                        <!-- KALIMAT PENUTUP -->
                        <p class="text-justify mb-4 leading-relaxed">
                            Demikian Berita Acara Penyerahan Barang ini dibuat rangkap secukupnya untuk dipergunakan sebagaimana mestinya.
                        </p>

                        <!-- AREA 2 TANDA TANGAN (KIRI: YANG MENYERAHKAN, KANAN: YANG MENERIMA) -->
                        <div class="grid grid-cols-2 gap-8 text-center font-sans text-[10px] mt-6">
                            <!-- Yang Menyerahkan -->
                            <div class="space-y-1">
                                <p class="font-normal">Yang Menyerahkan</p>
                                <p class="font-bold">Pengurus Barang Aset</p>
                                
                                <!-- Tanda Tangan Area -->
                                <div class="h-20 flex items-center justify-center py-1">
                                    <template x-if="selectedDistribusi.signed">
                                        <div class="flex items-center space-x-2 p-1.5 border border-emerald-600 bg-emerald-50 rounded">
                                            <div class="w-11 h-11 bg-white border border-black p-0.5 flex items-center justify-center">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-RSUD-KOESNANDI-PENGURUS-ASET" class="w-full h-full object-contain">
                                            </div>
                                            <div class="text-left text-[7.5px] leading-tight text-emerald-950 font-sans">
                                                <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                                <div>Pengurus Barang Aset RSUD</div>
                                                <div class="font-mono">Terverifikasi BSrE SIMAT</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!selectedDistribusi.signed">
                                        <div class="h-16 flex items-center justify-center text-slate-400 italic text-[10px]">
                                            ( Tanda Tangan )
                                        </div>
                                    </template>
                                </div>

                                <p class="font-bold underline text-[11px] uppercase tracking-wide" x-text="selectedDistribusi.pengurus_nama"></p>
                                <p class="font-mono text-[9.5px]" x-text="'NIP. ' + selectedDistribusi.pengurus_nip"></p>
                            </div>

                            <!-- Yang Menerima -->
                            <div class="space-y-1">
                                <p class="font-normal">Yang Menerima</p>
                                <p class="font-bold" x-text="selectedDistribusi.pj_jabatan_ttd || ('Kepala Ruangan ' + (selectedDistribusi.pj_ruangan || selectedDistribusi.tujuan))"></p>
                                
                                <!-- Tanda Tangan Area -->
                                <div class="h-20 flex items-center justify-center py-1">
                                    <template x-if="selectedDistribusi.signed">
                                        <div class="flex items-center space-x-2 p-1.5 border border-purple-600 bg-purple-50 rounded">
                                            <div class="w-11 h-11 bg-white border border-black p-0.5 flex items-center justify-center">
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-RSUD-KOESNANDI-DST-UNIT" class="w-full h-full object-contain">
                                            </div>
                                            <div class="text-left text-[7.5px] leading-tight text-purple-950 font-sans">
                                                <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                                <div>Penerima / Sub-Admin Ruangan</div>
                                                <div class="font-mono" x-text="selectedDistribusi.tgl_signed || '13/08/2026 11:30 WIB'"></div>
                                            </div>
                                        </div>
                                    </template>
                                    <template x-if="!selectedDistribusi.signed">
                                        <div class="h-16 flex items-center justify-center text-slate-400 italic text-[10px]">
                                            ( Tanda Tangan )
                                        </div>
                                    </template>
                                </div>

                                <p class="font-bold underline text-[11px] uppercase tracking-wide" x-text="selectedDistribusi.pj_nama"></p>
                                <p class="font-mono text-[9.5px]" x-text="'Nip. ' + selectedDistribusi.pj_nip"></p>
                            </div>
                        </div>

                    </div>
                </template>

            </div>
        </div>

    </div>
</x-layout>
