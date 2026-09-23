<!-- ========================================================================= -->
<!-- LANGKAH 3: RINCIAN FISIK BARANG & PENEMPATAN RUANGAN (HIBAH)               -->
<!-- ========================================================================= -->
<div x-show="currentStep === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">

    <!-- Header Langkah 3 -->
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2 bg-amber-400/10 text-amber-300 border border-amber-400/20">
            <span>📦 LANGKAH 3 DARI 3: RINCIAN FISIK BARANG &amp; PENEMPATAN</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-amber-400/10 text-amber-400 text-sm">📋</span>
            <span>Langkah 3: Rincian Fisik Barang (<span x-text="kibLabel"></span>) &amp; Penempatan Ruangan</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Lengkapi rincian spesifikasi fisik barang hibah, taksiran nilai, unit ruangan penempatan (KIR), dan pejabat pengesah RSUD.
        </p>
    </div>

    <!-- 1. Header Ringkasan Status BAST & Klasifikasi 108 -->
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
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider block mb-1">3. TOTAL TAKSIRAN NILAI HIBAH</span>
                <div class="text-sm sm:text-base font-black font-mono text-emerald-300 truncate" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
                <span class="text-[10px] text-slate-400 mt-1 block truncate" x-text="'Volume: ' + formData.jumlah_volume + ' ' + (formData.satuan || 'Unit')"></span>
            </div>
        </div>
    </div>

    <!-- 2. Rincian Spesifikasi Fisik Barang Sesuai Kategori KIB (Langsung Mulai Rincian Fisik) -->
    <div class="space-y-6">
        @include('pages.form_hibah_partials.step3_rincian.kib_a_tanah')
        @include('pages.form_hibah_partials.step3_rincian.kib_b_peralatan_mesin')
        @include('pages.form_hibah_partials.step3_rincian.kib_c_gedung_bangunan')
        @include('pages.form_hibah_partials.step3_rincian.kib_d_jaringan_irigasi')
        @include('pages.form_hibah_partials.step3_rincian.kib_e_aset_lainnya')
        @include('pages.form_hibah_partials.step3_rincian.atb_aset_tak_berwujud')
        @include('pages.form_hibah_partials.step3_rincian.kib_f_kdp')
        @include('pages.form_hibah_partials.step3_rincian.kategori_lainnya')
    </div>

    <!-- 3. Bagian Penempatan Ruangan & PPK RSUD -->
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
                    placeholder="Contoh: Hibah sarana medis dari Kemenkes RI, kondisi fisik baik & langsung ditempatkan di ruang perawatan..."
                    class="w-full bg-slate-900 border border-slate-700 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
            </div>
        </div>
    </div>

    <!-- 4. Ringkasan Konfirmasi Card Sebelum Submit -->
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
                <span class="font-bold text-emerald-400 font-mono" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></span>
            </div>
        </div>
    </div>

</div>
