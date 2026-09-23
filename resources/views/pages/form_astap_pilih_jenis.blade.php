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

        <!-- 4 Option Cards Grid (Symmetric, Exact Same Height & Same Elements) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Card 1: Belanja Modal (APBD / BLUD) -->
            <div class="flex flex-col justify-between p-5 rounded-2xl bg-slate-900/95 border border-slate-800 hover:border-emerald-500/60 shadow-lg hover:shadow-emerald-500/5 transition-all duration-200 group">
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

            <!-- Card 2: Hibah / Bantuan Pihak Ketiga -->
            <div class="flex flex-col justify-between p-5 rounded-2xl bg-slate-900/95 border border-slate-800 hover:border-amber-400/60 shadow-lg hover:shadow-amber-400/5 transition-all duration-200 group">
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
                        Hibah / Bantuan Pihak Ketiga
                    </h2>

                    <!-- Deskripsi (Kunci 2 baris seragam) -->
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed line-clamp-2 h-9">
                        Penerimaan barang dari Kemenkes RI, Dinkes, Pemprov, instansi luar, atau swasta berdasarkan dokumen BAST Hibah resmi.
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

            <!-- Card 3: Belanja Rekening (Barang & Jasa) -->
            <div class="flex flex-col justify-between p-5 rounded-2xl bg-slate-900/95 border border-slate-800 hover:border-indigo-500/60 shadow-lg hover:shadow-indigo-500/5 transition-all duration-200 group">
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
                            Non-Kapitalisasi (KIR)
                        </span>
                    </div>

                    <!-- Judul -->
                    <h2 class="text-base font-bold text-white group-hover:text-indigo-300 transition-colors">
                        Belanja Rekening (Barang & Jasa)
                    </h2>

                    <!-- Deskripsi (Kunci 2 baris seragam) -->
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed line-clamp-2 h-9">
                        Pencatatan barang dari rekening operasional (kode 5.1.02) untuk pengawasan fisik inventaris ruangan (KIR).
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
                            📋 Ekstrakom
                        </div>
                    </div>
                </div>

                <!-- Tombol Action Penuh yang Rapi & Seragam -->
                <div class="mt-4 pt-3 border-t border-slate-800/80">
                    <a href="{{ route('astap.create_rekening') }}"
                        class="w-full py-2.5 px-4 rounded-xl bg-indigo-500/15 hover:bg-indigo-500 text-indigo-300 hover:text-white font-bold text-xs transition-all duration-200 flex items-center justify-between border border-indigo-500/30 group-hover:border-indigo-500">
                        <span>Input Belanja Rekening</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                </div>
            </div>

            <!-- Card 4: Mutasi Masuk (Pelimpahan SKPD) -->
            <div class="flex flex-col justify-between p-5 rounded-2xl bg-slate-900/95 border border-slate-800 hover:border-purple-500/60 shadow-lg hover:shadow-purple-500/5 transition-all duration-200 group">
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
                        Mutasi Masuk (Pelimpahan SKPD)
                    </h2>

                    <!-- Deskripsi (Kunci 2 baris seragam) -->
                    <p class="text-xs text-slate-400 mt-1 leading-relaxed line-clamp-2 h-9">
                        Penerimaan atau pelimpahan aset dari SKPD/Dinas luar ke RSUD Dr. H. Koesnandi berdasarkan Berita Acara BAMB.
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
                    <a href="{{ route('astap.create_mutasi_masuk') }}"
                        class="w-full py-2.5 px-4 rounded-xl bg-purple-500/15 hover:bg-purple-500 text-purple-300 hover:text-white font-bold text-xs transition-all duration-200 flex items-center justify-between border border-purple-500/30 group-hover:border-purple-500">
                        <span>Input Mutasi Masuk</span>
                        <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </a>
                    <div class="text-center">
                        <a href="{{ route('mutasi.index') }}" class="text-[10px] text-slate-500 hover:text-purple-400 transition-colors">
                            Atau buka modul mutasi ruangan internal &rarr;
                        </a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-layout>
