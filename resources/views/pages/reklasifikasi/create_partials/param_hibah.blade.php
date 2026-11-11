            <!-- 2C. Panel Khusus Hibah Keluar -->
            <div x-show="formData.jenis_reklas === 'HIBAH_KELUAR'"
                 x-transition.duration.200ms
                 class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-purple-500/30 space-y-4 shadow-xl">
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-purple-500/15 border border-purple-500/30 flex items-center justify-center text-purple-300 shadow-sm shadow-purple-500/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                            </svg>
                        </div>
                        <div>
                            <span class="text-xs font-black text-purple-300 uppercase tracking-wider block">
                                Informasi Hibah Keluar (Aset Diserahkan)
                            </span>
                            <span class="text-[10px] text-slate-400">Pengalihan aset RSUD kepada Puskesmas / Desa / Instansi Luar</span>
                        </div>
                    </div>
                    <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 rounded-full bg-purple-500/10 text-purple-300 border border-purple-500/30 font-bold tracking-wide">
                        <span class="w-1.5 h-1.5 rounded-full bg-purple-400 animate-pulse"></span>
                        Baris 40 / Kolom 12 (DIHIBAHKAN)
                    </span>
                </div>

                <div class="p-3.5 rounded-2xl bg-purple-950/25 border border-purple-500/30 flex items-start gap-3 shadow-inner">
                    <div class="p-1.5 rounded-lg bg-purple-500/20 text-purple-300 shrink-0 mt-0.5 border border-purple-500/30 shadow-sm shadow-purple-500/20">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="text-[11px] text-purple-200/90 leading-relaxed">
                        <strong class="text-white block mb-0.5">Dampak Pencatatan Neraca BMD (Hibah Keluar):</strong>
                        <span>Aset tetap RSUD berkurang (Mutasi Kurang −). Baris 40 (Koreksi Hibah) bertambah (+) pada Kolom 12 (Dihibahkan) sebagai penyeimbang seimbang (TRUE).</span>
                    </div>
                </div>

                <!-- Bagian 1: Identitas Pihak Pertama (Yang Menyerahkan Hibah - RSUD Dr. H. Koesnadi) -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-purple-500/30 space-y-3 shadow-inner">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-purple-300 uppercase tracking-wider flex items-center gap-1.5 truncate">
                            <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                            <span class="truncate">Pihak Pertama (Pejabat Penyerah Hibah RSUD)</span>
                        </span>
                        <span class="text-[9.5px] font-mono text-purple-300/80 whitespace-nowrap shrink-0">Internal RSUD</span>
                    </div>

                    <div class="space-y-3 text-xs">
                        <!-- Baris 1: Nama Pejabat Penyerah RSUD (Lebar Penuh) -->
                        <div class="space-y-1 min-w-0">
                            <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                Nama Pejabat Penyerah RSUD <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" x-model="formData.pj_asal_nama"
                                   placeholder="BUDI HARTONO, S.Sos / dr. Yus..."
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">
                        </div>

                        <!-- Baris 2: NIP dan Jabatan -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="space-y-1 min-w-0">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    NIP Pejabat Penyerah
                                </label>
                                <input type="text" x-model="formData.pj_asal_nip"
                                       placeholder="19xxxxxxxxxxxxxx"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 text-xs text-white font-mono placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>

                            <div class="space-y-1 min-w-0">
                                <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                    Jabatan Penyerah RSUD
                                </label>
                                <input type="text" x-model="formData.pj_asal_jabatan"
                                       placeholder="Pengurus Barang Pengguna"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Informasi BAST & Penerima Hibah -->
                <div class="p-4 rounded-xl bg-slate-900/90 border border-purple-500/30 space-y-3 shadow-inner">
                    <div class="flex items-center justify-between border-b border-slate-800 pb-2">
                        <span class="text-xs font-bold text-purple-300 uppercase tracking-wider flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                            <span>Pihak Penerima &amp; Tanggal BAST</span>
                        </span>
                        <span class="text-[9.5px] font-mono text-purple-300/80">Pihak Kedua</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Instansi / Pihak Penerima Hibah</span>
                                <span class="text-rose-400 font-bold">*</span>
                            </label>
                            <input type="text" x-model="formData.pihak_hibah"
                                   placeholder="Contoh: Puskesmas Tamanan / Yayasan Sosial..."
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                        </div>

                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <span>Tanggal BAST Hibah</span>
                                <span class="text-rose-400 font-bold">*</span>
                            </label>
                            <input type="date" x-model="formData.tanggal_bast"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 text-xs font-mono text-white focus:outline-none transition-all shadow-inner">
                        </div>
                    </div>

                    <!-- Baris 2: Nama Penerima & NIP (2 Kolom Sejajar Presisi) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider h-5 whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span class="truncate">Nama Penerima Hibah</span>
                                <span class="text-rose-400 font-bold shrink-0">*</span>
                            </label>
                            <input type="text" x-model="formData.penerima_nama"
                                   placeholder="Contoh: dr. H. Ahmad Fauzi / Nama Pimpinan..."
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner font-bold">
                        </div>

                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider h-5 whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                                </svg>
                                <span class="truncate">NIP Pejabat Penerima (Opsional)</span>
                            </label>
                            <input type="text" x-model="formData.penerima_nip"
                                   placeholder="19xxxxxxxxxxxxxx atau -"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 text-xs font-mono text-purple-200 placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                        </div>
                    </div>

                    <!-- Baris 3: Jabatan & Alamat Kantor Penerima (2 Kolom Sejajar Presisi) -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider h-5 whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="truncate">Jabatan Pejabat Penerima</span>
                                <span class="text-rose-400 font-bold shrink-0">*</span>
                            </label>
                            <input type="text" x-model="formData.penerima_jabatan"
                                   placeholder="Contoh: Kepala Puskesmas / Ketua Yayasan..."
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner font-medium">
                        </div>

                        <div class="space-y-1.5 min-w-0">
                            <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider h-5 whitespace-nowrap">
                                <svg class="w-3.5 h-3.5 text-purple-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <span class="truncate">Alamat Kantor / Domisili Penerima</span>
                                <span class="text-rose-400 font-bold shrink-0">*</span>
                            </label>
                            <input type="text" x-model="formData.alamat_instansi"
                                   placeholder="Contoh: Jl. Raya Situbondo No. 45, Kecamatan Tenggarang, Bondowoso..."
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-purple-400 focus:ring-1 focus:ring-purple-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: Checklist Unit Register NIBAR & Pembaruan Kondisi Serah Terima Hibah -->
                <template x-if="selectedAstap && selectedAstap.registers && selectedAstap.registers.length > 0">
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border border-purple-500/30 space-y-3 shadow-inner">
                        <div class="flex items-center justify-between gap-3 border-b border-slate-800/80 pb-2.5">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-purple-500/15 border border-purple-500/30 flex items-center justify-center text-purple-300 shadow-sm shadow-purple-500/10 shrink-0">
                                    <svg class="w-4 h-4 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                </div>
                                <span class="text-xs font-black text-purple-300 uppercase tracking-wider flex items-center gap-1.5 min-w-0">
                                    <span class="truncate">Pilih Unit Register &amp; Kondisi Serah:</span>
                                    <span class="text-rose-400 shrink-0">*</span>
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <span class="text-[10px] font-mono px-2 py-1 rounded-lg bg-purple-500/10 text-purple-300 border border-purple-500/25 whitespace-nowrap"
                                      x-text="(formData.selected_register_ids ? formData.selected_register_ids.length : 0) + ' Terpilih'"></span>
                                <button type="button" @click="toggleSelectAllReklasNibars(true)"
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-purple-500/20 hover:bg-purple-500/30 text-purple-300 border border-purple-500/40 transition-all cursor-pointer whitespace-nowrap active:scale-95">
                                    Pilih Semua
                                </button>
                                <button type="button" @click="toggleSelectAllReklasNibars(false)"
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-slate-200 border border-slate-700 transition-all cursor-pointer whitespace-nowrap active:scale-95">
                                    Batal Pilih
                                </button>
                            </div>
                        </div>

                        <!-- Daftar Unit Register NIBAR (Scrollable) -->
                        <div class="space-y-2 max-h-60 overflow-y-auto custom-scrollbar pr-1">
                            <template x-for="reg in getReklasAvailableRegisters()" :key="reg.id">
                                <div class="p-3 rounded-xl border transition-all"
                                     :class="(reg.status === 'Mutasi Keluar OPD' || reg.status === 'Dihibahkan')
                                         ? 'bg-slate-950/40 border-slate-900 opacity-60'
                                         : (isNibarSelected(reg.id)
                                             ? 'bg-purple-500/10 border-purple-500/50 shadow-sm shadow-purple-500/10'
                                             : 'bg-slate-900/60 border-slate-800/90 hover:border-slate-700')">
                                    
                                    <!-- Baris Atas: Checkbox + NIBAR (Kiri) & Nilai Satuan (Kanan) -->
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                                            <template x-if="reg.status !== 'Mutasi Keluar OPD' && reg.status !== 'Dihibahkan'">
                                                <input type="checkbox" :value="reg.id" x-model="formData.selected_register_ids"
                                                       @change="onSelectedNibarsChange()"
                                                       class="w-4 h-4 rounded border-slate-700 text-purple-500 focus:ring-purple-400/30 bg-slate-950 cursor-pointer shrink-0">
                                            </template>
                                            <template x-if="reg.status === 'Mutasi Keluar OPD' || reg.status === 'Dihibahkan'">
                                                <span class="text-slate-600 cursor-not-allowed text-xs shrink-0" :title="'Unit ini sudah dialihkan keluar (' + reg.status + ')'">🔒</span>
                                            </template>
                                            <span class="font-mono font-bold text-xs tracking-tight truncate"
                                                  :class="isNibarSelected(reg.id) ? 'text-purple-300' : 'text-slate-300'"
                                                  x-text="reg.nibar || reg.no_register || ('Unit #' + reg.id)"></span>
                                        </div>

                                        <div class="text-right shrink-0">
                                            <template x-if="reg.status === 'Mutasi Keluar OPD'">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wide bg-cyan-500/20 text-cyan-300 border border-cyan-500/30">
                                                    Sudah Keluar OPD
                                                </span>
                                            </template>
                                            <template x-if="reg.status === 'Dihibahkan'">
                                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wide bg-purple-500/20 text-purple-300 border border-purple-500/30">
                                                    Sudah Dihibahkan
                                                </span>
                                            </template>
                                            <template x-if="reg.status !== 'Mutasi Keluar OPD' && reg.status !== 'Dihibahkan'">
                                                <div class="flex items-center gap-1.5">
                                                    <span class="text-[9.5px] uppercase font-bold tracking-wider text-slate-400">Nilai Satuan:</span>
                                                    <span class="text-xs font-mono font-extrabold text-purple-300"
                                                          x-text="'Rp ' + Number(reg.harga_satuan || (selectedAstap.total_realisasi / Math.max(1, selectedAstap.registers.length))).toLocaleString('id-ID')"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Baris Bawah: Ruangan / Gudang Aset (Kiri) & Dropdown Kondisi Serah (Kanan) -->
                                    <div class="mt-2.5 pt-2 border-t border-slate-800/60 flex items-center justify-between gap-3 pl-6.5">
                                        <div class="text-[10.5px] flex items-center space-x-1.5 min-w-0">
                                            <template x-if="reg.status === 'Mutasi Keluar OPD'">
                                                <span class="text-cyan-400 font-bold">🏛️ Sudah Dimutasi Keluar OPD</span>
                                            </template>
                                            <template x-if="reg.status === 'Dihibahkan'">
                                                <span class="text-purple-400 font-bold">🎁 Sudah Dihibahkan</span>
                                            </template>
                                            <template x-if="reg.status !== 'Mutasi Keluar OPD' && reg.status !== 'Dihibahkan' && reg.ruang_pemegang">
                                                <span class="text-slate-400 truncate">
                                                    <span class="text-slate-500">📍 Ruangan:</span> <span class="text-slate-300" x-text="reg.ruang_pemegang"></span>
                                                </span>
                                            </template>
                                            <template x-if="reg.status !== 'Mutasi Keluar OPD' && reg.status !== 'Dihibahkan' && !reg.ruang_pemegang">
                                                <span class="text-amber-300/90 font-medium text-[10px] px-1.5 py-0.5 rounded bg-amber-500/10 border border-amber-500/25">
                                                    📦 Gudang Aset (Belum Ditempatkan)
                                                </span>
                                            </template>
                                        </div>

                                        <!-- Dropdown Pembaruan Kondisi Aktual Serah Terima (Kanan) -->
                                        <div class="flex items-center gap-1.5 bg-slate-950/80 border border-purple-500/40 rounded-xl px-2.5 py-1.5 shadow-inner shrink-0"
                                             x-show="isNibarSelected(reg.id)">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Kondisi Serah:</span>
                                            <select x-model="formData.register_kondisi_map[reg.id]"
                                                    @click.stop
                                                    class="bg-transparent border-none text-[11px] font-extrabold text-purple-300 focus:ring-0 focus:outline-none cursor-pointer py-0 pl-1 pr-6">
                                                <option value="Baik" class="bg-slate-900 text-white">Baik (B)</option>
                                                <option value="Kurang Baik" class="bg-slate-900 text-white">Kurang Baik (KB)</option>
                                                <option value="Rusak Berat" class="bg-slate-900 text-white">Rusak Berat (RB)</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

