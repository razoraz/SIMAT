<x-layout title="Form Input Aset Kemitraan Pihak Ketiga (KSO) - SIMAT-RK">
    @section('page-title', 'Pencatatan Aset Kemitraan (KSO)')
    @section('breadcrumb', 'Master Utama / Data ASTAP / Tambah Kemitraan Pihak Ketiga')

    <!-- 1. Script Logika Form (Alpine.js & State Management) -->
    @include('pages.form_kemitraan_partials.scripts')

    <div x-data="formKemitraan()" x-cloak class="space-y-6">

        <!-- 2. Top Header Banner & Stepper Tabs Indicator -->
        @include('pages.form_kemitraan_partials.stepper_header')

        <!-- 3. Main Form Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
            
            <!-- LANGKAH 1: Dokumen PKS & Rekanan Mitra Pihak Ketiga -->
            @include('pages.form_kemitraan_partials.step1_pks_mitra')

            <!-- LANGKAH 2: Klasifikasi Kode Barang 108 (Akun 1.5.2) & Nilai Taksiran -->
            @include('pages.form_kemitraan_partials.step2_klasifikasi_108')

            <!-- LANGKAH 3: Rincian Spesifikasi Teknis & Lokasi Penempatan Ruangan RSUD -->
            @include('pages.form_kemitraan_partials.step3_rincian_aset')

            <!-- Stepper Bottom Navigation -->
            @include('pages.form_kemitraan_partials.stepper_navigation')

        </div>

        <!-- 4. Global Floating Toast Notification -->
        @include('pages.form_kemitraan_partials.dialogs_and_toast')

    </div>
</x-layout>
