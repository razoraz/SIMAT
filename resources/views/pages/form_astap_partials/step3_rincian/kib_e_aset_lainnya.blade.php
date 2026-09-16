                <!-- ===================================================================== -->
                <!-- KONDISI E: JIKA MEMILIH ASET TETAP LAINNYA (KIB E / 1.3.5) DI LANGKAH 2-->
                <!-- ===================================================================== -->
                <template x-if="isAsetLainnya">
                    <div class="space-y-6">

                        <!-- MODE EKSTRAKOMTABEL (EXTRACOM) -->
                        <div x-show="formData.is_extracomtable" class="space-y-6">
                            <!-- INFO BADGE: Mode Extracom Aktif -->
                            <div class="flex items-center justify-between px-4 py-3 rounded-2xl bg-cyan-950/30 border border-cyan-500/40">
                                <div class="flex items-center space-x-2.5">
                                    <span class="text-lg">📦</span>
                                    <div>
                                        <p class="text-xs font-black text-cyan-300">Mode: Barang Ekstrakomtabel (Extracom) — KIB E Aset Tetap Lainnya</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Harga satuan maks. Rp 300.000 · Dicatat di Sheet Ekstrakomtabel</p>
                                    </div>
                                </div>
                                <a @click.prevent="currentStep = 2" href="#" class="shrink-0 text-[10px] font-bold px-3 py-1.5 rounded-xl transition-all border cursor-pointer text-cyan-300 border-cyan-500/40 hover:bg-cyan-500/10">
                                    ← Ubah di Langkah 2
                                </a>
                            </div>

                            @include('pages.form_astap_partials.step3_rincian.extracom_dokumen')
                            @include('pages.form_astap_partials.step3_rincian.extracom_form')
                        </div>

                        <!-- MODE REGULER: FORM KIB E ASLI -->
                        <div x-show="!formData.is_extracomtable" class="space-y-6">

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-orange-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-orange-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-orange-500/20 text-orange-300 font-mono font-bold border border-orange-500/30">
                                        📅 Periode: <span x-text="triwulanDateLabel"></span>
                                    </span>
                                </div>
                                <span class="text-[10px] text-slate-400 font-medium">Klik pada kartu atau radio button untuk memilih jenis dokumen</span>
                            </div>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                                <!-- 1. SPK -->
                                <div @click="selectDocType('spk')" 
                                     :class="formData.doc_type === 'spk' ? 'border-cyan-500 bg-cyan-950/30 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_e" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-[11px] font-extrabold text-cyan-400">📄 SPK (Kontrak)</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'spk'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor SPK</label>
                                        <input type="text" x-model="formData.spk_nomor" placeholder="028/SPK-KTR/V/2026" 
                                               :disabled="formData.doc_type !== 'spk'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-cyan-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal SPK</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.spk_tanggal" @change="onDocDateChange(formData.spk_tanggal, 'spk_tanggal')" 
                                               :disabled="formData.doc_type !== 'spk'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 2. Surat Pesanan -->
                                <div @click="selectDocType('surat_pesanan')" 
                                     :class="formData.doc_type === 'surat_pesanan' ? 'border-purple-500 bg-purple-950/30 ring-1 ring-purple-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_e" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-[11px] font-extrabold text-purple-400">📦 Surat Pesanan</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'surat_pesanan'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Surat Pesanan</label>
                                        <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="028/SP-RSUD/V/2026" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-purple-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Surat Pesanan</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.surat_pesanan_tanggal" @change="onDocDateChange(formData.surat_pesanan_tanggal, 'surat_pesanan_tanggal')" 
                                               :disabled="formData.doc_type !== 'surat_pesanan'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 3. Kwitansi -->
                                <div @click="selectDocType('kwitansi')" 
                                     :class="formData.doc_type === 'kwitansi' ? 'border-amber-500 bg-amber-950/30 ring-1 ring-amber-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_e" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-[11px] font-extrabold text-amber-400">🧾 Kwitansi</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'kwitansi'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Kwitansi</label>
                                        <input type="text" x-model="formData.kwitansi_nomor" placeholder="KW-028/KTR/2026" 
                                               :disabled="formData.doc_type !== 'kwitansi'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Kwitansi</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.kwitansi_tanggal" @change="onDocDateChange(formData.kwitansi_tanggal, 'kwitansi_tanggal')" 
                                               :disabled="formData.doc_type !== 'kwitansi'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>

                                <!-- 4. Invoice -->
                                <div @click="selectDocType('faktur')" 
                                     :class="formData.doc_type === 'faktur' ? 'border-emerald-500 bg-emerald-950/30 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-3.5 rounded-xl border transition-all cursor-pointer space-y-2.5 relative">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-1.5">
                                            <input type="radio" name="doc_type_radio_e" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-[11px] font-extrabold text-emerald-400">📑 Invoice / Faktur</span>
                                        </div>
                                        <span x-show="formData.doc_type === 'faktur'" class="text-[9px] px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Nomor Invoice</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028" 
                                               :disabled="formData.doc_type !== 'faktur'"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-emerald-300 font-mono disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[9px] mb-0.5 font-semibold">Tanggal Invoice</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.faktur_tanggal" @change="onDocDateChange(formData.faktur_tanggal, 'faktur_tanggal')" 
                                               :disabled="formData.doc_type !== 'faktur'" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white disabled:opacity-50 disabled:cursor-not-allowed">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. DOKUMEN SP2D & BAST -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-amber-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">2. DOKUMEN SP2D & BAST:</span>
                                <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 font-mono font-bold border border-amber-500/30">
                                    📅 Periode: <span x-text="triwulanDateLabel"></span>
                                </span>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">SP2D</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor SP2D</label>
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="Contoh: 0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal SP2D</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.sp2d_tanggal" @change="onDocDateChange(formData.sp2d_tanggal, 'sp2d_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <span class="text-[10px] font-bold text-slate-300 block">BAST</span>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Nomor BAST</label>
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="Contoh: 000.2.3.2/224/RSUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.bast_dokumen_tanggal" @change="onDocDateChange(formData.bast_dokumen_tanggal, 'bast_dokumen_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. PILIH KATEGORI ASET TETAP LAINNYA -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-orange-500/40 space-y-3 shadow-lg">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-orange-400 block uppercase tracking-wider">3. PILIH KATEGORI ASET TETAP LAINNYA:</span>
                                    <span class="text-[10px] text-slate-400 font-medium">Kategori ini berlaku sebagai default untuk setiap item baru</span>
                                </div>
                                <!-- Badge kategori terpilih -->
                                <span class="text-[10px] px-2.5 py-1 rounded-full font-bold border"
                                      :class="{
                                          'bg-amber-500/20 text-amber-300 border-amber-500/40': (formData.kib_e_default_type || 'buku') === 'buku',
                                          'bg-purple-500/20 text-purple-300 border-purple-500/40': formData.kib_e_default_type === 'kesenian',
                                          'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': formData.kib_e_default_type === 'hewan_tumbuhan'
                                      }"
                                      x-text="formData.kib_e_default_type === 'kesenian' ? '🎨 Kesenian & Budaya Terpilih' : (formData.kib_e_default_type === 'hewan_tumbuhan' ? '🌿 Hewan & Tumbuhan Terpilih' : '📚 Buku Perpustakaan Terpilih')">
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                                <!-- Opsi 1: Buku Perpustakaan -->
                                <div @click="formData.kib_e_default_type = 'buku'; syncLainnyaFieldsToMain();"
                                     :class="(formData.kib_e_default_type || 'buku') === 'buku' ? 'border-amber-500 bg-amber-950/40 ring-1 ring-amber-500 shadow-lg shadow-amber-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-amber-500/50'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5">
                                            <input type="radio" name="kib_e_default_type_radio" value="buku"
                                                   :checked="(formData.kib_e_default_type || 'buku') === 'buku'"
                                                   @change="formData.kib_e_default_type = 'buku'; syncLainnyaFieldsToMain();"
                                                   class="text-amber-500 focus:ring-amber-500">
                                            <span class="text-sm font-extrabold text-amber-400">📚 Buku Perpustakaan</span>
                                        </div>
                                        <span x-show="(formData.kib_e_default_type || 'buku') === 'buku'"
                                              class="text-[9px] px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 leading-relaxed">Buku, jurnal ilmiah kedokteran, literatur medis, dan arsip pustaka rumah sakit. Kolom: Judul, Pencipta, Spesifikasi.</p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-950/60 text-amber-400 border border-amber-700/30">📖 Judul Buku</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-950/60 text-amber-400 border border-amber-700/30">✍️ Pencipta</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-950/60 text-amber-400 border border-amber-700/30">📋 Spesifikasi</span>
                                    </div>
                                </div>

                                <!-- Opsi 2: Kesenian & Kebudayaan -->
                                <div @click="formData.kib_e_default_type = 'kesenian'; syncLainnyaFieldsToMain();"
                                     :class="formData.kib_e_default_type === 'kesenian' ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500 shadow-lg shadow-purple-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-purple-500/50'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5">
                                            <input type="radio" name="kib_e_default_type_radio" value="kesenian"
                                                   :checked="formData.kib_e_default_type === 'kesenian'"
                                                   @change="formData.kib_e_default_type = 'kesenian'; syncLainnyaFieldsToMain();"
                                                   class="text-purple-500 focus:ring-purple-500">
                                            <span class="text-sm font-extrabold text-purple-400">🎨 Kesenian & Kebudayaan</span>
                                        </div>
                                        <span x-show="formData.kib_e_default_type === 'kesenian'"
                                              class="text-[9px] px-2 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 leading-relaxed">Lukisan, patung, ornamen dekoratif, dan benda seni atau budaya bersejarah milik rumah sakit. Kolom: Asal Daerah, Pencipta, Spesifikasi, Bahan, Ukuran.</p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">🗺️ Asal Daerah</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">🎭 Pencipta</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">🧱 Bahan</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-purple-950/60 text-purple-400 border border-purple-700/30">📐 Ukuran</span>
                                    </div>
                                </div>

                                <!-- Opsi 3: Hewan & Tumbuhan -->
                                <div @click="formData.kib_e_default_type = 'hewan_tumbuhan'; syncLainnyaFieldsToMain();"
                                     :class="formData.kib_e_default_type === 'hewan_tumbuhan' ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500 shadow-lg shadow-emerald-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-emerald-500/50'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2.5">
                                            <input type="radio" name="kib_e_default_type_radio" value="hewan_tumbuhan"
                                                   :checked="formData.kib_e_default_type === 'hewan_tumbuhan'"
                                                   @change="formData.kib_e_default_type = 'hewan_tumbuhan'; syncLainnyaFieldsToMain();"
                                                   class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-sm font-extrabold text-emerald-400">🌿 Hewan & Tumbuhan</span>
                                        </div>
                                        <span x-show="formData.kib_e_default_type === 'hewan_tumbuhan'"
                                              class="text-[9px] px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-400 leading-relaxed">Tanaman taman, pohon peneduh, hewan ternak, dan hewan peliharaan yang merupakan aset tetap milik rumah sakit. Kolom: Judul/Jenis, Spesifikasi.</p>
                                    <div class="flex flex-wrap gap-1 mt-1">
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-950/60 text-emerald-400 border border-emerald-700/30">🐄 Jenis Hewan</span>
                                        <span class="text-[9px] px-1.5 py-0.5 rounded bg-emerald-950/60 text-emerald-400 border border-emerald-700/30">🌳 Spesifikasi</span>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- PEMBUNGKUS ASET TETAP LAINNYA MULTI-ITEM REPEATER (MODEL PERSIS KIB B)     -->
                        <!-- ========================================================================= -->
                        <div class="space-y-4">
                            
                            <!-- Header Pembungkus Aset Tetap Lainnya Multi-Item -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-orange-950/30 border border-orange-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-orange-500/20 text-orange-400 text-sm">📚</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            RINCIAN ASET TETAP LAINNYA
                                            (<span class="text-orange-400" x-text="formData.lainnya_items.length"></span> Item Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap item aset tetap lainnya memiliki kategori (Buku Perpustakaan, Kesenian/Kebudayaan, Hewan/Tumbuhan), spesifikasi, volume, nilai satuan, dan ruang/pemegang penempatan masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addLainnyaItem()" 
                                        class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-orange-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Item Aset Tetap Lainnya Baru</span>
                                </button>
                            </div>

                            <!-- List Kartu Item Aset Tetap Lainnya (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.lainnya_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-orange-500/30 hover:border-orange-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap Item -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-orange-500/20 text-orange-300 font-mono font-extrabold text-xs border border-orange-500/40 flex items-center space-x-1.5">
                                                    <span>📚 Item #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold"
                                                      :class="{
                                                          'bg-amber-500/20 text-amber-300 border border-amber-500/40': (item.kib_e_sub_type || 'buku') === 'buku',
                                                          'bg-purple-500/20 text-purple-300 border border-purple-500/40': item.kib_e_sub_type === 'kesenian',
                                                          'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40': item.kib_e_sub_type === 'hewan_tumbuhan'
                                                      }"
                                                      x-text="(item.kib_e_sub_type === 'kesenian' ? '🎨 Kesenian & Budaya' : (item.kib_e_sub_type === 'hewan_tumbuhan' ? '🌿 Hewan & Tumbuhan' : '📚 Buku Perpustakaan'))">
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.lainnya_buku_judul || item.lainnya_kesenian_asal || item.lainnya_hewan_jenis">
                                                    • <span x-text="item.kib_e_sub_type === 'buku' ? item.lainnya_buku_judul : (item.kib_e_sub_type === 'kesenian' ? item.lainnya_kesenian_asal : (item.lainnya_hewan_jenis || item.lainnya_hewan_judul))"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Qty: <strong class="text-cyan-300" x-text="(item.lainnya_jumlah_barang || 1) + ' ' + (item.lainnya_satuan || 'Eksemplar')"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(item))"></strong>
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus Item (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.lainnya_items.length > 1" 
                                                    @click="removeLainnyaItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Item Ini</span>
                                            </button>
                                        </div>

                                        <!-- Sync kategori dari Section No. 3 global ke tiap item -->
                                        <template x-effect="item.kib_e_sub_type = formData.kib_e_default_type || 'buku'"></template>

                                        <!-- Grid Form Pengisian Spesifikasi Aset Tetap Lainnya -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- Sisi Kiri: Nama & Identitas Kategori -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span x-text="(item.kib_e_sub_type === 'kesenian' ? '🎨 Identitas Karya Seni' : (item.kib_e_sub_type === 'hewan_tumbuhan' ? '🌿 Identitas Hewan & Tumbuhan' : '📚 Identitas Buku Perpustakaan'))"></span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nama Barang (PMDN 108)</span>
                                                        <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
                                                            <span>🔒</span>
                                                            <span>Otomatis dari Langkah 2</span>
                                                        </span>
                                                    </label>
                                                    <input type="text" 
                                                           :value="item.lainnya_nama_barang || formData.lainnya_nama_barang || formData.sub_rincian_nama || 'Aset Tetap Lainnya'"
                                                           readonly
                                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                </div>

                                                <!-- Form Spesifik: Buku -->
                                                <template x-if="(item.kib_e_sub_type || 'buku') === 'buku'">
                                                    <div class="space-y-2.5">
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul Buku / Literatur Medis</label>
                                                            <input type="text" x-model="item.lainnya_buku_judul" placeholder="Pedoman Standar Pelayanan Klinis Kedokteran 2026"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Penulis / Penerbit</label>
                                                            <input type="text" x-model="item.lainnya_buku_pencipta" placeholder="Komite Medik & Tim Farmasi RSUD"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Form Spesifik: Kesenian -->
                                                <template x-if="item.kib_e_sub_type === 'kesenian'">
                                                    <div class="space-y-2.5">
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Asal Daerah / Wilayah Budaya</label>
                                                            <input type="text" x-model="item.lainnya_kesenian_asal" placeholder="Jawa Timur / Bondowoso"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-purple-500">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Seniman</label>
                                                            <input type="text" x-model="item.lainnya_kesenian_pencipta" placeholder="Sanggar Seni Budaya Bondowoso"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Form Spesifik: Hewan & Tumbuhan -->
                                                <template x-if="item.kib_e_sub_type === 'hewan_tumbuhan'">
                                                    <div class="space-y-2.5">
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul / Jenis Hewan & Tumbuhan</label>
                                                            <input type="text" x-model="item.lainnya_hewan_jenis" @input="item.lainnya_hewan_judul = $event.target.value" placeholder="Pohon Tabebuya Emas / Tanaman Lanskap Taman"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-emerald-500">
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Sisi Kanan: Spesifikasi Fisik, Bahan, Ukuran & Kondisi -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>🔍 Spesifikasi Fisik & Kondisi:</span>
                                                    </span>
                                                </div>

                                                <!-- Spesifikasi Buku -->
                                                <template x-if="(item.kib_e_sub_type || 'buku') === 'buku'">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Spesifikasi Buku / Literatur</label>
                                                        <input type="text" x-model="item.lainnya_buku_spesifikasi" placeholder="Edisi Revisi 2026 / Hardcover Lux / 850 Halaman"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 focus:border-amber-500">
                                                    </div>
                                                </template>

                                                <!-- Spesifikasi Kesenian -->
                                                <template x-if="item.kib_e_sub_type === 'kesenian'">
                                                    <div class="space-y-2.5">
                                                        <div>
                                                            <label class="block text-slate-400 text-[10px] mb-1 font-medium">Spesifikasi Karya Seni</label>
                                                            <input type="text" x-model="item.lainnya_kesenian_spesifikasi" placeholder="Lukisan Panorama Rumah Sakit / Patung Lambang Daerah"
                                                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-purple-300 focus:border-purple-500">
                                                        </div>
                                                        <div class="grid grid-cols-2 gap-2">
                                                            <div>
                                                                <label class="block text-slate-400 text-[10px] mb-1 font-medium">Bahan Seni</label>
                                                                <input type="text" x-model="item.lainnya_kesenian_bahan" placeholder="Kanvas & Kayu Jati"
                                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-purple-500">
                                                            </div>
                                                            <div>
                                                                <label class="block text-slate-400 text-[10px] mb-1 font-medium">Ukuran (m/cm)</label>
                                                                <input type="text" x-model="item.lainnya_kesenian_ukuran" placeholder="200 x 120 cm"
                                                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-purple-500">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Spesifikasi Hewan & Tumbuhan -->
                                                <template x-if="item.kib_e_sub_type === 'hewan_tumbuhan'">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-medium">Spesifikasi Tanaman / Hewan</label>
                                                        <input type="text" x-model="item.lainnya_hewan_spesifikasi" placeholder="Bibit Unggul Tinggi 3.5 Meter Siap Tanam"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-300 focus:border-emerald-500">
                                                    </div>
                                                </template>

                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Barang</label>
                                                    <select x-model="item.lainnya_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                                        <option value="Baik">Baik (B)</option>
                                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                    </select>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Volume & Nilai Satuan Barang -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2.5">
                                            <div class="flex items-center justify-between border-b border-emerald-500/20 pb-1.5">
                                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>💰 Volume & Nilai Satuan Barang (Rp):</span>
                                                </span>
                                                <div class="flex items-center space-x-1.5 bg-emerald-950/60 border border-emerald-500/30 px-2.5 py-0.5 rounded-lg">
                                                    <span class="text-[10px] text-slate-300 font-semibold">Sub Total Item #<span x-text="idx + 1"></span>:</span>
                                                    <span class="text-xs font-black text-emerald-400 font-mono" x-text="'Rp ' + Number(getLainnyaSubtotal(item)).toLocaleString('id-ID')"></span>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2.5">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah (Volume)</label>
                                                    <input type="number" min="1" x-model.number="item.lainnya_jumlah_barang" placeholder="1"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono font-bold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Satuan</label>
                                                    <input type="text" x-model="item.lainnya_satuan" placeholder="Eksemplar / Buah / Batang / Unit"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nilai Satuan (Rp)</span>
                                                        <span class="text-[9px] font-bold text-emerald-400">Wajib > Rp 300.000</span>
                                                    </label>
                                                    <input type="text" 
                                                           :value="item.lainnya_nilai_satuan ? Number(item.lainnya_nilai_satuan).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.lainnya_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           :class="Number(item.lainnya_nilai_satuan || 0) > 0 && Number(item.lainnya_nilai_satuan || 0) <= 300000 ? 'border-rose-500 text-rose-300 focus:border-rose-400 ring-1 ring-rose-500' : 'border-slate-700 text-emerald-300 focus:border-emerald-500'"
                                                           placeholder="450.000"
                                                           class="w-full bg-slate-950 border rounded-xl px-3 py-2 text-xs font-mono font-bold focus:outline-none">
                                                    <span x-show="Number(item.lainnya_nilai_satuan || 0) > 0 && Number(item.lainnya_nilai_satuan || 0) <= 300000" class="text-[9px] font-bold text-rose-400 block mt-1">
                                                        ⚠️ Aset Reguler: Nilai satuan harus > Rp 300.000.
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Admin Proyek (Rp)</label>
                                                    <input type="text" 
                                                           :value="item.lainnya_administrasi_proyek ? Number(item.lainnya_administrasi_proyek).toLocaleString('id-ID') : ''"
                                                           @input="
                                                               let raw = $event.target.value.replace(/\D/g, '');
                                                               item.lainnya_administrasi_proyek = raw ? parseInt(raw, 10) : 0;
                                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                                           "
                                                           placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-emerald-500">
                                                </div>
                                                <div class="col-span-2 sm:col-span-1">
                                                    <label class="block text-emerald-400 text-[10px] mb-1 font-bold">Sub Total (Rp)</label>
                                                    <div class="w-full bg-slate-950/90 border border-emerald-500/50 rounded-xl px-3 py-2 text-xs text-emerald-400 font-mono font-black flex items-center justify-between shadow-inner">
                                                        <span class="text-emerald-500 text-[10px]">Rp</span>
                                                        <span x-text="Number(getLainnyaSubtotal(item)).toLocaleString('id-ID')"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ruang / Pemegang Aset -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/40 space-y-2 relative" @click.away="item.isRuangOpen = false">
                                            <div class="flex items-center justify-between border-b border-amber-500/30 pb-1.5">
                                                <label class="block text-amber-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📍 Ruang / Unit Pemegang (Penanggung Jawab & Lokasi):</span>
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                                        <span>🏥</span>
                                                        <span>Unit & Paviliun</span>
                                                    </span>
                                                    <button type="button" 
                                                            x-show="item.ruang_pemegang || item.ruang_pemegang_lainnya" 
                                                            @click="item.ruang_pemegang = ''; item.ruang_pemegang_lainnya = ''; item.searchRuang = ''; item.isRuangOpen = true; syncLainnyaFieldsToMain();" 
                                                            class="text-[10px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                                        ✕ Reset
                                                    </button>
                                                </div>
                                            </div>
                                            
                                            <div class="relative">
                                                <input type="text" 
                                                       :value="!item.isRuangOpen ? (item.ruang_pemegang || item.ruang_pemegang_lainnya) : item.searchRuang"
                                                       @input="item.ruang_pemegang = $event.target.value; item.ruang_pemegang_lainnya = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true; syncLainnyaFieldsToMain();"
                                                       @focus="item.isRuangOpen = true"
                                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                                       class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 focus:border-amber-500 rounded-xl px-3.5 py-2.5 pl-9 text-xs text-white font-semibold focus:outline-none transition-all">
                                                <svg class="w-3.5 h-3.5 text-amber-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>

                                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                                            <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height: 180px;" class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                                <div class="px-2.5 py-1 bg-slate-950/80 rounded-lg text-[9.5px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between">
                                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                                    <span class="text-slate-400 font-mono text-[9px]" x-text="filterUnitsForLainnyaItem(item).length + ' Unit/Ruangan'"></span>
                                                </div>
                                                <template x-for="u in filterUnitsForLainnyaItem(item)" :key="u.id">
                                                    <div @click="selectUnitForLainnyaItem(item, u)" class="p-2 rounded-xl bg-slate-950/50 hover:bg-amber-500/15 border border-slate-800/60 hover:border-amber-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                                        <div class="min-w-0 pr-2">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-xs font-bold text-white group-hover:text-amber-300 truncate" x-text="u.nama"></span>
                                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                                            </div>
                                                            <p class="text-[9.5px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                                        </div>
                                                        <span class="px-2 py-0.5 rounded-lg bg-slate-900 text-amber-300 border border-amber-500/30 text-[9.5px] font-bold shrink-0">Pilih →</span>
                                                    </div>
                                                </template>
                                                <template x-if="filterUnitsForLainnyaItem(item).length === 0">
                                                    <div class="p-2.5 text-center text-xs text-slate-400">
                                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Item Aset Tetap Lainnya Baru (Besar & Jelas) -->
                            <button type="button" @click="addLainnyaItem()" 
                                    class="w-full py-3.5 border-2 border-dashed border-orange-500/50 hover:border-orange-400 bg-orange-950/20 hover:bg-orange-950/40 text-orange-300 hover:text-orange-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Item Aset Tetap Lainnya Baru</span>
                            </button>

                            <!-- Ringkasan Anggaran & Akumulasi Realisasi KIB E -->
                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-orange-500/40 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Jumlah Anggaran (Pagu):</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-orange-400 font-semibold block uppercase tracking-wider">📦 Total Volume / Item:</span>
                                        <span class="text-sm font-black text-orange-300 font-mono" x-text="totalVolumeAsetLainnya + ' Item/Barang'"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Jumlah Realisasi (Akumulasi):</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAsetLainnya)"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                                    <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">Total Nilai Realisasi Pengadaan:</span>
                                    <span class="text-base font-extrabold text-cyan-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAsetLainnya)"></span>
                                </div>
                            </div>

                        </div>

                        <!-- ========================================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL RESMI KIB E (30 KOLOM) DENGAN LOOP MULTI-ITEM   -->
                        <!-- ========================================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Aset Tetap Lainnya (Format Excel Resmi KIB E RSUD):</span>
                                </span>
                                <span class="text-[10px] text-orange-400 font-mono" x-text="formData.lainnya_items.length + ' Baris Item Terdaftar (KIB E 30 Kolom)'">Format Excel Resmi KIB E (30 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1650px]">
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="29" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                RUANG /<br>PEMEGANG
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BUKU PERPUSTAKAAN</th>
                                            <th colspan="5" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Barang Bercorak Kesenian / Kebudayaan</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Hewan Ternak / Tumbuhan</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">ADMINISTRASI PROYEK (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Judul</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Pencipta</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Spesifikasi</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Asal Daerah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Pencipta</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Spesifikasi</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Bahan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Ukuran<br>(m/cm)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Judul</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Spesifikasi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                        </tr>
                                    </thead>
                                    <!-- Body Data Live Sesuai Input User (Looping lainnya_items) -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(lItem, lIdx) in formData.lainnya_items" :key="lIdx">
                                            <tr class="hover:bg-slate-50 transition-colors">
                                                <!-- 1. NAMA BARANG -->
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="lItem.lainnya_nama_barang || formData.lainnya_nama_barang || formData.sub_rincian_nama"></td>
                                                <!-- 2. KODE BARANG -->
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="lItem.lainnya_kode_barang || formData.lainnya_kode_barang || formData.sub_rincian_kode"></td>
                                                <!-- 3-5. BUKU PERPUSTAKAAN -->
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_buku_judul || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_buku_pencipta || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_buku_spesifikasi || '-'"></td>
                                                <!-- 6-10. KESENIAN / KEBUDAYAAN -->
                                                <td class="px-2 py-2 border border-slate-400" x-text="lItem.lainnya_kesenian_asal || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_kesenian_pencipta || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_kesenian_spesifikasi || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400" x-text="lItem.lainnya_kesenian_bahan || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono" x-text="lItem.lainnya_kesenian_ukuran || '-'"></td>
                                                <!-- 11-12. HEWAN / TUMBUHAN -->
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_hewan_judul || lItem.lainnya_hewan_jenis || '-'"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="lItem.lainnya_hewan_spesifikasi || '-'"></td>
                                                <!-- 13-20. RIWAYAT PEMBELIAN -->
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <!-- 21-23. VOLUME & NILAI SATUAN -->
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="lItem.lainnya_jumlah_barang || 1"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="lItem.lainnya_satuan || 'Eksemplar'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(lItem.lainnya_nilai_satuan || 0)"></td>
                                                <!-- 24-25. ADMIN PROYEK & TOTAL NILAI -->
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(lItem.lainnya_administrasi_proyek || 0)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-orange-800" x-text="formatRupiah(getLainnyaSubtotal(lItem))"></td>
                                                <!-- 26-27. SP2D -->
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <!-- 28-29. BAST -->
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor || '-'"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <!-- 30. RUANG / PEMEGANG -->
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="lItem.ruang_pemegang || lItem.ruang_pemegang_lainnya || formData.ruang_pemegang || formData.ruang_pemegang_lainnya || '-'"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        </div><!-- /MODE REGULER -->

                    </div>
                </template>
