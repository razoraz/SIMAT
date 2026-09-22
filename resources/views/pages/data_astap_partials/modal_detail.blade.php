           <!-- FRONTEND MODAL: DETAIL ASTAP & RINCIAN REGISTER NIBAR -->
        <div x-show="showDetailModal" x-cloak @click.self="showDetailModal = false" class="fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 md:p-6 overflow-y-auto" style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 50;">
            <div class="border border-slate-800 rounded-3xl max-w-4xl w-full p-4 sm:p-6 md:p-8 shadow-2xl overflow-y-auto max-h-[90vh] space-y-5 my-auto" style="background-color: #0f172a;">
                
                <!-- Modal Header -->
                <div class="flex items-start justify-between pb-4 border-b border-slate-800 gap-4">
                    <div class="space-y-1.5 min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-extrabold border uppercase tracking-wider shrink-0"
                                :class="{
                                    'bg-amber-400/20 text-amber-300 border-amber-400/30': selectedAstapDetail?.category === 'EXTRACOM',
                                    'bg-amber-500/20 text-amber-300 border-amber-500/30': selectedAstapDetail?.category === 'KIB A',
                                    'bg-cyan-500/20 text-cyan-300 border-cyan-500/30':     selectedAstapDetail?.category === 'KIB B',
                                    'bg-purple-500/20 text-purple-300 border-purple-500/30': selectedAstapDetail?.category === 'KIB C',
                                    'bg-teal-500/20 text-teal-300 border-teal-500/30':     selectedAstapDetail?.category === 'KIB D',
                                    'bg-orange-500/20 text-orange-300 border-orange-500/30': selectedAstapDetail?.category === 'KIB E',
                                    'bg-rose-500/20 text-rose-300 border-rose-500/30':     selectedAstapDetail?.category === 'KIB F',
                                    'bg-indigo-500/20 text-indigo-300 border-indigo-500/30': selectedAstapDetail?.category === 'ATB'
                                }"
                                x-text="selectedAstapDetail?.category === 'EXTRACOM' ? '📦 EXTRACOM' : (selectedAstapDetail?.category || 'ASTAP')"></span>

                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-cyan-400 font-mono font-bold text-[11px] truncate max-w-full"
                                x-text="'Kode: ' + (selectedAstapDetail?.kode_barang || '-')"></span>

                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-950 border border-slate-800 text-slate-300 font-mono text-[11px] flex items-center space-x-1.5 shrink-0">
                                <span class="text-slate-400">📅 Tanggal Input:</span>
                                <span class="text-cyan-300 font-bold" x-text="formatTanggalIndo(selectedAstapDetail?.created_at || selectedAstapDetail?.spk_tanggal || (selectedAstapDetail?.tahun_perolehan ? selectedAstapDetail.tahun_perolehan + '-01-01' : null))"></span>
                            </span>

                            <template x-if="selectedAstapDetail">
                                <span class="px-2.5 py-0.5 rounded-lg border text-[11px] font-semibold flex items-center space-x-1.5 shrink-0"
                                      :class="getKondisiStats(selectedAstapDetail).badge_class">
                                    <span class="w-1.5 h-1.5 rounded-full" :class="getKondisiStats(selectedAstapDetail).dot_class"></span>
                                    <span x-text="'Kondisi: ' + getKondisiStats(selectedAstapDetail).text"></span>
                                </span>
                            </template>
                        </div>
                        <h3 class="text-base sm:text-lg md:text-xl font-extrabold text-white leading-snug break-words" x-text="selectedAstapDetail ? selectedAstapDetail.nama_barang : ''"></h3>
                    </div>

                    <!-- Tombol Close -->
                    <button type="button" @click="showDetailModal = false" class="w-8 h-8 rounded-full bg-slate-800/80 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 border border-transparent hover:border-rose-500/30 flex items-center justify-center transition-all shrink-0 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <template x-if="selectedAstapDetail">
                    <div class="space-y-4 text-xs text-slate-300">

                        <!-- Top 4 Metric KPI Cards (Fully Responsive Grid) -->
                        <div class="grid grid-cols-2 lg:grid-cols-4 gap-2.5 sm:gap-3">
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">🏷️ Jenis PMDN 108</span>
                                <span class="text-white font-bold text-xs sm:text-sm leading-tight block truncate" :title="selectedAstapDetail.jenis_aset_nama" x-text="selectedAstapDetail.jenis_aset_nama"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📅 Tahun Masuk</span>
                                <span class="text-cyan-300 font-extrabold font-mono text-xs sm:text-sm block" x-text="selectedAstapDetail.tahun_perolehan"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">📏 Volume / Satuan</span>
                                <span class="text-teal-300 font-extrabold font-mono text-xs sm:text-sm block truncate" x-text="selectedAstapDetail.volume_satuan"></span>
                            </div>
                            <div class="p-3 rounded-2xl bg-slate-950/80 border border-slate-800/80 min-w-0">
                                <span class="text-slate-400 text-[10px] uppercase font-bold block mb-1 truncate">💰 Realisasi Belanja</span>
                                <span class="text-emerald-400 font-extrabold font-mono text-xs sm:text-sm block truncate" x-text="selectedAstapDetail.jumlah_realisasi"></span>
                            </div>
                        </div>

                        <!-- DYNAMIC LANGKAH 3 SPESIFIKASI BERDASARKAN JENIS ASET (KIB A - F, ATB, EXTRACOM) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <h4 class="text-xs font-extrabold uppercase tracking-wider flex items-center space-x-1.5"
                                    :class="{
                                        'text-amber-400': selectedAstapDetail.category === 'KIB A',
                                        'text-cyan-400':  selectedAstapDetail.category === 'KIB B',
                                        'text-purple-400': selectedAstapDetail.category === 'KIB C',
                                        'text-teal-400':  selectedAstapDetail.category === 'KIB D',
                                        'text-orange-400': selectedAstapDetail.category === 'KIB E',
                                        'text-rose-400':  selectedAstapDetail.category === 'KIB F',
                                        'text-indigo-400': selectedAstapDetail.category === 'ATB',
                                        'text-amber-300': selectedAstapDetail.category === 'EXTRACOM'
                                    }">
                                    <span>🔍 Rincian Spesifikasi Belanja Modal (Langkah 3 - <span x-text="selectedAstapDetail.category"></span>)</span>
                                </h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400" x-text="'Spesifikasi Khusus ' + selectedAstapDetail.category"></span>
                            </div>

                            <!-- 1. KIB A (TANAH) -->
                            <template x-if="selectedAstapDetail.category === 'KIB A'">
                                <div class="space-y-2.5">
                                    <!-- Rincian Masing-Masing Bidang Tanah -->
                                    <template x-if="getTanahItemsForDetail(selectedAstapDetail).length > 0">
                                        <div class="space-y-2.5">
                                            <template x-for="(tItem, tIdx) in getTanahItemsForDetail(selectedAstapDetail)" :key="tIdx">
                                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2.5 shadow-sm">
                                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[11px] border border-emerald-500/30">
                                                                🌾 Bidang Tanah #<span x-text="tIdx + 1"></span>
                                                            </span>
                                                            <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-[10.5px] border border-cyan-500/30"
                                                                  x-text="(tItem.tanah_jumlah_bidang || 1) + ' ' + (selectedAstapDetail.satuan || 'Bidang')"></span>
                                                            <template x-if="getRincianNibar(selectedAstapDetail, tIdx, 'tanah_items')">
                                                                <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                                      :title="getRincianNibar(selectedAstapDetail, tIdx, 'tanah_items').tooltip"
                                                                      x-text="getRincianNibar(selectedAstapDetail, tIdx, 'tanah_items').label"></span>
                                                            </template>
                                                        </div>
                                                        <div class="text-[11px] font-mono">
                                                            <span class="text-slate-400">Total Realisasi: </span>
                                                            <strong class="text-emerald-400 font-bold" x-text="'Rp ' + (Number(tItem.tanah_nilai_perencanaan || 0) + Number(tItem.tanah_nilai_fisik || 0) + Number(tItem.tanah_nilai_pengawasan || 0)).toLocaleString('id-ID')"></strong>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📜 Hak &amp; Sertifikat</span>
                                                            <span class="text-amber-300 font-bold block" x-text="tItem.tanah_hak || 'Hak Pakai'"></span>
                                                            <span class="text-cyan-300 font-mono text-[10px] block truncate" x-text="tItem.tanah_sertifikat_no ? ('No: ' + tItem.tanah_sertifikat_no) : 'Tanpa No Sertifikat'"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Luas &amp; Kondisi</span>
                                                            <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(tItem.tanah_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                                            <span class="font-semibold text-[10px] inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-md border" :class="getRincianKondisiStats(selectedAstapDetail, tIdx, 'tanah_items').badge_class">
                                                                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, tIdx, 'tanah_items').dot_class"></span>
                                                                <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, tIdx, 'tanah_items').text"></span>
                                                            </span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Rincian Komponen Nilai</span>
                                                            <span class="text-slate-200 font-medium block text-[10px]" x-text="'Fisik: Rp ' + Number(tItem.tanah_nilai_fisik || 0).toLocaleString('id-ID')"></span>
                                                            <span class="text-slate-400 text-[9px]" x-text="'Pln: ' + Number(tItem.tanah_nilai_perencanaan || 0).toLocaleString('id-ID') + ' • Was: ' + Number(tItem.tanah_nilai_pengawasan || 0).toLocaleString('id-ID')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Alamat Lokasi</span>
                                                            <span class="text-teal-300 font-medium block truncate" :title="tItem.tanah_alamat" x-text="tItem.tanah_alamat || '-'"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Fallback jika data tanah tunggal / legacy -->
                                    <template x-if="getTanahItemsForDetail(selectedAstapDetail).length === 0">
                                        <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-emerald-500/30 space-y-2.5 shadow-sm">
                                            <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <span class="px-2.5 py-0.5 rounded-lg bg-emerald-500/20 text-emerald-300 font-mono font-bold text-[11px] border border-emerald-500/30">
                                                        🌾 Bidang Tanah #1
                                                    </span>
                                                    <span class="px-2 py-0.5 rounded-lg bg-cyan-500/10 text-cyan-300 font-mono font-bold text-[10.5px] border border-cyan-500/30"
                                                          x-text="(selectedAstapDetail.jumlah_volume || 1) + ' ' + (selectedAstapDetail.satuan || 'Bidang')"></span>
                                                    <template x-if="getRincianNibar(selectedAstapDetail, 0)">
                                                        <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                              :title="getRincianNibar(selectedAstapDetail, 0).tooltip"
                                                              x-text="getRincianNibar(selectedAstapDetail, 0).label"></span>
                                                    </template>
                                                </div>
                                                <div class="text-[11px] font-mono">
                                                    <span class="text-slate-400">Total Realisasi: </span>
                                                    <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(selectedAstapDetail.nilai_realisasi || 0).toLocaleString('id-ID')"></strong>
                                                </div>
                                            </div>
                                            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📜 Hak &amp; Sertifikat</span>
                                                    <span class="text-amber-300 font-bold block" x-text="selectedAstapDetail.spesifikasi_json?.hak_tanah || selectedAstapDetail.hak_tanah || 'Hak Pakai'"></span>
                                                    <span class="text-cyan-300 font-mono text-[10px] block truncate" x-text="selectedAstapDetail.spesifikasi_json?.sertifikat_no || selectedAstapDetail.sertifikat_no ? ('No: ' + (selectedAstapDetail.spesifikasi_json?.sertifikat_no || selectedAstapDetail.sertifikat_no)) : 'Tanpa No Sertifikat'"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Luas &amp; Kondisi</span>
                                                    <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(selectedAstapDetail.spesifikasi_json?.luas_m2 || selectedAstapDetail.luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                                    <span class="font-semibold text-[10px] inline-flex items-center gap-1 mt-0.5 px-2 py-0.5 rounded-md border" :class="getRincianKondisiStats(selectedAstapDetail, 0).badge_class">
                                                        <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, 0).dot_class"></span>
                                                        <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, 0).text"></span>
                                                    </span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Rincian Komponen Nilai</span>
                                                    <span class="text-slate-200 font-medium block text-[10px]" x-text="'Fisik: Rp ' + Number(selectedAstapDetail.spesifikasi_json?.nilai_fisik || selectedAstapDetail.nilai_realisasi || 0).toLocaleString('id-ID')"></span>
                                                    <span class="text-slate-400 text-[9px]" x-text="'Pln: ' + Number(selectedAstapDetail.spesifikasi_json?.nilai_perencanaan || 0).toLocaleString('id-ID') + ' • Was: ' + Number(selectedAstapDetail.spesifikasi_json?.nilai_pengawasan || 0).toLocaleString('id-ID')"></span>
                                                </div>
                                                <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                    <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Alamat Lokasi</span>
                                                    <span class="text-teal-300 font-medium block truncate" :title="selectedAstapDetail.alamat_barang" x-text="selectedAstapDetail.alamat_barang || '-'"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- 2. KIB B (PERALATAN & MESIN) & EXTRACOM -->
                            <template x-if="selectedAstapDetail.category === 'KIB B' || selectedAstapDetail.category === 'EXTRACOM'">
                                <div class="space-y-3">
                                    <!-- Multi-Item Repeater List jika ada mesin_items -->
                                    <template x-if="getMesinItemsForDetail(selectedAstapDetail).length > 0">
                                        <div class="space-y-2.5">
                                            <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                                <span class="flex items-center gap-1.5 text-cyan-400">
                                                    📦 Rincian Barang Terdaftar (<span x-text="getMesinItemsForDetail(selectedAstapDetail).length"></span> Item)
                                                </span>
                                                <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Vol: ' + (selectedAstapDetail.jumlah_volume || getMesinItemsForDetail(selectedAstapDetail).reduce((s, m) => s + (parseFloat(m.mesin_jumlah_barang) || 1), 0)) + ' ' + (selectedAstapDetail.satuan || 'Aset')"></span>
                                            </div>
                                            <template x-for="(mItem, mIdx) in getMesinItemsForDetail(selectedAstapDetail)" :key="mIdx">
                                                <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-cyan-500/30 space-y-2.5 shadow-sm hover:border-cyan-400/50 transition-all">
                                                    <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                        <div class="flex flex-wrap items-center gap-2">
                                                            <span class="px-2.5 py-0.5 rounded-lg bg-cyan-500/20 text-cyan-300 font-mono font-bold text-[11px] border border-cyan-500/30"
                                                                  x-text="'Item #' + (mIdx + 1)"></span>
                                                            <template x-if="selectedAstapDetail?.is_extracomtable || (parseFloat(mItem.mesin_nilai_satuan) || 0) <= 300000">
                                                                <span class="px-2 py-0.5 rounded-lg bg-amber-500/20 text-amber-300 font-bold text-[10px] border border-amber-500/40">
                                                                    📦 EXTRACOM (≤ Rp 300rb)
                                                                </span>
                                                            </template>
                                                            <template x-if="!selectedAstapDetail?.is_extracomtable && (parseFloat(mItem.mesin_nilai_satuan) || 0) > 300000">
                                                                <span class="px-2 py-0.5 rounded-lg bg-cyan-500/20 text-cyan-300 font-bold text-[10px] border border-cyan-500/40">
                                                                    ⚙️ KIB B (&gt; Rp 300rb)
                                                                </span>
                                                            </template>
                                                            <span class="text-white font-bold text-xs" x-text="(mItem.mesin_nama_barang ? (mItem.mesin_nama_barang + ' • ') : '') + ((mItem.mesin_merk || mItem.mesin_type) ? ((mItem.mesin_merk || '') + ' ' + (mItem.mesin_type || '')) : '-')"></span>
                                                            <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                                  x-text="(mItem.mesin_jumlah_barang || 1) + ' ' + (mItem.mesin_satuan || 'Unit')"></span>
                                                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5"
                                                                  :class="getRincianKondisiStats(selectedAstapDetail, mIdx, 'mesin_items').badge_class">
                                                                <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, mIdx, 'mesin_items').dot_class"></span>
                                                                <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, mIdx, 'mesin_items').text"></span>
                                                            </span>
                                                            <template x-if="getRincianNibar(selectedAstapDetail, mIdx, 'mesin_items')">
                                                                <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                                      :title="getRincianNibar(selectedAstapDetail, mIdx, 'mesin_items').tooltip"
                                                                      x-text="getRincianNibar(selectedAstapDetail, mIdx, 'mesin_items').label"></span>
                                                            </template>
                                                        </div>
                                                        <div class="text-[11px] font-mono">
                                                            <span class="text-slate-400">Subtotal: </span>
                                                            <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(((parseFloat(mItem.mesin_jumlah_barang) || 1) * (parseFloat(mItem.mesin_nilai_satuan) || 0)) + (parseFloat(mItem.mesin_administrasi_proyek) || 0)).toLocaleString('id-ID')"></strong>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏷️ Merk / Type / Bahan</span>
                                                            <span class="text-cyan-300 font-bold block truncate" x-text="(mItem.mesin_merk || '-') + ' / ' + (mItem.mesin_type || '-')"></span>
                                                            <span class="text-slate-300 text-[10px]" x-text="'Bhn: ' + (mItem.mesin_bahan || '-') + ' • Uk: ' + (mItem.mesin_ukuran || '-')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🔢 Identitas / No. Seri</span>
                                                            <span class="text-cyan-300 font-mono font-semibold block truncate" x-text="'Pabrik: ' + (mItem.mesin_no_pabrik || '-')"></span>
                                                            <span class="text-slate-400 font-mono text-[9px] block truncate" x-text="'Rgk: ' + (mItem.mesin_no_rangka || '-') + ' • Msn: ' + (mItem.mesin_no_mesin || '-')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Satuan &amp; Adm</span>
                                                            <span class="text-emerald-300 font-medium block text-[10px]" x-text="'Rp ' + Number(mItem.mesin_nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                                            <span class="text-slate-400 text-[9px]" x-text="'Adm: Rp ' + Number(mItem.mesin_administrasi_proyek || 0).toLocaleString('id-ID')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏥 Ruang / Unit Pemegang</span>
                                                            <span class="text-amber-300 font-medium block truncate" :title="mItem.ruang_pemegang || mItem.ruang_pemegang_mesin" x-text="mItem.ruang_pemegang || mItem.ruang_pemegang_mesin || '-'"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Fallback jika data single item / legacy -->
                                    <template x-if="!selectedAstapDetail.spesifikasi_json?.mesin_items || selectedAstapDetail.spesifikasi_json.mesin_items.length === 0">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏷️ Merk / Brand</span>
                                                <span class="text-white font-bold" x-text="selectedAstapDetail.merk || selectedAstapDetail.spesifikasi_json?.merk || '-'"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">⚙️ Type / Model</span>
                                                <span class="text-white font-bold" x-text="selectedAstapDetail.type || selectedAstapDetail.spesifikasi_json?.type || '-'"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🧪 Bahan / Material</span>
                                                <span class="text-white font-bold" x-text="selectedAstapDetail.bahan || selectedAstapDetail.spesifikasi_json?.bahan || '-'"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🔢 No. Pabrik / Seri</span>
                                                <span class="text-cyan-300 font-mono font-bold" x-text="selectedAstapDetail.no_pabrik || selectedAstapDetail.spesifikasi_json?.no_pabrik || '-'"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🚗 No. Rangka / Mesin</span>
                                                <span class="text-slate-200 font-mono font-semibold" x-text="(selectedAstapDetail.spesifikasi_json?.no_rangka || '-') + ' / ' + (selectedAstapDetail.spesifikasi_json?.no_mesin || '-')"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📋 No. BPKB / Polisi</span>
                                                <span class="text-slate-200 font-mono font-semibold" x-text="(selectedAstapDetail.spesifikasi_json?.no_bpkb || '-') + ' / ' + (selectedAstapDetail.spesifikasi_json?.no_polisi || '-')"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 col-span-full">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏥 Ruang / Unit Pemegang</span>
                                                <span class="text-amber-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.ruang_pemegang || (selectedAstapDetail.registers && selectedAstapDetail.registers[0] ? selectedAstapDetail.registers[0].ruang_pemegang : '-')"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- 3. KIB C (GEDUNG & BANGUNAN) -->
                            <template x-if="selectedAstapDetail.category === 'KIB C'">
                                <div class="space-y-3">
                                    <!-- Jika Multi-Item Gedung -->
                                    <template x-if="getGedungItemsForDetail(selectedAstapDetail).length > 0">
                                        <div class="space-y-2.5">
                                            <div class="flex items-center justify-between px-1">
                                                <span class="text-[11px] font-bold text-purple-300 uppercase tracking-wider flex items-center space-x-1.5">
                                                    <span>🏢 Rincian Gedung & Bangunan:</span>
                                                </span>
                                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-500/20 text-purple-300 font-mono font-bold border border-purple-500/30" 
                                                      x-text="getGedungItemsForDetail(selectedAstapDetail).length + ' Gedung/Bangunan Terdaftar'"></span>
                                            </div>
                                            <template x-for="(gItem, gIdx) in getGedungItemsForDetail(selectedAstapDetail)" :key="gIdx">
                                                <div class="p-3 rounded-2xl bg-slate-900/90 border border-purple-500/30 hover:border-purple-500/60 transition-all space-y-2.5">
                                                    <div class="flex flex-wrap items-center justify-between gap-2 border-b border-slate-800 pb-2">
                                                        <div class="flex items-center space-x-2">
                                                            <span class="px-2 py-0.5 rounded-lg bg-purple-500/20 text-purple-300 font-mono font-bold text-[10px] border border-purple-500/40" x-text="'Gedung #' + (gIdx + 1)"></span>
                                                            <span class="text-xs font-bold text-white" x-text="gItem.gedung_nama_barang || selectedAstapDetail.nama_barang"></span>
                                                            <template x-if="getRincianNibar(selectedAstapDetail, gIdx, 'gedung_items')">
                                                                <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                                      :title="getRincianNibar(selectedAstapDetail, gIdx, 'gedung_items').tooltip"
                                                                      x-text="getRincianNibar(selectedAstapDetail, gIdx, 'gedung_items').label"></span>
                                                            </template>
                                                        </div>
                                                        <div class="flex items-center space-x-3 text-[10.5px] font-mono">
                                                            <span class="text-slate-400">Luas: <strong class="text-cyan-300" x-text="(gItem.gedung_luas_m2 || 0) + ' M²'"></strong></span>
                                                            <span class="text-slate-400 flex items-center gap-1">
                                                                <span>Kondisi:</span>
                                                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold border"
                                                                      :class="getRincianKondisiStats(selectedAstapDetail, gIdx, 'gedung_items').badge_class"
                                                                      x-text="getRincianKondisiStats(selectedAstapDetail, gIdx, 'gedung_items').text"></span>
                                                            </span>
                                                            <span class="text-emerald-400 font-bold" x-text="'Rp ' + Number(Number(gItem.gedung_nilai_perencanaan || 0) + Number(gItem.gedung_nilai_fisik || 0) + Number(gItem.gedung_nilai_pengawasan || 0) + Number(gItem.gedung_nilai_ap || gItem.gedung_nilai_pip || 0)).toLocaleString('id-ID')"></span>
                                                        </div>
                                                    </div>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏢 Konstruksi</span>
                                                            <span class="text-purple-300 font-bold block" x-text="(gItem.gedung_bertingkat || 'Bertingkat') + ' • ' + (gItem.gedung_beton || 'Beton')"></span>
                                                            <span class="text-slate-300 text-[9.5px]" x-text="'Bangunan: ' + (gItem.gedung_is_baru === 'Baru' ? 'Baru (1)' : 'Lama (-)')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status & Kode Tanah</span>
                                                            <span class="text-teal-300 font-semibold block truncate" x-text="gItem.gedung_status_tanah || 'Hak Pakai RSUD'"></span>
                                                            <span class="text-cyan-400 font-mono text-[9px] block truncate" x-text="gItem.gedung_kode_aset_tanah || '1.3.1.01.01.02.013'"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Komponen Nilai</span>
                                                            <span class="text-slate-300 block text-[9.5px]" x-text="'Fisik: Rp ' + Number(gItem.gedung_nilai_fisik || 0).toLocaleString('id-ID')"></span>
                                                            <span class="text-slate-400 text-[9px]" x-text="'Pln: ' + Number(gItem.gedung_nilai_perencanaan || 0).toLocaleString('id-ID') + ' • Pws: ' + Number(gItem.gedung_nilai_pengawasan || 0).toLocaleString('id-ID') + ' • AP: ' + Number(gItem.gedung_nilai_ap || gItem.gedung_nilai_pip || 0).toLocaleString('id-ID')"></span>
                                                        </div>
                                                        <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                            <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Letak / Lokasi Fisik</span>
                                                            <span class="text-emerald-300 font-medium block truncate" :title="gItem.gedung_alamat" x-text="gItem.gedung_alamat || selectedAstapDetail.alamat_barang || '-'"></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </template>
                                        </div>
                                    </template>

                                    <!-- Fallback jika data single item / legacy -->
                                    <template x-if="getGedungItemsForDetail(selectedAstapDetail).length === 0">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-2.5">
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏢 Tipe Konstruksi</span>
                                                <span class="text-purple-300 font-bold" x-text="(selectedAstapDetail.spesifikasi_json?.bertingkat || 'Bertingkat') + ' • ' + (selectedAstapDetail.spesifikasi_json?.beton || 'Beton')"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📐 Luas Lantai Gedung</span>
                                                <span class="text-white font-bold font-mono" x-text="(selectedAstapDetail.spesifikasi_json?.luas_m2 || selectedAstapDetail.volume_satuan || '-') + ' m²'"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🌱 Status Hak Tanah Gedung</span>
                                                <span class="text-teal-300 font-bold" x-text="selectedAstapDetail.spesifikasi_json?.status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏷️ Kode Aset Tanah Induk</span>
                                                <span class="text-cyan-300 font-mono font-bold" x-text="selectedAstapDetail.spesifikasi_json?.kode_aset_tanah || '1.3.1.01.01.02.013'"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">🏗️ Nilai Fisik & Perencanaan</span>
                                                <span class="text-emerald-400 font-mono font-bold" x-text="'Fisik: Rp ' + Number(selectedAstapDetail.spesifikasi_json?.nilai_fisik || selectedAstapDetail.nilai_perolehan || 0).toLocaleString('id-ID')"></span>
                                            </div>
                                            <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800">
                                                <span class="text-slate-400 text-[10px] block font-semibold mb-0.5">📍 Lokasi Alamat Jaringan</span>
                                                <span class="text-white font-bold truncate block" x-text="selectedAstapDetail.alamat_barang || 'Kawasan RSUD Dr. H. Koesnandi'"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <!-- 4. KIB D (JALAN, IRIGASI DAN JARINGAN) -->
                            <template x-if="selectedAstapDetail.category === 'KIB D'">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                        <span class="flex items-center gap-1.5 text-teal-400 font-bold uppercase tracking-wider text-[11px]">
                                            🛣️ Rincian Jaringan / Ruas Terdaftar (<span x-text="getJaringanItemsForDetail(selectedAstapDetail).length"></span> Item)
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Vol: ' + (selectedAstapDetail.jumlah_volume || getJaringanItemsForDetail(selectedAstapDetail).reduce((acc, it) => acc + (parseFloat(it.jaringan_jumlah) || 1), 0)) + ' ' + (selectedAstapDetail.satuan || 'Ruas')"></span>
                                    </div>

                                    <!-- Loop Semua Item KIB D -->
                                    <div class="space-y-2.5">
                                        <template x-for="(jItem, jIdx) in getJaringanItemsForDetail(selectedAstapDetail)" :key="jIdx">
                                            <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-teal-500/30 space-y-2.5 shadow-sm hover:border-teal-400/50 transition-all">
                                                <!-- Header Card Item -->
                                                <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-teal-500/20 text-teal-300 font-mono font-bold text-[11px] border border-teal-500/30"
                                                              x-text="'Ruas #' + (jIdx + 1)"></span>
                                                        <span class="text-white font-bold text-xs" x-text="jItem.jaringan_nama_barang || selectedAstapDetail.nama_barang"></span>
                                                        <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                              x-text="(jItem.jaringan_jumlah || 1) + ' ' + (jItem.jaringan_satuan || selectedAstapDetail.satuan || 'Ruas')"></span>
                                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5"
                                                              :class="getRincianKondisiStats(selectedAstapDetail, jIdx, 'jaringan_items').badge_class">
                                                            <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, jIdx, 'jaringan_items').dot_class"></span>
                                                            <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, jIdx, 'jaringan_items').text"></span>
                                                        </span>
                                                        <template x-if="getRincianNibar(selectedAstapDetail, jIdx, 'jaringan_items')">
                                                            <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                                  :title="getRincianNibar(selectedAstapDetail, jIdx, 'jaringan_items').tooltip"
                                                                  x-text="getRincianNibar(selectedAstapDetail, jIdx, 'jaringan_items').label"></span>
                                                        </template>
                                                    </div>
                                                    <div class="text-[11px] font-mono">
                                                        <span class="text-slate-400">Total Realisasi: </span>
                                                        <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(Number(jItem.jaringan_nilai_perencanaan || 0) + Number(jItem.jaringan_nilai_fisik || 0) + Number(jItem.jaringan_nilai_pengawasan || 0) + Number(jItem.jaringan_nilai_ap || jItem.jaringan_nilai_pip || 0)).toLocaleString('id-ID')"></strong>
                                                    </div>
                                                </div>

                                                <!-- Grid Informasi 4 Kolom -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                    <!-- Kolom 1: Konstruksi & Spesifikasi -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🛣️ Konstruksi &amp; Tipe</span>
                                                        <span class="text-teal-300 font-bold block" x-text="(jItem.jaringan_bertingkat || 'Bertingkat') + ' • ' + (jItem.jaringan_beton || 'Beton')"></span>
                                                        <span class="text-slate-300 text-[9.5px]" x-text="'Status: ' + (jItem.jaringan_is_baru === 'Baru' ? 'Baru (1)' : 'Lama (-)')"></span>
                                                    </div>

                                                    <!-- Kolom 2: Dimensi & Luas -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📐 Dimensi &amp; Luas</span>
                                                        <span class="text-cyan-300 font-mono font-bold block" x-text="(Number(jItem.jaringan_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                                        <span class="text-slate-400 text-[9.5px]" x-text="'P: ' + (jItem.jaringan_panjang_m || 0) + ' m • L: ' + (jItem.jaringan_lebar_m || 0) + ' m'"></span>
                                                    </div>

                                                    <!-- Kolom 3: Status Hak Tanah & Kode -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status &amp; Kode Tanah</span>
                                                        <span class="text-amber-300 font-semibold block truncate" x-text="jItem.jaringan_status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                                        <span class="text-cyan-400 font-mono text-[9px] block truncate" x-text="jItem.jaringan_kode_aset_tanah || '1.3.1.01.01.02.013'"></span>
                                                    </div>

                                                    <!-- Kolom 4: Komponen Nilai & Lokasi -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Fisik &amp; Lokasi</span>
                                                        <span class="text-emerald-300 font-medium block text-[10px]" x-text="'Fisik: Rp ' + Number(jItem.jaringan_nilai_fisik || 0).toLocaleString('id-ID')"></span>
                                                        <span class="text-teal-300 font-medium block truncate text-[9.5px]" :title="jItem.jaringan_alamat" x-text="jItem.jaringan_alamat || selectedAstapDetail.alamat_barang || '-'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- 5. KIB E (ASET TETAP LAINNYA) -->
                            <template x-if="selectedAstapDetail.category === 'KIB E'">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                        <span class="flex items-center gap-1.5 text-orange-400 font-bold uppercase tracking-wider text-[11px]">
                                            📚 Rincian Barang Terdaftar (<span x-text="getLainnyaItemsForDetail(selectedAstapDetail).length"></span> Item)
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Vol: ' + (selectedAstapDetail.jumlah_volume || getLainnyaItemsForDetail(selectedAstapDetail).reduce((acc, it) => acc + (parseFloat(it.lainnya_jumlah_barang) || 1), 0)) + ' ' + (selectedAstapDetail.satuan || 'Eksemplar')"></span>
                                    </div>

                                    <!-- Loop Semua Item KIB E (Item #1, Item #2, dst) -->
                                    <div class="space-y-2.5">
                                        <template x-for="(lItem, lIdx) in getLainnyaItemsForDetail(selectedAstapDetail)" :key="lIdx">
                                            <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-orange-500/30 space-y-2.5 shadow-sm hover:border-orange-400/50 transition-all">
                                                <!-- Header Card Item -->
                                                <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-orange-500/20 text-orange-300 font-mono font-bold text-[11px] border border-orange-500/30"
                                                              x-text="'Item #' + (lIdx + 1)"></span>
                                                        <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border"
                                                              :class="{
                                                                  'bg-amber-500/20 text-amber-300 border-amber-500/40': (lItem.kib_e_sub_type || 'buku') === 'buku',
                                                                  'bg-purple-500/20 text-purple-300 border-purple-500/40': lItem.kib_e_sub_type === 'kesenian',
                                                                  'bg-emerald-500/20 text-emerald-300 border-emerald-500/40': lItem.kib_e_sub_type === 'hewan_tumbuhan'
                                                              }"
                                                              x-text="(lItem.kib_e_sub_type === 'kesenian' ? '🎨 Kesenian & Budaya' : (lItem.kib_e_sub_type === 'hewan_tumbuhan' ? '🌿 Hewan & Tumbuhan' : '📚 Buku Perpustakaan'))"></span>
                                                        <span class="text-white font-bold text-xs" x-text="lItem.lainnya_nama_barang || selectedAstapDetail.nama_barang"></span>
                                                        <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                              x-text="(lItem.lainnya_jumlah_barang || 1) + ' ' + (lItem.lainnya_satuan || 'Eksemplar')"></span>
                                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5"
                                                              :class="getRincianKondisiStats(selectedAstapDetail, lIdx, 'lainnya_items').badge_class">
                                                            <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, lIdx, 'lainnya_items').dot_class"></span>
                                                            <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, lIdx, 'lainnya_items').text"></span>
                                                        </span>
                                                        <template x-if="getRincianNibar(selectedAstapDetail, lIdx, 'lainnya_items')">
                                                            <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                                  :title="getRincianNibar(selectedAstapDetail, lIdx, 'lainnya_items').tooltip"
                                                                  x-text="getRincianNibar(selectedAstapDetail, lIdx, 'lainnya_items').label"></span>
                                                        </template>
                                                    </div>
                                                    <div class="text-[11px] font-mono">
                                                        <span class="text-slate-400">Subtotal: </span>
                                                        <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(((parseFloat(lItem.lainnya_jumlah_barang) || 1) * (parseFloat(lItem.lainnya_nilai_satuan) || 0)) + (parseFloat(lItem.lainnya_administrasi_proyek) || 0)).toLocaleString('id-ID')"></strong>
                                                    </div>
                                                </div>

                                                <!-- Grid Informasi 4 Kolom -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                    <!-- Kolom 1: Identitas / Judul -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <template x-if="(lItem.kib_e_sub_type || 'buku') === 'buku'">
                                                            <div>
                                                                <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📚 Judul &amp; Pencipta</span>
                                                                <span class="text-amber-300 font-bold block truncate" :title="lItem.lainnya_buku_judul" x-text="lItem.lainnya_buku_judul || '-'"></span>
                                                                <span class="text-slate-300 text-[9.5px] block truncate" x-text="'Penulis: ' + (lItem.lainnya_buku_pencipta || '-')"></span>
                                                            </div>
                                                        </template>
                                                        <template x-if="lItem.kib_e_sub_type === 'kesenian'">
                                                            <div>
                                                                <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🎨 Asal &amp; Seniman</span>
                                                                <span class="text-purple-300 font-bold block truncate" :title="lItem.lainnya_kesenian_asal" x-text="lItem.lainnya_kesenian_asal || '-'"></span>
                                                                <span class="text-slate-300 text-[9.5px] block truncate" x-text="'Seniman: ' + (lItem.lainnya_kesenian_pencipta || '-')"></span>
                                                            </div>
                                                        </template>
                                                        <template x-if="lItem.kib_e_sub_type === 'hewan_tumbuhan'">
                                                            <div>
                                                                <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌿 Jenis Hewan/Tumbuhan</span>
                                                                <span class="text-emerald-300 font-bold block truncate" :title="lItem.lainnya_hewan_jenis || lItem.lainnya_hewan_judul" x-text="lItem.lainnya_hewan_jenis || lItem.lainnya_hewan_judul || '-'"></span>
                                                                <span class="text-slate-300 text-[9.5px] block truncate" x-text="'Varietas: ' + (lItem.lainnya_hewan_spesifikasi || '-')"></span>
                                                            </div>
                                                        </template>
                                                    </div>

                                                    <!-- Kolom 2: Spesifikasi Fisik & Ukuran -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🔍 Detail &amp; Spesifikasi</span>
                                                        <span class="text-slate-200 font-medium block truncate"
                                                              :title="lItem.lainnya_buku_spesifikasi || lItem.lainnya_kesenian_spesifikasi || lItem.lainnya_hewan_spesifikasi || '-'"
                                                              x-text="lItem.lainnya_buku_spesifikasi || lItem.lainnya_kesenian_spesifikasi || lItem.lainnya_hewan_spesifikasi || '-'"></span>
                                                        <template x-if="lItem.kib_e_sub_type === 'kesenian'">
                                                            <span class="text-slate-400 text-[9px] block truncate" x-text="'Bahan: ' + (lItem.lainnya_kesenian_bahan || '-') + ' • Uk: ' + (lItem.lainnya_kesenian_ukuran || '-')"></span>
                                                        </template>
                                                    </div>

                                                    <!-- Kolom 3: Nilai Satuan & Adm -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Satuan &amp; Adm</span>
                                                        <span class="text-emerald-300 font-medium block text-[10px]" x-text="'Rp ' + Number(lItem.lainnya_nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                                        <span class="text-slate-400 text-[9px]" x-text="'Adm: Rp ' + Number(lItem.lainnya_administrasi_proyek || 0).toLocaleString('id-ID')"></span>
                                                    </div>

                                                    <!-- Kolom 4: Ruang / Unit Pemegang -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏥 Ruang / Unit Pemegang</span>
                                                        <span class="text-amber-300 font-medium block truncate"
                                                              :title="lItem.ruang_pemegang_lainnya || lItem.ruang_pemegang || selectedAstapDetail.ruang_unit"
                                                              x-text="lItem.ruang_pemegang_lainnya || lItem.ruang_pemegang || selectedAstapDetail.ruang_unit || '-'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- 6. KIB F (KONSTRUKSI DALAM PENGERJAAN / KDP) -->
                            <template x-if="selectedAstapDetail.category === 'KIB F'">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                        <span class="flex items-center gap-1.5 text-rose-400 font-bold uppercase tracking-wider text-[11px]">
                                            🏗️ Rincian Pengerjaan KDP Terdaftar (<span x-text="getKdpItemsForDetail(selectedAstapDetail).length"></span> Bangunan/Proyek)
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Vol: ' + (selectedAstapDetail.jumlah_volume || getKdpItemsForDetail(selectedAstapDetail).reduce((acc, it) => acc + (parseFloat(it.kdp_jumlah_bangunan) || 1), 0)) + ' ' + (selectedAstapDetail.satuan || 'Bangunan')"></span>
                                    </div>

                                    <!-- Loop Semua Item KIB F (KDP #1, KDP #2, dst) -->
                                    <div class="space-y-2.5">
                                        <template x-for="(kItem, kIdx) in getKdpItemsForDetail(selectedAstapDetail)" :key="kIdx">
                                            <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-rose-500/30 space-y-2.5 shadow-sm hover:border-rose-400/50 transition-all">
                                                <!-- Header Card Item -->
                                                <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-rose-500/20 text-rose-300 font-mono font-bold text-[11px] border border-rose-500/30"
                                                              x-text="'KDP #' + (kIdx + 1)"></span>
                                                        <span class="text-white font-bold text-xs" x-text="kItem.kdp_nama_barang || selectedAstapDetail.nama_barang"></span>
                                                        <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                              x-text="(kItem.kdp_jumlah_bangunan || 1) + ' ' + (kItem.kdp_satuan || 'Gedung')"></span>
                                                        
                                                        <!-- Badge Progres Fisik -->
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-rose-500/20 text-rose-300 font-mono font-extrabold text-[10.5px] border border-rose-500/40 flex items-center gap-1">
                                                            <span>📊</span>
                                                            <span x-text="(kItem.kdp_progres_persen || 0) + '% Fisik'"></span>
                                                        </span>

                                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5"
                                                              :class="getRincianKondisiStats(selectedAstapDetail, kIdx, 'kdp_items').badge_class">
                                                            <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, kIdx, 'kdp_items').dot_class"></span>
                                                            <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, kIdx, 'kdp_items').text"></span>
                                                        </span>
                                                        
                                                        <template x-if="getRincianNibar(selectedAstapDetail, kIdx, 'kdp_items')">
                                                            <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                                  :title="getRincianNibar(selectedAstapDetail, kIdx, 'kdp_items').tooltip"
                                                                  x-text="getRincianNibar(selectedAstapDetail, kIdx, 'kdp_items').label"></span>
                                                        </template>
                                                    </div>
                                                    <div class="text-[11px] font-mono">
                                                        <span class="text-slate-400">Subtotal Nilai: </span>
                                                        <strong class="text-rose-400 font-bold" x-text="'Rp ' + Number((parseFloat(kItem.kdp_nilai_perencanaan) || 0) + (parseFloat(kItem.kdp_nilai_fisik) || 0) + (parseFloat(kItem.kdp_nilai_pengawasan) || 0) + (parseFloat(kItem.kdp_nilai_ap) || 0)).toLocaleString('id-ID')"></strong>
                                                    </div>
                                                </div>

                                                <!-- Visual Progress Bar KDP -->
                                                <div class="bg-slate-950/80 p-2 rounded-xl border border-slate-800/80 space-y-1">
                                                    <div class="flex justify-between items-center text-[10px]">
                                                        <span class="text-slate-400 font-semibold flex items-center gap-1">
                                                            <span>🚀 Progres Pengerjaan Fisik Lapangan</span>
                                                        </span>
                                                        <span class="font-mono font-extrabold text-rose-400 text-[11px]" x-text="(kItem.kdp_progres_persen || 0) + '% Selesai'"></span>
                                                    </div>
                                                    <div class="w-full bg-slate-800 h-2.5 rounded-full overflow-hidden p-0.5 border border-slate-700/50">
                                                        <div class="bg-gradient-to-r from-amber-500 via-rose-500 to-emerald-400 h-full rounded-full transition-all duration-500"
                                                             :style="'width: ' + Math.min(100, Math.max(0, parseFloat(kItem.kdp_progres_persen) || 0)) + '%'"></div>
                                                    </div>
                                                </div>

                                                <!-- Grid Informasi 4 Kolom KDP -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                    <!-- Kolom 1: Konstruksi & Luas -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏢 Tipe Konstruksi</span>
                                                        <span class="text-purple-300 font-bold block" x-text="(kItem.kdp_bertingkat || 'Bertingkat') + ' • ' + (kItem.kdp_beton || 'Beton')"></span>
                                                        <span class="text-cyan-300 font-mono text-[10px] block mt-0.5" x-text="'Luas: ' + (Number(kItem.kdp_luas_m2 || 0)).toLocaleString('id-ID') + ' m²'"></span>
                                                    </div>

                                                    <!-- Kolom 2: Status Hak Tanah & Kode -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🌱 Status &amp; Kode Tanah</span>
                                                        <span class="text-teal-300 font-semibold block truncate" x-text="kItem.kdp_status_tanah || 'Tanah Hak Pakai RSUD'"></span>
                                                        <span class="text-cyan-400 font-mono text-[9px] block truncate" x-text="kItem.kdp_kode_aset_tanah || '1.3.1.01.01.02.013'"></span>
                                                    </div>

                                                    <!-- Kolom 3: Komponen Nilai Rinci -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Komponen Biaya</span>
                                                        <span class="text-slate-300 block text-[9.5px]" x-text="'Fisik: Rp ' + Number(kItem.kdp_nilai_fisik || 0).toLocaleString('id-ID')"></span>
                                                        <span class="text-slate-400 text-[9px]" x-text="'Pln: ' + Number(kItem.kdp_nilai_perencanaan || 0).toLocaleString('id-ID') + ' • Pws: ' + Number(kItem.kdp_nilai_pengawasan || 0).toLocaleString('id-ID') + ' • AP: ' + Number(kItem.kdp_nilai_ap || 0).toLocaleString('id-ID')"></span>
                                                    </div>

                                                    <!-- Kolom 4: Lokasi Proyek Fisik -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">📍 Lokasi Pengerjaan KDP</span>
                                                        <span class="text-rose-300 font-medium block truncate" :title="kItem.kdp_alamat" x-text="kItem.kdp_alamat || selectedAstapDetail.alamat_barang || '-'"></span>
                                                        <span class="text-slate-400 text-[9px] block mt-0.5" x-text="kItem.kdp_is_baru || 'Baru'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                            <!-- 7. ATB (ASET TIDAK BERWUJUD) - Multi-Item Cards -->
                            <template x-if="selectedAstapDetail.category === 'ATB'">
                                <div class="space-y-3">
                                    <!-- Header ATB Multi-Item -->
                                    <div class="flex items-center justify-between text-xs font-semibold text-slate-300 px-1">
                                        <span class="flex items-center gap-1.5 text-violet-400 font-bold uppercase tracking-wider text-[11px]">
                                            💻 Rincian Item ATB Terdaftar (<span x-text="getAtbItemsForDetail(selectedAstapDetail).length"></span> Item)
                                        </span>
                                        <span class="text-[11px] text-slate-400 font-mono" x-text="'Total Vol: ' + (selectedAstapDetail.jumlah_volume || getAtbItemsForDetail(selectedAstapDetail).reduce((s,a) => s + (a.atb_jumlah||1), 0)) + ' ' + (selectedAstapDetail.satuan || 'Lisensi')"></span>
                                    </div>

                                    <!-- Cards per Item ATB -->
                                    <div class="space-y-2.5">
                                        <template x-for="(aItem, aIdx) in getAtbItemsForDetail(selectedAstapDetail)" :key="aIdx">
                                            <div class="p-3.5 rounded-2xl bg-slate-900/90 border border-violet-500/30 space-y-2.5 shadow-sm hover:border-violet-400/50 transition-all">
                                                <!-- Header Card Item -->
                                                <div class="flex flex-wrap items-center justify-between border-b border-slate-800 pb-2 gap-2">
                                                    <div class="flex flex-wrap items-center gap-2">
                                                        <span class="px-2.5 py-0.5 rounded-lg bg-violet-500/20 text-violet-300 font-mono font-bold text-[11px] border border-violet-500/30"
                                                              x-text="'ATB #' + (aIdx + 1)"></span>
                                                        <span class="text-white font-bold text-xs" x-text="aItem.atb_nama_barang || selectedAstapDetail.nama_barang"></span>
                                                        <span class="px-2 py-0.5 rounded-lg bg-emerald-500/10 text-emerald-300 font-mono font-bold text-[10px] border border-emerald-500/30"
                                                              x-text="(aItem.atb_jumlah || 1) + ' ' + (aItem.atb_satuan || selectedAstapDetail.satuan || 'Lisensi')"></span>
                                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-semibold border inline-flex items-center gap-1.5"
                                                              :class="getRincianKondisiStats(selectedAstapDetail, aIdx, 'atb_items').badge_class">
                                                            <span class="w-1.5 h-1.5 rounded-full shrink-0" :class="getRincianKondisiStats(selectedAstapDetail, aIdx, 'atb_items').dot_class"></span>
                                                            <span x-text="'Kondisi: ' + getRincianKondisiStats(selectedAstapDetail, aIdx, 'atb_items').text"></span>
                                                        </span>
                                                        <template x-if="getRincianNibar(selectedAstapDetail, aIdx, 'atb_items')">
                                                            <span class="text-[11px] text-cyan-400 font-mono font-bold"
                                                                  :title="getRincianNibar(selectedAstapDetail, aIdx, 'atb_items').tooltip"
                                                                  x-text="getRincianNibar(selectedAstapDetail, aIdx, 'atb_items').label"></span>
                                                        </template>
                                                    </div>
                                                    <div class="text-[11px] font-mono">
                                                        <span class="text-slate-400">Subtotal: </span>
                                                        <strong class="text-emerald-400 font-bold" x-text="'Rp ' + Number(((parseFloat(aItem.atb_jumlah) || 1) * (parseFloat(aItem.atb_nilai_satuan) || 0)) + (parseFloat(aItem.atb_administrasi_proyek) || 0)).toLocaleString('id-ID')"></strong>
                                                    </div>
                                                </div>

                                                <!-- Grid Informasi 4 Kolom -->
                                                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-2 text-[10.5px]">
                                                    <!-- Kolom 1: Judul & Vendor -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💻 Judul &amp; Vendor</span>
                                                        <span class="text-indigo-300 font-bold block truncate" :title="aItem.atb_judul_nama" x-text="aItem.atb_judul_nama || aItem.atb_nama_barang || selectedAstapDetail.nama_barang || '-'"></span>
                                                        <span class="text-slate-300 text-[9.5px] block truncate" x-text="'Vendor: ' + (aItem.atb_pencipta || '-')"></span>
                                                    </div>

                                                    <!-- Kolom 2: Detail & Spesifikasi -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🔍 Detail &amp; Spesifikasi</span>
                                                        <span class="text-cyan-300 font-medium block truncate"
                                                              :title="aItem.atb_spesifikasi"
                                                              x-text="aItem.atb_spesifikasi || '-'"></span>
                                                        <span class="text-slate-400 text-[9.5px] block truncate" x-text="'Bentuk: ' + (aItem.atb_satuan || 'Lisensi')"></span>
                                                    </div>

                                                    <!-- Kolom 3: Nilai Satuan & Adm -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">💰 Nilai Satuan &amp; Adm</span>
                                                        <span class="text-emerald-300 font-medium block text-[10px]" x-text="'Rp ' + Number(aItem.atb_nilai_satuan || 0).toLocaleString('id-ID')"></span>
                                                        <span class="text-slate-400 text-[9px]" x-text="'Adm: Rp ' + Number(aItem.atb_administrasi_proyek || 0).toLocaleString('id-ID')"></span>
                                                    </div>

                                                    <!-- Kolom 4: Ruang / Unit Pemegang -->
                                                    <div class="p-2 rounded-xl bg-slate-950/70 border border-slate-800/80">
                                                        <span class="text-slate-400 block text-[9px] uppercase font-bold mb-0.5">🏥 Ruang / Unit Pemegang</span>
                                                        <span class="text-violet-300 font-medium block truncate"
                                                              :title="aItem.atb_ruang_pemegang || selectedAstapDetail.ruang_pemegang || selectedAstapDetail.ruang_unit"
                                                              x-text="aItem.atb_ruang_pemegang || selectedAstapDetail.ruang_pemegang || selectedAstapDetail.ruang_unit || '-'"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>

                        </div>

                        <!-- Dokumen Legalisasi & Pengadaan (Responsive Grid) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-2.5">
                            <h4 class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center space-x-1.5">
                                <span>📄 Dokumen Pengadaan &amp; Legalisasi BAST</span>
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 text-[11px]">
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor SPK / Kontrak:</span>
                                    <span class="text-cyan-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.spk_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Surat Pesanan / BAP:</span>
                                    <span class="text-purple-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.surat_pesanan_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor Kwitansi:</span>
                                    <span class="text-amber-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.kwitansi_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor Faktur / Invoice:</span>
                                    <span class="text-emerald-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.faktur_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor SP2D:</span>
                                    <span class="text-teal-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.sp2d_nomor || '-'"></span>
                                </div>
                                <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between gap-2 min-w-0">
                                    <span class="text-slate-400 font-medium shrink-0">Nomor BAST:</span>
                                    <span class="text-rose-300 font-mono font-bold truncate text-right" x-text="selectedAstapDetail.bast_nomor || '-'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Pihak Penyedia & Pejabat Pembuat Komitmen (PPK) -->
                        <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800/80 space-y-3">
                            <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                                <h4 class="text-xs font-extrabold text-teal-400 uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>🏢 Pihak Penyedia (Rekanan) &amp; Pejabat Pembuat Komitmen (PPK)</span>
                                </h4>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded bg-slate-900 border border-slate-800 text-slate-400">Langkah 4 - Rekanan &amp; PPK</span>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 text-[11px]">
                                <!-- Card Pihak Penyedia -->
                                <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-2.5">
                                    <span class="text-[10.5px] font-extrabold text-cyan-400 uppercase tracking-wider block flex items-center space-x-1">
                                        <span>🏢 Informasi Rekanan / Vendor</span>
                                    </span>
                                    <div class="space-y-1.5 divide-y divide-slate-800/60">
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-slate-400">Nama Perusahaan / Rekanan:</span>
                                            <strong class="text-white font-bold truncate max-w-[55%]" :title="selectedAstapDetail.penyedia_nama" x-text="selectedAstapDetail.penyedia_nama || '-'"></strong>
                                        </div>
                                        <div class="flex items-center justify-between pt-1.5">
                                            <span class="text-slate-400">Nama Pimpinan / Pemilik:</span>
                                            <span class="text-slate-200 font-medium truncate max-w-[55%]" :title="selectedAstapDetail.penyedia_pemilik" x-text="selectedAstapDetail.penyedia_pemilik || '-'"></span>
                                        </div>
                                        <template x-if="selectedAstapDetail.category === 'EXTRACOM' || selectedAstapDetail.category === 'Extracom' || selectedAstapDetail.is_extracomtable || selectedAstapDetail.category === 'ATB'">
                                            <div class="flex items-center justify-between pt-1.5">
                                                <span class="text-slate-400">No. HP / WhatsApp Aktif:</span>
                                                <span class="text-amber-400 font-mono font-bold truncate max-w-[55%] flex items-center gap-1.5">
                                                    <span class="text-xs">📱</span>
                                                    <span x-text="selectedAstapDetail.penyedia_telepon || selectedAstapDetail.spesifikasi_json?.penyedia_telepon || selectedAstapDetail.spesifikasi_json?.penyedia_kontak || '-'"></span>
                                                </span>
                                            </div>
                                        </template>
                                        <div class="flex items-center justify-between pt-1.5">
                                            <span class="text-slate-400">Rekening Bank:</span>
                                            <span class="text-amber-300 font-mono font-bold truncate max-w-[55%]" x-text="selectedAstapDetail.penyedia_rekening_nomor ? ((selectedAstapDetail.penyedia_rekening_nama ? selectedAstapDetail.penyedia_rekening_nama + ' - ' : '') + selectedAstapDetail.penyedia_rekening_nomor) : (selectedAstapDetail.penyedia_rekening_nama || '-')"></span>
                                        </div>
                                        <div class="flex items-start justify-between pt-1.5">
                                            <span class="text-slate-400 shrink-0">Alamat Perusahaan:</span>
                                            <span class="text-teal-300 font-medium text-right truncate max-w-[55%]" :title="selectedAstapDetail.penyedia_alamat" x-text="selectedAstapDetail.penyedia_alamat || '-'"></span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Card PPK -->
                                <div class="p-3.5 rounded-xl bg-slate-900/70 border border-slate-800/90 space-y-2.5">
                                    <span class="text-[10.5px] font-extrabold text-amber-400 uppercase tracking-wider block flex items-center space-x-1">
                                        <span>👔 Pejabat Pembuat Komitmen (PPK)</span>
                                    </span>
                                    <div class="space-y-1.5 divide-y divide-slate-800/60">
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-slate-400">Nama Pejabat (PPK):</span>
                                            <strong class="text-white font-bold truncate max-w-[55%]" :title="selectedAstapDetail.ppk_nama" x-text="selectedAstapDetail.ppk_nama || '-'"></strong>
                                        </div>
                                        <div class="flex items-center justify-between pt-1.5">
                                            <span class="text-slate-400">NIP Pejabat (PPK):</span>
                                            <span class="text-cyan-300 font-mono font-bold truncate max-w-[55%]" x-text="selectedAstapDetail.ppk_nip || '-'"></span>
                                        </div>
                                        <div class="flex items-start justify-between pt-1.5">
                                            <span class="text-slate-400 shrink-0">Keterangan / Catatan:</span>
                                            <span class="text-slate-300 italic text-right truncate max-w-[55%]" :title="selectedAstapDetail.keterangan_tambahan || selectedAstapDetail.keterangan" x-text="selectedAstapDetail.keterangan_tambahan || selectedAstapDetail.keterangan || '-'"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- TABEL RINCIAN REGISTER NIBAR PER-UNIT (FULLY RESPONSIVE SCROLL) -->
                        <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2.5 border-b border-slate-800 pb-3">
                                <div>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider">🏷️ RINCIAN NIBAR &amp; PENEMPATAN RUANGAN (REGISTER):</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Daftar unik kode NIBAR per-aset beserta lokasi penempatannya.</p>
                                </div>
                                <div class="flex items-center space-x-2 shrink-0">
                                    @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                    <button type="button" @click="resequenceSingleAstap(selectedAstapDetail)"
                                        class="px-2.5 py-1 rounded-xl bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 text-[10.5px] font-bold transition-all flex items-center space-x-1 cursor-pointer shadow-sm active:scale-95"
                                        title="Urutkan ulang register NIBAR barang ini agar berurutan tanpa celah">
                                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                        <span>Rapikan NIBAR Barang Ini</span>
                                    </button>
                                    @endif
                                    <span class="text-[10px] font-extrabold px-3 py-1 rounded-xl bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 font-mono shadow-sm"
                                          x-text="filteredRegisters.length + ' / ' + (selectedAstapDetail.registers ? selectedAstapDetail.registers.length : 0) + ' Aset'"></span>
                                </div>
                            </div>

                            <!-- FILTER BAR INTERAKTIF RINCIAN MODAL -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5 bg-slate-900/70 p-3 rounded-xl border border-slate-800">
                                <div>
                                    <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Status Penempatan</label>
                                    <select x-model="detailPenempatanFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                        <option value="all">Semua Penempatan</option>
                                        <option value="sudah">📍 Sudah Ditempatkan</option>
                                        <option value="belum">⚠️ Belum Ditempatkan</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Kondisi Aset</label>
                                    <select x-model="detailKondisiFilter" class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 focus:outline-none focus:border-cyan-500">
                                        <option value="all">Semua Kondisi</option>
                                        <option value="Baik">Baik (B)</option>
                                        <option value="Kurang Baik">Kurang Baik (KB)</option>
                                        <option value="Rusak Ringan">Rusak Ringan (RR)</option>
                                        <option value="Rusak Berat">Rusak Berat (RB)</option>
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-[9.5px] font-bold text-slate-400 uppercase tracking-wider mb-1">Cari NIBAR / Ruangan</label>
                                    <input type="text" x-model="detailSearchQuery" placeholder="Cari NIBAR / No Reg / Ruang..."
                                           class="w-full bg-slate-950 border border-slate-800 rounded-lg px-2.5 py-1.5 text-[11px] text-slate-200 placeholder-slate-500 focus:outline-none focus:border-cyan-500">
                                </div>
                            </div>

                            <!-- Responsive Scroll Container (Max 5 Rows Scrollable) -->
                            <div class="rounded-xl border border-slate-800/80 custom-scrollbar" style="max-height: 285px; overflow-y: auto; overflow-x: auto;">
                                <table class="w-full text-left text-[11px] text-slate-300 min-w-[640px]">
                                    <thead class="bg-slate-950 text-slate-400 font-bold uppercase text-[9.5px] border-b border-slate-800 shadow-sm" style="position: sticky; top: 0; z-index: 10; background-color: #020617;">
                                        <tr>
                                            <th class="px-3.5 py-2.5 text-center">NIBAR &amp; No. Register Resmi</th>
                                            <th class="px-3.5 py-2.5 text-center">Penempatan Ruangan</th>
                                            <th class="px-3.5 py-2.5 text-center">Kondisi</th>
                                            <th class="px-3.5 py-2.5 text-center whitespace-nowrap">QR Code</th>
                                            <th class="px-3.5 py-2.5 text-center whitespace-nowrap">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-800/80 bg-slate-900/50">
                                        <template x-for="reg in filteredRegisters" :key="reg.id">
                                            <tr class="hover:bg-slate-800/60 transition-colors">
                                                <td class="px-3.5 py-2.5 font-mono font-bold text-emerald-400 whitespace-nowrap text-center" x-text="reg.nibar || reg.no_register"></td>
                                                <td class="px-3.5 py-2.5 text-center">
                                                    <template x-if="reg.ruang_pemegang">
                                                        <span class="inline-flex items-center space-x-1.5 text-slate-200 font-semibold justify-center">
                                                            <span class="text-teal-400 text-xs">📍</span>
                                                            <span x-text="reg.ruang_pemegang"></span>
                                                        </span>
                                                    </template>
                                                    <template x-if="!reg.ruang_pemegang">
                                                        <span class="inline-flex items-center space-x-1.5 text-amber-400 font-bold bg-amber-500/10 px-2.5 py-1 rounded-lg border border-amber-500/30 text-[10px] justify-center">
                                                            <span>⚠️</span>
                                                            <span>Belum Ditempatkan</span>
                                                        </span>
                                                    </template>
                                                </td>
                                                <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                    <span class="px-2.5 py-1 rounded-xl text-[10.5px] font-bold border inline-block shadow-sm"
                                                          :class="{
                                                              'bg-emerald-500/20 text-emerald-300 border-emerald-500/30': reg.kondisi === 'Baik',
                                                              'bg-amber-500/20 text-amber-300 border-amber-500/30': reg.kondisi === 'Kurang Baik',
                                                              'bg-orange-500/20 text-orange-300 border-orange-500/30': reg.kondisi === 'Rusak Ringan',
                                                              'bg-rose-500/20 text-rose-300 border-rose-500/30': reg.kondisi === 'Rusak Berat' || reg.kondisi === 'Rusak'
                                                          }" x-text="reg.kondisi"></span>
                                                </td>
                                                <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                    <button type="button" @click.stop="downloadQrCodeNibar(reg, selectedAstapDetail)"
                                                        title="Pratinjau & Download QR NIBAR Aset Ini"
                                                        class="inline-flex items-center space-x-1 px-2.5 py-1.5 rounded-xl bg-emerald-500/10 hover:bg-emerald-500/25 border border-emerald-500/30 hover:border-emerald-400 text-emerald-400 hover:text-emerald-300 font-bold text-[10.5px] transition-all shadow-sm active:scale-95 group cursor-pointer leading-none">
                                                        <svg class="w-3.5 h-3.5 text-emerald-400 group-hover:scale-110 transition-transform shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                                        </svg>
                                                        <span class="leading-none pt-0.5">Download QR</span>
                                                    </button>
                                                </td>
                                                <td class="px-3.5 py-2.5 text-center whitespace-nowrap">
                                                    <div class="flex items-center justify-center space-x-1.5">
                                                        <!-- 1. Tombol Cek Riwayat Aset (Mutasi) -->
                                                        <button type="button" @click.stop="openRiwayatModal(reg)" title="Cek Riwayat Mutasi Aset Ini"
                                                                class="p-1.5 rounded-xl bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 text-purple-400 hover:text-purple-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        </button>

                                                        @if(in_array(Auth::user()->role ?? '', ['master_admin', 'admin']))
                                                        <!-- 2. Tombol Ubah Kondisi Barang (Modal Khusus) -->
                                                        <button type="button" @click.stop="openEditKondisiModal(reg)" title="Ubah Kondisi Aset Ini"
                                                                class="p-1.5 rounded-xl bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 text-amber-400 hover:text-amber-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 0L20.586 7a2 2 0 010 2.828l-8.586 8.586z"/></svg>
                                                        </button>

                                                        <!-- 3. Tombol Hapus Register -->
                                                        <button type="button" @click.stop="deleteRegister(reg)" title="Hapus Aset Register Ini"
                                                                class="p-1.5 rounded-xl bg-rose-500/10 hover:bg-rose-500/20 border border-rose-500/30 text-rose-400 hover:text-rose-300 transition-all cursor-pointer">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                        </button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        </template>
                                        <template x-if="filteredRegisters.length === 0">
                                            <tr>
                                                <td colspan="5" class="px-3 py-6 text-center text-slate-500 italic text-xs">
                                                    Tidak ditemukan rincian register NIBAR yang sesuai dengan filter pencarian.
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Catatan / Keterangan Tambahan -->
                        <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-slate-800 space-y-1" x-show="selectedAstapDetail.keterangan">
                            <span class="text-slate-400 text-[10px] block font-bold uppercase tracking-wider">💡 Keterangan &amp; Catatan Tambahan:</span>
                            <p class="text-slate-200 text-xs leading-relaxed" x-text="selectedAstapDetail.keterangan || '-'"></p>
                        </div>
                    </div>
                </template>

                <!-- Modal Footer -->
                <div class="pt-4 border-t border-slate-800 flex items-center justify-end space-x-2.5">
                    <button type="button" @click="showDetailModal = false" class="px-5 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-extrabold text-xs transition-all shadow-md active:scale-95 cursor-pointer">
                        Tutup Detail
                    </button>
                </div>
            </div>
        </div>
