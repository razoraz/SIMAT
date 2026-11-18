<!-- SEKSI 4: LIVE PREVIEW & AKSI SUBMIT -->
<div class="p-6 sm:p-7 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-4">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center font-black text-xs font-mono">
                04
            </span>
            <div>
                <h2 class="text-sm sm:text-base font-bold text-white tracking-wide">
                    Simulasi Dampak Neraca &amp; Konfirmasi Penerbitan
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Verifikasi dampak mutasi jurnal pembukuan dan narasi berita acara sebelum transaksi disimpan secara permanen.
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
                    <span>Siap Diterbitkan</span>
                </span>
            </template>
        </div>
    </div>

    <!-- Preview Narasi Berita Acara Otomatis -->
    <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2 shadow-inner">
        <div class="flex items-center justify-between">
            <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📑</span> Narasi Otomatis Berita Acara Reklasifikasi:
            </span>
            <span class="text-[9.5px] font-mono text-slate-500">Standar Audit BMD RSUD</span>
        </div>
        <p class="text-xs text-slate-300 italic leading-relaxed bg-slate-900/70 p-3.5 rounded-xl border border-slate-800/80 font-mono"
           x-text="getNarasiPreview()"></p>
    </div>

    <!-- Live Simulasi Jurnal Neraca (Debit / Kredit KIB) -->
    <template x-if="selectedAstap && Number(formData.nilai_reklas || 0) > 0">
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-slate-950 via-slate-900 to-slate-950 border border-slate-800 space-y-3 shadow-inner">
            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider block">
                ⚖️ Simulasi Dampak Neraca Matriks 5 Kolom:
            </span>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                <!-- Mutasi Kurang Asal -->
                <div class="p-3.5 rounded-xl bg-rose-950/20 border border-rose-500/30 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-rose-400 block">Kredit (-) Mutasi Kurang</span>
                        <span class="font-bold text-white text-xs" x-text="selectedAstap.category || 'KIB Asal'"></span>
                        <span class="text-[10px] text-slate-400 block mt-0.5" x-text="selectedAstap.nama_barang"></span>
                    </div>
                    <span class="text-sm font-black font-mono text-rose-400"
                          x-text="'- Rp ' + Number(formData.nilai_reklas || 0).toLocaleString('id-ID')"></span>
                </div>

                <!-- Mutasi Tambah Tujuan -->
                <div class="p-3.5 rounded-xl bg-emerald-950/20 border border-emerald-500/30 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold uppercase text-emerald-400 block">Debit (+) Mutasi Tambah</span>
                        <span class="font-bold text-white text-xs"
                              x-text="formData.jenis_reklas === 'MUTASI_EKSTERNAL' ? (formData.skpd_tujuan || 'OPD Luar') : (formData.jenis_reklas === 'HIBAH_KELUAR' ? (formData.pihak_hibah || 'Pihak Luar') : (formData.tujuan_kib || 'KIB Tujuan'))"></span>
                        <span class="text-[10px] text-slate-400 block mt-0.5"
                              x-text="formData.tujuan_nama || (formData.jenis_reklas.replace(/_/g, ' '))"></span>
                    </div>
                    <span class="text-sm font-black font-mono text-emerald-400"
                          x-text="'+ Rp ' + Number(formData.nilai_reklas || 0).toLocaleString('id-ID')"></span>
                </div>
            </div>
        </div>
    </template>

    <!-- Tombol Aksi Batal & Simpan -->
    <div class="pt-4 border-t border-slate-800 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
        <a href="{{ route('master.reklasifikasi', ['tahun' => $selectedTahun]) }}"
           class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-950 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 text-xs font-bold transition-all cursor-pointer text-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            <span>Batalkan &amp; Kembali</span>
        </a>

        <button type="submit" :disabled="!selectedAstap || isSubmitting"
                class="inline-flex items-center justify-center gap-2 px-7 py-3 rounded-2xl text-xs font-black shadow-xl transition-all"
                :class="!selectedAstap ? 'bg-slate-800 border border-slate-700 text-slate-500 cursor-not-allowed opacity-60 shadow-none' : 'bg-gradient-to-r from-indigo-600 via-violet-600 to-indigo-600 hover:from-indigo-500 hover:to-violet-500 text-white shadow-indigo-600/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 active:scale-95 cursor-pointer'">
            <template x-if="!selectedAstap">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Wajib Pilih Aset Terlebih Dahulu</span>
                </div>
            </template>
            <template x-if="selectedAstap && !isSubmitting">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Simpan &amp; Terbitkan Reklasifikasi Aset</span>
                </div>
            </template>
            <template x-if="selectedAstap && isSubmitting">
                <div class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memproses &amp; Menerbitkan Jurnal...</span>
                </div>
            </template>
        </button>
    </div>
</div>

