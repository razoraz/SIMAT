<!-- ========================================================================= -->
<!-- LANGKAH 2: KLASIFIKASI KODE BARANG 108 & NILAI ASET (AKUN 1.5.2)          -->
<!-- ========================================================================= -->
<div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2 border transition-all"
             :class="tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-400/10 text-cyan-300 border-cyan-400/20' : 'bg-emerald-400/10 text-emerald-300 border-emerald-400/20'">
            <span x-show="tipeKemitraan === 'dimanfaatkan'">🏛️ LANGKAH 2 DARI 3: KLASIFIKASI 108 PEMANFAATAN BMD</span>
            <span x-show="tipeKemitraan === 'ditambahkan'">📦 LANGKAH 2 DARI 3: KLASIFIKASI 108 &amp; SPESIFIKASI ALAT BARU MITRA</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl text-sm"
                  :class="tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-400/10 text-cyan-400' : 'bg-emerald-400/10 text-emerald-400'"
                  x-text="tipeKemitraan === 'dimanfaatkan' ? '🏛️' : '📦'"></span>
            <span x-show="tipeKemitraan === 'dimanfaatkan'">Langkah 2: Klasifikasi 108 Pemanfaatan BMD &amp; Nilai Sewa</span>
            <span x-show="tipeKemitraan === 'ditambahkan'">Langkah 2: Spesifikasi Teknis &amp; Taksiran Nilai Aset Baru Mitra</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            <span x-show="tipeKemitraan === 'dimanfaatkan'">Pilih sub-akun 1.5.2 Pemanfaatan BMD RSUD (Sewa Tanah/Gedung/Peralatan atau KSP) serta total tarif/nilai kontrak pemanfaatan.</span>
            <span x-show="tipeKemitraan === 'ditambahkan'">Pilih klasifikasi akun 1.5.2 kemitraan, formulir rincian spesifikasi barang baru rekanan (alat medis, laboratorium, fasilitas KSO), serta taksiran nilai wajar perolehan aset.</span>
        </p>
    </div>

    <!-- ===== BANNER ERROR INLINE LANGKAH 2 ===== -->
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

    <!-- Quick Action / Shortcut Akun 1.5.2 (Rekomendasi Sub-Sub Rincian Kemitraan) -->
    <div class="p-5 rounded-3xl bg-slate-900/80 border backdrop-blur-md shadow-xl space-y-4 relative overflow-hidden transition-all"
         :class="tipeKemitraan === 'dimanfaatkan' ? 'border-cyan-500/30' : 'border-emerald-500/30'">
        <!-- Subtle Glow Effect -->
        <div class="absolute -right-10 -bottom-10 w-44 h-44 rounded-full blur-2xl pointer-events-none"
             :class="tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500/5' : 'bg-emerald-500/5'"></div>

        <!-- Header Card: Info Skema Aktif -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br flex items-center justify-center text-lg font-bold shrink-0 border shadow-inner"
                     :class="tipeKemitraan === 'dimanfaatkan' ? 'from-cyan-400/20 to-cyan-600/10 text-cyan-400 border-cyan-500/30' : 'from-emerald-400/20 to-emerald-600/10 text-emerald-400 border-emerald-500/30'">
                    ⚡
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xs font-extrabold text-white tracking-wide">
                            <span x-show="tipeKemitraan === 'dimanfaatkan'">Pilih Objek Akun 1.5.2 Pemanfaatan BMD</span>
                            <span x-show="tipeKemitraan === 'ditambahkan'">Pilih Objek Akun 1.5.2 Aset Baru Mitra</span>
                        </h3>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border font-mono"
                              :class="tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500/10 text-cyan-300 border-cyan-500/20' : 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20'">Permendagri 108</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-[10px] font-bold border border-slate-700 font-mono"
                              :class="tipeKemitraan === 'dimanfaatkan' ? 'text-cyan-400' : 'text-emerald-400'"
                              x-text="activeSkemaKode"></span>
                        <span x-show="tipeKemitraan === 'dimanfaatkan' && lockedKibFromData" class="px-2.5 py-0.5 rounded-full bg-cyan-950/60 text-cyan-300 border border-cyan-500/40 text-[10px] font-bold font-mono inline-flex items-center gap-1 shadow-sm">
                            <span>🔒</span> Objek Terkunci Sesuai Data Aset BMD (KIB <span x-text="lockedKibFromData"></span>)
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        <span x-show="tipeKemitraan === 'dimanfaatkan'">
                            <span x-show="lockedKibFromData">Kategori objek terkunci otomatis sesuai data aset BMD yang dimanfaatkan (KIB <span class="font-bold text-cyan-300" x-text="lockedKibFromData"></span>) &mdash; kartu lain dinonaktifkan.</span>
                            <span x-show="!lockedKibFromData">Pemanfaatan BMD Akun 1.5.2 &mdash; Silakan pilih 1 objek aset yang disewakan / dimanfaatkan di bawah:</span>
                        </span>
                        <span x-show="tipeKemitraan === 'ditambahkan'">Skema Kemitraan: <span class="font-bold text-emerald-300" x-text="activeSkemaLabel"></span> &mdash; Klik salah satu kartu objek aset baru di bawah:</span>
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
                <span x-show="selectedSubSub" class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/30 inline-flex items-center gap-1.5 shadow-sm">
                    ✓ Terpilih: <span class="font-mono" x-text="selectedSubSub?.kode"></span>
                </span>
                <span x-show="!selectedSubSub" class="text-[11px] font-semibold text-amber-400 bg-amber-500/10 px-3 py-1.5 rounded-xl border border-amber-500/20 inline-flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-ping"></span>
                    Pilih salah satu objek di bawah
                </span>
            </div>
        </div>

        <!-- Buttons Grid: 5 Sub-Sub Rincian Objek Permendagri 108 -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <template x-for="item in currentSubSubRecommendations" :key="item.id">
                <button type="button" 
                    @click="selectSubSubItem(item)"
                    :disabled="isSubSubCardLocked(item)"
                    class="group p-3.5 rounded-2xl border text-left transition-all duration-200 flex flex-col justify-between gap-3 relative"
                    :class="[
                        isSubSubCardLocked(item) 
                            ? 'opacity-25 cursor-not-allowed pointer-events-none border-dashed border-slate-800/80 bg-slate-950/30 select-none grayscale'
                            : (selectedSubSub?.id === item.id 
                                ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-500/20 border-cyan-400 text-white shadow-xl shadow-cyan-500/20 ring-2 ring-cyan-400 cursor-default' : 'bg-emerald-500/20 border-emerald-400 text-white shadow-xl shadow-emerald-500/20 ring-2 ring-emerald-400 cursor-default') 
                                : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-cyan-500/50 hover:bg-slate-850 hover:-translate-y-0.5 cursor-pointer')
                    ]">
                    
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xl" x-text="getSubSubIcon(item.kode)"></span>
                        <div class="flex items-center gap-1">
                            <span x-show="isSubSubCardLocked(item)" class="text-[9px] px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-500 font-mono">🔒 Non-KIB</span>
                            <span x-show="selectedSubSub?.id === item.id && lockedKibFromData" class="text-[9px] px-1.5 py-0.5 rounded bg-cyan-950 border border-cyan-500/40 text-cyan-300 font-mono font-bold">🔒 Terkunci</span>
                            <span class="font-mono text-[10px] font-black"
                                  :class="isSubSubCardLocked(item) ? 'text-slate-600' : (tipeKemitraan === 'dimanfaatkan' ? 'text-cyan-400 group-hover:text-cyan-300' : 'text-emerald-400 group-hover:text-emerald-300')"
                                  x-text="item.kode"></span>
                        </div>
                    </div>

                    <div class="min-w-0 flex-1">
                        <span class="block text-xs font-bold truncate"
                              :class="isSubSubCardLocked(item) ? 'text-slate-500' : 'text-slate-200 group-hover:text-white'"
                              x-text="getSubSubShortLabel(item.kode, item.nama)"></span>
                        <span class="block text-[10px] line-clamp-2 mt-0.5 leading-snug"
                              :class="isSubSubCardLocked(item) ? 'text-slate-600 italic' : 'text-slate-400'"
                              x-text="isSubSubCardLocked(item) ? 'Terkunci (Aset BMD bukan kategori ini)' : item.nama"></span>
                    </div>

                    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-[9px] font-mono text-slate-500" x-text="'ID: ' + item.id"></span>
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] transition-colors font-bold"
                            :class="isSubSubCardLocked(item) ? 'bg-slate-900 text-slate-600' : (selectedSubSub?.id === item.id ? (tipeKemitraan === 'dimanfaatkan' ? 'bg-cyan-400 text-slate-950 font-black shadow-md shadow-cyan-400/30' : 'bg-emerald-400 text-slate-950 font-black shadow-md shadow-emerald-400/30') : 'bg-slate-800 text-slate-400 group-hover:bg-cyan-400 group-hover:text-slate-950')">
                            <span x-show="selectedSubSub?.id === item.id">✓</span>
                            <span x-show="selectedSubSub?.id !== item.id && !isSubSubCardLocked(item)">&rarr;</span>
                            <span x-show="isSubSubCardLocked(item)">✕</span>
                        </div>
                    </div>
                </button>
            </template>
        </div>

        <!-- Pilihan Status Akuntansi: Aset Tetap Reguler vs Ekstrakomtabel (Extracom) -->
        <!-- Khusus untuk Peralatan & Mesin (KIB B) dan Aset Tetap Lainnya (KIB E) -->
        <div x-show="isMesin || isLainnya" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="pt-3 border-t border-slate-800/80">
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-3 shadow-inner">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold uppercase tracking-wider flex items-center space-x-2"
                           :class="formData.is_extracomtable ? 'text-cyan-300' : 'text-purple-300'">
                        <span x-text="formData.is_extracomtable ? '📦 STATUS AKUNTANSI: EKSTRAKOMTABEL (EXTRACOM)' : '⚙️ STATUS AKUNTANSI: ASET TETAP REGULER (INTRAKOMPTABEL)'"></span>
                    </label>
                    <span class="text-[9.5px] px-2.5 py-0.5 rounded-lg font-bold font-mono border"
                          :class="formData.is_extracomtable ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                          x-text="formData.is_extracomtable ? '≤ Rp 300.000' : '> Rp 300.000'">
                    </span>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <!-- 1. Aset Tetap Reguler -->
                    <button type="button" 
                            @click="setGlobalExtracom(false)"
                            :class="!formData.is_extracomtable ? 'border-purple-500 bg-purple-950/40 ring-2 ring-purple-500 text-white font-extrabold shadow-md shadow-purple-500/20' : 'border-slate-800 bg-slate-900/60 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                            class="p-3 sm:p-3.5 rounded-2xl border transition-all flex items-center justify-between text-xs cursor-pointer">
                        <div class="flex items-center space-x-2.5">
                            <span class="text-lg">⚙️</span>
                            <div class="text-left">
                                <div class="text-xs font-bold text-white">Aset Tetap Reguler</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Nilai wajar satuan &gt; Rp 300.000</div>
                            </div>
                        </div>
                        <span x-show="!formData.is_extracomtable" class="text-purple-400 font-bold text-xs flex items-center gap-1">
                            <span>✓</span><span>Terpilih</span>
                        </span>
                    </button>

                    <!-- 2. Ekstrakomtabel -->
                    <button type="button" 
                            @click="setGlobalExtracom(true)"
                            :class="formData.is_extracomtable ? 'border-cyan-500 bg-cyan-950/40 ring-2 ring-cyan-500 text-white font-extrabold shadow-md shadow-cyan-500/20' : 'border-slate-800 bg-slate-900/60 text-slate-400 hover:text-slate-200 hover:border-slate-700'"
                            class="p-3 sm:p-3.5 rounded-2xl border transition-all flex items-center justify-between text-xs cursor-pointer">
                        <div class="flex items-center space-x-2.5">
                            <span class="text-lg">📦</span>
                            <div class="text-left">
                                <div class="text-xs font-bold text-white">Ekstrakomtabel (Extracom)</div>
                                <div class="text-[10px] text-slate-400 mt-0.5">Nilai wajar satuan ≤ Rp 300.000 (Non-Kendaraan)</div>
                            </div>
                        </div>
                        <span x-show="formData.is_extracomtable" class="text-cyan-400 font-bold text-xs flex items-center gap-1">
                            <span>✓</span><span>Terpilih</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- LEMBAR SPESIFIKASI FISIK DINAMIS (KIB A / B / C / D / E)                   -->
    <!-- ========================================================================= -->
    <!-- Sheet KIB A: Tanah -->
    @include('pages.kemitraan.form_partials.step3_sheets.sheet_tanah')

    <!-- Sheet KIB B: Peralatan & Mesin / Alkes Medis -->
    @include('pages.kemitraan.form_partials.step3_sheets.sheet_mesin')

    <!-- Sheet KIB C: Gedung & Bangunan -->
    @include('pages.kemitraan.form_partials.step3_sheets.sheet_gedung')

    <!-- Sheet KIB D: Jalan, Irigasi & Jaringan -->
    @include('pages.kemitraan.form_partials.step3_sheets.sheet_jaringan')

    <!-- Sheet KIB E: Aset Tetap Lainnya -->
    @include('pages.kemitraan.form_partials.step3_sheets.sheet_lainnya')



    <!-- ========================================================================= -->
    <!-- RINCIAN SPESIFIK BARANG & NILAI TAKSIRAN ASET (BAGIAN PALING BAWAH)       -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-cyan-500/30 space-y-5 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-cyan-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📝 Rincian Spesifik &amp; Nilai Taksiran Aset</span>
                <span class="text-rose-400">*</span>
            </span>
            <template x-if="selectedSubSub">
                <span class="text-[11px] font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-lg border border-emerald-500/30 font-mono" x-text="selectedSubSub.kode + ' • ' + selectedSubSub.nama">
                </span>
            </template>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- 0. Nama Barang / Aset Fisik Kemitraan (Eksplisit Bukan Jenis Kemitraan) -->
            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2.5 shadow-md md:col-span-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                        <span>Nama Barang / Aset Fisik Kemitraan <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">
                            Wajib Nama Barang Asli (Bukan Jenis Kemitraan)
                        </span>
                    </label>
                    <span class="text-[10px] font-mono text-slate-400" x-show="formData.nama_barang">
                        <strong class="text-cyan-300 font-mono" x-text="(formData.nama_barang || '').length"></strong> karakter
                    </span>
                </div>
                <div class="relative">
                    <input type="text"
                        x-model="formData.nama_barang"
                        @input="syncNameToActiveItem()"
                        required
                        placeholder="Contoh: Lahan Parkir Paviliun / Hematology Analyzer XN-1000 / Gedung Rawat Inap Melati"
                        class="w-full bg-slate-950 border border-slate-700 hover:border-emerald-400 focus:border-emerald-400 rounded-xl px-4 py-2.5 text-xs text-white font-bold focus:outline-none transition-all shadow-inner">
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    💡 <strong>Penting:</strong> Tuliskan nama spesifik fisik barang/lahan yang sebenarnya. Jenis kemitraan (Sewa, KSP, KSO, dll.) sudah otomatis ditentukan oleh kode 108 pada pilihan di atas.
                </p>
            </div>

            <!-- 1. Total Volume / Kuantitas Aset -->
            <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2.5 shadow-md">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                        <span>Total Volume / Kuantitas Aset <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-cyan-400 font-bold font-mono" x-show="isMultiItemActive">🔒 Akumulasi Rincian</span>
                    </label>
                    <span class="text-xs font-mono text-cyan-300 font-extrabold" x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></span>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <div class="col-span-2 relative">
                        <input type="number" x-model.number="formData.jumlah_volume" :readonly="isMultiItemActive" required min="1"
                            :class="isMultiItemActive ? 'bg-slate-950/70 border-slate-800 text-cyan-300 cursor-not-allowed' : 'bg-slate-950 border-slate-700 text-white'"
                            class="w-full border focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs focus:outline-none font-bold font-mono">
                    </div>
                    <div>
                        <input type="text" x-model="formData.satuan" required placeholder="Unit"
                            :readonly="isMultiItemActive"
                            :class="isMultiItemActive ? 'bg-slate-950/70 border-slate-800 text-slate-300 cursor-not-allowed' : 'bg-slate-950 border-slate-700 text-white'"
                            class="w-full border focus:border-cyan-400 rounded-xl px-3 py-2.5 text-xs text-center font-bold focus:outline-none">
                    </div>
                </div>

                <p class="text-[11px] text-slate-400 pt-1 leading-relaxed">
                    <span x-show="isTanah">🌾 Volume dan satuan diisi langsung pada kartu <strong>Bidang Tanah (KIB A)</strong> di atas.</span>
                    <span x-show="isMultiItemActive && !isTanah">💡 Total volume dihitung otomatis dari akumulasi rincian barang/unit di atas.</span>
                    <span x-show="!isMultiItemActive">Kuantitas fisik unit aset yang dikerjasamakan dalam kemitraan.</span>
                </p>
            </div>

            <!-- 2. Total Taksiran Nilai Wajar Aset (Rp) -->
            <div class="p-5 rounded-2xl bg-slate-900/90 border space-y-2.5 shadow-md"
                 :class="tipeKemitraan === 'dimanfaatkan' ? 'border-cyan-500/30' : 'border-emerald-500/30'">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                        <span x-show="tipeKemitraan === 'dimanfaatkan'">Total Nilai Kontrak Sewa / Pemanfaatan (Rp) <span class="text-rose-400">*</span></span>
                        <span x-show="tipeKemitraan === 'ditambahkan'">Total Taksiran Nilai Investasi / Aset Baru Mitra (Rp) <span class="text-rose-400">*</span></span>
                        <span class="text-[10px] text-emerald-400 font-bold font-mono" x-show="isMultiItemActive && formData.total_realisasi > 0">🔒 Akumulasi Otomatis</span>
                    </label>
                    <span class="text-xs font-mono font-extrabold"
                          :class="tipeKemitraan === 'dimanfaatkan' ? 'text-cyan-300' : 'text-emerald-300'"
                          x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></span>
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
                        :class="isMultiItemActive ? 'bg-slate-950/70 border-slate-800 text-emerald-400 cursor-not-allowed' : 'bg-slate-950 border-slate-700 text-cyan-300'"
                        placeholder="0"
                        class="w-full border focus:border-cyan-400 rounded-xl px-4 py-2.5 pl-10 text-xs font-bold font-mono focus:outline-none">
                </div>

                <div class="flex flex-col sm:flex-row sm:items-center justify-between text-[11px] text-slate-400 pt-1">
                    <p>
                        <span x-show="isTanah">🌾 Taksiran nilai wajar tanah diisi langsung pada inputan <strong>Volume &amp; Taksiran Nilai Wajar Tanah</strong> pada kartu bidang tanah di atas.</span>
                        <span x-show="isMultiItemActive && !isTanah && (isGedung || isJaringan)">💡 Total nilai wajar keseluruhan perolehan/appraisal (lump-sum per objek).</span>
                        <span x-show="isMultiItemActive && !isTanah && (isMesin || isLainnya)">💡 Akumulasi otomatis dari taksiran nilai wajar barang di atas.</span>
                        <span x-show="!isMultiItemActive">Sesuai klausul kontrak PKS atau taksiran appraisal.</span>
                    </p>
                    <template x-if="(isMesin || isLainnya) && formData.jumlah_volume > 1 && formData.total_realisasi > 0">
                        <span class="text-cyan-400 font-mono font-semibold">
                            Rata-rata: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / formData.jumlah_volume))"></span>
                        </span>
                    </template>
                </div>
            </div>
        </div>

    </div>

</div>
