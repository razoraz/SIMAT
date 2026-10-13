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
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
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
                            <p class="text-xs font-bold text-white">Ekstrakomptabel (≤ Rp 300rb)</p>
                            <p class="text-[10px] text-slate-400">Nilai satuan ≤ Rp 300.000 (di bawah batas kapitalisasi)</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-all select-none"
                        :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'border-teal-500 bg-teal-500/10 shadow-sm shadow-teal-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="KAPITALISASI_INTRAKOM" @change="onJenisReklasChange()" class="text-teal-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Kapitalisasi Intrakomptabel</p>
                            <p class="text-[10px] text-slate-400">Naikkan aset ekstrakomptabel → Aset Tetap (> Rp 300.000)</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-all select-none"
                        :class="formData.jenis_reklas === 'HIBAH_KELUAR' ? 'border-purple-500 bg-purple-500/10 shadow-sm shadow-purple-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="HIBAH_KELUAR" @change="onJenisReklasChange()" class="text-purple-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Hibah Keluar (Dihibahkan)</p>
                            <p class="text-[10px] text-slate-400">Dihibahkan ke luar (Koreksi Baris 40 / Kolom 12)</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-all select-none"
                        :class="formData.jenis_reklas === 'HIBAH_MASUK' ? 'border-amber-500 bg-amber-500/10 shadow-sm shadow-amber-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="HIBAH_MASUK" @change="onJenisReklasChange()" class="text-amber-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Hibah Masuk (Bantuan)</p>
                            <p class="text-[10px] text-slate-400">Penerimaan bantuan pemerintah / pihak ketiga non-kas</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-all select-none"
                        :class="formData.jenis_reklas === 'MUTASI_EKSTERNAL' ? 'border-teal-500 bg-teal-500/10 shadow-sm shadow-teal-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="MUTASI_EKSTERNAL" @change="onJenisReklasChange()" class="text-teal-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Mutasi Antar-OPD</p>
                            <p class="text-[10px] text-slate-400">Pindah ke SKPD / OPD lain (Koreksi Baris 42 / Kolom 13)</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-2xl border border-slate-700/80 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-all select-none sm:col-span-2 lg:col-span-2"
                        :class="formData.jenis_reklas === 'KOREKSI_LAIN' ? 'border-cyan-500 bg-cyan-500/10 shadow-sm shadow-cyan-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="KOREKSI_LAIN" @change="onJenisReklasChange()" class="text-cyan-500 focus:ring-0">
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-xs font-bold text-white">Koreksi Nilai / Perubahan Total Dana</p>
                                <span class="text-[9.5px] font-bold px-2 py-0.5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                    Audit BPK / Kapitalisasi Susulan
                                </span>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-0.5">Penyesuaian nominal buku aset bertambah (+) atau berkurang (-) dan sinkronisasi ke neraca</p>
                        </div>
                    </label>
                </div>
            </div>

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

            <!-- 2C. Panel Khusus Hibah (Keluar / Masuk) -->
            <div x-show="formData.jenis_reklas === 'HIBAH_KELUAR' || formData.jenis_reklas === 'HIBAH_MASUK'"
                 x-transition.duration.200ms
                 class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-purple-500/30 space-y-4 shadow-xl">
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-purple-500/20 text-purple-300 border border-purple-500/40 text-xs font-black flex items-center justify-center">🎁</span>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                            Informasi Berita Acara Serah Terima (BAST) Hibah
                        </span>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 border border-purple-500/40">
                        Penyeimbang: Baris 40 (Koreksi Hibah / Kolom 12)
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-purple-950/25 border border-purple-500/20 text-xs text-purple-200/90 leading-relaxed flex items-start gap-2.5">
                    <span class="text-base shrink-0 mt-0.5">💡</span>
                    <div>
                        <strong class="text-white block mb-0.5" x-text="formData.jenis_reklas === 'HIBAH_KELUAR' ? 'Hibah Keluar (Menyerahkan Aset ke Pihak Lain):' : 'Hibah Masuk (Menerima Bantuan Pemerintah):'"></strong>
                        <span x-text="formData.jenis_reklas === 'HIBAH_KELUAR' 
                            ? 'Aset tetap RSUD berkurang (Mutasi Kurang -). Baris 40 (Koreksi Hibah) bertambah (+) pada Kolom 12 (Dihibahkan) sebagai penyeimbang seimbang (TRUE).'
                            : 'Fisik aset bertambah di RSUD (Mutasi Tambah +). Baris 40 (Koreksi Hibah) berkurang (-) sebagai penyeimbang agar belanja modal kas daerah tidak membengkak.'"></span>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div>
                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                            Pihak Terkait / Lembaga / Penerima / Pemberi: <span class="text-rose-400">*</span>
                        </label>
                        <input type="text" x-model="formData.pihak_hibah"
                               :placeholder="formData.jenis_reklas === 'HIBAH_KELUAR' ? 'Contoh: Puskesmas Tamanan / Yayasan Sosial' : 'Contoh: Kementerian Kesehatan RI / Dinkes Prov'"
                               class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                            Tanggal BAST Hibah: <span class="text-rose-400">*</span>
                        </label>
                        <input type="date" x-model="formData.tanggal_bast"
                               class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- 2D. Panel Khusus Mutasi Eksternal (Antar-OPD / SKPD) -->
            <div x-show="formData.jenis_reklas === 'MUTASI_EKSTERNAL'"
                 x-transition.duration.200ms
                 class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-teal-500/30 space-y-4 shadow-xl">
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-teal-500/20 text-teal-300 border border-teal-500/40 text-xs font-black flex items-center justify-center">🏛️</span>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                            Informasi Mutasi Antar-OPD / SKPD Pemerintah Daerah
                        </span>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-teal-500/20 text-teal-300 border border-teal-500/40">
                        Penyeimbang: Baris 42 (Koreksi Lain / Kolom 13)
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-teal-950/25 border border-teal-500/20 text-xs text-teal-200/90 leading-relaxed flex items-start gap-2.5">
                    <span class="text-base shrink-0 mt-0.5">💡</span>
                    <div>
                        <strong class="text-white block mb-0.5">Pengalihan Aset Keluar RSUD Antar-Perangkat Daerah:</strong>
                        <span>Aset tetap RSUD dialihkan ke Dinas/Badan/Kecamatan lain di lingkungan Pemkab Bondowoso. Aset RSUD berkurang (Mutasi Kurang -), disinkronkan ke Baris 42 Koreksi Lain-Lain (Kolom 13 Mutasi -), serta otomatis tercatat di modul <strong>Mutasi Eksternal Keluar</strong>.</span>
                    </div>
                </div>

                <!-- Bagian 1: Identitas Pihak Pertama (RSUD Dr. H. Koesnadi - Yang Menyerahkan) -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs">📤</span>
                            <span class="text-xs font-bold text-teal-300 uppercase tracking-wider">PIHAK PERTAMA (Yang Menyerahkan - RSUD Dr. H. Koesnadi)</span>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/20 font-bold">
                            Internal RSUD
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <!-- Nama Pejabat Penyerah RSUD (Combobox) -->
                        <div class="relative space-y-1" @click.away="isPejabatAsalDropdownOpen = false">
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                Nama Pejabat Penyerah RSUD: <span class="text-rose-400">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" x-model="formData.pj_asal_nama"
                                       @focus="isPejabatAsalDropdownOpen = true"
                                       placeholder="Nama Pejabat RSUD"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                <button type="button" @click="isPejabatAsalDropdownOpen = !isPejabatAsalDropdownOpen"
                                        class="absolute right-2 top-2 text-slate-400 hover:text-white text-xs">▾</button>
                            </div>

                            <!-- Dropdown Riwayat Pejabat RSUD -->
                            <div x-show="isPejabatAsalDropdownOpen" x-cloak
                                 class="absolute z-40 mt-1 w-full bg-slate-900 border border-teal-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-48 overflow-y-auto">
                                <div @click="selectPejabatAsal({ nama: 'BUDI HARTONO, S.Sos', nip: '19760229 200801 1 010', jabatan: 'Pengurus Barang Pengguna' })"
                                     class="p-2.5 hover:bg-teal-500/15 cursor-pointer text-left transition-colors">
                                    <div class="font-bold text-white text-xs">BUDI HARTONO, S.Sos</div>
                                    <div class="text-[10px] text-teal-300 font-mono">NIP: 19760229 200801 1 010 · Pengurus Barang Pengguna</div>
                                </div>
                                <div @click="selectPejabatAsal({ nama: 'dr. H. Yus Priyatna, Sp.P', nip: '196904121999031004', jabatan: 'Direktur RSUD Dr. H. Koesnadi' })"
                                     class="p-2.5 hover:bg-teal-500/15 cursor-pointer text-left transition-colors">
                                    <div class="font-bold text-white text-xs">dr. H. Yus Priyatna, Sp.P</div>
                                    <div class="text-[10px] text-teal-300 font-mono">NIP: 196904121999031004 · Direktur RSUD Dr. H. Koesnadi</div>
                                </div>
                                <template x-for="p in (dbPejabats || [])" :key="p.nama">
                                    <div x-show="p.nama !== 'BUDI HARTONO, S.Sos' && p.nama !== 'dr. H. Yus Priyatna, Sp.P'"
                                         @click="selectPejabatAsal({ nama: p.nama, nip: p.nip || '-', jabatan: 'Pengurus Barang / PPK RSUD' })"
                                         class="p-2 hover:bg-teal-500/15 cursor-pointer text-left transition-colors">
                                        <div class="font-bold text-white text-xs" x-text="p.nama"></div>
                                        <div class="text-[10px] text-slate-400 font-mono" x-text="'NIP: ' + (p.nip || '-')"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                NIP Pejabat Penyerah RSUD:
                            </label>
                            <input type="text" x-model="formData.pj_asal_nip"
                                   placeholder="19xxxxxxxxxxxxxx"
                                   class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                Jabatan Penyerah RSUD:
                            </label>
                            <input type="text" x-model="formData.pj_asal_jabatan"
                                   placeholder="Pengurus Barang Pengguna"
                                   class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Identitas Pihak Kedua (SKPD / OPD Penerima) -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs">📥</span>
                            <span class="text-xs font-bold text-cyan-300 uppercase tracking-wider">PIHAK KEDUA (Yang Menerima - SKPD / OPD Tujuan)</span>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">
                            Eksternal Pemkab
                        </span>
                    </div>

                    <!-- Combobox Autocomplete SKPD Penerima -->
                    <div class="relative space-y-1" @click.away="isSkpdDropdownOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                SKPD / OPD Penerima BMD: <span class="text-rose-400">*</span>
                            </label>
                            <template x-if="skpdList && skpdList.length > 0">
                                <span class="text-[9.5px] text-cyan-300/80 font-mono" x-text="skpdList.length + ' Pilihan Instansi'"></span>
                            </template>
                        </div>

                        <div class="relative">
                            <input type="text" x-model="formData.skpd_tujuan"
                                   @focus="isSkpdDropdownOpen = true"
                                   @input="isSkpdDropdownOpen = true"
                                   placeholder="Ketik atau pilih nama dinas/instansi penerima (contoh: Dinas Kesehatan, BPKAD Bondowoso...)"
                                   autocomplete="off"
                                   class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3.5 py-2.5 pl-9 pr-8 text-xs text-white focus:outline-none font-bold shadow-inner">
                            <span class="text-xs text-teal-400 absolute left-3 top-2.5">🏛️</span>
                            <button type="button" x-show="formData.skpd_tujuan"
                                    @click="formData.skpd_tujuan = ''; isSkpdDropdownOpen = true"
                                    class="absolute right-2.5 top-2.5 text-slate-400 hover:text-rose-300 text-xs">✕</button>
                        </div>

                        <!-- Dropdown List SKPD -->
                        <div x-show="isSkpdDropdownOpen && filteredSkpdList.length > 0" x-cloak
                             class="absolute z-40 mt-1 w-full bg-slate-900 border border-teal-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-56 overflow-y-auto">
                            <div class="px-3 py-1.5 bg-slate-950 text-[10px] font-bold text-teal-400 uppercase tracking-wider flex items-center justify-between">
                                <span>Pilih Direktori SKPD Pemkab</span>
                                <span class="font-mono text-slate-400" x-text="filteredSkpdList.length + ' hasil'"></span>
                            </div>
                            <template x-for="(skpd, sIdx) in filteredSkpdList" :key="sIdx">
                                <div @click="selectSkpd(skpd)"
                                     class="p-2.5 hover:bg-teal-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2.5 text-left"
                                     :class="formData.skpd_tujuan === skpd ? 'bg-teal-500/20 text-teal-200' : 'text-slate-200'">
                                    <div class="min-w-0">
                                        <div class="font-bold text-xs group-hover:text-teal-300 truncate" x-text="skpd"></div>
                                        <div class="text-[10px] text-slate-400 truncate flex items-center gap-1 mt-0.5" x-show="skpdDirectory[skpd]?.alamat">
                                            <span>📍</span>
                                            <span x-text="skpdDirectory[skpd]?.alamat"></span>
                                        </div>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded bg-teal-500/10 text-teal-300 border border-teal-500/25 font-bold shrink-0">
                                        Pilih ↵
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Pejabat Penerima OPD (Combobox Dropdown) -->
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div class="relative space-y-1" @click.away="isPejabatTujuanDropdownOpen = false">
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                Nama Pejabat Penerima OPD:
                            </label>
                            <div class="relative">
                                <input type="text" x-model="formData.pejabat_opd_tujuan"
                                       @focus="isPejabatTujuanDropdownOpen = true"
                                       placeholder="Nama Pejabat / Pengurus Barang"
                                       class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                <button type="button" x-show="availablePejabatTujuanList.length > 0"
                                        @click="isPejabatTujuanDropdownOpen = !isPejabatTujuanDropdownOpen"
                                        class="absolute right-2 top-2 text-slate-400 hover:text-white text-xs">▾</button>
                            </div>

                            <!-- Dropdown Riwayat Pejabat Tujuan -->
                            <div x-show="isPejabatTujuanDropdownOpen && filteredPejabatTujuanList.length > 0" x-cloak
                                 class="absolute z-40 mt-1 w-full bg-slate-900 border border-teal-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-44 overflow-y-auto">
                                <div class="px-3 py-1 bg-slate-950 text-[10px] font-bold text-teal-400 uppercase tracking-wider">
                                    Riwayat Pejabat SKPD Terpilih
                                </div>
                                <template x-for="(p, pIdx) in filteredPejabatTujuanList" :key="pIdx">
                                    <div @click="selectPejabatTujuan(p)"
                                         class="p-2 hover:bg-teal-500/15 cursor-pointer text-left transition-colors">
                                        <div class="font-bold text-white text-xs" x-text="p.nama"></div>
                                        <div class="text-[10px] text-slate-400 font-mono" x-text="'NIP: ' + (p.nip || '-') + ' · ' + (p.jabatan || '')"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                NIP Pejabat Penerima OPD:
                            </label>
                            <input type="text" x-model="formData.nip_pejabat_opd_tujuan"
                                   placeholder="19xxxxxxxxxxxxxx atau -"
                                   class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                Jabatan Pejabat Penerima:
                            </label>
                            <input type="text" x-model="formData.jabatan_opd_tujuan"
                                   placeholder="Pengurus Barang / Kepala Bidang"
                                   class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                        </div>
                    </div>

                    <!-- Alamat SKPD Penerima -->
                    <div>
                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                            Alamat Kantor SKPD / Instansi Penerima:
                        </label>
                        <div class="relative">
                            <input type="text" x-model="formData.alamat_instansi"
                                   placeholder="Jl. Letnan Karsono No. 2, Bondowoso"
                                   class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 pl-8 text-xs text-white focus:outline-none">
                            <span class="text-xs text-slate-500 absolute left-2.5 top-2">📍</span>
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: Dokumen Legalitas BAST, Validasi Tanggal & Unggah Berkas Fisik -->
                <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                        <div class="flex items-center gap-2">
                            <span class="text-xs">📜</span>
                            <span class="text-xs font-bold text-white uppercase tracking-wider">Legalitas Berita Acara &amp; Dokumen Lampiran</span>
                        </div>
                        <span class="text-[10px] text-slate-400 font-mono">BAST &amp; SK Bupati</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                Nomor BAST Mutasi Eksternal: <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" x-model="formData.nomor_ba_reklas"
                                   placeholder="000.2.3.2/.../430.10.7/2026"
                                   class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Tanggal BAST Mutasi: <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[9px] text-teal-300 font-mono">Maks: Hari Ini</span>
                            </div>
                            <input type="date" x-model="formData.tanggal_bast"
                                   :max="new Date().toISOString().split('T')[0]"
                                   :class="isTanggalBastInvalid() ? 'border-rose-500 bg-rose-950/20 text-rose-200' : 'border-slate-700 bg-slate-950 text-white'"
                                   class="w-full border focus:border-teal-400 rounded-xl px-3 py-2 text-xs focus:outline-none font-mono">
                            <template x-if="isTanggalBastInvalid()">
                                <p class="text-[10px] text-rose-400 font-semibold mt-1">⚠️ Tanggal dokumen BAST tidak boleh melebihi tanggal hari ini!</p>
                            </template>
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                Nomor SK Dasar / Pemindahtanganan:
                            </label>
                            <input type="text" x-model="formData.nomor_sk_dasar"
                                   placeholder="SK Bupati / Nomor BAST Transfer BMD"
                                   class="w-full bg-slate-950 border border-slate-700 focus:border-teal-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                        </div>
                    </div>

                    <!-- Dropzone Unggah Berkas Fisik (BAST / SK) -->
                    <div class="pt-2 border-t border-slate-800/80">
                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1.5 flex items-center justify-between">
                            <span>Unggah Berkas Fisik Berita Acara (Scan BAST Basah / SK Bupati):</span>
                            <span class="text-[9px] font-normal text-slate-400">Opsional (Maks. 10 MB: PDF, JPG, PNG)</span>
                        </label>

                        <div class="p-3.5 rounded-xl bg-slate-950 border-2 border-dashed border-slate-700 hover:border-teal-400/60 transition-all text-center relative group">
                            <input type="file" id="reklas_dokumen_file_input" @change="handleDokumenFile" accept=".pdf,.jpg,.jpeg,.png"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="space-y-1 pointer-events-none">
                                <span class="text-xl">📁</span>
                                <p class="text-xs font-bold text-slate-300 group-hover:text-teal-300 transition-colors">
                                    <span x-show="!selectedDokumenFile">Klik atau seret scan BAST/SK ke sini (Opsional)</span>
                                    <span x-show="selectedDokumenFile" class="text-teal-300 font-mono" x-text="selectedDokumenFile ? selectedDokumenFile.name : ''"></span>
                                </p>
                                <p class="text-[10px] text-slate-500">Format yang didukung: PDF, JPG, JPEG, PNG</p>
                            </div>
                        </div>

                        <div x-show="selectedDokumenFile" class="mt-2 flex items-center justify-between p-2 rounded-lg bg-teal-950/40 border border-teal-500/30 text-xs">
                            <span class="text-teal-200 font-mono truncate">📄 <strong x-text="selectedDokumenFile?.name"></strong> (<span x-text="(selectedDokumenFile?.size / 1024).toFixed(1) + ' KB'"></span>)</span>
                            <button type="button" @click="clearDokumenFile()" class="text-rose-400 hover:text-rose-200 text-xs font-bold ml-2">Batal ✕</button>
                        </div>
                    </div>
                </div>

                <!-- Bagian 4: Checklist Unit Register NIBAR & Pembaruan Kondisi Serah Terima -->
                <template x-if="selectedAstap && selectedAstap.registers && selectedAstap.registers.length > 0">
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-3 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-xs font-bold text-teal-300 uppercase tracking-wider flex items-center gap-1.5">
                                <span>📋</span>
                                <span>Pilih Unit Register &amp; Kondisi Serah Terima Aktual:</span>
                                <span class="text-rose-400">*</span>
                            </span>
                            <div class="flex items-center space-x-2">
                                <span class="text-[10px] font-mono px-2 py-0.5 rounded-lg bg-teal-500/10 text-teal-300 border border-teal-500/20"
                                      x-text="(formData.selected_register_ids ? formData.selected_register_ids.length : 0) + ' Terpilih'"></span>
                                <button type="button" @click="toggleSelectAllReklasNibars(true)"
                                        class="text-[10px] font-bold px-2 py-1 rounded-lg bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/30 transition-all cursor-pointer">
                                    Pilih Semua
                                </button>
                                <button type="button" @click="toggleSelectAllReklasNibars(false)"
                                        class="text-[10px] font-bold px-2 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-slate-200 border border-slate-700 transition-all cursor-pointer">
                                    Batal Pilih
                                </button>
                            </div>
                        </div>

                        <!-- Daftar Unit Register NIBAR (Scrollable) -->
                        <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar pr-1">
                            <template x-for="reg in getReklasAvailableRegisters()" :key="reg.id">
                                <div class="p-2.5 rounded-xl border transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-3"
                                     :class="reg.status === 'Mutasi Keluar OPD'
                                         ? 'bg-slate-950/40 border-slate-900 opacity-60'
                                         : (formData.selected_register_ids.includes(reg.id)
                                             ? 'bg-teal-500/10 border-teal-500/50 shadow-sm shadow-teal-500/10'
                                             : 'bg-slate-900/60 border-slate-800/90 hover:border-slate-700')">
                                    
                                    <div class="flex items-center space-x-3 min-w-0 flex-1">
                                        <template x-if="reg.status !== 'Mutasi Keluar OPD'">
                                            <input type="checkbox" :value="reg.id" x-model="formData.selected_register_ids"
                                                   @change="onSelectedNibarsChange()"
                                                   class="w-4 h-4 rounded border-slate-700 text-teal-500 focus:ring-teal-400/30 bg-slate-950 cursor-pointer">
                                        </template>
                                        <template x-if="reg.status === 'Mutasi Keluar OPD'">
                                            <span class="text-slate-600 cursor-not-allowed text-xs" title="Unit ini sudah dimutasikan keluar">🔒</span>
                                        </template>
                                        
                                        <div class="min-w-0 flex-1">
                                            <div class="flex items-center space-x-2 flex-wrap">
                                                <span class="font-mono font-bold text-xs"
                                                      :class="formData.selected_register_ids.includes(reg.id) ? 'text-teal-300' : 'text-emerald-400'"
                                                      x-text="reg.nibar || reg.no_register || ('Unit #' + reg.id)"></span>
                                                <span class="px-1.5 py-0.2 rounded text-[9px] font-bold border font-mono"
                                                      :class="{
                                                          'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': (reg.kondisi || 'Baik') === 'Baik',
                                                          'bg-amber-500/15 text-amber-300 border-amber-500/30': (reg.kondisi || '').includes('Kurang') || (reg.kondisi || '').includes('Ringan'),
                                                          'bg-rose-500/15 text-rose-300 border-rose-500/30': (reg.kondisi || '').includes('Berat')
                                                      }"
                                                      x-text="'Kondisi Register: ' + (reg.kondisi || 'Baik')"></span>
                                            </div>
                                            <div class="text-[10.5px] flex items-center space-x-1.5 mt-0.5">
                                                <template x-if="reg.status === 'Mutasi Keluar OPD'">
                                                    <span class="text-cyan-400 font-bold">🏛️ Sudah Dimutasi Keluar OPD</span>
                                                </template>
                                                <template x-if="reg.status !== 'Mutasi Keluar OPD' && reg.ruang_pemegang">
                                                    <span class="text-slate-400 truncate">
                                                        <span class="text-slate-500">📍 Ruangan:</span> <span class="text-slate-300" x-text="reg.ruang_pemegang"></span>
                                                    </span>
                                                </template>
                                                <template x-if="reg.status !== 'Mutasi Keluar OPD' && !reg.ruang_pemegang">
                                                    <span class="text-amber-300/90 font-medium text-[10px] px-1.5 py-0.5 rounded bg-amber-500/10 border border-amber-500/25">
                                                        📦 Gudang Aset (Belum Ditempatkan)
                                                    </span>
                                                </template>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Kondisi Serah Terima Fisik & Nilai Satuan -->
                                    <div class="flex items-center justify-between sm:justify-end gap-3 shrink-0 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-800">
                                        <div x-show="formData.selected_register_ids.includes(reg.id)" class="flex items-center gap-1.5">
                                            <label class="text-[9.5px] font-bold text-slate-400 uppercase">Kondisi Serah:</label>
                                            <select x-model="formData.register_kondisi_map[reg.id]"
                                                    class="bg-slate-900 border border-slate-700 text-white rounded-lg px-2 py-1 text-[11px] font-bold focus:border-teal-400 focus:outline-none">
                                                <option value="Baik">Baik</option>
                                                <option value="Kurang Baik">Kurang Baik</option>
                                                <option value="Rusak Berat">Rusak Berat</option>
                                            </select>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <template x-if="reg.status === 'Mutasi Keluar OPD'">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wide bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                                    Sudah Keluar OPD
                                                </span>
                                            </template>
                                            <template x-if="reg.status !== 'Mutasi Keluar OPD'">
                                                <div>
                                                    <span class="text-[9px] text-slate-400 block uppercase font-bold">Nilai:</span>
                                                    <span class="text-xs font-mono font-extrabold text-teal-300"
                                                          x-text="'Rp ' + Number(reg.harga_satuan || (selectedAstap.total_realisasi / Math.max(1, selectedAstap.registers.length))).toLocaleString('id-ID')"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <!-- 3. Klasifikasi KIB Tujuan & Rekening PMDN 108 -->
            <div x-show="formData.jenis_reklas === 'KOREKSI_REKENING' || formData.jenis_reklas === 'KDP_TO_DEFINITIF' || formData.jenis_reklas === 'KAPITALISASI_INTRAKOM'"
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
                        <option value="KEMITRAAN">Kemitraan Pihak Ketiga (1.5.2)</option>
                        <option value="ASET LAIN">Aset Lain-Lain (1.5.4)</option>
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
            <div x-show="formData.tujuan_kib && (formData.jenis_reklas === 'KOREKSI_REKENING' || formData.jenis_reklas === 'KDP_TO_DEFINITIF' || formData.jenis_reklas === 'KAPITALISASI_INTRAKOM')"
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


                <!-- Form Spesifik: KEMITRAAN - Kemitraan Pihak Ketiga (Akun 1.5.2 Sesuai Form Kemitraan Langkah 1) -->
                <template x-if="formData.tujuan_kib === 'KEMITRAAN'">
                    <div class="space-y-4 pt-1">

                        <!-- 2. Nama Perusahaan Mitra / Rekanan Pihak Ketiga (Ketik Filter seperti Form 1 Kemitraan) -->
                        <div class="relative" @click.away="isReklasMitraDropdownOpen = false">
                            <div class="flex items-center justify-between mb-1">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Nama Perusahaan Mitra / Rekanan: <span class="text-rose-400">*</span>
                                </label>
                                <template x-if="masterMitraList && masterMitraList.length > 0">
                                    <span class="text-[9.5px] text-cyan-400 font-mono font-normal flex items-center gap-1 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/20">
                                        <span>⚡</span>
                                        <span>Riwayat Tersimpan</span>
                                    </span>
                                </template>
                            </div>

                            <!-- Input Box dengan Ikon dan Tombol Clear -->
                            <div class="relative">
                                <input type="text" 
                                    x-model="formData.spekBaru.kemitraan_mitra"
                                    @focus="isReklasMitraDropdownOpen = true"
                                    @input="isReklasMitraDropdownOpen = true; onReklasMitraInput($event.target.value)"
                                    @change="onReklasMitraInput($event.target.value)"
                                    @keydown.escape="isReklasMitraDropdownOpen = false"
                                    autocomplete="off"
                                    placeholder="Ketik atau pilih mitra rekanan (contoh: PT. Kimia Farma, PT. Roche...)"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3.5 py-2.5 pl-9 pr-9 text-xs text-white placeholder-slate-500 focus:outline-none font-bold shadow-inner transition-all">

                                <!-- Ikon Mitra Perusahaan -->
                                <svg class="w-4 h-4 text-cyan-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>

                                <!-- Tombol Kosongkan Input (Clear Button) -->
                                <button type="button" 
                                    x-show="formData.spekBaru.kemitraan_mitra"
                                    @click="formData.spekBaru.kemitraan_mitra = ''; isReklasMitraDropdownOpen = true" 
                                    title="Kosongkan nama mitra"
                                    style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                                    class="rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                                    ✕
                                </button>
                            </div>

                            <!-- Floating Dropdown Saran / Filter Mitra (Muncul saat fokus / diketik) -->
                            <div x-show="isReklasMitraDropdownOpen" 
                                x-cloak
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                style="max-height: 220px !important; overflow-y: auto !important;"
                                class="absolute z-50 mt-1.5 w-full bg-slate-900 border border-cyan-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">
                                
                                <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                    <span>Pilih Riwayat / Ketik Mitra Baru</span>
                                    <span class="font-mono text-slate-400" x-text="filteredReklasMitraList.length + ' saran'"></span>
                                </div>

                                <template x-for="(mitra, mIdx) in filteredReklasMitraList" :key="mIdx">
                                    <div @click="selectReklasMitra(mitra)"
                                        class="px-3.5 py-2 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                                        :class="formData.spekBaru.kemitraan_mitra === (mitra.nama || mitra) ? 'bg-cyan-500/20 text-cyan-200' : 'text-slate-200'">
                                        <div class="flex items-center gap-2.5 min-w-0">
                                            <span class="text-xs text-cyan-400/80">🤝</span>
                                            <div class="min-w-0">
                                                <span class="text-xs font-bold group-hover:text-cyan-300 truncate block" x-text="mitra.nama || mitra"></span>
                                                <template x-if="mitra.pimpinan || mitra.alamat">
                                                    <span class="text-[10px] text-slate-400 truncate block font-normal mt-0.5" x-text="(mitra.pimpinan ? 'Pimpinan: ' + mitra.pimpinan : '') + (mitra.pimpinan && mitra.alamat ? ' • ' : '') + (mitra.alamat ? 'Alamat: ' + mitra.alamat : '')"></span>
                                                </template>
                                            </div>
                                        </div>
                                        <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-bold shrink-0 group-hover:bg-cyan-500/25">
                                            Pilih &amp; Auto-fill ↵
                                        </span>
                                    </div>
                                </template>

                                <!-- Notifikasi jika mengetik nama mitra baru -->
                                <template x-if="formData.spekBaru.kemitraan_mitra && filteredReklasMitraList.length === 0">
                                    <div class="p-3 text-center text-xs text-slate-400 bg-slate-950/50">
                                        <span class="text-cyan-300 font-semibold" x-text="'➕ Gunakan Mitra Baru: &quot;' + formData.spekBaru.kemitraan_mitra + '&quot;'"></span>
                                        <p class="text-[10px] text-slate-500 mt-0.5">Nama mitra ini akan otomatis tercatat ke riwayat kemitraan.</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- 3. Pejabat Mitra & Alamat Domisili -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                    Pejabat Mitra / Direktur Rekanan: <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="formData.spekBaru.kemitraan_pimpinan"
                                    placeholder="Nama Direktur / Penanggung Jawab Rekanan"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none placeholder-slate-500 shadow-inner">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                    Alamat Kantor Domisili Mitra:
                                </label>
                                <input type="text" x-model="formData.spekBaru.kemitraan_alamat"
                                    placeholder="Alamat kantor / domisili rekanan mitra"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none placeholder-slate-500 shadow-inner">
                            </div>
                        </div>

                        <!-- 4. Nomor PKS & Tanggal PKS -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                    Nomor Dokumen Perjanjian (PKS / MoU): <span class="text-rose-400">*</span>
                                </label>
                                <input type="text" x-model="formData.spekBaru.kemitraan_perjanjian_no"
                                    placeholder="Contoh: 000.2.3.2/PKS-KSO/430.10.7/2026"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none shadow-inner">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                    Tanggal Penandatanganan PKS: <span class="text-rose-400">*</span>
                                </label>
                                <input type="date" x-model="formData.spekBaru.kemitraan_tanggal_pks"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none shadow-inner">
                            </div>
                        </div>

                        <!-- 5. Masa Berlaku Kerjasama (Tanggal Mulai & Tanggal Selesai) -->
                        <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/90 space-y-2.5">
                            <div class="flex items-center justify-between">
                                <span class="text-[11px] font-bold text-cyan-300 flex items-center gap-1.5">
                                    <span>🗓️</span>
                                    <span>Masa Berlaku Kerja Sama (Konsesi Pemanfaatan)</span>
                                </span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-400 mb-1">
                                        Tanggal Mulai Berlaku: <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="date" x-model="formData.spekBaru.kemitraan_tanggal_mulai"
                                        @input="calcReklasKemitraanDurasi()"
                                        @change="calcReklasKemitraanDurasi()"
                                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-400 mb-1">
                                        Tanggal Berakhir (Konsesi Selesai): <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="date" x-model="formData.spekBaru.kemitraan_tanggal_selesai"
                                        @input="calcReklasKemitraanDurasi()"
                                        @change="calcReklasKemitraanDurasi()"
                                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-semibold text-slate-400 mb-1">
                                    Estimasi Jangka Waktu / Durasi:
                                </label>
                                <input type="text" x-model="formData.spekBaru.kemitraan_jangka_waktu"
                                    placeholder="Contoh: 5 Tahun (2026 s/d 2031)"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: ASET LAIN - Aset Lain-Lain (1.5.4) -->
                <template x-if="formData.tujuan_kib === 'ASET LAIN' || formData.tujuan_kib === 'ASET LAIN-LAIN'">
                    <div class="space-y-3">
                        <div>
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Kondisi Fisik Barang:</label>
                            <select x-model="formData.spekBaru.aset_lain_kondisi"
                                    class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                <option value="Rusak Berat (Menunggu Penghapusan)">Rusak Berat (Menunggu Usulan Penghapusan)</option>
                                <option value="Tidak Dapat Digunakan Lagi">Tidak Dapat Digunakan Lagi / Usang</option>
                                <option value="Hilang / Tidak Ditemukan">Hilang / Tidak Ditemukan</option>
                                <option value="Sengketa / Status Hukum">Sengketa / Status Hukum</option>
                            </select>
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
