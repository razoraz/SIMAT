<!-- ========================================================================= -->
<!-- LANGKAH 2: FILTERING BERTINGKAT REKENING & JENIS ASTAP 108 (HIBAH)        -->
<!-- ========================================================================= -->
<div x-show="currentStep === 2" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 text-xs font-bold mb-2">
            <span>🗺️ FILTERING BERTINGKAT KODE BARANG 108</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-cyan-500/10 text-cyan-400 text-sm">📊</span>
            <span>Langkah 2: Memilih Klasifikasi Rekening &amp; Jenis ASTAP (Permendagri 108)</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">Pilih Rekening Terkait ➔ Jenis Aset PMDN 108 ➔ Sub Rincian Objek ➔ Identitas Spesifik Barang secara berjenjang:</p>
    </div>

    <!-- 4 Tingkat Filter Berjenjang Rekening & PMDN 108 -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-cyan-500/30 space-y-5 shadow-2xl">
        
        <!-- Tingkat 1: REKENING PENGELOMPOKAN Card Filter Model -->
        <div class="space-y-2 relative" @click.away="isRekeningOpen = false">
            <div class="flex items-center justify-between">
                <label class="block text-slate-200 font-bold text-xs flex items-center space-x-2">
                    <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-black flex items-center justify-center">1</span>
                    <span>Rekening Belanja / Akun Pembukuan Terkait</span>
                    <span class="text-[10px] text-slate-400 font-normal italic">(Opsional untuk Hibah)</span>
                </label>
                
                <div class="flex items-center space-x-3">
                    <button type="button" 
                            x-show="formData.kode_rek" 
                            @click="formData.kode_rek = ''; formData.nama_belanja = ''; searchRekening = ''; isRekeningOpen = false" 
                            class="text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors flex items-center space-x-1 cursor-pointer">
                        <span>🗑️ Kosongkan Pilihan</span>
                    </button>
                    <button type="button" 
                            x-show="formData.kode_rek && !isRekeningOpen" 
                            @click="isRekeningOpen = true; searchRekening = ''" 
                            class="text-xs font-bold text-rose-500 hover:text-rose-400 transition-colors flex items-center space-x-1 cursor-pointer">
                        <span>✕ Ganti Rekening</span>
                    </button>
                </div>
            </div>
            
            <!-- Input Search Box -->
            <div class="relative">
                <input type="text" 
                       :value="(!isRekeningOpen && formData.kode_rek) ? (formData.kode_rek + ' - ' + formData.nama_belanja) : searchRekening"
                       @input="searchRekening = $event.target.value; isRekeningOpen = true"
                       @focus="isRekeningOpen = true"
                       :placeholder="formData.kode_rek ? (formData.kode_rek + ' - ' + formData.nama_belanja) : 'Ketik untuk memfilter nama / kode rekening belanja (contoh: 5.2.02, Radiologi, Tanah, Gedung)...'" 
                       class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                       :class="formData.kode_rek && !isRekeningOpen ? 'border-amber-500/60 text-amber-200' : 'border-amber-500/40 text-white focus:border-amber-400'">
                <svg class="w-4 h-4 text-amber-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Cards List Dropdown -->
            <div x-show="isRekeningOpen" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-amber-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                <template x-for="r in filteredRekeningBelanja.slice(0, 30)" :key="r.kode_rek">
                    <div @click="selectRekening(r)"
                         class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                         :class="r.kode_rek === formData.kode_rek ? 'border-amber-500 bg-amber-950/40 shadow-lg' : 'border-slate-800 hover:border-amber-500/50'">
                        <div class="min-w-0 pr-3">
                            <h4 class="text-xs font-bold text-white group-hover:text-amber-300 transition-colors truncate" x-text="r.kode_rek + ' - ' + r.nama_belanja"></h4>
                            <p class="text-[10px] text-slate-400 truncate" x-text="'REKENING • ' + (r.kelompok || 'Kode Account SIPD')"></p>
                        </div>
                        <button type="button" 
                                @click.stop="selectRekening(r)" 
                                class="shrink-0 px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all flex items-center space-x-1"
                                :class="r.kode_rek === formData.kode_rek ? 'bg-amber-400 text-slate-950 shadow-lg shadow-amber-400/30' : 'bg-amber-400/20 text-amber-300 border border-amber-400/40 hover:bg-amber-400 hover:text-slate-950'">
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
                    <span class="text-rose-400">*</span>
                </label>
                
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

            <!-- Cards List Dropdown -->
            <div x-show="isJenis108Open" x-transition x-cloak style="max-height: 195px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-cyan-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                <template x-for="j in filteredJenisAstap108" :key="j.kode">
                    <div @click="selectJenisAstap(j)"
                         class="p-3 rounded-2xl bg-slate-950 border transition-all flex items-center justify-between group cursor-pointer"
                         :class="j.kode === formData.jenis_aset_kode ? 'border-cyan-500 bg-cyan-950/40 shadow-lg' : 'border-slate-800 hover:border-cyan-500/50'">
                        <div class="min-w-0 pr-3">
                            <h4 class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors truncate" x-text="j.kode + ' - ' + j.nama"></h4>
                            <p class="text-[10px] text-slate-400 truncate" x-text="'PMDN 108 • Kelompok Klasifikasi Aset'"></p>
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
                    <button type="button" 
                            x-show="formData.sub_rincian_kode" 
                            @click="formData.sub_rincian_kode = ''; formData.sub_rincian_nama = ''; searchSubRincian108 = ''; isSubRincian108Open = false" 
                            class="text-xs font-bold text-amber-400 hover:text-amber-300 transition-colors flex items-center space-x-1 cursor-pointer">
                        <span>🗑️ Kosongkan Pilihan</span>
                    </button>
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

            <!-- Cards List Dropdown -->
            <div x-show="isSubRincian108Open" x-transition x-cloak style="max-height: 220px !important; overflow-y: auto !important;" class="absolute z-30 mt-2 w-full space-y-1.5 custom-scrollbar p-2 bg-slate-900 border border-emerald-500/50 rounded-2xl shadow-2xl backdrop-blur-xl">
                <div @click="formData.sub_rincian_kode = ''; formData.sub_rincian_nama = ''; searchSubRincian108 = ''; isSubRincian108Open = false"
                     class="p-2.5 rounded-xl bg-amber-950/40 hover:bg-amber-900/60 border border-amber-500/40 hover:border-amber-400 cursor-pointer transition-all flex items-center justify-between group">
                    <div class="min-w-0 pr-3 flex items-center space-x-2">
                        <span class="text-xs">🗑️</span>
                        <div>
                            <h4 class="text-xs font-bold text-amber-300 group-hover:text-amber-200 transition-colors">Kosongkan Pilihan Sub Rincian Objek</h4>
                            <p class="text-[10px] text-amber-400/80">Biarkan kosong, akan otomatis terisi saat memilih Nama Barang di nomor 4</p>
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
                    <span class="text-[10px] text-purple-400 font-normal italic">(Nama &amp; Kode Barang 108)</span>
                    <span class="text-rose-400">*</span>
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
                       :placeholder="activeKodeBarang ? (activeKodeBarang + ' - ' + activeNamaBarang) : 'Ketik nama / kode barang untuk mencari (contoh: USG, Ambulance, Laptop, Meja, CT Scan)...'" 
                       class="w-full bg-slate-950/90 border rounded-2xl px-4 py-3 pl-10 text-xs font-bold transition-all shadow-inner"
                       :class="activeKodeBarang && !isNamaBarang108Open ? 'border-purple-500/60 text-purple-200' : 'border-purple-500/40 text-white focus:border-purple-400'">
                <svg class="w-4 h-4 text-purple-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <!-- Cards List Dropdown -->
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
        <!-- PILIHAN KATEGORI EKSTRAKOMTABEL (HANYA UNTUK KIB B & KIB E)         -->
        <!-- ===================================================================== -->
        <div x-show="formData.jenis_aset_kode" x-transition class="space-y-2">
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-5 h-5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/40 text-[10px] font-black flex items-center justify-center">5</span>
                <span class="block text-slate-200 font-bold text-xs">Kategori Pencatatan Aset Hibah</span>
                <span class="text-[10px] text-amber-400 font-normal italic" x-text="(isMesin || isAsetLainnya) ? '(Reguler vs Ekstrakomtabel)' : '(Wajib Aset Tetap Intrakomptabel)'"></span>
            </div>

            <!-- KONDISI A: UNTUK KIB A (TANAH), KIB C (GEDUNG), KIB D (JALAN), KIB F (KDP), ATB -->
            <div x-show="!isMesin && !isAsetLainnya" class="p-4 rounded-2xl bg-slate-950/80 border border-emerald-500/30 space-y-2.5 shadow-xl">
                <div class="flex items-start space-x-3">
                    <div class="p-2.5 rounded-2xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-base shrink-0 mt-0.5">
                        🏛️
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center space-x-2">
                            <span class="text-xs font-black text-emerald-300 uppercase tracking-wider">Aset Tetap Intrakomptabel (Wajib Kapitalisasi)</span>
                            <span class="px-2 py-0.5 rounded text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40">Standar SAP</span>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Sesuai Standar Akuntansi Pemerintahan (SAP), kategori <strong class="text-white" x-text="isTanah ? 'Tanah (KIB A)' : (isGedung ? 'Gedung & Bangunan (KIB C)' : (isJaringan ? 'Jalan, Irigasi & Jaringan (KIB D)' : (isKdp ? 'Konstruksi Dalam Pengerjaan (KIB F)' : 'Aset Tidak Berwujud (ATB)')))"></strong> tidak mengenal batas minimal kapitalisasi Rp 300.000 dan wajib dicatat penuh ke dalam Neraca Aset Tetap.
                        </p>
                    </div>
                </div>
            </div>

            <!-- KONDISI B: UNTUK KIB B (PERALATAN & MESIN) DAN KIB E (ASET TETAP LAINNYA) -->
            <div x-show="isMesin || isAsetLainnya" class="p-4 rounded-2xl bg-slate-950/80 border border-amber-500/30 space-y-3 shadow-xl">
                <div class="flex items-start space-x-2 p-2.5 rounded-xl bg-amber-950/30 border border-amber-500/20">
                    <span class="text-amber-400 text-sm shrink-0 mt-0.5">⚖️</span>
                    <p class="text-[10.5px] text-amber-300/90 leading-relaxed">
                        <strong class="text-amber-300">Aturan Nilai Kapitalisasi Daerah:</strong>
                        Barang dengan harga taksiran satuan <strong class="text-emerald-300">&gt; Rp 300.000</strong> dicatat sebagai <strong>Aset Reguler (KIB)</strong>.
                        Barang dengan harga satuan <strong class="text-cyan-300">≤ Rp 300.000</strong> dicatat sebagai <strong>Barang Ekstrakomtabel (Extracom)</strong>.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <!-- Opsi 1: Aset Reguler / Kapitalisasi -->
                    <div @click="formData.is_extracomtable = false; syncRealisasiFromStep3();"
                         :class="!formData.is_extracomtable ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                         class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <input type="radio" name="global_extracom_choice" :checked="!formData.is_extracomtable" @change="formData.is_extracomtable = false; syncRealisasiFromStep3();" class="text-emerald-500 focus:ring-emerald-500">
                                <span class="text-xs font-black text-emerald-300">⚙️ Aset Reguler / Kapitalisasi</span>
                            </div>
                            <span x-show="!formData.is_extracomtable" class="text-[9px] px-2 py-0.5 rounded-md bg-emerald-500/20 text-emerald-300 font-bold border border-emerald-500/40">✓ Terpilih</span>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Aset kapitalisasi standar dengan taksiran harga satuan <strong class="text-emerald-300">&gt; Rp 300.000 / unit</strong>. Dicatat dalam KIB Peralatan &amp; Mesin.
                        </p>
                    </div>

                    <!-- Opsi 2: Barang Ekstrakomtabel (Extracom) -->
                    <div @click="formData.is_extracomtable = true; syncRealisasiFromStep3();"
                         :class="formData.is_extracomtable ? 'border-cyan-500 bg-cyan-950/40 ring-1 ring-cyan-500' : 'border-slate-800 bg-slate-900/60 opacity-60 hover:opacity-100 hover:border-slate-700'"
                         class="p-4 rounded-2xl border transition-all cursor-pointer space-y-2 relative group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-2">
                                <input type="radio" name="global_extracom_choice" :checked="formData.is_extracomtable" @change="formData.is_extracomtable = true; syncRealisasiFromStep3();" class="text-cyan-500 focus:ring-cyan-500">
                                <span class="text-xs font-black text-cyan-300">📦 Barang Ekstrakomtabel (Extracom)</span>
                            </div>
                            <span x-show="formData.is_extracomtable" class="text-[9px] px-2 py-0.5 rounded-md bg-cyan-500/20 text-cyan-300 font-bold border border-cyan-500/40">✓ Terpilih</span>
                        </div>
                        <p class="text-[11px] text-slate-300 leading-relaxed">
                            Barang non-kapitalisasi dengan taksiran harga satuan <strong class="text-cyan-300">≤ Rp 300.000 / unit</strong>. Dicatat di Sheet Ekstrakomtabel terpisah.
                        </p>
                    </div>
                </div>

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

        <!-- Nama Barang Lengkap & Satuan -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-1">
            <div class="sm:col-span-2">
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nama Lengkap Barang / Merk / Tipe <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.nama_barang" required
                    placeholder="Contoh: USG Mindray DC-30 Color Doppler Portable..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Satuan Barang <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.satuan" required
                    placeholder="Unit / Bidang / M2"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-semibold">
            </div>
        </div>

        <!-- Nilai Taksiran / Realisasi Hibah (Kolom 15) -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold text-xs mb-1 flex items-center justify-between">
                        <span>VOLUME / KUANTITAS <span class="text-rose-500 font-bold">*</span></span>
                        <span class="text-[10px] text-slate-400">Jumlah Unit Fisik</span>
                    </label>
                    <input type="number" min="1" x-model.number="formData.jumlah_volume" @input="syncRealisasiFromStep3()"
                        class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none focus:border-cyan-400">
                </div>
                <div>
                    <label class="block text-emerald-400 font-semibold text-xs mb-1 flex items-center justify-between">
                        <span>TOTAL NILAI REALISASI HIBAH (Rp)</span>
                        <span class="text-[10px] text-emerald-400 font-mono">⚡ Otomatis dari Rincian</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3.5 top-2.5 text-emerald-500 text-xs font-bold font-mono">Rp</span>
                        <input type="text" 
                            :value="formData.total_realisasi ? Number(formData.total_realisasi).toLocaleString('id-ID') : '0'"
                            readonly
                            class="w-full bg-slate-950 border border-emerald-500/40 rounded-xl px-3.5 py-2.5 pl-10 text-xs text-emerald-400 font-mono font-extrabold focus:outline-none cursor-not-allowed">
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
                <span>📄 Live Preview Tabel Aset Hibah:</span>
            </span>
            <span class="text-[10px] text-emerald-400 font-mono">Format Standar Laporan Aset Hibah</span>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-700 shadow-xl">
            <table class="w-full min-w-[760px] text-center text-xs border-collapse font-sans">
                <thead>
                    <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b border-slate-600 text-[11px]">
                        <th colspan="7" class="py-2 border border-slate-600 tracking-wider">
                            KLASIFIKASI PEROLEHAN ASET HIBAH
                        </th>
                    </tr>
                    <tr class="bg-[#fde9d9] text-slate-950 font-bold border-b-2 border-slate-700 text-[10px]">
                        <th class="px-3 py-2 border border-slate-600">Kode Rekening</th>
                        <th class="px-3 py-2 border border-slate-600">Nama Rekening / Akun</th>
                        <th class="px-3 py-2 border border-slate-600">Kode 108</th>
                        <th class="px-3 py-2 border border-slate-600">Nama Barang (Jenis 108)</th>
                        <th class="px-3 py-2 border border-slate-600">Kode Sub Rincian</th>
                        <th class="px-3 py-2 border border-slate-600">Sub Rincian Objek</th>
                        <th class="px-3 py-2 border border-slate-600">Nilai Perolehan (Rp)</th>
                    </tr>
                </thead>
                <tbody class="bg-white text-slate-950 font-medium text-[11px]">
                    <tr>
                        <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.kode_rek || '-'"></td>
                        <td class="px-3 py-3 border border-slate-400 text-left font-semibold" x-text="formData.nama_belanja || 'Perolehan dari Hibah'"></td>
                        <td class="px-3 py-3 border border-slate-400 font-mono font-bold text-cyan-800" x-text="formData.jenis_aset_kode || '-'"></td>
                        <td class="px-3 py-3 border border-slate-400 text-left font-semibold uppercase" x-text="formData.jenis_aset_nama || formData.nama_barang || '-'"></td>
                        <td class="px-3 py-3 border border-slate-400 font-mono font-bold" x-text="formData.sub_rincian_kode || '-'"></td>
                        <td class="px-3 py-3 border border-slate-400 text-left font-semibold uppercase" x-text="formData.sub_rincian_nama || '-'"></td>
                        <td class="px-3 py-3 border border-slate-400 text-right font-mono font-bold text-emerald-800" x-text="formatRupiah(formData.total_realisasi)"></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
