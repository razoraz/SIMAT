<!-- ========================================================================= -->
<!-- LANGKAH 1: DOKUMEN FAKTUR/NOTA & TOKO SUPPLIER & UPLOAD BERKAS             -->
<!-- ========================================================================= -->
<div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div class="border-b border-slate-800 pb-4">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 text-xs font-bold mb-2">
            <span>Langkah 1 dari 3</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span>🧾 Dokumen Pembelian Faktur &amp; Toko Rekanan Perbekalan</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">
            Masukkan data faktur/nota pembelian toko rekanan dan lampirkan berkas dokumen digital yang diwajibkan untuk keperluan audit BPK &amp; penatausahaan BMD.
        </p>
    </div>

    <!-- Periode Anggaran & Triwulan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-950/70 border border-slate-800">
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Tahun Anggaran Pembukuan <span class="text-rose-400">*</span>
            </label>
            <input type="number" x-model.number="formData.tahun_perolehan" min="1990" max="2100" required
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-mono">
        </div>
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Triwulan Pembukuan <span class="text-rose-400">*</span>
            </label>
            <select x-model="formData.triwulan" required
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
                <option value="TW I">Triwulan I (Januari - Maret)</option>
                <option value="TW II">Triwulan II (April - Juni)</option>
                <option value="TW III">Triwulan III (Juli - September)</option>
                <option value="TW IV">Triwulan IV (Oktober - Desember)</option>
            </select>
        </div>
    </div>

    <!-- Bagian Faktur & Toko Penyedia -->
    <div class="p-5 rounded-2xl bg-slate-950/70 border border-indigo-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🏷️ Informasi Toko / Rekanan &amp; Faktur Pembelian</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-indigo-300 bg-indigo-500/10 px-2.5 py-0.5 rounded-full border border-indigo-500/20">
                📦 Belanja Barang (Akun 5.1.02)
            </span>
        </div>

        <!-- Toko / Supplier Rekanan (Smart Combobox) -->
        <div class="relative space-y-1.5" @click.away="isTokoDropdownOpen = false">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-200">
                    Nama Toko / Supplier / Rekanan <span class="text-rose-400">*</span>
                </label>
                <template x-if="masterTokoList && masterTokoList.length > 0">
                    <span class="text-[10px] text-indigo-400 font-mono font-normal flex items-center gap-1 bg-indigo-500/10 px-2 py-0.5 rounded-md border border-indigo-500/20">
                        <span>⚡</span>
                        <span>Riwayat Toko Tersimpan</span>
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
                    required
                    autocomplete="off"
                    placeholder="Ketik atau pilih nama toko rekanan (contoh: CV. Sahabat Medika, Toko ATK Berkah...)"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-3 pl-10 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">
                
                <!-- Ikon Toko -->
                <svg class="w-4 h-4 text-indigo-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0h1m-1-4h.01M9 16h.01M9 12h.01M9 8h.01M15 16h.01M15 12h.01M15 8h.01"/>
                </svg>

                <!-- Tombol Kosongkan Input -->
                <template x-if="formData.rekening_penyedia">
                    <button type="button" 
                        @click="formData.rekening_penyedia = ''; isTokoDropdownOpen = true" 
                        title="Kosongkan nama toko"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center text-xs transition-colors">
                        ✕
                    </button>
                </template>
            </div>

            <!-- Floating Dropdown Saran / Filter Toko (Muncul saat fokus/diketik) -->
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

        <!-- Nomor & Tanggal Faktur / Nota -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Faktur / Nota Toko <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.rekening_nomor_faktur" required
                    placeholder="Contoh: INV/2026/04/0012 atau NOTA-582"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal Faktur / Pembelian <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-datepicker x-model="formData.rekening_tanggal_faktur" required
                    placeholder="dd/mm/yyyy"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none">
            </div>
        </div>

        <!-- ================================================================= -->
        <!-- UNGGAH BERKAS DOKUMEN DIGITAL (AUDIT-READY)                       -->
        <!-- ================================================================= -->
        <div class="pt-2 border-t border-slate-800/80">
            <span class="text-xs font-extrabold text-cyan-400 uppercase tracking-wider block mb-3 flex items-center gap-1.5">
                <span>📎 Berkas Dokumen Pendukung Wajib (PDF / Gambar)</span>
            </span>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                
                <!-- 1. Dropzone Nota / Faktur Toko Rekanan (WAJIB) -->
                <div class="p-4 rounded-2xl bg-slate-900/90 border border-indigo-500/30 space-y-2.5 relative">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-white flex items-center gap-1.5">
                            <span>📄 Nota / Faktur Toko</span>
                            <span class="text-rose-400">*</span>
                        </label>
                        <span class="text-[9px] px-2 py-0.5 rounded bg-rose-500/10 text-rose-300 border border-rose-500/20 font-bold">
                            Wajib Diunggah
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
                                class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 transition-colors text-xs font-bold shrink-0">
                                ✕ Ganti
                            </button>
                        </div>
                    </template>
                </div>

                <!-- 2. Dropzone Bukti Penerimaan / Kuitansi Gudang (PENDUKUNG) -->
                <div class="p-4 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-2.5 relative">
                    <div class="flex items-center justify-between">
                        <label class="text-xs font-bold text-white flex items-center gap-1.5">
                            <span>📦 Bukti Terima / Kuitansi Gudang</span>
                        </label>
                        <span class="text-[9px] px-2 py-0.5 rounded bg-slate-800 text-slate-400 border border-slate-700 font-medium">
                            Dokumen Pendukung
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
                            <span class="text-[10px] text-slate-500 mt-0.5 block">Format PDF, JPG, PNG (Opsional / Max 5MB)</span>
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
                                class="p-1.5 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 hover:text-rose-300 transition-colors text-xs font-bold shrink-0">
                                ✕ Ganti
                            </button>
                        </div>
                    </template>
                </div>

            </div>
        </div>

        <!-- Catatan / Keterangan Pembelian -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Catatan / Keterangan Pembelian Faktur
            </label>
            <textarea x-model="formData.rekening_keterangan" rows="2"
                placeholder="Contoh: Pengadaan alat medis operasional ruangan melalui rekening operasional BLUD/Barang & Jasa..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
        </div>
    </div>

</div>
