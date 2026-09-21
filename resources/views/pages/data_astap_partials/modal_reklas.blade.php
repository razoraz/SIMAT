        <!-- ========================================================================= -->
        <!-- MODAL DIALOG: REKLASIFIKASI ASET TETAP (RSDK)                             -->
        <!-- ========================================================================= -->
        <div x-show="showReklasModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="showReklasModal = false"
                 class="bg-slate-900 border border-indigo-500/40 rounded-3xl max-w-2xl w-full shadow-2xl flex flex-col max-h-[92vh] overflow-hidden my-auto"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <!-- 1. Modal Header (Fixed Top) -->
                <div class="shrink-0 px-6 py-4 border-b border-slate-800 flex items-start justify-between bg-slate-950/40">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 rounded-2xl bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xl shadow-md shrink-0">
                            🔄
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-base sm:text-lg font-extrabold text-white">Reklasifikasi Aset Tetap</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">RSDK</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">Penyesuaian klasifikasi, pemindahan KIB, atau pengalihan ke Ekstrakomptabel sesuai standar akuntansi RSUD dr. H. Koesnadi.</p>
                        </div>
                    </div>
                    <button type="button" @click="showReklasModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg text-lg font-bold cursor-pointer transition-colors">&times;</button>
                </div>

                <!-- 2. Modal Body (Scrollable Middle) -->
                <div class="flex-1 overflow-y-auto custom-scrollbar px-6 py-5 space-y-4">

                    <!-- Info Aset yang Dipilih (Card Ringkas) -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2.5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nama Barang / Aset</span>
                                <div class="text-sm font-extrabold text-white truncate" x-text="selectedAstapReklas?.nama_barang || '-'"></div>
                                <div class="text-[11px] font-mono text-cyan-400 font-medium mt-0.5" x-text="'Kode: ' + (selectedAstapReklas?.kode_barang || '-')"></div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block" x-text="(reklasJenis === 'extracom' || reklasJenis === 'intracom') ? 'Nilai Realisasi (Disesuaikan)' : 'Nilai Realisasi'"></span>
                                <div class="text-sm font-extrabold font-mono"
                                     :class="reklasJenis === 'extracom' ? 'text-amber-300' : (reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-emerald-400')"
                                     x-text="(reklasJenis === 'extracom' || reklasJenis === 'intracom') ? ('Rp ' + Number(getReklasExtracomTotal()).toLocaleString('id-ID')) : (selectedAstapReklas?.jumlah_realisasi || 'Rp 0')"></div>
                                <div class="text-[10.5px] font-mono mt-0.5"
                                     :class="reklasJenis === 'extracom' ? 'text-amber-200/80' : (reklasJenis === 'intracom' ? 'text-emerald-200/80' : 'text-teal-300')"
                                     x-text="((reklasJenis === 'extracom' || reklasJenis === 'intracom') ? getReklasExtracomTotalVolume() : (selectedAstapReklas?.jumlah_volume || 1)) + ' Unit'"></div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 pt-2 border-t border-slate-800/80">
                            <span class="text-[11px] text-slate-400">Kategori Saat Ini:</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 shrink-0"
                                  x-text="selectedAstapReklas?.category || '-'"></span>
                            <span class="text-[11px] text-slate-300 truncate" x-text="selectedAstapReklas?.jenis_aset_nama || ''"></span>
                        </div>
                    </div>

                    <!-- Form Pilihan Jenis Reklasifikasi (Custom Radio Cards) -->
                    <div>
                        <label class="block text-slate-300 font-bold text-xs uppercase tracking-wider mb-2">
                            🎯 Pilih Jenis Reklasifikasi:
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- 1. Ekstrakomptabel / Kapitalisasi Intrakomptabel -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border transition-all select-none"
                                   style="gap: 12px;"
                                   :class="{
                                       'opacity-40 cursor-not-allowed bg-slate-950/30 border-slate-800': isReklasExtracomDisabled(),
                                       'cursor-pointer bg-amber-500/10 border-amber-500/50 shadow-sm shadow-amber-500/10': !isReklasExtracomDisabled() && reklasJenis === 'extracom',
                                       'cursor-pointer bg-emerald-500/10 border-emerald-500/50 shadow-sm shadow-emerald-500/10': !isReklasExtracomDisabled() && reklasJenis === 'intracom',
                                       'cursor-pointer bg-slate-950/60 border-slate-800 hover:border-slate-700': !isReklasExtracomDisabled() && reklasJenis !== 'extracom' && reklasJenis !== 'intracom'
                                   }">
                                <input type="radio" name="reklas_jenis" :value="isCurrentAstapExtracom() ? 'intracom' : 'extracom'" x-model="reklasJenis" :disabled="isReklasExtracomDisabled()" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="{
                                         'border-amber-400 bg-amber-500/20': reklasJenis === 'extracom' && !isReklasExtracomDisabled(),
                                         'border-emerald-400 bg-emerald-500/20': reklasJenis === 'intracom' && !isReklasExtracomDisabled(),
                                         'border-slate-700 bg-slate-900': reklasJenis !== 'extracom' && reklasJenis !== 'intracom'
                                     }">
                                    <div x-show="(reklasJenis === 'extracom' || reklasJenis === 'intracom') && !isReklasExtracomDisabled()" 
                                         class="rounded-full" :class="reklasJenis === 'intracom' ? 'bg-emerald-400' : 'bg-amber-400'" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs" 
                                              :class="{
                                                  'text-amber-300': reklasJenis === 'extracom' && !isReklasExtracomDisabled(),
                                                  'text-emerald-300': reklasJenis === 'intracom' && !isReklasExtracomDisabled(),
                                                  'text-white': reklasJenis !== 'extracom' && reklasJenis !== 'intracom'
                                              }"
                                              x-text="isCurrentAstapExtracom() ? 'Intrakomtable' : 'Ekstrakomtable'"></span>
                                        <template x-if="isReklasExtracomDisabled()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30">Terkunci (SAP)</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5" 
                                         x-text="isReklasExtracomDisabled() ? 'Tidak berlaku untuk kelompok aset ini (Wajib Intrakomtable)' : (isCurrentAstapExtracom() ? 'Pengalihan aset ke kelompok Intrakomtable' : 'Pengalihan aset ke kelompok Ekstrakomtable')"></div>
                                </div>
                            </label>

                            <!-- 2. Antar-KIB / ATB -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer transition-all select-none"
                                   style="gap: 12px;"
                                   :class="reklasJenis === 'antar_kib' ? 'bg-indigo-500/10 border-indigo-500/50 shadow-sm shadow-indigo-500/10' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <input type="radio" name="reklas_jenis" value="antar_kib" x-model="reklasJenis" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="reklasJenis === 'antar_kib' ? 'border-indigo-400 bg-indigo-500/20' : 'border-slate-700 bg-slate-900'">
                                    <div x-show="reklasJenis === 'antar_kib'" class="rounded-full bg-indigo-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-xs" :class="reklasJenis === 'antar_kib' ? 'text-indigo-300' : 'text-white'">Pindah KIB / ke ATB</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Salah kamar (misal: Mesin ke ATB/Software)</div>
                                </div>
                            </label>

                            <!-- 3. Kapitalisasi KDP Selesai -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer transition-all select-none"
                                   style="gap: 12px;"
                                   :class="reklasJenis === 'kdp' ? 'bg-rose-500/10 border-rose-500/50 shadow-sm shadow-rose-500/10' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <input type="radio" name="reklas_jenis" value="kdp" x-model="reklasJenis" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="reklasJenis === 'kdp' ? 'border-rose-400 bg-rose-500/20' : 'border-slate-700 bg-slate-900'">
                                    <div x-show="reklasJenis === 'kdp'" class="rounded-full bg-rose-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-xs" :class="reklasJenis === 'kdp' ? 'text-rose-300' : 'text-white'">Kapitalisasi KDP Selesai</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Pembangunan KIB F selesai 100% jadi KIB C/D</div>
                                </div>
                            </label>

                            <!-- 4. Koreksi Kode Rekening -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer transition-all select-none"
                                   style="gap: 12px;"
                                   :class="reklasJenis === 'koreksi_rekening' ? 'bg-cyan-500/10 border-cyan-500/50 shadow-sm shadow-cyan-500/10' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <input type="radio" name="reklas_jenis" value="koreksi_rekening" x-model="reklasJenis" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="reklasJenis === 'koreksi_rekening' ? 'border-cyan-400 bg-cyan-500/20' : 'border-slate-700 bg-slate-900'">
                                    <div x-show="reklasJenis === 'koreksi_rekening'" class="rounded-full bg-cyan-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-xs" :class="reklasJenis === 'koreksi_rekening' ? 'text-cyan-300' : 'text-white'">Koreksi Rekening / Sub-Rincian</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Koreksi kodefikasi belanja/rekening Simda BMD</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Input Dinamis Berdasarkan Jenis Reklas -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                        <!-- Jika Antar-KIB: Pilih KIB Tujuan -->
                        <div x-show="reklasJenis === 'antar_kib'">
                            <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📦 Klasifikasi / KIB Tujuan:</label>
                            <select x-model="reklasTujuanKib"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-indigo-500">
                                <option value="">-- Pilih KIB / Kelompok Tujuan --</option>
                                <option value="KIB A">KIB A - Tanah</option>
                                <option value="KIB B">KIB B - Peralatan &amp; Mesin</option>
                                <option value="KIB C">KIB C - Gedung &amp; Bangunan</option>
                                <option value="KIB D">KIB D - Jalan, Jaringan &amp; Irigasi</option>
                                <option value="KIB E">KIB E - Aset Tetap Lainnya</option>
                                <option value="ATB">ATB - Aset Tidak Berwujud</option>
                                <option value="ASET LAIN">Aset Lain-Lain</option>
                            </select>
                        </div>

                        <!-- Jika KDP Selesai: Pilih KIB Definitif -->
                        <div x-show="reklasJenis === 'kdp'">
                            <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">🏗️ Alihkan KDP Selesai ke KIB Definitif:</label>
                            <select x-model="reklasTujuanKib"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-rose-500">
                                <option value="KIB C">KIB C - Gedung &amp; Bangunan (Default)</option>
                                <option value="KIB D">KIB D - Jalan, Jaringan &amp; Irigasi</option>
                            </select>
                        </div>

                        <!-- Jika Koreksi Rekening: Input Sub-Rincian Tujuan -->
                        <div x-show="reklasJenis === 'koreksi_rekening'">
                            <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">🏷️ Kode / Sub-Rincian Rekening Tujuan (Simda BMD 108):</label>
                            <input type="text" x-model="reklasTujuanKode" placeholder="Contoh: 1.3.2.05.01 ALAT KANTOR DAN RUMAH TANGGA"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-cyan-500">
                        </div>

                        <!-- Jika Ekstrakomptabel ATAU Kapitalisasi Intrakomptabel: Form Penyesuaian Harga Satuan per Item -->
                        <div x-show="reklasJenis === 'extracom' || reklasJenis === 'intracom'" class="space-y-3">
                            <!-- Jika Intrakom: Pilihan KIB Tujuan -->
                            <div x-show="reklasJenis === 'intracom'" class="p-3.5 rounded-xl bg-slate-900 border border-emerald-500/30 space-y-1.5">
                                <label class="block text-emerald-300 font-bold text-[10.5px] uppercase tracking-wider">🎯 KIB Tujuan Intrakomtable:</label>
                                <select x-model="reklasTujuanKib"
                                        class="w-full bg-slate-950 border border-emerald-500/50 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-emerald-400">
                                    <option value="KIB B">KIB B - Peralatan &amp; Mesin (Definitif)</option>
                                    <option value="KIB E">KIB E - Aset Tetap Lainnya (Definitif)</option>
                                </select>
                                <p class="text-[10px] text-slate-400">Aset akan dipindahkan dari Ekstrakomtable ke KIB terpilih di Neraca Aset Tetap.</p>
                            </div>

                            <!-- Info Box Penjelasan -->
                            <div x-show="reklasJenis === 'extracom'" class="p-3 rounded-xl bg-amber-950/30 border border-amber-500/30 flex items-start space-x-2.5">
                                <span class="text-amber-400 text-sm shrink-0">💡</span>
                                <div class="text-[11px] text-amber-200/90 leading-relaxed">
                                    Barang ini akan dialihkan keluar dari Aset Tetap ke kelompok <strong>Ekstrakomtable</strong>. Anda dapat menyesuaikan harga satuan langsung di bawah ini jika diperlukan.
                                </div>
                            </div>
                            <div x-show="reklasJenis === 'intracom'" class="p-3 rounded-xl bg-emerald-950/30 border border-emerald-500/30 flex items-start space-x-2.5">
                                <span class="text-emerald-400 text-sm shrink-0">💡</span>
                                <div class="text-[11px] text-emerald-200/90 leading-relaxed">
                                    Barang Ekstrakomtable ini akan <strong>dimasukkan kembali ke Neraca Aset Tetap (Intrakomtable)</strong>. Silakan sesuaikan harga satuan langsung di bawah ini jika diperlukan.
                                </div>
                            </div>

                            <!-- Header Section Rincian Barang -->
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold uppercase tracking-wider"
                                          :class="reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-amber-300'">📦 Rincian Barang &amp; Harga Satuan:</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold border"
                                          :class="reklasJenis === 'intracom' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'"
                                          x-text="reklasExtracomItems.length + ' Item'"></span>
                                </div>
                            </div>

                            <!-- Daftar Rincian Barang (Repeater Cards) -->
                            <div class="space-y-2.5 max-h-[300px] overflow-y-auto custom-scrollbar pr-1">
                                <template x-for="(item, idx) in reklasExtracomItems" :key="idx">
                                    <div class="p-3 rounded-xl bg-slate-900/90 border transition-all space-y-2.5"
                                         :class="reklasJenis === 'intracom'
                                            ? (parseFloat(item.harga_satuan) <= 300000 ? 'border-rose-500/60 bg-rose-950/10' : 'border-slate-800 hover:border-slate-700')
                                            : (parseFloat(item.harga_satuan) > 300000 ? 'border-rose-500/60 bg-rose-950/10' : (parseFloat(item.harga_satuan) <= 0 ? 'border-amber-500/40' : 'border-slate-800 hover:border-slate-700'))">
                                        
                                        <!-- Baris Atas Item: Nomor & Nama Barang -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-800 text-slate-300 border border-slate-700"
                                                      x-text="'Barang #' + (idx + 1)"></span>
                                                <span class="text-[11px] font-bold text-white truncate max-w-[220px]" x-text="item.nama_barang || 'Barang Baru'"></span>
                                            </div>
                                        </div>

                                        <!-- Grid Input Kolom -->
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-end">
                                            <!-- 1. Nama Barang (Cols 5) -->
                                            <div class="sm:col-span-5">
                                                <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">Nama Barang:</label>
                                                <input type="text" x-model="item.nama_barang" placeholder="Nama barang..."
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none font-medium"
                                                       :class="reklasJenis === 'intracom' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                                            </div>

                                            <!-- 2. Volume & Satuan (Cols 3) -->
                                            <div class="sm:col-span-3">
                                                <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">Volume &amp; Satuan:</label>
                                                <div class="flex space-x-1">
                                                    <input type="number" min="1" x-model.number="item.jumlah_volume" placeholder="Qty"
                                                           class="w-16 bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white text-center focus:outline-none font-bold"
                                                           :class="reklasJenis === 'intracom' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                                                    <input type="text" x-model="item.satuan" placeholder="Satuan"
                                                           class="flex-1 min-w-0 bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white focus:outline-none"
                                                           :class="reklasJenis === 'intracom' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                                                </div>
                                            </div>

                                            <!-- 3. Harga Satuan (Rp) (Cols 4) -->
                                            <div class="sm:col-span-4">
                                                <div class="mb-1">
                                                    <label class="block font-bold text-[10px] uppercase tracking-wider"
                                                           :class="reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-amber-300'">Harga Satuan (Rp):</label>
                                                </div>
                                                <div class="relative">
                                                    <span class="absolute left-2.5 top-1.5 text-slate-500 text-xs font-mono font-bold">Rp</span>
                                                    <input type="number" step="100" min="0" x-model.number="item.harga_satuan"
                                                           placeholder="0"
                                                           class="w-full pl-8 pr-2.5 py-1.5 bg-slate-950 border rounded-lg text-xs font-mono font-extrabold focus:outline-none transition-colors text-right"
                                                           :class="reklasJenis === 'intracom'
                                                                ? (parseFloat(item.harga_satuan) <= 300000 ? 'border-rose-500 text-rose-300 focus:border-rose-400' : 'border-slate-700 text-emerald-400 focus:border-emerald-500')
                                                                : (parseFloat(item.harga_satuan) > 300000 ? 'border-rose-500 text-rose-300 focus:border-rose-400' : (parseFloat(item.harga_satuan) <= 0 ? 'border-amber-500/50 text-amber-300 focus:border-amber-400' : 'border-slate-700 text-emerald-400 focus:border-emerald-500'))">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subtotal & Status Validasi per Item -->
                                        <div class="flex items-center justify-between pt-1 border-t border-slate-800/60 text-[11px]">
                                            <div>
                                                <!-- Jika Intrakom: Validasi > 300.000 -->
                                                <template x-if="reklasJenis === 'intracom'">
                                                    <div>
                                                        <template x-if="parseFloat(item.harga_satuan) <= 300000">
                                                            <span class="text-rose-400 font-bold flex items-center space-x-1">
                                                                <span>⚠️</span>
                                                                <span>Belum memenuhi batas nilai minimum Intrakomtable</span>
                                                            </span>
                                                        </template>
                                                        <template x-if="parseFloat(item.harga_satuan) > 300000">
                                                            <span class="text-slate-400">
                                                                <span class="text-slate-300 font-medium" x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span> &times; 
                                                                <span class="text-emerald-400 font-mono font-bold" x-text="'Rp ' + Number(item.harga_satuan || 0).toLocaleString('id-ID')"></span>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </template>

                                                <!-- Jika Ekstrakom: Validasi <= 300.000 & > 0 -->
                                                <template x-if="reklasJenis === 'extracom'">
                                                    <div>
                                                        <template x-if="parseFloat(item.harga_satuan) > 300000">
                                                            <span class="text-rose-400 font-bold flex items-center space-x-1">
                                                                <span>⚠️</span>
                                                                <span>Melebihi batas nilai maksimum Ekstrakomtable</span>
                                                            </span>
                                                        </template>
                                                        <template x-if="parseFloat(item.harga_satuan) <= 0">
                                                            <span class="text-amber-400 font-medium flex items-center space-x-1">
                                                                <span>⚠️</span>
                                                                <span>Wajib diisi (&gt; Rp 0)</span>
                                                            </span>
                                                        </template>
                                                        <template x-if="parseFloat(item.harga_satuan) > 0 && parseFloat(item.harga_satuan) <= 300000">
                                                            <span class="text-slate-400">
                                                                <span class="text-slate-300 font-medium" x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span> &times; 
                                                                <span class="text-emerald-400 font-mono font-bold" x-text="'Rp ' + Number(item.harga_satuan || 0).toLocaleString('id-ID')"></span>
                                                            </span>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-slate-400 text-[10px]">Subtotal: </span>
                                                <span class="font-mono font-extrabold text-emerald-300"
                                                      x-text="'Rp ' + Number((item.jumlah_volume || 1) * (item.harga_satuan || 0)).toLocaleString('id-ID')"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Total Ringkasan Ekstrakomptabel / Intrakomptabel -->
                            <div class="p-3 rounded-xl border flex items-center justify-between"
                                 :class="reklasJenis === 'intracom' ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-amber-500/10 border-amber-500/30'">
                                <div>
                                    <div class="text-[10px] uppercase font-bold tracking-wider"
                                         :class="reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-amber-300'"
                                         x-text="reklasJenis === 'intracom' ? 'Total Nilai Intrakomtable:' : 'Total Nilai Ekstrakomtable:'"></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Semula: <span class="line-through text-slate-400 font-mono" x-text="selectedAstapReklas?.jumlah_realisasi || 'Rp 0'"></span>
                                        <span class="font-bold ml-1" :class="reklasJenis === 'intracom' ? 'text-emerald-400' : 'text-amber-400'">➔ Disesuaikan</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-base font-extrabold font-mono"
                                         :class="reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-amber-300'"
                                         x-text="'Rp ' + Number(getReklasExtracomTotal()).toLocaleString('id-ID')"></div>
                                    <div class="text-[10.5px] font-mono"
                                         :class="reklasJenis === 'intracom' ? 'text-emerald-200/80' : 'text-amber-200/80'"
                                         x-text="getReklasExtracomTotalVolume() + ' Total Unit'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Dasar Bukti Belanja / BAST & Tanggal Efektif -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📄 No. Bukti Belanja / BAST / SPK:</label>
                                <input type="text" x-model="reklasNomorBa" placeholder="Kosongkan jika menggunakan bukti transaksi belanja"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📅 Tanggal Efektif Reklas:</label>
                                <input type="date" x-model="reklasTanggal"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>

                        <!-- Keterangan / Alasan Reklasifikasi -->
                        <div>
                            <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📝 Alasan / Penjelasan Reklasifikasi:</label>
                            <textarea x-model="reklasAlasan" rows="2" placeholder="Tuliskan catatan tambahan jika ada..."
                                      class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500 resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Live Preview Narasi Resmi Sheet 3 Keterangan -->
                    <div class="p-3.5 rounded-2xl bg-indigo-950/30 border border-indigo-500/30 space-y-1.5">
                        <div class="flex items-center space-x-1.5 text-indigo-300 font-bold text-xs">
                            <span>📋</span>
                            <span>Pratinjau Narasi Keterangan (Sheet 3 Reklas RSDK):</span>
                        </div>
                        <p class="text-[11px] text-indigo-200/90 italic leading-relaxed"
                           x-text="getReklasNarasiPreview()"></p>
                    </div>

                </div>

                <!-- 3. Modal Footer Actions (Fixed Bottom) -->
                <div class="shrink-0 px-6 py-3.5 border-t border-slate-800/90 bg-slate-950/60 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showReklasModal = false"
                        class="px-4 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="submitReklas()"
                        :disabled="isSubmittingReklas || 
                                   (reklasJenis === 'extracom' && (!reklasExtracomItems.length || reklasExtracomItems.some(i => parseFloat(i.harga_satuan) > 300000 || parseFloat(i.harga_satuan) <= 0))) || 
                                   (reklasJenis === 'intracom' && (!reklasExtracomItems.length || reklasExtracomItems.some(i => parseFloat(i.harga_satuan) <= 300000)))"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2 cursor-pointer">
                        <template x-if="isSubmittingReklas">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isSubmittingReklas ? 'Menyimpan...' : '💾 Simpan Reklasifikasi'"></span>
                    </button>
                </div>

            </div>
        </div>
