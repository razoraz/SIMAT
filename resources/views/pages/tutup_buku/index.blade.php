<x-layout title="Tutup Buku BMD - SIMAT-RK" :fullWidth="true" :noSidebar="true">
    @section('page-title', 'Tutup Buku BMD')
    @section('breadcrumb', 'Master Utama / Tutup Buku BMD')

    <script>
        window.dbMitraKemitraans = @json($dbMitraKemitraans ?? []);
    </script>

    <!-- 1. Script Ekspor Multi-Sheet Excel, Logika Alpine.js & Reklasifikasi Modal -->
    @include('pages.astap.index_partials.scripts')

    <!-- Kontainer Alpine Khusus Halaman Tutup Buku (Mewarisi Seluruh Fungsi Reklasifikasi & Detail ASTAP) -->
    <div x-data="tutupBukuApp()" x-init="initTutupBuku()" x-cloak class="space-y-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

        <!-- ========================================================================= -->
        <!-- 1. HEADER BANNER & IDENTITAS TUTUP BUKU                                   -->
        <!-- ========================================================================= -->
        <div class="relative bg-gradient-to-br from-slate-900 via-slate-950 to-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl overflow-hidden">
            <!-- Glow Accents Ambient -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
                <!-- Sisi Kiri: Judul & Keterangan Regulasi -->
                <div class="space-y-3">
                    <div class="flex flex-wrap items-center gap-2.5">
                        <a href="{{ route('astap.index') }}" 
                           class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700/80 text-xs font-bold transition-all shadow-sm active:scale-95 group">
                            <svg class="w-4 h-4 text-cyan-400 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            <span>Kembali ke ASTAP</span>
                        </a>

                        <span class="px-3 py-1 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-300 text-xs font-black tracking-wider uppercase flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                            T.A. {{ $currentYear }} (Aktif)
                        </span>

                        <span class="px-3 py-1 rounded-xl bg-indigo-500/10 border border-indigo-500/30 text-indigo-300 text-xs font-black tracking-wider uppercase">
                            Triwulan {{ ['I', 'II', 'III', 'IV'][$actualQuarter - 1] ?? 'I' }} Berjalan
                        </span>

                        <span class="px-3 py-1 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 text-xs font-black tracking-wider uppercase flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                            </svg>
                            Integritas Neraca Terjaga
                        </span>
                    </div>

                    <div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-3">
                            <span class="p-2 rounded-2xl bg-gradient-to-tr from-cyan-600 to-indigo-600 shadow-lg shadow-cyan-500/20 text-white inline-flex">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                            </span>
                            <span>Manajemen Tutup Buku BMD</span>
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-400 mt-1 max-w-3xl leading-relaxed">
                            Penguncian periode akuntansi aset tetap, rekonsiliasi berkala triwulanan &amp; konsolidasi year-end closing LKPD BPK RI.
                            Triwulan yang telah terlewati otomatis terkunci dan data diarahkan ke konsolidasi tahunan.
                        </p>
                    </div>
                </div>

                <!-- Sisi Kanan: Pemilih Tahun & Ringkasan Cepat -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 shrink-0">
                    <form method="GET" action="{{ route('tutup_buku.index') }}" class="flex items-center gap-2 bg-slate-950/70 p-1.5 rounded-2xl border border-slate-800">
                        <label for="filter-tahun" class="text-xs font-bold text-slate-400 pl-3">Tahun:</label>
                        <select id="filter-tahun" name="tahun" onchange="this.form.submit()"
                                class="bg-slate-900 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs font-bold text-white focus:outline-none focus:border-cyan-500 cursor-pointer">
                            @foreach($distinctYears as $yr)
                                <option value="{{ $yr }}" {{ $currentYear === $yr ? 'selected' : '' }}>
                                    T.A. {{ $yr }} {{ $yr === $actualYear ? '(Tahun Berjalan)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 2. TAB NAVIGASI: TUTUP BUKU TRIWULAN VS TUTUP BUKU TAHUNAN               -->
        <!-- ========================================================================= -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div class="inline-flex p-1.5 rounded-2xl bg-slate-900/90 border border-slate-800 shadow-inner">
                <button type="button" 
                        @click="activeTab = 'triwulan'"
                        :class="activeTab === 'triwulan' ? 'bg-gradient-to-r from-cyan-600 to-indigo-600 text-white shadow-lg shadow-cyan-500/20' : 'text-slate-400 hover:text-slate-200'"
                        class="px-5 py-2.5 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span>Tutup Buku Triwulan (T.A. {{ $currentYear }})</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold"
                          :class="activeTab === 'triwulan' ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-300'">
                        4 Triwulan
                    </span>
                </button>

                <button type="button" 
                        @click="activeTab = 'tahunan'"
                        :class="activeTab === 'tahunan' ? 'bg-gradient-to-r from-cyan-600 to-indigo-600 text-white shadow-lg shadow-cyan-500/20' : 'text-slate-400 hover:text-slate-200'"
                        class="px-5 py-2.5 rounded-xl text-xs font-black transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span>Tutup Buku Tahunan (Konsolidasi &amp; Arsip)</span>
                    <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold"
                          :class="activeTab === 'tahunan' ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-300'">
                        {{ count($tahunanData) }} Tahun
                    </span>
                </button>
            </div>

            <!-- Sisi Kanan: Filter / Pemilih Tahun Interaktif (Bespoke Dark Dashboard) -->
            <div class="flex flex-wrap items-center gap-2.5">
                <!-- Mode Triwulan: Pemilih Tahun Aktif Triwulan -->
                <div x-show="activeTab === 'triwulan'" class="flex items-center gap-2 bg-slate-900/90 border border-slate-800 rounded-2xl p-1.5 shadow-inner">
                    <label for="filter-tahun-tab" class="text-xs font-bold text-slate-400 pl-2.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>T.A. Triwulan:</span>
                    </label>
                    <select id="filter-tahun-tab" 
                            onchange="window.location.href = '{{ route('tutup_buku.index') }}?tahun=' + this.value"
                            class="bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs font-black text-cyan-300 focus:outline-none focus:border-cyan-500 cursor-pointer hover:border-cyan-500/50 transition-colors">
                        @foreach($distinctYears as $yr)
                            <option value="{{ $yr }}" {{ $currentYear === $yr ? 'selected' : '' }}>
                                T.A. {{ $yr }} {{ $yr === $actualYear ? '(Tahun Berjalan)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Mode Tahunan: Filter Kartu Tahun Alpine (Instant Client-side) -->
                <div x-show="activeTab === 'tahunan'" class="flex items-center gap-2 bg-slate-900/90 border border-slate-800 rounded-2xl p-1.5 shadow-inner">
                    <label for="filter-tahun-tahunan" class="text-xs font-bold text-slate-400 pl-2.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                        </svg>
                        <span>Filter Tahun:</span>
                    </label>
                    <select id="filter-tahun-tahunan" 
                            x-model="filterTahunTahunan"
                            class="bg-slate-950 border border-slate-700/80 rounded-xl px-3 py-1.5 text-xs font-black text-indigo-300 focus:outline-none focus:border-indigo-500 cursor-pointer hover:border-indigo-500/50 transition-colors">
                        <option value="all">Semua Tahun ({{ count($tahunanData) }} Tahun)</option>
                        @foreach($tahunanData as $yrData)
                            <option value="{{ $yrData['tahun'] }}">T.A. {{ $yrData['tahun'] }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Petunjuk Singkat -->
                <div class="text-xs text-slate-400 hidden lg:flex items-center gap-2 pl-2 border-l border-slate-800">
                    <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                    <span>Klik kartu untuk buka <strong>Tabel Aset</strong></span>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 3. KONTEN TAB 1: KARTU TUTUP BUKU TRIWULAN (T.A. AKTIF)                   -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'triwulan'" x-transition:enter="transition ease-out duration-200 opacity-0" x-transition:enter-end="opacity-100" class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach($triwulanData as $tw)
                    <div class="relative bg-slate-900/90 border rounded-3xl p-5 shadow-xl transition-all duration-200 hover:-translate-y-1 flex flex-col justify-between overflow-hidden group
                        {{ $tw['is_locked'] ? 'border-slate-800 hover:border-emerald-500/40' : ($tw['is_active'] ? 'border-cyan-500/50 shadow-cyan-500/10 hover:border-cyan-400' : 'border-slate-800 opacity-80') }}"
                        :class="{ 'ring-2 ring-cyan-500 border-cyan-500': selectedPeriod?.type === 'triwulan' && selectedPeriod?.tw === {{ $tw['triwulan'] }} }">

                        <!-- Ambient Glow Pada Card Aktif -->
                        @if($tw['is_active'])
                            <div class="absolute -top-12 -right-12 w-32 h-32 bg-cyan-500/10 rounded-full blur-2xl"></div>
                        @endif

                        <div class="space-y-4">
                            <!-- Card Header: Badge & Status Lock -->
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="px-2.5 py-1 rounded-xl text-xs font-black uppercase tracking-wider
                                        {{ $tw['is_locked'] ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : ($tw['is_active'] ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40' : 'bg-slate-800 text-slate-400') }}">
                                        {{ $tw['badge'] }}
                                    </span>
                                    <h3 class="text-lg font-black text-white mt-1.5">{{ $tw['nama'] }}</h3>
                                    <p class="text-[11px] text-slate-400 font-medium">{{ $tw['rentang'] }}</p>
                                </div>

                                <div>
                                    @if($tw['is_locked'])
                                        <div class="w-9 h-9 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center shadow-inner"
                                             title="Ditutup Buku Otomatis (Triwulan Berlalu)">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                        </div>
                                    @elseif($tw['is_active'])
                                        <div class="w-9 h-9 rounded-2xl bg-cyan-500/15 border border-cyan-500/40 text-cyan-300 flex items-center justify-center shadow-inner animate-pulse"
                                             title="Triwulan Sedang Berjalan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-9 h-9 rounded-2xl bg-slate-800/80 border border-slate-700/60 text-slate-500 flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Status Box -->
                            <div class="p-3 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="text-slate-400">Status Buku:</span>
                                    @if($tw['is_locked'])
                                        <span class="font-bold text-emerald-400 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            Ditutup Buku
                                        </span>
                                    @elseif($tw['is_active'])
                                        <span class="font-bold text-cyan-300 flex items-center gap-1">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-ping"></span>
                                            Sedang Berjalan
                                        </span>
                                    @else
                                        <span class="font-bold text-slate-500">Belum Berjalan</span>
                                    @endif
                                </div>

                                <div class="flex items-center justify-between text-[10px] text-slate-500 pt-1 border-t border-slate-800/60">
                                    <span>Keterangan:</span>
                                    <span class="text-slate-400 font-mono truncate max-w-[150px]">
                                        @if($tw['is_locked'])
                                            Otomatis (Bulan Lewat)
                                        @elseif($tw['is_active'])
                                            Terbuka Transaksi
                                        @else
                                            Menunggu Periode
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Statistik Barang & Nilai -->
                            <div class="space-y-2 pt-1">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-xs text-slate-400 font-medium">Jumlah Aset:</span>
                                    <span class="text-sm font-extrabold text-white">{{ number_format($tw['total_item'], 0, ',', '.') }} Barang</span>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-xs text-slate-400 font-medium">Volume Fisik:</span>
                                    <span class="text-xs font-bold text-slate-300 font-mono">{{ number_format($tw['total_volume'], 0, ',', '.') }} Unit</span>
                                </div>
                                <div class="flex items-baseline justify-between pt-1 border-t border-slate-800/80">
                                    <span class="text-xs text-slate-400 font-medium">Nilai Realisasi:</span>
                                    <span class="text-sm font-black text-cyan-400 font-mono">{{ $tw['total_nominal_formatted'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi: Pilih Periode & Buka Rincian -->
                        <div class="pt-5 mt-4 border-t border-slate-800/60">
                            <button type="button"
                                    @click="selectPeriod({ type: 'triwulan', tw: {{ $tw['triwulan'] }}, year: {{ $currentYear }}, label: '{{ $tw['nama'] }} T.A. {{ $currentYear }}', isLocked: {{ $tw['is_locked'] ? 'true' : 'false' }}, rentang: '{{ $tw['rentang'] }}' })"
                                    class="w-full py-2.5 px-4 rounded-xl font-extrabold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95 shadow-md
                                    {{ $tw['is_locked'] ? 'bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700' : 'bg-gradient-to-r from-cyan-600 to-indigo-600 hover:from-cyan-500 hover:to-indigo-500 text-white shadow-cyan-500/20' }}">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Lihat Aset &amp; Reklas</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 4. KONTEN TAB 2: KARTU TUTUP BUKU TAHUNAN (ARSIP & KONSOLIDASI)          -->
        <!-- ========================================================================= -->
        <div x-show="activeTab === 'tahunan'" x-transition:enter="transition ease-out duration-200 opacity-0" x-transition:enter-end="opacity-100" class="space-y-6">
            
            <!-- Quick Year Filter Pills (Bespoke Filter Bar untuk Tahunan) -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-slate-900/60 p-3 rounded-2xl border border-slate-800">
                <div class="flex items-center gap-2 text-xs">
                    <span class="w-2 h-2 rounded-full bg-indigo-400"></span>
                    <span class="font-bold text-slate-300">Pilih Tahun Arsip:</span>
                    <span class="text-[11px] text-indigo-300 font-mono bg-indigo-500/10 border border-indigo-500/20 px-2 py-0.5 rounded-lg" 
                          x-text="filterTahunTahunan === 'all' ? 'Menampilkan Seluruh Tahun ({{ count($tahunanData) }})' : ('Tahun Anggaran ' + filterTahunTahunan)"></span>
                </div>
                
                <div class="flex items-center gap-1.5 overflow-x-auto pb-1 sm:pb-0 max-w-full">
                    <button type="button" 
                            @click="filterTahunTahunan = 'all'"
                            :class="filterTahunTahunan === 'all' ? 'bg-indigo-600 text-white font-extrabold shadow-md shadow-indigo-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                            class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer shrink-0">
                        Semua ({{ count($tahunanData) }})
                    </button>
                    @foreach($tahunanData as $yrData)
                        <button type="button" 
                                @click="filterTahunTahunan = '{{ $yrData['tahun'] }}'"
                                :class="filterTahunTahunan == '{{ $yrData['tahun'] }}' ? 'bg-indigo-600 text-white font-extrabold shadow-md shadow-indigo-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800'"
                                class="px-3 py-1.5 rounded-xl text-xs transition-all cursor-pointer shrink-0 font-mono">
                            {{ $yrData['tahun'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($tahunanData as $yrData)
                    <div x-show="filterTahunTahunan === 'all' || filterTahunTahunan == '{{ $yrData['tahun'] }}'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         class="relative bg-slate-900/90 border border-slate-800 rounded-3xl p-6 shadow-xl transition-all duration-200 hover:-translate-y-1 flex flex-col justify-between overflow-hidden group
                        {{ $yrData['is_past_year'] ? 'hover:border-indigo-500/40' : 'border-cyan-500/40 ring-1 ring-cyan-500/30' }}"
                        :class="{ 'ring-2 ring-indigo-500 border-indigo-500': selectedPeriod?.type === 'tahunan' && selectedPeriod?.year === {{ $yrData['tahun'] }} }">

                        <div class="space-y-4">
                            <!-- Header Tahun -->
                            <div class="flex items-start justify-between gap-3">
                                <div>
                                    <span class="px-3 py-1 rounded-xl text-xs font-black uppercase tracking-wider
                                        {{ $yrData['is_past_year'] ? 'bg-indigo-500/15 text-indigo-300 border border-indigo-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40' }}">
                                        T.A. {{ $yrData['tahun'] }}
                                    </span>
                                    <h3 class="text-xl font-black text-white mt-2">{{ $yrData['nama'] }}</h3>
                                    <p class="text-[11px] text-slate-400 font-medium">{{ $yrData['keterangan'] }}</p>
                                </div>

                                <div class="w-10 h-10 rounded-2xl {{ $yrData['is_past_year'] ? 'bg-indigo-500/10 border border-indigo-500/30 text-indigo-400' : 'bg-cyan-500/10 border border-cyan-500/30 text-cyan-400' }} flex items-center justify-center shadow-inner">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Status Box Tahunan -->
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-400">Status Pembukuan:</span>
                                    <span class="font-extrabold {{ $yrData['is_past_year'] ? 'text-indigo-400' : 'text-cyan-300' }}">
                                        {{ $yrData['status_label'] }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-500 pt-1.5 border-t border-slate-800/60">
                                    <span>Dasar Penutupan:</span>
                                    <span class="text-slate-300 font-mono text-[10px]">{{ $yrData['nomor_bar'] }}</span>
                                </div>
                            </div>

                            <!-- Statistik Tahunan -->
                            <div class="space-y-2 pt-1">
                                <div class="flex items-baseline justify-between">
                                    <span class="text-xs text-slate-400 font-medium">Total Aset Tercatat:</span>
                                    <span class="text-sm font-extrabold text-white">{{ number_format($yrData['total_item'], 0, ',', '.') }} Barang</span>
                                </div>
                                <div class="flex items-baseline justify-between">
                                    <span class="text-xs text-slate-400 font-medium">Akumulasi Volume:</span>
                                    <span class="text-xs font-bold text-slate-300 font-mono">{{ number_format($yrData['total_volume'], 0, ',', '.') }} Unit</span>
                                </div>
                                <div class="flex items-baseline justify-between pt-1 border-t border-slate-800/80">
                                    <span class="text-xs text-slate-400 font-medium">Total Saldo Aset:</span>
                                    <span class="text-base font-black text-cyan-400 font-mono">{{ $yrData['total_nominal_formatted'] }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Buka Aset Tahunan -->
                        <div class="pt-5 mt-4 border-t border-slate-800/60">
                            <button type="button"
                                    @click="selectPeriod({ type: 'tahunan', year: {{ $yrData['tahun'] }}, label: 'Konsolidasi Tutup Buku T.A. {{ $yrData['tahun'] }}', isLocked: {{ $yrData['is_locked'] ? 'true' : 'false' }}, rentang: 'Audit 31 Desember' })"
                                    class="w-full py-2.5 px-4 rounded-xl font-extrabold text-xs transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95 shadow-md bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                <span>Lihat Aset &amp; Reklas Tahunan</span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 5. PANEL TABEL RINCIAN ASET PERIODE TERPILIH (INSPECTION & REKLAS)        -->
        <!-- ========================================================================= -->
        <div id="panel-tabel-aset" class="space-y-5 pt-4">
            
            <!-- Banner Header Periode Terpilih -->
            <div class="bg-gradient-to-r from-slate-900 via-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                            Inspeksi Data Periode
                        </span>
                        <template x-if="selectedPeriod?.isLocked">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center gap-1">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Periode Terkunci (Tutup Buku)
                            </span>
                        </template>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2" x-text="selectedPeriod ? ('📋 Daftar Aset: ' + selectedPeriod.label) : '📋 Pilih Periode Triwulan / Tahunan'"></h2>
                    <p class="text-xs text-slate-400" x-text="selectedPeriod ? ('Rentang waktu: ' + selectedPeriod.rentang + ' — Seluruh aset yang tercatat pada periode buku ini.') : 'Silakan klik salah satu kartu triwulan atau tahun di atas untuk menampilkan rincian tabel aset.'"></p>
                </div>

                <!-- Ringkasan Cepat Periode -->
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2.5 rounded-2xl bg-slate-950/80 border border-slate-800 text-right">
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Aset Ditemukan</span>
                        <span class="text-base font-black text-white font-mono" x-text="filteredPeriodAstaps.length + ' Item'"></span>
                    </div>
                </div>
            </div>

            <!-- Callout Kebijakan Tutup Buku: Ubah Dinonaktifkan & Reklas Berfungsi -->
            <div class="bg-cyan-950/20 border border-cyan-500/30 rounded-2xl p-4 sm:p-5 flex items-start gap-3.5 shadow-lg">
                <div class="p-2 rounded-xl bg-cyan-500/20 text-cyan-300 shrink-0 mt-0.5">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="space-y-1 text-xs">
                    <h4 class="font-bold text-cyan-300 text-sm flex items-center gap-2">
                        <span>Kebijakan Transaksi Periode Tutup Buku</span>
                    </h4>
                    <p class="text-slate-300 leading-relaxed">
                        Data aset pada periode ini berada dalam proteksi penutupan buku.
                        <strong class="text-rose-300">Tombol 'Ubah' dinonaktifkan</strong> untuk mencegah perubahan sepihak pada nilai saldo awal dan spesifikasi pengadaan.
                        Namun, <strong class="text-emerald-300">Modul Reklasifikasi tetap aktif dan berfungsi penuh</strong> untuk mendukung penyesuaian akun (Pindah KIB, Ekstrakomptabel, Intrakomptabel, Koreksi Nilai Saldo LKD/Manset, Hibah, atau Mutasi Antar-OPD).
                    </p>
                </div>
            </div>

            <!-- Filter & Search Bar untuk Tabel Aset -->
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-4 flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 shadow-md">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input type="text" 
                           x-model="tableSearch" 
                           placeholder="Cari nama barang, kode 108, nomor register / NIBAR..." 
                           class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-cyan-500 transition-colors">
                </div>

                <!-- Filter Tahun, Kategori KIB & Kondisi -->
                <div class="flex flex-wrap items-center gap-2 shrink-0">
                    <!-- Filter Tahun Aset -->
                    <select x-model="tableTahunFilter" 
                            class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs font-bold text-slate-200 focus:outline-none focus:border-cyan-500 cursor-pointer">
                        <option value="all">Semua Tahun</option>
                        @foreach($distinctYears as $yr)
                            <option value="{{ $yr }}">T.A. {{ $yr }}</option>
                        @endforeach
                    </select>

                    <select x-model="tableKibFilter" 
                            class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs font-bold text-slate-200 focus:outline-none focus:border-cyan-500 cursor-pointer">
                        <option value="all">Semua KIB / Akun</option>
                        <option value="KIB A">KIB A (Tanah)</option>
                        <option value="KIB B">KIB B (Peralatan &amp; Mesin)</option>
                        <option value="KIB C">KIB C (Gedung &amp; Bangunan)</option>
                        <option value="KIB D">KIB D (Jalan, Irigasi &amp; Jaringan)</option>
                        <option value="KIB E">KIB E (Aset Tetap Lainnya)</option>
                        <option value="KIB F">KIB F (KDP / Konstruksi)</option>
                        <option value="ATB">ATB (Tak Berwujud)</option>
                        <option value="KEMITRAAN">Kemitraan (1.5.2)</option>
                        <option value="EXTRACOM">Ekstrakomptabel</option>
                    </select>

                    <select x-model="tableKondisiFilter" 
                            class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2 text-xs font-bold text-slate-200 focus:outline-none focus:border-cyan-500 cursor-pointer">
                        <option value="all">Semua Kondisi</option>
                        <option value="Baik">Baik</option>
                        <option value="Kurang Baik">Kurang Baik</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
            </div>

            <!-- TABEL BESPOKE DARK DASHBOARD -->
            <div class="bg-slate-900 border border-slate-800 rounded-3xl overflow-hidden shadow-2xl">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-slate-950/80 border-b border-slate-800 text-slate-400 font-extrabold uppercase tracking-wider text-[10px]">
                                <th class="py-3.5 px-4 w-12 text-center">No</th>
                                <th class="py-3.5 px-4 min-w-[170px]">Kode 108 &amp; Klasifikasi</th>
                                <th class="py-3.5 px-4 min-w-[240px]">Nama Barang &amp; Rincian</th>
                                <th class="py-3.5 px-4 min-w-[90px] text-center">Volume</th>
                                <th class="py-3.5 px-4 min-w-[140px] text-right">Nilai Realisasi</th>
                                <th class="py-3.5 px-4 min-w-[120px] text-center">Kondisi Aset</th>
                                <th class="py-3.5 px-4 min-w-[150px] text-center">Riwayat Reklas</th>
                                <th class="py-3.5 px-4 w-[160px] min-w-[160px] text-center">Aksi Transaksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/80 text-slate-300 font-medium">
                            <template x-for="(item, idx) in filteredPeriodAstaps" :key="item.id || idx">
                                <tr class="hover:bg-slate-800/40 transition-colors">
                                    <!-- 1. Nomor Urut -->
                                    <td class="py-3.5 px-4 text-center font-mono text-slate-500 text-[11px]" x-text="idx + 1"></td>

                                    <!-- 2. Kode 108 & Klasifikasi (Badge KIB + Badge Sumber Dana Berdampingan) -->
                                    <td class="py-3.5 px-4">
                                        <div class="space-y-1.5">
                                            <div class="flex flex-wrap items-center gap-1.5">
                                                <!-- Badge KIB -->
                                                <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider border shadow-sm"
                                                    :class="{
                                                        'bg-cyan-500/20 text-cyan-300 border-cyan-500/30': getEffectiveKibCategory(item) === 'KIB B',
                                                        'bg-purple-500/20 text-purple-300 border-purple-500/30': getEffectiveKibCategory(item) === 'KIB C',
                                                        'bg-amber-500/20 text-amber-300 border-amber-500/30': getEffectiveKibCategory(item) === 'KIB A',
                                                        'bg-teal-500/20 text-teal-300 border-teal-500/30': getEffectiveKibCategory(item) === 'KIB D',
                                                        'bg-orange-500/20 text-orange-300 border-orange-500/30': getEffectiveKibCategory(item) === 'KIB E',
                                                        'bg-rose-500/20 text-rose-300 border-rose-500/30': getEffectiveKibCategory(item) === 'KIB F',
                                                        'bg-indigo-500/20 text-indigo-300 border-indigo-500/30': getEffectiveKibCategory(item) === 'ATB',
                                                        'bg-amber-400/20 text-amber-300 border-amber-400/30': item.category === 'EXTRACOM'
                                                    }"
                                                    x-text="item.category === 'EXTRACOM' ? 'EXTRACOM' : getEffectiveKibCategory(item)"></span>

                                                <!-- Badge Sumber Perolehan (BLUD, BM, Hibah, Mutasi, Kemitraan) -->
                                                <span class="inline-block px-2 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-wider border shadow-sm"
                                                    :class="{
                                                        'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': item.sumber_type === 'kemitraan',
                                                        'bg-teal-500/15 text-teal-300 border-teal-500/30': item.sumber_type === 'blud',
                                                        'bg-purple-500/15 text-purple-300 border-purple-500/30': item.sumber_type === 'hibah',
                                                        'bg-indigo-500/15 text-indigo-300 border-indigo-500/30': item.sumber_type === 'mutasi',
                                                        'bg-amber-500/15 text-amber-300 border-amber-500/30': item.sumber_type === 'belanja_modal' || !item.sumber_type
                                                    }"
                                                    x-text="item.sumber_label || 'BELANJA MODAL'"></span>
                                            </div>
                                            <div class="font-mono text-[11px] font-bold text-cyan-400" x-text="item.kode_barang || '-'"></div>
                                        </div>
                                    </td>

                                    <!-- 3. Nama Barang & Rincian -->
                                    <td class="py-3.5 px-4">
                                        <div class="space-y-0.5">
                                            <div class="font-bold text-white text-xs leading-snug" x-text="cleanNamaBarang(item.nama_barang)"></div>
                                            <div class="text-[11px] text-slate-400 truncate max-w-xs" 
                                                 x-show="item.jenis_aset_nama && item.jenis_aset_nama !== '-' && item.jenis_aset_nama.toLowerCase() !== cleanNamaBarang(item.nama_barang).toLowerCase()" 
                                                 x-text="item.jenis_aset_nama"></div>
                                        </div>
                                    </td>

                                    <!-- 4. Volume -->
                                    <td class="py-3.5 px-4 text-center">
                                        <div class="inline-flex flex-col items-center">
                                            <span class="font-extrabold text-white text-xs font-mono" x-text="item.jumlah_volume || 1"></span>
                                            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mt-0.5 px-1.5 py-0.2 rounded bg-slate-800/80 border border-slate-700/60" x-text="item.satuan || 'Unit'"></span>
                                        </div>
                                    </td>

                                    <!-- 5. Nilai Realisasi (Accounting Tabular-Nums Lurus Sempurna) -->
                                    <td class="py-3.5 px-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end font-mono text-xs">
                                            <span class="text-slate-500 font-bold text-[10px] mr-1.5 select-none">Rp</span>
                                            <span class="font-black tabular-nums text-xs tracking-tight"
                                                  :class="item.total_realisasi_num > 0 ? 'text-cyan-400' : 'text-slate-500'"
                                                  x-text="(Number(item.total_realisasi_num || 0)).toLocaleString('id-ID')"></span>
                                        </div>
                                    </td>

                                    <!-- 6. Kondisi Aset (Murni Status Kondisi) -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase border shadow-sm"
                                            :class="{
                                                'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': item.kondisi === 'Baik',
                                                'bg-amber-500/15 text-amber-300 border-amber-500/30': item.kondisi === 'Kurang Baik',
                                                'bg-rose-500/15 text-rose-300 border-rose-500/30': item.kondisi === 'Rusak Berat'
                                            }">
                                            <span class="w-1.5 h-1.5 rounded-full"
                                                  :class="{
                                                      'bg-emerald-400 animate-pulse': item.kondisi === 'Baik',
                                                      'bg-amber-400': item.kondisi === 'Kurang Baik',
                                                      'bg-rose-400': item.kondisi === 'Rusak Berat'
                                                  }"></span>
                                            <span x-text="item.kondisi || 'Baik'"></span>
                                        </span>
                                    </td>

                                    <!-- 7. Riwayat Reklasifikasi (Kolom Khusus Reklas) -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <template x-if="item.is_reklas || item.reklas_count > 0">
                                            <div class="inline-flex flex-col items-center gap-0.5">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-[10px] font-black bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 shadow-sm shadow-indigo-500/10">
                                                    <svg class="w-3 h-3 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                    </svg>
                                                    <span x-text="(item.reklas_count ? item.reklas_count : 1) + 'x Direklas'"></span>
                                                </span>
                                                <template x-if="item.jenis_reklas">
                                                    <span class="text-[9px] font-bold text-indigo-400/80 font-mono tracking-tight" x-text="item.jenis_reklas"></span>
                                                </template>
                                            </div>
                                        </template>
                                        <template x-if="!item.is_reklas && (!item.reklas_count || item.reklas_count === 0)">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[10px] font-medium text-slate-500 bg-slate-950/60 border border-slate-800/80">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-600"></span>
                                                <span>Belum Direklas</span>
                                            </span>
                                        </template>
                                    </td>

                                    <!-- 8. Aksi Transaksi (Hanya Detail & Reklas) -->
                                    <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                        <div class="inline-flex items-center justify-center gap-1.5">
                                            <!-- Tombol 1: Detail Lengkap -->
                                            <button type="button" 
                                                    @click="openDetail(item)"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400 font-extrabold text-[11px] transition-all shadow-sm active:scale-95 cursor-pointer leading-none"
                                                    title="Lihat Detail Lengkap Spesifikasi & Daftar Register NIBAR">
                                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                                <span>Detail</span>
                                            </button>

                                            <!-- Tombol 2: Reklasifikasi (Berfungsi Penuh Sesuai Kebijakan Tutup Buku) -->
                                            <button type="button" 
                                                    @click="openReklas(item)"
                                                    class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-indigo-500/15 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 font-extrabold text-[11px] transition-all shadow-sm active:scale-95 cursor-pointer leading-none"
                                                    title="Proses Reklasifikasi Aset (Pindah KIB, Ekstrakom, Koreksi Nilai, Hibah, Mutasi)">
                                                <svg class="w-3.5 h-3.5 text-indigo-400 group-hover:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                </svg>
                                                <span>Reklas</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </template>

                            <!-- Keadaan Kosong (No Data) -->
                            <template x-if="filteredPeriodAstaps.length === 0">
                                <tr>
                                    <td colspan="8" class="py-12 text-center text-slate-500">
                                        <div class="flex flex-col items-center justify-center space-y-2">
                                            <svg class="w-10 h-10 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                                            </svg>
                                            <span class="text-sm font-bold text-slate-400">Tidak ada data aset ditemukan pada periode ini.</span>
                                            <span class="text-xs text-slate-500">Silakan pilih triwulan lain atau sesuaikan kata kunci pencarian.</span>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- ========================================================================= -->
        <!-- 6. MODAL DETAIL, MODAL REKLASIFIKASI, MODAL CONFIRM & TOAST               -->
        <!-- ========================================================================= -->
        @include('pages.astap.index_partials.modal_detail')
        @include('pages.astap.index_partials.modal_reklas')
        @include('pages.astap.index_partials.modal_confirm')
        @include('pages.astap.index_partials.toast')

    </div>

    <!-- Script Alpine Extension Khusus Halaman Tutup Buku -->
    <script>
        function tutupBukuApp() {
            // Mewarisi seluruh method dan state dari astapCatalog (Reklasifikasi, Detail, Formatters)
            const base = typeof astapCatalog === 'function' ? astapCatalog() : {};

            return {
                ...base,

                // State Khusus Halaman Tutup Buku
                activeTab: 'triwulan', // 'triwulan' | 'tahunan'
                selectedPeriod: null,
                filterTahunTahunan: 'all',
                tableSearch: '',
                tableTahunFilter: 'all',
                tableKibFilter: 'all',
                tableKondisiFilter: 'all',

                // Inisialisasi awal
                initTutupBuku() {
                    // Default memilih triwulan yang aktif atau triwulan sebelumnya jika ada
                    const defaultTw = {{ $actualQuarter }};
                    const defaultYr = {{ $currentYear }};
                    
                    this.selectedPeriod = {
                        type: 'triwulan',
                        tw: defaultTw,
                        year: defaultYr,
                        label: 'Triwulan ' + (['I', 'II', 'III', 'IV'][defaultTw - 1] || 'I') + ' T.A. ' + defaultYr,
                        isLocked: false,
                        rentang: 'Triwulan Berjalan'
                    };
                    this.tableTahunFilter = String(defaultYr);
                },

                // Pemilihan Periode (Triwulan atau Tahunan)
                selectPeriod(p) {
                    this.selectedPeriod = p;
                    this.tableSearch = '';
                    this.tableTahunFilter = p.year ? String(p.year) : 'all';
                    this.tableKibFilter = 'all';
                    this.tableKondisiFilter = 'all';

                    this.$nextTick(() => {
                        const panel = document.getElementById('panel-tabel-aset');
                        if (panel) {
                            panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                        }
                    });
                },

                // Filter data aset berdasarkan periode yang sedang dipilih
                get filteredPeriodAstaps() {
                    if (!this.selectedPeriod || !Array.isArray(this.astaps)) return [];

                    const p = this.selectedPeriod;

                    return this.astaps.filter(item => {
                        // 1. Filter Berdasarkan Periode & Filter Tahun
                        const itemYr = parseInt(item.tahun_perolehan) || 0;

                        if (p.type === 'triwulan') {
                            const targetYr = (this.tableTahunFilter !== 'all') ? parseInt(this.tableTahunFilter) : p.year;
                            if (itemYr !== targetYr) return false;

                            let twNum = 1;
                            if (typeof item.triwulan_num === 'number' && item.triwulan_num > 0) {
                                twNum = item.triwulan_num;
                            } else {
                                const rawTw = String(item.triwulan || '').toUpperCase();
                                if (rawTw.includes('IV') || rawTw === '4') twNum = 4;
                                else if (rawTw.includes('III') || rawTw === '3') twNum = 3;
                                else if (rawTw.includes('II') || rawTw === '2') twNum = 2;
                                else twNum = 1;
                            }

                            if (twNum !== p.tw) return false;
                        } else if (p.type === 'tahunan') {
                            const targetYr = (this.tableTahunFilter !== 'all') ? parseInt(this.tableTahunFilter) : p.year;
                            if (itemYr !== targetYr) return false;
                        } else if (this.tableTahunFilter !== 'all') {
                            if (itemYr !== parseInt(this.tableTahunFilter)) return false;
                        }

                        // 2. Filter Kategori KIB
                        if (this.tableKibFilter !== 'all') {
                            const effectiveCat = (typeof this.getEffectiveKibCategory === 'function')
                                ? this.getEffectiveKibCategory(item)
                                : (item.category || '');
                            
                            if (this.tableKibFilter === 'EXTRACOM') {
                                if (item.category !== 'EXTRACOM') return false;
                            } else {
                                if (effectiveCat !== this.tableKibFilter) return false;
                            }
                        }

                        // 3. Filter Kondisi
                        if (this.tableKondisiFilter !== 'all') {
                            const itemKondisi = item.kondisi || 'Baik';
                            if (itemKondisi !== this.tableKondisiFilter) return false;
                        }

                        // 4. Filter Pencarian Text
                        if (this.tableSearch.trim() !== '') {
                            const q = this.tableSearch.toLowerCase();
                            const nama = (item.nama_barang || '').toLowerCase();
                            const kode = (item.kode_barang || '').toLowerCase();
                            const jenis = (item.jenis_aset_nama || '').toLowerCase();
                            
                            let nibarJoined = '';
                            if (Array.isArray(item.registers)) {
                                nibarJoined = item.registers.map(r => (r.nibar || r.no_register || '')).join(' ').toLowerCase();
                            }

                            if (!nama.includes(q) && !kode.includes(q) && !jenis.includes(q) && !nibarJoined.includes(q)) {
                                return false;
                            }
                        }

                        return true;
                    });
                },

                // Pembersih Teks Duplikat Nama Barang
                cleanNamaBarang(str) {
                    if (!str) return '-';
                    const s = String(str).trim();
                    const parts = s.split(',').map(p => p.trim()).filter(Boolean);
                    if (parts.length === 2 && parts[0].toLowerCase() === parts[1].toLowerCase()) {
                        return parts[0];
                    }
                    return s;
                }
            };
        }
    </script>
</x-layout>
