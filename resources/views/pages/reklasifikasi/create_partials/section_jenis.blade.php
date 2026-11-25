<!-- SEKSI 2: JENIS & KATEGORI REKLASIFIKASI (6 PILIHAN SIMETRIS 3x2) -->
<div class="p-6 sm:p-7 rounded-3xl bg-slate-900/90 border border-slate-800 shadow-xl space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-800/80 pb-4">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-xl bg-violet-500/10 border border-violet-500/20 text-violet-400 flex items-center justify-center font-black text-xs font-mono">
                02
            </span>
            <div>
                <h2 class="text-sm sm:text-base font-bold text-white tracking-wide">
                    Jenis &amp; Sifat Reklasifikasi (PMDN 108)
                </h2>
                <p class="text-xs text-slate-400 mt-0.5">
                    Tentukan dasar perlakuan akuntansi mutasi buku atau penyesuaian status aset daerah.
                </p>
            </div>
        </div>
        <div>
            <template x-if="!selectedAstap">
                <span class="text-[11px] font-semibold text-amber-400/90 bg-amber-500/10 px-3 py-1 rounded-full border border-amber-500/20 flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5 text-amber-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <span>Terkunci &bull; Pilih Aset di Tahap 01</span>
                </span>
            </template>
            <template x-if="selectedAstap">
                <span class="text-[11px] font-semibold text-emerald-400 bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-500/20 flex items-center gap-1.5 self-start sm:self-auto">
                    <svg class="w-3.5 h-3.5 text-emerald-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span>Siap Dikonfigurasi</span>
                </span>
            </template>
        </div>
    </div>

    <!-- Banner Callout saat Belum Memilih Aset -->
    <template x-if="!selectedAstap">
        <div class="p-4 rounded-2xl bg-amber-950/20 border border-dashed border-amber-500/30 flex items-center gap-3.5 transition-all">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-xs font-bold text-amber-300">Tahap 02 Terkunci Sementara</h4>
                <p class="text-[11px] text-slate-400 mt-0.5">
                    Silakan pilih salah satu barang dari Buku Inventaris ASTAP pada <strong class="text-cyan-400">Tahap 01</strong> di atas terlebih dahulu untuk membuka dan memilih jenis perlakuan reklasifikasi.
                </p>
            </div>
        </div>
    </template>

    <!-- Pilihan 6 Kartu Radio Interaktif (Grid 3 Kolom x 2 Baris Simetris) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3.5 transition-all duration-300"
         :class="{ 'opacity-30 pointer-events-none select-none filter blur-[0.3px]': !selectedAstap }">
        <!-- 1. Koreksi Rekening / Pindah KIB -->
        <label class="group relative flex flex-col justify-start p-4 rounded-2xl border transition-all duration-200 select-none space-y-2.5"
               @click="if (isCurrentAstapKdp()) { $event.preventDefault(); window.showSimatToast ? window.showSimatToast('⚠️ Aset KDP (KIB F) belum selesai, pengalihan definitif hanya lewat KDP Selesai ➔ Definitif!', 'warning') : alert('Terkunci untuk aset KDP!'); }"
               :class="{
                   'opacity-40 cursor-not-allowed bg-slate-950/40 border-slate-900/80': isCurrentAstapKdp(),
                   'cursor-pointer bg-indigo-950/30 border-indigo-500 ring-1 ring-indigo-500/40 shadow-lg shadow-indigo-950/50': !isCurrentAstapKdp() && formData.jenis_reklas === 'KOREKSI_REKENING',
                   'cursor-pointer bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950': !isCurrentAstapKdp() && formData.jenis_reklas !== 'KOREKSI_REKENING'
               }">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 border"
                          :class="isCurrentAstapKdp() ? 'opacity-50 text-slate-500 bg-slate-900 border-slate-800' : 'bg-indigo-500/10 border-indigo-500/20 text-indigo-400'">
                        🔄
                    </span>
                    <div>
                        <span class="text-xs font-bold transition-colors block"
                              :class="isCurrentAstapKdp() ? 'text-slate-500' : 'text-white group-hover:text-indigo-300'">
                            Koreksi Rekening / Pindah KIB
                        </span>
                        <span x-show="isCurrentAstapKdp()" class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 mt-0.5 inline-block">
                            Terkunci (KDP)
                        </span>
                        <span x-show="!isCurrentAstapKdp()" class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mt-0.5 inline-block">
                            Salah Kamar / Akun 108
                        </span>
                    </div>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="KOREKSI_REKENING" :disabled="!selectedAstap || isCurrentAstapKdp()" @change="onJenisReklasChange()" class="text-indigo-600 focus:ring-0 mt-1" :class="(!selectedAstap || isCurrentAstapKdp()) ? 'cursor-not-allowed opacity-50 text-slate-700' : 'cursor-pointer'">
            </div>
            <p class="text-[11px] leading-relaxed"
               :class="isCurrentAstapKdp() ? 'text-slate-600' : 'text-slate-400'"
               x-text="isCurrentAstapKdp() ? 'Terkunci: KDP belum selesai, pengalihan definitif hanya lewat KDP Selesai ➔ Definitif.' : 'Koreksi salah kamar klasifikasi KIB (A s/d F, ATB, Kemitraan) atau penyesuaian hierarki kode rekening Permendagri 108.'"></p>
        </label>

        <!-- 2. KDP Selesai ➔ Definitif (Khusus Aset KIB F / KDP) -->
        <label class="group relative flex flex-col justify-start p-4 rounded-2xl border transition-all duration-200 select-none space-y-2.5"
               @click="if (!isAssetKdp()) { $event.preventDefault(); window.showSimatToast ? window.showSimatToast('⚠️ Opsi KDP Selesai ➔ Definitif hanya dapat digunakan untuk aset KDP (KIB F / Akun 1.3.6)!', 'warning') : alert('Khusus aset KDP (KIB F)!'); }"
               :class="{
                   'opacity-40 cursor-not-allowed bg-slate-950/40 border-slate-900/80': !isAssetKdp(),
                   'cursor-pointer': isAssetKdp(),
                   'bg-emerald-950/30 border-emerald-500 ring-1 ring-emerald-500/40 shadow-lg shadow-emerald-950/50': isAssetKdp() && formData.jenis_reklas === 'KDP_TO_DEFINITIF',
                   'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950': isAssetKdp() && formData.jenis_reklas !== 'KDP_TO_DEFINITIF'
               }">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm shrink-0"
                          :class="{ 'opacity-50 text-slate-500 bg-slate-900 border-slate-800': !isAssetKdp() }">
                        🏗️
                    </span>
                    <div>
                        <span class="text-xs font-bold transition-colors block"
                              :class="{ 'text-slate-500': !isAssetKdp(), 'text-white group-hover:text-emerald-300': isAssetKdp() }">
                            KDP Selesai ➔ Definitif
                        </span>
                        <template x-if="!isAssetKdp()">
                            <span class="text-[9.5px] font-bold text-slate-500 bg-slate-900 border border-slate-800 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                                Khusus Aset KIB F (KDP)
                            </span>
                        </template>
                        <template x-if="isAssetKdp()">
                            <span class="text-[9.5px] font-bold text-emerald-400 bg-emerald-500/20 border border-emerald-500/30 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                                Fisik 100% Selesai
                            </span>
                        </template>
                    </div>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="KDP_TO_DEFINITIF"
                       :disabled="!selectedAstap || !isAssetKdp()"
                       @change="onJenisReklasChange()" class="text-emerald-600 focus:ring-0 mt-1"
                       :class="{ 'cursor-not-allowed text-slate-700': (!selectedAstap || !isAssetKdp()), 'cursor-pointer': (selectedAstap && isAssetKdp()) }">
            </div>
            <p class="text-[11px] leading-relaxed" :class="(!selectedAstap || !isAssetKdp()) ? 'text-slate-600' : 'text-slate-400'">
                Pekerjaan konstruksi fisik (KIB F) telah selesai 100% dan siap dimutasikan menjadi aset definitif (Gedung KIB C / Jaringan KIB D).
            </p>
        </label>

        <!-- 3. DINAMIS: OTOMATIS SESUAI STATUS ASET (Ekstrakomptabel VS Kapitalisasi Intrakomptabel) -->
        <!-- Opsi 3A: Jika Aset Ekstrakomptabel -> Muncul Kapitalisasi Intrakomptabel -->
        <template x-if="selectedAstap && (selectedAstap.is_extracomtable || selectedAstap.category === 'EXTRACOM')">
            <label class="group relative flex flex-col justify-start p-4 rounded-2xl border transition-all duration-200 cursor-pointer select-none space-y-2.5"
                   :class="formData.jenis_reklas === 'KAPITALISASI_INTRAKOM' ? 'bg-teal-950/30 border-teal-500 ring-1 ring-teal-500/40 shadow-lg shadow-teal-950/50' : 'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950'">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl bg-teal-500/10 border border-teal-500/20 text-teal-400 flex items-center justify-center text-sm shrink-0">
                            🔺
                        </span>
                        <div>
                            <span class="text-xs font-bold text-white group-hover:text-teal-300 transition-colors block">
                                Kapitalisasi Intrakomptabel
                            </span>
                            <span class="text-[9.5px] font-bold text-teal-400 bg-teal-500/15 border border-teal-500/30 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                                Status: Ekstrakomptabel
                            </span>
                        </div>
                    </div>
                    <input type="radio" x-model="formData.jenis_reklas" value="KAPITALISASI_INTRAKOM" :disabled="!selectedAstap" @change="onJenisReklasChange()" class="text-teal-600 focus:ring-0 cursor-pointer mt-1">
                </div>
                <p class="text-[11px] text-slate-400 leading-relaxed">
                    Menaikkan aset dari daftar ekstrakomptabel ke neraca Aset Tetap Intrakomptabel karena nilai perolehan di atas batas kapitalisasi (> Rp 300.000).
                </p>
            </label>
        </template>

        <!-- Opsi 3B: Jika Aset Intrakomptabel / Belum Dipilih -> Muncul Ekstrakomptabel (≤ Rp 300rb) -->
        <template x-if="!selectedAstap || (!selectedAstap.is_extracomtable && selectedAstap.category !== 'EXTRACOM')">
            <label class="group relative flex flex-col justify-start p-4 rounded-2xl border transition-all duration-200 select-none space-y-2.5"
                   @click="if (isExtracomDisabled()) { $event.preventDefault(); window.showSimatToast ? window.showSimatToast('⚠️ Opsi Ekstrakomtable tidak berlaku untuk kelompok aset ini (Wajib Intrakomtable sesuai SAP)!', 'warning') : alert('Tidak berlaku untuk kelompok aset ini (Wajib Intrakomtable)!'); }"
                   :class="{
                       'opacity-40 cursor-not-allowed bg-slate-950/40 border-slate-900/80': isExtracomDisabled(),
                       'cursor-pointer bg-rose-950/30 border-rose-500 ring-1 ring-rose-500/40 shadow-lg shadow-rose-950/50': !isExtracomDisabled() && formData.jenis_reklas === 'EKSTRAKOMPTABEL',
                       'cursor-pointer bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950': !isExtracomDisabled() && formData.jenis_reklas !== 'EKSTRAKOMPTABEL'
                   }">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 border"
                              :class="isExtracomDisabled() ? 'opacity-50 text-slate-500 bg-slate-900 border-slate-800' : 'bg-rose-500/10 border-rose-500/20 text-rose-400'">
                            🔻
                        </span>
                        <div>
                            <span class="text-xs font-bold transition-colors block"
                                  :class="isExtracomDisabled() ? 'text-slate-500' : 'text-white group-hover:text-rose-300'">
                                Ekstrakomptabel (≤ Rp 300rb)
                            </span>
                            <span x-show="isExtracomDisabled()" class="text-[9.5px] font-bold text-rose-300 bg-rose-500/20 border border-rose-500/30 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                                Terkunci (SAP)
                            </span>
                            <span x-show="!isExtracomDisabled()" class="text-[9.5px] font-bold text-rose-400 bg-rose-500/15 border border-rose-500/30 px-1.5 py-0.5 rounded mt-0.5 inline-block">
                                Status: Intrakomptabel
                            </span>
                        </div>
                    </div>
                    <input type="radio" x-model="formData.jenis_reklas" value="EKSTRAKOMPTABEL"
                           :disabled="!selectedAstap || isExtracomDisabled()"
                           @change="onJenisReklasChange()" class="text-rose-600 focus:ring-0 mt-1"
                           :class="(!selectedAstap || isExtracomDisabled()) ? 'cursor-not-allowed text-slate-700 opacity-50' : 'cursor-pointer'">
                </div>
                <p class="text-[11px] leading-relaxed"
                   :class="isExtracomDisabled() ? 'text-slate-600' : 'text-slate-400'"
                   x-text="isExtracomDisabled() ? 'Tidak berlaku untuk kelompok aset ini (Wajib Intrakomtable sesuai Standar Akuntansi Pemerintahan).' : 'Nilai perolehan satuan barang di bawah batas kapitalisasi (≤ Rp 300.000). Dikeluarkan dari neraca aset tetap ke ekstrakomptabel.'"></p>
            </label>
        </template>

        <!-- 4. Bantuan Hibah Keluar -->
        <label class="group relative flex flex-col justify-start p-4 rounded-2xl border transition-all duration-200 select-none space-y-2.5"
               @click="if (isCurrentAstapKdp()) { $event.preventDefault(); window.showSimatToast ? window.showSimatToast('⚠️ Aset KDP (belum selesai) tidak dapat dihibahkan keluar!', 'warning') : alert('Terkunci untuk aset KDP!'); }"
               :class="{
                   'opacity-40 cursor-not-allowed bg-slate-950/40 border-slate-900/80': isCurrentAstapKdp(),
                   'cursor-pointer bg-purple-950/30 border-purple-500 ring-1 ring-purple-500/40 shadow-lg shadow-purple-950/50': !isCurrentAstapKdp() && formData.jenis_reklas === 'HIBAH_KELUAR',
                   'cursor-pointer bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950': !isCurrentAstapKdp() && formData.jenis_reklas !== 'HIBAH_KELUAR'
               }">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 border"
                          :class="isCurrentAstapKdp() ? 'opacity-50 text-slate-500 bg-slate-900 border-slate-800' : 'bg-purple-500/10 border-purple-500/20 text-purple-400'">
                        📤
                    </span>
                    <div>
                        <span class="text-xs font-bold transition-colors block"
                              :class="isCurrentAstapKdp() ? 'text-slate-500' : 'text-white group-hover:text-purple-300'">
                            Hibah Keluar (Dihibahkan)
                        </span>
                        <span x-show="isCurrentAstapKdp()" class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 mt-0.5 inline-block">
                            Terkunci (KDP)
                        </span>
                        <span x-show="!isCurrentAstapKdp()" class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-purple-500/20 text-purple-300 border border-purple-500/30 mt-0.5 inline-block">
                            BAST Hibah
                        </span>
                    </div>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="HIBAH_KELUAR" :disabled="!selectedAstap || isCurrentAstapKdp()" @change="onJenisReklasChange()" class="text-purple-600 focus:ring-0 mt-1" :class="(!selectedAstap || isCurrentAstapKdp()) ? 'cursor-not-allowed opacity-50 text-slate-700' : 'cursor-pointer'">
            </div>
            <p class="text-[11px] leading-relaxed"
               :class="isCurrentAstapKdp() ? 'text-slate-600' : 'text-slate-400'"
               x-text="isCurrentAstapKdp() ? 'Terkunci: Aset KDP (belum selesai) tidak dapat dihibahkan.' : 'Penyerahan aset milik RSUD secara cuma-cuma kepada yayasan, pemerintah desa, atau pihak ketiga berdasarkan naskah hibah resmi.'"></p>
        </label>

        <!-- 5. Mutasi Eksternal (Antar-OPD) -->
        <label class="group relative flex flex-col justify-start p-4 rounded-2xl border transition-all duration-200 select-none space-y-2.5"
               @click="if (isCurrentAstapKdp()) { $event.preventDefault(); window.showSimatToast ? window.showSimatToast('⚠️ Aset KDP (belum selesai) tidak dapat dimutasi ke luar OPD!', 'warning') : alert('Terkunci untuk aset KDP!'); }"
               :class="{
                   'opacity-40 cursor-not-allowed bg-slate-950/40 border-slate-900/80': isCurrentAstapKdp(),
                   'cursor-pointer bg-cyan-950/30 border-cyan-500 ring-1 ring-cyan-500/40 shadow-lg shadow-cyan-950/50': !isCurrentAstapKdp() && formData.jenis_reklas === 'MUTASI_EKSTERNAL',
                   'cursor-pointer bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950': !isCurrentAstapKdp() && formData.jenis_reklas !== 'MUTASI_EKSTERNAL'
               }">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl flex items-center justify-center text-sm shrink-0 border"
                          :class="isCurrentAstapKdp() ? 'opacity-50 text-slate-500 bg-slate-900 border-slate-800' : 'bg-cyan-500/10 border-cyan-500/20 text-cyan-400'">
                        🏛️
                    </span>
                    <div>
                        <span class="text-xs font-bold transition-colors block"
                              :class="isCurrentAstapKdp() ? 'text-slate-500' : 'text-white group-hover:text-cyan-300'">
                            Mutasi Antar-OPD
                        </span>
                        <span x-show="isCurrentAstapKdp()" class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-rose-500/20 text-rose-300 border border-rose-500/30 mt-0.5 inline-block">
                            Terkunci (KDP)
                        </span>
                        <span x-show="!isCurrentAstapKdp()" class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-cyan-500/20 text-cyan-300 border border-cyan-500/30 mt-0.5 inline-block">
                            BAST Antar-SKPD
                        </span>
                    </div>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="MUTASI_EKSTERNAL" :disabled="!selectedAstap || isCurrentAstapKdp()" @change="onJenisReklasChange()" class="text-cyan-600 focus:ring-0 mt-1" :class="(!selectedAstap || isCurrentAstapKdp()) ? 'cursor-not-allowed opacity-50 text-slate-700' : 'cursor-pointer'">
            </div>
            <p class="text-[11px] leading-relaxed"
               :class="isCurrentAstapKdp() ? 'text-slate-600' : 'text-slate-400'"
               x-text="isCurrentAstapKdp() ? 'Terkunci: Aset KDP (belum selesai) tidak dapat dimutasi keluar OPD.' : 'Pemindahan penguasaan aset ke SKPD/OPD lain di lingkungan Pemkab Bondowoso berdasarkan BAST resmi.'"></p>
        </label>

        <!-- 6. Koreksi Nilai Realisasi / BPK / Manset -->
        <label class="group relative flex flex-col justify-start p-4 rounded-2xl border transition-all duration-200 cursor-pointer select-none space-y-2.5"
               :class="formData.jenis_reklas === 'KOREKSI_LAIN' ? 'bg-amber-950/30 border-amber-500 ring-1 ring-amber-500/40 shadow-lg shadow-amber-950/50' : 'bg-slate-950/70 border-slate-800 hover:border-slate-700 hover:bg-slate-950'">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center text-sm shrink-0">
                        ⚖️
                    </span>
                    <div>
                        <span class="text-xs font-bold text-white group-hover:text-amber-300 transition-colors block">
                            Koreksi Nilai Realisasi
                        </span>
                        <span class="text-[9.5px] font-bold px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 mt-0.5 inline-block">
                            Audit BPK / Manset
                        </span>
                    </div>
                </div>
                <input type="radio" x-model="formData.jenis_reklas" value="KOREKSI_LAIN" :disabled="!selectedAstap" @change="onJenisReklasChange()" class="text-amber-500 focus:ring-0 cursor-pointer mt-1">
            </div>
            <p class="text-[11px] text-slate-400 leading-relaxed">
                Penyesuaian nilai nominal perolehan belanja modal aset bertambah (+) atau berkurang (-) akibat temuan pemeriksaan BPK-RI, koreksi audit internal, atau administrasi aset manset.
            </p>
        </label>
    </div>
</div>
