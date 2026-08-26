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
                showPrintBastModal: false,
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

                get countSelesai() {
                    return this.mutasis.filter(m => m.persetujuan_admin || m.status === 'Disetujui Admin (Selesai)').length;
                },

                get countMenunggu() {
                    return this.mutasis.filter(m => !m.persetujuan_admin && m.status !== 'Ditolak').length;
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

                approvePenerima(item) {
                    if (!confirm('Setujui mutasi "' + item.nama + '" sebagai unit penerima?')) return;
                    fetch('/mutasi-aset/' + item.id + '/approve-penerima', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    }).then(r => r.json().then(d => {
                        if (d.success || r.ok) {
                            item.persetujuan_penerima = true;
                            item.status = 'Disetujui 2 Pihak (Menunggu Admin)';
                        } else {
                            alert('Gagal menyetujui mutasi.');
                        }
                    })).catch(() => window.location.reload());
                },

                approveAdmin(item) {
                    if (!confirm('Sahkan dan mutasikan aset "' + item.nama + '" secara final ke ' + item.tujuan + '?')) return;
                    fetch('/mutasi-aset/' + item.id + '/approve-admin', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    }).then(r => r.json().then(d => {
                        if (d.success || r.ok) {
                            item.persetujuan_admin = true;
                            item.status = 'Disetujui Admin (Selesai)';
                        } else {
                            alert('Gagal mengesahkan mutasi.');
                        }
                    })).catch(() => window.location.reload());
                },

                rejectMutasi(item) {
                    const alasan = prompt('Masukkan alasan penolakan mutasi aset "' + item.nama + '":', 'Lokasi penempatan belum siap / Kurang sesuai');
                    if (!alasan || !alasan.trim()) return;
                    fetch('/mutasi-aset/' + item.id + '/reject', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ alasan_penolakan: alasan.trim() })
                    }).then(r => {
                        if (r.ok) {
                            item.status = 'Ditolak';
                            item.alasan_penolakan = alasan.trim();
                        } else {
                            alert('Gagal menolak pengajuan.');
                        }
                    }).catch(() => window.location.reload());
                },

                openDetail(item) {
                    this.selectedMutasi = item;
                    this.showDetailModal = true;
                },

                openPrintBast(item) {
                    const tglObj = item.tgl_raw ? new Date(item.tgl_raw) : new Date();
                    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                    this.selectedMutasi = {
                        ...item,
                        bast_nomor: item.kode ? item.kode.replace('MTS-', '') + ' / BAMB / 430.10.7 / ' + (isNaN(tglObj.getTime()) ? '2026' : tglObj.getFullYear()) : '001 / BAMB / 430.10.7 / 2026',
                        hari: isNaN(tglObj.getTime()) ? 'Senin' : days[tglObj.getDay()],
                        tanggal_angka: isNaN(tglObj.getTime()) ? String(new Date().getDate()) : String(tglObj.getDate()),
                        bulan: isNaN(tglObj.getTime()) ? months[new Date().getMonth()] : months[tglObj.getMonth()],
                        tahun: isNaN(tglObj.getTime()) ? String(new Date().getFullYear()) : String(tglObj.getFullYear()),
                        pengurus_nama: 'ESTU PRATIKA SARI, SST',
                        pengurus_nip: '198805122011012005',
                        pengurus_jabatan: 'Pengurus Barang Aset RSUD',
                        pj_asal_nama: item.pemohon || 'Ka. Ruangan ' + item.asal,
                        pj_asal_nip: '198004152006041008',
                        pj_asal_jabatan: 'Kepala Ruangan ' + item.asal,
                        pj_tujuan_nama: item.penerima_pj || 'Ka. Ruangan ' + item.tujuan,
                        pj_tujuan_nip: '198410272009021003',
                        pj_tujuan_jabatan: 'Kepala Ruangan ' + item.tujuan,
                        signed: true
                    };
                    this.showPrintBastModal = true;
                },

                toggleSignMutasi(item) {
                    if (!item) return;
                    item.signed = !item.signed;
                }
            };
        }
    </script>

    <div x-data="mutasiCatalog()" x-cloak>

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
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 scrollbar-none text-xs">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Status:</span>
                    <button type="button" @click="statusFilter = 'all'"
                        :class="statusFilter === 'all' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        Semua Status
                    </button>
                    <button type="button" @click="statusFilter = 'selesai'"
                        :class="statusFilter === 'selesai' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        ✅ Selesai
                    </button>
                    <button type="button" @click="statusFilter = 'menunggu_admin'"
                        :class="statusFilter === 'menunggu_admin' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        ⏳ Menunggu Admin
                    </button>
                    <button type="button" @click="statusFilter = 'menunggu_penerima'"
                        :class="statusFilter === 'menunggu_penerima' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        ⏳ Menunggu Penerima
                    </button>
                    <button type="button" @click="statusFilter = 'ditolak'"
                        :class="statusFilter === 'ditolak' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                        class="px-3 py-1.5 rounded-xl transition-all shrink-0">
                        ❌ Ditolak
                    </button>
                </div>

                <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
                    <div class="relative flex-1 w-full">
                        <input type="text" x-model="searchQuery" placeholder="Cari nomor BAMB / nama aset / NIBAR / ruangan asal / tujuan..."
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

        <!-- Sub Admin Context Banner -->
        <template x-if="userRole === 'sub_admin'">
            <div class="mb-5 p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-xs font-semibold flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3 shadow-lg">
                <div class="flex items-center space-x-2.5">
                    <span class="text-lg">🏛️</span>
                    <div>
                        <span class="font-extrabold text-white block text-sm">Mode Akses Unit: <span x-text="userUnit || 'Sub Admin Ruangan'"></span></span>
                        <span class="text-slate-400 text-[11px]">Hanya menampilkan transaksi mutasi aset yang dikirim dari atau ditujukan ke unit Anda.</span>
                    </div>
                </div>
                <span class="text-[10.5px] font-mono font-bold px-3 py-1 rounded-xl bg-rose-500/20 text-rose-200 border border-rose-500/40 shrink-0">Sub Admin Restricted</span>
            </div>
        </template>

        <!-- Table Mutasi -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-2xl overflow-hidden">

            {{-- Table Header Bar --}}
            <div class="px-6 py-4 border-b border-slate-800/80 flex items-center justify-between" style="background: linear-gradient(135deg, #0f172a 60%, #1e0a0a 100%);">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center">
                        <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                    </div>
                    <div>
                        <p class="text-white font-bold text-sm">Daftar Pengajuan Mutasi Aset</p>
                        <p class="text-slate-500 text-[10px]" x-text="filteredMutasis.length + ' pengajuan ditemukan'"></p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-[10px] font-bold">
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400 animate-pulse"></span>
                    Live
                </span>
            </div>

            <div class="custom-scrollbar" style="max-height: 440px; overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                    <thead style="position: sticky; top: 0; z-index: 20;">
                        <tr style="background: linear-gradient(90deg, #020617 0%, #0d0814 50%, #020617 100%);">
                            <th class="px-4 py-3 text-center w-10 text-[9px] font-extrabold uppercase tracking-widest text-slate-500 border-b border-slate-800/80">No</th>
                            <th class="px-4 py-3 text-center text-[9px] font-extrabold uppercase tracking-widest text-rose-500/70 border-b border-slate-800/80 whitespace-nowrap">No. BAMB</th>
                            <th class="px-4 py-3 text-center text-[9px] font-extrabold uppercase tracking-widest text-slate-500 border-b border-slate-800/80">Jenis</th>
                            <th class="px-4 py-3 text-left text-[9px] font-extrabold uppercase tracking-widest text-slate-500 border-b border-slate-800/80 min-w-[200px]">Nama Barang</th>
                            <th class="px-4 py-3 text-center text-[9px] font-extrabold uppercase tracking-widest text-slate-500 border-b border-slate-800/80 whitespace-nowrap min-w-[190px]">Asal → Tujuan</th>
                            <th class="px-4 py-3 text-center text-[9px] font-extrabold uppercase tracking-widest text-slate-500 border-b border-slate-800/80 whitespace-nowrap min-w-[200px]">Status Persetujuan</th>
                            <th class="px-4 py-3 text-center text-[9px] font-extrabold uppercase tracking-widest text-slate-500 border-b border-slate-800/80 w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        <template x-for="(item, index) in filteredMutasis" :key="item.id">
                            <tr class="group transition-all duration-150 cursor-default" :class="index % 2 === 0 ? 'bg-transparent hover:bg-rose-950/10' : 'bg-slate-950/30 hover:bg-rose-950/10'">
                                {{-- No --}}
                                <td class="px-4 py-3 text-center font-bold text-slate-500 whitespace-nowrap" x-text="index + 1"></td>

                                {{-- No. BAMB --}}
                                <td class="px-4 py-3 text-center font-mono font-semibold text-rose-400 whitespace-nowrap text-[11px]" x-text="item.kode"></td>

                                {{-- Jenis Mutasi — Badge Premium --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[10px] font-extrabold border shadow-sm"
                                        :class="{
                                            'bg-blue-500/10 text-blue-300 border-blue-500/25 shadow-blue-500/10':     item.jenis === 'Pemindahan',
                                            'bg-amber-500/10 text-amber-300 border-amber-500/25 shadow-amber-500/10': item.jenis === 'Perbaikan',
                                            'bg-teal-500/10 text-teal-300 border-teal-500/25 shadow-teal-500/10':     item.jenis === 'Pengembalian',
                                            'bg-rose-500/10 text-rose-300 border-rose-500/25 shadow-rose-500/10':     item.jenis === 'Penghapusan',
                                            'bg-slate-800 text-slate-400 border-slate-700':                           !item.jenis
                                        }">
                                        <span x-text="item.jenis === 'Pemindahan' ? '🔄' : item.jenis === 'Perbaikan' ? '🔧' : item.jenis === 'Pengembalian' ? '↩️' : item.jenis === 'Penghapusan' ? '🗑️' : '•'"></span>
                                        <span x-text="item.jenis || '—'"></span>
                                    </span>
                                </td>

                                {{-- Nama Barang --}}
                                <td class="px-4 py-4">
                                    <p class="font-bold text-white text-[12px] leading-tight group-hover:text-rose-100 transition-colors" x-text="item.nama"></p>
                                    <p class="text-[10px] text-slate-500 font-mono mt-0.5" x-text="item.kode_barang"></p>
                                </td>

                                {{-- Asal → Tujuan: Pill Card --}}
                                <td class="px-4 py-4 text-center">
                                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl bg-slate-800/80 border border-slate-700/60">
                                        <span class="max-w-[75px] truncate text-[10.5px] text-slate-300 font-medium" x-text="item.asal" :title="item.asal"></span>
                                        <div class="shrink-0 w-5 h-5 rounded-lg bg-rose-500/15 border border-rose-500/30 flex items-center justify-center">
                                            <svg class="w-2.5 h-2.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                                        </div>
                                        <span class="max-w-[75px] truncate text-[10.5px] text-rose-300 font-semibold" x-text="item.tujuan" :title="item.tujuan"></span>
                                    </div>
                                </td>

                                {{-- Status Persetujuan: Badge + Step Indicator --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <div class="flex flex-col items-center gap-2">

                                        {{-- Badge Status --}}
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-extrabold border shadow-sm"
                                            :class="{
                                                'bg-emerald-500/10 text-emerald-300 border-emerald-500/25 shadow-emerald-500/10': item.status === 'Disetujui Admin (Selesai)',
                                                'bg-cyan-500/10 text-cyan-300 border-cyan-500/25 shadow-cyan-500/10':             item.status === 'Disetujui 2 Pihak (Menunggu Admin)',
                                                'bg-amber-500/10 text-amber-300 border-amber-500/25 shadow-amber-500/10':         item.status === 'Menunggu Persetujuan Penerima',
                                                'bg-rose-500/10 text-rose-300 border-rose-500/25':                                item.status === 'Ditolak'
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
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                                    :class="item.persetujuan_pengirim ? 'bg-emerald-500 border-emerald-400 shadow shadow-emerald-500/40' : 'bg-slate-900 border-slate-700'">
                                                    <svg x-show="item.persetujuan_pengirim" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                                <span class="text-[8px] font-semibold" :class="item.persetujuan_pengirim ? 'text-emerald-400' : 'text-slate-600'">Kirim</span>
                                            </div>
                                            <div class="w-5 h-0.5 mb-3.5 transition-all" :class="item.persetujuan_penerima ? 'bg-emerald-500' : 'bg-slate-700'"></div>
                                            <div class="flex flex-col items-center gap-0.5">
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                                    :class="item.persetujuan_penerima ? 'bg-emerald-500 border-emerald-400 shadow shadow-emerald-500/40' : 'bg-slate-900 border-slate-700'">
                                                    <svg x-show="item.persetujuan_penerima" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                                <span class="text-[8px] font-semibold" :class="item.persetujuan_penerima ? 'text-emerald-400' : 'text-slate-600'">Terima</span>
                                            </div>
                                            <div class="w-5 h-0.5 mb-3.5 transition-all" :class="item.persetujuan_admin ? 'bg-emerald-500' : 'bg-slate-700'"></div>
                                            <div class="flex flex-col items-center gap-0.5">
                                                <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                                                    :class="item.persetujuan_admin ? 'bg-emerald-500 border-emerald-400 shadow shadow-emerald-500/40' : 'bg-slate-900 border-slate-700'">
                                                    <svg x-show="item.persetujuan_admin" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                </div>
                                                <span class="text-[8px] font-semibold" :class="item.persetujuan_admin ? 'text-emerald-400' : 'text-slate-600'">Admin</span>
                                            </div>
                                        </div>

                                    </div>
                                </td>

                                {{-- Kolom Aksi: Icon Buttons Premium --}}
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">

                                        {{-- Detail --}}
                                        <button type="button" @click="openDetail(item)" title="Lihat Detail Mutasi"
                                            class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white border border-slate-700 hover:border-slate-500 flex items-center justify-center transition-all shadow-sm active:scale-90">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        </button>

                                        {{-- Terima & Setujui (Penerima) --}}
                                        <template x-if="!item.persetujuan_penerima && item.status !== 'Ditolak' && (userRole !== 'sub_admin' || (item.tujuan || '').toLowerCase().includes((userUnit || '').toLowerCase()))">
                                            <button type="button" @click="approvePenerima(item)" title="Terima & Setujui (Pihak Penerima)"
                                                class="w-8 h-8 rounded-xl bg-teal-500/15 hover:bg-teal-500/30 text-teal-300 hover:text-teal-200 border border-teal-500/30 hover:border-teal-400/50 flex items-center justify-center transition-all shadow-sm active:scale-90">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </template>

                                        {{-- Sahkan Admin --}}
                                        <template x-if="item.persetujuan_penerima && !item.persetujuan_admin && item.status !== 'Ditolak' && (userRole === 'admin' || userRole === 'master_admin')">
                                            <button type="button" @click="approveAdmin(item)" title="Sahkan & Verifikasi Akhir (Admin)"
                                                class="w-8 h-8 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/30 text-emerald-300 hover:text-emerald-200 border border-emerald-500/30 hover:border-emerald-400/50 flex items-center justify-center transition-all shadow-sm active:scale-90">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </button>
                                        </template>

                                        {{-- Tolak --}}
                                        <template x-if="item.status !== 'Disetujui Admin (Selesai)' && item.status !== 'Ditolak'">
                                            <button type="button" @click="rejectMutasi(item)" title="Tolak Pengajuan"
                                                class="w-8 h-8 rounded-xl bg-rose-500/10 hover:bg-rose-500/25 text-rose-400 hover:text-rose-300 border border-rose-500/25 hover:border-rose-400/50 flex items-center justify-center transition-all shadow-sm active:scale-90">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                                            </button>
                                        </template>

                                        {{-- Edit (Jika belum selesai & belum ditolak) --}}
                                        <template x-if="item.status !== 'Disetujui Admin (Selesai)' && item.status !== 'Ditolak'">
                                            <a :href="'/mutasi-aset/' + item.id + '/edit'" title="Ubah Data Pengajuan"
                                                class="w-8 h-8 rounded-xl bg-amber-500/10 hover:bg-amber-500/25 text-amber-400 hover:text-amber-300 border border-amber-500/25 hover:border-amber-400/50 flex items-center justify-center transition-all shadow-sm active:scale-90">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                        </template>

                                        {{-- Cetak BAMB --}}
                                        <template x-if="item.status === 'Disetujui Admin (Selesai)' || item.status === 'Disetujui 2 Pihak (Menunggu Admin)'">
                                            <button type="button" @click="openPrintBast(item)" title="Cetak Berita Acara Mutasi Barang (BAMB)"
                                                class="w-8 h-8 rounded-xl bg-purple-500/10 hover:bg-purple-500/25 text-purple-400 hover:text-purple-300 border border-purple-500/25 hover:border-purple-400/50 flex items-center justify-center transition-all shadow-sm active:scale-90">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            </button>
                                        </template>

                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <template x-if="filteredMutasis.length === 0">
                            <tr>
                                <td colspan="7" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-14 h-14 rounded-2xl bg-slate-800 border border-slate-700 flex items-center justify-center opacity-40">
                                            <svg class="w-7 h-7 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg>
                                        </div>
                                        <p class="text-slate-500 text-xs font-medium">Tidak ada data mutasi yang ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>


        <!-- MODAL DETAIL MUTASI -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4" x-cloak>
            <div @click.away="showDetailModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 mb-4">
                    <div class="flex items-center space-x-2">
                        <span class="text-rose-400 font-bold">🔄</span>
                        <h3 class="text-base font-bold text-white">Detail Pengajuan Mutasi</h3>
                    </div>
                    <button type="button" @click="showDetailModal = false" class="text-slate-500 hover:text-white text-xl font-bold">&times;</button>
                </div>
                
                <template x-if="selectedMutasi">
                    <div class="space-y-3.5 text-xs">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-slate-400 text-[10.5px]">Nomor BAMB:</span>
                                <p class="font-mono font-bold text-rose-400 text-sm" x-text="selectedMutasi.kode"></p>
                            </div>
                            <div class="text-right">
                                <span class="text-slate-400 text-[10.5px]">Jenis Mutasi:</span>
                                <p class="font-bold text-white" x-text="selectedMutasi.jenis"></p>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-950/80 rounded-2xl border border-slate-800/80">
                            <span class="text-slate-400 text-[10.5px] block mb-0.5">Nama Barang / Aset:</span>
                            <p class="font-bold text-white text-sm" x-text="selectedMutasi.nama"></p>
                            <div class="flex items-center gap-3 mt-1 text-[10px]">
                                <span class="font-mono text-rose-400" x-text="'NIBAR: ' + (selectedMutasi.kode_barang || '-')"></span>
                                <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                                <span class="text-slate-400" x-text="'Kondisi: ' + (selectedMutasi.kondisi || 'Baik')"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                            <div>
                                <span class="text-slate-400 text-[10.5px]">Ruangan Asal (Pengirim):</span>
                                <p class="font-semibold text-slate-300 mt-0.5" x-text="selectedMutasi.asal"></p>
                                <p class="text-[10px] text-slate-500 mt-0.5" x-text="'PJ: ' + (selectedMutasi.pemohon || '-')"></p>
                            </div>
                            <div>
                                <span class="text-rose-400 text-[10.5px] font-semibold">Ruangan Tujuan (Penerima):</span>
                                <p class="font-semibold text-rose-300 mt-0.5" x-text="selectedMutasi.tujuan"></p>
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

                <div class="pt-4 mt-4 border-t border-slate-800 flex justify-end">
                    <button type="button" @click="showDetailModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all">Tutup</button>
                </div>
            </div>
        </div>

        <!-- MODAL PRINTER BERITA ACARA MUTASI BARANG (BAMB) RESMI RSUD KOESNANDI -->
        <div x-show="showPrintBastModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-950/85 backdrop-blur-md p-4 overflow-y-auto" x-cloak>
            <div @click.away="showPrintBastModal = false" class="bg-slate-900 border border-slate-800 rounded-3xl max-w-4xl w-full p-6 sm:p-8 shadow-2xl space-y-6 max-h-[92vh] overflow-y-auto my-6">
                
                <!-- Action Header Modal Print -->
                <div class="flex items-center justify-between pb-4 border-b border-slate-800 print:hidden">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-2xl bg-purple-500/20 text-purple-300 border border-purple-500/30 flex items-center justify-center font-bold text-lg">
                            📄
                        </div>
                        <div>
                            <h3 class="text-base font-extrabold text-white">Pratinjau Berita Acara Mutasi Barang (BAMB)</h3>
                            <p class="text-xs text-slate-400">Dokumen resmi Berita Acara Pemindahan & Mutasi Aset Antar Ruangan</p>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2">
                        <button type="button" @click="toggleSignMutasi(selectedMutasi)"
                            class="px-3.5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg transition-all active:scale-95 flex items-center space-x-1"
                            title="Tanda Tangan Digital BSrE">
                            <span x-text="selectedMutasi?.signed ? '✅ Tertanda Digital' : '✍️ TTD BSrE'"></span>
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
                                    <p class="text-[10px] leading-tight">Website: rsudrkoesnandi.go.id, Email: rsu.koesnandi@gmail.com</p>
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
                            <h3 class="font-black text-xs sm:text-sm uppercase underline tracking-wider">BERITA ACARA MUTASI BARANG (BAMB)</h3>
                            <p class="text-[11px] font-semibold">Nomor : <span x-text="selectedMutasi.bast_nomor"></span></p>
                        </div>

                        <!-- PARAGRAF PEMBUKA -->
                        <p class="text-justify mb-2 leading-relaxed font-sans">
                            Pada hari ini <strong x-text="selectedMutasi.hari"></strong> tanggal <strong x-text="selectedMutasi.tanggal_angka"></strong> bulan <strong x-text="selectedMutasi.bulan"></strong> tahun <strong x-text="selectedMutasi.tahun"></strong>, yang bertanda tangan di bawah ini :
                        </p>

                        <!-- PIHAK PERTAMA (RUANGAN ASAL / PENGIRIM) -->
                        <div class="space-y-0.5 mb-2 ml-4 font-sans text-[10.5px]">
                            <div class="flex"><div class="w-36 font-medium">Nama (Pihak I - Asal)</div><div class="w-4">:</div><div class="flex-1 font-bold uppercase" x-text="selectedMutasi.pj_asal_nama"></div></div>
                            <div class="flex"><div class="w-36 font-medium">NIP</div><div class="w-4">:</div><div class="flex-1 font-mono" x-text="selectedMutasi.pj_asal_nip"></div></div>
                            <div class="flex"><div class="w-36 font-medium">Jabatan / Ruangan</div><div class="w-4">:</div><div class="flex-1 font-bold" x-text="selectedMutasi.pj_asal_jabatan"></div></div>
                        </div>

                        <!-- PIHAK KEDUA (RUANGAN TUJUAN) -->
                        <p class="mb-1 leading-relaxed font-sans">Menyerahkan mutasi barang aset kepada :</p>
                        <div class="space-y-0.5 mb-3 ml-4 font-sans text-[10.5px]">
                            <div class="flex"><div class="w-36 font-medium">Nama (Pihak II - Tujuan)</div><div class="w-4">:</div><div class="flex-1 font-bold uppercase" x-text="selectedMutasi.pj_tujuan_nama"></div></div>
                            <div class="flex"><div class="w-36 font-medium">NIP</div><div class="w-4">:</div><div class="flex-1 font-mono" x-text="selectedMutasi.pj_tujuan_nip"></div></div>
                            <div class="flex"><div class="w-36 font-medium">Jabatan / Ruangan</div><div class="w-4">:</div><div class="flex-1 font-bold uppercase" x-text="selectedMutasi.pj_tujuan_jabatan"></div></div>
                        </div>

                        <!-- TABEL RINCIAN MUTASI ASET -->
                        <div class="my-3">
                            <table class="w-full text-center border-collapse border border-black text-[10px] font-sans">
                                <thead>
                                    <tr class="bg-gray-200 font-bold border-b border-black">
                                        <th class="border border-black px-2 py-1.5 w-8">No</th>
                                        <th class="border border-black px-3 py-1.5 text-left">Nama Barang / Aset</th>
                                        <th class="border border-black px-3 py-1.5 font-mono">NIBAR</th>
                                        <th class="border border-black px-3 py-1.5 font-mono">Kode Rekening 108</th>
                                        <th class="border border-black px-2 py-1.5 w-12">Vol</th>
                                        <th class="border border-black px-2 py-1.5">Kondisi</th>
                                        <th class="border border-black px-3 py-1.5 text-left">Alasan / Jenis Mutasi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="border-b border-black">
                                        <td class="border border-black px-2 py-1.5">1</td>
                                        <td class="border border-black px-3 py-1.5 text-left font-bold" x-text="selectedMutasi.nama"></td>
                                        <td class="border border-black px-3 py-1.5 font-mono text-[9px]" x-text="selectedMutasi.kode_barang"></td>
                                        <td class="border border-black px-3 py-1.5 font-mono text-[9px]" x-text="selectedMutasi.kode_108"></td>
                                        <td class="border border-black px-2 py-1.5 font-bold">1 Unit</td>
                                        <td class="border border-black px-2 py-1.5 font-bold"
                                            :class="{'text-emerald-800': selectedMutasi.kondisi === 'Baik', 'text-amber-800': selectedMutasi.kondisi === 'Kurang Baik', 'text-rose-800': selectedMutasi.kondisi === 'Rusak Berat'}"
                                            x-text="selectedMutasi.kondisi"></td>
                                        <td class="border border-black px-3 py-1.5 text-left text-[9.5px]">
                                            <span class="font-bold uppercase" x-text="'[' + selectedMutasi.jenis + '] '"></span>
                                            <span x-text="selectedMutasi.keterangan"></span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- KALIMAT PENUTUP -->
                        <p class="text-justify mb-4 leading-relaxed font-sans">
                            Demikian Berita Acara Mutasi Barang (BAMB) ini dibuat dengan sebenar-benarnya untuk dipergunakan sebagai kelengkapan administrasi SIMAT-RK RSUD Dr. H. Koesnandi Bondowoso.
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
