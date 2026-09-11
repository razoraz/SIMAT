<x-layout title="Unit & Paviliun - SIMAT-RK">
    @section('page-title', 'Unit & Paviliun')
    @section('breadcrumb', 'Master Utama / Unit & Paviliun')

    @include('pages.unit_paviliun_partials.scripts')

    <div x-data="unitPaviliunCatalog()" x-cloak class="space-y-6">
        @include('pages.unit_paviliun_partials.header_kpi')
        @include('pages.unit_paviliun_partials.filter_bar')
        @include('pages.unit_paviliun_partials.content_views')

        {{-- Modals & Dialogs --}}
        @include('pages.unit_paviliun_partials.modal_detail')
        @include('pages.unit_paviliun_partials.modal_kir')
        @include('pages.unit_paviliun_partials.modal_add_unit')
        @include('pages.unit_paviliun_partials.modal_edit_unit')
        @include('pages.unit_paviliun_partials.modal_confirm')
        @include('pages.unit_paviliun_partials.toast_notification')
    </div>
</x-layout>
