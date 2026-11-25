<!-- ========================================================================= -->
<!-- TABEL STRUKTUR KODE 108 BMD                                               -->
<!-- ========================================================================= -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-3.5 sm:p-5 mb-6 overflow-hidden">
    <style>
        /* Mobile & Medium (< 1024px): Kolom Aksi Scroll Alami (Tidak Menutupi Data) */
        .col-aksi-kode108-sticky {
            position: static !important;
            box-shadow: none !important;
        }
        .swipe-hint-kode108 {
            display: flex;
        }
        /* Desktop (>= 1024px): Kolom Aksi Tetap Freeze / Sticky di Kanan */
        @media (min-width: 1024px) {
            .col-aksi-kode108-sticky {
                position: sticky !important;
                right: 0 !important;
                box-shadow: -6px 0 12px rgba(0,0,0,0.6) !important;
            }
            th.col-aksi-kode108-sticky {
                top: 0 !important;
                z-index: 30 !important;
                background-color: #0f1d38 !important;
            }
            td.col-aksi-kode108-sticky {
                z-index: 2 !important;
                background-color: #0c172e !important;
            }
            .swipe-hint-kode108 {
                display: none !important;
            }
        }
    </style>

    <!-- Petunjuk Geser Khusus Mobile & Medium (< 1024px) -->
    <div class="swipe-hint-kode108 items-center justify-between px-2 pb-2.5 text-[10.5px] text-slate-400">
        <span class="flex items-center gap-1.5">
            <span class="text-emerald-400">👉</span>
            <span>Geser tabel ke kanan untuk melihat tombol aksi</span>
        </span>
        <span class="font-mono text-slate-500">Tabel Responsif</span>
    </div>

    <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 360px; overflow-y: auto; overflow-x: auto;">
        <table class="w-full text-left text-xs text-slate-300 border-collapse">
            <thead class="shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                <tr class="text-center font-extrabold uppercase tracking-wider text-xs border-b border-slate-800">
                    <th rowspan="2" class="px-4 py-3 bg-slate-950/90 text-slate-300 border-r border-slate-800 w-12 text-center align-middle">
                        NO
                    </th>
                    <th colspan="2" class="px-4 py-3 bg-emerald-950/60 text-emerald-300 border-r border-slate-800">
                        <div class="flex items-center justify-center space-x-2">
                            <span>🏛️</span>
                            <span>JENIS UTAMA</span>
                        </div>
                    </th>
                    <th colspan="2" class="px-4 py-3 bg-amber-950/60 text-amber-300 border-r border-slate-800">
                        <div class="flex items-center justify-center space-x-2">
                            <span>📁</span>
                            <span>SUB RINCIAN OBJEK</span>
                        </div>
                    </th>
                    <th colspan="2" class="px-4 py-3 bg-purple-950/60 text-purple-300 border-r border-slate-800">
                        <div class="flex items-center justify-center space-x-2">
                            <span>🏷️</span>
                            <span>SUB - SUB RINCIAN OBJEK</span>
                        </div>
                    </th>
                    <th rowspan="2" class="col-aksi-kode108-sticky px-3 py-3 bg-blue-950/80 text-blue-300 text-center align-middle whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[190px] w-[190px]">
                        <div class="flex items-center justify-center space-x-1.5 font-extrabold uppercase tracking-wider">
                            <span>⚡</span>
                            <span>AKSI</span>
                        </div>
                    </th>
                </tr>

                <tr class="text-center font-bold uppercase tracking-wider text-[11px] border-b border-slate-800">
                    <th class="px-3 py-2.5 bg-emerald-950/40 text-emerald-400 border-r border-slate-800/80 w-24">
                        JENIS
                    </th>
                    <th class="px-4 py-2.5 bg-emerald-950/40 text-emerald-200 border-r border-slate-800/80 min-w-[180px]">
                        NAMA JENIS
                    </th>

                    <th class="px-3 py-2.5 bg-amber-950/40 text-amber-400 border-r border-slate-800/80 w-36">
                        SUB RINCIAN OBJEK
                    </th>
                    <th class="px-4 py-2.5 bg-amber-950/40 text-amber-200 border-r border-slate-800/80 min-w-[240px]">
                        URAIAN
                    </th>

                    <th class="px-3 py-2.5 bg-purple-950/40 text-purple-400 border-r border-slate-800/80 w-44">
                        SUB - SUB RINCIAN OBJEK
                    </th>
                    <th class="px-4 py-2.5 bg-purple-950/40 text-purple-200 border-r border-slate-800/80 min-w-[260px]">
                        URAIAN
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-800/80">
                @forelse ($kode108List as $index => $item)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="px-4 py-4 text-center font-bold text-slate-400 border-r border-slate-800/80">
                            {{ $kode108List->firstItem() + $index }}
                        </td>

                        <td class="px-3 py-4 text-center font-mono font-bold text-emerald-400 bg-emerald-950/10 border-r border-slate-800/80">
                            {{ $item->jenis }}
                        </td>

                        <td class="px-4 py-4 font-bold text-slate-200 uppercase bg-emerald-950/10 border-r border-slate-800/80">
                            {{ $item->nama_jenis }}
                        </td>

                        <td class="px-3 py-4 text-center font-mono font-bold text-amber-400 bg-amber-950/10 border-r border-slate-800/80">
                            {{ $item->sub_rincian_objek }}
                        </td>

                        <td class="px-4 py-4 font-semibold text-slate-300 uppercase bg-amber-950/10 border-r border-slate-800/80 text-[11px]">
                            {{ $item->uraian_sub_rincian }}
                        </td>

                        <td class="px-3 py-4 text-center font-mono font-bold text-purple-400 bg-purple-950/10 border-r border-slate-800/80">
                            {{ $item->sub_sub_rincian_objek }}
                        </td>

                        <td class="px-4 py-4 font-semibold text-white bg-purple-950/10 border-r border-slate-800/80">
                            <div class="flex items-center justify-between gap-2">
                                <span class="font-bold text-white">{{ $item->uraian_sub_sub_rincian }}</span>
                                @if (($item->astaps_count ?? 0) > 0)
                                    <span class="px-2 py-0.5 rounded-full bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 text-[10px] font-bold font-mono shrink-0 shadow-sm" title="{{ $item->astaps_count }} aset fisik aktif terhubung ke klasifikasi ini">
                                        🔗 {{ $item->astaps_count }} Aset
                                    </span>
                                @endif
                            </div>
                        </td>

                        <!-- Kolom Aksi — FREEZE HANYA DI DESKTOP (>= 1024px), SCROLL ALAMI DI MOBILE & MEDIUM (< 1024px) -->
                        <td class="col-aksi-kode108-sticky px-3 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[190px] w-[190px]">
                            <div class="flex items-center justify-center gap-1.5">
                                <button type="button" @click="openEdit({{ json_encode($item) }})"
                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 text-cyan-300 hover:bg-cyan-500/20 border border-cyan-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    <span>Ubah</span>
                                </button>

                                @if (($item->astaps_count ?? 0) > 0)
                                    <button type="button" @click="askInUseWarning({{ json_encode($item->uraian_sub_sub_rincian . ' (' . $item->sub_sub_rincian_objek . ')') }}, {{ (int)$item->astaps_count }})"
                                        class="px-2.5 py-1.5 rounded-xl bg-slate-800/80 text-slate-400 hover:text-amber-300 border border-slate-700/80 hover:border-amber-500/30 font-semibold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer"
                                        title="Terkunci: Masih digunakan oleh {{ $item->astaps_count }} aset fisik aktif">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                        <span>Terkunci</span>
                                    </button>
                                @else
                                    <form action="{{ route('master.jenis_astap.destroy', $item->id) }}" method="POST" class="inline-block" @submit="confirmDeleteForm($event, {{ json_encode($item->uraian_sub_sub_rincian . ' (' . $item->sub_sub_rincian_objek . ')') }})">
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
                        <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                            <div class="text-3xl mb-2">🔍</div>
                            <p class="font-semibold text-sm">Tidak ada data Kode 108 BMD yang ditemukan.</p>
                            <p class="text-xs text-slate-500 mt-1">Coba sesuaikan kata kunci pencarian atau filter KIB Anda.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Laravel Pagination Links -->
    @if ($kode108List->hasPages())
        <div class="mt-4 px-3.5 sm:px-5 py-3.5 rounded-2xl bg-slate-950/60 border border-slate-800/80">
            {{ $kode108List->onEachSide(1)->links('vendor.pagination.simat-dark') }}
        </div>
    @endif
</div>
