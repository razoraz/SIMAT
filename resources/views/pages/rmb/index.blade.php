<x-layout title="Rekonsiliasi Belanja Modal (RMB) - SIMAT-RK">
    @section('page-title', 'Rekonsiliasi Belanja Modal (RMB)')
    @section('breadcrumb', 'Audit & Pemulihan / Rekonsiliasi Belanja Modal')

    <div class="space-y-6 pb-12" x-data="rmbDashboard()" x-cloak>
        {{-- 1. Header & Filter Bar --}}
        @include('pages.rmb.partials.header')

        {{-- 2. KPI Cards Ringkasan Rekonsiliasi --}}
        @include('pages.rmb.partials.kpi_cards')

        {{-- 3. Tab Navigasi: Tabel 21 Kolom vs Kertas Kerja Rekon --}}
        <div class="flex items-center justify-between border-b border-slate-800/80 pb-3 gap-4 flex-wrap">
            <div class="flex items-center p-1 rounded-2xl bg-slate-950/80 border border-slate-800/80 backdrop-blur-sm">
                <button type="button" @click="activeTab = 'tabel_21'"
                        class="flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition-all"
                        :class="activeTab === 'tabel_21' 
                            ? 'bg-gradient-to-r from-emerald-500/20 to-teal-500/20 text-emerald-300 border border-emerald-500/30 shadow-md shadow-emerald-500/10' 
                            : 'text-slate-400 hover:text-white'">
                    <span>📊</span>
                    <span>Format 21 Kolom BPKAD</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold bg-slate-900 border border-slate-700 text-slate-300">Penambahan &amp; Pengurangan</span>
                </button>

                <button type="button" @click="activeTab = 'kertas_kerja'"
                        class="flex items-center space-x-2 px-4 py-2 rounded-xl text-xs font-bold transition-all ml-1"
                        :class="activeTab === 'kertas_kerja' 
                            ? 'bg-gradient-to-r from-cyan-500/20 to-blue-500/20 text-cyan-300 border border-cyan-500/30 shadow-md shadow-cyan-500/10' 
                            : 'text-slate-400 hover:text-white'">
                    <span>⚖️</span>
                    <span>Kertas Kerja Rekon Kasda</span>
                    <span class="px-1.5 py-0.5 rounded text-[10px] font-mono font-bold"
                          :class="isBalance ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'"
                          x-text="isBalance ? 'BALANCE' : 'SELISIH'"></span>
                </button>
            </div>

            <div class="flex items-center space-x-2">
                <button type="button" @click="toggleAllAccordion()"
                        class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-[11px] font-semibold text-slate-300 hover:text-white transition-all flex items-center gap-1.5">
                    <span x-text="allExpanded ? '🔼 Ciutkan Semua' : '🔽 Bentangkan Semua KIB'"></span>
                </button>
                <button type="button" @click="exportToExcel()"
                        class="px-3.5 py-1.5 rounded-xl bg-emerald-600/20 border border-emerald-500/40 hover:bg-emerald-600/30 text-[11px] font-bold text-emerald-300 hover:text-emerald-200 transition-all flex items-center gap-1.5 shadow-sm">
                    <span>📥</span>
                    <span>Ekspor Excel Resmi RMB</span>
                </button>
            </div>
        </div>

        {{-- 4. Konten Tab 1: Format 21 Kolom BPKAD --}}
        <div x-show="activeTab === 'tabel_21'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1">
            @include('pages.rmb.partials.table_21_kolom')
        </div>

        {{-- 5. Konten Tab 2: Kertas Kerja Rekonsiliasi Kasda --}}
        <div x-show="activeTab === 'kertas_kerja'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1">
            @include('pages.rmb.partials.table_kertas_kerja')
        </div>

        {{-- 6. Scripts --}}
        @include('pages.rmb.partials.scripts')
    </div>
</x-layout>
