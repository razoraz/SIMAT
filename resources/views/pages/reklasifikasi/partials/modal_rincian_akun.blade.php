<!-- ============================================================
     MODAL RINCIAN ASET REKLASIFIKASI PER AKUN (STANDALONE ALPINE)
     Trigger: window dispatchEvent(new CustomEvent('open-rincian-akun', {detail: rowData}))
     Standalone x-data = TIDAK bergantung parent masterReklasifikasi()
     ============================================================ -->
<div x-data="{
        showModalRincianAkun: false,
        rincianAkunData: {},
        rincianAkunItems: [],
        rincianFilterArah: 'all',
        rincianSearch: '',

        init() {
            window.__rincianAkunModal = this;
            window.addEventListener('open-rincian-akun', (e) => {
                this.loadAndShow(e.detail);
            });
        },

        loadAndShow(row) {
            const rowData = row || {};
            const rowId = rowData.id;
            const allItems = (window.neracaReklasItems && window.neracaReklasItems[rowId])
                ? window.neracaReklasItems[rowId]
                : [];

            this.rincianAkunData = {
                id: rowId,
                nama: rowData.nama_sub_rincian || ('Rekening #' + rowId),
                kodePrefix: rowData.kode_prefix || '',
                kelompokKib: rowData.kelompok_kib || '',
                saldoAwal: rowData.saldo_awal || 0,
                mutasiTambah: rowData.mutasi_tambah || 0,
                mutasiKurang: rowData.mutasi_kurang || 0,
                saldoAkhir: rowData.saldo_akhir || 0,
            };
            this.rincianAkunItems = allItems;
            this.rincianFilterArah = 'all';
            this.rincianSearch = '';
            this.showModalRincianAkun = true;
        },

        get filteredRincianItems() {
            let list = this.rincianAkunItems || [];
            if (this.rincianFilterArah !== 'all') {
                list = list.filter(item => item.arah === this.rincianFilterArah);
            }
            if (this.rincianSearch && this.rincianSearch.trim() !== '') {
                const q = this.rincianSearch.trim().toLowerCase();
                list = list.filter(item => {
                    const nama = (item.nama_barang || '').toLowerCase();
                    const nibar = (item.nibar || '').toLowerCase();
                    const kode = (item.kode_108 || '').toLowerCase();
                    const ba = (item.nomor_ba || '').toLowerCase();
                    const ket = (item.keterangan || '').toLowerCase();
                    const jenis = (item.jenis_label || '').toLowerCase();
                    return nama.includes(q) || nibar.includes(q) || kode.includes(q) || ba.includes(q) || ket.includes(q) || jenis.includes(q);
                });
            }
            return list;
        },
     }"
     @keydown.escape.window="showModalRincianAkun = false">

    <!-- Teleport ke <body> agar berada di atas sidebar (z-50) & topbar (z-30) -->
    <template x-teleport="body">
        <div x-show="showModalRincianAkun"
             style="display:none; z-index: 99999 !important;"
             class="fixed inset-0 z-[99999] flex items-center justify-center p-3 sm:p-5 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <!-- Backdrop Blur -->
            <div class="fixed inset-0 bg-slate-950/85 backdrop-blur-md" @click="showModalRincianAkun = false"></div>

            <!-- Modal Container -->
            <div class="relative w-full max-w-5xl max-h-[88vh] flex flex-col bg-slate-900 border border-slate-700/80 rounded-3xl shadow-2xl overflow-hidden z-10 my-auto"
                 x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95">

            <!-- Header Strip Accent -->
            <div class="h-1.5 w-full bg-gradient-to-r from-emerald-500 via-indigo-500 to-cyan-500 shrink-0"></div>

            <!-- Modal Header -->
            <div class="px-6 py-4 border-b border-slate-800 bg-slate-950/70 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3.5">
                    <div class="p-2.5 rounded-2xl bg-indigo-500/15 text-indigo-400 border border-indigo-500/30 shadow-inner">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h3 class="text-base sm:text-lg font-black text-white" x-text="rincianAkunData.nama || 'Rincian Rekening'"></h3>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30"
                                  x-text="rincianAkunData.kodePrefix || '-'"></span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700"
                                  x-text="rincianAkunData.kelompokKib || '-'"></span>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar fisik barang BMD yang direklasifikasi masuk atau keluar pada akun ini</p>
                    </div>
                </div>

                <button type="button" @click="showModalRincianAkun = false"
                    class="p-2 rounded-xl text-slate-400 hover:text-white hover:bg-slate-800/80 transition-colors cursor-pointer"
                    title="Tutup (Esc)">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- 4 KPI Cards Ringkasan Akun -->
            <div class="p-6 pb-2 grid grid-cols-2 lg:grid-cols-4 gap-3 shrink-0">
                <!-- Saldo Awal -->
                <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 shadow-inner">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Saldo Awal Buku</span>
                    <div class="text-sm sm:text-base font-black font-mono text-slate-200 mt-1"
                         x-text="'Rp ' + Number(rincianAkunData.saldoAwal || 0).toLocaleString('id-ID')"></div>
                    <div class="text-[10px] text-slate-500 mt-0.5">Posisi awal pembukuan</div>
                </div>

                <!-- Mutasi Bertambah (+) -->
                <div class="p-3.5 rounded-2xl bg-emerald-950/25 border border-emerald-500/30 shadow-inner">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400">Mutasi Masuk (+)</span>
                        <span class="text-[9.5px] font-mono px-1.5 rounded bg-emerald-950/60 text-emerald-300 border border-emerald-700/50"
                              x-text="(rincianAkunItems.filter(i => i.arah === 'tambah').length) + ' Aset'"></span>
                    </div>
                    <div class="text-sm sm:text-base font-black font-mono text-emerald-300 mt-1"
                         x-text="'+Rp ' + Number(rincianAkunData.mutasiTambah || 0).toLocaleString('id-ID')"></div>
                    <div class="text-[10px] text-emerald-400/70 mt-0.5">Penambahan / Pindah Masuk</div>
                </div>

                <!-- Mutasi Berkurang (-) -->
                <div class="p-3.5 rounded-2xl bg-rose-950/25 border border-rose-500/30 shadow-inner">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-rose-400">Mutasi Keluar (-)</span>
                        <span class="text-[9.5px] font-mono px-1.5 rounded bg-rose-950/60 text-rose-300 border border-rose-700/50"
                              x-text="(rincianAkunItems.filter(i => i.arah === 'kurang').length) + ' Aset'"></span>
                    </div>
                    <div class="text-sm sm:text-base font-black font-mono text-rose-300 mt-1"
                         x-text="'-Rp ' + Number(rincianAkunData.mutasiKurang || 0).toLocaleString('id-ID')"></div>
                    <div class="text-[10px] text-rose-400/70 mt-0.5">Pengurangan / Pindah Keluar</div>
                </div>

                <!-- Saldo Akhir -->
                <div class="p-3.5 rounded-2xl bg-indigo-950/30 border border-indigo-500/30 shadow-inner">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-300 block">Saldo Akhir Neraca</span>
                    <div class="text-sm sm:text-base font-black font-mono text-indigo-200 mt-1"
                         x-text="'Rp ' + Number(rincianAkunData.saldoAkhir || 0).toLocaleString('id-ID')"></div>
                    <div class="text-[10px] text-indigo-400/70 mt-0.5">Nilai buku terkini akun</div>
                </div>
            </div>

            <!-- Filter & Search Bar Dalam Modal -->
            <div class="px-6 py-3 border-b border-slate-800 bg-slate-950/40 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 shrink-0">
                <!-- Tabs Filter Arah -->
                <div class="flex items-center gap-1.5 p-1 rounded-xl bg-slate-950 border border-slate-800 shrink-0 overflow-x-auto scrollbar-none">
                    <button type="button" @click="rincianFilterArah = 'all'"
                            :class="rincianFilterArah === 'all' ? 'bg-slate-800 text-white font-bold shadow-sm' : 'text-slate-400 hover:text-white'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap cursor-pointer">
                        Semua Aset (<span x-text="rincianAkunItems.length"></span>)
                    </button>
                    <button type="button" @click="rincianFilterArah = 'tambah'"
                            :class="rincianFilterArah === 'tambah' ? 'bg-emerald-950/80 text-emerald-300 border border-emerald-500/40 font-bold shadow-sm' : 'text-slate-400 hover:text-emerald-300'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span>Masuk (+)</span>
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800" x-text="rincianAkunItems.filter(i => i.arah === 'tambah').length"></span>
                    </button>
                    <button type="button" @click="rincianFilterArah = 'kurang'"
                            :class="rincianFilterArah === 'kurang' ? 'bg-rose-950/80 text-rose-300 border border-rose-500/40 font-bold shadow-sm' : 'text-slate-400 hover:text-rose-300'"
                            class="px-3 py-1.5 rounded-lg text-xs transition-all whitespace-nowrap flex items-center gap-1.5 cursor-pointer">
                        <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                        <span>Keluar (-)</span>
                        <span class="text-[10px] font-mono px-1.5 py-0.5 rounded bg-slate-900 border border-slate-800" x-text="rincianAkunItems.filter(i => i.arah === 'kurang').length"></span>
                    </button>
                </div>

                <!-- Search Box -->
                <div class="relative w-full sm:w-64 md:w-72 shrink-0">
                    <input type="text" x-model="rincianSearch"
                           placeholder="Cari nama barang, NIBAR, BA..."
                           class="w-full bg-slate-950 border border-slate-800 text-white rounded-xl pl-9 pr-3 py-1.5 text-xs placeholder:text-slate-500 focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-500 transition-all">
                    <svg class="w-4 h-4 text-slate-500 absolute left-3 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
            </div>

            <!-- Modal Body: Tabel Rincian Aset (Scrollable) -->
            <div class="overflow-y-auto p-6 flex-1 scrollbar-thin scrollbar-thumb-slate-700">
                <template x-if="filteredRincianItems.length === 0">
                    <div class="p-12 text-center space-y-3">
                        <div class="w-16 h-16 rounded-2xl bg-slate-800/80 border border-slate-700 flex items-center justify-center mx-auto text-2xl text-slate-400">
                            🔍
                        </div>
                        <div class="text-white font-bold text-sm">Tidak Ada Rincian Transaksi Reklasifikasi</div>
                        <p class="text-xs text-slate-400 max-w-md mx-auto"
                           x-text="rincianAkunItems.length === 0
                               ? 'Akun ini belum memiliki transaksi mutasi reklasifikasi pada periode pembukuan yang dipilih. Saldo berasal murni dari saldo awal pengadaan.'
                               : 'Tidak ada aset yang cocok dengan filter atau kata kunci pencarian Anda.'">
                        </p>
                    </div>
                </template>

                <template x-if="filteredRincianItems.length > 0">
                    <div class="overflow-x-auto rounded-2xl border border-slate-800 bg-slate-950/60 shadow-xl">
                        <table class="w-full text-left text-xs border-collapse">
                            <thead>
                                <tr class="border-b border-slate-800 bg-slate-950 text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                                    <th class="py-3 px-3 text-center w-10">No</th>
                                    <th class="py-3 px-4">Nama Barang (BMD) & NIBAR</th>
                                    <th class="py-3 px-4">Jenis Reklasifikasi</th>
                                    <th class="py-3 px-4 text-center">Arah Mutasi</th>
                                    <th class="py-3 px-4 text-right">Nilai Reklasifikasi</th>
                                    <th class="py-3 px-4">Dokumen & Tanggal</th>
                                    <th class="py-3 px-4">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60">
                                <template x-for="(item, idx) in filteredRincianItems" :key="item.id + '_' + item.arah + '_' + idx">
                                    <tr class="hover:bg-slate-900/60 transition-colors">
                                        <td class="py-3 px-3 text-center text-slate-400 font-mono text-[11px]" x-text="idx + 1"></td>

                                        <!-- Nama Barang & NIBAR -->
                                        <td class="py-3 px-4 min-w-[200px]">
                                            <div class="font-extrabold text-white flex items-center gap-1.5">
                                                <span x-text="item.nama_barang"></span>
                                                <template x-if="item.is_baru">
                                                    <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/35">
                                                        <span class="w-1 h-1 rounded-full bg-emerald-400 animate-pulse"></span>
                                                        <span>⚡ Baru</span>
                                                    </span>
                                                </template>
                                            </div>
                                            <div class="flex items-center gap-2 mt-1 text-[10.5px]">
                                                <span class="font-mono text-cyan-400" x-text="item.kode_108"></span>
                                                <span class="text-slate-500">·</span>
                                                <span class="text-slate-400 font-mono" x-text="'NIBAR: ' + (item.nibar || '-')"></span>
                                            </div>
                                        </td>

                                        <!-- Jenis Reklasifikasi -->
                                        <td class="py-3 px-4 whitespace-nowrap">
                                            <span class="inline-block px-2 py-0.5 rounded-lg text-[10px] font-bold bg-slate-900 text-slate-300 border border-slate-700"
                                                  x-text="item.jenis_label || item.jenis_reklas"></span>
                                        </td>

                                        <!-- Arah Mutasi (+ / -) -->
                                        <td class="py-3 px-4 text-center whitespace-nowrap">
                                            <template x-if="item.arah === 'tambah'">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-mono font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/35">
                                                    <span>+ Masuk</span>
                                                </span>
                                            </template>
                                            <template x-if="item.arah === 'kurang'">
                                                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10.5px] font-mono font-bold bg-rose-500/20 text-rose-300 border border-rose-500/35">
                                                    <span>- Keluar</span>
                                                </span>
                                            </template>
                                        </td>

                                        <!-- Nilai Mutasi -->
                                        <td class="py-3 px-4 text-right whitespace-nowrap font-mono font-bold text-xs"
                                            :class="item.arah === 'tambah' ? 'text-emerald-400' : 'text-rose-400'">
                                            <span x-text="(item.arah === 'tambah' ? '+' : '-') + item.nilai_formatted"></span>
                                        </td>

                                        <!-- Dokumen & Tanggal -->
                                        <td class="py-3 px-4 min-w-[150px]">
                                            <div class="font-mono text-[11px] text-slate-200" x-text="'📄 ' + (item.nomor_ba || '-')"></div>
                                            <div class="text-[10px] text-slate-400 mt-0.5" x-text="'📅 ' + item.tanggal"></div>
                                        </td>

                                        <!-- Keterangan -->
                                        <td class="py-3 px-4 text-slate-300 text-[11px] max-w-[220px] truncate"
                                            :title="item.keterangan"
                                            x-text="item.keterangan || '-'">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>
            </div>

            <!-- Modal Footer -->
            <div class="px-6 py-3 border-t border-slate-800 bg-slate-950/80 flex items-center justify-between text-xs text-slate-400 shrink-0">
                <div>
                    Menampilkan <strong class="text-white" x-text="filteredRincianItems.length"></strong>
                    dari <strong class="text-white" x-text="rincianAkunItems.length"></strong> aset reklasifikasi
                </div>
                <button type="button" @click="showModalRincianAkun = false"
                        class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white font-bold text-xs transition-colors cursor-pointer">
                    Tutup
                </button>
            </div>
        </div>
    </template>
</div>
