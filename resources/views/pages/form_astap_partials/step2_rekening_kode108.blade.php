            <!-- ========================================================================= -->
            <!-- LANGKAH 2: FILTERING BERTINGKAT REKENING BELANJA & JENIS ASTAP 108        -->
            <!-- ========================================================================= -->
            <div x-show="currentStep === 2" class="space-y-6">
                <div>
                    <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/30 text-xs font-bold mb-2">
                        <span>🗺️ FILTERING BERTINGKAT BELANJA MODAL</span>
                    </div>
                    <h2 class="text-lg font-bold text-white flex items-center space-x-2">
                        <span class="p-2 rounded-xl bg-blue-500/10 text-blue-400 text-sm">📊</span>
                        <span>Langkah 2: Memilih Rekening Belanja SIPD & Jenis ASTAP (PMDN 108)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-1">Pilih Rekening Belanja ➔ Jenis Aset PMDN 108 ➔ Sub Rincian Objek secara berjenjang:</p>
                </div>

                <!-- 3 Tingkat Filter Berjenjang Rekening Belanja & PMDN 108 -->
                <div class="p-6 rounded-3xl bg-slate-950/80 border border-blue-500/30 space-y-5 shadow-2xl">
                    
                    <!-- Tingkat 1: REKENING BELANJA PENGADAAN SIPD Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isRekeningOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/40 text-[10px] font-black flex items-center justify-center">1</span>
                                <span>Rekening Belanja Pengadaan SIPD</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Rekening (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.kode_rek && !isRekeningOpen" 
                                    @click="isRekeningOpen = true; searchRekening = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Rekening</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box dengan Icon Magnifying Glass -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isRekeningOpen && formData.kode_rek) ? (formData.kode_rek + ' - ' + formData.nama_belanja) : searchRekening"
                                   @input="searchRekening = $event.target.value; isRekeningOpen = true"
                                   @focus="isRekeningOpen = true"
                                   :placeholder="formData.kode_rek ? (formData.kode_rek + ' - ' + formData.nama_belanja) : 'Ketik untuk memfilter nama / kode rekening belanja (contoh: 5.2.02, Radiologi, Tanah, Gedung)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.kode_rek && !isRekeningOpen ? 'border-blue-500/60 text-blue-200' : 'border-blue-500/40 text-white focus:border-blue-400'">
                            <svg class="w-4 h-4 text-blue-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isRekeningOpen" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-blue-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="r in filteredRekeningBelanja.slice(0, 30)" :key="r.kode_rek">
                                <div @click="selectRekening(r)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="r.kode_rek === formData.kode_rek ? 'border-blue-500 bg-blue-950/40 shadow-lg' : 'border-slate-800 hover:border-blue-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-blue-300 transition-colors truncate" x-text="r.kode_rek + ' - ' + r.nama_belanja"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'REKENING BELANJA • ' + (r.kelompok || 'Kode Account SIPD')"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectRekening(r)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="r.kode_rek === formData.kode_rek ? 'bg-blue-500 text-slate-950 shadow-lg shadow-blue-500/30' : 'bg-blue-500/20 text-blue-300 border border-blue-500/40 hover:bg-blue-500 hover:text-slate-950'">
                                        <span x-text="r.kode_rek === formData.kode_rek ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                            <template x-if="isRekeningOpen && filteredRekeningBelanja.length > 30">
                                <p class="text-[10px] text-center text-slate-500 italic pt-1">...dan <span x-text="filteredRekeningBelanja.length - 30"></span> rekening lainnya. Ketik lebih spesifik untuk mempersempit.</p>
                            </template>
                            <template x-if="isRekeningOpen && filteredRekeningBelanja.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400 italic">
                                    Tidak ada rekening belanja yang cocok.
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 2: JENIS ASET PMDN 108 Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isJenis108Open = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center">2</span>
                                <span>Jenis Aset PMDN 108</span>
                            </label>
                            
                            <!-- Tombol Red ✕ Ganti Jenis Aset (Muncul bila sudah terpilih) -->
                            <button type="button" 
                                    x-show="formData.jenis_aset_kode && !isJenis108Open" 
                                    @click="isJenis108Open = true; searchJenis108 = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Jenis Aset</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isJenis108Open && formData.jenis_aset_kode) ? (formData.jenis_aset_kode + ' - ' + formData.jenis_aset_nama) : searchJenis108"
                                   @input="searchJenis108 = $event.target.value; isJenis108Open = true"
                                   @focus="isJenis108Open = true"
                                   :placeholder="formData.jenis_aset_kode ? (formData.jenis_aset_kode + ' - ' + formData.jenis_aset_nama) : 'Ketik untuk memfilter kode / nama jenis PMDN 108 (contoh: 1.3.1, TANAH, PERALATAN, GEDUNG)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.jenis_aset_kode && !isJenis108Open ? 'border-cyan-500/60 text-cyan-200' : 'border-cyan-500/40 text-white focus:border-cyan-400'">
                            <svg class="w-4 h-4 text-cyan-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isJenis108Open" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-cyan-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="j in filteredJenisAstap108" :key="j.kode">
                                <div @click="selectJenisAstap(j)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="j.kode === formData.jenis_aset_kode ? 'border-cyan-500 bg-cyan-950/40 shadow-lg' : 'border-slate-800 hover:border-cyan-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors truncate" x-text="j.kode + ' - ' + j.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'PMDN 108 • Kode Kelompok Permendagri 108'"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectJenisAstap(j)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="j.kode === formData.jenis_aset_kode ? 'bg-cyan-500 text-slate-950 shadow-lg shadow-cyan-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 hover:bg-cyan-500 hover:text-slate-950'">
                                        <span x-text="j.kode === formData.jenis_aset_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                            <template x-if="isJenis108Open && filteredJenisAstap108.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400 italic">
                                    Tidak ada jenis aset yang cocok.
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 3: SUB RINCIAN OBJEK PMDN 108 Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isSubRincian108Open = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[10px] font-black flex items-center justify-center">3</span>
                                <span>Sub Rincian Objek PMDN 108</span>
                                <span class="text-[10px] text-emerald-400 font-normal italic">(Boleh Dikosongkan)</span>
                            </label>
                            
                            <div class="flex items-center space-x-3">
                                <!-- Tombol Kosongkan Pilihan (Reset ke Kosong) -->
                                <button type="button" 
                                        x-show="formData.sub_rincian_kode" 
                                        @click="formData.sub_rincian_kode = ''; formData.sub_rincian_nama = ''; searchSubRincian108 = ''; isSubRincian108Open = false" 
                                        class="text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors flex items-center space-x-1 cursor-pointer">
                                    <span>🗑️ Kosongkan Pilihan</span>
                                </button>

                                <!-- Tombol Red ✕ Ganti Sub Rincian (Muncul bila sudah terpilih) -->
                                <button type="button" 
                                        x-show="formData.sub_rincian_kode && !isSubRincian108Open" 
                                        @click="isSubRincian108Open = true; searchSubRincian108 = ''" 
                                        class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                    <span>✕ Ganti Sub Rincian</span>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isSubRincian108Open && formData.sub_rincian_kode) ? (formData.sub_rincian_kode + ' - ' + formData.sub_rincian_nama) : searchSubRincian108"
                                   @input.debounce.200ms="searchSubRincian108 = $event.target.value; isSubRincian108Open = true"
                                   @focus="isSubRincian108Open = true"
                                   :placeholder="formData.sub_rincian_kode ? (formData.sub_rincian_kode + ' - ' + formData.sub_rincian_nama) : 'Ketik untuk memfilter sub rincian PMDN 108 (opsional, terisi otomatis saat memilih Nama Barang)...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="formData.sub_rincian_kode && !isSubRincian108Open ? 'border-emerald-500/60 text-emerald-200' : 'border-emerald-500/40 text-white focus:border-emerald-400'">
                            <svg class="w-4 h-4 text-emerald-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Catatan Edukatif untuk Pengguna -->
                        <p class="text-[10.5px] text-slate-400 italic">
                            💡 <strong class="text-emerald-300">Catatan:</strong> Bagian ini boleh dikosongkan jika Anda tidak hafal kode sub-rincian 108. Saat memilih <strong>Nama Barang</strong> pada nomor 4 di bawah, Sub Rincian 108 ini akan otomatis terisi.
                        </p>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isSubRincian108Open" x-transition x-cloak style="max-height: 220px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-emerald-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <!-- Opsi Kosongkan Pilihan di dalam Dropdown -->
                            <div @click="formData.sub_rincian_kode = ''; formData.sub_rincian_nama = ''; searchSubRincian108 = ''; isSubRincian108Open = false"
                                 class="p-2.5 rounded-xl bg-amber-950/40 hover:bg-amber-900/60 border border-amber-500/40 hover:border-amber-400 cursor-pointer transition-all flex items-center justify-between group">
                                <div class="min-w-0 pr-3 flex items-center space-x-2">
                                    <span class="text-xs">🗑️</span>
                                    <div>
                                        <h4 class="text-xs font-bold text-amber-300 group-hover:text-amber-200 transition-colors">Kosongkan Pilihan Sub Rincian Objek</h4>
                                        <p class="text-[10px] text-amber-400/80">Biarkan kosong, akan otomatis terisi saat memilih Nama Barang di Langkah 2 nomor 4</p>
                                    </div>
                                </div>
                                <span class="shrink-0 px-3 py-1 rounded-lg text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/40">Kosongkan →</span>
                            </div>

                            <template x-for="s in filteredSubRincian108.slice(0, 30)" :key="s.kode">
                                <div @click="selectSubRincian(s)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="s.kode === formData.sub_rincian_kode ? 'border-emerald-500 bg-emerald-950/40 shadow-lg' : 'border-slate-800 hover:border-emerald-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors truncate" x-text="s.kode + ' - ' + s.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'SUB RINCIAN OBJEK • ' + (s.keterangan || s.kelompok || 'Kode Sub Rincian PMDN 108')"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectSubRincian(s)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="s.kode === formData.sub_rincian_kode ? 'bg-emerald-500 text-slate-950 shadow-lg shadow-emerald-500/30' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-500 hover:text-slate-950'">
                                        <span x-text="s.kode === formData.sub_rincian_kode ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                            <template x-if="isSubRincian108Open && filteredSubRincian108.length > 30">
                                <p class="text-[10px] text-center text-slate-500 italic pt-1">...dan <span x-text="filteredSubRincian108.length - 30"></span> sub rincian lainnya. Ketik untuk mempersempit.</p>
                            </template>
                            <template x-if="isSubRincian108Open && filteredSubRincian108.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400 italic">
                                    Tidak ada sub rincian 108 yang cocok.
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Tingkat 4: IDENTITAS BARANG PMDN 108 Card Filter Model -->
                    <div class="space-y-2 relative" @click.away="isNamaBarang108Open = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                                <span class="w-5 h-5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[10px] font-black flex items-center justify-center">4</span>
                                <span>Identitas Barang PMDN 108</span>
                                <span class="text-[10px] text-purple-400 font-normal italic">(Nama & Kode Barang 108)</span>
                            </label>
                            
                            <button type="button" 
                                    x-show="activeKodeBarang && !isNamaBarang108Open" 
                                    @click="isNamaBarang108Open = true; searchNamaBarang108 = ''" 
                                    class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                                <span>✕ Ganti Barang</span>
                            </button>
                        </div>
                        
                        <!-- Input Search Box -->
                        <div class="relative">
                            <input type="text" 
                                   :value="(!isNamaBarang108Open && activeNamaBarang) ? (activeKodeBarang + ' - ' + activeNamaBarang) : searchNamaBarang108"
                                   @input.debounce.150ms="searchNamaBarang108 = $event.target.value; isNamaBarang108Open = true"
                                   @focus="isNamaBarang108Open = true"
                                   :placeholder="activeKodeBarang ? (activeKodeBarang + ' - ' + activeNamaBarang) : 'Ketik nama / kode barang untuk mencari...'" 
                                   class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                                   :class="activeKodeBarang && !isNamaBarang108Open ? 'border-purple-500/60 text-purple-200' : 'border-purple-500/40 text-white focus:border-purple-400'">
                            <svg class="w-4 h-4 text-purple-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>

                        <!-- Cards List (HANYA MUNCUL JIKA SEDANG DIFOKUSKAN / DIKETIK) -->
                        <div x-show="isNamaBarang108Open" x-transition x-cloak style="max-height: 220px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-purple-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                            <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                                <div @click="selectSubSubRincianItem(item)"
                                     class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                                     :class="item.kode === activeKodeBarang ? 'border-purple-500 bg-purple-950/40 shadow-lg' : 'border-slate-800 hover:border-purple-500/50'">
                                    <div class="min-w-0 pr-3">
                                        <h4 class="text-xs font-bold text-white group-hover:text-purple-300 transition-colors truncate" x-text="item.kode + ' - ' + item.nama"></h4>
                                        <p class="text-[10px] text-slate-400 truncate" x-text="'SUB-SUB RINCIAN 108 • Kode Barang PMDN 108'"></p>
                                    </div>
                                    <button type="button" 
                                            @click.stop="selectSubSubRincianItem(item)" 
                                            class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                            :class="item.kode === activeKodeBarang ? 'bg-purple-500 text-slate-950 shadow-lg shadow-purple-500/30' : 'bg-purple-500/20 text-purple-300 border border-purple-500/40 hover:bg-purple-500 hover:text-slate-950'">
                                        <span x-text="item.kode === activeKodeBarang ? '✓ Terpilih' : 'Pilih →'"></span>
                                    </button>
                                </div>
                            </template>
                            <template x-if="isNamaBarang108Open && filteredSubSubRincian108.length === 0">
                                <div class="p-3 text-center text-xs text-slate-400 italic">
                                    Tidak ada nama barang 108 yang cocok.
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- ===================================================================== -->
                    <!-- PILIHAN KATEGORI EKSTRAKOMTABEL (BERLAKU UNTUK SEMUA JENIS ASET)    -->
                    <!-- ===================================================================== -->
                    <div x-show="formData.jenis_aset_kode" x-transition class="space-y-2">
                        <div class="flex items-center space-x-2 mb-1">
                            <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-black flex items-center justify-center">5</span>
                            <span class="block text-slate-200 font-bold text-xs">Kategori Pencatatan Aset</span>
                            <span class="text-[10px] text-amber-400 font-normal italic">(Reguler vs Ekstrakomtabel)</span>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-amber-500/30 space-y-3 shadow-xl">
                            <!-- Info Aturan Harga -->
                            <div class="flex items-start space-x-2 p-2.5 rounded-xl bg-amber-950/30 border border-amber-500/20">
                                <span class="text-amber-400 text-sm shrink-0 mt-0.5">⚖️</span>
                                <p class="text-[10.5px] text-amber-300/90 leading-relaxed">
                                    <strong class="text-amber-300">Aturan Harga Satuan:</strong>
                                    Aset dengan harga satuan <strong class="text-emerald-300">&gt; Rp 300.000</strong> dicatat sebagai <strong>Aset Reguler/Kapitalisasi</strong>.
                                    Aset dengan harga satuan <strong class="text-cyan-300">≤ Rp 300.000</strong> dicatat sebagai <strong>Barang Ekstrakomtabel (Extracom)</strong>.
                                </p>
                            </div>

                            <!-- Dua Kartu Pilihan -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <!-- Opsi 1: Aset Reguler / Kapitalisasi -->
                                <div @click="formData.is_extracomtable = false"
                                     :class="!formData.is_extracomtable ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="global_extracom_choice" :checked="!formData.is_extracomtable" @change="formData.is_extracomtable = false" class="text-emerald-500 focus:ring-emerald-500">
                                            <span class="text-xs font-black text-emerald-300">⚙️ Aset Reguler / Kapitalisasi</span>
                                        </div>
                                        <span x-show="!formData.is_extracomtable" class="text-[9px] px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-300 leading-relaxed">
                                        Aset kapitalisasi standar dengan harga satuan <strong class="text-emerald-300">&gt; Rp 300.000 / unit</strong>. Dicatat dalam KIB sesuai jenis aset (KIB A, B, C, D, E, F, ATB).
                                    </p>
                                </div>

                                <!-- Opsi 2: Barang Ekstrakomtabel (Extracom) -->
                                <div @click="formData.is_extracomtable = true"
                                     :class="formData.is_extracomtable ? 'border-cyan-500 bg-cyan-950/40 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                                     class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <input type="radio" name="global_extracom_choice" :checked="formData.is_extracomtable" @change="formData.is_extracomtable = true" class="text-cyan-500 focus:ring-cyan-500">
                                            <span class="text-xs font-black text-cyan-300">📦 Barang Ekstrakomtabel (Extracom)</span>
                                        </div>
                                        <span x-show="formData.is_extracomtable" class="text-[9px] px-2 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                                    </div>
                                    <p class="text-[11px] text-slate-300 leading-relaxed">
                                        Barang non-kapitalisasi dengan harga satuan <strong class="text-cyan-300">≤ Rp 300.000 / unit</strong>. Dicatat di Sheet Ekstrakomtabel terpisah (tidak masuk KIB).
                                    </p>
                                </div>
                            </div>

                            <!-- Badge status terpilih -->
                            <div class="flex items-center justify-between pt-1 border-t border-slate-800/60">
                                <span class="text-[10px] text-slate-500">Status pencatatan aktif:</span>
                                <span class="text-[10px] font-bold px-3 py-1 rounded-full transition-all"
                                      :class="formData.is_extracomtable
                                          ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30'
                                          : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30'"
                                      x-text="formData.is_extracomtable ? '📦 Ekstrakomtabel — maks. Rp 300.000/unit' : '⚙️ Aset Reguler / Kapitalisasi'">
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Input Tahun Anggaran & Triwulan Pengadaan (SIPD) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- TAHUN ANGGARAN -->
                        <div>
                            <label class="block text-slate-300 font-semibold text-xs mb-1 flex items-center justify-between">
                                <span>📅 TAHUN ANGGARAN</span>
                                <span class="text-[10px] text-cyan-400 font-mono" x-text="'1900 - ' + maxYear"></span>
                            </label>
                            <div class="relative">
                                <input type="number" 
                                       min="1900" 
                                       :max="maxYear" 
                                       x-model.number="formData.tahun_anggaran" 
                                       @input="
                                           let val = String($event.target.value || '');
                                           if (val.length > 4) {
                                               val = val.slice(0, 4);
                                               $event.target.value = val;
                                           }
                                           formData.tahun_anggaran = val ? parseInt(val, 10) : '';
                                           formData.tahun_perolehan = formData.tahun_anggaran;
                                       "
                                       @change="validateTahunAnggaran(); fetchExistingAnggaran(); syncDatesWithTriwulan();"
                                       @blur="validateTahunAnggaran(); fetchExistingAnggaran(); syncDatesWithTriwulan();"
                                       placeholder="Contoh: 2026 atau 1994 (4 Digit)"
                                       class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none focus:border-cyan-500 placeholder:text-slate-500 placeholder:font-normal">
                            </div>
                        </div>

                        <!-- TRIWULAN -->
                        <div>
                            <label class="block text-cyan-400 font-semibold text-xs mb-1 flex items-center justify-between">
                                <span>📊 TRIWULAN PENGADAAN</span>
                                <span class="text-[10px] text-cyan-300/80 font-mono">TW I - IV</span>
                            </label>
                            <select x-model="formData.triwulan"
                                    @change="fetchExistingAnggaran(); syncDatesWithTriwulan();"
                                    class="w-full bg-slate-950 border border-cyan-500/50 rounded-xl px-3.5 py-2.5 text-xs font-bold focus:outline-none focus:border-cyan-400"
                                    :class="formData.triwulan ? 'text-cyan-300' : 'text-slate-500 font-normal'">
                                <option value="" disabled selected class="text-slate-500">-- Pilih Triwulan Pengadaan --</option>
                                <option value="TW I" class="text-cyan-300 bg-slate-900 font-bold">Triwulan I (TW I)</option>
                                <option value="TW II" class="text-cyan-300 bg-slate-900 font-bold">Triwulan II (TW II)</option>
                                <option value="TW III" class="text-cyan-300 bg-slate-900 font-bold">Triwulan III (TW III)</option>
                                <option value="TW IV" class="text-cyan-300 bg-slate-900 font-bold">Triwulan IV (TW IV)</option>
                            </select>
                        </div>
                    </div>

                    <!-- Input Nilai Anggaran & Realisasi (Kolom 14 & 15) -->
                    <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Kolom 14: JUMLAH ANGGARAN -->
                            <div>
                                <label class="block text-slate-300 font-semibold text-xs mb-1 flex items-center justify-between">
                                    <span>JUMLAH ANGGARAN (Rp) (Kolom 14) <span class="text-rose-500 font-bold">*</span></span>
                                    <span class="text-[10px] text-slate-400">Pagu Sub Rincian</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-slate-500 text-xs font-bold">Rp</span>
                                    <input type="text" 
                                        :value="formData.jumlah_anggaran ? Number(formData.jumlah_anggaran).toLocaleString('id-ID') : ''"
                                        @input="
                                            let raw = $event.target.value.replace(/\D/g, '');
                                            formData.jumlah_anggaran = raw ? parseInt(raw, 10) : '';
                                            $event.target.value = raw ? Number(raw).toLocaleString('id-ID') : '';
                                            isAnggaranAutoLoaded = false;"
                                        placeholder="1.000.000.000"
                                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 pl-10 text-xs text-white font-mono font-bold focus:outline-none focus:border-blue-500">
                                </div>
                            </div>
        
                            <!-- Kolom 15: JUMLAH REALISASI -->
                            <div>
                                <label class="block text-emerald-400 font-semibold text-xs mb-1 flex items-center justify-between">
                                    <span>JUMLAH REALISASI (Rp) (Kolom 15)</span>
                                    <span class="text-[10px] text-emerald-400 font-mono">⚡ Otomatis Akumulasi</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-3 text-emerald-500 text-xs font-bold">Rp</span>
                                    <input type="text" 
                                        :value="formData.jumlah_realisasi ? Number(formData.jumlah_realisasi).toLocaleString('id-ID') : '0'"
                                        readonly
                                        placeholder="Otomatis dari Langkah 3..."
                                        class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-3.5 py-2.5 pl-10 text-xs text-emerald-400 font-mono font-extrabold focus:outline-none cursor-not-allowed">
                                </div>
                                <div class="mt-1 flex flex-wrap items-center justify-between gap-1 text-[10px]">
                                    <span class="text-emerald-400/90 font-medium">⚡ Akumulasi Realisasi TW Ini</span>
                                    <span x-show="existingRealisasiDb > 0" class="text-amber-300 font-mono font-semibold"
                                          x-text="'(Rp ' + formatRupiah(existingRealisasiDb) + ' lama + Rp ' + formatRupiah(nilaiBarangSaatIni) + ' baru)'"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================================= -->
                <!-- LIVE PREVIEW TABEL PERSIS SEPERTI GAMBAR SCREENSHOT USER (LANGKAH 2)      -->
                <!-- ========================================================================= -->
                <div class="space-y-2 pt-2">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center space-x-1.5">
                            <span>📄 Live Preview Tabel Belanja Modal:</span>
                        </span>
                        <span class="text-[10px] text-emerald-400 font-mono">Format Excel Sesuai Standar Laporan</span>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-xl">
                        <table class="w-full min-w-[760px] text-center text-xs border-collapse font-sans">
                            <!-- Header Atas: BELANJA MODAL -->
                            <thead>
                                <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b border-slate-600 text-[11px]">
                                    <th colspan="8" class="py-2 border border-slate-600 tracking-wider">
                                        BELANJA MODAL
                                    </th>
                                </tr>
                                <tr class="bg-[#eaf1dd] text-slate-950 font-bold border-b-2 border-slate-700 text-[10px]">
                                    <th class="px-3 py-2 border border-slate-600">Kode Rekening</th>
                                    <th class="px-3 py-2 border border-slate-600">Nama Rekening Belanja</th>
                                    <th class="px-3 py-2 border border-slate-600">Kode 108</th>
                                    <th class="px-3 py-2 border border-slate-600">Nama Barang (Jenis 108)</th>
                                    <th class="px-3 py-2 border border-slate-600">Kode Sub Rincian</th>
                                    <th class="px-3 py-2 border border-slate-600">Sub Rincian Objek</th>
                                    <th class="px-3 py-2 border border-slate-600">Jumlah Anggaran (Rp)</th>
                                    <th class="px-3 py-2 border border-slate-600">Jumlah Realisasi (Rp)</th>
                                </tr>
                            </thead>
                            <!-- Baris Data Isi Live Sesuai Input User -->
                            <tbody class="bg-white text-slate-950 font-medium text-[11px]">
                                <tr>
                                    <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.kode_rek"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-left font-semibold" x-text="formData.nama_belanja"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.jenis_aset_kode"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-left font-semibold uppercase" x-text="formData.jenis_aset_nama"></td>
                                    <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.sub_rincian_kode"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-left font-semibold uppercase" x-text="formData.sub_rincian_nama"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-right font-mono font-bold" x-text="formatRupiah(formData.jumlah_anggaran)"></td>
                                    <td class="px-3 py-3 border border-slate-400 text-right font-mono font-bold text-emerald-800" x-text="formatRupiah(formData.jumlah_realisasi)"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
