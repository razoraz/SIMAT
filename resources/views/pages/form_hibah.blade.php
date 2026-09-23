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
                            🎁 RMB HIBAH
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Pendaftaran aset perolehan hibah / bantuan dari pihak ketiga tanpa pagu belanja modal APBD.
                    </p>
                </div>
            </div>

            <!-- Stepper Progress Indicator (4 Langkah) -->
            <div class="flex items-center space-x-1.5 bg-slate-900/90 border border-slate-800 p-1.5 rounded-2xl">
                <!-- Step 1: Program SIPD -->
                <button type="button" @click="goToStep(1)"
                    class="flex items-center space-x-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                    :class="step === 1 ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : (step > 1 ? 'text-amber-400 hover:bg-slate-800' : 'text-slate-500 hover:bg-slate-800')">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]"
                        :class="step === 1 ? 'bg-slate-950 text-amber-400' : (step > 1 ? 'bg-amber-400 text-slate-950' : 'bg-slate-800 text-slate-400')">
                        <span x-show="step <= 1">1</span>
                        <span x-show="step > 1">✓</span>
                    </span>
                    <span class="hidden md:inline">Program SIPD</span>
                </button>

                <div class="w-2 h-0.5 bg-slate-800"></div>

                <!-- Step 2: BAST & 108 -->
                <button type="button" @click="goToStep(2)"
                    class="flex items-center space-x-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                    :class="step === 2 ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : (step > 2 ? 'text-amber-400 hover:bg-slate-800' : 'text-slate-500 hover:bg-slate-800')">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]"
                        :class="step === 2 ? 'bg-slate-950 text-amber-400' : (step > 2 ? 'bg-amber-400 text-slate-950' : 'bg-slate-800 text-slate-400')">
                        <span x-show="step <= 2">2</span>
                        <span x-show="step > 2">✓</span>
                    </span>
                    <span class="hidden md:inline">BAST &amp; 108</span>
                </button>

                <div class="w-2 h-0.5 bg-slate-800"></div>

                <!-- Step 3: Spesifikasi Teknis -->
                <button type="button" @click="goToStep(3)"
                    class="flex items-center space-x-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                    :class="step === 3 ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : (step > 3 ? 'text-amber-400 hover:bg-slate-800' : 'text-slate-500 hover:bg-slate-800')">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]"
                        :class="step === 3 ? 'bg-slate-950 text-amber-400' : (step > 3 ? 'bg-amber-400 text-slate-950' : 'bg-slate-800 text-slate-400')">
                        <span x-show="step <= 3">3</span>
                        <span x-show="step > 3">✓</span>
                    </span>
                    <span class="hidden md:inline">Rincian Teknis</span>
                </button>

                <div class="w-2 h-0.5 bg-slate-800"></div>

                <!-- Step 4: Penerima & Konfirmasi -->
                <button type="button" @click="goToStep(4)"
                    class="flex items-center space-x-1 px-2.5 py-1.5 rounded-xl text-xs font-bold transition-all"
                    :class="step === 4 ? 'bg-amber-400 text-slate-950 shadow-md shadow-amber-400/20' : 'text-slate-500 hover:bg-slate-800'">
                    <span class="w-4 h-4 rounded-full flex items-center justify-center text-[10px]"
                        :class="step === 4 ? 'bg-slate-950 text-amber-400' : 'bg-slate-800 text-slate-400'">
                        4
                    </span>
                    <span class="hidden md:inline">Konfirmasi</span>
                </button>
            </div>
        </div>

        <!-- MAIN FORM CONTAINER -->
        <form @submit.prevent="submitForm" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">

            <!-- ========================================================================= -->
            <!-- LANGKAH 1: PROGRAM & KEGIATAN SIPD (GAMBAR 1 - TANPA JENIS PENGADAAN)      -->
            <!-- ========================================================================= -->
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20 text-xs font-bold mb-2">
                        <span>Langkah 1 dari 4</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>🏛️ Program &amp; Kegiatan Penunjang Urusan (SIPD)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Tentukan klasifikasi program dan kegiatan penunjang RSUD pada SIPD (bidang pelayanan/operasional RSUD).
                    </p>
                </div>

                <!-- Periode Anggaran & Triwulan Header -->
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

                <!-- Hierarki Program & Kegiatan SIPD -->
                <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                    <!-- 1. Program SIPD -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-200">
                            1. Program SIPD
                        </label>
                        <select x-model="selectedProgramKode" @change="onProgramChange()"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <template x-for="p in programsList" :key="p.kode">
                                <option :value="p.kode" x-text="p.kode + ' - ' + p.nama"></option>
                            </template>
                        </select>
                    </div>

                    <!-- 2. Kegiatan SIPD -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-200">
                            2. Kegiatan SIPD
                        </label>
                        <select x-model="selectedKegiatanKode" @change="onKegiatanChange()"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <template x-for="k in kegiatansList" :key="k.kode">
                                <option :value="k.kode" x-text="k.kode + ' - ' + k.nama"></option>
                            </template>
                        </select>
                    </div>

                    <!-- 3. Sub Kegiatan SIPD (Sebagai lingkup penunjang) -->
                    <div class="space-y-1.5">
                        <label class="block text-xs font-bold text-slate-200">
                            3. Sub Kegiatan SIPD
                        </label>
                        <select x-model="selectedSubKegiatanKode" @change="onSubKegiatanChange()"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                            <template x-for="sk in subKegiatansList" :key="sk.kode">
                                <option :value="sk.kode" x-text="sk.kode + ' - ' + sk.nama"></option>
                            </template>
                        </select>
                    </div>

                    <!-- Jalur SIPD Banner -->
                    <div class="p-3.5 rounded-xl bg-purple-950/20 border border-purple-500/30 flex items-center justify-between text-xs">
                        <div class="space-y-1">
                            <span class="text-purple-300 font-bold text-[10.5px] block uppercase">Jalur Urusan Terpilih:</span>
                            <div class="text-slate-300 text-[11px]" x-text="formData.program_nama + ' ➔ ' + formData.kegiatan_nama"></div>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-purple-500/20 text-purple-300 border border-purple-500/30">
                            SIPD BLUD
                        </span>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 2: DOKUMEN BAST HIBAH & KODE BARANG 108 (PENGGANTI GAMBAR 2)       -->
            <!-- ========================================================================= -->
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-300 border border-amber-400/20 text-xs font-bold mb-2">
                        <span>Langkah 2 dari 4</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>📜 Dokumen BAST &amp; Klasifikasi Kode Barang 108</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Masukkan legalitas penyerahan (BAST Hibah), nama pihak pemberi hibah, nilai perolehan, serta klasifikasi Permendagri 108.
                    </p>
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
                                placeholder="Contoh: 020/BAST-HIBAH/RSUD/2026"
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

                    <!-- Total Nilai Hibah & Volume -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2 border-t border-slate-800/80">
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Jumlah Kuantitas / Volume <span class="text-rose-400">*</span>
                            </label>
                            <input type="number" x-model.number="formData.jumlah_volume" min="1" required
                                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                                Satuan <span class="text-rose-400">*</span>
                            </label>
                            <select x-model="formData.satuan" required
                                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                                <option value="Unit">Unit</option>
                                <option value="Set">Set</option>
                                <option value="Buah">Buah</option>
                                <option value="Paket">Paket</option>
                                <option value="Bidang">Bidang</option>
                                <option value="Lembar">Lembar</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                                <span>Nilai Perolehan Realisasi (Rp) <span class="text-rose-400">*</span></span>
                            </label>
                            <input type="number" x-model.number="formData.total_realisasi" min="0" step="any" required
                                placeholder="0"
                                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-amber-300 font-bold font-mono focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Bagian Klasifikasi Kode 108 -->
                <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                            <span>🔍 Klasifikasi Kode Barang (Permendagri 108)</span>
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
                            placeholder="Ketik nama atau kode barang (contoh: USG, Bed Pasien, Ambulance, Laptop, Meja)..."
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

                    <!-- Nama Barang Lengkap -->
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1.5">
                            Nama Lengkap Barang / Merk / Tipe <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.nama_barang" required
                            placeholder="Contoh: USG Mindray DC-30 Color Doppler Portable SN: 88291..."
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 3: RINCIAN SPESIFIKASI TEKNIS ASET (KIB A - F, ATB, EXTRACOM)      -->
            <!-- ========================================================================= -->
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
                
                <div class="border-b border-slate-800 pb-4">
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/20 text-xs font-bold mb-2">
                        <span>Langkah 3 dari 4</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span>⚙️ Rincian Spesifikasi Teknis Barang</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Lengkapi atribut fisik barang sesuai kelompok KIB (Merk, Tipe, No Seri Pabrik, Dimensi, Bahan, Nomor Rangka/Mesin, dll.).
                    </p>
                </div>

                <!-- Accordion / Form Spesifikasi Teknis -->
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

                    <!-- Detail Tambahan Kendaraan / Mesin Berat (Opsional) -->
                    <div class="pt-3 border-t border-slate-800/80 space-y-3">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                            Atribut Tambahan Kendaraan / Gedung / Tanah (Jika Relevan):
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

            </div>

            <!-- ========================================================================= -->
            <!-- LANGKAH 4: PPK, PENEMPATAN & KONFIRMASI DATA                               -->
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
                            Alamat / Gedung Penempatan
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
                <div class="p-5 rounded-2xl bg-slate-950 border border-emerald-500/30 space-y-3">
                    <span class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider block">
                        📋 Ringkasan Pendaftaran Hibah:
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-300">
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Nama Barang:</span>
                            <span class="font-bold text-white text-sm" x-text="formData.nama_barang || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Klasifikasi 108:</span>
                            <span class="font-bold text-cyan-300" x-text="selectedSubSub?.kode + ' - ' + selectedSubSub?.nama"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Pemberi Hibah:</span>
                            <span class="font-bold text-amber-300" x-text="formData.hibah_pemberi || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Dokumen BAST:</span>
                            <span class="font-mono text-slate-200 font-semibold" x-text="formData.hibah_nomor_bast + ' (' + formatTanggalIndo(formData.hibah_tanggal_bast) + ')'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Volume &amp; Nilai Total:</span>
                            <span class="font-bold text-emerald-400 text-sm font-mono" x-text="formData.jumlah_volume + ' ' + formData.satuan + ' = ' + formatRupiah(formData.total_realisasi)"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px] uppercase">Penempatan:</span>
                            <span class="font-semibold text-slate-200" x-text="formData.alamat_barang"></span>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ========================================================================= -->
            <!-- BOTTOM NAVIGATION BUTTONS                                                 -->
            <!-- ========================================================================= -->
            <div class="pt-6 border-t border-slate-800 flex items-center justify-between">
                <div>
                    <button type="button" x-show="step > 1" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-all flex items-center space-x-2">
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
                dbPengadaans: @json($dbJenisPengadaans ?? []),
                pejabatsList: @json($dbPejabats ?? []),

                // Form State
                formData: {
                    tahun_perolehan: new Date().getFullYear(),
                    triwulan: 'TW I',
                    program_kode: '',
                    program_nama: '',
                    kegiatan_kode: '',
                    kegiatan_nama: '',
                    sub_kegiatan_kode: '',
                    sub_kegiatan_nama: '',
                    nama_barang: '',
                    jenis_astap_id: '',
                    jumlah_volume: 1,
                    satuan: 'Unit',
                    total_realisasi: 0,
                    hibah_pemberi: '',
                    hibah_nomor_bast: '',
                    hibah_tanggal_bast: new Date().toISOString().split('T')[0],
                    hibah_keterangan: '',
                    unit_id: '',
                    alamat_barang: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
                    kondisi: 'Baik',
                    merk: '',
                    type: '',
                    no_pabrik: '',
                    ukuran: '',
                    bahan: '',
                    no_rangka: '',
                    no_mesin: '',
                    no_polisi: '',
                    ppk_nama: '',
                    ppk_nip: ''
                },

                // SIPD Selection Lists
                programsList: [],
                kegiatansList: [],
                subKegiatansList: [],
                selectedProgramKode: '',
                selectedKegiatanKode: '',
                selectedSubKegiatanKode: '',

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

                    // Parse distinct programs from dbPengadaans
                    this.initSipdData();

                    // Flatten 108 for instant search
                    this.flatten108();

                    // Default PPK
                    if (this.pejabatsList && this.pejabatsList.length > 0) {
                        this.formData.ppk_nama = this.pejabatsList[0].nama;
                        this.formData.ppk_nip = this.pejabatsList[0].nip || '';
                    }
                },

                initSipdData() {
                    const progMap = new Map();
                    (this.dbPengadaans || []).forEach(p => {
                        const pKode = p.program_kode || '0.00.01';
                        const pNama = p.program_nama || 'Program Penunjang Urusan Pemerintah Daerah';
                        if (!progMap.has(pKode)) {
                            progMap.set(pKode, { kode: pKode, nama: pNama, items: [] });
                        }
                        progMap.get(pKode).items.push(p);
                    });

                    this.programsList = Array.from(progMap.values());
                    if (this.programsList.length > 0) {
                        this.selectedProgramKode = this.programsList[0].kode;
                        this.onProgramChange();
                    }
                },

                onProgramChange() {
                    const prog = this.programsList.find(p => p.kode === this.selectedProgramKode);
                    if (prog) {
                        this.formData.program_kode = prog.kode;
                        this.formData.program_nama = prog.nama;

                        const kegMap = new Map();
                        prog.items.forEach(it => {
                            const kKode = it.kegiatan_kode || '0.00.01.2.10';
                            const kNama = it.kegiatan_nama || 'Peningkatan Pelayanan BLUD';
                            if (!kegMap.has(kKode)) {
                                kegMap.set(kKode, { kode: kKode, nama: kNama, items: [] });
                            }
                            kegMap.get(kKode).items.push(it);
                        });
                        this.kegiatansList = Array.from(kegMap.values());
                        if (this.kegiatansList.length > 0) {
                            this.selectedKegiatanKode = this.kegiatansList[0].kode;
                            this.onKegiatanChange();
                        }
                    }
                },

                onKegiatanChange() {
                    const keg = this.kegiatansList.find(k => k.kode === this.selectedKegiatanKode);
                    if (keg) {
                        this.formData.kegiatan_kode = keg.kode;
                        this.formData.kegiatan_nama = keg.nama;

                        const subMap = new Map();
                        keg.items.forEach(it => {
                            const skKode = it.sub_kegiatan_kode || '0.00.01.2.10.0001';
                            const skNama = it.sub_kegiatan_nama || 'Pelayanan dan Penunjang Pelayanan BLUD';
                            if (!subMap.has(skKode)) {
                                subMap.set(skKode, { kode: skKode, nama: skNama });
                            }
                        });
                        this.subKegiatansList = Array.from(subMap.values());
                        if (this.subKegiatansList.length > 0) {
                            this.selectedSubKegiatanKode = this.subKegiatansList[0].kode;
                            this.onSubKegiatanChange();
                        }
                    }
                },

                onSubKegiatanChange() {
                    const sk = this.subKegiatansList.find(s => s.kode === this.selectedSubKegiatanKode);
                    if (sk) {
                        this.formData.sub_kegiatan_kode = sk.kode;
                        this.formData.sub_kegiatan_nama = sk.nama;
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
                },

                onJenisChange() {
                    this.currentSubList = [];
                    this.currentSubSubList = [];
                    this.selectedSubIdx = '';
                    this.formData.jenis_astap_id = '';
                    this.selectedSubSub = null;

                    if (this.selectedJenisIdx !== '' && this.master108[this.selectedJenisIdx]) {
                        this.currentSubList = this.master108[this.selectedJenisIdx].subRincian || [];
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
                        if (this.validateStep(this.step)) {
                            this.step = s;
                        }
                    } else {
                        this.step = s;
                    }
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
                            alert('⚠️ Mohon tentukan Tahun Anggaran.');
                            return false;
                        }
                        return true;
                    } else if (s === 2) {
                        if (!this.formData.hibah_pemberi.trim()) {
                            alert('⚠️ Mohon isi Nama Instansi Pemberi Hibah.');
                            return false;
                        }
                        if (!this.formData.hibah_nomor_bast.trim()) {
                            alert('⚠️ Mohon isi Nomor BAST Hibah.');
                            return false;
                        }
                        if (!this.formData.jenis_astap_id) {
                            alert('⚠️ Mohon pilih Klasifikasi Kode Barang 108.');
                            return false;
                        }
                        if (!this.formData.nama_barang.trim()) {
                            alert('⚠️ Mohon isi Nama Lengkap Barang.');
                            return false;
                        }
                        if (!this.formData.total_realisasi || this.formData.total_realisasi < 0) {
                            alert('⚠️ Mohon isi Nilai Perolehan Hibah (Rp).');
                            return false;
                        }
                        return true;
                    }
                    return true;
                },

                submitForm() {
                    if (!this.validateStep(1) || !this.validateStep(2)) return;
                    
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
