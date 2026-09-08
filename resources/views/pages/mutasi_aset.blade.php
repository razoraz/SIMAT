<x-layout title="Mutasi Aset - SIMAT-RK">
    @section('page-title', 'Mutasi Aset')
    @section('breadcrumb', 'Master Utama / Mutasi Aset')

    <script>
        function mutasiCatalog() {
            return {
                userRole: {{ Js::from(Auth::user()?->role ?? 'admin') }},
                userUnit: {{ Js::from(Auth::user()?->unitModel?->nama ?? (Auth::user()?->unit ?? '')) }},
                userName: {{ Js::from(Auth::user()?->name ?? 'Admin') }},

                searchQuery: '',
                statusFilter: 'all',
                showDetailModal: false,

                selectedMutasi: null,

                mutasis: {{ Js::from($mutasis) }},

                get filteredMutasis() {
                    const query = (this.searchQuery || '').toLowerCase().trim();
                    const role = this.userRole;
                    const myUnit = (this.userUnit || '').toLowerCase().trim();
                    const myName = (this.userName || '').toLowerCase().trim();

                    return this.mutasis.filter(item => {
                        // Jika Sub Admin: hanya tampilkan mutasi yang melibatkan unit dia
                        if (role === 'sub_admin' && myUnit) {
                            const isAsal = (item.asal || '').toLowerCase().includes(myUnit) || 
                                           (item.pemohon || '').toLowerCase().includes(myName) ||
                                           myUnit.includes((item.asal || '').toLowerCase());
                            const isTujuan = (item.tujuan || '').toLowerCase().includes(myUnit) || 
                                             (item.penerima_pj || '').toLowerCase().includes(myName) ||
                                             myUnit.includes((item.tujuan || '').toLowerCase());
                            if (!isAsal && !isTujuan) return false;
                        }

                        // Search query
                        const matchSearch = !query ||
                            (item.nama || '').toLowerCase().includes(query) ||
                            (item.kode || '').toLowerCase().includes(query) ||
                            (item.kode_barang || '').toLowerCase().includes(query) ||
                            (item.tujuan || '').toLowerCase().includes(query) ||
                            (item.asal || '').toLowerCase().includes(query) ||
                            (item.pemohon || '').toLowerCase().includes(query) ||
                            (item.penerima_pj || '').toLowerCase().includes(query) ||
                            (item.jenis || '').toLowerCase().includes(query);

                        // Status filter tab
                        let matchStatus = true;
                        if (this.statusFilter === 'selesai') {
                            matchStatus = item.status === 'Disetujui Admin (Selesai)' || item.persetujuan_admin;
                        } else if (this.statusFilter === 'menunggu_admin') {
                            matchStatus = item.status === 'Disetujui 2 Pihak (Menunggu Admin)' || (item.persetujuan_penerima && !item.persetujuan_admin && item.status !== 'Ditolak');
                        } else if (this.statusFilter === 'menunggu_penerima') {
                            matchStatus = item.status === 'Menunggu Persetujuan Penerima' || (!item.persetujuan_penerima && item.status !== 'Ditolak');
                        } else if (this.statusFilter === 'ditolak') {
                            matchStatus = item.status === 'Ditolak';
                        }

                        return matchSearch && matchStatus;
                    });
                },

                get countAll() {
                    return (this.mutasis || []).length;
                },

                get countSelesai() {
                    return (this.mutasis || []).filter(m => m.persetujuan_admin || m.status === 'Disetujui Admin (Selesai)').length;
                },

                get countMenungguAdmin() {
                    return (this.mutasis || []).filter(m => m.status === 'Disetujui 2 Pihak (Menunggu Admin)' || (m.persetujuan_penerima && !m.persetujuan_admin && m.status !== 'Ditolak')).length;
                },

                get countMenungguPenerima() {
                    return (this.mutasis || []).filter(m => m.status === 'Menunggu Persetujuan Penerima' || (!m.persetujuan_penerima && m.status !== 'Ditolak')).length;
                },

                get countMenunggu() {
                    return this.countMenungguAdmin + this.countMenungguPenerima;
                },

                get countDitolak() {
                    return (this.mutasis || []).filter(m => m.status === 'Ditolak').length;
                },

                get countUnits() {
                    const set = new Set();
                    this.mutasis.forEach(m => {
                        if (m.asal) set.add(m.asal);
                        if (m.tujuan) set.add(m.tujuan);
                    });
                    return set.size;
                },

                resetFilters() {
                    this.searchQuery = '';
                    this.statusFilter = 'all';
                },

                init() {
                    // Auto-buka modal detail jika kembali dari halaman Berita Acara (BAMB)
                    const urlParams = new URLSearchParams(window.location.search);
                    const openDetailId = urlParams.get('openDetail');
                    if (openDetailId) {
                        this.$nextTick(() => {
                            const target = this.mutasis.find(m => String(m.id) === String(openDetailId));
                            if (target) {
                                this.openDetail(target);
                            }
                        });
                        // Bersihkan parameter dari URL tanpa reload
                        const cleanUrl = window.location.pathname;
                        window.history.replaceState({}, '', cleanUrl);
                    }
                },

                canPrint(item) {
                    if (!item) return false;
                    return (item.persetujuan_pengirim && item.persetujuan_penerima && item.persetujuan_admin) || 
                           item.status === 'Disetujui Admin (Selesai)';
                },

                askConfirmation({ title, message, itemName, type = 'danger', btnText, onConfirm }) {
                    this.$dispatch('ask-confirm', {
                        title: title || 'Konfirmasi Tindakan',
                        message: message || 'Apakah Anda yakin ingin melanjutkan tindakan ini?',
                        itemName: itemName || '',
                        type: type,
                        btnText: btnText || (type === 'danger' ? 'Ya, Hapus Data' : (type === 'warning' ? 'Ya, Simpan Perubahan' : 'Ya, Tambahkan')),
                        onConfirm: onConfirm
                    });
                },

                showSimatToast(message, type = 'success') {
                    this.$dispatch('show-toast', { message, type });
                },

                canApprovePengirim(item) {
                    if (!item || item.persetujuan_pengirim || item.status === 'Ditolak') return false;
                    if (this.userRole !== 'sub_admin') return false;
                    if (!this.userUnit) return true;
                    const myUnit = (this.userUnit || '').toLowerCase().trim();
                    const asalUnit = (item.asal || '').toLowerCase().trim();
                    return asalUnit.includes(myUnit) || myUnit.includes(asalUnit) || myUnit === asalUnit;
                },

                canApprovePenerima(item) {
                    if (!item || item.persetujuan_penerima || item.status === 'Ditolak') return false;
                    const destUnit = (item.tujuan || '').toLowerCase().trim();
                    const isReturnToGudang = item.jenis === 'Pengembalian' && (
                        destUnit.includes('perbekalan') || destUnit.includes('rumah tangga') || destUnit.includes('gudang')
                    );
                    if (isReturnToGudang) return false;
                    if (this.userRole !== 'sub_admin') return false;
                    if (!this.userUnit) return true;
                    const myUnit = (this.userUnit || '').toLowerCase().trim();
                    return destUnit.includes(myUnit) || myUnit.includes(destUnit) || myUnit === destUnit;
                },

                canApproveAdmin(item) {
                    if (!item || item.persetujuan_admin || item.status === 'Ditolak') return false;
                    return this.userRole === 'admin' || this.userRole === 'master_admin';
                },

                approvePengirim(item) {
                    if (!item) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/mutasi-aset/' + item.id + '/approve-pengirim', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(d => {
                        if (d.success) {
                            item.persetujuan_pengirim = true;
                            if (d.is_completed) {
                                item.status = 'Disetujui Admin (Selesai)';
                            } else {
                                item.status = 'Disetujui Pengirim (Menunggu Pihak Lain)';
                            }
                            this.showSimatToast('✅ Mutasi berhasil disetujui sebagai Pihak Pengirim!', 'success');
                            setTimeout(() => window.location.reload(), 800);
                        } else {
                            this.showSimatToast('⚠️ Gagal menyetujui mutasi.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('approvePengirim error:', err);
                        window.location.reload();
                    });
                },

                approvePenerima(item) {
                    if (!item) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/mutasi-aset/' + item.id + '/approve-penerima', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(d => {
                        if (d.success) {
                            item.persetujuan_penerima = true;
                            const newStatus = item.persetujuan_admin ? 'Disetujui Admin (Selesai)' : 'Disetujui 2 Pihak (Menunggu Admin)';
                            item.status = newStatus;
                            if (this.selectedMutasi && this.selectedMutasi.id === item.id) {
                                this.selectedMutasi.persetujuan_penerima = true;
                                this.selectedMutasi.status = newStatus;
                            }
                            this.showSimatToast('✅ Mutasi berhasil disetujui sebagai Pihak Penerima!', 'success');
                        } else {
                            this.showSimatToast('⚠️ Gagal menyetujui mutasi.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('approvePenerima error:', err);
                        window.location.reload();
                    });
                },

                approveAdmin(item) {
                    if (!item) return;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                    fetch('/mutasi-aset/' + item.id + '/approve-admin', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': token,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(d => {
                        if (d.success) {
                            item.persetujuan_admin = true;
                            const newStatus = item.persetujuan_penerima ? 'Disetujui Admin (Selesai)' : 'Disetujui Admin (Menunggu Penerima)';
                            item.status = newStatus;
                            if (this.selectedMutasi && this.selectedMutasi.id === item.id) {
                                this.selectedMutasi.persetujuan_admin = true;
                                this.selectedMutasi.status = newStatus;
                            }
                            this.showSimatToast('✅ Mutasi aset telah disetujui oleh Admin!', 'success');
                        } else {
                            this.showSimatToast('⚠️ Gagal menyetujui mutasi.', 'error');
                        }
                    })
                    .catch(err => {
                        console.error('approveAdmin error:', err);
                        window.location.reload();
                    });
                },

                showRejectModal: false,
                rejectTargetItem: null,
                rejectAlasan: '',

                openRejectModal(item) {
                    if (!item) return;
                    this.rejectTargetItem = item;
                    this.rejectAlasan = 'Lokasi penempatan belum siap / Kurang sesuai';
                    this.showRejectModal = true;
                },

                confirmRejectMutasi() {
                    if (!this.rejectTargetItem) return;
                    if (!this.rejectAlasan || !this.rejectAlasan.trim()) {
                        this.showSimatToast('⚠️ Silakan isi alasan penolakan terlebih dahulu.', 'warning');
                        return;
                    }
                    const item = this.rejectTargetItem;
                    const alasan = this.rejectAlasan.trim();
                    this.showRejectModal = false;

                    fetch('/mutasi-aset/' + item.id + '/reject', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ alasan_penolakan: alasan })
                    }).then(r => {
                        if (r.ok) {
                            item.status = 'Ditolak';
                            item.alasan_penolakan = alasan;
                            this.showSimatToast('🚫 Pengajuan mutasi berhasil ditolak.', 'info');
                        } else {
                            this.showSimatToast('⚠️ Gagal menolak pengajuan mutasi.', 'error');
                        }
                    }).catch(() => window.location.reload());
                },

                deleteMutasi(item) {
                    if (!item) return;
                    const bNomor = item.kode || 'BAMB';
                    const targetName = (item.nama || 'Pengajuan Mutasi') + ' (' + (item.kode_barang && item.kode_barang !== '-' ? item.kode_barang : bNomor) + ')';
                    this.askConfirmation({
                        title: '⚠️ Konfirmasi Hapus Data Mutasi',
                        message: 'Apakah Anda yakin ingin menghapus data transaksi pengajuan mutasi aset ini dari sistem? Seluruh rincian register NIBAR terkait juga akan terhapus secara permanen.',
                        itemName: targetName,
                        type: 'danger',
                        btnText: '🗑️ Ya, Hapus Mutasi Ini',
                        onConfirm: () => {
                            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                            fetch('/mutasi-aset/' + item.id, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': token,
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(d => {
                                if (d.success) {
                                    window.location.reload();
                                } else {
                                    this.showSimatToast('⚠️ Gagal menghapus: ' + (d.message || 'Terjadi kesalahan.'), 'error');
                                }
                            })
                            .catch(err => {
                                console.error('deleteMutasi error:', err);
                                window.location.reload();
                            });
                        }
                    });
                },

                openDetail(item) {
                    this.selectedMutasi = item;
                    this.showDetailModal = true;
                },


            };
        }
    </script>

    <div x-data="mutasiCatalog()" x-cloak>

        <!-- KONTEN UTAMA KATALOG MUTASI (DISEMBUNYIKAN SAAT DICETAK) -->
        <div class="no-print space-y-6">

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
                        Pencatatan perpindahan lokasi unit penempatan barang antar ruangan, pelacakan riwayat pergerakan aset, dan pencetakan Berita Acara Mutasi Barang (BAMB).
                    </p>
                </div>
                
                <a href="{{ route('mutasi.create') }}"
                    class="px-4 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-slate-950 font-bold text-xs shadow-lg shadow-rose-500/20 transition-all flex items-center space-x-2 shrink-0 active:scale-95">
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
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Disetujui / Selesai</span>
                        <span class="text-sm sm:text-base font-extrabold text-emerald-300" x-text="countSelesai + ' Pengajuan'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 text-lg">⏳</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Menunggu Persetujuan</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300" x-text="countMenunggu + ' Pengajuan'"></span>
                    </div>
                </div>

                <div class="bg-slate-950/60 border border-slate-800/80 rounded-2xl p-3.5 flex items-center space-x-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 text-lg">🏥</div>
                    <div>
                        <span class="text-[10px] uppercase tracking-wider font-semibold text-slate-400 block">Unit Terlibat</span>
                        <span class="text-sm sm:text-base font-extrabold text-purple-300" x-text="countUnits + ' Ruangan'"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter, Quick Tabs & Search Bar Full-Width -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
            <div class="flex flex-col gap-4">
                
                <!-- Quick Filter Status Mutasi Tabs -->
                <div class="flex items-center gap-2 flex-wrap text-xs bg-slate-950/60 p-2 rounded-2xl border border-slate-800/80">
                    <span class="text-[10.5px] font-extrabold text-slate-400 uppercase tracking-wider px-2.5 shrink-0 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        STATUS:
                    </span>

                    {{-- Button Semua Status --}}
                    <button type="button" @click="statusFilter = 'all'"
                        :class="statusFilter === 'all' 
                            ? 'bg-rose-500 text-white font-extrabold shadow-lg shadow-rose-500/25 border-rose-400 ring-2 ring-rose-500/30' 
                            : 'bg-slate-900/90 text-slate-400 hover:text-white hover:bg-slate-800 border-slate-800'"
                        class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-95">
                        <span>Semua Status</span>
                        <span class="px-1.5 py-0.2 text-[10px] font-mono font-black rounded-md"
                            :class="statusFilter === 'all' ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400'"
                            x-text="countAll"></span>
                    </button>

                    {{-- Button Selesai --}}
                    <button type="button" @click="statusFilter = 'selesai'"
                        :class="statusFilter === 'selesai' 
                            ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-lg shadow-emerald-500/25 border-emerald-400 ring-2 ring-emerald-500/30' 
                            : 'bg-slate-900/90 text-slate-400 hover:text-emerald-300 hover:bg-slate-800 border-slate-800'"
                        class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-95">
                        <span>✓ Selesai</span>
                        <span class="px-1.5 py-0.2 text-[10px] font-mono font-black rounded-md"
                            :class="statusFilter === 'selesai' ? 'bg-slate-950/40 text-slate-950' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20'"
                            x-text="countSelesai"></span>
                    </button>

                    {{-- Button Menunggu Admin --}}
                    <button type="button" @click="statusFilter = 'menunggu_admin'"
                        :class="statusFilter === 'menunggu_admin' 
                            ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-lg shadow-cyan-500/25 border-cyan-400 ring-2 ring-cyan-500/30' 
                            : 'bg-slate-900/90 text-slate-400 hover:text-cyan-300 hover:bg-slate-800 border-slate-800'"
                        class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-95">
                        <span>⏳ Menunggu Admin</span>
                        <span class="px-1.5 py-0.2 text-[10px] font-mono font-black rounded-md"
                            :class="statusFilter === 'menunggu_admin' ? 'bg-slate-950/40 text-slate-950' : 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20'"
                            x-text="countMenungguAdmin"></span>
                    </button>

                    {{-- Button Menunggu Penerima --}}
                    <button type="button" @click="statusFilter = 'menunggu_penerima'"
                        :class="statusFilter === 'menunggu_penerima' 
                            ? 'bg-amber-500 text-slate-950 font-extrabold shadow-lg shadow-amber-500/25 border-amber-400 ring-2 ring-amber-500/30' 
                            : 'bg-slate-900/90 text-slate-400 hover:text-amber-300 hover:bg-slate-800 border-slate-800'"
                        class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-95">
                        <span>⏳ Menunggu Penerima</span>
                        <span class="px-1.5 py-0.2 text-[10px] font-mono font-black rounded-md"
                            :class="statusFilter === 'menunggu_penerima' ? 'bg-slate-950/40 text-slate-950' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20'"
                            x-text="countMenungguPenerima"></span>
                    </button>

                    {{-- Button Ditolak --}}
                    <button type="button" @click="statusFilter = 'ditolak'"
                        :class="statusFilter === 'ditolak' 
                            ? 'bg-rose-500 text-white font-extrabold shadow-lg shadow-rose-500/25 border-rose-400 ring-2 ring-rose-500/30' 
                            : 'bg-slate-900/90 text-slate-400 hover:text-rose-300 hover:bg-slate-800 border-slate-800'"
                        class="px-3.5 py-1.5 rounded-xl border text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shrink-0 active:scale-95">
                        <span>✕ Ditolak</span>
                        <span class="px-1.5 py-0.2 text-[10px] font-mono font-black rounded-md"
                            :class="statusFilter === 'ditolak' ? 'bg-white/20 text-white' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20'"
                            x-text="countDitolak"></span>
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nomor BAMB / nama aset / NIBAR / ruangan asal / tujuan..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 transition-all">
                        <svg class="w-4 h-4 text-indigo-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <button type="button" x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</button>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                            Menampilkan <span class="text-indigo-400 font-bold" x-text="filteredMutasis.length"></span> dari <span class="text-white font-bold" x-text="mutasis.length"></span> Mutasi
                        </span>
                        <button type="button" @click="resetFilters()"
                            class="px-3.5 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                            🔄 Reset
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabel Mutasi Aset -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="overflow-x-auto rounded-2xl border border-slate-800/80 bg-slate-950/40">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shadow-sm shrink-0">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">No. BAMB</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Jenis</th>
                            <th class="px-4 py-3.5 text-left min-w-[240px] max-w-[280px] bg-slate-950">Nama Barang / ASTAP</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap min-w-[190px] bg-slate-950">Asal → Tujuan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap min-w-[220px] bg-slate-950">Status Persetujuan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap border-l border-slate-800 shrink-0 min-w-[210px] w-[210px]" style="position: sticky; right: 0; z-index: 20; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredMutasis" :key="item.id">
                            <tr class="group hover:bg-slate-800/40 transition-colors">
                                {{-- No --}}
                                <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>

                                {{-- No. BAMB --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg bg-cyan-950/60 border border-cyan-500/30 text-cyan-300 font-mono font-bold text-[11px] shadow-sm inline-block" x-text="item.kode"></span>
                                </td>

                                {{-- Jenis Mutasi — Badge Premium --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-extrabold border shadow-sm"
                                        :class="{
                                            'bg-blue-500/10 text-blue-300 border-blue-500/30':   item.jenis === 'Ajukan Mutasi' || item.jenis === 'Pemindahan',
                                            'bg-amber-500/10 text-amber-300 border-amber-500/30': item.jenis === 'Perbaikan',
                                            'bg-teal-500/10 text-teal-300 border-teal-500/30':   item.jenis === 'Minta Mutasi' || item.jenis === 'Minta_Mutasi',
                                            'bg-rose-500/10 text-rose-300 border-rose-500/30':     item.jenis === 'Pengembalian' || item.jenis === 'Penghapusan',
                                            'bg-slate-800 text-slate-400 border-slate-700':       !item.jenis
                                        }">
                                        <span x-text="(item.jenis === 'Ajukan Mutasi' || item.jenis === 'Pemindahan') ? '🔄' : item.jenis === 'Perbaikan' ? '🔧' : (item.jenis === 'Minta Mutasi' || item.jenis === 'Minta_Mutasi') ? '📥' : '↩️'"></span>
                                        <span x-text="item.jenis || '—'"></span>
                                    </span>
                                </td>

                                {{-- Nama Barang & NIBAR (Multi-item badge support) --}}
                                <td class="px-4 py-4 min-w-[240px] max-w-[280px]">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="font-bold text-white text-xs leading-snug break-words flex-1" x-text="item.nama"></p>
                                        <template x-if="item.item_count > 1">
                                            <span class="px-2 py-0.5 rounded-lg text-[9.5px] font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/30 whitespace-nowrap shrink-0" x-text="item.item_count + ' Barang'"></span>
                                        </template>
                                    </div>
                                    <div class="mt-1 flex items-center space-x-1.5 flex-wrap gap-y-1">
                                        <span class="text-[10px] text-slate-400 font-mono bg-slate-950 px-2 py-0.5 rounded border border-slate-800/80 inline-block max-w-full truncate" x-text="item.kode_barang" :title="item.kode_barang"></span>
                                        <template x-if="item.item_count > 1">
                                            <span class="text-[10px] text-indigo-400 font-semibold" x-text="'(+' + (item.item_count - 1) + ' NIBAR)'"></span>
                                        </template>
                                    </div>
                                </td>

                                {{-- Asal → Tujuan: Pill Card --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 shadow-inner">
                                        <span class="text-[11px] text-slate-300 font-medium" x-text="item.asal" :title="item.asal"></span>
                                        <div class="shrink-0 w-4 h-4 rounded-md bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center">
                                            <svg class="w-2.5 h-2.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </div>
                                        <span class="text-[11px] text-indigo-300 font-semibold" x-text="item.tujuan" :title="item.tujuan"></span>
                                    </div>
                                </td>

                                {{-- Status Persetujuan: Badge + Step Indicator --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap min-w-[220px]">
                                    <div class="flex flex-col items-center gap-2">
                                        {{-- Badge Status --}}
                                        <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none border shadow-sm select-none"
                                            :class="{
                                                'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': item.status === 'Disetujui Admin (Selesai)',
                                                'bg-cyan-500/15 text-cyan-300 border-cyan-500/30':         item.status === 'Disetujui 2 Pihak (Menunggu Admin)',
                                                'bg-amber-500/15 text-amber-300 border-amber-500/30':     item.status === 'Menunggu Persetujuan Penerima',
                                                'bg-rose-500/15 text-rose-300 border-rose-500/30':         item.status === 'Ditolak'
                                            }">
                                            <span x-text="item.status === 'Disetujui Admin (Selesai)' ? '✓ Selesai'
                                                         : item.status === 'Disetujui 2 Pihak (Menunggu Admin)' ? '⏳ Menunggu Admin'
                                                         : item.status === 'Menunggu Persetujuan Penerima' ? '⏳ Menunggu Penerima'
                                                         : item.status === 'Ditolak' ? '✕ Ditolak'
                                                         : item.status">
                                            </span>
                                        </span>

                                        {{-- Step Track --}}
                                        <div class="flex items-center gap-0">
                                            <div class="flex flex-col items-center gap-0.5">
                                                <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                                                    :class="item.persetujuan_pengirim ? 'bg-emerald-500 border-emerald-400 shadow shadow-emerald-500/40' : 'bg-slate-900 border-slate-700'">
                                                    <svg x-show="item.persetujuan_pengirim" class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                                <span class="text-[8px] font-semibold" :class="item.persetujuan_pengirim ? 'text-emerald-400' : 'text-slate-500'">Kirim</span>
                                            </div>
                                            <div class="w-4 h-0.5 mb-3 transition-all" :class="item.persetujuan_penerima ? 'bg-emerald-500' : 'bg-slate-700'"></div>
                                            <div class="flex flex-col items-center gap-0.5">
                                                <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                                                    :class="item.persetujuan_penerima ? 'bg-emerald-500 border-emerald-400 shadow shadow-emerald-500/40' : 'bg-slate-900 border-slate-700'">
                                                    <svg x-show="item.persetujuan_penerima" class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                                <span class="text-[8px] font-semibold" :class="item.persetujuan_penerima ? 'text-emerald-400' : 'text-slate-500'">Terima</span>
                                            </div>
                                            <div class="w-4 h-0.5 mb-3 transition-all" :class="item.persetujuan_admin ? 'bg-emerald-500' : 'bg-slate-700'"></div>
                                            <div class="flex flex-col items-center gap-0.5">
                                                <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                                                    :class="item.persetujuan_admin ? 'bg-emerald-500 border-emerald-400 shadow shadow-emerald-500/40' : 'bg-slate-900 border-slate-700'">
                                                    <svg x-show="item.persetujuan_admin" class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                                <span class="text-[8px] font-semibold" :class="item.persetujuan_admin ? 'text-emerald-400' : 'text-slate-500'">Admin</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom Aksi — FREEZE STICKY RIGHT (3 TOMBOL: DETAIL, UBAH, HAPUS) --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[210px] w-[210px]" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">

                                        {{-- 1. Tombol Detail --}}
                                        <button type="button" @click="openDetail(item)" title="Lihat Detail & BAMB Mutasi"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </button>

                                        {{-- 2. Tombol Ubah --}}
                                        <a :href="'/mutasi-aset/' + item.id + '/edit'" title="Ubah Data Pengajuan Mutasi"
                                            class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Ubah</span>
                                        </a>

                                        {{-- 3. Tombol Hapus --}}
                                        <button type="button" @click="deleteMutasi(item)" title="Hapus Data Mutasi"
                                            class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>

                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State Baris Penuh Tinggi --}}
                        <template x-if="filteredMutasis.length === 0">
                            <tr>
                                <td colspan="7" class="text-center align-middle py-28 text-slate-400">
                                    <div class="flex flex-col items-center justify-center space-y-2 py-4">
                                        <p class="text-sm font-semibold text-slate-300">Tidak ada data transaksi mutasi aset yang sesuai kriteria pencarian / filter.</p>
                                        <p class="text-xs text-slate-500">Coba sesuaikan kata kunci pencarian atau filter status Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- END KONTEN UTAMA MUTASI (no-print) -->


        <!-- MODAL DETAIL MUTASI -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 50;" @click.self="showDetailModal = false" x-cloak>
            <div class="border border-slate-800 rounded-3xl max-w-2xl w-full p-6 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto my-auto" style="background-color: #0f172a;">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2">
                        <span class="text-indigo-400 font-bold text-lg">🔄</span>
                        <h3 class="text-base font-extrabold text-white">Detail Berita Acara Mutasi</h3>
                    </div>
                    <button type="button" @click.stop="showDetailModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
                </div>
                
                <template x-if="selectedMutasi">
                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-slate-400 text-[10.5px] uppercase font-bold">Nomor BAMB:</span>
                                <p class="font-mono font-bold text-cyan-300 text-sm" x-text="selectedMutasi.kode"></p>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-400 text-[10.5px] uppercase font-bold">Jenis Mutasi:</span>
                                <p class="font-bold text-white" x-text="selectedMutasi.jenis"></p>
                            </div>
                        </div>

                        <!-- Daftar Rincian Barang yang Dimutasi (Multi-Item Table) -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400 text-[10.5px] font-bold uppercase tracking-wider">
                                    Daftar Barang yang Dimutasi (<span x-text="(selectedMutasi.items ? selectedMutasi.items.length : 1) + ' Unit'"></span>):
                                </span>
                            </div>
                            <div class="bg-slate-950 rounded-2xl border border-slate-800 overflow-hidden shadow-inner">
                                <table class="w-full text-left text-xs">
                                    <thead class="bg-slate-900/90 text-slate-400 text-[10px] uppercase font-bold border-b border-slate-800">
                                        <tr>
                                            <th class="px-3 py-2 text-center w-8">No</th>
                                            <th class="px-3 py-2">Nama Barang / Aset</th>
                                            <th class="px-3 py-2 font-mono">NIBAR</th>
                                            <th class="px-3 py-2 text-center whitespace-nowrap">Kondisi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/60">
                                        <template x-for="(it, idx) in (selectedMutasi.items && selectedMutasi.items.length > 0 ? selectedMutasi.items : [{no: 1, nama_barang: selectedMutasi.nama, nibar: selectedMutasi.kode_barang, kondisi: selectedMutasi.kondisi}])" :key="idx">
                                            <tr class="hover:bg-slate-900/40">
                                                <td class="px-3 py-2 text-center font-bold text-slate-500" x-text="idx + 1"></td>
                                                <td class="px-3 py-2 font-bold text-white" x-text="it.nama_barang"></td>
                                                <td class="px-3 py-2 font-mono text-cyan-400 text-[11px]" x-text="it.nibar"></td>
                                                <td class="px-3 py-2 text-center whitespace-nowrap">
                                                    <span class="px-2 py-0.5 rounded text-[9.5px] font-black border whitespace-nowrap inline-block"
                                                          :class="{
                                                              'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': it.kondisi === 'Baik',
                                                              'bg-amber-500/20 text-amber-300 border-amber-500/30': it.kondisi === 'Rusak Ringan' || it.kondisi === 'Kurang Baik',
                                                              'bg-rose-500/20 text-rose-300 border-rose-500/30': it.kondisi === 'Rusak Berat'
                                                          }" x-text="it.kondisi || 'Baik'"></span>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                            <div>
                                <span class="text-slate-400 text-[10.5px]">Ruangan Asal (Pengirim):</span>
                                <p class="font-semibold text-slate-300 mt-0.5" x-text="selectedMutasi.asal"></p>
                                <p class="text-[10px] text-slate-500 mt-0.5" x-text="'PJ: ' + (selectedMutasi.pemohon || '-')"></p>
                            </div>
                            <div>
                                <span class="text-indigo-400 text-[10.5px] font-semibold">Ruangan Tujuan (Penerima):</span>
                                <p class="font-semibold text-indigo-300 mt-0.5" x-text="selectedMutasi.tujuan"></p>
                                <p class="text-[10px] text-slate-500 mt-0.5" x-text="'PJ: ' + (selectedMutasi.penerima_pj || '-')"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10.5px]">Tanggal Pengajuan:</span>
                                <p class="font-semibold text-slate-300 mt-0.5" x-text="selectedMutasi.tgl"></p>
                            </div>
                            <div>
                                <span class="text-slate-400 text-[10.5px]">Status Otorisasi:</span>
                                <p class="font-semibold mt-0.5"
                                    :class="{
                                        'text-emerald-400': selectedMutasi.status === 'Disetujui Admin (Selesai)',
                                        'text-cyan-400':    selectedMutasi.status === 'Disetujui 2 Pihak (Menunggu Admin)',
                                        'text-amber-400':   selectedMutasi.status === 'Menunggu Persetujuan Penerima',
                                        'text-rose-400':    selectedMutasi.status === 'Ditolak'
                                    }"
                                    x-text="selectedMutasi.status">
                                </p>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800" x-show="selectedMutasi.keterangan">
                            <span class="text-slate-500 block mb-1 font-semibold text-[10px]">Alasan / Urgensi Mutasi:</span>
                            <p class="text-slate-300 leading-relaxed" x-text="selectedMutasi.keterangan"></p>
                        </div>

                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800" x-show="selectedMutasi.catatan_penerima">
                            <span class="text-slate-500 block mb-1 font-semibold text-[10px]">Catatan Tambahan:</span>
                            <p class="text-slate-300 leading-relaxed" x-text="selectedMutasi.catatan_penerima"></p>
                        </div>

                        <div class="p-3 bg-rose-950/30 rounded-xl border border-rose-500/30" x-show="selectedMutasi.alasan_penolakan">
                            <span class="text-rose-400 block mb-1 font-bold text-[10px]">Alasan Penolakan:</span>
                            <p class="text-rose-200 leading-relaxed" x-text="selectedMutasi.alasan_penolakan"></p>
                        </div>
                    </div>
                </template>

                <div class="pt-4 mt-4 border-t border-slate-800 flex items-center justify-between flex-wrap gap-2">
                    <div class="flex items-center gap-2 flex-wrap">
                        {{-- 1. Tombol Cetak BAMB → Link ke Halaman Berita Acara (BAMB Mutasi) --}}
                        <template x-if="selectedMutasi && canPrint(selectedMutasi)">
                            <a :href="selectedMutasi ? ('/berita-acara?tab=mutasi&id=' + selectedMutasi.id + '&returnTo=' + encodeURIComponent('/mutasi-aset?openDetail=' + selectedMutasi.id)) : '#'"
                                class="px-3.5 py-2 rounded-xl text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95 bg-purple-500 hover:bg-purple-400 text-slate-950 shadow-purple-500/20 no-underline">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h6z"/></svg>
                                <span>🖨️ Cetak / Edit BAMB</span>
                            </a>
                        </template>

                        {{-- 2. Tombol Setujui Pengirim (HANYA DITAMPILKAN PADA AKUN SUB ADMIN RUANGAN ASAL) --}}
                        <template x-if="canApprovePengirim(selectedMutasi)">
                            <button type="button" @click="approvePengirim(selectedMutasi)" class="px-3.5 py-2 rounded-xl bg-blue-500/15 text-blue-300 border border-blue-500/30 hover:bg-blue-500/25 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                                <span>✓ Setujui (Pengirim)</span>
                            </button>
                        </template>

                        {{-- 3. Tombol Setujui Penerima (HANYA DITAMPILKAN PADA AKUN SUB ADMIN RUANGAN TUJUAN) --}}
                        <template x-if="canApprovePenerima(selectedMutasi)">
                            <button type="button" @click="approvePenerima(selectedMutasi)" class="px-3.5 py-2 rounded-xl bg-teal-500/15 text-teal-300 border border-teal-500/30 hover:bg-teal-500/25 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                                <span>✓ Setujui (Penerima)</span>
                            </button>
                        </template>

                        {{-- 3. Tombol Setujui Admin (HANYA DITAMPILKAN PADA AKUN ADMIN & MASTER ADMIN - BISA LANGSUNG) --}}
                        <template x-if="canApproveAdmin(selectedMutasi)">
                            <button type="button" @click="approveAdmin(selectedMutasi)" class="px-3.5 py-2 rounded-xl bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 hover:bg-emerald-500/25 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                                <span>✓ Setujui (Admin)</span>
                            </button>
                        </template>

                        {{-- 4. Tombol Tolak Mutasi (Selalu tampil jika status belum ditolak & belum selesai) --}}
                        <template x-if="selectedMutasi && selectedMutasi.status !== 'Ditolak' && selectedMutasi.status !== 'Disetujui Admin (Selesai)'">
                            <button type="button" @click="openRejectModal(selectedMutasi)" class="px-3.5 py-2 rounded-xl bg-rose-500/15 text-rose-300 border border-rose-500/30 hover:bg-rose-500/25 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer shadow-sm active:scale-95">
                                <span>✕ Tolak Mutasi</span>
                            </button>
                        </template>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">Tutup</button>
                </div>
            </div>
        </div>

        <!-- MODAL INPUT ALASAN PENOLAKAN -->
        <div x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 10000;" @click.self="showRejectModal = false" x-cloak>
            <div class="border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" style="background-color: #0f172a;">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 border border-rose-500/30 text-rose-400 flex items-center justify-center font-bold text-sm">
                            ✕
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-white">Penolakan Mutasi Aset</h3>
                            <p class="text-[11px] text-slate-400 font-mono" x-text="rejectTargetItem ? rejectTargetItem.kode : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click.stop="showRejectModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    <p class="text-slate-300 font-semibold leading-relaxed">
                        Silakan masukkan alasan penolakan pengajuan Berita Acara Mutasi:
                        <span class="text-rose-300 font-bold block mt-1" x-text="rejectTargetItem ? rejectTargetItem.nama : ''"></span>
                    </p>

                    <div class="space-y-1.5 pt-1">
                        <label class="block text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Alasan Penolakan <span class="text-rose-400">*</span></label>
                        <textarea x-model="rejectAlasan" rows="3" placeholder="Contoh: Unit penerima belum siap / spesifikasi barang tidak sesuai..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 transition-all resize-none"></textarea>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2">
                    <button type="button" @click.stop="showRejectModal = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click.stop="confirmRejectMutasi()"
                        class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-white text-xs font-extrabold shadow-lg shadow-rose-500/25 transition-all cursor-pointer">
                        🚫 Konfirmasi Tolak Mutasi
                    </button>
                </div>
            </div>
        </div>



    </div>
</x-layout>
