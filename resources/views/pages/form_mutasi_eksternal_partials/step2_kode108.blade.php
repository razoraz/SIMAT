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
            Tentukan klasifikasi 108 agar NIBAR, register buku barang, dan kodefikasi aset RSUD sinkron.
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
                placeholder="Ketik nama atau kode barang (contoh: Ambulance, Bed, USG, Komputer, Meja Rapat, Tanah)..."
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
                <label class="block text-[11px] font-bold text-slate-300 mb-1">1. Kelompok KIB Utama</label>
                <select x-model="selectedJenisIdx" @change="onJenisChange()"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-400">
                    <option value="">-- Pilih Kelompok KIB --</option>
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
                <label class="block text-[11px] font-bold text-slate-300 mb-1">3. Sub-Sub Rincian Objek (Detail Barang 108)</label>
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

    <!-- Input Nama Barang, Volume, Satuan -->
    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Nama Lengkap Barang <span class="text-rose-400">*</span>
            </label>
            <input type="text" x-model="formData.nama_barang" required
                placeholder="Contoh: Mobil Ambulance Transportasi Suzuki APV / Tempat Tidur Pasien"
                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-semibold">
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Jumlah Volume <span class="text-rose-400">*</span>
                </label>
                <input type="number" x-model.number="formData.jumlah_volume" min="1" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none font-bold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Satuan <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.satuan" required placeholder="Unit, Set, Buah, Bidang..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nilai Satuan (Otomatis)
                </label>
                <input type="text" readonly :value="formatRupiah(hargaSatuanHitung)"
                    class="w-full bg-slate-950 border border-slate-800 rounded-xl px-4 py-2.5 text-xs text-emerald-400 font-mono font-bold focus:outline-none">
            </div>
        </div>
    </div>

</div>
