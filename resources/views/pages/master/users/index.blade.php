<x-layout title="Manajemen Pengguna - SIMAT-RK">
    @section('page-title', 'Manajemen Pengguna')
    @section('breadcrumb', 'Master Data System / Manajemen Pengguna')

    @include('pages.master.users.partials.scripts')

    <div x-data="userManager()" x-cloak class="space-y-6">
        @include('pages.master.users.partials.header_kpi')
        @include('pages.master.users.partials.filter_bar')
        @include('pages.master.users.partials.table_users')

        {{-- Modals & Popups --}}
        @include('pages.master.users.partials.modal_detail')
        @include('pages.master.users.partials.modal_create')
        @include('pages.master.users.partials.modal_edit')
        @include('pages.master.users.partials.modal_confirm')
        @include('pages.master.users.partials.toast_notification')
    </div>
</x-layout>
