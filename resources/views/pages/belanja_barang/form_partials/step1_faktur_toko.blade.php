<!-- ========================================================================= -->
<!-- LANGKAH 1: DOKUMEN FAKTUR/NOTA & TOKO SUPPLIER                            -->
<!-- ========================================================================= -->
<div x-show="step === 1" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-6">
    
    <div class="border-b border-slate-800 pb-4">
        <div class="inline-flex items-center space-x-2 px-3 py-1 rounded-full bg-indigo-500/10 text-indigo-300 border border-indigo-500/20 text-xs font-bold mb-2">
            <span>Langkah 1 dari 3</span>
        </div>
        <h2 class="text-lg font-bold text-white flex items-center space-x-2">
            <span>🧾 Dokumen Pembelian Toko &amp; Rekanan Perbekalan</span>
        </h2>
        <p class="text-xs text-slate-400 mt-0.5">
            Masukkan data faktur, nota pembelian toko, atau kuitansi pembayaran operasional perbekalan ruangan.
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
    <div class="p-5 rounded-2xl bg-slate-950/70 border border-indigo-500/30 space-y-4 shadow-xl">
        <div class="flex items-center justify-between">
            <span class="text-xs font-extrabold text-indigo-400 uppercase tracking-wider flex items-center gap-1.5">
                <span>🏷️ Informasi Toko / Rekanan &amp; Faktur Pembelian</span>
                <span class="text-rose-400">*</span>
            </span>
            <span class="text-[10px] font-bold text-indigo-300 bg-indigo-500/10 px-2 py-0.5 rounded border border-indigo-500/20">
                Tanpa Pagu Belanja Modal APBD
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
                    Nomor Faktur / Nota / Kuitansi <span class="text-rose-400">*</span>
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

        <!-- Total Nilai Pembelian (Rp) -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5 flex items-center justify-between">
                <span>Total Nilai Pembelian (Rp) <span class="text-rose-400">*</span></span>
                <span class="text-[11px] font-mono text-indigo-400" x-text="formatRupiah(formData.total_realisasi)"></span>
            </label>
            <input type="number" x-model.number="formData.total_realisasi" min="0" step="any" required
                placeholder="0"
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl px-4 py-2.5 text-xs text-indigo-300 font-bold font-mono focus:outline-none">
        </div>

        <!-- Catatan / Keterangan Pembelian -->
        <div>
            <label class="block text-xs font-bold text-slate-200 mb-1.5">
                Catatan / Keterangan Pembelian Perbekalan
            </label>
            <textarea x-model="formData.rekening_keterangan" rows="2"
                placeholder="Contoh: Pengadaan alat medis operasional ruangan melalui rekening operasional BLUD/Barang & Jasa..."
                class="w-full bg-slate-900 border border-slate-700 focus:border-indigo-400 rounded-xl p-3 text-xs text-white focus:outline-none"></textarea>
        </div>
    </div>

</div>
