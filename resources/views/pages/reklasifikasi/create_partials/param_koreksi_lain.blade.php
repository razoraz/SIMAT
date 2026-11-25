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

                <!-- 1B. Banner Info Dinamis & Pemetaan Kolom RMB -->
                <div class="p-3.5 rounded-2xl border text-[11px] leading-relaxed flex items-start gap-2.5 transition-all shadow-inner"
                     :class="{
                         'bg-indigo-950/30 border-indigo-500/35 text-indigo-200': formData.sub_koreksi === 'biasa',
                         'bg-cyan-950/30 border-cyan-500/35 text-cyan-200': formData.sub_koreksi === 'lkd',
                         'bg-emerald-950/30 border-emerald-500/35 text-emerald-200': formData.sub_koreksi === 'manset'
                     }">
                    <span class="text-base shrink-0">💡</span>
                    <div class="flex-1">
                        <template x-if="formData.sub_koreksi === 'biasa'">
                            <div>
                                <strong>Koreksi Biasa (Internal RSUD):</strong> Digunakan jika ada kesalahan input nominal, pembulatan SP2D, atau selisih pencatatan antara bendahara pengeluaran dan aset.
                                Di matriks RMB 21 kolom, pengurangan akan masuk ke <strong>Kolom 15 (KOREKSI &minus;)</strong> dan penambahan ke <strong>Kolom 5 (Koreksi Rekening +)</strong>.
                            </div>
                        </template>
                        <template x-if="formData.sub_koreksi === 'lkd'">
                            <div>
                                <strong>Koreksi LKD (BPK RI):</strong> Digunakan untuk menindaklanjuti temuan resmi LHP BPK atas LKPD, kelebihan bayar yang telah disetor ke Kasda, atau koreksi kapitalisasi auditor eksternal.
                                Di matriks RMB 21 kolom, pengurangan akan masuk ke <strong>Kolom 16 (KOREKSI LKD &minus;)</strong> dan penambahan ke <strong>Kolom 6 (Koreksi LKD +)</strong>.
                            </div>
                        </template>
                        <template x-if="formData.sub_koreksi === 'manset'">
                            <div>
                                <strong>Koreksi Manset (Bidang Aset BPKAD):</strong> Digunakan untuk menyelaraskan nilai saldo aset antara SIMAT RSUD dengan aplikasi SIMDA BMD / E-Manset BPKAD Bondowoso.
                                Di matriks RMB 21 kolom, pengurangan akan masuk ke <strong>Kolom 17 (KOREKSI MANSET &minus;)</strong> dan penambahan ke <strong>Kolom 7 (Koreksi Manset +)</strong>.
                            </div>
                        </template>
                    </div>
                </div>

                <!-- 1C. Dokumen Dasar Penyesuaian Nilai & Unggah Scan PDF -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-3">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5">
                            <span>📄</span>
                            <span>Dokumen Dasar Penyesuaian Nilai:</span>
                        </div>
                        <span x-show="formData.sub_koreksi === 'lkd' || formData.sub_koreksi === 'manset'" class="text-[9.5px] font-semibold text-rose-400">
                            * Wajib Berdasar LHP / BA Rekon
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8 space-y-1">
                            <label class="block text-[10.5px] font-bold text-slate-300">
                                <span x-text="formData.sub_koreksi === 'lkd' ? 'Nomor LHP BPK RI / Rekomendasi' : (formData.sub_koreksi === 'manset' ? 'Nomor BA Rekonsiliasi Manset BPKAD' : 'Nomor Dokumen / Nota Rekonsiliasi Internal')"></span>
                                <span x-show="formData.sub_koreksi === 'lkd' || formData.sub_koreksi === 'manset'" class="text-rose-400 font-black">*</span>
                            </label>
                            <input type="text" x-model="formData.no_dokumen_koreksi"
                                   :placeholder="formData.sub_koreksi === 'lkd' ? 'Contoh: 12/LHP/XVIII.SBY/05/2026' : (formData.sub_koreksi === 'manset' ? 'Contoh: 000.2/BA-MANSET/BPKAD/2026' : 'Contoh: 000.1.2/BA-REKON/RSUD/2026')"
                                   class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl text-xs font-mono text-white focus:outline-none">
                        </div>
                        <div class="sm:col-span-4 space-y-1">
                            <label class="block text-[10.5px] font-bold text-slate-300">
                                <span>Tanggal Dokumen</span>
                                <span x-show="formData.sub_koreksi === 'lkd' || formData.sub_koreksi === 'manset'" class="text-rose-400 font-black">*</span>
                            </label>
                            <input type="date" x-model="formData.tanggal_dokumen_koreksi"
                                   class="w-full px-3.5 py-2 bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl text-xs font-mono text-white focus:outline-none">
                        </div>
                    </div>

                    <!-- Slot Upload Scan PDF LHP BPK / BA Rekonsiliasi (Opsional) -->
                    <div class="pt-2.5 border-t border-slate-800/80">
                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between gap-2">
                            <span class="flex items-center gap-1.5">
                                <span>📎</span>
                                <span>Unggah Scan PDF Dokumen (LHP BPK / BA Rekonsiliasi / Bukti Kas):</span>
                            </span>
                            <span class="text-[9.5px] font-normal text-slate-400 shrink-0">PDF, JPG, PNG (Maks. 10 MB)</span>
                        </label>
                        <div class="p-3 rounded-xl bg-slate-950/70 border-2 border-dashed border-slate-700 hover:border-cyan-400/60 transition-all text-center relative group">
                            <input type="file" id="reklas_koreksi_dokumen_file" @change="handleDokumenFile($event)" accept=".pdf,.jpg,.jpeg,.png"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            <div class="space-y-1 pointer-events-none">
                                <p class="text-xs font-bold text-slate-300 group-hover:text-cyan-300 transition-colors">
                                    <span x-show="!selectedDokumenFile">Klik atau seret file PDF / scan bukti ke sini (Opsional)</span>
                                    <span x-show="selectedDokumenFile" class="text-cyan-300 font-mono" x-text="selectedDokumenFile ? selectedDokumenFile.name : ''"></span>
                                </p>
                                <p class="text-[9.5px] text-slate-500">Bukti resmi pendukung pemeriksaan auditor &amp; pembukuan BPKAD</p>
                            </div>
                        </div>
                        <div x-show="selectedDokumenFile" class="mt-2 flex items-center justify-between p-2 rounded-xl bg-cyan-950/40 border border-cyan-500/30 text-xs">
                            <span class="text-cyan-200 font-mono truncate flex items-center gap-2">
                                <span>📄</span>
                                <strong x-text="selectedDokumenFile?.name"></strong>
                                <span class="text-slate-400" x-text="'(' + ((selectedDokumenFile?.size || 0) / 1024).toFixed(1) + ' KB)'"></span>
                            </span>
                            <button type="button" @click="clearDokumenFile()" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 text-[10.5px] font-bold transition-all ml-2 cursor-pointer shrink-0">
                                ✕ Batal
                            </button>
                        </div>
                    </div>
                </div>

                <!-- 1D. Grid Nilai Anggaran (DPA/RBA) & Total Nilai Realisasi Aset -->
                <div class="grid gap-3" :class="isReklasBelanjaModal() ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1'">
                    <!-- Nilai Anggaran (DPA/RBA) - Hanya Tampil di Belanja Modal & Terkunci -->
                    <template x-if="isReklasBelanjaModal()">
                        <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-800 space-y-2">
                            <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider flex items-center gap-1.5">
                                <span>🔒</span>
                                <span>Nilai Anggaran (DPA/RBA):</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-slate-500 text-xs font-mono font-bold">Rp</span>
                                <input type="text" readonly
                                       :value="Number(selectedAstap?.jumlah_anggaran || 0).toLocaleString('id-ID')"
                                       class="w-full pl-9 pr-3.5 py-1.5 bg-slate-950/70 border border-slate-700/80 rounded-xl text-xs font-mono font-bold text-slate-300 cursor-not-allowed text-right select-none focus:outline-none">
                            </div>
                            <p class="text-[9.5px] text-slate-500 leading-tight">Pagu awal DPA APBD/BLUD bersifat permanen sebagai dokumen otorisasi awal.</p>
                        </div>
                    </template>

                    <!-- Total Nilai Realisasi Aset -->
                    <div class="p-3.5 rounded-xl bg-emerald-950/30 border border-emerald-500/40 space-y-2"
                         :class="!isReklasBelanjaModal() ? 'w-full' : ''">
                        <div class="flex items-center justify-between gap-2">
                            <label class="block text-emerald-300 font-bold text-[10.5px] uppercase tracking-wider flex items-center gap-1.5">
                                <span>📦</span>
                                <span>Total Nilai Realisasi Aset:</span>
                            </label>
                            <template x-if="!isReklasBelanjaModal()">
                                <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold bg-cyan-950/80 border border-cyan-800/80 text-cyan-300"
                                      x-text="selectedAstap?.sumber_dana === 'hibah' ? 'Perolehan Hibah (Non-DPA)' : (selectedAstap?.sumber_dana === 'kemitraan' ? 'Perolehan Kemitraan (Non-DPA)' : 'Perolehan Non-DPA')">
                                </span>
                            </template>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2 text-slate-400 text-xs font-mono font-bold">Rp</span>
                            <input type="text" readonly
                                   :value="Number(formData.nilai_realisasi_baru || 0).toLocaleString('id-ID')"
                                   class="w-full pl-9 pr-3 py-1.5 bg-slate-950 border border-emerald-500/40 rounded-xl text-xs font-mono font-extrabold text-emerald-300 cursor-not-allowed text-right focus:outline-none select-none">
                        </div>
                        <p class="text-[9.5px] text-slate-400 leading-tight">Terkalkulasi otomatis dari akumulasi nilai barang (Barang 1, 2, dst) di bawah.</p>
                    </div>
                </div>

                <!-- Peringatan Visual Pagu Belanja Modal DPA (Jika Tambah Nilai Melampaui DPA) -->
                <template x-if="isReklasBelanjaModal() && formData.tipe_koreksi === 'tambah' && parseFloat(formData.nilai_realisasi_baru || 0) > parseFloat(selectedAstap?.jumlah_anggaran || 0)">
                    <div class="p-3 rounded-2xl bg-amber-500/10 border border-amber-500/40 text-amber-200 text-xs flex items-start gap-2.5 shadow-md">
                        <span class="text-base shrink-0 mt-0.5">⚠️</span>
                        <div class="space-y-1 flex-1 leading-relaxed">
                            <strong class="text-amber-300 font-extrabold uppercase tracking-wide text-[11px] block">Peringatan Pagu Belanja Modal DPA:</strong>
                            <div>
                                Nilai realisasi baru (<span class="font-mono font-bold text-amber-300" x-text="'Rp ' + Number(formData.nilai_realisasi_baru || 0).toLocaleString('id-ID')"></span>) melampaui Pagu Anggaran DPA (<span class="font-mono font-bold text-slate-300" x-text="'Rp ' + Number(selectedAstap?.jumlah_anggaran || 0).toLocaleString('id-ID')"></span>) sebesar <span class="font-mono font-bold text-rose-300" x-text="'Rp ' + Number((parseFloat(formData.nilai_realisasi_baru || 0) - parseFloat(selectedAstap?.jumlah_anggaran || 0))).toLocaleString('id-ID')"></span>.
                            </div>
                            <p class="text-[10px] text-amber-300/80">
                                Pastikan penambahan realisasi belanja modal ini telah memiliki dasar otorisasi Pergeseran Anggaran atau Perubahan DPA (PAK APBD/BLUD).
                            </p>
                        </div>
                    </div>
                </template>

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
