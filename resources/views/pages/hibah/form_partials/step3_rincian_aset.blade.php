{{-- =========================================================================
     LANGKAH 3: VERIFIKASI TERPADU, PENEMPATAN RUANGAN & KONFIRMASI SUBMIT
     Design: Dark Luxury Amber — Mirror Kemitraan Step 3
     ========================================================================= --}}
<div x-show="currentStep === 3"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="space-y-6">

    {{-- Header Langkah 3 --}}
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-300 border border-amber-400/20 text-xs font-bold mb-2">
            <span>✅ LANGKAH 3 DARI 3: VERIFIKASI TERPADU &amp; PENEMPATAN ASET</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-amber-400/10 text-amber-400 text-sm">📋</span>
            <span>Langkah 3: Verifikasi Terpadu &amp; Penempatan Aset Hibah</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Konfirmasi seluruh data BAST, klasifikasi, dan rincian aset hibah. Tetapkan unit ruangan penempatan (KIR), PPK penerima, dan simpan ke database inventaris RSUD.
        </p>
    </div>

    {{-- Banner Error Inline Langkah 3 --}}
    <template x-if="stepErrors[3]">
        <div class="flex items-start gap-3 p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 shadow-lg shadow-rose-500/10">
            <span class="text-rose-400 text-lg mt-0.5 shrink-0">⚠️</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-rose-300 mb-0.5">Perhatian — Data Langkah 3 Belum Lengkap</p>
                <p class="text-xs text-rose-200/90 leading-relaxed" x-text="stepErrors[3]"></p>
            </div>
            <button type="button" @click="clearStepError(3)" class="ml-auto shrink-0 text-rose-400 hover:text-rose-200 transition-colors text-sm leading-none">✕</button>
        </div>
    </template>

    {{-- ─── KARTU VERIFIKASI 3-PANEL ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

        {{-- Panel 1: Status Dokumen BAST --}}
        <div class="p-4 rounded-2xl bg-slate-950 border shadow-inner transition-all relative overflow-hidden"
            :class="formData.hibah_pemberi && formData.hibah_nomor_bast && formData.hibah_tanggal_bast
                ? 'border-emerald-500/40 shadow-emerald-500/5'
                : 'border-rose-500/40 shadow-rose-500/5'">
            <div class="absolute -right-4 -bottom-4 w-16 h-16 rounded-full blur-2xl pointer-events-none opacity-60"
                :class="formData.hibah_pemberi && formData.hibah_nomor_bast ? 'bg-emerald-500/20' : 'bg-rose-500/20'"></div>

            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs transition-colors"
                    :class="formData.hibah_pemberi && formData.hibah_nomor_bast && formData.hibah_tanggal_bast ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400'">
                    <span x-show="formData.hibah_pemberi && formData.hibah_nomor_bast && formData.hibah_tanggal_bast">✓</span>
                    <span x-show="!(formData.hibah_pemberi && formData.hibah_nomor_bast && formData.hibah_tanggal_bast)">!</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider"
                    :class="formData.hibah_pemberi && formData.hibah_nomor_bast ? 'text-emerald-400' : 'text-rose-400'">
                    1. Dokumen BAST / NPHD
                </span>
            </div>

            <div class="space-y-2">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">Pemberi Hibah</span>
                    <div class="text-xs font-bold text-white truncate" x-text="formData.hibah_pemberi || '—'"></div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">No. BAST / NPHD</span>
                    <div class="text-xs font-mono font-bold text-amber-300 truncate" x-text="formData.hibah_nomor_bast || '—'"></div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">Tanggal &amp; Periode</span>
                    <div class="text-xs font-mono text-slate-200" x-text="(formData.hibah_tanggal_bast || '—') + ' • ' + (formData.triwulan || 'TW ?') + ' / ' + (formData.tahun_perolehan || '????')"></div>
                </div>
                <template x-if="selectedFile">
                    <div class="flex items-center gap-1.5 mt-1 px-2 py-1 rounded-lg bg-emerald-950/40 border border-emerald-500/20">
                        <span class="text-emerald-400 text-xs">📎</span>
                        <span class="text-[10px] text-emerald-300 font-semibold truncate" x-text="selectedFile?.name"></span>
                    </div>
                </template>
            </div>

            <button type="button" @click="goToStep(1)"
                class="mt-3 w-full text-center text-[10px] font-bold text-slate-400 hover:text-amber-400 transition-colors py-1 rounded-lg hover:bg-slate-800 cursor-pointer">
                ✎ Edit Langkah 1
            </button>
        </div>

        {{-- Panel 2: Status Klasifikasi 108 --}}
        <div class="p-4 rounded-2xl bg-slate-950 border shadow-inner transition-all relative overflow-hidden"
            :class="formData.jenis_aset_kode && formData.nama_barang
                ? 'border-amber-500/40 shadow-amber-500/5'
                : 'border-rose-500/40 shadow-rose-500/5'">
            <div class="absolute -right-4 -bottom-4 w-16 h-16 rounded-full blur-2xl pointer-events-none opacity-60"
                :class="formData.jenis_aset_kode && formData.nama_barang ? 'bg-amber-500/20' : 'bg-rose-500/20'"></div>

            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs transition-colors"
                    :class="formData.jenis_aset_kode && formData.nama_barang ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400'">
                    <span x-show="formData.jenis_aset_kode && formData.nama_barang">✓</span>
                    <span x-show="!(formData.jenis_aset_kode && formData.nama_barang)">!</span>
                </div>
                <span class="text-[10px] font-bold uppercase tracking-wider"
                    :class="formData.jenis_aset_kode && formData.nama_barang ? 'text-amber-400' : 'text-rose-400'">
                    2. Klasifikasi &amp; Barang
                </span>
            </div>

            <div class="space-y-2">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">Kode &amp; Jenis Aset</span>
                    <div class="text-xs font-mono font-bold text-amber-300 truncate" x-text="(selectedSubSub?.kode || formData.jenis_aset_kode || '—') + ' • ' + (formData.jenis_aset_nama || '—')"></div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">Nama Barang</span>
                    <div class="text-xs font-bold text-white truncate" x-text="formData.nama_barang || '—'"></div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">Kelompok KIB</span>
                    <div class="text-xs font-bold text-amber-300" x-text="kibLabel"></div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">Kategori</span>
                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold border inline-block"
                        :class="formData.is_extracomtable ? 'bg-amber-100/10 text-amber-400 border-amber-500/30' : 'bg-emerald-100/10 text-emerald-400 border-emerald-500/30'"
                        x-text="formData.is_extracomtable ? '📦 Ekstrakomtabel (≤ 300rb)' : '✅ Reguler (Kapitalisasi)'">
                    </span>
                </div>
            </div>

            <button type="button" @click="goToStep(2)"
                class="mt-3 w-full text-center text-[10px] font-bold text-slate-400 hover:text-amber-400 transition-colors py-1 rounded-lg hover:bg-slate-800 cursor-pointer">
                ✎ Edit Langkah 2
            </button>
        </div>

        {{-- Panel 3: Status Nilai & Volume --}}
        <div class="p-4 rounded-2xl bg-slate-950 border shadow-inner transition-all relative overflow-hidden"
            :class="formData.total_realisasi > 0
                ? 'border-emerald-500/40 shadow-emerald-500/5'
                : 'border-slate-700'">
            <div class="absolute -right-4 -bottom-4 w-16 h-16 rounded-full blur-2xl pointer-events-none opacity-60 bg-emerald-500/10"></div>

            <div class="flex items-center gap-2 mb-3">
                <div class="w-7 h-7 rounded-xl flex items-center justify-center text-xs bg-emerald-500/20 text-emerald-400">
                    <span>Σ</span>
                </div>
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">3. Nilai &amp; Volume Hibah</span>
            </div>

            <div class="space-y-2">
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">Taksiran Nilai Total</span>
                    <div class="text-base font-black font-mono text-emerald-300" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></div>
                </div>
                <div>
                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">Volume / Satuan</span>
                    <div class="text-xs font-bold font-mono text-white" x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></div>
                </div>
                <template x-if="formData.jumlah_volume > 0 && formData.total_realisasi > 0">
                    <div>
                        <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide block">Rata-rata / Unit</span>
                        <div class="text-xs font-mono text-amber-400" x-text="'Rp ' + formatRupiah(Math.round(formData.total_realisasi / (formData.jumlah_volume || 1)))"></div>
                    </div>
                </template>
            </div>

            <button type="button" @click="goToStep(2)"
                class="mt-3 w-full text-center text-[10px] font-bold text-slate-400 hover:text-amber-400 transition-colors py-1 rounded-lg hover:bg-slate-800 cursor-pointer">
                ✎ Edit Volume &amp; Nilai
            </button>
        </div>
    </div>

    {{-- ─── PENEMPATAN RUANGAN & PPK RSUD ─────────────────────────────────── --}}
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-400/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                <span>🏢 Penempatan Ruangan &amp; Pejabat Penerima Aset</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] text-slate-400 font-mono">Pencatatan KIR (Kartu Inventaris Ruangan)</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            {{-- PPK / Pejabat Penerima --}}
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Pejabat Pembuat Komitmen (PPK) / Pengesah Penerimaan Hibah
                </label>
                <select x-model="formData.ppk_nama" @change="onPpkSelect()"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none">
                    <option value="">-- Pilih Pejabat PPK RSUD --</option>
                    <template x-for="p in pejabatsList" :key="p.nama">
                        <option :value="p.nama" x-text="p.nama + (p.nip ? ' (' + p.nip + ')' : '')"></option>
                    </template>
                </select>
            </div>

            {{-- Unit / Ruangan Penempatan --}}
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
                <p class="text-[10px] text-slate-500 mt-1">Ruangan fisik tempat aset hibah ini ditempatkan dan digunakan di RSUD.</p>
            </div>

            {{-- Alamat Penempatan --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Alamat / Gedung Penempatan Fisik Barang
                </label>
                <input type="text" x-model="formData.alamat_barang"
                    placeholder="RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>

            {{-- Keterangan Tambahan --}}
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Catatan / Keterangan Tambahan Hibah
                </label>
                <textarea x-model="formData.hibah_keterangan" rows="2"
                    placeholder="Contoh: Hibah sarana medis dari Kemenkes RI, kondisi fisik baik &amp; langsung ditempatkan di ruang perawatan..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
            </div>
        </div>
    </div>

    {{-- ─── RINGKASAN KONFIRMASI FINAL ─────────────────────────────────────── --}}
    <div class="p-6 rounded-3xl bg-slate-950 border border-amber-500/20 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-2">
                <span>🎁 Ringkasan Pendaftaran Aset Hibah Masuk RSUD</span>
            </span>
            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20" x-text="kibLabel"></span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 text-xs text-slate-300">
            <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-0.5">
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Nama Barang</span>
                <span class="font-bold text-white text-sm block truncate" x-text="formData.nama_barang || '—'"></span>
            </div>
            <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-0.5">
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Kode 108</span>
                <span class="font-bold text-amber-300 font-mono block truncate" x-text="selectedSubSub ? (selectedSubSub.kode) : (formData.jenis_aset_kode || '—')"></span>
            </div>
            <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-0.5">
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Pemberi Hibah</span>
                <span class="font-bold text-amber-300 block truncate" x-text="formData.hibah_pemberi || '—'"></span>
            </div>
            <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-0.5">
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Nomor BAST</span>
                <span class="font-mono text-slate-200 font-semibold block truncate" x-text="formData.hibah_nomor_bast || '—'"></span>
            </div>
            <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-0.5">
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Tgl. BAST</span>
                <span class="font-mono text-slate-200 font-semibold block" x-text="formData.hibah_tanggal_bast || '—'"></span>
            </div>
            <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-0.5">
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Periode Pembukuan</span>
                <span class="font-bold text-white font-mono block" x-text="(formData.triwulan || 'TW ?') + ' / ' + (formData.tahun_perolehan || '????')"></span>
            </div>
            <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-0.5">
                <span class="text-slate-500 block text-[10px] uppercase font-bold">Volume / Satuan</span>
                <span class="font-bold text-white font-mono block" x-text="(formData.jumlah_volume || 0) + ' ' + (formData.satuan || 'Unit')"></span>
            </div>
            <div class="p-3 rounded-xl bg-emerald-950/40 border border-emerald-500/30 space-y-0.5">
                <span class="text-emerald-500/80 block text-[10px] uppercase font-bold">Taksiran Nilai</span>
                <span class="font-black text-emerald-400 font-mono text-sm block" x-text="'Rp ' + formatRupiah(formData.total_realisasi)"></span>
            </div>
        </div>

        {{-- Berkas Dokumen Status --}}
        <div class="flex items-center gap-2 pt-2">
            <template x-if="selectedFile">
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-emerald-950/40 border border-emerald-500/30">
                    <span class="text-emerald-400">📎</span>
                    <span class="text-[11px] text-emerald-300 font-semibold" x-text="'Berkas: ' + selectedFile?.name + ' (' + ((selectedFile?.size || 0) / 1024 / 1024).toFixed(2) + ' MB)'"></span>
                    <span class="text-[10px] text-emerald-400 font-bold ml-1">✓ Siap diunggah</span>
                </div>
            </template>
            <template x-if="!selectedFile">
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-800 border border-slate-700">
                    <span class="text-slate-400">📁</span>
                    <span class="text-[11px] text-slate-400">Tanpa lampiran berkas dokumen BAST</span>
                </div>
            </template>
        </div>
    </div>

</div>
