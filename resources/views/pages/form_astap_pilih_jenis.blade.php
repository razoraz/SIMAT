<x-layout title="Pilih Metode Perolehan Aset - SIMAT-RK">
    @section('page-title', 'Pilih Metode Perolehan Aset')
    @section('breadcrumb', 'Master Utama / Data ASTAP / Pilih Perolehan')

    <div class="max-w-6xl mx-auto space-y-8 py-4">

        <!-- Top Header & Back Button -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('astap.index') }}"
                    class="p-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-emerald-500/50 text-slate-400 hover:text-emerald-400 transition-all shadow-lg shadow-black/20 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight flex items-center gap-2.5">
                        <span>Pilih Sumber Perolehan Aset</span>
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-400 mt-0.5">
                        Tentukan asal perolehan barang untuk mengarahkan form pengisian data yang sesuai.
                    </p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                    SIMAT-RK v2.6 Ready
                </span>
            </div>
        </div>

        <!-- 4 Option Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Card 1: Belanja Modal (Active) -->
            <a href="{{ route('astap.create') }}"
                class="group relative flex flex-col justify-between p-7 rounded-3xl bg-gradient-to-br from-slate-900/90 via-slate-900/70 to-slate-950/90 border border-slate-800 hover:border-emerald-500/60 shadow-xl hover:shadow-2xl hover:shadow-emerald-500/10 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="absolute -right-12 -top-12 w-40 h-40 bg-emerald-500/10 rounded-full blur-3xl group-hover:bg-emerald-500/20 transition-all"></div>
                
                <div>
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-2xl text-emerald-400 shadow-inner group-hover:scale-110 transition-transform">
                            🏗️
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wider uppercase bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                            Perolehan Rutin
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-white group-hover:text-emerald-300 transition-colors mb-2">
                        Belanja Modal (APBD / BLUD)
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                        Pencatatan aset melalui pengadaan APBD/BLUD lengkap dengan rekening belanja SIPD, SPK, Faktur, SP2D, BAST Pemeriksaan, dan rekanan penyedia.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2 text-[11px]">
                        <span class="px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-300 border border-slate-700/50">Form 4-Langkah</span>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-300 border border-slate-700/50">SIPD Terintegrasi</span>
                        <span class="px-2.5 py-1 rounded-lg bg-slate-800/80 text-slate-300 border border-slate-700/50">KIB A - F</span>
                    </div>
                </div>

                <div class="mt-7 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-bold text-emerald-400 group-hover:text-emerald-300">
                    <span>Mulai Input Belanja Modal</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </div>
            </a>

            <!-- Card 2: Hibah / Bantuan (Active) -->
            <a href="{{ route('astap.create_hibah') }}"
                class="group relative flex flex-col justify-between p-7 rounded-3xl bg-gradient-to-br from-slate-900/90 via-slate-900/70 to-slate-950/90 border border-slate-800 hover:border-amber-400/60 shadow-xl hover:shadow-2xl hover:shadow-amber-400/10 transition-all duration-300 transform hover:-translate-y-1 overflow-hidden">
                <div class="absolute -right-12 -top-12 w-40 h-40 bg-amber-400/10 rounded-full blur-3xl group-hover:bg-amber-400/20 transition-all"></div>
                
                <div>
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="w-14 h-14 rounded-2xl bg-amber-400/10 border border-amber-400/30 flex items-center justify-center text-2xl text-amber-300 shadow-inner group-hover:scale-110 transition-transform">
                            🎁
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wider uppercase bg-amber-400/15 text-amber-300 border border-amber-400/30">
                            RMB Hibah RSDK
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-white group-hover:text-amber-300 transition-colors mb-2">
                        Hibah / Bantuan Pihak Ketiga
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-400 leading-relaxed">
                        Penerimaan aset dari Kemenkes, Dinas Kesehatan, Pemprov, instansi luar, atau swasta yang diserahkan berdasarkan Berita Acara Serah Terima (BAST) Hibah tanpa SP2D.
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2 text-[11px]">
                        <span class="px-2.5 py-1 rounded-lg bg-amber-400/10 text-amber-200 border border-amber-400/20">Form Ringkas 3-Langkah</span>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-400/10 text-amber-200 border border-amber-400/20">BAST Hibah Wajib</span>
                        <span class="px-2.5 py-1 rounded-lg bg-amber-400/10 text-amber-200 border border-amber-400/20">Sheet RMB Hibah</span>
                    </div>
                </div>

                <div class="mt-7 pt-4 border-t border-slate-800/80 flex items-center justify-between text-xs font-bold text-amber-300 group-hover:text-amber-200">
                    <span>Mulai Input Data Hibah</span>
                    <svg class="w-5 h-5 transform group-hover:translate-x-1.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                    </svg>
                </div>
            </a>

            <!-- Card 3: Belanja Barang & Jasa (Coming Soon) -->
            <div class="relative flex flex-col justify-between p-7 rounded-3xl bg-slate-900/40 border border-slate-800/50 shadow-lg opacity-75 cursor-not-allowed overflow-hidden">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="w-14 h-14 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-2xl text-indigo-400 shadow-inner">
                            📦
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wider uppercase bg-slate-800 text-slate-400 border border-slate-700">
                            Segera Hadir
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-300 mb-2">
                        Belanja Barang (Ekstrakomtabel)
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Inventarisasi barang non-kapitalisasi dari Belanja Barang & Jasa (kode rekening 5.1.02) untuk monitoring penempatan ruangan.
                    </p>
                </div>

                <div class="mt-7 pt-4 border-t border-slate-800/40 flex items-center justify-between text-xs font-semibold text-slate-500">
                    <span>Modul dalam persiapan rilis</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

            <!-- Card 4: Mutasi Masuk SKPD (Coming Soon) -->
            <div class="relative flex flex-col justify-between p-7 rounded-3xl bg-slate-900/40 border border-slate-800/50 shadow-lg opacity-75 cursor-not-allowed overflow-hidden">
                <div>
                    <div class="flex items-start justify-between gap-4 mb-5">
                        <div class="w-14 h-14 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-2xl text-purple-400 shadow-inner">
                            🔄
                        </div>
                        <span class="px-3 py-1 rounded-full text-[11px] font-extrabold tracking-wider uppercase bg-slate-800 text-slate-400 border border-slate-700">
                            Segera Hadir
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-300 mb-2">
                        Mutasi Masuk (+) Antar SKPD
                    </h3>
                    <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                        Penerimaan barang hasil pelimpahan hak kelola dari OPD/Dinas lain di lingkungan Pemerintah Kabupaten Bondowoso.
                    </p>
                </div>

                <div class="mt-7 pt-4 border-t border-slate-800/40 flex items-center justify-between text-xs font-semibold text-slate-500">
                    <span>Modul dalam persiapan rilis</span>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>
            </div>

        </div>

        <!-- Info Box Bottom -->
        <div class="rounded-2xl bg-slate-900/60 border border-slate-800 p-5 flex items-center gap-4 text-xs text-slate-400">
            <div class="p-2.5 rounded-xl bg-slate-800/80 text-emerald-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <div>
                <span class="font-semibold text-slate-200">Keterangan:</span>
                Semua aset yang didaftarkan (baik Belanja Modal maupun Hibah) akan memperoleh Nomor Induk Barang (NIBAR) dan label QR Code unik untuk penatausahaan di Kartu Inventaris Ruangan (KIR).
            </div>
        </div>

    </div>
</x-layout>
