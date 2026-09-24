<!-- ========================================================================= -->
<!-- LANGKAH 3: RINCIAN SPESIFIKASI KIB & PENEMPATAN RUANGAN RSUD              -->
<!-- ========================================================================= -->
<div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div class="border-b border-slate-800 pb-4">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2"
             :class="isTanah ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : 'bg-purple-500/10 text-purple-300 border border-purple-500/20'">
            <span>Langkah 3 dari 3</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span x-text="kibBadgeIcon"></span>
            <span x-text="hasSelectedKib ? ('Spesifikasi Teknis (' + kibLabel + ') & Penempatan Baru di RSUD') : 'Spesifikasi Teknis & Penempatan Baru di RSUD'"></span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">
            <span x-show="!hasSelectedKib">Tentukan Klasifikasi KIB pada Langkah 2 terlebih dahulu agar formulir spesifikasi teknis barang dapat dimuat.</span>
            <span x-show="hasSelectedKib && isTanah">Lengkapi data sertifikat, luas, dan batas bidang tanah pelimpahan (multi-bidang didukung).</span>
            <span x-show="hasSelectedKib && !isTanah">Lengkapi spesifikasi teknis barang pelimpahan, lalu tentukan unit ruangan penempatan baru di RSUD Dr. H. Koesnandi.</span>
        </p>
    </div>

    <!-- Keadaan jika KIB belum dipilih di Langkah 2 -->
    <template x-if="!hasSelectedKib">
        <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 text-center space-y-3">
            <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center mx-auto text-xl text-slate-400">🔍</div>
            <h4 class="text-sm font-bold text-white">Kelompok KIB Belum Dipilih</h4>
            <p class="text-xs text-slate-400 max-w-md mx-auto">Silakan kembali ke <strong>Langkah 2</strong> dan tentukan Kelompok KIB atau cari nama barang 108 terlebih dahulu agar spesifikasi teknis yang sesuai dapat dimuat.</p>
            <button type="button" @click="step = 2" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-cyan-400 text-xs font-bold rounded-xl border border-slate-700 transition">
                ← Kembali ke Langkah 2
            </button>
        </div>
    </template>

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
                            RINCIAN BIDANG TANAH MUTASI (<span class="text-emerald-400" x-text="formData.tanah_items.length"></span> Bidang Terdaftar)
                        </h3>
                    </div>
                    <p class="text-[11px] text-slate-400">
                        Total Luas: <strong class="text-cyan-300" x-text="totalLuasTanah.toLocaleString('id-ID') + ' m²'"></strong> • Setiap bidang tanah memiliki sertifikat dan luas masing-masing.
                    </p>
                </div>
                <button type="button" @click="addTanahItem()" 
                        class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 text-xs font-bold transition-all flex items-center justify-center space-x-1.5 shadow-lg shadow-emerald-500/20 shrink-0 cursor-pointer">
                    <span>➕ Tambah Bidang Tanah</span>
                </button>
            </div>

            <!-- Repeater Kartu Tanah -->
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
                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">📜 Status Tanah &amp; Sertifikat:</span>
                                <div>
                                    <label class="block text-slate-400 text-[11px] mb-1 font-semibold">Hak Tanah</label>
                                    <select x-model="item.tanah_hak" @change="syncTanahFields()"
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
                                        <input type="text" x-model="item.tanah_sertifikat_no" @input="syncTanahFields()" placeholder="HP-108/1984"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Sertifikat Tanggal</label>
                                        <input type="text" x-datepicker x-model="item.tanah_sertifikat_tgl" @change="syncTanahFields()"
                                               placeholder="dd/mm/yyyy"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500">
                                    </div>
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3">
                                <span class="text-xs font-bold text-cyan-400 block uppercase tracking-wider">📐 Kondisi, Penggunaan &amp; Volume:</span>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Kondisi (B/KB/RB)</label>
                                        <select x-model="item.tanah_kondisi" @change="syncTanahFields()"
                                            class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                            <option value="Baik">Baik (B)</option>
                                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Jumlah Bidang</label>
                                        <input type="number" min="1" x-model.number="item.tanah_jumlah_bidang" @input="syncTanahFields()"
                                               placeholder="1"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono font-bold focus:border-cyan-500">
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-2">
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Luas Tanah (m²)</label>
                                        <input type="number" step="any" min="0" x-model.number="item.tanah_luas_m2" @input="syncTanahFields()"
                                               placeholder="Contoh: 1250"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-cyan-300 font-mono font-bold focus:border-cyan-500">
                                    </div>
                                    <div>
                                        <label class="block text-slate-400 text-[10px] mb-1">Penggunaan Tanah</label>
                                        <input type="text" x-model="item.tanah_penggunaan" @input="syncTanahFields()"
                                               placeholder="Pelayanan Pasien"
                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-cyan-500">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- --------------------------------------------------------------------- -->
    <!-- KONDISI B: FORM PERALATAN & MESIN (KIB B)                             -->
    <!-- --------------------------------------------------------------------- -->
    <template x-if="isMesin">
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Merk / Brand</label>
                    <input type="text" x-model="formData.merk" placeholder="Contoh: Suzuki / Toyota / Omron / GE"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Tipe / Model</label>
                    <input type="text" x-model="formData.type" placeholder="Contoh: APV Blindvan / Standar Medis"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Nomor Pabrik / Serial Number (SN)</label>
                    <input type="text" x-model="formData.no_pabrik" placeholder="Nomor seri pabrikan barang"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Ukuran / Dimensi / Kapasitas</label>
                    <input type="text" x-model="formData.ukuran" placeholder="Contoh: 1500 cc / 120 x 80 cm"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Bahan / Material</label>
                    <input type="text" x-model="formData.bahan" placeholder="Contoh: Besi Baja / Campuran"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-200 mb-1">Kondisi Fisik Saat Diterima</label>
                    <select x-model="formData.kondisi"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        <option value="Baik">Baik (Berfungsi Normal)</option>
                        <option value="Kurang Baik">Kurang Baik</option>
                        <option value="Rusak Ringan">Rusak Ringan</option>
                        <option value="Rusak Berat">Rusak Berat</option>
                    </select>
                </div>
            </div>

            <!-- Legalitas Kendaraan / Atribut Mesin (Jika Relevan) -->
            <div class="pt-3 border-t border-slate-800/80 space-y-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">
                    Legalitas Kendaraan / Atribut Mesin (Jika Relevan):
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
    </template>

    <!-- --------------------------------------------------------------------- -->
    <!-- KONDISI C: FORM GEDUNG, JARINGAN, ATB, LAINNYA                        -->
    <!-- --------------------------------------------------------------------- -->
    <template x-if="isGedung">
        <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
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
            </div>
        </div>
    </template>

    <!-- Bagian Penempatan Ruangan & PPK -->
    <div class="p-5 rounded-2xl bg-slate-950/80 border border-purple-500/30 space-y-4 shadow-xl">
        <span class="text-xs font-extrabold text-purple-400 uppercase tracking-wider block">
            🏢 Unit Ruangan Penempatan Baru di RSUD &amp; Pejabat Penerima:
        </span>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Pejabat Pembuat Komitmen (PPK) / Pengesah Penerimaan
                </label>
                <select x-model="formData.ppk_nama" @change="onPpkSelect()"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    <template x-for="p in pejabatsList" :key="p.nama">
                        <option :value="p.nama" x-text="p.nama + (p.nip ? ' (' + p.nip + ')' : '')"></option>
                    </template>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Unit / Ruangan Penempatan Baru di RSUD <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.unit_id" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    <option value="">-- Pilih Unit / Ruangan --</option>
                    @foreach($dbUnits ?? [] as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->kode_unit ?? 'Unit' }})</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Alamat / Gedung Penempatan Fisik
                </label>
                <input type="text" x-model="formData.alamat_barang"
                    placeholder="RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
            </div>
        </div>
    </div>

    <!-- Ringkasan Konfirmasi Card -->
    <div class="p-5 rounded-2xl bg-slate-950 border border-purple-500/30 space-y-3 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-2">
            <span class="text-xs font-extrabold text-purple-400 uppercase tracking-wider flex items-center gap-2">
                <span>📋 Ringkasan Pendaftaran Mutasi Eksternal</span>
            </span>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20" x-text="kibLabel"></span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-slate-300">
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Nama Barang:</span>
                <span class="font-bold text-white text-sm" x-text="formData.nama_barang || '-'"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Klasifikasi 108:</span>
                <span class="font-bold text-cyan-300" x-text="selectedSubSub ? (selectedSubSub.kode + ' - ' + selectedSubSub.nama) : '-'"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">SKPD Pengirim:</span>
                <span class="font-bold text-purple-300" x-text="formData.mutasi_asal || '-'"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Dokumen BAMB:</span>
                <span class="font-mono text-slate-200 font-semibold" x-text="(formData.mutasi_nomor_bamb || '-') + ' (' + formatTanggalIndo(formData.mutasi_tanggal) + ')'"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Volume / Satuan:</span>
                <span class="font-bold text-white font-mono" x-text="formData.jumlah_volume + ' ' + formData.satuan"></span>
            </div>
            <div>
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Nilai Perolehan / Buku:</span>
                <span class="font-bold text-emerald-400 font-mono" x-text="formatRupiah(formData.total_realisasi)"></span>
            </div>
        </div>
    </div>

</div>
