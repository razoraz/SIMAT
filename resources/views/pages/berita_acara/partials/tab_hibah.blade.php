<!-- ========================================================================= -->
<!-- KONTEN TAB 4: BAST HIBAH ASET BMD (MASUK & KELUAR)                         -->
<!-- ========================================================================= -->
<div x-show="activeTab === 'hibah'" class="space-y-6" x-cloak>
    
    <!-- Toolbar & Filter Hibah -->
    <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl p-5 sm:p-6 shadow-xl">
        <div class="ba-toolbar-wrap flex flex-col lg:flex-row items-center justify-between gap-4">
            
            <div class="ba-toolbar-actions flex flex-wrap items-center gap-3 w-full lg:w-auto">
                <div class="relative flex-1 min-w-0 sm:w-80">
                    <input type="text" x-model="hibahSearch" placeholder="Cari nomor BAST / pihak hibah / nama barang..."
                        class="w-full bg-slate-950 border border-slate-800 rounded-2xl px-4 py-2.5 pl-10 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-emerald-500">
                    <svg class="w-4 h-4 text-emerald-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>

                <!-- Filter Tipe Hibah -->
                <select x-model="hibahTipeFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-emerald-300 font-bold focus:outline-none focus:border-emerald-500">
                    <option value="all">🎁 Semua Alur Hibah</option>
                    <option value="masuk">📥 Hibah Masuk ke RSUD</option>
                    <option value="keluar">📤 Hibah Keluar RSUD</option>
                </select>

                <!-- Filter Status TTD -->
                <select x-model="hibahStatusFilter" class="bg-slate-950 border border-slate-800 rounded-2xl px-3 py-2.5 text-xs text-slate-300 focus:outline-none focus:border-emerald-500">
                    <option value="all">🔍 Semua Status TTD</option>
                    <option value="signed">✍️ Telah Ditandatangani BSrE</option>
                    <option value="unsigned">⏳ Belum Ditandatangani</option>
                </select>
            </div>

            <div class="text-xs text-slate-400 font-medium text-center lg:text-right">
                Total Data: <span class="font-bold text-emerald-400 font-mono" x-text="filteredHibahList.length"></span> Dokumen BAST
            </div>
        </div>
    </div>

    <!-- Tabel Daftar BAST Hibah Aset -->
    <div class="no-print bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
        <div class="swipe-hint-ba hidden items-center justify-center gap-1.5 py-2 px-4 text-[10px] font-bold uppercase tracking-wider text-emerald-300/90 bg-slate-950/60 border border-b-0 border-slate-800/80 rounded-t-2xl">
            <svg class="w-3.5 h-3.5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" /></svg>
            <span>Geser tabel ke samping untuk melihat seluruh kolom</span>
        </div>
        <div class="rounded-2xl border border-slate-800/80 custom-scrollbar" style="max-height: 400px; overflow-y: auto; overflow-x: auto;">
            <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800" style="position: sticky; top: 0; z-index: 20; background-color: #020617;">
                    <tr>
                        <th class="px-4 py-3.5 text-center w-12">No</th>
                        <th class="px-4 py-3.5 text-left">Nomor BAST Hibah</th>
                        <th class="px-4 py-3.5 text-center">Tipe Alur</th>
                        <th class="px-4 py-3.5 text-left">Pihak Pemberi / Penyerah</th>
                        <th class="px-4 py-3.5 text-left">Pihak Penerima</th>
                        <th class="px-4 py-3.5 text-left">Barang &amp; Volume</th>
                        <th class="px-4 py-3.5 text-right">Nilai Aset (Rp)</th>
                        <th class="px-4 py-3.5 text-center">Status TTD BSrE</th>
                        <th class="px-4 py-3.5 text-center whitespace-nowrap">Aksi BAST</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/80">
                    <template x-for="(item, index) in filteredHibahList" :key="item.id">
                        <tr class="hover:bg-slate-800/30 transition-colors">
                            <td class="px-4 py-4 text-center font-bold text-slate-400" x-text="index + 1"></td>
                            <td class="px-4 py-4">
                                <div class="font-mono font-bold text-emerald-400" x-text="item.nomor_bast"></div>
                                <div class="text-[10px] text-slate-400" x-text="item.tgl_bast_format"></div>
                            </td>
                            <td class="px-4 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-xl text-[10px] font-extrabold uppercase border shadow-sm"
                                    :class="item.tipe_hibah === 'masuk' ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : 'bg-rose-500/15 text-rose-300 border-rose-500/30'"
                                    x-text="item.tipe_hibah === 'masuk' ? '📥 Hibah Masuk' : '📤 Hibah Keluar'">
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-white" x-text="item.pemberi"></div>
                                <div class="text-[10px] text-slate-400" x-text="item.pemberi_nama"></div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-emerald-400" x-text="item.penerima_nama"></div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="item.penerima_nip && item.penerima_nip !== '-' ? ('NIP. ' + item.penerima_nip) : item.penerima_jabatan"></div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-bold text-slate-200" x-text="item.nama_barang"></div>
                                <div class="text-[10px] text-slate-400 font-mono" x-text="item.jumlah_volume + ' ' + (item.satuan || 'Unit')"></div>
                            </td>
                            <td class="px-4 py-4 text-right font-mono font-bold text-emerald-300" x-text="item.nilai_aset_format"></td>
                            <td class="px-4 py-4 text-center whitespace-nowrap">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl text-xs font-bold whitespace-nowrap border shadow-sm"
                                      :class="item.signed ? 'bg-emerald-500/15 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/15 text-amber-300 border-amber-500/30'">
                                    <template x-if="item.signed">
                                        <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </template>
                                    <template x-if="!item.signed">
                                        <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </template>
                                    <span x-text="item.signed ? 'Sudah TTD BSrE' : 'Belum TTD'"></span>
                                </span>
                            </td>
                            <td class="px-4 py-4 text-center space-x-1.5 whitespace-nowrap">
                                
                                <!-- Tombol Toggle TTD BSrE -->
                                @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('bast', 'tte'))
                                <button type="button" @click="toggleSignHibah(item)"
                                    :class="item.signed ? 'bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/40' : 'bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold shadow-sm'"
                                    class="px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all inline-flex items-center space-x-1 active:scale-95"
                                    :title="item.signed ? 'Batalkan Tanda Tangan Digital BSrE' : 'Tanda Tangan Digital BSrE'">
                                    <span x-text="item.signed ? '↩️ Batal TTD' : '✍️ TTD BSrE'"></span>
                                </button>
                                @endif

                                <!-- Tombol Cetak / Live Edit -->
                                <button type="button" @click="openPrintHibah(item)"
                                    class="px-3 py-1.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-300 border border-emerald-500/40 font-bold text-xs transition-all inline-flex items-center space-x-1 shadow-sm active:scale-95">
                                    <span>🖨️ Cetak BAST</span>
                                </button>

                            </td>
                        </tr>
                    </template>

                    <!-- Empty State -->
                    <template x-if="filteredHibahList.length === 0">
                        <tr>
                            <td colspan="9" class="text-center py-12 text-slate-500">
                                <div class="flex flex-col items-center justify-center space-y-1.5">
                                    <span class="text-2xl">🎁</span>
                                    <p class="font-semibold text-slate-300 text-xs">Tidak ada data BAST hibah yang ditemukan.</p>
                                    <p class="text-[11px] text-slate-500">Data naskah hibah dapat dikelola melalui menu <span class="text-emerald-400 font-semibold">Master Aset &rarr; Kelola Hibah</span>.</p>
                                </div>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

</div>
