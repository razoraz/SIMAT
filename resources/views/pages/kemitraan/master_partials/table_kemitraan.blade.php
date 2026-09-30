<!-- ========================================================================= -->
<!-- TABEL DATA MASTER ASET KEMITRAAN PIHAK KETIGA (AKUN 1.5.2)                -->
<!-- ========================================================================= -->
<div class="rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl overflow-hidden">
    <div class="p-5 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                <span>📋 Daftar Aset Kemitraan (Sewa, KSP, BGS/BSG, KSPI)</span>
            </h2>
            <p class="text-xs text-slate-400 mt-0.5">
                Total {{ count($kemitraanRecords ?? []) }} data aset kerja sama tercatat dalam sistem SIMAT-RK.
            </p>
        </div>

        <span class="text-[11px] font-mono font-bold text-cyan-400 bg-cyan-500/10 px-3 py-1 rounded-xl border border-cyan-500/30">
            Akun 1.5.2 Aset Kemitraan
        </span>
    </div>

    <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 custom-scrollbar" style="max-height: 480px; overflow-y: auto; overflow-x: auto;">
        <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
            <thead class="text-slate-400 font-extrabold uppercase text-[10px] tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                <tr>
                    <th class="py-3.5 px-4 w-12 text-center bg-slate-950 whitespace-nowrap">No</th>
                    <th class="py-3.5 px-4 min-w-[200px] bg-slate-950">Dokumen PKS &amp; Rekanan</th>
                    <th class="py-3.5 px-4 min-w-[220px] bg-slate-950">Identitas Barang (Akun 108)</th>
                    <th class="py-3.5 px-4 min-w-[135px] text-center bg-slate-950 whitespace-nowrap">Kondisi</th>
                    <th class="py-3.5 px-4 min-w-[140px] text-right bg-slate-950 whitespace-nowrap">Total Nilai (Rp)</th>
                    <th class="py-3.5 px-4 min-w-[180px] bg-slate-950">Masa Konsesi / Kerjasama</th>
                    <th class="py-3.5 px-4 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0 min-w-[280px] w-[280px]" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
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
                                    {{ $row->skema_kemitraan === 'KSO' || $row->skema_kemitraan === 'KSPI' ? 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30' : '' }}
                                    {{ $row->skema_kemitraan === 'BGS' || $row->skema_kemitraan === 'BSG' || $row->skema_kemitraan === 'BGS/BSG' ? 'bg-blue-500/20 text-blue-300 border border-blue-500/30' : '' }}
                                    {{ $row->skema_kemitraan === 'Sewa' ? 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30' : '' }}
                                    {{ $row->skema_kemitraan === 'KSP' ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : '' }}
                                ">
                                    {{ $row->skema_kemitraan ?: 'Sewa' }}
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
                                <span>Vol: <strong class="text-slate-200">{{ $row->jumlah_volume }} {{ $row->satuan }}</strong></span>
                            </div>
                        </td>

                        <!-- 4. Kondisi Aset (Persentase 3 Kondisi: Baik, Kurang Baik, Rusak Berat) -->
                        <td class="py-4 px-4 text-center whitespace-nowrap">
                            @php
                                $regs = $astap?->registers ?? collect();
                                $totalReg = $regs->count() ?: (int)($row->jumlah_volume ?: 1);

                                if ($regs->isEmpty()) {
                                    $kDefault = $firstReg?->kondisi ?: 'Baik';
                                    $baik = ($kDefault === 'Baik' || $kDefault === 'B') ? $totalReg : 0;
                                    $kb   = ($kDefault === 'Kurang Baik' || $kDefault === 'KB' || $kDefault === 'Rusak Ringan' || $kDefault === 'RR') ? $totalReg : 0;
                                    $rb   = ($kDefault === 'Rusak Berat' || $kDefault === 'RB' || $kDefault === 'Rusak') ? $totalReg : 0;
                                } else {
                                    $baik = $regs->filter(fn($r) => in_array($r->kondisi, ['Baik', 'B']))->count();
                                    $kb   = $regs->filter(fn($r) => in_array($r->kondisi, ['Kurang Baik', 'KB', 'Rusak Ringan', 'RR']))->count();
                                    $rb   = $regs->filter(fn($r) => in_array($r->kondisi, ['Rusak Berat', 'RB', 'Rusak']))->count();
                                }

                                $pctBaik = $totalReg > 0 ? round(($baik / $totalReg) * 100) : 0;
                                $pctKb   = $totalReg > 0 ? round(($kb / $totalReg) * 100) : 0;
                                $pctRb   = $totalReg > 0 ? round(($rb / $totalReg) * 100) : 0;

                                $isSingle = ($baik === $totalReg) || ($kb === $totalReg) || ($rb === $totalReg);
                            @endphp

                            @if($isSingle)
                                @if($baik === $totalReg)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 mr-1.5 animate-pulse"></span>
                                        100% Baik
                                    </span>
                                @elseif($kb === $totalReg)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400 mr-1.5"></span>
                                        100% K.Baik
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400 mr-1.5"></span>
                                        100% R.Berat
                                    </span>
                                @endif
                            @else
                                <div class="min-w-[125px] max-w-[150px] mx-auto">
                                    <!-- Mini progress bar gabungan (3 Kondisi) -->
                                    <div class="flex h-2 rounded-full overflow-hidden bg-slate-800 mb-1 border border-slate-700/50">
                                        @if($pctBaik > 0)
                                            <div class="bg-emerald-400 transition-all" style="width: {{ $pctBaik }}%" title="{{ $pctBaik }}% Baik ({{ $baik }}/{{ $totalReg }})"></div>
                                        @endif
                                        @if($pctKb > 0)
                                            <div class="bg-amber-400 transition-all" style="width: {{ $pctKb }}%" title="{{ $pctKb }}% Kurang Baik ({{ $kb }}/{{ $totalReg }})"></div>
                                        @endif
                                        @if($pctRb > 0)
                                            <div class="bg-rose-400 transition-all" style="width: {{ $pctRb }}%" title="{{ $pctRb }}% Rusak Berat ({{ $rb }}/{{ $totalReg }})"></div>
                                        @endif
                                    </div>
                                    <!-- Label persentase per kondisi -->
                                    <div class="flex flex-wrap gap-x-2 gap-y-0.5 justify-center text-[9px] font-bold">
                                        @if($baik > 0)
                                            <span class="text-emerald-400">{{ $pctBaik }}% Baik</span>
                                        @endif
                                        @if($kb > 0)
                                            <span class="text-amber-400">{{ $pctKb }}% KB</span>
                                        @endif
                                        @if($rb > 0)
                                            <span class="text-rose-400">{{ $pctRb }}% RB</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </td>

                        <!-- 4. Total Nilai Aset -->
                        <td class="py-4 px-4 text-right">
                            <div class="font-mono text-xs font-black text-cyan-300">
                                Rp {{ number_format($row->nilai_aset, 0, ',', '.') }}
                            </div>
                        </td>

                        <!-- 5. Masa Konsesi / Kerjasama & Status -->
                        <td class="py-4 px-4">
                            @if($row->tanggal_mulai || $row->tanggal_selesai)
                                <div class="text-[11px] font-medium text-slate-300">
                                    {{ $row->tanggal_mulai ? \Carbon\Carbon::parse($row->tanggal_mulai)->format('d/m/Y') : '?' }} 
                                    s.d. 
                                    {{ $row->tanggal_selesai ? \Carbon\Carbon::parse($row->tanggal_selesai)->format('d/m/Y') : '?' }}
                                </div>
                                @if($row->status_konsesi === 'Selesai / Reklasifikasi')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-blue-400 bg-blue-500/10 px-2 py-0.5 rounded-md mt-1 border border-blue-500/30">
                                        <span>🔄</span> Siap Reklasifikasi
                                    </span>
                                @elseif($row->status_konsesi === 'Dihentikan')
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-md mt-1 border border-rose-500/30">
                                        <span>🛑</span> Dihentikan
                                    </span>
                                @elseif(!is_null($sisaHari))
                                    @if($sisaHari > 60)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded-md mt-1 border border-emerald-500/20">
                                            <span>⏱️</span> Sisa {{ $sisaHari }} hari
                                        </span>
                                    @elseif($sisaHari > 0)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded-md mt-1 border border-amber-500/20">
                                            <span>⚠️</span> Sisa {{ $sisaHari }} hari
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 text-[10px] font-bold text-rose-400 bg-rose-500/10 px-2 py-0.5 rounded-md mt-1 border border-rose-500/30">
                                            <span>🛑</span> Konsesi Berakhir
                                        </span>
                                    @endif
                                @endif
                            @else
                                <span class="text-slate-500 text-[11px] italic">Tanpa batas waktu</span>
                            @endif
                        </td>

                        <!-- 8. Aksi (Detail, Reklas, Ubah, Hapus) — FREEZE STICKY RIGHT -->
                        <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[280px] w-[280px]" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                            <div class="flex items-center justify-center gap-1.5">
                                <!-- 1. Tombol Detail -->
                                <button type="button" @click="openDetail({{ json_encode($row) }}, {{ json_encode($astap) }}, {{ json_encode($firstReg) }})"
                                    title="Lihat Detail Lengkap PKS & Aset Kemitraan"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 hover:border-emerald-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-emerald-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <span>Detail</span>
                                </button>

                                @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                <!-- 2. Tombol Reklas (Reklasifikasi Aset Konsesi Selesai ke Definitif KIB) -->
                                <a href="{{ route('master.reklasifikasi') }}?astap_id={{ $astap?->id }}"
                                    title="Reklasifikasi Aset (Pindah ke Aset Tetap KIB A-E saat Masa Konsesi Berakhir)"
                                    class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                    <svg class="w-3.5 h-3.5 text-indigo-400 group-hover/btn:text-white group-hover/btn:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                    </svg>
                                    <span>Reklas</span>
                                </a>

                                <!-- 3. Tombol Ubah (Form Edit ASTAP Kemitraan) -->
                                <a href="{{ route('astap.edit_kemitraan', ['id' => $astap?->id]) }}"
                                    title="Ubah Data ASTAP Kemitraan (Form Lengkap)"
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
                            <div class="text-3xl mb-2">🤝</div>
                            <p class="text-sm font-bold text-white">Belum Ada Aset Kemitraan Tercatat</p>
                            <p class="text-xs text-slate-500 mt-1 max-w-sm mx-auto">
                                Belum ada aset dengan skema KSO, KSP, atau sewa pihak ketiga yang tercatat pada periode ini.
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
