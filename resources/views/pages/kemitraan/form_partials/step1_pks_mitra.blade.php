<!-- ========================================================================= -->
<!-- LANGKAH 1: DOKUMEN PKS & MITRA REKANAN PIHAK KETIGA (AKUN 1.5.2)          -->
<!-- ========================================================================= -->
<div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-cyan-400/10 text-cyan-300 border border-cyan-400/20 text-xs font-bold mb-2">
            <span>🤝 LANGKAH 1 DARI 3: LEGALITAS PERJANJIAN KERJA SAMA (PKS)</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-cyan-400/10 text-cyan-400 text-sm">📜</span>
            <span>Langkah 1: Dokumen PKS &amp; Identitas Mitra Rekanan</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Masukkan legalitas perjanjian kerja sama (PKS/MoU/KSO), identitas pihak ketiga/mitra rekanan, masa berlaku kerjasama, serta periode pembukuan (Akun 1.5.2).
        </p>
    </div>

    <!-- ===== BANNER ERROR INLINE LANGKAH 1 ===== -->
    <template x-if="stepErrors[1]">
        <div class="flex items-start gap-3 p-4 rounded-2xl bg-rose-950/60 border border-rose-500/50 shadow-lg shadow-rose-500/10 animate-[fadeInDown_0.25s_ease-out]">
            <span class="text-rose-400 text-lg mt-0.5 shrink-0">⚠️</span>
            <div class="min-w-0">
                <p class="text-xs font-bold text-rose-300 mb-0.5">Perhatian — Data Langkah 1 Belum Lengkap</p>
                <p class="text-xs text-rose-200/90 leading-relaxed" x-text="stepErrors[1]"></p>
            </div>
            <button type="button" @click="clearStepError(1)" class="ml-auto shrink-0 text-rose-400 hover:text-rose-200 transition-colors text-sm leading-none">✕</button>
        </div>
    </template>

    <!-- Bagian PKS & Mitra Pihak Ketiga -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-cyan-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🤝 Identitas Mitra &amp; Legalitas Dokumen PKS</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-cyan-300 bg-cyan-400/10 px-2 py-0.5 rounded-lg border border-cyan-400/20">
                Aset Kemitraan · Akun 1.5.2
            </span>
        </div>

        <!-- ========================================================================= -->
        <!-- 1. SINKRONISASI OBJEK ASET RSUD (TANAH / GEDUNG / RUANGAN YANG DISEWAKAN) -->
        <!-- ========================================================================= -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-r from-cyan-950/30 via-slate-900/90 to-slate-950/90 border border-cyan-500/30 shadow-xl space-y-3.5 relative">
            <div class="flex items-center justify-between gap-3 flex-wrap sm:flex-nowrap pb-3 border-b border-slate-800/80">
                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 flex items-center justify-center text-base shrink-0 shadow-inner">
                        🏛️
                    </div>
                    <div class="min-w-0">
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black tracking-wider text-cyan-300 uppercase">
                                Objek Aset Milik RSUD yang Dimanfaatkan / Dikerjasamakan
                            </span>
                            <span class="text-[10px] text-slate-400 font-mono">(Opsional)</span>
                        </div>
                        <p class="text-[11px] text-slate-400 truncate sm:whitespace-normal mt-0.5">
                            Pilih aset daerah milik RSUD (Semua KIB: KIB A s.d. E) jika kerja sama ini memanfaatkan aset milik RSUD.
                        </p>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-950/80 text-cyan-300 border border-cyan-500/30 flex items-center gap-1.5 shadow-sm">
                        <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                        <span>Sinkronisasi BMD</span>
                    </span>
                    <template x-if="formData.objek_nibar">
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 flex items-center gap-1">
                            <span>✓</span>
                            <span>Tertaut</span>
                        </span>
                    </template>
                </div>
            </div>

            <!-- Jika Belum Memilih Objek Aset (Input Pencarian Spotlight) -->
            <div x-show="!formData.objek_nibar" class="space-y-2">
                <div class="relative" @click.away="isObjekDropdownOpen = false">
                    <div class="relative flex items-center">
                        <div class="absolute left-3.5 text-slate-400 pointer-events-none flex items-center">
                            <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <input type="text"
                            x-model="objekSearchQuery"
                            @focus="isObjekDropdownOpen = true"
                            @input="isObjekDropdownOpen = true"
                            placeholder="Ketik NIBAR, nama barang aset RSUD (Semua KIB), lokasi ruangan, atau kode 108..."
                            class="w-full bg-slate-950 border border-slate-700/80 hover:border-slate-600 focus:border-cyan-400 focus:ring-2 focus:ring-cyan-500/20 rounded-xl pl-10 pr-24 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all shadow-inner">
                        
                        <div class="absolute right-2.5 flex items-center gap-1.5">
                            <template x-if="objekSearchQuery">
                                <button type="button" @click="objekSearchQuery = ''; isObjekDropdownOpen = true" class="text-slate-400 hover:text-white p-1 text-xs">
                                    ✕
                                </button>
                            </template>
                            <span class="text-[10px] font-mono text-cyan-400/80 bg-slate-900 px-2 py-0.5 rounded-md border border-slate-800">
                                Cari BMD
                            </span>
                        </div>
                    </div>

                    <!-- Dropdown Hasil Pencarian (Slice 5 Items) -->
                    <div x-show="isObjekDropdownOpen"
                        x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-1"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute left-0 right-0 top-full mt-2 bg-slate-950 border border-cyan-500/40 rounded-2xl shadow-2xl z-30 overflow-hidden divide-y divide-slate-900">
                        
                        <!-- Filter Kategori KIB (Semua KIB A - E) & Hitungan Hasil -->
                        <div class="p-2.5 bg-slate-900/90 flex items-center justify-between gap-2 border-b border-slate-800 flex-wrap">
                            <div class="flex items-center gap-1 flex-wrap">
                                <button type="button" @click="objekKibFilter = 'ALL'"
                                    :class="objekKibFilter === 'ALL' ? 'bg-cyan-500/20 text-cyan-300 border-cyan-400/60 font-extrabold shadow-sm shadow-cyan-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border-slate-800'"
                                    class="px-2.5 py-1 rounded-lg text-[10px] border transition-all">Semua KIB</button>
                                <button type="button" @click="objekKibFilter = 'KIB A'"
                                    :class="objekKibFilter === 'KIB A' ? 'bg-emerald-500/20 text-emerald-300 border-emerald-400/60 font-extrabold shadow-sm shadow-emerald-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border-slate-800'"
                                    class="px-2 py-1 rounded-lg text-[10px] border transition-all">🌱 KIB A (Tanah)</button>
                                <button type="button" @click="objekKibFilter = 'KIB B'"
                                    :class="objekKibFilter === 'KIB B' ? 'bg-amber-500/20 text-amber-300 border-amber-400/60 font-extrabold shadow-sm shadow-amber-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border-slate-800'"
                                    class="px-2 py-1 rounded-lg text-[10px] border transition-all">🚜 KIB B (Peralatan)</button>
                                <button type="button" @click="objekKibFilter = 'KIB C'"
                                    :class="objekKibFilter === 'KIB C' ? 'bg-indigo-500/20 text-indigo-300 border-indigo-400/60 font-extrabold shadow-sm shadow-indigo-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border-slate-800'"
                                    class="px-2 py-1 rounded-lg text-[10px] border transition-all">🏢 KIB C (Gedung)</button>
                                <button type="button" @click="objekKibFilter = 'KIB D'"
                                    :class="objekKibFilter === 'KIB D' ? 'bg-purple-500/20 text-purple-300 border-purple-400/60 font-extrabold shadow-sm shadow-purple-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border-slate-800'"
                                    class="px-2 py-1 rounded-lg text-[10px] border transition-all">🛣️ KIB D (Jalan)</button>
                                <button type="button" @click="objekKibFilter = 'KIB E'"
                                    :class="objekKibFilter === 'KIB E' ? 'bg-teal-500/20 text-teal-300 border-teal-400/60 font-extrabold shadow-sm shadow-teal-500/20' : 'bg-slate-950 text-slate-400 hover:text-white border-slate-800'"
                                    class="px-2 py-1 rounded-lg text-[10px] border transition-all">📦 KIB E (Lainnya)</button>
                            </div>
                            <span class="text-[10px] font-mono text-cyan-400/80 bg-slate-950 px-2 py-0.5 rounded-md border border-slate-800 shrink-0"
                                x-text="'Menampilkan 5 dari ' + totalFilteredObjekCount + ' aset'"></span>
                        </div>

                        <!-- Opsi Tanpa Objek Aset Khusus -->
                        <div @click="clearObjekAset(); isObjekDropdownOpen = false"
                            class="px-3.5 py-2.5 bg-slate-900/40 hover:bg-slate-800/80 cursor-pointer flex items-center gap-2 text-slate-400 hover:text-slate-200 text-xs transition-colors">
                            <span class="text-slate-500">🚫</span>
                            <span class="font-medium">Tanpa Objek Khusus (Aset Ditambahkan Langsung oleh Mitra)</span>
                        </div>

                        <!-- List 5 Item Aset Teratas -->
                        <template x-for="item in filteredObjekAsetList" :key="item.register_id">
                            <div @click="selectObjekAset(item)"
                                class="p-3 hover:bg-cyan-950/40 cursor-pointer transition-all border-b border-slate-900/80 last:border-b-0 group">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="px-2 py-0.5 rounded text-[9.5px] font-mono font-extrabold uppercase shrink-0"
                                            :class="{
                                                'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40': item.kib === 'KIB A',
                                                'bg-amber-500/20 text-amber-300 border border-amber-500/40': item.kib === 'KIB B',
                                                'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40': item.kib === 'KIB C',
                                                'bg-purple-500/20 text-purple-300 border border-purple-500/40': item.kib === 'KIB D',
                                                'bg-teal-500/20 text-teal-300 border border-teal-500/40': item.kib === 'KIB E'
                                            }"
                                            x-text="item.kib"></span>
                                        <span class="font-bold text-white text-xs truncate group-hover:text-cyan-300 transition-colors" x-text="item.nama_barang"></span>
                                    </div>
                                    <span class="font-mono text-cyan-400 text-[11px] font-bold shrink-0 bg-cyan-950/50 px-2 py-0.5 rounded border border-cyan-500/30" x-text="item.nibar"></span>
                                </div>
                                <div class="flex items-center justify-between text-[11px] text-slate-400 gap-2 mt-1">
                                    <span class="truncate flex items-center gap-1">
                                        <span>📍</span>
                                        <span x-text="item.unit_nama || item.alamat_barang || 'RSUD Dr. H. Koesnandi'"></span>
                                    </span>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span x-show="item.luas" class="font-mono text-slate-300" x-text="item.luas + ' m²'"></span>
                                        <template x-if="item.pks_aktif">
                                            <span class="px-1.5 py-0.5 rounded text-[9px] bg-amber-500/20 text-amber-300 border border-amber-500/40 font-bold"
                                                x-text="'⚠️ PKS Aktif: ' + item.pks_aktif.nomor_pks"></span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="filteredObjekAsetList.length === 0">
                            <div class="p-6 text-center text-xs space-y-2">
                                <template x-if="dbObjekAsetList.length === 0">
                                    <div class="space-y-1.5">
                                        <div class="text-2xl">🏛️</div>
                                        <div class="font-bold text-slate-200">Belum Ada Aset RSUD yang Direklasifikasi ke Kemitraan</div>
                                        <p class="text-[11px] text-slate-400 max-w-md mx-auto leading-relaxed">
                                            Aset tanah/gedung milik RSUD yang disewakan ke mitra harus direklasifikasi terlebih dahulu ke Akun 1.5.2 pada Menu Reklasifikasi ASTAP agar dapat dipilih di sini.
                                        </p>
                                    </div>
                                </template>
                                <template x-if="dbObjekAsetList.length > 0">
                                    <div class="text-slate-400 py-2">
                                        Tidak ada aset kemitraan yang cocok dengan kata kunci pencarian.
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
                <p class="text-[10.5px] text-slate-500 italic">
                    💡 Tips: Jika kemitraan ini berupa penerimaan aset baru dari rekanan tanpa menyewa lahan/gedung RSUD, bagian ini boleh dilewati.
                </p>
            </div>

            <!-- Jika Sudah Memilih Objek Aset (Kartu Aset BMD Terpilih) -->
            <div x-show="formData.objek_nibar" class="p-4 rounded-2xl bg-gradient-to-r from-cyan-950/30 via-slate-900/90 to-slate-950 border border-cyan-500/40 space-y-3 relative shadow-lg overflow-hidden">
                <div class="flex items-start justify-between gap-3 flex-wrap sm:flex-nowrap">
                    <div class="space-y-1.5 min-w-0 flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-0.5 rounded-lg text-[10px] font-mono font-bold"
                                :class="(formData.objek_aset_terpilih?.kib === 'KIB A') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-indigo-500/20 text-indigo-300 border border-indigo-500/40'"
                                x-text="formData.objek_aset_terpilih?.kib || 'Aset BMD'"></span>
                            <span class="text-xs font-mono font-extrabold text-cyan-300 bg-cyan-950/60 px-2.5 py-0.5 rounded-lg border border-cyan-500/30" x-text="'NIBAR: ' + formData.objek_nibar"></span>
                            <span class="text-[10px] text-emerald-400 font-bold bg-emerald-500/10 px-2 py-0.5 rounded-md border border-emerald-500/20">
                                ✓ Terhubung Database Aset
                            </span>
                        </div>
                        <h4 class="text-sm font-extrabold text-white leading-snug" x-text="formData.objek_aset_terpilih?.nama_barang || 'Aset Objek RSUD Terpilih'"></h4>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-2 text-xs pt-1">
                            <div class="p-2 rounded-xl bg-slate-950/80 border border-slate-800">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">📍 Lokasi / Ruangan</span>
                                <span class="font-bold text-slate-200 block truncate" x-text="formData.objek_aset_terpilih?.unit_nama || formData.objek_aset_terpilih?.alamat_barang || 'RSUD Dr. H. Koesnandi'"></span>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-950/80 border border-slate-800">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">📐 Luas Objek</span>
                                <span class="font-mono font-bold text-cyan-300 block" x-text="formData.objek_aset_terpilih?.luas ? (formData.objek_aset_terpilih.luas + ' m²') : '-'"></span>
                            </div>
                            <div class="p-2 rounded-xl bg-slate-950/80 border border-slate-800">
                                <span class="text-[10px] uppercase font-bold text-slate-500 block">📜 Sertifikat / Dokumen</span>
                                <span class="font-mono font-bold text-slate-300 block truncate" x-text="formData.objek_aset_terpilih?.sertifikat || '-'"></span>
                            </div>
                        </div>
                    </div>
                    <button type="button" @click="clearObjekAset()"
                        class="px-3 py-1.5 rounded-xl bg-slate-800/90 hover:bg-rose-950/80 border border-slate-700 hover:border-rose-500/50 text-slate-300 hover:text-rose-200 text-xs font-bold transition-all shrink-0 cursor-pointer shadow-sm">
                        ✕ Ganti / Lepas Objek
                    </button>
                </div>

                <!-- Peringatan jika aset sedang dalam PKS aktif -->
                <template x-if="formData.objek_aset_terpilih?.pks_aktif">
                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/30 flex items-start gap-2.5 text-xs text-amber-200">
                        <span class="text-amber-400 text-sm mt-0.5">⚠️</span>
                        <div>
                            <span class="font-bold">Aset ini memiliki Perjanjian Kerja Sama aktif: </span>
                            <span class="font-mono font-bold" x-text="formData.objek_aset_terpilih?.pks_aktif.nomor_pks"></span>
                            <span x-text="' (' + formData.objek_aset_terpilih?.pks_aktif.mitra_nama + ')'"></span>
                            <span x-show="formData.objek_aset_terpilih?.pks_aktif.tanggal_selesai" x-text="' sampai ' + formData.objek_aset_terpilih?.pks_aktif.tanggal_selesai"></span>.
                            <div class="text-[11px] text-amber-300/80 mt-0.5">
                                Pastikan tanggal mulai dan berakhir kemitraan baru ini tidak tumpang tindih dengan periode yang sedang berjalan.
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Bentuk Skema Kemitraan Sesuai Permendagri 108 Akun 1.5.2 -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                <span>Bentuk Skema Kemitraan Sesuai Permendagri 108 / SAP <span class="text-rose-400">*</span></span>
                <span class="text-[10px] text-cyan-400/90 font-mono">Akun Neraca 1.5.2</span>
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2.5">
                <!-- 1. Sewa (1.5.2.01.01.01) -->
                <button type="button" @click="formData.skema_kemitraan = 'Sewa'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.skema_kemitraan === 'Sewa' ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-black shadow-lg shadow-cyan-500/20 ring-1 ring-cyan-400' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-xs font-bold">Sewa</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Sewa Barang / Alat</span>
                    <span class="block font-mono text-[9px] text-cyan-400/70 mt-0.5">1.5.2.01.01.01</span>
                </button>

                <!-- 2. KSP (1.5.2.01.01.02) -->
                <button type="button" @click="formData.skema_kemitraan = 'KSP'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.skema_kemitraan === 'KSP' ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-black shadow-lg shadow-cyan-500/20 ring-1 ring-cyan-400' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-xs font-bold">KSP</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Kerja Sama Pemanfaatan</span>
                    <span class="block font-mono text-[9px] text-cyan-400/70 mt-0.5">1.5.2.01.01.02</span>
                </button>

                <!-- 3. BGS / BSG (1.5.2.01.01.03) -->
                <button type="button" @click="formData.skema_kemitraan = 'BGS/BSG'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="(formData.skema_kemitraan === 'BGS/BSG' || formData.skema_kemitraan === 'BSG' || formData.skema_kemitraan === 'BGS') ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-black shadow-lg shadow-cyan-500/20 ring-1 ring-cyan-400' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-xs font-bold">BGS / BSG</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Bangun Guna / Serah Guna</span>
                    <span class="block font-mono text-[9px] text-cyan-400/70 mt-0.5">1.5.2.01.01.03</span>
                </button>

                <!-- 4. KSPI (1.5.2.01.01.04) -->
                <button type="button" @click="formData.skema_kemitraan = 'KSPI'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="(formData.skema_kemitraan === 'KSPI' || formData.skema_kemitraan === 'KSO') ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-black shadow-lg shadow-cyan-500/20 ring-1 ring-cyan-400' : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-xs font-bold">KSPI</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Penyediaan Infrastruktur</span>
                    <span class="block font-mono text-[9px] text-cyan-400/70 mt-0.5">1.5.2.01.01.04</span>
                </button>
            </div>
        </div>

        <!-- Nama Mitra / Rekanan Pihak Ketiga (Combobox / Filter Riwayat & Bebas Ketik) -->
        <div class="relative space-y-1.5" @click.away="isMitraDropdownOpen = false">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-200">
                    Nama Perusahaan Mitra / Rekanan Pihak Ketiga <span class="text-rose-400">*</span>
                </label>
                <template x-if="masterMitraList && masterMitraList.length > 0">
                    <span class="text-[10px] text-cyan-400 font-mono font-normal flex items-center gap-1 bg-cyan-500/10 px-2 py-0.5 rounded-md border border-cyan-500/20">
                        <span>⚡</span>
                        <span>Riwayat Tersimpan</span>
                    </span>
                </template>
            </div>

            <!-- Input Box dengan Ikon dan Clear Button -->
            <div class="relative">
                <input type="text" 
                    x-model="formData.mitra_nama" 
                    @focus="isMitraDropdownOpen = true"
                    @input="isMitraDropdownOpen = true; onMitraInput($event.target.value)"
                    @change="onMitraInput($event.target.value)"
                    @keydown.escape="isMitraDropdownOpen = false"
                    required
                    autocomplete="off"
                    placeholder="Ketik atau pilih nama mitra / perusahaan (contoh: PT. Roche, PT. Kimia Farma...)"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-3 pl-10 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">
                
                <!-- Ikon Mitra Perusahaan -->
                <svg class="w-4 h-4 text-cyan-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>

                <!-- Tombol Kosongkan Input (Diposisikan di kanan input dengan inline style pasti) -->
                <button type="button" 
                    x-show="formData.mitra_nama"
                    @click="formData.mitra_nama = ''; isMitraDropdownOpen = true" 
                    title="Kosongkan nama mitra"
                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                    class="rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                    ✕
                </button>
            </div>

            <!-- Floating Dropdown Saran / Filter Mitra (Muncul saat fokus/diketik) -->
            <div x-show="isMitraDropdownOpen" 
                x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                style="max-height: 240px !important; overflow-y: auto !important;"
                class="absolute z-50 mt-1.5 w-full bg-slate-900 border border-cyan-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">
                
                <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                    <span>Pilih Riwayat / Ketik Mitra Baru</span>
                    <span class="font-mono text-slate-400" x-text="filteredMitraList.length + ' saran'"></span>
                </div>

                <template x-for="(mitra, mIdx) in filteredMitraList" :key="mIdx">
                    <div @click="selectMitra(mitra)"
                        class="px-4 py-2.5 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                        :class="formData.mitra_nama === (mitra.nama || mitra) ? 'bg-cyan-500/20 text-cyan-200' : 'text-slate-200'">
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
                <template x-if="formData.mitra_nama && filteredMitraList.length === 0">
                    <div class="p-3 text-center text-xs text-slate-400 bg-slate-950/50">
                        <span class="text-cyan-300 font-semibold" x-text="'➕ Gunakan Mitra Baru: &quot;' + formData.mitra_nama + '&quot;'"></span>
                        <p class="text-[10px] text-slate-500 mt-0.5">Nama mitra ini akan otomatis tersimpan ke riwayat kemitraan setelah formulir disimpan.</p>
                    </div>
                </template>
            </div>
            
            <!-- Riwayat Tersimpan Mitra (Badge Shortcut Riwayat Database) -->
            <template x-if="masterMitraList && masterMitraList.length > 0">
                <div class="pt-1 flex flex-wrap items-center gap-1.5">
                    <span class="text-[10px] text-slate-500 font-semibold mr-1">Riwayat Tersimpan:</span>
                    <template x-for="(m, mIdx) in masterMitraList.slice(0, 5)" :key="mIdx">
                        <button type="button" @click="selectMitra(m)"
                            class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 cursor-pointer transition-colors flex items-center gap-1"
                            :class="formData.mitra_nama === (m.nama || m) ? 'bg-cyan-500/20 border-cyan-400 text-cyan-300 font-bold ring-1 ring-cyan-400' : ''">
                            <span x-text="m.nama || m"></span>
                            <template x-if="m.pimpinan || m.alamat">
                                <span class="text-[9px] text-cyan-400" title="Ada data pimpinan & alamat tersimpan">⚡</span>
                            </template>
                        </button>
                    </template>
                </div>
            </template>
        </div>

        <!-- Pejabat Mitra & Alamat Mitra -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-1">
            <!-- Pejabat Mitra -->
            <div class="relative space-y-1.5" @click.away="isPejabatDropdownOpen = false">
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        <span>Pejabat Mitra / Direktur Rekanan</span>
                    </span>
                </label>
                <div class="relative">
                    <input type="text" x-model="formData.mitra_pimpinan"
                        @focus="isPejabatDropdownOpen = true"
                        @input="isPejabatDropdownOpen = true"
                        @keydown.escape="isPejabatDropdownOpen = false"
                        autocomplete="off"
                        placeholder="Nama Pejabat / Direktur / Penanggung Jawab Pihak Ketiga"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 pr-9 text-xs text-white focus:outline-none transition-all placeholder-slate-500 shadow-inner">
                    <button type="button" 
                        x-show="formData.mitra_pimpinan"
                        @click="formData.mitra_pimpinan = ''; isPejabatDropdownOpen = true" 
                        title="Kosongkan"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                        class="rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                        ✕
                    </button>
                </div>

                <!-- Floating Dropdown Riwayat Pejabat Mitra -->
                <div x-show="isPejabatDropdownOpen && filteredPejabatList.length > 0" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    style="max-height: 220px !important; overflow-y: auto !important;"
                    class="absolute z-50 mt-1 w-full bg-slate-900 border border-cyan-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">
                    
                    <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                        <span>Pilih Riwayat Pejabat Mitra</span>
                        <span class="font-mono text-slate-400" x-text="filteredPejabatList.length + ' saran'"></span>
                    </div>

                    <template x-for="(pejabat, pIdx) in filteredPejabatList" :key="pIdx">
                        <div @click="selectPejabat(pejabat)"
                            class="px-3.5 py-2 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2.5 text-left"
                            :class="formData.mitra_pimpinan === pejabat ? 'bg-cyan-500/20 text-cyan-200 font-bold' : 'text-slate-200'">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xs text-cyan-400/80">👤</span>
                                <span class="text-xs group-hover:text-cyan-300 truncate" x-text="pejabat"></span>
                            </div>
                            <template x-if="pIdx === 0">
                                <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 font-bold shrink-0">
                                    ⚡ Terbaru
                                </span>
                            </template>
                            <template x-if="pIdx > 0">
                                <span class="text-[9px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700 font-medium shrink-0">
                                    Riwayat Lama
                                </span>
                            </template>
                        </div>
                    </template>
                </div>
                <p class="text-[10px] text-slate-500 mt-1">Nama direktur, pimpinan cabang, atau kuasa rekanan penandatangan PKS.</p>
            </div>

            <!-- Alamat Mitra -->
            <div class="relative space-y-1.5" @click.away="isAlamatDropdownOpen = false">
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                        <span>Alamat Mitra / Domisili Kantor</span>
                    </span>
                </label>
                <div class="relative">
                    <input type="text" x-model="formData.mitra_alamat"
                        @focus="isAlamatDropdownOpen = true"
                        @input="isAlamatDropdownOpen = true"
                        @keydown.escape="isAlamatDropdownOpen = false"
                        autocomplete="off"
                        placeholder="Alamat kantor pusat / domisili rekanan mitra"
                        class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 pr-9 text-xs text-white focus:outline-none transition-all placeholder-slate-500 shadow-inner">
                    <button type="button" 
                        x-show="formData.mitra_alamat"
                        @click="formData.mitra_alamat = ''; isAlamatDropdownOpen = true" 
                        title="Kosongkan"
                        style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                        class="rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                        ✕
                    </button>
                </div>

                <!-- Floating Dropdown Riwayat Alamat Mitra -->
                <div x-show="isAlamatDropdownOpen && filteredAlamatList.length > 0" 
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    x-transition:leave="transition ease-in duration-75"
                    x-transition:leave-start="opacity-100 translate-y-0"
                    x-transition:leave-end="opacity-0 translate-y-1"
                    style="max-height: 220px !important; overflow-y: auto !important;"
                    class="absolute z-50 mt-1 w-full bg-slate-900 border border-cyan-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">
                    
                    <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-cyan-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                        <span>Pilih Riwayat Alamat Kantor</span>
                        <span class="font-mono text-slate-400" x-text="filteredAlamatList.length + ' saran'"></span>
                    </div>

                    <template x-for="(alamat, aIdx) in filteredAlamatList" :key="aIdx">
                        <div @click="selectAlamat(alamat)"
                            class="px-3.5 py-2 hover:bg-cyan-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-2.5 text-left"
                            :class="formData.mitra_alamat === alamat ? 'bg-cyan-500/20 text-cyan-200 font-bold' : 'text-slate-200'">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xs text-cyan-400/80">📍</span>
                                <span class="text-xs group-hover:text-cyan-300 truncate" x-text="alamat"></span>
                            </div>
                            <template x-if="aIdx === 0">
                                <span class="text-[9px] px-2 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/40 font-bold shrink-0">
                                    ⚡ Terbaru
                                </span>
                            </template>
                            <template x-if="aIdx > 0">
                                <span class="text-[9px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700 font-medium shrink-0">
                                    Riwayat Lama
                                </span>
                            </template>
                        </div>
                    </template>
                </div>
                <p class="text-[10px] text-slate-500 mt-1">Alamat kantor domisili rekanan mitra penyedia aset kemitraan.</p>
            </div>
        </div>


        <!-- Nomor & Tanggal PKS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
            <div class="flex flex-col">
                <div class="h-7 flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-200">
                        Nomor Dokumen Perjanjian Kerja Sama (PKS / MoU) <span class="text-rose-400">*</span>
                    </label>
                </div>
                <input type="text" x-model="formData.nomor_pks" required
                    placeholder="Contoh: 000.2.3.2/PKS-KSO/430.10.7/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>
            <div class="flex flex-col">
                <div class="h-7 flex items-center justify-between mb-1.5 gap-2">
                    <label class="block text-xs font-bold text-slate-200">
                        Tanggal Penandatanganan PKS <span class="text-rose-400">*</span>
                    </label>
                    <span class="text-[10px] font-mono text-cyan-300 bg-cyan-950/50 px-2 py-0.5 rounded-md border border-cyan-700/50 shrink-0">Maks: Hari Ini</span>
                </div>
                <input type="text" 
                    x-datepicker="{ maxDate: 'today' }" 
                    x-model="formData.tanggal_pks"
                    @input="syncTahunTriwulanFromPks($event.target.value)"
                    @change="syncTahunTriwulanFromPks($event.target.value)"
                    required
                    placeholder="dd/mm/yyyy"
                    :class="isTanggalPksInvalid() ? 'border-rose-500 focus:border-rose-400 bg-rose-950/20' : 'border-slate-700 focus:border-cyan-400 bg-slate-900'"
                    class="w-full border rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none transition-colors">
                
                <!-- Peringatan Visual Jika Tanggal PKS Lebih dari Hari Ini -->
                <template x-if="isTanggalPksInvalid()">
                    <div class="mt-1.5 flex items-center gap-1.5 text-[10.5px] text-rose-400 font-semibold animate-pulse">
                        <span>⚠️</span>
                        <span>Tanggal penandatanganan PKS tidak boleh melebihi tanggal hari ini!</span>
                    </div>
                </template>
            </div>
        </div>


        <!-- Masa Berlaku Kerja Sama (Mulai s.d. Selesai) -->
        <!-- Masa Berlaku Kerja Sama (Mulai s.d. Selesai) -->
        <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <span class="text-xs font-bold text-slate-200 block">
                    🗓️ Jangka Waktu / Masa Berlaku Kerjasama (Konsesi)
                </span>
                <template x-if="durasiKonsesiText">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-mono font-bold bg-cyan-950/70 text-cyan-300 border border-cyan-500/30">
                        <span>⏳ Estimasi Durasi:</span>
                        <span x-text="durasiKonsesiText"></span>
                    </span>
                </template>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 items-start">
                <div class="flex flex-col">
                    <div class="h-7 flex items-center justify-between mb-1.5 gap-2">
                        <label class="text-[11px] font-semibold text-slate-300 truncate">
                            Tanggal Mulai Berlaku Kerjasama
                        </label>
                        <div class="flex items-center gap-1.5 text-[9.5px] font-mono text-cyan-400/90 shrink-0">
                            <span x-show="formData.tanggal_pks" class="bg-slate-800/80 px-2 py-0.5 rounded-md border border-slate-700/80 text-[9.5px]">Min (PKS): <span class="text-cyan-300 font-semibold" x-text="formatTanggalIndo(formData.tanggal_pks)"></span></span>
                            <span class="bg-cyan-950/50 text-cyan-300 px-2 py-0.5 rounded-md border border-cyan-700/50 text-[9.5px]">Maks: Hari Ini</span>
                        </div>
                    </div>
                    <input type="text" 
                        x-datepicker="{ minDate: formatDateToIso(formData.tanggal_pks) || undefined, maxDate: 'today' }" 
                        x-model="formData.tanggal_mulai"
                        placeholder="dd/mm/yyyy"
                        :class="isTanggalMulaiInvalid() ? 'border-rose-500 focus:border-rose-400 bg-rose-950/20' : 'border-slate-700 focus:border-cyan-400 bg-slate-950'"
                        class="w-full border rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none transition-colors">
                    
                    <!-- Peringatan Visual Jika Tanggal Mulai Tidak Valid -->
                    <template x-if="isTanggalMulaiInvalid()">
                        <div class="mt-1.5 flex items-center gap-1.5 text-[10.5px] text-rose-400 font-semibold animate-pulse">
                            <span>⚠️</span>
                            <span x-text="getTanggalMulaiErrorMsg()"></span>
                        </div>
                    </template>
                </div>
                <div class="flex flex-col">
                    <div class="h-7 flex items-center justify-between mb-1.5 gap-2">
                        <label class="text-[11px] font-semibold text-slate-300 truncate">
                            Tanggal Berakhir Kerjasama (Konsesi Berakhir)
                        </label>
                        <div class="flex items-center gap-1.5 text-[9.5px] font-mono text-cyan-400/90 shrink-0">
                            <span x-show="formData.tanggal_mulai || formData.tanggal_pks" class="bg-slate-800/80 px-2 py-0.5 rounded-md border border-slate-700/80 text-[9.5px]">
                                Min: <span class="text-cyan-300 font-semibold" x-text="formatTanggalIndo(formData.tanggal_mulai || formData.tanggal_pks)"></span>
                            </span>
                        </div>
                    </div>
                    <input type="text" 
                        x-datepicker="{ minDate: formatDateToIso(formData.tanggal_mulai || formData.tanggal_pks) || undefined }" 
                        x-model="formData.tanggal_selesai"
                        placeholder="dd/mm/yyyy"
                        :class="isTanggalSelesaiInvalid() ? 'border-rose-500 focus:border-rose-400 bg-rose-950/20' : 'border-slate-700 focus:border-cyan-400 bg-slate-950'"
                        class="w-full border rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none transition-colors">
                    
                    <!-- Peringatan Visual Jika Tanggal Selesai Kurang dari Tanggal Mulai -->
                    <template x-if="isTanggalSelesaiInvalid()">
                        <div class="mt-1.5 flex items-center gap-1.5 text-[10.5px] text-rose-400 font-semibold animate-pulse">
                            <span>⚠️</span>
                            <span>Tanggal berakhir tidak boleh di bawah (lebih awal dari) tanggal mulai kerjasama!</span>
                        </div>
                    </template>
                </div>
            </div>
            <p class="text-[10.5px] text-cyan-400/80 italic">
                ℹ️ Catatan: Selama masa konsesi berlangsung, aset ini dibukukan pada Akun Neraca 1.5.2 (Aset Kemitraan Pihak Ketiga). Setelah konsesi berakhir, aset dapat diserahkan penuh menjadi Aset Tetap RSUD Koesnandi melalui Reklasifikasi.
            </p>
        </div>

        <!-- Tahun Perolehan & Triwulan -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span>Tahun Pembukuan / Mulai Operasional <span class="text-rose-400">*</span></span>
                    <span class="inline-flex items-center gap-1 text-[10px] text-cyan-400/90 font-medium bg-cyan-950/40 px-2 py-0.5 rounded-md border border-cyan-800/40">
                        <svg class="w-3 h-3 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Sinkron PKS
                    </span>
                </label>
                <input type="number" x-model.number="formData.tahun_perolehan" required min="1990" :max="new Date().getFullYear()"
                    placeholder="{{ date('Y') }}"
                    @input="if(formData.tahun_perolehan > {{ date('Y') }}) formData.tahun_perolehan = {{ date('Y') }};"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span>Periode Triwulan Pembukuan <span class="text-rose-400">*</span></span>
                    <span class="inline-flex items-center gap-1 text-[10px] text-cyan-400/90 font-medium bg-cyan-950/40 px-2 py-0.5 rounded-md border border-cyan-800/40">
                        <svg class="w-3 h-3 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                        Sinkron PKS
                    </span>
                </label>
                <select x-model="formData.triwulan" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
                    <option value="TW I">TW I (Januari - Maret)</option>
                    <option value="TW II">TW II (April - Juni)</option>
                    <option value="TW III">TW III (Juli - September)</option>
                    <option value="TW IV">TW IV (Oktober - Desember)</option>
                </select>
            </div>
        </div>

        <!-- Ruang Lingkup & Keterangan Kerjasama -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Ruang Lingkup / Keterangan Kerjasama
            </label>
            <textarea x-model="formData.kemitraan_keterangan" rows="2"
                placeholder="Contoh: Kerja Sama Operasional (KSO) penempatan alat laboratorium analyzer dengan skema komitmen reagen, atau Bangun Guna Serah gedung parkir..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
        </div>

        <!-- Unggah Berkas Dokumen BAST / PKS Kerja Sama -->
        <div class="p-4 rounded-2xl bg-slate-900/60 border border-slate-800 space-y-3">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-200">
                    Unggah Berkas Dokumen BAST / PKS Kerja Sama
                </label>
                <span class="text-[10.5px] text-slate-400 font-mono">Format: PDF, JPG, PNG (Maks 10MB)</span>
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <input type="file" id="inputDokumenBastForm" name="dokumen_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden"
                       @change="handleFormFileSelect($event)">

                <button type="button" @click="triggerFormFileSelect()"
                        class="w-full sm:w-auto px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-cyan-300 border border-slate-700/80 transition-all flex items-center justify-center gap-2 cursor-pointer active:scale-95 shadow-sm">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    <span>Pilih Berkas Dokumen</span>
                </button>

                <div class="flex-1 min-w-0 w-full">
                    <template x-if="selectedFile">
                        <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-emerald-950/30 border border-emerald-500/40 text-xs text-emerald-300 font-mono">
                            <span class="truncate" x-text="selectedFile.name"></span>
                            <button type="button" @click="clearFormFileSelect()" class="text-rose-400 hover:text-rose-300 font-bold ml-2">✕</button>
                        </div>
                    </template>
                    <template x-if="!selectedFile && formData.dokumen_path">
                        <div class="flex items-center justify-between px-3.5 py-2 rounded-xl bg-slate-950 border border-slate-800 text-xs text-slate-300 font-mono">
                            <span class="truncate" x-text="'Tersimpan: ' + formData.dokumen_path.split('/').pop()"></span>
                            <a :href="'/storage/' + formData.dokumen_path" target="_blank" class="text-cyan-400 hover:underline text-[11px] ml-2 font-sans font-bold">Lihat</a>
                        </div>
                    </template>
                    <template x-if="!selectedFile && !formData.dokumen_path">
                        <span class="text-xs text-slate-500 italic block pl-1">Belum ada berkas dokumen yang dipilih (opsional).</span>
                    </template>
                </div>
            </div>
        </div>

    </div>

</div>
