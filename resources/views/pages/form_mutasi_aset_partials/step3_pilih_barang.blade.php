            {{-- ========================================================================= --}}
            {{-- ===== STEP 3: PILIH BARANG ASET MULTI-SELECT & ALASAN ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 3" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">3</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 3: Pilih Barang Aset (Bisa Pilih Banyak Sekaligus) & Alasan</h2>
                            <p class="text-xs text-slate-400">Centang satu atau beberapa barang aset register dari unit pengirim yang akan dimutasi.</p>
                        </div>
                    </div>
                </div>

                {{-- Banner Filter Unit Pengirim Aktif --}}
                <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 flex items-center justify-between gap-3 flex-wrap">
                    <div class="flex items-center space-x-2.5">
                        <span class="text-lg">📍</span>
                        <div>
                            <p class="text-xs font-extrabold text-rose-300 uppercase tracking-wider" x-text="assetBannerLabel"></p>
                            <p class="text-sm font-black text-white font-mono" x-text="ruangan_asal || 'Belum Dipilih'"></p>
                        </div>
                    </div>
                    <span class="text-xs font-mono font-bold px-3 py-1 rounded-xl bg-slate-900 text-rose-300 border border-rose-500/30"
                        x-text="availableRegistersForAsal.length + ' Barang Tersedia'"></span>
                </div>

                {{-- Banner Keterangan Jika Ada Aset Terkunci Mutasi Lain --}}
                <div x-show="lockedCountForAsal > 0" x-cloak class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between gap-2 text-xs text-amber-200">
                    <div class="flex items-center space-x-2">
                        <span class="text-base">🔒</span>
                        <span>
                            Ada <strong class="text-amber-300 font-mono font-bold" x-text="lockedCountForAsal"></strong> aset dari unit ini yang disembunyikan karena sedang dalam proses persetujuan mutasi lain.
                        </span>
                    </div>
                    <span class="px-2 py-0.5 rounded-md bg-amber-500/20 text-amber-300 text-[10px] font-bold shrink-0 uppercase tracking-wider">Terkunci Sementara</span>
                </div>

                {{-- Filter & Batch Action Buttons --}}
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                    <div class="relative flex-1 flex items-center space-x-2">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 flex items-center pointer-events-none text-rose-400" style="left: 1.25rem;">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>
                            <input type="text" x-model="searchBarang"
                                placeholder="Ketik NIBAR / nama barang..."
                                class="w-full bg-slate-950 border border-slate-800 rounded-xl py-2.5 pr-4 text-xs text-white font-mono placeholder-slate-500 focus:outline-none focus:border-rose-500 transition-all"
                                style="padding-left: 3.1rem !important;">
                        </div>

                        {{-- Dropdown Filter Kondisi --}}
                        <select x-model="filterKondisi"
                            class="bg-slate-950 border border-slate-800 rounded-xl px-3 py-2.5 text-xs font-bold text-slate-300 focus:outline-none focus:border-rose-500 transition-all cursor-pointer shrink-0">
                            <option value="">Semua Kondisi</option>
                            <option value="Baik" class="bg-slate-900 text-emerald-400">🟢 Baik</option>
                            <option value="Kurang Baik" class="bg-slate-900 text-amber-400">🟡 Kurang Baik</option>
                            <option value="Rusak Berat" class="bg-slate-900 text-rose-400">🔴 Rusak Berat</option>
                        </select>
                    </div>

                    <div class="flex items-center space-x-2 shrink-0">
                        <button type="button" @click="selectAllRegisters()"
                            class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-rose-300 text-xs font-bold border border-slate-700 transition-all cursor-pointer flex items-center space-x-1.5">
                            <span>✓ Pilih Semua (<span x-text="availableRegistersForAsal.length"></span>)</span>
                        </button>
                        <button type="button" x-show="selectedRegisterIds.length > 0" @click="clearAllSelectedRegisters()"
                            class="px-3.5 py-2 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 text-xs font-bold border border-rose-500/20 transition-all cursor-pointer">
                            ✕ Hapus Pilihan
                        </button>
                    </div>
                </div>

                {{-- Counter Badge Multi-Select --}}
                <div class="flex items-center justify-between px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800">
                    <span class="text-xs text-slate-400 font-semibold">Total Aset Terpilih untuk Dimutasi:</span>
                    <span class="text-xs font-mono font-black px-3 py-1 rounded-lg"
                        :class="selectedRegisterIds.length > 0 ? 'bg-rose-500 text-slate-950 shadow-md' : 'bg-slate-800 text-slate-500'"
                        x-text="selectedRegisterIds.length + ' Barang Terpilih'"></span>
                </div>

                {{-- Hidden Inputs kondisi_baru untuk setiap barang terpilih --}}
                <template x-for="id in selectedRegisterIds" :key="id">
                    <input type="hidden" :name="'kondisi_baru[' + id + ']'" :value="editedKondisi[id] || (registers.find(r => Number(r.id) === Number(id))?.kondisi || 'Baik')">
                </template>

                {{-- Daftar Aset (Cards Grid dengan Checkbox Multi Select) --}}
                <div class="space-y-2.5 custom-scrollbar pr-2 p-1 rounded-2xl bg-slate-950/40 border border-slate-800/80" style="max-height: 280px !important; overflow-y: auto !important;">
                    <template x-for="r in filteredRegisters" :key="r.id">
                        <div @click="toggleSelectRegister(r)"
                            class="p-4 rounded-2xl border transition-all flex items-center justify-between gap-4 cursor-pointer group active:scale-[0.99]"
                            :class="isRegisterSelected(r.id) ? 'border-rose-500 bg-rose-950/25 ring-2 ring-rose-500/30 shadow-lg' : 'border-slate-800 bg-slate-950/80 hover:border-slate-700 hover:bg-slate-900'">
                            
                            <div class="flex items-center space-x-3.5 min-w-0">
                                {{-- Checkbox Icon --}}
                                <div class="w-6 h-6 rounded-lg flex items-center justify-center transition-all shrink-0 font-black text-xs"
                                     :class="isRegisterSelected(r.id) ? 'bg-rose-500 text-slate-950 shadow-md' : 'bg-slate-900 border border-slate-700 text-transparent group-hover:border-rose-400'">
                                    ✓
                                </div>
                                <div class="min-w-0">
                                    <p class="text-xs font-extrabold text-white group-hover:text-rose-300 transition-colors truncate" x-text="r.nama_barang"></p>
                                    <div class="flex items-center gap-2.5 mt-1.5 flex-wrap text-[10.5px]">
                                        <span class="text-rose-400 font-mono font-bold" x-text="r.nibar"></span>
                                        <span class="text-slate-600">•</span>
                                        <span class="text-slate-400" x-text="'Unit: ' + r.unit_nama"></span>
                                        <span class="text-slate-600">•</span>
                                        
                                        {{-- Selector Kondisi Langsung (Jika canChangeKondisi aktif) --}}
                                        <template x-if="canChangeKondisi">
                                            <div class="flex items-center space-x-1.5" @click.stop>
                                                <span class="text-slate-400 font-semibold">Kondisi:</span>
                                                <select x-model="editedKondisi[r.id]" @click.stop
                                                    class="bg-slate-900 border rounded-lg px-2 py-0.5 text-[10.5px] font-extrabold focus:outline-none cursor-pointer text-center [text-align-last:center]"
                                                    :class="{
                                                        'border-emerald-500/60 text-emerald-400 bg-emerald-950/40': (editedKondisi[r.id] || r.kondisi) === 'Baik',
                                                        'border-amber-500/60 text-amber-400 bg-amber-950/40': (editedKondisi[r.id] || r.kondisi) === 'Kurang Baik',
                                                        'border-rose-500/60 text-rose-400 bg-rose-950/40': (editedKondisi[r.id] || r.kondisi) === 'Rusak Berat'
                                                    }">
                                                    <option value="Baik" class="bg-slate-900 text-emerald-400 font-bold text-center">Baik</option>
                                                    <option value="Kurang Baik" class="bg-slate-900 text-amber-400 font-bold text-center">Kurang Baik</option>
                                                    <option value="Rusak Berat" class="bg-slate-900 text-rose-400 font-bold text-center">Rusak Berat</option>
                                                </select>
                                            </div>
                                        </template>

                                        {{-- Statis jika canChangeKondisi false --}}
                                        <template x-if="!canChangeKondisi">
                                            <span class="font-bold"
                                                :class="{
                                                    'text-emerald-400': r.kondisi === 'Baik',
                                                    'text-amber-400':   r.kondisi === 'Kurang Baik',
                                                    'text-rose-400':    r.kondisi === 'Rusak Berat'
                                                }"
                                                x-text="'Kondisi: ' + r.kondisi"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <span class="text-xs font-bold px-3 py-1.5 rounded-xl shrink-0 transition-all"
                                :class="isRegisterSelected(r.id) ? 'bg-rose-500 text-slate-950 font-black' : 'bg-slate-900 text-slate-400 border border-slate-800 group-hover:text-white'">
                                <span x-text="isRegisterSelected(r.id) ? '✓ Terpilih' : '+ Pilih'"></span>
                            </span>
                        </div>
                    </template>

                    <template x-if="filteredRegisters.length === 0">
                        <div class="p-8 text-center bg-slate-950/60 rounded-2xl border border-slate-800 space-y-2">
                            <span class="text-2xl">📦</span>
                            <p class="text-xs text-rose-400 font-bold">Tidak ada barang aset register yang tersedia pada unit pengirim ini.</p>
                            <p class="text-[11px] text-slate-400">Pastikan Anda memilih unit pengirim yang memiliki register aset di Langkah 2.</p>
                        </div>
                    </template>
                </div>

                {{-- Alasan Mutasi --}}
                <div class="space-y-2 pt-2">
                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1.5 flex items-center justify-between">
                        <span>Alasan / Urgensi Pengajuan Mutasi (<span class="text-white font-bold" x-text="jenis_mutasi"></span>) <span class="text-rose-400 font-bold">*</span></span>
                        <span x-show="!alasan_mutasi || !alasan_mutasi.trim()" class="text-[10px] text-amber-400 font-bold">⚠️ Wajib diisi</span>
                    </label>
                    <textarea name="alasan_mutasi" x-model="alasan_mutasi" rows="3" :placeholder="alasanPlaceholder" required
                        :class="(!alasan_mutasi || !alasan_mutasi.trim()) ? 'border-amber-500/50 focus:border-rose-500' : 'border-slate-800 focus:border-rose-500'"
                        class="w-full bg-slate-950 border rounded-xl px-4 py-3 text-xs text-white resize-none focus:outline-none transition-all placeholder:text-slate-600"></textarea>
                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <button type="button" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        ← Kembali ke Langkah 2
                    </button>
                    <button type="button" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-extrabold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 4: Preview & Konfirmasi →</span>
                    </button>
                </div>
            </div>
