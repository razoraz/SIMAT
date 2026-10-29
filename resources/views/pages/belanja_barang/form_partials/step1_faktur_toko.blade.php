<!-- ========================================================================= -->
<!-- LANGKAH 1: DOKUMEN FAKTUR/NOTA & TOKO REKANAN & BERKAS UPLOAD              -->
<!-- 100% PERSIS POLA MUTASI EKSTERNAL & KEMITRAAN                             -->
<!-- ========================================================================= -->
<div x-show="currentStep === 1 || step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <!-- Header Step 1 Subtitle -->
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-400/10 text-indigo-300 border border-indigo-400/20 text-xs font-bold mb-2">
            <span>📦 LANGKAH 1 DARI 3: DOKUMEN FAKTUR &amp; REKANAN TOKO</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-indigo-400/10 text-indigo-400 text-sm">🧾</span>
            <span>Langkah 1: Dokumen Pembelian Faktur &amp; Toko Rekanan Perbekalan</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Lengkapi nomor faktur/nota pembelian, tanggal faktur, toko rekanan perbekalan, pejabat penanggung jawab RSUD, serta lampiran berkas dokumen nota digital.
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

    <!-- Alert Mode Testing Aktif -->
    <div class="p-3.5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-between gap-3 text-xs">
        <div class="flex items-center gap-2.5">
            <span class="text-base">🧪</span>
            <div>
                <span class="font-bold text-amber-300 block">Mode Testing Aktif: Isian Form Tidak Diwajibkan</span>
                <span class="text-[11px] text-slate-400 block mt-0.5">Seluruh input dan upload berkas dapat dilewati atau dikosongkan. Nilai default akan terisi otomatis saat disimpan.</span>
            </div>
        </div>
        <span class="px-2.5 py-1 rounded-lg bg-amber-500/20 text-amber-300 border border-amber-500/30 font-bold text-[10px] uppercase shrink-0">
            Testing Mode
        </span>
    </div>

    <!-- ========================================================================= -->
    <!-- BAGIAN 1: IDENTITAS TOKO / REKANAN PENYEDIA & PEJABAT RSUD                -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-indigo-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🏪 Toko / Rekanan Penyedia Perbekalan</span>
            </span>
            <span class="text-[10px] font-bold text-indigo-300 bg-indigo-400/10 px-2 py-0.5 rounded-lg border border-indigo-400/20">
                Akun 5.1.02 · Ekstrakomptabel
            </span>
        </div>

        <!-- Toko / Supplier Rekanan (Smart Combobox) -->
        <div class="relative space-y-1.5" @click.away="isTokoDropdownOpen = false">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-200">
                    Nama Toko / Supplier / Rekanan
                </label>
                <template x-if="masterTokoList && masterTokoList.length > 0">
                    <span class="text-[10px] text-indigo-400 font-mono font-normal flex items-center gap-1 bg-indigo-500/10 px-2 py-0.5 rounded-md border border-indigo-500/20">
                        <span>⚡</span>
                        <span x-text="masterTokoList.length + ' Toko Tersimpan'"></span>
                    </span>
                </template>
            </div>

            <!-- Input Box dengan Ikon dan Clear Button -->
            <div class="relative">
                <input type="text" 
                    x-model="formData.rekening_penyedia" 
                    @focus="isTokoDropdownOpen = true"
                    @input="isTokoDropdownOpen = true"
                    @keydown.escape="isTokoDropdownOpen = false"
                    autocomplete="off"
                    placeholder="Ketik atau pilih nama toko rekanan (contoh: CV. Sahabat Medika, Toko ATK Berkah...)"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-3 pl-10 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">
                
                <!-- Ikon Toko -->
                <svg class="w-4 h-4 text-indigo-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/>
                </svg>

                <!-- Tombol Kosongkan Input -->
                <button type="button" 
                    x-show="formData.rekening_penyedia" 
                    @click="formData.rekening_penyedia = ''; isTokoDropdownOpen = true" 
                    title="Kosongkan nama toko"
                    class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center text-xs transition-colors cursor-pointer">
                    ✕
                </button>
            </div>

            <!-- Floating Dropdown Saran / Filter Toko -->
            <div x-show="isTokoDropdownOpen" 
                x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                style="max-height: 220px !important; overflow-y: auto !important;"
                class="absolute z-50 mt-1.5 w-full bg-slate-900 border border-indigo-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">
                
                <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-indigo-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                    <span>Pilih Riwayat / Ketik Toko Baru</span>
                    <span class="font-mono text-slate-400" x-text="filteredTokoList.length + ' saran'"></span>
                </div>

                <template x-for="(toko, tIdx) in filteredTokoList" :key="tIdx">
                    <div @click="selectToko(toko)"
                        class="px-4 py-2.5 hover:bg-indigo-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                        :class="formData.rekening_penyedia === toko ? 'bg-indigo-500/20 text-indigo-200' : 'text-slate-200'">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="text-xs text-indigo-400/80">🏪</span>
                            <span class="text-xs font-bold group-hover:text-indigo-300 truncate" x-text="toko"></span>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/25 font-bold shrink-0 group-hover:bg-indigo-500/25">
                            Pilih ↵
                        </span>
                    </div>
                </template>

                <template x-if="filteredTokoList.length === 0">
                    <div class="px-4 py-3 text-xs text-slate-400 text-center bg-slate-950/50">
                        <span>Toko baru akan didaftarkan otomatis: </span>
                        <strong class="text-indigo-300 font-bold" x-text="formData.rekening_penyedia"></strong>
                    </div>
                </template>
            </div>

            <!-- Shortcut Chip Riwayat Toko Populer -->
            <template x-if="masterTokoList && masterTokoList.length > 0">
                <div class="flex flex-wrap items-center gap-1.5 pt-1">
                    <span class="text-[10px] text-slate-400 font-semibold mr-1">Rekomendasi Cepat:</span>
                    <template x-for="(sampleToko, sIdx) in masterTokoList.slice(0, 4)" :key="sIdx">
                        <button type="button" 
                            @click="selectToko(sampleToko)"
                            class="px-2.5 py-0.5 rounded-lg bg-slate-800/80 hover:bg-indigo-500/20 text-slate-300 hover:text-indigo-300 text-[10px] border border-slate-700/60 hover:border-indigo-500/40 transition-all cursor-pointer font-medium">
                            <span x-text="sampleToko"></span>
                        </button>
                    </template>
                </div>
            </template>
        </div>

        <!-- Pejabat Pembuat Komitmen (PPK) / Penanggung Jawab RSUD -->
        <div class="relative space-y-1.5" @click.away="isPejabatDropdownOpen = false">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-200">
                    Pejabat Pembuat Komitmen (PPK) / Penanggung Jawab RSUD
                </label>
                <template x-if="masterPejabatList && masterPejabatList.length > 0">
                    <span class="text-[10px] text-slate-400 font-mono" x-text="masterPejabatList.length + ' Pilihan Pejabat'"></span>
                </template>
            </div>

            <div class="relative">
                <input type="text" 
                    x-model="formData.ppk_nama" 
                    @focus="isPejabatDropdownOpen = true"
                    @input="isPejabatDropdownOpen = true"
                    @keydown.escape="isPejabatDropdownOpen = false"
                    autocomplete="off"
                    placeholder="Ketik atau pilih nama PPK / Kepala Instalasi Perbekalan..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold transition-all shadow-inner">

                <!-- Floating Dropdown Pejabat -->
                <div x-show="isPejabatDropdownOpen && filteredPejabatList.length > 0"
                    x-cloak
                    x-transition:enter="transition ease-out duration-100"
                    x-transition:enter-start="opacity-0 translate-y-1"
                    x-transition:enter-end="opacity-100 translate-y-0"
                    class="absolute z-50 mt-1.5 w-full bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl max-h-48 overflow-y-auto">
                    <template x-for="(pj, pIdx) in filteredPejabatList" :key="pIdx">
                        <div @click="selectPejabat(pj)"
                            class="px-4 py-2 hover:bg-slate-800 cursor-pointer text-xs text-slate-300 hover:text-white flex items-center justify-between">
                            <span x-text="pj"></span>
                            <span class="text-[9px] text-indigo-400 font-bold">Pilih ↵</span>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- BAGIAN 2: LEGALITAS FAKTUR / NOTA PEMBELIAN & ANGGARAN                     -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-indigo-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📜 Legalitas Faktur &amp; Periode Anggaran</span>
            </span>
            <span class="text-[10px] font-bold text-indigo-300 bg-indigo-400/10 px-2 py-0.5 rounded-lg border border-indigo-400/20">
                Bukti Transaksi Toko
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Nomor Faktur -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Faktur / Nota Toko
                </label>
                <input type="text" x-model="formData.rekening_nomor_faktur"
                    placeholder="Contoh: INV/2026/04/0012 atau NOTA-582"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none shadow-inner">
            </div>

            <!-- Tanggal Faktur -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal Faktur / Pembelian <span class="text-rose-400">*</span>
                </label>
                <input type="text" 
                    x-datepicker="{ maxDate: 'today' }" 
                    x-model="formData.rekening_tanggal_faktur" 
                    @input="onTanggalFakturChange($event.target.value)"
                    @change="onTanggalFakturChange($event.target.value)"
                    placeholder="dd/mm/yyyy"
                    required
                    :class="isTanggalFakturInvalid() ? 'border-rose-500 focus:border-rose-400 bg-rose-950/20' : 'border-slate-700 focus:border-indigo-400 bg-slate-900'"
                    class="w-full border rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none transition-colors shadow-inner">
                
                <!-- Peringatan Visual Jika Tanggal Faktur Lebih dari Hari Ini -->
                <template x-if="isTanggalFakturInvalid()">
                    <div class="mt-1.5 flex items-center gap-1.5 text-[10.5px] text-rose-400 font-semibold animate-pulse">
                        <span>⚠️</span>
                        <span>Tanggal Faktur/Pembelian tidak boleh melebihi tanggal hari ini!</span>
                    </div>
                </template>
            </div>

            <!-- Tahun Anggaran -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tahun Anggaran Pembukuan
                </label>
                <input type="number" x-model.number="formData.tahun_perolehan" min="1990" max="2100"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-mono shadow-inner">
            </div>

            <!-- Triwulan -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Triwulan Pembukuan
                </label>
                <select x-model="formData.triwulan"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none shadow-inner">
                    <option value="TW I">Triwulan I (Jan - Mar)</option>
                    <option value="TW II">Triwulan II (Apr - Jun)</option>
                    <option value="TW III">Triwulan III (Jul - Sep)</option>
                    <option value="TW IV">Triwulan IV (Okt - Des)</option>
                </select>
            </div>
        </div>

        <!-- Catatan / Keterangan Pembelian -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Keterangan / Keperluan Operasional Belanja
            </label>
            <textarea x-model="formData.rekening_keterangan" rows="2"
                placeholder="Contoh: Pengadaan perbekalan alat operasional ruangan rawat inap dan IGD melalui anggaran perbekalan BLUD..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl p-3 text-xs text-white focus:outline-none shadow-inner"></textarea>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- BAGIAN 3: UNGGAH BERKAS DOKUMEN DIGITAL (FAKTUR & KUITANSI GUDANG)        -->
    <!-- ========================================================================= -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-indigo-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📎 Lampiran Berkas Digital (Faktur &amp; Bukti Terima)</span>
            </span>
            <span class="text-[10px] font-bold text-slate-400 bg-slate-900 px-2.5 py-0.5 rounded-lg border border-slate-800">
                PDF / JPG / PNG Maks 5MB
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            
            <!-- 1. Dropzone Nota / Faktur Toko Rekanan -->
            <div class="p-4 rounded-2xl bg-slate-900/90 border border-indigo-500/30 space-y-2.5 relative">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-white flex items-center gap-1.5">
                        <span>📄 Nota / Faktur Pembelian Toko</span>
                    </label>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">
                        Faktur Asli
                    </span>
                </div>

                <!-- Hidden File Input -->
                <input type="file" id="dokumen_faktur_input" accept=".pdf,.jpg,.jpeg,.png"
                    @change="handleFileChange($event, 'faktur')" class="hidden">

                <!-- Dropzone Box saat Kosong -->
                <template x-if="!fileFaktur">
                    <div @click="document.getElementById('dokumen_faktur_input').click()"
                        class="border-2 border-dashed border-slate-700 hover:border-indigo-400 rounded-xl p-4 text-center cursor-pointer hover:bg-indigo-500/5 transition-all group">
                        <svg class="w-8 h-8 text-indigo-400/70 group-hover:text-indigo-400 mx-auto mb-1.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span class="text-xs font-bold text-slate-300 block group-hover:text-white">Klik untuk Unggah Nota / Faktur</span>
                        <span class="text-[10px] text-slate-500 mt-0.5 block">Format PDF, JPG, PNG (Maksimal 5MB)</span>
                    </div>
                </template>

                <!-- Preview saat File Terpilih -->
                <template x-if="fileFaktur">
                    <div class="p-3 rounded-xl bg-slate-950 border border-indigo-500/40 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0"
                                :class="fileFaktur.name.endsWith('.pdf') ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30'">
                                <span x-text="fileFaktur.name.endsWith('.pdf') ? 'PDF' : 'IMG'"></span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-white block truncate" x-text="fileFaktur.name"></span>
                                <span class="text-[10px] text-slate-400 font-mono block" x-text="formatFileSize(fileFaktur.size)"></span>
                            </div>
                        </div>
                        <button type="button" @click="removeFile('faktur')"
                            title="Hapus file ini"
                            class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 transition-colors text-xs font-bold shrink-0 cursor-pointer">
                            ✕ Ganti
                        </button>
                    </div>
                </template>
            </div>

            <!-- 2. Dropzone Bukti Penerimaan / Kuitansi Gudang -->
            <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2.5 relative">
                <div class="flex items-center justify-between">
                    <label class="text-xs font-bold text-white flex items-center gap-1.5">
                        <span>📦 Bukti Terima / Kuitansi Gudang</span>
                    </label>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700 font-medium">
                        Kuitansi / BAST Terima
                    </span>
                </div>

                <!-- Hidden File Input -->
                <input type="file" id="dokumen_kuitansi_input" accept=".pdf,.jpg,.jpeg,.png"
                    @change="handleFileChange($event, 'kuitansi')" class="hidden">

                <!-- Dropzone Box saat Kosong -->
                <template x-if="!fileKuitansi">
                    <div @click="document.getElementById('dokumen_kuitansi_input').click()"
                        class="border-2 border-dashed border-slate-700 hover:border-slate-500 rounded-xl p-4 text-center cursor-pointer hover:bg-slate-800/40 transition-all group">
                        <svg class="w-8 h-8 text-slate-500 group-hover:text-slate-300 mx-auto mb-1.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                        </svg>
                        <span class="text-xs font-bold text-slate-300 block group-hover:text-white">Klik untuk Unggah Kuitansi / Bukti Terima</span>
                        <span class="text-[10px] text-slate-500 mt-0.5 block">Format PDF, JPG, PNG (Maksimal 5MB)</span>
                    </div>
                </template>

                <!-- Preview saat File Terpilih -->
                <template x-if="fileKuitansi">
                    <div class="p-3 rounded-xl bg-slate-950 border border-slate-700 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs shrink-0"
                                :class="fileKuitansi.name.endsWith('.pdf') ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-cyan-500/20 text-cyan-400 border border-cyan-500/30'">
                                <span x-text="fileKuitansi.name.endsWith('.pdf') ? 'PDF' : 'IMG'"></span>
                            </div>
                            <div class="min-w-0">
                                <span class="text-xs font-bold text-white block truncate" x-text="fileKuitansi.name"></span>
                                <span class="text-[10px] text-slate-400 font-mono block" x-text="formatFileSize(fileKuitansi.size)"></span>
                            </div>
                        </div>
                        <button type="button" @click="removeFile('kuitansi')"
                            title="Hapus file ini"
                            class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 transition-colors text-xs font-bold shrink-0 cursor-pointer">
                            ✕ Ganti
                        </button>
                    </div>
                </template>
            </div>

        </div>
    </div>

</div>
