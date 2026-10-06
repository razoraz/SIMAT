@php
    $isFromEksternal = request('from') === 'eksternal';
    $isEdit = isset($astap);
    $backUrl = $isFromEksternal ? route('mutasi.eksternal') : ($isEdit ? route('astap.index') : route('astap.pilih_jenis'));
    $pageTitle = $isEdit ? 'Ubah Data Pelimpahan Aset SKPD' : 'Pencatatan Pelimpahan Aset SKPD';
    $breadcrumb = $isEdit 
        ? ($isFromEksternal ? 'Master Aset / Mutasi Eksternal / Ubah Data Pelimpahan Aset' : 'Master Utama / Data ASTAP / Ubah Data Pelimpahan Aset')
        : ($isFromEksternal ? 'Master Aset / Mutasi Eksternal / Tambah Pelimpahan Aset' : 'Master Utama / Data ASTAP / Tambah Pelimpahan Aset');

    $initialAstap = null;
    if ($isEdit) {
        $firstReg = $astap->registers ? $astap->registers->first() : null;
        $spec = is_array($astap->spesifikasi_json) ? $astap->spesifikasi_json : (is_string($astap->spesifikasi_json) ? (json_decode($astap->spesifikasi_json, true) ?: []) : []);
        $me = $astap->mutasiEksternal;
        
        $rawTgl = $me?->tanggal_mutasi ?: ($astap->pelimpahanSkpd?->tanggal_bamb ?: ($astap->mutasi_tanggal ?: ($astap->bast_dokumen_tanggal ?: ($spec['tanggal_bamb'] ?? null))));
        $mutasiTanggal = date('Y-m-d');
        if ($rawTgl) {
            try {
                $mutasiTanggal = \Carbon\Carbon::parse($rawTgl)->format('Y-m-d');
            } catch (\Throwable $e) {
                $mutasiTanggal = date('Y-m-d');
            }
        }

        $initialAstap = [
            'id' => $astap->id,
            'from' => request('from', 'eksternal'),
            'sumber_dana' => 'pelimpahan_skpd',
            'tahun_perolehan' => (int) ($astap->tahun_perolehan ?: date('Y')),
            'triwulan' => $astap->triwulan ?: 'TW I',
            'mutasi_asal' => $me?->opd_asal ?: ($astap->pelimpahanSkpd?->skpd_asal ?: ($astap->mutasi_asal ?: ($spec['skpd_asal'] ?? ''))),
            'mutasi_nomor_bamb' => $me?->nomor_bamb ?: ($astap->pelimpahanSkpd?->nomor_bamb ?: ($astap->mutasi_nomor_bamb ?: ($astap->bast_dokumen_nomor ?: ($spec['nomor_bamb'] ?? '')))),
            'mutasi_tanggal' => $mutasiTanggal,
            'total_realisasi' => (float) ($me?->nilai_perolehan ?: ($astap->total_realisasi ?: ($astap->pelimpahanSkpd?->nilai_perolehan ?: 0))),
            'mutasi_keterangan' => $me?->alasan_mutasi ?: ($astap->pelimpahanSkpd?->keterangan ?: ($astap->mutasi_keterangan ?: ($astap->keterangan_tambahan ?: ($spec['keterangan'] ?? '')))),
            'nomor_sk_dasar' => $me?->nomor_sk_dasar ?: ($spec['nomor_sk_dasar'] ?? ''),
            'alamat_instansi' => $me?->alamat_instansi ?: ($spec['alamat_instansi'] ?? ''),
            'pj_asal_nama' => $me?->pj_asal_nama ?: ($spec['pj_asal_nama'] ?? ''),
            'pj_asal_nip' => $me?->pj_asal_nip ?: ($spec['pj_asal_nip'] ?? ''),
            'pj_asal_jabatan' => $me?->pj_asal_jabatan ?: ($spec['pj_asal_jabatan'] ?? ''),
            'dokumen_lampiran_path' => $me?->dokumen_lampiran ?: '',
            'nama_barang' => $astap->nama_barang,
            'jenis_astap_id' => $astap->jenis_astap_id,
            'jumlah_volume' => (int) ($astap->jumlah_volume ?: ($me?->jumlah_volume ?: ($astap->registers ? $astap->registers->count() : 1))),
            'satuan' => $astap->satuan ?: ($me?->satuan ?: 'Unit'),
            'kondisi' => $firstReg?->kondisi ?: ($me?->kondisi ?: ($spec['kondisi'] ?? 'Baik')),
            'unit_id' => $me?->unit_id ?: ($astap->unit_id ?: ($firstReg?->unit_id ?: '')),
            'alamat_barang' => $astap->alamat_barang ?: 'RSUD Dr. H. Koesnandi Bondowoso, Jl. Piere Tendean No. 1',
            'ppk_nama' => ($me?->pj_tujuan_nama && !str_contains(strtolower($me->pj_tujuan_nama), 'yus')) ? $me->pj_tujuan_nama : ($astap->ppk_nama && !str_contains(strtolower($astap->ppk_nama), 'yus') ? $astap->ppk_nama : ($spec['ppk_nama'] ?? 'BUDI HARTONO, S.Sos')),
            'ppk_nip' => ($me?->pj_tujuan_nip && !str_contains($me->pj_tujuan_nip, '19771002') && !str_contains($me->pj_tujuan_nip, '19690412')) ? $me->pj_tujuan_nip : ($astap->ppk_nip && !str_contains($astap->ppk_nip, '19771002') && !str_contains($astap->ppk_nip, '19690412') ? $astap->ppk_nip : ($spec['ppk_nip'] ?? '19760229 200801 1 010')),
            'tanah_items' => $spec['tanah_items'] ?? [],
            'mesin_items' => $spec['mesin_items'] ?? [],
            'gedung_items' => $spec['gedung_items'] ?? [],
            'jaringan_items' => $spec['jaringan_items'] ?? [],
            'lainnya_items' => $spec['lainnya_items'] ?? [],
            'tanah_luas_m2' => $spec['tanah_luas_m2'] ?? ($spec['luas_m2'] ?? ''),
            'tanah_hak' => $spec['tanah_hak'] ?? ($spec['hak_tanah'] ?? 'Hak Pakai'),
            'tanah_sertifikat_no' => $spec['tanah_sertifikat_no'] ?? ($spec['sertifikat_no'] ?? ($spec['sertifikat_nomor'] ?? '')),
            'tanah_sertifikat_tgl' => $spec['tanah_sertifikat_tgl'] ?? ($spec['sertifikat_tgl'] ?? ''),
            'tanah_penggunaan' => $spec['tanah_penggunaan'] ?? ($spec['penggunaan'] ?? 'Bangunan Fasilitas Pelayanan Rumah Sakit'),
            'sertifikat_nomor' => $spec['sertifikat_no'] ?? ($spec['sertifikat_nomor'] ?? ''),
            'merk' => $spec['merk'] ?? '',
            'type' => $spec['type'] ?? '',
            'no_pabrik' => $spec['no_pabrik'] ?? '',
            'ukuran' => $spec['ukuran'] ?? '',
            'bahan' => $spec['bahan'] ?? '',
            'is_extracom' => $spec['is_extracom'] ?? false,
            'tahun_pembuatan' => $spec['tahun_pembuatan'] ?? ($spec['mesin_tahun_pembuatan'] ?? null),
            'no_rangka' => $spec['no_rangka'] ?? '',
            'no_mesin' => $spec['no_mesin'] ?? '',
            'no_bpkb' => $spec['no_bpkb'] ?? ($spec['mesin_no_bpkb'] ?? ''),
            'no_polisi' => $spec['no_polisi'] ?? '',
            'ruang_pemegang' => $spec['ruang_pemegang'] ?? '',
            'gedung_luas_m2' => $spec['gedung_luas_m2'] ?? ($spec['luas_m2'] ?? ''),
            'gedung_bertingkat' => $spec['gedung_bertingkat'] ?? 'Tidak',
            'gedung_beton' => $spec['gedung_beton'] ?? 'Beton',
            'gedung_status_tanah' => $spec['gedung_status_tanah'] ?? 'Tanah Pemda',
            'gedung_dokumen_no' => $spec['gedung_dokumen_no'] ?? ($spec['dokumen_gedung_nomor'] ?? ''),
            'jaringan_luas_m2' => $spec['jaringan_luas_m2'] ?? ($spec['luas_m2'] ?? ''),
            'jaringan_bertingkat' => $spec['jaringan_bertingkat'] ?? 'Tidak',
            'jaringan_beton' => $spec['jaringan_beton'] ?? 'Beton',
            'jaringan_status_tanah' => $spec['jaringan_status_tanah'] ?? 'Tanah Pemda',
            'jaringan_dokumen_no' => $spec['jaringan_dokumen_no'] ?? ($spec['dokumen_jaringan_nomor'] ?? ''),
        ];
    }
@endphp

<x-layout :title="($isEdit ? 'Ubah Data Pelimpahan Aset: ' . $astap->nama_barang : 'Pencatatan Pelimpahan Aset SKPD (Mutasi Eksternal)') . ' - SIMAT-RK'">
    @section('page-title', $pageTitle)
    @section('breadcrumb', $breadcrumb)

    <!-- 1. Script Logika Form (Alpine.js & State Management) -->
    @include('pages.mutasi_eksternal.form_partials.scripts')

    <div x-data="formMutasiEksternal()" x-cloak class="space-y-6">

        <!-- 2. Top Header Banner & Stepper Tabs Indicator -->
        @include('pages.mutasi_eksternal.form_partials.stepper_header')

        <!-- 3. Main Form Container -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-6 sm:p-8 shadow-2xl">
            
            <!-- LANGKAH 1: Berita Acara (BAMB/BAST) & SKPD Pengirim -->
            @include('pages.mutasi_eksternal.form_partials.step1_bamb_skpd')

            <!-- LANGKAH 2: Klasifikasi Kode Barang 108 & Spesifikasi Fisik KIB -->
            @include('pages.mutasi_eksternal.form_partials.step2_klasifikasi_108')

            <!-- LANGKAH 3: Lembar Verifikasi Data Pelimpahan & Register NIBAR -->
            @include('pages.mutasi_eksternal.form_partials.step3_verifikasi_data')

            <!-- Stepper Bottom Navigation -->
            @include('pages.mutasi_eksternal.form_partials.stepper_navigation')

        </div>

        <!-- 4. Global Floating Toast Notification -->
        @include('pages.mutasi_eksternal.form_partials.dialogs_and_toast')

    </div>
</x-layout>
