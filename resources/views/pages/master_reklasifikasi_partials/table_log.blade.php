<!-- TABEL LOG TRANSAKSI REKLASIFIKASI (AUDIT TRAIL) -->
<div class="bg-slate-900/90 border border-slate-800 rounded-2xl shadow-2xl overflow-hidden backdrop-blur-xl">
    <div class="px-6 py-4 border-b border-slate-800 flex items-center justify-between bg-slate-950/40">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span>📋</span> Riwayat Audit & Transaksi Mutasi Reklasifikasi
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">
                Daftar per item barang yang pernah dipindahkan/direklasifikasi dalam periode aktif
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
                class="mt-4 inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all">
                <span>+ Catat Reklasifikasi Baru</span>
            </button>
        </div>
    @else
        <div class="overflow-x-auto scrollbar-thin scrollbar-thumb-slate-700">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-800/90 text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700 text-[11px]">
                        <th class="py-3 px-4 w-12 text-center">No</th>
                        <th class="py-3 px-4 min-w-[140px]">Tanggal & Periode</th>
                        <th class="py-3 px-4 min-w-[220px]">Nama Barang & No. BA</th>
                        <th class="py-3 px-4 min-w-[180px]">Jenis Reklasifikasi</th>
                        <th class="py-3 px-4 min-w-[200px]">Perpindahan (Asal ➔ Tujuan)</th>
                        <th class="py-3 px-4 min-w-[150px] text-right">Nilai Reklas</th>
                        <th class="py-3 px-4 min-w-[180px]">Keterangan</th>
                        <th class="py-3 px-4 w-24 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @foreach ($logReklas as $idx => $item)
                        <tr id="row-reklas-{{ $item->id }}" class="hover:bg-slate-800/30 transition-colors">
                            <td class="py-3 px-4 text-center text-slate-400 font-mono text-[11px]">
                                {{ $idx + 1 }}
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-white">
                                    {{ date('d/m/Y', strtotime($item->tanggal_reklas)) }}
                                </div>
                                <div class="text-[11px] text-indigo-400 font-medium mt-0.5">
                                    Triwulan {{ $item->triwulan }} · {{ $item->tahun }}
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                <div class="font-bold text-white text-sm">
                                    {{ $item->astap->nama_barang ?? 'Aset #' . $item->astap_id }}
                                </div>
                                @if ($item->nomor_ba_reklas)
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        BA: {{ $item->nomor_ba_reklas }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $badge = match ($item->jenis_reklas) {
                                        'KOREKSI_REKENING' => ['bg' => 'bg-indigo-500/10', 'text' => 'text-indigo-400', 'border' => 'border-indigo-500/30', 'label' => 'Koreksi Rekening'],
                                        'KDP_TO_DEFINITIF' => ['bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'border' => 'border-emerald-500/30', 'label' => 'KDP Selesai ➔ Gedung'],
                                        'EKSTRAKOMPTABEL' => ['bg' => 'bg-rose-500/10', 'text' => 'text-rose-400', 'border' => 'border-rose-500/30', 'label' => 'Ekstrakomptabel'],
                                        'HIBAH_MASUK' => ['bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'border' => 'border-amber-500/30', 'label' => 'Hibah Masuk'],
                                        default => ['bg' => 'bg-slate-500/10', 'text' => 'text-slate-400', 'border' => 'border-slate-500/30', 'label' => $item->jenis_reklas],
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold border {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                    {{ $badge['label'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <div class="flex items-center gap-2 text-xs">
                                    <span class="px-2 py-0.5 rounded bg-slate-800 text-rose-300 font-semibold border border-rose-500/20">
                                        {{ $item->jenisReklasAsal->nama_sub_rincian ?? ($item->asal_kib ?: '-') }}
                                    </span>
                                    <span class="text-slate-500 font-black">➔</span>
                                    <span class="px-2 py-0.5 rounded bg-slate-800 text-emerald-300 font-semibold border border-emerald-500/20">
                                        {{ $item->jenisReklasTujuan->nama_sub_rincian ?? ($item->tujuan_kib ?: '-') }}
                                    </span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-white">
                                Rp {{ number_format($item->nilai_reklas, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-slate-400 text-xs">
                                {{ $item->keterangan ?: '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <button type="button" @click="confirmHapusReklas({{ $item->id }}, '{{ addslashes($item->astap->nama_barang ?? 'Aset') }}')"
                                    class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-600 text-rose-400 hover:text-white border border-rose-500/30 hover:border-rose-500 transition-all duration-200 cursor-pointer"
                                    title="Hapus / Batalkan Transaksi Reklasifikasi">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
