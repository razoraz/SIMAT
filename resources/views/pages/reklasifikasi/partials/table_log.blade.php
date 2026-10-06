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
            <h4 class="text-base font-bold text-white">
                @if (($selectedJenis ?? 'all') !== 'all')
                    Tidak Ditemukan Transaksi
                @else
                    Belum Ada Transaksi Reklasifikasi
                @endif
            </h4>
            <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                @if (($selectedJenis ?? 'all') !== 'all')
                    Tidak ada transaksi reklasifikasi dengan jenis yang dipilih pada periode ini.
                @else
                    Belum ada aset yang dimutasi antar rekening atau KIB pada periode tahun {{ $selectedTahun }}.
                @endif
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
                    <tr class="bg-slate-800/90 text-slate-300 font-bold uppercase tracking-wider border-b border-slate-700 text-[10.5px]">
                        <th class="py-3 px-3.5 w-12 text-center">No</th>
                        <th class="py-3 px-3.5 min-w-[125px]">Tanggal &amp; Periode</th>
                        <th class="py-3 px-3.5 min-w-[220px]">Identitas Barang &amp; Dokumen</th>
                        <th class="py-3 px-3.5 min-w-[230px]">Jenis &amp; Alasan Reklasifikasi</th>
                        <th class="py-3 px-3.5 min-w-[240px]">Mutasi Rekening / KIB</th>
                        <th class="py-3 px-3.5 min-w-[170px] text-right">Nilai &amp; Dampak Buku</th>
                        <th class="py-3 px-3.5 w-28 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 font-medium">
                    @foreach ($logReklas as $idx => $item)
                        @php
                            // Deteksi arah mutasi nilai untuk KOREKSI_LAIN
                            $isKoreksiTambah = ($item->jenisReklasAsal?->kode_prefix === 'KOR_LAIN') || 
                                               (str_contains(strtolower($item->keterangan ?? ''), 'penambahan nilai') || 
                                                str_contains(strtolower($item->alasan_reklas ?? ''), 'tambah'));
                            
                            $badge = match ($item->jenis_reklas) {
                                'KOREKSI_REKENING'      => ['bg' => 'bg-indigo-500/15', 'text' => 'text-indigo-300', 'border' => 'border-indigo-500/40', 'icon' => '🔄', 'label' => 'Koreksi Rekening'],
                                'KDP_TO_DEFINITIF'     => ['bg' => 'bg-emerald-500/15', 'text' => 'text-emerald-300', 'border' => 'border-emerald-500/40', 'icon' => '🏗️', 'label' => 'KDP Selesai ➔ Definitif'],
                                'EKSTRAKOMPTABEL'      => ['bg' => 'bg-rose-500/15', 'text' => 'text-rose-300', 'border' => 'border-rose-500/40', 'icon' => '🔻', 'label' => 'Ekstrakomptabel'],
                                'KAPITALISASI_INTRAKOM' => ['bg' => 'bg-teal-500/15', 'text' => 'text-teal-300', 'border' => 'border-teal-500/40', 'icon' => '🔺', 'label' => 'Kapitalisasi Intrakom'],
                                'HIBAH_KELUAR'         => ['bg' => 'bg-purple-500/15', 'text' => 'text-purple-300', 'border' => 'border-purple-500/40', 'icon' => '📤', 'label' => 'Hibah Keluar (BAST)'],
                                'HIBAH_MASUK'          => ['bg' => 'bg-amber-500/15', 'text' => 'text-amber-300', 'border' => 'border-amber-500/40', 'icon' => '📥', 'label' => 'Hibah Masuk (Bantuan)'],
                                'MUTASI_EKSTERNAL'     => ['bg' => 'bg-teal-500/15', 'text' => 'text-teal-300', 'border' => 'border-teal-500/40', 'icon' => '🏛️', 'label' => 'Mutasi Keluar Antar-OPD'],
                                'KOREKSI_LAIN'         => ['bg' => 'bg-cyan-500/15', 'text' => 'text-cyan-300', 'border' => 'border-cyan-500/40', 'icon' => '⚖️', 'label' => 'Koreksi Nilai / BPK'],
                                default                => ['bg' => 'bg-slate-500/15', 'text' => 'text-slate-300', 'border' => 'border-slate-500/40', 'icon' => '📄', 'label' => $item->jenis_reklas],
                            };

                            // Harga satuan barang & volume
                            $volBarang = $item->astap?->jumlah_volume ?? 1;
                            $hrgSatuan = $item->astap?->harga_satuan ?? ($volBarang > 0 ? ($item->nilai_reklas / $volBarang) : $item->nilai_reklas);
                        @endphp
                        <tr id="row-reklas-{{ $item->id }}" class="hover:bg-slate-800/30 transition-colors">
                            <!-- 1. Nomor Urut -->
                            <td class="py-3.5 px-3.5 text-center text-slate-400 font-mono text-[11px]">
                                {{ $idx + 1 }}
                            </td>

                            <!-- 2. Tanggal & Periode -->
                            <td class="py-3.5 px-3.5">
                                <div class="font-bold text-white text-xs">
                                    {{ date('d/m/Y', strtotime($item->tanggal_reklas)) }}
                                </div>
                                <div class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-mono font-semibold bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 mt-1">
                                    Triwulan {{ $item->triwulan }} · {{ $item->tahun }}
                                </div>
                            </td>

                            <!-- 3. Identitas Barang & Dokumen Dasar -->
                            <td class="py-3.5 px-3.5">
                                <div class="font-bold text-white text-sm leading-snug">
                                    {{ $item->astap->nama_barang ?? 'Aset #' . $item->astap_id }}
                                </div>
                                
                                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                                    @if ($item->nomor_ba_reklas)
                                        <span class="text-[10px] text-slate-300 font-mono bg-slate-950 px-2 py-0.5 rounded-md border border-slate-800 flex items-center gap-1 shadow-sm" title="Nomor Dokumen / BAST / LHP BPK">
                                            <span class="text-indigo-400">📄</span>
                                            <span>{{ $item->nomor_ba_reklas }}</span>
                                        </span>
                                    @endif

                                    @if (!empty($item->spesifikasi_baru))
                                        <span class="text-[9.5px] font-bold text-cyan-300 bg-cyan-500/15 px-1.5 py-0.5 rounded border border-cyan-500/30 flex items-center gap-1">
                                            <span>✨</span> Spek Baru
                                        </span>
                                    @endif

                                    @if ($item->tujuan_kib === 'KEMITRAAN' || str_starts_with($item->tujuan_kode ?? '', '1.5.2'))
                                        <span class="text-[9.5px] font-extrabold text-teal-300 bg-teal-500/15 px-1.5 py-0.5 rounded border border-teal-500/30 flex items-center gap-1">
                                            <span>🤝</span> Kemitraan
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 4. Jenis Reklasifikasi & Alasan Kenapa Melakukan Reklas (WAJIB DIISI) -->
                            <td class="py-3.5 px-3.5">
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-extrabold border shadow-sm {{ $badge['bg'] }} {{ $badge['text'] }} {{ $badge['border'] }}">
                                        <span>{{ $badge['icon'] }}</span>
                                        <span>{{ $badge['label'] }}</span>
                                    </span>
                                </div>

                                <!-- Box Alasan Reklasifikasi (Terbaca Jelas & Scannable) -->
                                <div class="mt-2 p-2 rounded-xl bg-slate-950/80 border border-slate-800/90 text-[11px] leading-relaxed shadow-inner">
                                    <div class="text-[9.5px] uppercase font-bold text-amber-300 flex items-center gap-1 mb-0.5">
                                        <span>📝</span>
                                        <span>Alasan Reklas:</span>
                                    </div>
                                    <p class="text-slate-300 line-clamp-2 italic" title="{{ $item->alasan_reklas }}">
                                        "{{ $item->alasan_reklas ?: 'Penyesuaian klasifikasi akuntansi aset tetap RSUD' }}"
                                    </p>
                                </div>
                            </td>

                            <!-- 5. Mutasi Rekening / Kamar KIB (Konteks Spesifik per Jenis) -->
                            <td class="py-3.5 px-3.5">
                                @if ($item->jenis_reklas === 'KOREKSI_LAIN')
                                    @php
                                        $kibAsal = ($item->asal_kib && $item->asal_kib !== 'KOREKSI') 
                                            ? $item->asal_kib 
                                            : (($item->tujuan_kib && $item->tujuan_kib !== 'KOREKSI') ? $item->tujuan_kib : ($item->astap->category ?? 'KIB B'));
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2.5 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 border border-cyan-500/40 shrink-0">
                                            {{ $kibAsal }} (Tetap)
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-cyan-300/80 mt-1 flex items-center gap-1">
                                        <span>⚖️</span>
                                        <span>Audit LHP BPK / Rekonsiliasi Nilai</span>
                                    </div>

                                @elseif ($item->jenis_reklas === 'EKSTRAKOMPTABEL')
                                    @php
                                        $kibAsal = $item->asal_kib ?: ($item->astap->category ?? 'KIB B');
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-300 border border-slate-700 shrink-0">
                                            {{ $kibAsal }}
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 shrink-0">
                                            Ekstrakomptabel
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-rose-300/85 mt-1 font-mono flex items-center gap-1" title="Nilai satuan barang di bawah batas kapitalisasi (≤ Rp 300.000)">
                                        <span>🏷️</span>
                                        <span>{{ $volBarang }} Unit @ Rp {{ number_format($hrgSatuan, 0, ',', '.') }} (&le; Rp 300rb)</span>
                                    </div>

                                @elseif ($item->jenis_reklas === 'KAPITALISASI_INTRAKOM')
                                    @php
                                        $kibTujuan = $item->tujuan_kib ?: 'KIB B';
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700 shrink-0">
                                            Ekstrakomptabel
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shrink-0">
                                            {{ $kibTujuan }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-emerald-300/85 mt-1 font-mono flex items-center gap-1" title="Nilai satuan melampaui batas kapitalisasi (> Rp 300.000)">
                                        <span>🏷️</span>
                                        <span>{{ $volBarang }} Unit @ Rp {{ number_format($hrgSatuan, 0, ',', '.') }} (&gt; Rp 300rb)</span>
                                    </div>

                                @elseif ($item->jenis_reklas === 'KDP_TO_DEFINITIF')
                                    @php
                                        $kibTujuan = ($item->tujuan_kib === 'KIB D') ? 'KIB D' : 'KIB C';
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 shrink-0">
                                            KIB F (KDP)
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shrink-0">
                                            {{ $kibTujuan }} (Definitif)
                                        </span>
                                    </div>
                                    <div class="inline-flex items-center gap-1 mt-1 px-2 py-0.5 rounded-full text-[9.5px] font-extrabold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span>100% Fisik Rampung &amp; BAST</span>
                                    </div>

                                @elseif ($item->jenis_reklas === 'HIBAH_KELUAR')
                                    @php
                                        $kibAsal = $item->asal_kib ?: ($item->astap->category ?? 'KIB B');
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 shrink-0">
                                            {{ $kibAsal }}
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/40 shrink-0">
                                            Hibah Keluar
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-purple-300 mt-1 truncate max-w-[200px]" title="{{ $item->nomor_ba_reklas ?: 'BAST Hibah' }}">
                                        {{ $item->nomor_ba_reklas ?: 'Penyerahan Hibah (BAST)' }}
                                    </div>

                                @elseif ($item->jenis_reklas === 'HIBAH_MASUK')
                                    @php
                                        $kibTujuan = $item->tujuan_kib ?: ($item->astap->category ?? 'KIB B');
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/40 shrink-0">
                                            Hibah Masuk
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shrink-0">
                                            {{ $kibTujuan }}
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-amber-400 mt-1">
                                        Penerimaan Bantuan Tanpa Kas
                                    </div>

                                @elseif ($item->jenis_reklas === 'MUTASI_EKSTERNAL')
                                    @php
                                        $kibAsal = $item->asal_kib ?: ($item->astap->category ?? 'KIB B');
                                    @endphp
                                    <div class="flex items-center gap-1.5 text-xs font-bold">
                                        <span class="px-2 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/40 shrink-0">
                                            {{ $kibAsal }}
                                        </span>
                                        <span class="text-slate-500 font-bold">➔</span>
                                        <span class="px-2 py-0.5 rounded bg-teal-500/20 text-teal-300 border border-teal-500/40 shrink-0">
                                            Mutasi OPD
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-teal-300 mt-1 truncate max-w-[200px]" title="{{ $item->nomor_ba_reklas ?: 'BAST Mutasi' }}">
                                        {{ $item->nomor_ba_reklas ?: 'Alih Antar-SKPD' }}
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
                                    <div class="text-[10px] text-slate-400 mt-1 flex items-center gap-1 font-mono" title="{{ $fullTitle }}">
                                        @if ($item->asal_kode || $item->tujuan_kode)
                                            <span class="text-rose-300/80">{{ $item->asal_kode ?: $asalKibLabel }}</span>
                                            <span class="text-slate-500 text-[9px]">➔</span>
                                            <span class="text-emerald-300 font-semibold">{{ $item->tujuan_kode ?: $tujuanKibLabel }}</span>
                                        @else
                                            <span class="truncate">{{ $tujuanNamaTeks }}</span>
                                        @endif
                                    </div>
                                @endif
                            </td>

                            <!-- 6. Nilai Reklasifikasi & Dampak Neraca -->
                            <td class="py-3.5 px-3.5 text-right font-mono">
                                <div class="font-extrabold text-white text-sm">
                                    Rp {{ number_format($item->nilai_reklas, 0, ',', '.') }}
                                </div>

                                <!-- Indikator Dampak Neraca per Jenis Reklas -->
                                <div class="mt-1">
                                    @if ($item->jenis_reklas === 'KOREKSI_LAIN')
                                        @if ($isKoreksiTambah)
                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20" title="Penyeimbang Baris 42">
                                                <span>[+]</span> <span>Penambahan Nilai</span>
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-rose-400 bg-rose-500/10 px-1.5 py-0.5 rounded border border-rose-500/20" title="Penyeimbang Baris 42">
                                                <span>[-]</span> <span>Pengurangan Nilai</span>
                                            </span>
                                        @endif
                                    @elseif ($item->jenis_reklas === 'HIBAH_KELUAR')
                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-purple-300 bg-purple-500/10 px-1.5 py-0.5 rounded border border-purple-500/20" title="Mengisi Kolom 12 (Dihibahkan) & Baris 40 (+)">
                                            <span>📤</span> <span>Penyeimbang Hibah</span>
                                        </span>
                                    @elseif ($item->jenis_reklas === 'HIBAH_MASUK')
                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-amber-300 bg-amber-500/10 px-1.5 py-0.5 rounded border border-amber-500/20" title="Penyeimbang Baris 40 (-)">
                                            <span>📥</span> <span>Bantuan Masuk</span>
                                        </span>
                                    @elseif ($item->jenis_reklas === 'MUTASI_EKSTERNAL')
                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-teal-300 bg-teal-500/10 px-1.5 py-0.5 rounded border border-teal-500/20" title="Mengisi Kolom 13 (Mutasi -) & Baris 42 (+)">
                                            <span>🏛️</span> <span>Mutasi Keluar OPD</span>
                                        </span>
                                    @elseif ($item->jenis_reklas === 'EKSTRAKOMPTABEL')
                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-rose-400 bg-rose-500/10 px-1.5 py-0.5 rounded border border-rose-500/20" title="Mengisi Kolom 14 (Kapitalisasi -) & Baris 41 (+)">
                                            <span>[-]</span> <span>Eliminasi Neraca</span>
                                        </span>
                                    @elseif ($item->jenis_reklas === 'KAPITALISASI_INTRAKOM')
                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-teal-300 bg-teal-500/10 px-1.5 py-0.5 rounded border border-teal-500/20" title="Penyeimbang Baris 41 (-)">
                                            <span>[+]</span> <span>Masuk Neraca Aset</span>
                                        </span>
                                    @elseif ($item->jenis_reklas === 'KDP_TO_DEFINITIF')
                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-emerald-300 bg-emerald-500/10 px-1.5 py-0.5 rounded border border-emerald-500/20" title="Mengisi Kolom 20 (KDP)">
                                            <span>🏛️</span> <span>Kapitalisasi Penuh</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-0.5 text-[10px] font-bold text-indigo-300 bg-indigo-500/10 px-1.5 py-0.5 rounded border border-indigo-500/20">
                                            <span>🔄</span> <span>Pergeseran Akun</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- 7. Tombol Aksi -->
                            <td class="py-3.5 px-3.5 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- Tombol Rincian / Detail Audit & Komparasi Spek -->
                                    <button type="button" @click="openDetailReklas({{ json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) }})"
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
