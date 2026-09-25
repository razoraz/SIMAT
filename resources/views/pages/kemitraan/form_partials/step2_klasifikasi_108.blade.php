<!-- ========================================================================= -->
<!-- LANGKAH 2: KLASIFIKASI KODE BARANG 108 & NILAI ASET (AKUN 1.5.2)          -->
<!-- ========================================================================= -->
<div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-400/10 text-cyan-300 border border-cyan-400/20 text-xs font-bold mb-2">
            <span>🔍 LANGKAH 2 DARI 3: KLASIFIKASI KODE BARANG 108 &amp; NILAI TAKSIRAN</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-cyan-400/10 text-cyan-400 text-sm">📊</span>
            <span>Langkah 2: Klasifikasi Kode Barang 108 &amp; Nilai Wajar Aset</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Pilih klasifikasi kode barang Permendagri No. 108/2016 (khususnya sub-akun <strong>1.5.2 Kemitraan Pihak Ketiga</strong>), rincian volume, satuan, dan total taksiran nilai wajar aset.
        </p>
    </div>

    <!-- Quick Action / Shortcut Akun 1.5.2 -->
    <div class="p-4 sm:p-5 rounded-3xl bg-cyan-950/30 border border-cyan-500/30 flex flex-col md:flex-row md:items-center justify-between gap-4 shadow-xl">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-2xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-lg font-bold shrink-0 border border-cyan-500/30">
                ⚡
            </div>
            <div>
                <p class="text-xs font-extrabold text-white">Rekomendasi Akun Neraca 1.5.2 (Permendagri 108)</p>
                <p class="text-[11px] text-cyan-300/80">Klik tombol cepat di samping untuk memilih klasifikasi standar kemitraan secara instan:</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-1.5 shrink-0">
            <button type="button" @click="quickSelectKemitraan('1.5.2.05')"
                class="px-3 py-1.5 rounded-xl bg-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 text-cyan-300 text-xs font-bold border border-cyan-500/30 transition-all cursor-pointer">
                1.5.2.05 KSO Alat Medis
            </button>
            <button type="button" @click="quickSelectKemitraan('1.5.2.01')"
                class="px-3 py-1.5 rounded-xl bg-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 text-cyan-300 text-xs font-bold border border-cyan-500/30 transition-all cursor-pointer">
                1.5.2.01 Sewa Mesin/Alat
            </button>
            <button type="button" @click="quickSelectKemitraan('1.5.2.02')"
                class="px-3 py-1.5 rounded-xl bg-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 text-cyan-300 text-xs font-bold border border-cyan-500/30 transition-all cursor-pointer">
                1.5.2.02 KSP Gedung/Ruangan
            </button>
            <button type="button" @click="quickSelectKemitraan('1.5.2.03')"
                class="px-3 py-1.5 rounded-xl bg-cyan-500/20 hover:bg-cyan-500 hover:text-slate-950 text-cyan-300 text-xs font-bold border border-cyan-500/30 transition-all cursor-pointer">
                1.5.2.03 KSO Operasional Alkes
            </button>
        </div>
    </div>

    <!-- Bagian Filter Kode Barang 108 -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-cyan-500/30 space-y-5 shadow-2xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🔍 Pemilihan Kode Barang Permendagri 108</span>
                <span class="text-rose-400">*</span>
            </span>
            <span x-show="selectedSubSub" class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-lg border border-emerald-500/30">
                ✓ Terpilih: <span class="font-mono" x-text="selectedSubSub?.kode"></span>
            </span>
        </div>

        <!-- Live Search Box Kode 108 -->
        <div class="relative">
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Pencarian Cepat Kode / Nama Barang 108
            </label>
            <div class="relative">
                <input type="text"
                    x-model="search108"
                    @input="performSearch108()"
                    placeholder="Ketik kata kunci atau kode 108 (contoh: 1.5.2, Kemitraan, Sewa, Hemodialisa, USG, Laboratorium, Analyzer, Gedung Parkir)..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-3 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none transition-colors">
                <svg class="w-4 h-4 text-cyan-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <button type="button" x-show="search108" @click="search108 = ''; searchResults108 = []"
                    class="absolute right-3.5 top-3 text-xs text-slate-400 hover:text-white cursor-pointer">✕</button>
            </div>

            <!-- Hasil Live Search Dropdown -->
            <div x-show="searchResults108.length > 0" class="absolute z-20 mt-1.5 w-full max-h-56 overflow-y-auto space-y-1 p-2 bg-slate-900/95 border border-cyan-500/40 rounded-2xl shadow-2xl backdrop-blur-xl custom-scrollbar">
                <template x-for="item in searchResults108" :key="item.id">
                    <button type="button" @click="selectFromSearch(item)"
                        class="w-full text-left p-2.5 rounded-xl hover:bg-cyan-500/20 text-xs flex items-center justify-between group transition-colors cursor-pointer">
                        <div class="truncate mr-3">
                            <span class="font-mono text-cyan-300 font-bold" x-text="item.kode"></span>
                            <span class="text-white ml-2" x-text="item.nama"></span>
                        </div>
                        <span class="text-[10px] text-slate-400 bg-slate-800 px-2 py-0.5 rounded-md group-hover:bg-cyan-500 group-hover:text-slate-950 font-bold shrink-0">
                            Pilih &rarr;
                        </span>
                    </button>
                </template>
            </div>
        </div>

        <!-- Cascading Dropdowns 108 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    1. Kelompok Aset / Akun 108
                </label>
                <select x-model="selectedJenisIdx" @change="onJenisChange()"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="">-- Pilih Kelompok Akun --</option>
                    <template x-for="(j, idx) in master108" :key="j.kode">
                        <option :value="idx" x-text="j.kode + ' - ' + j.nama"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    2. Objek Barang 108
                </label>
                <select x-model="selectedSubIdx" @change="onSubChange()" :disabled="!currentSubList.length"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 disabled:opacity-40">
                    <option value="">-- Pilih Objek Barang --</option>
                    <template x-for="(s, idx) in currentSubList" :key="s.kode">
                        <option :value="idx" x-text="s.kode + ' - ' + s.nama"></option>
                    </template>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    3. Sub-Sub Rincian Objek (Detail Kode Barang 108) <span class="text-rose-400">*</span>
                </label>
                <select @change="onSubSubChange($event)" :disabled="!currentSubSubList.length"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-400 disabled:opacity-40 font-mono">
                    <option value="">-- Pilih Sub-Sub Rincian Objek --</option>
                    <template x-for="ss in currentSubSubList" :key="ss.id">
                        <option :value="ss.id" :selected="selectedSubSub && selectedSubSub.id === ss.id" x-text="ss.kode + ' - ' + ss.nama"></option>
                    </template>
                </select>
            </div>
        </div>

        <!-- Nama Barang, Volume, Satuan, Total Taksiran Nilai Wajar -->
        <div class="pt-3 border-t border-slate-800 space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nama Spesifik Barang Aset Kemitraan <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.nama_barang" required
                    placeholder="Contoh: Automated Clinical Chemistry Analyzer Cobas c311 / Mesin Hemodialisis / Gedung Parkir..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none font-bold">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1.5">
                        Jumlah Volume / Unit <span class="text-rose-400">*</span>
                    </label>
                    <input type="number" x-model.number="formData.jumlah_volume" required min="1"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1.5">
                        Satuan Barang <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" x-model="formData.satuan" required
                        placeholder="Unit / Set / Buah / Gedung / Paket"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                </div>
            </div>

            <!-- Nilai Taksiran Wajar Aset Kemitraan -->
            <div class="p-5 rounded-2xl bg-slate-900 border border-cyan-500/30 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-200">
                        Total Taksiran Nilai Wajar Aset Kemitraan (Rp) <span class="text-rose-400">*</span>
                    </label>
                    <span class="text-xs font-mono text-cyan-300 font-extrabold" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></span>
                </div>

                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-slate-400 text-xs font-bold font-mono">Rp</span>
                    <input type="text"
                        :value="formData.total_realisasi ? Number(formData.total_realisasi).toLocaleString('id-ID') : ''"
                        @input="
                            let raw = $event.target.value.replace(/\D/g, '');
                            formData.total_realisasi = raw ? parseInt(raw, 10) : 0;
                            $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                        "
                        placeholder="0"
                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-cyan-300 font-bold font-mono focus:outline-none">
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-slate-400 pt-1">
                    <p>
                        💡 Taksiran nilai wajar aset sesuai klausul kontrak PKS atau appraisal wajar untuk Akun 1.5.2.
                    </p>
                    <template x-if="formData.jumlah_volume > 1 && formData.total_realisasi > 0">
                        <span class="text-cyan-400 font-mono font-semibold">
                            Taksiran/Unit: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / formData.jumlah_volume))"></span>
                        </span>
                    </template>
                </div>
            </div>

        </div>

    </div>

</div>
