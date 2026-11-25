{{-- MODAL DETAIL AUDIT KOREKSI NILAI BMD --}}
<div x-show="detailModalOpen" x-cloak
     class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4"
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0">

    <div class="relative w-full max-w-2xl rounded-3xl bg-slate-900 border border-slate-800 shadow-2xl overflow-hidden"
         @click.outside="closeDetailModal()">
        
        {{-- Header Modal --}}
        <div class="p-4 sm:p-6 border-b border-slate-800 flex items-center justify-between gap-3 bg-slate-950/80">
            <div class="flex items-center gap-3 min-w-0">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg border shadow-inner shrink-0"
                     :class="{
                         'bg-indigo-950/80 border-indigo-500/40 text-indigo-300': detailData.sub_koreksi === 'biasa',
                         'bg-cyan-950/80 border-cyan-500/40 text-cyan-300': detailData.sub_koreksi === 'lkd',
                         'bg-emerald-950/80 border-emerald-500/40 text-emerald-300': detailData.sub_koreksi === 'manset'
                     }">
                    {{-- SVGs based on sub_koreksi --}}
                    <template x-if="detailData.sub_koreksi === 'biasa'">
                        <svg class="w-5 h-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </template>
                    <template x-if="detailData.sub_koreksi === 'lkd'">
                        <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3" />
                        </svg>
                    </template>
                    <template x-if="detailData.sub_koreksi === 'manset'">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </template>
                </div>
                <div class="min-w-0">
                    <h3 class="text-base font-bold text-white flex items-center gap-2 flex-wrap">
                        <span>Rincian Audit Koreksi Nilai</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase"
                              :class="{
                                  'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30': detailData.sub_koreksi === 'biasa',
                                  'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': detailData.sub_koreksi === 'lkd',
                                  'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': detailData.sub_koreksi === 'manset'
                              }"
                              x-text="detailData.sub_koreksi_label || 'KOREKSI NILAI'"></span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">
                        Tanggal Transaksi: <strong class="text-white" x-text="detailData.tanggal_reklas"></strong> 
                        (TW <span x-text="detailData.triwulan"></span> · T.A. <span x-text="detailData.tahun"></span>)
                    </p>
                </div>
            </div>

            <button type="button" @click="closeDetailModal()"
                    class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all cursor-pointer shrink-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        {{-- Isi Modal --}}
        <div class="p-4 sm:p-6 space-y-4 sm:space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
            {{-- 1. Identitas Aset BMD --}}
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-3.5">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5 flex-wrap border-b border-slate-800/80 pb-2.5">
                    <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                    </svg>
                    <span>Identitas Aset Milik Daerah</span>
                    <template x-if="detailData.astap_id">
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono font-bold bg-cyan-950/80 text-cyan-300 border border-cyan-800/60" x-text="'#ASTAP-' + detailData.astap_id"></span>
                    </template>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                    {{-- Nama Barang --}}
                    <div>
                        <span class="text-slate-400 text-[11px] block uppercase tracking-wider font-semibold">Nama Barang:</span>
                        <strong class="text-white text-sm block mt-0.5" x-text="detailData.nama_barang"></strong>
                    </div>

                    {{-- Kelompok KIB Resmi --}}
                    <div>
                        <span class="text-slate-400 text-[11px] block uppercase tracking-wider font-semibold">Kelompok KIB:</span>
                        <div class="mt-1">
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-extrabold tracking-wide uppercase leading-none shadow-sm"
                                  :class="{
                                      'bg-amber-500/20 text-amber-300 border border-amber-500/40': detailData.kelompok_kib === 'KIB A',
                                      'bg-cyan-500/20 text-cyan-300 border border-cyan-500/40': detailData.kelompok_kib === 'KIB B',
                                      'bg-purple-500/20 text-purple-300 border border-purple-500/40': detailData.kelompok_kib === 'KIB C',
                                      'bg-teal-500/20 text-teal-300 border border-teal-500/40': detailData.kelompok_kib === 'KIB D',
                                      'bg-orange-500/20 text-orange-300 border border-orange-500/40': detailData.kelompok_kib === 'KIB E',
                                      'bg-rose-500/20 text-rose-300 border border-rose-500/40': detailData.kelompok_kib === 'KIB F',
                                      'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40': !['KIB A','KIB B','KIB C','KIB D','KIB E','KIB F'].includes(detailData.kelompok_kib)
                                  }"
                                  x-text="detailData.kelompok_kib || 'KIB B'">
                            </span>
                        </div>
                    </div>

                    {{-- NIBAR 45-Digit --}}
                    <div>
                        <span class="text-slate-400 text-[11px] block uppercase tracking-wider font-semibold">Nomor Induk Barang (NIBAR):</span>
                        <template x-if="detailData.nibar && detailData.nibar !== '-'">
                            <div class="font-mono text-amber-300 font-bold text-xs mt-1 break-all select-all tracking-wide" x-text="detailData.nibar"></div>
                        </template>
                        <template x-if="!detailData.nibar || detailData.nibar === '-'">
                            <span class="inline-block mt-1 text-[11px] text-slate-500 italic">Belum Teregistrasi NIBAR</span>
                        </template>
                    </div>

                    {{-- Kodefikasi Akun 108 --}}
                    <div>
                        <span class="text-slate-400 text-[11px] block uppercase tracking-wider font-semibold">Kode Akun 108:</span>
                        <template x-if="detailData.kode_108 && detailData.kode_108 !== '-'">
                            <div class="font-mono text-slate-200 font-bold text-xs mt-1 tracking-wider" x-text="detailData.kode_108"></div>
                        </template>
                        <template x-if="!detailData.kode_108 || detailData.kode_108 === '-'">
                            <span class="inline-block mt-1 text-[11px] text-slate-500 italic">Belum Ditentukan (Non-108)</span>
                        </template>
                    </div>

                    {{-- Sumber Dana & Tahun Perolehan --}}
                    <div>
                        <span class="text-slate-400 text-[11px] block uppercase tracking-wider font-semibold">Tahun &amp; Sumber Dana:</span>
                        <div class="text-slate-200 font-medium mt-0.5 flex items-center gap-1.5 flex-wrap">
                            <span class="text-amber-300 font-bold" x-text="detailData.astap_tahun ? 'T.A. ' + detailData.astap_tahun : '-'"></span>
                            <span class="text-slate-500">·</span>
                            <span class="text-slate-300 text-xs" x-text="detailData.astap_sumber_dana || 'Belanja Modal'"></span>
                        </div>
                    </div>

                    {{-- Ruang / Unit Pemegang --}}
                    <div>
                        <span class="text-slate-400 text-[11px] block uppercase tracking-wider font-semibold">Unit / Ruangan Pemegang:</span>
                        <div class="text-slate-200 font-medium mt-0.5 truncate" x-text="detailData.astap_unit || '-'"></div>
                    </div>

                    {{-- Rekening Belanja --}}
                    <div class="sm:col-span-2">
                        <span class="text-slate-400 text-[11px] block uppercase tracking-wider font-semibold">Rekening Belanja Asal:</span>
                        <div class="text-slate-300 font-medium mt-0.5 truncate" x-text="detailData.rekening_belanja || '-'"></div>
                    </div>
                </div>
            </div>

            {{-- 2. Komparasi Finansial & Deviasi --}}
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-3">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span>Komparasi Perubahan Nilai Buku</span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 sm:gap-3 text-center">
                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Nilai Semula</span>
                        <div class="text-xs sm:text-sm font-mono font-bold text-slate-300" x-text="formatRupiah(detailData.nilai_semula)"></div>
                    </div>
                    <div class="p-3 rounded-xl border"
                         :class="detailData.tipe_koreksi === 'tambah' ? 'bg-emerald-950/40 border-emerald-500/40 text-emerald-300' : 'bg-rose-950/40 border-rose-500/40 text-rose-300'">
                        <span class="text-[10px] uppercase font-bold block mb-1" x-text="detailData.tipe_koreksi === 'tambah' ? 'Penambahan (+)' : 'Pengurangan (-)'"></span>
                        <div class="text-xs sm:text-sm font-mono font-black" x-text="(detailData.tipe_koreksi === 'tambah' ? '+' : '-') + formatRupiah(detailData.nilai_reklas)"></div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 block mb-1">Nilai Baru</span>
                        <div class="text-xs sm:text-sm font-mono font-bold text-white" x-text="formatRupiah(detailData.nilai_setelah_koreksi)"></div>
                    </div>
                </div>
            </div>

            {{-- 3. Dokumen Dasar & Uraian Temuan --}}
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>Dasar Hukum &amp; Uraian Dokumen</span>
                </div>
                <div class="text-xs">
                    <span class="text-slate-400 text-[11px] block">Nomor Bukti / Berita Acara / LHP:</span>
                    <strong class="text-white font-mono text-sm break-words" x-text="detailData.nomor_ba_reklas || '-'"></strong>
                </div>
                <div class="text-xs pt-1">
                    <span class="text-slate-400 text-[11px] block">Alasan / Catatan Temuan:</span>
                    <p class="text-slate-300 leading-relaxed bg-slate-900/90 p-3 rounded-xl border border-slate-800 text-xs mt-1" x-text="detailData.alasan_reklas || detailData.keterangan || '-'"></p>
                </div>
            </div>

            {{-- 4. Info Audit Entry --}}
            <div class="flex items-center justify-between flex-wrap gap-2 text-[11px] text-slate-500 pt-2 border-t border-slate-800">
                <div>Dicatat oleh: <span class="text-slate-300 font-semibold" x-text="detailData.user_nama || 'Administrator'"></span></div>
                <div>Waktu Input: <span class="text-slate-400 font-mono" x-text="detailData.created_at || '-'"></span></div>
            </div>
        </div>

        {{-- Footer Modal --}}
        <div class="p-4 border-t border-slate-800 bg-slate-950/80 flex items-center justify-center sm:justify-between flex-wrap gap-2.5">
            <template x-if="detailData.astap_id">
                <a :href="detailData.astap_url || ('{{ route('astap.index') }}?open_detail=' + detailData.astap_id)"
                   target="_blank"
                   class="px-4 py-2 rounded-xl bg-cyan-950/40 hover:bg-cyan-900/60 active:bg-cyan-950 border border-cyan-500/30 hover:border-cyan-400/60 text-cyan-200 hover:text-white text-xs font-bold transition-all flex items-center gap-2 cursor-pointer shadow-sm group">
                    <svg class="w-4 h-4 text-cyan-400 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                    <span>Buka Data ASTAP</span>
                    <span class="text-[10px] text-cyan-400/80 font-mono" x-text="'(#ASTAP-' + detailData.astap_id + ')'"></span>
                </a>
            </template>
            <div x-show="!detailData.astap_id"></div>

            <button type="button" @click="closeDetailModal()"
                    class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 active:bg-slate-900 border border-slate-700 text-white text-xs font-bold transition-all cursor-pointer">
                Tutup
            </button>
        </div>
    </div>
</div>