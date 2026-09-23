<!-- ========================================================================= -->
<!-- LANGKAH 3: RINCIAN SPESIFIKASI TEKNIS KIB & PENEMPATAN RUANGAN (HIBAH)     -->
<!-- ========================================================================= -->
<div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">

    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2"
             :class="isTanah ? 'bg-emerald-500/15 text-emerald-300 border border-emerald-500/30' : 'bg-amber-400/10 text-amber-300 border border-amber-400/20'">
            <span>📦 LANGKAH 3 DARI 3: RINCIAN SPESIFIKASI TEKNIS &amp; PENEMPATAN</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-amber-400/10 text-amber-400 text-sm">📋</span>
            <span>Langkah 3: Rincian Spesifikasi Barang (<span x-text="kibLabel"></span>) &amp; Penempatan Ruangan</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Lengkapi rincian spesifikasi fisik barang, taksiran nilai barang, unit ruangan penempatan (KIR), dan pengesahan PPK RSUD.
        </p>
    </div>

    <!-- 1. Header & Dashboard Taksiran Nilai Hibah -->
    <div class="p-4 sm:p-5 rounded-3xl bg-slate-900/95 border border-slate-800 shadow-2xl space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 shadow-inner">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">1. DOKUMEN BAST HIBAH</span>
                <div class="text-sm font-black font-mono text-white truncate" x-text="formData.hibah_nomor_bast || '-'"></div>
                <span class="text-[10px] text-amber-400 mt-1 block truncate" x-text="formData.hibah_pemberi ? ('Dari: ' + formData.hibah_pemberi) : 'Pemberi belum diisi'"></span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950 border border-cyan-500/40 bg-cyan-950/10 shadow-inner">
                <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider block mb-1">2. KLASIFIKASI KODE 108</span>
                <div class="text-sm font-black font-mono text-cyan-300 truncate" x-text="selectedSubSub ? (selectedSubSub.kode + ' • ' + selectedSubSub.nama) : (formData.nama_barang || '-')"></div>
                <span class="text-[10px] text-cyan-400/80 mt-1 block font-bold" x-text="'Kelompok: ' + kibLabel"></span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950 border border-emerald-500/40 bg-emerald-950/10 shadow-inner">
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider block mb-1">3. TOTAL NILAI PEROLEHAN HIBAH</span>
                <div class="text-sm sm:text-base font-black font-mono text-emerald-300 truncate" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
                <span class="text-[10px] text-slate-400 mt-1 block truncate" x-text="'Volume: ' + formData.jumlah_volume + ' ' + (formData.satuan || 'Unit')"></span>
            </div>
        </div>
    </div>

    <!-- --------------------------------------------------------------------- -->
    <!-- KONDISI A: FORM SPESIFIKASI TANAH (KIB A)                             -->
    <!-- --------------------------------------------------------------------- -->
    <template x-if="isTanah">
        <div class="space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 shadow-md">
                <div class="space-y-0.5">
                    <div class="flex items-center space-x-2">
                        <span class="p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 text-sm">🌾</span>
                        <h3 class="text-xs sm:text-sm font-extrabold text-white tracking-wide uppercase">
                            RINCIAN BIDANG TANAH (<span class="text-emerald-400" x-text="formData.tanah_items.length"></span> Bidang Terdaftar)
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Total Luas: <strong class="text-cyan-300" x-text="totalLuasTanah.toLocaleString('id-ID') + ' m²'"></strong> • Setiap bidang tanah memiliki sertifikat, luas, dan alamat fisik masing-masing.
                    </p>
                </div>
                <button type="button" @click="addTanahItem()" 
                        class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-emerald-500/20 shrink-0 cursor-pointer">
                    <span>➕ Tambah Bidang Tanah</span>
                </button>
            </div>

            <!-- List Kartu Bidang Tanah (Repeater) -->
            <div class="space-y-5">
                <template x-for="(item, idx) in formData.tanah_items" :key="idx">
                    <div class="p-5 sm:p-6 rounded-3xl bg-slate-950/90 border border-emerald-500/30 hover:border-emerald-500/60 transition-all space-y-4 shadow-xl relative group">
                        
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="px-3 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 font-mono font-extrabold text-xs border border-emerald-500/40 flex items-center space-x-1.5">
                                    <span>🌾 Bidang Tanah #<span x-text="idx + 1"></span></span>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Luas: <strong class="text-cyan-300" x-text="(item.tanah_luas_m2 || 0).toLocaleString('id-ID') + ' m²'"></strong>
                                </span>
                                <span class="text-[11px] text-slate-400 font-mono">
                                    • Hak: <strong class="text-amber-300" x-text="item.tanah_hak || 'Hak Pakai'"></strong>
                                </span>
                            </div>

                            <button type="button" 
                                    x-show="formData.tanah_items.length > 1" 
                                    @click="removeTanahItem(idx)" 
                                    class="px-2.5 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/30 text-[11px] font-bold transition-all flex items-center space-x-1 cursor-pointer">
                                <span>🗑️ Hapus Bidang Ini</span>
                            </button>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Status Tanah & Sertifikat -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">📜 Status Tanah &amp; Sertifikat:</span>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1 font-semibold">Hak Tanah</label>
                                    <select x-model="item.tanah_hak" @change="syncTanahFieldsToMain()"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                        <option value="Hak Pakai">Hak Pakai</option>
                                        <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                        <option value="Hak Milik">Hak Milik</option>
                                        <option value="Lainnya">Lainnya</option>
                                    </select>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Nomor</label>
                                        <input type="text" x-model="item.tanah_sertifikat_no" @input="syncTanahFieldsToMain()" placeholder="HP-108/1984"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Tanggal</label>
                                        <input type="date" x-model="item.tanah_sertifikat_tgl" @change="syncTanahFieldsToMain()"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2 py-2 text-xs text-white focus:border-amber-500">
                                    </div>
                                </div>
                            </div>

                            <!-- Kondisi, Penggunaan & Volume -->
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">📐 Kondisi, Penggunaan &amp; Volume:</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kondisi Fisik</label>
                                        <select x-model="item.tanah_kondisi" @change="syncTanahFieldsToMain()"
                                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                        <option value="Baik">Baik (B)</option>
                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Jumlah Bidang</label>
                                    <input type="number" min="1" x-model.number="item.tanah_jumlah_bidang" @input="syncTanahFieldsToMain()"
                                           placeholder="1"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-cyan-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Luas Tanah (m²)</label>
                                    <input type="number" min="0" step="any" x-model.number="item.tanah_luas_m2" @input="syncTanahFieldsToMain()"
                                           placeholder="Contoh: 35400"
                                           class="w-full bg-slate-950 border border-cyan-500/40 rounded-xl px-2.5 py-2 text-xs text-cyan-300 font-mono font-bold focus:border-cyan-500">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10px] mb-1">Penggunaan Lahan</label>
                                    <input type="text" x-model="item.tanah_penggunaan" @input="syncTanahFieldsToMain()" placeholder="Fasilitas Pelayanan RSUD"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:outline-none">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Letak / Alamat Tanah -->
                    <div class="p-4 rounded-2xl bg-slate-900/80 border border-amber-500/30 space-y-2">
                        <div class="flex items-center justify-between border-b border-amber-500/20 pb-1.5">
                            <label class="block text-amber-400 font-bold text-xs uppercase tracking-wider">
                                📍 Letak / Alamat Tanah &amp; Lokasi Fisik:
                            </label>
                            <span class="text-[9px] px-2 py-0.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold">Lokasi Fisik Bidang #<span x-text="idx + 1"></span></span>
                        </div>
                        <input type="text" x-model="item.tanah_alamat" @input="syncTanahFieldsToMain()"
                            placeholder="Contoh: Jl. Piere Tendean No. 3, Kel. Badean, Kec. Bondowoso (Area Paviliun RSUD Dr. H. Koesnandi)"
                            class="w-full bg-slate-950 border border-slate-700 hover:border-amber-500 rounded-xl px-3.5 py-2.5 text-xs text-white font-medium focus:outline-none focus:border-amber-500 transition-all">
                    </div>

                </div>
            </template>
        </div>

        <button type="button" @click="addTanahItem()" 
                class="w-full py-3.5 border-2 border-dashed border-emerald-500/50 hover:border-emerald-400 bg-emerald-950/20 hover:bg-emerald-950/40 text-emerald-300 hover:text-emerald-200 font-bold rounded-2xl flex items-center justify-center space-x-2 transition-all shadow-md group cursor-pointer">
            <span class="text-base group-hover:scale-125 transition-transform">➕</span>
            <span class="text-xs sm:text-sm">Klik Disini untuk Menambah Bidang Tanah Lainnya</span>
        </button>
    </div>
</template>

    <!-- --------------------------------------------------------------------- -->
    <!-- KONDISI B: FORM PERALATAN DAN MESIN (KIB B)                           -->
    <!-- --------------------------------------------------------------------- -->
    <template x-if="isMesin">
        <div class="space-y-4">
            <div class="p-5 rounded-2xl bg-slate-950/70 border border-cyan-500/30 space-y-4 shadow-xl">
                <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                    <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-2">
                        <span>⚙️ Spesifikasi Teknis Peralatan &amp; Mesin (KIB B)</span>
                    </span>
                    <span class="text-[10px] font-bold text-cyan-300 bg-cyan-500/10 px-2 py-0.5 rounded border border-cyan-500/20" x-text="formData.is_extracomtable ? 'Ekstrakomtabel' : 'Aset Reguler'"></span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Merk / Brand</label>
                        <input type="text" x-model="formData.merk" placeholder="Contoh: Mindray / GE / Samsung / Toyota"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Tipe / Model</label>
                        <input type="text" x-model="formData.type" placeholder="Contoh: DC-30 Color Doppler Portable"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Nomor Pabrik / Serial Number (SN)</label>
                        <input type="text" x-model="formData.no_pabrik" placeholder="SN-12345678"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Ukuran / Kapasitas</label>
                        <input type="text" x-model="formData.ukuran" placeholder="Contoh: 120 x 80 cm / 500 Watt"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Bahan / Material</label>
                        <input type="text" x-model="formData.bahan" placeholder="Contoh: Stainless Steel / Plastik ABS"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-200 mb-1">Kondisi Fisik Saat Diterima</label>
                        <select x-model="formData.kondisi"
                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-bold">
                            <option value="Baik">Baik (Baru / Berfungsi Optimal)</option>
                            <option value="Kurang Baik">Kurang Baik</option>
                            <option value="Rusak Ringan">Rusak Ringan</option>
                            <option value="Rusak Berat">Rusak Berat</option>
                        </select>
                    </div>
                </div>

                <!-- Detail Kendaraan (Jika Relevan) -->
                <div class="pt-3 border-t border-slate-800 space-y-3">
                    <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                        Atribut Kendaraan Bermotor (Jika Merupakan Kendaraan):
                    </span>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-[11px] text-slate-300 mb-1">Nomor Rangka</label>
                            <input type="text" x-model="formData.no_rangka" placeholder="No Rangka (Opsional)"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-300 mb-1">Nomor Mesin</label>
                            <input type="text" x-model="formData.no_mesin" placeholder="No Mesin (Opsional)"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                        </div>
                        <div>
                            <label class="block text-[11px] text-slate-300 mb-1">Nomor Polisi / Plat</label>
                            <input type="text" x-model="formData.no_polisi" placeholder="Contoh: P 1234 AP"
                                class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>

    <!-- --------------------------------------------------------------------- -->
    <!-- KONDISI C: FORM GEDUNG DAN BANGUNAN (KIB C)                           -->
    <!-- --------------------------------------------------------------------- -->
    <template x-if="isGedung">
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-purple-500/30 space-y-4 shadow-xl">
            <span class="text-xs font-extrabold text-purple-400 uppercase tracking-wider block border-b border-slate-800 pb-2">
                🏛️ Rincian Gedung &amp; Bangunan (KIB C)
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Luas Lantai (m²)</label>
                    <input type="number" min="0" step="any" x-model="formData.gedung_luas_m2" placeholder="Contoh: 450"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Kondisi Bangunan</label>
                    <select x-model="formData.kondisi"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <option value="Baik">Baik</option>
                        <option value="Kurang Baik">Kurang Baik</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Konstruksi Bertingkat</label>
                    <select x-model="formData.gedung_bertingkat"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <option value="Tidak">Tidak Bertingkat</option>
                        <option value="Bertingkat">Bertingkat</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Konstruksi Beton</label>
                    <select x-model="formData.gedung_beton"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <option value="Beton">Beton</option>
                        <option value="Bukan Beton">Bukan Beton</option>
                    </select>
                </div>
            </div>
        </div>
    </template>

    <!-- --------------------------------------------------------------------- -->
    <!-- KONDISI D: JALAN, IRIGASI & JARINGAN (KIB D)                          -->
    <!-- --------------------------------------------------------------------- -->
    <template x-if="isJaringan">
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-teal-500/30 space-y-4 shadow-xl">
            <span class="text-xs font-extrabold text-teal-400 uppercase tracking-wider block border-b border-slate-800 pb-2">
                🛣️ Rincian Jalan, Irigasi &amp; Jaringan (KIB D)
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Panjang (m)</label>
                    <input type="number" min="0" step="any" x-model="formData.jaringan_panjang_m" placeholder="150"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Lebar (m)</label>
                    <input type="number" min="0" step="any" x-model="formData.jaringan_lebar_m" placeholder="4"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Luas Total (m²)</label>
                    <input type="number" min="0" step="any" x-model="formData.jaringan_luas_m2" placeholder="600"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                </div>
            </div>
        </div>
    </template>

    <!-- --------------------------------------------------------------------- -->
    <!-- KONDISI E: ASET TETAP LAINNYA (KIB E) & ATB                           -->
    <!-- --------------------------------------------------------------------- -->
    <template x-if="isAtb">
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-indigo-500/30 space-y-4 shadow-xl">
            <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider block border-b border-slate-800 pb-2">
                💡 Rincian Aset Tidak Berwujud (ATB)
            </span>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Jenis Aset Tak Berwujud</label>
                    <select x-model="formData.atb_jenis"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-semibold">
                        <option value="Software / Aplikasi Sistem Informasi">Software / Aplikasi SIMRS</option>
                        <option value="Lisensi / Hak Cipta">Lisensi / Hak Cipta</option>
                        <option value="Kajian Medis & Dokumen Strategis">Kajian Medis &amp; Dokumen</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Masa Manfaat (Tahun)</label>
                    <input type="number" min="1" max="20" x-model.number="formData.atb_masa_manfaat" placeholder="4"
                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                </div>
            </div>
        </div>
    </template>

    <!-- --------------------------------------------------------------------- -->
    <!-- BAGIAN PENEMPATAN RUANGAN & PPK (TERINTEGRASI DI LANGKAH 3)            -->
    <!-- --------------------------------------------------------------------- -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-400/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                <span>🏢 Pejabat Penerima &amp; Lokasi Penempatan Aset di RSUD</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] text-slate-400 font-mono">Pencatatan Kartu Inventaris Ruangan (KIR)</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Pejabat Pembuat Komitmen (PPK) / Pengesah RSUD
                </label>
                <select x-model="formData.ppk_nama" @change="onPpkSelect()"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none">
                    <option value="">-- Pilih Pejabat PPK --</option>
                    <template x-for="p in pejabatsList" :key="p.nama">
                        <option :value="p.nama" x-text="p.nama + (p.nip ? ' (' + p.nip + ')' : '')"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Unit / Ruangan Penempatan Aset (KIR) <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.unit_id" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none font-semibold">
                    <option value="">-- Pilih Unit / Ruangan Penempatan --</option>
                    @foreach($dbUnits ?? [] as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->kode_unit ?? 'Unit' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Alamat / Gedung Penempatan Fisik Barang
                </label>
                <input type="text" x-model="formData.alamat_barang"
                    placeholder="RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Catatan / Keterangan Tambahan Hibah
                </label>
                <textarea x-model="formData.hibah_keterangan" rows="2"
                    placeholder="Contoh: Hibah sarana medis dari Kemenkes RI tahun anggaran 2026, kondisi fisik baik & operasional aktif..."
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
            </div>
        </div>
    </div>

    <!-- Ringkasan Konfirmasi Card Sebelum Submit -->
    <div class="p-6 rounded-3xl bg-slate-950 border border-emerald-500/30 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-emerald-400 uppercase tracking-wider flex items-center gap-2">
                <span>📋 Ringkasan Pendaftaran Aset Hibah</span>
            </span>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20" x-text="kibLabel"></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-xs text-slate-300">
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Nama Barang:</span>
                <span class="font-bold text-white text-sm" x-text="formData.nama_barang || '-'"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Klasifikasi 108:</span>
                <span class="font-bold text-cyan-300 font-mono" x-text="selectedSubSub ? (selectedSubSub.kode + ' - ' + selectedSubSub.nama) : (formData.jenis_aset_kode || '-')"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Pemberi Hibah:</span>
                <span class="font-bold text-amber-300" x-text="formData.hibah_pemberi || '-'"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Dokumen BAST:</span>
                <span class="font-mono text-slate-200 font-semibold" x-text="(formData.hibah_nomor_bast || '-') + ' (' + formatTanggalIndo(formData.hibah_tanggal_bast) + ')'"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Volume / Satuan:</span>
                <span class="font-bold text-white font-mono" x-text="formData.jumlah_volume + ' ' + (formData.satuan || 'Unit')"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Taksiran Nilai:</span>
                <span class="font-bold text-emerald-400 font-mono" x-text="formatRupiah(formData.total_realisasi)"></span>
            </div>
        </div>
    </div>

</div>
