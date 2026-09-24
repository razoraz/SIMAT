@php
    $isFromEksternal = request('from') === 'eksternal';
    $isEdit = isset($astap);
    $backUrl = $isFromEksternal ? route('mutasi.eksternal') : ($isEdit ? route('astap.index') : route('astap.pilih_jenis'));
    $pageTitle = $isEdit ? 'Ubah Data Mutasi Eksternal' : 'Pencatatan Mutasi Eksternal';
    $breadcrumb = $isEdit 
        ? ($isFromEksternal ? 'Master Aset / Mutasi Eksternal / Ubah Data Mutasi Eksternal' : 'Master Utama / Data ASTAP / Ubah Data Mutasi Eksternal')
        : ($isFromEksternal ? 'Master Aset / Mutasi Eksternal / Tambah Mutasi Eksternal' : 'Master Utama / Data ASTAP / Tambah Mutasi Eksternal');

    $initialAstap = null;
    if ($isEdit) {
        $firstReg = $astap->registers ? $astap->registers->first() : null;
        $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : [];
        $me = $astap->mutasiEksternal;
        $initialAstap = [
            'id' => $astap->id,
            'from' => request('from', 'eksternal'),
            'sumber_dana' => 'pelimpahan_skpd',
            'tahun_perolehan' => (int) ($astap->tahun_perolehan ?: date('Y')),
            'triwulan' => $astap->triwulan ?: 'TW I',
            'mutasi_asal' => $me?->opd_asal ?: ($astap->pelimpahanSkpd?->skpd_asal ?: ($astap->mutasi_asal ?: ($spec['skpd_asal'] ?? ''))),
            'mutasi_nomor_bamb' => $me?->nomor_bamb ?: ($astap->pelimpahanSkpd?->nomor_bamb ?: ($astap->mutasi_nomor_bamb ?: ($astap->bast_dokumen_nomor ?: ($spec['nomor_bamb'] ?? '')))),
            'mutasi_tanggal' => $me?->tanggal_mutasi ? $me->tanggal_mutasi->format('Y-m-d') : ($astap->pelimpahanSkpd?->tanggal_bamb ?: ($astap->mutasi_tanggal ?: ($astap->bast_dokumen_tanggal ?: ($spec['tanggal_bamb'] ?? date('Y-m-d'))))),
            'total_realisasi' => (float) ($me?->nilai_perolehan ?: ($astap->total_realisasi ?: ($astap->pelimpahanSkpd?->nilai_perolehan ?: 0))),
            'mutasi_keterangan' => $me?->alasan_mutasi ?: ($astap->pelimpahanSkpd?->keterangan ?: ($astap->mutasi_keterangan ?: ($astap->keterangan_tambahan ?: ($spec['keterangan'] ?? '')))),
            'nama_barang' => $astap->nama_barang,
            'jenis_astap_id' => $astap->jenis_astap_id,
            'jumlah_volume' => (int) ($astap->jumlah_volume ?: ($me?->jumlah_volume ?: ($astap->registers ? $astap->registers->count() : 1))),
            'satuan' => $astap->satuan ?: ($me?->satuan ?: 'Unit'),
            'kondisi' => $firstReg?->kondisi ?: ($me?->kondisi ?: ($spec['kondisi'] ?? 'Baik')),
            'unit_id' => $me?->unit_id ?: ($astap->unit_id ?: ($firstReg?->unit_id ?: '')),
            'alamat_barang' => $astap->alamat_barang ?: 'RSUD Dr. H. Koesnadi Bondowoso, Jl. Piere Tendean No. 1',
            'ppk_nama' => $me?->pj_tujuan_nama ?: ($astap->ppk_nama ?: ($spec['ppk_nama'] ?? '')),
            'ppk_nip' => $me?->pj_tujuan_nip ?: ($astap->ppk_nip ?: ($spec['ppk_nip'] ?? '')),
            'tanah_items' => $spec['tanah_items'] ?? [
                [
                    'tanah_hak' => $spec['hak_tanah'] ?? 'Hak Pakai',
                    'tanah_sertifikat_tgl' => $spec['sertifikat_tgl'] ?? '',
                    'tanah_sertifikat_no' => $spec['sertifikat_no'] ?? ($spec['sertifikat_nomor'] ?? ''),
                    'tanah_kondisi' => $spec['kondisi'] ?? 'Baik',
                    'tanah_penggunaan' => $spec['penggunaan'] ?? 'Bangunan Fasilitas Kesehatan & Pelayanan Rumah Sakit',
                    'tanah_jumlah_bidang' => 1,
                    'tanah_luas_m2' => $spec['luas_m2'] ?? '',
                    'tanah_alamat' => '',
                    'tanah_nilai_fisik' => (float) ($astap->total_realisasi ?: 0),
                ]
            ],
            'sertifikat_nomor' => $spec['sertifikat_no'] ?? ($spec['sertifikat_nomor'] ?? ''),
            'merk' => $spec['merk'] ?? '',
            'type' => $spec['type'] ?? '',
            'no_pabrik' => $spec['no_pabrik'] ?? '',
            'ukuran' => $spec['ukuran'] ?? '',
            'bahan' => $spec['bahan'] ?? '',
            'no_rangka' => $spec['no_rangka'] ?? '',
            'no_mesin' => $spec['no_mesin'] ?? '',
            'no_polisi' => $spec['no_polisi'] ?? '',
            'gedung_luas_m2' => $spec['gedung_luas_m2'] ?? ($spec['luas_m2'] ?? ''),
            'gedung_bertingkat' => $spec['gedung_bertingkat'] ?? 'Tidak',
            'gedung_beton' => $spec['gedung_beton'] ?? 'Beton',
            'gedung_status_tanah' => $spec['gedung_status_tanah'] ?? 'Tanah Pemda',
        ];
    }
@endphp

<x-layout :title="($isEdit ? 'Ubah Data Mutasi Eksternal: ' . $astap->nama_barang : 'Pencatatan Mutasi Eksternal (Antar-OPD / Pelimpahan SKPD)') . ' - SIMAT-RK'">
    @section('page-title', $pageTitle)
    @section('breadcrumb', $breadcrumb)

    <div x-data="formMutasiEksternal()" x-cloak class="max-w-5xl mx-auto space-y-6 py-2">

        <!-- Top Header & Back -->
        @include('pages.form_mutasi_eksternal_partials.header_card')

        <!-- Stepper Navigation Bar -->
        @include('pages.form_mutasi_eksternal_partials.stepper_nav')

        <!-- MAIN FORM CONTAINER -->
        <form @submit.prevent="submitForm" class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl space-y-6">

            <!-- LANGKAH 1: DOKUMEN BAMB & SKPD PENGIRIM -->
            @include('pages.form_mutasi_eksternal_partials.step1_bamb_skpd')

            <!-- LANGKAH 2: KLASIFIKASI KODE BARANG 108 -->
            @include('pages.form_mutasi_eksternal_partials.step2_kode108')

            <!-- LANGKAH 3: RINCIAN SPESIFIKASI KIB & PENEMPATAN RUANGAN RSUD -->
            @include('pages.form_mutasi_eksternal_partials.step3_spesifikasi_penempatan')

            <!-- BOTTOM NAVIGATION BUTTONS -->
            @include('pages.form_mutasi_eksternal_partials.stepper_navigation')

        </form>

    </div>

    <!-- Alpine.js Script Implementation -->
    @include('pages.form_mutasi_eksternal_partials.scripts')
</x-layout>
