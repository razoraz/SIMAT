        <!-- 6. FRONTEND MODAL: DETAIL ASET RUANGAN (SESUAI KATALOG DATA ASTAP) -->
        <div x-show="showDetailModal" x-cloak @click.self="showDetailModal = false" 
            class="flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto" 
            style="position: fixed !important; top: 0 !important; left: 0 !important; width: 100vw !important; height: 100vh !important; z-index: 99999 !important; background-color: rgba(2, 6, 23, 0.88) !important; backdrop-filter: blur(14px) !important; -webkit-backdrop-filter: blur(14px) !important;">
            <div class="border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-5 my-auto" style="background-color: #0f172a;">
                
                <template x-if="selectedAsset">
                    <div class="space-y-5">
                        <!-- Modal Header -->
                        <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
                            <div class="space-y-1.5 min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                                        :class="{
                                            'bg-amber-500/20 text-amber-300 border-amber-500/30': (selectedAsset.kategori_kib === 'KIB A'),
                                            'bg-cyan-500/20 text-cyan-300 border-cyan-500/30':     (selectedAsset.kategori_kib === 'KIB B' || !selectedAsset.kategori_kib),
                                            'bg-purple-500/20 text-purple-300 border-purple-500/30': (selectedAsset.kategori_kib === 'KIB C'),
                                            'bg-teal-500/20 text-teal-300 border-teal-500/30':     (selectedAsset.kategori_kib === 'KIB D'),
                                            'bg-orange-500/20 text-orange-300 border-orange-500/30': (selectedAsset.kategori_kib === 'KIB E'),
                                            'bg-rose-500/20 text-rose-300 border-rose-500/30':     (selectedAsset.kategori_kib === 'KIB F'),
                                            'bg-indigo-500/20 text-indigo-300 border-indigo-500/30': (selectedAsset.kategori_kib === 'ATB'),
                                            'bg-amber-400/20 text-amber-300 border-amber-400/30': (selectedAsset.kategori_kib === 'EXTRACOM')
                                        }"
                                        x-text="selectedAsset.kategori_kib || selectedAsset.kategori || 'ASTAP'"></span>

                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate max-w-full"
                                        x-text="'Kode: ' + (selectedAsset.kode_barang || selectedAsset.kode_108 || '-')"></span>

                                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-300 font-mono font-bold text-[11px] truncate max-w-full"
                                        x-text="'NIBAR: ' + selectedAsset.nibar"></span>

                                    <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0">
                                        <span class="text-slate-400">📅 Tahun:</span>
                                        <span class="text-cyan-300 font-bold" x-text="selectedAsset.tahun"></span>
                                    </span>
                                </div>
                                <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-white leading-snug break-words mt-1" x-text="selectedAsset.nama"></h3>
                            </div>

                            <!-- Tombol Close -->
                            <button type="button" @click="showDetailModal = false" class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-slate-800 text-slate-400 hover:text-white flex items-center justify-center text-lg font-bold transition-all shrink-0 cursor-pointer">&times;</button>
                        </div>

                        <!-- Top 4 Metric KPI Cards (Persis Data ASTAP) -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3 text-xs">
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">🏷️ Jenis PMDN 108</span>
                                <span class="text-white font-bold text-xs sm:text-sm leading-tight block truncate" :title="selectedAsset.kategori" x-text="selectedAsset.kategori"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📅 Tahun Masuk</span>
                                <span class="text-cyan-300 font-extrabold font-mono text-xs sm:text-sm block" x-text="selectedAsset.tahun"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">⚡ Kondisi Fisik</span>
                                <div class="mt-0.5">
                                    <span class="px-2 py-0.5 rounded-lg text-xs font-bold border inline-block"
                                        :class="{
                                            'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': selectedAsset.kondisi === 'Baik',
                                            'bg-amber-500/20 text-amber-300 border-amber-500/30': (selectedAsset.kondisi === 'Kurang Baik' || selectedAsset.kondisi === 'Rusak Ringan'),
                                            'bg-rose-500/20 text-rose-300 border-rose-500/30': (selectedAsset.kondisi === 'Rusak Berat' || selectedAsset.kondisi === 'Rusak')
                                        }"
                                        x-text="selectedAsset.kondisi"></span>
                                </div>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">💰 Nilai Satuan / Buku</span>
                                <span class="text-emerald-400 font-extrabold font-mono text-xs sm:text-sm block truncate" x-text="selectedAsset.harga_fmt"></span>
                            </div>
                        </div>

                        <!-- 1. SPESIFIKASI TEKNIS RINCI (LANGKAH 3 BELANJA MODAL) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 text-xs">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-cyan-400 flex items-center space-x-1.5">
                                    <span>🔍 Rincian Spesifikasi Teknis Belanja Modal</span>
                                </h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400" x-text="selectedAsset.kategori_kib || 'KIB B'"></span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏷️ Merk / Brand</span>
                                    <span class="text-white font-bold" x-text="selectedAsset.merk || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">⚙️ Type / Model</span>
                                    <span class="text-white font-bold" x-text="selectedAsset.tipe || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🧪 Bahan / Material</span>
                                    <span class="text-white font-bold" x-text="selectedAsset.bahan || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🔢 No. Pabrik / Seri</span>
                                    <span class="text-cyan-300 font-mono font-bold" x-text="selectedAsset.no_pabrik || selectedAsset.no_seri || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🚗 No. Rangka / Mesin</span>
                                    <span class="text-slate-200 font-mono font-semibold" x-text="(selectedAsset.no_rangka || '-') + ' / ' + (selectedAsset.no_mesin || '-')"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Ukuran / Kapasitas</span>
                                    <span class="text-slate-200 font-bold" x-text="selectedAsset.ukuran || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 col-span-full">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏥 Ruang / Unit Pemegang Terpasang</span>
                                    <span class="text-amber-300 font-bold text-sm" x-text="selectedAsset.ruang"></span>
                                </div>
                            </div>
                        </div>

                        <!-- 2. ADMINISTRASI PENGADAAN & DOKUMEN SPK/SP2D (LANGKAH 4) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3 text-xs">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-400 flex items-center space-x-1.5">
                                    <span>📋 Dokumen Administrasi & Pengadaan SIPD</span>
                                </h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400">Riwayat Pengadaan</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏢 Pihak Rekanan / Penyedia</span>
                                    <span class="text-teal-300 font-bold block truncate" x-text="selectedAsset.penyedia || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📜 Nomor & Tgl SPK</span>
                                    <span class="text-slate-200 font-mono font-medium block truncate" x-text="(selectedAsset.spk_nomor || '-') + ' (' + (selectedAsset.spk_tanggal || '-') + ')'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">💳 Nomor & Tgl SP2D</span>
                                    <span class="text-slate-200 font-mono font-medium block truncate" x-text="(selectedAsset.sp2d_nomor || '-') + ' (' + (selectedAsset.sp2d_tanggal || '-') + ')'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📑 Nomor BAST / Bukti Terima</span>
                                    <span class="text-slate-200 font-mono font-medium block truncate" x-text="selectedAsset.bast_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 sm:col-span-2">
                                    <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📍 Alamat Pengiriman / Lokasi Barang</span>
                                    <span class="text-slate-200 font-medium block truncate" x-text="selectedAsset.alamat || 'RSUD dr. H. Koesnandi Bondowoso'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Modal Detail -->
                        <div class="pt-3 border-t border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-3">
                            <div class="flex items-center space-x-2 w-full sm:w-auto">
                                <!-- Tombol Ubah Kondisi Barang -->
                                <button type="button" @click="showDetailModal = false; openEditKondisi(selectedAsset)"
                                    class="px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs shadow-lg shadow-amber-500/20 transition-all flex items-center justify-center space-x-1.5 cursor-pointer w-full sm:w-auto">
                                    <svg class="w-4 h-4 text-slate-950" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    <span>Ubah Kondisi Barang</span>
                                </button>

                                <!-- Tombol Scan QR Publik -->
                                <a :href="'/scan/' + (selectedAsset.nibar || selectedAsset.kode)" target="_blank"
                                    class="px-4 py-2.5 rounded-xl bg-cyan-500/15 hover:bg-cyan-500/25 text-cyan-300 border border-cyan-500/30 font-bold text-xs transition-all flex items-center justify-center space-x-1.5 cursor-pointer w-full sm:w-auto">
                                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
                                    </svg>
                                    <span>Buka QR Scan &rarr;</span>
                                </a>
                            </div>

                            <button type="button" @click="showDetailModal = false"
                                class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-bold text-xs border border-slate-700 transition-all cursor-pointer w-full sm:w-auto text-center">
                                Tutup
                            </button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
