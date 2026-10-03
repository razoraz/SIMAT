@php
    $isEdit = isset($astap) && $astap;
    $pageTitle = $isEdit ? 'Ubah Data Aset Hibah' : 'Pencatatan Aset Hibah';
    $pageBreadcrumb = $isEdit ? 'Master Aset / Kelola Hibah Aset / Ubah Aset Hibah' : 'Master Utama / Data ASTAP / Tambah Hibah';
@endphp

<x-layout :title="($isEdit ? 'Form Ubah Aset Hibah' : 'Form Input Aset Hibah') . ' - SIMAT-RK'">
    @section('page-title', $pageTitle)
    @section('breadcrumb', $pageBreadcrumb)

    <script>
        window.editAstapData = @json($astap ?? null);
    </script>

    <!-- 1. Script Logika Form (Alpine.js & State Management) -->
    @include('pages.hibah.form_partials.scripts')

    <div x-data="formHibah()" x-cloak class="space-y-6">

        <!-- 2. Top Navigation Bar & Stepper Tabs Indicator -->
        @include('pages.hibah.form_partials.stepper_header')

        <!-- 3. Main Form Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-xl">
            
            <!-- LANGKAH 1: BAST & Pemberi Hibah -->
            @include('pages.hibah.form_partials.step1_bast_pemberi')

            <!-- LANGKAH 2: Rekening & Klasifikasi Kode Barang 108 -->
            @include('pages.hibah.form_partials.step2_rekening_kode108')

            <!-- LANGKAH 3: Rincian Aset KIB & Penempatan Ruangan -->
            @include('pages.hibah.form_partials.step3_rincian_aset')

            <!-- Bottom Navigation Between Steps -->
            @include('pages.hibah.form_partials.stepper_navigation')

        </div>

        <!-- Global Toast Notification -->
        @include('pages.hibah.form_partials.dialogs_and_toast')

    </div>
</x-layout>
