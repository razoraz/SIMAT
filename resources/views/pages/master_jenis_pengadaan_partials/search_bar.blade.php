<!-- Search Bar Only -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
    <form method="GET" action="{{ route('master.jenis_pengadaan') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full text-xs">
        
        <!-- Search Input Field -->
        <div class="relative flex-1 w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode / nama program, kegiatan, atau sub kegiatan SIPD... (Tekan Enter)"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
            <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            @if (request('search'))
                <a href="{{ route('master.jenis_pengadaan') }}" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</a>
            @endif
        </div>

        <!-- Action Buttons & Counter Badge -->
        <div class="flex items-center space-x-2 shrink-0 w-full sm:w-auto">
            <button type="submit" class="px-5 py-3 rounded-2xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs shadow-lg shadow-emerald-500/20 transition-all flex items-center space-x-1.5 active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <span>Cari</span>
            </button>

            <span class="px-3.5 py-3 rounded-2xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300 whitespace-nowrap">
                Menampilkan <span class="text-emerald-400 font-bold">{{ count($sipdList) }}</span> dari <span class="text-white font-bold">{{ $totalCount }}</span> Data SIPD
            </span>

            @if (request('search'))
                <button type="button" @click="resetFilters()" class="px-3.5 py-3 rounded-2xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all shrink-0">
                    🔄 Reset
                </button>
            @endif
        </div>

    </form>
</div>
