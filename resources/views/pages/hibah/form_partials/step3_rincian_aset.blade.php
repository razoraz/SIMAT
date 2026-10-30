<!-- ========================================================================= -->
<!-- LANGKAH 3: LEMBAR VERIFIKASI & KONFIRMASI DATA PENERIMAAN ASET HIBAH       -->
<!-- Design: Bespoke Dark Amber Dashboard — Mirror Persis Kemitraan Langkah 3   -->
<!-- ========================================================================= -->
<div x-show="currentStep === 3" 
     x-transition:enter="transition ease-out duration-200" 
     x-transition:enter-start="opacity-0 translate-y-2" 
     x-transition:enter-end="opacity-100 translate-y-0" 
     class="space-y-6">
    
    <!-- 1. Header Banner Langkah 3 -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2 bg-amber-400/10 text-amber-300 border border-amber-400/20">
                <span>🛡️ LANGKAH 3 DARI 3: VERIFIKASI PENERIMAAN ASET HIBAH</span>
            </div>
            <h2 class="text-lg sm:text-xl font-extrabold text-white flex items-center space-x-2">
                <span class="p-2 rounded-xl text-sm bg-amber-400/10 text-amber-400">📋</span>
                <span>Langkah 3: Lembar Verifikasi Data Penerimaan Hibah Aset</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Tinjau kembali legalitas berita acara serah terima (BAST/NPHD), rincian pengadaan fisik dari instansi pemberi hibah, serta estimasi nilai wajar sebelum disimpan resmi ke inventaris RSUD.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
            <button type="button" @click="goToStep(2)"
                class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-700 cursor-pointer">
                <span>✏️ Edit Data (Langkah 2)</span>
            </button>
        </div>
    </div>

    <!-- 2. Quick Health Check Status (Grid 3 Kolom Indikator Utama) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
        <!-- Status 1: BAST & Pemberi Hibah -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-amber-500/30 shadow-lg space-y-1 relative overflow-hidden group transition-all">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400">1. BAST &amp; PEMBERI HIBAH</span>
                <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full border bg-amber-500/10 text-amber-300 border-amber-500/20">
                    HIBAH MASUK
                </span>
            </div>
            <div class="text-xs font-black font-mono truncate text-amber-300" x-text="formData.hibah_nomor_bast || '-'"></div>
            <div class="text-[11px] text-white truncate font-bold" x-text="formData.hibah_pemberi || 'Pemberi belum diisi'"></div>
        </div>

        <!-- Status 2: Klasifikasi 108 & Identitas Barang -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-purple-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider">2. KLASIFIKASI KODE 108</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20 font-mono">
                    Permendagri 108
                </span>
            </div>
            <div class="text-xs font-black font-mono text-purple-300 truncate" x-text="selectedSubSub?.kode || formData.jenis_aset_kode || '-'"></div>
            <div class="text-[11px] text-slate-200 truncate font-semibold" x-text="formData.nama_barang || '-'"></div>
        </div>

        <!-- Status 3: Nilai Taksiran Wajar & Volume Fisik -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-emerald-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">3. TAKSIRAN NILAI &amp; VOLUME</span>
                <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-mono">
                    <span x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></span>
                </span>
            </div>
            <div class="text-xs sm:text-sm font-black font-mono text-emerald-400 truncate" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
            <div class="text-[10.5px] text-slate-400 truncate">
                Rata-rata: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></span> / unit
            </div>
        </div>
    </div>

    <!-- 3. Rincian Komprehensif Lembar Verifikasi Data (Tabulasi Kartu) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- ===================================================================== -->
        <!-- KARTU 1: LEGALITAS BAST & IDENTITAS INSTANSI PEMBERI HIBAH           -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-2 p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-amber-500/30 shadow-xl space-y-4 relative transition-all">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold border bg-amber-500/20 text-amber-400 border-amber-500/30">1</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Legalitas Berita Acara Serah Terima (BAST / NPHD) &amp; Pemberi Hibah
                    </h3>
                </div>
                <button type="button" @click="goToStep(1)" class="text-[11px] font-bold flex items-center gap-1 text-amber-400 hover:text-amber-300 cursor-pointer">
                    <span>Ubah</span> &rarr;
                </button>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 text-xs">
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nama Instansi Pemberi Hibah:</span>
                    <span class="font-bold text-white text-sm block" x-text="formData.hibah_pemberi || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pimpinan / Pejabat Penyerah:</span>
                    <span class="font-medium text-slate-200 block" x-text="formData.hibah_pimpinan || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Alamat / Lokasi Pemberi Hibah:</span>
                    <span class="text-slate-300 block text-[11px] truncate" x-text="formData.hibah_alamat_pemberi || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor Berkas Dokumen BAST:</span>
                    <span class="font-mono text-amber-300 font-extrabold block text-xs" x-text="formData.hibah_nomor_bast || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tanggal Penandatanganan BAST:</span>
                    <span class="font-mono text-slate-200 block text-xs" x-text="formatTanggalIndo(formData.hibah_tanggal_bast)"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tahun Pembukuan &amp; Triwulan:</span>
                    <span class="font-mono font-bold text-white block text-xs" x-text="(formData.tahun_perolehan || '{{ date('Y') }}') + ' • ' + (formData.triwulan || 'TW I')"></span>
                </div>

                <div class="sm:col-span-2 lg:col-span-2">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Berkas Dokumen Lampiran BAST:</span>

                    {{-- 1. Berkas Baru Dipilih --}}
                    <template x-if="selectedFile">
                        <span class="text-emerald-400 font-mono text-xs inline-flex items-center gap-1.5 font-semibold flex-wrap">
                            <span>📄</span>
                            <span x-text="selectedFile.name + ' (' + (selectedFile.size / 1024 / 1024).toFixed(2) + ' MB)'"></span>
                            <span class="text-[9.5px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/30">
                                <span x-show="existingDokumenPath">⚡ Menggantikan Berkas Lama</span>
                                <span x-show="!existingDokumenPath">✓ Siap Diunggah</span>
                            </span>
                        </span>
                    </template>

                    {{-- 2. Berkas Lama Tersimpan (Tidak diganti & tidak dihapus) --}}
                    <template x-if="!selectedFile && existingDokumenPath && !removeExistingDokumen">
                        <span class="text-amber-300 font-mono text-xs inline-flex items-center gap-1.5 font-semibold flex-wrap">
                            <span>📄</span>
                            <span x-text="getExistingFileName()"></span>
                            <span class="text-[9.5px] px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 font-bold">
                                ✓ Tersimpan di Arsip
                            </span>
                            <a :href="'/storage/' + existingDokumenPath" target="_blank"
                                class="text-cyan-300 hover:text-cyan-200 hover:underline text-[11px] font-sans font-bold inline-flex items-center gap-0.5 ml-1">
                                <span>Lihat Berkas</span>
                                <span>↗</span>
                            </a>
                        </span>
                    </template>

                    {{-- 3. Berkas Lama Ditandai untuk Dihapus --}}
                    <template x-if="!selectedFile && existingDokumenPath && removeExistingDokumen">
                        <span class="text-rose-400 text-xs inline-flex items-center gap-1 font-semibold">
                            <span>⚠️</span>
                            <span>Berkas lama (<span class="font-mono" x-text="getExistingFileName()"></span>) ditandai untuk dihapus saat disimpan.</span>
                        </span>
                    </template>

                    {{-- 4. Benar-benar Tanpa Berkas --}}
                    <template x-if="!selectedFile && !existingDokumenPath">
                        <span class="text-slate-500 text-xs italic">Tanpa berkas lampiran (opsional).</span>
                    </template>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kategori / Akun Perolehan:</span>
                    <span class="font-mono text-amber-300 font-bold block text-xs">Penerimaan Hibah Aset (1.3.x)</span>
                </div>

                <div class="sm:col-span-2 lg:col-span-3">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Catatan / Keterangan Penyerahan:</span>
                    <p class="text-[11px] text-slate-300 italic" x-text="formData.hibah_keterangan || 'Tidak ada catatan tambahan.'"></p>
                </div>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 2: KLASIFIKASI KODE BARANG 108 & PENILAIAN ASET (KOLOM KIRI)    -->
        <!-- ===================================================================== -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-amber-500/30 shadow-xl space-y-4 relative transition-all">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold border bg-amber-500/20 text-amber-400 border-amber-500/30">2</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Klasifikasi 108 &amp; Nilai Taksiran Wajar
                    </h3>
                </div>
                <button type="button" @click="goToStep(2)" class="text-[11px] font-bold flex items-center gap-1 text-amber-400 hover:text-amber-300 cursor-pointer">
                    <span>Ubah</span> &rarr;
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="col-span-2">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Klasifikasi Kode Permendagri 108:</span>
                    <span class="font-mono font-extrabold text-amber-300 text-xs block" x-text="selectedSubSub ? (selectedSubSub.kode + ' • ' + selectedSubSub.nama) : (formData.jenis_aset_kode || '-')"></span>
                </div>
                <div class="col-span-2">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nama Lengkap Barang Hibah:</span>
                    <span class="font-bold text-white text-sm block" x-text="formData.nama_barang || '-'"></span>
                </div>
                <template x-if="formData.kode_rek">
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Rekening Belanja Terkait:</span>
                        <span class="font-mono text-cyan-300 block text-xs" x-text="formData.kode_rek + ' - ' + (formData.nama_belanja || '')"></span>
                    </div>
                </template>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kelompok Lembar KIB:</span>
                    <span class="font-bold text-amber-400 block" x-text="kibLabel"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kategori Pencatatan:</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold border inline-block"
                          :class="formData.is_extracomtable ? 'bg-amber-500/20 text-amber-300 border-amber-500/40' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'"
                          x-text="formData.is_extracomtable ? '📦 Ekstrakomtabel (≤ 300rb)' : '✅ Aset Tetap Reguler (Kapitalisasi)'">
                    </span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Total Volume / Kuantitas Fisik:</span>
                    <span class="font-mono font-extrabold text-white text-sm block" x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Total Taksiran Nilai Wajar:</span>
                    <div class="text-emerald-400 font-mono font-extrabold text-sm block">
                        Rp <span x-text="formatRupiah(formData.total_realisasi)"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 3: SPESIFIKASI TEKNIS FISIK BARANG (KOLOM KANAN BERSEBELAHAN)  -->
        <!-- ===================================================================== -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-slate-800 shadow-xl space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-xs font-bold border border-purple-500/30">3</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Rincian Penjelasan Spesifikasi Fisik
                    </h3>
                </div>
                <span class="text-[10px] font-mono font-bold text-amber-300 bg-amber-950/60 px-2.5 py-0.5 rounded-lg border border-amber-500/30" x-text="kibLabel">
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
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-amber-500/40 transition-all space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[10px] font-bold flex items-center justify-center border border-amber-500/30" x-text="tIdx + 1"></span>
                                                <span class="font-bold text-white text-xs" x-text="tItem.tanah_nama_barang || ('Bidang Tanah #' + (tIdx + 1))"></span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-amber-950/60 text-amber-300 border border-amber-500/30" x-text="(tItem.tanah_luas_m2 || 0) + ' m²'"></span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Hak &amp; Sertifikat:</span>
                                                <span class="font-semibold text-amber-300 block truncate" x-text="(tItem.tanah_hak || 'Hak Pakai') + (tItem.tanah_sertifikat_no ? ' • ' + tItem.tanah_sertifikat_no : '')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Penggunaan Lahan:</span>
                                                <span class="text-slate-300 block truncate" x-text="tItem.tanah_penggunaan || '-'"></span>
                                            </div>
                                            <div class="col-span-2 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(tItem.tanah_nilai_fisik || 0)"></span>
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
                                <span class="font-mono text-amber-300 block" x-text="(formData.tanah_sertifikat_no || formData.tanah_items?.[0]?.tanah_sertifikat_no || '-') + ((formData.tanah_sertifikat_tgl || formData.tanah_items?.[0]?.tanah_sertifikat_tgl) ? (' (Tgl: ' + (formData.tanah_sertifikat_tgl || formData.tanah_items?.[0]?.tanah_sertifikat_tgl) + ')') : '')"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Penggunaan Lahan:</span>
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
                                <span class="font-mono text-amber-400 font-bold" x-text="'Total: ' + totalVolumeMesin + ' Unit'"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(mItem, mIdx) in formData.mesin_items" :key="mIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-amber-500/40 transition-all space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[10px] font-bold flex items-center justify-center border border-amber-500/30" x-text="mIdx + 1"></span>
                                                <span class="font-bold text-white text-xs truncate max-w-[200px]" x-text="mItem.mesin_nama_barang || ('Barang #' + (mIdx + 1))"></span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-800 text-amber-300 border border-slate-700 shrink-0" x-text="(mItem.mesin_jumlah_barang || 1) + ' ' + (mItem.mesin_satuan || 'Unit')"></span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Merk / Tipe:</span>
                                                <span class="font-semibold text-amber-300 truncate block" x-text="(mItem.mesin_merk || '-') + ' ' + (mItem.mesin_type || '')"></span>
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
                                <span class="font-semibold text-amber-300 block" x-text="formData.type || (formData.mesin_items?.[0]?.mesin_type || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor Pabrik / Seri:</span>
                                <span class="font-mono text-slate-200 block" x-text="formData.no_pabrik || (formData.mesin_items?.[0]?.mesin_no_pabrik || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Ukuran / Konstruksi:</span>
                                <span class="text-slate-300 block" x-text="formData.ukuran || (formData.mesin_items?.[0]?.mesin_ukuran || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Bahan Material:</span>
                                <span class="text-slate-300 block" x-text="formData.bahan || (formData.mesin_items?.[0]?.mesin_bahan || '-')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kondisi Fisik:</span>
                                <span class="font-bold text-emerald-400 block" x-text="formData.kondisi || (formData.mesin_items?.[0]?.mesin_kondisi || 'Baik')"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika GEDUNG & BANGUNAN (KIB C) -->
            <template x-if="isGedung">
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Fisik Bangunan:</span>
                            <span class="font-bold text-amber-400 block" x-text="(formData.gedung_items?.[0]?.gedung_is_baru || 'Baru')"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Bertingkat:</span>
                            <span class="font-bold text-white block" x-text="(formData.gedung_items?.[0]?.gedung_bertingkat || 'Bertingkat')"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Beton:</span>
                            <span class="font-bold text-white block" x-text="(formData.gedung_items?.[0]?.gedung_beton || 'Beton')"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Total Lantai:</span>
                            <span class="font-mono font-bold text-amber-400 text-sm block" x-text="(formData.gedung_items?.[0]?.gedung_luas_m2 || 0) + ' m²'"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Tanah Tempat Berdiri:</span>
                            <span class="text-slate-200 block" x-text="(formData.gedung_items?.[0]?.gedung_status_tanah || 'Tanah Hak Pakai RSUD')"></span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika JALAN, IRIGASI & JARINGAN (KIB D) -->
            <template x-if="isJaringan">
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="col-span-2">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Jaringan / Jalan:</span>
                            <span class="font-bold text-amber-300 block" x-text="(formData.jaringan_items?.[0]?.jaringan_konstruksi || '-') + ' • ' + (formData.jaringan_items?.[0]?.jaringan_beton || 'Beton')"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dimensi (P x L):</span>
                            <span class="font-mono text-white block" x-text="(formData.jaringan_items?.[0]?.jaringan_panjang_m || 0) + 'm x ' + (formData.jaringan_items?.[0]?.jaringan_lebar_m || 0) + 'm'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Total:</span>
                            <span class="font-mono text-amber-400 font-bold block" x-text="(formData.jaringan_items?.[0]?.jaringan_luas_m2 || 0) + ' m²'"></span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika ASET LAINNYA (KIB E) -->
            <template x-if="isAsetLainnya">
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="col-span-2">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Sub-Tipe KIB E:</span>
                            <span class="font-bold text-amber-300 uppercase block" x-text="formData.lainnya_items?.[0]?.kib_e_sub_type || 'Buku / Kepustakaan'"></span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Judul / Spesifikasi / Pencipta:</span>
                            <span class="text-white block" x-text="(formData.lainnya_items?.[0]?.lainnya_buku_judul || formData.lainnya_items?.[0]?.lainnya_nama_barang || '-') + ' • ' + (formData.lainnya_items?.[0]?.lainnya_buku_pencipta || '-')"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 4: PENEMPATAN RUANGAN (KIR), KONDISI & PEJABAT PPK RSUD         -->
        <!-- ===================================================================== -->
        <!-- 4. Pejabat Pembuat Komitmen (PPK) & Pengesah Penerimaan RSUD          -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-2 p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-amber-500/30 shadow-xl space-y-5 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2.5">
                    <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold border bg-amber-500/20 text-amber-400 border-amber-500/30">4</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Penempatan Ruangan (KIR) &amp; Pejabat Pengesah RSUD
                    </h3>
                </div>
                <span class="text-[10px] font-mono font-bold text-amber-400/90 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg">
                    Pengesahan Naskah BAST
                </span>
            </div>

            <!-- Pilihan Penempatan Ruangan (KIR) Opsional -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                        <span>🏢 Unit / Ruangan Penempatan Aset (KIR)</span>
                    </label>
                    <span class="text-[10px] text-amber-400 font-mono font-bold bg-amber-500/10 border border-amber-500/25 px-2 py-0.5 rounded-md whitespace-nowrap shrink-0">
                        Opsional
                    </span>
                </div>
                <select x-model="formData.unit_id"
                    class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none font-semibold transition-colors">
                    <option value="">-- Belum Ditempatkan (Menunggu Distribusi Barang) --</option>
                    @foreach($dbUnits ?? [] as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->kode_unit ?? 'Unit' }})</option>
                    @endforeach
                </select>
                <p class="text-[10.5px] text-slate-400 leading-tight">
                    Biarkan kosong jika barang hibah baru tiba di RSUD dan menunggu proses distribusi ke ruangan/paviliun. Jika sudah ada ruangan penempatan definitif, silakan pilih unit di atas.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-start">
                <!-- Nama PPK dengan Filter Autocomplete & Auto-Fill NIP -->
                <div class="relative space-y-2" @click.away="isPpkDropdownOpen = false">
                    <div class="h-6 flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-200 flex items-center gap-1">
                            <span>Nama Pejabat (PPK) / Pengesah</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[10px] text-amber-400 font-mono font-bold bg-amber-500/10 border border-amber-500/25 px-2 py-0.5 rounded-md whitespace-nowrap shrink-0">
                            PPK RSUD
                        </span>
                    </div>
                    <div class="relative">
                        <input type="text" x-model="formData.ppk_nama"
                            @focus="isPpkDropdownOpen = true"
                            @input="isPpkDropdownOpen = true; onPpkInput($event.target.value)"
                            @change="onPpkInput($event.target.value)"
                            @keydown.escape="isPpkDropdownOpen = false"
                            autocomplete="off"
                            placeholder="Contoh: BUDI HARTONO, S.Sos"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-4 py-2.5 pr-10 text-xs text-white font-bold focus:outline-none placeholder-slate-500 shadow-inner transition-all">
                        <button type="button" 
                            x-show="formData.ppk_nama"
                            @click="formData.ppk_nama = ''; formData.ppk_nip = ''; isPpkDropdownOpen = true" 
                            title="Kosongkan"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                            class="rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
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
                        class="absolute z-[9999] mt-1 w-full bg-slate-900 border border-amber-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">
                        
                        <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                            <span>Pilih Riwayat Pejabat (PPK)</span>
                            <span class="font-mono text-slate-400" x-text="filteredPpkList.length + ' pejabat'"></span>
                        </div>

                        <template x-for="(p, pIdx) in filteredPpkList" :key="pIdx">
                            <div @click="selectPpk(p)"
                                class="px-4 py-2 hover:bg-amber-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                                :class="formData.ppk_nama === p.nama ? 'bg-amber-500/20 text-amber-200' : 'text-slate-200'">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <span class="text-xs text-amber-400/80">👔</span>
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold group-hover:text-amber-300 truncate block" x-text="p.nama"></span>
                                        <span class="text-[10px] text-amber-400/80 font-mono truncate block" x-text="p.nip ? 'NIP: ' + p.nip : 'NIP belum terdata'"></span>
                                    </div>
                                </div>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/25 font-bold shrink-0 group-hover:bg-amber-500/25">
                                    Pilih &amp; Auto-fill NIP ↵
                                </span>
                            </div>
                        </template>
                    </div>

                    <p class="text-[10.5px] text-slate-400 leading-tight">Nama pejabat yang berwenang mengesahkan penerimaan aset hibah.</p>
                </div>

                <!-- NIP PPK -->
                <div class="space-y-2">
                    <div class="h-6 flex items-center justify-between">
                        <label class="text-xs font-bold text-slate-200 flex items-center gap-1">
                            <span>NIP Pejabat (PPK) / Pengesah</span>
                        </label>
                        <span class="text-[10px] text-amber-400 font-mono font-bold bg-amber-500/10 border border-amber-500/25 px-2 py-0.5 rounded-md whitespace-nowrap shrink-0">
                            NIP Pegawai
                        </span>
                    </div>
                    <div class="relative">
                        <input type="text" x-model="formData.ppk_nip"
                            placeholder="Contoh: 19760229 200801 1 010"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 focus:ring-2 focus:ring-amber-500/20 rounded-xl px-4 py-2.5 pr-10 text-xs text-amber-300 font-mono focus:outline-none placeholder-slate-500 shadow-inner transition-all">
                        <button type="button" 
                            x-show="formData.ppk_nip"
                            @click="formData.ppk_nip = ''" 
                            title="Kosongkan"
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                            class="rounded-lg bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                            ✕
                        </button>
                    </div>
                    <p class="text-[10.5px] text-slate-400 leading-tight">Nomor Induk Pegawai bersangkutan (otomatis terisi saat memilih nama pejabat).</p>
                </div>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 5: CHECKLIST VERIFIKASI KEABSAHAN DATA & TOMBOL SUBMIT           -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-2 p-5 sm:p-6 rounded-3xl bg-slate-950/80 border border-amber-500/30 space-y-4 shadow-xl transition-all">
            <label class="flex items-start space-x-3 cursor-pointer select-none">
                <input type="checkbox" x-model="isDataVerified"
                    class="mt-1 w-4 h-4 rounded bg-slate-900 border-slate-700 text-amber-500 focus:ring-amber-400 focus:ring-offset-slate-950 cursor-pointer">
                <div class="text-xs text-slate-300 leading-relaxed">
                    <strong class="text-white block font-bold mb-0.5">Konfirmasi &amp; Validasi Pencatatan Penerimaan Hibah Aset RSUD:</strong>
                    <span>
                        Saya menyatakan bahwa seluruh rincian berita acara serah terima hibah (BAST/NPHD), identitas instansi pemberi, klasifikasi kode 108, spesifikasi teknis fisik aset, taksiran nilai wajar perolehan, unit penempatan ruangan (KIR), serta pejabat penanggung jawab di atas telah diperiksa dengan cermat dan sesuai dokumen legalitas aslinya.
                    </span>
                </div>
            </label>
        </div>

    </div>

</div>
