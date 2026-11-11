<!-- FILTER BAR & CONTROLS REKLASIFIKASI -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl backdrop-blur-xl space-y-4">
    
    <!-- BARIS 1: TAB NAVIGATION (KIRI) & TOMBOL AKSI UTAMA (KANAN) -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pb-4 border-b border-slate-800/80">
        <!-- Tab Switcher: Matriks Neraca vs Log Transaksi -->
        <div class="inline-flex items-center p-1 bg-slate-950/80 border border-slate-800 rounded-2xl shadow-inner self-start sm:self-auto">
            <button type="button" @click="activeTab = 'matriks'"
                :class="activeTab === 'matriks' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white'"
                class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                </svg>
                <span>Matriks Neraca</span>
            </button>
            <button type="button" @click="activeTab = 'log'"
                :class="activeTab === 'log' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/30' : 'text-slate-400 hover:text-white'"
                class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-bold transition-all duration-200 cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                <span>Log Transaksi</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-900 text-indigo-300 border border-indigo-500/20">
                    {{ count($logReklas) }}
                </span>
            </button>
        </div>

        <!-- Tombol Aksi Kanan: Reklasifikasi Baru & Ekspor Excel -->
        <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
            <a href="{{ route('master.reklasifikasi.create', ['tahun' => $selectedTahun]) }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Reklasifikasi Baru</span>
            </a>

            <button type="button" @click="exportToExcel()"
                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-emerald-400 hover:text-emerald-300 border border-emerald-500/30 hover:border-emerald-500/50 text-xs font-bold shadow-sm hover:shadow-emerald-500/10 active:scale-95 transition-all duration-200 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Ekspor Excel</span>
            </button>
        </div>
    </div>

    <!-- BARIS 2: FILTER PANEL (ADAPTIF: 2 KOLOM UNTUK MATRIKS NERACA, 3 KOLOM UNTUK LOG TRANSAKSI) -->
    <div class="pt-1">
        <form method="GET" action="{{ route('master.reklasifikasi') }}" class="w-full">
            <input type="hidden" name="tab" :value="activeTab">

            <div class="grid gap-3 w-full transition-all duration-300"
                 :class="activeTab === 'matriks' ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'">
                <!-- 1. TAHUN ANGGARAN -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Tahun Anggaran</span>
                    </label>
                    <div class="relative">
                        <select name="tahun" onchange="this.form.submit()"
                            style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                            class="w-full bg-slate-950 border border-slate-800 hover:border-indigo-500/50 focus:border-indigo-500 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-bold text-white focus:outline-none transition-all cursor-pointer shadow-sm">
                            @foreach ($tahunList as $th)
                                <option value="{{ $th }}" {{ $selectedTahun == $th ? 'selected' : '' }} class="bg-slate-900 text-white">
                                    {{ $th }}
                                </option>
                            @endforeach
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-indigo-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- 2. PERIODE TRIWULAN -->
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Periode Triwulan</span>
                    </label>
                    <div class="relative">
                        <select name="triwulan" onchange="this.form.submit()"
                            style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                            class="w-full bg-slate-950 border border-slate-800 hover:border-cyan-500/50 focus:border-cyan-500 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-bold text-white focus:outline-none transition-all cursor-pointer shadow-sm">
                            <option value="all" {{ $selectedTw == 'all' ? 'selected' : '' }} class="bg-slate-900 text-white">Seluruh Tahun</option>
                            <option value="1" {{ $selectedTw == '1' ? 'selected' : '' }} class="bg-slate-900 text-white">Triwulan I (Jan - Mar)</option>
                            <option value="2" {{ $selectedTw == '2' ? 'selected' : '' }} class="bg-slate-900 text-white">Triwulan II (Apr - Jun)</option>
                            <option value="3" {{ $selectedTw == '3' ? 'selected' : '' }} class="bg-slate-900 text-white">Triwulan III (Jul - Sep)</option>
                            <option value="4" {{ $selectedTw == '4' ? 'selected' : '' }} class="bg-slate-900 text-white">Triwulan IV (Okt - Des)</option>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-cyan-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- 3. KATEGORI / JENIS REKLASIFIKASI (Hanya Tampil Pada Tab Log Transaksi) -->
                <div x-show="activeTab === 'log'" x-cloak
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[10px] font-bold uppercase tracking-wider text-slate-400 flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            <span>Kategori / Jenis Reklas</span>
                        </label>
                        @if (($selectedJenis ?? 'all') !== 'all')
                            <a href="{{ route('master.reklasifikasi', ['tahun' => $selectedTahun, 'triwulan' => $selectedTw, 'tab' => 'log']) }}"
                               class="text-[10px] text-violet-400 hover:text-white font-bold transition-colors inline-flex items-center gap-1"
                               title="Reset filter jenis reklasifikasi">
                                <span>✕ Reset Filter</span>
                            </a>
                        @endif
                    </div>
                    <div class="relative">
                        <select name="jenis_reklas" onchange="this.form.submit()"
                            :disabled="activeTab === 'matriks'"
                            style="background-image: none !important; -webkit-appearance: none; -moz-appearance: none; appearance: none;"
                            class="w-full bg-slate-950 border {{ ($selectedJenis ?? 'all') !== 'all' ? 'border-violet-500 ring-1 ring-violet-500/30' : 'border-slate-800' }} hover:border-violet-500/50 focus:border-violet-500 rounded-xl px-3.5 py-2.5 pr-8 text-xs font-bold text-white focus:outline-none transition-all cursor-pointer shadow-sm truncate">
                            <option value="all" {{ ($selectedJenis ?? 'all') == 'all' ? 'selected' : '' }} class="bg-slate-900 text-white">Semua Jenis (6 Kategori)</option>
                            <optgroup label="1. Koreksi Rekening & Kemitraan" class="bg-slate-950 text-indigo-400 font-bold">
                                <option value="KOREKSI_REKENING" {{ ($selectedJenis ?? '') == 'KOREKSI_REKENING' ? 'selected' : '' }} class="bg-slate-900 text-white">Koreksi Rekening / Pindah KIB</option>
                            </optgroup>
                            <optgroup label="2. Batas Kapitalisasi (≤ Rp 300rb)" class="bg-slate-950 text-rose-400 font-bold">
                                <option value="EXTRACOM_GROUP" {{ ($selectedJenis ?? '') == 'EXTRACOM_GROUP' ? 'selected' : '' }} class="bg-slate-900 text-white">Semua Ekstrakomptabel &amp; Intrakom</option>
                                <option value="EKSTRAKOMPTABEL" {{ ($selectedJenis ?? '') == 'EKSTRAKOMPTABEL' ? 'selected' : '' }} class="bg-slate-900 text-white">🔻 Ekstrakomptabel (≤ Rp 300rb)</option>
                                <option value="KAPITALISASI_INTRAKOM" {{ ($selectedJenis ?? '') == 'KAPITALISASI_INTRAKOM' ? 'selected' : '' }} class="bg-slate-900 text-white">🔺 Kapitalisasi Intrakomptabel</option>
                            </optgroup>
                            <optgroup label="3. Konstruksi Dalam Pengerjaan (KDP)" class="bg-slate-950 text-emerald-400 font-bold">
                                <option value="KDP_GROUP" {{ ($selectedJenis ?? '') == 'KDP_GROUP' ? 'selected' : '' }} class="bg-slate-900 text-white">Semua Transaksi KDP</option>
                                <option value="KDP_TO_DEFINITIF" {{ ($selectedJenis ?? '') == 'KDP_TO_DEFINITIF' ? 'selected' : '' }} class="bg-slate-900 text-white">🏗️ KDP Selesai ➔ Definitif</option>
                                <option value="DEFINITIF_TO_KDP" {{ ($selectedJenis ?? '') == 'DEFINITIF_TO_KDP' ? 'selected' : '' }} class="bg-slate-900 text-white">🔄 Definitif ➔ KDP</option>
                            </optgroup>
                            <optgroup label="4. Penyesuaian Nilai Realisasi" class="bg-slate-950 text-cyan-400 font-bold">
                                <option value="KOREKSI_LAIN" {{ ($selectedJenis ?? '') == 'KOREKSI_LAIN' ? 'selected' : '' }} class="bg-slate-900 text-white">⚖️ Koreksi Nilai / Audit BPK / Manset</option>
                            </optgroup>
                            <optgroup label="5. Bantuan Hibah Barang" class="bg-slate-950 text-purple-400 font-bold">
                                <option value="HIBAH_GROUP" {{ ($selectedJenis ?? '') == 'HIBAH_GROUP' ? 'selected' : '' }} class="bg-slate-900 text-white">Semua Hibah (Keluar &amp; Masuk)</option>
                                <option value="HIBAH_KELUAR" {{ ($selectedJenis ?? '') == 'HIBAH_KELUAR' ? 'selected' : '' }} class="bg-slate-900 text-white">📤 Hibah Keluar (Dihibahkan)</option>
                                <option value="HIBAH_MASUK" {{ ($selectedJenis ?? '') == 'HIBAH_MASUK' ? 'selected' : '' }} class="bg-slate-900 text-white">📥 Hibah Masuk (Bantuan Pemerintah)</option>
                            </optgroup>
                            <optgroup label="6. Mutasi Keluar OPD" class="bg-slate-950 text-teal-400 font-bold">
                                <option value="MUTASI_EKSTERNAL" {{ ($selectedJenis ?? '') == 'MUTASI_EKSTERNAL' ? 'selected' : '' }} class="bg-slate-900 text-white">🏛️ Mutasi Eksternal (Antar-OPD)</option>
                            </optgroup>
                        </select>
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-violet-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
