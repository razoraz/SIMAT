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
                        <div x-show="reklasJenis === 'pindah_kib'" class="space-y-4">
                            
                            <!-- Tingkat 1: Klasifikasi / KIB Tujuan -->
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between gap-2">
                                    <label class="text-slate-300 font-bold text-xs uppercase tracking-wider flex items-center gap-1.5 min-w-0">
                                        <span class="w-4 h-4 rounded-full bg-blue-500/20 text-blue-300 border border-blue-500/40 text-[9px] font-black flex items-center justify-center shrink-0">1</span>
                                        <span class="truncate">📦 Klasifikasi / KIB Tujuan:</span>
                                    </label>
                                    <template x-if="reklasTujuanKib">
                                        <span class="shrink-0 text-[10px] font-bold px-2 py-0.5 rounded-md bg-blue-500/15 text-blue-300 border border-blue-500/30 whitespace-nowrap">
                                            KIB Terpilih
                                        </span>
                                    </template>
                                </div>
                                <select x-model="reklasTujuanKib" @change="onReklasTujuanKibChange()"
                                        class="w-full bg-slate-900 border border-slate-700 rounded-xl px-3.5 py-2.5 text-xs font-bold text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/40 cursor-pointer shadow-inner transition-all">
                                    <option value="">-- Pilih KIB / Kelompok Tujuan --</option>
                                    <option value="KIB A">KIB A - Tanah (1.3.1)</option>
                                    <option value="KIB B">KIB B - Peralatan &amp; Mesin (1.3.2)</option>
                                    <option value="KIB C">KIB C - Gedung &amp; Bangunan (1.3.3)</option>
                                    <option value="KIB D">KIB D - Jalan, Jaringan &amp; Irigasi (1.3.4)</option>
                                    <option value="KIB E">KIB E - Aset Tetap Lainnya (1.3.5)</option>
                                    <option value="ATB">ATB - Aset Tidak Berwujud (1.5.3)</option>
                                    <option value="ASET LAIN">Aset Lain-Lain (1.5.4)</option>
                                    <option value="KEMITRAAN">Kemitraan Pihak Ketiga (1.5.2)</option>
                                </select>
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

                            <!-- Tingkat 4: Penyesuaian Spesifikasi Fisik Baru Sesuai KIB Tujuan -->
                            <div x-show="reklasTujuanKib" x-transition.duration.300ms class="p-4 rounded-2xl bg-slate-900 border border-cyan-500/30 space-y-3.5 shadow-lg">
                                <div class="flex items-center justify-between border-b border-slate-800 pb-2.5">
                                    <div class="flex items-center space-x-2">
                                        <span class="w-5 h-5 rounded-full bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 text-[10px] font-black flex items-center justify-center shrink-0">4</span>
                                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                                            📝 Spesifikasi Fisik Baru (<span class="text-cyan-300 font-extrabold" x-text="reklasTujuanKib"></span>):
                                        </span>
                                    </div>
                                    <span class="text-[10px] text-cyan-400 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/30 font-semibold">
                                        Format Spek Dinamis
                                    </span>
                                </div>

                                <div class="p-2.5 rounded-xl bg-cyan-950/20 border border-cyan-500/20 text-[11px] text-cyan-200/90 leading-relaxed flex items-center gap-2">
                                    <span>💡</span>
                                    <span>Aset dialihkan ke kelompok <strong class="text-white" x-text="reklasTujuanKib"></strong>. Data spesifikasi lama akan diarsip ke riwayat audit, dan form di bawah otomatis disesuaikan agar register &amp; KIR terbit sesuai format fisik <strong class="text-white" x-text="reklasTujuanKib"></strong>.</span>
                                </div>

                                <!-- Form Spesifik: KIB A - Tanah -->
                                <template x-if="reklasTujuanKib === 'KIB A'">
                                    <div class="space-y-3">
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

                                <!-- Form Spesifik: KIB B - Peralatan & Mesin -->
                                <template x-if="reklasTujuanKib === 'KIB B'">
                                    <div class="space-y-3">
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

                                <!-- Form Spesifik: KIB C - Gedung & Bangunan -->
                                <template x-if="reklasTujuanKib === 'KIB C'">
                                    <div class="space-y-3">
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

                                <!-- Form Spesifik: KIB D - Jalan, Jaringan & Irigasi -->
                                <template x-if="reklasTujuanKib === 'KIB D'">
                                    <div class="space-y-3">
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

                                <!-- Form Spesifik: KIB E - Aset Tetap Lainnya -->
                                <template x-if="reklasTujuanKib === 'KIB E'">
                                    <div class="space-y-3">
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
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Kondisi Fisik Barang:</label>
                                                <select x-model="reklasSpekBaru.aset_lain_kondisi"
                                                        class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                                    <option value="Rusak Berat">Rusak Berat (Tidak Dapat Dimanfaatkan)</option>
                                                    <option value="Tidak Digunakan Operasional">Tidak Digunakan dalam Operasional Pemerintah</option>
                                                    <option value="Akan Dihapuskan">Dalam Proses Usulan Penghapusan (SK)</option>
                                                    <option value="Aset Hilang">Aset Hilang (TGR / Penelusuran)</option>
                                                    <option value="Lainnya">Kondisi Sebab Lainnya</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Lokasi Penyimpanan / Gudang:</label>
                                                <input type="text" x-model="reklasSpekBaru.aset_lain_lokasi" placeholder="Contoh: Gudang Penampungan Aset Rusak RSUD"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Alasan Pengalihan ke Aset Lain-Lain:</label>
                                                <input type="text" x-model="reklasSpekBaru.aset_lain_alasan" placeholder="Contoh: Rusak berat akibat usia pemakaian dan tidak ekonomis untuk diperbaiki"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <!-- Form Spesifik: KEMITRAAN - Kemitraan Pihak Ketiga (1.5.2) -->
                                <template x-if="reklasTujuanKib === 'KEMITRAAN'">
                                    <div class="space-y-3">
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nama Mitra / Pihak Ketiga:</label>
                                                <input type="text" x-model="reklasSpekBaru.kemitraan_mitra" placeholder="Contoh: PT Kerjasama Medika Indonesia"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div>
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Nomor Dokumen Perjanjian (KSO/BSG):</label>
                                                <input type="text" x-model="reklasSpekBaru.kemitraan_perjanjian_no" placeholder="Contoh: 020/KSO-RSUD/2026"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
                                            <div class="sm:col-span-2">
                                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-1">Jangka Waktu Kerjasama:</label>
                                                <input type="text" x-model="reklasSpekBaru.kemitraan_jangka_waktu" placeholder="Contoh: 5 Tahun (2026 s/d 2031)"
                                                       class="w-full bg-slate-950 border border-slate-700 focus:border-cyan-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                                            </div>
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
                                               class="w-full pl-9 pr-3.5 py-1.5 bg-slate-900 border border-blue-500/50 rounded-xl text-xs font-mono font-bold text-blue-200 focus:outline-none focus:border-blue-400 text-right no-spinner [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none">
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
                            <div class="space-y-3 pt-2">
                                <div class="flex items-center justify-between pb-1.5 border-b border-slate-800/80">
                                    <div class="flex items-center space-x-2.5">
                                        <span class="text-xs font-black text-cyan-300 uppercase tracking-wider flex items-center gap-1.5">
                                            <span>📦</span>
                                            <span>Rincian Nilai Kapitalisasi Barang:</span>
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-lg text-[10.5px] font-mono font-extrabold bg-gradient-to-r from-cyan-500/15 to-blue-500/15 text-cyan-300 border border-cyan-500/30 shadow-xs"
                                              x-text="reklasExtracomItems.length + ' Item Terdaftar'"></span>
                                    </div>
                                    <span class="text-[11px] text-slate-400 font-medium">Ubah nilai per unit barang temuan</span>
                                </div>

                                <!-- Cards Container Barang 1 & 2 -->
                                <div class="space-y-3 max-h-[300px] overflow-y-auto custom-scrollbar pr-1">
                                    <template x-for="(item, idx) in reklasExtracomItems" :key="idx">
                                        <div class="p-4 rounded-2xl bg-slate-950/80 border border-slate-800/90 hover:border-cyan-500/40 transition-all space-y-3 shadow-lg group backdrop-blur-sm">
                                            <!-- Baris Atas Item: Badge Nomor & Nama Barang & Volume -->
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex items-center space-x-2.5 min-w-0">
                                                    <span class="px-2.5 py-1 rounded-lg text-[11px] font-mono font-extrabold bg-cyan-500/15 text-cyan-300 border border-cyan-500/35 shrink-0 shadow-xs"
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

                                            <!-- Grid Input: Nilai Kapitalisasi Satuan & Subtotal -->
                                            <div class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-center pt-2.5 border-t border-slate-800/80">
                                                <div class="sm:col-span-7 space-y-1">
                                                    <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider flex items-center gap-1.5">
                                                        <span>🏷️</span>
                                                        <span>Nilai Kapitalisasi Satuan (Rp):</span>
                                                    </label>
                                                    <div class="relative flex items-center rounded-xl bg-slate-900/90 border border-cyan-500/40 focus-within:border-cyan-400 focus-within:ring-2 focus-within:ring-cyan-500/20 shadow-inner transition-all overflow-hidden">
                                                        <span class="px-3 py-2 bg-slate-950/80 border-r border-slate-800 text-cyan-400 font-mono font-bold text-xs select-none">
                                                            Rp
                                                        </span>
                                                        <input type="text"
                                                               inputmode="numeric"
                                                               :value="formatRupiahInput(item.harga_satuan)"
                                                               @input="updateItemHargaSatuan(item, $event.target.value)"
                                                               @focus="$event.target.select()"
                                                               placeholder="0"
                                                               class="w-full bg-transparent px-3.5 py-2 text-xs font-mono font-extrabold text-cyan-200 focus:outline-none text-right placeholder-slate-600">
                                                    </div>
                                                </div>
                                                <div class="sm:col-span-5 space-y-1 sm:text-right">
                                                    <label class="block text-slate-400 font-bold text-[10px] uppercase tracking-wider">
                                                        Subtotal Barang:
                                                    </label>
                                                    <div class="p-2.5 rounded-xl bg-emerald-950/30 border border-emerald-500/30 flex flex-col items-end justify-center shadow-inner">
                                                        <span class="text-[9.5px] font-extrabold text-emerald-400/80 uppercase tracking-wider hidden sm:block">Subtotal Item</span>
                                                        <div class="text-xs sm:text-sm font-mono font-black text-emerald-400"
                                                             x-text="'Rp ' + Number((item.jumlah_volume || 1) * (parseFloat(item.harga_satuan) || 0)).toLocaleString('id-ID')">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
