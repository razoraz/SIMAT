<!-- ========================================================================= -->
<!-- LANGKAH 1: DOKUMEN BAST HIBAH & PIHAK PEMBERI                             -->
<!-- ========================================================================= -->
<div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-300 border border-amber-400/20 text-xs font-bold mb-2">
            <span>🎁 LANGKAH 1 DARI 3: LEGALITAS HIBAH</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-amber-400/10 text-amber-400 text-sm">📜</span>
            <span>Langkah 1: Dokumen BAST &amp; Informasi Pihak Pemberi Hibah</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Masukkan legalitas penyerahan (BAST Hibah), periode pembukuan, dan pihak pemberi hibah (tanpa pagu belanja APBD &amp; tanpa rekanan tender).
        </p>
    </div>

    <!-- Periode Anggaran & Triwulan -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/30 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <label class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                <span>📅 Periode Pembukuan Aset</span>
                <span class="text-rose-400">*</span>
            </label>
            <span class="text-[10px] text-slate-400 font-mono">Tahun &amp; Triwulan Pencatatan SIMAT-RK</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span>Tahun Anggaran Pembukuan <span class="text-rose-400">*</span></span>
                    <span class="text-[10px] text-amber-400 font-mono">1990 - 2100</span>
                </label>
                <input type="number" x-model.number="formData.tahun_perolehan" min="1990" max="2100" required
                    placeholder="Contoh: 2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-mono font-bold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Triwulan Pembukuan <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.triwulan" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-semibold">
                    <option value="TW I">Triwulan I (Januari - Maret)</option>
                    <option value="TW II">Triwulan II (April - Juni)</option>
                    <option value="TW III">Triwulan III (Juli - September)</option>
                    <option value="TW IV">Triwulan IV (Oktober - Desember)</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Bagian BAST & Pemberi Hibah -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🎁 Informasi Pihak Pemberi &amp; Legalitas BAST</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded-lg border border-amber-400/20">
                Non-Belanja Modal APBD
            </span>
        </div>

        <!-- Pihak Pemberi Hibah -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Instansi / Lembaga Pemberi Hibah <span class="text-rose-400">*</span>
            </label>
            <input type="text" x-model="formData.hibah_pemberi" required
                placeholder="Contoh: Kementerian Kesehatan RI, Dinas Kesehatan Provinsi Jawa Timur..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none font-bold">
            
            <!-- Rekomendasi Cepat Pemberi Hibah -->
            <div class="mt-2.5 flex flex-wrap items-center gap-1.5">
                <span class="text-[10px] text-slate-500 font-semibold mr-1">Rekomendasi Cepat:</span>
                <button type="button" @click="formData.hibah_pemberi = 'Kementerian Kesehatan Republik Indonesia'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    Kemenkes RI
                </button>
                <button type="button" @click="formData.hibah_pemberi = 'Dinas Kesehatan Provinsi Jawa Timur'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    Dinkes Prov. Jatim
                </button>
                <button type="button" @click="formData.hibah_pemberi = 'Pemerintah Kabupaten Bondowoso'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    Pemkab Bondowoso
                </button>
                <button type="button" @click="formData.hibah_pemberi = 'Donatur Swasta / Yayasan CSR'"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors">
                    Donatur Swasta / CSR
                </button>
            </div>
        </div>

        <!-- Nomor & Tanggal BAST -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor BAST Hibah <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.hibah_nomor_bast" required
                    placeholder="Contoh: 028/BAST-HB/KEMENKES/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal BAST Hibah <span class="text-rose-400">*</span>
                </label>
                <input type="date" x-model="formData.hibah_tanggal_bast" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>
        </div>

        <!-- Nilai Taksiran Buku Hibah -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800">
            <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                <span>Nilai Taksiran / Buku Hibah (Rp) <span class="text-slate-400 font-normal text-[11px]">(Boleh Rp 0 jika hibah tanpa taksiran harga)</span></span>
                <span class="text-xs font-mono text-emerald-400 font-black" x-text="formatRupiah(formData.total_realisasi)"></span>
            </label>
            <div class="relative">
                <span class="absolute left-3.5 top-2.5 text-slate-500 text-xs font-bold font-mono">Rp</span>
                <input type="text" 
                    :value="formData.total_realisasi ? Number(formData.total_realisasi).toLocaleString('id-ID') : ''"
                    @input="
                        let raw = $event.target.value.replace(/\D/g, '');
                        formData.total_realisasi = raw ? parseInt(raw, 10) : 0;
                        formData.jumlah_realisasi = formData.total_realisasi;
                        $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                    "
                    placeholder="0"
                    class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 pl-10 text-xs text-emerald-400 font-bold font-mono focus:outline-none">
            </div>
            <p class="text-[10.5px] text-slate-400 mt-1.5 italic">
                💡 Nilai ini juga akan otomatis sinkron dari rincian harga barang yang Anda input pada Langkah 3.
            </p>
        </div>
    </div>

</div>
