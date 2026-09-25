<!-- TABEL LOG TRANSAKSI REKLASIFIKASI (AUDIT TRAIL & PERBANDINGAN SPESIFIKASI) -->
<div class="bg-slate-900/90 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-xl">
    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-950/40">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>📋</span> Riwayat Audit &amp; Transaksi Mutasi Reklasifikasi
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
                Jejak audit pemindahan bukuan, perubahan klasifikasi, dan snapshot komparasi spesifikasi fisik
            </p>
        </div>
        <span id="total-reklas-count" class="text-xs font-semibold text-slate-400 bg-slate-800 px-3 py-1 rounded-lg border border-slate-700">
            Total {{ count($logReklas) }} Transaksi
        </span>
    </div>

    @if (count($logReklas) === 0)
        <div class="p-12 text-center">
            <div class="w-16 h-16 rounded-full bg-slate-800/80 border border-slate-700 flex items-center justify-center mx-auto text-slate-500 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
            </div>
            <h4 class="text-base font-bold text-white">Belum Ada Transaksi Reklasifikasi</h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                Belum ada aset yang dimutasi antar rekening atau KIB pada periode tahun {{ $selectedTahun }}.
            </p>
            <button type="button" @click="openModalTambah()"
                class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all cursor-pointer">
                <span>+ Catat Reklasifikasi Baru</span>
            </button>
        </div>
    @else
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-slate-700">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-800/90 text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700 text-[11px]">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 min-w-[130px]">Tanggal &amp; Periode</th>
                        <th class="py-3 px-4 min-w-[230px]">Nama Barang &amp; No. BA</th>
                        <th class="py-3 px-4 min-w-[170px]">Jenis Reklasifikasi</th>
                        <th class="py-3 px-4 min-w-[220px]">Mutasi Rekening / KIB</th>
                        <th class="py-3 px-4 min-w-[150px] text-right">Nilai Reklas</th>
                        <th class="py-3 px-4 w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @foreach ($logReklas as $idx => $item)
                        <tr id="row-reklas-{{ $item->id }}" class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3.5 px-4 text-center text-slate-400 font-mono text-[11px]">
                                {{ $idx + 1 }}
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white">
                                    {{ date('d/m/Y', strtotime($item->tanggal_reklas)) }}
                                </div>
                                <div class="text-[11px] text-indigo-400 font-medium mt-0.5">
                                    Triwulan {{ $item->triwulan }} · {{ $item->tahun }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="font-bold text-white text-sm">
                                    {{ $item->astap->nama_barang ?? 'Aset #' . $item->astap_id }}
                                </div>
                                <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                    @if ($item->nomor_ba_reklas)
                                        <span class="text-[10px] text-slate-400 font-mono bg-slate-950 px-1.5 py-0.5 rounded border border-slate-800">
                                            BA: {{ $item->nomor_ba_reklas }}
                                        </span>
                                    @endif
                                    @if (!empty($item->alasan_reklas))
                                        <span class="text-[9.5px] text-amber-300 bg-amber-500/15 px-1.5 py-0.5 rounded border border-amber-500/30 flex items-center gap-1" title="Alasan: {{ $item->alasan_reklas }}">
                                            <span>💡</span> <span class="max-w-[140px] truncate">{{ $item->alasan_reklas }}</span>
                                        </span>
                                    @endif
                                    @if (!empty($item->spesifikasi_baru))
                                        <span class="text-[9.5px] font-bold text-cyan-300 bg-cyan-500/15 px-1.5 py-0.5 rounded border border-cyan-500/30 flex items-center gap-1">
                                            <span>✨</span> Spek Baru
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-3.5 px-4">
                                @php
                                    $badge = match ($item->jenis_reklas) {
                                        'KOREKSI_REKENING' => ['bg' => 'bg-indigo-500/10', 'text' => 'text-indigo-400', 'border' => 'border-indigo-500/30', 'label' => 'Koreksi Rekening'],
                                        'KDP_TO_DEFINITIF' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'border' => 'border-emerald-500/30', 'label' => 'KDP Selesai ➔ Definitif'],
                                        'EKSTRAKOMPTABEL' => ['bg' => 'bg-rose-500/10', 'text' => 'text-rose-400', 'border' => 'border-rose-500/30', 'label' => 'Ekstrakomptabel'],
                                        'KAPITALISASI_INTRAKOM' => ['bg' => 'bg-teal-500/10', 'text' => 'text-teal-300', 'border' => 'border-teal-500/30', 'label' => 'Kapitalisasi Intrakom'],
                                        'HIBAH_MASUK' => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'border' => 'border-amber-500/30', 'label' => 'Hibah Masuk'],
                                        'KOREKSI_LAIN' => ['bg' => 'bg-cyan-500/10', 'text' => 'text-cyan-400', 'border' => 'border-cyan-500/30', 'label' => 'Koreksi Nilai / BPK'],
                                        default => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-400', 'border' => 'border-slate-500/30', 'label' => $item->jenis_reklas],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                @if ($item->jenis_reklas === 'KOREKSI_LAIN')
                                    @php
                                        // Koreksi Nilai Realisasi: Aset tetap di KIB semula (tanpa perpindahan rekening)
                                        $kibAsal = ($item->asal_kib && $item->asal_kib !== 'KOREKSI') 
                                            ? $item->asal_kib 
                                            : (($item->tujuan_kib && $item->tujuan_kib !== 'KOREKSI') ? $item->tujuan_kib : ($item->astap->category ?? 'KIB'));
                                    @endphp
                                    <div class="flex items-center gap-1.5" title="Aset tetap pada {{ $kibAsal }} (tanpa perpindahan rekening / kamar KIB)">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 shrink-0">
                                            {{ $kibAsal }} (Tetap)
                                        </span>
                                    </div>
                                    <div class="text-[10.5px] text-slate-400 mt-1 flex items-center gap-1.5 truncate max-w-[240px]">
                                        <span class="text-cyan-400/90 font-medium">Penyesuaian Realisasi</span>
                                        <span class="text-slate-600">·</span>
                                        <span class="text-slate-400">Audit BPK</span>
                                    </div>

                                @elseif ($item->jenis_reklas === 'EKSTRAKOMPTABEL')
                                    @php
                                        $kibAsal = $item->asal_kib ?: ($item->astap->category ?? 'KIB');
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-rose-500/15 text-rose-300 border border-rose-500/30 shrink-0">
                                            {{ $kibAsal }}
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-rose-300 border border-slate-700 shrink-0">
                                            Ekstrakomptabel
                                        </span>
                                    </div>
                                    <div class="text-[10.5px] text-slate-400 mt-1 truncate max-w-[240px]" title="Di bawah batas kapitalisasi (≤ Rp 300.000)">
                                        Di bawah batas kapitalisasi (≤ Rp 300rb)
                                    </div>

                                @elseif ($item->jenis_reklas === 'KAPITALISASI_INTRAKOM')
                                    @php
                                        $kibTujuan = $item->tujuan_kib ?: 'KIB B';
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 shrink-0">
                                            Ekstrakomptabel
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 shrink-0">
                                            {{ $kibTujuan }}
                                        </span>
                                    </div>
                                    <div class="text-[10.5px] text-teal-400/90 mt-1 truncate max-w-[240px]" title="Masuk Aset Tetap Intrakomptabel">
                                        Masuk Aset Tetap Intrakomptabel
                                    </div>

                                @elseif ($item->jenis_reklas === 'KDP_TO_DEFINITIF')
                                    @php
                                        $kibTujuan = $item->tujuan_kib ?: 'Definitif';
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-amber-500/15 text-amber-300 border border-amber-500/30 shrink-0">
                                            KIB F (KDP)
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 shrink-0">
                                            {{ $kibTujuan }}
                                        </span>
                                    </div>
                                    <div class="text-[10.5px] text-emerald-400/90 mt-1 truncate max-w-[240px]" title="Kapitalisasi Fisik Selesai 100%">
                                        Kapitalisasi Fisik Selesai 100%
                                    </div>

                                @elseif ($item->jenis_reklas === 'HIBAH_MASUK')
                                    @php
                                        $kibTujuan = $item->tujuan_kib ?: ($item->astap->category ?? 'KIB');
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-amber-500/15 text-amber-300 border border-amber-500/30 shrink-0">
                                            Hibah
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 shrink-0">
                                            {{ $kibTujuan }}
                                        </span>
                                    </div>
                                    <div class="text-[10.5px] text-amber-400/90 mt-1 truncate max-w-[240px]">
                                        Penerimaan Hibah Aset
                                    </div>

                                @else
                                    @php
                                        $asalKibLabel = $item->asal_kib ?: ($item->jenisReklasAsal->kelompok_kib ?? 'Asal');
                                        $tujuanKibLabel = $item->tujuan_kib ?: ($item->jenisReklasTujuan->kelompok_kib ?? 'Tujuan');
                                        $asalNamaTeks = $item->asal_nama ?: ($item->jenisReklasAsal->nama_sub_rincian ?? $asalKibLabel);
                                        $tujuanNamaTeks = $item->tujuan_nama ?: ($item->jenisReklasTujuan->nama_sub_rincian ?? $tujuanKibLabel);
                                        $fullTitle = "Dari: " . ($item->asal_kode ? "[{$item->asal_kode}] " : '') . "{$asalNamaTeks} ➔ Ke: " . ($item->tujuan_kode ? "[{$item->tujuan_kode}] " : '') . "{$tujuanNamaTeks}";
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold" title="{{ $fullTitle }}">
                                        <span class="px-2 py-0.5 rounded bg-rose-500/15 text-rose-300 border border-rose-500/30 shrink-0 shadow-sm">
                                            {{ $asalKibLabel }}
                                        </span>
                                        <span class="text-indigo-400 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 shrink-0 shadow-sm">
                                            {{ $tujuanKibLabel }}
                                        </span>
                                    </div>
                                    <div class="text-[10.5px] text-slate-400 mt-1 truncate max-w-[240px] flex items-center gap-1.5" title="{{ $fullTitle }}">
                                        @if ($item->asal_kode || $item->tujuan_kode)
                                            <span class="font-mono text-rose-300/80">{{ $item->asal_kode ?: $asalKibLabel }}</span>
                                            <span class="text-slate-500 text-[9px]">➔</span>
                                            <span class="font-mono text-emerald-300 font-semibold">{{ $item->tujuan_kode ?: $tujuanKibLabel }}</span>
                                        @else
                                            <span class="truncate">{{ $tujuanNamaTeks }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-white text-sm">
                                Rp {{ number_format($item->nilai_reklas, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Rincian / Detail Audit & Komparasi Spek -->
                                    <button type="button" @click="openDetailReklas({{ json_encode($item) }})"
                                        class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-400 hover:text-cyan-300 border border-cyan-500/30 hover:border-cyan-500/50 transition-all duration-200 cursor-pointer text-xs font-semibold shadow-sm group"
                                        title="Lihat Rincian &amp; Keterangan Lengkap">
                                        <svg class="w-3.5 h-3.5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>Detail</span>
                                    </button>

                                    <!-- Tombol Hapus / Batalkan Transaksi -->
                                    <button type="button" @click="confirmHapusReklas({{ $item->id }}, '{{ addslashes($item->astap->nama_barang ?? 'Aset') }}')"
                                        class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 border border-rose-500/30 hover:border-rose-500/50 transition-all duration-200 cursor-pointer shadow-sm"
                                        title="Hapus / Batalkan Transaksi Reklasifikasi">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
