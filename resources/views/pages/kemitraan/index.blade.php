<x-layout title="Kelola Data Aset Kemitraan (Akun 1.5.2) - SIMAT-RK">
    @section('page-title', 'Kelola Data Aset Kemitraan Pihak Ketiga (KSO, KSP, Sewa)')
    @section('breadcrumb', 'Master Aset / Kelola Kemitraan Aset')

    @include('pages.kemitraan.master_partials.scripts')

    <div x-data="masterKemitraan()" x-cloak class="space-y-6 pb-16">
        <!-- HEADER BANNER & 4 KARTU STATISTIK KPI -->
        @include('pages.kemitraan.master_partials.header_banner')

        <!-- FILTER BAR & PENCARIAN -->
        @include('pages.kemitraan.master_partials.filter_bar')

        <!-- TABEL DATA MASTER ASET KEMITRAAN -->
        @include('pages.kemitraan.master_partials.table_kemitraan')

        <!-- MODAL DETAIL & STATUS KONSESI -->
        @include('pages.kemitraan.master_partials.modal_detail_kemitraan')

        <!-- MODAL KONFIRMASI HAPUS / BATALKAN -->
        @include('pages.kemitraan.master_partials.modal_confirm')
    </div>
</x-layout>
