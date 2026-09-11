            {{-- ========================================================================= --}}
            {{-- ===== STEP 1: PILIH JENIS PENGAJUAN MUTASI ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">1</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 1: Pilih Jenis Pengajuan Mutasi</h2>
                            <p class="text-xs text-slate-400">Pilih salah satu dari 4 jenis pengajuan mutasi di bawah ini.</p>
                        </div>
                    </div>
                    <span class="text-[11px] px-3 py-1 rounded-full bg-rose-500/10 text-rose-300 border border-rose-500/30 font-bold">Wajib Pilih 1</span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <template x-for="opt in jenisMutasiOptions" :key="opt.value">
                        <button type="button" @click="selectJenisMutasi(opt.value)"
                            :class="{
                                'border-blue-500 bg-blue-500/15 ring-2 ring-blue-500/40 shadow-xl shadow-blue-500/10':   jenis_mutasi === opt.value && opt.color === 'blue',
                                'border-amber-500 bg-amber-500/15 ring-2 ring-amber-500/40 shadow-xl shadow-amber-500/10': jenis_mutasi === opt.value && opt.color === 'amber',
                                'border-teal-500 bg-teal-500/15 ring-2 ring-teal-500/40 shadow-xl shadow-teal-500/10':   jenis_mutasi === opt.value && opt.color === 'teal',
                                'border-rose-500 bg-rose-500/15 ring-2 ring-rose-500/40 shadow-xl shadow-rose-500/10':   jenis_mutasi === opt.value && opt.color === 'rose',
                                'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 hover:border-slate-700': jenis_mutasi !== opt.value
                            }"
                            class="relative flex items-start space-x-4 p-5 rounded-2xl border-2 text-left transition-all w-full active:scale-[0.99] cursor-pointer group">
                            
                            <!-- Selected Indicator Checkmark -->
                            <div x-show="jenis_mutasi === opt.value" class="absolute top-4 right-4 w-6 h-6 rounded-full bg-rose-500 text-slate-950 flex items-center justify-center text-xs font-black shadow">✓</div>

                            <span class="text-2xl shrink-0 p-3 rounded-2xl bg-slate-900 border border-slate-800 group-hover:scale-110 transition-transform" x-text="opt.emoji"></span>
                            <div class="pr-6">
                                <p class="text-sm font-extrabold text-white" x-text="opt.label"></p>
                                <p class="text-xs text-slate-400 mt-1 leading-relaxed" x-text="opt.desc"></p>
                            </div>
                        </button>
                    </template>
                </div>

                <!-- PILIHAN OPSI KHUSUS UNTUK PENGEMBALIAN BARANG -->
                <template x-if="jenis_mutasi === 'Pengembalian'">
                    <div class="p-5 bg-slate-950/80 border border-rose-500/30 rounded-2xl space-y-3 shadow-inner">
                        <div class="flex items-center space-x-2">
                            <span class="text-rose-400 font-extrabold text-xs uppercase tracking-wider">↩️ Tentukan Tujuan Pengembalian Barang:</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <!-- Opsi A: Ke Gudang -->
                            <button type="button" @click="setPengembalianTarget('gudang')"
                                :class="subJenisPengembalian === 'gudang' ? 'border-rose-500 bg-rose-500/20 text-white font-extrabold ring-2 ring-rose-500/40 shadow-lg shadow-rose-500/10' : 'border-slate-800 bg-slate-900 text-slate-400 hover:bg-slate-800/80'"
                                class="p-4 rounded-xl border text-left transition-all cursor-pointer flex items-start space-x-3 group">
                                <span class="text-2xl shrink-0 p-2 rounded-lg bg-slate-950 border border-slate-800">📦</span>
                                <div>
                                    <p class="text-xs font-extrabold text-white">Ke Gudang Utama / Pengurus Barang</p>
                                    <p class="text-[11px] text-slate-400 font-normal mt-1 leading-snug">Barang dikembalikan ke Pengurus Barang / Inst. Perbekalan & Rumah Tangga RSUD.</p>
                                </div>
                            </button>

                            <!-- Opsi B: Ke Unit Terkait (Selesai Perbaikan) -->
                            <button type="button" @click="setPengembalianTarget('unit_terkait')"
                                :class="subJenisPengembalian === 'unit_terkait' ? 'border-rose-500 bg-rose-500/20 text-white font-extrabold ring-2 ring-rose-500/40 shadow-lg shadow-rose-500/10' : 'border-slate-800 bg-slate-900 text-slate-400 hover:bg-slate-800/80'"
                                class="p-4 rounded-xl border text-left transition-all cursor-pointer flex items-start space-x-3 group">
                                <span class="text-2xl shrink-0 p-2 rounded-lg bg-slate-950 border border-slate-800">🔧</span>
                                <div>
                                    <p class="text-xs font-extrabold text-white">Ke Unit Pemilik / Ruangan Terkait (Setelah Perbaikan)</p>
                                    <p class="text-[11px] text-slate-400 font-normal mt-1 leading-snug">Barang yang sudah selesai diperbaiki dikembalikan ke ruangan / unit pemilik asal (pilih unit tujuan di Langkah 2).</p>
                                </div>
                            </button>
                        </div>
                    </div>
                </template>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-end pt-4 border-t border-slate-800">
                    <button type="button" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-extrabold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 2: Unit Pengirim & Penerima →</span>
                    </button>
                </div>
            </div>
