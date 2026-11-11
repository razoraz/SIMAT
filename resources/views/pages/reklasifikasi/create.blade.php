<x-layout title="Catat Reklasifikasi Aset Baru - SIMAT-RK">
    @section('page-title', 'Catat Reklasifikasi Aset Baru (RSDK)')
    @section('breadcrumb', 'Master Aset / Reklasifikasi Aset / Catat Baru')

    <script>
        window.isReklasCreatePage = true;
        window.dbMitraKemitraans = @json($dbMitraKemitraans ?? []);
        window.targetAstapPreselected = @json($targetAstap ?? null);
        window.kandidatAstaps = @json($kandidatAstaps ?? []);
        window.templateRows = @json($templateRows ?? []);
        window.dbMasterJenisAstap108 = @json(!empty($dbMaster108) ? $dbMaster108 : \App\Models\JenisAstap::getNested108());
    </script>

    @include('pages.reklasifikasi.partials.scripts')

    <div x-data="masterReklasifikasi()" x-cloak class="space-y-6 pb-20 max-w-7xl mx-auto">
        <!-- HEADER & BREADCRUMB NAVIGATION -->
        @include('pages.reklasifikasi.create_partials.header')

        <!-- FORM CARD UTAMA (4 TAHAP TERSTRUKTUR & LAPANG) -->
        <form @submit.prevent="submitFormTambah()" class="space-y-6">
            <!-- 1. IDENTITAS BARANG MILIK DAERAH (ASTAP) -->
            @include('pages.reklasifikasi.create_partials.section_aset')

            <!-- 2. JENIS & SIFAT REKLASIFIKASI PMDN 108 -->
            @include('pages.reklasifikasi.create_partials.section_jenis')

            <!-- 3. PARAMETER REKLASIFIKASI & DATA BARU -->
            @include('pages.reklasifikasi.create_partials.section_parameter')

            <!-- 4. LIVE PREVIEW & AKSI PENERBITAN -->
            @include('pages.reklasifikasi.create_partials.section_preview_action')
        </form>
    </div>
</x-layout>

