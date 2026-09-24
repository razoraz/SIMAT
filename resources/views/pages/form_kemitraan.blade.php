<x-layout title="Form Input Aset Kemitraan Pihak Ketiga (KSO) - SIMAT-RK">
    @section('page-title', 'Pencatatan Aset Kemitraan (KSO)')
    @section('breadcrumb', 'Master Utama / Data ASTAP / Tambah Kemitraan Pihak Ketiga')

    <div x-data="formKemitraan()" x-cloak class="max-w-5xl mx-auto space-y-6 py-2">

        <!-- Top Header & Back -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('astap.pilih_jenis') }}"
                    class="p-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-cyan-500/50 text-slate-400 hover:text-cyan-400 transition-all shadow-lg shadow-black/20 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-white tracking-tight">
                            Pencatatan Aset Kemitraan Pihak Ketiga
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                            🤝 KSO / SEWA / BGS · AKUN 1.5.2
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pencatatan aset kerja sama operasi (KSO), sewa, atau pemanfaatan barang milik pihak ketiga sesuai standar Permendagri No. 108 Tahun 2016.
                    </p>
                </div>
            </div>

            <!-- Quick Link to Reklasifikasi -->
            <a href="{{ route('master.reklasifikasi') }}"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-900 border border-slate-800 hover:border-cyan-500/40 text-xs font-bold text-cyan-400 hover:text-cyan-300 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                <span>Lihat Matriks Reklasifikasi Neraca</span>
            </a>
        </div>

        <!-- Stepper Navigation Bar (3 Langkah Bersih) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl">
            <div class="grid grid-cols-3 gap-3 sm:gap-4">
                
                <!-- Step 1 Tab -->
                <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 1 ? 'bg-cyan-500 text-slate-950 font-black shadow-lg shadow-cyan-500/30' : (step > 1 ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 1">1</span>
                            <span x-show="step > 1">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 1 ? 'text-cyan-400' : 'text-slate-500'">Langkah 1</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Dokumen PKS &amp; Mitra</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 1 ? 'bg-cyan-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 2 Tab -->
                <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 2 ? 'bg-cyan-500 text-slate-950 font-black shadow-lg shadow-cyan-500/30' : (step > 2 ? 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 2">2</span>
                            <span x-show="step > 2">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 2 ? 'text-cyan-400' : 'text-slate-500'">Langkah 2</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Kode 108 (Akun 1.5.2)</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 2 ? 'bg-cyan-500' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 3 Tab (Rincian Teknis KIB & Penempatan Ruangan) -->
                <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 3 ? 'bg-cyan-500 text-slate-950 font-black shadow-lg shadow-cyan-500/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                            <span>3</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 3 ? 'text-cyan-400' : 'text-slate-500'">Langkah 3</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Spesifikasi &amp; Ruangan</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 3 ? 'bg-cyan-500' : 'bg-slate-950'"></div>
                </button>

            </div>
        </div>

        <!-- MAIN FORM CONTAINER -->
        <form @submit.prevent="submitForm" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">

            <!-- ========================================================================= -->
            <!-- LANGKAH 1: DOKUMEN PKS & REKANAN MITRA PIHAK KETIGA                       -->
            <!-- ========================================================================= -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-xs font-bold mb-2">
                        <span>Langkah 1 dari 3</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>🤝 Dokumen Perjanjian Kerja Sama (PKS / MoU) &amp; Mitra</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Isi identitas mitra rekanan pihak ketiga dan nomor dokumen PKS sebagai legalitas pencatatan aset kerja sama.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Nama Mitra / Perusahaan Rekanan -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Nama Perusahaan Mitra / Rekanan Pihak Ketiga <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.mitra_nama" required
                            placeholder="Contoh: PT. Roche Indonesia / PT. Fresenius Medical Care / CV. Medika Nusantara..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                    </div>

                    <!-- Nomor Dokumen PKS -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Nomor Perjanjian Kerja Sama (PKS / MoU) <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.nomor_pks" required
                            placeholder="Contoh: 000.2.3.2/PKS-KSO/430.10.7/2026..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                    </div>

                    <!-- Tanggal Penandatanganan PKS -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Tanggal Penandatanganan PKS <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-datepicker x-model="formData.tanggal_pks" required
                            placeholder="dd/mm/yyyy"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                    </div>

                    <!-- Tanggal Mulai Kerjasama -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Tanggal Mulai Masa Kerjasama (KSO)
                        </label>
                        <input type="text" x-datepicker x-model="formData.tanggal_mulai"
                            placeholder="dd/mm/yyyy"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                    </div>

                    <!-- Tanggal Selesai Kerjasama -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Tanggal Berakhir Masa Kerjasama (KSO)
                        </label>
                        <input type="text" x-datepicker x-model="formData.tanggal_selesai"
                            placeholder="dd/mm/yyyy"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                    </div>

                    <!-- Tahun Perolehan & Triwulan -->
                    <div class="grid grid-cols-2 gap-3 md:col-span-2">
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">
                                Tahun Mulai / Perolehan <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" x-model.number="formData.tahun_perolehan" required min="1990" max="2100"
                                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-300 mb-1">
                                Periode Triwulan <span class="text-rose-400">*</span>
                            </label>
                            <select x-model="formData.triwulan" required
                                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                                <option value="TW I">TW I (Januari - Maret)</option>
                                <option value="TW II">TW II (April - Juni)</option>
                                <option value="TW III">TW III (Juli - September)</option>
                                <option value="TW IV">TW IV (Oktober - Desember)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Keterangan Kerja Sama -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Keterangan / Ruang Lingkup Kerja Sama
                        </label>
                        <textarea x-model="formData.kemitraan_keterangan" rows="2"
                            placeholder="Contoh: Kerja Sama Operasional (KSO) penempatan alat laboratorium analyzer dengan skema pembelian reagen..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 2: KLASIFIKASI KODE BARANG 108 (AKUN 1.5.2 KEMITRAAN)              -->
            <!-- ========================================================================= -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-xs font-bold mb-2">
                        <span>Langkah 2 dari 3</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>🔍 Klasifikasi Kode Barang 108 (Akun 1.5.2 Kemitraan)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Tentukan klasifikasi barang Permendagri 108. Sangat dianjurkan memilih sub-akun <strong>1.5.2 (Kemitraan Pihak Ketiga)</strong> agar langsung sinkron dengan Matriks Reklasifikasi Neraca.
                    </p>
                </div>

                <!-- Banner Quick Select 1.5.2 -->
                <div class="p-4 rounded-2xl bg-cyan-950/40 border border-cyan-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-xl bg-cyan-500/20 text-cyan-400 shrink-0">
                            ⚡
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Rekomendasi Akun Neraca 1.5.2</p>
                            <p class="text-[11px] text-cyan-300">Pilih cepat klasifikasi standar Kemitraan Pihak Ketiga:</p>
                        </div>
                    </div>
                    <div class="flex flex-wrap items-center gap-1.5">
                        <button type="button" @click="quickSelectKemitraan('1.5.2.01.01.02.002')"
                            class="px-2.5 py-1 rounded-lg bg-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 text-cyan-300 text-[11px] font-bold border border-cyan-500/30 transition-all">
                            Peralatan &amp; Mesin (KSO)
                        </button>
                        <button type="button" @click="quickSelectKemitraan('1.5.2.01.01.01.002')"
                            class="px-2.5 py-1 rounded-lg bg-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 text-cyan-300 text-[11px] font-bold border border-cyan-500/30 transition-all">
                            Sewa Alat/Mesin
                        </button>
                        <button type="button" @click="quickSelectKemitraan('1.5.2.01.01.02.003')"
                            class="px-2.5 py-1 rounded-lg bg-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 text-cyan-300 text-[11px] font-bold border border-cyan-500/30 transition-all">
                            Gedung/Ruangan (KSP)
                        </button>
                    </div>
                </div>

                <!-- Bagian Klasifikasi Kode 108 -->
                <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🔍 KLASIFIKASI KODE BARANG (PERMENDAGRI 108)</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <span x-show="selectedSubSub" class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20">
                            ✓ Terpilih: <span x-text="selectedSubSub?.kode"></span>
                        </span>
                    </div>

                    <!-- Search Box Filter 108 -->
                    <div class="relative">
                        <input type="text"
                            x-model="search108"
                            @input="performSearch108()"
                            placeholder="Cari nama atau kode barang (contoh: Kemitraan, Sewa, Hemodialisa, USG, Laboratorium, Analyzer)..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                        <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button type="button" x-show="search108" @click="search108 = ''; searchResults108 = []"
                            class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-white">✕</button>
                    </div>

                    <!-- Hasil Pencarian 108 -->
                    <div x-show="searchResults108.length > 0" class="max-h-48 overflow-y-auto space-y-1 p-2 bg-slate-900 rounded-xl border border-cyan-500/30 custom-scrollbar">
                        <template x-for="item in searchResults108" :key="item.id">
                            <button type="button" @click="selectFromSearch(item)"
                                class="w-full text-left p-2 rounded-lg hover:bg-cyan-500/20 text-xs flex items-center justify-between group transition-colors">
                                <div class="truncate mr-2">
                                    <span class="font-mono text-cyan-300 font-bold" x-text="item.kode"></span>
                                    <span class="text-white ml-2" x-text="item.nama"></span>
                                </div>
                                <span class="text-[10px] text-slate-400 bg-slate-800 px-2 py-0.5 rounded group-hover:bg-cyan-500 group-hover:text-slate-950 font-bold shrink-0">Pilih</span>
                            </button>
                        </template>
                    </div>

                    <!-- Cascading Dropdowns -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">1. Kelompok Akun / KIB</label>
                            <select x-model="selectedJenisIdx" @change="onJenisChange()"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                                <option value="">-- Pilih Kelompok Akun --</option>
                                <template x-for="(j, idx) in master108" :key="j.id">
                                    <option :value="idx" x-text="j.kode + ' - ' + j.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">2. Objek Barang</label>
                            <select x-model="selectedSubIdx" @change="onSubChange()" :disabled="!currentSubList.length"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 disabled:opacity-40">
                                <option value="">-- Pilih Objek --</option>
                                <template x-for="(s, idx) in currentSubList" :key="s.id">
                                    <option :value="idx" x-text="s.kode + ' - ' + s.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-[11px] font-bold text-slate-300 mb-1">3. Sub-Sub Rincian Objek (Detail Kode 108)</label>
                            <select @change="onSubSubChange($event)" :disabled="!currentSubSubList.length"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400 disabled:opacity-40 font-mono">
                                <option value="">-- Pilih Sub-Sub Rincian Objek --</option>
                                <template x-for="ss in currentSubSubList" :key="ss.id">
                                    <option :value="ss.id" :selected="selectedSubSub && selectedSubSub.id === ss.id" x-text="ss.kode + ' - ' + ss.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Input Nama Barang, Volume, Satuan, Total Taksiran Nilai -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Nama Spesifik Barang Kemitraan / KSO <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.nama_barang" required
                            placeholder="Contoh: Automated Clinical Chemistry Analyzer Cobas c311..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Jumlah Volume / Unit <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" x-model.number="formData.jumlah_volume" required min="1"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Satuan Barang <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.satuan" required
                            placeholder="Unit / Set / Buah..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Total Taksiran Nilai Wajar Aset Kemitraan (Rp) <span class="text-rose-400">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-xs font-bold text-slate-400 font-mono">Rp</span>
                            <input type="number" step="0.01" x-model.number="formData.total_realisasi" required min="0"
                                placeholder="0"
                                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 pl-11 text-xs text-cyan-300 font-mono font-bold focus:outline-none transition-all">
                        </div>
                        <p class="text-[11px] text-slate-400 mt-1">
                            Nilai aset sesuai taksiran kontrak PKS untuk dilaporkan pada baris neraca <em>1.5.2 Kemitraan dengan Pihak Ketiga</em>.
                        </p>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 3: SPESIFIKASI TEKNIS & RUANGAN PENEMPATAN                        -->
            <!-- ========================================================================= -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-xs font-bold mb-2">
                        <span>Langkah 3 dari 3</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>🏥 Lokasi Penempatan Ruangan &amp; Spesifikasi Teknis</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Tentukan ruangan penanggung jawab dan rincian fisik alat medis / mesin untuk register NIBAR dan label QR Code.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Unit / Ruangan Penempatan -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Unit / Ruangan Penempatan <span class="text-rose-400">*</span>
                        </label>
                        <select x-model="formData.unit_id" required
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <option value="">-- Pilih Unit / Ruangan --</option>
                            @foreach ($dbUnits as $u)
                                <option value="{{ $u->id }}">{{ $u->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kondisi Fisik Barang -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Kondisi Fisik Saat Diterima <span class="text-rose-400">*</span>
                        </label>
                        <select x-model="formData.kondisi" required
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <option value="Baik">🟢 Baik (Operasional Normal)</option>
                            <option value="Rusak Ringan">🟡 Rusak Ringan</option>
                            <option value="Rusak Berat">🔴 Rusak Berat</option>
                        </select>
                    </div>

                    <!-- Alamat Lokasi Barang -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold text-slate-300 mb-1">
                            Alamat Lokasi Barang
                        </label>
                        <input type="text" x-model="formData.alamat_barang"
                            placeholder="RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    </div>

                    <!-- Spesifikasi Alat / Mesin: Merk, Type, No Seri -->
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Merk / Brand</label>
                        <input type="text" x-model="formData.merk" placeholder="Contoh: Roche / Siemens / Fresenius..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Tipe / Model</label>
                        <input type="text" x-model="formData.type" placeholder="Contoh: Cobas e411 / 4008S / Multix..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Nomor Pabrik / Seri (Serial Number)</label>
                        <input type="text" x-model="formData.no_pabrik" placeholder="Contoh: SN-892301982..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-300 mb-1">Bahan / Material</label>
                        <input type="text" x-model="formData.bahan" placeholder="Contoh: Logam, Elektronik, Plastik..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    </div>
                </div>

                <!-- Ringkasan Singkat Kartu Aset Kemitraan Sebelum Simpan -->
                <div class="p-4 rounded-2xl bg-cyan-950/30 border border-cyan-500/20 space-y-2">
                    <span class="text-[11px] font-bold text-cyan-400 uppercase tracking-wider block">Ringkasan Aset Kemitraan:</span>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-xs text-slate-300">
                        <div>
                            <span class="text-slate-500 block text-[10px]">Mitra Pihak Ketiga:</span>
                            <span class="font-bold text-white truncate block" x-text="formData.mitra_nama || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px]">No. PKS:</span>
                            <span class="font-bold text-white truncate block" x-text="formData.nomor_pks || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px]">Jumlah Unit:</span>
                            <span class="font-bold text-white block" x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px]">Total Taksiran:</span>
                            <span class="font-bold text-cyan-300 font-mono block" x-text="'Rp ' + Number(formData.total_realisasi || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- STEPPER CONTROLS & TOMBOL NAVIGASI                                        -->
            <!-- ========================================================================= -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-between">
                <div>
                    <button type="button" x-show="step > 1" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all flex items-center space-x-1.5 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                        <span>Kembali</span>
                    </button>
                </div>

                <div class="flex items-center space-x-3">
                    <button type="button" x-show="step < 3" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 text-xs font-black transition-all flex items-center space-x-1.5 shadow-lg shadow-cyan-500/20 cursor-pointer">
                        <span>Lanjutkan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>

                    <button type="submit" x-show="step === 3" :disabled="isSubmitting"
                        class="px-8 py-2.5 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-600 hover:from-cyan-400 hover:to-blue-500 text-white text-xs font-black transition-all shadow-xl shadow-cyan-500/30 flex items-center space-x-2 disabled:opacity-50 cursor-pointer">
                        <span x-show="!isSubmitting">💾 Simpan Aset Kemitraan</span>
                        <span x-show="isSubmitting">Menyimpan ke Database...</span>
                    </button>
                </div>
            </div>

        </form>

    </div>

    <!-- Alpine Script Logika Form Kemitraan -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('formKemitraan', () => ({
                step: 1,
                isSubmitting: false,

                // Form Data Payload
                formData: {
                    mitra_nama: '',
                    nomor_pks: '',
                    tanggal_pks: '{{ date('d/m/Y') }}',
                    tanggal_mulai: '',
                    tanggal_selesai: '',
                    tahun_perolehan: {{ date('Y') }},
                    triwulan: 'TW I',
                    kemitraan_keterangan: '',
                    
                    nama_barang: '',
                    jenis_astap_id: null,
                    jumlah_volume: 1,
                    satuan: 'Unit',
                    total_realisasi: 0,
                    
                    unit_id: '',
                    kondisi: 'Baik',
                    alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                    merk: '',
                    type: '',
                    no_pabrik: '',
                    bahan: 'Logam / Elektronik Medis',
                },

                // Master 108 Data dari Server
                master108Raw: @json($dbMaster108),
                master108: [],
                flat108: [],
                search108: '',
                searchResults108: [],

                selectedJenisIdx: '',
                selectedSubIdx: '',
                currentSubList: [],
                currentSubSubList: [],
                selectedSubSub: null,

                init() {
                    this.prepareMaster108();
                },

                prepareMaster108() {
                    const parsed = [];
                    const flat = [];

                    Object.entries(this.master108Raw || {}).forEach(([jenisKode, jObj]) => {
                        if (!jObj || typeof jObj !== 'object') return;
                        const subList = [];
                        Object.entries(jObj).forEach(([subKode, sObj]) => {
                            if (!sObj || !Array.isArray(sObj)) return;
                            const ssList = [];
                            sObj.forEach(item => {
                                const entry = {
                                    id: item.id,
                                    kode: item.kode,
                                    nama: item.nama
                                };
                                ssList.push(entry);
                                flat.push({
                                    ...entry,
                                    jenisKode: jenisKode,
                                    subKode: subKode
                                });
                            });
                            subList.push({
                                kode: subKode,
                                nama: sObj[0]?.nama_sub || subKode,
                                subSubs: ssList
                            });
                        });
                        parsed.push({
                            kode: jenisKode,
                            nama: jObj[Object.keys(jObj)[0]]?.[0]?.nama_jenis || ('Kelompok ' + jenisKode),
                            subs: subList
                        });
                    });

                    this.master108 = parsed;
                    this.flat108 = flat;
                },

                // Pencarian cepat
                performSearch108() {
                    const q = (this.search108 || '').trim().toLowerCase();
                    if (!q || q.length < 2) {
                        this.searchResults108 = [];
                        return;
                    }
                    this.searchResults108 = this.flat108.filter(it => 
                        it.kode.toLowerCase().includes(q) || it.nama.toLowerCase().includes(q)
                    ).slice(0, 15);
                },

                selectFromSearch(item) {
                    this.selectedSubSub = item;
                    this.formData.jenis_astap_id = item.id;
                    if (!this.formData.nama_barang) {
                        this.formData.nama_barang = item.nama;
                    }
                    this.search108 = '';
                    this.searchResults108 = [];
                },

                // Quick select 1.5.2 Kemitraan
                quickSelectKemitraan(prefixKode) {
                    const found = this.flat108.find(it => it.kode.startsWith(prefixKode));
                    if (found) {
                        this.selectFromSearch(found);
                    } else {
                        // Fallback: cari yang ada kata kunci 'Kemitraan' atau '1.5.2'
                        const fallback = this.flat108.find(it => it.kode.startsWith('1.5.2') || it.nama.toLowerCase().includes('kemitraan'));
                        if (fallback) this.selectFromSearch(fallback);
                    }
                },

                onJenisChange() {
                    if (this.selectedJenisIdx === '') {
                        this.currentSubList = [];
                        this.currentSubSubList = [];
                        this.selectedSubIdx = '';
                        return;
                    }
                    this.currentSubList = this.master108[this.selectedJenisIdx]?.subs || [];
                    this.currentSubSubList = [];
                    this.selectedSubIdx = '';
                },

                onSubChange() {
                    if (this.selectedSubIdx === '') {
                        this.currentSubSubList = [];
                        return;
                    }
                    this.currentSubSubList = this.currentSubList[this.selectedSubIdx]?.subSubs || [];
                },

                onSubSubChange(e) {
                    const id = parseInt(e.target.value);
                    const it = this.flat108.find(x => x.id === id);
                    if (it) {
                        this.selectFromSearch(it);
                    }
                },

                goToStep(s) {
                    if (s > this.step) {
                        if (!this.validateStep(this.step)) return;
                    }
                    this.step = s;
                },

                nextStep() {
                    if (this.validateStep(this.step)) {
                        this.step++;
                    }
                },

                prevStep() {
                    if (this.step > 1) {
                        this.step--;
                    }
                },

                validateStep(s) {
                    if (s === 1) {
                        if (!this.formData.mitra_nama.trim()) {
                            alert('Mohon isi nama perusahaan mitra/rekanan pihak ketiga.');
                            return false;
                        }
                        if (!this.formData.nomor_pks.trim()) {
                            alert('Mohon isi nomor dokumen Perjanjian Kerja Sama (PKS).');
                            return false;
                        }
                        if (!this.formData.tanggal_pks) {
                            alert('Mohon isi tanggal dokumen PKS.');
                            return false;
                        }
                    } else if (s === 2) {
                        if (!this.formData.jenis_astap_id) {
                            alert('Mohon pilih klasifikasi kode barang 108 (rekomendasi akun 1.5.2 Kemitraan).');
                            return false;
                        }
                        if (!this.formData.nama_barang.trim()) {
                            alert('Mohon isi nama spesifik barang kemitraan/KSO.');
                            return false;
                        }
                        if (this.formData.jumlah_volume < 1) {
                            alert('Jumlah volume barang minimal 1 unit.');
                            return false;
                        }
                        if (!this.formData.total_realisasi || this.formData.total_realisasi <= 0) {
                            alert('Mohon masukkan total taksiran nilai wajar aset kemitraan.');
                            return false;
                        }
                    }
                    return true;
                },

                async submitForm() {
                    if (!this.validateStep(1) || !this.validateStep(2)) return;
                    if (!this.formData.unit_id) {
                        alert('Mohon pilih unit / ruangan penempatan barang.');
                        return;
                    }

                    this.isSubmitting = true;

                    try {
                        const res = await fetch('{{ route('astap.store_kemitraan') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const json = await res.json();

                        if (res.ok && json.success) {
                            alert(json.message || 'Data Aset Kemitraan berhasil disimpan!');
                            window.location.href = json.redirect || '{{ route('astap.index') }}';
                        } else {
                            alert(json.message || 'Terjadi kesalahan saat menyimpan data aset kemitraan.');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Terjadi kesalahan jaringan atau server saat menyimpan data.');
                    } finally {
                        this.isSubmitting = false;
                    }
                }
            }));
        });
    </script>
</x-layout>
