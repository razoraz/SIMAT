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
                Tinjau kembali seluruh data legalitas PKS, klasifikasi akun 1.5.2, spesifikasi fisik, unit penempatan KIR, serta simulasi NIBAR sebelum disimpan resmi ke database SIMAT-RK.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
            <button type="button" @click="goToStep(2)"
                class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-700">
                <span>✏️ Edit Data (Langkah 2)</span>
            </button>
        </div>
    </div>

    <!-- 2. Quick Health Check Status (Grid 4 Kolom Indikator Kelayakan) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Status 1: Mitra & Dokumen PKS -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">1. PKS &amp; MITRA</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    ✓ Terdaftar
                </span>
            </div>
            <div class="text-xs font-black font-mono text-white truncate" x-text="formData.nomor_pks || '-'"></div>
            <div class="text-[11px] text-cyan-400 truncate font-semibold" x-text="formData.mitra_nama || 'Mitra belum diisi'"></div>
        </div>

        <!-- Status 2: Klasifikasi 108 & Skema -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-cyan-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider">2. AKUN 1.5.2</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20" x-text="formData.skema_kemitraan">
                </span>
            </div>
            <div class="text-xs font-black font-mono text-cyan-300 truncate" x-text="selectedKode108 || '-'"></div>
            <div class="text-[11px] text-slate-300 truncate font-semibold" x-text="formData.nama_barang || '-'"></div>
        </div>

        <!-- Status 3: Nilai Taksiran Wajar & Volume -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-emerald-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">3. NILAI &amp; VOLUME</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-mono">
                    <span x-text="formData.jumlah_volume"></span> <span x-text="formData.satuan"></span>
                </span>
            </div>
            <div class="text-xs sm:text-sm font-black font-mono text-emerald-400 truncate" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
            <div class="text-[10.5px] text-slate-400 truncate">
                Taksiran per <span x-text="formData.satuan || 'Unit'"></span>: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></span>
            </div>
        </div>

        <!-- Status 4: Ruangan Penempatan KIR -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-indigo-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">4. RUANGAN KIR</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20" x-text="formData.kondisi">
                </span>
            </div>
            <div class="text-xs font-black text-white truncate" x-text="getUnitName(formData.unit_id)"></div>
            <div class="text-[10.5px] text-slate-400 truncate" x-text="formData.ppk_nama ? ('PPK: ' + formData.ppk_nama) : 'PPK belum ditentukan'"></div>
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
                    <span class="font-extrabold text-white block" x-text="formData.mitra_nama || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor Perjanjian (PKS):</span>
                    <span class="font-mono font-bold text-cyan-300 block" x-text="formData.nomor_pks || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tanggal PKS:</span>
                    <span class="font-semibold text-white block" x-text="formData.tanggal_pks || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Bentuk / Skema Kemitraan:</span>
                    <span class="px-2 py-0.5 rounded-lg bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 font-bold inline-block text-[11px]" x-text="formData.skema_kemitraan || 'Sewa'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Periode Berlaku Kerja Sama:</span>
                    <span class="font-mono text-slate-200 block" x-text="formData.tanggal_mulai && formData.tanggal_selesai ? (formData.tanggal_mulai + ' s/d ' + formData.tanggal_selesai) : 'Sesuai masa operasional PKS'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tahun Pembukuan &amp; Periode:</span>
                    <span class="font-mono font-bold text-white block" x-text="(formData.tahun_perolehan || '{{ date('Y') }}') + ' • ' + (formData.triwulan || 'TW I')"></span>
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
                    <span class="text-[11px] font-mono text-slate-400 font-semibold" x-text="'@ Rp ' + formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></span>
                </div>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 3: SPESIFIKASI TEKNIS FISIK BARANG (MENYESUAIKAN LEMBAR KIB)    -->
        <!-- ===================================================================== -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-slate-800 shadow-xl space-y-4 relative">
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
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Tanah:</span>
                        <span class="font-mono font-bold text-emerald-400 text-sm block" x-text="(formData.tanah_luas_m2 || 0) + ' m²'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Hak Penguasaan:</span>
                        <span class="font-bold text-white block" x-text="formData.tanah_hak || 'Hak Pakai'"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor &amp; Tanggal Sertifikat:</span>
                        <span class="font-mono text-cyan-300 block" x-text="(formData.tanah_sertifikat_no || '-') + (formData.tanah_sertifikat_tgl ? (' (Tgl: ' + formData.tanah_sertifikat_tgl + ')') : '')"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Peruntukan / Penggunaan Lahan:</span>
                        <span class="font-semibold text-white block" x-text="formData.tanah_penggunaan || '-'"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Batas-Batas Bidang Tanah:</span>
                        <span class="text-slate-300 block text-[11px]" x-text="formData.tanah_batas || '-'"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Letak / Alamat Lahan:</span>
                        <span class="text-slate-300 block text-[11px]" x-text="formData.tanah_alamat || formData.alamat_barang || '-'"></span>
                    </div>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika PERALATAN & MESIN (KIB B) -->
            <template x-if="isMesin">
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Merk / Pabrikan:</span>
                        <span class="font-bold text-white block" x-text="formData.merk || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tipe / Model:</span>
                        <span class="font-bold text-cyan-300 block" x-text="formData.type || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor Seri Pabrik (S/N):</span>
                        <span class="font-mono text-slate-200 block" x-text="formData.no_pabrik || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Bahan / Material:</span>
                        <span class="text-slate-200 block" x-text="formData.bahan || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Ukuran / Kapasitas:</span>
                        <span class="text-slate-200 block" x-text="formData.ukuran || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tahun Pembuatan:</span>
                        <span class="font-mono text-slate-200 block" x-text="formData.tahun_pembuatan || '-'"></span>
                    </div>
                    <template x-if="formData.no_rangka || formData.no_mesin || formData.no_polisi">
                        <div class="col-span-2 pt-2 border-t border-slate-900">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Identitas Kendaraan:</span>
                            <span class="font-mono text-[11px] text-cyan-300 block" x-text="'Rangka: ' + (formData.no_rangka || '-') + ' • Mesin: ' + (formData.no_mesin || '-') + ' • Plat: ' + (formData.no_polisi || '-')"></span>
                        </div>
                    </template>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika GEDUNG & BANGUNAN (KIB C) -->
            <template x-if="isGedung">
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Bertingkat:</span>
                        <span class="font-bold text-white block" x-text="formData.gedung_bertingkat || 'Tidak'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Beton / Rangka:</span>
                        <span class="font-bold text-white block" x-text="formData.gedung_beton || 'Beton Bertulang'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Total Lantai:</span>
                        <span class="font-mono font-bold text-blue-400 text-sm block" x-text="(formData.gedung_luas_lantai || 0) + ' m²'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Tanah Tempat Berdiri:</span>
                        <span class="text-slate-200 block" x-text="formData.gedung_status_tanah || 'Tanah Milik RSUD'"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dokumen IMB / PBG / SLF:</span>
                        <span class="font-mono text-cyan-300 block" x-text="(formData.gedung_dokumen_no || '-') + (formData.gedung_dokumen_tgl ? (' (Tgl: ' + formData.gedung_dokumen_tgl + ')') : '')"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Fungsi &amp; Peruntukan Operasional:</span>
                        <span class="text-slate-200 block font-semibold" x-text="formData.gedung_fungsi || '-'"></span>
                    </div>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika JALAN & JARINGAN (KIB D) -->
            <template x-if="isJaringan">
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi Jaringan / Jalan:</span>
                        <span class="font-bold text-teal-300 block" x-text="formData.jaringan_konstruksi || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Total:</span>
                        <span class="font-mono text-white block" x-text="formData.jaringan_luas ? (formData.jaringan_luas + ' m²') : '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dimensi (Panjang x Lebar):</span>
                        <span class="font-mono text-white block" x-text="(formData.jaringan_panjang || 0) + ' m x ' + (formData.jaringan_lebar || 0) + ' m'"></span>
                    </div>
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dokumen Kontrak Teknis:</span>
                        <span class="font-mono text-cyan-300 block" x-text="(formData.jaringan_dokumen_no || '-') + (formData.jaringan_dokumen_tgl ? (' (Tgl: ' + formData.jaringan_dokumen_tgl + ')') : '')"></span>
                    </div>
                </div>
            </template>

            <!-- Tampilan Spesifikasi Fisik Jika ASET TETAP LAINNYA (KIB E) -->
            <template x-if="isLainnya">
                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="col-span-2">
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Judul Spesifik Aset:</span>
                        <span class="font-bold text-purple-300 block" x-text="formData.lainnya_judul || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Jenis / Kategori:</span>
                        <span class="text-white block" x-text="formData.lainnya_jenis || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Ukuran / Dimensi:</span>
                        <span class="text-white block" x-text="formData.lainnya_ukuran || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Bahan / Material:</span>
                        <span class="text-white block" x-text="formData.lainnya_bahan || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Asal-Usul:</span>
                        <span class="text-white block" x-text="formData.lainnya_asal || '-'"></span>
                    </div>
                </div>
            </template>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 4: ALOKASI RUANGAN (KIR), KONDISI FISIK & PEJABAT RSUD          -->
        <!-- ===================================================================== -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-slate-800 shadow-xl space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-bold border border-indigo-500/30">4</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Penempatan Ruangan &amp; Akuntabilitas RSUD
                    </h3>
                </div>
                <button type="button" @click="goToStep(2)" class="text-[11px] font-bold text-cyan-400 hover:text-cyan-300 flex items-center gap-1 cursor-pointer">
                    <span>Ubah</span> &rarr;
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div class="col-span-2">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Unit / Ruangan Penempatan (KIR):</span>
                    <span class="font-extrabold text-white text-sm block" x-text="getUnitName(formData.unit_id)"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kondisi Fisik Saat Diterima:</span>
                    <span class="font-bold text-emerald-400 block" x-text="formData.kondisi || 'Baik'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pejabat PPK RSUD:</span>
                    <span class="font-bold text-white block" x-text="formData.ppk_nama || '-'"></span>
                </div>
                <div class="col-span-2">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Alamat / Lokasi Fisik Barang:</span>
                    <span class="text-slate-300 text-[11px] block" x-text="formData.alamat_barang || 'RSUD Dr. H. Koesnandi Bondowoso'"></span>
                </div>
                <div class="col-span-2 pt-2 border-t border-slate-900">
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">NIP Pejabat Penanggung Jawab:</span>
                    <span class="font-mono text-slate-300 block" x-text="formData.ppk_nip || '-'"></span>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. Simulasi Visual Kartu NIBAR (Nomor Induk Barang Barcode) -->
    <div class="p-6 rounded-3xl bg-gradient-to-br from-slate-950 via-slate-900 to-cyan-950/40 border border-cyan-500/40 shadow-2xl space-y-4 relative overflow-hidden">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-cyan-400/20 text-cyan-400 flex items-center justify-center text-sm font-bold border border-cyan-400/30">🏷️</span>
                <div>
                    <h3 class="text-xs sm:text-sm font-black text-white uppercase tracking-wider">
                        Simulasi Alokasi Barcode &amp; NIBAR SIMAT-RK
                    </h3>
                    <p class="text-[11px] text-slate-400">Preview struktur Nomor Induk Barang unik yang akan diterbitkan secara otomatis untuk aset ini.</p>
                </div>
            </div>
            <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-xl border border-emerald-500/20 font-mono shrink-0">
                Otomatis <span x-text="formData.jumlah_volume"></span> Unit Register
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
            <!-- Kartu Stiker Barcode Simulasi -->
            <div class="md:col-span-2 p-4 rounded-2xl bg-slate-950 border border-slate-800 space-y-2 shadow-inner">
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                    <span class="text-[9px] font-bold tracking-widest text-slate-400 uppercase">RSUD DR. H. KOESNANDI BONDOWOSO</span>
                    <span class="text-[9px] font-mono text-cyan-400 font-bold">AKUN 1.5.2 KEMITRAAN</span>
                </div>
                <div class="py-1">
                    <span class="text-[10px] text-slate-400 block font-mono">SIMULASI NIBAR UNIT #1:</span>
                    <div class="text-sm sm:text-base font-black font-mono text-cyan-300 tracking-wider break-all" x-text="simulatedNibar"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400 pt-1 border-t border-slate-900">
                    <span x-text="'Barang: ' + (formData.nama_barang || '-')"></span>
                    <span x-text="'Mitra: ' + (formData.mitra_nama || '-')"></span>
                </div>
            </div>

            <!-- Catatan Integrasi Otomatis -->
            <div class="text-xs text-slate-400 space-y-2">
                <p class="leading-relaxed">
                    ✨ Setelah form disimpan, sistem secara instan menghasilkan record inventaris pada tabel <code class="text-cyan-400 font-mono text-[10px]">astap_registers</code> dan siap dicetak label QR barcode.
                </p>
                <div class="flex items-center gap-1.5 text-[11px] text-emerald-400 font-semibold">
                    <span>✓ Siap dicatat ke Neraca Aset Kemitraan</span>
                </div>
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
