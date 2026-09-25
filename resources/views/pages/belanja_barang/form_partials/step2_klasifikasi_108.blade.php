<!-- ========================================================================= -->
<!-- LANGKAH 2: KLASIFIKASI KODE BARANG 108                                    -->
<!-- ========================================================================= -->
<div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div class="border-b border-slate-800 pb-4">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-xs font-bold mb-2">
            <span>Langkah 2 dari 3</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span>🔍 Klasifikasi Kode Barang (Permendagri 108)</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">
            Tentukan klasifikasi barang Permendagri 108 agar NIBAR, QR code, dan buku inventaris terbuat dengan benar.
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
                placeholder="Ketik nama atau kode barang (contoh: Tensimeter, Bed Pasien, Kursi Roda, USG, Laptop, AC, Lemari)..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
            <svg class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <button type="button" x-show="search108" @click="search108 = ''; searchResults108 = []"
                class="absolute right-3 top-2.5 text-xs text-slate-400 hover:text-white">✕</button>
        </div>

        <!-- Hasil Pencarian Langsung -->
        <div x-show="searchResults108.length > 0" class="max-h-48 overflow-y-auto rounded-xl border border-cyan-500/30 bg-slate-900 divide-y divide-slate-800 custom-scrollbar">
            <template x-for="item in searchResults108" :key="item.id">
                <div @click="selectSearchResult108(item)"
                    class="p-3 hover:bg-cyan-500/10 cursor-pointer transition-colors flex items-center justify-between text-xs">
                    <div>
                        <span class="font-bold text-white" x-text="item.nama"></span>
                        <span class="text-cyan-400 font-mono text-[11px] block mt-0.5" x-text="item.kode"></span>
                    </div>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 font-bold shrink-0">Pilih</span>
                </div>
            </template>
        </div>

        <!-- Dropdown Cascading Klasifikasi 108 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
            <!-- 1. Akun/Kelompok -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">1. Kelompok Aset (Akun)</label>
                <select x-model="selectedAkunKode" @change="onAkunChange()"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    <option value="">-- Pilih Kelompok --</option>
                    <template x-for="item in master108" :key="item.kode">
                        <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                    </template>
                </select>
            </div>

            <!-- 2. Jenis -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">2. Jenis Aset</label>
                <select x-model="selectedJenisKode" @change="onJenisChange()" :disabled="!jenisList.length"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                    <option value="">-- Pilih Jenis --</option>
                    <template x-for="item in jenisList" :key="item.kode">
                        <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                    </template>
                </select>
            </div>

            <!-- 3. Objek -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">3. Objek Aset</label>
                <select x-model="selectedObjekKode" @change="onObjekChange()" :disabled="!objekList.length"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                    <option value="">-- Pilih Objek --</option>
                    <template x-for="item in objekList" :key="item.kode">
                        <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                    </template>
                </select>
            </div>

            <!-- 4. Rincian Objek -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 mb-1">4. Rincian Objek</label>
                <select x-model="selectedRincianKode" @change="onRincianChange()" :disabled="!rincianList.length"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none disabled:opacity-50">
                    <option value="">-- Pilih Rincian --</option>
                    <template x-for="item in rincianList" :key="item.kode">
                        <option :value="item.kode" x-text="item.kode + ' - ' + item.nama"></option>
                    </template>
                </select>
            </div>
        </div>

        <!-- 5. Sub-Sub Rincian (Final Level) -->
        <div class="pt-2">
            <label class="block text-xs font-bold text-emerald-400 mb-1.5">
                5. Sub-Sub Rincian Objek (Level Akhir) <span class="text-rose-400">*</span>
            </label>
            <select x-model="selectedSubSubId" @change="onSubSubChange()" :disabled="!subSubList.length"
                class="w-full bg-slate-900 border border-emerald-500/40 focus:border-emerald-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none disabled:opacity-50 font-bold">
                <option value="">-- Pilih Sub-Sub Rincian Akhir --</option>
                <template x-for="item in subSubList" :key="item.id">
                    <option :value="item.id" x-text="item.kode + ' - ' + item.nama"></option>
                </template>
            </select>
        </div>

        <!-- Preview Badge Klasifikasi Terpilih -->
        <div x-show="selectedSubSub" class="p-3.5 rounded-xl bg-slate-900 border border-emerald-500/30 flex items-center justify-between text-xs">
            <div>
                <span class="text-[10px] text-slate-400 uppercase font-semibold block">Kode 108 Terpilih</span>
                <span class="font-mono font-bold text-emerald-400" x-text="selectedSubSub?.kode"></span>
                <span class="text-slate-300 ml-2" x-text="selectedSubSub?.nama"></span>
            </div>
            <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-300 text-[10px] font-bold border border-emerald-500/20"
                x-text="'KIB ' + detectedKib"></span>
        </div>
    </div>

    <!-- Nama Barang & Volume -->
    <div class="grid grid-cols-1 sm:grid-cols-12 gap-4 p-5 rounded-2xl bg-slate-950/70 border border-slate-800">
        <div class="sm:col-span-8">
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Nama Barang Spesifik <span class="text-rose-400">*</span>
            </label>
            <input type="text" x-model="formData.nama_barang" required
                placeholder="Contoh: Tensimeter Digital Ruang Melati, Kursi Roda Lipat..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Jumlah Volume <span class="text-rose-400">*</span>
            </label>
            <input type="number" x-model.number="formData.jumlah_volume" min="1" required
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono text-center focus:outline-none font-bold">
        </div>

        <div class="sm:col-span-2">
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Satuan <span class="text-rose-400">*</span>
            </label>
            <input type="text" x-model="formData.satuan" required
                placeholder="Unit/Pcs"
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white text-center focus:outline-none">
        </div>
    </div>

</div>
