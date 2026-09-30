<!-- ========================================================================= -->
<!-- FRONTEND MODAL: PRATINJAU & DOWNLOAD QR CODE ASET KEMITRAAN               -->
<!-- ========================================================================= -->
<div x-show="showQrModal" x-cloak @click.self="showQrModal = false" class="fixed inset-0 flex items-center justify-center p-4 overflow-y-auto" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 9999;">
    <div class="border border-cyan-500/30 rounded-3xl max-w-md w-full p-6 shadow-2xl text-center space-y-5 max-h-[90vh] overflow-y-auto my-auto" style="background-color: #0f172a;">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <div class="flex items-center space-x-2">
                <span class="text-lg">🤝</span>
                <h3 class="text-base font-extrabold text-white">Label QR Code Aset Kemitraan</h3>
            </div>
            <button type="button" @click.stop="showQrModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
        </div>

        <template x-if="selectedQrItem">
            <div class="space-y-4">
                <!-- Gambar QR Code yang Berisi URL Publik (Bisa Di-scan HP Tanpa Login) -->
                <div class="p-4 bg-white rounded-2xl inline-block shadow-lg border-2 border-cyan-500/40 min-w-[230px] min-h-[230px] relative">
                    <template x-if="isGeneratingQr">
                        <div class="w-52 h-52 flex flex-col items-center justify-center text-slate-500 space-y-2.5">
                            <svg class="animate-spin h-8 w-8 text-cyan-600" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-bold text-slate-700">Membuat QR Code...</span>
                        </div>
                    </template>
                    <template x-if="!isGeneratingQr && qrDataUrl">
                        <img :src="qrDataUrl"
                             :alt="selectedQrItem.nama_barang"
                             class="w-52 h-52 mx-auto object-contain rounded-lg" />
                    </template>
                </div>

                <!-- Kartu Informasi Detail Barang Sesuai QR Code -->
                <div class="p-3.5 bg-slate-950/80 rounded-2xl border border-slate-800 text-left space-y-2 text-xs">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-slate-400 font-semibold text-[10.5px]">📦 Nama Barang:</span>
                        <span class="text-white font-bold text-right max-w-[200px] truncate" x-text="selectedQrItem.nama_barang"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-slate-400 font-semibold text-[10.5px]">🏷️ NIBAR / Kode:</span>
                        <span class="text-cyan-400 font-bold font-mono text-right text-[11px]" x-text="selectedQrItem.kode_barang"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-slate-400 font-semibold text-[10.5px]">📅 Tahun Perolehan:</span>
                        <span class="text-slate-200 font-bold font-mono" x-text="selectedQrItem.tahun_perolehan"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-slate-400 font-semibold text-[10.5px]">📍 Penempatan Ruangan:</span>
                        <span class="text-teal-300 font-bold text-right max-w-[190px] truncate" x-text="selectedQrItem.ruang_pemegang"></span>
                    </div>
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <span class="text-slate-400 font-semibold text-[10.5px]">⚙️ Kondisi Aset:</span>
                        <span class="text-emerald-300 font-extrabold" x-text="selectedQrItem.kondisi"></span>
                    </div>
                    <div class="flex items-start justify-between pt-0.5">
                        <span class="text-slate-400 font-semibold text-[10.5px] shrink-0 mr-2">🤝 Info Konsesi:</span>
                        <span class="text-cyan-300 font-medium text-right text-[10.5px]" x-text="selectedQrItem.riwayat_servis"></span>
                    </div>
                </div>

                <!-- Box URL Publik Scan -->
                <div class="p-3 bg-slate-950 rounded-xl border border-slate-800 text-left space-y-1.5">
                    <span class="text-[9.5px] font-bold text-slate-500 uppercase tracking-wider block">🔗 URL Publik Terenkripsi QR Code (Tanpa Login):</span>
                    <a :href="getQrPayloadUrl(selectedQrItem)" target="_blank"
                       class="font-mono text-[10.5px] text-cyan-400 hover:underline block truncate" x-text="getQrPayloadUrl(selectedQrItem)"></a>
                </div>

                <!-- Tombol Aksi -->
                <div class="flex flex-col sm:flex-row items-center justify-center gap-2.5 pt-1">
                    <button type="button" @click="downloadQrImage()"
                        class="w-full sm:flex-1 py-2.5 px-4 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-cyan-500/20 transition-all flex items-center justify-center space-x-2 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Unduh QR</span>
                    </button>
                    <a :href="getQrPayloadUrl(selectedQrItem)" target="_blank"
                       class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-300 border border-cyan-500/40 font-bold text-xs transition-all flex items-center justify-center space-x-1.5">
                        <span>🌐 Buka Halaman Scan</span>
                    </a>
                    <button type="button" @click="showQrModal = false"
                        class="w-full sm:w-auto py-2.5 px-4 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-all cursor-pointer">
                        Tutup
                    </button>
                </div>
            </div>
        </template>
    </div>
</div>
