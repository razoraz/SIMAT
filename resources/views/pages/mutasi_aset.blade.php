<x-layout title="Mutasi Aset - SIMAT-RK">
    @section('page-title', 'Mutasi Aset')
    @section('breadcrumb', 'Master Utama / Mutasi Aset')

    @include('pages.mutasi_aset_partials.scripts')

    <div x-data="mutasiCatalog()" x-cloak>
        <!-- KONTEN UTAMA KATALOG MUTASI (DISEMBUNYIKAN SAAT DICETAK) -->
        <div class="no-print space-y-6">
            @include('pages.mutasi_aset_partials.header_kpi')
            @include('pages.mutasi_aset_partials.filter_bar')
            @include('pages.mutasi_aset_partials.table_mutasi')
        </div>

        <!-- MODALS & POPUPS -->
        @include('pages.mutasi_aset_partials.modal_detail')
        @include('pages.mutasi_aset_partials.modal_reject')
    </div>
</x-layout>
