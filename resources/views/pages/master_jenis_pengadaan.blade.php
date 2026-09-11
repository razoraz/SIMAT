<x-layout title="Jenis Pengadaan SIPD - SIMAT-RK">
    @section('page-title', 'Master Jenis Pengadaan (SIPD)')
    @section('breadcrumb', 'Master Data System / Jenis Pengadaan SIPD')

    @include('pages.master_jenis_pengadaan_partials.scripts')

    <div x-data="masterJenisPengadaan()" x-cloak>
        @include('pages.master_jenis_pengadaan_partials.header_banner')
        @include('pages.master_jenis_pengadaan_partials.search_bar')
        @include('pages.master_jenis_pengadaan_partials.table_pengadaan')

        <!-- MODALS & POPUPS -->
        @include('pages.master_jenis_pengadaan_partials.modal_tambah')
        @include('pages.master_jenis_pengadaan_partials.modal_edit')
        @include('pages.master_jenis_pengadaan_partials.modal_confirm')
        @include('pages.master_jenis_pengadaan_partials.toast')
    </div>
</x-layout>
