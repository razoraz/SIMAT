<!-- ========================================================================= -->
<!-- MODAL TERMINASI KONSESI: PENGEMBALIAN KE MITRA REKANAN                   -->
<!-- SIMAT-RK Bespoke Modern Dark UI Standard                                  -->
<!-- ========================================================================= -->
<template x-teleport="body">
<div x-show="showModalTerminasi" 
     x-cloak
     class="fixed inset-0 overflow-y-auto"
     style="display: none; z-index: 9999;"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <!-- Backdrop Dark Glassmorphism -->
    <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md transition-opacity" 
         @click="if(!isSubmittingTerminasi) closeTerminasiModal()"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div x-show="showModalTerminasi"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl shadow-purple-950/50 text-left transition-all w-full max-w-4xl my-auto max-h-[92vh] flex flex-col">

            <!-- ========================================================================= -->
            <!-- 1. HEADER MODAL ELEGAN                                                    -->
            <!-- ========================================================================= -->
            <div class="shrink-0 relative px-6 py-4 sm:py-5 border-b flex items-center justify-between bg-gradient-to-r from-purple-950/60 via-slate-900 to-slate-950 border-purple-500/20">
                <div class="flex items-center space-x-3.5">
                    <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-inner ring-1 bg-purple-500/15 border border-purple-500/30 text-purple-300 ring-purple-500/20">
                        <span>📦</span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-[10px] font-extrabold tracking-widest uppercase font-mono text-purple-400">
                                Terminasi Konsesi / PKS
                            </span>
                            <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                            <span class="text-[10px] text-slate-400 font-mono">Permendagri 19/2016</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-black text-white flex items-center gap-2">
                            <span>Pengembalian Aset ke Mitra Rekanan</span>
                        </h3>
                    </div>
                </div>

                <button type="button" 
                        @click="if(!isSubmittingTerminasi) closeTerminasiModal()" 
                        class="w-9 h-9 rounded-xl bg-slate-800/80 text-slate-400 hover:text-white hover:bg-rose-500/20 hover:border-rose-500/30 border border-slate-700/60 flex items-center justify-center transition-all cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- ========================================================================= -->
            <!-- 2. FORM & SCROLLABLE CONTENT BODY                                         -->
            <!-- ========================================================================= -->
            <form @submit.prevent="submitKembalikanForm()" class="flex flex-col flex-1 overflow-hidden">
                <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4 custom-scrollbar">

                    <!-- Kartu Ringkasan Objek Aset -->
                    <div class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 shadow-inner space-y-2.5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-2.5">
                            <div class="min-w-0">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-mono">
                                    Objek Aset Kemitraan:
                                </span>
                                <h4 class="text-sm sm:text-base font-extrabold text-white flex flex-wrap items-center gap-2 mt-0.5">
                                    <span class="truncate" x-text="terminasiData.nama_barang || '-'"></span>
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold border"
                                          :class="terminasiData.tipe_kemitraan === 'dimanfaatkan' ? 'bg-cyan-500/10 text-cyan-300 border-cyan-500/30' : 'bg-purple-500/10 text-purple-300 border-purple-500/30'"
                                          x-text="terminasiData.tipe_kemitraan === 'dimanfaatkan' ? 'Pemanfaatan BMD' : 'Aset Mitra Ditambahkan'"></span>
                                </h4>
                            </div>
                            <div class="sm:text-right shrink-0">
                                <span class="text-[10px] text-slate-400 block font-mono">Nilai Kontrak di PKS</span>
                                <span class="text-xs sm:text-sm font-mono font-bold text-emerald-400" 
                                      x-text="'Rp ' + Number(terminasiData.nilai_aset || 0).toLocaleString('id-ID')"></span>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5 text-xs">
                            <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-800/60 min-w-0">
                                <span class="text-[9.5px] text-slate-400 block uppercase font-mono">Mitra Rekanan</span>
                                <span class="font-semibold text-slate-200 block truncate" x-text="terminasiData.mitra_nama || '-'"></span>
                            </div>
                            <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-800/60 min-w-0">
                                <span class="text-[9.5px] text-slate-400 block uppercase font-mono">Nomor PKS</span>
                                <span class="font-mono text-cyan-300 block truncate" x-text="terminasiData.nomor_pks || '-'"></span>
                            </div>
                            <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-800/60 min-w-0">
                                <span class="text-[9.5px] text-slate-400 block uppercase font-mono">Masa Berlaku</span>
                                <span class="text-slate-300 block truncate text-[11px]" x-text="(terminasiData.tanggal_mulai_fmt || '-') + ' s/d ' + (terminasiData.tanggal_selesai_fmt || '-')"></span>
                            </div>
                            <div class="bg-slate-900/60 p-2.5 rounded-xl border border-slate-800/60 min-w-0">
                                <span class="text-[9.5px] text-slate-400 block uppercase font-mono">Status Saat Ini</span>
                                <span class="font-bold text-amber-400 block truncate text-[11px]" x-text="terminasiData.status_konsesi || 'Aktif'"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu Form Informasi Berita Acara (BAST) -->
                    <div class="p-4 sm:p-5 rounded-2xl bg-purple-950/15 border border-purple-500/25 space-y-4">
                        <div class="flex items-center justify-between pb-2 border-b border-purple-500/20">
                            <span class="text-xs font-bold text-purple-300 uppercase tracking-wider flex items-center gap-2">
                                <span class="text-sm">📋</span>
                                <span>Informasi Berita Acara Serah Terima Pengembalian (BAST)</span>
                            </span>
                            <span class="text-[10px] text-purple-400/80 font-mono bg-purple-500/10 px-2 py-0.5 rounded border border-purple-500/20">
                                Formulir Resmi
                            </span>
                        </div>

                        <!-- Baris 1: Nomor Berita Acara (BAST) — Full Width Spacious -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-[11px] font-bold text-slate-200 flex items-center gap-1.5">
                                    <span>Nomor Berita Acara Pengembalian</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[10px] text-slate-400 font-mono">Format Sah RSUD / Pemkab</span>
                            </div>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-purple-400 text-xs">
                                    <span>📄</span>
                                </div>
                                <input type="text" x-model="terminasiForm.nomor_ba_terminasi" required
                                       placeholder="Contoh: 000.2.3.2/BAST-KMT-KEMBALI/001/430.10.7/2026"
                                       class="w-full min-w-0 bg-slate-900 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl pl-9 pr-3.5 py-2.5 text-xs font-mono font-semibold text-purple-200 placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">Nomor BAST ini akan tercatat dalam buku inventaris dan dicetak pada berkas fisik serah terima.</p>
                        </div>

                        <!-- Baris 2: Tanggal BAST & Kondisi Fisik Akhir (2 Kolom Seimbang) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Tanggal Pengembalian -->
                            <div class="min-w-0">
                                <label class="block text-[11px] font-bold text-slate-200 mb-1.5 flex items-center gap-1.5">
                                    <span>Tanggal BAST Pengembalian</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <input type="date" x-model="terminasiForm.tanggal_terminasi" required
                                       class="w-full min-w-0 bg-slate-900 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition-all cursor-pointer shadow-inner">
                                <p class="text-[10px] text-slate-400 mt-1">Tanggal resmi penandatanganan BAST dan serah terima unit.</p>
                            </div>

                            <!-- Kondisi Akhir -->
                            <div class="min-w-0">
                                <label class="block text-[11px] font-bold text-slate-200 mb-1.5 flex items-center gap-1.5">
                                    <span>Kondisi Fisik Akhir Saat Dikembalikan</span>
                                    <span class="text-rose-400">*</span>
                                </label>
                                <select x-model="terminasiForm.kondisi_akhir" required
                                        class="w-full min-w-0 bg-slate-900 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 py-2.5 text-xs text-white focus:outline-none transition-all cursor-pointer font-medium shadow-inner">
                                    <option value="Baik">🟢 Baik (Layak fungsi & operasional lengkap)</option>
                                    <option value="Rusak Ringan">🟡 Rusak Ringan (Perlu rekondisi minor)</option>
                                    <option value="Rusak Berat">🔴 Rusak Berat (Rusak permanen / tidak berfungsi)</option>
                                </select>
                                <p class="text-[10px] text-slate-400 mt-1">Status keutuhan dan kelayakan fisik aset saat diserahkan.</p>
                            </div>
                        </div>

                        <!-- Baris 3: Komparasi Pihak Penyerah & Penerima -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            <!-- Pihak Penyerah (RSUD Dr. H. Koesnandi) -->
                            <div class="p-4 rounded-xl bg-slate-900/90 border border-cyan-500/25 space-y-3 shadow-inner min-w-0">
                                <div class="flex items-center justify-between pb-1.5 border-b border-cyan-500/15">
                                    <span class="text-[10.5px] font-bold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5 font-mono">
                                        <span>🏛️</span>
                                        <span>Pihak Penyerah (RSUD)</span>
                                    </span>
                                    <span class="text-[9.5px] text-cyan-300 font-semibold bg-cyan-500/15 px-2 py-0.5 rounded-full border border-cyan-500/25">Pihak I</span>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-300 mb-1">
                                        Nama Pejabat RSUD <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="terminasiForm.pihak_penyerah_nama" required
                                           placeholder="Nama Pengurus Barang / PPK / Direktur"
                                           class="w-full min-w-0 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-300 mb-1">Jabatan Pejabat RSUD</label>
                                    <input type="text" x-model="terminasiForm.pihak_penyerah_jabatan"
                                           placeholder="Contoh: Direktur RSUD Dr. H. Koesnandi"
                                           class="w-full min-w-0 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                            </div>

                            <!-- Pihak Penerima (Mitra Rekanan) -->
                            <div class="p-4 rounded-xl bg-slate-900/90 border border-purple-500/25 space-y-3 shadow-inner min-w-0">
                                <div class="flex items-center justify-between pb-1.5 border-b border-purple-500/15">
                                    <span class="text-[10.5px] font-bold text-purple-400 uppercase tracking-wider flex items-center gap-1.5 font-mono">
                                        <span>🏢</span>
                                        <span>Pihak Penerima (Mitra Rekanan)</span>
                                    </span>
                                    <span class="text-[9.5px] text-purple-300 font-semibold bg-purple-500/15 px-2 py-0.5 rounded-full border border-purple-500/25">Pihak II</span>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-300 mb-1">
                                        Nama Pihak Mitra <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="terminasiForm.pihak_penerima_nama" required
                                           placeholder="Nama Pimpinan / Perwakilan Mitra"
                                           class="w-full min-w-0 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-slate-300 mb-1">Jabatan Pihak Mitra</label>
                                    <input type="text" x-model="terminasiForm.pihak_penerima_jabatan"
                                           placeholder="Contoh: Direktur / Penanggung Jawab Mitra"
                                           class="w-full min-w-0 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- Baris 4: Dropzone Berkas BAST & Catatan Tambahan -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-1">
                            <!-- Custom Bespoke File Dropzone -->
                            <div class="min-w-0">
                                <label class="block text-[11px] font-bold text-slate-200 mb-1.5">
                                    Berkas Fisik BAST Bertanda Tangan (Scan / PDF)
                                </label>
                                
                                <!-- Hidden Native Input -->
                                <input type="file" 
                                       x-ref="fileInputTerminasi" 
                                       class="hidden" 
                                       accept=".pdf,.jpg,.jpeg,.png" 
                                       @change="handleTerminasiFileChange($event)">

                                <!-- Dropzone Box -->
                                <div class="rounded-xl border-2 border-dashed transition-all p-4 text-center flex flex-col items-center justify-center min-h-[115px]"
                                     :class="isDraggingTerminasi 
                                        ? 'border-purple-400 bg-purple-500/15' 
                                        : (terminasiForm.berkas_ba_terminasi ? 'border-purple-500/40 bg-purple-950/20' : 'border-slate-700/80 hover:border-purple-400/60 bg-slate-900/60 hover:bg-slate-900')"
                                     @dragover.prevent="isDraggingTerminasi = true"
                                     @dragleave.prevent="isDraggingTerminasi = false"
                                     @drop.prevent="isDraggingTerminasi = false; if ($event.dataTransfer.files && $event.dataTransfer.files[0]) { handleTerminasiFileChange({ target: { files: $event.dataTransfer.files } }) }">

                                    <!-- Keadaan 1: Belum Ada File Dipilih -->
                                    <template x-if="!terminasiForm.berkas_ba_terminasi">
                                        <div class="cursor-pointer space-y-1.5 w-full" @click="$refs.fileInputTerminasi.click()">
                                            <div class="w-8 h-8 rounded-full bg-purple-500/15 text-purple-300 flex items-center justify-center mx-auto text-base">
                                                📁
                                            </div>
                                            <p class="text-xs font-semibold text-slate-200">
                                                Klik untuk unggah atau seret berkas ke sini
                                            </p>
                                            <p class="text-[10px] text-slate-400 font-mono">
                                                Format: PDF, JPG, PNG (Maksimal 10 MB)
                                            </p>
                                        </div>
                                    </template>

                                    <!-- Keadaan 2: File Telah Dipilih -->
                                    <template x-if="terminasiForm.berkas_ba_terminasi">
                                        <div class="w-full space-y-2">
                                            <div class="flex items-center justify-between p-2.5 rounded-lg bg-slate-900 border border-purple-500/30 text-left">
                                                <div class="flex items-center space-x-2.5 overflow-hidden min-w-0">
                                                    <span class="text-lg shrink-0">📄</span>
                                                    <div class="overflow-hidden min-w-0">
                                                        <p class="text-xs font-bold text-white truncate" x-text="terminasiForm.berkas_ba_terminasi.name"></p>
                                                        <p class="text-[10px] text-purple-300 font-mono" x-text="(terminasiForm.berkas_ba_terminasi.size / 1024).toFixed(1) + ' KB'"></p>
                                                    </div>
                                                </div>
                                                <button type="button" @click="removeTerminasiFile()" 
                                                        title="Hapus file"
                                                        class="w-6 h-6 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 flex items-center justify-center text-xs transition-colors shrink-0 cursor-pointer ml-2">
                                                    ✕
                                                </button>
                                            </div>
                                            <button type="button" @click="$refs.fileInputTerminasi.click()" 
                                                    class="text-[10.5px] text-purple-400 hover:text-purple-300 underline cursor-pointer">
                                                Ganti berkas lain
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Catatan Tambahan -->
                            <div class="min-w-0 flex flex-col">
                                <label class="block text-[11px] font-bold text-slate-200 mb-1.5">
                                    Catatan Tambahan / Alasan Pengembalian
                                </label>
                                <textarea x-model="terminasiForm.catatan_terminasi" rows="4"
                                          placeholder="Contoh: Masa konsesi/sewa telah selesai, seluruh unit aset ditarik dan diserahkan dalam keadaan berfungsi normal beserta kelengkapan aksesoris..."
                                          class="w-full min-w-0 flex-1 bg-slate-900 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all resize-none leading-relaxed shadow-inner"></textarea>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- ========================================================================= -->
                <!-- 3. STICKY MODAL FOOTER BAR                                                -->
                <!-- ========================================================================= -->
                <div class="shrink-0 px-6 py-4 bg-slate-950/90 border-t border-slate-800/80 backdrop-blur-md flex flex-col sm:flex-row items-center justify-between gap-3">
                    <button type="button" @click="printTerminasiBast('pengembalian')"
                            class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 text-purple-300 border border-purple-500/30 hover:border-purple-400 font-bold text-xs flex items-center justify-center gap-2 transition-all cursor-pointer shadow-sm">
                        <span>🖨️</span>
                        <span>Cetak Lembar BAST Pengembalian</span>
                    </button>

                    <div class="flex items-center gap-2.5 w-full sm:w-auto">
                        <button type="button" @click="closeTerminasiModal()"
                                class="flex-1 sm:flex-none px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit" :disabled="isSubmittingTerminasi"
                                class="flex-1 sm:flex-none px-5 py-2.5 rounded-xl bg-gradient-to-r from-purple-600 via-purple-500 to-indigo-600 hover:from-purple-500 hover:to-indigo-500 text-white font-black text-xs shadow-lg shadow-purple-600/25 transition-all flex items-center justify-center gap-2 disabled:opacity-50 cursor-pointer">
                            <span x-show="!isSubmittingTerminasi" class="flex items-center gap-1.5">
                                <span>📦</span>
                                <span>Simpan Pengembalian ke Mitra</span>
                            </span>
                            <span x-show="isSubmittingTerminasi" class="flex items-center gap-1.5">
                                <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memproses...</span>
                            </span>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
</template>
