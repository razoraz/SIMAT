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

    <!-- Quick Action / Shortcut Akun 1.5.2 (Rekomendasi Sub-Sub Rincian Kemitraan) -->
    <div class="p-5 rounded-3xl bg-slate-900/80 border border-cyan-500/30 backdrop-blur-md shadow-xl space-y-4 relative overflow-hidden">
        <!-- Subtle Glow Effect -->
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-cyan-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <!-- Header Card: Info Skema Aktif -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800/80">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-cyan-400/20 to-cyan-600/10 text-cyan-400 flex items-center justify-center text-lg font-bold shrink-0 border border-cyan-500/30 shadow-inner">
                    ⚡
                </div>
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h3 class="text-xs font-extrabold text-white tracking-wide">Pilih Objek Akun 1.5.2 Kemitraan</h3>
                        <span class="px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 text-[10px] font-bold border border-cyan-500/20">Permendagri 108</span>
                        <span class="px-2 py-0.5 rounded-full bg-slate-800 text-cyan-400 text-[10px] font-bold border border-slate-700 font-mono" x-text="activeSkemaKode"></span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Skema Aktif: <span class="font-bold text-cyan-300" x-text="activeSkemaLabel"></span> &mdash; Klik salah satu kartu objek di bawah:
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
                <button type="button" @click="selectSubSubItem(item)"
                    class="group p-3.5 rounded-2xl border text-left transition-all duration-200 cursor-pointer flex flex-col justify-between gap-3 relative"
                    :class="selectedSubSub?.id === item.id 
                        ? 'bg-cyan-500/20 border-cyan-400 text-white shadow-xl shadow-cyan-500/20 ring-2 ring-cyan-400' 
                        : 'bg-slate-950/70 border-slate-800 text-slate-300 hover:border-cyan-500/50 hover:bg-slate-850 hover:-translate-y-0.5'">
                    
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xl" x-text="getSubSubIcon(item.kode)"></span>
                        <span class="font-mono text-[10px] font-black text-cyan-400 group-hover:text-cyan-300" x-text="item.kode"></span>
                    </div>

                    <div class="min-w-0 flex-1">
                        <span class="block text-xs font-bold text-slate-200 group-hover:text-white truncate" x-text="getSubSubShortLabel(item.kode, item.nama)"></span>
                        <span class="block text-[10px] text-slate-400 line-clamp-2 mt-0.5 leading-snug" x-text="item.nama"></span>
                    </div>

                    <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between">
                        <span class="text-[9px] font-mono text-slate-500" x-text="'ID: ' + item.id"></span>
                        <div class="w-6 h-6 rounded-lg flex items-center justify-center text-[10px] transition-colors font-bold"
                            :class="selectedSubSub?.id === item.id ? 'bg-cyan-400 text-slate-950 font-black shadow-md shadow-cyan-400/30' : 'bg-slate-800 text-slate-400 group-hover:bg-cyan-400 group-hover:text-slate-950'">
                            <span x-show="selectedSubSub?.id === item.id">✓</span>
                            <span x-show="selectedSubSub?.id !== item.id">&rarr;</span>
                        </div>
                    </div>
                </button>
            </template>
        </div>
    </div>

    <!-- Rincian Spesifik Barang & Nilai Aset -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-cyan-500/30 space-y-5 shadow-2xl">
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

        <div class="space-y-4">
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
