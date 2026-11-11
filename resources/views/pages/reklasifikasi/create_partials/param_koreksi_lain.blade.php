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
                        Penyeimbang: Baris 42 (Koreksi Lain)
                    </span>
                </div>

                <!-- Sub-Kategori Koreksi Nilai (RMB 21 Kolom) -->
                <div class="space-y-1.5">
                    <div class="flex items-center justify-between">
                        <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span>⚖️</span>
                            <span>Sub-Kategori Koreksi Nilai:</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[9.5px] font-mono text-cyan-300">Format Lembar Kerja BPKAD</span>
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
                                    Kolom 15 / 5
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
                                    Kolom 16 / 6
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
                                    Kolom 17 / 7
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-400 leading-tight">
                                Penyelarasan SIMDA BMD / E-Manset BPKAD Kab. Bondowoso.
                            </p>
                        </label>
                    </div>
                </div>

                <!-- Tipe Penyesuaian: Tambah atau Kurang -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">
                        Arah Penyesuaian Dana <span class="text-rose-400">*</span>
                    </label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                        <!-- Kurang (-) -->
                        <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all select-none"
                               :class="formData.tipe_koreksi === 'kurang' ? 'border-rose-500 bg-rose-500/15 shadow-sm' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900'">
                            <input type="radio" x-model="formData.tipe_koreksi" value="kurang" @change="onTipeKoreksiChange()" class="text-rose-600 focus:ring-0">
                            <div>
                                <p class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <span class="text-rose-400 font-black text-sm">(-)</span> Koreksi Kurang Nilai Aset
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Pengurangan nilai buku aset tetap ke kolom penyeimbang neraca</p>
                            </div>
                        </label>

                        <!-- Tambah (+) -->
                        <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-all select-none"
                               :class="formData.tipe_koreksi === 'tambah' ? 'border-emerald-500 bg-emerald-500/15 shadow-sm' : 'border-slate-800 bg-slate-900/60 hover:bg-slate-900'">
                            <input type="radio" x-model="formData.tipe_koreksi" value="tambah" @change="onTipeKoreksiChange()" class="text-emerald-600 focus:ring-0">
                            <div>
                                <p class="text-xs font-bold text-white flex items-center gap-1.5">
                                    <span class="text-emerald-400 font-black text-sm">(+)</span> Koreksi Tambah Nilai Aset
                                </p>
                                <p class="text-[10px] text-slate-400 mt-0.5">Penambahan nilai buku aset tetap dari kolom penyeimbang neraca</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Dual Input yang saling sinkron: Nilai Baru vs Nominal Selisih -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                Nilai Realisasi Baru (Hasil Akhir)
                            </label>
                            <span class="text-[9.5px] text-slate-400">Total nilai setelah koreksi</span>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-slate-400 font-mono">Rp</span>
                            <input type="number" step="0.01" min="0" x-model="formData.nilai_realisasi_baru" @input="onNilaiBaruInput()"
                                   class="w-full pl-9 pr-3 py-2 rounded-lg bg-slate-950 border border-slate-700 text-white font-mono text-xs font-bold focus:outline-none focus:border-cyan-400">
                        </div>
                        <p class="text-[10px] text-slate-400">
                            Ketik total nominal akhir yang diinginkan, selisih reklas otomatis dihitung.
                        </p>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800 space-y-1.5">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-bold text-cyan-300 uppercase tracking-wider">
                                Nominal Koreksi / Selisih Reklas (Rp)
                            </label>
                            <span class="text-[9.5px] text-cyan-400 font-semibold" x-text="formData.tipe_koreksi === 'tambah' ? '(+ Menambah)' : '(- Mengurang)'"></span>
                        </div>
                        <div class="relative">
                            <span class="absolute left-3 top-2.5 text-xs text-cyan-400 font-mono">Rp</span>
                            <input type="number" step="0.01" min="0" x-model="formData.nilai_reklas" @input="onNominalSelisihInput()"
                                   class="w-full pl-9 pr-3 py-2 rounded-lg bg-slate-950 border border-cyan-500/60 text-cyan-300 font-mono text-xs font-black focus:outline-none focus:border-cyan-400">
                        </div>
                        <p class="text-[10px] text-slate-400">
                            Nominal yang akan dimasukkan ke kolom Mutasi Neraca.
                        </p>
                    </div>
                </div>

                <!-- Visual Banner Dampak Kalkulasi -->
                <div class="p-3 rounded-xl bg-cyan-950/30 border border-cyan-500/20 text-xs flex flex-wrap items-center justify-between gap-3">
                    <div class="space-y-0.5">
                        <span class="text-[10px] text-slate-400 uppercase tracking-wider block font-bold">Simulasi Perubahan Nilai Buku:</span>
                        <div class="flex items-center gap-2 font-mono">
                            <span class="text-slate-300">Rp <span x-text="Number(selectedAstap?.total_realisasi || 0).toLocaleString('id-ID')"></span></span>
                            <span class="font-black" :class="formData.tipe_koreksi === 'tambah' ? 'text-emerald-400' : 'text-rose-400'"
                                  x-text="formData.tipe_koreksi === 'tambah' ? ('➔ (+) Rp ' + Number(formData.nilai_reklas || 0).toLocaleString('id-ID')) : ('➔ (-) Rp ' + Number(formData.nilai_reklas || 0).toLocaleString('id-ID'))"></span>
                            <span class="text-slate-500 font-bold">=</span>
                            <span class="font-extrabold text-cyan-300">Rp <span x-text="Number(formData.nilai_realisasi_baru || 0).toLocaleString('id-ID')"></span></span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-[10px] text-slate-400 block font-semibold">Dampak Matriks Neraca:</span>
                        <span class="text-[11px] font-bold" :class="formData.tipe_koreksi === 'tambah' ? 'text-emerald-400' : 'text-rose-400'"
                              x-text="formData.tipe_koreksi === 'tambah' ? 'Mutasi Tambah Akun Aset Tetap' : 'Mutasi Kurang Akun Aset Tetap'"></span>
                    </div>
                </div>
            </div>

