<x-layout title="Rekonsiliasi Belanja Modal (RMB) - SIMAT-RK">
    @section('page-title', 'Rekonsiliasi Belanja Modal (RMB)')
    @section('breadcrumb', 'Audit & Pemulihan / Rekonsiliasi Belanja Modal')

    <div class="space-y-6 pb-12" x-data="rmbDashboard()" x-cloak>
        {{-- 1. Header & Filter Bar --}}
        @include('pages.rmb.partials.header')

        {{-- 2. KPI Cards Ringkasan Rekonsiliasi --}}
        @include('pages.rmb.partials.kpi_cards')

        {{-- 3. Bilah Kontrol Tabel 21 Kolom & Status Kertas Kerja --}}
        <style>
            .rmb-toolbar-wrap {
                display: flex;
                flex-direction: column;
                align-items: center;
                gap: 0.75rem;
            }
            .rmb-tabs-box {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: 0.35rem;
                max-width: 100%;
            }
            .rmb-toolbar-actions {
                display: flex;
                flex-wrap: wrap;
                align-items: center;
                justify-content: center;
                gap: 0.5rem;
                width: 100%;
            }
            @media (min-width: 1024px) {
                .rmb-toolbar-wrap {
                    flex-direction: row;
                    align-items: center;
                    justify-content: space-between;
                    gap: 1rem;
                }
                .rmb-tabs-box {
                    justify-content: flex-start;
                }
                .rmb-toolbar-actions {
                    justify-content: flex-end;
                    width: auto;
                }
            }
        </style>
        <div class="rmb-toolbar-wrap border-b border-slate-800/80 pb-3">
            <div class="rmb-tabs-box px-3 py-1.5 rounded-2xl bg-slate-950/80 border border-slate-800/80 backdrop-blur-sm">
                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-300">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    <span>Format 21 Kolom BPKAD &amp; Kertas Kerja Rekon</span>
                </span>
                <span class="px-2 py-0.5 rounded-md text-[10px] font-mono font-bold whitespace-nowrap"
                      :class="isBalance ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-rose-500/20 text-rose-300 border border-rose-500/30'"
                      x-text="isBalance ? 'BALANCE' : 'SELISIH'"></span>
            </div>

            <div class="rmb-toolbar-actions">
                <button type="button" @click="toggleAllAccordion()"
                        class="px-3 py-1.5 rounded-xl bg-slate-900 border border-slate-800 hover:border-slate-700 text-[11px] font-semibold text-slate-300 hover:text-white transition-all flex items-center gap-1.5 cursor-pointer">
                    <span x-text="allExpanded ? '🔼 Ciutkan Semua' : '🔽 Bentangkan Semua KIB'"></span>
                </button>
                <button type="button" @click="exportToExcel()"
                        class="px-3.5 py-1.5 rounded-xl bg-emerald-600/20 border border-emerald-500/40 hover:bg-emerald-600/30 text-[11px] font-bold text-emerald-300 hover:text-emerald-200 transition-all flex items-center gap-1.5 shadow-sm cursor-pointer">
                    <span>📥</span>
                    <span>Ekspor Excel Resmi RMB</span>
                </button>
            </div>
        </div>

        {{-- 4. Format 21 Kolom BPKAD --}}
        <div>
            @include('pages.rmb.partials.table_21_kolom')
        </div>

        {{-- 5. Lembar Rekonsiliasi Realisasi Belanja Modal (Tepat di Bawah Format 21 Kolom) --}}
        <div>
            @include('pages.rmb.partials.table_kertas_kerja')
        </div>

        {{-- 6. Scripts --}}
        @include('pages.rmb.partials.scripts')
    </div>
</x-layout>
