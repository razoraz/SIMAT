<!-- ========================================================================= -->
<!-- LANGKAH 2: KLASIFIKASI KODE 108 & SPESIFIKASI FISIK ASET (PELIMPAHAN BMD) -->
<!-- ========================================================================= -->
<div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-400/10 text-indigo-300 border border-indigo-400/20 text-xs font-bold mb-2">
            <span>🔍 LANGKAH 2 DARI 3: KLASIFIKASI 108 &amp; SPESIFIKASI FISIK ASET</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-indigo-400/10 text-indigo-400 text-sm">📊</span>
            <span>Langkah 2: Klasifikasi 108 &amp; Spesifikasi Fisik Aset</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Pilih klasifikasi kode barang Permendagri No. 108/2016 (Akun 1.3 Aset Tetap Milik Daerah), formulir spesifikasi teknis barang sesuai KIB, penempatan ruangan di RSUD, serta rincian nilai perolehan BMD dari SKPD asal.
        </p>
    </div>

    <!-- Quick Action / Shortcut 5 Kategori Objek KIB Permendagri 108 -->
    <div class="p-5 rounded-3xl bg-slate-900/80 border border-indigo-500/30 backdrop-blur-md shadow-xl space-y-4 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-indigo-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Header Card: Info Objek KIB Aktif -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-indigo-400/20 to-indigo-600/10 text-indigo-400 flex items-center justify-center text-lg font-bold shrink-0 border border-indigo-500/30 shadow-inner">
                    ⚡
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xs font-extrabold text-white tracking-wide">Pilih Kategori Objek KIB (Akun 1.3 Aset Tetap)</h3>
                        <span class="px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 text-[10px] font-bold border border-indigo-500/20">Permendagri 108</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-indigo-300 text-[10px] font-bold border border-slate-700 font-mono" x-text="activeKibCode"></span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Kategori Aktif: <span class="font-bold text-indigo-300" x-text="kibLabel"></span> &mdash; Klik salah satu kartu objek KIB di bawah:
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                <span x-show="hasSelectedKib" class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/30 inline-flex items-center gap-1.5 shadow-sm">
                    ✓ Kategori: <span class="font-mono" x-text="kibLabel"></span>
                </span>
            </div>
        </div>

        <!-- Buttons Grid: 5 Objek KIB Aset Tetap Permendagri 108 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            
            <!-- KIB A: Tanah -->
            <button type="button" @click="selectKibCategory('tanah')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                :class="isTanah 
                    ? 'bg-emerald-500/20 border-emerald-400 text-white shadow-xl shadow-emerald-500/20 ring-1 ring-emerald-400' 
                    : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-emerald-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-2 shrink-0">
                    <span class="text-xl">🌾</span>
                    <span class="font-mono text-[10px] font-black text-emerald-400 group-hover:text-emerald-300">1.3.1</span>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="block text-xs font-bold text-slate-200 group-hover:text-white truncate">KIB A (Tanah)</span>
                    <span class="block text-[10px] text-slate-400 line-clamp-2 mt-0.5 leading-snug">Tanah perkantoran, pelayanan medis, sarana kesehatan</span>
                </div>
                <div class="kib-footer pt-2 border-t border-slate-800/80 flex items-center justify-between shrink-0">
                    <span class="text-[9px] font-mono text-slate-500">KIB A</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold"
                        :class="isTanah ? 'bg-emerald-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-emerald-400 group-hover:text-slate-950'">
                        <span x-show="isTanah">✓</span>
                        <span x-show="!isTanah">&rarr;</span>
                    </div>
                </div>
            </button>

            <!-- KIB B: Peralatan & Mesin -->
            <button type="button" @click="selectKibCategory('mesin')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                :class="isMesin 
                    ? 'bg-indigo-500/20 border-indigo-400 text-white shadow-xl shadow-indigo-500/20 ring-1 ring-indigo-400' 
                    : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-indigo-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-2 shrink-0">
                    <span class="text-xl">⚙️</span>
                    <span class="font-mono text-[10px] font-black text-indigo-400 group-hover:text-indigo-300">1.3.2</span>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="block text-xs font-bold text-slate-200 group-hover:text-white truncate">KIB B (Peralatan/Mesin)</span>
                    <span class="block text-[10px] text-slate-400 line-clamp-2 mt-0.5 leading-snug">Alat kesehatan, kendaraan, mesin, komputer, mebeler</span>
                </div>
                <div class="kib-footer pt-2 border-t border-slate-800/80 flex items-center justify-between shrink-0">
                    <span class="text-[9px] font-mono text-slate-500">KIB B</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold"
                        :class="isMesin ? 'bg-indigo-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-indigo-400 group-hover:text-slate-950'">
                        <span x-show="isMesin">✓</span>
                        <span x-show="!isMesin">&rarr;</span>
                    </div>
                </div>
            </button>

            <!-- KIB C: Gedung & Bangunan -->
            <button type="button" @click="selectKibCategory('gedung')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                :class="isGedung 
                    ? 'bg-blue-500/20 border-blue-400 text-white shadow-xl shadow-blue-500/20 ring-1 ring-blue-400' 
                    : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-blue-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-2 shrink-0">
                    <span class="text-xl">🏢</span>
                    <span class="font-mono text-[10px] font-black text-blue-400 group-hover:text-blue-300">1.3.3</span>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="block text-xs font-bold text-slate-200 group-hover:text-white truncate">KIB C (Gedung/Bangunan)</span>
                    <span class="block text-[10px] text-slate-400 line-clamp-2 mt-0.5 leading-snug">Gedung rawat inap, paviliun, gudang, laboratorium</span>
                </div>
                <div class="kib-footer pt-2 border-t border-slate-800/80 flex items-center justify-between shrink-0">
                    <span class="text-[9px] font-mono text-slate-500">KIB C</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold"
                        :class="isGedung ? 'bg-blue-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-blue-400 group-hover:text-slate-950'">
                        <span x-show="isGedung">✓</span>
                        <span x-show="!isGedung">&rarr;</span>
                    </div>
                </div>
            </button>

            <!-- KIB D: Jalan, Irigasi & Jaringan -->
            <button type="button" @click="selectKibCategory('jaringan')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                :class="isJaringan 
                    ? 'bg-teal-500/20 border-teal-400 text-white shadow-xl shadow-teal-500/20 ring-1 ring-teal-400' 
                    : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-teal-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-2 shrink-0">
                    <span class="text-xl">🛣️</span>
                    <span class="font-mono text-[10px] font-black text-teal-400 group-hover:text-teal-300">1.3.4</span>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="block text-xs font-bold text-slate-200 group-hover:text-white truncate">KIB D (Jalan/Jaringan)</span>
                    <span class="block text-[10px] text-slate-400 line-clamp-2 mt-0.5 leading-snug">Jalan lingkungan, instalasi pemipaan, kabel listrik, IT</span>
                </div>
                <div class="kib-footer pt-2 border-t border-slate-800/80 flex items-center justify-between shrink-0">
                    <span class="text-[9px] font-mono text-slate-500">KIB D</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold"
                        :class="isJaringan ? 'bg-teal-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-teal-400 group-hover:text-slate-950'">
                        <span x-show="isJaringan">✓</span>
                        <span x-show="!isJaringan">&rarr;</span>
                    </div>
                </div>
            </button>

            <!-- KIB E: Aset Tetap Lainnya -->
            <button type="button" @click="selectKibCategory('lainnya')"
                class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative kib-card-last-span"
                :class="isLainnya 
                    ? 'bg-fuchsia-500/20 border-fuchsia-400 text-white shadow-xl shadow-fuchsia-500/20 ring-1 ring-fuchsia-400' 
                    : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-fuchsia-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                <div class="flex items-center justify-between gap-2 shrink-0">
                    <span class="text-xl">📦</span>
                    <span class="font-mono text-[10px] font-black text-fuchsia-400 group-hover:text-fuchsia-300">1.3.5</span>
                </div>
                <div class="min-w-0 flex-1">
                    <span class="block text-xs font-bold text-slate-200 group-hover:text-white truncate">KIB E (Aset Lainnya)</span>
                    <span class="block text-[10px] text-slate-400 line-clamp-2 mt-0.5 leading-snug">Buku medis perpustakaan, seni, hewan/tanaman, software</span>
                </div>
                <div class="kib-footer pt-2 border-t border-slate-800/80 flex items-center justify-between shrink-0">
                    <span class="text-[9px] font-mono text-slate-500">KIB E</span>
                    <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] font-bold"
                        :class="isLainnya ? 'bg-fuchsia-400 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-400 group-hover:bg-fuchsia-400 group-hover:text-slate-950'">
                        <span x-show="isLainnya">✓</span>
                        <span x-show="!isLainnya">&rarr;</span>
                    </div>
                </div>
            </button>

        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- LEMBAR SPESIFIKASI FISIK DINAMIS (KIB A / B / C / D / E)                   -->
    <!-- ========================================================================= -->
    <!-- Sheet KIB A: Tanah -->
    @include('pages.mutasi_eksternal.form_partials.step2_sheets.sheet_tanah')

    <!-- Sheet KIB B: Peralatan & Mesin / Alkes Medis -->
    @include('pages.mutasi_eksternal.form_partials.step2_sheets.sheet_mesin')

    <!-- Sheet KIB C: Gedung & Bangunan -->
    @include('pages.mutasi_eksternal.form_partials.step2_sheets.sheet_gedung')

    <!-- Sheet KIB D: Jalan, Irigasi & Jaringan -->
    @include('pages.mutasi_eksternal.form_partials.step2_sheets.sheet_jaringan')

    <!-- Sheet KIB E: Aset Tetap Lainnya -->
    @include('pages.mutasi_eksternal.form_partials.step2_sheets.sheet_lainnya')

    <!-- ========================================================================= -->
    <!-- ========================================================================= -->
    <!-- AKUMULASI TOTAL VOLUME & NILAI PEROLEHAN BMD                              -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-indigo-500/30 space-y-5 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-indigo-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-sm border border-indigo-500/30">🔒</span>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Akumulasi Total Volume &amp; Nilai Perolehan BMD
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Terkunci otomatis dari hasil perhitungan seluruh rincian unit barang pada lembar KIB di atas.</p>
                </div>
            </div>
            <template x-if="selected108Item">
                <span class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-lg border border-emerald-500/30 font-mono hidden sm:inline-block"
                    x-text="selected108Item.kode + ' • ' + selected108Item.nama"></span>
            </template>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- 1. Total Volume / Kuantitas Aset -->
            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2.5 shadow-md flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                            <span>Total Volume / Kuantitas <span class="text-rose-400">*</span></span>
                            <span class="text-[9px] font-bold text-indigo-400 bg-indigo-500/15 px-2 py-0.5 rounded border border-indigo-500/30">
                                🔒 Terkunci
                            </span>
                        </label>
                        <span class="text-xs font-mono text-indigo-300 font-extrabold" x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></span>
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div class="col-span-2 relative">
                            <input type="number" x-model.number="formData.jumlah_volume" readonly required min="1"
                                class="w-full bg-slate-950/80 border border-slate-800 text-indigo-300 rounded-xl px-4 py-2.5 text-xs font-bold font-mono cursor-not-allowed select-none focus:outline-none shadow-inner">
                        </div>
                        <div>
                            <input type="text" x-model="formData.satuan" readonly required placeholder="Unit"
                                class="w-full bg-slate-950/80 border border-slate-800 text-slate-300 rounded-xl px-3 py-2.5 text-xs text-center font-bold cursor-not-allowed select-none focus:outline-none shadow-inner">
                        </div>
                    </div>
                </div>

                <p class="text-[10.5px] text-slate-400 pt-1 leading-relaxed">
                    💡 Total volume dihitung otomatis dari akumulasi kuantitas rincian barang pada lembar KIB di atas.
                </p>
            </div>

            <!-- 2. Total Nilai Perolehan BMD dari SKPD Pengirim (Rp) -->
            <div class="p-5 rounded-2xl bg-slate-900/90 border border-indigo-500/30 space-y-2.5 shadow-md flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                            <span>Total Nilai Perolehan (Rp) <span class="text-rose-400">*</span></span>
                            <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/15 px-2 py-0.5 rounded border border-emerald-500/30">
                                🔒 Terkunci
                            </span>
                        </label>
                        <span class="text-xs font-mono text-emerald-400 font-extrabold" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></span>
                    </div>

                    <div class="flex items-center rounded-xl border border-slate-800 bg-slate-950/80 overflow-hidden cursor-not-allowed shadow-inner">
                        <span class="px-3.5 py-2.5 bg-slate-900 border-r border-slate-800 text-slate-400 text-xs font-bold font-mono select-none flex items-center justify-center">
                            Rp
                        </span>
                        <input type="text"
                            :value="formData.total_realisasi ? Number(formData.total_realisasi).toLocaleString('id-ID') : '0'"
                            readonly
                            placeholder="0"
                            class="w-full bg-transparent px-3.5 py-2.5 text-xs font-bold font-mono text-emerald-400 cursor-not-allowed select-none focus:outline-none">
                    </div>
                </div>

                <div class="text-[10.5px] text-slate-400 pt-1 leading-relaxed">
                    💡 Akumulasi otomatis dari nilai total seluruh rincian barang pada lembar KIB di atas.
                </div>
            </div>

        </div>
    </div>

    <!-- BANNER PERINGATAN EXTRACOM (> RP 300.000) -->
    <div x-show="hasExtracomViolation" x-cloak
         class="mt-4 p-4 sm:p-5 rounded-3xl bg-rose-950/40 border border-rose-500/50 text-rose-300 shadow-xl shadow-rose-950/40 flex items-start space-x-3.5 transition-all">
        <div class="w-10 h-10 rounded-2xl bg-rose-500/20 border border-rose-500/30 flex items-center justify-center text-xl shrink-0 text-rose-400 shadow-lg shadow-rose-500/20">
            ⚠️
        </div>
        <div class="space-y-2 flex-1 min-w-0">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <h4 class="font-extrabold text-rose-100 text-xs sm:text-sm tracking-wide">
                    Peringatan: Nilai Satuan Ekstrakomtabel (Extracom) Melebihi Rp 300.000!
                </h4>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/20 text-rose-300 border border-rose-500/40">
                    Tidak Dapat Lanjut ke Tahap 3
                </span>
            </div>
            <p class="text-xs text-rose-200/90 leading-relaxed">
                Terdapat rincian barang yang Anda beri status <span class="px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold border border-amber-500/30">Ekstrakomtabel (≤ 300rb)</span> namun diisi dengan nilai satuan per unit lebih dari <strong>Rp 300.000</strong>. Anda harus menyesuaikannya sebelum dapat melanjutkan ke Tahap 3:
            </p>
            <div class="p-3 rounded-2xl bg-slate-950/80 border border-rose-500/20 text-[11.5px] text-slate-300 space-y-1.5 shadow-inner">
                <div class="flex items-start gap-2">
                    <span class="text-emerald-400 font-bold">1.</span>
                    <span>Jika nilai perolehan memang <strong>&gt; Rp 300.000</strong>: Klik tombol status akuntansi <strong>⚙️ Aset Tetap Reguler (Intrakomptabel)</strong> pada kartu rincian barang di atas.</span>
                </div>
                <div class="flex items-start gap-2">
                    <span class="text-amber-400 font-bold">2.</span>
                    <span>Jika barang benar-benar <strong>Ekstrakomtabel</strong>: Perbaiki nilai satuan agar <strong>maksimal Rp 300.000</strong>.</span>
                </div>
            </div>
        </div>
    </div>

</div>
