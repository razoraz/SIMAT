<!-- Filter, Quick Tabs & Search Bar Full-Width -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl mb-6">
    <form method="GET" action="{{ route('master.jenis_astap') }}" class="flex flex-col gap-4">
        
        <!-- Quick Filter Jenis Utama Tabs -->
        <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mr-1 shrink-0">Kategori Aset:</span>
            <a href="{{ route('master.jenis_astap', ['search' => request('search')]) }}"
                class="px-3 py-1.5 rounded-xl transition-all {{ request('jenis', 'all') === 'all' ? 'bg-emerald-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                Semua Aset
            </a>
            <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.1', 'search' => request('search')]) }}"
                class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.1' ? 'bg-amber-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                <span>🌾 Aset Tanah</span>
            </a>
            <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.2', 'search' => request('search')]) }}"
                class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.2' ? 'bg-cyan-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                <span>🔬 Aset Peralatan dan Mesin</span>
            </a>
            <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.3', 'search' => request('search')]) }}"
                class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.3' ? 'bg-purple-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                <span>🏢 Aset Gedung & Bangunan</span>
            </a>
            <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.4', 'search' => request('search')]) }}"
                class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.4' ? 'bg-teal-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                <span>🚰 Aset Jalan, Irigasi dan Jaringan</span>
            </a>
            <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.5', 'search' => request('search')]) }}"
                class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.5' ? 'bg-orange-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                <span>📦 Aset Tetap Lainnya</span>
            </a>
            <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.6', 'search' => request('search')]) }}"
                class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.6' ? 'bg-rose-500 text-slate-950 font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                <span>🏗️ Aset Konstruksi Dalam Pengerjaan</span>
            </a>
            <a href="{{ route('master.jenis_astap', ['jenis' => '1.5.3', 'search' => request('search')]) }}"
                class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.5.3' ? 'bg-indigo-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                <span>💾 Aset Tidak Berwujud</span>
            </a>
            <a href="{{ route('master.jenis_astap', ['jenis' => '1.3.7', 'search' => request('search')]) }}"
                class="px-3 py-1.5 rounded-xl transition-all flex items-center space-x-1.5 {{ request('jenis') === '1.3.7' ? 'bg-pink-500 text-white font-extrabold shadow-md' : 'bg-slate-950 text-slate-400 hover:text-white border border-slate-800' }}">
                <span>🔨 Aset Tetap Dalam Renovasi</span>
            </a>
        </div>

        @if(request('jenis'))
            <input type="hidden" name="jenis" value="{{ request('jenis') }}">
        @endif

        <!-- Search Bar & Counter -->
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full pt-2 border-t border-slate-800/80">
            <div class="relative flex-1 w-full">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari kode 108 / nama jenis / sub rincian / uraian spesifik barang..."
                    class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-3 pl-11 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-all">
                <svg class="w-4 h-4 text-emerald-400 absolute left-4 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </div>

            <div class="flex items-center space-x-2 shrink-0">
                <button type="submit"
                    class="px-4 py-2.5 rounded-xl bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-bold text-xs transition-all flex items-center space-x-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Cari</span>
                </button>

                <a href="{{ route('master.jenis_astap') }}"
                    class="px-3 py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white text-xs font-semibold border border-slate-700 transition-all">
                    🔄 Reset
                </a>
            </div>
        </div>
    </form>
</div>
