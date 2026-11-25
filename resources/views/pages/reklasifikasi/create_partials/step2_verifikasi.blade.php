<!-- ========================================================================= -->
<!-- LANGKAH 2: LEMBAR VERIFIKASI & KONFIRMASI TRANSAKSI REKLASIFIKASI ASET -->
<!-- ========================================================================= -->
<div class="space-y-6">

    <!-- 1. Header Banner Langkah 2 -->
    <div class="p-6 sm:p-7 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4 backdrop-blur-xl relative overflow-hidden">
        <div class="absolute -top-16 -right-16 w-56 h-56 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full text-xs font-bold mb-2 bg-purple-500/15 text-purple-300 border border-purple-500/30">
                <span>🛡️ LANGKAH 2 DARI 2: VERIFIKASI REKLASIFIKASI ASET</span>
            </div>
            <h2 class="text-xl sm:text-2xl font-black text-white flex items-center gap-2.5">
                <span class="p-2 rounded-xl bg-purple-500/20 text-purple-300 border border-purple-500/30 text-sm">📋</span>
                <span>Lembar Verifikasi Transaksi Reklasifikasi Aset</span>
            </h2>
            <p class="text-xs text-slate-400 mt-1.5 max-w-2xl leading-relaxed">
                Tinjau kembali ringkasan mutasi akun, identitas aset, unit register NIBAR terpilih, serta pratinjau berita acara resmi sebelum transaksi dibukukan secara permanen ke neraca.
            </p>
        </div>

        <div class="flex items-center gap-2.5 self-start md:self-center shrink-0 relative z-10">
            <button type="button" @click="goToStep(1)"
                    class="px-4 py-2.5 rounded-2xl bg-slate-950 hover:bg-slate-800 text-slate-300 hover:text-white text-xs font-bold transition-all flex items-center gap-2 border border-slate-800 cursor-pointer shadow-sm active:scale-95">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Edit Data (Langkah 1)</span>
            </button>
        </div>
    </div>

    <!-- 2. Quick Status Cards (Grid 3 Kolom) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Aset Asal & Nilai Semula -->
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">1. Aset Asal (Mutasi Kurang -)</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700"
                      x-text="formData.asal_kib || selectedAstap?.category || 'KIB'"></span>
            </div>
            <div class="text-sm font-black text-white truncate" x-text="selectedAstap?.nama_barang || 'Aset BMD'"></div>
            <div class="text-[11px] font-mono text-cyan-400 truncate"
                 x-text="'Kode 108: ' + (selectedAstap?.kode_108 || selectedAstap?.jenis_astap?.sub_sub_rincian_objek || '-')"></div>
            <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 text-[11px]">Nilai Buku Semula:</span>
                <span class="font-mono font-bold text-white"
                      x-text="'Rp ' + Number(selectedAstap?.total_realisasi || 0).toLocaleString('id-ID')"></span>
            </div>
        </div>

        <!-- Card 2: Arah & Rekening Tujuan -->
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider">2. Arah Reklasifikasi</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-500/20 text-purple-300 border border-purple-500/30"
                      x-text="getJenisReklasLabel(formData.jenis_reklas)"></span>
            </div>
            <div class="text-sm font-black text-purple-200 truncate"
                 x-text="formData.tujuan_nama || (formData.tujuan_kib ? ('Ke ' + formData.tujuan_kib) : 'Penyesuaian Neraca')"></div>
            <div class="text-[11px] font-mono text-purple-300/80 truncate">
                <span x-show="formData.tujuan_kode" x-text="'Kode Tujuan: ' + formData.tujuan_kode"></span>
                <span x-show="!formData.tujuan_kode && formData.skpd_tujuan" x-text="'OPD Tujuan: ' + formData.skpd_tujuan"></span>
                <span x-show="!formData.tujuan_kode && formData.pihak_hibah" x-text="'Penerima: ' + formData.pihak_hibah"></span>
                <span x-show="!formData.tujuan_kode && !formData.skpd_tujuan && !formData.pihak_hibah" x-text="'Kelompok: ' + (formData.tujuan_kib || 'Definitif')"></span>
            </div>
            <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 text-[11px]">Tujuan Mutasi:</span>
                <span class="font-bold text-purple-300" x-text="formData.tujuan_kib || 'Intrakomptabel'"></span>
            </div>
        </div>

        <!-- Card 3: Total Nilai Mutasi & Register Terpilih -->
        <div class="p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-2 relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">3. Nilai Reklasifikasi</span>
                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                    Selesai Dihitung
                </span>
            </div>
            <div class="text-lg font-black font-mono text-emerald-400"
                 x-text="'Rp ' + Number(formData.nilai_reklas || 0).toLocaleString('id-ID')"></div>
            <div class="text-[11px] text-slate-400 flex items-center gap-2">
                <span class="inline-flex items-center gap-1 font-bold text-slate-300">
                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                    <span x-text="(formData.selected_register_ids ? formData.selected_register_ids.length : 0) + ' Unit Register'"></span>
                </span>
                <span class="text-slate-600">&bull;</span>
                <span x-text="selectedAstap && selectedAstap.registers && formData.selected_register_ids && formData.selected_register_ids.length < selectedAstap.registers.length ? 'Sebagian (Parsial)' : 'Penuh (100%)'"></span>
            </div>
            <div class="pt-2 border-t border-slate-800/80 flex items-center justify-between text-xs">
                <span class="text-slate-400 text-[11px]">Nomor Berita Acara:</span>
                <span class="font-mono text-slate-200 text-[11px] truncate max-w-[140px]"
                      :title="formData.nomor_ba_reklas || '-'"
                      x-text="formData.nomor_ba_reklas || '-'"></span>
            </div>
        </div>
    </div>

    <!-- 3. Rincian Unit Register NIBAR Terpilih (Jika Aset Memiliki Register) -->
    <template x-if="selectedAstap && selectedAstap.registers && selectedAstap.registers.length > 0">
        <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-cyan-300 uppercase tracking-wider">
                        🏷️ Unit Register Fisik (NIBAR) yang Diproses Reklasifikasi:
                    </span>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-mono"
                          x-text="(formData.selected_register_ids ? formData.selected_register_ids.length : 0) + ' Unit Terpilih'"></span>
                </div>
                <button type="button" @click="goToStep(1)"
                        class="text-[11px] font-bold text-cyan-400 hover:text-cyan-300 transition-colors cursor-pointer">
                    Ubah Pilihan Unit &uarr;
                </button>
            </div>

            <div class="grid grid-cols-1 gap-2.5 max-h-48 overflow-y-auto custom-scrollbar pr-1"
                 :class="{
                     'sm:grid-cols-2': (formData.selected_register_ids ? formData.selected_register_ids.length : 0) >= 2,
                     'lg:grid-cols-3': (formData.selected_register_ids ? formData.selected_register_ids.length : 0) >= 3
                 }">
                <template x-for="reg in (selectedAstap.registers || []).filter(r => (formData.selected_register_ids || []).includes(Number(r.id)))" :key="reg.id">
                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-cyan-500/20 flex items-center justify-between gap-2 shadow-inner">
                        <div class="min-w-0">
                            <div class="font-mono text-xs font-bold text-cyan-300 truncate" x-text="reg.nibar || reg.no_register"></div>
                            <div class="text-[10px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                <span x-text="'Kondisi: ' + (formData.register_kondisi_map[reg.id] || reg.kondisi || 'Baik')"></span>
                                <span class="text-slate-600">&bull;</span>
                                <span class="font-mono text-slate-300" x-text="'Rp ' + Number(reg.harga_satuan || selectedAstap.harga_satuan || (selectedAstap.total_realisasi / (selectedAstap.registers.length || 1))).toLocaleString('id-ID')"></span>
                            </div>
                        </div>
                        <span class="text-[9.5px] px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-400 border border-emerald-500/25 font-bold shrink-0">
                            Terpilih ✓
                        </span>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- 4. Rincian Khusus Ekstrakomptabel / Intrakomptabel (Jika Berlaku) -->
    <template x-if="(formData.jenis_reklas === 'EKSTRAKOMPTABEL' || formData.jenis_reklas === 'KAPITALISASI_INTRAKOM') && formData.reklas_items && formData.reklas_items.length > 0">
        <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-3">
            <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                <span class="text-xs font-bold text-amber-300 uppercase tracking-wider">
                    📦 Rincian Barang &amp; Harga Satuan Ekstrakomptabel:
                </span>
                <span class="text-xs font-mono font-bold text-white"
                      x-text="'Total: Rp ' + Number(formData.nilai_reklas || 0).toLocaleString('id-ID')"></span>
            </div>

            <div class="space-y-2">
                <template x-for="(item, idx) in formData.reklas_items" :key="idx">
                    <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 flex items-center justify-between gap-3 text-xs">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-6 h-6 rounded-lg bg-slate-800 text-slate-300 font-bold text-[10px] flex items-center justify-center shrink-0"
                                  x-text="idx + 1"></span>
                            <div class="min-w-0">
                                <span class="font-bold text-white block truncate" x-text="item.nama_barang"></span>
                                <span class="text-[10px] text-slate-400 font-mono" x-show="item.nibar" x-text="'NIBAR: ' + item.nibar"></span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="font-mono font-bold text-emerald-400"
                                 x-text="'Rp ' + Number(item.harga_satuan || 0).toLocaleString('id-ID') + ' / ' + (item.satuan || 'Unit')"></div>
                            <div class="text-[10px] text-slate-500" x-text="item.jumlah_volume + ' ' + (item.satuan || 'Unit')"></div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </template>

    <!-- 5. Narasi Otomatis Berita Acara Reklasifikasi -->
    <div class="p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-3">
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
            <span class="text-xs font-bold text-indigo-300 uppercase tracking-wider flex items-center gap-2">
                <span>📑</span>
                <span>Naskah Narasi Resmi Berita Acara Reklasifikasi:</span>
            </span>
            <span class="text-[10px] font-mono text-slate-500">Standar Audit BMD RSUD Dr. H. Koesnandi</span>
        </div>
        <p class="text-xs text-slate-200 italic leading-relaxed bg-slate-950/80 p-4 rounded-2xl border border-slate-800/80 font-mono"
           x-text="getNarasiPreview()"></p>
        <div class="text-[11px] text-slate-400 flex items-center gap-2 pt-1">
            <span class="text-indigo-400 font-bold">Dasar / Alasan:</span>
            <span class="text-slate-300" x-text="formData.alasan_reklas || '-'"></span>
        </div>
    </div>

    <!-- Banner Error Server Persisten (Jika Gagal Simpan) -->
    <div x-show="submitErrorMessage" x-cloak x-transition
         class="p-4 sm:p-5 rounded-2xl bg-rose-950/60 border border-rose-500/50 text-rose-200 flex items-start justify-between gap-3 shadow-lg shadow-rose-950/40">
        <div class="flex items-start gap-3 min-w-0">
            <span class="w-8 h-8 rounded-xl bg-rose-500/20 border border-rose-500/30 text-rose-300 flex items-center justify-center shrink-0 mt-0.5 text-sm">
                ⚠️
            </span>
            <div class="space-y-1 min-w-0">
                <p class="text-xs font-black uppercase tracking-wider text-rose-300">Gagal Menyimpan Reklasifikasi</p>
                <p class="text-xs leading-relaxed text-rose-100" x-text="submitErrorMessage"></p>
            </div>
        </div>
        <button type="button" @click="submitErrorMessage = ''"
                class="text-rose-300 hover:text-white px-2 py-1 rounded-lg hover:bg-rose-500/20 text-xs font-bold shrink-0 cursor-pointer">
            &times;
        </button>
    </div>

    <!-- 6. Bilah Aksi Simpan Final & Kembali -->
    <div class="p-5 sm:p-6 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 backdrop-blur-xl">
        <button type="button" @click="goToStep(1)"
                class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-slate-950 hover:bg-slate-800 text-slate-400 hover:text-white border border-slate-800 text-xs font-bold transition-all cursor-pointer text-center">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>&larr; Kembali &amp; Edit Formulir</span>
        </button>

        <button type="submit" :disabled="!selectedAstap || isSubmitting"
                class="inline-flex items-center justify-center gap-2.5 px-7 py-3 rounded-2xl text-xs font-black shadow-xl transition-all cursor-pointer"
                :class="!selectedAstap || isSubmitting
                    ? 'bg-slate-800 border border-slate-700 text-slate-500 cursor-not-allowed opacity-60 shadow-none'
                    : 'bg-gradient-to-r from-emerald-600 via-teal-600 to-teal-500 hover:from-emerald-500 hover:to-teal-500 text-white shadow-emerald-600/30 hover:shadow-emerald-500/50 hover:-translate-y-0.5 active:scale-95'">
            <template x-if="!isSubmitting">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                    </svg>
                    <span>Simpan &amp; Terbitkan Reklasifikasi Aset</span>
                </div>
            </template>
            <template x-if="isSubmitting">
                <div class="flex items-center gap-2">
                    <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Memproses &amp; Menerbitkan Jurnal Reklasifikasi...</span>
                </div>
            </template>
        </button>
    </div>

</div>
