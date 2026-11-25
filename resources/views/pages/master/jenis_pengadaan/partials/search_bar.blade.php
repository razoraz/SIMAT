<!-- Search & Filter Bar -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-3.5 sm:p-5 shadow-xl mb-6 overflow-hidden">
    <style>
        .pengadaan-search-form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0.625rem;
            width: 100%;
            min-width: 0;
        }
        .pengadaan-filter-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            width: 100%;
            min-width: 0;
        }
        .pengadaan-program-box {
            display: flex;
            align-items: center;
            flex: 1 1 200px;
            min-width: 0;
            max-width: 100%;
        }
        .pengadaan-program-select {
            width: 100%;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .pengadaan-baris-box {
            flex: 0 0 auto;
        }
        .pengadaan-search-actions {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex: 0 0 auto;
            margin-left: auto;
        }
        @media (min-width: 1024px) {
            .pengadaan-search-form {
                flex-direction: row;
                align-items: center;
                gap: 0.75rem;
            }
            .pengadaan-filter-group {
                flex-wrap: nowrap;
                justify-content: flex-end;
                width: auto;
                flex-shrink: 0;
            }
            .pengadaan-program-box {
                flex: 0 1 240px;
                max-width: 240px;
            }
            .pengadaan-search-actions {
                margin-left: 0;
            }
        }
    </style>

    <form method="GET" action="{{ route('master.jenis_pengadaan') }}" class="pengadaan-search-form text-xs">
        
        <!-- Search Input Field -->
        <div class="relative flex-1 w-full min-w-0">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / nama program, kegiatan, atau sub kegiatan SIPD... (Tekan Enter)"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 sm:py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
            <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3 sm:top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            @if (request('search'))
                <a href="{{ route('master.jenis_pengadaan', ['program' => request('program'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-2.5 sm:top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</a>
            @endif
        </div>

        <!-- Filter & Action Group (Wrap Aman di Mobile & Medium) -->
        <div class="pengadaan-filter-group">
            <!-- Filter Dropdown Program SIPD -->
            <div class="pengadaan-program-box space-x-1.5 bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-400">
                <span class="text-[10px] uppercase font-bold text-slate-500 shrink-0">Program:</span>
                <select name="program" onchange="this.form.submit()" class="pengadaan-program-select bg-transparent text-emerald-400 font-bold focus:outline-none cursor-pointer text-xs">
                    <option value="all" {{ request('program', 'all') === 'all' ? 'selected' : '' }} class="bg-slate-900 text-white">Semua Program</option>
                    @foreach ($uniquePrograms as $prog)
                        <option value="{{ $prog->program_kode }}" {{ request('program') === $prog->program_kode ? 'selected' : '' }} class="bg-slate-900 text-white">
                            {{ $prog->program_kode }} - {{ $prog->program_nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Selector Jumlah Baris (Per Page) -->
            <div class="pengadaan-baris-box flex items-center space-x-1.5 bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-400">
                <span class="text-[10px] uppercase font-bold text-slate-500">Baris:</span>
                <select name="per_page" onchange="this.form.submit()" class="bg-transparent text-emerald-400 font-bold focus:outline-none cursor-pointer text-xs">
                    <option value="10" {{ request('per_page', '15') == '10' ? 'selected' : '' }} class="bg-slate-900 text-white">10</option>
                    <option value="15" {{ request('per_page', '15') == '15' ? 'selected' : '' }} class="bg-slate-900 text-white">15</option>
                    <option value="25" {{ request('per_page', '15') == '25' ? 'selected' : '' }} class="bg-slate-900 text-white">25</option>
                    <option value="50" {{ request('per_page', '15') == '50' ? 'selected' : '' }} class="bg-slate-900 text-white">50</option>
                    <option value="100" {{ request('per_page', '15') == '100' ? 'selected' : '' }} class="bg-slate-900 text-white">100</option>
                </select>
            </div>

            <!-- Action Buttons -->
            <div class="pengadaan-search-actions">
                <button type="submit" class="px-4 sm:px-5 py-2.5 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari</span>
                </button>

                @if (request('search') || (request('program') && request('program') !== 'all'))
                    <a href="{{ route('master.jenis_pengadaan') }}" class="px-3.5 py-2.5 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all shrink-0">
                        🔄 Reset
                    </a>
                @endif
            </div>
        </div>

    </form>
</div>
