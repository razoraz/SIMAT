{{-- HEADER & FILTER BAR MODUL AUDIT KOREKSI NILAI BMD --}}
<div class="relative overflow-hidden rounded-3xl bg-slate-900/90 border border-slate-800 p-6 sm:p-8 backdrop-blur-xl shadow-2xl">
    {{-- Glow background subtle decoration --}}
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-cyan-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
        {{-- Sisi Kiri: Judul & Subjudul Resmi --}}
        <div class="space-y-2">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-800/80 border border-slate-700/60 text-[11px] font-semibold text-emerald-300">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span>Standar Kertas Kerja RMB 21 Kolom · BPKAD &amp; BPK RI</span>
            </div>
            
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-white flex items-center gap-3">
                <span>Audit &amp; Ledger Koreksi Nilai BMD</span>
            </h1>
            
            <p class="text-xs sm:text-sm text-slate-400 max-w-2xl leading-relaxed">
                Pusat pengawasan dan rekapitulasi 3 klaster penyesuaian nilai buku aset tetap: 
                <strong class="text-indigo-300">Koreksi Biasa</strong> (Internal), 
                <strong class="text-cyan-300">Koreksi LKD</strong> (Audit BPK), dan 
                <strong class="text-emerald-300">Koreksi Manset</strong> (Harmonisasi BPKAD) pada RSUD dr. H. Koesnandi.
            </p>
        </div>

        {{-- Sisi Kanan: Action Buttons (Ekspor CSV & Cetak) --}}
        <div class="flex items-center flex-wrap gap-2.5 shrink-0">
            <a href="{{ route('audit_koreksi.export', ['tahun' => $selectedYear, 'triwulan' => $selectedTriwulan, 'sub_koreksi' => $selectedSubKoreksi, 'tipe_koreksi' => $selectedTipe]) }}"
               class="px-4 py-2.5 rounded-xl bg-emerald-600/20 hover:bg-emerald-600/30 border border-emerald-500/30 text-emerald-300 hover:text-emerald-200 text-xs font-bold transition-all flex items-center gap-2 shadow-lg shadow-emerald-950/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Unduh Rekap CSV</span>
            </a>

            <button type="button" @click="window.print()"
                    class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700/80 border border-slate-700 text-slate-200 hover:text-white text-xs font-bold transition-all flex items-center gap-2 shadow-sm">
                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                </svg>
                <span>Cetak Lembar Audit</span>
            </button>
        </div>
    </div>

    {{-- Filter Bar Interaktif --}}
    <form method="GET" action="{{ route('audit_koreksi.index') }}" class="mt-6 pt-6 border-t border-slate-800/80 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3.5">
        {{-- 1. Filter Tahun --}}
        <div class="lg:col-span-2">
            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5">Tahun Anggaran</label>
            <select name="tahun" onchange="this.form.submit()"
                    class="w-full bg-slate-950/80 border border-slate-800 text-white rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 font-mono transition-all">
                @foreach ($availableYears as $yr)
                    <option value="{{ $yr }}" {{ (int) $selectedYear === (int) $yr ? 'selected' : '' }}>T.A. {{ $yr }}</option>
                @endforeach
            </select>
        </div>

        {{-- 2. Filter Triwulan --}}
        <div class="lg:col-span-2">
            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5">Periode Triwulan</label>
            <select name="triwulan" onchange="this.form.submit()"
                    class="w-full bg-slate-950/80 border border-slate-800 text-white rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                <option value="all" {{ $selectedTriwulan === 'all' ? 'selected' : '' }}>Semua Triwulan (1 - 4)</option>
                <option value="1" {{ (string) $selectedTriwulan === '1' ? 'selected' : '' }}>Triwulan 1 (Jan - Mar)</option>
                <option value="2" {{ (string) $selectedTriwulan === '2' ? 'selected' : '' }}>Triwulan 2 (Apr - Jun)</option>
                <option value="3" {{ (string) $selectedTriwulan === '3' ? 'selected' : '' }}>Triwulan 3 (Jul - Sep)</option>
                <option value="4" {{ (string) $selectedTriwulan === '4' ? 'selected' : '' }}>Triwulan 4 (Okt - Des)</option>
            </select>
        </div>

        {{-- 3. Filter Arah Koreksi --}}
        <div class="lg:col-span-2">
            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5">Arah Penyesuaian</label>
            <select name="tipe_koreksi" onchange="this.form.submit()"
                    class="w-full bg-slate-950/80 border border-slate-800 text-white rounded-xl px-3 py-2 text-xs focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                <option value="all" {{ $selectedTipe === 'all' ? 'selected' : '' }}>Semua Arah (+ &amp; -)</option>
                <option value="tambah" {{ $selectedTipe === 'tambah' ? 'selected' : '' }}>Bertambah (+) Kapitalisasi</option>
                <option value="kurang" {{ $selectedTipe === 'kurang' ? 'selected' : '' }}>Berkurang (-) Pengurangan</option>
            </select>
        </div>

        {{-- 4. Search Box Live --}}
        <div class="lg:col-span-4">
            <label class="block text-[10px] uppercase font-bold text-slate-400 tracking-wider mb-1.5">Pencarian Cepat</label>
            <div class="relative">
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari NIBAR, Nama Aset, Dokumen BA / LHP..."
                       class="w-full bg-slate-950/80 border border-slate-800 text-white rounded-xl pl-9 pr-3 py-2 text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-emerald-500/30 focus:border-emerald-500 transition-all">
                <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </div>
        </div>

        {{-- 5. Tombol Cari & Reset --}}
        <div class="lg:col-span-2 flex items-end gap-2">
            <button type="submit"
                    class="flex-1 px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-white text-xs font-bold transition-all text-center">
                Filter
            </button>
            @if ($search !== '' || $selectedTriwulan !== 'all' || $selectedTipe !== 'all')
                <a href="{{ route('audit_koreksi.index', ['tahun' => $selectedYear]) }}"
                   title="Reset filter pencarian"
                   class="px-2.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 border border-slate-800 text-slate-400 hover:text-rose-400 text-xs font-bold transition-all">
                    ✕
                </a>
            @endif
        </div>
    </form>
</div>
