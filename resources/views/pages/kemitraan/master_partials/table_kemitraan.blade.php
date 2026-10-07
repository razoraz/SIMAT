<!-- ========================================================================= -->
<!-- TABEL DATA MASTER ASET KEMITRAAN PIHAK KETIGA (AKUN 1.5.2)                -->
<!-- DIPISAH: 1. Aset RSUD Dimanfaatkan Mitra | 2. Aset Ditambahkan Mitra       -->
<!-- ========================================================================= -->

@php
    $isDimanfaatkan = function($row) {
        $astap = $row->astap;
        
        // 1. Aset RSUD yang Dimanfaatkan Mitra: HANYA jika secara eksplisit menautkan objek aset BMD milik RSUD
        if (!empty($row->objek_nibar) || !empty($row->objek_register_id) || !empty($row->objek_astap_id)) {
            return true;
        }
        $spec = is_array($astap?->spesifikasi_json) ? $astap->spesifikasi_json : (json_decode($astap?->spesifikasi_json ?? '[]', true) ?: []);
        if (!empty($spec['objek_nibar']) || !empty($spec['objek_register_id']) || !empty($spec['objek_astap_id'])) {
            return true;
        }

        // 2. ATAU jika secara eksplisit menautkan objek aset BMD
        return false;
    };

    $recordsDimanfaatkan = collect($kemitraanRecords ?? [])->filter(fn($r) => $isDimanfaatkan($r))->values();
    $recordsDitambahkan  = collect($kemitraanRecords ?? [])->filter(fn($r) => !$isDimanfaatkan($r))->values();

    // Integrasikan Aset BMD RSUD yang merupakan hasil reklasifikasi ke Kemitraan (1.5.2) ke Tabel Atas
    $seenReklasAstapIds = [];
    foreach ($reklasKemitraanRecords ?? [] as $reklasItem) {
        if (!in_array($reklasItem->astap_id, $seenReklasAstapIds)) {
            $seenReklasAstapIds[] = $reklasItem->astap_id; // Kunci agar unik dan tidak dobel/kembar
            $rAstap = $reklasItem->astap;
            if ($rAstap) {
                $rReg = $rAstap->registers->first();
                $rSpec = is_array($rAstap->spesifikasi_json) ? $rAstap->spesifikasi_json : (json_decode($rAstap->spesifikasi_json ?? '[]', true) ?: []);
                
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
                ]);
            }
        }
    }
@endphp

<div class="space-y-6">

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
                        :class="kemitraanTableTab === 'dimanfaatkan' ? 'bg-cyan-400 text-slate-950' : 'bg-slate-800 text-slate-300'">
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
                        :class="kemitraanTableTab === 'ditambahkan' ? 'bg-emerald-400 text-slate-950' : 'bg-slate-800 text-slate-300'">
                        {{ count($recordsDitambahkan) }}
                    </span>
                </button>

                <!-- 4. Tab: Semua Data Gabungan -->
                <button type="button" @click="kemitraanTableTab = 'all'"
                    :class="kemitraanTableTab === 'all' ? 'bg-slate-800 text-white border-slate-700 font-extrabold' : 'text-slate-500 hover:text-slate-300 border-transparent'"
                    class="px-2.5 py-1.5 rounded-xl text-xs border transition-all flex items-center gap-1 shrink-0 cursor-pointer">
                    <span>📋</span>
                    <span>Semua ({{ count($kemitraanRecords ?? []) }})</span>
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
            <span class="font-mono text-cyan-400/80 text-[10px] hidden sm:inline-block">Total {{ count($kemitraanRecords ?? []) }} Data Kemitraan</span>
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
                    {{ count($recordsDimanfaatkan) }} Aset BMD Dimanfaatkan
                </span>
            </div>
        </div>

        <div class="border-t border-slate-800/80 bg-slate-950/40 custom-scrollbar" style="max-height: 480px; overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                <thead class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center bg-slate-950 whitespace-nowrap">No</th>
                        <th class="py-3.5 px-4 min-w-[200px] bg-slate-950">Dokumen PKS &amp; Rekanan</th>
                        <th class="py-3.5 px-4 min-w-[240px] bg-slate-950">Identitas Barang &amp; Spesifikasi (Akun 108)</th>
                        <th class="py-3.5 px-4 min-w-[135px] text-center bg-slate-950 whitespace-nowrap">Kondisi</th>
                        <th class="py-3.5 px-4 min-w-[140px] text-right bg-slate-950 whitespace-nowrap">Nilai Pemanfaatan (Rp)</th>
                        <th class="py-3.5 px-4 min-w-[180px] bg-slate-950">Masa Pemanfaatan / Konsesi</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0 min-w-[340px] w-[340px]" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
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
                            $namaObjekBmd = $objekAsetBmd?->nama_barang ?: ($astap?->nama_barang ?: 'Objek Aset BMD RSUD');
                            $luasObjek = $spec['luas_m2'] ?? ($spec['tanah_luas_m2'] ?? ($spec['gedung_luas_lantai'] ?? null));
                            $sertifikatObjek = $spec['sertifikat_no'] ?? ($spec['tanah_sertifikat_no'] ?? ($spec['gedung_dokumen_no'] ?? null));
                            $targetPrintId = $row->id ?: ($row->astap_id ?: ($astap?->id ?: null));
                        @endphp
                        <tr class="hover:bg-cyan-950/20 transition-colors group">
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

                            <!-- 3. Identitas Barang & Spesifikasi (Akun 108) -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors leading-snug">
                                    {{ $namaObjekBmd }}
                                </div>
                                <div class="text-[11px] font-mono text-cyan-400 mt-0.5">
                                    {{ $objekAsetBmd?->kode_108 ?: ($astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '1.5.2.01.01.001')) }}
                                </div>
                                <div class="flex items-center flex-wrap gap-2 mt-1 text-[10px] text-slate-400">
                                    <span>Vol: <strong class="text-slate-200">{{ $row->jumlah_volume ?? ($astap?->jumlah_volume ?? 1) }} {{ $row->satuan ?? ($astap?->satuan ?? 'Bidang') }}</strong></span>

                                    @if($luasObjek)
                                        <span>· 📐 {{ $luasObjek }} m²</span>
                                    @endif
                                    @if($sertifikatObjek)
                                        <span>· 📜 {{ $sertifikatObjek }}</span>
                                    @endif
                                </div>
                            </td>

                            <!-- 4. Kondisi Aset -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                    100% Baik
                                </span>
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
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        {{ $row->status_konsesi === 'Aktif' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Akan Berakhir' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Konsesi Berakhir' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Selesai / Reklasifikasi' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                    ">
                                        {{ $row->status_konsesi }}
                                    </span>
                                    @if($sisaHari !== null)
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
                            <td class="py-4 px-4 text-center whitespace-nowrap border-l border-slate-800 shrink-0" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
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
                                    <button type="button" @click="openReklas({{ json_encode($astap) }}, {{ json_encode($row) }})"
                                        title="Reklasifikasi Aset (Pindah KIB / Ekstrakom / Koreksi)"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-indigo-400 group-hover/btn:text-white group-hover/btn:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <span>Reklas</span>
                                    </button>

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
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">🏛️</div>
                                <p class="text-sm font-bold text-white">Belum Ada Aset BMD RSUD yang Dimanfaatkan</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Belum ada data pemanfaatan aset daerah milik RSUD (Semua KIB: KIB A s.d. E) oleh pihak ketiga pada periode ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
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
                    {{ count($recordsDitambahkan) }} Aset Ditambahkan Mitra
                </span>
            </div>
        </div>

        <div class="border-t border-slate-800/80 bg-slate-950/40 custom-scrollbar" style="max-height: 480px; overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                <thead class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center bg-slate-950 whitespace-nowrap">No</th>
                        <th class="py-3.5 px-4 min-w-[200px] bg-slate-950">Dokumen PKS &amp; Rekanan</th>
                        <th class="py-3.5 px-4 min-w-[240px] bg-slate-950">Identitas Barang &amp; Spesifikasi (Akun 108)</th>
                        <th class="py-3.5 px-4 min-w-[135px] text-center bg-slate-950 whitespace-nowrap">Kondisi</th>
                        <th class="py-3.5 px-4 min-w-[140px] text-right bg-slate-950 whitespace-nowrap">Taksiran Nilai (Rp)</th>
                        <th class="py-3.5 px-4 min-w-[180px] bg-slate-950">Masa Konsesi Operasional</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0 min-w-[340px] w-[340px]" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
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
                        <tr class="hover:bg-emerald-950/20 transition-colors group">
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

                            <!-- 3. Identitas Barang KSO & Spesifikasi -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors leading-snug">
                                    {{ $astap?->nama_barang ?: 'Barang KSO Rekanan' }}
                                </div>
                                <div class="text-[11px] font-mono text-emerald-400 mt-0.5">
                                    {{ $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '1.5.2.01.01.002') }}
                                </div>
                                <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400">
                                    <span>Vol: <strong class="text-slate-200">{{ $row->jumlah_volume }} {{ $row->satuan }}</strong></span>
                                </div>
                            </td>

                            <!-- 4. Kondisi Aset -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                    100% Baik
                                </span>
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
                                <div class="flex items-center gap-1.5 mb-1">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                        {{ $row->status_konsesi === 'Aktif' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Akan Berakhir' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Konsesi Berakhir' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Selesai / Reklasifikasi' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                    ">
                                        {{ $row->status_konsesi }}
                                    </span>
                                    @if($sisaHari !== null)
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
                            <td class="py-4 px-4 text-center whitespace-nowrap border-l border-slate-800 shrink-0" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
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
                                    <button type="button" @click="openReklas({{ json_encode($astap) }}, {{ json_encode($row) }})"
                                        title="Reklasifikasi Aset (Pindah KIB / Ekstrakom / Koreksi)"
                                        class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                        <svg class="w-3.5 h-3.5 text-indigo-400 group-hover/btn:text-white group-hover/btn:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                        </svg>
                                        <span>Reklas</span>
                                    </button>

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
                            <td colspan="7" class="py-12 text-center text-slate-400">
                                <div class="text-3xl mb-2">📦</div>
                                <p class="text-sm font-bold text-white">Belum Ada Aset yang Ditambahkan oleh Mitra</p>
                                <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                    Belum ada aset (peralatan, mesin, tanah, atau gedung) yang ditambahkan dari kerja sama pihak ketiga pada periode ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
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
                    Menampilkan total {{ count($kemitraanRecords ?? []) }} data aset kerja sama baik pemanfaatan BMD RSUD maupun pengadaan KSO mitra.
                </p>
            </div>

            <span class="text-[11px] font-mono font-bold text-cyan-400 bg-cyan-500/10 px-3 py-1 rounded-xl border border-cyan-500/30">
                Akun 1.5.2 Aset Kemitraan
            </span>
        </div>

        <div class="border-t border-slate-800/80 bg-slate-950/40 custom-scrollbar" style="max-height: 480px; overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                <thead class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                    <tr>
                        <th class="py-3.5 px-4 w-12 text-center bg-slate-950 whitespace-nowrap">No</th>
                        <th class="py-3.5 px-4 min-w-[130px] bg-slate-950">Kategori Kemitraan</th>
                        <th class="py-3.5 px-4 min-w-[190px] bg-slate-950">Dokumen PKS &amp; Rekanan</th>
                        <th class="py-3.5 px-4 min-w-[210px] bg-slate-950">Identitas Barang (Akun 108)</th>
                        <th class="py-3.5 px-4 min-w-[125px] text-center bg-slate-950 whitespace-nowrap">Kondisi</th>
                        <th class="py-3.5 px-4 min-w-[130px] text-right bg-slate-950 whitespace-nowrap">Total Nilai (Rp)</th>
                        <th class="py-3.5 px-4 min-w-[170px] bg-slate-950">Masa Konsesi</th>
                        <th class="py-3.5 px-4 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0 min-w-[340px] w-[340px]" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
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
                        <tr class="hover:bg-slate-800/40 transition-colors group">
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

                            <!-- 4. Identitas Barang (Akun 108) -->
                            <td class="py-4 px-4">
                                <div class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors leading-snug">
                                    {{ $astap?->nama_barang ?: 'Barang Aset Kemitraan' }}
                                </div>
                                <div class="text-[11px] font-mono text-cyan-400 mt-0.5">
                                    {{ $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '1.5.2.x') }}
                                </div>
                                <div class="flex items-center gap-2 mt-0.5 text-[10px] text-slate-400">
                                    <span>Vol: <strong class="text-slate-200">{{ $row->jumlah_volume }} {{ $row->satuan }}</strong></span>
                                </div>
                            </td>

                            <!-- 5. Kondisi -->
                            <td class="py-4 px-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1 animate-pulse"></span>
                                    100% Baik
                                </span>
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
                                <div class="flex items-center gap-1.5 mb-0.5">
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold
                                        {{ $row->status_konsesi === 'Aktif' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Akan Berakhir' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Konsesi Berakhir' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : '' }}
                                        {{ $row->status_konsesi === 'Selesai / Reklasifikasi' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                    ">
                                        {{ $row->status_konsesi }}
                                    </span>
                                </div>
                                <div class="text-[10px] font-mono text-slate-400">
                                    {{ $row->tanggal_mulai ? \Carbon\Carbon::parse($row->tanggal_mulai)->format('d/m/y') : '?' }} - {{ $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->format('d/m/y') : '?' }}
                                </div>
                            </td>

                            <!-- 8. Aksi (Sticky Right) -->
                            <td class="py-4 px-4 text-center whitespace-nowrap border-l border-slate-800 shrink-0" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                <div class="flex items-center justify-center space-x-1.5">
                                    <button type="button" @click="openDetail({{ json_encode($row) }}, {{ json_encode($astap) }}, {{ json_encode($firstReg) }}, false)"
                                        class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 font-bold text-xs transition-all">
                                        Detail
                                    </button>
                                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                    <button type="button" @click="openReklas({{ json_encode($astap) }}, {{ json_encode($row) }})"
                                        class="px-2.5 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 font-bold text-xs transition-all">
                                        Reklas
                                    </button>
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
                            <td colspan="8" class="py-12 text-center text-slate-400">
                                <p class="text-sm font-bold text-white">Belum Ada Aset Kemitraan Tercatat</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
