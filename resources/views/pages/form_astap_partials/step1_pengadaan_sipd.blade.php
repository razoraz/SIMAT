            <!-- ========================================================================= -->
            <!-- LANGKAH 1: FILTERING BERTINGKAT JENIS PENGADAAN (PROVINSI ➔ KOTA ➔ KEC)   -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 1" class="space-y-6">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 text-xs font-bold mb-2">
                        <span>🗺️ FILTERING BERTINGKAT SIPD</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-purple-500/10 text-purple-400 text-sm">🏛️</span>
                        <span>Langkah 1: Memilih Jenis Pengadaan Berdasarkan Program & Kegiatan (SIPD)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Pilih Program ➔ Kegiatan ➔ Sub Kegiatan (Jenis Pengadaan Detail) secara berjenjang:</p>
                </div>

                <!-- 3 Tingkat Filter Berjenjang (Ibarat Provinsi ➔ Kota ➔ Kecamatan) -->
                <div class="p-6 rounded-3xl bg-slate-950/80 border border-purple-500/30 space-y-5 shadow-2xl">
                    
                    <!-- Tingkat 1: PROGRAM PENGADAAN (SIPD) Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isProgramOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[10px] font-black flex items-center justify-center">1</span>
                                <span>Program Pengadaan SIPD (<span x-text="filteredPrograms.length"></span> Program Terdaftar)</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Program (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.program_kode && !isProgramOpen" 
                                    @click="isProgramOpen = true; searchProgram = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Program</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box dengan Icon Magnifying Glass -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isProgramOpen && formData.program_kode) ? (formData.program_kode + ' - ' + formData.program_nama) : searchProgram"
                                   @input="searchProgram = $event.target.value; isProgramOpen = true"
                                   @focus="isProgramOpen = true"
                                   :placeholder="formData.program_kode ? (formData.program_kode + ' - ' + formData.program_nama) : 'Ketik untuk memfilter nama / kode program (contoh: Penunjang, BLUD, Pelayanan)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.program_kode && !isProgramOpen ? 'border-purple-500/60 text-purple-200' : 'border-purple-500/40 text-white focus:border-purple-400'">
                            <svg class="w-4 h-4 text-purple-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isProgramOpen" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-purple-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="p in filteredPrograms" :key="p.kode">
                                <div @click="selectProgram(p)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="p.kode === formData.program_kode ? 'border-purple-500 bg-purple-950/40 shadow-lg' : 'border-slate-800 hover:border-purple-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-purple-300 transition-colors truncate" x-text="p.kode + ' - ' + p.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'PROGRAM SIPD • ' + (p.kegiatans ? p.kegiatans.length : 0) + ' Kegiatan Terdaftar'"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectProgram(p)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="p.kode === formData.program_kode ? 'bg-purple-500 text-slate-950 shadow-lg shadow-purple-500/30' : 'bg-purple-500/20 text-purple-300 border border-purple-500/40 hover:bg-purple-500 hover:text-slate-950'">
                                        <span x-text="p.kode === formData.program_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 2: KEGIATAN PENGADAAN (SIPD) Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isKegiatanOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center">2</span>
                                <span>Kegiatan Pengadaan SIPD (<span x-text="filteredKegiatans.length"></span> Kegiatan Terdaftar)</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Kegiatan (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.kegiatan_kode && !isKegiatanOpen" 
                                    @click="isKegiatanOpen = true; searchKegiatan = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Kegiatan</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isKegiatanOpen && formData.kegiatan_kode) ? (formData.kegiatan_kode + ' - ' + formData.kegiatan_nama) : searchKegiatan"
                                   @input="searchKegiatan = $event.target.value; isKegiatanOpen = true"
                                   @focus="isKegiatanOpen = true"
                                   :placeholder="formData.kegiatan_kode ? (formData.kegiatan_kode + ' - ' + formData.kegiatan_nama) : 'Ketik untuk memfilter nama / kode kegiatan (contoh: Peningkatan BLUD, Sarana Prasarana)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.kegiatan_kode && !isKegiatanOpen ? 'border-cyan-500/60 text-cyan-200' : 'border-cyan-500/40 text-white focus:border-cyan-400'">
                            <svg class="w-4 h-4 text-cyan-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isKegiatanOpen" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-cyan-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="k in filteredKegiatans" :key="k.kode">
                                <div @click="selectKegiatan(k)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="k.kode === formData.kegiatan_kode ? 'border-cyan-500 bg-cyan-950/40 shadow-lg' : 'border-slate-800 hover:border-cyan-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors truncate" x-text="k.kode + ' - ' + k.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'KEGIATAN SIPD • Terfilter dari Program: ' + formData.program_nama"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectKegiatan(k)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="k.kode === formData.kegiatan_kode ? 'bg-cyan-500 text-slate-950 shadow-lg shadow-cyan-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 hover:bg-cyan-500 hover:text-slate-950'">
                                        <span x-text="k.kode === formData.kegiatan_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 3: SUB KEGIATAN / JENIS PENGADAAN Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isSubKegiatanOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-black flex items-center justify-center">3</span>
                                <span>Sub Kegiatan / Jenis Pengadaan Spesifik (<span x-text="filteredSubKegiatans.length"></span> Sub Kegiatan Terdaftar)</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Sub Kegiatan (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.sub_kegiatan_kode && !isSubKegiatanOpen" 
                                    @click="isSubKegiatanOpen = true; searchSubKegiatan = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Sub Kegiatan</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isSubKegiatanOpen && formData.sub_kegiatan_kode) ? (formData.sub_kegiatan_kode + ' - ' + formData.sub_kegiatan_nama) : searchSubKegiatan"
                                   @input="searchSubKegiatan = $event.target.value; isSubKegiatanOpen = true"
                                   @focus="isSubKegiatanOpen = true"
                                   :placeholder="formData.sub_kegiatan_kode ? (formData.sub_kegiatan_kode + ' - ' + formData.sub_kegiatan_nama) : 'Ketik untuk memfilter nama / kode sub kegiatan (contoh: Pelayanan BLUD, Alat Medis)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.sub_kegiatan_kode && !isSubKegiatanOpen ? 'border-emerald-500/60 text-emerald-200' : 'border-emerald-500/40 text-white focus:border-emerald-400'">
                            <svg class="w-4 h-4 text-emerald-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isSubKegiatanOpen" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-emerald-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="s in filteredSubKegiatans" :key="s.kode">
                                <div @click="selectSubKegiatan(s)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="s.kode === formData.sub_kegiatan_kode ? 'border-emerald-500 bg-emerald-950/40 shadow-lg' : 'border-slate-800 hover:border-emerald-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors truncate" x-text="s.kode + ' - ' + s.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'SUB KEGIATAN • ' + s.keterangan"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectSubKegiatan(s)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="s.kode === formData.sub_kegiatan_kode ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500 hover:text-slate-950'">
                                        <span x-text="s.kode === formData.sub_kegiatan_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Kotak Info Jalur Hierarki Aktif -->
                    <div class="p-4 rounded-2xl bg-purple-950/20 border border-purple-500/30 text-xs space-y-2">
                        <div class="flex items-center space-x-2 text-purple-300 font-bold text-[11px] uppercase tracking-wider">
                            <span>✅ Jalur Pengadaan Terpilih:</span>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-[11px]">
                            <span class="px-2.5 py-1 rounded-xl bg-purple-500/20 text-purple-300 border border-purple-500/30 font-semibold" x-text="'Prog: ' + formData.program_nama"></span>
                            <span class="text-slate-500">&rarr;</span>
                            <span class="px-2.5 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-semibold" x-text="'Keg: ' + formData.kegiatan_nama"></span>
                            <span class="text-slate-500">&rarr;</span>
                            <span class="px-2.5 py-1 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold" x-text="'Sub Keg: ' + formData.sub_kegiatan_nama"></span>
                        </div>
                        <p class="text-[10px] text-slate-400 italic pt-1" x-text="formData.keterangan_pengadaan"></p>
                    </div>

                </div>
            </div>
