<!-- ========================================================================= -->
<!-- LANGKAH 3: LEMBAR VERIFIKASI & KONFIRMASI DATA PELIMPAHAN BMD SKPD         -->
<!-- ========================================================================= -->
<div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <!-- 1. Header Banner & Navigasi Cepat -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-2 border-b border-slate-800">
        <div>
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 text-xs font-bold mb-1.5">
                <span>🛡️ LANGKAH 3 DARI 3: VERIFIKASI &amp; KONFIRMASI RESMI</span>
            </div>
            <h2 class="text-xl font-black text-white tracking-tight flex items-center space-x-2">
                <span>Lembar Verifikasi Pelimpahan BMD (Mutasi Eksternal)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Pastikan seluruh data legalitas Berita Acara, kedua belah pihak, rincian fisik aset, dan nilai perolehan telah sesuai sebelum disahkan ke database SIMAT-RK.
            </p>
        </div>

        <div class="flex items-center gap-2 self-start sm:self-center shrink-0">
            <button type="button" @click="goToStep(1)"
                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-700 shadow-sm cursor-pointer">
                <span>✏️ Edit Dokumen (Langkah 1)</span>
            </button>
            <button type="button" @click="goToStep(2)"
                class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5 border border-slate-700 shadow-sm cursor-pointer">
                <span>✏️ Edit Fisik (Langkah 2)</span>
            </button>
        </div>
    </div>

    <!-- ===== BANNER ERROR INLINE LANGKAH 3 ===== -->
    <template x-if="stepErrors[3]">
        <div class="flex items-start gap-3 p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 shadow-lg shadow-rose-500/10 animate-[fadeInDown_0.25s_ease-out]">
            <span class="text-rose-400 text-lg mt-0.5 shrink-0">⚠️</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-rose-300 mb-0.5">Perhatian — Verifikasi Langkah 3 Diperlukan</p>
                <p class="text-xs text-rose-200/90 leading-relaxed" x-text="stepErrors[3]"></p>
            </div>
            <button type="button" @click="clearStepError(3)" class="ml-auto shrink-0 text-rose-400 hover:text-rose-200 transition-colors text-sm leading-none">✕</button>
        </div>
    </template>

    <!-- 2. SEKSI UTAMA 1: DOKUMEN BERITA ACARA & KEDUA BELAH PIHAK (PIHAK I & PIHAK II) -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 shadow-xl space-y-5">
        
        <!-- Header Seksi Dokumen -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-extrabold border border-indigo-500/30">1</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">
                        Dokumen Berita Acara &amp; Para Pihak (BAST)
                    </h3>
                    <p class="text-[11px] text-slate-400">Identitas Berita Acara, Pihak Pertama (SKPD Pengirim), dan Pihak Kedua (RSUD Dr. H. Koesnadi)</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">
                    Periode: <span x-text="(formData.triwulan || 'TW I') + ' · ' + (formData.tahun_perolehan || new Date().getFullYear())"></span>
                </span>
            </div>
        </div>

        <!-- Metadata Berita Acara Ringkas & Rapi -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Nomor Berita Acara (BAMB/BAST):</span>
                <span class="font-mono font-black text-indigo-300 text-xs sm:text-sm block truncate" x-text="formData.mutasi_nomor_bamb || '-'"></span>
            </div>
            
            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tanggal Dokumen BAMB:</span>
                <span class="font-semibold text-white text-xs sm:text-sm block font-mono" x-text="formatTanggalIndo(formData.mutasi_tanggal) || '-'"></span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-900/70 border border-slate-800">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Berkas Lampiran Fisik:</span>
                <template x-if="selectedFile">
                    <span class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-emerald-400 truncate max-w-full">
                        <span>📄</span>
                        <span class="truncate" x-text="selectedFile.name"></span>
                        <span class="text-[9px] text-emerald-500 font-sans font-normal">(Siap)</span>
                    </span>
                </template>
                <template x-if="!selectedFile && formData.dokumen_lampiran_path">
                    <span class="inline-flex items-center gap-1.5 text-xs font-mono font-bold text-indigo-300 truncate max-w-full">
                        <span>📄</span>
                        <span class="truncate" x-text="formData.dokumen_lampiran_path.split('/').pop()"></span>
                        <a :href="'/storage/' + formData.dokumen_lampiran_path" target="_blank" class="text-indigo-400 hover:underline font-sans text-[10px] ml-1">Buka ↗</a>
                    </span>
                </template>
                <template x-if="!selectedFile && !formData.dokumen_lampiran_path">
                    <span class="text-xs text-slate-500 italic block">Tidak ada berkas (Opsional)</span>
                </template>
            </div>
        </div>

        <!-- Kolom Komparasi Berdampingan: PIHAK PERTAMA vs PIHAK KEDUA -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 pt-1">
            
            <!-- BLOK PIHAK PERTAMA (YANG MENYERAHKAN - SKPD PENGIRIM) -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3 relative flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs">📤</span>
                            <span class="text-xs font-black text-amber-300 uppercase tracking-wider">PIHAK PERTAMA (Yang Menyerahkan)</span>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">
                            SKPD Asal
                        </span>
                    </div>

                    <div class="space-y-2.5 text-xs pt-1">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Instansi / SKPD Pengirim:</span>
                            <span class="font-extrabold text-white text-sm block" x-text="formData.mutasi_asal || 'Belum diisi'"></span>
                        </div>

                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Alamat Instansi Pengirim:</span>
                            <span class="text-slate-300 text-xs block leading-relaxed flex items-center gap-1.5" x-show="formData.alamat_instansi">
                                <span class="text-cyan-400">📍</span>
                                <span x-text="formData.alamat_instansi"></span>
                            </span>
                            <span class="text-slate-500 italic text-xs block" x-show="!formData.alamat_instansi">-</span>
                        </div>

                        <div x-show="formData.nomor_sk_dasar" class="pt-1">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Dasar Pelimpahan / SK Bupati:</span>
                            <span class="text-indigo-300 font-mono text-xs block font-bold" x-text="formData.nomor_sk_dasar"></span>
                        </div>

                        <div class="pt-2 border-t border-slate-800/60">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-1">Pejabat Penyerah:</span>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-white flex items-center gap-1 text-xs">
                                    <span>👤</span>
                                    <span x-text="formData.pj_asal_nama || 'Pejabat Penyerah OPD Pengirim'"></span>
                                </span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700"
                                    x-show="formData.pj_asal_nip && formData.pj_asal_nip !== '-'"
                                    x-text="'NIP: ' + formData.pj_asal_nip"></span>
                            </div>
                            <div class="text-[11px] text-amber-300 font-semibold mt-1" x-show="formData.pj_asal_jabatan" x-text="'Jabatan: ' + formData.pj_asal_jabatan"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- BLOK PIHAK KEDUA (YANG MENERIMA - RSUD DR. H. KOESNADI) -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3 relative flex flex-col justify-between"
                 x-data="{ isEditPpk: false }">
                <div>
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs">📥</span>
                            <span class="text-xs font-black text-indigo-300 uppercase tracking-wider">PIHAK KEDUA (Yang Menerima)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">
                                RSUD Dr. H. Koesnadi
                            </span>
                            <button type="button" @click="isEditPpk = !isEditPpk" 
                                class="text-[10.5px] text-indigo-400 hover:text-indigo-200 font-bold flex items-center gap-1 cursor-pointer transition-colors">
                                <span x-text="isEditPpk ? 'Selesai ✓' : '✏️ Ubah'"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Mode Tampilan Normal (Bersih, Rapi & Seimbang dengan Pihak I) -->
                    <div x-show="!isEditPpk" class="space-y-2.5 text-xs pt-1">
                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Instansi Penerima:</span>
                            <span class="font-extrabold text-white text-sm block">RSUD Dr. H. Koesnadi Bondowoso</span>
                        </div>

                        <div>
                            <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Unit / Ruangan Penempatan:</span>
                            <span class="text-slate-300 text-xs block leading-relaxed flex items-center gap-1.5">
                                <span class="text-indigo-400">🏢</span>
                                <span x-text="selectedUnitName || 'RSUD Dr. H. Koesnandi (Semua Unit)'"></span>
                            </span>
                        </div>

                        <div class="pt-2 border-t border-slate-800/60">
                            <span class="text-[10px] font-semibold text-slate-400 block mb-1">Pengurus Barang Pengguna:</span>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="font-bold text-white flex items-center gap-1 text-xs">
                                    <span>👤</span>
                                    <span x-text="formData.ppk_nama || 'BUDI HARTONO, S.Sos'"></span>
                                </span>
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700"
                                    x-text="'NIP: ' + (formData.ppk_nip || '19760229 200801 1 010')"></span>
                            </div>
                            <div class="text-[11px] text-indigo-300 font-semibold mt-1">Jabatan: Pengurus Barang Pengguna RSUD</div>
                        </div>
                    </div>

                    <!-- Mode Edit Pejabat Penerima (Muncul saat tombol Ubah diklik) -->
                    <div x-show="isEditPpk" x-cloak class="space-y-3 text-xs pt-1">
                        <div class="p-3.5 rounded-xl bg-slate-950 border border-indigo-500/40 space-y-2.5 shadow-inner">
                            <div>
                                <label class="block text-[10.5px] font-bold text-slate-300 mb-1">Pengurus Barang Pengguna RSUD:</label>
                                <input type="text" x-model="formData.ppk_nama"
                                    list="pejabat-rsud-list-step3"
                                    @input="
                                        const match = pejabatsList.find(p => p.nama === formData.ppk_nama);
                                        if (match && match.nip) {
                                            formData.ppk_nip = match.nip;
                                        }
                                    "
                                    placeholder="BUDI HARTONO, S.Sos"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none">
                                <datalist id="pejabat-rsud-list-step3">
                                    <template x-for="(pj, pIdx) in pejabatsList" :key="pIdx">
                                        <option :value="pj.nama" x-text="pj.nama + (pj.nip ? ' (' + pj.nip + ')' : '')"></option>
                                    </template>
                                </datalist>
                            </div>

                            <div>
                                <label class="block text-[10.5px] font-bold text-slate-300 mb-1">NIP Pengurus Barang RSUD:</label>
                                <input type="text" x-model="formData.ppk_nip"
                                    placeholder="19760229 200801 1 010"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>

                            <button type="button" @click="isEditPpk = false" 
                                class="w-full py-1.5 rounded-lg bg-indigo-500 hover:bg-indigo-400 text-white font-bold text-xs transition-colors cursor-pointer shadow-md">
                                Simpan Perubahan Pejabat ✓
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Status Berkas Lampiran BAMB / BAST (Feedback Visual Sebelum Simpan) -->
        <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs flex items-center justify-between flex-wrap gap-2">
            <div class="flex items-center space-x-2.5">
                <span class="text-base" x-text="selectedFile ? '📁' : (isEdit && formData.dokumen_lampiran_path ? '📄' : '📎')"></span>
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Berkas Lampiran Berita Acara (BAMB / BAST):</span>
                    <template x-if="selectedFile">
                        <span class="font-mono font-bold text-emerald-400 flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span x-text="selectedFile.name"></span>
                            <span class="text-slate-400 text-[10px] font-normal" x-text="'(' + (selectedFile.size / 1024).toFixed(1) + ' KB) • Siap diunggah'"></span>
                        </span>
                    </template>
                    <template x-if="!selectedFile && isEdit && formData.dokumen_lampiran_path">
                        <span class="font-mono font-bold text-indigo-300 flex items-center gap-1.5 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400"></span>
                            <span x-text="formData.dokumen_lampiran_path.split('/').pop()"></span>
                            <span class="text-slate-400 text-[10px] font-normal">• Tersimpan di sistem</span>
                        </span>
                    </template>
                    <template x-if="!selectedFile && (!isEdit || !formData.dokumen_lampiran_path)">
                        <span class="text-slate-400 italic block mt-0.5">Tidak ada berkas yang dilampirkan (Boleh dikosongkan/opsional)</span>
                    </template>
                </div>
            </div>
            <div>
                <span class="px-2.5 py-1 rounded-xl text-[10px] font-mono font-bold border"
                    :class="selectedFile ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : (isEdit && formData.dokumen_lampiran_path ? 'bg-indigo-500/15 text-indigo-300 border-indigo-500/30' : 'bg-slate-800 text-slate-400 border-slate-700')"
                    x-text="selectedFile ? 'Berkas Baru Terpilih ✓' : (isEdit && formData.dokumen_lampiran_path ? 'Berkas Tersimpan ✓' : 'Tanpa Lampiran')">
                </span>
            </div>
        </div>

        <!-- Catatan Tambahan Jika Ada -->
        <template x-if="formData.mutasi_keterangan">
            <div class="p-3.5 rounded-2xl bg-slate-900/60 border border-slate-800 text-xs text-slate-300 space-y-1">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan / Alasan Pelimpahan:</span>
                <p class="leading-relaxed text-slate-300" x-text="formData.mutasi_keterangan"></p>
            </div>
        </template>
    </div>

    <!-- 3. SEKSI UTAMA 2: RINCIAN OBJEK BARANG 108 & SPESIFIKASI TEKNIS FISIK -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 shadow-xl space-y-5">
        
        <!-- Header Seksi Objek Barang -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-extrabold border border-indigo-500/30">2</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">
                        Rincian Objek Barang, Nilai &amp; Spesifikasi Fisik
                    </h3>
                    <p class="text-[11px] text-slate-400">Klasifikasi Kode Rekening 108, nama barang, kondisi, spesifikasi KIB, dan nilai perolehan</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold px-2.5 py-1 rounded-lg bg-indigo-500/10 text-indigo-300 border border-indigo-500/20" x-text="kibLabel"></span>
                <span class="text-[10px] font-mono font-bold px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-300 border border-emerald-500/20">
                    Akun 1.3 Aset Tetap
                </span>
            </div>
        </div>

        <!-- Banner Ringkasan Objek & Nilai Total -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-3">
            
            <!-- Objek Barang & Kode 108 -->
            <div class="lg:col-span-2 p-4 rounded-2xl bg-slate-900/70 border border-slate-800 space-y-1.5">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Kode Rekening 108:</span>
                    <span class="font-extrabold inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] border transition-all"
                        :class="{
                            'bg-emerald-500/15 text-emerald-400 border-emerald-500/30': !kondisiSummary.includes('KB') && !kondisiSummary.includes('RB') && !kondisiSummary.includes('Kurang') && !kondisiSummary.includes('Rusak'),
                            'bg-amber-500/15 text-amber-400 border-amber-500/30': (kondisiSummary.includes('KB') || kondisiSummary.includes('Kurang')) && !kondisiSummary.includes('RB') && !kondisiSummary.includes('Rusak'),
                            'bg-rose-500/15 text-rose-400 border-rose-500/30': kondisiSummary.includes('RB') || kondisiSummary.includes('Rusak')
                        }"
                        x-text="'Kondisi: ' + kondisiSummary"></span>
                </div>
                <div class="font-mono font-black text-indigo-300 text-xs sm:text-sm truncate" x-text="selected108Item ? (selected108Item.kode + ' • ' + selected108Item.nama) : 'Kode 108 belum dipilih'"></div>
                <div class="text-sm font-black text-white truncate pt-0.5" x-text="formData.nama_barang || 'Nama barang belum diisi'"></div>
            </div>

            <!-- Total Nilai & Volume -->
            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-950/30 to-slate-900/90 border border-emerald-500/30 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Total Nilai Perolehan:</span>
                        <span class="font-mono font-bold text-white text-xs" x-text="(formData.jumlah_volume || 1) + ' ' + (formData.satuan || 'Unit')"></span>
                    </div>
                    <div class="font-mono font-black text-emerald-400 text-lg sm:text-xl tracking-tight mt-1" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
                </div>
                <div class="text-[10px] text-slate-400 font-mono pt-1.5 border-t border-slate-800">
                    Rata-rata: Rp <span x-text="formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></span> / <span x-text="formData.satuan || 'Unit'"></span>
                </div>
            </div>

        </div>

        <!-- Spesifikasi Fisik Sesuai KIB Aktif -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                <span class="text-xs font-extrabold text-indigo-300 uppercase tracking-wider" x-text="'Spesifikasi Teknis Fisik (' + kibLabel + ')'"></span>
                <span class="text-[10px] text-slate-400">Parameter KIB Permendagri 108</span>
            </div>

            <!-- KIB A Tanah -->
            <template x-if="isTanah">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Jumlah Bidang:</span>
                        <span class="font-extrabold text-white block" x-text="(formData.tanah_items ? formData.tanah_items.length : 1) + ' Bidang'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Total Luas Tanah:</span>
                        <span class="font-mono font-bold text-indigo-300 block" x-text="(totalLuasTanah || 0).toLocaleString('id-ID') + ' m²'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Hak Tanah:</span>
                        <span class="font-semibold text-white block" x-text="formData.tanah_items && formData.tanah_items[0] ? formData.tanah_items[0].tanah_hak : 'Hak Pakai'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Letak / Alamat Fisik:</span>
                        <span class="text-emerald-400 font-semibold block truncate" x-text="(formData.tanah_items && formData.tanah_items[0] ? formData.tanah_items[0].tanah_alamat : '') || formData.alamat_barang || '-'"></span>
                    </div>
                </div>
            </template>

            <!-- KIB B Peralatan & Mesin -->
            <template x-if="isMesin">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Merk / Type:</span>
                        <span class="font-bold text-white block truncate" x-text="(firstMesinItem?.mesin_merk || '-') + ' ' + (firstMesinItem?.mesin_type || '')"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">No. Pabrik / Serial Number:</span>
                        <span class="font-mono font-bold text-indigo-300 block truncate" x-text="firstMesinItem?.mesin_no_pabrik || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">No. Polisi / Plat:</span>
                        <span class="font-mono font-bold text-amber-300 block truncate" x-text="firstMesinItem?.mesin_no_polisi || '-'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Ruang / Pemegang:</span>
                        <span class="font-bold text-slate-200 block truncate" x-text="firstMesinItem?.ruang_pemegang || selectedUnitName || 'RSUD Dr. H. Koesnandi'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Akuntansi:</span>
                        <span class="font-mono font-bold text-[10.5px] px-2 py-0.5 rounded"
                            :class="firstMesinItem?.is_extracom ? 'text-cyan-300 bg-cyan-950/60 border border-cyan-500/30' : 'text-purple-300 bg-purple-950/60 border border-purple-500/30'"
                            x-text="firstMesinItem?.is_extracom ? 'Ekstrakomtabel (≤ 300rb)' : 'Aset Tetap Reguler'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Bahan / Material:</span>
                        <span class="text-white block" x-text="firstMesinItem?.mesin_bahan || '-'"></span>
                    </div>
                </div>
            </template>

            <!-- KIB C Gedung & Bangunan -->
            <template x-if="isGedung">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Jumlah Bangunan:</span>
                        <span class="font-bold text-white block" x-text="(formData.gedung_items ? formData.gedung_items.length : 1) + ' Gedung'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas Total Lantai:</span>
                        <span class="font-mono font-bold text-indigo-300 block" x-text="(totalLuasGedung || 0).toLocaleString('id-ID') + ' m²'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi:</span>
                        <span class="text-white block" x-text="firstGedungItem?.gedung_beton || 'Beton Bertulang'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Letak / Alamat Fisik:</span>
                        <span class="text-emerald-400 font-semibold block truncate" x-text="firstGedungItem?.gedung_alamat || formData.alamat_barang || '-'"></span>
                    </div>
                </div>
            </template>

            <!-- KIB D Jaringan & Irigasi -->
            <template x-if="isJaringan">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Total Ruas / Titik:</span>
                        <span class="font-bold text-white block" x-text="(formData.jaringan_items ? formData.jaringan_items.length : 1) + ' Ruas'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Luas / Panjang Jaringan:</span>
                        <span class="font-mono font-bold text-teal-300 block" x-text="(totalLuasJaringan || 0).toLocaleString('id-ID') + ' m²'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Konstruksi:</span>
                        <span class="text-white block" x-text="firstJaringanItem?.jaringan_beton || 'Beton Bertulang'"></span>
                    </div>
                    <div>
                        <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Letak / Alamat Fisik:</span>
                        <span class="text-emerald-400 font-semibold block truncate" x-text="firstJaringanItem?.jaringan_alamat || formData.alamat_barang || '-'"></span>
                    </div>
                </div>
            </template>

            <!-- KIB E Aset Tetap Lainnya -->
            <template x-if="isLainnya">
                <div class="space-y-3">
                    <template x-if="formData.lainnya_items && formData.lainnya_items.length > 1">
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between text-xs text-slate-400">
                                <span>Rincian <strong class="text-white" x-text="formData.lainnya_items.length"></strong> Item Aset Tetap Lainnya:</span>
                                <span class="font-mono text-purple-400 font-bold" x-text="'Total: ' + totalVolumeLainnya + ' ' + (formData.satuan || 'Item')"></span>
                            </div>
                            <div class="space-y-2 max-h-72 overflow-y-auto pr-1 custom-scrollbar">
                                <template x-for="(lItem, lIdx) in formData.lainnya_items" :key="lIdx">
                                    <div class="p-3 rounded-2xl bg-slate-900/90 border border-slate-800/80 hover:border-purple-500/40 transition-all space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 font-mono text-[10px] font-bold flex items-center justify-center border border-purple-500/30" x-text="lIdx + 1"></span>
                                                <span class="font-bold text-white text-xs truncate max-w-[200px]" x-text="lItem.lainnya_judul || lItem.lainnya_nama_barang || ('Item #' + (lIdx + 1))"></span>
                                                <span class="text-[9px] px-1.5 py-0.5 rounded font-bold border"
                                                      :class="lItem.is_extracom ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40' : 'bg-purple-500/20 text-purple-300 border-purple-500/40'"
                                                      x-text="lItem.is_extracom ? '📦 Extracom' : '⚙️ Reguler'">
                                                </span>
                                            </div>
                                            <span class="text-[10px] font-mono font-bold px-2 py-0.5 rounded-md bg-slate-800 text-purple-300 border border-slate-700 shrink-0" x-text="(lItem.lainnya_jumlah || 1) + ' ' + (lItem.lainnya_satuan || 'Buah')"></span>
                                        </div>
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px] text-slate-300 pt-1 border-t border-slate-800/60">
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Kategori:</span>
                                                <span class="font-semibold text-purple-300 block" x-text="lItem.kib_e_type === 'kesenian' ? '🎨 Kesenian' : (lItem.kib_e_type === 'hewan_tumbuhan' ? '🌿 Hewan/Tanaman' : '📚 Buku Pustaka')"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Pencipta / Asal:</span>
                                                <span class="text-slate-300 truncate block" x-text="lItem.lainnya_pencipta || lItem.lainnya_asal_daerah || lItem.lainnya_spesifikasi || '-'"></span>
                                            </div>
                                            <div>
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Ruang / Pemegang:</span>
                                                <span class="text-slate-300 truncate block" x-text="lItem.ruang_pemegang || selectedUnitName || 'RSUD Dr. H. Koesnadi'"></span>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-[9.5px] text-slate-500 block uppercase">Taksiran Nilai:</span>
                                                <span class="font-mono font-bold text-emerald-400 block" x-text="'Rp ' + formatRupiah(getLainnyaSubtotal(lItem))"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                    <template x-if="!formData.lainnya_items || formData.lainnya_items.length <= 1">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                            <div class="col-span-2">
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Judul / Nama Barang:</span>
                                <span class="font-bold text-purple-300 block" x-text="firstLainnyaItem?.lainnya_judul || firstLainnyaItem?.lainnya_nama_barang || formData.nama_barang || '-'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Kategori KIB E:</span>
                                <span class="font-semibold text-white block" x-text="firstLainnyaItem?.kib_e_type === 'kesenian' ? '🎨 Kesenian & Budaya' : (firstLainnyaItem?.kib_e_type === 'hewan_tumbuhan' ? '🌿 Hewan & Tanaman' : '📚 Buku Pustaka')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Status Akuntansi:</span>
                                <span class="font-mono font-bold text-[10.5px] px-2 py-0.5 rounded"
                                    :class="firstLainnyaItem?.is_extracom ? 'text-cyan-300 bg-cyan-950/60 border border-cyan-500/30' : 'text-purple-300 bg-purple-950/60 border border-purple-500/30'"
                                    x-text="firstLainnyaItem?.is_extracom ? 'Ekstrakomtabel (≤ 300rb)' : 'Aset Tetap Reguler'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Pencipta / Penulis:</span>
                                <span class="text-white block" x-text="firstLainnyaItem?.lainnya_pencipta || '-'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Spesifikasi / Penerbit:</span>
                                <span class="text-white block" x-text="firstLainnyaItem?.lainnya_spesifikasi || '-'"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Tahun / Dimensi:</span>
                                <span class="text-white block" x-text="(firstLainnyaItem?.lainnya_tahun || '-') + (firstLainnyaItem?.lainnya_ukuran ? ' / ' + firstLainnyaItem?.lainnya_ukuran : '')"></span>
                            </div>
                            <div>
                                <span class="text-[10px] font-semibold text-slate-400 block mb-0.5">Ruang / Pemegang:</span>
                                <span class="text-slate-200 block truncate" x-text="firstLainnyaItem?.ruang_pemegang || selectedUnitName || 'RSUD Dr. H. Koesnadi'"></span>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>

    </div>

    <!-- 4. SEKSI UTAMA 3: SIMULASI PREVIEW REGISTER NIBAR (45-DIGIT) -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 shadow-xl space-y-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-indigo-500/20 text-indigo-400 flex items-center justify-center text-xs font-extrabold border border-indigo-500/30">3</span>
                <div>
                    <h3 class="text-sm font-extrabold text-white uppercase tracking-wider">
                        Simulasi Preview Nomor Register NIBAR (45-Digit)
                    </h3>
                    <p class="text-[11px] text-slate-400">Nomor inventaris fisik dan barcode/QR yang akan otomatis dibuatkan oleh sistem</p>
                </div>
            </div>
            
            <span class="text-[10px] font-mono text-indigo-400 bg-indigo-500/10 px-2.5 py-1 rounded-lg border border-indigo-500/20 font-bold">
                Total Digenerate: <strong x-text="formData.jumlah_volume || 1"></strong> Unit
            </span>
        </div>

        <!-- Tabel Ringkas Preview NIBAR (Membaca Rincian Tiap Barang, Bukan Duplikat) -->
        <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-900/60 max-h-56 overflow-y-auto custom-scrollbar">
            <table class="w-full text-left text-xs min-w-[600px]">
                <thead class="bg-slate-950 text-slate-400 uppercase font-mono text-[10px] tracking-wider border-b border-slate-800 sticky top-0 z-10 backdrop-blur-md">
                    <tr>
                        <th class="px-4 py-2.5">Unit Ke</th>
                        <th class="px-4 py-2.5">Nama / Rincian Barang</th>
                        <th class="px-4 py-2.5">Simulasi NIBAR 45-Digit</th>
                        <th class="px-4 py-2.5">Ruangan Pemegang</th>
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
                            <td class="px-4 py-2 text-indigo-300 font-bold tracking-wider" x-text="generateSimulatedNibar(u.unitNumber)"></td>
                            <td class="px-4 py-2 text-slate-300 font-sans">
                                <span class="inline-flex items-center gap-1 truncate max-w-[180px]" :title="u.ruang">
                                    <span class="text-cyan-400 text-xs">📍</span>
                                    <span x-text="u.ruang"></span>
                                </span>
                            </td>
                            <td class="px-4 py-2 text-center">
                                <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-bold border inline-block"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-400 border-emerald-500/30': u.kondisi === 'Baik' || u.kondisi === 'B',
                                        'bg-amber-500/15 text-amber-400 border-amber-500/30': u.kondisi === 'Kurang Baik' || u.kondisi === 'KB' || u.kondisi === 'Rusak Ringan' || u.kondisi === 'RR',
                                        'bg-rose-500/15 text-rose-400 border-rose-500/30': u.kondisi === 'Rusak Berat' || u.kondisi === 'RB' || u.kondisi === 'Rusak'
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

    <!-- 5. SEKSI UTAMA 4: CHECKLIST KONFIRMASI & PENGESAHAN VERIFIKASI AKHIR -->
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
                        Pernyataan Verifikasi &amp; Pengesahan Serah Terima BMD <span class="text-rose-400">*</span>
                    </span>
                    <span x-show="isDataVerified" class="text-[10px] font-extrabold px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1">
                        <span>✓</span>
                        <span>Data Telah Diverifikasi &amp; Siap Disahkan</span>
                    </span>
                </div>
                <p class="text-xs text-slate-400 leading-relaxed">
                    Saya menyatakan dengan sebenarnya bahwa data Berita Acara Serah Terima (BAMB/BAST), identitas pihak penyerah SKPD &amp; penerima RSUD, kode rekening Permendagri 108, spesifikasi fisik barang, penempatan ruangan RSUD, dan nilai perolehan telah diperiksa sesuai dengan fisik barang yang diserahterimakan dari SKPD pengirim ke RSUD Dr. H. Koesnandi. Data ini siap diintegrasikan resmi ke dalam sistem inventaris SIMAT-RK.
                </p>
            </label>
        </div>
    </div>

</div>
