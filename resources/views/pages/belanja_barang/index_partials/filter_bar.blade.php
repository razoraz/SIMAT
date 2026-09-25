<!-- ========================================================================= -->
<!-- FILTER BAR & PENCARIAN MASTER BELANJA BARANG                               -->
<!-- ========================================================================= -->
<div class="p-4 sm:p-5 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl">
    <form method="GET" action="{{ route('master.belanja_barang') }}" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
            
            <!-- Filter 1: Pencarian Keyword (Col 5) -->
            <div class="lg:col-span-6">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">
                    Pencarian Barang / Faktur / Toko
                </label>
                <div class="relative">
                    <input type="text" name="search" value="{{ $search ?? '' }}"
                        placeholder="Ketik nama barang, nomor faktur, toko penyedia, NIBAR, atau ruangan..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-950 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-xs text-white placeholder-slate-500 transition-all font-medium">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    @if(!empty($search))
                        <a href="{{ route('master.belanja_barang', array_merge(request()->except('search'))) }}" 
                           class="absolute right-3 top-2.5 text-slate-500 hover:text-white text-xs font-bold"
                           title="Hapus pencarian">✕</a>
                    @endif
                </div>
            </div>

            <!-- Filter 2: Tahun Perolehan (Col 3) -->
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">
                    Tahun Perolehan
                </label>
                <select name="tahun" onchange="this.form.submit()"
                    class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-xs text-white font-medium transition-all cursor-pointer">
                    <option value="all">Semua Tahun</option>
                    @php
                        $currentYear = (int) date('Y');
                        $yearRange = range($currentYear + 1, 2020);
                    @endphp
                    @foreach($yearRange as $y)
                        <option value="{{ $y }}" {{ ($filterTahun ?? '') == $y ? 'selected' : '' }}>
                            Tahun {{ $y }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter 3: Triwulan (Col 3) -->
            <div class="lg:col-span-3">
                <label class="block text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5">
                    Periode Triwulan
                </label>
                <div class="flex items-center gap-2">
                    <select name="triwulan" onchange="this.form.submit()"
                        class="w-full px-3 py-2.5 rounded-xl bg-slate-950 border border-slate-800 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500/50 text-xs text-white font-medium transition-all cursor-pointer">
                        <option value="all">Semua Triwulan</option>
                        <option value="TW I" {{ ($filterTw ?? '') === 'TW I' ? 'selected' : '' }}>Triwulan I (TW I)</option>
                        <option value="TW II" {{ ($filterTw ?? '') === 'TW II' ? 'selected' : '' }}>Triwulan II (TW II)</option>
                        <option value="TW III" {{ ($filterTw ?? '') === 'TW III' ? 'selected' : '' }}>Triwulan III (TW III)</option>
                        <option value="TW IV" {{ ($filterTw ?? '') === 'TW IV' ? 'selected' : '' }}>Triwulan IV (TW IV)</option>
                    </select>

                    <button type="submit"
                        class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold shrink-0 transition-all shadow-md shadow-indigo-600/20 active:scale-95">
                        Filter
                    </button>
                </div>
            </div>

        </div>

        <!-- Filter Reset Link jika aktif -->
        @if(($filterTahun && $filterTahun !== 'all') || ($filterTw && $filterTw !== 'all') || !empty($search))
            <div class="flex items-center justify-between pt-2 border-t border-slate-800/80 text-xs">
                <div class="flex items-center gap-2 text-slate-400">
                    <span class="w-2 h-2 rounded-full bg-indigo-400 animate-pulse"></span>
                    <span>Filter aktif:</span>
                    @if($filterTahun && $filterTahun !== 'all')
                        <span class="px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-300 font-mono text-[11px] border border-indigo-500/20">Tahun: {{ $filterTahun }}</span>
                    @endif
                    @if($filterTw && $filterTw !== 'all')
                        <span class="px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-300 text-[11px] border border-indigo-500/20">{{ $filterTw }}</span>
                    @endif
                    @if(!empty($search))
                        <span class="px-2 py-0.5 rounded-md bg-indigo-500/10 text-indigo-300 text-[11px] border border-indigo-500/20">"{{ $search }}"</span>
                    @endif
                </div>

                <a href="{{ route('master.belanja_barang') }}"
                    class="text-indigo-400 hover:text-indigo-300 font-semibold transition-colors flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    <span>Reset Filter</span>
                </a>
            </div>
        @endif
    </form>
</div>
