<!-- ========================================================================= -->
<!-- TABEL REKENING BELANJA UNTUK PENGADAAN SIPD                               -->
<!-- ========================================================================= -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-3.5 sm:p-5 mb-6 overflow-hidden">
    <style>
        /* Mobile & Medium (< 1024px): Kolom Aksi Scroll Alami (Tidak Menutupi Data) */
        .col-aksi-rekening-sticky {
            position: static !important;
            box-shadow: none !important;
        }
        .swipe-hint-rekening {
            display: flex;
        }
        /* Desktop (>= 1024px): Kolom Aksi Tetap Freeze / Sticky di Kanan */
        @media (min-width: 1024px) {
            .col-aksi-rekening-sticky {
                position: sticky !important;
                right: 0 !important;
                box-shadow: -6px 0 12px rgba(0,0,0,0.6) !important;
            }
            th.col-aksi-rekening-sticky {
                top: 0 !important;
                z-index: 30 !important;
                background-color: #0f1d38 !important;
            }
            td.col-aksi-rekening-sticky {
                z-index: 2 !important;
                background-color: #0c172e !important;
            }
            .swipe-hint-rekening {
                display: none !important;
            }
        }
    </style>

    <!-- Petunjuk Geser Khusus Mobile & Medium (< 1024px) -->
    <div class="swipe-hint-rekening items-center justify-between px-2 pb-2.5 text-[10.5px] text-slate-400">
        <span class="flex items-center gap-1.5">
            <span class="text-blue-400">👉</span>
            <span>Geser tabel ke kanan untuk melihat tombol aksi</span>
        </span>
        <span class="font-mono text-slate-500">Tabel Responsif</span>
    </div>

    <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 480px; overflow-y: auto; overflow-x: auto;">
        <table class="w-full text-left text-xs text-slate-300 border-collapse">
            
            <!-- 2-Tier Header Sesuai Format -->
            <thead class="shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                <!-- Baris 1: Header Utama -->
                <tr class="text-center font-extrabold uppercase tracking-wider text-xs border-b border-slate-800">
                    <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 w-14 text-center align-middle">
                        NO
                    </th>
                    <th colspan="2" class="px-4 py-3 bg-blue-950/60 text-blue-300 border-r border-slate-800">
                        <div class="flex items-center justify-center space-x-2">
                            <span>💳</span>
                            <span>Rekening Belanja Untuk Pengadaan SIPD</span>
                        </div>
                    </th>
                    <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 text-center align-middle w-52">
                        Kelompok Akun Belanja
                    </th>
                    <th rowspan="2" class="col-aksi-rekening-sticky px-3 py-3 bg-blue-950/80 text-blue-300 text-center align-middle whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[190px] w-[190px]">
                        <div class="flex items-center justify-center space-x-1.5 font-extrabold uppercase tracking-wider">
                            <span>⚡</span>
                            <span>AKSI</span>
                        </div>
                    </th>
                </tr>

                <!-- Baris 2: Sub Kolom (Kode Rek & Nama Belanja Pengadaan) -->
                <tr class="text-center font-bold uppercase tracking-wider text-[11px] border-b border-slate-800">
                    <th class="px-4 py-2.5 bg-blue-950/40 text-blue-400 border-r border-slate-800/80 w-48 text-center">
                        Kode Rek
                    </th>
                    <th class="px-6 py-2.5 bg-blue-950/40 text-blue-200 border-r border-slate-800/80 min-w-[320px] text-left">
                        Nama Belanja Pengadaan
                    </th>
                </tr>
            </thead>

            <!-- Body Tabel -->
            <tbody class="divide-y divide-slate-800/80">
                @forelse ($rekeningList as $index => $item)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <!-- Kolom 1: No -->
                        <td class="px-4 py-4 text-center font-bold text-slate-400 border-r border-slate-800/80">
                            {{ $rekeningList->firstItem() ? ($rekeningList->firstItem() + $index) : ($index + 1) }}
                        </td>

                        <!-- Kolom 2: Kode Rek -->
                        <td class="px-4 py-4 text-center font-mono font-bold text-blue-400 bg-blue-950/10 border-r border-slate-800/80">
                            {{ $item->kode_rek }}
                        </td>

                        <!-- Kolom 3: Nama Belanja Pengadaan -->
                        <td class="px-6 py-4 font-semibold text-white bg-blue-950/10 border-r border-slate-800/80 text-sm">
                            <div>{{ $item->nama_belanja }}</div>
                            @if ($item->astap_belanja_modals_count > 0)
                                <div class="mt-1 flex items-center space-x-1.5">
                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-blue-500/15 text-blue-300 border border-blue-500/30">
                                        <span>🔗</span>
                                        <span>{{ $item->astap_belanja_modals_count }} Aset Terkait</span>
                                    </span>
                                </div>
                            @endif
                        </td>

                        <!-- Kolom 4: Kelompok Akun -->
                        <td class="px-4 py-4 text-center border-r border-slate-800/80 align-middle">
                            <span class="inline-block px-3 py-1.5 rounded-lg text-[11px] font-semibold border leading-tight"
                                :class="'{{ $item->kelompok }}' === '5.2.02' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-500/30' : ('{{ $item->kelompok }}' === '5.2.03' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' : 'bg-blue-500/20 text-blue-300 border-blue-500/30')">
                                {{ $item->kelompok }} - {{ $item->nama_kelompok }}
                            </span>
                        </td>

                        <!-- Kolom 5: Aksi — FREEZE HANYA DI DESKTOP (>= 1024px), SCROLL ALAMI DI MOBILE & MEDIUM (< 1024px) -->
                        <td class="col-aksi-rekening-sticky px-3 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[190px] w-[190px]">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" @click="openEdit({{ json_encode($item) }})"
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Ubah</span>
                                </button>

                                @if ($item->astap_belanja_modals_count > 0)
                                    <button type="button" @click="askInUseWarning({{ json_encode($item->nama_belanja . ' (' . $item->kode_rek . ')') }}, {{ $item->astap_belanja_modals_count }})"
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-800/80 text-amber-300/80 hover:text-amber-300 hover:bg-amber-500/10 border border-slate-700 hover:border-amber-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer"
                                        title="Rekening ini sedang digunakan oleh data aset aktif">
                                        <span>🔒</span>
                                        <span>Terkunci</span>
                                    </button>
                                @else
                                    <form method="POST" action="{{ route('master.rekening_belanja.destroy', $item->id) }}" class="inline-block" @submit="confirmDeleteForm($event, {{ json_encode($item->nama_belanja . ' (' . $item->kode_rek . ')') }})">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-10 text-slate-500">
                            <div class="text-2xl mb-2">📂</div>
                            <div class="font-semibold text-slate-400">Tidak ada data rekening belanja ditemukan.</div>
                            <div class="text-[11px] text-slate-600 mt-0.5">Silakan tambahkan akun rekening baru atau sesuaikan filter pencarian Anda.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination Footer -->
    @if ($rekeningList->hasPages())
        <div class="mt-4 px-3.5 sm:px-5 py-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80">
            {{ $rekeningList->onEachSide(1)->links('vendor.pagination.simat-dark') }}
        </div>
    @endif
</div>
