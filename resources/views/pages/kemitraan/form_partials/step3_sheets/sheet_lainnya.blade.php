<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: ASET TETAP LAINNYA (KIB E / AKUN 1.5.2.xx.005)         -->
<!-- SAMAKAN PERSIS DENGAN FORMAT LANGKAH 3 BELANJA MODAL (ASTAP)              -->
<!-- ========================================================================= -->
<div x-show="isLainnya" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB E -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-purple-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB E -->
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-lg border border-purple-500/30 shadow-inner">📦</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Aset Tetap Lainnya
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40">
                            KIB E
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih kategori aset (Buku Pustaka Medis, Kesenian &amp; Kebudayaan, atau Hewan &amp; Tanaman) beserta rincian spesifikasinya.</p>
                </div>
            </div>
            <span class="text-[10px] font-mono font-bold text-purple-400 bg-purple-950/60 px-3 py-1.5 rounded-xl border border-purple-500/30 shrink-0 shadow-sm">
                Format KIB E (Aset Lainnya)
            </span>
        </div>

        <!-- Pemilih 3 Kategori KIB E (Persis Seperti Langkah 3 Belanja Modal) -->
        <div class="space-y-3">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                    <span>📑 Pilih Kategori Aset KIB E:</span>
                </span>
                <span class="text-[10px] px-2.5 py-0.5 rounded-full font-bold border"
                    :class="{
                        'bg-amber-500/20 text-amber-300 border-amber-500/40': (formData.kib_e_type || 'buku') === 'buku',
                        'bg-purple-500/20 text-purple-300 border-purple-500/40': formData.kib_e_type === 'kesenian',
                        'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': formData.kib_e_type === 'hewan_tumbuhan'
                    }"
                    x-text="formData.kib_e_type === 'kesenian' ? '🎨 Kesenian & Kebudayaan Terpilih' : (formData.kib_e_type === 'hewan_tumbuhan' ? '🌿 Hewan & Tumbuhan Terpilih' : '📚 Buku Perpustakaan Terpilih')">
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <!-- Opsi 1: Buku Perpustakaan -->
                <div @click="formData.kib_e_type = 'buku'"
                    :class="(formData.kib_e_type || 'buku') === 'buku' ? 'border-amber-500 bg-amber-950/40 ring-1 ring-amber-500 shadow-lg shadow-amber-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-amber-500/50'"
                    class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <input type="radio" name="kib_e_type_radio" value="buku"
                                :checked="(formData.kib_e_type || 'buku') === 'buku'"
                                @change="formData.kib_e_type = 'buku'"
                                class="text-amber-500 focus:ring-amber-500">
                            <span class="text-xs font-extrabold text-amber-400">📚 Buku Perpustakaan</span>
                        </div>
                        <span x-show="(formData.kib_e_type || 'buku') === 'buku'"
                            class="text-[9px] px-1.5 py-0.5 rounded-md bg-amber-500/20 text-amber-300 font-bold border border-amber-500/40">✓ Terpilih</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">Buku, jurnal ilmiah kedokteran, literatur medis, dan arsip pustaka rumah sakit.</p>
                </div>

                <!-- Opsi 2: Kesenian & Kebudayaan -->
                <div @click="formData.kib_e_type = 'kesenian'"
                    :class="formData.kib_e_type === 'kesenian' ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500 shadow-lg shadow-purple-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-purple-500/50'"
                    class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <input type="radio" name="kib_e_type_radio" value="kesenian"
                                :checked="formData.kib_e_type === 'kesenian'"
                                @change="formData.kib_e_type = 'kesenian'"
                                class="text-purple-500 focus:ring-purple-500">
                            <span class="text-xs font-extrabold text-purple-400">🎨 Kesenian &amp; Kebudayaan</span>
                        </div>
                        <span x-show="formData.kib_e_type === 'kesenian'"
                            class="text-[9px] px-1.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 font-bold border border-purple-500/40">✓ Terpilih</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">Lukisan, patung, ornamen dekoratif, dan karya seni budaya milik RSUD.</p>
                </div>

                <!-- Opsi 3: Hewan & Tumbuhan -->
                <div @click="formData.kib_e_type = 'hewan_tumbuhan'"
                    :class="formData.kib_e_type === 'hewan_tumbuhan' ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500 shadow-lg shadow-emerald-500/10' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-emerald-500/50'"
                    class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-2.5">
                            <input type="radio" name="kib_e_type_radio" value="hewan_tumbuhan"
                                :checked="formData.kib_e_type === 'hewan_tumbuhan'"
                                @change="formData.kib_e_type = 'hewan_tumbuhan'"
                                class="text-emerald-500 focus:ring-emerald-500">
                            <span class="text-xs font-extrabold text-emerald-400">🌿 Hewan &amp; Tumbuhan</span>
                        </div>
                        <span x-show="formData.kib_e_type === 'hewan_tumbuhan'"
                            class="text-[9px] px-1.5 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                    </div>
                    <p class="text-[11px] text-slate-400 leading-relaxed">Tanaman hias penghijauan lingkungan rumah sakit atau hewan laboratorium.</p>
                </div>
            </div>
        </div>

        <!-- Form Input Detail Sesuai Kategori Terpilih (Persis Belanja Modal KIB E) -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-md">
            
            <!-- Kasus 1: Buku Perpustakaan -->
            <div x-show="(formData.kib_e_type || 'buku') === 'buku'" class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>📚 Rincian Buku / Literatur Medis:</span>
                    </span>
                    <span class="text-[9px] text-slate-400">Buku Perpustakaan RSUD</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Judul Buku / Pustaka <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.lainnya_judul"
                            placeholder="Contoh: Atlas Anatomi Manusia Sobotta Edisi 24 / Pedoman Pelayanan Klinis"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Pencipta / Pengarang Buku
                        </label>
                        <input type="text" x-model="formData.lainnya_pencipta"
                            placeholder="Contoh: Prof. Dr. Friedrich Paulsen"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Penerbit / Spesifikasi Teknis
                        </label>
                        <input type="text" x-model="formData.lainnya_spesifikasi"
                            placeholder="Contoh: EGC Penerbit Buku Kedokteran / Hardcover"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Tahun Terbit / Cetakan
                        </label>
                        <input type="number" min="1950" max="2100" x-model.number="formData.lainnya_tahun"
                            placeholder="2024"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Jumlah Halaman / Dimensi Buku
                        </label>
                        <input type="text" x-model="formData.lainnya_ukuran"
                            placeholder="Contoh: 840 Halaman / 21 x 29.7 cm"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Kasus 2: Kesenian & Kebudayaan -->
            <div x-show="formData.kib_e_type === 'kesenian'" class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-purple-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>🎨 Rincian Benda Seni / Ornamen Budaya:</span>
                    </span>
                    <span class="text-[9px] text-slate-400">Karya Seni &amp; Sejarah RSUD</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Judul / Nama Benda Seni <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.lainnya_judul"
                            placeholder="Contoh: Lukisan Sejarah Pelayanan RSUD Bondowoso / Patung Medis"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-purple-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Asal Daerah / Wilayah
                        </label>
                        <input type="text" x-model="formData.lainnya_asal_daerah"
                            placeholder="Contoh: Bondowoso, Jawa Timur"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Pencipta / Seniman
                        </label>
                        <input type="text" x-model="formData.lainnya_pencipta"
                            placeholder="Contoh: Sanggar Seni Rupa Bondowoso"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Bahan / Material Pembuatan
                        </label>
                        <input type="text" x-model="formData.lainnya_bahan"
                            placeholder="Contoh: Cat Minyak di atas Kanvas / Logam Perunggu / Kayu Jati"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Ukuran / Dimensi
                        </label>
                        <input type="text" x-model="formData.lainnya_ukuran"
                            placeholder="Contoh: 200 x 120 cm / Tinggi 1.8 Meter"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500 focus:outline-none transition-all">
                    </div>
                </div>
            </div>

            <!-- Kasus 3: Hewan & Tumbuhan -->
            <div x-show="formData.kib_e_type === 'hewan_tumbuhan'" class="space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center space-x-1.5">
                        <span>🌿 Rincian Hewan / Tumbuhan:</span>
                    </span>
                    <span class="text-[9px] text-slate-400">Taman / Laboratorium RSUD</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div class="sm:col-span-2">
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Jenis / Nama Spesies <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.lainnya_judul"
                            placeholder="Contoh: Pohon Tabebuya Kuning / Tanaman Obat Keluarga / Hewan Uji Lab"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-emerald-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Ukuran / Umur / Kondisi Fisik
                        </label>
                        <input type="text" x-model="formData.lainnya_ukuran"
                            placeholder="Contoh: Tinggi 3.5 Meter / Usia 2 Tahun"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                            Asal Usul Perolehan
                        </label>
                        <input type="text" x-model="formData.lainnya_asal"
                            placeholder="Contoh: Kerja Sama Penghijauan CSR Mitra"
                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500 focus:outline-none transition-all">
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
