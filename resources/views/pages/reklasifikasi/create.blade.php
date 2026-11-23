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
        window.skpdDirectory = @json($dbSkpdDirectory ?? []);
        window.skpdList = @json($dbSkpdAsals ?? []);
        window.distinctPejabats = @json($dbPejabats ?? []);
    </script>

    @include('pages.reklasifikasi.partials.scripts')

    <div x-data="masterReklasifikasi()" x-cloak class="space-y-6 pb-20 max-w-7xl mx-auto">
        <!-- HEADER & BREADCRUMB NAVIGATION WITH STEPPER -->
        @include('pages.reklasifikasi.create_partials.header')

        <!-- FORM CARD UTAMA (STEPPER ALUR REKLASIFIKASI) -->
        <form @submit.prevent="submitFormTambah()" class="space-y-6">
            <!-- LANGKAH 1: FORMULIR INPUT & PARAMETER REKLASIFIKASI -->
            <div x-show="currentStep === 1"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">
                <!-- 1. IDENTITAS BARANG MILIK DAERAH (ASTAP) -->
                @include('pages.reklasifikasi.create_partials.section_aset')

                <!-- 2. JENIS & SIFAT REKLASIFIKASI PMDN 108 -->
                @include('pages.reklasifikasi.create_partials.section_jenis')

                <!-- 3. PARAMETER REKLASIFIKASI & DATA BARU -->
                @include('pages.reklasifikasi.create_partials.section_parameter')

                <!-- BILAH AKSI NAVIGASI LANGKAH 1 -->
                @include('pages.reklasifikasi.create_partials.section_preview_action')
            </div>

            <!-- LANGKAH 2: LEMBAR VERIFIKASI & KONFIRMASI REKLASIFIKASI (HALAMAN BERIKUTNYA) -->
            <div x-show="currentStep === 2"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">
                @include('pages.reklasifikasi.create_partials.step2_verifikasi')
            </div>
        </form>

        <!-- MODAL KAMUS PANDUAN KIB & PMDN 108 -->
        @include('pages.reklasifikasi.partials.modal_panduan')
    </div>
</x-layout>

