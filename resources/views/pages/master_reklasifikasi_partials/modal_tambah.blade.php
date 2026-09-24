<!-- MODAL TAMBAH REKLASIFIKASI BARU (ADAPTIF & DINAMIS PMDN 108) -->
<div x-show="showModalTambah" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-5"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    
    <!-- Backdrop Blur -->
    <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md" @click="showModalTambah = false"></div>

    <!-- Modal Dialog -->
    <div class="relative w-full max-w-3xl max-h-[92vh] flex flex-col bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden z-10"
         x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">
        
        <!-- Header Strip Accent -->
        <div class="h-1.5 w-full bg-gradient-to-r from-indigo-500 via-purple-500 to-cyan-500 shrink-0"></div>

        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-800 bg-slate-950/70 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3.5">
                <div class="p-2.5 rounded-2xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base sm:text-lg font-black text-white">Catat Reklasifikasi Aset Baru</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pemindahan bukuan antar KIB, Koreksi Rekening 108 &amp; Penyesuaian Fisik</p>
                </div>
            </div>
            <button type="button" @click="showModalTambah = false"
                class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 transition-colors cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <form @submit.prevent="submitFormTambah()" class="overflow-y-auto p-6 space-y-5 flex-1 scrollbar-thin scrollbar-thumb-slate-700">
            
            <!-- 1. Pilih Aset yang Akan Direklasifikasi -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider">
                    1. Pilih Barang Milik Daerah (ASTAP) <span class="text-rose-400">*</span>
                </label>
                <div class="relative">
                    <select x-model="formData.astap_id" @change="onSelectAstap($event.target.value)" required
                        class="w-full px-4 py-2.5 rounded-xl bg-slate-800/90 border border-slate-700 text-white text-xs font-medium focus:outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/40 shadow-inner">
                        <option value="">-- Pilih Barang dari Buku Inventaris ASTAP --</option>
                        @foreach ($kandidatAstaps as $kand)
                            <option value="{{ $kand->id }}" 
                                data-nilai="{{ $kand->total_realisasi }}"
                                data-nama="{{ $kand->nama_barang }}"
                                data-kib="{{ $kand->category ?: ($kand->jenisAstap ? substr($kand->jenisAstap->sub_rincian_objek ?? '', 0, 5) : '') }}"
                                data-prefix="{{ $kand->jenisAstap ? substr($kand->jenisAstap->sub_rincian_objek ?? '', 0, 8) : '' }}"
                                data-sub="{{ $kand->jenisAstap->uraian_sub_rincian ?? '' }}">
                                {{ $kand->nama_barang }} (Rp {{ number_format($kand->total_realisasi, 0, ',', '.') }})
                                [{{ $kand->category ?: 'ASTAP' }}]
                                {{ $kand->is_reklas ? ' • (Sudah Pernah Reklas)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Card Ringkasan Aset Terpilih -->
                <template x-if="selectedAstap">
                    <div class="p-3.5 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2 text-xs shadow-inner">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Kondisi Aset Saat Ini:</span>
                                <div class="text-sm font-black text-white truncate" x-text="selectedAstap.nama_barang"></div>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30"
                                          x-text="selectedAstap.category || 'KIB'"></span>
                                    <span class="text-slate-400 text-[11px] truncate" x-text="selectedAstap.jenis_astap?.uraian_sub_rincian || '-'"></span>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nilai Realisasi</span>
                                <div class="text-sm font-bold font-mono text-emerald-400"
                                     x-text="'Rp ' + Number(selectedAstap.total_realisasi || 0).toLocaleString('id-ID')"></div>
                                <div class="text-[11px] text-slate-400 mt-0.5" x-text="(selectedAstap.jumlah_volume || 1) + ' ' + (selectedAstap.satuan || 'Unit')"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- 2. Jenis Reklasifikasi -->
            <div class="space-y-2">
                <label class="block text-xs font-bold text-slate-200 uppercase tracking-wider">
                    2. Jenis Reklasifikasi <span class="text-rose-400">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-all select-none"
                        :class="formData.jenis_reklas === 'KOREKSI_REKENING' ? 'border-indigo-500 bg-indigo-500/10 shadow-sm shadow-indigo-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="KOREKSI_REKENING" @change="onJenisReklasChange()" class="text-indigo-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Koreksi Rekening / Pindah KIB</p>
                            <p class="text-[10px] text-slate-400">Salah kamar KIB atau penyesuaian akun PMDN 108</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-all select-none"
                        :class="formData.jenis_reklas === 'KDP_TO_DEFINITIF' ? 'border-emerald-500 bg-emerald-500/10 shadow-sm shadow-emerald-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="KDP_TO_DEFINITIF" @change="onJenisReklasChange()" class="text-emerald-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">KDP Selesai ➔ Definitif</p>
                            <p class="text-[10px] text-slate-400">Pekerjaan fisik KIB F selesai 100% jadi Gedung/Jaringan</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-all select-none"
                        :class="formData.jenis_reklas === 'EKSTRAKOMPTABEL' ? 'border-rose-500 bg-rose-500/10 shadow-sm shadow-rose-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="EKSTRAKOMPTABEL" @change="onJenisReklasChange()" class="text-rose-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Ekstrakomptabel</p>
                            <p class="text-[10px] text-slate-400">Nilai satuan ≤ Rp 300.000 (di bawah batas kapitalisasi)</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-all select-none"
                        :class="formData.jenis_reklas === 'HIBAH_MASUK' ? 'border-amber-500 bg-amber-500/10 shadow-sm shadow-amber-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="HIBAH_MASUK" @change="onJenisReklasChange()" class="text-amber-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Hibah / Bantuan Masuk</p>
                            <p class="text-[10px] text-slate-400">Penerimaan dari pihak ketiga / instansi lain</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 3. Klasifikasi KIB Tujuan & Rekening PMDN 108 -->
            <div x-show="formData.jenis_reklas === 'KOREKSI_REKENING' || formData.jenis_reklas === 'KDP_TO_DEFINITIF'"
                 x-transition.duration.200ms
                 class="p-4 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-4">
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-2.5">
                    <span class="text-xs font-bold text-white uppercase tracking-wider flex items-center gap-2">
                        <span>🎯</span> 3. Klasifikasi &amp; Rekening Tujuan PMDN 108
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        Hierarki 108
                    </span>
                </div>

                <!-- Tingkat 1: KIB Tujuan -->
                <div class="space-y-1.5">
                    <label class="block text-[11px] font-bold text-slate-300 uppercase tracking-wider">
                        Tingkat 1: Pilih Kelompok / KIB Tujuan <span class="text-rose-400">*</span>
                    </label>
                    <select x-model="formData.tujuan_kib" @change="onTujuanKibChange()"
                        class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs font-bold focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih KIB / Kelompok Tujuan --</option>
                        <option value="KIB A">KIB A - Tanah (1.3.1)</option>
                        <option value="KIB B">KIB B - Peralatan &amp; Mesin (1.3.2)</option>
                        <option value="KIB C">KIB C - Gedung &amp; Bangunan (1.3.3)</option>
                        <option value="KIB D">KIB D - Jalan, Jaringan &amp; Irigasi (1.3.4)</option>
                        <option value="KIB E">KIB E - Aset Tetap Lainnya (1.3.5)</option>
                        <option value="ATB">ATB - Aset Tidak Berwujud (1.5.3)</option>
                    </select>
                </div>

                <!-- Tingkat 2: Sub-Rincian Rekening 108 -->
                <div class="space-y-1.5 relative" @click.away="isReklasSubRincianOpen = false">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[9px] font-black flex items-center justify-center">2</span>
                            <span>Sub-Rincian Objek PMDN 108:</span>
                        </label>
                        <div class="shrink-0 flex items-center gap-1.5" x-show="reklasSubRincianKode">
                            <button type="button" @click="clearSubRincian()" class="text-[10px] text-amber-300 hover:underline">
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <input type="text"
                               :value="(!isReklasSubRincianOpen && reklasSubRincianKode) ? (reklasSubRincianKode + ' - ' + reklasSubRincianNama) : searchReklasSubRincian"
                               @input="searchReklasSubRincian = $event.target.value; isReklasSubRincianOpen = true"
                               @focus="isReklasSubRincianOpen = true"
                               :placeholder="reklasSubRincianKode ? (reklasSubRincianKode + ' - ' + reklasSubRincianNama) : 'Ketik untuk memfilter sub-rincian 108...'"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border text-white text-xs font-medium focus:outline-none shadow-inner"
                               :class="reklasSubRincianKode && !isReklasSubRincianOpen ? 'border-emerald-500/60 text-emerald-200' : 'border-slate-700 focus:border-emerald-400'">
                    </div>

                    <!-- Dropdown List Sub-Rincian -->
                    <div x-show="isReklasSubRincianOpen" x-transition x-cloak
                         class="absolute z-30 mt-1.5 w-full max-h-48 overflow-y-auto space-y-1 p-2 bg-slate-900/95 border border-emerald-500/50 rounded-xl shadow-2xl backdrop-blur-xl custom-scrollbar">
                        <template x-for="s in filteredSubRincian108" :key="s.kode">
                            <div @click="selectSubRincian(s)"
                                 class="p-2 rounded-lg bg-slate-950 border border-slate-800 hover:border-emerald-500/50 hover:bg-slate-900 cursor-pointer flex items-center justify-between text-xs">
                                <span class="font-bold text-white truncate mr-2" x-text="s.kode + ' - ' + s.nama"></span>
                                <span class="shrink-0 text-[10px] text-emerald-300 bg-emerald-500/20 px-2 py-0.5 rounded">Pilih</span>
                            </div>
                        </template>
                        <template x-if="isReklasSubRincianOpen && filteredSubRincian108.length === 0">
                            <div class="p-2.5 text-center text-xs text-slate-400 italic">Tidak ada sub-rincian cocok.</div>
                        </template>
                    </div>
                </div>

                <!-- Tingkat 3: Sub-Sub Rincian / Identitas Barang 108 -->
                <div class="space-y-1.5 relative" @click.away="isReklasSubSubRincianOpen = false">
                    <div class="flex items-center justify-between gap-2">
                        <label class="text-[11px] font-bold text-slate-300 uppercase tracking-wider flex items-center gap-1.5">
                            <span class="w-4 h-4 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[9px] font-black flex items-center justify-center">3</span>
                            <span>Identitas Barang / Kode 108 (Sub-Sub):</span>
                        </label>
                        <div class="shrink-0 flex items-center gap-1.5" x-show="reklasSubSubRincianKode">
                            <button type="button" @click="clearSubSubRincian()" class="text-[10px] text-amber-300 hover:underline">
                                Kosongkan
                            </button>
                        </div>
                    </div>

                    <div class="relative">
                        <input type="text"
                               :value="(!isReklasSubSubRincianOpen && reklasSubSubRincianKode) ? (reklasSubSubRincianKode + ' - ' + reklasSubSubRincianNama) : searchReklasSubSubRincian"
                               @input="searchReklasSubSubRincian = $event.target.value; isReklasSubSubRincianOpen = true"
                               @focus="isReklasSubSubRincianOpen = true"
                               :placeholder="reklasSubSubRincianKode ? (reklasSubSubRincianKode + ' - ' + reklasSubSubRincianNama) : 'Ketik nama / kode barang spesifik...'"
                               class="w-full px-3.5 py-2.5 rounded-xl bg-slate-900 border text-white text-xs font-medium focus:outline-none shadow-inner"
                               :class="reklasSubSubRincianKode && !isReklasSubSubRincianOpen ? 'border-purple-500/60 text-purple-200' : 'border-slate-700 focus:border-purple-400'">
                    </div>

                    <!-- Dropdown List Sub-Sub Rincian -->
                    <div x-show="isReklasSubSubRincianOpen" x-transition x-cloak
                         class="absolute z-30 mt-1.5 w-full max-h-48 overflow-y-auto space-y-1 p-2 bg-slate-900/95 border border-purple-500/50 rounded-xl shadow-2xl backdrop-blur-xl custom-scrollbar">
                        <template x-for="item in filteredSubSubRincian108" :key="item.kode">
                            <div @click="selectSubSubRincian(item)"
                                 class="p-2 rounded-lg bg-slate-950 border border-slate-800 hover:border-purple-500/50 hover:bg-slate-900 cursor-pointer flex items-center justify-between text-xs">
                                <span class="font-bold text-white truncate mr-2" x-text="item.kode + ' - ' + item.nama"></span>
                                <span class="shrink-0 text-[10px] text-purple-300 bg-purple-500/20 px-2 py-0.5 rounded">Pilih</span>
                            </div>
                        </template>
                        <template x-if="isReklasSubSubRincianOpen && filteredSubSubRincian108.length === 0">
                            <div class="p-2.5 text-center text-xs text-slate-400 italic">Tidak ada rincian barang cocok.</div>
                        </template>
                    </div>
                </div>

                <!-- Pemetaan Baris Matriks 42 Baris Neraca (Otomatis / Sinkron) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2 border-t border-slate-800">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                            Baris Matriks Asal (Mutasi Kurang -) <span class="text-rose-400">*</span>
                        </label>
                        <select x-model="formData.jenis_reklasifikasi_asal_id" required
                            class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                            <option value="">-- Pilih Baris Matriks Asal --</option>
                            @foreach ($templateRows as $tRow)
                                <option value="{{ $tRow->id }}">
                                    [{{ $tRow->kelompok_kib }}] {{ $tRow->nama_sub_rincian }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                            Baris Matriks Tujuan (Mutasi Tambah +) <span class="text-emerald-400">*</span>
                        </label>
                        <select x-model="formData.jenis_reklasifikasi_tujuan_id"
                            class="w-full px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                            <option value="">-- Pilih Baris Matriks Tujuan --</option>
                            @foreach ($templateRows as $tRow)
                                <option value="{{ $tRow->id }}">
                                    [{{ $tRow->kelompok_kib }}] {{ $tRow->nama_sub_rincian }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- 4. Form Spesifikasi Fisik Baru Dinamis Sesuai KIB Tujuan -->
            <div x-show="formData.tujuan_kib && (formData.jenis_reklas === 'KOREKSI_REKENING' || formData.jenis_reklas === 'KDP_TO_DEFINITIF')"
                 x-transition.duration.300ms
                 class="p-4 rounded-2xl bg-slate-900 border border-cyan-500/30 space-y-3.5 shadow-lg">
                
                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                    <div class="flex items-center space-x-2">
                        <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center shrink-0">4</span>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                            📝 Spesifikasi Fisik Baru (<span class="text-cyan-300 font-extrabold" x-text="formData.tujuan_kib"></span>):
                        </span>
                    </div>
                    <span class="text-[10px] text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/30 font-semibold">
                        Format Spek Dinamis
                    </span>
                </div>

                <div class="p-2.5 rounded-xl bg-cyan-950/20 border border-cyan-500/20 text-[11px] text-cyan-200/90 leading-relaxed flex items-center gap-2">
                    <span>💡</span>
                    <span>Aset dialihkan ke kelompok <strong class="text-white" x-text="formData.tujuan_kib"></strong>. Rincian spesifikasi lama otomatis diarsip ke riwayat audit, dan form di bawah menyesuaikan register &amp; KIR sesuai format <strong class="text-white" x-text="formData.tujuan_kib"></strong>.</span>
                </div>

                <!-- Form Spesifik: KIB A - Tanah -->
                <template x-if="formData.tujuan_kib === 'KIB A'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Tanah (m²):</label>
                                <input type="number" step="0.01" min="0" x-model="formData.spekBaru.tanah_luas_m2" placeholder="Contoh: 1500"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Status Hak Tanah:</label>
                                <select x-model="formData.spekBaru.tanah_hak"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Hak Pakai">Hak Pakai</option>
                                    <option value="Hak Milik">Hak Milik</option>
                                    <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                    <option value="Hak Guna Bangunan">Hak Guna Bangunan (HGB)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Sertifikat Tanah:</label>
                                <input type="text" x-model="formData.spekBaru.tanah_sertifikat_no" placeholder="Contoh: No. 12.04.05.001..."
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Tanggal Sertifikat:</label>
                                <input type="date" x-model="formData.spekBaru.tanah_sertifikat_tgl"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Penggunaan Bidang Tanah:</label>
                                <input type="text" x-model="formData.spekBaru.tanah_penggunaan" placeholder="Contoh: Gedung Instalasi Farmasi & Rawat Inap RSUD"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Alamat / Lokasi Fisik Tanah:</label>
                                <input type="text" x-model="formData.spekBaru.tanah_alamat" placeholder="Contoh: Jl. Kapten Piere Tendean No. 1 Bondowoso"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: KIB B - Peralatan & Mesin -->
                <template x-if="formData.tujuan_kib === 'KIB B'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Merk / Pabrikan:</label>
                                <input type="text" x-model="formData.spekBaru.mesin_merk" placeholder="Contoh: GE Healthcare / Philips / Honda"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Tipe / Model:</label>
                                <input type="text" x-model="formData.spekBaru.mesin_type" placeholder="Contoh: Brivo XR575 / Veradius"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Pabrik / Seri:</label>
                                <input type="text" x-model="formData.spekBaru.mesin_no_pabrik" placeholder="Contoh: SN-88291039"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Ukuran / Kapasitas:</label>
                                <input type="text" x-model="formData.spekBaru.mesin_ukuran_cc" placeholder="Contoh: 500 mA / 2000 VA / 150 cc"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Bahan / Material:</label>
                                <input type="text" x-model="formData.spekBaru.mesin_bahan" placeholder="Contoh: Logam, Komponen Elektronik, Kaca Optik"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Rangka / Polisi (Opsional):</label>
                                <input type="text" x-model="formData.spekBaru.mesin_no_polisi" placeholder="Contoh: P 1234 AP (jika kendaraan dinas)"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: KIB C - Gedung & Bangunan -->
                <template x-if="formData.tujuan_kib === 'KIB C'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Bertingkat:</label>
                                <select x-model="formData.spekBaru.gedung_konstruksi_bertingkat"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Bertingkat">Bertingkat (2 Lantai atau lebih)</option>
                                    <option value="Tidak Bertingkat">Tidak Bertingkat (1 Lantai)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Beton / Baja:</label>
                                <select x-model="formData.spekBaru.gedung_konstruksi_beton"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Beton">Konstruksi Beton Bertulang</option>
                                    <option value="Baja">Konstruksi Baja</option>
                                    <option value="Kayu">Konstruksi Kayu</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Lantai Gedung (m²):</label>
                                <input type="number" step="0.01" min="0" x-model="formData.spekBaru.gedung_luas_lantai_m2" placeholder="Contoh: 450.5"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Status Kepemilikan Tanah Gedung:</label>
                                <select x-model="formData.spekBaru.gedung_status_tanah"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Tanah Pemda">Tanah Milik Pemda / RSUD</option>
                                    <option value="Hak Pakai">Hak Pakai</option>
                                    <option value="Sewa">Sewa / Pinjam Pakai</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Dokumen PBG / IMB:</label>
                                <input type="text" x-model="formData.spekBaru.gedung_dokumen_nomor" placeholder="Contoh: 640/IMB/DPMPTSP/2026"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Tanggal Dokumen IMB:</label>
                                <input type="date" x-model="formData.spekBaru.gedung_dokumen_tgl"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div class="sm:col-span-2">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Alamat / Letak Gedung di RSUD:</label>
                                <input type="text" x-model="formData.spekBaru.gedung_alamat" placeholder="Contoh: Sayap Barat RSUD Dr. H. Koesnandi"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: KIB D - Jalan, Jaringan & Irigasi -->
                <template x-if="formData.tujuan_kib === 'KIB D'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Fisik Jaringan:</label>
                                <input type="text" x-model="formData.spekBaru.jaringan_konstruksi" placeholder="Contoh: Aspal Hotmix / Paving Blok / Pipa HDPE"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Jaringan (m²):</label>
                                <input type="number" step="0.01" min="0" x-model="formData.spekBaru.jaringan_luas_m2" placeholder="Contoh: 1200"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Panjang (m/km):</label>
                                <input type="text" x-model="formData.spekBaru.jaringan_panjang_km" placeholder="Contoh: 350 Meter"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Lebar (Meter):</label>
                                <input type="text" x-model="formData.spekBaru.jaringan_lebar_m" placeholder="Contoh: 6 Meter"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: KIB E - Aset Tetap Lainnya -->
                <template x-if="formData.tujuan_kib === 'KIB E'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Judul / Pencipta / Spesifikasi:</label>
                                <input type="text" x-model="formData.spekBaru.lainnya_judul_pencipta" placeholder="Judul buku, lukisan, atau instrumen musik..."
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Bahan / Asal Usul:</label>
                                <input type="text" x-model="formData.spekBaru.lainnya_bahan" placeholder="Contoh: Kanvas / Perunggu / Kayu Jati"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: ATB - Aset Tak Berwujud -->
                <template x-if="formData.tujuan_kib === 'ATB'">
                    <div class="space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nama Software / Sistem Informasi:</label>
                                <input type="text" x-model="formData.spekBaru.atb_nama_software" placeholder="Contoh: Modul SIMRS Radiologi & Bridging BPJS"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Vendor / Pengembang Software:</label>
                                <input type="text" x-model="formData.spekBaru.atb_pengembang" placeholder="Contoh: PT Medika Teknologi Solusindo"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Estimasi Masa Manfaat (Tahun):</label>
                                <input type="number" min="1" max="20" x-model="formData.spekBaru.atb_masa_manfaat" placeholder="4"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Registrasi Lisensi / HAKI:</label>
                                <input type="text" x-model="formData.spekBaru.atb_nomor_lisensi" placeholder="Contoh: LIC-SIMRS-2026-009"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- 5. Nilai, Tanggal, Triwulan & Periode -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Nilai Reklasifikasi (Rp) <span class="text-rose-400">*</span>
                    </label>
                    <input type="number" step="0.01" x-model="formData.nilai_reklas" required
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white font-mono text-xs focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Tanggal Reklas <span class="text-rose-400">*</span>
                    </label>
                    <input type="date" x-model="formData.tanggal_reklas" required
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Triwulan <span class="text-rose-400">*</span>
                    </label>
                    <select x-model="formData.triwulan" required
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                        <option value="1">TW I</option>
                        <option value="2">TW II</option>
                        <option value="3">TW III</option>
                        <option value="4">TW IV</option>
                    </select>
                </div>
            </div>

            <!-- 6. Nomor Berita Acara, Alasan Reklasifikasi & Keterangan -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Nomor BA Reklasifikasi
                    </label>
                    <input type="text" x-model="formData.nomor_ba_reklas" placeholder="000.2.3.2/.../430.10.7/2026"
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        📝 Alasan Reklasifikasi
                    </label>
                    <input type="text" x-model="formData.alasan_reklas" placeholder="Contoh: Salah kode rekening pengadaan..."
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Keterangan Tambahan
                    </label>
                    <input type="text" x-model="formData.keterangan" placeholder="Catatan tambahan jika ada..."
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- 7. Narasi Preview Berita Acara Otomatis -->
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 space-y-1.5 shadow-inner">
                <span class="text-[10px] font-bold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span>📑</span> Preview Narasi Berita Acara:
                </span>
                <p class="text-xs text-slate-300 italic leading-relaxed" x-text="getNarasiPreview()"></p>
            </div>

            <!-- Modal Footer (Inside Form) -->
            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3 shrink-0">
                <button type="button" @click="showModalTambah = false"
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-bold transition-all cursor-pointer">
                    Batal
                </button>
                <button type="submit" :disabled="isSubmitting"
                    class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/50 transition-all disabled:opacity-50 cursor-pointer">
                    <span x-show="!isSubmitting">Simpan Transaksi Reklasifikasi</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2">
                        <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        <span>Menyimpan...</span>
                    </span>
                </button>
            </div>
        </form>
    </div>
</div>
