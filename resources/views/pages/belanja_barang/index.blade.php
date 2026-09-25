<x-layout title="Kelola Belanja Barang (Instalasi Perbekalan / Gudang) - SIMAT-RK">
    @section('page-title', 'Kelola Belanja Barang (Instalasi Perbekalan / Gudang)')
    @section('breadcrumb', 'Master Aset / Kelola Belanja Barang')

    @include('pages.belanja_barang.index_partials.scripts')

    <div x-data="masterBelanjaBarang()" x-cloak class="space-y-6 pb-16">
        <!-- HEADER BANNER & 4 KARTU STATISTIK KPI -->
        @include('pages.belanja_barang.index_partials.header_banner')

        <!-- FILTER BAR & PENCARIAN -->
        @include('pages.belanja_barang.index_partials.filter_bar')

        <!-- TABEL DATA MASTER BELANJA BARANG -->
        @include('pages.belanja_barang.index_partials.table_belanja_barang')

        <!-- MODAL DETAIL BELANJA BARANG & NIBAR -->
        @include('pages.belanja_barang.index_partials.modal_detail')

        <!-- MODAL KONFIRMASI HAPUS / BATALKAN -->
        @include('pages.belanja_barang.index_partials.modal_confirm')
    </div>
</x-layout>
