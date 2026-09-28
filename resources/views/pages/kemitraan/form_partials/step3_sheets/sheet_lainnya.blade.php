<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: ASET TETAP LAINNYA (KIB E / AKUN 1.5.2.xx.005)         -->
<!-- ========================================================================= -->
<div x-show="isLainnya" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-purple-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-purple-500/5 rounded-full blur-2xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-base border border-purple-500/30">📦</span>
                <div>
                    <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                        Spesifikasi Aset Tetap Lainnya / Fasilitas Khusus (KIB E)
                    </h3>
                    <p class="text-[11px] text-slate-400">Rincian spesifikasi aset tetap lainnya, karya seni, instalasi khusus, atau fasilitas kerja sama lainnya.</p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-bold text-purple-400 bg-purple-950/50 px-2.5 py-1 rounded-lg border border-purple-500/30 shrink-0">
                Format KIB E
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <!-- Judul / Nama Aset -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Judul / Nama Spesifik Aset <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.lainnya_judul"
                    placeholder="Contoh: Instalasi Pengolahan Khusus / Koleksi Pameran Medis / Software Hak Cipta..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
            </div>

            <!-- Jenis / Kategori -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Jenis / Kategori Aset
                </label>
                <input type="text" x-model="formData.lainnya_jenis"
                    placeholder="Contoh: Seni Budaya / Perpustakaan / Instalasi Khusus"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <!-- Ukuran / Dimensi -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Ukuran / Dimensi
                </label>
                <input type="text" x-model="formData.lainnya_ukuran"
                    placeholder="Contoh: 2 x 3 meter / 100 Halaman / 1 Set Unit"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <!-- Bahan / Material Utama -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Bahan / Material Utama
                </label>
                <input type="text" x-model="formData.lainnya_bahan"
                    placeholder="Contoh: Logam, Kayu Jati, Serat Karbon, Digital"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <!-- Asal Usul Perolehan -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Asal Usul Perolehan
                </label>
                <input type="text" x-model="formData.lainnya_asal"
                    placeholder="Contoh: Kerja Sama Operasional Rekanan Swasta"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>
        </div>
    </div>
</div>
