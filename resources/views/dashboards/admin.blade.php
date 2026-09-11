<x-layout title="Dashboard Admin Operasional - SIMAT-RK">
    @section('page-title', 'Dashboard Admin')
    @section('breadcrumb', 'Beranda / Admin Operasional')

    <!-- Welcome Banner Card -->
    <div class="bg-gradient-to-r from-cyan-500/10 via-slate-900 to-slate-900 border border-cyan-500/30 rounded-3xl p-6 sm:p-8 shadow-2xl mb-8 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-cyan-500/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold mb-3">
                    <span>🛡️ ADMIN OPERASIONAL - OTORISASI OPERASIONAL (CRUD)</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-white tracking-tight">Selamat Datang, {{ Auth::user()->name }}!</h1>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl leading-relaxed">
                    Anda berada di Panel Admin Operasional. Wewenang Anda meliputi pembuatan Distribusi ASTAP, Mutasi Aset, Berita Acara (BAST), pembuatan akun User, serta Update/Delete Data ASTAP (status kondisi), Distribusi, User, dan BAST.
                </p>
            </div>
            
            <!-- Admin Fast Create Action -->
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('distribusi.create') }}" class="px-3.5 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center space-x-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    <span>Distribusi ASTAP Baru</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Metric Summary Stats Cards (Posisi 1) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Aset (ASTAP)</span>
                <div class="p-2 rounded-xl bg-emerald-500/10 text-emerald-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg></div>
            </div>
            <p class="text-2xl font-black text-white">{{ number_format($totalAsetVolumeCount ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-emerald-400">Unit</span></p>
            <p class="text-[11px] text-slate-400 mt-1">Valuasi: <span class="text-emerald-400 font-bold">{{ $hargaAsetFormatted ?? 'Rp 0' }} {{ $hargaAsetUnit ?? '' }}</span> ({{ number_format($totalAstapMasterCount ?? 0, 0, ',', '.') }} Master)</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Total Unit RSUD</span>
                <div class="p-2 rounded-xl bg-cyan-500/10 text-cyan-400"><svg class="w-4 h-4" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m0 0h4m-4 0V11m0 4h4" />
                    </svg></div>
            </div>
            <p class="text-2xl font-black text-white">{{ number_format($totalUnitRsudCount ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-cyan-400">Unit</span>
            </p>
            <p class="text-[11px] text-slate-400 mt-1">Master Unit & Ruang Kerja RSUD</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Distribusi Barang</span>
                <div class="p-2 rounded-xl bg-teal-500/10 text-teal-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/></svg></div>
            </div>
            <p class="text-2xl font-black text-white">{{ number_format($totalTerdistribusiUnit ?? 0, 0, ',', '.') }} <span class="text-xs font-normal text-teal-400">Terdistribusi</span></p>
            <p class="text-[11px] text-slate-400 mt-1">Ke {{ $totalUnitRsudCount ?? 0 }} Unit RSUD ({{ $totalTransaksiDistribusi ?? 0 }} Transaksi)</p>
        </div>

        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 shadow-xl">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="text-xs font-bold uppercase tracking-wider">Kondisi Aset</span>
                <div class="p-2 rounded-xl bg-amber-500/10 text-amber-400"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg></div>
            </div>
            <div class="flex items-baseline justify-between">
                <p class="text-2xl font-black text-white">{{ number_format($kondisiBaik ?? 0, 0, ',', '.') }} <span class="text-xs font-bold text-emerald-400">Baik</span></p>
                <span class="text-xs font-extrabold text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-md border border-rose-500/20">{{ number_format($totalRusak ?? 0, 0, ',', '.') }} Rusak</span>
            </div>
            <p class="text-[11px] text-slate-400 mt-1">{{ number_format($kondisiRusakRingan ?? 0, 0, ',', '.') }} Rusak Ringan · {{ number_format($kondisiRusakBerat ?? 0, 0, ',', '.') }} Rusak Berat</p>
        </div>
    </div>

    <!-- Grafik Peningkatan Aset (Posisi Kedua) -->
    <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 mb-8">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div>
                <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-400 border border-cyan-500/30 text-xs font-bold mb-2">
                    <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                    <span>GRAFIK PERTUMBUHAN ASET TETAP</span>
                </div>
                <h3 class="text-lg font-extrabold text-white">Peningkatan Aset: Valuasi Harga & Kuantitas Volume</h3>
                <p class="text-xs text-slate-400 mt-0.5">Visualisasi tren pertumbuhan akumulasi nilai investasi dan jumlah unit aset RSUD Dr. H. Koesnandi</p>
            </div>

            <!-- Mode Selector Toggle Pills -->
            <div class="inline-flex p-1 bg-slate-950 border border-slate-800 rounded-2xl shrink-0 text-xs font-semibold">
                <button id="btnKumulatif" onclick="switchChartMode('kumulatif')" class="px-3.5 py-1.5 rounded-xl bg-cyan-500 text-slate-950 font-bold transition-all shadow-md">
                    📈 Akumulasi Peningkatan
                </button>
                <button id="btnPerTahun" onclick="switchChartMode('pertahun')" class="px-3.5 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all">
                    📊 Per Tahun Pengadaan
                </button>
            </div>
        </div>

        <!-- Canvas Chart -->
        <div class="relative w-full h-[320px] sm:h-[360px] p-3 bg-slate-950/50 rounded-2xl border border-slate-800/80">
            <canvas id="astapGrowthChart"></canvas>
        </div>

        <!-- Indicator Keterangan Di Bagian Bawah Chart -->
        <div class="mt-4 pt-3 border-t border-slate-800/80 flex items-center justify-between text-xs text-slate-400">
            <div class="flex items-center space-x-2">
                <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
                <span>Data Valuasi & Kuantitas Terhubung Real-Time dengan Database</span>
            </div>
            <div class="text-[11px] text-slate-500 hidden sm:block">
                RSUD dr. H. Koesnandi Bondowoso
            </div>
        </div>
    </div>

    <!-- Grid 2 Kolom: Grafik Kondisi Barang & Grafik Distribusi Barang -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        
        <!-- 1. Grafik Keseluruhan Kondisi Barang -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>STATUS FISIK & KELAYAKAN</span>
                    </div>
                    <span class="text-xs font-mono font-bold text-slate-400">{{ $totalAsetRegisterCount ?? 0 }} Total Register</span>
                </div>
                <h3 class="text-base font-extrabold text-white">Grafik Kondisi Keseluruhan Barang</h3>
                <p class="text-xs text-slate-400 mt-0.5">Proporsi kondisi aset inventaris siap pakai vs memerlukan servis</p>
            </div>

            <!-- Chart Canvas Container -->
            <div class="relative w-full h-[240px] sm:h-[260px] my-4 flex items-center justify-center">
                <canvas id="kondisiChart"></canvas>
            </div>

            <!-- Summary Chips -->
            <div class="grid grid-cols-4 gap-2 pt-4 border-t border-slate-800/80 text-center">
                <div class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                    <div class="text-[10px] text-emerald-400 font-bold uppercase">Baik</div>
                    <div class="text-sm font-black text-white font-mono">{{ $kondisiBaik ?? 0 }}</div>
                </div>
                <div class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/20">
                    <div class="text-[10px] text-amber-400 font-bold uppercase">Kurang Baik</div>
                    <div class="text-sm font-black text-white font-mono">{{ $kondisiKurangBaik ?? 0 }}</div>
                </div>
                <div class="p-2 rounded-xl bg-orange-500/10 border border-orange-500/20">
                    <div class="text-[10px] text-orange-400 font-bold uppercase">Rusak Ringan</div>
                    <div class="text-sm font-black text-white font-mono">{{ $kondisiRusakRingan ?? 0 }}</div>
                </div>
                <div class="p-2 rounded-xl bg-rose-500/10 border border-rose-500/20">
                    <div class="text-[10px] text-rose-400 font-bold uppercase">Rusak Berat</div>
                    <div class="text-sm font-black text-white font-mono">{{ $kondisiRusakBerat ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- 2. Grafik Distribusi & Penempatan Aset -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between gap-2 mb-3">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-400 border border-teal-500/30 text-xs font-bold">
                        <span class="w-2 h-2 rounded-full bg-teal-400 animate-pulse"></span>
                        <span>PERBANDINGAN DISTRIBUSI</span>
                    </div>
                    <span class="text-xs font-mono font-bold text-teal-400">{{ $persenTerdistribusi ?? 0 }}% Terdistribusi</span>
                </div>
                <h3 class="text-base font-extrabold text-white">Grafik Distribusi Barang ke Ruangan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Perbandingan barang yang sudah didistribusikan vs belum didistribusikan</p>
            </div>

            <!-- Chart Canvas Container -->
            <div class="relative w-full h-[240px] sm:h-[260px] my-4 flex items-center justify-center">
                <canvas id="distribusiChart"></canvas>
            </div>

            <!-- Summary Chips -->
            <div class="grid grid-cols-2 gap-2 pt-4 border-t border-slate-800/80 text-center">
                <div class="p-2 rounded-xl bg-teal-500/10 border border-teal-500/20">
                    <div class="text-[10px] text-teal-400 font-bold uppercase">Sudah Didistribusikan</div>
                    <div class="text-sm font-black text-white font-mono">{{ $totalTerdistribusiUnit ?? 0 }} Unit</div>
                </div>
                <div class="p-2 rounded-xl bg-purple-500/10 border border-purple-500/20">
                    <div class="text-[10px] text-purple-400 font-bold uppercase">Belum Didistribusikan (Gudang)</div>
                    <div class="text-sm font-black text-white font-mono">{{ $belumTerdistribusi ?? 0 }} Unit</div>
                </div>
            </div>
        </div>

    </div>

    <!-- Admin CRUD Action Cards (Posisi Ketiga) -->
    <div class="mb-8">
        <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-4 flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-cyan-400"></span>
            <span>Akses Fitur Admin Operasional</span>
        </h3>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- 1. Manajemen Akun Sub Admin -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-amber-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl bg-amber-500/10 text-amber-400 font-bold text-lg">👥</div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
                </div>
                <h4 class="text-sm font-bold text-white group-hover:text-amber-400 transition-colors">Manajemen Akun Sub Admin</h4>
                <p class="text-xs text-slate-400 mt-1">Register akun baru, reset password, & hapus akun.</p>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('master.users') }}" class="text-amber-400 hover:text-amber-300">+ Create Akun</a>
                    <a href="{{ route('master.users') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
                </div>
            </div>

            <!-- 2. Mutasi Aset -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-cyan-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl bg-cyan-500/10 text-cyan-400 font-bold text-lg">🔄</div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
                </div>
                <h4 class="text-sm font-bold text-white group-hover:text-cyan-400 transition-colors">Mutasi Aset</h4>
                <p class="text-xs text-slate-400 mt-1">Form perpindahan lokasi barang antar unit/ruangan RSUD & riwayat penanggung jawab.</p>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('mutasi.create') }}" class="text-cyan-400 hover:text-cyan-300">+ Mutasi Baru</a>
                    <a href="{{ route('mutasi.index') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
                </div>
            </div>

            <!-- 3. Distribusi ASTAP -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-teal-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl bg-teal-500/10 text-teal-400 font-bold text-lg">🚚</div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
                </div>
                <h4 class="text-sm font-bold text-white group-hover:text-teal-400 transition-colors">Distribusi ASTAP</h4>
                <p class="text-xs text-slate-400 mt-1">Form alokasi distribusi barang ke unit RSUD, perbarui lokasi barang, & hapus data distribusi.</p>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('distribusi.create') }}" class="text-teal-400 hover:text-teal-300">+ Distribusi Baru</a>
                    <a href="{{ route('distribusi.index') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
                </div>
            </div>

            <!-- 4. Pembuatan Berita Acara (BAST) -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-purple-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl bg-purple-500/10 text-purple-400 font-bold text-lg">📄</div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
                </div>
                <h4 class="text-sm font-bold text-white group-hover:text-purple-400 transition-colors">Berita Acara
                    (BAST)</h4>
                <p class="text-xs text-slate-400 mt-1">Cetak & buat dokumen BAST penyerahan aset, update penanggung jawab, & hapus dokumen.</p>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('bast.index') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
                </div>
            </div>

            <!-- 5. Unit & Paviliun -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-indigo-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl bg-indigo-500/10 text-indigo-400 font-bold text-lg">🏥</div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
                </div>
                <h4 class="text-sm font-bold text-white group-hover:text-indigo-400 transition-colors">Unit & Paviliun</h4>
                <p class="text-xs text-slate-400 mt-1">Manajemen gedung paviliun, unit ruangan kerja, instalasi RSUD, & kepala penanggung jawab.</p>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('unit.create') }}" class="text-indigo-400 hover:text-indigo-300">+ Tambah Unit</a>
                    <a href="{{ route('unit.index') }}" class="text-slate-400 hover:text-white">Kelola Data
                        &rarr;</a>
                </div>
            </div>

            <!-- 6. Data ASTAP (Aset Tetap) -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-emerald-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold text-lg">📦</div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
                </div>
                <h4 class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors">Data ASTAP (Aset Tetap)</h4>
                <p class="text-xs text-slate-400 mt-1">Buku induk inventaris ASTAP, nomor registrasi NIBAR, cetak barcode QR, & rincian perolehan.</p>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('astap.create') }}" class="text-emerald-400 hover:text-emerald-300">+ Tambah ASTAP</a>
                    <a href="{{ route('astap.index') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
                </div>
            </div>

            <!-- 7. Master Jenis ASTAP -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-emerald-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl bg-emerald-500/10 text-emerald-400 font-bold text-lg">🏷️</div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
                </div>
                <h4 class="text-sm font-bold text-white group-hover:text-emerald-400 transition-colors">Master Jenis ASTAP</h4>
                <p class="text-xs text-slate-400 mt-1">Kelola 8 kategori utama (Tanah, Bangunan, Peralatan/Mesin, Irigasi, Tetap Lainnya, Tidak Berwujud).</p>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('master.jenis_astap') }}" class="text-emerald-400 hover:text-emerald-300">+ Tambah Jenis</a>
                    <a href="{{ route('master.jenis_astap') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
                </div>
            </div>

            <!-- 8. Master Jenis Pengadaan -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-blue-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl bg-blue-500/10 text-blue-400 font-bold text-lg">📋</div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
                </div>
                <h4 class="text-sm font-bold text-white group-hover:text-blue-400 transition-colors">Master Jenis Pengadaan</h4>
                <p class="text-xs text-slate-400 mt-1">Kelola sumber dana pengadaan (APBD Kabupaten, DAK Kesehatan, BLUD RSUD, Hibah Pemerintah).</p>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('master.jenis_pengadaan') }}" class="text-blue-400 hover:text-blue-300">+ Tambah Sumber</a>
                    <a href="{{ route('master.jenis_pengadaan') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
                </div>
            </div>

            <!-- 9. Rekening Belanja SIPD -->
            <div class="bg-slate-900/80 border border-slate-800 rounded-2xl p-5 hover:border-violet-500/40 transition-all group">
                <div class="flex items-center justify-between mb-3">
                    <div class="p-2.5 rounded-xl bg-violet-500/10 text-violet-400 font-bold text-lg">💳</div>
                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 border border-emerald-500/30 px-2 py-0.5 rounded-md">CRUD Aktif</span>
                </div>
                <h4 class="text-sm font-bold text-white group-hover:text-violet-400 transition-colors">Rekening Belanja SIPD</h4>
                <p class="text-xs text-slate-400 mt-1">Kelola kode akun rekening belanja aset, klasifikasi belanja modal, & sinkronisasi SIPD.</p>
                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs font-semibold">
                    <a href="{{ route('master.rekening_belanja') }}" class="text-violet-400 hover:text-violet-300">+ Tambah Akun</a>
                    <a href="{{ route('master.rekening_belanja') }}" class="text-slate-400 hover:text-white">Kelola Data &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Script Inisialisasi Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script>
    function initAstapAdminChart() {
        const labels = {{ Js::from($chartLabels ?? []) }};
        const hargaPerTahun = {{ Js::from($chartHargaDataJuta ?? []) }};
        const volPerTahun = {{ Js::from($chartVolumeData ?? []) }};
        const hargaKumulatif = {{ Js::from($chartKumulatifHargaJuta ?? []) }};
        const volKumulatif = {{ Js::from($chartKumulatifVolume ?? []) }};

        const ctx = document.getElementById('astapGrowthChart');
        if (!ctx) return;

        if (typeof Chart === 'undefined') {
            console.warn('Chart.js belum siap, mencoba memuat kembali...');
            setTimeout(initAstapAdminChart, 150);
            return;
        }

        const chartCtx = ctx.getContext('2d');

        // Linear Gradient Fills
        const gradientHarga = chartCtx.createLinearGradient(0, 0, 0, 300);
        gradientHarga.addColorStop(0, 'rgba(6, 182, 212, 0.45)');
        gradientHarga.addColorStop(1, 'rgba(6, 182, 212, 0.0)');

        const gradientVol = chartCtx.createLinearGradient(0, 0, 0, 300);
        gradientVol.addColorStop(0, 'rgba(16, 185, 129, 0.35)');
        gradientVol.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

        window.astapChart = new Chart(chartCtx, {
            type: 'line',
            data: {
                labels: labels.length > 0 ? labels : ['Thn 2022', 'Thn 2023', 'Thn 2024', 'Thn 2025', 'Thn 2026'],
                datasets: [
                    {
                        label: 'Valuasi Harga Aset (Rp Juta)',
                        data: hargaKumulatif.length > 0 ? hargaKumulatif : [350, 750, 1200, 2400, 5200],
                        borderColor: '#06b6d4',
                        backgroundColor: gradientHarga,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#06b6d4',
                        pointBorderColor: '#020617',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        yAxisID: 'yHarga'
                    },
                    {
                        label: 'Kuantitas Volume (Unit)',
                        data: volKumulatif.length > 0 ? volKumulatif : [15, 38, 75, 120, 184],
                        borderColor: '#10b981',
                        backgroundColor: gradientVol,
                        borderWidth: 2.5,
                        borderDash: [4, 4],
                        fill: false,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#020617',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 8,
                        yAxisID: 'yVol'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: '#94a3b8',
                            font: { size: 11, weight: 'bold' },
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#f8fafc',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function (context) {
                                let val = context.raw || 0;
                                if (context.datasetIndex === 0) {
                                    if (val >= 1000) {
                                        return ` 💰 Valuasi Aset: Rp ${(val / 1000).toFixed(2).replace('.', ',')} Miliar (${val.toLocaleString('id-ID')} Juta)`;
                                    }
                                    return ` 💰 Valuasi Aset: Rp ${val.toLocaleString('id-ID')} Juta`;
                                } else {
                                    return ` 📏 Total Volume: ${val} Unit Barang`;
                                }
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: { color: '#94a3b8', font: { size: 11, weight: '600' } }
                    },
                    yHarga: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                        grid: { color: 'rgba(51, 65, 85, 0.3)' },
                        ticks: {
                            color: '#06b6d4',
                            font: { size: 10, weight: 'bold' },
                            callback: function (val) {
                                if (val >= 1000) {
                                    return 'Rp ' + (val / 1000).toFixed(1) + ' M';
                                }
                                return 'Rp ' + val + ' Jt';
                            }
                        },
                        title: {
                            display: true,
                            text: 'Valuasi (Rupiah)',
                            color: '#06b6d4',
                            font: { size: 10, weight: 'bold' }
                        }
                    },
                    yVol: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: { drawOnChartArea: false },
                        ticks: {
                            color: '#10b981',
                            font: { size: 10, weight: 'bold' },
                            callback: function (val) { return val + ' Unit'; }
                        },
                        title: {
                            display: true,
                            text: 'Kuantitas (Unit)',
                            color: '#10b981',
                            font: { size: 10, weight: 'bold' }
                        }
                    }
                }
            }
        });

        // 2. Inisialisasi Grafik Kondisi Keseluruhan Barang (Doughnut Chart)
        const kondisiCtx = document.getElementById('kondisiChart');
        if (kondisiCtx) {
            const kondisiLabels = {{ Js::from($chartKondisiLabels ?? ['Baik', 'Kurang Baik', 'Rusak Ringan', 'Rusak Berat']) }};
            const kondisiData = {{ Js::from($chartKondisiData ?? [0, 0, 0, 0]) }};

            new Chart(kondisiCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: kondisiLabels,
                    datasets: [{
                        data: kondisiData,
                        backgroundColor: [
                            '#10b981', // Emerald - Baik
                            '#f59e0b', // Amber - Kurang Baik
                            '#f97316', // Orange - Rusak Ringan
                            '#ef4444', // Rose - Rusak Berat
                        ],
                        borderColor: '#020617',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                font: { size: 10, weight: 'bold' },
                                usePointStyle: true,
                                padding: 10
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const val = context.raw || 0;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${val} Unit (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        // 3. Inisialisasi Grafik Perbandingan Distribusi Barang (Doughnut Chart)
        const distCtx = document.getElementById('distribusiChart');
        if (distCtx) {
            const distLabels = {{ Js::from($chartDistribusiStatusLabels ?? ['Sudah Didistribusikan', 'Belum Didistribusikan (Gudang)']) }};
            const distData = {{ Js::from($chartDistribusiStatusData ?? [$totalTerdistribusiUnit ?? 0, $belumTerdistribusi ?? 0]) }};

            new Chart(distCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: distLabels,
                    datasets: [{
                        data: distData,
                        backgroundColor: [
                            '#14b8a6', // Teal - Sudah Didistribusikan
                            '#a855f7', // Purple - Belum Didistribusikan (Gudang)
                        ],
                        borderColor: '#020617',
                        borderWidth: 3,
                        hoverOffset: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '68%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: '#94a3b8',
                                font: { size: 10, weight: 'bold' },
                                usePointStyle: true,
                                padding: 10
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleColor: '#f8fafc',
                            bodyColor: '#cbd5e1',
                            borderColor: '#334155',
                            borderWidth: 1,
                            padding: 10,
                            callbacks: {
                                label: function(context) {
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const val = context.raw || 0;
                                    const pct = total > 0 ? ((val / total) * 100).toFixed(1) : 0;
                                    return ` ${context.label}: ${val} Unit (${pct}%)`;
                                }
                            }
                        }
                    }
                }
            });
        }

        window.switchChartMode = function (mode) {
            const btnKumulatif = document.getElementById('btnKumulatif');
            const btnPerTahun = document.getElementById('btnPerTahun');
            if (!btnKumulatif || !btnPerTahun || !window.astapChart) return;

            if (mode === 'kumulatif') {
                btnKumulatif.className = "px-3.5 py-1.5 rounded-xl bg-cyan-500 text-slate-950 font-bold transition-all shadow-md";
                btnPerTahun.className = "px-3.5 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all";

                window.astapChart.data.datasets[0].label = 'Akumulasi Valuasi (Rp Juta)';
                window.astapChart.data.datasets[0].data = hargaKumulatif;
                window.astapChart.data.datasets[1].label = 'Akumulasi Kuantitas (Unit)';
                window.astapChart.data.datasets[1].data = volKumulatif;
            } else {
                btnPerTahun.className = "px-3.5 py-1.5 rounded-xl bg-emerald-500 text-slate-950 font-bold transition-all shadow-md";
                btnKumulatif.className = "px-3.5 py-1.5 rounded-xl text-slate-400 hover:text-white transition-all";

                window.astapChart.data.datasets[0].label = 'Pengadaan Valuasi (Rp Juta/Thn)';
                window.astapChart.data.datasets[0].data = hargaPerTahun;
                window.astapChart.data.datasets[1].label = 'Pengadaan Kuantitas (Unit/Thn)';
                window.astapChart.data.datasets[1].data = volPerTahun;
            }
            window.astapChart.update();
        };
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAstapAdminChart);
    } else {
        initAstapAdminChart();
    }
    </script>
</x-layout>
