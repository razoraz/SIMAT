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

    @include('pages.mutasi_aset.form_partials.scripts')

    <div x-data="formMutasiApp()" x-cloak class="space-y-6">
        @include('pages.mutasi_aset.form_partials.header_card')
        @include('pages.mutasi_aset.form_partials.stepper_nav')

        @if ($errors->any())
            <div class="p-4 rounded-2xl bg-rose-500/10 border border-rose-500/30 text-rose-300 text-sm flex items-start space-x-3 shadow-lg">
                <div class="rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center shrink-0" style="width: 32px; height: 32px; min-width: 32px; min-height: 32px;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="space-y-1">
                    <p class="font-bold text-rose-200">Pengajuan belum dapat diproses karena data belum lengkap atau ada kendala:</p>
                    <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-300/90 font-medium">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

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
            @include('pages.mutasi_aset.form_partials.step1_jenis_mutasi')
            @include('pages.mutasi_aset.form_partials.step2_unit_ruangan')
            @include('pages.mutasi_aset.form_partials.step3_pilih_barang')
            @include('pages.mutasi_aset.form_partials.step4_ringkasan')
        </form>

        @include('pages.mutasi_aset.form_partials.modal_confirm')
        @include('pages.mutasi_aset.form_partials.toast_notification')
    </div>
</x-layout>
