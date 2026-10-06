<x-layout title="Audit Koreksi Nilai BMD - SIMAT-RK">
    @section('page-title', 'Audit Koreksi Nilai BMD')
    @section('breadcrumb', 'Audit & Pemulihan / Audit Koreksi Nilai BMD')

    <div class="space-y-6 pb-12" x-data="auditKoreksiDashboard()" x-cloak>
        {{-- 1. Header & Filter Bar --}}
        @include('pages.audit_koreksi.partials.header')

        {{-- 2. KPI Cards Ringkasan Koreksi Nilai (Biasa, LKD, Manset) --}}
        @include('pages.audit_koreksi.partials.kpi_cards')

        {{-- 3. Tab Navigasi Khusus 3 Sub-Koreksi BMD --}}
        @include('pages.audit_koreksi.partials.tabs_nav')

        {{-- 4. Konten Tab 1: Semua Koreksi (Komprehensif) --}}
        <div x-show="activeTab === 'semua'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1">
            @include('pages.audit_koreksi.partials.tab_semua')
        </div>

        {{-- 5. Konten Tab 2: Koreksi Biasa (Internal RSUD - Kolom 5 & 15 RMB) --}}
        <div x-show="activeTab === 'biasa'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1">
            @include('pages.audit_koreksi.partials.tab_biasa')
        </div>

        {{-- 6. Konten Tab 3: Koreksi LKD (Temuan BPK RI - Kolom 6 & 16 RMB) --}}
        <div x-show="activeTab === 'lkd'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1">
            @include('pages.audit_koreksi.partials.tab_lkd')
        </div>

        {{-- 7. Konten Tab 4: Koreksi Manset (E-Manset BPKAD - Kolom 7 & 17 RMB) --}}
        <div x-show="activeTab === 'manset'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1">
            @include('pages.audit_koreksi.partials.tab_manset')
        </div>

        {{-- 8. Modal Detail & Komparasi Audit Koreksi --}}
        @include('pages.audit_koreksi.partials.modal_detail')

        {{-- 9. Scripts & Alpine Dashboard Logic --}}
        @include('pages.audit_koreksi.partials.scripts')
    </div>
</x-layout>
