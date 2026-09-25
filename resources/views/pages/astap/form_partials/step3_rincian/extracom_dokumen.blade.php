                        <!-- ========================================================================= -->
                        <!-- 1. DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA) - MODE EXTRACOM             -->
                        <!-- ========================================================================= -->
                        <div class="p-5 rounded-2xl bg-slate-950/70 border border-cyan-500/40 space-y-4 shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold text-cyan-300 uppercase tracking-wider">1. RIWAYAT DOKUMEN PEMBELIAN (PILIH 1 DOKUMEN UTAMA):</span>
                                    <span x-show="formData.tahun_anggaran && formData.triwulan" class="text-[10px] px-2.5 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 font-mono font-bold border border-cyan-500/30">
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
                                            <input type="radio" name="doc_type_radio_extracom" value="spk" :checked="formData.doc_type === 'spk'" @change="selectDocType('spk')" class="text-cyan-500 focus:ring-cyan-500">
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
                                            <input type="radio" name="doc_type_radio_extracom" value="surat_pesanan" :checked="formData.doc_type === 'surat_pesanan'" @change="selectDocType('surat_pesanan')" class="text-purple-500 focus:ring-purple-500">
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
                                            <input type="radio" name="doc_type_radio_extracom" value="kwitansi" :checked="formData.doc_type === 'kwitansi'" @change="selectDocType('kwitansi')" class="text-amber-500 focus:ring-amber-500">
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
                                            <input type="radio" name="doc_type_radio_extracom" value="faktur" :checked="formData.doc_type === 'faktur'" @change="selectDocType('faktur')" class="text-emerald-500 focus:ring-emerald-500">
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

                        <!-- ========================================================================= -->
                        <!-- 2. DOKUMEN SP2D & BAST - MODE EXTRACOM                                    -->
                        <!-- ========================================================================= -->
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
