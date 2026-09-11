<x-layout title="Manajemen Pengguna - SIMAT-RK">
    @section('page-title', 'Manajemen Pengguna')
    @section('breadcrumb', 'Master Data System / Manajemen Pengguna')

    @include('pages.master_users_partials.scripts')

    <div x-data="userManager()" x-cloak class="space-y-6">
        @include('pages.master_users_partials.header_kpi')
        @include('pages.master_users_partials.filter_bar')
        @include('pages.master_users_partials.table_users')

        {{-- Modals & Popups --}}
        @include('pages.master_users_partials.modal_detail')
        @include('pages.master_users_partials.modal_create')
        @include('pages.master_users_partials.modal_edit')
        @include('pages.master_users_partials.modal_confirm')
        @include('pages.master_users_partials.toast_notification')
    </div>
</x-layout>
