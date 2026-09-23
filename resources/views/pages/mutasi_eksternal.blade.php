<x-layout title="Mutasi Eksternal (Antar-OPD) - SIMAT-RK" :fullWidth="true">
    @section('page-title', 'Mutasi Eksternal (Antar-OPD)')
    @section('breadcrumb', 'Master Aset / Mutasi Eksternal')

    @include('pages.mutasi_eksternal_partials.scripts')

    <div x-data="mutasiEksternalCatalog()" x-cloak>
        <!-- KONTEN UTAMA KATALOG MUTASI EKSTERNAL -->
        <div class="no-print space-y-6">
            @include('pages.mutasi_eksternal_partials.header_kpi')
            @include('pages.mutasi_eksternal_partials.filter_bar')
            @include('pages.mutasi_eksternal_partials.table_mutasi')
        </div>

        <!-- MODAL DETAIL BAST ANTAR-OPD -->
        @include('pages.mutasi_eksternal_partials.modal_detail')
    </div>
</x-layout>
