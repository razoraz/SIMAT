        <!-- ========================================================================= -->
        <!-- TABEL KATALOG DATA ASTAP                                                  -->
        <!-- ========================================================================= -->
        <div class="bg-slate-900/90 border border-slate-800 rounded-3xl shadow-xl p-6">
            <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 custom-scrollbar min-h-[520px]" style="max-height: calc(100vh - 200px); overflow-y: auto; overflow-x: auto;">
                <table class="w-full text-left text-xs text-slate-300 relative border-collapse min-h-[480px]">
                    <thead class="text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 shrink-0" style="position: sticky; top: 0; z-index: 5; background-color: #020617;">
                        <tr>
                            <th class="px-4 py-3.5 text-center w-12 whitespace-nowrap bg-slate-950">No</th>
                            <th class="px-4 py-3.5 text-center min-w-[220px] bg-slate-950">Nama Barang / ASTAP</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Tahun Masuk</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Volume / Kuantitas</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Nilai Realisasi</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950">Kondisi</th>
                            <th class="px-4 py-3.5 text-center whitespace-nowrap bg-slate-950 border-l border-slate-800 shrink-0 min-w-[280px] w-[280px]" style="position: sticky; right: 0; z-index: 5; background-color: #020617 !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/80">
                        <template x-for="(item, index) in filteredAstaps" :key="item.id">
                            <tr class="group hover:bg-slate-800/40 transition-colors">
                                <!-- Nomor Urut 1, 2, 3... -->
                                <td class="px-4 py-4 text-center font-bold text-slate-400 whitespace-nowrap" x-text="index + 1"></td>

                                <!-- Nama Barang / ASTAP -->
                                <td class="px-4 py-4">
                                    <div class="font-bold text-white text-sm" x-text="item.nama_barang"></div>
                                    <div class="text-[11px] font-mono text-cyan-400/90 font-medium mt-0.5" x-text="'Kode: ' + item.kode_barang"></div>
                                    <div class="flex items-center space-x-1.5 mt-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-extrabold whitespace-nowrap leading-none shrink-0 shadow-sm"
                                            :class="{
                                                'bg-amber-500/20 text-amber-300 border border-amber-500/40 shadow-amber-500/10': item.category === 'KIB A',
                                                'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 shadow-cyan-500/10': item.category === 'KIB B',
                                                'bg-purple-500/20 text-purple-300 border border-purple-500/40 shadow-purple-500/10': item.category === 'KIB C',
                                                'bg-teal-500/20 text-teal-300 border border-teal-500/40 shadow-teal-500/10': item.category === 'KIB D',
                                                'bg-orange-500/20 text-orange-300 border border-orange-500/40 shadow-orange-500/10': item.category === 'KIB E',
                                                'bg-rose-500/20 text-rose-300 border border-rose-500/40 shadow-rose-500/10': item.category === 'KIB F',
                                                'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40 shadow-indigo-500/10': item.category === 'ATB',
                                                'bg-amber-400/20 text-amber-300 border border-amber-400/40 shadow-amber-400/10': item.category === 'EXTRACOM'
                                            }"
                                            x-text="item.category === 'ATB' ? 'ATB' : (item.category === 'EXTRACOM' ? 'Extracom' : item.category)"></span>
                                        {{-- Badge Hibah — tampil bila sumber_dana === 'hibah' --}}
                                        <template x-if="item.sumber_dana === 'hibah' || item.sumber_dana_raw === 'hibah' || item.jenis_reklas === 'HIBAH_MASUK'">
                                            <span class="inline-flex items-center space-x-1 px-2 py-0.5 rounded-md text-[9px] font-black whitespace-nowrap leading-none shrink-0 bg-gradient-to-r from-amber-500/25 via-amber-400/20 to-orange-500/25 text-amber-300 border border-amber-400/60 shadow-sm shadow-amber-500/20 tracking-wider">
                                                <span>🎁</span><span>HIBAH</span>
                                            </span>
                                        </template>
                                        <span class="text-[11px] text-slate-400 truncate font-medium" x-text="item.jenis_aset_nama"></span>
                                    </div>
                                </td>

                                <!-- Tahun Masuk / Perolehan -->
                                <td class="px-4 py-4 text-center font-mono font-bold text-slate-200 whitespace-nowrap" x-text="item.tahun_perolehan"></td>

                                <!-- Volume / Kuantitas / Luas Aset -->
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-xl bg-slate-950 border border-slate-800 text-teal-300 font-semibold font-mono text-xs whitespace-nowrap">
                                        <span>📏</span>
                                        <span x-text="(item.jumlah_volume || parseInt(item.volume_satuan) || 1) + ' Aset'"></span>
                                    </div>
                                </td>

                                <!-- Nilai Realisasi Anggaran -->
                                <td class="px-4 py-4 text-center font-mono font-extrabold text-emerald-400 text-sm whitespace-nowrap" x-text="item.jumlah_realisasi"></td>

                                <!-- Kondisi Aset Terkini -->
                                <td class="px-4 py-4 text-center whitespace-nowrap">
                                    <div x-data="{ get st() { return getKondisiStats(item); } }">
                                        <!-- Jika hanya 1 unit / semua kondisi sama: tampilkan badge tunggal -->
                                        <template x-if="st.total <= 1 || (st.pct_baik === 100 || st.pct_kb === 100 || st.pct_rr === 100 || st.pct_rb === 100)">
                                            <span class="inline-flex items-center px-3 py-1 rounded-xl text-[11px] font-bold border shadow-sm select-none"
                                                  :class="st.badge_class">
                                                <span class="w-1.5 h-1.5 rounded-full mr-1.5" :class="st.dot_class"></span>
                                                <span x-text="st.kondisi_dominan"></span>
                                            </span>
                                        </template>
                                        <!-- Jika multi kondisi: tampilkan progress bar breakdown -->
                                        <template x-if="st.total > 1 && !(st.pct_baik === 100 || st.pct_kb === 100 || st.pct_rr === 100 || st.pct_rb === 100)">
                                            <div class="min-w-[130px]">
                                                <!-- Mini progress bar gabungan -->
                                                <div class="flex h-2 rounded-full overflow-hidden bg-slate-800 mb-1.5">
                                                    <div x-show="st.pct_baik > 0" class="bg-emerald-400 transition-all" :style="'width:' + st.pct_baik + '%'"></div>
                                                    <div x-show="st.pct_kb > 0"   class="bg-amber-400 transition-all"   :style="'width:' + st.pct_kb + '%'"></div>
                                                    <div x-show="st.pct_rr > 0"   class="bg-orange-400 transition-all"  :style="'width:' + st.pct_rr + '%'"></div>
                                                    <div x-show="st.pct_rb > 0"   class="bg-rose-400 transition-all"    :style="'width:' + st.pct_rb + '%'"></div>
                                                </div>
                                                <!-- Label persentase per kondisi -->
                                                <div class="flex flex-wrap gap-x-2 gap-y-0.5 justify-center">
                                                    <template x-if="st.baik > 0">
                                                        <span class="text-[9.5px] font-bold text-emerald-400" x-text="st.pct_baik + '% Baik'"></span>
                                                    </template>
                                                    <template x-if="st.kurang_baik > 0">
                                                        <span class="text-[9.5px] font-bold text-amber-400" x-text="st.pct_kb + '% K.Baik'"></span>
                                                    </template>
                                                    <template x-if="st.rusak_ringan > 0">
                                                        <span class="text-[9.5px] font-bold text-orange-400" x-text="st.pct_rr + '% R.Ringan'"></span>
                                                    </template>
                                                    <template x-if="st.rusak_berat > 0">
                                                        <span class="text-[9.5px] font-bold text-rose-400" x-text="st.pct_rb + '% R.Berat'"></span>
                                                    </template>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </td>

                                <!-- Aksi (Detail, Reklas, Ubah, Hapus) — FREEZE STICKY RIGHT -->
                                <td class="px-4 py-4 text-center whitespace-nowrap border-l border-slate-800/80 shrink-0 min-w-[280px] w-[280px]" style="position: sticky; right: 0; z-index: 2; background-color: #0f172a !important; box-shadow: -6px 0 12px rgba(0,0,0,0.6);">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- 1. Tombol Detail -->
                                        <button type="button" @click="openDetail(item)"
                                            title="Lihat Detail ASTAP"
                                            class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 hover:border-emerald-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-emerald-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-emerald-400 group-hover/btn:text-white group-hover/btn:scale-110 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span>Detail</span>
                                        </button>

                                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                        <!-- 2. Tombol Reklas (Reklasifikasi Aset) -->
                                        <button type="button" @click="openReklas(item)"
                                            title="Reklasifikasi Aset (Pindah KIB / Ekstrakom / Koreksi)"
                                            class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-indigo-500/10 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 hover:border-indigo-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-indigo-400 group-hover/btn:text-white group-hover/btn:rotate-180 transition-all duration-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                            </svg>
                                            <span>Reklas</span>
                                        </button>

                                        <!-- 3. Tombol Ubah (Form Edit) -->
                                        <a :href="'/astap/' + item.id + '/edit'"
                                            title="Ubah Data ASTAP (Form Lengkap)"
                                            class="group/btn inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-cyan-500/10 hover:bg-cyan-600 text-cyan-300 hover:text-white border border-cyan-500/30 hover:border-cyan-400 font-bold text-xs transition-all duration-200 shadow-sm hover:shadow-lg hover:shadow-cyan-500/40 hover:-translate-y-0.5 active:scale-95 cursor-pointer leading-none">
                                            <svg class="w-3.5 h-3.5 text-cyan-400 group-hover/btn:text-white group-hover/btn:rotate-12 transition-all duration-200 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            <span>Ubah</span>
                                        </a>

                                        <!-- 4. Tombol Hapus -->
                                        <button type="button" @click="deleteAstap(item)"
                                            title="Hapus Data ASTAP"
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
                        </template>

                        <!-- Empty State jika data tidak ditemukan -->
                        <template x-if="filteredAstaps.length === 0">
                            <tr>
                                <td colspan="7" class="text-center align-middle py-28 text-slate-400">
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
