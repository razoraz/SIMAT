        <!-- MODAL INPUT ALASAN PENOLAKAN -->
        <div x-show="showRejectModal" class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 10000;" @click.self="showRejectModal = false" x-cloak>
            <div class="border border-slate-800 rounded-3xl max-w-md w-full p-6 shadow-2xl space-y-4" style="background-color: #0f172a;">
                <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                    <div class="flex items-center space-x-2.5">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 border border-rose-500/30 text-rose-400 flex items-center justify-center font-bold text-sm">
                            ✕
                        </div>
                        <div>
                            <h3 class="text-sm font-extrabold text-white">Penolakan Mutasi Aset</h3>
                            <p class="text-[11px] text-slate-400 font-mono" x-text="rejectTargetItem ? rejectTargetItem.kode : ''"></p>
                        </div>
                    </div>
                    <button type="button" @click.stop="showRejectModal = false" class="text-slate-500 hover:text-white text-xl font-bold cursor-pointer">&times;</button>
                </div>

                <div class="space-y-3 text-xs">
                    <p class="text-slate-300 font-semibold leading-relaxed">
                        Silakan masukkan alasan penolakan pengajuan Berita Acara Mutasi:
                        <span class="text-rose-300 font-bold block mt-1" x-text="rejectTargetItem ? rejectTargetItem.nama : ''"></span>
                    </p>

                    <div class="space-y-1.5 pt-1">
                        <label class="block text-[10.5px] font-bold text-slate-400 uppercase tracking-wider">Alasan Penolakan <span class="text-rose-400">*</span></label>
                        <textarea x-model="rejectAlasan" rows="3" placeholder="Contoh: Unit penerima belum siap / spesifikasi barang tidak sesuai..."
                            class="w-full bg-slate-950 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-rose-500 transition-all resize-none"></textarea>
                    </div>
                </div>

                <div class="pt-3 border-t border-slate-800 flex items-center justify-end space-x-2">
                    <button type="button" @click.stop="showRejectModal = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click.stop="confirmRejectMutasi()"
                        class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-400 text-white text-xs font-extrabold shadow-lg shadow-rose-500/25 transition-all cursor-pointer">
                        🚫 Konfirmasi Tolak Mutasi
                    </button>
                </div>
            </div>
        </div>
