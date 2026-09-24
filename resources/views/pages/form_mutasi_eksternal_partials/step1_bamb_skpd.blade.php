<!-- ========================================================================= -->
<!-- LANGKAH 1: DOKUMEN BAMB & SKPD PENGIRIM                                  -->
<!-- ========================================================================= -->
<div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div class="border-b border-slate-800 pb-4">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20 text-xs font-bold mb-2">
            <span>Langkah 1 dari 3</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span>📜 Dokumen Berita Acara Mutasi Barang (BAMB) &amp; SKPD Asal</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">
            Lengkapi data legalitas serah terima pelimpahan Barang Milik Daerah (BMD) dari instansi atau SKPD luar ke RSUD Dr. H. Koesnadi.
        </p>
    </div>

    <!-- Periode Anggaran & Triwulan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-950/70 border border-slate-800">
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Tahun Anggaran Pembukuan <span class="text-rose-400">*</span>
            </label>
            <input type="number" x-model.number="formData.tahun_perolehan" min="1990" max="2100" required
                class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-mono">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Triwulan Pembukuan <span class="text-rose-400">*</span>
            </label>
            <select x-model="formData.triwulan" required
                class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                <option value="TW I">Triwulan I (Januari - Maret)</option>
                <option value="TW II">Triwulan II (April - Juni)</option>
                <option value="TW III">Triwulan III (Juli - September)</option>
                <option value="TW IV">Triwulan IV (Oktober - Desember)</option>
            </select>
        </div>
    </div>

    <!-- Bagian BAMB & SKPD Asal -->
    <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <span class="text-xs font-extrabold text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🔄 Informasi SKPD Asal &amp; Berita Acara BAMB</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-purple-300 bg-purple-500/10 px-2.5 py-0.5 rounded-lg border border-purple-500/20">
                Pelimpahan Antar-SKPD
            </span>
        </div>

        <!-- SKPD Asal Pelimpahan -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Instansi / SKPD Asal Pelimpahan <span class="text-rose-400">*</span>
            </label>
            <input type="text" x-model="formData.mutasi_asal" required
                placeholder="Contoh: Dinas Kesehatan Kabupaten Bondowoso, BPKAD Bondowoso, RSUD Besuki..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none font-bold">
            
            <!-- Rekomendasi Cepat SKPD -->
            <div class="mt-2 flex flex-wrap items-center gap-1.5">
                <span class="text-[10px] text-slate-500 font-semibold mr-1">Rekomendasi Cepat:</span>
                <button type="button" @click="formData.mutasi_asal = 'Dinas Kesehatan Kabupaten Bondowoso'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 transition cursor-pointer">
                    Dinkes Bondowoso
                </button>
                <button type="button" @click="formData.mutasi_asal = 'BPKAD Kabupaten Bondowoso'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 transition cursor-pointer">
                    BPKAD Bondowoso
                </button>
                <button type="button" @click="formData.mutasi_asal = 'Pemerintah Kabupaten Bondowoso'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 transition cursor-pointer">
                    Setda / Pemkab
                </button>
                <button type="button" @click="formData.mutasi_asal = 'Dinas Kesehatan Provinsi Jawa Timur'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 transition cursor-pointer">
                    Dinkes Prov. Jatim
                </button>
            </div>
        </div>

        <!-- Nomor & Tanggal BAMB + Nomor SK Bupati/Dasar Hukum -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Berita Acara BAMB / BAST <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.mutasi_nomor_bamb" required
                    placeholder="Contoh: 028/014/BAST-OPD/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal BAMB / BAST <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-datepicker x-model="formData.mutasi_tanggal" required
                    placeholder="dd/mm/yyyy"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-mono">
            </div>
            <div class="sm:col-span-2 lg:col-span-1">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor SK Penetapan / Dasar Hukum <span class="text-slate-400 font-normal text-[11px]">(Opsional)</span>
                </label>
                <input type="text" x-model="formData.nomor_sk_dasar"
                    placeholder="Contoh: SK-BUPATI/028/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>
        </div>

        <!-- Identitas Pejabat Penyerah (Pihak Pertama: SKPD Pengirim) -->
        <div class="p-4 rounded-xl bg-slate-900/60 border border-slate-800 space-y-3">
            <span class="text-[11px] font-extrabold text-amber-300 uppercase tracking-wider block flex items-center gap-1.5">
                <span>👤 Pejabat yang Menyerahkan (Pihak Pertama / SKPD Pengirim)</span>
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1">Nama Pejabat</label>
                    <input type="text" x-model="formData.pj_asal_nama"
                        placeholder="Nama Lengkap & Gelar Pejabat"
                        class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1">NIP Pejabat</label>
                    <input type="text" x-model="formData.pj_asal_nip"
                        placeholder="NIP: 19xxxxxxxxxxxx"
                        class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                </div>
                <div>
                    <label class="block text-[11px] font-semibold text-slate-400 mb-1">Jabatan di SKPD Asal</label>
                    <input type="text" x-model="formData.pj_asal_jabatan"
                        placeholder="Pengurus Barang / PPK Asal"
                        class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>
            </div>
        </div>

        <!-- Nilai Buku / Taksiran Aset Mutasi Eksternal (Rp) -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                <span>Nilai Buku / Taksiran Perolehan Aset (Rp) <span class="text-slate-400 font-normal text-[11px]">(Boleh Rp 0 jika tanpa taksiran)</span></span>
                <span class="text-[11px] font-mono text-purple-400 font-extrabold" x-text="formatRupiah(formData.total_realisasi)"></span>
            </label>
            <input type="number" x-model.number="formData.total_realisasi" min="0" step="any"
                placeholder="0"
                class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-purple-300 font-bold font-mono focus:outline-none">
        </div>

        <!-- Upload File Lampiran BAST Fisik (PDF) -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                <span>📎 Berkas Scan Dokumen BAST / SK Pelimpahan (PDF) <span class="text-slate-400 font-normal text-[11px]">(Opsional, Maks. 10MB)</span></span>
                <template x-if="formData.dokumen_lampiran_path">
                    <span class="text-[10.5px] text-emerald-400 font-semibold flex items-center gap-1">
                        <span>✓ Berkas sudah tersimpan</span>
                    </span>
                </template>
            </label>
            
            <div class="flex flex-col sm:flex-row items-center gap-3">
                <label class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-purple-300 border border-purple-500/30 hover:border-purple-400 text-xs font-bold transition flex items-center justify-center space-x-2 cursor-pointer shadow-sm">
                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span x-text="selectedFile ? 'Ganti File Dokumen' : 'Pilih File PDF BAST'"></span>
                    <input type="file" @change="handleFileSelect($event)" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                </label>

                <template x-if="selectedFile">
                    <div class="flex items-center space-x-2 text-xs bg-purple-950/40 border border-purple-500/30 px-3 py-1.5 rounded-xl">
                        <span class="text-purple-300 font-medium truncate max-w-[280px]" x-text="selectedFile.name"></span>
                        <span class="text-[10px] text-slate-400 font-mono" x-text="'(' + (selectedFile.size / 1024).toFixed(0) + ' KB)'"></span>
                        <button type="button" @click="selectedFile = null" class="text-rose-400 hover:text-white font-bold ml-1 cursor-pointer" title="Batalkan file">&times;</button>
                    </div>
                </template>

                <template x-if="!selectedFile && formData.dokumen_lampiran_path">
                    <div class="text-xs text-slate-400 flex items-center space-x-2">
                        <span>File lampiran tersimpan:</span>
                        <a :href="'/storage/' + formData.dokumen_lampiran_path" target="_blank" class="text-cyan-400 hover:underline font-mono text-[11px] font-bold">Lihat Berkas BAST</a>
                    </div>
                </template>
            </div>
        </div>

        <!-- Catatan Pelimpahan -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Catatan / Uraian Pelimpahan Aset
            </label>
            <textarea x-model="formData.mutasi_keterangan" rows="2"
                placeholder="Contoh: Pelimpahan sarana prasarana penunjang pelayanan rawat inap dari Dinas Kesehatan Bondowoso..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
        </div>
    </div>

</div>
