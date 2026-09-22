            {{-- ========================================================================= --}}
            {{-- ===== STEP 4: PREVIEW & KONFIRMASI (RINGKASAN BAMB MULTI-ITEM) ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 4" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center justify-center text-xs font-extrabold">4</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 4: Review & Konfirmasi Pengajuan Mutasi</h2>
                            <p class="text-xs text-slate-400">Periksa kembali seluruh ringkasan data sebelum mengirimkan dokumen pengajuan mutasi aset.</p>
                        </div>
                    </div>
                    <span class="text-[11px] px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-300 border border-emerald-500/30 font-bold">Langkah Terakhir</span>
                </div>

                {{-- Summary Card Review --}}
                <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 space-y-5">
                    
                    {{-- Jenis Mutasi Badge --}}
                    <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                        <span class="text-xs font-bold text-slate-400">Jenis Pengajuan:</span>
                        <div class="flex items-center space-x-2">
                            <span class="text-xl" x-text="selectedJenis?.emoji"></span>
                            <span class="text-sm font-black text-white" x-text="jenis_mutasi"></span>
                        </div>
                    </div>

                    {{-- Data Daftar Barang Terpilih --}}
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">📦 Daftar Barang Aset Terpilih (<span x-text="selectedRegistersList.length"></span> Barang):</span>
                            <span class="text-[10.5px] text-rose-400 font-mono font-bold" x-text="selectedRegistersList.length + ' Item Terdaftar'"></span>
                        </div>

                        <div class="overflow-x-auto rounded-xl border border-slate-800 bg-slate-900">
                            <table class="w-full text-left text-xs text-slate-300">
                                <thead>
                                    <tr class="bg-slate-950 text-slate-400 font-bold border-b border-slate-800 text-[10.5px] uppercase">
                                        <th class="py-2.5 px-3 w-10 text-center">#</th>
                                        <th class="py-2.5 px-3">NIBAR</th>
                                        <th class="py-2.5 px-3">Nama Barang</th>
                                        <th class="py-2.5 px-3">Kondisi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, idx) in selectedRegistersList" :key="item.id">
                                        <tr class="border-b border-slate-800/60 last:border-b-0 hover:bg-slate-800/50 transition-colors">
                                            <td class="py-2 px-3 text-center text-slate-500 font-mono font-bold" x-text="idx + 1"></td>
                                            <td class="py-2 px-3 font-mono font-bold text-rose-400" x-text="item.nibar"></td>
                                            <td class="py-2 px-3 font-extrabold text-white" x-text="item.nama_barang"></td>
                                            <td class="py-2.5 px-3 font-bold">
                                                <span :class="{
                                                    'text-emerald-400': (editedKondisi[item.id] || item.kondisi) === 'Baik',
                                                    'text-amber-400':   (editedKondisi[item.id] || item.kondisi) === 'Kurang Baik',
                                                    'text-rose-400':    (editedKondisi[item.id] || item.kondisi) === 'Rusak Berat'
                                                }" x-text="editedKondisi[item.id] || item.kondisi"></span>
                                                <template x-if="canChangeKondisi && editedKondisi[item.id] && editedKondisi[item.id] !== item.kondisi">
                                                    <span class="text-[9.5px] px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 font-bold ml-1 border border-amber-500/30">
                                                        Diubah dari <span x-text="item.kondisi"></span>
                                                    </span>
                                                </template>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    {{-- Alur Perpindahan --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="p-4 rounded-xl bg-slate-900 border border-slate-800">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1">📤 Ruangan Pengirim (Asal)</span>
                            <p class="text-sm font-bold text-white" x-text="ruangan_asal || '-'"></p>
                            <p class="text-xs text-slate-400 mt-1" x-text="'PJ: ' + (penanggung_jawab_asal || '-')"></p>
                        </div>
                        <div class="p-4 rounded-xl bg-slate-900 border border-rose-500/30">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-rose-400 block mb-1">📥 Ruangan Penerima (Tujuan)</span>
                            <p class="text-sm font-bold text-white" x-text="ruangan_tujuan || '-'"></p>
                            <p class="text-xs text-slate-400 mt-1" x-text="'PJ: ' + (penanggung_jawab_tujuan || '-')"></p>
                        </div>
                    </div>

                    {{-- Alasan Mutasi --}}
                    <div class="p-4 rounded-xl bg-slate-900 border border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Alasan & Urgensi Mutasi</span>
                        <p class="text-xs text-slate-200 leading-relaxed font-semibold" x-text="alasan_mutasi || 'Belum diisi'"></p>
                    </div>

                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <button type="button" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        ← Kembali ke Langkah 3
                    </button>
                    <button type="submit"
                        class="px-7 py-3 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-extrabold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        <span x-text="isEdit ? '✏️ Simpan Perubahan Mutasi' : '🚀 Kirim Pengajuan Mutasi'"></span>
                    </button>
                </div>
            </div>
