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

    {{-- ─── Banner Error Inline Langkah 1 ────────────────────────────────── --}}
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

    {{-- ─── CARD 1: Jenis / Sumber Hibah ─────────────────────────────────── --}}
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-amber-500/30 space-y-5 shadow-xl relative overflow-hidden">
        <div class="absolute -right-8 -bottom-8 w-40 h-40 bg-amber-400/5 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-amber-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🎁 Jenis / Sumber Hibah</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-amber-300 bg-amber-400/10 px-2 py-0.5 rounded-lg border border-amber-400/20">
                Non-Belanja Modal APBD
            </span>
        </div>

        {{-- Pilihan Tipe Hibah --}}
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-2.5">
                Kategori Sumber Hibah <span class="text-rose-400">*</span>
            </label>
            <div class="grid grid-cols-2 sm:grid-cols-5 gap-2.5">
                {{-- 1. Pemerintah Pusat --}}
                <button type="button" @click="formData.tipe_hibah = 'pemerintah_pusat'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.tipe_hibah === 'pemerintah_pusat'
                        ? 'bg-amber-500/20 border-amber-400 text-amber-300 font-black shadow-lg shadow-amber-500/20 ring-1 ring-amber-400'
                        : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-base mb-0.5">🏛️</span>
                    <span class="block text-[10px] font-bold">Pem. Pusat</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Kemenkes, APBN</span>
                </button>

                {{-- 2. Pemerintah Provinsi --}}
                <button type="button" @click="formData.tipe_hibah = 'pemerintah_provinsi'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.tipe_hibah === 'pemerintah_provinsi'
                        ? 'bg-amber-500/20 border-amber-400 text-amber-300 font-black shadow-lg shadow-amber-500/20 ring-1 ring-amber-400'
                        : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-base mb-0.5">🏢</span>
                    <span class="block text-[10px] font-bold">Pem. Provinsi</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Dinkes Prov. Jatim</span>
                </button>

                {{-- 3. Pemerintah Kabupaten / OPD --}}
                <button type="button" @click="formData.tipe_hibah = 'pemerintah_daerah'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.tipe_hibah === 'pemerintah_daerah'
                        ? 'bg-amber-500/20 border-amber-400 text-amber-300 font-black shadow-lg shadow-amber-500/20 ring-1 ring-amber-400'
                        : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-base mb-0.5">🏠</span>
                    <span class="block text-[10px] font-bold">Pem. Daerah</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Pemkab Bondowoso</span>
                </button>

                {{-- 4. Swasta / BUMN / CSR --}}
                <button type="button" @click="formData.tipe_hibah = 'swasta_csr'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.tipe_hibah === 'swasta_csr'
                        ? 'bg-amber-500/20 border-amber-400 text-amber-300 font-black shadow-lg shadow-amber-500/20 ring-1 ring-amber-400'
                        : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-base mb-0.5">🏭</span>
                    <span class="block text-[10px] font-bold">Swasta / CSR</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">BUMN, Yayasan</span>
                </button>

                {{-- 5. Perorangan / Komunitas --}}
                <button type="button" @click="formData.tipe_hibah = 'perorangan'"
                    class="p-2.5 rounded-xl border text-center transition-all cursor-pointer"
                    :class="formData.tipe_hibah === 'perorangan'
                        ? 'bg-amber-500/20 border-amber-400 text-amber-300 font-black shadow-lg shadow-amber-500/20 ring-1 ring-amber-400'
                        : 'bg-slate-900 border-slate-800 text-slate-400 hover:border-slate-700'">
                    <span class="block text-base mb-0.5">👤</span>
                    <span class="block text-[10px] font-bold">Perorangan</span>
                    <span class="block text-[9px] text-slate-400 mt-0.5">Donatur / Tokoh</span>
                </button>
            </div>
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
                    @input="isInstansiDropdownOpen = true"
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

            {{-- Dropdown Saran Instansi --}}
            <div x-show="isInstansiDropdownOpen"
                x-cloak
                x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-75"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                style="max-height: 220px !important; overflow-y: auto !important;"
                class="absolute z-50 mt-1.5 w-full bg-slate-900 border border-amber-500/40 rounded-2xl shadow-2xl overflow-hidden divide-y divide-slate-800 custom-scrollbar backdrop-blur-xl">

                <div class="px-3.5 py-1.5 bg-slate-950/90 text-[10px] font-bold text-amber-400 uppercase tracking-wider flex items-center justify-between border-b border-slate-800">
                    <span>Pilih Riwayat / Ketik Instansi Baru</span>
                    <span class="font-mono text-slate-400" x-text="filteredInstansiList.length + ' saran'"></span>
                </div>

                <template x-for="(inst, iIdx) in filteredInstansiList" :key="iIdx">
                    <div @click="selectInstansi(inst)"
                        class="px-4 py-2.5 hover:bg-amber-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                        :class="formData.hibah_pemberi === inst ? 'bg-amber-500/20 text-amber-200' : 'text-slate-200'">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="text-xs text-amber-400/80">🏛️</span>
                            <span class="text-xs font-bold group-hover:text-amber-300 truncate" x-text="inst"></span>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/25 font-bold shrink-0 group-hover:bg-amber-500/25">
                            Pilih ↵
                        </span>
                    </div>
                </template>

                <template x-if="formData.hibah_pemberi && filteredInstansiList.length === 0">
                    <div class="p-3 text-center text-xs text-slate-400 bg-slate-950/50">
                        <span class="text-amber-300 font-semibold" x-text="'➕ Gunakan Instansi Baru: &quot;' + formData.hibah_pemberi + '&quot;'"></span>
                        <p class="text-[10px] text-slate-500 mt-0.5">Instansi ini akan otomatis tersimpan ke riwayat database setelah formulir disimpan.</p>
                    </div>
                </template>
            </div>

            {{-- Shortcut Instansi Populer --}}
            <div class="pt-1 flex flex-wrap items-center gap-1.5">
                <span class="text-[10px] text-slate-500 font-semibold mr-1">Pilih Cepat:</span>
                <button type="button" @click="selectInstansi('Kementerian Kesehatan Republik Indonesia'); formData.tipe_hibah = 'pemerintah_pusat';"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-500/15 text-slate-300 hover:text-amber-300 text-[10px] border border-slate-700 hover:border-amber-500/30 cursor-pointer transition-colors">
                    Kemenkes RI
                </button>
                <button type="button" @click="selectInstansi('Dinas Kesehatan Provinsi Jawa Timur'); formData.tipe_hibah = 'pemerintah_provinsi';"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-500/15 text-slate-300 hover:text-amber-300 text-[10px] border border-slate-700 hover:border-amber-500/30 cursor-pointer transition-colors">
                    Dinkes Prov. Jatim
                </button>
                <button type="button" @click="selectInstansi('Pemerintah Kabupaten Bondowoso'); formData.tipe_hibah = 'pemerintah_daerah';"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-500/15 text-slate-300 hover:text-amber-300 text-[10px] border border-slate-700 hover:border-amber-500/30 cursor-pointer transition-colors">
                    Pemkab Bondowoso
                </button>
                <button type="button" @click="selectInstansi('Donatur Swasta / Yayasan CSR'); formData.tipe_hibah = 'swasta_csr';"
                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-amber-500/15 text-slate-300 hover:text-amber-300 text-[10px] border border-slate-700 hover:border-amber-500/30 cursor-pointer transition-colors">
                    Donatur / CSR
                </button>
            </div>
        </div>

        {{-- ─── Pimpinan / Kuasa Pemberi & Alamat (2 Kolom) ──────────────── --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span>Nama Pimpinan / Kuasa Pemberi</span>
                </label>
                <input type="text" x-model="formData.hibah_pimpinan"
                    placeholder="Contoh: dr. H. Ahmad Fauzi, Sp.PD — Kepala Dinas Kesehatan"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
                <p class="text-[10px] text-slate-500 mt-1">Nama direktur, kepala dinas, atau ketua yayasan penandatangan BAST/NPHD.</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span>Alamat Kantor Pemberi Hibah</span>
                </label>
                <input type="text" x-model="formData.hibah_alamat_pemberi"
                    placeholder="Alamat kantor / domisili instansi pemberi hibah"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white placeholder-slate-500 focus:outline-none transition-all">
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
                <input type="text" x-datepicker="{ maxDate: 'today' }" x-model="formData.hibah_tanggal_bast" required
                    placeholder="dd/mm/yyyy"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-amber-400 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none transition-all">
            </div>
        </div>

        {{-- Tahun Perolehan & Triwulan --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                    <span>Tahun Pembukuan <span class="text-rose-400">*</span></span>
                    <span class="inline-flex items-center gap-1 text-[10px] text-amber-400/90 font-medium bg-amber-950/40 px-2 py-0.5 rounded-md border border-amber-800/40">
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
                    <span class="text-[10px] text-amber-400/80 font-mono">Periode Pencatatan</span>
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
        <div class="p-4 sm:p-5 rounded-2xl bg-slate-900/90 border border-slate-800 space-y-3 shadow-lg">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div class="flex items-center gap-2">
                    <span class="text-xs font-bold text-slate-200 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        <span>Unggah Berkas BAST / NPHD Hibah</span>
                    </span>
                    <span class="px-2 py-0.5 rounded-full text-[9px] font-bold bg-amber-950/60 text-amber-300 border border-amber-800/40">
                        Opsional
                    </span>
                </div>
                <span class="text-[10px] text-slate-400 font-mono">Format: PDF, JPG, PNG, DOC/DOCX (Maks. 10 MB)</span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-950/60 border-2 border-dashed border-slate-700 hover:border-amber-400/60 transition-all text-center relative group">
                <input type="file" @change="handleFileSelect" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx"
                    class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">

                <div class="space-y-1.5 pointer-events-none">
                    <div class="w-10 h-10 mx-auto rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg shadow-inner">
                        📁
                    </div>
                    <p class="text-xs font-bold text-slate-300 group-hover:text-amber-300 transition-colors">
                        <span x-show="!selectedFile">Klik atau seret berkas BAST / NPHD ke sini</span>
                        <span x-show="selectedFile" class="text-amber-400 font-mono" x-text="selectedFile ? ('📄 ' + selectedFile.name + ' (' + (selectedFile.size / 1024 / 1024).toFixed(2) + ' MB)') : ''"></span>
                    </p>
                    <p class="text-[10.5px] text-slate-500">Maksimal 10 MB (Format: PDF, Gambar Scan, Dokumen Word)</p>
                </div>
            </div>

            <template x-if="selectedFile">
                <div class="flex items-center justify-between px-1 text-[11px]">
                    <span class="text-emerald-400 font-semibold flex items-center gap-1">
                        <span>✓</span>
                        <span>Berkas siap disimpan bersama data aset hibah</span>
                    </span>
                    <button type="button" @click="selectedFile = null" class="text-rose-400 hover:underline font-medium cursor-pointer">
                        ✕ Batal / Ganti Berkas
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
