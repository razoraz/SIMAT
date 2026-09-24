<x-layout title="Reklasifikasi Aset Tetap - SIMAT-RK">
    @section('page-title', 'Reklasifikasi Aset Tetap (RSDK)')
    @section('breadcrumb', 'Master Aset / Reklasifikasi Aset')

    @include('pages.master_reklasifikasi_partials.scripts')

    <div x-data="masterReklasifikasi()" x-cloak class="space-y-6 pb-16">
        <!-- HEADER & 4 KARTU STATISTIK RINGKAS -->
        @include('pages.master_reklasifikasi_partials.header_banner')

        <!-- FILTER TAHUN, TRIWULAN, SWITCH TAB & AKSI -->
        @include('pages.master_reklasifikasi_partials.filter_bar')

        <!-- TAB 1: MATRIKS NERACA REKLASIFIKASI 5 KOLOM (PMDN 108) -->
        <div x-show="activeTab === 'matriks'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @include('pages.master_reklasifikasi_partials.table_matriks')
        </div>

        <!-- TAB 2: LOG TRANSAKSI REKLASIFIKASI (AUDIT TRAIL) -->
        <div x-show="activeTab === 'log'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            @include('pages.master_reklasifikasi_partials.table_log')
        </div>

        <!-- MODAL TAMBAH REKLASIFIKASI DARI MASTER -->
        @include('pages.master_reklasifikasi_partials.modal_tambah')
        @include('pages.master_reklasifikasi_partials.modal_detail')
        @include('pages.master_reklasifikasi_partials.modal_confirm')
        @include('pages.master_reklasifikasi_partials.modal_panduan')
    </div>
</x-layout>
