<x-layout title="Jenis Pengadaan SIPD - SIMAT-RK">
    @section('page-title', 'Master Jenis Pengadaan (SIPD)')
    @section('breadcrumb', 'Master Data System / Jenis Pengadaan SIPD')

    @include('pages.master.jenis_pengadaan.partials.scripts')

    <div x-data="masterJenisPengadaan()" x-cloak>
        @include('pages.master.jenis_pengadaan.partials.header_banner')
        @include('pages.master.jenis_pengadaan.partials.search_bar')
        @include('pages.master.jenis_pengadaan.partials.table_pengadaan')

        <!-- MODALS & POPUPS -->
        @include('pages.master.jenis_pengadaan.partials.modal_tambah')
        @include('pages.master.jenis_pengadaan.partials.modal_edit')
        @include('pages.master.jenis_pengadaan.partials.modal_confirm')
        @include('pages.master.jenis_pengadaan.partials.toast')
    </div>
</x-layout>
