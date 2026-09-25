<x-layout title="Rekening Belanja SIPD - SIMAT-RK">
    @section('page-title', 'Master Rekening Belanja SIPD')
    @section('breadcrumb', 'Master Data System / Rekening Belanja SIPD')

    @include('pages.master.rekening_belanja.partials.scripts')

    <div x-data="masterRekeningBelanja()" x-cloak>
        @include('pages.master.rekening_belanja.partials.header_banner')
        @include('pages.master.rekening_belanja.partials.search_bar')
        @include('pages.master.rekening_belanja.partials.table_rekening')

        <!-- MODALS & POPUPS -->
        @include('pages.master.rekening_belanja.partials.modal_tambah')
        @include('pages.master.rekening_belanja.partials.modal_edit')
        @include('pages.master.rekening_belanja.partials.modal_confirm')
        @include('pages.master.rekening_belanja.partials.toast')
    </div>
</x-layout>
