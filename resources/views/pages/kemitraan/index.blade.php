<x-layout title="Kelola Data Aset Kemitraan (Akun 1.5.2) - SIMAT-RK">
    @section('page-title', 'Kelola Data Aset Kemitraan Pihak Ketiga (Sewa, KSP, BGS/BSG, KSPI)')
    @section('breadcrumb', 'Master Aset / Kelola Kemitraan Aset')

    <script>
        window.dbMitraKemitraans = @json($dbMitraKemitraans ?? []);
    </script>

    @include('pages.kemitraan.master_partials.scripts_export')
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

        <!-- MODAL PRATINJAU & DOWNLOAD QR CODE (TERMASUK UBAH KONDISI & RIWAYAT) -->
        @include('pages.kemitraan.master_partials.modal_qr_kemitraan')

        <!-- MODAL KONFIRMASI HAPUS / BATALKAN & DIALOG GLOBAL -->
        @include('pages.kemitraan.master_partials.modal_confirm')

        <!-- MODAL REKLASIFIKASI ASET TETAP (RSDK) — SAMA SEPERTI DI DATA ASTAP -->
        @include('pages.astap.index_partials.modal_reklas')

        <!-- MODAL PILIH TAHUN, TRIWULAN & SKEMA EKSPOR EXCEL KEMITRAAN -->
        @include('pages.kemitraan.master_partials.modal_export_excel')

        <!-- FLOATING TOAST NOTIFICATION -->
        @include('pages.kemitraan.master_partials.toast')
    </div>
</x-layout>
