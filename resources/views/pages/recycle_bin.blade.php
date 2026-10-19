<x-layout title="Pusat Pemulihan Data - SIMAT-RK">
    @section('page-title', 'Pusat Pemulihan Data')
    @section('breadcrumb', 'Audit & Pemulihan / Pusat Data Terhapus')

    {{-- 1. ALPINE.JS APP LOGIC & AJAX/RESTORE HANDLERS (Di-include sebelum x-data agar recycleBinApp selalu siap saat Alpine init) --}}
    @include('pages.recycle_bin_partials.scripts')

    <div x-data="recycleBinApp()" x-cloak class="space-y-6">

        {{-- 2. HEADER & EXECUTIVE STATS RIBBON, MODULE TABS, SEARCH & TIME FILTER --}}
        @include('pages.recycle_bin_partials.header_nav')

        {{-- 3. TAB CONTENT: BELANJA MODAL (AKUN 5.2 KASDA / BLUD APBD & REGISTER NIBAR) --}}
        @include('pages.recycle_bin_partials.tab_belanja_modal')

        {{-- 4. TAB CONTENT: MUTASI ASET (INTERNAL & EKSTERNAL) --}}
        @include('pages.recycle_bin_partials.tab_mutasi')

        {{-- 5. TAB CONTENT: MASTER ASTAP (PAKET PENGADAAN & REGISTER NIBAR) --}}
        @include('pages.recycle_bin_partials.tab_astap')

        {{-- 6. TAB CONTENT: DISTRIBUSI ASET --}}
        @include('pages.recycle_bin_partials.tab_distribusi')

        {{-- 7. TAB CONTENT: UNIT & PAVILIUN --}}
        @include('pages.recycle_bin_partials.tab_unit')

        {{-- 8. TAB CONTENT: HIBAH ASET --}}
        @include('pages.recycle_bin_partials.tab_hibah')

        {{-- 9. TAB CONTENT: KEMITRAAN ASET (AKUN 1.5.2 / KSO) --}}
        @include('pages.recycle_bin_partials.tab_kemitraan')

        {{-- 10. TAB CONTENT: BELANJA BARANG (AKUN 5.1.02 / PERBEKALAN RUANGAN) --}}
        @include('pages.recycle_bin_partials.tab_belanja_barang')

        {{-- 11. TAB CONTENT: REKLASIFIKASI ASET --}}
        @include('pages.recycle_bin_partials.tab_reklas')

        {{-- 12. TAB CONTENT: AKUN PENGGUNA (USERS) --}}
        @include('pages.recycle_bin_partials.tab_users')

        {{-- 13. DYNAMIC DETAIL MODAL PREVIEW --}}
        @include('pages.recycle_bin_partials.modal_detail')

        {{-- 14. FLOATING ACTION DOCK (SELEKSI MASSAL) --}}
        @include('pages.recycle_bin_partials.floating_dock')

    </div>
</x-layout>
