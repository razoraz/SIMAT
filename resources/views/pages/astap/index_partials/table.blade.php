        <!-- ========================================================================= -->
        <!-- TABEL KATALOG DATA ASTAP (BELANJA MODAL) - SERAGAM DENGAN MUTASI EKSTERNAL -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 custom-scrollbar" style="max-height: calc(100vh - 220px); overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 relative border-collapse">
                    <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                        <tr>
                            <th class="px-3.5 py-3.5 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                            <th class="px-4 py-3.5 text-left min-w-[260px] bg-slate-950">Nama Barang / ASTAP</th>
                            <th class="px-3 py-3.5 text-center whitespace-nowrap w-24 bg-slate-950">Tahun Masuk</th>
                            <th class="px-3 py-3.5 text-center whitespace-nowrap w-32 bg-slate-950">Volume / Kuantitas</th>
                            <th class="px-3 py-3.5 text-center whitespace-nowrap w-36 bg-slate-950">Nilai Realisasi</th>
                            <th class="px-3 py-3.5 text-center whitespace-nowrap w-28 bg-slate-950">Kondisi</th>
                            <th class="px-3.5 py-3.5 text-center whitespace-nowrap w-36 bg-slate-950">Status</th>
                            <th class="px-3 py-3.5 text-center whitespace-nowrap w-[230px] min-w-[230px] bg-slate-950 border-l border-slate-800 shrink-0" style="position: sticky; right: 0; z-index: 20; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredAstaps" :key="item.id">
                            <tr class="group hover:bg-slate-800/40 transition-colors"
                                :class="isItemLocked(item) ? 'bg-amber-950/10 hover:bg-amber-950/20' : 'hover:bg-slate-800/40'">
                                <!-- 1. Nomor Urut 1, 2, 3... -->
                                <td class="px-3 py-3 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>

                                <!-- 2. Nama Barang / ASTAP -->
                                <td class="px-4 py-4">
                                    <div class="font-bold text-white text-sm" x-text="item.nama_barang || item.nama"></div>
                                    <div class="text-[11px] font-mono text-cyan-400/90 font-medium mt-0.5" x-text="'Kode: ' + (item.kode_barang || item.kode_108 || '-')"></div>
                                    <div class="flex items-center space-x-1.5 mt-1 flex-wrap gap-y-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold whitespace-nowrap leading-none shrink-0 shadow-sm"
                                            :class="{
                                                'bg-amber-400/20 text-amber-300 border border-amber-400/40 shadow-amber-400/10': isItemExtracom(item),
                                                'bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-amber-500/10': !isItemExtracom(item) && item.category === 'KIB A',
                                                'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-cyan-500/10': !isItemExtracom(item) && item.category === 'KIB B',
                                                'bg-purple-500/20 text-purple-300 border border-purple-500/40 shadow-purple-500/10': !isItemExtracom(item) && item.category === 'KIB C',
                                                'bg-teal-500/20 text-teal-300 border border-teal-500/40 shadow-teal-500/10': !isItemExtracom(item) && item.category === 'KIB D',
                                                'bg-orange-500/20 text-orange-300 border border-orange-500/40 shadow-orange-500/10': !isItemExtracom(item) && item.category === 'KIB E',
                                                'bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-rose-500/10': !isItemExtracom(item) && item.category === 'KIB F',
                                                'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 shadow-indigo-500/10': !isItemExtracom(item) && item.category === 'ATB',
                                                'bg-violet-500/20 text-violet-300 border border-violet-500/40 shadow-violet-500/10': !isItemExtracom(item) && (item.category === 'ASET LAIN' || item.category === 'ASET LAINNYA')
                                            }"
                                            x-text="isItemExtracom(item) ? 'Extracom' : (item.category === 'ATB' ? 'ATB' : (item.category === 'ASET LAIN' || item.category === 'ASET LAINNYA' ? 'Aset Lain' : (item.category || 'KIB B')))"></span>

                                        {{-- 2. Reklasifikasi --}}
                                        <template x-if="item.has_reklas || item.is_reklas || (item.reklas_count && item.reklas_count > 0) || item.jenis_reklas || !!item.mutasi_keluar">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 bg-indigo-500/25 text-indigo-300 border border-indigo-400/50 shadow-sm shadow-indigo-500/20 tracking-wider"
                                                :title="'Aset ini memiliki riwayat Reklasifikasi' + (item.jenis_reklas ? ' (' + item.jenis_reklas.replace(/_/g, ' ') + ')' : '')">
                                                <svg class="w-2.5 h-2.5 shrink-0 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                </svg>
                                                <span>REKLASIFIKASI</span>
                                            </span>
                                        </template>

                                        {{-- 3. Mutasi Masuk (dari Luar OPD) --}}
                                        <template x-if="!item.is_mutasi_keluar_full && !item.is_mutasi_keluar_partial && !item.mutasi_keluar && item.jenis_reklas !== 'MUTASI_EKSTERNAL' && (item.sumber_dana === 'mutasi_eksternal' || item.sumber_dana === 'pelimpahan' || item.sumber_dana === 'pelimpahan_skpd' || item.sumber_dana === 'mutasi_masuk' || item.sumber_dana === 'mutasi')">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 bg-purple-500/25 text-purple-300 border border-purple-500/50 shadow-sm tracking-wider">
                                                <span>📥</span><span>MUTASI MASUK</span>
                                            </span>
                                        </template>

                                        {{-- 4. Hibah Masuk --}}
                                        <template x-if="item.sumber_dana === 'hibah' || item.sumber_dana_raw === 'hibah' || item.jenis_reklas === 'HIBAH_MASUK'">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 bg-gradient-to-r from-amber-500/25 via-amber-400/20 to-orange-500/25 text-amber-300 border border-amber-400/60 shadow-sm shadow-amber-500/20 tracking-wider">
                                                <span>🎁</span><span>HIBAH</span>
                                            </span>
                                        </template>

                                        {{-- 4.b Hibah Keluar --}}
                                        <template x-if="isHibahKeluar(item)">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 bg-gradient-to-r from-amber-500/25 via-amber-400/20 to-orange-500/25 text-amber-300 border border-amber-400/60 shadow-sm shadow-amber-500/20 tracking-wider"
                                                  title="Aset telah dihibahkan keluar dari RSUD">
                                                <span>🎁</span><span>HIBAH KELUAR</span>
                                            </span>
                                        </template>

                                        {{-- 5. Belanja Barang --}}
                                        <template x-if="item.sumber_dana === 'belanja_barang' || item.sumber_dana === 'belanja_rekening'">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 bg-indigo-500/25 text-indigo-300 border border-indigo-500/50 shadow-sm tracking-wider">
                                                <span>📦</span><span>BELANJA BARANG</span>
                                            </span>
                                        </template>

                                        {{-- 6. Kemitraan --}}
                                        <template x-if="item.sumber_dana === 'kemitraan' || item.sumber_dana_raw === 'kemitraan' || !!item.kemitraan">
                                            <span>
                                                <template x-if="item.is_kemitraan_reklas || item.kemitraan?.status_konsesi === 'Selesai / Reklasifikasi' || item.kemitraan?.status_konsesi === 'Selesai'">
                                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm shadow-emerald-500/10 tracking-wider">
                                                        <span>🏛️</span><span>MILIK RSUD</span>
                                                    </span>
                                                </template>
                                                <template x-if="isKemitraanReturned(item)">
                                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 bg-purple-500/25 text-purple-300 border border-purple-500/50 shadow-sm shadow-purple-500/20 tracking-wider">
                                                        <span>📦</span><span>DIKEMBALIKAN KE MITRA</span>
                                                    </span>
                                                </template>
                                                <template x-if="!(item.is_kemitraan_reklas || item.kemitraan?.status_konsesi === 'Selesai / Reklasifikasi' || item.kemitraan?.status_konsesi === 'Selesai' || isKemitraanReturned(item))">
                                                    <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 shadow-sm tracking-wider"
                                                          :class="(item.is_bmd_dimanfaatkan || item.kemitraan?.tipe_kemitraan === 'dimanfaatkan') 
                                                              ? 'bg-cyan-500/25 text-cyan-300 border border-cyan-500/50 shadow-cyan-500/10' 
                                                              : 'bg-teal-500/25 text-teal-300 border border-teal-500/50 shadow-teal-500/10'">
                                                        <span x-text="(item.is_bmd_dimanfaatkan || item.kemitraan?.tipe_kemitraan === 'dimanfaatkan') ? '🏛️' : '🤝'"></span>
                                                        <span x-text="(item.is_bmd_dimanfaatkan || item.kemitraan?.tipe_kemitraan === 'dimanfaatkan') ? 'BMD DIMANFAATKAN' : 'ASET MITRA'"></span>
                                                    </span>
                                                </template>
                                            </span>
                                        </template>

                                        {{-- 7. Belanja Modal (APBD / BLUD RSUD) --}}
                                        <template x-if="!isHibahKeluar(item) && !item.kemitraan && item.sumber_dana !== 'kemitraan' && item.sumber_dana_raw !== 'kemitraan' && item.sumber_dana !== 'hibah' && item.sumber_dana_raw !== 'hibah' && item.jenis_reklas !== 'HIBAH_MASUK' && item.sumber_dana !== 'belanja_barang' && item.sumber_dana !== 'belanja_rekening' && item.sumber_dana !== 'mutasi_eksternal' && item.sumber_dana !== 'pelimpahan' && item.sumber_dana !== 'pelimpahan_skpd' && item.sumber_dana !== 'mutasi_masuk' && item.sumber_dana !== 'mutasi'">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 bg-emerald-500/25 text-emerald-300 border border-emerald-500/50 shadow-sm tracking-wider">
                                                <span>🏛️</span><span>BELANJA MODAL</span>
                                            </span>
                                        </template>

                                        <span class="text-[11px] text-slate-400 truncate font-medium" x-text="item.jenis_aset_nama"></span>
                                    </div>
                                </td>

                                <!-- 3. Tahun Masuk / Perolehan -->
                                <td class="px-3 py-3 text-center font-mono font-bold text-slate-200 whitespace-nowrap w-24 text-sm">
                                    <div x-text="item.tahun_perolehan || '-'"></div>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5" x-text="item.triwulan || 'TW I'"></div>
                                </td>

                                <!-- 4. Volume / Kuantitas -->
                                <td class="px-3 py-3 text-center whitespace-nowrap w-32">
                                    <div class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-xl bg-slate-950 border border-slate-800 text-teal-300 font-semibold font-mono text-xs whitespace-nowrap">
                                        <span>📏</span>
                                        <span x-text="(item.jumlah_volume || parseInt(item.volume_satuan) || item.item_count || 1) + ' ' + (item.satuan || 'Aset')"></span>
                                    </div>
                                </td>

                                <!-- 5. Nilai Realisasi -->
                                <td class="px-3 py-3 text-center font-mono font-extrabold text-emerald-400 text-sm whitespace-nowrap w-36"
                                    x-text="item.total_realisasi || item.jumlah_realisasi || formatRupiah(item.total_realisasi_num || item.nilai_perolehan || 0)"></td>

                                <!-- 6. Kondisi Aset Terkini -->
                                <td class="px-3 py-3 text-center whitespace-nowrap w-28">
                                    <div x-data="{ get st() { return getKondisiStats(item); } }" class="flex items-center justify-center">
                                        <template x-if="st.total <= 1 || (st.pct_baik === 100 || st.pct_kb === 100 || st.pct_rr === 100 || st.pct_rb === 100)">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-xl text-[11px] font-bold border shadow-sm select-none"
                                                  :class="st.badge_class">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="st.dot_class"></span>
                                                <span x-text="st.kondisi_dominan"></span>
                                            </span>
                                        </template>
                                        <template x-if="st.total > 1 && !(st.pct_baik === 100 || st.pct_kb === 100 || st.pct_rr === 100 || st.pct_rb === 100)">
                                            <div class="min-w-[100px] max-w-[120px]">
                                                <div class="flex h-1.5 rounded-full overflow-hidden bg-slate-800 mb-1">
                                                    <div x-show="st.pct_baik > 0" class="bg-emerald-400" :style="'width:' + st.pct_baik + '%'"></div>
                                                    <div x-show="st.pct_kb > 0"   class="bg-amber-400"   :style="'width:' + st.pct_kb + '%'"></div>
                                                    <div x-show="st.pct_rr > 0"   class="bg-orange-400"  :style="'width:' + st.pct_rr + '%'"></div>
                                                    <div x-show="st.pct_rb > 0"   class="bg-rose-400"    :style="'width:' + st.pct_rb + '%'"></div>
                                                </div>
                                                <div class="flex flex-wrap gap-x-1 justify-center text-[9px] font-bold">
                                                    <template x-if="st.baik > 0"><span class="text-emerald-400" x-text="st.pct_baik + '% B'"></span></template>
                                                    <template x-if="st.kurang_baik > 0"><span class="text-amber-400" x-text="st.pct_kb + '% KB'"></span></template>
                                                    <template x-if="st.rusak_berat > 0"><span class="text-rose-400" x-text="st.pct_rb + '% RB'"></span></template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </td>

                                <!-- 7. Status Aset -->
                                <td class="px-3 py-3 text-center whitespace-nowrap w-36">
                                    <template x-if="isItemLocked(item)">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm select-none"
                                              title="Periode Anggaran ini telah resmi DITUTUP BUKU oleh BPKAD">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            <span>Terkunci (Tutup Buku)</span>
                                        </span>
                                    </template>
                                    <template x-if="!isItemLocked(item) && isMutasiKeluarFull(item)">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 shadow-sm select-none"
                                              title="Seluruh unit aset telah dilimpahkan ke SKPD luar">
                                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400"></span>
                                            <span>Mutasi Keluar</span>
                                        </span>
                                    </template>
                                    <template x-if="!isItemLocked(item) && !isMutasiKeluarFull(item) && isHibahKeluar(item)">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold bg-amber-500/15 text-amber-300 border border-amber-500/30 shadow-sm select-none"
                                              title="Seluruh unit aset telah resmi diserahkan / dihibahkan ke luar RSUD">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            <span>Dihibahkan Keluar</span>
                                        </span>
                                    </template>
                                    <template x-if="!isItemLocked(item) && !isMutasiKeluarFull(item) && !isHibahKeluar(item) && getKemitraanStatusInfo(item)">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold shadow-sm select-none"
                                              :class="getKemitraanStatusInfo(item).badgeClass"
                                              :title="getKemitraanStatusInfo(item).title">
                                            <span class="w-1.5 h-1.5 rounded-full" :class="getKemitraanStatusInfo(item).dotClass"></span>
                                            <span x-text="getKemitraanStatusInfo(item).label"></span>
                                        </span>
                                    </template>
                                    <template x-if="!isItemLocked(item) && !isMutasiKeluarFull(item) && !isHibahKeluar(item) && !getKemitraanStatusInfo(item)">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-xl text-[10.5px] font-extrabold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 shadow-sm select-none">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span>Aktif (Tersedia)</span>
                                        </span>
                                    </template>
                                </td>

                                <!-- 8. Aksi (Detail, Reklas, Ubah, Hapus) — SERAGAM PERSIS DENGAN MUTASI EKSTERNAL -->
                                <td class="px-3 py-3 text-center whitespace-nowrap border-l border-slate-800/80 w-[230px] min-w-[230px] shrink-0" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- 1. Tombol Detail -->
                                        <button type="button" @click="openDetail(item)"
                                            title="Lihat Detail ASTAP"
                                            class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 hover:border-emerald-400 font-bold text-xs transition-all duration-200 shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-emerald-400 group-hover/btn:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Detail</span>
                                        </button>

                                        <!-- 2. Tombol Reklas -->
                                        @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('astap', 'reklas'))
                                        <button type="button" 
                                            @click="if (isKemitraanReturned(item)) { showKemitraanReturnedWarning(item, 'reklas'); } else if (isMutasiKeluarFull(item)) { showMutasiKeluarLockedWarning(item, 'reklas'); } else if (isHibahKeluar(item)) { showHibahKeluarLockedWarning(item, 'reklas'); } else if (!isItemLocked(item)) { showBelumTutupBukuReklasWarning(item); } else { openReklas(item); }"
                                            :title="isKemitraanReturned(item) ? 'Terkunci (Aset Telah Dikembalikan ke Rekanan Mitra)' : (isMutasiKeluarFull(item) ? 'Terkunci (Aset Telah Dimutasi Keluar Penuh ke OPD Lain)' : (isHibahKeluar(item) ? 'Terkunci (Aset Telah Dihibahkan Keluar dari RSUD)' : (!isItemLocked(item) ? 'Terkunci (Aset Belum Ditutup Buku)' : 'Reklasifikasi Aset (Pindah KIB / Ekstrakom / Koreksi)')))"
                                            class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all duration-200 shadow-sm active:scale-95 cursor-pointer leading-none"
                                            :class="!isReklasAllowed(item)
                                                ? 'bg-slate-800/60 text-slate-500 border border-slate-700/60 cursor-pointer hover:bg-slate-800 hover:text-slate-400' 
                                                : 'bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400'">
                                            <!-- Icon Terkunci (Gembok SVG Elegan) -->
                                            <svg x-show="!isReklasAllowed(item)" class="w-3.5 h-3.5 text-slate-500 group-hover/btn:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            <!-- Icon Normal Reklas -->
                                            <svg x-show="isReklasAllowed(item)" class="w-3.5 h-3.5 text-indigo-400 group-hover/btn:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                            <span>Reklas</span>
                                        </button>
                                        @endif

                                        <!-- 3. Tombol Ubah -->
                                        @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('astap', 'edit'))
                                        <a :href="!isEditAllowed(item) ? '#' : ('/astap/' + item.id + '/edit?from=astap')"
                                            @click="if (isKemitraanReturned(item)) { $event.preventDefault(); showKemitraanReturnedWarning(item, 'ubah'); } else if (isMutasiKeluarFull(item)) { $event.preventDefault(); showMutasiKeluarLockedWarning(item, 'ubah'); } else if (isHibahKeluar(item)) { $event.preventDefault(); showHibahKeluarLockedWarning(item, 'ubah'); } else if (isReklasAset(item)) { $event.preventDefault(); showReklasLockedWarning(item); } else if (isItemLocked(item)) { $event.preventDefault(); showSudahTutupBukuEditWarning(item); }"
                                            :title="isKemitraanReturned(item) ? 'Terkunci (Aset Telah Dikembalikan ke Rekanan Mitra)' : (isMutasiKeluarFull(item) ? 'Terkunci (Aset Telah Dimutasi Keluar Penuh ke OPD Lain)' : (isHibahKeluar(item) ? 'Terkunci (Aset Telah Dihibahkan Keluar dari RSUD)' : (isReklasAset(item) ? 'Terkunci (Aset Telah Direklasifikasi - Kelola di Menu Reklasifikasi)' : (isItemLocked(item) ? 'Terkunci (Aset Telah Ditutup Buku)' : 'Ubah Data ASTAP (Form Lengkap)'))))"
                                            class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl font-bold text-xs transition-all duration-200 shadow-sm active:scale-95 cursor-pointer leading-none"
                                            :class="!isEditAllowed(item)
                                                ? 'bg-slate-800/60 text-slate-500 border border-slate-700/60 cursor-pointer hover:bg-slate-800 hover:text-slate-400'
                                                : 'bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400'">
                                            <!-- Icon Terkunci (Gembok SVG Elegan) -->
                                            <svg x-show="!isEditAllowed(item)" class="w-3.5 h-3.5 text-slate-500 group-hover/btn:text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            <!-- Icon Normal Ubah -->
                                            <svg x-show="isEditAllowed(item)" class="w-3.5 h-3.5 text-cyan-400 group-hover/btn:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Ubah</span>
                                        </a>
                                        @endif

                                        <!-- 4. Tombol Hapus (Pindahkan ke Tong Sampah) -->
                                        @if(Auth::user()?->role === 'master_admin' || Auth::user()?->canDo('astap', 'delete'))
                                        <button type="button" @click="deleteAstap(item)"
                                            title="Hapus Data ASTAP (Pindahkan ke Tong Sampah)"
                                            class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 hover:border-rose-400 font-bold text-xs transition-all duration-200 shadow-sm active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-rose-400 group-hover/btn:text-white shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                            <span>Hapus</span>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        </template>

                        <!-- Empty State jika data tidak ditemukan -->
                        <template x-if="filteredAstaps.length === 0">
                            <tr>
                                <td colspan="8" class="text-center align-middle py-28 text-slate-400">
                                    <div class="flex flex-col items-center justify-center space-y-2 py-4">
                                        <p class="text-sm font-semibold text-slate-300">Tidak ada data aset yang cocok dengan filter atau pencarian Anda.</p>
                                        <p class="text-xs text-slate-500">Coba ubah kata kunci atau reset filter klasifikasi/tahun.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
