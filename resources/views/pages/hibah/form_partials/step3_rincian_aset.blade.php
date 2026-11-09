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
                                <span class="font-mono text-emerald-400 font-bold" x-text="'Total: ' + ((totalLuasTanah || formData.tanah_luas_m2 || 0).toLocaleString('id-ID')) + ' m²'"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(tItem, tIdx) in formData.tanah_items" :key="tIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-amber-500/40 transition-all space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[10px] font-bold flex items-center justify-center border border-amber-500/30" x-text="tIdx + 1"></span>
                                                <span class="font-bold text-white text-xs" x-text="tItem.tanah_nama_barang || ('Bidang Tanah #' + (tIdx + 1))"></span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-amber-950/60 text-amber-300 border border-amber-500/30" x-text="((parseFloat(tItem.tanah_luas_m2) || 0).toLocaleString('id-ID')) + ' m²'"></span>
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
                                <span class="font-mono font-bold text-emerald-400 text-sm block" x-text="((formData.tanah_items?.[0]?.tanah_luas_m2 || formData.tanah_luas_m2 || totalLuasTanah || 0).toLocaleString('id-ID')) + ' m²'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Hak Penguasaan:</span>
                                <span class="font-bold text-white block" x-text="formData.tanah_items?.[0]?.tanah_hak || formData.tanah_hak || 'Hak Pakai'"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor &amp; Tanggal Sertifikat:</span>
                                <span class="font-mono text-amber-300 block" x-text="(formData.tanah_items?.[0]?.tanah_sertifikat_no || formData.tanah_sertifikat_no || '-') + ((formData.tanah_items?.[0]?.tanah_sertifikat_tgl || formData.tanah_sertifikat_tgl) ? (' (Tgl: ' + (formData.tanah_items?.[0]?.tanah_sertifikat_tgl || formData.tanah_sertifikat_tgl) + ')') : '')"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Penggunaan Lahan:</span>
                                <span class="font-semibold text-white block" x-text="formData.tanah_items?.[0]?.tanah_penggunaan || formData.tanah_penggunaan || '-'"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Letak / Alamat Lahan:</span>
                                <span class="text-slate-300 block text-[11px]" x-text="formData.tanah_items?.[0]?.tanah_alamat || formData.tanah_alamat || formData.alamat_barang || '-'"></span>
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
                    <template x-if="formData.gedung_items && formData.gedung_items.length > 1">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Rincian <strong class="text-white" x-text="formData.gedung_items.length"></strong> Bangunan Gedung:</span>
                                <span class="font-mono text-amber-400 font-bold" x-text="'Total: ' + totalLuasGedung.toLocaleString('id-ID') + ' m²'"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(gItem, gIdx) in formData.gedung_items" :key="gIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-amber-500/40 transition-all space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[10px] font-bold flex items-center justify-center border border-amber-500/30" x-text="gIdx + 1"></span>
                                                <span class="font-bold text-white text-xs truncate max-w-[200px]" x-text="gItem.gedung_nama_barang || ('Bangunan #' + (gIdx + 1))"></span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-amber-950/60 text-amber-300 border border-amber-500/30" x-text="((parseFloat(gItem.gedung_luas_m2 || gItem.gedung_luas_lantai) || 0).toLocaleString('id-ID')) + ' m²'"></span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Konstruksi:</span>
                                                <span class="font-semibold text-amber-300 truncate block" x-text="(gItem.gedung_bertingkat || 'Bertingkat') + ' • ' + (gItem.gedung_beton || 'Beton')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Kondisi:</span>
                                                <span class="font-semibold text-emerald-400 truncate block" x-text="gItem.gedung_kondisi || 'Baik'"></span>
                                            </div>
                                            <div class="col-span-2 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
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
                                <span class="font-mono font-bold text-amber-400 text-sm block" x-text="((formData.gedung_items?.[0]?.gedung_luas_m2 || formData.gedung_items?.[0]?.gedung_luas_lantai || formData.gedung_luas_m2 || 0).toLocaleString('id-ID')) + ' m²'"></span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Tanah Tempat Berdiri:</span>
                                <span class="text-slate-200 block" x-text="(formData.gedung_items?.[0]?.gedung_status_tanah || 'Tanah Hak Pakai RSUD')"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika JALAN, IRIGASI & JARINGAN (KIB D) -->
            <template x-if="isJaringan">
                <div class="space-y-3">
                    <template x-if="formData.jaringan_items && formData.jaringan_items.length > 1">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Rincian <strong class="text-white" x-text="formData.jaringan_items.length"></strong> Ruas / Jaringan:</span>
                                <span class="font-mono text-amber-400 font-bold" x-text="'Total: ' + totalLuasJaringan.toLocaleString('id-ID') + ' m²'"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(jItem, jIdx) in formData.jaringan_items" :key="jIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-amber-500/40 transition-all space-y-1.5">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[10px] font-bold flex items-center justify-center border border-amber-500/30" x-text="jIdx + 1"></span>
                                                <span class="font-bold text-white text-xs truncate max-w-[200px]" x-text="jItem.jaringan_nama_barang || ('Ruas #' + (jIdx + 1))"></span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-amber-950/60 text-amber-300 border border-amber-500/30" x-text="((parseFloat(jItem.jaringan_luas || jItem.jaringan_luas_m2) || 0).toLocaleString('id-ID')) + ' m²'"></span>
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Konstruksi:</span>
                                                <span class="font-semibold text-amber-300 truncate block" x-text="(jItem.jaringan_konstruksi || '-') + ' • ' + (jItem.jaringan_beton || 'Beton')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Dimensi:</span>
                                                <span class="font-mono text-slate-300 truncate block" x-text="(jItem.jaringan_panjang || jItem.jaringan_panjang_m || 0) + 'm x ' + (jItem.jaringan_lebar || jItem.jaringan_lebar_m || 0) + 'm'"></span>
                                            </div>
                                            <div class="col-span-2 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(getJaringanSubtotal(jItem))"></span>
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
                                <span class="font-bold text-amber-300 block" x-text="(formData.jaringan_items?.[0]?.jaringan_konstruksi || formData.jaringan_konstruksi || '-') + ' • ' + (formData.jaringan_items?.[0]?.jaringan_beton || 'Beton')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dimensi (P x L):</span>
                                <span class="font-mono text-white block" x-text="(formData.jaringan_items?.[0]?.jaringan_panjang || formData.jaringan_items?.[0]?.jaringan_panjang_m || 0) + 'm x ' + (formData.jaringan_items?.[0]?.jaringan_lebar || formData.jaringan_items?.[0]?.jaringan_lebar_m || 0) + 'm'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Total:</span>
                                <span class="font-mono text-amber-400 font-bold block" x-text="((formData.jaringan_items?.[0]?.jaringan_luas || formData.jaringan_items?.[0]?.jaringan_luas_m2 || 0).toLocaleString('id-ID')) + ' m²'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika ASET LAINNYA (KIB E) -->
            <template x-if="isAsetLainnya || isLainnya">
                <div class="space-y-3">

                    <!-- ========================================================================= -->
                    <!-- KASUS 1: ASET TETAP LAINNYA EKSTRAKOMTABEL (<= 300rb)                     -->
                    <!-- ========================================================================= -->
                    <template x-if="formData.is_extracomtable || formData.lainnya_items?.[0]?.is_extracom">
                        <div>
                            <!-- 1A. Multi-Item Ekstrakomtabel Repeater -->
                            <template x-if="formData.lainnya_items && formData.lainnya_items.length > 1">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between text-xs text-slate-400">
                                        <span>Rincian <strong class="text-white" x-text="formData.lainnya_items.length"></strong> Item Ekstrakomtabel (KIB E):</span>
                                        <span class="font-mono text-amber-400 font-bold" x-text="'Total: ' + totalVolumeAsetLainnya + ' ' + (formData.satuan || 'Item')"></span>
                                    </div>
                                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                        <template x-for="(lItem, lIdx) in formData.lainnya_items" :key="lIdx">
                                            <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-amber-500/40 transition-all space-y-1.5">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[10px] font-bold flex items-center justify-center border border-amber-500/30" x-text="lIdx + 1"></span>
                                                        <span class="font-bold text-white text-xs truncate max-w-[200px]" x-text="lItem.lainnya_nama_barang || ('Item #' + (lIdx + 1))"></span>
                                                        <span class="text-[9px] px-1.5 py-0.5 rounded font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                                            Ekstrakom
                                                        </span>
                                                    </div>
                                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-800 text-amber-300 border border-slate-700" x-text="(lItem.lainnya_jumlah || lItem.lainnya_jumlah_barang || 1) + ' ' + (lItem.lainnya_satuan || 'Unit')"></span>
                                                </div>
                                                <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                                    <div>
                                                        <span class="text-[9.5px] text-slate-500 block uppercase">Merk / Tipe:</span>
                                                        <span class="font-semibold text-amber-300 truncate block" x-text="(lItem.lainnya_pencipta || lItem.lainnya_merk || '-') + ' • ' + (lItem.lainnya_spesifikasi || lItem.lainnya_type || '-')"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-[9.5px] text-slate-500 block uppercase">No Pabrik / Bahan:</span>
                                                        <span class="font-mono text-slate-300 truncate block" x-text="(lItem.lainnya_no_pabrik || '-') + ' • ' + (lItem.lainnya_bahan || '-')"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-[9.5px] text-slate-500 block uppercase">Ukuran / Kapasitas:</span>
                                                        <span class="text-slate-300 truncate block" x-text="lItem.lainnya_ukuran || '-'"></span>
                                                    </div>
                                                    <div>
                                                        <span class="text-[9.5px] text-slate-500 block uppercase">Kondisi:</span>
                                                        <span class="font-semibold text-emerald-400 truncate block" x-text="lItem.lainnya_kondisi || 'Baik'"></span>
                                                    </div>
                                                    <div class="col-span-2 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                        <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                        <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(lItem))"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- 1B. Single-Item Ekstrakomtabel (Mirror Persis Gambar 2 Input) -->
                            <template x-if="!formData.lainnya_items || formData.lainnya_items.length <= 1">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between pb-1.5 border-b border-slate-800">
                                        <div class="flex items-center space-x-1.5">
                                            <span class="text-amber-400 font-bold text-xs uppercase tracking-wider">📦 Format Spesifikasi Ekstrakomtabel (KIB E)</span>
                                        </div>
                                        <span class="text-[9px] px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold font-mono">
                                            Ekstrakomtabel (≤ 300rb)
                                        </span>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                                        <!-- Kolom Kiri: MERK, TYPE & UKURAN -->
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2.5 shadow-sm">
                                            <div class="text-[10px] font-bold text-amber-400 uppercase tracking-wider border-b border-slate-800 pb-1 flex items-center justify-between">
                                                <span>⚙️ MERK, TYPE &amp; UKURAN:</span>
                                                <span class="text-[8.5px] text-slate-500 font-mono">Spesifikasi Utama</span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-400 block mb-0.5">Nama Barang (PMDN 108):</span>
                                                <span class="font-bold text-white text-xs block truncate" x-text="formData.lainnya_items?.[0]?.lainnya_nama_barang || formData.nama_barang || (selectedSubSub?.nama || '-')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-400 block mb-0.5">Merk Barang:</span>
                                                <span class="font-bold text-amber-300 block truncate" x-text="formData.lainnya_items?.[0]?.lainnya_pencipta || formData.lainnya_items?.[0]?.lainnya_merk || formData.merk || '-'"></span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2 pt-1 border-t border-slate-800/60">
                                                <div>
                                                    <span class="text-[9px] text-slate-400 block mb-0.5">Type / Model:</span>
                                                    <span class="font-mono text-slate-200 block truncate" x-text="formData.lainnya_items?.[0]?.lainnya_spesifikasi || formData.lainnya_items?.[0]?.lainnya_type || formData.type || '-'"></span>
                                                </div>
                                                <div>
                                                    <span class="text-[9px] text-slate-400 block mb-0.5">Ukuran / Kapasitas:</span>
                                                    <span class="text-slate-200 block truncate" x-text="formData.lainnya_items?.[0]?.lainnya_ukuran || formData.ukuran || '-'"></span>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Kolom Kanan: NO PABRIK, BAHAN & KONDISI -->
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2.5 shadow-sm">
                                            <div class="text-[10px] font-bold text-amber-400 uppercase tracking-wider border-b border-slate-800 pb-1 flex items-center justify-between">
                                                <span>🏷️ NO PABRIK, BAHAN &amp; KONDISI:</span>
                                                <span class="text-[8.5px] text-slate-500 font-mono">Fisik &amp; Legal</span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-2">
                                                <div>
                                                    <span class="text-[9.5px] text-slate-400 block mb-0.5">No Pabrik / SN:</span>
                                                    <span class="font-mono text-slate-200 block truncate" x-text="formData.lainnya_items?.[0]?.lainnya_no_pabrik || formData.no_pabrik || '-'"></span>
                                                </div>
                                                <div>
                                                    <span class="text-[9.5px] text-slate-400 block mb-0.5">Bahan Pembuatan:</span>
                                                    <span class="text-slate-200 block truncate" x-text="formData.lainnya_items?.[0]?.lainnya_bahan || formData.bahan || '-'"></span>
                                                </div>
                                            </div>
                                            <div class="pt-1 border-t border-slate-800/60">
                                                <span class="text-[9.5px] text-slate-400 block mb-0.5">Kondisi Barang:</span>
                                                <span class="font-bold text-emerald-400 block" x-text="formData.lainnya_items?.[0]?.lainnya_kondisi || formData.kondisi || 'Baik (B)'"></span>
                                            </div>
                                            <template x-if="formData.lainnya_items?.[0]?.lainnya_keterangan">
                                                <div class="pt-1 border-t border-slate-800/60">
                                                    <span class="text-[9px] text-slate-400 block mb-0.5">Keterangan Khusus:</span>
                                                    <span class="text-slate-300 text-[11px] block truncate" x-text="formData.lainnya_items?.[0]?.lainnya_keterangan"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </template>

                    <!-- ========================================================================= -->
                    <!-- KASUS 2: ASET TETAP LAINNYA REGULER (DIBEDAKAN 3 SUB-TIPE)                 -->
                    <!-- ========================================================================= -->
                    <template x-if="!(formData.is_extracomtable || formData.lainnya_items?.[0]?.is_extracom)">
                        <div>
                            <!-- 2A. Multi-Item Reguler Repeater -->
                            <template x-if="formData.lainnya_items && formData.lainnya_items.length > 1">
                                <div class="space-y-2.5">
                                    <div class="flex items-center justify-between text-xs text-slate-400">
                                        <span>Rincian <strong class="text-white" x-text="formData.lainnya_items.length"></strong> Item Aset Tetap Lainnya:</span>
                                        <span class="font-mono text-amber-400 font-bold" x-text="'Total: ' + totalVolumeAsetLainnya + ' ' + (formData.satuan || 'Item')"></span>
                                    </div>
                                    <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                        <template x-for="(lItem, lIdx) in formData.lainnya_items" :key="lIdx">
                                            <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-amber-500/40 transition-all space-y-1.5">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center space-x-2">
                                                        <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 font-mono text-[10px] font-bold flex items-center justify-center border border-amber-500/30" x-text="lIdx + 1"></span>
                                                        <span class="font-bold text-white text-xs truncate max-w-[180px]" x-text="lItem.lainnya_nama_barang || lItem.lainnya_judul || ('Item #' + (lIdx + 1))"></span>
                                                        <span class="text-[9px] px-2 py-0.5 rounded-full font-bold border"
                                                              :class="{
                                                                  'bg-amber-500/20 text-amber-300 border-amber-500/30': (!lItem.kib_e_type || lItem.kib_e_type === 'buku'),
                                                                  'bg-purple-500/20 text-purple-300 border-purple-500/30': lItem.kib_e_type === 'kesenian',
                                                                  'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': lItem.kib_e_type === 'hewan_tumbuhan'
                                                              }"
                                                              x-text="lItem.kib_e_type === 'kesenian' ? '🎨 Kesenian' : (lItem.kib_e_type === 'hewan_tumbuhan' ? '🌿 Hewan/Tanaman' : '📚 Buku Pustaka')">
                                                        </span>
                                                    </div>
                                                    <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-800 text-amber-300 border border-slate-700" x-text="(lItem.lainnya_jumlah || lItem.lainnya_jumlah_barang || 1) + ' ' + (lItem.lainnya_satuan || 'Unit')"></span>
                                                </div>

                                                <!-- Field Item Buku -->
                                                <template x-if="(!lItem.kib_e_type || lItem.kib_e_type === 'buku')">
                                                    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Judul &amp; Penulis:</span>
                                                            <span class="font-semibold text-amber-300 truncate block" x-text="(lItem.lainnya_judul || lItem.lainnya_buku_judul || '-') + ' • ' + (lItem.lainnya_pencipta || lItem.lainnya_buku_pencipta || '-')"></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Kondisi:</span>
                                                            <span class="font-semibold text-emerald-400 truncate block" x-text="lItem.lainnya_kondisi || 'Baik'"></span>
                                                        </div>
                                                        <template x-if="lItem.lainnya_spesifikasi">
                                                            <div class="col-span-2">
                                                                <span class="text-[9.5px] text-slate-500 block uppercase">Spesifikasi:</span>
                                                                <span class="text-slate-300 text-[10.5px] truncate block" x-text="lItem.lainnya_spesifikasi"></span>
                                                            </div>
                                                        </template>
                                                        <div class="col-span-2 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                            <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                            <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(lItem))"></span>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Field Item Kesenian -->
                                                <template x-if="lItem.kib_e_type === 'kesenian'">
                                                    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Asal &amp; Seniman:</span>
                                                            <span class="font-semibold text-purple-300 truncate block" x-text="(lItem.lainnya_asal_daerah || lItem.lainnya_kesenian_asal || '-') + ' • ' + (lItem.lainnya_pencipta || lItem.lainnya_kesenian_pencipta || '-')"></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Bahan &amp; Dimensi:</span>
                                                            <span class="text-slate-200 truncate block" x-text="(lItem.lainnya_bahan || lItem.lainnya_kesenian_bahan || '-') + ' • ' + (lItem.lainnya_ukuran || lItem.lainnya_kesenian_ukuran || '-')"></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Kondisi:</span>
                                                            <span class="font-semibold text-emerald-400 truncate block" x-text="lItem.lainnya_kondisi || 'Baik'"></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Spesifikasi Seni:</span>
                                                            <span class="text-amber-300/90 truncate block text-[10.5px]" x-text="lItem.lainnya_spesifikasi || lItem.lainnya_kesenian_spesifikasi || '-'"></span>
                                                        </div>
                                                        <div class="col-span-2 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                            <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                            <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(lItem))"></span>
                                                        </div>
                                                    </div>
                                                </template>

                                                <!-- Field Item Hewan / Tumbuhan -->
                                                <template x-if="lItem.kib_e_type === 'hewan_tumbuhan'">
                                                    <div class="grid grid-cols-2 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Jenis Spesies:</span>
                                                            <span class="font-semibold text-emerald-300 truncate block" x-text="lItem.lainnya_judul || lItem.lainnya_nama_barang || '-'"></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Ukuran / Tinggi:</span>
                                                            <span class="font-mono text-slate-200 truncate block" x-text="lItem.lainnya_ukuran || '-'"></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Kondisi:</span>
                                                            <span class="font-semibold text-emerald-400 truncate block" x-text="lItem.lainnya_kondisi || 'Baik'"></span>
                                                        </div>
                                                        <div>
                                                            <span class="text-[9.5px] text-slate-500 block uppercase">Keterangan:</span>
                                                            <span class="text-slate-300 truncate block text-[10.5px]" x-text="lItem.lainnya_spesifikasi || '-'"></span>
                                                        </div>
                                                        <div class="col-span-2 flex items-center justify-between pt-1 border-t border-slate-900 text-xs">
                                                            <span class="text-slate-400 text-[10px]">Taksiran Nilai:</span>
                                                            <span class="font-mono font-bold text-emerald-400" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(lItem))"></span>
                                                        </div>
                                                    </div>
                                                </template>

                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- 2B. Single-Item Reguler (Menyesuaikan 3 Sub-Tipe KIB E) -->
                            <template x-if="!formData.lainnya_items || formData.lainnya_items.length <= 1">
                                <div>
                                    <!-- SUB-TIPE 1: BUKU PERPUSTAKAAN -->
                                    <template x-if="(!formData.lainnya_items?.[0]?.kib_e_type || formData.lainnya_items?.[0]?.kib_e_type === 'buku')">
                                        <div class="grid grid-cols-2 gap-3 text-xs">
                                            <div class="col-span-2 flex items-center justify-between pb-1.5 border-b border-slate-800">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Sub-Tipe KIB E:</span>
                                                    <span class="font-bold text-amber-400 uppercase text-xs flex items-center gap-1.5">
                                                        <span>📚 Buku Perpustakaan &amp; Pustaka Medis</span>
                                                    </span>
                                                </div>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold font-mono">
                                                    KIB E Reguler
                                                </span>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Judul Buku / Pustaka Medis:</span>
                                                <span class="font-bold text-white text-sm block" x-text="formData.lainnya_items?.[0]?.lainnya_judul || formData.lainnya_items?.[0]?.lainnya_buku_judul || formData.lainnya_items?.[0]?.lainnya_nama_barang || formData.nama_barang || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pengarang / Penerbit / Pencipta:</span>
                                                <span class="text-slate-200 block font-medium" x-text="formData.lainnya_items?.[0]?.lainnya_pencipta || formData.lainnya_items?.[0]?.lainnya_buku_pencipta || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kondisi Fisik:</span>
                                                <span class="font-bold text-emerald-400 block" x-text="formData.lainnya_items?.[0]?.lainnya_kondisi || formData.kondisi || 'Baik'"></span>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Spesifikasi Buku / Literatur:</span>
                                                <span class="text-amber-300/90 block text-[11px]" x-text="formData.lainnya_items?.[0]?.lainnya_spesifikasi || formData.lainnya_items?.[0]?.lainnya_buku_spesifikasi || '-'"></span>
                                            </div>
                                            <template x-if="formData.lainnya_items?.[0]?.lainnya_keterangan">
                                                <div class="col-span-2">
                                                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Catatan Khusus:</span>
                                                    <span class="text-slate-300 block text-[11px]" x-text="formData.lainnya_items?.[0]?.lainnya_keterangan"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- SUB-TIPE 2: KESENIAN & KEBUDAYAAN -->
                                    <template x-if="formData.lainnya_items?.[0]?.kib_e_type === 'kesenian'">
                                        <div class="grid grid-cols-2 gap-3 text-xs">
                                            <div class="col-span-2 flex items-center justify-between pb-1.5 border-b border-slate-800">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Sub-Tipe KIB E:</span>
                                                    <span class="font-bold text-purple-400 uppercase text-xs flex items-center gap-1.5">
                                                        <span>🎨 Kesenian &amp; Kebudayaan</span>
                                                    </span>
                                                </div>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20 font-bold font-mono">
                                                    KIB E Reguler
                                                </span>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nama / Jenis Benda Seni:</span>
                                                <span class="font-bold text-white text-sm block" x-text="formData.lainnya_items?.[0]?.lainnya_nama_barang || formData.nama_barang || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Asal Daerah / Wilayah:</span>
                                                <span class="font-semibold text-purple-300 block" x-text="formData.lainnya_items?.[0]?.lainnya_asal_daerah || formData.lainnya_items?.[0]?.lainnya_kesenian_asal || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pencipta / Seniman:</span>
                                                <span class="text-slate-200 block" x-text="formData.lainnya_items?.[0]?.lainnya_pencipta || formData.lainnya_items?.[0]?.lainnya_kesenian_pencipta || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Bahan Pembuatan:</span>
                                                <span class="text-slate-200 block" x-text="formData.lainnya_items?.[0]?.lainnya_bahan || formData.lainnya_items?.[0]?.lainnya_kesenian_bahan || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Ukuran / Dimensi:</span>
                                                <span class="font-mono text-slate-200 block" x-text="formData.lainnya_items?.[0]?.lainnya_ukuran || formData.lainnya_items?.[0]?.lainnya_kesenian_ukuran || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kondisi Fisik:</span>
                                                <span class="font-bold text-emerald-400 block" x-text="formData.lainnya_items?.[0]?.lainnya_kondisi || formData.kondisi || 'Baik'"></span>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Spesifikasi Karya Seni / Ornamen:</span>
                                                <span class="text-amber-300/90 block text-[11px]" x-text="formData.lainnya_items?.[0]?.lainnya_spesifikasi || formData.lainnya_items?.[0]?.lainnya_kesenian_spesifikasi || '-'"></span>
                                            </div>
                                            <template x-if="formData.lainnya_items?.[0]?.lainnya_keterangan">
                                                <div class="col-span-2">
                                                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Catatan Khusus:</span>
                                                    <span class="text-slate-300 block text-[11px]" x-text="formData.lainnya_items?.[0]?.lainnya_keterangan"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- SUB-TIPE 3: HEWAN & TUMBUHAN -->
                                    <template x-if="formData.lainnya_items?.[0]?.kib_e_type === 'hewan_tumbuhan'">
                                        <div class="grid grid-cols-2 gap-3 text-xs">
                                            <div class="col-span-2 flex items-center justify-between pb-1.5 border-b border-slate-800">
                                                <div class="flex items-center space-x-2">
                                                    <span class="text-[10px] font-bold text-slate-400 uppercase">Sub-Tipe KIB E:</span>
                                                    <span class="font-bold text-emerald-400 uppercase text-xs flex items-center gap-1.5">
                                                        <span>🌿 Hewan &amp; Tumbuhan</span>
                                                    </span>
                                                </div>
                                                <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-bold font-mono">
                                                    KIB E Reguler
                                                </span>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Jenis Hewan / Tanaman:</span>
                                                <span class="font-bold text-white text-sm block" x-text="formData.lainnya_items?.[0]?.lainnya_judul || formData.lainnya_items?.[0]?.lainnya_hewan_jenis || formData.lainnya_items?.[0]?.lainnya_nama_barang || formData.nama_barang || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Ukuran / Tinggi:</span>
                                                <span class="font-mono text-emerald-300 font-bold block" x-text="formData.lainnya_items?.[0]?.lainnya_ukuran || formData.lainnya_items?.[0]?.lainnya_hewan_ukuran || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kondisi Fisik:</span>
                                                <span class="font-bold text-emerald-400 block" x-text="formData.lainnya_items?.[0]?.lainnya_kondisi || formData.kondisi || 'Baik'"></span>
                                            </div>
                                            <div class="col-span-2">
                                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Keterangan / Spesifikasi Khusus:</span>
                                                <span class="text-slate-300 block text-[11px]" x-text="formData.lainnya_items?.[0]?.lainnya_spesifikasi || formData.lainnya_items?.[0]?.lainnya_hewan_spesifikasi || '-'"></span>
                                            </div>
                                            <template x-if="formData.lainnya_items?.[0]?.lainnya_keterangan">
                                                <div class="col-span-2">
                                                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Catatan Tambahan:</span>
                                                    <span class="text-slate-300 block text-[11px]" x-text="formData.lainnya_items?.[0]?.lainnya_keterangan"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </template>

                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika ATB (Aset Tidak Berwujud) -->
            <template x-if="isAtb">
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="col-span-2">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nama Lisensi / Software:</span>
                            <span class="font-bold text-amber-300 block" x-text="formData.atb_items?.[0]?.atb_judul_nama || formData.nama_barang || '-'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pencipta / Vendor:</span>
                            <span class="text-white block" x-text="formData.atb_items?.[0]?.atb_pencipta || '-'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Jumlah Lisensi:</span>
                            <span class="font-mono text-emerald-400 font-bold block" x-text="(formData.atb_items?.[0]?.atb_jumlah || 1) + ' Lisensi'"></span>
                        </div>
                    </div>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika KDP (KIB F) -->
            <template x-if="isKdp">
                <div class="space-y-3">
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="col-span-2">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Fisik:</span>
                            <span class="font-bold text-amber-300 block" x-text="(formData.kdp_items?.[0]?.kdp_bertingkat || 'Bertingkat') + ' • ' + (formData.kdp_items?.[0]?.kdp_beton || 'Beton')"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Bangunan:</span>
                            <span class="font-mono text-amber-400 font-bold block" x-text="(formData.kdp_items?.[0]?.kdp_luas_m2 || 0) + ' m²'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Fisik:</span>
                            <span class="text-white font-bold block" x-text="formData.kdp_items?.[0]?.kdp_is_baru || 'Baru'"></span>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 4: PEJABAT PEMBUAT KOMITMEN (PPK) & PENGESAH RSUD               -->
        <!-- ===================================================================== -->
        <div class="lg:col-span-2 p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-amber-500/30 shadow-xl space-y-5 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2.5">
                    <span class="w-7 h-7 rounded-xl flex items-center justify-center text-xs font-bold border bg-amber-500/20 text-amber-400 border-amber-500/30">4</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Pejabat Pembuat Komitmen (PPK) &amp; Pengesah RSUD
                    </h3>
                </div>
                <span class="text-[10px] font-mono font-bold text-amber-400/90 bg-amber-500/10 border border-amber-500/20 px-2.5 py-1 rounded-lg">
                    Pengesahan Naskah BAST
                </span>
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
                        Saya menyatakan bahwa seluruh rincian berita acara serah terima hibah (BAST/NPHD), identitas instansi pemberi, klasifikasi kode 108, spesifikasi teknis fisik aset, taksiran nilai wajar perolehan, serta pejabat penanggung jawab di atas telah diperiksa dengan cermat dan sesuai dokumen legalitas aslinya.
                    </span>
                </div>
            </label>
        </div>

    </div>

</div>
