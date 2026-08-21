<x-layout title="Mutasi Aset - SIMAT-RK">
    @section('page-title', 'Mutasi Aset')
    @section('breadcrumb', 'Master Utama / Mutasi Aset')

    <div x-data="{
        searchQuery: '',
        statusFilter: 'all',
        showAddModal: false,
        showEditModal: false,
        showDetailModal: false,
        showPrintBastModal: false,
        selectedMutasi: null,

        mutasis: [
            { id: 1, kode: 'MTS-2026-002', nama: 'Bed Pasien Crank Manual (3 Unit)', kode_barang: '1.3.2.02.01.08.002', qty: 3, satuan: 'Unit', asal: 'Ruang Rawat Inap Melati', tujuan: 'Paviliun Graha Amukti', tgl: '10 Ags 2026', pemohon: 'dr. H. Rahmat, Sp.PD', status: 'Disetujui', keterangan: 'Penambahan kapasitas ranjang cadangan ruang isolasi VIP' },
            { id: 2, kode: 'MTS-2026-005', nama: 'Infusion Pump Terumo TE-112', kode_barang: '1.3.2.02.01.01.012', qty: 2, satuan: 'Unit', asal: 'Instalasi Gawat Darurat (IGD)', tujuan: 'Ruang ICU Medis', tgl: '12 Ags 2026', pemohon: 'dr. Anita Wijaya, Sp.Em', status: 'Disetujui', keterangan: 'Kebutuhan mendesak monitoring cairan pasien kritis ICU' },
            { id: 3, kode: 'MTS-2026-009', nama: 'Komputer Desktop All-in-One Core i5', kode_barang: '1.3.2.10.01.02.003', qty: 1, satuan: 'Unit', asal: 'Gudang Inventaris Pusat', tujuan: 'Poliklinik Jantung Terpadu', tgl: '14 Ags 2026', pemohon: 'Ns. Bagus, S.Kep', status: 'Menunggu Persetujuan', keterangan: 'Penggantian PC lama unit entri resep elektronik' }
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
        },

        openPrintBast(item) {
            this.selectedMutasi = {
                ...item,
                bast_nomor: '034 / MTS / 430.10.7 / 2026',
                hari: 'Jumat',
                tanggal_angka: '15',
                bulan: 'Agustus',
                tahun: '2026',
                sk_bupati_nomor: '188.45/969/430.4.2/2024',
                sk_bupati_tanggal: '02 Januari 2025',
                pengurus_nama: 'ESTU PRATIKA SARI, SST',
                pengurus_nip: '198805122011012005',
                pengurus_jabatan: 'Pengurus Barang Aset RSUD',
                pj_asal_nama: item.pemohon || 'dr. H. Rahmat, Sp.PD',
                pj_asal_nip: '198004152006041008',
                pj_asal_jabatan: 'Kepala Ruangan ' + item.asal,
                pj_tujuan_nama: 'dr. ADHI SUDARMADJI',
                pj_tujuan_nip: '198410272009021003',
                pj_tujuan_jabatan: 'Kepala Ruangan ' + item.tujuan,
                signed: true
            };
            this.showPrintBastModal = true;
        },

        toggleSignMutasi(item) {
            if (!item) return;
            const target = this.mutasis.find(m => m.id === item.id) || item;
            if (target.signed) {
                target.signed = false;
                target.tgl_signed = '-';
                target.qr_hash = '';
                target.status = 'Menunggu Persetujuan';
                item.signed = false;
                item.tgl_signed = '-';
                item.qr_hash = '';
                item.status = 'Menunggu Persetujuan';
                alert('↩️ Tanda tangan digital BSrE BAST Mutasi (' + (target.kode || target.bast_nomor) + ') berhasil dibatalkan.');
            } else {
                target.signed = true;
                const now = new Date();
                target.tgl_signed = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                target.qr_hash = 'BSRE-KOESNANDI-MTS-' + Date.now();
                target.status = 'Disetujui';
                item.signed = true;
                item.tgl_signed = target.tgl_signed;
                item.qr_hash = target.qr_hash;
                item.status = 'Disetujui';
                alert('✍️ BAST Mutasi (' + (target.kode || target.bast_nomor) + ') berhasil ditandatangani secara digital (QR Code BSrE Aktif)!');
            }
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
                        Pencatatan perpindahan lokasi unit penempatan barang antar ruangan, pelacakan riwayat pergerakan aset, dan pencetakan Berita Acara Mutasi resmi.
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
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Menunggu Persetujuan</span>
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
                    <button type="button" @click="statusFilter = 'Ditolak'"
                        :class="statusFilter === 'Ditolak' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        ❌ Ditolak
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
                        <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap">No</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">No. Mutasi</th>
                        <th class="px-4 py-3.5 text-left min-w-[220px]">Nama Barang</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Ruangan Asal</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Ruangan Tujuan</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tgl Pengajuan</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Status</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredMutasis" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                            <td class="px-4 py-4 text-center font-mono font-semibold text-rose-400 whitespace-nowrap" x-text="item.kode"></td>
                            <td class="px-4 py-4 font-bold text-white" x-text="item.nama"></td>
                            <td class="px-4 py-4 text-center text-slate-300 whitespace-nowrap" x-text="item.asal"></td>
                            <td class="px-4 py-4 text-center font-semibold text-rose-300 whitespace-nowrap" x-text="item.tujuan"></td>
                            <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap" x-text="item.tgl"></td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none border shadow-sm select-none"
                                    :class="item.status === 'Disetujui' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/15 text-amber-300 border-amber-500/30'"
                                    x-text="item.status"></span>
                            </td>
                            <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                
                                <!-- Tombol Cetak Berita Acara Mutasi -->
                                <button type="button" @click="openPrintBast(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-purple-500/15 text-purple-300 hover:bg-purple-500/25 border border-purple-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                    <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    <span>Cetak Berita Acara</span>
                                </button>

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

        <!-- MODAL PRINTER BERITA ACARA SERAH TERIMA (BAST) MUTASI ASET RESMI RSUD KOESNANDI -->
        <div x-show="showPrintBastModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintBastModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto my-6">
                
                <!-- Action Header Modal Print -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 print:hidden">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-500/20 text-purple-300 border border-purple-500/30 flex items-center justify-center font-bold text-lg">
                            📄
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Pratinjau Berita Acara Mutasi Aset (BAST)</h3>
                            <p class="text-xs text-slate-400">Dokumen resmi Berita Acara Serah Terima Pemindahan Barang Antar Ruangan</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="button" @click="terimaMutasi(selectedMutasi)"
                            class="px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all active:scale-95 flex items-center space-x-1"
                            title="Tanda Tangan Digital BSrE">
                            <span>✍️ TTD BSrE</span>
                        </button>

                        <button type="button" @click="window.print()" 
                                class="px-4 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak Sekarang</span>
                        </button>
                        <button type="button" @click="showPrintBastModal = false" class="p-2 rounded-xl bg-slate-800 text-slate-400 hover:text-white text-base font-bold">&times;</button>
                    </div>
                </div>

                <!-- SURAT BAST MUTASI FISIK (PRINTABLE LEMBAR RESMI KERTAS F4/A4) -->
                <template x-if="selectedMutasi">
                    <div class="bg-white text-black p-8 sm:p-10 rounded-2xl font-serif shadow-2xl text-xs space-y-4 print:p-0 print:shadow-none print:bg-transparent">
                        
                        <!-- KOP SURAT RESMI RSUD DR. H. KOESNANDI BONDOWOSO -->
                        <div class="border-b-[3px] border-black pb-1 mb-0.5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                                </div>
                                <div class="flex-1 text-center font-sans text-black">
                                    <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr. H. KOESNADI</h3>
                                    <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Pierre Tendean No. 3 Telepon (0332) 421974. Fax.0332 422311</p>
                                    <p class="text-[10px] leading-tight">Website: rsudrkoesnadi.go.id, Email: rsu.koesnadi@gmail.com</p>
                                    <div class="flex items-center justify-between mt-0.5 px-4">
                                        <span></span>
                                        <h4 class="font-bold text-xs tracking-[0.3em] uppercase">B O N D O W O S O</h4>
                                        <span class="text-[9.5px] font-sans font-semibold">Kode Pos: 68214</span>
                                    </div>
                                </div>
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="w-16 h-16 object-contain">
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-black mb-4"></div>

                        <!-- JUDUL & NOMOR SURAT -->
                        <div class="text-center font-sans mb-3">
                            <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">BERITA ACARA SERAH TERIMA MUTASI ASET</h3>
                            <p class="text-[11px] font-semibold">Nomor : <span x-text="selectedMutasi.bast_nomor"></span></p>
                        </div>

                        <!-- PARAGRAF PEMBUKA -->
                        <p class="text-justify mb-2 leading-relaxed font-sans">
                            Pada hari ini <strong x-text="selectedMutasi.hari || 'Jumat'"></strong> tanggal <strong x-text="selectedMutasi.tanggal_angka || '15'"></strong> bulan <strong x-text="selectedMutasi.bulan || 'Agustus'"></strong> tahun <strong x-text="selectedMutasi.tahun || '2026'"></strong>, yang bertanda tangan di bawah ini :
                        </p>

                        <!-- PIHAK PERTAMA (RUANGAN ASAL / PENGURUS BARANG) -->
                        <div class="space-y-0.5 mb-2 ml-4 font-sans text-[10.5px]">
                            <div class="flex"><div class="w-32 font-medium">Nama (Pihak I - Asal)</div><div class="w-4">:</div><div class="flex-1 font-bold uppercase" x-text="selectedMutasi.pj_asal_nama"></div></div>
                            <div class="flex"><div class="w-32 font-medium">NIP</div><div class="w-4">:</div><div class="flex-1 font-mono" x-text="selectedMutasi.pj_asal_nip"></div></div>
                            <div class="flex"><div class="w-32 font-medium">Jabatan / Ruangan</div><div class="w-4">:</div><div class="flex-1 font-bold" x-text="selectedMutasi.pj_asal_jabatan + ' (' + selectedMutasi.asal + ')'"></div></div>
                        </div>

                        <!-- PIHAK KEDUA (RUANGAN TUJUAN) -->
                        <p class="mb-1 leading-relaxed font-sans">Menyerahkan mutasi barang aset kepada :</p>
                        <div class="space-y-0.5 mb-3 ml-4 font-sans text-[10.5px]">
                            <div class="flex"><div class="w-32 font-medium">Nama (Pihak II - Tujuan)</div><div class="w-4">:</div><div class="flex-1 font-bold uppercase" x-text="selectedMutasi.pj_tujuan_nama"></div></div>
                            <div class="flex"><div class="w-32 font-medium">NIP</div><div class="w-4">:</div><div class="flex-1 font-mono" x-text="selectedMutasi.pj_tujuan_nip"></div></div>
                            <div class="flex"><div class="w-32 font-medium">Jabatan / Ruangan</div><div class="w-4">:</div><div class="flex-1 font-bold uppercase" x-text="selectedMutasi.pj_tujuan_jabatan + ' (' + selectedMutasi.tujuan + ')'"></div></div>
                        </div>

                        <!-- TABEL RINCIAN MUTASI ASET -->
                        <div class="my-3">
                            <table class="w-full text-center border-collapse border border-black text-[10px] font-sans">
                                <thead>
                                    <tr class="bg-gray-200 font-bold border-b border-black">
                                        <th class="border border-black px-2 py-1.5 w-8">No</th>
                                        <th class="border border-black px-3 py-1.5 text-left">Nama Barang / Aset</th>
                                        <th class="border border-black px-3 py-1.5 font-mono">Kode Rekening 108</th>
                                        <th class="border border-black px-2 py-1.5 w-12">Vol</th>
                                        <th class="border border-black px-2 py-1.5 w-14">Satuan</th>
                                        <th class="border border-black px-2 py-1.5">Kondisi</th>
                                        <th class="border border-black px-3 py-1.5 text-left">Alasan / Urgensi Pemindahan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-black">
                                        <td class="border border-black px-2 py-1.5">1</td>
                                        <td class="border border-black px-3 py-1.5 text-left font-bold" x-text="selectedMutasi.nama"></td>
                                        <td class="border border-black px-3 py-1.5 font-mono" x-text="selectedMutasi.kode_barang || '1.3.2.02.01.08.002'"></td>
                                        <td class="border border-black px-2 py-1.5 font-bold" x-text="selectedMutasi.qty || 1"></td>
                                        <td class="border border-black px-2 py-1.5" x-text="selectedMutasi.satuan || 'Unit'"></td>
                                        <td class="border border-black px-2 py-1.5 font-bold text-emerald-800">Baik</td>
                                        <td class="border border-black px-3 py-1.5 text-left text-[9.5px]" x-text="selectedMutasi.keterangan"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- KALIMAT PENUTUP -->
                        <p class="text-justify mb-4 leading-relaxed font-sans">
                            Demikian Berita Acara Mutasi Aset ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagai kelengkapan administrasi SIMAT-RK RSUD Dr. H. Koesnandi Bondowoso.
                        </p>

                        <!-- TANDA TANGAN DUAL BSR-E -->
                        <div class="grid grid-cols-2 gap-8 text-center font-sans text-[10px] mt-6">
                            <div>
                                <p>Yang Menyerahkan (Ruangan Asal)</p>
                                <p class="font-bold" x-text="selectedMutasi.pj_asal_jabatan"></p>
                                <div class="h-20 flex items-center justify-center py-1">
                                    <div class="flex items-center space-x-2 p-1.5 border border-purple-600 bg-purple-50 rounded">
                                        <div class="w-11 h-11 bg-white border border-black p-0.5 flex items-center justify-center">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-RSUD-MUTASI-ASAL" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[7.5px] leading-tight text-purple-950 font-sans">
                                            <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                            <div>Penanggung Jawab Ruangan Asal</div>
                                            <div class="font-mono">Terverifikasi BSrE SIMAT</div>
                                        </div>
                                    </div>
                                </div>
                                <p class="font-bold underline text-[11px] uppercase" x-text="selectedMutasi.pj_asal_nama"></p>
                                <p class="font-mono text-[9.5px]" x-text="'NIP. ' + selectedMutasi.pj_asal_nip"></p>
                            </div>

                            <div>
                                <p>Yang Menerima (Ruangan Tujuan)</p>
                                <p class="font-bold" x-text="selectedMutasi.pj_tujuan_jabatan"></p>
                                <div class="h-20 flex items-center justify-center py-1">
                                    <div class="flex items-center space-x-2 p-1.5 border border-emerald-600 bg-emerald-50 rounded">
                                        <div class="w-11 h-11 bg-white border border-black p-0.5 flex items-center justify-center">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-RSUD-MUTASI-TUJUAN" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[7.5px] leading-tight text-emerald-950 font-sans">
                                            <div class="font-bold">DITANDATANGANI ELEKTRONIK</div>
                                            <div>Penanggung Jawab Ruangan Tujuan</div>
                                            <div class="font-mono">Terverifikasi BSrE SIMAT</div>
                                        </div>
                                    </div>
                                </div>
                                <p class="font-bold underline text-[11px] uppercase" x-text="selectedMutasi.pj_tujuan_nama"></p>
                                <p class="font-mono text-[9.5px]" x-text="'NIP. ' + selectedMutasi.pj_tujuan_nip"></p>
                            </div>
                        </div>

                    </div>
                </template>

            </div>
        </div>

    </div>
</x-layout>
