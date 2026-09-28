<x-layout title="Pusat Pemulihan Data - SIMAT-RK">
    @section('page-title', 'Pusat Pemulihan Data')
    @section('breadcrumb', 'Audit & Pemulihan / Pusat Data Terhapus')

    <div x-data="recycleBinApp()" x-cloak class="space-y-6">

        {{-- 1. HEADER & EXECUTIVE STATS RIBBON, MODULE TABS, SEARCH & TIME FILTER --}}
        @include('pages.recycle_bin_partials.header_nav')

        {{-- 2. TAB CONTENT: MUTASI ASET (INTERNAL & EKSTERNAL) --}}
        @include('pages.recycle_bin_partials.tab_mutasi')

        {{-- 3. TAB CONTENT: MASTER ASTAP (PAKET PENGADAAN & REGISTER NIBAR) --}}
        @include('pages.recycle_bin_partials.tab_astap')

        {{-- 4. TAB CONTENT: DISTRIBUSI ASET --}}
        @include('pages.recycle_bin_partials.tab_distribusi')

        {{-- 5. TAB CONTENT: UNIT & PAVILIUN --}}
        @include('pages.recycle_bin_partials.tab_unit')

        {{-- 6. TAB CONTENT: HIBAH ASET --}}
        @include('pages.recycle_bin_partials.tab_hibah')

        {{-- 7. TAB CONTENT: KEMITRAAN ASET (AKUN 1.5.2 / KSO) --}}
        @include('pages.recycle_bin_partials.tab_kemitraan')

        {{-- 8. TAB CONTENT: BELANJA BARANG (AKUN 5.1.02 / PERBEKALAN RUANGAN) --}}
        @include('pages.recycle_bin_partials.tab_belanja_barang')

        {{-- 9. TAB CONTENT: AKUN PENGGUNA (USERS) --}}
        @include('pages.recycle_bin_partials.tab_users')

        {{-- 10. DYNAMIC DETAIL MODAL PREVIEW --}}
        @include('pages.recycle_bin_partials.modal_detail')

        {{-- 11. FLOATING ACTION DOCK (SELEKSI MASSAL) --}}
        @include('pages.recycle_bin_partials.floating_dock')

    </div>

    {{-- 12. ALPINE.JS APP LOGIC & AJAX/RESTORE HANDLERS --}}
    @include('pages.recycle_bin_partials.scripts')
</x-layout>
