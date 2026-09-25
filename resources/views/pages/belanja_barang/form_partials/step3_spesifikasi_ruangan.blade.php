<!-- ========================================================================= -->
<!-- LANGKAH 3: RINCIAN TEKNIS KIB & PENEMPATAN RUANGAN (KIR)                  -->
<!-- ========================================================================= -->
<div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div class="border-b border-slate-800 pb-4">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/20 text-xs font-bold mb-2">
            <span>Langkah 3 dari 3</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span>🏢 Spesifikasi Teknis &amp; Ruangan Penempatan (KIR)</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">
            Lengkapi spesifikasi barang perbekalan dan tentukan ruangan penempatan fisik untuk penomoran NIBAR &amp; KIR.
        </p>
    </div>

    <!-- Bagian Ruangan Penempatan (KIR) -->
    <div class="p-5 rounded-2xl bg-slate-950/70 border border-indigo-500/30 space-y-4">
        <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider block">
            🏢 Unit / Ruangan Penempatan Inventaris (KIR) <span class="text-rose-400">*</span>
        </span>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Pilih Unit / Ruangan / Paviliun <span class="text-rose-400">*</span>
                </label>
                <select x-model.number="formData.unit_id" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    <option value="">-- Pilih Ruangan Pemegang Aset --</option>
                    @foreach($dbUnits ?? [] as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->nama }} ({{ $unit->kode ?? '-' }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Kondisi Fisik Barang <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.kondisi" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                    <option value="Baik">Baik (Bagus / Baru)</option>
                    <option value="Kurang Baik">Kurang Baik (Dapat Berfungsi)</option>
                    <option value="Rusak Berat">Rusak Berat (Tidak Berfungsi)</option>
                </select>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Detail Lokasi Fisik Spesifik
            </label>
            <input type="text" x-model="formData.alamat_barang"
                placeholder="Contoh: Gedung Paviliun Melati Lt. 2, Ruang Rawat Inap 204..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
        </div>
    </div>

    <!-- Bagian Spesifikasi KIB Sesuai Kode 108 -->
    <div class="p-5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
        <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-slate-300 uppercase tracking-wider block">
                📋 Spesifikasi KIB (<span x-text="kibLabel"></span>)
            </span>
            <span class="text-[10px] text-slate-500 font-mono" x-text="selectedSubSub?.kode || ''"></span>
        </div>

        <!-- KIB B: Peralatan & Mesin (Paling Umum untuk Belanja Barang/Alkes) -->
        <template x-if="isMesin">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Merk / Brand</label>
                    <input type="text" x-model="formData.spesifikasi_json.merk" placeholder="Contoh: Omron, GE, Sharp, Panasonic..."
                        class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Tipe / Model</label>
                    <input type="text" x-model="formData.spesifikasi_json.type" placeholder="Contoh: HEM-7120, Seri A-202..."
                        class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Bahan / Material</label>
                    <input type="text" x-model="formData.spesifikasi_json.bahan" placeholder="Contoh: Plastik ABS, Stainless Steel..."
                        class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Nomor Pabrik / Seri (Serial No)</label>
                    <input type="text" x-model="formData.spesifikasi_json.no_pabrik" placeholder="Contoh: SN-849204820..."
                        class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-white font-mono">
                </div>
            </div>
        </template>

        <!-- KIB E / Aset Lainnya -->
        <template x-if="!isMesin">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Judul / Spesifikasi Barang</label>
                    <input type="text" x-model="formData.spesifikasi_json.judul" placeholder="Spesifikasi atau nama model..."
                        class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-white">
                </div>
                <div>
                    <label class="block text-slate-400 mb-1 font-semibold">Ukuran / Dimensi</label>
                    <input type="text" x-model="formData.spesifikasi_json.ukuran" placeholder="Contoh: 120 x 80 x 75 cm..."
                        class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-white">
                </div>
            </div>
        </template>
    </div>

    <!-- Ringkasan Sebelum Simpan -->
    <div class="p-4 rounded-2xl bg-indigo-500/10 border border-indigo-500/30 flex items-center justify-between text-xs">
        <div>
            <span class="text-indigo-400 font-bold block">✨ Siap Menerbitkan NIBAR Ekstrakomptabel</span>
            <span class="text-slate-300 text-[11px] mt-0.5 block">
                Sistem akan secara otomatis menerbitkan <strong class="text-white" x-text="formData.jumlah_volume"></strong> nomor register NIBAR dan QR Code ke ruangan penempatan.
            </span>
        </div>
        <span class="text-lg">🏷️</span>
    </div>

</div>
