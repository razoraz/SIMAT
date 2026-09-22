        <!-- ========================================================================= -->
        <!-- MODAL DIALOG: REKLASIFIKASI ASET TETAP (RSDK)                             -->
        <!-- ========================================================================= -->
        <div x-show="showReklasModal" x-cloak
             class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-3 sm:p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">
            
            <div @click.away="showReklasModal = false"
                 class="bg-slate-900 border border-indigo-500/40 rounded-3xl max-w-2xl w-full shadow-2xl flex flex-col max-h-[92vh] overflow-hidden my-auto"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">
                
                <!-- 1. Modal Header (Fixed Top) -->
                <div class="shrink-0 px-6 py-4 border-b border-slate-800 flex items-start justify-between bg-slate-950/40">
                    <div class="flex items-center space-x-3">
                        <div class="p-2.5 rounded-2xl bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 text-xl shadow-md shrink-0">
                            🔄
                        </div>
                        <div>
                            <div class="flex items-center space-x-2">
                                <h3 class="text-base sm:text-lg font-extrabold text-white">Reklasifikasi Aset Tetap</h3>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">RSDK</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">Penyesuaian klasifikasi, pemindahan KIB, atau pengalihan ke Ekstrakomptabel sesuai standar akuntansi RSUD dr. H. Koesnadi.</p>
                        </div>
                    </div>
                    <button type="button" @click="showReklasModal = false" class="text-slate-400 hover:text-white p-1 rounded-lg text-lg font-bold cursor-pointer transition-colors">&times;</button>
                </div>

                <!-- 2. Modal Body (Scrollable Middle) -->
                <div class="flex-1 overflow-y-auto custom-scrollbar px-6 py-5 space-y-4">

                    <!-- Info Aset yang Dipilih (Card Ringkas) -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-2.5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0 flex-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block">Nama Barang / Aset</span>
                                <div class="text-sm font-extrabold text-white truncate" x-text="selectedAstapReklas?.nama_barang || '-'"></div>
                                <div class="text-[11px] font-mono text-cyan-400 font-medium mt-0.5" x-text="'Kode: ' + (selectedAstapReklas?.kode_barang || '-')"></div>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block" x-text="(reklasJenis === 'extracom' || reklasJenis === 'intracom') ? 'Nilai Realisasi (Disesuaikan)' : 'Nilai Realisasi'"></span>
                                <div class="text-sm font-extrabold font-mono"
                                     :class="reklasJenis === 'extracom' ? 'text-amber-300' : (reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-emerald-400')"
                                     x-text="(reklasJenis === 'extracom' || reklasJenis === 'intracom') ? ('Rp ' + Number(getReklasExtracomTotal()).toLocaleString('id-ID')) : (selectedAstapReklas?.jumlah_realisasi || 'Rp 0')"></div>
                                <div class="text-[10.5px] font-mono mt-0.5"
                                     :class="reklasJenis === 'extracom' ? 'text-amber-200/80' : (reklasJenis === 'intracom' ? 'text-emerald-200/80' : 'text-teal-300')"
                                     x-text="((reklasJenis === 'extracom' || reklasJenis === 'intracom') ? getReklasExtracomTotalVolume() : (selectedAstapReklas?.jumlah_volume || 1)) + ' Unit'"></div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 pt-2 border-t border-slate-800/80">
                            <span class="text-[11px] text-slate-400">Kategori Saat Ini:</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 shrink-0"
                                  x-text="selectedAstapReklas?.category || '-'"></span>
                            <span class="text-[11px] text-slate-300 truncate" x-text="selectedAstapReklas?.jenis_aset_nama || ''"></span>
                        </div>
                    </div>

                    <!-- Form Pilihan Jenis Reklasifikasi (Custom Radio Cards) -->
                    <div>
                        <label class="block text-slate-300 font-bold text-xs uppercase tracking-wider mb-2">
                            🎯 Pilih Jenis Reklasifikasi:
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <!-- 1. Ekstrakomptabel / Kapitalisasi Intrakomptabel -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border transition-all select-none"
                                   style="gap: 12px;"
                                   :class="{
                                       'opacity-40 cursor-not-allowed bg-slate-950/30 border-slate-800': isReklasExtracomDisabled(),
                                       'cursor-pointer bg-amber-500/10 border-amber-500/50 shadow-sm shadow-amber-500/10': !isReklasExtracomDisabled() && reklasJenis === 'extracom',
                                       'cursor-pointer bg-emerald-500/10 border-emerald-500/50 shadow-sm shadow-emerald-500/10': !isReklasExtracomDisabled() && reklasJenis === 'intracom',
                                       'cursor-pointer bg-slate-950/60 border-slate-800 hover:border-slate-700': !isReklasExtracomDisabled() && reklasJenis !== 'extracom' && reklasJenis !== 'intracom'
                                   }">
                                <input type="radio" name="reklas_jenis" :value="isCurrentAstapExtracom() ? 'intracom' : 'extracom'" x-model="reklasJenis" :disabled="isReklasExtracomDisabled()" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="{
                                         'border-amber-400 bg-amber-500/20': reklasJenis === 'extracom' && !isReklasExtracomDisabled(),
                                         'border-emerald-400 bg-emerald-500/20': reklasJenis === 'intracom' && !isReklasExtracomDisabled(),
                                         'border-slate-700 bg-slate-900': reklasJenis !== 'extracom' && reklasJenis !== 'intracom'
                                     }">
                                    <div x-show="(reklasJenis === 'extracom' || reklasJenis === 'intracom') && !isReklasExtracomDisabled()" 
                                         class="rounded-full" :class="reklasJenis === 'intracom' ? 'bg-emerald-400' : 'bg-amber-400'" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs" 
                                              :class="{
                                                  'text-amber-300': reklasJenis === 'extracom' && !isReklasExtracomDisabled(),
                                                  'text-emerald-300': reklasJenis === 'intracom' && !isReklasExtracomDisabled(),
                                                  'text-white': reklasJenis !== 'extracom' && reklasJenis !== 'intracom'
                                              }"
                                              x-text="isCurrentAstapExtracom() ? 'Intrakomtable' : 'Ekstrakomtable'"></span>
                                        <template x-if="isReklasExtracomDisabled()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30">Terkunci (SAP)</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5" 
                                         x-text="isReklasExtracomDisabled() ? 'Tidak berlaku untuk kelompok aset ini (Wajib Intrakomtable)' : (isCurrentAstapExtracom() ? 'Pengalihan aset ke kelompok Intrakomtable' : 'Batas nilai satuan ≤ Rp 300.000 per unit')"></div>
                                </div>
                            </label>

                            <!-- 2. Pindah KIB / Koreksi Rekening (Salah Kamar / Salah Akun 108) -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer transition-all select-none"
                                   style="gap: 12px;"
                                   :class="reklasJenis === 'pindah_kib' ? 'bg-indigo-500/10 border-indigo-500/50 shadow-sm shadow-indigo-500/10' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <input type="radio" name="reklas_jenis" value="pindah_kib" x-model="reklasJenis" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="reklasJenis === 'pindah_kib' ? 'border-indigo-400 bg-indigo-500/20' : 'border-slate-700 bg-slate-900'">
                                    <div x-show="reklasJenis === 'pindah_kib'" class="rounded-full bg-indigo-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-xs" :class="reklasJenis === 'pindah_kib' ? 'text-indigo-300' : 'text-white'">Pindah KIB / Koreksi Rekening</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Salah kamar KIB atau perbaikan sub-rincian Simda 108</div>
                                </div>
                            </label>

                            <!-- 3. KDP (Konstruksi Dalam Pengerjaan) - Kapitalisasi KDP Selesai -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border transition-all select-none"
                                   style="gap: 12px;"
                                   :class="{
                                       'opacity-40 cursor-not-allowed bg-slate-950/30 border-slate-800': isReklasKdpDisabled(),
                                       'cursor-pointer bg-rose-500/10 border-rose-500/50 shadow-sm shadow-rose-500/10': !isReklasKdpDisabled() && reklasJenis === 'kdp',
                                       'cursor-pointer bg-slate-950/60 border-slate-800 hover:border-slate-700': !isReklasKdpDisabled() && reklasJenis !== 'kdp'
                                   }">
                                <input type="radio" name="reklas_jenis" value="kdp" x-model="reklasJenis" :disabled="isReklasKdpDisabled()" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="{
                                         'border-rose-400 bg-rose-500/20': reklasJenis === 'kdp' && !isReklasKdpDisabled(),
                                         'border-slate-700 bg-slate-900': reklasJenis !== 'kdp' || isReklasKdpDisabled()
                                     }">
                                    <div x-show="reklasJenis === 'kdp' && !isReklasKdpDisabled()" class="rounded-full bg-rose-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs" 
                                              :class="{
                                                  'text-rose-300': reklasJenis === 'kdp' && !isReklasKdpDisabled(),
                                                  'text-white': reklasJenis !== 'kdp' && !isReklasKdpDisabled(),
                                                  'text-slate-500': isReklasKdpDisabled()
                                              }">Kapitalisasi KDP Selesai</span>
                                        <template x-if="isReklasKdpDisabled()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-slate-800 text-slate-400 border border-slate-700">Khusus KDP (KIB F)</span>
                                        </template>
                                        <template x-if="!isReklasKdpDisabled()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">KDP Selesai</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5" 
                                         x-text="isReklasKdpDisabled() 
                                            ? 'Hanya untuk kapitalisasi aset KDP (KIB F) yang telah selesai 100%' 
                                            : 'Pekerjaan fisik 100% selesai, dialihkan ke KIB C/D Definitif'"></div>
                                </div>
                            </label>

                            <!-- 4. Koreksi Nilai / Audit BPK (Koreksi Lain-Lain) -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer transition-all select-none"
                                   style="gap: 12px;"
                                   :class="reklasJenis === 'koreksi_nilai' ? 'bg-cyan-500/10 border-cyan-500/50 shadow-sm shadow-cyan-500/10' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <input type="radio" name="reklas_jenis" value="koreksi_nilai" x-model="reklasJenis" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="reklasJenis === 'koreksi_nilai' ? 'border-cyan-400 bg-cyan-500/20' : 'border-slate-700 bg-slate-900'">
                                    <div x-show="reklasJenis === 'koreksi_nilai'" class="rounded-full bg-cyan-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-bold text-xs" :class="reklasJenis === 'koreksi_nilai' ? 'text-cyan-300' : 'text-white'">Koreksi Nilai Realisasi / Audit BPK</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Penyesuaian nilai realisasi belanja modal hasil pemeriksaan BPK / rekonsiliasi LKD</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Input Dinamis Berdasarkan Jenis Reklas -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                        <!-- Jika Pindah KIB / Koreksi Rekening -->
                        <div x-show="reklasJenis === 'pindah_kib'" class="space-y-3">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📦 Klasifikasi / KIB Tujuan:</label>
                                    <select x-model="reklasTujuanKib"
                                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-indigo-500">
                                        <option value="">-- Pilih KIB / Kelompok Tujuan --</option>
                                        <option value="KIB A">KIB A - Tanah</option>
                                        <option value="KIB B">KIB B - Peralatan &amp; Mesin</option>
                                        <option value="KIB C">KIB C - Gedung &amp; Bangunan</option>
                                        <option value="KIB D">KIB D - Jalan, Jaringan &amp; Irigasi</option>
                                        <option value="KIB E">KIB E - Aset Tetap Lainnya</option>
                                        <option value="ATB">ATB - Aset Tidak Berwujud</option>
                                        <option value="ASET LAIN">Aset Lain-Lain</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">🏷️ Sub-Rincian Rekening Simda 108:</label>
                                    <input type="text" x-model="reklasTujuanKode" placeholder="Contoh: 1.3.2.05.01 Alat Kantor"
                                            class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400">Digunakan jika aset salah kamar kelompok KIB atau salah sub-rincian kode rekening Simda BMD 108.</p>
                        </div>

                        <!-- Jika KDP: Pilihan KIB Tujuan Definitif -->
                        <div x-show="reklasJenis === 'kdp'" class="space-y-2">
                            <label class="block text-rose-300 font-bold text-[10.5px] uppercase tracking-wider mb-1">🏗️ Alihkan KDP Selesai ke KIB Definitif:</label>
                            <select x-model="reklasTujuanKib"
                                    class="w-full bg-slate-900 border border-rose-500/50 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-rose-400">
                                <option value="KIB C">KIB C - Gedung &amp; Bangunan (Definitif)</option>
                                <option value="KIB D">KIB D - Jalan, Jaringan &amp; Irigasi (Definitif)</option>
                                <option value="KIB B">KIB B - Peralatan &amp; Mesin (Instalasi Mekanikal Gedung)</option>
                            </select>
                            <p class="text-[10px] text-slate-400 mt-1">Akumulasi nilai KDP akan dikapitalisasi dan dicatat sebagai aset tetap definitif di neraca.</p>
                        </div>

                        <!-- Jika Koreksi Nilai / Audit BPK (Koreksi Lain-Lain) -->
                        <div x-show="reklasJenis === 'koreksi_nilai'" class="space-y-3.5">
                            <!-- Banner Info Koreksi Temuan BPK -->
                            <div class="p-3.5 rounded-xl bg-cyan-950/30 border border-cyan-500/30 flex items-start space-x-2.5">
                                <span class="text-cyan-400 text-base shrink-0">💡</span>
                                <div class="text-[11px] text-cyan-200/90 leading-relaxed">
                                    <strong>Penyesuaian Nilai Kapitalisasi &amp; Anggaran (Temuan Audit BPK):</strong><br>
                                    Ubah nilai kapitalisasi satuan pada masing-masing barang (Barang 1, 2, dst) di bawah. <strong>Total Nilai Realisasi</strong> akan terkalkulasi secara otomatis. <strong>Nilai Anggaran (DPA/RBA)</strong> dapat disesuaikan jika terdapat revisi pagu anggaran.
                                </div>
                            </div>

                            <!-- 1. Grid Anggaran & Realisasi -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Nilai Anggaran (DPA/RBA) -->
                                <div class="p-3.5 rounded-xl bg-blue-950/30 border border-blue-500/40 space-y-2">
                                    <div>
                                        <label class="block text-blue-300 font-bold text-[10.5px] uppercase tracking-wider">
                                            💰 Nilai Anggaran (DPA/RBA):
                                        </label>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-slate-400 text-xs font-mono font-bold">Rp</span>
                                        <input type="number" step="1000" min="0" x-model.number="reklasNilaiAnggaran" placeholder="0"
                                               class="w-full pl-9 pr-3 py-1.5 bg-slate-900 border border-blue-500/50 rounded-xl text-xs font-mono font-bold text-blue-200 focus:outline-none focus:border-blue-400 text-right">
                                    </div>
                                    <p class="text-[9.5px] text-slate-400 leading-tight">Pagu anggaran belanja modal dapat disesuaikan jika ada perubahan pagu DPA.</p>
                                </div>

                                <!-- Nilai Realisasi Aset -->
                                <div class="p-3.5 rounded-xl bg-emerald-950/30 border border-emerald-500/40 space-y-2">
                                    <div>
                                        <label class="block text-emerald-300 font-bold text-[10.5px] uppercase tracking-wider">
                                            🔒 Total Nilai Realisasi Aset:
                                        </label>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-slate-400 text-xs font-mono font-bold">Rp</span>
                                        <input type="text" readonly :value="Number(reklasNilaiRealisasiBaru || 0).toLocaleString('id-ID')"
                                               class="w-full pl-9 pr-3 py-1.5 bg-slate-950 border border-emerald-500/40 rounded-xl text-xs font-mono font-extrabold text-emerald-300 cursor-not-allowed text-right focus:outline-none select-none">
                                    </div>
                                    <p class="text-[9.5px] text-slate-400 leading-tight">Terkalkulasi otomatis dari akumulasi nilai barang (Barang 1, 2, dst) di bawah.</p>
                                </div>
                            </div>

                            <!-- 2. Ringkasan Perbandingan & Dampak Selisih Koreksi -->
                            <div class="p-3 rounded-xl bg-slate-900/90 border border-slate-800 flex flex-wrap items-center justify-between gap-2.5 text-[11px]">
                                <div class="flex items-center space-x-2">
                                    <span class="text-slate-400">Realisasi Semula:</span>
                                    <span class="font-mono text-slate-200 font-bold" x-text="selectedAstapReklas?.jumlah_realisasi || 'Rp 0'"></span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-slate-400">➔ Realisasi Baru:</span>
                                    <span class="font-mono text-emerald-400 font-extrabold" x-text="'Rp ' + Number(reklasNilaiRealisasiBaru || 0).toLocaleString('id-ID')"></span>
                                </div>
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-slate-400">Dampak Koreksi:</span>
                                    <template x-if="reklasNominalKoreksi > 0">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold border"
                                              :class="reklasTipeKoreksiNilai === 'kurang' ? 'bg-rose-500/20 text-rose-300 border-rose-500/40' : 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40'"
                                              x-text="(reklasTipeKoreksiNilai === 'kurang' ? '🔻 Berkurang Rp ' : '🔺 Bertambah Rp ') + Number(reklasNominalKoreksi).toLocaleString('id-ID')"></span>
                                    </template>
                                    <template x-if="reklasNominalKoreksi <= 0">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400 border border-slate-700">Tidak ada selisih nilai</span>
                                    </template>
                                </div>
                            </div>

                            <!-- 3. Rincian Barang & Nilai Kapitalisasi (Barang 1, Barang 2, dst) -->
                            <div class="space-y-2 pt-1">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="text-xs font-bold text-cyan-300 uppercase tracking-wider">📦 Rincian Nilai Kapitalisasi Barang:</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30"
                                              x-text="reklasExtracomItems.length + ' Item Terdaftar'"></span>
                                    </div>
                                    <span class="text-[10px] text-slate-400">Ubah nilai per unit barang temuan</span>
                                </div>

                                <!-- Cards Container Barang 1 & 2 -->
                                <div class="space-y-2.5 max-h-[260px] overflow-y-auto custom-scrollbar pr-1">
                                    <template x-for="(item, idx) in reklasExtracomItems" :key="idx">
                                        <div class="p-3 rounded-xl bg-slate-900/90 border border-slate-800 hover:border-slate-700 transition-all space-y-2">
                                            <!-- Baris Atas Item: Badge Nomor & Nama Barang -->
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center space-x-2 min-w-0">
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-800 text-cyan-300 border border-slate-700 shrink-0"
                                                          x-text="'Barang #' + (idx + 1)"></span>
                                                    <span class="text-xs font-bold text-white truncate" x-text="item.nama_barang || ('Barang #' + (idx + 1))"></span>
                                                </div>
                                                <span class="text-[10.5px] font-mono text-slate-400 shrink-0"
                                                      x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span>
                                            </div>

                                            <!-- Grid Input: Nilai Kapitalisasi Satuan & Subtotal -->
                                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-center pt-1.5 border-t border-slate-800/80">
                                                <div class="sm:col-span-7">
                                                    <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">
                                                        Nilai Kapitalisasi Satuan (Rp):
                                                    </label>
                                                    <div class="relative">
                                                        <span class="absolute left-2.5 top-1.5 text-slate-500 text-xs font-mono font-bold">Rp</span>
                                                        <input type="number" step="100" min="0" x-model.number="item.harga_satuan"
                                                               @input="onReklasItemPriceChanged()"
                                                               placeholder="0"
                                                               class="w-full pl-8 pr-2.5 py-1.5 bg-slate-950 border border-cyan-500/40 rounded-lg text-xs font-mono font-bold text-cyan-200 focus:outline-none focus:border-cyan-400 text-right">
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-5 text-right">
                                                    <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">
                                                        Subtotal Barang:
                                                    </label>
                                                    <div class="text-xs font-mono font-extrabold text-emerald-400 py-1.5"
                                                         x-text="'Rp ' + Number((item.jumlah_volume || 1) * (parseFloat(item.harga_satuan) || 0)).toLocaleString('id-ID')">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 4. No Dokumen Pendukung / LHP BPK -->
                            <div>
                                <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📑 No. LHP BPK / BA Rekonsiliasi / Dasar Koreksi:</label>
                                <input type="text" x-model="reklasNoDokumenKoreksi" placeholder="Contoh: LHP/BPK/2026/04 atau BA-REKON/01/2026"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-cyan-500">
                            </div>
                        </div>

                        <!-- Jika Ekstrakomptabel ATAU Kapitalisasi Intrakomptabel: Form Penyesuaian Harga Satuan per Item -->
                        <div x-show="reklasJenis === 'extracom' || reklasJenis === 'intracom'" class="space-y-3">
                            <!-- Jika Intrakom: Pilihan KIB Tujuan -->
                            <div x-show="reklasJenis === 'intracom'" class="p-3.5 rounded-xl bg-slate-900 border border-emerald-500/30 space-y-1.5">
                                <label class="block text-emerald-300 font-bold text-[10.5px] uppercase tracking-wider">🎯 KIB Tujuan Intrakomtable:</label>
                                <select x-model="reklasTujuanKib"
                                        class="w-full bg-slate-950 border border-emerald-500/50 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-emerald-400">
                                    <option value="KIB B">KIB B - Peralatan &amp; Mesin (Definitif)</option>
                                    <option value="KIB E">KIB E - Aset Tetap Lainnya (Definitif)</option>
                                </select>
                                <p class="text-[10px] text-slate-400">Aset akan dipindahkan dari Ekstrakomtable ke KIB terpilih di Neraca Aset Tetap.</p>
                            </div>

                            <!-- Info Box Penjelasan -->
                            <div x-show="reklasJenis === 'extracom'" class="p-3 rounded-xl bg-amber-950/30 border border-amber-500/30 flex items-start space-x-2.5">
                                <span class="text-amber-400 text-sm shrink-0">💡</span>
                                <div class="text-[11px] text-amber-200/90 leading-relaxed">
                                    Barang ini akan dialihkan keluar dari Aset Tetap ke kelompok <strong>Ekstrakomtable</strong>. Anda dapat menyesuaikan harga satuan langsung di bawah ini jika diperlukan.
                                </div>
                            </div>
                            <div x-show="reklasJenis === 'intracom'" class="p-3 rounded-xl bg-emerald-950/30 border border-emerald-500/30 flex items-start space-x-2.5">
                                <span class="text-emerald-400 text-sm shrink-0">💡</span>
                                <div class="text-[11px] text-emerald-200/90 leading-relaxed">
                                    Barang Ekstrakomtable ini akan <strong>dimasukkan kembali ke Neraca Aset Tetap (Intrakomtable)</strong>. Silakan sesuaikan harga satuan langsung di bawah ini jika diperlukan.
                                </div>
                            </div>

                            <!-- Header Section Rincian Barang -->
                            <div class="flex items-center justify-between pt-1">
                                <div class="flex items-center space-x-2">
                                    <span class="text-xs font-bold uppercase tracking-wider"
                                          :class="reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-amber-300'">📦 Rincian Barang &amp; Harga Satuan:</span>
                                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold border"
                                          :class="reklasJenis === 'intracom' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border-amber-500/30'"
                                          x-text="reklasExtracomItems.length + ' Item'"></span>
                                </div>
                            </div>

                            <!-- Daftar Rincian Barang (Repeater Cards) -->
                            <div class="space-y-2.5 max-h-[300px] overflow-y-auto custom-scrollbar pr-1">
                                <template x-for="(item, idx) in reklasExtracomItems" :key="idx">
                                    <div class="p-3 rounded-xl bg-slate-900/90 border transition-all space-y-2.5"
                                         :class="reklasJenis === 'intracom'
                                            ? (parseFloat(item.harga_satuan) <= 300000 ? 'border-rose-500/60 bg-rose-950/10' : 'border-slate-800 hover:border-slate-700')
                                            : (parseFloat(item.harga_satuan) > 300000 ? 'border-rose-500/60 bg-rose-950/10' : (parseFloat(item.harga_satuan) <= 0 ? 'border-amber-500/40' : 'border-slate-800 hover:border-slate-700'))">
                                        
                                        <!-- Baris Atas Item: Nomor & Nama Barang -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-2">
                                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-slate-800 text-slate-300 border border-slate-700"
                                                      x-text="'Barang #' + (idx + 1)"></span>
                                                <span class="text-[11px] font-bold text-white truncate max-w-[220px]" x-text="item.nama_barang || 'Barang Baru'"></span>
                                            </div>
                                        </div>

                                        <!-- Grid Input Kolom -->
                                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-2.5 items-end">
                                            <!-- 1. Nama Barang (Cols 5) -->
                                            <div class="sm:col-span-5">
                                                <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">Nama Barang:</label>
                                                <input type="text" x-model="item.nama_barang" placeholder="Nama barang..."
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none font-medium"
                                                       :class="reklasJenis === 'intracom' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                                            </div>

                                            <!-- 2. Volume & Satuan (Cols 3) -->
                                            <div class="sm:col-span-3">
                                                <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">Volume &amp; Satuan:</label>
                                                <div class="flex space-x-1">
                                                    <input type="number" min="1" x-model.number="item.jumlah_volume" placeholder="Qty"
                                                           class="w-16 bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white text-center focus:outline-none font-bold"
                                                           :class="reklasJenis === 'intracom' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                                                    <input type="text" x-model="item.satuan" placeholder="Satuan"
                                                           class="flex-1 min-w-0 bg-slate-950 border border-slate-700 rounded-lg px-2 py-1.5 text-xs text-white focus:outline-none"
                                                           :class="reklasJenis === 'intracom' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                                                </div>
                                            </div>

                                            <!-- 3. Harga Satuan (Rp) (Cols 4) -->
                                            <div class="sm:col-span-4">
                                                <div class="mb-1">
                                                    <label class="block font-bold text-[10px] uppercase tracking-wider"
                                                           :class="reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-amber-300'">Harga Satuan (Rp):</label>
                                                </div>
                                                <div class="relative">
                                                    <span class="absolute left-2.5 top-1.5 text-slate-500 text-xs font-mono font-bold">Rp</span>
                                                    <input type="number" step="100" min="0" x-model.number="item.harga_satuan"
                                                           placeholder="0"
                                                           class="w-full pl-8 pr-2.5 py-1.5 bg-slate-950 border rounded-lg text-xs font-mono font-extrabold focus:outline-none transition-colors text-right"
                                                           :class="reklasJenis === 'intracom'
                                                                ? (parseFloat(item.harga_satuan) <= 300000 ? 'border-rose-500 text-rose-300 focus:border-rose-400' : 'border-slate-700 text-emerald-400 focus:border-emerald-500')
                                                                : (parseFloat(item.harga_satuan) > 300000 ? 'border-rose-500 text-rose-300 focus:border-rose-400' : (parseFloat(item.harga_satuan) <= 0 ? 'border-amber-500/50 text-amber-300 focus:border-amber-400' : 'border-slate-700 text-emerald-400 focus:border-emerald-500'))">
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Subtotal & Status Validasi per Item -->
                                        <div class="flex items-center justify-between pt-1 border-t border-slate-800/60 text-[11px]">
                                            <div>
                                                <!-- Jika Intrakom: Validasi > 300.000 -->
                                                <template x-if="reklasJenis === 'intracom'">
                                                    <div>
                                                        <template x-if="parseFloat(item.harga_satuan) <= 300000">
                                                            <div class="space-y-0.5">
                                                                <span class="text-rose-400 font-bold flex items-center space-x-1">
                                                                    <span>⚠️</span>
                                                                    <span>Belum memenuhi batas nilai minimum Intrakomtable</span>
                                                                </span>
                                                                <div class="text-[10px] text-rose-300/85 pl-4 flex items-center space-x-1">
                                                                    <span>ℹ️</span>
                                                                    <span>Batas Intrakomtable: Nilai satuan wajib <strong>&gt; Rp 300.000</strong> per unit</span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <template x-if="parseFloat(item.harga_satuan) > 300000">
                                                            <div class="space-y-0.5">
                                                                <span class="text-slate-400">
                                                                    <span class="text-slate-300 font-medium" x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span> &times; 
                                                                    <span class="text-emerald-400 font-mono font-bold" x-text="'Rp ' + Number(item.harga_satuan || 0).toLocaleString('id-ID')"></span>
                                                                </span>
                                                                <div class="text-[9.5px] text-emerald-400/80 pl-0.5">
                                                                    ✓ Memenuhi batas Intrakomtable (&gt; Rp 300.000)
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>

                                                <!-- Jika Ekstrakom: Validasi <= 300.000 & > 0 -->
                                                <template x-if="reklasJenis === 'extracom'">
                                                    <div>
                                                        <template x-if="parseFloat(item.harga_satuan) > 300000">
                                                            <div class="space-y-0.5">
                                                                <span class="text-rose-400 font-bold flex items-center space-x-1">
                                                                    <span>⚠️</span>
                                                                    <span>Melebihi batas nilai maksimum Ekstrakomtable</span>
                                                                </span>
                                                                <div class="text-[10px] text-rose-300/85 pl-4 flex items-center space-x-1">
                                                                    <span>ℹ️</span>
                                                                    <span>Batas Ekstrakomtable: Nilai satuan maksimal <strong>Rp 300.000</strong> per unit</span>
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <template x-if="parseFloat(item.harga_satuan) <= 0">
                                                            <div class="space-y-0.5">
                                                                <span class="text-amber-400 font-medium flex items-center space-x-1">
                                                                    <span>⚠️</span>
                                                                    <span>Wajib diisi (&gt; Rp 0)</span>
                                                                </span>
                                                                <div class="text-[10px] text-amber-300/80 pl-4">
                                                                    Batas Ekstrakomtable: Rp 1 s/d Rp 300.000 per unit
                                                                </div>
                                                            </div>
                                                        </template>
                                                        <template x-if="parseFloat(item.harga_satuan) > 0 && parseFloat(item.harga_satuan) <= 300000">
                                                            <div class="space-y-0.5">
                                                                <span class="text-slate-400">
                                                                    <span class="text-slate-300 font-medium" x-text="(item.jumlah_volume || 1) + ' ' + (item.satuan || 'Unit')"></span> &times; 
                                                                    <span class="text-emerald-400 font-mono font-bold" x-text="'Rp ' + Number(item.harga_satuan || 0).toLocaleString('id-ID')"></span>
                                                                </span>
                                                                <div class="text-[9.5px] text-emerald-400/80 pl-0.5">
                                                                    ✓ Memenuhi batas Ekstrakomtable (&le; Rp 300.000)
                                                                </div>
                                                            </div>
                                                        </template>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-slate-400 text-[10px]">Subtotal: </span>
                                                <span class="font-mono font-extrabold text-emerald-300"
                                                      x-text="'Rp ' + Number((item.jumlah_volume || 1) * (item.harga_satuan || 0)).toLocaleString('id-ID')"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Total Ringkasan Ekstrakomptabel / Intrakomptabel -->
                            <div class="p-3 rounded-xl border flex items-center justify-between"
                                 :class="reklasJenis === 'intracom' ? 'bg-emerald-500/10 border-emerald-500/30' : 'bg-amber-500/10 border-amber-500/30'">
                                <div>
                                    <div class="text-[10px] uppercase font-bold tracking-wider"
                                         :class="reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-amber-300'"
                                         x-text="reklasJenis === 'intracom' ? 'Total Nilai Intrakomtable:' : 'Total Nilai Ekstrakomtable:'"></div>
                                    <div class="text-[11px] text-slate-400 mt-0.5">
                                        Semula: <span class="line-through text-slate-400 font-mono" x-text="selectedAstapReklas?.jumlah_realisasi || 'Rp 0'"></span>
                                        <span class="font-bold ml-1" :class="reklasJenis === 'intracom' ? 'text-emerald-400' : 'text-amber-400'">➔ Disesuaikan</span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-base font-extrabold font-mono"
                                         :class="reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-amber-300'"
                                         x-text="'Rp ' + Number(getReklasExtracomTotal()).toLocaleString('id-ID')"></div>
                                    <div class="text-[10.5px] font-mono"
                                         :class="reklasJenis === 'intracom' ? 'text-emerald-200/80' : 'text-amber-200/80'"
                                         x-text="getReklasExtracomTotalVolume() + ' Total Unit'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Dasar Bukti Belanja / BAST & Tanggal Efektif -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📄 No. Bukti Belanja / BAST / SPK:</label>
                                <input type="text" x-model="reklasNomorBa" placeholder="Kosongkan jika menggunakan bukti transaksi belanja"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                            </div>
                            <div>
                                <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📅 Tanggal Efektif Reklas:</label>
                                <input type="date" x-model="reklasTanggal"
                                       class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs font-bold text-white focus:outline-none focus:border-indigo-500">
                            </div>
                        </div>

                        <!-- Keterangan / Alasan Reklasifikasi -->
                        <div>
                            <label class="block text-slate-400 font-bold text-[10.5px] uppercase tracking-wider mb-1">📝 Alasan / Penjelasan Reklasifikasi:</label>
                            <textarea x-model="reklasAlasan" rows="2" placeholder="Tuliskan catatan tambahan jika ada..."
                                      class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500 resize-none"></textarea>
                        </div>
                    </div>

                    <!-- Live Preview Narasi Resmi Sheet 3 Keterangan -->
                    <div class="p-3.5 rounded-2xl bg-indigo-950/30 border border-indigo-500/30 space-y-1.5">
                        <div class="flex items-center space-x-1.5 text-indigo-300 font-bold text-xs">
                            <span>📋</span>
                            <span>Pratinjau Narasi Keterangan (Sheet 3 Reklas RSDK):</span>
                        </div>
                        <p class="text-[11px] text-indigo-200/90 italic leading-relaxed"
                           x-text="getReklasNarasiPreview()"></p>
                    </div>

                    <button type="button" @click="submitReklas()"
                        :disabled="isSubmittingReklas"
                        class="px-5 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2 cursor-pointer">
                        <template x-if="isSubmittingReklas">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <span x-text="isSubmittingReklas ? 'Menyimpan...' : '💾 Simpan Reklasifikasi'"></span>
                    </button>
                </div>

            </div>
        </div>
