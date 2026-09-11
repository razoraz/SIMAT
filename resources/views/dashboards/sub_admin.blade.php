<x-layout title="Dashboard Sub Admin - SIMAT-RK">
    @section('page-title', 'Dashboard Sub Admin')
    @section('breadcrumb', 'Beranda / Sub Admin')

    @php
        $unitNama = $unit->nama ?? 'Unit Ruangan';
        $unitKode = $unit->kode_unit ?? ('UNIT-' . str_pad($unit->id ?? 1, 3, '0', STR_PAD_LEFT));
        $unitTipe = $unit->tipe ?? 'Unit Pelayanan Medis / Operasional';
        $unitKepala = $unit->kepala ?? Auth::user()->name;
        $unitNip = $unit->nip ?? Auth::user()->nip ?? '-';
        $unitTotalAset = $totalAsetCount ?? 0;
        $unitTotalNilai = $totalNilaiFormatted ?? 'Rp 0';
    @endphp

    <div x-data="{
        // Daftar Aset Ruangan yang Perlu Perhatian / Pemeliharaan dari Database Backend
        attentionAssets: {{ Js::from($attentionAssets ?? []) }},
    }" x-cloak>
        @include('dashboards.sub_admin_partials.header_unit')
        @include('dashboards.sub_admin_partials.metric_cards')
        @include('dashboards.sub_admin_partials.charts_grid')

        <!-- 4. MODUL UTAMA: KATALOG & LEMBAR KIR + ASET PERLU PERHATIAN -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- KOLOM KIRI (2/3): KATALOG DATA ASTAP & LEMBAR KIR RUANGAN -->
            <div class="lg:col-span-2 space-y-6">
                @include('dashboards.sub_admin_partials.katalog_card')
                @include('dashboards.sub_admin_partials.kir_card')
            </div>

            <!-- KOLOM KANAN (1/3): CARD ASET PERLU PERHATIAN -->
            @include('dashboards.sub_admin_partials.attention_card')
        </div>
    </div>

    @include('dashboards.sub_admin_partials.scripts')
</x-layout>
