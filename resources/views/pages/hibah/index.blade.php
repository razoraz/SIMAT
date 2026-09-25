<x-layout title="Kelola Data Hibah Aset - SIMAT-RK">
    @section('page-title', 'Kelola Data Hibah Aset (Masuk & Pengurangan)')
    @section('breadcrumb', 'Master Aset / Kelola Hibah Aset')

    @include('pages.hibah.master_partials.scripts')

    <div x-data="masterHibah()" x-cloak class="space-y-6 pb-16">
        <!-- HEADER & 4 KARTU STATISTIK KPI -->
        @include('pages.hibah.master_partials.header_banner')

        <!-- FILTER TAHUN, TRIWULAN, TABS & PENCARIAN -->
        @include('pages.hibah.master_partials.filter_bar')

        <!-- TABEL TRANSAKSI HIBAH (MASUK & KELUAR) -->
        @include('pages.hibah.master_partials.table_hibah')

        <!-- MODAL HIBAH KELUAR (PENGURANGAN) -->
        @include('pages.hibah.master_partials.modal_hibah_keluar')

        <!-- MODAL DETAIL TRANSAKSI HIBAH -->
        @include('pages.hibah.master_partials.modal_detail_hibah')

        <!-- MODAL EKSPOR EXCEL MULTI-SHEET SIPENERBANG -->
        @include('pages.hibah.master_partials.modal_export_excel')
    </div>
</x-layout>
