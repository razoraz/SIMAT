<!-- ========================================================================= -->
<!-- LANGKAH 3: LEMBAR VERIFIKASI & KONFIRMASI DATA PELIMPAHAN BMD SKPD         -->
<!-- ========================================================================= -->
<div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <!-- 1. Header Banner Langkah 3 -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-400/10 text-indigo-300 border border-indigo-400/20 text-xs font-bold mb-2">
                <span>🛡️ LANGKAH 3 DARI 3: VERIFIKASI &amp; KONFIRMASI DATA PELIMPAHAN</span>
            </div>
            <h2 class="text-lg sm:text-xl font-extrabold text-white flex items-center space-x-2">
                <span class="p-2 rounded-xl bg-indigo-400/10 text-indigo-400 text-sm">📋</span>
                <span>Langkah 3: Lembar Verifikasi Data Pelimpahan Aset SKPD</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1">
                Tinjau kembali seluruh data legalitas Berita Acara (BAMB/BAST), klasifikasi akun 1.3 Aset Tetap, spesifikasi fisik barang, penempatan ruangan RSUD, serta rekapitulasi nilai perolehan sebelum disahkan resmi ke database SIMAT-RK.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
            <button type="button" @click="goToStep(1)"
                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1 border border-slate-700">
                <span>✏️ Edit Legalitas (Langkah 1)</span>
            </button>
            <button type="button" @click="goToStep(2)"
                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1 border border-slate-700">
                <span>✏️ Edit Fisik (Langkah 2)</span>
            </button>
        </div>
    </div>

    <!-- 2. Quick Health Check Status (Grid 3 Kolom Indikator Utama) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5">
        
        <!-- Status 1: SKPD Pengirim & Dokumen BAMB -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">1. BAMB &amp; SKPD PENGIRIM</span>
                <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-mono">
                    <span x-text="formData.triwulan || 'TW I'"></span> / <span x-text="formData.tahun_perolehan || new Date().getFullYear()"></span>
                </span>
            </div>
            <div class="text-xs font-black font-mono text-indigo-300 truncate" x-text="formData.mutasi_nomor_bamb || '-'"></div>
            <div class="text-[11px] text-white truncate font-bold" x-text="formData.mutasi_asal || 'SKPD Asal belum diisi'"></div>
        </div>

        <!-- Status 2: Klasifikasi 108 & Objek KIB -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-indigo-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider">2. KLASIFIKASI 108 &amp; KIB</span>
                <span class="text-[9px] font-extrabold px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-mono" x-text="kibLabel">
                </span>
            </div>
            <div class="text-xs font-black font-mono text-indigo-300 truncate" x-text="selected108Item ? selected108Item.kode : '-'"></div>
            <div class="text-[11px] text-slate-200 truncate font-semibold" x-text="formData.nama_barang || '-'"></div>
        </div>

        <!-- Status 3: Nilai Perolehan BMD & Volume Fisik -->
        <div class="p-4 rounded-2xl bg-slate-950/80 border border-emerald-500/30 shadow-lg space-y-1 relative overflow-hidden group">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">3. NILAI BMD &amp; VOLUME TOTAL</span>
                <span class="text-[9px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-mono">
                    <span x-text="formData.jumlah_volume"></span> <span x-text="formData.satuan"></span>
                </span>
            </div>
            <div class="text-xs sm:text-sm font-black font-mono text-emerald-400 truncate" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
            <div class="text-[10.5px] text-slate-400 truncate">
                Rata-rata per <span x-text="formData.satuan || 'Unit'"></span>: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></span>
            </div>
        </div>
    </div>

    <!-- 3. Rincian Komprehensif Lembar Verifikasi Data (Tabulasi Kartu) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
        
        <!-- ===================================================================== -->
        <!-- KARTU 1: LEGALITAS BERITA ACARA (BAMB/BAST) & SKPD PENGIRIM           -->
        <!-- ===================================================================== -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-slate-800 shadow-xl space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-bold border border-indigo-500/30">1</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Legalitas Berita Acara &amp; SKPD Pengirim
                    </h3>
                </div>
                <button type="button" @click="goToStep(1)" class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-1 cursor-pointer">
                    <span>Ubah</span> &rarr;
                </button>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Instansi Asal Pengirim:</span>
                    <span class="font-extrabold text-white block truncate" x-text="formData.mutasi_asal || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nomor Dokumen BAMB:</span>
                    <span class="font-mono font-bold text-indigo-300 block truncate" x-text="formData.mutasi_nomor_bamb || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tanggal BAMB:</span>
                    <span class="font-semibold text-white block font-mono" x-text="formatTanggalIndo(formData.mutasi_tanggal) || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dasar SK Pelimpahan:</span>
                    <span class="font-mono text-slate-300 block truncate" x-text="formData.nomor_sk_dasar || '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pejabat Penyerah (OPD):</span>
                    <span class="font-semibold text-white block truncate" x-text="formData.pj_asal_nama ? (formData.pj_asal_nama + (formData.pj_asal_nip ? ' (' + formData.pj_asal_nip + ')' : '')) : '-'"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pejabat Penerima RSUD:</span>
                    <span class="font-semibold text-indigo-300 block truncate" x-text="formData.ppk_nama ? (formData.ppk_nama + (formData.ppk_nip ? ' (' + formData.ppk_nip + ')' : '')) : '-'"></span>
                </div>
            </div>

            <template x-if="formData.mutasi_keterangan">
                <div class="p-3 rounded-2xl bg-slate-900 border border-slate-800 text-[11px] text-slate-300 space-y-1">
                    <span class="text-[10px] font-bold text-slate-400 block uppercase">Alasan / Catatan Pelimpahan:</span>
                    <p class="leading-relaxed" x-text="formData.mutasi_keterangan"></p>
                </div>
            </template>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 2: KLASIFIKASI KODE 108 & SPESIFIKASI FISIK BARANG              -->
        <!-- ===================================================================== -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-indigo-500/30 shadow-xl space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-bold border border-indigo-500/30">2</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Klasifikasi 108 &amp; Rincian Spesifikasi
                    </h3>
                </div>
                <button type="button" @click="goToStep(2)" class="text-[11px] font-bold text-indigo-400 hover:text-indigo-300 flex items-center gap-1 cursor-pointer">
                    <span>Ubah</span> &rarr;
                </button>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kode Rekening Barang 108:</span>
                    <span class="font-mono font-black text-indigo-300 block text-xs" x-text="selected108Item ? (selected108Item.kode + ' • ' + selected108Item.nama) : '-'"></span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Nama Barang Pelimpahan:</span>
                        <span class="font-bold text-white block text-sm" x-text="formData.nama_barang || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kondisi Fisik Saat Diterima:</span>
                        <span class="font-extrabold inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px]"
                            :class="formData.kondisi === 'Baik' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : (formData.kondisi === 'Rusak Ringan' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30')"
                            x-text="formData.kondisi || 'Baik'"></span>
                    </div>
                </div>

                <!-- Preview Spesifikasi Khusus Sesuai KIB -->
                <div class="p-3 rounded-2xl bg-slate-900 border border-slate-800 space-y-1.5">
                    <span class="text-[10px] font-bold text-indigo-400 block uppercase" x-text="'Spesifikasi Teknis (' + kibLabel + '):'"></span>
                    
                    <!-- KIB A Tanah -->
                    <template x-if="isTanah">
                        <div class="text-[11px] text-slate-300 space-y-0.5">
                            <div>• Total Bidang: <strong class="text-white" x-text="(formData.tanah_items ? formData.tanah_items.length : 1) + ' Bidang Tanah'"></strong></div>
                            <div>• Total Luas: <strong class="text-indigo-300 font-mono" x-text="(totalLuasTanah || 0).toLocaleString('id-ID') + ' m²'"></strong></div>
                            <div>• Status Hak: <span class="text-white" x-text="formData.tanah_items && formData.tanah_items[0] ? formData.tanah_items[0].tanah_hak : 'Hak Pakai'"></span></div>
                        </div>
                    </template>

                    <!-- KIB B Mesin -->
                    <template x-if="isMesin">
                        <div class="text-[11px] text-slate-300 space-y-0.5">
                            <div>• Rincian Item: <strong class="text-white" x-text="(formData.mesin_items ? formData.mesin_items.length : 1) + ' Item/Barang'"></strong></div>
                            <div x-show="firstMesinItem?.mesin_merk || firstMesinItem?.mesin_type">
                                • Merk / Type: <strong class="text-white" x-text="(firstMesinItem?.mesin_merk || '') + ' ' + (firstMesinItem?.mesin_type || '')"></strong>
                            </div>
                            <div x-show="firstMesinItem?.mesin_no_pabrik">
                                • No. Pabrik/SN: <span class="font-mono text-indigo-300" x-text="firstMesinItem?.mesin_no_pabrik"></span>
                            </div>
                            <div x-show="firstMesinItem?.mesin_no_polisi">
                                • No. Polisi: <span class="font-mono text-amber-300 font-bold" x-text="firstMesinItem?.mesin_no_polisi"></span>
                            </div>
                        </div>
                    </template>

                    <!-- KIB C Gedung -->
                    <template x-if="isGedung">
                        <div class="text-[11px] text-slate-300 space-y-0.5">
                            <div>• Total Gedung: <strong class="text-white" x-text="(formData.gedung_items ? formData.gedung_items.length : 1) + ' Bangunan'"></strong></div>
                            <div>• Luas Lantai: <strong class="text-indigo-300 font-mono" x-text="(totalLuasGedung || 0).toLocaleString('id-ID') + ' m²'"></strong></div>
                            <div>• Konstruksi: <span class="text-white" x-text="firstGedungItem?.gedung_beton || 'Beton Bertulang'"></span></div>
                        </div>
                    </template>

                    <!-- KIB D Jaringan -->
                    <template x-if="isJaringan">
                        <div class="text-[11px] text-slate-300 space-y-0.5">
                            <div>• Total Ruas/Instalasi: <strong class="text-white" x-text="(formData.jaringan_items ? formData.jaringan_items.length : 1) + ' Ruas'"></strong></div>
                            <div>• Panjang: <strong class="text-indigo-300 font-mono" x-text="(firstJaringanItem?.jaringan_panjang_m || 0) + ' Meter'"></strong></div>
                        </div>
                    </template>

                    <!-- KIB E Lainnya -->
                    <template x-if="isLainnya">
                        <div class="text-[11px] text-slate-300 space-y-0.5">
                            <div>• Total Item: <strong class="text-white" x-text="(formData.lainnya_items ? formData.lainnya_items.length : 1) + ' Item'"></strong></div>
                            <div>• Kategori: <span class="text-white" x-text="firstLainnyaItem?.lainnya_jenis || 'Aset Tetap Lainnya'"></span></div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        <!-- ===================================================================== -->
        <!-- KARTU 3: AKUMULASI KEUANGAN & INTEGRASI NIBAR REGISTER                -->
        <!-- ===================================================================== -->
        <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 shadow-xl space-y-4 relative">
            <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                <div class="flex items-center space-x-2">
                    <span class="w-7 h-7 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs font-bold border border-emerald-500/30">3</span>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                        Akumulasi Keuangan &amp; Register NIBAR
                    </h3>
                </div>
                <span class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-950/60 px-2 py-0.5 rounded border border-emerald-500/30">
                    Otomatisasi Sistem
                </span>
            </div>

            <div class="grid grid-cols-2 gap-3 text-xs">
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Total Nilai Perolehan BMD:</span>
                    <span class="font-mono font-black text-emerald-400 block text-sm" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Jumlah Fisik Unit:</span>
                    <span class="font-mono font-bold text-white block text-sm" x-text="(formData.jumlah_volume || 1) + ' ' + (formData.satuan || 'Unit')"></span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Akun Neraca:</span>
                    <span class="font-mono text-indigo-300 block">1.3 Aset Tetap Milik Daerah</span>
                </div>
                <div>
                    <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Sumber Dana:</span>
                    <span class="font-bold text-white block">Pelimpahan SKPD Luar</span>
                </div>
            </div>
        </div>

    </div>

    <!-- 4. Preview Generator NIBAR 45-Digit untuk Setiap Unit -->
    <div class="p-6 rounded-3xl bg-slate-950/90 border border-slate-800 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2">
                <span class="text-lg">🏷️</span>
                <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wider">
                    Simulasi Preview Nomor Register NIBAR (45-Digit)
                </h3>
            </div>
            <span class="text-[10px] font-mono text-indigo-400">
                Total Digenerate: <strong x-text="formData.jumlah_volume || 1"></strong> Register
            </span>
        </div>

        <p class="text-[11px] text-slate-400">
            Setiap unit aset pelimpahan akan secara otomatis dibuatkan nomor inventaris 45 digit standar SIMAT-RK beserta tautan QR-Code fisik untuk pelabelan Kartu Inventaris Ruangan (KIR):
        </p>

        <!-- Tabel Ringkas Preview NIBAR -->
        <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 max-h-56 overflow-y-auto custom-scrollbar">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950 text-slate-400 uppercase font-mono text-[10px] tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-2.5">Unit Ke</th>
                        <th class="px-4 py-2.5">Simulasi NIBAR 45-Digit</th>
                        <th class="px-4 py-2.5">Ruangan Pemegang</th>
                        <th class="px-4 py-2.5">Kondisi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80 font-mono text-[11px]">
                    <template x-for="n in Math.min(Number(formData.jumlah_volume || 1), 10)" :key="n">
                        <tr class="hover:bg-indigo-500/5 transition-colors">
                            <td class="px-4 py-2 text-slate-400" x-text="'Unit #' + n"></td>
                            <td class="px-4 py-2 text-indigo-300 font-bold tracking-wider" x-text="generateSimulatedNibar(n)"></td>
                            <td class="px-4 py-2 text-slate-300 font-sans" x-text="selectedUnitName || 'RSUD Dr. H. Koesnandi'"></td>
                            <td class="px-4 py-2">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold"
                                    :class="formData.kondisi === 'Baik' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400'"
                                    x-text="formData.kondisi || 'Baik'"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
        <template x-if="Number(formData.jumlah_volume || 1) > 10">
            <p class="text-[10px] text-slate-500 italic">
                * Menampilkan preview 10 unit pertama dari total <span x-text="formData.jumlah_volume"></span> unit yang akan dicatat ke database.
            </p>
        </template>
    </div>

    <!-- 5. Checklist Konfirmasi & Verifikasi Data Legalitas -->
    <div class="p-6 rounded-3xl bg-slate-950/90 border border-indigo-500/40 space-y-4 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-start space-x-3.5">
            <div class="pt-0.5">
                <input type="checkbox" id="confirmVerification" x-model="isDataVerified"
                    class="w-5 h-5 rounded-lg bg-slate-900 border-slate-700 text-indigo-500 focus:ring-indigo-400 focus:ring-offset-slate-950 cursor-pointer">
            </div>
            <label for="confirmVerification" class="cursor-pointer select-none">
                <span class="text-xs sm:text-sm font-extrabold text-white block">
                    Pernyataan Verifikasi &amp; Pengesahan Serah Terima BMD
                </span>
                <span class="text-[11px] text-slate-400 mt-1 block leading-relaxed">
                    Saya menyatakan dengan sebenarnya bahwa data Berita Acara Serah Terima (BAMB/BAST), kode rekening Permendagri 108, spesifikasi fisik barang, penempatan ruangan RSUD, dan nilai perolehan telah diperiksa sesuai dengan fisik barang yang diserahterimakan dari SKPD pengirim ke RSUD Dr. H. Koesnandi. Data ini siap diintegrasikan resmi ke dalam sistem inventaris SIMAT-RK.
                </span>
            </label>
        </div>
    </div>

</div>
