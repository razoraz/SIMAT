@php
    $isSubAdmin = (Auth::user()->role ?? '') === 'sub_admin';
    $pageTitle = $isSubAdmin ? 'Pengajuan Baru' : 'Distribusi ASTAP';
    $breadcrumbTitle = $isSubAdmin ? 'Master Utama / Pengajuan Baru' : 'Master Utama / Distribusi ASTAP';
@endphp

<x-layout :title="$pageTitle . ' - SIMAT-RK'">
    @section('page-title', $pageTitle)
    @section('breadcrumb', $breadcrumbTitle)

    @include('pages.distribusi_partials.scripts')

    <div x-data="distribusiCatalog()" x-cloak>
        <!-- KONTEN UTAMA KATALOG DISTRIBUSI (DISEMBUNYIKAN SAAT DICETAK) -->
        <div class="no-print space-y-6">
            @include('pages.distribusi_partials.header_kpi')
            @include('pages.distribusi_partials.filter_bar')
            @include('pages.distribusi_partials.table_distribusi')
        </div>

        <!-- MODALS & POPUPS -->
        @include('pages.distribusi_partials.modal_detail')
        @include('pages.distribusi_partials.modal_confirm')
        @include('pages.distribusi_partials.toast_notification')
    </div>
</x-layout>
