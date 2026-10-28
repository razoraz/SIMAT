<!-- ========================================================================= -->
<!-- LANGKAH 3: LEMBAR VERIFIKASI & KONFIRMASI BELANJA BARANG PERBEKALAN       -->
<!-- 100% PERSIS POLA MUTASI EKSTERNAL & KEMITRAAN                             -->
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
                Pastikan data dokumen faktur toko rekanan, kedua belah pihak, rincian fisik aset, dan nilai belanja telah sesuai sebelum disahkan ke database SIMAT-RK.
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

    <!-- ===== BANNER ERROR INLINE LANGKAH 3 ===== -->
    <template x-if="stepErrors[3]">
        <div class="flex items-start gap-3 p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 shadow-lg shadow-rose-500/10 animate-[fadeInDown_0.25s_ease-out]">
            <span class="text-rose-400 text-lg mt-0.5 shrink-0">⚠️</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-rose-300 mb-0.5">Perhatian — Verifikasi Data Belum Lengkap</p>
                <p class="text-xs text-rose-200/90 leading-relaxed" x-text="stepErrors[3]"></p>
            </div>
            <button type="button" @click="clearStepError(3)" class="ml-auto shrink-0 text-rose-400 hover:text-rose-200 transition-colors text-sm leading-none">✕</button>
        </div>
    </template>

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
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-indigo-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">2. KERANJANG BARANG</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-mono"
                    x-text="formData.items.length + ' Macam'">
                </span>
            </div>
            <div class="text-xs font-black font-mono text-indigo-300" x-text="totalVolumeFaktur + ' Total Unit Fisik'"></div>
            <div class="text-[11px] text-emerald-400 font-mono font-black" x-text="formatRupiah(totalNilaiFaktur)"></div>
            <div class="text-[10px] text-slate-400 pt-1 border-t border-slate-900 flex items-center justify-between">
                <span>Kategori Utama:</span>
                <span class="text-indigo-300 font-mono" x-text="kibLabel"></span>
            </div>
        </div>

        <!-- Status 3: Alokasi Penempatan (Opsi A) -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-indigo-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">3. ALUR PENEMPATAN</span>
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
                    x-text="globalAlokasiMode === 'ruangan' ? 'Tidak Tersedia (Di Ruangan)' : 'Tersedia di Gudang'"></span>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 3. SEKSI UTAMA 1: DOKUMEN FAKTUR & PARA PIHAK (PENYEDIA VS RSUD)          -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 shadow-xl space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-extrabold border border-indigo-500/30">1</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">
                        Dokumen Faktur Pembelian &amp; Para Pihak
                    </h3>
                    <p class="text-[11px] text-slate-400">Identitas bukti transaksi pembelian, toko rekanan penyedia, dan penanggung jawab RSUD</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">
                    Periode: <span x-text="(formData.triwulan || 'TW I') + ' · ' + (formData.tahun_perolehan || new Date().getFullYear())"></span>
                </span>
            </div>
        </div>

        <!-- Metadata Faktur Ringkas & Rapi -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Nomor Faktur / Nota Toko:</span>
                <span class="font-mono font-black text-indigo-300 text-xs sm:text-sm block truncate" x-text="formData.rekening_nomor_faktur || '-'"></span>
            </div>
            
            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tanggal Faktur Pembelian:</span>
                <span class="font-semibold text-white text-xs sm:text-sm block font-mono" x-text="formatTanggalIndo(formData.rekening_tanggal_faktur) || '-'"></span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Berkas Lampiran Nota:</span>
                <template x-if="fileFaktur">
                    <span class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-emerald-400 truncate max-w-full">
                        <span>📄</span>
                        <span class="truncate" x-text="fileFaktur.name"></span>
                        <span class="text-[9px] text-emerald-500 font-sans font-normal" x-text="'(' + formatFileSize(fileFaktur.size) + ')'"></span>
                    </span>
                </template>
                <template x-if="!fileFaktur">
                    <span class="text-xs text-slate-500 italic block">Tidak ada berkas (Opsional)</span>
                </template>
            </div>
        </div>

        <!-- Kolom Komparasi Berdampingan: TOKO REKANAN vs RSUD PENERIMA -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-1">
            
            <!-- BLOK TOKO REKANAN (PIHAK PENYEDIA) -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3 relative flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs">🏪</span>
                            <span class="text-xs font-black text-amber-300 uppercase tracking-wider">PIHAK PENYEDIA (Toko Rekanan)</span>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">
                            Supplier
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs pt-2">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nama Toko / Rekanan:</span>
                            <span class="font-extrabold text-white text-sm block" x-text="formData.rekening_penyedia || 'Toko Rekanan (Testing)'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Perlakuan Rekening Akuntansi:</span>
                            <span class="text-amber-300/90 font-mono text-[11px] block">Belanja Barang &amp; Jasa (Akun 5.1.02) · Ekstrakomptabel</span>
                        </div>
                        <div x-show="formData.rekening_keterangan">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Keterangan Belanja:</span>
                            <span class="text-slate-300 italic text-[11px] block leading-snug" x-text="formData.rekening_keterangan"></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOK RSUD KOESNANDI (PIHAK PENERIMA & PENANGGUNG JAWAB) -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3 relative flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs">🏛️</span>
                            <span class="text-xs font-black text-indigo-300 uppercase tracking-wider">PIHAK PENERIMA (RSUD Koesnandi)</span>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">
                            Pengelola
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs pt-2">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Unit Kerja Penerima:</span>
                            <span class="font-extrabold text-white text-sm block">Instalasi Perbekalan / Gudang Logistik RSUD</span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pejabat Pembuat Komitmen (PPK):</span>
                            <span class="text-indigo-300 font-bold text-xs block" x-text="formData.ppk_nama || 'BUDI HARTONO, S.Sos'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tujuan Distribusi Fisik:</span>
                            <span class="font-bold text-xs block"
                                :class="globalAlokasiMode === 'ruangan' ? 'text-indigo-300' : 'text-emerald-400'"
                                x-text="globalAlokasiMode === 'ruangan' ? ('Langsung ke KIR: ' + (getUnitNameById(globalUnitId) || 'Ruangan RSUD')) : 'Transit Stok Gudang Perbekalan (Siap BAST Distribusi)'"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 4. SEKSI UTAMA 2: RINCIAN BARANG BELANJA (MULTI-ITEM)                     -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-extrabold border border-indigo-500/30">2</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">
                        Rincian Barang Belanja (Multi-Item)
                    </h3>
                    <p class="text-[11px] text-slate-400">Daftar item barang perbekalan, kode klasifikasi 108, volume, dan harga satuan</p>
                </div>
            </div>
            
            <span class="text-xs font-mono px-3 py-1 rounded-xl bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-black"
                x-text="'Total: ' + formatRupiah(totalNilaiFaktur)">
            </span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60">
            <table class="w-full text-left text-xs min-w-[700px]">
                <thead class="bg-slate-950 text-slate-400 uppercase font-mono text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-2.5">No</th>
                        <th class="px-4 py-2.5">Nama Barang</th>
                        <th class="px-4 py-2.5">Kode 108</th>
                        <th class="px-4 py-2.5 text-center">Volume</th>
                        <th class="px-4 py-2.5 text-right">Harga Satuan</th>
                        <th class="px-4 py-2.5 text-right">Subtotal</th>
                        <th class="px-4 py-2.5">Penempatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-mono text-[11px]">
                    <template x-for="(item, idx) in activeSheetItems" :key="idx">
                        <tr class="hover:bg-indigo-500/5 transition-colors">
                            <td class="px-4 py-2.5 text-slate-400 font-bold" x-text="idx + 1"></td>
                            <td class="px-4 py-2.5 text-white font-sans font-bold">
                                <span x-text="item.nama_barang || 'Barang #' + (idx + 1)"></span>
                                <span x-show="item.spesifikasi" class="text-[10px] text-slate-400 block font-normal" x-text="item.spesifikasi"></span>
                            </td>
                            <td class="px-4 py-2.5 text-indigo-300" x-text="item.kode_108 || '-'"></td>
                            <td class="px-4 py-2.5 text-center text-white" x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></td>
                            <td class="px-4 py-2.5 text-right text-slate-300" x-text="formatRupiah(item.harga_satuan || 0)"></td>
                            <td class="px-4 py-2.5 text-right text-emerald-400 font-bold" x-text="formatRupiah(item.subtotal || 0)"></td>
                            <td class="px-4 py-2.5 text-slate-300 font-sans text-[11px]">
                                <span x-text="item.penempatan"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Rekap Total Nilai Faktur -->
        <div class="p-4 rounded-2xl bg-slate-900 border border-slate-800 flex items-center justify-between">
            <span class="text-xs font-bold text-slate-300 uppercase">Total Nilai Pembelian Faktur:</span>
            <span class="text-xl font-mono font-black text-emerald-400" x-text="formatRupiah(totalNilaiFaktur)"></span>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 5. SEKSI UTAMA 3: SIMULASI PREVIEW REGISTER NIBAR (28-DIGIT)              -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-extrabold border border-indigo-500/30">3</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">
                        Simulasi Preview Nomor Register NIBAR (28-Digit)
                    </h3>
                    <p class="text-[11px] text-slate-400">Nomor inventaris fisik dan barcode/QR yang akan otomatis diterbitkan oleh sistem untuk setiap unit fisik</p>
                </div>
            </div>
            
            <span class="text-[10px] font-mono text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-lg border border-indigo-500/20 font-bold">
                Total Digenerate: <strong x-text="totalVolumeFaktur"></strong> Unit
            </span>
        </div>

        <!-- Tabel Ringkas Preview NIBAR -->
        <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 max-h-60 overflow-y-auto custom-scrollbar">
            <table class="w-full text-left text-xs min-w-[650px]">
                <thead class="bg-slate-950 text-slate-400 uppercase font-mono text-[10px] tracking-wider border-b border-slate-800 sticky top-0 z-10 backdrop-blur-md">
                    <tr>
                        <th class="px-4 py-2.5">Unit Ke</th>
                        <th class="px-4 py-2.5">Nama Barang</th>
                        <th class="px-4 py-2.5">Simulasi NIBAR 28-Digit</th>
                        <th class="px-4 py-2.5">Lokasi / Ruangan</th>
                        <th class="px-4 py-2.5 text-center">Kondisi Fisik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-mono text-[11px]">
                    <template x-for="(u, uIdx) in simulatedUnits.slice(0, 15)" :key="uIdx">
                        <tr class="hover:bg-indigo-500/5 transition-colors">
                            <td class="px-4 py-2 text-slate-400 font-semibold" x-text="'Unit #' + u.unitNumber"></td>
                            <td class="px-4 py-2 text-white font-sans font-bold text-xs">
                                <span class="truncate block max-w-[220px]" :title="u.nama" x-text="u.nama"></span>
                            </td>
                            <td class="px-4 py-2 text-indigo-300 font-bold tracking-wider" x-text="generateSimulatedNibar(u.unitNumber, u.kode108)"></td>
                            <td class="px-4 py-2 text-slate-300 font-sans">
                                <span class="inline-flex items-center gap-1 truncate max-w-[180px]" :title="u.ruang">
                                    <span class="text-indigo-400 text-xs">📍</span>
                                    <span x-text="u.ruang"></span>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold border inline-block"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-400 border-emerald-500/30': u.kondisi === 'Baik' || u.kondisi === 'B',
                                        'bg-amber-500/15 text-amber-400 border-amber-500/30': u.kondisi === 'Kurang Baik' || u.kondisi === 'KB',
                                        'bg-rose-500/15 text-rose-400 border-rose-500/30': u.kondisi === 'Rusak Berat' || u.kondisi === 'RB'
                                    }"
                                    x-text="u.kondisi || 'Baik'"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <template x-if="simulatedUnits.length > 15">
            <p class="text-[10px] text-slate-500 italic">
                * Menampilkan preview 15 unit pertama dari total <span x-text="simulatedUnits.length"></span> unit yang akan dicatat ke database.
            </p>
        </template>
    </div>

    <!-- ========================================================================= -->
    <!-- 6. SEKSI UTAMA 4: CHECKLIST KONFIRMASI & PENGESAHAN VERIFIKASI AKHIR      -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-3xl bg-slate-950/90 border space-y-4 shadow-2xl relative overflow-hidden transition-all duration-300"
        :class="isDataVerified ? 'border-emerald-500/60 shadow-emerald-500/10 bg-emerald-950/15 ring-1 ring-emerald-500/30' : 'border-slate-800 hover:border-indigo-500/40'">
        
        <div class="flex items-start space-x-3.5">
            <div class="pt-0.5">
                <input type="checkbox" id="confirmVerification" x-model="isDataVerified"
                    class="w-5 h-5 rounded-lg bg-slate-900 border-slate-700 text-emerald-500 focus:ring-emerald-400 focus:ring-offset-slate-950 cursor-pointer">
            </div>
            <label for="confirmVerification" class="cursor-pointer select-none space-y-1.5">
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-sm font-extrabold text-white">
                        Pernyataan Verifikasi &amp; Pengesahan Pembukuan Belanja Barang
                    </span>
                    <span x-show="isDataVerified" class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1">
                        <span>✓</span>
                        <span>Data Telah Diverifikasi &amp; Siap Diterbitkan</span>
                    </span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Saya menyatakan dengan sebenarnya bahwa data faktur/nota pembelian toko rekanan, rincian barang perbekalan, kode klasifikasi Permendagri 108, alur penempatan gudang/ruangan (KIR), dan nilai pembelian telah diperiksa sesuai dengan fisik barang yang diterima di RSUD Dr. H. Koesnandi. Data ini siap dibukukan ke dalam penatausahaan inventaris SIMAT-RK.
                </p>
            </label>
        </div>
    </div>

</div>
