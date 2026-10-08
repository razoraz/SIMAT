<!-- ========================================================================= -->
<!-- TABEL DATA MASTER ASET KEMITRAAN PIHAK KETIGA (AKUN 1.5.2)                -->
<!-- DIPISAH: 1. Aset RSUD Dimanfaatkan Mitra | 2. Aset Ditambahkan Mitra       -->
<!-- ========================================================================= -->

@php
    $isDimanfaatkan = function($row) {
        $astap = $row->astap;

        // 1. Aset yang berasal dari REKLASIFIKASI ke Kemitraan → selalu masuk Tabel Pemanfaatan
        if ($astap && (
            $astap->is_reklas ||
            ($astap->reklas && $astap->reklas->isNotEmpty())
        )) {
            return true;
        }

        // 2. Aset RSUD Dimanfaatkan Mitra: ada tautan eksplisit ke objek BMD RSUD
        if (!empty($row->objek_nibar) || !empty($row->objek_register_id) || !empty($row->objek_astap_id)) {
            return true;
        }
        $spec = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap?->spesifikasi_json ?? '[]', true) ?: []);
        if (!empty($spec['objek_nibar']) || !empty($spec['objek_register_id']) || !empty($spec['objek_astap_id'])) {
            return true;
        }

        // 3. Bukan reklas dan tidak ada tautan objek BMD → masuk Tabel Ditambahkan Mitra
        return false;
    };

    $resolveKemitraanKib = function($row) {
        if (!empty($row->asal_kib)) {
            return strtoupper($row->asal_kib);
        }
        $astap = $row->astap ?? null;
        $objekAstap = $row->objekAstap ?? null;
        $cat = $objekAstap?->category ?: ($astap?->category ?: null);
        if ($cat && in_array(strtoupper($cat), ['KIB A', 'KIB B', 'KIB C', 'KIB D', 'KIB E', 'KIB F', 'ATB', 'EXTRACOM', 'ASET LAIN'])) {
            return strtoupper($cat);
        }
        $kode = $astap?->kode_108 ?: ($objekAstap?->kode_108 ?: ($astap?->kode_barang ?: ''));
        if (str_contains($kode, '.01.01.001') || str_ends_with($kode, '.001') || str_starts_with($kode, '1.3.1')) return 'KIB A';
        if (str_contains($kode, '.01.01.002') || str_ends_with($kode, '.002') || str_starts_with($kode, '1.3.2')) return 'KIB B';
        if (str_contains($kode, '.01.01.003') || str_ends_with($kode, '.003') || str_starts_with($kode, '1.3.3')) return 'KIB C';
        if (str_contains($kode, '.01.01.004') || str_ends_with($kode, '.004') || str_starts_with($kode, '1.3.4')) return 'KIB D';
        if (str_contains($kode, '.01.01.005') || str_ends_with($kode, '.005') || str_starts_with($kode, '1.3.5')) return 'KIB E';
        
        $spec = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap?->spesifikasi_json ?? '[]', true) ?: []);
        if (!empty($spec['tanah_nama_barang']) || !empty($spec['tanah_luas_m2']) || !empty($spec['luas_m2'])) return 'KIB A';
        if (!empty($spec['gedung_nama_bangunan']) || !empty($spec['gedung_luas_lantai'])) return 'KIB C';

        $rowKeterangan = isset($row->keterangan) ? $row->keterangan : '';
        $nama = strtolower(($astap?->nama_barang ?? '') . ' ' . ($objekAstap?->nama_barang ?? '') . ' ' . $rowKeterangan);
        if (str_contains($nama, 'tanah') || str_contains($nama, 'lahan') || str_contains($nama, 'kavling')) return 'KIB A';
        if (str_contains($nama, 'gedung') || str_contains($nama, 'bangunan') || str_contains($nama, 'ruang') || str_contains($nama, 'paviliun')) return 'KIB C';
        if (str_contains($nama, 'jalan') || str_contains($nama, 'irigasi') || str_contains($nama, 'jaringan') || str_contains($nama, 'instalasi')) return 'KIB D';
        if (str_contains($nama, 'mesin') || str_contains($nama, 'alat') || str_contains($nama, 'alkes') || str_contains($nama, 'kendaraan') || str_contains($nama, 'laboratorium')) return 'KIB B';
        return 'KIB B';
    };

    $recordsDimanfaatkan = collect($kemitraanRecords ?? [])->filter(fn($r) => $isDimanfaatkan($r))->values();
    $recordsDitambahkan  = collect($kemitraanRecords ?? [])->filter(fn($r) => !$isDimanfaatkan($r))->values();

    // Kumpulkan seluruh ID astap yang sudah terdaftar resmi di tabel kemitraan agar tidak terduplikasi
    $existingKemitraanAstapIds = collect($kemitraanRecords ?? [])->flatMap(function($r) {
        return array_filter([$r->astap_id, $r->objek_astap_id]);
    })->unique()->values()->toArray();

    // Integrasikan Aset BMD RSUD yang merupakan hasil reklasifikasi ke Kemitraan (1.5.2) HANYA jika belum memiliki PKS
    $seenReklasAstapIds = $existingKemitraanAstapIds;
    foreach ($reklasKemitraanRecords ?? [] as $reklasItem) {
        if (!in_array($reklasItem->astap_id, $seenReklasAstapIds)) {
            $seenReklasAstapIds[] = $reklasItem->astap_id; // Kunci agar unik dan tidak dobel/kembar
            $rAstap = $reklasItem->astap;
            if ($rAstap) {
                $rReg = $rAstap->registers->first();
                $recordsDimanfaatkan->push((object) [
                    'id'                  => null,
                    'is_reklas_pending'   => true,
                    'reklas_id'           => $reklasItem->id,
                    'astap_id'            => $rAstap->id,
                    'astap'               => $rAstap,
                    'objek_astap_id'      => $rAstap->id,
                    'objekAstap'          => $rAstap,
                    'objek_register_id'   => $rReg?->id,
                    'objekRegister'       => $rReg,
                    'objek_nibar'         => $rReg?->nibar ?: null,
                    'mitra_nama'          => '-',
                    'nomor_pks'           => 'Belum Ada PKS',
                    'tanggal_pks'         => $reklasItem->tanggal_reklas,
                    'skema_kemitraan'     => 'Reklasifikasi',
                    'status_konsesi'      => 'Siap Dikerjasamakan',
                    'sisa_hari_konsesi'   => null,
                    'nilai_aset'          => (float) ($reklasItem->nilai_reklas ?: $rAstap->total_realisasi),
                    'tahun'               => $reklasItem->tahun ?: ($rAstap->tahun_perolehan ?: date('Y')),
                    'triwulan'            => 'TW ' . ($reklasItem->triwulan ?: 1),
                    'tanggal_mulai'       => $reklasItem->tanggal_reklas,
                    'tanggal_selesai'     => null,
                    'asal_kib'            => $reklasItem->asal_kib ?: ($rAstap->category ?: 'KIB A'),
                    'keterangan'          => $reklasItem->keterangan ?? ($reklasItem->alasan_reklas ?? null),
                ]);
            }
        }
    }

    $buildRowMeta = function($row, $isDimanfaatkanVal) use ($resolveKemitraanKib) {
        $astap = $row->astap ?? null;
        $objekAstap = $row->objekAstap ?? null;
        $reklasHistory = $astap?->reklas?->sortByDesc('id')->first();
        $objekAsetBmd = $objekAstap ?: ($row->objekRegister?->astap ?? null);
        $spec = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap?->spesifikasi_json ?? '[]', true) ?: []);

        $namaFisik = $spec['tanah_nama_barang'] ?? ($spec['gedung_nama_bangunan'] ?? null);
        if (!$namaFisik && $reklasHistory && !empty($reklasHistory->asal_nama)) {
            $namaFisik = $reklasHistory->asal_nama;
        }
        if (!$namaFisik && !empty($objekAsetBmd?->nama_barang) && !str_starts_with(strtolower($objekAsetBmd->nama_barang), 'kerja sama pemanfaatan')) {
            $namaFisik = $objekAsetBmd->nama_barang;
        }
        if (!$namaFisik) {
            $namaFisik = $spec['mesin_items'][0]['mesin_nama_barang'] ?? ($spec['tanah_items'][0]['tanah_nama_barang'] ?? ($astap?->nama_barang ?: 'Objek Aset'));
        }

        $kib = $resolveKemitraanKib($row);
        $skema = $row->skema_kemitraan ?: ($spec['skema_kemitraan'] ?? 'Sewa');
        $tahun = (int) ($row->tahun ?: ($astap?->tahun_perolehan ?: date('Y')));
        $tw = $row->triwulan ?: ($astap?->triwulan ?: 'TW I');
        $status = $row->status_konsesi ?: 'Aktif';

        $searchParts = [
            $namaFisik,
            $astap?->nama_barang,
            $row->mitra_nama,
            $row->nomor_pks,
            $row->objek_nibar,
            $row->objekRegister?->nibar,
            $row->objekRegister?->no_register,
            $astap?->kode_108,
            $astap?->kode_barang,
            $reklasHistory?->asal_nama,
            $reklasHistory?->tujuan_nama,
            isset($row->keterangan) ? $row->keterangan : '',
            $spec['merk'] ?? '',
            $spec['type'] ?? '',
        ];
        $searchText = strtolower(implode(' ', array_filter($searchParts)));

        return [
            'id'              => $row->id ?? null,
            'category'        => $kib,
            'skema'           => $skema,
            'tahun'           => $tahun,
            'triwulan'        => $tw,
            'status'          => $status,
            'is_dimanfaatkan' => $isDimanfaatkanVal,
            'search_text'     => $searchText,
        ];
    };

    $dimanfaatkanMetaList = [];
    foreach ($recordsDimanfaatkan as $r) {
        $dimanfaatkanMetaList[] = $buildRowMeta($r, true);
    }

    $ditambahkanMetaList = [];
    foreach ($recordsDitambahkan as $r) {
        $ditambahkanMetaList[] = $buildRowMeta($r, false);
    }

    $allMetaList = [];
    foreach ($kemitraanRecords as $r) {
        $allMetaList[] = $buildRowMeta($r, $isDimanfaatkan($r));
    }
@endphp

<script>
    window.__dimanfaatkanMetaList = @json($dimanfaatkanMetaList);
    window.__ditambahkanMetaList  = @json($ditambahkanMetaList);
    window.__allMetaList          = @json($allMetaList);
</script>

<div class="space-y-6" x-init="initMetaLists(@js($dimanfaatkanMetaList), @js($ditambahkanMetaList), @js($allMetaList))">

    <!-- ========================================================================= -->
    <!-- SWITCHER TAB & MODE PEMISAH TABEL KEMITRAAN                              -->
    <!-- ========================================================================= -->
    <div class="p-4 sm:p-5 rounded-3xl bg-slate-900/95 border border-slate-800 shadow-xl space-y-3">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-cyan-400 block mb-0.5">
                    🗂️ Pemisah Klasifikasi Aset Kemitraan (Akun 1.5.2)
                </span>
                <h3 class="text-sm sm:text-base font-extrabold text-white flex items-center gap-2">
                    <span>Pemisahan Objek Aset BMD RSUD &amp; Pengadaan Barang KSO Mitra</span>
                </h3>
            </div>

            <!-- Tombol Switcher Tab -->
            <div class="flex items-center gap-1.5 p-1.5 rounded-2xl bg-slate-950 border border-slate-800/90 shrink-0 overflow-x-auto max-w-full">
                <!-- 1. Tampilkan Kedua Tabel Sekaligus -->
                <button type="button" @click="kemitraanTableTab = 'both'"
                    :class="kemitraanTableTab === 'both' ? 'bg-gradient-to-r from-cyan-500/20 to-teal-500/20 text-cyan-300 border-cyan-400 font-extrabold shadow-md shadow-cyan-500/20' : 'text-slate-400 hover:text-white border-transparent'"
                    class="px-3 py-1.5 rounded-xl text-xs border transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                    <span>📑</span>
                    <span>Tampilkan Kedua Tabel</span>
                </button>

                <!-- 2. Tab: Aset RSUD Dimanfaatkan Mitra -->
                <button type="button" @click="kemitraanTableTab = 'dimanfaatkan'"
                    :class="kemitraanTableTab === 'dimanfaatkan' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-400 font-extrabold shadow-md shadow-cyan-500/20' : 'text-slate-400 hover:text-white border-transparent'"
                    class="px-3 py-1.5 rounded-xl text-xs border transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                    <span>🏛️</span>
                    <span>Aset RSUD Dimanfaatkan</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[10px] font-mono font-bold"
                        :class="kemitraanTableTab === 'dimanfaatkan' ? 'bg-cyan-400 text-slate-950' : 'bg-slate-800 text-slate-300'"
                        x-text="countVisibleDimanfaatkan">
                        {{ count($recordsDimanfaatkan) }}
                    </span>
                </button>

                <!-- 3. Tab: Aset Ditambahkan Mitra -->
                <button type="button" @click="kemitraanTableTab = 'ditambahkan'"
                    :class="kemitraanTableTab === 'ditambahkan' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400 font-extrabold shadow-md shadow-emerald-500/20' : 'text-slate-400 hover:text-white border-transparent'"
                    class="px-3 py-1.5 rounded-xl text-xs border transition-all flex items-center gap-1.5 shrink-0 cursor-pointer">
                    <span>📦</span>
                    <span>Aset Ditambahkan Mitra</span>
                    <span class="px-1.5 py-0.2 rounded-md text-[10px] font-mono font-bold"
                        :class="kemitraanTableTab === 'ditambahkan' ? 'bg-emerald-400 text-slate-950' : 'bg-slate-800 text-slate-300'"
                        x-text="countVisibleDitambahkan">
                        {{ count($recordsDitambahkan) }}
                    </span>
                </button>

                <!-- 4. Tab: Semua Data Gabungan -->
                <button type="button" @click="kemitraanTableTab = 'all'"
                    :class="kemitraanTableTab === 'all' ? 'bg-slate-800 text-white border-slate-700 font-extrabold' : 'text-slate-500 hover:text-slate-300 border-transparent'"
                    class="px-2.5 py-1.5 rounded-xl text-xs border transition-all flex items-center gap-1 shrink-0 cursor-pointer">
                    <span>📋</span>
                    <span>Semua (<span x-text="countVisibleAll">{{ count($recordsDimanfaatkan) + count($recordsDitambahkan) }}</span>)</span>
                </button>
            </div>
        </div>

        <div class="flex items-center justify-between text-[11px] text-slate-400 pt-2 border-t border-slate-800/80">
            <template x-if="kemitraanTableTab === 'both'">
                <span class="text-cyan-400 flex items-center gap-1.5">
                    <span>💡</span>
                    <span>Menampilkan 2 tabel terpisah: <strong>Tabel 1 (Aset BMD RSUD yang Dimanfaatkan Mitra)</strong> dan <strong>Tabel 2 (Aset yang Ditambahkan Mitra)</strong>.</span>
                </span>
            </template>
            <template x-if="kemitraanTableTab === 'dimanfaatkan'">
                <span class="text-cyan-300 flex items-center gap-1.5">
                    <span>🏛️</span>
                    <span>Fokus pada aset daerah milik RSUD (Semua KIB: KIB A s.d. E) yang dimanfaatkan oleh pihak ketiga.</span>
                </span>
            </template>
            <template x-if="kemitraanTableTab === 'ditambahkan'">
                <span class="text-emerald-300 flex items-center gap-1.5">
                    <span>📦</span>
                    <span>Fokus pada peralatan, mesin, dan instalasi yang didatangkan/ditambahkan oleh pihak ketiga untuk operasional RSUD.</span>
                </span>
            </template>
            <template x-if="kemitraanTableTab === 'all'">
                <span class="text-slate-400 flex items-center gap-1.5">
                    <span>📋</span>
                    <span>Menampilkan tabel gabungan seluruh arsip aset kemitraan Akun 1.5.2.</span>
                </span>
            </template>
            <span class="font-mono text-cyan-400/80 text-[10px] hidden sm:inline-block">Total <span x-text="countVisibleAll">{{ count($recordsDimanfaatkan) + count($recordsDitambahkan) }}</span> Data Kemitraan</span>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TABEL 1: ASET MILIK RSUD YANG DIMANFAATKAN / DISEWAKAN KE MITRA           -->
    <!-- ========================================================================= -->
    <div x-show="kemitraanTableTab === 'both' || kemitraanTableTab === 'dimanfaatkan'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="rounded-3xl bg-slate-900/90 border border-cyan-500/30 shadow-2xl overflow-hidden space-y-0">
        
        <!-- Table Header Banner -->
        <div class="p-5 border-b border-cyan-500/20 bg-gradient-to-r from-cyan-950/40 via-slate-900/90 to-slate-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-lg shrink-0 shadow-inner">
                    🏛️
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span>Daftar Aset Milik RSUD yang Dimanfaatkan oleh Mitra</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Seluruh aset daerah milik RSUD (Semua KIB: KIB A s.d. E) yang dimanfaatkan atau dikerjasamakan dengan pihak ketiga.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="text-[11px] font-mono font-bold text-cyan-300 bg-cyan-500/10 px-3 py-1 rounded-xl border border-cyan-500/30">
                    <span x-text="countVisibleDimanfaatkan">{{ count($recordsDimanfaatkan) }}</span> Aset BMD Dimanfaatkan
                </span>
            </div>
        </div>

        <div class="border-t border-slate-800/80 bg-slate-950/40 custom-scrollbar" style="max-height: 480px; overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                <thead class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800 shrink-0 sticky top-0 z-10 bg-slate-950">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center bg-slate-950 whitespace-nowrap">No</th>
                        <th class="py-3.5 px-4 min-w-[200px] bg-slate-950">Dokumen PKS &amp; Rekanan</th>
                        <th class="py-3.5 px-4 min-w-[240px] bg-slate-950">Nama &amp; Spesifikasi Barang</th>
                        <th class="py-3.5 px-4 min-w-[220px] bg-slate-950">Identitas 108 Kerja Sama Pemanfaatan</th>
                        <th class="py-3.5 px-4 min-w-[135px] text-center bg-slate-950 whitespace-nowrap">Kondisi</th>
                        <th class="py-3.5 px-4 min-w-[140px] text-right bg-slate-950 whitespace-nowrap">Nilai Pemanfaatan (Rp)</th>
                        <th class="py-3.5 px-4 min-w-[180px] bg-slate-950">Masa Pemanfaatan / Konsesi</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800/80 shrink-0 min-w-[260px] sticky right-0 z-10">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($recordsDimanfaatkan as $idx => $row)
                        @php
                            $astap = $row->astap;
                            $firstReg = $astap?->registers?->first();
                            $spec = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap?->spesifikasi_json ?? '[]', true) ?: []);
                            $sisaHari = $row->sisa_hari_konsesi;
                            $nibarObjek = $row->objek_nibar ?: ($spec['objek_nibar'] ?? null);
                            $objekAsetBmd = $row->objekRegister?->astap ?: $row->objekAstap;
                            
                            // 1. Resolusi Nama Fisik Barang (Sebelum Reklasifikasi - Gambar 1)
                            $reklasHistory = $astap?->reklas?->sortByDesc('id')->first() ?: ($objekAsetBmd?->reklas?->sortByDesc('id')->first());
                            
                            $namaFisikAsli = null;
                            if (!empty($astap->nama_barang) && !str_starts_with(strtolower($astap->nama_barang), 'kerja sama pemanfaatan') && !str_starts_with(strtolower($astap->nama_barang), 'bangun guna serah')) {
                                $namaFisikAsli = $astap->nama_barang;
                            } elseif ($reklasHistory && !empty($reklasHistory->asal_nama)) {
                                $namaFisikAsli = $reklasHistory->asal_nama;
                            } elseif (!empty($objekAsetBmd?->nama_barang) && !str_starts_with(strtolower($objekAsetBmd->nama_barang), 'kerja sama pemanfaatan')) {
                                $namaFisikAsli = $objekAsetBmd->nama_barang;
                            } else {
                                $namaFisikAsli = $spec['mesin_items'][0]['mesin_nama_barang'] ?? ($spec['tanah_items'][0]['tanah_nama_barang'] ?? ($astap?->nama_barang ?: 'Objek Aset BMD RSUD'));
                            }

                            $luasObjek = $spec['luas_m2'] ?? ($spec['tanah_luas_m2'] ?? ($spec['gedung_luas_lantai'] ?? null));
                            $sertifikatObjek = $spec['sertifikat_no'] ?? ($spec['tanah_sertifikat_no'] ?? ($spec['gedung_dokumen_no'] ?? null));
                            $targetPrintId = $row->id ?: ($row->astap_id ?: ($astap?->id ?: null));

                            // 2. Resolusi Identitas & Kode Akun 108 Kemitraan (Kerja Sama Pemanfaatan)
                            $namaAkun108 = $reklasHistory?->tujuan_nama 
                                ?: ($astap?->jenisAstap?->uraian_sub_sub_rincian 
                                ?: ($astap?->jenisAstap?->uraian_sub_rincian 
                                ?: ($astap?->jenisAstap?->nama_jenis ?: 'Kerja Sama Pemanfaatan Tanah')));

                            $kodeAkun108 = $reklasHistory?->tujuan_kode 
                                ?: ($astap?->kode_108 
                                ?: ($objekAsetBmd?->kode_108 
                                ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '1.5.2.01.01.02.001')));
                        @endphp
                        <tr x-show="matchKemitraan({{ json_encode($dimanfaatkanMetaList[$idx] ?? []) }})" class="hover:bg-cyan-950/20 transition-colors group">
                            <!-- 1. Nomor -->
                            <td class="py-4 px-4 text-center font-mono text-cyan-400 font-bold text-xs">
                                {{ $idx + 1 }}
                            </td>

                            <!-- 2. Dokumen PKS & Mitra -->
                            <td class="py-4 px-4">
                                @if($row->is_reklas_pending ?? false)
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                            Reklasifikasi
                                        </span>
                                    </div>
                                    <div class="text-[11px] font-mono text-slate-400">
                                        Akun 1.5.2 Kemitraan
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">
                                        Tgl Reklas: {{ $row->tanggal_pks ? \Carbon\Carbon::parse($row->tanggal_pks)->translatedFormat('d F Y') : '-' }}
                                    </div>
                                @else
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                            {{ $row->skema_kemitraan ?: 'Sewa' }}
                                        </span>
                                        @if($row->status_konsesi === 'Konsesi Berakhir')
                                            <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-amber-500/25 text-amber-300 border border-amber-400/50 shadow-sm shadow-amber-500/20 animate-pulse"
                                                title="Masa konsesi telah berakhir. Aset siap direklasifikasi balik ke KIB asal.">
                                                <span>🔄 SIAP REKLAS BALIK</span>
                                            </span>
                                        @endif
                                        @if(($astap?->is_reklas) || ($astap?->reklas && $astap->reklas->isNotEmpty()) || ($objekAsetBmd?->is_reklas) || ($objekAsetBmd?->reklas && $objekAsetBmd->reklas->isNotEmpty()))
                                            <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-indigo-500/25 text-indigo-300 border border-indigo-400/50 shadow-sm shadow-indigo-500/20"
                                                title="Aset ini memiliki riwayat Reklasifikasi">
                                                <svg class="w-2.5 h-2.5 shrink-0 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                </svg>
                                                <span>REKLASIFIKASI</span>
                                            </span>
                                        @endif
                                        <span class="text-xs font-bold text-white truncate max-w-[180px]" title="{{ $row->mitra_nama }}">
                                            {{ $row->mitra_nama }}
                                        </span>
                                    </div>
                                    <div class="text-[11px] font-mono text-slate-400 truncate max-w-[200px]" title="{{ $row->nomor_pks }}">
                                        No: {{ $row->nomor_pks }}
                                    </div>
                                    <div class="text-[10px] text-slate-500 mt-0.5">
                                        Tgl PKS: {{ $row->tanggal_pks ? \Carbon\Carbon::parse($row->tanggal_pks)->translatedFormat('d F Y') : '-' }}
                                    </div>
                                @endif
                            </td>

                            <!-- 3. Nama & Spesifikasi Barang -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors leading-snug">
                                    {{ $namaFisikAsli }}
                                </div>
                                <div class="flex items-center flex-wrap gap-2 mt-1 text-[10px] text-slate-400">
                                    <span class="inline-flex items-center gap-1 font-medium bg-slate-800/80 px-1.5 py-0.5 rounded text-slate-300">
                                        <span>📦</span>
                                        <span>Vol: <strong class="text-white font-mono">{{ $row->jumlah_volume ?? ($astap?->jumlah_volume ?? 1) }} {{ $row->satuan ?? ($astap?->satuan ?? 'Bidang') }}</strong></span>
                                    </span>

                                    @if($luasObjek)
                                        <span class="inline-flex items-center gap-1 bg-slate-800/80 px-1.5 py-0.5 rounded text-cyan-300">
                                            <span>📐</span>
                                            <span>{{ $luasObjek }} m²</span>
                                        </span>
                                    @endif
                                    @if($sertifikatObjek)
                                        <span class="inline-flex items-center gap-1 bg-slate-800/80 px-1.5 py-0.5 rounded text-slate-300 truncate max-w-[200px]" title="{{ $sertifikatObjek }}">
                                            <span>📜</span>
                                            <span>{{ $sertifikatObjek }}</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 4. Identitas 108 Kerja Sama Pemanfaatan -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-bold text-cyan-300 leading-snug">
                                    {{ $namaAkun108 }}
                                </div>
                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                    <span class="font-mono text-[11px] font-bold text-cyan-400 bg-cyan-950/60 border border-cyan-500/40 px-2 py-0.5 rounded-lg shadow-sm">
                                        {{ $kodeAkun108 }}
                                    </span>
                                </div>
                                <div class="text-[9.5px] text-slate-400 mt-1 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                    <span>Akun 1.5.2 Kemitraan</span>
                                </div>
                            </td>

                            <!-- 4. Kondisi Aset -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @php
                                    $regCol = ($row->objekRegister ? collect([$row->objekRegister]) : ($astap?->registers ?? collect()))->where('is_deleted', 0);
                                    $totR = $regCol->count();
                                    if ($totR > 0) {
                                        $cB = $regCol->filter(fn($r) => in_array($r->kondisi, ['Baik', 'B']))->count();
                                        $cK = $regCol->filter(fn($r) => in_array($r->kondisi, ['Kurang Baik', 'KB', 'Rusak Ringan', 'RR']))->count();
                                        $cR = $regCol->filter(fn($r) => in_array($r->kondisi, ['Rusak Berat', 'RB', 'Rusak']))->count();
                                    } else {
                                        $kRaw = $spec['kondisi'] ?? ($astap?->kondisi_barang ?? 'Baik');
                                        $cB = in_array($kRaw, ['Baik', 'B']) ? 1 : 0;
                                        $cK = in_array($kRaw, ['Kurang Baik', 'KB', 'Rusak Ringan', 'RR']) ? 1 : 0;
                                        $cR = in_array($kRaw, ['Rusak Berat', 'RB', 'Rusak']) ? 1 : 0;
                                        $totR = 1;
                                    }
                                    $pB = round(($cB / $totR) * 100);
                                    $pK = round(($cK / $totR) * 100);
                                    $pR = round(($cR / $totR) * 100);
                                @endphp
                                @if($pB === 100)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                        100% Baik
                                    </span>
                                @elseif($pK === 100)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                                        100% Kurang Baik
                                    </span>
                                @elseif($pR === 100)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mr-1.5 animate-pulse"></span>
                                        100% Rusak Berat
                                    </span>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <div class="flex h-1.5 w-24 rounded-full overflow-hidden bg-slate-800 mb-1">
                                            @if($pB > 0)<div class="bg-emerald-400" style="width: {{ $pB }}%"></div>@endif
                                            @if($pK > 0)<div class="bg-amber-400" style="width: {{ $pK }}%"></div>@endif
                                            @if($pR > 0)<div class="bg-rose-400" style="width: {{ $pR }}%"></div>@endif
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[9.5px] font-bold">
                                            @if($pB > 0)<span class="text-emerald-400">{{ $pB }}% Baik</span>@endif
                                            @if($pK > 0)<span class="text-amber-400">{{ $pK }}% KB</span>@endif
                                            @if($pR > 0)<span class="text-rose-400">{{ $pR }}% RB</span>@endif
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- 5. Total Nilai Konsesi / Taksiran -->
                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                <span class="font-mono font-bold text-xs text-white block">
                                    Rp {{ number_format($row->nilai_aset, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    Tahun {{ $row->tahun }} · {{ $row->triwulan }}
                                </span>
                            </td>

                            <!-- 6. Masa Konsesi / Sewa -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        {{ $row->status_konsesi === 'Aktif' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Konsesi Berakhir' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : '' }}
                                        {{ in_array($row->status_konsesi, ['Selesai', 'Selesai / Reklasifikasi']) ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Dihentikan' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Siap Dikerjasamakan' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : '' }}
                                    ">
                                        {{ in_array($row->status_konsesi, ['Selesai', 'Selesai / Reklasifikasi']) ? 'Selesai' : ($row->status_konsesi === 'Konsesi Berakhir' ? 'Konsesi Berakhir' : $row->status_konsesi) }}
                                    </span>
                                    @if($sisaHari !== null && $row->status_konsesi === 'Aktif')
                                        <span class="text-[10px] font-mono {{ $sisaHari <= 30 ? 'text-amber-400 font-bold' : 'text-slate-400' }}">
                                            {{ $sisaHari > 0 ? $sisaHari . ' hari lagi' : 'Berakhir' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10.5px] font-mono text-slate-400">
                                    {{ $row->tanggal_mulai ? \Carbon\Carbon::parse($row->tanggal_mulai)->format('d/m/Y') : '?' }} s/d {{ $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->format('d/m/Y') : '?' }}
                                </div>
                            </td>

                            <!-- 7. Aksi (Sticky Right) -->
                            <td class="py-4 px-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 sticky right-0 z-10 bg-slate-900/95 group-hover:bg-[#072535] transition-colors">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- 1. Tombol Detail -->
                                    <button type="button" @click="openDetail({{ json_encode($row) }}, {{ json_encode($astap) }}, {{ json_encode($firstReg) }}, true)"
                                        title="Lihat Detail Lengkap PKS & Objek Aset"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-cyan-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span>Detail</span>
                                    </button>

                                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                    <!-- 2. Tombol Reklas -->
                                    @if($row->status_konsesi === 'Konsesi Berakhir')
                                    <button type="button" @click="openReklas({{ json_encode($astap) }}, {{ json_encode($row) }})"
                                        title="Masa konsesi berakhir! Reklasifikasi balik aset ini ke KIB Asal RSUD"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 border border-amber-400 font-extrabold text-xs transition-all duration-200 shadow-md shadow-amber-500/30 ring-1 ring-amber-400 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none animate-pulse">
                                        <svg class="w-3.5 h-3.5 text-amber-300 group-hover/btn:text-slate-950 group-hover/btn:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <span>Reklas</span>
                                    </button>
                                    @else
                                    <button type="button" @click="openReklas({{ json_encode($astap) }}, {{ json_encode($row) }})"
                                        title="Reklasifikasi Aset (Pindah KIB / Ekstrakom / Koreksi)"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-indigo-400 group-hover/btn:text-white group-hover/btn:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <span>Reklas</span>
                                    </button>
                                    @endif

                                    <!-- 3. Tombol Ubah -->
                                    <a href="{{ route('astap.edit_kemitraan', ['id' => $astap?->id]) }}"
                                        title="Ubah Data Aset Kemitraan"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-cyan-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/btn:text-white group-hover/btn:rotate-12 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Ubah</span>
                                    </a>

                                    <!-- 4. Tombol Hapus -->
                                    <button type="button" @click="confirmDelete({{ $row->id ?: ($astap?->kemitraan?->id ?: ($row->astap_id ?: $astap?->id)) }}, '{{ addslashes($astap?->nama_barang ?: 'Aset Kemitraan') }}')"
                                        title="Hapus / Batalkan Aset Kemitraan"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 hover:border-rose-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-rose-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-rose-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">🏛️</div>
                                <p class="text-sm font-bold text-white">Belum Ada Aset BMD RSUD yang Dimanfaatkan</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Belum ada data pemanfaatan aset daerah milik RSUD (Semua KIB: KIB A s.d. E) oleh pihak ketiga pada periode ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    @if(count($recordsDimanfaatkan) > 0)
                        <tr x-show="countVisibleDimanfaatkan === 0" x-cloak>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">🔍</div>
                                <p class="text-sm font-bold text-white">Tidak Ada Aset BMD RSUD yang Sesuai Filter</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Tidak ditemukan aset yang cocok dengan klasifikasi KIB atau kriteria filter saat ini.
                                </p>
                                <button type="button" @click="resetAllFilters()" class="mt-3 px-3.5 py-1.5 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-xs font-bold hover:bg-cyan-500 hover:text-slate-950 transition-all cursor-pointer">
                                    🔄 Reset Filter
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TABEL 2: ASET YANG DITAMBAHKAN / DIDATANGKAN OLEH MITRA                   -->
    <!-- ========================================================================= -->
    <div x-show="kemitraanTableTab === 'both' || kemitraanTableTab === 'ditambahkan'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="rounded-3xl bg-slate-900/90 border border-emerald-500/30 shadow-2xl overflow-hidden space-y-0">
        
        <!-- Table Header Banner -->
        <div class="p-5 border-b border-emerald-500/20 bg-gradient-to-r from-emerald-950/40 via-slate-900/90 to-slate-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-lg shrink-0 shadow-inner">
                    📦
                </div>
                <div>
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span>Daftar Aset yang Ditambahkan oleh Mitra (Sewa, KSP, BGS/BSG, KSPI)</span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Seluruh aset (peralatan, mesin, tanah, gedung, atau instalasi) yang diperoleh / ditambahkan melalui kerja sama dengan pihak ketiga.
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <span class="text-[11px] font-mono font-bold text-emerald-300 bg-emerald-500/10 px-3 py-1 rounded-xl border border-emerald-500/30">
                    <span x-text="countVisibleDitambahkan">{{ count($recordsDitambahkan) }}</span> Aset Ditambahkan Mitra
                </span>
            </div>
        </div>

        <div class="border-t border-slate-800/80 bg-slate-950/40 custom-scrollbar" style="max-height: 480px; overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                <thead class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800 shrink-0 sticky top-0 z-10 bg-slate-950">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center bg-slate-950 whitespace-nowrap">No</th>
                        <th class="py-3.5 px-4 min-w-[200px] bg-slate-950">Dokumen PKS &amp; Rekanan</th>
                        <th class="py-3.5 px-4 min-w-[240px] bg-slate-950">Nama &amp; Spesifikasi Barang</th>
                        <th class="py-3.5 px-4 min-w-[220px] bg-slate-950">Identitas 108 Aset Kemitraan</th>
                        <th class="py-3.5 px-4 min-w-[135px] text-center bg-slate-950 whitespace-nowrap">Kondisi</th>
                        <th class="py-3.5 px-4 min-w-[140px] text-right bg-slate-950 whitespace-nowrap">Taksiran Nilai (Rp)</th>
                        <th class="py-3.5 px-4 min-w-[180px] bg-slate-950">Masa Konsesi Operasional</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800/80 shrink-0 min-w-[260px] sticky right-0 z-10">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($recordsDitambahkan as $idx => $row)
                        @php
                            $astap = $row->astap;
                            $firstReg = $astap?->registers?->first();
                            $spec = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap?->spesifikasi_json ?? '[]', true) ?: []);
                            $sisaHari = $row->sisa_hari_konsesi;
                            $merk = $spec['merk'] ?? ($spec['mesin_merk'] ?? null);
                            $type = $spec['type'] ?? ($spec['mesin_type'] ?? null);
                            $targetPrintId = $row->id ?: ($row->astap_id ?: ($astap?->id ?: null));
                        @endphp
                        <tr x-show="matchKemitraan({{ json_encode($ditambahkanMetaList[$idx] ?? []) }})" class="hover:bg-emerald-950/20 transition-colors group">
                            <!-- 1. Nomor -->
                            <td class="py-4 px-4 text-center font-mono text-emerald-400 font-bold text-xs">
                                {{ $idx + 1 }}
                            </td>

                            <!-- 2. Dokumen PKS & Rekanan -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2 mb-1 flex-wrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                        {{ $row->skema_kemitraan ?: 'KSO' }}
                                    </span>
                                    @if($row->status_konsesi === 'Konsesi Berakhir')
                                        <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-500/25 text-emerald-300 border border-emerald-400/50 shadow-sm shadow-emerald-500/20 animate-pulse"
                                            title="Masa konsesi telah berakhir. Aset siap direklasifikasi masuk ke Aset Tetap RSUD.">
                                            <span>🔄 SIAP REKLAS KE ASET TETAP</span>
                                        </span>
                                    @endif
                                    @if(($astap?->is_reklas) || ($astap?->reklas && $astap->reklas->isNotEmpty()))
                                        <span class="inline-flex items-center space-x-1 px-1.5 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-indigo-500/25 text-indigo-300 border border-indigo-400/50 shadow-sm shadow-indigo-500/20"
                                            title="Aset ini memiliki riwayat Reklasifikasi">
                                            <svg class="w-2.5 h-2.5 shrink-0 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                            <span>REKLASIFIKASI</span>
                                        </span>
                                    @endif
                                    <span class="text-xs font-bold text-white truncate max-w-[180px]" title="{{ $row->mitra_nama }}">
                                        {{ $row->mitra_nama }}
                                    </span>
                                </div>
                                <div class="text-[11px] font-mono text-slate-400 truncate max-w-[200px]" title="{{ $row->nomor_pks }}">
                                    No: {{ $row->nomor_pks }}
                                </div>
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    Tgl PKS: {{ $row->tanggal_pks ? \Carbon\Carbon::parse($row->tanggal_pks)->translatedFormat('d F Y') : '-' }}
                                </div>
                            </td>

                            <!-- 3. Nama & Spesifikasi Barang -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors leading-snug">
                                    {{ $astap?->nama_barang ?: 'Barang KSO Rekanan' }}
                                </div>
                                <div class="flex items-center flex-wrap gap-2 mt-1 text-[10px] text-slate-400">
                                    <span class="inline-flex items-center gap-1 font-medium bg-slate-800/80 px-1.5 py-0.5 rounded text-slate-300">
                                        <span>📦</span>
                                        <span>Vol: <strong class="text-white font-mono">{{ $row->jumlah_volume }} {{ $row->satuan }}</strong></span>
                                    </span>
                                    @if($merk || $type)
                                        <span class="inline-flex items-center gap-1 bg-slate-800/80 px-1.5 py-0.5 rounded text-emerald-300 truncate max-w-[200px]" title="{{ trim(($merk ?? '') . ' ' . ($type ?? '')) }}">
                                            <span>🏷️</span>
                                            <span>{{ trim(($merk ?? '') . ' ' . ($type ?? '')) }}</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 4. Identitas 108 Aset Kemitraan -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-bold text-emerald-300 leading-snug">
                                    {{ $astap?->jenisAstap?->uraian_sub_sub_rincian ?: ($astap?->jenisAstap?->uraian_sub_rincian ?: 'Aset Kemitraan Pihak Ketiga') }}
                                </div>
                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                    <span class="font-mono text-[11px] font-bold text-emerald-400 bg-emerald-950/60 border border-emerald-500/40 px-2 py-0.5 rounded-lg shadow-sm">
                                        {{ $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '1.5.2.02.01.001') }}
                                    </span>
                                </div>
                                <div class="text-[9.5px] text-slate-400 mt-1 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                    <span>Akun 1.5.2 Kemitraan</span>
                                </div>
                            </td>

                            <!-- 4. Kondisi Aset -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @php
                                    $regCol = ($astap?->registers ?? collect())->where('is_deleted', 0);
                                    $totR = $regCol->count();
                                    if ($totR > 0) {
                                        $cB = $regCol->filter(fn($r) => in_array($r->kondisi, ['Baik', 'B']))->count();
                                        $cK = $regCol->filter(fn($r) => in_array($r->kondisi, ['Kurang Baik', 'KB', 'Rusak Ringan', 'RR']))->count();
                                        $cR = $regCol->filter(fn($r) => in_array($r->kondisi, ['Rusak Berat', 'RB', 'Rusak']))->count();
                                    } else {
                                        $kRaw = $spec['kondisi'] ?? ($astap?->kondisi_barang ?? 'Baik');
                                        $cB = in_array($kRaw, ['Baik', 'B']) ? 1 : 0;
                                        $cK = in_array($kRaw, ['Kurang Baik', 'KB', 'Rusak Ringan', 'RR']) ? 1 : 0;
                                        $cR = in_array($kRaw, ['Rusak Berat', 'RB', 'Rusak']) ? 1 : 0;
                                        $totR = 1;
                                    }
                                    $pB = round(($cB / $totR) * 100);
                                    $pK = round(($cK / $totR) * 100);
                                    $pR = round(($cR / $totR) * 100);
                                @endphp
                                @if($pB === 100)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                        100% Baik
                                    </span>
                                @elseif($pK === 100)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5 animate-pulse"></span>
                                        100% Kurang Baik
                                    </span>
                                @elseif($pR === 100)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mr-1.5 animate-pulse"></span>
                                        100% Rusak Berat
                                    </span>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <div class="flex h-1.5 w-24 rounded-full overflow-hidden bg-slate-800 mb-1">
                                            @if($pB > 0)<div class="bg-emerald-400" style="width: {{ $pB }}%"></div>@endif
                                            @if($pK > 0)<div class="bg-amber-400" style="width: {{ $pK }}%"></div>@endif
                                            @if($pR > 0)<div class="bg-rose-400" style="width: {{ $pR }}%"></div>@endif
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[9.5px] font-bold">
                                            @if($pB > 0)<span class="text-emerald-400">{{ $pB }}% Baik</span>@endif
                                            @if($pK > 0)<span class="text-amber-400">{{ $pK }}% KB</span>@endif
                                            @if($pR > 0)<span class="text-rose-400">{{ $pR }}% RB</span>@endif
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- 5. Taksiran Nilai Aset -->
                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                <span class="font-mono font-bold text-xs text-white block">
                                    Rp {{ number_format($row->nilai_aset, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    Tahun {{ $row->tahun }} · {{ $row->triwulan }}
                                </span>
                            </td>

                            <!-- 6. Masa Konsesi Operasional -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-1.5 mb-1 flex-wrap">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        {{ $row->status_konsesi === 'Aktif' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Konsesi Berakhir' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : '' }}
                                        {{ in_array($row->status_konsesi, ['Selesai', 'Selesai / Reklasifikasi']) ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Dihentikan' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : '' }}
                                    ">
                                        {{ in_array($row->status_konsesi, ['Selesai', 'Selesai / Reklasifikasi']) ? 'Selesai' : ($row->status_konsesi === 'Konsesi Berakhir' ? 'Konsesi Berakhir' : $row->status_konsesi) }}
                                    </span>
                                    @if($sisaHari !== null && $row->status_konsesi === 'Aktif')
                                        <span class="text-[10px] font-mono {{ $sisaHari <= 30 ? 'text-amber-400 font-bold' : 'text-slate-400' }}">
                                            {{ $sisaHari > 0 ? $sisaHari . ' hari lagi' : 'Berakhir' }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-[10.5px] font-mono text-slate-400">
                                    {{ $row->tanggal_mulai ? \Carbon\Carbon::parse($row->tanggal_mulai)->format('d/m/Y') : '?' }} s/d {{ $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->format('d/m/Y') : '?' }}
                                </div>
                            </td>

                            <!-- 7. Aksi (Sticky Right) -->
                            <td class="py-4 px-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 sticky right-0 z-10 bg-slate-900/95 group-hover:bg-[#062922] transition-colors">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <!-- 1. Tombol Detail -->
                                    <button type="button" @click="openDetail({{ json_encode($row) }}, {{ json_encode($astap) }}, {{ json_encode($firstReg) }}, false)"
                                        title="Lihat Detail Lengkap PKS & Aset Kemitraan"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-cyan-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                        <span>Detail</span>
                                    </button>

                                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                    <!-- 2. Tombol Reklas -->
                                    @if($row->status_konsesi === 'Konsesi Berakhir')
                                    <button type="button" @click="openReklas({{ json_encode($astap) }}, {{ json_encode($row) }})"
                                        title="Masa konsesi berakhir! Reklasifikasi aset ini menjadi Aset Tetap RSUD"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500 text-emerald-300 hover:text-slate-950 border border-emerald-400 font-extrabold text-xs transition-all duration-200 shadow-md shadow-emerald-500/30 ring-1 ring-emerald-400 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none animate-pulse">
                                        <svg class="w-3.5 h-3.5 text-emerald-300 group-hover/btn:text-slate-950 group-hover/btn:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <span>Reklas</span>
                                    </button>
                                    @else
                                    <button type="button" @click="openReklas({{ json_encode($astap) }}, {{ json_encode($row) }})"
                                        title="Reklasifikasi Aset (Pindah KIB / Ekstrakom / Koreksi)"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-indigo-400 group-hover/btn:text-white group-hover/btn:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <span>Reklas</span>
                                    </button>
                                    @endif

                                    <!-- 3. Tombol Ubah -->
                                    <a href="{{ route('astap.edit_kemitraan', ['id' => $astap?->id]) }}"
                                        title="Ubah Data ASTAP Kemitraan"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-cyan-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/btn:text-white group-hover/btn:rotate-12 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        <span>Ubah</span>
                                    </a>

                                    <!-- 4. Tombol Hapus -->
                                    <button type="button" @click="confirmDelete({{ $row->id }}, '{{ addslashes($astap?->nama_barang ?: 'Aset Kemitraan') }}')"
                                        title="Hapus / Batalkan Aset Kemitraan"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 hover:border-rose-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-rose-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-rose-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        <span>Hapus</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">📦</div>
                                <p class="text-sm font-bold text-white">Belum Ada Aset yang Ditambahkan oleh Mitra</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Belum ada aset (peralatan, mesin, tanah, atau gedung) yang ditambahkan dari kerja sama pihak ketiga pada periode ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                    @if(count($recordsDitambahkan) > 0)
                        <tr x-show="countVisibleDitambahkan === 0" x-cloak>
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">🔍</div>
                                <p class="text-sm font-bold text-white">Tidak Ada Aset Ditambahkan Mitra yang Sesuai Filter</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Tidak ditemukan aset yang cocok dengan klasifikasi KIB atau kriteria filter saat ini.
                                </p>
                                <button type="button" @click="resetAllFilters()" class="mt-3 px-3.5 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-xs font-bold hover:bg-emerald-500 hover:text-slate-950 transition-all cursor-pointer">
                                    🔄 Reset Filter
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>


    <!-- ========================================================================= -->
    <!-- TABEL 3: SEMUA DATA GABUNGAN (JIKA MODE TAB 'ALL')                        -->
    <!-- ========================================================================= -->
    <div x-show="kemitraanTableTab === 'all'"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="rounded-3xl bg-slate-900/90 border border-slate-800 shadow-2xl overflow-hidden space-y-0">
        
        <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                    <span>📋 Seluruh Daftar Aset Kemitraan (Gabungan Akun 1.5.2)</span>
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Menampilkan total <span x-text="countVisibleAll">{{ count($kemitraanRecords ?? []) }}</span> data aset kerja sama baik pemanfaatan BMD RSUD maupun pengadaan KSO mitra.
                </p>
            </div>

            <span class="text-[11px] font-mono font-bold text-cyan-400 bg-cyan-500/10 px-3 py-1 rounded-xl border border-cyan-500/30">
                Akun 1.5.2 Aset Kemitraan
            </span>
        </div>

        <div class="border-t border-slate-800/80 bg-slate-950/40 custom-scrollbar" style="max-height: 480px; overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                <thead class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800 shrink-0 sticky top-0 z-10 bg-slate-950">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center bg-slate-950 whitespace-nowrap">No</th>
                        <th class="py-3.5 px-4 min-w-[130px] bg-slate-950">Kategori Kemitraan</th>
                        <th class="py-3.5 px-4 min-w-[190px] bg-slate-950">Dokumen PKS &amp; Rekanan</th>
                        <th class="py-3.5 px-4 min-w-[220px] bg-slate-950">Nama &amp; Spesifikasi Barang</th>
                        <th class="py-3.5 px-4 min-w-[210px] bg-slate-950">Identitas 108 Kemitraan</th>
                        <th class="py-3.5 px-4 min-w-[125px] text-center bg-slate-950 whitespace-nowrap">Kondisi</th>
                        <th class="py-3.5 px-4 min-w-[130px] text-right bg-slate-950 whitespace-nowrap">Total Nilai (Rp)</th>
                        <th class="py-3.5 px-4 min-w-[170px] bg-slate-950">Masa Konsesi</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800/80 shrink-0 min-w-[260px] sticky right-0 z-10">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($kemitraanRecords ?? [] as $idx => $row)
                        @php
                            $astap = $row->astap;
                            $firstReg = $astap?->registers?->first();
                            $sisaHari = $row->sisa_hari_konsesi;
                            $isRowDimanfaatkan = $isDimanfaatkan($row);
                        @endphp
                        <tr x-show="matchKemitraan({{ json_encode($allMetaList[$idx] ?? []) }})" class="hover:bg-slate-800/40 transition-colors group">
                            <!-- 1. Nomor -->
                            <td class="py-4 px-4 text-center font-mono text-slate-500 text-xs">
                                {{ $idx + 1 }}
                            </td>

                            <!-- 2. Kategori Kemitraan Badge -->
                            <td class="py-4 px-4">
                                @if($isRowDimanfaatkan)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30">
                                        <span>🏛️</span>
                                        <span>Aset RSUD Dimanfaatkan</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-extrabold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                        <span>📦</span>
                                        <span>Aset Ditambahkan Mitra</span>
                                    </span>
                                @endif
                            </td>

                            <!-- 3. Dokumen PKS & Rekanan -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                        {{ $row->skema_kemitraan === 'KSO' || $row->skema_kemitraan === 'KSPI' ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : '' }}
                                        {{ $row->skema_kemitraan === 'BGS' || $row->skema_kemitraan === 'BSG' || $row->skema_kemitraan === 'BGS/BSG' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                        {{ $row->skema_kemitraan === 'Sewa' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : '' }}
                                        {{ $row->skema_kemitraan === 'KSP' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : '' }}
                                    ">
                                        {{ $row->skema_kemitraan ?: 'Sewa' }}
                                    </span>
                                    <span class="text-xs font-bold text-white truncate max-w-[150px]" title="{{ $row->mitra_nama }}">
                                        {{ $row->mitra_nama }}
                                    </span>
                                </div>
                                <div class="text-[11px] font-mono text-slate-400 truncate max-w-[180px]" title="{{ $row->nomor_pks }}">
                                    No: {{ $row->nomor_pks }}
                                </div>
                            </td>

                            <!-- 4. Nama & Spesifikasi Barang -->
                            <td class="py-4 px-4">
                                @php
                                    $t3Reklas = $astap?->reklas?->sortByDesc('id')->first();
                                    $t3NamaFisik = null;
                                    if (!empty($astap->nama_barang) && !str_starts_with(strtolower($astap->nama_barang), 'kerja sama pemanfaatan') && !str_starts_with(strtolower($astap->nama_barang), 'bangun guna serah')) {
                                        $t3NamaFisik = $astap->nama_barang;
                                    } elseif ($t3Reklas && !empty($t3Reklas->asal_nama)) {
                                        $t3NamaFisik = $t3Reklas->asal_nama;
                                    } else {
                                        $t3NamaFisik = $astap?->nama_barang ?: 'Barang Aset Kemitraan';
                                    }
                                @endphp
                                <div class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors leading-snug">
                                    {{ $t3NamaFisik }}
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400">
                                    <span>Vol: <strong class="text-slate-200">{{ $row->jumlah_volume }} {{ $row->satuan }}</strong></span>
                                </div>
                            </td>

                            <!-- 5. Identitas 108 Kemitraan -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-bold text-cyan-300 leading-snug">
                                    {{ $t3Reklas?->tujuan_nama ?: ($astap?->jenisAstap?->uraian_sub_sub_rincian ?: ($astap?->jenisAstap?->uraian_sub_rincian ?: ($astap?->jenisAstap?->nama_jenis ?: 'Aset Kemitraan (1.5.2)'))) }}
                                </div>
                                <div class="mt-1 flex items-center gap-1.5 flex-wrap">
                                    <span class="font-mono text-[11px] font-bold text-cyan-400 bg-cyan-950/60 border border-cyan-500/40 px-2 py-0.5 rounded-lg shadow-sm">
                                        {{ $t3Reklas?->tujuan_kode ?: ($astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '1.5.2.x')) }}
                                    </span>
                                </div>
                                <div class="text-[9.5px] text-slate-400 mt-1 flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                    <span>Akun 1.5.2 Kemitraan</span>
                                </div>
                            </td>

                            <!-- 5. Kondisi -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                @php
                                    $regCol = ($row->objekRegister ? collect([$row->objekRegister]) : ($astap?->registers ?? collect()))->where('is_deleted', 0);
                                    $totR = $regCol->count();
                                    if ($totR > 0) {
                                        $cB = $regCol->filter(fn($r) => in_array($r->kondisi, ['Baik', 'B']))->count();
                                        $cK = $regCol->filter(fn($r) => in_array($r->kondisi, ['Kurang Baik', 'KB', 'Rusak Ringan', 'RR']))->count();
                                        $cR = $regCol->filter(fn($r) => in_array($r->kondisi, ['Rusak Berat', 'RB', 'Rusak']))->count();
                                    } else {
                                        $kRaw = $spec['kondisi'] ?? ($astap?->kondisi_barang ?? 'Baik');
                                        $cB = in_array($kRaw, ['Baik', 'B']) ? 1 : 0;
                                        $cK = in_array($kRaw, ['Kurang Baik', 'KB', 'Rusak Ringan', 'RR']) ? 1 : 0;
                                        $cR = in_array($kRaw, ['Rusak Berat', 'RB', 'Rusak']) ? 1 : 0;
                                        $totR = 1;
                                    }
                                    $pB = round(($cB / $totR) * 100);
                                    $pK = round(($cK / $totR) * 100);
                                    $pR = round(($cR / $totR) * 100);
                                @endphp
                                @if($pB === 100)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1 animate-pulse"></span>
                                        100% Baik
                                    </span>
                                @elseif($pK === 100)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1 animate-pulse"></span>
                                        100% Kurang Baik
                                    </span>
                                @elseif($pR === 100)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-300 border border-rose-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mr-1 animate-pulse"></span>
                                        100% Rusak Berat
                                    </span>
                                @else
                                    <div class="inline-flex flex-col items-center">
                                        <div class="flex h-1.5 w-20 rounded-full overflow-hidden bg-slate-800 mb-1">
                                            @if($pB > 0)<div class="bg-emerald-400" style="width: {{ $pB }}%"></div>@endif
                                            @if($pK > 0)<div class="bg-amber-400" style="width: {{ $pK }}%"></div>@endif
                                            @if($pR > 0)<div class="bg-rose-400" style="width: {{ $pR }}%"></div>@endif
                                        </div>
                                        <div class="flex items-center gap-1.5 text-[9px] font-bold">
                                            @if($pB > 0)<span class="text-emerald-400">{{ $pB }}% Baik</span>@endif
                                            @if($pK > 0)<span class="text-amber-400">{{ $pK }}% KB</span>@endif
                                            @if($pR > 0)<span class="text-rose-400">{{ $pR }}% RB</span>@endif
                                        </div>
                                    </div>
                                @endif
                            </td>

                            <!-- 6. Total Nilai -->
                            <td class="py-4 px-4 text-right whitespace-nowrap">
                                <span class="font-mono font-bold text-xs text-white block">
                                    Rp {{ number_format($row->nilai_aset, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    {{ $row->tahun }} · {{ $row->triwulan }}
                                </span>
                            </td>

                            <!-- 7. Masa Konsesi -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-1.5 mb-0.5 flex-wrap">
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold
                                        {{ $row->status_konsesi === 'Aktif' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Konsesi Berakhir' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : '' }}
                                        {{ in_array($row->status_konsesi, ['Selesai', 'Selesai / Reklasifikasi']) ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Dihentikan' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : '' }}
                                    ">
                                        {{ in_array($row->status_konsesi, ['Selesai', 'Selesai / Reklasifikasi']) ? 'Selesai' : ($row->status_konsesi === 'Konsesi Berakhir' ? 'Konsesi Berakhir' : $row->status_konsesi) }}
                                    </span>
                                </div>
                                <div class="text-[10px] font-mono text-slate-400">
                                    {{ $row->tanggal_mulai ? \Carbon\Carbon::parse($row->tanggal_mulai)->format('d/m/y') : '?' }} - {{ $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->format('d/m/y') : '?' }}
                                </div>
                            </td>

                            <!-- 8. Aksi (Sticky Right) -->
                            <td class="py-4 px-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 sticky right-0 z-10 bg-slate-900/95 group-hover:bg-slate-800 transition-colors">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button type="button" @click="openDetail({{ json_encode($row) }}, {{ json_encode($astap) }}, {{ json_encode($firstReg) }}, false)"
                                        class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 font-bold text-xs transition-all">
                                        Detail
                                    </button>
                                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                    @if($row->status_konsesi === 'Konsesi Berakhir')
                                    <button type="button" @click="openReklas({{ json_encode($astap) }}, {{ json_encode($row) }})"
                                        title="Masa konsesi berakhir! Reklasifikasi aset ini"
                                        class="px-2.5 py-1.5 rounded-xl bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 border border-amber-400 font-extrabold text-xs transition-all ring-1 ring-amber-400 animate-pulse">
                                        Reklas
                                    </button>
                                    @else
                                    <button type="button" @click="openReklas({{ json_encode($astap) }}, {{ json_encode($row) }})"
                                        class="px-2.5 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 font-bold text-xs transition-all">
                                        Reklas
                                    </button>
                                    @endif
                                    <a href="{{ route('astap.edit_kemitraan', ['id' => $astap?->id]) }}"
                                        class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 font-bold text-xs transition-all">
                                        Ubah
                                    </a>
                                    <button type="button" @click="confirmDelete({{ $row->id }}, '{{ addslashes($astap?->nama_barang ?: 'Aset Kemitraan') }}')"
                                        class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 font-bold text-xs transition-all">
                                        Hapus
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-bold text-white">Belum Ada Aset Kemitraan Tercatat</p>
                            </td>
                        </tr>
                    @endforelse
                    @if(count($kemitraanRecords ?? []) > 0)
                        <tr x-show="countVisibleAll === 0" x-cloak>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">🔍</div>
                                <p class="text-sm font-bold text-white">Tidak Ada Data Kemitraan yang Sesuai Filter</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Tidak ditemukan aset yang cocok dengan klasifikasi KIB atau kriteria filter saat ini.
                                </p>
                                <button type="button" @click="resetAllFilters()" class="mt-3 px-3.5 py-1.5 rounded-xl bg-slate-800 text-slate-300 border border-slate-700 text-xs font-bold hover:bg-slate-700 hover:text-white transition-all cursor-pointer">
                                    🔄 Reset Filter
                                </button>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>
