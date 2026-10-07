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
        ];

        // Ekstrak repeater items dengan fallback cerdas dari registers bila ada banyak unit
        $mesinItems = $spec['mesin_items'] ?? [];
        if (empty($mesinItems) && $astap->registers && $astap->registers->count() > 1 && (str_starts_with($astap->kode_108 ?? '', '1.3.2') || $astap->category === 'KIB B')) {
            $mesinItems = [];
            foreach ($astap->registers as $reg) {
                $mesinItems[] = [
                    'mesin_nama_barang' => $astap->nama_barang,
                    'mesin_kondisi'     => $reg->kondisi ?: ($me?->kondisi ?: 'Baik'),
                    'mesin_jumlah'      => 1,
                    'mesin_satuan'      => $astap->satuan ?: 'Unit',
                    'mesin_nilai_satuan'=> (float)($astap->harga_satuan ?: ($astap->total_realisasi / max(1, $astap->registers->count()))),
                    'mesin_merk'        => $spec['merk'] ?? '',
                    'mesin_type'        => $spec['type'] ?? '',
                    'mesin_ukuran'      => $spec['ukuran'] ?? '',
                    'mesin_bahan'       => $spec['bahan'] ?? '',
                    'mesin_no_pabrik'   => $spec['no_pabrik'] ?? '',
                    'mesin_no_rangka'   => $spec['no_rangka'] ?? '',
                    'mesin_no_mesin'    => $spec['no_mesin'] ?? '',
                    'mesin_no_polisi'   => $spec['no_polisi'] ?? '',
                    'mesin_no_bpkb'     => $spec['no_bpkb'] ?? '',
                    'ruang_pemegang'    => $reg->ruang_pemegang ?: ($me?->ruangan_tujuan ?: ''),
                    'is_extracom'       => !empty($spec['is_extracom']),
                ];
            }
        }

        $tanahItems = $spec['tanah_items'] ?? [];
        if (empty($tanahItems) && $astap->registers && $astap->registers->count() > 1 && (str_starts_with($astap->kode_108 ?? '', '1.3.1') || $astap->category === 'KIB A')) {
            $tanahItems = [];
            foreach ($astap->registers as $reg) {
                $tanahItems[] = [
                    'tanah_nama_barang'    => $astap->nama_barang,
                    'tanah_kondisi'        => $reg->kondisi ?: ($me?->kondisi ?: 'Baik'),
                    'tanah_jumlah_bidang'  => 1,
                    'tanah_satuan'         => $astap->satuan ?: 'Bidang',
                    'tanah_nilai_satuan'   => (float)($astap->harga_satuan ?: ($astap->total_realisasi / max(1, $astap->registers->count()))),
                    'tanah_hak'            => $spec['hak_tanah'] ?? 'Hak Pakai',
                    'tanah_sertifikat_no'  => $spec['sertifikat_no'] ?? '',
                    'tanah_sertifikat_tgl' => $spec['sertifikat_tgl'] ?? '',
                    'tanah_penggunaan'     => $spec['penggunaan'] ?? '',
                    'tanah_luas_m2'        => $spec['luas_m2'] ?? '',
                    'tanah_alamat'         => $astap->alamat_barang ?: '',
                    'ruang_pemegang'       => $reg->ruang_pemegang ?: ($me?->ruangan_tujuan ?: ''),
                ];
            }
        }

        $gedungItems = $spec['gedung_items'] ?? [];
        if (empty($gedungItems) && $astap->registers && $astap->registers->count() > 1 && (str_starts_with($astap->kode_108 ?? '', '1.3.3') || $astap->category === 'KIB C')) {
            $gedungItems = [];
            foreach ($astap->registers as $reg) {
                $gedungItems[] = [
                    'gedung_nama_barang'     => $astap->nama_barang,
                    'gedung_kondisi'         => $reg->kondisi ?: ($me?->kondisi ?: 'Baik'),
                    'gedung_jumlah_bangunan' => 1,
                    'gedung_satuan'          => $astap->satuan ?: 'Gedung',
                    'gedung_nilai_satuan'    => (float)($astap->harga_satuan ?: ($astap->total_realisasi / max(1, $astap->registers->count()))),
                    'gedung_luas_m2'         => $spec['gedung_luas_m2'] ?? ($spec['luas_m2'] ?? ''),
                    'gedung_bertingkat'      => $spec['gedung_bertingkat'] ?? 'Tidak',
                    'gedung_beton'           => $spec['gedung_beton'] ?? 'Beton',
                    'gedung_status_tanah'    => $spec['gedung_status_tanah'] ?? 'Tanah Pemda',
                    'gedung_dokumen_no'      => $spec['gedung_dokumen_no'] ?? '',
                    'gedung_dokumen_tgl'     => $spec['gedung_dokumen_tgl'] ?? '',
                    'gedung_fungsi'          => $spec['gedung_fungsi'] ?? '',
                    'gedung_alamat'          => $spec['gedung_alamat'] ?? ($astap->alamat_barang ?: ''),
                    'ruang_pemegang'         => $reg->ruang_pemegang ?: ($me?->ruangan_tujuan ?: ''),
                ];
            }
        }

        $jaringanItems = $spec['jaringan_items'] ?? [];
        if (empty($jaringanItems) && $astap->registers && $astap->registers->count() > 1 && (str_starts_with($astap->kode_108 ?? '', '1.3.4') || $astap->category === 'KIB D')) {
            $jaringanItems = [];
            foreach ($astap->registers as $reg) {
                $jaringanItems[] = [
                    'jaringan_nama_barang'  => $astap->nama_barang,
                    'jaringan_kondisi'      => $reg->kondisi ?: ($me?->kondisi ?: 'Baik'),
                    'jaringan_jumlah'       => 1,
                    'jaringan_satuan'       => $astap->satuan ?: 'Ruas',
                    'jaringan_nilai_satuan' => (float)($astap->harga_satuan ?: ($astap->total_realisasi / max(1, $astap->registers->count()))),
                    'jaringan_luas_m2'      => $spec['jaringan_luas_m2'] ?? ($spec['luas_m2'] ?? ''),
                    'jaringan_panjang_m'    => $spec['jaringan_panjang_m'] ?? '',
                    'jaringan_lebar_m'      => $spec['jaringan_lebar_m'] ?? '',
                    'jaringan_bertingkat'   => $spec['jaringan_bertingkat'] ?? 'Tidak',
                    'jaringan_konstruksi'   => $spec['jaringan_konstruksi'] ?? '',
                    'jaringan_beton'        => $spec['jaringan_beton'] ?? 'Beton',
                    'jaringan_status_tanah' => $spec['jaringan_status_tanah'] ?? 'Tanah Pemda',
                    'jaringan_dokumen_no'   => $spec['jaringan_dokumen_no'] ?? '',
                    'jaringan_dokumen_tgl'  => $spec['jaringan_dokumen_tgl'] ?? '',
                    'jaringan_keterangan'   => $spec['jaringan_keterangan'] ?? '',
                    'jaringan_alamat'       => $spec['jaringan_alamat'] ?? ($astap->alamat_barang ?: ''),
                    'ruang_pemegang'        => $reg->ruang_pemegang ?: ($me?->ruangan_tujuan ?: ''),
                ];
            }
        }

        $lainnyaItems = $spec['lainnya_items'] ?? [];
        if (empty($lainnyaItems) && $astap->registers && $astap->registers->count() > 1 && (str_starts_with($astap->kode_108 ?? '', '1.3.5') || $astap->category === 'KIB E')) {
            $lainnyaItems = [];
            foreach ($astap->registers as $reg) {
                $lainnyaItems[] = [
                    'lainnya_nama_barang' => $astap->nama_barang,
                    'lainnya_kondisi'     => $reg->kondisi ?: ($me?->kondisi ?: 'Baik'),
                    'lainnya_jumlah'      => 1,
                    'lainnya_satuan'      => $astap->satuan ?: 'Buah',
                    'lainnya_nilai_satuan'=> (float)($astap->harga_satuan ?: ($astap->total_realisasi / max(1, $astap->registers->count()))),
                    'lainnya_judul'       => $spec['lainnya_judul'] ?? $astap->nama_barang,
                    'lainnya_pencipta'    => $spec['lainnya_pencipta'] ?? '',
                    'lainnya_spesifikasi' => $spec['lainnya_spesifikasi'] ?? '',
                    'lainnya_tahun'       => $spec['lainnya_tahun'] ?? null,
                    'lainnya_ukuran'      => $spec['lainnya_ukuran'] ?? '',
                    'lainnya_asal_daerah' => $spec['lainnya_asal_daerah'] ?? '',
                    'lainnya_bahan'       => $spec['lainnya_bahan'] ?? '',
                    'lainnya_jenis'       => $spec['lainnya_jenis'] ?? 'Buku / Kepustakaan Medis',
                    'lainnya_no_pabrik'   => $spec['no_pabrik'] ?? '',
                    'lainnya_keterangan'  => $spec['lainnya_keterangan'] ?? '',
                    'ruang_pemegang'      => $reg->ruang_pemegang ?: ($me?->ruangan_tujuan ?: ''),
                    'is_extracom'         => !empty($spec['is_extracom']),
                ];
            }
        }

        $initialAstap['tanah_items'] = $tanahItems;
        $initialAstap['mesin_items'] = $mesinItems;
        $initialAstap['gedung_items'] = $gedungItems;
        $initialAstap['jaringan_items'] = $jaringanItems;
        $initialAstap['lainnya_items'] = $lainnyaItems;

        $initialAstap += [
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
