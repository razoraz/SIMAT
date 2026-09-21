                <template x-if="isAtb">
                    <div class="space-y-6">

                        <!-- FORM ATB ASLI -->
                        <div class="space-y-6">

                        <!-- 1. DOKUMEN PEMBELIAN & DOKUMEN SP2D / BAST (TARUH PALING ATAS - NO 1 & 2) -->
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
                                            <input type="radio" name="doc_type_radio_atb" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
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
                                            <input type="radio" name="doc_type_radio_atb" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
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
                                            <input type="radio" name="doc_type_radio_atb" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
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
                                            <input type="radio" name="doc_type_radio_atb" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
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

                        <!-- ============================================================= -->
                        <!-- MULTI-ITEM REPEATER KHUSUS ATB (ASET TIDAK BERWUJUD)           -->
                        <!-- ============================================================= -->
                        <div class="space-y-4">

                            <!-- Header Pembungkus ATB -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-violet-950/30 border border-violet-500/40 shadow-md">
                                <div class="space-y-0.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-violet-500/20 text-violet-400 text-sm">💻</span>
                                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                                            RINCIAN ASET TIDAK BERWUJUD (<span class="text-violet-400" x-text="formData.atb_items.length"></span> Item ATB Terdaftar)
                                        </h3>
                                    </div>
                                    <p class="text-[11px] text-slate-400">
                                        Setiap item ATB memiliki Judul/Nama, Pencipta/Vendor, Spesifikasi, Jumlah, Satuan, Kondisi, Nilai Satuan, Administrasi Proyek, dan Ruang/Pemegang masing-masing.
                                    </p>
                                </div>
                                <button type="button" @click="addAtbItem()"
                                        class="px-4 py-2.5 rounded-xl bg-violet-600 hover:bg-violet-500 text-white text-xs font-bold transition-all flex items-center justify-center space-x-2 shadow-lg shadow-violet-600/30 border border-violet-400/40 shrink-0 cursor-pointer active:scale-95">
                                    <span>➕</span>
                                    <span>Tambah Item ATB Baru</span>
                                </button>
                            </div>

                            <!-- List Kartu ATB (Repeater) -->
                            <div class="space-y-5">
                                <template x-for="(item, idx) in formData.atb_items" :key="idx">
                                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-violet-500/30 hover:border-violet-500/60 transition-all space-y-4 shadow-xl relative group">
                                        
                                        <!-- Header Kartu Tiap ATB -->
                                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span class="px-3 py-1 rounded-xl bg-violet-500/20 text-violet-300 font-mono font-extrabold text-xs border border-violet-500/40 flex items-center space-x-1.5">
                                                    <span>💻 Item ATB #<span x-text="idx + 1"></span></span>
                                                </span>
                                                <span class="text-[11px] text-slate-300 font-semibold" x-show="item.atb_nama_barang">
                                                    • <span x-text="item.atb_nama_barang"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono" x-show="item.atb_judul_nama">
                                                    • <span x-text="item.atb_judul_nama"></span>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Qty: <strong class="text-cyan-300" x-text="(item.atb_jumlah || 1) + ' ' + (item.atb_satuan || 'Lisensi')"></strong>
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono">
                                                    • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getAtbSubtotal(item))"></strong>
                                                </span>
                                            </div>

                                            <!-- Tombol Hapus ATB (Muncul jika > 1 item) -->
                                            <button type="button" 
                                                    x-show="formData.atb_items.length > 1" 
                                                    @click="removeAtbItem(idx)" 
                                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                                <span>🗑️ Hapus Item Ini</span>
                                            </button>
                                        </div>

                                        <!-- Grid Form Pengisian Spesifikasi ATB -->
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                            <!-- Kolom Kiri: Identitas ATB -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>💻 Identitas &amp; Spesifikasi ATB:</span>
                                                    </span>
                                                </div>

                                                <!-- Nama Barang (Terkunci) -->
                                                <div>
                                                    <div class="flex items-center justify-between mb-1">
                                                        <label class="block text-slate-400 text-[10px] font-semibold">Nama Barang (PMDN 108)</label>
                                                        <span class="text-[9px] text-amber-400/80 flex items-center gap-1 font-medium bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20">
                                                            <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                            Terkunci (Mengikuti Langkah 2)
                                                        </span>
                                                    </div>
                                                    <input type="text" :value="item.atb_nama_barang || formData.atb_nama_barang || formData.sub_rincian_nama || 'Aset Tidak Berwujud'" readonly
                                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                </div>

                                                <!-- Judul / Nama Software -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul / Nama Software &amp; Lisensi</label>
                                                    <input type="text" x-model="item.atb_judul_nama" placeholder="Aplikasi SIMAT-RK (Sistem Informasi Manajemen Aset)..."
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                                                </div>

                                                <!-- Pencipta / Vendor -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Vendor / Pengembang</label>
                                                    <input type="text" x-model="item.atb_pencipta" placeholder="Tim IT SIMRS RSUD & Pengembang Sistem"
                                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                                                </div>

                                                <!-- Spesifikasi -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi Software / Hak Cipta</label>
                                                    <textarea rows="2" x-model="item.atb_spesifikasi" placeholder="Web-Based, Multi-Role Access, Integrasi SatuSehat & RME..."
                                                              class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-cyan-300 focus:outline-none focus:border-cyan-500 resize-none"></textarea>
                                                </div>
                                            </div>

                                            <!-- Kolom Kanan: Volume & Nilai -->
                                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                                                    <span class="text-xs font-bold text-violet-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📦 Volume, Nilai &amp; Ruang Pemegang:</span>
                                                    </span>
                                                </div>

                                                <!-- Jumlah, Satuan, Kondisi -->
                                                <div class="grid grid-cols-3 gap-2">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jumlah</label>
                                                        <input type="number" min="1" x-model.number="item.atb_jumlah" placeholder="1"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-violet-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nama Satuan</label>
                                                        <input type="text" x-model="item.atb_satuan" placeholder="Lisensi"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-violet-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi</label>
                                                        <select x-model="item.atb_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-violet-500">
                                                            <option value="Baik">Baik (B)</option>
                                                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Nilai Satuan -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nilai Satuan Barang (Rp)</span>
                                                        <span class="text-[9px] font-bold text-violet-400">Wajib Diisi</span>
                                                    </label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                                        <input type="number" x-model.number="item.atb_nilai_satuan" placeholder="145000000"
                                                               :class="Number(item.atb_nilai_satuan || 0) <= 0 ? 'border-amber-500/50' : 'border-slate-700'"
                                                               class="w-full bg-slate-950 border rounded-xl pl-9 pr-3 py-2 text-xs text-violet-300 font-mono font-bold focus:border-violet-500">
                                                    </div>
                                                    <span x-show="Number(item.atb_nilai_satuan || 0) <= 0" class="text-[9px] font-bold text-amber-400 block mt-1">
                                                        ⚠️ Wajib diisi (nilai satuan tidak boleh Rp 0).
                                                    </span>
                                                </div>

                                                <!-- Administrasi Proyek -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Administrasi Proyek (Rp)</label>
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-2 text-slate-500 text-xs font-bold">Rp</span>
                                                        <input type="number" x-model.number="item.atb_administrasi_proyek" placeholder="5000000"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl pl-9 pr-3 py-2 text-xs text-amber-300 font-mono font-bold focus:border-violet-500">
                                                    </div>
                                                </div>

                                                <!-- Subtotal Item -->
                                                <div class="p-3 rounded-xl bg-violet-950/40 border border-violet-500/30 flex items-center justify-between">
                                                    <span class="text-[10px] text-violet-400 font-semibold uppercase tracking-wider">Subtotal Item Ini:</span>
                                                    <span class="text-sm font-black text-violet-300 font-mono" x-text="'Rp ' + formatRupiah(getAtbSubtotal(item))"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Ruang / Pemegang — Full Width (di bawah grid, terpisah) -->
                                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-violet-500/40 space-y-2" @click.away="item.isRuangOpen = false">
                                            <div class="flex items-center justify-between border-b border-violet-500/20 pb-2">
                                                <label class="block text-violet-400 font-bold text-xs uppercase tracking-wider flex items-center space-x-2">
                                                    <span>📍 RUANG / PEMEGANG (PENANGGUNG JAWAB & LOKASI ITEM INI):</span>
                                                </label>
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold flex items-center space-x-1">
                                                        <span>🏥</span>
                                                        <span>Tersinkron Unit & Paviliun</span>
                                                    </span>
                                                    <button type="button"
                                                            x-show="item.atb_ruang_pemegang"
                                                            @click="item.atb_ruang_pemegang = ''; item.searchRuang = ''; item.isRuangOpen = true"
                                                            class="text-[10.5px] font-bold text-rose-400 hover:text-rose-300 transition-colors">
                                                        ✕ Reset
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="relative">
                                                <input type="text"
                                                       :value="!item.isRuangOpen ? item.atb_ruang_pemegang : item.searchRuang"
                                                       @input="item.atb_ruang_pemegang = $event.target.value; item.searchRuang = $event.target.value; item.isRuangOpen = true"
                                                       @focus="item.isRuangOpen = true"
                                                       placeholder="Ketik atau pilih nama Ruang / Unit / Paviliun dari master data RSUD..."
                                                       class="w-full bg-slate-900 border border-slate-700 hover:border-violet-500 focus:border-violet-500 rounded-xl px-4 py-3 pl-10 text-xs text-white font-semibold focus:outline-none transition-all">
                                                <svg class="w-4 h-4 text-violet-400 absolute left-3.5 top-3.5 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                            </div>
                                            <!-- Dropdown List Pilihan Unit & Paviliun -->
                                            <div x-show="item.isRuangOpen" x-transition x-cloak style="max-height: 210px;"
                                                 class="absolute left-0 right-0 z-40 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-violet-500/50 rounded-2xl shadow-2xl overflow-y-auto divide-y divide-slate-800">
                                                <div class="px-3 py-1.5 bg-slate-950/80 rounded-xl text-[10px] font-bold text-violet-400 uppercase tracking-wider flex items-center justify-between">
                                                    <span>PILIH DARI DATA UNIT & PAVILIUN RSUD:</span>
                                                    <span class="text-slate-400 font-mono text-[9.5px]" x-text="(masterUnits || []).filter(u => !item.searchRuang || u.nama.toLowerCase().includes(item.searchRuang.toLowerCase())).length + ' Unit/Ruangan'"></span>
                                                </div>
                                                <template x-for="u in (masterUnits || []).filter(u => !item.searchRuang || u.nama.toLowerCase().includes(item.searchRuang.toLowerCase()))" :key="u.id">
                                                    <div @click="item.atb_ruang_pemegang = u.nama; item.isRuangOpen = false; item.searchRuang = ''"
                                                         class="p-2.5 rounded-xl bg-slate-950/50 hover:bg-violet-500/15 border border-slate-800/60 hover:border-violet-500/40 cursor-pointer transition-all flex items-center justify-between group">
                                                        <div class="min-w-0 pr-2">
                                                            <div class="flex items-center space-x-2">
                                                                <span class="text-xs font-bold text-white group-hover:text-violet-300 truncate" x-text="u.nama"></span>
                                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-mono font-bold bg-slate-800 text-slate-300 border border-slate-700" x-text="u.tipe || 'Unit'"></span>
                                                            </div>
                                                            <p class="text-[10px] text-slate-400 truncate mt-0.5" x-text="'Kepala/PJ: ' + (u.kepala || '-') + ' • Kode: ' + (u.kode || '-')"></p>
                                                        </div>
                                                        <span class="px-2 py-1 rounded-lg bg-slate-900 text-violet-300 border border-violet-500/30 text-[10px] font-bold shrink-0">Pilih →</span>
                                                    </div>
                                                </template>
                                                <template x-if="(masterUnits || []).filter(u => !item.searchRuang || u.nama.toLowerCase().includes(item.searchRuang.toLowerCase())).length === 0">
                                                    <div class="p-3 text-center text-xs text-slate-400">
                                                        <span>Tidak ada unit yang cocok. Ketikkan nama secara manual jika tidak ada di daftar.</span>
                                                    </div>
                                                </template>
                                            </div>
                                        </div>

                                    </div>
                                </template>
                            </div>

                            <!-- Tombol Tambah ATB Lainnya (Violet-Style, Dashed Border) -->
                            <button type="button" @click="addAtbItem()"
                                    class="w-full py-3.5 border-2 border-dashed border-violet-500/50 hover:border-violet-400 bg-violet-950/20 hover:bg-violet-950/40 text-violet-300 hover:text-violet-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
                                <span class="text-base group-hover:scale-125 transition-transform">➕</span>
                                <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Aset Tidak Berwujud Lainnya</span>
                            </button>

                            <!-- Ringkasan Total ATB -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-violet-500/30 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex flex-wrap items-center gap-4">
                                    <div>
                                        <span class="text-[10px] text-slate-400 font-semibold block uppercase tracking-wider">💰 Jumlah Anggaran:</span>
                                        <span class="text-sm font-black text-white font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_anggaran)"></span>
                                    </div>
                                    <div class="h-7 w-px bg-slate-700 hidden sm:block"></div>
                                    <div>
                                        <span class="text-[10px] text-emerald-400 font-semibold block uppercase tracking-wider">📈 Jumlah Realisasi (Kolom 15):</span>
                                        <span class="text-sm font-black text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(formData.jumlah_realisasi)"></span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] text-violet-400 font-semibold block uppercase tracking-wider">Total Nilai Semua ATB:</span>
                                    <span class="text-base font-extrabold text-violet-300 font-mono" x-text="'Rp ' + formatRupiah(totalNilaiAtb)"></span>
                                </div>
                            </div>
                        </div>

                        <!-- ========================================================================= -->
                        <!-- LIVE PREVIEW TABEL ATB (Format Excel Resmi ATB 23 Kolom)               -->
                        <!-- ========================================================================= -->
                        <div class="space-y-2 pt-4">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📄 Live Preview Tabel Aset Tidak Berwujud Sesuai Gambar:</span>
                                </span>
                                <span class="text-[10px] text-violet-400 font-mono">Format Excel Resmi ATB (23 Kolom)</span>
                            </div>

                            <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-2xl">
                                <table class="w-full text-center text-[10px] border-collapse font-sans min-w-[1450px]">
                                    <!-- Header Utama Pastel Senada -->
                                    <thead>
                                        <tr class="bg-[#fde9d9] text-slate-950 font-black border-b border-slate-600">
                                            <th colspan="22" class="py-2 text-xs uppercase tracking-wider border border-slate-500 bg-[#fde9d9]">
                                                RINCIAN BELANJA MODAL SESUAI SPK / SURAT PESANAN/KWITANSI /INVOICE/2026
                                            </th>
                                            <th rowspan="4" class="px-3 py-2 border border-slate-500 w-44 align-middle bg-[#fde9d9] font-bold text-slate-950 text-[10.5px]">
                                                Ruang /<br>Pemegang
                                            </th>
                                        </tr>
                                        <!-- Header Tingkat 1 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500">
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">NAMA BARANG<br><span class="font-normal text-[9px]">(Uraian Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Kode Barang<br><span class="font-normal text-[9px]">(Kode Sub Sub Rincian Objek PMDN 108)</span></th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">Judul / Nama</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-32 align-middle bg-[#fde9d9]">Pencipta</th>
                                            <th rowspan="3" class="px-2 py-1.5 border border-slate-500 w-36 align-middle bg-[#fde9d9]">Spesifikasi</th>
                                            <th colspan="8" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">Riwayat Pembelian</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">VOLUME</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Nilai Satuan Barang (Rp)</th>
                                            <th rowspan="3" class="px-2.5 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">ADMINISTRASI PROYEK (Rp)</th>
                                            <th rowspan="3" class="px-3 py-1.5 border border-slate-500 w-28 align-middle bg-[#fde9d9]">Total Nilai Barang (Rp)</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">SP2D</th>
                                            <th colspan="2" class="px-2 py-1 border border-slate-500 bg-[#fde9d9]">BAST pada SPK/Surat Pesanan/Kwitansi/Invoice</th>
                                        </tr>
                                        <!-- Header Tingkat 2 -->
                                        <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-500 text-[9.5px]">
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">SPK</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Surat Pesanan</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Kwitansi</th>
                                            <th colspan="2" class="px-2 py-0.5 border border-slate-500">Invoice</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Jumlah</th>
                                            <th rowspan="2" class="px-2 py-1 border border-slate-500 align-middle bg-[#fde9d9]">Nama Satuan Barang</th>
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
                                    <!-- Body Data Live - Multi-Row per item ATB -->
                                    <tbody class="bg-white text-slate-950 font-medium text-[9.5px]">
                                        <template x-for="(item, idx) in formData.atb_items" :key="idx">
                                            <tr>
                                                <td class="px-2 py-2 border border-slate-400 text-left font-semibold" x-text="item.atb_nama_barang || formData.atb_nama_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold" x-text="item.atb_kode_barang || formData.atb_kode_barang"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="item.atb_judul_nama"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="item.atb_pencipta"></td>
                                                <td class="px-2 py-2 border border-slate-400 text-left" x-text="item.atb_spesifikasi"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.spk_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.spk_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.surat_pesanan_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.surat_pesanan_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.kwitansi_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.kwitansi_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.faktur_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.faktur_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono font-bold" x-text="item.atb_jumlah || 1"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-semibold" x-text="item.atb_satuan || 'Lisensi'"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(item.atb_nilai_satuan)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono text-right" x-text="formatRupiah(item.atb_administrasi_proyek)"></td>
                                                <td class="px-2 py-2 border border-slate-400 font-mono font-bold text-right text-violet-800" x-text="formatRupiah(getAtbSubtotal(item))"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.sp2d_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.sp2d_tanggal)"></td>
                                                <td class="px-1.5 py-2 border border-slate-400 font-mono" x-text="formData.bast_dokumen_nomor"></td>
                                                <td class="px-1.5 py-2 border border-slate-400" x-text="formatDateDisplay(formData.bast_dokumen_tanggal)"></td>
                                                <td class="px-2.5 py-2 border border-slate-400 text-left font-medium" x-text="item.atb_ruang_pemegang || formData.ruang_pemegang_atb"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </template>
