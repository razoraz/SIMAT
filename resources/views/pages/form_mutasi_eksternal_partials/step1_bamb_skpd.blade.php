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
            Masukkan data legalitas pelimpahan barang dari instansi atau SKPD luar ke RSUD Dr. H. Koesnandi.
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
    <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/30 space-y-4 shadow-xl">
        <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-purple-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🔄 Informasi SKPD Asal &amp; Berita Acara BAMB</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-purple-300 bg-purple-500/10 px-2 py-0.5 rounded border border-purple-500/20">
                Mutasi Antar SKPD
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
                    class="px-2.5 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer">
                    Dinkes Bondowoso
                </button>
                <button type="button" @click="formData.mutasi_asal = 'BPKAD Kabupaten Bondowoso'"
                    class="px-2.5 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer">
                    BPKAD Bondowoso
                </button>
                <button type="button" @click="formData.mutasi_asal = 'Pemerintah Kabupaten Bondowoso'"
                    class="px-2.5 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer">
                    Setda / Pemkab
                </button>
                <button type="button" @click="formData.mutasi_asal = 'Dinas Kesehatan Provinsi Jawa Timur'"
                    class="px-2.5 py-0.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer">
                    Dinkes Prov. Jatim
                </button>
            </div>
        </div>

        <!-- Nomor & Tanggal BAMB -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Berita Acara BAMB / SK Pelimpahan <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.mutasi_nomor_bamb" required
                    placeholder="Contoh: 028/BAMB/DINKES/2026 atau SK-BUPATI/04/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal BAMB / SK Pelimpahan <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-datepicker x-model="formData.mutasi_tanggal" required
                    placeholder="dd/mm/yyyy"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>
        </div>

        <!-- Nilai Buku / Taksiran Aset Mutasi Eksternal (Rp) -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                <span>Nilai Buku / Taksiran Perolehan Aset (Rp) <span class="text-slate-400 font-normal text-[11px]">(Boleh Rp 0 jika tanpa taksiran)</span></span>
                <span class="text-[11px] font-mono text-purple-400" x-text="formatRupiah(formData.total_realisasi)"></span>
            </label>
            <input type="number" x-model.number="formData.total_realisasi" min="0" step="any"
                placeholder="0"
                class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-purple-300 font-bold font-mono focus:outline-none">
        </div>

        <!-- Catatan Pelimpahan -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Catatan / Keterangan Pelimpahan Aset
            </label>
            <textarea x-model="formData.mutasi_keterangan" rows="2"
                placeholder="Contoh: Pelimpahan sarana prasarana penunjang pelayanan rawat inap dari Dinas Kesehatan..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
        </div>
    </div>

</div>
