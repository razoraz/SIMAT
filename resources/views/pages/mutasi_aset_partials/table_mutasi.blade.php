        <!-- Tabel Mutasi Aset -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="overflow-x-auto rounded-2xl border border-slate-800/80 bg-slate-950/40">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shadow-sm shrink-0">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">No. BAMB</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Jenis</th>
                            <th class="px-4 py-3.5 text-left min-w-[240px] max-w-[280px] bg-slate-950">Nama Barang / ASTAP</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap min-w-[190px] bg-slate-950">Asal → Tujuan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap min-w-[220px] bg-slate-950">Status Persetujuan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap border-l border-slate-800 shrink-0 min-w-[210px] w-[210px]" style="position: sticky; right: 0; z-index: 20; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredMutasis" :key="item.id">
                            <tr class="group transition-colors" :class="item.is_deleted ? 'bg-red-950/20 hover:bg-red-950/30' : 'hover:bg-slate-800/40'">
                                {{-- No --}}
                                <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>

                                {{-- No. BAMB --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-lg bg-cyan-950/60 border border-cyan-500/30 text-cyan-300 font-mono font-bold text-[11px] shadow-sm inline-block" x-text="item.kode"></span>
                                </td>

                                {{-- Jenis Mutasi — Badge Premium --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[10px] font-extrabold border shadow-sm"
                                        :class="{
                                            'bg-blue-500/10 text-blue-300 border-blue-500/30':   item.jenis === 'Ajukan Mutasi' || item.jenis === 'Pemindahan',
                                            'bg-amber-500/10 text-amber-300 border-amber-500/30': item.jenis === 'Perbaikan',
                                            'bg-teal-500/10 text-teal-300 border-teal-500/30':   item.jenis === 'Minta Mutasi' || item.jenis === 'Minta_Mutasi',
                                            'bg-rose-500/10 text-rose-300 border-rose-500/30':     item.jenis === 'Pengembalian' || item.jenis === 'Penghapusan',
                                            'bg-slate-800 text-slate-400 border-slate-700':       !item.jenis
                                        }">
                                        <span x-text="(item.jenis === 'Ajukan Mutasi' || item.jenis === 'Pemindahan') ? '🔄' : item.jenis === 'Perbaikan' ? '🔧' : (item.jenis === 'Minta Mutasi' || item.jenis === 'Minta_Mutasi') ? '📥' : '↩️'"></span>
                                        <span x-text="item.jenis || '—'"></span>
                                    </span>
                                </td>

                                {{-- Nama Barang & NIBAR (Multi-item badge support) --}}
                                <td class="px-4 py-4 min-w-[240px] max-w-[280px]">
                                    <div class="flex items-start justify-between gap-2">
                                        <p class="font-bold text-white text-xs leading-snug break-words flex-1" x-text="item.nama"></p>
                                        <template x-if="item.item_count > 1">
                                            <span class="px-2 py-0.5 rounded-lg text-[9.5px] font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/30 whitespace-nowrap shrink-0" x-text="item.item_count + ' Barang'"></span>
                                        </template>
                                    </div>
                                    <div class="mt-1 flex items-center space-x-1.5 flex-wrap gap-y-1">
                                        <span class="text-[10px] text-slate-400 font-mono bg-slate-950 px-2 py-0.5 rounded border border-slate-800/80 inline-block max-w-full truncate" x-text="item.kode_barang" :title="item.kode_barang"></span>
                                        <template x-if="item.item_count > 1">
                                            <span class="text-[10px] text-indigo-400 font-semibold" x-text="'(+' + (item.item_count - 1) + ' NIBAR)'"></span>
                                        </template>
                                    </div>
                                </td>

                                {{-- Asal → Tujuan: Pill Card --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-800 shadow-inner">
                                        <span class="text-[11px] text-slate-300 font-medium" x-text="item.asal" :title="item.asal"></span>
                                        <div class="shrink-0 w-4 h-4 rounded-md bg-indigo-500/15 border border-indigo-500/30 flex items-center justify-center">
                                            <svg class="w-2.5 h-2.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                        </div>
                                        <span class="text-[11px] text-indigo-300 font-semibold" x-text="item.tujuan" :title="item.tujuan"></span>
                                    </div>
                                </td>

                                {{-- Status Persetujuan: Badge + Step Indicator (atau Info Terhapus) --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap min-w-[220px]">
                                    {{-- Tampilan jika data dihapus (Label 1) --}}
                                    <template x-if="item.is_deleted">
                                        <div class="flex flex-col items-center gap-1.5 py-1">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-[11px] font-black bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-sm">
                                                <span>🗑️</span>
                                                <span>Terhapus (Label: 1)</span>
                                            </span>
                                            <div class="text-[10px] text-slate-400 text-center leading-tight">
                                                <p class="truncate max-w-[200px]" :title="'Dihapus oleh: ' + (item.deleted_by || '-')">
                                                    <span class="text-slate-500">Oleh:</span> <span class="text-rose-300 font-semibold" x-text="item.deleted_by || '-'"></span>
                                                </p>
                                                <p class="text-slate-400 text-[9.5px] mt-0.5 font-mono" x-text="item.deleted_at || '-'"></p>
                                            </div>
                                        </div>
                                    </template>

                                    {{-- Tampilan jika data aktif (Label 0) --}}
                                    <template x-if="!item.is_deleted">
                                        <div class="flex flex-col items-center gap-2">
                                            {{-- Badge Status --}}
                                            <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none border shadow-sm select-none"
                                                :class="{
                                                    'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': item.status === 'Disetujui Admin (Selesai)',
                                                    'bg-cyan-500/15 text-cyan-300 border-cyan-500/30':         item.status === 'Disetujui 2 Pihak (Menunggu Admin)',
                                                    'bg-amber-500/15 text-amber-300 border-amber-500/30':     item.status === 'Menunggu Persetujuan Penerima',
                                                    'bg-rose-500/15 text-rose-300 border-rose-500/30':         item.status === 'Ditolak'
                                                }">
                                                <span x-text="item.status === 'Disetujui Admin (Selesai)' ? '✓ Selesai'
                                                             : item.status === 'Disetujui 2 Pihak (Menunggu Admin)' ? '⏳ Menunggu Admin'
                                                             : item.status === 'Menunggu Persetujuan Penerima' ? '⏳ Menunggu Penerima'
                                                             : item.status === 'Ditolak' ? '✕ Ditolak'
                                                             : item.status">
                                                </span>
                                            </span>

                                            {{-- Indikator Batas Waktu 24 Jam (Jika Pending) --}}
                                            <template x-if="item.is_pending && item.sisa_waktu">
                                                <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-lg text-[9.5px] font-bold border"
                                                    :class="item.sisa_menit < 180 ? 'bg-rose-500/15 text-rose-300 border-rose-500/30 animate-pulse' : 'bg-amber-500/10 text-amber-300/90 border-amber-500/20'"
                                                    :title="'Batas Waktu Persetujuan 24 Jam: berakhir pada ' + (item.expires_at_formatted || '')">
                                                    <span>⏱️</span>
                                                    <span x-text="'Batas: ' + item.sisa_waktu"></span>
                                                </span>
                                            </template>

                                            {{-- Badge Jika Ditolak Karena Batas 24 Jam --}}
                                            <template x-if="item.status === 'Ditolak' && (item.alasan_penolakan || '').includes('24 jam')">
                                                <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-lg text-[9px] font-extrabold bg-rose-950/40 text-rose-400 border border-rose-800/40">
                                                    <span>⚠️ Batas 24 Jam Lewat</span>
                                                </span>
                                            </template>

                                            {{-- Step Track --}}
                                            <div class="flex items-center gap-0">
                                                <div class="flex flex-col items-center gap-0.5">
                                                    <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                                                        :class="item.persetujuan_pengirim ? 'bg-emerald-500 border-emerald-400 shadow shadow-emerald-500/40' : 'bg-slate-900 border-slate-700'">
                                                        <svg x-show="item.persetujuan_pengirim" class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    </div>
                                                    <span class="text-[8px] font-semibold" :class="item.persetujuan_pengirim ? 'text-emerald-400' : 'text-slate-500'">Kirim</span>
                                                </div>
                                                <div class="w-4 h-0.5 mb-3 transition-all" :class="item.persetujuan_penerima ? 'bg-emerald-500' : 'bg-slate-700'"></div>
                                                <div class="flex flex-col items-center gap-0.5">
                                                    <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                                                        :class="item.persetujuan_penerima ? 'bg-emerald-500 border-emerald-400 shadow shadow-emerald-500/40' : 'bg-slate-900 border-slate-700'">
                                                        <svg x-show="item.persetujuan_penerima" class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    </div>
                                                    <span class="text-[8px] font-semibold" :class="item.persetujuan_penerima ? 'text-emerald-400' : 'text-slate-500'">Terima</span>
                                                </div>
                                                <div class="w-4 h-0.5 mb-3 transition-all" :class="item.persetujuan_admin ? 'bg-emerald-500' : 'bg-slate-700'"></div>
                                                <div class="flex flex-col items-center gap-0.5">
                                                    <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                                                        :class="item.persetujuan_admin ? 'bg-emerald-500 border-emerald-400 shadow shadow-emerald-500/40' : 'bg-slate-900 border-slate-700'">
                                                        <svg x-show="item.persetujuan_admin" class="w-2 h-2 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                                    </div>
                                                    <span class="text-[8px] font-semibold" :class="item.persetujuan_admin ? 'text-emerald-400' : 'text-slate-500'">Admin</span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </td>

                                {{-- Kolom Aksi — FREEZE STICKY RIGHT --}}
                                <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[210px] w-[210px]" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    {{-- Aksi untuk Data Terhapus (Label 1): Detail & Pulihkan --}}
                                    <template x-if="item.is_deleted">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <button type="button" @click="openDetail(item)" title="Lihat Detail & BAMB Mutasi"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail</span>
                                            </button>

                                            <button type="button" @click="restoreMutasi(item)" title="Pulihkan Data Mutasi (Kembalikan Label ke 0)"
                                                class="px-2.5 py-1.5 rounded-xl bg-indigo-500/15 hover:bg-indigo-500/25 text-indigo-300 border border-indigo-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                <span>Pulihkan</span>
                                            </button>
                                        </div>
                                    </template>

                                    {{-- Aksi untuk Data Aktif (Label 0): Detail, Ubah, Hapus --}}
                                    <template x-if="!item.is_deleted">
                                        <div class="flex items-center justify-center gap-1.5">

                                            {{-- 1. Tombol Detail --}}
                                            <button type="button" @click="openDetail(item)" title="Lihat Detail & BAMB Mutasi"
                                                class="px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <span>Detail</span>
                                            </button>

                                            {{-- 2. Tombol Ubah (Terkunci jika status Ditolak) --}}
                                            <template x-if="item.status !== 'Ditolak'">
                                                <a :href="'/mutasi-aset/' + item.id + '/edit'" title="Ubah Data Pengajuan Mutasi"
                                                    class="px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                    <span>Ubah</span>
                                                </a>
                                            </template>
                                            <template x-if="item.status === 'Ditolak'">
                                                <button type="button" disabled title="Terkunci: Pengajuan berstatus Ditolak. Batalkan penolakan terlebih dahulu di menu Detail."
                                                    class="px-2.5 py-1.5 rounded-xl bg-slate-800/80 text-slate-500 border border-slate-700/60 font-bold text-xs inline-flex items-center space-x-1 cursor-not-allowed opacity-60 select-none">
                                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                    <span>Ubah</span>
                                                </button>
                                            </template>

                                            {{-- 3. Tombol Hapus --}}
                                            <button type="button" @click="deleteMutasi(item)" title="Hapus Data Mutasi"
                                                class="px-2.5 py-1.5 rounded-xl bg-rose-500/10 text-rose-300 hover:bg-rose-500/20 border border-rose-500/30 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                <span>Hapus</span>
                                            </button>

                                        </div>
                                    </template>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State Baris Penuh Tinggi --}}
                        <template x-if="filteredMutasis.length === 0">
                            <tr>
                                <td colspan="7" class="text-center align-middle py-28 text-slate-400">
                                    <div class="flex flex-col items-center justify-center space-y-2 py-4">
                                        <p class="text-sm font-semibold text-slate-300">Tidak ada data transaksi mutasi aset yang sesuai kriteria pencarian / filter.</p>
                                        <p class="text-xs text-slate-500">Coba sesuaikan kata kunci pencarian atau filter status Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
