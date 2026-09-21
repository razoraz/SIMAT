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
            <button type="button" @click="openModalTambah()"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white text-xs font-bold shadow-lg shadow-indigo-600/30 hover:shadow-indigo-500/50 hover:-translate-y-0.5 active:scale-95 transition-all duration-200 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Reklasifikasi Baru</span>
            </button>

            <button type="button" @click="exportToExcel()"
                class="inline-flex items-center gap-2 px-3.5 py-2.5 rounded-xl bg-slate-800/90 hover:bg-slate-800 text-emerald-400 hover:text-emerald-300 border border-emerald-500/30 hover:border-emerald-500/50 text-xs font-bold shadow-sm hover:shadow-emerald-500/10 active:scale-95 transition-all duration-200 cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Ekspor Excel</span>
            </button>
        </div>
    </div>

    <!-- BARIS 2: FILTER PERIODE (KIRI) & RINGKASAN DATA AKTIF (KANAN) -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
        <!-- Form Filter Tahun & Periode -->
        <form method="GET" action="{{ route('master.reklasifikasi') }}" class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
            <!-- Filter Tahun -->
            <div class="flex items-center gap-2 bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500/30 transition-all shadow-sm">
                <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-xs font-semibold text-slate-400">Tahun:</span>
                <select name="tahun" onchange="this.form.submit()" class="bg-transparent text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    @foreach ($tahunList as $th)
                        <option value="{{ $th }}" {{ $selectedTahun == $th ? 'selected' : '' }} class="bg-slate-900 text-white">
                            {{ $th }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Triwulan / Periode -->
            <div class="flex items-center gap-2 bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2 focus-within:border-indigo-500 focus-within:ring-1 focus-within:ring-indigo-500/30 transition-all shadow-sm">
                <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="text-xs font-semibold text-slate-400">Periode:</span>
                <select name="triwulan" onchange="this.form.submit()" class="bg-transparent text-xs font-bold text-white focus:outline-none cursor-pointer pr-1">
                    <option value="all" {{ $selectedTw == 'all' ? 'selected' : '' }} class="bg-slate-900 text-white">Seluruh Tahun</option>
                    <option value="1" {{ $selectedTw == '1' ? 'selected' : '' }} class="bg-slate-900 text-white">Triwulan I (Jan - Mar)</option>
                    <option value="2" {{ $selectedTw == '2' ? 'selected' : '' }} class="bg-slate-900 text-white">Triwulan II (Apr - Jun)</option>
                    <option value="3" {{ $selectedTw == '3' ? 'selected' : '' }} class="bg-slate-900 text-white">Triwulan III (Jul - Sep)</option>
                    <option value="4" {{ $selectedTw == '4' ? 'selected' : '' }} class="bg-slate-900 text-white">Triwulan IV (Okt - Des)</option>
                </select>
            </div>
        </form>

        <!-- Status / Active Filter Badge (Kanan) -->
        <div class="flex items-center gap-2 text-xs text-slate-400">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-950/80 border border-slate-800 text-[11px] font-semibold text-slate-300">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Periode Aktif: <strong class="text-white">{{ $selectedTahun }}</strong> ({{ $selectedTw == 'all' ? 'Seluruh Tahun' : 'Triwulan ' . $selectedTw }})</span>
            </span>
        </div>
    </div>
</div>
