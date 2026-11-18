<!-- SEKSI 3: PARAMETER REKLASIFIKASI & DATA BARU -->
<div class="p-6 sm:p-7 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-4">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center font-black text-xs font-mono">
                03
            </span>
            <div>
                <h2 class="text-sm sm:text-base font-bold text-white tracking-wide">
                    Parameter Transaksi &amp; Dokumen Dasar Hukum
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Isi nominal mutasi buku, tanggal eksekusi, triwulan pengakuan, dan dokumen pendukung resmi.
                </p>
            </div>
        </div>
        <div>
            <template x-if="!selectedAstap">
                <span class="text-[11px] font-semibold text-amber-400/90 bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20 flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Terkunci &bull; Pilih Aset di Tahap 01</span>
                </span>
            </template>
            <template x-if="selectedAstap">
                <span class="text-[11px] font-semibold text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20 flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Siap Dikonfigurasi</span>
                </span>
            </template>
        </div>
    </div>

    <!-- Banner Callout saat Belum Memilih Aset -->
    <template x-if="!selectedAstap">
        <div class="p-4 rounded-2xl bg-amber-950/20 border border-dashed border-amber-500/30 flex items-center gap-3.5 transition-all">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-amber-300">Tahap 03 Terkunci Sementara</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Parameter transaksi, pemilihan rekening PMDN 108, dan spesifikasi fisik baru akan otomatis aktif setelah Anda memilih barang pada <strong class="text-cyan-400">Tahap 01</strong>.
                </p>
            </div>
        </div>
    </template>

    <!-- WRAPPER FORM PARAMETER & DOKUMEN (LOCKED JIKA BELUM PILIH ASET) -->
    <div class="space-y-6 transition-all duration-300"
         :class="{ 'opacity-30 pointer-events-none select-none filter blur-[0.3px]': !selectedAstap }">
        <!-- Parameter Pokok: Nilai Reklas, Tanggal, Triwulan, Upload File -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Nilai Reklasifikasi -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center justify-between">
                <span>Nilai Reklasifikasi (Rp) <span class="text-rose-400">*</span></span>
                <span class="text-[9.5px] text-slate-500">Nominal Buku</span>
            </label>
            <div class="relative">
                <input type="number" step="0.01" min="0" x-model="formData.nilai_reklas" required
                       placeholder="0"
                       class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs font-mono font-bold text-white focus:outline-none transition-all shadow-sm">
            </div>
        </div>

        <!-- 2. Tanggal Reklasifikasi -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                Tanggal Reklasifikasi <span class="text-rose-400">*</span>
            </label>
            <input type="date" x-model="formData.tanggal_reklas" required
                   class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs font-bold text-white focus:outline-none transition-all shadow-sm">
        </div>

        <!-- 3. Triwulan Periode -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                Triwulan Periode <span class="text-rose-400">*</span>
            </label>
            <div class="relative">
                <select x-model="formData.triwulan" required
                        style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                        class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-bold text-white focus:outline-none transition-all cursor-pointer shadow-sm">
                    <option value="1">Triwulan I (Jan - Mar)</option>
                    <option value="2">Triwulan II (Apr - Jun)</option>
                    <option value="3">Triwulan III (Jul - Sep)</option>
                    <option value="4">Triwulan IV (Okt - Des)</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-cyan-400">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </div>
            </div>
        </div>

        <!-- 4. Upload Dokumen / Scan Berita Acara -->
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                Upload Scan BAST / SK (PDF/JPG)
            </label>
            <input type="file" @change="handleFileUpload($event)" accept=".pdf,.jpg,.jpeg,.png"
                   class="w-full bg-slate-950 border border-slate-800 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-bold file:bg-slate-800 file:text-slate-300 hover:file:bg-slate-700 rounded-xl p-1.5 text-xs text-slate-400 focus:outline-none transition-all">
        </div>
    </div>

    <!-- Nomor Berita Acara & Dokumen Pendukung -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                Nomor BA / SK Reklasifikasi
            </label>
            <input type="text" x-model="formData.nomor_ba_reklas" placeholder="000.2.3.2/.../430.10.7/{{ $selectedTahun }}"
                   class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none font-mono transition-all shadow-sm">
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                📝 Alasan Reklasifikasi
            </label>
            <input type="text" x-model="formData.alasan_reklas" placeholder="Contoh: Koreksi rekening belanja modal..."
                   class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition-all shadow-sm">
        </div>
        <div>
            <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5">
                Keterangan Tambahan
            </label>
            <input type="text" x-model="formData.keterangan" placeholder="Catatan audit atau disposisi..."
                   class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition-all shadow-sm">
        </div>
    </div>

    <!-- SUB-PANEL KONDISIONAL BERDASARKAN JENIS REKLASIFIKASI -->
    
    <!-- 1. Panel Khusus Koreksi Nilai Realisasi / BPK / Manset -->
    @include('pages.reklasifikasi.create_partials.param_koreksi_lain')

    <!-- 2. Panel Khusus Bantuan Hibah Keluar -->
    @include('pages.reklasifikasi.create_partials.param_hibah')

    <!-- 3. Panel Khusus Mutasi Eksternal Antar-OPD -->
    @include('pages.reklasifikasi.create_partials.param_mutasi_eksternal')

    <!-- 4. Panel Tujuan KIB & Rekening PMDN 108 & Spesifikasi Fisik Baru -->
    @include('pages.reklasifikasi.create_partials.param_pindah_kib_108')
    </div>
</div>

