<!-- Tabel Katalog Mutasi Eksternal (Antar-OPD) -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
    <div class="overflow-x-auto rounded-2xl border border-slate-800/80 bg-slate-950/40">
        <table class="w-full text-left text-xs text-slate-300">
            <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shadow-sm shrink-0">
                <tr>
                    <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                    <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">No. BAST Antar-OPD</th>
                    <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Jenis Transaksi</th>
                    <th class="px-4 py-3.5 text-left min-w-[240px] max-w-[280px] bg-slate-950">Nama Barang / ASTAP</th>
                    <th class="px-4 py-3.5 text-left min-w-[260px] bg-slate-950">Pengirim (RSUD) → Penerima (OPD)</th>
                    <th class="px-4 py-3.5 text-left min-w-[220px] bg-slate-950">Dasar Mutasi / SK</th>
                    <th class="px-4 py-3.5 text-center whitespace-nowrap min-w-[190px] bg-slate-950">Status BAST</th>
                    <th class="px-4 py-3.5 text-center whitespace-nowrap border-l border-slate-800 shrink-0 min-w-[180px] w-[180px]" style="position: sticky; right: 0; z-index: 20; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/80">
                <template x-for="(item, index) in filteredMutasis" :key="item.id">
                    <tr class="group transition-colors hover:bg-slate-800/40">
                        {{-- No --}}
                        <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>

                        {{-- No. BAST Antar-OPD --}}
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <span class="px-2.5 py-1 rounded-lg bg-indigo-950/60 border border-indigo-500/30 text-indigo-300 font-mono font-bold text-[11px] shadow-sm inline-block" x-text="item.kode"></span>
                            <span class="block text-[10px] text-slate-500 font-mono mt-1" x-text="item.tgl"></span>
                        </td>

                        {{-- Jenis Transaksi --}}
                        <td class="px-4 py-4 text-center whitespace-nowrap">
                            <div class="flex flex-col items-center gap-1">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10.5px] font-extrabold border shadow-sm"
                                    :class="{
                                        'bg-indigo-500/15 text-indigo-300 border-indigo-500/30': item.jenis === 'Transfer Antar-OPD',
                                        'bg-sky-500/15 text-sky-300 border-sky-500/30':         item.jenis === 'Peminjaman Antar-OPD',
                                        'bg-violet-500/15 text-violet-300 border-violet-500/30': item.jenis === 'Penyerahan ke BPKAD'
                                    }">
                                    <span x-text="item.jenis === 'Transfer Antar-OPD' ? '🏛️' : item.jenis === 'Peminjaman Antar-OPD' ? '🤝' : '📦'"></span>
                                    <span x-text="item.jenis"></span>
                                </span>
                                <span class="text-[9.5px] text-slate-400 font-semibold" x-text="item.kategori_label"></span>
                            </div>
                        </td>

                        {{-- Nama Barang & NIBAR --}}
                        <td class="px-4 py-4 min-w-[240px] max-w-[280px]">
                            <div class="flex items-start justify-between gap-2">
                                <p class="font-bold text-white text-xs leading-snug break-words flex-1" x-text="item.nama"></p>
                                <template x-if="item.item_count > 1">
                                    <span class="px-2 py-0.5 rounded-lg text-[9.5px] font-extrabold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 whitespace-nowrap shrink-0" x-text="item.item_count + ' Barang'"></span>
                                </template>
                            </div>
                            <div class="mt-1 flex items-center space-x-1.5 flex-wrap gap-y-1">
                                <span class="text-[10px] text-slate-400 font-mono bg-slate-950 px-2 py-0.5 rounded border border-slate-800/80 inline-block max-w-full truncate" x-text="item.kode_barang" :title="item.kode_barang"></span>
                            </div>
                        </td>

                        {{-- Pengirim (RSUD) → Penerima (OPD) --}}
                        <td class="px-4 py-4 min-w-[260px]">
                            <div class="space-y-1.5">
                                <div class="flex items-center space-x-2">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-slate-800 text-slate-400 shrink-0">ASAL</span>
                                    <span class="text-[11px] text-slate-200 font-semibold truncate" x-text="item.ruangan_asal"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 shrink-0">TUJUAN</span>
                                    <span class="text-[11px] text-indigo-300 font-extrabold truncate" x-text="item.opd_tujuan"></span>
                                </div>
                                <div class="text-[10px] text-slate-400 pl-10 truncate" :title="item.pejabat_opd_tujuan + ' (NIP: ' + item.nip_pejabat_opd_tujuan + ')'">
                                    <span>PIC: </span><span class="text-slate-300 font-medium" x-text="item.pejabat_opd_tujuan"></span>
                                </div>
                            </div>
                        </td>

                        {{-- Dasar Mutasi / SK --}}
                        <td class="px-4 py-4 min-w-[220px]">
                            <p class="text-xs font-semibold text-slate-200 leading-snug" x-text="item.nomor_sk_dasar || '-'"></p>
                            <template x-if="item.tgl_estimasi_kembali">
                                <div class="mt-1 inline-flex items-center space-x-1 px-2 py-0.5 rounded-md bg-amber-500/10 border border-amber-500/20 text-amber-300 text-[10px] font-semibold">
                                    <span>⏱️ Est. Kembali:</span>
                                    <span class="font-mono font-bold" x-text="item.tgl_estimasi_kembali"></span>
                                </div>
                            </template>
                        </td>

                        {{-- Status BAST --}}
                        <td class="px-4 py-4 text-center whitespace-nowrap min-w-[190px]">
                            <div class="flex flex-col items-center gap-1.5">
                                <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none border shadow-sm select-none"
                                    :class="{
                                        'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': item.status.includes('Selesai') || item.status.includes('Disahkan'),
                                        'bg-cyan-500/15 text-cyan-300 border-cyan-500/30':         item.status.includes('Peminjaman'),
                                        'bg-amber-500/15 text-amber-300 border-amber-500/30':     item.status.includes('Menunggu')
                                    }">
                                    <span x-text="item.status.includes('Selesai') ? '✓ ' + item.status : item.status.includes('Peminjaman') ? '⏱️ ' + item.status : '⏳ ' + item.status"></span>
                                </span>
                                <template x-if="item.dokumen_lampiran">
                                    <span class="text-[9.5px] text-emerald-400 flex items-center gap-1 font-mono">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Berkas BAST Terlampir
                                    </span>
                                </template>
                            </div>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800" style="position: sticky; right: 0; z-index: 10; background-color: #020617 !important;">
                            <div class="flex items-center justify-center space-x-2">
                                <button type="button" @click="openDetail(item)"
                                    class="px-3 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 font-bold text-xs transition-all flex items-center space-x-1.5 active:scale-95 cursor-pointer"
                                    title="Lihat Detail BAST Antar-OPD">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span>Detail</span>
                                </button>
                                <button type="button" @click="openDetail(item)"
                                    class="px-2.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 text-xs transition-all active:scale-95 cursor-pointer"
                                    title="Cetak Berita Acara (BAST)">
                                    <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>

                <!-- Empty State jika tidak ada data yang cocok dengan pencarian/filter -->
                <template x-if="filteredMutasis.length === 0">
                    <tr>
                        <td colspan="8" class="text-center py-12 text-slate-400">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center text-2xl text-indigo-400">
                                    🏛️
                                </div>
                                <p class="text-sm font-bold text-white">Tidak ada transaksi mutasi eksternal yang cocok</p>
                                <p class="text-xs text-slate-500 max-w-sm">Silakan ubah kata kunci pencarian atau sesuaikan pilihan filter jenis dan status BAST.</p>
                                <button type="button" @click="resetFilters()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs text-indigo-300 font-bold border border-slate-700 transition-all cursor-pointer">
                                    🔄 Reset Filter
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>
</div>
