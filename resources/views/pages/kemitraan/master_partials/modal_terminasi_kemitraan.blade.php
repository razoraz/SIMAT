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
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-md transition-opacity" 
         @click="if(!isSubmittingTerminasi) closeTerminasiModal()"></div>

    <div class="flex min-h-full items-center justify-center p-3 sm:p-4 text-center">
        <div x-show="showModalTerminasi"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="relative transform overflow-hidden rounded-3xl bg-slate-900 border border-purple-500/40 shadow-2xl shadow-purple-500/10 text-left transition-all w-full max-w-3xl my-auto max-h-[90vh] flex flex-col">

            <!-- Header Modal -->
            <div class="shrink-0 relative p-5 sm:p-6 border-b flex items-center justify-between bg-gradient-to-r from-purple-950/70 via-slate-900 to-slate-950 border-purple-500/20">
                <div class="flex items-center space-x-3.5">
                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-inner ring-1 bg-purple-500/20 border border-purple-500/40 text-purple-400 ring-purple-500/30">
                        <span>📦</span>
                    </div>
                    <div>
                        <span class="text-[10px] font-extrabold tracking-wider uppercase block mb-0.5 font-mono text-purple-400">
                            Penyelesaian Akhir Masa Konsesi / Kontrak Kemitraan
                        </span>
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

            <!-- Scrollable Body -->
            <div class="flex-1 overflow-y-auto p-5 sm:p-6 space-y-4 custom-scrollbar">

                <!-- 1. Ringkasan Aset yang Sedang Diproses -->
                <div class="p-4 rounded-2xl bg-slate-950/90 border border-slate-800 shadow-inner space-y-2.5">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-2">
                        <div>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block font-mono">
                                Objek Aset Kemitraan:
                            </span>
                            <h4 class="text-sm font-bold text-white flex items-center gap-2">
                                <span x-text="terminasiData.nama_barang || '-'"></span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-mono border"
                                      :class="terminasiData.tipe_kemitraan === 'dimanfaatkan' ? 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30' : 'bg-purple-500/10 text-purple-400 border-purple-500/30'"
                                      x-text="terminasiData.tipe_kemitraan === 'dimanfaatkan' ? 'Pemanfaatan BMD' : 'Aset Mitra Ditambahkan'"></span>
                            </h4>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block">Nilai Tercatat di PKS</span>
                            <span class="text-xs font-mono font-bold text-emerald-400" 
                                  x-text="'Rp ' + Number(terminasiData.nilai_aset || 0).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 text-[11px]">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Mitra Rekanan:</span>
                            <span class="font-semibold text-slate-200" x-text="terminasiData.mitra_nama || '-'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block">Nomor PKS:</span>
                            <span class="font-mono text-cyan-300" x-text="terminasiData.nomor_pks || '-'"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block">Masa Berlaku:</span>
                            <span class="text-slate-300" x-text="(terminasiData.tanggal_mulai_fmt || '-') + ' s/d ' + (terminasiData.tanggal_selesai_fmt || '-')"></span>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block">Status Saat Ini:</span>
                            <span class="font-bold text-amber-400" x-text="terminasiData.status_konsesi || 'Aktif'"></span>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- FORM PENGEMBALIAN KE MITRA REKANAN (BAST)                                 -->
                <!-- ========================================================================= -->
                <form @submit.prevent="submitKembalikanForm()" class="space-y-4">
                    <div class="p-4 rounded-2xl bg-purple-950/20 border border-purple-500/30 space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📋</span>
                                <span>Informasi Berita Acara Serah Terima Pengembalian (BAST)</span>
                            </span>
                            <span class="text-[10px] text-purple-400/80 font-mono">Permendagri 19/2016</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 mb-1">
                                    Nomor Berita Acara Pengembalian <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="terminasiForm.nomor_ba_terminasi" required
                                       placeholder="Contoh: BAST-KBL/001/RSUD/2026"
                                       class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none transition-colors">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 mb-1">
                                        Tanggal Pengembalian <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="date" x-model="terminasiForm.tanggal_terminasi" required
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-colors">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 mb-1">
                                        Kondisi Akhir <span class="text-rose-400">*</span>
                                    </label>
                                    <select x-model="terminasiForm.kondisi_akhir" required
                                            class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-colors">
                                        <option value="Baik">🟢 Baik</option>
                                        <option value="Rusak Ringan">🟡 Rusak Ringan</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Pihak Penyerah & Penerima -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-purple-500/20">
                            <!-- Pihak Penyerah (RSUD) -->
                            <div class="space-y-2">
                                <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider block">
                                    🏛️ Pihak Penyerah (RSUD Dr. H. Koesnandi)
                                </span>
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-0.5">Nama Pejabat RSUD <span class="text-rose-400">*</span></label>
                                    <input type="text" x-model="terminasiForm.pihak_penyerah_nama" required
                                           placeholder="Nama Pengurus Barang / PPK"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-0.5">Jabatan Pejabat RSUD</label>
                                    <input type="text" x-model="terminasiForm.pihak_penyerah_jabatan"
                                           placeholder="Contoh: Pengurus Barang Pengguna"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none">
                                </div>
                            </div>

                            <!-- Pihak Penerima (Mitra Rekanan) -->
                            <div class="space-y-2">
                                <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider block">
                                    🏢 Pihak Penerima (Mitra Rekanan)
                                </span>
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-0.5">Nama Pihak Mitra <span class="text-rose-400">*</span></label>
                                    <input type="text" x-model="terminasiForm.pihak_penerima_nama" required
                                           placeholder="Nama Pimpinan / Perwakilan Mitra"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] text-slate-400 mb-0.5">Jabatan Pihak Mitra</label>
                                    <input type="text" x-model="terminasiForm.pihak_penerima_jabatan"
                                           placeholder="Contoh: Direktur / Penanggung Jawab KSO"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none">
                                </div>
                            </div>
                        </div>

                        <!-- Unggah Berkas & Catatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-purple-500/20">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 mb-1">
                                    Unggah Berkas Fisik BAST Pengembalian (PDF / Gambar)
                                </label>
                                <input type="file" @change="handleTerminasiFileChange($event)" accept=".pdf,.jpg,.jpeg,.png"
                                       class="w-full text-xs text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-purple-500/20 file:text-purple-300 hover:file:bg-purple-500/30 cursor-pointer">
                                <p class="text-[9.5px] text-slate-400 mt-1">Maksimal ukuran file 10MB (PDF atau Foto scan BAST bertanda tangan).</p>
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-slate-300 mb-1">Catatan Tambahan / Alasan Pengembalian</label>
                                <textarea x-model="terminasiForm.catatan_terminasi" rows="2"
                                          placeholder="Contoh: Masa sewa selesai, alat ditarik dalam kondisi baik lengkap beserta aksesoris..."
                                          class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Action -->
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3 pt-2">
                        <button type="button" @click="printTerminasiBast('pengembalian')"
                                class="w-full sm:w-auto px-4 py-2 rounded-xl bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 font-bold text-xs flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                            <span>🖨️ Cetak Lembar BAST Pengembalian</span>
                        </button>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <button type="button" @click="closeTerminasiModal()"
                                    class="flex-1 sm:flex-none px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="isSubmittingTerminasi"
                                    class="flex-1 sm:flex-none px-5 py-2 rounded-xl bg-purple-500 hover:bg-purple-400 text-slate-950 font-black text-xs shadow-lg shadow-purple-500/20 transition-all flex items-center justify-center gap-1.5 disabled:opacity-50 cursor-pointer">
                                <span x-show="!isSubmittingTerminasi">📦 Simpan Pengembalian ke Mitra</span>
                                <span x-show="isSubmittingTerminasi">Memproses...</span>
                            </button>
                        </div>
                    </div>
                </form>

            </div>

        </div>
    </div>
</div>
</template>
