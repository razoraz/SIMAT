<!-- ========================================================================= -->
<!-- SHEET SPESIFIKASI: TANAH (KIB A / AKUN 1.5.2.01.01.xx.001)               -->
<!-- REPEATER MULTI-ITEM PERSIS LANGKAH 3 BELANJA MODAL (ASTAP)                -->
<!-- ========================================================================= -->
<div x-show="isTanah" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-5">
    
    <!-- Wrapper Card Utama KIB A -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-emerald-500/40 space-y-5 shadow-2xl relative overflow-hidden">
        <!-- Glow Ambient -->
        <div class="absolute -right-8 -bottom-8 w-44 h-44 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Header Card: Spesifikasi KIB A -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2.5">
                <span class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg border border-emerald-500/30 shadow-inner">🌾</span>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                            Spesifikasi Fisik &amp; Legalitas Tanah
                        </h3>
                        <span class="px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/40">
                            KIB A
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bisa menambah beberapa bidang tanah dengan status hak, sertifikat, luas bidang, dan letak lokasi masing-masing.</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-[10px] font-mono font-bold text-emerald-400 bg-emerald-950/60 px-3 py-1.5 rounded-xl border border-emerald-500/30 shadow-sm">
                    Total: <span x-text="formData.tanah_items ? formData.tanah_items.length : 1"></span> Bidang Tanah
                </span>
            </div>
        </div>

        <!-- List Kartu Bidang Tanah (Repeater) -->
        <div class="space-y-5">
            <template x-for="(item, idx) in formData.tanah_items" :key="idx">
                <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 hover:border-emerald-500/60 transition-all space-y-4 shadow-xl relative group">
                    
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-800 pb-3 gap-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-mono font-extrabold text-xs border border-emerald-500/40 flex items-center space-x-1.5">
                                <span>🌾 Bidang Tanah #<span x-text="idx + 1"></span></span>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Luas: <strong class="text-cyan-300" x-text="(item.tanah_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                            </span>
                            <span class="text-[11px] text-slate-400 font-mono">
                                • Taksiran: <strong class="text-emerald-400" x-text="'Rp ' + formatRupiah(getTanahSubtotal(item))"></strong>
                            </span>
                        </div>

                        <button type="button" 
                                x-show="formData.tanah_items.length > 1" 
                                @click="removeTanahItem(idx)" 
                                class="px-3 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 self-start sm:self-auto cursor-pointer">
                            <span>🗑️ Hapus Bidang Ini</span>
                        </button>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- 1. Status Tanah & Sertifikat -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📜 Status Tanah &amp; Sertifikat:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Legalitas Lahan</span>
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                    Hak / Status Penguasaan Tanah <span class="text-rose-400">*</span>
                                </label>
                                <select x-model="item.tanah_hak"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                    <option value="Hak Pakai">Hak Pakai (Pemda / RSUD)</option>
                                    <option value="Hak Pengelolaan">Hak Pengelolaan (HPL)</option>
                                    <option value="Hak Milik Pemda">Hak Milik Pemerintah Kabupaten</option>
                                    <option value="Tanah Adat / Ulayat">Tanah Adat / Ulayat</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Nomor Sertifikat Lahan</label>
                                    <input type="text" x-model="item.tanah_sertifikat_no"
                                        placeholder="HP-108/Bondowoso/1998"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Tanggal Terbit Sertifikat</label>
                                    <input type="text" x-datepicker x-model="item.tanah_sertifikat_tgl"
                                        placeholder="dd/mm/yyyy"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- 2. Kondisi, Penggunaan & Luas Bidang -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📐 Kondisi, Penggunaan &amp; Luas:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Fisik &amp; Dimensi</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Lahan Tanah <span class="text-rose-400">*</span></label>
                                    <select x-model="item.tanah_kondisi"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-cyan-500 focus:outline-none transition-all">
                                        <option value="Baik">🟢 Baik (Siap Digunakan)</option>
                                        <option value="Kurang Baik">🟡 Kurang Baik (Perlu Pematangan)</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat (Rawa / Longsor)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Luas Tanah (m²) <span class="text-rose-400">*</span></label>
                                    <div class="relative">
                                        <input type="number" step="0.01" min="0" x-model.number="item.tanah_luas_m2" @input="syncTotalsFromItems()"
                                            placeholder="Contoh: 1500"
                                            class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-3 py-2 text-xs text-emerald-300 font-mono font-bold focus:border-emerald-400 focus:outline-none transition-all">
                                        <span class="absolute right-3 top-2 text-[10px] font-mono font-bold text-slate-500">m²</span>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Peruntukan Lahan <span class="text-rose-400">*</span></label>
                                <input type="text" x-model="item.tanah_penggunaan"
                                    placeholder="Contoh: Area Parkir Terpadu / Gedung Paviliun"
                                    class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all font-medium">
                            </div>
                        </div>

                        <!-- 3. Batas & Lokasi -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-2 md:col-span-2 shadow-md">
                            <label class="block text-teal-400 font-bold text-xs uppercase tracking-wider">🧭 Batas-Batas Bidang Tanah:</label>
                            <input type="text" x-model="item.tanah_batas"
                                placeholder="Contoh: Utara: Jl. Piere Tendean, Timur: Pemukiman, Selatan: Rawat Jalan, Barat: Saluran Air"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none transition-all">
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-2 md:col-span-2 shadow-md">
                            <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider">📍 Letak / Alamat Tanah &amp; Lokasi Fisik:</label>
                            <input type="text" x-model="item.tanah_alamat"
                                placeholder="Contoh: Kompleks RSUD Dr. H. Koesnandi, Jl. Piere Tendean No. 1, Bondowoso"
                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none transition-all">
                        </div>

                        <!-- 4. Nilai Taksiran Wajar Per Bidang -->
                        <div class="p-4 rounded-2xl bg-slate-900/80 border border-emerald-500/30 space-y-2 md:col-span-2 shadow-md">
                            <div class="flex items-center justify-between">
                                <label class="block text-emerald-400 font-bold text-xs uppercase tracking-wider">💰 Taksiran Nilai Wajar Bidang Ini (Rp):</label>
                                <span class="text-xs font-mono font-black text-emerald-400" x-text="'Rp ' + Number(getTanahSubtotal(item)).toLocaleString('id-ID')"></span>
                            </div>
                            <input type="text"
                                :value="item.tanah_nilai_satuan ? Number(item.tanah_nilai_satuan).toLocaleString('id-ID') : ''"
                                @input="
                                    let raw = $event.target.value.replace(/\D/g, '');
                                    item.tanah_nilai_satuan = raw ? parseInt(raw, 10) : 0;
                                    $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                    syncTotalsFromItems();
                                "
                                placeholder="0"
                                class="w-full bg-slate-950 border border-slate-700 text-emerald-300 font-mono font-bold rounded-xl px-3.5 py-2 text-xs focus:border-emerald-500 focus:outline-none transition-all">
                        </div>
                    </div>

                </div>
            </template>
        </div>

        <!-- Tombol Tambah Bidang Tanah Baru -->
        <div class="flex items-center justify-between pt-2 border-t border-slate-800">
            <button type="button" @click="addTanahItem()"
                class="px-4 py-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-500/20 hover:shadow-emerald-500/40 border border-emerald-400/40 flex items-center space-x-2 transition-all cursor-pointer">
                <span>➕</span>
                <span>Tambah Bidang Tanah Baru</span>
            </button>
            <div class="text-right text-xs">
                <span class="text-slate-400 block text-[10.5px]">Total Taksiran Tanah:</span>
                <span class="font-mono font-extrabold text-emerald-400 text-sm" x-text="'Rp ' + formatRupiah(totalNilaiTanah)"></span>
            </div>
        </div>

    </div>

</div>
