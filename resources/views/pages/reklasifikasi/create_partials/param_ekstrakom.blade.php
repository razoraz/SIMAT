<!-- PANEL KHUSUS EKSTRAKOMTABEL & KAPITALISASI INTRAKOMTABEL (BATAS NILAI KAPITALISASI RP 300.000) -->
<div x-show="formData.jenis_reklas === 'EKSTRAKOMPTABEL' || formData.jenis_reklas === 'KAPITALISASI_INTRAKOM'"
     x-transition.duration.300ms
     class="space-y-4">

    <!-- 1. Banner Panduan & Edukasi Batas Nilai Kapitalisasi -->
    <template x-if="formData.jenis_reklas === 'EKSTRAKOMPTABEL'">
        <div class="p-4 rounded-2xl bg-amber-950/20 border border-amber-500/30 flex items-start gap-3 shadow-sm">
            <span class="text-amber-400 text-lg shrink-0 mt-0.5">💡</span>
            <div class="text-xs text-amber-200/90 leading-relaxed space-y-1">
                <div class="font-bold text-amber-300">
                    Pengalihan Aset ke Kelompok Ekstrakomptabel (Batas Perolehan &le; Rp 300.000)
                </div>
                <div>
                    Aset ini akan dikeluarkan dari Neraca Aset Tetap dan dibukukan ke daftar Ekstrakomptabel karena nilai perolehan satuan barang tidak memenuhi batas minimum kapitalisasi aset daerah (&le; Rp 300.000 per unit).
                </div>
                <div class="text-[11px] text-cyan-300 font-semibold pt-0.5 flex items-center gap-1.5">
                    <span>✨</span>
                    <span>Saran: Jika aset ini ingin dipertahankan di Neraca Aset Tetap (Intrakomptabel), nilai satuan barang wajib di atas <strong>Rp 300.000</strong>.</span>
                </div>
            </div>
        </div>
    </template>

    <template x-if="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM'">
        <div class="p-4 rounded-2xl bg-emerald-950/20 border border-emerald-500/30 flex items-start gap-3 shadow-sm">
            <span class="text-emerald-400 text-lg shrink-0 mt-0.5">💡</span>
            <div class="text-xs text-emerald-200/90 leading-relaxed space-y-1">
                <div class="font-bold text-emerald-300">
                    Kapitalisasi Aset Ekstrakomptabel ke Neraca Aset Tetap (Intrakomptabel)
                </div>
                <div>
                    Barang ekstrakomptabel ini akan <strong>dinaikkan kembali ke Neraca Aset Tetap (Intrakomptabel)</strong>.
                </div>
                <div class="text-[11px] text-emerald-300 font-semibold pt-0.5 flex items-center gap-1.5">
                    <span>✨</span>
                    <span>Syarat Kapitalisasi: Nilai satuan barang wajib <strong>di atas Rp 300.000 (&gt; Rp 300.000)</strong> per unit. Silakan sesuaikan harga satuan langsung di bawah ini.</span>
                </div>
            </div>
        </div>
    </template>

    <!-- 2. Header Section Rincian Barang -->
    <div class="flex items-center justify-between pt-1">
        <div class="flex items-center gap-2">
            <span class="text-xs font-bold uppercase tracking-wider"
                  :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'text-emerald-300' : 'text-amber-300'">
                📦 Rincian Barang &amp; Harga Satuan:
            </span>
            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold border"
                  :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'"
                  x-text="(formData.reklas_items ? formData.reklas_items.length : 0) + ' Item'"></span>
        </div>
        <div class="text-[11px] text-slate-400">
            Standar Batas Kapitalisasi BMD: <strong class="text-white font-mono">Rp 300.000</strong>
        </div>
    </div>

    <!-- 3. Daftar Rincian Barang (Repeater Cards) -->
    <template x-if="!formData.reklas_items || formData.reklas_items.length === 0">
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-dashed border-slate-800 text-center space-y-1.5 shadow-inner">
            <span class="text-amber-400 text-lg block">⚠️</span>
            <p class="text-xs font-bold text-slate-300">Belum ada unit register yang dipilih</p>
            <p class="text-[11px] text-slate-500">Silakan centang minimal 1 unit register (NIBAR) pada <strong class="text-cyan-400">Tahap 01</strong> di atas untuk mengatur rincian nilai barang yang diproses.</p>
        </div>
    </template>

    <div x-show="formData.reklas_items && formData.reklas_items.length > 0"
         class="space-y-3 max-h-[380px] overflow-y-auto custom-scrollbar pr-1">
        <template x-for="(item, idx) in (formData.reklas_items || [])" :key="item.register_id || idx">
            <div class="p-4 rounded-2xl bg-slate-900/90 border transition-all space-y-3"
                 :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM'
                    ? (parseFloat(item.harga_satuan) <= 300000 ? 'border-rose-500/60 bg-rose-950/10 ring-1 ring-rose-500/20' : 'border-slate-800 hover:border-slate-700')
                    : (parseFloat(item.harga_satuan) > 300000 ? 'border-rose-500/60 bg-rose-950/10 ring-1 ring-rose-500/20' : (parseFloat(item.harga_satuan) <= 0 ? 'border-amber-500/40 bg-amber-950/10' : 'border-slate-800 hover:border-slate-700'))">

                <!-- Baris Atas Item: Tag Nomor, Nama Barang & NIBAR -->
                <div class="flex flex-wrap items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-lg text-[10.5px] font-extrabold bg-slate-800 text-slate-300 border border-slate-700"
                              x-text="'Barang #' + (idx + 1)"></span>
                        <span class="text-xs font-bold text-white truncate max-w-[240px] sm:max-w-[350px]"
                              x-text="item.nama_barang || 'Barang Baru'"></span>
                    </div>
                    <template x-if="item.nibar">
                        <span class="text-[9.5px] font-mono font-bold text-cyan-300 bg-cyan-950/60 border border-cyan-800/80 px-2 py-0.5 rounded-md flex items-center gap-1 shadow-sm">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                            <span x-text="'NIBAR: ' + item.nibar"></span>
                        </span>
                    </template>
                </div>

                <!-- Grid Input Kolom -->
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                    <!-- 1. Nama Barang (Cols 5) -->
                    <div class="sm:col-span-5">
                        <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">
                            Nama Barang:
                        </label>
                        <input type="text" x-model="item.nama_barang"
                               @input="onExtracomItemChange()"
                               placeholder="Nama barang..."
                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-medium transition-all"
                               :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                    </div>

                    <!-- 2. Volume & Satuan (Cols 3) -->
                    <div class="sm:col-span-3">
                        <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">
                            Volume &amp; Satuan:
                        </label>
                        <div class="flex items-center gap-1.5">
                            <input type="number" min="1" x-model.number="item.jumlah_volume" placeholder="1"
                                   @input="if (item.jumlah_volume !== undefined && item.jumlah_volume !== null && item.jumlah_volume < 1 && item.jumlah_volume !== '') item.jumlah_volume = 1; onExtracomItemChange();"
                                   title="Jumlah Volume"
                                   class="w-14 shrink-0 bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white text-center focus:outline-none font-bold no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                   :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                            <input type="text" x-model="item.satuan"
                                   @input="onExtracomItemChange()"
                                   placeholder="Satuan..."
                                   title="Satuan Barang"
                                   class="flex-1 min-w-0 bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none"
                                   :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                        </div>
                    </div>

                    <!-- 3. Harga Satuan (Rp) (Cols 4) -->
                    <div class="sm:col-span-4">
                        <div class="mb-1">
                            <label class="block font-bold text-[10px] uppercase tracking-wider"
                                   :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'text-emerald-300' : 'text-amber-300'">
                                Harga Satuan (Rp):
                            </label>
                        </div>
                        <div class="relative flex items-center rounded-xl bg-slate-950 border focus-within:ring-2 shadow-inner transition-all overflow-hidden"
                             :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM'
                                 ? (parseFloat(item.harga_satuan) <= 300000 ? 'border-rose-500 text-rose-300 focus-within:border-rose-400 focus-within:ring-rose-500/20' : 'border-slate-700 text-emerald-400 focus-within:border-emerald-500 focus-within:ring-emerald-500/20')
                                 : (parseFloat(item.harga_satuan) > 300000 ? 'border-rose-500 text-rose-300 focus-within:border-rose-400 focus-within:ring-rose-500/20' : (parseFloat(item.harga_satuan) <= 0 ? 'border-amber-500/60 text-amber-300 focus-within:border-amber-400 focus-within:ring-amber-500/20' : 'border-slate-700 text-emerald-400 focus-within:border-emerald-500 focus-within:ring-emerald-500/20'))">
                            <span class="px-3 py-2 bg-slate-900 border-r border-slate-800 text-slate-400 font-mono font-bold text-xs select-none">
                                Rp
                            </span>
                            <input type="text"
                                   inputmode="numeric"
                                   :value="formatRupiahInput(item.harga_satuan)"
                                   @keydown="if (!/[0-9]/.test($event.key) && !['Backspace', 'Delete', 'ArrowLeft', 'ArrowRight', 'Tab', 'Enter', 'Home', 'End'].includes($event.key) && !$event.ctrlKey && !$event.metaKey) { $event.preventDefault(); }"
                                   @input="updateItemHargaSatuan(item, $event)"
                                   @paste="$nextTick(() => updateItemHargaSatuan(item, $event))"
                                   @focus="$event.target.select()"
                                   placeholder="0"
                                   class="w-full bg-transparent px-3 py-2 text-xs font-mono font-extrabold focus:outline-none text-right transition-colors"
                                   :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM'
                                        ? (parseFloat(item.harga_satuan) <= 300000 ? 'text-rose-300 placeholder-rose-700' : 'text-emerald-400 placeholder-slate-600')
                                        : (parseFloat(item.harga_satuan) > 300000 ? 'text-rose-300 placeholder-rose-700' : (parseFloat(item.harga_satuan) <= 0 ? 'text-amber-300 placeholder-amber-700' : 'text-emerald-400 placeholder-slate-600'))">
                        </div>
                    </div>
                </div>

                <!-- Subtotal & Status Validasi per Item -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pt-2 border-t border-slate-800/60 text-[11px] gap-2">
                    <div>
                        <!-- Jika Ekstrakomptabel: Validasi <= 300.000 & > 0 -->
                        <template x-if="formData.jenis_reklas === 'EKSTRAKOMPTABEL'">
                            <div>
                                <template x-if="parseFloat(item.harga_satuan) > 300000">
                                    <div class="space-y-0.5">
                                        <span class="text-rose-400 font-bold flex items-center gap-1.5">
                                            <span>⚠️</span>
                                            <span>Melebihi batas nilai maksimum Ekstrakomtable</span>
                                        </span>
                                        <div class="text-[10px] text-rose-300/85 pl-5 flex items-center gap-1.5">
                                            <span>ℹ️</span>
                                            <span>Batas Ekstrakomtable: Nilai satuan maksimal <strong>Rp 300.000</strong> per unit</span>
                                        </div>
                                        <div class="text-[9.5px] text-emerald-300/90 pl-5 flex items-center gap-1.5 mt-0.5">
                                            <span>✨</span>
                                            <span>Nilai ini memenuhi syarat kapitalisasi ke <strong>Intrakomtable (&gt; Rp 300.000)</strong>.</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="parseFloat(item.harga_satuan) <= 0">
                                    <div class="space-y-0.5">
                                        <span class="text-amber-400 font-medium flex items-center gap-1.5">
                                            <span>⚠️</span>
                                            <span>Wajib diisi (&gt; Rp 0)</span>
                                        </span>
                                        <div class="text-[10px] text-amber-300/80 pl-5">
                                            Batas Ekstrakomtable: Rp 1 s/d Rp 300.000 per unit
                                        </div>
                                    </div>
                                </template>
                                <template x-if="parseFloat(item.harga_satuan) > 0 && parseFloat(item.harga_satuan) <= 300000">
                                    <div class="space-y-0.5">
                                        <span class="text-slate-400">
                                            <span class="text-slate-300 font-medium" x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span> &times; 
                                            <span class="text-emerald-400 font-mono font-bold" x-text="'Rp ' + Number(item.harga_satuan || 0).toLocaleString('id-ID')"></span>
                                        </span>
                                        <div class="text-[9.5px] text-emerald-400/80 pl-0.5 flex items-center gap-1.5">
                                            <span>✓</span>
                                            <span>Memenuhi batas Ekstrakomtable (&le; Rp 300.000)</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        <!-- Jika Intrakomptabel: Validasi > 300.000 -->
                        <template x-if="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM'">
                            <div>
                                <template x-if="parseFloat(item.harga_satuan) <= 300000">
                                    <div class="space-y-0.5">
                                        <span class="text-rose-400 font-bold flex items-center gap-1.5">
                                            <span>⚠️</span>
                                            <span>Belum memenuhi batas nilai minimum Intrakomtable</span>
                                        </span>
                                        <div class="text-[10px] text-rose-300/85 pl-5 flex items-center gap-1.5">
                                            <span>ℹ️</span>
                                            <span>Batas Intrakomtable: Nilai satuan minimal di atas <strong>Rp 300.000 (&gt; Rp 300.000)</strong> per unit</span>
                                        </div>
                                    </div>
                                </template>
                                <template x-if="parseFloat(item.harga_satuan) > 300000">
                                    <div class="space-y-0.5">
                                        <span class="text-slate-400">
                                            <span class="text-slate-300 font-medium" x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span> &times; 
                                            <span class="text-emerald-400 font-mono font-bold" x-text="'Rp ' + Number(item.harga_satuan || 0).toLocaleString('id-ID')"></span>
                                        </span>
                                        <div class="text-[9.5px] text-emerald-400/80 pl-0.5 flex items-center gap-1.5">
                                            <span>✓</span>
                                            <span>Memenuhi syarat kapitalisasi Intrakomtable (&gt; Rp 300.000)</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="text-left sm:text-right shrink-0">
                        <span class="text-slate-400 text-[10px]">Subtotal: </span>
                        <span class="font-mono font-extrabold text-emerald-300 text-xs sm:text-sm"
                              x-text="'Rp ' + Number((item.jumlah_volume || 1) * (item.harga_satuan || 0)).toLocaleString('id-ID')"></span>
                    </div>
                </div>
            </div>
        </template>
    </div>

    <!-- 4. Total Ringkasan Ekstrakomptabel / Intrakomptabel -->
    <div class="p-4 rounded-2xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-md"
         :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'bg-emerald-950/20 border-emerald-500/30' : 'bg-amber-950/20 border-amber-500/30'">
        <div>
            <div class="text-[10.5px] uppercase font-bold tracking-wider"
                 :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'text-emerald-300' : 'text-amber-300'"
                 x-text="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'Total Nilai Intrakomtable:' : 'Total Nilai Ekstrakomtable:'"></div>
            <div class="text-xs text-slate-400 mt-0.5">
                Semula: <span class="line-through text-slate-400 font-mono" x-text="'Rp ' + Number(selectedAstap?.total_realisasi || 0).toLocaleString('id-ID')"></span>
                <span class="font-bold ml-1.5" :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'text-emerald-400' : 'text-amber-400'">➔ Disesuaikan</span>
            </div>
        </div>
        <div class="text-left sm:text-right">
            <div class="text-base sm:text-lg font-extrabold font-mono"
                 :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'text-emerald-300' : 'text-amber-300'"
                 x-text="'Rp ' + Number(getReklasExtracomTotal()).toLocaleString('id-ID')"></div>
            <div class="text-[10.5px] font-mono"
                 :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'text-emerald-200/80' : 'text-amber-200/80'"
                 x-text="getReklasExtracomTotalVolume() + ' Total Unit'"></div>
        </div>
    </div>
</div>
