<!-- Search Bar & Counter -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
    <form method="GET" action="{{ route('master.rekening_belanja') }}" class="flex flex-col sm:flex-row items-center gap-3 w-full">
        <div class="relative flex-1 w-full">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode rekening / nama belanja pengadaan SIPD... (Tekan Enter)"
                class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-blue-500 transition-all">
            <svg class="w-4 h-4 text-blue-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            @if (request('search'))
                <a href="{{ route('master.rekening_belanja') }}" class="absolute right-3.5 top-3 text-slate-500 hover:text-white text-xs font-bold">&times;</a>
            @endif
        </div>

        <div class="flex items-center space-x-2 shrink-0">
            <span class="px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-[11px] font-semibold text-slate-300">
                Menampilkan <span class="text-blue-400 font-bold">{{ count($rekeningList) }}</span> dari <span class="text-white font-bold">{{ $totalCount }}</span> Akun Belanja
            </span>
            <button type="button" @click="resetFilters()"
                class="px-3 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                🔄 Reset
            </button>
        </div>
    </form>
</div>
