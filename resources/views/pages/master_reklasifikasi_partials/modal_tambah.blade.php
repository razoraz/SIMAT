<!-- MODAL TAMBAH REKLASIFIKASI BARU -->
<div x-show="showModalTambah" class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
    
    <!-- Backdrop -->
    <div class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm" @click="showModalTambah = false"></div>

    <!-- Modal Dialog -->
    <div class="relative w-full max-w-2xl max-h-[92vh] flex flex-col bg-slate-900 border border-slate-700/80 rounded-2xl shadow-2xl overflow-hidden z-10">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-800 bg-slate-950/60 flex items-center justify-between shrink-0">
            <div class="flex items-center gap-3">
                <div class="p-2 rounded-xl bg-indigo-600/20 text-indigo-400 border border-indigo-500/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Catat Reklasifikasi Aset Baru</h3>
                    <p class="text-xs text-slate-400">Mutasi antar KIB / Sub-Rincian PMDN 108 RSUD Dr. H. Koesnandi</p>
                </div>
            </div>
            <button type="button" @click="showModalTambah = false"
                class="p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <form @submit.prevent="submitFormTambah()" class="overflow-y-auto p-6 space-y-4 flex-1 scrollbar-thin scrollbar-thumb-slate-700">
            <!-- 1. Pilih Aset yang Akan Direklasifikasi -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Pilih Barang Milik Daerah (ASTAP) <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.astap_id" @change="onSelectAstap($event.target.value)" required
                    class="w-full px-3.5 py-2.5 rounded-xl bg-slate-800/90 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                    <option value="">-- Pilih Barang dari Katalog ASTAP --</option>
                    @foreach ($kandidatAstaps as $kand)
                        <option value="{{ $kand->id }}" 
                            data-nilai="{{ $kand->total_realisasi }}"
                            data-nama="{{ $kand->nama_barang }}"
                            data-prefix="{{ $kand->jenisAstap ? substr($kand->jenisAstap->sub_rincian_objek ?? '', 0, 8) : '' }}"
                            data-sub="{{ $kand->jenisAstap->uraian_sub_rincian ?? '' }}">
                            {{ $kand->nama_barang }} (Rp {{ number_format($kand->total_realisasi, 0, ',', '.') }})
                            {{ $kand->is_reklas ? ' [Sudah Pernah Reklas]' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- 2. Jenis Reklasifikasi -->
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                    Jenis Reklasifikasi <span class="text-rose-400">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-700 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-colors"
                        :class="formData.jenis_reklas === 'KOREKSI_REKENING' ? 'border-indigo-500 bg-indigo-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="KOREKSI_REKENING" class="text-indigo-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Koreksi Rekening</p>
                            <p class="text-[10px] text-slate-400">Salah kamar KIB / Sub-Rincian</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-700 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-colors"
                        :class="formData.jenis_reklas === 'KDP_TO_DEFINITIF' ? 'border-emerald-500 bg-emerald-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="KDP_TO_DEFINITIF" class="text-emerald-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">KDP Selesai ➔ Definitif</p>
                            <p class="text-[10px] text-slate-400">Pekerjaan fisik KIB F selesai</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-700 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-colors"
                        :class="formData.jenis_reklas === 'EKSTRAKOMPTABEL' ? 'border-rose-500 bg-rose-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="EKSTRAKOMPTABEL" class="text-rose-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Ekstrakomptabel</p>
                            <p class="text-[10px] text-slate-400">Nilai di bawah batas kapitalisasi</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-3 rounded-xl border border-slate-700 bg-slate-800/60 hover:bg-slate-800 cursor-pointer transition-colors"
                        :class="formData.jenis_reklas === 'HIBAH_MASUK' ? 'border-amber-500 bg-amber-500/10' : ''">
                        <input type="radio" x-model="formData.jenis_reklas" value="HIBAH_MASUK" class="text-amber-600 focus:ring-0">
                        <div>
                            <p class="text-xs font-bold text-white">Hibah / Bantuan Masuk</p>
                            <p class="text-[10px] text-slate-400">Penerimaan dari pihak ketiga</p>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 3. Kelompok Asal & Kelompok Tujuan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Baris Asal (Mutasi Kurang) <span class="text-rose-400">*</span>
                    </label>
                    <select x-model="formData.jenis_reklasifikasi_asal_id" required
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Baris Asal --</option>
                        @foreach ($templateRows as $tRow)
                            <option value="{{ $tRow->id }}">
                                [{{ $tRow->kelompok_kib }}] {{ $tRow->nama_sub_rincian }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Baris Tujuan (Mutasi Tambah)
                    </label>
                    <select x-model="formData.jenis_reklasifikasi_tujuan_id"
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                        <option value="">-- Pilih Baris Tujuan --</option>
                        @foreach ($templateRows as $tRow)
                            <option value="{{ $tRow->id }}">
                                [{{ $tRow->kelompok_kib }}] {{ $tRow->nama_sub_rincian }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- 4. Nilai, Tanggal, Triwulan & Tahun -->
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

            <!-- 5. No. BA Reklas & Keterangan -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Nomor Berita Acara Reklasifikasi
                    </label>
                    <input type="text" x-model="formData.nomor_ba_reklas" placeholder="000.2.3.2/.../430.10.7/2026"
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1.5">
                        Keterangan / Alasan
                    </label>
                    <input type="text" x-model="formData.keterangan" placeholder="Alasan pemindahan bukuan..."
                        class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-white text-xs focus:outline-none focus:border-indigo-500">
                </div>
            </div>

            <!-- 6. Narasi Preview Berita Acara Otomatis -->
            <div class="p-3.5 rounded-xl bg-slate-950/60 border border-slate-800 space-y-1.5">
                <span class="text-[11px] font-bold text-indigo-400 uppercase tracking-wider">Preview Narasi Berita Acara:</span>
                <p class="text-xs text-slate-300 italic leading-relaxed" x-text="getNarasiPreview()"></p>
            </div>

            <!-- Modal Footer (Inside Form) -->
            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" @click="showModalTambah = false"
                    class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-bold transition-all">
                    Batal
                </button>
                <button type="submit" :disabled="isSubmitting"
                    class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 transition-all disabled:opacity-50">
                    <span x-show="!isSubmitting">Simpan Transaksi Reklas</span>
                    <span x-show="isSubmitting">Menyimpan...</span>
                </button>
            </div>
        </form>
    </div>
</div>
