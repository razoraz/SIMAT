<!-- MODAL: DETAIL TRANSAKSI HIBAH ASET -->
<div x-show="showModalDetail" x-cloak @click.self="showModalDetail = false"
    class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto"
    style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px);">
    
    <div class="bg-slate-900 border border-slate-800 rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl space-y-6 my-auto">
        <!-- Header -->
        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-xl shrink-0"
                    :class="selectedDetail?.tipe_hibah === 'masuk' ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'">
                    <span x-text="selectedDetail?.tipe_hibah === 'masuk' ? '🎁' : '📤'"></span>
                </div>
                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-lg font-black text-white" x-text="selectedDetail?.tipe_hibah === 'masuk' ? 'Detail Hibah Masuk' : 'Detail Hibah Keluar'"></h3>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold"
                            :class="selectedDetail?.tipe_hibah === 'masuk' ? 'bg-amber-400/20 text-amber-300 border border-amber-400/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'"
                            x-text="selectedDetail?.tipe_hibah === 'masuk' ? 'HIBAH MASUK' : 'PENGURANGAN AT'"></span>
                    </div>
                    <p class="text-xs text-slate-400 mt-0.5 font-mono" x-text="'No. BAST: ' + (selectedDetail?.nomor_bast || '-')"></p>
                </div>
            </div>
            <button type="button" @click="showModalDetail = false"
                class="w-8 h-8 rounded-full bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-400 flex items-center justify-center transition-all cursor-pointer">
                ✕
            </button>
        </div>

        <!-- Detail Content -->
        <template x-if="selectedDetail">
            <div class="space-y-4 text-xs">
                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Card 1: Barang -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block">📦 Identitas Barang</span>
                        <div class="font-extrabold text-white text-sm" x-text="selectedDetail.astap ? selectedDetail.astap.nama_barang : (selectedDetail.nama_barang || '-')"></div>
                        <div class="text-[11px] font-mono text-cyan-400" x-text="'Kode 108: ' + (selectedDetail.astap?.kode_108 || selectedDetail.kode_108 || '-')"></div>
                        <div class="pt-1 text-slate-300 flex items-center justify-between border-t border-slate-800/80">
                            <span>Volume:</span>
                            <span class="font-bold text-teal-300 font-mono" x-text="(selectedDetail.jumlah_volume || 1) + ' ' + (selectedDetail.satuan || 'Unit')"></span>
                        </div>
                    </div>

                    <!-- Card 2: Legalitas & Pihak -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2">
                        <span class="text-slate-400 text-[10px] uppercase font-bold block">📜 Dokumen &amp; Pihak</span>
                        <div>
                            <span class="text-slate-400 text-[10px] block" x-text="selectedDetail.tipe_hibah === 'masuk' ? 'Pemberi Hibah:' : 'Penerima Hibah:'"></span>
                            <span class="font-bold text-amber-300" x-text="selectedDetail.pihak_hibah || '-'"></span>
                        </div>
                        <div>
                            <span class="text-slate-400 text-[10px] block">Tanggal BAST:</span>
                            <span class="font-medium text-slate-200" x-text="formatTanggalIndo(selectedDetail.tanggal_bast)"></span>
                        </div>
                        <div class="pt-1 text-slate-300 flex items-center justify-between border-t border-slate-800/80">
                            <span>Periode Pembukuan:</span>
                            <span class="font-bold text-white" x-text="selectedDetail.triwulan + ' TA ' + selectedDetail.tahun"></span>
                        </div>
                    </div>
                </div>

                <!-- Total Banner -->
                <div class="p-4 rounded-2xl border flex items-center justify-between"
                    :class="selectedDetail.tipe_hibah === 'masuk' ? 'bg-amber-400/10 border-amber-400/30' : 'bg-rose-500/10 border-rose-500/30'">
                    <div>
                        <span class="text-[10px] font-bold uppercase tracking-wider block"
                            :class="selectedDetail.tipe_hibah === 'masuk' ? 'text-amber-400' : 'text-rose-400'">
                            Total Nilai Aset Hibah:
                        </span>
                        <div class="text-2xl font-black font-mono mt-0.5"
                            :class="selectedDetail.tipe_hibah === 'masuk' ? 'text-amber-300' : 'text-rose-300'"
                            x-text="formatRupiah(selectedDetail.nilai_aset)"></div>
                    </div>
                    <div class="text-right text-[11px] text-slate-400">
                        <div>Nilai Satuan:</div>
                        <div class="font-mono font-bold text-white text-xs" x-text="formatRupiah(selectedDetail.nilai_aset / Math.max(1, selectedDetail.jumlah_volume || 1))"></div>
                    </div>
                </div>

                <!-- Keterangan Tambahan -->
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 space-y-1" x-show="selectedDetail.keterangan">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Catatan / Alasan:</span>
                    <p class="text-slate-200 text-xs italic" x-text="selectedDetail.keterangan || '-'"></p>
                </div>

                <!-- Audit info -->
                <div class="text-[10px] text-slate-500 flex items-center justify-between pt-2">
                    <span>Operator: <strong class="text-slate-400" x-text="selectedDetail.user ? selectedDetail.user.name : 'Administrator'"></strong></span>
                    <span>Dicatat: <strong class="text-slate-400" x-text="formatTanggalIndo(selectedDetail.created_at)"></strong></span>
                </div>
            </div>
        </template>

        <!-- Footer -->
        <div class="pt-4 border-t border-slate-800 flex items-center justify-between">
            <template x-if="selectedDetail">
                <button type="button" @click="showModalDetail = false; openPrintBast(selectedDetail);"
                    class="px-4 py-2.5 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    <span>🖨️ Cetak Lembar BAST Resmi</span>
                </button>
            </template>
            <button type="button" @click="showModalDetail = false"
                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs transition-colors ml-auto">
                Tutup
            </button>
        </div>
    </div>
</div>
