<x-layout title="Pusat Pemulihan Data - SIMAT-RK">
    @section('page-title', 'Pusat Pemulihan Data')
    @section('breadcrumb', 'Audit & Pemulihan / Pusat Data Terhapus')

    {{-- 1. ALPINE.JS APP LOGIC & AJAX/RESTORE HANDLERS (Di-include sebelum x-data agar recycleBinApp selalu siap saat Alpine init) --}}
    @include('pages.recycle_bin_partials.scripts')

    <div x-data="recycleBinApp()" x-cloak class="space-y-6">
        <style>
            /* Responsive Panel, Sub-Tab & Sticky Action Column untuk Pusat Data Terhapus */
            .rb-card-panel {
                padding: 1rem;
            }
            @media (min-width: 640px) {
                .rb-card-panel {
                    padding: 1.25rem;
                }
            }
            @media (min-width: 1024px) {
                .rb-card-panel {
                    padding: 1.5rem;
                }
            }

            @media (max-width: 1023px) {
                .swipe-hint-rb {
                    display: flex;
                }
                .rb-sticky-aksi {
                    position: static !important;
                    right: auto !important;
                    z-index: auto !important;
                    box-shadow: none !important;
                }
                td.rb-sticky-aksi,
                th.rb-sticky-aksi {
                    background-color: transparent !important;
                }
                .rb-subbar {
                    display: flex !important;
                    flex-direction: column !important;
                    align-items: center !important;
                    text-align: center !important;
                }
                .rb-subbar-pills {
                    display: flex !important;
                    flex-wrap: wrap !important;
                    justify-content: center !important;
                    width: 100% !important;
                    max-width: 100% !important;
                    gap: 0.375rem !important;
                    overflow-x: visible !important;
                }
                .rb-subbar-pills > button {
                    max-width: 100% !important;
                    white-space: normal !important;
                    text-align: center !important;
                    justify-content: center !important;
                }
                .rb-subbar-info {
                    justify-content: center !important;
                    text-align: center !important;
                    width: 100% !important;
                    padding-left: 0 !important;
                }
            }

            @media (min-width: 1024px) {
                .swipe-hint-rb {
                    display: none !important;
                }
                th.rb-sticky-aksi {
                    position: sticky !important;
                    right: 0 !important;
                    z-index: 10 !important;
                    background-color: #020617 !important;
                    min-width: 220px !important;
                    width: 220px !important;
                }
                td.rb-sticky-aksi {
                    position: sticky !important;
                    right: 0 !important;
                    z-index: 2 !important;
                    background-color: #0f172a !important;
                    box-shadow: -6px 0 12px rgba(0, 0, 0, 0.6) !important;
                    min-width: 220px !important;
                    width: 220px !important;
                }
                tr:hover td.rb-sticky-aksi {
                    background-color: #1e293b !important;
                }
                .rb-subbar {
                    display: flex !important;
                    flex-direction: row !important;
                    align-items: center !important;
                    justify-content: space-between !important;
                    text-align: left !important;
                }
                .rb-subbar-pills {
                    justify-content: flex-start !important;
                    width: auto !important;
                }
                .rb-subbar-info {
                    justify-content: flex-end !important;
                    text-align: right !important;
                    width: auto !important;
                }
            }
        </style>

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
