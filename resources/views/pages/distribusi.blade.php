@php
    $isSubAdmin = (Auth::user()->role ?? '') === 'sub_admin';
    $pageTitle = $isSubAdmin ? 'Pengajuan Baru' : 'Distribusi ASTAP';
    $breadcrumbTitle = $isSubAdmin ? 'Master Utama / Pengajuan Baru' : 'Master Utama / Distribusi ASTAP';
@endphp

<x-layout :title="$pageTitle . ' - SIMAT-RK'">
    @section('page-title', $pageTitle)
    @section('breadcrumb', $breadcrumbTitle)

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
                selectedDistribusi: null,

                unitList: {{ Js::from($units ?? []) }},

                distribusis: {{ Js::from($distribusis ?? []) }},

                init() {
                    // Sub admin: SELALU gunakan data dari backend (jangan baca localStorage)
                    // karena localStorage bisa berisi data sesi admin/user lain
                    if (this.userRole === 'sub_admin') {
                        localStorage.removeItem('simat_distribusis');
                        return; // distribusis sudah di-set dari {{ Js::from($distribusis) }}
                    }

                    // Admin/Master: prioritaskan data dari DB, fallback ke localStorage
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
                    // Sub admin: jangan simpan ke localStorage
                    if (this.userRole === 'sub_admin') return;
                    localStorage.setItem('simat_distribusis', JSON.stringify(this.distribusis));
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

                deleteDistribusi(item) {
                    if (!item) return;
                    const targetObj = typeof item === 'object' && item !== null ? item : this.distribusis.find(d => d.id === item);
                    const targetId = typeof item === 'object' && item !== null ? item.id : item;

                    this.askConfirmation({
                        title: '⚠️ Konfirmasi Hapus Transaksi Distribusi',
                        message: 'Apakah Anda yakin ingin menghapus data transaksi distribusi aset ini dari sistem? Status unit register NIBAR terkait akan dikembalikan ke Gudang Aset Utama.',
                        itemName: targetObj ? ((targetObj.kode || 'DIST') + ' - ' + (targetObj.nama || 'Aset') + ' (' + (targetObj.tujuan || 'Unit') + ')') : ('ID: ' + targetId),
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Transaksi Distribusi',
                        onConfirm: async () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            try {
                                await fetch('/distribusi/' + targetId, {
                                    method: 'DELETE',
                                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                                });
                                window.location.reload();
                            } catch(err) {
                                window.location.reload();
                            }
                        }
                    });
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
                    if (!item) return;
                    const targetId = typeof item === 'object' ? item.id : item;
                    window.location.href = '/berita-acara?tab=distribusi&id=' + encodeURIComponent(targetId);
                },

                terimaDistribusi(item) {
                    this.toggleSignDistribusi(item);
                },

                async toggleSignDistribusi(item) {
                    if (!item || !item.id) return;
                    const target = this.distribusis.find(d => d.id === item.id) || item;

                    try {
                        const resp = await fetch(`/distribusi/${item.id}/sign`, {
                            method: 'PATCH',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                            }
                        });
                        const data = await resp.json();
                        if (!data.success) throw new Error(data.message || 'Gagal menyimpan status TTD.');

                        // Update semua referensi objek di state Alpine
                        const applyUpdate = (obj) => {
                            obj.signed     = data.signed;
                            obj.tgl_signed = data.tgl_signed;
                            obj.qr_hash    = data.qr_hash;
                            obj.status     = data.signed ? 'Telah Diterima' : 'Menunggu Konfirmasi';
                        };
                        applyUpdate(target);
                        if (item !== target) applyUpdate(item);
                        if (this.selectedDistribusi && this.selectedDistribusi.id === item.id) {
                            applyUpdate(this.selectedDistribusi);
                        }
                        this.saveToStorage();

                        alert(data.signed
                            ? '✍️ BAST (' + (target.nomor_bast || target.kode) + ') berhasil ditandatangani secara digital BSrE! Status tersimpan.'
                            : '↩️ Tanda tangan BSrE BAST (' + (target.nomor_bast || target.kode) + ') berhasil dibatalkan.');
                    } catch (err) {
                        alert('❌ Gagal menyimpan status TTD: ' + err.message);
                    }
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
                }
            }
        }
    </script>

    <div x-data="distribusiCatalog()" x-cloak>

        <!-- KONTEN UTAMA KATALOG DISTRIBUSI (DISEMBUNYIKAN SAAT DICETAK) -->
        <div class="no-print space-y-6">

            <!-- Header Banner & Mini KPI Strip -->
            <div class="bg-gradient-to-r from-teal-600/15 via-slate-900 to-slate-900 border border-teal-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-6 relative overflow-hidden">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 relative z-10">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-xs font-bold mb-3">
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                        <span>{{ $isSubAdmin ? 'PENGAJUAN & PERMOHONAN BARANG ASET RUANGAN' : 'PENYERAHAN & ALOKASI BARANG ASET RSUD' }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">{{ $isSubAdmin ? 'Katalog Pengajuan Baru' : 'Katalog Distribusi ASTAP' }}</h1>
                    <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                        {{ $isSubAdmin 
                            ? 'Daftar pengajuan permohonan alokasi barang aset ruangan. Anda dapat mengajukan permintaan kebutuhan barang baru untuk unit/ruangan Anda.' 
                            : 'Pengelolaan alokasi penyerahan barang aset dari inventaris ke paviliun rawat inap, unit RSUD, dan instalasi RSUD. Mendukung distribusi beberapa barang sekaligus dalam satu transaksi.' }}
                    </p>
                </div>
                
                <!-- Action Button Input Baru -->
                <div class="flex items-center gap-2.5 shrink-0">
                    <a href="{{ route('distribusi.create') }}"
                        class="px-4 py-2.5 rounded-xl bg-teal-500 hover:bg-teal-400 text-slate-950 font-bold text-xs shadow-lg shadow-teal-500/20 transition-all flex items-center space-x-2 shrink-0">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        <span>{{ $isSubAdmin ? 'Input Pengajuan Baru' : 'Input Distribusi Baru' }}</span>
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

        <!-- Tabel Distribusi ASTAP (Multi-Barang / Transaksi) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 custom-scrollbar min-h-[520px]" style="max-height: calc(100vh - 200px); overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-h-[480px]">
                    <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">No. Distribusi</th>
                            <th class="px-4 py-3.5 text-left min-w-[260px] bg-slate-950">Rincian Barang yang Didistribusikan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Tujuan Unit / Ruangan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Tgl Distribusi</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Penerima</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Status</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0 min-w-[210px]" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredDistribusis" :key="item.id">
                            <tr class="group hover:bg-slate-800/40 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                                <td class="px-4 py-4 text-center font-mono font-semibold text-teal-400 whitespace-nowrap" x-text="item.kode"></td>
                                
                                <!-- Kolom Barang: Ringkasan Rapi (Detail Lengkap Dapat Dilihat di Modal Detail / BAST) -->
                                <td class="px-4 py-4">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center space-x-2">
                                            <p class="font-extrabold text-white text-xs" x-text="item.nama"></p>
                                            <template x-if="item.items && item.items.length > 0">
                                                <span class="px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold shrink-0"
                                                      x-text="item.items.length + ' Jenis Barang'">
                                                </span>
                                            </template>
                                        </div>
                                        <!-- Badge Volume per Item -->
                                        <template x-if="item.items && item.items.length > 0">
                                            <div class="flex flex-wrap gap-1.5 pt-0.5">
                                                <template x-for="(it, iIdx) in item.items.slice(0, 3)" :key="iIdx">
                                                    <div class="inline-flex items-center space-x-1 text-[10px] font-mono bg-slate-950 border border-slate-800 rounded-lg px-2 py-0.5">
                                                        <span class="text-slate-400 truncate max-w-[100px]" x-text="it.nama_barang || '-'"></span>
                                                        <span class="text-slate-600">|</span>
                                                        <!-- Volume Pengajuan -->
                                                        <span class="text-teal-400 font-bold" :title="'Volume Pengajuan: ' + (it.qty || 0)" x-text="'📋 ' + (it.qty || 0)"></span>
                                                        <!-- Volume ACC -->
                                                        <template x-if="it.qty_acc !== null && it.qty_acc !== undefined">
                                                            <span class="font-bold" :class="it.qty_acc > 0 ? 'text-emerald-400' : 'text-rose-400'" :title="'Volume Di-ACC: ' + it.qty_acc" x-text="'✅ ' + it.qty_acc"></span>
                                                        </template>
                                                        <template x-if="it.qty_acc === null || it.qty_acc === undefined">
                                                            <span class="text-amber-500/70" title="Belum Di-ACC">⏳</span>
                                                        </template>
                                                    </div>
                                                </template>
                                                <template x-if="item.items.length > 3">
                                                    <span class="text-[10px] text-slate-500 italic" x-text="'+ ' + (item.items.length - 3) + ' lainnya'"></span>
                                                </template>
                                            </div>
                                        </template>
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
                                <!-- Kolom Aksi — FREEZE STICKY RIGHT -->
                                <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[210px]" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- 1. Tombol Cetak Berita Acara (BAST) di Kolom Aksi -> Direct ke Cetak/Edit BAST Terintegrasi -->
                                        <a :href="'/berita-acara?tab=distribusi&id=' + item.id"
                                            title="Cetak Berita Acara (BAST)"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 border border-purple-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                            <span>Cetak Berita Acara</span>
                                        </a>

                                        <!-- 2. Tombol Detail Modal -->
                                        <button type="button" @click="openDetail(item)"
                                            title="Lihat Detail Distribusi"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </button>

                                        <!-- 3. Tombol Ubah Form (tersembunyi untuk sub admin jika sudah Dalam Pengiriman / Telah Diterima) -->
                                        <a :href="'/distribusi/' + item.id + '/edit'"
                                            x-show="!(userRole === 'sub_admin' && ['Dalam Pengiriman','Telah Diterima'].includes(item.status))"
                                            title="Ubah Data Distribusi"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Ubah</span>
                                        </a>
                                        
                                        <!-- 4. Tombol Hapus (tersembunyi untuk sub admin jika sudah Dalam Pengiriman / Telah Diterima) -->
                                        <button type="button" @click="deleteDistribusi(item.id)"
                                            x-show="!(userRole === 'sub_admin' && ['Dalam Pengiriman','Telah Diterima'].includes(item.status))"
                                            title="Hapus Data Distribusi"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State Baris Penuh Tinggi -->
                        <template x-if="filteredDistribusis.length === 0">
                            <tr>
                                <td colspan="8" class="text-center align-middle py-28 text-slate-400">
                                    <div class="flex flex-col items-center justify-center space-y-2 py-4">
                                        <p class="text-sm font-semibold text-slate-300">
                                            {{ $isSubAdmin ? 'Belum ada data permohonan pengajuan baru.' : 'Tidak ada data transaksi distribusi yang cocok.' }}
                                        </p>
                                        <p class="text-xs text-slate-500">Coba sesuaikan kata kunci pencarian atau filter status Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END KONTEN UTAMA KATALOG (no-print) -->

        <!-- MODAL DETAIL RINCIAN DISTRIBUSI BARANG (MENDUKUNG MULTI-BARANG & NIBAR REGISTER) -->
        <div x-show="showDetailModal" class="no-print fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
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

                            <div class="overflow-x-auto overflow-y-auto rounded-2xl border border-slate-800 bg-slate-950 shadow-inner" style="max-height: 380px; overflow-y: auto;">
                                <table class="w-full text-left text-xs text-slate-300">
                                    <thead class="bg-slate-900 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-800 shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #0f172a;">
                                        <tr>
                                            <th class="px-3.5 py-3 text-center w-8">No</th>
                                            <th class="px-3.5 py-3 text-center min-w-[180px]">Nama Barang & Kode 108</th>
                                            <th class="px-3.5 py-3 text-center min-w-[140px]">Merk / Spesifikasi</th>
                                            <th class="px-3.5 py-3 text-center min-w-[300px]">Nomor Register NIBAR (45 Digit)</th>
                                            <th class="px-3.5 py-3 text-center w-24">Kondisi</th>
                                            <th class="px-3.5 py-3 text-center w-24">Vol / Satuan</th>
                                            <th class="px-3.5 py-3 text-center w-20">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/80">
                                        <template x-for="(item, idx) in selectedDistribusi.items" :key="idx">
                                            <tr class="hover:bg-slate-900/40 transition-colors">
                                                <td class="px-3.5 py-3 text-center font-bold text-slate-500 align-top" x-text="idx + 1"></td>
                                                
                                                <!-- Nama Barang & Kode 108 -->
                                                <td class="px-3.5 py-3 align-top text-center">
                                                    <p class="font-extrabold text-white text-xs leading-snug" x-text="item.nama_barang"></p>
                                                    <p class="text-[10px] text-teal-400 font-mono mt-0.5" x-text="'Kode 108: ' + (item.kode_barang || '-')"></p>
                                                    <template x-if="item.jenis_nama">
                                                        <span class="text-[9.5px] text-slate-500 block truncate max-w-xs" x-text="item.jenis_nama"></span>
                                                    </template>
                                                </td>

                                                <!-- Merk & Spesifikasi -->
                                                <td class="px-3.5 py-3 text-slate-300 text-[11px] align-top text-center">
                                                    <span class="font-semibold text-slate-200" x-text="item.merk_type || item.merk || '-'"></span>
                                                    <template x-if="item.keterangan">
                                                        <p class="text-[10px] text-slate-500 italic mt-0.5" x-text="'Ket: ' + item.keterangan"></p>
                                                    </template>
                                                </td>

                                                <!-- Kolom NIBAR -->
                                                <td class="px-3.5 py-3 align-top text-center">
                                                    <!-- Ada nibar_registers -->
                                                    <template x-if="item.nibar_registers && item.nibar_registers.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(reg, nIdx) in item.nibar_registers" :key="reg.nibar || nIdx">
                                                                <div class="flex items-center h-[32px] bg-slate-800/90 hover:bg-slate-800 border border-slate-700/60 hover:border-teal-500/50 rounded-lg px-3 transition-all group">
                                                                    <span class="text-slate-400 font-mono text-[10px] font-semibold shrink-0 mr-2" x-text="'#' + (nIdx + 1)"></span>
                                                                    <a :href="'/scan/' + reg.nibar" target="_blank"
                                                                       class="font-mono text-[11px] font-bold text-teal-300 group-hover:text-teal-200 group-hover:underline tracking-tight select-all truncate"
                                                                       title="Buka Detail Barang Aset"
                                                                       x-text="reg.nibar">
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Fallback: nibar_list -->
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && item.nibar_list && item.nibar_list.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(nibar, nIdx) in item.nibar_list" :key="nIdx">
                                                                <div class="flex items-center h-[32px] bg-slate-800/90 hover:bg-slate-800 border border-slate-700/60 hover:border-teal-500/50 rounded-lg px-3 transition-all group">
                                                                    <span class="text-slate-400 font-mono text-[10px] font-semibold shrink-0 mr-2" x-text="'#' + (nIdx + 1)"></span>
                                                                    <a :href="'/scan/' + nibar" target="_blank"
                                                                       class="font-mono text-[11px] font-bold text-teal-300 group-hover:text-teal-200 group-hover:underline tracking-tight select-all truncate"
                                                                       title="Buka Detail Barang Aset"
                                                                       x-text="nibar">
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>

                                                    <!-- Belum ada NIBAR -->
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && (!item.nibar_list || item.nibar_list.length === 0)">
                                                        <div class="inline-flex items-center h-[32px] space-x-1.5 px-3 rounded-lg bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[10.5px] font-mono">
                                                            <span>⚠️</span>
                                                            <span>Belum ditentukan</span>
                                                        </div>
                                                    </template>
                                                </td>

                                                <!-- Kolom Kondisi — tinggi h-[32px] & gap-3 sama persis dengan NIBAR -->
                                                <td class="px-2 py-3 align-top">
                                                    <template x-if="item.nibar_registers && item.nibar_registers.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(reg, kIdx) in item.nibar_registers" :key="'k-' + (reg.nibar || kIdx)">
                                                                <div class="flex items-center justify-center h-[32px]">
                                                                    <span class="h-[32px] px-3 flex items-center justify-center rounded-lg text-[10px] font-bold whitespace-nowrap"
                                                                          :class="{
                                                                              'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30': reg.kondisi === 'Baik' || !reg.kondisi,
                                                                              'bg-amber-500/15 text-amber-300 border border-amber-500/30': reg.kondisi === 'Kurang Baik',
                                                                              'bg-orange-500/15 text-orange-300 border border-orange-500/30': reg.kondisi === 'Rusak Ringan',
                                                                              'bg-rose-500/15 text-rose-300 border border-rose-500/30': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'Rusak'
                                                                          }"
                                                                          x-text="reg.kondisi || 'Baik'"></span>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="!item.nibar_registers || item.nibar_registers.length === 0">
                                                        <div class="flex items-center justify-center h-[32px]">
                                                            <span class="h-[32px] px-3 flex items-center justify-center rounded-lg text-[10px] font-bold"
                                                                  :class="{
                                                                      'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30': item.kondisi === 'Baik',
                                                                      'bg-amber-500/15 text-amber-300 border border-amber-500/30': item.kondisi === 'Kurang Baik' || item.kondisi === 'KB',
                                                                      'bg-orange-500/15 text-orange-300 border border-orange-500/30': item.kondisi === 'Rusak Ringan',
                                                                      'bg-rose-500/15 text-rose-300 border border-rose-500/30': item.kondisi === 'Rusak Berat' || item.kondisi === 'Rusak'
                                                                  }"
                                                                  x-text="item.kondisi || 'Baik'"></span>
                                                        </div>
                                                    </template>
                                                </td>



                                                <!-- Volume & Satuan (digabung) -->
                                                <td class="px-3.5 py-3 text-center align-top">
                                                    <span class="font-bold text-emerald-400 text-xs" x-text="item.qty"></span>
                                                    <span class="text-slate-400 text-[10px] ml-0.5" x-text="item.satuan"></span>
                                                </td>

                                                <!-- Kolom Aksi (dipindah ke kanan) -->
                                                <td class="px-2 py-3 align-top">
                                                    <template x-if="item.nibar_registers && item.nibar_registers.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(reg, aIdx) in item.nibar_registers" :key="'aksi2-' + (reg.nibar || aIdx)">
                                                                <div class="flex items-center justify-center gap-1.5 h-[32px]">
                                                                    <button type="button" @click="copyNibar(reg.nibar)"
                                                                            class="h-[32px] w-[32px] flex items-center justify-center rounded-lg bg-slate-800 hover:bg-teal-500 text-slate-400 hover:text-slate-950 border border-slate-700/60 transition-all text-xs"
                                                                            :title="copiedNibar === reg.nibar ? 'Tersalin!' : 'Salin NIBAR'">
                                                                        <span x-show="copiedNibar !== reg.nibar">📋</span>
                                                                        <span x-show="copiedNibar === reg.nibar" class="text-emerald-400 font-bold">✓</span>
                                                                    </button>
                                                                    <a :href="'/scan/' + reg.nibar" target="_blank"
                                                                       class="h-[32px] w-[32px] flex items-center justify-center rounded-lg bg-teal-500/15 hover:bg-teal-500 text-teal-300 hover:text-slate-950 border border-teal-500/30 transition-all text-xs"
                                                                       title="Lihat Detail Barang Aset">
                                                                        🔍
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && item.nibar_list && item.nibar_list.length > 0">
                                                        <div class="flex flex-col gap-3">
                                                            <template x-for="(nibar, aIdx) in item.nibar_list" :key="'aksi2-' + aIdx">
                                                                <div class="flex items-center justify-center gap-1.5 h-[32px]">
                                                                    <button type="button" @click="copyNibar(nibar)"
                                                                            class="h-[32px] w-[32px] flex items-center justify-center rounded-lg bg-slate-800 hover:bg-teal-500 text-slate-400 hover:text-slate-950 border border-slate-700/60 transition-all text-xs"
                                                                            :title="copiedNibar === nibar ? 'Tersalin!' : 'Salin NIBAR'">
                                                                        <span x-show="copiedNibar !== nibar">📋</span>
                                                                        <span x-show="copiedNibar === nibar" class="text-emerald-400 font-bold">✓</span>
                                                                    </button>
                                                                    <a :href="'/scan/' + nibar" target="_blank"
                                                                       class="h-[32px] w-[32px] flex items-center justify-center rounded-lg bg-teal-500/15 hover:bg-teal-500 text-teal-300 hover:text-slate-950 border border-teal-500/30 transition-all text-xs"
                                                                       title="Lihat Detail Barang Aset">
                                                                        🔍
                                                                    </a>
                                                                </div>
                                                            </template>
                                                        </div>
                                                    </template>
                                                    <template x-if="(!item.nibar_registers || item.nibar_registers.length === 0) && (!item.nibar_list || item.nibar_list.length === 0)">
                                                        <div class="h-[32px] flex items-center justify-center">
                                                            <span class="text-slate-600 text-[10px]">—</span>
                                                        </div>
                                                    </template>
                                                </td>
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
                    <div class="flex items-center space-x-2">
                        <a :href="selectedDistribusi ? ('/berita-acara?tab=distribusi&id=' + selectedDistribusi.id) : '#'"
                           class="px-4 py-2.5 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-extrabold text-xs transition-all shadow-md active:scale-95 inline-flex items-center space-x-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            <span>🖨️ Cetak / Edit BAST</span>
                        </a>
                        <button type="button" @click="showDetailModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all shadow-md active:scale-95">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL UBAH STATUS DISTRIBUSI -->
        <div x-show="showEditModal" class="no-print fixed inset-0 z-50 flex items-center justify-center bg-slate-950/80 backdrop-blur-sm p-4" x-cloak>
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

        <!-- GLOBAL CUSTOM CONFIRMATION DIALOG MODAL (Sleek Dark Theme) -->
        <div x-show="showConfirmModal" x-cloak class="no-print fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4">
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
        <div x-show="toast.show" x-cloak class="no-print fixed bottom-6 right-6 z-50 max-w-sm w-full bg-slate-900/95 border rounded-2xl p-4 shadow-2xl backdrop-blur-md flex items-center justify-between space-x-3"
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
