<x-layout title="Mutasi Eksternal (Antar-OPD) - SIMAT-RK" :fullWidth="true">
    @section('page-title', 'Mutasi Eksternal (Antar-OPD)')
    @section('breadcrumb', 'Master Aset / Mutasi Eksternal')

    @include('pages.mutasi_eksternal.index_partials.scripts')

    <div x-data="mutasiEksternalCatalog()" x-cloak>
        <!-- KONTEN UTAMA KATALOG MUTASI EKSTERNAL -->
        <div class="no-print space-y-6">
            @include('pages.mutasi_eksternal.index_partials.header_kpi')
            @include('pages.mutasi_eksternal.index_partials.filter_bar')
            @include('pages.mutasi_eksternal.index_partials.table_mutasi')
        </div>

        <!-- MODAL DETAIL BAST ANTAR-OPD -->
        @include('pages.mutasi_eksternal.index_partials.modal_detail')

        <!-- MODAL CETAK BAST PELIMPAHAN BMD (INTERAKTIF & LIVE EDIT) -->
        @include('pages.mutasi_eksternal.index_partials.modal_print_bast')

        <!-- MODAL KONFIRMASI HAPUS (SOFT-DELETE) -->
        @include('pages.mutasi_eksternal.index_partials.modal_delete')

        <!-- MODAL KONFIRMASI TINDAKAN (CUSTOM BESPOKE MODAL) -->
        @include('pages.mutasi_eksternal.index_partials.modal_confirm')

        <!-- MODAL AKSI REGISTER: UBAH KONDISI & RIWAYAT MUTASI -->
        @include('pages.mutasi_eksternal.index_partials.modal_register_actions')
    </div>

    <!-- Print Media Query Styling untuk Cetak BAST Kertas Putih Sempurna -->
    <style>
    @media print {
        body {
            background: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        .no-print, aside, header, nav, footer {
            display: none !important;
        }
        #print-area-bast-eksternal {
            display: block !important;
            width: 100% !important;
            padding: 0 !important;
            margin: 0 !important;
            box-shadow: none !important;
            border: none !important;
        }
    }
    </style>
</x-layout>
