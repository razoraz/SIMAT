            <!-- 2B. Panel Khusus Penyesuaian Nilai Realisasi / Perubahan Total Dana -->
            <div x-show="formData.jenis_reklas === 'KOREKSI_LAIN'"
                 x-transition.duration.200ms
                 class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-cyan-500/30 space-y-4 shadow-xl">
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-xs font-black flex items-center justify-center">💰</span>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                            Penyesuaian Nilai Buku &amp; Perubahan Total Dana Aset
                        </span>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">
                        Penyeimbang: Koreksi Nilai Realisasi
                    </span>
                </div>

                <!-- Sub-Kategori Koreksi Nilai -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span>⚖️</span>
                            <span>Sub-Kategori Koreksi Nilai:</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[9.5px] font-mono text-cyan-300">Standar BPKAD Bondowoso</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <!-- 1. Koreksi Biasa -->
                        <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition-all select-none group"
                               :class="formData.sub_koreksi === 'biasa' ? 'border-indigo-500 bg-indigo-500/15 shadow-sm ring-1 ring-indigo-500/30' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900'">
                            <input type="radio" x-model="formData.sub_koreksi" value="biasa" class="hidden" style="display: none;">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold" :class="formData.sub_koreksi === 'biasa' ? 'text-indigo-300' : 'text-white'">
                                    📝 Koreksi Biasa
                                </span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded"
                                      :class="formData.sub_koreksi === 'biasa' ? 'bg-indigo-500/30 text-indigo-200' : 'bg-slate-800 text-slate-400'">
                                    SP2D / Kas RSUD
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-400 leading-tight">
                                Internal kas RSUD, pembulatan SP2D, koreksi salah catat belanja.
                            </p>
                        </label>

                        <!-- 2. Koreksi LKD -->
                        <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition-all select-none group"
                               :class="formData.sub_koreksi === 'lkd' ? 'border-cyan-500 bg-cyan-500/15 shadow-sm ring-1 ring-cyan-500/30' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900'">
                            <input type="radio" x-model="formData.sub_koreksi" value="lkd" class="hidden" style="display: none;">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold" :class="formData.sub_koreksi === 'lkd' ? 'text-cyan-300' : 'text-white'">
                                    ⚖️ Koreksi LKD
                                </span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded"
                                      :class="formData.sub_koreksi === 'lkd' ? 'bg-cyan-500/30 text-cyan-200' : 'bg-slate-800 text-slate-400'">
                                    Audit BPK / LHP
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-400 leading-tight">
                                Temuan audit BPK RI, rekomendasi LHP LKPD, kelebihan bayar/TGR.
                            </p>
                        </label>

                        <!-- 3. Koreksi Manset -->
                        <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition-all select-none group"
                               :class="formData.sub_koreksi === 'manset' ? 'border-emerald-500 bg-emerald-500/15 shadow-sm ring-1 ring-emerald-500/30' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900'">
                            <input type="radio" x-model="formData.sub_koreksi" value="manset" class="hidden" style="display: none;">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold" :class="formData.sub_koreksi === 'manset' ? 'text-emerald-300' : 'text-white'">
                                    🏢 Koreksi Manset
                                </span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded"
                                      :class="formData.sub_koreksi === 'manset' ? 'bg-emerald-500/30 text-emerald-200' : 'bg-slate-800 text-slate-400'">
                                    Sinkron E-Manset
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-400 leading-tight">
                                Penyelarasan SIMDA BMD / E-Manset BPKAD Kab. Bondowoso.
                            </p>
                        </label>
                    </div>
                </div>

                <!-- 2. Rincian Barang & Nilai Realisasi Baru (Barang 1, Barang 2, dst) Sesuai Standar Modul ASTAP -->
                <div class="space-y-3 pt-2">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 pb-2 border-b border-slate-800/80">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs font-black text-cyan-300 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📦</span>
                                <span>Rincian Nilai Realisasi Barang:</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-mono font-extrabold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 shadow-xs"
                                  x-text="(formData.reklas_items ? formData.reklas_items.length : 0) + ' Item Terdaftar'"></span>
                        </div>
                        <span class="text-[10.5px] text-slate-400 font-medium">Ubah nilai per unit barang temuan / penyesuaian</span>
                    </div>

                    <!-- Empty state jika belum memilih unit -->
                    <template x-if="!formData.reklas_items || formData.reklas_items.length === 0">
                        <div class="p-4 rounded-2xl bg-slate-900/60 border border-dashed border-slate-800 text-center space-y-1">
                            <span class="text-amber-400 text-base block">⚠️</span>
                            <p class="text-xs font-bold text-slate-300">Belum ada unit register yang dipilih</p>
                            <p class="text-[11px] text-slate-500">Pilih minimal 1 register (NIBAR) pada daftar di atas untuk menyesuaikan rincian nilai barang.</p>
                        </div>
                    </template>

                    <!-- Cards Container Barang 1 & 2 -->
                    <div x-show="formData.reklas_items && formData.reklas_items.length > 0"
                         class="space-y-3 max-h-[350px] overflow-y-auto custom-scrollbar pr-1">
                        <template x-for="(item, idx) in (formData.reklas_items || [])" :key="item.register_id || idx">
                            <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/90 hover:border-cyan-500/40 transition-all space-y-3 shadow-lg group backdrop-blur-sm">
                                <!-- Baris Atas Item: Badge Nomor & Nama Barang & Volume -->
                                <div class="flex items-center justify-between gap-2.5">
                                    <div class="flex items-center gap-2 min-w-0 flex-1">
                                        <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-mono font-black bg-cyan-500/15 text-cyan-300 border border-cyan-500/35 shrink-0 shadow-xs"
                                              x-text="'Barang #' + (idx + 1)"></span>
                                        <span class="text-xs sm:text-sm font-extrabold text-white truncate" 
                                              :title="item.nama_barang || ('Barang #' + (idx + 1))"
                                              x-text="item.nama_barang || ('Barang #' + (idx + 1))"></span>
                                        <template x-if="item.nibar">
                                            <span class="text-[9.5px] font-mono font-bold text-cyan-300 bg-cyan-950/60 border border-cyan-800/80 px-2 py-0.5 rounded-md shrink-0"
                                                  x-text="'NIBAR: ' + item.nibar"></span>
                                        </template>
                                    </div>
                                    <div class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 text-[11px] font-mono font-semibold shrink-0 shadow-inner flex items-center gap-1.5">
                                        <span class="text-slate-500 text-[10px]">Qty:</span>
                                        <span class="text-cyan-300 font-bold" x-text="item.jumlah_volume || 1"></span>
                                        <span class="text-slate-400 text-[10px]" x-text="item.satuan || 'Unit'"></span>
                                    </div>
                                </div>

                                <!-- Grid Input: Nilai Satuan & Subtotal (Symmetric & Responsive) -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start pt-3 border-t border-slate-800/80">
                                    <!-- Nilai Satuan -->
                                    <div class="space-y-1.5">
                                        <label class="flex items-center gap-1.5 text-slate-400 font-bold text-[10px] uppercase tracking-wider h-4">
                                            <span>🏷️</span>
                                            <span>Nilai Satuan (Rp):</span>
                                        </label>
                                        <div class="relative flex items-center h-10 rounded-xl bg-slate-900/90 border border-cyan-500/40 focus-within:border-cyan-400 focus-within:ring-2 focus-within:ring-cyan-500/20 shadow-inner transition-all overflow-hidden">
                                            <span class="h-full px-3 flex items-center justify-center bg-slate-950/80 border-r border-slate-800 text-cyan-400 font-mono font-bold text-xs select-none">
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
                                                   class="w-full h-full bg-transparent px-3 text-xs sm:text-sm font-mono font-extrabold text-cyan-200 focus:outline-none text-right placeholder-slate-600">
                                        </div>
                                    </div>

                                    <!-- Subtotal -->
                                    <div class="space-y-1.5">
                                        <div class="flex items-center justify-between text-slate-400 font-bold text-[10px] uppercase tracking-wider h-4">
                                            <span>Subtotal Barang:</span>
                                            <span class="text-[9.5px] font-mono text-slate-500 font-normal lowercase" x-text="'(' + (item.jumlah_volume || 1) + ' ' + (item.satuan || 'unit') + ')'"></span>
                                        </div>
                                        <div class="flex items-center justify-between px-3.5 h-10 rounded-xl bg-emerald-950/30 border border-emerald-500/40 shadow-inner">
                                            <span class="text-[9.5px] font-mono font-bold text-emerald-400/80 uppercase tracking-wider">Subtotal</span>
                                            <div class="text-xs sm:text-sm font-mono font-black text-emerald-300"
                                                 x-text="'Rp ' + Number((item.jumlah_volume || 1) * (parseFloat(item.harga_satuan) || 0)).toLocaleString('id-ID')">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <!-- 3. Ringkasan Perbandingan & Dampak Selisih Koreksi (Gambar 2 Footer) -->
                    <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800 flex flex-wrap items-center justify-between gap-2.5 text-xs shadow-md">
                        <div class="flex items-center space-x-2">
                            <span class="text-slate-400">Realisasi Semula:</span>
                            <span class="font-mono text-slate-200 font-bold" x-text="'Rp ' + Number(selectedAstap?.total_realisasi || 0).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <span class="text-slate-400">➔ Realisasi Baru:</span>
                            <span class="font-mono text-emerald-400 font-extrabold" x-text="'Rp ' + Number(formData.nilai_realisasi_baru || 0).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex items-center space-x-1.5">
                            <span class="text-slate-400">Dampak Koreksi:</span>
                            <template x-if="parseFloat(formData.nilai_reklas || 0) > 0">
                                <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-black border"
                                      :class="formData.tipe_koreksi === 'kurang' ? 'bg-rose-500/20 text-rose-300 border-rose-500/40 shadow-rose-950/20' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40 shadow-emerald-950/20'"
                                      x-text="(formData.tipe_koreksi === 'kurang' ? '🔻 Berkurang Rp ' : '🔺 Bertambah Rp ') + Number(formData.nilai_reklas).toLocaleString('id-ID')"></span>
                            </template>
                            <template x-if="parseFloat(formData.nilai_reklas || 0) <= 0">
                                <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-bold bg-slate-800 text-slate-400 border border-slate-700">Tidak ada selisih nilai</span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
