<!-- Search & Filter Bar -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
    <form method="GET" action="{{ route('master.jenis_pengadaan') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full text-xs">
        
        <!-- Search Input Field -->
        <div class="relative flex-1 w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / nama program, kegiatan, atau sub kegiatan SIPD... (Tekan Enter)"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
            <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            @if (request('search'))
                <a href="{{ route('master.jenis_pengadaan', ['program' => request('program'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</a>
            @endif
        </div>

        <!-- Filter Dropdown Program SIPD -->
        <div class="flex items-center space-x-1.5 bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-400 w-full sm:w-auto shrink-0">
            <span class="text-[10px] uppercase font-bold text-slate-500">Program:</span>
            <select name="program" onchange="this.form.submit()" class="bg-transparent text-emerald-400 font-bold focus:outline-none cursor-pointer text-xs max-w-[180px] sm:max-w-[220px] truncate">
                <option value="all" {{ request('program', 'all') === 'all' ? 'selected' : '' }} class="bg-slate-900 text-white">Semua Program</option>
                @foreach ($uniquePrograms as $prog)
                    <option value="{{ $prog->program_kode }}" {{ request('program') === $prog->program_kode ? 'selected' : '' }} class="bg-slate-900 text-white">
                        {{ $prog->program_kode }} - {{ $prog->program_nama }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Selector Jumlah Baris (Per Page) -->
        <div class="flex items-center space-x-1.5 bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-400 shrink-0">
            <span class="text-[10px] uppercase font-bold text-slate-500">Baris:</span>
            <select name="per_page" onchange="this.form.submit()" class="bg-transparent text-emerald-400 font-bold focus:outline-none cursor-pointer text-xs">
                <option value="10" {{ request('per_page', '15') == '10' ? 'selected' : '' }} class="bg-slate-900 text-white">10</option>
                <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }} class="bg-slate-900 text-white">15</option>
                <option value="25" {{ request('per_page', '15') == '25' ? 'selected' : '' }} class="bg-slate-900 text-white">25</option>
                <option value="50" {{ request('per_page', '15') == '50' ? 'selected' : '' }} class="bg-slate-900 text-white">50</option>
                <option value="100" {{ request('per_page', '15') == '100' ? 'selected' : '' }} class="bg-slate-900 text-white">100</option>
            </select>
        </div>

        <!-- Action Buttons & Counter Badge -->
        <div class="flex items-center space-x-2 shrink-0 w-full sm:w-auto">
            <button type="submit" class="px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Cari</span>
            </button>

            @if (request('search') || (request('program') && request('program') !== 'all'))
                <a href="{{ route('master.jenis_pengadaan') }}" class="px-3.5 py-2.5 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all shrink-0">
                    🔄 Reset
                </a>
            @endif
        </div>

    </form>
</div>
