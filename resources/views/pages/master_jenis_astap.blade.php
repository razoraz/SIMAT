<x-layout title="Jenis ASTAP Kode 108 BMD - SIMAT-RK">
    @section('page-title', 'Master Jenis ASTAP (Kode 108 BMD)')
    @section('breadcrumb', 'Master Data System / Jenis ASTAP Kode 108')

    @include('pages.master_jenis_astap_partials.scripts')

    <div x-data="masterJenisAstap()" x-cloak>
        @include('pages.master_jenis_astap_partials.header_banner')
        @include('pages.master_jenis_astap_partials.filter_bar')
        @include('pages.master_jenis_astap_partials.table_kode108')

        <!-- MODALS & POPUPS -->
        @include('pages.master_jenis_astap_partials.modal_tambah')
        @include('pages.master_jenis_astap_partials.modal_edit')
        @include('pages.master_jenis_astap_partials.modal_import')
        @include('pages.master_jenis_astap_partials.modal_confirm')
        @include('pages.master_jenis_astap_partials.toast')
    </div>
</x-layout>