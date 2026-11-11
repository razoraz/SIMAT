            <!-- 2D. Panel Khusus Mutasi Eksternal (Antar-OPD / SKPD) -->
            <div x-show="formData.jenis_reklas === 'MUTASI_EKSTERNAL'"
                 x-transition.duration.200ms
                 class="p-4 sm:p-5 rounded-2xl bg-slate-950/80 border border-teal-500/30 space-y-4 shadow-xl">
                
                <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-teal-500/20 text-teal-300 border border-teal-500/40 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                        </div>
                        <span class="text-xs font-bold text-white uppercase tracking-wider">
                            Informasi Mutasi Antar-OPD / SKPD Pemerintah Daerah
                        </span>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-0.5 rounded-md bg-teal-500/20 text-teal-300 border border-teal-500/40">
                        Penyeimbang: Baris 42 (Koreksi Lain / Kolom 13)
                    </span>
                </div>

                <div class="p-3 rounded-xl bg-teal-950/25 border border-teal-500/20 text-xs text-teal-200/90 leading-relaxed flex items-start gap-2.5">
                    <div class="p-1 rounded-md bg-teal-500/20 text-teal-300 shrink-0 mt-0.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <div>
                        <strong class="text-white block mb-0.5">Pengalihan Aset Keluar RSUD Antar-Perangkat Daerah:</strong>
                        <span>Aset tetap RSUD dialihkan ke Dinas/Badan/Kecamatan lain di lingkungan Pemkab Bondowoso. Aset RSUD berkurang (Mutasi Kurang -), disinkronkan ke Baris 42 Koreksi Lain-Lain (Kolom 13 Mutasi -), serta otomatis tercatat di modul <strong>Mutasi Eksternal Keluar</strong>.</span>
                    </div>
                </div>

                <!-- Bagian 1: Identitas Pihak Pertama (RSUD Dr. H. Koesnadi - Yang Menyerahkan) -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-teal-500/30 shadow-lg shadow-teal-950/20 space-y-4">
                    <div class="flex items-center justify-between gap-3 border-b border-slate-800/80 pb-3">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-8 h-8 rounded-xl bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-300 shadow-sm shadow-teal-500/10 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-black text-teal-300 uppercase tracking-wider truncate block">PIHAK PERTAMA (PENYERAH ASET)</span>
                                <span class="text-[10px] text-slate-400 truncate block">RSUD Dr. H. Koesnadi · Instansi Penyerah BMD</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 rounded-full bg-teal-500/10 text-teal-300 border border-teal-500/30 font-bold tracking-wide whitespace-nowrap shrink-0">
                            <span class="w-1.5 h-1.5 rounded-full bg-teal-400 animate-pulse"></span>
                            Internal RSUD
                        </span>
                    </div>

                    <div class="space-y-3.5 text-xs">
                        <!-- Baris 1: Nama Pejabat Penyerah RSUD (Lebar Penuh) -->
                        <div class="relative space-y-1.5 min-w-0" @click.away="isPejabatAsalDropdownOpen = false">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span>Nama Pejabat Penyerah RSUD</span>
                                    <span class="text-rose-400 font-bold">*</span>
                                </label>
                                <span class="text-[9.5px] font-mono text-teal-300/80" x-show="filteredPejabatAsalList.length > 0" x-text="filteredPejabatAsalList.length + ' Pilihan Pejabat'"></span>
                            </div>
                            <div class="relative">
                                <input type="text" x-model="formData.pj_asal_nama"
                                       @focus="isPejabatAsalDropdownOpen = true"
                                       @input="isPejabatAsalDropdownOpen = true"
                                       placeholder="Ketik atau pilih pejabat penyerah RSUD (BUDI HARTONO, S.Sos / dr. Yus...)"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-teal-400 focus:ring-1 focus:ring-teal-400/30 rounded-xl px-3.5 pr-9 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">
                                <button type="button" @click="isPejabatAsalDropdownOpen = !isPejabatAsalDropdownOpen"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-teal-300 rounded-lg hover:bg-slate-800 transition-colors"
                                        title="Buka pilihan pejabat">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="isPejabatAsalDropdownOpen ? 'rotate-180 text-teal-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                            </div>

                            <!-- Dropdown Riwayat Pejabat RSUD (Otomatis menutup jika tidak ada hasil cocok) -->
                            <div x-show="isPejabatAsalDropdownOpen && filteredPejabatAsalList.length > 0" x-cloak
                                 class="absolute z-40 mt-1 w-full bg-slate-900 border border-teal-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-48 overflow-y-auto">
                                <div class="px-3 py-1.5 bg-slate-950 text-[10px] font-bold text-teal-400 uppercase tracking-wider flex items-center justify-between">
                                    <span>Pilihan Pejabat Penyerah RSUD</span>
                                    <span class="font-mono text-slate-400" x-text="filteredPejabatAsalList.length + ' hasil'"></span>
                                </div>
                                <template x-for="(pj, pjIdx) in filteredPejabatAsalList" :key="pjIdx">
                                    <div @click="selectPejabatAsal(pj)"
                                         class="p-2.5 hover:bg-teal-500/15 cursor-pointer text-left transition-colors flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="font-bold text-white text-xs" x-text="pj.nama"></div>
                                            <div class="text-[10px] text-teal-400 font-mono mt-0.5" x-text="'NIP: ' + (pj.nip || '-') + ' · ' + (pj.jabatan || 'Pejabat RSUD')"></div>
                                        </div>
                                        <span class="text-[9px] px-2 py-0.5 rounded bg-teal-500/10 text-teal-300 border border-teal-500/25 font-bold shrink-0">
                                            Pilih ↵
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Baris 2: NIP dan Jabatan Penyerah RSUD (2 Kolom Lapang) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- NIP Pejabat Penyerah RSUD -->
                            <div class="space-y-1.5 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                                    <span>NIP Pejabat Penyerah RSUD</span>
                                </label>
                                <input type="text" x-model="formData.pj_asal_nip"
                                       placeholder="19xxxxxxxxxxxxxx"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-teal-400 focus:ring-1 focus:ring-teal-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-mono tracking-wider transition-all shadow-inner">
                            </div>

                            <!-- Jabatan Penyerah RSUD -->
                            <div class="space-y-1.5 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    <span>Jabatan Penyerah RSUD</span>
                                </label>
                                <input type="text" x-model="formData.pj_asal_jabatan"
                                       placeholder="Pengurus Barang Pengguna"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-teal-400 focus:ring-1 focus:ring-teal-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-semibold transition-all shadow-inner">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian 2: Identitas Pihak Kedua (SKPD / OPD Penerima) -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-cyan-500/30 shadow-lg shadow-cyan-950/20 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-cyan-500/15 border border-cyan-500/30 flex items-center justify-center text-cyan-300 shadow-sm shadow-cyan-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-black text-cyan-300 uppercase tracking-wider block">PIHAK KEDUA (Yang Menerima - SKPD / OPD Tujuan)</span>
                                <span class="text-[10px] text-slate-400">Instansi Pemerintah Daerah Penerima Aset</span>
                            </div>
                        </div>
                        <span class="inline-flex items-center gap-1.5 text-[10px] px-2.5 py-1 rounded-full bg-cyan-500/10 text-cyan-300 border border-cyan-500/30 font-bold tracking-wide">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                            Eksternal Pemkab
                        </span>
                    </div>

                    <!-- Combobox Autocomplete SKPD Penerima -->
                    <div class="relative space-y-1" @click.away="isSkpdDropdownOpen = false">
                        <div class="flex items-center justify-between">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                                <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                <span>SKPD / OPD Penerima BMD</span>
                                <span class="text-rose-400 font-bold">*</span>
                            </label>
                            <template x-if="skpdList && skpdList.length > 0">
                                <span class="text-[9.5px] text-cyan-300/80 font-mono" x-text="skpdList.length + ' Pilihan Instansi'"></span>
                            </template>
                        </div>

                        <div class="relative">
                            <input type="text" x-model="formData.skpd_tujuan"
                                   @focus="isSkpdDropdownOpen = true"
                                   @input="isSkpdDropdownOpen = true"
                                   placeholder="Ketik atau pilih nama dinas/instansi penerima (contoh: Dinas Kesehatan, BPKAD Bondowoso...)"
                                   autocomplete="off"
                                   class="w-full bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 py-2.5 pl-9 pr-9 text-xs text-white placeholder-slate-500 focus:outline-none font-bold shadow-inner transition-all">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-cyan-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z" /></svg>
                            </div>
                            <button type="button" x-show="formData.skpd_tujuan"
                                    @click="formData.skpd_tujuan = ''; isSkpdDropdownOpen = true"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-rose-300 rounded-lg hover:bg-slate-800 transition-colors">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>

                        <!-- Dropdown List SKPD -->
                        <div x-show="isSkpdDropdownOpen && filteredSkpdList.length > 0" x-cloak
                             class="absolute z-40 mt-1 w-full bg-slate-900 border border-cyan-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-56 overflow-y-auto">
                            <div class="px-3 py-1.5 bg-slate-950 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between">
                                <span>Pilih Direktori SKPD Pemkab</span>
                                <span class="font-mono text-slate-400" x-text="filteredSkpdList.length + ' hasil'"></span>
                            </div>
                            <template x-for="(skpd, sIdx) in filteredSkpdList" :key="sIdx">
                                <div @click="selectSkpd(skpd)"
                                     class="p-2.5 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2.5 text-left"
                                     :class="formData.skpd_tujuan === skpd ? 'bg-cyan-500/20 text-cyan-200' : 'text-slate-200'">
                                    <div class="min-w-0">
                                        <div class="font-bold text-xs group-hover:text-cyan-300 truncate" x-text="skpd"></div>
                                        <div class="text-[10px] text-slate-400 truncate flex items-center gap-1 mt-0.5" x-show="skpdDirectory[skpd]?.alamat">
                                            <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                            <span x-text="skpdDirectory[skpd]?.alamat"></span>
                                        </div>
                                    </div>
                                    <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-bold shrink-0">
                                        Pilih ↵
                                    </span>
                                </div>
                            </template>
                        </div>
                    </div>

                    <!-- Pejabat Penerima OPD (Tata Letak Lapang: Baris 1 Nama Penuh, Baris 2 NIP & Jabatan) -->
                    <div class="space-y-3.5 text-xs">
                        <!-- Nama Pejabat Penerima OPD (Lebar Penuh) -->
                        <div class="relative space-y-1.5 min-w-0" @click.away="isPejabatTujuanDropdownOpen = false">
                            <div class="flex items-center justify-between">
                                <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                                    <span>Nama Pejabat Penerima OPD</span>
                                    <span class="text-rose-400 font-bold">*</span>
                                </label>
                                <span class="text-[9.5px] font-mono text-cyan-300/80" x-show="filteredPejabatTujuanList.length > 0" x-text="filteredPejabatTujuanList.length + ' Riwayat'"></span>
                            </div>
                            <div class="relative">
                                <input type="text" x-model="formData.pejabat_opd_tujuan"
                                       @focus="isPejabatTujuanDropdownOpen = true"
                                       @input="isPejabatTujuanDropdownOpen = true"
                                       placeholder="Nama Pejabat / Pengurus Barang"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 pr-9 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">
                                <button type="button" x-show="availablePejabatTujuanList.length > 0"
                                        @click="isPejabatTujuanDropdownOpen = !isPejabatTujuanDropdownOpen"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 text-slate-400 hover:text-cyan-300 rounded-lg hover:bg-slate-800 transition-colors"
                                        title="Buka riwayat pejabat OPD">
                                    <svg class="w-4 h-4 transition-transform duration-200" :class="isPejabatTujuanDropdownOpen ? 'rotate-180 text-cyan-400' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                </button>
                            </div>

                            <!-- Dropdown Riwayat Pejabat Tujuan -->
                            <div x-show="isPejabatTujuanDropdownOpen && filteredPejabatTujuanList.length > 0" x-cloak
                                 class="absolute z-40 mt-1 w-full bg-slate-900 border border-cyan-500/40 rounded-xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-44 overflow-y-auto">
                                <div class="px-3 py-1 bg-slate-950 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between">
                                    <span>Riwayat Pejabat SKPD Terpilih</span>
                                    <span class="font-mono text-slate-400" x-text="filteredPejabatTujuanList.length + ' hasil'"></span>
                                </div>
                                <template x-for="(p, pIdx) in filteredPejabatTujuanList" :key="pIdx">
                                    <div @click="selectPejabatTujuan(p)"
                                         class="p-2.5 hover:bg-cyan-500/15 cursor-pointer text-left transition-colors flex items-center justify-between gap-2">
                                        <div class="min-w-0">
                                            <div class="font-bold text-white text-xs" x-text="p.nama"></div>
                                            <div class="text-[10px] text-slate-400 font-mono mt-0.5" x-text="'NIP: ' + (p.nip || '-') + ' · ' + (p.jabatan || '')"></div>
                                        </div>
                                        <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/25 font-bold shrink-0">
                                            Pilih ↵
                                        </span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Baris 2: NIP dan Jabatan Penerima (2 Kolom Lapang) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- NIP Pejabat Penerima OPD -->
                            <div class="space-y-1.5 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                                    <span>NIP Pejabat Penerima OPD</span>
                                </label>
                                <input type="text" x-model="formData.nip_pejabat_opd_tujuan"
                                       placeholder="19xxxxxxxxxxxxxx atau -"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-mono tracking-wider transition-all shadow-inner">
                            </div>

                            <!-- Jabatan Pejabat Penerima -->
                            <div class="space-y-1.5 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10.5px] font-bold text-slate-300 uppercase tracking-wider">
                                    <svg class="w-3.5 h-3.5 text-cyan-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                                    <span>Jabatan Pejabat Penerima</span>
                                </label>
                                <input type="text" x-model="formData.jabatan_opd_tujuan"
                                       placeholder="Pengurus Barang / Kepala Bidang"
                                       class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-semibold transition-all shadow-inner">
                            </div>
                        </div>
                    </div>

                    <!-- Alamat SKPD Penerima (12 Kolom Penuh) -->
                    <div class="space-y-1">
                        <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider">
                            <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            <span>Alamat Kantor SKPD / Instansi Penerima</span>
                        </label>
                        <div class="relative">
                            <input type="text" x-model="formData.alamat_instansi"
                                   placeholder="Jl. Letnan Karsono No. 2, Bondowoso"
                                   class="w-full bg-slate-950 border border-slate-700/80 focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/30 rounded-xl px-3.5 py-2.5 pl-9 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bagian 3: Dokumen Legalitas BAST, Validasi Tanggal & Unggah Berkas Fisik -->
                <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-amber-500/30 shadow-lg shadow-amber-950/20 space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-800/80 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500/15 border border-amber-500/30 flex items-center justify-center text-amber-300 shadow-sm shadow-amber-500/10">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div>
                                <span class="text-xs font-black text-white uppercase tracking-wider block">Legalitas Berita Acara &amp; Dokumen Lampiran</span>
                                <span class="text-[10px] text-slate-400 font-mono">BAST &amp; SK Bupati Mutasi Aset</span>
                            </div>
                        </div>
                        <span class="text-[10px] px-2.5 py-1 rounded-full bg-slate-800 text-slate-300 border border-slate-700 font-mono">
                            Dokumen Sah
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3.5 text-xs items-start">
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                <span class="truncate" title="Nomor BAST Mutasi Eksternal">Nomor BAST Mutasi Eksternal</span>
                                <span class="text-rose-400 font-bold shrink-0">*</span>
                            </label>
                            <input type="text" x-model="formData.nomor_ba_reklas"
                                   placeholder="000.2.3.2/.../430.10.7/2026"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-mono tracking-wider transition-all shadow-inner">
                        </div>

                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between h-5 min-w-0">
                                <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider min-w-0">
                                    <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                    <span class="truncate" title="Tanggal BAST Mutasi">Tanggal BAST Mutasi</span>
                                    <span class="text-rose-400 font-bold shrink-0">*</span>
                                </label>
                            </div>
                            <input type="date" x-model="formData.tanggal_bast"
                                   :max="new Date().toISOString().split('T')[0]"
                                   :class="isTanggalBastInvalid() ? 'border-rose-500 bg-rose-950/20 text-rose-200' : 'border-slate-700/80 bg-slate-950 text-white'"
                                   class="w-full h-10 border border-slate-700/80 bg-slate-950 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3.5 text-xs focus:outline-none font-mono transition-all shadow-inner">
                            <template x-if="isTanggalBastInvalid()">
                                <p class="text-[10px] text-rose-400 font-semibold mt-1 flex items-center gap-1">
                                    <svg class="w-3 h-3 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    <span>Tanggal dokumen BAST tidak boleh melebihi tanggal hari ini!</span>
                                </p>
                            </template>
                        </div>

                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-[10px] font-bold text-slate-300 uppercase tracking-wider h-5 min-w-0">
                                <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                                <span class="truncate" title="Nomor SK Dasar / Mutasi">Nomor SK Dasar / Mutasi</span>
                            </label>
                            <input type="text" x-model="formData.nomor_sk_dasar"
                                   placeholder="SK Bupati / Nomor BAST Transfer BMD"
                                   class="w-full h-10 bg-slate-950 border border-slate-700/80 focus:border-amber-400 focus:ring-1 focus:ring-amber-400/30 rounded-xl px-3.5 text-xs text-white placeholder-slate-500 focus:outline-none font-mono tracking-wider transition-all shadow-inner">
                        </div>
                    </div>

                    <!-- Dropzone Unggah Berkas Fisik (BAST / SK) -->
                    <div class="pt-3 border-t border-slate-800/80">
                        <label class="block text-[10px] font-bold text-slate-300 uppercase tracking-wider mb-2 flex items-center justify-between">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                <span>Unggah Berkas Fisik Berita Acara (Scan BAST Basah / SK Bupati):</span>
                            </span>
                            <span class="text-[9px] font-normal text-slate-400">Opsional (Maks. 10 MB: PDF, JPG, PNG)</span>
                        </label>

                        <div class="p-4 rounded-xl bg-slate-950 border-2 border-dashed border-slate-700 hover:border-teal-400/60 transition-all text-center relative group">
                            <input type="file" id="reklas_dokumen_file_input" @change="handleDokumenFile" accept=".pdf,.jpg,.jpeg,.png"
                                   class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                            
                            <div class="space-y-1.5 pointer-events-none">
                                <div class="w-9 h-9 mx-auto rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                </div>
                                <p class="text-xs font-bold text-slate-300 group-hover:text-teal-300 transition-colors">
                                    <span x-show="!selectedDokumenFile">Klik atau seret scan BAST/SK ke sini (Opsional)</span>
                                    <span x-show="selectedDokumenFile" class="text-teal-300 font-mono" x-text="selectedDokumenFile ? selectedDokumenFile.name : ''"></span>
                                </p>
                                <p class="text-[10px] text-slate-500">Format yang didukung: PDF, JPG, JPEG, PNG</p>
                            </div>
                        </div>

                        <div x-show="selectedDokumenFile" class="mt-2.5 flex items-center justify-between p-2.5 rounded-xl bg-teal-950/40 border border-teal-500/30 text-xs">
                            <span class="text-teal-200 font-mono truncate flex items-center gap-2">
                                <svg class="w-4 h-4 text-teal-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                <strong x-text="selectedDokumenFile?.name"></strong>
                                <span class="text-slate-400" x-text="'(' + (selectedDokumenFile?.size / 1024).toFixed(1) + ' KB)'"></span>
                            </span>
                            <button type="button" @click="clearDokumenFile()" class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500/30 text-rose-300 border border-rose-500/30 text-xs font-bold transition-all ml-2">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                <span>Batal</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Bagian 4: Checklist Unit Register NIBAR & Pembaruan Kondisi Serah Terima -->
                <template x-if="selectedAstap && selectedAstap.registers && selectedAstap.registers.length > 0">
                    <div class="p-3.5 sm:p-4 rounded-2xl bg-slate-950/90 border border-slate-800 space-y-3 shadow-inner">
                        <div class="flex items-center justify-between gap-3 border-b border-slate-800/80 pb-2.5">
                            <div class="flex items-center space-x-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg bg-teal-500/15 border border-teal-500/30 flex items-center justify-center text-teal-300 shadow-sm shadow-teal-500/10 shrink-0">
                                    <svg class="w-4 h-4 text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                </div>
                                <span class="text-xs font-black text-teal-300 uppercase tracking-wider flex items-center gap-1.5 min-w-0">
                                    <span class="truncate">Pilih Unit Register &amp; Kondisi Serah:</span>
                                    <span class="text-rose-400 shrink-0">*</span>
                                </span>
                            </div>
                            <div class="flex items-center space-x-2 shrink-0">
                                <span class="text-[10px] font-mono px-2 py-1 rounded-lg bg-teal-500/10 text-teal-300 border border-teal-500/25 whitespace-nowrap"
                                      x-text="(formData.selected_register_ids ? formData.selected_register_ids.length : 0) + ' Terpilih'"></span>
                                <button type="button" @click="toggleSelectAllReklasNibars(true)"
                                        class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-teal-500/20 hover:bg-teal-500/30 text-teal-300 border border-teal-500/40 transition-all cursor-pointer whitespace-nowrap active:scale-95">
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
                                             ? 'bg-teal-500/10 border-teal-500/50 shadow-sm shadow-teal-500/10'
                                             : 'bg-slate-900/60 border-slate-800/90 hover:border-slate-700')">
                                    
                                    <!-- Baris Atas: Checkbox + NIBAR (Kiri) & Nilai Satuan (Kanan) -->
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="flex items-center space-x-2.5 min-w-0 flex-1">
                                            <template x-if="reg.status !== 'Mutasi Keluar OPD' && reg.status !== 'Dihibahkan'">
                                                <input type="checkbox" :value="reg.id" x-model="formData.selected_register_ids"
                                                       @change="onSelectedNibarsChange()"
                                                       class="w-4 h-4 rounded border-slate-700 text-teal-500 focus:ring-teal-400/30 bg-slate-950 cursor-pointer shrink-0">
                                            </template>
                                            <template x-if="reg.status === 'Mutasi Keluar OPD' || reg.status === 'Dihibahkan'">
                                                <span class="text-slate-600 cursor-not-allowed text-xs shrink-0" :title="'Unit ini sudah dialihkan keluar (' + reg.status + ')'">🔒</span>
                                            </template>
                                            <span class="font-mono font-bold text-xs tracking-tight truncate"
                                                  :class="isNibarSelected(reg.id) ? 'text-teal-300' : 'text-emerald-400'"
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
                                                    <span class="text-xs font-mono font-extrabold text-teal-300"
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
                                        <div class="flex items-center gap-1.5 bg-slate-950/80 border border-teal-500/40 rounded-xl px-2.5 py-1.5 shadow-inner shrink-0"
                                             x-show="isNibarSelected(reg.id)">
                                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Kondisi Serah:</span>
                                            <select x-model="formData.register_kondisi_map[reg.id]"
                                                    @click.stop
                                                    class="bg-transparent border-none text-[11px] font-extrabold text-teal-300 focus:ring-0 focus:outline-none cursor-pointer py-0 pl-1 pr-6">
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

