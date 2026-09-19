<x-layout title="Data ASTAP - SIMAT-RK" :fullWidth="true">
    @section('page-title', 'Data ASTAP')
    @section('breadcrumb', 'Master Utama / Data ASTAP')

    <!-- 1. Script Ekspor Multi-Sheet Excel & Logika Alpine.js (astapCatalog) -->
    @include('pages.data_astap_partials.scripts')

    <div x-data="astapCatalog()" x-cloak class="space-y-6">

        <!-- 2. Header Banner & Statistik Ringkas -->
        @include('pages.data_astap_partials.header_banner')

        <!-- 3. Filter Bar (KIB, Tahun, Triwulan, Search & Tombol Ekspor) -->
        @include('pages.data_astap_partials.filter_bar')

        <!-- 4. Tabel Katalog Data ASTAP Utama -->
        @include('pages.data_astap_partials.table')

        <!-- 5. Modal Detail Lengkap ASTAP & Rincian Register NIBAR -->
        @include('pages.data_astap_partials.modal_detail')

        <!-- 6. Modal Pratinjau & Download Label QR Code -->
        @include('pages.data_astap_partials.modal_qr')

        <!-- 7. Modal Konfirmasi Tindakan Global -->
        @include('pages.data_astap_partials.modal_confirm')

        <!-- 8. Modal Pilih Tahun & Triwulan untuk Ekspor Excel -->
        @include('pages.data_astap_partials.modal_export_excel')

        <!-- 9. Modal Dialog: Rapikan & Urutkan Ulang NIBAR -->
        @include('pages.data_astap_partials.modal_resequence_nibar')

        <!-- 10. Modal Dialog: Reklasifikasi Aset Tetap (RSDK) -->
        @include('pages.data_astap_partials.modal_reklas')

        <!-- 11. Global Floating Toast Notification Popup -->
        @include('pages.data_astap_partials.toast')

    </div>
</x-layout>
