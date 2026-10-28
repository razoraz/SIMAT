<!-- ========================================================================= -->
<!-- LANGKAH 3: LEMBAR VERIFIKASI & KONFIRMASI BELANJA BARANG PERBEKALAN       -->
<!-- ========================================================================= -->
<div x-show="currentStep === 3 || step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <!-- 1. Header Banner & Navigasi Cepat -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-800">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 text-xs font-bold mb-1.5">
                <span>🛡️ LANGKAH 3 DARI 3: VERIFIKASI &amp; KONFIRMASI RESMI</span>
            </div>
            <h2 class="text-xl font-black text-white tracking-tight flex items-center space-x-2">
                <span>Lembar Verifikasi Belanja Barang (Perbekalan)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Pastikan data dokumen faktur toko rekanan, berkas nota asli, rincian barang, dan alokasi penempatan KIR telah sesuai sebelum diterbitkan ke database SIMAT-RK.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
            <button type="button" @click="goToStep(1)"
                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-700 shadow-sm cursor-pointer">
                <span>✏️ Edit Dokumen (Langkah 1)</span>
            </button>
            <button type="button" @click="goToStep(2)"
                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-700 shadow-sm cursor-pointer">
                <span>✏️ Edit Barang (Langkah 2)</span>
            </button>
        </div>
    </div>

    <!-- 2. QUICK HEALTH CHECK STATUS (3 KARTU INDIKATOR UTAMA) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
        <!-- Status 1: Toko Rekanan & Faktur -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-indigo-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">1. TOKO &amp; FAKTUR</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-mono">
                    Akun 5.1.02
                </span>
            </div>
            <div class="text-xs font-black font-mono text-indigo-300 truncate" x-text="formData.rekening_nomor_faktur || 'Nomor Faktur Otomatis'"></div>
            <div class="text-[11px] text-white truncate font-bold" x-text="formData.rekening_penyedia || 'Toko Rekanan (Testing)'"></div>
            <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-900 flex items-center justify-between">
                <span>Berkas Nota:</span>
                <span :class="fileFaktur ? 'text-emerald-400 font-bold' : 'text-slate-500 italic'" x-text="fileFaktur ? '✓ Terlampir (' + formatFileSize(fileFaktur.size) + ')' : 'Belum Diunggah'"></span>
            </div>
        </div>

        <!-- Status 2: Keranjang Barang Multi-Item -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-cyan-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider">2. KERANJANG BARANG</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-mono"
                    x-text="formData.items.length + ' Macam'">
                </span>
            </div>
            <div class="text-xs font-black font-mono text-cyan-300" x-text="totalVolumeFaktur + ' Total Unit Fisik'"></div>
            <div class="text-[11px] text-emerald-400 font-mono font-black" x-text="'Rp ' + formatRupiah(totalNilaiFaktur)"></div>
            <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-900 flex items-center justify-between">
                <span>Klasifikasi 108:</span>
                <span class="text-cyan-300 font-mono" x-text="formData.items[0]?.kode_108 || 'Permendagri 108'"></span>
            </div>
        </div>

        <!-- Status 3: Alokasi Penempatan (Opsi A) -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-purple-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider">3. ALUR PENEMPATAN</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full border"
                    :class="globalAlokasiMode === 'ruangan' ? 'bg-indigo-500/10 text-indigo-300 border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-300 border-emerald-500/20'"
                    x-text="globalAlokasiMode === 'ruangan' ? 'KIR Ruangan' : 'Gudang Stok'">
                </span>
            </div>
            <div class="text-xs font-bold text-white truncate"
                x-text="globalAlokasiMode === 'ruangan' ? (getUnitNameById(globalUnitId) || 'Ruangan Belum Dipilih') : 'Gudang Perbekalan RSUD'"></div>
            <div class="text-[10px] text-slate-400 truncate"
                x-text="globalAlokasiMode === 'ruangan' ? 'Langsung Aktif di KIR Ruangan' : 'Buffer Stok · Siap Distribusi'"></div>
            <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-900 flex items-center justify-between">
                <span>Status Register:</span>
                <span class="font-bold" :class="globalAlokasiMode === 'ruangan' ? 'text-indigo-400' : 'text-emerald-400'"
                    x-text="globalAlokasiMode === 'ruangan' ? 'Tidak Tersedia' : 'Tersedia'"></span>
            </div>
        </div>
    </div>

    <!-- 3. SEKSI 1: DOKUMEN FAKTUR PEMBELIAN & TOKO REKANAN -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="w-7 h-7 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-extrabold border border-indigo-500/30">1</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">
                        Dokumen Faktur Pembelian &amp; Rekanan Toko
                    </h3>
                    <p class="text-[11px] text-slate-400">Identitas penyedia perbekalan, nomor faktur/kuitansi, dan berkas bukti pembayaran fisik</p>
                </div>
            </div>
            
            <span class="text-[10px] font-mono px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">
                Periode: <span x-text="(formData.triwulan || 'TW I') + ' · ' + (formData.tahun_perolehan || new Date().getFullYear())"></span>
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-xs">
            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-0.5">Toko / Supplier Rekanan:</span>
                <span class="font-bold text-white block truncate text-sm" x-text="formData.rekening_penyedia || 'Toko Rekanan (Testing)'"></span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-0.5">Nomor Faktur / Nota:</span>
                <span class="font-mono font-bold text-indigo-300 block truncate" x-text="formData.rekening_nomor_faktur || '-'"></span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-0.5">Tanggal Faktur / Pembelian:</span>
                <span class="font-medium text-white block font-mono" x-text="formData.rekening_tanggal_faktur || '-'"></span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block mb-0.5">Berkas Lampiran Nota:</span>
                <template x-if="fileFaktur">
                    <span class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-emerald-400 truncate max-w-full">
                        <span>📄</span>
                        <span class="truncate" x-text="fileFaktur.name"></span>
                    </span>
                </template>
                <template x-if="!fileFaktur">
                    <span class="text-xs text-slate-500 italic block">Tidak ada berkas (Mode Testing)</span>
                </template>
            </div>
        </div>

        <!-- Keterangan Faktur -->
        <template x-if="formData.rekening_keterangan">
            <div class="p-3 rounded-xl bg-slate-900/50 border border-slate-800/80 text-xs">
                <span class="text-[10px] text-slate-500 font-bold uppercase block">Catatan Pembelian:</span>
                <p class="text-slate-300 mt-0.5 whitespace-pre-line" x-text="formData.rekening_keterangan"></p>
            </div>
        </template>
    </div>

    <!-- 4. SEKSI 2: TABEL REKAPITULASI RINCIAN BARANG (MULTI-ITEM) -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="w-7 h-7 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs font-extrabold border border-cyan-500/30">2</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">
                        Rincian Barang Belanja &amp; Klasifikasi Kode 108
                    </h3>
                    <p class="text-[11px] text-slate-400">Daftar macam barang perbekalan yang terdaftar dalam faktur ini</p>
                </div>
            </div>

            <span class="text-xs font-bold text-slate-300">
                Total: <strong class="text-white font-mono" x-text="formData.items.length + ' Macam Barang'"></strong>
            </span>
        </div>

        <!-- Tabel Rincian -->
        <div class="rounded-2xl border border-slate-800 overflow-hidden bg-slate-900/60">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950 text-slate-400 uppercase text-[10px] font-bold border-b border-slate-800">
                    <tr>
                        <th class="py-2.5 px-3 w-10 text-center">No</th>
                        <th class="py-2.5 px-3">Nama Barang &amp; Kode 108</th>
                        <th class="py-2.5 px-3 text-center">Volume</th>
                        <th class="py-2.5 px-3 text-right">Harga Satuan (Rp)</th>
                        <th class="py-2.5 px-3 text-right">Subtotal (Rp)</th>
                        <th class="py-2.5 px-3">Penempatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, iIdx) in formData.items" :key="'verif-item-' + iIdx">
                        <tr class="hover:bg-slate-900/80">
                            <td class="py-3 px-3 text-center font-mono text-slate-500" x-text="iIdx + 1"></td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-white" x-text="item.nama_barang || 'Barang #' + (iIdx + 1)"></div>
                                <div class="text-[10px] font-mono text-cyan-400" x-text="item.kode_108 || '1.3.2.00.00.00'"></div>
                                <template x-if="item.harga_satuan >= 1000000">
                                    <span class="inline-block mt-1 text-[9px] px-1.5 py-0.2 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">
                                        ⚠️ &ge; Rp 1 Jt (Ekstrakomptabel)
                                    </span>
                                </template>
                            </td>
                            <td class="py-3 px-3 text-center font-mono">
                                <span class="font-bold text-white" x-text="item.jumlah_volume || 1"></span>
                                <span class="text-slate-400 text-[10px]" x-text="item.satuan || 'Unit'"></span>
                            </td>
                            <td class="py-3 px-3 text-right font-mono text-slate-300" x-text="formatRupiah(item.harga_satuan || 0)"></td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-emerald-400" x-text="formatRupiah((item.jumlah_volume || 1) * (item.harga_satuan || 0))"></td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                    :class="item.mode_penempatan === 'ruangan' ? 'bg-indigo-500/10 text-indigo-300 border border-indigo-500/20' : 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20'"
                                    x-text="item.mode_penempatan === 'ruangan' ? ('🏢 ' + (getUnitNameById(item.unit_id) || 'Ruangan KIR')) : '📦 Gudang Perbekalan'"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Rekap Total Nilai Faktur -->
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-300 uppercase">Total Nilai Pembelian Faktur:</span>
            <span class="text-xl font-mono font-black text-emerald-400" x-text="'Rp ' + formatRupiah(totalNilaiFaktur)"></span>
        </div>
    </div>

    <!-- 5. SEKSI 3: SPESIFIKASI TEKNIS & SIMULASI NIBAR FISIK -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="w-7 h-7 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-xs font-extrabold border border-purple-500/30">3</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">
                        Spesifikasi Teknis KIB &amp; Penerbitan NIBAR
                    </h3>
                    <p class="text-[11px] text-slate-400">Total <strong class="text-white font-mono" x-text="totalVolumeFaktur"></strong> unit register NIBAR 28-digit dan QR Code akan diterbitkan secara otomatis</p>
                </div>
            </div>
            
            <span class="text-[10px] font-mono px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold">
                ✓ Otomatis Ber-NIBAR
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <template x-for="(item, iIdx) in formData.items" :key="'spec-box-' + iIdx">
                <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800 text-xs space-y-2">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                        <span class="font-bold text-white" x-text="item.nama_barang || 'Barang #' + (iIdx + 1)"></span>
                        <span class="text-[10px] text-slate-400 font-mono" x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span>
                    </div>

                    <div class="grid grid-cols-2 gap-2 text-[11px]">
                        <div>
                            <span class="text-slate-500 block text-[10px]">Merk / Tipe:</span>
                            <span class="text-slate-300 font-semibold" x-text="(item.spesifikasi_json?.merk || '-') + ' / ' + (item.spesifikasi_json?.type || '-')"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px]">Serial No (SN):</span>
                            <span class="text-slate-300 font-mono" x-text="item.spesifikasi_json?.no_pabrik || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px]">Alokasi:</span>
                            <span class="text-indigo-300 font-semibold" x-text="item.mode_penempatan === 'ruangan' ? (getUnitNameById(item.unit_id) || 'KIR Ruangan') : 'Gudang Perbekalan'"></span>
                        </div>
                        <div>
                            <span class="text-slate-500 block text-[10px]">Kondisi:</span>
                            <span class="text-emerald-400 font-semibold" x-text="item.kondisi || 'Baik'"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- 6. PERNYATAAN & KONFIRMASI AKHIR SEBELUM SUBMIT -->
    <div class="p-5 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 space-y-3">
        <div class="flex items-start gap-3">
            <span class="text-lg">⚖️</span>
            <div>
                <h4 class="text-xs font-bold text-white">Konfirmasi &amp; Pengesahan Pembukuan Belanja Barang</h4>
                <p class="text-[11px] text-slate-300 leading-relaxed mt-0.5">
                    Data belanja barang operasional/perbekalan ini dicatat secara ekstrakomptabel untuk pengawasan fisik inventaris ruangan (KIR) RSUD Dr. H. Koesnandi sesuai dengan standar penatausahaan BMD.
                </p>
            </div>
        </div>
    </div>

</div>

