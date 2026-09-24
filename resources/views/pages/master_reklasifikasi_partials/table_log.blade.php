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
                        <th class="py-3 px-4 min-w-[140px]">Tanggal &amp; Periode</th>
                        <th class="py-3 px-4 min-w-[240px]">Nama Barang &amp; No. BA</th>
                        <th class="py-3 px-4 min-w-[170px]">Jenis Reklasifikasi</th>
                        <th class="py-3 px-4 min-w-[210px]">Perpindahan (Asal ➔ Tujuan)</th>
                        <th class="py-3 px-4 min-w-[150px] text-right">Nilai Reklas</th>
                        <th class="py-3 px-4 min-w-[180px]">Keterangan</th>
                        <th class="py-3 px-4 w-28 text-center">Aksi</th>
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
                                        'HIBAH_MASUK' => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'border' => 'border-amber-500/30', 'label' => 'Hibah Masuk'],
                                        default => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-400', 'border' => 'border-slate-500/30', 'label' => $item->jenis_reklas],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="px-2 py-0.5 rounded bg-slate-800 text-rose-300 font-semibold border border-rose-500/20"
                                          title="{{ $item->jenisReklasAsal->nama_sub_rincian ?? ($item->asal_kib ?: '-') }}">
                                        {{ $item->asal_kib ?: ($item->jenisReklasAsal->kelompok_kib ?? 'Asal') }}
                                    </span>
                                    <span class="text-slate-500 font-black">➔</span>
                                    <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-300 font-semibold border border-emerald-500/20"
                                          title="{{ $item->jenisReklasTujuan->nama_sub_rincian ?? ($item->tujuan_kib ?: '-') }}">
                                        {{ $item->tujuan_kib ?: ($item->jenisReklasTujuan->kelompok_kib ?? 'Tujuan') }}
                                    </span>
                                </div>
                                <div class="text-[10px] text-slate-400 truncate max-w-[200px] mt-1"
                                     title="{{ $item->jenisReklasTujuan->nama_sub_rincian ?? ($item->tujuan_nama ?: '') }}">
                                    {{ $item->jenisReklasTujuan->nama_sub_rincian ?? ($item->tujuan_nama ?: '-') }}
                                </div>
                            </td>
                            <td class="py-3.5 px-4 text-right font-mono font-bold text-white text-sm">
                                Rp {{ number_format($item->nilai_reklas, 0, ',', '.') }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-400 text-xs">
                                @if (!empty($item->alasan_reklas))
                                    <div class="text-amber-300 font-semibold mb-1 flex items-center gap-1 text-[11px] truncate max-w-[220px]" title="{{ $item->alasan_reklas }}">
                                        <span>💡</span>
                                        <span class="truncate">{{ $item->alasan_reklas }}</span>
                                    </div>
                                @endif
                                <span class="line-clamp-2 text-slate-400 text-[11px]" title="{{ $item->keterangan }}">
                                    {{ $item->keterangan ?: '-' }}
                                </span>
                            </td>
                            <td class="py-3.5 px-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Rincian / Detail Audit & Komparasi Spek -->
                                    <button type="button" @click="openDetailReklas({{ json_encode($item) }})"
                                        class="p-1.5 rounded-lg bg-cyan-500/10 hover:bg-cyan-600 text-cyan-400 hover:text-white border border-cyan-500/30 hover:border-cyan-500 transition-all duration-200 cursor-pointer shadow-sm"
                                        title="Lihat Rincian Audit & Komparasi Spesifikasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </button>

                                    <!-- Tombol Hapus / Batalkan Transaksi -->
                                    <button type="button" @click="confirmHapusReklas({{ $item->id }}, '{{ addslashes($item->astap->nama_barang ?? 'Aset') }}')"
                                        class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/30 hover:border-rose-500 transition-all duration-200 cursor-pointer shadow-sm"
                                        title="Hapus / Batalkan Transaksi Reklasifikasi">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
