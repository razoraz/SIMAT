<x-layout title="Kelola Data Aset Kemitraan (Akun 1.5.2) - SIMAT-RK">
    @section('page-title', 'Kelola Data Aset Kemitraan Pihak Ketiga (KSO, BGS, Sewa)')
    @section('breadcrumb', 'Master Aset / Kelola Kemitraan Aset')

    @include('pages.master_kemitraan_partials.scripts')

    <div x-data="masterKemitraan()" x-cloak class="space-y-6 pb-16">
        <!-- HEADER BANNER & 4 KARTU STATISTIK KPI -->
        @include('pages.master_kemitraan_partials.header_banner')

        <!-- FILTER BAR & PENCARIAN -->
        @include('pages.master_kemitraan_partials.filter_bar')

        <!-- TABEL DATA MASTER ASET KEMITRAAN -->
        @include('pages.master_kemitraan_partials.table_kemitraan')

        <!-- MODAL DETAIL & STATUS KONSESI -->
        @include('pages.master_kemitraan_partials.modal_detail_kemitraan')

        <!-- MODAL KONFIRMASI HAPUS / BATALKAN -->
        @include('pages.master_kemitraan_partials.modal_confirm')
    </div>
</x-layout>
