<!-- ========================================================================= -->
<!-- LANGKAH 3: SPESIFIKASI FISIK, PENEMPATAN RUANGAN & PPK (KEMITRAAN 1.5.2)  -->
<!-- ========================================================================= -->
<div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-400/10 text-cyan-300 border border-cyan-400/20 text-xs font-bold mb-2">
            <span>📦 LANGKAH 3 DARI 3: SPESIFIKASI FISIK &amp; PENEMPATAN RUANGAN RSUD</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-cyan-400/10 text-cyan-400 text-sm">📋</span>
            <span>Langkah 3: Rincian Spesifikasi Teknis &amp; Lokasi Penempatan Ruangan</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Lengkapi rincian fisik alat/fasilitas kemitraan (merk, serial number), unit ruangan penempatan (KIR), kondisi fisik, dan pejabat penanggung jawab RSUD.
        </p>
    </div>

    <!-- 1. Header Ringkasan Status PKS, 108 & Nilai Aset -->
    <div class="p-4 sm:p-5 rounded-3xl bg-slate-900/95 border border-slate-800 shadow-2xl space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 shadow-inner">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block mb-1">1. DOKUMEN PKS &amp; MITRA</span>
                <div class="text-sm font-black font-mono text-white truncate" x-text="formData.nomor_pks || '-'"></div>
                <span class="text-[10px] text-cyan-400 mt-1 block truncate" x-text="formData.mitra_nama ? ('Mitra: ' + formData.mitra_nama) : 'Mitra belum diisi'"></span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950 border border-cyan-500/40 bg-cyan-950/10 shadow-inner">
                <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider block mb-1">2. KLASIFIKASI KODE 108</span>
                <div class="text-sm font-black font-mono text-cyan-300 truncate" x-text="selectedSubSub ? (selectedSubSub.kode + ' • ' + selectedSubSub.nama) : (formData.nama_barang || '-')"></div>
                <span class="text-[10px] text-cyan-400/80 mt-1 block font-bold" x-text="'Skema: ' + (formData.skema_kemitraan || 'KSO / Kemitraan')"></span>
            </div>

            <div class="p-3.5 rounded-2xl bg-slate-950 border border-emerald-500/40 bg-emerald-950/10 shadow-inner">
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider block mb-1">3. TOTAL TAKSIRAN NILAI WAJAR</span>
                <div class="text-sm sm:text-base font-black font-mono text-emerald-300 truncate" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
                <span class="text-[10px] text-slate-400 mt-1 block truncate" x-text="'Volume: ' + formData.jumlah_volume + ' ' + (formData.satuan || 'Unit')"></span>
            </div>
        </div>
    </div>

    <!-- 2. Form Rincian Spesifikasi Teknis Barang -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-cyan-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-2">
                <span>🔧 Rincian Spesifikasi Fisik Barang / Mesin / Alkes</span>
            </span>
            <span class="text-[10px] text-slate-400 font-mono">Untuk Identitas Fisik &amp; Label Barcode</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Merk / Brand Pabrikan
                </label>
                <input type="text" x-model="formData.merk"
                    placeholder="Contoh: Roche / Siemens / Fresenius / GE Healthcare..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tipe / Model Barang
                </label>
                <input type="text" x-model="formData.type"
                    placeholder="Contoh: Cobas e411 / 4008S / Multix Select DR..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Pabrik / Seri (Serial Number)
                </label>
                <input type="text" x-model="formData.no_pabrik"
                    placeholder="Contoh: SN-892301982 / S/N-2026-X88..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Bahan / Material Utama
                </label>
                <input type="text" x-model="formData.bahan"
                    placeholder="Contoh: Logam, Elektronik Medis, Beton, Kaca..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Ukuran / Kapasitas / Spesifikasi Tambahan
                </label>
                <input type="text" x-model="formData.ukuran"
                    placeholder="Contoh: 120 x 80 x 150 cm / Kapasitas 120 Tes/Jam / 3 Lantai Beton..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>
        </div>
    </div>

    <!-- 3. Bagian Penempatan Ruangan, Kondisi & PPK RSUD -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-cyan-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-2">
                <span>🏢 Penempatan Ruangan (KIR), Kondisi Fisik &amp; Pejabat RSUD</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] text-slate-400 font-mono">Pencatatan Kartu Inventaris Ruangan (KIR)</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Unit / Ruangan Penempatan -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Unit / Ruangan Penempatan Aset (KIR) <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.unit_id" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none font-semibold">
                    <option value="">-- Pilih Unit / Ruangan Penempatan --</option>
                    @foreach($dbUnits ?? [] as $u)
                        <option value="{{ $u->id }}">{{ $u->nama }} ({{ $u->kode_unit ?? 'Unit' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- Kondisi Fisik Saat Diterima -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Kondisi Fisik Saat Diterima <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.kondisi" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none font-semibold">
                    <option value="Baik">🟢 Baik (Operasional Normal / Siap Digunakan)</option>
                    <option value="Rusak Ringan">🟡 Rusak Ringan (Perlu Kalibrasi / Setting)</option>
                    <option value="Rusak Berat">🔴 Rusak Berat</option>
                </select>
            </div>

            <!-- Alamat / Gedung Penempatan Fisik Barang -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Alamat / Gedung Penempatan Fisik Barang
                </label>
                <input type="text" x-model="formData.alamat_barang"
                    placeholder="RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1"
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            <!-- Pejabat Pembuat Komitmen (PPK) / Pengurus Barang RSUD -->
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Pejabat Penanggung Jawab RSUD (PPK / Pengurus Barang)
                </label>
                <select x-model="formData.ppk_nama" @change="onPpkSelect()"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none">
                    <option value="">-- Pilih Pejabat Penanggung Jawab --</option>
                    <template x-for="p in pejabatsList" :key="p.nama">
                        <option :value="p.nama" x-text="p.nama + (p.nip ? ' (' + p.nip + ')' : '')"></option>
                    </template>
                </select>
            </div>
        </div>
    </div>

    <!-- 4. Ringkasan Kartu Aset Kemitraan Sebelum Simpan -->
    <div class="p-5 rounded-3xl bg-cyan-950/30 border border-cyan-500/30 space-y-3 shadow-xl">
        <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-cyan-300 uppercase tracking-wider flex items-center gap-1.5">
                <span>📑 Ringkasan Pra-Penyimpanan Aset Kemitraan Pihak Ketiga</span>
            </span>
            <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-lg border border-emerald-500/20">
                Otomatis Generate NIBAR
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
            <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800">
                <span class="text-slate-500 block text-[10px] font-semibold">Mitra Rekanan:</span>
                <span class="font-bold text-white truncate block mt-0.5" x-text="formData.mitra_nama || '-'"></span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800">
                <span class="text-slate-500 block text-[10px] font-semibold">Nomor Dokumen PKS:</span>
                <span class="font-mono text-white truncate block mt-0.5" x-text="formData.nomor_pks || '-'"></span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-950/70 border border-slate-800">
                <span class="text-slate-500 block text-[10px] font-semibold">Volume &amp; Satuan:</span>
                <span class="font-bold text-white block mt-0.5" x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></span>
            </div>
            <div class="p-3 rounded-2xl bg-slate-950/70 border border-cyan-500/30 bg-cyan-950/20">
                <span class="text-cyan-400 block text-[10px] font-semibold">Total Nilai Wajar:</span>
                <span class="font-extrabold text-cyan-300 font-mono block mt-0.5" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></span>
            </div>
        </div>

        <p class="text-[10.5px] text-slate-400 italic pt-1">
            ✨ Setelah disimpan, sistem akan secara otomatis menerbitkan Nomor Induk Barang (NIBAR) unik untuk setiap unit dan mencatatkannya pada Akun 1.5.2 Aset Kemitraan SIMAT-RK RSUD Dr. H. Koesnandi.
        </p>
    </div>

</div>
