<!-- ========================================================================= -->
<!-- TABEL DATA MASTER ASET KEMITRAAN PIHAK KETIGA (AKUN 1.5.2)                -->
<!-- ========================================================================= -->
<div class="rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl overflow-hidden">
    <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                <span>📋 Daftar Aset Kemitraan (KSO, BGS, Sewa)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Total {{ count($kemitraanRecords ?? []) }} data aset kerja sama tercatat dalam sistem SIMAT-RK.
            </p>
        </div>

        <span class="text-[11px] font-mono font-bold text-cyan-400 bg-cyan-500/10 px-3 py-1 rounded-xl border border-cyan-500/30">
            Akun 1.5.2 Aset Kemitraan
        </span>
    </div>

    <div class="overflow-x-auto custom-scrollbar">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950/80 text-slate-400 uppercase text-[10px] tracking-wider border-b border-slate-800 font-extrabold">
                <tr>
                    <th class="py-3.5 px-4 w-12 text-center">No</th>
                    <th class="py-3.5 px-4 min-w-[200px]">Dokumen PKS &amp; Rekanan</th>
                    <th class="py-3.5 px-4 min-w-[220px]">Identitas Barang (Akun 108)</th>
                    <th class="py-3.5 px-4 min-w-[170px]">Ruangan Penempatan</th>
                    <th class="py-3.5 px-4 min-w-[170px]">Masa Konsesi / Kerjasama</th>
                    <th class="py-3.5 px-4 min-w-[140px] text-right">Taksiran Nilai (Rp)</th>
                    <th class="py-3.5 px-4 min-w-[110px] text-center">Status</th>
                    <th class="py-3.5 px-4 w-28 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/60">
                @forelse($kemitraanRecords ?? [] as $idx => $row)
                    @php
                        $astap = $row->astap;
                        $firstReg = $astap?->registers?->first();
                        $unit = $firstReg?->unit ?? $astap?->unit;
                        $sisaHari = $row->sisa_hari_konsesi;
                    @endphp
                    <tr class="hover:bg-slate-800/40 transition-colors group">
                        <!-- 1. Nomor -->
                        <td class="py-4 px-4 text-center font-mono text-slate-500 text-xs">
                            {{ $idx + 1 }}
                        </td>

                        <!-- 2. Dokumen PKS & Rekanan -->
                        <td class="py-4 px-4">
                            <div class="flex items-center gap-2 mb-1">
                                <span class="px-2 py-0.5 rounded text-[10px] font-black uppercase tracking-wider
                                    {{ $row->skema_kemitraan === 'KSO' ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : '' }}
                                    {{ $row->skema_kemitraan === 'BGS' ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : '' }}
                                    {{ $row->skema_kemitraan === 'BSG' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                    {{ $row->skema_kemitraan === 'Sewa' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : '' }}
                                    {{ $row->skema_kemitraan === 'KSP' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : '' }}
                                ">
                                    {{ $row->skema_kemitraan ?: 'KSO' }}
                                </span>
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

                        <!-- 3. Identitas Barang & 108 -->
                        <td class="py-4 px-4">
                            <div class="text-xs font-bold text-white group-hover:text-cyan-300 transition-colors leading-snug">
                                {{ $astap?->nama_barang ?: 'Barang Aset Kemitraan' }}
                            </div>
                            <div class="text-[11px] font-mono text-cyan-400 mt-0.5">
                                {{ $astap?->kode_108 ?: ($astap?->jenisAstap?->sub_sub_rincian_objek ?: '1.5.2.x') }}
                            </div>
                            <div class="flex items-center gap-2 mt-1 text-[10px] text-slate-400">
                                <span>Vol: <strong>{{ $row->jumlah_volume }} {{ $row->satuan }}</strong></span>
                                <span>•</span>
                                <span>Kondisi: <strong class="text-emerald-400">{{ $firstReg?->kondisi ?: 'Baik' }}</strong></span>
                            </div>
                        </td>

                        <!-- 4. Ruangan Penempatan -->
                        <td class="py-4 px-4">
                            <div class="text-xs font-semibold text-slate-200">
                                🏢 {{ $unit?->nama ?: ($firstReg?->ruang_pemegang ?: 'RSUD Dr. H. Koesnandi') }}
                            </div>
                            <div class="text-[10px] text-slate-400 mt-0.5">
                                NIBAR: <span class="font-mono text-slate-300">{{ $firstReg?->nibar ?: '-' }}</span>
                            </div>
                        </td>

                        <!-- 5. Masa Konsesi & Countdown Sisa Hari -->
                        <td class="py-4 px-4">
                            @if($row->tanggal_mulai || $row->tanggal_selesai)
                                <div class="text-[11px] font-medium text-slate-300">
                                    {{ $row->tanggal_mulai ? \Carbon\Carbon::parse($row->tanggal_mulai)->format('d/m/Y') : '?' }} 
                                    s.d. 
                                    {{ $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->format('d/m/Y') : '?' }}
                                </div>
                                @if(!is_null($sisaHari))
                                    @if($sisaHari > 60)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md mt-1">
                                            <span>⏱️</span> Sisa {{ $sisaHari }} hari
                                        </span>
                                    @elseif($sisaHari > 0)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-md mt-1">
                                            <span>⚠️</span> Sisa {{ $sisaHari }} hari
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-md mt-1">
                                            <span>🛑</span> Konsesi Berakhir
                                        </span>
                                    @endif
                                @endif
                            @else
                                <span class="text-slate-500 text-[11px] italic">Tanpa batas waktu</span>
                            @endif
                        </td>

                        <!-- 6. Taksiran Nilai Wajar -->
                        <td class="py-4 px-4 text-right">
                            <div class="font-mono text-xs font-black text-cyan-300">
                                Rp {{ number_format($row->nilai_aset, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-slate-500 mt-0.5">
                                {{ $row->tahun }} • {{ $row->triwulan }}
                            </div>
                        </td>

                        <!-- 7. Status Konsesi -->
                        <td class="py-4 px-4 text-center">
                            @if($row->status_konsesi === 'Aktif')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                    Aktif
                                </span>
                            @elseif($row->status_konsesi === 'Selesai / Reklasifikasi')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/15 text-blue-400 border border-blue-500/30">
                                    Siap Reklas
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">
                                    {{ $row->status_konsesi }}
                                </span>
                            @endif
                        </td>

                        <!-- 8. Aksi -->
                        <td class="py-4 px-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- Tombol Detail -->
                                <button type="button" @click="openDetail({{ json_encode($row) }}, {{ json_encode($astap) }}, {{ json_encode($firstReg) }})"
                                    title="Lihat Detail Lengkap PKS"
                                    class="p-2 rounded-xl bg-slate-800 hover:bg-cyan-500 hover:text-slate-950 text-slate-300 transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>

                                <!-- Tombol Hapus / Batalkan -->
                                <button type="button" @click="confirmDelete({{ $row->id }}, '{{ addslashes($astap?->nama_barang ?: 'Aset Kemitraan') }}')"
                                    title="Hapus / Batalkan Aset Kemitraan"
                                    class="p-2 rounded-xl bg-slate-800 hover:bg-rose-500 hover:text-white text-slate-400 transition-colors cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="py-12 text-center text-slate-400">
                            <div class="text-3xl mb-2">🤝</div>
                            <p class="text-sm font-bold text-white">Belum Ada Aset Kemitraan Tercatat</p>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Belum ada aset dengan skema KSO, BGS, atau sewa pihak ketiga yang tercatat pada periode ini.
                            </p>
                            <div class="mt-4">
                                <a href="{{ route('astap.create_kemitraan') }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-slate-950 font-bold text-xs shadow-lg shadow-cyan-500/20 transition-all">
                                    <span>+ Catat Aset Kemitraan Baru</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
