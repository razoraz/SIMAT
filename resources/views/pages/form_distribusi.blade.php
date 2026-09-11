@php
    $isSubAdmin = (Auth::user()->role ?? '') === 'sub_admin';
    $pageTitle = request()->routeIs('distribusi.edit') 
        ? 'Ubah Distribusi ASTAP' 
        : ($isSubAdmin ? 'Input Pengajuan Baru' : 'Input Distribusi Baru');
    $breadcrumbTitle = request()->routeIs('distribusi.edit') 
        ? 'Master Utama / Distribusi ASTAP / Ubah' 
        : ($isSubAdmin ? 'Master Utama / Pengajuan Baru / Input Baru' : 'Master Utama / Distribusi ASTAP / Input Baru');
@endphp

<x-layout :title="$pageTitle . ' - SIMAT-RK'">
    @section('page-title', $pageTitle)
    @section('breadcrumb', $breadcrumbTitle)

    @include('pages.form_distribusi_partials.scripts')

    <div x-data="formDistribusiApp()" x-cloak class="space-y-6">
        @include('pages.form_distribusi_partials.header_card')

        <!-- Form Card Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl space-y-6">
            @include('pages.form_distribusi_partials.rejected_banner')
            @include('pages.form_distribusi_partials.section_info_transaksi')
            @include('pages.form_distribusi_partials.section_items_distribusi')
            @include('pages.form_distribusi_partials.action_buttons')
        </div>

        @include('pages.form_distribusi_partials.modal_confirm')
        @include('pages.form_distribusi_partials.toast_notification')
    </div>
</x-layout>
