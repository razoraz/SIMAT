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

        <!-- 1. Info Tanggal Pembukuan (Read-Only Baris Atas) -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800 shadow-inner">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-indigo-400 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Tanggal Pembukuan Reklasifikasi:</span>
                    <span class="text-xs sm:text-sm font-bold text-white font-mono"
                          x-text="new Date(formData.tanggal_reklas).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' })"></span>
                </div>
            </div>
            <div class="flex items-center gap-2 self-start sm:self-auto">
                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 flex items-center gap-1.5 shadow-sm">
                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                    <span>Terkunci Otomatis (Hari Ini)</span>
                </span>
            </div>
        </div>

        <!-- 2. Nomor Berita Acara / Dokumen Reklasifikasi (Otomatis BAST/Pengadaan) -->
        <div class="space-y-1.5">
            <div class="flex items-center justify-between">
                <label class="block text-indigo-300 font-bold text-[10.5px] uppercase tracking-wider flex items-center gap-1.5">
                    <span>📄</span>
                    <span>Nomor Berita Acara / Dokumen Reklasifikasi:</span>
                </label>
                <span class="text-[9.5px] text-slate-400 font-medium">
                    (Otomatis BAST/Pengadaan atau Sesuaikan BA Fisik)
                </span>
            </div>
            <input type="text" x-model="formData.nomor_ba_reklas"
                   placeholder="Nomor dokumen BAST / SPK / BA Reklas..."
                   class="w-full bg-slate-950 border border-slate-800 focus:border-indigo-500 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none transition-all shadow-inner">
        </div>

        <!-- SUB-PANEL KONDISIONAL BERDASARKAN JENIS REKLASIFIKASI -->
        
        <!-- 1. Panel Khusus Koreksi Nilai Realisasi / BPK / Manset -->
        @include('pages.reklasifikasi.create_partials.param_koreksi_lain')

        <!-- 2. Panel Khusus Bantuan Hibah Keluar -->
        @include('pages.reklasifikasi.create_partials.param_hibah')

        <!-- 3. Panel Khusus Mutasi Eksternal Antar-OPD -->
        @include('pages.reklasifikasi.create_partials.param_mutasi_eksternal')

        <!-- 4. Panel Khusus Batas Kapitalisasi Ekstrakomptabel / Intrakomptabel -->
        @include('pages.reklasifikasi.create_partials.param_ekstrakom')

        <!-- 5. Panel Tujuan KIB & Rekening PMDN 108 & Spesifikasi Fisik Baru -->
        @include('pages.reklasifikasi.create_partials.param_pindah_kib_108')

        <!-- 3. Alasan Reklasifikasi (Wajib Diisi untuk Semua Jenis Reklas - Taruh Paling Bawah) -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2 shadow-inner">
            <div class="flex items-center justify-between">
                <label class="block text-indigo-300 font-bold text-xs uppercase tracking-wider flex items-center gap-2">
                    <span>📝</span>
                    <span>Alasan Kenapa Melakukan Reklasifikasi:</span>
                    <span class="text-rose-400 font-black text-sm">*</span>
                </label>
                <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                    Wajib Diisi
                </span>
            </div>
            <textarea x-model="formData.alasan_reklas" rows="2" required
                      placeholder="Tuliskan alasan reklasifikasi (misal: Pekerjaan fisik KDP selesai 100%, koreksi rekening, dsb)..."
                      class="w-full bg-slate-900/90 border border-slate-700/80 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition-all resize-none placeholder-slate-500 shadow-inner"></textarea>
            <p class="text-[10.5px] text-slate-400 flex items-center gap-1.5">
                <span class="text-amber-400">⚠️</span>
                <span>Alasan reklasifikasi wajib diisi untuk semua jenis reklasifikasi sebagai dasar pencatatan berita acara &amp; audit.</span>
            </p>
        </div>
    </div>
</div>

