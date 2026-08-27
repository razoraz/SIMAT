<x-layout title="Dashboard Sub Admin - SIMAT-RK">
    @section('page-title', 'Dashboard Sub Admin')
    @section('breadcrumb', 'Beranda / Sub Admin')

    @php
        $unitNama = $unit->nama ?? 'Unit Ruangan';
        $unitKode = $unit->kode_unit ?? ('UNIT-' . str_pad($unit->id ?? 1, 3, '0', STR_PAD_LEFT));
        $unitTipe = $unit->tipe ?? 'Unit Pelayanan Medis / Operasional';
        $unitKepala = $unit->kepala ?? Auth::user()->name;
        $unitNip = $unit->nip ?? Auth::user()->nip ?? '-';
        $unitTotalAset = $totalAsetCount ?? 0;
        $unitTotalNilai = $totalNilaiFormatted ?? 'Rp 0';
    @endphp

    <div x-data="{
        searchQuery: '',
        statusFilter: 'all',
        showDetailModal: false,
        selectedDistribusi: null,

        // Data Distribusi Khusus Unit Sub Admin Ini dari Database Backend
        distribusis: {{ Js::from($distribusisList ?? []) }},

        // Daftar Aset Ruangan yang Perlu Perhatian / Pemeliharaan dari Database Backend
        attentionAssets: {{ Js::from($attentionAssets ?? []) }},

        get filteredDistribusis() {
            const q = (this.searchQuery || '').toLowerCase();
            return this.distribusis.filter(d => {
                const matchSearch = (d.nama || '').toLowerCase().includes(q) || 
                                    (d.kode || '').toLowerCase().includes(q) || 
                                    (d.keterangan || '').toLowerCase().includes(q);
                const matchStatus = this.statusFilter === 'all' || d.status === this.statusFilter;
                return matchSearch && matchStatus;
            });
        },

        get countDraft() {
            return this.distribusis.filter(d => d.status === 'Draft').length;
        },

        get countMenunggu() {
            return this.distribusis.filter(d => d.status === 'Menunggu Konfirmasi' || d.status === 'Pending').length;
        },

        get countDalamPengiriman() {
            return this.distribusis.filter(d => d.status === 'Dalam Pengiriman' || d.status === 'Dikirim').length;
        },

        get countDiterima() {
            return this.distribusis.filter(d => d.status === 'Telah Diterima' || d.status === 'Diterima').length;
        },

        openDetail(item) {
            this.selectedDistribusi = item;
            this.showDetailModal = true;
        }
    }" x-cloak>

        <!-- 1. HEADER & IDENTITAS UNIT TERDAFTAR -->
        <div class="bg-gradient-to-r from-emerald-600/15 via-teal-950/40 to-slate-900 border border-emerald-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-8 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-56 h-56 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute top-0 right-1/4 w-32 h-32 bg-teal-500/10 rounded-full blur-2xl pointer-events-none"></div>

            <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6 relative z-10">
                <!-- Info Sub Admin & Unit -->
                <div class="space-y-3 max-w-3xl">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-xs font-bold tracking-wide">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>SUB ADMIN · PENANGGUNG JAWAB RUANGAN</span>
                        </span>
                        <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-700 text-xs font-mono font-semibold">
                            <span>{{ $unitKode }}</span>
                        </span>
                    </div>

                    <div>
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white tracking-tight flex flex-wrap items-center gap-3">
                            <span>🏥 {{ $unitNama }}</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-300 mt-1.5 leading-relaxed">
                            Penanggung Jawab: <span class="font-bold text-white">{{ $unitKepala }}</span> 
                            <span class="text-slate-400">(NIP. {{ $unitNip }})</span> · 
                            <span class="text-emerald-400 font-semibold">{{ $unitTipe }}</span>
                        </p>
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed">
                        Kelola barang inventaris ruangan Anda, pantau riwayat penerimaan barang baru, ajukan mutasi/pemeliharaan aset, dan cetak Lembar Kartu Inventaris Ruangan (KIR) resmi.
                    </p>
                </div>

                <!-- Action Buttons Khusus Sub Admin -->
                <div class="flex flex-wrap sm:flex-nowrap gap-2.5 shrink-0 w-full sm:w-auto">
                    <a href="{{ route('unit.index') }}" 
                        class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-700 text-slate-200 border border-slate-700 font-bold text-xs transition-all flex items-center justify-center space-x-2">
                        <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Lembar KIR Ruangan</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. RINGKASAN METRIK KHUSUS RUANGAN (4 STATS CARDS) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- 1. Total Aset Ruangan -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-emerald-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Aset di Ruangan Ini</span>
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $unitTotalAset }}</p>
                    <span class="text-xs font-bold text-emerald-400">Unit Barang</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                    <span>Tercatat di Dokumen KIR</span>
                </p>
            </div>

            <!-- 2. Kondisi Aset Ruangan -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-teal-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Kondisi Aset Ruangan</span>
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline justify-between">
                    <p class="text-2xl sm:text-3xl font-black text-white">{{ $kondisiBaik ?? 0 }} <span class="text-xs font-bold text-emerald-400">Baik</span></p>
                    <span class="text-xs font-extrabold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">{{ $totalRusak ?? 0 }} Rusak/Servis</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    {{ $kondisiKurangBaik ?? 0 }} Kurang Baik · {{ $kondisiRusakRingan ?? 0 }} Rusak Ringan · {{ $kondisiRusakBerat ?? 0 }} Rusak Berat
                </p>
            </div>

            <!-- 3. Valuasi Nilai Aset Ruangan -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-cyan-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Nilai Aset Ruangan</span>
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-1">
                    <p class="text-xl sm:text-2xl font-black text-white">{{ $unitTotalNilai }}</p>
                </div>
                <p class="text-[11px] text-slate-400 mt-2">
                    Estimasi Nilai Buku Inventaris Unit
                </p>
            </div>

            <!-- 4. Pengajuan Distribusi Aktif -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl relative overflow-hidden group hover:border-amber-500/40 transition-all">
                <div class="flex items-center justify-between text-slate-400 mb-2">
                    <span class="text-xs font-bold uppercase tracking-wider">Distribusi Diajukan</span>
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline space-x-2">
                    <p class="text-2xl sm:text-3xl font-black text-white" x-text="distribusis.length">0</p>
                    <span class="text-xs font-bold text-amber-400">Permohonan</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-2 flex items-center justify-between">
                    <span><strong class="text-slate-400" x-text="countDraft">0</strong> Draft · <strong class="text-amber-400" x-text="countMenunggu">0</strong> Menunggu · <strong class="text-emerald-400" x-text="countDiterima">0</strong> Diterima</span>
                </p>
            </div>
        </div>

        <!-- 3. KONTEN UTAMA: DUA KOLOM (KIRI: TABEL DISTRIBUSI, KANAN: STATUS RUANGAN & KIR) -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- KOLOM KIRI (2/3): DAFTAR PENGAJUAN DISTRIBUSI BARANG RUANGAN SAYA -->
            <div class="lg:col-span-2 bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl flex flex-col justify-between">
                <div class="flex-1 flex flex-col">
                    <!-- Header Section & Filter -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center space-x-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                <h3 class="text-base sm:text-lg font-extrabold text-white">Permohonan Distribusi Barang Saya</h3>
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                Riwayat pengajuan alokasi barang aset baru yang diajukan oleh unit <span class="text-slate-200 font-semibold">{{ $unitNama }}</span>
                            </p>
                        </div>

                        <a href="{{ route('distribusi.create') }}" 
                            class="whitespace-nowrap px-3 py-1.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 text-xs font-bold transition-all flex items-center space-x-1.5 w-fit">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span>Pengajuan Baru</span>
                        </a>
                    </div>

                    <!-- Filter Status Bar & Search Input -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 mb-4">
                        <!-- Filter Badge Buttons -->
                        <div class="flex flex-wrap items-center gap-1.5 text-xs">
                            <button type="button" @click="statusFilter = 'all'"
                                :class="statusFilter === 'all' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                class="px-2.5 py-1 rounded-lg transition-all">
                                Semua (<span x-text="distribusis.length"></span>)
                            </button>
                            <button type="button" @click="statusFilter = 'Draft'"
                                :class="statusFilter === 'Draft' ? 'bg-slate-700 text-white font-extrabold shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                class="px-2.5 py-1 rounded-lg transition-all flex items-center space-x-1">
                                <span>Draft</span>
                                <span class="px-1.5 py-0.2 rounded-full bg-slate-800 text-slate-300 text-[10px]" x-show="countDraft > 0" x-text="countDraft"></span>
                            </button>
                            <button type="button" @click="statusFilter = 'Menunggu Konfirmasi'"
                                :class="statusFilter === 'Menunggu Konfirmasi' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                class="px-2.5 py-1 rounded-lg transition-all flex items-center space-x-1">
                                <span>Menunggu</span>
                                <span class="px-1.5 py-0.2 rounded-full bg-cyan-500/20 text-cyan-300 text-[10px]" x-show="countMenunggu > 0" x-text="countMenunggu"></span>
                            </button>
                            <button type="button" @click="statusFilter = 'Dalam Pengiriman'"
                                :class="statusFilter === 'Dalam Pengiriman' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                class="px-2.5 py-1 rounded-lg transition-all flex items-center space-x-1">
                                <span>Dikirim</span>
                                <span class="px-1.5 py-0.2 rounded-full bg-amber-500/20 text-amber-300 text-[10px]" x-show="countDalamPengiriman > 0" x-text="countDalamPengiriman"></span>
                            </button>
                            <button type="button" @click="statusFilter = 'Telah Diterima'"
                                :class="statusFilter === 'Telah Diterima' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-sm' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                class="px-2.5 py-1 rounded-lg transition-all">
                                Diterima
                            </button>
                        </div>

                        <!-- Search Box -->
                        <div class="flex items-center space-x-0.5 min-w-[220px]">
                            <!-- Ikon di luar text box -->
                            <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>

                            <!-- Text box (padding kembali normal) -->
                            <input type="text" x-model="searchQuery" placeholder="Cari nama barang / no. usulan..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                        </div>
                    </div>

                    <!-- Tabel Permohonan Distribusi -->
                    <div class="flex-1 flex flex-col min-h-[300px] overflow-x-auto rounded-2xl border border-slate-800/80 bg-slate-950/40">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shrink-0">
                                <tr>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap w-10">No</th>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap">No. Pengajuan</th>
                                    <th class="px-3.5 py-3 text-left min-w-[200px]">Nama Barang / ASTAP</th>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap">Qty</th>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap">Tanggal</th>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap">Status</th>
                                    <th class="px-3.5 py-3 text-center whitespace-nowrap">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/80">
                                <template x-for="(item, index) in filteredDistribusis" :key="item.id">
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="px-3.5 py-3.5 text-center font-bold text-slate-500" x-text="index + 1"></td>
                                        <td class="px-3.5 py-3.5 text-center font-mono font-semibold text-emerald-400 whitespace-nowrap" x-text="item.kode"></td>
                                        <td class="px-3.5 py-3.5">
                                            <p class="font-bold text-white text-xs" x-text="item.nama"></p>
                                            <p class="text-[10px] text-slate-400 truncate max-w-xs mt-0.5" x-text="item.keterangan"></p>
                                        </td>
                                        <td class="px-3.5 py-3.5 text-center font-semibold text-slate-200 whitespace-nowrap" x-text="item.qty"></td>
                                        <td class="px-3.5 py-3.5 text-center font-mono text-slate-400 whitespace-nowrap text-[11px]" x-text="item.tgl"></td>
                                        <td class="px-3.5 py-3.5 text-center whitespace-nowrap">
                                            <template x-if="item.status === 'Draft'">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                                    📝 Draft Permohonan
                                                </span>
                                            </template>
                                            <template x-if="item.status === 'Menunggu Konfirmasi' || item.status === 'Pending' || item.status === 'Menunggu Verifikasi'">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                                                    ⏳ Menunggu Verifikasi
                                                </span>
                                            </template>
                                            <template x-if="item.status === 'Dalam Pengiriman' || item.status === 'Dikirim' || item.status === 'Disetujui Admin'">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                                    🚚 Dalam Pengiriman
                                                </span>
                                            </template>
                                            <template x-if="item.status === 'Telah Diterima' || item.status === 'Diterima'">
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                                    🟢 Telah Diterima (KIR)
                                                </span>
                                            </template>
                                        </td>
                                        <td class="px-3.5 py-3.5 text-center space-x-1 whitespace-nowrap">
                                            <button type="button" @click="openDetail(item)"
                                                class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold text-xs border border-slate-700 transition-all inline-flex items-center space-x-1 shadow-sm">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail</span>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>

                        <!-- Empty state jika kosong, mengisi sisa ruang secara fleksibel tepat di tengah -->
                        <div x-show="filteredDistribusis.length === 0" class="flex-1 flex flex-col items-center justify-center p-8 text-center text-slate-400">
                            <p class="text-sm font-semibold">Tidak ada data permohonan distribusi yang cocok.</p>
                            <p class="text-xs text-slate-500 mt-1">Coba ubah kata kunci pencarian atau filter status Anda.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
                    <span>Menampilkan <strong class="text-white" x-text="filteredDistribusis.length"></strong> dari <strong class="text-white" x-text="distribusis.length"></strong> usulan distribusi</span>
                    <a href="{{ route('distribusi.index') }}" class="text-emerald-400 hover:underline font-semibold">Buka Seluruh Riwayat Distribusi &rarr;</a>
                </div>
            </div>

            <!-- KOLOM KANAN (1/3): KONDISI RUANGAN & LEMBAR KIR -->
            <div class="space-y-6">
                <!-- 1. Card Lembar Kartu Inventaris Ruangan (KIR) -->
                <div class="bg-gradient-to-br from-slate-900 via-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-5 shadow-xl relative overflow-hidden">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">📋</span>
                            <h4 class="text-sm font-extrabold text-white">Lembar KIR Ruangan</h4>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-full">Tersinkronisasi</span>
                    </div>

                    <p class="text-xs text-slate-400 leading-relaxed mb-4">
                        Daftar lengkap inventaris fisik yang ditempatkan di <strong class="text-slate-200">{{ $unitNama }}</strong>. Anda dapat mencetak lembar resmi KIR ber-barcode untuk ditempel pada pintu ruangan.
                    </p>

                    <div class="bg-slate-950/80 border border-slate-800 rounded-2xl p-3.5 mb-4 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-300">
                            <span class="text-slate-500">Unit Ruangan:</span>
                            <span class="font-bold text-white">{{ $unitNama }}</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span class="text-slate-500">Total Item Terpasang:</span>
                            <span class="font-bold text-emerald-400">{{ $unitTotalAset }} Unit Barang</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span class="text-slate-500">Penanggung Jawab:</span>
                            <span class="font-semibold text-slate-200">{{ $unitKepala }}</span>
                        </div>
                    </div>

                    <a href="{{ route('unit.index') }}" 
                        class="w-full py-2.5 rounded-xl bg-emerald-500/15 hover:bg-emerald-500/25 text-emerald-300 border border-emerald-500/30 text-xs font-bold transition-all flex items-center justify-center space-x-2 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                        <span>Buka & Cetak Dokumen KIR</span>
                    </a>
                </div>

                <!-- 2. Card Aset Butuh Perhatian / Dalam Servis IPSRS -->
                <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center space-x-2">
                            <span class="text-lg">🛠️</span>
                            <h4 class="text-sm font-extrabold text-white">Aset Perlu Perhatian</h4>
                        </div>
                        <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 border border-amber-500/30 px-2 py-0.5 rounded-full"
                              x-text="attentionAssets.length + ' Item'"></span>
                    </div>

                    <p class="text-xs text-slate-400 mb-3">
                        Barang di ruangan Anda yang mengalami kendala atau membutuhkan servis berkala:
                    </p>

                    <div class="space-y-2.5">
                        <template x-for="item in attentionAssets.slice(0, 4)" :key="item.id">
                            <div class="p-3 rounded-2xl bg-slate-950 border border-slate-800 text-xs hover:border-slate-700 transition-all">
                                <div class="flex items-start justify-between gap-2">
                                    <p class="font-bold text-white text-xs" x-text="item.nama"></p>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0"
                                        :class="item.status === 'Rusak Ringan' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : (item.status === 'Rusak Berat' || item.status === 'Rusak' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30')"
                                        x-text="item.status">
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 mt-1" x-text="item.catatan"></p>
                                <div class="mt-2 flex items-center justify-between text-[10px] text-slate-500">
                                    <span class="font-mono text-emerald-400" x-text="item.kode"></span>
                                    <span x-text="'Lokasi: ' + item.lokasi"></span>
                                </div>
                            </div>
                        </template>

                        <template x-if="attentionAssets.length === 0">
                            <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 text-center text-xs text-slate-400">
                                <p class="text-emerald-400 font-bold">✅ Seluruh Aset Baik</p>
                                <p class="text-[11px] text-slate-500 mt-1">Tidak ada aset rusak yang tercatat di ruangan ini.</p>
                            </div>
                        </template>
                    </div>

                    <a href="{{ route('pemeliharaan.index') }}" 
                        class="mt-4 block text-center w-full py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all">
                        Lihat Seluruh Log Pemeliharaan &rarr;
                    </a>
                </div>
            </div>
        </div>


        <!-- 5. MODAL DETAIL DISTRIBUSI -->
        <div x-show="showDetailModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
            <div @click.away="showDetailModal = false"
                class="bg-slate-900 border border-slate-800 rounded-3xl max-w-xl w-full p-6 shadow-2xl relative overflow-hidden"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                
                <template x-if="selectedDistribusi">
                    <div>
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between border-b border-slate-800 pb-4 mb-4">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-0.5 rounded font-mono text-xs font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30" x-text="selectedDistribusi.kode"></span>
                                    <span class="text-xs text-slate-400 font-mono" x-text="selectedDistribusi.tgl"></span>
                                </div>
                                <h3 class="text-lg font-black text-white mt-1" x-text="selectedDistribusi.nama"></h3>
                            </div>
                            <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Modal Body -->
                        <div class="space-y-4 text-xs">
                            <!-- Info Ruangan & Status -->
                            <div class="grid grid-cols-2 gap-3 bg-slate-950 p-3.5 rounded-2xl border border-slate-800">
                                <div>
                                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Ruangan Pemohon</span>
                                    <span class="text-white font-bold" x-text="selectedDistribusi.ruangan"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Status Verifikasi</span>
                                    <span class="font-bold text-emerald-400" x-text="selectedDistribusi.status"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Pengaju / PJ</span>
                                    <span class="text-slate-200" x-text="selectedDistribusi.pengaju"></span>
                                </div>
                                <div>
                                    <span class="text-slate-500 block text-[10px] uppercase font-bold">Total Volume</span>
                                    <span class="text-slate-200 font-bold" x-text="selectedDistribusi.qty"></span>
                                </div>
                            </div>

                            <!-- Catatan Keperluan -->
                            <div>
                                <span class="text-slate-400 font-bold block mb-1">Catatan / Alasan Permohonan:</span>
                                <p class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-slate-300 italic" x-text="selectedDistribusi.keterangan"></p>
                            </div>

                            <!-- Rincian Item Barang -->
                            <div>
                                <span class="text-slate-400 font-bold block mb-1.5">Rincian Barang yang Diminta:</span>
                                <div class="space-y-1.5">
                                    <template x-for="(item, idx) in selectedDistribusi.items" :key="idx">
                                        <div class="p-2.5 bg-slate-950 rounded-xl border border-slate-800/80 flex items-center justify-between">
                                            <div>
                                                <p class="font-bold text-white" x-text="item.nama"></p>
                                                <p class="text-[11px] text-slate-400 font-mono" x-text="'Merk: ' + item.merk"></p>
                                            </div>
                                            <div class="text-right">
                                                <span class="font-bold text-emerald-400" x-text="item.qty"></span>
                                                <span class="block text-[10px] text-slate-400" x-text="item.kondisi"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Footer -->
                        <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-end space-x-2">
                            <button type="button" @click="showDetailModal = false"
                                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all">
                                Tutup
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>

    </div>
</x-layout>
