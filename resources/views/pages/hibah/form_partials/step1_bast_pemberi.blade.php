{{-- =========================================================================
     LANGKAH 1: DOKUMEN BAST HIBAH & PIHAK PEMBERI (IDENTITAS SUMBER HIBAH)
     Design: Dark Luxury Amber — Bespoke SIMAT-RK
     ========================================================================= --}}
<div x-show="currentStep === 1"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0 translate-y-2"
     x-transition:enter-end="opacity-100 translate-y-0"
     class="space-y-6">

    {{-- ─── Header Langkah 1 ──────────────────────────────────────────────── --}}
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-amber-400/10 text-amber-300 border border-amber-400/20 text-xs font-bold mb-2">
            <span>🎁 LANGKAH 1 DARI 3: LEGALITAS PENYERAHAN HIBAH</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-amber-400/10 text-amber-400 text-sm">📜</span>
            <span>Langkah 1: Dokumen BAST / NPHD &amp; Identitas Pemberi Hibah</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Masukkan legalitas penyerahan hibah (BAST/NPHD), jenis sumber hibah, identitas instansi pemberi, periode pembukuan, dan berkas dokumen hibah.
        </p>
    </div>


    {{-- ─── CARD 1: Identitas Instansi & Pihak Pemberi Hibah ──────────────── --}}
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/30 space-y-5 shadow-xl relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🎁 Identitas Instansi & Pihak Pemberi Hibah</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded-lg border border-amber-400/20">
                Non-Belanja Modal APBD
            </span>
        </div>

        {{-- ─── Instansi / Lembaga Pemberi Hibah (Combobox) ──────────────── --}}
        <div class="relative space-y-1.5" @click.away="isInstansiDropdownOpen = false">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-200">
                    Instansi / Lembaga / Nama Pemberi Hibah <span class="text-rose-400">*</span>
                </label>
                <template x-if="masterInstansiList && masterInstansiList.length > 0">
                    <span class="text-[10px] text-amber-400 font-mono font-normal flex items-center gap-1 bg-amber-500/10 px-2 py-0.5 rounded-md border border-amber-500/20">
                        <span>⚡</span>
                        <span>Riwayat Tersimpan</span>
                    </span>
                </template>
            </div>

            <div class="relative">
                <input type="text"
                    x-model="formData.hibah_pemberi"
                    @focus="isInstansiDropdownOpen = true"
                    @input="isInstansiDropdownOpen = true; checkAutoFillInstansi()"
                    @change="checkAutoFillInstansi()"
                    @keydown.escape="isInstansiDropdownOpen = false"
                    required
                    autocomplete="off"
                    placeholder="Ketik atau pilih nama instansi (contoh: Kementerian Kesehatan RI, Dinas Kesehatan...)"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-3 pl-10 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">

                <svg class="w-4 h-4 text-amber-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>

                <template x-if="formData.hibah_pemberi">
                    <button type="button"
                        @click="formData.hibah_pemberi = ''; isInstansiDropdownOpen = true"
                        title="Kosongkan"
                        class="absolute right-3 top-1/2 -translate-y-1/2 w-5 h-5 rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 flex items-center justify-center text-xs transition-colors">
                        ✕
                    </button>
                </template>
            </div>

            {{-- Dropdown Saran Instansi (Hanya Tampil Jika Ada Saran yang Sesuai) --}}
            <div x-show="isInstansiDropdownOpen && filteredInstansiList.length > 0"
                x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                style="max-height: 240px !important; overflow-y: auto !important;"
                class="absolute z-50 mt-1.5 w-full bg-slate-900 border border-amber-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">

                <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                    <span>Pilih Riwayat Instansi (Auto-Fill Pimpinan &amp; Alamat)</span>
                    <span class="font-mono text-slate-400" x-text="filteredInstansiList.length + ' saran'"></span>
                </div>

                <template x-for="(inst, iIdx) in filteredInstansiList" :key="iIdx">
                    <div @click="selectInstansi(inst)"
                        class="px-4 py-2.5 hover:bg-amber-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                        :class="formData.hibah_pemberi === inst.nama ? 'bg-amber-500/20 text-amber-200' : 'text-slate-200'">
                        <div class="space-y-0.5 min-w-0 flex-1">
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-amber-400/80">🏛️</span>
                                <span class="text-xs font-bold group-hover:text-amber-300 truncate" x-text="inst.nama"></span>
                            </div>
                            <template x-if="inst.pimpinan || inst.alamat">
                                <div class="text-[10px] text-slate-400 pl-5 flex flex-wrap items-center gap-x-2 gap-y-0.5 truncate">
                                    <template x-if="inst.pimpinan">
                                        <span class="truncate max-w-[240px]" :title="inst.pimpinan" x-text="'👤 ' + inst.pimpinan"></span>
                                    </template>
                                    <template x-if="inst.alamat">
                                        <span class="text-slate-500 truncate max-w-[200px]" :title="inst.alamat" x-text="'📍 ' + inst.alamat"></span>
                                    </template>
                                </div>
                            </template>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/25 font-bold shrink-0 group-hover:bg-amber-500/25">
                            Pilih ↵
                        </span>
                    </div>
                </template>
            </div>

            {{-- Shortcut Instansi Populer --}}
            <div class="pt-1 flex flex-wrap items-center gap-1.5">
                <span class="text-[10px] text-slate-500 font-semibold mr-1">Pilih Cepat:</span>
                <button type="button" @click="selectInstansi('Kementerian Kesehatan Republik Indonesia')"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-500/15 text-slate-300 hover:text-amber-300 text-[10px] border border-slate-700 hover:border-amber-500/30 cursor-pointer transition-colors">
                    Kemenkes RI
                </button>
                <button type="button" @click="selectInstansi('Dinas Kesehatan Provinsi Jawa Timur')"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-500/15 text-slate-300 hover:text-amber-300 text-[10px] border border-slate-700 hover:border-amber-500/30 cursor-pointer transition-colors">
                    Dinkes Prov. Jatim
                </button>
                <button type="button" @click="selectInstansi('Pemerintah Kabupaten Bondowoso')"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-500/15 text-slate-300 hover:text-amber-300 text-[10px] border border-slate-700 hover:border-amber-500/30 cursor-pointer transition-colors">
                    Pemkab Bondowoso
                </button>
                <button type="button" @click="selectInstansi('Donatur Swasta / Yayasan CSR')"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-500/15 text-slate-300 hover:text-amber-300 text-[10px] border border-slate-700 hover:border-amber-500/30 cursor-pointer transition-colors">
                    Donatur / CSR
                </button>
            </div>
        </div>

        {{-- ─── Pimpinan / Kuasa Pemberi & Alamat (2 Kolom) ──────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span>Nama Pimpinan / Kuasa Pemberi</span>
                    </span>
                    <template x-if="formData.hibah_pimpinan">
                        <span class="text-[9.5px] text-emerald-400 font-mono font-semibold">✓ Terisi</span>
                    </template>
                </label>
                <input type="text" x-model="formData.hibah_pimpinan"
                    placeholder="Contoh: dr. H. Ahmad Fauzi, Sp.PD — Kepala Dinas Kesehatan"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                <p class="text-[10px] text-slate-500 mt-1">Nama direktur, kepala dinas, atau ketua yayasan penandatangan BAST/NPHD.</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span>Alamat Kantor Pemberi Hibah</span>
                    </span>
                    <template x-if="formData.hibah_alamat_pemberi">
                        <span class="text-[9.5px] text-emerald-400 font-mono font-semibold">✓ Terisi</span>
                    </template>
                </label>
                <input type="text" x-model="formData.hibah_alamat_pemberi"
                    placeholder="Alamat kantor / domisili instansi pemberi hibah"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                <p class="text-[10px] text-slate-500 mt-1">Alamat domisili kantor / lembaga instansi pemberi hibah.</p>
            </div>
        </div>
    </div>

    {{-- ─── CARD 2: Nomor & Tanggal BAST / NPHD ──────────────────────────── --}}
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/20 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>📋 Nomor &amp; Tanggal Dokumen BAST / NPHD</span>
                <span class="text-rose-400">*</span>
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor BAST / NPHD Hibah <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.hibah_nomor_bast" required
                    placeholder="Contoh: 028/BAST-HB/KEMENKES/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white font-mono focus:outline-none transition-all">
                <p class="text-[10px] text-slate-500 mt-1">Nomor Berita Acara Serah Terima (BAST) atau Naskah Perjanjian Hibah Daerah (NPHD).</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal BAST / NPHD Hibah <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-datepicker="{ maxDate: 'today' }" x-model="formData.hibah_tanggal_bast"
                    @input="onTanggalBastChange($event.target.value)"
                    @change="onTanggalBastChange($event.target.value)"
                    required
                    placeholder="dd/mm/yyyy"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none transition-all">
            </div>
        </div>

        {{-- Tahun Perolehan & Triwulan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span>Tahun Pembukuan <span class="text-rose-400">*</span></span>
                    <span class="inline-flex items-center gap-1 text-[10px] text-amber-400/90 font-medium bg-amber-950/40 px-2 py-0.5 rounded-md border border-amber-800/40" title="Tahun otomatis tersinkron dari Tanggal BAST">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Auto Sinkron
                    </span>
                </label>
                <input type="number" x-model.number="formData.tahun_perolehan" required min="1990" :max="new Date().getFullYear()"
                    placeholder="{{ date('Y') }}"
                    @input="if(formData.tahun_perolehan > {{ date('Y') }}) formData.tahun_perolehan = {{ date('Y') }};"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span>Triwulan Pembukuan <span class="text-rose-400">*</span></span>
                    <span class="inline-flex items-center gap-1 text-[10px] text-amber-400/90 font-medium bg-amber-950/40 px-2 py-0.5 rounded-md border border-amber-800/40" title="Triwulan otomatis tersinkron dari bulan Tanggal BAST">
                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Auto Sinkron
                    </span>
                </label>
                <select x-model="formData.triwulan" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none font-bold">
                    <option value="TW I">TW I (Januari - Maret)</option>
                    <option value="TW II">TW II (April - Juni)</option>
                    <option value="TW III">TW III (Juli - September)</option>
                    <option value="TW IV">TW IV (Oktober - Desember)</option>
                </select>
            </div>
        </div>

        {{-- ─── Upload Berkas BAST / NPHD ──────────────────────────────── --}}
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3.5 shadow-lg">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span>Berkas Dokumen BAST / NPHD Hibah</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-950/60 text-amber-300 border border-amber-800/40">
                        Opsional
                    </span>
                </div>
                <span class="text-[10px] text-slate-400 font-mono">Format: PDF, JPG, PNG, DOC/DOCX (Maks. 10 MB)</span>
            </div>

            {{-- 1. Kartu Berkas Lama yang Sudah Ada (Mode Edit) --}}
            <template x-if="existingDokumenPath && !removeExistingDokumen">
                <div class="p-3.5 rounded-2xl bg-slate-950/90 border border-amber-500/30 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-md">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-11 h-11 rounded-xl overflow-hidden flex items-center justify-center shrink-0 border border-amber-500/30 bg-slate-900">
                            <template x-if="isExistingFileImage()">
                                <img :src="'/storage/' + existingDokumenPath" alt="Thumbnail BAST" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!isExistingFileImage()">
                                <span class="text-xl">📄</span>
                            </template>
                        </div>
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="text-xs font-bold text-white truncate block" x-text="getExistingFileName()"></span>
                                <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider bg-emerald-500/15 text-emerald-300 border border-emerald-500/30 whitespace-nowrap shrink-0">
                                    ✓ Berkas Tersimpan
                                </span>
                            </div>
                            <p class="text-[10.5px] text-slate-400 mt-0.5">Berkas BAST ini sudah tersimpan di arsip server.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                        <a :href="'/storage/' + existingDokumenPath" target="_blank"
                            class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-cyan-300 border border-cyan-500/30 text-xs font-bold flex items-center gap-1 transition-all">
                            <span>👁️ Lihat Berkas</span>
                        </a>
                        <button type="button" @click="toggleRemoveExistingFile()"
                            class="px-3 py-1.5 rounded-xl bg-rose-950/60 hover:bg-rose-900/80 text-rose-300 border border-rose-500/40 text-xs font-bold flex items-center gap-1 transition-all cursor-pointer">
                            <span>🗑️ Hapus Berkas</span>
                        </button>
                    </div>
                </div>
            </template>

            {{-- 2. Peringatan jika pengguna memilih menghapus berkas lama --}}
            <template x-if="existingDokumenPath && removeExistingDokumen">
                <div class="p-3.5 rounded-2xl bg-rose-950/40 border border-rose-500/40 flex items-center justify-between gap-3 text-xs">
                    <span class="text-rose-300 font-semibold flex items-center gap-1.5">
                        <span>⚠️</span>
                        <span>Berkas BAST lama ditandai untuk dihapus saat Anda menekan tombol simpan.</span>
                    </span>
                    <button type="button" @click="toggleRemoveExistingFile()"
                        class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-300 border border-slate-700 text-[11px] font-bold cursor-pointer shrink-0">
                        ↺ Batalkan Hapus (Pertahankan Berkas)
                    </button>
                </div>
            </template>

            {{-- 3. Area Dropzone Unggah File / Ganti File --}}
            <div class="p-4 rounded-2xl bg-slate-950/60 border-2 border-dashed transition-all text-center relative group"
                :class="selectedFile ? 'border-amber-400 bg-amber-950/10' : 'border-slate-700 hover:border-amber-400/60'">
                <input type="file" id="hibahDokumenInput" @change="handleFileSelect" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                <div class="space-y-1.5 pointer-events-none">
                    <div class="w-10 h-10 mx-auto rounded-xl flex items-center justify-center text-lg shadow-inner"
                        :class="selectedFile ? 'bg-emerald-500/20 text-emerald-300' : 'bg-amber-500/10 text-amber-400'">
                        <span x-show="!selectedFile">📁</span>
                        <span x-show="selectedFile">📄</span>
                    </div>
                    <p class="text-xs font-bold transition-colors"
                        :class="selectedFile ? 'text-emerald-300 font-mono' : 'text-slate-300 group-hover:text-amber-300'">
                        <span x-show="!selectedFile && !existingDokumenPath">Klik atau seret berkas BAST / NPHD ke sini</span>
                        <span x-show="!selectedFile && existingDokumenPath && !removeExistingDokumen">Klik atau seret untuk <strong>MENGGANTI</strong> dengan berkas baru</span>
                        <span x-show="!selectedFile && existingDokumenPath && removeExistingDokumen">Klik atau seret untuk mengunggah berkas pengganti baru</span>
                        <span x-show="selectedFile" x-text="selectedFile ? (selectedFile.name + ' (' + (selectedFile.size / 1024 / 1024).toFixed(2) + ' MB)') : ''"></span>
                    </p>
                    <p class="text-[10.5px] text-slate-500">
                        <span x-show="!selectedFile && existingDokumenPath && !removeExistingDokumen">💡 Biarkan kosong jika ingin tetap mempertahankan berkas yang sudah ada.</span>
                        <span x-show="!selectedFile && (!existingDokumenPath || removeExistingDokumen)">Maksimal 10 MB (Format: PDF, Gambar Scan, Dokumen Word)</span>
                        <span x-show="selectedFile" class="text-amber-300/80">
                            <span x-show="existingDokumenPath">⚡ Berkas baru ini akan menggantikan berkas lama saat disimpan.</span>
                            <span x-show="!existingDokumenPath">✓ Berkas siap disimpan bersama dokumen hibah.</span>
                        </span>
                    </p>
                </div>
            </div>

            {{-- 4. Indikator dan Tombol Batal saat Berkas Baru Dipilih --}}
            <template x-if="selectedFile">
                <div class="flex items-center justify-between px-1 text-[11px]">
                    <span class="text-emerald-400 font-semibold flex items-center gap-1">
                        <span>✓</span>
                        <span x-show="existingDokumenPath">Berkas baru siap diunggah menggantikan berkas lama</span>
                        <span x-show="!existingDokumenPath">Berkas siap disimpan</span>
                    </span>
                    <button type="button" @click="cancelSelectedFile()" class="text-rose-400 hover:text-rose-300 hover:underline font-bold cursor-pointer">
                        ✕ Batal / Lepas Berkas Baru
                    </button>
                </div>
            </template>
        </div>

        {{-- Ruang Lingkup & Keterangan --}}
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Ruang Lingkup / Keterangan Hibah
            </label>
            <textarea x-model="formData.hibah_keterangan" rows="2"
                placeholder="Contoh: Hibah sarana medis alkes dari Kemenkes RI Program DAK 2026, kondisi baru dan langsung operasional di IGD..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
        </div>
    </div>

</div>
