<x-layout title="Unit & Paviliun - SIMAT-RK">
    @section('page-title', 'Unit & Paviliun')
    @section('breadcrumb', 'Master Utama / Unit & Paviliun')

    <script>
        function unitPaviliunCatalog() {
            return {
                searchQuery: '',
                viewMode: 'grid', // 'grid' or 'table'
                showAddModal: false,
                showEditModal: false,
                showDetailModal: false,
                showPrintKIRModal: false,
                showEditKIRForm: false,
                selectedUnit: null,

                // Data Dokumen Cetak KIR
                kirDoc: {
                    nomor_surat: '028/KIR-RSUD/2026',
                    tanggal_pengesahan: '10 Januari 2026',
                    pj_nama: '',
                    pj_nip: '19880512 201201 2 004',
                    pj_jabatan: 'Kepala / Penanggung Jawab Ruangan',
                    pengurus_nama: 'BAMBANG HERMANTO, S.Sos',
                    pengurus_nip: '19790315 200801 1 012',
                    pengurus_jabatan: 'Pengurus Barang Pengelola Aset',
                    direktur_nama: 'dr. YUS PRIYATNA ADRYANTO,Sp.P,FISR.',
                    direktur_nip: '19771002 200604 1 006'
                },

                // Filter di dalam Modal Detail Aset Ruangan
                detailSearchQuery: '',
                detailKondisiFilter: 'all',
                detailCategoryFilter: 'all',

                // Data Unit & Paviliun RSUD diambil dinamis langsung dari Database Tabel Users (Role Sub Admin)
                units: {{ Js::from($units ?? []) }},

                get filteredUnits() {
                    const query = (this.searchQuery || '').toLowerCase();
                    return this.units.filter(item => {
                        return (item.nama || '').toLowerCase().includes(query) || 
                               (item.kode || '').toLowerCase().includes(query) ||
                               (item.kepala || '').toLowerCase().includes(query) ||
                               (item.tipe || '').toLowerCase().includes(query);
                    });
                },

                // Filter aset di dalam modal detail ruangan
                get filteredDetailAssets() {
                    if (!this.selectedUnit || !this.selectedUnit.assets) return [];
                    const query = (this.detailSearchQuery || '').toLowerCase();
                    return this.selectedUnit.assets.filter(ast => {
                        const matchSearch = (ast.nama || '').toLowerCase().includes(query) ||
                                            (ast.kode || '').toLowerCase().includes(query) ||
                                            (ast.merk || '').toLowerCase().includes(query) ||
                                            (ast.no_seri || '').toLowerCase().includes(query);
                        const matchKondisi = this.detailKondisiFilter === 'all' || ast.kondisi === this.detailKondisiFilter;
                        const matchCategory = this.detailCategoryFilter === 'all' || ast.category === this.detailCategoryFilter;
                        return matchSearch && matchKondisi && matchCategory;
                    });
                },

                formatRupiah(number) {
                    if (number === null || number === undefined) return '0';
                    const num = Number(number);
                    if (isNaN(num)) return '0';
                    return new Intl.NumberFormat('id-ID').format(num);
                },

                resetFilters() {
                    this.searchQuery = '';
                },

                openDetail(item) {
                    this.selectedUnit = item;
                    this.detailSearchQuery = '';
                    this.detailKondisiFilter = 'all';
                    this.detailCategoryFilter = 'all';
                    this.showDetailModal = true;
                },

                openPrintKIR(item) {
                    this.selectedUnit = item ? { ...item } : (this.selectedUnit || this.units[0]);
                    this.kirDoc.pj_nama = this.selectedUnit.pj_aset || this.selectedUnit.kepala;
                    this.showPrintKIRModal = true;
                },

                printCurrentKIR() {
                    window.print();
                },

                openEdit(item) {
                    this.selectedUnit = { ...item };
                    this.showEditModal = true;
                },

                showConfirmModal: false,
                confirmData: {
                    title: 'Konfirmasi Tindakan',
                    message: 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                    itemName: '',
                    type: 'danger',
                    btnText: 'Ya, Lanjutkan',
                    onConfirm: null
                },

                toast: {
                    show: false,
                    message: '',
                    type: 'success'
                },

                askConfirmation({ title, message, itemName, type = 'danger', btnText, onConfirm }) {
                    this.confirmData = {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        type: type,
                        btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan')),
                        onConfirm: onConfirm
                    };
                    this.showConfirmModal = true;
                },

                executeConfirmedAction() {
                    if (typeof this.confirmData.onConfirm === 'function') {
                        this.confirmData.onConfirm();
                    }
                    this.showConfirmModal = false;
                },

                showSimatToast(message, type = 'success') {
                    this.toast = { show: true, message: message, type: type };
                    setTimeout(() => { this.toast.show = false; }, 4000);
                },

                deleteUnit(item) {
                    if (!item) return;
                    this.askConfirmation({
                        title: '⚠️ Konfirmasi Hapus Unit / Paviliun',
                        message: 'Apakah Anda yakin ingin menghapus data unit / ruangan ini dari master data RSUD? Keterkaitan lokasi aset pada ruangan ini akan dilepas.',
                        itemName: (item.nama || 'Unit') + ' (' + (item.kode || 'UNIT') + ')',
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Unit',
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                await fetch('/unit-paviliun/' + item.id, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                                });
                                window.location.reload();
                            } catch(err) {
                                window.location.reload();
                            }
                        }
                    });
                }
            };
        }
    </script>

    <div x-data="unitPaviliunCatalog()" x-cloak>

        <!-- Header Banner & Mini KPI Strip -->
        <div class="bg-gradient-to-r from-blue-600/15 via-slate-900 to-slate-900 border border-blue-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/30 text-blue-300 text-xs font-extrabold uppercase tracking-wider mb-3 shadow-sm backdrop-blur-md">
                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse shadow-sm shadow-blue-400"></span>
                        <span>HIERARKI UNIT KERJA, PAVILIUN & INSTALASI RSUD</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Katalog Unit Kerja</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        Memuat informasi lokasi penempatan aset tetap, penanggung jawab ruangan, kuantitas aset tercatat, serta akumulasi nilai aset di setiap unit kerja.
                    </p>
                </div>
                
                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                <a href="{{ route('unit.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-bold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-2 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Tambah Unit Baru</span>
                </a>
                @endif
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
                        <span class="text-sm sm:text-base font-extrabold text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(units.reduce((acc, u) => acc + (parseInt(String(u.total_nilai).replace(/[^0-9]/g, '')) || 0), 0))"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">📦</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Total Aset Aktif</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300" x-text="units.reduce((acc, u) => acc + (u.assets ? u.assets.length : (u.total_aset || 0)), 0) + ' Item'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">👨‍⚕️</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Penanggung Jawab</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300" x-text="units.filter(u => u.kepala && u.kepala !== '-').length + ' Kepala Unit'"></span>
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
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold bg-slate-800 text-cyan-300 border border-slate-700" x-text="item.total_aset + ' Aset'"></span>
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
                        <button type="button" @click="openPrintKIR(item)"
                            class="px-2.5 py-1.5 rounded-xl bg-blue-500/15 text-blue-300 hover:bg-blue-500/25 border border-blue-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>KIR</span>
                        </button>
                        <button type="button" @click="openDetail(item)"
                            class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <span>Detail</span>
                        </button>
                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                        <a :href="'/unit-paviliun/' + item.id + '/edit'"
                            class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            <span>Ubah</span>
                        </a>
                        <button type="button" @click="deleteUnit(item)"
                            class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm cursor-pointer active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            <span>Hapus</span>
                        </button>
                        @endif
                    </div>
                </div>
            </template>
        </div>

        <!-- VIEW MODE 2: TABLE VIEW -->
        <div x-show="viewMode === 'table'" class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 340px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                    <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 bg-slate-950">No</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950">Kode Unit</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950">Nama Unit / Paviliun</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950">Tipe Pelayanan</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950">Kepala Ruangan</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950">Total Aset</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950">Total Nilai</th>
                            <th class="px-4 py-3.5 text-center bg-slate-950">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredUnits" :key="item.id">
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                                <td class="px-4 py-4 text-center font-mono font-semibold text-blue-400 whitespace-nowrap" x-text="item.kode"></td>
                                <td class="px-4 py-4 font-bold text-white whitespace-nowrap" x-text="item.nama"></td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="item.tipe"></span>
                                </td>
                                <td class="px-4 py-4 text-center whitespace-nowrap text-slate-300" x-text="item.kepala || '-'"></td>
                                <td class="px-4 py-4 text-center font-bold text-cyan-400 font-mono whitespace-nowrap" x-text="item.total_aset + ' Item'"></td>
                                <td class="px-4 py-4 text-center font-bold text-emerald-400 font-mono whitespace-nowrap" x-text="item.total_nilai"></td>
                                <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                    <button type="button" @click="openPrintKIR(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-blue-500/15 text-blue-300 hover:bg-blue-500/25 border border-blue-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                        <span>KIR</span>
                                    </button>
                                    <button type="button" @click="openDetail(item)"
                                        class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/20 border border-emerald-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        <span>Detail</span>
                                    </button>
                                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                    <a :href="'/unit-paviliun/' + item.id + '/edit'"
                                        class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span>Ubah</span>
                                    </a>
                                    <button type="button" @click="deleteUnit(item)"
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
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL DETAIL UNIT & INVENTARIS ASET RUANGAN (LENGKAP DENGAN DAFTAR ASET)   -->
        <!-- ========================================================================= -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-3 sm:p-5 overflow-y-auto" x-cloak>
            <div @click.away="if (!showPrintKIRModal && !showEditModal) showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-5xl w-full p-6 sm:p-7 shadow-2xl space-y-5 my-auto max-h-[90vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 shrink-0">
                    <div class="flex items-center space-x-3">
                        <div class="p-3 rounded-2xl bg-blue-500/20 text-blue-300 text-xl">🏥</div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30" x-text="selectedUnit ? selectedUnit.kode : ''"></span>
                                <h3 class="text-lg sm:text-xl font-extrabold text-white" x-text="selectedUnit ? selectedUnit.nama : ''"></h3>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="selectedUnit ? (selectedUnit.tipe + ' • Kepala: ' + selectedUnit.kepala) : ''"></p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <button type="button" @click="printKIR()"
                            class="hidden sm:inline-flex items-center space-x-1.5 px-3.5 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/40 text-xs font-bold transition-all active:scale-95">
                            <svg class="w-3.5 h-3.5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak KIR</span>
                        </button>
                        <button type="button" @click="showDetailModal = false" class="p-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white text-base font-bold transition-all">
                            &times;
                        </button>
                    </div>
                </div>

                <!-- Informasi Ringkas Profil Ruangan -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 shrink-0" x-if="selectedUnit">
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block tracking-wider">Kepala Ruangan / PJ</span>
                        <p class="font-bold text-white text-xs mt-0.5 truncate" x-text="selectedUnit.kepala"></p>
                        <p class="text-[10px] text-slate-400 font-mono truncate mt-0.5" x-text="'NIP: ' + (selectedUnit.nip || '-')"></p>
                    </div>
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-blue-400 block tracking-wider">Email Login Sub Admin</span>
                        <p class="font-bold text-blue-300 text-xs mt-0.5 truncate" x-text="selectedUnit.email"></p>
                        <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="'Ruangan ' + selectedUnit.nama"></p>
                    </div>
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-cyan-400 block tracking-wider">Total Aset Terpasang</span>
                        <p class="font-black text-cyan-300 text-xs mt-0.5" x-text="(selectedUnit.assets ? selectedUnit.assets.length : selectedUnit.total_aset) + ' Item Inventaris'"></p>
                        <span class="text-[10px] text-slate-500 block">KIR Ruangan</span>
                    </div>
                    <div class="p-3 bg-slate-950/70 rounded-2xl border border-slate-800/80">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 block tracking-wider">Total Nilai Realisasi</span>
                        <p class="font-black text-emerald-400 font-mono text-xs mt-0.5" x-text="selectedUnit.total_nilai"></p>
                        <span class="text-[10px] text-slate-500 block">Akumulasi Aset</span>
                    </div>
                </div>

                <!-- Toolbar Pencarian & Filter Aset Ruangan -->
                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto flex-1">
                        <div class="relative flex-1 sm:w-64">
                            <input type="text" x-model="detailSearchQuery" placeholder="Cari nama aset, merk, kode 108, nomor seri..."
                                class="w-full bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2 pl-9 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500">
                            <svg class="w-3.5 h-3.5 text-blue-400 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            <button type="button" x-show="detailSearchQuery" @click="detailSearchQuery = ''" class="absolute right-2.5 top-2 text-slate-500 hover:text-white text-xs">&times;</button>
                        </div>

                        <select x-model="detailKondisiFilter" class="bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-2 text-xs text-slate-300 focus:outline-none focus:border-blue-500">
                            <option value="all">Semua Kondisi</option>
                            <option value="Baik">Kondisi Baik (B)</option>
                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                        </select>
                    </div>

                    <div class="text-[11px] text-slate-400 shrink-0 font-medium">
                        Ditemukan <span class="text-blue-400 font-bold" x-text="filteredDetailAssets.length"></span> dari <span class="text-white font-bold" x-text="(selectedUnit && selectedUnit.assets) ? selectedUnit.assets.length : 0"></span> Aset Terdata
                    </div>
                </div>

                <!-- Tabel Daftar Rincian Aset Terpasang di Ruangan -->
                <div class="flex-1 overflow-y-auto bg-slate-950/80 border border-slate-800 rounded-2xl overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-300 min-w-[750px]">
                        <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider text-[10px] sticky top-0 z-10 border-b border-slate-800">
                            <tr>
                                <th class="px-3 py-3 text-center w-10">No</th>
                                <th class="px-3 py-3 text-center">Kode 108 / Register</th>
                                <th class="px-4 py-3">Nama Barang & Spesifikasi</th>
                                <th class="px-3 py-3 text-center">Kategori</th>
                                <th class="px-3 py-3 text-center">Tahun</th>
                                <th class="px-3 py-3 text-center">Kondisi</th>
                                <th class="px-3 py-3 text-right">Nilai Aset</th>
                                <th class="px-3 py-3 text-center">Status Operasional</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80">
                            <template x-for="(ast, idx) in filteredDetailAssets" :key="idx">
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <td class="px-3 py-3 text-center text-slate-400 font-mono font-bold" x-text="idx + 1"></td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="font-mono text-cyan-400 font-bold text-[11px] block" x-text="ast.kode"></span>
                                        <span class="font-mono text-[9px] text-slate-400" x-text="ast.no_seri"></span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-white text-xs" x-text="ast.nama"></div>
                                        <div class="text-[10px] text-blue-300 font-medium" x-text="ast.merk"></div>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="ast.category"></span>
                                    </td>
                                    <td class="px-3 py-3 text-center font-mono font-semibold text-slate-300" x-text="ast.tahun"></td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold"
                                            :class="{
                                                'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': ast.kondisi === 'Baik',
                                                'bg-amber-500/20 text-amber-300 border border-amber-500/30': ast.kondisi === 'Kurang Baik' || ast.kondisi === 'Rusak Ringan',
                                                'bg-rose-500/20 text-rose-300 border border-rose-500/30': ast.kondisi === 'Rusak Berat'
                                            }"
                                            x-text="ast.kondisi"></span>
                                    </td>
                                    <td class="px-3 py-3 text-right font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(ast.nilai ?? ast.harga ?? 0)"></td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-md text-[10px] font-semibold"
                                            :class="{
                                                'bg-cyan-500/15 text-cyan-300 border border-cyan-500/30': ast.status === 'Aktif Digunakan',
                                                'bg-purple-500/15 text-purple-300 border border-purple-500/30': ast.status === 'Standby Cadangan',
                                                'bg-amber-500/15 text-amber-300 border border-amber-500/30': ast.status.includes('Servis') || ast.status.includes('Kalibrasi')
                                            }"
                                            x-text="ast.status"></span>
                                    </td>
                                </tr>
                            </template>
                            <template x-if="filteredDetailAssets.length === 0">
                                <tr>
                                    <td colspan="8" class="px-4 py-8 text-center text-slate-400">
                                        <div class="text-2xl mb-1">🔍</div>
                                        <p class="font-semibold text-white">Tidak ada aset yang cocok dengan filter</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Coba ubah kata kunci pencarian atau reset filter kondisi.</p>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                <!-- Modal Action Footer -->
                <div class="pt-3 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3 shrink-0">
                    <div class="flex items-center space-x-2 text-xs text-slate-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                        <span>Inventaris ruangan terintegrasi dengan Kartu Inventaris Ruangan (KIR) & BAST Distribusi.</span>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                        <a href="{{ route('bast.index') }}"
                            class="px-3.5 py-2 rounded-xl bg-purple-500/15 hover:bg-purple-500/25 text-purple-300 border border-purple-500/30 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            <span>📑 BAST Ruangan</span>
                        </a>
                        @endif

                        <button type="button" @click="openPrintKIR(selectedUnit)"
                            class="px-3.5 py-2 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-bold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>🖨️ Cetak KIR</span>
                        </button>

                        <button type="button" @click="showDetailModal = false" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- MODAL CETAK LEMBAR KARTU INVENTARIS RUANGAN (KIR) RESMI KEDINASAN BMD     -->
        <!-- ========================================================================= -->
        <div x-show="showPrintKIRModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/90 backdrop-blur-md p-2 sm:p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintKIRModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-5xl w-full p-4 sm:p-6 shadow-2xl space-y-4 my-auto relative">
                
                <!-- Action Bar Modal -->
                <div class="no-print flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-blue-500/20 text-blue-300 text-sm">🖨️</span>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Lembar Cetak Kartu Inventaris Ruangan (KIR)</h3>
                            <p class="text-[11px] text-slate-400" x-text="selectedUnit ? (selectedUnit.nama + ' • Kode: ' + selectedUnit.kode) : ''"></p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="showEditKIRForm = !showEditKIRForm"
                            class="px-3.5 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 text-blue-300 border border-blue-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95">
                            <span x-text="showEditKIRForm ? '✕ Tutup Form Edit' : '✏️ Edit Data Pejabat / Tanggal KIR'"></span>
                        </button>
                        <button type="button" @click="printCurrentKIR()"
                            class="px-4 py-2 rounded-xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>Cetak (Print / PDF)</span>
                        </button>
                        <button type="button" @click="showPrintKIRModal = false" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>

                <!-- Formulir Cepat Edit Pejabat KIR (Hidden when Printed) -->
                <div x-show="showEditKIRForm" class="no-print bg-slate-950 p-4 rounded-2xl border border-blue-500/40 text-xs space-y-3 shadow-inner">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="font-bold text-blue-300 text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                            <span>✏️ Sesuaikan Data Nomor KIR, Tanggal & Identitas Pejabat Pengesah:</span>
                        </span>
                        <span class="text-[10px] text-emerald-400 font-mono">Teks di lembar KIR otomatis berganti live</span>
                    </div>

                    <!-- Nomor & Tanggal -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Nomor Registrasi KIR</label>
                            <input type="text" x-model="kirDoc.nomor_surat" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-blue-300 font-mono font-bold text-xs">
                        </div>
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1">Tanggal Pengesahan (Kota, Tanggal Bulan Tahun)</label>
                            <input type="text" x-model="kirDoc.tanggal_pengesahan" class="w-full bg-slate-900 border border-slate-700 rounded-lg px-2.5 py-1.5 text-white text-xs">
                        </div>
                    </div>

                    <!-- 3 Pejabat: Penanggung Jawab Ruangan, Pengurus Barang, Direktur -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2 border-t border-slate-800/80">
                        <!-- PJ Ruangan -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-blue-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-blue-400 block uppercase">1. Penanggung Jawab Ruangan:</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama PJ Ruangan / Gelar</label>
                                <input type="text" x-model="kirDoc.pj_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-blue-300 font-semibold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP PJ Ruangan</label>
                                <input type="text" x-model="kirDoc.pj_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>

                        <!-- Pengurus Barang -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-emerald-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-emerald-400 block uppercase">2. Pengurus Barang RSUD:</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Pengurus Barang</label>
                                <input type="text" x-model="kirDoc.pengurus_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-emerald-400 font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP Pengurus Barang</label>
                                <input type="text" x-model="kirDoc.pengurus_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>

                        <!-- Direktur RSUD -->
                        <div class="p-3 rounded-xl bg-slate-900/80 border border-purple-500/30 space-y-2">
                            <span class="text-[10px] font-bold text-purple-400 block uppercase">3. Mengetahui (Direktur RSUD):</span>
                            <div>
                                <label class="block text-slate-400 text-[9px]">Nama Direktur & Gelar</label>
                                <input type="text" x-model="kirDoc.direktur_nama" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-bold text-xs">
                            </div>
                            <div>
                                <label class="block text-slate-400 text-[9px]">NIP Direktur</label>
                                <input type="text" x-model="kirDoc.direktur_nip" class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1 text-white font-mono text-xs">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- LEMBAR CETAK ASLI DOKUMEN KARTU INVENTARIS RUANGAN (KIR) KERTAS PUTIH -->
                <template x-if="selectedUnit">
                    <div id="print-area-kir" class="bg-white text-black p-6 sm:p-8 rounded-2xl shadow-xl max-h-[70vh] overflow-y-auto font-serif text-[11px] leading-relaxed select-text print:max-h-none print:overflow-visible print:p-0 print:m-0 print:shadow-none print:rounded-none">
                        
                        <!-- KOP SURAT RESMI -->
                        <div class="border-b-[3px] border-black pb-1 mb-0.5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/logo-bondowoso.png') }}" alt="Logo Dinas Bondowoso" class="h-16 w-16 object-contain">
                                </div>

                                <div class="flex-1 text-center font-sans text-black">
                                    <h4 class="font-bold text-xs sm:text-sm uppercase tracking-wide leading-tight">PEMERINTAH KABUPATEN BONDOWOSO</h4>
                                    <h3 class="font-black text-sm sm:text-base uppercase tracking-tight leading-tight">RUMAH SAKIT UMUM DAERAH dr.H.KOESNADI</h3>
                                    <p class="text-[10px] leading-tight mt-0.5">Jl. Kapten Piere Tendean No. 3 Telp. (0332) 421974 Fax.0332 422311</p>
                                    <p class="text-[10px] leading-tight">e-mail : rsu.koesnadi@gmail.com, Website : rsudrkoesnadi.go.id</p>
                                    <h4 class="font-bold text-xs tracking-[0.3em] uppercase mt-0.5">B O N D O W O S O</h4>
                                </div>

                                <div class="w-20 shrink-0 flex justify-center">
                                    <img src="{{ asset('img/Logo-rsud/logo-rsud.png') }}" alt="Logo RSUD" class="h-16 w-16 object-contain">
                                </div>
                            </div>
                        </div>
                        <div class="border-b border-black mb-4"></div>

                        <!-- JUDUL LEMBAR KIR -->
                        <div class="text-center font-sans mb-3">
                            <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">KARTU INVENTARIS RUANGAN (KIR)</h3>
                            <p class="text-[10px] font-semibold">Nomor Registrasi : <span x-text="kirDoc.nomor_surat"></span></p>
                        </div>

                        <!-- ATRIBUT DATA RUANGAN -->
                        <div class="grid grid-cols-2 gap-x-6 gap-y-1 font-sans text-[10px] mb-3 border p-2.5 bg-gray-50 border-gray-300 rounded">
                            <div class="flex">
                                <span class="w-32 font-bold">SKPD / Unit Kerja</span>
                                <span class="w-3">:</span>
                                <span class="font-semibold text-black">RSUD dr. H. KOESNANDI</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">KABUPATEN / PROV</span>
                                <span class="w-3">:</span>
                                <span class="font-semibold text-black">BONDOWOSO / JAWA TIMUR</span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">NAMA RUANGAN / UNIT</span>
                                <span class="w-3">:</span>
                                <span class="font-bold text-black uppercase" x-text="selectedUnit.nama"></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">KODE RUANGAN / LOKASI</span>
                                <span class="w-3">:</span>
                                <span class="font-mono font-bold text-black" x-text="selectedUnit.kode"></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">PENANGGUNG JAWAB</span>
                                <span class="w-3">:</span>
                                <span class="font-semibold text-black" x-text="kirDoc.pj_nama"></span>
                            </div>
                            <div class="flex">
                                <span class="w-32 font-bold">TAHUN ANGGARAN</span>
                                <span class="w-3">:</span>
                                <span class="font-bold text-black">2026</span>
                            </div>
                        </div>

                        <!-- TABEL STANDAR PERMENDAGRI BMD UNTUK KARTU INVENTARIS RUANGAN -->
                        <div class="mb-4">
                            <table class="w-full border-collapse border border-black text-[9.5px]">
                                <thead class="bg-gray-100 font-sans text-center font-bold">
                                    <tr>
                                        <th rowspan="2" class="border border-black px-1.5 py-2 w-7">No</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Jenis Barang / Nama Barang</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Merk / Type / Spesifikasi</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">No. Pabrik / No. Seri</th>
                                        <th rowspan="2" class="border border-black px-2 py-2 w-12">Tahun</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Kode 108 / Register</th>
                                        <th rowspan="2" class="border border-black px-1.5 py-2 w-10">Jml</th>
                                        <th rowspan="2" class="border border-black px-2 py-2 text-right">Harga Beli / Nilai (Rp)</th>
                                        <th colspan="3" class="border border-black px-1 py-1">Keadaan Barang</th>
                                        <th rowspan="2" class="border border-black px-2 py-2">Keterangan</th>
                                    </tr>
                                    <tr>
                                        <th class="border border-black px-1 py-1 w-8">B</th>
                                        <th class="border border-black px-1 py-1 w-8">RR</th>
                                        <th class="border border-black px-1 py-1 w-8">RB</th>
                                    </tr>
                                    <tr class="bg-gray-50 text-[8px] font-mono">
                                        <th class="border border-black">1</th>
                                        <th class="border border-black">2</th>
                                        <th class="border border-black">3</th>
                                        <th class="border border-black">4</th>
                                        <th class="border border-black">5</th>
                                        <th class="border border-black">6</th>
                                        <th class="border border-black">7</th>
                                        <th class="border border-black">8</th>
                                        <th class="border border-black">9</th>
                                        <th class="border border-black">10</th>
                                        <th class="border border-black">11</th>
                                        <th class="border border-black">12</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(ast, idx) in (selectedUnit.assets || [])" :key="idx">
                                        <tr>
                                            <td class="border border-black px-1 py-1 text-center font-mono" x-text="idx + 1"></td>
                                            <td class="border border-black px-2 py-1 font-sans font-bold" x-text="ast.nama"></td>
                                            <td class="border border-black px-2 py-1 font-sans" x-text="ast.merk"></td>
                                            <td class="border border-black px-2 py-1 font-mono text-[9px]" x-text="ast.no_seri"></td>
                                            <td class="border border-black px-1 py-1 text-center font-mono" x-text="ast.tahun"></td>
                                            <td class="border border-black px-2 py-1 font-mono text-center text-[9px]" x-text="ast.kode"></td>
                                            <td class="border border-black px-1 py-1 text-center font-mono font-bold">1</td>
                                            <td class="border border-black px-2 py-1 text-right font-mono font-semibold" x-text="formatRupiah(ast.nilai ?? ast.harga ?? 0)"></td>
                                            
                                            <!-- Keadaan Barang: Baik (B), Rusak Ringan (RR), Rusak Berat (RB) -->
                                            <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                                <span x-text="ast.kondisi === 'Baik' ? '✓' : ''"></span>
                                            </td>
                                            <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                                <span x-text="ast.kondisi === 'Rusak Ringan' ? '✓' : ''"></span>
                                            </td>
                                            <td class="border border-black px-1 py-1 text-center font-bold font-sans">
                                                <span x-text="ast.kondisi === 'Rusak Berat' ? '✓' : ''"></span>
                                            </td>

                                            <td class="border border-black px-2 py-1 font-sans text-[8.5px]" x-text="ast.status"></td>
                                        </tr>
                                    </template>
                                    
                                    <template x-if="!selectedUnit.assets || selectedUnit.assets.length === 0">
                                        <tr>
                                            <td colspan="12" class="border border-black px-2 py-3 text-center italic text-gray-500">
                                                (Belum ada data barang/aset yang terdata di ruangan ini)
                                            </td>
                                        </tr>
                                    </template>

                                    <!-- Baris Total -->
                                    <tr class="bg-gray-100 font-bold font-sans">
                                        <td colspan="6" class="border border-black px-2 py-1.5 text-right uppercase">JUMLAH TOTAL DI RUANGAN:</td>
                                        <td class="border border-black px-1 py-1.5 text-center font-mono" x-text="(selectedUnit.assets ? selectedUnit.assets.length : 0) + ' Unit'"></td>
                                        <td class="border border-black px-2 py-1.5 text-right font-mono font-bold" x-text="selectedUnit.total_nilai"></td>
                                        <td colspan="4" class="border border-black px-2 py-1.5 text-center text-[8.5px] italic text-gray-700">Terinventarisasi Lengkap</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- CATATAN KETENTUAN KIR -->
                        <div class="text-[8.5px] text-gray-700 mb-4 leading-tight font-sans">
                            <p class="font-bold">Keterangan & Ketentuan Pengelolaan Ruangan:</p>
                            <p>1. Barang inventaris yang tercatat dalam daftar ini berada di bawah pengawasan & tanggung jawab Kepala Ruangan.</p>
                            <p>2. Dilarang memindahkan barang inventaris dari ruangan ini ke ruangan lain tanpa izin resmi & Berita Acara Mutasi.</p>
                            <p>3. Apabila terjadi kerusakan/kehilangan segera melapor kepada Pengurus Barang / IPSRS RSUD dr. H. Koesnandi Bondowoso.</p>
                        </div>

                        <!-- 3 KOLOM TANDA TANGAN PENGESAHAN -->
                        <div class="grid grid-cols-3 gap-3 text-center font-sans text-[10px] pt-1">
                            
                            <!-- Kolom 1: Direktur RSUD -->
                            <div>
                                <p class="font-bold">Mengetahui / Menyetujui,</p>
                                <p class="font-black uppercase text-[9.5px]">DIREKTUR RSUD dr.H.KOESNANDI</p>
                                
                                <div class="h-14 flex items-center justify-center my-1">
                                    <div class="flex items-center space-x-1.5 p-1 border border-black bg-gray-50 rounded">
                                        <div class="w-8 h-8 bg-white border border-black p-0.5">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-KIR-DIREKTUR-KOESNANDI" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[6.5px] leading-tight text-black">
                                            <div class="font-bold">PENGGUNA BARANG</div>
                                            <div>Tervalidasi BSrE</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="font-bold underline text-[10.5px]" x-text="kirDoc.direktur_nama"></p>
                                <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.direktur_nip"></p>
                            </div>

                            <!-- Kolom 2: Pengurus Barang Aset -->
                            <div>
                                <p class="font-bold">Pengurus Barang Pengelola Aset,</p>
                                <p class="font-black uppercase text-[9.5px]">RSUD dr.H.KOESNANDI</p>
                                
                                <div class="h-14 flex items-center justify-center my-1">
                                    <div class="flex items-center space-x-1.5 p-1 border border-black bg-gray-50 rounded">
                                        <div class="w-8 h-8 bg-white border border-black p-0.5">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-KIR-PENGURUS-BARANG" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[6.5px] leading-tight text-black">
                                            <div class="font-bold">PENGURUS BARANG</div>
                                            <div>Tervalidasi BSrE</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="font-bold underline text-[10.5px]" x-text="kirDoc.pengurus_nama"></p>
                                <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.pengurus_nip"></p>
                            </div>

                            <!-- Kolom 3: Penanggung Jawab Ruangan -->
                            <div>
                                <p class="font-semibold text-[9.5px]" x-text="kirDoc.tanggal_pengesahan"></p>
                                <p class="font-bold uppercase text-[9.5px]">PENANGGUNG JAWAB RUANGAN,</p>
                                
                                <div class="h-14 flex items-center justify-center my-1">
                                    <div class="flex items-center space-x-1.5 p-1 border border-black bg-gray-50 rounded">
                                        <div class="w-8 h-8 bg-white border border-black p-0.5">
                                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=100x100&data=BSRE-KIR-PJ-RUANGAN" class="w-full h-full object-contain">
                                        </div>
                                        <div class="text-left text-[6.5px] leading-tight text-black">
                                            <div class="font-bold">PJ RUANGAN</div>
                                            <div>Tervalidasi BSrE</div>
                                        </div>
                                    </div>
                                </div>

                                <p class="font-bold underline text-[10.5px]" x-text="kirDoc.pj_nama"></p>
                                <p class="font-mono text-[9px]" x-text="'NIP. ' + kirDoc.pj_nip"></p>
                            </div>

                        </div>

                    </div>
                </template>

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

        <!-- GLOBAL CUSTOM CONFIRMATION DIALOG MODAL (Sleek Dark Theme) -->
        <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
            <div @click.away="showConfirmModal = false"
                 x-show="showConfirmModal"
                 x-transition:enter="transition ease-out duration-200 transform opacity-0 scale-95"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-150 transform opacity-100 scale-100"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="bg-slate-900 border rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4 relative"
                 :class="{
                     'border-rose-500/40': confirmData.type === 'danger',
                     'border-amber-500/40': confirmData.type === 'warning',
                     'border-emerald-500/40': confirmData.type === 'success',
                     'border-cyan-500/40': confirmData.type === 'info'
                 }">
                
                <!-- Header Icon & Title -->
                <div class="flex items-start space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-xl shrink-0 font-bold border"
                         :class="{
                             'bg-rose-500/20 text-rose-400 border-rose-500/30': confirmData.type === 'danger',
                             'bg-amber-500/20 text-amber-300 border-amber-500/30': confirmData.type === 'warning',
                             'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': confirmData.type === 'success',
                             'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': confirmData.type === 'info'
                         }">
                        <span x-text="confirmData.type === 'danger' ? '🗑️' : (confirmData.type === 'warning' ? '✏️' : '➕')"></span>
                    </div>
                    <div class="space-y-1 min-w-0 flex-1">
                        <h3 class="text-base font-extrabold text-white leading-snug" x-text="confirmData.title"></h3>
                        <p class="text-slate-300 text-xs leading-relaxed" x-text="confirmData.message"></p>
                    </div>
                </div>

                <!-- Item Target Preview Card -->
                <template x-if="confirmData.itemName">
                    <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-0.5">Item Target:</span>
                        <p class="text-xs font-bold text-cyan-300 truncate font-mono" x-text="confirmData.itemName"></p>
                    </div>
                </template>

                <!-- Footer Action Buttons -->
                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showConfirmModal = false"
                        class="px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="executeConfirmedAction()"
                        class="px-5 py-2.5 rounded-xl font-extrabold text-xs shadow-lg transition-all active:scale-95 cursor-pointer flex items-center space-x-1.5"
                        :class="{
                            'bg-rose-500 hover:bg-rose-400 text-white shadow-rose-500/20': confirmData.type === 'danger',
                            'bg-amber-500 hover:bg-amber-400 text-slate-950 shadow-amber-500/20': confirmData.type === 'warning',
                            'bg-emerald-500 hover:bg-emerald-400 text-slate-950 shadow-emerald-500/20': confirmData.type === 'success',
                            'bg-cyan-500 hover:bg-cyan-400 text-slate-950 shadow-cyan-500/20': confirmData.type === 'info'
                        }">
                        <span x-text="confirmData.btnText"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- GLOBAL FLOATING TOAST NOTIFICATION POPUP -->
        <div x-show="toast.show" x-cloak
             x-transition:enter="transition ease-out duration-300 transform opacity-0 translate-y-4 scale-95"
             x-transition:enter-start="opacity-0 translate-y-4 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform opacity-100 translate-y-0 scale-100"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 scale-95"
             class="fixed bottom-6 right-6 z-50 max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3"
             :class="{
                 'border-emerald-500/40 text-emerald-300': toast.type === 'success',
                 'border-rose-500/40 text-rose-300': toast.type === 'error',
                 'border-amber-500/40 text-amber-300': toast.type === 'warning',
                 'border-cyan-500/40 text-cyan-300': toast.type === 'info'
             }">
            <div class="flex items-center space-x-2.5 min-w-0">
                <span class="text-base shrink-0" x-text="toast.type === 'success' ? '✅' : (toast.type === 'error' ? '⚠️' : 'ℹ️')"></span>
                <p class="text-xs font-bold leading-snug truncate" x-text="toast.message"></p>
            </div>
            <button type="button" @click="toast.show = false" class="text-slate-400 hover:text-white text-base font-bold shrink-0">&times;</button>
        </div>

    </div>
</x-layout>
