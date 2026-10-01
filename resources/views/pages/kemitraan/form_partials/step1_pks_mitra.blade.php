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

        <!-- Bentuk Skema Kemitraan Sesuai Permendagri 108 Akun 1.5.2 (Ditaruh Paling Atas Sendiri) -->
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
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Dokumen Perjanjian Kerja Sama (PKS / MoU) <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.nomor_pks" required
                    placeholder="Contoh: 000.2.3.2/PKS-KSO/430.10.7/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-cyan-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label class="block text-xs font-bold text-slate-200">
                        Tanggal Penandatanganan PKS <span class="text-rose-400">*</span>
                    </label>
                    <span class="text-[10px] font-mono text-cyan-400/80 bg-cyan-950/40 px-2 py-0.5 rounded border border-cyan-800/40">Maks: Hari Ini</span>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[11px] font-semibold text-slate-400">
                            Tanggal Mulai Berlaku Kerjasama
                        </label>
                        <div class="flex items-center gap-1.5 text-[9.5px] font-mono text-cyan-400/90">
                            <span x-show="formData.tanggal_pks" class="bg-slate-800/70 px-1.5 py-0.5 rounded border border-slate-700">Min (PKS): <span x-text="formatTanggalIndo(formData.tanggal_pks)"></span></span>
                            <span class="bg-cyan-950/40 px-1.5 py-0.5 rounded border border-cyan-800/40">Maks: Hari Ini</span>
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
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-[11px] font-semibold text-slate-400">
                            Tanggal Berakhir Kerjasama (Konsesi Berakhir)
                        </label>
                        <span x-show="formData.tanggal_mulai || formData.tanggal_pks" class="text-[9.5px] font-mono text-cyan-400/90">
                            Min: <span x-text="formatTanggalIndo(formData.tanggal_mulai || formData.tanggal_pks)"></span>
                        </span>
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

        <!-- Unggah Berkas Dokumen BAST / PKS Kerja Sama (Diletakkan di antara Tahun/Triwulan dan Ruang Lingkup) -->
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3 shadow-lg">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span>Unggah Berkas Dokumen BAST / PKS Kerja Sama</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-cyan-950/60 text-cyan-300 border border-cyan-800/40">
                        Opsional
                    </span>
                </div>
                <span class="text-[10px] text-slate-400 font-mono">Format: PDF, JPG, PNG, DOC/DOCX (Maks. 10 MB)</span>
            </div>

            <!-- Drag & Drop / Click Upload Box -->
            <div class="p-4 rounded-2xl bg-slate-950/60 border-2 border-dashed border-slate-700 hover:border-cyan-400/60 transition-all text-center relative group">
                <input type="file" @change="handleFileSelect" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                
                <div class="space-y-1.5 pointer-events-none">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center text-lg shadow-inner">
                        📁
                    </div>
                    <p class="text-xs font-bold text-slate-300 group-hover:text-cyan-300 transition-colors">
                        <span x-show="!selectedFile">Klik atau seret berkas BAST / PKS Kerja Sama ke sini</span>
                        <span x-show="selectedFile" class="text-cyan-400 font-mono" x-text="selectedFile ? ('📄 ' + selectedFile.name + ' (' + (selectedFile.size / 1024 / 1024).toFixed(2) + ' MB)') : ''"></span>
                    </p>
                    <p class="text-[10.5px] text-slate-500">Maksimal 10 MB (Format: PDF, Gambar Scan, Dokumen Word)</p>
                </div>
            </div>

            <!-- Info Berkas yang Sudah Tersimpan (Mode Edit) -->
            <template x-if="isEditMode && formData.dokumen_path">
                <div class="p-2.5 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-between text-xs">
                    <span class="text-slate-300 truncate">📄 File Tersimpan: <strong class="text-cyan-300 font-mono" x-text="formData.dokumen_path.split('/').pop()"></strong></span>
                    <a :href="'/storage/' + formData.dokumen_path" target="_blank" class="text-cyan-400 hover:underline font-bold text-[11px] shrink-0 ml-2 flex items-center gap-1">
                        <span>Lihat Berkas BAST</span>
                        <span>↗</span>
                    </a>
                </div>
            </template>

            <!-- Aksi Jika File Terpilih -->
            <template x-if="selectedFile">
                <div class="flex items-center justify-between px-1 text-[11px]">
                    <span class="text-emerald-400 font-semibold flex items-center gap-1">
                        <span>✓</span>
                        <span>Berkas siap disimpan ke sistem bersama aset kemitraan</span>
                    </span>
                    <button type="button" @click="selectedFile = null" class="text-rose-400 hover:underline font-medium cursor-pointer">
                        ✕ Batal / Ganti Berkas
                    </button>
                </div>
            </template>
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

    </div>

</div>
