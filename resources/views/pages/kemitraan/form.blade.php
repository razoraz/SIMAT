@php
    $astapModel = $astap ?? null;
    $isEdit = !empty($astapModel);
    $isFromAstap = request('from') === 'astap' || request()->query('from') === 'astap';

    // Status konsesi dari tabel astap_kemitraans
    $statusKonsesi = $astapModel?->kemitraan?->status_konsesi;
    if (!$statusKonsesi && $isEdit) {
        $kCheck = \App\Models\AstapKemitraan::where(function ($q) use ($astapModel) {
            $q->where('astap_id', $astapModel->id)->orWhere('objek_astap_id', $astapModel->id);
        })->where('is_deleted', 0)->first();
        $statusKonsesi = $kCheck?->status_konsesi;
    }

    // Reklasifikasi terakhir: apakah reklas keluar dari Kemitraan ke Milik RSUD?
    $latestReklas = ($isEdit && $astapModel->reklas) ? $astapModel->reklas->sortByDesc('id')->first() : null;
    $isReklasKeluar = ($latestReklas && $latestReklas->asal_kib === 'KEMITRAAN' && $latestReklas->tujuan_kib !== 'KEMITRAAN');

    // Suatu aset HANYA dianggap "Milik RSUD (Eks-Kemitraan)" jika konsesinya telah selesai / direklasifikasi kembali ke RSUD
    $isMilikRsud = $isEdit && (
        in_array($statusKonsesi, ['Selesai / Reklasifikasi', 'Selesai'], true)
        || $isReklasKeluar
    );

    $backUrl = ($isFromAstap || $isMilikRsud) ? route('astap.index') : route('master.kemitraan');
    $pageTitle = $isMilikRsud 
        ? 'Ubah Data Aset Milik RSUD (Eks-Kemitraan)' 
        : ($isEdit ? 'Ubah Data Aset Kemitraan (KSO)' : 'Pencatatan Aset Kemitraan (KSO)');
    $pageBreadcrumb = $isMilikRsud
        ? 'Master Utama / Data ASTAP / Ubah Data Aset Milik RSUD'
        : ($isFromAstap 
            ? 'Master Utama / Data ASTAP / Ubah Data Aset Kemitraan' 
            : ($isEdit ? 'Master Aset / Kelola Kemitraan Aset / Ubah Aset Kemitraan' : 'Master Aset / Kelola Kemitraan Aset / Tambah Aset Kemitraan'));
@endphp

<x-layout :title="($pageTitle) . ' - SIMAT-RK'">
    @section('page-title', $pageTitle)
    @section('breadcrumb', $pageBreadcrumb)

    <script>
        window.editAstapData = @json($astap ?? null);
        window.isMilikRsud = @json($isMilikRsud);
        window.isFromAstap = @json($isFromAstap);
        window.backUrl = @json($backUrl);
        window.dbObjekAsetKemitraan = @json($dbObjekAsetKemitraan ?? []);
        window.initialTipeKemitraan = @json($tipe ?? request()->query('tipe', 'dimanfaatkan'));
    </script>

    <!-- 1. Script Logika Form (Alpine.js & State Management) -->
    @include('pages.kemitraan.form_partials.scripts')

    <div x-data="formKemitraan()" x-cloak class="space-y-6">

        <!-- 2. Top Header Banner & Stepper Tabs Indicator -->
        @include('pages.kemitraan.form_partials.stepper_header')

        <!-- 3. Main Form Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
            
            <!-- LANGKAH 1: Dokumen PKS & Rekanan Mitra Pihak Ketiga -->
            @include('pages.kemitraan.form_partials.step1_pks_mitra')

            <!-- LANGKAH 2: Klasifikasi Kode Barang 108 (Akun 1.5.2) & Nilai Taksiran -->
            @include('pages.kemitraan.form_partials.step2_klasifikasi_108')

            <!-- LANGKAH 3: Lembar Verifikasi Data Aset Kemitraan & Konfirmasi -->
            @include('pages.kemitraan.form_partials.step3_verifikasi_data')

            <!-- Stepper Bottom Navigation -->
            @include('pages.kemitraan.form_partials.stepper_navigation')

        </div>

        <!-- 4. Global Floating Toast Notification -->
        @include('pages.kemitraan.form_partials.dialogs_and_toast')

    </div>
</x-layout>
