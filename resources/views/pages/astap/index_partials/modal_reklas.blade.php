        <!-- ========================================================================= -->
        <!-- MODAL DIALOG: REKLASIFIKASI ASET TETAP (RSDK)                             -->
        <!-- ========================================================================= -->
        <template x-teleport="body">
            <div x-show="showReklasModal" x-cloak id="modalReklasDialog"
                 class="fixed inset-0 overflow-y-auto flex items-center justify-center p-3 sm:p-4"
                 style="background-color: rgba(2, 6, 23, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); z-index: 99999;"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">

            <style>
                /* Hilangkan spinner scroller angka bawaan browser agar tidak menutupi digit nominal */
                #modalReklasDialog input[type="number"]::-webkit-inner-spin-button,
                #modalReklasDialog input[type="number"]::-webkit-outer-spin-button {
                    -webkit-appearance: none !important;
                    margin: 0 !important;
                }
                #modalReklasDialog input[type="number"] {
                    -moz-appearance: textfield !important;
                    appearance: textfield !important;
                }
            </style>
            
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
                        <div class="pt-2.5 border-t border-slate-800/80 space-y-2">
                            <div class="flex items-center space-x-2">
                                <span class="text-[11px] text-slate-400 shrink-0">Kategori Saat Ini:</span>
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 shrink-0"
                                      x-text="selectedAstapReklas?.category || '-'"></span>
                                <span class="text-[11px] text-slate-300 truncate" x-text="selectedAstapReklas?.jenis_aset_nama || ''"></span>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                                <span class="text-slate-400 shrink-0">📄 No. Bukti / BAST / SPK:</span>
                                <span class="font-mono text-cyan-300 font-semibold px-2.5 py-0.5 rounded-lg bg-slate-900 border border-slate-700/80 text-[11px]"
                                      x-text="getReklasNoBuktiDisplay()"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Tanggal Reklas (Terkunci Otomatis ke Hari Ini) -->
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between gap-2">
                            <label class="text-slate-300 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 min-w-0">
                                <span class="truncate">📅 Tanggal Reklas:</span>
                            </label>
                            <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-md bg-indigo-500/15 text-indigo-300 border border-indigo-500/30 flex items-center gap-1">
                                <span>🔒</span>
                                <span>Terkunci (Hari Ini)</span>
                            </span>
                        </div>
                        <div class="relative cursor-not-allowed select-none">
                            <input type="text" :value="reklasTanggal ? (reklasTanggal.includes('-') ? reklasTanggal.split('-').reverse().join('/') : reklasTanggal) : '-'" readonly tabindex="-1"
                                   class="w-full bg-slate-950/80 border border-slate-800 rounded-xl px-3.5 py-2.5 text-xs font-bold text-indigo-200 cursor-not-allowed select-none focus:outline-none pointer-events-none shadow-inner"
                                   title="Tanggal reklasifikasi terkunci otomatis pada tanggal hari ini">
                        </div>
                    </div>

                    <!-- Form Pilihan Jenis Reklasifikasi (Custom Radio Cards) -->
                    <div>
                        <label class="block text-slate-300 font-bold text-xs uppercase tracking-wider mb-2">
                            🎯 Pilih Jenis Reklasifikasi:
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5">
                            <!-- 1. Ekstrakomptabel / Kapitalisasi Intrakomptabel -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border transition-all select-none"
                                   style="gap: 12px;"
                                   :class="{
                                       'opacity-40 cursor-not-allowed bg-slate-950/30 border-slate-800': isReklasExtracomDisabled() || isKemitraanAktif(),
                                       'cursor-pointer bg-amber-500/10 border-amber-500/50 shadow-sm shadow-amber-500/10': !(isReklasExtracomDisabled() || isKemitraanAktif()) && reklasJenis === 'extracom',
                                       'cursor-pointer bg-emerald-500/10 border-emerald-500/50 shadow-sm shadow-emerald-500/10': !(isReklasExtracomDisabled() || isKemitraanAktif()) && reklasJenis === 'intracom',
                                       'cursor-pointer bg-slate-950/60 border-slate-800 hover:border-slate-700': !(isReklasExtracomDisabled() || isKemitraanAktif()) && reklasJenis !== 'extracom' && reklasJenis !== 'intracom'
                                   }">
                                <input type="radio" name="reklas_jenis" :value="isCurrentAstapExtracom() ? 'intracom' : 'extracom'" x-model="reklasJenis" :disabled="isReklasExtracomDisabled() || isKemitraanAktif()" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="{
                                         'border-amber-400 bg-amber-500/20': reklasJenis === 'extracom' && !(isReklasExtracomDisabled() || isKemitraanAktif()),
                                         'border-emerald-400 bg-emerald-500/20': reklasJenis === 'intracom' && !(isReklasExtracomDisabled() || isKemitraanAktif()),
                                         'border-slate-700 bg-slate-900': (isReklasExtracomDisabled() || isKemitraanAktif()) || (reklasJenis !== 'extracom' && reklasJenis !== 'intracom')
                                     }">
                                    <div x-show="(reklasJenis === 'extracom' || reklasJenis === 'intracom') && !(isReklasExtracomDisabled() || isKemitraanAktif())" 
                                         class="rounded-full" :class="reklasJenis === 'intracom' ? 'bg-emerald-400' : 'bg-amber-400'" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs" 
                                              :class="{
                                                  'text-amber-300': reklasJenis === 'extracom' && !(isReklasExtracomDisabled() || isKemitraanAktif()),
                                                  'text-emerald-300': reklasJenis === 'intracom' && !(isReklasExtracomDisabled() || isKemitraanAktif()),
                                                  'text-white': !(isReklasExtracomDisabled() || isKemitraanAktif()) && reklasJenis !== 'extracom' && reklasJenis !== 'intracom',
                                                  'text-slate-500': isReklasExtracomDisabled() || isKemitraanAktif()
                                              }"
                                              x-text="isCurrentAstapExtracom() ? 'Intrakomtable' : 'Ekstrakomtable'"></span>
                                        <template x-if="isKemitraanAktif()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">PKS Aktif</span>
                                        </template>
                                        <template x-if="!isKemitraanAktif() && isReklasExtracomDisabled()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30">Terkunci (SAP)</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5" 
                                         x-text="isKemitraanAktif() ? 'Terkunci: Aset sedang dalam kemitraan aktif' : (isReklasExtracomDisabled() ? 'Tidak berlaku untuk kelompok aset ini (Wajib Intrakomtable)' : (isCurrentAstapExtracom() ? 'Pengalihan aset ke kelompok Intrakomtable' : 'Batas nilai satuan ≤ Rp 300.000 per unit'))"></div>
                                </div>
                            </label>

                            <!-- 2. Pindah KIB / Koreksi Rekening (Salah Kamar / Salah Akun 108) -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border cursor-pointer transition-all select-none"
                                   style="gap: 12px;"
                                   :class="reklasJenis === 'pindah_kib' ? 'bg-indigo-500/10 border-indigo-500/50 shadow-sm shadow-indigo-500/10 ring-1 ring-indigo-500/20' : 'bg-slate-950/60 border-slate-800 hover:border-slate-700'">
                                <input type="radio" name="reklas_jenis" value="pindah_kib" x-model="reklasJenis" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="reklasJenis === 'pindah_kib' ? 'border-indigo-400 bg-indigo-500/20' : 'border-slate-700 bg-slate-900'">
                                    <div x-show="reklasJenis === 'pindah_kib'" class="rounded-full bg-indigo-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs" :class="reklasJenis === 'pindah_kib' ? 'text-indigo-300' : 'text-white'"
                                              x-text="isKemitraanAktif() ? 'Pindah Sub-Rekening 108 Kemitraan' : 'Pindah KIB / Koreksi Rekening'"></span>
                                        <template x-if="isKemitraanAktif()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">1.5.2 Kemitraan</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5" 
                                         x-text="isKemitraanAktif() ? 'Perbaikan sub-rincian 108 seputar Akun 1.5.2 Kemitraan (Sewa, KSP, BGS/BSG)' : 'Salah kamar KIB atau perbaikan sub-rincian Simda 108'"></div>
                                </div>
                            </label>

                            <!-- 3. KDP (Konstruksi Dalam Pengerjaan) - Kapitalisasi KDP Selesai -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border transition-all select-none"
                                   style="gap: 12px;"
                                   :class="{
                                       'opacity-40 cursor-not-allowed bg-slate-950/30 border-slate-800': isReklasKdpDisabled() || isKemitraanAktif(),
                                       'cursor-pointer bg-rose-500/10 border-rose-500/50 shadow-sm shadow-rose-500/10': !(isReklasKdpDisabled() || isKemitraanAktif()) && reklasJenis === 'kdp',
                                       'cursor-pointer bg-slate-950/60 border-slate-800 hover:border-slate-700': !(isReklasKdpDisabled() || isKemitraanAktif()) && reklasJenis !== 'kdp'
                                   }">
                                <input type="radio" name="reklas_jenis" value="kdp" x-model="reklasJenis" :disabled="isReklasKdpDisabled() || isKemitraanAktif()" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="{
                                         'border-rose-400 bg-rose-500/20': reklasJenis === 'kdp' && !(isReklasKdpDisabled() || isKemitraanAktif()),
                                         'border-slate-700 bg-slate-900': (isReklasKdpDisabled() || isKemitraanAktif()) || reklasJenis !== 'kdp'
                                     }">
                                    <div x-show="reklasJenis === 'kdp' && !(isReklasKdpDisabled() || isKemitraanAktif())" class="rounded-full bg-rose-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs" 
                                              :class="{
                                                  'text-rose-300': reklasJenis === 'kdp' && !(isReklasKdpDisabled() || isKemitraanAktif()),
                                                  'text-white': !(isReklasKdpDisabled() || isKemitraanAktif()) && reklasJenis !== 'kdp',
                                                  'text-slate-500': isReklasKdpDisabled() || isKemitraanAktif()
                                              }">Kapitalisasi KDP Selesai</span>
                                        <template x-if="isKemitraanAktif()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">PKS Aktif</span>
                                        </template>
                                        <template x-if="!isKemitraanAktif() && isReklasKdpDisabled()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-slate-800 text-slate-400 border border-slate-700">Khusus KDP (KIB F)</span>
                                        </template>
                                        <template x-if="!isKemitraanAktif() && !isReklasKdpDisabled()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">KDP Selesai</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5" 
                                         x-text="isKemitraanAktif() ? 'Terkunci: Tidak berlaku untuk aset kemitraan aktif' : (isReklasKdpDisabled() ? 'Hanya untuk kapitalisasi aset KDP (KIB F) yang telah selesai 100%' : 'Pekerjaan fisik 100% selesai, dialihkan ke KIB C/D Definitif')"></div>
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
                                    <div class="font-bold text-xs" :class="reklasJenis === 'koreksi_nilai' ? 'text-cyan-300' : 'text-white'">Koreksi Nilai Realisasi / BPK</div>
                                    <div class="text-[10px] text-slate-400 mt-0.5">Penyesuaian nilai wajar / taksiran konsesi hasil rekonsiliasi atau audit BPK</div>
                                </div>
                            </label>

                            <!-- 5. Hibah (Bantuan Pemerintah) -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border transition-all select-none"
                                   style="gap: 12px;"
                                   :class="{
                                       'opacity-40 cursor-not-allowed bg-slate-950/30 border-slate-800': isKemitraanAktif(),
                                       'cursor-pointer bg-purple-500/10 border-purple-500/50 shadow-sm shadow-purple-500/10': !isKemitraanAktif() && reklasJenis === 'hibah',
                                       'cursor-pointer bg-slate-950/60 border-slate-800 hover:border-slate-700': !isKemitraanAktif() && reklasJenis !== 'hibah'
                                   }">
                                <input type="radio" name="reklas_jenis" value="hibah" x-model="reklasJenis" :disabled="isKemitraanAktif()" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="{
                                         'border-purple-400 bg-purple-500/20': !isKemitraanAktif() && reklasJenis === 'hibah',
                                         'border-slate-700 bg-slate-900': isKemitraanAktif() || reklasJenis !== 'hibah'
                                     }">
                                    <div x-show="!isKemitraanAktif() && reklasJenis === 'hibah'" class="rounded-full bg-purple-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs" :class="(!isKemitraanAktif() && reklasJenis === 'hibah') ? 'text-purple-300' : (isKemitraanAktif() ? 'text-slate-500' : 'text-white')">Hibah (Bantuan Pemerintah)</span>
                                        <template x-if="isKemitraanAktif()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">PKS Aktif</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5" x-text="isKemitraanAktif() ? 'Terkunci: Aset sedang dalam kemitraan aktif' : 'Penyerahan hibah keluar (BAST) atau penerimaan hibah masuk tanpa kas'"></div>
                                </div>
                            </label>

                            <!-- 6. Mutasi Eksternal (Antar-OPD) -->
                            <label class="relative flex items-center p-3.5 rounded-2xl border transition-all select-none"
                                   style="gap: 12px;"
                                   :class="{
                                       'opacity-40 cursor-not-allowed bg-slate-950/30 border-slate-800': isKemitraanAktif(),
                                       'cursor-pointer bg-teal-500/10 border-teal-500/50 shadow-sm shadow-teal-500/10': !isKemitraanAktif() && reklasJenis === 'mutasi_eksternal',
                                       'cursor-pointer bg-slate-950/60 border-slate-800 hover:border-slate-700': !isKemitraanAktif() && reklasJenis !== 'mutasi_eksternal'
                                   }">
                                <input type="radio" name="reklas_jenis" value="mutasi_eksternal" x-model="reklasJenis" :disabled="isKemitraanAktif()" class="hidden" style="display: none;">
                                <div class="flex items-center justify-center rounded-full border shrink-0 transition-all"
                                     style="width: 18px; height: 18px; min-width: 18px; margin-right: 6px;"
                                     :class="{
                                         'border-teal-400 bg-teal-500/20': !isKemitraanAktif() && reklasJenis === 'mutasi_eksternal',
                                         'border-slate-700 bg-slate-900': isKemitraanAktif() || reklasJenis !== 'mutasi_eksternal'
                                     }">
                                    <div x-show="!isKemitraanAktif() && reklasJenis === 'mutasi_eksternal'" class="rounded-full bg-teal-400" style="width: 8px; height: 8px;"></div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-bold text-xs" :class="(!isKemitraanAktif() && reklasJenis === 'mutasi_eksternal') ? 'text-teal-300' : (isKemitraanAktif() ? 'text-slate-500' : 'text-white')">Mutasi Eksternal Antar-OPD</span>
                                        <template x-if="isKemitraanAktif()">
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">PKS Aktif</span>
                                        </template>
                                    </div>
                                    <div class="text-[10px] text-slate-400 mt-0.5" x-text="isKemitraanAktif() ? 'Terkunci: Aset sedang dalam kemitraan aktif' : 'Pengalihan barang RSUD ke dinas/SKPD lain di lingkungan Pemkab'"></div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Input Dinamis Berdasarkan Jenis Reklas -->
                    <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800 space-y-3">
                        <!-- Jika Pindah KIB / Koreksi Rekening -->
                        <div x-show="reklasJenis === 'pindah_kib'" class="space-y-4">
                            
                            <!-- Tingkat 1: Klasifikasi / KIB Tujuan -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-slate-300 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 min-w-0">
                                        <span class="w-4 h-4 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/40 text-[9px] font-black flex items-center justify-center shrink-0">1</span>
                                        <span class="truncate">📦 Klasifikasi / KIB Tujuan:</span>
                                    </label>
                                    <template x-if="isKemitraanAktif()">
                                        <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-md bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 flex items-center gap-1">
                                            <span>🔒</span>
                                            <span>Terkunci: PKS Aktif (1.5.2)</span>
                                        </span>
                                    </template>
                                    <template x-if="!isKemitraanAktif() && reklasTujuanKib">
                                        <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-500/15 text-blue-300 border border-blue-500/30 whitespace-nowrap">
                                            KIB Terpilih
                                        </span>
                                    </template>
                                </div>
                                <select x-model="reklasTujuanKib" @change="onReklasTujuanKibChange()"
                                        :disabled="isKemitraanAktif()"
                                        class="w-full bg-slate-900 border rounded-xl px-3.5 py-2.5 text-xs font-bold text-white focus:outline-none transition-all shadow-inner"
                                        :class="isKemitraanAktif() ? 'border-cyan-500/50 bg-slate-950/90 text-cyan-200 cursor-not-allowed ring-1 ring-cyan-500/20' : 'border-slate-700 focus:border-blue-500 focus:ring-1 focus:ring-blue-500/40 cursor-pointer'">
                                    <option value="">-- Pilih KIB / Kelompok Tujuan --</option>
                                    <option value="KIB A" :disabled="isKemitraanAktif()">KIB A - Tanah (1.3.1)</option>
                                    <option value="KIB B" :disabled="isKemitraanAktif()">KIB B - Peralatan &amp; Mesin (1.3.2)</option>
                                    <option value="KIB C" :disabled="isKemitraanAktif()">KIB C - Gedung &amp; Bangunan (1.3.3)</option>
                                    <option value="KIB D" :disabled="isKemitraanAktif()">KIB D - Jalan, Jaringan &amp; Irigasi (1.3.4)</option>
                                    <option value="KIB E" :disabled="isKemitraanAktif()">KIB E - Aset Tetap Lainnya (1.3.5)</option>
                                    <option value="ATB" :disabled="isKemitraanAktif()">ATB - Aset Tidak Berwujud (1.5.3)</option>
                                    <option value="KEMITRAAN">Kemitraan Pihak Ketiga (1.5.2)</option>
                                    <option value="ASET LAIN" :disabled="isKemitraanAktif()">Aset Lain-Lain (1.5.4)</option>
                                </select>

                                <!-- Banner Penjelasan Interaktif Khusus Kemitraan Aktif -->
                                <template x-if="isKemitraanAktif()">
                                    <div class="mt-2.5 p-3 rounded-2xl bg-cyan-950/60 border border-cyan-500/35 text-cyan-200 text-xs flex items-start gap-2.5 shadow-md">
                                        <span class="text-base shrink-0 mt-0.5">🔒</span>
                                        <div class="space-y-1">
                                            <div class="flex items-center gap-2">
                                                <span class="font-extrabold text-cyan-300">Status Kemitraan Masih Aktif</span>
                                                <span class="px-2 py-0.2 rounded text-[9.5px] font-bold bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">Hanya Antar-Akun 1.5.2</span>
                                            </div>
                                            <p class="text-[11px] text-cyan-200/90 leading-relaxed">
                                                Aset sedang terikat perjanjian kerja sama pemanfaatan yang masih berjalan. Reklasifikasi hanya diperbolehkan antar sub-rekening di dalam <strong>Akun 1.5.2 (Kemitraan Pihak Ketiga)</strong> seperti koreksi jenis sewa, KSP, atau BGS/BSG. Pemindahan balik ke KIB A–F atau Aset Lain-Lain (1.5.4) hanya dapat dilakukan apabila masa konsesi PKS telah berakhir / selesai.
                                            </p>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <!-- Tingkat 2: Sub-Rincian Objek PMDN 108 -->
                            <div class="space-y-1.5 relative" @click.away="isReklasSubRincianOpen = false">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-slate-300 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 min-w-0">
                                        <span class="w-4 h-4 rounded-full bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-[9px] font-black flex items-center justify-center shrink-0">2</span>
                                        <span class="truncate">🏷️ Sub-Rincian Rekening 108:</span>
                                    </label>
                                    <div class="shrink-0 flex items-center gap-1.5 ml-auto">
                                        <button type="button" 
                                                x-show="reklasSubRincianKode" 
                                                @click="clearReklasSubRincian()" 
                                                class="px-2 py-0.5 rounded-md text-[10.5px] font-bold text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 transition-all inline-flex items-center gap-1 cursor-pointer whitespace-nowrap active:scale-95 shadow-xs">
                                            <span>🗑️</span>
                                            <span>Kosongkan</span>
                                        </button>
                                        <button type="button" 
                                                x-show="reklasSubRincianKode && !isReklasSubRincianOpen" 
                                                @click="isReklasSubRincianOpen = true; searchReklasSubRincian = ''" 
                                                class="px-2 py-0.5 rounded-md text-[10.5px] font-bold text-emerald-300 bg-emerald-500/10 hover:bg-emerald-500/20 border border-emerald-500/30 transition-all inline-flex items-center gap-1 cursor-pointer whitespace-nowrap active:scale-95 shadow-xs">
                                            <span>✕</span>
                                            <span>Ganti</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="relative">
                                    <input type="text" 
                                           :value="(!isReklasSubRincianOpen && reklasSubRincianKode) ? (reklasSubRincianKode + ' - ' + reklasSubRincianNama) : searchReklasSubRincian"
                                           @input="searchReklasSubRincian = $event.target.value; isReklasSubRincianOpen = true"
                                           @focus="isReklasSubRincianOpen = true"
                                           :placeholder="reklasSubRincianKode ? (reklasSubRincianKode + ' - ' + reklasSubRincianNama) : (reklasTujuanKib ? 'Ketik untuk memfilter sub rincian pada ' + reklasTujuanKib + '...' : 'Pilih KIB tujuan atau ketik sub rincian 108...')" 
                                           class="w-full bg-slate-900 border rounded-xl px-3.5 py-2.5 pl-9 pr-9 text-xs font-bold transition-all shadow-inner"
                                           :class="reklasSubRincianKode && !isReklasSubRincianOpen ? 'border-emerald-500/60 text-emerald-200 ring-1 ring-emerald-500/20' : 'border-slate-700 text-white focus:border-emerald-400 focus:ring-1 focus:ring-emerald-400/30'">
                                    <svg class="w-4 h-4 absolute left-3 top-3 transition-colors" :class="reklasSubRincianKode ? 'text-emerald-400' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>

                                    <!-- Quick clear reset icon inside input -->
                                    <button type="button" 
                                            x-show="reklasSubRincianKode || searchReklasSubRincian" 
                                            @click.stop="clearReklasSubRincian(); searchReklasSubRincian = ''" 
                                            title="Hapus / Reset Pilihan"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full flex items-center justify-center text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <!-- Dropdown List Sub Rincian -->
                                <div x-show="isReklasSubRincianOpen" x-transition x-cloak style="max-height: 190px !important; overflow-y: auto !important;" class="absolute z-30 mt-1.5 w-full space-y-1 custom-scrollbar p-2 bg-slate-900/95 border border-emerald-500/50 rounded-xl shadow-2xl backdrop-blur-xl">
                                    <template x-for="s in filteredReklasSubRincian108" :key="s.kode">
                                        <div @click="selectReklasSubRincian(s)"
                                             class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between gap-2 group cursor-pointer"
                                             :class="s.kode === reklasSubRincianKode ? 'border-emerald-500 bg-emerald-950/40 shadow-md' : 'border-slate-800 hover:border-emerald-500/50 hover:bg-slate-900/80'">
                                            <div class="min-w-0 flex-1 pr-2">
                                                <h4 class="text-xs font-bold text-white group-hover:text-emerald-300 transition-colors truncate" x-text="s.kode + ' - ' + s.nama"></h4>
                                                <p class="text-[10px] text-slate-400 truncate" x-text="'SUB RINCIAN 108'"></p>
                                            </div>
                                            <button type="button" 
                                                    class="shrink-0 px-2.5 py-1 rounded-lg text-[10.5px] font-bold whitespace-nowrap transition-all"
                                                    :class="s.kode === reklasSubRincianKode ? 'bg-emerald-500 text-slate-950 shadow-xs font-extrabold' : 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 group-hover:bg-emerald-500 group-hover:text-slate-950'">
                                                <span x-text="s.kode === reklasSubRincianKode ? '✓ Terpilih' : 'Pilih →'"></span>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="isReklasSubRincianOpen && filteredReklasSubRincian108.length === 0">
                                        <div class="p-3 text-center text-xs text-slate-400 italic">
                                            Tidak ada sub rincian 108 yang cocok.
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Tingkat 3: Sub-Sub Rincian Objek PMDN 108 (Identitas Barang / Kode 108) -->
                            <div class="space-y-1.5 relative" @click.away="isReklasSubSubRincianOpen = false">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-slate-300 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 min-w-0">
                                        <span class="w-4 h-4 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/40 text-[9px] font-black flex items-center justify-center shrink-0">3</span>
                                        <span class="truncate">🔖 Identitas Barang (Sub-Sub 108):</span>
                                    </label>
                                    <div class="shrink-0 flex items-center gap-1.5 ml-auto">
                                        <button type="button" 
                                                x-show="reklasSubSubRincianKode" 
                                                @click="clearReklasSubSubRincian()" 
                                                class="px-2 py-0.5 rounded-md text-[10.5px] font-bold text-amber-300 bg-amber-500/10 hover:bg-amber-500/20 border border-amber-500/30 transition-all inline-flex items-center gap-1 cursor-pointer whitespace-nowrap active:scale-95 shadow-xs">
                                            <span>🗑️</span>
                                            <span>Kosongkan</span>
                                        </button>
                                        <button type="button" 
                                                x-show="reklasSubSubRincianKode && !isReklasSubSubRincianOpen" 
                                                @click="isReklasSubSubRincianOpen = true; searchReklasSubSubRincian = ''" 
                                                class="px-2 py-0.5 rounded-md text-[10.5px] font-bold text-purple-300 bg-purple-500/10 hover:bg-purple-500/20 border border-purple-500/30 transition-all inline-flex items-center gap-1 cursor-pointer whitespace-nowrap active:scale-95 shadow-xs">
                                            <span>✕</span>
                                            <span>Ganti</span>
                                        </button>
                                    </div>
                                </div>
                                <div class="relative">
                                    <input type="text" 
                                           :value="(!isReklasSubSubRincianOpen && reklasSubSubRincianKode) ? (reklasSubSubRincianKode + ' - ' + reklasSubSubRincianNama) : searchReklasSubSubRincian"
                                           @input="searchReklasSubSubRincian = $event.target.value; isReklasSubSubRincianOpen = true"
                                           @focus="isReklasSubSubRincianOpen = true"
                                           :placeholder="reklasSubSubRincianKode ? (reklasSubSubRincianKode + ' - ' + reklasSubSubRincianNama) : 'Ketik nama / kode barang untuk mencari spesifik...'" 
                                           class="w-full bg-slate-900 border rounded-xl px-3.5 py-2.5 pl-9 pr-9 text-xs font-bold transition-all shadow-inner"
                                           :class="reklasSubSubRincianKode && !isReklasSubSubRincianOpen ? 'border-purple-500/60 text-purple-200 ring-1 ring-purple-500/20' : 'border-slate-700 text-white focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30'">
                                    <svg class="w-4 h-4 absolute left-3 top-3 transition-colors" :class="reklasSubSubRincianKode ? 'text-purple-400' : 'text-slate-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>

                                    <!-- Quick clear reset icon inside input -->
                                    <button type="button" 
                                            x-show="reklasSubSubRincianKode || searchReklasSubSubRincian" 
                                            @click.stop="clearReklasSubSubRincian(); searchReklasSubSubRincian = ''" 
                                            title="Hapus / Reset Pilihan"
                                            class="absolute right-2.5 top-1/2 -translate-y-1/2 w-5 h-5 rounded-full flex items-center justify-center text-slate-400 hover:text-rose-400 hover:bg-slate-800 transition-all cursor-pointer">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>

                                <!-- Dropdown List Sub-Sub Rincian -->
                                <div x-show="isReklasSubSubRincianOpen" x-transition x-cloak style="max-height: 190px !important; overflow-y: auto !important;" class="absolute z-30 mt-1.5 w-full space-y-1 custom-scrollbar p-2 bg-slate-900/95 border border-purple-500/50 rounded-xl shadow-2xl backdrop-blur-xl">
                                    <template x-for="item in filteredReklasSubSubRincian108" :key="item.kode">
                                        <div @click="selectReklasSubSubRincian(item)"
                                             class="p-2.5 rounded-xl bg-slate-950 border transition-all flex items-center justify-between gap-2 group cursor-pointer"
                                             :class="item.kode === reklasSubSubRincianKode ? 'border-purple-500 bg-purple-950/40 shadow-md' : 'border-slate-800 hover:border-purple-500/50 hover:bg-slate-900/80'">
                                            <div class="min-w-0 flex-1 pr-2">
                                                <h4 class="text-xs font-bold text-white group-hover:text-purple-300 transition-colors truncate" x-text="item.kode + ' - ' + item.nama"></h4>
                                                <p class="text-[10px] text-slate-400 truncate" x-text="'KODE 108 • Identitas Barang Simda BMD'"></p>
                                            </div>
                                            <button type="button" 
                                                    class="shrink-0 px-2.5 py-1 rounded-lg text-[10.5px] font-bold whitespace-nowrap transition-all"
                                                    :class="item.kode === reklasSubSubRincianKode ? 'bg-purple-500 text-slate-950 shadow-xs font-extrabold' : 'bg-purple-500/20 text-purple-300 border border-purple-500/40 group-hover:bg-purple-500 group-hover:text-slate-950'">
                                                <span x-text="item.kode === reklasSubSubRincianKode ? '✓ Terpilih' : 'Pilih →'"></span>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="isReklasSubSubRincianOpen && filteredReklasSubSubRincian108.length === 0">
                                        <div class="p-3 text-center text-xs text-slate-400 italic">
                                            Tidak ada nama barang 108 yang cocok.
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- Placeholder Petunjuk: Tampil jika langkah 1, 2, atau 3 belum lengkap -->
                            <div x-show="!reklasTujuanKib || !reklasSubRincianKode || !reklasSubSubRincianKode" 
                                 x-transition.duration.250ms
                                 class="p-4 rounded-2xl bg-slate-950/70 border border-dashed border-slate-800 space-y-3 shadow-inner">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2 text-slate-300 text-xs font-bold">
                                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                        <span>Lengkapi Rekening Akun 108 Terlebih Dahulu</span>
                                    </div>
                                    <span class="text-[10px] text-amber-300/80 bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20 font-mono">
                                        Langkah 1, 2 &amp; 3 Wajib
                                    </span>
                                </div>
                                <p class="text-[11px] text-slate-400 leading-relaxed">
                                    Formulir spesifikasi fisik baru akan otomatis terbuka setelah Anda menyelesaikan 3 tahapan rekening kodefikasi di atas:
                                </p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-[10.5px]">
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl border transition-all"
                                         :class="reklasTujuanKib ? 'bg-blue-500/15 border-blue-500/40 text-blue-300' : 'bg-slate-900 border-slate-800 text-slate-500'">
                                        <span class="font-bold text-xs" x-text="reklasTujuanKib ? '✓' : '1.'"></span>
                                        <span class="font-bold truncate" x-text="reklasTujuanKib ? ('1. ' + reklasTujuanKib) : '1. Pilih KIB Tujuan'"></span>
                                    </div>
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl border transition-all"
                                         :class="reklasSubRincianKode ? 'bg-emerald-500/15 border-emerald-500/40 text-emerald-300' : 'bg-slate-900 border-slate-800 text-slate-500'">
                                        <span class="font-bold text-xs" x-text="reklasSubRincianKode ? '✓' : '2.'"></span>
                                        <span class="font-bold truncate" x-text="reklasSubRincianKode ? ('2. ' + reklasSubRincianKode) : '2. Sub-Rincian 108'"></span>
                                    </div>
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl border transition-all"
                                         :class="reklasSubSubRincianKode ? 'bg-purple-500/15 border-purple-500/40 text-purple-300' : 'bg-slate-900 border-slate-800 text-slate-500'">
                                        <span class="font-bold text-xs" x-text="reklasSubSubRincianKode ? '✓' : '3.'"></span>
                                        <span class="font-bold truncate" x-text="reklasSubSubRincianKode ? ('3. ' + (reklasSubSubRincianNama || reklasSubSubRincianKode)) : '3. Identitas 108'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Bagian Kemitraan: Dokumen Kerja Sama & Rekanan Mitra (Akun 1.5.2) - Non Multi-Choice -->
                            <div x-show="reklasTujuanKib === 'KEMITRAAN' && reklasSubRincianKode && reklasSubSubRincianKode" x-transition.duration.300ms class="p-4 rounded-2xl bg-slate-900 border border-cyan-500/30 space-y-3.5 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center shrink-0">4</span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                                            🤝 Dokumen Kerja Sama &amp; Rekanan Mitra (Akun 1.5.2):
                                        </span>
                                    </div>
                                    <div class="text-[10px] text-cyan-300 font-mono bg-cyan-500/10 px-2.5 py-1 rounded-lg border border-cyan-500/25 flex items-center gap-1.5 shrink-0">
                                        <span>Wujud Fisik:</span>
                                        <strong class="text-white font-extrabold uppercase" x-text="getReklasKemitraanPhysicalType() === 'tanah' ? '🌾 Tanah (KIB A)' : (getReklasKemitraanPhysicalType() === 'mesin' ? '⚙️ Mesin (KIB B)' : (getReklasKemitraanPhysicalType() === 'gedung' ? '🏢 Gedung (KIB C)' : (getReklasKemitraanPhysicalType() === 'jaringan' ? '🛣️ Jaringan (KIB D)' : '📚 Aset Tetap Lain (KIB E)')))"></strong>
                                    </div>
                                </div>

                                <div class="p-2.5 rounded-xl bg-cyan-950/20 border border-cyan-500/20 text-[11px] text-cyan-200/90 leading-relaxed flex items-center gap-2">
                                    <span>💡</span>
                                    <span>Data perjanjian kerja sama (PKS/MoU) dan pihak ketiga rekanan bersifat <strong>tunggal (single contract)</strong> untuk seluruh objek aset yang dimanfaatkan bersama mitra.</span>
                                </div>

                                <!-- 1. Nama Perusahaan Mitra / Rekanan Pihak Ketiga -->
                                <div class="relative" @click.away="isReklasMitraDropdownOpen = false">
                                    <div class="flex items-center justify-between mb-1">
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                            Nama Perusahaan Mitra / Rekanan: <span class="text-rose-400">*</span>
                                        </label>
                                        <template x-if="masterMitraList && masterMitraList.length > 0">
                                            <span class="text-[9.5px] text-cyan-400 font-mono font-normal flex items-center gap-1 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/20">
                                                <span>⚡</span>
                                                <span>Riwayat Tersimpan</span>
                                            </span>
                                        </template>
                                    </div>

                                    <!-- Input Box dengan Ikon dan Tombol Clear -->
                                    <div class="relative">
                                        <input type="text" 
                                            x-model="reklasSpekBaru.kemitraan_mitra"
                                            @focus="isReklasMitraDropdownOpen = true"
                                            @input="isReklasMitraDropdownOpen = true; onReklasMitraInput($event.target.value)"
                                            @change="onReklasMitraInput($event.target.value)"
                                            @keydown.escape="isReklasMitraDropdownOpen = false"
                                            autocomplete="off"
                                            placeholder="Ketik atau pilih mitra rekanan (contoh: PT. Kimia Farma, PT. Roche...)"
                                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3.5 py-2.5 pl-9 pr-9 text-xs text-white placeholder-slate-500 focus:outline-none font-bold shadow-inner transition-all">

                                        <!-- Ikon Mitra Perusahaan -->
                                        <svg class="w-4 h-4 text-cyan-400 absolute left-3 top-3 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                        </svg>

                                        <!-- Tombol Kosongkan Input (Clear Button) -->
                                        <button type="button" 
                                            x-show="reklasSpekBaru.kemitraan_mitra"
                                            @click="reklasSpekBaru.kemitraan_mitra = ''; isReklasMitraDropdownOpen = true" 
                                            title="Kosongkan nama mitra"
                                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                                            class="rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                                            ✕
                                        </button>
                                    </div>

                                    <!-- Floating Dropdown Saran / Filter Mitra -->
                                    <div x-show="isReklasMitraDropdownOpen" 
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 translate-y-1"
                                        x-transition:enter-end="opacity-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 translate-y-0"
                                        x-transition:leave-end="opacity-0 translate-y-1"
                                        style="max-height: 220px !important; overflow-y: auto !important;"
                                        class="absolute z-50 mt-1.5 w-full bg-slate-900 border border-cyan-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">
                                        
                                        <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                                            <span>Pilih Riwayat / Ketik Mitra Baru</span>
                                            <span class="font-mono text-slate-400" x-text="filteredReklasMitraList.length + ' saran'"></span>
                                        </div>

                                        <template x-for="(mitra, mIdx) in filteredReklasMitraList" :key="mIdx">
                                            <div @click="selectReklasMitra(mitra)"
                                                 class="px-3.5 py-2 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                                                 :class="reklasSpekBaru.kemitraan_mitra === (mitra.nama || mitra) ? 'bg-cyan-500/20 text-cyan-200' : 'text-slate-200'">
                                                <div class="flex items-center gap-2.5 min-w-0">
                                                    <span class="text-xs text-cyan-400/80">🤝</span>
                                                    <div class="min-w-0">
                                                        <span class="text-xs font-bold group-hover:text-cyan-300 truncate block" x-text="mitra.nama || mitra"></span>
                                                        <template x-if="mitra.pimpinan || mitra.alamat">
                                                            <span class="text-[10px] text-slate-400 truncate block font-normal mt-0.5" x-text="(mitra.pimpinan ? 'Pimpinan: ' + mitra.pimpinan : '') + (mitra.pimpinan && mitra.alamat ? ' • ' : '') + (mitra.alamat ? 'Alamat: ' + mitra.alamat : '')"></span>
                                                        </template>
                                                    </div>
                                                </div>
                                                <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-bold shrink-0 group-hover:bg-cyan-500/25">
                                                    Pilih &amp; Auto-fill ↵
                                                </span>
                                            </div>
                                        </template>

                                        <!-- Notifikasi jika mengetik nama mitra baru -->
                                        <template x-if="reklasSpekBaru.kemitraan_mitra && filteredReklasMitraList.length === 0">
                                            <div class="p-3 text-center text-xs text-slate-400 bg-slate-950/50">
                                                <span class="text-cyan-300 font-semibold" x-text="'➕ Gunakan Mitra Baru: &quot;' + reklasSpekBaru.kemitraan_mitra + '&quot;'"></span>
                                                <p class="text-[10px] text-slate-500 mt-0.5">Nama mitra ini akan otomatis tercatat ke riwayat kemitraan.</p>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <!-- 2. Pejabat Mitra & Alamat Domisili -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                            Pejabat Mitra / Direktur Rekanan:
                                        </label>
                                        <input type="text" x-model="reklasSpekBaru.kemitraan_pimpinan"
                                            placeholder="Nama Direktur / Penanggung Jawab Rekanan"
                                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none placeholder-slate-500 shadow-inner">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                            Alamat Kantor Domisili Mitra:
                                        </label>
                                        <input type="text" x-model="reklasSpekBaru.kemitraan_alamat"
                                            placeholder="Alamat kantor / domisili rekanan mitra"
                                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none placeholder-slate-500 shadow-inner">
                                    </div>
                                </div>

                                <!-- 3. Nomor PKS & Tanggal PKS -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                            Nomor Dokumen Perjanjian (PKS / MoU): <span class="text-rose-400">*</span>
                                        </label>
                                        <input type="text" x-model="reklasSpekBaru.kemitraan_perjanjian_no"
                                            placeholder="Contoh: 000.2.3.2/PKS-KSO/430.10.7/2026"
                                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none shadow-inner">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">
                                            Tanggal Penandatanganan PKS: <span class="text-rose-400">*</span>
                                        </label>
                                        <input type="date" x-model="reklasSpekBaru.kemitraan_tanggal_pks"
                                            class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none shadow-inner">
                                    </div>
                                </div>

                                <!-- 4. Masa Berlaku Kerjasama (Tanggal Mulai & Tanggal Selesai) -->
                                <div class="p-3.5 rounded-2xl bg-slate-950 border border-slate-800/90 space-y-2.5">
                                    <div class="flex items-center justify-between">
                                        <span class="text-[11px] font-bold text-cyan-300 flex items-center gap-1.5">
                                            <span>🗓️</span>
                                            <span>Masa Berlaku Kerja Sama (Konsesi Pemanfaatan)</span>
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-400 mb-1">
                                                Tanggal Mulai Berlaku:
                                            </label>
                                            <input type="date" x-model="reklasSpekBaru.kemitraan_tanggal_mulai"
                                                @input="calcReklasKemitraanDurasi()"
                                                @change="calcReklasKemitraanDurasi()"
                                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        </div>
                                        <div>
                                            <label class="block text-[10px] font-semibold text-slate-400 mb-1">
                                                Tanggal Berakhir (Konsesi Selesai):
                                            </label>
                                            <input type="date" x-model="reklasSpekBaru.kemitraan_tanggal_selesai"
                                                @input="calcReklasKemitraanDurasi()"
                                                @change="calcReklasKemitraanDurasi()"
                                                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-semibold text-slate-400 mb-1">
                                            Estimasi Jangka Waktu / Durasi:
                                        </label>
                                        <input type="text" x-model="reklasSpekBaru.kemitraan_jangka_waktu"
                                            placeholder="Contoh: 5 Tahun (2026 s/d 2031)"
                                            class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                    </div>
                                </div>
                            </div>

                            <!-- Tingkat 4 (atau 5 jika Kemitraan): Penyesuaian Spesifikasi Fisik Baru Sesuai KIB Tujuan -->
                            <div x-show="reklasTujuanKib && reklasSubRincianKode && reklasSubSubRincianKode" x-transition.duration.300ms class="p-4 rounded-2xl bg-slate-900 border border-cyan-500/30 space-y-3.5 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center shrink-0"
                                              x-text="reklasTujuanKib === 'KEMITRAAN' ? '5' : '4'"></span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                                            📝 Spesifikasi Fisik Baru (<span class="text-cyan-300 font-extrabold" x-text="reklasTujuanKib === 'KEMITRAAN' ? ('Fisik Objek ' + (getReklasKemitraanPhysicalType() === 'tanah' ? 'Tanah (KIB A)' : (getReklasKemitraanPhysicalType() === 'mesin' ? 'Peralatan & Mesin (KIB B)' : (getReklasKemitraanPhysicalType() === 'gedung' ? 'Gedung & Bangunan (KIB C)' : (getReklasKemitraanPhysicalType() === 'jaringan' ? 'Jalan & Jaringan (KIB D)' : 'Aset Lainnya (KIB E)'))))) : reklasTujuanKib"></span>):
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/30 font-semibold">
                                        Format Spek Dinamis
                                    </span>
                                </div>

                                <div class="p-2.5 rounded-xl bg-cyan-950/20 border border-cyan-500/20 text-[11px] text-cyan-200/90 leading-relaxed flex items-center gap-2">
                                    <span>💡</span>
                                    <span>Aset dialihkan ke kelompok <strong class="text-white" x-text="reklasTujuanKib"></strong><template x-if="reklasTujuanKib === 'KEMITRAAN'"><span> dengan wujud fisik <strong class="text-cyan-300" x-text="getReklasKemitraanPhysicalType() === 'tanah' ? 'Tanah' : (getReklasKemitraanPhysicalType() === 'mesin' ? 'Peralatan & Mesin' : (getReklasKemitraanPhysicalType() === 'gedung' ? 'Gedung & Bangunan' : (getReklasKemitraanPhysicalType() === 'jaringan' ? 'Jalan & Jaringan' : 'Aset Tetap Lainnya')))"></strong></span></template>. Data spesifikasi lama akan diarsip ke riwayat audit, dan form di bawah otomatis disesuaikan agar register &amp; KIR terbit sesuai format fisik tersebut.</span>
                                </div>

                                <!-- Multi-Item Navigation & Mass Actions (Tampil bila > 1 Item Rincian Barang) -->
                                <template x-if="reklasSpekBaruItems && reklasSpekBaruItems.length > 1">
                                    <div class="p-3 rounded-2xl bg-slate-950/80 border border-cyan-500/30 space-y-2.5 shadow-inner">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                                <span class="text-[11px] font-extrabold text-cyan-300 tracking-wide uppercase">
                                                    Pilih Rincian Barang (<span x-text="reklasSpekBaruItems.length"></span> Item):
                                                </span>
                                            </div>
                                            <!-- Tombol Salin Cepat Spesifikasi ke Seluruh Item -->
                                            <button type="button" @click="copyActiveSpekToAll()"
                                                class="px-2.5 py-1 rounded-xl bg-cyan-950/90 hover:bg-cyan-900 text-cyan-300 hover:text-white border border-cyan-500/40 hover:border-cyan-400 text-[10.5px] font-bold transition-all flex items-center justify-center gap-1.5 shadow-sm active:scale-95 cursor-pointer">
                                                <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2" />
                                                </svg>
                                                <span>⚡ Salin Spesifikasi ke Semua Item</span>
                                            </button>
                                        </div>

                                        <!-- Tab Pills: Full-Width (2-5 Item) & Scrollable (6+ Item) -->
                                        <div class="pb-1.5 custom-scrollbar"
                                             :class="reklasSpekBaruItems.length <= 5 
                                                ? ('grid gap-2 ' + (reklasSpekBaruItems.length === 2 ? 'grid-cols-2' : (reklasSpekBaruItems.length === 3 ? 'grid-cols-3' : (reklasSpekBaruItems.length === 4 ? 'grid-cols-4' : 'grid-cols-5')))) 
                                                : 'flex items-center gap-2 overflow-x-auto'">
                                            <template x-for="(uItem, uIdx) in reklasSpekBaruItems" :key="uIdx">
                                                <button type="button" @click="switchActiveSpekUnit(uIdx)"
                                                    class="px-3 py-2 rounded-xl border text-left transition-all text-xs flex flex-col justify-center cursor-pointer min-w-0"
                                                    :class="[
                                                        reklasSpekBaruItems.length > 5 ? 'shrink-0 min-w-[150px]' : 'w-full',
                                                        activeSpekUnitTab === uIdx 
                                                            ? 'bg-gradient-to-r from-cyan-950 to-slate-900 border-cyan-400 text-cyan-200 font-extrabold shadow-md shadow-cyan-950/60 ring-1 ring-cyan-400/50' 
                                                            : 'bg-slate-900/60 hover:bg-slate-900 border-slate-800 hover:border-slate-700 text-slate-400 hover:text-slate-200'
                                                    ]">
                                                    <div class="flex items-center space-x-1.5 truncate">
                                                        <span class="w-4 h-4 rounded-full text-[9px] font-black flex items-center justify-center shrink-0"
                                                              :class="activeSpekUnitTab === uIdx ? 'bg-cyan-400 text-slate-950' : 'bg-slate-800 text-slate-400'"
                                                              x-text="uIdx + 1"></span>
                                                        <span class="font-mono text-[11px] truncate font-bold" x-text="getUnitTabTitle(uIdx)"></span>
                                                    </div>
                                                    <span class="text-[9px] text-slate-400 truncate ml-5.5 mt-0.5"
                                                          x-show="getUnitTabSubtitle(uItem)"
                                                          x-text="getUnitTabSubtitle(uItem)"></span>
                                                </button>
                                            </template>
                                        </div>

                                        <!-- Info Banner Unit Aktif -->
                                        <div class="px-3 py-1.5 rounded-xl bg-slate-950/80 border border-slate-800 text-[11px] text-slate-300 flex items-center justify-between gap-2 shadow-inner">
                                            <div class="flex items-center space-x-2 truncate">
                                                <span class="w-2 h-2 rounded-full bg-cyan-400 shrink-0 animate-pulse"></span>
                                                <span class="font-bold text-white truncate" x-text="getUnitTabTitle(activeSpekUnitTab)"></span>
                                                <template x-if="getUnitFullTitle(activeSpekUnitTab) && getUnitFullTitle(activeSpekUnitTab) !== getUnitTabTitle(activeSpekUnitTab)">
                                                    <span class="text-slate-400 text-[10.5px] truncate" x-text="'(' + getUnitFullTitle(activeSpekUnitTab) + ')'"></span>
                                                </template>
                                                <template x-if="reklasSpekBaruItems[activeSpekUnitTab]?.ruang_pemegang && reklasSpekBaruItems[activeSpekUnitTab]?.ruang_pemegang !== '-'">
                                                    <span class="text-slate-400 text-[10px] truncate" x-text="'• ' + reklasSpekBaruItems[activeSpekUnitTab]?.ruang_pemegang"></span>
                                                </template>
                                            </div>
                                            <span class="text-[10px] font-mono px-2 py-0.5 rounded bg-slate-900 text-slate-300 border border-slate-700/80 shrink-0 font-semibold"
                                                  x-text="'Kondisi: ' + (reklasSpekBaruItems[activeSpekUnitTab]?.kondisi || 'Baik')"></span>
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: KIB A - Tanah (Termasuk Sewa/Kemitraan Fisik Tanah) -->
                                <template x-if="reklasTujuanKib === 'KIB A' || (reklasTujuanKib === 'KEMITRAAN' && getReklasKemitraanPhysicalType() === 'tanah')">
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-1.5 pb-1" x-show="reklasTujuanKib === 'KEMITRAAN'">
                                            <span class="text-xs">🌾</span>
                                            <span class="text-[11px] font-extrabold text-cyan-300 uppercase tracking-wider">Spesifikasi Fisik Objek Tanah Kemitraan:</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Tanah (m²):</label>
                                                <input type="number" step="0.01" min="0" x-model="reklasSpekBaru.tanah_luas_m2" placeholder="Contoh: 1500"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Status Hak Tanah:</label>
                                                <select x-model="reklasSpekBaru.tanah_hak"
                                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                    <option value="Hak Pakai">Hak Pakai</option>
                                                    <option value="Hak Milik">Hak Milik</option>
                                                    <option value="Hak Pengelolaan">Hak Pengelolaan</option>
                                                    <option value="Hak Guna Bangunan">Hak Guna Bangunan (HGB)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Sertifikat Tanah:</label>
                                                <input type="text" x-model="reklasSpekBaru.tanah_sertifikat_no" placeholder="Contoh: No. 12.04.05.001..."
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Tanggal Sertifikat:</label>
                                                <input type="date" x-model="reklasSpekBaru.tanah_sertifikat_tgl"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Penggunaan Bidang Tanah:</label>
                                                <input type="text" x-model="reklasSpekBaru.tanah_penggunaan" placeholder="Contoh: Gedung Instalasi Farmasi & Rawat Inap RSUD"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Alamat / Lokasi Fisik Tanah:</label>
                                                <input type="text" x-model="reklasSpekBaru.tanah_alamat" placeholder="Contoh: Jl. Kapten Piere Tendean No. 1 Bondowoso"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: KIB B - Peralatan & Mesin (Termasuk Sewa/Kemitraan Fisik Mesin) -->
                                <template x-if="reklasTujuanKib === 'KIB B' || (reklasTujuanKib === 'KEMITRAAN' && getReklasKemitraanPhysicalType() === 'mesin')">
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-1.5 pb-1" x-show="reklasTujuanKib === 'KEMITRAAN'">
                                            <span class="text-xs">⚙️</span>
                                            <span class="text-[11px] font-extrabold text-cyan-300 uppercase tracking-wider">Spesifikasi Fisik Peralatan & Mesin Kemitraan:</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Merk / Pabrikan:</label>
                                                <input type="text" x-model="reklasSpekBaru.mesin_merk" placeholder="Contoh: GE Healthcare / Philips / Honda"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Tipe / Model:</label>
                                                <input type="text" x-model="reklasSpekBaru.mesin_type" placeholder="Contoh: Brivo XR575 / Veradius"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Pabrik / Seri:</label>
                                                <input type="text" x-model="reklasSpekBaru.mesin_no_pabrik" placeholder="Contoh: SN-88291039"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Ukuran / Kapasitas:</label>
                                                <input type="text" x-model="reklasSpekBaru.mesin_ukuran_cc" placeholder="Contoh: 500 mA / 2000 VA / 150 cc"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Bahan / Material:</label>
                                                <input type="text" x-model="reklasSpekBaru.mesin_bahan" placeholder="Contoh: Logam, Komponen Elektronik, Kaca Optik"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Rangka / Polisi (Opsional):</label>
                                                <input type="text" x-model="reklasSpekBaru.mesin_no_polisi" placeholder="Contoh: P 1234 AP (jika kendaraan dinas)"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: KIB C - Gedung & Bangunan (Termasuk Sewa/Kemitraan Fisik Gedung) -->
                                <template x-if="reklasTujuanKib === 'KIB C' || (reklasTujuanKib === 'KEMITRAAN' && getReklasKemitraanPhysicalType() === 'gedung')">
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-1.5 pb-1" x-show="reklasTujuanKib === 'KEMITRAAN'">
                                            <span class="text-xs">🏢</span>
                                            <span class="text-[11px] font-extrabold text-cyan-300 uppercase tracking-wider">Spesifikasi Fisik Gedung & Bangunan Kemitraan:</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Bertingkat:</label>
                                                <select x-model="reklasSpekBaru.gedung_konstruksi_bertingkat"
                                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                    <option value="Bertingkat">Bertingkat (2 Lantai atau lebih)</option>
                                                    <option value="Tidak Bertingkat">Tidak Bertingkat (1 Lantai)</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Beton / Baja:</label>
                                                <select x-model="reklasSpekBaru.gedung_konstruksi_beton"
                                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                    <option value="Beton">Konstruksi Beton Bertulang</option>
                                                    <option value="Baja">Konstruksi Baja</option>
                                                    <option value="Kayu">Konstruksi Kayu</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Lantai Gedung (m²):</label>
                                                <input type="number" step="0.01" min="0" x-model="reklasSpekBaru.gedung_luas_lantai_m2" placeholder="Contoh: 450.5"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Status Kepemilikan Tanah Gedung:</label>
                                                <select x-model="reklasSpekBaru.gedung_status_tanah"
                                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                    <option value="Tanah Pemda">Tanah Milik Pemda / RSUD</option>
                                                    <option value="Hak Pakai">Hak Pakai</option>
                                                    <option value="Sewa">Sewa / Pinjam Pakai</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Dokumen PBG / IMB:</label>
                                                <input type="text" x-model="reklasSpekBaru.gedung_dokumen_nomor" placeholder="Contoh: 640/IMB/DPMPTSP/2026"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Tanggal Dokumen IMB:</label>
                                                <input type="date" x-model="reklasSpekBaru.gedung_dokumen_tgl"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Alamat / Letak Gedung di RSUD:</label>
                                                <input type="text" x-model="reklasSpekBaru.gedung_alamat" placeholder="Contoh: Sayap Barat RSUD Dr. H. Koesnandi"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: KIB D - Jalan, Jaringan & Irigasi (Termasuk Sewa/Kemitraan Fisik Jaringan) -->
                                <template x-if="reklasTujuanKib === 'KIB D' || (reklasTujuanKib === 'KEMITRAAN' && getReklasKemitraanPhysicalType() === 'jaringan')">
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-1.5 pb-1" x-show="reklasTujuanKib === 'KEMITRAAN'">
                                            <span class="text-xs">🛣️</span>
                                            <span class="text-[11px] font-extrabold text-cyan-300 uppercase tracking-wider">Spesifikasi Fisik Jalan & Jaringan Kemitraan:</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Konstruksi Fisik Jaringan:</label>
                                                <input type="text" x-model="reklasSpekBaru.jaringan_konstruksi" placeholder="Contoh: Aspal Hotmix / Paving Blok / Pipa HDPE"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Luas Jaringan (m²):</label>
                                                <input type="number" step="0.01" min="0" x-model="reklasSpekBaru.jaringan_luas_m2" placeholder="Contoh: 1200"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Panjang (m/km):</label>
                                                <input type="text" x-model="reklasSpekBaru.jaringan_panjang_km" placeholder="Contoh: 350 Meter"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Lebar (Meter):</label>
                                                <input type="text" x-model="reklasSpekBaru.jaringan_lebar_m" placeholder="Contoh: 6 Meter"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: KIB E - Aset Tetap Lainnya (Termasuk Sewa/Kemitraan Fisik Lainnya) -->
                                <template x-if="reklasTujuanKib === 'KIB E' || (reklasTujuanKib === 'KEMITRAAN' && getReklasKemitraanPhysicalType() === 'lainnya')">
                                    <div class="space-y-3">
                                        <div class="flex items-center space-x-1.5 pb-1" x-show="reklasTujuanKib === 'KEMITRAAN'">
                                            <span class="text-xs">📚</span>
                                            <span class="text-[11px] font-extrabold text-cyan-300 uppercase tracking-wider">Spesifikasi Fisik Aset Tetap Lainnya Kemitraan:</span>
                                        </div>
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Judul / Pencipta / Spesifikasi:</label>
                                                <input type="text" x-model="reklasSpekBaru.lainnya_judul_pencipta" placeholder="Judul buku, lukisan, atau instrumen musik..."
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Bahan / Asal Usul:</label>
                                                <input type="text" x-model="reklasSpekBaru.lainnya_bahan" placeholder="Contoh: Kanvas / Perunggu / Kayu Jati"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: ATB - Aset Tak Berwujud -->
                                <template x-if="reklasTujuanKib === 'ATB'">
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nama Software / Sistem Informasi:</label>
                                                <input type="text" x-model="reklasSpekBaru.atb_nama_software" placeholder="Contoh: Modul SIMRS Radiologi & Bridging BPJS"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Vendor / Pengembang Software:</label>
                                                <input type="text" x-model="reklasSpekBaru.atb_pengembang" placeholder="Contoh: PT Medika Teknologi Solusindo"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Estimasi Masa Manfaat (Tahun):</label>
                                                <input type="number" min="1" max="20" x-model="reklasSpekBaru.atb_masa_manfaat" placeholder="4"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Registrasi Lisensi / HAKI:</label>
                                                <input type="text" x-model="reklasSpekBaru.atb_nomor_lisensi" placeholder="Contoh: LIC-SIMRS-2026-009"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: ASET LAIN - Aset Lain-Lain (1.5.4) -->
                                <template x-if="reklasTujuanKib === 'ASET LAIN' || reklasTujuanKib === 'ASET LAIN-LAIN'">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Kondisi Fisik Barang:</label>
                                            <select x-model="reklasSpekBaru.aset_lain_kondisi"
                                                    class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                <option value="Rusak Berat (Menunggu Penghapusan)">Rusak Berat (Menunggu Usulan Penghapusan)</option>
                                                <option value="Tidak Dapat Digunakan Lagi">Tidak Dapat Digunakan Lagi / Usang</option>
                                                <option value="Hilang / Tidak Ditemukan">Hilang / Tidak Ditemukan</option>
                                                <option value="Sengketa / Status Hukum">Sengketa / Status Hukum</option>
                                            </select>
                                        </div>
                                    </div>
                                </template>
                            </div>

                            <div class="p-2.5 rounded-xl bg-indigo-950/30 border border-indigo-500/20 flex items-start gap-2.5">
                                <span class="text-sm shrink-0">💡</span>
                                <p class="text-[11px] leading-relaxed text-slate-300">
                                    <strong class="text-indigo-300 font-semibold">Petunjuk:</strong> Pilih <strong class="text-white">KIB tujuan</strong> terlebih dahulu, tentukan <strong class="text-white">Sub-Rincian</strong> &amp; <strong class="text-white">Nama Barang 108</strong>, serta sesuaikan rincian spesifikasi fisik di atas.
                                </p>
                            </div>
                        </div>


                        <!-- Jika KDP: Pilihan KIB Tujuan Definitif & Kunci Progres Fisik 100% -->
                        <div x-show="reklasJenis === 'kdp'" class="space-y-3.5">
                            <!-- 1. Pilihan KIB Tujuan Definitif -->
                            <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-rose-500/30 shadow-lg shadow-rose-950/20 space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-rose-300 font-bold text-[11px] uppercase tracking-wider flex items-center space-x-1.5">
                                        <span>🏗️</span>
                                        <span>Alihkan KDP Selesai ke KIB Definitif:</span>
                                    </label>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/40">
                                        KIB F &rarr; Definitif
                                    </span>
                                </div>
                                <select x-model="reklasTujuanKib"
                                        class="w-full bg-slate-900 border border-rose-500/40 rounded-xl px-3 py-2.5 text-xs font-bold text-white focus:outline-none focus:border-rose-400 focus:ring-1 focus:ring-rose-400/50 transition-all cursor-pointer">
                                    <option value="KIB C">🏛️ KIB C - Gedung &amp; Bangunan (Definitif)</option>
                                    <option value="KIB D">🛣️ KIB D - Jalan, Jaringan &amp; Irigasi (Definitif)</option>
                                </select>
                                <p class="text-[10px] text-slate-400 leading-relaxed">
                                    Akumulasi belanja modal KDP akan dikapitalisasi dan dibukukan sebagai aset tetap definitif pada kelompok KIB di atas.
                                </p>
                            </div>

                            <!-- 2. Panel Transformasi Progres Fisik Konstruksi (Otomatis 100% Selesai) -->
                            <div class="p-4 rounded-2xl bg-gradient-to-br from-emerald-950/40 via-slate-950/70 to-slate-900 border border-emerald-500/40 shadow-xl shadow-emerald-950/30 space-y-3">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="p-1.5 rounded-lg bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs">
                                            ✅
                                        </span>
                                        <div>
                                            <h4 class="text-xs font-black text-white tracking-wide">Transformasi Progres Fisik KDP</h4>
                                            <p class="text-[10px] text-emerald-300/80">Syarat mutlak kapitalisasi KDP ke aset definitif</p>
                                        </div>
                                    </div>
                                    <span class="inline-flex items-center space-x-1 px-2.5 py-1 rounded-full text-[10px] font-black bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 shadow-sm animate-pulse">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                        <span>DIKUNCI 100%</span>
                                    </span>
                                </div>

                                <!-- Komparasi Progres Semula vs Progres Baru -->
                                <div class="grid grid-cols-1 sm:grid-cols-11 gap-2.5 items-center bg-slate-900/80 p-3 rounded-xl border border-slate-800">
                                    <!-- Progres Awal -->
                                    <div class="sm:col-span-5 bg-slate-950/80 p-2.5 rounded-lg border border-slate-800/80 flex items-center justify-between">
                                        <div>
                                            <span class="text-[9.5px] font-bold text-slate-400 uppercase tracking-wider block">Progres Semula (KIB F):</span>
                                            <span class="text-xs font-extrabold text-amber-300 font-mono" x-text="(reklasKdpProgresAwal || 0) + '% Fisik'"></span>
                                        </div>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-semibold">
                                            Dalam Pengerjaan
                                        </span>
                                    </div>

                                    <!-- Indikator Panah -->
                                    <div class="sm:col-span-1 text-center py-1 sm:py-0">
                                        <span class="text-emerald-400 font-black text-sm">&rarr;</span>
                                    </div>

                                    <!-- Progres Baru (100%) -->
                                    <div class="sm:col-span-5 bg-emerald-950/40 p-2.5 rounded-lg border border-emerald-500/40 flex items-center justify-between">
                                        <div>
                                            <span class="text-[9.5px] font-bold text-emerald-300 uppercase tracking-wider block">Progres Baru (Definitif):</span>
                                            <span class="text-xs font-black text-emerald-200 font-mono">100% Selesai Penuh</span>
                                        </div>
                                        <span class="text-[10px] px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 font-bold">
                                            Terbit BAST 100%
                                        </span>
                                    </div>
                                </div>

                                <!-- Progress Bar Visual 100% -->
                                <div class="space-y-1">
                                    <div class="flex justify-between text-[10px] font-medium text-slate-400">
                                        <span>Tingkat Kesiapan Aset:</span>
                                        <span class="font-mono font-bold text-emerald-300">100% (Pekerjaan Fisik Selesai)</span>
                                    </div>
                                    <div class="w-full bg-slate-950 rounded-full h-2.5 p-0.5 border border-slate-800 overflow-hidden">
                                        <div class="bg-gradient-to-r from-emerald-500 via-teal-400 to-cyan-400 h-1.5 rounded-full w-full transition-all duration-500 shadow-sm shadow-emerald-500/50"></div>
                                    </div>
                                </div>

                                <!-- Catatan Akuntansi & Neraca RSUD -->
                                <div class="p-2.5 rounded-xl bg-slate-950/70 border border-slate-800 text-[10.5px] text-slate-300 leading-relaxed flex items-start space-x-2">
                                    <span class="text-emerald-400 text-xs shrink-0 mt-0.5">ℹ️</span>
                                    <div>
                                        <strong class="text-white">Dampak Buku &amp; Neraca:</strong> Saldo <span class="text-rose-300 font-mono font-bold">KIB F (Konstruksi)</span> akan dinihilkan / berkurang (-) di neraca, dan berpindah menjadi <span class="text-emerald-300 font-mono font-bold" x-text="reklasTujuanKib || 'KIB Definitif'"></span> aktif (+) yang siap dioperasikan serta dihitung penyusutannya.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Jika Koreksi Nilai / Audit BPK (Koreksi Lain-Lain) -->
                        <div x-show="reklasJenis === 'koreksi_nilai'" class="space-y-4">
                            <!-- 1. Pilihan 3 Sub-Kategori Koreksi Nilai Resmi -->
                            <div class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-cyan-300 font-extrabold text-[11px] uppercase tracking-wider flex items-center gap-1.5">
                                        <span>⚖️</span>
                                        <span>Sub-Kategori Koreksi Nilai:</span>
                                        <span class="text-rose-400">*</span>
                                    </label>
                                    <span class="text-[9.5px] font-mono font-bold text-slate-400">Pilih klasifikasi audit / rekon</span>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                                    <!-- A. Koreksi Biasa (Internal RSUD) -->
                                    <label class="relative flex flex-col p-3 rounded-2xl border cursor-pointer transition-all select-none group"
                                           :class="reklasSubKoreksi === 'biasa' 
                                                ? 'bg-indigo-500/15 border-indigo-500/60 shadow-lg shadow-indigo-500/10 ring-1 ring-indigo-500/40' 
                                                : 'bg-slate-950/70 border-slate-800 hover:border-slate-700'">
                                        <input type="radio" name="reklas_sub_koreksi" value="biasa" x-model="reklasSubKoreksi" class="hidden" style="display: none;">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                                                     :class="reklasSubKoreksi === 'biasa' ? 'border-indigo-400 bg-indigo-500/30' : 'border-slate-700 bg-slate-900'">
                                                    <div x-show="reklasSubKoreksi === 'biasa'" class="w-1.5 h-1.5 rounded-full bg-indigo-400"></div>
                                                </div>
                                                <span class="text-xs font-black" :class="reklasSubKoreksi === 'biasa' ? 'text-indigo-300' : 'text-white'">
                                                    Koreksi Biasa
                                                </span>
                                            </div>
                                            <span class="text-xs">📝</span>
                                        </div>
                                        <div class="text-[9.5px] text-slate-400 leading-tight">
                                            Internal kas RSUD, pembulatan SP2D, koreksi salah catat belanja.
                                        </div>
                                    </label>

                                    <!-- B. Koreksi LKD (BPK RI) -->
                                    <label class="relative flex flex-col p-3 rounded-2xl border cursor-pointer transition-all select-none group"
                                           :class="reklasSubKoreksi === 'lkd' 
                                                ? 'bg-cyan-500/15 border-cyan-500/60 shadow-lg shadow-cyan-500/10 ring-1 ring-cyan-500/40' 
                                                : 'bg-slate-950/70 border-slate-800 hover:border-slate-700'">
                                        <input type="radio" name="reklas_sub_koreksi" value="lkd" x-model="reklasSubKoreksi" class="hidden" style="display: none;">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                                                     :class="reklasSubKoreksi === 'lkd' ? 'border-cyan-400 bg-cyan-500/30' : 'border-slate-700 bg-slate-900'">
                                                    <div x-show="reklasSubKoreksi === 'lkd'" class="w-1.5 h-1.5 rounded-full bg-cyan-400"></div>
                                                </div>
                                                <span class="text-xs font-black" :class="reklasSubKoreksi === 'lkd' ? 'text-cyan-300' : 'text-white'">
                                                    Koreksi LKD
                                                </span>
                                            </div>
                                            <span class="text-xs">⚖️</span>
                                        </div>
                                        <div class="text-[9.5px] text-slate-400 leading-tight">
                                            Temuan audit BPK RI, rekomendasi LHP LKPD, kelebihan bayar/TGR.
                                        </div>
                                    </label>

                                    <!-- C. Koreksi Manset (BPKAD) -->
                                    <label class="relative flex flex-col p-3 rounded-2xl border cursor-pointer transition-all select-none group"
                                           :class="reklasSubKoreksi === 'manset' 
                                                ? 'bg-emerald-500/15 border-emerald-500/60 shadow-lg shadow-emerald-500/10 ring-1 ring-emerald-500/40' 
                                                : 'bg-slate-950/70 border-slate-800 hover:border-slate-700'">
                                        <input type="radio" name="reklas_sub_koreksi" value="manset" x-model="reklasSubKoreksi" class="hidden" style="display: none;">
                                        <div class="flex items-center justify-between mb-1.5">
                                            <div class="flex items-center space-x-2">
                                                <div class="w-4 h-4 rounded-full border flex items-center justify-center transition-all"
                                                     :class="reklasSubKoreksi === 'manset' ? 'border-emerald-400 bg-emerald-500/30' : 'border-slate-700 bg-slate-900'">
                                                    <div x-show="reklasSubKoreksi === 'manset'" class="w-1.5 h-1.5 rounded-full bg-emerald-400"></div>
                                                </div>
                                                <span class="text-xs font-black" :class="reklasSubKoreksi === 'manset' ? 'text-emerald-300' : 'text-white'">
                                                    Koreksi Manset
                                                </span>
                                            </div>
                                            <span class="text-xs">🏢</span>
                                        </div>
                                        <div class="text-[9.5px] text-slate-400 leading-tight">
                                            Penyelarasan SIMDA BMD / E-Manset BPKAD Kab. Bondowoso.
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- 2. Dynamic Info Banner & Pemetaan Kolom RMB -->
                            <div class="p-3 rounded-2xl border text-[11px] leading-relaxed flex items-start gap-2.5 transition-all shadow-inner"
                                 :class="{
                                     'bg-indigo-950/30 border-indigo-500/35 text-indigo-200': reklasSubKoreksi === 'biasa',
                                     'bg-cyan-950/30 border-cyan-500/35 text-cyan-200': reklasSubKoreksi === 'lkd',
                                     'bg-emerald-950/30 border-emerald-500/35 text-emerald-200': reklasSubKoreksi === 'manset'
                                 }">
                                <span class="text-base shrink-0">💡</span>
                                <div class="flex-1">
                                    <template x-if="reklasSubKoreksi === 'biasa'">
                                        <div>
                                            <strong>Koreksi Biasa (Internal RSUD):</strong> Digunakan jika ada kesalahan input nominal, pembulatan SP2D, atau selisih pencatatan antara bendahara pengeluaran dan aset.
                                            Di matriks RMB 21 kolom, pengurangan akan masuk ke <strong>Kolom 15 (KOREKSI −)</strong> dan penambahan ke <strong>Kolom 5 (Koreksi Rekening +)</strong>.
                                        </div>
                                    </template>
                                    <template x-if="reklasSubKoreksi === 'lkd'">
                                        <div>
                                            <strong>Koreksi LKD (BPK RI):</strong> Digunakan untuk menindaklanjuti temuan resmi LHP BPK atas LKPD, kelebihan bayar yang telah disetor ke Kasda, atau koreksi kapitalisasi auditor eksternal.
                                            Di matriks RMB 21 kolom, pengurangan akan masuk ke <strong>Kolom 16 (KOREKSI LKD −)</strong> dan penambahan ke <strong>Kolom 6 (Koreksi LKD +)</strong>.
                                        </div>
                                    </template>
                                    <template x-if="reklasSubKoreksi === 'manset'">
                                        <div>
                                            <strong>Koreksi Manset (Bidang Aset BPKAD):</strong> Digunakan untuk menyelaraskan nilai saldo aset antara SIMAT RSUD dengan aplikasi SIMDA BMD / E-Manset BPKAD Bondowoso.
                                            Di matriks RMB 21 kolom, pengurangan akan masuk ke <strong>Kolom 17 (KOREKSI MANSET −)</strong> dan penambahan ke <strong>Kolom 7 (Koreksi Manset +)</strong>.
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 3. Dokumen Dasar Penyesuaian (Contextual Inputs) -->
                            <div class="p-3.5 rounded-2xl bg-slate-950/70 border border-slate-800 space-y-3">
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                                    <span>📄</span>
                                    <span>Dokumen Dasar Penyesuaian Nilai:</span>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                    <div class="sm:col-span-8 space-y-1">
                                        <label class="block text-[10px] font-bold text-slate-300"
                                               x-text="reklasSubKoreksi === 'lkd' ? 'Nomor LHP BPK RI / Rekomendasi' : (reklasSubKoreksi === 'manset' ? 'Nomor BA Rekonsiliasi Manset BPKAD' : 'Nomor Dokumen / Nota Rekonsiliasi Internal')"></label>
                                        <input type="text" x-model="reklasNoDokumenKoreksi"
                                               :placeholder="reklasSubKoreksi === 'lkd' ? 'Contoh: 12/LHP/XVIII.SBY/05/2026' : (reklasSubKoreksi === 'manset' ? 'Contoh: 000.2/BA-MANSET/BPKAD/2026' : 'Contoh: 000.1.2/BA-REKON/RSUD/2026')"
                                               class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl text-xs font-mono text-white focus:outline-none">
                                    </div>
                                    <div class="sm:col-span-4 space-y-1">
                                        <label class="block text-[10px] font-bold text-slate-300">Tanggal Dokumen</label>
                                        <input type="date" x-model="reklasDokumenTglKoreksi"
                                               class="w-full px-3 py-1.5 bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl text-xs font-mono text-white focus:outline-none">
                                    </div>
                                </div>
                            </div>

                            <!-- 1. Grid Anggaran & Realisasi (Kondisional: Anggaran hanya di Belanja Modal & Di-Lock) -->
                            <div class="grid gap-3"
                                 :class="isReklasBelanjaModal() ? 'grid-cols-1 sm:grid-cols-2' : 'grid-cols-1'">
                                <!-- Nilai Anggaran (DPA/RBA) - HANYA TAMPIL DI BELANJA MODAL & TERKUNCI (READ-ONLY) -->
                                <template x-if="isReklasBelanjaModal()">
                                    <div class="p-3.5 rounded-xl bg-slate-950/70 border border-slate-800 space-y-2">
                                        <div class="flex items-center justify-between">
                                            <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider flex items-center gap-1.5">
                                                <span>🔒</span>
                                                <span>Nilai Anggaran (DPA/RBA):</span>
                                            </label>
                                        </div>
                                        <div class="relative">
                                            <span class="absolute left-3 top-2 text-slate-500 text-xs font-mono font-bold">Rp</span>
                                            <input type="text" readonly
                                                   :value="Number(reklasNilaiAnggaran || 0).toLocaleString('id-ID')"
                                                   class="w-full pl-9 pr-3.5 py-1.5 bg-slate-900/60 border border-slate-700/80 rounded-xl text-xs font-mono font-bold text-slate-300 cursor-not-allowed text-right select-none focus:outline-none">
                                        </div>
                                        <p class="text-[9.5px] text-slate-500 leading-tight">Pagu awal DPA APBD/BLUD bersifat permanen sebagai dokumen otorisasi awal.</p>
                                    </div>
                                </template>

                                <!-- Nilai Realisasi Aset -->
                                <div class="p-3.5 rounded-xl bg-emerald-950/30 border border-emerald-500/40 space-y-2"
                                     :class="!isReklasBelanjaModal() ? 'w-full' : ''">
                                    <div class="flex items-center justify-between">
                                        <label class="block text-emerald-300 font-bold text-[10.5px] uppercase tracking-wider flex items-center gap-1.5">
                                            <span>📦</span>
                                            <span>Total Nilai Realisasi Aset:</span>
                                        </label>
                                        <template x-if="!isReklasBelanjaModal()">
                                            <span class="px-2 py-0.5 rounded text-[9px] font-mono font-bold bg-cyan-950/80 border border-cyan-800/80 text-cyan-300"
                                                  x-text="selectedAstapReklas?.sumber_dana === 'hibah' ? 'Perolehan Hibah (Non-DPA)' : (selectedAstapReklas?.sumber_dana === 'kemitraan' ? 'Perolehan Kemitraan (Non-DPA)' : 'Perolehan Non-DPA')">
                                            </span>
                                        </template>
                                    </div>
                                    <div class="relative">
                                        <span class="absolute left-3 top-2 text-slate-400 text-xs font-mono font-bold">Rp</span>
                                        <input type="text" readonly :value="Number(reklasNilaiRealisasiBaru || 0).toLocaleString('id-ID')"
                                               class="w-full pl-9 pr-3 py-1.5 bg-slate-950 border border-emerald-500/40 rounded-xl text-xs font-mono font-extrabold text-emerald-300 cursor-not-allowed text-right focus:outline-none select-none">
                                    </div>
                                    <p class="text-[9.5px] text-slate-400 leading-tight">Terkalkulasi otomatis dari akumulasi nilai barang (Barang 1, 2, dst) di bawah.</p>
                                </div>
                            </div>

                            <!-- 2. Rincian Barang & Nilai Kapitalisasi (Barang 1, Barang 2, dst) -->
                            <div class="space-y-3 pt-2">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1.5 pb-2 border-b border-slate-800/80">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-xs font-black text-cyan-300 uppercase tracking-wider flex items-center gap-1.5">
                                            <span>📦</span>
                                            <span>Rincian Nilai Kapitalisasi Barang:</span>
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-mono font-extrabold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 shadow-xs"
                                              x-text="reklasExtracomItems.length + ' Item Terdaftar'"></span>
                                    </div>
                                    <span class="text-[10.5px] text-slate-400 font-medium">Ubah nilai per unit barang temuan</span>
                                </div>

                                <!-- Cards Container Barang 1 & 2 -->
                                <div class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar pr-1">
                                    <template x-for="(item, idx) in reklasExtracomItems" :key="idx">
                                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/90 hover:border-cyan-500/40 transition-all space-y-3 shadow-lg group backdrop-blur-sm">
                                            <!-- Baris Atas Item: Badge Nomor & Nama Barang & Volume -->
                                            <div class="flex items-center justify-between gap-2.5">
                                                <div class="flex items-center gap-2 min-w-0 flex-1">
                                                    <span class="px-2.5 py-1 rounded-lg text-[10.5px] font-mono font-black bg-cyan-500/15 text-cyan-300 border border-cyan-500/35 shrink-0 shadow-xs"
                                                          x-text="'Barang #' + (idx + 1)"></span>
                                                    <span class="text-xs sm:text-sm font-extrabold text-white truncate" 
                                                          :title="item.nama_barang || ('Barang #' + (idx + 1))"
                                                          x-text="item.nama_barang || ('Barang #' + (idx + 1))"></span>
                                                </div>
                                                <div class="px-2.5 py-1 rounded-lg bg-slate-900 border border-slate-800 text-slate-300 text-[11px] font-mono font-semibold shrink-0 shadow-inner flex items-center gap-1.5">
                                                    <span class="text-slate-500 text-[10px]">Qty:</span>
                                                    <span class="text-cyan-300 font-bold" x-text="item.jumlah_volume || 1"></span>
                                                    <span class="text-slate-400 text-[10px]" x-text="item.satuan || 'Unit'"></span>
                                                </div>
                                            </div>

                                            <!-- Grid Input: Nilai Kapitalisasi Satuan & Subtotal (Symmetric & Responsive) -->
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 items-start pt-3 border-t border-slate-800/80">
                                                <!-- Nilai Satuan -->
                                                <div class="space-y-1.5">
                                                    <label class="flex items-center gap-1.5 text-slate-400 font-bold text-[10px] uppercase tracking-wider h-4">
                                                        <span>🏷️</span>
                                                        <span>Nilai Satuan (Rp):</span>
                                                    </label>
                                                    <div class="relative flex items-center h-10 rounded-xl bg-slate-900/90 border border-cyan-500/40 focus-within:border-cyan-400 focus-within:ring-2 focus-within:ring-cyan-500/20 shadow-inner transition-all overflow-hidden">
                                                        <span class="h-full px-3 flex items-center justify-center bg-slate-950/80 border-r border-slate-800 text-cyan-400 font-mono font-bold text-xs select-none">
                                                            Rp
                                                        </span>
                                                        <input type="text"
                                                               inputmode="numeric"
                                                               :value="formatRupiahInput(item.harga_satuan)"
                                                               @input="updateItemHargaSatuan(item, $event.target.value)"
                                                               @focus="$event.target.select()"
                                                               placeholder="0"
                                                               class="w-full h-full bg-transparent px-3 text-xs sm:text-sm font-mono font-extrabold text-cyan-200 focus:outline-none text-right placeholder-slate-600">
                                                    </div>
                                                </div>

                                                <!-- Subtotal -->
                                                <div class="space-y-1.5">
                                                    <div class="flex items-center justify-between text-slate-400 font-bold text-[10px] uppercase tracking-wider h-4">
                                                        <span>Subtotal Barang:</span>
                                                        <span class="text-[9.5px] font-mono text-slate-500 font-normal lowercase" x-text="'(' + (item.jumlah_volume || 1) + ' ' + (item.satuan || 'unit') + ')'"></span>
                                                    </div>
                                                    <div class="flex items-center justify-between px-3.5 h-10 rounded-xl bg-emerald-950/30 border border-emerald-500/40 shadow-inner">
                                                        <span class="text-[9.5px] font-mono font-bold text-emerald-400/80 uppercase tracking-wider">Subtotal</span>
                                                        <div class="text-xs sm:text-sm font-mono font-black text-emerald-300"
                                                             x-text="'Rp ' + Number((item.jumlah_volume || 1) * (parseFloat(item.harga_satuan) || 0)).toLocaleString('id-ID')">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <!-- 3. Ringkasan Perbandingan & Dampak Selisih Koreksi -->
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
                        </div>

                        <!-- Jika Ekstrakomptabel ATAU Kapitalisasi Intrakomptabel: Form Penyesuaian Harga Satuan per Item -->
                        <div x-show="reklasJenis === 'extracom' || reklasJenis === 'intracom'" class="space-y-3">
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
                                            <!-- 1. Nama Barang (Cols 4) -->
                                            <div class="sm:col-span-4">
                                                <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">Nama Barang:</label>
                                                <input type="text" x-model="item.nama_barang" placeholder="Nama barang..."
                                                       class="w-full bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none font-medium"
                                                       :class="reklasJenis === 'intracom' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                                            </div>

                                            <!-- 2. Volume & Satuan (Cols 4 - Diperluas) -->
                                            <div class="sm:col-span-4">
                                                <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider mb-1">Volume &amp; Satuan:</label>
                                                <div class="flex items-center space-x-1.5">
                                                    <input type="number" min="1" x-model.number="item.jumlah_volume" placeholder="1"
                                                           @input="if (item.jumlah_volume !== undefined && item.jumlah_volume !== null && item.jumlah_volume < 1 && item.jumlah_volume !== '') item.jumlah_volume = 1;"
                                                           title="Jumlah Volume"
                                                           class="w-12 shrink-0 bg-slate-950 border border-slate-700 rounded-lg px-1.5 py-1.5 text-xs text-white text-center focus:outline-none font-bold no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                                                           :class="reklasJenis === 'intracom' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                                                    <input type="text" x-model="item.satuan" placeholder="Satuan..."
                                                           title="Satuan Barang"
                                                           class="flex-1 min-w-0 bg-slate-950 border border-slate-700 rounded-lg px-2.5 py-1.5 text-xs text-white focus:outline-none"
                                                           :class="reklasJenis === 'intracom' ? 'focus:border-emerald-500' : 'focus:border-amber-500'">
                                                </div>
                                            </div>

                                            <!-- 3. Harga Satuan (Rp) (Cols 4) -->
                                            <div class="sm:col-span-4">
                                                <div class="mb-1">
                                                    <label class="block font-bold text-[10px] uppercase tracking-wider"
                                                           :class="reklasJenis === 'intracom' ? 'text-emerald-300' : 'text-amber-300'">Harga Satuan (Rp):</label>
                                                </div>
                                                <div class="relative flex items-center rounded-xl bg-slate-950 border focus-within:ring-2 shadow-inner transition-all overflow-hidden"
                                                     :class="reklasJenis === 'intracom'
                                                         ? (parseFloat(item.harga_satuan) <= 300000 ? 'border-rose-500 text-rose-300 focus-within:border-rose-400 focus-within:ring-rose-500/20' : 'border-slate-700 text-emerald-400 focus-within:border-emerald-500 focus-within:ring-emerald-500/20')
                                                         : (parseFloat(item.harga_satuan) > 300000 ? 'border-rose-500 text-rose-300 focus-within:border-rose-400 focus-within:ring-rose-500/20' : (parseFloat(item.harga_satuan) <= 0 ? 'border-amber-500/60 text-amber-300 focus-within:border-amber-400 focus-within:ring-amber-500/20' : 'border-slate-700 text-emerald-400 focus-within:border-emerald-500 focus-within:ring-emerald-500/20'))">
                                                    <span class="px-3 py-2 bg-slate-900 border-r border-slate-800 text-slate-400 font-mono font-bold text-xs select-none">
                                                        Rp
                                                    </span>
                                                    <input type="text"
                                                           inputmode="numeric"
                                                           :value="formatRupiahInput(item.harga_satuan)"
                                                           @input="updateItemHargaSatuan(item, $event.target.value)"
                                                           @focus="$event.target.select()"
                                                           placeholder="0"
                                                           class="w-full bg-transparent px-3 py-2 text-xs font-mono font-extrabold focus:outline-none text-right transition-colors"
                                                           :class="reklasJenis === 'intracom'
                                                                ? (parseFloat(item.harga_satuan) <= 300000 ? 'text-rose-300 placeholder-rose-700' : 'text-emerald-400 placeholder-slate-600')
                                                                : (parseFloat(item.harga_satuan) > 300000 ? 'text-rose-300 placeholder-rose-700' : (parseFloat(item.harga_satuan) <= 0 ? 'text-amber-300 placeholder-amber-700' : 'text-emerald-400 placeholder-slate-600'))">
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

                        <!-- Jika Hibah (Bantuan Pemerintah) -->
                        <div x-show="reklasJenis === 'hibah'" class="space-y-4">
                            <!-- Toggle Mode Hibah: Keluar vs Masuk -->
                            <div class="space-y-1.5">
                                <label class="block text-purple-300 font-bold text-xs uppercase tracking-wider">
                                    🎁 Arah Alur Transaksi Hibah:
                                </label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                    <label class="relative flex items-center p-3 rounded-xl border cursor-pointer transition-all select-none"
                                           :class="reklasTipeHibah === 'keluar' ? 'bg-purple-500/15 border-purple-500/50 shadow-sm shadow-purple-500/10' : 'bg-slate-900 border-slate-800 hover:border-slate-700'">
                                        <input type="radio" name="reklas_tipe_hibah" value="keluar" x-model="reklasTipeHibah" class="hidden">
                                        <div class="flex items-center justify-center rounded-full border shrink-0 mr-2.5"
                                             style="width: 16px; height: 16px;"
                                             :class="reklasTipeHibah === 'keluar' ? 'border-purple-400 bg-purple-500/20' : 'border-slate-700 bg-slate-950'">
                                            <div x-show="reklasTipeHibah === 'keluar'" class="rounded-full bg-purple-400" style="width: 6px; height: 6px;"></div>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-xs" :class="reklasTipeHibah === 'keluar' ? 'text-purple-300' : 'text-white'">📤 Hibah Keluar (Aset Diserahkan)</div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">Aset RSUD diserahkan ke Puskesmas/Desa/Instansi lain</div>
                                        </div>
                                    </label>

                                    <label class="relative flex items-center p-3 rounded-xl border cursor-pointer transition-all select-none"
                                           :class="reklasTipeHibah === 'masuk' ? 'bg-purple-500/15 border-purple-500/50 shadow-sm shadow-purple-500/10' : 'bg-slate-900 border-slate-800 hover:border-slate-700'">
                                        <input type="radio" name="reklas_tipe_hibah" value="masuk" x-model="reklasTipeHibah" class="hidden">
                                        <div class="flex items-center justify-center rounded-full border shrink-0 mr-2.5"
                                             style="width: 16px; height: 16px;"
                                             :class="reklasTipeHibah === 'masuk' ? 'border-purple-400 bg-purple-500/20' : 'border-slate-700 bg-slate-950'">
                                            <div x-show="reklasTipeHibah === 'masuk'" class="rounded-full bg-purple-400" style="width: 6px; height: 6px;"></div>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="font-bold text-xs" :class="reklasTipeHibah === 'masuk' ? 'text-purple-300' : 'text-white'">📥 Hibah Masuk (Bantuan Diterima)</div>
                                            <div class="text-[10px] text-slate-400 mt-0.5">RSUD menerima bantuan alat/hibah dari Kemenkes/CSR</div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Field Informasi BAST Hibah -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                        📄 Nomor Dokumen BAST Hibah: <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="reklasNomorBastHibah" placeholder="Contoh: 028/BAST-HB/430.10.7/2026..."
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs font-mono text-purple-200 focus:outline-none focus:border-purple-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                        📅 Tanggal BAST Hibah: <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="date" x-model="reklasTanggalBastHibah"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs font-mono text-white focus:outline-none focus:border-purple-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider"
                                           x-text="reklasTipeHibah === 'keluar' ? '🏢 Instansi / Pihak Penerima Hibah:' : '🏢 Instansi / Pihak Pemberi Hibah:'">
                                    </label>
                                    <input type="text" x-model="reklasPihakHibah" :placeholder="reklasTipeHibah === 'keluar' ? 'Contoh: Puskesmas Curahdami / Dinas Kesehatan...' : 'Contoh: Kementerian Kesehatan RI / CSR Bank Jatim...'"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                        🏷️ Nilai Buku Aset yang Dihibahkan:
                                    </label>
                                    <div class="p-2.5 rounded-xl bg-purple-950/30 border border-purple-500/40 flex items-center justify-between">
                                        <span class="text-[10px] text-purple-300 font-bold">Total Nilai:</span>
                                        <span class="text-sm font-mono font-extrabold text-purple-300" x-text="selectedAstapReklas?.jumlah_realisasi || 'Rp 0'"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Box Penjelasan Dampak Neraca BMD -->
                            <div class="p-3 rounded-xl bg-purple-950/30 border border-purple-500/30 flex items-start space-x-2.5">
                                <span class="text-purple-400 text-sm shrink-0">💡</span>
                                <div class="text-[11px] text-purple-200/90 leading-relaxed">
                                    <template x-if="reklasTipeHibah === 'keluar'">
                                        <span>Transaksi ini akan mencatat <strong>Mutasi Kurang (−)</strong> di KIB dan mengisi penyeimbang <strong>Mutasi Tambah (+) di Baris 40: Hibah (KOR_HIBAH)</strong> serta mengisi <strong>Kolom 12 (DIHIBAHKAN)</strong> pada Laporan Rekonsiliasi BMD.</span>
                                    </template>
                                    <template x-if="reklasTipeHibah === 'masuk'">
                                        <span>Transaksi ini akan mencatat <strong>Mutasi Tambah (+)</strong> di KIB dan menetralkan belanja kas via <strong>Mutasi Kurang (−) di Baris 40: Hibah (KOR_HIBAH)</strong> pada Laporan Rekonsiliasi BMD.</span>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Jika Mutasi Eksternal (Antar-OPD) -->
                        <div x-show="reklasJenis === 'mutasi_eksternal'" class="space-y-4">
                            <!-- Field Informasi Mutasi Antar-OPD -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                        🏛️ SKPD / Dinas Penerima Mutasi: <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="reklasSkpdTujuan" placeholder="Contoh: Dinas Kesehatan Kab. Bondowoso / BPBD..."
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-teal-200 focus:outline-none focus:border-teal-500 font-medium">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                        🏷️ Nilai Aset yang Dimutasikan:
                                    </label>
                                    <div class="p-2.5 rounded-xl bg-teal-950/30 border border-teal-500/40 flex items-center justify-between">
                                        <span class="text-[10px] text-teal-300 font-bold">Total Nilai:</span>
                                        <span class="text-sm font-mono font-extrabold text-teal-300" x-text="selectedAstapReklas?.jumlah_realisasi || 'Rp 0'"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                        📄 Nomor BAST Mutasi Antar-OPD: <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="text" x-model="reklasNomorBastMutasi" placeholder="Contoh: 028/BAST-MUTASI/430.10.7/2026..."
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs font-mono text-white focus:outline-none focus:border-teal-500">
                                </div>
                                <div class="space-y-1.5">
                                    <label class="block text-slate-300 font-bold text-[10.5px] uppercase tracking-wider">
                                        📅 Tanggal BAST Mutasi: <span class="text-rose-400">*</span>
                                    </label>
                                    <input type="date" x-model="reklasTanggalBastMutasi"
                                           class="w-full bg-slate-950 border border-slate-700 rounded-xl px-3.5 py-2 text-xs font-mono text-white focus:outline-none focus:border-teal-500">
                                </div>
                            </div>

                            <!-- Box Penjelasan Dampak Neraca BMD -->
                            <div class="p-3 rounded-xl bg-teal-950/30 border border-teal-500/30 flex items-start space-x-2.5">
                                <span class="text-teal-400 text-sm shrink-0">💡</span>
                                <div class="text-[11px] text-teal-200/90 leading-relaxed">
                                    Aset ini akan dialihkan keluar dari RSUD dr. H. Koesnandi ke SKPD penerima. Di laporan rekon BMD, transaksi ini mencatat <strong>Mutasi Kurang (−)</strong> di KIB asal dan mengisi penyeimbang <strong>Mutasi Tambah (+) di Baris 42: Koreksi Lain-Lain (KOR_LAIN)</strong> serta mengisi <strong>Kolom 13 (MUTASI −)</strong>.
                                </div>
                            </div>
                        </div>

                        <!-- Alasan Reklasifikasi (Wajib Diisi untuk Semua Jenis Reklas) -->
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between">
                                <label class="block text-indigo-300 font-bold text-[10.5px] uppercase tracking-wider flex items-center space-x-1.5">
                                    <span>📝</span>
                                    <span>Alasan Kenapa Melakukan Reklasifikasi:</span>
                                    <span class="text-rose-400 font-black text-xs">*</span>
                                </label>
                                <span class="px-2 py-0.5 rounded text-[9.5px] font-extrabold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    Wajib Diisi
                                </span>
                            </div>
                            <textarea x-model="reklasAlasan" rows="2" 
                                      placeholder="Tuliskan alasan kenapa melakukan reklasifikasi (contoh: Pekerjaan fisik KDP telah selesai 100% dan terbit BAST / Koreksi salah rekening Simda BMD / Hasil temuan audit BPK)..."
                                      :class="(!reklasAlasan || !reklasAlasan.trim()) ? 'border-rose-500/60 focus:border-rose-400 focus:ring-1 focus:ring-rose-400/40' : 'border-indigo-500/40 focus:border-indigo-400 focus:ring-1 focus:ring-indigo-400/40'"
                                      class="w-full bg-slate-900/90 border rounded-xl px-3 py-2 text-xs text-white focus:outline-none transition-all resize-none placeholder-slate-500"></textarea>
                            <p class="text-[10px] text-slate-400 flex items-center space-x-1">
                                <span class="text-amber-400">⚠️</span>
                                <span>Alasan reklasifikasi wajib diisi untuk semua jenis reklasifikasi sebagai dasar pencatatan berita acara &amp; audit.</span>
                            </p>
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

                </div>

                <!-- 3. Modal Footer Actions (Fixed Bottom) -->
                <div class="shrink-0 px-6 py-3.5 border-t border-slate-800/90 bg-slate-950/70 flex items-center justify-end space-x-3">
                    <button type="button" @click="showReklasModal = false"
                        class="px-4 py-2 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white font-bold text-xs border border-slate-700/80 shadow-sm transition-all active:scale-95 cursor-pointer">
                        Batal
                    </button>
                    <button type="button" @click="submitReklas()"
                        :disabled="isSubmittingReklas"
                        class="px-5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-500 active:bg-indigo-700 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 border border-indigo-500/50 hover:border-indigo-400 transition-all active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex items-center space-x-2 cursor-pointer">
                        <template x-if="isSubmittingReklas">
                            <svg class="animate-spin w-4 h-4 text-white shrink-0" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </template>
                        <template x-if="!isSubmittingReklas">
                            <svg class="w-4 h-4 text-indigo-100 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </template>
                        <span x-text="isSubmittingReklas ? 'Menyimpan...' : 'Simpan Reklasifikasi'"></span>
                    </button>
                </div>

            </div>
        </div>
    </template>
