<!-- Search & Filter Bar -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
    <form method="GET" action="{{ route('master.rekening_belanja') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full text-xs">
        
        <!-- Search Input Field -->
        <div class="relative flex-1 w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode rekening / nama belanja pengadaan SIPD... (Tekan Enter)"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all">
            <svg class="w-4 h-4 text-blue-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            @if (request('search'))
                <a href="{{ route('master.rekening_belanja', ['kelompok' => request('kelompok'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</a>
            @endif
        </div>

        <!-- Filter Dropdown Kelompok Akun Belanja -->
        <div class="flex items-center space-x-1.5 bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-400 w-full sm:w-auto shrink-0">
            <span class="text-[10px] uppercase font-bold text-slate-500">Kelompok:</span>
            <select name="kelompok" onchange="this.form.submit()" class="bg-transparent text-blue-400 font-bold focus:outline-none cursor-pointer text-xs max-w-[180px] sm:max-w-[240px] truncate">
                <option value="all" {{ request('kelompok', 'all') === 'all' ? 'selected' : '' }} class="bg-slate-900 text-white">Semua Kelompok</option>
                @foreach ($uniqueKelompok as $kel)
                    <option value="{{ $kel['kelompok'] }}" {{ request('kelompok') === $kel['kelompok'] ? 'selected' : '' }} class="bg-slate-900 text-white">
                        {{ $kel['kelompok'] }} - {{ $kel['nama_kelompok'] }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Selector Jumlah Baris (Per Page) -->
        <div class="flex items-center space-x-1.5 bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-400 shrink-0">
            <span class="text-[10px] uppercase font-bold text-slate-500">Baris:</span>
            <select name="per_page" onchange="this.form.submit()" class="bg-transparent text-blue-400 font-bold focus:outline-none cursor-pointer text-xs">
                <option value="10" {{ request('per_page', '15') == '10' ? 'selected' : '' }} class="bg-slate-900 text-white">10</option>
                <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }} class="bg-slate-900 text-white">15</option>
                <option value="25" {{ request('per_page', '15') == '25' ? 'selected' : '' }} class="bg-slate-900 text-white">25</option>
                <option value="50" {{ request('per_page', '15') == '50' ? 'selected' : '' }} class="bg-slate-900 text-white">50</option>
                <option value="100" {{ request('per_page', '15') == '100' ? 'selected' : '' }} class="bg-slate-900 text-white">100</option>
            </select>
        </div>

        <!-- Action Buttons & Counter Badge -->
        <div class="flex items-center space-x-2 shrink-0 w-full sm:w-auto">
            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Cari</span>
            </button>

            @if (request('search') || (request('kelompok') && request('kelompok') !== 'all'))
                <a href="{{ route('master.rekening_belanja') }}" class="px-3.5 py-2.5 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all shrink-0">
                    🔄 Reset
                </a>
            @endif

            <span class="px-3.5 py-2.5 rounded-2xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300 shrink-0">
                Menampilkan <span class="text-blue-400 font-bold">{{ $rekeningList->count() }}</span> dari <span class="text-white font-bold">{{ $rekeningList->total() }}</span> Akun
            </span>
        </div>
    </form>
</div>
