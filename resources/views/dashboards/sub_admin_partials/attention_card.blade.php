<!-- KOLOM KANAN (1/3): CARD ASET PERLU PERHATIAN (DENGAN TAMPILAN YANG SUDAH DIPERBAIKI) -->
<div class="bg-slate-900/90 border border-slate-800 rounded-3xl p-5 shadow-xl flex flex-col justify-between">
    <div>
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center space-x-2">
                <span class="text-lg">🛠️</span>
                <h4 class="text-sm font-extrabold text-white">Aset Perlu Perhatian</h4>
            </div>
            <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 border border-amber-500/30 px-2.5 py-0.5 rounded-full"
                  x-text="attentionAssets.length + ' Item'"></span>
        </div>

        <p class="text-xs text-slate-400 mb-3">
            Barang di ruangan Anda yang mengalami kendala atau membutuhkan servis berkala:
        </p>

        <!-- List Aset Rusak / Servis dengan perbaikan tampilan nomor NIBAR panjang & lokasi -->
        <div class="space-y-3">
            <template x-for="item in attentionAssets.slice(0, 5)" :key="item.id">
                <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800 text-xs hover:border-slate-700 transition-all">
                    <div class="flex items-start justify-between gap-2">
                        <p class="font-bold text-white text-xs leading-snug" x-text="item.nama"></p>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold shrink-0"
                            :class="item.status === 'Kurang Baik' || item.status === 'Rusak Ringan' ? 'bg-amber-500/20 text-amber-300 border border-amber-500/30' : (item.status === 'Rusak Berat' || item.status === 'Rusak' ? 'bg-rose-500/20 text-rose-300 border border-rose-500/30' : 'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30')"
                            x-text="item.status">
                        </span>
                    </div>
                    
                    <p class="text-[11px] text-slate-400 mt-1.5 leading-relaxed" x-text="item.catatan"></p>
                    
                    <!-- Container Bawah: NIBAR dan Lokasi Dipisah Secara Rapi & Bebas Tabrakan -->
                    <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-2 text-[10px]">
                        <!-- NIBAR dengan tooltip & truncate -->
                        <div class="flex items-center space-x-1.5 min-w-0 max-w-full">
                            <span class="text-slate-500 shrink-0 font-medium">NIBAR:</span>
                            <span class="font-mono text-emerald-400 font-semibold truncate block" 
                                  :title="item.kode" 
                                  x-text="item.kode"></span>
                        </div>

                        <!-- Lokasi Ruangan -->
                        <div class="flex items-center space-x-1 shrink-0 text-slate-400">
                            <svg class="w-3 h-3 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="truncate max-w-[140px]" :title="item.lokasi" x-text="item.lokasi"></span>
                        </div>
                    </div>
                </div>
            </template>

            <template x-if="attentionAssets.length === 0">
                <div class="p-6 rounded-2xl bg-slate-950 border border-slate-800 text-center text-xs text-slate-400">
                    <span class="text-2xl block mb-1.5">✅</span>
                    <p class="text-emerald-400 font-bold">Seluruh Aset Berstatus Baik</p>
                    <p class="text-[11px] text-slate-500 mt-1">Tidak ada aset rusak yang tercatat di ruangan ini.</p>
                </div>
            </template>
        </div>
    </div>

    <button type="button" 
        onclick="alert('Fitur Riwayat & Log Pemeliharaan sedang dalam tahap pengembangan.')"
        class="mt-4 block text-center w-full py-2.5 rounded-xl bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-slate-300 font-bold text-xs border border-slate-700/60 transition-all shadow-sm cursor-pointer">
        Lihat Seluruh Log Pemeliharaan &rarr;
    </button>
</div>
