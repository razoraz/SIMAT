<x-layout :title="request()->routeIs('astap.edit') ? 'Ubah Data ASTAP - SIMAT-RK' : 'Tambah Data ASTAP Baru - SIMAT-RK'">
    @section('page-title', request()->routeIs('astap.edit') ? 'Ubah Data ASTAP' : 'Tambah Data ASTAP Baru')
    @section('breadcrumb', request()->routeIs('astap.edit') ? 'Master Utama / Data ASTAP / Ubah Data' : 'Master Utama / Data ASTAP / Tambah Baru')

    <!-- 1. Script Logika Form (Alpine.js & State Management) -->
    @include('pages.form_astap_partials.scripts')

    <div x-data="astapForm()" x-cloak class="space-y-6">

        <!-- 2. Top Navigation Bar & Stepper Tabs Indicator -->
        @include('pages.form_astap_partials.stepper_header')

        <!-- 3. Main Form Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
            
            <!-- LANGKAH 1: Filtering Bertingkat SIPD -->
            @include('pages.form_astap_partials.step1_pengadaan_sipd')

            <!-- LANGKAH 2: Rekening Belanja SIPD & Jenis ASTAP Kode 108 -->
            @include('pages.form_astap_partials.step2_rekening_kode108')

            <!-- LANGKAH 3: Rincian Belanja Modal (Tanah/Mesin/Gedung/Jaringan/Lain) -->
            @include('pages.form_astap_partials.step3_rincian_aset')

            <!-- LANGKAH 4: Pihak Rekanan Penyedia & PPK -->
            @include('pages.form_astap_partials.step4_penyedia_ppk')

            <!-- Bottom Navigation Between Steps -->
            @include('pages.form_astap_partials.stepper_navigation')

        </div>

        <!-- Global Confirmation Modal Dialog & Floating Toast -->
        @include('pages.form_astap_partials.dialogs_and_toast')

    </div>
</x-layout>
