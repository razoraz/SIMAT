{{-- =========================================================================
     LANGKAH 2: KLASIFIKASI KODE BARANG 108 & RINCIAN SPESIFIKASI BARANG (HIBAH)
     Design: Dark Luxury Amber — Identik dengan Kemitraan Step 2
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
            Pilih klasifikasi kode barang Permendagri No. 108/2016 yang sesuai dengan aset hibah masuk, rincian teknis fisik, serta taksiran nilai wajar perolehan aset.
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

    {{-- ========================================================================= --}}
    {{-- LEMBAR SPESIFIKASI FISIK DINAMIS (KIB A / B / C / D / E)                   --}}
    {{-- Menggantikan form 4 tingkat belanja modal dengan layout Kemitraan         --}}
    {{-- ========================================================================= --}}
    <!-- Sheet KIB A: Tanah -->
    @include('pages.hibah.form_partials.step3_sheets.sheet_tanah')

    <!-- Sheet KIB B: Peralatan & Mesin -->
    @include('pages.hibah.form_partials.step3_sheets.sheet_mesin')

    <!-- Sheet KIB C: Gedung & Bangunan -->
    @include('pages.hibah.form_partials.step3_sheets.sheet_gedung')

    <!-- Sheet KIB D: Jalan, Irigasi & Jaringan -->
    @include('pages.hibah.form_partials.step3_sheets.sheet_jaringan')

    <!-- Sheet KIB E: Aset Tetap Lainnya -->
    @include('pages.hibah.form_partials.step3_sheets.sheet_lainnya')

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
                                @keydown="if(!isMultiItemActive && ['-', '+', 'e', 'E', '.'].includes($event.key)) $event.preventDefault();"
                                @input="
                                    if (!isMultiItemActive) {
                                        let raw = String($event.target.value).replace(/\D/g, '');
                                        if (raw.startsWith('0')) raw = raw.replace(/^0+/, '');
                                        formData.jumlah_volume = raw ? parseInt(raw, 10) : '';
                                    }
                                "
                                @blur="
                                    if (!isMultiItemActive && (!formData.jumlah_volume || Number(formData.jumlah_volume) < 1)) {
                                        formData.jumlah_volume = 1;
                                    }
                                "
                                :class="isMultiItemActive ? 'bg-slate-950/70 border-slate-800 text-amber-300 cursor-not-allowed' : ((!formData.jumlah_volume || Number(formData.jumlah_volume) < 1) ? 'bg-slate-950 border-rose-500 ring-1 ring-rose-500 text-rose-300' : 'bg-slate-950 border-slate-700 text-white focus:border-amber-400')"
                                placeholder="1"
                                class="w-full border rounded-xl px-4 py-2.5 text-xs focus:outline-none font-bold font-mono transition-colors">
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

                    {{-- Warning Batas Nilai Akuntansi (Rp 300.000) --}}
                    <template x-if="(isMesin || isAsetLainnya) && !formData.is_extracomtable && formData.total_realisasi > 0 && Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)) <= 300000">
                        <div class="p-3 rounded-xl bg-rose-950/40 border border-rose-500/30 text-[10.5px] text-rose-300 space-y-1.5 mt-2">
                            <div class="font-bold flex items-center gap-1.5">
                                <span>⚠️</span>
                                <span>Nilai satuan Aset Reguler wajib &gt; Rp 300.000 (Saat ini: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></span>/unit)</span>
                            </div>
                            <p class="text-slate-400 leading-tight">
                                Berdasarkan aturan akuntansi pemerintah, aset bernilai ≤ Rp 300.000 harus dicatat sebagai <strong>Ekstrakomtabel (Extracom)</strong>.
                            </p>
                            <button type="button" @click="setGlobalExtracom(true)"
                                class="px-3 py-1 rounded-lg bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>📦</span>
                                <span>Beralih ke Ekstrakomtabel Sekarang</span>
                            </button>
                        </div>
                    </template>

                    <template x-if="(isMesin || isAsetLainnya) && formData.is_extracomtable && formData.total_realisasi > 0 && Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)) > 300000">
                        <div class="p-3 rounded-xl bg-rose-950/40 border border-rose-500/30 text-[10.5px] text-rose-300 space-y-1.5 mt-2">
                            <div class="font-bold flex items-center gap-1.5">
                                <span>⚠️</span>
                                <span>Nilai satuan Ekstrakomtabel tidak boleh &gt; Rp 300.000 (Saat ini: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></span>/unit)</span>
                            </div>
                            <button type="button" @click="setGlobalExtracom(false)"
                                class="px-3 py-1 rounded-lg bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold transition-all flex items-center gap-1.5 cursor-pointer">
                                <span>⚙️</span>
                                <span>Beralih ke Aset Tetap Reguler</span>
                            </button>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

</div>
