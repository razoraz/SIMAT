                <!-- ===================================================================== -->
                <!-- KONDISI A: JIKA MEMILIH ASET TANAH (KIB A) DI LANGKAH 2               -->
                <!-- ===================================================================== -->
                <template x-if="isTanah">
                    <div class="space-y-6">

                        <!-- FORM TANAH ASLI (KIB A) -->

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1) -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-purple-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30">
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
                                            <input type="radio" name="doc_type_radio_a" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
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
                                            <input type="radio" name="doc_type_radio_a" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
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
                                            <input type="radio" name="doc_type_radio_a" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
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
                                            <input type="radio" name="doc_type_radio_a" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
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
                                        <input type="text" x-model="formData.sp2d_nomor" placeholder="0129/SP2D/BLUD/2026" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
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
                                        <input type="text" x-model="formData.bast_dokumen_nomor" placeholder="000.2.3.2/224/..." class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-1.5 text-xs text-white font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-slate-500 text-[9px]">Tanggal BAST</label>
                                        <input type="text" x-datepicker="{ minDate: minDateTriwulan, maxDate: maxDateTriwulan }" x-model="formData.bast_dokumen_tanggal" @change="onDocDateChange(formData.bast_dokumen_tanggal, 'bast_dokumen_tanggal')" placeholder="dd/mm/yyyy" class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-1.5 text-xs text-white">
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- ========================================================================= -->
                        <!-- PEMBUNGKUS BIDANG TANAH MULTI-ITEM (BISA TAMBAH BIDANG TANAH JAMAK)       -->
                        <!-- ========================================================================= -->
                        <div class="space-y-4">
                            
                            <!-- Header Pembungkus Bidang Tanah -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 text-sm">🌾</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            RINCIAN BIDANG TANAH (<span class="text-emerald-400" x-text="formData.tanah_items.length"></span> Bidang Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap bidang tanah memiliki rincian sertifikat, luas, kondisi, nilai perolehan, dan alamat lokasi fisik masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addTanahItem()" 
                                        class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-emerald-500/20 shrink-0 cursor-pointer">
                                    <span>➕ Tambah Bidang Tanah</span>
                                </button>
                            </div>

                            <!-- List Kartu Bidang Tanah (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.tanah_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 hover:border-emerald-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap Bidang Tanah -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-mono font-extrabold text-xs border border-emerald-500/40 flex items-center space-x-1.5">
                                                    <span>🌾 Bidang Tanah #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Luas: <strong class="text-cyan-300" x-text="(item.tanah_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getTanahSubtotal(item))"></strong>
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus Bidang (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.tanah_items.length > 1" 
                                                    @click="removeTanahItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Bidang Ini</span>
                                            </button>
                                        </div>

                                        <!-- Grid Status Sertifikat & Kondisi/Luas -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- Status Tanah & Sertifikat (Tanpa Penomoran 3) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📜 Status Tanah & Sertifikat:</span>
                                                    </span>
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[11px] mb-1 font-semibold">Hak Tanah</label>
                                                    <select x-model="item.tanah_hak" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                        <option value="Hak Pakai">Hak Pakai</option>
                                                        <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                                    </select>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Nomor</label>
                                                        <input type="text" x-model="item.tanah_sertifikat_no" placeholder="HP-108/1984"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Tanggal</label>
                                                        <input type="text" x-datepicker="{ maxDate: maxDateToday }" x-model="item.tanah_sertifikat_tgl" placeholder="dd/mm/yyyy"
                                                               @change="if(item.tanah_sertifikat_tgl > maxDateToday) item.tanah_sertifikat_tgl = maxDateToday"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-amber-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kondisi, Penggunaan & Volume (Tanpa Penomoran 4) -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📐 Kondisi, Penggunaan & Volume:</span>
                                                    </span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Kondisi (B/KB/RB)</label>
                                                        <select x-model="item.tanah_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                                            <option value="Baik">Baik (B)</option>
                                                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Jumlah Bidang</label>
                                                        <input type="number" min="1" x-model.number="item.tanah_jumlah_bidang" 
                                                               @input="if (item.tanah_jumlah_bidang < 1) item.tanah_jumlah_bidang = 1;"
                                                               placeholder="1"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-cyan-500">
                                                    </div>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Luas Tanah (m²)</label>
                                                        <input type="number" min="0" step="any" x-model.number="item.tanah_luas_m2" 
                                                               @input="if (item.tanah_luas_m2 < 0) item.tanah_luas_m2 = 0;"
                                                               placeholder="35400"
                                                               class="w-full bg-slate-950 border border-cyan-500/40 rounded-xl px-2.5 py-2 text-xs text-cyan-300 font-mono font-bold focus:border-cyan-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1">Penggunaan Lahan</label>
                                                        <input type="text" x-model="item.tanah_penggunaan" placeholder="Fasilitas RSUD"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                                    </div>
                                                </div>
                                            </div>

                                        </div>

                                        <!-- Nilai Barang (Rp) (Tanpa Penomoran 5) -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                            <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>💰 Nilai Barang (Rp):</span>
                                                </span>
                                                <span class="text-[10px] text-slate-400">Rincian Komponen Nilai Tanah</span>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Nilai Perencanaan (Rp)</label>
                                                    <input type="number" x-model.number="item.tanah_nilai_perencanaan" placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Nilai Fisik (Rp)</label>
                                                    <input type="number" x-model.number="item.tanah_nilai_fisik" placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1">Nilai Pengawasan (Rp)</label>
                                                    <input type="number" x-model.number="item.tanah_nilai_pengawasan" placeholder="0"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-emerald-500">
                                                </div>
                                            </div>
                                            <!-- Subtotal Kartu Bidang Tanah Ini -->
                                            <div :class="getTanahSubtotal(item) <= 0 ? 'border-amber-500/40 bg-amber-950/10' : 'border-emerald-500/30 bg-slate-950'" 
                                                 class="p-2.5 rounded-xl border flex flex-col gap-1 text-xs transition-colors shadow-inner">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-slate-400 font-medium text-[11px]">Subtotal Nilai Bidang Tanah #<span x-text="idx + 1"></span>:</span>
                                                    <span :class="getTanahSubtotal(item) <= 0 ? 'text-amber-400' : 'text-emerald-400'" 
                                                          class="font-extrabold font-mono text-sm" x-text="'Rp ' + formatRupiah(getTanahSubtotal(item))"></span>
                                                </div>
                                                <span x-show="getTanahSubtotal(item) <= 0" class="text-[10px] font-bold text-amber-400">
                                                    ⚠️ Wajib diisi (nilai perolehan tidak boleh Rp 0)
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Letak / Alamat Barang (Tanpa Penomoran) -->
                                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-2">
                                            <div class="flex items-center justify-between border-b border-amber-500/20 pb-1.5">
                                                <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>📍 Letak / Alamat Tanah & Aset:</span>
                                                </label>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Fisik Bidang #<span x-text="idx + 1"></span></span>
                                            </div>
                                            <input type="text" x-model="item.tanah_alamat" placeholder="Contoh: Jl. Piere Tendean No. 3, Kel. Badean, Kec. Bondowoso (Area Paviliun RSUD Dr. H. Koesnandi)"
                                                   class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-medium focus:outline-none focus:border-amber-500 transition-all">
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah Bidang Tanah Baru (Besar & Jelas) -->
                            <button type="button" @click="addTanahItem()" 
                                    class="w-full py-3.5 border-2 border-dashed border-emerald-500/50 hover:border-emerald-400 bg-emerald-950/20 hover:bg-emerald-950/40 text-emerald-300 hover:text-emerald-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Bidang Tanah Lainnya</span>
                            </button>

                            <!-- Ringkasan Anggaran vs Realisasi Keseluruhan Tanah -->
                            <div class="p-4 rounded-2xl bg-slate-950/90 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-lg">
                                <div class="flex flex-wrap items-center gap-4 sm:gap-6">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Pagu Anggaran (Langkah 2):</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Total Realisasi Semua Pengadaan:</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_realisasi)"></span>
                                    </div>
                                </div>
                                <div class="text-left sm:text-right border-t sm:border-t-0 border-slate-800 pt-2 sm:pt-0">
                                    <span class="text-[10px] text-cyan-400 font-semibold block uppercase tracking-wider">Total Nilai Semua Bidang Tanah Ini:</span>
                                    <span class="text-base font-extrabold text-cyan-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiTanah)"></span>
                                </div>
                            </div>

                        </div>

                        <!-- ============================================================= -->
                        <!-- LIVE PREVIEW TABEL EXCEL SESUAI GAMBAR USER (KHUSUS TANAH)    -->
                        <!-- ============================================================= -->
                        <div class="space-y-2 pt-2">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Rincian Belanja Modal Tanah (Sesuai SPK / SP / Kwitansi / Invoice):</span>
                                </span>
                                <span class="text-[10px] text-emerald-400 font-mono" x-text="formData.tanah_items.length + ' Baris Bidang Terdaftar'">Format Excel KIB A RSUD (26 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1300px]">
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="25" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Letak / Alamat<br>Barang
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Status Tanah</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-16 align-middle bg-[#fde9d9]">Kondisi<br><span class="font-normal text-[9px]">(B,KB,RB)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-32 align-middle bg-[#fde9d9]">Penggunaan</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th colspan="3" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Nilai Barang (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">Hak Tanah<br><span class="font-normal text-[8.5px]">(Hak Pakai / Hak Pengelolaan)</span></th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Sertifikat</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice (Tanggal dan Nomor)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah Bidang Tanah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Luas Tanah (m²)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Perencanaan (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Fisik (Rp)</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nilai Pengawasan</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">NOMOR</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle">TANGGAL</th>
                                        </tr>
                                        <!-- Header Tingkat 3 -->
                                        <tr class="bg-[#fde9d9] text-slate-900 font-semibold border-b border-slate-600 text-[9px]">
                                            <th class="px-1.5 py-0.5 border border-slate-500">Tanggal</th>
                                            <th class="px-1.5 py-0.5 border border-slate-500">Nomor</th>
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
                                    <!-- Body Data Live Sesuai Input Multi-Item User -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(tItem, tIdx) in formData.tanah_items" :key="tIdx">
                                            <tr>
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="formData.tanah_nama_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="formData.tanah_kode_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="tItem.tanah_hak"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(tItem.tanah_sertifikat_tgl)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="tItem.tanah_sertifikat_no"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-bold" x-text="(tItem.tanah_kondisi === 'Baik' || tItem.tanah_kondisi === 'B') ? 'Baik' : ((tItem.tanah_kondisi === 'Kurang Baik' || tItem.tanah_kondisi === 'KB') ? 'Kurang Baik' : 'Rusak Berat')"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="tItem.tanah_penggunaan"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="tItem.tanah_jumlah_bidang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right" x-text="formatRupiah(tItem.tanah_luas_m2)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(tItem.tanah_nilai_perencanaan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(tItem.tanah_nilai_fisik)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(tItem.tanah_nilai_pengawasan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-emerald-800" x-text="formatRupiah(getTanahSubtotal(tItem))"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="tItem.tanah_alamat || formData.alamat_barang"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>
