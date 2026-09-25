        <!-- Tabel Distribusi ASTAP (Multi-Barang / Transaksi) -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 custom-scrollbar min-h-[520px]" style="max-height: calc(100vh - 200px); overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-h-[480px]">
                    <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">No. Distribusi</th>
                            <th class="px-4 py-3.5 text-left min-w-[260px] bg-slate-950">Rincian Barang yang Didistribusikan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Tujuan Unit / Ruangan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Tgl Pengajuan</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Penerima</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Status</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0" :class="userRole === 'sub_admin' ? 'w-24' : 'min-w-[210px]'" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredDistribusis" :key="item.id">
                            <tr class="group hover:bg-slate-800/40 transition-colors">
                                <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>
                                <td class="px-4 py-4 text-center font-mono font-semibold text-teal-400 whitespace-nowrap" x-text="item.kode"></td>
                                
                                <!-- Kolom Barang: Ringkasan Rapi (Detail Lengkap Dapat Dilihat di Modal Detail / BAST) -->
                                <td class="px-4 py-4">
                                    <div class="space-y-1.5">
                                        <div class="flex items-center space-x-2">
                                            <p class="font-extrabold text-white text-xs" x-text="item.nama"></p>
                                            <template x-if="item.items && item.items.length > 0">
                                                <span class="px-2.5 py-0.5 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30 text-[10px] font-bold shrink-0"
                                                      x-text="item.items.length + ' Jenis Barang'">
                                                </span>
                                            </template>
                                        </div>

                                        <template x-if="item.keterangan">
                                            <p class="text-[11px] text-slate-400 truncate max-w-xs sm:max-w-md" x-text="'Catatan: ' + item.keterangan"></p>
                                        </template>
                                    </div>
                                </td>

                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="font-bold text-slate-200 bg-slate-950 px-2.5 py-1 rounded-lg border border-slate-800" x-text="item.tujuan"></span>
                                </td>
                                <td class="px-4 py-4 text-center font-mono text-slate-300 whitespace-nowrap" x-text="item.tgl"></td>
                                <td class="px-4 py-4 text-center font-semibold text-white whitespace-nowrap" x-text="item.penerima"></td>
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <span class="inline-flex items-center justify-center px-3 py-1 rounded-xl text-[11px] font-bold whitespace-nowrap tracking-wide leading-none border shadow-sm select-none"
                                        :class="{
                                            'bg-emerald-500/15 text-emerald-300 border-emerald-500/30': item.status === 'Telah Diterima' || item.status === 'Diterima',
                                            'bg-cyan-500/15 text-cyan-300 border-cyan-500/30': item.status === 'Dalam Pengiriman' || item.status === 'Dikirim',
                                            'bg-amber-500/15 text-amber-300 border-amber-500/30': item.status === 'Menunggu Konfirmasi' || item.status === 'Pending',
                                            'bg-rose-500/15 text-rose-300 border-rose-500/30': item.status === 'Ditolak',
                                            'bg-slate-500/15 text-slate-300 border-slate-500/30': item.status === 'Draft' || !item.status
                                        }"
                                        x-text="item.status || 'Draft'"></span>
                                </td>
                                <!-- Kolom Aksi — FREEZE STICKY RIGHT -->
                                <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0" :class="userRole === 'sub_admin' ? 'w-24' : 'min-w-[210px]'" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- 1. Tombol Detail Modal -->
                                        <button type="button" @click="openDetail(item)"
                                            title="Lihat Detail Distribusi"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-teal-500/10 hover:bg-teal-500/20 text-teal-300 border border-teal-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            <span>Detail</span>
                                        </button>

                                        <!-- 2. Tombol Ubah Form (Hanya untuk Admin & Master Admin) -->
                                        <a :href="'/distribusi/' + item.id + '/edit'"
                                            x-show="userRole !== 'sub_admin'"
                                            title="Ubah Data Distribusi"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            <span>Ubah</span>
                                        </a>
                                        
                                        <!-- 3. Tombol Hapus (Hanya untuk Admin & Master Admin) -->
                                        <button type="button" @click="deleteDistribusi(item.id)"
                                            x-show="userRole !== 'sub_admin'"
                                            title="Hapus Data Distribusi"
                                            class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 text-rose-300 border border-rose-500/30 font-bold text-xs transition-all shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-rose-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            <span>Hapus</span>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State Baris Penuh Tinggi -->
                        <template x-if="filteredDistribusis.length === 0">
                            <tr>
                                <td colspan="8" class="text-center align-middle py-28 text-slate-400">
                                    <div class="flex flex-col items-center justify-center space-y-2 py-4">
                                        <p class="text-sm font-semibold text-slate-300">
                                            {{ $isSubAdmin ? 'Belum ada data permohonan pengajuan baru.' : 'Tidak ada data transaksi distribusi yang cocok.' }}
                                        </p>
                                        <p class="text-xs text-slate-500">Coba sesuaikan kata kunci pencarian atau filter status Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
