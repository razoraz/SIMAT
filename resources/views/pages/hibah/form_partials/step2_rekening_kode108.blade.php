{{-- =========================================================================
     LANGKAH 2: KLASIFIKASI KODE BARANG 108 & SPESIFIKASI FISIK ASET (HIBAH)
     Design: Dark Luxury Amber — Mirror Kemitraan Step 2
     ========================================================================= --}}
<div x-show="currentStep === 2"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="space-y-6">

    {{-- Header Langkah 2 --}}
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-300 border border-amber-400/20 text-xs font-bold mb-2">
            <span>🔍 LANGKAH 2 DARI 3: KLASIFIKASI 108 &amp; SPESIFIKASI FISIK ASET HIBAH</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-amber-400/10 text-amber-400 text-sm">📊</span>
            <span>Langkah 2: Klasifikasi Kode Barang 108 &amp; Spesifikasi Fisik Aset Hibah</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Pilih klasifikasi kode barang Permendagri No. 108/2016 yang sesuai dengan aset hibah masuk. Pilih Jenis KIB → Sub Rincian → Nama Barang secara berjenjang.
        </p>
    </div>

    {{-- Banner Error Inline Langkah 2 --}}
    <template x-if="stepErrors[2]">
        <div class="flex items-start gap-3 p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 shadow-lg shadow-rose-500/10">
            <span class="text-rose-400 text-lg mt-0.5 shrink-0">⚠️</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-rose-300 mb-0.5">Perhatian — Data Langkah 2 Belum Lengkap</p>
                <p class="text-xs text-rose-200/90 leading-relaxed" x-text="stepErrors[2]"></p>
            </div>
            <button type="button" @click="clearStepError(2)" class="ml-auto shrink-0 text-rose-400 hover:text-rose-200 transition-colors text-sm leading-none">✕</button>
        </div>
    </template>

    {{-- ─── SHORTCUT: Pilih Kelompok KIB Aset Hibah ─────────────────────── --}}
    <div class="p-5 rounded-3xl bg-slate-900/80 border border-amber-500/30 backdrop-blur-md shadow-xl space-y-4 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-amber-400/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400/20 to-amber-600/10 text-amber-400 flex items-center justify-center text-lg font-bold shrink-0 border border-amber-500/30 shadow-inner">⚡</div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xs font-extrabold text-white tracking-wide">Pilih Kelompok Aset Hibah (Akun KIB 1.3.x)</h3>
                        <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 text-[10px] font-bold border border-amber-500/20">Permendagri 108</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-amber-400 text-[10px] font-bold border border-slate-700 font-mono" x-text="formData.jenis_aset_kode || '—'"></span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Kelompok KIB Aktif: <span class="font-bold text-amber-300" x-text="kibLabel || 'Belum dipilih'"></span> — Klik salah satu shortcut di bawah:
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                <span x-show="selectedSubSub" class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/30 inline-flex items-center gap-1.5 shadow-sm">
                    ✓ Terpilih: <span class="font-mono" x-text="selectedSubSub?.kode"></span>
                </span>
                <span x-show="!selectedSubSub" class="text-[11px] font-semibold text-amber-400 bg-amber-500/10 px-3 py-1.5 rounded-xl border border-amber-500/20 inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                    Pilih kategori aset hibah
                </span>
            </div>
        </div>

        {{-- Shortcut Grid: 5 Kelompok KIB --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            {{-- KIB A: Tanah --}}
            <button type="button"
                @click="quickSelectKib('1.3.1', 'TANAH')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-2 relative"
                :class="isTanah ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between">
                    <span class="text-xl">🏞️</span>
                    <span class="font-mono text-[10px] font-black text-amber-400">1.3.1</span>
                </div>
                <div>
                    <span class="block text-xs font-bold group-hover:text-amber-300 truncate">KIB A</span>
                    <span class="block text-[10px] text-slate-400">Tanah</span>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex justify-end">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold transition-colors"
                        :class="isTanah ? 'bg-amber-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isTanah">✓</span><span x-show="!isTanah">→</span>
                    </div>
                </div>
            </button>

            {{-- KIB B: Peralatan & Mesin --}}
            <button type="button"
                @click="quickSelectKib('1.3.2', 'PERALATAN DAN MESIN')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-2 relative"
                :class="isMesin ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between">
                    <span class="text-xl">⚙️</span>
                    <span class="font-mono text-[10px] font-black text-amber-400">1.3.2</span>
                </div>
                <div>
                    <span class="block text-xs font-bold group-hover:text-amber-300 truncate">KIB B</span>
                    <span class="block text-[10px] text-slate-400">Peralatan &amp; Mesin</span>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex justify-end">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold transition-colors"
                        :class="isMesin ? 'bg-amber-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isMesin">✓</span><span x-show="!isMesin">→</span>
                    </div>
                </div>
            </button>

            {{-- KIB C: Gedung & Bangunan --}}
            <button type="button"
                @click="quickSelectKib('1.3.3', 'GEDUNG DAN BANGUNAN')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-2 relative"
                :class="isGedung ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between">
                    <span class="text-xl">🏗️</span>
                    <span class="font-mono text-[10px] font-black text-amber-400">1.3.3</span>
                </div>
                <div>
                    <span class="block text-xs font-bold group-hover:text-amber-300 truncate">KIB C</span>
                    <span class="block text-[10px] text-slate-400">Gedung &amp; Bangunan</span>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex justify-end">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold transition-colors"
                        :class="isGedung ? 'bg-amber-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isGedung">✓</span><span x-show="!isGedung">→</span>
                    </div>
                </div>
            </button>

            {{-- KIB D: Jalan, Irigasi & Jaringan --}}
            <button type="button"
                @click="quickSelectKib('1.3.4', 'JALAN, IRIGASI DAN JARINGAN')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-2 relative"
                :class="isJaringan ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between">
                    <span class="text-xl">🛣️</span>
                    <span class="font-mono text-[10px] font-black text-amber-400">1.3.4</span>
                </div>
                <div>
                    <span class="block text-xs font-bold group-hover:text-amber-300 truncate">KIB D</span>
                    <span class="block text-[10px] text-slate-400">Jalan &amp; Jaringan</span>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex justify-end">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold transition-colors"
                        :class="isJaringan ? 'bg-amber-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isJaringan">✓</span><span x-show="!isJaringan">→</span>
                    </div>
                </div>
            </button>

            {{-- KIB E: Aset Tetap Lainnya --}}
            <button type="button"
                @click="quickSelectKib('1.3.5', 'ASET TETAP LAINNYA')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-2 relative"
                :class="isAsetLainnya ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between">
                    <span class="text-xl">📦</span>
                    <span class="font-mono text-[10px] font-black text-amber-400">1.3.5</span>
                </div>
                <div>
                    <span class="block text-xs font-bold group-hover:text-amber-300 truncate">KIB E</span>
                    <span class="block text-[10px] text-slate-400">Aset Lainnya</span>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex justify-end">
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold transition-colors"
                        :class="isAsetLainnya ? 'bg-amber-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isAsetLainnya">✓</span><span x-show="!isAsetLainnya">→</span>
                    </div>
                </div>
            </button>
        </div>
    </div>

    {{-- ─── FILTER BERTINGKAT PMDN 108 ────────────────────────────────────── --}}
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/30 space-y-5 shadow-2xl">

        {{-- Tingkat 1: JENIS ASET PMDN 108 --}}
        <div class="space-y-2 relative" @click.away="isJenis108Open = false">
            <div class="flex items-center justify-between">
                <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-black flex items-center justify-center">1</span>
                    <span>Jenis Aset PMDN 108 (Kelompok Aset)</span>
                    <span class="text-rose-400">*</span>
                </label>
                <button type="button"
                    x-show="formData.jenis_aset_kode && !isJenis108Open"
                    @click="isJenis108Open = true; searchJenis108 = ''"
                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                    <span>✕ Ganti Jenis Aset</span>
                </button>
            </div>
            <div class="relative">
                <input type="text"
                    :value="(!isJenis108Open && formData.jenis_aset_kode) ? (formData.jenis_aset_kode + ' - ' + formData.jenis_aset_nama) : searchJenis108"
                    @input="searchJenis108 = $event.target.value; isJenis108Open = true"
                    @focus="isJenis108Open = true"
                    :placeholder="formData.jenis_aset_kode ? (formData.jenis_aset_kode + ' - ' + formData.jenis_aset_nama) : 'Ketik untuk memfilter kode / nama jenis PMDN 108 (contoh: 1.3.2, PERALATAN, TANAH)...'"
                    class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                    :class="formData.jenis_aset_kode && !isJenis108Open ? 'border-amber-500/60 text-amber-200' : 'border-amber-500/40 text-white focus:border-amber-400'">
                <svg class="w-4 h-4 text-amber-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>
            <div x-show="isJenis108Open" x-transition x-cloak
                style="max-height: 195px !important; overflow-y: auto !important;"
                class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                <template x-for="j in filteredJenisAstap108" :key="j.kode">
                    <div @click="selectJenisAstap(j)"
                        class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                        :class="j.kode === formData.jenis_aset_kode ? 'border-amber-500 bg-amber-950/40 shadow-lg' : 'border-slate-800 hover:border-amber-500/50'">
                        <div class="min-w-0 pr-3">
                            <h4 class="text-xs font-bold text-white group-hover:text-amber-300 transition-colors truncate" x-text="j.kode + ' - ' + j.nama"></h4>
                            <p class="text-[10px] text-slate-400 truncate">PMDN 108 • Kode Kelompok Permendagri 108</p>
                        </div>
                        <button type="button" @click.stop="selectJenisAstap(j)"
                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                            :class="j.kode === formData.jenis_aset_kode ? 'bg-amber-500 text-slate-950 shadow-lg shadow-amber-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/40 hover:bg-amber-500 hover:text-slate-950'">
                            <span x-text="j.kode === formData.jenis_aset_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                        </button>
                    </div>
                </template>
                <template x-if="isJenis108Open && filteredJenisAstap108.length === 0">
                    <div class="p-3 text-center text-xs text-slate-400 italic">Tidak ada jenis aset yang cocok.</div>
                </template>
            </div>
        </div>

        {{-- Tingkat 2: SUB RINCIAN OBJEK PMDN 108 --}}
        <div class="space-y-2 relative" @click.away="isSubRincian108Open = false">
            <div class="flex items-center justify-between">
                <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-black flex items-center justify-center">2</span>
                    <span>Sub Rincian Objek PMDN 108</span>
                    <span class="text-slate-400 font-normal">(Opsional – Ketik untuk mempersempit)</span>
                </label>
                <button type="button"
                    x-show="formData.sub_rincian_kode && !isSubRincian108Open"
                    @click="formData.sub_rincian_kode = ''; formData.sub_rincian_nama = ''; selectedSubSub = null; isSubRincian108Open = true; searchSubRincian108 = ''"
                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors cursor-pointer">
                    ✕ Reset Sub Rincian
                </button>
            </div>
            <div class="relative">
                <input type="text"
                    :value="(!isSubRincian108Open && formData.sub_rincian_kode) ? (formData.sub_rincian_kode + ' - ' + formData.sub_rincian_nama) : searchSubRincian108"
                    @input="searchSubRincian108 = $event.target.value; isSubRincian108Open = true"
                    @focus="isSubRincian108Open = true"
                    :placeholder="formData.jenis_aset_kode ? 'Ketik untuk filter sub rincian dari ' + formData.jenis_aset_kode : 'Pilih Jenis Aset dahulu...'"
                    class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                    :class="formData.sub_rincian_kode && !isSubRincian108Open ? 'border-emerald-500/60 text-emerald-200' : 'border-slate-700 text-white focus:border-emerald-500/60'">
                <svg class="w-4 h-4 text-emerald-500 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </div>
            <div x-show="isSubRincian108Open" x-transition x-cloak
                style="max-height: 210px !important; overflow-y: auto !important;"
                class="absolute z-20 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-emerald-500/40 rounded-2xl shadow-2xl backdrop-blur-xl">
                <template x-for="s in filteredSubRincian108" :key="s.kode">
                    <div @click="selectSubRincian(s)"
                        class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                        :class="s.kode === formData.sub_rincian_kode ? 'border-emerald-500 bg-emerald-950/40' : 'border-slate-800 hover:border-emerald-500/40'">
                        <div class="min-w-0 pr-3">
                            <h4 class="text-xs font-bold text-white group-hover:text-emerald-300 truncate" x-text="s.kode + ' - ' + s.nama"></h4>
                        </div>
                        <button type="button" @click.stop="selectSubRincian(s)"
                            class="shrink-0 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                            :class="s.kode === formData.sub_rincian_kode ? 'bg-emerald-500 text-slate-950' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500 hover:text-slate-950'">
                            <span x-text="s.kode === formData.sub_rincian_kode ? '✓' : '→'"></span>
                        </button>
                    </div>
                </template>
                <template x-if="isSubRincian108Open && filteredSubRincian108.length === 0">
                    <div class="p-3 text-center text-xs text-slate-400 italic">Tidak ada sub rincian ditemukan.</div>
                </template>
            </div>
        </div>

        {{-- Tingkat 3: NAMA BARANG (Sub-Sub Rincian) --}}
        <div class="space-y-2 relative" @click.away="isNamaBarang108Open = false">
            <div class="flex items-center justify-between">
                <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[10px] font-black flex items-center justify-center">3</span>
                    <span>Nama Barang (Sub-Sub Rincian 108 / ID Jenis ASTAP)</span>
                    <span class="text-rose-400">*</span>
                </label>
                <button type="button"
                    x-show="selectedSubSub && !isNamaBarang108Open"
                    @click="selectedSubSub = null; formData.jenis_astap_id = null; formData.nama_barang = ''; isNamaBarang108Open = true; searchNamaBarang108 = ''"
                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors cursor-pointer">
                    ✕ Ganti Nama Barang
                </button>
            </div>
            <div class="relative">
                <input type="text"
                    :value="(!isNamaBarang108Open && selectedSubSub) ? (selectedSubSub.kode + ' ● ' + selectedSubSub.nama) : searchNamaBarang108"
                    @input="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true; formData.nama_barang = $event.target.value"
                    @focus="isNamaBarang108Open = true"
                    :placeholder="formData.sub_rincian_kode ? ('Ketik nama spesifik dari sub-rincian ' + formData.sub_rincian_kode + ' (contoh: Ventilator, Tempat Tidur Pasien)...') : 'Ketik nama barang (contoh: Ventilator, USG, Meja Operasi, Genset...)'"
                    class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                    :class="selectedSubSub && !isNamaBarang108Open ? 'border-purple-500/60 text-purple-200' : 'border-slate-700 text-white focus:border-purple-500/60'">
                <svg class="w-4 h-4 text-purple-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
            </div>
            <div x-show="isNamaBarang108Open" x-transition x-cloak
                style="max-height: 220px !important; overflow-y: auto !important;"
                class="absolute z-10 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-purple-500/40 rounded-2xl shadow-2xl backdrop-blur-xl">
                <template x-for="item in filteredSubSubRincian108" :key="item.id">
                    <div @click="selectSubSubRincianItem(item)"
                        class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                        :class="selectedSubSub?.id === item.id ? 'border-purple-500 bg-purple-950/40' : 'border-slate-800 hover:border-purple-500/40'">
                        <div class="min-w-0 pr-3">
                            <h4 class="text-xs font-bold text-white group-hover:text-purple-300 truncate" x-text="item.kode + ' — ' + item.nama"></h4>
                            <p class="text-[10px] text-slate-400 font-mono" x-text="'ID: ' + item.id + ' • Kode 108: ' + item.kode"></p>
                        </div>
                        <button type="button" @click.stop="selectSubSubRincianItem(item)"
                            class="shrink-0 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                            :class="selectedSubSub?.id === item.id ? 'bg-purple-500 text-white' : 'bg-purple-500/20 text-purple-300 border border-purple-500/40 hover:bg-purple-500 hover:text-white'">
                            <span x-text="selectedSubSub?.id === item.id ? '✓ Terpilih' : 'Pilih →'"></span>
                        </button>
                    </div>
                </template>
                <template x-if="isNamaBarang108Open && filteredSubSubRincian108.length === 0">
                    <div class="p-4 text-center">
                        <p class="text-xs text-slate-400 italic mb-2">Nama barang ini tidak ada di database 108.</p>
                        <p class="text-[10px] text-amber-400 font-semibold">💡 Ketik nama barang secara manual di field input di atas, maka akan disimpan dengan kode induk yang terpilih.</p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Tingkat 4: Nama Barang Manual + Ekstrakomtabel --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center gap-2">
                    <span class="w-5 h-5 rounded-full bg-slate-700 text-slate-300 border border-slate-600 text-[10px] font-black flex items-center justify-center">4</span>
                    Nama Lengkap Barang Hibah <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.nama_barang" required
                    placeholder="Nama lengkap barang aset hibah..."
                    class="w-full bg-slate-950/90 border border-amber-500/30 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-amber-100 font-bold focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">Kategori Pencatatan</label>
                <div class="flex items-center gap-3 mt-3">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <div class="w-10 h-5 rounded-full relative transition-colors cursor-pointer"
                            :class="formData.is_extracomtable ? 'bg-amber-500' : 'bg-slate-700'"
                            @click="formData.is_extracomtable = !formData.is_extracomtable">
                            <div class="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-all"
                                :class="formData.is_extracomtable ? 'left-5' : 'left-0.5'"></div>
                        </div>
                        <span class="text-xs font-bold transition-colors"
                            :class="formData.is_extracomtable ? 'text-amber-300' : 'text-slate-400'">
                            <span x-show="formData.is_extracomtable">📦 Ekstrakomtabel (≤ Rp 300.000)</span>
                            <span x-show="!formData.is_extracomtable">✅ Aset Reguler (Kapitalisasi)</span>
                        </span>
                    </label>
                </div>
            </div>
        </div>

        {{-- Live Preview Card --}}
        <template x-if="formData.jenis_aset_kode">
            <div class="p-3 rounded-2xl bg-amber-950/40 border border-amber-500/30 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm shrink-0">🎁</span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Klasifikasi Terpilih</p>
                        <p class="text-xs font-bold text-white truncate" x-text="(selectedSubSub?.kode || formData.jenis_aset_kode) + ' — ' + (formData.nama_barang || formData.jenis_aset_nama)"></p>
                        <p class="text-[10px] text-slate-400 font-mono" x-text="'KIB: ' + kibLabel + ' • Kategori: ' + (formData.is_extracomtable ? 'Ekstrakomtabel' : 'Reguler')"></p>
                    </div>
                </div>
                <span class="shrink-0 px-2.5 py-1 rounded-lg text-[10px] font-black border"
                    :class="formData.is_extracomtable ? 'bg-amber-100/10 text-amber-400 border-amber-500/30' : 'bg-emerald-100/10 text-emerald-400 border-emerald-500/30'">
                    <span x-text="formData.is_extracomtable ? '📦 Ekstrakom' : '✅ Reguler'"></span>
                </span>
            </div>
        </template>
    </div>

    {{-- ─── LEMBAR SPESIFIKASI FISIK DINAMIS (KIB A - E + ATB + KDP) ──────── --}}
    @include('pages.hibah.form_partials.step3_rincian.kib_a_tanah')
    @include('pages.hibah.form_partials.step3_rincian.kib_b_peralatan_mesin')
    @include('pages.hibah.form_partials.step3_rincian.kib_c_gedung_bangunan')
    @include('pages.hibah.form_partials.step3_rincian.kib_d_jaringan_irigasi')
    @include('pages.hibah.form_partials.step3_rincian.kib_e_aset_lainnya')
    @include('pages.hibah.form_partials.step3_rincian.atb_aset_tak_berwujud')
    @include('pages.hibah.form_partials.step3_rincian.kib_f_kdp')
    @include('pages.hibah.form_partials.step3_rincian.kategori_lainnya')

    {{-- ─── RINCIAN VOLUME & NILAI TAKSIRAN ASET HIBAH ─────────────────────── --}}
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/30 space-y-5 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📝 Volume &amp; Taksiran Nilai Aset Hibah</span>
                <span class="text-rose-400">*</span>
            </span>
            <template x-if="selectedSubSub">
                <span class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-lg border border-emerald-500/30 font-mono" x-text="selectedSubSub.kode + ' • ' + selectedSubSub.nama"></span>
            </template>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Total Volume --}}
            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2.5 shadow-md">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                        <span>Total Volume / Kuantitas Aset <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-amber-400 font-bold font-mono" x-show="isMultiItemActive">🔒 Akumulasi Rincian</span>
                    </label>
                    <span class="text-xs font-mono text-amber-300 font-extrabold" x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></span>
                </div>
                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-2">
                        <input type="number" x-model.number="formData.jumlah_volume" :readonly="isMultiItemActive" required min="1"
                            :class="isMultiItemActive ? 'bg-slate-950/70 border-slate-800 text-amber-300 cursor-not-allowed' : 'bg-slate-950 border-slate-700 text-white'"
                            class="w-full border focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs focus:outline-none font-bold font-mono">
                    </div>
                    <div>
                        <input type="text" x-model="formData.satuan" required placeholder="Unit"
                            :readonly="isMultiItemActive"
                            :class="isMultiItemActive ? 'bg-slate-950/70 border-slate-800 text-slate-300 cursor-not-allowed' : 'bg-slate-950 border-slate-700 text-white'"
                            class="w-full border focus:border-amber-400 rounded-xl px-3 py-2.5 text-xs text-center font-bold focus:outline-none">
                    </div>
                </div>
                <p class="text-[11px] text-slate-400 pt-1 leading-relaxed">
                    <span x-show="isMultiItemActive">💡 Total volume dihitung otomatis dari akumulasi rincian barang/unit.</span>
                    <span x-show="!isMultiItemActive">Jumlah unit/satuan fisik aset hibah yang diterima.</span>
                </p>
            </div>

            {{-- Total Nilai Taksiran --}}
            <div class="p-5 rounded-2xl bg-slate-900/90 border border-amber-500/30 space-y-2.5 shadow-md">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                        <span>Total Taksiran Nilai Aset Hibah (Rp) <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-emerald-400 font-bold font-mono" x-show="isMultiItemActive && formData.total_realisasi > 0">🔒 Akumulasi Otomatis</span>
                    </label>
                    <span class="text-xs font-mono text-amber-300 font-extrabold" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></span>
                </div>
                <div class="relative">
                    <span class="absolute left-3.5 top-2.5 text-slate-400 text-xs font-bold font-mono">Rp</span>
                    <input type="text"
                        :value="formData.total_realisasi ? Number(formData.total_realisasi).toLocaleString('id-ID') : ''"
                        :readonly="isMultiItemActive"
                        @input="
                            let raw = $event.target.value.replace(/\D/g, '');
                            formData.total_realisasi = raw ? parseInt(raw, 10) : 0;
                            $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                        "
                        :class="isMultiItemActive ? 'bg-slate-950/70 border-slate-800 text-emerald-400 cursor-not-allowed' : 'bg-slate-950 border-slate-700 text-amber-300'"
                        placeholder="0"
                        class="w-full border focus:border-amber-400 rounded-xl px-4 py-2.5 pl-10 text-xs font-bold font-mono focus:outline-none">
                </div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-slate-400 pt-1">
                    <p>
                        <span x-show="isMultiItemActive">💡 Total nilai hibah akumulasi dari rincian spesifikasi fisik.</span>
                        <span x-show="!isMultiItemActive">Taksiran nilai wajar aset hibah sesuai BAST / appraisal.</span>
                    </p>
                    <template x-if="isMultiItemActive && formData.jumlah_volume > 1 && formData.total_realisasi > 0">
                        <span class="text-amber-400 font-mono font-semibold">
                            Rata-rata: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / formData.jumlah_volume))"></span>
                        </span>
                    </template>
                </div>
            </div>
        </div>
    </div>

</div>
