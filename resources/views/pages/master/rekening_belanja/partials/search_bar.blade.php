<!-- Search & Filter Bar -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-3.5 sm:p-5 shadow-xl mb-6 overflow-hidden">
    <style>
        .rekening-search-form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0.625rem;
            width: 100%;
            min-width: 0;
        }
        .rekening-search-input-wrap {
            width: 100%;
            min-width: 0;
        }
        .rekening-filter-group {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            width: 100%;
            min-width: 0;
        }
        .rekening-kelompok-box {
            display: flex;
            align-items: center;
            flex: 1 1 200px;
            min-width: 0;
            max-width: 100%;
        }
        .rekening-kelompok-select {
            width: 100%;
            min-width: 0;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .rekening-baris-box {
            flex: 0 0 auto;
        }
        .rekening-search-actions {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: flex-end;
            gap: 0.5rem;
            flex: 1 1 auto;
            min-width: 0;
        }
        @media (min-width: 1024px) {
            .rekening-search-form {
                flex-direction: row;
                flex-wrap: wrap;
                align-items: center;
                gap: 0.75rem;
            }
            .rekening-search-input-wrap {
                flex: 1 1 240px;
            }
            .rekening-filter-group {
                flex-wrap: wrap;
                justify-content: flex-end;
                width: auto;
                flex: 0 1 auto;
            }
            .rekening-kelompok-box {
                flex: 0 1 240px;
                max-width: 240px;
            }
            .rekening-search-actions {
                flex: 0 0 auto;
            }
        }
        @media (min-width: 1280px) {
            .rekening-search-form {
                flex-wrap: nowrap;
            }
            .rekening-filter-group {
                flex-wrap: nowrap;
                flex-shrink: 0;
            }
        }
    </style>

    <form method="GET" action="{{ route('master.rekening_belanja') }}" class="rekening-search-form text-xs">
        
        <!-- Search Input Field -->
        <div class="rekening-search-input-wrap relative">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode rekening / nama belanja pengadaan SIPD... (Tekan Enter)"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 sm:py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all">
            <svg class="w-4 h-4 text-blue-400 absolute left-4 top-3 sm:top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            @if (request('search'))
                <a href="{{ route('master.rekening_belanja', ['kelompok' => request('kelompok'), 'per_page' => request('per_page')]) }}" class="absolute right-3.5 top-2.5 sm:top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</a>
            @endif
        </div>

        <!-- Filter & Action Group (Wrap Aman di Mobile & Medium) -->
        <div class="rekening-filter-group">
            <!-- Filter Dropdown Kelompok Akun Belanja -->
            <div class="rekening-kelompok-box space-x-1.5 bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-400">
                <span class="text-[10px] uppercase font-bold text-slate-500 shrink-0">Kelompok:</span>
                <select name="kelompok" onchange="this.form.submit()" class="rekening-kelompok-select bg-transparent text-blue-400 font-bold focus:outline-none cursor-pointer text-xs">
                    <option value="all" {{ request('kelompok', 'all') === 'all' ? 'selected' : '' }} class="bg-slate-900 text-white">Semua Kelompok</option>
                    @foreach ($uniqueKelompok as $kel)
                        <option value="{{ $kel['kelompok'] }}" {{ request('kelompok') === $kel['kelompok'] ? 'selected' : '' }} class="bg-slate-900 text-white">
                            {{ $kel['kelompok'] }} - {{ $kel['nama_kelompok'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Selector Jumlah Baris (Per Page) -->
            <div class="rekening-baris-box flex items-center space-x-1.5 bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-400">
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
            <div class="rekening-search-actions">
                <button type="submit" class="px-4 sm:px-5 py-2.5 rounded-2xl bg-blue-500 hover:bg-blue-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-blue-500/20 transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer shrink-0">
                    <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari</span>
                </button>

                @if (request('search') || (request('kelompok') && request('kelompok') !== 'all'))
                    <a href="{{ route('master.rekening_belanja') }}" class="px-3.5 py-2.5 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all shrink-0">
                        🔄 Reset
                    </a>
                @endif

                <span class="px-3 sm:px-3.5 py-2.5 rounded-2xl bg-slate-950 border border-slate-800 text-[10.5px] sm:text-[11px] font-semibold text-slate-300 shrink-0 max-w-full">
                    Menampilkan <span class="text-blue-400 font-bold">{{ $rekeningList->count() }}</span> dari <span class="text-white font-bold">{{ $rekeningList->total() }}</span> Akun
                </span>
            </div>
        </div>
    </form>
</div>
