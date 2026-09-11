<x-layout title="Rekening Belanja SIPD - SIMAT-RK">
    @section('page-title', 'Master Rekening Belanja SIPD')
    @section('breadcrumb', 'Master Data System / Rekening Belanja SIPD')

    @include('pages.master_rekening_belanja_partials.scripts')

    <div x-data="masterRekeningBelanja()" x-cloak>
        @include('pages.master_rekening_belanja_partials.header_banner')
        @include('pages.master_rekening_belanja_partials.search_bar')
        @include('pages.master_rekening_belanja_partials.table_rekening')

        <!-- MODALS & POPUPS -->
        @include('pages.master_rekening_belanja_partials.modal_tambah')
        @include('pages.master_rekening_belanja_partials.modal_edit')
        @include('pages.master_rekening_belanja_partials.modal_confirm')
        @include('pages.master_rekening_belanja_partials.toast')
    </div>
</x-layout>
