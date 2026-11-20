            <!-- 2B. Panel Khusus Penyesuaian Nilai Realisasi / Perubahan Total Dana -->
            <div x-show="formData.jenis_reklas === 'KOREKSI_LAIN'"
                 x-transition.duration.200ms
                 class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-cyan-500/30 space-y-4 shadow-xl">
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-xs font-black flex items-center justify-center">💰</span>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                            Penyesuaian Nilai Buku &amp; Perubahan Total Dana Aset
                        </span>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 border border-cyan-500/40">
                        Penyeimbang: Koreksi Nilai Realisasi
                    </span>
                </div>

                <!-- Sub-Kategori Koreksi Nilai -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span>⚖️</span>
                            <span>Sub-Kategori Koreksi Nilai:</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[9.5px] font-mono text-cyan-300">Standar BPKAD Bondowoso</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                        <!-- 1. Koreksi Biasa -->
                        <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition-all select-none group"
                               :class="formData.sub_koreksi === 'biasa' ? 'border-indigo-500 bg-indigo-500/15 shadow-sm ring-1 ring-indigo-500/30' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900'">
                            <input type="radio" x-model="formData.sub_koreksi" value="biasa" class="hidden" style="display: none;">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold" :class="formData.sub_koreksi === 'biasa' ? 'text-indigo-300' : 'text-white'">
                                    📝 Koreksi Biasa
                                </span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded"
                                      :class="formData.sub_koreksi === 'biasa' ? 'bg-indigo-500/30 text-indigo-200' : 'bg-slate-800 text-slate-400'">
                                    SP2D / Kas RSUD
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-400 leading-tight">
                                Internal kas RSUD, pembulatan SP2D, koreksi salah catat belanja.
                            </p>
                        </label>

                        <!-- 2. Koreksi LKD -->
                        <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition-all select-none group"
                               :class="formData.sub_koreksi === 'lkd' ? 'border-cyan-500 bg-cyan-500/15 shadow-sm ring-1 ring-cyan-500/30' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900'">
                            <input type="radio" x-model="formData.sub_koreksi" value="lkd" class="hidden" style="display: none;">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold" :class="formData.sub_koreksi === 'lkd' ? 'text-cyan-300' : 'text-white'">
                                    ⚖️ Koreksi LKD
                                </span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded"
                                      :class="formData.sub_koreksi === 'lkd' ? 'bg-cyan-500/30 text-cyan-200' : 'bg-slate-800 text-slate-400'">
                                    Audit BPK / LHP
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-400 leading-tight">
                                Temuan audit BPK RI, rekomendasi LHP LKPD, kelebihan bayar/TGR.
                            </p>
                        </label>

                        <!-- 3. Koreksi Manset -->
                        <label class="flex flex-col p-3 rounded-xl border cursor-pointer transition-all select-none group"
                               :class="formData.sub_koreksi === 'manset' ? 'border-emerald-500 bg-emerald-500/15 shadow-sm ring-1 ring-emerald-500/30' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900'">
                            <input type="radio" x-model="formData.sub_koreksi" value="manset" class="hidden" style="display: none;">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-bold" :class="formData.sub_koreksi === 'manset' ? 'text-emerald-300' : 'text-white'">
                                    🏢 Koreksi Manset
                                </span>
                                <span class="text-[9px] font-mono font-bold px-1.5 py-0.5 rounded"
                                      :class="formData.sub_koreksi === 'manset' ? 'bg-emerald-500/30 text-emerald-200' : 'bg-slate-800 text-slate-400'">
                                    Sinkron E-Manset
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-400 leading-tight">
                                Penyelarasan SIMDA BMD / E-Manset BPKAD Kab. Bondowoso.
                            </p>
                        </label>
                    </div>
                </div>
            </div>
