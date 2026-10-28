{{-- =========================================================================
     LANGKAH 2: KLASIFIKASI KODE BARANG 108 & RINCIAN SPESIFIKASI BARANG (HIBAH)
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
            <span>🎁 LANGKAH 2 DARI 3: KLASIFIKASI 108 &amp; RINCIAN SPESIFIKASI BARANG HIBAH</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-amber-400/10 text-amber-400 text-sm">📊</span>
            <span>Langkah 2: Klasifikasi Kode 108 &amp; Rincian Spesifikasi Barang Hibah</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Pilih klasifikasi kode barang Permendagri No. 108/2016 yang sesuai dengan aset hibah masuk. Pilih Jenis KIB &rarr; Sub Rincian &rarr; Nama Barang secara berjenjang serta rincian spesifikasi fisik dan taksiran nilai wajar perolehan.
        </p>
    </div>

    {{-- ─── SHORTCUT: Pilih Kelompok KIB Aset Hibah ─────────────────────── --}}
    <div class="p-5 rounded-3xl bg-slate-900/80 border border-amber-500/30 backdrop-blur-md shadow-xl space-y-4 relative overflow-hidden transition-all">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-amber-400/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-amber-400/20 to-amber-600/10 text-amber-400 flex items-center justify-center text-lg font-bold shrink-0 border border-amber-500/30 shadow-inner">⚡</div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xs font-extrabold text-white tracking-wide">Pilih Objek Kategori KIB Aset Hibah</h3>
                        <span class="px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 text-[10px] font-bold border border-amber-500/20 font-mono">Permendagri 108</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-amber-400 text-[10px] font-bold border border-slate-700 font-mono" x-text="kibLabel || 'KIB A — E'"></span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Kelompok KIB Aktif: <span class="font-bold text-amber-300" x-text="kibLabel || 'Belum dipilih'"></span> &mdash; Silakan pilih salah satu kategori KIB objek aset hibah di bawah:
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                <span x-show="formData.jenis_aset_kode" class="text-[11px] font-bold text-amber-400 bg-amber-500/10 px-3 py-1.5 rounded-xl border border-amber-500/30 inline-flex items-center gap-1.5 shadow-sm">
                    ✓ Terpilih: <span class="font-mono" x-text="formData.jenis_aset_kode"></span>
                </span>
                <span x-show="!formData.jenis_aset_kode" class="text-[11px] font-semibold text-amber-400 bg-amber-500/10 px-3 py-1.5 rounded-xl border border-amber-500/20 inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                    Pilih kategori aset hibah
                </span>
            </div>
        </div>

        {{-- Shortcut Grid: 5 Kelompok KIB --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            {{-- KIB A: Tanah --}}
            <button type="button"
                @click="quickSelectKib('1.3.1', 'TANAH')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                :class="isTanah ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-center text-lg shrink-0 shadow-inner"
                         :class="isTanah ? 'border-amber-400/50 bg-amber-950/60' : ''">
                        🌾
                    </div>
                    <div class="shrink-0 flex items-center">
                        <span x-show="isTanah" class="text-[9px] px-2 py-0.5 rounded-full bg-amber-950 border border-amber-500/50 text-amber-300 font-mono font-extrabold shadow-sm">✓ Terpilih</span>
                    </div>
                </div>
                <div class="min-w-0 flex-1 space-y-1 pt-0.5">
                    <div>
                        <span class="font-mono text-[10px] font-extrabold tracking-tight block truncate text-amber-400 group-hover:text-amber-300">1.3.1 (KIB A)</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold truncate leading-tight text-slate-100 group-hover:text-white">Tanah &amp; Lahan</span>
                        <span class="block text-[10px] line-clamp-2 mt-0.5 leading-snug text-slate-400">Bidang tanah, pekarangan &amp; tapak bangunan RSUD</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-[9px] font-mono text-slate-500">KIB A</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] transition-colors font-bold"
                        :class="isTanah ? 'bg-amber-400 text-slate-950 font-black shadow-md shadow-amber-400/30' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isTanah">✓</span>
                        <span x-show="!isTanah">&rarr;</span>
                    </div>
                </div>
            </button>

            {{-- KIB B: Peralatan & Mesin --}}
            <button type="button"
                @click="quickSelectKib('1.3.2', 'PERALATAN DAN MESIN')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                :class="isMesin ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-center text-lg shrink-0 shadow-inner"
                         :class="isMesin ? 'border-amber-400/50 bg-amber-950/60' : ''">
                        ⚙️
                    </div>
                    <div class="shrink-0 flex items-center">
                        <span x-show="isMesin" class="text-[9px] px-2 py-0.5 rounded-full bg-amber-950 border border-amber-500/50 text-amber-300 font-mono font-extrabold shadow-sm">✓ Terpilih</span>
                    </div>
                </div>
                <div class="min-w-0 flex-1 space-y-1 pt-0.5">
                    <div>
                        <span class="font-mono text-[10px] font-extrabold tracking-tight block truncate text-amber-400 group-hover:text-amber-300">1.3.2 (KIB B)</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold truncate leading-tight text-slate-100 group-hover:text-white">Peralatan &amp; Mesin</span>
                        <span class="block text-[10px] line-clamp-2 mt-0.5 leading-snug text-slate-400">Alat medis, lab, instrumen RS, genset &amp; kendaraan</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-[9px] font-mono text-slate-500">KIB B</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] transition-colors font-bold"
                        :class="isMesin ? 'bg-amber-400 text-slate-950 font-black shadow-md shadow-amber-400/30' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isMesin">✓</span>
                        <span x-show="!isMesin">&rarr;</span>
                    </div>
                </div>
            </button>

            {{-- KIB C: Gedung & Bangunan --}}
            <button type="button"
                @click="quickSelectKib('1.3.3', 'GEDUNG DAN BANGUNAN')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                :class="isGedung ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-center text-lg shrink-0 shadow-inner"
                         :class="isGedung ? 'border-amber-400/50 bg-amber-950/60' : ''">
                        🏢
                    </div>
                    <div class="shrink-0 flex items-center">
                        <span x-show="isGedung" class="text-[9px] px-2 py-0.5 rounded-full bg-amber-950 border border-amber-500/50 text-amber-300 font-mono font-extrabold shadow-sm">✓ Terpilih</span>
                    </div>
                </div>
                <div class="min-w-0 flex-1 space-y-1 pt-0.5">
                    <div>
                        <span class="font-mono text-[10px] font-extrabold tracking-tight block truncate text-amber-400 group-hover:text-amber-300">1.3.3 (KIB C)</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold truncate leading-tight text-slate-100 group-hover:text-white">Gedung &amp; Bangunan</span>
                        <span class="block text-[10px] line-clamp-2 mt-0.5 leading-snug text-slate-400">Gedung paviliun, rawat inap, klinik &amp; selasar</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-[9px] font-mono text-slate-500">KIB C</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] transition-colors font-bold"
                        :class="isGedung ? 'bg-amber-400 text-slate-950 font-black shadow-md shadow-amber-400/30' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isGedung">✓</span>
                        <span x-show="!isGedung">&rarr;</span>
                    </div>
                </div>
            </button>

            {{-- KIB D: Jalan, Irigasi & Jaringan --}}
            <button type="button"
                @click="quickSelectKib('1.3.4', 'JALAN, IRIGASI DAN JARINGAN')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                :class="isJaringan ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-center text-lg shrink-0 shadow-inner"
                         :class="isJaringan ? 'border-amber-400/50 bg-amber-950/60' : ''">
                        🛣️
                    </div>
                    <div class="shrink-0 flex items-center">
                        <span x-show="isJaringan" class="text-[9px] px-2 py-0.5 rounded-full bg-amber-950 border border-amber-500/50 text-amber-300 font-mono font-extrabold shadow-sm">✓ Terpilih</span>
                    </div>
                </div>
                <div class="min-w-0 flex-1 space-y-1 pt-0.5">
                    <div>
                        <span class="font-mono text-[10px] font-extrabold tracking-tight block truncate text-amber-400 group-hover:text-amber-300">1.3.4 (KIB D)</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold truncate leading-tight text-slate-100 group-hover:text-white">Jalan &amp; Jaringan</span>
                        <span class="block text-[10px] line-clamp-2 mt-0.5 leading-snug text-slate-400">Instalasi gas medis, listrik PLN, pipa air &amp; jalan</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-[9px] font-mono text-slate-500">KIB D</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] transition-colors font-bold"
                        :class="isJaringan ? 'bg-amber-400 text-slate-950 font-black shadow-md shadow-amber-400/30' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isJaringan">✓</span>
                        <span x-show="!isJaringan">&rarr;</span>
                    </div>
                </div>
            </button>

            {{-- KIB E: Aset Tetap Lainnya --}}
            <button type="button"
                @click="quickSelectKib('1.3.5', 'ASET TETAP LAINNYA')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                :class="isAsetLainnya ? 'bg-amber-500/20 border-amber-400 text-white shadow-xl shadow-amber-500/20 ring-2 ring-amber-400' : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-amber-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-1.5">
                    <div class="w-8 h-8 rounded-xl bg-slate-900/80 border border-slate-800/80 flex items-center justify-center text-lg shrink-0 shadow-inner"
                         :class="isAsetLainnya ? 'border-amber-400/50 bg-amber-950/60' : ''">
                        📦
                    </div>
                    <div class="shrink-0 flex items-center">
                        <span x-show="isAsetLainnya" class="text-[9px] px-2 py-0.5 rounded-full bg-amber-950 border border-amber-500/50 text-amber-300 font-mono font-extrabold shadow-sm">✓ Terpilih</span>
                    </div>
                </div>
                <div class="min-w-0 flex-1 space-y-1 pt-0.5">
                    <div>
                        <span class="font-mono text-[10px] font-extrabold tracking-tight block truncate text-amber-400 group-hover:text-amber-300">1.3.5 (KIB E)</span>
                    </div>
                    <div>
                        <span class="block text-xs font-bold truncate leading-tight text-slate-100 group-hover:text-white">Aset Lainnya</span>
                        <span class="block text-[10px] line-clamp-2 mt-0.5 leading-snug text-slate-400">Buku kedokteran, arsip, barang kesenian &amp; koleksi</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                    <span class="text-[9px] font-mono text-slate-500">KIB E</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] transition-colors font-bold"
                        :class="isAsetLainnya ? 'bg-amber-400 text-slate-950 font-black shadow-md shadow-amber-400/30' : 'bg-slate-800 text-slate-400 group-hover:bg-amber-400 group-hover:text-slate-950'">
                        <span x-show="isAsetLainnya">✓</span>
                        <span x-show="!isAsetLainnya">&rarr;</span>
                    </div>
                </div>
            </button>
        </div>

        {{-- Pilihan Status Akuntansi: Aset Tetap Reguler vs Ekstrakomtabel (Extracom) --}}
        {{-- Khusus untuk Peralatan & Mesin (KIB B) dan Aset Tetap Lainnya (KIB E) --}}
        <div x-show="isMesin || isAsetLainnya" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="pt-3 border-t border-slate-800/80">
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-3 shadow-inner">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider flex items-center space-x-2"
                           :class="formData.is_extracomtable ? 'text-amber-300' : 'text-purple-300'">
                        <span x-text="formData.is_extracomtable ? '📦 STATUS AKUNTANSI: EKSTRAKOMTABEL (EXTRACOM)' : '⚙️ STATUS AKUNTANSI: ASET TETAP REGULER (INTRAKOMPTABEL)'"></span>
                    </label>
                    <span class="text-[9.5px] px-2.5 py-0.5 rounded-lg font-bold font-mono border"
                          :class="formData.is_extracomtable ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                          x-text="formData.is_extracomtable ? '≤ Rp 300.000' : '> Rp 300.000'">
                    </span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    {{-- 1. Aset Tetap Reguler --}}
                    <button type="button" 
                            @click="setGlobalExtracom(false)"
                            :class="!formData.is_extracomtable ? 'border-purple-500 bg-purple-950/40 ring-2 ring-purple-500 text-white font-extrabold shadow-md shadow-purple-500/20' : 'border-slate-800 bg-slate-900/60 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                            class="p-3 sm:p-3.5 rounded-2xl border transition-all flex items-center justify-between text-xs cursor-pointer">
                        <div class="flex items-center space-x-2.5">
                            <span class="text-lg">⚙️</span>
                            <div class="text-left">
                                <div class="text-xs font-bold text-white">Aset Tetap Reguler</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Nilai wajar perolehan satuan &gt; Rp 300.000 (Kapitalisasi)</div>
                            </div>
                        </div>
                        <span x-show="!formData.is_extracomtable" class="text-purple-400 font-bold text-xs flex items-center gap-1">
                            <span>✓</span><span>Terpilih</span>
                        </span>
                    </button>

                    {{-- 2. Ekstrakomtabel --}}
                    <button type="button" 
                            @click="setGlobalExtracom(true)"
                            :class="formData.is_extracomtable ? 'border-amber-500 bg-amber-950/40 ring-2 ring-amber-500 text-white font-extrabold shadow-md shadow-amber-500/20' : 'border-slate-800 bg-slate-900/60 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                            class="p-3 sm:p-3.5 rounded-2xl border transition-all flex items-center justify-between text-xs cursor-pointer">
                        <div class="flex items-center space-x-2.5">
                            <span class="text-lg">📦</span>
                            <div class="text-left">
                                <div class="text-xs font-bold text-white">Ekstrakomtabel (Extracom)</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Nilai wajar satuan ≤ Rp 300.000 (Non-Kendaraan)</div>
                            </div>
                        </div>
                        <span x-show="formData.is_extracomtable" class="text-amber-400 font-bold text-xs flex items-center gap-1">
                            <span>✓</span><span>Terpilih</span>
                        </span>
                    </button>
                </div>
            </div>
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

        {{-- Tingkat 4: Nama Barang Manual --}}
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center gap-2">
                <span class="w-5 h-5 rounded-full bg-slate-700 text-slate-300 border border-slate-600 text-[10px] font-black flex items-center justify-center">4</span>
                Nama Lengkap Barang Hibah <span class="text-rose-400">*</span>
            </label>
            <input type="text" x-model="formData.nama_barang" required
                placeholder="Nama lengkap barang aset hibah..."
                class="w-full bg-slate-950/90 border border-amber-500/30 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-amber-100 font-bold focus:outline-none">
        </div>

        {{-- Live Preview Card --}}
        <template x-if="formData.jenis_aset_kode">
            <div class="p-3.5 rounded-2xl bg-amber-950/40 border border-amber-500/30 flex items-center justify-between gap-3 shadow-inner">
                <div class="flex items-center gap-3 min-w-0">
                    <span class="w-8 h-8 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm shrink-0">🎁</span>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Klasifikasi Terpilih</p>
                        <p class="text-xs font-bold text-white truncate" x-text="(selectedSubSub?.kode || formData.jenis_aset_kode) + ' — ' + (formData.nama_barang || formData.jenis_aset_nama)"></p>
                        <p class="text-[10px] text-slate-400 font-mono" x-text="'KIB: ' + kibLabel + ' • Kategori: ' + (formData.is_extracomtable ? 'Ekstrakomtabel (≤ Rp 300.000)' : 'Aset Reguler (Kapitalisasi)')"></p>
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

    {{-- ─── RINCIAN VOLUME & NILAI TAKSIRAN ASET HIBAH (BAGIAN PALING BAWAH) ── --}}
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/30 space-y-5 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-amber-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📝 Rincian Spesifik &amp; Nilai Taksiran Aset Hibah</span>
                <span class="text-rose-400">*</span>
            </span>
            <template x-if="selectedSubSub">
                <span class="text-[11px] font-bold text-amber-400 bg-amber-500/10 px-2.5 py-0.5 rounded-lg border border-amber-500/30 font-mono" x-text="selectedSubSub.kode + ' • ' + selectedSubSub.nama"></span>
            </template>
        </div>

        {{-- Rekapitulasi Terpadu: Total Volume & Nilai Taksiran Aset --}}
        <div class="p-5 sm:p-6 rounded-2xl bg-slate-900/90 border border-amber-500/30 space-y-4 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-100 uppercase tracking-wide">
                        Rekapitulasi Volume &amp; Nilai Taksiran Aset Hibah
                    </span>
                    <span class="text-[10px] text-amber-400 font-bold font-mono px-2.5 py-0.5 rounded-lg bg-amber-950/80 border border-amber-500/30 inline-flex items-center gap-1" x-show="isMultiItemActive">
                        <span>🔒</span> Akumulasi Rincian
                    </span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-mono font-extrabold text-amber-300"
                          x-text="'Total: Rp ' + formatRupiah(formData.total_realisasi)"></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- 1. Total Volume / Kuantitas Aset --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-200 flex items-center justify-between">
                        <span>Total Volume / Kuantitas Aset Hibah <span class="text-rose-400">*</span></span>
                        <span class="text-[11px] font-mono text-amber-400 font-extrabold" x-show="formData.jumlah_volume" x-text="formData.jumlah_volume + ' ' + (formData.satuan || 'Unit')"></span>
                    </label>

                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-2">
                            <input type="number" x-model.number="formData.jumlah_volume" :readonly="isMultiItemActive" required min="1"
                                :class="isMultiItemActive ? 'bg-slate-950/70 border-slate-800 text-amber-300 cursor-not-allowed' : 'bg-slate-950 border-slate-700 text-white'"
                                placeholder="1"
                                class="w-full border focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs focus:outline-none font-bold font-mono">
                        </div>
                        <div>
                            <input type="text" x-model="formData.satuan" required placeholder="Unit"
                                :readonly="isMultiItemActive"
                                :class="isMultiItemActive ? 'bg-slate-950/70 border-slate-800 text-slate-300 cursor-not-allowed' : 'bg-slate-950 border-slate-700 text-white'"
                                class="w-full border focus:border-amber-400 rounded-xl px-3 py-2.5 text-xs text-center font-bold focus:outline-none">
                        </div>
                    </div>

                    <p class="text-[11px] text-slate-400 pt-0.5 leading-relaxed">
                        <span x-show="isTanah">🌾 Volume diakumulasi otomatis dari jumlah <strong>Bidang Tanah (KIB A)</strong> di atas.</span>
                        <span x-show="isMultiItemActive && !isTanah">💡 Total volume dihitung otomatis dari akumulasi seluruh item rincian fisik di atas.</span>
                        <span x-show="!isMultiItemActive">Jumlah total unit / kuantitas fisik aset hibah yang diserahterimakan.</span>
                    </p>
                </div>

                {{-- 2. Total Taksiran Nilai Wajar Perolehan Hibah (Rp) --}}
                <div class="space-y-2">
                    <label class="text-xs font-bold text-slate-200 flex items-center justify-between">
                        <span>Total Nilai Taksiran Wajar Perolehan Hibah (Rp) <span class="text-rose-400">*</span></span>
                        <span class="text-[11px] font-mono text-emerald-400 font-extrabold" x-show="isMultiItemActive && formData.total_realisasi > 0">🔒 Akumulasi Otomatis</span>
                    </label>

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

                    <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-slate-400 pt-0.5">
                        <p>
                            <span x-show="isMultiItemActive">💡 Total nilai hibah akumulasi dari rincian fisik barang.</span>
                            <span x-show="!isMultiItemActive">Taksiran nilai wajar aset hibah perolehan sesuai BAST / appraisal.</span>
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

</div>

