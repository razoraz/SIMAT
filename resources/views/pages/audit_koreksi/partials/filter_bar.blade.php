{{-- FILTER BAR & LIVE SEARCH MODUL AUDIT KOREKSI NILAI BMD --}}
<div class="rounded-3xl bg-slate-900/90 border border-slate-800 p-4 sm:p-5 shadow-xl backdrop-blur-xl">
    <style>
        .audit-filter-form {
            display: flex;
            flex-direction: column;
            align-items: stretch;
            gap: 0.875rem;
            width: 100%;
        }
        .audit-filter-year,
        .audit-filter-tw,
        .audit-filter-search {
            width: 100%;
            min-width: 0;
        }
        .audit-filter-actions {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
        }
        @media (max-width: 639px) {
            .audit-filter-submit {
                flex: 1 1 0%;
            }
        }
        @media (min-width: 640px) and (max-width: 1023px) {
            .audit-filter-form {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                align-items: end;
                gap: 0.875rem;
            }
            .audit-filter-search,
            .audit-filter-actions {
                grid-column: span 2 / span 2;
            }
            .audit-filter-submit {
                min-width: 150px;
            }
        }
        @media (min-width: 1024px) {
            .audit-filter-form {
                display: flex;
                flex-direction: row;
                align-items: flex-end;
                gap: 0.875rem;
            }
            .audit-filter-year {
                width: 145px;
                flex-shrink: 0;
            }
            .audit-filter-tw {
                width: 220px;
                flex-shrink: 0;
            }
            .audit-filter-search {
                flex: 1 1 0%;
                min-width: 180px;
            }
            .audit-filter-actions {
                width: auto;
                flex-shrink: 0;
                justify-content: flex-end;
            }
        }
    </style>

    <form method="GET" action="{{ route('audit_koreksi.index') }}" class="audit-filter-form">
        {{-- 1. Filter Tahun Anggaran --}}
        <div class="audit-filter-year">
            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5 flex items-center gap-1.5 whitespace-nowrap">
                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>Tahun Anggaran</span>
            </label>
            <select name="tahun" onchange="this.form.submit()"
                    class="w-full bg-slate-950/90 border border-slate-800 text-white rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 font-mono transition-all cursor-pointer shadow-inner">
                @foreach ($availableYears as $yr)
                    <option value="{{ $yr }}" {{ (int) $selectedYear === (int) $yr ? 'selected' : '' }}>T.A. {{ $yr }}</option>
                @endforeach
            </select>
        </div>

        {{-- 2. Filter Periode Triwulan --}}
        <div class="audit-filter-tw">
            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5 flex items-center gap-1.5 whitespace-nowrap">
                <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>Periode Triwulan</span>
            </label>
            <select name="triwulan" onchange="this.form.submit()"
                    class="w-full bg-slate-950/90 border border-slate-800 text-white rounded-xl px-3 py-2.5 text-xs focus:ring-2 focus:ring-cyan-500/40 focus:border-cyan-500 transition-all cursor-pointer shadow-inner">
                <option value="all" {{ $selectedTriwulan === 'all' ? 'selected' : '' }}>Semua Triwulan (1 - 4)</option>
                <option value="1" {{ (string) $selectedTriwulan === '1' ? 'selected' : '' }}>Triwulan 1 (Jan - Mar)</option>
                <option value="2" {{ (string) $selectedTriwulan === '2' ? 'selected' : '' }}>Triwulan 2 (Apr - Jun)</option>
                <option value="3" {{ (string) $selectedTriwulan === '3' ? 'selected' : '' }}>Triwulan 3 (Jul - Sep)</option>
                <option value="4" {{ (string) $selectedTriwulan === '4' ? 'selected' : '' }}>Triwulan 4 (Okt - Des)</option>
            </select>
        </div>

        {{-- 3. Pencarian Cepat --}}
        <div class="audit-filter-search">
            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5 flex items-center gap-1.5 whitespace-nowrap">
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <span>Pencarian Cepat</span>
            </label>
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari NIBAR, Nama Aset, Dokumen BA / LHP BPK..."
                       class="w-full bg-slate-950/90 border border-slate-800 text-white rounded-xl pl-9 pr-3 py-2.5 text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-500 transition-all shadow-inner">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        {{-- 4. Tombol Filter & Reset --}}
        <div class="audit-filter-actions">
            <button type="submit"
                    class="audit-filter-submit px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 active:bg-slate-900 border border-slate-700/80 text-white text-xs font-bold transition-all shadow-md flex items-center justify-center gap-1.5 hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                </svg>
                <span>Filter</span>
            </button>

            @if ($search !== '' || $selectedTriwulan !== 'all')
                <a href="{{ route('audit_koreksi.index', ['tahun' => $selectedYear]) }}"
                   title="Reset seluruh filter pencarian"
                   class="px-3 py-2.5 rounded-xl bg-slate-950 hover:bg-rose-950/50 border border-slate-800 hover:border-rose-500/40 text-slate-400 hover:text-rose-300 text-xs font-bold transition-all flex items-center justify-center hover:-translate-y-0.5 active:scale-95 cursor-pointer shadow-sm shrink-0">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
            @endif
        </div>
    </form>
</div>