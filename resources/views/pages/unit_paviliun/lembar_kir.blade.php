<x-layout title="Lembar Kartu Inventaris Ruangan (KIR) - SIMAT-RK">
    @section('page-title', 'Lembar KIR Ruangan')
    @section('breadcrumb', 'Master Utama / Lembar KIR Ruangan')

    @php
        $unitNama = $currentUnit->nama ?? 'Unit Ruangan';
        $unitKode = $currentUnit->kode_unit ?? ('UNIT-' . str_pad($currentUnit->id ?? 1, 3, '0', STR_PAD_LEFT));
        $unitTipe = $currentUnit->tipe ?? 'Unit Pelayanan / Instalasi RSUD';
        $unitKepala = $currentUnit->kepala ?? Auth::user()->name;
        $unitNip = $currentUnit->nip ?? Auth::user()->nip ?? '-';
        $isSubAdmin = (Auth::user()->role ?? '') === 'sub_admin';
    @endphp

    @include('pages.unit_paviliun.kir_partials.scripts')

    <div x-data="kirRuanganData()" x-cloak class="space-y-6">
        @include('pages.unit_paviliun.kir_partials.toast_notification')
        @include('pages.unit_paviliun.kir_partials.header_kpi')
        @include('pages.unit_paviliun.kir_partials.table_assets')

        {{-- Modals & Dialogs --}}
        @include('pages.unit_paviliun.kir_partials.modal_edit_kondisi')
        @include('pages.unit_paviliun.kir_partials.modal_print_kir')
        @include('pages.unit_paviliun.kir_partials.modal_detail')
    </div>

    @include('pages.unit_paviliun.kir_partials.print_styles')
</x-layout>
