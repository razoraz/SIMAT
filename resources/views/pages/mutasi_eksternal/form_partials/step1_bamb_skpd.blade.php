<!-- ========================================================================= -->
<!-- LANGKAH 1: DOKUMEN BAMB & SKPD PENGIRIM (PELIMPAHAN BMD SKPD)              -->
<!-- ========================================================================= -->
<div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div>
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-400/10 text-indigo-300 border border-indigo-400/20 text-xs font-bold mb-2">
            <span>🔄 LANGKAH 1 DARI 3: LEGALITAS BERITA ACARA SERAH TERIMA (BAMB / BAST)</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span class="p-2 rounded-xl bg-indigo-400/10 text-indigo-400 text-sm">📜</span>
            <span>Langkah 1: Dokumen BAMB &amp; SKPD Pengirim</span>
        </h2>
        <p class="text-xs text-slate-400 mt-1">
            Lengkapi data legalitas Berita Acara Mutasi Barang (BAMB/BAST), instansi atau SKPD pengirim, pejabat penyerah dan penerima, serta dokumen berkas serah terima.
        </p>
    </div>

    <!-- Bagian 1: Identitas SKPD Pengirim & Dokumen Berita Acara -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-indigo-500/30 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🏛️ Identitas SKPD Pengirim &amp; Dokumen BAMB</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-indigo-300 bg-indigo-400/10 px-2 py-0.5 rounded-lg border border-indigo-400/20">
                Pelimpahan BMD · Antar-OPD
            </span>
        </div>

        <!-- Instansi / SKPD Pengirim (Combobox Autocomplete) -->
        <div class="relative space-y-1.5" @click.away="isSkpdDropdownOpen = false">
            <div class="flex items-center justify-between">
                <label class="block text-xs font-bold text-slate-200">
                    Instansi / SKPD Asal Pengirim BMD <span class="text-rose-400">*</span>
                </label>
                <template x-if="masterSkpdList && masterSkpdList.length > 0">
                    <span class="text-[10px] text-indigo-400 font-mono font-normal flex items-center gap-1 bg-indigo-500/10 px-2 py-0.5 rounded-md border border-indigo-500/20">
                        <span>⚡</span>
                        <span>Riwayat Tersimpan</span>
                    </span>
                </template>
            </div>

            <!-- Input Box dengan Ikon dan Clear Button -->
            <div class="relative">
                <input type="text" 
                    x-model="formData.mutasi_asal" 
                    @focus="isSkpdDropdownOpen = true"
                    @input="isSkpdDropdownOpen = true"
                    @keydown.escape="isSkpdDropdownOpen = false"
                    required
                    autocomplete="off"
                    placeholder="Ketik atau pilih nama dinas/instansi pengirim (contoh: Dinas Kesehatan, BPKAD Bondowoso...)"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-3 pl-10 pr-10 text-xs text-white placeholder-slate-500 focus:outline-none font-bold transition-all shadow-inner">
                
                <!-- Ikon Instansi -->
                <svg class="w-4 h-4 text-indigo-400 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>

                <!-- Tombol Kosongkan Input -->
                <button type="button" 
                    x-show="formData.mutasi_asal"
                    @click="formData.mutasi_asal = ''; isSkpdDropdownOpen = true" 
                    title="Kosongkan nama instansi"
                    style="position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 22px; height: 22px; display: flex; align-items: center; justify-content: center; cursor: pointer; z-index: 10;"
                    class="rounded-md bg-slate-800 hover:bg-rose-500/20 text-slate-400 hover:text-rose-300 text-xs transition-colors">
                    ✕
                </button>
            </div>

            <!-- Floating Dropdown Saran / Filter SKPD -->
            <div x-show="isSkpdDropdownOpen" 
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
                    <span>Pilih Riwayat / Ketik SKPD Baru</span>
                    <span class="font-mono text-slate-400" x-text="filteredSkpdList.length + ' instansi'"></span>
                </div>

                <template x-for="(skpd, sIdx) in filteredSkpdList" :key="sIdx">
                    <div @click="selectSkpd(skpd)"
                        class="px-4 py-2.5 hover:bg-indigo-500/15 cursor-pointer transition-colors group flex items-center justify-between gap-3 text-left"
                        :class="formData.mutasi_asal === skpd ? 'bg-indigo-500/20 text-indigo-200' : 'text-slate-200'">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="text-xs text-indigo-400/80">🏛️</span>
                            <span class="text-xs font-bold group-hover:text-indigo-300 truncate" x-text="skpd"></span>
                        </div>
                        <span class="text-[9px] px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/25 font-bold shrink-0 group-hover:bg-indigo-500/25">
                            Pilih ↵
                        </span>
                    </div>
                </template>
            </div>
        </div>

        <!-- Grid Nomor BAMB, Tanggal, SK Dasar & Periode -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            
            <!-- Nomor Dokumen BAMB / BAST -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Nomor Berita Acara (BAMB / BAST) <span class="text-rose-400">*</span>
                </label>
                <input type="text" x-model="formData.mutasi_nomor_bamb" required
                    placeholder="Contoh: 028/123/BAMB/430.10.2/2026"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none shadow-inner">
            </div>

            <!-- Tanggal Dokumen BAMB -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tanggal Dokumen BAMB <span class="text-rose-400">*</span>
                </label>
                <input type="date" x-model="formData.mutasi_tanggal" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-semibold focus:outline-none shadow-inner">
            </div>

            <!-- Dasar Hukum / Nomor SK Kepala Daerah -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Dasar Pelimpahan (SK Bupati / Surat Pengantar)
                </label>
                <input type="text" x-model="formData.nomor_sk_dasar"
                    placeholder="Nomor SK Bupati / Surat Tugas Pelimpahan"
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono focus:outline-none shadow-inner">
            </div>

            <!-- Tahun Perolehan BMD -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tahun Perolehan BMD <span class="text-rose-400">*</span>
                </label>
                <input type="number" x-model.number="formData.tahun_perolehan" min="1990" max="2100" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-mono font-bold focus:outline-none shadow-inner">
            </div>

            <!-- Triwulan Pembukuan -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Triwulan Pembukuan <span class="text-rose-400">*</span>
                </label>
                <select x-model="formData.triwulan" required
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-3.5 py-2.5 text-xs text-white font-bold focus:outline-none shadow-inner">
                    <option value="TW I">Triwulan I (Jan - Mar)</option>
                    <option value="TW II">Triwulan II (Apr - Jun)</option>
                    <option value="TW III">Triwulan III (Jul - Sep)</option>
                    <option value="TW IV">Triwulan IV (Okt - Des)</option>
                </select>
            </div>

            <!-- Jenis Mutasi / Tipe Pelimpahan -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Tipe Mutasi Eksternal
                </label>
                <div class="px-3.5 py-2.5 rounded-xl bg-slate-900/60 border border-slate-800 text-xs text-indigo-300 font-bold flex items-center justify-between">
                    <span>Transfer Antar-OPD / SKPD Masuk</span>
                    <span class="text-[10px] px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-mono">BMD MASUK</span>
                </div>
            </div>

        </div>
    </div>

    <!-- Bagian 2: Pihak yang Terlibat dalam Berita Acara Serah Terima -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 space-y-5 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-1.5">
                <span>👥 Pihak Penyerah (SKPD Asal) &amp; Pihak Penerima (RSUD)</span>
            </span>
            <span class="text-[10px] text-slate-400 font-mono">Penandatangan Berita Acara</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            
            <!-- Pihak Pertama (SKPD Pengirim) -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/90 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                    <span class="text-xs font-bold text-amber-400 block uppercase tracking-wider">
                        1. Pihak Pertama (Penyerah / SKPD Pengirim)
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 font-bold">OPD Asal</span>
                </div>

                <div>
                    <label class="block text-[10px] text-slate-400 font-semibold mb-1">Nama Pejabat Penyerah</label>
                    <input type="text" x-model="formData.pj_asal_nama"
                        placeholder="Nama Kepala Dinas / Pengurus Barang SKPD Asal"
                        class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-[10px] text-slate-400 font-semibold mb-1">NIP Penyerah</label>
                        <input type="text" x-model="formData.pj_asal_nip"
                            placeholder="1980xxxx..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] text-slate-400 font-semibold mb-1">Jabatan Penyerah</label>
                        <input type="text" x-model="formData.pj_asal_jabatan"
                            placeholder="Pengurus Barang / PPK Asal"
                            class="w-full bg-slate-950 border border-slate-700 focus:border-amber-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none">
                    </div>
                </div>
            </div>

            <!-- Pihak Kedua (Penerima / RSUD Dr. H. Koesnandi) -->
            <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/90 space-y-3">
                <div class="flex items-center justify-between border-b border-slate-800 pb-1.5">
                    <span class="text-xs font-bold text-indigo-400 block uppercase tracking-wider">
                        2. Pihak Kedua (Penerima / RSUD Dr. H. Koesnandi)
                    </span>
                    <span class="text-[9px] px-2 py-0.5 rounded bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 font-bold">RSUD</span>
                </div>

                <!-- Pejabat RSUD dengan Autocomplete / Pilihan Pejabat -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[10px] text-slate-400 font-semibold">Pejabat Pembuat Komitmen / Pengurus Barang RSUD</label>
                        <template x-if="pejabatsList && pejabatsList.length > 0">
                            <span class="text-[9px] text-indigo-400 font-mono">Daftar Pejabat Aktif</span>
                        </template>
                    </div>
                    <div class="relative">
                        <input type="text" x-model="formData.ppk_nama"
                            list="pejabat-rsud-list"
                            placeholder="Ketik atau pilih pejabat RSUD..."
                            class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs text-white focus:outline-none font-bold">
                        <datalist id="pejabat-rsud-list">
                            <template x-for="(pj, pIdx) in pejabatsList" :key="pIdx">
                                <option :value="pj.nama" x-text="pj.nama + (pj.nip ? ' (' + pj.nip + ')' : '')"></option>
                            </template>
                        </datalist>
                    </div>
                </div>

                <div>
                    <label class="block text-[10px] text-slate-400 font-semibold mb-1">NIP Pejabat RSUD</label>
                    <input type="text" x-model="formData.ppk_nip"
                        placeholder="196904121999031004"
                        class="w-full bg-slate-950 border border-slate-700 focus:border-indigo-400 rounded-xl px-3 py-2 text-xs font-mono text-white focus:outline-none">
                </div>
            </div>

        </div>
    </div>

    <!-- Bagian 3: Dokumen Berkas Lampiran & Keterangan Alasan Pelimpahan -->
    <div class="p-6 rounded-3xl bg-slate-950/80 border border-slate-800 space-y-4 shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <span class="text-xs font-extrabold text-white uppercase tracking-wider flex items-center gap-1.5">
                <span>📎 Berkas Lampiran Berita Acara &amp; Keterangan</span>
            </span>
            <span class="text-[10px] text-slate-400 font-mono">Dokumen Pendukung (PDF/Gambar)</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            
            <!-- Upload Berkas Berita Acara -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Unggah Berkas Berita Acara (BAMB/BAST/SK)
                </label>
                
                <div class="p-4 rounded-2xl bg-slate-900 border-2 border-dashed border-slate-700 hover:border-indigo-400/60 transition-all text-center relative group">
                    <input type="file" @change="handleFileSelect" accept=".pdf,.jpg,.jpeg,.png"
                        class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10">
                    
                    <div class="space-y-1.5 pointer-events-none">
                        <div class="w-10 h-10 mx-auto rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center text-lg">
                            📁
                        </div>
                        <p class="text-xs font-bold text-slate-300 group-hover:text-indigo-300 transition-colors">
                            <span x-show="!selectedFile">Klik atau seret berkas BAMB ke sini</span>
                            <span x-show="selectedFile" class="text-indigo-400 font-mono" x-text="selectedFile ? selectedFile.name : ''"></span>
                        </p>
                        <p class="text-[10.5px] text-slate-500">Maksimal 10 MB (Format: PDF, JPG, PNG)</p>
                    </div>
                </div>

                <!-- Info Berkas yang Sudah Tersimpan (Mode Edit) -->
                <template x-if="isEdit && formData.dokumen_lampiran_path">
                    <div class="mt-2 p-2.5 rounded-xl bg-slate-900/90 border border-slate-800 flex items-center justify-between text-xs">
                        <span class="text-slate-300 truncate">📄 File Tersimpan: <strong class="text-indigo-300" x-text="formData.dokumen_lampiran_path.split('/').pop()"></strong></span>
                        <a :href="'/storage/' + formData.dokumen_lampiran_path" target="_blank" class="text-indigo-400 hover:underline font-bold text-[11px] shrink-0 ml-2">Lihat File ↗</a>
                    </div>
                </template>
            </div>

            <!-- Keterangan / Alasan Pelimpahan BMD -->
            <div>
                <label class="block text-xs font-bold text-slate-200 mb-1.5">
                    Keterangan Tambahan / Alasan Pelimpahan Status
                </label>
                <textarea x-model="formData.mutasi_keterangan" rows="4"
                    placeholder="Contoh: Pelimpahan status penggunaan aset peralatan kesehatan dari Dinas Kesehatan Bondowoso untuk pemenuhan sarana medis pelayanan RSUD dr. H. Koesnandi..."
                    class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-3 text-xs text-white placeholder-slate-500 focus:outline-none shadow-inner leading-relaxed"></textarea>
            </div>

        </div>
    </div>

</div>
