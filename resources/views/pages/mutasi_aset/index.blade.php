<x-layout title="Mutasi Internal - SIMAT-RK">
    @section('page-title', 'Mutasi Internal (Antar Ruangan)')
    @section('breadcrumb', 'Master Aset / Mutasi Internal')

    @include('pages.mutasi_aset.index_partials.scripts')

    <div x-data="mutasiCatalog()" x-cloak>
        <!-- KONTEN UTAMA KATALOG MUTASI (DISEMBUNYIKAN SAAT DICETAK) -->
        <div class="no-print space-y-6">
            @include('pages.mutasi_aset.index_partials.header_kpi')
            @include('pages.mutasi_aset.index_partials.filter_bar')
            @include('pages.mutasi_aset.index_partials.table_mutasi')
        </div>

        <!-- MODALS & POPUPS -->
        @include('pages.mutasi_aset.index_partials.modal_detail')
        @include('pages.mutasi_aset.index_partials.modal_reject')
    </div>
</x-layout>
