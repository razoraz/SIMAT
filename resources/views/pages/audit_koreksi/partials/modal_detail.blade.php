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
        <div class="p-6 border-b border-slate-800 flex items-center justify-between bg-slate-950/60">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl flex items-center justify-center text-lg border"
                     :class="{
                         'bg-indigo-950/80 border-indigo-500/40 text-indigo-300': detailData.sub_koreksi === 'biasa',
                         'bg-cyan-950/80 border-cyan-500/40 text-cyan-300': detailData.sub_koreksi === 'lkd',
                         'bg-emerald-950/80 border-emerald-500/40 text-emerald-300': detailData.sub_koreksi === 'manset'
                     }">
                    <span x-show="detailData.sub_koreksi === 'biasa'">🏢</span>
                    <span x-show="detailData.sub_koreksi === 'lkd'">⚖️</span>
                    <span x-show="detailData.sub_koreksi === 'manset'">🔄</span>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>Rincian Audit Koreksi Nilai</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-mono font-bold uppercase"
                              :class="{
                                  'bg-indigo-500/20 text-indigo-300 border border-indigo-500/30': detailData.sub_koreksi === 'biasa',
                                  'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': detailData.sub_koreksi === 'lkd',
                                  'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30': detailData.sub_koreksi === 'manset'
                              }"
                              x-text="detailData.sub_koreksi_label || 'KOREKSI NILAI'"></span>
                    </h3>
                    <p class="text-xs text-slate-400">
                        Tanggal Transaksi: <strong class="text-white" x-text="detailData.tanggal_reklas"></strong> 
                        (TW <span x-text="detailData.triwulan"></span> · T.A. <span x-text="detailData.tahun"></span>)
                    </p>
                </div>
            </div>

            <button type="button" @click="closeDetailModal()"
                    class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition-all">
                ✕
            </button>
        </div>

        {{-- Isi Modal --}}
        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">
            {{-- 1. Identitas Aset BMD --}}
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-3">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Identitas Aset Milik Daerah</div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs">
                    <div>
                        <span class="text-slate-400 text-[11px] block">Nama Barang:</span>
                        <strong class="text-white text-sm" x-text="detailData.nama_barang"></strong>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">NIBAR &amp; KIB:</span>
                        <div class="font-mono text-amber-300 font-bold" x-text="detailData.nibar"></div>
                        <div class="text-[10px] text-slate-400 mt-0.5">Kelompok: <span class="text-slate-200" x-text="detailData.kelompok_kib"></span></div>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Kode Akun 108:</span>
                        <div class="font-mono text-slate-300" x-text="detailData.kode_108"></div>
                    </div>
                    <div>
                        <span class="text-slate-400 text-[11px] block">Rekening Belanja Asal:</span>
                        <div class="text-slate-300 truncate" x-text="detailData.rekening_belanja"></div>
                    </div>
                </div>
            </div>

            {{-- 2. Komparasi Finansial & Deviasi --}}
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-3">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Komparasi Perubahan Nilai Buku</div>
                <div class="grid grid-cols-3 gap-3 text-center">
                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Nilai Semula</span>
                        <div class="text-xs sm:text-sm font-mono font-bold text-slate-300" x-text="formatRupiah(detailData.nilai_semula)"></div>
                    </div>
                    <div class="p-3 rounded-xl border"
                         :class="detailData.tipe_koreksi === 'tambah' ? 'bg-emerald-950/40 border-emerald-500/40 text-emerald-300' : 'bg-rose-950/40 border-rose-500/40 text-rose-300'">
                        <span class="text-[10px] uppercase font-bold block mb-1" x-text="detailData.tipe_koreksi === 'tambah' ? 'Penambahan (+)' : 'Pengurangan (-)'"></span>
                        <div class="text-xs sm:text-sm font-mono font-black" x-text="formatRupiah(detailData.nilai_reklas)"></div>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-900 border border-slate-800">
                        <span class="text-[10px] uppercase font-bold text-emerald-400 block mb-1">Nilai Baru</span>
                        <div class="text-xs sm:text-sm font-mono font-bold text-white" x-text="formatRupiah(detailData.nilai_setelah_koreksi)"></div>
                    </div>
                </div>
            </div>

            {{-- 3. Dokumen Dasar & Uraian Temuan --}}
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-2">
                <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Dasar Hukum &amp; Uraian Dokumen</div>
                <div class="text-xs">
                    <span class="text-slate-400 text-[11px] block">Nomor Bukti / Berita Acara / LHP:</span>
                    <strong class="text-white font-mono text-sm" x-text="detailData.nomor_ba_reklas"></strong>
                </div>
                <div class="text-xs pt-1">
                    <span class="text-slate-400 text-[11px] block">Alasan / Catatan Temuan:</span>
                    <p class="text-slate-300 leading-relaxed bg-slate-900 p-3 rounded-xl border border-slate-800 text-xs mt-1" x-text="detailData.alasan_reklas"></p>
                </div>
            </div>

            {{-- 4. Pemetaan Kertas Kerja RMB 21 Kolom BPKAD --}}
            <div class="p-4 rounded-2xl bg-gradient-to-r from-slate-950 to-slate-900 border border-slate-800 flex items-center justify-between gap-4">
                <div>
                    <div class="text-[10px] uppercase font-bold text-slate-400 tracking-wider">Dampak Pada Formulir Resmi RMB BPKAD</div>
                    <div class="text-xs font-bold text-white mt-1" x-text="detailData.dampak_rmb?.label || '-'"></div>
                    <div class="text-[11px] text-slate-400 mt-0.5">Otomatis terhubung pada baris penyeimbang neraca Kertas Kerja Rekon Kasda.</div>
                </div>
                <div class="px-3 py-2 rounded-xl text-xs font-mono font-black shrink-0 border"
                     :class="detailData.tipe_koreksi === 'tambah' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40' : 'bg-rose-500/20 text-rose-300 border-rose-500/40'">
                    <span x-text="detailData.dampak_rmb?.kolom_text || '-'"></span> 
                    (<span x-text="detailData.dampak_rmb?.arah || '+'"></span>)
                </div>
            </div>

            {{-- 5. Info Audit Entry --}}
            <div class="flex items-center justify-between text-[11px] text-slate-500 pt-2 border-t border-slate-800">
                <div>Dicatat oleh: <span class="text-slate-300 font-semibold" x-text="detailData.user_nama"></span></div>
                <div>Waktu Input: <span class="text-slate-400 font-mono" x-text="detailData.created_at"></span></div>
            </div>
        </div>

        {{-- Footer Modal --}}
        <div class="p-4 border-t border-slate-800 bg-slate-950/60 flex items-center justify-between">
            <template x-if="detailData.nibar && detailData.nibar !== '-'">
                <a :href="'{{ route('astap.index') }}?search=' + encodeURIComponent(detailData.nibar)"
                   class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5">
                    <span>🔗</span>
                    <span>Lihat di Data ASTAP</span>
                </a>
            </template>
            <div x-show="!detailData.nibar || detailData.nibar === '-'"></div>

            <button type="button" @click="closeDetailModal()"
                    class="px-5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold transition-all">
                Tutup
            </button>
        </div>
    </div>
</div>
