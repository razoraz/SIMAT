                <!-- ===================================================================== -->
                <!-- KONDISI G: JIKA MEMILIH KATEGORI LAINNYA (RENOVASI)                   -->
                <!-- ===================================================================== -->
                <template x-if="!isTanah && !isMesin && !isGedung && !isJaringan && !isAsetLainnya && !isAtb && !isKdp">
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            
                            <!-- 1. SPK / Kontrak Kerja -->
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-cyan-400"></span>
                                    <span class="text-xs font-bold text-white uppercase tracking-wider">1. Surat Perintah Kerja (SPK / Kontrak)</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Nomor SPK / Kontrak</label>
                                    <input type="text" x-model="formData.spk_nomor" placeholder="Contoh: 028/SPK-KTR/V/2026"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-cyan-300 font-mono font-bold focus:outline-none focus:border-cyan-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Tanggal SPK</label>
                                    <input type="text" x-datepicker x-model="formData.spk_tanggal" :min="minDateTriwulan" :max="maxDateTriwulan" @change="onDocDateChange($event.target.value, 'spk_tanggal')" placeholder="dd/mm/yyyy"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-cyan-500">
                                </div>
                            </div>

                            <!-- 2. Surat Pesanan (SP / BAP) -->
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400"></span>
                                    <span class="text-xs font-bold text-white uppercase tracking-wider">2. Surat Pesanan (SP / BAP)</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Nomor Surat Pesanan</label>
                                    <input type="text" x-model="formData.surat_pesanan_nomor" placeholder="Contoh: 028/SP-RSUD/V/2026"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-purple-300 font-mono font-bold focus:outline-none focus:border-purple-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Tanggal Surat Pesanan</label>
                                    <input type="text" x-datepicker x-model="formData.surat_pesanan_tanggal" :min="minDateTriwulan" :max="maxDateTriwulan" @change="onDocDateChange($event.target.value, 'surat_pesanan_tanggal')" placeholder="dd/mm/yyyy"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-purple-500">
                                </div>
                            </div>

                            <!-- 3. Kwitansi Pembayaran -->
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                                    <span class="text-xs font-bold text-white uppercase tracking-wider">3. Bukti Kwitansi Pembayaran</span>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Nomor Kwitansi</label>
                                    <input type="text" x-model="formData.kwitansi_nomor" placeholder="Contoh: KW-028/KTR/2026"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-amber-300 font-mono font-bold focus:outline-none focus:border-amber-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Tanggal Kwitansi</label>
                                    <input type="text" x-datepicker x-model="formData.kwitansi_tanggal" :min="minDateTriwulan" :max="maxDateTriwulan" @change="onDocDateChange($event.target.value, 'kwitansi_tanggal')" placeholder="dd/mm/yyyy"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none focus:border-amber-500">
                                </div>
                            </div>

                            <!-- 4. Faktur Pajak / Invoice / Rekanan -->
                            <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                                <div class="flex items-center space-x-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400"></span>
                                    <span class="text-xs font-bold text-white uppercase tracking-wider">4. Faktur Invoice & Rekanan</span>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Nomor Faktur</label>
                                        <input type="text" x-model="formData.faktur_nomor" placeholder="INV-2026-028"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-emerald-300 font-mono font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[11px] mb-1">Tanggal Faktur</label>
                                        <input type="text" x-datepicker x-model="formData.faktur_tanggal" :min="minDateTriwulan" :max="maxDateTriwulan" @change="onDocDateChange($event.target.value, 'faktur_tanggal')" placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1">Nama Perusahaan Rekanan / Penyedia</label>
                                    <input type="text" x-model="formData.penyedia_nama" placeholder="PT / CV Penyedia Barang"
                                           class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white font-semibold">
                                </div>
                            </div>

                        </div>
                    </div>
                </template>
