            {{-- ========================================================================= --}}
            {{-- ===== STEP 2: PILIH UNIT PENGIRIM & PENERIMA ===== --}}
            {{-- ========================================================================= --}}
            <div x-show="step === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-800">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-300 border border-rose-500/30 flex items-center justify-center text-xs font-extrabold">2</div>
                        <div>
                            <h2 class="text-base font-extrabold text-white">Langkah 2: Tentukan Unit Pengirim (Asal) & Unit Penerima (Tujuan)</h2>
                            <p class="text-xs text-slate-400">Tentukan lokasi unit pengirim barang dan unit penerima tujuan mutasi aset.</p>
                        </div>
                    </div>
                </div>

                {{-- Hidden input tanggal_mutasi (otomatis hari ini) --}}
                <input type="hidden" name="tanggal_mutasi" value="{{ old('tanggal_mutasi', isset($mutasi) ? $mutasi->tanggal_mutasi->format('Y-m-d') : date('Y-m-d')) }}">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- Ruangan Asal (Pengirim / Pemilik Aset) --}}
                    <div class="space-y-4 p-5 rounded-2xl bg-slate-950/60 border border-slate-800 relative">
                        <div class="border-b border-slate-800 pb-2 flex items-center justify-between">
                            <label class="block text-slate-300 text-xs font-bold uppercase tracking-wider" x-text="labelAsal"></label>
                            <template x-if="isSubAdmin && userUnitNama && jenis_mutasi !== 'Minta Mutasi'">
                                <span class="text-[9.5px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Terunci Role Sub Admin</span>
                            </template>
                        </div>

                        {{-- Untuk Role Sub Admin pada mode Ajukan/Perbaikan/Pengembalian (Unit Asal Terkunci) --}}
                        <template x-if="isSubAdmin && userUnitNama && jenis_mutasi !== 'Minta Mutasi'">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-[10px] text-slate-400 block mb-1">Nama Unit/Ruangan:</span>
                                    <div class="px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white font-extrabold text-xs flex items-center justify-between">
                                        <span x-text="ruangan_asal || userUnitNama"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="ruangan_asal" :value="ruangan_asal || userUnitNama">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1">Penanggung Jawab Pengirim (Kepala Ruangan)</label>
                                    <div class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-emerald-300 font-extrabold text-xs flex items-center justify-between">
                                        <span x-text="penanggung_jawab_asal || userUnitKepala || 'Kepala Ruangan'"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="penanggung_jawab_asal" :value="penanggung_jawab_asal || userUnitKepala">
                                </div>
                            </div>
                        </template>

                        {{-- Pencarian Filter Unit Pengirim (Master Admin / Admin OR Sub Admin di Minta Mutasi) --}}
                        <template x-if="!isSubAdmin || !userUnitNama || jenis_mutasi === 'Minta Mutasi'">
                            <div class="space-y-3">
                                <div class="space-y-1.5 relative" @click.outside="isUnitAsalOpen = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider">
                                            <span x-text="jenis_mutasi === 'Minta Mutasi' ? 'Pilih Unit Pemilik Aset (Asal)' : 'Pilih Unit Pengirim (Asal)'"></span> <span class="text-rose-400">*</span>
                                        </label>
                                        <button type="button" x-show="ruangan_asal && !isUnitAsalOpen"
                                            @click="isUnitAsalOpen = true; searchUnitAsal = ''"
                                            class="text-[11px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Unit
                                        </button>
                                    </div>
                                    
                                    <div class="relative">
                                        <input type="text"
                                            :value="(!isUnitAsalOpen && ruangan_asal) ? ruangan_asal : searchUnitAsal"
                                            @input="searchUnitAsal = $event.target.value; isUnitAsalOpen = true"
                                            @focus="isUnitAsalOpen = true"
                                            :placeholder="jenis_mutasi === 'Minta Mutasi' ? 'Ketik unit pemilik aset yang diminta...' : 'Ketik nama unit pengirim...'"
                                            class="w-full bg-slate-900 border rounded-xl py-3 pr-4 text-xs font-bold transition-all shadow-inner focus:outline-none"
                                            :class="ruangan_asal && !isUnitAsalOpen ? 'border-rose-500/60 text-rose-200' : 'border-slate-700 text-white focus:border-rose-400'"
                                            style="padding-left: 3.1rem !important;">
                                        <svg class="w-4 h-4 text-rose-400 absolute pointer-events-none" style="left: 1.25rem; top: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <input type="hidden" name="ruangan_asal" :value="ruangan_asal">
                                    <template x-if="!ruangan_asal">
                                        <p class="text-[10.5px] text-amber-400 font-semibold flex items-center space-x-1 mt-1">
                                            <span>⚠️ Unit pengirim wajib dipilih</span>
                                        </p>
                                    </template>

                                    {{-- Dropdown Scrollable List (Excludes Unit Tujuan) --}}
                                    <div x-show="isUnitAsalOpen" x-transition x-cloak style="max-height: 210px !important; overflow-y: auto !important;"
                                        class="absolute z-50 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-rose-500/40 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="u in filteredUnitsAsal" :key="u.id">
                                            <div @click="selectUnitAsal(u)"
                                                class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between cursor-pointer group hover:bg-slate-800"
                                                :class="u.nama === ruangan_asal ? 'border-rose-500 bg-rose-950/40 shadow-lg' : 'border-slate-800/80 hover:border-rose-500/40'">
                                                <div class="min-w-0 pr-3">
                                                    <p class="text-xs font-bold text-white group-hover:text-rose-300 transition-colors truncate" x-text="u.nama"></p>
                                                    <p class="text-[10px] text-slate-400 truncate" x-text="'PJ / Kepala: ' + (u.kepala || 'Belum Diatur')"></p>
                                                </div>
                                                <span class="shrink-0 text-[11px] font-bold px-2.5 py-1 rounded-lg"
                                                    :class="u.nama === ruangan_asal ? 'bg-rose-500 text-slate-950' : 'bg-slate-800 text-slate-400 group-hover:text-white'">
                                                    <span x-text="u.nama === ruangan_asal ? '✓ Terpilih' : 'Pilih'"></span>
                                                </span>
                                            </div>
                                        </template>
                                        <template x-if="filteredUnitsAsal.length === 0">
                                            <div class="p-3 text-center text-xs text-rose-400 font-semibold">Tidak menemukan unit dengan kata kunci tersebut.</div>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1">Penanggung Jawab Pengirim (Kepala Ruangan)</label>
                                    <div class="px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-emerald-300 font-extrabold text-xs flex items-center justify-between shadow-inner">
                                        <span x-text="penanggung_jawab_asal || 'Pilih Unit Pengirim Terlebih Dahulu'"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="penanggung_jawab_asal" :value="penanggung_jawab_asal">
                                    <template x-if="penanggung_jawab_asal">
                                        <p class="text-[10px] text-emerald-400 font-semibold flex items-center space-x-1 mt-1">
                                            <span>✓ Terkunci otomatis dari data Kepala Ruangan RSUD</span>
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>

                    {{-- Ruangan Tujuan (Penerima / Pemohon) --}}
                    <div class="space-y-4 p-5 rounded-2xl bg-slate-950/60 border border-rose-500/30">
                        <div class="border-b border-rose-500/30 pb-2 flex items-center justify-between">
                            <label class="block text-rose-300 text-xs font-bold uppercase tracking-wider" x-text="labelTujuan"></label>
                            <template x-if="(isSubAdmin && userUnitNama && jenis_mutasi === 'Minta Mutasi') || (jenis_mutasi === 'Pengembalian' && subJenisPengembalian === 'gudang')">
                                <span class="text-[9.5px] px-2 py-0.5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold">Terunci Otomatis</span>
                            </template>
                        </div>

                        {{-- Mode Terkunci 1: Role Sub Admin pada Minta Mutasi --}}
                        <template x-if="isSubAdmin && userUnitNama && jenis_mutasi === 'Minta Mutasi'">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-[10px] text-slate-400 block mb-1">Nama Unit Pemohon (Penerima Aset):</span>
                                    <div class="px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white font-extrabold text-xs flex items-center justify-between">
                                        <span x-text="ruangan_tujuan || userUnitNama"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="ruangan_tujuan" :value="ruangan_tujuan || userUnitNama">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1">Penanggung Jawab Penerima (Kepala Ruangan)</label>
                                    <div class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-emerald-300 font-extrabold text-xs flex items-center justify-between">
                                        <span x-text="penanggung_jawab_tujuan || userUnitKepala || 'Kepala Ruangan'"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="penanggung_jawab_tujuan" :value="penanggung_jawab_tujuan || userUnitKepala">
                                </div>
                            </div>
                        </template>

                        {{-- Mode Terkunci 2: Pengembalian ke Gudang / Pengurus Barang --}}
                        <template x-if="jenis_mutasi === 'Pengembalian' && subJenisPengembalian === 'gudang'">
                            <div class="space-y-3">
                                <div>
                                    <span class="text-[10px] text-slate-400 block mb-1">Nama Unit Penerima (Gudang Utama):</span>
                                    <div class="px-4 py-3 rounded-xl bg-slate-900 border border-slate-700 text-white font-extrabold text-xs flex items-center justify-between">
                                        <span x-text="ruangan_tujuan || 'Instalasi Perbekalan & Rumah Tangga'"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="ruangan_tujuan" :value="ruangan_tujuan">
                                </div>
                                <div>
                                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1">Penanggung Jawab Penerima (Pengurus Barang)</label>
                                    <div class="px-4 py-2.5 rounded-xl bg-slate-900 border border-slate-800 text-emerald-300 font-extrabold text-xs flex items-center justify-between">
                                        <span x-text="penanggung_jawab_tujuan || 'Pengurus Barang Aset'"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="penanggung_jawab_tujuan" :value="penanggung_jawab_tujuan">
                                </div>
                                <p class="text-[10px] text-emerald-400 font-semibold flex items-center space-x-1 mt-1">
                                    <span>✓ Terkunci otomatis untuk Pengembalian ke Gudang Utama / Pengurus Barang</span>
                                </p>
                            </div>
                        </template>

                        {{-- Pencarian Filter Unit Penerima (Untuk selain mode terkunci) --}}
                        <template x-if="!(isSubAdmin && userUnitNama && jenis_mutasi === 'Minta Mutasi') && !(jenis_mutasi === 'Pengembalian' && subJenisPengembalian === 'gudang')">
                            <div class="space-y-3">
                                <div class="space-y-1.5 relative" @click.outside="isUnitTujuanOpen = false">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider">
                                            Pilih Unit Penerima (Tujuan) <span class="text-rose-400">*</span>
                                        </label>
                                        <button type="button" x-show="ruangan_tujuan && !isUnitTujuanOpen"
                                            @click="isUnitTujuanOpen = true; searchUnitTujuan = ''"
                                            class="text-[11px] font-bold text-rose-400 hover:text-rose-300 transition-colors cursor-pointer">
                                            ✕ Ganti Unit
                                        </button>
                                    </div>
                                    
                                    <div class="relative">
                                        <input type="text"
                                            :value="(!isUnitTujuanOpen && ruangan_tujuan) ? ruangan_tujuan : searchUnitTujuan"
                                            @input="searchUnitTujuan = $event.target.value; isUnitTujuanOpen = true"
                                            @focus="isUnitTujuanOpen = true"
                                            placeholder="Ketik nama unit penerima..."
                                            class="w-full bg-slate-900 border rounded-xl py-3 pr-4 text-xs font-bold transition-all shadow-inner focus:outline-none"
                                            :class="ruangan_tujuan && !isUnitTujuanOpen ? 'border-rose-500/60 text-rose-200' : 'border-rose-500/40 text-white focus:border-rose-400'"
                                            style="padding-left: 3.1rem !important;">
                                        <svg class="w-4 h-4 text-rose-400 absolute pointer-events-none" style="left: 1.25rem; top: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <input type="hidden" name="ruangan_tujuan" :value="ruangan_tujuan">
                                    <template x-if="!ruangan_tujuan">
                                        <p class="text-[10.5px] text-amber-400 font-semibold flex items-center space-x-1 mt-1">
                                            <span>⚠️ Unit penerima wajib dipilih</span>
                                        </p>
                                    </template>
                                    <template x-if="ruangan_asal && ruangan_tujuan && ruangan_asal === ruangan_tujuan">
                                        <p class="text-[10.5px] text-rose-400 font-bold flex items-center space-x-1 mt-1">
                                            <span>❌ Unit penerima tidak boleh sama dengan unit pengirim</span>
                                        </p>
                                    </template>

                                    {{-- Dropdown Scrollable List (Excludes Unit Pengirim) --}}
                                    <div x-show="isUnitTujuanOpen" x-transition x-cloak style="max-height: 210px !important; overflow-y: auto !important;"
                                        class="absolute z-50 mt-1 w-full space-y-1 custom-scrollbar p-2 bg-slate-900 border border-rose-500/40 rounded-2xl shadow-2xl backdrop-blur-xl">
                                        <template x-for="u in filteredUnitsTujuan" :key="u.id">
                                            <div @click="selectUnitTujuan(u)"
                                                class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between cursor-pointer group hover:bg-slate-800"
                                                :class="u.nama === ruangan_tujuan ? 'border-rose-500 bg-rose-950/40 shadow-lg' : 'border-slate-800/80 hover:border-rose-500/40'">
                                                <div class="min-w-0 pr-3">
                                                    <p class="text-xs font-bold text-white group-hover:text-rose-300 transition-colors truncate" x-text="u.nama"></p>
                                                    <p class="text-[10px] text-slate-400 truncate" x-text="'PJ / Kepala: ' + (u.kepala || 'Belum Diatur')"></p>
                                                </div>
                                                <span class="shrink-0 text-[11px] font-bold px-2.5 py-1 rounded-lg"
                                                    :class="u.nama === ruangan_tujuan ? 'bg-rose-500 text-slate-950' : 'bg-slate-800 text-slate-400 group-hover:text-white'">
                                                    <span x-text="u.nama === ruangan_tujuan ? '✓ Terpilih' : 'Pilih'"></span>
                                                </span>
                                            </div>
                                        </template>
                                        <template x-if="filteredUnitsTujuan.length === 0">
                                            <div class="p-3 text-center text-xs text-rose-400 font-semibold">Tidak menemukan unit tujuan dengan kata kunci tersebut.</div>
                                        </template>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-slate-400 text-[10.5px] font-semibold uppercase tracking-wider mb-1">Penanggung Jawab Penerima (Kepala Ruangan)</label>
                                    <div class="px-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 text-emerald-300 font-extrabold text-xs flex items-center justify-between shadow-inner">
                                        <span x-text="penanggung_jawab_tujuan || 'Pilih Unit Penerima Terlebih Dahulu'"></span>
                                        <span>🔒</span>
                                    </div>
                                    <input type="hidden" name="penanggung_jawab_tujuan" :value="penanggung_jawab_tujuan">
                                    <template x-if="penanggung_jawab_tujuan">
                                        <p class="text-[10px] text-emerald-400 font-semibold flex items-center space-x-1 mt-1">
                                            <span>✓ Terkunci otomatis dari data Kepala Ruangan RSUD</span>
                                        </p>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Visualisasi Alur Perpindahan --}}
                <div class="p-4 rounded-2xl bg-slate-950 border border-slate-800 flex items-center justify-between gap-3 flex-wrap">
                    <span class="text-xs text-slate-400 font-semibold">Skema Alur Pemindahan:</span>
                    <div class="flex items-center gap-2">
                        <span class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-bold border border-slate-700" x-text="ruangan_asal || 'Ruangan Asal'"></span>
                        <span class="text-rose-400 font-bold">➔</span>
                        <span class="px-3.5 py-1.5 rounded-xl bg-rose-500/20 text-rose-300 text-xs font-bold border border-rose-500/30" x-text="ruangan_tujuan || 'Ruangan Tujuan'"></span>
                    </div>
                </div>

                {{-- Action Navigation Buttons --}}
                <div class="flex items-center justify-between pt-4 border-t border-slate-800">
                    <button type="button" @click="prevStep()"
                        class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all active:scale-95 cursor-pointer">
                        ← Kembali ke Langkah 1
                    </button>
                    <button type="button" @click="nextStep()"
                        class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-400 text-white font-extrabold text-xs shadow-lg shadow-rose-500/25 transition-all flex items-center space-x-2 active:scale-95 cursor-pointer">
                        <span>Lanjut ke Langkah 3: Pilih Barang Aset →</span>
                    </button>
                </div>
            </div>
