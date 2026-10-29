<x-layout title="Kelola Data Hibah Aset - SIMAT-RK">
    @section('page-title', 'Kelola Data Hibah Aset (Masuk & Pengurangan)')
    @section('breadcrumb', 'Master Aset / Kelola Hibah Aset')

    <script src="{{ asset('js/qrcode.min.js') }}"></script>

    @include('pages.hibah.master_partials.scripts_export')
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

        <!-- MODAL PRATINJAU QR CODE, UBAH KONDISI & RIWAYAT MUTASI -->
        @include('pages.hibah.master_partials.modal_qr_hibah')

        <!-- MODAL REKLASIFIKASI ASET TETAP (RSDK) - SAMA PERSIS SEPERTI DI DATA ASTAP & KEMITRAAN -->
        @include('pages.astap.index_partials.modal_reklas')

        <!-- MODAL EKSPOR EXCEL MULTI-SHEET SIPENERBANG -->
        @include('pages.hibah.master_partials.modal_export_excel')

        <!-- MODAL CETAK RESMI BAST HIBAH ASET -->
        @include('pages.hibah.master_partials.modal_print_bast')

        <!-- MODAL KONFIRMASI BESPOKE (Z-INDEX TINGGI z-[60]) -->
        @include('pages.hibah.master_partials.modal_confirm_hibah')

        <!-- FLOATING TOAST NOTIFICATION -->
        @include('pages.hibah.master_partials.toast')
    </div>
</x-layout>
