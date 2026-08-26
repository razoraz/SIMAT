<x-layout title="Distribusi ASTAP - SIMAT-RK">
    @section('page-title', 'Distribusi ASTAP')
    @section('breadcrumb', 'Master Utama / Distribusi ASTAP')

    <script>
        function distribusiCatalog() {
            return {
                userRole: {{ Js::from(Auth::user()->role) }},
                userUnit: {{ Js::from(Auth::user()->unitModel?->nama ?? Auth::user()->unit ?? '') }},
                userName: {{ Js::from(Auth::user()->name) }},

                searchQuery: '',
                unitFilter: 'all',
                statusFilter: 'all',
                showDetailModal: false,
                showEditModal: false,
                showPrintBastModal: false,
                showEditBastForm: false,
                selectedDistribusi: null,

                unitList: {{ Js::from($units ?? []) }},

                distribusis: {{ Js::from($distribusis ?? []) }},

                init() {
                    // Prioritaskan data yang dikirim dari database backend
                    if (this.distribusis && this.distribusis.length > 0) {
                        localStorage.setItem('simat_distribusis', JSON.stringify(this.distribusis));
                        return;
                    }
                    const stored = localStorage.getItem('simat_distribusis');
                    if (stored) {
                        try {
                            const parsed = JSON.parse(stored);
                            if (Array.isArray(parsed) && parsed.length > 0) {
                                this.distribusis = parsed;
                            }
                        } catch (e) {
                            this.distribusis = [];
                        }
                    }
                },

                saveToStorage() {
                    localStorage.setItem('simat_distribusis', JSON.stringify(this.distribusis));
                },

                deleteDistribusi(id) {
                    if (confirm('Apakah Anda yakin ingin menghapus data transaksi distribusi ini?')) {
                        this.distribusis = this.distribusis.filter(d => d.id !== id);
                        this.saveToStorage();
                    }
                },

                unitSearchQuery: '',
                isUnitDropdownOpen: false,

                get filteredUnitDropdownList() {
                    if (!this.unitSearchQuery || this.unitSearchQuery.trim().length === 0) {
                        return this.unitList;
                    }
                    const q = this.unitSearchQuery.toLowerCase().trim();
                    return this.unitList.filter(u => 
                        (u.nama || '').toLowerCase().includes(q) ||
                        (u.kode || '').toLowerCase().includes(q) ||
                        (u.tipe || '').toLowerCase().includes(q) ||
                        (u.kepala || '').toLowerCase().includes(q)
                    );
                },

                selectUnitFilter(unit) {
                    if (!unit) {
                        this.unitFilter = 'all';
                        this.unitSearchQuery = '';
                    } else {
                        this.unitFilter = unit.nama;
                        this.unitSearchQuery = unit.nama;
                    }
                    this.isUnitDropdownOpen = false;
                },

                clearUnitFilter() {
                    this.unitFilter = 'all';
                    this.unitSearchQuery = '';
                    this.isUnitDropdownOpen = false;
                },

                get filteredDistribusis() {
                    const query = (this.searchQuery || '').toLowerCase().trim();
                    const role = this.userRole;
                    const myUnit = (this.userUnit || '').toLowerCase().trim();
                    const myName = (this.userName || '').toLowerCase().trim();

                    return this.distribusis.filter(item => {
                        // Jika Sub Admin: hanya tampilkan distribusi yang melibatkan unit atau penerima dirinya
                        if (role === 'sub_admin' && myUnit) {
                            const isUnitMatch = (item.tujuan || '').toLowerCase().includes(myUnit) ||
                                                (item.pj_ruangan || '').toLowerCase().includes(myUnit) ||
                                                myUnit.includes((item.tujuan || '').toLowerCase());
                            const isPenerimaMatch = (item.penerima || '').toLowerCase().includes(myName) ||
                                                    (item.pj_nama || '').toLowerCase().includes(myName);
                            if (!isUnitMatch && !isPenerimaMatch) return false;
                        }

                        const matchSearch = !query ||
                                            (item.nama || '').toLowerCase().includes(query) || 
                                            (item.kode || '').toLowerCase().includes(query) || 
                                            (item.penerima || '').toLowerCase().includes(query) ||
                                            (item.items || []).some(it => (it.nama_barang || '').toLowerCase().includes(query));

                        const matchUnit = this.unitFilter === 'all' || 
                                          item.tujuan.toLowerCase().includes(this.unitFilter.toLowerCase()) ||
                                          this.unitFilter.toLowerCase().includes(item.tujuan.toLowerCase());

                        const matchStatus = this.statusFilter === 'all' || item.status === this.statusFilter;
                        return matchSearch && matchUnit && matchStatus;
                    });
                },

                getTotalQty(items) {
                    if (!items || !items.length) return 0;
                    return items.reduce((acc, curr) => acc + (parseInt(curr.qty) || 0), 0);
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.unitFilter = 'all';
                    this.unitSearchQuery = '';
                    this.isUnitDropdownOpen = false;
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

                terimaDistribusi(item) {
                    this.toggleSignDistribusi(item);
                },

                toggleSignDistribusi(item) {
                    if (!item) return;
                    const target = this.distribusis.find(d => d.id === item.id) || item;
                    if (target.signed) {
                        target.signed = false;
                        target.tgl_signed = '-';
                        target.qr_hash = '';
                        target.status = 'Menunggu Konfirmasi';
                        item.signed = false;
                        item.tgl_signed = '-';
                        item.qr_hash = '';
                        item.status = 'Menunggu Konfirmasi';
                        alert('↩️ Tanda tangan digital BSrE BAST Distribusi (' + (target.nomor_bast || target.kode) + ') berhasil dibatalkan.');
                    } else {
                        target.signed = true;
                        const now = new Date();
                        target.tgl_signed = now.toLocaleDateString('id-ID') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0') + ' WIB';
                        target.qr_hash = 'BSRE-KOESNANDI-DST-' + Date.now();
                        target.status = 'Telah Diterima';
                        item.signed = true;
                        item.tgl_signed = target.tgl_signed;
                        item.qr_hash = target.qr_hash;
                        item.status = 'Telah Diterima';
                        alert('✍️ BAST Distribusi (' + (target.nomor_bast || target.kode) + ') berhasil ditandatangani secara digital (QR Code BSrE Aktif)!');
                    }
                    this.saveToStorage();
                },

                tolakDistribusi(item) {
                    if (!item) return;
                    item.signed = false;
                    item.tgl_signed = '-';
                    item.qr_hash = '';
                    item.status = 'Ditolak';

                    // Sync with main array item
                    const found = this.distribusis.find(d => d.id === item.id);
                    if (found) {
                        found.signed = false;
                        found.tgl_signed = '-';
                        found.qr_hash = '';
                        found.status = 'Ditolak';
                    }
                    this.saveToStorage();
                    alert('❌ BAST Distribusi (' + (item.kode || item.bast_nomor) + ') ditolak! Tanda tangan digital telah dihapus.');
                },

                copiedNibar: null,
                copyNibar(nibar) {
                    navigator.clipboard.writeText(nibar).then(() => {
                        this.copiedNibar = nibar;
                        setTimeout(() => {
                            if (this.copiedNibar === nibar) {
                                this.copiedNibar = null;
                            }
                        }, 2000);
                    });
                },

                kondisiSaving: {},   // { reg_id: true/false }
                kondisiSaved: {},    // { reg_id: true } — tampil centang sesaat

                updateKondisiRegister(regId, newKondisi, item) {
                    if (!regId) {
                        alert('⚠️ Register ID tidak ditemukan untuk NIBAR ini. Kondisi tidak dapat disimpan ke database.');
                        return;
                    }
                    this.kondisiSaving[regId] = true;

                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/distribusi/register-kondisi/' + regId, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ kondisi: newKondisi })
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.kondisiSaving[regId] = false;
                        if (data.success) {
                            this.kondisiSaved[regId] = true;
                            // Update kondisi di nibar_registers item yang bersangkutan
                            if (item && item.nibar_registers) {
                                const reg = item.nibar_registers.find(r => r.reg_id == regId);
                                if (reg) reg.kondisi = newKondisi;
                            }
                            // Update kondisi ringkasan item (ambil dari register pertama)
                            if (item && item.nibar_registers && item.nibar_registers.length > 0) {
                                item.kondisi = item.nibar_registers[0].kondisi;
                            }
                            setTimeout(() => { delete this.kondisiSaved[regId]; }, 2000);
                        } else {
                            alert('⚠️ Gagal menyimpan kondisi: ' + (data.message || 'Terjadi kesalahan.'));
                        }
                    })
                    .catch(() => {
                        this.kondisiSaving[regId] = false;
                        alert('⚠️ Koneksi gagal. Pastikan server berjalan dan coba kembali.');
                    });
                },

                printCurrentBast() {
                    window.print();
                }
            }
        }
    </script>

    <div x-data="distribusiCatalog()" x-cloak>

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
                        Pengelolaan alokasi penyerahan barang aset dari inventaris ke paviliun rawat inap, unit RSUD, dan instalasi RSUD. Mendukung distribusi beberapa barang sekaligus dalam satu transaksi.
                    </p>
                </div>
                
                <!-- Action Button Input Baru -->
                <div class="flex items-center gap-2.5 shrink-0">
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
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300" x-text="distribusis.filter(d => d.status === 'Telah Diterima' || d.status === 'Diterima').length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 text-lg">🚛</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Dalam Kirim</span>
                        <span class="text-sm sm:text-base font-extrabold text-cyan-300" x-text="distribusis.filter(d => d.status === 'Dalam Pengiriman' || d.status === 'Dikirim').length + ' Transaksi'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">⏳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Menunggu Konfirmasi</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300" x-text="distribusis.filter(d => d.status === 'Menunggu Konfirmasi' || d.status === 'Pending').length + ' Transaksi'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search Section (Filter Unit Mengambil Dinamis dari Database Unit) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Baris Atas: Input Pencarian & Counter Data -->
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari no. distribusi / nama aset barang / pegawai penerima..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-teal-500 transition-all">
                        <svg class="w-4 h-4 text-teal-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-teal-400 font-bold" x-text="filteredDistribusis.length"></span> dari <span class="text-white font-bold" x-text="distribusis.length"></span> Data
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>

                <!-- Baris Bawah: Filter Dropdown Unit (Searchable Ketik Filter) & Filter Status -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-3 border-t border-slate-800/80">
                    
                    <!-- 1. FILTER UNIT / PAVILIUN (SEARCHABLE INPUT / KETIK FILTER DINAMIS DARI DATABASE) -->
                    <div class="relative" @click.away="isUnitDropdownOpen = false">
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>🏥 Filter Unit / Paviliun Penerima</span>
                                <span class="text-teal-400 font-mono text-[10px]" x-text="'(' + unitList.length + ' Unit RSUD)'"></span>
                            </label>
                            <template x-if="unitFilter !== 'all'">
                                <button type="button" @click="clearUnitFilter()" class="text-[10px] text-rose-400 hover:text-rose-300 font-bold flex items-center space-x-0.5">
                                    <span>✕ Reset Filter Unit</span>
                                </button>
                            </template>
                        </div>

                        <div class="relative">
                            <input type="text" 
                                   x-model="unitSearchQuery" 
                                   @focus="isUnitDropdownOpen = true"
                                   @input="isUnitDropdownOpen = true; if(unitSearchQuery.trim() === '') unitFilter = 'all'"
                                   placeholder="Ketik nama unit (misal: 'igd', 'melati', 'radiologi', 'graha')..." 
                                   class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 pl-9 pr-8 text-xs text-white font-semibold placeholder-slate-500 focus:outline-none focus:border-teal-500 focus:ring-1 focus:ring-teal-500 transition-all">
                            
                            <svg class="w-4 h-4 text-teal-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>

                            <template x-if="unitSearchQuery && unitSearchQuery.length > 0">
                                <button type="button" @click="clearUnitFilter()" class="absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-400 p-0.5 rounded">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                </button>
                            </template>
                        </div>

                        <!-- Dropdown Floating Hasil Pencarian Unit -->
                        <div x-show="isUnitDropdownOpen" 
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             class="absolute left-0 right-0 z-40 mt-1 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden max-h-64 overflow-y-auto divide-y divide-slate-800">
                            
                            <!-- Opsi Semua Unit -->
                            <div @click="selectUnitFilter(null)" 
                                 class="px-4 py-2.5 hover:bg-teal-500/15 cursor-pointer transition-colors flex items-center justify-between font-bold text-xs"
                                 :class="unitFilter === 'all' ? 'bg-teal-500/10 text-teal-300' : 'text-slate-300'">
                                <div class="flex items-center space-x-2">
                                    <span>🏢</span>
                                    <span>Semua Unit & Paviliun (Seluruh RSUD)</span>
                                </div>
                                <template x-if="unitFilter === 'all'">
                                    <span class="text-teal-400 text-xs">✓ Aktif</span>
                                </template>
                            </div>

                            <!-- List Unit Terfilter -->
                            <template x-for="u in filteredUnitDropdownList" :key="u.id">
                                <div @click="selectUnitFilter(u)" 
                                     class="px-4 py-2.5 hover:bg-teal-500/15 cursor-pointer transition-colors group flex items-center justify-between"
                                     :class="unitFilter === u.nama ? 'bg-teal-500/10 text-teal-300' : 'text-slate-300'">
                                    <div class="space-y-0.5">
                                        <div class="font-bold text-xs group-hover:text-teal-300 flex items-center space-x-1.5 text-white">
                                            <span>🏥</span>
                                            <span x-text="u.nama"></span>
                                        </div>
                                        <div class="text-[10px] text-slate-400" x-text="(u.kode ? u.kode + ' • ' : '') + u.tipe + ' • PJ: ' + u.kepala"></div>
                                    </div>
                                    <template x-if="unitFilter === u.nama">
                                        <span class="text-teal-400 font-bold text-xs">✓ Terpilih</span>
                                    </template>
                                </div>
                            </template>

                            <template x-if="filteredUnitDropdownList.length === 0">
                                <div class="px-4 py-4 text-center text-xs text-slate-400">
                                    <p class="font-semibold text-amber-400">Unit "<span x-text="unitSearchQuery"></span>" tidak ditemukan</p>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Coba ketik kata kunci nama ruangan lainnya</p>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- 2. FILTER STATUS PENYERAHAN -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                <span>Status Penyerahan Distribusi</span>
                            </label>
                            <template x-if="statusFilter !== 'all'">
                                <button type="button" @click="statusFilter = 'all'" class="text-[10px] text-rose-400 hover:text-rose-300 font-bold">
                                    <span>✕ Reset Status</span>
                                </button>
                            </template>
                        </div>
                        <div>
                            <select x-model="statusFilter" 
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-slate-200 font-semibold focus:outline-none focus:border-teal-500 transition-all cursor-pointer">
                                <option value="all">🔍 Semua Status Penyerahan BAST</option>
                                <option value="Telah Diterima">✅ Telah Diterima & Disahkan</option>
                                <option value="Ditolak">❌ Ditolak</option>
                                <option value="Dalam Pengiriman">🚚 Dalam Pengiriman (Siap Kirim)</option>
                                <option value="Menunggu Konfirmasi">⏳ Menunggu Konfirmasi (Verifikasi)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sub Admin Context Banner -->
        <template x-if="userRole === 'sub_admin'">
            <div class="mb-5 p-4 rounded-2xl bg-teal-500/10 border border-teal-500/30 text-teal-300 text-xs font-semibold flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-lg">
                <div class="flex items-center space-x-2.5">
                    <span class="text-lg">🚚</span>
                    <div>
                        <span class="font-extrabold text-white block text-sm">Mode Akses Distribusi Unit: <span x-text="userUnit || 'Sub Admin Ruangan'"></span></span>
                        <span class="text-slate-400 text-[11px]">Hanya menampilkan transaksi distribusi barang yang ditujukan ke unit / ruangan Anda.</span>
                    </div>
                </div>
                <span class="text-[10.5px] font-mono font-bold px-3 py-1 rounded-xl bg-teal-500/20 text-teal-200 border border-teal-500/40 shrink-0">Sub Admin Restricted</span>
            </div>
        </template>

        <!-- Tabel Distribusi ASTAP (Multi-Barang / Transaksi) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap">No</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">No. Distribusi</th>
                        <th class="px-4 py-3.5 text-left min-w-[260px]">Rincian Barang yang Didistribusikan</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Tujuan Unit / Ruangan</th>
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
                            
                            <!-- Kolom Barang: Ringkasan Rapi (Detail Lengkap Dapat Dilihat di Modal Detail / BAST) -->
                            <td class="px-4 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center space-x-2">
                                        <p class="font-extrabold text-white text-xs" x-text="item.nama"></p>
                                        <template x-if="item.items && item.items.length > 0">
                                            <span class="px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold shrink-0"
                                                  x-text="item.items.length + ' Jenis Barang'">
                                            </span>
                                        </template>
                                    </div>
                                    <template x-if="item.keterangan">
                                        <p class="text-[11px] text-slate-400 truncate max-w-xs sm:max-w-md" x-text="'Catatan: ' + item.keterangan"></p>
                                    </template>
                                </div>
                            </td>

                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="font-bold text-slate-200 bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800" x-text="item.tujuan"></span>
                            </td>
                            <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap" x-text="item.tgl"></td>
                            <td class="px-4 py-4 text-center font-semibold text-white whitespace-nowrap" x-text="item.penerima"></td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none border shadow-sm select-none"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': item.status === 'Telah Diterima' || item.status === 'Diterima',
                                        'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': item.status === 'Dalam Pengiriman' || item.status === 'Dikirim',
                                        'bg-amber-500/15 text-amber-300 border-amber-500/30': item.status === 'Menunggu Konfirmasi' || item.status === 'Pending',
                                        'bg-slate-500/15 text-slate-300 border-slate-500/30': item.status === 'Draft' || !item.status
                                    }"
                                    x-text="item.status || 'Draft'"></span>
                            </td>
                            <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                
                                <!-- 1. Tombol Cetak Berita Acara (BAST) di Kolom Aksi -->
                                <button type="button" @click="openPrintBast(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-purple-500/15 text-purple-300 hover:bg-purple-500/25 border border-purple-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                    <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                    </svg>
                                    <span>Cetak Berita Acara</span>
                                </button>

                                <!-- 2. Tombol Detail Modal -->
                                <button type="button" @click="openDetail(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-teal-500/15 text-teal-300 hover:bg-teal-500/25 border border-teal-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                    <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>

                                <!-- 3. Tombol Ubah Form -->
                                <a :href="'/distribusi/' + item.id + '/edit'"
                                    class="px-2.5 py-1.5 rounded-xl bg-slate-800 text-slate-300 hover:bg-slate-700 hover:text-white border border-slate-700 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Ubah</span>
                                </a>
                                
                                <!-- 4. Tombol Hapus -->
                                <button type="button" @click="deleteDistribusi(item.id)"
                                    class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    <span>Hapus</span>
                                </button>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
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
                        <button type="button" @click="terimaDistribusi(selectedDistribusi)"
                            class="px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all active:scale-95 flex items-center space-x-1"
                            title="Tanda Tangan Digital BSrE">
                            <span>✍️ TTD BSrE</span>
                        </button>

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

        <!-- MODAL DETAIL RINCIAN DISTRIBUSI BARANG (MENDUKUNG MULTI-BARANG & NIBAR REGISTER) -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl space-y-5 max-h-[90vh] overflow-y-auto">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <span class="p-2.5 rounded-2xl bg-teal-500/20 text-teal-300 text-xl border border-teal-500/30">🚚</span>
                        <div>
                            <h3 class="text-base sm:text-lg font-extrabold text-white">Detail Alokasi Penyerahan & Register NIBAR</h3>
                            <p class="text-xs text-slate-400 font-mono" x-text="selectedDistribusi ? ('Nomor Registrasi: ' + selectedDistribusi.kode + ' • BAST: ' + (selectedDistribusi.bast_nomor || selectedDistribusi.nomor_bast)) : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg text-lg font-bold">&times;</button>
                </div>

                <template x-if="selectedDistribusi">
                    <div class="space-y-5 text-xs">
                        
                        <!-- Informasi Ringkas Transaksi -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3.5 bg-slate-950 p-4 sm:p-5 rounded-2xl border border-slate-800">
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Tujuan Unit / Ruangan</span>
                                <span class="font-bold text-teal-300 text-xs block mt-0.5" x-text="selectedDistribusi.tujuan"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Pegawai Penerima (PJ)</span>
                                <span class="font-bold text-white text-xs block mt-0.5" x-text="selectedDistribusi.penerima || selectedDistribusi.pj_nama"></span>
                                <span class="text-[10px] text-slate-400 font-mono" x-text="selectedDistribusi.pj_nip ? ('NIP: ' + selectedDistribusi.pj_nip) : ''"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Tanggal Distribusi</span>
                                <span class="font-mono text-slate-300 text-xs block mt-0.5" x-text="selectedDistribusi.tgl || selectedDistribusi.tanggal_distribusi"></span>
                            </div>
                            <div>
                                <span class="text-slate-500 block text-[10px] uppercase font-bold tracking-wider">Status Penyerahan</span>
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-extrabold border mt-0.5"
                                      :class="{
                                          'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': selectedDistribusi.status === 'Telah Diterima' || selectedDistribusi.status === 'Diterima',
                                          'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': selectedDistribusi.status === 'Dalam Pengiriman' || selectedDistribusi.status === 'Dikirim',
                                          'bg-amber-500/15 text-amber-300 border-amber-500/30': selectedDistribusi.status === 'Menunggu Konfirmasi' || selectedDistribusi.status === 'Pending',
                                          'bg-slate-500/15 text-slate-300 border-slate-500/30': selectedDistribusi.status === 'Draft' || !selectedDistribusi.status
                                      }"
                                      x-text="selectedDistribusi.status"></span>
                            </div>
                            <div class="col-span-2 sm:col-span-4 pt-2 border-t border-slate-900 flex items-center justify-between">
                                <div class="text-slate-400 text-[11px]">
                                    <span class="font-bold text-slate-300">Catatan:</span>
                                    <span class="italic ml-1" x-text="selectedDistribusi.keterangan || '-'"></span>
                                </div>
                                <template x-if="selectedDistribusi.signed">
                                    <span class="inline-flex items-center space-x-1 text-[10px] text-emerald-400 font-mono bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                                        <span>✍️ E-Sign BSrE Sah:</span>
                                        <span x-text="selectedDistribusi.tgl_signed || 'Terverifikasi'"></span>
                                    </span>
                                </template>
                            </div>
                        </div>

                        <!-- Tabel Rincian Semua Barang & Register NIBAR yang Didistribusikan -->
                        <div>
                            <div class="flex items-center justify-between mb-2.5">
                                <span class="text-slate-200 font-extrabold text-xs flex items-center space-x-2">
                                    <span>📦 Rincian Barang & Nomor NIBAR 45 Karakter</span>
                                    <span class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold"
                                          x-text="(selectedDistribusi.items ? selectedDistribusi.items.length : 0) + ' Jenis Barang'"></span>
                                </span>
                                <span class="text-[11px] text-emerald-400 font-bold bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/20"
                                      x-text="'Total Volume: ' + getTotalQty(selectedDistribusi.items) + ' Item'"></span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-950 shadow-inner">
                                <table class="w-full text-left text-xs text-slate-300">
                                    <thead class="bg-slate-900/90 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800">
                                        <tr>
                                            <th class="px-3.5 py-3 text-center w-8">No</th>
                                            <th class="px-3.5 py-3 text-left min-w-[180px]">Nama Barang & Kode 108</th>
                                            <th class="px-3.5 py-3 text-left min-w-[140px]">Merk / Spesifikasi</th>
                                            <th class="px-3.5 py-3 text-left min-w-[320px]">Nomor Register NIBAR (45 Digit) &amp; Kondisi</th>
                                            <th class="px-3.5 py-3 text-center w-16">Vol</th>
                                            <th class="px-3.5 py-3 text-center w-16">Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/80">
                                        <template x-for="(item, idx) in selectedDistribusi.items" :key="idx">
                                            <tr class="hover:bg-slate-900/40 transition-colors">
                                                <td class="px-3.5 py-3 text-center font-bold text-slate-500 align-top" x-text="idx + 1"></td>
                                                
                                                <!-- Nama Barang & Kode 108 -->
                                                <td class="px-3.5 py-3 align-top">
                                                    <p class="font-extrabold text-white text-xs leading-snug" x-text="item.nama_barang"></p>
                                                    <p class="text-[10px] text-teal-400 font-mono mt-0.5" x-text="'Kode 108: ' + (item.kode_barang || '-')"></p>
                                                    <template x-if="item.jenis_nama">
                                                        <span class="text-[9.5px] text-slate-500 block truncate max-w-xs" x-text="item.jenis_nama"></span>
                                                    </template>
                                                </td>

                                                <!-- Merk & Spesifikasi -->
                                                <td class="px-3.5 py-3 text-slate-300 text-[11px] align-top">
                                                    <span class="font-semibold text-slate-200" x-text="item.merk_type || item.merk || '-'"></span>
                                                    <template x-if="item.keterangan">
                                                        <p class="text-[10px] text-slate-500 italic mt-0.5" x-text="'Ket: ' + item.keterangan"></p>
                                                    </template>
                                                </td>

                                                <!-- NIBAR + Kondisi (digabung dalam 1 kolom) -->
                                                <td class="px-3.5 py-3 align-top">
                                                    <!-- Ada nibar_registers: tampil NIBAR + badge kondisi inline -->
                                                    <template x-if="item.nibar_registers && item.nibar_registers.length > 0">
                                                        <div class="space-y-1">
                                                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-medium mb-1">
                                                                <span class="text-teal-300 font-semibold" x-text="item.nibar_registers.length + ' Unit NIBAR Terdaftar:'"></span>
                                                                <span class="text-[9.5px] text-slate-500 font-mono">Klik NIBAR untuk detail barang</span>
                                                            </div>
                                                            <template x-for="(reg, nIdx) in item.nibar_registers" :key="reg.nibar || nIdx">
                                                                <div class="flex items-center justify-between bg-slate-900 hover:bg-slate-800/80 border border-slate-800 hover:border-teal-500/50 rounded-xl px-2.5 transition-all group h-[30px]">
                                                                    <!-- Kiri: nomor urut + link NIBAR -->
                                                                    <div class="flex items-center space-x-1.5 overflow-hidden min-w-0">
                                                                        <span class="text-slate-500 font-mono text-[9.5px] shrink-0" x-text="'#' + (nIdx + 1)"></span>
                                                                        <a :href="'/scan/' + reg.nibar" target="_blank"
                                                                           class="font-mono text-[10.5px] font-bold text-teal-300 group-hover:text-teal-200 group-hover:underline tracking-tight select-all truncate"
                                                                           title="Buka Detail Barang Aset"
                                                                           x-text="reg.nibar">
                                                                        </a>
                                                                    </div>
                                                                    <!-- Kanan: badge kondisi + tombol aksi -->
                                                                    <div class="flex items-center space-x-1.5 shrink-0 ml-2">
                                                                        <!-- Badge Kondisi inline -->
                                                                        <span class="px-2 py-0.5 rounded-lg text-[9px] font-bold whitespace-nowrap"
                                                                              :class="{
                                                                                  'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30': reg.kondisi === 'Baik' || !reg.kondisi,
                                                                                  'bg-amber-500/15 text-amber-300 border border-amber-500/30': reg.kondisi === 'Kurang Baik',
                                                                                  'bg-orange-500/15 text-orange-300 border border-orange-500/30': reg.kondisi === 'Rusak Ringan',
                                                                                  'bg-rose-500/15 text-rose-300 border border-rose-500/30': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'Rusak'
                                                                              }"
                                                                              x-text="reg.kondisi || 'Baik'"></span>
                                                                        <!-- Salin NIBAR -->
                                                                        <button type="button" @click="copyNibar(reg.nibar)"
                                                                                class="p-1 rounded-md bg-slate-800 hover:bg-teal-500 text-slate-400 hover:text-slate-950 transition-all text-[9.5px]"
                                                                                :title="copiedNibar === reg.nibar ? 'Tersalin!' : 'Salin NIBAR'">
                                                                            <span x-show="copiedNibar !== reg.nibar">📋</span>
                                                                            <span x-show="copiedNibar === reg.nibar" class="text-emerald-400 font-bold">✓</span>
                                                                        </button>
                                                                        <!-- Detail Barang -->
                                                                        <a :href="'/scan/' + reg.nibar" target="_blank"
                                                                           class="p-1 rounded-md bg-teal-500/15 hover:bg-teal-500 text-teal-300 hover:text-slate-950 transition-all text-[9.5px]"
                                                                           title="Lihat Detail Barang Aset">
                                                                            <span>🔍</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Fallback: hanya nibar_list tanpa kondisi -->
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && item.nibar_list && item.nibar_list.length > 0">
                                                        <div class="space-y-1">
                                                            <div class="flex items-center justify-between text-[10px] text-slate-400 font-medium mb-1">
                                                                <span class="text-teal-300 font-semibold" x-text="item.nibar_list.length + ' Unit NIBAR Terdaftar:'"></span>
                                                                <span class="text-[9.5px] text-slate-500 font-mono">Klik NIBAR untuk detail barang</span>
                                                            </div>
                                                            <template x-for="(nibar, nIdx) in item.nibar_list" :key="nIdx">
                                                                <div class="flex items-center justify-between bg-slate-900 hover:bg-slate-800/80 border border-slate-800 hover:border-teal-500/50 rounded-xl px-2.5 transition-all group h-[30px]">
                                                                    <div class="flex items-center space-x-1.5 overflow-hidden min-w-0">
                                                                        <span class="text-slate-500 font-mono text-[9.5px] shrink-0" x-text="'#' + (nIdx + 1)"></span>
                                                                        <a :href="'/scan/' + nibar" target="_blank"
                                                                           class="font-mono text-[10.5px] font-bold text-teal-300 group-hover:text-teal-200 group-hover:underline tracking-tight select-all truncate"
                                                                           title="Buka Detail Barang Aset"
                                                                           x-text="nibar">
                                                                        </a>
                                                                    </div>
                                                                    <div class="flex items-center space-x-1 shrink-0 ml-2">
                                                                        <button type="button" @click="copyNibar(nibar)"
                                                                                class="p-1 rounded-md bg-slate-800 hover:bg-teal-500 text-slate-400 hover:text-slate-950 transition-all text-[9.5px]"
                                                                                :title="copiedNibar === nibar ? 'Tersalin!' : 'Salin NIBAR'">
                                                                            <span x-show="copiedNibar !== nibar">📋</span>
                                                                            <span x-show="copiedNibar === nibar" class="text-emerald-400 font-bold">✓</span>
                                                                        </button>
                                                                        <a :href="'/scan/' + nibar" target="_blank"
                                                                           class="p-1 rounded-md bg-teal-500/15 hover:bg-teal-500 text-teal-300 hover:text-slate-950 transition-all text-[9.5px]"
                                                                           title="Lihat Detail Barang Aset">
                                                                            <span>🔍</span>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Belum ada NIBAR sama sekali -->
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && (!item.nibar_list || item.nibar_list.length === 0)">
                                                        <div class="inline-flex items-center space-x-1.5 px-2.5 py-1 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[10.5px] font-mono">
                                                            <span>⚠️</span>
                                                            <span>Belum ditentukan</span>
                                                        </div>
                                                    </template>
                                                </td>

                                                <!-- Volume & Satuan -->
                                                <td class="px-3.5 py-3 text-center font-bold text-emerald-400 text-xs align-top" x-text="item.qty"></td>
                                                <td class="px-3.5 py-3 text-center text-slate-300 text-xs align-top" x-text="item.satuan"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>

                <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
                    <div class="text-[11px] text-slate-500 font-mono">
                        <span>Format NIBAR: 45 Digit Kode BMD RSUD dr. H. Koesnandi</span>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all shadow-md active:scale-95">
                        Tutup
                    </button>
                </div>
            </div>
        </div>

        <!-- MODAL UBAH STATUS DISTRIBUSI -->
        <div x-show="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
            <div @click.away="showEditModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <h3 class="text-base font-bold text-white">✏️ Ubah Status Distribusi</h3>
                    <button type="button" @click="showEditModal = false" class="text-slate-500 hover:text-white">&times;</button>
                </div>
                <form @submit.prevent="showEditModal = false" class="space-y-4 text-xs">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Judul / Ringkasan Barang</label>
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
