<!-- ========================================================================= -->
<!-- LANGKAH 3: LEMBAR VERIFIKASI & KONFIRMASI DATA ASET KEMITRAAN (AKUN 1.5.2) -->
<!-- ========================================================================= -->
<div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <!-- 1. Header Banner Langkah 3 -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-400/10 text-cyan-300 border border-cyan-400/20 text-xs font-bold mb-2">
                <span>🛡️ LANGKAH 3 DARI 3: VERIFIKASI &amp; KONFIRMASI DATA</span>
            </div>
            <h2 class="text-lg sm:text-xl font-extrabold text-white flex items-center space-x-2">
                <span class="p-2 rounded-xl bg-cyan-400/10 text-cyan-400 text-sm">📋</span>
                <span>Langkah 3: Lembar Verifikasi Data Aset Kemitraan</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Tinjau kembali seluruh data legalitas PKS, klasifikasi akun 1.5.2, rincian fisik aset, serta estimasi nilai wajar sebelum disimpan resmi ke database SIMAT-RK.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
            <button type="button" @click="goToStep(2)"
                class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-700">
                <span>✏️ Edit Data (Langkah 2)</span>
            </button>
        </div>
    </div>

    <!-- ===== BANNER ERROR INLINE LANGKAH 3 ===== -->
    <template x-if="stepErrors[3]">
        <div class="flex items-start gap-3 p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 shadow-lg shadow-rose-500/10">
            <span class="text-rose-400 text-lg mt-0.5 shrink-0">⚠️</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-rose-300 mb-0.5">Perhatian — Verifikasi Langkah 3 Diperlukan</p>
                <p class="text-xs text-rose-200/90 leading-relaxed" x-text="stepErrors[3]"></p>
            </div>
            <button type="button" @click="clearStepError(3)" class="ml-auto shrink-0 text-rose-400 hover:text-rose-200 transition-colors text-sm leading-none">✕</button>
        </div>
    </template>

    <!-- 2. Quick Health Check Status (Grid 3 Kolom Indikator Utama) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
        <!-- Status 1: Mitra & Dokumen PKS -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">1. PKS &amp; MITRA REKANAN</span>
                <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20" x-text="formData.skema_kemitraan || 'Sewa'">
                </span>
            </div>
            <div class="text-xs font-black font-mono text-cyan-300 truncate" x-text="formData.nomor_pks || '-'"></div>
            <div class="text-[11px] text-white truncate font-bold" x-text="formData.mitra_nama || 'Mitra belum diisi'"></div>
        </div>

        <!-- Status 2: Klasifikasi 108 & Akun 1.5.2 -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-purple-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider">2. KLASIFIKASI AKUN 1.5.2</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20 font-mono">
                    Permendagri 108
                </span>
            </div>
            <div class="text-xs font-black font-mono text-purple-300 truncate" x-text="selectedKode108 || '-'"></div>
            <div class="text-[11px] text-slate-200 truncate font-semibold" x-text="formData.nama_barang || '-'"></div>
        </div>

        <!-- Status 3: Nilai Taksiran Wajar & Volume Fisik -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-emerald-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">3. NILAI &amp; VOLUME TOTAL</span>
                <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-mono">
                    <span x-text="formData.jumlah_volume"></span> <span x-text="formData.satuan"></span>
                </span>
            </div>
            <div class="text-xs sm:text-sm font-black font-mono text-emerald-400 truncate" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
            <div class="text-[10.5px] text-slate-400 truncate">
                Taksiran per <span x-text="formData.satuan || 'Unit'"></span>: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></span>
            </div>
        </div>
    </div>

    <!-- 3. Rincian Komprehensif Lembar Verifikasi Data (Tabulasi Kartu) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- ===================================================================== -->
        <!-- KARTU 1: LEGALITAS PERJANJIAN KERJA SAMA (PKS) & MITRA REKANAN        -->
        <!-- ===================================================================== -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-slate-800 shadow-xl space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs font-bold border border-cyan-500/30">1</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Legalitas PKS &amp; Rekanan Mitra
                    </h3>
                </div>
                <button type="button" @click="goToStep(1)" class="text-[11px] font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 cursor-pointer">
                    <span>Ubah</span> &rarr;
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nama Perusahaan Mitra:</span>
                    <span class="font-extrabold text-white block truncate" x-text="formData.mitra_nama || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pejabat Mitra / Direktur:</span>
                    <span class="font-bold text-cyan-300 block truncate" x-text="formData.mitra_pimpinan || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor Perjanjian (PKS):</span>
                    <span class="font-mono font-bold text-cyan-300 block truncate" x-text="formData.nomor_pks || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tanggal Dokumen PKS:</span>
                    <span class="font-semibold text-white block" x-text="formData.tanggal_pks || '-'"></span>
                </div>
                <div class="col-span-2">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Alamat Domisili Mitra:</span>
                    <span class="text-slate-200 block truncate" x-text="formData.mitra_alamat || '-'"></span>
                </div>
                <!-- Objek Aset BMD RSUD yang Dikerjasamakan -->
                <div class="col-span-2 p-3 rounded-2xl bg-slate-900/90 border border-cyan-500/20">
                    <span class="text-[10px] font-semibold text-cyan-400 block mb-1">🏛️ Objek Aset BMD RSUD yang Dikerjasamakan:</span>
                    <template x-if="formData.objek_nibar">
                        <div class="flex items-center justify-between text-xs gap-2">
                            <div>
                                <span class="font-extrabold text-white" x-text="formData.objek_aset_terpilih?.nama_barang || 'Aset BMD Terpilih'"></span>
                                <span class="text-cyan-400 font-mono text-[11px] block" x-text="'NIBAR: ' + formData.objek_nibar + ' · ' + (formData.objek_aset_terpilih?.kib || '')"></span>
                            </div>
                            <span class="text-[10px] text-slate-400 font-mono shrink-0" x-text="formData.objek_aset_terpilih?.unit_nama || ''"></span>
                        </div>
                    </template>
                    <template x-if="!formData.objek_nibar">
                        <span class="text-slate-500 text-xs italic">Tanpa penautan objek aset BMD spesifik (Penerimaan/Pengadaan Barang KSO Baru).</span>
                    </template>
                </div>
                <div class="col-span-2">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Berkas Dokumen BAST Kerja Sama:</span>
                    <template x-if="selectedFile">
                        <span class="text-emerald-400 font-mono text-xs flex items-center gap-1 font-semibold">
                            <span>📄</span>
                            <span x-text="selectedFile.name + ' (' + (selectedFile.size / 1024 / 1024).toFixed(2) + ' MB)'"></span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 font-bold ml-1">Siap Diunggah</span>
                        </span>
                    </template>
                    <template x-if="!selectedFile && formData.dokumen_path">
                        <span class="text-cyan-400 font-mono text-xs flex items-center gap-1">
                            <span>📄</span>
                            <span x-text="formData.dokumen_path.split('/').pop()"></span>
                            <span class="text-[10px] px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-300 font-bold ml-1">Tersimpan</span>
                        </span>
                    </template>
                    <template x-if="!selectedFile && !formData.dokumen_path">
                        <span class="text-slate-500 text-xs italic">Tidak ada berkas yang diunggah.</span>
                    </template>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Bentuk / Skema Kemitraan:</span>
                    <span class="px-2.5 py-0.5 rounded-lg bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 font-bold inline-block text-[11px]" x-text="formData.skema_kemitraan || 'Sewa'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Pengelolaan Konsesi:</span>
                    <span class="font-bold text-emerald-400 flex items-center gap-1.5 text-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Aktif Operasional</span>
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Sub-Akun Neraca:</span>
                    <span class="font-mono text-cyan-300 font-bold block text-xs">1.5.2 Kemitraan</span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Periode Berlaku Kerja Sama:</span>
                    <span class="font-mono text-slate-200 block text-[11.5px]" x-text="formData.tanggal_mulai && formData.tanggal_selesai ? (formData.tanggal_mulai + ' s/d ' + formData.tanggal_selesai) : 'Sesuai masa operasional PKS'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tahun Pembukuan &amp; Periode:</span>
                    <span class="font-mono font-bold text-white block text-[11.5px]" x-text="(formData.tahun_perolehan || '{{ date('Y') }}') + ' • ' + (formData.triwulan || 'TW I')"></span>
                </div>
                <div class="col-span-2 pt-2 border-t border-slate-900">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Catatan / Keterangan Tambahan:</span>
                    <p class="text-[11px] text-slate-300 italic" x-text="formData.kemitraan_keterangan || 'Tidak ada catatan tambahan.'"></p>
                </div>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 2: KLASIFIKASI KODE BARANG 108 & PENILAIAN ASET                 -->
        <!-- ===================================================================== -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-slate-800 shadow-xl space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-xs font-bold border border-cyan-500/30">2</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Klasifikasi 108 &amp; Nilai Taksiran Wajar
                    </h3>
                </div>
                <button type="button" @click="goToStep(2)" class="text-[11px] font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 cursor-pointer">
                    <span>Ubah</span> &rarr;
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="col-span-2">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Klasifikasi Akun 1.5.2 (Permendagri 108):</span>
                    <span class="font-mono font-extrabold text-cyan-300 text-xs block" x-text="selectedSubSub ? (selectedSubSub.kode + ' • ' + selectedSubSub.nama) : (selectedKode108 || '-')"></span>
                </div>
                <div class="col-span-2">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nama Spesifik Barang Aset:</span>
                    <span class="font-extrabold text-white text-sm block" x-text="formData.nama_barang || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Jumlah Volume Fisik:</span>
                    <span class="font-mono font-bold text-white text-xs block" x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Satuan Barang:</span>
                    <span class="font-semibold text-slate-200 block" x-text="formData.satuan || 'Unit'"></span>
                </div>
                <div class="col-span-2 p-3 rounded-2xl bg-cyan-950/20 border border-cyan-500/30 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-cyan-400 block uppercase">Total Taksiran Nilai Wajar Aset:</span>
                        <div class="text-base font-black font-mono text-cyan-300" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
                    </div>
                    <div>
                        <span x-show="(isMesin || isLainnya) && (formData.jumlah_volume || 1) > 1" class="text-[11px] font-mono text-slate-400 font-semibold" x-text="'@ Rp ' + formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></span>
                        <span x-show="isTanah || isGedung || isJaringan" class="text-[10px] font-bold text-emerald-400 bg-emerald-500/15 px-2.5 py-1 rounded-lg border border-emerald-500/30">Lump-sum (Nilai Keseluruhan)</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 3: SPESIFIKASI TEKNIS FISIK BARANG (MENYESUAIKAN LEMBAR KIB)    -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-2 p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-slate-800 shadow-xl space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-xs font-bold border border-purple-500/30">3</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Rincian Penjelasan Spesifikasi Fisik
                    </h3>
                </div>
                <span class="text-[10px] font-mono font-bold text-purple-300 bg-purple-950/60 px-2.5 py-0.5 rounded-lg border border-purple-500/30" x-text="kibLabel">
                </span>
            </div>

            <!-- Tampilan Spesifikasi Fisik Jika TANAH (KIB A) -->
            <template x-if="isTanah">
                <div class="space-y-3">
                    <template x-if="formData.tanah_items && formData.tanah_items.length > 1">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Rincian <strong class="text-white" x-text="formData.tanah_items.length"></strong> Bidang Tanah:</span>
                                <span class="font-mono text-emerald-400 font-bold" x-text="'Total: ' + (formData.tanah_luas_m2 || 0) + ' m²'"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(tItem, tIdx) in formData.tanah_items" :key="tIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-emerald-500/40 transition-all space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono text-[10px] font-bold flex items-center justify-center border border-emerald-500/30" x-text="tIdx + 1"></span>
                                                <span class="font-bold text-white text-xs" x-text="tItem.tanah_nama_barang || ('Bidang Tanah #' + (tIdx + 1))"></span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-emerald-950/60 text-emerald-300 border border-emerald-500/30" x-text="(tItem.tanah_luas_m2 || 0) + ' m²'"></span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Hak &amp; Sertifikat:</span>
                                                <span class="font-semibold text-cyan-300 block truncate" x-text="(tItem.tanah_hak || 'Hak Pakai') + (tItem.tanah_sertifikat_no ? ' • ' + tItem.tanah_sertifikat_no : '')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Keterangan:</span>
                                                <span class="text-slate-300 block truncate" x-text="tItem.tanah_penggunaan || '-'"></span>
                                            </div>
                                            <div class="col-span-2 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(tItem.tanah_nilai_satuan || 0)"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="!formData.tanah_items || formData.tanah_items.length <= 1">
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Tanah:</span>
                                <span class="font-mono font-bold text-emerald-400 text-sm block" x-text="(formData.tanah_luas_m2 || 0) + ' m²'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Hak Penguasaan:</span>
                                <span class="font-bold text-white block" x-text="formData.tanah_hak || (formData.tanah_items?.[0]?.tanah_hak || 'Hak Pakai')"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor &amp; Tanggal Sertifikat:</span>
                                <span class="font-mono text-cyan-300 block" x-text="(formData.tanah_sertifikat_no || formData.tanah_items?.[0]?.tanah_sertifikat_no || '-') + ((formData.tanah_sertifikat_tgl || formData.tanah_items?.[0]?.tanah_sertifikat_tgl) ? (' (Tgl: ' + (formData.tanah_sertifikat_tgl || formData.tanah_items?.[0]?.tanah_sertifikat_tgl) + ')') : '')"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Keterangan:</span>
                                <span class="font-semibold text-white block" x-text="formData.tanah_penggunaan || formData.tanah_items?.[0]?.tanah_penggunaan || '-'"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Letak / Alamat Lahan:</span>
                                <span class="text-slate-300 block text-[11px]" x-text="formData.tanah_alamat || formData.tanah_items?.[0]?.tanah_alamat || formData.alamat_barang || '-'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika PERALATAN & MESIN (KIB B) -->
            <template x-if="isMesin">
                <div class="space-y-3">
                    <template x-if="formData.mesin_items && formData.mesin_items.length > 1">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Rincian <strong class="text-white" x-text="formData.mesin_items.length"></strong> Varian / Unit Barang:</span>
                                <span class="font-mono text-cyan-400 font-bold" x-text="'Total: ' + totalVolumeMesin + ' Unit'"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(mItem, mIdx) in formData.mesin_items" :key="mIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-purple-500/40 transition-all space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 font-mono text-[10px] font-bold flex items-center justify-center border border-purple-500/30" x-text="mIdx + 1"></span>
                                                <span class="font-bold text-white text-xs truncate max-w-[200px]" x-text="mItem.mesin_nama_barang || ('Barang #' + (mIdx + 1))"></span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-800 text-purple-300 border border-slate-700 shrink-0" x-text="(mItem.mesin_jumlah_barang || 1) + ' ' + (mItem.mesin_satuan || 'Unit')"></span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Merk / Tipe:</span>
                                                <span class="font-semibold text-cyan-300 truncate block" x-text="(mItem.mesin_merk || '-') + ' ' + (mItem.mesin_type || '')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">No Seri Pabrik:</span>
                                                <span class="font-mono text-slate-300 truncate block" x-text="mItem.mesin_no_pabrik || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Kondisi:</span>
                                                <span class="font-bold block" :class="mItem.mesin_kondisi === 'Baik' ? 'text-emerald-400' : 'text-amber-400'" x-text="mItem.mesin_kondisi || 'Baik'"></span>
                                            </div>
                                            <div class="col-span-2 sm:col-span-3 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(getMesinSubtotal(mItem))"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="!formData.mesin_items || formData.mesin_items.length <= 1">
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Merk / Pabrikan:</span>
                                <span class="font-bold text-white block" x-text="formData.merk || (formData.mesin_items?.[0]?.mesin_merk || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tipe / Model:</span>
                                <span class="font-bold text-cyan-300 block" x-text="formData.type || (formData.mesin_items?.[0]?.mesin_type || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor Seri Pabrik (S/N):</span>
                                <span class="font-mono text-slate-200 block" x-text="formData.no_pabrik || (formData.mesin_items?.[0]?.mesin_no_pabrik || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Bahan / Material:</span>
                                <span class="text-slate-200 block" x-text="formData.bahan || (formData.mesin_items?.[0]?.mesin_bahan || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Ukuran / Kapasitas:</span>
                                <span class="text-slate-200 block" x-text="formData.ukuran || (formData.mesin_items?.[0]?.mesin_ukuran || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tahun Pembuatan:</span>
                                <span class="font-mono text-slate-200 block" x-text="formData.tahun_pembuatan || (formData.mesin_items?.[0]?.mesin_tahun_pembuatan || '-')"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Keterangan:</span>
                                <span class="text-slate-200 block" x-text="formData.mesin_items?.[0]?.mesin_keterangan || '-'"></span>
                            </div>
                            <template x-if="formData.no_rangka || formData.no_mesin || formData.no_polisi || formData.mesin_items?.[0]?.mesin_no_rangka">
                                <div class="col-span-2 pt-2 border-t border-slate-900">
                                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Identitas Kendaraan:</span>
                                    <span class="font-mono text-[11px] text-cyan-300 block" x-text="'Rangka: ' + (formData.no_rangka || formData.mesin_items?.[0]?.mesin_no_rangka || '-') + ' • Mesin: ' + (formData.no_mesin || formData.mesin_items?.[0]?.mesin_no_mesin || '-') + ' • Plat: ' + (formData.no_polisi || formData.mesin_items?.[0]?.mesin_no_polisi || '-')"></span>
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika GEDUNG & BANGUNAN (KIB C) -->
            <template x-if="isGedung">
                <div class="space-y-3">
                    <template x-if="formData.gedung_items && formData.gedung_items.length > 1">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Rincian <strong class="text-white" x-text="formData.gedung_items.length"></strong> Bangunan / Gedung:</span>
                                <span class="font-mono text-blue-400 font-bold" x-text="'Total: ' + totalVolumeGedung + ' Bangunan'"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(gItem, gIdx) in formData.gedung_items" :key="gIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-blue-500/40 transition-all space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-300 font-mono text-[10px] font-bold flex items-center justify-center border border-blue-500/30" x-text="gIdx + 1"></span>
                                                <span class="font-bold text-white text-xs truncate max-w-[200px]" x-text="gItem.gedung_nama_barang || ('Bangunan #' + (gIdx + 1))"></span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-800 text-blue-300 border border-slate-700 shrink-0" x-text="(gItem.gedung_jumlah_bangunan || 1) + ' ' + (gItem.gedung_satuan || 'Gedung')"></span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Luas Lantai:</span>
                                                <span class="font-semibold text-blue-300 block" x-text="(gItem.gedung_luas_lantai || 0) + ' m²'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Konstruksi:</span>
                                                <span class="text-slate-300 block" x-text="(gItem.gedung_bertingkat === 'Bertingkat' ? 'Bertingkat' : '1 Lantai') + ' • ' + (gItem.gedung_beton || 'Beton')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Kondisi:</span>
                                                <span class="font-bold block text-emerald-400" x-text="gItem.gedung_kondisi || 'Baik'"></span>
                                            </div>
                                            <div class="col-span-2 sm:col-span-3 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(getGedungSubtotal(gItem))"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="!formData.gedung_items || formData.gedung_items.length <= 1">
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Bertingkat:</span>
                                <span class="font-bold text-white block" x-text="formData.gedung_bertingkat || (formData.gedung_items?.[0]?.gedung_bertingkat || 'Tidak')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Beton / Rangka:</span>
                                <span class="font-bold text-white block" x-text="formData.gedung_beton || (formData.gedung_items?.[0]?.gedung_beton || 'Beton Bertulang')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Total Lantai:</span>
                                <span class="font-mono font-bold text-blue-400 text-sm block" x-text="(formData.gedung_luas_lantai || formData.gedung_items?.[0]?.gedung_luas_lantai || 0) + ' m²'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Tanah Tempat Berdiri:</span>
                                <span class="text-slate-200 block" x-text="formData.gedung_status_tanah || (formData.gedung_items?.[0]?.gedung_status_tanah || 'Tanah Milik RSUD')"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dokumen IMB / PBG / SLF:</span>
                                <span class="font-mono text-cyan-300 block" x-text="(formData.gedung_dokumen_no || formData.gedung_items?.[0]?.gedung_dokumen_no || '-') + ((formData.gedung_dokumen_tgl || formData.gedung_items?.[0]?.gedung_dokumen_tgl) ? (' (Tgl: ' + (formData.gedung_dokumen_tgl || formData.gedung_items?.[0]?.gedung_dokumen_tgl) + ')') : '')"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Keterangan:</span>
                                <span class="text-slate-200 block font-semibold" x-text="formData.gedung_fungsi || (formData.gedung_items?.[0]?.gedung_fungsi || '-')"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika JALAN & JARINGAN (KIB D) -->
            <template x-if="isJaringan">
                <div class="space-y-3">
                    <template x-if="formData.jaringan_items && formData.jaringan_items.length > 1">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Rincian <strong class="text-white" x-text="formData.jaringan_items.length"></strong> Ruas Jaringan:</span>
                                <span class="font-mono text-teal-400 font-bold" x-text="'Total: ' + totalVolumeJaringan + ' Ruas'"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(jItem, jIdx) in formData.jaringan_items" :key="jIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-teal-500/40 transition-all space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-teal-500/20 text-teal-300 font-mono text-[10px] font-bold flex items-center justify-center border border-teal-500/30" x-text="jIdx + 1"></span>
                                                <span class="font-bold text-white text-xs truncate max-w-[200px]" x-text="jItem.jaringan_nama_barang || ('Ruas #' + (jIdx + 1))"></span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-800 text-teal-300 border border-slate-700 shrink-0" x-text="(jItem.jaringan_jumlah || 1) + ' ' + (jItem.jaringan_satuan || 'Ruas')"></span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div class="col-span-2">
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Konstruksi:</span>
                                                <span class="font-semibold text-teal-300 block" x-text="(jItem.jaringan_konstruksi || '-') + ' • ' + (jItem.jaringan_beton || 'Beton') + ' (' + (jItem.jaringan_bertingkat === 'Bertingkat' ? 'Bertingkat' : 'Tidak Bertingkat') + ')'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Dimensi / Luas:</span>
                                                <span class="font-mono text-slate-300 block" x-text="jItem.jaringan_luas ? (jItem.jaringan_luas + ' m²') : ((jItem.jaringan_panjang || 0) + 'm x ' + (jItem.jaringan_lebar || 0) + 'm')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Kondisi:</span>
                                                <span class="font-bold block text-emerald-400" x-text="jItem.jaringan_kondisi || 'Baik'"></span>
                                            </div>
                                            <div class="col-span-2 sm:col-span-4 flex flex-wrap items-center justify-between pt-1 border-t border-slate-800/40 text-[10.5px]">
                                                <span class="text-slate-400" x-show="jItem.jaringan_status_tanah" x-text="'Tanah: ' + jItem.jaringan_status_tanah"></span>
                                                <span class="text-slate-400" x-show="jItem.jaringan_keterangan" x-text="'Ket: ' + jItem.jaringan_keterangan"></span>
                                                <span class="font-mono font-bold text-emerald-400 ml-auto" x-text="'Subtotal: Rp ' + formatRupiah(getJaringanSubtotal(jItem))"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="!formData.jaringan_items || formData.jaringan_items.length <= 1">
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Jaringan / Jalan:</span>
                                <span class="font-bold text-teal-300 block" x-text="(formData.jaringan_konstruksi || formData.jaringan_items?.[0]?.jaringan_konstruksi || '-') + ' • ' + (formData.jaringan_items?.[0]?.jaringan_beton || 'Beton') + ' (' + ((formData.jaringan_items?.[0]?.jaringan_bertingkat === 'Bertingkat') ? 'Bertingkat' : 'Tidak Bertingkat') + ')'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Total:</span>
                                <span class="font-mono text-white block" x-text="(formData.jaringan_luas || formData.jaringan_items?.[0]?.jaringan_luas) ? ((formData.jaringan_luas || formData.jaringan_items?.[0]?.jaringan_luas) + ' m²') : '-'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dimensi (Panjang x Lebar):</span>
                                <span class="font-mono text-white block" x-text="(formData.jaringan_panjang || formData.jaringan_items?.[0]?.jaringan_panjang || 0) + ' m x ' + (formData.jaringan_lebar || formData.jaringan_items?.[0]?.jaringan_lebar || 0) + ' m'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Penguasaan Tanah:</span>
                                <span class="text-white block font-medium" x-text="formData.jaringan_items?.[0]?.jaringan_status_tanah || '-'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kode Aset Tanah:</span>
                                <span class="text-emerald-400 font-mono block" x-text="formData.jaringan_items?.[0]?.jaringan_kode_aset_tanah || '-'"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dokumen Kontrak Teknis:</span>
                                <span class="font-mono text-cyan-300 block" x-text="(formData.jaringan_dokumen_no || formData.jaringan_items?.[0]?.jaringan_dokumen_no || '-') + ((formData.jaringan_dokumen_tgl || formData.jaringan_items?.[0]?.jaringan_dokumen_tgl) ? (' (Tgl: ' + (formData.jaringan_dokumen_tgl || formData.jaringan_items?.[0]?.jaringan_dokumen_tgl) + ')') : '')"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Keterangan:</span>
                                <span class="text-slate-200 block" x-text="formData.jaringan_items?.[0]?.jaringan_keterangan || '-'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika ASET TETAP LAINNYA (KIB E) -->
            <template x-if="isLainnya">
                <div class="space-y-3">
                    <template x-if="formData.lainnya_items && formData.lainnya_items.length > 1">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Rincian <strong class="text-white" x-text="formData.lainnya_items.length"></strong> Item Aset Tetap Lainnya:</span>
                                <span class="font-mono text-purple-400 font-bold" x-text="'Total: ' + totalVolumeLainnya + ' Item'"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(lItem, lIdx) in formData.lainnya_items" :key="lIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-purple-500/40 transition-all space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 font-mono text-[10px] font-bold flex items-center justify-center border border-purple-500/30" x-text="lIdx + 1"></span>
                                                <span class="font-bold text-white text-xs truncate max-w-[170px]" x-text="lItem.lainnya_judul || ('Item #' + (lIdx + 1))"></span>
                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-bold border"
                                                      :class="lItem.is_extracom ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                                      x-text="lItem.is_extracom ? '📦 Extracom' : '⚙️ Reguler'">
                                                </span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-800 text-purple-300 border border-slate-700 shrink-0" x-text="(lItem.lainnya_jumlah || 1) + ' ' + (lItem.lainnya_satuan || 'Buah')"></span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Kategori:</span>
                                                <span class="font-semibold text-purple-300 block" x-text="lItem.kib_e_type === 'kesenian' ? '🎨 Kesenian' : (lItem.kib_e_type === 'hewan_tumbuhan' ? '🌿 Hewan/Tanaman' : '📚 Buku Pustaka')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Pencipta / Spesifikasi:</span>
                                                <span class="text-slate-300 truncate block" x-text="lItem.lainnya_pencipta || lItem.lainnya_spesifikasi || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Kondisi:</span>
                                                <span class="font-bold block text-emerald-400" x-text="lItem.lainnya_kondisi || 'Baik'"></span>
                                            </div>
                                            <div class="col-span-2 sm:col-span-3 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(lItem))"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="!formData.lainnya_items || formData.lainnya_items.length <= 1">
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Judul Spesifik Aset:</span>
                                <span class="font-bold text-purple-300 block" x-text="formData.lainnya_judul || (formData.lainnya_items?.[0]?.lainnya_judul || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Jenis / Kategori:</span>
                                <span class="text-white block" x-text="formData.lainnya_jenis || (formData.lainnya_items?.[0]?.kib_e_type || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Ukuran / Dimensi:</span>
                                <span class="text-white block" x-text="formData.lainnya_ukuran || (formData.lainnya_items?.[0]?.lainnya_ukuran || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Bahan / Material:</span>
                                <span class="text-white block" x-text="formData.lainnya_bahan || (formData.lainnya_items?.[0]?.lainnya_bahan || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pencipta / Seniman:</span>
                                <span class="text-white block" x-text="formData.lainnya_pencipta || (formData.lainnya_items?.[0]?.lainnya_pencipta || '-')"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Keterangan:</span>
                                <span class="text-slate-200 block" x-text="formData.lainnya_items?.[0]?.lainnya_keterangan || formData.lainnya_items?.[0]?.lainnya_spesifikasi || '-'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

    </div>

    <!-- 4. Pejabat Pembuat Komitmen (PPK) & Pengesahan (Sesuai Gambar 2 Rekap Excel Kolom 24 & 25) -->
    <div class="p-6 rounded-3xl bg-slate-950/90 border border-cyan-500/30 space-y-5 shadow-2xl relative overflow-visible">
        <div class="absolute -right-10 -bottom-10 w-44 h-44 bg-cyan-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-500/30 shrink-0">
                    👔
                </span>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider flex items-center gap-2">
                        <span>Pejabat Pembuat Komitmen (PPK RSUD)</span>
                        <span class="text-rose-400">*</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pengesahan penanggung jawab komitmen pengadaan &amp; kemitraan RSUD Koesnandi (Kolom 24 &amp; 25)</p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-bold text-cyan-300 bg-cyan-400/10 px-2.5 py-1 rounded-lg border border-cyan-400/20 self-start sm:self-center shrink-0">
                Kolom 24 &amp; 25 Excel
            </span>
        </div>

        <!-- Riwayat Tersimpan PPK Cepat -->
        <template x-if="masterPpkList && masterPpkList.length > 0">
            <div class="flex flex-wrap items-center gap-2 pt-0.5">
                <span class="text-[10px] text-slate-400 font-semibold mr-1">Riwayat Tersimpan:</span>
                <template x-for="(p, pIdx) in masterPpkList.slice(0, 4)" :key="pIdx">
                    <button type="button" @click="selectPpk(p)"
                        class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10.5px] font-bold transition-all border cursor-pointer"
                        :class="formData.ppk_nama === p.nama 
                            ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 shadow-md shadow-cyan-500/20 ring-1 ring-cyan-400' 
                            : 'bg-slate-900 border-slate-700 text-slate-300 hover:border-cyan-500/50 hover:text-white'">
                        <span>👔</span>
                        <span x-text="p.nama"></span>
                        <span class="text-[9.5px] font-mono opacity-80" x-show="p.nip" x-text="'(' + p.nip + ')'"></span>
                    </button>
                </template>
            </div>
        </template>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Nama PPK (Kolom 24) dengan Filter Autocomplete & Auto-Fill NIP -->
            <div class="relative space-y-1.5" @click.away="isPpkDropdownOpen = false">
                <label class="block text-xs font-bold text-slate-200 flex items-center justify-between">
                    <span>Nama Lengkap Pejabat Pembuat Komitmen (PPK) <span class="text-rose-400">*</span></span>
                    <span class="text-[10px] text-cyan-400 font-mono">Kolom 24</span>
                </label>
                <div class="relative">
                    <input type="text" x-model="formData.ppk_nama"
                        @focus="isPpkDropdownOpen = true"
                        @input="isPpkDropdownOpen = true; onPpkInput($event.target.value)"
                        @change="onPpkInput($event.target.value)"
                        @keydown.escape="isPpkDropdownOpen = false"
                        autocomplete="off"
                        placeholder="Contoh: BUDI HARTONO, S.Sos"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 pr-10 text-xs text-white font-bold focus:outline-none placeholder-slate-500 shadow-inner">
                    <button type="button" 
                        x-show="formData.ppk_nama"
                        @click="formData.ppk_nama = ''; formData.ppk_nip = ''; isPpkDropdownOpen = true" 
                        title="Kosongkan"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                        class="rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                        ✕
                    </button>
                </div>

                <!-- Floating Dropdown Saran / Filter PPK -->
                <div x-show="isPpkDropdownOpen && filteredPpkList.length > 0" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    style="max-height: 220px !important; overflow-y: auto !important;"
                    class="absolute z-[9999] mt-1.5 w-full bg-slate-900 border border-cyan-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">
                    
                    <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                        <span>Pilih Riwayat Pejabat (PPK)</span>
                        <span class="font-mono text-slate-400" x-text="filteredPpkList.length + ' pejabat'"></span>
                    </div>

                    <template x-for="(p, pIdx) in filteredPpkList" :key="pIdx">
                        <div @click="selectPpk(p)"
                            class="px-4 py-2 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                            :class="formData.ppk_nama === p.nama ? 'bg-cyan-500/20 text-cyan-200' : 'text-slate-200'">
                            <div class="flex items-center gap-2.5 min-w-0">
                                <span class="text-xs text-cyan-400/80">👔</span>
                                <div class="min-w-0">
                                    <span class="text-xs font-bold group-hover:text-cyan-300 truncate block" x-text="p.nama"></span>
                                    <span class="text-[10px] text-cyan-400/80 font-mono truncate block" x-text="p.nip ? 'NIP: ' + p.nip : 'NIP belum terdata'"></span>
                                </div>
                            </div>
                            <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-bold shrink-0 group-hover:bg-cyan-500/25">
                                Pilih &amp; Auto-fill NIP ↵
                            </span>
                        </div>
                    </template>
                </div>

                <p class="text-[10px] text-slate-500 mt-1">Nama pejabat pembuat komitmen yang menandatangani berkas kontrak kerja sama.</p>
            </div>

            <!-- NIP PPK (Kolom 25) -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span>NIP Pejabat Pembuat Komitmen (PPK)</span>
                    <span class="text-[10px] text-cyan-400 font-mono">Kolom 25</span>
                </label>
                <div class="relative">
                    <input type="text" x-model="formData.ppk_nip"
                        placeholder="Contoh: 19760229 200801 1 010"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-cyan-300 font-mono focus:outline-none placeholder-slate-500 shadow-inner">
                    <button type="button" 
                        x-show="formData.ppk_nip"
                        @click="formData.ppk_nip = ''" 
                        title="Kosongkan"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                        class="rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                        ✕
                    </button>
                </div>
                <p class="text-[10px] text-slate-500 mt-1">Nomor Induk Pegawai (NIP) PPK bersangkutan (otomatis terisi bila memilih dari daftar).</p>
            </div>
        </div>
    </div>

    <!-- 5. Checklist Verifikasi Keabsahan Data & Tombol Finalisasi -->
    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/80 border border-slate-800 space-y-4 shadow-xl">
        <label class="flex items-start space-x-3 cursor-pointer select-none">
            <input type="checkbox" x-model="isDataVerified"
                class="mt-1 w-4 h-4 rounded text-cyan-500 bg-slate-900 border-slate-700 focus:ring-cyan-400 focus:ring-offset-slate-950 cursor-pointer">
            <div class="text-xs text-slate-300 leading-relaxed">
                <strong class="text-white block font-bold mb-0.5">Konfirmasi &amp; Validasi Data Aset Kemitraan:</strong>
                Saya menyatakan bahwa seluruh rincian perjanjian kerja sama (PKS), kodefikasi akun 1.5.2 Permendagri 108, taksiran nilai wajar, spesifikasi teknis fisik, serta unit penempatan KIR di atas telah diperiksa dengan cermat dan sesuai dengan dokumen aslinya.
            </div>
        </label>
    </div>

</div>
