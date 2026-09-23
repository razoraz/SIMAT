<x-layout title="Form Input Aset Hibah - SIMAT-RK">
    @section('page-title', 'Pencatatan Aset Hibah')
    @section('breadcrumb', 'Master Utama / Data ASTAP / Tambah Hibah')

    <div x-data="formHibah()" x-cloak class="max-w-5xl mx-auto space-y-6 py-2">

        <!-- Top Header & Back -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('astap.pilih_jenis') }}"
                    class="p-2.5 rounded-2xl bg-slate-900 border border-slate-800 hover:border-amber-500/50 text-slate-400 hover:text-amber-400 transition-all shadow-lg shadow-black/20 group">
                    <svg class="w-5 h-5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <div class="flex items-center gap-2">
                        <h1 class="text-2xl font-black text-white tracking-tight">
                            Pencatatan Aset Hibah
                        </h1>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-400/15 text-amber-300 border border-amber-400/30">
                            🎁 HIBAH
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pendaftaran aset perolehan hibah / bantuan dari pihak ketiga tanpa pagu belanja modal APBD.
                    </p>
                </div>
            </div>
        </div>

        <!-- Stepper Navigation Bar (4 Langkah Sesuai Standar SIMAT-RK) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-2xl sm:rounded-3xl p-4 sm:p-5 shadow-xl">
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                
                <!-- Step 1 Tab -->
                <button type="button" @click="goToStep(1)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 1 ? 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30' : (step > 1 ? 'bg-amber-400/20 text-amber-400 border border-amber-400/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 1">1</span>
                            <span x-show="step > 1">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 1 ? 'text-amber-400' : 'text-slate-500'">Langkah 1</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">BAST &amp; Pemberi</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 1 ? 'bg-amber-400' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 2 Tab -->
                <button type="button" @click="goToStep(2)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 2 ? 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30' : (step > 2 ? 'bg-amber-400/20 text-amber-400 border border-amber-400/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 2">2</span>
                            <span x-show="step > 2">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 2 ? 'text-amber-400' : 'text-slate-500'">Langkah 2</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">Klasifikasi 108</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 2 ? 'bg-amber-400' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 3 Tab (Rincian Teknis KIB) -->
                <button type="button" @click="goToStep(3)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 3 ? 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30' : (step > 3 ? 'bg-amber-400/20 text-amber-400 border border-amber-400/40' : 'bg-slate-950 text-slate-500 border border-slate-800')">
                            <span x-show="step <= 3">3</span>
                            <span x-show="step > 3">✓</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 3 ? 'text-amber-400' : 'text-slate-500'">Langkah 3</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate" x-text="kibLabel"></span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 3 ? 'bg-amber-400' : 'bg-slate-950'"></div>
                </button>

                <!-- Step 4 Tab -->
                <button type="button" @click="goToStep(4)" class="text-left group cursor-pointer p-2 sm:p-0 rounded-xl hover:bg-slate-800/40 transition-all">
                    <div class="flex items-center space-x-2 sm:space-x-3 mb-1.5 sm:mb-2">
                        <div class="w-8 h-8 rounded-xl font-bold text-xs flex items-center justify-center transition-all shrink-0"
                             :class="step === 4 ? 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30' : 'bg-slate-950 text-slate-500 border border-slate-800'">
                            <span>4</span>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider block truncate" :class="step === 4 ? 'text-amber-400' : 'text-slate-500'">Langkah 4</span>
                            <span class="text-[11px] sm:text-xs font-bold text-white block truncate">PPK &amp; Konfirmasi</span>
                        </div>
                    </div>
                    <div class="h-1 sm:h-1.5 rounded-full w-full transition-all" :class="step >= 4 ? 'bg-amber-400' : 'bg-slate-950'"></div>
                </button>

            </div>
        </div>

        <!-- MAIN FORM CONTAINER -->
        <form @submit.prevent="submitForm" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">

            <!-- ========================================================================= -->
            <!-- LANGKAH 1: DOKUMEN BAST HIBAH & PIHAK PEMBERI                             -->
            <!-- ========================================================================= -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-300 border border-amber-400/20 text-xs font-bold mb-2">
                        <span>Langkah 1 dari 4</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>📜 Dokumen BAST &amp; Informasi Pihak Pemberi Hibah</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Masukkan legalitas penyerahan (BAST Hibah), periode pembukuan, nama pihak pemberi hibah, serta total nilai taksiran/perolehan hibah.
                    </p>
                </div>

                <!-- Periode Anggaran & Triwulan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-950/70 border border-slate-800">
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Tahun Anggaran Pembukuan <span class="text-rose-400">*</span>
                        </label>
                        <input type="number" x-model.number="formData.tahun_perolehan" min="1990" max="2100" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-mono">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Triwulan Pembukuan <span class="text-rose-400">*</span>
                        </label>
                        <select x-model="formData.triwulan" required
                            class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <option value="TW I">Triwulan I (Januari - Maret)</option>
                            <option value="TW II">Triwulan II (April - Juni)</option>
                            <option value="TW III">Triwulan III (Juli - September)</option>
                            <option value="TW IV">Triwulan IV (Oktober - Desember)</option>
                        </select>
                    </div>
                </div>

                <!-- Bagian BAST & Pemberi Hibah -->
                <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-400/30 space-y-4 shadow-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🎁 Informasi Pihak Pemberi &amp; BAST Hibah</span>
                            <span class="text-rose-400">*</span>
                        </span>
                        <span class="text-[10px] font-bold text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded border border-amber-400/20">
                            Tanpa Rekening Belanja APBD
                        </span>
                    </div>

                    <!-- Pihak Pemberi Hibah -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Instansi / Lembaga Pemberi Hibah <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.hibah_pemberi" required
                            placeholder="Contoh: Kementerian Kesehatan RI, Dinas Kesehatan Provinsi Jawa Timur..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none font-bold">
                        
                        <!-- Rekomendasi Cepat Pemberi Hibah -->
                        <div class="mt-2 flex flex-wrap items-center gap-1.5">
                            <span class="text-[10px] text-slate-500 font-semibold mr-1">Rekomendasi Cepat:</span>
                            <button type="button" @click="formData.hibah_pemberi = 'Kementerian Kesehatan Republik Indonesia'"
                                class="px-2.5 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700">
                                Kemenkes RI
                            </button>
                            <button type="button" @click="formData.hibah_pemberi = 'Dinas Kesehatan Provinsi Jawa Timur'"
                                class="px-2.5 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700">
                                Dinkes Prov. Jatim
                            </button>
                            <button type="button" @click="formData.hibah_pemberi = 'Pemerintah Kabupaten Bondowoso'"
                                class="px-2.5 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700">
                                Pemkab Bondowoso
                            </button>
                            <button type="button" @click="formData.hibah_pemberi = 'Donatur Swasta / Yayasan'"
                                class="px-2.5 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700">
                                Donatur Swasta
                            </button>
                        </div>
                    </div>

                    <!-- Nomor & Tanggal BAST -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Nomor BAST Hibah <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" x-model="formData.hibah_nomor_bast" required
                                placeholder="Contoh: 028/BAST-HB/KEMENKES/2026"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Tanggal BAST Hibah <span class="text-rose-400">*</span>
                            </label>
                            <input type="date" x-model="formData.hibah_tanggal_bast" required
                                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                        </div>
                    </div>

                    <!-- Nilai Perolehan Realisasi (Rp) -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                            <span>Total Nilai Perolehan / Taksiran Hibah (Rp) <span class="text-rose-400">*</span></span>
                            <span class="text-[11px] font-mono text-amber-400" x-text="formatRupiah(formData.total_realisasi)"></span>
                        </label>
                        <input type="number" x-model.number="formData.total_realisasi" min="0" step="any" required
                            placeholder="0"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-amber-300 font-bold font-mono focus:outline-none">
                        <p class="text-[10.5px] text-slate-400 mt-1">
                            Masukkan taksiran wajar atau nilai yang tertera pada naskah perjanjian hibah / BAST.
                        </p>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 2: KLASIFIKASI KODE BARANG 108 (PERMENDAGRI 108)                  -->
            <!-- ========================================================================= -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-xs font-bold mb-2">
                        <span>Langkah 2 dari 4</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>🔍 Klasifikasi Kode Barang (Permendagri 108)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Tentukan kelompok KIB (Tanah, Peralatan &amp; Mesin, Gedung, dll.), sub rincian objek, dan nama spesifik aset.
                    </p>
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
                            placeholder="Ketik nama atau kode barang (contoh: Tanah, Gedung, USG, Bed Pasien, Ambulance, Laptop, Meja)..."
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
                            <div @click="select108Direct(item)"
                                class="p-2.5 rounded-lg bg-slate-950 hover:bg-cyan-500/10 border border-slate-800 hover:border-cyan-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                <div class="min-w-0 pr-3">
                                    <div class="text-xs font-bold text-white group-hover:text-cyan-300" x-text="item.nama"></div>
                                    <div class="text-[10px] text-slate-400" x-text="item.kode + ' • ' + item.parentNama"></div>
                                </div>
                                <span class="text-[11px] font-bold text-cyan-400 shrink-0">Pilih →</span>
                            </div>
                        </template>
                    </div>

                    <!-- Cascading Dropdowns Fallback -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3 pt-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Kelompok KIB</label>
                            <select x-model="selectedJenisIdx" @change="onJenisChange()"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                <option value="">-- Pilih Kelompok KIB --</option>
                                <template x-for="(j, idx) in master108" :key="j.kode">
                                    <option :value="idx" x-text="j.kode + ' - ' + j.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Sub Rincian</label>
                            <select x-model="selectedSubIdx" @change="onSubChange()" :disabled="!currentSubList.length"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                                <option value="">-- Pilih Sub Rincian --</option>
                                <template x-for="(s, idx) in currentSubList" :key="s.kode">
                                    <option :value="idx" x-text="s.kode + ' - ' + s.nama"></option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Objek Spesifik 108</label>
                            <select x-model="formData.jenis_astap_id" @change="onSubSubChange()" :disabled="!currentSubSubList.length"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                                <option value="">-- Pilih Objek 108 --</option>
                                <template x-for="ss in currentSubSubList" :key="ss.id">
                                    <option :value="ss.id" x-text="ss.kode + ' - ' + ss.nama"></option>
                                </template>
                            </select>
                        </div>
                    </div>

                    <!-- Nama Barang Lengkap & Satuan -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
                        <div class="sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Nama Lengkap Barang / Merk / Tipe <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" x-model="formData.nama_barang" required
                                placeholder="Contoh: USG Mindray DC-30 Color Doppler Portable SN: 88291..."
                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Satuan Barang <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" x-model="formData.satuan" required
                                placeholder="Unit / Bidang / M2"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-semibold">
                        </div>
                    </div>

                    <!-- Status Preview KIB Terdeteksi -->
                    <div class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 flex items-center justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="text-base" x-text="kibBadgeIcon"></span>
                            <div>
                                <span class="text-xs font-bold text-white" x-text="'Kelompok Terdeteksi: ' + kibLabel"></span>
                                <p class="text-[10.5px] text-slate-400" x-text="isTanah ? 'Form spesifikasi tanah (KIB A) akan otomatis tampil pada Langkah 3.' : 'Form rincian spesifikasi teknis barang akan otomatis menyesuaikan pada Langkah 3.'"></p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-lg text-[10px] font-extrabold uppercase border"
                              :class="isTanah ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : 'bg-cyan-500/15 text-cyan-300 border-cyan-500/30'"
                              x-text="isTanah ? 'KIB A (Tanah)' : (isGedung ? 'KIB C (Gedung)' : (isJaringan ? 'KIB D (Jaringan)' : (isAsetLainnya ? 'KIB E (Lainnya)' : (isAtb ? 'ATB' : 'KIB B (Peralatan)'))))"></span>
                    </div>

                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 3: RINCIAN SPESIFIKASI TEKNIS ASET (KIB A - F, ATB)               -->
            <!-- ========================================================================= -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2"
                         :class="isTanah ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-teal-500/10 text-teal-300 border border-teal-500/20'">
                        <span>Langkah 3 dari 4</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span x-text="kibBadgeIcon"></span>
                        <span x-text="'⚙️ ' + kibLabel"></span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        <span x-show="isTanah">Lengkapi rincian sertifikat, hak pakai/pengelolaan, luas tanah, kondisi, dan lokasi fisik bidang tanah perolehan hibah.</span>
                        <span x-show="!isTanah">Lengkapi atribut fisik barang sesuai kelompok KIB (Merk, Tipe, No Seri Pabrik, Dimensi, Bahan, Nomor Rangka/Mesin, dll.).</span>
                    </p>
                </div>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI A: FORM SPESIFIKASI TANAH (KIB A)                             -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isTanah">
                    <div class="space-y-5">
                        
                        <!-- Header Pembungkus Bidang Tanah -->
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 shadow-md">
                            <div class="space-y-0.5">
                                <div class="flex items-center space-x-2">
                                    <span class="p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 text-sm">🌾</span>
                                    <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                        RINCIAN BIDANG TANAH (<span class="text-emerald-400" x-text="formData.tanah_items.length"></span> Bidang Terdaftar)
                                    </h3>
                                </div>
                                <p class="text-[11px] text-slate-400">
                                    Total Luas: <strong class="text-cyan-300" x-text="totalLuasTanah.toLocaleString('id-ID') + ' m²'"></strong> • Setiap bidang tanah memiliki rincian sertifikat, luas, kondisi, dan lokasi fisik masing-masing.
                                </p>
                            </div>
                            <button type="button" @click="addTanahItem()" 
                                    class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-emerald-500/20 shrink-0 cursor-pointer">
                                <span>➕ Tambah Bidang Tanah</span>
                            </button>
                        </div>

                        <!-- List Kartu Bidang Tanah (Repeater) -->
                        <div class="space-y-5">
                            <template x-for="(item, idx) in formData.tanah_items" :key="idx">
                                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 hover:border-emerald-500/60 transition-all space-y-4 shadow-xl relative group">
                                    
                                    <!-- Header Kartu Tiap Bidang Tanah -->
                                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-mono font-extrabold text-xs border border-emerald-500/40 flex items-center space-x-1.5">
                                                <span>🌾 Bidang Tanah #<span x-text="idx + 1"></span></span>
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                • Luas: <strong class="text-cyan-300" x-text="(item.tanah_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                                            </span>
                                            <span class="text-[11px] text-slate-400 font-mono">
                                                • Hak: <strong class="text-amber-300" x-text="item.tanah_hak || 'Hak Pakai'"></strong>
                                            </span>
                                        </div>

                                        <!-- Tombol Hapus Bidang (Muncul jika > 1 item) -->
                                        <button type="button" 
                                                x-show="formData.tanah_items.length > 1" 
                                                @click="removeTanahItem(idx)" 
                                                class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                            <span>🗑️ Hapus Bidang Ini</span>
                                        </button>
                                    </div>

                                    <!-- Grid Status Sertifikat & Kondisi/Luas -->
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                        <!-- Status Tanah & Sertifikat -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📜 Status Tanah &amp; Sertifikat:</span>
                                                </span>
                                            </div>
                                            <div>
                                                <label class="block text-slate-400 text-[11px] mb-1 font-semibold">Hak Tanah</label>
                                                <select x-model="item.tanah_hak" @change="syncTanahFields()"
                                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                    <option value="Hak Pakai">Hak Pakai</option>
                                                    <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                                    <option value="Hak Milik">Hak Milik</option>
                                                    <option value="Lainnya">Lainnya</option>
                                                </select>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Nomor</label>
                                                    <input type="text" x-model="item.tanah_sertifikat_no" @input="syncTanahFields()" placeholder="HP-108/1984"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Tanggal</label>
                                                    <input type="date" x-model="item.tanah_sertifikat_tgl" @change="syncTanahFields()"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-amber-500">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kondisi, Penggunaan & Volume -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📐 Kondisi, Penggunaan &amp; Volume:</span>
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Kondisi (B/KB/RB)</label>
                                                    <select x-model="item.tanah_kondisi" @change="syncTanahFields()"
                                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                                        <option value="Baik">Baik (B)</option>
                                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Jumlah Bidang</label>
                                                    <input type="number" min="1" x-model.number="item.tanah_jumlah_bidang" @input="syncTanahFields()"
                                                           placeholder="1"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-cyan-500">
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Luas Tanah (m²)</label>
                                                    <input type="number" min="0" step="any" x-model.number="item.tanah_luas_m2" 
                                                           placeholder="Contoh: 35400"
                                                           class="w-full bg-slate-950 border border-cyan-500/40 rounded-xl px-2.5 py-2 text-xs text-cyan-300 font-mono font-bold focus:border-cyan-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Penggunaan Lahan</label>
                                                    <input type="text" x-model="item.tanah_penggunaan" placeholder="Fasilitas RSUD"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                    <!-- Letak / Alamat Bidang Tanah -->
                                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-2">
                                        <div class="flex items-center justify-between border-b border-amber-500/20 pb-1.5">
                                            <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-1.5">
                                                <span>📍 Letak / Alamat Tanah &amp; Lokasi Fisik:</span>
                                            </label>
                                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Fisik Bidang #<span x-text="idx + 1"></span></span>
                                        </div>
                                        <input type="text" x-model="item.tanah_alamat" @input="syncTanahFields()"
                                            placeholder="Contoh: Jl. Piere Tendean No. 3, Kel. Badean, Kec. Bondowoso (Area Paviliun RSUD Dr. H. Koesnandi)"
                                            class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-medium focus:outline-none focus:border-amber-500 transition-all">
                                    </div>

                                </div>
                            </template>
                        </div>

                        <!-- Tombol Tambah Bidang Baru (Dashed) -->
                        <button type="button" @click="addTanahItem()" 
                                class="w-full py-3.5 border-2 border-dashed border-emerald-500/50 hover:border-emerald-400 bg-emerald-950/20 hover:bg-emerald-950/40 text-emerald-300 hover:text-emerald-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                            <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                            <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Bidang Tanah Lainnya</span>
                        </button>

                    </div>
                </template>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI B: FORM PERALATAN DAN MESIN (KIB B)                           -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isMesin">
                    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Merk / Brand</label>
                                <input type="text" x-model="formData.merk" placeholder="Contoh: Mindray / GE / Samsung / Toyota"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Tipe / Model</label>
                                <input type="text" x-model="formData.type" placeholder="Contoh: DC-30 Color Doppler Portable"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Nomor Pabrik / Serial Number (SN)</label>
                                <input type="text" x-model="formData.no_pabrik" placeholder="Nomor seri pabrikan barang"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Ukuran / Dimensi / Kapasitas</label>
                                <input type="text" x-model="formData.ukuran" placeholder="Contoh: 120 x 80 x 75 cm / 500 Watt"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Bahan / Material</label>
                                <input type="text" x-model="formData.bahan" placeholder="Contoh: Stainless Steel / Plastik ABS / Besi"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Kondisi Fisik Saat Diterima</label>
                                <select x-model="formData.kondisi"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Baik">Baik (Baru / Berfungsi Optimal)</option>
                                    <option value="Kurang Baik">Kurang Baik</option>
                                    <option value="Rusak Ringan">Rusak Ringan</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>

                        <!-- Detail Tambahan Kendaraan (Opsional) -->
                        <div class="pt-3 border-t border-slate-800/80 space-y-3">
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                                Legalitas Kendaraan / Atribut Mesin (Jika Relevan):
                            </span>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] text-slate-300 mb-1">Nomor Rangka</label>
                                    <input type="text" x-model="formData.no_rangka" placeholder="No Rangka (Opsional)"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-300 mb-1">Nomor Mesin</label>
                                    <input type="text" x-model="formData.no_mesin" placeholder="No Mesin (Opsional)"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[11px] text-slate-300 mb-1">Nomor Polisi / Plat</label>
                                    <input type="text" x-model="formData.no_polisi" placeholder="Contoh: P 1234 AP"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI C: FORM GEDUNG DAN BANGUNAN (KIB C)                           -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isGedung">
                    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Luas Lantai (m²)</label>
                                <input type="number" min="0" step="any" x-model="formData.gedung_luas_m2" placeholder="Contoh: 450"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Kondisi Bangunan</label>
                                <select x-model="formData.kondisi"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Baik">Baik</option>
                                    <option value="Kurang Baik">Kurang Baik</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Konstruksi Bertingkat</label>
                                <select x-model="formData.gedung_bertingkat"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Tidak">Tidak Bertingkat (1 Lantai)</option>
                                    <option value="Bertingkat">Bertingkat</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Konstruksi Beton</label>
                                <select x-model="formData.gedung_beton"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Beton">Beton</option>
                                    <option value="Bukan Beton">Bukan Beton</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Nomor Dokumen IMB / PBG</label>
                                <input type="text" x-model="formData.gedung_dokumen_no" placeholder="Contoh: 503/IMB/2026"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Status Kepemilikan Tanah</label>
                                <input type="text" x-model="formData.gedung_status_tanah" placeholder="Tanah Milik Pemda / RSUD"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI D: FORM JALAN, IRIGASI & JARINGAN (KIB D)                     -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isJaringan">
                    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Panjang (m)</label>
                                <input type="number" min="0" step="any" x-model="formData.jaringan_panjang_m" placeholder="Contoh: 150"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Lebar (m)</label>
                                <input type="number" min="0" step="any" x-model="formData.jaringan_lebar_m" placeholder="Contoh: 4"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Luas Total (m²)</label>
                                <input type="number" min="0" step="any" x-model="formData.jaringan_luas_m2" placeholder="Contoh: 600"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-200 mb-1">Bahan / Konstruksi</label>
                                <input type="text" x-model="formData.jaringan_konstruksi" placeholder="Aspal Hotmix / Paving / Pipa HDPE"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Kondisi</label>
                                <select x-model="formData.kondisi"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Baik">Baik</option>
                                    <option value="Kurang Baik">Kurang Baik</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI E: FORM ASET TETAP LAINNYA (KIB E)                            -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isAsetLainnya">
                    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Judul Buku / Nama Tanaman / Hewan</label>
                                <input type="text" x-model="formData.lainnya_judul" placeholder="Contoh: Buku Panduan Medis / Pohon Hias"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Pencipta / Asal Usul</label>
                                <input type="text" x-model="formData.lainnya_asal" placeholder="Pengarang / Pembibitan"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Spesifikasi / Ukuran</label>
                                <input type="text" x-model="formData.lainnya_spesifikasi" placeholder="Hardcover / Tinggi 2m"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Kondisi</label>
                                <select x-model="formData.kondisi"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Baik">Baik</option>
                                    <option value="Kurang Baik">Kurang Baik</option>
                                    <option value="Rusak Berat">Rusak Berat</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI ATB: ASET TIDAK BERWUJUD                                      -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isAtb">
                    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Jenis Aset Tak Berwujud</label>
                                <select x-model="formData.atb_jenis"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Software / Aplikasi Sistem Informasi">Software / Aplikasi SIMRS</option>
                                    <option value="Lisensi / Hak Cipta">Lisensi / Hak Cipta</option>
                                    <option value="Kajian Medis & Dokumen Strategis">Kajian Medis &amp; Studi</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Nama Aplikasi / Lisensi</label>
                                <input type="text" x-model="formData.atb_nama_aplikasi" placeholder="Contoh: Sistem E-Rekam Medis Cloud v3"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-bold">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Masa Manfaat (Tahun)</label>
                                <input type="number" min="1" max="20" x-model.number="formData.atb_masa_manfaat" placeholder="4"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Keterangan / Versi</label>
                                <input type="text" x-model="formData.atb_keterangan" placeholder="Versi 3.2.1 Enterprise Edition"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- --------------------------------------------------------------------- -->
                <!-- KONDISI KDP: KONSTRUKSI DALAM PENGERJAAN                              -->
                <!-- --------------------------------------------------------------------- -->
                <template x-if="isKdp">
                    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Nama Bangunan / Objek KDP</label>
                                <input type="text" x-model="formData.kdp_nama_bangunan" placeholder="Contoh: Gedung Rawat Inap Tahap I"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-200 mb-1">Persentase Fisik Saat Ini (%)</label>
                                <input type="number" min="0" max="100" x-model.number="formData.kdp_persentase_fisik" placeholder="65"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 4: PEJABAT PPK, PENEMPATAN & KONFIRMASI DATA                      -->
            <!-- ========================================================================= -->
            <div x-show="step === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-emerald-400/10 text-emerald-300 border border-emerald-400/20 text-xs font-bold mb-2">
                        <span>Langkah 4 dari 4</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>🔍 Pejabat Penerima, Lokasi Penempatan &amp; Konfirmasi</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Tentukan pejabat PPK yang mengesahkan penerimaan aset hibah dan unit ruangan penempatan.
                    </p>
                </div>

                <!-- Input PPK & Penempatan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Pejabat Pembuat Komitmen (PPK) RSUD
                        </label>
                        <select x-model="formData.ppk_nama" @change="onPpkSelect()"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            <template x-for="p in pejabatsList" :key="p.nama">
                                <option :value="p.nama" x-text="p.nama + (p.nip ? ' (' + p.nip + ')' : '')"></option>
                            </template>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Unit / Ruangan Penempatan Aset
                        </label>
                        <select x-model="formData.unit_id"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            <option value="">-- Pilih Unit / Ruangan --</option>
                            @foreach($dbUnits ?? [] as $u)
                                <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->kode_unit ?? 'Unit' }})</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Alamat / Lokasi Fisik Penempatan
                        </label>
                        <input type="text" x-model="formData.alamat_barang"
                            placeholder="RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1"
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Catatan / Keterangan Riwayat Hibah
                        </label>
                        <textarea x-model="formData.hibah_keterangan" rows="2"
                            placeholder="Contoh: Hibah sarana medis program penanggulangan dari Kemenkes RI, kondisi operasional aktif..."
                            class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
                    </div>
                </div>

                <!-- Ringkasan Review Card -->
                <div class="p-5 rounded-2xl bg-slate-950 border border-emerald-500/30 space-y-4 shadow-xl">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                            <span>📋 Ringkasan Pendaftaran Aset Hibah</span>
                        </span>
                        <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20" x-text="kibLabel"></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs text-slate-300">
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Nama Barang:</span>
                            <span class="font-bold text-white text-sm" x-text="formData.nama_barang || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Klasifikasi 108:</span>
                            <span class="font-bold text-cyan-300" x-text="selectedSubSub ? (selectedSubSub.kode + ' - ' + selectedSubSub.nama) : '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Pemberi Hibah:</span>
                            <span class="font-bold text-amber-300" x-text="formData.hibah_pemberi || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Dokumen BAST:</span>
                            <span class="font-mono text-slate-200 font-semibold" x-text="(formData.hibah_nomor_bast || '-') + ' (' + formatTanggalIndo(formData.hibah_tanggal_bast) + ')'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Volume &amp; Nilai Total:</span>
                            <span class="font-bold text-emerald-400 text-sm font-mono" x-text="formData.jumlah_volume + ' ' + formData.satuan + ' = ' + formatRupiah(formData.total_realisasi)"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase font-bold">Penempatan:</span>
                            <span class="font-semibold text-slate-200" x-text="formData.alamat_barang"></span>
                        </div>
                    </div>

                    <!-- Spesifikasi Khusus Tanah Preview -->
                    <template x-if="isTanah && formData.tanah_items.length > 0">
                        <div class="p-3 rounded-xl bg-slate-900 border border-emerald-500/20 text-xs space-y-1.5">
                            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider block">Rincian Spesifikasi Tanah:</span>
                            <div class="flex flex-wrap gap-4 text-slate-300 text-[11px]">
                                <span>Total Luas: <strong class="text-cyan-300 font-mono" x-text="totalLuasTanah.toLocaleString('id-ID') + ' m²'"></strong></span>
                                <span>Jumlah Bidang: <strong class="text-white" x-text="formData.tanah_items.length + ' Bidang'"></strong></span>
                                <span>Hak: <strong class="text-amber-300" x-text="formData.tanah_items[0].tanah_hak"></strong></span>
                                <span x-show="formData.tanah_items[0].tanah_sertifikat_no">Sertifikat: <strong class="text-slate-200 font-mono" x-text="formData.tanah_items[0].tanah_sertifikat_no"></strong></span>
                            </div>
                        </div>
                    </template>

                    <!-- Spesifikasi Mesin Preview -->
                    <template x-if="isMesin && (formData.merk || formData.type || formData.no_pabrik)">
                        <div class="p-3 rounded-xl bg-slate-900 border border-teal-500/20 text-xs space-y-1.5">
                            <span class="text-[10px] font-bold text-teal-400 uppercase tracking-wider block">Rincian Peralatan:</span>
                            <div class="flex flex-wrap gap-4 text-slate-300 text-[11px]">
                                <span x-show="formData.merk">Merk: <strong class="text-white" x-text="formData.merk"></strong></span>
                                <span x-show="formData.type">Tipe: <strong class="text-white" x-text="formData.type"></strong></span>
                                <span x-show="formData.no_pabrik">SN: <strong class="text-cyan-300 font-mono" x-text="formData.no_pabrik"></strong></span>
                            </div>
                        </div>
                    </template>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- BOTTOM NAVIGATION BUTTONS                                                 -->
            <!-- ========================================================================= -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-between">
                <div>
                    <button type="button" x-show="step > 1" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-all flex items-center space-x-2 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Kembali</span>
                    </button>
                </div>

                <div class="flex items-center space-x-3">
                    <a href="{{ route('astap.pilih_jenis') }}"
                        class="px-4 py-2.5 rounded-xl text-slate-400 hover:text-rose-400 font-bold text-xs transition-colors">
                        Batal
                    </a>

                    <!-- Next Step Button -->
                    <button type="button" x-show="step < 4" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-amber-400 hover:bg-amber-300 text-slate-950 font-extrabold text-xs shadow-lg shadow-amber-400/20 transition-all flex items-center space-x-2 cursor-pointer">
                        <span>Lanjutkan</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>

                    <!-- Final Submit Button -->
                    <button type="submit" x-show="step === 4" :disabled="isSubmitting"
                        class="px-8 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 disabled:opacity-50 text-slate-950 font-black text-xs shadow-xl shadow-emerald-500/25 transition-all flex items-center space-x-2 cursor-pointer">
                        <span x-show="!isSubmitting">💾 Simpan Data Hibah</span>
                        <span x-show="isSubmitting">Menyimpan...</span>
                    </button>
                </div>
            </div>

        </form>

    </div>

    <!-- Alpine.js Script Implementation -->
    <script>
        function formHibah() {
            return {
                step: 1,
                isSubmitting: false,
                master108: @json($dbMaster108 ?? []),
                pejabatsList: @json($dbPejabats ?? []),

                // Form State
                formData: {
                    tahun_perolehan: new Date().getFullYear(),
                    triwulan: 'TW I',
                    hibah_pemberi: '',
                    hibah_nomor_bast: '',
                    hibah_tanggal_bast: new Date().toISOString().split('T')[0],
                    total_realisasi: 0,
                    hibah_keterangan: '',
                    nama_barang: '',
                    jenis_astap_id: '',
                    jumlah_volume: 1,
                    satuan: 'Unit',
                    kondisi: 'Baik',
                    unit_id: '',
                    alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                    ppk_nama: '',
                    ppk_nip: '',
                    
                    // Spesifikasi Tanah (KIB A)
                    tanah_items: [
                        {
                            tanah_hak: 'Hak Pakai',
                            tanah_sertifikat_tgl: '',
                            tanah_sertifikat_no: '',
                            tanah_kondisi: 'Baik',
                            tanah_penggunaan: 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                            tanah_jumlah_bidang: 1,
                            tanah_luas_m2: '',
                            tanah_alamat: '',
                            tanah_nilai_fisik: 0
                        }
                    ],
                    sertifikat_nomor: '',

                    // Spesifikasi Peralatan & Mesin (KIB B)
                    merk: '',
                    type: '',
                    no_pabrik: '',
                    ukuran: '',
                    bahan: '',
                    no_rangka: '',
                    no_mesin: '',
                    no_polisi: '',

                    // Spesifikasi Gedung & Bangunan (KIB C)
                    gedung_luas_m2: '',
                    gedung_bertingkat: 'Tidak',
                    gedung_beton: 'Beton',
                    gedung_status_tanah: 'Tanah Pemda',
                    gedung_dokumen_no: '',
                    gedung_dokumen_tgl: '',

                    // Spesifikasi Jalan, Irigasi & Jaringan (KIB D)
                    jaringan_panjang_m: '',
                    jaringan_lebar_m: '',
                    jaringan_luas_m2: '',
                    jaringan_konstruksi: '',

                    // Spesifikasi Aset Tetap Lainnya (KIB E)
                    lainnya_judul: '',
                    lainnya_spesifikasi: '',
                    lainnya_asal: '',

                    // Spesifikasi ATB
                    atb_jenis: 'Software / Aplikasi Sistem Informasi',
                    atb_nama_aplikasi: '',
                    atb_masa_manfaat: 4,
                    atb_keterangan: '',

                    // Spesifikasi KDP
                    kdp_nama_bangunan: '',
                    kdp_persentase_fisik: 0
                },

                // 108 Selection State
                selectedJenisIdx: '',
                selectedSubIdx: '',
                selectedSubSub: null,
                currentSubList: [],
                currentSubSubList: [],
                search108: '',
                searchResults108: [],
                allFlattened108: [],

                init() {
                    // Set auto triwulan based on month
                    const m = new Date().getMonth() + 1;
                    if (m >= 1 && m <= 3) this.formData.triwulan = 'TW I';
                    else if (m >= 4 && m <= 6) this.formData.triwulan = 'TW II';
                    else if (m >= 7 && m <= 9) this.formData.triwulan = 'TW III';
                    else this.formData.triwulan = 'TW IV';

                    // Flatten 108 for instant search
                    this.flatten108();

                    // Default PPK
                    if (this.pejabatsList && this.pejabatsList.length > 0) {
                        this.formData.ppk_nama = this.pejabatsList[0].nama;
                        this.formData.ppk_nip = this.pejabatsList[0].nip || '';
                    }
                },

                // Getters Deteksi Kelompok KIB
                get selectedKibKode() {
                    if (this.selectedSubSub && this.selectedSubSub.jenisKode) {
                        return this.selectedSubSub.jenisKode;
                    }
                    if (this.selectedJenisIdx !== '' && this.master108[this.selectedJenisIdx]) {
                        return this.master108[this.selectedJenisIdx].kode;
                    }
                    return '';
                },

                get selectedKibNama() {
                    if (this.selectedSubSub && this.selectedSubSub.jenisNama) {
                        return this.selectedSubSub.jenisNama;
                    }
                    if (this.selectedJenisIdx !== '' && this.master108[this.selectedJenisIdx]) {
                        return this.master108[this.selectedJenisIdx].nama;
                    }
                    return '';
                },

                get isTanah() {
                    return this.selectedKibKode.startsWith('1.3.1') || this.selectedKibNama.includes('TANAH');
                },

                get isMesin() {
                    return this.selectedKibKode.startsWith('1.3.2') || this.selectedKibNama.includes('PERALATAN') || (!this.selectedKibKode && this.selectedJenisIdx === '');
                },

                get isGedung() {
                    return this.selectedKibKode.startsWith('1.3.3') || this.selectedKibNama.includes('GEDUNG') || this.selectedKibNama.includes('BANGUNAN');
                },

                get isJaringan() {
                    return this.selectedKibKode.startsWith('1.3.4') || this.selectedKibNama.includes('JARINGAN') || this.selectedKibNama.includes('JALAN') || this.selectedKibNama.includes('IRIGASI');
                },

                get isAsetLainnya() {
                    return this.selectedKibKode.startsWith('1.3.5') || this.selectedKibNama.includes('ASET TETAP LAINNYA') || this.selectedKibNama.includes('LAINNYA');
                },

                get isAtb() {
                    return this.selectedKibKode.startsWith('1.5.3') || this.selectedKibNama.includes('TIDAK BERWUJUD') || this.selectedKibNama.includes('ATB');
                },

                get isKdp() {
                    return this.selectedKibKode.startsWith('1.3.6') || this.selectedKibNama.includes('KONSTRUKSI') || this.selectedKibNama.includes('KDP');
                },

                get kibLabel() {
                    if (this.isTanah) return 'Rincian Tanah';
                    if (this.isGedung) return 'Rincian Gedung';
                    if (this.isJaringan) return 'Rincian Jaringan';
                    if (this.isAsetLainnya) return 'Rincian Aset Lainnya';
                    if (this.isAtb) return 'Rincian ATB';
                    if (this.isKdp) return 'Rincian KDP';
                    return 'Rincian Mesin & Alat';
                },

                get kibBadgeIcon() {
                    if (this.isTanah) return '🌾';
                    if (this.isGedung) return '🏢';
                    if (this.isJaringan) return '🛣️';
                    if (this.isAsetLainnya) return '📚';
                    if (this.isAtb) return '💻';
                    if (this.isKdp) return '🏗️';
                    return '⚙️';
                },

                get totalLuasTanah() {
                    if (!this.formData.tanah_items) return 0;
                    return this.formData.tanah_items.reduce((sum, it) => sum + (parseFloat(it.tanah_luas_m2) || 0), 0);
                },

                // Multi-Bidang Tanah Repeater Helper
                addTanahItem() {
                    this.formData.tanah_items.push({
                        tanah_hak: 'Hak Pakai',
                        tanah_sertifikat_tgl: '',
                        tanah_sertifikat_no: '',
                        tanah_kondisi: 'Baik',
                        tanah_penggunaan: 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                        tanah_jumlah_bidang: 1,
                        tanah_luas_m2: '',
                        tanah_alamat: '',
                        tanah_nilai_fisik: 0
                    });
                    this.syncTanahFields();
                },

                removeTanahItem(index) {
                    if (this.formData.tanah_items.length > 1) {
                        this.formData.tanah_items.splice(index, 1);
                        this.syncTanahFields();
                    }
                },

                syncTanahFields() {
                    if (this.formData.tanah_items && this.formData.tanah_items.length > 0) {
                        const first = this.formData.tanah_items[0];
                        this.formData.sertifikat_nomor = first.tanah_sertifikat_no;
                        this.formData.kondisi = first.tanah_kondisi;
                        const totalBidang = this.formData.tanah_items.reduce((s, it) => s + (parseInt(it.tanah_jumlah_bidang) || 1), 0);
                        this.formData.jumlah_volume = totalBidang;
                        this.formData.satuan = 'Bidang';
                        if (first.tanah_alamat) {
                            this.formData.alamat_barang = first.tanah_alamat;
                        }
                    }
                },

                adjustSatuanForKib() {
                    if (this.isTanah) {
                        this.formData.satuan = 'Bidang';
                        this.syncTanahFields();
                    } else if (this.isGedung) {
                        this.formData.satuan = 'Gedung';
                    } else if (this.isJaringan) {
                        this.formData.satuan = 'M²';
                    } else if (this.isAtb) {
                        this.formData.satuan = 'Paket';
                    } else {
                        this.formData.satuan = 'Unit';
                    }
                },

                flatten108() {
                    this.allFlattened108 = [];
                    (this.master108 || []).forEach(j => {
                        (j.subRincian || []).forEach(s => {
                            (s.subSubRincian || []).forEach(ss => {
                                this.allFlattened108.push({
                                    id: ss.id,
                                    kode: ss.kode,
                                    nama: ss.nama,
                                    parentKode: s.kode,
                                    parentNama: s.nama,
                                    jenisKode: j.kode,
                                    jenisNama: j.nama
                                });
                            });
                        });
                    });
                },

                performSearch108() {
                    const q = (this.search108 || '').trim().toLowerCase();
                    if (!q || q.length < 2) {
                        this.searchResults108 = [];
                        return;
                    }
                    this.searchResults108 = this.allFlattened108.filter(item => 
                        item.nama.toLowerCase().includes(q) || item.kode.toLowerCase().includes(q)
                    ).slice(0, 20);
                },

                select108Direct(item) {
                    this.formData.jenis_astap_id = item.id;
                    this.selectedSubSub = item;
                    this.search108 = '';
                    this.searchResults108 = [];

                    if (!this.formData.nama_barang) {
                        this.formData.nama_barang = item.nama;
                    }

                    const jIdx = this.master108.findIndex(j => j.kode === item.jenisKode);
                    if (jIdx !== -1) {
                        this.selectedJenisIdx = jIdx;
                        this.currentSubList = this.master108[jIdx].subRincian || [];
                        const sIdx = this.currentSubList.findIndex(s => s.kode === item.parentKode);
                        if (sIdx !== -1) {
                            this.selectedSubIdx = sIdx;
                            this.currentSubSubList = this.currentSubList[sIdx].subSubRincian || [];
                        }
                    }
                    this.adjustSatuanForKib();
                },

                onJenisChange() {
                    this.currentSubList = [];
                    this.currentSubSubList = [];
                    this.selectedSubIdx = '';
                    this.formData.jenis_astap_id = '';
                    this.selectedSubSub = null;

                    if (this.selectedJenisIdx !== '' && this.master108[this.selectedJenisIdx]) {
                        this.currentSubList = this.master108[this.selectedJenisIdx].subRincian || [];
                        this.adjustSatuanForKib();
                    }
                },

                onSubChange() {
                    this.currentSubSubList = [];
                    this.formData.jenis_astap_id = '';
                    this.selectedSubSub = null;

                    if (this.selectedSubIdx !== '' && this.currentSubList[this.selectedSubIdx]) {
                        this.currentSubSubList = this.currentSubList[this.selectedSubIdx].subSubRincian || [];
                    }
                },

                onSubSubChange() {
                    const id = parseInt(this.formData.jenis_astap_id);
                    const found = this.currentSubSubList.find(x => x.id === id);
                    this.selectedSubSub = found || null;
                    if (found && !this.formData.nama_barang) {
                        this.formData.nama_barang = found.nama;
                    }
                    this.adjustSatuanForKib();
                },

                onPpkSelect() {
                    const p = this.pejabatsList.find(x => x.nama === this.formData.ppk_nama);
                    if (p) {
                        this.formData.ppk_nip = p.nip || '';
                    }
                },

                formatRupiah(val) {
                    const num = parseFloat(val) || 0;
                    return 'Rp ' + Math.round(num).toLocaleString('id-ID');
                },

                formatTanggalIndo(dateStr) {
                    if (!dateStr) return '-';
                    try {
                        const d = new Date(dateStr);
                        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
                    } catch (e) {
                        return dateStr;
                    }
                },

                goToStep(s) {
                    if (s > this.step) {
                        for (let i = this.step; i < s; i++) {
                            if (!this.validateStep(i)) return;
                        }
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
                        if (!this.formData.tahun_perolehan) {
                            alert('⚠️ Mohon tentukan Tahun Anggaran Pembukuan.');
                            return false;
                        }
                        if (!this.formData.triwulan) {
                            alert('⚠️ Mohon tentukan Triwulan Pembukuan.');
                            return false;
                        }
                        if (!this.formData.hibah_pemberi.trim()) {
                            alert('⚠️ Mohon isi Nama Instansi Pemberi Hibah.');
                            return false;
                        }
                        if (!this.formData.hibah_nomor_bast.trim()) {
                            alert('⚠️ Mohon isi Nomor BAST Hibah.');
                            return false;
                        }
                        if (!this.formData.hibah_tanggal_bast) {
                            alert('⚠️ Mohon isi Tanggal BAST Hibah.');
                            return false;
                        }
                        if (this.formData.total_realisasi === null || this.formData.total_realisasi < 0) {
                            alert('⚠️ Mohon isi Total Nilai Perolehan / Taksiran Hibah (Rp).');
                            return false;
                        }
                        return true;
                    }
                    if (s === 2) {
                        if (!this.formData.jenis_astap_id) {
                            alert('⚠️ Mohon pilih Klasifikasi Kode Barang 108.');
                            return false;
                        }
                        if (!this.formData.nama_barang.trim()) {
                            alert('⚠️ Mohon isi Nama Lengkap Barang.');
                            return false;
                        }
                        return true;
                    }
                    if (s === 3) {
                        if (this.isTanah) {
                            this.syncTanahFields();
                        }
                        return true;
                    }
                    return true;
                },

                submitForm() {
                    if (!this.validateStep(1) || !this.validateStep(2) || !this.validateStep(3)) return;
                    
                    if (this.isTanah) {
                        this.syncTanahFields();
                    }

                    this.isSubmitting = true;
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

                    fetch("{{ route('astap.store_hibah') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': token
                        },
                        body: JSON.stringify(this.formData)
                    })
                    .then(res => res.json().then(data => ({ status: res.status, body: data })))
                    .then(result => {
                        this.isSubmitting = false;
                        if (result.status === 200 && result.body.success) {
                            alert('🎉 Berhasil! ' + (result.body.message || 'Data Hibah berhasil disimpan.'));
                            window.location.href = "{{ route('master.hibah') }}";
                        } else {
                            const errMsg = result.body.message || (result.body.errors ? Object.values(result.body.errors).flat().join('\n') : 'Gagal menyimpan data.');
                            alert('❌ Terjadi Kesalahan:\n' + errMsg);
                        }
                    })
                    .catch(err => {
                        this.isSubmitting = false;
                        console.error(err);
                        alert('❌ Gagal menghubungi server. Silakan coba kembali.');
                    });
                }
            };
        }
    </script>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(15, 23, 42, 0.6);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(245, 158, 11, 0.4);
            border-radius: 9999px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(245, 158, 11, 0.7);
        }
    </style>
</x-layout>
