        <!-- 4. MODAL UBAH KONDISI BARANG (INTERAKTIF & REALTIME) -->
        <div x-show="showEditModal" x-cloak 
            class="flex items-center justify-center p-4"
            style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999 !important; background-color: rgba(2, 6, 23, 0.88) !important; backdrop-filter: blur(14px) !important; -webkit-backdrop-filter: blur(14px) !important;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
            <div @click.away="if (!isSavingKondisi) showEditModal = false"
                class="bg-slate-900 border border-slate-800 rounded-3xl max-w-lg w-full p-6 shadow-2xl relative overflow-hidden"
                x-transition:enter="transition ease-out duration-300 transform"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200 transform"
                x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
                
                <template x-if="editAsset">
                    <div>
                        <!-- Header Modal Ubah -->
                        <div class="flex items-start justify-between border-b border-slate-800 pb-4 mb-4">
                            <div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-0.5 rounded font-mono text-xs font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                        🛠️ UBAH KONDISI FISIK
                                    </span>
                                    <span class="font-mono text-xs text-slate-400" x-text="editAsset.nibar"></span>
                                </div>
                                <h3 class="text-base sm:text-lg font-black text-white mt-1.5" x-text="editAsset.nama"></h3>
                            </div>
                            <button type="button" @click="showEditModal = false" :disabled="isSavingKondisi" class="text-slate-400 hover:text-white p-1 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <!-- Body Modal Ubah -->
                        <div class="space-y-4 text-xs">
                            <!-- Info Singkat Aset -->
                            <div class="p-3 bg-slate-950 rounded-2xl border border-slate-800 flex items-center justify-between text-xs">
                                <div>
                                    <span class="text-slate-500 text-[10px] block uppercase font-bold">Ruangan Terpasang</span>
                                    <span class="text-white font-semibold">{{ $unitNama }}</span>
                                </div>
                                <div class="text-right">
                                    <span class="text-slate-500 text-[10px] block uppercase font-bold">Kondisi Saat Ini</span>
                                    <span class="font-bold text-amber-400" x-text="editAsset.kondisi"></span>
                                </div>
                            </div>

                            <!-- Opsi Pilihan Kondisi (3 Radio Cards) -->
                            <div>
                                <label class="block text-slate-300 font-bold mb-2">Pilih Kondisi Fisik Baru:</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <!-- 1. Baik -->
                                    <label class="p-3 rounded-xl border cursor-pointer transition-all flex items-start space-x-2.5"
                                        :class="editForm.kondisi === 'Baik' ? 'bg-emerald-500/15 border-emerald-500 text-white shadow-sm' : 'bg-slate-950 border-slate-800 text-slate-400 hover:border-slate-700'">
                                        <input type="radio" name="pilihan_kondisi" value="Baik" x-model="editForm.kondisi" class="mt-0.5 text-emerald-500 focus:ring-0">
                                        <div>
                                            <span class="font-bold block text-xs" :class="editForm.kondisi === 'Baik' ? 'text-emerald-300' : 'text-white'">🟢 Baik</span>
                                            <span class="text-[10px] text-slate-400 leading-tight block mt-0.5">Berfungsi normal &amp; siap digunakan</span>
                                        </div>
                                    </label>

                                    <!-- 2. Kurang Baik -->
                                    <label class="p-3 rounded-xl border cursor-pointer transition-all flex items-start space-x-2.5"
                                        :class="editForm.kondisi === 'Kurang Baik' ? 'bg-amber-500/15 border-amber-500 text-white shadow-sm' : 'bg-slate-950 border-slate-800 text-slate-400 hover:border-slate-700'">
                                        <input type="radio" name="pilihan_kondisi" value="Kurang Baik" x-model="editForm.kondisi" class="mt-0.5 text-amber-500 focus:ring-0">
                                        <div>
                                            <span class="font-bold block text-xs" :class="editForm.kondisi === 'Kurang Baik' ? 'text-amber-300' : 'text-white'">🟡 Kurang Baik</span>
                                            <span class="text-[10px] text-slate-400 leading-tight block mt-0.5">Kendala minor / aus / perlu servis</span>
                                        </div>
                                    </label>

                                    <!-- 3. Rusak Berat -->
                                    <label class="p-3 rounded-xl border cursor-pointer transition-all flex items-start space-x-2.5"
                                        :class="editForm.kondisi === 'Rusak Berat' ? 'bg-rose-500/15 border-rose-500 text-white shadow-sm' : 'bg-slate-950 border-slate-800 text-slate-400 hover:border-slate-700'">
                                        <input type="radio" name="pilihan_kondisi" value="Rusak Berat" x-model="editForm.kondisi" class="mt-0.5 text-rose-500 focus:ring-0">
                                        <div>
                                            <span class="font-bold block text-xs" :class="editForm.kondisi === 'Rusak Berat' ? 'text-rose-300' : 'text-white'">🔴 Rusak Berat</span>
                                            <span class="text-[10px] text-slate-400 leading-tight block mt-0.5">Mati total / tidak dapat digunakan</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Modal Ubah -->
                        <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                            <button type="button" @click="showEditModal = false" :disabled="isSavingKondisi"
                                class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all">
                                Batal
                            </button>
                            <button type="button" @click="saveKondisi()" :disabled="isSavingKondisi"
                                class="px-5 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 disabled:opacity-50">
                                <span x-show="isSavingKondisi" class="animate-spin text-sm leading-none">⚙️</span>
                                <span x-text="isSavingKondisi ? 'Menyimpan...' : 'Simpan Perubahan Kondisi'"></span>
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
