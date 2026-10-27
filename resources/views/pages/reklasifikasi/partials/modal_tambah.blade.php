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
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 border border-teal-500/40 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                            Informasi Mutasi Antar-OPD / SKPD Pemerintah Daerah
                        </span>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-teal-500/20 text-teal-300 border border-teal-500/40">
                        Penyeimbang: Baris 42 (Koreksi Lain / Kolom 13)
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-teal-950/25 border border-teal-500/20 text-xs text-teal-200/90 leading-relaxed flex items-start gap-2.5">
                    <div class="p-1 rounded-md bg-teal-500/20 text-teal-300 shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <strong class="text-white block mb-0.5">Pengalihan Aset Keluar RSUD Antar-Perangkat Daerah:</strong>
                        <span>Aset tetap RSUD dialihkan ke Dinas/Badan/Kecamatan lain di lingkungan Pemkab Bondowoso. Aset RSUD berkurang (Mutasi Kurang -), disinkronkan ke Baris 42 Koreksi Lain-Lain (Kolom 13 Mutasi -), serta otomatis tercatat di modul <strong>Mutasi Eksternal Keluar</strong>.</span>
                    </div>
                </div>

                <!-- Bagian 1: Identitas Pihak Pertama (RSUD Dr. H. Koesnadi - Yang Menyerahkan) -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-teal-500/30 shadow-lg shadow-teal-950/20 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-300 shadow-sm shadow-teal-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-black text-teal-300 uppercase tracking-wider block">PIHAK PERTAMA (Yang Menyerahkan - RSUD Dr. H. Koesnadi)</span>
                                <span class="text-[10px] text-slate-400">Instansi Penyerah Barang Milik Daerah</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/30 font-bold tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                            Internal RSUD
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 text-xs items-start">
                        <!-- Nama Pejabat Penyerah RSUD -->
                        <div class="relative space-y-1.5 min-w-0" @click.away="isPejabatAsalDropdownOpen = false">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span class="truncate" title="Nama Pejabat Penyerah RSUD">Nama Pejabat Penyerah RSUD</span>
                                <span class="text-rose-400 font-bold shrink-0">*</span>
                            </label>
                            <div class="relative">
                                <input type="text" x-model="formData.pj_asal_nama"
                                       @focus="isPejabatAsalDropdownOpen = true"
                                       placeholder="Nama Pejabat RSUD (BUDI HARTONO, S.Sos / dr. Yus...)"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-teal-400 focus:ring-1 focus:ring-teal-400/30 rounded-xl px-3.5 pr-9 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">
                                <button type="button" @click="isPejabatAsalDropdownOpen = !isPejabatAsalDropdownOpen"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-teal-300 rounded-lg hover:bg-slate-800 transition-colors"
                                        title="Buka pilihan pejabat">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="isPejabatAsalDropdownOpen ? 'rotate-180 text-teal-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                            </div>

                            <!-- Dropdown Riwayat Pejabat RSUD -->
                            <div x-show="isPejabatAsalDropdownOpen" x-cloak
                                 class="absolute z-40 mt-1 w-full bg-slate-900 border border-teal-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-48 overflow-y-auto">
                                <div class="px-3 py-1.5 bg-slate-950 text-[10px] font-bold text-teal-400 uppercase tracking-wider">
                                    Pilihan Pejabat Penyerah RSUD
                                </div>
                                <div @click="selectPejabatAsal({ nama: 'BUDI HARTONO, S.Sos', nip: '19760229 200801 1 010', jabatan: 'Pengurus Barang Pengguna' })"
                                     class="p-2.5 hover:bg-teal-500/15 cursor-pointer text-left transition-colors">
                                    <div class="font-bold text-white text-xs">BUDI HARTONO, S.Sos</div>
                                    <div class="text-[10px] text-teal-400 font-mono mt-0.5">NIP: 19760229 200801 1 010 · Pengurus Barang Pengguna</div>
                                </div>
                                <div @click="selectPejabatAsal({ nama: 'dr. H. Yus Priyatna, Sp.P', nip: '196904121999031004', jabatan: 'Direktur RSUD Dr. H. Koesnadi' })"
                                     class="p-2.5 hover:bg-teal-500/15 cursor-pointer text-left transition-colors">
                                    <div class="font-bold text-white text-xs">dr. H. Yus Priyatna, Sp.P</div>
                                    <div class="text-[10px] text-teal-400 font-mono mt-0.5">NIP: 196904121999031004 · Direktur RSUD Dr. H. Koesnadi</div>
                                </div>
                                <template x-for="p in (dbPejabats || [])" :key="p.nama">
                                    <div x-show="p.nama !== 'BUDI HARTONO, S.Sos' && p.nama !== 'dr. H. Yus Priyatna, Sp.P'"
                                         @click="selectPejabatAsal({ nama: p.nama, nip: p.nip || '-', jabatan: 'Pengurus Barang / PPK RSUD' })"
                                         class="p-2.5 hover:bg-teal-500/15 cursor-pointer text-left transition-colors">
                                        <div class="font-bold text-white text-xs" x-text="p.nama"></div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="'NIP: ' + (p.nip || '-') + ' · ' + (p.jabatan || 'Pengurus Barang / PPK RSUD')"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- NIP Pejabat Penyerah RSUD -->
                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                                <span class="truncate" title="NIP Pejabat Penyerah RSUD">NIP Pejabat Penyerah RSUD</span>
                            </label>
                            <input type="text" x-model="formData.pj_asal_nip"
                                   placeholder="19xxxxxxxxxxxxxx"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-teal-400 focus:ring-1 focus:ring-teal-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-mono tracking-wider transition-all shadow-inner">
                        </div>

                        <!-- Jabatan Penyerah RSUD -->
                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                <span class="truncate" title="Jabatan Penyerah RSUD">Jabatan Penyerah RSUD</span>
                            </label>
                            <input type="text" x-model="formData.pj_asal_jabatan"
                                   placeholder="Pengurus Barang Pengguna"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-teal-400 focus:ring-1 focus:ring-teal-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-semibold transition-all shadow-inner">
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Identitas Pihak Kedua (SKPD / OPD Penerima) -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-cyan-500/30 shadow-lg shadow-cyan-950/20 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-cyan-300 shadow-sm shadow-cyan-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-black text-cyan-300 uppercase tracking-wider block">PIHAK KEDUA (Yang Menerima - SKPD / OPD Tujuan)</span>
                                <span class="text-[10px] text-slate-400">Instansi Pemerintah Daerah Penerima Aset</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 font-bold tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                            Eksternal Pemkab
                        </span>
                    </div>

                    <!-- Combobox Autocomplete SKPD Penerima -->
                    <div class="relative space-y-1" @click.away="isSkpdDropdownOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                <span>SKPD / OPD Penerima BMD</span>
                                <span class="text-rose-400 font-bold">*</span>
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
                                   class="w-full bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 py-2.5 pl-9 pr-9 text-xs text-white placeholder-slate-500 focus:outline-none font-bold shadow-inner transition-all">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-cyan-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                            </div>
                            <button type="button" x-show="formData.skpd_tujuan"
                                    @click="formData.skpd_tujuan = ''; isSkpdDropdownOpen = true"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-rose-300 rounded-lg hover:bg-slate-800 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Dropdown List SKPD -->
                        <div x-show="isSkpdDropdownOpen && filteredSkpdList.length > 0" x-cloak
                             class="absolute z-40 mt-1 w-full bg-slate-900 border border-cyan-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-56 overflow-y-auto">
                            <div class="px-3 py-1.5 bg-slate-950 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between">
                                <span>Pilih Direktori SKPD Pemkab</span>
                                <span class="font-mono text-slate-400" x-text="filteredSkpdList.length + ' hasil'"></span>
                            </div>
                            <template x-for="(skpd, sIdx) in filteredSkpdList" :key="sIdx">
                                <div @click="selectSkpd(skpd)"
                                     class="p-2.5 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2.5 text-left"
                                     :class="formData.skpd_tujuan === skpd ? 'bg-cyan-500/20 text-cyan-200' : 'text-slate-200'">
                                    <div class="min-w-0">
                                        <div class="font-bold text-xs group-hover:text-cyan-300 truncate" x-text="skpd"></div>
                                        <div class="text-[10px] text-slate-400 truncate flex items-center gap-1 mt-0.5" x-show="skpdDirectory[skpd]?.alamat">
                                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            <span x-text="skpdDirectory[skpd]?.alamat"></span>
                                        </div>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-bold shrink-0">
                                        Pilih ↵
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Pejabat Penerima OPD (Grid 3 Kolom Sama Lebar) -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3.5 text-xs items-start">
                        <!-- Nama Pejabat Penerima OPD -->
                        <div class="relative space-y-1.5 min-w-0" @click.away="isPejabatTujuanDropdownOpen = false">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                <span class="truncate" title="Nama Pejabat Penerima OPD">Nama Pejabat Penerima OPD</span>
                            </label>
                            <div class="relative">
                                <input type="text" x-model="formData.pejabat_opd_tujuan"
                                       @focus="isPejabatTujuanDropdownOpen = true"
                                       placeholder="Nama Pejabat / Pengurus Barang"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 pr-9 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">
                                <button type="button" x-show="availablePejabatTujuanList.length > 0"
                                        @click="isPejabatTujuanDropdownOpen = !isPejabatTujuanDropdownOpen"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-cyan-300 rounded-lg hover:bg-slate-800 transition-colors"
                                        title="Buka riwayat pejabat OPD">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="isPejabatTujuanDropdownOpen ? 'rotate-180 text-cyan-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                            </div>

                            <!-- Dropdown Riwayat Pejabat Tujuan -->
                            <div x-show="isPejabatTujuanDropdownOpen && filteredPejabatTujuanList.length > 0" x-cloak
                                 class="absolute z-40 mt-1 w-full bg-slate-900 border border-cyan-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-44 overflow-y-auto">
                                <div class="px-3 py-1 bg-slate-950 text-[10px] font-bold text-cyan-400 uppercase tracking-wider">
                                    Riwayat Pejabat SKPD Terpilih
                                </div>
                                <template x-for="(p, pIdx) in filteredPejabatTujuanList" :key="pIdx">
                                    <div @click="selectPejabatTujuan(p)"
                                         class="p-2.5 hover:bg-cyan-500/15 cursor-pointer text-left transition-colors">
                                        <div class="font-bold text-white text-xs" x-text="p.nama"></div>
                                        <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="'NIP: ' + (p.nip || '-') + ' · ' + (p.jabatan || '')"></div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- NIP Pejabat Penerima OPD -->
                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                                <span class="truncate" title="NIP Pejabat Penerima OPD">NIP Pejabat Penerima OPD</span>
                            </label>
                            <input type="text" x-model="formData.nip_pejabat_opd_tujuan"
                                   placeholder="19xxxxxxxxxxxxxx atau -"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-mono tracking-wider transition-all shadow-inner">
                        </div>

                        <!-- Jabatan Pejabat Penerima -->
                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                <span class="truncate" title="Jabatan Pejabat Penerima">Jabatan Pejabat Penerima</span>
                            </label>
                            <input type="text" x-model="formData.jabatan_opd_tujuan"
                                   placeholder="Pengurus Barang / Kepala Bidang"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-semibold transition-all shadow-inner">
                        </div>
                    </div>

                    <!-- Alamat SKPD Penerima (12 Kolom Penuh) -->
                    <div class="space-y-1">
                        <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>Alamat Kantor SKPD / Instansi Penerima</span>
                        </label>
                        <div class="relative">
                            <input type="text" x-model="formData.alamat_instansi"
                                   placeholder="Jl. Letnan Karsono No. 2, Bondowoso"
                                   class="w-full bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 py-2.5 pl-9 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: Dokumen Legalitas BAST, Validasi Tanggal & Unggah Berkas Fisik -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-amber-500/30 shadow-lg shadow-amber-950/20 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-300 shadow-sm shadow-amber-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-black text-white uppercase tracking-wider block">Legalitas Berita Acara &amp; Dokumen Lampiran</span>
                                <span class="text-[10px] text-slate-400 font-mono">BAST &amp; SK Bupati Mutasi Aset</span>
                            </div>
                        </div>
                        <span class="text-[10px] px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-mono">
                            Dokumen Sah
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 text-xs items-start">
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                <span class="truncate" title="Nomor BAST Mutasi Eksternal">Nomor BAST Mutasi Eksternal</span>
                                <span class="text-rose-400 font-bold shrink-0">*</span>
                            </label>
                            <input type="text" x-model="formData.nomor_ba_reklas"
                                   placeholder="000.2.3.2/.../430.10.7/2026"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-mono tracking-wider transition-all shadow-inner">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between h-5 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider min-w-0">
                                    <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="truncate" title="Tanggal BAST Mutasi">Tanggal BAST Mutasi</span>
                                    <span class="text-rose-400 font-bold shrink-0">*</span>
                                </label>
                                <span class="text-[9px] text-teal-300 font-mono shrink-0 ml-1">Maks: Hari Ini</span>
                            </div>
                            <input type="date" x-model="formData.tanggal_bast"
                                   :max="new Date().toISOString().split('T')[0]"
                                   :class="isTanggalBastInvalid() ? 'border-rose-500 bg-rose-950/20 text-rose-200' : 'border-slate-700/80 bg-slate-950 text-white'"
                                   class="w-full h-10 border focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3.5 text-xs focus:outline-none font-mono transition-all shadow-inner">
                            <template x-if="isTanggalBastInvalid()">
                                <p class="text-[10px] text-rose-400 font-semibold mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    <span>Tanggal dokumen BAST tidak boleh melebihi tanggal hari ini!</span>
                                </p>
                            </template>
                        </div>

                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                <span class="truncate" title="Nomor SK Dasar / Mutasi">Nomor SK Dasar / Mutasi</span>
                            </label>
                            <input type="text" x-model="formData.nomor_sk_dasar"
                                   placeholder="SK Bupati / Nomor BAST Transfer BMD"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-mono tracking-wider transition-all shadow-inner">
                        </div>
                    </div>

                    <!-- Dropzone Unggah Berkas Fisik (BAST / SK) -->
                    <div class="pt-3 border-t border-slate-800/80">
                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                <span>Unggah Berkas Fisik Berita Acara (Scan BAST Basah / SK Bupati):</span>
                            </span>
                            <span class="text-[9px] font-normal text-slate-400">Opsional (Maks. 10 MB: PDF, JPG, PNG)</span>
                        </label>

                        <div class="p-4 rounded-xl bg-slate-950 border-2 border-dashed border-slate-700 hover:border-teal-400/60 transition-all text-center relative group">
                            <input type="file" id="reklas_dokumen_file_input" @change="handleDokumenFile" accept=".pdf,.jpg,.jpeg,.png"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="space-y-1.5 pointer-events-none">
                                <div class="w-9 h-9 mx-auto rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                </div>
                                <p class="text-xs font-bold text-slate-300 group-hover:text-teal-300 transition-colors">
                                    <span x-show="!selectedDokumenFile">Klik atau seret scan BAST/SK ke sini (Opsional)</span>
                                    <span x-show="selectedDokumenFile" class="text-teal-300 font-mono" x-text="selectedDokumenFile ? selectedDokumenFile.name : ''"></span>
                                </p>
                                <p class="text-[10px] text-slate-500">Format yang didukung: PDF, JPG, JPEG, PNG</p>
                            </div>
                        </div>

                        <div x-show="selectedDokumenFile" class="mt-2.5 flex items-center justify-between p-2.5 rounded-xl bg-teal-950/40 border border-teal-500/30 text-xs">
                            <span class="text-teal-200 font-mono truncate flex items-center gap-2">
                                <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <strong x-text="selectedDokumenFile?.name"></strong>
                                <span class="text-slate-400" x-text="'(' + (selectedDokumenFile?.size / 1024).toFixed(1) + ' KB)'"></span>
                            </span>
                            <button type="button" @click="clearDokumenFile()" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 text-xs font-bold transition-all ml-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                <span>Batal</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bagian 4: Checklist Unit Register NIBAR & Pembaruan Kondisi Serah Terima -->
                <template x-if="selectedAstap && selectedAstap.registers && selectedAstap.registers.length > 0">
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-3 shadow-inner">
                        <div class="flex items-center justify-between border-b border-slate-800/80 pb-2">
                            <span class="text-xs font-bold text-teal-300 uppercase tracking-wider flex items-center gap-2">
                                <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
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

                <!-- Banner Umpan Balik Cerdas: Deteksi Kesamaan Kelompok Fisik -->
                <template x-if="isReklasSamePhysicalType()">
                    <div class="p-3.5 rounded-2xl bg-emerald-950/40 border border-emerald-500/40 text-emerald-200 text-xs leading-relaxed flex items-start gap-3 shadow-lg shadow-emerald-950/30">
                        <div class="w-7 h-7 rounded-xl bg-emerald-500/20 border border-emerald-500/40 flex items-center justify-center shrink-0 text-emerald-300 font-extrabold text-sm mt-0.5 shadow-inner">
                            ✓
                        </div>
                        <div class="space-y-1 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-extrabold text-white text-xs uppercase tracking-wide">
                                    Wujud Fisik Sama (<span class="text-emerald-300 font-mono" x-text="getPhysicalTypeLabel(getTargetPhysicalType()) || formData.tujuan_kib"></span>)
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm animate-pulse">
                                    ✨ Spesifikasi Otomatis Dipertahankan (Tidak Perlu Isi Ulang)
                                </span>
                            </div>
                            <p class="text-emerald-200/90 text-[11px] leading-relaxed">
                                Aset tetap berada dalam kategori wujud fisik yang sama. Seluruh rincian spesifikasi sebelumnya (luas, nomor &amp; tanggal dokumen legalitas, jenis konstruksi, status tanah, serta lokasi) telah <strong>disalin otomatis</strong> ke formulir di bawah. Anda tidak perlu repot mengetik ulang, silakan langsung lanjutkan atau sesuaikan jika ada perubahan kecil.
                            </p>
                        </div>
                    </div>
                </template>

                <template x-if="!isReklasSamePhysicalType()">
                    <div class="p-3.5 rounded-2xl bg-amber-950/40 border border-amber-500/40 text-amber-200 text-xs leading-relaxed flex items-start gap-3 shadow-lg shadow-amber-950/30">
                        <div class="w-7 h-7 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center shrink-0 text-amber-300 font-extrabold text-sm mt-0.5 shadow-inner">
                            ⚠️
                        </div>
                        <div class="space-y-1 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-extrabold text-white text-xs uppercase tracking-wide">
                                    Perubahan Kelompok Fisik (<span class="text-slate-300" x-text="getPhysicalTypeLabel(getSourcePhysicalType()) || 'Asal'"></span> ➔ <span class="text-amber-300 font-mono" x-text="getPhysicalTypeLabel(getTargetPhysicalType()) || formData.tujuan_kib"></span>)
                                </span>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-sm">
                                    Wajib Isi Spesifikasi Baru
                                </span>
                            </div>
                            <p class="text-amber-200/90 text-[11px] leading-relaxed">
                                Aset dialihkan ke kelompok fisik yang berbeda, sehingga atribut fisik lama diarsipkan ke riwayat audit. Silakan lengkapi formulir spesifikasi teknis baru di bawah ini agar pencatatan buku inventaris &amp; KIR terbit sesuai format fisik tujuan.
                            </p>
                        </div>
                    </div>
                </template>

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
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1 flex items-center justify-between">
                                    <span>Status Bangunan:</span>
                                    <span class="text-amber-400 font-mono text-[9.5px]" x-text="formData.spekBaru.gedung_is_baru === 'Lama' ? '🏛️ Bangunan Lama' : '✨ Bangunan Baru'"></span>
                                </label>
                                <select x-model="formData.spekBaru.gedung_is_baru"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Lama">🏛️ Bangunan Lama (Eksisting / Pasca-Kemitraan)</option>
                                    <option value="Baru">✨ Bangunan Baru (Pembangunan Perdana)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Bertingkat:</label>
                                <select x-model="formData.spekBaru.gedung_konstruksi_bertingkat"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Bertingkat">Bertingkat (2 Lantai atau lebih)</option>
                                    <option value="Tidak Bertingkat">Tidak Bertingkat (1 Lantai)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Beton:</label>
                                <select x-model="formData.spekBaru.gedung_konstruksi_beton"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Beton">Beton</option>
                                    <option value="Tidak">Tidak</option>
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
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1 flex items-center justify-between">
                                    <span>Status Jaringan:</span>
                                    <span class="text-teal-400 font-mono text-[9.5px]" x-text="formData.spekBaru.jaringan_is_baru === 'Lama' ? '🌐 Jaringan Lama' : '✨ Jaringan Baru'"></span>
                                </label>
                                <select x-model="formData.spekBaru.jaringan_is_baru"
                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    <option value="Lama">🌐 Jaringan / Ruas Lama (Eksisting / Pasca-Kemitraan)</option>
                                    <option value="Baru">✨ Jaringan Baru (Pemasangan / Pembangunan Perdana)</option>
                                </select>
                            </div>
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
                    <div class="space-y-4">
                        <!-- Header Card KIB E -->
                        <div class="flex items-center justify-between pb-1 border-b border-slate-800/80">
                            <div class="flex items-center space-x-2">
                                <span class="w-7 h-7 rounded-lg bg-purple-500/20 text-purple-300 flex items-center justify-center text-sm border border-purple-500/40">📦</span>
                                <div>
                                    <span class="text-[11px] font-extrabold text-purple-300 uppercase tracking-wider">
                                        Spesifikasi Aset Tetap Lainnya (KIB E):
                                    </span>
                                    <p class="text-[10px] text-slate-400">Buku Perpustakaan Medis, Kesenian/Budaya &amp; Ornamen, atau Hewan &amp; Tanaman RSUD.</p>
                                </div>
                            </div>
                            <span class="text-[9.5px] px-2.5 py-0.5 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30 font-bold font-mono">
                                KIB E &bull; Akun 1.3.5
                            </span>
                        </div>

                        <!-- Selector Sub-Kategori KIB E (Interactive Pill Buttons) -->
                        <div class="space-y-1.5">
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider flex items-center justify-between">
                                <span>PILIH SUB-KATEGORI KIB E:</span>
                                <span class="text-[9px] text-purple-400 font-mono" x-text="'Aktif: ' + ((formData.spekBaru.kib_e_type === 'kesenian') ? 'Kesenian (1.3.5.02)' : ((formData.spekBaru.kib_e_type === 'hewan_tumbuhan') ? 'Hewan/Tanaman (1.3.5.03)' : 'Buku Perpustakaan (1.3.5.01)'))"></span>
                            </label>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                <button type="button" @click="formData.spekBaru.kib_e_type = 'buku'"
                                        :class="(formData.spekBaru.kib_e_type || 'buku') === 'buku' ? 'border-amber-500/80 bg-amber-950/40 text-amber-200 ring-1 ring-amber-500/50 shadow-sm' : 'border-slate-800 bg-slate-950/60 text-slate-400 hover:text-slate-200'"
                                        class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">📚</span>
                                        <div>
                                            <div class="text-xs font-bold">Buku Perpustakaan</div>
                                            <div class="text-[9px] font-mono text-slate-400">Kode 1.3.5.01</div>
                                        </div>
                                    </div>
                                    <span x-show="(formData.spekBaru.kib_e_type || 'buku') === 'buku'" class="text-amber-400 font-bold text-xs">✓</span>
                                </button>
                                <button type="button" @click="formData.spekBaru.kib_e_type = 'kesenian'"
                                        :class="formData.spekBaru.kib_e_type === 'kesenian' ? 'border-purple-500/80 bg-purple-950/40 text-purple-200 ring-1 ring-purple-500/50 shadow-sm' : 'border-slate-800 bg-slate-950/60 text-slate-400 hover:text-slate-200'"
                                        class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">🎨</span>
                                        <div>
                                            <div class="text-xs font-bold">Kesenian &amp; Budaya</div>
                                            <div class="text-[9px] font-mono text-slate-400">Kode 1.3.5.02</div>
                                        </div>
                                    </div>
                                    <span x-show="formData.spekBaru.kib_e_type === 'kesenian'" class="text-purple-400 font-bold text-xs">✓</span>
                                </button>
                                <button type="button" @click="formData.spekBaru.kib_e_type = 'hewan_tumbuhan'"
                                        :class="formData.spekBaru.kib_e_type === 'hewan_tumbuhan' ? 'border-emerald-500/80 bg-emerald-950/40 text-emerald-200 ring-1 ring-emerald-500/50 shadow-sm' : 'border-slate-800 bg-slate-950/60 text-slate-400 hover:text-slate-200'"
                                        class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-left">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-base">🌿</span>
                                        <div>
                                            <div class="text-xs font-bold">Hewan &amp; Tanaman</div>
                                            <div class="text-[9px] font-mono text-slate-400">Kode 1.3.5.03</div>
                                        </div>
                                    </div>
                                    <span x-show="formData.spekBaru.kib_e_type === 'hewan_tumbuhan'" class="text-emerald-400 font-bold text-xs">✓</span>
                                </button>
                            </div>
                        </div>

                        <!-- 1. Form Sub-Kategori: Buku Perpustakaan / Literatur Medis -->
                        <div x-show="(formData.spekBaru.kib_e_type || 'buku') === 'buku'" class="p-3.5 rounded-2xl bg-slate-950/70 border border-amber-500/30 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                                <span class="text-[10.5px] font-extrabold text-amber-300 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>📚</span>
                                    <span>Atribut Buku Perpustakaan / Literatur Medis:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">KIB E Buku</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                        Judul Buku / Nama Literatur <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_judul"
                                           @input="formData.spekBaru.lainnya_nama_barang = formData.spekBaru.lainnya_judul; formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Pedoman Standar Pelayanan Klinis & Formularium RSUD..."
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Pengarang / Pencipta / Penerbit:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_pencipta"
                                           @input="formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Tim Komite Medik RSUD / Penerbit EGC"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Spesifikasi / Edisi / Cetakan:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi"
                                           placeholder="Contoh: Edisi Revisi 2026 / Cetakan Lux Hardcover"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Bahan Pembuatan / Cover:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_bahan"
                                           placeholder="Contoh: Kertas Art Paper / Hardcover / HVS"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Kondisi Fisik Buku:</label>
                                    <select x-model="formData.spekBaru.kondisi"
                                            @change="if (formData.spekBaru) { formData.spekBaru.lainnya_kondisi = formData.spekBaru.kondisi; }"
                                            class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white font-bold focus:outline-none transition-all cursor-pointer">
                                        <option value="Baik">🟢 Baik (B) — Lengkap & Layak Baca</option>
                                        <option value="Kurang Baik">🟡 Kurang Baik (KB) — Sebagian Rusak/Sobek</option>
                                        <option value="Rusak Berat">🔴 Rusak Berat (RB) — Rusak Total/Lapuk</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 2. Form Sub-Kategori: Kesenian & Budaya / Ornamen -->
                        <div x-show="formData.spekBaru.kib_e_type === 'kesenian'" class="p-3.5 rounded-2xl bg-slate-950/70 border border-purple-500/30 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                                <span class="text-[10.5px] font-extrabold text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🎨</span>
                                    <span>Atribut Benda Seni, Budaya &amp; Ornamen:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20 font-bold">KIB E Kesenian</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                        Judul Karya Seni / Nama Barang <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_judul"
                                           @input="formData.spekBaru.lainnya_nama_barang = formData.spekBaru.lainnya_judul; formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Lukisan Sejarah Pelayanan RSUD Bondowoso / Patung Ornamen Medis"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Pencipta / Seniman:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_pencipta"
                                           @input="formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Sanggar Seni Rupa Bondowoso"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Asal Daerah / Wilayah:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_asal_daerah"
                                           placeholder="Contoh: Bondowoso, Jawa Timur"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Bahan Pembuatan:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_bahan"
                                           placeholder="Contoh: Kanvas Cat Minyak / Logam Perunggu / Kayu Jati"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Ukuran / Dimensi:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_ukuran"
                                           placeholder="Contoh: 150 x 200 cm / Tinggi 1.8 Meter"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Spesifikasi / Deskripsi Karya:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi"
                                           placeholder="Contoh: Pigura ukir kayu mahoni gold, terpajang di lobi utama RSUD"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-purple-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>

                        <!-- 3. Form Sub-Kategori: Hewan & Tanaman -->
                        <div x-show="formData.spekBaru.kib_e_type === 'hewan_tumbuhan'" class="p-3.5 rounded-2xl bg-slate-950/70 border border-emerald-500/30 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800/80 pb-1.5">
                                <span class="text-[10.5px] font-extrabold text-emerald-300 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>🌿</span>
                                    <span>Atribut Hewan, Ternak &amp; Tanaman:</span>
                                </span>
                                <span class="text-[9px] px-2 py-0.5 rounded bg-emerald-500/10 text-emerald-300 border border-emerald-500/20 font-bold">KIB E Flora/Fauna</span>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                        Jenis Hewan / Tanaman <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_judul"
                                           @input="formData.spekBaru.lainnya_nama_barang = formData.spekBaru.lainnya_judul; formData.spekBaru.lainnya_judul_pencipta = (formData.spekBaru.lainnya_judul || '') + (formData.spekBaru.lainnya_pencipta ? ' - ' + formData.spekBaru.lainnya_pencipta : '');"
                                           placeholder="Contoh: Pohon Ketapang Kencana / Palem Raja / Tanaman Hias Lanskap"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Ukuran / Tinggi / Usia:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_ukuran"
                                           placeholder="Contoh: Tinggi 3.5 Meter / Usia 2 Tahun"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Asal Usul / Bibit:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_asal_daerah"
                                           placeholder="Contoh: Balai Pembibitan Pertanian Jawa Timur"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                                <div class="sm:col-span-2">
                                    <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Keterangan / Lokasi Penghijauan:</label>
                                    <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi"
                                           placeholder="Contoh: Penghijauan Taman Lobi Rawat Jalan RSUD Dr. H. Koesnandi"
                                           class="w-full bg-slate-900 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Form Spesifik: ATB - Aset Tak Berwujud -->
                <template x-if="formData.tujuan_kib === 'ATB'">
                    <div class="space-y-3">
                        <!-- Judul / Nama Software & Lisensi -->
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul / Nama Software &amp; Lisensi</label>
                            <input type="text" 
                                   x-model="formData.spekBaru.atb_judul_nama" 
                                   @input="formData.spekBaru.atb_nama_software = formData.spekBaru.atb_judul_nama;"
                                   placeholder="Aplikasi SIMAT-RK (Sistem Informasi Manajemen Aset)..."
                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                        </div>

                        <!-- Pencipta / Vendor / Pengembang -->
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Vendor / Pengembang</label>
                            <input type="text" 
                                   x-model="formData.spekBaru.atb_pencipta" 
                                   @input="formData.spekBaru.atb_pengembang = formData.spekBaru.atb_pencipta;"
                                   placeholder="Tim IT SIMRS RSUD & Pengembang Sistem"
                                   class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                        </div>

                        <!-- Spesifikasi Software / Hak Cipta -->
                        <div>
                            <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi Software / Hak Cipta</label>
                            <textarea rows="2" 
                                      x-model="formData.spekBaru.atb_spesifikasi" 
                                      placeholder="Web-Based, Multi-Role Access, Integrasi SatuSehat & RME..."
                                      class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-cyan-300 focus:outline-none focus:border-cyan-500 resize-none"></textarea>
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
                                <div class="flex items-center gap-1.5">
                                    <template x-if="formData.spekBaru.kemitraan_mitra && filteredReklasMitraList.length === 0">
                                        <span class="text-[9.5px] text-emerald-400 font-mono font-bold flex items-center gap-1 bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/20">
                                            <span>➕</span>
                                            <span>Mitra Baru</span>
                                        </span>
                                    </template>
                                    <template x-if="masterMitraList && masterMitraList.length > 0">
                                        <span class="text-[9.5px] text-cyan-400 font-mono font-normal flex items-center gap-1 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/20">
                                            <span>⚡</span>
                                            <span>Riwayat Tersimpan</span>
                                        </span>
                                    </template>
                                </div>
                            </div>

                            <!-- Input Box dengan Ikon dan Tombol Clear -->
                            <div class="relative">
                                <input type="text" 
                                    x-model="formData.spekBaru.kemitraan_mitra"
                                    @focus="isReklasMitraDropdownOpen = true"
                                    @input="isReklasMitraDropdownOpen = true; onReklasMitraInput($event.target.value)"
                                    @change="onReklasMitraInput($event.target.value)"
                                    @keydown.escape="isReklasMitraDropdownOpen = false"
                                    @keydown.enter.prevent="isReklasMitraDropdownOpen = false"
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

                            <!-- Floating Dropdown Saran / Filter Mitra (Hanya Tampil Jika Ada Saran yang Cocok) -->
                            <div x-show="isReklasMitraDropdownOpen && filteredReklasMitraList.length > 0" 
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

                        <!-- 3. Skema Pemanfaatan Kemitraan (Otomatis Terkunci dari Rekening 108) -->
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1.5">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Bentuk Skema Pemanfaatan (Permendagri 108): <span class="text-rose-400">*</span>
                                </label>
                                <span class="text-[9.5px] font-mono font-bold flex items-center gap-1 text-cyan-400 bg-cyan-500/10 border border-cyan-500/25 px-2 py-0.5 rounded-md">
                                    <span>🔒</span>
                                    <span>Otomatis Sesuai Akun 108</span>
                                </span>
                            </div>
                            <input type="hidden" x-model="formData.spekBaru.kemitraan_skema">
                            <div class="w-full bg-slate-950/90 border border-cyan-500/30 rounded-xl p-2.5 flex items-center justify-between gap-3 shadow-inner">
                                <div class="flex items-center gap-2.5 min-w-0">
                                    <div class="w-8 h-8 rounded-lg bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-sm shrink-0"
                                         x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? '💰' : (formData.spekBaru.kemitraan_skema === 'BGS/BSG' ? '🏗️' : (formData.spekBaru.kemitraan_skema === 'KSPI' ? '⚡' : '🤝'))">
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-extrabold text-white truncate" 
                                             x-text="formData.spekBaru.kemitraan_skema || 'Sewa'"></div>
                                        <div class="text-[10px] text-cyan-300/80 font-mono truncate"
                                             x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? 'Akun 1.5.2.01.01.01 — Sewa BMD' : (formData.spekBaru.kemitraan_skema === 'BGS/BSG' ? 'Akun 1.5.2.01.01.03 — Bangun Guna Serah / BSG' : (formData.spekBaru.kemitraan_skema === 'KSPI' ? 'Akun 1.5.2.01.01.04 — KSPI' : 'Akun 1.5.2.01.01.02 — Kerja Sama Pemanfaatan (KP)'))">
                                        </div>
                                    </div>
                                </div>
                                <span class="shrink-0 text-[10px] font-bold text-slate-400 bg-slate-900 border border-slate-800 px-2.5 py-1 rounded-lg flex items-center gap-1">
                                    <span>✓</span>
                                    <span>Terpilih di Atas</span>
                                </span>
                            </div>
                            <p class="text-[9.5px] text-slate-500 mt-1">
                                Skema pemanfaatan otomatis disinkronkan dan terkunci dari Sub-Rincian Rekening 108 yang Anda pilih pada langkah di atas.
                            </p>
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
                                        @input="onReklasKemitraanTanggalMulaiChange()"
                                        @change="onReklasKemitraanTanggalMulaiChange()"
                                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-semibold text-slate-400 mb-1">
                                        Tanggal Berakhir (Konsesi Selesai): <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="date" x-model="formData.spekBaru.kemitraan_tanggal_selesai"
                                        :min="formData.spekBaru.kemitraan_tanggal_mulai"
                                        @input="calcReklasKemitraanDurasi()"
                                        @change="calcReklasKemitraanDurasi()"
                                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="text-[10px] font-semibold text-slate-400">
                                        Estimasi Jangka Waktu / Durasi:
                                    </label>
                                    <span class="text-[9.5px] text-cyan-400 font-mono">⚡ Ketik 2 Tahun dkk, tanggal berakhir otomatis menyesuaikan</span>
                                </div>
                                <input type="text" x-model="formData.spekBaru.kemitraan_jangka_waktu"
                                    @input="onReklasKemitraanDurasiInput()"
                                    @change="onReklasKemitraanDurasiInput()"
                                    placeholder="Contoh: 2 Tahun / 6 Bulan / 90 Hari"
                                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-medium">
                                <!-- Shortcut Preset Buttons -->
                                <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                    <span class="text-[9.5px] text-slate-500 font-medium mr-0.5">⚡ Pilihan Cepat:</span>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(6, 'bulan')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +6 Bulan
                                    </button>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(1, 'tahun')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +1 Tahun
                                    </button>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(2, 'tahun')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +2 Tahun
                                    </button>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(3, 'tahun')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +3 Tahun
                                    </button>
                                    <button type="button" @click="setReklasKemitraanDurasiQuick(5, 'tahun')"
                                        class="px-2 py-0.5 rounded-lg bg-slate-900 hover:bg-cyan-500/20 text-slate-300 hover:text-cyan-300 border border-slate-700/80 hover:border-cyan-500/40 text-[10px] font-semibold transition-all cursor-pointer active:scale-95">
                                        +5 Tahun
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Tarif / Nilai Finansial Sewa & Kontribusi Pemanfaatan -->
                        <div class="p-3.5 rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 border border-cyan-500/30 space-y-3 shadow-md">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <span class="text-[11px] font-bold text-cyan-300 flex items-center gap-1.5">
                                    <span x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? '💰' : '🤝'"></span>
                                    <span x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? 'Tarif / Nilai Harga Sewa Pemanfaatan BMD' : 'Nilai Kontribusi / Kerja Sama Pemanfaatan'"></span>
                                </span>
                                <span class="text-[9.5px] font-mono px-2 py-0.5 rounded-md bg-cyan-500/10 text-cyan-300 border border-cyan-500/20"
                                      x-text="formData.spekBaru.kemitraan_skema === 'Sewa' ? 'Pendapatan Sewa BMD' : 'Bagi Hasil / Kontribusi'"></span>
                            </div>

                            <!-- KONDISI A: JIKA SKEMA SEWA (S) -->
                            <div x-show="formData.spekBaru.kemitraan_skema === 'Sewa'" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-300 mb-1">
                                            Harga / Tarif Sewa (Rp): <span class="text-rose-400">*</span>
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs font-bold text-cyan-400">Rp</span>
                                            <input type="number" min="0" step="1000" x-model="formData.spekBaru.kemitraan_harga_sewa"
                                                placeholder="Contoh: 50000000"
                                                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl pl-9 pr-3 py-2 text-xs font-mono font-bold text-white focus:outline-none shadow-inner">
                                        </div>
                                        <template x-if="formData.spekBaru.kemitraan_harga_sewa > 0">
                                            <p class="text-[10px] text-cyan-400 font-mono font-semibold mt-1"
                                               x-text="'Terbaca: Rp ' + Number(formData.spekBaru.kemitraan_harga_sewa).toLocaleString('id-ID')"></p>
                                        </template>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-300 mb-1">
                                            Periode / Satuan Waktu Sewa: <span class="text-rose-400">*</span>
                                        </label>
                                        <select x-model="formData.spekBaru.kemitraan_periode_sewa"
                                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-semibold">
                                            <option value="Per Tahun">Per Tahun (Tahunan)</option>
                                            <option value="Per Bulan">Per Bulan (Bulanan)</option>
                                            <option value="Total Selama Kontrak">Total Selama Masa Kontrak</option>
                                        </select>
                                        <p class="text-[9.5px] text-slate-500 mt-1">Dasar perhitungan tagihan penerimaan sewa daerah.</p>
                                    </div>
                                </div>

                                <!-- Highlight Estimasi Total Penerimaan Kontrak Sewa -->
                                <template x-if="getReklasEstimasiTotalSewa() > 0">
                                    <div class="p-2.5 rounded-xl bg-emerald-500/10 border border-emerald-500/25 flex items-center justify-between text-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="text-base">📈</span>
                                            <div>
                                                <span class="text-[10px] text-emerald-300 font-bold uppercase tracking-wider block">Estimasi Total Kontrak Sewa:</span>
                                                <span class="text-slate-400 text-[10px]" x-text="'Durasi: ' + (formData.spekBaru.kemitraan_jangka_waktu || '-') + ' • Tarif ' + (formData.spekBaru.kemitraan_periode_sewa || 'Per Tahun')"></span>
                                            </div>
                                        </div>
                                        <span class="font-mono font-extrabold text-sm text-emerald-300"
                                              x-text="'Rp ' + Number(getReklasEstimasiTotalSewa()).toLocaleString('id-ID')"></span>
                                    </div>
                                </template>
                            </div>

                            <!-- KONDISI B: JIKA SKEMA NON-SEWA (KP, BGS/BSG, KSPI) -->
                            <div x-show="formData.spekBaru.kemitraan_skema !== 'Sewa'" class="space-y-3">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-300 mb-1">
                                            Nilai Kontribusi Tetap / Bagi Hasil (Rp):
                                        </label>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-xs font-bold text-cyan-400">Rp</span>
                                            <input type="number" min="0" step="1000" x-model="formData.spekBaru.kemitraan_nilai_kontribusi"
                                                placeholder="Contoh: 75000000"
                                                class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl pl-9 pr-3 py-2 text-xs font-mono font-bold text-white focus:outline-none shadow-inner">
                                        </div>
                                        <template x-if="formData.spekBaru.kemitraan_nilai_kontribusi > 0">
                                            <p class="text-[10px] text-cyan-400 font-mono font-semibold mt-1"
                                               x-text="'Terbaca: Rp ' + Number(formData.spekBaru.kemitraan_nilai_kontribusi).toLocaleString('id-ID')"></p>
                                        </template>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-300 mb-1">
                                            Keterangan Pola Pembagian / Kontribusi:
                                        </label>
                                        <input type="text" x-model="formData.spekBaru.kemitraan_keterangan_kontribusi"
                                            placeholder="Contoh: Kontribusi tetap per tahun + 15% laba operasional"
                                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none shadow-inner">
                                    </div>
                                </div>
                            </div>

                            <div class="text-[9.5px] text-slate-400 bg-slate-950/60 p-2 rounded-lg border border-slate-800 flex items-center gap-1.5">
                                <span>ℹ️</span>
                                <span><strong>Catatan:</strong> Nilai buku aset BMD (tanah/gedung) yang direklasifikasi tetap dicatat terpisah sebesar nilai perolehan/revaluasinya. Nilai di atas adalah nilai kontrak finansial pemanfaatan.</span>
                            </div>
                        </div>

                        <!-- 7. SPESIFIKASI FISIK OBJEK BMD YANG DIKEMITRAKAN (AUTO-ADAPTIF SESUAI KIB / KODE PMDN 108) -->
                        <div class="p-4 sm:p-5 rounded-2xl bg-slate-950/90 border border-purple-500/30 space-y-4 shadow-xl relative overflow-hidden">
                            <!-- Ambient Glow Effect -->
                            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-purple-500/10 rounded-full blur-3xl pointer-events-none"></div>

                            <!-- Header Section 7 -->
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-800 pb-3">
                                <div class="flex items-center space-x-2.5">
                                    <span class="w-8 h-8 rounded-xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-base border border-purple-500/30 shadow-inner">
                                        <span x-text="getKemitraanTargetSubGroup() === 'tanah' ? '🏛️' : (getKemitraanTargetSubGroup() === 'gedung' ? '🏢' : (getKemitraanTargetSubGroup() === 'jaringan' ? '🌐' : (getKemitraanTargetSubGroup() === 'lainnya' ? '📦' : '⚙️')))"></span>
                                    </span>
                                    <div>
                                        <div class="flex items-center gap-2">
                                            <h4 class="text-xs sm:text-sm font-extrabold text-white uppercase tracking-wide">
                                                Spesifikasi Fisik Objek BMD yang Dikemitrakan
                                            </h4>
                                            <!-- Sub-Group Badge -->
                                            <span class="px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40"
                                                  x-text="getKemitraanTargetSubGroup() === 'tanah' ? 'KIB A (Tanah)' : (getKemitraanTargetSubGroup() === 'gedung' ? 'KIB C (Gedung)' : (getKemitraanTargetSubGroup() === 'jaringan' ? 'KIB D (Jaringan)' : (getKemitraanTargetSubGroup() === 'lainnya' ? 'KIB E (Lainnya)' : 'KIB B (Peralatan & Mesin)')))">
                                            </span>
                                            <!-- Extracom vs Reguler Indicator Badge -->
                                            <template x-if="getKemitraanTargetSubGroup() === 'mesin' || getKemitraanTargetSubGroup() === 'lainnya'">
                                                <button type="button" 
                                                        class="px-2.5 py-0.5 rounded-md text-[10px] font-bold border transition-all flex items-center gap-1 cursor-pointer"
                                                        :class="isKemitraanExtracom() ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/40 shadow-sm hover:bg-cyan-500/30' : 'bg-purple-500/20 text-purple-300 border-purple-500/40 hover:bg-purple-500/30'"
                                                        @click="formData.spekBaru.is_extracom = !isKemitraanExtracom()"
                                                        title="Klik untuk beralih mode Extracom / Reguler secara manual">
                                                    <span x-text="isKemitraanExtracom() ? '📦 Ekstrakomptabel (≤ 300rb)' : '⚙️ Aset Tetap Reguler'"></span>
                                                    <span class="text-[9px] opacity-60">⇄ Ubah</span>
                                                </button>
                                            </template>
                                        </div>
                                        <p class="text-[11px] text-slate-400 mt-0.5">
                                            Data rincian fisik aset yang menjadi objek kemitraan. Tersalin otomatis dari aset terpilih dan dapat Anda sesuaikan.
                                        </p>
                                    </div>
                                </div>

                                <!-- Sub-Group Selector Tabs -->
                                <div class="flex items-center gap-1 bg-slate-900/90 p-1 rounded-xl border border-slate-800 self-start sm:self-auto">
                                    <button type="button" @click="kemitraanSubGroupOverride = 'mesin'"
                                            :class="(kemitraanSubGroupOverride || getKemitraanTargetSubGroup()) === 'mesin' ? 'bg-purple-600 text-white font-bold shadow-md' : 'text-slate-400 hover:text-slate-200'"
                                            class="px-2.5 py-1 rounded-lg text-[10.5px] transition-all cursor-pointer">
                                        ⚙️ Mesin / Alkes
                                    </button>
                                    <button type="button" @click="kemitraanSubGroupOverride = 'lainnya'"
                                            :class="(kemitraanSubGroupOverride || getKemitraanTargetSubGroup()) === 'lainnya' ? 'bg-purple-600 text-white font-bold shadow-md' : 'text-slate-400 hover:text-slate-200'"
                                            class="px-2.5 py-1 rounded-lg text-[10.5px] transition-all cursor-pointer">
                                        📦 KIB E
                                    </button>
                                    <button type="button" @click="kemitraanSubGroupOverride = 'gedung'"
                                            :class="(kemitraanSubGroupOverride || getKemitraanTargetSubGroup()) === 'gedung' ? 'bg-purple-600 text-white font-bold shadow-md' : 'text-slate-400 hover:text-slate-200'"
                                            class="px-2.5 py-1 rounded-lg text-[10.5px] transition-all cursor-pointer">
                                        🏢 Gedung
                                    </button>
                                    <button type="button" @click="kemitraanSubGroupOverride = 'tanah'"
                                            :class="(kemitraanSubGroupOverride || getKemitraanTargetSubGroup()) === 'tanah' ? 'bg-purple-600 text-white font-bold shadow-md' : 'text-slate-400 hover:text-slate-200'"
                                            class="px-2.5 py-1 rounded-lg text-[10.5px] transition-all cursor-pointer">
                                        🏛️ Tanah
                                    </button>
                                </div>
                            </div>
                            
                            <!-- SUB-TIPE 1: PERALATAN DAN MESIN (KIB B / 1.5.2.xx.002) -->
                            <template x-if="(kemitraanSubGroupOverride || getKemitraanTargetSubGroup()) === 'mesin'">
                                <div class="space-y-4">
                                    
                                    <!-- KASUS A: REGULER (NON-EXTRACOM) -> GAMBAR 2 -->
                                    <template x-if="!isKemitraanExtracom()">
                                        <div class="space-y-4">
                                            <!-- Single Cohesive Card: Spesifikasi Fisik Mesin Reguler -->
                                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-md">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>⚙️ Spesifikasi & Identitas Fisik Peralatan & Mesin:</span>
                                                    </span>
                                                    <span class="text-[9px] px-2 py-0.5 rounded-md bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">Identitas Fisik & Spesifikasi</span>
                                                </div>

                                                <!-- Baris 1: Merk, Type, Ukuran & Tahun Pembuatan (4 Kolom Seimbang) -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                                            Merk / Brand Pabrikan <span class="text-rose-400">*</span>
                                                        </label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_merk"
                                                               placeholder="Contoh: Siemens / Mindray / Roche / Sysmex / Fresenius / GE"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Type / Model <span class="text-rose-400">*</span></label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_type"
                                                               placeholder="Contoh: SOMATOM go.Now / DC-70 / 4008S"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Kapasitas</label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_ukuran_cc"
                                                               placeholder="Contoh: 128 Slice / 300 Test/Jam"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Tahun Pabrik</label>
                                                        <input type="number" min="1970" :max="new Date().getFullYear()" x-model.number="formData.spekBaru.mesin_tahun_pembuatan"
                                                               placeholder="Contoh: {{ date('Y') }}"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                                    </div>
                                                </div>

                                                <!-- Baris 2: No Pabrik/SN, Bahan, Kondisi (3 Kolom Seimbang) -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                                            No Pabrik / Serial Number (SN)
                                                        </label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_no_pabrik"
                                                               placeholder="SN-RAD-2026-88192"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Pembuatan</label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_bahan"
                                                               placeholder="Logam & Elektronik / Stainless"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                                            Kondisi Fisik Barang <span class="text-rose-400">*</span>
                                                        </label>
                                                        <select x-model="formData.spekBaru.mesin_kondisi"
                                                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500 focus:outline-none transition-all">
                                                            <option value="Baik">🟢 Baik (B) — Siap Operasional</option>
                                                            <option value="Kurang Baik">🟡 Kurang Baik (KB) — Perlu Kalibrasi / Setting</option>
                                                            <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <!-- Baris 3: Legality Kendaraan (Khusus Ambulans / Kendaraan Operasional) -->
                                                <div class="p-3 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2 transition-all">
                                                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-1">
                                                        <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                                            <span>🚗</span>
                                                            <span>Legality Kendaraan (Khusus Ambulans / Kendaraan Operasional):</span>
                                                        </span>
                                                        <span class="text-[9px] text-slate-500 italic">Opsional jika bukan kendaraan bermotor</span>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                                                        <div>
                                                            <label class="block text-slate-500 text-[9px] mb-1">No Rangka</label>
                                                            <input type="text" x-model="formData.spekBaru.mesin_no_rangka" placeholder="MH1JM..."
                                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-500 text-[9px] mb-1">No Mesin</label>
                                                            <input type="text" x-model="formData.spekBaru.mesin_no_mesin" placeholder="JM51E..."
                                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-500 text-[9px] mb-1">No BPKB</label>
                                                            <input type="text" x-model="formData.spekBaru.mesin_no_bpkb" placeholder="BPKB-88..."
                                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none">
                                                        </div>
                                                        <div>
                                                            <label class="block text-slate-500 text-[9px] mb-1">No POLISI / Plat</label>
                                                            <input type="text" x-model="formData.spekBaru.mesin_no_polisi" placeholder="P 1080 RS"
                                                                   class="w-full bg-slate-900 border border-slate-700/80 rounded-lg px-2.5 py-1.5 text-xs text-amber-300 font-mono font-bold focus:border-cyan-500 focus:outline-none">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Keterangan / Catatan Khusus Mesin -->
                                            <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-1 shadow-md">
                                                <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                                    📝 Keterangan / Catatan Khusus Peralatan:
                                                </label>
                                                <input type="text" x-model="formData.spekBaru.mesin_keterangan"
                                                       placeholder="Contoh: Penempatan alat uji laboratorium KSO / Garansi vendor 3 tahun / Keterangan operasional"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                            </div>
                                        </div>
                                    </template>

                                    <!-- KASUS B: EXTRACOM (EKSTRAKOMPTABEL) -> GAMBAR 3 -->
                                    <template x-if="isKemitraanExtracom()">
                                        <div class="space-y-4">
                                            <!-- Single Cohesive Card: Spesifikasi Fisik Mesin Extracom -->
                                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-md">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📦 SPESIFIKASI & IDENTITAS FISIK (EKSTRAKOMTABEL):</span>
                                                    </span>
                                                    <span class="text-[9px] px-2 py-0.5 rounded-md bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 font-bold">Ekstrakomptabel</span>
                                                </div>

                                                <!-- Nama Barang (PMDN 108) Khusus Extracom -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nama Barang (PMDN 108)</span>
                                                        <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
                                                            <span>🔒</span>
                                                            <span>Otomatis Sesuai Akun 108</span>
                                                        </span>
                                                    </label>
                                                    <input type="text" 
                                                           :value="formData.tujuan_nama || selectedAstap?.nama_barang || 'Barang Ekstrakomptabel'"
                                                           readonly
                                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                </div>

                                                <!-- Baris 1: Merk, Type & Ukuran (3 Kolom Seimbang) -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                                            Merk Barang <span class="text-rose-400">*</span>
                                                        </label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_merk"
                                                               placeholder="Contoh: Olympic / Lion / Krisbow / Kenko"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500 focus:outline-none transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Type / Model <span class="text-rose-400">*</span></label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_type"
                                                               placeholder="Contoh: Standard / Meja / Rak"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500 focus:outline-none transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Kapasitas</label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_ukuran_cc"
                                                               placeholder="Contoh: 120x60 cm / Sedang"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500 focus:outline-none transition-all">
                                                    </div>
                                                </div>

                                                <!-- Baris 2: No Pabrik, Bahan & Kondisi (3 Kolom Seimbang) -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                                            No Pabrik / SN
                                                        </label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_no_pabrik"
                                                               placeholder="SN-EXT-2026-001"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500 focus:outline-none transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Pembuatan</label>
                                                        <input type="text" x-model="formData.spekBaru.mesin_bahan"
                                                               placeholder="Kayu / Besi / Plastik"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500 focus:outline-none transition-all">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">
                                                            Kondisi Barang <span class="text-rose-400">*</span>
                                                        </label>
                                                        <select x-model="formData.spekBaru.mesin_kondisi"
                                                                class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500 focus:outline-none transition-all">
                                                            <option value="Baik">Baik (B)</option>
                                                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Keterangan / Catatan Khusus Extracom -->
                                            <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-1 shadow-md">
                                                <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                                    📝 Keterangan / Catatan Khusus Barang:
                                                </label>
                                                <input type="text" x-model="formData.spekBaru.mesin_keterangan"
                                                       placeholder="Contoh: Barang ekstrakomptabel penunjang operasional kemitraan"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-amber-500 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                            </div>
                                        </div>
                                    </template>

                                </div>
                            </template>

                            <!-- SUB-TIPE 2: ASET TETAP LAINNYA (KIB E / 1.5.2.xx.005) -->
                            <template x-if="(kemitraanSubGroupOverride || getKemitraanTargetSubGroup()) === 'lainnya'">
                                <div class="space-y-4">
                                    
                                    <!-- KASUS A: REGULER (NON-EXTRACOM) -> SPESIFIK BUKU / KESENIAN / HEWAN TANAMAN -->
                                    <template x-if="!isKemitraanExtracom()">
                                        <div class="space-y-3">
                                            <!-- Tab Kategori KIB E (Buku, Kesenian, Hewan/Tumbuhan) -->
                                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                                <div @click="formData.spekBaru.kib_e_type = 'buku'"
                                                     :class="(formData.spekBaru.kib_e_type || 'buku') === 'buku' ? 'border-amber-500 bg-amber-950/40 ring-1 ring-amber-500 text-white font-extrabold shadow-sm' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 text-amber-300/80'"
                                                     class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold">
                                                    <div class="flex items-center space-x-2">
                                                        <span>📚</span>
                                                        <div class="text-left">
                                                            <div class="text-xs">Buku Perpustakaan</div>
                                                            <div class="text-[9px] text-slate-400 font-mono font-normal">Pustaka Medis</div>
                                                        </div>
                                                    </div>
                                                    <span x-show="(formData.spekBaru.kib_e_type || 'buku') === 'buku'" class="text-amber-400 font-bold">✓</span>
                                                </div>
                                                <div @click="formData.spekBaru.kemitraan_kib_e_type = 'kesenian'; formData.spekBaru.kib_e_type = 'kesenian'"
                                                     :class="formData.spekBaru.kib_e_type === 'kesenian' ? 'border-purple-500 bg-purple-950/40 ring-1 ring-purple-500 text-white font-extrabold shadow-sm' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 text-purple-300/80'"
                                                     class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold">
                                                    <div class="flex items-center space-x-2">
                                                        <span>🎨</span>
                                                        <div class="text-left">
                                                            <div class="text-xs">Kesenian &amp; Budaya</div>
                                                            <div class="text-[9px] text-slate-400 font-mono font-normal">Karya Seni / Ornamen</div>
                                                        </div>
                                                    </div>
                                                    <span x-show="formData.spekBaru.kib_e_type === 'kesenian'" class="text-purple-400 font-bold">✓</span>
                                                </div>
                                                <div @click="formData.spekBaru.kemitraan_kib_e_type = 'hewan_tumbuhan'; formData.spekBaru.kib_e_type = 'hewan_tumbuhan'"
                                                     :class="formData.spekBaru.kib_e_type === 'hewan_tumbuhan' ? 'border-emerald-500 bg-emerald-950/40 ring-1 ring-emerald-500 text-white font-extrabold shadow-sm' : 'border-slate-800 bg-slate-950/60 opacity-60 hover:opacity-100 text-emerald-300/80'"
                                                     class="p-2.5 rounded-xl border transition-all cursor-pointer flex items-center justify-between text-xs font-bold">
                                                    <div class="flex items-center space-x-2">
                                                        <span>🌿</span>
                                                        <div class="text-left">
                                                            <div class="text-xs">Hewan &amp; Tumbuhan</div>
                                                            <div class="text-[9px] text-slate-400 font-mono font-normal">Penghijauan / Hewan Lab</div>
                                                        </div>
                                                    </div>
                                                    <span x-show="formData.spekBaru.kib_e_type === 'hewan_tumbuhan'" class="text-emerald-400 font-bold">✓</span>
                                                </div>
                                            </div>

                                            <!-- 1. Form Buku Perpustakaan -->
                                            <div x-show="(formData.spekBaru.kib_e_type || 'buku') === 'buku'" class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Judul Buku / Pustaka Medis <span class="text-rose-400">*</span></label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_judul" placeholder="Contoh: Atlas Anatomi Manusia Sobotta Edisi 24"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pengarang / Penerbit / Pencipta</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_pencipta" placeholder="Contoh: EGC Penerbit Buku Kedokteran"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi / Edisi</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi" placeholder="Contoh: Edisi Revisi 2026 / Hardcover Lux"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-amber-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 2. Form Benda Seni & Budaya -->
                                            <div x-show="formData.spekBaru.kib_e_type === 'kesenian'" class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Asal Daerah / Wilayah</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_asal_daerah" placeholder="Bondowoso, Jawa Timur"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Pencipta / Seniman</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_pencipta" placeholder="Sanggar Seni Rupa Bondowoso"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Pembuatan</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_bahan" placeholder="Kanvas Cat Minyak / Perunggu / Kayu Jati"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Dimensi</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_ukuran" placeholder="150 x 200 cm"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Spesifikasi Karya Seni / Ornamen</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi" placeholder="Lukisan dinding sejarah pelayanan RSUD"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-purple-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- 3. Form Hewan & Tumbuhan -->
                                            <div x-show="formData.spekBaru.kib_e_type === 'hewan_tumbuhan'" class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Jenis Hewan / Tanaman <span class="text-rose-400">*</span></label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_judul" placeholder="Contoh: Pohon Ketapang Kencana / Palem Raja"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Tinggi</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_ukuran" placeholder="Tinggi 3.5 Meter"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500">
                                                    </div>
                                                    <div class="sm:col-span-2">
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Keterangan / Spesifikasi Khusus</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi" placeholder="Penghijauan Area Taman Rawat Inap Barat"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-emerald-500">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Kondisi Fisik KIB E Reguler -->
                                            <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800 flex items-center justify-between gap-3">
                                                <label class="text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">Kondisi Fisik Barang:</label>
                                                <select x-model="formData.spekBaru.lainnya_kondisi" class="bg-slate-950 border border-slate-700 rounded-xl px-3 py-1.5 text-xs text-white font-bold focus:border-cyan-500">
                                                    <option value="Baik">🟢 Baik (B)</option>
                                                    <option value="Kurang Baik">🟡 Kurang Baik (KB)</option>
                                                    <option value="Rusak Berat">🔴 Rusak Berat (RB)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </template>

                                    <!-- KASUS B: EXTRACOM (UNIVERSAL GAMBAR 3) -->
                                    <template x-if="isKemitraanExtracom()">
                                        <div class="space-y-4">
                                            <!-- Single Cohesive Card: Spesifikasi Fisik KIB E Extracom -->
                                            <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-4 shadow-md">
                                                <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider flex items-center space-x-1.5">
                                                        <span>📚 SPESIFIKASI & IDENTITAS FISIK (EKSTRAKOMTABEL KIB E):</span>
                                                    </span>
                                                    <span class="text-[9px] px-2 py-0.5 rounded-md bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 font-bold">Ekstrakomptabel KIB E</span>
                                                </div>

                                                <!-- Nama Barang (PMDN 108) Khusus Extracom KIB E -->
                                                <div>
                                                    <label class="block text-slate-400 text-[10px] mb-1 font-semibold flex items-center justify-between">
                                                        <span>Nama Barang (PMDN 108)</span>
                                                        <span class="text-[9px] text-amber-400 font-bold flex items-center space-x-1">
                                                            <span>🔒</span>
                                                            <span>Otomatis Sesuai Akun 108</span>
                                                        </span>
                                                    </label>
                                                    <input type="text" 
                                                           :value="formData.tujuan_nama || selectedAstap?.nama_barang || 'Aset Tetap Lainnya'"
                                                           readonly
                                                           class="w-full bg-slate-950/70 border border-slate-800 rounded-xl px-3 py-2 text-xs text-slate-300 font-bold cursor-not-allowed select-none focus:outline-none">
                                                </div>

                                                <!-- Baris 1: Merk, Type & Ukuran (3 Kolom Seimbang) -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Merk / Penerbit <span class="text-rose-400">*</span></label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_pencipta"
                                                               placeholder="Contoh: Olympic / Lion / Krisbow / Kenko"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-semibold focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Type / Model <span class="text-rose-400">*</span></label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_spesifikasi"
                                                               placeholder="Contoh: Standard / Meja / Rak"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white font-mono focus:border-amber-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Ukuran / Kapasitas</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_ukuran"
                                                               placeholder="Contoh: 120x60 cm / Sedang"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-2.5 py-2 text-xs text-white focus:border-amber-500">
                                                    </div>
                                                </div>

                                                <!-- Baris 2: No Pabrik, Bahan & Kondisi (3 Kolom Seimbang) -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">No Pabrik / SN</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_no_pabrik"
                                                               placeholder="SN-EXT-2026-001"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:border-cyan-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Bahan Pembuatan</label>
                                                        <input type="text" x-model="formData.spekBaru.lainnya_bahan"
                                                               placeholder="Kayu / Besi / Plastik"
                                                               class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:border-cyan-500">
                                                    </div>
                                                    <div>
                                                        <label class="block text-slate-400 text-[10px] mb-1 font-semibold">Kondisi Barang <span class="text-rose-400">*</span></label>
                                                        <select x-model="formData.spekBaru.lainnya_kondisi" class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-bold focus:border-cyan-500">
                                                            <option value="Baik">Baik (B)</option>
                                                            <option value="Kurang Baik">Kurang Baik (KB)</option>
                                                            <option value="Rusak Berat">Rusak Berat (RB)</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Keterangan / Catatan Khusus Extracom KIB E -->
                                            <div class="p-3 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-1 shadow-md">
                                                <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                                    📝 Keterangan / Catatan Khusus Barang:
                                                </label>
                                                <input type="text" x-model="formData.spekBaru.lainnya_keterangan"
                                                       placeholder="Contoh: Koleksi literatur medis kemitraan / Spesifikasi khusus"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-purple-500 rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all">
                                            </div>
                                        </div>
                                    </template>

                                </div>
                            </template>

                            <!-- SUB-TIPE 3: GEDUNG DAN BANGUNAN (KIB C / 1.5.2.xx.003) -->
                            <template x-if="(kemitraanSubGroupOverride || getKemitraanTargetSubGroup()) === 'gedung'">
                                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Bertingkat:</label>
                                            <select x-model="formData.spekBaru.gedung_konstruksi_bertingkat" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                <option value="Bertingkat">Bertingkat</option>
                                                <option value="Tidak Bertingkat">Tidak Bertingkat</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Beton:</label>
                                            <select x-model="formData.spekBaru.gedung_konstruksi_beton" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                <option value="Beton">Beton</option>
                                                <option value="Tidak">Bukan Beton / Semi Permanen</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Lantai (M2):</label>
                                            <input type="number" step="0.01" min="0" x-model="formData.spekBaru.gedung_luas_lantai_m2" placeholder="Contoh: 450.5" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Status Hak Tanah:</label>
                                            <select x-model="formData.spekBaru.gedung_status_tanah" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                <option value="Tanah Pemda">Tanah Milik Pemda</option>
                                                <option value="Tanah Negara">Tanah Negara</option>
                                                <option value="Hak Pengelolaan">Hak Pengelolaan (HPL)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Dokumen Bangunan (IMB/PBG):</label>
                                            <input type="text" x-model="formData.spekBaru.gedung_dokumen_nomor" placeholder="Contoh: 640/IMB/DPMPTSP/2026" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Lokasi Alamat Bangunan:</label>
                                            <input type="text" x-model="formData.spekBaru.gedung_alamat" placeholder="Sayap Barat RSUD Dr. H. Koesnandi" class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <!-- SUB-TIPE 4: TANAH (KIB A / 1.5.2.xx.001) -->
                            <template x-if="(kemitraanSubGroupOverride || getKemitraanTargetSubGroup()) === 'tanah'">
                                <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800 space-y-3 shadow-md">
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Tanah (M2):</label>
                                            <input type="number" step="0.01" min="0" x-model="formData.spekBaru.tanah_luas_m2" placeholder="Contoh: 1500" class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Hak Tanah:</label>
                                            <select x-model="formData.spekBaru.tanah_hak" class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                <option value="Hak Pakai">Hak Pakai</option>
                                                <option value="Hak Milik">Hak Milik</option>
                                                <option value="Hak Pengelolaan">Hak Pengelolaan (HPL)</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Sertifikat:</label>
                                            <input type="text" x-model="formData.spekBaru.tanah_sertifikat_no" placeholder="No. 12.04.05..." class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-mono">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Penggunaan Tanah:</label>
                                            <input type="text" x-model="formData.spekBaru.tanah_penggunaan" placeholder="Area Parkir / Lahan Kemitraan" class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Alamat / Lokasi:</label>
                                            <input type="text" x-model="formData.spekBaru.tanah_alamat" placeholder="Jl. Kapten Piere Tendean No. 1 Bondowoso" class="w-full bg-slate-950 border border-slate-700 focus:border-emerald-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        </div>
                                    </div>
                                </div>
                            </template>
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
