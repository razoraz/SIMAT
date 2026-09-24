<x-layout title="Pilih Sumber Perolehan Aset - SIMAT-RK">
    @section('page-title', 'Pilih Metode Perolehan Aset')
    @section('breadcrumb', 'Master Utama / Data ASTAP / Pilih Perolehan')

    <div class="max-w-6xl mx-auto space-y-4 py-1 px-2 sm:px-4">

        <!-- Header Ringkas & Sejajar -->
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-3">
                <a href="{{ route('astap.index') }}"
                    class="p-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-slate-400 hover:text-white transition-all flex items-center justify-center shrink-0"
                    title="Kembali ke Data ASTAP">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-xl sm:text-2xl font-black text-white tracking-tight leading-tight">
                        Pilih Sumber Perolehan Aset
                    </h1>
                    <p class="text-xs text-slate-400">
                        Tentukan asal perolehan barang untuk mengarahkan form pengisian data yang sesuai.
                    </p>
                </div>
            </div>

            <div class="hidden sm:flex items-center">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                    SIMAT-RK v2.6 Ready
                </span>
            </div>
        </div>

        <!-- 5 Option Cards Grid (Symmetric, 6-col responsive layout: Row 1 2-cards 50-50, Row 2 3-cards 33-33-33) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">

            <!-- Card 1: Belanja Modal (APBD / BLUD) -->
            <div class="flex flex-col justify-between p-5 rounded-2xl bg-slate-900/95 border border-slate-800 hover:border-emerald-500/60 shadow-lg hover:shadow-emerald-500/5 transition-all duration-200 group md:col-span-1 lg:col-span-3">
                <div>
                    <!-- Header Kartu -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/15 border border-emerald-500/30 flex items-center justify-center text-emerald-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                            Perolehan Rutin APBD
                        </span>
                    </div>

                    <!-- Judul -->
                    <h2 class="text-base font-bold text-white group-hover:text-emerald-300 transition-colors">
                        Belanja Modal (APBD / BLUD)
                    </h2>

                    <!-- Deskripsi (Kunci 2 baris seragam) -->
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed line-clamp-2 h-9">
                        Pengadaan aset rutin APBD/BLUD lengkap dengan kode rekening belanja SIPD, SPK, Faktur, SP2D, dan BAST.
                    </p>

                    <!-- Tags Seragam (3 Buah) -->
                    <div class="grid grid-cols-3 gap-2 mt-3 text-[10px] text-slate-300">
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            ✓ Form 4-Langkah
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            📋 Pagu SIPD
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            📦 KIB A - F
                        </div>
                    </div>
                </div>

                <!-- Tombol Action Penuh yang Rapi & Seragam -->
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <a href="{{ route('astap.create') }}"
                        class="w-full py-2.5 px-4 rounded-xl bg-emerald-500/15 hover:bg-emerald-500 text-emerald-300 hover:text-slate-950 font-bold text-xs transition-all duration-200 flex items-center justify-between border border-emerald-500/30 group-hover:border-emerald-500">
                        <span>Input Belanja Modal</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Card 2: Hibah / Bantuan Masuk -->
            <div class="flex flex-col justify-between p-5 rounded-2xl bg-slate-900/95 border border-slate-800 hover:border-amber-400/60 shadow-lg hover:shadow-amber-400/5 transition-all duration-200 group md:col-span-1 lg:col-span-3">
                <div>
                    <!-- Header Kartu -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-400/15 border border-amber-400/30 flex items-center justify-center text-amber-300 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-400/10 text-amber-300 border border-amber-400/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                            Hibah Masuk RSDK
                        </span>
                    </div>

                    <!-- Judul -->
                    <h2 class="text-base font-bold text-white group-hover:text-amber-300 transition-colors">
                        Hibah / Bantuan Masuk
                    </h2>

                    <!-- Deskripsi (Kunci 2 baris seragam) -->
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed line-clamp-2 h-9">
                        Penerimaan barang dari Kemenkes RI, Dinkes, Pemprov, instansi pemerintah, atau lembaga swasta berdasarkan BAST Hibah resmi.
                    </p>

                    <!-- Tags Seragam (3 Buah) -->
                    <div class="grid grid-cols-3 gap-2 mt-3 text-[10px] text-slate-300">
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            ✓ BAST Hibah
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            📄 Tanpa SIPD
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            📊 Sheet Hibah
                        </div>
                    </div>
                </div>

                <!-- Tombol Action Penuh yang Rapi & Seragam -->
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <a href="{{ route('astap.create_hibah') }}"
                        class="w-full py-2.5 px-4 rounded-xl bg-amber-400/15 hover:bg-amber-400 text-amber-300 hover:text-slate-950 font-bold text-xs transition-all duration-200 flex items-center justify-between border border-amber-400/30 group-hover:border-amber-400">
                        <span>Input Penerimaan Hibah</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Card 3: Belanja Barang (Perbekalan / Operasional) -->
            <div class="flex flex-col justify-between p-5 rounded-2xl bg-slate-900/95 border border-slate-800 hover:border-indigo-500/60 shadow-lg hover:shadow-indigo-500/5 transition-all duration-200 group md:col-span-1 lg:col-span-2">
                <div>
                    <!-- Header Kartu -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center text-indigo-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-500/10 text-indigo-300 border border-indigo-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                            Perbekalan / Operasional
                        </span>
                    </div>

                    <!-- Judul -->
                    <h2 class="text-base font-bold text-white group-hover:text-indigo-300 transition-colors">
                        Belanja Barang (Perbekalan)
                    </h2>

                    <!-- Deskripsi (Kunci 2 baris seragam) -->
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed line-clamp-2 h-9">
                        Pencatatan perbekalan &amp; barang operasional (kode 5.1.02) dari pusat perbekalan untuk pengawasan fisik inventaris ruangan (KIR).
                    </p>

                    <!-- Tags Seragam (3 Buah) -->
                    <div class="grid grid-cols-3 gap-2 mt-3 text-[10px] text-slate-300">
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            🏷️ Kode 5.1.02
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            🔍 Kontrol KIR
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            📦 Ekstrakom
                        </div>
                    </div>
                </div>

                <!-- Tombol Action Penuh yang Rapi & Seragam -->
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <a href="{{ route('astap.create_rekening') }}"
                        class="w-full py-2.5 px-4 rounded-xl bg-indigo-500/15 hover:bg-indigo-500 text-indigo-300 hover:text-white font-bold text-xs transition-all duration-200 flex items-center justify-between border border-indigo-500/30 group-hover:border-indigo-500">
                        <span>Input Belanja Barang</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Card 4: Pelimpahan SKPD (Dinas Luar) -->
            <div class="flex flex-col justify-between p-5 rounded-2xl bg-slate-900/95 border border-slate-800 hover:border-purple-500/60 shadow-lg hover:shadow-purple-500/5 transition-all duration-200 group md:col-span-1 lg:col-span-2">
                <div>
                    <!-- Header Kartu -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-500/15 border border-purple-500/30 flex items-center justify-center text-purple-400 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-purple-500/10 text-purple-300 border border-purple-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-purple-400"></span>
                            Pelimpahan SKPD Luar
                        </span>
                    </div>

                    <!-- Judul -->
                    <h2 class="text-base font-bold text-white group-hover:text-purple-300 transition-colors">
                        Pelimpahan SKPD (Dinas Luar)
                    </h2>

                    <!-- Deskripsi (Kunci 2 baris seragam) -->
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed line-clamp-2 h-9">
                        Penerimaan atau pelimpahan aset dari SKPD/Dinas luar ke RSUD Dr. H. Koesnandi berdasarkan Berita Acara resmi (BAP/BAMB).
                    </p>

                    <!-- Tags Seragam (3 Buah) -->
                    <div class="grid grid-cols-3 gap-2 mt-3 text-[10px] text-slate-300">
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            📑 Berita BAMB
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            🏢 Tanpa SIPD
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            📍 Lokasi Baru
                        </div>
                    </div>
                </div>

                <!-- Tombol Action Penuh yang Rapi & Seragam -->
                <div class="mt-4 pt-3 border-t border-slate-800/80 space-y-2">
                    <a href="{{ route('astap.create_mutasi_eksternal') }}"
                        class="w-full py-2.5 px-4 rounded-xl bg-purple-500/15 hover:bg-purple-500 text-purple-300 hover:text-white font-bold text-xs transition-all duration-200 flex items-center justify-between border border-purple-500/30 group-hover:border-purple-500">
                        <span>Input Mutasi Eksternal</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <div class="text-center">
                        <a href="{{ route('mutasi.index') }}" class="text-[10px] text-slate-500 hover:text-purple-400 transition-colors">
                            Atau buka mutasi internal ruangan &rarr;
                        </a>
                    </div>
                </div>
            </div>

            <!-- Card 5: Kemitraan dengan Pihak Ketiga (KSO / Akun 1.5.2) -->
            <div class="flex flex-col justify-between p-5 rounded-2xl bg-slate-900/95 border border-slate-800 hover:border-cyan-400/60 shadow-lg hover:shadow-cyan-400/5 transition-all duration-200 group md:col-span-2 lg:col-span-2">
                <div>
                    <!-- Header Kartu -->
                    <div class="flex items-center justify-between gap-2 mb-3">
                        <div class="w-10 h-10 rounded-xl bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-cyan-300 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-cyan-500/10 text-cyan-300 border border-cyan-500/20">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                            Aset Lainnya · Akun 1.5.2
                        </span>
                    </div>

                    <!-- Judul -->
                    <h2 class="text-base font-bold text-white group-hover:text-cyan-300 transition-colors">
                        Kemitraan Pihak Ketiga (KSO)
                    </h2>

                    <!-- Deskripsi (Kunci 2 baris seragam) -->
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed line-clamp-2 h-9">
                        Pencatatan aset kerja sama operasional (KSO), sewa, atau pemanfaatan barang milik pihak ketiga berdasarkan Perjanjian Kerja Sama (PKS).
                    </p>

                    <!-- Tags Seragam (3 Buah) -->
                    <div class="grid grid-cols-3 gap-2 mt-3 text-[10px] text-slate-300">
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            ✓ Dokumen PKS
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            🏥 Akun 1.5.2
                        </div>
                        <div class="px-2 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60 text-center font-medium truncate">
                            🤝 Mitra KSO
                        </div>
                    </div>
                </div>

                <!-- Tombol Action Penuh yang Rapi & Seragam -->
                <div class="mt-4 pt-3 border-t border-slate-800/80 space-y-2">
                    <a href="{{ route('astap.create_kemitraan') }}"
                        class="w-full py-2.5 px-4 rounded-xl bg-cyan-500/15 hover:bg-cyan-500 text-cyan-300 hover:text-slate-950 font-bold text-xs transition-all duration-200 flex items-center justify-between border border-cyan-500/30 group-hover:border-cyan-400">
                        <span>Input Aset Kemitraan</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <div class="text-center">
                        <a href="{{ route('master.reklasifikasi') }}" class="text-[10px] text-slate-500 hover:text-cyan-400 transition-colors">
                            Buka matriks reklasifikasi neraca &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-layout>
