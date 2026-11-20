<x-layout title="Berita Acara (BAST) - SIMAT-RK">
    @section('page-title', 'Berita Acara (BAST)')
    @section('breadcrumb', 'Master Utama / Berita Acara (BAST)')

    @include('pages.berita_acara.partials.scripts')

    <div x-data="beritaAcaraApp()" x-cloak>
        @include('pages.berita_acara.partials.header_banner')
        @include('pages.berita_acara.partials.nav_tabs')

        {{-- Tab Contents --}}
        @include('pages.berita_acara.partials.tab_triwulan')
        @include('pages.berita_acara.partials.tab_distribusi')
        @include('pages.berita_acara.partials.tab_mutasi')
        @include('pages.berita_acara.partials.tab_hibah')
        @include('pages.berita_acara.partials.tab_kemitraan')
        @include('pages.berita_acara.partials.tab_belanja_barang')

        {{-- Detail Popups --}}
        @include('pages.berita_acara.partials.modal_detail_distribusi')
        @include('pages.berita_acara.partials.modal_detail_mutasi')
        @include('pages.berita_acara.partials.modal_detail_mutasi_eksternal')

        {{-- Print Document Modals --}}
        @include('pages.berita_acara.partials.modal_print_triwulan')
        @include('pages.berita_acara.partials.modal_print_distribusi')
        @include('pages.berita_acara.partials.modal_print_mutasi')
        @include('pages.berita_acara.partials.modal_print_hibah')
        @include('pages.berita_acara.partials.modal_print_kemitraan')
        @include('pages.berita_acara.partials.modal_print_belanja_barang')
    </div>
</x-layout>
