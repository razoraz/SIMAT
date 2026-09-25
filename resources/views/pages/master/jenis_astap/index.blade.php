<x-layout title="Jenis ASTAP Kode 108 BMD - SIMAT-RK">
    @section('page-title', 'Master Jenis ASTAP (Kode 108 BMD)')
    @section('breadcrumb', 'Master Data System / Jenis ASTAP Kode 108')

    @include('pages.master.jenis_astap.partials.scripts')

    <div x-data="masterJenisAstap()" x-cloak>
        @include('pages.master.jenis_astap.partials.header_banner')
        @include('pages.master.jenis_astap.partials.filter_bar')
        @include('pages.master.jenis_astap.partials.table_kode108')

        <!-- MODALS & POPUPS -->
        @include('pages.master.jenis_astap.partials.modal_tambah')
        @include('pages.master.jenis_astap.partials.modal_edit')
        @include('pages.master.jenis_astap.partials.modal_import')
        @include('pages.master.jenis_astap.partials.modal_confirm')
        @include('pages.master.jenis_astap.partials.toast')
    </div>
</x-layout>
