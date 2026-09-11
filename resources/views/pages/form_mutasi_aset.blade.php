<x-layout :title="isset($mutasi) ? 'Ubah Pengajuan Mutasi - SIMAT-RK' : 'Pengajuan Mutasi Baru - SIMAT-RK'">
    @section('page-title', isset($mutasi) ? 'Ubah Pengajuan Mutasi' : 'Pengajuan Mutasi Baru')
    @section('breadcrumb', isset($mutasi) ? 'Master Utama / Mutasi Aset / Ubah' : 'Master Utama / Mutasi Aset / Pengajuan Baru')

    @php
        $user = Auth::user();
        $isSubAdmin = in_array($user->role ?? '', ['sub_admin']);
        $userUnitObj = $user->unitModel ?? null;
        if (!$userUnitObj && $user->unit_id) {
            $userUnitObj = \App\Models\Unit::find($user->unit_id);
        }
        $userUnitNama = $userUnitObj?->nama ?? (is_string($user->unit) ? $user->unit : '');
        $userUnitKepala = $userUnitObj?->kepala ?? ($user->name ?? '');

        $initialRegisterIds = [];
        if (old('astap_register_ids')) {
            $initialRegisterIds = array_map('intval', old('astap_register_ids'));
        } elseif (old('astap_register_id')) {
            $initialRegisterIds = [(int) old('astap_register_id')];
        } elseif (isset($relatedRegisterIds) && count($relatedRegisterIds) > 0) {
            $initialRegisterIds = array_map('intval', $relatedRegisterIds);
        } elseif (isset($mutasi) && $mutasi->astap_register_id) {
            $initialRegisterIds = [(int) $mutasi->astap_register_id];
        }
    @endphp

    @include('pages.form_mutasi_aset_partials.scripts')

    <div x-data="formMutasiApp()" x-cloak class="space-y-6">
        @include('pages.form_mutasi_aset_partials.header_card')
        @include('pages.form_mutasi_aset_partials.stepper_nav')

        <form method="POST" action="{{ isset($mutasi) ? route('mutasi.update', $mutasi->id) : route('mutasi.store') }}"
              enctype="multipart/form-data"
              class="space-y-6"
              @submit="submitWithConfirmation($event)">
            @csrf
            @if(isset($mutasi)) @method('PUT') @endif

            {{-- Hidden Inputs --}}
            <input type="hidden" name="jenis_mutasi" :value="jenis_mutasi">
            <template x-for="id in selectedRegisterIds" :key="id">
                <input type="hidden" name="astap_register_ids[]" :value="id">
            </template>
            <input type="hidden" name="astap_register_id" :value="selectedRegisterIds.length > 0 ? selectedRegisterIds[0] : ''">

            {{-- Multi-Step Form Sections --}}
            @include('pages.form_mutasi_aset_partials.step1_jenis_mutasi')
            @include('pages.form_mutasi_aset_partials.step2_unit_ruangan')
            @include('pages.form_mutasi_aset_partials.step3_pilih_barang')
            @include('pages.form_mutasi_aset_partials.step4_ringkasan')
        </form>

        @include('pages.form_mutasi_aset_partials.modal_confirm')
        @include('pages.form_mutasi_aset_partials.toast_notification')
    </div>
</x-layout>
