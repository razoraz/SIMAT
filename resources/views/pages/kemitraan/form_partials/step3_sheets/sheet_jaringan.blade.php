<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: JALAN, IRIGASI & JARINGAN (KIB D / AKUN 1.5.2.xx.004) -->
<!-- MULTI-ITEM REPEATER (MODEL PERSIS KIB B PERALATAN & MESIN)                 -->
<!-- ========================================================================= -->
<div x-show="isJaringan" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB D -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-teal-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-teal-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB D -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg border border-teal-500/30 shadow-inner">🛣️</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Teknis Jalan, Irigasi &amp; Jaringan
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-teal-500/20 text-teal-300 font-mono font-bold text-[10px] border border-teal-500/40">
                            KIB D
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Rincian konstruksi jalan/jaringan, dimensi bentang (panjang &amp; lebar), luas total, kondisi, dan dokumen kontrak teknis.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-teal-400 bg-teal-950/60 px-3 py-1.5 rounded-xl border border-teal-500/30 shadow-sm">
                    Total: <span x-text="formData.jaringan_items ? formData.jaringan_items.length : 1"></span> Ruas / Jaringan
                </span>
            </div>
        </div>

        <!-- REPEATER DAFTAR JALAN & JARINGAN -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.jaringan_items" :key="idx">
                <div class="p-5 rounded-2xl bg-slate-900/90 border border-slate-800/90 hover:border-teal-500/50 transition-all space-y-4 shadow-lg relative">
                    
                    <!-- Header Kartu Jaringan -->
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-teal-500/20 text-teal-300 font-mono font-extrabold text-xs border border-teal-500/40 flex items-center space-x-1.5 shadow-sm">
                                <span>🛣️ Ruas / Jaringan #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-xs text-white font-bold" x-show="item.jaringan_nama_barang" x-text="item.jaringan_nama_barang"></span>
                            <span class="text-[10.5px] text-slate-400 font-mono" x-show="item.jaringan_luas">
                                • Luas: <strong class="text-teal-300" x-text="(item.jaringan_luas || 0) + ' m²'"></strong>
                            </span>
                            <span class="text-[10.5px] text-slate-400 font-mono">
                                • Subtotal: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getJaringanSubtotal(item))"></strong>
                            </span>
                        </div>

                        <!-- Tombol Hapus Jaringan -->
                        <button type="button"
                            x-show="formData.jaringan_items.length > 1"
                            @click="removeJaringanItem(idx)"
                            class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer self-end sm:self-auto">
                            <span>🗑️ Hapus Ruas Ini</span>
                        </button>
                    </div>

                    <!-- Input Nama Jaringan -->
                    <div>
                        <label class="block text-slate-400 text-[10.5px] mb-1 font-semibold">
                            Nama Spesifik Jaringan / Ruas Jalan <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="item.jaringan_nama_barang"
                            placeholder="Contoh: Instalasi Jaringan Sentral Gas Oksigen Medis Paviliun Barat / Ruas Jalan Aspal Lingkar RSUD"
                            class="w-full bg-slate-950 border border-slate-700 hover:border-teal-500 focus:border-teal-500 rounded-xl px-3.5 py-2 text-xs text-white font-bold focus:outline-none transition-all">
                    </div>

                    <!-- Grid Form Spesifikasi Jaringan -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- 1. Konstruksi & Dimensi Jaringan -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>🏗️ Konstruksi &amp; Dimensi:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Fisik Jaringan</span>
                            </div>

                            <!-- Konstruksi Jaringan -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Konstruksi Jaringan / Jalan <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="item.jaringan_konstruksi"
                                    placeholder="Contoh: Pipa Tembaga Medis Degreased / Pipa HDPE / Aspal Hotmix / Paving K-350"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                            </div>

                            <!-- Dimensi 3 Kolom: Panjang, Lebar, Luas -->
                            <div class="grid grid-cols-3 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Panjang (M)</label>
                                    <input type="number" step="0.01" min="0" x-model.number="item.jaringan_panjang"
                                        @input="
                                            if (item.jaringan_panjang && item.jaringan_lebar) {
                                                item.jaringan_luas = parseFloat(((parseFloat(item.jaringan_panjang) || 0) * (parseFloat(item.jaringan_lebar) || 0)).toFixed(2));
                                            }
                                        "
                                        placeholder="350"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Lebar (M)</label>
                                    <input type="number" step="0.01" min="0" x-model.number="item.jaringan_lebar"
                                        @input="
                                            if (item.jaringan_panjang && item.jaringan_lebar) {
                                                item.jaringan_luas = parseFloat(((parseFloat(item.jaringan_panjang) || 0) * (parseFloat(item.jaringan_lebar) || 0)).toFixed(2));
                                            }
                                        "
                                        placeholder="4.5"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas (m²)</label>
                                    <div class="flex items-center rounded-xl bg-slate-900 border border-teal-500/40 focus-within:border-teal-400 overflow-hidden transition-all">
                                        <input type="number" step="0.01" min="0" x-model.number="item.jaringan_luas"
                                            placeholder="1575"
                                            class="w-full bg-transparent px-2.5 py-2 text-xs text-teal-300 font-mono font-bold focus:outline-none">
                                        <span class="px-2 py-2 text-[10px] font-mono font-bold text-teal-400/80 bg-slate-800/80 border-l border-slate-700 shrink-0">m²</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bertingkat / Beton -->
                            <div class="grid grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Konstruksi Bertingkat</label>
                                    <select x-model="item.jaringan_bertingkat"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                        <option value="Tidak">Tidak Bertingkat</option>
                                        <option value="Bertingkat">Bertingkat</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Konstruksi Beton</label>
                                    <select x-model="item.jaringan_beton"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                        <option value="Beton">Beton</option>
                                        <option value="Tidak">Bukan Beton</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Kondisi Jaringan -->
                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Kondisi Jaringan Saat Diterima <span class="text-rose-400">*</span>
                                </label>
                                <select x-model="item.jaringan_kondisi"
                                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-amber-500 focus:outline-none transition-all">
                                    <option value="Baik">🟢 Baik (B) &mdash; Operasional Normal</option>
                                    <option value="Kurang Baik">🟡 Kurang Baik (KB) &mdash; Perlu Perawatan</option>
                                    <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                </select>
                            </div>
                        </div>

                        <!-- 2. Dokumen / Kontrak Teknis & Legalitas Tanah -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 shadow-inner">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📜 Dokumen Teknis &amp; Status Tanah:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Administrasi</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <!-- Nomor Dokumen -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Nomor Dokumen / Kontrak Teknis
                                    </label>
                                    <input type="text" x-model="item.jaringan_dokumen_no"
                                        placeholder="DOK-JAR/2026/01"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                                </div>

                                <!-- Tanggal Dokumen -->
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                        Tanggal Dokumen Kontrak
                                    </label>
                                    <input type="text" x-datepicker x-model="item.jaringan_dokumen_tgl"
                                        placeholder="dd/mm/yyyy"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                                </div>
                            </div>

                            <!-- Status Tanah & Kode Aset Tanah -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2 border-t border-slate-800/80">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Status Penguasaan Tanah</label>
                                    <input type="text" x-model="item.jaringan_status_tanah"
                                        placeholder="Tanah Hak Pakai RSUD"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kode Aset Tanah (Jika Ada)</label>
                                    <input type="text" x-model="item.jaringan_kode_aset_tanah"
                                        placeholder="1.3.1.01.01.02.013"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-emerald-400 font-mono focus:border-cyan-500 focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- 3. Keterangan / Catatan Khusus Jaringan (Diletakkan di Atas Lokasi Fisik) -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 hover:border-slate-700 space-y-1.5 shadow-md transition-all">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                            <label class="block text-slate-300 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📝 KETERANGAN / CATATAN KHUSUS JARINGAN:</span>
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-bold">Catatan Tambahan</span>
                        </div>
                        <input type="text" x-model="item.jaringan_keterangan"
                            placeholder="Contoh: Jaringan pipa distribusi air bersih instalasi baru kemitraan / Keterangan teknis &amp; operasional"
                            class="w-full bg-slate-950 border border-slate-700 hover:border-slate-600 focus:border-teal-500 rounded-xl px-3.5 py-2 text-xs text-white font-medium focus:outline-none transition-all">
                    </div>

                    <!-- 4. Letak / Lokasi Fisik Jaringan & Instalasi -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/40 space-y-1.5 shadow-md">
                        <div class="flex items-center justify-between border-b border-emerald-500/30 pb-1.5">
                            <label class="block text-emerald-400 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📍 LETAK / LOKASI FISIK JARINGAN &amp; INSTALASI:</span>
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Lokasi Fisik</span>
                        </div>
                        <input type="text" x-model="item.jaringan_alamat" placeholder="Contoh: Kompleks RSUD Dr. H. Koesnandi (Area Distribusi Air Bersih / Rute Pipa)"
                               class="w-full bg-slate-950 border border-slate-700 hover:border-emerald-500 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none focus:border-emerald-500 transition-all">
                    </div>

                    <!-- 5. Volume & Taksiran Nilai Wajar Ruas / Jaringan -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-3 shadow-md">
                        <div class="flex items-center justify-between border-b border-emerald-500/20 pb-2">
                            <span class="text-xs font-bold text-emerald-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                <span>💰 Volume &amp; Taksiran Nilai Wajar Jaringan (Rp):</span>
                            </span>
                            <div class="flex items-center space-x-2 bg-emerald-950/70 border border-emerald-500/40 px-3 py-1 rounded-xl shadow-sm">
                                <span class="text-[10px] text-slate-300 font-semibold">Sub Total Ruas #<span x-text="idx + 1"></span>:</span>
                                <span class="text-xs font-black text-emerald-300 font-mono tracking-tight" x-text="'Rp ' + Number(getJaringanSubtotal(item)).toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        <div class="space-y-3">
                            {{-- Baris 1: Jumlah Volume & Satuan --}}
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1.5 font-semibold">Jumlah Volume / Ruas <span class="text-rose-400">*</span></label>
                                    <input type="number" min="1" x-model.number="item.jaringan_jumlah" @input="syncTotalsFromItems()" placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-500 rounded-xl px-3.5 py-2 text-xs text-white font-mono font-bold focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1.5 font-semibold">Satuan <span class="text-rose-400">*</span></label>
                                    <input type="text" x-model="item.jaringan_satuan" @input="syncTotalsFromItems()" placeholder="Ruas / Paket / Meter"
                                           class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-500 rounded-xl px-3.5 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>
                            </div>
                            {{-- Baris 2: Taksiran Nilai (full width) --}}
                            <div>
                                <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                                    <label class="text-slate-400 text-[10px] font-semibold">
                                        Taksiran Total Nilai Wajar Ruas (Rp) <span class="text-rose-400">*</span>
                                    </label>
                                    <span class="text-[9px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/25 font-mono whitespace-nowrap">
                                        Lump-sum • Nilai Keseluruhan
                                    </span>
                                </div>
                                <div class="relative flex items-center">
                                    <span class="absolute left-3.5 text-emerald-500 text-xs font-mono font-extrabold select-none">Rp</span>
                                    <input type="text" 
                                           :value="item.jaringan_nilai_satuan ? Number(item.jaringan_nilai_satuan).toLocaleString('id-ID') : ''"
                                           @input="
                                               let raw = $event.target.value.replace(/\D/g, '');
                                               item.jaringan_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                               $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                               syncTotalsFromItems();
                                           "
                                           placeholder="Contoh: 75.000.000"
                                           class="w-full bg-slate-950 border border-emerald-500/40 hover:border-emerald-500/60 focus:border-emerald-400 text-emerald-300 rounded-xl pl-10 pr-3.5 py-2 text-xs font-mono font-bold focus:outline-none transition-all shadow-inner">
                                </div>
                            </div>
                        </div>

                        <div class="pt-2 flex items-center gap-2 text-[10.5px] text-slate-400 border-t border-slate-800/60">
                            <span class="text-xs">💡</span>
                            <span><strong class="text-slate-300">Catatan:</strong> Taksiran nilai jaringan adalah nilai appraisal total keseluruhan untuk Ruas #<span x-text="idx + 1"></span> (lump-sum, tidak dikalikan jumlah ruas).</span>
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Ruas / Jaringan Baru -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addJaringanItem()"
                class="px-5 py-2.5 rounded-full bg-slate-950/90 hover:bg-slate-900 text-white font-bold text-xs border border-white/80 hover:border-white shadow-lg flex items-center space-x-2 transition-all cursor-pointer">
                <span class="text-base font-light leading-none">+</span>
                <span>Tambah Ruas / Jaringan Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Jaringan:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiJaringan)"></span>
            </div>
        </div>

    </div>

</div>
