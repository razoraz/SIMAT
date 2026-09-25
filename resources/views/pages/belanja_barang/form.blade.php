<x-layout title="Form Input Belanja Barang (Akun 5.1.02) - SIMAT-RK">
    @section('page-title', 'Pencatatan Belanja Barang')
    @section('breadcrumb', 'Master Aset / Kelola Belanja Barang / Tambah Belanja Barang')

    <!-- 1. Script Logika Form (Alpine.js & State Management) -->
    @include('pages.belanja_barang.form_partials.scripts')

    <div x-data="formBelanjaBarang()" x-cloak class="max-w-5xl mx-auto space-y-6 py-2">

        <!-- 2. Header Banner & Stepper Tabs Navigation -->
        @include('pages.belanja_barang.form_partials.stepper_header')

        <!-- 3. Main Form Container -->
        <form @submit.prevent="submitForm" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">

            <!-- LANGKAH 1: Dokumen Pembelian Faktur/Nota & Toko Rekanan (Smart Combobox) -->
            @include('pages.belanja_barang.form_partials.step1_faktur_toko')

            <!-- LANGKAH 2: Klasifikasi Kode Barang 108 Permendagri & Volume -->
            @include('pages.belanja_barang.form_partials.step2_klasifikasi_108')

            <!-- LANGKAH 3: Spesifikasi Teknis KIB & Ruangan Penempatan KIR -->
            @include('pages.belanja_barang.form_partials.step3_spesifikasi_ruangan')

            <!-- Stepper Bottom Navigation (Sebelumnya / Lanjutkan / Simpan) -->
            @include('pages.belanja_barang.form_partials.stepper_navigation')

        </form>

    </div>
</x-layout>
